@extends('layouts.admin')
@section('title', 'Quản lý tài khoản')

@push('styles')
<style>
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px
    }

    .page-title {
        font-size: 20px;
        font-weight: 700;
        color: #1a1a1a
    }

    /* Tabs */
    .tab-bar {
        display: flex;
        gap: 4px;
        margin-bottom: 20px;
        border-bottom: 2px solid #e8eaf0;
    }

    .tab-item {
        padding: 10px 20px;
        font-size: 13px;
        font-weight: 600;
        color: #888;
        cursor: pointer;
        text-decoration: none;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
        border-radius: 6px 6px 0 0;
        transition: .15s;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .tab-item:hover {
        color: #1565C0;
        background: #f0f4fb;
    }

    .tab-item.active {
        color: #1565C0;
        border-bottom-color: #1565C0;
        background: #f0f4fb;
    }

    .tab-badge {
        background: #1565C0;
        color: #fff;
        border-radius: 10px;
        font-size: 11px;
        padding: 1px 7px;
        font-weight: 700;
    }

    .tab-badge.red {
        background: #C62828;
    }

    /* Stats */
    .stats-row {
        display: flex;
        gap: 14px;
        margin-bottom: 20px;
        flex-wrap: wrap
    }

    .stat-box {
        background: #fff;
        border-radius: 10px;
        padding: 14px 18px;
        flex: 1;
        min-width: 130px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .07);
        display: flex;
        align-items: center;
        gap: 12px
    }

    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px
    }

    .stat-icon.blue {
        background: #E3F2FD;
        color: #1565C0
    }

    .stat-icon.orange {
        background: #FFF3E0;
        color: #E65100
    }

    .stat-icon.purple {
        background: #F3E5F5;
        color: #6A1B9A
    }

    .stat-icon.red {
        background: #FFEBEE;
        color: #C62828
    }

    .stat-val {
        font-size: 20px;
        font-weight: 800;
        color: #1a1a1a
    }

    .stat-lbl {
        font-size: 12px;
        color: #888;
        margin-top: 1px
    }

    /* Filter */
    .filter-bar {
        background: #fff;
        border-radius: 10px;
        padding: 14px 18px;
        margin-bottom: 18px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: flex-end;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .07)
    }

    .filter-bar select,
    .filter-bar input {
        padding: 7px 11px;
        border: 1px solid #ddd;
        border-radius: 7px;
        font-size: 13px;
        color: #333;
        outline: none;
        min-width: 150px
    }

    .filter-bar select:focus,
    .filter-bar input:focus {
        border-color: #1E88E5
    }

    .btn-filter {
        padding: 7px 16px;
        background: #1565C0;
        color: #fff;
        border: none;
        border-radius: 7px;
        font-size: 13px;
        cursor: pointer
    }

    .btn-reset {
        padding: 7px 13px;
        background: #eee;
        color: #555;
        border: none;
        border-radius: 7px;
        font-size: 13px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px
    }

    /* Table */
    .card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .07);
        overflow: hidden
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px
    }

    thead th {
        background: #f8f9fb;
        padding: 11px 14px;
        text-align: left;
        font-weight: 600;
        color: #555;
        border-bottom: 1px solid #eee;
        white-space: nowrap
    }

    tbody td {
        padding: 11px 14px;
        border-bottom: 1px solid #f4f4f4;
        vertical-align: middle
    }

    tbody tr:last-child td {
        border-bottom: none
    }

    tbody tr:hover {
        background: #fafbfc
    }

    /* Badges */
    .badge {
        display: inline-block;
        padding: 3px 9px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600
    }

    .badge-admin {
        background: #FFF3E0;
        color: #E65100
    }

    .badge-staff {
        background: #E3F2FD;
        color: #1565C0
    }

    .badge-customer {
        background: #F3E5F5;
        color: #6A1B9A
    }

    .badge-active {
        background: #E8F5E9;
        color: #2E7D32
    }

    .badge-locked {
        background: #FFEBEE;
        color: #C62828
    }

    .badge-deleted {
        background: #f0f0f0;
        color: #888
    }

    /* Buttons */
    .btn-sm {
        padding: 5px 11px;
        border: none;
        border-radius: 6px;
        font-size: 12px;
        cursor: pointer;
        font-weight: 600;
        transition: .15s;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        text-decoration: none
    }

    .btn-primary-sm {
        background: #1565C0;
        color: #fff
    }

    .btn-primary-sm:hover {
        background: #0D47A1;
        color: #fff
    }

    .btn-success-sm {
        background: #2E7D32;
        color: #fff
    }

    .btn-success-sm:hover {
        background: #1B5E20
    }

    .btn-warning-sm {
        background: #F57F17;
        color: #fff
    }

    .btn-warning-sm:hover {
        background: #E65100
    }

    .btn-danger-sm {
        background: #C62828;
        color: #fff
    }

    .btn-danger-sm:hover {
        background: #B71C1C
    }

    .btn-gray-sm {
        background: #e0e0e0;
        color: #555
    }

    .btn-gray-sm:hover {
        background: #ccc
    }

    .btn-add {
        padding: 9px 18px;
        background: #1565C0;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px
    }

    .btn-add:hover {
        background: #0D47A1;
        color: #fff
    }

    /* Role form */
    .role-form {
        display: flex;
        align-items: center;
        gap: 5px
    }

    .role-select {
        padding: 5px 8px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 13px;
        color: #333;
        outline: none;
        min-width: 105px
    }

    .role-select:focus {
        border-color: #1E88E5
    }

    /* Avatar */
    .me-info {
        display: flex;
        align-items: center;
        gap: 8px
    }

    .avatar-sm {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #1565C0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        flex-shrink: 0
    }

    .user-meta {
        font-size: 12px;
        color: #888;
        margin-top: 2px
    }

    /* Pagination */
    .pag {
        display: flex;
        gap: 6px;
        justify-content: center;
        padding: 14px
    }

    .pag a,
    .pag span {
        padding: 5px 11px;
        border-radius: 6px;
        font-size: 13px;
        border: 1px solid #ddd;
        color: #555;
        text-decoration: none
    }

    .pag .active-page {
        background: #1565C0;
        color: #fff;
        border-color: #1565C0
    }

    .pag a:hover {
        background: #f0f4f8
    }

    .empty {
        text-align: center;
        padding: 48px;
        color: #aaa
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-user-shield" style="color:#1E88E5"></i> Quản lý tài khoản</h1>
    @if($tab === 'staff')
    <a href="{{ route('admin.users.create') }}" class="btn-add">
        <i class="fas fa-user-plus"></i> Thêm nhân viên
    </a>
    @endif
</div>

@if(session('success'))
<div class="alert-success" style="margin-bottom:16px"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert-error" style="margin-bottom:16px"><i class="fas fa-times-circle"></i> {{ session('error') }}</div>
@endif

{{-- Thống kê --}}
<div class="stats-row">
    <div class="stat-box">
        <div class="stat-icon blue"><i class="fas fa-id-badge"></i></div>
        <div>
            <div class="stat-val">{{ $stats['staff'] }}</div>
            <div class="stat-lbl">Nhân viên / Admin</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-icon purple"><i class="fas fa-users"></i></div>
        <div>
            <div class="stat-val">{{ $stats['customer'] }}</div>
            <div class="stat-lbl">Khách hàng</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-icon red"><i class="fas fa-ban"></i></div>
        <div>
            <div class="stat-val">{{ $stats['locked'] }}</div>
            <div class="stat-lbl">Đã khoá</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-icon orange"><i class="fas fa-trash"></i></div>
        <div>
            <div class="stat-val">{{ $stats['trash'] }}</div>
            <div class="stat-lbl">Thùng rác</div>
        </div>
    </div>
</div>

{{-- Tabs --}}
<div class="tab-bar">
    <a href="{{ route('admin.users.index', array_merge(request()->except('tab','page'), ['tab'=>'staff'])) }}"
        class="tab-item {{ $tab === 'staff' ? 'active' : '' }}">
        <i class="fas fa-id-badge"></i> Nhân viên &amp; Admin
        <span class="tab-badge">{{ $stats['staff'] }}</span>
    </a>
    <a href="{{ route('admin.users.index', array_merge(request()->except('tab','page'), ['tab'=>'customer'])) }}"
        class="tab-item {{ $tab === 'customer' ? 'active' : '' }}">
        <i class="fas fa-users"></i> Khách hàng
        <span class="tab-badge" style="background:#6A1B9A">{{ $stats['customer'] }}</span>
    </a>
    <a href="{{ route('admin.users.index', array_merge(request()->except('tab','page'), ['tab'=>'trash'])) }}"
        class="tab-item {{ $tab === 'trash' ? 'active' : '' }}">
        <i class="fas fa-trash-alt"></i> Thùng rác
        @if($stats['trash'] > 0)
        <span class="tab-badge red">{{ $stats['trash'] }}</span>
        @endif
    </a>
</div>

{{-- Bộ lọc (ẩn ở tab trash) --}}
@if($tab !== 'trash')
<form method="GET" class="filter-bar">
    <input type="hidden" name="tab" value="{{ $tab }}">
    <input type="text" name="search" placeholder="Tìm tên, email, SĐT…" value="{{ request('search') }}">
    <select name="status">
        <option value="">-- Tất cả trạng thái --</option>
        <option value="active" @selected(request('status')==='active' )>Đang hoạt động</option>
        <option value="locked" @selected(request('status')==='locked' )>Đã khoá</option>
    </select>
    <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Tìm kiếm</button>
    <a href="{{ route('admin.users.index', ['tab'=>$tab]) }}" class="btn-reset"><i class="fas fa-redo"></i> Đặt lại</a>
</form>
@endif

{{-- Bảng danh sách --}}
<div class="card">
    @if($users->isEmpty())
    <div class="empty">
        <i class="fas {{ $tab === 'trash' ? 'fa-trash' : 'fa-users' }}" style="font-size:38px;display:block;margin-bottom:10px"></i>
        {{ $tab === 'trash' ? 'Thùng rác trống.' : 'Không tìm thấy tài khoản nào.' }}
    </div>
    @else
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Người dùng</th>
                <th>Email</th>
                <th>Số điện thoại</th>
                @if($tab === 'trash')
                <th>Ngày xoá</th>
                @else
                <th>Trạng thái</th>
                @if($tab === 'staff')
                <th>Đổi role</th>
                @endif
                @endif
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td style="color:#aaa;font-size:13px">{{ $user->id }}</td>
                <td>
                    <div class="me-info">
                        <div class="avatar-sm">{{ strtoupper(substr($user->name,0,1)) }}</div>
                        <div>
                            <div style="font-weight:600">{{ $user->name }}
                                @if(!$user->trashed() && $user->id === auth()->id())
                                <span style="font-size:11px;background:#E3F2FD;color:#1565C0;padding:1px 6px;border-radius:10px;margin-left:4px">Bạn</span>
                                @endif
                                @if(!$user->trashed() && $user->isFirstAdmin())
                                <span style="font-size:11px;background:#FFF3E0;color:#E65100;padding:1px 6px;border-radius:10px;margin-left:4px"><i class="fas fa-shield-alt"></i> Super Admin</span>
                                @endif
                            </div>
                            <div class="user-meta">
                                <span class="badge badge-{{ $user->role }}">
                                    {{ $user->role === 'admin' ? 'Quản trị viên' : ($user->role === 'staff' ? 'Nhân viên' : 'Khách hàng') }}
                                </span>
                                @if($user->trashed())
                                <span class="badge badge-deleted" style="margin-left:4px"><i class="fas fa-trash" style="font-size:10px"></i> Đã xoá</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </td>
                <td style="font-size:13px">{{ $user->email }}</td>
                <td style="font-size:13px;color:#666">{{ $user->phone ?: '—' }}</td>

                @if($tab === 'trash')
                <td style="font-size:12px;color:#aaa">{{ $user->deleted_at->format('d/m/Y H:i') }}</td>
                @else
                <td>
                    @if($user->is_active)
                    <span class="badge badge-active"><i class="fas fa-circle" style="font-size:9px"></i> Hoạt động</span>
                    @else
                    <span class="badge badge-locked"><i class="fas fa-lock" style="font-size:11px"></i> Đã khoá</span>
                    @endif
                </td>
                @if($tab === 'staff')
                <td>
                    @if($user->id !== auth()->id() && !$user->isFirstAdmin())
                    <form method="POST" action="{{ route('admin.users.update-role', $user) }}" class="role-form">
                        @csrf @method('PATCH')
                        <select name="role" class="role-select">
                            <option value="staff" @selected($user->role==='staff')>Nhân viên</option>
                            <option value="admin" @selected($user->role==='admin')>Quản trị viên</option>
                        </select>
                        <button type="submit" class="btn-sm btn-primary-sm">Lưu</button>
                    </form>
                    @elseif($user->isFirstAdmin())
                    <span style="font-size:12px;color:#aaa"><i class="fas fa-shield-alt"></i> Super Admin</span>
                    @else
                    <span style="font-size:12px;color:#aaa">—</span>
                    @endif
                </td>
                @endif
                @endif

                <td>
                    <div style="display:flex;gap:5px;flex-wrap:wrap">
                        @if($tab === 'trash')
                        {{-- Thùng rác: khôi phục & xoá vĩnh viễn --}}
                        <form method="POST" action="{{ route('admin.users.restore', $user->id) }}" style="display:inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-sm btn-success-sm" title="Khôi phục">
                                <i class="fas fa-undo"></i> Khôi phục
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.users.force-delete', $user->id) }}" style="display:inline"
                            onsubmit="return confirm('Xoá vĩnh viễn tài khoản này? Hành động không thể hoàn tác!')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-sm btn-danger-sm" title="Xoá vĩnh viễn">
                                <i class="fas fa-times"></i> Xoá vĩnh viễn
                            </button>
                        </form>
                        @elseif($tab === 'staff')
                        {{-- Nhân viên/Admin: Sửa + Khoá + Xoá mềm --}}
                        @if($user->isFirstAdmin() && $user->id !== auth()->id())
                        <span style="font-size:12px;color:#aaa">Không có quyền</span>
                        @else
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn-sm btn-primary-sm">
                            <i class="fas fa-edit"></i> Sửa
                        </a>
                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}" style="display:inline"
                            onsubmit="return confirm('{{ $user->is_active ? 'Khoá tài khoản này?' : 'Mở khoá tài khoản này?' }}')">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-sm {{ $user->is_active ? 'btn-warning-sm' : 'btn-success-sm' }}">
                                <i class="fas {{ $user->is_active ? 'fa-lock' : 'fa-lock-open' }}"></i>
                                {{ $user->is_active ? 'Khoá' : 'Mở khoá' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display:inline"
                            onsubmit="return confirm('Xoá mềm tài khoản \" {{ $user->name }}\"? Có thể khôi phục sau.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-sm btn-danger-sm">
                                <i class="fas fa-trash"></i> Xoá
                            </button>
                        </form>
                        @endif
                        @endif
                        @else
                        {{-- Khách hàng: Sửa + Xoá mềm --}}
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn-sm btn-primary-sm">
                            <i class="fas fa-edit"></i> Sửa
                        </a>
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display:inline"
                            onsubmit="return confirm('Xoá mềm tài khoản khách hàng \" {{ $user->name }}\"?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-sm btn-danger-sm">
                                <i class="fas fa-trash"></i> Xoá
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($users->hasPages())
    <div class="pag">{!! $users->links() !!}</div>
    @endif
    @endif
</div>
@endsection