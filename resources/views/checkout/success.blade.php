@extends('layouts.app')
@section('title', 'Đặt hàng thành công - ElectronicShop')

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
   SCROLL REVEAL & ANIMATIONS
   ============================================================ */
.reveal {
    opacity: 0; transform: translateY(28px);
    transition: opacity .65s cubic-bezier(.16,1,.3,1), transform .65s cubic-bezier(.16,1,.3,1);
}
.reveal.revealed { opacity: 1; transform: translateY(0); }

/* Success checkmark pop animation */
.success-icon-wrap {
    width: 90px; height: 90px; border-radius: 50%;
    background: linear-gradient(135deg, #16a34a, #4ade80);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 20px;
    box-shadow: 0 8px 30px rgba(22,163,74,.35);
    animation: iconPop .6s cubic-bezier(.34,1.56,.64,1);
}
@keyframes iconPop {
    0%   { transform: scale(0) rotate(-45deg); opacity: 0; }
    80%  { transform: scale(1.15) rotate(5deg); }
    100% { transform: scale(1) rotate(0); opacity: 1; }
}
.success-icon-wrap i { font-size: 46px; color: #fff; }

/* ripple */
.ripple-wave {
    position: absolute; border-radius: 50%;
    background: rgba(125,211,252,.3);
    transform: scale(0); animation: rippleOut .6s linear;
    pointer-events: none; z-index: 10;
}
@keyframes rippleOut { to { transform:scale(4); opacity:0; } }

/* ============================================================
   PAGE WRAPPER
   ============================================================ */
.success-page {
    min-height: 100vh;
    padding: 40px 0 70px;
    position: relative; z-index: 1;
}

.success-wrap {
    max-width: 700px;
    margin: 0 auto;
    padding: 0 16px;
    text-align: center;
    position: relative; z-index: 1;
}

.success-wrap h1 {
    font-size: 28px; font-weight: 800;
    color: #0c4a6e; margin-bottom: 10px;
}

.success-wrap p {
    color: #0369a1; font-size: 15.5px;
    margin-bottom: 28px; opacity: .88;
}

/* ============================================================
   ORDER CARD — glassmorphism
   ============================================================ */
.order-card {
    background: rgba(255,255,255,.84);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(186,230,253,.65);
    border-radius: 20px;
    box-shadow: 0 6px 28px rgba(14,165,233,.12);
    padding: 28px;
    text-align: left;
    margin-bottom: 28px;
    transition: box-shadow .3s;
}
.order-card:hover {
    box-shadow: 0 10px 36px rgba(14,165,233,.18);
}

.order-card .row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 11px 0;
    border-bottom: 1px solid rgba(186,230,253,.45);
    font-size: 14px; color: #0c4a6e;
}
.order-card .row:last-child { border-bottom: none; }

.order-card .label {
    color: #0369a1; font-weight: 600; opacity: .85;
}

.items-container {
    margin-top: 14px; padding: 12px 14px;
    background: rgba(186,230,253,.22);
    border-radius: 12px;
    border: 1px solid rgba(186,230,253,.4);
}

.item-row {
    display: flex; justify-content: space-between;
    font-size: 13.5px; padding: 6px 0;
    color: #0c4a6e; font-weight: 500;
}
.item-row + .item-row {
    border-top: 1px dashed rgba(186,230,253,.5);
}

.total-row {
    font-weight: 800; color: #0369a1; font-size: 17px;
    margin-top: 12px; padding-top: 14px;
    border-top: 2px solid rgba(186,230,253,.65) !important;
}

/* ============================================================
   STATUS BADGES
   ============================================================ */
.status-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 12px; border-radius: 12px;
    font-size: 12px; font-weight: 700;
}
.status-paid {
    background: rgba(220,252,231,.9); color: #166534;
    border: 1px solid rgba(187,247,208,.8);
}
.status-unpaid {
    background: rgba(254,243,199,.9); color: #b45309;
    border: 1px solid rgba(253,230,138,.8);
}

/* ============================================================
   BUTTON GROUP
   ============================================================ */
.btn-group {
    display: flex; justify-content: center; gap: 14px;
    flex-wrap: wrap;
}
.btn-group a {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 13px 28px; border-radius: 12px;
    font-weight: 700; font-size: 14.5px;
    text-decoration: none;
    transition: opacity .2s, transform .18s, box-shadow .2s;
    position: relative; overflow: hidden;
}

.btn-primary {
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    color: #fff;
    box-shadow: 0 4px 18px rgba(14,165,233,.35);
}
.btn-primary::after {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,.28) 50%, transparent 60%);
    transform: translateX(-120%); transition: transform .5s ease; pointer-events: none;
}
.btn-primary:hover::after { transform: translateX(120%); }
.btn-primary:hover {
    opacity: .92; transform: translateY(-2px); color: #fff;
    box-shadow: 0 8px 24px rgba(14,165,233,.45);
}

.btn-outline {
    border: 2px solid #0ea5e9;
    color: #0369a1;
    background: rgba(255,255,255,.75);
    backdrop-filter: blur(6px);
}
.btn-outline:hover {
    background: rgba(186,230,253,.45);
    transform: translateY(-2px); color: #0c4a6e;
    box-shadow: 0 4px 16px rgba(14,165,233,.18);
}
</style>
@endpush

@section('content')
{{-- Sky Canvas --}}
<canvas id="sky-canvas" aria-hidden="true"></canvas>

<div class="success-page">
<div class="success-wrap">

    <div class="success-icon-wrap">
        <i class="fas fa-check"></i>
    </div>

    <h1 class="reveal">Đặt hàng thành công!</h1>
    <p class="reveal">Cảm ơn bạn đã mua sắm tại ElectronicShop. Đơn hàng <strong>#{{ $order->id }}</strong> đang được xử lý.</p>

    <div class="order-card reveal">
        <div class="row">
            <span class="label">Mã đơn hàng</span>
            <strong style="color:#0c4a6e">#{{ $order->id }}</strong>
        </div>
        <div class="row">
            <span class="label">Người nhận</span>
            <span>{{ $order->address->full_name ?? '' }} — {{ $order->address->phone ?? '' }}</span>
        </div>
        <div class="row">
            <span class="label">Địa chỉ</span>
            <span>{{ $order->address->full_address ?? '' }}</span>
        </div>
        <div class="row">
            <span class="label">Phương thức thanh toán</span>
            <span>{{ $order->payment_method === 'momo' ? 'Ví MoMo' : 'Thanh toán khi nhận hàng (COD)' }}</span>
        </div>
        <div class="row">
            <span class="label">Trạng thái thanh toán</span>
            <span class="status-badge {{ $order->payment_status === 'paid' ? 'status-paid' : 'status-unpaid' }}">
                <i class="fas {{ $order->payment_status === 'paid' ? 'fa-check-circle' : 'fa-clock' }}"></i>
                {{ $order->payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
            </span>
        </div>

        <div class="items-container">
            @foreach($order->items as $item)
            <div class="item-row">
                <span>{{ $item->product_name }} × {{ $item->quantity }}</span>
                <span>{{ number_format($item->total_price) }}đ</span>
            </div>
            @endforeach
        </div>

        <div class="row total-row">
            <span>Tổng cộng</span>
            <span>{{ number_format($order->total) }}đ</span>
        </div>
    </div>

    <div class="btn-group reveal">
        <a href="{{ route('products.index') }}" class="btn-outline">
            <i class="fas fa-store"></i> Tiếp tục mua sắm
        </a>
        <a href="{{ route('home') }}" class="btn-primary">
            <i class="fas fa-home"></i> Về trang chủ
        </a>
    </div>

</div>
</div>
@endsection

@push('scripts')
<script>
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
                     r: 50+Math.random()*110, dx: .13+Math.random()*.2,
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
    }, { threshold: 0.06, rootMargin: '0px 0px -20px 0px' });
    document.querySelectorAll('.reveal').forEach(el => io.observe(el));

    /* ---- Ripple on buttons ---- */
    document.querySelectorAll('.btn-group a').forEach(btn => {
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
