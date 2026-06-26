@extends('layouts.admin')
@section('title', 'Quản lý phân quyền')

@push('styles')
<style>
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px
    }

    .page-title {
        font-size: 20px;
        font-weight: 700;
        color: #1a1a1a
    }

    .filter-bar {
        background: #fff;
        border-radius: 10px;
        padding: 16px 20px;
        margin-bottom: 20px;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: flex-end;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .07)
    }

    .filter-bar select,
    .filter-bar input {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 7px;
        font-size: 13px;
        color: #333;
        outline: none;
        min-width: 160px
    }

    .filter-bar select:focus,
    .filter-bar input:focus {
        border-color: #1E88E5
    }

    .btn-filter {
        padding: 8px 18px;
        background: #1565C0;
        color: #fff;
        border: none;
        border-radius: 7px;
        font-size: 13px;
        cursor: pointer
    }

    .btn-reset {
        padding: 8px 14px;
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
        padding: 12px 16px;
        text-align: left;
        font-weight: 600;
        color: #555;
        border-bottom: 1px solid #eee;
        white-space: nowrap
    }

    tbody td {
        padding: 12px 16px;
        border-bottom: 1px solid #f4f4f4;
        vertical-align: middle
    }

    tbody tr:last-child td {
        border-bottom: none
    }

    tbody tr:hover {
        background: #fafbfc
    }

    .badge {
        display: inline-block;
        padding: 3px 10px;
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

    .role-form {
        display: flex;
        align-items: center;
        gap: 6px
    }

    .role-select {
        padding: 5px 8px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 13px;
        color: #333;
        outline: none;
        min-width: 110px
    }

    .role-select:focus {
        border-color: #1E88E5
    }

    .btn-sm {
        padding: 5px 12px;
        border: none;
        border-radius: 6px;
        font-size: 12px;
        cursor: pointer;
        font-weight: 600;
        transition: background .15s
    }

    .btn-primary-sm {
        background: #1565C0;
        color: #fff
    }

    .btn-primary-sm:hover {
        background: #0D47A1
    }

    .btn-success-sm {
        background: #2E7D32;
        color: #fff
    }

    .btn-success-sm:hover {
        background: #1B5E20
    }

    .btn-danger-sm {
        background: #C62828;
        color: #fff
    }

    .btn-danger-sm:hover {
        background: #B71C1C
    }

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
        margin-top: 1px
    }

    .pag {
        display: flex;
        gap: 6px;
        justify-content: center;
        padding: 16px
    }

    .pag a,
    .pag span {
        padding: 6px 12px;
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

    .stats-row {
        display: flex;
        gap: 14px;
        margin-bottom: 20px;
        flex-wrap: wrap
    }

    .stat-box {
        background: #fff;
        border-radius: 10px;
        padding: 16px 20px;
        flex: 1;
        min-width: 140px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .07);
        display: flex;
        align-items: center;
        gap: 14px
    }

    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px
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
        font-size: 22px;
        font-weight: 800;
        color: #1a1a1a
    }

    .stat-lbl {
        font-size: 12px;
        color: #888;
        margin-top: 1px
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-user-shield" style="color:#1E88E5"></i> Quản lý phân quyền</h1>
</div>

@if(session('success'))
<div class="alert-success" style="margin-bottom:16px"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert-error" style="margin-bottom:16px"><i class="fas fa-times-circle"></i> {{ session('error') }}</div>
@endif

{{-- Thống kê nhanh --}}
<div class="stats-row">
    <div class="stat-box">
        <div class="stat-icon blue"><i class="fas fa-users"></i></div>
        <div>
            <div class="stat-val">{{ \App\Models\User::count() }}</div>
            <div class="stat-lbl">Tổng tài khoản</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-icon orange"><i class="fas fa-user-cog"></i></div>
        <div>
            <div class="stat-val">{{ \App\Models\User::where('role','admin')->count() }}</div>
            <div class="stat-lbl">Quản trị viên</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-icon purple"><i class="fas fa-id-badge"></i></div>
        <div>
            <div class="stat-val">{{ \App\Models\User::where('role','staff')->count() }}</div>
            <div class="stat-lbl">Nhân viên</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-icon red"><i class="fas fa-ban"></i></div>
        <div>
            <div class="stat-val">{{ \App\Models\User::where('is_active',0)->count() }}</div>
            <div class="stat-lbl">Đã khoá</div>
        </div>
    </div>
</div>

{{-- Bộ lọc --}}
<form method="GET" class="filter-bar">
    <input type="text" name="search" placeholder="Tìm tên, email, SĐT…" value="{{ request('search') }}">
    <select name="role">
        <option value="">-- Tất cả role --</option>
        <option value="customer" @selected(request('role')==='customer' )>Khách hàng</option>
        <option value="staff" @selected(request('role')==='staff' )>Nhân viên</option>
        <option value="admin" @selected(request('role')==='admin' )>Quản trị viên</option>
    </select>
    <select name="status">
        <option value="">-- Tất cả trạng thái --</option>
        <option value="active" @selected(request('status')==='active' )>Đang hoạt động</option>
        <option value="locked" @selected(request('status')==='locked' )>Đã khoá</option>
    </select>
    <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Tìm kiếm</button>
    <a href="{{ route('admin.users.index') }}" class="btn-reset"><i class="fas fa-redo"></i> Đặt lại</a>
</form>

{{-- Bảng danh sách --}}
<div class="card">
    @if($users->isEmpty())
    <div class="empty"><i class="fas fa-users" style="font-size:40px;display:block;margin-bottom:10px"></i>Không tìm thấy tài khoản nào.</div>
    @else
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Người dùng</th>
                <th>Email</th>
                <th>Số điện thoại</th>
                <th>Trạng thái</th>
                <th>Đổi role</th>
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
                                @if($user->id === auth()->id())
                                <span style="font-size:11px;background:#E3F2FD;color:#1565C0;padding:1px 6px;border-radius:10px;margin-left:4px">Bạn</span>
                                @endif
                                @if($user->isFirstAdmin())
                                <span style="font-size:11px;background:#FFF3E0;color:#E65100;padding:1px 6px;border-radius:10px;margin-left:4px"><i class="fas fa-shield-alt"></i> Supper Admin</span>
                                @endif
                            </div>
                            <div class="user-meta">
                                <span class="badge badge-{{ $user->role }}">
                                    {{ $user->role === 'admin' ? 'Quản trị viên' : ($user->role === 'staff' ? 'Nhân viên' : 'Khách hàng') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </td>
                <td style="font-size:13px">{{ $user->email }}</td>
                <td style="font-size:13px;color:#666">{{ $user->phone ?: '—' }}</td>
                <td>
                    @if($user->is_active)
                    <span class="badge badge-active"><i class="fas fa-circle" style="font-size:9px"></i> Hoạt động</span>
                    @else
                    <span class="badge badge-locked"><i class="fas fa-lock" style="font-size:11px"></i> Đã khoá</span>
                    @endif
                </td>
                <td>
                    @if($user->id !== auth()->id() && ! $user->isFirstAdmin())
                    <form method="POST" action="{{ route('admin.users.update-role', $user) }}" class="role-form">
                        @csrf @method('PATCH')
                        <select name="role" class="role-select">
                            <option value="customer" @selected($user->role==='customer')>Khách hàng</option>
                            <option value="staff" @selected($user->role==='staff')>Nhân viên</option>
                            <option value="admin" @selected($user->role==='admin')>Quản trị viên</option>
                        </select>
                        <button type="submit" class="btn-sm btn-primary-sm">Lưu</button>
                    </form>
                    @elseif($user->isFirstAdmin())
                    <span style="font-size:12px;color:#aaa" title="Quản trị viên cấp cao nhất"><i class="fas fa-shield-alt"></i> Supper Admin</span>
                    @else
                    <span style="font-size:12px;color:#aaa">—</span>
                    @endif
                </td>
                <td>
                    <div style="display:flex;gap:6px;flex-wrap:wrap">
                        @if($user->isFirstAdmin() && $user->id !== auth()->id())
                        <span style="font-size:12px;color:#aaa">Không có quyền</span>
                        @else
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn-sm btn-primary-sm" style="text-decoration:none;display:inline-flex;align-items:center;gap:4px">
                            <i class="fas fa-edit"></i> Sửa
                        </a>
                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}" style="display:inline"
                            onsubmit="return confirm('{{ $user->is_active ? 'Khoá tài khoản này?' : 'Mở khoá tài khoản này?' }}')">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-sm {{ $user->is_active ? 'btn-danger-sm' : 'btn-success-sm' }}">
                                <i class="fas {{ $user->is_active ? 'fa-lock' : 'fa-lock-open' }}"></i>
                                {{ $user->is_active ? 'Khoá' : 'Mở khoá' }}
                            </button>
                        </form>
                        @else
                        <span style="font-size:12px;color:#aaa">—</span>
                        @endif
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($users->hasPages())
    <div class="pag">
        {!! $users->links() !!}
    </div>
    @endif
    @endif
</div>
@endsection