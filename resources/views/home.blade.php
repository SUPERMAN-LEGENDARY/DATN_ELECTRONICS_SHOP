@extends('layouts.app')
@section('title', 'ElectronicShop - Trang chủ')
@php $showSearch = true; @endphp

@push('styles')
<style>
    /* ===== HERO SLIDER ===== */
    .hero {
        position: relative;
        overflow: hidden;
        background: #EBF3FF;
    }

    .hero-inner {
        display: grid;
        grid-template-columns: 1fr 1fr;
        min-height: 320px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .hero-content {
        padding: 48px 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .hero-label {
        font-size: 12px;
        font-weight: 700;
        color: #1565C0;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 10px;
    }

    .hero-title {
        font-size: 36px;
        font-weight: 800;
        line-height: 1.2;
        color: #0D1B2A;
        margin-bottom: 14px;
    }

    .hero-desc {
        font-size: 15px;
        color: #555;
        margin-bottom: 14px;
    }

    .hero-price {
        font-size: 22px;
        font-weight: 800;
        color: #E53935;
        margin-bottom: 20px;
    }

    .hero-img {
        background: #dce8f5;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .hero-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .hero-img-placeholder {
        width: 100%;
        height: 320px;
        background: #d8e6f3;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #aaa;
    }

    .hero-dots {
        position: absolute;
        bottom: 12px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 6px;
    }

    .hero-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #bbb;
        cursor: pointer;
    }

    .hero-dot.active {
        background: #1565C0;
        width: 20px;
        border-radius: 4px;
    }

    .hero-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255, 255, 255, .85);
        border: none;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .15);
        z-index: 2;
        font-size: 14px;
        color: #333;
    }

    .hero-arrow.left {
        left: 12px;
    }

    .hero-arrow.right {
        right: 12px;
    }

    /* ===== TRUST BAR ===== */
    .trust-bar {
        border-bottom: 1px solid #e0e0e0;
    }

    .trust-bar .inner {
        display: flex;
        justify-content: space-around;
        padding: 16px;
        max-width: 1200px;
        margin: 0 auto;
        flex-wrap: wrap;
        gap: 8px;
    }

    .trust-bar-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
    }

    .trust-bar-item i {
        color: #1565C0;
        font-size: 18px;
    }

    .trust-bar-item b {
        display: block;
        font-weight: 600;
        font-size: 13px;
    }

    .trust-bar-item span {
        font-size: 11px;
        color: #888;
    }

    /* ===== PRODUCTS GRID ===== */
    .section {
        padding: 32px 0;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }

    /* ===== EVENTS / KHUYẾN MÃI THEO MÙA ===== */
    .event-strip {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 16px;
        margin: 20px 0 8px;
    }

    .event-card {
        position: relative;
        display: block;
        border-radius: 14px;
        min-height: 130px;
        padding: 22px 24px;
        overflow: hidden;
        text-decoration: none;
        color: #fff;
        box-shadow: 0 4px 14px rgba(0, 0, 0, .12);
        transition: transform .2s, box-shadow .2s;
    }

    .event-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, .18);
    }

    .event-card-bg {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: .32;
        z-index: 0;
    }

    .event-card-content {
        position: relative;
        z-index: 1;
    }

    .event-card-tag {
        display: inline-block;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .5px;
        text-transform: uppercase;
        opacity: .9;
        margin-bottom: 6px;
    }

    .event-card-title {
        font-size: 20px;
        font-weight: 800;
        margin-bottom: 6px;
        line-height: 1.25;
    }

    .event-card-offer {
        font-size: 14px;
        font-weight: 700;
        color: #FFD54F;
        margin-bottom: 10px;
    }

    .event-card-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 700;
        background: rgba(255, 255, 255, .18);
        padding: 6px 14px;
        border-radius: 20px;
    }

    /* ===== PROMO BANNERS ===== */
    .promo-banners {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin: 8px 0 32px;
    }

    .promo-banner {
        border-radius: 10px;
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 110px;
        position: relative;
        overflow: hidden;
    }

    .promo-banner.apple {
        background: #EBF3FF;
    }

    .promo-banner.samsung {
        background: #F3E8FF;
    }

    .promo-banner.phu-kien {
        background: #FFF3E8;
    }

    .promo-banner .content .tag {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .promo-banner.apple .tag {
        color: #1565C0;
    }

    .promo-banner.samsung .tag {
        color: #7B1FA2;
    }

    .promo-banner.phu-kien .tag {
        color: #E65100;
    }

    .promo-banner .content h3 {
        font-size: 18px;
        font-weight: 800;
        margin: 4px 0 10px;
    }

    .promo-banner .content .btn-sm {
        padding: 6px 14px;
        font-size: 12px;
        border-radius: 5px;
        border: none;
        cursor: pointer;
        font-weight: 700;
    }

    .promo-banner.apple .btn-sm {
        background: #1565C0;
        color: #fff;
    }

    .promo-banner.samsung .btn-sm {
        background: #7B1FA2;
        color: #fff;
    }

    .promo-banner.phu-kien .btn-sm {
        background: #E65100;
        color: #fff;
    }

    .promo-img {
        width: 90px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        background: rgba(255, 255, 255, .5);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ccc;
    }

    /* ===== NEWS ===== */
    .news-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }

    .news-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        overflow: hidden;
    }

    .news-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, .1);
    }

    .news-card-img {
        height: 160px;
        background: #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ccc;
    }

    .news-card-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .news-card-body {
        padding: 12px;
    }

    .news-card-title {
        font-size: 13px;
        font-weight: 600;
        line-height: 1.45;
        margin-bottom: 6px;
    }

    .news-card-excerpt {
        font-size: 12px;
        color: #777;
        line-height: 1.5;
        margin-bottom: 8px;
    }

    .news-card-meta {
        font-size: 11px;
        color: #aaa;
    }
</style>
@endpush

@section('content')

{{-- HERO SLIDER --}}
@if($banners->isNotEmpty())
<section class="hero">
    @if($banners->count() > 1)
    <button class="hero-arrow left" id="heroPrev"><i class="fas fa-chevron-left"></i></button>
    @endif

    @foreach($banners as $i => $banner)
    @if($banner->isImageOnly())
    <div class="hero-inner hero-slide hero-slide-image {{ $i === 0 ? 'active' : '' }}" data-slide="{{ $i }}" style="{{ $i === 0 ? '' : 'display:none' }}">
        @if($banner->button_link)
        <a href="{{ $banner->button_link }}" style="display:block;width:100%;height:100%">
            @endif
            @if($banner->image)
            <img src="{{ $banner->image }}" alt="banner" style="width:100%;height:100%;object-fit:cover;display:block">
            @else
            <div class="hero-img-placeholder" style="width:100%;height:100%"><i class="fas fa-image fa-2x"></i></div>
            @endif
            @if($banner->button_link)
        </a>
        @endif
    </div>
    @else
    <div class="hero-inner hero-slide {{ $i === 0 ? 'active' : '' }}" data-slide="{{ $i }}"
        style="{{ $i === 0 ? '' : 'display:none' }}{{ $banner->bg_color ? ' background:'.$banner->bg_color.';' : '' }}">
        <div class="hero-content" style="{{ $banner->text_color ? 'color:'.$banner->text_color.';' : '' }}">
            @if($banner->label)
            <div class="hero-label" style="{{ $banner->text_color ? 'color:'.$banner->text_color.';opacity:.85;' : '' }}">{{ $banner->label }}</div>
            @endif
            @if($banner->title)
            <h1 class="hero-title">{!! nl2br(e($banner->title)) !!}</h1>
            @endif
            @if($banner->description)
            <p class="hero-desc" style="{{ $banner->text_color ? 'color:'.$banner->text_color.';opacity:.8;' : '' }}">{{ $banner->description }}</p>
            @endif
            @if($banner->price_text)
            <div class="hero-price">{{ $banner->price_text }}</div>
            @endif
            @if($banner->button_text)
            <a href="{{ $banner->button_link ?: '#' }}" class="btn btn-primary" style="width:fit-content">{{ $banner->button_text }}</a>
            @endif
        </div>
        <div class="hero-img">
            @if($banner->image)
            <img src="{{ $banner->image }}" alt="{{ $banner->title }}">
            @else
            <div class="hero-img-placeholder"><i class="fas fa-image fa-2x"></i></div>
            @endif
        </div>
    </div>
    @endif
    @endforeach

    @if($banners->count() > 1)
    <button class="hero-arrow right" id="heroNext"><i class="fas fa-chevron-right"></i></button>
    <div class="hero-dots">
        @foreach($banners as $i => $banner)
        <div class="hero-dot {{ $i === 0 ? 'active' : '' }}" data-slide="{{ $i }}"></div>
        @endforeach
    </div>
    @endif
</section>
@endif

{{-- EVENTS / KHUYẾN MÃI THEO MÙA --}}
@if(isset($events) && $events->isNotEmpty())
<div class="container">
    <div class="event-strip">
        @foreach($events as $event)
        <a href="{{ $event->button_link ?: '#' }}" class="event-card"
            style="background:{{ $event->bg_color ?: '#C62828' }};color:{{ $event->text_color ?: '#FFFFFF' }}">
            @if($event->image)
            <img src="{{ $event->image }}" class="event-card-bg" alt="{{ $event->title }}">
            @endif
            <div class="event-card-content">
                @if($event->tag)
                <span class="event-card-tag">{{ $event->tag }}</span>
                @endif
                <h3 class="event-card-title">{{ $event->title }}</h3>
                @if($event->offer_text)
                <div class="event-card-offer">{{ $event->offer_text }}</div>
                @endif
                @if($event->button_text)
                <span class="event-card-btn">{{ $event->button_text }} <i class="fas fa-arrow-right"></i></span>
                @endif
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif

{{-- TRUST BAR --}}
<div class="trust-bar">
    <div class="inner">
        <div class="trust-bar-item"><i class="fas fa-truck"></i>
            <div><b>Giao hàng miễn phí</b><span>Đơn hàng từ 500k</span></div>
        </div>
        <div class="trust-bar-item"><i class="fas fa-shield-alt"></i>
            <div><b>Chính hãng 100%</b><span>Bảo hành toàn quốc</span></div>
        </div>
        <div class="trust-bar-item"><i class="fas fa-sync-alt"></i>
            <div><b>Đổi trả dễ dàng</b><span>Trong vòng 30 ngày</span></div>
        </div>
        <div class="trust-bar-item"><i class="fas fa-credit-card"></i>
            <div><b>Trả góp 0%</b><span>Thủ tục nhanh chóng</span></div>
        </div>
        <div class="trust-bar-item"><i class="fas fa-headset"></i>
            <div><b>Hỗ trợ 24/7</b><span>Hotline: 1900 1234</span></div>
        </div>
    </div>
</div>

{{-- NEW PRODUCTS --}}
<div class="container">
    <section class="section">
        <div class="section-header">
            <h2 class="section-title">Sản phẩm mới nhất</h2>
            <a href="{{ route('products.index') }}" class="section-link">Xem tất cả →</a>
        </div>
        <div class="products-grid">
            @forelse($newProducts ?? [] as $product)
            <a href="{{ route('products.show', $product->slug) }}" class="product-card">
                <div class="product-card-img">
                    @if($product->image)
                    <img src="{{ $product->image }}" alt="{{ $product->name }}">
                    @else
                    <i class="fas fa-image fa-2x" style="color:#ccc"></i>
                    @endif
                </div>
                <div class="product-card-body">
                    <div class="product-card-name">{{ $product->name }}</div>
                    <div><span class="product-card-price">{{ number_format($product->price) }}đ</span></div>
                    <div class="stars">★★★★★ <span class="review-count">({{ $product->reviews_count ?? 0 }})</span></div>
                </div>
            </a>
            @empty
            @foreach([
            ['iPhone 15 Pro Max 256GB Titan','29.990.000','45'],
            ['Samsung Galaxy S24 Ultra 5G','26.490.000','32'],
            ['Xiaomi 14 Pro Leica','18.990.000','27'],
            ['MacBook Air M3 2024','27.890.000','78'],
            ] as $i => $p)
            <a href="#" class="product-card">
                @if($i === 0)<span class="badge-tag">MỚI</span>@endif
                <span class="wish"><i class="far fa-heart"></i></span>
                <div class="product-card-img img-placeholder"><i class="fas fa-image"></i></div>
                <div class="product-card-body">
                    <div class="product-card-name">{{ $p[0] }}</div>
                    <div><span class="product-card-price">{{ $p[1] }}đ</span></div>
                    <div class="stars">★★★★★ <span class="review-count">({{ $p[2] }})</span></div>
                </div>
            </a>
            @endforeach
            @endforelse
        </div>
    </section>

    {{-- PROMO BANNERS --}}
    <div class="promo-banners">
        <div class="promo-banner apple">
            <div class="content">
                <div class="tag">THẾ GIỚI APPLE</div>
                <h3>Giảm đến 4TR</h3>
                <button class="btn-sm">Săn Ngay</button>
            </div>
            <div class="promo-img img-placeholder"><i class="fas fa-image"></i></div>
        </div>
        <div class="promo-banner samsung">
            <div class="content">
                <div class="tag">SAMSUNG STORE</div>
                <h3>Thu cũ đổi mới</h3>
                <button class="btn-sm">Xem Thêm</button>
            </div>
            <div class="promo-img img-placeholder"><i class="fas fa-image"></i></div>
        </div>
        <div class="promo-banner phu-kien">
            <div class="content">
                <div class="tag">PHỤ KIỆN CÔNG NGHỆ</div>
                <h3>Mua 1 tặng 1</h3>
                <button class="btn-sm">Mua Ngay</button>
            </div>
            <div class="promo-img img-placeholder"><i class="fas fa-image"></i></div>
        </div>
    </div>

    {{-- NEWS --}}
    <section class="section" style="border-top:1px solid #f0f0f0; padding-top:32px">
        <div class="section-header">
            <h2 class="section-title">Tin tức công nghệ</h2>
            <a href="{{ route('news.index') }}" class="section-link">Xem tin mới nhất →</a>
        </div>
        <div class="news-grid">
            @forelse($latestNews ?? [] as $news)
            <a href="{{ route('news.show', $news->slug) }}" class="news-card">
                <div class="news-card-img">
                    @if($news->thumbnail)<img src="{{ $news->thumbnail }}" alt="{{ $news->title }}">
                    @else<i class="fas fa-image fa-2x"></i>@endif
                </div>
                <div class="news-card-body">
                    <div class="news-card-title">{{ $news->title }}</div>
                    <div class="news-card-excerpt">{{ Str::limit($news->excerpt, 80) }}</div>
                    <div class="news-card-meta">{{ $news->created_at->diffForHumans() }} · {{ number_format($news->views ?? 0) }} lượt xem</div>
                </div>
            </a>
            @empty
            @foreach([
            ['Đánh giá chi tiết iPhone 15 Pro Max: Titan thực sự khác biệt?','Sau một tháng sử dụng, khung viền titan mang lại cảm giác nhẹ hơn hẳn...','2 giờ trước · 343 lượt xem'],
            ['Samsung Galaxy S24 ra mắt: Trí tuệ nhân tạo Galaxy AI là tâm điểm','Những tính năng dịch thuật trực tiếp và chỉnh sửa ảnh bằng AI gây ấn tượng...','5 giờ trước · 1.2k lượt xem'],
            ['Lộ diện thiết kế iPad Pro M3 với màn hình OLED siêu mỏng','Các báo cáo mới nhất cho thấy Apple sẽ nâng cấp màn hình OLED cho dòng Pro...','1 ngày trước · 890 lượt xem'],
            ['5 mẹo tiết kiệm pin cực hay cho Android 14 bạn nên biết','Tối ưu hóa cài đặt hệ thống giúp điện thoại của bạn duy trì thời lượng pin...','2 ngày trước · 3.4k lượt xem'],
            ] as $n)
            <a href="#" class="news-card">
                <div class="news-card-img img-placeholder"><i class="fas fa-image fa-2x"></i></div>
                <div class="news-card-body">
                    <div class="news-card-title">{{ $n[0] }}</div>
                    <div class="news-card-excerpt">{{ $n[1] }}</div>
                    <div class="news-card-meta">{{ $n[2] }}</div>
                </div>
            </a>
            @endforeach
            @endforelse
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    // Hero slider (banner trang chủ)
    (function() {
        const slides = document.querySelectorAll('.hero-slide');
        const dots = document.querySelectorAll('.hero-dot');
        if (slides.length <= 1) return;

        let current = 0;
        let autoTimer;

        function showSlide(index) {
            slides.forEach((s, i) => s.style.display = i === index ? '' : 'none');
            dots.forEach((d, i) => d.classList.toggle('active', i === index));
            current = index;
        }

        function nextSlide() {
            showSlide((current + 1) % slides.length);
        }

        function prevSlide() {
            showSlide((current - 1 + slides.length) % slides.length);
        }

        function resetAutoplay() {
            clearInterval(autoTimer);
            autoTimer = setInterval(nextSlide, 5000);
        }

        document.getElementById('heroNext')?.addEventListener('click', () => {
            nextSlide();
            resetAutoplay();
        });
        document.getElementById('heroPrev')?.addEventListener('click', () => {
            prevSlide();
            resetAutoplay();
        });
        dots.forEach((dot, i) => dot.addEventListener('click', () => {
            showSlide(i);
            resetAutoplay();
        }));

        resetAutoplay();
    })();
</script>
@endpush