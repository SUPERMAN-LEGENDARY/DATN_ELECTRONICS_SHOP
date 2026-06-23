{{-- ============================================ --}}
{{-- FILE: resources/views/pages/contact.blade.php --}}
{{-- ============================================ --}}
@extends('layouts.app')
@section('title', 'Liên hệ - ElectronicShop')

@push('styles')
<style>
.contact-page { max-width: 1200px; margin: 0 auto; padding: 0 16px 48px; }

/* HERO */
.contact-hero {
    display: grid; grid-template-columns: 1fr 1fr; align-items: center;
    gap: 32px; padding: 40px 0 36px; background: #f8f9fa;
    margin-left: -16px; margin-right: -16px; padding-left: 16px; padding-right: 16px;
    margin-bottom: 40px;
}
.contact-hero-text .breadcrumb { padding: 0 0 12px; }
.contact-hero-text h1 { font-size: 36px; font-weight: 800; color: #1565C0; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 14px; }
.contact-hero-text p { font-size: 15px; color: #555; line-height: 1.7; max-width: 400px; }
.contact-hero-img { border-radius: 12px; overflow: hidden; box-shadow: 0 8px 32px rgba(0,0,0,.12); }
.contact-hero-img img { width: 100%; height: 320px; object-fit: cover; display: block; }

/* MAIN LAYOUT */
.contact-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 32px; margin-bottom: 40px; }

/* FORM CARD */
.contact-form-card { background: #fff; border: 1px solid #e0e0e0; border-radius: 10px; padding: 28px; }
.contact-form-card h2 { font-size: 16px; font-weight: 800; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 22px; display: flex; align-items: center; gap: 8px; }
.contact-form-card h2::before { content: ''; width: 4px; height: 20px; background: #1565C0; border-radius: 2px; display: inline-block; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
.form-group { }
.form-group label { display: block; font-size: 12px; font-weight: 600; color: #555; margin-bottom: 5px; text-transform: uppercase; letter-spacing: .3px; }
.form-group .required { color: #E53935; }
.form-group input, .form-group select, .form-group textarea {
    width: 100%; border: 1px solid #e0e0e0; border-radius: 6px;
    padding: 10px 12px; font-size: 14px; outline: none; font-family: inherit; color: #333;
    transition: border-color .15s;
}
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: #1565C0; }
.form-group textarea { resize: vertical; min-height: 110px; }
.btn-send {
    display: inline-flex; align-items: center; gap: 8px; background: #1565C0; color: #fff;
    border: none; border-radius: 6px; padding: 12px 28px; font-size: 14px; font-weight: 700;
    cursor: pointer; transition: background .2s; text-transform: uppercase; letter-spacing: .5px;
}
.btn-send:hover { background: #0D47A1; }

/* INFO CARDS */
.contact-info-col { display: flex; flex-direction: column; gap: 16px; }
.info-card { background: #fff; border: 1px solid #e0e0e0; border-radius: 10px; padding: 22px; }
.info-card h2 { font-size: 16px; font-weight: 800; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
.info-card h2::before { content: ''; width: 4px; height: 20px; background: #1565C0; border-radius: 2px; display: inline-block; }
.info-item { display: flex; gap: 14px; align-items: flex-start; margin-bottom: 18px; }
.info-item:last-child { margin-bottom: 0; }
.info-icon {
    width: 44px; height: 44px; border-radius: 50%; background: #EBF3FF;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.info-icon i { color: #1565C0; font-size: 18px; }
.info-label { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .3px; margin-bottom: 4px; }
.info-value { font-size: 18px; font-weight: 800; color: #1565C0; margin-bottom: 3px; }
.info-sub { font-size: 12px; color: #888; }
.info-link { color: #1565C0; font-size: 13px; font-weight: 600; }

/* SOCIAL CARD */
.social-card { background: #fff; border: 1px solid #e0e0e0; border-radius: 10px; padding: 22px; }
.social-card h2 { font-size: 15px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 10px; }
.social-card p { font-size: 13px; color: #666; margin-bottom: 16px; line-height: 1.6; }
.social-links { display: flex; gap: 10px; }
.social-link {
    width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center;
    justify-content: center; color: #fff; font-size: 17px; transition: transform .2s, opacity .2s;
}
.social-link:hover { transform: scale(1.1); opacity: .9; }
.sl-fb { background: #1877F2; }
.sl-yt { background: #FF0000; }
.sl-tt { background: #000; }
.sl-ig { background: linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888); }

/* TRUST BAR */
.contact-trust {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-top: 8px;
}
.trust-card {
    background: #f8f9fa; border: 1px solid #e0e0e0; border-radius: 8px; padding: 16px;
    display: flex; align-items: center; gap: 12px;
}
.trust-card .tc-icon { color: #1565C0; font-size: 22px; flex-shrink: 0; }
.trust-card .tc-label { font-size: 13px; font-weight: 700; }
.trust-card .tc-sub { font-size: 12px; color: #888; }
</style>
@endpush

@section('content')
<div class="contact-page">
    {{-- HERO --}}
    <div class="contact-hero">
        <div class="contact-hero-text">
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Trang chủ</a>
                <span>›</span>
                <span style="color:#1565C0">Liên hệ</span>
            </div>
            <h1>Liên hệ</h1>
            <p>Chúng tôi luôn sẵn sàng lắng nghe và hỗ trợ bạn. Vui lòng liên hệ với EletronShop qua các kênh bên dưới.</p>
        </div>
        <div class="contact-hero-img">
            @if(isset($heroImage))
                <img src="{{ $heroImage }}" alt="Liên hệ ElectronicShop">
            @else
                <div style="width:100%;height:320px;background:linear-gradient(135deg,#0d1b2a 0%,#1565C0 100%);display:flex;align-items:center;justify-content:center;color:#4a90d9;font-size:80px">
                    <i class="fas fa-headset"></i>
                </div>
            @endif
        </div>
    </div>

    {{-- MAIN LAYOUT --}}
    <div class="contact-layout">
        {{-- FORM --}}
        <div class="contact-form-card">
            <h2>Gửi liên hệ cho chúng tôi</h2>
            <form action="{{ route('contact.send') }}" method="POST">
                @csrf
                <div class="form-grid">
                    <div class="form-group">
                        <label>Họ và tên <span class="required">*</span></label>
                        <input type="text" name="name" placeholder="Nhập họ và tên" value="{{ old('name') }}" required>
                        @error('name')<span style="color:#E53935;font-size:12px">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Email <span class="required">*</span></label>
                        <input type="email" name="email" placeholder="Nhập email của bạn" value="{{ old('email') }}" required>
                        @error('email')<span style="color:#E53935;font-size:12px">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Số điện thoại <span class="required">*</span></label>
                        <input type="tel" name="phone" placeholder="Nhập số điện thoại" value="{{ old('phone') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Chủ đề <span class="required">*</span></label>
                        <select name="subject" required>
                            <option value="">Chọn chủ đề</option>
                            <option value="order" {{ old('subject')=='order'?'selected':'' }}>Tư vấn đặt hàng</option>
                            <option value="warranty" {{ old('subject')=='warranty'?'selected':'' }}>Bảo hành sản phẩm</option>
                            <option value="return" {{ old('subject')=='return'?'selected':'' }}>Đổi trả hàng</option>
                            <option value="complaint" {{ old('subject')=='complaint'?'selected':'' }}>Phản ánh / Khiếu nại</option>
                            <option value="other" {{ old('subject')=='other'?'selected':'' }}>Khác</option>
                        </select>
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:18px">
                    <label>Nội dung tin nhắn <span class="required">*</span></label>
                    <textarea name="message" placeholder="Nhập nội dung tin nhắn của bạn..." required>{{ old('message') }}</textarea>
                    @error('message')<span style="color:#E53935;font-size:12px">{{ $message }}</span>@enderror
                </div>
                @if(session('success'))
                <div style="background:#E8F5E9;border:1px solid #C8E6C9;border-radius:6px;padding:12px 16px;margin-bottom:16px;color:#2E7D32;font-size:14px">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
                @endif
                <button type="submit" class="btn-send">
                    <i class="fas fa-paper-plane"></i> Gửi tin nhắn
                </button>
            </form>
        </div>

        {{-- INFO + SOCIAL --}}
        <div class="contact-info-col">
            <div class="info-card">
                <h2>Thông tin liên hệ</h2>
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-phone-alt"></i></div>
                    <div>
                        <div class="info-label">Hotline</div>
                        <div class="info-value">0123 456 789</div>
                        <div class="info-sub">Hỗ trợ từ 8:00 – 22:00 (Tất cả các ngày)</div>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-envelope"></i></div>
                    <div>
                        <div class="info-label">Email</div>
                        <div class="info-value" style="font-size:15px">support@eletronshop.vn</div>
                        <div class="info-sub">Phản hồi trong vòng 24h</div>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div>
                        <div class="info-label">Hệ thống cửa hàng</div>
                        <div class="info-sub" style="margin-bottom:4px">Xem danh sách cửa hàng trên toàn quốc</div>
                        <a href="#" class="info-link">Xem cửa hàng →</a>
                    </div>
                </div>
            </div>

            <div class="social-card">
                <h2>Kết nối với chúng tôi</h2>
                <p>Theo dõi chúng tôi trên các mạng xã hội để cập nhật tin tức mới nhất.</p>
                <div class="social-links">
                    <a href="#" class="social-link sl-fb"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link sl-yt"><i class="fab fa-youtube"></i></a>
                    <a href="#" class="social-link sl-tt"><i class="fab fa-tiktok"></i></a>
                    <a href="#" class="social-link sl-ig"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </div>

    {{-- TRUST BAR --}}
    <!-- <div class="contact-trust">
        <div class="trust-card">
            <i class="fas fa-truck tc-icon"></i>
            <div><div class="tc-label">Giao hàng miễn phí</div><div class="tc-sub">Đơn từ 500k</div></div>
        </div>
        <div class="trust-card">
            <i class="fas fa-sync-alt tc-icon"></i>
            <div><div class="tc-label">Đổi trả dễ dàng</div><div class="tc-sub">Đổi mới trong 30 ngày</div></div>
        </div>
        <div class="trust-card">
            <i class="fas fa-shield-alt tc-icon"></i>
            <div><div class="tc-label">Cam kết chính hãng</div><div class="tc-sub">100% sản phẩm chính hãng</div></div>
        </div>
        <div class="trust-card">
            <i class="fas fa-headset tc-icon"></i>
            <div><div class="tc-label">Hỗ trợ 24/7</div><div class="tc-sub">Hotline: 0123 456 789</div></div>
        </div>
    </div> -->
</div>
@endsection
