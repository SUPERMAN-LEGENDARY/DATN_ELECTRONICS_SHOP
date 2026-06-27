<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class NewsController extends Controller
{
    /* ──────────────── DANH SÁCH ──────────────── */
    public function index(Request $request): View
    {
        $query = News::with(['author', 'category']);

        if ($request->filled('category')) {
            $query->where('news_category_id', $request->category);
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active' ? 1 : 0);
        }
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where('title', 'like', "%$q%");
        }

        $newsList     = $query->orderByDesc('id')->paginate(15)->withQueryString();
        $categories   = NewsCategory::orderBy('name')->get();
        $trashedCount = News::onlyTrashed()->count();

        return view('admin.news.index', compact('newsList', 'categories', 'trashedCount'));
    }

    /* ──────────────── TẠO MỚI ──────────────── */
    public function create(): View
    {
        $categories = NewsCategory::where('is_active', 1)->orderBy('name')->get();
        return view('admin.news.form', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'news_category_id' => ['required', 'exists:news_categories,id'],
            'excerpt'          => ['nullable', 'string', 'max:500'],
            'content'          => ['required', 'string'],
            'thumbnail'        => ['nullable', 'image', 'max:2048'],
            'is_active'        => ['boolean'],
            'published_at'     => ['nullable', 'date'],
        ], [
            'excerpt.max' => 'Mô tả ngắn không vượt quá :max ký tự.',
        ]);

        $data['slug']      = News::generateSlug($data['title']);
        $data['user_id']   = auth()->id();
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('news', 'public');
        }

        News::create($data);

        return redirect()->route('admin.news.index')->with('success', 'Đã thêm bài viết thành công.');
    }

    /* ──────────────── CHỈNH SỬA ──────────────── */
    public function edit(News $news): View
    {
        $categories = NewsCategory::where('is_active', 1)->orderBy('name')->get();
        return view('admin.news.form', compact('news', 'categories'));
    }

    public function update(Request $request, News $news): RedirectResponse
    {
        $data = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'news_category_id' => ['required', 'exists:news_categories,id'],
            'excerpt'          => ['nullable', 'string', 'max:500'],
            'content'          => ['required', 'string'],
            'thumbnail'        => ['nullable', 'image', 'max:2048'],
            'is_active'        => ['boolean'],
            'published_at'     => ['nullable', 'date'],
        ], [
            'excerpt.max' => 'Mô tả ngắn không vượt quá :max ký tự.',
        ]);

        $data['slug']      = News::generateSlug($data['title'], $news->id);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('thumbnail')) {
            if ($news->thumbnail) {
                Storage::disk('public')->delete($news->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('news', 'public');
        }

        $news->update($data);

        return redirect()->route('admin.news.index')->with('success', 'Đã cập nhật bài viết.');
    }

    /* ──────────────── XOÁ MỀM (CHUYỂN VÀO THÙNG RÁC) ──────────────── */
    public function destroy(News $news): RedirectResponse
    {
        $news->delete(); // ghi deleted_at, chuyển vào thùng rác — KHÔNG xoá file ảnh
        return back()->with('success', 'Đã chuyển bài viết vào thùng rác.');
    }

    /* ──────────────── THÙNG RÁC BÀI VIẾT ──────────────── */
    public function trash(Request $request): View
    {
        $query = News::onlyTrashed()->with(['author', 'category']);

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where('title', 'like', "%$q%");
        }

        $newsList = $query->orderByDesc('deleted_at')->paginate(15)->withQueryString();

        return view('admin.news.trash', compact('newsList'));
    }

    /* ──────────────── KHÔI PHỤC 1 BÀI VIẾT ──────────────── */
    public function restore(int $id): RedirectResponse
    {
        $news = News::onlyTrashed()->findOrFail($id);
        $news->restore();

        return redirect()->route('admin.news.trash')->with('success', "Đã khôi phục \"{$news->title}\".");
    }

    /* ──────────────── KHÔI PHỤC TẤT CẢ BÀI VIẾT ──────────────── */
    public function restoreAll(): RedirectResponse
    {
        $count = News::onlyTrashed()->count();
        News::onlyTrashed()->restore();

        return redirect()->route('admin.news.trash')->with('success', "Đã khôi phục {$count} bài viết.");
    }

    /* ──────────────── XOÁ VĨNH VIỄN 1 BÀI VIẾT ──────────────── */
    public function forceDelete(int $id): RedirectResponse
    {
        $news = News::onlyTrashed()->findOrFail($id);

        if ($news->thumbnail) {
            Storage::disk('public')->delete($news->thumbnail);
        }
        $news->forceDelete();

        return redirect()->route('admin.news.trash')->with('success', 'Đã xoá vĩnh viễn bài viết.');
    }

    /* ──────────────── DỌN SẠCH THÙNG RÁC BÀI VIẾT ──────────────── */
    public function emptyTrash(): RedirectResponse
    {
        $trashed = News::onlyTrashed()->get();

        foreach ($trashed as $news) {
            if ($news->thumbnail) {
                Storage::disk('public')->delete($news->thumbnail);
            }
            $news->forceDelete();
        }

        return redirect()->route('admin.news.trash')->with('success', 'Đã dọn sạch thùng rác.');
    }

    /* ──────────────── TOGGLE ACTIVE ──────────────── */
    public function toggleActive(News $news): RedirectResponse
    {
        $news->update(['is_active' => ! $news->is_active]);
        return back()->with('success', $news->is_active ? 'Đã hiển thị bài viết.' : 'Đã ẩn bài viết.');
    }

    /* ──────────── DANH MỤC TIN TỨC ──────────── */
    public function categories(): View
    {
        $categories   = NewsCategory::withCount('news')->orderByDesc('id')->paginate(20);
        $trashedCount = NewsCategory::onlyTrashed()->count();
        return view('admin.news.categories', compact('categories', 'trashedCount'));
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'is_active' => ['boolean'],
        ]);
        $data['slug']      = NewsCategory::generateSlug($data['name']);
        $data['is_active'] = $request->boolean('is_active', true);
        NewsCategory::create($data);
        return back()->with('success', 'Đã thêm danh mục tin tức.');
    }

    public function updateCategory(Request $request, NewsCategory $newsCategory): RedirectResponse
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'is_active' => ['boolean'],
        ]);
        $data['slug']      = NewsCategory::generateSlug($data['name'], $newsCategory->id);
        $data['is_active'] = $request->boolean('is_active', true);
        $newsCategory->update($data);
        return back()->with('success', 'Đã cập nhật danh mục.');
    }

    public function destroyCategory(NewsCategory $newsCategory): RedirectResponse
    {
        if ($newsCategory->news()->count() > 0) {
            return back()->with('error', 'Không thể xoá danh mục còn chứa bài viết.');
        }
        $newsCategory->delete(); // ghi deleted_at, chuyển vào thùng rác
        return back()->with('success', 'Đã chuyển danh mục vào thùng rác.');
    }

    /* ──────────────── THÙNG RÁC DANH MỤC TIN TỨC ──────────────── */
    public function categoriesTrash(Request $request): View
    {
        $query = NewsCategory::onlyTrashed();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where('name', 'like', "%$q%");
        }

        $categories = $query->orderByDesc('deleted_at')->paginate(20)->withQueryString();

        return view('admin.news.categories-trash', compact('categories'));
    }

    public function restoreCategory(int $id): RedirectResponse
    {
        $newsCategory = NewsCategory::onlyTrashed()->findOrFail($id);
        $newsCategory->restore();

        return redirect()->route('admin.news.categories.trash')
            ->with('success', "Đã khôi phục danh mục \"{$newsCategory->name}\".");
    }

    public function restoreAllCategories(): RedirectResponse
    {
        $count = NewsCategory::onlyTrashed()->count();
        NewsCategory::onlyTrashed()->restore();

        return redirect()->route('admin.news.categories.trash')
            ->with('success', "Đã khôi phục {$count} danh mục.");
    }

    public function forceDeleteCategory(int $id): RedirectResponse
    {
        $newsCategory = NewsCategory::onlyTrashed()->findOrFail($id);

        // Kiểm tra ràng buộc khóa ngoại — kể cả bài viết đã ở thùng rác
        $count = $newsCategory->news()->withTrashed()->count();
        if ($count > 0) {
            return redirect()->route('admin.news.categories.trash')
                ->with('error', "Không thể xóa vĩnh viễn: còn {$count} bài viết (kể cả trong thùng rác) đang tham chiếu.");
        }

        $newsCategory->forceDelete();

        return redirect()->route('admin.news.categories.trash')
            ->with('success', 'Đã xoá vĩnh viễn danh mục.');
    }

    public function emptyTrashCategories(): RedirectResponse
    {
        $trashed = NewsCategory::onlyTrashed()->get();

        $deleted = 0;
        $skipped = 0;

        foreach ($trashed as $newsCategory) {
            if ($newsCategory->news()->withTrashed()->count() > 0) {
                $skipped++;
                continue;
            }
            $newsCategory->forceDelete();
            $deleted++;
        }

        $message = "Đã xóa vĩnh viễn {$deleted} danh mục.";
        if ($skipped > 0) {
            $message .= " Bỏ qua {$skipped} danh mục còn bài viết tham chiếu.";
        }

        return redirect()->route('admin.news.categories.trash')->with('success', $message);
    }
}
