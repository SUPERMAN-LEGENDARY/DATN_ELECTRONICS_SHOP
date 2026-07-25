@extends('layouts.app')
@section('title', 'So sánh sản phẩm - ElectronicShop')
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
    transition: opacity .6s cubic-bezier(.16,1,.3,1), transform .6s cubic-bezier(.16,1,.3,1);
}
.reveal.revealed { opacity: 1; transform: translateY(0); }

.stagger-cards > * {
    opacity: 0; transform: translateY(36px) scale(.97);
    transition: opacity .55s cubic-bezier(.16,1,.3,1), transform .55s cubic-bezier(.16,1,.3,1);
}
.stagger-cards.revealed > *:nth-child(1) { opacity:1; transform:none; transition-delay:.05s; }
.stagger-cards.revealed > *:nth-child(2) { opacity:1; transform:none; transition-delay:.15s; }
.stagger-cards.revealed > *:nth-child(3) { opacity:1; transform:none; transition-delay:.25s; }
.stagger-cards.revealed > *:nth-child(4) { opacity:1; transform:none; transition-delay:.35s; }

/* ripple */
.ripple-wave {
    position: absolute; border-radius: 50%;
    background: rgba(125,211,252,.25);
    transform: scale(0); animation: rippleOut .6s linear;
    pointer-events: none; z-index: 10;
}
@keyframes rippleOut { to { transform:scale(4); opacity:0; } }

/* ============================================================
   PAGE WRAPPER
   ============================================================ */
.compare-page {
    min-height: 100vh;
    padding: 36px 0 70px;
    position: relative; z-index: 1;
}

/* ============================================================
   PAGE TITLE
   ============================================================ */
.compare-title {
    text-align: center;
    font-size: 32px; font-weight: 800;
    color: #0c4a6e; margin-bottom: 32px;
    display: flex; align-items: center; justify-content: center; gap: 12px;
}
.compare-title i {
    width: 52px; height: 52px; border-radius: 14px;
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    color: #fff; font-size: 22px;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 16px rgba(14,165,233,.35);
}

/* ============================================================
   ALERT
   ============================================================ */
.alert-success {
    display: flex; align-items: center; gap: 10px;
    background: rgba(220,252,231,.9); backdrop-filter: blur(8px);
    color: #166534; border: 1px solid rgba(187,247,208,.8);
    padding: 14px 18px; border-radius: 12px;
    margin-bottom: 28px; font-weight: 600; font-size: 14px;
    animation: alertIn .4s cubic-bezier(.16,1,.3,1);
}
@keyframes alertIn { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }

/* ============================================================
   EMPTY STATE
   ============================================================ */
.compare-empty {
    background: rgba(255,255,255,.82);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(186,230,253,.6);
    border-radius: 20px; padding: 80px 30px;
    text-align: center;
    box-shadow: 0 6px 28px rgba(14,165,233,.12);
}
.compare-empty-icon {
    font-size: 76px;
    background: linear-gradient(135deg, #0369a1, #38bdf8);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    display: block; margin-bottom: 20px;
    animation: emptyFloat 3s ease-in-out infinite;
}
@keyframes emptyFloat {
    0%,100% { transform: translateY(0);  }
    50%      { transform: translateY(-12px); }
}
.compare-empty h2 { font-size: 28px; font-weight: 800; color: #0c4a6e; margin-bottom: 12px; }
.compare-empty p  { color: #0369a1; margin-bottom: 24px; opacity: .8; }

/* ============================================================
   PRIMARY BUTTON (shared)
   ============================================================ */
.btn-primary {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    color: #fff; padding: 12px 28px; border-radius: 10px;
    text-decoration: none; font-weight: 700; font-size: 14px;
    transition: opacity .2s, transform .18s, box-shadow .2s;
    box-shadow: 0 4px 16px rgba(14,165,233,.35);
}
.btn-primary:hover {
    opacity: .9; transform: translateY(-2px); color: #fff;
    box-shadow: 0 8px 22px rgba(14,165,233,.45);
}

/* ============================================================
   COMPARE HEADER — product cards
   ============================================================ */
.compare-header {
    display: flex; justify-content: center;
    gap: 28px; flex-wrap: wrap; margin-bottom: 36px;
}

/* ============================================================
   COMPARE CARD — glassmorphism
   ============================================================ */
.compare-card {
    width: 300px;
    background: rgba(255,255,255,.82);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(186,230,253,.65);
    border-radius: 18px; padding: 24px;
    box-shadow: 0 6px 24px rgba(14,165,233,.1);
    transition: transform .28s cubic-bezier(.16,1,.3,1),
                box-shadow .28s, border-color .25s;
    position: relative; overflow: hidden;
}

/* top gradient bar */
.compare-card::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, #0369a1, #38bdf8);
    border-radius: 18px 18px 0 0;
}

.compare-card:hover {
    transform: translateY(-10px) scale(1.01);
    box-shadow: 0 16px 40px rgba(14,165,233,.22);
    border-color: #7dd3fc;
}

.compare-image {
    display: flex; justify-content: center; align-items: center;
    height: 200px;
    background: linear-gradient(160deg, #f0f9ff, #e0f2fe);
    border-radius: 12px; margin-bottom: 16px;
    overflow: hidden;
}
.compare-image img {
    width: 175px; height: 175px; object-fit: contain;
    transition: transform .35s cubic-bezier(.16,1,.3,1);
}
.compare-card:hover .compare-image img { transform: scale(1.08); }
.compare-image i { color: #7dd3fc; }

.compare-card h3 {
    font-size: 15px; font-weight: 700;
    color: #0c4a6e; margin: 0 0 10px;
    min-height: 42px;
    display: -webkit-box; -webkit-line-clamp: 2;
    -webkit-box-orient: vertical; overflow: hidden;
    line-height: 1.45;
}

.price {
    font-size: 24px; font-weight: 800;
    color: #0369a1; margin-bottom: 4px;
}

.old-price {
    font-size: 13px; color: #7dd3fc;
    text-decoration: line-through; margin-bottom: 16px;
}

.card-buttons { display: flex; flex-direction: column; gap: 10px; }

.buy-btn {
    display: flex; justify-content: center; align-items: center; gap: 8px;
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    color: #fff; border-radius: 10px; padding: 11px;
    text-decoration: none; font-weight: 700; font-size: 14px;
    transition: opacity .2s, transform .15s, box-shadow .2s;
    box-shadow: 0 4px 12px rgba(14,165,233,.3);
    position: relative; overflow: hidden;
}
.buy-btn::after {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,.3) 50%, transparent 60%);
    transform: translateX(-120%); transition: transform .5s ease; pointer-events: none;
}
.buy-btn:hover::after { transform: translateX(120%); }
.buy-btn:hover { opacity:.9; transform: translateY(-1px); color:#fff; box-shadow: 0 6px 18px rgba(14,165,233,.4); }

.remove-btn {
    width: 100%; border: none;
    background: rgba(239,68,68,.1);
    color: #ef4444;
    border: 1px solid rgba(239,68,68,.25);
    padding: 10px; border-radius: 10px;
    cursor: pointer; font-weight: 700; font-size: 14px;
    transition: background .2s, transform .15s, box-shadow .2s;
    display: flex; align-items: center; justify-content: center; gap: 6px;
}
.remove-btn:hover {
    background: #ef4444; color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(239,68,68,.3);
}

/* ============================================================
   ADD CARD — dashed glassmorphism
   ============================================================ */
.compare-add {
    display: flex; flex-direction: column;
    justify-content: center; align-items: center;
    gap: 8px;
    background: rgba(255,255,255,.55);
    backdrop-filter: blur(10px);
    border: 2.5px dashed rgba(125,211,252,.6) !important;
    color: #0369a1;
    transition: background .2s, border-color .2s !important;
}
.compare-add::before { display: none; }
.compare-add:hover {
    background: rgba(255,255,255,.72) !important;
    border-color: #0ea5e9 !important;
    transform: translateY(-6px) scale(1.01) !important;
}
.compare-add .add-icon {
    font-size: 52px;
    background: linear-gradient(135deg, #0369a1, #38bdf8);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    margin-bottom: 10px;
    animation: addPulse 2.4s ease-in-out infinite;
}
@keyframes addPulse {
    0%,100% { transform: scale(1);    opacity: .9; }
    50%      { transform: scale(1.1); opacity: 1;  }
}
.compare-add h3 { color: #0c4a6e; font-size: 16px; }
.compare-add p  { color: #0369a1; opacity:.75; font-size: 13px; text-align: center; margin: 0; }

/* ============================================================
   COMPARE TABLE — glassmorphism
   ============================================================ */
.compare-table {
    overflow: auto;
    border-radius: 16px;
    box-shadow: 0 6px 28px rgba(14,165,233,.1);
}

.compare-table table {
    width: 100%;
    border-collapse: separate; border-spacing: 0;
    background: rgba(255,255,255,.85);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border-radius: 16px; overflow: hidden;
    border: 1px solid rgba(186,230,253,.6);
    min-width: 600px;
}

.compare-table thead th {
    background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 100%);
    color: #fff; padding: 16px 18px;
    font-size: 14.5px; font-weight: 700;
    white-space: nowrap; letter-spacing: .2px;
    position: sticky; top: 0; z-index: 10;
}
.compare-table thead th:first-child {
    background: linear-gradient(135deg, #0c4a6e, #0369a1);
    text-align: left;
}

.compare-table td {
    padding: 14px 16px;
    border-bottom: 1px solid rgba(186,230,253,.4);
    text-align: center;
    font-size: 14px; color: #0c4a6e;
    transition: background .15s;
}

.compare-table tbody tr:nth-child(even) td { background: rgba(224,242,254,.25); }
.compare-table tbody tr:hover td { background: rgba(186,230,253,.3); }
.compare-table tbody tr:last-child td { border-bottom: none; }

.attribute-name {
    text-align: left !important;
    font-weight: 700; color: #0c4a6e;
    background: rgba(186,230,253,.18) !important;
    min-width: 200px; white-space: nowrap;
}
.compare-table tbody tr:hover .attribute-name { background: rgba(186,230,253,.3) !important; }

.empty-cell { color: #bae6fd; }

/* ============================================================
   DIFF HIGHLIGHT
   ============================================================ */
.diff {
    background: rgba(187,247,208,.55) !important;
    color: #166534; font-weight: 700;
    position: relative;
}
.diff::after {
    content: '';
    position: absolute; left: 0; top: 0; bottom: 0;
    width: 3px;
    background: linear-gradient(180deg, #16a34a, #4ade80);
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 992px) {
    .compare-header { flex-direction: column; align-items: center; }
    .compare-card   { width: 100%; max-width: 420px; }
}
@media (max-width: 768px) {
    .compare-title  { font-size: 24px; }
    .compare-image  { height: 160px; }
    .compare-image img { width: 140px; height: 140px; }
    .price          { font-size: 20px; }
    .compare-table th, .compare-table td { padding: 10px 12px; font-size: 13px; }
    .attribute-name { min-width: 140px; }
}
@media (max-width: 576px) {
    .compare-page   { padding: 20px 0; }
    .compare-card   { padding: 18px; }
}
</style>
@endpush

@section('content')
{{-- Sky Canvas --}}
<canvas id="sky-canvas" aria-hidden="true"></canvas>

<section class="compare-page">
    <div class="container" style="position:relative;z-index:1">

        <h1 class="compare-title reveal">
            <i class="fas fa-code-compare"></i>
            So sánh sản phẩm
        </h1>

        @if(session('success'))
        <div class="alert-success reveal">
            <i class="fas fa-circle-check"></i>
            {{ session('success') }}
        </div>
        @endif

        @if($products->count() == 0)

        {{-- ===== EMPTY STATE ===== --}}
        <div class="compare-empty reveal">
            <i class="fas fa-mobile-screen compare-empty-icon"></i>
            <h2>Chưa có sản phẩm để so sánh</h2>
            <p>Hãy chọn tối đa 3 sản phẩm để bắt đầu so sánh.</p>
            <a href="{{ route('products.index') }}" class="btn-primary">
                <i class="fas fa-store"></i> Xem sản phẩm
            </a>
        </div>

        @else

        {{-- ===== PRODUCT CARDS ===== --}}
        <div class="compare-header stagger-cards">

            @foreach($products as $product)
            <div class="compare-card">
                <div class="compare-image">
                    @if($product->first_image)
                        <img src="{{ $product->first_image }}" alt="{{ $product->name }}">
                    @else
                        <i class="fas fa-image fa-3x"></i>
                    @endif
                </div>

                <h3>{{ $product->name }}</h3>

                <div class="price">{{ number_format($product->sale_price) }}đ</div>
                @if($product->discount_percent > 0)
                <div class="old-price">{{ number_format($product->price) }}đ</div>
                @endif

                <div class="card-buttons">
                    <a href="{{ route('products.show', $product->slug) }}" class="buy-btn">
                        <i class="fas fa-cart-shopping"></i> Mua ngay
                    </a>
                    <form action="{{ route('compare.remove', $product) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="remove-btn" type="submit">
                            <i class="fas fa-trash"></i> Xóa
                        </button>
                    </form>
                </div>
            </div>
            @endforeach

            @if($products->count() < 3)
            <div class="compare-card compare-add">
                <i class="fas fa-plus-circle add-icon"></i>
                <h3>Chọn thêm sản phẩm</h3>
                <p>Bạn có thể so sánh tối đa 3 sản phẩm.</p>
                <a href="{{ route('products.index') }}" class="btn-primary" style="margin-top:10px">
                    <i class="fas fa-store"></i> Chọn sản phẩm
                </a>
            </div>
            @endif

        </div>

        {{-- ===== COMPARISON TABLE ===== --}}
        <div class="compare-table reveal">
            <table>
                <thead>
                    <tr>
                        <th width="200">Thông số</th>
                        @foreach($products as $product)
                            <th>{{ $product->name }}</th>
                        @endforeach
                        @for($i = $products->count(); $i < 3; $i++)
                            <th>Sản phẩm thứ {{ $i + 1 }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="attribute-name">Danh mục</td>
                        @foreach($products as $product)
                            <td>{{ $product->category->name ?? '-' }}</td>
                        @endforeach
                        @for($i = $products->count(); $i < 3; $i++)
                            <td class="empty-cell">—</td>
                        @endfor
                    </tr>
                    <tr>
                        <td class="attribute-name">Thương hiệu</td>
                        @foreach($products as $product)
                            <td>{{ $product->brand->name ?? '-' }}</td>
                        @endforeach
                        @for($i = $products->count(); $i < 3; $i++)
                            <td class="empty-cell">—</td>
                        @endfor
                    </tr>

                    @foreach($attributes as $attribute)
                    <tr>
                        <td class="attribute-name">{{ $attribute->name }}</td>
                        @foreach($products as $product)
                        @php
                            $value = optional(
                                $product->attributes
                                    ->where('attribute_id', $attribute->id)
                                    ->first()
                            )->value;
                        @endphp
                        <td>{{ $value ?? '-' }}</td>
                        @endforeach
                        @for($i = $products->count(); $i < 3; $i++)
                            <td class="empty-cell">—</td>
                        @endfor
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @endif

    </div>
</section>
@endsection

@push('scripts')
<script>
/* ============================================================
   TABLE LOGIC — highlight diff rows
   ============================================================ */
document.addEventListener('DOMContentLoaded', function () {

    /* Highlight các thông số khác nhau */
    document.querySelectorAll('.compare-table tbody tr').forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length <= 2) return;
        const values = [];
        for (let i = 1; i < cells.length; i++) {
            const v = cells[i].innerText.trim();
            if (v !== '—') values.push(v);
        }
        const hasReal = values.length >= 2;
        const unique  = new Set(values);
        if (hasReal && unique.size > 1) {
            for (let i = 1; i < cells.length; i++) {
                cells[i].classList.add('diff');
            }
        }
    });

    /* Đồng bộ chiều cao card */
    const cards = document.querySelectorAll('.compare-card:not(.compare-add)');
    let maxH = 0;
    cards.forEach(c => { if (c.offsetHeight > maxH) maxH = c.offsetHeight; });
    cards.forEach(c => c.style.minHeight = maxH + 'px');
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
    }, { threshold: 0.07, rootMargin: '0px 0px -30px 0px' });
    document.querySelectorAll('.reveal, .stagger-cards').forEach(el => io.observe(el));

    /* ---- 3D Tilt on product cards ---- */
    document.querySelectorAll('.compare-card:not(.compare-add)').forEach(card => {
        card.addEventListener('mousemove', function (e) {
            const r = card.getBoundingClientRect();
            const dx = (e.clientX-r.left-r.width/2)/(r.width/2);
            const dy = (e.clientY-r.top-r.height/2)/(r.height/2);
            card.style.transform = `perspective(600px) rotateX(${-dy*5}deg) rotateY(${dx*5}deg) translateY(-10px) scale(1.01)`;
        });
        card.addEventListener('mouseleave', function () {
            card.style.transform = '';
            card.style.transition = 'transform .4s cubic-bezier(.16,1,.3,1), box-shadow .28s, border-color .25s';
            setTimeout(() => card.style.transition = '', 420);
        });
    });

    /* ---- Ripple on buy buttons ---- */
    document.querySelectorAll('.buy-btn, .btn-primary').forEach(btn => {
        btn.addEventListener('click', function (e) {
            const r    = btn.getBoundingClientRect();
            const size = Math.max(r.width, r.height)*1.8;
            const rip  = document.createElement('span');
            rip.className = 'ripple-wave';
            rip.style.cssText = [`width:${size}px`,`height:${size}px`,
                `left:${e.clientX-r.left-size/2}px`,
                `top:${e.clientY-r.top-size/2}px`].join(';');
            btn.appendChild(rip);
            rip.addEventListener('animationend', () => rip.remove());
        });
    });

    /* ---- Table row hover glow ---- */
    document.querySelectorAll('.compare-table tbody tr').forEach(row => {
        row.addEventListener('mouseenter', function () {
            this.style.transition = 'background .15s';
        });
    });

    /* ---- Sticky thead on scroll ---- */
    const table = document.querySelector('.compare-table table');
    if (table) {
        const thead = table.querySelector('thead');
        window.addEventListener('scroll', () => {
            const r = table.getBoundingClientRect();
            if (r.top < 70 && r.bottom > 120) {
                thead.style.boxShadow = '0 4px 20px rgba(14,165,233,.25)';
            } else {
                thead.style.boxShadow = '';
            }
        }, { passive: true });
    }

    /* ---- Diff badge pulse ---- */
    document.querySelectorAll('.diff').forEach((cell, i) => {
        cell.style.animationDelay = (i * .05) + 's';
    });

})();
</script>
@endpush
