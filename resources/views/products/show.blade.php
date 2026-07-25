@extends('layouts.app')
@section('title', $product->name . ' - ElectronicShop')

@push('styles')
<style>
/* ============================================================
   PAGE BACKGROUND — sky gradient (khớp trang chủ)
   ============================================================ */
body {
    background: linear-gradient(180deg,
        #bae6fd 0%,
        #e0f2fe 18%,
        #f0f9ff 38%,
        #e0f2fe 62%,
        #bae6fd 100%) fixed;
    background-attachment: fixed;
}

#sky-canvas {
    position: fixed; inset: 0;
    width: 100%; height: 100%;
    pointer-events: none; z-index: 0; opacity: .42;
}

.bubble {
    position: fixed; border-radius: 50%;
    background: radial-gradient(circle at 35% 35%, rgba(255,255,255,.8), rgba(186,230,253,.3));
    border: 1px solid rgba(125,211,252,.4);
    pointer-events: none; z-index: 0;
    animation: bubbleRise linear infinite;
}
@keyframes bubbleRise {
    0%   { transform: translateY(0) scale(1);    opacity: .7; }
    80%  { opacity: .4; }
    100% { transform: translateY(-110vh) scale(1.1); opacity: 0; }
}

.product-detail-wrap {
    max-width: 1200px; margin: 0 auto; padding: 16px;
    position: relative; z-index: 1;
}

/* ============================================================
   SCROLL REVEAL
   ============================================================ */
.reveal {
    opacity: 0; transform: translateY(28px);
    transition: opacity .6s cubic-bezier(.16,1,.3,1), transform .6s cubic-bezier(.16,1,.3,1);
}
.reveal.revealed { opacity: 1; transform: translateY(0); }

.reveal-left {
    opacity: 0; transform: translateX(-28px);
    transition: opacity .6s cubic-bezier(.16,1,.3,1), transform .6s cubic-bezier(.16,1,.3,1);
}
.reveal-left.revealed { opacity: 1; transform: translateX(0); }

/* stagger children */
.stagger-children > * {
    opacity: 0; transform: translateY(18px);
    transition: opacity .45s cubic-bezier(.16,1,.3,1), transform .45s cubic-bezier(.16,1,.3,1);
}
.stagger-children.revealed > *:nth-child(1)  { opacity:1; transform:translateY(0); transition-delay:.04s; }
.stagger-children.revealed > *:nth-child(2)  { opacity:1; transform:translateY(0); transition-delay:.09s; }
.stagger-children.revealed > *:nth-child(3)  { opacity:1; transform:translateY(0); transition-delay:.14s; }
.stagger-children.revealed > *:nth-child(4)  { opacity:1; transform:translateY(0); transition-delay:.19s; }
.stagger-children.revealed > *:nth-child(5)  { opacity:1; transform:translateY(0); transition-delay:.24s; }
.stagger-children.revealed > *:nth-child(n+6){ opacity:1; transform:translateY(0); transition-delay:.29s; }

/* ripple */
.ripple-wave {
    position: absolute; border-radius: 50%;
    background: rgba(125,211,252,.3);
    transform: scale(0);
    animation: rippleOut .6s linear;
    pointer-events: none; z-index: 10;
}
@keyframes rippleOut { to { transform: scale(4); opacity: 0; } }

/* ============================================================
   BREADCRUMB
   ============================================================ */
.breadcrumb-row {
    font-size: 13px; color: #0369a1;
    margin-bottom: 16px;
    display: flex; align-items: center;
    justify-content: space-between;
    flex-wrap: wrap; gap: 10px;
}
.breadcrumb-row a { color: #0c4a6e; font-weight: 600; text-decoration: none; }
.breadcrumb-row a:hover { text-decoration: underline; }

/* ============================================================
   FLASH MESSAGES
   ============================================================ */
.flash-success {
    background: rgba(232,245,233,.9); color: #2e7d32;
    border: 1px solid rgba(76,175,80,.25);
    padding: 10px 14px; border-radius: 10px;
    margin-bottom: 16px; font-size: 14px;
    backdrop-filter: blur(6px);
}
.flash-error {
    background: rgba(255,235,238,.9); color: #c62828;
    border: 1px solid rgba(229,57,53,.25);
    padding: 10px 14px; border-radius: 10px;
    margin-bottom: 16px; font-size: 14px;
    backdrop-filter: blur(6px);
}

/* ============================================================
   PRODUCT MAIN GRID
   ============================================================ */
.product-main {
    display: grid;
    grid-template-columns: 340px 1fr 260px;
    gap: 24px; margin-bottom: 32px;
}
@media (max-width: 1024px) { .product-main { grid-template-columns: 320px 1fr; } .product-sidebar { display: none; } }
@media (max-width: 700px)  { .product-main { grid-template-columns: 1fr; } }

/* ============================================================
   GALLERY — glassmorphism card
   ============================================================ */
.gallery {}

.gallery-main {
    border: 1px solid rgba(186,230,253,.7);
    border-radius: 16px;
    height: 300px;
    display: flex; align-items: center; justify-content: center;
    position: relative;
    background: rgba(255,255,255,.82);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    margin-bottom: 12px; overflow: hidden;
    box-shadow: 0 4px 24px rgba(14,165,233,.12);
    transition: box-shadow .3s;
}
.gallery-main:hover {
    box-shadow: 0 8px 32px rgba(14,165,233,.2);
}
.gallery-main img {
    max-width: 100%; max-height: 100%;
    object-fit: contain;
    transition: transform .4s cubic-bezier(.16,1,.3,1);
}
.gallery-main:hover img { transform: scale(1.05); }

.discount-tag {
    position: absolute; top: 10px; left: 10px;
    background: linear-gradient(135deg, #ef4444, #e53935);
    color: #fff; font-size: 12px; font-weight: 700;
    padding: 3px 10px; border-radius: 6px;
    box-shadow: 0 2px 6px rgba(229,57,53,.35);
    animation: badgePop .4s cubic-bezier(.34,1.56,.64,1);
}
@keyframes badgePop { from { transform: scale(0); } to { transform: scale(1); } }

.gallery-thumbs { display: flex; gap: 10px; flex-wrap: wrap; }

.thumb {
    width: 58px; height: 58px;
    border: 2px solid rgba(186,230,253,.6);
    border-radius: 10px; overflow: hidden; cursor: pointer;
    background: rgba(255,255,255,.8);
    backdrop-filter: blur(6px);
    display: flex; align-items: center; justify-content: center;
    transition: border-color .2s, transform .2s, box-shadow .2s;
}
.thumb:hover {
    border-color: #7dd3fc;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(14,165,233,.18);
}
.thumb.active {
    border-color: #0ea5e9;
    box-shadow: 0 0 0 3px rgba(14,165,233,.2);
}
.thumb img { width: 100%; height: 100%; object-fit: contain; padding: 4px; box-sizing: border-box; }

/* ============================================================
   PRODUCT INFO
   ============================================================ */
.product-info {
    background: rgba(255,255,255,.78);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border-radius: 16px;
    border: 1px solid rgba(186,230,253,.6);
    box-shadow: 0 4px 24px rgba(14,165,233,.1);
    padding: 24px;
}

.product-brand {
    font-size: 12px; font-weight: 700;
    color: #0369a1; letter-spacing: 1px;
    text-transform: uppercase; margin-bottom: 6px;
    display: inline-flex; align-items: center; gap: 6px;
}
.product-brand::before {
    content: '';
    display: inline-block;
    width: 20px; height: 2px;
    background: linear-gradient(90deg, #0ea5e9, #38bdf8);
    border-radius: 2px;
}

.product-name {
    font-size: 22px; font-weight: 800;
    line-height: 1.3; margin-bottom: 10px;
    color: #0c4a6e;
}

.product-rating {
    display: flex; align-items: center;
    gap: 10px; font-size: 13px; margin-bottom: 14px;
    color: #0369a1;
}
.product-rating .stars { color: #f59e0b; }

.price-current { font-size: 28px; font-weight: 800; color: #0369a1; }
.price-old     { font-size: 14px; color: #7dd3fc; text-decoration: line-through; margin-left: 10px; }
.price-pct     { font-size: 12px; color: #e53935; font-weight: 600; margin-left: 6px; }

.in-stock      { color: #16a34a; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 4px; margin: 8px 0 16px; }
.out-of-stock  { color: #e53935; font-size: 13px; font-weight: 500; margin: 8px 0 16px; }

#variantSelector { display: flex; flex-direction: column; gap: 14px; margin-bottom: 6px; }

.option-group { margin-bottom: 0; }
.option-label { font-size: 13px; color: #0369a1; margin-bottom: 8px; font-weight: 600; }
.option-label strong { color: #0c4a6e; font-weight: 700; }
.option-btns { display: flex; gap: 10px; flex-wrap: wrap; }

.opt-btn {
    padding: 8px 18px; min-width: 56px;
    border: 1.5px solid rgba(125,211,252,.5);
    border-radius: 10px; font-size: 13.5px; font-weight: 600;
    cursor: pointer;
    background: rgba(255,255,255,.7);
    backdrop-filter: blur(4px);
    color: #0369a1;
    transition: all .2s;
}
.opt-btn:hover:not(.opt-btn-disabled) {
    border-color: #0ea5e9;
    background: rgba(186,230,253,.4);
    transform: translateY(-1px);
}
.opt-btn.active {
    border-color: #0ea5e9;
    color: #0c4a6e; font-weight: 700;
    background: rgba(186,230,253,.55);
    box-shadow: 0 0 0 2px rgba(14,165,233,.25) inset;
}
.opt-btn-disabled { opacity: .4; text-decoration: line-through; cursor: not-allowed; }

.action-btns { display: flex; gap: 12px; margin-top: 20px; }

.btn-add-cart {
    flex: 1; padding: 13px;
    border: 2px solid #0ea5e9; border-radius: 10px;
    background: rgba(255,255,255,.7); backdrop-filter: blur(6px);
    color: #0369a1; font-size: 15px; font-weight: 700;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    transition: all .2s;
}
.btn-add-cart:hover {
    background: rgba(186,230,253,.55);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(14,165,233,.2);
}

.btn-buy-now {
    flex: 1; padding: 13px; border: none;
    border-radius: 10px;
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    color: #fff; font-size: 15px; font-weight: 700;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    transition: all .2s;
    box-shadow: 0 4px 16px rgba(14,165,233,.35);
}
.btn-buy-now:hover {
    opacity: .92;
    transform: translateY(-2px);
    box-shadow: 0 8px 22px rgba(14,165,233,.45);
}

/* ============================================================
   SIDEBAR BENEFITS
   ============================================================ */
.product-sidebar { display: flex; flex-direction: column; gap: 14px; }

.benefit-box, .commit-box {
    background: rgba(255,255,255,.78);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border: 1px solid rgba(186,230,253,.6);
    border-radius: 16px; padding: 18px;
    box-shadow: 0 4px 18px rgba(14,165,233,.1);
}

.benefit-item { display: flex; gap: 10px; align-items: flex-start; margin-bottom: 14px; }
.benefit-item:last-child { margin-bottom: 0; }
.benefit-item .bi-icon {
    width: 34px; height: 34px;
    border-radius: 10px;
    background: linear-gradient(135deg, #bae6fd, #7dd3fc);
    color: #0369a1; font-size: 15px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; margin-top: 1px;
    box-shadow: 0 2px 8px rgba(14,165,233,.18);
}
.benefit-item .bi-title { font-size: 13px; font-weight: 700; color: #0c4a6e; }
.benefit-item .bi-desc  { font-size: 12px; color: #0369a1; opacity: .75; }

.commit-box h4 {
    font-size: 12px; font-weight: 800;
    text-transform: uppercase; letter-spacing: .5px;
    color: #0c4a6e; margin-bottom: 14px;
    padding-bottom: 10px;
    border-bottom: 1px solid rgba(186,230,253,.6);
}
.commit-item {
    display: flex; align-items: center; gap: 8px;
    margin-bottom: 10px; font-size: 13px; color: #0369a1;
}
.commit-item:last-child { margin-bottom: 0; }
.commit-item i { color: #0ea5e9; }

/* ============================================================
   TABS
   ============================================================ */
.product-tabs {
    border-bottom: 2px solid rgba(186,230,253,.6);
    margin-bottom: 24px;
    display: flex; gap: 0;
    overflow-x: auto;
    background: rgba(255,255,255,.6);
    backdrop-filter: blur(10px);
    border-radius: 12px 12px 0 0;
    padding: 0 4px;
}
.tab-btn {
    padding: 13px 24px;
    background: none; border: none;
    border-bottom: 3px solid transparent;
    font-size: 13px; font-weight: 600;
    cursor: pointer;
    text-transform: uppercase; letter-spacing: .5px;
    color: #0369a1; white-space: nowrap;
    transition: all .2s; border-radius: 8px 8px 0 0;
    margin-bottom: -2px;
}
.tab-btn:hover { background: rgba(186,230,253,.35); color: #0c4a6e; }
.tab-btn.active {
    color: #0c4a6e;
    border-bottom-color: #0ea5e9;
    background: rgba(186,230,253,.25);
    font-weight: 700;
}

.tab-panel { display: none; }
.tab-panel.active { display: block; }

/* ============================================================
   TAB PANELS — glassmorphism wrappers
   ============================================================ */
.tab-panel-inner {
    background: rgba(255,255,255,.78);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border-radius: 0 0 16px 16px;
    border: 1px solid rgba(186,230,253,.55);
    border-top: none;
    padding: 24px;
    box-shadow: 0 4px 20px rgba(14,165,233,.08);
}

/* ============================================================
   SPECS TABLE
   ============================================================ */
.specs-table { width: 100%; border-collapse: collapse; font-size: 14px; }
.specs-table tr:nth-child(even) { background: rgba(186,230,253,.2); }
.specs-table tr:hover { background: rgba(186,230,253,.35); }
.specs-table td { padding: 10px 16px; border: 1px solid rgba(186,230,253,.4); }
.specs-table td:first-child { color: #0369a1; width: 200px; font-weight: 600; }
.specs-table td:last-child { color: #0c4a6e; }

/* ============================================================
   RATING SUMMARY
   ============================================================ */
.rating-summary {
    display: flex; align-items: center; gap: 32px;
    padding: 26px;
    background: linear-gradient(135deg, #e0f2fe, #bae6fd);
    border-radius: 14px; margin-bottom: 26px;
    border: 1px solid rgba(125,211,252,.4);
    box-shadow: 0 4px 16px rgba(14,165,233,.12);
}
.rating-avg { text-align: center; }
.rating-avg .big { font-size: 56px; font-weight: 800; color: #0369a1; line-height: 1; }
.rating-avg .stars-lg { font-size: 20px; color: #f59e0b; margin: 4px 0; }
.rating-avg small { color: #0369a1; font-size: 13px; opacity: .75; }

.rating-bars { flex: 1; }
.rating-bar-row { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; font-size: 13px; color: #0369a1; }
.rating-bar-row .bar { flex: 1; height: 7px; background: rgba(255,255,255,.6); border-radius: 4px; overflow: hidden; }
.rating-bar-row .bar-fill { height: 100%; background: linear-gradient(90deg, #f59e0b, #fbbf24); border-radius: 4px; transition: width .8s cubic-bezier(.16,1,.3,1); }
.rating-bar-row .cnt { width: 30px; text-align: right; color: #0369a1; opacity: .7; }

/* ============================================================
   REVIEWS
   ============================================================ */
.review-item {
    border-bottom: 1px solid rgba(186,230,253,.5);
    padding: 16px 0;
    transition: background .2s;
}
.review-item:last-child { border-bottom: none; }
.review-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px; }
.review-user { font-weight: 700; font-size: 14px; color: #0c4a6e; }
.review-date { font-size: 12px; color: #7dd3fc; }
.review-stars { color: #f59e0b; font-size: 14px; margin-bottom: 6px; }
.review-content { font-size: 14px; color: #0369a1; line-height: 1.6; }

.review-form {
    background: linear-gradient(135deg, #e0f2fe, #bae6fd);
    border-radius: 14px; padding: 22px; margin-top: 26px;
    border: 1px solid rgba(125,211,252,.4);
}
.review-form h4 { font-size: 15px; font-weight: 700; margin-bottom: 16px; color: #0c4a6e; }

.star-rating { display: flex; gap: 6px; margin-bottom: 12px; flex-direction: row-reverse; justify-content: flex-end; }
.star-rating input { display: none; }
.star-rating label { font-size: 28px; color: rgba(125,211,252,.5); cursor: pointer; transition: color .12s, transform .12s; }
.star-rating label:hover { transform: scale(1.15); }
.star-rating input:checked ~ label,
.star-rating label:hover,
.star-rating label:hover ~ label { color: #f59e0b; }

.form-textarea {
    width: 100%;
    border: 1px solid rgba(125,211,252,.5);
    border-radius: 10px; padding: 10px;
    font-size: 14px; outline: none; resize: vertical;
    font-family: inherit;
    background: rgba(255,255,255,.75);
    backdrop-filter: blur(6px);
    color: #0c4a6e;
    transition: border-color .2s, box-shadow .2s;
}
.form-textarea:focus {
    border-color: #0ea5e9;
    box-shadow: 0 0 0 3px rgba(14,165,233,.15);
}

.btn-submit-review {
    margin-top: 10px; padding: 10px 24px;
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    color: #fff; border: none; border-radius: 8px;
    font-size: 14px; font-weight: 700; cursor: pointer;
    transition: opacity .2s, transform .15s, box-shadow .2s;
    box-shadow: 0 4px 14px rgba(14,165,233,.3);
}
.btn-submit-review:hover {
    opacity: .9; transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(14,165,233,.4);
}

/* ============================================================
   RELATED PRODUCTS
   ============================================================ */
.related-section {
    margin-top: 48px;
    background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 35%, #7dd3fc 65%, #38bdf8 100%);
    border-radius: 16px; padding: 24px 24px 28px;
    position: relative; overflow: hidden;
    box-shadow: 0 4px 20px rgba(14,165,233,.18);
}
.related-section::before {
    content: ''; position: absolute; top: -60px; right: -60px;
    width: 200px; height: 200px; border-radius: 50%;
    background: rgba(255,255,255,.18); pointer-events: none;
}
.related-section::after {
    content: ''; position: absolute; bottom: -40px; left: -40px;
    width: 140px; height: 140px; border-radius: 50%;
    background: rgba(255,255,255,.14); pointer-events: none;
}

.related-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 18px; position: relative; z-index: 1;
}
.related-header h2 { font-size: 16px; font-weight: 800; color: #0c4a6e; text-transform: uppercase; letter-spacing: .3px; margin: 0; padding-left: 10px; border-left: 3px solid #0ea5e9; }
.related-header a {
    font-size: 12.5px; color: #0c4a6e; font-weight: 700;
    text-decoration: none;
    background: rgba(255,255,255,.55); padding: 5px 12px;
    border-radius: 20px; border: 1px solid rgba(255,255,255,.8);
    transition: background .2s;
}
.related-header a:hover { background: rgba(255,255,255,.8); }

.related-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 14px;
    position: relative; z-index: 1;
}
@media (max-width: 960px) { .related-grid { grid-template-columns: repeat(4, 1fr); } }
@media (max-width: 640px) { .related-grid { grid-template-columns: repeat(2, 1fr); } }

.product-card {
    display: flex; flex-direction: column; height: 100%;
    border-radius: 12px; overflow: hidden;
    text-decoration: none; color: inherit;
    transition: transform .22s cubic-bezier(.16,1,.3,1), box-shadow .22s, border-color .22s;
    position: relative;
    background: rgba(255,255,255,.85);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,.7);
    box-shadow: 0 2px 10px rgba(14,165,233,.08);
    cursor: pointer;
}
.product-card:hover {
    transform: translateY(-5px) scale(1.02);
    box-shadow: 0 14px 32px rgba(14,165,233,.22);
    border-color: rgba(255,255,255,.95);
}
/* shine sweep */
.product-card::after {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,.4) 50%, transparent 60%);
    transform: translateX(-120%); transition: transform .5s ease;
    pointer-events: none; z-index: 3;
}
.product-card:hover::after { transform: translateX(120%); }

.product-card-img {
    height: 140px;
    background: linear-gradient(160deg, #f0f9ff, #e0f2fe);
    display: flex; align-items: center; justify-content: center;
    overflow: hidden; flex-shrink: 0;
}
.product-card-img img {
    width: 100%; height: 100%; object-fit: contain;
    padding: 8px; box-sizing: border-box;
    transition: transform .35s cubic-bezier(.16,1,.3,1);
}
.product-card:hover .product-card-img img { transform: scale(1.07); }

.product-card-body { padding: 12px; display: flex; flex-direction: column; flex: 1; }
.product-card-name {
    font-size: 12.5px; font-weight: 600; margin-bottom: 6px;
    line-height: 1.4; min-height: 35px;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    color: #0f4c75;
}
.product-card-price { font-size: 14.5px; font-weight: 800; color: #0369a1; margin-top: auto; }
.stars { font-size: 11px; color: #f59e0b; }
</style>
@endpush

@section('content')
{{-- Sky Canvas --}}
<canvas id="sky-canvas" aria-hidden="true"></canvas>

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
            <span style="color:#0c4a6e;font-weight:600">{{ $product->name }}</span>
        </div>
        <form action="{{ route('compare.add', $product) }}" method="POST" style="margin:0">
            @csrf
            <button type="submit" style="
                padding: 6px 14px;
                background: rgba(255,255,255,.7);
                backdrop-filter: blur(6px);
                border: 1px solid rgba(125,211,252,.5);
                border-radius: 8px;
                color: #0369a1; font-size: 13px; font-weight: 600;
                cursor: pointer; transition: all .2s;
            ">
                <i class="fas fa-code-compare"></i> So sánh
            </button>
        </form>
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
                    <span class="discount-tag">-{{ $product->discount_percent }}%</span>
                @endif
                @if($galleryImages->isNotEmpty())
                    <img src="{{ $galleryImages->first() }}" alt="{{ $product->name }}" id="mainImg">
                @else
                    <i class="fas fa-image fa-3x" style="color:#7dd3fc"></i>
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
            <div class="product-brand">{{ $product->brand->name ?? '' }}</div>
            <h1 class="product-name">{{ $product->name }}</h1>

            <div class="product-rating">
                <span class="stars">
                    @for($i=1;$i<=5;$i++){{ $i <= round($product->avg_rating) ? '★' : '☆' }}@endfor
                </span>
                <span>{{ $product->avg_rating }}/5</span>
                <span style="color:#bae6fd">|</span>
                <span>{{ $product->reviews_count }} đánh giá</span>
                @if($product->variants->isEmpty())
                <span style="color:#bae6fd">|</span>
                <span style="opacity:.7">{{ number_format($product->stock) }} còn lại</span>
                @endif
            </div>

            <div class="price-block" id="priceBlock">
                <span class="price-current" id="priceDisplay">{{ number_format($product->sale_price) }}đ</span>
                @if($product->discount_percent > 0)
                    <span class="price-old" id="priceOldDisplay">{{ number_format($product->price) }}đ</span>
                    <span class="price-pct" id="pricePctDisplay">-{{ $product->discount_percent }}%</span>
                @else
                    <span class="price-old" id="priceOldDisplay" style="display:none"></span>
                    <span class="price-pct" id="pricePctDisplay" style="display:none"></span>
                @endif
            </div>

            <div id="stockDisplay" style="margin:8px 0 16px">
                @if($product->stock > 0)
                    <div class="in-stock"><i class="fas fa-check-circle"></i> Còn hàng ({{ $product->stock }})</div>
                @else
                    <div class="out-of-stock"><i class="fas fa-times-circle"></i> Hết hàng</div>
                @endif
            </div>

            @if($product->variants->isNotEmpty())
            <div id="variantSelector"></div>
            <div id="variantAlert" style="display:none;color:#e53935;font-size:13px;margin-bottom:10px">
                <i class="fas fa-exclamation-triangle"></i> Phiên bản này hiện không có sẵn.
            </div>
            @endif

            {{-- Nút biến thể --}}
            <div class="action-btns" id="actionBtns" style="display:none">
                <form action="{{ route('cart.add') }}" method="POST" style="flex:1" id="formAddCart">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="variant_id" id="inputVariantId" value="">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="btn-add-cart" style="width:100%" id="btnAddCart">
                        <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                    </button>
                </form>
                <form action="{{ route('cart.buy-now') }}" method="POST" style="flex:1" id="formBuyNow">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="variant_id" id="inputVariantIdBuy" value="">
                    <button type="submit" class="btn-buy-now" style="width:100%" id="btnBuyNow">
                        <i class="fas fa-bolt"></i> Mua ngay
                    </button>
                </form>
            </div>

            {{-- Sản phẩm đơn giản --}}
            @if($product->variants->isEmpty())
            <div class="action-btns">
                @if($product->stock > 0)
                <form action="{{ route('cart.add') }}" method="POST" style="flex:1">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="btn-add-cart" style="width:100%">
                        <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                    </button>
                </form>
                <form action="{{ route('cart.buy-now') }}" method="POST" style="flex:1">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <button type="submit" class="btn-buy-now" style="width:100%">
                        <i class="fas fa-bolt"></i> Mua ngay
                    </button>
                </form>
                @else
                <button class="btn-add-cart" style="width:100%;opacity:.5;cursor:not-allowed" disabled>
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
        <button class="tab-btn active" onclick="switchTab(this,'tab-desc')">Mô tả sản phẩm</button>
        <button class="tab-btn" onclick="switchTab(this,'tab-specs')">Thông số kỹ thuật</button>
        <button class="tab-btn" onclick="switchTab(this,'tab-reviews')">
            Đánh giá ({{ $product->reviews_count }})
        </button>
    </div>

    {{-- Tab: Mô tả --}}
    <div id="tab-desc" class="tab-panel active">
        <div class="tab-panel-inner">
            @if($product->description)
                <div style="font-size:14px;line-height:1.8;color:#0369a1;max-width:800px">
                    {!! nl2br(e($product->description)) !!}
                </div>
            @else
                <p style="color:#7dd3fc;padding:24px 0">Chưa có mô tả cho sản phẩm này.</p>
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
            <p id="specsEmptyMsg" style="color:#7dd3fc;padding:24px 0;{{ $product->attributes->isNotEmpty() ? 'display:none' : '' }}">
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
                        <i class="fas fa-star" style="color:#f59e0b;font-size:11px"></i>
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
            <p style="color:#7dd3fc;padding:16px 0">Chưa có đánh giá nào. Hãy là người đầu tiên đánh giá!</p>
            @endforelse

            @if($reviews->hasPages())
            <div style="margin-top:16px">{{ $reviews->links() }}</div>
            @endif

            @auth
            <div class="review-form">
                <h4><i class="fas fa-pen" style="margin-right:8px"></i>Viết đánh giá của bạn</h4>
                <form action="{{ route('products.review', $product->id) }}" method="POST">
                    @csrf
                    <div style="margin-bottom:12px">
                        <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px;color:#0c4a6e">Chọn sao:</label>
                        <div class="star-rating" id="starRating">
                            @for($i=5;$i>=1;$i--)
                            <input type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}" {{ old('rating')==$i ? 'checked' : '' }}>
                            <label for="star{{ $i }}">★</label>
                            @endfor
                        </div>
                        @error('rating')<span style="color:#e53935;font-size:12px">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px;color:#0c4a6e">Nội dung (tuỳ chọn):</label>
                        <textarea name="content" rows="4" class="form-textarea"
                                  placeholder="Chia sẻ trải nghiệm của bạn...">{{ old('content') }}</textarea>
                    </div>
                    <button type="submit" class="btn-submit-review">
                        <i class="fas fa-paper-plane"></i> Gửi đánh giá
                    </button>
                </form>
            </div>
            @else
            <div style="background:rgba(186,230,253,.3);border-radius:10px;padding:20px;margin-top:24px;text-align:center;font-size:14px;color:#0369a1">
                <a href="{{ route('login') }}" style="color:#0c4a6e;font-weight:700">Đăng nhập</a> để viết đánh giá.
            </div>
            @endauth
        </div>
    </div>

    {{-- ===== RELATED PRODUCTS ===== --}}
    @if($relatedProducts->isNotEmpty())
    <div class="related-section reveal">
        <div class="related-header">
            <h2>Sản phẩm liên quan</h2>
            <a href="{{ route('products.index', ['category' => $product->category_id]) }}">Xem tất cả →</a>
        </div>
        <div class="related-grid stagger-children">
            @foreach($relatedProducts as $p)
            <a href="{{ route('products.show', $p->slug) }}" class="product-card">
                <div class="product-card-img">
                    @if($p->first_image)
                        <img src="{{ $p->first_image }}" alt="{{ $p->name }}" loading="lazy">
                    @else
                        <i class="fas fa-image fa-2x" style="color:#7dd3fc"></i>
                    @endif
                </div>
                <div class="product-card-body">
                    <div class="product-card-name">{{ $p->name }}</div>
                    <div class="product-card-price">{{ number_format($p->sale_price) }}đ</div>
                    <div class="stars">
                        @for($i=1;$i<=5;$i++){{ $i <= round($p->avg_rating) ? '★' : '☆' }}@endfor
                        <span style="color:#7dd3fc">({{ $p->reviews_count }})</span>
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
        img.style.transform = 'scale(.96)';
        setTimeout(() => {
            img.src = src;
            img.style.transition = 'opacity .3s, transform .3s';
            img.style.opacity = '1';
            img.style.transform = 'scale(1)';
        }, 120);
    }
}

/* ============================================================
   VARIANT SELECTION (logic giữ nguyên, chỉ đổi màu sắc CSS)
   ============================================================ */
@php
$variantsForJs = $product->variants->map(function($v) {
    return [
        'id'               => $v->id,
        'price'            => (float) $v->price,
        'discount_percent' => (int)   $v->discount_percent,
        'stock'            => (int)   $v->stock,
        'is_active'        => (bool)  $v->is_active,
        'attrs'            => $v->variantAttributes
            ->filter(fn($va) => $va->attribute->is_variant)
            ->mapWithKeys(function($va) {
                return [$va->attribute->name => $va->value];
            })->toArray(),
    ];
})->values()->toArray();
@endphp

@if($product->variants->isNotEmpty())
(function() {
    const VARIANTS    = {!! json_encode($variantsForJs, JSON_UNESCAPED_UNICODE) !!};
    const BASE_ATTRS  = @json($product->attributes->mapWithKeys(fn($pa) => [$pa->attribute->name => $pa->value]));
    const MAIN_ATTR_NAMES = @json($product->attributes->pluck('attribute')->filter(fn($a) => $a && $a->is_variant)->pluck('name')->values());
    const BASE_PRICE    = {{ (float)$product->price }};
    const BASE_DISCOUNT = {{ (int)$product->discount_percent }};
    const BASE_STOCK    = {{ (int)$product->stock }};

    function fmt(n) { return Math.round(n).toLocaleString('vi-VN') + 'đ'; }
    function esc(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

    const BASE_MAIN_ATTRS = {};
    MAIN_ATTR_NAMES.forEach(name => { if (BASE_ATTRS[name] !== undefined) BASE_MAIN_ATTRS[name] = BASE_ATTRS[name]; });

    const ALL_OPTIONS = [
        { id: null, price: BASE_PRICE, discount_percent: BASE_DISCOUNT, stock: BASE_STOCK, is_active: true, attrs: BASE_MAIN_ATTRS },
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

    const selectedAttrs = {};
    DIFF_KEYS.forEach(key => { selectedAttrs[key] = BASE_ATTRS[key] ?? valuesForKey(key)[0]; });

    function findMatchingOption() {
        return ALL_OPTIONS.find(o => DIFF_KEYS.every(key => (o.attrs[key] ?? '') === selectedAttrs[key])) || null;
    }

    function isValueAvailable(key, value) {
        return ALL_OPTIONS.some(o => o.attrs[key] === value && o.is_active !== false && o.stock > 0);
    }

    function applyOption(option) {
        const alertEl    = document.getElementById('variantAlert');
        const actionBtns = document.getElementById('actionBtns');
        const addBtn     = document.getElementById('btnAddCart');
        const buyBtn     = document.getElementById('btnBuyNow');

        if (!option) {
            if (alertEl) alertEl.style.display = '';
            if (actionBtns) actionBtns.style.display = 'none';
            renderSpecs(selectedAttrs);
            return;
        }

        if (alertEl) alertEl.style.display = 'none';
        if (actionBtns) actionBtns.style.display = '';

        ['inputVariantId', 'inputVariantIdBuy'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = option.id ?? '';
        });

        const soldOut = option.stock <= 0 || option.is_active === false;
        if (addBtn) { addBtn.disabled = soldOut; addBtn.style.opacity = soldOut ? '.5' : '1'; }
        if (buyBtn) { buyBtn.disabled = soldOut; buyBtn.style.opacity = soldOut ? '.5' : '1'; }

        renderPrice(option.price, option.discount_percent);
        renderStock(option.stock);
        renderSpecs(option.attrs);
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
                if (!isValueAvailable(key, value)) { btn.classList.add('opt-btn-disabled'); btn.title = 'Hết hàng / không khả dụng'; }
                btn.addEventListener('click', function () {
                    selectedAttrs[key] = value;
                    btnWrap.querySelectorAll('.opt-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    document.getElementById(`lbl-${key}`).textContent = value;
                    applyOption(findMatchingOption());
                });
                btnWrap.appendChild(btn);
            });
            group.appendChild(btnWrap);
            selector.appendChild(group);
        });
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

    (function initVariant() { applyOption(findMatchingOption() || ALL_OPTIONS[0]); })();
})();
@endif

/* ============================================================
   ANIMATIONS
   ============================================================ */
(function () {

    /* ---- Canvas clouds ---- */
    const canvas = document.getElementById('sky-canvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let W, H, clouds = [];
        function resize() { W = canvas.width = window.innerWidth; H = canvas.height = window.innerHeight; }
        window.addEventListener('resize', resize); resize();
        function makeCloud() {
            return { x: Math.random() * W * 1.2, y: Math.random() * H * .6,
                     r: 50 + Math.random() * 110, dx: .12 + Math.random() * .2,
                     alpha: .05 + Math.random() * .1 };
        }
        for (let i = 0; i < 8; i++) clouds.push(makeCloud());
        function drawCloud(c) {
            const g = ctx.createRadialGradient(c.x,c.y,0,c.x,c.y,c.r);
            g.addColorStop(0, `rgba(255,255,255,${c.alpha})`);
            g.addColorStop(.6, `rgba(186,230,253,${c.alpha*.6})`);
            g.addColorStop(1, 'rgba(186,230,253,0)');
            ctx.beginPath(); ctx.arc(c.x,c.y,c.r,0,Math.PI*2);
            ctx.fillStyle = g; ctx.fill();
            [-.5,.5].forEach(o => {
                ctx.beginPath(); ctx.arc(c.x+c.r*.55*o, c.y-c.r*.18, c.r*.72, 0, Math.PI*2);
                ctx.fillStyle = `rgba(255,255,255,${c.alpha*.7})`; ctx.fill();
            });
        }
        (function anim() {
            ctx.clearRect(0,0,W,H);
            clouds.forEach(c => { drawCloud(c); c.x += c.dx; if (c.x-c.r > W*1.2) { c.x=-c.r*2; c.y=Math.random()*H*.6; } });
            requestAnimationFrame(anim);
        })();
    }

    /* ---- Bubbles ---- */
    function spawnBubble() {
        const el = document.createElement('div');
        el.className = 'bubble';
        const size = 4 + Math.random() * 14, dur = 8 + Math.random() * 12;
        el.style.cssText = [`width:${size}px`,`height:${size}px`,`left:${Math.random()*100}vw`,
            `bottom:-${size}px`,`animation-duration:${dur}s`,`animation-delay:${Math.random()*5}s`].join(';');
        document.body.appendChild(el);
        setTimeout(() => el.remove(), (dur+5)*1000);
    }
    for (let i = 0; i < 8; i++) spawnBubble();
    setInterval(spawnBubble, 3500);

    /* ---- Scroll Reveal ---- */
    const io = new IntersectionObserver(entries => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('revealed'); io.unobserve(e.target); } });
    }, { threshold: 0.07, rootMargin: '0px 0px -30px 0px' });
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

    /* ---- Ripple on related product cards ---- */
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', function (e) {
            const rect = card.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height) * 1.6;
            const ripple = document.createElement('span');
            ripple.className = 'ripple-wave';
            ripple.style.cssText = [`width:${size}px`,`height:${size}px`,
                `left:${e.clientX-rect.left-size/2}px`,`top:${e.clientY-rect.top-size/2}px`].join(';');
            card.appendChild(ripple);
            ripple.addEventListener('animationend', () => ripple.remove());
        });
    });

    /* ---- 3D Tilt on related product cards ---- */
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('mousemove', function (e) {
            const r = card.getBoundingClientRect();
            const dx = (e.clientX-r.left-r.width/2)/(r.width/2);
            const dy = (e.clientY-r.top-r.height/2)/(r.height/2);
            card.style.transform = `perspective(600px) rotateX(${-dy*5}deg) rotateY(${dx*5}deg) translateY(-5px) scale(1.02)`;
        });
        card.addEventListener('mouseleave', function () {
            card.style.transform = '';
            card.style.transition = 'transform .4s cubic-bezier(.16,1,.3,1), box-shadow .22s, border-color .22s';
            setTimeout(() => card.style.transition = '', 420);
        });
    });

    /* ---- Thumb hover pulse ---- */
    document.querySelectorAll('.thumb').forEach(t => {
        t.addEventListener('mouseenter', () => t.style.transform = 'translateY(-3px) scale(1.06)');
        t.addEventListener('mouseleave', () => t.style.transform = '');
    });

    /* ---- Benefit item stagger on load ---- */
    const benefitItems = document.querySelectorAll('.benefit-item');
    benefitItems.forEach((item, i) => {
        item.style.opacity = '0';
        item.style.transform = 'translateX(12px)';
        item.style.transition = `opacity .5s ${i*.1}s, transform .5s ${i*.1}s`;
        setTimeout(() => { item.style.opacity = '1'; item.style.transform = 'translateX(0)'; }, 400 + i*100);
    });

})();
</script>
@endpush
