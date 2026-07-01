<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
        'assigned_user_id',
        'note',
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

    /** Khách hàng được tặng riêng voucher này (null nếu là voucher dùng chung) */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    /** Voucher cá nhân hoá - chỉ 1 khách cụ thể dùng được */
    public function scopePersonal(Builder $query): Builder
    {
        return $query->whereNotNull('assigned_user_id');
    }

    /** Voucher dùng chung cho mọi khách */
    public function scopeGeneral(Builder $query): Builder
    {
        return $query->whereNull('assigned_user_id');
    }
}