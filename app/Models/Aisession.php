<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiSession extends Model
{
    protected $fillable = [
        'user_id',
        'profile_id',
        'session_token',
        'intent_label',
        'sentiment_score',
        'messages',
        'search_keywords',
        'product_interactions',
        'total_messages',
        'tokens_used',
    ];

    protected $casts = [
        'messages'             => 'array',
        'search_keywords'      => 'array',
        'product_interactions' => 'array',
        'sentiment_score'      => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function profile()
    {
        return $this->belongsTo(CustomerAiProfile::class, 'profile_id');
    }
}