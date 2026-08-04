<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $table = 'orders';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'address_id',
        'voucher_id',
        'status',
        'payment_method',
        'payment_status',
        'subtotal',
        'discount_amount',
        'total',
        'note'
    ];

    // ─── Relationships ──────────────────────────────────────────

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    // Toàn bộ lịch sử giao dịch thanh toán của đơn hàng (có thể có nhiều lần thử,
    // ví dụ MoMo thất bại rồi khách thử lại)
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Giao dịch thanh toán mới nhất — dùng khi chỉ cần biết trạng thái hiện tại
    public function payment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }
}
