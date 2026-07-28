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
        $product = Product::onlyTrashed()->with('variants')->findOrFail($id);

        // Xóa ảnh khỏi storage
        foreach ($product->images ?? [] as $img) {
            $path = str_replace('/storage/', '', $img);
            Storage::disk('public')->delete($path);
        }
        if ($product->thumbnail) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $product->thumbnail));
        }

        // Xóa ảnh riêng của từng biến thể
        foreach ($product->variants as $variant) {
            $this->deleteVariantImages($variant);
        }

        $product->forceDelete();

        return redirect()->route('admin.products.trash')
            ->with('success', 'Đã xóa vĩnh viễn sản phẩm.');
    }

    // ─── Xóa vĩnh viễn tất cả trong thùng rác ───────────────────

    public function emptyTrash()
    {
        $trashed = Product::onlyTrashed()->with('variants')->get();

        foreach ($trashed as $product) {
            foreach ($product->images ?? [] as $img) {
                $path = str_replace('/storage/', '', $img);
                Storage::disk('public')->delete($path);
            }
            if ($product->thumbnail) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $product->thumbnail));
            }
            foreach ($product->variants as $variant) {
                $this->deleteVariantImages($variant);
            }
            $product->forceDelete();
        }

        return redirect()->route('admin.products.trash')
            ->with('success', 'Đã dọn sạch thùng rác.');
    }

    // ─── Kiểm tra trùng tên sản phẩm (AJAX, gọi khi người dùng gõ tên) ─

    public function checkName(Request $request)
    {
        $name = trim((string) $request->query('name'));
        $ignoreId = $request->query('ignore_id');

        if ($name === '') {
            return response()->json(['exists' => false]);
        }

        $exists = Product::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists();

        return response()->json(['exists' => $exists]);
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
        // ── Cảnh báo trùng tên sản phẩm (không phân biệt hoa/thường) ──
        $this->assertNameNotDuplicate($request->input('name'));

        // ── Cảnh báo quy tắc giá: Giá vốn > 0, Giá niêm yết >= Giá vốn,
        //    Giá bán >= Giá vốn, Giá bán <= Giá niêm yết ──
        $this->assertProductPriceRules(
            $request->input('cost_price'),
            $request->input('list_price'),
            $request->input('price')
        );

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
            $this->syncVariants($request, $product, $request->input('variants', []), $request->boolean('has_variants'));
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
        // ── Cảnh báo trùng tên sản phẩm (bỏ qua chính sản phẩm đang sửa) ──
        $this->assertNameNotDuplicate($request->input('name'), $product->id);

        // ── Cảnh báo quy tắc giá: Giá vốn > 0, Giá niêm yết >= Giá vốn,
        //    Giá bán >= Giá vốn, Giá bán <= Giá niêm yết ──
        $this->assertProductPriceRules(
            $request->input('cost_price'),
            $request->input('list_price'),
            $request->input('price')
        );

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

            $data['slug'] = Str::slug($data['name']);
            unset($data['has_variants'], $data['variants'], $data['attributes']);
            $product->update($data);

            $this->syncAttributes($product, $request->input('attributes', []));
            $this->syncVariants($request, $product, $request->input('variants', []), $request->boolean('has_variants'));
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

    // ─── Helper: đồng bộ biến thể (+ ảnh riêng của từng biến thể) ─

    private function syncVariants(Request $request, Product $product, array $variants, bool $hasVariants): void
    {
        // Nếu tắt biến thể → xóa tất cả (kèm ảnh riêng của chúng)
        if (!$hasVariants || empty($variants)) {
            foreach ($product->variants as $v) {
                $this->deleteVariantImages($v);
                $v->variantAttributes()->delete();
                $v->delete();
            }
            return;
        }

        $submittedIds = [];

        foreach ($variants as $sortOrder => $vData) {
            $variantId = isset($vData['id']) && $vData['id'] ? (int) $vData['id'] : null;

            // Tạo label tự động từ giá trị các thuộc tính
            $attrs = $vData['attrs'] ?? [];
            $labelParts = array_filter(array_values($attrs), fn($v) => $v !== null && $v !== '');
            $label = implode(' / ', $labelParts) ?: ('Biến thể ' . ($sortOrder + 1));

            // ── Lấy giá biến thể từ form ──
            $variantCost  = (float) ($vData['cost_price'] ?? 0);
            $variantList  = (float) ($vData['list_price'] ?? 0);
            $variantPrice = (float) ($vData['price'] ?? 0);

            // ── Quy tắc giá của chính biến thể: Giá vốn > 0, Giá niêm yết >= Giá vốn,
            //    Giá bán >= Giá vốn, Giá bán <= Giá niêm yết ──
            $this->assertVariantPriceRules($label, $sortOrder, $variantCost, $variantList, $variantPrice);

            // ── Giá bán biến thể không được nhỏ hơn giá bán của sản phẩm ──
            if ($variantPrice < (float) $product->price) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "variants.$sortOrder.price" => "Giá bán biến thể \"{$label}\" ({$this->formatVnd($variantPrice)}) không được nhỏ hơn giá bán sản phẩm ({$this->formatVnd($product->price)}).",
                ]);
            }

            $variantData = [
                'label'      => $label,
                'cost_price' => $variantCost,
                'list_price' => $variantList,
                'price'      => $variantPrice,
                'stock'      => $vData['stock'] ?? 0,
                'is_active'  => isset($vData['is_active']) ? (bool) $vData['is_active'] : true,
                'sort_order' => $sortOrder,
            ];

            // ── Tìm variant đúng của SẢN PHẨM NÀY qua quan hệ Eloquent
            //    (thay vì so sánh product_id thủ công — dễ lệch kiểu int/string
            //    và từng khiến variant cũ bị coi là "không hợp lệ" rồi bị xóa
            //    mất thuộc tính). whereKey() tự động scope theo product_id. ──
            $variant = $variantId
                ? $product->variants()->whereKey($variantId)->first()
                : null;

            if ($variant) {
                $variant->update($variantData);
            } else {
                $variant = $product->variants()->create($variantData);
            }

            // ── Ảnh riêng của biến thể (đại diện + album) ──
            $thumbKey  = "variants.$sortOrder.thumbnail";
            $imagesKey = "variants.$sortOrder.images";

            if ($request->hasFile($thumbKey)) {
                if ($variant->thumbnail) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $variant->thumbnail));
                }
                $path = $request->file($thumbKey)->store('products/variants', 'public');
                $variant->thumbnail = Storage::url($path);
            }

            if ($request->hasFile($imagesKey)) {
                foreach ((array) $variant->images as $oldImg) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $oldImg));
                }
                $newImages = [];
                foreach ($request->file($imagesKey) as $file) {
                    $newImages[] = Storage::url($file->store('products/variants', 'public'));
                }
                $variant->images = $newImages;
            }

            if ($variant->isDirty()) {
                $variant->save();
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

        // Xóa các variant không còn trong form submit (đã bị remove) + ảnh của chúng
        $product->variants()
            ->whereNotIn('id', $submittedIds)
            ->get()
            ->each(function ($v) {
                $this->deleteVariantImages($v);
                $v->variantAttributes()->delete();
                $v->delete();
            });
    }

    // ─── Helper: cảnh báo trùng tên sản phẩm ─────────────────────

    private function assertNameNotDuplicate(string $name, ?int $ignoreId = null): void
    {
        $exists = Product::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower(trim($name))])
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists();

        if ($exists) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'name' => "Đã tồn tại sản phẩm khác có tên \"{$name}\". Vui lòng đặt tên khác.",
            ]);
        }
    }

    // ─── Helper: cảnh báo quy tắc giá sản phẩm ───────────────────
    //    Giá vốn > 0, Giá niêm yết >= Giá vốn, Giá bán >= Giá vốn, Giá bán <= Giá niêm yết

    private function assertProductPriceRules($cost, $list, $price): void
    {
        $cost  = (float) $cost;
        $list  = (float) $list;
        $price = (float) $price;

        if ($cost <= 0) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'cost_price' => 'Giá vốn phải lớn hơn 0.',
            ]);
        }
        if ($list < $cost) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'list_price' => 'Giá niêm yết (' . $this->formatVnd($list) . ') không được nhỏ hơn giá vốn (' . $this->formatVnd($cost) . ').',
            ]);
        }
        if ($price < $cost) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'price' => 'Giá bán (' . $this->formatVnd($price) . ') không được nhỏ hơn giá vốn (' . $this->formatVnd($cost) . ').',
            ]);
        }
        if ($price > $list) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'price' => 'Giá bán (' . $this->formatVnd($price) . ') không được lớn hơn giá niêm yết (' . $this->formatVnd($list) . ').',
            ]);
        }
    }

    // ─── Helper: cảnh báo quy tắc giá của 1 biến thể (cùng 4 quy tắc như trên) ──

    private function assertVariantPriceRules(string $label, int $sortOrder, float $cost, float $list, float $price): void
    {
        if ($cost <= 0) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                "variants.$sortOrder.cost_price" => "Giá vốn biến thể \"{$label}\" phải lớn hơn 0.",
            ]);
        }
        if ($list < $cost) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                "variants.$sortOrder.list_price" => "Giá niêm yết biến thể \"{$label}\" ({$this->formatVnd($list)}) không được nhỏ hơn giá vốn ({$this->formatVnd($cost)}).",
            ]);
        }
        if ($price < $cost) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                "variants.$sortOrder.price" => "Giá bán biến thể \"{$label}\" ({$this->formatVnd($price)}) không được nhỏ hơn giá vốn ({$this->formatVnd($cost)}).",
            ]);
        }
        if ($price > $list) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                "variants.$sortOrder.price" => "Giá bán biến thể \"{$label}\" ({$this->formatVnd($price)}) không được lớn hơn giá niêm yết ({$this->formatVnd($list)}).",
            ]);
        }
    }

    // ─── Helper: format số tiền cho thông báo lỗi ───────────────

    private function formatVnd(float $amount): string
    {
        return number_format($amount) . 'đ';
    }

    // ─── Helper: xóa ảnh riêng (đại diện + album) của 1 biến thể khỏi storage ─

    private function deleteVariantImages(ProductVariant $variant): void
    {
        if ($variant->thumbnail) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $variant->thumbnail));
        }
        foreach ($variant->images ?? [] as $img) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $img));
        }
    }
}