<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\Product;
use App\Models\Review;

class CompareController extends Controller
{
    /**
     * Trang so sánh — hệ thống tính điểm đa tiêu chí (AI gợi ý)
     *
     * Điểm tổng = Thông số (40%) + Review (25%) + Giá (20%) + Phổ biến (15%)
     */
    public function index()
    {
        $ids = session('compare', []);

        $products = Product::with(['attributes.attribute', 'category', 'brand', 'reviews'])
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(function ($product) use ($ids) {
                return array_search($product->id, $ids);
            });

        $attributes = Attribute::orderBy('id')->get();

        // ── Tính điểm đa tiêu chí khi có >= 2 sản phẩm ──────────────
        if ($products->count() >= 2) {
            $this->calculateAiScores($products, $attributes);
        } else {
            // Chỉ có 1 sản phẩm → gán mặc định
            $products->each(function ($p) {
                $avgRating = $p->reviews->avg('rating') ?? 0;
                $p->ai_score = $p->reviews->count() > 0 ? round($avgRating * 20) : 75;
                $p->ai_badge = 'good';
                $p->ai_badge_label = 'Phù hợp';
                $p->ai_reasons = [];
                $p->ai_spec_score = 50;
                $p->ai_review_score = $p->ai_score;
                $p->ai_price_score = 50;
                $p->ai_popularity_score = 50;
            });
        }

        return view('compare.index', [
            'products'   => $products,
            'attributes' => $attributes,
        ]);
    }

    /**
     * Tính điểm AI đa tiêu chí cho từng sản phẩm trong danh sách so sánh.
     */
    private function calculateAiScores($products, $attributes)
    {
        $count = $products->count();

        // ── 1. ĐIỂM THÔNG SỐ KỸ THUẬT (specScore) ───────────────
        // Đếm số thông số mà sản phẩm "thắng" (giá trị số lớn nhất, không hòa)
        $specWins = [];
        $specTotal = 0; // tổng số thông số có thể so sánh

        foreach ($products as $p) {
            $specWins[$p->id] = 0;
        }

        foreach ($attributes as $attribute) {
            $numericValues = [];

            foreach ($products as $p) {
                $rawValue = optional(
                    $p->attributes->where('attribute_id', $attribute->id)->first()
                )->value;

                if ($rawValue !== null) {
                    // Chuẩn hóa TB -> GB
                    $normalized = preg_replace_callback(
                        '/([\d.,]+)\s*TB/i',
                        fn($m) => ((float) str_replace(',', '.', $m[1]) * 1024) . 'GB',
                        $rawValue
                    );

                    preg_match_all('/[\d]+(?:[.,]\d+)?/', str_replace(',', '.', $normalized), $matches);

                    if (!empty($matches[0])) {
                        $numbers = array_map(fn($n) => (float) str_replace(',', '.', $n), $matches[0]);
                        $numericValues[$p->id] = max($numbers);
                    }
                }
            }

            if (count($numericValues) >= 2) {
                $specTotal++;
                $maxVal = max($numericValues);
                $winners = array_filter($numericValues, fn($v) => $v == $maxVal);

                if (count($winners) === 1) {
                    $winnerId = array_key_first($winners);
                    $specWins[$winnerId]++;
                }
            }
        }

        // ── 2. ĐIỂM REVIEW (reviewScore) ─────────────────────────
        $reviewData = [];
        foreach ($products as $p) {
            $avg = $p->reviews->avg('rating') ?? 0;
            $cnt = $p->reviews->count();
            // Bonus nhẹ cho sản phẩm có nhiều review (log scale, tối đa +10%)
            $bonus = $cnt > 0 ? min(10, log($cnt, 2) * 2) : 0;
            $reviewData[$p->id] = [
                'avg'   => $avg,
                'count' => $cnt,
                'score' => min(100, ($avg / 5) * 100 + $bonus),
            ];
        }

        // ── 3. ĐIỂM GIÁ (priceScore) ────────────────────────────
        // Giá thấp hơn → điểm cao hơn (normalize min-max)
        $prices = [];
        foreach ($products as $p) {
            $prices[$p->id] = $p->min_price;
        }
        $minPrice = min($prices);
        $maxPrice = max($prices);
        $priceRange = $maxPrice - $minPrice;

        $priceScores = [];
        foreach ($prices as $id => $price) {
            // Nếu giá bằng nhau → tất cả 100 điểm
            $priceScores[$id] = $priceRange > 0
                ? round((1 - ($price - $minPrice) / $priceRange) * 100)
                : 100;
        }

        // ── 4. ĐIỂM PHỔ BIẾN (popularityScore) ──────────────────
        $popData = [];
        foreach ($products as $p) {
            $popData[$p->id] = $p->reviews->count(); // dùng số review làm proxy
        }
        $maxPop = max($popData) ?: 1;
        $popScores = [];
        foreach ($popData as $id => $pop) {
            $popScores[$id] = round(($pop / $maxPop) * 100);
        }

        // ── TỔNG HỢP ĐIỂM ───────────────────────────────────────
        $weights = [
            'spec'       => 0.40,
            'review'     => 0.25,
            'price'      => 0.20,
            'popularity' => 0.15,
        ];

        foreach ($products as $p) {
            $specScore = $specTotal > 0
                ? round(($specWins[$p->id] / $specTotal) * 100)
                : 50;
            $revScore  = round($reviewData[$p->id]['score']);
            $priScore  = $priceScores[$p->id];
            $popScore  = $popScores[$p->id];

            $total = round(
                $specScore * $weights['spec']
                + $revScore * $weights['review']
                + $priScore * $weights['price']
                + $popScore * $weights['popularity']
            );

            // Clamp 0-100
            $total = max(0, min(100, $total));

            $p->ai_score           = $total;
            $p->ai_spec_score      = $specScore;
            $p->ai_review_score    = $revScore;
            $p->ai_price_score     = $priScore;
            $p->ai_popularity_score = $popScore;
        }

        // ── XÁC ĐỊNH BADGE ──────────────────────────────────────
        $maxScore = $products->max('ai_score');
        $bestPriceId = collect($priceScores)->sortDesc()->keys()->first();

        foreach ($products as $p) {
            if ($p->ai_score == $maxScore) {
                $p->ai_badge       = 'best';
                $p->ai_badge_label = 'Đề xuất tốt nhất';
            } elseif ($p->id == $bestPriceId && $p->ai_price_score >= 70) {
                $p->ai_badge       = 'value';
                $p->ai_badge_label = 'Giá trị tốt nhất';
            } else {
                $p->ai_badge       = 'good';
                $p->ai_badge_label = 'Phù hợp';
            }
        }

        // Nếu sản phẩm "best" cũng là sản phẩm giá rẻ nhất → sản phẩm có
        // ai_score cao thứ 2 sẽ nhận "value" (nếu phù hợp)
        $bestProduct = $products->firstWhere('ai_badge', 'best');
        if ($bestProduct && $bestProduct->id == $bestPriceId) {
            $secondBest = $products
                ->where('ai_badge', '!=', 'best')
                ->sortByDesc('ai_price_score')
                ->first();
            if ($secondBest && $secondBest->ai_price_score >= 50) {
                $secondBest->ai_badge       = 'value';
                $secondBest->ai_badge_label = 'Giá trị tốt nhất';
            }
        }

        // ── SINH LÝ DO GỢI Ý ĐỘNG ──────────────────────────────
        foreach ($products as $p) {
            $reasons = [];

            // Lý do thông số
            if ($specWins[$p->id] > 0) {
                $winCount = $specWins[$p->id];
                $reasons[] = [
                    'icon'  => 'fas fa-microchip',
                    'text'  => "Vượt trội {$winCount}/{$specTotal} thông số kỹ thuật",
                    'type'  => 'spec',
                ];
            }

            // Lý do review
            if ($reviewData[$p->id]['count'] > 0) {
                $avg = round($reviewData[$p->id]['avg'], 1);
                $cnt = $reviewData[$p->id]['count'];
                $reasons[] = [
                    'icon'  => 'fas fa-star',
                    'text'  => "{$avg}/5 sao từ {$cnt} đánh giá",
                    'type'  => 'review',
                ];
            }

            // Lý do giá
            if ($priceRange > 0 && $p->ai_price_score >= 60) {
                $savingPercent = round(($maxPrice - $prices[$p->id]) / $maxPrice * 100);
                if ($savingPercent > 0) {
                    $reasons[] = [
                        'icon'  => 'fas fa-tags',
                        'text'  => "Giá tốt hơn {$savingPercent}% so với SP đắt nhất",
                        'type'  => 'price',
                    ];
                }
            }

            // Lý do phổ biến
            if ($popData[$p->id] > 0 && $popScores[$p->id] >= 70) {
                $reasons[] = [
                    'icon'  => 'fas fa-fire',
                    'text'  => "Được nhiều người quan tâm ({$popData[$p->id]} đánh giá)",
                    'type'  => 'popularity',
                ];
            }

            // Nếu không có lý do nào → fallback từ attributes thật
            if (empty($reasons)) {
                foreach ($p->attributes->take(2) as $attr) {
                    $reasons[] = [
                        'icon'  => 'fas fa-circle-check',
                        'text'  => ($attr->attribute->name ?? '') . ': ' . $attr->value,
                        'type'  => 'attribute',
                    ];
                }
            }

            $p->ai_reasons = array_slice($reasons, 0, 4);
        }
    }

    /**
     * Thêm sản phẩm vào so sánh
     */
    public function add(Product $product)
    {
        $compare = session()->get('compare', []);

        if (!in_array($product->id, $compare)) {
            $compare[] = $product->id;
        }

        // Chỉ giữ tối đa 3 sản phẩm
        if (count($compare) > 3) {
            array_shift($compare);
        }

        session()->put('compare', $compare);

        return redirect()
            ->route('compare')
            ->with('success', 'Đã thêm sản phẩm vào danh sách so sánh.');
    }

    /**
     * Xóa sản phẩm khỏi so sánh
     */
    public function remove(Product $product)
    {
        $compare = session()->get('compare', []);

        $compare = array_values(array_filter($compare, function ($id) use ($product) {
            return $id != $product->id;
        }));

        session()->put('compare', $compare);

        return redirect()
            ->route('compare')
            ->with('success', 'Đã xóa sản phẩm khỏi danh sách so sánh.');
    }
}