@extends('layouts.app')
@section('title', 'ElectronicShop - Liên hệ')
@php $showSearch = true; @endphp

@push('styles')
<style>
/* ============================================================
   PAGE BACKGROUND — Samsung Style (Minimalist, Light Gray)
   ============================================================ */
body {
    background: #f4f4f4; /* Màu nền xám nhạt tinh tế */
    color: #000000;
}

/* ============================================================
   SCROLL REVEAL (Giữ lại hiệu ứng cuộn mượt mà)
   ============================================================ */
.reveal {
    opacity: 0; transform: translateY(28px);
    transition: opacity .65s cubic-bezier(.16,1,.3,1), transform .65s cubic-bezier(.16,1,.3,1);
}
.reveal.revealed { opacity: 1; transform: translateY(0); }

.reveal-left {
    opacity: 0; transform: translateX(-32px);
    transition: opacity .65s cubic-bezier(.16,1,.3,1), transform .65s cubic-bezier(.16,1,.3,1);
}
.reveal-left.revealed { opacity: 1; transform: translateX(0); }

.reveal-right {
    opacity: 0; transform: translateX(32px);
    transition: opacity .65s cubic-bezier(.16,1,.3,1), transform .65s cubic-bezier(.16,1,.3,1);
}
.reveal-right.revealed { opacity: 1; transform: translateX(0); }

.stagger-children > * {
    opacity: 0; transform: translateY(18px);
    transition: opacity .5s cubic-bezier(.16,1,.3,1), transform .5s cubic-bezier(.16,1,.3,1);
}
.stagger-children.revealed > *:nth-child(1) { opacity:1; transform:translateY(0); transition-delay:.05s; }
.stagger-children.revealed > *:nth-child(2) { opacity:1; transform:translateY(0); transition-delay:.12s; }
.stagger-children.revealed > *:nth-child(3) { opacity:1; transform:translateY(0); transition-delay:.19s; }

/* ============================================================
   PAGE WRAPPER
   ============================================================ */
.contact-page {
    padding: 36px 0 60px;
    min-height: 100vh;
}

.contact-page .container {
    max-width: 1200px; margin: auto;
}

/* ============================================================
   ALERT
   ============================================================ */
.alert-success {
    display: flex; align-items: center; gap: 10px;
    background: #e6f4ea;
    color: #137333;
    border: 1px solid #ceead6;
    padding: 14px 18px; border-radius: 12px;
    margin-bottom: 24px; font-weight: 600; font-size: 14px;
    animation: alertIn .4s cubic-bezier(.16,1,.3,1);
}
@keyframes alertIn { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }

.text-danger { display:block; color:#d93025; font-size:12.5px; margin-top:4px; }

/* ============================================================
   HERO HEADER — Phong cách banner Đen mờ sang trọng
   ============================================================ */
.contact-header {
    display: flex; justify-content: space-between;
    align-items: center; gap: 40px; margin-bottom: 40px;
    background: #000000;
    border-radius: 24px; padding: 40px 48px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
}

.contact-text { flex: 1; }
.contact-text h1 {
    font-size: 38px; font-weight: 800;
    color: #ffffff; margin-bottom: 14px;
    line-height: 1.2;
    letter-spacing: -0.5px;
}
.contact-text h1 span {
    color: #2189ff; /* Samsung Blue */
}
.contact-text p {
    color: #aaaaaa; font-size: 16px;
    line-height: 1.6; max-width: 500px;
}

.contact-banner {
    flex: 0 0 180px;
    display: flex; align-items: center; justify-content: center;
}
.contact-banner i {
    font-size: 100px;
    color: #ffffff;
    filter: drop-shadow(0 4px 12px rgba(255,255,255,0.15));
}

/* ============================================================
   LAYOUT GRID
   ============================================================ */
.contact-layout {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 28px; align-items: start;
}
@media (max-width: 992px) { .contact-layout { grid-template-columns: 1fr; } }

/* ============================================================
   CONTACT FORM & INFO — Trắng tinh, viền mảnh
   ============================================================ */
.contact-form, .contact-info {
    background: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 24px; padding: 32px;
}

.contact-form h3, .contact-info h3 {
    font-size: 16px; font-weight: 700;
    color: #000000; margin-bottom: 24px;
    padding-bottom: 14px;
    border-bottom: 1px solid #eeeeee;
}

.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
@media (max-width: 600px) { .form-row { grid-template-columns: 1fr; } }

.form-group { margin-bottom: 20px; }

.contact-form input,
.contact-form textarea,
.form-select {
    width: 100%;
    border: 1px solid #d5d5d5;
    border-radius: 12px;
    padding: 14px 16px;
    font-size: 15px;
    outline: none;
    background: #f8f9fa;
    color: #000000;
    transition: all .2s ease;
    font-family: inherit;
}
.contact-form input::placeholder,
.contact-form textarea::placeholder { color: #757575; }

.contact-form input:focus,
.contact-form textarea:focus,
.form-select:focus {
    border-color: #2189ff;
    background: #ffffff;
    box-shadow: 0 0 0 3px rgba(33, 137, 255, 0.15);
}

.contact-form textarea { resize: vertical; min-height: 140px; }

.form-label {
    display: block; margin-bottom: 8px;
    font-weight: 600; font-size: 14px; color: #333333;
}

/* Nút bấm viền bo tròn đặc trưng Samsung */
.btn-send {
    width: 100%; border: none;
    background: #000000;
    color: #ffffff; padding: 16px;
    border-radius: 30px; /* Pill shape */
    font-size: 15px; font-weight: 700;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    transition: all .2s;
}
.btn-send:hover {
    background: #333333;
}

/* ============================================================
   CONTACT INFO ITEMS
   ============================================================ */
.info-item {
    display: flex; align-items: center; gap: 16px;
    margin-bottom: 20px; padding: 8px 0;
}

.info-item .info-icon {
    width: 48px; height: 48px; border-radius: 50%;
    background: #f4f4f4;
    color: #000000; font-size: 18px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    transition: background .2s, color .2s;
}
.info-item:hover .info-icon {
    background: #000000;
    color: #ffffff;
}

.info-item b { display:block; color:#000000; margin-bottom:4px; font-size:14px; font-weight:700; }
.info-item p { color:#555555; line-height:1.4; margin:0; font-size:14px; }

/* ============================================================
   SOCIAL BOX
   ============================================================ */
.social-box {
    margin-top: 30px; padding-top: 24px;
    border-top: 1px solid #eeeeee;
}
.social-box h4 {
    font-size: 13px; font-weight: 700;
    color: #000000; margin-bottom: 16px;
}

.socials { display: flex; gap: 12px; }
.socials a {
    width: 42px; height: 42px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    background: #f4f4f4; color: #000000; font-size: 16px;
    text-decoration: none;
    transition: all .25s;
}
.socials a:hover { 
    transform: translateY(-4px); 
    background: #000000; color: #ffffff; 
}

/* ============================================================
   GOOGLE MAP
   ============================================================ */
.contact-map {
    margin-top: 40px; border-radius: 24px; overflow: hidden;
    border: 1px solid #e0e0e0;
}
.contact-map iframe { width: 100%; height: 440px; border: 0; display: block; }

/* ============================================================
   SERVICES STRIP — Minimalist
   ============================================================ */
.contact-services {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px; margin-top: 40px;
}
@media (max-width: 768px) { .contact-services { grid-template-columns: 1fr; } }

.service-card {
    background: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 24px; padding: 32px 24px;
    text-align: center;
    transition: transform .25s, box-shadow .25s;
}
.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.06);
}

.service-icon {
    width: 64px; height: 64px; border-radius: 50%;
    background: #f4f4f4;
    color: #000000; font-size: 24px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 20px;
    transition: all .3s;
}
.service-card:hover .service-icon { 
    background: #000000; color: #ffffff; 
}

.service-card b {
    display: block; font-size: 16px; font-weight: 700;
    color: #000000; margin-bottom: 8px;
}
.service-card p { color: #555555; margin: 0; font-size: 14px; }

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 992px) {
    .contact-header { flex-direction: column; text-align: center; padding: 32px 24px; }
    .contact-text p  { max-width: 100%; margin: 0 auto; }
    .contact-banner  { justify-content: center; margin-top: -10px; }
    .contact-banner i { font-size: 80px; }
}
@media (max-width: 768px) {
    .contact-page { padding: 20px 0; }
    .contact-text h1 { font-size: 28px; }
    .contact-form, .contact-info, .service-card { padding: 24px; }
    .contact-map iframe { height: 300px; }
}
</style>
@endpush

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

        {{-- ===== HERO HEADER ===== --}}
        <div class="contact-header reveal">
            <div class="contact-text">
                <h1>LIÊN HỆ <span>VỚI CHÚNG TÔI</span></h1>
                <p>Chúng tôi luôn sẵn sàng hỗ trợ và giải đáp mọi thắc mắc của khách hàng về sản phẩm và dịch vụ.</p>
            </div>
            <div class="contact-banner">
                <i class="fas fa-headset"></i>
            </div>
        </div>

        {{-- ===== LAYOUT ===== --}}
        <div class="contact-layout">

            {{-- FORM --}}
            <div class="contact-form reveal-left">
                <h3>Gửi liên hệ cho chúng tôi</h3>

                <form action="{{ route('contact.send') }}" method="POST">
                    @csrf

                    <div class="form-row">
                        <div class="form-group">
                            <input type="text" name="name" value="{{ old('name') }}"
                                   placeholder="Họ và tên">
                            @error('name')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" value="{{ old('email') }}"
                                   placeholder="Email">
                            @error('email')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <input type="text" name="phone" value="{{ old('phone') }}"
                               placeholder="Số điện thoại">
                        @error('phone')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Chủ đề</label>
                        <select name="subject" class="form-select">
                            <option value="">-- Chọn chủ đề --</option>
                            @foreach(['Tư vấn sản phẩm','Đặt hàng','Bảo hành','Đổi trả','Khiếu nại','Góp ý','Khác'] as $opt)
                            <option value="{{ $opt }}" {{ old('subject')==$opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                        @error('subject')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <textarea name="message" rows="6"
                                  placeholder="Nội dung liên hệ...">{{ old('message') }}</textarea>
                        @error('message')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <button type="submit" class="btn-send" id="btnSend">
                        Gửi liên hệ
                    </button>
                </form>
            </div>

            {{-- THÔNG TIN --}}
            <div class="contact-info reveal-right">
                <h3>Thông tin liên hệ</h3>

                <div class="stagger-children">
                    @foreach([
                        ['fa-map-marker-alt', 'Địa chỉ',       '123 Nguyễn Văn Linh, Hải Châu, Đà Nẵng'],
                        ['fa-phone',          'Điện thoại',     '1900 1234'],
                        ['fa-envelope',       'Email',          'cskh@electronicshop.vn'],
                        ['fa-clock',          'Giờ làm việc',   'Thứ 2 - Chủ nhật · 08:00 – 22:00'],
                    ] as [$icon, $label, $val])
                    <div class="info-item">
                        <div class="info-icon"><i class="fas {{ $icon }}"></i></div>
                        <div>
                            <b>{{ $label }}</b>
                            <p>{{ $val }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="social-box">
                    <h4>KẾT NỐI VỚI CHÚNG TÔI</h4>
                    <div class="socials">
                        <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" title="YouTube"><i class="fab fa-youtube"></i></a>
                        <a href="#" title="TikTok"><i class="fab fa-tiktok"></i></a>
                        <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>

        </div>

        {{-- ===== GOOGLE MAP ===== --}}
        <div class="contact-map reveal">
            <iframe
                src="https://www.google.com/maps?q=123+Nguyen+Van+Linh+Da+Nang&output=embed"
                loading="lazy" allowfullscreen title="Bản đồ ElectronicShop">
            </iframe>
        </div>

        {{-- ===== SERVICES ===== --}}
        <div class="contact-services stagger-children">
            @foreach([
                ['fa-truck-fast',     'Giao hàng nhanh',    'Toàn quốc'],
                ['fa-shield-halved',  'Bảo hành chính hãng','12 – 24 tháng'],
                ['fa-headset',        'Hỗ trợ 24/7',        'Luôn sẵn sàng'],
            ] as [$icon, $title, $sub])
            <div class="service-card">
                <div class="service-icon"><i class="fas {{ $icon }}"></i></div>
                <b>{{ $title }}</b>
                <p>{{ $sub }}</p>
            </div>
            @endforeach
        </div>

    </div>
</section>
@endsection

@push('scripts')
<script>
/* ============================================================
   FORM — loading spinner on submit
   ============================================================ */
document.querySelector('form[action="{{ route('contact.send') }}"]')
    ?.addEventListener('submit', function () {
        const btn = document.getElementById('btnSend');
        if (btn) {
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
            btn.disabled = true;
            btn.style.opacity = '.7';
        }
    });

/* ============================================================
   ANIMATIONS
   ============================================================ */
(function () {
    /* ---- Scroll Reveal ---- */
    const io = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) { 
                e.target.classList.add('revealed'); 
                io.unobserve(e.target); 
            }
        });
    }, { threshold: 0.07, rootMargin: '0px 0px -30px 0px' });
    
    document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .stagger-children')
        .forEach(el => io.observe(el));
})();
</script>
@endpush