<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Đăng ký – ElectronicShop</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

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

            --glass: rgba(255, 255, 255, .075);
            --glass-hover: rgba(255, 255, 255, .11);
            --glass-focus: rgba(255, 255, 255, .14);

            --border: rgba(255, 255, 255, .18);
            --border-hover: rgba(255, 255, 255, .28);
            --border-focus: rgba(255, 255, 255, .48);

            --error: #ff8585;

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

        /* Mobile video */
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
            max-width: 600px;
            padding: 35px 18px;
            position: relative;
            z-index: 2;
        }

        /* =========================================================
   GLASS CARD (Khung thẻ chính trong suốt)
========================================================= */
.auth-card {
    position: relative;
    overflow: hidden;
    border-radius: 28px;
    
    /* Giảm độ đục nền để trong suốt hơn */
    background: rgba(255, 255, 255, 0.03);
    
    /* Viền kính mỏng tinh tế */
    border: 1px solid rgba(255, 255, 255, 0.15);
    
    /* Bóng đổ nhẹ nhàng */
    box-shadow:
        0 25px 50px rgba(0, 0, 0, 0.35),
        inset 0 1px 1px rgba(255, 255, 255, 0.15);

    /* Độ nhòe mờ kính */
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

        /* =========================================================
           LOGO
        ========================================================= */
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

        /* =========================================================
           TITLE
        ========================================================= */
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
           BODY & FORM GRID
        ========================================================= */
        .auth-body {
            padding: 14px 42px 32px;
        }

        .row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group > label:not(.form-check) {
            display: block;
            margin-bottom: 8px;
            color: rgba(255, 255, 255, .82);
            font-size: 13px;
            font-weight: 700;
            letter-spacing: -.01em;
        }

        /* =========================================================
   INPUT (Các ô nhập liệu trong suốt)
========================================================= */
.input-icon input {
    width: 100%;
    height: 52px;
    padding: 0 46px 0 46px;
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 14px;
    outline: none;
    
    /* Nền ô input siêu trong suốt */
    background: rgba(255, 255, 255, 0.04);
    
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

/* Hover & Focus vào ô input */
.input-icon input:hover {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.28);
}

.input-icon input:focus {
    background: rgba(255, 255, 255, 0.10);
    border-color: rgba(255, 255, 255, 0.45);
    box-shadow:
        0 0 0 4px rgba(255, 255, 255, 0.05),
        0 10px 25px rgba(0, 0, 0, 0.15);
}

        /* Password Toggle */
        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 0;
            background: transparent;
            color: #ffffff;
            font-size: 14px;
            cursor: pointer;
            border-radius: 8px;
            filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.4));
            transition: color .2s, background .2s;
        }

        .password-toggle:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, .15);
        }

        /* =========================================================
           ERROR
        ========================================================= */
        .error-msg {
            margin-top: 7px;
            color: var(--error);
            font-size: 12px;
            font-weight: 600;
            line-height: 1.4;
        }

        /* =========================================================
   NÚT ĐĂNG KÝ (Nút bấm Glass trong suốt)
========================================================= */
.btn-main {
    width: 100%;
    height: 54px;
    margin-top: 6px;
    border: 1px solid rgba(255, 255, 255, 0.22);
    border-radius: 14px;
    
    /* Gradient kính trong suốt */
    background: linear-gradient(
        135deg,
        rgba(255, 255, 255, 0.16),
        rgba(255, 255, 255, 0.06)
    );
    
    color: #ffffff;
    font-family: inherit;
    font-size: 15px;
    font-weight: 800;
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

            .row-2 {
                grid-template-columns: 1fr;
                gap: 0;
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
        <source src="{{ asset('videos/16.9.1.mp4') }}" type="video/mp4">
    </video>

    <!-- MOBILE VIDEO (9:16) -->
    <video autoplay muted loop playsinline preload="auto" class="video-bg-mobile" aria-hidden="true">
        <source src="{{ asset('videos/9.16.1.mp4') }}" type="video/mp4">
    </video>

    <!-- DARK OVERLAY -->
    <div class="video-overlay"></div>

    <!-- REGISTER -->
    <main class="auth-wrap">
        <section class="auth-card">

            <!-- HEADER -->
            <header class="auth-header">
                <a href="{{ route('home') }}" class="sm-logo" aria-label="Electronics Shop">
                    <img src="{{ asset('images/logo.png') }}" alt="Electronics Shop">
                </a>
                <h1>Đăng ký tài khoản</h1>
                <p>Tạo tài khoản mới hoàn toàn miễn phí</p>
            </header>

            <!-- FORM BODY -->
            <div class="auth-body">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="row-2">
                        <!-- NAME -->
                        <div class="form-group">
                            <label for="name">Họ và tên</label>
                            <div class="input-icon">
                                <i class="fas fa-user"></i>
                                <input id="name" type="text" name="name" value="{{ old('name') }}"
                                    placeholder="Nguyễn Văn A" required autofocus autocomplete="name">
                            </div>
                            @error('name')
                                <div class="error-msg">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- PHONE -->
                        <div class="form-group">
                            <label for="phone">Số điện thoại</label>
                            <div class="input-icon">
                                <i class="fas fa-phone"></i>
                                <input id="phone" type="tel" name="phone" value="{{ old('phone') }}"
                                    placeholder="0901234567" autocomplete="tel" maxlength="15">
                            </div>
                            @error('phone')
                                <div class="error-msg">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- EMAIL -->
                    <div class="form-group">
                        <label for="email">Email</label>
                        <div class="input-icon">
                            <i class="fas fa-envelope"></i>
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                placeholder="your@email.com" required autocomplete="username">
                        </div>
                        @error('email')
                            <div class="error-msg">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row-2">
                        <!-- PASSWORD -->
                        <div class="form-group">
                            <label for="password">Mật khẩu</label>
                            <div class="input-icon">
                                <i class="fas fa-lock"></i>
                                <input id="password" type="password" name="password"
                                    placeholder="••••••••" required autocomplete="new-password">
                                <button type="button" class="password-toggle toggle-pass" data-target="password" aria-label="Hiện mật khẩu">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="error-msg">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- PASSWORD CONFIRM -->
                        <div class="form-group">
                            <label for="password_confirmation">Xác nhận mật khẩu</label>
                            <div class="input-icon">
                                <i class="fas fa-shield-alt"></i>
                                <input id="password_confirmation" type="password" name="password_confirmation"
                                    placeholder="••••••••" required autocomplete="new-password">
                                <button type="button" class="password-toggle toggle-pass" data-target="password_confirmation" aria-label="Hiện mật khẩu">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- SUBMIT BUTTON -->
                    <button type="submit" class="btn-main">
                        <i class="fas fa-user-plus"></i> Đăng ký ngay
                    </button>
                </form>
            </div>

            <hr class="divider">

            <!-- FOOTER -->
            <footer class="auth-footer">
                Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a>
            </footer>

        </section>
    </main>

    <!-- JAVASCRIPT -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Hiện / Ẩn mật khẩu cho cả 2 ô mật khẩu
            const toggleButtons = document.querySelectorAll('.toggle-pass');
            toggleButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    const targetId = this.getAttribute('data-target');
                    const input = document.getElementById(targetId);
                    const icon = this.querySelector('i');

                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                        this.setAttribute('aria-label', 'Ẩn mật khẩu');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                        this.setAttribute('aria-label', 'Hiện mật khẩu');
                    }
                });
            });

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