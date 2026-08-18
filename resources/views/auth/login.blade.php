<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Đăng nhập – ElectronicShop</title>

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


        /* =========================================================
           EXTRA DARK LAYER
        ========================================================= */

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
    max-width: 560px;
    padding: 35px 18px;
    position: relative;
    z-index: 2;
}


        @keyframes cardEnter {

            from {
                opacity: 0;
                transform: translateY(25px) scale(.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }


        /* =========================================================
           GLASS CARD
        ========================================================= */

        .auth-card {
    position: relative;
    overflow: hidden;

    border-radius: 28px;

    background: rgba(255, 255, 255, 0.055);

    border: 1px solid rgba(255, 255, 255, 0.20);

    box-shadow:
        0 30px 80px rgba(0, 0, 0, 0.45),
        inset 0 1px 1px rgba(255, 255, 255, 0.18);

    backdrop-filter: blur(18px) saturate(125%);
    -webkit-backdrop-filter: blur(18px) saturate(125%);
}


        /* Glass shine */

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

            padding: 42px 42px 25px;
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

            filter:
                drop-shadow(0 8px 20px rgba(255, 255, 255, .16));
        }


        .sm-logo img {
            display: block;

            width: 250px;
            max-width: 80vw;

            max-height: 105px;

            height: auto;

            object-fit: contain;

            object-position: center;

            /*
             * Nếu logo gốc đã màu trắng/xám:
             * không cần filter.
             */

            filter:
                drop-shadow(
                    0 5px 18px
                    rgba(0, 0, 0, .25)
                );
        }


        /* =========================================================
           TITLE
        ========================================================= */

        .auth-header h1 {
            margin-top: 22px;

            color: var(--text);

            font-size: 30px;

            line-height: 1.2;

            font-weight: 800;

            letter-spacing: -.04em;
        }


        .auth-header p {
            margin-top: 9px;

            color: var(--text-soft);

            font-size: 14px;

            line-height: 1.6;

            font-weight: 500;
        }


        /* =========================================================
           BODY
        ========================================================= */

        .auth-body {
            padding: 18px 42px 34px;
        }


        /* =========================================================
           FORM GROUP
        ========================================================= */

        .form-group {
            margin-bottom: 19px;
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
           INPUT WRAPPER
        ========================================================= */

        .input-icon {
            position: relative;

            width: 100%;
        }


        /* =========================================================
           INPUT ICON
        ========================================================= */

        .input-icon > i {
            position: absolute;

            left: 17px;
            top: 50%;

            transform: translateY(-50%);

            color: rgba(255, 255, 255, .52);

            font-size: 15px;

            z-index: 2;

            pointer-events: none;

            transition:
                color .2s var(--ease);
        }

        /* =========================================================
   ẨN ICON MẮT MẶC ĐỊNH CỦA TRÌNH DUYỆT (EDGE / CHROME / SAFARI)
========================================================= */
input::-ms-reveal,
input::-ms-clear {
    display: none !important;
}

input::-webkit-contacts-auto-fill-button,
input::-webkit-credentials-auto-fill-button {
    visibility: hidden;
    display: none !important;
    pointer-events: none;
}

        /* =========================================================
           INPUT
        ========================================================= */

        .input-icon input {
            width: 100%;

            height: 56px;

            padding:
                0 48px 0 46px;

            border:
                1px solid var(--border);

            border-radius: 14px;

            outline: none;

            background:
                rgba(255, 255, 255, .075);

            color: #ffffff;

            font-family: inherit;

            font-size: 14px;

            font-weight: 500;

            box-shadow:
                inset 0 1px 1px rgba(255, 255, 255, .04);

            backdrop-filter: blur(12px);

            -webkit-backdrop-filter: blur(12px);

            transition:
                background .25s var(--ease),
                border-color .25s var(--ease),
                box-shadow .25s var(--ease),
                transform .25s var(--ease);
        }


        /* Placeholder */

        .input-icon input::placeholder {
            color: rgba(255, 255, 255, .40);

            font-weight: 400;
        }


        /* Hover */

        .input-icon input:hover {
            background:
                rgba(255, 255, 255, .105);

            border-color:
                var(--border-hover);
        }


        /* Focus */

        .input-icon input:focus {
            background:
                rgba(255, 255, 255, .135);

            border-color:
                var(--border-focus);

            box-shadow:
                0 0 0 4px rgba(255, 255, 255, .065),
                0 10px 30px rgba(0, 0, 0, .12);
        }


        .input-icon:focus-within > i {
            color: rgba(255, 255, 255, .88);
        }


        /* =========================================================
           PASSWORD TOGGLE
        ========================================================= */

        .password-toggle {
            position: absolute;

            right: 16px;
            top: 50%;

            transform: translateY(-50%);

            width: 30px;
            height: 30px;

            display: flex;

            align-items: center;
            justify-content: center;

            border: 0;

            background: transparent;

            color: rgba(255, 255, 255, .45);

            cursor: pointer;

            border-radius: 8px;

            transition:
                color .2s,
                background .2s;
        }


        .password-toggle:hover {
            color: rgba(255, 255, 255, .9);

            background:
                rgba(255, 255, 255, .08);
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
           FORGOT PASSWORD
        ========================================================= */

        .forgot-link {
            text-align: right;

            margin-top: 9px;
        }


        .forgot-link a {
            color:
                rgba(255, 255, 255, .64);

            font-size: 12px;

            font-weight: 600;

            text-decoration: none;

            transition:
                color .2s,
                opacity .2s;
        }


        .forgot-link a:hover {
            color:
                rgba(255, 255, 255, .96);

            text-decoration: underline;
        }


        /* =========================================================
           REMEMBER CHECKBOX
        ========================================================= */

        .form-check {
            display: flex !important;

            align-items: center;

            gap: 9px;

            margin: 0 !important;

            color:
                rgba(255, 255, 255, .66) !important;

            font-size: 12px !important;

            font-weight: 500 !important;

            cursor: pointer;
        }


        .form-check input {
            appearance: none;

            -webkit-appearance: none;

            width: 17px;
            height: 17px;

            flex: 0 0 17px;

            margin: 0;

            border:
                1px solid rgba(255, 255, 255, .30);

            border-radius: 5px;

            background:
                rgba(255, 255, 255, .07);

            cursor: pointer;

            position: relative;

            transition:
                background .2s,
                border-color .2s;
        }


        .form-check input:checked {
            background:
                rgba(255, 255, 255, .90);

            border-color:
                rgba(255, 255, 255, .90);
        }


        .form-check input:checked::after {
            content: "";

            position: absolute;

            left: 5px;
            top: 2px;

            width: 4px;
            height: 8px;

            border:
                solid #111;

            border-width:
                0 2px 2px 0;

            transform: rotate(45deg);
        }


        /* =========================================================
           BUTTON
        ========================================================= */

        .btn-main {
            width: 100%;

            height: 56px;

            margin-top: 3px;

            border:
                1px solid rgba(255, 255, 255, .24);

            border-radius: 14px;

            background:
                linear-gradient(
                    135deg,
                    rgba(255, 255, 255, .21),
                    rgba(255, 255, 255, .09)
                );

            color: #ffffff;

            font-family: inherit;

            font-size: 14px;

            font-weight: 800;

            letter-spacing: -.01em;

            cursor: pointer;

            box-shadow:
                0 12px 30px rgba(0, 0, 0, .20),
                inset 0 1px 1px rgba(255, 255, 255, .13);

            backdrop-filter: blur(12px);

            -webkit-backdrop-filter: blur(12px);

            transition:
                transform .22s var(--ease),
                background .22s var(--ease),
                border-color .22s var(--ease),
                box-shadow .22s var(--ease);
        }


        .btn-main:hover {
            transform: translateY(-2px);

            background:
                linear-gradient(
                    135deg,
                    rgba(255, 255, 255, .29),
                    rgba(255, 255, 255, .13)
                );

            border-color:
                rgba(255, 255, 255, .38);

            box-shadow:
                0 16px 38px rgba(0, 0, 0, .28),
                inset 0 1px 1px rgba(255, 255, 255, .18);
        }


        .btn-main:active {
            transform: translateY(0);
        }


        .btn-main i {
            margin-right: 7px;
        }


        /* =========================================================
           STATUS
        ========================================================= */

        .alert-status {
            margin-bottom: 18px;

            padding: 12px 14px;

            border:
                1px solid rgba(255, 255, 255, .18);

            border-radius: 12px;

            background:
                rgba(255, 255, 255, .08);

            color:
                rgba(255, 255, 255, .82);

            font-size: 12px;

            line-height: 1.5;

            backdrop-filter: blur(12px);

            -webkit-backdrop-filter: blur(12px);
        }


        /* =========================================================
           DIVIDER
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


        /* =========================================================
           FOOTER
        ========================================================= */

        .auth-footer {
            padding:
                23px 42px 30px;

            text-align: center;

            color:
                rgba(255, 255, 255, .54);

            font-size: 12px;

            font-weight: 500;
        }


        .auth-footer a {
            color:
                rgba(255, 255, 255, .92);

            font-weight: 800;

            text-decoration: none;

            transition:
                color .2s,
                opacity .2s;
        }


        .auth-footer a:hover {
            color: #ffffff;

            text-decoration: underline;
        }


        /* =========================================================
           VIDEO LOADING FALLBACK
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
           RESPONSIVE - TABLET
        ========================================================= */

        @media (max-width: 700px) {

            .auth-wrap {
                max-width: 520px;

                padding:
                    25px 15px;
            }

            .auth-header {
                padding:
                    36px 30px 22px;
            }

            .auth-body {
                padding:
                    16px 30px 30px;
            }

            .divider {
                margin:
                    0 30px;
            }

            .auth-footer {
                padding:
                    21px 30px 28px;
            }
        }


        /* =========================================================
           RESPONSIVE - MOBILE
        ========================================================= */

        @media (max-width: 600px) {

            body {
                align-items: center;
            }


            /* Đổi video desktop -> video dọc */

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
                width: 100%;

                padding:
                    18px 13px;
            }


            .auth-card {
                border-radius: 23px;
            }


            .auth-header {
                padding:
                    32px 24px 20px;
            }


            .sm-logo img {
                width: 210px;

                max-height: 85px;
            }


            .auth-header h1 {
                margin-top: 19px;

                font-size: 26px;
            }


            .auth-header p {
                font-size: 13px;

                margin-top: 8px;
            }


            .auth-body {
                padding:
                    16px 24px 28px;
            }


            .input-icon input {
                height: 54px;
            }


            .btn-main {
                height: 54px;
            }


            .divider {
                margin:
                    0 24px;
            }


            .auth-footer {
                padding:
                    20px 24px 27px;
            }
        }


        /* =========================================================
           SMALL MOBILE
        ========================================================= */

        @media (max-width: 380px) {

            .auth-header {
                padding:
                    28px 20px 18px;
            }


            .auth-body {
                padding:
                    14px 20px 25px;
            }


            .divider {
                margin:
                    0 20px;
            }


            .auth-footer {
                padding:
                    19px 20px 24px;
            }


            .sm-logo img {
                width: 185px;
            }


            .auth-header h1 {
                font-size: 24px;
            }
        }


        /* =========================================================
           REDUCE MOTION
        ========================================================= */

        @media (prefers-reduced-motion: reduce) {

            .auth-wrap {
                animation: none;
            }

            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>


<body>

    <!-- =========================================================
         VIDEO FALLBACK
    ========================================================== -->

    <div class="video-fallback"></div>


    <!-- =========================================================
         DESKTOP VIDEO
         File: public/videos/16.9.mp4
    ========================================================== -->

    <video
        autoplay
        muted
        loop
        playsinline
        preload="auto"
        class="video-bg"
        aria-hidden="true">

        <source
            src="{{ asset('videos/16.9.mp4') }}"
            type="video/mp4">

    </video>


    <!-- =========================================================
         MOBILE VIDEO
         File: public/videos/9.16.mp4
    ========================================================== -->

    <video
        autoplay
        muted
        loop
        playsinline
        preload="auto"
        class="video-bg-mobile"
        aria-hidden="true">

        <source
            src="{{ asset('videos/9.16.mp4') }}"
            type="video/mp4">

    </video>


    <!-- =========================================================
         DARK OVERLAY
    ========================================================== -->

    <div class="video-overlay"></div>


    <!-- =========================================================
         LOGIN
    ========================================================== -->

    <main class="auth-wrap">

        <section class="auth-card">

            <!-- =================================================
                 HEADER
            ================================================== -->

            <header class="auth-header">

                <a
                    href="{{ route('home') }}"
                    class="sm-logo"
                    aria-label="Electronics Shop">

                    <img
                        src="{{ asset('images/logo.png') }}"
                        alt="Electronics Shop">

                </a>


                <h1>Đăng nhập</h1>

                <p>
                    Đăng nhập vào tài khoản của bạn
                </p>

            </header>


            <!-- =================================================
                 FORM BODY
            ================================================== -->

            <div class="auth-body">

                {{-- Session status --}}

                @if(session('status'))

                    <div class="alert-status">
                        {{ session('status') }}
                    </div>

                @endif


                <form
                    method="POST"
                    action="{{ route('login') }}">

                    @csrf


                    <!-- =========================================
                         EMAIL
                    ========================================== -->

                    <div class="form-group">

                        <label for="email">
                            Email
                        </label>


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

                            <div class="error-msg">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    <!-- =========================================
                         PASSWORD
                    ========================================== -->

                    <div class="form-group">

                        <label for="password">
                            Mật khẩu
                        </label>


                        <div class="input-icon">

                            <i class="fas fa-lock"></i>


                            <input
                                id="password"
                                type="password"
                                name="password"
                                placeholder="••••••••"
                                required
                                autocomplete="current-password">


                            <button
                                type="button"
                                class="password-toggle"
                                id="togglePassword"
                                aria-label="Hiện mật khẩu">

                                <i class="fas fa-eye"></i>

                            </button>

                        </div>


                        @error('password')

                            <div class="error-msg">
                                {{ $message }}
                            </div>

                        @enderror


                        <div class="forgot-link">

                            <a
                                href="{{ route('password.request') }}">

                                Quên mật khẩu?

                            </a>

                        </div>

                    </div>


                    <!-- =========================================
                         REMEMBER
                    ========================================== -->

                    <div class="form-group">

                        <label class="form-check">

                            <input
                                type="checkbox"
                                name="remember"
                                value="1"
                                {{ old('remember') ? 'checked' : '' }}>

                            <span>
                                Ghi nhớ đăng nhập
                            </span>

                        </label>

                    </div>


                    <!-- =========================================
                         LOGIN BUTTON
                    ========================================== -->

                    <button
                        type="submit"
                        class="btn-main">

                        <i class="fas fa-sign-in-alt"></i>

                        Đăng nhập

                    </button>

                </form>

            </div>


            <!-- =================================================
                 DIVIDER
            ================================================== -->

            <hr class="divider">


            <!-- =================================================
                 FOOTER
            ================================================== -->

            <footer class="auth-footer">

                Chưa có tài khoản?

                <a href="{{ route('register') }}">
                    Đăng ký ngay
                </a>

            </footer>

        </section>

    </main>


    <!-- =========================================================
         JAVASCRIPT
    ========================================================== -->

    <script>

        document.addEventListener('DOMContentLoaded', function () {

            /*
             * ================================================
             * HIỆN / ẨN MẬT KHẨU
             * ================================================
             */

            const passwordInput =
                document.getElementById('password');

            const togglePassword =
                document.getElementById('togglePassword');


            if (passwordInput && togglePassword) {

                togglePassword.addEventListener('click', function () {

                    const icon =
                        this.querySelector('i');


                    if (passwordInput.type === 'password') {

                        passwordInput.type = 'text';

                        icon.classList.remove('fa-eye');

                        icon.classList.add('fa-eye-slash');

                        this.setAttribute(
                            'aria-label',
                            'Ẩn mật khẩu'
                        );

                    } else {

                        passwordInput.type = 'password';

                        icon.classList.remove('fa-eye-slash');

                        icon.classList.add('fa-eye');

                        this.setAttribute(
                            'aria-label',
                            'Hiện mật khẩu'
                        );
                    }

                });

            }


            /*
             * ================================================
             * VIDEO AUTOPLAY FALLBACK
             * ================================================
             */

            const videos =
                document.querySelectorAll('video');


            videos.forEach(function (video) {

                video.play().catch(function () {

                    /*
                     * Một số trình duyệt có thể chặn autoplay.
                     * Video vẫn sẽ hoạt động khi người dùng
                     * tương tác với trang.
                     */

                    const startVideo = function () {

                        video.play().catch(function () {});

                    };

                    document.addEventListener(
                        'click',
                        startVideo,
                        {
                            once: true
                        }
                    );

                    document.addEventListener(
                        'touchstart',
                        startVideo,
                        {
                            once: true
                        }
                    );

                });

            });

        });

    </script>

</body>

</html>