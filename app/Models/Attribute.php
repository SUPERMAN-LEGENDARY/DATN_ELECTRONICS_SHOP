<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'is_variant'];

    protected $casts = [
        'is_variant' => 'boolean',
    ];

    public function productAttributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }
}