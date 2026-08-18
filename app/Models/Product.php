<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
use HasFactory, SoftDeletes;

    const UPDATED_AT = null;

    protected $fillable = [
        'name', 'slug', 'category_id', 'brand_id',
        'description', 'thumbnail', 'images',
        'cost_price', 'list_price', 'price',
        'stock', 'is_active',
    ];

    protected $casts = [
        'images'     => 'array',
        'cost_price' => 'decimal:0',
        'list_price' => 'decimal:0',
        'price'      => 'decimal:0',
        'is_active'  => 'boolean',
    ];

    // Tự tạo slug từ name + validate giá (Giá vốn > 0, Giá niêm yết >= Giá vốn,
    // Giá bán >= Giá vốn, Giá bán <= Giá niêm yết)
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name')) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::saving(function (Product $product) {
            $product->assertPriceRulesValid();
        });
    }

    /**
     * Lớp bảo vệ cuối cùng ở tầng model cho quy tắc giá.
     * Đây là lớp phòng thủ cuối; cảnh báo chính hiển thị ở form/controller.
     */
    public function assertPriceRulesValid(): void
    {
        $cost = (float) $this->cost_price;
        $list = (float) $this->list_price;
        $sale = (float) $this->price;

        $errors = [];

        if ($cost <= 0) {
            $errors['cost_price'] = 'Giá vốn phải lớn hơn 0.';
        }
        if ($list < $cost) {
            $errors['list_price'] = 'Giá niêm yết không được nhỏ hơn giá vốn.';
        }
        if ($sale < $cost) {
            $errors['price'] = 'Giá bán không được nhỏ hơn giá vốn.';
        }
        if ($sale > $list) {
            $errors['price'] = 'Giá bán không được lớn hơn giá niêm yết.';
        }

        if (!empty($errors)) {
            throw \Illuminate\Validation\ValidationException::withMessages($errors);
        }
    }

    // ─── Relationships ────────────────────────────────────────────

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Category::class, 'brand_id');
    }

    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function visibleReviews()
    {
        return $this->hasMany(Review::class)->where('is_visible', true);
    }

    public function wishlistUsers()
    {
        return $this->belongsToMany(User::class, 'wishlists')->withPivot('created_at');
    }

    // ─── Accessors ────────────────────────────────────────────────

    /** Giá niêm yết (dùng để hiển thị giá gạch ngang) */
    public function getOriginalPriceAttribute(): float
    {
        return (float) $this->list_price;
    }

    /** Sản phẩm có đang bán rẻ hơn giá niêm yết hay không (để hiển thị giá gạch ngang) */
    public function getIsOnSaleAttribute(): bool
    {
        return false;
    }

    protected static $activeEventsCache = null;

    public function getActiveEvent()
    {
        if (self::$activeEventsCache === null) {
            $now = now();
            self::$activeEventsCache = \App\Models\Event::with(['categories', 'products'])
                ->where('is_active', true)
                ->where('start_date', '<=', $now)
                ->where('end_date', '>=', $now)
                ->orderBy('sort_order', 'desc')
                ->get();
        }

        // Ưu tiên 1: Event chỉ định đích danh sản phẩm này
        foreach (self::$activeEventsCache as $event) {
            if ($event->apply_scope === 'select' && $event->products->contains('id', $this->id)) return $event;
        }

        // Ưu tiên 2: Event áp dụng cho danh mục / thương hiệu của sản phẩm này
        foreach (self::$activeEventsCache as $event) {
            if ($event->apply_scope === 'category' && ($event->categories->contains('id', $this->category_id) || $event->categories->contains('id', $this->brand_id))) return $event;
        }

        // Ưu tiên 3: Event áp dụng cho toàn bộ cửa hàng
        foreach (self::$activeEventsCache as $event) {
            if ($event->apply_scope === 'all') return $event;
        }

        return null;
    }

    /** % giảm giá lấy từ sự kiện đang diễn ra */
    public function getDiscountPercentAttribute(): int
    {
        $event = $this->getActiveEvent();
        if (!$event) return 0;

        $price = (float) $this->attributes['price'];
        if ($price <= 0) return 0;

        $discount = 0;
        if ($event->discount_type === 'percent') {
            $discount = $price * ((float) $event->discount_value / 100);
        } elseif ($event->discount_type === 'amount') {
            $discount = (float) $event->discount_value;
        } elseif ($event->discount_type === 'fixed') {
            $discount = $price - (float) $event->discount_value;
        }

        if ($event->max_discount > 0 && $discount > $event->max_discount) {
            $discount = (float) $event->max_discount;
        }

        if ($discount < 0) $discount = 0;
        if ($discount > $price) $discount = $price;

        return (int) round(($discount / $price) * 100);
    }

    /** Điểm đánh giá trung bình */
    public function getAvgRatingAttribute(): float
    {
        return round($this->visibleReviews()->avg('rating') ?? 0, 1);
    }

    /** Thumbnail đầu tiên (ưu tiên thumbnail, fallback ảnh album đầu) */
    public function getFirstImageAttribute(): ?string
    {
        return $this->thumbnail ?? ($this->images[0] ?? null);
    }

    /**
     * Giá thấp nhất hiện có (dùng để hiển thị cho user ngoài trang danh sách/chi tiết).
     * Nếu sản phẩm có biến thể còn hàng thì lấy giá thấp nhất trong các biến thể đó,
     * ngược lại lấy giá của chính sản phẩm.
     */
    public function getMinPriceAttribute(): float
    {
        $prices = collect([(float) $this->price]);

        foreach ($this->variants as $variant) {
            if ($variant->is_active && $variant->stock > 0) {
                $prices->push($variant->final_price);
            }
        }

        $min = (float) $prices->min();
        
        $discountPercent = $this->discount_percent;
        if ($discountPercent > 0) {
            $min = $min * (1 - $discountPercent / 100);
        }

        return $min;
    }

    /** Sản phẩm có nhiều mức giá khác nhau (do biến thể) -> nên hiển thị "Từ ..." */
    public function getHasPriceRangeAttribute(): bool
    {
        return $this->variants->isNotEmpty();
    }

    // ─── Scopes ───────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, ?string $keyword)
    {
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
            });
        }
        return $query;
    }

    public function scopeFilterBrand($query, mixed $brandId)
    {
        if ($brandId) {
            $query->where('brand_id', $brandId);
        }
        return $query;
    }

    public function scopeFilterCategory($query, mixed $categoryId)
    {
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        return $query;
    }

    public function scopeFilterPrice($query, ?string $from, ?string $to)
    {
        if ($from) $query->where('price', '>=', $from);
        if ($to)   $query->where('price', '<=', $to);
        return $query;
    }

    /**
     * Lọc theo thuộc tính kỹ thuật.
     * $attributes dạng: [attribute_id => [value1, value2, ...]]
     * Trong cùng 1 thuộc tính: OR (vd RAM = 8GB hoặc 16GB)
     * Giữa các thuộc tính khác nhau: AND (vd RAM=8GB AND Màu=Đen)
     */
    public function scopeFilterAttributes($query, ?array $attributes)
    {
        if (empty($attributes)) {
            return $query;
        }

        foreach ($attributes as $attributeId => $values) {
            $values = array_filter((array) $values, fn ($v) => $v !== null && $v !== '');
            if (empty($values)) {
                continue;
            }

            $query->whereHas('attributes', function ($q) use ($attributeId, $values) {
                $q->where('attribute_id', $attributeId)
                  ->whereIn('value', $values);
            });
        }

        return $query;
    }

    public function scopeSorted($query, ?string $sort)
    {
        return match ($sort) {
            'price_asc'   => $query->orderBy('price', 'asc'),
            'price_desc'  => $query->orderBy('price', 'desc'),
            'newest'      => $query->orderBy('created_at', 'desc'),
            'rating'      => $query->withAvg('visibleReviews', 'rating')
                                   ->orderByDesc('visible_reviews_avg_rating'),
            default       => $query->orderBy('created_at', 'desc'),
        };
    }
}