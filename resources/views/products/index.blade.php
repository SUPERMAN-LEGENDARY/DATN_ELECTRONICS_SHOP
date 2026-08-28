@extends('layouts.app')
@php
    $topGlobalEvent = \App\Models\Event::active()->ongoing()->ordered()->first();
@endphp
@section('title', 'Tất cả sản phẩm - ElectronicShop')
@php $showSearch = true; @endphp

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
    --sam-gray:   #f7f7f7;   /* card / tile background */
    --sam-gray-2: #eaeaea;   /* hover on gray */
    --sam-line:   #ddd;      /* borders */
    --sam-muted:  #666;      /* secondary text */
    --sam-blue:   #2189ff;   /* links */
    --sam-star:   #f5a623;
    --sam-sale:   #d90000;
    --sam-font:   'Inter', 'SamsungOne', arial, sans-serif;
    --sam-head:   'Poppins', 'SamsungSharpSans', arial, sans-serif;
}

/* ============================================================
   PAGE
   ============================================================ */
body {
    background: var(--sam-white);
    color: var(--sam-black);
    font-family: var(--sam-font);
    -webkit-font-smoothing: antialiased;
}

.products-page {
    padding: 0 0 80px;
    max-width: 1440px;
    margin: 0 auto;
}

/* ============================================================
   BREADCRUMB
   ============================================================ */
.breadcrumb-wrap {
    font-size: 12px;
    color: var(--sam-muted);
    padding: 20px 0 0;
    display: block;
}
.breadcrumb-wrap a { color: var(--sam-muted); text-decoration: none; }
.breadcrumb-wrap a:hover { color: var(--sam-black); text-decoration: underline; }
.breadcrumb-wrap span { color: var(--sam-black); }

/* ============================================================
   PAGE HEADLINE — Samsung Sharp Sans, 40px / 700
   ============================================================ */
.page-headline {
    font-family: var(--sam-head);
    font-size: 40px;
    font-weight: 700;
    letter-spacing: -.02em;
    line-height: 1.2;
    color: var(--sam-black);
    padding: 44px 0 22px;
    margin: 0;
}
@media (max-width: 640px) { .page-headline { font-size: 28px; padding: 28px 0 16px; } }

/* ============================================================
   CATEGORY TILES (Samsung "Galaxy Tab / Watch / Buds" row)
   ============================================================ */
/* CATEGORY TILES — nhỏ gọn */
.cat-tiles {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
    gap: 12px;
    margin-bottom: 28px;
    overflow-x: auto;
    scrollbar-width: none;
}

.cat-tiles::-webkit-scrollbar { display: none; }

.cat-tile {
    width: 150px;
    min-width: 150px;
    height: 96px;
    min-height: 96px;
    box-sizing: border-box;
    background: var(--sam-gray);
    border: 1px solid transparent;
    border-radius: 14px;
    padding: 10px 14px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 6px;
    text-align: center;
    text-decoration: none;
    color: var(--sam-black);
    font-size: 14px;
    font-weight: 700;
    line-height: 1.25;
    transition: background .2s, border-color .2s;
}

.cat-tile:hover { background: var(--sam-gray-2); }

.cat-tile.active {
    background: var(--sam-white);
    border-color: var(--sam-black);
}

.cat-tile__img {
    width: 38px;
    height: 38px;
    object-fit: contain;
    border-radius: 7px;
    flex-shrink: 0;
}

.cat-tile__icon {
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #c9c9c9;
    font-size: 14px;
    flex-shrink: 0;
}

/* ============================================================
   TAB NAV (Tất cả / thương hiệu) — centered underline nav
   ============================================================ */
.brand-nav {
    display: flex;
    justify-content: center;
    gap: 32px;
    flex-wrap: wrap;
    margin-bottom: 36px;
}
.brand-nav a {
    font-size: 16px;
    font-weight: 400;
    color: var(--sam-black);
    text-decoration: none;
    padding-bottom: 6px;
    border-bottom: 2px solid transparent;
    transition: border-color .2s;
}
.brand-nav a:hover { border-bottom-color: var(--sam-line); }
.brand-nav a.active { font-weight: 700; border-bottom-color: var(--sam-black); }

/* ============================================================
   FILTER BAR — "Bộ lọc | 52 Kết quả" ... "Sắp xếp"
   ============================================================ */
.filter-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 14px 0;
    border-top: 1px solid var(--sam-line);
    border-bottom: 1px solid var(--sam-line);
    flex-wrap: wrap;
}
.filter-bar__left { display: flex; align-items: center; gap: 16px; }
.filter-bar__title {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 16px;
    font-weight: 700;
    color: var(--sam-black);
    background: none;
    border: none;
    padding: 0;
    font-family: inherit;
    cursor: pointer;
}
.filter-bar__title i { font-size: 11px; transition: transform .2s; }
.filter-bar__title.is-collapsed i { transform: rotate(-90deg); }
.filter-bar__sep { width: 1px; height: 14px; background: var(--sam-line); }
.filter-bar__count { font-size: 14px; color: var(--sam-black); }
.filter-bar__count strong { font-weight: 700; }

.filter-bar__right { display: flex; align-items: center; gap: 8px; }
.sort-label { font-size: 14px; font-weight: 700; }
.sort-select {
    border: none;
    background: transparent;
    font-family: inherit;
    font-size: 14px;
    color: var(--sam-muted);
    font-weight: 400;
    padding: 4px 22px 4px 4px;
    cursor: pointer;
    outline: none;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23000' stroke-width='1.4' fill='none'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 2px center;
}
.sort-select:hover { color: var(--sam-black); }

.btn-clear-filter {
    font-size: 13px;
    color: var(--sam-black);
    text-decoration: underline;
    font-weight: 400;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.btn-clear-filter:hover { color: var(--sam-muted); }

/* ============================================================
   FILTER CHIPS — white pill, 1px #ddd, radius 8px, 14px/700
   ============================================================ */
.chip-row {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    padding: 22px 0 0;
}
.chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    height: 40px;
    padding: 0 15px;
    background: var(--sam-white);
    border: 1px solid var(--sam-line);
    border-radius: 8px;
    font-family: inherit;
    font-size: 14px;
    font-weight: 700;
    color: var(--sam-black);
    cursor: pointer;
    transition: border-color .2s, background .2s;
}
.chip:hover { border-color: #999; }
.chip.open, .chip.has-value { border-color: var(--sam-black); }
.chip__caret { transition: transform .2s; font-size: 10px; }
.chip.open .chip__caret { transform: rotate(180deg); }
.chip__badge {
    background: var(--sam-black);
    color: var(--sam-white);
    font-size: 11px;
    font-weight: 700;
    border-radius: 10px;
    padding: 1px 7px;
    line-height: 1.5;
}

/* Panel that opens under the chip row */
.chip-panel {
    display: none;
    border: 1px solid var(--sam-line);
    border-radius: 12px;
    background: var(--sam-white);
    padding: 24px;
    margin-top: 12px;
}
.chip-panel.open { display: block; }
.chip-panel__title {
    font-size: 14px;
    font-weight: 700;
    margin: 0 0 16px;
}
.chip-panel__grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 10px 24px;
}
.chip-panel__foot {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 22px;
    padding-top: 18px;
    border-top: 1px solid var(--sam-line);
}

/* option link (danh mục / thương hiệu) */
.opt-link {
    font-size: 14px;
    color: var(--sam-black);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 0;
}
.opt-link__img {
    width: 22px;
    height: 22px;
    object-fit: cover;
    border-radius: 5px;
    flex-shrink: 0;
}
.opt-link:hover { text-decoration: underline; }
.opt-link.active { font-weight: 700; }
.opt-link.active::before { content: '✓ '; }

/* checkbox rows — Samsung square checkbox */
.check-row {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    cursor: pointer;
    padding: 5px 0;
}
.check-row input[type="checkbox"] {
    appearance: none;
    width: 18px; height: 18px;
    border: 1px solid #767676;
    border-radius: 2px;
    background: var(--sam-white);
    cursor: pointer;
    flex-shrink: 0;
    position: relative;
}
.check-row input[type="checkbox"]:checked {
    background: var(--sam-black);
    border-color: var(--sam-black);
}
.check-row input[type="checkbox"]:checked::after {
    content: '';
    position: absolute;
    left: 5px; top: 1px;
    width: 5px; height: 10px;
    border: solid var(--sam-white);
    border-width: 0 1.6px 1.6px 0;
    transform: rotate(45deg);
}

.price-range { display: flex; align-items: center; gap: 8px; margin-top: 14px; }
.price-range input {
    width: 120px;
    border: 1px solid var(--sam-line);
    border-radius: 8px;
    padding: 9px 12px;
    font-family: inherit;
    font-size: 14px;
    color: var(--sam-black);
    outline: none;
}
.price-range input:focus { border-color: var(--sam-black); }
.price-range span { font-size: 14px; color: var(--sam-muted); }

/* ============================================================
   CTA — Samsung pill buttons (radius 20px, 14px/700)
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
    transition: background .2s, color .2s, border-color .2s;
}
.cta--black { background: var(--sam-black); color: var(--sam-white); }
.cta--black:hover { background: #3d3d3d; color: var(--sam-white); }
.cta--outline { background: transparent; color: var(--sam-black); border-color: var(--sam-black); }
.cta--outline:hover { background: rgba(0,0,0,.05); }
.cta--ghost { background: var(--sam-white); color: var(--sam-black); border-color: var(--sam-line); }
.cta--block { width: 100%; }

/* ============================================================
   PRODUCT GRID — Samsung: 4 up, 24px gutter
   ============================================================ */
.grid-4 {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
    padding-top: 40px;
}
@media (max-width: 1280px) { .grid-4 { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 900px)  { .grid-4 { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 560px)  { .grid-4 { grid-template-columns: 1fr; gap: 16px; } }

/* ============================================================
   PRODUCT CARD — bg #f7f7f7, radius 20px, padding 24px, no shadow
   ============================================================ */
.product-card {
    position: relative;
    display: flex;
    flex-direction: column;
    background: var(--sam-gray);
    border-radius: 20px;
    padding: 24px;
    text-decoration: none;
    color: var(--sam-black);
    overflow: hidden;
    transition: background .2s;
}
.product-card:hover { background: #f1f1f1; }

.badge-tag {
    font-family: inherit;
    font-size: 12px;
    font-weight: 700;
    color: var(--sam-sale);
    letter-spacing: .04em;
    text-transform: uppercase;
    margin-bottom: 12px;
    display: block;
}

.wish {
    position: absolute; top: 10px; right: 10px;
    width: 32px; height: 32px; border-radius: 50%;
    background: rgba(255,255,255,.92); backdrop-filter: blur(6px);
    display: flex; align-items: center; justify-content: center;
    color: #94a3b8; font-size: 15px; z-index: 3;
    box-shadow: 0 1px 6px rgba(14,165,233,.18);
    transition: color .2s, transform .2s, background .2s;
    border: none; cursor: pointer; outline: none; padding: 0;
}
.wish:hover { color: #ef4444; transform: scale(1.15); background: rgba(255,255,255,1); }
.wish.active { color: #ef4444; }
.wish.active i { font-weight: 900; }

.product-card-img {
    height: 220px;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 0 20px 0;
    overflow: hidden;
    border-radius: 12px;
    background: var(--sam-gray);
    padding: 8px;
    box-sizing: border-box;
}

/* Ảnh sản phẩm: đầy khung vừa phải, không crop và không méo */
.product-card-img img {
    display: block;
    width: 100%;
    height: 100%;
    min-width: 0;
    min-height: 0;
    object-fit: contain;
    object-position: center;
    padding: 0;
    margin: 0;
    box-sizing: border-box;
    image-rendering: auto;
    transition: none;
}

.product-card:hover .product-card-img img {
    transform: none;
}
.img-placeholder { color: #c9c9c9; }
.product-card-img:has(.img-placeholder) {
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--sam-gray-2);
}

.product-card-body { display: flex; flex-direction: column; gap: 10px; }

.product-card-name {
    font-size: 18px;
    font-weight: 700;
    line-height: 1.45;
    letter-spacing: -.01em;
    color: var(--sam-black);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 52px;
}

.product-card-price { font-size: 18px; font-weight: 700; color: var(--sam-black); }
.price-strike { font-size: 14px; color: var(--sam-muted); text-decoration: line-through; margin-left: 8px; font-weight: 400; }

.stars { font-size: 13px; color: var(--sam-star); letter-spacing: 1px; }
.review-count { color: var(--sam-blue); text-decoration: underline; margin-left: 4px; letter-spacing: 0; }

.card-actions { display: flex; flex-direction: column; gap: 8px; margin-top: 6px; }

/* ============================================================
   SCROLL REVEAL (subtle, Samsung-like fade up)
   ============================================================ */
.reveal { opacity: 0; transform: translateY(20px); transition: opacity .6s cubic-bezier(.33,0,.3,1), transform .6s cubic-bezier(.33,0,.3,1); }
.reveal.revealed { opacity: 1; transform: none; }
.stagger-children > * { opacity: 0; transform: translateY(20px); transition: opacity .5s cubic-bezier(.33,0,.3,1), transform .5s cubic-bezier(.33,0,.3,1); }
.stagger-children.revealed > * { opacity: 1; transform: none; }
.stagger-children.revealed > *:nth-child(1) { transition-delay: .05s; }
.stagger-children.revealed > *:nth-child(2) { transition-delay: .1s; }
.stagger-children.revealed > *:nth-child(3) { transition-delay: .15s; }
.stagger-children.revealed > *:nth-child(4) { transition-delay: .2s; }
.stagger-children.revealed > *:nth-child(5) { transition-delay: .25s; }
.stagger-children.revealed > *:nth-child(6) { transition-delay: .3s; }
.stagger-children.revealed > *:nth-child(n+7) { transition-delay: .35s; }

/* ============================================================
   EMPTY STATE
   ============================================================ */
.empty-state {
    text-align: center;
    padding: 96px 24px;
    background: var(--sam-gray);
    border-radius: 20px;
    margin-top: 40px;
}
.empty-state i { color: #c9c9c9; margin-bottom: 20px; display: block; }
.empty-state p { font-size: 18px; font-weight: 700; margin-bottom: 20px; }

/* ============================================================
   PAGINATION
   ============================================================ */
.pagination-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 16px;
    margin-top: 56px;
}
.pagination-wrap nav { display: flex; flex-direction: column; align-items: center; gap: 16px; }
.pagination-wrap .pagination { display: flex; gap: 4px; list-style: none; margin: 0; padding: 0; }
.pagination-wrap .page-item .page-link {
    min-width: 36px; height: 36px;
    display: flex; align-items: center; justify-content: center;
    border: none; background: transparent;
    border-radius: 50%;
    color: var(--sam-black);
    font-size: 14px; font-weight: 400;
    text-decoration: none;
    transition: background .2s;
}
.pagination-wrap .page-item .page-link:hover { background: var(--sam-gray); }
.pagination-wrap .page-item.active .page-link {
    background: var(--sam-black); color: var(--sam-white); font-weight: 700;
}
.pagination-wrap .page-item.disabled .page-link { color: #c9c9c9; pointer-events: none; }
.pagination-wrap .pagination-info { margin: 0; font-size: 13px; color: var(--sam-muted); text-align: center; }
.pagination-wrap .pagination-info strong { color: var(--sam-black); font-weight: 700; }
</style>
@endpush

@section('content')
<div class="products-page container">

    {{-- Breadcrumb --}}
    <div class="breadcrumb-wrap reveal">
        <a href="{{ route('home') }}">Trang chủ</a>
        <span> / </span>
        <span>Sản phẩm</span>
        @if(request('q'))
            <span> / </span>
            <span>Tìm: "{{ request('q') }}"</span>
        @endif
    </div>

    <h1 class="page-headline reveal">
        @if(request('q'))
            Kết quả tìm kiếm: "{{ request('q') }}"
        @else
            Khám phá tất cả sản phẩm
        @endif
    </h1>

    {{-- ===== CATEGORY TILES ===== --}}
    @if($categories->isNotEmpty())
    <div class="cat-tiles stagger-children">
        <a href="{{ request()->fullUrlWithQuery(['category' => null, 'page' => null]) }}"
           class="cat-tile {{ !request('category') ? 'active' : '' }}">
            <span class="cat-tile__icon" aria-hidden="true"><i class="fas fa-th-large"></i></span>
            <span>Tất cả sản phẩm</span>
        </a>
        @foreach($categories as $cat)
        <a href="{{ request()->fullUrlWithQuery(['category' => $cat->id, 'page' => null]) }}"
           class="cat-tile {{ request('category') == $cat->id ? 'active' : '' }}">
            @if($cat->logo)
                <img src="{{ $cat->logo_url }}" alt="" class="cat-tile__img" loading="lazy">
            @else
                <span class="cat-tile__icon" aria-hidden="true"><i class="fas fa-image"></i></span>
            @endif
            <span>{{ $cat->name }}</span>
        </a>
        @endforeach
    </div>
    @endif

    {{-- ===== BRAND NAV ===== --}}
    @if($brands->isNotEmpty())
    <nav class="brand-nav reveal" aria-label="Thương hiệu">
        <a href="{{ request()->fullUrlWithQuery(['brand' => null, 'page' => null]) }}"
           class="{{ !request('brand') ? 'active' : '' }}">Tất cả</a>
        @foreach($brands as $brand)
        <a href="{{ request()->fullUrlWithQuery(['brand' => $brand->id, 'page' => null]) }}"
           class="{{ request('brand') == $brand->id ? 'active' : '' }}">{{ $brand->name }}</a>
        @endforeach
    </nav>
    @endif

    @php
        $hasActiveFilters = request('category')
            || request('brand')
            || collect(request('price', []))->filter()->isNotEmpty()
            || request('price_from')
            || request('price_to')
            || collect(request('attr', []))->flatten()->filter(fn ($v) => $v !== null && $v !== '')->isNotEmpty();

        $priceCount = collect(request('price', []))->filter()->count()
            + (request('price_from') ? 1 : 0)
            + (request('price_to') ? 1 : 0);
    @endphp

    {{-- ===== FILTER BAR ===== --}}
    <div class="filter-bar reveal">
        <div class="filter-bar__left">
            <button type="button" class="filter-bar__title is-collapsed" id="filterToggle"
                    aria-expanded="false" aria-controls="filterChipsWrap">
                Bộ lọc <i class="fas fa-chevron-down" aria-hidden="true"></i>
            </button>
            <span class="filter-bar__sep"></span>
            <span class="filter-bar__count"><strong>{{ number_format($totalProducts) }}</strong> Kết quả</span>
            @if($hasActiveFilters)
                <a href="{{ route('products.index', request('q') ? ['q' => request('q')] : []) }}" class="btn-clear-filter">Xóa tất cả</a>
            @endif
        </div>
        <div class="filter-bar__right">
            <span class="sort-label">Sắp xếp</span>
            <form id="sortForm" method="GET" action="{{ route('products.index') }}">
                @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
                @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                @if(request('brand'))<input type="hidden" name="brand" value="{{ request('brand') }}">@endif
                <select class="sort-select" name="sort" onchange="document.getElementById('sortForm').submit()">
                    <option value="">Theo Khuyến Nghị</option>
                    <option value="price_asc"  {{ request('sort')=='price_asc'  ? 'selected' : '' }}>Giá: Thấp đến cao</option>
                    <option value="price_desc" {{ request('sort')=='price_desc' ? 'selected' : '' }}>Giá: Cao đến thấp</option>
                    <option value="newest"     {{ request('sort')=='newest'     ? 'selected' : '' }}>Mới nhất</option>
                    <option value="rating"     {{ request('sort')=='rating'     ? 'selected' : '' }}>Đánh giá cao</option>
                </select>
            </form>
        </div>
    </div>

    {{-- ===== FILTER CHIPS + PANELS ===== --}}
    <form action="{{ route('products.index') }}" method="GET" id="filterForm">
        @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
        @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif

        <div id="filterChipsWrap" style="display:none">
        <div class="chip-row">
            <button type="button" class="chip {{ request('category') ? 'has-value' : '' }}" data-panel="panel-category">
                Phạm vi sản phẩm <span class="chip__caret">▾</span>
            </button>
            <button type="button" class="chip {{ request('brand') ? 'has-value' : '' }}" data-panel="panel-brand">
                Thương hiệu <span class="chip__caret">▾</span>
            </button>
            <button type="button" class="chip {{ $priceCount ? 'has-value' : '' }}" data-panel="panel-price">
                Khoảng Giá
                @if($priceCount)<span class="chip__badge">{{ $priceCount }}</span>@endif
                <span class="chip__caret">▾</span>
            </button>
            @foreach($attributesFilter as $attr)
                @php $attrCount = collect(request('attr.'.$attr['id'], []))->filter()->count(); @endphp
                <button type="button" class="chip {{ $attrCount ? 'has-value' : '' }}" data-panel="panel-attr-{{ $attr['id'] }}">
                    {{ $attr['name'] }}
                    @if($attrCount)<span class="chip__badge">{{ $attrCount }}</span>@endif
                    <span class="chip__caret">▾</span>
                </button>
            @endforeach
        </div>

        {{-- Panel: Danh mục --}}
        <div class="chip-panel" id="panel-category">
            <h3 class="chip-panel__title">Danh mục</h3>
            <div class="chip-panel__grid">
                <label class="check-row">
                    <input type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }}>
                    <span>Tất cả</span>
                </label>
                @foreach($categories as $cat)
                <label class="check-row">
                    <input type="radio" name="category" value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'checked' : '' }}>
                    @if($cat->logo)<img src="{{ $cat->logo_url }}" alt="" class="opt-link__img" loading="lazy">@endif
                    <span>{{ $cat->name }}</span>
                </label>
                @endforeach
            </div>
            <div class="chip-panel__foot">
                <button type="button" class="cta cta--ghost js-panel-clear">Bỏ chọn</button>
                <button type="submit" class="cta cta--black">Lọc</button>
            </div>
        </div>

        {{-- Panel: Thương hiệu --}}
        <div class="chip-panel" id="panel-brand">
            <h3 class="chip-panel__title">Thương hiệu</h3>
            <div class="chip-panel__grid">
                <label class="check-row">
                    <input type="radio" name="brand" value="" {{ !request('brand') ? 'checked' : '' }}>
                    <span>Tất cả</span>
                </label>
                @foreach($brands as $brand)
                <label class="check-row">
                    <input type="radio" name="brand" value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'checked' : '' }}>
                    @if($brand->logo)<img src="{{ $brand->logo_url }}" alt="" class="opt-link__img" loading="lazy">@endif
                    <span>{{ $brand->name }}</span>
                </label>
                @endforeach
            </div>
            <div class="chip-panel__foot">
                <button type="button" class="cta cta--ghost js-panel-clear">Bỏ chọn</button>
                <button type="submit" class="cta cta--black">Lọc</button>
            </div>
        </div>

        {{-- Panel: Khoảng giá --}}
        <div class="chip-panel" id="panel-price">
            <h3 class="chip-panel__title">Khoảng Giá</h3>
            <div class="chip-panel__grid">
                @foreach([
                    ['Dưới 5 triệu',    '0',        '5000000'],
                    ['5 – 10 triệu',    '5000000',  '10000000'],
                    ['10 – 20 triệu',   '10000000', '20000000'],
                    ['20 – 30 triệu',   '20000000', '30000000'],
                    ['Trên 30 triệu',   '30000000', ''],
                ] as $range)
                <label class="check-row">
                    <input type="checkbox" name="price[]"
                           value="{{ $range[1] }}_{{ $range[2] }}"
                           {{ in_array($range[1].'_'.$range[2], request('price', [])) ? 'checked' : '' }}>
                    <span>{{ $range[0] }}</span>
                </label>
                @endforeach
            </div>
            <div class="price-range">
                <input type="number" name="price_from" placeholder="Từ" value="{{ request('price_from') }}">
                <span>–</span>
                <input type="number" name="price_to" placeholder="Đến" value="{{ request('price_to') }}">
            </div>
            <div class="chip-panel__foot">
                <button type="button" class="cta cta--ghost js-panel-clear">Bỏ chọn</button>
                <button type="submit" class="cta cta--black">Áp dụng</button>
            </div>
        </div>

        {{-- Panels: Thuộc tính (lọc nâng cao) --}}
        @foreach($attributesFilter as $attr)
        <div class="chip-panel" id="panel-attr-{{ $attr['id'] }}">
            <h3 class="chip-panel__title">{{ $attr['name'] }}</h3>
            <div class="chip-panel__grid">
                @foreach($attr['values'] as $value)
                <label class="check-row">
                    <input type="checkbox" name="attr[{{ $attr['id'] }}][]" value="{{ $value }}"
                        {{ in_array($value, request('attr.'.$attr['id'], [])) ? 'checked' : '' }}>
                    <span>{{ $value }}</span>
                </label>
                @endforeach
            </div>
            <div class="chip-panel__foot">
                <button type="button" class="cta cta--ghost js-panel-clear">Bỏ chọn</button>
                <button type="submit" class="cta cta--black">Áp dụng</button>
            </div>
        </div>
        @endforeach
        </div>
    </form>

    {{-- ===== PRODUCT GRID ===== --}}
    @if($products->isEmpty())
        <div class="empty-state reveal">
            <i class="fas fa-search fa-3x"></i>
            <p>Không tìm thấy sản phẩm phù hợp.</p>
            <a href="{{ route('products.index') }}" class="cta cta--black">Xem tất cả sản phẩm</a>
        </div>
    @else
    <!-- Skeleton Container -->
    <div class="sm-skeleton-container" data-target="real-products-list">
        <div class="sm-skeleton-grid">
            @for($i = 0; $i < 8; $i++)
            <div class="sm-skeleton-card">
                <div class="sm-skel-img sm-skel-shimmer"></div>
                <div class="sm-skel-line sm-skel-shimmer"></div>
                <div class="sm-skel-line sm-skel-shimmer"></div>
                <div class="sm-skel-line short sm-skel-shimmer"></div>
                <div class="sm-skel-line price sm-skel-shimmer"></div>
            </div>
            @endfor
        </div>
    </div>

    <div id="real-products-list" style="display:none;">
        <div class="grid-4 stagger-children" id="productsGrid">
            @foreach($products as $product)
        <div class="product-card {{ $product->getActiveEvent() ? 'theme-'.$product->getActiveEvent()->theme_effect : '' }}">
            
            @auth
            <button class="wish wish-btn {{ auth()->user()->wishlists->contains('product_id', $product->id) ? 'active' : '' }}"
                data-product-id="{{ $product->id }}"
                data-url="{{ route('wishlist.toggle', $product->id) }}"
                title="Yêu thích"
                onclick="event.preventDefault(); event.stopPropagation(); toggleWishlist(this)">
                <i class="{{ auth()->user()->wishlists->contains('product_id', $product->id) ? 'fas' : 'far' }} fa-heart"></i>
            </button>
            @else
            <a href="{{ route('login') }}" class="wish" title="Đăng nhập để yêu thích" onclick="event.stopPropagation()">
                <i class="far fa-heart"></i>
            </a>
            @endauth

            
                        @php
                            $discount = 0;
                            if(isset($product->discount_percent) && $product->discount_percent > 0) {
                                $discount = $product->discount_percent;
                            } elseif(isset($product->sale_price) && $product->sale_price > 0 && $product->price > 0) {
                                $discount = round((1 - $product->sale_price / $product->price) * 100);
                            }
                        @endphp
                        @if($discount > 0)
                            <div class="sm-product-ribbon"><span>GIẢM {{ $discount }}%</span></div>
                        @endif

                        
                        @php
                            $cardEvent = $product->getActiveEvent();
                        @endphp
                        @if($cardEvent)
                            <div class="sm-product-event-tree">
                                @if($cardEvent->theme_effect == 'christmas') 🎄
                                @elseif($cardEvent->theme_effect == 'tet') 🏮
                                @elseif($cardEvent->theme_effect == 'womens_day') 🌸
                                @elseif($cardEvent->theme_effect == 'summer') 🌴
                                @endif
                            </div>
                            <div class="sm-product-event-banner"><div class="marquee-wrap"><div class="marquee-inner">✨ {{ mb_strtoupper($cardEvent->title, "UTF-8") }} ✨</div></div></div>
                        @endif


<a href="{{ route('products.show', $product->slug) }}" class="product-card-img" tabindex="-1" aria-hidden="true">
                @if($product->first_image)
                    <img
    src="{{ $product->first_image }}"
    alt="{{ $product->name }}"
    loading="lazy"
    decoding="async"
>
                @else
                    <i class="fas fa-image fa-2x img-placeholder"></i>
                @endif
            </a>

            <div class="product-card-body">
                <a href="{{ route('products.show', $product->slug) }}" class="product-card-name" style="text-decoration:none;color:inherit">{{ $product->name }}</a>

                <div class="stars">
                    @for($i=1;$i<=5;$i++){{ $i <= round($product->avg_rating) ? '★' : '☆' }}@endfor
                    <span class="review-count">({{ $product->reviews_count }})</span>
                </div>

                <div>
                    @if($product->has_price_range)
                        <span class="product-card-price">Từ {{ number_format($product->min_price) }}đ</span>
                    @else
                        <span class="product-card-price">{{ number_format($product->price) }}đ</span>
                        @if($product->discount_percent > 0)
                            <span class="price-strike">{{ number_format($product->price) }}đ</span>
                        @endif
                    @endif
                </div>

                <div class="card-actions">
                    <a href="{{ route('products.show', $product->slug) }}" class="cta cta--black cta--block">Mua ngay</a>
                    <a href="{{ route('products.show', $product->slug) }}" class="cta cta--outline cta--block">Tìm hiểu thêm</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- PAGINATION --}}
    @php $paginator = $products->appends(request()->query()); @endphp
    @if ($paginator->hasPages())
    <div class="pagination-wrap reveal">
        <nav aria-label="Phân trang">
            <ul class="pagination">
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled"><span class="page-link">&lsaquo;</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">&lsaquo;</a></li>
                @endif

                @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <li class="page-item"><a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">&rsaquo;</a></li>
                @else
                    <li class="page-item disabled"><span class="page-link">&rsaquo;</span></li>
                @endif
            </ul>

            <p class="pagination-info">
                Hiển thị <strong>{{ $paginator->firstItem() }}</strong>–<strong>{{ $paginator->lastItem() }}</strong>
                trong tổng <strong>{{ number_format($paginator->total()) }}</strong> kết quả
            </p>
        </nav>
    </div>
    @endif
    </div>
    @endif

</div><!-- /.products-page -->
@endsection

@push('scripts')
<script>
/* ============================================================
   FILTER CHIP PANELS
   ============================================================ */
(function () {
    const chips  = document.querySelectorAll('.chip[data-panel]');
    const panels = document.querySelectorAll('.chip-panel');

    function closeAll() {
        chips.forEach(c => c.classList.remove('open'));
        panels.forEach(p => p.classList.remove('open'));
    }

    chips.forEach(chip => {
        chip.addEventListener('click', function (e) {
            e.stopPropagation();
            const panel  = document.getElementById(this.dataset.panel);
            const isOpen = this.classList.contains('open');
            closeAll();
            if (!isOpen && panel) {
                this.classList.add('open');
                panel.classList.add('open');
            }
        });
    });

    document.querySelectorAll('.js-panel-clear').forEach(btn => {
        btn.addEventListener('click', function () {
            const panel = this.closest('.chip-panel');
            panel.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
            panel.querySelectorAll('input[type="number"]').forEach(i => i.value = '');
            const allRadio = panel.querySelector('input[type="radio"][value=""]');
            if (allRadio) allRadio.checked = true;
        });
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.chip-panel') && !e.target.closest('.chip')) closeAll();
    });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAll(); });

    /* ---- Ẩn/hiện toàn bộ khối bộ lọc ---- */
    const filterToggle = document.getElementById('filterToggle');
    const filterWrap    = document.getElementById('filterChipsWrap');
    if (filterToggle && filterWrap) {
        filterToggle.addEventListener('click', function () {
            const isOpen = filterWrap.style.display !== 'none';
            if (isOpen) {
                closeAll();
                filterWrap.style.display = 'none';
            } else {
                filterWrap.style.display = '';
            }
            filterToggle.classList.toggle('is-collapsed', isOpen);
            filterToggle.setAttribute('aria-expanded', String(!isOpen));
        });
    }
})();

/* ============================================================
   SCROLL REVEAL
   ============================================================ */
(function () {
    const io = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                io.unobserve(entry.target);
            }
        });
    }, { threshold: 0.05, rootMargin: '0px 0px -40px 0px' });

    document.querySelectorAll('.reveal, .stagger-children').forEach(el => io.observe(el));
})();

/* ============================================================
   WISHLIST
   ============================================================ */
/* Wishlist toggle */
let _toastTimer;
function showWishToast(msg, isErr = false) {
    let t = document.getElementById('wishToast');
    if (!t) {
        t = document.createElement('div');
        t.id = 'wishToast';
        t.style.cssText = 'position:fixed;bottom:24px;right:24px;background:rgba(15,23,42,.92);color:#fff;padding:12px 20px;border-radius:12px;font-size:14px;font-weight:500;display:flex;align-items:center;gap:10px;z-index:9999;opacity:0;transform:translateY(10px);transition:opacity .3s,transform .3s;pointer-events:none;';
        t.innerHTML = '<i class="fas fa-check-circle" style="color:#34d399;font-size:16px"></i><span></span>';
        document.body.appendChild(t);
    }
    const icon = t.querySelector('i');
    icon.style.color = isErr ? '#f87171' : '#34d399';
    icon.className = isErr ? 'fas fa-times-circle' : 'fas fa-check-circle';
    t.querySelector('span').textContent = msg;
    t.style.opacity = '1'; t.style.transform = 'translateY(0)';
    clearTimeout(_toastTimer);
    _toastTimer = setTimeout(() => { t.style.opacity = '0'; t.style.transform = 'translateY(10px)'; }, 2800);
}
window.toggleWishlist = function(btn) {
    const url = btn.dataset.url;
    const icon = btn.querySelector('i');
    const isActive = btn.classList.contains('active');
    btn.classList.toggle('active', !isActive);
    icon.className = isActive ? 'far fa-heart' : 'fas fa-heart';
    fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(data => {
        btn.classList.toggle('active', data.wishlisted);
        icon.className = data.wishlisted ? 'fas fa-heart' : 'far fa-heart';
        showWishToast(data.wishlisted ? '♥ Đã thêm vào yêu thích' : 'Nhấp khỏi danh sách yêu thích');
    })
    .catch(() => {
        btn.classList.toggle('active', isActive);
        icon.className = isActive ? 'fas fa-heart' : 'far fa-heart';
        showWishToast('Có lỗi xảy ra, vui lòng thử lại', true);
    });
};
</script>
@endpush