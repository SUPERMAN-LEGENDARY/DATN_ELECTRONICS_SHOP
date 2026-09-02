@extends('layouts.app')
@section('title', 'Đặt hàng thành công - ElectronicShop')

@push('styles')
<style>
    /* ============================================================
       SAMSUNG OFFICIAL UI - GIAO DIỆN THÀNH CÔNG
       ============================================================ */
    :root {
        --samsung-gray-dark: #363636;
        --samsung-gray-dark-hover: #1f1f1f;
        --samsung-light-gray-dark: #f0f4ff;
        --samsung-black: #000000;
        --samsung-dark: #111111;
        --samsung-gray-dark: #363636;
        --samsung-gray-mid: #707070;
        --samsung-gray-light: #e0e0e0;
        --samsung-bg-section: #f8f9fa;
        --samsung-radius-pill: 30px;
        --samsung-radius-card: 16px;
        --samsung-success: #16a34a; /* Xanh lá thành công */
    }

    body {
        background: #ffffff;
        color: var(--samsung-dark);
        font-family: "SamsungOne", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        -webkit-font-smoothing: antialiased;
    }

    /* SCROLL REVEAL (Hiệu ứng mượt mà kiểu Samsung) */
    .reveal {
        opacity: 0; transform: translateY(20px);
        transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1), transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .reveal.revealed { opacity: 1; transform: translateY(0); }

    /* ============================================================
       PAGE WRAPPER
       ============================================================ */
    .success-page {
        min-height: 100vh;
        padding: 60px 20px 100px;
        background: #ffffff;
    }

    .success-wrap {
        max-width: 680px;
        margin: 0 auto;
        text-align: center;
    }

    /* Success Icon */
    .success-icon-wrap {
        width: 80px; height: 80px;
        border-radius: 50%;
        background: var(--samsung-success);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 24px;
        box-shadow: 0 8px 24px rgba(22, 163, 74, 0.2);
        animation: iconPop 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes iconPop {
        0%   { transform: scale(0.5); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
    .success-icon-wrap i { font-size: 36px; color: #ffffff; }

    .success-wrap h1 {
        font-size: clamp(26px, 4vw, 32px);
        font-weight: 800;
        color: var(--samsung-black);
        margin-bottom: 12px;
        letter-spacing: -0.5px;
    }

    .success-wrap p {
        color: var(--samsung-gray-dark);
        font-size: 15px;
        margin-bottom: 40px;
        line-height: 1.5;
    }

    /* ============================================================
       ORDER CARD — SAMSUNG STYLE
       ============================================================ */
    .order-card {
        background: var(--samsung-bg-section);
        border: 1px solid #e9ecef;
        border-radius: var(--samsung-radius-card);
        padding: 32px;
        text-align: left;
        margin-bottom: 40px;
    }

    .order-card .row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 0;
        border-bottom: 1px solid var(--samsung-gray-light);
        font-size: 14.5px;
        color: var(--samsung-black);
    }
    .order-card .row:last-child { border-bottom: none; }

    .order-card .label {
        color: var(--samsung-gray-dark);
        font-weight: 700;
    }

    .items-container {
        margin-top: 20px;
        padding: 16px;
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e9ecef;
    }

    .item-row {
        display: flex; justify-content: space-between;
        font-size: 14px; padding: 8px 0;
        color: var(--samsung-black); font-weight: 500;
    }
    .item-row + .item-row {
        border-top: 1px solid var(--samsung-gray-light);
    }

    .total-row {
        font-weight: 800; color: var(--samsung-black); font-size: 18px;
        margin-top: 16px; padding-top: 16px;
        border-top: 2px solid var(--samsung-black) !important;
    }

    /* ============================================================
       STATUS BADGES
       ============================================================ */
    .status-badge {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 5px 12px; border-radius: 12px;
        font-size: 12.5px; font-weight: 700;
    }
    .status-paid {
        background: #e8f5e9; color: #2e7d32;
    }
    .status-unpaid {
        background: #fff8e1; color: #f57f17;
    }

    /* ============================================================
       BUTTON GROUP (SAMSUNG PILL BUTTONS)
       ============================================================ */
    .btn-group {
        display: flex; justify-content: center; gap: 16px;
        flex-wrap: wrap;
    }
    .btn-group a {
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        padding: 15px 32px; border-radius: var(--samsung-radius-pill);
        font-weight: 700; font-size: 15px;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-primary {
        background: var(--samsung-gray-dark);
        color: #ffffff;
        border: 2px solid var(--samsung-gray-dark);
    }
    .btn-primary:hover {
        background: var(--samsung-gray-dark-hover);
        border-color: var(--samsung-gray-dark-hover);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(20, 40, 160, 0.2);
    }

    .btn-outline {
        background: #ffffff;
        color: var(--samsung-black);
        border: 2px solid var(--samsung-gray-light);
    }
    .btn-outline:hover {
        border-color: var(--samsung-black);
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<div class="success-page">
    <div class="success-wrap">

        <div class="success-icon-wrap">
            <i class="fas fa-check"></i>
        </div>

        <h1 class="reveal">Đặt hàng thành công!</h1>
        <p class="reveal">Cảm ơn bạn đã mua sắm tại ElectronicShop.<br>Đơn hàng <strong>#{{ $order->id }}</strong> của bạn đang được hệ thống xử lý.</p>

        <div class="order-card reveal">
            <div class="row">
                <span class="label">Mã đơn hàng</span>
                <strong>#{{ $order->id }}</strong>
            </div>
            <div class="row">
                <span class="label">Người nhận</span>
                <span>{{ $order->address->full_name ?? '' }} — {{ $order->address->phone ?? '' }}</span>
            </div>
            <div class="row">
                <span class="label">Địa chỉ</span>
                <span style="text-align: right; max-width: 60%">{{ $order->address->full_address ?? '' }}</span>
            </div>
            <div class="row">
                <span class="label">Phương thức thanh toán</span>
                <span>{{ $order->payment_method === 'momo' ? 'Ví MoMo' : 'Thanh toán khi nhận hàng (COD)' }}</span>
            </div>
            <div class="row">
                <span class="label">Trạng thái thanh toán</span>
                <span class="status-badge {{ $order->payment_status === 'paid' ? 'status-paid' : 'status-unpaid' }}">
                    <i class="fas {{ $order->payment_status === 'paid' ? 'fa-check-circle' : 'fa-clock' }}"></i>
                    {{ $order->payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                </span>
            </div>

            <div class="items-container">
                @foreach($order->items as $item)
                <div class="item-row">
                    <span>
        {{ $item->product_name }}
        @if($item->variant_attributes_text)
            <span style="font-size: 12px; color: #555; background: #eee; padding: 2px 6px; border-radius: 4px; margin: 0 4px; display: inline-block;">{{ $item->variant_attributes_text }}</span>
        @endif
        <span style="color:var(--samsung-gray-mid)"> × {{ $item->quantity }}</span>
    </span>
                    <span>{{ number_format($item->total_price) }}đ</span>
                </div>
                @endforeach
            </div>

            <div class="row total-row">
                <span>Tổng cộng</span>
                <span style="color:var(--samsung-gray-dark)">{{ number_format($order->total) }}đ</span>
            </div>
        </div>

        <div class="btn-group reveal">
            <a href="{{ route('products.index') }}" class="btn-outline">
                <i class="fas fa-store"></i> Tiếp tục mua sắm
            </a>
            <a href="{{ route('home') }}" class="btn-primary">
                <i class="fas fa-home"></i> Về trang chủ
            </a>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
/* ============================================================
   ANIMATIONS - Đã dọn dẹp các script bong bóng/canvas không cần thiết
   ============================================================ */
(function () {
    /* ---- Scroll Reveal (Intersection Observer) ---- */
    const io = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) { 
                e.target.classList.add('revealed'); 
                io.unobserve(e.target); 
            }
        });
    }, { threshold: 0.05, rootMargin: '0px 0px -20px 0px' });
    
    document.querySelectorAll('.reveal').forEach(el => io.observe(el));
})();
</script>
@endpush