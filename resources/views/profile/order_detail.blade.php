@extends('layouts.app')
@section('title', 'Chi tiết đơn hàng #' . $order->id . ' - ElectronicShop')

@push('styles')
<style>
/* ============================================================
   PAGE BACKGROUND — Samsung Minimalist (White & Light Gray)
   ============================================================ */
body {
    background: #f4f4f4;
    background-attachment: fixed;
    color: #111111;
}
#sky-canvas {
    position: fixed; inset: 0; width: 100%; height: 100%;
    pointer-events: none; z-index: 0; opacity: .6;
}
.bubble {
    position: fixed; border-radius: 50%;
    background: radial-gradient(circle at 35% 35%, rgba(255,255,255,1), rgba(0,0,0,0.03));
    border: 1px solid rgba(0,0,0,0.04);
    pointer-events: none; z-index: 0;
    animation: bubbleRise linear infinite;
}
@keyframes bubbleRise {
    0%   { transform: translateY(0) scale(1);    opacity: .7; }
    80%  { opacity: .3; }
    100% { transform: translateY(-110vh) scale(1.1); opacity: 0; }
}

/* ============================================================
   SCROLL REVEAL
   ============================================================ */
.reveal {
    opacity: 0; transform: translateY(26px);
    transition: opacity .6s cubic-bezier(.16,1,.3,1), transform .6s cubic-bezier(.16,1,.3,1);
}
.reveal.revealed { opacity: 1; transform: translateY(0); }

.stagger-section > * {
    opacity: 0; transform: translateY(20px);
    transition: opacity .52s cubic-bezier(.16,1,.3,1), transform .52s cubic-bezier(.16,1,.3,1);
}
.stagger-section.revealed > *:nth-child(1)  { opacity:1; transform:none; transition-delay:.05s; }
.stagger-section.revealed > *:nth-child(2)  { opacity:1; transform:none; transition-delay:.12s; }
.stagger-section.revealed > *:nth-child(3)  { opacity:1; transform:none; transition-delay:.19s; }
.stagger-section.revealed > *:nth-child(4)  { opacity:1; transform:none; transition-delay:.26s; }
.stagger-section.revealed > *:nth-child(n+5){ opacity:1; transform:none; transition-delay:.32s; }

/* ripple */
.ripple-wave {
    position: absolute; border-radius: 50%;
    background: rgba(0,0,0,0.08);
    transform: scale(0); animation: rippleOut .6s linear;
    pointer-events: none; z-index: 10;
}
@keyframes rippleOut { to { transform:scale(4); opacity:0; } }

/* ============================================================
   PAGE WRAPPER
   ============================================================ */
.ordershow-page {
    min-height: 100vh;
    padding: 32px 0 60px;
    position: relative; z-index: 1;
}
.ordershow-container {
    max-width: 1200px; margin: 0 auto; padding: 0 16px;
    display: grid; grid-template-columns: 260px 1fr;
    gap: 24px; align-items: start;
    position: relative; z-index: 1;
}
@media (max-width: 991px) { .ordershow-container { grid-template-columns: 1fr; } }
.profile-sidebar-wrap { position: sticky; top: 88px; }

/* ============================================================
   ALERTS
   ============================================================ */
.alert-sky {
    display: flex; align-items: center; gap: 8px;
    backdrop-filter: blur(8px);
    padding: 12px 18px; border-radius: 12px;
    margin-bottom: 14px; font-weight: 600; font-size: 14px;
    animation: alertIn .4s cubic-bezier(.16,1,.3,1);
}
@keyframes alertIn { from{opacity:0;transform:translateY(-10px)} to{opacity:1;transform:none} }
.alert-sky-success { background: rgba(240,253,244,.9); color: #166534; border: 1px solid rgba(187,247,208,.8); }
.alert-sky-error   { background: rgba(254,226,226,.92); color: #991b1b; border: 1px solid rgba(252,165,165,.6); }
.alert-sky-info    { background: rgba(255,255,255,.9); color: #111111; border: 1px solid #e5e5e5; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }

/* ============================================================
   TOP ROW
   ============================================================ */
.order-toprow {
    display: flex; justify-content: space-between; align-items: center;
    gap: 14px; flex-wrap: wrap; margin-bottom: 22px;
}
.btn-back {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 9px 18px; border-radius: 20px;
    background: #ffffff;
    border: 1px solid #e5e5e5;
    color: #111111; font-weight: 700; font-size: 13.5px;
    text-decoration: none;
    transition: background .2s, transform .18s, box-shadow .2s;
}
.btn-back:hover {
    background: #f8f9fa; color: #000000;
    transform: translateY(-1px); box-shadow: 0 4px 14px rgba(0,0,0,.04);
}
.order-id-block { text-align: right; }
.order-id-block h5 { font-size: 16px; font-weight: 800; color: #000000; margin-bottom: 6px; }

/* Status badges */
.status-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 14px; border-radius: 20px;
    font-size: 12.5px; font-weight: 700; letter-spacing: .3px;
}
.badge-warning   { background:#fffbea; color:#b45309; border:1px solid #fde68a; }
.badge-info      { background:#f0f9ff; color:#0381fe; border:1px solid #bae6fd; }
.badge-primary   { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }
.badge-success   { background:#f0fdf4; color:#166534; border:1px solid #bbf7d0; }
.badge-danger    { background:#fef2f2; color:#b91c1c; border:1px solid #fecaca; }
.badge-secondary { background:#f8f9fa; color:#475569; border:1px solid #e2e8f0;}

/* ============================================================
   SECTION CARD — Clean Tech
   ============================================================ */
.section-card {
    background: #ffffff;
    border: 1px solid #e5e5e5;
    border-radius: 16px; overflow: hidden;
    box-shadow: 0 4px 16px rgba(0,0,0,.02);
    margin-bottom: 20px;
    transition: box-shadow .25s, border-color .25s;
}
.section-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,.04); border-color: #d1d5db; }

.section-card-header {
    background: #f8f9fa;
    padding: 14px 22px;
    border-bottom: 1px solid #e5e5e5;
    display: flex; align-items: center; gap: 8px;
    font-size: 14px; font-weight: 800; color: #000000;
}
.section-card-header .hicon {
    width: 28px; height: 28px; border-radius: 8px;
    background: #000000;
    color: #fff; font-size: 12px;
    display: flex; align-items: center; justify-content: center;
}
.section-card-body { padding: 20px 22px; }

/* ============================================================
   TIMELINE
   ============================================================ */
.timeline {
    display: flex; align-items: center;
    justify-content: space-between; overflow-x: auto;
    padding: 10px 0;
}

.timeline-item { text-align: center; min-width: 90px; }

.timeline-icon {
    width: 50px; height: 50px; border-radius: 50%;
    background: #ffffff;
    border: 2px solid #e5e5e5;
    color: #9ca3af; font-size: 18px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto; position: relative;
    transition: all .35s cubic-bezier(.34,1.56,.64,1);
}
.timeline-item.active .timeline-icon {
    background: #000000;
    border-color: #000000;
    color: #fff;
    box-shadow: 0 4px 12px rgba(0,0,0,.15);
    transform: scale(1.1);
}
.timeline-item.current .timeline-icon::after {
    content: ''; position: absolute;
    inset: -6px; border-radius: 50%;
    border: 2px solid rgba(0,0,0,.15);
    animation: pulseRing 1.6s ease-out infinite;
}
@keyframes pulseRing { 0%{transform:scale(1);opacity:.7} 100%{transform:scale(1.4);opacity:0} }

.timeline-line {
    flex: 1; height: 3px; min-width: 20px;
    background: #e5e5e5;
    border-radius: 2px; margin: 0 8px;
    transition: background .4s ease;
    position: relative; overflow: hidden;
}
.timeline-line.active {
    background: #000000;
}
.timeline-line.active::after {
    content: ''; position: absolute;
    top: 0; left: -40%; width: 40%; height: 100%;
    background: rgba(255,255,255,.3);
    animation: lineShimmer 2s linear infinite;
}
@keyframes lineShimmer { to { left: 140%; } }

.timeline-text {
    margin-top: 12px; font-size: 12.5px; font-weight: 700;
    color: #9ca3af;
}
.timeline-item.active .timeline-text { color: #000000; }

/* ============================================================
   ADDRESS SECTION
   ============================================================ */
.addr-row {
    display: flex; gap: 16px; align-items: flex-start;
}
.addr-pin {
    width: 44px; height: 44px; flex-shrink: 0;
    border-radius: 12px;
    background: #f4f4f4;
    color: #000000; font-size: 18px;
    display: flex; align-items: center; justify-content: center;
    margin-top: 2px; border: 1px solid #e5e5e5;
}
.addr-name { font-size: 16px; font-weight: 800; color: #000000; margin-bottom: 4px; }
.addr-phone { font-size: 13.5px; color: #555555; margin-bottom: 6px; }
.addr-full  { font-size: 14px; color: #555555; line-height: 1.5; }

/* ============================================================
   PRODUCT ROWS
   ============================================================ */
.product-row {
    display: flex; align-items: center;
    gap: 16px; padding: 16px 0;
    border-bottom: 1px solid #f3f4f6;
    transition: background .2s;
}
.product-row:last-of-type { border-bottom: none; }
.product-row:hover { background: #f8f9fa; border-radius: 10px; padding-left: 8px; padding-right: 8px; }

.product-img {
    width: 82px; height: 82px; flex-shrink: 0;
    object-fit: contain; border-radius: 10px;
    border: 1px solid #e5e5e5;
    background: #ffffff;
    padding: 4px; box-sizing: border-box;
    transition: transform .3s cubic-bezier(.16,1,.3,1);
}
.product-row:hover .product-img { transform: scale(1.04); }

.product-name {
    font-size: 14.5px; font-weight: 700; color: #000000;
    text-decoration: none; display: block; margin-bottom: 3px;
    transition: color .15s;
}
.product-name:hover { color: #0381fe; }
.product-qty { font-size: 13px; color: #555555; }

.product-unit  { font-size: 14px; color: #555555; font-weight: 500; text-align: right; white-space: nowrap; }
.product-total { font-size: 16px; font-weight: 800; color: #000000; text-align: right; white-space: nowrap; }

/* ============================================================
   SUMMARY TABLE
   ============================================================ */
.summary-section {
    display: grid; grid-template-columns: 1fr 1fr; gap: 20px;
    margin-top: 20px; padding-top: 18px;
    border-top: 1px solid #e5e5e5;
}
@media (max-width: 640px) { .summary-section { grid-template-columns: 1fr; } }

.summary-table { width: 100%; border-collapse: collapse; font-size: 14px; }
.summary-table td { padding: 8px 0; color: #555555; }
.summary-table td:last-child { text-align: right; font-weight: 600; color: #000000; }
.summary-table .total-row td { border-top: 2px solid #e5e5e5; padding-top: 12px; }
.summary-table .total-row td { font-size: 17px; font-weight: 800; }
.summary-table .total-row td:first-child { color: #000000; }
.summary-table .total-row td:last-child  { color: #000000; }
.discount-val { color: #16a34a !important; }

/* payment info box */
.payment-box {
    background: #f8f9fa;
    border: 1px solid #e5e5e5;
    border-radius: 12px; padding: 18px;
}
.payment-box h6 { font-size: 14px; font-weight: 800; color: #000000; margin-bottom: 12px; }
.payment-box p  { font-size: 13.5px; color: #555555; margin-bottom: 8px; }
.payment-box p:last-child { margin-bottom: 0; }
.payment-box strong { color: #000000; }

.pay-badge-paid     { background:#f0fdf4; color:#166534; border:1px solid #bbf7d0; padding:3px 10px; border-radius:10px; font-size:12px; font-weight:700; }
.pay-badge-refunded { background:#f8f9fa; color:#475569; border:1px solid #e2e8f0; padding:3px 10px; border-radius:10px; font-size:12px; font-weight:700; }
.pay-badge-pending  { background:#fffbea; color:#b45309; border:1px solid #fde68a; padding:3px 10px; border-radius:10px; font-size:12px; font-weight:700; }

/* ============================================================
   ACTION BUTTONS
   ============================================================ */
.actions-row {
    display: flex; justify-content: flex-end; gap: 10px;
    flex-wrap: wrap; margin-top: 8px;
}
.btn-action {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 11px 24px; border-radius: 24px; /* Samsung pill shape */
    font-size: 14px; font-weight: 700; cursor: pointer;
    transition: all .2s;
    border: none; font-family: inherit; text-decoration: none;
    position: relative; overflow: hidden;
}

.btn-cancel   { background:#ffffff; color:#ef4444; border:1px solid #fecaca; }
.btn-cancel:hover   { background:#fef2f2; }
.btn-received { background:#ffffff; color:#16a34a; border:1px solid #bbf7d0; }
.btn-received:hover { background:#f0fdf4; }
.btn-reorder  { background:#000000; color:#ffffff; }
.btn-reorder:hover  { background:#333333; box-shadow:0 4px 14px rgba(0,0,0,.15); }
.btn-review   { background:#0381fe; color:#ffffff; }
.btn-review:hover   { background:#026eda; box-shadow:0 4px 14px rgba(3,129,254,.25); color:#ffffff; }

/* ============================================================
   REVIEW MODAL OVERLAY STYLES (Samsung Light/Clean Style)
   ============================================================ */
.review-modal {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 99999;
    background: rgba(0, 0, 0, 0.4);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    align-items: center;
    justify-content: center;
    padding: 16px;
}
.review-modal.show {
    display: flex !important;
}

.review-modal-content {
    background: #ffffff;
    color: #111111;
    width: 650px;
    max-width: 100%;
    max-height: 90vh;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    animation: modalPopIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes modalPopIn {
    from { opacity: 0; transform: scale(0.95) translateY(10px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
}

.review-header {
    padding: 20px 26px 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e5e5e5;
    background: #ffffff;
}
.review-header h4 {
    margin: 0;
    font-size: 19px;
    font-weight: 800;
    color: #000000;
    display: flex;
    align-items: center;
    gap: 10px;
}
.review-header h4 i {
    color: #0381fe;
}
#closeReviewModal {
    background: #f4f4f4;
    border: none;
    color: #555555;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    font-size: 20px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    transition: all 0.2s ease;
}
#closeReviewModal:hover {
    background: #e5e5e5;
    color: #000000;
    transform: rotate(90deg);
}

.review-modal-content form {
    overflow-y: auto;
    padding: 22px 26px;
    display: flex;
    flex-direction: column;
    gap: 20px;
}
.review-modal-content form::-webkit-scrollbar {
    width: 6px;
}
.review-modal-content form::-webkit-scrollbar-track {
    background: #f4f4f4;
}
.review-modal-content form::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 3px;
}

/* Individual product review block container */
.review-item-card {
    background: #ffffff;
    border: 1px solid #e5e5e5;
    border-radius: 16px;
    padding: 18px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    transition: border-color 0.2s;
}
.review-item-card:hover {
    border-color: #000000;
}

.review-product {
    display: flex;
    align-items: center;
    gap: 16px;
}
.review-image img {
    width: 68px;
    height: 68px;
    object-fit: contain;
    border-radius: 10px;
    border: 1px solid #e5e5e5;
    background: #ffffff;
}
.review-info h5 {
    font-size: 15px;
    font-weight: 700;
    color: #000000;
    margin: 0 0 6px;
    line-height: 1.3;
}
.review-info small {
    display: inline-block;
    background: #f8f9fa;
    color: #555555;
    font-size: 12px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 12px;
    border: 1px solid #e5e5e5;
}

/* Rating label & stars layout */
.review-rating-block {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f8f9fa;
    padding: 12px 16px;
    border-radius: 12px;
    border: 1px solid #e5e5e5;
}
.review-rating-title {
    font-size: 14px;
    font-weight: 700;
    color: #000000;
}

/* Star rating */
.rating-stars {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
    gap: 8px;
}
.rating-stars input {
    display: none;
}
.rating-stars label {
    font-size: 28px;
    color: #d1d5db;
    cursor: pointer;
    transition: color 0.18s, transform 0.18s;
    user-select: none;
    line-height: 1;
}
.rating-stars input:checked ~ label,
.rating-stars label:hover,
.rating-stars label:hover ~ label {
    color: #0381fe;
}
.rating-stars label:hover {
    transform: scale(1.15);
}

.review-modal-content textarea {
    background: #ffffff;
    border: 1px solid #e5e5e5;
    border-radius: 12px;
    color: #111111;
    padding: 14px;
    font-size: 14px;
    outline: none;
    resize: vertical;
    width: 100%;
    box-sizing: border-box;
    font-family: inherit;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.review-modal-content textarea::placeholder {
    color: #9ca3af;
}
.review-modal-content textarea:focus {
    border-color: #000000;
    box-shadow: 0 0 0 2px rgba(0,0,0,0.05);
}

.review-footer {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 12px;
    padding-top: 14px;
    border-top: 1px solid #e5e5e5;
}
#cancelReview {
    background: #ffffff;
    border: 1px solid #e5e5e5;
    color: #111111;
    font-weight: 700;
    font-size: 14px;
    padding: 11px 24px;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.18s;
}
#cancelReview:hover {
    background: #f4f4f4;
}

.review-footer .btn-primary {
    background: #000000;
    border: none;
    color: #ffffff;
    font-weight: 700;
    font-size: 14.5px;
    padding: 11px 32px;
    border-radius: 20px;
    cursor: pointer;
    transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.review-footer .btn-primary:hover {
    background: #333333;
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(0,0,0,0.15);
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 640px) {
    .product-row { flex-wrap: wrap; }
    .product-unit, .product-total { text-align: left; }
    .actions-row { justify-content: stretch; }
    .btn-action  { flex: 1; justify-content: center; }
}
</style>
@endpush

@section('content')
<canvas id="sky-canvas" aria-hidden="true"></canvas>

<div class="ordershow-page">
<div class="ordershow-container">

    {{-- ===== SIDEBAR ===== --}}
    <div class="profile-sidebar-wrap reveal">
        @include('profile.sidebar')
    </div>

    {{-- ===== CONTENT ===== --}}
    <div class="stagger-section">

        {{-- Alerts --}}
        @if(session('success'))
        <div class="alert-sky alert-sky-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="alert-sky alert-sky-error">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
        @endif

        {{-- Top row --}}
        <div class="order-toprow">
            <a href="{{ route('profile.order') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Trở lại
            </a>
            <div class="order-id-block">
                <h5>Mã đơn hàng #{{ $order->id }}</h5>
                @php
                    $statusLabels = [
                        'pending'    => ['Chờ xác nhận', 'warning',   'fa-clock'],
                        'confirmed'  => ['Đã xác nhận',  'info',      'fa-check'],
                        'processing' => ['Đang xử lý',   'info',      'fa-gear'],
                        'shipped'    => ['Đang giao',    'primary',   'fa-truck-fast'],
                        'delivered'  => ['Hoàn thành',   'success',   'fa-circle-check'],
                        'cancelled'  => ['Đã hủy',       'danger',    'fa-ban'],
                        'returned'   => ['Đã hoàn trả',  'secondary', 'fa-rotate-left'],
                    ];
                    [$label, $badge, $icon] = $statusLabels[$order->status] ?? [$order->status, 'secondary', 'fa-circle'];
                @endphp
                <span class="status-badge badge-{{ $badge }}">
                    <i class="fas {{ $icon }}"></i> {{ strtoupper($label) }}
                </span>
            </div>
        </div>

        {{-- ─── TIMELINE ─── --}}
        @if(!in_array($order->status, ['cancelled','returned']))
        @php
            $steps = [
                ['pending',   'receipt',         'Đặt hàng'],
                ['confirmed', 'clipboard-check', 'Xác nhận'],
                ['shipped',   'truck',           'Đang giao'],
                ['delivered', 'box-open',        'Hoàn thành'],
            ];
            $order_rank = ['pending'=>0,'confirmed'=>1,'processing'=>1,'shipped'=>2,'delivered'=>3];
            $current = $order_rank[$order->status] ?? 0;
        @endphp
        <div class="section-card">
            <div class="section-card-header">
                <span class="hicon"><i class="fas fa-route"></i></span>
                Trạng thái đơn hàng
            </div>
            <div class="section-card-body">
                <div class="timeline">
                    @foreach($steps as $idx => [$key, $ic, $txt])
                    @php $rank = $idx; $isActive = $current >= $rank; $isCurrent = $current === $rank; @endphp
                    @if($idx > 0)
                    <div class="timeline-line {{ $current >= $rank ? 'active' : '' }}"></div>
                    @endif
                    <div class="timeline-item {{ $isActive ? 'active' : '' }} {{ $isCurrent ? 'current' : '' }}">
                        <div class="timeline-icon">
                            <i class="fas fa-{{ $ic }}"></i>
                        </div>
                        <div class="timeline-text">{{ $txt }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @else
        <div class="alert-sky alert-sky-info">
            <i class="fas fa-info-circle"></i>
            Đơn hàng này đã {{ $order->status === 'cancelled' ? 'bị hủy' : 'được hoàn trả' }}.
        </div>
        @endif

        {{-- ─── ADDRESS ─── --}}
        <div class="section-card">
            <div class="section-card-header">
                <span class="hicon"><i class="fas fa-map-marker-alt"></i></span>
                Địa chỉ nhận hàng
            </div>
            <div class="section-card-body">
                @if($order->address)
                <div class="addr-row">
                    <div class="addr-pin"><i class="fas fa-map-marker-alt"></i></div>
                    <div>
                        <div class="addr-name">
                            {{ $order->address->full_name }}
                            <span style="font-weight:400;color:#555;font-size:14px;margin-left:12px">{{ $order->address->phone }}</span>
                        </div>
                        <div class="addr-full">
                            <i class="fas fa-location-dot" style="font-size:12px;margin-right:5px;color:#9ca3af"></i>
                            {{ $order->address->full_address }}
                        </div>
                    </div>
                </div>
                @else
                <p style="color:#555;margin:0">Không có thông tin địa chỉ.</p>
                @endif
            </div>
        </div>

        {{-- ─── PRODUCTS ─── --}}
        <div class="section-card">
            <div class="section-card-header">
                <span class="hicon"><i class="fas fa-box"></i></span>
                Sản phẩm đã đặt
            </div>
            <div class="section-card-body">

                @foreach($order->items as $item)
                <div class="product-row">
                    {{-- Image --}}
                    <div style="flex-shrink:0">
                        @if($item->product && $item->product->first_image)
                        <img src="{{ $item->product->first_image }}" class="product-img" alt="{{ $item->product_name }}">
                        @else
                        <img src="https://placehold.co/82x82/f4f4f4/555555?text=SP" class="product-img" alt="{{ $item->product_name }}">
                        @endif
                    </div>
                    {{-- Info --}}
                    <div style="flex:1; min-width:0">
                        @if($item->product)
                        <a href="{{ route('products.show', $item->product->slug) }}" class="product-name">
                            {{ $item->product_name }}
                        </a>
                        @else
                        <span class="product-name" style="cursor:default;color:#94a3b8">{{ $item->product_name }}</span>
                        @endif
                        <div class="product-qty">
                            <i class="fas fa-times" style="font-size:10px;margin-right:2px"></i>{{ $item->quantity }}
                        </div>
                    </div>
                    {{-- Unit price --}}
                    <div class="product-unit">{{ number_format($item->unit_price) }}đ</div>
                    {{-- Total --}}
                    <div class="product-total">{{ number_format($item->total_price) }}đ</div>
                </div>
                @endforeach

                {{-- Summary + Payment --}}
                <div class="summary-section">
                    {{-- Price summary --}}
                    <table class="summary-table">
                        <tr>
                            <td>Tạm tính</td>
                            <td>{{ number_format($order->subtotal) }}đ</td>
                        </tr>
                        <tr>
                            <td>Giảm giá</td>
                            <td class="discount-val">−{{ number_format($order->discount_amount) }}đ</td>
                        </tr>
                        <tr class="total-row">
                            <td>Tổng thanh toán</td>
                            <td>{{ number_format($order->total) }}đ</td>
                        </tr>
                    </table>

                    {{-- Payment info --}}
                    <div class="payment-box">
                        <h6><i class="fas fa-credit-card" style="margin-right:6px;color:#000000"></i>Thông tin thanh toán</h6>
                        <p>
                            <strong>Phương thức:</strong>
                            {{ $order->payment_method === 'momo' ? 'Ví MoMo' : 'Thanh toán khi nhận hàng (COD)' }}
                        </p>
                        <p>
                            <strong>Trạng thái:</strong>
                            @if($order->payment_status === 'paid')
                                <span class="pay-badge-paid"><i class="fas fa-check-circle"></i> Đã thanh toán</span>
                            @elseif($order->payment_status === 'refunded')
                                <span class="pay-badge-refunded"><i class="fas fa-rotate-left"></i> Đã hoàn tiền</span>
                            @else
                                <span class="pay-badge-pending"><i class="fas fa-clock"></i> Chưa thanh toán</span>
                            @endif
                        </p>
                        @if($order->voucher)
                        <p>
                            <strong>Mã giảm giá:</strong>
                            <span style="font-family:monospace;background:#e5e5e5;padding:2px 8px;border-radius:6px;color:#000;font-weight:700">{{ $order->voucher->code }}</span>
                        </p>
                        @endif
                        @if($order->note)
                        <p><strong>Ghi chú:</strong> {{ $order->note }}</p>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        {{-- ─── ACTIONS ─── --}}
        <div class="actions-row">
            @if(in_array($order->status, ['pending','confirmed','processing']))
            <form action="{{ route('profile.order.cancel', $order) }}" method="POST" style="display:inline">
                @csrf @method('PATCH')
                <button type="submit" class="btn-action btn-cancel"
                        onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">
                    <i class="fas fa-ban"></i> Hủy đơn
                </button>
            </form>
            @endif

            @if($order->status === 'shipped')
            <form action="{{ route('profile.order.received', $order) }}" method="POST" style="display:inline">
                @csrf @method('PATCH')
                <button type="submit" class="btn-action btn-received">
                    <i class="fas fa-circle-check"></i> Đã nhận hàng
                </button>
            </form>
            @endif

            @if(in_array($order->status, ['delivered','cancelled','returned']))
            <form action="{{ route('profile.order.reorder', $order) }}" method="POST" style="display:inline">
                @csrf
                <button type="submit" class="btn-action btn-reorder">
                    <i class="fas fa-rotate-right"></i> Mua lại
                </button>
            </form>
            @endif

            @if($order->status === 'delivered')
            <button type="button"
                    class="btn-action btn-review"
                    id="openReviewModal">
                <i class="fas fa-star"></i> Đánh giá
            </button>
            @endif
        </div>

    </div>{{-- /.stagger-section --}}
</div>{{-- /.ordershow-container --}}
</div>{{-- /.ordershow-page --}}

{{-- ─── FLOATING REVIEW MODAL OVERLAY ─── --}}
<div class="review-modal" id="reviewModal">
    <div class="review-modal-content">

        <div class="review-header">
            <h4><i class="fas fa-star"></i> Đánh giá sản phẩm</h4>
            <button type="button" id="closeReviewModal">&times;</button>
        </div>

        <form action="{{ route('profile.review.store', $order) }}"
              method="POST"
              enctype="multipart/form-data">
            @csrf

            @foreach($order->items as $item)

            <div class="review-item-card">

                <div class="review-product">
                    <div class="review-image">
                        @if($item->product && $item->product->first_image)
                            <img src="{{ $item->product->first_image }}" alt="{{ $item->product_name }}">
                        @else
                            <img src="https://placehold.co/68x68/f4f4f4/555555?text=SP" alt="{{ $item->product_name }}">
                        @endif
                    </div>

                    <div class="review-info">
                        <h5>{{ $item->product_name }}</h5>
                        <small>{{ $item->product->category->name ?? 'Phân loại: Mặc định' }}</small>
                    </div>
                </div>

                <input type="hidden"
                       name="reviews[{{$loop->index}}][product_id]"
                       value="{{ $item->product_id }}">

                <div class="review-rating-block">
                    <span class="review-rating-title">Chất lượng sản phẩm</span>
                    <div class="rating-stars">
                        @for($i=5;$i>=1;$i--)
                        <input type="radio"
                               id="star{{$i}}{{$loop->index}}"
                               name="reviews[{{$loop->index}}][rating]"
                               value="{{$i}}"
                               {{ $i === 5 ? 'checked' : '' }}>
                        <label for="star{{$i}}{{$loop->index}}">★</label>
                        @endfor
                    </div>
                </div>

                <textarea class="form-control"
                          rows="3"
                          name="reviews[{{$loop->index}}][comment]"
                          placeholder="Hãy chia sẻ cảm nhận về chất lượng sản phẩm..."></textarea>


            </div>{{-- /.review-item-card --}}

            @endforeach

            <div class="review-footer">
                <button type="button"
                        id="cancelReview">
                    Trở lại
                </button>

                <button type="submit"
                        class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Gửi đánh giá
                </button>
            </div>

        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    /* ---- Canvas clouds (Subtle Gray theme) ---- */
    const canvas = document.getElementById('sky-canvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let W, H, clouds = [];
        function resize() { W = canvas.width = window.innerWidth; H = canvas.height = window.innerHeight; }
        window.addEventListener('resize', resize); resize();
        function makeCloud() {
            return { x: Math.random()*W*1.2, y: Math.random()*H*.6,
                     r: 50+Math.random()*110, dx: .13+Math.random()*.2, alpha: .01+Math.random()*.03 };
        }
        for (let i = 0; i < 8; i++) clouds.push(makeCloud());
        function drawCloud(c) {
            const g = ctx.createRadialGradient(c.x,c.y,0,c.x,c.y,c.r);
            g.addColorStop(0, `rgba(0,0,0,${c.alpha})`);
            g.addColorStop(.6, `rgba(0,0,0,${c.alpha*.6})`);
            g.addColorStop(1, 'rgba(0,0,0,0)');
            ctx.beginPath(); ctx.arc(c.x,c.y,c.r,0,Math.PI*2);
            ctx.fillStyle = g; ctx.fill();
            [-.5,.5].forEach(o => {
                ctx.beginPath();
                ctx.arc(c.x+c.r*.55*o, c.y-c.r*.18, c.r*.72, 0, Math.PI*2);
                ctx.fillStyle = `rgba(0,0,0,${c.alpha*.7})`; ctx.fill();
            });
        }
        (function anim() {
            ctx.clearRect(0,0,W,H);
            clouds.forEach(c => { drawCloud(c); c.x += c.dx;
                if (c.x-c.r > W*1.2) { c.x=-c.r*2; c.y=Math.random()*H*.6; } });
            requestAnimationFrame(anim);
        })();
    }

    /* ---- Bubbles ---- */
    function spawnBubble() {
        const el = document.createElement('div'); el.className = 'bubble';
        const size = 4+Math.random()*14, dur = 8+Math.random()*12;
        el.style.cssText = [`width:${size}px`,`height:${size}px`,
            `left:${Math.random()*100}vw`,`bottom:-${size}px`,
            `animation-duration:${dur}s`,`animation-delay:${Math.random()*5}s`].join(';');
        document.body.appendChild(el);
        setTimeout(() => el.remove(), (dur+5)*1000);
    }
    for (let i = 0; i < 8; i++) spawnBubble();
    setInterval(spawnBubble, 3500);

    /* ---- Scroll Reveal ---- */
    const io = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) { e.target.classList.add('revealed'); io.unobserve(e.target); }
        });
    }, { threshold: 0.04, rootMargin: '0px 0px -16px 0px' });
    document.querySelectorAll('.reveal, .stagger-section').forEach(el => io.observe(el));

    /* ---- Ripple on action buttons ---- */
    document.querySelectorAll('.btn-action').forEach(btn => {
        btn.addEventListener('click', function (e) {
            const r    = btn.getBoundingClientRect();
            const size = Math.max(r.width, r.height) * 1.8;
            const rip  = document.createElement('span');
            rip.className = 'ripple-wave';
            rip.style.cssText = [`width:${size}px`,`height:${size}px`,
                `left:${e.clientX-r.left-size/2}px`,
                `top:${e.clientY-r.top-size/2}px`].join(';');
            btn.appendChild(rip);
            rip.addEventListener('animationend', () => rip.remove());
        });
    });

    /* ---- Timeline entry animation ---- */
    document.querySelectorAll('.timeline-item.active .timeline-icon').forEach((el, i) => {
        el.style.opacity = '0'; el.style.transform = 'scale(.5)';
        setTimeout(() => {
            el.style.transition = 'opacity .4s, transform .4s cubic-bezier(.34,1.56,.64,1)';
            el.style.opacity = '1';
            el.style.transform = el.closest('.timeline-item').classList.contains('current') ? 'scale(1.1)' : 'scale(1)';
        }, 300 + i * 140);
    });

    /* ---- Review Modal Logic ---- */
    const modal = document.getElementById('reviewModal');
    const openBtn = document.getElementById('openReviewModal');
    const closeBtn = document.getElementById('closeReviewModal');
    const cancelBtn = document.getElementById('cancelReview');

    if (openBtn && modal) {
        openBtn.addEventListener('click', function () {
            modal.classList.add('show');
        });
    }

    // Auto-open modal if requested via URL
    if (window.location.search.includes('open_review=1') && modal) {
        modal.classList.add('show');
    }

    if (closeBtn && modal) {
        closeBtn.addEventListener('click', function () {
            modal.classList.remove('show');
        });
    }

    if (cancelBtn && modal) {
        cancelBtn.addEventListener('click', function () {
            modal.classList.remove('show');
        });
    }

    window.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.remove('show');
        }
    });

})();
</script>
@endpush