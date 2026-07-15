<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Event;
use App\Models\News;
use App\Models\Product;
use App\Models\ProductView;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $banners = Banner::active()->ordered()->get();
        $events  = Event::active()->ongoing()->ordered()->get();

        $newProducts = Product::with(['brand'])
            ->withCount('visibleReviews as reviews_count')
            ->active()
            ->latest()
            ->limit(4)
            ->get();

        $categories = Category::categories()->active()->get();
        $brands     = Category::brands()->active()->limit(6)->get();

        $latestNews = News::with(['category'])
            ->where('is_active', true)
            ->orderByDesc('published_at')
            ->limit(4)
            ->get();

        $suggestedProducts = $this->buildSuggestedProducts($newProducts->pluck('id'));

        return view('home', compact('newProducts', 'latestNews', 'categories', 'brands', 'banners', 'events', 'suggestedProducts'));
    }

    /**
     * Gợi ý sản phẩm dựa trên lịch sử xem/tương tác (product_views) của khách đang đăng nhập.
     * Nếu chưa có lịch sử, fallback về sản phẩm mới nhất (khác với danh sách "Sản phẩm mới nhất").
     */
    private function buildSuggestedProducts($excludeIds)
    {
        if (! Auth::check()) {
            return collect();
        }

        $baseQuery = fn() => Product::with('brand')
            ->withCount('visibleReviews as reviews_count')
            ->active();

        $viewedProductIds = ProductView::where('user_id', Auth::id())
            ->pluck('product_id')
            ->unique();

        $viewedCategoryIds = Product::whereIn('id', $viewedProductIds)
            ->pluck('category_id')
            ->filter()
            ->unique();

        $suggested = collect();

        if ($viewedCategoryIds->isNotEmpty()) {
            $suggested = $baseQuery()
                ->whereIn('category_id', $viewedCategoryIds)
                ->whereNotIn('id', $viewedProductIds)
                ->inRandomOrder()
                ->limit(4)
                ->get();
        }

        if ($suggested->isEmpty()) {
            $suggested = $baseQuery()
                ->whereNotIn('id', $excludeIds)
                ->inRandomOrder()
                ->limit(4)
                ->get();
        }

        return $suggested;
    }
}
