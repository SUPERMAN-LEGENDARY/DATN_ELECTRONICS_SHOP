<?php

namespace App\Services;

use App\Models\ProductView;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class BehaviorLogger
{
    /**
     * Ghi 1 dòng log hành vi khách hàng với sản phẩm.
     *
     * @param int $productId
     * @param string $eventType  view | add_to_cart | compare | click_suggestion | wishlist
     * @param string|null $source  vd: chatbot, search, homepage, category
     * @param int|null $duration  thời gian xem (giây), chỉ dùng cho event 'view'
     */
    public static function log(int $productId, string $eventType = 'view', ?string $source = null, ?int $duration = null): void
    {
        try {
            ProductView::create([
                'user_id'          => Auth::id(), // null nếu khách chưa đăng nhập
                'session_token'    => session()->getId(),
                'product_id'       => $productId,
                'event_type'       => $eventType,
                'duration_seconds' => $duration,
                'source'           => $source ?? self::detectSource(),
            ]);
        } catch (\Throwable $e) {
            // Không để lỗi log làm hỏng trải nghiệm người dùng
            report($e);
        }
    }

    /**
     * Tự đoán nguồn truy cập dựa vào trang trước đó (referer).
     */
    private static function detectSource(): string
    {
        $referer = Request::header('referer', '');

        return match (true) {
            str_contains($referer, '/search')   => 'search',
            str_contains($referer, '/products') => 'category',
            $referer === ''                     => 'direct',
            default                              => 'homepage',
        };
    }
}