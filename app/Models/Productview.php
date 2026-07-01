<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductView extends Model
{
    const UPDATED_AT = null; // bảng chỉ có created_at

    protected $fillable = [
        'user_id',
        'session_token',
        'product_id',
        'event_type',
        'duration_seconds',
        'source',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}