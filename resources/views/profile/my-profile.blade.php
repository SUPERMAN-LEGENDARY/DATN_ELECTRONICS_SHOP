@extends('layouts.app')

@section('title', 'Hồ sơ - ' . config('app.name', 'ElectronicShop'))

@section('content')
    @push('styles')
    <style>
    /* ============================================================
       LAYOUT 1 CỘT — ĐEN TRẮNG
       ============================================================ */
    .profile-page {
        padding: 32px 0 60px;
    }
    .profile-container {
        max-width: 980px; margin: 0 auto;
        padding: 0 16px;
    }

    /* ============================================================
       BANNER CHÀO MỪNG
       ============================================================ */
    .profile-hero {
        /*
          - Lớp phủ tối (Overlay): Giúp chữ màu trắng hiển thị rõ ràng hơn trên ảnh nền.
          - Thay 'URL_ANH_CUA_BAN_TAI_DAY' bằng link ảnh thực tế của bạn.
        */
        background: 
            linear-gradient(120deg, rgba(0, 0, 0, 0.75) 0%, rgba(0, 0, 0, 0.45) 55%, rgba(0, 0, 0, 0.7) 100%),
            url('https://images.unsplash.com/photo-1550745165-9bc0b252726f?q=80&w=1200&auto=format&fit=crop') center / cover no-repeat;
        background-color: #1a1a1a; /* Màu nền dự phòng nếu link ảnh hỏng/chưa load kịp */
        border-radius: var(--sm-radius, 16px);
        padding: 32px 32px 28px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 22px;
        flex-wrap: wrap;
        color: #fff;
        position: relative;
        overflow: hidden;
        min-height: 148px;
    }
    .profile-hero-avatar {
        width: 64px; height: 64px; border-radius: 50%;
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.25);
        display: flex; align-items: center; justify-content: center;
        font-family: 'Manrope', sans-serif; font-weight: 800; font-size: 22px;
        color: #fff; letter-spacing: .5px; flex-shrink: 0;
    }
    .profile-hero-info { flex: 1; min-width: 200px; }
    .profile-hero-info h3 {
        font-size: 20px; margin: 0 0 4px; color: #fff;
        font-family: 'Manrope', sans-serif; font-weight: 800;
    }
    .profile-hero-info p { margin: 0; font-size: 13.5px; color: rgba(255,255,255,.6); }
    .profile-hero-stats { display: flex; gap: 28px; flex-wrap: wrap; }
    .profile-hero-stat { text-align: center; min-width: 64px; }
    .profile-hero-stat b {
        display: block; font-size: 22px; font-family: 'Manrope', sans-serif; font-weight: 800; color: #fff;
    }
    .profile-hero-stat span { font-size: 11.5px; color: rgba(255,255,255,.55); }

    /* ============================================================
       SECTION LABEL
       ============================================================ */
    .profile-section-label {
        font-size: 13px; font-weight: 700; letter-spacing: .04em;
        color: #999; text-transform: uppercase;
        margin: 28px 2px 12px;
    }

    /* ============================================================
       LƯỚI THẺ CHỨC NĂNG (thay cho menu sidebar) — DESKTOP/TABLET
       ============================================================ */
    .quick-grid {
        display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;
    }
    @media (max-width: 768px) { .quick-grid { grid-template-columns: repeat(2, 1fr); } }

    .quick-card {
        background: #fff; border: 1px solid #e5e5e5; border-radius: 16px;
        padding: 22px 18px;
        transition: box-shadow .25s ease, transform .25s ease, border-color .25s ease;
        color: #111; cursor: pointer;
        font: inherit; width: 100%;
        display: flex; flex-direction: column; align-items: center; text-align: center;
    }
    .quick-card:hover {
        box-shadow: 0 8px 22px rgba(0,0,0,.06); transform: translateY(-2px);
        border-color: #d0d0d0; color: #111;
    }
    .quick-card-icon {
        width: 44px; height: 44px; border-radius: 50%; background: #f4f4f4;
        display: flex; align-items: center; justify-content: center;
        margin: 0 0 12px; font-size: 18px; color: #111; flex-shrink: 0;
    }
    .quick-card-text h6 {
        font-size: 14.5px; font-weight: 700; margin: 0 0 4px; color: #111;
    }
    .quick-card-text p { font-size: 12px; color: #888; margin: 0; line-height: 1.4; }
    .quick-card-arrow { display: none; }

    /* ============================================================
       BẢN MOBILE — danh sách hàng ngang, kiểu app
       ============================================================ */
    @media (max-width: 576px) {
        .profile-page { padding: 16px 0 40px; }
        .profile-container { padding: 0 12px; }

        /* Banner: căn giữa như app di động */
        .profile-hero {
            flex-direction: column;
            text-align: center;
            padding: 26px 20px 22px;
            gap: 12px;
        }
        .profile-hero-info { width: 100%; }
        .profile-hero-stats { width: 100%; justify-content: space-around; gap: 8px; margin-top: 4px; }
        .profile-hero-stat { min-width: 0; }

        .profile-section-label { margin: 22px 4px 8px; font-size: 12px; }

        /* Menu: chuyển từ lưới thẻ sang danh sách hàng ngang */
        .quick-grid {
            display: block;
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 16px;
            overflow: hidden;
        }
        .quick-card {
            flex-direction: row;
            align-items: center;
            text-align: left;
            border: none;
            border-bottom: 1px solid #f0f0f0;
            border-radius: 0;
            padding: 14px 16px;
            box-shadow: none !important;
            transform: none !important;
        }
        .quick-grid .quick-card:last-child { border-bottom: none; }
        .quick-card-icon { margin: 0 14px 0 0; width: 36px; height: 36px; font-size: 15px; }
        .quick-card-text { flex: 1; min-width: 0; }
        .quick-card-text p { display: none; }
        .quick-card-arrow {
            display: block; color: #c2c2c2; font-size: 13px; margin-left: 8px; flex-shrink: 0;
        }
    }
    </style>
    @endpush

    <div class="profile-page">
        <div class="profile-container">

            {{-- Banner chào mừng --}}
            <div class="profile-hero">
                <div class="profile-hero-avatar">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div class="profile-hero-info">
                    <h3>Xin chào, {{ $user->name }}</h3>
                    <p>{{ $user->email }}</p>
                </div>
                <div class="profile-hero-stats">
                    <div class="profile-hero-stat">
                        <b>{{ $totalOrders }}</b>
                        <span>Tổng đơn</span>
                    </div>
                    <div class="profile-hero-stat">
                        <b>{{ $processingOrders }}</b>
                        <span>Đang xử lý</span>
                    </div>
                    <div class="profile-hero-stat">
                        <b>{{ $completedOrders }}</b>
                        <span>Hoàn thành</span>
                    </div>
                </div>
            </div>

            {{-- Menu dạng thẻ (mobile: dạng danh sách) --}}
            <div class="profile-section-label">Quản lý tài khoản</div>
            <div class="quick-grid">

                <a href="{{ route('profile.account') }}" class="quick-card">
                    <div class="quick-card-icon"><i class="fas fa-user-circle"></i></div>
                    <div class="quick-card-text">
                        <h6>Thông tin tài khoản</h6>
                        <p>Cập nhật hồ sơ và sổ địa chỉ</p>
                    </div>
                    <span class="quick-card-arrow"><i class="fas fa-chevron-right"></i></span>
                </a>

                <a href="{{ route('profile.order') }}" class="quick-card">
                    <div class="quick-card-icon"><i class="fas fa-shopping-bag"></i></div>
                    <div class="quick-card-text">
                        <h6>Đơn hàng của tôi</h6>
                        <p>Theo dõi, hủy hoặc đánh giá đơn hàng</p>
                    </div>
                    <span class="quick-card-arrow"><i class="fas fa-chevron-right"></i></span>
                </a>

                <a href="{{ route('profile.voucher') }}" class="quick-card">
                    <div class="quick-card-icon"><i class="fas fa-ticket-alt"></i></div>
                    <div class="quick-card-text">
                        <h6>Kho Voucher</h6>
                        <p>Xem voucher bạn đang sở hữu</p>
                    </div>
                    <span class="quick-card-arrow"><i class="fas fa-chevron-right"></i></span>
                </a>

                <a href="{{ route('profile.review') }}" class="quick-card">
                    <div class="quick-card-icon"><i class="fas fa-star"></i></div>
                    <div class="quick-card-text">
                        <h6>Đánh giá của tôi</h6>
                        <p>Chia sẻ cảm nhận về sản phẩm</p>
                    </div>
                    <span class="quick-card-arrow"><i class="fas fa-chevron-right"></i></span>
                </a>

                <a href="{{ route('profile.wishlist') }}" class="quick-card">
                    <div class="quick-card-icon"><i class="fas fa-heart"></i></div>
                    <div class="quick-card-text">
                        <h6>Sản phẩm yêu thích</h6>
                        <p>Danh sách sản phẩm đã lưu</p>
                    </div>
                    <span class="quick-card-arrow"><i class="fas fa-chevron-right"></i></span>
                </a>

                <a href="{{ route('contact.index') }}" class="quick-card">
                    <div class="quick-card-icon"><i class="fas fa-headset"></i></div>
                    <div class="quick-card-text">
                        <h6>Hỗ trợ</h6>
                        <p>Liên hệ với ElectronicShop</p>
                    </div>
                    <span class="quick-card-arrow"><i class="fas fa-chevron-right"></i></span>
                </a>

            </div>

            <div class="profile-section-label">Khác</div>
            <div class="quick-grid">

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="quick-card is-logout">
                        <div class="quick-card-icon"><i class="fas fa-sign-out-alt"></i></div>
                        <div class="quick-card-text">
                            <h6>Đăng xuất</h6>
                            <p>Thoát khỏi tài khoản hiện tại</p>
                        </div>
                        <span class="quick-card-arrow"><i class="fas fa-chevron-right"></i></span>
                    </button>
                </form>

            </div>

        </div>
    </div>
@endsection