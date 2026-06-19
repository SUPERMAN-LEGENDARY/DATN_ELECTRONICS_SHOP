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
.table-wrap { overflow-x:auto; }
table.data-table { width:100%; border-collapse:collapse; font-size:13px; }
.data-table th { background:#f5f5f5; padding:10px 12px; text-align:left; font-weight:700; border-bottom:2px solid #e0e0e0; white-space:nowrap; }
.data-table td { padding:10px 12px; border-bottom:1px solid #f0f0f0; vertical-align:middle; }
.data-table tr:hover td { background:#fafafa; }
.badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; }
.badge-success { background:#d4edda; color:#155724; }
.badge-danger { background:#f8d7da; color:#721c24; }
.badge-warning { background:#fff3cd; color:#856404; }
.badge-info { background:#d1ecf1; color:#0c5460; }
.badge-secondary { background:#e2e3e5; color:#383d41; }
.alert-success { background:#E8F5E9; border:1px solid #A5D6A7; color:#2E7D32; padding:10px 16px; border-radius:6px; margin-bottom:16px; font-size:14px; }
.alert-danger { background:#f8d7da; border:1px solid #f5c6cb; color:#721c24; padding:10px 16px; border-radius:6px; margin-bottom:16px; font-size:14px; }
.text-center { text-align:center; }
.status-select {
    padding: 4px 8px;
    border-radius: 4px;
    border: 1px solid #ced4da;
    font-size: 12px;
    background: #fff;
    cursor: pointer;
    min-width: 110px;
}
.status-select:focus {
    border-color: #1565C0;
    outline: none;
}
.status-select:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}
</style>
@endpush

@section('content')
<div class="admin-header">
    <h1><i class="fas fa-shopping-cart"></i> Quản lý đơn hàng</h1>
    <div style="display:flex;gap:10px;align-items:center">
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

{{-- Thanh lọc --}}
<form class="filter-bar" method="GET" action="{{ route('admin.orders.index') }}">
    <input type="text" name="q" placeholder="Tìm theo ID hoặc tên khách hàng..." value="{{ request('q') }}" style="flex:1;min-width:160px">
    <select name="status">
        <option value="">Tất cả trạng thái</option>
        @foreach(['pending','confirmed','processing','shipped','delivered','cancelled','returned'] as $st)
            <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
        @endforeach
    </select>
    <select name="payment_status">
        <option value="">Tất cả thanh toán</option>
        <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Chưa thanh toán</option>
        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
        <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Hoàn tiền</option>
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
                <th>Trạng thái</th>
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
                <td>{{ number_format($order->total) }}đ</td>
                <td>
                    @php
                        // Phải khớp với $allowedTransitions trong OrderController@updateStatus
                        $allowedTransitions = [
                            'pending'    => ['confirmed', 'cancelled'],
                            'confirmed'  => ['processing', 'cancelled'],
                            'processing' => ['shipped', 'cancelled'],
                            'shipped'    => ['delivered', 'cancelled'],
                            'delivered'  => ['returned'],
                        ];
                        $isOrderLocked = in_array($order->status, ['cancelled', 'returned']);
                        $nextStatusOptions = $allowedTransitions[$order->status] ?? [];
                    @endphp
                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="status-form-inline">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="status-select" onchange="this.form.submit()"
                                @if($isOrderLocked) disabled @endif>
                            {{-- Trạng thái hiện tại luôn hiện sẵn --}}
                            <option value="{{ $order->status }}" selected>{{ ucfirst($order->status) }}</option>
                            {{-- Chỉ hiện các trạng thái được phép đi tiếp theo, không cho nhảy bậc --}}
                            @foreach($nextStatusOptions as $st)
                                <option value="{{ $st }}">{{ ucfirst($st) }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="payment_status" value="{{ $order->payment_status }}">
                    </form>
                </td>
                <td>
                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="status-form-inline">
                        @csrf
                        @method('PATCH')
                        <select name="payment_status" class="status-select" onchange="this.form.submit()"
                                @if(in_array($order->status, ['cancelled', 'returned']) || $order->payment_status !== 'unpaid') disabled @endif>
                            @if($order->payment_status === 'unpaid')
                                <option value="unpaid" selected>Chưa thanh toán</option>
                                <option value="paid">Đã thanh toán</option>
                            @elseif($order->payment_status === 'paid')
                                <option value="paid" selected>Đã thanh toán</option>
                            @else
                                <option value="refunded" selected>Hoàn tiền</option>
                            @endif
                        </select>
                        <input type="hidden" name="status" value="{{ $order->status }}">
                    </form>
                </td>
                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <div style="display:flex;gap:6px;align-items:center">
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn-info"><i class="fas fa-eye"></i></a>
                        <form action="{{ route('admin.orders.destroy', $order) }}" method="POST"
                              onsubmit="return confirm('Chuyển đơn hàng này vào thùng rác?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding:40px;color:#aaa">
                    Chưa có đơn hàng nào.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:16px">{{ $orders->links() }}</div>
@endsection

@push('scripts')
<script>
    // Tự động submit form khi thay đổi dropdown (đã dùng onchange)
</script>
@endpush