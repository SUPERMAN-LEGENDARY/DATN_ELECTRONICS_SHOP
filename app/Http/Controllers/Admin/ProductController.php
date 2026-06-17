<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    // ─── Danh sách sản phẩm (có tìm kiếm, lọc) ──────────────────

    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand'])
            ->withCount('visibleReviews as reviews_count');

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $products   = $query->latest()->paginate(20)->withQueryString();
        $categories = Category::categories()->active()->get();
        $brands     = Category::brands()->active()->get();

        return view('admin.products.index', compact('products', 'categories', 'brands'));
    }

    // ─── Form tạo mới ────────────────────────────────────────────

    public function create()
    {
        $categories = Category::categories()->active()->get();
        $brands     = Category::brands()->active()->get();
        $attributes = Attribute::orderBy('name')->get();

        return view('admin.products.create', compact('categories', 'brands', 'attributes'));
    }

    // ─── Lưu sản phẩm mới ────────────────────────────────────────

    public function store(ProductRequest $request)
    {
        DB::transaction(function () use ($request) {
            $data = $request->validated();

            // Xử lý upload ảnh
            $images = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('products', 'public');
                    $images[] = Storage::url($path);
                }
            }
            $data['images'] = $images;
            $data['slug']   = Str::slug($data['name']);

            $product = Product::create($data);

            // Lưu thuộc tính
            $this->syncAttributes($product, $request->input('attributes', []));
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Thêm sản phẩm thành công!');
    }

    // ─── Form chỉnh sửa ──────────────────────────────────────────

    public function edit(Product $product)
    {
        $categories    = Category::categories()->active()->get();
        $brands        = Category::brands()->active()->get();
        $attributes    = Attribute::orderBy('name')->get();
        $productAttrs  = $product->attributes->keyBy('attribute_id');

        return view('admin.products.edit', compact(
            'product', 'categories', 'brands', 'attributes', 'productAttrs'
        ));
    }

    // ─── Cập nhật sản phẩm ───────────────────────────────────────

    public function update(ProductRequest $request, Product $product)
    {
        DB::transaction(function () use ($request, $product) {
            $data = $request->validated();

            // Xử lý upload ảnh mới (nếu có)
            if ($request->hasFile('images')) {
                // Xóa ảnh cũ
                foreach ($product->images ?? [] as $old) {
                    $path = str_replace('/storage/', '', $old);
                    Storage::disk('public')->delete($path);
                }
                $images = [];
                foreach ($request->file('images') as $file) {
                    $path = $file->store('products', 'public');
                    $images[] = Storage::url($path);
                }
                $data['images'] = $images;
            } else {
                unset($data['images']); // giữ nguyên ảnh cũ
            }

            $data['slug'] = Str::slug($data['name']);
            $product->update($data);

            // Đồng bộ thuộc tính
            $this->syncAttributes($product, $request->input('attributes', []));
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Cập nhật sản phẩm thành công!');
    }

    // ─── Xóa sản phẩm ────────────────────────────────────────────

    public function destroy(Product $product)
    {
        // Xóa ảnh trên storage
        foreach ($product->images ?? [] as $img) {
            $path = str_replace('/storage/', '', $img);
            Storage::disk('public')->delete($path);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Đã xóa sản phẩm.');
    }

    // ─── Bật / tắt trạng thái hiển thị (AJAX) ───────────────────

    public function toggleActive(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);

        return response()->json([
            'is_active' => $product->is_active,
            'message'   => $product->is_active ? 'Đã hiển thị' : 'Đã ẩn',
        ]);
    }

    // ─── Helper: đồng bộ thuộc tính ─────────────────────────────

    private function syncAttributes(Product $product, array $attrs): void
    {
        // Xóa thuộc tính cũ rồi tạo lại
        $product->attributes()->delete();

        foreach ($attrs as $attrId => $value) {
            if ($value === null || $value === '') continue;
            ProductAttribute::create([
                'product_id'   => $product->id,
                'attribute_id' => $attrId,
                'value'        => $value,
            ]);
        }
    }
}
