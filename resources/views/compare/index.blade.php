@extends('layouts.app')
@section('title', 'So sánh sản phẩm - ElectronicShop')
@php $showSearch = true; @endphp

@push('styles')
<style>
/* ============================================================
   PAGE BACKGROUND — Samsung Style (trắng / xám nhạt)
   ============================================================ */
body {
    background: #f4f4f4;
    color: #000000;
}

/* ============================================================
   SCROLL REVEAL
   ============================================================ */
.reveal {
    opacity: 0; transform: translateY(28px);
    transition: opacity .6s cubic-bezier(.16,1,.3,1), transform .6s cubic-bezier(.16,1,.3,1);
}
.reveal.revealed { opacity: 1; transform: translateY(0); }

.stagger-cards > * {
    opacity: 0; transform: translateY(36px) scale(.97);
    transition: opacity .55s cubic-bezier(.16,1,.3,1), transform .55s cubic-bezier(.16,1,.3,1);
}
.stagger-cards.revealed > *:nth-child(1) { opacity:1; transform:none; transition-delay:.05s; }
.stagger-cards.revealed > *:nth-child(2) { opacity:1; transform:none; transition-delay:.15s; }
.stagger-cards.revealed > *:nth-child(3) { opacity:1; transform:none; transition-delay:.25s; }
.stagger-cards.revealed > *:nth-child(4) { opacity:1; transform:none; transition-delay:.35s; }

/* ripple */
.ripple-wave {
    position: absolute; border-radius: 50%;
    background: rgba(0,0,0,.12);
    transform: scale(0); animation: rippleOut .6s linear;
    pointer-events: none; z-index: 10;
}
@keyframes rippleOut { to { transform:scale(4); opacity:0; } }

/* ============================================================
   PAGE WRAPPER
   ============================================================ */
.compare-page {
    min-height: 100vh;
    padding: 36px 0 70px;
}

/* ============================================================
   PAGE TITLE
   ============================================================ */
.compare-title {
    text-align: center;
    font-size: 32px; font-weight: 800;
    color: #000000; margin-bottom: 32px;
    display: flex; align-items: center; justify-content: center; gap: 12px;
}
.compare-title i {
    width: 52px; height: 52px; border-radius: 14px;
    background: #000000;
    color: #fff; font-size: 22px;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 16px rgba(0,0,0,.15);
}

/* ============================================================
   ALERT (giữ màu xanh lá — mang ý nghĩa thành công)
   ============================================================ */
.alert-success {
    display: flex; align-items: center; gap: 10px;
    background: #e6f4ea;
    color: #137333; border: 1px solid #ceead6;
    padding: 14px 18px; border-radius: 12px;
    margin-bottom: 28px; font-weight: 600; font-size: 14px;
    animation: alertIn .4s cubic-bezier(.16,1,.3,1);
}
@keyframes alertIn { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }

/* ============================================================
   EMPTY STATE
   ============================================================ */
.compare-empty {
    background: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 20px; padding: 80px 30px;
    text-align: center;
    box-shadow: 0 6px 24px rgba(0,0,0,.05);
}
.compare-empty-icon {
    font-size: 76px;
    color: #000000;
    display: block; margin-bottom: 20px;
    animation: emptyFloat 3s ease-in-out infinite;
}
@keyframes emptyFloat {
    0%,100% { transform: translateY(0);  }
    50%      { transform: translateY(-12px); }
}
.compare-empty h2 { font-size: 28px; font-weight: 800; color: #000; margin-bottom: 12px; }
.compare-empty p  { color: #666; margin-bottom: 24px; }

/* ============================================================
   PRIMARY BUTTON (shared)
   ============================================================ */
.btn-primary {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    background: #000000;
    color: #fff; padding: 12px 28px; border-radius: 10px;
    text-decoration: none; font-weight: 700; font-size: 14px;
    transition: opacity .2s, transform .18s, box-shadow .2s;
    box-shadow: 0 4px 16px rgba(0,0,0,.15);
}
.btn-primary:hover {
    opacity: .85; transform: translateY(-2px); color: #fff;
    box-shadow: 0 8px 22px rgba(0,0,0,.2);
}

/* ============================================================
   RATING BADGES BELOW TABLE
   ============================================================ */
.rating-badges-below {
    margin-top: 48px;
    padding: 0;
}

.ai-suggest-header {
    display: flex;
    align-items: flex-start;
    gap: 14px;
}

.ai-suggest-icon {
    width: 44px; height: 44px; border-radius: 12px;
    background: #000000;
    color: #fff; font-size: 18px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 4px 14px rgba(0,0,0,.15);
}

.ai-suggest-title {
    font-size: 20px;
    font-weight: 800;
    color: #000;
    margin: 0 0 4px;
}

.ai-suggest-desc {
    font-size: 13px;
    color: #666;
    margin: 0;
}

.rating-badges-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 28px;
    margin-top: 28px;
}

.rating-badge-item {
    background: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 18px rgba(0,0,0,.05);
    transition: transform .28s cubic-bezier(.16,1,.3,1), box-shadow .28s, border-color .25s;
}

.rating-badge-item:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 32px rgba(0,0,0,.12);
    border-color: #d0d0d0;
}

.rating-badge-top {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid #f0f0f0;
}

.rating-circle-small {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    flex-shrink: 0;
}

.rating-circle-small.excellent {
    background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
    color: #fff;
    box-shadow: 0 6px 16px rgba(76, 175, 80, 0.3);
}

.rating-circle-small.good {
    background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
    color: #fff;
    box-shadow: 0 6px 16px rgba(33, 150, 243, 0.3);
}

.rating-circle-small.fair {
    background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);
    color: #fff;
    box-shadow: 0 6px 16px rgba(255, 152, 0, 0.3);
}

.rating-percent-small {
    font-size: 32px;
    line-height: 1;
}

.rating-label-top {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.top-badge-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    width: fit-content;
    padding: 5px 12px;
    border-radius: 20px;
    background: #e8f0fe;
    border: 1px solid #d2e3fc;
}

.top-badge-pill.best {
    background: #e6f4ea;
    border-color: #ceead6;
}

.top-badge-pill i {
    font-size: 12px;
    color: #1a73e8;
}

.top-badge-pill.best i {
    color: #137333;
}

.top-badge-pill span {
    font-size: 12px;
    font-weight: 700;
    color: #1a73e8;
    letter-spacing: 0.2px;
}

.top-badge-pill.best span {
    color: #137333;
}

.rating-badge-desc {
    padding: 0;
}

.desc-title {
    font-size: 14px;
    font-weight: 700;
    color: #000;
    margin: 0 0 14px 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.desc-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.desc-list li {
    font-size: 12px;
    color: #555;
    display: flex;
    align-items: flex-start;
    gap: 8px;
    line-height: 1.4;
}

.desc-list li i {
    font-size: 11px;
    color: #4CAF50;
    margin-top: 2px;
    flex-shrink: 0;
}

/* ============================================================
   BADGE BEST — Đề xuất tốt nhất (viền vàng, glow)
   ============================================================ */
.rating-badge-item.badge-best {
    border: 2px solid #f0c14b;
    box-shadow: 0 4px 18px rgba(240,193,75,.18), 0 0 0 3px rgba(240,193,75,.08);
    position: relative;
    overflow: hidden;
}
.rating-badge-item.badge-best:hover {
    box-shadow: 0 12px 32px rgba(240,193,75,.22), 0 0 0 3px rgba(240,193,75,.12);
}

/* Ribbon */
.ai-badge-ribbon {
    position: absolute;
    top: 14px; right: -30px;
    background: linear-gradient(135deg, #f0c14b 0%, #e6a817 100%);
    color: #000;
    font-size: 11px; font-weight: 800;
    padding: 4px 36px;
    transform: rotate(35deg);
    letter-spacing: .3px;
    box-shadow: 0 2px 8px rgba(240,193,75,.3);
    z-index: 5;
}
.ai-badge-ribbon i {
    font-size: 10px;
    margin-right: 3px;
}

/* Value badge pill */
.top-badge-pill.value {
    background: #e8f5e9;
    border-color: #c8e6c9;
}
.top-badge-pill.value i {
    color: #2e7d32;
}
.top-badge-pill.value span {
    color: #2e7d32;
}

/* ============================================================
   SCORE BARS — thanh bar 4 tiêu chí
   ============================================================ */
.score-bars {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 16px;
    padding: 14px;
    background: #fafafa;
    border-radius: 10px;
    border: 1px solid #f0f0f0;
}

.score-bar-row {
    display: flex;
    align-items: center;
    gap: 8px;
}

.score-bar-label {
    font-size: 11px;
    font-weight: 600;
    color: #666;
    min-width: 74px;
    display: flex;
    align-items: center;
    gap: 5px;
}
.score-bar-label i {
    font-size: 10px;
    color: #999;
    width: 14px;
    text-align: center;
}

.score-bar-track {
    flex: 1;
    height: 7px;
    background: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
}

.score-bar-fill {
    height: 100%;
    border-radius: 4px;
    transition: width .8s cubic-bezier(.16,1,.3,1);
}

.spec-fill    { background: linear-gradient(90deg, #667eea 0%, #764ba2 100%); }
.review-fill  { background: linear-gradient(90deg, #f6d365 0%, #fda085 100%); }
.price-fill   { background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%); }
.pop-fill     { background: linear-gradient(90deg, #fa709a 0%, #fee140 100%); }

.score-bar-value {
    font-size: 11px;
    font-weight: 700;
    color: #333;
    min-width: 24px;
    text-align: right;
}

/* ============================================================
   COMPARE HEADER — product cards
   ============================================================ */
.compare-header {
    display: flex; justify-content: center;
    gap: 28px; flex-wrap: wrap; margin-bottom: 36px;
}

/* ============================================================
   COMPARE CARD
   ============================================================ */
.compare-card {
    width: 300px;
    background: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 18px; padding: 24px;
    box-shadow: 0 4px 18px rgba(0,0,0,.05);
    transition: transform .28s cubic-bezier(.16,1,.3,1),
                box-shadow .28s, border-color .25s;
    position: relative; overflow: hidden;
    text-align: center;
    /* FIX: Use flexbox to align content vertically */
    display: flex;
    flex-direction: column;
}

/* top bar */
.compare-card::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0;
    height: 3px;
    background: #000000;
    border-radius: 18px 18px 0 0;
}

.compare-card:hover {
    transform: translateY(-10px) scale(1.01);
    box-shadow: 0 16px 36px rgba(0,0,0,.1);
    border-color: #cccccc;
}

.compare-image {
    display: flex; justify-content: center; align-items: center;
    height: 200px;
    background: #f4f4f4;
    border-radius: 12px; margin-bottom: 16px;
    overflow: hidden;
}
.compare-image img {
    width: 175px; height: 175px; object-fit: contain;
    transition: transform .35s cubic-bezier(.16,1,.3,1);
}
.compare-card:hover .compare-image img { transform: scale(1.08); }
.compare-image i { color: #bbbbbb; }

.compare-card h3 {
    font-size: 15px; font-weight: 700;
    color: #000000; margin: 0 0 10px;
    min-height: 42px;
    display: -webkit-box; -webkit-line-clamp: 2;
    -webkit-box-orient: vertical; overflow: hidden;
    line-height: 1.45;
}

.price {
    font-size: 24px; font-weight: 800;
    color: #000000; margin-bottom: 4px;
}

/* FIX: Reserve space for old-price even when empty */
.old-price {
    font-size: 13px; color: #999999;
    text-decoration: line-through; margin-bottom: 16px;
    min-height: 18px;  /* ← Ensures consistent spacing */
}

/* FIX: Flex-grow pushes buttons to bottom */
.card-content {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.card-buttons { 
    display: flex; 
    flex-direction: column; 
    gap: 10px;
    margin-top: auto;  /* ← Pushes buttons to bottom */
}

.buy-btn {
    display: flex; justify-content: center; align-items: center; gap: 8px;
    background: #000000;
    color: #fff; border-radius: 10px; padding: 11px;
    text-decoration: none; font-weight: 700; font-size: 14px;
    transition: opacity .2s, transform .15s, box-shadow .2s;
    box-shadow: 0 4px 12px rgba(0,0,0,.15);
    position: relative; overflow: hidden;
}
.buy-btn::after {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,.25) 50%, transparent 60%);
    transform: translateX(-120%); transition: transform .5s ease; pointer-events: none;
}
.buy-btn:hover::after { transform: translateX(120%); }
.buy-btn:hover { opacity:.85; transform: translateY(-1px); color:#fff; box-shadow: 0 6px 16px rgba(0,0,0,.2); }

/* Nút xóa — giữ màu đỏ, mang ý nghĩa cảnh báo/hủy */
.remove-btn {
    width: 100%; border: none;
    background: rgba(239,68,68,.1);
    color: #ef4444;
    border: 1px solid rgba(239,68,68,.25);
    padding: 10px; border-radius: 10px;
    cursor: pointer; font-weight: 700; font-size: 14px;
    transition: background .2s, transform .15s, box-shadow .2s;
    display: flex; align-items: center; justify-content: center; gap: 6px;
}
.remove-btn:hover {
    background: rgba(239,68,68,.15);
    box-shadow: 0 4px 12px rgba(239,68,68,.15);
    transform: translateY(-1px);
}

.compare-add {
    display: flex !important; 
    flex-direction: column !important; 
    align-items: center; 
    justify-content: center;
    gap: 14px;
    background: #fafafa;
    border: 2px dashed #e0e0e0;
}
.compare-add:hover {
    border-color: #bbb;
    background: #f5f5f5;
}
.add-icon {
    font-size: 56px;
    color: #d0d0d0;
    transition: transform .3s, color .3s;
}
.compare-add:hover .add-icon {
    transform: scale(1.15) rotate(10deg);
    color: #aaa;
}
.compare-add h3 { font-size: 16px; margin: 0; min-height: auto; }
.compare-add p  { color: #888; font-size: 13px; margin: 0; }

/* ============================================================
   COMPARE TABLE
   ============================================================ */
.compare-table { width: 100%; overflow-x: auto; }
.compare-table table {
    width: 100%;
    border-collapse: collapse;
    background: #ffffff;
    box-shadow: 0 4px 20px rgba(0,0,0,.06);
    border-radius: 14px;
    overflow: hidden;
}
.compare-table thead {
    background: #000000;
    color: #fff;
    position: sticky; top: 0; z-index: 20;
}
.compare-table th, .compare-table td {
    padding: 16px; text-align: center; border-bottom: 1px solid #f0f0f0;
    font-size: 14px;
}
.compare-table th {
    font-weight: 700;
    letter-spacing: 0.3px;
}
.compare-table tbody tr:hover {
    background: #f9f9f9;
}
.compare-table tbody tr:last-child td {
    border-bottom: none;
}
.attribute-name {
    font-weight: 700; color: #333; text-align: left;
}
.empty-cell { color: #ccc; font-style: italic; }
.diff {
    background: #fff3cd;
    color: #856404;
    font-weight: 600;
    animation: diffPulse 1.2s cubic-bezier(.16,1,.3,1);
}
@keyframes diffPulse {
    0% { background: #ffeaa7; }
    100% { background: #fff3cd; }
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 1024px) {
    .compare-card { width: 280px; }
}
@media (max-width: 768px) {
    .compare-header { gap: 18px; }
    .compare-card { width: 240px; padding: 18px; }
    .compare-image { height: 160px; }
    .compare-image img { width: 140px; height: 140px; }
    .compare-card h3 { font-size: 13px; }
    .price { font-size: 20px; }
}
@media (max-width: 480px) {
    .compare-header { flex-direction: column; align-items: center; }
    .compare-card, .compare-add { width: 100%; max-width: 320px; }
    .compare-table { font-size: 12px; }
    .compare-table th, .compare-table td { padding: 12px 8px; }
}
</style>
@endpush

@section('content')
<section class="compare-page">
    <div class="container">

        {{-- ===== PAGE TITLE ===== --}}
        <div class="compare-title">
            <i class="fas fa-code-compare"></i>
            So sánh sản phẩm
        </div>

        @if($products->isEmpty())

        {{-- ===== EMPTY STATE ===== --}}
        <div class="compare-empty">
            <i class="compare-empty-icon fas fa-cube"></i>
            <h2>Chưa có sản phẩm để so sánh</h2>
            <p>Hãy chọn tối đa 3 sản phẩm để bắt đầu so sánh.</p>
            <a href="{{ route('products.index') }}" class="btn-primary">
                <i class="fas fa-store"></i> Xem sản phẩm
            </a>
        </div>

        @else

        {{-- ===== SUCCESS ALERT ===== --}}
        @if(session('success'))
        <div class="alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
        @endif

        {{-- ===== PRODUCT CARDS ===== --}}
        <div class="compare-header stagger-cards">

            @foreach($products as $product)
            <div class="compare-card">
                <div class="compare-image">
                    @if($product->first_image)
                        <img src="{{ $product->first_image }}" alt="{{ $product->name }}">
                    @else
                        <i class="fas fa-image fa-3x"></i>
                    @endif
                </div>

                {{-- FIX: Wrap price/title in card-content to control flex layout --}}
                <div class="card-content">
                    <h3>{{ $product->name }}</h3>

                    @php
                        $displayPrice = $product->min_price;
                    @endphp

                    <div class="price">
                        {{ number_format($displayPrice, 0, ',', '.') }}đ
                    </div>

                    {{-- FIX: Always render old-price div, even if empty --}}
                    <div class="old-price">
                        @if($product->discount_percent > 0)
                            {{ number_format($product->min_price / (1 - $product->discount_percent / 100), 0, ',', '.') }}đ
                        @endif
                    </div>
                </div>

                <div class="card-buttons">
                    <a href="{{ route('products.show', $product->slug) }}" class="buy-btn">
                        <i class="fas fa-cart-shopping"></i> Mua ngay
                    </a>
                    <form action="{{ route('compare.remove', $product) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="remove-btn" type="submit">
                            <i class="fas fa-trash"></i> Xóa
                        </button>
                    </form>
                </div>
            </div>
            @endforeach

            @if($products->count() < 3)
            <div class="compare-card compare-add">
                <i class="fas fa-plus-circle add-icon"></i>
                <h3>Chọn thêm sản phẩm</h3>
                <p>Bạn có thể so sánh tối đa 3 sản phẩm.</p>
                <a href="{{ route('products.index') }}" class="btn-primary" style="margin-top:10px">
                    <i class="fas fa-store"></i> Chọn sản phẩm
                </a>
            </div>
            @endif

        </div>

        {{-- ===== COMPARISON TABLE ===== --}}
        <div class="compare-table reveal">
            <table>
                <thead>
                    <tr>
                        <th width="200">Thông số</th>
                        @foreach($products as $product)
                            <th>{{ $product->name }}</th>
                        @endforeach
                        @for($i = $products->count(); $i < 3; $i++)
                            <th>Sản phẩm thứ {{ $i + 1 }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="attribute-name">Danh mục</td>
                        @foreach($products as $product)
                            <td>{{ $product->category->name ?? '-' }}</td>
                        @endforeach
                        @for($i = $products->count(); $i < 3; $i++)
                            <td class="empty-cell">—</td>
                        @endfor
                    </tr>
                    <tr>
                        <td class="attribute-name">Thương hiệu</td>
                        @foreach($products as $product)
                            <td>{{ $product->brand->name ?? '-' }}</td>
                        @endforeach
                        @for($i = $products->count(); $i < 3; $i++)
                            <td class="empty-cell">—</td>
                        @endfor
                    </tr>

                    @foreach($attributes as $attribute)
                    <tr>
                        <td class="attribute-name">{{ $attribute->name }}</td>
                        @foreach($products as $product)
                        @php
                            $value = optional(
                                $product->attributes
                                    ->where('attribute_id', $attribute->id)
                                    ->first()
                            )->value;
                        @endphp
                        <td>{{ $value ?? '-' }}</td>
                        @endforeach
                        @for($i = $products->count(); $i < 3; $i++)
                            <td class="empty-cell">—</td>
                        @endfor
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ===== AI GỢI Ý — ĐA TIÊU CHÍ ===== --}}
        @if($products->count() > 0)
        @php
            // So sánh từng thông số giữa các sản phẩm để tìm ra
            // thông số nào sản phẩm đang "vượt trội" (giá trị số lớn nhất, không hòa)
            $highlights = [];
            foreach ($products as $p) {
                $highlights[$p->id] = [];
            }

            foreach ($attributes as $attribute) {
                $numericValues = [];

                foreach ($products as $p) {
                    $rawValue = optional(
                        $p->attributes->where('attribute_id', $attribute->id)->first()
                    )->value;

                    if ($rawValue !== null) {
                        $normalized = preg_replace_callback(
                            '/([\d.,]+)\s*TB/i',
                            fn ($m) => ((float) str_replace(',', '.', $m[1]) * 1024) . 'GB',
                            $rawValue
                        );

                        preg_match_all('/[\d]+(?:[.,]\d+)?/', str_replace(',', '.', $normalized), $matches);

                        if (!empty($matches[0])) {
                            $numbers = array_map(fn ($n) => (float) str_replace(',', '.', $n), $matches[0]);
                            $numericValues[$p->id] = [
                                'raw' => $rawValue,
                                'num' => max($numbers),
                            ];
                        }
                    }
                }

                if (count($numericValues) >= 2) {
                    $maxVal  = max(array_column($numericValues, 'num'));
                    $winners = array_filter($numericValues, fn($v) => $v['num'] == $maxVal);

                    if (count($winners) === 1) {
                        $winnerId = array_key_first($winners);
                        $highlights[$winnerId][] = [
                            'label' => $attribute->name,
                            'value' => $numericValues[$winnerId]['raw'],
                        ];
                    }
                }
            }
        @endphp
        <div class="rating-badges-below reveal">
            <div class="ai-suggest-header">
                <div class="ai-suggest-icon">
                    <i class="fas fa-robot"></i>
                </div>
                <div>
                    <h3 class="ai-suggest-title">AI gợi ý cho bạn</h3>
                    <p class="ai-suggest-desc">Phân tích dựa trên thông số kỹ thuật, đánh giá người dùng, giá cả và độ phổ biến.</p>
                </div>
            </div>
            <div class="rating-badges-grid">
                @foreach($products as $product)
                <div class="rating-badge-item {{ $product->ai_badge === 'best' ? 'badge-best' : '' }}">

                    {{-- Badge phân cấp --}}
                    @if($product->ai_badge === 'best')
                        <div class="ai-badge-ribbon">
                            <i class="fas fa-trophy"></i> {{ $product->ai_badge_label }}
                        </div>
                    @endif

                    <div class="rating-badge-top">
                        <div class="rating-circle-small {{ $product->ai_score >= 75 ? 'excellent' : ($product->ai_score >= 50 ? 'good' : 'fair') }}">
                            <div class="rating-percent-small">{{ intval($product->ai_score) }}%</div>
                        </div>
                        <div class="rating-label-top">
                            @if($product->ai_badge === 'best')
                                <div class="top-badge-pill best">
                                    <i class="fas fa-trophy"></i>
                                    <span>{{ $product->ai_badge_label }}</span>
                                </div>
                            @elseif($product->ai_badge === 'value')
                                <div class="top-badge-pill value">
                                    <i class="fas fa-tags"></i>
                                    <span>{{ $product->ai_badge_label }}</span>
                                </div>
                            @else
                                <div class="top-badge-pill">
                                    <i class="fas fa-check-circle"></i>
                                    <span>{{ $product->ai_badge_label }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="rating-badge-desc">
                        <p class="desc-title">{{ $product->name }}</p>

                        {{-- Thanh bar so sánh 4 tiêu chí --}}
                        <div class="score-bars">
                            <div class="score-bar-row">
                                <span class="score-bar-label"><i class="fas fa-microchip"></i> Thông số</span>
                                <div class="score-bar-track">
                                    <div class="score-bar-fill spec-fill" style="width: {{ $product->ai_spec_score }}%"></div>
                                </div>
                                <span class="score-bar-value">{{ $product->ai_spec_score }}</span>
                            </div>
                            <div class="score-bar-row">
                                <span class="score-bar-label"><i class="fas fa-star"></i> Đánh giá</span>
                                <div class="score-bar-track">
                                    <div class="score-bar-fill review-fill" style="width: {{ $product->ai_review_score }}%"></div>
                                </div>
                                <span class="score-bar-value">{{ $product->ai_review_score }}</span>
                            </div>
                            <div class="score-bar-row">
                                <span class="score-bar-label"><i class="fas fa-tags"></i> Giá cả</span>
                                <div class="score-bar-track">
                                    <div class="score-bar-fill price-fill" style="width: {{ $product->ai_price_score }}%"></div>
                                </div>
                                <span class="score-bar-value">{{ $product->ai_price_score }}</span>
                            </div>
                            <div class="score-bar-row">
                                <span class="score-bar-label"><i class="fas fa-fire"></i> Phổ biến</span>
                                <div class="score-bar-track">
                                    <div class="score-bar-fill pop-fill" style="width: {{ $product->ai_popularity_score }}%"></div>
                                </div>
                                <span class="score-bar-value">{{ $product->ai_popularity_score }}</span>
                            </div>
                        </div>

                        {{-- Lý do gợi ý --}}
                        <ul class="desc-list">
                            @if(!empty($product->ai_reasons))
                                @foreach($product->ai_reasons as $reason)
                                <li><i class="{{ $reason['icon'] }}"></i> {{ $reason['text'] }}</li>
                                @endforeach
                            @endif

                            {{-- Thêm highlight thông số vượt trội (nếu có) --}}
                            @php $productHighlights = array_slice($highlights[$product->id] ?? [], 0, 2); @endphp
                            @foreach($productHighlights as $h)
                            <li><i class="fas fa-circle-check"></i> {{ $h['label'] }}: {{ $h['value'] }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @endif

    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* Highlight các thông số khác nhau */
    document.querySelectorAll('.compare-table tbody tr').forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length <= 2) return;
        const values = [];
        for (let i = 1; i < cells.length; i++) {
            const v = cells[i].innerText.trim();
            if (v !== '—') values.push(v);
        }
        const hasReal = values.length >= 2;
        const unique  = new Set(values);
        if (hasReal && unique.size > 1) {
            for (let i = 1; i < cells.length; i++) {
                cells[i].classList.add('diff');
            }
        }
    });

    /* Đồng bộ chiều cao card */
    const cards = document.querySelectorAll('.compare-card:not(.compare-add)');
    let maxH = 0;
    cards.forEach(c => { if (c.offsetHeight > maxH) maxH = c.offsetHeight; });
    cards.forEach(c => c.style.minHeight = maxH + 'px');
});

(function () {

    /* ---- Scroll Reveal ---- */
    const io = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) { e.target.classList.add('revealed'); io.unobserve(e.target); }
        });
    }, { threshold: 0.07, rootMargin: '0px 0px -30px 0px' });
    document.querySelectorAll('.reveal, .stagger-cards').forEach(el => io.observe(el));

    /* ---- 3D Tilt on product cards ---- */
    document.querySelectorAll('.compare-card:not(.compare-add)').forEach(card => {
        card.addEventListener('mousemove', function (e) {
            const r = card.getBoundingClientRect();
            const dx = (e.clientX-r.left-r.width/2)/(r.width/2);
            const dy = (e.clientY-r.top-r.height/2)/(r.height/2);
            card.style.transform = `perspective(600px) rotateX(${-dy*5}deg) rotateY(${dx*5}deg) translateY(-10px) scale(1.01)`;
        });
        card.addEventListener('mouseleave', function () {
            card.style.transform = '';
            card.style.transition = 'transform .4s cubic-bezier(.16,1,.3,1), box-shadow .28s, border-color .25s';
            setTimeout(() => card.style.transition = '', 420);
        });
    });

    /* ---- Ripple on buy buttons ---- */
    document.querySelectorAll('.buy-btn, .btn-primary').forEach(btn => {
        btn.addEventListener('click', function (e) {
            const r    = btn.getBoundingClientRect();
            const size = Math.max(r.width, r.height)*1.8;
            const rip  = document.createElement('span');
            rip.className = 'ripple-wave';
            rip.style.cssText = [`width:${size}px`,`height:${size}px`,
                `left:${e.clientX-r.left-size/2}px`,
                `top:${e.clientY-r.top-size/2}px`].join(';');
            btn.appendChild(rip);
            rip.addEventListener('animationend', () => rip.remove());
        });
    });

    /* ---- Sticky thead on scroll ---- */
    const table = document.querySelector('.compare-table table');
    if (table) {
        const thead = table.querySelector('thead');
        window.addEventListener('scroll', () => {
            const r = table.getBoundingClientRect();
            if (r.top < 70 && r.bottom > 120) {
                thead.style.boxShadow = '0 4px 20px rgba(0,0,0,.2)';
            } else {
                thead.style.boxShadow = '';
            }
        }, { passive: true });
    }

    /* ---- Diff badge pulse ---- */
    document.querySelectorAll('.diff').forEach((cell, i) => {
        cell.style.animationDelay = (i * .05) + 's';
    });

    /* ---- Score bar animation on scroll into view ---- */
    const scoreBars = document.querySelectorAll('.score-bar-fill');
    scoreBars.forEach(bar => {
        const targetWidth = bar.style.width;
        bar.style.width = '0%';
        const barObserver = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    setTimeout(() => { bar.style.width = targetWidth; }, 150);
                    barObserver.unobserve(e.target);
                }
            });
        }, { threshold: 0.2 });
        barObserver.observe(bar);
    });

})();
</script>
@endpush