@extends('layouts.admin')
@section('title', 'Tạo tài khoản nhân viên')

@push('styles')
<style>
    .form-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .08);
        max-width: 640px;
        padding: 32px;
    }

    .breadcrumb {
        font-size: 13px;
        color: #888;
        margin-bottom: 18px;
    }

    .breadcrumb a {
        color: #1565C0;
        text-decoration: none;
    }

    .breadcrumb a:hover {
        text-decoration: underline;
    }

    .form-title {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #1a1a1a;
    }

    .form-row {
        display: flex;
        gap: 16px;
    }

    .form-row .form-group {
        flex: 1;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #444;
        margin-bottom: 6px;
    }

    .form-group label .req {
        color: #C62828;
        margin-left: 2px;
    }

    .form-control {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        transition: .15s;
        box-sizing: border-box;
    }

    .form-control:focus {
        outline: none;
        border-color: #1565C0;
        box-shadow: 0 0 0 3px rgba(21, 101, 192, .1);
    }

    .form-control.is-invalid {
        border-color: #C62828;
    }

    .invalid-feedback {
        color: #C62828;
        font-size: 12px;
        margin-top: 4px;
    }

    .form-hint {
        font-size: 12px;
        color: #999;
        margin-top: 5px;
    }

    .btn-save {
        padding: 10px 28px;
        background: #1565C0;
        color: #fff;
        border: none;
        border-radius: 7px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: .15s;
    }

    .btn-save:hover {
        background: #0D47A1;
    }

    .btn-cancel {
        padding: 10px 20px;
        background: #f5f5f5;
        color: #555;
        border: none;
        border-radius: 7px;
        font-size: 14px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-cancel:hover {
        background: #eee;
    }

    .section-sep {
        border: none;
        border-top: 1px solid #f0f0f0;
        margin: 24px 0;
    }

    .section-label {
        font-size: 12px;
        font-weight: 700;
        color: #aaa;
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: 16px;
    }
</style>
@endpush

@section('content')
<div class="breadcrumb">
    <a href="{{ route('admin.users.index', ['tab'=>'staff']) }}"><i class="fas fa-user-shield"></i> Quản lý tài khoản</a>
    <span style="margin:0 6px">›</span> Tạo nhân viên mới
</div>

<div class="form-card">
    <div class="form-title">
        <i class="fas fa-user-plus" style="color:#1565C0"></i> Tạo tài khoản nhân viên
    </div>

    @if($errors->any())
    <div style="background:#FFEBEE;border-radius:7px;padding:12px 16px;margin-bottom:20px;color:#C62828;font-size:13px">
        <i class="fas fa-exclamation-circle"></i>
        <ul style="margin:6px 0 0 16px;padding:0">
            @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf

        <div class="section-label">Thông tin cơ bản</div>

        <div class="form-row">
            <div class="form-group">
                <label>Họ và tên <span class="req">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name') }}" placeholder="Nguyễn Văn A">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Số điện thoại</label>
                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                    value="{{ old('phone') }}" placeholder="0912345678">
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-group">
            <label>Email <span class="req">*</span></label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}" placeholder="nhanvien@shop.com">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label>Vai trò <span class="req">*</span></label>
            <select name="role" class="form-control @error('role') is-invalid @enderror">
                <option value="staff" @selected(old('role','staff')==='staff' )>Nhân viên</option>
                <option value="admin" @selected(old('role')==='admin' )>Quản trị viên</option>
            </select>
            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <hr class="section-sep">
        <div class="section-label">Mật khẩu</div>

        <div class="form-row">
            <div class="form-group">
                <label>Mật khẩu <span class="req">*</span></label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                    placeholder="Tối thiểu 8 ký tự">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Xác nhận mật khẩu <span class="req">*</span></label>
                <input type="password" name="password_confirmation" class="form-control"
                    placeholder="Nhập lại mật khẩu">
            </div>
        </div>

        <div style="display:flex;gap:12px;align-items:center;margin-top:8px">
            <button type="submit" class="btn-save"><i class="fas fa-plus"></i> Tạo tài khoản</button>
            <a href="{{ route('admin.users.index', ['tab'=>'staff']) }}" class="btn-cancel">
                <i class="fas fa-arrow-left"></i> Huỷ
            </a>
        </div>
    </form>
</div>
@endsection