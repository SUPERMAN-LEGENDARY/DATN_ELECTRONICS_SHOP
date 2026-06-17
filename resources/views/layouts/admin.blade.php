<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - ElectronicShop</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Segoe UI', sans-serif; background: #f4f6f9; color: #333; display: flex; min-height: 100vh; }

    /* SIDEBAR */
    .sidebar { width: 240px; background: #0D1B2A; color: #fff; display: flex; flex-direction: column; position: fixed; top: 0; left: 0; height: 100vh; z-index: 100; }
    .sidebar-logo { padding: 20px 20px 16px; border-bottom: 1px solid rgba(255,255,255,.1); }
    .sidebar-logo a { color: #fff; text-decoration: none; font-size: 18px; font-weight: 800; display: flex; align-items: center; gap: 10px; }
    .sidebar-logo span { color: #1E88E5; }
    .sidebar-nav { flex: 1; padding: 12px 0; overflow-y: auto; }
    .nav-section { font-size: 10px; font-weight: 700; letter-spacing: 1px; color: rgba(255,255,255,.4); padding: 12px 20px 6px; text-transform: uppercase; }
    .nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 20px; color: rgba(255,255,255,.75); text-decoration: none; font-size: 14px; transition: all .15s; }
    .nav-item:hover, .nav-item.active { background: rgba(255,255,255,.1); color: #fff; }
    .nav-item.active { border-left: 3px solid #1E88E5; }
    .nav-item i { width: 18px; text-align: center; font-size: 14px; }
    .sidebar-footer { padding: 16px 20px; border-top: 1px solid rgba(255,255,255,.1); font-size: 13px; color: rgba(255,255,255,.5); }

    /* TOPBAR */
    .topbar { position: fixed; top: 0; left: 240px; right: 0; height: 56px; background: #fff; border-bottom: 1px solid #e0e0e0; display: flex; align-items: center; justify-content: space-between; padding: 0 24px; z-index: 99; }
    .topbar-left { font-size: 14px; color: #888; }
    .topbar-right { display: flex; align-items: center; gap: 16px; }
    .topbar-user { display: flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 600; }
    .topbar-user .avatar { width: 32px; height: 32px; background: #1565C0; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 13px; font-weight: 700; }
    .topbar-link { font-size: 13px; color: #666; text-decoration: none; }
    .topbar-link:hover { color: #1565C0; }

    /* MAIN */
    .main-wrap { margin-left: 240px; margin-top: 56px; flex: 1; padding: 24px; min-height: calc(100vh - 56px); }

    /* ALERTS */
    .alert-success { background:#E8F5E9; border:1px solid #A5D6A7; color:#2E7D32; padding:10px 16px; border-radius:6px; margin-bottom:16px; font-size:14px; }
    .alert-error   { background:#FFEBEE; border:1px solid #FFCDD2; color:#C62828; padding:10px 16px; border-radius:6px; margin-bottom:16px; font-size:14px; }
    </style>

    @stack('styles')
</head>
<body>

{{-- SIDEBAR --}}
<aside class="sidebar">
    <div class="sidebar-logo">
        <a href="{{ route('home') }}">
            <i class="fas fa-bolt"></i>
            Electronic<span>Shop</span>
        </a>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section">Tổng quan</div>
        <a href="{{ route('home') }}" class="nav-item">
            <i class="fas fa-store"></i> Xem cửa hàng
        </a>

        <div class="nav-section">Quản lý</div>
        <a href="{{ route('admin.products.index') }}"
           class="nav-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
            <i class="fas fa-box"></i> Sản phẩm
        </a>
        {{-- Thêm menu khác sau --}}
    </nav>
    <div class="sidebar-footer">
        Admin Panel v1.0
    </div>
</aside>

{{-- TOPBAR --}}
<div class="topbar">
    <div class="topbar-left">
        @yield('title', 'Dashboard')
    </div>
    <div class="topbar-right">
        <a href="{{ route('home') }}" class="topbar-link" target="_blank">
            <i class="fas fa-external-link-alt"></i> Cửa hàng
        </a>
        <div class="topbar-user">
            <div class="avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            {{ Auth::user()->name }}
        </div>
        <form method="POST" action="{{ route('logout') }}" style="display:inline">
            @csrf
            <button type="submit" style="background:none;border:none;color:#666;font-size:13px;cursor:pointer">
                <i class="fas fa-sign-out-alt"></i> Đăng xuất
            </button>
        </form>
    </div>
</div>

{{-- MAIN CONTENT --}}
<main class="main-wrap">
    @yield('content')
</main>

@stack('scripts')
</body>
</html>
