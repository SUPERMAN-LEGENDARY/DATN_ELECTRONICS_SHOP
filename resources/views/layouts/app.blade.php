<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'ElectronicShop'))</title>
    
    <!-- CSS Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        :root {
            --brand-blue: #0ea5e9;
            --brand-dark-blue: #0369a1;
            --brand-navy: #0c4a6e;
            --brand-red: #ef4444;
        }

        body {
            font-family: "Segoe UI", Roboto, Arial, sans-serif;
            background: linear-gradient(180deg,
                #bae6fd 0%, #e0f2fe 18%, #f0f9ff 38%,
                #e0f2fe 62%, #bae6fd 100%) fixed;
            background-attachment: fixed;
            color: #0c4a6e;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Responsive Images */
        img {
            max-width: 100%;
            height: auto;
        }

        /* Main layout structure */
        main {
            flex: 1;
            position: relative;
            z-index: 1;
        }

        /* ===== Header trên (Glassmorphism Sky Theme) ===== */
        header.top-header {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            padding: 0.9rem 0;
            box-shadow: 0 4px 20px rgba(14, 165, 233, 0.08);
            border-bottom: 1px solid rgba(186, 230, 253, 0.6);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-brand {
            font-size: 1.75rem;
            font-weight: 800;
            background: linear-gradient(135deg, #0369a1, #0ea5e9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
            text-decoration: none;
            transition: opacity 0.2s;
        }
        .navbar-brand:hover {
            opacity: 0.9;
        }

        /* ===== Thanh tìm kiếm Sky ===== */
        .search-wrapper {
            background: rgba(240, 249, 255, 0.85);
            backdrop-filter: blur(8px);
            border-radius: 16px;
            display: flex;
            align-items: center;
            padding: 4px 6px;
            flex-grow: 1;
            border: 1.5px solid rgba(186, 230, 253, 0.8);
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }
        .search-wrapper:focus-within {
            border-color: #0ea5e9;
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.15);
            background: #ffffff;
        }

        .search-wrapper input {
            background: transparent;
            border: none;
            outline: none;
            flex-grow: 1;
            padding: 0.6rem 1.1rem;
            font-size: 0.95rem;
            color: #0c4a6e;
            font-weight: 500;
        }

        .search-wrapper input::placeholder {
            color: #0369a1;
            opacity: 0.6;
        }

        .search-wrapper .btn-search {
            background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 100%);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.05rem;
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
            transition: transform 0.18s, box-shadow 0.18s;
        }

        .search-wrapper .btn-search:hover {
            transform: scale(1.04);
            box-shadow: 0 6px 16px rgba(14, 165, 233, 0.45);
        }

        /* ===== Icon tài khoản / giỏ hàng ===== */
        .nav-icon-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #0c4a6e;
            font-size: 0.95rem;
            font-weight: 700;
            text-decoration: none;
            white-space: nowrap;
            padding: 8px 14px;
            border-radius: 12px;
            background: rgba(186, 230, 253, 0.25);
            border: 1px solid rgba(186, 230, 253, 0.5);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .nav-icon-link:hover {
            color: #0369a1;
            background: rgba(186, 230, 253, 0.55);
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(14, 165, 233, 0.15);
        }

        .nav-icon-link i {
            font-size: 1.25rem;
            color: #0ea5e9;
        }

        .icon-wrap {
            position: relative;
            display: inline-flex;
        }

        .cart-badge {
            position: absolute;
            top: -7px;
            right: -10px;
            background: linear-gradient(135deg, #ef4444, #f87171);
            color: #ffffff;
            font-size: 0.65rem;
            min-width: 18px;
            height: 18px;
            padding: 0 4px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.45);
        }

        /* ===== Menu phụ + Hotline ===== */
        .sub-nav {
            background: rgba(255, 255, 255, 0.72);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(186, 230, 253, 0.5);
            padding: 0.7rem 0;
            position: relative;
            z-index: 90;
        }

        .nav-links {
            display: flex;
            gap: 2.2rem;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .nav-links a {
            color: #0c4a6e;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.95rem;
            padding: 6px 12px;
            border-radius: 10px;
            transition: all 0.2s;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: #0ea5e9;
            background: rgba(186, 230, 253, 0.35);
        }

        .hotline {
            color: #ef4444;
            font-weight: 800;
            font-size: 0.95rem;
            letter-spacing: 0.3px;
            display: flex;
            align-items: center;
            gap: 6px;
            background: rgba(254, 226, 226, 0.6);
            padding: 6px 14px;
            border-radius: 20px;
            border: 1px solid rgba(252, 165, 165, 0.5);
        }

        /* ===== FOOTER (Sky Glassmorphism) ===== */
        .footer {
            background: rgba(255, 255, 255, 0.86);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            padding: 45px 0 0;
            color: #0369a1;
            margin-top: auto;
            border-top: 1px solid rgba(186, 230, 253, 0.7);
            box-shadow: 0 -10px 30px rgba(14, 165, 233, 0.08);
            position: relative;
            z-index: 1;
        }

        .footer-container {
            display: grid;
            grid-template-columns: 1.2fr 1fr 1fr 1.1fr;
            gap: 35px;
            padding: 0 15px 40px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer h2 {
            color: #0c4a6e;
            font-size: 24px;
            font-weight: 800;
            margin: 0 0 18px;
        }

        .footer h3 {
            color: #0c4a6e;
            font-size: 17px;
            font-weight: 800;
            margin: 0 0 18px;
        }

        .footer p {
            font-size: 14px;
            line-height: 1.6;
            color: #0369a1;
            opacity: 0.9;
            margin: 0 0 15px;
        }

        .footer-column a {
            display: block;
            color: #0369a1;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 12px;
            transition: color 0.2s, transform 0.2s;
        }

        .footer-column a:hover {
            color: #0ea5e9;
            transform: translateX(4px);
        }

        /* Social Icons */
        .socials {
            display: flex;
            gap: 14px;
            margin-top: 20px;
        }

        .socials a {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 17px;
            text-decoration: none;
            transition: all 0.25s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .socials a:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        }

        .facebook { background: #1877f2; }
        .youtube  { background: #ff0000; }
        .tiktok   { background: #0f172a; }
        .instagram{ background: linear-gradient(45deg, #feda75, #d62976, #4f5bd5); }

        /* Newsletter Box */
        .email-box {
            display: flex;
            margin-top: 15px;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid rgba(186, 230, 253, 0.9);
            box-shadow: 0 2px 10px rgba(14, 165, 233, 0.08);
        }

        .email-box input {
            height: 46px;
            flex: 1;
            border: none;
            background: rgba(240, 249, 255, 0.9);
            padding: 0 14px;
            font-size: 14px;
            outline: none;
            color: #0c4a6e;
            font-weight: 500;
        }

        .email-box input::placeholder {
            color: #0369a1;
            opacity: 0.6;
        }

        .email-box button {
            width: 90px;
            height: 46px;
            border: none;
            background: linear-gradient(135deg, #0369a1, #0ea5e9);
            color: #ffffff;
            font-size: 13.5px;
            font-weight: 800;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        .email-box button:hover {
            opacity: 0.92;
        }

        /* Copyright */
        .footer-bottom {
            border-top: 1px solid rgba(186, 230, 253, 0.6);
            padding: 20px 15px;
            font-size: 13.5px;
            font-weight: 600;
            color: #0369a1;
            text-align: center;
            background: rgba(224, 242, 254, 0.5);
        }

        @media(max-width:900px) {
            .footer-container {
                grid-template-columns: 1fr 1fr;
            }
        }
        @media(max-width:640px) {
            .footer-container {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <!-- Page Specific Styles -->
    @stack('styles')
</head>

<body>

    {{-- ===== HEADER ===== --}}
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
                    <button type="submit" class="btn-search" title="Tìm kiếm">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    @auth
                    <a href="{{ route('profile.account') }}" class="nav-icon-link">
                        <i class="bi bi-person-circle"></i> Tài khoản
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
                            $cartCount = session('cart') ? count(session('cart')) : 0;
                            @endphp
                            <span class="cart-badge">{{ $cartCount }}</span>
                        </span>
                        Giỏ hàng
                    </a>
                </div>
            </div>
        </div>
    </header>

    {{-- ===== SUB NAVIGATION ===== --}}
    <nav class="sub-nav">
        <div class="container d-flex justify-content-between align-items-center">
            <ul class="nav-links">
                <li><a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Trang chủ</a></li>
                <li><a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">Sản phẩm</a></li>
                <li><a href="{{ route('news.index') }}" class="{{ request()->routeIs('news.*') ? 'active' : '' }}">Tin tức</a></li>
                <li><a href="{{ route('contact.index') }}" class="{{ request()->routeIs('contact.*') ? 'active' : '' }}">Liên hệ</a></li>
            </ul>
            <div class="hotline">
                <i class="bi bi-telephone-fill"></i> HOTLINE: 1900 1234
            </div>
        </div>
    </nav>

    {{-- ===== MAIN CONTENT ===== --}}
    <main>
        @yield('content')
    </main>

    {{-- ===== FOOTER ===== --}}
    <footer class="footer">
        <div class="footer-container">
            <!-- Cột 1 -->
            <div class="footer-column footer-brand">
                <h2>ElectronicShop</h2>
                <p>
                    Địa chỉ: 123 Đường Nguyễn Văn Linh,<br>
                    Quận Hải Châu, TP Đà Nẵng<br>
                    Điện thoại: 1900 1234<br>
                    Email: electronicshop@gmail.com
                </p>
                <div class="socials">
                    <a href="#" class="facebook" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="youtube" title="Youtube"><i class="fab fa-youtube"></i></a>
                    <a href="#" class="tiktok" title="Tiktok"><i class="fab fa-tiktok"></i></a>
                    <a href="#" class="instagram" title="Instagram"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <!-- Cột 2 -->
            <div class="footer-column">
                <h3>Hỗ trợ khách hàng</h3>
                <a href="#">Hướng dẫn mua hàng</a>
                <a href="#">Chính sách bảo hành</a>
                <a href="#">Chính sách đổi trả</a>
                <a href="#">Tra cứu đơn hàng</a>
                <a href="#">Câu hỏi thường gặp</a>
            </div>
            <!-- Cột 3 -->
            <div class="footer-column">
                <h3>Về chúng tôi</h3>
                <a href="#">Giới thiệu ElectronicShop</a>
                <a href="#">Tuyển dụng</a>
                <a href="#">Hệ thống cửa hàng</a>
                <a href="#">Chính sách bảo mật</a>
                <a href="#">Liên hệ đối tác</a>
            </div>
            <!-- Cột 4 -->
            <div class="footer-column newsletter">
                <h3>Đăng ký nhận tin</h3>
                <p>
                    Đăng ký để nhận các chương trình khuyến mãi sớm nhất từ ElectronicShop.
                </p>
                <form id="footerNewsletterForm" class="email-box" onsubmit="return false;">
                    <input type="email" name="email" id="footerNewsletterEmail" placeholder="Email của bạn..." required>
                    <button type="submit" id="footerNewsletterBtn">
                        ĐĂNG KÝ
                    </button>
                </form>
                <div id="footerNewsletterMsg" style="font-size:12.5px;margin-top:8px;display:none"></div>
            </div>
        </div>
        <div class="footer-bottom">
            © 2026 ELECTRONICSHOP. All rights reserved. Designed for Vietnamese users.
        </div>
    </footer>

    <!-- JS Dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Newsletter Script -->
    <script>
        (function () {
            const form  = document.getElementById('footerNewsletterForm');
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
                        msg.style.color = '#16a34a';
                        msg.textContent = data.message;
                        input.value = '';
                    } else {
                        msg.style.color = '#e53935';
                        msg.textContent = data.message || 'Có lỗi xảy ra, vui lòng thử lại.';
                    }
                })
                .catch(() => {
                    msg.style.display = 'block';
                    msg.style.color = '#e53935';
                    msg.textContent = 'Không thể kết nối, vui lòng thử lại.';
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                });
            });
        })();
    </script>

    <!-- Page Specific Scripts -->
    @stack('scripts')

    <!-- Floating Chatbot Widget -->
    <x-chatbot-widget />
</body>
</html>
