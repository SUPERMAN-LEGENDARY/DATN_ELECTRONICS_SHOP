<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đặt lại mật khẩu – ElectronicShop</title>
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
    </style>
</head>

<body>
    <div class="auth-wrap">
        <div class="auth-card">
            <div class="auth-header">
                <a href="{{ route('home') }}" class="sm-logo">ElectronicShop</a>
                <p>Tạo mật khẩu mới</p>
            </div>
            <div class="auth-body">
                <form method="POST" action="{{ route('password.store') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div class="form-group">
                        <label for="email">Email</label>
                        <div class="input-icon">
                            <i class="fas fa-envelope"></i>
                            <input id="email" type="email" name="email"
                                value="{{ old('email', $request->email) }}"
                                required autofocus autocomplete="username">
                        </div>
                        @error('email')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Mật khẩu mới</label>
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                            <input id="password" type="password" name="password"
                                placeholder="Ít nhất 8 ký tự" required autocomplete="new-password">
                        </div>
                        @error('password')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Xác nhận mật khẩu mới</label>
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                placeholder="Nhập lại mật khẩu" required autocomplete="new-password">
                        </div>
                    </div>

                    <button type="submit" class="btn-main">
                        <i class="fas fa-key"></i> Đặt lại mật khẩu
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>