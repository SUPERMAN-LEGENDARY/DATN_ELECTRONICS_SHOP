<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Review;
use App\Services\BadWordDetector;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // ─── Danh sách sản phẩm (có filter, sort, search, phân trang) ─

    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand'])
            ->withCount(['visibleReviews as reviews_count'])
            ->active()
            ->search($request->q)
            ->filterBrand($request->brand)
            ->filterCategory($request->category)
            ->filterPrice($request->price_from, $request->price_to)
            ->sorted($request->sort);

        // Lọc theo khoảng giá dạng checkbox (vd: 0_5000000)
        if ($request->filled('price') && is_array($request->price)) {
            $query->where(function ($q) use ($request) {
                foreach ($request->price as $range) {
                    [$from, $to] = explode('_', $range . '_');
                    $q->orWhere(function ($sub) use ($from, $to) {
                        if ($from !== '') $sub->where('price', '>=', $from);
                        if ($to   !== '') $sub->where('price', '<=', $to);
                    });
                }
            });
        }

        $totalProducts = (clone $query)->count();
        $products      = $query->paginate(24)->withQueryString();

        $categories = Category::categories()->active()->get();
        $brands     = Category::brands()->active()->get();

        return view('products.index', compact(
            'products', 'categories', 'brands', 'totalProducts'
        ));
    }

    // ─── Chi tiết sản phẩm ────────────────────────────────────────

    public function show(string $slug)
    {
        $product = Product::with([
                'category',
                'brand',
                'attributes.attribute',
                'variants.variantAttributes.attribute',
            ])
            ->withCount(['visibleReviews as reviews_count'])
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

        // Đánh giá phân trang (5 per page)
        $reviews = Review::with('user')
            ->where('product_id', $product->id)
            ->visible()
            ->latest()
            ->paginate(5, ['*'], 'review_page');

        // Phân bố rating 1-5
        $ratingDistribution = Review::where('product_id', $product->id)
            ->visible()
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating');

        // Sản phẩm liên quan (cùng category, khác bản thân)
        $relatedProducts = Product::with('brand')
            ->active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->limit(5)
            ->get();

        return view('products.show', compact(
            'product', 'reviews', 'ratingDistribution', 'relatedProducts'
        ));
    }

    // ─── Gửi đánh giá ────────────────────────────────────────────

    public function storeReview(Request $request, int $productId)
    {
        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'content' => 'nullable|string|max:1000',
        ]);

        /** @var int $userId */
        $userId = auth()->id();

        // Mỗi user chỉ review 1 lần
        $exists = Review::where('product_id', $productId)
            ->where('user_id', $userId)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Bạn đã đánh giá sản phẩm này rồi.');
        }

        // ── Kiểm tra từ không chuẩn mực ──────────────────────────
        $badWordResult = BadWordDetector::check($request->content);
        $hasBadWords   = $badWordResult['found'];

        Review::create([
            'product_id'     => $productId,
            'user_id'        => $userId,
            'rating'         => $request->rating,
            'content'        => $request->content,
            'is_visible'     => !$hasBadWords,   // tự động ẩn nếu có từ xấu
            'bad_words_flag' => $hasBadWords,
        ]);

        if ($hasBadWords) {
            return back()->with('error', 'Đánh giá của bạn chứa từ ngữ không phù hợp và đang chờ kiểm duyệt.');
        }

        return back()->with('success', 'Cảm ơn bạn đã đánh giá!');
    }
}