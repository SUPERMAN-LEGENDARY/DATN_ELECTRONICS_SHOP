<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Quên mật khẩu – ElectronicShop</title>

    <!-- Font Awesome -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        /* =========================================================
           RESET
        ========================================================= */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            min-height: 100%;
        }

        :root {
            --white: #ffffff;
            --text: rgba(255, 255, 255, .96);
            --text-soft: rgba(255, 255, 255, .72);
            --text-muted: rgba(255, 255, 255, .48);

            --glass: rgba(255, 255, 255, .04);
            --glass-hover: rgba(255, 255, 255, .08);
            --glass-focus: rgba(255, 255, 255, .10);

            --border: rgba(255, 255, 255, .15);
            --border-hover: rgba(255, 255, 255, .28);
            --border-focus: rgba(255, 255, 255, .45);

            --error: #ff8585;
            --success: #6ee7b7;

            --ease: cubic-bezier(.4, 0, .2, 1);
        }

        /* =========================================================
           BODY
        ========================================================= */
        body {
            font-family:
                'Manrope',
                'Segoe UI',
                Inter,
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                sans-serif;

            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            overflow-y: auto;
            position: relative;
            background: transparent;
        }

        /* =========================================================
           VIDEO BACKGROUND
        ========================================================= */
        .video-bg {
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 0;
            pointer-events: none;
        }

        .video-bg-mobile {
            display: none;
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 0;
            pointer-events: none;
        }

        /* =========================================================
           DARK OVERLAY
        ========================================================= */
        .video-overlay {
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.42);
            z-index: 1;
            pointer-events: none;
        }

        .video-overlay::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(
                    circle at center,
                    rgba(255, 255, 255, .035),
                    rgba(0, 0, 0, .20) 55%,
                    rgba(0, 0, 0, .42) 100%
                );
        }

        /* =========================================================
           MAIN WRAPPER
        ========================================================= */
        .auth-wrap {
            width: 100%;
            max-width: 520px;
            padding: 35px 18px;
            position: relative;
            z-index: 2;
        }

        /* =========================================================
           GLASS CARD (Trong Suốt)
        ========================================================= */
        .auth-card {
            position: relative;
            overflow: hidden;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border);
            box-shadow:
                0 25px 50px rgba(0, 0, 0, 0.35),
                inset 0 1px 1px rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        .auth-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 1px;
            background:
                linear-gradient(
                    90deg,
                    transparent,
                    rgba(255, 255, 255, .55),
                    transparent
                );
            opacity: .7;
        }

        /* =========================================================
           HEADER
        ========================================================= */
        .auth-header {
            text-align: center;
            padding: 38px 42px 20px;
        }

        .sm-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition:
                transform .25s var(--ease),
                opacity .25s var(--ease),
                filter .25s var(--ease);
        }

        .sm-logo:hover {
            transform: translateY(-2px);
            opacity: .92;
            filter: drop-shadow(0 8px 20px rgba(255, 255, 255, .16));
        }

        .sm-logo img {
            display: block;
            width: 250px;
            max-width: 80vw;
            max-height: 105px;
            height: auto;
            object-fit: contain;
            object-position: center;
            filter: drop-shadow(0 5px 18px rgba(0, 0, 0, .25));
        }

        .auth-header h1 {
            margin-top: 18px;
            color: var(--text);
            font-size: 28px;
            line-height: 1.2;
            font-weight: 800;
            letter-spacing: -.04em;
        }

        .auth-header p {
            margin-top: 8px;
            color: var(--text-soft);
            font-size: 14px;
            line-height: 1.5;
            font-weight: 500;
        }

        /* =========================================================
           BODY
        ========================================================= */
        .auth-body {
            padding: 14px 42px 32px;
        }

        .desc-text {
            color: var(--text-soft);
            font-size: 13px;
            line-height: 1.6;
            margin-bottom: 20px;
            text-align: center;
        }

        .alert-success {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(110, 231, 183, 0.12);
            border: 1px solid rgba(110, 231, 183, 0.35);
            color: var(--success);
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            line-height: 1.5;
            margin-bottom: 20px;
            backdrop-filter: blur(8px);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group > label {
            display: block;
            margin-bottom: 8px;
            color: rgba(255, 255, 255, .82);
            font-size: 13px;
            font-weight: 700;
            letter-spacing: -.01em;
        }

        /* =========================================================
           INPUT & ICON
        ========================================================= */
        .input-icon {
            position: relative;
            width: 100%;
        }

        .input-icon > i {
            position: absolute;
            left: 17px;
            top: 50%;
            transform: translateY(-50%);
            color: #ffffff;
            font-size: 16px;
            z-index: 2;
            pointer-events: none;
            filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.4));
            transition: color .2s var(--ease);
        }

        .input-icon input {
            width: 100%;
            height: 54px;
            padding: 0 16px 0 46px;
            border: 1px solid var(--border);
            border-radius: 14px;
            outline: none;
            background: var(--glass);
            color: #ffffff;
            font-family: inherit;
            font-size: 14px;
            font-weight: 500;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            transition:
                background .25s var(--ease),
                border-color .25s var(--ease),
                box-shadow .25s var(--ease);
        }

        .input-icon input::placeholder {
            color: rgba(255, 255, 255, .40);
            font-weight: 400;
        }

        .input-icon input:hover {
            background: var(--glass-hover);
            border-color: var(--border-hover);
        }

        .input-icon input:focus {
            background: var(--glass-focus);
            border-color: var(--border-focus);
            box-shadow:
                0 0 0 4px rgba(255, 255, 255, 0.05),
                0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .input-icon:focus-within > i {
            color: #ffffff;
        }

        .error-msg {
            margin-top: 7px;
            color: var(--error);
            font-size: 12px;
            font-weight: 600;
            line-height: 1.4;
        }

        /* =========================================================
           BUTTON
        ========================================================= */
        .btn-main {
            width: 100%;
            height: 54px;
            margin-top: 4px;
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 14px;
            background: linear-gradient(
                135deg,
                rgba(255, 255, 255, 0.16),
                rgba(255, 255, 255, 0.06)
            );
            color: #ffffff;
            font-family: inherit;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: -.01em;
            cursor: pointer;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
            transition: all .22s var(--ease);
        }

        .btn-main:hover {
            transform: translateY(-2px);
            background: linear-gradient(
                135deg,
                rgba(255, 255, 255, 0.25),
                rgba(255, 255, 255, 0.10)
            );
            border-color: rgba(255, 255, 255, 0.35);
            box-shadow: 0 14px 30px rgba(0, 0, 0, 0.35);
        }

        .btn-main:active {
            transform: translateY(0);
        }

        .btn-main i {
            margin-right: 7px;
        }

        /* =========================================================
           DIVIDER & FOOTER
        ========================================================= */
        .divider {
            height: 1px;
            border: 0;
            margin: 0 42px;
            background:
                linear-gradient(
                    90deg,
                    transparent,
                    rgba(255, 255, 255, .18),
                    transparent
                );
        }

        .auth-footer {
            padding: 22px 42px 28px;
            text-align: center;
            color: rgba(255, 255, 255, .54);
            font-size: 13px;
            font-weight: 500;
        }

        .auth-footer a {
            color: rgba(255, 255, 255, .92);
            font-weight: 800;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: color .2s, opacity .2s;
        }

        .auth-footer a:hover {
            color: #ffffff;
            text-decoration: underline;
        }

        /* =========================================================
           VIDEO FALLBACK
        ========================================================= */
        .video-fallback {
            position: fixed;
            inset: 0;
            z-index: -4;
            background:
                radial-gradient(
                    circle at center,
                    #20252d 0%,
                    #090b0f 70%
                );
        }

        /* =========================================================
           RESPONSIVE
        ========================================================= */
        @media (max-width: 600px) {
            .video-bg {
                display: none;
            }

            .video-bg-mobile {
                display: block;
            }

            .video-overlay {
                background:
                    linear-gradient(
                        180deg,
                        rgba(0, 0, 0, .62),
                        rgba(0, 0, 0, .48),
                        rgba(0, 0, 0, .68)
                    );
            }

            .auth-wrap {
                padding: 18px 13px;
            }

            .auth-card {
                border-radius: 23px;
            }

            .auth-header {
                padding: 30px 24px 18px;
            }

            .sm-logo img {
                width: 210px;
                max-height: 85px;
            }

            .auth-header h1 {
                font-size: 24px;
            }

            .auth-body {
                padding: 14px 24px 26px;
            }

            .input-icon input,
            .btn-main {
                height: 52px;
            }

            .divider {
                margin: 0 24px;
            }

            .auth-footer {
                padding: 18px 24px 24px;
            }
        }
    </style>
</head>

<body>

    <!-- VIDEO FALLBACK -->
    <div class="video-fallback"></div>

    <!-- DESKTOP VIDEO (16:9) -->
    <video autoplay muted loop playsinline preload="auto" class="video-bg" aria-hidden="true">
        <source src="{{ asset('videos/16.9.2.mp4') }}" type="video/mp4">
    </video>

    <!-- MOBILE VIDEO (9:16) -->
    <video autoplay muted loop playsinline preload="auto" class="video-bg-mobile" aria-hidden="true">
        <source src="{{ asset('videos/9.16.2.mp4') }}" type="video/mp4">
    </video>

    <!-- DARK OVERLAY -->
    <div class="video-overlay"></div>

    <!-- FORGOT PASSWORD WRAPPER -->
    <main class="auth-wrap">
        <section class="auth-card">

            <!-- HEADER -->
            <header class="auth-header">
                <a href="{{ route('home') }}" class="sm-logo" aria-label="Electronics Shop">
                    <img src="{{ asset('images/logo.png') }}" alt="Electronics Shop">
                </a>
                <h1>Quên mật khẩu?</h1>
                <p>Khôi phục quyền truy cập tài khoản</p>
            </header>

            <!-- FORM BODY -->
            <div class="auth-body">
                @if(session('status'))
                    <div class="alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <p class="desc-text">
                    Nhập địa chỉ email đã đăng ký. Chúng tôi sẽ gửi đường dẫn đặt lại mật khẩu về hòm thư của bạn.
                </p>

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <!-- EMAIL -->
                    <div class="form-group">
                        <label for="email">Email</label>
                        <div class="input-icon">
                            <i class="fas fa-envelope"></i>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="your@email.com"
                                required
                                autofocus
                                autocomplete="username">
                        </div>
                        @error('email')
                            <div class="error-msg">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- SUBMIT BUTTON -->
                    <button type="submit" class="btn-main">
                        <i class="fas fa-paper-plane"></i> Gửi liên kết khôi phục
                    </button>
                </form>
            </div>

            <hr class="divider">

            <!-- FOOTER -->
            <footer class="auth-footer">
                <a href="{{ route('login') }}">
                    <i class="fas fa-arrow-left"></i> Quay lại đăng nhập
                </a>
            </footer>

        </section>
    </main>

    <!-- JAVASCRIPT -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Autoplay Video Fallback
            const videos = document.querySelectorAll('video');
            videos.forEach(function (video) {
                video.play().catch(function () {
                    const startVideo = function () {
                        video.play().catch(function () {});
                    };
                    document.addEventListener('click', startVideo, { once: true });
                    document.addEventListener('touchstart', startVideo, { once: true });
                });
            });
        });
    </script>
</body>

</html>