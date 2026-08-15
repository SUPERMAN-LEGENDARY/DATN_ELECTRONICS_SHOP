<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mail xác nhận đã nhận được thanh toán cho đơn hàng, gửi khi
 * payment_status chuyển unpaid -> paid MÀ KHÔNG kèm theo việc status
 * chuyển sang "confirmed" trong cùng request (trường hợp đó đã có
 * OrderConfirmedMail lo, xem OrderController::updateStatus()).
 *
 * Ví dụ tình huống dùng: đơn đang confirmed/processing/shipped ở trạng
 * thái unpaid (COD chưa thu tiền, hoặc thanh toán online đến trễ) rồi
 * được đánh dấu paid sau đó -> khách cần được báo đã ghi nhận thanh toán.
 */
class OrderPaymentConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Đã xác nhận thanh toán cho đơn hàng #{$this->order->id}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-payment-confirmed',
            with: [
                'order' => $this->order,
            ],
        );
    }
}