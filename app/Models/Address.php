<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'full_name',
        'phone',
        'province',
        'district',
        'ward',
        'street',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Địa chỉ đầy đủ dạng chuỗi: "123 Đường ABC, Phường X, Quận Y, Tỉnh Z"
     */
    public function getFullAddressAttribute(): string
    {
        return implode(', ', array_filter([$this->street, $this->ward, $this->district, $this->province]));
    }
}
