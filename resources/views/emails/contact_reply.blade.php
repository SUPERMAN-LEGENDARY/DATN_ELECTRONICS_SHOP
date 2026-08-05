<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phản hồi liên hệ</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        .header { background: linear-gradient(135deg, #1565C0, #0D47A1); padding: 32px 36px; text-align: center; }
        .header h1 { color: #fff; font-size: 22px; margin: 0; letter-spacing: -.3px; }
        .header p  { color: rgba(255,255,255,.8); font-size: 13px; margin: 6px 0 0; }
        .body { padding: 36px; }
        .greeting { font-size: 16px; font-weight: 600; color: #1a1a2e; margin-bottom: 16px; }
        .reply-box {
            background: #f0f6ff;
            border-left: 4px solid #1565C0;
            border-radius: 0 8px 8px 0;
            padding: 20px 22px;
            margin: 20px 0;
            font-size: 15px;
            line-height: 1.7;
            color: #333;
            white-space: pre-wrap;
        }
        .original { margin-top: 28px; padding-top: 20px; border-top: 1px solid #f0f0f0; }
        .original h3 { font-size: 12px; font-weight: 700; color: #9e9e9e; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 14px; }
        .original-item { display: flex; gap: 12px; margin-bottom: 8px; font-size: 13px; }
        .original-item strong { color: #555; min-width: 80px; flex-shrink: 0; }
        .original-item span { color: #333; }
        .original-msg { background: #fafafa; border: 1px solid #eee; border-radius: 6px; padding: 14px 16px; font-size: 13px; color: #555; line-height: 1.65; margin-top: 10px; white-space: pre-wrap; }
        .footer { background: #f9fafb; padding: 20px 36px; text-align: center; font-size: 12px; color: #9e9e9e; border-top: 1px solid #f0f0f0; }
        .footer a { color: #1565C0; text-decoration: none; }
        .logo { font-weight: 800; font-size: 16px; color: #fff; letter-spacing: .5px; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <div class="logo">⚡ ElectronicShop</div>
        <h1>Phản hồi từ chúng tôi</h1>
        <p>Cảm ơn bạn đã liên hệ với ElectronicShop</p>
    </div>

    <div class="body">
        <p class="greeting">Xin chào {{ $contact->name }},</p>

        <p style="font-size:14px;color:#555;line-height:1.7;margin-bottom:20px;">
            Chúng tôi đã nhận được liên hệ của bạn và xin gửi phản hồi như sau:
        </p>

        <div class="reply-box">{{ $replyMessage }}</div>

        <p style="font-size:13px;color:#777;margin-top:20px;line-height:1.6;">
            Nếu bạn có thêm câu hỏi, đừng ngần ngại liên hệ lại với chúng tôi qua email này
            hoặc truy cập trang <a href="{{ route('contact.index') }}" style="color:#1565C0;">liên hệ</a> của chúng tôi.
        </p>

        <div class="original">
            <h3>Nội dung liên hệ ban đầu</h3>
            <div class="original-item"><strong>Họ tên:</strong>  <span>{{ $contact->name }}</span></div>
            <div class="original-item"><strong>Email:</strong>   <span>{{ $contact->email }}</span></div>
            @if($contact->phone)
            <div class="original-item"><strong>SĐT:</strong>    <span>{{ $contact->phone }}</span></div>
            @endif
            @if($contact->subject)
            <div class="original-item"><strong>Chủ đề:</strong> <span>{{ $contact->subject }}</span></div>
            @endif
            <div class="original-msg">{{ $contact->message }}</div>
        </div>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} ElectronicShop — 123 Nguyễn Văn Linh, Đà Nẵng</p>
        <p><a href="{{ route('home') }}">Trang chủ</a> · <a href="{{ route('contact.index') }}">Liên hệ</a></p>
    </div>
</div>
</body>
</html>
