@extends('layouts.app')
@section('title', 'Kho Voucher - ElectronicShop')

@push('styles')
<style>
/* ============================================================
   PAGE BACKGROUND — sky gradient
   ============================================================ */
body {
    background: linear-gradient(180deg,
        #bae6fd 0%, #e0f2fe 18%, #f0f9ff 38%,
        #e0f2fe 62%, #bae6fd 100%) fixed;
    background-attachment: fixed;
}
#sky-canvas {
    position: fixed; inset: 0; width: 100%; height: 100%;
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
    background: rgba(125,211,252,.28);
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
}
@keyframes alertIn { from{opacity:0;transform:translateY(-10px)} to{opacity:1;transform:none} }
.alert-sky-success { background: rgba(220,252,231,.9); color: #166534; border: 1px solid rgba(187,247,208,.8); }

/* ============================================================
   MAIN CONTENT WRAPPER — glassmorphism
   ============================================================ */
.voucher-page {
    background: rgba(255,255,255,.78);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(186,230,253,.65);
    border-radius: 20px;
    padding: 24px;
    box-shadow: 0 6px 28px rgba(14,165,233,.1);
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
    margin: 0; font-size: 24px; font-weight: 800; color: #0c4a6e;
    display: flex; align-items: center; gap: 10px;
}
.voucher-title h2 .title-icon {
    width: 40px; height: 40px; border-radius: 11px;
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    color: #fff; font-size: 16px;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 14px rgba(14,165,233,.35);
}

/* ============================================================
   SEARCH
   ============================================================ */
.voucher-search {
    display: flex; gap: 8px;
}
.voucher-search input {
    width: 220px; height: 40px;
    border: 1px solid rgba(125,211,252,.55);
    border-radius: 10px; padding: 0 14px;
    outline: none; font-size: 14px;
    background: rgba(255,255,255,.8); color: #0c4a6e;
    transition: border-color .2s, box-shadow .2s;
}
.voucher-search input::placeholder { color: #7dd3fc; }
.voucher-search input:focus {
    border-color: #0ea5e9;
    box-shadow: 0 0 0 3px rgba(14,165,233,.15);
}
.voucher-search button {
    height: 40px; padding: 0 18px; border: none;
    border-radius: 10px;
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    color: #fff; font-weight: 700; font-size: 13.5px;
    cursor: pointer;
    box-shadow: 0 3px 12px rgba(14,165,233,.3);
    transition: opacity .2s, transform .15s;
}
.voucher-search button:hover { opacity:.9; transform:translateY(-1px); }
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
   VOUCHER CARD — glassmorphism with dashed divider
   ============================================================ */
.voucher-card {
    display: flex; align-items: stretch;
    background: rgba(255,255,255,.88);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(186,230,253,.6);
    border-radius: 16px; overflow: hidden;
    min-height: 138px;
    box-shadow: 0 3px 14px rgba(14,165,233,.08);
    transition: transform .25s cubic-bezier(.16,1,.3,1), box-shadow .25s, border-color .25s;
    position: relative;
}
.voucher-card::after {
    content: ''; position: absolute; top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, #0369a1, #38bdf8);
    border-radius: 16px 16px 0 0;
}
.voucher-card:hover {
    transform: translateY(-5px) scale(1.005);
    box-shadow: 0 14px 32px rgba(14,165,233,.18);
    border-color: #7dd3fc;
}

/* LEFT panel */
.voucher-left {
    width: 130px; flex-shrink: 0;
    background: linear-gradient(160deg, #0369a1 0%, #0ea5e9 55%, #38bdf8 100%);
    color: #fff;
    display: flex; flex-direction: column;
    justify-content: center; align-items: center; gap: 6px;
    position: relative;
    text-align: center;
}
/* scalloped edge */
.voucher-left::after {
    content: '';
    position: absolute; right: -10px; top: 0;
    width: 20px; height: 100%;
    background: radial-gradient(circle at left, rgba(240,249,255,.92) 9px, transparent 10px);
    background-size: 20px 22px;
}
.voucher-left i    { font-size: 30px; opacity: .95; }
.voucher-left span { font-size: 13px; font-weight: 800; line-height: 1.4; letter-spacing: .3px; }

/* CENTER */
.voucher-center {
    flex: 1; padding: 16px 22px 16px 28px;
    display: flex; flex-direction: column; justify-content: center; gap: 4px;
}
.voucher-center h4 {
    margin: 0 0 8px; font-size: 20px; font-weight: 800; color: #0c4a6e;
}
.voucher-code-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 12px; border-radius: 8px;
    border: 1.5px dashed #0ea5e9;
    background: rgba(186,230,253,.28);
    color: #0369a1; font-weight: 800; font-size: 14px;
    margin-bottom: 8px; width: fit-content;
    letter-spacing: .5px;
    transition: background .2s;
}
.voucher-code-badge:hover { background: rgba(186,230,253,.5); }
.voucher-center p {
    margin: 0; font-size: 13.5px; color: #0369a1; opacity: .85;
}
.voucher-center strong { color: #0c4a6e; font-weight: 700; }
.voucher-center small  { font-size: 12px; color: #7dd3fc; }

.badge-personal {
    display: inline-flex; align-items: center; gap: 3px;
    margin-left: 8px; padding: 2px 8px;
    border-radius: 20px;
    background: rgba(251,191,36,.2); color: #b45309;
    border: 1px solid rgba(251,191,36,.4);
    font-size: 11px; font-weight: 700;
}

/* RIGHT panel */
.voucher-right {
    width: 140px; flex-shrink: 0;
    display: flex; justify-content: center; align-items: center;
    border-left: 1.5px dashed rgba(186,230,253,.7);
    padding: 16px;
}
.btn-use {
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    color: #fff; border: none; border-radius: 10px;
    padding: 9px 14px; font-size: 13px; font-weight: 700;
    cursor: pointer; width: 100%;
    display: flex; align-items: center; justify-content: center; gap: 6px;
    transition: opacity .2s, transform .15s, box-shadow .2s;
    box-shadow: 0 3px 12px rgba(14,165,233,.3);
    position: relative; overflow: hidden;
    font-family: inherit;
}
.btn-use::after {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,.28) 50%, transparent 60%);
    transform: translateX(-120%); transition: transform .5s ease; pointer-events: none;
}
.btn-use:hover::after { transform: translateX(120%); }
.btn-use:hover { opacity:.9; transform:translateY(-1px); box-shadow: 0 6px 18px rgba(14,165,233,.4); }
.btn-use.copied {
    background: linear-gradient(135deg, #16a34a, #22c55e);
    box-shadow: 0 3px 12px rgba(22,163,74,.3);
}

/* ============================================================
   EMPTY STATE
   ============================================================ */
.voucher-empty {
    text-align: center; padding: 60px 20px;
}
.voucher-empty i {
    font-size: 64px; display: block; margin-bottom: 16px;
    background: linear-gradient(135deg, #7dd3fc, #bae6fd);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    animation: emptyFloat 3s ease-in-out infinite;
}
@keyframes emptyFloat { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
.voucher-empty h3 { font-size: 20px; font-weight: 800; color: #0c4a6e; margin-bottom: 8px; }
.voucher-empty p  { color: #0369a1; font-size: 14px; opacity: .8; margin-bottom: 22px; }
.btn-find {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 20px; border-radius: 10px;
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    color: #fff; font-size: 13px; font-weight: 700;
    text-decoration: none;
    box-shadow: 0 3px 12px rgba(14,165,233,.3);
    transition: opacity .2s, transform .18s;
}
.btn-find:hover { opacity:.9; transform:translateY(-1px); color:#fff; }

/* ============================================================
   PAGINATION
   ============================================================ */
.pagination-wrap { margin-top: 20px; }
.pagination-wrap .pagination .page-link {
    border: 1px solid rgba(186,230,253,.6);
    color: #0369a1;
    background: rgba(255,255,255,.75);
    backdrop-filter: blur(6px);
    border-radius: 8px !important;
    margin: 0 2px; font-weight: 600; font-size: 13.5px;
    transition: all .18s;
}
.pagination-wrap .pagination .page-link:hover { background: rgba(186,230,253,.5); color: #0c4a6e; }
.pagination-wrap .pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    border-color: transparent; color: #fff;
    box-shadow: 0 3px 10px rgba(14,165,233,.3);
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 768px) {
    .voucher-card    { flex-direction: column; }
    .voucher-left    { width: 100%; height: 90px; flex-direction: row; gap: 10px; }
    .voucher-left::after { display: none; }
    .voucher-right   { width: 100%; border-left: none; border-top: 1.5px dashed rgba(186,230,253,.7); padding: 14px; }
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
    /* ---- Canvas clouds ---- */
    const canvas = document.getElementById('sky-canvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let W, H, clouds = [];
        function resize() { W = canvas.width = window.innerWidth; H = canvas.height = window.innerHeight; }
        window.addEventListener('resize', resize); resize();
        function makeCloud() {
            return { x: Math.random()*W*1.2, y: Math.random()*H*.6,
                     r: 50+Math.random()*110, dx: .13+Math.random()*.2, alpha: .05+Math.random()*.1 };
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
