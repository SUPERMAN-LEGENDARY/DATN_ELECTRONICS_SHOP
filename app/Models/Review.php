<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'product_id', 'user_id', 'rating',
        'content', 'is_visible', 'admin_reply',
        'bad_words_flag',   // ← thêm mới
    ];

    protected $casts = [
        'is_visible'     => 'boolean',
        'bad_words_flag' => 'boolean',   // ← thêm mới
        'created_at'     => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }
}