@extends('layouts.admin')
@section('title', 'Quản lý mã giảm giá')

@push('styles')
<style>
    .table-container {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        padding: 16px 0;
        overflow-x: auto;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }
    .table th {
        background: #f8f9fc;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
        padding: 12px 16px;
        border-bottom: 2px solid #e9ecef;
        color: #495057;
        text-align: left;
        white-space: nowrap;
    }
    .table td {
        padding: 12px 16px;
        border-bottom: 1px solid #e9ecef;
        vertical-align: middle;
    }
    .table tbody tr:hover {
        background: #f8f9fc;
    }
    .badge {
        display: inline-block;
        padding: 4px 12px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 20px;
        line-height: 1.5;
        text-align: center;
        min-width: 60px;
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
        background: #e9ecef;
        color: #495057;
    }
    .btn-sm {
        font-size: 12px;
        padding: 4px 10px;
    }
    .btn-primary { background: #1565C0; color: #fff; }
    .btn-primary:hover { background: #0D47A1; }
    .btn-success { background: #28a745; color: #fff; }
    .btn-success:hover { background: #1e7e34; }
    .btn-danger { background: #dc3545; color: #fff; }
    .btn-danger:hover { background: #bd2130; }
    .btn-info { background: #17a2b8; color: #fff; }
    .btn-info:hover { background: #117a8b; }
    .btn-warning { background: #ffc107; color: #212529; }
    .btn-warning:hover { background: #e0a800; }
    .btn-secondary { background: #6c757d; color: #fff; }
    .btn-secondary:hover { background: #5a6268; }
    .btn-trash   { background:#fff; color:#757575; border:1px solid #e0e0e0; padding:9px 18px; border-radius:6px; font-size:14px; font-weight:600; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:6px; position:relative; }
    .btn-trash:hover { background:#fafafa; border-color:#bdbdbd; }
    .trash-badge { background:#E53935; color:#fff; border-radius:10px; font-size:11px; font-weight:700; padding:1px 7px; margin-left:4px; }

    .form-control {
        width: 100%;
        padding: 8px 12px;
        font-size: 14px;
        border: 1px solid #ced4da;
        border-radius: 6px;
        background: #fff;
        transition: border-color 0.15s;
    }
    .form-control:focus {
        border-color: #1565C0;
        outline: none;
        box-shadow: 0 0 0 0.2rem rgba(21, 101, 192, 0.25);
    }
    .row {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
    }
    .mb-3 { margin-bottom: 16px; }
    .mt-3 { margin-top: 16px; }
    .text-center { text-align: center; }
    .text-muted { color: #6c757d; font-size: 13px; }
    .pagination {
        display: flex;
        gap: 4px;
        padding: 16px 0;
        justify-content: center;
        list-style: none;
    }
    .pagination .page-item { list-style: none; }
    .pagination .page-link {
        display: block;
        padding: 6px 12px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        color: #1565C0;
        text-decoration: none;
        font-size: 14px;
        transition: 0.15s;
    }
    .pagination .page-link:hover { background: #e9ecef; }
    .pagination .active .page-link {
        background: #1565C0;
        color: #fff;
        border-color: #1565C0;
    }
    .pagination .disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background: #f8f9fa;
    }
    .table-responsive { overflow-x: auto; }
    .toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 16px;
    }
    .text-nowrap { white-space: nowrap; }
</style>
@endpush

@section('content')
<div class="container">
    <div class="toolbar">
    <h1>Quản lý mã giảm giá</h1>
    <div style="display:flex;gap:10px;align-items:center">
        <a href="{{ route('admin.vouchers.trash') }}" class="btn-trash">
            <i class="fas fa-trash-alt"></i> Thùng rác
            @if($trashedCount > 0)
                <span class="trash-badge">{{ $trashedCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.vouchers.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Thêm mã giảm giá
        </a>
    </div>
</div>

    <form method="GET" class="row mb-3">
        <div class="col-md-3">
            <input type="text" name="q" class="form-control" placeholder="Tìm theo mã code..." value="{{ request('q') }}">
        </div>
        <div class="col-md-2">
            <select name="status" class="form-control">
                <option value="">Tất cả trạng thái</option>
                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Hoạt động</option>
                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Vô hiệu</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Lọc</button>
            <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Mã</th>
                    <th>Giảm (%)</th>
                    <th>Đơn tối thiểu</th>
                    <th>Đã dùng / Giới hạn</th>
                    <th>Thời gian</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vouchers as $voucher)
                <tr>
                    <td>#{{ $voucher->id }}</td>
                    <td><strong>{{ $voucher->code }}</strong></td>
                    <td>{{ $voucher->discount_percent }}%</td>
                    <td>{{ number_format($voucher->min_order_value) }}đ</td>
                    <td>{{ $voucher->used_count }} / {{ $voucher->usage_limit }}</td>
                    <td class="text-nowrap">
                        {{ \Carbon\Carbon::parse($voucher->starts_at)->format('d/m/Y') }}
                        →
                        {{ \Carbon\Carbon::parse($voucher->expires_at)->format('d/m/Y') }}
                    </td>
                    <td>
                        @if($voucher->is_active)
                            <span class="badge bg-success">Hoạt động</span>
                        @else
                            <span class="badge bg-danger">Vô hiệu</span>
                        @endif
                    </td>
                    <td class="text-nowrap">
                        <a href="{{ route('admin.vouchers.edit', $voucher) }}" class="btn btn-sm btn-info">Sửa</a>
                        <form action="{{ route('admin.vouchers.toggle-active', $voucher) }}" method="POST" style="display:inline;">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-sm {{ $voucher->is_active ? 'btn-warning' : 'btn-success' }}">
                                {{ $voucher->is_active ? 'Vô hiệu' : 'Kích hoạt' }}
                            </button>
                        </form>
                        <form action="{{ route('admin.vouchers.destroy', $voucher) }}" method="POST" style="display:inline;" onsubmit="return confirm('Xóa mã giảm giá này?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">Chưa có mã giảm giá nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $vouchers->links() }}
</div>
@endsection