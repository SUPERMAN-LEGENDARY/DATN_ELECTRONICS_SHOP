<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ScoreAllCustomerAiProfilesJob;
use App\Models\CustomerAiProfile;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductView;
use App\Models\User;
use App\Models\Voucher;
use App\Services\CustomerAiScoringService;
use App\Services\CustomerAiSummaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class LeadController extends Controller
{
    /**
     * Danh sách khách hàng tiềm năng, sắp xếp theo điểm AI giảm dần.
     * Lọc theo: lead_label (hot/warm/cold), lead_stage, có gợi ý voucher hay không, từ khoá tên/email.
     */
    public function index(Request $request)
    {
        $query = CustomerAiProfile::with(['user', 'topInterestProduct', 'lastSeenProduct'])
            ->whereHas('user'); // tránh profile mồ côi nếu user bị xoá

        if ($request->filled('label')) {
            $query->where('lead_label', $request->label);
        }

        if ($request->filled('stage')) {
            $query->where('lead_stage', $request->stage);
        }

        if ($request->boolean('voucher_only')) {
            $query->where('voucher_recommended', true);
        }

        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->whereHas('user', function ($u) use ($keyword) {
                $u->where('name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%")
                  ->orWhere('phone', 'like', "%{$keyword}%");
            });
        }

        $profiles = $query->orderByDesc('total_score')->paginate(20)->withQueryString();

        // Thống kê nhanh cho dashboard nhỏ trên đầu trang
        $stats = [
            'hot'  => CustomerAiProfile::where('lead_label', 'hot')->count(),
            'warm' => CustomerAiProfile::where('lead_label', 'warm')->count(),
            'cold' => CustomerAiProfile::where('lead_label', 'cold')->count(),
            'voucher_suggested' => CustomerAiProfile::where('voucher_recommended', true)->count(),
        ];

        return view('admin.leads.index', compact('profiles', 'stats'));
    }

    /**
     * Chi tiết 1 khách hàng tiềm năng: điểm số, lịch sử hành vi, đơn hàng,
     * sản phẩm gợi ý hiện tại (AI) + form đề xuất thủ công + form tặng voucher.
     */
    public function show(User $user)
    {
        $profile = CustomerAiProfile::with([
            'topInterestProduct',
            'lastSeenProduct',
            'repurchaseProduct',
        ])->where('user_id', $user->id)->first();

        $suggestedProducts = collect();
        if ($profile && !empty($profile->suggested_products)) {
            $suggestedProducts = Product::whereIn('id', $profile->suggested_products)
                ->get()
                ->sortBy(fn ($p) => array_search($p->id, $profile->suggested_products))
                ->values();
        }

        $recentViews = ProductView::with('product')
            ->where('user_id', $user->id)
            ->latest()
            ->limit(20)
            ->get();

        $orders = Order::with('items')
            ->where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();

        // Voucher cá nhân đã từng tặng cho khách này
        $personalVouchers = Voucher::personal()
            ->where('assigned_user_id', $user->id)
            ->latest('id')
            ->get();

        // Danh sách sản phẩm để admin chọn khi đề xuất thủ công (giới hạn để không quá nặng)
        // Lưu ý: bảng products KHÔNG có cột discount_percent (cũng không có sale_price) — schema
        // thực chỉ có price/cost_price/list_price. Bỏ cột không tồn tại để hết lỗi SQL "Unknown column".
        $productOptions = Product::active()->orderBy('name')->limit(500)->get(['id', 'name', 'price']);

        return view('admin.leads.show', compact(
            'user', 'profile', 'suggestedProducts', 'recentViews',
            'orders', 'personalVouchers', 'productOptions'
        ));
    }

    /**
     * Admin đề xuất sản phẩm thủ công cho khách hàng — ghi đè/bổ sung vào
     * suggested_products của customer_ai_profiles. Nếu khách chưa có profile
     * (vd: khách mới, chưa đủ dữ liệu hành vi) thì tạo mới với điểm = 0.
     */
    public function updateSuggestions(Request $request, User $user)
    {
        $request->validate([
            'product_ids'   => 'required|array|min:1|max:20',
            'product_ids.*' => 'integer|exists:products,id',
        ]);

        $productIds = array_values(array_unique(array_map('intval', $request->product_ids)));

        CustomerAiProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'suggested_products' => $productIds,
                // Không đụng tới các điểm số khác (lead_label, total_score...) — đây là
                // gợi ý thủ công đè lên phần "suggested_products" do AI tính, các trường
                // còn lại vẫn giữ nguyên giá trị AI đã tính lần gần nhất (nếu có).
            ]
        );

        return back()->with('success', "Đã cập nhật {$user->name} — " . count($productIds) . ' sản phẩm đề xuất thủ công.');
    }

    /**
     * Admin tặng voucher cá nhân hoá cho 1 khách hàng tiềm năng.
     * Tự sinh mã ngẫu nhiên duy nhất, gắn assigned_user_id để chỉ khách này dùng được
     * (CheckoutController đã kiểm tra điều kiện này khi áp mã).
     */
    public function giftVoucher(Request $request, User $user)
    {
        $request->validate([
            'discount_percent' => 'required|integer|min:1|max:90',
            'min_order_value'  => 'nullable|integer|min:0',
            'expires_at'       => 'required|date|after:now',
            'note'             => 'nullable|string|max:255',
        ]);

        do {
            $code = 'GIFT-' . strtoupper(Str::random(6));
        } while (Voucher::where('code', $code)->exists());

        $voucher = Voucher::create([
            'code'              => $code,
            'assigned_user_id'  => $user->id,
            'note'              => $request->note ?: 'Admin tặng từ trang khách hàng tiềm năng',
            'discount_percent'  => $request->discount_percent,
            'min_order_value'   => $request->min_order_value ?? 0,
            'usage_limit'       => 1,
            'used_count'        => 0,
            'starts_at'         => now(),
            'expires_at'        => $request->expires_at,
            'is_active'         => true,
        ]);

        // Đánh dấu là đã xử lý gợi ý voucher để không bị lẫn với gợi ý AI cũ
        CustomerAiProfile::where('user_id', $user->id)->update([
            'voucher_recommended' => false,
            'voucher_reason'      => "Đã tặng mã {$voucher->code} (thủ công)",
        ]);

        return back()->with('success', "Đã tặng mã {$voucher->code} (-{$voucher->discount_percent}%) cho {$user->name}.");
    }

    /**
     * Tính lại điểm AI ngay cho 1 khách hàng (chạy lại logic của lệnh
     * customer:score-ai-profiles nhưng chỉ cho user này), dùng khi admin
     * vừa thao tác xong và muốn xem kết quả mới nhất luôn.
     *
     * Sau khi tính điểm xong (đã lưu DB), gọi thêm CustomerAiSummaryService để AI
     * viết 1 đoạn nhận định ngắn cho admin đọc nhanh, cũng lưu thẳng vào DB.
     *
     * Hỗ trợ cả 2 kiểu gọi:
     * - AJAX (fetch từ nút "Tính lại điểm"): trả JSON, không reload trang.
     * - Submit form thường (không có JS): redirect back như cũ, vẫn hoạt động bình thường.
     */
    public function recalculate(Request $request, User $user, CustomerAiSummaryService $summaryService)
    {
        Artisan::call('customer:score-ai-profiles', [
            '--user' => $user->id,
        ]);

        $profile = CustomerAiProfile::with(['topInterestProduct', 'lastSeenProduct'])
            ->where('user_id', $user->id)
            ->first();

        // AI viết tóm tắt chỉ khi đã có profile (tức khách có đủ dữ liệu hành vi để tính điểm).
        // Lỗi gọi AI (mạng, quota...) không được làm hỏng việc tính điểm chính, nên bọc riêng.
        if ($profile) {
            try {
                $summary = $summaryService->generate($user, $profile);
                if ($summary) {
                    $profile->update([
                        'ai_summary'              => $summary,
                        'ai_summary_generated_at' => now(),
                    ]);
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $message = "Đã tính lại điểm AI cho {$user->name}.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'profile' => $profile ? [
                    'lead_label'            => $profile->lead_label,
                    'lead_stage'            => $profile->lead_stage,
                    'total_score'           => round($profile->total_score, 1),
                    'score_view'            => round($profile->score_view, 1),
                    'score_chat'            => round($profile->score_chat, 1),
                    'score_order'           => round($profile->score_order, 1),
                    'keywords_history'      => $profile->keywords_history ?? [],
                    'voucher_recommended'   => $profile->voucher_recommended,
                    'voucher_reason'        => $profile->voucher_reason,
                    'ai_summary'            => $profile->ai_summary,
                    'ai_summary_generated_at' => $profile->ai_summary_generated_at?->format('d/m/Y H:i'),
                    'top_interest_product'  => $profile->topInterestProduct?->name,
                    'last_seen_product'     => $profile->lastSeenProduct?->name,
                    'scored_at'             => $profile->scored_at?->format('d/m/Y H:i'),
                ] : null,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Chấm điểm AI lại cho TOÀN BỘ khách hàng — chạy nền qua Queue vì có thể
     * mất nhiều phút nếu số lượng khách lớn (tránh timeout HTTP request).
     * Có khoá cache để chặn bấm trùng khi đang chạy.
     */
    public function recalculateAll(Request $request, CustomerAiScoringService $service)
    {
        if (Cache::get(ScoreAllCustomerAiProfilesJob::CACHE_KEY_RUNNING)) {
            return response()->json([
                'success' => false,
                'message' => 'Đang có 1 tiến trình chấm điểm chạy rồi, vui lòng đợi hoàn tất.',
            ], 409);
        }

        // Khoá ngay tại đây (không đợi job bắt đầu chạy) để 2 request gần như
        // đồng thời không cùng lọt qua điều kiện check phía trên.
        Cache::put(ScoreAllCustomerAiProfilesJob::CACHE_KEY_RUNNING, true, now()->addMinutes(35));

        // TẠM THỜI chạy ĐỒNG BỘ ngay trong request này, không qua Job/Queue/Batch
        // nữa (dùng khi môi trường chưa có queue worker chạy nền). Nếu sau này
        // deploy production với lượng khách hàng lớn, nên quay lại dùng
        // ScoreAllCustomerAiProfilesJob::dispatch() + chạy queue worker thật
        // (Supervisor/Horizon) để tránh timeout HTTP khi chấm điểm quá lâu.
        set_time_limit(0);

        try {
            $userIds = $service->eligibleUserIds();

            if ($userIds->isNotEmpty()) {
                $service->scoreUsers($userIds->all());
            }

            Cache::forget(ScoreAllCustomerAiProfilesJob::CACHE_KEY_RUNNING);
            Cache::put(ScoreAllCustomerAiProfilesJob::CACHE_KEY_LAST_DONE, now()->toDateTimeString(), now()->addDay());
            Cache::forget(ScoreAllCustomerAiProfilesJob::CACHE_KEY_LAST_ERROR);

            return response()->json([
                'success' => true,
                'message' => "Đã chấm điểm lại xong {$userIds->count()} khách hàng.",
            ]);
        } catch (\Throwable $e) {
            report($e);

            Cache::forget(ScoreAllCustomerAiProfilesJob::CACHE_KEY_RUNNING);
            Cache::put(ScoreAllCustomerAiProfilesJob::CACHE_KEY_LAST_ERROR, $e->getMessage(), now()->addDay());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi khi chấm điểm: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * FE gọi định kỳ (polling) để biết job chấm điểm hàng loạt đã xong chưa.
     */
    public function recalculateAllStatus(Request $request)
    {
        return response()->json([
            'running'           => (bool) Cache::get(ScoreAllCustomerAiProfilesJob::CACHE_KEY_RUNNING),
            'last_completed_at' => Cache::get(ScoreAllCustomerAiProfilesJob::CACHE_KEY_LAST_DONE),
            'last_error'        => Cache::get(ScoreAllCustomerAiProfilesJob::CACHE_KEY_LAST_ERROR),
        ]);
    }
}