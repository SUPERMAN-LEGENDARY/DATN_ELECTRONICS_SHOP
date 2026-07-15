<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Danh sách đánh giá của tôi
     */
    public function index()
    {
        $reviews = Review::with(['product'])
            ->where('user_id', auth()->id())
            ->latest('created_at')
            ->paginate(10);

        return view('profile.review_index', compact('reviews'));
    }

    /**
     * Form đánh giá các sản phẩm trong đơn hàng đã giao
     */
    public function create(Order $order)
    {
        abort_if($order->user_id != auth()->id(), 403);
        abort_if($order->status !== 'delivered', 403, 'Chỉ có thể đánh giá đơn hàng đã giao thành công.');

        $order->load('items.product');

        $reviewedProductIds = Review::where('user_id', auth()->id())
            ->whereIn('product_id', $order->items->pluck('product_id'))
            ->pluck('product_id')
            ->all();

        return view('profile.review', compact('order', 'reviewedProductIds'));
    }

    /**
     * Lưu đánh giá cho các sản phẩm trong đơn hàng
     */
    public function store(Request $request, Order $order)
    {
        abort_if($order->user_id != auth()->id(), 403);
        abort_if($order->status !== 'delivered', 403);

        $data = $request->validate([
            'ratings'            => 'required|array',
            'ratings.*'          => 'nullable|integer|min:1|max:5',
            'contents'           => 'array',
            'contents.*'         => 'nullable|string|max:1000',
        ]);

        $productIds = $order->items->pluck('product_id')->all();
        $saved = 0;

        foreach ($data['ratings'] as $productId => $rating) {
            if (!$rating || !in_array($productId, $productIds)) {
                continue;
            }

            $exists = Review::where('product_id', $productId)
                ->where('user_id', auth()->id())
                ->exists();

            if ($exists) {
                continue;
            }

            Review::create([
                'product_id'  => $productId,
                'user_id'     => auth()->id(),
                'rating'      => $rating,
                'content'     => $data['contents'][$productId] ?? null,
                'is_visible'  => true,
            ]);

            $saved++;
        }

        if ($saved === 0) {
            return back()->with('error', 'Vui lòng chọn số sao cho ít nhất một sản phẩm chưa đánh giá.');
        }

        return redirect()
            ->route('profile.order.show', $order)
            ->with('success', 'Cảm ơn bạn đã đánh giá sản phẩm!');
    }
}
