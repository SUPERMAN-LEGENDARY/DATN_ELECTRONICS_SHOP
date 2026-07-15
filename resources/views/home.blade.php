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

    .page-body .container {
        padding-top: 16px;
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

    /* ===== EVENTS (dưới banner) - khớp với xem trước ở trang admin ===== */
    .events-strip {
        max-width: 1200px;
        margin: 14px auto 0;
        padding: 0 15px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 14px;
    }

    .event-card {
        position: relative;
        display: flex;
        align-items: center;
        min-height: 140px;
        border-radius: 12px;
        overflow: hidden;
        text-decoration: none;
        background: #263238;
        color: #fff;
        padding: 22px 26px;
        transition: transform .2s, box-shadow .2s;
    }

    .event-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(0, 0, 0, .16);
    }

    .event-card-bg {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: .35;
    }

    .event-card-body {
        position: relative;
        z-index: 1;
        min-width: 0;
    }

    .event-card-tag {
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: .85;
        margin-bottom: 6px;
    }

    .event-card-title {
        font-size: 21px;
        font-weight: 800;
        line-height: 1.25;
        margin-bottom: 6px;
        white-space: normal;
    }

    .event-card-offer {
        font-size: 14.5px;
        font-weight: 700;
        color: #FFD54F;
    }

    /* ===== TRUST BAR ===== */
    .trust-bar {
        background: #fff;
        margin: 14px auto 0;
        max-width: 1200px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .05);
    }

    .trust-bar .inner {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        padding: 14px 16px;
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

    .trust-bar-item .tbi-icon {
        background: #eff6ff;
        color: #2563eb;
    }

    /* ===== TWO-COLUMN MAIN LAYOUT ===== */
    /* (ảnh thực tế dùng layout full-width dọc, không có sidebar) */

    /* ===== SECTION ===== */
    .section {
        background: #fff;
        border-radius: 12px;
        padding: 20px 22px;
        margin-bottom: 16px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .05);
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f3f4f6;
    }

    .section-title {
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
        text-transform: uppercase;
        letter-spacing: .3px;
        margin: 0;
        padding-left: 10px;
        border-left: 3px solid #2563eb;
        line-height: 1.4;
    }

    .section-link {
        color: #2563eb;
        font-size: 12.5px;
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
        gap: 16px;
    }

    .product-card {
        position: relative;
        background: #fff;
        border: 1px solid #ebebeb;
        border-radius: 10px;
        overflow: hidden;
        text-decoration: none;
        color: #000;
        transition: box-shadow .25s, transform .25s;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .product-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, .1);
        border-color: transparent;
    }

    .product-card-img {
        height: 170px;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
    }

    .product-card-img img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 10px;
    }

    .product-card-body {
        padding: 12px 12px 14px;
        border-top: 1px solid #f3f4f6;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .product-card-name {
        font-size: 13px;
        color: #374151;
        min-height: 36px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
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
        margin-top: auto;
        padding-top: 4px;
    }

    .review-count {
        color: #9ca3af;
        margin-left: 2px;
        font-size: 11px;
    }

    .badge-tag {
        position: absolute;
        top: 8px;
        left: 8px;
        background: #e53935;
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        padding: 3px 7px;
        border-radius: 5px;
        text-transform: uppercase;
        letter-spacing: .3px;
        z-index: 2;
    }

    .wish {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 26px;
        height: 26px;
        background: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
        font-size: 12px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .15);
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
        gap: 14px;
        margin-bottom: 16px;
    }

    .promo-banner {
        border-radius: 12px;
        padding: 20px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        overflow: hidden;
    }

    .promo-banner .content {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .promo-banner .tag {
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .promo-banner h3 {
        margin: 0;
        font-size: 15.5px;
        font-weight: 700;
        color: #1e293b;
    }

    .promo-banner .btn-sm {
        border: none;
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 11.5px;
        font-weight: 600;
        color: #fff;
        cursor: pointer;
        align-self: flex-start;
    }

    .promo-img {
        width: 84px;
        height: 84px;
        border-radius: 14px;
        flex-shrink: 0;
        object-fit: contain;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, .06);
        box-shadow: 0 2px 8px rgba(0, 0, 0, .06);
        padding: 8px;
        box-sizing: border-box;
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

    .promo-banner.bao-hanh {
        background: #fff7ed;
    }

    .promo-banner.bao-hanh .tag {
        color: #f97316;
    }

    .promo-banner.bao-hanh .btn-sm {
        background: #f97316;
    }

    /* ===== NEWS ===== */
    .news-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }

    .news-card {
        background: #fff;
        border: 1px solid #ebebeb;
        border-radius: 10px;
        overflow: hidden;
        text-decoration: none;
        color: #000;
        transition: box-shadow .25s, transform .25s;
        display: block;
    }

    .news-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 18px rgba(0, 0, 0, .08);
        border-color: transparent;
    }

    .news-card-img {
        height: 160px;
        overflow: hidden;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .news-card-img img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 6px;
        box-sizing: border-box;
    }

    .news-card-body {
        padding: 14px;
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
                <h1 class="hero-title" style="{{ $banner->text_color ? 'color:'.$banner->text_color.';' : '' }}">{!! nl2br(e($banner->title)) !!}</h1>
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

    {{-- ===== SỰ KIỆN / KHUYẾN MÃI (hiển thị ngay dưới banner, khớp xem trước ở trang admin) ===== --}}
    @if(isset($events) && $events->isNotEmpty())
    <div class="events-strip">
        @foreach($events as $event)
        <a href="{{ $event->button_link ?: '#' }}" class="event-card"
            style="background:{{ $event->bg_color ?: '#263238' }};color:{{ $event->text_color ?: '#fff' }}">
            @if($event->image)
            <img src="{{ $event->image }}" alt="{{ $event->title }}" class="event-card-bg">
            @endif
            <div class="event-card-body">
                @if($event->tag)
                <div class="event-card-tag">{{ $event->tag }}</div>
                @endif
                <div class="event-card-title">{{ $event->title }}</div>
                @if($event->offer_text)
                <div class="event-card-offer">{{ $event->offer_text }}</div>
                @endif
            </div>
        </a>
        @endforeach
    </div>
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

        {{-- ===== GỢI Ý DÀNH CHO BẠN (AI cá nhân hóa) ===== --}}
        @auth
        @if(!empty($suggestedProducts) && $suggestedProducts->isNotEmpty())
        <section class="section">
            <div class="section-header">
                <h2 class="section-title">Gợi ý dành cho bạn</h2>
            </div>
            <div class="products-grid">
                @foreach($suggestedProducts as $product)
                <a href="{{ route('products.show', ['slug' => $product->slug, 'from' => 'suggestion', 'via' => 'homepage']) }}"
                    class="product-card">
                    <span class="wish"><i class="far fa-heart"></i></span>
                    <div class="product-card-img">
                        @if($product->first_image)
                        <img src="{{ $product->first_image }}" alt="{{ $product->name }}">
                        @else
                        <div class="img-placeholder"><i class="fas fa-image"></i></div>
                        @endif
                    </div>
                    <div class="product-card-body">
                        <div class="product-card-name">{{ $product->name }}</div>
                        <div class="product-card-price">{{ number_format($product->sale_price) }}đ</div>
                    </div>
                </a>
                @endforeach
            </div>
        </section>
        @endif
        @endauth

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

                        <div style="font-size:11px;color:#6b7280;margin-top:4px;min-height:14px">
                            {{ $product->brand->name ?? '' }}
                        </div>

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
                <img class="promo-img" src="{{ asset('images/promo-apple.png') }}" alt="Thế giới Apple">
            </div>
            <div class="promo-banner samsung">
                <div class="content">
                    <div class="tag">SAMSUNG STORE</div>
                    <h3>Thu cũ đổi mới</h3>
                    <button class="btn-sm">Xem Thêm</button>
                </div>
                <img class="promo-img" src="{{ asset('images/promo-samsung.png') }}" alt="Samsung Store">
            </div>
            <div class="promo-banner bao-hanh">
                <div class="content">
                    <div class="tag">BẢO HÀNH CHÍNH HÃNG</div>
                    <h3>An tâm 12 tháng</h3>
                    <button class="btn-sm">Xem Chi Tiết</button>
                </div>
                <img class="promo-img" src="{{ asset('images/promo-baohanh.png') }}" alt="Bảo hành chính hãng">
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
                        <img src="{{ asset('storage/' . $news->thumbnail) }}"
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