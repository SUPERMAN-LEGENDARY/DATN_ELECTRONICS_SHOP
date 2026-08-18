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
        \Illuminate\Support\Facades\Log::info('STORE METHOD CALLED', $request->all());
        $data = $this->validateData($request);
        $this->handleUploads($request, $data);

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
        \Illuminate\Support\Facades\Log::info('UPDATE METHOD CALLED', $request->all());
        $data = $this->validateData($request);
        $this->handleUploads($request, $data, $banner);

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

    // ─── Xử lý file tải lên ──────────────────────────────────────
    private function handleUploads(Request $request, array &$data, ?Banner $banner = null): void
    {
        // Template
        if ($data['creation_method'] === 'template' && $request->hasFile('template_image')) {
            $this->deleteOldFile($banner?->image);
            $data['image'] = Storage::url($request->file('template_image')->store('banners', 'public'));
            $data['template'] = null;
        }
        // Custom
        elseif ($data['creation_method'] === 'custom') {
            if ($request->hasFile('custom_image_desktop')) {
                $this->deleteOldFile($banner?->image);
                $data['image'] = Storage::url($request->file('custom_image_desktop')->store('banners', 'public'));
            }
            if ($request->hasFile('custom_image_mobile')) {
                $this->deleteOldFile($banner?->image_mobile);
                $data['image_mobile'] = Storage::url($request->file('custom_image_mobile')->store('banners', 'public'));
            }
        }
        // Upload
        elseif ($data['creation_method'] === 'upload') {
            if ($data['media_type'] === 'image' && $request->hasFile('image')) {
                $this->deleteOldFile($banner?->image);
                $data['image'] = Storage::url($request->file('image')->store('banners', 'public'));
            } elseif ($data['media_type'] === 'video' && $request->hasFile('video')) {
                $this->deleteOldFile($banner?->video);
                $data['video'] = Storage::url($request->file('video')->store('banners', 'public'));
            }
        }
    }

    private function deleteOldFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $path));
        }
    }

    // ─── Validate dữ liệu chung cho store/update ─────────────────
    private function validateData(Request $request): array
    {
        try {
            $validated = $request->validate([
                'creation_method' => ['required', 'in:template,custom,upload'],
                'banner_type'     => ['nullable', 'string', 'max:50'],
                'template'        => ['nullable', 'string', 'max:50'],
                'custom_layout'   => ['nullable', 'string', 'max:50'],
                'custom_bg_color' => ['nullable', 'string', 'max:20'],
                'custom_text_color'=> ['nullable', 'string', 'max:20'],
                'custom_btn_color'=> ['nullable', 'string', 'max:20'],
                'fx_shadow'       => ['nullable', 'boolean'],
                'fx_gradient'     => ['nullable', 'boolean'],
                'fx_radius'       => ['nullable', 'boolean'],
                'text_align'      => ['nullable', 'string', 'in:left,center,right'],
                'badge'           => ['nullable', 'string', 'max:255'],
                'title'           => ['nullable', 'string', 'max:255'],
                'description'     => ['nullable', 'string'],
                'price'           => ['nullable', 'integer', 'min:0'],
                'compare_price'   => ['nullable', 'integer', 'min:0'],
                'button_text'     => ['nullable', 'string', 'max:100'],
                'button_link'     => ['nullable', 'string', 'max:255'],
                'media_type'      => ['nullable', 'string', 'in:image,video'],
                'start_at'        => ['nullable', 'date'],
                'end_at'          => ['nullable', 'date', 'after_or_equal:start_at'],
                'is_active'       => ['nullable', 'boolean'],
                'sort_order'      => ['nullable', 'integer', 'min:0'],
                'template_image'  => ['nullable', 'image', 'max:4096'],
                'custom_image_desktop' => ['nullable', 'image', 'max:4096'],
                'custom_image_mobile'  => ['nullable', 'image', 'max:4096'],
                'image'           => ['nullable', 'image', 'max:4096'],
                'video'           => ['nullable', 'mimes:mp4', 'max:10240'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::error('Banner Validation Failed: ' . json_encode($e->errors()));
            throw $e;
        }

        $validated['label'] = $validated['badge'] ?? null;
        $validated['bg_color'] = $validated['custom_bg_color'] ?? null;
        $validated['text_color'] = $validated['custom_text_color'] ?? null;
        $validated['btn_color'] = $validated['custom_btn_color'] ?? null;
        
        // Prevent inserting null into NOT NULL columns
        $validated['layout'] = $validated['custom_layout'] ?? 'split';
        $validated['media_type'] = $validated['media_type'] ?? 'image';
        $validated['text_align'] = $validated['text_align'] ?? 'left';

        $validated['fx_shadow'] = $request->boolean('fx_shadow');
        $validated['fx_gradient'] = $request->boolean('fx_gradient');
        $validated['fx_radius'] = $request->boolean('fx_radius');
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        unset(
            $validated['badge'], $validated['custom_bg_color'], $validated['custom_text_color'], 
            $validated['custom_btn_color'], $validated['custom_layout'],
            $validated['template_image'], $validated['custom_image_desktop'], 
            $validated['custom_image_mobile'], $validated['image'], $validated['video']
        );

        return $validated;
    }
}
