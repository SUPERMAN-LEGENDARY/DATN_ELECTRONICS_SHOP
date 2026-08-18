<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Tin tức mới</title>
</head>

<body style="margin:0;padding:0;background:#f3f4f6;font-family:'Segoe UI',Roboto,Arial,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0"
                    style="background:#ffffff;border-radius:10px;overflow:hidden;max-width:600px;width:100%;">

                    <tr>
                        <td style="background:#2563eb;padding:28px;text-align:center;">
                            <h1 style="margin:0;color:#ffffff;font-size:20px;">ElectronicShop</h1>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:32px 28px;">
                            <h2 style="margin:0 0 12px;color:#111827;font-size:18px;">{{ $news->title }}</h2>
                            <p style="margin:0 0 16px;color:#4b5563;font-size:14px;line-height:1.7;">
                                {{ \Illuminate\Support\Str::limit(strip_tags($news->excerpt ?: $news->content), 120) }}
                            </p>
                            <a href="{{ route('news.show', $news->slug) }}"
                                style="display:inline-block;padding:12px 28px;background:#2563eb;color:#ffffff;
                                text-decoration:none;border-radius:8px;font-size:14px;font-weight:600;">
                                Xem chi tiết
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:16px 28px;background:#f8fafc;border-top:1px solid #f1f5f9;">
                            <p style="margin:0;color:#9ca3af;font-size:12px;">
                                Bạn nhận được email này vì đã đăng ký nhận tin tại ElectronicShop.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>
