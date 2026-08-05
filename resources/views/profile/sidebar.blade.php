<div class="card profile-sidebar-card mb-4">

    {{-- Avatar --}}
    <div class="card-body text-center py-4">

        <div class="avatar-circle mx-auto mb-3">
            {{ strtoupper(substr(auth()->user()->name,0,2)) }}
        </div>

        <h4 class="fw-bold mb-1 text-dark-custom">
            {{ auth()->user()->name }}
        </h4>

        <p class="text-muted-custom mb-0">
            {{ auth()->user()->email }}
        </p>

    </div>

    {{-- Menu --}}
    <div class="list-group list-group-flush pb-3">

        <a href="{{ route('profile.account') }}"
            class="list-group-item list-group-item-action {{ request()->routeIs('profile.account') ? 'active' : '' }}">
            <i class="fas fa-user-circle me-2"></i>
            <span>Thông tin tài khoản</span>
        </a>

        <a href="{{ route('profile.order') }}"
            class="list-group-item list-group-item-action {{ request()->routeIs('profile.order') ? 'active' : '' }}">
            <i class="fas fa-shopping-bag me-2"></i>
            <span>Đơn hàng</span>
        </a>

        <a href="{{ route('profile.voucher') }}"
            class="list-group-item list-group-item-action {{ request()->routeIs('profile.voucher') ? 'active' : '' }}">
            <i class="fas fa-ticket-alt me-2"></i>
            <span>Kho Voucher</span>
        </a>

        <a href="{{ route('profile.review') }}"
            class="list-group-item list-group-item-action {{ request()->routeIs('profile.review') ? 'active' : '' }}">
            <i class="fas fa-star me-2"></i>
            <span>Đánh giá của tôi</span>
        </a>

        <a href="{{ route('profile.wishlist') }}"
            class="list-group-item list-group-item-action {{ request()->routeIs('profile.wishlist') ? 'active' : '' }}">
            <i class="fas fa-heart"></i>
            <span>Sản phẩm yêu thích</span>
        </a>

        <form action="{{ route('logout') }}" method="POST" class="mt-2">
            @csrf
            <button class="list-group-item list-group-item-action btn-logout w-100 text-start">
                <i class="fas fa-sign-out-alt me-2"></i>
                <span>Đăng xuất</span>
            </button>
        </form>

    </div>

</div>

<div class="card profile-sidebar-card mb-4">
    <div class="card-body text-center py-4">

        <div class="support-icon mb-3">
            <i class="fas fa-headset"></i>
        </div>

        <h5 class="fw-bold text-dark-custom mb-2">
            Bạn cần hỗ trợ?
        </h5>

        <p class="text-muted-custom mb-4" style="font-size: 13.5px;">
            Liên hệ với chúng tôi để được hỗ trợ nhanh nhất.
        </p>

        <a href="{{ route('contact.index') }}" class="btn-support w-100">
            Liên hệ ngay
        </a>

    </div>
</div>

<style>
    /* ============================================================
       SAMSUNG WEB STYLES FOR SIDEBAR
       ============================================================ */
    
    .text-dark-custom { color: #000000; }
    .text-muted-custom { color: #555555; font-size: 14px; }

    .profile-sidebar-card {
        background: #ffffff;
        border: 1px solid #e5e5e5 !important;
        border-radius: 16px !important;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02) !important;
        transition: box-shadow 0.3s ease;
    }
    .profile-sidebar-card:hover {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05) !important;
    }

    .avatar-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: #111111; /* Đen nhám sang trọng */
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        font-weight: 700;
        margin: 0 auto;
        letter-spacing: 1px;
    }

    .list-group-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 20px;
        border: none;
        border-radius: 12px !important;
        margin: 4px 16px;
        font-size: 15px;
        font-weight: 600;
        color: #111111;
        background: transparent;
        transition: all 0.2s ease;
    }

    .list-group-item i {
        width: 22px;
        text-align: center;
        color: #9ca3af; /* Icon màu xám mặc định */
        font-size: 17px;
        transition: color 0.2s ease;
    }

    .list-group-item:hover {
        background: #f8f9fa;
        color: #e5e5e5; /* Samsung Blue */
    }

    .list-group-item:hover i {
        color: #e5e5e5;
    }

    .list-group-item.active {
        background: #f4f4f4;
        color: #e5e5e5;
        font-weight: 700;
    }

    .list-group-item.active i {
        color: #e5e5e5;
    }

    .list-group-item span {
        flex: 1;
    }

    button.list-group-item {
        cursor: pointer;
    }

    /* Nút Đăng xuất riêng biệt */
    .btn-logout {
        color: #ef4444;
    }
    .btn-logout i {
        color: #ef4444;
    }
    .btn-logout:hover {
        background: #fef2f2;
        color: #dc2626;
    }
    .btn-logout:hover i {
        color: #dc2626;
    }

    /* ============================================================
       SUPPORT BOX
       ============================================================ */
    .support-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #f4f4f4;
        color: #000000;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 24px;
        margin: auto;
        border: 1px solid #e5e5e5;
    }

    .btn-support {
        display: inline-block;
        background: #000000;
        color: #ffffff;
        border: none;
        border-radius: 24px; /* Bo cong viên thuốc */
        padding: 12px;
        font-weight: 700;
        font-size: 14.5px;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .btn-support:hover {
        background: #333333;
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
    }

    /* =========================
       RESPONSIVE
    ========================= */

    @media (min-width: 1200px) {
        .avatar-circle { width: 90px; height: 90px; font-size: 32px; }
        .list-group-item { font-size: 15.5px; padding: 15px 20px; }
    }

    @media (max-width: 992px) {
        .avatar-circle { width: 75px; height: 75px; font-size: 26px; }
        .list-group-item { font-size: 14.5px; padding: 13px 16px; margin: 4px 12px; }
    }

    @media (max-width: 768px) {
        .profile-sidebar-card { margin-bottom: 20px; }
        .avatar-circle { width: 65px; height: 65px; font-size: 22px; }
        .list-group-item { padding: 12px 14px; font-size: 14px; margin: 4px 10px; }
        .list-group-item i { width: 20px; font-size: 15px; }
        .support-icon { width: 50px; height: 50px; font-size: 20px; }
    }

    @media (max-width: 480px) {
        .avatar-circle { width: 58px; height: 58px; font-size: 20px; }
        .list-group-item { padding: 11px 12px; font-size: 14px; }
    }
</style>