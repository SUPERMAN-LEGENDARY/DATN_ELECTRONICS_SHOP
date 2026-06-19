@extends('layouts.admin')
@section('title', 'Thùng rác mã giảm giá')

@push('styles')
<style>
.admin-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:12px; }
.admin-header h1 { font-size:20px; font-weight:800; }
.header-actions { display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
.btn-primary { background:#1565C0; color:#fff; border:none; padding:9px 18px; border-radius:6px; font-size:14px; font-weight:600; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
.btn-primary:hover { background:#0D47A1; }
.btn-secondary { background:#fff; color:#424242; border:1px solid #e0e0e0; padding:9px 18px; border-radius:6px; font-size:14px; font-weight:600; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
.btn-secondary:hover { background:#fafafa; border-color:#bdbdbd; }
.btn-restore { background:#2E7D32; color:#fff; border:none; padding:6px 12px; border-radius:4px; font-size:12px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:4px; }
.btn-restore:hover { background:#1B5E20; }
.btn-danger  { background:#E53935; color:#fff; border:none; padding:6px 12px; border-radius:4px; font-size:12px; font-weight:600; cursor:pointer; }
.btn-danger:hover { background:#C62828; }
.btn-danger-outline { background:#fff; color:#E53935; border:1px solid #E53935; padding:9px 18px; border-radius:6px; font-size:14px; font-weight:600; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
.btn-danger-outline:hover { background:#FFEBEE; }
.btn-danger-outline:disabled, .btn-restore:disabled { opacity:.5; cursor:not-allowed; }
.filter-bar { display:flex; gap:10px; margin-bottom:16px; flex-wrap:wrap; }
.filter-bar input { border:1px solid #e0e0e0; border-radius:6px; padding:8px 12px; font-size:13px; outline:none; }
.filter-bar button { background:#1565C0; color:#fff; border:none; border-radius:6px; padding:8px 16px; font-size:13px; cursor:pointer; }
.table-wrap { overflow-x:auto; }
table.data-table { width:100%; border-collapse:collapse; font-size:13px; }
.data-table th { background:#f5f5f5; padding:10px 12px; text-align:left; font-weight:700; border-bottom:2px solid #e0e0e0; white-space:nowrap; }
.data-table td { padding:10px 12px; border-bottom:1px solid #f0f0f0; vertical-align:middle; }
.data-table tr:hover td { background:#fafafa; }
.badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; }
.badge-success { background:#d4edda; color:#155724; }
.badge-danger { background:#f8d7da; color:#721c24; }
.badge-secondary { background:#e2e3e5; color:#383d41; }
.deleted-at { color:#888; font-size:12px; white-space:nowrap; }
.alert-success { background:#E8F5E9; border:1px solid #A5D6A7; color:#2E7D32; padding:10px 16px; border-radius:6px; margin-bottom:16px; font-size:14px; }
.empty-state { text-align:center; padding:50px 20px; color:#aaa; }
.empty-state i { font-size:36px; display:block; margin-bottom:10px; color:#ddd; }
</style>
@endpush

@section('content')
<div class="admin-header">
    <h1><i class="fas fa-trash-alt"></i> Thùng rác mã giảm giá</h1>
    <div class="header-actions">
        <a href="{{ route('admin.vouchers.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại danh sách
        </a>

        <form action="{{ route('admin.vouchers.restore-all') }}" method="POST"
              onsubmit="return confirm('Khôi phục tất cả voucher trong thùng rác?')">
            @csrf @method('PATCH')
            <button type="submit" class="btn-primary" {{ $vouchers->isEmpty() ? 'disabled' : '' }}>
                <i class="fas fa-undo"></i> Khôi phục tất cả
            </button>
        </form>

        <form action="{{ route('admin.vouchers.empty-trash') }}" method="POST"
              onsubmit="return confirm('Xóa VĨNH VIỄN toàn bộ voucher trong thùng rác? Hành động này không thể hoàn tác!')">
            @csrf @method('DELETE')
            <button type="submit" class="btn-danger-outline" {{ $vouchers->isEmpty() ? 'disabled' : '' }}>
                <i class="fas fa-broom"></i> Dọn thùng rác
            </button>
        </form>
    </div>
</div>

@if(session('success'))
<div class="alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

{{-- Thanh tìm kiếm --}}
<form class="filter-bar" method="GET" action="{{ route('admin.vouchers.trash') }}">
    <input type="text" name="q" placeholder="Tìm theo mã code..." value="{{ request('q') }}" style="flex:1;min-width:200px">
    <button type="submit"><i class="fas fa-search"></i> Tìm</button>
    @if(request('q'))
        <a href="{{ route('admin.vouchers.trash') }}" style="padding:8px 12px;font-size:13px;color:#666;text-decoration:none">Xóa bộ lọc</a>
    @endif
</form>

{{-- Bảng voucher đã xóa --}}
<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Mã</th>
                <th>Giảm (%)</th>
                <th>Đơn tối thiểu</th>
                <th>Đã dùng / Giới hạn</th>
                <th>Thời gian</th>
                <th>Trạng thái</th>
                <th>Đã xóa lúc</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vouchers as $voucher)
            <tr>
                <td style="color:#aaa">#{{ $voucher->id }}</td>
                <td><strong>{{ $voucher->code }}</strong></td>
                <td>{{ $voucher->discount_percent }}%</td>
                <td>{{ number_format($voucher->min_order_value) }}đ</td>
                <td>{{ $voucher->used_count }} / {{ $voucher->usage_limit }}</td>
                <td>
                    {{ \Carbon\Carbon::parse($voucher->starts_at)->format('d/m/Y') }}
                    →
                    {{ \Carbon\Carbon::parse($voucher->expires_at)->format('d/m/Y') }}
                </td>
                <td>
                    @if($voucher->is_active)
                        <span class="badge badge-success">Hoạt động</span>
                    @else
                        <span class="badge badge-danger">Vô hiệu</span>
                    @endif
                </td>
                <td class="deleted-at">
                    @if($voucher->deleted_at)
                        {{ $voucher->deleted_at->format('d/m/Y H:i') }}
                    @else
                        —
                    @endif
                </td>
                <td>
                    <div style="display:flex;gap:6px;align-items:center">
                        <form action="{{ route('admin.vouchers.restore', $voucher->id) }}" method="POST"
                              onsubmit="return confirm('Khôi phục voucher này?')">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-restore" title="Khôi phục">
                                <i class="fas fa-undo"></i> Khôi phục
                            </button>
                        </form>
                        <form action="{{ route('admin.vouchers.force-delete', $voucher->id) }}" method="POST"
                              onsubmit="return confirm('Xóa VĨNH VIỄN voucher này? Hành động này không thể hoàn tác!')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-danger" title="Xóa vĩnh viễn">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9">
                    <div class="empty-state">
                        <i class="fas fa-trash-alt"></i>
                        @if(request('q'))
                            Không tìm thấy voucher nào trong thùng rác khớp với "{{ request('q') }}".
                        @else
                            Thùng rác đang trống.
                        @endif
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:16px">{{ $vouchers->links() }}</div>
@endsection