<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ElectronicShop')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --blue: #1565C0;
            --blue-light: #1976D2;
            --blue-hover: #0D47A1;
            --red: #E53935;
            --green: #2E7D32;
            --text: #1a1a1a;
            --text-secondary: #555;
            --text-muted: #888;
            --border: #e0e0e0;
            --bg: #f5f5f5;
            --white: #ffffff;
            --radius: 8px;
            --shadow: 0 2px 8px rgba(0,0,0,0.08);
            --shadow-md: 0 4px 16px rgba(0,0,0,0.12);
        }
        body { font-family: 'Be Vietnam Pro', sans-serif; color: var(--text); background: var(--white); font-size: 14px; line-height: 1.6; }
        a { text-decoration: none; color: inherit; }
        ul { list-style: none; }
        img { max-width: 100%; display: block; }
        input, button, select, textarea { font-family: inherit; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 16px; }

        /* ===== HEADER ===== */
        .header-top {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            position: sticky; top: 0; z-index: 100;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }
        .header-top .inner {
            display: flex; align-items: center; gap: 16px;
            padding: 12px 16px; max-width: 1200px; margin: 0 auto;
        }
        .logo { font-size: 20px; font-weight: 800; color: var(--blue); white-space: nowrap; }
        .header-search {
            flex: 1; display: flex; max-width: 500px;
        }
        .header-search input {
            flex: 1; border: 1px solid var(--border); border-right: none;
            padding: 8px 14px; border-radius: 6px 0 0 6px; font-size: 14px; outline: none;
        }
        .header-search input:focus { border-color: var(--blue); }
        .header-search button {
            background: var(--blue); color: #fff; border: none;
            padding: 8px 16px; border-radius: 0 6px 6px 0; cursor: pointer;
        }
        .header-actions { display: flex; align-items: center; gap: 20px; margin-left: auto; }
        .header-actions a { display: flex; align-items: center; gap: 6px; font-size: 14px; color: var(--text); }
        .header-actions a:hover { color: var(--blue); }
        .cart-badge { position: relative; }
        .cart-badge .badge {
            position: absolute; top: -6px; right: -8px;
            background: var(--red); color: #fff; border-radius: 50%;
            font-size: 10px; width: 17px; height: 17px;
            display: flex; align-items: center; justify-content: center; font-weight: 700;
        }

        /* ===== NAV ===== */
        .header-nav {
            border-bottom: 1px solid var(--border); background: var(--white);
        }
        .header-nav .inner {
            display: flex; align-items: center; justify-content: space-between;
            max-width: 1200px; margin: 0 auto; padding: 0 16px;
        }
        .header-nav ul { display: flex; }
        .header-nav ul li a {
            display: block; padding: 12px 18px; font-size: 14px; font-weight: 500;
            color: var(--text); transition: color .2s;
        }
        .header-nav ul li a:hover, .header-nav ul li a.active { color: var(--blue); }
        .header-nav ul li a.active { border-bottom: 2px solid var(--blue); }
        .hotline { color: var(--red); font-weight: 700; font-size: 14px; }

        /* ===== FOOTER ===== */
        .footer { background: var(--white); border-top: 1px solid var(--border); margin-top: 48px; }
        .footer-trust {
            background: #f8f9fa; border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border); padding: 20px 0;
        }
        .footer-trust .inner {
            display: flex; justify-content: space-around; flex-wrap: wrap; gap: 12px;
            max-width: 1200px; margin: 0 auto; padding: 0 16px;
        }
        .trust-item { display: flex; align-items: center; gap: 10px; }
        .trust-item .icon { color: var(--blue); font-size: 22px; }
        .trust-item .label { font-weight: 600; font-size: 13px; }
        .trust-item .sub { font-size: 12px; color: var(--text-muted); }
        .footer-main { padding: 40px 0 20px; }
        .footer-main .inner {
            display: grid; grid-template-columns: 2fr 1.5fr 1.5fr 2fr; gap: 32px;
            max-width: 1200px; margin: 0 auto; padding: 0 16px;
        }
        .footer-logo { font-size: 20px; font-weight: 800; color: var(--text); margin-bottom: 12px; }
        .footer-info p { font-size: 13px; color: var(--text-secondary); margin-bottom: 6px; }
        .footer-socials { display: flex; gap: 10px; margin-top: 14px; }
        .footer-socials a {
            width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 15px;
        }
        .social-fb { background: #1877F2; }
        .social-yt { background: #FF0000; }
        .social-tt { background: #000; }
        .social-ig { background: linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888); }
        .footer-col h4 { font-size: 14px; font-weight: 700; margin-bottom: 14px; color: var(--text); }
        .footer-col ul li { margin-bottom: 8px; }
        .footer-col ul li a { font-size: 13px; color: var(--text-secondary); }
        .footer-col ul li a:hover { color: var(--blue); }
        .footer-newsletter p { font-size: 13px; color: var(--text-secondary); margin-bottom: 12px; }
        .newsletter-form { display: flex; gap: 0; }
        .newsletter-form input {
            flex: 1; border: 1px solid var(--border); padding: 8px 12px;
            border-radius: 6px 0 0 6px; font-size: 13px; outline: none;
        }
        .newsletter-form button {
            background: var(--blue); color: #fff; border: none;
            padding: 8px 14px; border-radius: 0 6px 6px 0; font-size: 12px; font-weight: 700; cursor: pointer;
        }
        .footer-bottom {
            border-top: 1px solid var(--border); padding: 14px 0;
            text-align: center; font-size: 12px; color: var(--text-muted);
        }

        /* ===== BREADCRUMB ===== */
        .breadcrumb { padding: 10px 0; font-size: 13px; color: var(--text-muted); }
        .breadcrumb a { color: var(--text-muted); }
        .breadcrumb a:hover { color: var(--blue); }
        .breadcrumb span { margin: 0 6px; }

        /* ===== BUTTONS ===== */
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; border: none; transition: all .2s; }
        .btn-primary { background: var(--blue); color: #fff; }
        .btn-primary:hover { background: var(--blue-hover); }
        .btn-outline { background: #fff; color: var(--blue); border: 2px solid var(--blue); }
        .btn-outline:hover { background: var(--blue); color: #fff; }
        .btn-lg { padding: 13px 28px; font-size: 15px; }

        /* ===== PRODUCT CARD ===== */
        .product-card {
            background: #fff; border: 1px solid var(--border); border-radius: var(--radius);
            overflow: hidden; transition: box-shadow .2s, transform .2s; position: relative;
        }
        .product-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
        .product-card .badge-tag {
            position: absolute; top: 10px; left: 10px; background: var(--red); color: #fff;
            font-size: 11px; font-weight: 700; padding: 2px 7px; border-radius: 4px; z-index:1;
        }
        .product-card .wish {
            position: absolute; top: 10px; right: 10px;
            background: #fff; border: 1px solid var(--border); width: 30px; height: 30px;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            color: var(--text-muted); cursor: pointer; z-index:1;
        }
        .product-card .wish:hover { color: var(--red); }
        .product-card-img {
            width: 100%; height: 180px; object-fit: cover;
            background: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #ccc;
        }
        .product-card-img img { width: 100%; height: 100%; object-fit: cover; }
        .product-card-body { padding: 12px; }
        .product-card-name { font-size: 13px; font-weight: 500; margin-bottom: 6px; color: var(--text); line-height: 1.4; }
        .product-card-price { color: var(--red); font-weight: 700; font-size: 15px; }
        .product-card-oldprice { color: var(--text-muted); font-size: 12px; text-decoration: line-through; margin-left: 6px; }
        .stars { color: #FFA000; font-size: 12px; }
        .review-count { color: var(--text-muted); font-size: 12px; margin-left: 4px; }

        /* ===== SECTION TITLE ===== */
        .section-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
        .section-title { font-size: 18px; font-weight: 800; text-transform: uppercase; display: flex; align-items: center; gap: 8px; }
        .section-title::before { content: ''; width: 4px; height: 22px; background: var(--blue); border-radius: 2px; display: inline-block; }
        .section-link { color: var(--blue); font-size: 13px; font-weight: 500; }

        /* ===== MISC ===== */
        .img-placeholder {
            background: #e8e8e8; display: flex; align-items: center; justify-content: center;
            color: #bbb; font-size: 12px;
        }
        .img-placeholder i { font-size: 24px; }
    </style>
    @stack('styles')
</head>
<body>

{{-- HEADER --}}
<header class="header-top">
    <div class="inner">
        <a href="{{ route('home') }}" class="logo">ElectronicShop</a>
        @if(isset($showSearch) && $showSearch)
        <form class="header-search" action="{{ route('products.index') }}" method="GET">
            <input type="text" name="q" placeholder="Bạn cần tìm gì?" value="{{ request('q') }}">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
        @endif
        <div class="header-actions">
            @auth
                <a href="{{ route('account.profile') }}"><i class="fas fa-user-circle fa-lg"></i> {{ Auth::user()->name }}</a>
            @else
                <a href="{{ route('login') }}"><i class="fas fa-user fa-lg"></i> Đăng nhập</a>
            @endauth
            <a href="{{ route('wishlist') }}"><i class="fas fa-heart fa-lg"></i> Yêu thích</a>
            <a href="{{ route('cart.index') }}" class="cart-badge">
                <i class="fas fa-shopping-cart fa-lg"></i> Giỏ hàng
                @if(session('cart_count', 0) > 0)
                <span class="badge">{{ session('cart_count') }}</span>
                @endif
            </a>
        </div>
    </div>
</header>
<nav class="header-nav">
    <div class="inner">
        <ul>
            <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Trang chủ</a></li>
            <li><a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">Sản phẩm</a></li>
            <li><a href="{{ route('news.index') }}" class="{{ request()->routeIs('news.*') ? 'active' : '' }}">Tin tức</a></li>
            <li><a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">Liên hệ</a></li>
        </ul>
        <span class="hotline">HOTLINE: 1900 1234</span>
    </div>
</nav>

{{-- CONTENT --}}
@yield('content')

{{-- FOOTER --}}
<footer class="footer">
    @hasSection('hide_trust')
    @else
    <div class="footer-trust">
        <div class="inner">
            <div class="trust-item">
                <span class="icon"><i class="fas fa-truck"></i></span>
                <div><div class="label">Giao hàng miễn phí</div><div class="sub">Đơn từ 500K</div></div>
            </div>
            <div class="trust-item">
                <span class="icon"><i class="fas fa-shield-alt"></i></span>
                <div><div class="label">Chính hãng 100%</div><div class="sub">Cam kết chất lượng</div></div>
            </div>
            <div class="trust-item">
                <span class="icon"><i class="fas fa-sync-alt"></i></span>
                <div><div class="label">Đổi trả dễ dàng</div><div class="sub">Trong 30 ngày</div></div>
            </div>
            <div class="trust-item">
                <span class="icon"><i class="fas fa-credit-card"></i></span>
                <div><div class="label">Trả góp 0%</div><div class="sub">Thủ tục đơn giản</div></div>
            </div>
            <div class="trust-item">
                <span class="icon"><i class="fas fa-headset"></i></span>
                <div><div class="label">Hỗ trợ 24/7</div><div class="sub">Hotline: 1900 1234</div></div>
            </div>
        </div>
    </div>
    @endif

    <div class="footer-main">
        <div class="inner">
            <div class="footer-info">
                <div class="footer-logo">ElectronicShop</div>
                <p>Địa chỉ: 123 Đường Nguyễn Văn Linh,<br>Quận Hải Châu, TP Đà Nẵng</p>
                <p>Điện thoại: 1900 1234 - Email: cskh@eletron.vn</p>
                <div class="footer-socials">
                    <a href="#" class="social-fb"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-yt"><i class="fab fa-youtube"></i></a>
                    <a href="#" class="social-tt"><i class="fab fa-tiktok"></i></a>
                    <a href="#" class="social-ig"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="footer-col">
                <h4>Hỗ trợ khách hàng</h4>
                <ul>
                    <li><a href="#">Hướng dẫn mua hàng</a></li>
                    <li><a href="#">Chính sách bảo hành</a></li>
                    <li><a href="#">Chính sách đổi trả</a></li>
                    <li><a href="#">Tra cứu đơn hàng</a></li>
                    <li><a href="#">Câu hỏi thường gặp</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Về chúng tôi</h4>
                <ul>
                    <li><a href="#">Giới thiệu ElectronicShop</a></li>
                    <li><a href="#">Tuyển dụng</a></li>
                    <li><a href="#">Hệ thống cửa hàng</a></li>
                    <li><a href="#">Chính sách bảo mật</a></li>
                    <li><a href="#">Liên hệ đối tác</a></li>
                </ul>
            </div>
            <div class="footer-newsletter">
                <h4>Đăng ký nhận tin</h4>
                <p>Đăng ký để nhận các chương trình khuyến mãi sớm nhất từ ElectronicShop.</p>
                <form class="newsletter-form" action="#" method="POST">
                    @csrf
                    <input type="email" placeholder="Email của bạn...">
                    <button type="submit">ĐĂNG KÝ</button>
                </form>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        © 2024 ELETRON SHOP. All rights reserved. Designed for Vietnamese users.
    </div>
</footer>

@stack('scripts')
</body>
</html>