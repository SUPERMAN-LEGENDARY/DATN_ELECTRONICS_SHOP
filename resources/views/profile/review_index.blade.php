@extends('layouts.app')
@section('title', 'Đánh giá của tôi - ElectronicShop')

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

.stagger-reviews > * {
    opacity: 0; transform: translateY(20px);
    transition: opacity .5s cubic-bezier(.16,1,.3,1), transform .5s cubic-bezier(.16,1,.3,1);
}
.stagger-reviews.revealed > *:nth-child(1)  { opacity:1; transform:none; transition-delay:.04s; }
.stagger-reviews.revealed > *:nth-child(2)  { opacity:1; transform:none; transition-delay:.10s; }
.stagger-reviews.revealed > *:nth-child(3)  { opacity:1; transform:none; transition-delay:.16s; }
.stagger-reviews.revealed > *:nth-child(4)  { opacity:1; transform:none; transition-delay:.22s; }
.stagger-reviews.revealed > *:nth-child(n+5){ opacity:1; transform:none; transition-delay:.28s; }

/* ============================================================
   PAGE WRAPPER
   ============================================================ */
.myreviews-page {
    min-height: 100vh;
    padding: 32px 0 60px;
    position: relative; z-index: 1;
}
.myreviews-container {
    max-width: 1200px; margin: 0 auto; padding: 0 16px;
    display: grid; grid-template-columns: 260px 1fr;
    gap: 24px; align-items: start;
    position: relative; z-index: 1;
}
@media (max-width: 991px) { .myreviews-container { grid-template-columns: 1fr; } }
.profile-sidebar-wrap { position: sticky; top: 88px; }

/* ============================================================
   MAIN CARD — glassmorphism
   ============================================================ */
.myreviews-card {
    background: rgba(255,255,255,.82);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(186,230,253,.65);
    border-radius: 20px; overflow: hidden;
    box-shadow: 0 6px 28px rgba(14,165,233,.1);
}

.myreviews-card-header {
    background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
    padding: 18px 24px;
    border-bottom: 1px solid rgba(186,230,253,.55);
    display: flex; align-items: center; gap: 10px;
}
.myreviews-card-header h4 {
    margin: 0; font-size: 17px; font-weight: 800; color: #0c4a6e;
    display: flex; align-items: center; gap: 10px;
}
.myreviews-card-header .header-icon {
    width: 32px; height: 32px; border-radius: 9px;
    background: linear-gradient(135deg, #d97706, #f59e0b);
    color: #fff; font-size: 14px;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 3px 10px rgba(245,158,11,.35);
}

.myreviews-card-body { padding: 22px; }

/* ============================================================
   REVIEW ITEM CARD
   ============================================================ */
.review-item {
    background: rgba(255,255,255,.86);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(186,230,253,.6) !important;
    border-radius: 16px !important;
    padding: 18px 20px;
    margin-bottom: 16px;
    position: relative;
    transition: transform .25s cubic-bezier(.16,1,.3,1), box-shadow .25s, border-color .25s;
    box-shadow: 0 3px 14px rgba(14,165,233,.08);
}
/* amber-sky top bar */
.review-item::before {
    content: ''; position: absolute; top:0; left:0; right:0; height:3px;
    background: linear-gradient(90deg, #f59e0b, #fbbf24, #38bdf8);
    border-radius: 16px 16px 0 0;
}
.review-item:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(14,165,233,.16);
    border-color: #7dd3fc !important;
}
.review-item:last-child { margin-bottom: 0; }

/* product row */
.review-product-row { display: flex; gap: 14px; align-items: flex-start; }

.review-thumb {
    width: 72px; height: 72px; flex-shrink: 0;
    object-fit: contain; border-radius: 12px;
    border: 1px solid rgba(186,230,253,.6);
    background: linear-gradient(160deg, #f0f9ff, #e0f2fe);
    padding: 4px; box-sizing: border-box;
    transition: transform .3s cubic-bezier(.16,1,.3,1);
}
.review-item:hover .review-thumb { transform: scale(1.08); }

.review-product-name {
    font-size: 15px; font-weight: 700; color: #0c4a6e;
    text-decoration: none; margin-bottom: 4px; display: block;
    transition: color .15s;
}
.review-product-name:hover { color: #0ea5e9; }

/* Star display */
.star-display {
    display: flex; gap: 2px; margin-bottom: 8px; align-items: center;
}
.star-display .star {
    font-size: 18px; line-height: 1;
    transition: transform .15s;
}
.star-display .star.filled { color: #f59e0b; text-shadow: 0 1px 6px rgba(245,158,11,.4); }
.star-display .star.empty  { color: rgba(186,230,253,.8); }
.star-display:hover .star.filled { transform: scale(1.1); }

.review-rating-num {
    font-size: 12px; font-weight: 700; color: #b45309;
    background: rgba(251,191,36,.2); border: 1px solid rgba(251,191,36,.4);
    border-radius: 6px; padding: 1px 7px; margin-left: 6px;
}

.review-content {
    font-size: 14px; color: #0369a1; margin-bottom: 6px; line-height: 1.6;
    font-style: italic; opacity: .9;
}
.review-date {
    font-size: 12px; color: #7dd3fc;
    display: flex; align-items: center; gap: 4px;
}

/* top-right badge */
.review-badges { display: flex; align-items: flex-start; flex-shrink: 0; }
.badge-pending {
    display: inline-flex; align-items: center; gap: 4px;
    background: rgba(148,163,184,.18); color: #475569;
    border: 1px solid rgba(148,163,184,.35);
    padding: 4px 11px; border-radius: 20px;
    font-size: 12px; font-weight: 700;
}

/* ============================================================
   ADMIN REPLY
   ============================================================ */
.admin-reply {
    margin-top: 14px; padding: 14px 16px;
    background: rgba(186,230,253,.28);
    backdrop-filter: blur(4px);
    border: 1px solid rgba(125,211,252,.45);
    border-left: 3px solid #0ea5e9;
    border-radius: 12px; font-size: 14px;
    color: #0c4a6e; line-height: 1.6;
    animation: replySlide .4s cubic-bezier(.16,1,.3,1);
}
@keyframes replySlide { from{opacity:0;transform:translateY(-8px)} to{opacity:1;transform:none} }
.admin-reply strong {
    display: flex; align-items: center; gap: 6px;
    font-size: 13px; font-weight: 800; color: #0369a1; margin-bottom: 6px;
}
.admin-reply strong i { color: #0ea5e9; }

/* ============================================================
   EMPTY STATE
   ============================================================ */
.reviews-empty {
    text-align: center; padding: 60px 20px;
}
.reviews-empty .empty-icon {
    font-size: 62px; display: block; margin-bottom: 16px;
    background: linear-gradient(135deg, #f59e0b, #fbbf24);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    animation: emptyFloat 3s ease-in-out infinite;
}
@keyframes emptyFloat { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
.reviews-empty h5 { font-size: 19px; font-weight: 800; color: #0c4a6e; margin-bottom: 8px; }
.reviews-empty p  { color: #0369a1; font-size: 14px; opacity: .8; margin-bottom: 22px; }
.btn-explore {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 10px 22px; border-radius: 10px;
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    color: #fff; font-weight: 700; font-size: 13.5px;
    text-decoration: none;
    box-shadow: 0 3px 14px rgba(14,165,233,.32);
    transition: opacity .2s, transform .18s;
}
.btn-explore:hover { opacity:.9; transform:translateY(-1px); color:#fff; }

/* ============================================================
   PAGINATION
   ============================================================ */
.pagination-wrap { margin-top: 20px; }
.pagination-wrap .pagination .page-link {
    border: 1px solid rgba(186,230,253,.6);
    color: #0369a1;
    background: rgba(255,255,255,.75); backdrop-filter: blur(6px);
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
</style>
@endpush

@section('content')
<canvas id="sky-canvas" aria-hidden="true"></canvas>

<div class="myreviews-page">
<div class="myreviews-container">

    {{-- ===== SIDEBAR ===== --}}
    <div class="profile-sidebar-wrap reveal">
        @include('profile.sidebar')
    </div>

    {{-- ===== CONTENT ===== --}}
    <div class="reveal">
        <div class="myreviews-card">

            {{-- Header --}}
            <div class="myreviews-card-header">
                <h4>
                    <span class="header-icon"><i class="fas fa-star"></i></span>
                    Đánh giá của tôi
                </h4>
            </div>

            {{-- Body --}}
            <div class="myreviews-card-body">

                @forelse($reviews as $review)
                <div class="review-item stagger-reviews">

                    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap">

                        {{-- Product info + stars + content --}}
                        <div class="review-product-row" style="flex:1; min-width:0">
                            @if($review->product && $review->product->first_image)
                            <img src="{{ $review->product->first_image }}"
                                 class="review-thumb" alt="{{ $review->product->name ?? '' }}">
                            @endif
                            <div style="min-width:0">
                                @if($review->product)
                                <a href="{{ route('products.show', $review->product->slug) }}"
                                   class="review-product-name">
                                    {{ $review->product->name }}
                                </a>
                                @else
                                <span class="review-product-name" style="color:#94a3b8;cursor:default">
                                    Sản phẩm đã xóa
                                </span>
                                @endif

                                {{-- Stars --}}
                                <div class="star-display">
                                    @for($i = 1; $i <= 5; $i++)
                                    <span class="star {{ $i <= $review->rating ? 'filled' : 'empty' }}">★</span>
                                    @endfor
                                    <span class="review-rating-num">{{ $review->rating }}/5</span>
                                </div>

                                <p class="review-content">
                                    "{{ $review->content ?: 'Không có nhận xét.' }}"
                                </p>

                                <div class="review-date">
                                    <i class="far fa-clock"></i>
                                    {{ optional($review->created_at)->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>

                        {{-- Pending badge --}}
                        @if(!$review->is_visible)
                        <div class="review-badges">
                            <span class="badge-pending">
                                <i class="fas fa-clock"></i> Đang chờ kiểm duyệt
                            </span>
                        </div>
                        @endif

                    </div>

                    {{-- Admin reply --}}
                    @if($review->admin_reply)
                    <div class="admin-reply">
                        <strong>
                            <i class="fas fa-store"></i> Phản hồi từ ElectronicShop:
                        </strong>
                        <p style="margin:0">{{ $review->admin_reply }}</p>
                    </div>
                    @endif

                </div>
                @empty

                <div class="reviews-empty">
                    <i class="fas fa-star empty-icon"></i>
                    <h5>Bạn chưa có đánh giá nào</h5>
                    <p>Hãy mua sắm và chia sẻ cảm nhận của bạn về sản phẩm!</p>
                    <a href="{{ route('products.index') }}" class="btn-explore">
                        <i class="fas fa-store"></i> Khám phá sản phẩm
                    </a>
                </div>

                @endforelse

                {{-- Pagination --}}
                @if($reviews->hasPages())
                <div class="pagination-wrap">
                    {{ $reviews->links() }}
                </div>
                @endif

            </div>
        </div>
    </div>

</div>{{-- /.myreviews-container --}}
</div>{{-- /.myreviews-page --}}
@endsection

@push('scripts')
<script>
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
    document.querySelectorAll('.reveal, .stagger-reviews').forEach(el => io.observe(el));

    /* ---- Star sparkle on hover ---- */
    document.querySelectorAll('.star-display').forEach(row => {
        row.addEventListener('mouseenter', () => {
            row.querySelectorAll('.star.filled').forEach((s, i) => {
                setTimeout(() => {
                    s.style.transform = 'scale(1.25) rotate(8deg)';
                    setTimeout(() => { s.style.transform = ''; }, 280);
                }, i * 55);
            });
        });
    });

})();
</script>
@endpush
