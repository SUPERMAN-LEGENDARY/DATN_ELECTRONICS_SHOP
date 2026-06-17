<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    // ─── Danh sách ────────────────────────────────────────────────
    public function index(Request $request)
    {
        $type   = $request->get('type', 'category');
        $search = $request->get('search');

        $items = Category::where('type', $type)
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.categories.index', compact('items', 'type', 'search'));
    }

    // ─── Form tạo mới ─────────────────────────────────────────────
    public function create(Request $request)
    {
        $type = $request->get('type', 'category');
        return view('admin.categories.form', compact('type'));
    }

    // ─── Lưu mới ──────────────────────────────────────────────────
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'type'      => 'required|in:category,brand',
            'logo'      => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'is_active' => 'boolean',
        ]);

        $data['slug']      = $this->uniqueSlug($request->name);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        Category::create($data);

        return redirect()
            ->route('admin.categories.index', ['type' => $data['type']])
            ->with('success', 'Đã thêm thành công.');
    }

    // ─── Form chỉnh sửa ───────────────────────────────────────────
    public function edit(Category $category)
    {
        $type = $category->type;
        return view('admin.categories.form', compact('category', 'type'));
    }

    // ─── Cập nhật ─────────────────────────────────────────────────
    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'logo'      => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'is_active' => 'boolean',
        ]);

        // Đổi slug nếu tên thay đổi
        if ($category->name !== $request->name) {
            $data['slug'] = $this->uniqueSlug($request->name, $category->id);
        }

        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('logo')) {
            // Xóa logo cũ nếu có
            if ($category->logo) {
                \Storage::disk('public')->delete($category->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $category->update($data);

        return redirect()
            ->route('admin.categories.index', ['type' => $category->type])
            ->with('success', 'Đã cập nhật thành công.');
    }

    // ─── Xóa ──────────────────────────────────────────────────────
    public function destroy(Category $category)
    {
        // Kiểm tra xem có sản phẩm đang dùng không
        $count = $category->type === 'brand'
            ? $category->brandProducts()->count()
            : $category->products()->count();

        if ($count > 0) {
            return back()->with('error', "Không thể xóa: còn {$count} sản phẩm đang dùng.");
        }

        if ($category->logo) {
            \Storage::disk('public')->delete($category->logo);
        }

        $type = $category->type;
        $category->delete();

        return redirect()
            ->route('admin.categories.index', ['type' => $type])
            ->with('success', 'Đã xóa thành công.');
    }

    // ─── Bật/tắt trạng thái ───────────────────────────────────────
    public function toggleActive(Category $category)
    {
        $category->update(['is_active' => !$category->is_active]);

        return back()->with('success',
            $category->is_active ? 'Đã kích hoạt.' : 'Đã tắt kích hoạt.'
        );
    }

    // ─── Helper: tạo slug không trùng ─────────────────────────────
    private function uniqueSlug(string $name, ?int $excludeId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i    = 1;

        while (
            Category::where('slug', $slug)
                ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
                ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}