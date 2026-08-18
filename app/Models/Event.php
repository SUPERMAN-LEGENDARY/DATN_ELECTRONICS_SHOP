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
