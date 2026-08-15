<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác nhận thanh toán đơn hàng #{{ $order->id }}</title>
</head>
<body style="font-family: Arial, sans-serif; color:#333; line-height:1.6;">
    <h2>Xin chào {{ $order->user->name ?? 'quý khách' }},</h2>

    <p>
        Chúng tôi đã ghi nhận thanh toán thành công cho đơn hàng
        <strong>#{{ $order->id }}</strong>. Cảm ơn bạn đã mua sắm, đơn hàng
        sẽ tiếp tục được xử lý và giao đến bạn theo tiến độ.
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

    <p style="color:#999;font-size:12px;margin-top:24px;">
        Nếu bạn có thắc mắc về thanh toán, xin liên hệ bộ phận chăm sóc
        khách hàng.
    </p>
</body>
</html>