<!DOCTYPE html>

<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name', 'ElectronicShop'))</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --brand-blue: #2563eb;
            --brand-red: #e53935;
        }

        body {
            font-family: "Segoe UI", Roboto, Arial, sans-serif;
        }

        /* ===== Header trên ===== */
        header.top-header {
            background: #fff;
            padding: 1rem 0;
        }

        .navbar-brand {
            font-size: 1.7rem;
            font-weight: 800;
            color: var(--brand-blue) !important;
            letter-spacing: -0.5px;
            text-decoration: none;
        }

        /* ===== Thanh tìm kiếm ===== */
        .search-wrapper {
            background: #f1f3f5;
            border-radius: 14px;
            display: flex;
            align-items: center;
            padding: 4px;
            flex-grow: 1;
        }

        .search-wrapper input {
            background: transparent;
            border: none;
            outline: none;
            flex-grow: 1;
            padding: 0.7rem 1.1rem;
            font-size: 0.95rem;
            color: #333;
        }

        .search-wrapper input::placeholder {
            color: #9aa0a6;
        }

        .search-wrapper .btn-search {
            background: var(--brand-blue);
            color: #fff;
            border: none;
            border-radius: 12px;
            width: 46px;
            height: 46px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.1rem;
            transition: background 0.2s;
        }

        .search-wrapper .btn-search:hover {
            background: #1d4fd7;
        }

        /* ===== Icon tài khoản / giỏ hàng ===== */
        .nav-icon-link {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            color: #333;
            font-size: 0.95rem;
            font-weight: 500;
            text-decoration: none;
            white-space: nowrap;
        }

        .nav-icon-link:hover {
            color: var(--brand-blue);
        }

        .nav-icon-link i {
            font-size: 1.4rem;
        }

        .icon-wrap {
            position: relative;
            display: inline-flex;
        }

        .cart-badge {
            position: absolute;
            top: -6px;
            right: -8px;
            background: var(--brand-red);
            color: #fff;
            font-size: 0.65rem;
            min-width: 18px;
            height: 18px;
            padding: 0 3px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        /* ===== Menu phụ + Hotline ===== */
        .sub-nav {
            background: #fff;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
            padding: 0.7rem 0;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .nav-links a {
            color: #333;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .nav-links a:hover {
            color: var(--brand-blue);
        }

        .hotline {
            color: var(--brand-red);
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 0.3px;
        }

        /* ===== FOOTER ===== */

        .footer {
            background: #fff;
            padding: 30px 15px 0;
            color: #555;
            font-family: "Segoe UI", Roboto, Arial, sans-serif;
        }

        .footer-container {
            display: grid;
            grid-template-columns: 1.2fr 1fr 1fr 1fr;
            gap: 35px;
            padding-bottom: 40px;
        }

        /* Tiêu đề */
        .footer h2 {
            color: #111;
            font-size: 26px;
            font-weight: 700;
            margin: 0 0 25px;
        }

        .footer h3 {
            color: #111;
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 25px;
        }

        /* Nội dung */
        .footer p {
            font-size: 15px;
            line-height: 1.6;
            color: #555;
            margin: 0 0 15px;
        }

        /* Link */
        .footer-column a {
            display: block;
            color: #666;
            text-decoration: none;
            font-size: 15px;
            margin-bottom: 12px;
            transition: .3s;
        }

        .footer-column a:hover {
            color: var(--brand-blue);
        }

        /* Social */
        .socials {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .socials a {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 20px;
        }

        /* Newsletter */
        .newsletter p {
            max-width: 280px;
        }

        .email-box {
            display: flex;
            margin-top: 15px;
        }

        .email-box input {
            height: 48px;
            width: 220px;
            border: none;
            background: #7bbaf0;
            padding: 0 15px;
            font-size: 14px;
            outline: none;
        }

        .email-box input::placeholder {
            color: #111;
        }

        .email-box button {
            width: 90px;
            height: 48px;
            border: none;
            background: var(--brand-blue);
            color: #111;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        /* Copyright */
        .footer-bottom {
            border-top: 1px solid #cfe8ff;
            padding: 20px 0;
            font-size: 14px;
            color: #777;
        }

        /* Mạng xã hội */
        .socials {
            display: flex;
            gap: 16px;
            margin-top: 24px;
        }

        .socials a {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 18px;
            transition: all .3s ease;
        }

        .socials a:hover {
            transform: translateY(-2px);
        }

        .facebook {
            background: #1877f2;
        }

        .youtube {
            background: #ff0000;
        }

        .tiktok {
            background: #000;
        }

        .instagram {
            background: linear-gradient(45deg, #feda75, #d62976, #4f5bd5);
        }

        @media(max-width:900px) {
            .footer-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <header class="top-header">
        <div class="container">
            <div class="d-flex align-items-center gap-4">
                <a href="{{ url('/') }}" class="navbar-brand flex-shrink-0">
                    ElectronicShop
                </a>
                <form class="search-wrapper" action="{{ route('products.index') }}" method="GET">
                    <input
                        type="text"
                        name="q"
                        placeholder="Tìm kiếm sản phẩm, thương hiệu..."
                        value="{{ request('q') }}"
                        autocomplete="off">
                    <button type="submit" class="btn-search">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
                <div class="d-flex align-items-center gap-4 flex-shrink-0">
                    @auth
                    <a href="{{ route('login') }}" class="nav-icon-link">
                        <i class="bi bi-person"></i> Tài khoản
                    </a>
                    @else
                    <a href="{{ route('login') }}" class="nav-icon-link">
                        <i class="bi bi-person"></i> Đăng nhập
                    </a>
                    @endauth
                    <a href="{{ route('cart.index') }}" class="nav-icon-link">
                        <span class="icon-wrap">
                            <i class="bi bi-cart3"></i>
                            @php
                            $cartCount = session('cart')
                            ? count(session('cart'))
                            : 0;
                            @endphp
                            <span class="cart-badge">{{ $cartCount }}</span>
                        </span>
                        Giỏ hàng
                    </a>
                </div>
            </div>
        </div>
    </header>
    <nav class="sub-nav">
        <div class="container d-flex justify-content-between align-items-center">
            <ul class="nav-links">
                <li><a href="{{ url('/') }}">Trang chủ</a></li>
                <li><a href="{{ route('products.index') }}">Sản phẩm</a></li>
                <li><a href="{{ route('news.index') }}">Tin tức</a></li>
                <li><a href="{{route('news.index')}}">Liên hệ</a></li>
            </ul>
            <div class="hotline">
                HOTLINE: 1900 1234
            </div>
        </div>
    </nav>
    <main>
        @stack('styles')
        @yield('content')
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
<footer class="footer">
    <div class="footer-container">
        <!-- Cột 1 -->
        <div class="footer-column footer-brand">
            <h2>ElectronShop</h2>
            <p>
                Địa chỉ: 123 Đường Nguyễn Văn Linh,<br>
                Quận Hải Châu, TP Đà Nẵng<br>
                Điện thoại: 1900 1234<br>
                Email: electronshop@gmail.com
            </p>
            <div class="socials">
                <a class="facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a class="youtube">
                    <i class="fab fa-youtube"></i>
                </a>
                <a class="tiktok">
                    <i class="fab fa-tiktok"></i>
                </a>
                <a class="instagram">
                    <i class="fab fa-instagram"></i>
                </a>
            </div>
        </div>
        <!-- Cột 2 -->
        <div class="footer-column">
            <h3>Hỗ trợ khách hàng</h3>
            <a>Hướng dẫn mua hàng</a>
            <a>Chính sách bảo hành</a>
            <a>Chính sách đổi trả</a>
            <a>Tra cứu đơn hàng</a>
            <a>Câu hỏi thường gặp</a>
        </div>
        <!-- Cột 3 -->
        <div class="footer-column">
            <h3>Về chúng tôi</h3>
            <a>Giới thiệu ElectronShop</a>
            <a>Tuyển dụng</a>
            <a>Hệ thống cửa hàng</a>
            <a>Chính sách bảo mật</a>
            <a>Liên hệ đối tác</a>
        </div>
        <!-- Cột 4 -->
        <div class="footer-column newsletter">
            <h3>Đăng ký nhận tin</h3>
            <p>
                Đăng ký để nhận các chương trình khuyến mãi
                sớm nhất từ ElectronShop.
            </p>
            <div class="email-box">
                <input type="email" placeholder="Email của bạn...">
                <button>
                    ĐĂNG<br>
                    KÝ
                </button>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        © 2026 ELETRON SHOP. All rights reserved. Designed for Vietnamese users.
    </div>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</footer>

</html>