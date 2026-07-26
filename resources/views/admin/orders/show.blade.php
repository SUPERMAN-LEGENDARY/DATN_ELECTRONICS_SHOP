@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng #' . $order->id)

@push('styles')
<style>
    .order-detail {
        background: #fff;
        border-radius: 8px;
        padding: 24px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.07);
    }
    .order-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px 32px;
        margin-bottom: 24px;
        background: #f8f9fc;
        border-radius: 8px;
        padding: 20px;
        border: 1px solid #e9ecef;
    }
    .order-info-grid .info-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .order-info-grid .label {
        font-weight: 700;
        color: #1565C0;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    .order-info-grid .value {
        font-size: 14px;
        color: #333;
    }

    /* ── Bảng sản phẩm ── */
    .order-items h3 {
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 0;
        color: #333;
    }
    .order-items .table-wrap {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,0.07);
        margin-top: 14px;
    }
    .order-items table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .order-items thead tr {
        background: #1565C0;
    }
    .order-items th {
        padding: 11px 14px;
        font-weight: 700;
        font-size: 13px;
        color: #fff;
        text-align: left;
        border: none;
        white-space: nowrap;
    }
    .order-items tbody tr {
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.12s;
    }
    .order-items tbody tr:last-child { border-bottom: none; }
    .order-items tbody tr:hover td { background: #f0f6ff; }
    .order-items td {
        padding: 11px 14px;
        vertical-align: middle;
        color: #333;
    }

    /* ── Dòng tổng ── */
    .order-items .subtotal-row td,
    .order-items .discount-row td,
    .order-items .total-row td {
        background: #f8f9fc;
        border-top: 1px solid #e9ecef;
    }
    .order-items .total-row td {
        font-weight: 800;
        font-size: 15px;
        color: #1565C0;
        border-top: 2px solid #1565C0;
    }
    .order-items .discount-row .amount { color: #dc3545; }

    /* ── Badge ── */
    .badge {
        display: inline-block;
        padding: 4px 14px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 20px;
        line-height: 1.5;
        text-align: center;
        white-space: nowrap;
        width: fit-content;
        max-width: 100%;
    }
    .badge-pending    { background: #fff3cd; color: #856404; }
    .badge-confirmed  { background: #d1ecf1; color: #0c5460; }
    .badge-processing { background: #cce5ff; color: #004085; }
    .badge-shipped    { background: #e2d9f3; color: #4a235a; }
    .badge-delivered  { background: #d4edda; color: #155724; }
    .badge-cancelled  { background: #f8d7da; color: #721c24; }
    .badge-returned   { background: #e2e3e5; color: #383d41; }
    .badge-paid       { background: #d4edda; color: #155724; }
    .badge-unpaid     { background: #fff3cd; color: #856404; }
    .badge-refunded   { background: #f8d7da; color: #721c24; }

    /* ── Thuộc tính sản phẩm ── */
    .attr-item {
        font-size: 12px;
        line-height: 1.7;
    }
    .attr-item .attr-name {
        color: #495057;
        font-weight: 600;
    }

    /* ── Header ── */
    .btn {
        display: inline-block;
        font-weight: 500;
        font-size: 13px;
        padding: 6px 16px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        transition: all 0.15s;
        text-decoration: none;
    }
    .btn-secondary { background: #6c757d; color: #fff; }
    .btn-secondary:hover { background: #5a6268; }
    .header-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 10px;
    }
    .header-bar h1 {
        font-size: 20px;
        font-weight: 800;
        margin: 0;
    }
</style>
@endpush

@php
$statusLabels = [
    'pending'    => 'Chờ xác nhận',
    'confirmed'  => 'Đã xác nhận',
    'processing' => 'Đang xử lý',
    'shipped'    => 'Đang giao',
    'delivered'  => 'Đã giao',
    'cancelled'  => 'Đã hủy',
    'returned'   => 'Đã hoàn trả',
];
$paymentLabels = [
    'unpaid'   => 'Chưa thanh toán',
    'paid'     => 'Đã thanh toán',
    'refunded' => 'Đã hoàn tiền',
];
$paymentMethods = [
    'cod'  => 'Thanh toán khi nhận hàng (COD)',
    'momo' => 'Ví MoMo',
];
@endphp

@section('content')
<div class="header-bar">
    <h1><i class="fas fa-receipt"></i> Chi tiết đơn hàng #{{ $order->id }}</h1>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">← Quay lại danh sách</a>
</div>

<div class="order-detail">

    <!-- Thông tin chung -->
    <div class="order-info-grid">
        <div class="info-item">
            <span class="label">Khách hàng</span>
            <span class="value">{{ $order->user->name ?? 'N/A' }}</span>
        </div>
        <div class="info-item">
            <span class="label">Email</span>
            <span class="value">{{ $order->user->email ?? 'N/A' }}</span>
        </div>
        <div class="info-item">
    <span class="label">Số điện thoại</span>
    <span class="value">{{ $order->address->phone ?? 'N/A' }}</span>
</div>
        <div class="info-item">
            <span class="label">Địa chỉ giao hàng</span>
            <span class="value">
                {{ $order->address->full_name ?? '' }}<br>
                {{ $order->address->street ?? '' }}, {{ $order->address->ward ?? '' }}, {{ $order->address->district ?? '' }}, {{ $order->address->province ?? '' }}
            </span>
        </div>
        <div class="info-item">
            <span class="label">Phương thức thanh toán</span>
            <span class="value">{{ $paymentMethods[$order->payment_method] ?? strtoupper($order->payment_method) }}</span>
        </div>
        <div class="info-item">
            <span class="label">Trạng thái thanh toán</span>
            <span class="badge badge-{{ $order->payment_status }}">
                {{ $paymentLabels[$order->payment_status] ?? $order->payment_status }}
            </span>
        </div>
        <div class="info-item">
            <span class="label">Trạng thái đơn hàng</span>
            <span class="badge badge-{{ $order->status }}">
                {{ $statusLabels[$order->status] ?? $order->status }}
            </span>
        </div>
        <div class="info-item">
            <span class="label">Ngày đặt</span>
            <span class="value">{{ $order->created_at->format('d/m/Y H:i') }}</span>
        </div>
        @if($order->voucher)
        <div class="info-item">
            <span class="label">Mã giảm giá</span>
            <span class="value">{{ $order->voucher->code }} (giảm {{ $order->voucher->discount_percent }}%)</span>
        </div>
        @endif
        <div class="info-item">
            <span class="label">Ghi chú</span>
            <span class="value">{{ $order->note ?? 'Không có' }}</span>
        </div>
    </div>

    <!-- Danh sách sản phẩm -->
    <div class="order-items">
        <h3><i class="fas fa-box"></i> Chi tiết sản phẩm</h3>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Thông số</th>
                        <th>Đơn giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td style="font-weight:600;">{{ $item->product_name }}</td>
                        <td>
                            @forelse($item->attributes as $attr)
                                <div class="attr-item">
                                    <span class="attr-name">{{ $attr->attribute->name ?? $attr->attribute_id }}:</span>
                                    {{ $attr->value }}
                                </div>
                            @empty
                                <span style="color:#aaa; font-size:13px;">—</span>
                            @endforelse
                        </td>
                        <td>{{ number_format($item->unit_price) }}đ</td>
                        <td>{{ $item->quantity }}</td>
                        <td style="font-weight:600;">{{ number_format($item->total_price) }}đ</td>
                    </tr>
                    @endforeach
                    <tr class="subtotal-row">
                        <td colspan="4" style="text-align:right; font-weight:600;">Tạm tính:</td>
                        <td style="font-weight:600;">{{ number_format($order->subtotal) }}đ</td>
                    </tr>
                    <tr class="discount-row">
                        <td colspan="4" style="text-align:right; font-weight:600;">Giảm giá:</td>
                        <td class="amount" style="font-weight:600;">-{{ number_format($order->discount_amount) }}đ</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="4" style="text-align:right;">Tổng cộng:</td>
                        <td>{{ number_format($order->total) }}đ</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection