@extends('layouts.app')
@section('title', 'ElectronicShop - Liên hệ')
@php
    $showSearch = true;
@endphp

@push('styles')
<style>
/* ============================================================
   GENERAL
   ============================================================ */
body { background: #ffffff; color: #000000; }

.reveal {
    opacity: 0; transform: translateY(24px);
    transition: opacity .6s cubic-bezier(.16,1,.3,1), transform .6s cubic-bezier(.16,1,.3,1);
}
.reveal.revealed { opacity: 1; transform: translateY(0); }

.text-danger { display:block; color:#d93025; font-size:12.5px; margin-top:4px; }

.alert-success {
    display: flex; align-items: center; gap: 10px;
    background: #e6f4ea; color: #137333;
    border: 1px solid #ceead6; border-radius: 12px;
    padding: 14px 18px; margin: 24px auto 0; max-width: 1200px;
    font-weight: 600; font-size: 14px;
}

/* ============================================================
   TOP BANNER — thống kê + tìm kiếm nhanh (kiểu OPPO Support)
   ============================================================ */
.top-banner {
    position: relative;
    height: 560px;
    min-height: 460px;
    color: #fff;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}
.banner-bg-layer {
    position: absolute; inset: 0; z-index: 0;
    /* Giữ ảnh phủ kín banner, không làm vỡ bố cục */
    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;
    transform: translateZ(0);
    will-change: opacity, background-image;
    filter: brightness(1.08) saturate(1.08);
    background-color: #111827; /* màu nền dự phòng */
    opacity: 0; transition: opacity .7s ease;
}
.banner-bg-layer.is-active { opacity: 1; }

.top-banner .container {
    max-width: 1200px;
    width: 100%;
    margin: 0 auto;
    padding: 56px 24px 40px;
    position: relative;
    z-index: 2;
    flex: 1;
}
.top-banner .stat-number {
    font-size: 44px; font-weight: 800; line-height: 1;
    display: inline-block; margin-right: 10px;
}
.top-banner .stat-label { font-size: 18px; font-weight: 500; opacity: .95; }
.top-banner .banner-desc {
    max-width: 560px; font-size: 16px; line-height: 1.5;
    margin: 18px 0 32px; opacity: .92;
}

/* QUICK SUPPORT ACTIONS */
.banner-quick-actions { display:flex; align-items:stretch; gap:14px; margin-top:28px; flex-wrap:wrap; max-width:820px; }
.quick-action { display:flex; align-items:center; gap:12px; min-width:220px; padding:14px 18px; color:#fff; text-decoration:none; background:rgba(18,18,18,.40); backdrop-filter:blur(12px); -webkit-backdrop-filter:blur(12px); border:1px solid rgba(255,255,255,.18); border-radius:14px; transition:transform .25s ease,background .25s ease,border-color .25s ease; }
.quick-action:hover { transform:translateY(-4px); background:rgba(18,18,18,.62); border-color:rgba(255,255,255,.38); color:#fff; }
.quick-action-icon { width:42px; height:42px; flex-shrink:0; display:flex; align-items:center; justify-content:center; border-radius:12px; background:rgba(255,255,255,.14); font-size:20px; }
.quick-action strong { display:block; font-size:14px; font-weight:700; margin-bottom:3px; }
.quick-action span { display:block; font-size:12px; line-height:1.35; color:rgba(255,255,255,.78); }


/* thanh danh mục dưới cùng banner */
.top-category-bar {
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 3;
    min-height: 122px;
    background: rgba(0,0,0,.38);
    backdrop-filter: blur(6px);
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 64px;
    flex-wrap: wrap;
    padding: 24px;
}
.top-category-bar .cat-item {
    display: flex; flex-direction: column; align-items: center; gap: 8px;
    color: #fff; text-decoration: none; min-width: 60px;
}
.top-category-bar .cat-item i { font-size: 22px; }
.top-category-bar .cat-item span { font-size: 14px; font-weight: 500; }
.top-category-bar .cat-item.active { background: rgba(255,255,255,.14); border-radius: 10px; padding: 10px 18px; margin: -10px 0; }

@media (max-width: 768px) {
    .top-banner { height: 650px; min-height: 560px; }
    .banner-bg-layer { background-position: center center; }
    .top-banner .container { padding: 40px 20px 190px; }
    .top-banner .stat-number { font-size: 32px; }
    .banner-quick-actions { gap: 10px; margin-top: 22px; }
    .quick-action { width: 100%; min-width: 100%; padding: 12px 14px; }
    .top-category-bar { gap: 18px 28px; min-height: 140px; padding: 18px 16px; }
}

/* ============================================================
   HERO — "Chúng tôi có thể giúp gì cho bạn?"
   ============================================================ */
.support-hero { padding: 64px 0 56px; text-align: center; }
.support-hero .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }

.hero-icon {
    width: 64px; height: 64px; border-radius: 50%;
    background: gray; color: #fff; font-size: 26px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 24px;
}

.support-hero h1 { font-size: 34px; font-weight: 800; color: #000; margin-bottom: 12px; }
.support-hero .lead { color: #666; font-size: 16px; margin-bottom: 48px; }

.category-row {
    display: flex; justify-content: center; gap: 56px;
    flex-wrap: wrap; row-gap: 32px;
}
.category-item {
    display: flex; flex-direction: column; align-items: center; gap: 12px;
    text-decoration: none; color: inherit; min-width: 64px;
}
.category-icon {
    width: 44px; height: 44px; display: flex;
    align-items: center; justify-content: center;
    font-size: 26px; color: #111; transition: color .2s;
}
.category-icon img {
    width: 40px; height: 40px; object-fit: contain;
}
.category-item:hover .category-icon { color: #0d6efd; }
.category-item span { font-size: 14px; font-weight: 500; color: #333; }

/* ============================================================
   TÌM CHỦ ĐỀ HỖ TRỢ (gray section)
   ============================================================ */
.support-search-section { background: #f5f5f6; padding: 56px 0; text-align: center; }
.support-search-section .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
.support-search-section h2 { font-size: 26px; font-weight: 800; color: #000; margin-bottom: 24px; }

.search-box { position: relative; max-width: 600px; margin: 0 auto 40px; }
.search-box i {
    position: absolute; left: 18px; top: 50%; transform: translateY(-50%);
    color: #888; font-size: 15px;
}
.search-box input {
    width: 100%; border: 1px solid #ddd; border-radius: 14px;
    padding: 14px 18px 14px 46px; font-size: 15px;
    background: #fff; outline: none; font-family: inherit;
}
.search-box input:focus { border-color: #0d6efd; }

.support-cards {
    display: grid; grid-template-columns: repeat(3, 1fr);
    gap: 24px; max-width: 1000px; margin: 0 auto;
}
@media (max-width: 768px) { .support-cards { grid-template-columns: 1fr; } }

.support-card {
    background: #fff; border-radius: 18px; padding: 32px 24px;
    text-align: left; text-decoration: none; color: inherit; display: block;
    box-shadow: 0 1px 3px rgba(0,0,0,.05);
    transition: transform .2s, box-shadow .2s;
}
.support-card:hover { transform: translateY(-4px); box-shadow: 0 10px 24px rgba(0,0,0,.08); }
.support-card-icon { font-size: 24px; color: #0d6efd; margin-bottom: 16px; }
.support-card b { display: block; font-size: 16px; font-weight: 700; color: #000; margin-bottom: 6px; }
.support-card p { color: #777; font-size: 14px; margin: 0 0 16px; }
.support-card .card-arrow { color: #0d6efd; }

/* ============================================================
   LIÊN HỆ ElectronicShop
   ============================================================ */
.contact-section { padding: 72px 0; text-align: center; }
.contact-section .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
.contact-section h2 { font-size: 28px; font-weight: 800; color: #000; margin-bottom: 10px; }
.contact-section .lead { color: #666; margin-bottom: 40px; }

.contact-card {
    display: grid; grid-template-columns: 1fr 1.7fr;
    max-width: 1000px; margin: 0 auto;
    border-radius: 24px; overflow: hidden;
    border: 1px solid #eee; box-shadow: 0 4px 24px rgba(0,0,0,.05);
}
@media (max-width: 860px) { .contact-card { grid-template-columns: 1fr; } }

/* --- left: thông tin liên hệ --- */
.contact-info-panel { background: #181818; color: #fff; padding: 40px 32px; text-align: left; }
.contact-info-panel h3 { font-size: 22px; font-weight: 800; margin-bottom: 10px; }
.contact-info-panel .panel-desc { color: #aaa; font-size: 14px; margin-bottom: 32px; line-height: 1.5; }

.info-item { display: flex; gap: 14px; margin-bottom: 22px; align-items: flex-start; }
.info-item .info-icon {
    width: 36px; height: 36px; border-radius: 50%;
    background: #2a2a2a; color: #fff; font-size: 14px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
}
.info-item b { display: block; font-size: 12px; color: #999; font-weight: 500; margin-bottom: 2px; }
.info-item p { margin: 0; font-size: 14.5px; font-weight: 600; color: #fff; }

/* --- right: gửi yêu cầu hỗ trợ (form) --- */
.contact-form-panel { background: #fff; padding: 40px 36px; text-align: left; }
.contact-form-panel h3 { font-size: 20px; font-weight: 800; color: #000; margin-bottom: 24px; }

.contact-form-panel .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
@media (max-width: 520px) { .contact-form-panel .form-row { grid-template-columns: 1fr; } }

.contact-form-panel .form-group { margin-bottom: 18px; }
.contact-form-panel .form-group.full { margin-bottom: 22px; }
.contact-form-panel label {
    display: block; font-size: 13.5px; font-weight: 600; color: #333; margin-bottom: 6px;
}
.contact-form-panel input,
.contact-form-panel textarea,
.contact-form-panel select {
    width: 100%; border: 1px solid #ddd; border-radius: 10px;
    padding: 12px 14px; font-size: 14.5px; outline: none;
    background: #fff; color: #000; font-family: inherit;
}
.contact-form-panel input:focus,
.contact-form-panel textarea:focus,
.contact-form-panel select:focus {
    border-color: #0d6efd; box-shadow: 0 0 0 3px rgba(13,110,253,.12);
}
.contact-form-panel textarea { min-height: 130px; resize: vertical; }

.btn-send {
    width: 100%; background: gray; color: #fff; border: none;
    padding: 15px; border-radius: 12px; font-size: 15px; font-weight: 700;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    gap: 8px; transition: background .2s;
}
.btn-send:hover { background: gray; }

/* ============================================================
   GOOGLE MAP
   ============================================================ */
.contact-map {
    max-width: 1000px; margin: 40px auto 0;
    border-radius: 24px; overflow: hidden;
    border: 1px solid #eee; box-shadow: 0 4px 24px rgba(0,0,0,.05);
}
.contact-map iframe {
    width: 100%; height: 360px; border: 0; display: block;
}
@media (max-width: 768px) {
    .contact-map iframe { height: 260px; }
}
</style>
@endpush

@section('content')

{{-- Thông báo --}}
@if(session('success'))
<div class="alert-success">
    <i class="fas fa-circle-check"></i>
    {{ session('success') }}
</div>
@endif

{{-- ===== TOP BANNER: thống kê người dùng + tìm kiếm nhanh ===== --}}
@php
    // Ảnh nền lấy từ thư mục public/images
    // Mỗi thương hiệu dùng một ảnh riêng.
    $brandBackgrounds = [
        'apple'   => asset('images/apple.jpg'),
        'samsung' => asset('images/samsung.jpg'),
        'xiaomi'  => asset('images/xiaomi.jpg'),
        'oppo'    => asset('images/oppo.jpg'),
        'vivo'    => asset('images/vivo.jpg'),
        'realme'  => asset('images/realme.jpg'),
    ];

    // Ảnh nền mặc định lấy từ thư mục public/images
    $defaultBgImage = asset('images/apple.jpg');


    // Lớp phủ tối để chữ trắng vẫn đọc rõ trên mọi ảnh
    $bannerBgCss = fn ($imageUrl) => "linear-gradient(90deg, rgba(8,12,10,.72) 0%, rgba(8,12,10,.45) 42%, rgba(8,12,10,.08) 100%), url('{$imageUrl}')";

    $defaultBg = $bannerBgCss($defaultBgImage);
@endphp
<section class="top-banner reveal" id="topBanner">
    <div class="banner-bg-layer bg-layer-a is-active" style="background-image: {{ $defaultBg }};"></div>
    <div class="banner-bg-layer bg-layer-b" style="background-image: {{ $defaultBg }};"></div>

    <div class="container">
        <div>
            <span class="stat-number" id="statNumber" data-target="{{ $userCount ?? 137843 }}">0</span>
            <span class="stat-label">Người sử dụng</span>
        </div>
        <p class="banner-desc">Đồng hành và chọn lựa sử dụng dịch vụ tự phục vụ chính hãng hôm nay</p>
        <div class="banner-quick-actions">
            <a href="#support-order" class="quick-action"><div class="quick-action-icon"><i class="bi bi-box-seam"></i></div><div><strong>Theo dõi đơn hàng</strong><span>Kiểm tra trạng thái đơn hàng của bạn</span></div></a>
            <a href="#support-warranty" class="quick-action"><div class="quick-action-icon"><i class="bi bi-shield-check"></i></div><div><strong>Bảo hành sản phẩm</strong><span>Tra cứu và nhận hỗ trợ bảo hành</span></div></a>
            <a href="#contact-section" class="quick-action"><div class="quick-action-icon"><i class="bi bi-headset"></i></div><div><strong>Liên hệ hỗ trợ</strong><span>Đội ngũ ElectronicShop luôn sẵn sàng</span></div></a>
        </div>
    </div>

    <div class="top-category-bar" id="topCategoryBar" data-default-bg="{{ $defaultBgImage }}">
        @foreach($brands as $brand)
        @php
            $brandImage = $brandBackgrounds[$brand->slug] ?? $defaultBgImage;
            $brandHref = $brandLinks[$brand->slug] ?? route('products.index', ['brand' => $brand->slug]);
            $isExternal = isset($brandLinks[$brand->slug]);
        @endphp
        <a href="{{ $brandHref }}"
           class="cat-item {{ $loop->first ? 'active' : '' }}"
           data-bg="{{ $brandImage }}"
           @if($isExternal) target="_blank" rel="noopener" @endif>
            @if($brand->logo)
                <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" style="width:22px;height:22px;object-fit:contain;">
            @else
                <i class="bi bi-tag"></i>
            @endif
            <span>{{ $brand->name }}</span>
        </a>
        @endforeach
    </div>
</section>

{{-- ===== HERO: Chúng tôi có thể giúp gì cho bạn? ===== --}}
<section class="support-hero reveal">
    <div class="container">
        <div class="hero-icon"><i class="bi bi-headset"></i></div>
        <h1>Chúng tôi có thể giúp gì cho bạn?</h1>
        <p class="lead">Chọn sản phẩm hoặc tìm nhanh nội dung bạn cần hỗ trợ.</p>

        <div class="category-row">
            @foreach($brands as $brand)
            <a href="{{ route('products.index', ['brand' => $brand->slug]) }}" class="category-item">
                <div class="category-icon">
                    @if($brand->logo)
                        <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}">
                    @else
                        <i class="bi bi-tag"></i>
                    @endif
                </div>
                <span>{{ $brand->name }}</span>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== TÌM CHỦ ĐỀ HỖ TRỢ ===== --}}
<section class="support-search-section reveal" id="support-order">
    <div class="container">
        <h2>Tìm chủ đề hỗ trợ</h2>

        <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Tìm kiếm hỗ trợ">
        </div>

        <div class="support-cards">
            <a href="#" class="support-card">
                <div class="support-card-icon"><i class="bi bi-box-seam"></i></div>
                <b>Kiểm tra đơn hàng</b>
                <p>Theo dõi trạng thái giao hàng</p>
                <span class="card-arrow"><i class="bi bi-chevron-right"></i></span>
            </a>
            <a href="#contact-section" class="support-card" id="support-warranty">
                <div class="support-card-icon"><i class="bi bi-wrench-adjustable"></i></div>
                <b>Bảo hành &amp; sửa chữa</b>
                <p>Đặt lịch tại trung tâm gần nhất</p>
                <span class="card-arrow"><i class="bi bi-chevron-right"></i></span>
            </a>
            <a href="#" class="support-card">
                <div class="support-card-icon"><i class="bi bi-cash-coin"></i></div>
                <b>Thanh toán &amp; hoàn tiền</b>
                <p>Hóa đơn, trả góp và đổi trả</p>
                <span class="card-arrow"><i class="bi bi-chevron-right"></i></span>
            </a>
        </div>
    </div>
</section>

{{-- ===== LIÊN HỆ ElectronicShop ===== --}}
<section class="contact-section reveal" id="contact-section">
    <div class="container">
        <h2>Liên hệ ElectronicShop</h2>
        <p class="lead">Đội ngũ chuyên viên luôn sẵn sàng lắng nghe và hỗ trợ bạn.</p>

        <div class="contact-card">
            {{-- THÔNG TIN --}}
            <div class="contact-info-panel">
                <h3>Thông tin liên hệ</h3>
                <p class="panel-desc">Kết nối với chúng tôi qua kênh thuận tiện nhất cho bạn.</p>

                @foreach([
                    ['fa-phone',          'Tổng đài',     '1900 1234'],
                    ['fa-envelope',       'Email',        'cskh@electronicshop.vn'],
                    ['fa-map-marker-alt', 'Địa chỉ',      '123 Nguyễn Văn Linh, Hải Châu, Đà Nẵng'],
                    ['fa-clock',          'Giờ làm việc', '08:00 – 22:00, mỗi ngày'],
                ] as [$icon, $label, $val])
                <div class="info-item">
                    <div class="info-icon"><i class="fas {{ $icon }}"></i></div>
                    <div>
                        <b>{{ $label }}</b>
                        <p>{{ $val }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- FORM --}}
            <div class="contact-form-panel">
                <h3>Gửi yêu cầu hỗ trợ</h3>

                <form action="{{ route('contact.send') }}" method="POST">
                    @csrf

                    <div class="form-row">
                        <div class="form-group">
                            <label>Họ và tên</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                   placeholder="Nguyễn Văn A">
                            @error('name')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   placeholder="ban@email.com">
                            @error('email')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                   placeholder="0901 234 567">
                            @error('phone')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label>Chủ đề</label>
                            <select name="subject">
                                <option value="">Chọn chủ đề</option>
                                @foreach(['Tư vấn sản phẩm','Đặt hàng','Bảo hành','Đổi trả','Khiếu nại','Góp ý','Khác'] as $opt)
                                <option value="{{ $opt }}" {{ old('subject')==$opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                            @error('subject')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="form-group full">
                        <label>Nội dung</label>
                        <textarea name="message" rows="5"
                                  placeholder="Mô tả vấn đề bạn đang gặp...">{{ old('message') }}</textarea>
                        @error('message')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <button type="submit" class="btn-send" id="btnSend">
                        <i class="bi bi-chat-dots-fill"></i> Gửi yêu cầu
                    </button>
                </form>
            </div>
        </div>
        {{-- ===== GOOGLE MAP ===== --}}
        <div class="contact-map reveal">
            <iframe
                src="https://www.google.com/maps?q=123+Nguyen+Van+Linh+Da+Nang&output=embed"
                loading="lazy" allowfullscreen title="Bản đồ ElectronicShop">
            </iframe>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
document.querySelector('form[action="{{ route('contact.send') }}"]')
    ?.addEventListener('submit', function () {
        const btn = document.getElementById('btnSend');
        if (btn) {
            btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Đang xử lý...';
            btn.disabled = true;
            btn.style.opacity = '.7';
        }
    });

(function () {
    const io = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.classList.add('revealed');
                io.unobserve(e.target);
            }
        });
    }, { threshold: 0.07, rootMargin: '0px 0px -30px 0px' });

    document.querySelectorAll('.reveal').forEach(el => io.observe(el));
})();

/* ===== TOP BANNER: số người sử dụng tự động đếm tăng + tiếp tục tăng liên tục ===== */
(function () {
    const el = document.getElementById('statNumber');
    if (!el) return;

    const target = parseInt(el.dataset.target, 10) || 0;
    const duration = 1800; // ms cho lần đếm ban đầu
    let started = false;
    let currentValue = 0;

    function formatNumber(n) {
        return n.toLocaleString('vi-VN');
    }

    function render() {
        el.textContent = formatNumber(currentValue);
    }

    // Sau khi đếm tới target, tiếp tục tăng dần đều đặn để mô phỏng người dùng mới liên tục
    function startLiveGrowth() {
        function scheduleNextTick() {
            // mỗi lần tăng 1–4 người, cách nhau 2–6 giây (ngẫu nhiên cho tự nhiên)
            const delay = 2000 + Math.random() * 4000;
            setTimeout(() => {
                const increment = 1 + Math.floor(Math.random() * 4);
                currentValue += increment;
                render();
                scheduleNextTick();
            }, delay);
        }
        scheduleNextTick();
    }

    function runCountUp() {
        if (started) return;
        started = true;

        const startTime = performance.now();

        function tick(now) {
            const elapsed = now - startTime;
            const progress = Math.min(elapsed / duration, 1);
            // easeOutCubic để số chạy nhanh lúc đầu, chậm dần lúc gần đích
            const eased = 1 - Math.pow(1 - progress, 3);
            currentValue = Math.floor(eased * target);
            render();

            if (progress < 1) {
                requestAnimationFrame(tick);
            } else {
                currentValue = target;
                render();
                startLiveGrowth();
            }
        }

        requestAnimationFrame(tick);
    }

    const statObserver = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                runCountUp();
                statObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.3 });

    statObserver.observe(el);
})();

/* ===== TOP BANNER: đổi nền động khi hover thương hiệu ===== */
(function () {
    const bar = document.getElementById('topCategoryBar');
    if (!bar) return;

    const layerA = document.querySelector('.bg-layer-a');
    const layerB = document.querySelector('.bg-layer-b');
    const defaultBg = bar.dataset.defaultBg;
    let showingA = true;

    function setBanner(imageUrl) {
        if (!imageUrl) return;

        const nextLayer = showingA ? layerB : layerA;
        const currentLayer = showingA ? layerA : layerB;
        const overlay = 'linear-gradient(90deg, rgba(8,12,10,.68) 0%, rgba(8,12,10,.38) 45%, rgba(8,12,10,.08) 100%)';

        nextLayer.style.backgroundImage = `${overlay}, url("${imageUrl}")`;
        nextLayer.classList.add('is-active');
        currentLayer.classList.remove('is-active');
        showingA = !showingA;
    }

    bar.querySelectorAll('.cat-item').forEach(item => {
        item.addEventListener('mouseenter', function () {
            const bg = this.dataset.bg;
            if (!bg) return;
            setBanner(bg);
            bar.querySelectorAll('.cat-item').forEach(el => el.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Giữ ảnh của thương hiệu đang hover/được chọn, không reset ngay khi rời thanh thương hiệu.
})();
</script>
@endpush