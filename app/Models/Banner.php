<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'layout',
        'label',
        'title',
        'description',
        'price_text',
        'button_text',
        'button_link',
        'image',
        'bg_color',
        'text_color',
        'sort_order',
        'is_active',
        // New fields
        'creation_method',
        'banner_type',
        'template',
        'image_mobile',
        'btn_color',
        'fx_shadow',
        'fx_gradient',
        'fx_radius',
        'text_align',
        'price',
        'compare_price',
        'media_type',
        'video',
        'start_at',
        'end_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'fx_shadow' => 'boolean',
            'fx_gradient' => 'boolean',
            'fx_radius' => 'boolean',
            'start_at' => 'datetime',
            'end_at' => 'datetime',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderByDesc('id');
    }

    public function isImageOnly(): bool
    {
        return $this->creation_method === 'upload';
    }
}
