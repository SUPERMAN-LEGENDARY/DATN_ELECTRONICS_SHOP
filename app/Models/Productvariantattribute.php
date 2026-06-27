<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantAttribute extends Model
{
    public $timestamps = false;

    protected $table = 'product_variant_attributes';

    protected $fillable = ['variant_id', 'attribute_id', 'value'];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }
}