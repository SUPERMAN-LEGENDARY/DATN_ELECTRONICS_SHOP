<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Services\BadWordDetector;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['product', 'user'])
            ->latest('created_at');

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('is_visible', $request->status === 'visible');
        }

        // Lọc theo rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Lọc theo cờ từ không chuẩn mực
        if ($request->filled('bad_words')) {
            $query->where('bad_words_flag', $request->bad_words === '1');
        }

        // Tìm kiếm theo tên sản phẩm hoặc người dùng
        if ($request->filled('search')) {
            $kw = $request->search;
            $query->where(function ($q) use ($kw) {
                $q->whereHas('product', fn($p) => $p->where('name', 'like', "%{$kw}%"))
                  ->orWhereHas('user',    fn($u) => $u->where('name', 'like', "%{$kw}%"));
            });
        }

        $reviews = $query->paginate(20)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    /** Ẩn / hiện một review */
    public function toggleVisible(Review $review)
    {
        $review->update(['is_visible' => !$review->is_visible]);

        return back()->with('success', $review->is_visible ? 'Đã hiển thị đánh giá.' : 'Đã ẩn đánh giá.');
    }

    /** Lưu reply (admin_reply) */
    public function reply(Request $request, Review $review)
    {
        $request->validate([
            'admin_reply' => 'required|string|max:1000',
        ], [
            'admin_reply.required' => 'Vui lòng nhập nội dung phản hồi.',
        ]);

        $review->update(['admin_reply' => $request->admin_reply]);

        return back()->with('success', 'Đã lưu phản hồi.');
    }

    /** Xoá reply */
    public function deleteReply(Review $review)
    {
        $review->update(['admin_reply' => null]);

        return back()->with('success', 'Đã xoá phản hồi.');
    }

    /** Xoá hẳn review */
    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('success', 'Đã xoá đánh giá.');
    }

    /** Toggle visible hàng loạt */
    public function bulkToggle(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'action' => 'required|in:show,hide']);

        $visible = $request->action === 'show';
        Review::whereIn('id', $request->ids)->update(['is_visible' => $visible]);

        return back()->with('success', $visible ? 'Đã hiển thị các đánh giá đã chọn.' : 'Đã ẩn các đánh giá đã chọn.');
    }
}