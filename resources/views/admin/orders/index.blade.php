@extends('layouts.admin')
@section('title', 'Quản lý đơn hàng')

@push('styles')
<style>
.admin-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:12px; }
.admin-header h1 { font-size:20px; font-weight:800; }
.btn-primary { background:#1565C0; color:#fff; border:none; padding:9px 18px; border-radius:6px; font-size:14px; font-weight:600; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
.btn-primary:hover { background:#0D47A1; }
.btn-secondary { background:#fff; color:#424242; border:1px solid #e0e0e0; padding:9px 18px; border-radius:6px; font-size:14px; font-weight:600; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
.btn-secondary:hover { background:#fafafa; border-color:#bdbdbd; }
.btn-danger  { background:#E53935; color:#fff; border:none; padding:6px 12px; border-radius:4px; font-size:12px; font-weight:600; cursor:pointer; }
.btn-info { background:#17a2b8; color:#fff; border:none; padding:6px 12px; border-radius:4px; font-size:12px; font-weight:600; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; }
.btn-info:hover { background:#138496; }
.btn-trash   { background:#fff; color:#757575; border:1px solid #e0e0e0; padding:9px 18px; border-radius:6px; font-size:14px; font-weight:600; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:6px; position:relative; }
.btn-trash:hover { background:#fafafa; border-color:#bdbdbd; }
.trash-badge { background:#E53935; color:#fff; border-radius:10px; font-size:11px; font-weight:700; padding:1px 7px; margin-left:4px; }
.filter-bar { display:flex; gap:10px; margin-bottom:16px; flex-wrap:wrap; }
.filter-bar input, .filter-bar select { border:1px solid #e0e0e0; border-radius:6px; padding:8px 12px; font-size:13px; outline:none; }
.filter-bar button { background:#1565C0; color:#fff; border:none; border-radius:6px; padding:8px 16px; font-size:13px; cursor:pointer; }
.alert-success { background:#E8F5E9; border:1px solid #A5D6A7; color:#2E7D32; padding:10px 16px; border-radius:6px; margin-bottom:16px; font-size:14px; }
.alert-danger { background:#f8d7da; border:1px solid #f5c6cb; color:#721c24; padding:10px 16px; border-radius:6px; margin-bottom:16px; font-size:14px; }
.text-center { text-align:center; }

/* ── Bảng đơn hàng ── */
.table-wrap {
    overflow-x: auto;
    border-radius: 8px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.07);
    background: #fff;
}
table.data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.data-table thead tr {
    background: #1565C0;
}
.data-table th {
    padding: 12px 14px;
    text-align: left;
    font-weight: 700;
    font-size: 13px;
    color: #fff;
    white-space: nowrap;
    border: none;
}
.data-table th:first-child { border-radius: 8px 0 0 0; }
.data-table th:last-child  { border-radius: 0 8px 0 0; }
.data-table tbody tr {
    border-bottom: 1px solid #f0f0f0;
    transition: background 0.12s;
}
.data-table tbody tr:last-child { border-bottom: none; }
.data-table tbody tr:hover td { background: #f0f6ff; }
.data-table td {
    padding: 11px 14px;
    vertical-align: middle;
    color: #333;
}
.data-table td:first-child {
    font-weight: 700;
    color: #1565C0;
}

/* ── Badge trạng thái ── */
.badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
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

/* ── Dropdown trạng thái ── */
.status-select {
    padding: 5px 10px;
    border-radius: 20px;
    border: 1px solid #ced4da;
    font-size: 12px;
    font-weight: 600;
    background: #fff;
    cursor: pointer;
    min-width: 130px;
    outline: none;
    transition: border-color 0.15s;
}
.status-select:focus { border-color: #1565C0; }
.status-select:disabled { opacity: 0.6; cursor: not-allowed; }

/* ── Nút thao tác ── */
.action-group { display: flex; gap: 6px; align-items: center; }
.btn-view {
    background: #e3f2fd;
    color: #1565C0;
    border: none;
    padding: 6px 10px;
    border-radius: 5px;
    font-size: 13px;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    transition: background 0.15s;
}
.btn-view:hover { background: #bbdefb; }
.btn-del {
    background: #fdecea;
    color: #c62828;
    border: none;
    padding: 6px 10px;
    border-radius: 5px;
    font-size: 13px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    transition: background 0.15s;
}
.btn-del:hover { background: #ffcdd2; }

/* ── Phân trang ──
   Project này không load Tailwind CSS, nên view pagination mặc định của Laravel
   (dùng class Tailwind như h-5 w-5 cho icon SVG mũi tên) bị mất kích thước và
   hiển thị icon to bất thường. Đã chuyển sang view "bootstrap-4" (chữ « » thay
   vì SVG) và style lại thủ công cho khớp giao diện admin hiện tại. */
.pagination {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
    gap: 4px;
    flex-wrap: wrap;
}
.pagination .page-item .page-link {
    display: inline-block;
    padding: 6px 12px;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    color: #1565C0;
    font-size: 13px;
    text-decoration: none;
    background: #fff;
    cursor: pointer;
}
.pagination .page-item .page-link:hover {
    background: #f0f6ff;
    border-color: #1565C0;
}
.pagination .page-item.active .page-link {
    background: #1565C0;
    border-color: #1565C0;
    color: #fff;
}
.pagination .page-item.disabled .page-link {
    color: #bdbdbd;
    cursor: not-allowed;
    background: #fafafa;
}
</style>
@endpush

@section('content')
<div class="admin-header">
    <h1><i class="fas fa-shopping-cart"></i> Quản lý đơn hàng</h1>
    <div style="display:flex;gap:10px;align-items:center">
        <a href="{{ route('admin.orders.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i> Thêm đơn hàng
        </a>
        <a href="{{ route('admin.orders.trash') }}" class="btn-trash">
            <i class="fas fa-trash-alt"></i> Thùng rác
            @if($trashedCount > 0)
                <span class="trash-badge">{{ $trashedCount }}</span>
            @endif
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
@endif

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
$allowedTransitions = [
    'pending'    => ['confirmed', 'cancelled'],
    'confirmed'  => ['processing', 'cancelled'],
    'processing' => ['shipped', 'cancelled'],
    'shipped'    => ['delivered', 'cancelled'],
    'delivered'  => [], // Không cho phép hoàn trả sau khi đã giao
];
@endphp

{{-- Thanh lọc --}}
<form class="filter-bar" method="GET" action="{{ route('admin.orders.index') }}">
    <input type="text" name="q" placeholder="Tìm theo ID hoặc tên khách hàng..." value="{{ request('q') }}" style="flex:1;min-width:160px">
    <select name="status">
        <option value="">Tất cả trạng thái</option>
        @foreach($statusLabels as $val => $label)
            <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    <select name="payment_status">
        <option value="">Tất cả thanh toán</option>
        @foreach($paymentLabels as $val => $label)
            <option value="{{ $val }}" {{ request('payment_status') == $val ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    <button type="submit"><i class="fas fa-search"></i> Lọc</button>
    <a href="{{ route('admin.orders.index') }}" style="padding:8px 12px;font-size:13px;color:#666;text-decoration:none">Xóa bộ lọc</a>
</form>

{{-- Bảng đơn hàng --}}
<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Khách hàng</th>
                <th>Tổng tiền</th>
                <th>Trạng thái đơn</th>
                <th>Thanh toán</th>
                <th>Ngày tạo</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td>#{{ $order->id }}</td>
                <td>{{ $order->user->name ?? 'N/A' }}</td>
                <td style="font-weight:600;">{{ number_format($order->total) }}đ</td>

                {{-- Trạng thái đơn hàng --}}
                <td>
                    @php
                        $isLocked = in_array($order->status, ['cancelled', 'returned', 'delivered']);
                        $nextOptions = $allowedTransitions[$order->status] ?? [];
                    @endphp
                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                        @csrf @method('PATCH')
                        <select name="status" class="status-select badge-{{ $order->status }}"
                                onchange="handleOrderStatusChange(this)" @if($isLocked) disabled @endif>
                            <option value="{{ $order->status }}" selected>
                                {{ $statusLabels[$order->status] ?? $order->status }}
                            </option>
                            @foreach($nextOptions as $st)
                                <option value="{{ $st }}">{{ $statusLabels[$st] ?? $st }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="payment_status" class="payment-status-field" value="{{ $order->payment_status }}">
                    </form>
                </td>

                {{-- Trạng thái thanh toán --}}
                <td>
                    @php
                        $payLocked = in_array($order->status, ['cancelled', 'returned']) || $order->payment_status !== 'unpaid';
                    @endphp
                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                        @csrf @method('PATCH')
                        <select name="payment_status" class="status-select badge-{{ $order->payment_status }}"
                                onchange="this.form.submit()" @if($payLocked) disabled @endif>
                            @if($order->payment_status === 'unpaid')
                                <option value="unpaid" selected>Chưa thanh toán</option>
                                <option value="paid">Đã thanh toán</option>
                            @elseif($order->payment_status === 'paid')
                                <option value="paid" selected>Đã thanh toán</option>
                            @else
                                <option value="refunded" selected>Đã hoàn tiền</option>
                            @endif
                        </select>
                        <input type="hidden" name="status" value="{{ $order->status }}">
                    </form>
                </td>

                <td style="color:#666;">{{ $order->created_at->format('d/m/Y H:i') }}</td>

                <td>
                    <div class="action-group">
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn-view">
                            <i class="fas fa-eye"></i>
                        </a>
                        <form action="{{ route('admin.orders.destroy', $order) }}" method="POST"
                              onsubmit="return confirm('Chuyển đơn hàng này vào thùng rác?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-del"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding:40px;color:#aaa">
                    <i class="fas fa-inbox" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                    Chưa có đơn hàng nào.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:16px">{{ $orders->links('pagination::bootstrap-4') }}</div>
@endsection

@push('scripts')
<script>
    // Khi đổi trạng thái đơn hàng sang "Đã giao" (delivered),
    // tự động chuyển thanh toán sang "Đã thanh toán" (paid) trước khi gửi form.
    function handleOrderStatusChange(selectEl) {
        const form = selectEl.form;
        if (selectEl.value === 'delivered') {
            const paymentField = form.querySelector('.payment-status-field');
            if (paymentField) {
                paymentField.value = 'paid';
            }
        }
        form.submit();
    }
</script>
@endpush