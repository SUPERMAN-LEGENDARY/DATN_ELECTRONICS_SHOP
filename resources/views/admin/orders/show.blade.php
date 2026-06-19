@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng #' . $order->id)

@push('styles')
<style>
    .order-detail {
        background: #fff;
        border-radius: 8px;
        padding: 24px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    }
    .order-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px 32px;
        margin-bottom: 24px;
    }
    .order-info-grid .label {
        font-weight: 600;
        color: #495057;
        font-size: 14px;
    }
    .order-info-grid .value {
        font-size: 14px;
    }
    .order-items table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 16px;
    }
    .order-items th {
        background: #f8f9fc;
        font-weight: 600;
        font-size: 13px;
        padding: 10px 12px;
        border-bottom: 2px solid #e9ecef;
        text-align: left;
    }
    .order-items td {
        padding: 10px 12px;
        border-bottom: 1px solid #e9ecef;
    }
    .order-items tbody tr:hover {
        background: #f8f9fc;
    }
    .badge {
        display: inline-block;
        padding: 4px 12px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 20px;
        line-height: 1.5;
        min-width: 70px;
        text-align: center;
    }
    .bg-success { background: #d4edda; color: #155724; }
    .bg-danger { background: #f8d7da; color: #721c24; }
    .bg-warning { background: #fff3cd; color: #856404; }
    .bg-info { background: #d1ecf1; color: #0c5460; }
    .bg-secondary { background: #e2e3e5; color: #383d41; }

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

    .mt-3 { margin-top: 16px; }
    .mb-3 { margin-bottom: 16px; }
    .total-row {
        font-weight: bold;
        font-size: 16px;
    }
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

@section('content')
<div class="header-bar">
    <h1>Chi tiết đơn hàng #{{ $order->id }}</h1>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">← Quay lại danh sách</a>
</div>

<div class="order-detail">
    <!-- Thông tin chung -->
    <div class="order-info-grid">
        <div><span class="label">Khách hàng:</span> <span class="value">{{ $order->user->name ?? 'N/A' }}</span></div>
        <div><span class="label">Email:</span> <span class="value">{{ $order->user->email ?? 'N/A' }}</span></div>
        <div><span class="label">Số điện thoại:</span> <span class="value">{{ $order->user->phone ?? 'N/A' }}</span></div>
        <div><span class="label">Địa chỉ giao hàng:</span> <span class="value">
            {{ $order->address->full_name ?? '' }}<br>
            {{ $order->address->street ?? '' }}, {{ $order->address->ward ?? '' }}, {{ $order->address->district ?? '' }}, {{ $order->address->province ?? '' }}
        </span></div>
        <div><span class="label">Phương thức thanh toán:</span> <span class="value">{{ strtoupper($order->payment_method) }}</span></div>
        <div><span class="label">Trạng thái thanh toán:</span>
            @php
                $paymentClass = match($order->payment_status) {
                    'paid' => 'bg-success',
                    'unpaid' => 'bg-warning',
                    'refunded' => 'bg-danger',
                    default => 'bg-secondary'
                };
            @endphp
            <span class="badge {{ $paymentClass }}">{{ ucfirst($order->payment_status) }}</span>
        </div>
        <div><span class="label">Trạng thái đơn hàng:</span>
            @php
                $statusClass = match($order->status) {
                    'delivered' => 'bg-success',
                    'cancelled' => 'bg-danger',
                    'pending' => 'bg-warning',
                    'processing' => 'bg-info',
                    default => 'bg-secondary'
                };
            @endphp
            <span class="badge {{ $statusClass }}">{{ ucfirst($order->status) }}</span>
        </div>
        <div><span class="label">Ngày đặt:</span> <span class="value">{{ $order->created_at->format('d/m/Y H:i') }}</span></div>
        @if($order->voucher)
        <div><span class="label">Mã giảm giá:</span> <span class="value">{{ $order->voucher->code }} (giảm {{ $order->voucher->discount_percent }}%)</span></div>
        @endif
        <div><span class="label">Ghi chú:</span> <span class="value">{{ $order->note ?? 'Không có' }}</span></div>
    </div>

    <!-- Danh sách sản phẩm -->
    <div class="order-items">
        <h3>Chi tiết sản phẩm</h3>
        <table>
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Đơn giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ number_format($item->unit_price) }}đ</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->total_price) }}đ</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="3" style="text-align:right; font-weight:600;">Tạm tính:</td>
                    <td>{{ number_format($order->subtotal) }}đ</td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align:right; font-weight:600;">Giảm giá:</td>
                    <td>-{{ number_format($order->discount_amount) }}đ</td>
                </tr>
                <tr class="total-row">
                    <td colspan="3" style="text-align:right;">Tổng cộng:</td>
                    <td>{{ number_format($order->total) }}đ</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection