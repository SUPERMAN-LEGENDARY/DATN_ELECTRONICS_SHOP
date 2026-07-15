<div class="card shadow-sm border-0">

    {{-- Avatar --}}
    <div class="card-body text-center py-4">

        <div class="avatar-circle mx-auto mb-3">
            {{ strtoupper(substr(auth()->user()->name,0,2)) }}
        </div>

        <h4 class="fw-bold mb-1">
            {{ auth()->user()->name }}
        </h4>

        <p class="text-muted mb-0">
            {{ auth()->user()->email }}
        </p>

    </div>

    {{-- Menu --}}
    <div class="list-group list-group-flush">

        <a href="{{ route('profile.account') }}"
            class="list-group-item list-group-item-action {{ request()->routeIs('profile.account') ? 'active' : '' }}">
            <i class="fas fa-user-circle me-2"></i>
            Thông tin tài khoản
        </a>

        <a href="{{ route('profile.order') }}"
            class="list-group-item list-group-item-action {{ request()->routeIs('profile.order') ? 'active' : '' }}">
            <i class="fas fa-shopping-bag me-2"></i>
            Đơn hàng
        </a>

        <a href="{{ route('profile.voucher') }}"
            class="list-group-item list-group-item-action {{ request()->routeIs('profile.voucher') ? 'active' : '' }}">
            <i class="fas fa-gift me-2"></i>
            Kho Voucher
        </a>

        <a href="{{ route('profile.review') }}"
            class="list-group-item list-group-item-action">
            <i class="far fa-star"></i>
            <span>Đánh giá của tôi</span>
        </a>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="list-group-item list-group-item-action text-danger w-100 text-start border-0 bg-white">
                Đăng xuất
            </button>
        </form>

    </div>

    <div class="card shadow-sm border-0 mt-4">

        <div class="card-body text-center">

            <div class="support-icon mb-3">
                <i class="fas fa-headset"></i>
            </div>

            <h5 class="fw-bold text-primary">
                Bạn cần hỗ trợ?
            </h5>

            <p class="text-muted mb-4">
                Liên hệ với chúng tôi để được hỗ trợ nhanh nhất.
            </p>

            <a href="{{ route('contact.index') }}"
                class="btn btn-primary w-100">
                Liên hệ ngay
            </a>

        </div>

    </div>

</div>

<style>
    .avatar-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: #2563eb;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        font-weight: 600;
        margin: 0 auto;
    }

    .card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .05);
    }

    .list-group-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 15px 20px;
        border: none;
        border-radius: 12px !important;
        margin: 4px 12px;
        font-size: 16px;
        font-weight: 500;
        color: #374151;
        transition: .25s;
    }

    .list-group-item i {
        width: 22px;
        text-align: center;
        color: #374151;
        font-size: 18px;
    }

    .list-group-item:hover {
        background: #eef4ff;
        color: #2563eb;
    }

    .list-group-item:hover i {
        color: #2563eb;
    }

    .list-group-item.active {
        background: #eef4ff;
        color: #2563eb;
        font-weight: 700;
    }

    .list-group-item.active i {
        color: #2563eb;
    }

    .list-group-item span {
        flex: 1;
    }

    button.list-group-item {
        cursor: pointer;
    }

    .text-danger {
        color: #ef4444 !important;
    }

    .card-body {
        padding: 20px;
    }

    .card-body h5 {
        font-size: 20px;
        margin-top: 12px;
        margin-bottom: 6px;
    }

    .card-body p {
        font-size: 15px;
        color: #6b7280;
        margin: 0;
    }

    /* =========================
   Desktop lớn
========================= */

    @media (min-width:1200px) {

        .avatar-circle {
            width: 90px;
            height: 90px;
            font-size: 34px;
        }

        .list-group-item {
            font-size: 18px;
            padding: 15px 20px;
        }

    }

    /* =========================
   iPad
========================= */

    @media (max-width:992px) {

        .avatar-circle {
            width: 75px;
            height: 75px;
            font-size: 28px;
        }

        .card-body {
            padding: 18px;
        }

        .card-body h5 {
            font-size: 18px;
        }

        .card-body p {
            font-size: 14px;
        }

        .list-group-item {
            font-size: 16px;
            padding: 13px 16px;
        }

        .list-group-item i {
            font-size: 17px;
        }

    }

    /* =========================
   Mobile
========================= */

    @media (max-width:768px) {

        .card {
            margin-bottom: 20px;
        }

        .avatar-circle {
            width: 65px;
            height: 65px;
            font-size: 24px;
        }

        .card-body {
            text-align: center;
            padding: 16px;
        }

        .card-body h5 {
            font-size: 17px;
        }

        .card-body p {
            font-size: 13px;
        }

        .list-group-item {
            padding: 12px 14px;
            font-size: 15px;
        }

        .list-group-item i {
            width: 20px;
            font-size: 16px;
        }

    }

    /* =========================
   Điện thoại nhỏ
========================= */

    @media (max-width:480px) {

        .avatar-circle {
            width: 58px;
            height: 58px;
            font-size: 22px;
        }

        .card-body {
            padding: 14px;
        }

        .card-body h5 {
            font-size: 16px;
        }

        .card-body p {
            font-size: 12px;
        }

        .list-group-item {
            padding: 11px 12px;
            font-size: 14px;
        }

        .list-group-item i {
            font-size: 15px;
        }

    }

    .support-icon {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: #eef4ff;
        color: #2563eb;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 32px;
        margin: auto;
    }

    .btn-primary {
        border-radius: 10px;
        padding: 10px;
        font-weight: 600;
    }
</style>