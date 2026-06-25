<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BannerController extends Controller
{
    // ─── Danh sách banner ────────────────────────────────────────
    public function index(Request $request): View
    {
        $query = Banner::query();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qb) use ($q) {
                $qb->where('title', 'like', "%$q%")
                    ->orWhere('label', 'like', "%$q%");
            });
        }

        $banners = $query->ordered()->paginate(15)->withQueryString();
        $trashedCount = Banner::onlyTrashed()->count();

        return view('admin.banners.index', compact('banners', 'trashedCount'));
    }

    // ─── Form thêm mới ───────────────────────────────────────────
    public function create(): View
    {
        return view('admin.banners.form', ['banner' => new Banner()]);
    }

    // ─── Lưu banner mới ──────────────────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        if ($request->hasFile('image')) {
            $data['image'] = Storage::url($request->file('image')->store('banners', 'public'));
        }

        Banner::create($data);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Thêm banner thành công!');
    }

    // ─── Form chỉnh sửa ──────────────────────────────────────────
    public function edit(Banner $banner): View
    {
        return view('admin.banners.form', compact('banner'));
    }

    // ─── Cập nhật banner ─────────────────────────────────────────
    public function update(Request $request, Banner $banner): RedirectResponse
    {
        $data = $this->validateData($request);

        if ($request->hasFile('image')) {
            if ($banner->image) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $banner->image));
            }
            $data['image'] = Storage::url($request->file('image')->store('banners', 'public'));
        } else {
            unset($data['image']);
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Cập nhật banner thành công!');
    }

    // ─── Bật / tắt hiển thị ──────────────────────────────────────
    public function toggleActive(Banner $banner): RedirectResponse
    {
        $banner->update(['is_active' => ! $banner->is_active]);

        return back()->with('success', $banner->is_active
            ? "Đã hiển thị banner \"{$banner->title}\"."
            : "Đã ẩn banner \"{$banner->title}\".");
    }

    // ─── Xoá (chuyển vào thùng rác) ──────────────────────────────
    public function destroy(Banner $banner): RedirectResponse
    {
        $banner->delete();

        return redirect()->route('admin.banners.index')
            ->with('success', "Đã chuyển banner \"{$banner->title}\" vào thùng rác.");
    }

    // ─── Thùng rác ────────────────────────────────────────────────
    public function trash(): View
    {
        $banners = Banner::onlyTrashed()->ordered()->paginate(15);

        return view('admin.banners.trash', compact('banners'));
    }

    public function restore(int $id): RedirectResponse
    {
        $banner = Banner::onlyTrashed()->findOrFail($id);
        $banner->restore();

        return back()->with('success', "Đã khôi phục banner \"{$banner->title}\".");
    }

    public function restoreAll(): RedirectResponse
    {
        Banner::onlyTrashed()->restore();

        return back()->with('success', 'Đã khôi phục tất cả banner trong thùng rác.');
    }

    public function forceDelete(int $id): RedirectResponse
    {
        $banner = Banner::onlyTrashed()->findOrFail($id);

        if ($banner->image) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $banner->image));
        }

        $banner->forceDelete();

        return back()->with('success', 'Đã xoá vĩnh viễn banner.');
    }

    public function emptyTrash(): RedirectResponse
    {
        $trashed = Banner::onlyTrashed()->get();

        foreach ($trashed as $banner) {
            if ($banner->image) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $banner->image));
            }
        }

        Banner::onlyTrashed()->forceDelete();

        return back()->with('success', 'Đã dọn sạch thùng rác banner.');
    }

    // ─── Validate dữ liệu chung cho store/update ─────────────────
    private function validateData(Request $request): array
    {
        $validated = $request->validate([
            'layout'      => ['required', 'in:split,image'],
            'label'       => ['nullable', 'string', 'max:255'],
            'title'       => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price_text'  => ['nullable', 'string', 'max:255'],
            'button_text' => ['nullable', 'string', 'max:100'],
            'button_link' => ['nullable', 'string', 'max:255'],
            'bg_color'    => ['nullable', 'string', 'max:20'],
            'text_color'  => ['nullable', 'string', 'max:20'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'is_active'   => ['nullable', 'boolean'],
            'image'       => ['nullable', 'image', 'max:4096'],
        ]);

        // Banner kiểu "chỉ ảnh" thì không cần các trường chữ
        if ($validated['layout'] === 'image') {
            $validated['label'] = $validated['title'] = $validated['description'] = null;
            $validated['price_text'] = $validated['button_text'] = null;
            $validated['bg_color'] = $validated['text_color'] = null;
        }

        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_active']  = $request->boolean('is_active');

        unset($validated['image']);

        return $validated;
    }
}
