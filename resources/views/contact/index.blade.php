@extends('layouts.app')
@section('title', 'ElectronicShop - Liên hệ')
@php $showSearch = true; @endphp

@push('styles')
<style>
/* ============================================================
   PAGE BACKGROUND — sky gradient (khớp trang chủ)
   ============================================================ */
body {
    background: linear-gradient(180deg,
        #bae6fd 0%,
        #e0f2fe 18%,
        #f0f9ff 38%,
        #e0f2fe 62%,
        #bae6fd 100%) fixed;
    background-attachment: fixed;
}

#sky-canvas {
    position: fixed; inset: 0;
    width: 100%; height: 100%;
    pointer-events: none; z-index: 0; opacity: .42;
}

.bubble {
    position: fixed; border-radius: 50%;
    background: radial-gradient(circle at 35% 35%, rgba(255,255,255,.8), rgba(186,230,253,.3));
    border: 1px solid rgba(125,211,252,.4);
    pointer-events: none; z-index: 0;
    animation: bubbleRise linear infinite;
}
@keyframes bubbleRise {
    0%   { transform: translateY(0) scale(1);    opacity: .7; }
    80%  { opacity: .4; }
    100% { transform: translateY(-110vh) scale(1.1); opacity: 0; }
}

/* ============================================================
   SCROLL REVEAL
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
.stagger-children.revealed > *:nth-child(4) { opacity:1; transform:translateY(0); transition-delay:.26s; }

/* ============================================================
   PAGE WRAPPER
   ============================================================ */
.contact-page {
    padding: 36px 0 60px;
    min-height: 100vh;
    position: relative; z-index: 1;
}

.contact-page .container {
    max-width: 1200px; margin: auto;
    position: relative; z-index: 1;
}

/* ============================================================
   ALERT
   ============================================================ */
.alert-success {
    display: flex; align-items: center; gap: 10px;
    background: rgba(220,252,231,.9);
    backdrop-filter: blur(8px);
    color: #166534;
    border: 1px solid rgba(187,247,208,.8);
    padding: 14px 18px; border-radius: 12px;
    margin-bottom: 24px; font-weight: 600; font-size: 14px;
    animation: alertIn .4s cubic-bezier(.16,1,.3,1);
}
@keyframes alertIn { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }

.text-danger { display:block; color:#ef4444; font-size:12.5px; margin-top:4px; }

/* ============================================================
   HERO HEADER
   ============================================================ */
.contact-header {
    display: flex; justify-content: space-between;
    align-items: center; gap: 40px; margin-bottom: 40px;
    background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 45%, #7dd3fc 80%, #38bdf8 100%);
    border-radius: 20px; padding: 36px 40px;
    position: relative; overflow: hidden;
    box-shadow: 0 6px 28px rgba(14,165,233,.2);
}

/* decorative orbs */
.contact-header::before {
    content: ''; position: absolute; top: -70px; right: -70px;
    width: 220px; height: 220px; border-radius: 50%;
    background: rgba(255,255,255,.18); pointer-events: none;
}
.contact-header::after {
    content: ''; position: absolute; bottom: -50px; left: -50px;
    width: 160px; height: 160px; border-radius: 50%;
    background: rgba(255,255,255,.14); pointer-events: none;
}

.contact-text { flex: 1; position: relative; z-index: 1; }
.contact-text h1 {
    font-size: 38px; font-weight: 800;
    color: #0c4a6e; margin-bottom: 14px;
    line-height: 1.2;
}
.contact-text h1 span {
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
}
.contact-text p {
    color: #0369a1; font-size: 15.5px;
    line-height: 1.75; max-width: 500px; opacity: .88;
}

.contact-banner {
    flex: 0 0 180px;
    display: flex; align-items: center; justify-content: center;
    position: relative; z-index: 1;
}
.contact-banner i {
    font-size: 110px;
    background: linear-gradient(135deg, #0369a1, #38bdf8);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    animation: headsetFloat 3s ease-in-out infinite;
    filter: drop-shadow(0 6px 16px rgba(14,165,233,.3));
}
@keyframes headsetFloat {
    0%, 100% { transform: translateY(0);  }
    50%       { transform: translateY(-12px); }
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
   CONTACT FORM — glassmorphism
   ============================================================ */
.contact-form {
    background: rgba(255,255,255,.82);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(186,230,253,.65);
    border-radius: 18px; padding: 30px;
    box-shadow: 0 6px 28px rgba(14,165,233,.1);
}

.contact-form h3 {
    font-size: 14px; font-weight: 800;
    text-transform: uppercase; letter-spacing: .6px;
    color: #0c4a6e; margin-bottom: 24px;
    padding-bottom: 14px;
    border-bottom: 1px solid rgba(186,230,253,.55);
    display: flex; align-items: center; gap: 8px;
}
.contact-form h3::before {
    content: ''; width: 20px; height: 3px;
    background: linear-gradient(90deg, #0369a1, #38bdf8);
    border-radius: 2px; display: inline-block;
}

.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
@media (max-width: 600px) { .form-row { grid-template-columns: 1fr; } }

.form-group { margin-bottom: 18px; }

.contact-form input,
.contact-form textarea,
.form-select {
    width: 100%;
    border: 1px solid rgba(125,211,252,.5);
    border-radius: 10px;
    padding: 13px 15px;
    font-size: 14.5px;
    outline: none;
    background: rgba(255,255,255,.72);
    backdrop-filter: blur(4px);
    color: #0c4a6e;
    transition: border-color .22s, box-shadow .22s, background .22s;
    font-family: inherit;
}
.contact-form input::placeholder,
.contact-form textarea::placeholder { color: #7dd3fc; }

.contact-form input:focus,
.contact-form textarea:focus,
.form-select:focus {
    border-color: #0ea5e9;
    box-shadow: 0 0 0 3px rgba(14,165,233,.15);
    background: rgba(255,255,255,.9);
}

.contact-form textarea { resize: vertical; min-height: 150px; }

.form-label {
    display: block; margin-bottom: 7px;
    font-weight: 600; font-size: 13.5px; color: #0369a1;
}

.form-select { cursor: pointer; }

.btn-send {
    width: 100%; border: none;
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    color: #fff; padding: 14px;
    border-radius: 10px; font-size: 15px; font-weight: 700;
    cursor: pointer; letter-spacing: .3px;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    transition: opacity .2s, transform .18s, box-shadow .2s;
    box-shadow: 0 4px 18px rgba(14,165,233,.35);
    position: relative; overflow: hidden;
}
.btn-send::after {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,.25) 50%, transparent 60%);
    transform: translateX(-120%); transition: transform .5s ease;
    pointer-events: none;
}
.btn-send:hover::after { transform: translateX(120%); }
.btn-send:hover {
    opacity: .92; transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(14,165,233,.45);
}

/* ============================================================
   CONTACT INFO — glassmorphism
   ============================================================ */
.contact-info {
    background: rgba(255,255,255,.8);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(186,230,253,.65);
    border-radius: 18px; padding: 28px;
    box-shadow: 0 6px 28px rgba(14,165,233,.1);
}

.contact-info h3 {
    font-size: 14px; font-weight: 800;
    text-transform: uppercase; letter-spacing: .6px;
    color: #0c4a6e; margin-bottom: 24px;
    padding-bottom: 14px;
    border-bottom: 1px solid rgba(186,230,253,.55);
    display: flex; align-items: center; gap: 8px;
}
.contact-info h3::before {
    content: ''; width: 20px; height: 3px;
    background: linear-gradient(90deg, #0369a1, #38bdf8);
    border-radius: 2px; display: inline-block;
}

.info-item {
    display: flex; align-items: flex-start; gap: 14px;
    margin-bottom: 20px; padding: 10px 8px;
    border-radius: 10px;
    transition: background .2s, transform .2s;
}
.info-item:hover {
    background: rgba(186,230,253,.25);
    transform: translateX(4px);
}

.info-item .info-icon {
    width: 46px; height: 46px; border-radius: 12px;
    background: linear-gradient(135deg, #bae6fd, #7dd3fc);
    color: #0369a1; font-size: 18px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 2px 10px rgba(14,165,233,.2);
    transition: transform .2s, box-shadow .2s;
}
.info-item:hover .info-icon {
    transform: scale(1.1) rotate(-4deg);
    box-shadow: 0 4px 14px rgba(14,165,233,.3);
}

.info-item b { display:block; color:#0c4a6e; margin-bottom:4px; font-size:14px; font-weight:700; }
.info-item p { color:#0369a1; line-height:1.6; margin:0; font-size:13.5px; opacity:.85; }

/* ============================================================
   SOCIAL BOX
   ============================================================ */
.social-box {
    margin-top: 24px; padding-top: 20px;
    border-top: 1px solid rgba(186,230,253,.55);
}
.social-box h4 {
    font-size: 12px; font-weight: 800;
    text-transform: uppercase; letter-spacing: .6px;
    color: #0c4a6e; margin-bottom: 14px;
}

.socials { display: flex; gap: 10px; }
.socials a {
    width: 44px; height: 44px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 17px;
    text-decoration: none;
    transition: transform .25s cubic-bezier(.34,1.56,.64,1), box-shadow .2s;
}
.socials a:hover { transform: translateY(-6px) scale(1.08); }
.facebook  { background: #1877F2; box-shadow: 0 4px 12px rgba(24,119,242,.35); }
.youtube   { background: #ff0000; box-shadow: 0 4px 12px rgba(255,0,0,.3); }
.tiktok    { background: #111; box-shadow: 0 4px 12px rgba(0,0,0,.25); }
.instagram { background: linear-gradient(45deg, #feda75, #fa7e1e, #d62976, #962fbf, #4f5bd5); box-shadow: 0 4px 12px rgba(214,41,118,.3); }

/* ============================================================
   GOOGLE MAP
   ============================================================ */
.contact-map {
    margin-top: 40px; border-radius: 18px; overflow: hidden;
    box-shadow: 0 6px 28px rgba(14,165,233,.18);
    border: 1px solid rgba(186,230,253,.5);
}
.contact-map iframe { width: 100%; height: 440px; border: 0; }

/* ============================================================
   SERVICES STRIP — giống brands section trang chủ
   ============================================================ */
.contact-services {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px; margin-top: 36px;
}
@media (max-width: 768px) { .contact-services { grid-template-columns: 1fr; } }

.service-card {
    background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 45%, #7dd3fc 80%, #38bdf8 100%);
    border-radius: 16px; padding: 28px 20px;
    text-align: center;
    box-shadow: 0 4px 18px rgba(14,165,233,.15);
    transition: transform .25s cubic-bezier(.16,1,.3,1), box-shadow .25s;
    position: relative; overflow: hidden;
    cursor: default;
}
.service-card::before {
    content: ''; position: absolute; top: -45px; right: -45px;
    width: 130px; height: 130px; border-radius: 50%;
    background: rgba(255,255,255,.2); pointer-events: none;
}
.service-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 32px rgba(14,165,233,.25);
}

.service-icon {
    width: 64px; height: 64px; border-radius: 16px;
    background: rgba(255,255,255,.7);
    backdrop-filter: blur(6px);
    color: #0369a1; font-size: 28px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 16px;
    box-shadow: 0 4px 14px rgba(14,165,233,.2);
    transition: transform .3s cubic-bezier(.34,1.56,.64,1);
}
.service-card:hover .service-icon { transform: scale(1.12) rotate(-4deg); }

.service-card b {
    display: block; font-size: 16px; font-weight: 700;
    color: #0c4a6e; margin-bottom: 6px;
}
.service-card p { color: #0369a1; margin: 0; font-size: 13.5px; opacity: .8; }

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 992px) {
    .contact-header { flex-direction: column; text-align: center; padding: 28px 24px; }
    .contact-text p  { max-width: 100%; }
    .contact-banner  { justify-content: center; }
}
@media (max-width: 768px) {
    .contact-page { padding: 20px 0; }
    .contact-text h1 { font-size: 28px; }
    .contact-form, .contact-info { padding: 20px; }
    .contact-map iframe { height: 300px; }
}
</style>
@endpush

@section('content')
{{-- Sky Canvas --}}
<canvas id="sky-canvas" aria-hidden="true"></canvas>

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
                <p>Chúng tôi luôn sẵn sàng hỗ trợ và giải đáp mọi thắc mắc
                   của khách hàng về sản phẩm và dịch vụ.</p>
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
                        <i class="fas fa-paper-plane"></i> Gửi liên hệ
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
                        <a href="#" class="facebook" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="youtube"   title="YouTube"><i class="fab fa-youtube"></i></a>
                        <a href="#" class="tiktok"    title="TikTok"><i class="fab fa-tiktok"></i></a>
                        <a href="#" class="instagram" title="Instagram"><i class="fab fa-instagram"></i></a>
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
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';
            btn.disabled = true;
            btn.style.opacity = '.8';
        }
    });

/* ============================================================
   ANIMATIONS
   ============================================================ */
(function () {

    /* ---- Canvas clouds ---- */
    const canvas = document.getElementById('sky-canvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let W, H, clouds = [];
        function resize() { W = canvas.width = window.innerWidth; H = canvas.height = window.innerHeight; }
        window.addEventListener('resize', resize); resize();
        function makeCloud() {
            return { x: Math.random()*W*1.2, y: Math.random()*H*.6,
                     r: 50+Math.random()*110, dx: .13+Math.random()*.22,
                     alpha: .05+Math.random()*.1 };
        }
        for (let i = 0; i < 8; i++) clouds.push(makeCloud());
        function drawCloud(c) {
            const g = ctx.createRadialGradient(c.x,c.y,0,c.x,c.y,c.r);
            g.addColorStop(0, `rgba(255,255,255,${c.alpha})`);
            g.addColorStop(.6, `rgba(186,230,253,${c.alpha*.6})`);
            g.addColorStop(1, 'rgba(186,230,253,0)');
            ctx.beginPath(); ctx.arc(c.x,c.y,c.r,0,Math.PI*2);
            ctx.fillStyle = g; ctx.fill();
            [-.5,.5].forEach(o => {
                ctx.beginPath();
                ctx.arc(c.x+c.r*.55*o, c.y-c.r*.18, c.r*.72, 0, Math.PI*2);
                ctx.fillStyle = `rgba(255,255,255,${c.alpha*.7})`; ctx.fill();
            });
        }
        (function anim() {
            ctx.clearRect(0,0,W,H);
            clouds.forEach(c => { drawCloud(c); c.x += c.dx;
                if (c.x-c.r > W*1.2) { c.x=-c.r*2; c.y=Math.random()*H*.6; } });
            requestAnimationFrame(anim);
        })();
    }

    /* ---- Bubbles ---- */
    function spawnBubble() {
        const el = document.createElement('div');
        el.className = 'bubble';
        const size = 4+Math.random()*14, dur = 8+Math.random()*12;
        el.style.cssText = [`width:${size}px`,`height:${size}px`,
            `left:${Math.random()*100}vw`,`bottom:-${size}px`,
            `animation-duration:${dur}s`,`animation-delay:${Math.random()*5}s`].join(';');
        document.body.appendChild(el);
        setTimeout(() => el.remove(), (dur+5)*1000);
    }
    for (let i = 0; i < 8; i++) spawnBubble();
    setInterval(spawnBubble, 3500);

    /* ---- Scroll Reveal ---- */
    const io = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) { e.target.classList.add('revealed'); io.unobserve(e.target); }
        });
    }, { threshold: 0.07, rootMargin: '0px 0px -30px 0px' });
    document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .stagger-children')
        .forEach(el => io.observe(el));

    /* ---- Input focus glow effect ---- */
    document.querySelectorAll('.contact-form input, .contact-form textarea, .form-select')
        .forEach(el => {
            el.addEventListener('focus', function () {
                this.parentElement.style.transform = 'scale(1.005)';
                this.parentElement.style.transition = 'transform .2s';
            });
            el.addEventListener('blur', function () {
                this.parentElement.style.transform = 'scale(1)';
            });
        });

    /* ---- Service card icon tilt ---- */
    document.querySelectorAll('.service-card').forEach(card => {
        card.addEventListener('mousemove', function (e) {
            const r  = card.getBoundingClientRect();
            const dx = (e.clientX - r.left - r.width/2)  / (r.width/2);
            const dy = (e.clientY - r.top  - r.height/2) / (r.height/2);
            card.style.transform = `perspective(500px) rotateX(${-dy*4}deg) rotateY(${dx*4}deg) translateY(-8px)`;
        });
        card.addEventListener('mouseleave', function () {
            card.style.transform = '';
            card.style.transition = 'transform .4s cubic-bezier(.16,1,.3,1), box-shadow .25s';
            setTimeout(() => card.style.transition = '', 420);
        });
    });

    /* ---- Social links ripple ---- */
    document.querySelectorAll('.socials a').forEach(a => {
        a.style.position = 'relative';
        a.style.overflow = 'hidden';
        a.addEventListener('click', function (e) {
            const r    = a.getBoundingClientRect();
            const size = Math.max(r.width, r.height) * 2;
            const rip  = document.createElement('span');
            rip.style.cssText = [
                'position:absolute','border-radius:50%',
                `width:${size}px`,`height:${size}px`,
                `left:${e.clientX-r.left-size/2}px`,
                `top:${e.clientY-r.top-size/2}px`,
                'background:rgba(255,255,255,.3)',
                'transform:scale(0)',
                'animation:rippleOut .5s linear',
                'pointer-events:none',
            ].join(';');
            a.appendChild(rip);
            rip.addEventListener('animationend', () => rip.remove());
        });
    });

    /* ---- Inject ripple keyframe ---- */
    if (!document.querySelector('#rStyle')) {
        const s = document.createElement('style');
        s.id = 'rStyle';
        s.textContent = '@keyframes rippleOut { to { transform:scale(4); opacity:0; } }';
        document.head.appendChild(s);
    }

})();
</script>
@endpush
