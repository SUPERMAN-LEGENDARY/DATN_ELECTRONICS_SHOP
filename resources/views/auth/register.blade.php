<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng ký – ElectronicShop</title>
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
            justify-content: center;
            padding: 20px 0
        }

        .auth-wrap {
            width: 100%;
            max-width: 480px;
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
            transition: background .2s;
            margin-top: 4px
        }

        .btn-main:hover {
            background: #333333
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

        .auth-footer a:hover {
            text-decoration: underline
        }

        .divider {
            border: none;
            border-top: 1px solid #eee;
            margin: 20px 0
        }

        .row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px
        }

        @media(max-width:480px) {
            .row-2 {
                grid-template-columns: 1fr
            }
        }

        .hint {
            font-size: 11px;
            color: #999;
            margin-top: 3px
        }
    </style>
</head>

<body>
    <div class="auth-wrap">
        <div class="auth-card">
            <div class="auth-header">
                <a href="{{ route('home') }}" class="sm-logo">ElectronicShop</a>
                <p>Tạo tài khoản mới miễn phí</p>
            </div>
            <div class="auth-body">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="row-2">
                        <div class="form-group">
                            <label for="name">Họ và tên</label>
                            <div class="input-icon">
                                <i class="fas fa-user"></i>
                                <input id="name" type="text" name="name" value="{{ old('name') }}"
                                    placeholder="Nguyễn Văn A" required autofocus autocomplete="name">
                            </div>
                            @error('name')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label for="phone">Số điện thoại</label>
                            <div class="input-icon">
                                <i class="fas fa-phone"></i>
                                <input id="phone" type="tel" name="phone" value="{{ old('phone') }}"
                                    placeholder="0901234567" autocomplete="tel" maxlength="15">
                            </div>
                            @error('phone')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <div class="input-icon">
                            <i class="fas fa-envelope"></i>
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                placeholder="your@email.com" required autocomplete="username">
                        </div>
                        @error('email')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>

                    <div class="row-2">
                        <div class="form-group">
                            <label for="password">Mật khẩu</label>
                            <div class="input-icon">
                                <i class="fas fa-lock"></i>
                                <input id="password" type="password" name="password"
                                    placeholder="Ít nhất 8 ký tự" required autocomplete="new-password">
                            </div>
                            @error('password')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Xác nhận mật khẩu</label>
                            <div class="input-icon">
                                <i class="fas fa-lock"></i>
                                <input id="password_confirmation" type="password" name="password_confirmation"
                                    placeholder="Nhập lại mật khẩu" required autocomplete="new-password">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-main">
                        <i class="fas fa-user-plus"></i> Đăng ký tài khoản
                    </button>
                </form>
            </div>
            <hr class="divider">
            <div class="auth-footer">
                Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a>
            </div>
        </div>
    </div>
</body>

</html>