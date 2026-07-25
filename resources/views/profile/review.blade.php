@extends('layouts.app')
@section('title', 'Chi tiết đơn hàng #' . $order->id . ' - ElectronicShop')

@push('styles')
<style>
/* ============================================================
   PAGE BACKGROUND — sky gradient
   ============================================================ */
body {
    background: linear-gradient(180deg,
        #bae6fd 0%, #e0f2fe 18%, #f0f9ff 38%,
        #e0f2fe 62%, #bae6fd 100%) fixed;
    background-attachment: fixed;
}
#sky-canvas {
    position: fixed; inset: 0; width: 100%; height: 100%;
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
    background: rgba(125,211,252,.28);
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
.alert-sky-success { background: rgba(220,252,231,.9); color: #166534; border: 1px solid rgba(187,247,208,.8); }
.alert-sky-error   { background: rgba(254,226,226,.92); color: #991b1b; border: 1px solid rgba(252,165,165,.6); }
.alert-sky-info    { background: rgba(186,230,253,.5); color: #0c4a6e; border: 1px solid rgba(125,211,252,.5); }

/* ============================================================
   TOP ROW — back + order id + status
   ============================================================ */
.order-toprow {
    display: flex; justify-content: space-between; align-items: center;
    gap: 14px; flex-wrap: wrap; margin-bottom: 22px;
}
.btn-back {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 9px 18px; border-radius: 10px;
    background: rgba(255,255,255,.75); backdrop-filter: blur(8px);
    border: 1px solid rgba(186,230,253,.65);
    color: #0369a1; font-weight: 700; font-size: 13.5px;
    text-decoration: none;
    transition: background .2s, transform .18s, box-shadow .2s;
}
.btn-back:hover {
    background: rgba(186,230,253,.5); color: #0c4a6e;
    transform: translateY(-1px); box-shadow: 0 4px 14px rgba(14,165,233,.14);
}
.order-id-block { text-align: right; }
.order-id-block h5 { font-size: 16px; font-weight: 800; color: #0c4a6e; margin-bottom: 6px; }

/* Status badges */
.status-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 14px; border-radius: 20px;
    font-size: 12.5px; font-weight: 700; letter-spacing: .3px;
}
.badge-warning   { background:rgba(251,191,36,.18); color:#b45309; border:1px solid rgba(251,191,36,.4); }
.badge-info      { background:rgba(56,189,248,.18); color:#0369a1; border:1px solid rgba(56,189,248,.4); }
.badge-primary   { background:rgba(14,165,233,.18); color:#0c4a6e; border:1px solid rgba(14,165,233,.4); }
.badge-success   { background:rgba(34,197,94,.15);  color:#166534; border:1px solid rgba(34,197,94,.35); }
.badge-danger    { background:rgba(239,68,68,.12);  color:#b91c1c; border:1px solid rgba(239,68,68,.3); }
.badge-secondary { background:rgba(148,163,184,.18);color:#475569; border:1px solid rgba(148,163,184,.35);}

/* ============================================================
   SECTION CARD — glassmorphism
   ============================================================ */
.section-card {
    background: rgba(255,255,255,.84);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(186,230,253,.65);
    border-radius: 18px; overflow: hidden;
    box-shadow: 0 6px 24px rgba(14,165,233,.1);
    margin-bottom: 20px;
    transition: box-shadow .25s, border-color .25s;
}
.section-card:hover { box-shadow: 0 10px 30px rgba(14,165,233,.15); border-color: #7dd3fc; }

.section-card-header {
    background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
    padding: 14px 22px;
    border-bottom: 1px solid rgba(186,230,253,.55);
    display: flex; align-items: center; gap: 8px;
    font-size: 14px; font-weight: 800; color: #0c4a6e;
}
.section-card-header .hicon {
    width: 28px; height: 28px; border-radius: 8px;
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    color: #fff; font-size: 12px;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 2px 8px rgba(14,165,233,.3);
}
.section-card-body { padding: 20px 22px; }

/* ============================================================
   TIMELINE — sky-blue
   ============================================================ */
.timeline {
    display: flex; align-items: center;
    justify-content: space-between; overflow-x: auto;
    padding: 6px 0;
}

.timeline-item { text-align: center; min-width: 90px; }

.timeline-icon {
    width: 60px; height: 60px; border-radius: 50%;
    background: rgba(186,230,253,.5);
    border: 2px solid rgba(125,211,252,.4);
    color: #7dd3fc; font-size: 22px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto; position: relative;
    transition: all .35s cubic-bezier(.34,1.56,.64,1);
}
.timeline-item.active .timeline-icon {
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    border-color: #0ea5e9;
    color: #fff;
    box-shadow: 0 4px 16px rgba(14,165,233,.38);
    transform: scale(1.1);
}
/* pulse ring on current active step */
.timeline-item.current .timeline-icon::after {
    content: ''; position: absolute;
    inset: -6px; border-radius: 50%;
    border: 2px solid rgba(14,165,233,.4);
    animation: pulseRing 1.6s ease-out infinite;
}
@keyframes pulseRing { 0%{transform:scale(1);opacity:.7} 100%{transform:scale(1.5);opacity:0} }

.timeline-line {
    flex: 1; height: 4px; min-width: 20px;
    background: rgba(186,230,253,.5);
    border-radius: 2px; margin: 0 8px;
    transition: background .4s ease;
    position: relative; overflow: hidden;
}
.timeline-line.active {
    background: linear-gradient(90deg, #0369a1, #0ea5e9);
    box-shadow: 0 1px 6px rgba(14,165,233,.25);
}
/* animated fill on active lines */
.timeline-line.active::after {
    content: ''; position: absolute;
    top: 0; left: -40%; width: 40%; height: 100%;
    background: rgba(255,255,255,.4);
    animation: lineShimmer 2s linear infinite;
}
@keyframes lineShimmer { to { left: 140%; } }

.timeline-text {
    margin-top: 10px; font-size: 12.5px; font-weight: 700;
    color: #7dd3fc;
}
.timeline-item.active .timeline-text { color: #0c4a6e; }

/* ============================================================
   ADDRESS SECTION
   ============================================================ */
.addr-row {
    display: flex; gap: 16px; align-items: flex-start;
}
.addr-pin {
    width: 44px; height: 44px; flex-shrink: 0;
    border-radius: 12px;
    background: linear-gradient(135deg, #ef4444, #f87171);
    color: #fff; font-size: 18px;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 12px rgba(239,68,68,.3);
    margin-top: 2px;
}
.addr-name { font-size: 16px; font-weight: 800; color: #0c4a6e; margin-bottom: 4px; }
.addr-phone { font-size: 13.5px; color: #0369a1; margin-bottom: 6px; }
.addr-full  { font-size: 14px; color: #0369a1; opacity: .85; }

/* ============================================================
   PRODUCT ROWS
   ============================================================ */
.product-row {
    display: flex; align-items: center;
    gap: 16px; padding: 16px 0;
    border-bottom: 1px solid rgba(186,230,253,.4);
    transition: background .2s;
}
.product-row:last-of-type { border-bottom: none; }
.product-row:hover { background: rgba(186,230,253,.12); border-radius: 10px; padding-left: 8px; padding-right: 8px; }

.product-img {
    width: 82px; height: 82px; flex-shrink: 0;
    object-fit: contain; border-radius: 12px;
    border: 1px solid rgba(186,230,253,.6);
    background: linear-gradient(160deg, #f0f9ff, #e0f2fe);
    padding: 4px; box-sizing: border-box;
    transition: transform .3s cubic-bezier(.16,1,.3,1);
}
.product-row:hover .product-img { transform: scale(1.06); }

.product-name {
    font-size: 14.5px; font-weight: 700; color: #0c4a6e;
    text-decoration: none; display: block; margin-bottom: 3px;
    transition: color .15s;
}
.product-name:hover { color: #0ea5e9; }
.product-qty { font-size: 13px; color: #0369a1; opacity: .8; }

.product-unit  { font-size: 14px; color: #0369a1; font-weight: 500; text-align: right; white-space: nowrap; }
.product-total { font-size: 16px; font-weight: 800; color: #ef4444; text-align: right; white-space: nowrap; }

/* ============================================================
   SUMMARY TABLE
   ============================================================ */
.summary-section {
    display: grid; grid-template-columns: 1fr 1fr; gap: 20px;
    margin-top: 20px; padding-top: 18px;
    border-top: 1px solid rgba(186,230,253,.4);
}
@media (max-width: 640px) { .summary-section { grid-template-columns: 1fr; } }

.summary-table { width: 100%; border-collapse: collapse; font-size: 14px; }
.summary-table td { padding: 8px 0; color: #0369a1; }
.summary-table td:last-child { text-align: right; font-weight: 600; color: #0c4a6e; }
.summary-table .total-row td { border-top: 2px solid rgba(186,230,253,.55); padding-top: 12px; }
.summary-table .total-row td { font-size: 17px; font-weight: 800; }
.summary-table .total-row td:first-child { color: #0c4a6e; }
.summary-table .total-row td:last-child  { color: #ef4444; }
.discount-val { color: #16a34a !important; }

/* payment info box */
.payment-box {
    background: rgba(186,230,253,.22);
    backdrop-filter: blur(6px);
    border: 1px solid rgba(125,211,252,.45);
    border-radius: 14px; padding: 18px;
}
.payment-box h6 { font-size: 14px; font-weight: 800; color: #0c4a6e; margin-bottom: 12px; }
.payment-box p  { font-size: 13.5px; color: #0369a1; margin-bottom: 8px; }
.payment-box p:last-child { margin-bottom: 0; }
.payment-box strong { color: #0c4a6e; }

.pay-badge-paid     { background:rgba(34,197,94,.15);  color:#166534; border:1px solid rgba(34,197,94,.35);  padding:3px 10px; border-radius:10px; font-size:12px; font-weight:700; }
.pay-badge-refunded { background:rgba(148,163,184,.18);color:#475569; border:1px solid rgba(148,163,184,.35);padding:3px 10px; border-radius:10px; font-size:12px; font-weight:700; }
.pay-badge-pending  { background:rgba(251,191,36,.18); color:#b45309; border:1px solid rgba(251,191,36,.4);  padding:3px 10px; border-radius:10px; font-size:12px; font-weight:700; }

/* ============================================================
   ACTION BUTTONS
   ============================================================ */
.actions-row {
    display: flex; justify-content: flex-end; gap: 10px;
    flex-wrap: wrap; margin-top: 8px;
}
.btn-action {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 11px 22px; border-radius: 11px;
    font-size: 14px; font-weight: 700; cursor: pointer;
    transition: opacity .2s, transform .18s, box-shadow .2s;
    border: none; font-family: inherit; text-decoration: none;
    position: relative; overflow: hidden;
}
.btn-action::after {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,.28) 50%, transparent 60%);
    transform: translateX(-120%); transition: transform .5s ease; pointer-events: none;
}
.btn-action:hover::after { transform: translateX(120%); }
.btn-action:hover { opacity:.91; transform:translateY(-2px); }

.btn-cancel   { background:rgba(239,68,68,.12); color:#ef4444; border:1px solid rgba(239,68,68,.3); }
.btn-cancel:hover   { background:#ef4444; color:#fff; box-shadow:0 5px 16px rgba(239,68,68,.35); }
.btn-received { background:rgba(34,197,94,.12); color:#16a34a; border:1px solid rgba(34,197,94,.35); }
.btn-received:hover { background:#22c55e; color:#fff; box-shadow:0 5px 16px rgba(34,197,94,.35); }
.btn-reorder  { background:linear-gradient(135deg,#0369a1,#0ea5e9); color:#fff; box-shadow:0 3px 14px rgba(14,165,233,.3); }
.btn-reorder:hover  { box-shadow:0 7px 22px rgba(14,165,233,.45); }
.btn-review   { background:linear-gradient(135deg,#d97706,#f59e0b); color:#fff; box-shadow:0 3px 14px rgba(245,158,11,.3); }
.btn-review:hover   { box-shadow:0 7px 22px rgba(245,158,11,.45); }
.btn-review:hover { color:#fff; }

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
                            <span style="font-weight:400;color:#0369a1;font-size:14px;margin-left:12px">{{ $order->address->phone }}</span>
                        </div>
                        <div class="addr-full">
                            <i class="fas fa-location-dot" style="font-size:12px;margin-right:5px;color:#7dd3fc"></i>
                            {{ $order->address->full_address }}
                        </div>
                    </div>
                </div>
                @else
                <p style="color:#7dd3fc;margin:0">Không có thông tin địa chỉ.</p>
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
                        <img src="https://placehold.co/82x82/e0f2fe/0369a1?text=SP" class="product-img" alt="{{ $item->product_name }}">
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
                        <h6><i class="fas fa-credit-card" style="margin-right:6px;color:#0ea5e9"></i>Thông tin thanh toán</h6>
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
                            <span style="font-family:monospace;background:rgba(186,230,253,.35);padding:2px 8px;border-radius:6px;color:#0369a1;font-weight:700">{{ $order->voucher->code }}</span>
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
            <a href="{{ route('profile.review.create', $order) }}" class="btn-action btn-review">
                <i class="fas fa-star"></i> Đánh giá
            </a>
            @endif
        </div>

    </div>{{-- /.stagger-section --}}
</div>{{-- /.ordershow-container --}}
</div>{{-- /.ordershow-page --}}
@endsection

@push('scripts')
<script>
(function () {
    /* ---- Canvas clouds ---- */
    const canvas = document.getElementById('sky-canvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let W, H, clouds = [];
        function resize() { W = canvas.width = window.innerWidth; H = canvas.height = window.innerHeight; }
        window.addEventListener('resize', resize); resize();
        function makeCloud() {
            return { x: Math.random()*W*1.2, y: Math.random()*H*.6,
                     r: 50+Math.random()*110, dx: .13+Math.random()*.2, alpha: .05+Math.random()*.1 };
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
                ctx.beginPath();
                ctx.arc(c.x+c.r*.55*o, c.y-c.r*.18, c.r*.72, 0, Math.PI*2);
                ctx.fillStyle = `rgba(255,255,255,${c.alpha*.7})`; ctx.fill();
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

})();
</script>
@endpush
