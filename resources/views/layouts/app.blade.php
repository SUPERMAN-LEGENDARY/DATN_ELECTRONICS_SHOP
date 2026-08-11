<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#000000">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'ElectronicShop'))</title>

    {{-- CSS Dependencies --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    {{-- Fonts: Manrope (heading, gần SamsungSharpSans) + Inter (body, gần SamsungOne) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* ============================================================
           SAMSUNG-STYLE DESIGN TOKENS
           ============================================================ */
        :root {
            --sm-black:  #000000;
            --sm-ink:    #121212;
            --sm-gray:   #545454;   /* body text */
            --sm-line:   #dcdcdc;   /* hairline */
            --sm-surface:#f7f7f7;   /* section bg */
            --sm-white:  #ffffff;
            --sm-blue:   #2189ff;   /* accent / eyebrow */
            --sm-blue-d: #1428a0;   /* ElectronicShop deep blue */
            --sm-radius: 24px;
            --sm-ease:   cubic-bezier(.25,.46,.45,.94);
            --sm-header-h: 56px;
        }

        * { -webkit-tap-highlight-color: transparent; }

        html { background: var(--sm-white); scroll-behavior: smooth; }

        body {
            font-family: 'Inter', 'Segoe UI', Roboto, Arial, sans-serif;
            background: var(--sm-white);
            color: var(--sm-ink);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            -webkit-font-smoothing: antialiased;
            letter-spacing: -0.01em;
        }

        h1, h2, h3, .sm-display {
            font-family: 'Manrope', 'Inter', sans-serif;
            font-weight: 800;
            letter-spacing: -0.035em;
            color: var(--sm-black);
        }

        img { max-width: 100%; height: auto; }

        main { flex: 1; position: relative; z-index: 1; }

        a { text-decoration: none; }

        ::selection { background: var(--sm-black); color: #fff; }

        /* ===== Scrollbar (mảnh, kiểu Samsung) ===== */
        ::-webkit-scrollbar { width: 10px; height: 10px; }
        ::-webkit-scrollbar-track { background: #f2f2f2; }
        ::-webkit-scrollbar-thumb { background: #c9c9c9; border-radius: 10px; border: 3px solid #f2f2f2; }
        ::-webkit-scrollbar-thumb:hover { background: #9a9a9a; }

        /* ============================================================
           1. UTILITY BAR (Hỗ trợ / For Business)
           ============================================================ */
        .sm-utility {
            background: var(--sm-white);
            border-bottom: 1px solid rgba(0,0,0,.06);
            font-size: 12px;
            font-weight: 600;
        }
        .sm-utility .inner {
            max-width: 1440px; margin: 0 auto; padding: 7px 24px;
            display: flex; justify-content: flex-end; align-items: center; gap: 22px;
        }
        .sm-utility a { color: var(--sm-gray); transition: color .2s var(--sm-ease); }
        .sm-utility a:hover { color: var(--sm-black); }
        .sm-utility .hot { color: #d0021b; }

        /* ============================================================
           2. MAIN HEADER (sticky, ẩn khi cuộn xuống – hiện khi cuộn lên)
           ============================================================ */
        header.sm-header {
            position: sticky; top: 0; z-index: 500;
            background: rgba(255,255,255,.92);
            backdrop-filter: saturate(180%) blur(14px);
            -webkit-backdrop-filter: saturate(180%) blur(14px);
            border-bottom: 1px solid rgba(0,0,0,.07);
            transition: transform .45s var(--sm-ease), box-shadow .3s var(--sm-ease);
            will-change: transform;
        }
        header.sm-header.is-hidden { transform: translateY(-100%); }
        header.sm-header.is-scrolled { box-shadow: 0 2px 18px rgba(0,0,0,.06); }

        .sm-header .inner {
            max-width: 1440px; margin: 0 auto; padding: 0 24px;
            height: var(--sm-header-h);
            display: flex; align-items: center; gap: 34px;
        }

        /* Logo trong khung viền – y hệt logo SAMSUNG */
        .sm-logo {
            display: inline-flex; align-items: center; justify-content: center;
            border: 1.5px solid var(--sm-black);
            padding: 5px 12px;
            font-family: 'Manrope', sans-serif;
            font-weight: 800; font-size: 14px; letter-spacing: .14em;
            color: var(--sm-black); text-transform: uppercase;
            white-space: nowrap; flex-shrink: 0;
            transition: background .25s var(--sm-ease), color .25s var(--sm-ease);
        }
        .sm-logo:hover { background: var(--sm-black); color: #fff; }

        /* Nav chính */
        .sm-nav { display: flex; align-items: center; gap: 30px; margin: 0; padding: 0; list-style: none; flex: 1; }
        .sm-nav > li { position: relative; }
        .sm-nav > li > a {
            display: block; padding: 17px 0;
            font-size: 14px; font-weight: 600; color: var(--sm-ink);
            position: relative;
        }
        .sm-nav > li > a::after {
            content: ''; position: absolute; left: 0; right: 0; bottom: 8px;
            height: 2px; background: var(--sm-black);
            transform: scaleX(0); transform-origin: center;
            transition: transform .32s var(--sm-ease);
        }
        .sm-nav > li > a:hover::after,
        .sm-nav > li > a.active::after { transform: scaleX(1); }
        .sm-nav > li > a.active { font-weight: 700; }

        /* Icon phải */
        .sm-actions { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }
        .sm-icon-btn {
            width: 40px; height: 40px; border: none; background: transparent;
            display: inline-flex; align-items: center; justify-content: center;
            color: var(--sm-black); font-size: 19px; border-radius: 50%;
            position: relative; cursor: pointer;
            transition: background .22s var(--sm-ease), transform .22s var(--sm-ease);
        }
        .sm-icon-btn:hover { background: rgba(0,0,0,.06); transform: translateY(-1px); }
        .sm-cart-badge {
            position: absolute; top: 4px; right: 3px;
            min-width: 17px; height: 17px; padding: 0 4px;
            background: #d0021b; color: #fff;
            font-size: 10px; font-weight: 800; line-height: 17px;
            border-radius: 999px; text-align: center;
            box-shadow: 0 0 0 2px #fff;
        }

        /* NOTIFICATION BELL */
        .notif-wrap { position: relative; }
        .notif-badge {
            position: absolute; top: 4px; right: 3px;
            min-width: 17px; height: 17px; padding: 0 4px;
            background: #d0021b; color: #fff;
            font-size: 10px; font-weight: 800; line-height: 17px;
            border-radius: 999px; text-align: center;
            box-shadow: 0 0 0 2px #fff; display: none;
            animation: badgePop .35s cubic-bezier(.34,1.56,.64,1);
        }
        @keyframes badgePop { 0% { transform: scale(0); } 100% { transform: scale(1); } }
        .notif-dropdown {
            position: absolute; top: calc(100% + 14px); right: -10px; width: 360px;
            background: rgba(255,255,255,.97); backdrop-filter: blur(20px);
            border: 1px solid rgba(0,0,0,.08); border-radius: 18px;
            box-shadow: 0 16px 48px rgba(0,0,0,.14); z-index: 700; overflow: hidden;
            opacity: 0; visibility: hidden; transform: translateY(-10px) scale(.97);
            transition: opacity .25s cubic-bezier(.16,1,.3,1), transform .25s cubic-bezier(.16,1,.3,1), visibility .25s;
        }
        .notif-dropdown.open { opacity: 1; visibility: visible; transform: translateY(0) scale(1); }
        .notif-dropdown::before {
            content: ''; position: absolute; top: -7px; right: 18px;
            width: 14px; height: 14px; background: rgba(255,255,255,.97);
            border: 1px solid rgba(0,0,0,.08); transform: rotate(45deg);
            border-bottom: none; border-right: none;
        }
        .notif-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 18px 12px; border-bottom: 1px solid #f0f0f0; }
        .notif-header h4 { font-size: 15px; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 7px; }
        .notif-header h4 i { color: #3b82f6; }
        .notif-mark-all { font-size: 12px; font-weight: 600; color: #3b82f6; border: none; background: none; cursor: pointer; padding: 4px 8px; border-radius: 6px; transition: background .2s; }
        .notif-mark-all:hover { background: #eff6ff; }
        .notif-list { max-height: 380px; overflow-y: auto; scrollbar-width: thin; }
        .notif-item { display: flex; gap: 12px; align-items: flex-start; padding: 13px 18px; cursor: pointer; transition: background .18s; border-bottom: 1px solid #f8fafc; position: relative; }
        .notif-item:hover { background: #f8fafc; }
        .notif-item.unread { background: #eff6ff; }
        .notif-item.unread:hover { background: #dbeafe; }
        .notif-item-img { width: 46px; height: 46px; border-radius: 10px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden; }
        .notif-item-img img { width: 100%; height: 100%; object-fit: cover; }
        .notif-item-img i { font-size: 20px; color: #94a3b8; }
        .notif-item-content { flex: 1; min-width: 0; }
        .notif-item-title { font-size: 13px; font-weight: 600; color: #1e293b; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 4px; }
        .notif-item-body { font-size: 12px; color: #64748b; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .notif-item-time { font-size: 11px; color: #94a3b8; margin-top: 5px; display: flex; align-items: center; gap: 4px; }
        .notif-dot { width: 8px; height: 8px; border-radius: 50%; background: #3b82f6; flex-shrink: 0; margin-top: 6px; }
        .notif-empty { padding: 40px 20px; text-align: center; color: #94a3b8; }
        .notif-empty i { font-size: 36px; margin-bottom: 10px; display: block; }
        .notif-empty p { font-size: 13px; margin: 0; }
        .notif-footer { padding: 10px 18px; border-top: 1px solid #f0f0f0; text-align: center; }
        .notif-footer a { font-size: 13px; font-weight: 600; color: #3b82f6; }
        .notif-footer a:hover { text-decoration: underline; }

        /* Menu hamburger (mobile) */
        .sm-burger { display: none; }

        /* ============================================================
           3. SEARCH OVERLAY (mở rộng từ trên xuống)
           ============================================================ */
        .sm-search-panel {
            position: fixed; inset: 0 0 auto 0; z-index: 600;
            background: #fff;
            border-bottom: 1px solid var(--sm-line);
            transform: translateY(-100%);
            transition: transform .5s var(--sm-ease);
            padding: 0 24px;
        }
        .sm-search-panel.open { transform: translateY(0); }
        .sm-search-panel .wrap {
            max-width: 900px; margin: 0 auto; padding: 34px 0 30px;
        }
        .sm-search-form {
            display: flex; align-items: center; gap: 12px;
            border-bottom: 2px solid var(--sm-black); padding-bottom: 12px;
        }
        .sm-search-form i { font-size: 20px; }
        .sm-search-form input {
            flex: 1; border: none; outline: none; background: transparent;
            font-family: 'Manrope', sans-serif;
            font-size: clamp(20px, 3vw, 30px); font-weight: 700;
            letter-spacing: -.03em; color: var(--sm-black);
        }
        .sm-search-form input::placeholder { color: #b5b5b5; }
        .sm-search-close {
            border: none; background: transparent; font-size: 22px; cursor: pointer; color: var(--sm-black);
        }
        .sm-search-tags { display: flex; flex-wrap: wrap; gap: 9px; margin-top: 20px; }
        .sm-search-tags span { font-size: 12px; font-weight: 700; color: var(--sm-gray); margin-right: 4px; align-self: center; }
        .sm-search-tags a {
            font-size: 13px; font-weight: 600; color: var(--sm-ink);
            border: 1px solid var(--sm-line); border-radius: 999px; padding: 7px 15px;
            transition: all .22s var(--sm-ease);
        }
        .sm-search-tags a:hover { border-color: var(--sm-black); background: var(--sm-black); color: #fff; }

        .sm-backdrop {
            position: fixed; inset: 0; z-index: 550;
            background: rgba(0,0,0,.35); opacity: 0; visibility: hidden;
            transition: opacity .4s var(--sm-ease), visibility .4s;
        }
        .sm-backdrop.open { opacity: 1; visibility: visible; }

        /* ============================================================
           4. BUTTONS (pill – chuẩn Samsung)
           ============================================================ */
        .sm-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            height: 48px; padding: 0 30px; border-radius: 999px;
            font-size: 15px; font-weight: 600; letter-spacing: -.01em;
            border: 1px solid transparent; cursor: pointer;
            transition: all .28s var(--sm-ease);
        }
        .sm-btn--primary { background: var(--sm-blue); color: #fff; }
        .sm-btn--primary:hover { background: #0b6fd8; color: #fff; transform: translateY(-2px); box-shadow: 0 8px 22px rgba(33,137,255,.32); }
        .sm-btn--dark { background: var(--sm-black); color: #fff; }
        .sm-btn--dark:hover { background: #2b2b2b; color: #fff; transform: translateY(-2px); }
        .sm-btn--ghost { background: transparent; color: var(--sm-black); border-color: var(--sm-black); }
        .sm-btn--ghost:hover { background: var(--sm-black); color: #fff; }
        .sm-btn--light { background: rgba(255,255,255,.14); color: #fff; border-color: rgba(255,255,255,.85); backdrop-filter: blur(6px); }
        .sm-btn--light:hover { background: #fff; color: var(--sm-black); }
        .sm-btn--sm { height: 40px; padding: 0 22px; font-size: 13.5px; }

        /* Link mũi tên kiểu Samsung */
        .sm-link {
            display: inline-flex; align-items: center; gap: 7px;
            font-size: 14px; font-weight: 700; color: var(--sm-black);
        }
        .sm-link i { font-size: 12px; transition: transform .28s var(--sm-ease); }
        .sm-link:hover i { transform: translateX(5px); }

        /* ============================================================
           5. FOOTER (trắng, hairline, tối giản)
           ============================================================ */
        .sm-footer {
            background: var(--sm-surface);
            color: var(--sm-gray);
            margin-top: auto;
            border-top: 1px solid var(--sm-line);
            font-size: 13.5px;
        }
        .sm-footer-top {
            max-width: 1440px; margin: 0 auto;
            padding: 56px 24px 40px;
            display: grid; grid-template-columns: 1.4fr 1fr 1fr 1.3fr; gap: 40px;
        }
        .sm-footer h2 {
            font-size: 15px; letter-spacing: .14em; text-transform: uppercase;
            border: 1.5px solid var(--sm-black); display: inline-flex; padding: 5px 12px;
            margin: 0 0 20px;
        }
        .sm-footer h3 {
            font-size: 13px; font-weight: 800; color: var(--sm-black);
            letter-spacing: .02em; margin: 0 0 18px; text-transform: uppercase;
        }
        .sm-footer p { line-height: 1.7; margin: 0 0 14px; color: var(--sm-gray); }
        .sm-footer-col a {
            display: block; color: var(--sm-gray); font-weight: 500;
            margin-bottom: 12px; width: fit-content;
            transition: color .2s var(--sm-ease);
        }
        .sm-footer-col a:hover { color: var(--sm-black); text-decoration: underline; text-underline-offset: 3px; }

        .sm-socials { display: flex; gap: 10px; margin-top: 22px; }
        .sm-socials a {
            width: 38px; height: 38px; border-radius: 50%;
            border: 1px solid var(--sm-line); background: #fff;
            display: inline-flex; align-items: center; justify-content: center;
            color: var(--sm-ink); font-size: 15px; margin: 0;
            transition: all .25s var(--sm-ease);
        }
        .sm-socials a:hover { background: var(--sm-black); color: #fff; border-color: var(--sm-black); transform: translateY(-3px); }

        .sm-news-box {
            display: flex; align-items: center; gap: 10px; margin-top: 16px;
            border-bottom: 1.5px solid var(--sm-black); padding-bottom: 8px;
        }
        .sm-news-box input {
            flex: 1; border: none; outline: none; background: transparent;
            font-size: 14px; font-weight: 500; color: var(--sm-black); padding: 6px 0;
        }
        .sm-news-box input::placeholder { color: #a8a8a8; }
        .sm-news-box button {
            border: none; background: var(--sm-black); color: #fff;
            border-radius: 999px; height: 34px; padding: 0 18px;
            font-size: 12px; font-weight: 800; cursor: pointer;
            transition: all .25s var(--sm-ease);
        }
        .sm-news-box button:hover { background: var(--sm-blue); }

        .sm-footer-bottom {
            border-top: 1px solid var(--sm-line);
            padding: 20px 24px;
            font-size: 12.5px; color: #767676;
            max-width: 1440px; margin: 0 auto;
            display: flex; justify-content: space-between; gap: 14px; flex-wrap: wrap;
        }
        .sm-footer-bottom nav { display: flex; gap: 20px; flex-wrap: wrap; }
        .sm-footer-bottom a { color: #767676; }
        .sm-footer-bottom a:hover { color: var(--sm-black); }

        /* ============================================================
           6. BACK TO TOP
           ============================================================ */
        .sm-top {
            position: fixed; right: 22px; bottom: 96px; z-index: 400;
            width: 46px; height: 46px; border-radius: 50%;
            background: rgba(0,0,0,.82); color: #fff; border: none;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 16px; cursor: pointer;
            opacity: 0; visibility: hidden; transform: translateY(14px);
            transition: all .35s var(--sm-ease);
            backdrop-filter: blur(6px);
        }
        .sm-top.show { opacity: 1; visibility: visible; transform: translateY(0); }
        .sm-top:hover { background: var(--sm-blue); }

        /* ============================================================
           7. RESPONSIVE
           ============================================================ */
        @media (max-width: 1024px) {
            .sm-nav { display: none; }
            .sm-burger {
                display: inline-flex; margin-left: auto;
            }
            .sm-header .inner { gap: 14px; }
            .sm-footer-top { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 640px) {
            :root { --sm-radius: 18px; }
            .sm-utility { display: none; }
            .sm-footer-top { grid-template-columns: 1fr; padding: 40px 20px 30px; }
            .sm-footer-bottom { flex-direction: column; }
            .sm-header .inner { padding: 0 16px; }
        }

        /* Mobile drawer */
        .sm-drawer {
            position: fixed; top: 0; right: 0; bottom: 0; width: min(86vw, 340px);
            background: #fff; z-index: 620; padding: 22px;
            transform: translateX(100%); transition: transform .45s var(--sm-ease);
            display: flex; flex-direction: column; gap: 4px;
            box-shadow: -12px 0 40px rgba(0,0,0,.12);
        }
        .sm-drawer.open { transform: translateX(0); }
        .sm-drawer a {
            font-family: 'Manrope', sans-serif; font-weight: 700; font-size: 19px;
            color: var(--sm-black); padding: 14px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .sm-drawer .close { align-self: flex-end; border: none; background: transparent; font-size: 22px; margin-bottom: 10px; cursor: pointer; }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: .001ms !important; transition-duration: .001ms !important; }
            html { scroll-behavior: auto; }
        }
    </style>

    {{-- Page Specific Styles --}}
    @stack('styles')
</head>

<body>

    {{-- ===== UTILITY BAR ===== --}}
    <div class="sm-utility">
        <div class="inner">
            <a href="#">Hỗ trợ</a>
            <a href="#" class="hot"><i class="bi bi-telephone-fill"></i> 1900 1234</a>
            <a href="#">For Business <i class="bi bi-arrow-up-right"></i></a>
        </div>
    </div>

    {{-- ===== MAIN HEADER ===== --}}
    <header class="sm-header" id="smHeader">
        <div class="inner">
            <a href="{{ url('/') }}" class="sm-logo">ElectronicShop</a>

            <ul class="sm-nav">
                <li><a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Trang chủ</a></li>
                <li><a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">Sản phẩm</a></li>
                <li><a href="{{ route('news.index') }}" class="{{ request()->routeIs('news.*') ? 'active' : '' }}">Tin tức</a></li>
                <li><a href="{{ route('about.index') }}">Giới thiệu</a></li>
                <li><a href="{{ route('contact.index') }}" class="{{ request()->routeIs('contact.*') ? 'active' : '' }}">Liên hệ & Hỗ trợ</a></li>
            </ul>

            <div class="sm-actions">
                <button class="sm-icon-btn" id="smSearchOpen" aria-label="Tìm kiếm">
                    <i class="bi bi-search"></i>
                </button>

                <a href="{{ route('cart.index') }}" class="sm-icon-btn" aria-label="Giỏ hàng">
                    <i class="bi bi-cart2"></i>
                    @php $cartCount = session('cart') ? count(session('cart')) : 0; @endphp
                    @if($cartCount > 0)
                    <span class="sm-cart-badge">{{ $cartCount }}</span>
                    @endif
                </a>

                @auth
                {{-- Chuông thông báo --}}
                <div class="notif-wrap" id="notifWrap">
                    <button class="sm-icon-btn" id="notifBtn" aria-label="Thông báo">
                        <i class="bi bi-bell"></i>
                        <span class="notif-badge" id="notifBadge"></span>
                    </button>

                    {{-- Dropdown --}}
                    <div class="notif-dropdown" id="notifDropdown">
                        <div class="notif-header">
                            <h4><i class="bi bi-bell-fill"></i> Thông báo</h4>
                            <button class="notif-mark-all" id="notifMarkAll">
                                ✓ Đọc tất cả
                            </button>
                        </div>
                        <div class="notif-list" id="notifList">
                            <div class="notif-empty">
                                <i class="bi bi-bell-slash"></i>
                                <p>Không có thông báo</p>
                            </div>
                        </div>
                        <div class="notif-footer">
                            <a href="{{ route('news.index') }}">Xem tất cả tin tức →</a>
                        </div>
                    </div>
                </div>

                <a href="{{ route('profile') }}" class="sm-icon-btn" aria-label="Tài khoản"> <i class="bi bi-person"></i> </a>
                @else
                <a href="{{ route('login') }}" class="sm-icon-btn" aria-label="Đăng nhập">
                    <i class="bi bi-person"></i>
                </a>
                @endauth

                <button class="sm-icon-btn sm-burger" id="smBurger" aria-label="Menu">
                    <i class="bi bi-list"></i>
                </button>
            </div>
        </div>
    </header>

    {{-- ===== SEARCH PANEL ===== --}}
    <div class="sm-search-panel" id="smSearchPanel" role="dialog" aria-label="Tìm kiếm sản phẩm">
        <div class="wrap">
            <form class="sm-search-form" action="{{ route('products.index') }}" method="GET">
                <i class="bi bi-search"></i>
                <input type="text" name="q" id="smSearchInput" placeholder="Bạn đang tìm gì?" value="{{ request('q') }}" autocomplete="off">
                <button type="button" class="sm-search-close" id="smSearchClose" aria-label="Đóng">
                    <i class="bi bi-x-lg"></i>
                </button>
            </form>
            <div class="sm-search-tags">
                <span>Phổ biến</span>
                <a href="{{ route('products.index', ['q' => 'Galaxy Z Fold']) }}">Galaxy Z Fold</a>
                <a href="{{ route('products.index', ['q' => 'iPhone']) }}">iPhone</a>
                <a href="{{ route('products.index', ['q' => 'Tai nghe']) }}">Tai nghe</a>
                <a href="{{ route('products.index', ['q' => 'Laptop']) }}">Laptop</a>
                <a href="{{ route('products.index', ['q' => 'Smartwatch']) }}">Smartwatch</a>
                <a href="{{ route('products.index', ['q' => 'Sạc nhanh']) }}">Sạc nhanh</a>
            </div>
        </div>
    </div>

    {{-- ===== MOBILE DRAWER ===== --}}
    <nav class="sm-drawer" id="smDrawer" aria-label="Menu di động">
        <button class="close" id="smDrawerClose" aria-label="Đóng menu"><i class="bi bi-x-lg"></i></button>
        <a href="{{ url('/') }}">Trang chủ</a>
        <a href="{{ route('products.index') }}">Sản phẩm</a>
        <a href="{{ route('news.index') }}">Tin tức</a>
        <a href="{{ route('contact.index') }}">Liên hệ</a>
        <a href="{{ route('cart.index') }}">Giỏ hàng ({{ $cartCount ?? 0 }})</a>
        @auth
        <a href="{{ route('profile.account') }}">Tài khoản</a>
        @else
        <a href="{{ route('login') }}">Đăng nhập</a>
        @endauth
    </nav>

    <div class="sm-backdrop" id="smBackdrop"></div>

    {{-- ===== MAIN CONTENT ===== --}}
    <main>
        @yield('content')
    </main>

    {{-- ===== FOOTER ===== --}}
    <footer class="sm-footer">
        <div class="sm-footer-top">
            <div class="sm-footer-col">
                <h2>ElectronicShop</h2>
                <p>
                    123 Đường Nguyễn Văn Linh, Quận Hải Châu,<br>
                    TP Đà Nẵng<br>
                    Điện thoại: 1900 1234<br>
                    Email: electronicshop@gmail.com
                </p>
                <div class="sm-socials">
                    <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" title="Youtube"><i class="fab fa-youtube"></i></a>
                    <a href="#" title="Tiktok"><i class="fab fa-tiktok"></i></a>
                    <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                </div>
            </div>

            <div class="sm-footer-col">
                <h3>Hỗ trợ khách hàng</h3>
                <a href="#">Hướng dẫn mua hàng</a>
                <a href="#">Chính sách bảo hành</a>
                <a href="#">Chính sách đổi trả</a>
                <a href="#">Tra cứu đơn hàng</a>
                <a href="#">Câu hỏi thường gặp</a>
            </div>

            <div class="sm-footer-col">
                <h3>Về chúng tôi</h3>
                <a href="#">Giới thiệu ElectronicShop</a>
                <a href="#">Tuyển dụng</a>
                <a href="#">Hệ thống cửa hàng</a>
                <a href="#">Chính sách bảo mật</a>
                <a href="#">Liên hệ đối tác</a>
            </div>

            <div class="sm-footer-col">
                <h3>Đăng ký nhận tin</h3>
                <p>Nhận thông tin sản phẩm mới và ưu đãi độc quyền sớm nhất từ ElectronicShop.</p>
                <form id="footerNewsletterForm" class="sm-news-box" onsubmit="return false;">
                    <input type="email" name="email" id="footerNewsletterEmail" placeholder="Email của bạn" required>
                    <button type="submit" id="footerNewsletterBtn">ĐĂNG KÝ</button>
                </form>
                <div id="footerNewsletterMsg" style="font-size:12.5px;margin-top:10px;display:none"></div>
            </div>
        </div>

        <div class="sm-footer-bottom">
            <nav>
                <a href="#">Chính sách bảo mật</a>
                <a href="#">Điều khoản sử dụng</a>
                <a href="#">Cookies</a>
                <a href="{{ route('contact.index') }}">Liên hệ</a>
            </nav>
            <div>© 2026 ElectronicShop. All rights reserved.</div>
        </div>
    </footer>

    <button class="sm-top" id="smTop" aria-label="Lên đầu trang"><i class="bi bi-arrow-up"></i></button>

    {{-- JS Dependencies --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- ===== HEADER / SEARCH / DRAWER / BACK-TO-TOP ===== --}}
    <script>
    (function () {
        /* --- Sticky header ẩn/hiện theo hướng cuộn (kiểu Samsung) --- */
        const header = document.getElementById('smHeader');
        let lastY = window.scrollY;

        window.addEventListener('scroll', function () {
            const y = window.scrollY;
            header.classList.toggle('is-scrolled', y > 8);

            if (y > 220 && y > lastY) {
                header.classList.add('is-hidden');
            } else {
                header.classList.remove('is-hidden');
            }
            lastY = y;

            document.getElementById('smTop').classList.toggle('show', y > 700);
        }, { passive: true });

        /* --- Search panel --- */
        const panel    = document.getElementById('smSearchPanel');
        const backdrop = document.getElementById('smBackdrop');
        const drawer   = document.getElementById('smDrawer');

        function closeAll() {
            panel.classList.remove('open');
            drawer.classList.remove('open');
            backdrop.classList.remove('open');
            document.body.style.overflow = '';
        }

        document.getElementById('smSearchOpen').addEventListener('click', function () {
            panel.classList.add('open');
            backdrop.classList.add('open');
            setTimeout(() => document.getElementById('smSearchInput').focus(), 320);
        });
        document.getElementById('smSearchClose').addEventListener('click', closeAll);

        document.getElementById('smBurger').addEventListener('click', function () {
            drawer.classList.add('open');
            backdrop.classList.add('open');
            document.body.style.overflow = 'hidden';
        });
        document.getElementById('smDrawerClose').addEventListener('click', closeAll);
        backdrop.addEventListener('click', closeAll);

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeAll();
        });

        /* --- Back to top --- */
        document.getElementById('smTop').addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    })();
    </script>

    {{-- ===== NEWSLETTER ===== --}}
    <script>
        (function () {
            const form = document.getElementById('footerNewsletterForm');
            if (!form) return;
            const input = document.getElementById('footerNewsletterEmail');
            const btn   = document.getElementById('footerNewsletterBtn');
            const msg   = document.getElementById('footerNewsletterMsg');

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const email = input.value.trim();
                if (!email) return;

                btn.disabled = true;
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '...';

                fetch('{{ route('newsletter.subscribe') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify({ email: email, source: 'footer' }),
                })
                .then(async (res) => {
                    const data = await res.json();
                    msg.style.display = 'block';
                    if (res.ok) {
                        msg.style.color = '#0f8a3c';
                        msg.textContent = data.message;
                        input.value = '';
                    } else {
                        msg.style.color = '#d0021b';
                        msg.textContent = data.message || 'Có lỗi xảy ra, vui lòng thử lại.';
                    }
                })
                .catch(() => {
                    msg.style.display = 'block';
                    msg.style.color = '#d0021b';
                    msg.textContent = 'Không thể kết nối, vui lòng thử lại.';
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                });
            });
        })();

    @auth
    /* NOTIFICATION SYSTEM */
    (function () {
        const btn        = document.getElementById('notifBtn');
        const dropdown   = document.getElementById('notifDropdown');
        const list       = document.getElementById('notifList');
        const badge      = document.getElementById('notifBadge');
        const markAllBtn = document.getElementById('notifMarkAll');
        if (!btn) return;
        const API_LIST     = '{{ route("notifications.list") }}';
        const API_MARK_ALL = '{{ route("notifications.mark-all-read") }}';
        const CSRF         = document.querySelector('meta[name="csrf-token"]').content;
        let isOpen = false, cache = [];
        function setBadge(count) {
            if (count > 0) { badge.textContent = count > 99 ? '99+' : count; badge.style.display = 'block'; }
            else { badge.style.display = 'none'; }
        }
        function renderList(notifications) {
            if (!notifications.length) { list.innerHTML = '<div class="notif-empty"><i class="bi bi-bell-slash"></i><p>Chưa có thông báo nào</p></div>'; return; }
            list.innerHTML = notifications.map(n => `<div class="notif-item ${n.is_read ? '' : 'unread'}" data-id="${n.id}" data-url="${n.url || '#'}"><div class="notif-item-img">${ n.image ? `<img src="${n.image}" alt="">` : `<i class="bi bi-newspaper"></i>` }</div><div class="notif-item-content"><div class="notif-item-title">${n.title}</div>${ n.body ? `<div class="notif-item-body">${n.body}</div>` : '' }<div class="notif-item-time"><i class="bi bi-clock" style="font-size:10px"></i>${n.created_at}</div></div>${ !n.is_read ? '<div class="notif-dot"></div>' : '' }</div>`).join('');
            list.querySelectorAll('.notif-item').forEach(item => {
                item.addEventListener('click', function () {
                    const id = this.dataset.id, url = this.dataset.url;
                    fetch(`/thong-bao/${id}/doc`, { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } })
                    .then(r => r.json()).then(data => { setBadge(data.unread_count); const c = cache.find(n => n.id == id); if (c) c.is_read = true; this.classList.remove('unread'); this.querySelector('.notif-dot')?.remove(); if (url && url !== '#') window.location.href = url; }).catch(() => { if (url && url !== '#') window.location.href = url; });
                });
            });
        }
        function fetchNotifications(silent = false) {
            return fetch(API_LIST, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } })
            .then(r => r.json()).then(data => { cache = data.notifications; setBadge(data.unread_count); if (isOpen) renderList(cache); }).catch(() => {});
        }
        btn.addEventListener('click', function(e) { e.stopPropagation(); isOpen = !isOpen; dropdown.classList.toggle('open', isOpen); if (isOpen) fetchNotifications(); });
        document.addEventListener('click', function(e) { if (isOpen && !dropdown.contains(e.target) && e.target !== btn) { isOpen = false; dropdown.classList.remove('open'); } });
        markAllBtn.addEventListener('click', function () { fetch(API_MARK_ALL, { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } }).then(r => r.json()).then(() => { cache.forEach(n => n.is_read = true); setBadge(0); renderList(cache); }); });
        fetchNotifications(true);
        setInterval(() => fetchNotifications(true), 60000);
    })();
    @endauth
    </script>

    {{-- Page Specific Scripts --}}
    @stack('scripts')

    {{-- Floating Chatbot Widget --}}
    <x-chatbot-widget />
</body>
</html>