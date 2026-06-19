<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'addresses';
    
    // Không dùng cột updated_at, created_at (bảng addresses không có các cột này)
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'full_name',
        'phone',
        'province',
        'district',
        'ward',
        'street',
        'is_default'
    ];

    // Quan hệ với User (1 địa chỉ thuộc 1 user)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ với Order (1 địa chỉ có nhiều đơn hàng)
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}