@extends('layouts.app')
@section('title', 'Đơn mua - ElectronicShop')

@push('styles')
<style>
/* ============================================================
   PAGE BACKGROUND — Samsung Minimalist
   ============================================================ */
body {
    background: linear-gradient(180deg,
        #f8f9fa 0%, #f1f3f5 38%, #e9ecef 100%) fixed;
    background-attachment: fixed;
    color: #000000;
}
#sky-canvas {
    position: fixed; inset: 0; width: 100%; height: 100%;
    pointer-events: none; z-index: 0; opacity: .6;
}
.bubble {
    position: fixed; border-radius: 50%;
    background: radial-gradient(circle at 35% 35%, rgba(255,255,255,1), rgba(200,200,200,.2));
    border: 1px solid rgba(0,0,0,.05);
    pointer-events: none; z-index: 0;
    animation: bubbleRise linear infinite;
}
@keyframes bubbleRise {
    0%   { transform: translateY(0) scale(1);   opacity: .5; }
    80%  { opacity: .2; }
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

.stagger-orders > * {
    opacity: 0; transform: translateY(22px);
    transition: opacity .55s cubic-bezier(.16,1,.3,1), transform .55s cubic-bezier(.16,1,.3,1);
}
.stagger-orders.revealed > *:nth-child(1)  { opacity:1; transform:none; transition-delay:.04s; }
.stagger-orders.revealed > *:nth-child(2)  { opacity:1; transform:none; transition-delay:.09s; }
.stagger-orders.revealed > *:nth-child(3)  { opacity:1; transform:none; transition-delay:.14s; }
.stagger-orders.revealed > *:nth-child(4)  { opacity:1; transform:none; transition-delay:.19s; }
.stagger-orders.revealed > *:nth-child(n+5){ opacity:1; transform:none; transition-delay:.24s; }

/* ripple */
.ripple-wave {
    position: absolute; border-radius: 50%;
    background: rgba(0,0,0,.1);
    transform: scale(0); animation: rippleOut .6s linear;
    pointer-events: none; z-index: 10;
}
@keyframes rippleOut { to { transform:scale(4); opacity:0; } }

/* ============================================================
   PAGE WRAPPER
   ============================================================ */
.orders-page {
    min-height: 100vh;
    padding: 32px 0 60px;
    position: relative; z-index: 1;
}
.orders-container {
    max-width: 1200px; margin: 0 auto; padding: 0 16px;
    display: grid; grid-template-columns: 260px 1fr;
    gap: 24px; align-items: start;
    position: relative; z-index: 1;
}
@media (max-width: 991px) { .orders-container { grid-template-columns: 1fr; } }

.profile-sidebar-wrap { position: sticky; top: 88px; }

/* ============================================================
   ALERTS
   ============================================================ */
.alert-box {
    display: flex; align-items: center; gap: 8px;
    padding: 14px 18px; border-radius: 12px;
    margin-bottom: 20px; font-weight: 600; font-size: 14px;
    animation: alertIn .4s cubic-bezier(.16,1,.3,1);
}
@keyframes alertIn { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:none; } }
.alert-success { background: #e6f4ea; color: #137333; border: 1px solid #ceead6; }
.alert-error   { background: #fce8e6; color: #d93025; border: 1px solid #fad2cf; }

/* ============================================================
   PAGE TITLE
   ============================================================ */
.orders-title {
    font-size: 22px; font-weight: 700; color: #000000;
    margin-bottom: 24px;
    display: flex; align-items: center; gap: 12px;
}
.orders-title i {
    width: 42px; height: 42px; border-radius: 50%;
    background: #f4f4f4;
    color: #000000; font-size: 16px;
    display: flex; align-items: center; justify-content: center;
}

/* ============================================================
   STATUS TABS
   ============================================================ */
.order-tabs {
    display: flex; flex-wrap: wrap; gap: 0;
    list-style: none; margin: 0 0 24px; padding: 0;
    background: #ffffff;
    border: 1px solid #ebebeb;
    border-radius: 16px; overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,.02);
}
.order-tabs li { flex: 1; min-width: 100px; }

.order-tabs a {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    gap: 6px; padding: 14px 8px;
    font-size: 13.5px; font-weight: 600; color: #555555;
    text-decoration: none; text-align: center;
    border-bottom: 3px solid transparent;
    transition: all .2s;
}
.order-tabs a:hover {
    background: #fafafa;
    color: #000000;
}
.order-tabs a.active {
    background: #ffffff;
    color: #000000;
    border-bottom-color: #000000;
    font-weight: 700;
}

.tab-count {
    background: #f4f4f4;
    border-radius: 20px; padding: 2px 10px;
    font-size: 12px; font-weight: 700;
    color: #333333;
    min-width: 24px; text-align: center;
    transition: all .2s;
}
.order-tabs a.active .tab-count {
    background: #000000;
    color: #ffffff;
}

/* ============================================================
   SEARCH BOX
   ============================================================ */
.search-box {
    background: #ffffff;
    border: 1px solid #ebebeb;
    border-radius: 16px; padding: 16px 20px;
    margin-bottom: 24px;
    box-shadow: 0 4px 20px rgba(0,0,0,.02);
    display: flex; gap: 12px; align-items: center;
}
.search-box .search-icon {
    color: #999999; font-size: 16px; flex-shrink: 0;
}
.search-box input {
    flex: 1; border: 1px solid #cccccc;
    border-radius: 12px; padding: 12px 16px;
    font-size: 14.5px; outline: none;
    background: #fafafa; color: #000000;
    transition: all .2s;
}
.search-box input::placeholder { color: #999999; }
.search-box input:focus {
    border-color: #000000;
    background: #ffffff;
    box-shadow: 0 0 0 1px #000000;
}
.btn-search {
    padding: 12px 24px; border: none; border-radius: 30px;
    background: #000000;
    color: #ffffff; font-weight: 600; font-size: 14px;
    cursor: pointer; white-space: nowrap;
    transition: all .2s;
    box-shadow: 0 4px 14px rgba(0,0,0,.15);
}
.btn-search:hover { background: #333333; transform: translateY(-1px); }

/* ============================================================
   ORDER CARD
   ============================================================ */
.order-card {
    background: #ffffff;
    border: 1px solid #ebebeb;
    border-radius: 20px; overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,.03);
    margin-bottom: 20px;
    transition: transform .25s ease, box-shadow .25s, border-color .25s;
    position: relative;
}
.order-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 30px rgba(0,0,0,.06);
    border-color: #dcdcdc;
}

/* Card header */
.order-card-header {
    display: flex; justify-content: space-between; align-items: center;
    flex-wrap: wrap; gap: 10px;
    padding: 18px 24px 14px;
    border-bottom: 1px solid #eeeeee;
}
.order-id { font-weight: 700; font-size: 15px; color: #000000; }
.order-id span { color: #555555; }
.order-date { font-size: 13px; color: #777777; margin-top: 4px; }

/* Card body */
.order-card-body {
    display: grid;
    grid-template-columns: 1fr auto auto;
    gap: 24px; align-items: center;
    padding: 20px 24px;
}
@media (max-width: 768px) {
    .order-card-body { grid-template-columns: 1fr; }
}

/* Product preview */
.order-product { display: flex; align-items: center; gap: 16px; }
.order-product img,
.order-product .no-img {
    width: 84px; height: 84px; flex-shrink: 0;
    border-radius: 12px; object-fit: contain;
    border: 1px solid #ebebeb;
    background: #f8f9fa;
    padding: 6px; box-sizing: border-box;
    transition: transform .3s ease;
}
.order-card:hover .order-product img { transform: scale(1.05); }
.order-product .no-img {
    display: flex; align-items: center; justify-content: center;
    color: #cccccc; font-size: 24px;
}
.order-product-name { font-size: 15px; font-weight: 600; color: #000000; margin-bottom: 6px; line-height: 1.4; }
.order-product-qty  { font-size: 14px; color: #555555; }
.order-product-more { font-size: 13px; color: #777777; font-style: italic; margin-left: 4px; }

/* Total */
.order-total-cell { text-align: center; min-width: 140px; }
.order-total-label { font-size: 13px; color: #777777; margin-bottom: 6px; }
.order-total-amount { font-size: 20px; font-weight: 700; color: #000000; }

/* Actions */
.order-actions { display: flex; flex-direction: column; gap: 10px; min-width: 150px; }

/* ============================================================
   STATUS BADGES - Clean UI Colors
   ============================================================ */
.status-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 14px; border-radius: 20px;
    font-size: 12.5px; font-weight: 600;
}
.badge-warning   { background: #fef7e0; color: #b06000; border: 1px solid #fce8b2; }
.badge-info      { background: #e8f0fe; color: #1967d2; border: 1px solid #d2e3fc; }
.badge-primary   { background: #e3f2fd; color: #0066cc; border: 1px solid #bbdefb; }
.badge-success   { background: #e6f4ea; color: #137333; border: 1px solid #ceead6; }
.badge-danger    { background: #fce8e6; color: #d93025; border: 1px solid #fad2cf; }
.badge-secondary { background: #f1f3f4; color: #5f6368; border: 1px solid #dadce0; }

/* ============================================================
   ACTION BUTTONS
   ============================================================ */
.btn-action {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    width: 100%; padding: 10px 16px; border-radius: 30px; /* Bo tròn viên thuốc */
    font-size: 13.5px; font-weight: 600; cursor: pointer;
    transition: all .2s ease;
    text-decoration: none; border: none; font-family: inherit;
    position: relative; overflow: hidden;
}

.btn-detail   { background: #f8f9fa; color: #333333; border: 1px solid #cccccc; }
.btn-detail:hover { background: #ffffff; color: #000000; border-color: #000000; }

.btn-cancel   { background: transparent; color: #d93025; border: 1px solid #fad2cf; }
.btn-cancel:hover { background: #d93025; color: #ffffff; }

.btn-received { background: #137333; color: #ffffff; }
.btn-received:hover { background: #0d5c27; box-shadow: 0 4px 12px rgba(19,115,51,.2); }

.btn-reorder  { background: #000000; color: #ffffff; }
.btn-reorder:hover { background: #333333; box-shadow: 0 4px 12px rgba(0,0,0,.2); }

.btn-review   { background: #2189ff; color: #ffffff; } /* Samsung Blue */
.btn-review:hover { background: #0066cc; box-shadow: 0 4px 12px rgba(33,137,255,.3); }

/* ============================================================
   EMPTY STATE
   ============================================================ */
.orders-empty {
    background: #ffffff;
    border: 1px solid #ebebeb;
    border-radius: 20px; padding: 70px 20px;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0,0,0,.02);
}
.orders-empty i {
    font-size: 64px; display: block; margin-bottom: 20px;
    color: #cccccc;
    animation: emptyFloat 3s ease-in-out infinite;
}
@keyframes emptyFloat { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
.orders-empty h4 { font-size: 20px; font-weight: 700; color: #000000; margin-bottom: 10px; }
.orders-empty p  { color: #555555; font-size: 15px; margin-bottom: 24px; }
.btn-shop-now {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 12px 28px; border-radius: 30px;
    background: #000000;
    color: #ffffff; font-weight: 600; font-size: 14px;
    text-decoration: none;
    box-shadow: 0 4px 14px rgba(0,0,0,.15);
    transition: all .2s;
}
.btn-shop-now:hover { background: #333333; transform: translateY(-2px); color: #ffffff; }

/* ============================================================
   PAGINATION
   ============================================================ */
.pagination-wrap { margin-top: 24px; }
.pagination-wrap .pagination .page-link {
    border: 1px solid #ebebeb !important;
    color: #333333 !important;
    background: #ffffff !important;
    border-radius: 10px !important;
    margin: 0 4px;
    font-weight: 600; font-size: 14px;
    transition: all .2s;
    box-shadow: none !important;
}
.pagination-wrap .pagination .page-link:hover,
.pagination-wrap .pagination .page-link:focus {
    background: #f4f4f4 !important;
    color: #000000 !important;
    border-color: #cccccc !important;
    box-shadow: none !important;
}
.pagination-wrap .pagination .page-item.active .page-link {
    background: #000000 !important;
    border-color: #000000 !important;
    color: #ffffff !important;
    box-shadow: 0 4px 10px rgba(0,0,0,.15) !important;
}
.pagination-wrap .pagination .page-item.disabled .page-link {
    color: #bbbbbb !important;
    background: #ffffff !important;
    border-color: #ebebeb !important;
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 768px) {
    .order-total-cell { text-align: left; }
    .order-actions    { flex-direction: row; flex-wrap: wrap; }
    .btn-action       { flex: 1; min-width: 130px; }
}
</style>
@endpush

@section('content')
{{-- Sky Canvas (Updated to Silver/Gray) --}}
<canvas id="sky-canvas" aria-hidden="true"></canvas>

<div class="orders-page">
<div class="orders-container">

    {{-- ===== SIDEBAR ===== --}}
    <div class="profile-sidebar-wrap reveal">
        @include('profile.sidebar')
    </div>

    {{-- ===== CONTENT ===== --}}
    <div>

        @if(session('success'))
        <div class="alert-box alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="alert-box alert-error">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
        @endif

        <h2 class="orders-title reveal">
            <i class="fas fa-box"></i> Đơn mua
        </h2>

        {{-- ===== TABS ===== --}}
        <ul class="order-tabs reveal">
            @foreach([
                ['all',        'Tất cả',        $counts->total],
                ['pending',    'Chờ xác nhận',  $counts->pending],
                ['processing', 'Đang xử lý',    $counts->processing],
                ['shipped',    'Đang giao',     $counts->shipped],
                ['delivered',  'Hoàn thành',    $counts->delivered],
                ['cancelled',  'Đã hủy',        $counts->cancelled],
            ] as [$val, $label, $count])
            <li>
                <a href="{{ route('profile.order', ['status' => $val]) }}"
                   class="{{ $status === $val ? 'active' : '' }}">
                    {{ $label }}
                    <span class="tab-count">{{ $count }}</span>
                </a>
            </li>
            @endforeach
        </ul>

        {{-- ===== SEARCH ===== --}}
        <form method="GET" action="{{ route('profile.order') }}">
            <input type="hidden" name="status" value="{{ $status }}">
            <div class="search-box reveal">
                <i class="fas fa-search search-icon"></i>
                <input type="text" name="keyword" value="{{ $keyword }}"
                       placeholder="Tìm theo mã đơn hoặc tên sản phẩm...">
                <button type="submit" class="btn-search">
                    Tìm kiếm
                </button>
            </div>
        </form>

        {{-- ===== ORDER LIST ===== --}}
        <div class="stagger-orders">

        @forelse($orders as $order)
        @php
            $item    = $order->items->first();
            $product = $item?->product;
            $badgeClass = match($order->status) {
                'pending'    => 'warning',
                'confirmed'  => 'info',
                'processing' => 'info',
                'shipped'    => 'primary',
                'delivered'  => 'success',
                'cancelled'  => 'danger',
                'returned'   => 'secondary',
                default      => 'secondary',
            };
            $badgeText = match($order->status) {
                'pending'    => 'Chờ xác nhận',
                'confirmed'  => 'Đã xác nhận',
                'processing' => 'Đang xử lý',
                'shipped'    => 'Đang giao hàng',
                'delivered'  => 'Hoàn thành',
                'cancelled'  => 'Đã hủy',
                'returned'   => 'Đã hoàn trả',
                default      => $order->status,
            };
            $badgeIcon = match($order->status) {
                'pending'    => 'fa-clock',
                'confirmed'  => 'fa-check',
                'processing' => 'fa-gear',
                'shipped'    => 'fa-truck-fast',
                'delivered'  => 'fa-circle-check',
                'cancelled'  => 'fa-ban',
                'returned'   => 'fa-rotate-left',
                default      => 'fa-circle',
            };
        @endphp

        <div class="order-card">
            {{-- Header --}}
            <div class="order-card-header">
                <div>
                    <div class="order-id">
                        Mã đơn: <span>#{{ $order->id }}</span>
                    </div>
                    <div class="order-date">
                        <i class="fas fa-calendar-alt" style="margin-right:6px"></i>
                        {{ $order->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>
                <span class="status-badge badge-{{ $badgeClass }}">
                    <i class="fas {{ $badgeIcon }}"></i>
                    {{ $badgeText }}
                </span>
            </div>

            {{-- Body --}}
            <div class="order-card-body">

                {{-- Product preview --}}
                <div class="order-product">
                    @if($product?->first_image)
                        <img src="{{ $product->first_image }}" alt="{{ $product->name }}">
                    @else
                        <div class="no-img"><i class="fas fa-image"></i></div>
                    @endif
                    <div>
                        <div class="order-product-name">
                            {{ $item?->product_name ?? $product?->name }}
                        </div>
                        <div class="order-product-qty">
                            × {{ $item?->quantity }}
                            @if($order->items->count() > 1)
                            <span class="order-product-more">và {{ $order->items->count() - 1 }} sản phẩm khác</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Total --}}
                <div class="order-total-cell">
                    <div class="order-total-label">Thành tiền</div>
                    <div class="order-total-amount">{{ number_format($order->total, 0, ',', '.') }}đ</div>
                </div>

                {{-- Actions --}}
                <div class="order-actions">
                    <a href="{{ route('profile.order.show', $order) }}" class="btn-action btn-detail">
                        Xem chi tiết
                    </a>

                    @if(in_array($order->status, ['pending','confirmed','processing']))
                    <form method="POST" action="{{ route('profile.order.cancel', $order) }}">
                        @csrf @method('PATCH')
                        <button class="btn-action btn-cancel"
                                onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">
                            Hủy đơn
                        </button>
                    </form>

                    @elseif($order->status === 'shipped')
                    <form method="POST" action="{{ route('profile.order.received', $order) }}">
                        @csrf @method('PATCH')
                        <button class="btn-action btn-received">
                            Đã nhận hàng
                        </button>
                    </form>

                    @elseif($order->status === 'delivered')
                    <form method="POST" action="{{ route('profile.order.reorder', $order) }}">
                        @csrf
                        <button class="btn-action btn-reorder">
                            Mua lại
                        </button>
                    </form>

                    @elseif(in_array($order->status, ['cancelled','returned']))
                    <form method="POST" action="{{ route('profile.order.reorder', $order) }}">
                        @csrf
                        <button class="btn-action btn-reorder">
                            Mua lại
                        </button>
                    </form>
                    @endif
                </div>

            </div>
        </div>

        @empty
        <div class="orders-empty reveal">
            <i class="fas fa-box-open"></i>
            <h4>Chưa có đơn hàng</h4>
            <p>Các đơn hàng của bạn sẽ xuất hiện ở đây sau khi đặt hàng.</p>
            <a href="{{ route('products.index') }}" class="btn-shop-now">
                Mua sắm ngay <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        @endforelse

        </div>{{-- /.stagger-orders --}}

        {{-- Pagination --}}
            <div class="pagination-wrap reveal">
                {{ $orders->links('pagination::bootstrap-5') }}
            </div>

    </div>{{-- /.content --}}
</div>{{-- /.orders-container --}}
</div>{{-- /.orders-page --}}
@endsection

@push('scripts')
<script>
(function () {
    /* ---- Canvas clouds (Samsung Style: Silver/Gray) ---- */
    const canvas = document.getElementById('sky-canvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let W, H, clouds = [];
        function resize() { W = canvas.width = window.innerWidth; H = canvas.height = window.innerHeight; }
        window.addEventListener('resize', resize); resize();
        function makeCloud() {
            return { x: Math.random()*W*1.2, y: Math.random()*H*.6,
                     r: 50+Math.random()*110, dx: .1+Math.random()*.2, alpha: .02+Math.random()*.05 };
        }
        for (let i = 0; i < 6; i++) clouds.push(makeCloud());
        function drawCloud(c) {
            const g = ctx.createRadialGradient(c.x,c.y,0,c.x,c.y,c.r);
            g.addColorStop(0, `rgba(200,205,210,${c.alpha})`);
            g.addColorStop(.6, `rgba(220,224,228,${c.alpha*.6})`);
            g.addColorStop(1, 'rgba(230,230,230,0)');
            ctx.beginPath(); ctx.arc(c.x,c.y,c.r,0,Math.PI*2);
            ctx.fillStyle = g; ctx.fill();
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
        const size = 3+Math.random()*10, dur = 10+Math.random()*15;
        el.style.cssText = [`width:${size}px`,`height:${size}px`,
            `left:${Math.random()*100}vw`,`bottom:-${size}px`,
            `animation-duration:${dur}s`,`animation-delay:${Math.random()*5}s`].join(';');
        document.body.appendChild(el);
        setTimeout(() => el.remove(), (dur+5)*1000);
    }
    for (let i = 0; i < 6; i++) spawnBubble();
    setInterval(spawnBubble, 4500);

    /* ---- Scroll Reveal ---- */
    const io = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) { e.target.classList.add('revealed'); io.unobserve(e.target); }
        });
    }, { threshold: 0.05, rootMargin: '0px 0px -20px 0px' });
    document.querySelectorAll('.reveal, .stagger-orders').forEach(el => io.observe(el));

    /* ---- Ripple on action buttons ---- */
    document.querySelectorAll('.btn-action, .btn-search').forEach(btn => {
        btn.addEventListener('click', function (e) {
            const r    = btn.getBoundingClientRect();
            const size = Math.max(r.width, r.height) * 1.8;
            const rip  = document.createElement('span');
            rip.className = 'ripple-wave';
            
            // Màu gợn sóng nhạt cho nền tối, tối cho nền sáng
            const isDarkBg = window.getComputedStyle(btn).backgroundColor !== 'rgb(248, 249, 250)' && window.getComputedStyle(btn).backgroundColor !== 'rgba(0, 0, 0, 0)';
            rip.style.background = isDarkBg ? 'rgba(255,255,255,.3)' : 'rgba(0,0,0,.1)';

            rip.style.cssText += `;width:${size}px;height:${size}px;` +
                `left:${e.clientX-r.left-size/2}px;` +
                `top:${e.clientY-r.top-size/2}px;`;
            
            btn.style.position = 'relative';
            btn.appendChild(rip);
            rip.addEventListener('animationend', () => rip.remove());
        });
    });
})();
</script>
@endpush