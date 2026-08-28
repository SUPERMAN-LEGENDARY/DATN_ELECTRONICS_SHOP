<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'tag',
        'offer_text',
        'description',
        'image',
        'button_text',
        'button_link',
        'bg_color',
        'text_color',
        'start_date',
        'end_date',
        'sort_order',
        'is_active',
        'apply_scope',
        'discount_type',
        'discount_value',
        'max_discount',
        'voucher_id',
    ];

    protected function casts(): array
    {
        return [
            'is_active'  => 'boolean',
            'start_date' => 'date',
            'end_date'   => 'date',
            'discount_value' => 'decimal:2',
            'max_discount'   => 'decimal:2',
        ];
    }

    public function getThemeEffectAttribute(): string
    {
        $titleLower = mb_strtolower($this->title, 'UTF-8');
        $tagLower = mb_strtolower($this->tag ?? '', 'UTF-8');
        
        if (str_contains($titleLower, 'giáng sinh') || str_contains($titleLower, 'noel') || str_contains($tagLower, 'giáng sinh') || str_contains($tagLower, 'noel')) {
            return 'christmas';
        } elseif (str_contains($titleLower, 'tết') || str_contains($titleLower, 'lì xì') || str_contains($titleLower, 'năm mới') || str_contains($tagLower, 'tết') || str_contains($tagLower, 'năm mới')) {
            return 'tet';
        } elseif (str_contains($titleLower, 'quốc tế phụ nữ') || str_contains($titleLower, '8/3') || str_contains($tagLower, 'phụ nữ') || str_contains($tagLower, '8/3')) {
            return 'womens_day';
        } elseif (str_contains($titleLower, 'hè') || str_contains($titleLower, 'summer') || str_contains($tagLower, 'mùa hè') || str_contains($tagLower, 'summer')) {
            return 'summer';
        }

        return 'none';
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'event_product');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'event_category');
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    // Chỉ lấy event đang được bật hiển thị
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Sắp xếp theo thứ tự ưu tiên rồi mới nhất
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderByDesc('id');
    }

    // Đang trong thời gian diễn ra (nếu có set ngày bắt đầu/kết thúc)
    public function scopeOngoing($query)
    {
        $today = now()->toDateString();

        return $query->where(function ($q) use ($today) {
            $q->whereNull('start_date')->orWhereDate('start_date', '<=', $today);
        })->where(function ($q) use ($today) {
            $q->whereNull('end_date')->orWhereDate('end_date', '>=', $today);
        });
    }

    // Trạng thái hiển thị dạng chữ cho admin
    public function statusLabel(): string
    {
        if (! $this->is_active) {
            return 'Đã ẩn';
        }

        $today = now()->toDateString();

        if ($this->start_date && $this->start_date->toDateString() > $today) {
            return 'Chưa bắt đầu';
        }

        if ($this->end_date && $this->end_date->toDateString() < $today) {
            return 'Đã kết thúc';
        }

        return 'Đang diễn ra';
    }
}
