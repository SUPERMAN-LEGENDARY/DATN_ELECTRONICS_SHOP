<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $banners = Banner::active()->ordered()->get();

        $newProducts = Product::with(['brand'])
            ->withCount('visibleReviews as reviews_count')
            ->active()
            ->latest()
            ->limit(4)
            ->get();

        $categories = Category::categories()->active()->get();
        $brands     = Category::brands()->active()->limit(6)->get();

        // Nếu sau này có model News thì gán vào đây
        $latestNews = collect();

        return view('home', compact('newProducts', 'latestNews', 'categories', 'brands', 'banners'));
    }
}
