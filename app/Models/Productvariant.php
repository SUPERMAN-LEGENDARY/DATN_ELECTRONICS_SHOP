<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    public $timestamps = false;

    protected $table = 'product_variants';

    protected $fillable = [
        'product_id', 'label', 'price', 'discount_percent',
        'stock', 'is_active', 'sort_order',
        'thumbnail', 'images',
    ];

    protected $casts = [
        'price'            => 'decimal:0',
        'discount_percent' => 'integer',
        'stock'            => 'integer',
        'is_active'        => 'boolean',
        'images'           => 'array',
    ];

    // ── Relations ──────────────────────────────────────────────────

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variantAttributes()
    {
        return $this->hasMany(ProductVariantAttribute::class, 'variant_id');
    }

    // ── Helpers ────────────────────────────────────────────────────

    /** Giá sau khi giảm */
    public function getFinalPriceAttribute(): float
    {
        return $this->price * (1 - $this->discount_percent / 100);
    }

    /** Ảnh đại diện của biến thể — nếu biến thể không có ảnh riêng thì lấy ảnh của sản phẩm gốc */
    public function getDisplayThumbnailAttribute(): ?string
    {
        return $this->thumbnail ?? $this->product?->thumbnail;
    }

    /** Album ảnh của biến thể — nếu biến thể không có ảnh riêng thì lấy album của sản phẩm gốc */
    public function getDisplayImagesAttribute(): array
    {
        if (!empty($this->images)) {
            return $this->images;
        }
        return $this->product?->images ?? [];
    }
}