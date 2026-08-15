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
        'note',
        // Snapshot thông tin liên hệ tại thời điểm đặt hàng.
        // Với khách vãng lai (user_id = null) đây là nguồn duy nhất để biết
        // tên/SĐT/email khách hàng và gửi mail xác nhận đơn.
        // Với khách có tài khoản, vẫn lưu lại để tránh trường hợp sau này
        // khách đổi tên/email trong hồ sơ làm sai lệch dữ liệu đơn hàng cũ.
        'customer_name',
        'customer_phone',
        'customer_email',
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

    // ─── Helpers ────────────────────────────────────────────────

    /**
     * Email để gửi thông báo đơn hàng (xác nhận, cập nhật trạng thái...).
     * Ưu tiên customer_email đã lưu trên đơn (đúng với cả khách vãng lai
     * lẫn khách có tài khoản), fallback sang email tài khoản nếu vì lý do
     * gì đó customer_email bị thiếu (đơn cũ tạo trước khi có cột này).
     */
    public function getContactEmailAttribute(): ?string
    {
        return $this->customer_email ?: $this->user?->email;
    }

    /**
     * true nếu đây là đơn của khách vãng lai (không có tài khoản).
     */
    public function getIsGuestOrderAttribute(): bool
    {
        return is_null($this->user_id);
    }
}