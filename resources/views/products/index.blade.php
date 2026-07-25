@extends('layouts.app')
@section('title', 'Tất cả sản phẩm - ElectronicShop')
@php $showSearch = true; @endphp

@push('styles')
<style>
/* ============================================================
   PAGE WRAPPER — sky gradient (khớp trang chủ)
   ============================================================ */
.products-page {
    padding: 20px 0 60px;
    position: relative;
    min-height: 100vh;
}

body {
    background: linear-gradient(180deg,
        #bae6fd 0%,
        #e0f2fe 18%,
        #f0f9ff 38%,
        #e0f2fe 62%,
        #bae6fd 100%) fixed;
    background-attachment: fixed;
}

/* Canvas clouds */
#sky-canvas {
    position: fixed;
    inset: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 0;
    opacity: .45;
}

/* Floating bubbles */
.bubble {
    position: fixed;
    border-radius: 50%;
    background: radial-gradient(circle at 35% 35%, rgba(255,255,255,.8), rgba(186,230,253,.3));
    border: 1px solid rgba(125,211,252,.4);
    pointer-events: none;
    z-index: 0;
    animation: bubbleRise linear infinite;
}

@keyframes bubbleRise {
    0%   { transform: translateY(0) scale(1);   opacity: .7; }
    80%  { opacity: .4; }
    100% { transform: translateY(-110vh) scale(1.1); opacity: 0; }
}

.products-page > * {
    position: relative;
    z-index: 1;
}

/* ============================================================
   BREADCRUMB
   ============================================================ */
.breadcrumb-wrap {
    font-size: 13px;
    color: #0369a1;
    margin-bottom: 14px;
    display: block;
}

.breadcrumb-wrap a {
    color: #0c4a6e;
    font-weight: 600;
    text-decoration: none;
}

.breadcrumb-wrap a:hover {
    text-decoration: underline;
}

/* ============================================================
   PAGE HEADER
   ============================================================ */
.page-header-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
    flex-wrap: wrap;
    gap: 10px;
}

.page-header-row h1 {
    font-size: 24px;
    font-weight: 800;
    color: #0c4a6e;
}

.results-count {
    font-size: 13.5px;
    color: #0369a1;
    margin-bottom: 20px;
    opacity: .8;
}

/* ============================================================
   LAYOUT
   ============================================================ */
.layout-row {
    display: grid;
    grid-template-columns: 220px 1fr;
    gap: 28px;
    align-items: start;
}

@media (max-width: 860px) {
    .layout-row { grid-template-columns: 1fr; }
}

/* ============================================================
   SIDEBAR — glassmorphism
   ============================================================ */
.filter-sidebar {
    background: rgba(255,255,255,.78);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border-radius: 16px;
    border: 1px solid rgba(186,230,253,.65);
    box-shadow: 0 4px 24px rgba(14,165,233,.12);
    padding: 18px;
    position: sticky;
    top: 90px;
    transition: box-shadow .3s;
}

.filter-sidebar:hover {
    box-shadow: 0 8px 32px rgba(14,165,233,.18);
}

.filter-sidebar-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}

.filter-sidebar-header h2 {
    font-size: 13px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: #0c4a6e;
    margin: 0;
}

.btn-clear-filter {
    font-size: 12.5px;
    color: #e53935;
    font-weight: 700;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 4px 6px;
    border-radius: 6px;
    transition: background .15s;
}

.btn-clear-filter:hover { background: #fdecea; }

.filter-group { margin-bottom: 22px; }
.filter-group:last-of-type { margin-bottom: 0; }

.filter-group h3 {
    font-size: 12px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: #0c4a6e;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid rgba(186,230,253,.7);
}

.filter-group ul { list-style: none; margin: 0; padding: 0; }
.filter-group ul li { margin-bottom: 4px; }

.filter-group ul li a {
    font-size: 13.5px;
    color: #0369a1;
    display: block;
    padding: 6px 8px;
    border-radius: 8px;
    text-decoration: none;
    transition: background .15s, color .15s, transform .15s;
}

.filter-group ul li a:hover {
    background: rgba(186,230,253,.5);
    color: #0c4a6e;
    transform: translateX(3px);
}

.filter-group ul li a.active {
    color: #0c4a6e;
    font-weight: 700;
    background: rgba(125,211,252,.3);
    border-left: 3px solid #0ea5e9;
    padding-left: 10px;
}

.price-check {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    cursor: pointer;
    padding: 4px 6px;
    border-radius: 8px;
    transition: background .15s;
}

.price-check:hover { background: rgba(186,230,253,.35); }
.price-check input { cursor: pointer; accent-color: #0ea5e9; width: 16px; height: 16px; }
.price-check label { font-size: 13.5px; color: #0369a1; cursor: pointer; }
.price-check .cnt { color: #7dd3fc; font-size: 12px; }

.price-range { display: flex; align-items: center; gap: 6px; margin: 10px 0 0; }

.price-range input {
    flex: 1;
    border: 1px solid rgba(125,211,252,.6);
    border-radius: 8px;
    padding: 7px 9px;
    font-size: 12.5px;
    outline: none;
    min-width: 0;
    background: rgba(255,255,255,.7);
    color: #0c4a6e;
    transition: border-color .2s, box-shadow .2s;
}

.price-range input:focus {
    border-color: #0ea5e9;
    box-shadow: 0 0 0 3px rgba(14,165,233,.15);
}

.price-range span { font-size: 12px; color: #0369a1; }

.attr-check { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; cursor: pointer; }
.attr-check input { cursor: pointer; accent-color: #0ea5e9; width: 16px; height: 16px; }
.attr-check label { font-size: 13.5px; color: #0369a1; cursor: pointer; }
.attr-values-wrap { max-height: 180px; overflow-y: auto; padding-right: 4px; }

.btn-filter {
    width: 100%;
    padding: 10px;
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 13.5px;
    font-weight: 700;
    cursor: pointer;
    transition: opacity .2s, transform .15s, box-shadow .2s;
    box-shadow: 0 4px 14px rgba(14,165,233,.35);
}

.btn-filter:hover {
    opacity: .92;
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(14,165,233,.45);
}

.btn-advanced-toggle {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    padding: 10px 12px;
    background: rgba(186,230,253,.4);
    border: 1px dashed rgba(14,165,233,.6);
    border-radius: 10px;
    font-size: 13.5px;
    font-weight: 700;
    color: #0c4a6e;
    cursor: pointer;
    margin-bottom: 16px;
    transition: background .15s, transform .15s;
}

.btn-advanced-toggle:hover {
    background: rgba(125,211,252,.45);
    transform: translateY(-1px);
}

.btn-advanced-toggle .badge-count {
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    border-radius: 10px;
    padding: 1px 7px;
    margin-left: auto;
}

/* ============================================================
   MODAL LỌC NÂNG CAO
   ============================================================ */
.advanced-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(12,74,110,.45);
    backdrop-filter: blur(4px);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.advanced-overlay.open { display: flex; }

.advanced-modal {
    background: rgba(255,255,255,.95);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-radius: 18px;
    border: 1px solid rgba(186,230,253,.7);
    width: 100%;
    max-width: 520px;
    max-height: 80vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    box-shadow: 0 24px 60px rgba(12,74,110,.3);
    animation: modalIn .25s cubic-bezier(.16,1,.3,1);
}

@keyframes modalIn {
    from { opacity: 0; transform: scale(.94) translateY(12px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
}

.advanced-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 22px;
    border-bottom: 1px solid rgba(186,230,253,.6);
    flex-shrink: 0;
    background: linear-gradient(135deg, #e0f2fe, #bae6fd);
}

.advanced-modal-header h3 {
    font-size: 16px;
    font-weight: 800;
    margin: 0;
    color: #0c4a6e;
}

.advanced-modal-close {
    border: none;
    background: rgba(255,255,255,.6);
    width: 30px;
    height: 30px;
    border-radius: 50%;
    font-size: 14px;
    color: #0369a1;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background .15s;
}

.advanced-modal-close:hover { background: rgba(255,255,255,.9); color: #0c4a6e; }

.advanced-modal-body {
    padding: 18px 22px;
    overflow-y: auto;
    flex: 1;
}

.advanced-modal-body .filter-group:last-child { margin-bottom: 0; }

.advanced-modal-footer {
    padding: 16px 22px;
    border-top: 1px solid rgba(186,230,253,.6);
    display: flex;
    gap: 10px;
    flex-shrink: 0;
}

.advanced-modal-footer .btn-filter { margin: 0; }

.btn-advanced-clear {
    flex: 0 0 auto;
    padding: 9px 18px;
    background: rgba(255,255,255,.7);
    color: #0369a1;
    border: 1px solid rgba(125,211,252,.5);
    border-radius: 8px;
    font-size: 13.5px;
    font-weight: 600;
    cursor: pointer;
    transition: background .15s;
}

.btn-advanced-clear:hover { background: rgba(186,230,253,.5); }

/* ============================================================
   SORT BAR
   ============================================================ */
.sort-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
}

.sort-select {
    border: 1px solid rgba(125,211,252,.6);
    padding: 9px 14px;
    border-radius: 10px;
    font-size: 13.5px;
    outline: none;
    background: rgba(255,255,255,.75);
    backdrop-filter: blur(8px);
    color: #0c4a6e;
    font-weight: 600;
    transition: border-color .2s, box-shadow .2s;
    cursor: pointer;
}

.sort-select:focus {
    border-color: #0ea5e9;
    box-shadow: 0 0 0 3px rgba(14,165,233,.15);
}

.view-toggle { display: flex; gap: 4px; }

.view-btn {
    width: 36px;
    height: 36px;
    border: 1px solid rgba(125,211,252,.5);
    border-radius: 10px;
    background: rgba(255,255,255,.7);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #7dd3fc;
    font-size: 14px;
    transition: .15s;
    backdrop-filter: blur(6px);
}

.view-btn.active {
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    color: #fff;
    border-color: transparent;
    box-shadow: 0 4px 10px rgba(14,165,233,.35);
}

/* ============================================================
   PRODUCT GRID
   ============================================================ */
.grid-4 {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
}

@media (max-width: 640px) {
    .grid-4 { grid-template-columns: repeat(2, 1fr); gap: 12px; }
}

/* ============================================================
   PRODUCT CARD — glassmorphism + animations
   ============================================================ */
.product-card {
    display: block;
    border-radius: 14px;
    overflow: hidden;
    text-decoration: none;
    color: inherit;
    transition: transform .22s cubic-bezier(.16,1,.3,1), box-shadow .22s, border-color .22s;
    position: relative;
    background: rgba(255,255,255,.82);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(186,230,253,.6);
    box-shadow: 0 2px 12px rgba(14,165,233,.08);
    cursor: pointer;
}

.product-card:hover {
    transform: translateY(-5px) scale(1.01);
    box-shadow: 0 12px 32px rgba(14,165,233,.2);
    border-color: #7dd3fc;
}

/* Shine sweep on hover */
.product-card::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(105deg,
        transparent 40%,
        rgba(255,255,255,.45) 50%,
        transparent 60%);
    transform: translateX(-120%);
    transition: transform .55s ease;
    pointer-events: none;
    z-index: 3;
}

.product-card:hover::after {
    transform: translateX(120%);
}

.product-card-img {
    height: 170px;
    background: linear-gradient(160deg, #f0f9ff 0%, #e0f2fe 100%);
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
    box-sizing: border-box;
    transition: transform .35s cubic-bezier(.16,1,.3,1);
}

.product-card:hover .product-card-img img {
    transform: scale(1.06);
}

.img-placeholder { color: #7dd3fc; }

.product-card-body {
    padding: 12px 14px 14px;
    border-top: 1px solid rgba(186,230,253,.5);
}

.product-card-name {
    font-size: 13.5px;
    font-weight: 600;
    margin-bottom: 6px;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 37px;
    color: #0f4c75;
}

.product-card-price {
    font-size: 16px;
    font-weight: 800;
    color: #0369a1;
}

.stars { font-size: 12px; color: #f59e0b; margin-top: 6px; }
.review-count { color: #7dd3fc; }

.wish {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: rgba(255,255,255,.85);
    backdrop-filter: blur(6px);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #7dd3fc;
    font-size: 14px;
    z-index: 2;
    box-shadow: 0 1px 6px rgba(14,165,233,.18);
    transition: color .2s, transform .2s;
}

.wish:hover { color: #e53935; transform: scale(1.15); }

.badge-tag {
    position: absolute;
    top: 10px;
    left: 10px;
    background: linear-gradient(135deg, #ef4444, #e53935);
    color: #fff;
    font-size: 10.5px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
    z-index: 2;
    box-shadow: 0 2px 6px rgba(229,57,53,.35);
}

/* ============================================================
   RIPPLE
   ============================================================ */
.ripple-wave {
    position: absolute;
    border-radius: 50%;
    background: rgba(125,211,252,.3);
    transform: scale(0);
    animation: rippleOut .6s linear;
    pointer-events: none;
    z-index: 10;
}

@keyframes rippleOut {
    to { transform: scale(4); opacity: 0; }
}

/* ============================================================
   SCROLL REVEAL
   ============================================================ */
.reveal {
    opacity: 0;
    transform: translateY(28px);
    transition: opacity .55s cubic-bezier(.16,1,.3,1), transform .55s cubic-bezier(.16,1,.3,1);
}

.reveal.revealed {
    opacity: 1;
    transform: translateY(0);
}

.stagger-children > * {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity .45s cubic-bezier(.16,1,.3,1), transform .45s cubic-bezier(.16,1,.3,1);
}

.stagger-children.revealed > *:nth-child(1)  { opacity:1; transform:translateY(0); transition-delay:.04s; }
.stagger-children.revealed > *:nth-child(2)  { opacity:1; transform:translateY(0); transition-delay:.09s; }
.stagger-children.revealed > *:nth-child(3)  { opacity:1; transform:translateY(0); transition-delay:.14s; }
.stagger-children.revealed > *:nth-child(4)  { opacity:1; transform:translateY(0); transition-delay:.19s; }
.stagger-children.revealed > *:nth-child(5)  { opacity:1; transform:translateY(0); transition-delay:.24s; }
.stagger-children.revealed > *:nth-child(6)  { opacity:1; transform:translateY(0); transition-delay:.29s; }
.stagger-children.revealed > *:nth-child(7)  { opacity:1; transform:translateY(0); transition-delay:.34s; }
.stagger-children.revealed > *:nth-child(8)  { opacity:1; transform:translateY(0); transition-delay:.39s; }
.stagger-children.revealed > *:nth-child(n+9){ opacity:1; transform:translateY(0); transition-delay:.43s; }

/* ============================================================
   EMPTY STATE
   ============================================================ */
.empty-state {
    text-align: center;
    padding: 60px 0;
    color: #0369a1;
    background: rgba(255,255,255,.7);
    backdrop-filter: blur(12px);
    border-radius: 16px;
    border: 1px solid rgba(186,230,253,.5);
}

.empty-state i { opacity: .35; color: #7dd3fc; margin-bottom: 16px; display: block; }
.empty-state a { color: #0369a1; font-weight: 600; }

/* ============================================================
   PAGINATION
   ============================================================ */
.pagination-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    margin-top: 36px;
}

.pagination-wrap nav {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
}

.pagination-wrap .pagination {
    display: flex;
    gap: 6px;
    list-style: none;
    margin: 0;
    padding: 0;
}

.pagination-wrap .page-item .page-link {
    min-width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(125,211,252,.5);
    border-radius: 10px;
    background: rgba(255,255,255,.7);
    backdrop-filter: blur(6px);
    color: #0369a1;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: all .15s;
}

.pagination-wrap .page-item .page-link:hover {
    border-color: #0ea5e9;
    background: rgba(186,230,253,.5);
    color: #0c4a6e;
    transform: translateY(-1px);
}

.pagination-wrap .page-item.active .page-link {
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    color: #fff;
    border-color: transparent;
    box-shadow: 0 4px 12px rgba(14,165,233,.4);
}

.pagination-wrap .page-item.disabled .page-link {
    color: #bae6fd;
    pointer-events: none;
    opacity: .6;
}

.pagination-wrap .pagination-info {
    margin: 0;
    font-size: 13px;
    color: #0369a1;
    text-align: center;
    opacity: .8;
}

.pagination-wrap .pagination-info strong {
    color: #0c4a6e;
    font-weight: 700;
}
</style>
@endpush

@section('content')
{{-- Sky Canvas --}}
<canvas id="sky-canvas" aria-hidden="true"></canvas>

<div class="products-page container">

    {{-- Breadcrumb --}}
    <div class="breadcrumb-wrap reveal">
        <a href="{{ route('home') }}">Trang chủ</a>
        <span> › </span>
        <span>Sản phẩm</span>
        @if(request('q'))
            <span> › </span>
            <span>Tìm: "{{ request('q') }}"</span>
        @endif
    </div>

    <div class="page-header-row reveal">
        <h1>
            @if(request('q'))
                Kết quả tìm kiếm: "{{ request('q') }}"
            @else
                Tất cả sản phẩm
            @endif
        </h1>
        <div class="sort-bar" style="margin-bottom:0">
            <form id="sortForm" method="GET" action="{{ route('products.index') }}" style="display:flex;align-items:center;gap:8px">
                @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
                @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                @if(request('brand'))<input type="hidden" name="brand" value="{{ request('brand') }}">@endif

                <select class="sort-select" name="sort" onchange="document.getElementById('sortForm').submit()">
                    <option value="">Sắp xếp: Mặc định</option>
                    <option value="price_asc"  {{ request('sort')=='price_asc'  ? 'selected' : '' }}>Giá: Thấp đến cao</option>
                    <option value="price_desc" {{ request('sort')=='price_desc' ? 'selected' : '' }}>Giá: Cao đến thấp</option>
                    <option value="newest"     {{ request('sort')=='newest'     ? 'selected' : '' }}>Mới nhất</option>
                    <option value="rating"     {{ request('sort')=='rating'     ? 'selected' : '' }}>Đánh giá cao</option>
                </select>
            </form>
            <div class="view-toggle" style="margin-left:10px">
                <button class="view-btn active" id="gridView" title="Lưới"><i class="fas fa-th"></i></button>
                <button class="view-btn" id="listView" title="Danh sách"><i class="fas fa-list"></i></button>
            </div>
        </div>
    </div>

    <div class="results-count reveal">
        Hiển thị {{ $products->firstItem() }}–{{ $products->lastItem() }} trong {{ number_format($totalProducts) }} sản phẩm
    </div>

    <div class="layout-row">

        {{-- ===== SIDEBAR FILTER ===== --}}
        <aside class="filter-sidebar reveal">
            <form action="{{ route('products.index') }}" method="GET" id="filterForm">
                @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
                @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif
                @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                @if(request('brand'))<input type="hidden" name="brand" value="{{ request('brand') }}">@endif

                @php
                    $hasActiveFilters = request('category')
                        || request('brand')
                        || collect(request('price', []))->filter()->isNotEmpty()
                        || request('price_from')
                        || request('price_to')
                        || collect(request('attr', []))->flatten()->filter(fn ($v) => $v !== null && $v !== '')->isNotEmpty();
                @endphp

                <div class="filter-sidebar-header">
                    <h2><i class="fas fa-filter" style="margin-right:6px;opacity:.7"></i>Bộ lọc</h2>
                    @if($hasActiveFilters)
                        <a href="{{ route('products.index', request('q') ? ['q' => request('q')] : []) }}" class="btn-clear-filter">
                            <i class="fas fa-times-circle"></i> Xóa bộ lọc
                        </a>
                    @endif
                </div>

                {{-- Lọc nâng cao --}}
                @if(count($attributesFilter))
                    @php
                        $selectedAttrCount = collect(request('attr', []))
                            ->flatten()
                            ->filter(fn ($v) => $v !== null && $v !== '')
                            ->count();
                    @endphp
                    <button type="button" id="advancedToggle" class="btn-advanced-toggle">
                        <span><i class="fas fa-sliders-h"></i> Lọc nâng cao</span>
                        @if($selectedAttrCount)
                            <span class="badge-count">{{ $selectedAttrCount }}</span>
                        @endif
                    </button>
                @endif

                {{-- Danh mục --}}
                <div class="filter-group">
                    <h3>Danh mục</h3>
                    <ul>
                        <li>
                            <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}"
                               class="{{ !request('category') ? 'active' : '' }}">
                                Tất cả
                            </a>
                        </li>
                        @foreach($categories as $cat)
                        <li>
                            <a href="{{ request()->fullUrlWithQuery(['category' => $cat->id]) }}"
                               class="{{ request('category') == $cat->id ? 'active' : '' }}">
                                {{ $cat->name }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Thương hiệu --}}
                <div class="filter-group">
                    <h3>Thương hiệu</h3>
                    <ul>
                        @foreach($brands as $brand)
                        <li>
                            <a href="{{ request()->fullUrlWithQuery(['brand' => $brand->id]) }}"
                               class="{{ request('brand') == $brand->id ? 'active' : '' }}">
                                {{ $brand->name }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Khoảng giá --}}
                <div class="filter-group">
                    <h3>Khoảng giá</h3>
                    @foreach([
                        ['Dưới 5 triệu',    '0',        '5000000'],
                        ['5 – 10 triệu',    '5000000',  '10000000'],
                        ['10 – 20 triệu',   '10000000', '20000000'],
                        ['20 – 30 triệu',   '20000000', '30000000'],
                        ['Trên 30 triệu',   '30000000', ''],
                    ] as $range)
                    <label class="price-check">
                        <input type="checkbox" name="price[]"
                               value="{{ $range[1] }}_{{ $range[2] }}"
                               {{ in_array($range[1].'_'.$range[2], request('price', [])) ? 'checked' : '' }}>
                        <span>{{ $range[0] }}</span>
                    </label>
                    @endforeach

                    <div class="price-range">
                        <input type="number" name="price_from" placeholder="Từ" value="{{ request('price_from') }}">
                        <span>–</span>
                        <input type="number" name="price_to" placeholder="Đến" value="{{ request('price_to') }}">
                    </div>
                </div>

                <div class="filter-group">
                    <button type="submit" class="btn-filter">
                        <i class="fas fa-search" style="margin-right:6px"></i>Lọc ngay
                    </button>
                </div>
            </form>
        </aside>

        {{-- ===== PRODUCT GRID ===== --}}
        <div>
            @if($products->isEmpty())
                <div class="empty-state reveal">
                    <i class="fas fa-search fa-3x"></i>
                    <p>Không tìm thấy sản phẩm phù hợp.</p>
                    <a href="{{ route('products.index') }}">Xem tất cả sản phẩm</a>
                </div>
            @else
            <div class="grid-4 stagger-children" id="productsGrid">
                @foreach($products as $product)
                <a href="{{ route('products.show', $product->slug) }}" class="product-card">
                    @if($product->discount_percent > 0)
                        <span class="badge-tag">-{{ $product->discount_percent }}%</span>
                    @endif
                    <span class="wish"><i class="far fa-heart"></i></span>
                    <div class="product-card-img">
                        @if($product->first_image)
                            <img src="{{ $product->first_image }}" alt="{{ $product->name }}" loading="lazy">
                        @else
                            <i class="fas fa-image fa-2x img-placeholder"></i>
                        @endif
                    </div>
                    <div class="product-card-body">
                        <div class="product-card-name">{{ $product->name }}</div>
                        <div>
                            <span class="product-card-price">{{ number_format($product->sale_price) }}đ</span>
                            @if($product->discount_percent > 0)
                                <span style="font-size:12px;color:#7dd3fc;text-decoration:line-through;margin-left:4px">
                                    {{ number_format($product->price) }}đ
                                </span>
                            @endif
                        </div>
                        <div class="stars">
                            @for($i=1;$i<=5;$i++)
                                {{ $i <= round($product->avg_rating) ? '★' : '☆' }}
                            @endfor
                            <span class="review-count">({{ $product->reviews_count }})</span>
                        </div>
                    </div>
                </a>
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
            @endif
        </div>
    </div>

    {{-- Modal lọc nâng cao --}}
    @if(count($attributesFilter))
    <div id="advancedOverlay" class="advanced-overlay">
        <div class="advanced-modal">
            <div class="advanced-modal-header">
                <h3><i class="fas fa-sliders-h" style="margin-right:8px"></i>Lọc nâng cao</h3>
                <button type="button" class="advanced-modal-close" id="advancedClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="advanced-modal-body">
                @foreach($attributesFilter as $attr)
                <div class="filter-group">
                    <h3>{{ $attr['name'] }}</h3>
                    <div class="attr-values-wrap">
                        @foreach($attr['values'] as $value)
                        <label class="attr-check">
                            <input type="checkbox" form="filterForm" name="attr[{{ $attr['id'] }}][]" value="{{ $value }}"
                                {{ in_array($value, request('attr.'.$attr['id'], [])) ? 'checked' : '' }}>
                            <span>{{ $value }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            <div class="advanced-modal-footer">
                <button type="button" class="btn-advanced-clear" id="advancedClear">Bỏ chọn</button>
                <button type="submit" form="filterForm" class="btn-filter">
                    <i class="fas fa-check" style="margin-right:6px"></i>Áp dụng
                </button>
            </div>
        </div>
    </div>
    @endif

</div><!-- /.products-page -->
@endsection

@push('scripts')
<script>
/* ============================================================
   GRID / LIST VIEW TOGGLE
   ============================================================ */
document.getElementById('gridView').addEventListener('click', function () {
    this.classList.add('active');
    document.getElementById('listView').classList.remove('active');
    document.getElementById('productsGrid').style.gridTemplateColumns = 'repeat(4, 1fr)';
});
document.getElementById('listView').addEventListener('click', function () {
    this.classList.add('active');
    document.getElementById('gridView').classList.remove('active');
    document.getElementById('productsGrid').style.gridTemplateColumns = '1fr';
});

/* ============================================================
   ADVANCED FILTER MODAL
   ============================================================ */
const advancedToggle  = document.getElementById('advancedToggle');
const advancedOverlay = document.getElementById('advancedOverlay');
const advancedClose   = document.getElementById('advancedClose');
const advancedClear   = document.getElementById('advancedClear');

if (advancedToggle && advancedOverlay) {
    advancedToggle.addEventListener('click', () => advancedOverlay.classList.add('open'));
    advancedClose.addEventListener('click', () => advancedOverlay.classList.remove('open'));
    advancedOverlay.addEventListener('click', e => {
        if (e.target === advancedOverlay) advancedOverlay.classList.remove('open');
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') advancedOverlay.classList.remove('open');
    });
    advancedClear.addEventListener('click', () => {
        advancedOverlay.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
    });
}

/* ============================================================
   ANIMATIONS
   ============================================================ */
(function () {

    /* ---- 1. Canvas clouds ---- */
    const canvas = document.getElementById('sky-canvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let W, H, clouds = [];

        function resize() {
            W = canvas.width  = window.innerWidth;
            H = canvas.height = window.innerHeight;
        }
        window.addEventListener('resize', resize);
        resize();

        function makeCloud() {
            return {
                x:     Math.random() * W * 1.2,
                y:     Math.random() * H * .6,
                r:     50 + Math.random() * 110,
                dx:    .15 + Math.random() * .25,
                alpha: .06 + Math.random() * .11,
            };
        }

        for (let i = 0; i < 8; i++) clouds.push(makeCloud());

        function drawCloud(c) {
            const g = ctx.createRadialGradient(c.x, c.y, 0, c.x, c.y, c.r);
            g.addColorStop(0,  `rgba(255,255,255,${c.alpha})`);
            g.addColorStop(.6, `rgba(186,230,253,${c.alpha * .6})`);
            g.addColorStop(1,  'rgba(186,230,253,0)');
            ctx.beginPath();
            ctx.arc(c.x, c.y, c.r, 0, Math.PI * 2);
            ctx.fillStyle = g;
            ctx.fill();
            [-.5, .5].forEach(o => {
                ctx.beginPath();
                ctx.arc(c.x + c.r * .55 * o, c.y - c.r * .18, c.r * .72, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(255,255,255,${c.alpha * .7})`;
                ctx.fill();
            });
        }

        (function animateClouds() {
            ctx.clearRect(0, 0, W, H);
            clouds.forEach(c => {
                drawCloud(c);
                c.x += c.dx;
                if (c.x - c.r > W * 1.2) { c.x = -c.r * 2; c.y = Math.random() * H * .6; }
            });
            requestAnimationFrame(animateClouds);
        })();
    }

    /* ---- 2. Bubbles ---- */
    function spawnBubble() {
        const el   = document.createElement('div');
        el.className = 'bubble';
        const size = 5 + Math.random() * 16;
        const dur  = 8 + Math.random() * 12;
        el.style.cssText = [
            `width:${size}px`, `height:${size}px`,
            `left:${Math.random() * 100}vw`,
            `bottom:-${size}px`,
            `animation-duration:${dur}s`,
            `animation-delay:${Math.random() * 5}s`,
        ].join(';');
        document.body.appendChild(el);
        setTimeout(() => el.remove(), (dur + 5) * 1000);
    }
    for (let i = 0; i < 10; i++) spawnBubble();
    setInterval(spawnBubble, 3500);

    /* ---- 3. Scroll Reveal ---- */
    const io = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                io.unobserve(entry.target);
            }
        });
    }, { threshold: 0.07, rootMargin: '0px 0px -30px 0px' });

    document.querySelectorAll('.reveal, .stagger-children').forEach(el => io.observe(el));

    /* ---- 4. Ripple on cards ---- */
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', function (e) {
            const rect   = card.getBoundingClientRect();
            const size   = Math.max(rect.width, rect.height) * 1.6;
            const ripple = document.createElement('span');
            ripple.className = 'ripple-wave';
            ripple.style.cssText = [
                `width:${size}px`, `height:${size}px`,
                `left:${e.clientX - rect.left - size / 2}px`,
                `top:${e.clientY - rect.top  - size / 2}px`,
            ].join(';');
            card.appendChild(ripple);
            ripple.addEventListener('animationend', () => ripple.remove());
        });
    });

    /* ---- 5. 3D Tilt ---- */
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('mousemove', function (e) {
            const rect = card.getBoundingClientRect();
            const dx   = (e.clientX - rect.left - rect.width  / 2) / (rect.width  / 2);
            const dy   = (e.clientY - rect.top  - rect.height / 2) / (rect.height / 2);
            card.style.transform = `perspective(600px) rotateX(${-dy * 5}deg) rotateY(${dx * 5}deg) translateY(-5px) scale(1.02)`;
        });
        card.addEventListener('mouseleave', function () {
            card.style.transform = '';
            card.style.transition = 'transform .4s cubic-bezier(.16,1,.3,1), box-shadow .22s, border-color .22s';
            setTimeout(() => card.style.transition = '', 420);
        });
    });

})();
</script>
@endpush
