<?php

namespace App\Services;

use App\Models\AiSession;
use App\Models\CustomerAiProfile;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductView;
use App\Models\Voucher;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Logic chấm điểm AI khách hàng, tách từ lệnh customer:score-ai-profiles để
 * dùng chung cho cả Command (CLI/cron) và ScoreCustomerAiProfilesChunkJob
 * (chạy theo lô ~50 user, nhiều lô chạy SONG SONG trên nhiều queue worker).
 *
 * Tối ưu tốc độ so với bản gốc:
 * - Bản gốc: mỗi user tốn ~6 query rời (views, order_items, sessions, product
 *   lookup, order exists check, repeat-product query...) -> N user = 6N query.
 * - Bản này: toàn bộ dữ liệu hành vi của CẢ LÔ được fetch bằng 4 query DUY
 *   NHẤT (group theo user_id trong PHP), phần tính toán còn lại chạy hoàn
 *   toàn trong memory, không còn query nào trong vòng lặp per-user (trừ
 *   trường hợp hiếm cần bổ sung sản phẩm gợi ý cùng danh mục).
 */
class CustomerAiScoringService
{
    // Trọng số cho từng loại hành vi xem (product_views.event_type)
    private const VIEW_WEIGHTS = [
        'view'             => 1,
        'click_suggestion' => 2,
        'compare'          => 2,
        'wishlist'         => 3,
        'add_to_cart'      => 5,
    ];

    // Trọng số cho 1 đơn hàng đã đặt (theo product)
    private const ORDER_WEIGHT_PER_ITEM = 10;

    // Trọng số theo ý định chatbot nhận diện được trong 1 phiên chat (ai_sessions.intent_label)
    private const CHAT_INTENT_WEIGHTS = [
        'buy'     => 5,
        'compare' => 3,
        'search'  => 2,
        'support' => 1,
        'unknown' => 0.5,
    ];

    // Số tin nhắn quy đổi ra 1 điểm "mức độ tương tác" trong phiên chat, tối đa +3 điểm/phiên
    private const CHAT_ENGAGEMENT_MESSAGES_PER_POINT = 4;
    private const CHAT_ENGAGEMENT_MAX_BONUS = 3;

    // Cảm xúc rõ rệt (dương/âm) trong phiên chat sẽ cộng/trừ thêm điểm
    private const CHAT_SENTIMENT_THRESHOLD = 0.3;
    private const CHAT_SENTIMENT_BONUS = 1;

    // Số từ khoá tối đa lưu vào keywords_history (ưu tiên tần suất xuất hiện nhiều nhất)
    private const KEYWORDS_HISTORY_LIMIT = 15;

    // Ngưỡng phân loại lead_label theo total_score
    private const THRESHOLD_HOT  = 50;
    private const THRESHOLD_WARM = 15;

    // Đơn được tính là "đã mua thành công" khi xét dự đoán mua lại
    private const COMPLETED_ORDER_STATUSES = ['delivered'];

    // Chu kỳ mua lại mặc định (ngày) khi khách mới chỉ có 1 đơn -> chưa đủ dữ liệu tính chu kỳ thật
    private const DEFAULT_REPURCHASE_CYCLE_DAYS = 45;

    // Còn bao nhiêu ngày thì coi là "sắp đến hạn mua lại" để bắn gợi ý voucher
    private const REPURCHASE_SOON_DAYS = 7;

    // Cửa sổ dữ liệu hành vi dùng để chấm điểm (ngày gần nhất)
    private const BEHAVIOR_WINDOW_DAYS = 90;

    /**
     * Toàn bộ user_id có ít nhất 1 tín hiệu hành vi (view / order / chat).
     * Truyền $onlyUserId để chỉ lấy đúng 1 user (debug / tính lại cho 1 khách).
     */
    public function eligibleUserIds(?int $onlyUserId = null): Collection
    {
        if ($onlyUserId) {
            return collect([$onlyUserId]);
        }

        return ProductView::query()->whereNotNull('user_id')->distinct()->pluck('user_id')
            ->merge(Order::query()->whereNotNull('user_id')->distinct()->pluck('user_id'))
            ->merge(AiSession::query()->whereNotNull('user_id')->distinct()->pluck('user_id'))
            ->unique()
            ->values();
    }

    /**
     * Chấm điểm cho 1 lô user_id. Tối ưu để gọi với ~50 user/lần (kích thước
     * lô được chốt ở ScoreAllCustomerAiProfilesJob::CHUNK_SIZE). Không giới
     * hạn cứng, nhưng lô càng lớn thì 1 job càng tốn RAM/thời gian hơn.
     */
    public function scoreUsers(array $userIds): void
    {
        if (empty($userIds)) {
            return;
        }

        $since          = now()->subDays(self::BEHAVIOR_WINDOW_DAYS);
        $activeVouchers = $this->fetchActiveVouchers();

        // ── 4 query CHO CẢ LÔ, group theo user_id trong PHP thay vì query per-user ──
        $views = ProductView::whereIn('user_id', $userIds)
            ->where('created_at', '>=', $since)
            ->get(['user_id', 'product_id', 'event_type', 'duration_seconds', 'created_at']);

        $orderRows = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('orders.user_id', $userIds)
            ->whereNull('orders.deleted_at')
            ->select(
                'orders.user_id',
                'orders.id as order_id',
                'orders.status',
                'orders.created_at as order_created_at',
                'order_items.product_id',
                'order_items.quantity'
            )
            ->get();

        $sessions = AiSession::whereIn('user_id', $userIds)
            ->where('updated_at', '>=', $since)
            ->get(['user_id', 'intent_label', 'sentiment_score', 'total_messages', 'search_keywords']);

        // Toàn bộ product_id liên quan tới lô này -> 1 query duy nhất thay vì N
        $productIds = $views->pluck('product_id')->merge($orderRows->pluck('product_id'))->unique()->values();
        $products   = Product::whereIn('id', $productIds)->get(['id', 'category_id', 'price'])->keyBy('id');

        $viewsByUser     = $views->groupBy('user_id');
        $orderRowsByUser = $orderRows->groupBy('user_id');
        $sessionsByUser  = $sessions->groupBy('user_id');

        foreach ($userIds as $userId) {
            $this->scoreUser(
                (int) $userId,
                $viewsByUser->get($userId, collect()),
                $orderRowsByUser->get($userId, collect()),
                $sessionsByUser->get($userId, collect()),
                $products,
                $activeVouchers,
            );
        }
    }

    private function fetchActiveVouchers()
    {
        $now = now();

        return Voucher::where('is_active', true)
            ->where('expires_at', '>=', $now)
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->orderBy('min_order_value')
            ->get(['code', 'min_order_value']);
    }

    private function scoreUser(
        int $userId,
        Collection $views,
        Collection $orderRows,
        Collection $sessions,
        Collection $products,
        Collection $activeVouchers
    ): void {
        $hasChatSessions = $sessions->isNotEmpty();

        if ($views->isEmpty() && !$hasChatSessions && $orderRows->isEmpty()) {
            return; // Không có dữ liệu gì thì bỏ qua, không tạo profile rác
        }

        // ── Điểm theo hành vi xem ──
        $productScores = [];
        $scoreView = 0.0;

        foreach ($views as $v) {
            $weight = self::VIEW_WEIGHTS[$v->event_type] ?? 1;

            if ($v->event_type === 'view' && $v->duration_seconds) {
                $weight += min(3, floor($v->duration_seconds / 30));
            }

            $productScores[$v->product_id] = ($productScores[$v->product_id] ?? 0) + $weight;
            $scoreView += $weight;
        }

        // ── Điểm theo đơn hàng ──
        $scoreOrder = 0.0;
        foreach ($orderRows as $item) {
            $points = self::ORDER_WEIGHT_PER_ITEM * max(1, $item->quantity);
            $productScores[$item->product_id] = ($productScores[$item->product_id] ?? 0) + $points;
            $scoreOrder += $points;
        }

        // ── Điểm chat + từ khoá ──
        [$scoreChat, $keywordsHistory] = $this->scoreChatSessions($sessions);

        $totalScore = round($scoreView + $scoreChat + $scoreOrder, 2);

        if (empty($productScores) && $scoreChat <= 0) {
            return;
        }

        $viewedProducts = $products->only(array_keys($productScores));

        // ── Xếp hạng sản phẩm ──
        $topInterestProductId = null;
        $suggestedProductIds  = [];

        if (!empty($productScores)) {
            arsort($productScores);
            $rankedProductIds = array_keys($productScores);

            $topInterestProductId = $rankedProductIds[0] ?? null;
            $suggestedProductIds  = array_slice($rankedProductIds, 1, 8);

            if (count($suggestedProductIds) < 4 && $topInterestProductId) {
                $topProduct = $viewedProducts->get($topInterestProductId);
                if ($topProduct) {
                    // Trường hợp hiếm (thiếu gợi ý) -- sản phẩm NGOÀI những gì user
                    // đã xem/mua, nên vẫn cần 1 query riêng ở đây.
                    $fillerIds = Product::active()
                        ->where('category_id', $topProduct->category_id)
                        ->where('id', '!=', $topInterestProductId)
                        ->whereNotIn('id', $suggestedProductIds)
                        ->inRandomOrder()
                        ->limit(4 - count($suggestedProductIds))
                        ->pluck('id')
                        ->all();
                    $suggestedProductIds = array_merge($suggestedProductIds, $fillerIds);
                }
            }
        }

        $categoryWeights = [];
        foreach ($viewedProducts as $p) {
            $categoryWeights[$p->category_id] = ($categoryWeights[$p->category_id] ?? 0) + ($productScores[$p->id] ?? 0);
        }
        arsort($categoryWeights);
        $interestCategories = array_slice(array_keys($categoryWeights), 0, 5);

        $prices = $viewedProducts->pluck('price')->filter()->map(fn ($p) => (float) $p);
        $priceRange = $prices->isNotEmpty()
            ? ['min' => (float) $prices->min(), 'max' => (float) $prices->max()]
            : null;

        $lastSeenProductId = $views->sortByDesc('created_at')->first()?->product_id;

        $hasOrdered   = $orderRows->isNotEmpty();
        $hasAddedCart = $views->contains('event_type', 'add_to_cart');
        $hasViewed    = $views->isNotEmpty();

        $leadStage = match (true) {
            $hasOrdered      => 'da_mua',
            $hasAddedCart    => 'da_them_gio',
            $hasViewed       => 'dang_xem',
            $hasChatSessions => 'dang_tim_hieu',
            default          => 'moi_ghe',
        };

        $leadLabel = match (true) {
            $totalScore >= self::THRESHOLD_HOT  => 'hot',
            $totalScore >= self::THRESHOLD_WARM => 'warm',
            default                              => 'cold',
        };

        [$repurchaseProbability, $predictedRepurchaseAt, $repurchaseProductId] =
            $this->predictRepurchase($orderRows, $topInterestProductId);

        [$voucherRecommended, $voucherReason] = $this->recommendVoucher(
            $activeVouchers,
            $hasOrdered,
            $hasAddedCart,
            $scoreView,
            $predictedRepurchaseAt,
            $priceRange
        );

        CustomerAiProfile::updateOrCreate(
            ['user_id' => $userId],
            [
                'lead_label'               => $leadLabel,
                'lead_stage'               => $leadStage,
                'total_score'              => $totalScore,
                'score_view'               => round($scoreView, 2),
                'score_chat'               => round($scoreChat, 2),
                'score_order'              => round($scoreOrder, 2),
                'interest_categories'      => $interestCategories,
                'price_range'              => $priceRange,
                'last_seen_product_id'     => $lastSeenProductId,
                'top_interest_product_id'  => $topInterestProductId,
                'suggested_products'       => array_values($suggestedProductIds),
                'keywords_history'         => $keywordsHistory,
                'repurchase_probability'   => $repurchaseProbability,
                'predicted_repurchase_at'  => $predictedRepurchaseAt,
                'repurchase_product_id'    => $repurchaseProductId,
                'voucher_recommended'      => $voucherRecommended,
                'voucher_reason'           => $voucherReason,
                'scored_at'                => now(),
            ]
        );
    }

    /**
     * @return array{0: float, 1: array<int, string>} [score_chat, keywords_history]
     */
    private function scoreChatSessions(Collection $sessions): array
    {
        if ($sessions->isEmpty()) {
            return [0.0, []];
        }

        $scoreChat = 0.0;
        $keywordFrequency = [];

        foreach ($sessions as $session) {
            $weight = self::CHAT_INTENT_WEIGHTS[$session->intent_label] ?? self::CHAT_INTENT_WEIGHTS['unknown'];

            $engagementBonus = min(
                self::CHAT_ENGAGEMENT_MAX_BONUS,
                floor(($session->total_messages ?? 0) / self::CHAT_ENGAGEMENT_MESSAGES_PER_POINT)
            );
            $weight += $engagementBonus;

            $sentiment = (float) ($session->sentiment_score ?? 0);
            if ($sentiment >= self::CHAT_SENTIMENT_THRESHOLD) {
                $weight += self::CHAT_SENTIMENT_BONUS;
            } elseif ($sentiment <= -self::CHAT_SENTIMENT_THRESHOLD) {
                $weight -= self::CHAT_SENTIMENT_BONUS;
            }

            $scoreChat += max(0, $weight);

            // search_keywords có thể ra dạng chuỗi JSON khi lấy raw (Eloquent cast
            // 'array' chỉ áp dụng khi load qua Model, ở đây vẫn qua AiSession::get()
            // nên bình thường đã là mảng -- decode thêm 1 bước phòng hờ cho an toàn.
            $rawKeywords = $session->search_keywords ?? [];
            if (is_string($rawKeywords)) {
                $rawKeywords = json_decode($rawKeywords, true) ?: [];
            }

            foreach ((array) $rawKeywords as $keyword) {
                $keyword = trim(mb_strtolower((string) $keyword));
                if ($keyword === '') {
                    continue;
                }
                $keywordFrequency[$keyword] = ($keywordFrequency[$keyword] ?? 0) + 1;
            }
        }

        arsort($keywordFrequency);
        $keywordsHistory = array_slice(array_keys($keywordFrequency), 0, self::KEYWORDS_HISTORY_LIMIT);

        return [round($scoreChat, 2), $keywordsHistory];
    }

    /**
     * @param Collection $orderRows Các dòng order_items JOIN orders của user này
     *                              (đã fetch sẵn theo lô trong scoreUsers()).
     * @return array{0: ?float, 1: ?Carbon, 2: ?int} [repurchase_probability, predicted_repurchase_at, repurchase_product_id]
     */
    private function predictRepurchase(Collection $orderRows, ?int $topInterestProductId): array
    {
        $delivered = $orderRows->whereIn('status', self::COMPLETED_ORDER_STATUSES);

        if ($delivered->isEmpty()) {
            return [null, null, null];
        }

        // Mỗi đơn có thể có nhiều dòng item -> dedupe theo order_id để ra đúng ngày đặt đơn
        $orderDates = $delivered->unique('order_id')
            ->pluck('order_created_at')
            ->map(fn ($d) => Carbon::parse($d))
            ->sort()
            ->values();

        // Sản phẩm mua lặp lại nhiều nhất trong các đơn đã giao thành công
        $repeatCandidate = $delivered->groupBy('product_id')
            ->map(function ($rows, $productId) {
                return [
                    'product_id'     => (int) $productId,
                    'order_count'    => $rows->pluck('order_id')->unique()->count(),
                    'last_bought_at' => (string) $rows->pluck('order_created_at')->max(),
                ];
            })
            ->filter(fn ($r) => $r['order_count'] > 1)
            ->sort(fn ($a, $b) => [$b['order_count'], $b['last_bought_at']] <=> [$a['order_count'], $a['last_bought_at']])
            ->first();

        $repurchaseProductId = $repeatCandidate['product_id'] ?? $topInterestProductId;
        $lastOrderAt = $orderDates->last();

        if ($orderDates->count() < 2) {
            // Chỉ mới có 1 đơn -> chưa có chu kỳ thật, dùng mặc định với độ tin cậy thấp
            return [
                0.3,
                $lastOrderAt->copy()->addDays(self::DEFAULT_REPURCHASE_CYCLE_DAYS),
                $repurchaseProductId,
            ];
        }

        $intervals = [];
        for ($i = 1; $i < $orderDates->count(); $i++) {
            $intervals[] = $orderDates[$i - 1]->diffInDays($orderDates[$i]);
        }

        $avgInterval = max(7, array_sum($intervals) / count($intervals));

        $variance = 0.0;
        foreach ($intervals as $i) {
            $variance += ($i - $avgInterval) ** 2;
        }
        $stdDev        = sqrt($variance / count($intervals));
        $coefVariation = $avgInterval > 0 ? $stdDev / $avgInterval : 1;

        $probability = max(0.3, min(0.9, 1 - $coefVariation));
        $predictedAt = $lastOrderAt->copy()->addDays((int) round($avgInterval));

        if ($predictedAt->isPast()) {
            $probability = min(0.95, $probability + 0.1);
        }

        return [round($probability, 2), $predictedAt, $repurchaseProductId];
    }

    /**
     * @return array{0: bool, 1: ?string}
     */
    private function recommendVoucher(
        $activeVouchers,
        bool $hasOrdered,
        bool $hasAddedCart,
        float $scoreView,
        ?Carbon $predictedRepurchaseAt,
        ?array $priceRange
    ): array {
        $now = now();

        if ($activeVouchers->isEmpty()) {
            return [false, null];
        }

        $minBudget = $priceRange['min'] ?? 0;

        $pickVoucher = function () use ($activeVouchers, $minBudget) {
            return $activeVouchers->first(fn ($v) => (float) $v->min_order_value <= $minBudget)
                ?? $activeVouchers->first();
        };

        if ($hasOrdered && $predictedRepurchaseAt) {
            $daysUntil = (int) floor(($predictedRepurchaseAt->timestamp - $now->timestamp) / 86400);
            if ($daysUntil <= self::REPURCHASE_SOON_DAYS) {
                $voucher = $pickVoucher();
                return [true, "Sắp đến hạn mua lại - gợi ý mã {$voucher->code}"];
            }
        }

        if (!$hasOrdered && $scoreView >= self::THRESHOLD_WARM) {
            $voucher = $activeVouchers->first();
            return [true, "Xem nhiều SP nhưng chưa mua - gợi ý mã {$voucher->code}"];
        }

        if (!$hasOrdered && $hasAddedCart) {
            $voucher = $activeVouchers->first();
            return [true, "Đã thêm giỏ nhưng chưa thanh toán - gợi ý mã {$voucher->code}"];
        }

        return [false, null];
    }
}