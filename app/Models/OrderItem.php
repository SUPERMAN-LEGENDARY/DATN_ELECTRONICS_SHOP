<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'order_items';

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'product_id',
        'variant_id',
        'product_name',
        'quantity',
        'unit_price',
        'total_price'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class, 'product_id', 'product_id');
    }

    public function getVariantAttributesTextAttribute(): string
    {
        if ($this->variant) {
            return $this->variant->attributes_text;
        } elseif ($this->product && $this->product->variants->isNotEmpty()) {
            $variantAttrIds = \App\Models\ProductVariantAttribute::whereIn('variant_id', $this->product->variants->pluck('id'))->pluck('attribute_id')->unique();
            if ($variantAttrIds->isNotEmpty()) {
                return (string) \App\Models\ProductAttribute::where('product_id', $this->product->id)
                    ->whereIn('attribute_id', $variantAttrIds)
                    ->get()
                    ->sortBy('attribute_id')
                    ->pluck('value')
                    ->implode(' - ');
            }
        }
        return '';
    }
}