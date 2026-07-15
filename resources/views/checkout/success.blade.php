@extends('layouts.app')
@section('title', 'Đặt hàng thành công - ElectronicShop')

@push('styles')
<style>
    .success-wrap {
        max-width: 700px;
        margin: 0 auto;
        padding: 50px 16px;
        text-align: center;
    }

    .success-icon {
        font-size: 64px;
        color: #16a34a;
        margin-bottom: 16px;
    }

    .success-wrap h1 {
        font-size: 26px;
        font-weight: 800;
        margin-bottom: 10px;
    }

    .success-wrap p {
        color: #6b7280;
        margin-bottom: 24px;
    }

    .order-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 3px 12px rgba(0,0,0,.06);
        padding: 24px;
        text-align: left;
        margin-bottom: 24px;
    }

    .order-card .row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
    }

    .order-card .row:last-child {
        border-bottom: none;
    }

    .order-card .label {
        color: #6b7280;
    }

    .item-row {
        display: flex;
        justify-content: space-between;
        font-size: 13px;
        padding: 6px 0;
    }

    .btn-group a {
        display: inline-block;
        padding: 13px 26px;
        border-radius: 10px;
        font-weight: 700;
        text-decoration: none;
        margin: 0 6px;
    }

    .btn-primary {
        background: #2563eb;
        color: #fff;
    }

    .btn-primary:hover { background: #1d4ed8; color: #fff; }

    .btn-outline {
        border: 2px solid #2563eb;
        color: #2563eb;
    }

    .status-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-paid {
        background: #dcfce7;
        color: #166534;
    }

    .status-unpaid {
        background: #fef3c7;
        color: #b45309;
    }
</style>
@endpush

@section('content')
<div class="success-wrap">
    <i class="fas fa-check-circle success-icon"></i>
    <h1>Đặt hàng thành công!</h1>
    <p>Cảm ơn bạn đã mua sắm tại ElectronicShop. Đơn hàng #{{ $order->id }} đang được xử lý.</p>

    <div class="order-card">
        <div class="row"><span class="label">Mã đơn hàng</span><span>#{{ $order->id }}</span></div>
        <div class="row"><span class="label">Người nhận</span><span>{{ $order->address->full_name ?? '' }} - {{ $order->address->phone ?? '' }}</span></div>
        <div class="row"><span class="label">Địa chỉ</span><span>{{ $order->address->full_address ?? '' }}</span></div>
        <div class="row"><span class="label">Phương thức thanh toán</span>
            <span>{{ $order->payment_method === 'momo' ? 'Ví MoMo' : 'Thanh toán khi nhận hàng (COD)' }}</span>
        </div>
        <div class="row"><span class="label">Trạng thái thanh toán</span>
            <span class="status-badge {{ $order->payment_status === 'paid' ? 'status-paid' : 'status-unpaid' }}">
                {{ $order->payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
            </span>
        </div>

        <div style="margin-top:14px">
            @foreach($order->items as $item)
            <div class="item-row">
                <span>{{ $item->product_name }} x{{ $item->quantity }}</span>
                <span>{{ number_format($item->total_price) }}đ</span>
            </div>
            @endforeach
        </div>

        <div class="row" style="font-weight:800;color:#2563eb;font-size:16px;margin-top:8px">
            <span>Tổng cộng</span><span>{{ number_format($order->total) }}đ</span>
        </div>
    </div>

    <div class="btn-group">
        <a href="{{ route('products.index') }}" class="btn-outline">Tiếp tục mua sắm</a>
        <a href="{{ route('home') }}" class="btn-primary">Về trang chủ</a>
    </div>
</div>
@endsection