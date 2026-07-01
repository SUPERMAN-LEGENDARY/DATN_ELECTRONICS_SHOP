<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductAttribute;
use App\Models\Review;
use App\Services\AiSearchParser;
use App\Services\BadWordDetector;
use App\Services\BehaviorLogger;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // ─── Danh sách sản phẩm (có filter, sort, search, phân trang) ─

    public function index(Request $request, AiSearchParser $aiSearch)
    {
        // ── AI phân tích thanh tìm kiếm: tách từ khóa / thuộc tính / giá / hãng / danh mục ──
        $ai = $request->filled('q') ? $aiSearch->parse($request->q) : [];

        $keywords   = $ai['keywords'] ?? $request->q;
        $priceFrom  = $request->price_from ?: ($ai['price_min'] ?? null);
        $priceTo    = $request->price_to ?: ($ai['price_max'] ?? null);
        $sort       = $request->sort ?: ($ai['sort'] ?? null);
        $attributes = $ai['attributes'] ?? [];

        // Dropdown thủ công gửi sẵn ID; AI chỉ trả về slug nên cần resolve sang ID khi dropdown trống
        $brandId = $request->brand
            ?: optional(Category::where('slug', $ai['brand'] ?? null)->first())->id;
        $categoryId = $request->category
            ?: optional(Category::where('slug', $ai['category'] ?? null)->first())->id;

        // Bộ lọc thuộc tính do user chọn thủ công từ sidebar
        // (vd: attr[3][]=8GB&attr[3][]=16GB) -> ưu tiên hơn kết quả AI phân tích
        $requestAttributes = $request->input('attr', []);
        if (!empty($requestAttributes)) {
            $attributes = $requestAttributes;
        }

        $query = Product::with(['category', 'brand'])
            ->withCount(['visibleReviews as reviews_count'])
            ->active()
            ->search($keywords)
            ->filterBrand($brandId)
            ->filterCategory($categoryId)
            ->filterPrice($priceFrom, $priceTo)
            ->filterAttributes($attributes)
            ->sorted($sort);

        // Bộ lọc AI quá chặt (thuộc tính/hãng/danh mục) mà không ra kết quả -> nới lỏng,
        // chỉ giữ từ khóa gốc + khoảng giá để không trả về trang trống.
        // Chỉ áp dụng nới lỏng khi thuộc tính đến từ AI (không nới lỏng khi user tự chọn checkbox).
        if (!empty($ai['attributes']) && empty($requestAttributes) && (clone $query)->count() === 0) {
            $query = Product::with(['category', 'brand'])
                ->withCount(['visibleReviews as reviews_count'])
                ->active()
                ->search($request->q)
                ->filterPrice($priceFrom, $priceTo)
                ->sorted($sort);
        }

        // Lọc theo khoảng giá dạng checkbox (vd: 0_5000000)
        if ($request->filled('price') && is_array($request->price)) {
            $query->where(function ($q) use ($request) {
                foreach ($request->price as $range) {
                    [$from, $to] = explode('_', $range . '_');
                    $q->orWhere(function ($sub) use ($from, $to) {
                        if ($from !== '') $sub->where('price', '>=', $from);
                        if ($to   !== '') $sub->where('price', '<=', $to);
                    });
                }
            });
        }

        $totalProducts = (clone $query)->count();
        $products      = $query->paginate(24)->withQueryString();

        $categories = Category::categories()->active()->get();
        $brands     = Category::brands()->active()->get();

        // ── Danh sách thuộc tính kỹ thuật để hiển thị bộ lọc sidebar ──
        $attributesFilter = $this->getAttributesFilter($categoryId, $brandId);

        return view('products.index', compact(
            'products', 'categories', 'brands', 'totalProducts', 'attributesFilter'
        ));
    }

    /**
     * Lấy danh sách thuộc tính + các giá trị khả dụng để hiển thị bộ lọc sidebar.
     * Giới hạn theo danh mục/hãng đang được lọc (nếu có) để tránh hiện thuộc tính
     * không liên quan (vd: "Dung lượng pin" cho sản phẩm thời trang).
     *
     * @return array<int, array{id:int, name:string, values:array<int,string>}>
     */
    private function getAttributesFilter($categoryId = null, $brandId = null): array
    {
        $productIdsQuery = Product::query()->active();

        if ($categoryId) {
            $productIdsQuery->where('category_id', $categoryId);
        }
        if ($brandId) {
            $productIdsQuery->where('brand_id', $brandId);
        }

        $productIds = $productIdsQuery->pluck('id');

        if ($productIds->isEmpty()) {
            return [];
        }

        return ProductAttribute::query()
            ->whereIn('product_id', $productIds)
            ->whereNotNull('value')
            ->where('value', '!=', '')
            ->with('attribute')
            ->get()
            ->filter(fn ($pa) => $pa->attribute !== null)
            ->groupBy('attribute_id')
            ->map(function ($group) {
                $attribute = $group->first()->attribute;
                return [
                    'id'     => $attribute->id,
                    'name'   => $attribute->name,
                    'values' => $group->pluck('value')->unique()->sort()->values()->all(),
                ];
            })
            ->sortBy('name')
            ->values()
            ->all();
    }

    // ─── Chi tiết sản phẩm ────────────────────────────────────────

    public function show(Request $request, string $slug)
    {
        $product = Product::with([
                'category',
                'brand',
                'attributes.attribute',
                'variants.variantAttributes.attribute',
            ])
            ->withCount(['visibleReviews as reviews_count'])
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

        // ── Ghi log hành vi: khách đang xem sản phẩm này ─────────
        BehaviorLogger::log($product->id, 'view');

        // ── Nếu khách đến từ 1 link gợi ý (related/AI suggestion) ──
        // Link gợi ý cần thêm ?from=suggestion&via=<nguồn> vào URL, vd:
        // route('products.show', ['slug' => $p->slug, 'from' => 'suggestion', 'via' => 'related'])
        if ($request->query('from') === 'suggestion') {
            BehaviorLogger::log(
                $product->id,
                'click_suggestion',
                $request->query('via', 'related') // related | homepage | chatbot...
            );
        }

        // Đánh giá phân trang (5 per page)
        $reviews = Review::with('user')
            ->where('product_id', $product->id)
            ->visible()
            ->latest()
            ->paginate(5, ['*'], 'review_page');

        // Phân bố rating 1-5
        $ratingDistribution = Review::where('product_id', $product->id)
            ->visible()
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating');

        // Sản phẩm liên quan (cùng category, khác bản thân)
        $relatedProducts = Product::with('brand')
            ->active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->limit(5)
            ->get();

        return view('products.show', compact(
            'product', 'reviews', 'ratingDistribution', 'relatedProducts'
        ));
    }

    // ─── Gửi đánh giá ────────────────────────────────────────────

    public function storeReview(Request $request, int $productId)
    {
        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'content' => 'nullable|string|max:1000',
        ]);

        /** @var int $userId */
        $userId = auth()->id();

        // Mỗi user chỉ review 1 lần
        $exists = Review::where('product_id', $productId)
            ->where('user_id', $userId)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Bạn đã đánh giá sản phẩm này rồi.');
        }

        // ── Kiểm tra từ không chuẩn mực ──────────────────────────
        $badWordResult = BadWordDetector::check($request->content);
        $hasBadWords   = $badWordResult['found'];

        Review::create([
            'product_id'     => $productId,
            'user_id'        => $userId,
            'rating'         => $request->rating,
            'content'        => $request->content,
            'is_visible'     => !$hasBadWords,   // tự động ẩn nếu có từ xấu
            'bad_words_flag' => $hasBadWords,
        ]);

        if ($hasBadWords) {
            return back()->with('error', 'Đánh giá của bạn chứa từ ngữ không phù hợp và đang chờ kiểm duyệt.');
        }

        return back()->with('success', 'Cảm ơn bạn đã đánh giá!');
    }
}