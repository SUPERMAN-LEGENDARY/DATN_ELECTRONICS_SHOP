@extends('layouts.app')
@section('title', 'ElectronicShop - Trang chủ')
@php $showSearch = true; @endphp

@push('styles')
<style>
    /* ===== LAYOUT WRAPPER ===== */
    .page-body {
        background: #f0f0f0;
        min-height: 100vh;
        padding-bottom: 40px;
    }

    /* ===== HERO ===== */
    .hero {
        position: relative;
        border-radius: 0;
        overflow: hidden;
        margin: 0 0 8px;
        background: #dce8fb;
    }

    .hero-inner {
        display: grid;
        grid-template-columns: 42% 58%;
        min-height: 340px;
    }

    .hero-content {
        padding: 36px 32px 36px 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 8px;
    }

    .hero-label {
        color: #2563eb;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .6px;
    }

    .hero-title {
        font-size: 28px;
        font-weight: 800;
        line-height: 1.25;
        color: #111827;
        margin: 0;
    }

    .hero-desc {
        color: #6b7280;
        font-size: 13px;
        line-height: 1.6;
        margin: 0;
        max-width: 280px;
    }

    .hero-price {
        color: #e53935;
        font-size: 20px;
        font-weight: 800;
        margin-top: 4px;
    }

    .hero-content .btn-primary {
        background: #2563eb;
        border: none;
        border-radius: 6px;
        font-weight: 700;
        font-size: 12px;
        padding: 9px 20px;
        color: #fff;
        cursor: pointer;
        width: fit-content;
        text-decoration: none;
        display: inline-block;
        margin-top: 4px;
    }

    .hero-content .btn-primary:hover {
        background: #1d4ed8;
    }

    .hero-img {
        height: 100%;
        overflow: hidden;
    }

    .hero-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .hero-img-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #bfdbfe;
        font-size: 40px;
        background: #cfe2fb;
    }

    /* Arrow buttons */
    .hero-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #fff;
        border: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #374151;
        cursor: pointer;
        z-index: 3;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .12);
        font-size: 11px;
    }

    .hero-arrow.left {
        left: 20px;
    }

    .hero-arrow.right {
        right: 20px;
    }

    .hero-dots {
        position: absolute;
        left: 40px;
        bottom: 18px;
        display: flex;
        gap: 5px;
        z-index: 3;
    }

    .hero-dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: #bfdbfe;
        cursor: pointer;
        transition: .2s;
    }

    .hero-dot.active {
        width: 16px;
        border-radius: 3px;
        background: #2563eb;
    }

    /* ===== TRUST BAR ===== */
    .trust-bar {
        background: #fff;
        margin-bottom: 8px;
    }

    .trust-bar .inner {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        padding: 10px 12px;
        gap: 8px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .trust-bar-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 4px 6px;
    }

    .trust-bar-item .tbi-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }

    .trust-bar-item div.tbi-text {
        display: flex;
        flex-direction: column;
        gap: 1px;
    }

    .trust-bar-item b {
        font-size: 12px;
        color: #1e293b;
        font-weight: 700;
    }

    .trust-bar-item span {
        font-size: 10.5px;
        color: #9ca3af;
    }

    .trust-bar-item:nth-child(1) .tbi-icon {
        background: #eff6ff;
        color: #2563eb;
    }

    .trust-bar-item:nth-child(2) .tbi-icon {
        background: #dcfce7;
        color: #16a34a;
    }

    .trust-bar-item:nth-child(3) .tbi-icon {
        background: #f3e8ff;
        color: #7c3aed;
    }

    .trust-bar-item:nth-child(4) .tbi-icon {
        background: #ffedd5;
        color: #f97316;
    }

    .trust-bar-item:nth-child(5) .tbi-icon {
        background: #fee2e2;
        color: #ef4444;
    }

    /* ===== TWO-COLUMN MAIN LAYOUT ===== */
    /* (ảnh thực tế dùng layout full-width dọc, không có sidebar) */

    /* ===== SECTION ===== */
    .section {
        background: #fff;
        border-radius: 4px;
        padding: 16px;
        margin-bottom: 8px;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 14px;
        padding-bottom: 10px;
        border-bottom: 1px solid #f3f4f6;
    }

    .section-title {
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
        text-transform: uppercase;
        letter-spacing: .3px;
        margin: 0;
        padding-left: 9px;
        border-left: 3px solid #2563eb;
        line-height: 1.4;
    }

    .section-link {
        color: #2563eb;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
    }

    .section-link:hover {
        text-decoration: underline;
    }

    /* ===== PRODUCT GRID ===== */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 10px;
    }

    .product-card {
        position: relative;
        background: #fff;
        border: 1px solid #ebebeb;
        border-radius: 6px;
        overflow: hidden;
        text-decoration: none;
        color: #000;
        transition: box-shadow .25s, transform .25s;
        display: block;
    }

    .product-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, .1);
    }

    .product-card-img {
        height: 160px;
        background: #fafafa;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .product-card-img img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 8px;
    }

    .product-card-body {
        padding: 8px;
        border-top: 1px solid #f3f4f6;
    }

    .product-card-name {
        font-size: 12.5px;
        color: #374151;
        min-height: 36px;
        line-height: 1.4;
        display: -webkit-box;
        /* -webkit-line-clamp:2; */
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-card-price {
        color: #e53935;
        font-size: 14px;
        font-weight: 700;
        display: block;
        margin-top: 4px;
    }

    .stars {
        color: #f59e0b;
        font-size: 11px;
    }

    .review-count {
        color: #9ca3af;
        margin-left: 2px;
        font-size: 11px;
    }

    .badge-tag {
        position: absolute;
        top: 6px;
        left: 6px;
        background: #e53935;
        color: #fff;
        font-size: 9px;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 3px;
        text-transform: uppercase;
        letter-spacing: .3px;
        z-index: 2;
    }

    .wish {
        position: absolute;
        top: 6px;
        right: 6px;
        width: 24px;
        height: 24px;
        background: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
        font-size: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .15);
        z-index: 2;
    }

    /* ===== IMAGE PLACEHOLDER ===== */
    .img-placeholder {
        background: #f3f4f6;
        background-image:
            linear-gradient(45deg, transparent calc(50% - 1px), #e5e7eb calc(50% - 1px), #e5e7eb calc(50% + 1px), transparent calc(50% + 1px)),
            linear-gradient(-45deg, transparent calc(50% - 1px), #e5e7eb calc(50% - 1px), #e5e7eb calc(50% + 1px), transparent calc(50% + 1px));
        color: #d1d5db;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* ===== PROMO BANNERS ===== */
    .promo-banners {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        margin-bottom: 8px;
    }

    .promo-banner {
        border-radius: 6px;
        padding: 16px 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        overflow: hidden;
    }

    .promo-banner .content {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .promo-banner .tag {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .promo-banner h3 {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
    }

    .promo-banner .btn-sm {
        border: none;
        padding: 5px 12px;
        border-radius: 5px;
        font-size: 11px;
        font-weight: 600;
        color: #fff;
        cursor: pointer;
        align-self: flex-start;
    }

    .promo-img {
        width: 80px;
        height: 80px;
        border-radius: 6px;
        flex-shrink: 0;
    }

    .promo-banner.apple {
        background: #eff6ff;
    }

    .promo-banner.apple .tag {
        color: #2563eb;
    }

    .promo-banner.apple .btn-sm {
        background: #2563eb;
    }

    .promo-banner.samsung {
        background: #f3e8ff;
    }

    .promo-banner.samsung .tag {
        color: #7c3aed;
    }

    .promo-banner.samsung .btn-sm {
        background: #7c3aed;
    }

    .promo-banner.phu-kien {
        background: #fff7ed;
    }

    .promo-banner.phu-kien .tag {
        color: #f97316;
    }

    .promo-banner.phu-kien .btn-sm {
        background: #f97316;
    }

    /* ===== NEWS ===== */
    .news-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
    }

    .news-card {
        background: #fff;
        border: 1px solid #ebebeb;
        border-radius: 6px;
        overflow: hidden;
        text-decoration: none;
        color: #000;
        transition: box-shadow .25s, transform .25s;
        display: block;
    }

    .news-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 14px rgba(0, 0, 0, .08);
    }

    .news-card-img {
        height: 160px;
        overflow: hidden;
    }

    .news-card-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .news-card-body {
        padding: 10px;
    }

    .news-card-title {
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
        line-height: 1.5;
        min-height: 40px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .news-card-excerpt {
        color: #6b7280;
        font-size: 12px;
        line-height: 1.5;
        margin: 5px 0 6px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .news-card-meta {
        color: #9ca3af;
        font-size: 11px;
    }

    /* ===== FOOTER ===== */
    .footer {
        margin-top: 0;
        background: #fff;
        border-top: 1px solid #e5e7eb;
        padding: 32px 0;
    }

    .footer-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
    }

    /* ===== RESPONSIVE ===== */
    @media(max-width:1100px) {
        .products-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    @media(max-width:860px) {
        .products-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .news-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .promo-banners {
            grid-template-columns: 1fr;
        }

        .hero-inner {
            grid-template-columns: 1fr;
        }

        .trust-bar .inner {
            grid-template-columns: repeat(2, 1fr);
        }

        .footer-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media(max-width:540px) {
        .products-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .news-grid {
            grid-template-columns: 1fr;
        }

        .trust-bar .inner {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
@endpush

@section('content')
<div class="page-body">

    {{-- ===== HERO SLIDER ===== --}}
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
            style="{{ $i === 0 ? '' : 'display:none' }}{{ $banner->bg_color ? ';background:'.$banner->bg_color.';' : '' }}">
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
                <a href="{{ $banner->button_link ?: '#' }}" class="btn-primary" style="width:fit-content">{{ $banner->button_text }}</a>
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

    {{-- ===== TRUST BAR ===== --}}
    <div class="trust-bar">
        <div class="inner">
            <div class="trust-bar-item">
                <div class="tbi-icon"><i class="fas fa-truck"></i></div>
                <div class="tbi-text"><b>Giao hàng miễn phí</b><span>Đơn hàng từ 500k</span></div>
            </div>
            <div class="trust-bar-item">
                <div class="tbi-icon"><i class="fas fa-shield-alt"></i></div>
                <div class="tbi-text"><b>Chính hãng 100%</b><span>Bảo hành toàn quốc</span></div>
            </div>
            <div class="trust-bar-item">
                <div class="tbi-icon"><i class="fas fa-sync-alt"></i></div>
                <div class="tbi-text"><b>Đổi trả dễ dàng</b><span>Trong vòng 30 ngày</span></div>
            </div>
            <div class="trust-bar-item">
                <div class="tbi-icon"><i class="fas fa-credit-card"></i></div>
                <div class="tbi-text"><b>Trả góp 0%</b><span>Thủ tục nhanh chóng</span></div>
            </div>
            <div class="trust-bar-item">
                <div class="tbi-icon"><i class="fas fa-headset"></i></div>
                <div class="tbi-text"><b>Hỗ trợ 24/7</b><span>Hotline: 1900 1234</span></div>
            </div>
        </div>
    </div>

    <div class="container">

        {{-- ===== SẢN PHẨM MỚI NHẤT ===== --}}
        <section class="section">
            <div class="section-header">
                <h2 class="section-title">Sản phẩm mới nhất</h2>
                <a href="{{ route('products.index') }}" class="section-link">
                    Xem tất cả →
                </a>
            </div>

            <div class="products-grid">

                @forelse($newProducts as $product)
                <a href="{{ route('products.show', $product->slug) }}"
                    class="product-card">

                    <span class="wish">
                        <i class="far fa-heart"></i>
                    </span>

                    <div class="product-card-img">

                        @if($product->first_image)
                        <img src="{{ $product->first_image }}" alt="{{ $product->name }}">
                        @else
                        <div class="img-placeholder"
                            style="width:100%;height:100%">
                            <i class="fas fa-image"></i>
                        </div>
                        @endif

                    </div>

                    <div class="product-card-body">

                        <div class="product-card-name">
                            {{ $product->name }}
                        </div>

                        @if($product->brand)
                        <div style="font-size:11px;color:#6b7280;margin-top:4px">
                            {{ $product->brand->name }}
                        </div>
                        @endif

                        <span class="product-card-price">
                            {{ number_format($product->price) }}đ
                        </span>

                        <div class="stars">
                            ★★★★★
                            <span class="review-count">
                                ({{ $product->reviews_count ?? 0 }})
                            </span>
                        </div>

                    </div>

                </a>

                @empty

                <div style="grid-column:1/-1;padding:40px;text-align:center">
                    Chưa có sản phẩm nào.
                </div>

                @endforelse

            </div>
        </section>
        {{-- ===== PROMO BANNERS ===== --}}
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

        {{-- ===== TIN TỨC CÔNG NGHỆ ===== --}}
        @if($latestNews->isNotEmpty())
        <section class="section">

            <div class="section-header">
                <h2 class="section-title">Tin tức công nghệ</h2>

                <a href="{{ route('news.index') }}"
                    class="section-link">
                    Xem tin mới nhất →
                </a>
            </div>

            <div class="news-grid">

                @foreach($latestNews as $news)

                <a href="{{ route('news.show', $news->slug) }}"
                    class="news-card">

                    <div class="news-card-img">

                        @if(!empty($news->thumbnail))
                        <img src="{{ $news->thumbnail }}"
                            alt="{{ $news->title }}">
                        @else
                        <div class="img-placeholder"
                            style="height:100%">
                            <i class="fas fa-image fa-2x"></i>
                        </div>
                        @endif

                    </div>

                    <div class="news-card-body">

                        <div class="news-card-title">
                            {{ $news->title }}
                        </div>

                        <div class="news-card-excerpt">
                            {{ \Illuminate\Support\Str::limit(strip_tags($news->content), 100) }}
                        </div>

                        <div class="news-card-meta">

                            @if($news->published_at)
                            {{ $news->published_at->diffForHumans() }}
                            @endif

                            · {{ number_format($news->views ?? 0) }} lượt xem

                            @if($news->category)
                            · {{ $news->category->name }}
                            @endif

                        </div>

                    </div>

                </a>

                @endforeach

            </div>

        </section>
        @endif

    </div><!-- /.container -->
</div><!-- /.page-body -->
@endsection

@push('scripts')
<script>
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