<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = News::query()
            ->with(['category', 'author'])
            ->where('is_active', true);

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('keyword')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->keyword . '%')
                    ->orWhere('content', 'like', '%' . $request->keyword . '%');
            });
        }

        $news = $query
            ->orderByDesc('published_at')
            ->paginate(12)
            ->withQueryString();

        $categories = NewsCategory::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('news.index', compact('news', 'categories'));
    }

    public function show(string $slug)
    {
        $article = News::with(['category', 'author'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $article->increment('views');

        $relatedNews = News::with('category')
            ->where('is_active', true)
            ->where('id', '!=', $article->id)
            ->where('news_category_id', $article->news_category_id)
            ->orderByDesc('published_at')
            ->limit(4)
            ->get();

        $latestNews = News::where('is_active', true)
            ->where('id', '!=', $article->id)
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        return view('news.show', compact('article', 'relatedNews', 'latestNews'));
    }

    public function category(string $slug)
    {
        $category = NewsCategory::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $news = News::with(['category', 'author'])
            ->where('is_active', true)
            ->where('news_category_id', $category->id)
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('news.index', [
            'news'       => $news,
            'categories' => NewsCategory::where('is_active', true)->orderBy('name')->get(),
            'activeCategory' => $category,
        ]);
    }
}
