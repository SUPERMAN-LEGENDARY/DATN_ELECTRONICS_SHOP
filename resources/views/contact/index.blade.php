@extends('layouts.app')

@section('title', 'ElectronicShop - Liên hệ')

@php
$showSearch = true;
@endphp

@section('content')

<section class="contact-page">

    <div class="container">

        {{-- Thông báo --}}
        @if(session('success'))
        <div class="alert-success">
            <i class="fas fa-circle-check"></i>
            {{ session('success') }}
        </div>
        @endif

        {{-- Header --}}
        <div class="contact-header">

            <div class="contact-text">

                <h1>LIÊN HỆ</h1>

                <p>
                    Chúng tôi luôn sẵn sàng hỗ trợ và giải đáp mọi thắc mắc
                    của khách hàng về sản phẩm và dịch vụ.
                </p>

            </div>

            <div class="contact-banner">
                <i class="fas fa-headset"></i>
            </div>

        </div>

        {{-- Layout --}}
        <div class="contact-layout">

            {{-- FORM --}}
            <div class="contact-form">

                <h3>GỬI LIÊN HỆ CHO CHÚNG TÔI</h3>

                <form action="{{ route('contact.send') }}" method="POST">

                    @csrf

                    <div class="form-row">

                        <div class="form-group">
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Họ và tên">
                            @error('name')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="Email">
                            @error('email')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    <div class="form-group">
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Số điện thoại">
                        @error('phone')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">

                        <label class="form-label">Chủ đề</label>

                        <select name="subject" class="form-select">
                            <option value="">-- Chọn chủ đề --</option>
                            <option value="Tư vấn sản phẩm" {{ old('subject') == 'Tư vấn sản phẩm' ? 'selected' : '' }}>Tư vấn sản phẩm</option>
                            <option value="Đặt hàng" {{ old('subject') == 'Đặt hàng' ? 'selected' : '' }}>Đặt hàng</option>
                            <option value="Bảo hành" {{ old('subject') == 'Bảo hành' ? 'selected' : '' }}>Bảo hành</option>
                            <option value="Đổi trả" {{ old('subject') == 'Đổi trả' ? 'selected' : '' }}>Đổi trả</option>
                            <option value="Khiếu nại" {{ old('subject') == 'Khiếu nại' ? 'selected' : '' }}>Khiếu nại</option>
                            <option value="Góp ý" {{ old('subject') == 'Góp ý' ? 'selected' : '' }}>Góp ý</option>
                            <option value="Khác" {{ old('subject') == 'Khác' ? 'selected' : '' }}>Khác</option>
                        </select>

                        @error('subject')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror

                    </div>

                    <div class="form-group">
                        <textarea name="message" rows="6" placeholder="Nội dung liên hệ">{{ old('message') }}</textarea>
                        @error('message')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn-send">
                        <i class="fas fa-paper-plane"></i>
                        Gửi liên hệ
                    </button>

                </form>

            </div>

            {{-- THÔNG TIN --}}
            <div class="contact-info">

                <h3>THÔNG TIN LIÊN HỆ</h3>

                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <b>Địa chỉ</b>
                        <p>123 Nguyễn Văn Linh, Hải Châu, Đà Nẵng</p>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <b>Điện thoại</b>
                        <p>1900 1234</p>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <b>Email</b>
                        <p>cskh@electronicshop.vn</p>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-clock"></i>
                    <div>
                        <b>Giờ làm việc</b>
                        <p>Thứ 2 - Chủ nhật<br>08:00 - 22:00</p>
                    </div>
                </div>

                <div class="social-box">

                    <h4>KẾT NỐI VỚI CHÚNG TÔI</h4>

                    <div class="socials">
                        <a href="#" class="facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="youtube"><i class="fab fa-youtube"></i></a>
                        <a href="#" class="tiktok"><i class="fab fa-tiktok"></i></a>
                        <a href="#" class="instagram"><i class="fab fa-instagram"></i></a>
                    </div>

                </div>

            </div>

        </div>

        {{-- Google Map --}}
        <div class="contact-map">
            <iframe
                src="https://www.google.com/maps?q=123+Nguyen+Van+Linh+Da+Nang&output=embed"
                loading="lazy"
                allowfullscreen>
            </iframe>
        </div>

        {{-- Dịch vụ --}}
        <div class="contact-services">

            <div>
                <i class="fas fa-truck-fast"></i>
                <b>Giao hàng nhanh</b>
                <p>Toàn quốc</p>
            </div>

            <div>
                <i class="fas fa-shield-halved"></i>
                <b>Bảo hành chính hãng</b>
                <p>12 - 24 tháng</p>
            </div>

            <div>
                <i class="fas fa-headset"></i>
                <b>Hỗ trợ 24/7</b>
                <p>Luôn sẵn sàng</p>
            </div>

        </div>

    </div>

</section>

@endsection

<style>
    /* ========================== CONTACT PAGE =========================== */
    .contact-page {
        background: #f5f7fb;
        padding: 40px 0 60px;
        min-height: 100vh;
    }

    .contact-page .container {
        max-width: 1200px;
        margin: auto;
    }

    /* ========================== ALERT =========================== */
    .alert-success {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
        padding: 15px 18px;
        border-radius: 10px;
        margin-bottom: 25px;
        font-weight: 600;
    }

    .text-danger {
        display: block;
        color: #ef4444;
        font-size: 13px;
        margin-top: -6px;
        margin-bottom: 12px;
    }

    /* ========================== HEADER =========================== */
    .contact-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 40px;
        margin-bottom: 40px;
    }

    .contact-text {
        flex: 1;
    }

    .contact-text h1 {
        font-size: 40px;
        font-weight: 800;
        color: #111827;
        margin-bottom: 15px;
    }

    .contact-text p {
        color: #6b7280;
        font-size: 16px;
        line-height: 1.8;
        max-width: 520px;
    }

    .contact-banner {
        flex: 0 0 220px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .contact-banner i {
        font-size: 130px;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* ========================== CONTENT =========================== */
    .contact-layout {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
    }

    .contact-form,
    .contact-info {
        background: #fff;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, .08);
    }

    .contact-form h3,
    .contact-info h3 {
        margin-bottom: 25px;
        font-size: 22px;
        color: #111827;
        font-weight: 700;
    }

    /* ========================== FORM =========================== */
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .contact-form input,
    .contact-form textarea {
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        padding: 14px 15px;
        font-size: 15px;
        transition: .3s;
        outline: none;
        background: #fff;
    }

    .contact-form textarea {
        resize: vertical;
        min-height: 150px;
    }

    .contact-form input:focus,
    .contact-form textarea:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .15);
    }

    .btn-send {
        width: 100%;
        border: none;
        background: #2563eb;
        color: #fff;
        padding: 15px;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        transition: .3s;
    }

    .btn-send:hover {
        background: #1d4ed8;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #111827;
    }

    .form-select {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 15px;
        background: #fff;
        cursor: pointer;
        transition: .3s;
    }

    .form-select:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .15);
    }

    /* ========================== CONTACT INFO =========================== */
    .info-item {
        display: flex;
        align-items: flex-start;
        gap: 18px;
        margin-bottom: 25px;
    }

    .info-item i {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #eff6ff;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .info-item b {
        display: block;
        color: #111827;
        margin-bottom: 6px;
        font-size: 16px;
    }

    .info-item p {
        color: #6b7280;
        line-height: 1.6;
        margin: 0;
    }

    /* ========================== SOCIAL =========================== */
    .social-box {
        margin-top: 35px;
        border-top: 1px solid #e5e7eb;
        padding-top: 25px;
    }

    .social-box h4 {
        margin-bottom: 18px;
        font-size: 17px;
        font-weight: 700;
        color: #111827;
    }

    .socials {
        display: flex;
        gap: 12px;
    }

    .socials a {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 18px;
        transition: .3s;
        text-decoration: none;
    }

    .socials a:hover {
        transform: translateY(-5px) scale(1.08);
    }

    .facebook {
        background: #1877F2;
    }

    .youtube {
        background: #ff0000;
    }

    .tiktok {
        background: #000;
    }

    .instagram {
        background: linear-gradient(45deg, #feda75, #fa7e1e, #d62976, #962fbf, #4f5bd5);
    }

    /* ========================== GOOGLE MAP =========================== */
    .contact-map {
        margin-top: 45px;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
    }

    .contact-map iframe {
        width: 100%;
        height: 450px;
        border: 0;
    }

    /* ========================== SERVICES =========================== */
    .contact-services {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
        margin-top: 40px;
    }

    .contact-services div {
        background: #fff;
        padding: 30px 20px;
        border-radius: 16px;
        text-align: center;
        box-shadow: 0 8px 20px rgba(0, 0, 0, .08);
        transition: .3s;
    }

    .contact-services div:hover {
        transform: translateY(-8px);
    }

    .contact-services i {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: #eff6ff;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        margin: 0 auto 18px;
    }

    .contact-services b {
        display: block;
        font-size: 18px;
        color: #111827;
        margin-bottom: 8px;
    }

    .contact-services p {
        color: #6b7280;
        margin: 0;
    }

    /* ========================== RESPONSIVE =========================== */
    @media(max-width:992px) {
        .contact-header {
            flex-direction: column;
            text-align: center;
        }

        .contact-text p {
            max-width: 100%;
        }

        .contact-banner {
            justify-content: center;
        }

        .contact-layout {
            grid-template-columns: 1fr;
        }

        .contact-services {
            grid-template-columns: 1fr;
        }
    }

    @media(max-width:768px) {
        .contact-page {
            padding: 25px 0;
        }

        .contact-text h1 {
            font-size: 30px;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .contact-form,
        .contact-info {
            padding: 20px;
        }

        .contact-map iframe {
            height: 320px;
        }
    }

    @media(max-width:576px) {
        .contact-text h1 {
            font-size: 26px;
        }

        .contact-form h3,
        .contact-info h3 {
            font-size: 20px;
        }

        .btn-send {
            font-size: 15px;
        }

        .socials {
            justify-content: center;
        }
    }
</style>