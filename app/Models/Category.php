<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['name', 'slug', 'type', 'logo', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    // ─── Scopes ───────────────────────────────────────────────────

    public function scopeCategories($query)
    {
        return $query->where('type', 'category');
    }

    public function scopeBrands($query)
    {
        return $query->where('type', 'brand');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ─── Relationships ────────────────────────────────────────────

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function brandProducts()
    {
        return $this->hasMany(Product::class, 'brand_id');
    }
}
