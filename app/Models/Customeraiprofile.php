<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerAiProfile extends Model
{
    protected $fillable = [
        'user_id',
        'lead_label',
        'lead_stage',
        'total_score',
        'score_view',
        'score_chat',
        'score_order',
        'interest_categories',
        'price_range',
        'last_seen_product_id',
        'top_interest_product_id',
        'suggested_products',
        'keywords_history',
        'repurchase_probability',
        'predicted_repurchase_at',
        'repurchase_product_id',
        'voucher_recommended',
        'voucher_reason',
        'scored_at',
        // Nhận định AI (Gemini) sinh ra sau mỗi lần tính lại điểm — trước đó thiếu 2 dòng
        // này trong $fillable nên $profile->update(['ai_summary' => ...]) bị Eloquent
        // chặn mass-assignment và không bao giờ lưu được vào DB.
        'ai_summary',
        'ai_summary_generated_at',
    ];

    protected $casts = [
        'interest_categories'     => 'array',
        'price_range'             => 'array',
        'suggested_products'      => 'array',
        'keywords_history'        => 'array',
        'voucher_recommended'     => 'boolean',
        'scored_at'               => 'datetime',
        'predicted_repurchase_at' => 'datetime',
        'ai_summary_generated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lastSeenProduct()
    {
        return $this->belongsTo(Product::class, 'last_seen_product_id');
    }

    public function topInterestProduct()
    {
        return $this->belongsTo(Product::class, 'top_interest_product_id');
    }

    public function repurchaseProduct()
    {
        return $this->belongsTo(Product::class, 'repurchase_product_id');
    }
}