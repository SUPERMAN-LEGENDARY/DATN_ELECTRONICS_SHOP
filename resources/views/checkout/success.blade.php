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
        color: #2E7D32;
        margin-bottom: 16px;
    }

    .success-wrap h1 {
        font-size: 24px;
        font-weight: 800;
        margin-bottom: 10px;
    }

    .success-wrap p {
        color: #666;
        margin-bottom: 24px;
    }

    .order-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        padding: 24px;
        text-align: left;
        margin-bottom: 24px;
    }

    .order-card .row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #f5f5f5;
        font-size: 14px;
    }

    .order-card .row:last-child {
        border-bottom: none;
    }

    .order-card .label {
        color: #888;
    }

    .item-row {
        display: flex;
        justify-content: space-between;
        font-size: 13px;
        padding: 6px 0;
    }

    .btn-group a {
        display: inline-block;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 700;
        text-decoration: none;
        margin: 0 6px;
    }

    .btn-primary {
        background: #1565C0;
        color: #fff;
    }

    .btn-outline {
        border: 1px solid #1565C0;
        color: #1565C0;
    }

    .status-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-paid {
        background: #E8F5E9;
        color: #2E7D32;
    }

    .status-unpaid {
        background: #FFF3E0;
        color: #E65100;
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

        <div class="row" style="font-weight:800;color:#1565C0;font-size:16px;margin-top:8px">
            <span>Tổng cộng</span><span>{{ number_format($order->total) }}đ</span>
        </div>
    </div>

    <div class="btn-group">
        <a href="{{ route('products.index') }}" class="btn-outline">Tiếp tục mua sắm</a>
        <a href="{{ route('home') }}" class="btn-primary">Về trang chủ</a>
    </div>
</div>
@endsection