<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use SoftDeletes;

    protected $table = 'vouchers';

    public $timestamps = false;

    protected $casts = [
        'starts_at'  => 'datetime',
        'expires_at' => 'datetime',
        'is_active'  => 'boolean',
    ];

    protected $fillable = [
        'code',
        'discount_percent',
        'min_order_value',
        'usage_limit',
        'used_count',
        'starts_at',
        'expires_at',
        'is_active'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
