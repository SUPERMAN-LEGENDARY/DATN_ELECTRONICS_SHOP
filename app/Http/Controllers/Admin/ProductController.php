<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductVariant;
use App\Models\ProductVariantAttribute;
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

        $products      = $query->latest()->paginate(20)->withQueryString();
        $categories    = Category::categories()->active()->get();
        $brands        = Category::brands()->active()->get();
        $trashedCount  = Product::onlyTrashed()->count();

        return view('admin.products.index', compact('products', 'categories', 'brands', 'trashedCount'));
    }

    // ─── Thùng rác ───────────────────────────────────────────────

    public function trash(Request $request)
    {
        $query = Product::onlyTrashed()->with(['category', 'brand']);

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        $products = $query->latest('deleted_at')->paginate(20)->withQueryString();

        return view('admin.products.trash', compact('products'));
    }

    // ─── Khôi phục 1 sản phẩm ────────────────────────────────────

    public function restore(int $id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();

        return redirect()->route('admin.products.trash')
        ->with('success', "Đã khôi phục sản phẩm \"{$product->name}\".");          
    }

    // ─── Khôi phục tất cả ────────────────────────────────────────

    public function restoreAll()
    {
        $count = Product::onlyTrashed()->count();
        Product::onlyTrashed()->restore();

        return redirect()->route('admin.products.trash')
            ->with('success', "Đã khôi phục {$count} sản phẩm.");
    }

    // ─── Xóa vĩnh viễn 1 sản phẩm ───────────────────────────────

    public function forceDelete(int $id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);

        // Xóa ảnh khỏi storage
        foreach ($product->images ?? [] as $img) {
            $path = str_replace('/storage/', '', $img);
            Storage::disk('public')->delete($path);
        }

        $product->forceDelete();

        return redirect()->route('admin.products.trash')
            ->with('success', 'Đã xóa vĩnh viễn sản phẩm.');
    }

    // ─── Xóa vĩnh viễn tất cả trong thùng rác ───────────────────

    public function emptyTrash()
    {
        $trashed = Product::onlyTrashed()->get();

        foreach ($trashed as $product) {
            foreach ($product->images ?? [] as $img) {
                $path = str_replace('/storage/', '', $img);
                Storage::disk('public')->delete($path);
            }
            $product->forceDelete();
        }

        return redirect()->route('admin.products.trash')
            ->with('success', 'Đã dọn sạch thùng rác.');
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

            $images = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('products', 'public');
                    $images[] = Storage::url($path);
                }
            }
            $data['images'] = $images;

            // Xử lý ảnh đại diện
            $thumbnailUrl = null;
            if ($request->hasFile('thumbnail')) {
                $path = $request->file('thumbnail')->store('products', 'public');
                $thumbnailUrl = Storage::url($path);
            }

            $data['slug'] = Str::slug($data['name']);
            unset($data['has_variants'], $data['variants'], $data['attributes'], $data['thumbnail']);
            if ($thumbnailUrl) $data['thumbnail'] = $thumbnailUrl;

            $product = Product::create($data);

            $this->syncAttributes($product, $request->input('attributes', []));
            $this->syncVariants($product, $request->input('variants', []), $request->boolean('has_variants'));
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Thêm sản phẩm thành công!');
    }

    // ─── Form chỉnh sửa ──────────────────────────────────────────

    public function edit(Product $product)
    {
        $categories   = Category::categories()->active()->get();
        $brands       = Category::brands()->active()->get();
        $attributes   = Attribute::orderBy('name')->get();
        $productAttrs = $product->attributes->keyBy('attribute_id');

        return view('admin.products.edit', compact(
            'product', 'categories', 'brands', 'attributes', 'productAttrs'
        ));
    }

    // ─── Cập nhật sản phẩm ───────────────────────────────────────

    public function update(ProductRequest $request, Product $product)
    {
        DB::transaction(function () use ($request, $product) {
            $data = $request->validated();

            if ($request->hasFile('images')) {
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
                unset($data['images']);
            }

            // Xử lý ảnh đại diện
            if ($request->hasFile('thumbnail')) {
                if ($product->thumbnail) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $product->thumbnail));
                }
                $path = $request->file('thumbnail')->store('products', 'public');
                $data['thumbnail'] = Storage::url($path);
            } else {
                unset($data['thumbnail']); // giữ nguyên ảnh cũ
            }

            // Xử lý ảnh đại diện
            if ($request->hasFile('thumbnail')) {
                if ($product->thumbnail) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $product->thumbnail));
                }
                $path = $request->file('thumbnail')->store('products', 'public');
                $data['thumbnail'] = Storage::url($path);
            } else {
                unset($data['thumbnail']); // giữ nguyên ảnh cũ
            }

            $data['slug'] = Str::slug($data['name']);
            unset($data['has_variants'], $data['variants'], $data['attributes']);
            $product->update($data);

            $this->syncAttributes($product, $request->input('attributes', []));
            $this->syncVariants($product, $request->input('variants', []), $request->boolean('has_variants'));
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Cập nhật sản phẩm thành công!');
    }

    // ─── Xóa mềm sản phẩm ────────────────────────────────────────

    public function destroy(Product $product)
    {
        $product->delete(); // ghi deleted_at, chuyển vào thùng rác

        return redirect()->route('admin.products.index')
            ->with('success', 'Đã chuyển sản phẩm vào thùng rác.');
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

    // ─── Thêm số lượng (AJAX) ────────────────────────────────────

    public function addStock(Request $request, Product $product)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        $product->increment('stock', $request->quantity);

        return response()->json([
            'stock' => $product->fresh()->stock,
        ]);
    }

    // ─── Helper: đồng bộ thuộc tính ─────────────────────────────

    private function syncAttributes(Product $product, array $attrs): void
    {
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

    // ─── Helper: đồng bộ biến thể ────────────────────────────────

    private function syncVariants(Product $product, array $variants, bool $hasVariants): void
    {
        // Nếu tắt biến thể → xóa tất cả
        if (!$hasVariants || empty($variants)) {
            $product->variants()->each(function ($v) {
                $v->variantAttributes()->delete();
                $v->delete();
            });
            return;
        }

        $submittedIds = [];

        foreach ($variants as $sortOrder => $vData) {
            $variantId = isset($vData['id']) && $vData['id'] ? (int) $vData['id'] : null;

            // Tạo label tự động từ giá trị các thuộc tính
            $attrs = $vData['attrs'] ?? [];
            $labelParts = array_filter(array_values($attrs), fn($v) => $v !== null && $v !== '');
            $label = implode(' / ', $labelParts) ?: ('Biến thể ' . ($sortOrder + 1));

            $variantData = [
                'product_id'       => $product->id,
                'label'            => $label,
                'price'            => $vData['price'] ?? 0,
                'discount_percent' => $vData['discount_percent'] ?? 0,
                'stock'            => $vData['stock'] ?? 0,
                'is_active'        => isset($vData['is_active']) ? (bool) $vData['is_active'] : true,
                'sort_order'       => $sortOrder,
            ];

            if ($variantId) {
                // Cập nhật variant cũ
                $variant = ProductVariant::find($variantId);
                if ($variant && $variant->product_id === $product->id) {
                    $variant->update($variantData);
                } else {
                    // Không hợp lệ → tạo mới
                    $variant = ProductVariant::create($variantData);
                }
            } else {
                // Tạo variant mới
                $variant = ProductVariant::create($variantData);
            }

            $submittedIds[] = $variant->id;

            // Đồng bộ variant_attributes
            $variant->variantAttributes()->delete();
            foreach ($attrs as $attrId => $value) {
                if ($value === null || $value === '') continue;
                ProductVariantAttribute::create([
                    'variant_id'   => $variant->id,
                    'attribute_id' => $attrId,
                    'value'        => $value,
                ]);
            }
        }

        // Xóa các variant không còn trong form submit (đã bị remove)
        $product->variants()
            ->whereNotIn('id', $submittedIds)
            ->each(function ($v) {
                $v->variantAttributes()->delete();
                $v->delete();
            });
    }
}