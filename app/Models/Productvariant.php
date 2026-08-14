<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class ProductVariant extends Model
{
    public $timestamps = false;

    protected $table = 'product_variants';

    protected $fillable = [
        'product_id', 'label', 'cost_price', 'list_price', 'price',
        'stock', 'is_active', 'sort_order',
        'thumbnail', 'images',
    ];

    protected $casts = [
        'cost_price' => 'decimal:0',
        'list_price' => 'decimal:0',
        'price'      => 'decimal:0',
        'stock'      => 'integer',
        'is_active'  => 'boolean',
        'images'     => 'array',
    ];

    /**
     * Chặn lưu nếu vi phạm quy tắc giá của biến thể.
     * Đây là lớp bảo vệ cuối cùng ở tầng model, phòng trường hợp
     * biến thể được tạo/sửa ở nơi khác ngoài Admin\ProductController.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (ProductVariant $variant) {
            $variant->assertPriceRulesValid();

            $productPrice = $variant->product?->price
                ?? \App\Models\Product::whereKey($variant->product_id)->value('price');

            if ($productPrice !== null && (float) $variant->price < (float) $productPrice) {
                throw ValidationException::withMessages([
                    'price' => 'Giá bán biến thể không được nhỏ hơn giá bán sản phẩm ('
                        . number_format((float) $productPrice) . 'đ).',
                ]);
            }
        });
    }

    /** Giá vốn > 0, Giá niêm yết >= Giá vốn, Giá bán >= Giá vốn, Giá bán <= Giá niêm yết */
    public function assertPriceRulesValid(): void
    {
        $cost = (float) $this->cost_price;
        $list = (float) $this->list_price;
        $sale = (float) $this->price;

        $errors = [];

        if ($cost <= 0) {
            $errors['cost_price'] = 'Giá vốn biến thể phải lớn hơn 0.';
        }
        if ($list < $cost) {
            $errors['list_price'] = 'Giá niêm yết biến thể không được nhỏ hơn giá vốn.';
        }
        if ($sale < $cost) {
            $errors['price'] = 'Giá bán biến thể không được nhỏ hơn giá vốn.';
        }
        if ($sale > $list) {
            $errors['price'] = 'Giá bán biến thể không được lớn hơn giá niêm yết.';
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

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

    /** Giá bán thực tế của biến thể */
    public function getFinalPriceAttribute(): float
    {
        return (float) $this->price;
    }

    /** % giảm giá so với giá niêm yết của biến thể (làm tròn, 0 nếu không giảm) */
    public function getDiscountPercentAttribute(): int
    {
        $list  = (float) $this->list_price;
        $price = (float) $this->price;

        if ($list <= 0 || $price >= $list) {
            return 0;
        }

        return (int) round((1 - $price / $list) * 100);
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