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
        'description', 'thumbnail', 'images', 'price',
        'discount_percent', 'stock', 'is_active',
    ];

    protected $casts = [
        'images'   => 'array',
        'price'    => 'decimal:0',
        'is_active' => 'boolean',
    ];

    // Tự tạo slug từ name
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

    // ─── Accessors ────────────────────────────────────────────────

    /** Giá sau khi giảm */
    public function getSalePriceAttribute(): float
    {
        return $this->price * (1 - $this->discount_percent / 100);
    }

    /** Giá gốc (trước giảm) */
    public function getOriginalPriceAttribute(): float
    {
        return (float) $this->price;
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