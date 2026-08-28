@extends('layouts.app')
@section('title', $product->name . ' - ElectronicShop')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
<style>
/* ============================================================
   SAMSUNG.COM DESIGN TOKENS
   ============================================================ */
:root {
    --sam-black:  #000;
    --sam-white:  #fff;
    --sam-gray:   #f7f7f7;
    --sam-gray-2: #eaeaea;
    --sam-line:   #ddd;
    --sam-muted:  #666;
    --sam-blue:   #2189ff;
    --sam-navy:   #1428a0;
    --sam-star:   #f5a623;
    --sam-sale:   #d90000;
    --sam-ok:     #007a3d;
    --sam-font:   'Inter', 'SamsungOne', arial, sans-serif;
    --sam-head:   'Poppins', 'SamsungSharpSans', arial, sans-serif;
}

body {
    background: var(--sam-white);
    color: var(--sam-black);
    font-family: var(--sam-font);
    -webkit-font-smoothing: antialiased;
}

.product-detail-wrap {
    max-width: 1440px;
    margin: 0 auto;
    padding: 0 24px 80px;
}

/* ============================================================
   SCROLL REVEAL
   ============================================================ */
.reveal, .reveal-left { opacity: 0; transform: translateY(20px); transition: opacity .6s cubic-bezier(.33,0,.3,1), transform .6s cubic-bezier(.33,0,.3,1); }
.reveal-left { transform: translateX(-20px); }
.reveal.revealed, .reveal-left.revealed { opacity: 1; transform: none; }

.stagger-children > * { opacity: 0; transform: translateY(14px); transition: opacity .45s cubic-bezier(.33,0,.3,1), transform .45s cubic-bezier(.33,0,.3,1); }
.stagger-children.revealed > * { opacity: 1; transform: none; }
.stagger-children.revealed > *:nth-child(1) { transition-delay: .05s; }
.stagger-children.revealed > *:nth-child(2) { transition-delay: .1s; }
.stagger-children.revealed > *:nth-child(3) { transition-delay: .15s; }
.stagger-children.revealed > *:nth-child(4) { transition-delay: .2s; }
.stagger-children.revealed > *:nth-child(n+5) { transition-delay: .25s; }

/* ============================================================
   CTA — Samsung pill buttons
   ============================================================ */
.cta {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 40px;
    padding: 9px 24px 10px;
    border-radius: 20px;
    font-family: inherit;
    font-size: 14px;
    font-weight: 700;
    line-height: 1.4;
    text-decoration: none;
    cursor: pointer;
    border: 1px solid transparent;
    transition: background .2s, color .2s, border-color .2s, opacity .2s;
}
.cta--black { background: var(--sam-black); color: var(--sam-white); }
.cta--black:hover { background: #3d3d3d; color: var(--sam-white); }
.cta--blue { background: var(--sam-blue); color: var(--sam-white); }
.cta--blue:hover { background: #0d6fd8; color: var(--sam-white); }
.cta--outline { background: transparent; color: var(--sam-black); border-color: var(--sam-black); }
.cta--outline:hover { background: rgba(0,0,0,.05); }
.cta--ghost { background: var(--sam-white); color: var(--sam-black); border-color: var(--sam-line); }
.cta--ghost:hover { background: var(--sam-gray); }
.cta--lg { min-height: 48px; font-size: 15px; padding: 12px 28px; border-radius: 24px; }

/* ============================================================
   BREADCRUMB / TOP BAR
   ============================================================ */
.breadcrumb-row {
    font-size: 12px;
    color: var(--sam-muted);
    padding: 20px 0 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}
.breadcrumb-row a { color: var(--sam-muted); text-decoration: none; }
.breadcrumb-row a:hover { color: var(--sam-black); text-decoration: underline; }
.breadcrumb-row .crumb-current { color: var(--sam-black); font-weight: 500; }
.breadcrumb-actions { display: flex; align-items: center; gap: 8px; }

.btn-icon-text {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 16px;
    background: var(--sam-white);
    border: 1px solid var(--sam-line);
    border-radius: 20px;
    font-family: inherit;
    font-size: 13px;
    font-weight: 700;
    color: var(--sam-black);
    cursor: pointer;
    text-decoration: none;
    transition: border-color .2s, background .2s;
}
.btn-icon-text:hover { border-color: var(--sam-black); background: var(--sam-gray); }

.btn-wishlist-detail {
    width: 46px; height: 46px; border-radius: 12px;
    border: 1.5px solid rgba(239,68,68,.35);
    background: rgba(254,226,226,.5); color: #ef4444;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 18px; cursor: pointer;
    transition: background .2s, transform .15s, border-color .2s; flex-shrink: 0;
}
.btn-wishlist-detail:hover { background: rgba(239,68,68,.15); transform: scale(1.07); }
.btn-wishlist-detail.active { background: linear-gradient(135deg,#fca5a5,#ef4444); color: #fff; border-color: #ef4444; }

/* ============================================================
   FLASH MESSAGES
   ============================================================ */
.flash-success, .flash-error {
    padding: 12px 18px;
    border-radius: 8px;
    margin: 16px 0 0;
    font-size: 14px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
}
.flash-success { background: #eef7f1; color: var(--sam-ok); border: 1px solid #cfe7d8; }
.flash-error   { background: #fdeeee; color: var(--sam-sale); border: 1px solid #f5cccc; }

/* ============================================================
   PRODUCT MAIN GRID — gallery | info | sidebar
   ============================================================ */
.product-main {
    display: grid;
    grid-template-columns: minmax(0, 1.15fr) minmax(0, 1fr) 280px;
    gap: 40px;
    padding: 32px 0 64px;
    align-items: start;
}
@media (max-width: 1200px) { .product-main { grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); } .product-sidebar { display: none; } }
@media (max-width: 820px)  { .product-main { grid-template-columns: 1fr; gap: 32px; } }

/* ============================================================
   GALLERY — flat #f7f7f7 panel, radius 20px
   ============================================================ */
.gallery-main {
    background: var(--sam-gray);
    border-radius: 20px;
    height: 480px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
    margin-bottom: 16px;
}
@media (max-width: 820px) { .gallery-main { height: 340px; } }
.gallery-main img {
    max-width: 84%;
    max-height: 84%;
    object-fit: contain;
    transition: transform .4s cubic-bezier(.33,0,.3,1), opacity .3s;
}
.gallery-main:hover img { transform: scale(1.03); }

.discount-tag {
    position: absolute;
    top: 24px; left: 24px;
    background: transparent;
    color: var(--sam-sale);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .04em;
    text-transform: uppercase;
}

.gallery-thumbs { display: flex; gap: 8px; flex-wrap: wrap; }
.thumb {
    width: 64px; height: 64px;
    background: var(--sam-gray);
    border: 1px solid transparent;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: border-color .2s, background .2s;
}
.thumb:hover { background: var(--sam-gray-2); }
.thumb.active { border-color: var(--sam-black); background: var(--sam-white); }
.thumb img { width: 100%; height: 100%; object-fit: contain; padding: 6px; box-sizing: border-box; }

/* ============================================================
   PRODUCT INFO
   ============================================================ */
.product-info { background: transparent; padding: 0; }

.product-brand {
    font-size: 13px;
    font-weight: 700;
    color: var(--sam-blue);
    margin-bottom: 8px;
    display: block;
}

.product-name {
    font-family: var(--sam-head);
    font-size: 32px;
    font-weight: 700;
    letter-spacing: -.02em;
    line-height: 1.2;
    margin: 0 0 14px;
    color: var(--sam-black);
}
@media (max-width: 640px) { .product-name { font-size: 24px; } }

.product-rating {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: var(--sam-muted);
    margin-bottom: 24px;
    flex-wrap: wrap;
}
.product-rating .stars { color: var(--sam-star); letter-spacing: 1px; font-size: 14px; }
.product-rating .rating-num { color: var(--sam-black); font-weight: 700; }
.product-rating .divider { color: var(--sam-line); }

.price-block { padding: 20px 0 0; border-top: 1px solid var(--sam-line); }
.price-current { font-size: 32px; font-weight: 700; letter-spacing: -.02em; color: var(--sam-black); }
.price-old     { font-size: 15px; color: var(--sam-muted); text-decoration: line-through; margin-left: 12px; }
.price-pct     { font-size: 15px; color: var(--sam-sale); font-weight: 700; margin-left: 8px; }

.in-stock     { color: var(--sam-ok);   font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 6px; }
.out-of-stock { color: var(--sam-sale); font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 6px; }

/* ============================================================
   VARIANT OPTIONS — Samsung bordered selector cards
   ============================================================ */
#variantSelector { display: flex; flex-direction: row; flex-wrap: wrap; gap: 20px 32px; margin-bottom: 16px; } .option-group { flex: 0 1 auto; }

.option-group { margin: 0; }
.option-label { font-size: 14px; color: var(--sam-muted); margin-bottom: 10px; }
.option-label strong { color: var(--sam-black); font-weight: 700; }
.option-btns { display: flex; gap: 8px; flex-wrap: wrap; }

.opt-btn {
    padding: 11px 20px;
    min-width: 64px;
    background: var(--sam-white);
    border: 1px solid var(--sam-line);
    border-radius: 8px;
    font-family: inherit;
    font-size: 14px;
    font-weight: 400;
    color: var(--sam-black);
    cursor: pointer;
    transition: border-color .2s, box-shadow .2s, background .2s;
}
.opt-btn:hover:not(.opt-btn-disabled) { border-color: #999; }
.opt-btn.active {
    border-color: var(--sam-navy);
    box-shadow: inset 0 0 0 1px var(--sam-navy);
    font-weight: 700;
}
.opt-btn-disabled {
    color: #bbb;
    background: var(--sam-gray);
    border-color: #ececec;
    text-decoration: line-through;
    cursor: not-allowed;
    pointer-events: none;
}

#variantAlert { font-size: 13px; color: var(--sam-sale); font-weight: 500; }

.action-btns { display: flex; gap: 12px; margin-top: 28px; flex-wrap: wrap; }
.action-btns form { flex: 1 1 180px; }
.btn-add-cart { width: 100%; }
.btn-buy-now  { width: 100%; }

/* ============================================================
   SIDEBAR — benefits / commitments
   ============================================================ */
.product-sidebar { display: flex; flex-direction: column; gap: 12px; }

.benefit-box, .commit-box {
    background: var(--sam-gray);
    border-radius: 16px;
    padding: 22px;
}

.benefit-item { display: flex; gap: 12px; align-items: flex-start; margin-bottom: 18px; }
.benefit-item:last-child { margin-bottom: 0; }
.benefit-item .bi-icon {
    width: 32px; height: 32px;
    border-radius: 50%;
    background: var(--sam-white);
    color: var(--sam-black);
    font-size: 13px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.benefit-item .bi-title { font-size: 13px; font-weight: 700; color: var(--sam-black); margin-bottom: 2px; }
.benefit-item .bi-desc  { font-size: 12px; color: var(--sam-muted); line-height: 1.5; }

.commit-box h4 {
    font-size: 13px;
    font-weight: 700;
    color: var(--sam-black);
    margin: 0 0 14px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--sam-line);
}
.commit-item { display: flex; align-items: center; gap: 8px; margin-bottom: 10px; font-size: 13px; color: var(--sam-muted); }
.commit-item:last-child { margin-bottom: 0; }
.commit-item i { color: var(--sam-black); font-size: 12px; }

/* ============================================================
   TABS — Samsung underline nav
   ============================================================ */
.product-tabs {
    display: flex;
    gap: 32px;
    overflow-x: auto;
    border-bottom: 1px solid var(--sam-line);
    margin-bottom: 40px;
    scrollbar-width: none;
}
.product-tabs::-webkit-scrollbar { display: none; }

.tab-btn {
    padding: 14px 0 12px;
    background: none;
    border: none;
    border-bottom: 2px solid transparent;
    font-family: inherit;
    font-size: 16px;
    font-weight: 400;
    color: var(--sam-black);
    white-space: nowrap;
    cursor: pointer;
    margin-bottom: -1px;
    transition: border-color .2s, font-weight .2s;
}
.tab-btn:hover { border-bottom-color: var(--sam-line); }
.tab-btn.active { font-weight: 700; border-bottom-color: var(--sam-black); }

.tab-panel { display: none; }
.tab-panel.active { display: block; }
.tab-panel-inner { padding: 32px 0; }

/* ============================================================
   DESCRIPTION (CKEditor content)
   ============================================================ */
.product-description-content :where(h1,h2,h3,h4) { font-family: var(--sam-head); color: var(--sam-black); margin: 28px 0 14px; font-weight: 700; }
.product-description-content p { margin: 0 0 20px; }
.product-description-content ul, .product-description-content ol { margin: 0 0 20px 22px; }
.product-description-content img { max-width: 100%; border-radius: 12px; margin: 16px 0; }
.product-description-content table { border-collapse: collapse; margin: 14px 0; }
.product-description-content table td, .product-description-content table th { border: 1px solid var(--sam-line); padding: 8px 12px; }
.product-description-content a { color: var(--sam-blue); }

.empty-note { color: var(--sam-muted); font-size: 14px; padding: 32px 0; }

/* ============================================================
   SPECS TABLE
   ============================================================ */
.specs-table { width: 100%; border-collapse: collapse; font-size: 14px; max-width: 100%; margin: 0 auto; }
.specs-table td { padding: 16px 20px; border-bottom: 1px solid var(--sam-line); vertical-align: top; }
.specs-table tr:first-child td { border-top: 1px solid var(--sam-line); }
.specs-table td:first-child { color: var(--sam-muted); width: 30%; font-weight: 400; }
.specs-table td:last-child { color: var(--sam-black); font-weight: 500; }

/* ============================================================
   RATING SUMMARY
   ============================================================ */
.rating-summary {
    display: flex;
    align-items: center;
    gap: 48px;
    padding: 32px;
    background: var(--sam-gray);
    border-radius: 20px;
    margin-bottom: 32px;
    flex-wrap: wrap;
}
.rating-avg { text-align: center; }
.rating-avg .big { font-family: var(--sam-head); font-size: 56px; font-weight: 700; color: var(--sam-black); line-height: 1; letter-spacing: -.03em; }
.rating-avg .stars-lg { font-size: 18px; color: var(--sam-star); margin: 8px 0 4px; letter-spacing: 2px; }
.rating-avg small { color: var(--sam-muted); font-size: 13px; }

.rating-bars { flex: 1; min-width: 240px; }
.rating-bar-row { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; font-size: 13px; color: var(--sam-muted); }
.rating-bar-row .bar { flex: 1; height: 6px; background: var(--sam-white); border-radius: 3px; overflow: hidden; }
.rating-bar-row .bar-fill { height: 100%; background: var(--sam-star); border-radius: 3px; transition: width .8s cubic-bezier(.33,0,.3,1); }
.rating-bar-row .cnt { width: 32px; text-align: right; }

/* ============================================================
   REVIEWS
   ============================================================ */
.review-item { border-bottom: 1px solid var(--sam-line); padding: 22px 0; }
.review-item:first-of-type { border-top: 1px solid var(--sam-line); }
.review-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }
.review-user { font-weight: 700; font-size: 14px; color: var(--sam-black); }
.review-date { font-size: 12px; color: var(--sam-muted); }
.review-stars { color: var(--sam-star); font-size: 14px; letter-spacing: 1px; margin-bottom: 8px; }
.review-content { font-size: 14px; color: #333; line-height: 1.7; }

.review-form { background: var(--sam-gray); border-radius: 20px; padding: 32px; margin-top: 32px; }
.review-form h4 { font-family: var(--sam-head); font-size: 18px; font-weight: 700; margin: 0 0 20px; color: var(--sam-black); }
.form-label { font-size: 13px; font-weight: 700; display: block; margin-bottom: 8px; color: var(--sam-black); }

.star-rating { display: flex; gap: 6px; margin-bottom: 4px; flex-direction: row-reverse; justify-content: flex-end; }
.star-rating input { display: none; }
.star-rating label { font-size: 30px; color: #d4d4d4; cursor: pointer; transition: color .15s, transform .15s; line-height: 1; }
.star-rating label:hover { transform: scale(1.1); }
.star-rating input:checked ~ label,
.star-rating label:hover,
.star-rating label:hover ~ label { color: var(--sam-star); }

.form-textarea {
    width: 100%;
    border: 1px solid var(--sam-line);
    border-radius: 8px;
    padding: 12px 14px;
    font-family: inherit;
    font-size: 14px;
    color: var(--sam-black);
    background: var(--sam-white);
    outline: none;
    resize: vertical;
    transition: border-color .2s;
}
.form-textarea:focus { border-color: var(--sam-black); }
.field-error { color: var(--sam-sale); font-size: 12px; }

.notice-box {
    background: var(--sam-gray);
    border-radius: 16px;
    padding: 24px;
    margin-top: 32px;
    text-align: center;
    font-size: 14px;
    color: var(--sam-muted);
}
.notice-box a { color: var(--sam-blue); font-weight: 700; }

/* ============================================================
   RELATED PRODUCTS
   ============================================================ */
.related-section { margin-top: 80px; }
.related-header {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 12px;
}
.related-header h2 {
    font-family: var(--sam-head);
    font-size: 28px;
    font-weight: 700;
    letter-spacing: -.02em;
    color: var(--sam-black);
    margin: 0;
}
.related-header a { font-size: 14px; font-weight: 700; color: var(--sam-black); text-decoration: underline; }

.related-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; }
@media (max-width: 1100px) { .related-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 640px)  { .related-grid { grid-template-columns: repeat(2, 1fr); } }

.product-card {
    display: flex;
    flex-direction: column;
    height: 100%;
    background: var(--sam-gray);
    border-radius: 16px;
    padding: 20px;
    text-decoration: none;
    color: var(--sam-black);
    transition: background .2s;
}
.product-card:hover { background: #f1f1f1; }

.product-card-img {
    height: 150px;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 16px;
    flex-shrink: 0;
}
.product-card-img img {
    max-width: 100%; max-height: 100%;
    object-fit: contain;
    transition: transform .3s cubic-bezier(.33,0,.3,1);
}
.product-card:hover .product-card-img img { transform: scale(1.05); }

.product-card-body { display: flex; flex-direction: column; gap: 8px; flex: 1; }
.product-card-name {
    font-size: 15px; font-weight: 700; line-height: 1.45;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    min-height: 44px; color: var(--sam-black);
}
.product-card-price { font-size: 16px; font-weight: 700; color: var(--sam-black); margin-top: auto; }
.stars { font-size: 12px; color: var(--sam-star); letter-spacing: 1px; }
.stars .muted { color: var(--sam-muted); letter-spacing: 0; }
</style>
@endpush

@section('content')
<div class="product-detail-wrap">

    {{-- Breadcrumb --}}
    <div class="breadcrumb-row reveal">
        <div>
            <a href="{{ route('home') }}">Trang chủ</a> /
            <a href="{{ route('products.index') }}">Sản phẩm</a> /
            @if($product->category)
                <a href="{{ route('products.index', ['category' => $product->category_id]) }}">
                    {{ $product->category->name }}
                </a> /
            @endif
            <span class="crumb-current">{{ $product->name }}</span>
        </div>

        <div class="breadcrumb-actions">
            <form action="{{ route('compare.add', $product) }}" method="POST" style="margin:0">
                @csrf
                <button type="submit" class="btn-icon-text">
                    <i class="fas fa-code-compare"></i> So sánh
                </button>
            </form>

            @auth
            <button id="btnWishlistDetail"
                class="btn-wishlist-detail {{ auth()->user()->wishlists->contains('product_id', $product->id) ? 'active' : '' }}"
                data-url="{{ route('wishlist.toggle', $product->id) }}"
                title="Yêu thích">
                <i class="{{ auth()->user()->wishlists->contains('product_id', $product->id) ? 'fas' : 'far' }} fa-heart"></i>
            </button>
            @else
            <a href="{{ route('login') }}" style="padding:6px 14px;background:rgba(255,255,255,.7);backdrop-filter:blur(6px);border:1px solid rgba(239,68,68,.4);border-radius:8px;color:#ef4444;font-size:13px;font-weight:600;cursor:pointer;transition:all .2s;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                <i class="far fa-heart"></i> Yêu thích
            </a>
            @endauth
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="flash-success reveal"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="flash-error reveal"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="flash-error reveal"><i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}</div>
    @endif

    {{-- ===== PRODUCT MAIN ===== --}}
    <div class="product-main">

        {{-- Gallery --}}
        @php
            $galleryImages = collect();
            if ($product->thumbnail) $galleryImages->push($product->thumbnail);
            foreach ($product->images ?? [] as $img) {
                if ($img !== $product->thumbnail) $galleryImages->push($img);
            }
        @endphp
        <div class="gallery reveal-left">
            <div class="gallery-main">
                @if($product->discount_percent > 0)
                    <div class="sm-product-ribbon"><span>GIẢM {{ $product->discount_percent }}%</span></div>
                @endif
                @if($galleryImages->isNotEmpty())
                    <img src="{{ $galleryImages->first() }}" alt="{{ $product->name }}" id="mainImg">
                @else
                    <i class="fas fa-image fa-3x" style="color:#c9c9c9"></i>
                @endif
            </div>
            <div class="gallery-thumbs stagger-children">
                @foreach($galleryImages as $img)
                <div class="thumb {{ $loop->first ? 'active' : '' }}"
                     onclick="switchImage(this, '{{ $img }}')">
                    <img src="{{ $img }}" alt="">
                </div>
                @endforeach
            </div>
        </div>

        {{-- Product Info --}}
        <div class="product-info reveal">
            @if($product->brand)
                <span class="product-brand">{{ $product->brand->name }}</span>
            @endif
            <h1 class="product-name">{{ $product->name }}</h1>

            <div class="product-rating">
                <span class="stars">
                    @for($i=1;$i<=5;$i++){{ $i <= round($product->avg_rating) ? '★' : '☆' }}@endfor
                </span>
                <span class="rating-num">{{ $product->avg_rating }}</span>
                <span class="divider">|</span>
                <span>{{ $product->reviews_count }} đánh giá</span>
                @if($product->variants->isEmpty())
                <span class="divider">|</span>
                <span>{{ number_format($product->stock) }} còn lại</span>
                @endif
            </div>

            @php
                $activeEvent = $product->getActiveEvent();
            @endphp
            @php
                $activeEvent = $product->getActiveEvent();
            @endphp
            @if($activeEvent && $activeEvent->end_date && $activeEvent->end_date > now())
                @php
                    $theme = $activeEvent->theme ?? 'default';
                    $leftBg = '#ef4444';
                    $rightBg = 'linear-gradient(90deg, #f59e0b, #ea580c)';
                    if ($theme == 'summer') { $leftBg = '#f59e0b'; $rightBg = 'linear-gradient(90deg, #10b981, #059669)'; }
                    if ($theme == 'womens_day') { $leftBg = '#ec4899'; $rightBg = 'linear-gradient(90deg, #8b5cf6, #6366f1)'; }
                @endphp
                
                <div class="shopee-flash-sale-banner" style="display: flex; border-radius: 8px; margin-bottom: 24px; position: relative;">
                    <!-- LEFT SIDE: PRICE -->
                    <div style="flex: 1; background: {{ $leftBg }}; padding: 12px 16px 12px 20px; color: #fff; border-top-left-radius: 8px; border-bottom-left-radius: 8px;">
                        @if($product->has_price_range)
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                                <span id="pricePctDisplay" style="display:none; background: #fff; color: {{ $leftBg }}; font-size: 13px; font-weight: 800; padding: 2px 6px; border-radius: 4px;"></span>
                                <span id="priceDisplay" style="font-size: 28px; font-weight: 700; letter-spacing: -0.5px;">Từ {{ number_format($product->min_price) }}đ</span>
                            </div>
                            <div id="priceOldDisplay" style="display:none; font-size: 15px; text-decoration: line-through; opacity: 0.8;"></div>
                        @else
                            @php
                                $discountedPrice = $product->price;
                                if ($product->discount_percent > 0) {
                                    $discountedPrice = $product->price * (1 - $product->discount_percent / 100);
                                }
                            @endphp
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                                @if($product->discount_percent > 0)
                                    <span id="pricePctDisplay" style="background: #fff; color: {{ $leftBg }}; font-size: 13px; font-weight: 800; padding: 3px 8px; border-radius: 4px;">-{{ $product->discount_percent }}%</span>
                                @else
                                    <span id="pricePctDisplay" style="display:none; background: #fff; color: {{ $leftBg }}; font-size: 13px; font-weight: 800; padding: 3px 8px; border-radius: 4px;"></span>
                                @endif
                                <span id="priceDisplay" style="font-size: 28px; font-weight: 700; letter-spacing: -0.5px;">{{ number_format($discountedPrice) }}đ</span>
                            </div>
                            
                            @if($product->discount_percent > 0)
                                <div id="priceOldDisplay" style="font-size: 15px; text-decoration: line-through; opacity: 0.8;">{{ number_format($product->price) }}đ</div>
                            @else
                                <div id="priceOldDisplay" style="display:none; font-size: 15px; text-decoration: line-through; opacity: 0.8;"></div>
                            @endif
                        @endif
                    </div>
                    
                    <!-- RIGHT SIDE: COUNTDOWN -->
                    <div style="background: {{ $rightBg }}; padding: 12px 20px 12px 30px; display: flex; flex-direction: column; justify-content: center; align-items: flex-start; border-top-right-radius: 8px; border-bottom-right-radius: 8px; clip-path: polygon(15px 0, 100% 0, 100% 100%, 0 100%); margin-left: -15px; z-index: 2;">
                        <div style="font-size: 16px; font-weight: 800; color: #fff; text-transform: uppercase; margin-bottom: 4px; display: flex; align-items: center; gap: 6px; text-shadow: 0 1px 2px rgba(0,0,0,0.2);">
                            <i class="fas fa-bolt" style="font-size: 20px;"></i> {{ $activeEvent->title }}
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px; color: #fff; font-size: 14px;">
                            <span style="opacity: 0.9;">Kết thúc sau</span>
                            <span class="product-countdown" data-end="{{ $activeEvent->end_date->format('Y-m-d\TH:i:s') }}" style="font-weight: 700; font-family: monospace; font-size: 15px; letter-spacing: 1px;">00:00:00</span>
                        </div>
                    </div>
                </div>
            @else
                <div class="price-block" id="priceBlock">
                    @if($product->has_price_range)
                        <span class="price-current" id="priceDisplay">Từ {{ number_format($product->min_price) }}đ</span>
                        <span class="price-old" id="priceOldDisplay" style="display:none"></span>
                        <span class="price-pct" id="pricePctDisplay" style="display:none"></span>
                    @else
                        @php
                            $discountedPrice = $product->price;
                            if ($product->discount_percent > 0) {
                                $discountedPrice = $product->price * (1 - $product->discount_percent / 100);
                            }
                        @endphp
                        <span class="price-current" id="priceDisplay">{{ number_format($discountedPrice) }}đ</span>
                        @if($product->discount_percent > 0)
                            <span class="price-old" id="priceOldDisplay">{{ number_format($product->price) }}đ</span>
                            <span class="price-pct" id="pricePctDisplay">-{{ $product->discount_percent }}%</span>
                        @else
                            <span class="price-old" id="priceOldDisplay" style="display:none"></span>
                            <span class="price-pct" id="pricePctDisplay" style="display:none"></span>
                        @endif
                    @endif
                </div>
            @endif

            <div id="stockDisplay" style="margin:12px 0 28px">
                @if($product->stock > 0)
                    <div class="in-stock"><i class="fas fa-check-circle"></i> Còn hàng ({{ $product->stock }})</div>
                @else
                    <div class="out-of-stock"><i class="fas fa-times-circle"></i> Hết hàng</div>
                @endif
            </div>

            @if($product->variants->isNotEmpty())
            <div id="variantSelector"></div>
            <div id="variantAlert" style="display:none;margin-top:14px">
                <i class="fas fa-exclamation-triangle"></i> <span id="variantAlertText">Phiên bản này hiện không có sẵn.</span>
            </div>
            @endif

            {{-- Nút biến thể --}}
            <div class="action-btns" id="actionBtns" style="display:none">
                <form action="{{ route('cart.add') }}" method="POST" id="formAddCart">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="variant_id" id="inputVariantId" value="">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="cta cta--outline cta--lg btn-add-cart" id="btnAddCart">
                        <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                    </button>
                </form>
                <form action="{{ route('cart.buy-now') }}" method="POST" id="formBuyNow">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="variant_id" id="inputVariantIdBuy" value="">
                    <button type="submit" class="cta cta--black cta--lg btn-buy-now" id="btnBuyNow">
                        Mua ngay
                    </button>
                </form>
            </div>

            {{-- Sản phẩm đơn giản --}}
            @if($product->variants->isEmpty())
            <div class="action-btns">
                @if($product->stock > 0)
                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="cta cta--outline cta--lg btn-add-cart">
                        <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                    </button>
                </form>
                <form action="{{ route('cart.buy-now') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <button type="submit" class="cta cta--black cta--lg btn-buy-now">
                        Mua ngay
                    </button>
                </form>
                @else
                <button class="cta cta--outline cta--lg btn-add-cart" style="opacity:.4;cursor:not-allowed" disabled>
                    <i class="fas fa-ban"></i> Hết hàng
                </button>
                @endif
            </div>
            @endif
        </div>

        {{-- Sidebar Benefits --}}
        <div class="product-sidebar reveal">
            <div class="benefit-box">
                @foreach([
                    ['fa-truck',       'Giao hàng tận nơi',   'Miễn phí toàn quốc đơn từ 500k'],
                    ['fa-shield-alt',  'Thanh toán an toàn',  'Nhận hàng kiểm tra rồi mới thanh toán'],
                    ['fa-credit-card', 'Trả góp 0%',          'Duyệt nhanh qua thẻ tín dụng'],
                    ['fa-undo',        'Đổi trả 30 ngày',     'Hoàn tiền nếu lỗi nhà sản xuất'],
                ] as [$icon, $title, $desc])
                <div class="benefit-item">
                    <div class="bi-icon"><i class="fas {{ $icon }}"></i></div>
                    <div>
                        <div class="bi-title">{{ $title }}</div>
                        <div class="bi-desc">{{ $desc }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="commit-box">
                <h4>Cam kết từ ElectronicShop</h4>
                @foreach([
                    ['fa-check-circle', 'Hàng chính hãng 100%'],
                    ['fa-award',        'Bảo hành 12 tháng'],
                    ['fa-headset',      'Hỗ trợ 24/7: 1900 1234'],
                    ['fa-map-marker-alt','50+ cửa hàng toàn quốc'],
                ] as [$icon, $text])
                <div class="commit-item"><i class="fas {{ $icon }}"></i> {{ $text }}</div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ===== TABS ===== --}}
    <div class="product-tabs reveal">
        <button class="tab-btn active" onclick="switchTab(this,'tab-desc')">Tổng quan</button>
        <button class="tab-btn" onclick="switchTab(this,'tab-specs')">Thông số kỹ thuật</button>
        <button class="tab-btn" onclick="switchTab(this,'tab-reviews')">
            Đánh giá ({{ $product->reviews_count }})
        </button>
    </div>

    {{-- Tab: Mô tả --}}
    <div id="tab-desc" class="tab-panel active">
        <div class="tab-panel-inner">
            @if($product->description)
                {{-- Mô tả được soạn bằng trình soạn thảo (CKEditor) ở trang admin nên đã
                     chứa sẵn thẻ HTML (đoạn văn, danh sách, in đậm...). Nội dung này đã
                     được làm sạch (purify) phía server khi lưu ở admin, nên hiển thị
                     trực tiếp ở đây mà không escape để giữ định dạng. --}}
                <div class="product-description-content" style="font-size:15px;line-height:1.85;color:#333;">
                    {!! $product->description !!}
                </div>
            @else
                <p class="empty-note">Chưa có mô tả cho sản phẩm này.</p>
            @endif
        </div>
    </div>

    {{-- Tab: Thông số --}}
    <div id="tab-specs" class="tab-panel">
        <div class="tab-panel-inner">
            <table class="specs-table" id="specsTable" style="{{ $product->attributes->isEmpty() ? 'display:none' : '' }}">
                <tbody id="specsTableBody">
                    @foreach($product->attributes as $attr)
                    <tr>
                        <td>{{ $attr->attribute->name }}</td>
                        <td>{{ $attr->value }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <p class="empty-note" id="specsEmptyMsg" style="{{ $product->attributes->isNotEmpty() ? 'display:none' : '' }}">
                Chưa có thông số kỹ thuật.
            </p>
        </div>
    </div>

    {{-- Tab: Đánh giá --}}
    <div id="tab-reviews" class="tab-panel">
        <div class="tab-panel-inner">

            <div class="rating-summary">
                <div class="rating-avg">
                    <div class="big">{{ number_format($product->avg_rating, 1) }}</div>
                    <div class="stars-lg">
                        @for($i=1;$i<=5;$i++){{ $i <= round($product->avg_rating) ? '★' : '☆' }}@endfor
                    </div>
                    <small>{{ $product->reviews_count }} đánh giá</small>
                </div>
                <div class="rating-bars">
                    @for($star=5; $star>=1; $star--)
                    @php $cnt = $ratingDistribution[$star] ?? 0; $pct = $product->reviews_count ? ($cnt/$product->reviews_count*100) : 0; @endphp
                    <div class="rating-bar-row">
                        <span style="width:14px;text-align:right">{{ $star }}</span>
                        <i class="fas fa-star" style="color:var(--sam-star);font-size:11px"></i>
                        <div class="bar"><div class="bar-fill" style="width:{{ $pct }}%" data-width="{{ $pct }}"></div></div>
                        <span class="cnt">{{ $cnt }}</span>
                    </div>
                    @endfor
                </div>
            </div>

            @forelse($reviews as $review)
            <div class="review-item">
                <div class="review-header">
                    <span class="review-user">{{ $review->user->name ?? 'Người dùng ẩn danh' }}</span>
                    <span class="review-date">{{ $review->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="review-stars">
                    @for($i=1;$i<=5;$i++){{ $i <= $review->rating ? '★' : '☆' }}@endfor
                </div>
                @if($review->content)
                <div class="review-content">{{ $review->content }}</div>
                @endif
            </div>
            @empty
            <p class="empty-note">Chưa có đánh giá nào. Hãy là người đầu tiên đánh giá!</p>
            @endforelse

            @if($reviews->hasPages())
            <div style="margin-top:20px">{{ $reviews->links() }}</div>
            @endif

            @auth
                @if($canReview)
                <div class="review-form">
                    <h4>Viết đánh giá của bạn</h4>
                    <form action="{{ route('products.review', $product->id) }}" method="POST">
                        @csrf
                        <div style="margin-bottom:18px">
                            <label class="form-label">Chọn sao</label>
                            <div class="star-rating" id="starRating">
                                @for($i=5;$i>=1;$i--)
                                <input type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}" {{ old('rating')==$i ? 'checked' : '' }}>
                                <label for="star{{ $i }}">★</label>
                                @endfor
                            </div>
                            @error('rating')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label class="form-label">Nội dung (tuỳ chọn)</label>
                            <textarea name="content" rows="4" class="form-textarea"
                                      placeholder="Chia sẻ trải nghiệm của bạn...">{{ old('content') }}</textarea>
                        </div>
                        <button type="submit" class="cta cta--black" style="margin-top:16px">Gửi đánh giá</button>
                    </form>
                </div>
                @else
                <div class="notice-box">
                    Bạn cần mua và nhận hàng thành công sản phẩm này trước khi đánh giá.
                </div>
                @endif
            @else
            <div class="notice-box">
                <a href="{{ route('login') }}">Đăng nhập</a> để viết đánh giá.
            </div>
            @endauth
        </div>
    </div>

    {{-- ===== RELATED PRODUCTS ===== --}}
    @if($relatedProducts->isNotEmpty())
    <div class="related-section reveal">
        <div class="related-header">
            <h2>Sản phẩm liên quan</h2>
            <a href="{{ route('products.index', ['category' => $product->category_id]) }}">Xem tất cả</a>
        </div>
        <div class="related-grid stagger-children">
            @foreach($relatedProducts as $p)
            <a href="{{ route('products.show', $p->slug) }}" class="product-card">
                <div class="product-card-img">
                    @if($p->first_image)
                        <img src="{{ $p->first_image }}" alt="{{ $p->name }}" loading="lazy">
                    @else
                        <i class="fas fa-image fa-2x" style="color:#c9c9c9"></i>
                    @endif
                </div>
                <div class="product-card-body">
                    <div class="product-card-name">{{ $p->name }}</div>
                    <div class="stars">
                        @for($i=1;$i<=5;$i++){{ $i <= round($p->avg_rating) ? '★' : '☆' }}@endfor
                        <span class="muted">({{ $p->reviews_count }})</span>
                    </div>
                    <div class="product-card-price">
                        {{ $p->has_price_range ? 'Từ ' : '' }}{{ number_format($p->min_price) }}đ
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

</div><!-- /.product-detail-wrap -->
@endsection

@push('scripts')
<script>
/* ============================================================
   TAB & GALLERY helpers
   ============================================================ */
function switchTab(btn, tabId) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-panel').forEach(c => c.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById(tabId).classList.add('active');
}

function switchImage(thumb, src) {
    document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
    thumb.classList.add('active');
    const img = document.getElementById('mainImg');
    if (img) {
        img.style.opacity = '0';
        img.style.transform = 'scale(.97)';
        setTimeout(() => {
            img.src = src;
            img.style.transition = 'opacity .3s, transform .3s';
            img.style.opacity = '1';
            img.style.transform = 'scale(1)';
        }, 120);
    }
}

/* ============================================================
   VARIANT SELECTION (logic giữ nguyên, chỉ đổi giao diện)
   ============================================================ */
@php
$variantsForJs = $product->variants->map(function($v) {
    return [
        'id'               => $v->id,
        'price'            => (float) $v->price,
        'discount_percent' => (int)   $v->discount_percent,
        'stock'            => (int)   $v->stock,
        'is_active'        => (bool)  $v->is_active,
        'thumbnail'        => $v->display_thumbnail,
        'images'           => $v->display_images,
        'attrs'            => $v->variantAttributes
            ->filter(fn($va) => $va->attribute->is_variant)
            ->mapWithKeys(function($va) {
                return [$va->attribute->name => $va->value];
            })->toArray(),
    ];
})->values()->toArray();
$baseGalleryForJs = $galleryImages->values()->toArray();
@endphp

@if($product->variants->isNotEmpty())
(function() {
    const VARIANTS    = {!! json_encode($variantsForJs, JSON_UNESCAPED_UNICODE) !!};
    const BASE_ATTRS  = @json($product->attributes->mapWithKeys(fn($pa) => [$pa->attribute->name => $pa->value]));
    const MAIN_ATTR_NAMES = @json($product->attributes->pluck('attribute')->filter(fn($a) => $a && $a->is_variant)->pluck('name')->values());
    const BASE_PRICE    = {{ (float)$product->price }};
    const BASE_DISCOUNT = {{ (int)$product->discount_percent }};
    const BASE_STOCK    = {{ (int)$product->stock }};
    const BASE_THUMBNAIL = {!! json_encode($product->thumbnail) !!};
    const BASE_GALLERY   = {!! json_encode($baseGalleryForJs, JSON_UNESCAPED_UNICODE) !!};

    function fmt(n) { return Math.round(n).toLocaleString('vi-VN') + 'đ'; }
    function esc(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

    const BASE_MAIN_ATTRS = {};
    MAIN_ATTR_NAMES.forEach(name => { if (BASE_ATTRS[name] !== undefined) BASE_MAIN_ATTRS[name] = BASE_ATTRS[name]; });

    const ALL_OPTIONS = [
        { id: null, price: BASE_PRICE, discount_percent: BASE_DISCOUNT, stock: BASE_STOCK, is_active: true, attrs: BASE_MAIN_ATTRS, thumbnail: BASE_THUMBNAIL, images: BASE_GALLERY },
        ...VARIANTS,
    ];

    function getDiffKeys() {
        const keys = [];
        ALL_OPTIONS.forEach(o => Object.keys(o.attrs).forEach(k => { if (!keys.includes(k)) keys.push(k); }));
        return keys.filter(key => new Set(ALL_OPTIONS.map(o => o.attrs[key] ?? '')).size > 1);
    }
    const DIFF_KEYS = getDiffKeys();

    function valuesForKey(key) {
        const vals = [];
        ALL_OPTIONS.forEach(o => { const v = o.attrs[key]; if (v && !vals.includes(v)) vals.push(v); });
        return vals;
    }

    function optionFinalPrice(o) { return o.price * (1 - o.discount_percent / 100); }
    function isOptionAvailable(o) { return o.is_active !== false && o.stock > 0; }

    /**
     * Option có đủ giá trị cho MỌI thuộc tính phân biệt hay không. "Sản phẩm gốc" (id: null)
     * thường KHÔNG khai đủ các thuộc tính này (chỉ biến thể mới có), nên nếu chọn nó làm mặc
     * định thì mỗi thuộc tính sẽ bị fallback độc lập -> ra 1 tổ hợp không khớp biến thể thật nào
     * -> các nút bị khoá hết, không bấm đổi được. Vì vậy khi có DIFF_KEYS, chỉ ưu tiên các option
     * "đầy đủ" (biến thể thật) làm lựa chọn mặc định.
     */
    function isCompleteOption(o) {
        return DIFF_KEYS.every(key => o.attrs[key] !== undefined && o.attrs[key] !== '');
    }

    /** Chọn mặc định option còn hàng, đầy đủ thuộc tính, có giá thấp nhất */
    function findCheapestAvailableOption() {
        let pool = ALL_OPTIONS.filter(o => isOptionAvailable(o) && (DIFF_KEYS.length === 0 || isCompleteOption(o)));
        if (pool.length === 0) pool = ALL_OPTIONS.filter(isOptionAvailable);
        if (pool.length === 0) pool = ALL_OPTIONS;
        return pool.reduce((min, o) => (optionFinalPrice(o) < optionFinalPrice(min) ? o : min), pool[0]);
    }

    const defaultOption = findCheapestAvailableOption();
    const selectedAttrs = {};
    DIFF_KEYS.forEach(key => { selectedAttrs[key] = defaultOption.attrs[key] ?? valuesForKey(key)[0]; });

    function findMatchingOption() {
        return ALL_OPTIONS.find(o => DIFF_KEYS.every(key => (o.attrs[key] ?? '') === selectedAttrs[key])) || null;
    }

    /**
     * Giá trị `value` của thuộc tính `key` có khả dụng hay không, XÉT THEO các thuộc tính
     * khác đang được chọn — tức là chỉ cho phép chọn nếu tồn tại 1 tổ hợp còn hàng khớp
     * với lựa chọn hiện tại + giá trị này. Nhờ vậy không thể chọn ra 1 tổ hợp không tồn tại.
     */
    function isValueAvailable(key, value) {
        return ALL_OPTIONS.some(o => {
            if ((o.attrs[key] ?? '') !== value) return false;
            if (!isOptionAvailable(o)) return false;
            return DIFF_KEYS.every(k => k === key || !selectedAttrs[k] || (o.attrs[k] ?? '') === selectedAttrs[k]);
        });
    }

    /** Đã chọn đủ giá trị cho tất cả thuộc tính phân biệt chưa (option có thể bị bỏ chọn) */
    function isSelectionComplete() {
        return DIFF_KEYS.every(key => !!selectedAttrs[key]);
    }

    /**
     * Tìm 1 option "tạm khớp" với những thuộc tính ĐÃ chọn (không cần khớp hết) để lấy ảnh
     * preview — nhờ vậy vừa đổi màu là ảnh đổi ngay, không cần đợi chọn xong hết các thuộc tính
     * còn lại (dung lượng, size...).
     */
    function findPreviewOption() {
        const selectedKeys = DIFF_KEYS.filter(k => selectedAttrs[k]);
        if (selectedKeys.length === 0) return null;

        const candidates = ALL_OPTIONS.filter(o => selectedKeys.every(k => (o.attrs[k] ?? '') === selectedAttrs[k]));
        if (candidates.length === 0) return null;

        return candidates.find(o => isOptionAvailable(o) && (o.thumbnail || (o.images && o.images.length)))
            || candidates.find(o => o.thumbnail || (o.images && o.images.length))
            || candidates[0];
    }

    function applyOption(option) {
        const alertEl     = document.getElementById('variantAlert');
        const alertTextEl = document.getElementById('variantAlertText');
        const actionBtns  = document.getElementById('actionBtns');
        const addBtn      = document.getElementById('btnAddCart');
        const buyBtn      = document.getElementById('btnBuyNow');

        if (!option) {
            if (alertTextEl) {
                alertTextEl.textContent = isSelectionComplete()
                    ? 'Phiên bản này hiện không có sẵn.'
                    : 'Vui lòng chọn đầy đủ các tuỳ chọn để xem giá và mua hàng.';
            }
            if (alertEl) alertEl.style.display = '';
            if (actionBtns) actionBtns.style.display = 'none';
            renderSpecs(selectedAttrs);
            const preview = findPreviewOption();
            renderGallery(preview || { thumbnail: BASE_THUMBNAIL, images: BASE_GALLERY });
            return;
        }

        if (alertEl) alertEl.style.display = 'none';
        if (actionBtns) actionBtns.style.display = '';

        ['inputVariantId', 'inputVariantIdBuy'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = option.id ?? '';
        });

        const soldOut = option.stock <= 0 || option.is_active === false;
        if (addBtn) { addBtn.disabled = soldOut; addBtn.style.opacity = soldOut ? '.4' : '1'; }
        if (buyBtn) { buyBtn.disabled = soldOut; buyBtn.style.opacity = soldOut ? '.4' : '1'; }

        renderPrice(option.price, option.discount_percent);
        renderStock(option.stock);
        renderSpecs(option.attrs);
        renderGallery(option);
    }

    function renderGallery(option) {
        const mainImg = document.getElementById('mainImg');
        const thumbsWrap = document.querySelector('.gallery-thumbs');
        if (!mainImg || !thumbsWrap) return;

        const gallery = [];
        if (option.thumbnail) gallery.push(option.thumbnail);
        (option.images || []).forEach(img => { if (img && !gallery.includes(img)) gallery.push(img); });
        if (gallery.length === 0) return; // giữ nguyên ảnh hiện tại nếu biến thể không có ảnh nào

        mainImg.src = gallery[0];
        thumbsWrap.innerHTML = gallery.map((img, i) => `
            <div class="thumb ${i === 0 ? 'active' : ''}" onclick="switchImage(this, '${img}')">
                <img src="${img}" alt="">
            </div>`).join('');
    }

    function renderSpecs(currentAttrs) {
        const tbody   = document.getElementById('specsTableBody');
        const table   = document.getElementById('specsTable');
        const emptyEl = document.getElementById('specsEmptyMsg');
        if (!tbody) return;
        const merged = Object.assign({}, BASE_ATTRS, currentAttrs);
        const keys   = Object.keys(merged);
        if (keys.length === 0) {
            if (table) table.style.display = 'none';
            if (emptyEl) emptyEl.style.display = '';
            return;
        }
        tbody.innerHTML = keys.map(k => `<tr><td>${esc(k)}</td><td>${esc(merged[k])}</td></tr>`).join('');
        if (table) table.style.display = '';
        if (emptyEl) emptyEl.style.display = 'none';
    }

    const optionButtons = []; // { key, value, btn } — dùng để refresh trạng thái disabled sau mỗi lần chọn

    function refreshOptionAvailability() {
        optionButtons.forEach(({ key, value, btn }) => {
            const available = isValueAvailable(key, value);
            btn.disabled = !available;
            btn.classList.toggle('opt-btn-disabled', !available);
            btn.title = available ? '' : 'Hết hàng / không khả dụng';
        });
    }

    const selector = document.getElementById('variantSelector');
    if (selector && DIFF_KEYS.length > 0) {
        DIFF_KEYS.forEach(key => {
            const group   = document.createElement('div');
            group.className = 'option-group';
            const label   = document.createElement('div');
            label.className = 'option-label';
            label.innerHTML = `${esc(key)}: <strong id="lbl-${esc(key)}">${esc(selectedAttrs[key] ?? '')}</strong>`;
            group.appendChild(label);
            const btnWrap = document.createElement('div');
            btnWrap.className = 'option-btns';
            valuesForKey(key).forEach(value => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'opt-btn' + (selectedAttrs[key] === value ? ' active' : '');
                btn.textContent = value;
                btn.addEventListener('click', function () {
                    if (this.classList.contains('opt-btn-disabled') || this.disabled) return; // hết hàng / không khả dụng -> không cho bấm

                    const wasActive = this.classList.contains('active');
                    btnWrap.querySelectorAll('.opt-btn').forEach(b => b.classList.remove('active'));

                    if (wasActive) {
                        // Bấm lại option đang chọn -> bỏ chọn (kiểu checkbox)
                        delete selectedAttrs[key];
                        document.getElementById(`lbl-${key}`).textContent = 'Chưa chọn';
                    } else {
                        selectedAttrs[key] = value;
                        this.classList.add('active');
                        document.getElementById(`lbl-${key}`).textContent = value;
                    }

                    refreshOptionAvailability(); // các giá trị của thuộc tính khác có thể đổi trạng thái khả dụng
                    applyOption(findMatchingOption());
                });
                btnWrap.appendChild(btn);
                optionButtons.push({ key, value, btn });
            });
            group.appendChild(btnWrap);
            selector.appendChild(group);
        });
        refreshOptionAvailability();
    }

    function renderPrice(price, discount) {
        const sale = price * (1 - discount / 100);
        document.getElementById('priceDisplay').textContent = fmt(sale);
        const oldEl = document.getElementById('priceOldDisplay');
        const pctEl = document.getElementById('pricePctDisplay');
        if (discount > 0) {
            oldEl.textContent = fmt(price); pctEl.textContent = `-${discount}%`;
            oldEl.style.display = ''; pctEl.style.display = '';
        } else {
            oldEl.textContent = ''; pctEl.textContent = '';
            oldEl.style.display = 'none'; pctEl.style.display = 'none';
        }
    }

    function renderStock(stock) {
        const el = document.getElementById('stockDisplay');
        if (!el) return;
        el.innerHTML = stock > 0
            ? `<div class="in-stock"><i class="fas fa-check-circle"></i> Còn hàng (${stock})</div>`
            : `<div class="out-of-stock"><i class="fas fa-times-circle"></i> Hết hàng</div>`;
    }

    (function initVariant() { applyOption(findMatchingOption() || defaultOption); })();
})();
@endif

/* ============================================================
   SCROLL REVEAL + WISHLIST
   ============================================================ */
(function () {

    /* ---- Scroll Reveal ---- */
    const io = new IntersectionObserver(entries => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('revealed'); io.unobserve(e.target); } });
    }, { threshold: 0.05, rootMargin: '0px 0px -40px 0px' });
    document.querySelectorAll('.reveal, .reveal-left, .stagger-children').forEach(el => io.observe(el));

    /* ---- Rating bar animate on reveal ---- */
    const barObserver = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.querySelectorAll('.bar-fill').forEach(bar => {
                    const w = bar.dataset.width || '0';
                    bar.style.width = '0';
                    setTimeout(() => { bar.style.width = w + '%'; }, 200);
                });
                barObserver.unobserve(e.target);
            }
        });
    }, { threshold: 0.3 });
    document.querySelectorAll('.rating-summary').forEach(el => barObserver.observe(el));

    /* Wishlist toggle (chi tiết sản phẩm) */
    (function() {
        const btn = document.getElementById('btnWishlistDetail');
        if (!btn) return;
        let _t;
        function showToast(msg, isErr) {
            let t = document.getElementById('_wlToast');
            if (!t) {
                t = document.createElement('div');
                t.id = '_wlToast';
                t.style.cssText = 'position:fixed;bottom:24px;right:24px;background:rgba(15,23,42,.92);color:#fff;padding:12px 20px;border-radius:12px;font-size:14px;font-weight:500;display:flex;align-items:center;gap:10px;z-index:9999;opacity:0;transform:translateY(10px);transition:opacity .3s,transform .3s;pointer-events:none;';
                t.innerHTML = '<i style="font-size:16px"></i><span></span>';
                document.body.appendChild(t);
            }
            const icon = t.querySelector('i');
            icon.style.color = isErr ? '#f87171' : '#34d399';
            icon.className = isErr ? 'fas fa-times-circle' : 'fas fa-check-circle';
            t.querySelector('span').textContent = msg;
            t.style.opacity = '1'; t.style.transform = 'translateY(0)';
            clearTimeout(_t);
            _t = setTimeout(() => { t.style.opacity='0'; t.style.transform='translateY(10px)'; }, 2800);
        }
        btn.addEventListener('click', function() {
            const icon = this.querySelector('i');
            const wasActive = this.classList.contains('active');
            this.classList.toggle('active', !wasActive);
            icon.className = wasActive ? 'far fa-heart' : 'fas fa-heart';
            fetch(this.dataset.url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            })
            .then(r => r.json())
            .then(data => {
                this.classList.toggle('active', data.wishlisted);
                icon.className = data.wishlisted ? 'fas fa-heart' : 'far fa-heart';
                showToast(data.wishlisted ? '♥ Đã thêm vào yêu thích' : 'Nhấp khỏi yêu thích');
            })
            .catch(() => {
                this.classList.toggle('active', wasActive);
                icon.className = wasActive ? 'fas fa-heart' : 'far fa-heart';
                showToast('Có lỗi, vui lòng thử lại', true);
            });
        });
    })();

    // Product page event countdown
    const prodCountdown = document.querySelector('.product-countdown');
    if (prodCountdown) {
        setInterval(() => {
            const now = new Date().getTime();
            const end = new Date(prodCountdown.dataset.end).getTime();
            const distance = end - now;
            if (distance < 0) {
                prodCountdown.innerHTML = "Đã kết thúc";
                return;
            }
            const d = Math.floor(distance / (1000 * 60 * 60 * 24));
            const h = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const s = Math.floor((distance % (1000 * 60)) / 1000);
            
            const hoursStr = (d * 24 + h).toString().padStart(2, '0');
            prodCountdown.innerHTML = `${hoursStr}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
        }, 1000);
    }
})();
</script>
@endpush
