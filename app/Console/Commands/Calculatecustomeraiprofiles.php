<?php

namespace App\Console\Commands;

use App\Models\AiSession;
use App\Models\CustomerAiProfile;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductView;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CalculateCustomerAiProfiles extends Command
{
    /**
     * php artisan customer:score-ai-profiles
     * Chạy định kỳ (vd: mỗi giờ) qua Task Scheduler.
     */
    protected $signature = 'customer:score-ai-profiles {--user= : Chỉ tính cho 1 user_id cụ thể (debug)}';

    protected $description = 'Tính điểm tiềm năng, sở thích, dự đoán mua lại & gợi ý voucher cho từng khách hàng (customer_ai_profiles)';

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
    // (không tính pending/cancelled/returned vì chưa chắc đã giao thành công)
    private const COMPLETED_ORDER_STATUSES = ['delivered'];

    // Chu kỳ mua lại mặc định (ngày) khi khách mới chỉ có 1 đơn -> chưa đủ dữ liệu tính chu kỳ thật
    private const DEFAULT_REPURCHASE_CYCLE_DAYS = 45;

    // Còn bao nhiêu ngày thì coi là "sắp đến hạn mua lại" để bắn gợi ý voucher
    private const REPURCHASE_SOON_DAYS = 7;

    public function handle(): int
    {
        $userIds = $this->option('user')
            ? collect([(int) $this->option('user')])
            : ProductView::query()->whereNotNull('user_id')->distinct()->pluck('user_id')
                ->merge(Order::query()->whereNotNull('user_id')->distinct()->pluck('user_id'))
                ->merge(AiSession::query()->whereNotNull('user_id')->distinct()->pluck('user_id'))
                ->unique()
                ->values();

        if ($userIds->isEmpty()) {
            $this->info('Không có khách hàng nào có dữ liệu hành vi để tính điểm.');
            return self::SUCCESS;
        }

        $this->info("Đang tính điểm cho {$userIds->count()} khách hàng...");
        $bar = $this->output->createProgressBar($userIds->count());

        foreach ($userIds as $userId) {
            $this->scoreUser((int) $userId);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Hoàn tất.');

        return self::SUCCESS;
    }

    private function scoreUser(int $userId): void
    {
        // ── 1. Lấy toàn bộ log hành vi (90 ngày gần nhất để dữ liệu không bị "cũ") ──
        $views = ProductView::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(90))
            ->get(['product_id', 'event_type', 'duration_seconds', 'created_at']);

        $hasChatSessions = AiSession::where('user_id', $userId)
            ->where('updated_at', '>=', now()->subDays(90))
            ->exists();

        if ($views->isEmpty() && !$hasChatSessions && !Order::where('user_id', $userId)->exists()) {
            return; // Không có dữ liệu gì thì bỏ qua, không tạo profile rác
        }

        // ── 2. Tính điểm theo từng sản phẩm dựa trên hành vi xem ──
        $productScores = []; // [product_id => score]
        $scoreView      = 0.0;

        foreach ($views as $v) {
            $weight = self::VIEW_WEIGHTS[$v->event_type] ?? 1;

            // Xem lâu (duration_seconds) thì cộng thêm điểm, tối đa +3
            if ($v->event_type === 'view' && $v->duration_seconds) {
                $weight += min(3, floor($v->duration_seconds / 30)); // mỗi 30s xem +1 điểm
            }

            $productScores[$v->product_id] = ($productScores[$v->product_id] ?? 0) + $weight;
            $scoreView += $weight;
        }

        // ── 3. Cộng điểm từ đơn hàng đã mua (orders + order_items) ──
        $orderItems = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.user_id', $userId)
            ->whereNull('orders.deleted_at')
            ->select('order_items.product_id', 'order_items.quantity')
            ->get();

        $scoreOrder = 0.0;
        foreach ($orderItems as $item) {
            $points = self::ORDER_WEIGHT_PER_ITEM * max(1, $item->quantity);
            $productScores[$item->product_id] = ($productScores[$item->product_id] ?? 0) + $points;
            $scoreOrder += $points;
        }

        // ── 4. Điểm chat + từ khoá quan tâm, dựa trên các phiên chatbot AI thật (ai_sessions) ──
        [$scoreChat, $keywordsHistory] = $this->scoreChatSessions($userId);

        $totalScore = round($scoreView + $scoreChat + $scoreOrder, 2);

        if (empty($productScores) && $scoreChat <= 0) {
            return;
        }

        // ── 5. Xếp hạng sản phẩm theo điểm ──
        $topInterestProductId = null;
        $suggestedProductIds  = [];

        if (!empty($productScores)) {
            arsort($productScores);
            $rankedProductIds = array_keys($productScores);

            $topInterestProductId = $rankedProductIds[0] ?? null;
            $suggestedProductIds  = array_slice($rankedProductIds, 1, 8); // 8 gợi ý tiếp theo

            // Nếu chưa đủ gợi ý, bổ sung sản phẩm cùng category với top interest
            if (count($suggestedProductIds) < 4 && $topInterestProductId) {
                $topProduct = Product::find($topInterestProductId);
                if ($topProduct) {
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

        // ── 6. Danh mục & khoảng giá quan tâm ──
        $viewedProducts = Product::whereIn('id', array_keys($productScores))
            ->get(['id', 'category_id', 'price']);

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

        // ── 7. Sản phẩm xem gần nhất ──
        $lastSeenProductId = $views->sortByDesc('created_at')->first()?->product_id;

        // ── 8. Giai đoạn hành trình mua hàng (lead_stage) ──
        $hasOrdered    = $orderItems->isNotEmpty();
        $hasAddedCart  = $views->contains('event_type', 'add_to_cart');
        $hasViewed     = $views->isNotEmpty();

        $leadStage = match (true) {
            $hasOrdered   => 'da_mua',
            $hasAddedCart => 'da_them_gio',
            $hasViewed    => 'dang_xem',
            $hasChatSessions => 'dang_tim_hieu',
            default       => 'moi_ghe',
        };

        // ── 9. Phân loại tiềm năng (lead_label) ──
        $leadLabel = match (true) {
            $totalScore >= self::THRESHOLD_HOT  => 'hot',
            $totalScore >= self::THRESHOLD_WARM => 'warm',
            default                              => 'cold',
        };

        // ── 10. Dự đoán khả năng & thời điểm mua lại ──
        [$repurchaseProbability, $predictedRepurchaseAt, $repurchaseProductId] =
            $this->predictRepurchase($userId, $topInterestProductId);

        // ── 11. Gợi ý voucher đúng lúc, đúng lý do ──
        [$voucherRecommended, $voucherReason] = $this->recommendVoucher(
            $hasOrdered,
            $hasAddedCart,
            $scoreView,
            $predictedRepurchaseAt,
            $priceRange
        );

        // ── 12. Ghi / cập nhật profile ──
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
     * Tính score_chat + danh sách từ khoá quan tâm dựa trên các phiên chatbot AI thật
     * (ai_sessions, 90 ngày gần nhất). Mỗi phiên đóng góp điểm theo:
     * - Ý định nhận diện được (mua > so sánh > tìm kiếm > hỗ trợ > chưa rõ)
     * - Mức độ tương tác (càng nhiều tin nhắn càng quan tâm), tối đa +3đ/phiên
     * - Cảm xúc rõ rệt: rất tích cực +1đ, rất tiêu cực -1đ (khách đang bực bội cũng là
     *   tín hiệu cần chăm sóc, không chỉ tín hiệu "quan tâm mua hàng")
     *
     * @return array{0: float, 1: array<int, string>} [score_chat, keywords_history]
     */
    private function scoreChatSessions(int $userId): array
    {
        $sessions = AiSession::where('user_id', $userId)
            ->where('updated_at', '>=', now()->subDays(90))
            ->get(['intent_label', 'sentiment_score', 'total_messages', 'search_keywords']);

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
                $weight -= self::CHAT_SENTIMENT_BONUS; // khách bực bội -> vẫn tính là tương tác nhưng trừ điểm "thiện chí"
            }

            $scoreChat += max(0, $weight);

            foreach ((array) ($session->search_keywords ?? []) as $keyword) {
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
     * Dự đoán xác suất & thời điểm khách quay lại mua hàng, dựa trên lịch sử
     * các đơn đã giao thành công (status = delivered).
     *
     * - 0 đơn  -> không đủ dữ liệu, trả về null hết.
     * - 1 đơn  -> dùng chu kỳ mặc định (DEFAULT_REPURCHASE_CYCLE_DAYS), độ tin cậy thấp (0.3).
     * - >=2 đơn -> tính chu kỳ trung bình giữa các đơn + độ ổn định (coefficient of variation)
     *              để suy ra xác suất; nếu đã quá hạn dự đoán mà chưa quay lại thì tăng nhẹ xác suất.
     *
     * @return array{0: ?float, 1: ?Carbon, 2: ?int} [repurchase_probability, predicted_repurchase_at, repurchase_product_id]
     */
    private function predictRepurchase(int $userId, ?int $topInterestProductId): array
    {
        $orderDates = Order::where('user_id', $userId)
            ->whereIn('status', self::COMPLETED_ORDER_STATUSES)
            ->orderBy('created_at')
            ->pluck('created_at')
            ->map(fn ($d) => Carbon::parse($d))
            ->values();

        if ($orderDates->isEmpty()) {
            // Chưa có đơn nào giao thành công -> không đủ căn cứ để dự đoán mua lại
            return [null, null, null];
        }

        // Sản phẩm có khả năng được mua lại nhất: sản phẩm đã được mua ở nhiều đơn nhất,
        // ưu tiên đơn gần đây nhất nếu hòa điểm. Nếu không có sản phẩm nào mua lặp lại,
        // fallback sang sản phẩm khách đang quan tâm nhất (top_interest_product_id).
        $repeatProductId = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.user_id', $userId)
            ->whereIn('orders.status', self::COMPLETED_ORDER_STATUSES)
            ->select('order_items.product_id')
            ->selectRaw('COUNT(DISTINCT orders.id) as order_count')
            ->selectRaw('MAX(orders.created_at) as last_bought_at')
            ->groupBy('order_items.product_id')
            ->havingRaw('COUNT(DISTINCT orders.id) > 1')
            ->orderByDesc('order_count')
            ->orderByDesc('last_bought_at')
            ->value('order_items.product_id');

        $repurchaseProductId = $repeatProductId ?: $topInterestProductId;

        $lastOrderAt = $orderDates->last();

        if ($orderDates->count() < 2) {
            // Chỉ mới có 1 đơn -> chưa có chu kỳ thật, dùng mặc định với độ tin cậy thấp
            return [
                0.3,
                $lastOrderAt->copy()->addDays(self::DEFAULT_REPURCHASE_CYCLE_DAYS),
                $repurchaseProductId,
            ];
        }

        // Tính khoảng cách (ngày) giữa các đơn liên tiếp
        $intervals = [];
        for ($i = 1; $i < $orderDates->count(); $i++) {
            $intervals[] = $orderDates[$i - 1]->diffInDays($orderDates[$i]);
        }

        $avgInterval = max(7, array_sum($intervals) / count($intervals)); // tối thiểu 7 ngày

        // Độ lệch chuẩn của chu kỳ: càng đều đặn thì dự đoán càng đáng tin
        $variance = 0.0;
        foreach ($intervals as $i) {
            $variance += ($i - $avgInterval) ** 2;
        }
        $stdDev         = sqrt($variance / count($intervals));
        $coefVariation  = $avgInterval > 0 ? $stdDev / $avgInterval : 1;

        // Chu kỳ đều (coefVariation thấp) -> xác suất cao, dao động trong khoảng 0.3 - 0.9
        $probability = max(0.3, min(0.9, 1 - $coefVariation));

        $predictedAt = $lastOrderAt->copy()->addDays((int) round($avgInterval));

        // Nếu mốc dự đoán đã trôi qua mà khách chưa quay lại -> khách "đang nợ" 1 chu kỳ,
        // tăng nhẹ xác suất vì khả năng sắp mua lại là cao hơn.
        if ($predictedAt->isPast()) {
            $probability = min(0.95, $probability + 0.1);
        }

        return [round($probability, 2), $predictedAt, $repurchaseProductId];
    }

    /**
     * Gợi ý có nên gửi voucher hay không, kèm lý do ngắn gọn (<= 100 ký tự để khớp cột DB).
     * Ưu tiên theo thứ tự: sắp đến hạn mua lại > xem nhiều nhưng chưa mua > đã thêm giỏ nhưng chưa thanh toán.
     *
     * @return array{0: bool, 1: ?string}
     */
    private function recommendVoucher(
        bool $hasOrdered,
        bool $hasAddedCart,
        float $scoreView,
        ?Carbon $predictedRepurchaseAt,
        ?array $priceRange
    ): array {
        $now = now();

        $activeVouchers = Voucher::where('is_active', true)
            ->where('expires_at', '>=', $now)
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->orderBy('min_order_value')
            ->get(['code', 'min_order_value']);

        if ($activeVouchers->isEmpty()) {
            return [false, null]; // Không có voucher nào đang hoạt động thì không gợi ý
        }

        $minBudget = $priceRange['min'] ?? 0;

        // Chọn voucher có min_order_value phù hợp nhất với khoảng giá khách quan tâm,
        // nếu không có cái nào vừa thì fallback voucher có điều kiện thấp nhất.
        $pickVoucher = function () use ($activeVouchers, $minBudget) {
            return $activeVouchers->first(fn ($v) => (float) $v->min_order_value <= $minBudget)
                ?? $activeVouchers->first();
        };

        // Ưu tiên 1: sắp đến hạn mua lại trong vòng REPURCHASE_SOON_DAYS ngày tới
        if ($hasOrdered && $predictedRepurchaseAt) {
            $daysUntil = (int) floor(($predictedRepurchaseAt->timestamp - $now->timestamp) / 86400);
            if ($daysUntil <= self::REPURCHASE_SOON_DAYS) {
                $voucher = $pickVoucher();
                return [true, "Sắp đến hạn mua lại - gợi ý mã {$voucher->code}"];
            }
        }

        // Ưu tiên 2: xem nhiều sản phẩm nhưng chưa từng mua -> ưu đãi chào mừng để chốt đơn đầu
        if (!$hasOrdered && $scoreView >= self::THRESHOLD_WARM) {
            $voucher = $activeVouchers->first(); // voucher dễ áp dụng nhất (min_order_value thấp nhất)
            return [true, "Xem nhiều SP nhưng chưa mua - gợi ý mã {$voucher->code}"];
        }

        // Ưu tiên 3: đã thêm giỏ nhưng chưa thanh toán -> nhắc/thúc đẩy hoàn tất đơn
        if (!$hasOrdered && $hasAddedCart) {
            $voucher = $activeVouchers->first();
            return [true, "Đã thêm giỏ nhưng chưa thanh toán - gợi ý mã {$voucher->code}"];
        }

        return [false, null];
    }
}