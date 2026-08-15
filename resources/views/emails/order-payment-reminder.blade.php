<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Nhắc thanh toán đơn hàng #{{ $order->id }}</title>
</head>
<body style="font-family: Arial, sans-serif; color:#333; line-height:1.6;">
    <h2>Xin chào {{ $order->user->name ?? 'quý khách' }},</h2>

    <p>
        Đơn hàng <strong>#{{ $order->id }}</strong> của bạn đã được xác nhận,
        nhưng chúng tôi chưa nhận được thanh toán. Vui lòng hoàn tất thanh
        toán để đơn hàng được tiếp tục xử lý và giao đến bạn sớm nhất.
    </p>

    <table style="border-collapse:collapse;margin:16px 0;">
        <tr>
            <td style="padding:4px 12px 4px 0;color:#666;">Mã đơn hàng:</td>
            <td style="padding:4px 0;font-weight:600;">#{{ $order->id }}</td>
        </tr>
        <tr>
            <td style="padding:4px 12px 4px 0;color:#666;">Tổng tiền:</td>
            <td style="padding:4px 0;font-weight:600;">{{ number_format($order->total) }}đ</td>
        </tr>
        <tr>
            <td style="padding:4px 12px 4px 0;color:#666;">Phương thức thanh toán:</td>
            <td style="padding:4px 0;font-weight:600;">{{ strtoupper($order->payment_method) }}</td>
        </tr>
    </table>

    @if($paymentUrl)
        <p>
            <a href="{{ $paymentUrl }}"
               style="background:#1565C0;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;font-weight:600;display:inline-block;">
                Thanh toán ngay
            </a>
        </p>
    @else
        <p style="color:#666;">
            Đơn hàng thanh toán khi nhận hàng (COD) — vui lòng chuẩn bị số tiền trên khi shipper giao hàng.
        </p>
    @endif

    <p style="color:#999;font-size:12px;margin-top:24px;">
        Nếu bạn đã thanh toán, vui lòng bỏ qua email này. Mọi thắc mắc xin
        liên hệ bộ phận chăm sóc khách hàng.
    </p>
</body>
</html>