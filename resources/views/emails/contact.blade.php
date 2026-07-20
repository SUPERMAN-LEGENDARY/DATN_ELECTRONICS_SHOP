<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Liên hệ mới</title>
</head>

<body style="margin:0;padding:0;background:#f3f4f6;font-family:'Segoe UI',Roboto,Arial,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0"
                    style="background:#ffffff;border-radius:10px;overflow:hidden;max-width:600px;width:100%;">

                    {{-- Header --}}
                    <tr>
                        <td style="background:#2563eb;padding:22px 28px;">
                            <h1 style="margin:0;color:#ffffff;font-size:18px;">
                                📩 Liên hệ mới từ ElectronicShop
                            </h1>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:28px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding:8px 0;color:#6b7280;font-size:13px;width:120px;vertical-align:top;">Họ tên</td>
                                    <td style="padding:8px 0;color:#111827;font-size:14px;font-weight:600;">{{ $contact->name }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;color:#6b7280;font-size:13px;vertical-align:top;">Email</td>
                                    <td style="padding:8px 0;color:#111827;font-size:14px;">
                                        <a href="mailto:{{ $contact->email }}" style="color:#2563eb;text-decoration:none;">{{ $contact->email }}</a>
                                    </td>
                                </tr>
                                @if($contact->phone)
                                <tr>
                                    <td style="padding:8px 0;color:#6b7280;font-size:13px;vertical-align:top;">Điện thoại</td>
                                    <td style="padding:8px 0;color:#111827;font-size:14px;">{{ $contact->phone }}</td>
                                </tr>
                                @endif
                                @if($contact->subject)
                                <tr>
                                    <td style="padding:8px 0;color:#6b7280;font-size:13px;vertical-align:top;">Chủ đề</td>
                                    <td style="padding:8px 0;color:#111827;font-size:14px;">{{ $contact->subject }}</td>
                                </tr>
                                @endif
                            </table>

                            <div style="margin-top:18px;padding-top:18px;border-top:1px solid #f1f5f9;">
                                <p style="margin:0 0 6px;color:#6b7280;font-size:13px;">Nội dung</p>
                                <p style="margin:0;color:#111827;font-size:14px;line-height:1.7;white-space:pre-wrap;">{{ $contact->message }}</p>
                            </div>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:16px 28px;background:#f8fafc;border-top:1px solid #f1f5f9;">
                            <p style="margin:0;color:#9ca3af;font-size:12px;">
                                Gửi lúc {{ $contact->created_at?->format('H:i d/m/Y') }} · ElectronicShop
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>