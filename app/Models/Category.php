<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;

    protected $fillable = ['name', 'slug', 'type', 'logo', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    /** URL đầy đủ của logo (tự nối storage nếu chỉ lưu path tương đối) */
    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) {
            return null;
        }

        return str_starts_with($this->logo, 'http://') || str_starts_with($this->logo, 'https://')
            ? $this->logo
            : asset('storage/' . $this->logo);
    }

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