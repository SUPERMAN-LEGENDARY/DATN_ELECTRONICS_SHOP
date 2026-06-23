{{-- ============================================ --}}
{{-- FILE: resources/views/pages/account/profile.blade.php --}}
{{-- ============================================ --}}
@extends('layouts.app')
@section('title', 'Tài khoản của tôi - ElectronicShop')
@php $showSearch = true; @endphp

@push('styles')
<style>
.account-page { max-width: 1200px; margin: 0 auto; padding: 16px 16px 48px; }
.account-layout { display: grid; grid-template-columns: 220px 1fr; gap: 24px; align-items: start; }

/* SIDEBAR */
.account-sidebar { background: #fff; border: 1px solid #e0e0e0; border-radius: 10px; overflow: hidden; }
.account-user { padding: 20px; border-bottom: 1px solid #f0f0f0; display: flex; align-items: center; gap: 12px; }
.account-avatar {
    width: 52px; height: 52px; border-radius: 50%; background: linear-gradient(135deg,#1565C0,#42a5f5);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 22px; font-weight: 700; flex-shrink: 0;
}
.account-user-info .name { font-size: 15px; font-weight: 700; margin-bottom: 2px; }
.account-user-info .badge { font-size: 11px; background: #EBF3FF; color: #1565C0; padding: 2px 8px; border-radius: 10px; font-weight: 600; }
.account-nav { padding: 8px 0; }
.account-nav a {
    display: flex; align-items: center; gap: 10px; padding: 11px 20px;
    font-size: 14px; color: #444; font-weight: 500; transition: all .15s;
}
.account-nav a:hover { background: #f5f5f5; color: #1565C0; }
.account-nav a.active { background: #EBF3FF; color: #1565C0; font-weight: 600; border-left: 3px solid #1565C0; }
.account-nav a i { width: 18px; text-align: center; color: inherit; }
.account-nav .nav-divider { border: none; border-top: 1px solid #f0f0f0; margin: 6px 0; }
.account-nav a.logout { color: #E53935; }
.account-nav a.logout:hover { background: #FFEBEE; }

/* MAIN */
.account-main { display: flex; flex-direction: column; gap: 20px; }
.account-card { background: #fff; border: 1px solid #e0e0e0; border-radius: 10px; padding: 26px; }
.account-card-title { font-size: 17px; font-weight: 800; margin-bottom: 6px; }
.account-card-sub { font-size: 13px; color: #888; margin-bottom: 22px; }

/* PROFILE FORM */
.profile-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
.pf-group label { display: block; font-size: 12px; font-weight: 600; color: #555; margin-bottom: 5px; text-transform: uppercase; letter-spacing: .3px; }
.pf-group input, .pf-group select {
    width: 100%; border: 1px solid #e0e0e0; border-radius: 6px;
    padding: 10px 12px; font-size: 14px; outline: none; font-family: inherit;
    transition: border-color .15s; color: #333;
}
.pf-group input:focus, .pf-group select:focus { border-color: #1565C0; }
.gender-row { display: flex; gap: 20px; align-items: center; padding-top: 4px; }
.gender-row label { display: flex; align-items: center; gap: 6px; font-size: 14px; cursor: pointer; }
.gender-row input { accent-color: #1565C0; }
.btn-save {
    background: #1565C0; color: #fff; border: none; border-radius: 6px;
    padding: 11px 28px; font-size: 14px; font-weight: 700; cursor: pointer;
    transition: background .2s; display: block; margin-left: auto;
}
.btn-save:hover { background: #0D47A1; }

/* ADDRESS */
.address-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px; }
.btn-add-addr {
    display: inline-flex; align-items: center; gap: 6px; border: 1.5px solid #1565C0;
    color: #1565C0; background: #fff; border-radius: 6px; padding: 8px 16px;
    font-size: 13px; font-weight: 600; cursor: pointer; transition: all .15s;
}
.btn-add-addr:hover { background: #EBF3FF; }
.address-list { display: flex; flex-direction: column; gap: 10px; margin-top: 14px; }
.address-item {
    border: 1.5px solid #e0e0e0; border-radius: 8px; padding: 14px 16px;
    display: flex; align-items: flex-start; gap: 14px;
}
.address-item.default { border-color: #1565C0; background: #f8fbff; }
.addr-icon { color: #1565C0; font-size: 18px; margin-top: 2px; }
.addr-type { font-size: 13px; font-weight: 700; margin-bottom: 4px; }
.addr-badge {
    display: inline-block; font-size: 10px; font-weight: 700; color: #1565C0;
    background: #EBF3FF; padding: 2px 8px; border-radius: 10px; margin-left: 6px; text-transform: uppercase;
}
.addr-name { font-size: 14px; font-weight: 600; margin-bottom: 2px; }
.addr-phone { font-size: 13px; color: #777; margin-bottom: 2px; }
.addr-text { font-size: 13px; color: #555; }
.addr-actions { margin-left: auto; display: flex; gap: 12px; white-space: nowrap; }
.addr-actions a { font-size: 13px; font-weight: 500; }
.addr-edit { color: #1565C0; }
.addr-del { color: #E53935; }

/* STATS */
.stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
.stat-card {
    border: 1px solid #e0e0e0; border-radius: 8px; padding: 18px 14px; text-align: center;
}
.stat-icon { font-size: 28px; margin-bottom: 8px; }
.stat-num { font-size: 32px; font-weight: 800; color: #1a1a1a; margin-bottom: 4px; }
.stat-label { font-size: 12px; color: #888; margin-bottom: 8px; }
.stat-link { font-size: 12px; color: #1565C0; font-weight: 500; }
</style>
@endpush

@section('content')
<div class="account-page">
    <div class="breadcrumb">
        <a href="{{ route('home') }}">Trang chủ</a>
        <span>›</span>
        <a href="{{ route('account.profile') }}">Tài khoản</a>
        <span>›</span>
        <span>Thông tin cá nhân</span>
    </div>

    <div class="account-layout">
        {{-- SIDEBAR --}}
        <aside class="account-sidebar">
            <div class="account-user">
                <div class="account-avatar">
                    {{ strtoupper(substr(auth()->user()->name ?? 'N', 0, 1)) }}
                </div>
                <div class="account-user-info">
                    <div class="name">{{ auth()->user()->name ?? 'Nguyễn Văn A' }}</div>
                    <span class="badge">Thành viên</span>
                </div>
            </div>
            <nav class="account-nav">
                <a href="{{ route('account.profile') }}" class="{{ request()->routeIs('account.profile') ? 'active' : '' }}">
                    <i class="fas fa-user"></i> Thông tin cá nhân
                </a>
                <a href="{{ route('account.addresses') ?? '#' }}" class="{{ request()->routeIs('account.addresses') ? 'active' : '' }}">
                    <i class="fas fa-map-marker-alt"></i> Quản lý địa chỉ
                </a>
                <a href="{{ route('account.orders') ?? '#' }}" class="{{ request()->routeIs('account.orders') ? 'active' : '' }}">
                    <i class="fas fa-shopping-bag"></i> Đơn hàng của tôi
                </a>
                <a href="{{ route('wishlist') ?? '#' }}" class="{{ request()->routeIs('wishlist') ? 'active' : '' }}">
                    <i class="fas fa-heart"></i> Sản phẩm yêu thích
                </a>
                <a href="{{ route('account.reviews') ?? '#' }}" class="{{ request()->routeIs('account.reviews') ? 'active' : '' }}">
                    <i class="fas fa-star"></i> Đánh giá của tôi
                </a>
                <a href="{{ route('account.notifications') ?? '#' }}" class="{{ request()->routeIs('account.notifications') ? 'active' : '' }}">
                    <i class="fas fa-bell"></i> Thông báo
                </a>
                <hr class="nav-divider">
                <a href="{{ route('account.password') ?? '#' }}">
                    <i class="fas fa-lock"></i> Đổi mật khẩu
                </a>
                <a href="{{ route('logout') }}" class="logout"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit()">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
            </nav>
        </aside>

        {{-- MAIN --}}
        <div class="account-main">

            {{-- PROFILE FORM --}}
            <div class="account-card">
                <div class="account-card-title">Thông tin cá nhân</div>
                <div class="account-card-sub">Cập nhật thông tin cá nhân để quản lý tài khoản dễ dàng hơn.</div>

                @if(session('success'))
                <div style="background:#E8F5E9;border:1px solid #C8E6C9;border-radius:6px;padding:10px 14px;margin-bottom:16px;color:#2E7D32;font-size:13px">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
                @endif

                <form action="{{ route('account.profile.update') ?? '#' }}" method="POST">
                    @csrf @method('PUT')
                    <div class="profile-form-grid">
                        <div class="pf-group">
                            <label>Họ và tên</label>
                            <input type="text" name="name" value="{{ old('name', auth()->user()->name ?? 'Nguyễn Văn A') }}">
                        </div>
                        <div class="pf-group">
                            <label>Giới tính</label>
                            <div class="gender-row">
                                <label><input type="radio" name="gender" value="male" {{ (auth()->user()->gender ?? 'male') === 'male' ? 'checked' : '' }}> Nam</label>
                                <label><input type="radio" name="gender" value="female" {{ (auth()->user()->gender ?? '') === 'female' ? 'checked' : '' }}> Nữ</label>
                                <label><input type="radio" name="gender" value="other" {{ (auth()->user()->gender ?? '') === 'other' ? 'checked' : '' }}> Khác</label>
                            </div>
                        </div>
                    </div>
                    <div class="profile-form-grid">
                        <div class="pf-group">
                            <label>Email</label>
                            <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? 'nguyenvana@gmail.com') }}">
                        </div>
                        <div class="pf-group">
                            <label>Địa chỉ</label>
                            <input type="text" name="address" value="{{ old('address', auth()->user()->address ?? '123 Đường Công Nghệ, Phường Tân Thuận') }}">
                        </div>
                    </div>
                    <div class="profile-form-grid">
                        <div class="pf-group">
                            <label>Số điện thoại</label>
                            <input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone ?? '0123 456 789') }}">
                        </div>
                        <div class="pf-group">
                            <label>Quận / Huyện</label>
                            <input type="text" name="district" value="{{ old('district', auth()->user()->district ?? 'Quận 7') }}">
                        </div>
                    </div>
                    <div class="profile-form-grid" style="margin-bottom:20px">
                        <div class="pf-group">
                            <label>Ngày sinh</label>
                            <input type="date" name="birthday" value="{{ old('birthday', auth()->user()->birthday ?? '1995-05-15') }}">
                        </div>
                        <div class="pf-group">
                            <label>Thành phố</label>
                            <input type="text" name="city" value="{{ old('city', auth()->user()->city ?? 'TP. Hồ Chí Minh') }}">
                        </div>
                    </div>
                    <button type="submit" class="btn-save">Lưu thay đổi</button>
                </form>
            </div>

            {{-- ADDRESS BOOK --}}
            <div class="account-card">
                <div class="address-header">
                    <div>
                        <div class="account-card-title">Sổ địa chỉ của tôi</div>
                        <div class="account-card-sub" style="margin-bottom:0">Quản lý và cập nhật các địa chỉ giao hàng của bạn.</div>
                    </div>
                    <button class="btn-add-addr"><i class="fas fa-plus"></i> Thêm địa chỉ mới</button>
                </div>
                <div class="address-list">
                    @forelse(auth()->user()->addresses ?? [] as $addr)
                    <div class="address-item {{ $addr->is_default ? 'default' : '' }}">
                        <i class="fas {{ $addr->type === 'company' ? 'fa-building' : 'fa-home' }} addr-icon"></i>
                        <div style="flex:1">
                            <div class="addr-type">{{ $addr->type === 'company' ? 'Công ty' : 'Nhà riêng' }}
                                @if($addr->is_default)<span class="addr-badge">Mặc định</span>@endif
                            </div>
                            <div class="addr-name">{{ $addr->name }}</div>
                            <div class="addr-phone">{{ $addr->phone }}</div>
                            <div class="addr-text">{{ $addr->full_address }}</div>
                        </div>
                        <div class="addr-actions">
                            <a href="#" class="addr-edit"><i class="fas fa-pen"></i> Sửa</a>
                            <a href="#" class="addr-del"><i class="fas fa-times"></i> Xóa</a>
                        </div>
                    </div>
                    @empty
                    {{-- Demo data --}}
                    <div class="address-item default">
                        <i class="fas fa-home addr-icon"></i>
                        <div style="flex:1">
                            <div class="addr-type">Nhà riêng <span class="addr-badge">Mặc định</span></div>
                            <div class="addr-name">Nguyễn Văn A &nbsp;|&nbsp; <span style="color:#777">0123 456 789</span></div>
                            <div class="addr-text">123 Đường Công Nghệ, Phường Tân Thuận, Quận 7, TP. Hồ Chí Minh</div>
                        </div>
                        <div class="addr-actions">
                            <a href="#" class="addr-edit"><i class="fas fa-pen"></i> Sửa</a>
                            <a href="#" class="addr-del"><i class="fas fa-times"></i> Xóa</a>
                        </div>
                    </div>
                    <div class="address-item">
                        <i class="fas fa-building addr-icon"></i>
                        <div style="flex:1">
                            <div class="addr-type">Công ty</div>
                            <div class="addr-name">Nguyễn Văn A &nbsp;|&nbsp; <span style="color:#777">0123 456 789</span></div>
                            <div class="addr-text">Tòa nhà Viettel, 285 Cách Mạng Tháng 8, Phường 12, Quận 10, TP. Hồ Chí Minh</div>
                        </div>
                        <div class="addr-actions">
                            <a href="#" class="addr-edit"><i class="fas fa-pen"></i> Sửa</a>
                            <a href="#" class="addr-del"><i class="fas fa-times"></i> Xóa</a>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- ACCOUNT STATS --}}
            <div class="account-card">
                <div class="account-card-title" style="margin-bottom:18px">Tổng quan tài khoản</div>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">🛍️</div>
                        <div class="stat-num">{{ auth()->user()->orders_count ?? 12 }}</div>
                        <div class="stat-label">Đơn hàng</div>
                        <a href="#" class="stat-link">Xem tất cả</a>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">❤️</div>
                        <div class="stat-num">{{ auth()->user()->wishlist_count ?? 8 }}</div>
                        <div class="stat-label">Yêu thích</div>
                        <a href="#" class="stat-link">Xem tất cả</a>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">⭐</div>
                        <div class="stat-num">{{ auth()->user()->reviews_count ?? 15 }}</div>
                        <div class="stat-label">Đánh giá</div>
                        <a href="#" class="stat-link">Xem tất cả</a>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">🏷️</div>
                        <div class="stat-num">{{ auth()->user()->coupons_count ?? 2 }}</div>
                        <div class="stat-label">Mã giảm giá</div>
                        <a href="#" class="stat-link">Xem tất cả</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
