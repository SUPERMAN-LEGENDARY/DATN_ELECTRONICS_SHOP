<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    /**
     * Danh sách đánh giá của tôi
     */
    public function index()
    {
        $reviews = Review::with('product')
            ->where('user_id', auth()->id())
            ->latest('created_at')
            ->paginate(10);

        return view('profile.review_index', compact('reviews'));
    }

    /**
     * Nếu người dùng vào GET /profile/order/{order}/review
     * -> Tự động chuyển hướng về trang Chi tiết đơn hàng (nơi có sẵn Modal Popup Đánh giá)
     */
    public function create(Order $order)
    {
        abort_if($order->user_id != auth()->id(), 403);

        return redirect()->route('profile.order.show', [
            'order' => $order->id,
            'open_review' => 1
        ]);
    }

    /**
     * Chi tiết đơn hàng
     */
    public function show(Order $order)
    {
        abort_if($order->user_id != auth()->id(), 403);

        $order->load('items.product.category');

        $reviewedProductIds = Review::where('user_id', auth()->id())
            ->pluck('product_id')
            ->toArray();

        return view('profile.order_detail', compact(
            'order',
            'reviewedProductIds'
        ));
    }

    /**
     * Lưu đánh giá (Xử lý đa sản phẩm + Tải lên hình ảnh)
     */
    public function store(Request $request, Order $order)
    {
        // 1. Kiểm tra quyền sở hữu đơn hàng
        abort_if($order->user_id != auth()->id(), 403);

        // 2. Chỉ đánh giá khi đơn hàng đã giao thành công
        abort_if(
            $order->status !== 'delivered',
            403,
            'Chỉ có thể đánh giá đơn hàng đã giao thành công.'
        );

        // 3. Validate dữ liệu đầu vào (Bao gồm kiểm tra ảnh)
        $data = $request->validate([
            'reviews' => 'required|array',
            'reviews.*.product_id' => 'required|exists:products,id',
            'reviews.*.rating'     => 'required|integer|between:1,5',
            'reviews.*.comment'    => 'nullable|string|max:1000',
            'reviews.*.images.*'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $saved = 0;
        $productIds = $order->items->pluck('product_id')->toArray();

        // 4. Lặp qua danh sách đánh giá từng sản phẩm
        foreach ($data['reviews'] as $index => $reviewData) {

            $productId = $reviewData['product_id'];

            // Kiểm tra sản phẩm có thuộc đơn hàng này không
            if (!in_array($productId, $productIds)) {
                continue;
            }

            // Kiểm tra xem người dùng đã từng đánh giá sản phẩm này chưa
            $exists = Review::where('user_id', auth()->id())
                ->where('product_id', $productId)
                ->exists();

            if ($exists) {
                continue;
            }

            // Xử lý tải lên hình ảnh (nếu có)
            $imagePaths = [];
            if ($request->hasFile("reviews.{$index}.images")) {
                foreach ($request->file("reviews.{$index}.images") as $file) {
                    if ($file->isValid()) {
                        $path = $file->store('reviews', 'public');
                        $imagePaths[] = $path;
                    }
                }
            }

            // Tạo bản ghi đánh giá
            Review::create([
                'user_id'        => auth()->id(),
                'product_id'     => $productId,
                'rating'         => $reviewData['rating'],
                'content'        => $reviewData['comment'] ?? null,
                'images'         => !empty($imagePaths) ? json_encode($imagePaths) : null,
                'is_visible'     => true,
                'admin_reply'    => null,
                'bad_words_flag' => false,
            ]);

            $saved++;
        }

        // 5. Kết quả phản hồi
        if ($saved === 0) {
            return back()->with(
                'error',
                'Các sản phẩm trong đơn hàng này đã được đánh giá trước đó.'
            );
        }

        return redirect()
            ->route('profile.order.show', $order)
            ->with('success', 'Cảm ơn bạn đã đánh giá sản phẩm!');
    }
}