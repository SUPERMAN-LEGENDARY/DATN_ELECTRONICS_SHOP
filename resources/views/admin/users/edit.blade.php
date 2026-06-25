@extends('layouts.admin')

@section('title', 'Sửa thông tin tài khoản')

@push('styles')
<style>
    .form-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .08);
        max-width: 620px;
        padding: 28px;
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

    .form-control:disabled {
        background: #f5f5f5;
        color: #999;
        cursor: not-allowed;
    }

    .invalid-feedback {
        color: #C62828;
        font-size: 12px;
        margin-top: 4px;
    }

    .form-hint {
        font-size: 12px;
        color: #999;
        margin-top: 6px;
    }

    .section-divider {
        border-top: 1px dashed #e0e0e0;
        margin: 24px 0 20px;
        padding-top: 4px;
    }

    .section-label {
        font-size: 12px;
        font-weight: 700;
        color: #888;
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: 14px;
    }

    /* Toggle switch */
    .toggle-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .toggle-input {
        position: relative;
        width: 44px;
        height: 24px;
        flex-shrink: 0;
    }

    .toggle-input input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        inset: 0;
        background: #ccc;
        border-radius: 12px;
        cursor: pointer;
        transition: .2s;
    }

    .toggle-slider:before {
        content: '';
        position: absolute;
        width: 18px;
        height: 18px;
        left: 3px;
        top: 3px;
        background: #fff;
        border-radius: 50%;
        transition: .2s;
    }

    .toggle-input input:checked+.toggle-slider {
        background: #1565C0;
    }

    .toggle-input input:checked+.toggle-slider:before {
        transform: translateX(20px);
    }

    .toggle-input input:disabled+.toggle-slider {
        opacity: .5;
        cursor: not-allowed;
    }

    .toggle-label {
        font-size: 13px;
        color: #555;
    }

    .note-box {
        background: #FFF8E1;
        border: 1px solid #FFE082;
        color: #8D6E00;
        font-size: 12px;
        padding: 10px 14px;
        border-radius: 7px;
        margin-bottom: 20px;
        display: flex;
        gap: 8px;
        align-items: flex-start;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 28px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 20px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        text-decoration: none;
        transition: .15s;
    }

    .btn-primary {
        background: #1565C0;
        color: #fff;
    }

    .btn-primary:hover {
        background: #0D47A1;
    }

    .btn-outline {
        background: #fff;
        border: 1px solid #ddd;
        color: #444;
    }

    .btn-outline:hover {
        background: #f5f5f5;
    }
</style>
@endpush

@section('content')

{{-- Breadcrumb --}}
<div class="breadcrumb">
    <a href="{{ route('admin.users.index') }}">Quản lý tài khoản</a>
    &rsaquo; Sửa: {{ $user->name }}
</div>

<div class="form-card">
    <div class="form-title">
        <i class="fas fa-user-edit" style="color:#1565C0"></i>
        Sửa thông tin {{ $user->role === 'staff' ? 'nhân viên' : ($user->role === 'admin' ? 'quản trị viên' : 'khách hàng') }}
    </div>

    @if($user->id === auth()->id())
    <div class="note-box">
        <i class="fas fa-info-circle" style="margin-top:1px"></i>
        Bạn đang sửa tài khoản của chính mình nên không thể tự đổi role hoặc tự khoá tài khoản.
    </div>
    @endif

    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf
        @method('PUT')

        {{-- Họ tên --}}
        <div class="form-group">
            <label>Họ tên <span class="req">*</span></label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                class="form-control @error('name') is-invalid @enderror" placeholder="Nguyễn Văn A">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-row">
            {{-- Email --}}
            <div class="form-group">
                <label>Email <span class="req">*</span></label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                    class="form-control @error('email') is-invalid @enderror" placeholder="email@vidu.com">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- SĐT --}}
            <div class="form-group">
                <label>Số điện thoại</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                    class="form-control @error('phone') is-invalid @enderror" placeholder="09xxxxxxxx">
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="section-divider">
            <div class="section-label">Phân quyền &amp; trạng thái</div>
        </div>

        <div class="form-row">
            {{-- Role --}}
            <div class="form-group">
                <label>Vai trò</label>
                <select name="role" class="form-control @error('role') is-invalid @enderror"
                    {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                    <option value="customer" @selected(old('role', $user->role)==='customer')>Khách hàng</option>
                    <option value="staff" @selected(old('role', $user->role)==='staff')>Nhân viên</option>
                    <option value="admin" @selected(old('role', $user->role)==='admin')>Quản trị viên</option>
                </select>
                @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                @if($user->id === auth()->id())
                <div class="form-hint">Không thể tự đổi role của chính mình.</div>
                @endif
            </div>

            {{-- Trạng thái --}}
            <div class="form-group">
                <label>Trạng thái</label>
                <div class="toggle-wrap" style="margin-top:8px">
                    <label class="toggle-input">
                        <input type="checkbox" name="is_active" value="1"
                            {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                            {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                    <span class="toggle-label">Tài khoản đang hoạt động</span>
                </div>
                @if($user->id === auth()->id())
                <div class="form-hint">Không thể tự khoá tài khoản của chính mình.</div>
                @endif
            </div>
        </div>

        <div class="section-divider">
            <div class="section-label"><i class="fas fa-map-marker-alt"></i> Địa chỉ</div>
        </div>
        <div class="form-hint" style="margin-top:-10px;margin-bottom:16px">
            Địa chỉ mặc định của {{ $user->role === 'staff' ? 'nhân viên' : 'khách hàng' }} này. Để trống nếu chưa có hoặc không cần cập nhật.
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Tỉnh / Thành phố</label>
                <input type="text" name="address_province"
                    value="{{ old('address_province', $address->province ?? '') }}"
                    class="form-control @error('address_province') is-invalid @enderror"
                    placeholder="Ví dụ: Đà Nẵng">
                @error('address_province')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Quận / Huyện</label>
                <input type="text" name="address_district"
                    value="{{ old('address_district', $address->district ?? '') }}"
                    class="form-control @error('address_district') is-invalid @enderror"
                    placeholder="Ví dụ: Hải Châu">
                @error('address_district')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Phường / Xã</label>
                <input type="text" name="address_ward"
                    value="{{ old('address_ward', $address->ward ?? '') }}"
                    class="form-control @error('address_ward') is-invalid @enderror"
                    placeholder="Ví dụ: Phường Thanh Khê">
                @error('address_ward')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Số nhà, đường</label>
                <input type="text" name="address_street"
                    value="{{ old('address_street', $address->street ?? '') }}"
                    class="form-control @error('address_street') is-invalid @enderror"
                    placeholder="Ví dụ: 123 Nguyễn Văn Linh">
                @error('address_street')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- Nút --}}
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Cập nhật
            </button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </form>
</div>

@endsection