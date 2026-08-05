<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quên mật khẩu – ElectronicShop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        :root {
            --sm-black: #000000;
            --sm-white: #ffffff;
            --sm-ease: cubic-bezier(.4, 0, .2, 1);
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f4f4;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center
        }

        .auth-wrap {
            width: 100%;
            max-width: 440px;
            padding: 16px
        }

        .auth-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, .08);
            overflow: hidden
        }

        .auth-header {
            background: #000000;
            padding: 28px 32px 24px;
            text-align: center
        }

        /* Logo trong khung viền – y hệt logo SAMSUNG (nền đen: viền/chữ trắng) */
        .sm-logo {
            display: inline-flex; align-items: center; justify-content: center;
            border: 1.5px solid var(--sm-white);
            padding: 5px 12px;
            font-family: 'Manrope', sans-serif;
            font-weight: 800; font-size: 14px; letter-spacing: .14em;
            color: var(--sm-white); text-transform: uppercase;
            white-space: nowrap; flex-shrink: 0;
            text-decoration: none;
            transition: background .25s var(--sm-ease), color .25s var(--sm-ease);
        }
        .sm-logo:hover { background: var(--sm-white); color: var(--sm-black); }

        .auth-header p {
            color: rgba(255, 255, 255, .6);
            font-size: 13px;
            margin-top: 12px
        }

        .auth-body {
            padding: 32px
        }

        .desc {
            font-size: 13px;
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6
        }

        .form-group {
            margin-bottom: 18px
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #444;
            margin-bottom: 6px
        }

        .input-icon {
            position: relative
        }

        .input-icon i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            font-size: 14px
        }

        .input-icon input {
            width: 100%;
            padding: 10px 12px 10px 36px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            color: #333;
            outline: none;
            transition: border .2s
        }

        .input-icon input:focus {
            border-color: #000000;
            box-shadow: 0 0 0 3px rgba(0, 0, 0, .1)
        }

        .error-msg {
            color: #C62828;
            font-size: 12px;
            margin-top: 4px
        }

        .btn-main {
            width: 100%;
            padding: 12px;
            background: #000000;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: background .2s
        }

        .btn-main:hover {
            background: #333333
        }

        .alert-success {
            background: #E8F5E9;
            border: 1px solid #A5D6A7;
            color: #2E7D32;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 16px
        }

        .auth-footer {
            text-align: center;
            padding: 0 32px 28px;
            font-size: 13px;
            color: #666
        }

        .auth-footer a {
            color: #000000;
            font-weight: 600;
            text-decoration: none
        }

        .divider {
            border: none;
            border-top: 1px solid #eee;
            margin: 20px 0
        }
    </style>
</head>

<body>
    <div class="auth-wrap">
        <div class="auth-card">
            <div class="auth-header">
                <a href="{{ route('home') }}" class="sm-logo">ElectronicShop</a>
                <p>Đặt lại mật khẩu của bạn</p>
            </div>
            <div class="auth-body">
                @if(session('status'))
                <div class="alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('status') }}
                </div>
                @endif

                <p class="desc">
                    Nhập địa chỉ email đã đăng ký. Chúng tôi sẽ gửi link đặt lại mật khẩu cho bạn.
                </p>

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="form-group">
                        <label for="email">Email</label>
                        <div class="input-icon">
                            <i class="fas fa-envelope"></i>
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                placeholder="your@email.com" required autofocus>
                        </div>
                        @error('email')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn-main">
                        <i class="fas fa-paper-plane"></i> Gửi link đặt lại mật khẩu
                    </button>
                </form>
            </div>
            <hr class="divider">
            <div class="auth-footer">
                <a href="{{ route('login') }}"><i class="fas fa-arrow-left"></i> Quay lại đăng nhập</a>
            </div>
        </div>
    </div>
</body>

</html>