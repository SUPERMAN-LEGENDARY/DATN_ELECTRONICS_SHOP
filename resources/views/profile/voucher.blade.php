@extends('layouts.app')
@section('title', 'Kho Voucher - ElectronicShop')

@push('styles')
<style>
/* ============================================================
   PAGE BACKGROUND — Samsung Minimalist (Light Gray & White)
   ============================================================ */
body {
    background: #f4f4f4;
    background-attachment: fixed;
    color: #111111;
}
#sky-canvas {
    position: fixed; inset: 0; width: 100%; height: 100%;
    pointer-events: none; z-index: 0; opacity: .6;
}
.bubble {
    position: fixed; border-radius: 50%;
    background: radial-gradient(circle at 35% 35%, rgba(255,255,255,1), rgba(0,0,0,0.03));
    border: 1px solid rgba(0,0,0,0.04);
    pointer-events: none; z-index: 0;
    animation: bubbleRise linear infinite;
}
@keyframes bubbleRise {
    0%   { transform: translateY(0) scale(1);    opacity: .7; }
    80%  { opacity: .3; }
    100% { transform: translateY(-110vh) scale(1.1); opacity: 0; }
}

/* ============================================================
   SCROLL REVEAL
   ============================================================ */
.reveal {
    opacity: 0; transform: translateY(26px);
    transition: opacity .6s cubic-bezier(.16,1,.3,1), transform .6s cubic-bezier(.16,1,.3,1);
}
.reveal.revealed { opacity: 1; transform: translateY(0); }

.stagger-vouchers > * {
    opacity: 0; transform: translateY(22px) scale(.98);
    transition: opacity .5s cubic-bezier(.16,1,.3,1), transform .5s cubic-bezier(.16,1,.3,1);
}
.stagger-vouchers.revealed > *:nth-child(1)  { opacity:1; transform:none; transition-delay:.05s; }
.stagger-vouchers.revealed > *:nth-child(2)  { opacity:1; transform:none; transition-delay:.11s; }
.stagger-vouchers.revealed > *:nth-child(3)  { opacity:1; transform:none; transition-delay:.17s; }
.stagger-vouchers.revealed > *:nth-child(4)  { opacity:1; transform:none; transition-delay:.23s; }
.stagger-vouchers.revealed > *:nth-child(n+5){ opacity:1; transform:none; transition-delay:.28s; }

/* ripple */
.ripple-wave {
    position: absolute; border-radius: 50%;
    background: rgba(0,0,0,0.08);
    transform: scale(0); animation: rippleOut .6s linear;
    pointer-events: none; z-index: 10;
}
@keyframes rippleOut { to { transform:scale(4); opacity:0; } }

/* ============================================================
   PAGE WRAPPER
   ============================================================ */
.voucher-page-wrap {
    min-height: 100vh;
    padding: 32px 0 60px;
    position: relative; z-index: 1;
}
.voucher-container {
    max-width: 1200px; margin: 0 auto; padding: 0 16px;
    display: grid; grid-template-columns: 260px 1fr;
    gap: 24px; align-items: start;
    position: relative; z-index: 1;
}
@media (max-width: 991px) { .voucher-container { grid-template-columns: 1fr; } }
.profile-sidebar-wrap { position: sticky; top: 88px; }

/* ============================================================
   ALERT
   ============================================================ */
.alert-sky {
    display: flex; align-items: center; gap: 8px;
    backdrop-filter: blur(8px);
    padding: 12px 18px; border-radius: 12px;
    margin-bottom: 18px; font-weight: 600; font-size: 14px;
    animation: alertIn .4s cubic-bezier(.16,1,.3,1);
    background: rgba(240,253,244,.9); color: #166534; border: 1px solid rgba(187,247,208,.8);
}
@keyframes alertIn { from{opacity:0;transform:translateY(-10px)} to{opacity:1;transform:none} }
.alert-sky-success { background: rgba(240,253,244,.9); color: #166534; border: 1px solid rgba(187,247,208,.8); }

/* ============================================================
   MAIN CONTENT WRAPPER — Clean Tech
   ============================================================ */
.voucher-page {
    background: #ffffff;
    border: 1px solid #e5e5e5;
    border-radius: 20px;
    padding: 24px;
    box-shadow: 0 4px 20px rgba(0,0,0,.03);
}

/* ============================================================
   TITLE ROW
   ============================================================ */
.voucher-title {
    display: flex; justify-content: space-between;
    align-items: center; gap: 16px; margin-bottom: 22px;
    flex-wrap: wrap;
}
.voucher-title h2 {
    margin: 0; font-size: 24px; font-weight: 800; color: #000000;
    display: flex; align-items: center; gap: 10px;
}
.voucher-title h2 .title-icon {
    width: 40px; height: 40px; border-radius: 11px;
    background: #000000;
    color: #fff; font-size: 16px;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 12px rgba(0,0,0,.15);
}

/* ============================================================
   SEARCH
   ============================================================ */
.voucher-search {
    display: flex; gap: 8px;
}
.voucher-search input {
    width: 220px; height: 40px;
    border: 1px solid #d1d5db;
    border-radius: 20px; padding: 0 16px; /* Pill shape */
    outline: none; font-size: 14px;
    background: #ffffff; color: gray-dark; /* Samsung gray-dark */
    transition: border-color .2s, box-shadow .2s;
}
.voucher-search input::placeholder { color: #9ca3af; }
.voucher-search input:focus {
    border-color: gray-dark; /* Samsung gray-dark */
    box-shadow: 0 0 0 2px rgba(0,0,0,.1);
}
.voucher-search button {
    height: 40px; padding: 0 20px; border: none;
    border-radius: 20px; /* Pill shape */
    background: gray; /* Samsung gray-dark */
    color: #fff; font-weight: 700; font-size: 13.5px;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(0,0,0,.1);
    transition: background .2s, transform .15s, box-shadow .2s;
}
.voucher-search button:hover { 
    background: var(--samsung-gray-dark-hover); 
    transform: translateY(-1px); 
    box-shadow: 0 6px 14px rgba(0,0,0,.15); 
}
@media (max-width: 768px) {
    .voucher-title { flex-direction: column; align-items: stretch; }
    .voucher-search { flex-direction: column; }
    .voucher-search input { width: 100%; }
    .voucher-search button { height: 40px; }
}

/* ============================================================
   VOUCHER LIST
   ============================================================ */
.voucher-list {
    display: flex; flex-direction: column; gap: 14px;
}

/* ============================================================
   VOUCHER CARD — Clean White with subtle shadows
   ============================================================ */
.voucher-card {
    display: flex; align-items: stretch;
    background: #ffffff;
    border: 1px solid #e5e5e5;
    border-radius: 16px; overflow: hidden;
    min-height: 138px;
    box-shadow: 0 2px 10px rgba(0,0,0,.02);
    transition: transform .25s cubic-bezier(.16,1,.3,1), box-shadow .25s, border-color .25s;
    position: relative;
}
/* A sleek black top line instead of gray-dark gradient */
.voucher-card::after {
    content: ''; position: absolute; top: 0; left: 0; right: 0;
    height: 3px;
    background: #000000;
    border-radius: 16px 16px 0 0;
}
.voucher-card:hover {
    transform: translateY(-4px) scale(1.005);
    box-shadow: 0 10px 24px rgba(0,0,0,.06);
    border-color: #d1d5db;
}

/* LEFT panel */
.voucher-left {
    width: 130px; flex-shrink: 0;
    background: #111111; /* Sleek black */
    color: #fff;
    display: flex; flex-direction: column;
    justify-content: center; align-items: center; gap: 6px;
    position: relative;
    text-align: center;
}
/* scalloped edge - matching the white background of the card's center */
.voucher-left::after {
    content: '';
    position: absolute; right: -10px; top: 0;
    width: 20px; height: 100%;
    background: radial-gradient(circle at left, #ffffff 9px, transparent 10px);
    background-size: 20px 22px;
}
.voucher-left i    { font-size: 30px; opacity: .95; color: #ffffff; }
.voucher-left span { font-size: 13px; font-weight: 800; line-height: 1.4; letter-spacing: .3px; }

/* CENTER */
.voucher-center {
    flex: 1; padding: 16px 22px 16px 28px;
    display: flex; flex-direction: column; justify-content: center; gap: 4px;
}
.voucher-center h4 {
    margin: 0 0 8px; font-size: 20px; font-weight: 800; color: #000000;
}
.voucher-code-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 12px; border-radius: 8px;
    border: 1px dashed #9ca3af;
    background: #f8f9fa;
    color: #111111; font-weight: 800; font-size: 14px;
    margin-bottom: 8px; width: fit-content;
    letter-spacing: .5px;
    transition: background .2s, border-color .2s;
}
.voucher-code-badge:hover { background: #f3f4f6; border-color: #555555; }
.voucher-center p {
    margin: 0; font-size: 13.5px; color: #555555;
}
.voucher-center strong { color: #000000; font-weight: 700; }
.voucher-center small  { font-size: 12px; color: #9ca3af; }

.badge-personal {
    display: inline-flex; align-items: center; gap: 3px;
    margin-left: 8px; padding: 2px 8px;
    border-radius: 20px;
    background: #fffbea; color: #b45309;
    border: 1px solid #fde68a;
    font-size: 11px; font-weight: 700;
}

/* RIGHT panel */
.voucher-right {
    width: 140px; flex-shrink: 0;
    display: flex; justify-content: center; align-items: center;
    border-left: 1.5px dashed #e5e5e5;
    padding: 16px;
}
.btn-use {
    background: gray; /* Samsung gray-dark */
    color: #fff; border: none; border-radius: 20px; /* Pill shape */
    padding: 9px 14px; font-size: 13px; font-weight: 700;
    cursor: pointer; width: 100%;
    display: flex; align-items: center; justify-content: center; gap: 6px;
    transition: background .2s, transform .15s, box-shadow .2s;
    box-shadow: 0 4px 12px rgba(3,129,254,.2);
    position: relative; overflow: hidden;
    font-family: inherit;
}
.btn-use:hover { 
    background: gray-dark-hover; 
    transform: translateY(-1px); 
    box-shadow: 0 6px 16px rgba(3,129,254,.3); 
}
.btn-use.copied {
    background: #16a34a;
    box-shadow: 0 4px 12px rgba(22,163,74,.3);
}

/* ============================================================
   EMPTY STATE
   ============================================================ */
.voucher-empty {
    text-align: center; padding: 60px 20px;
}
.voucher-empty i {
    font-size: 64px; display: block; margin-bottom: 16px;
    color: #d1d5db;
    animation: emptyFloat 3s ease-in-out infinite;
}
@keyframes emptyFloat { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
.voucher-empty h3 { font-size: 20px; font-weight: 800; color: #000000; margin-bottom: 8px; }
.voucher-empty p  { color: #555555; font-size: 14px; margin-bottom: 22px; }
.btn-find {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 10px 24px; border-radius: 24px;
    background: #000000;
    color: #fff; font-size: 13.5px; font-weight: 700;
    text-decoration: none;
    transition: background .2s, transform .18s, box-shadow .2s;
}
.btn-find:hover { background: #333333; transform:translateY(-1px); color:#fff; box-shadow: 0 4px 14px rgba(0,0,0,.15); }

/* ============================================================
   PAGINATION
   ============================================================ */
.pagination-wrap { margin-top: 20px; }
.pagination-wrap .pagination .page-link {
    border: 1px solid #e5e5e5;
    color: #111111;
    background: #ffffff;
    border-radius: 8px !important;
    margin: 0 2px; font-weight: 600; font-size: 13.5px;
    transition: all .18s;
}
.pagination-wrap .pagination .page-link:hover { background: #f8f9fa; color: #000000; border-color: #d1d5db; }
.pagination-wrap .pagination .page-item.active .page-link {
    background: #000000;
    border-color: #000000; color: #fff;
    box-shadow: 0 4px 10px rgba(0,0,0,.15);
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 768px) {
    .voucher-card    { flex-direction: column; }
    .voucher-left    { width: 100%; height: 90px; flex-direction: row; gap: 10px; }
    .voucher-left::after { display: none; }
    .voucher-right   { width: 100%; border-left: none; border-top: 1.5px dashed #e5e5e5; padding: 14px; }
    .btn-use         { width: 100%; }
    .voucher-center  { padding: 16px; }
}
@media (max-width: 480px) {
    .voucher-page    { padding: 16px; }
    .voucher-center h4 { font-size: 17px; }
}
</style>
@endpush

@section('content')
<canvas id="sky-canvas" aria-hidden="true"></canvas>

<div class="voucher-page-wrap">
<div class="voucher-container">

    {{-- ===== SIDEBAR ===== --}}
    <div class="profile-sidebar-wrap reveal">
        @include('profile.sidebar')
    </div>

    {{-- ===== CONTENT ===== --}}
    <div>

        @if(session('success'))
        <div class="alert-sky alert-sky-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif

        <div class="voucher-page reveal">

            {{-- Title + Search --}}
            <div class="voucher-title">
                <h2>
                    <span class="title-icon"><i class="fas fa-ticket-alt"></i></span>
                    Kho Voucher
                </h2>
                <form action="{{ route('profile.voucher') }}" method="GET" class="voucher-search">
                    <input type="text" name="code" value="{{ $keyword ?? '' }}"
                           placeholder="Nhập mã voucher...">
                    <button type="submit">
                        <i class="fas fa-search" style="margin-right:4px"></i>Tìm
                    </button>
                </form>
            </div>

            {{-- Voucher List --}}
            @if($vouchers->count())
            <div class="voucher-list stagger-vouchers">
                @foreach($vouchers as $voucher)
                <div class="voucher-card">

                    {{-- LEFT --}}
                    <div class="voucher-left">
                        <i class="fas fa-ticket-alt"></i>
                        <span>Electronic<br>Shop</span>
                    </div>

                    {{-- CENTER --}}
                    <div class="voucher-center">
                        <h4>Giảm {{ $voucher->discount_percent }}%</h4>

                        <div class="voucher-code-badge" id="code-{{ $voucher->id }}">
                            <i class="fas fa-tag" style="font-size:11px"></i>
                            {{ $voucher->code }}
                        </div>

                        <p>Đơn tối thiểu:
                            <strong>{{ number_format($voucher->min_order_value) }}đ</strong>
                        </p>
                        <small>
                            @if($voucher->expires_at)
                                <i class="far fa-calendar-alt" style="margin-right:3px"></i>
                                HSD: {{ \Carbon\Carbon::parse($voucher->expires_at)->format('d/m/Y') }}
                            @else
                                <i class="fas fa-infinity" style="margin-right:3px"></i>Không giới hạn
                            @endif
                            @if($voucher->assigned_user_id)
                            <span class="badge-personal">
                                <i class="fas fa-user"></i> Dành riêng cho bạn
                            </span>
                            @endif
                        </small>
                    </div>

                    {{-- RIGHT --}}
                    <div class="voucher-right">
                        <button type="button" class="btn-use"
                                onclick="copyVoucher('{{ $voucher->code }}', this)">
                            <i class="fas fa-copy"></i> Sao chép mã
                        </button>
                    </div>

                </div>
                @endforeach
            </div>

            <div class="pagination-wrap reveal">
                {{ $vouchers->links() }}
            </div>

            @else
            <div class="voucher-empty reveal">
                <i class="fas fa-ticket-alt"></i>
                <h3>Không có voucher phù hợp</h3>
                <p>Hãy quay lại sau để nhận thêm nhiều ưu đãi hấp dẫn.</p>
                <a href="{{ route('products.index') }}" class="btn-find">
                    <i class="fas fa-store"></i> Mua sắm ngay
                </a>
            </div>
            @endif

        </div>{{-- /.voucher-page --}}

    </div>{{-- /.content --}}
</div>{{-- /.voucher-container --}}
</div>{{-- /.voucher-page-wrap --}}
@endsection

@push('scripts')
<script>
function copyVoucher(code, btn) {
    navigator.clipboard.writeText(code).then(function () {
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Đã sao chép!';
        btn.classList.add('copied');
        setTimeout(function () {
            btn.innerHTML = orig;
            btn.classList.remove('copied');
        }, 1800);
    }).catch(function () {
        /* fallback for older browsers */
        const ta = document.createElement('textarea');
        ta.value = code; document.body.appendChild(ta);
        ta.select(); document.execCommand('copy');
        document.body.removeChild(ta);
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Đã sao chép!';
        btn.classList.add('copied');
        setTimeout(() => { btn.innerHTML = orig; btn.classList.remove('copied'); }, 1800);
    });
}

(function () {
    /* ---- Canvas clouds (Subtle Gray theme) ---- */
    const canvas = document.getElementById('sky-canvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let W, H, clouds = [];
        function resize() { W = canvas.width = window.innerWidth; H = canvas.height = window.innerHeight; }
        window.addEventListener('resize', resize); resize();
        function makeCloud() {
            return { x: Math.random()*W*1.2, y: Math.random()*H*.6,
                     r: 50+Math.random()*110, dx: .13+Math.random()*.2, alpha: .01+Math.random()*.03 };
        }
        for (let i = 0; i < 8; i++) clouds.push(makeCloud());
        function drawCloud(c) {
            const g = ctx.createRadialGradient(c.x,c.y,0,c.x,c.y,c.r);
            g.addColorStop(0, `rgba(0,0,0,${c.alpha})`);
            g.addColorStop(.6, `rgba(0,0,0,${c.alpha*.6})`);
            g.addColorStop(1, 'rgba(0,0,0,0)');
            ctx.beginPath(); ctx.arc(c.x,c.y,c.r,0,Math.PI*2);
            ctx.fillStyle = g; ctx.fill();
            [-.5,.5].forEach(o => {
                ctx.beginPath();
                ctx.arc(c.x+c.r*.55*o, c.y-c.r*.18, c.r*.72, 0, Math.PI*2);
                ctx.fillStyle = `rgba(0,0,0,${c.alpha*.7})`; ctx.fill();
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
        const el = document.createElement('div'); el.className = 'bubble';
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
    }, { threshold: 0.05, rootMargin: '0px 0px -20px 0px' });
    document.querySelectorAll('.reveal, .stagger-vouchers').forEach(el => io.observe(el));

    /* ---- Ripple on copy button ---- */
    document.querySelectorAll('.btn-use').forEach(btn => {
        btn.addEventListener('click', function (e) {
            const r    = btn.getBoundingClientRect();
            const size = Math.max(r.width, r.height) * 1.8;
            const rip  = document.createElement('span');
            rip.className = 'ripple-wave';
            rip.style.cssText = [`width:${size}px`,`height:${size}px`,
                `left:${e.clientX-r.left-size/2}px`,
                `top:${e.clientY-r.top-size/2}px`].join(';');
            btn.appendChild(rip);
            rip.addEventListener('animationend', () => rip.remove());
        });
    });

})();
</script>
@endpush