<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

/**
 * Mail nhắc khách hoàn tất thanh toán cho đơn hàng đã được xác nhận
 * nhưng payment_status vẫn đang "unpaid" (VD: thanh toán online bị
 * bỏ dở). Được gửi từ OrderController::sendOrderStatusMail() khi đơn
 * chuyển sang trạng thái "confirmed" mà chưa thanh toán.
 */
class OrderPaymentReminderMail extends Mailable
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
            subject: "Vui lòng thanh toán cho đơn hàng #{$this->order->id}",
        );
    }

    public function content(): Content
    {
        // Chỉ đơn MoMo mới có cổng thanh toán lại (retryMomoPayment); COD thì
        // "thanh toán khi nhận hàng" nên không có link thanh toán online.
        //
        // Dùng SIGNED URL (có hạn 7 ngày) thay vì route() thông thường: khách
        // bấm link này trực tiếp từ email, không cần đăng nhập vào tài khoản.
        // Route 'checkout.momo.retry' có middleware 'signed' nên chỉ link
        // được sinh đúng cách (như ở đây) và còn hạn mới truy cập được.
        $paymentUrl = $this->order->payment_method === 'momo'
            ? URL::temporarySignedRoute(
                'checkout.momo.retry',
                now()->addDays(7),
                ['order' => $this->order->id]
            )
            : null;

        return new Content(
            view: 'emails.order-payment-reminder',
            with: [
                'order'      => $this->order,
                'paymentUrl' => $paymentUrl,
            ],
        );
    }
}