@extends('layouts.app')
@section('title', 'Đơn mua - ElectronicShop')

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
    0%   { transform: translateY(0) scale(1);   opacity: .7; }
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
    background: rgba(125,211,252,.28);
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
.alert-sky {
    display: flex; align-items: center; gap: 8px;
    backdrop-filter: blur(8px);
    padding: 12px 18px; border-radius: 12px;
    margin-bottom: 16px; font-weight: 600; font-size: 14px;
    animation: alertIn .4s cubic-bezier(.16,1,.3,1);
}
@keyframes alertIn { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:none; } }
.alert-sky-success { background: rgba(220,252,231,.9); color: #166534; border: 1px solid rgba(187,247,208,.8); }
.alert-sky-error   { background: rgba(254,226,226,.92); color: #991b1b; border: 1px solid rgba(252,165,165,.6); }

/* ============================================================
   PAGE TITLE
   ============================================================ */
.orders-title {
    font-size: 22px; font-weight: 800; color: #0c4a6e;
    margin-bottom: 20px;
    display: flex; align-items: center; gap: 10px;
}
.orders-title i {
    width: 40px; height: 40px; border-radius: 11px;
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    color: #fff; font-size: 16px;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 14px rgba(14,165,233,.35);
}

/* ============================================================
   STATUS TABS — glassmorphism strip
   ============================================================ */
.order-tabs {
    display: flex; flex-wrap: wrap; gap: 0;
    list-style: none; margin: 0 0 20px; padding: 0;
    background: rgba(255,255,255,.72);
    backdrop-filter: blur(14px);
    border: 1px solid rgba(186,230,253,.6);
    border-radius: 16px; overflow: hidden;
    box-shadow: 0 4px 18px rgba(14,165,233,.09);
}
.order-tabs li { flex: 1; min-width: 90px; }

.order-tabs a {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    gap: 4px; padding: 13px 6px;
    font-size: 13px; font-weight: 600; color: #0369a1;
    text-decoration: none; text-align: center;
    border-bottom: 3px solid transparent;
    transition: background .2s, color .2s, border-color .2s;
}
.order-tabs a:hover {
    background: rgba(186,230,253,.3);
    color: #0c4a6e;
}
.order-tabs a.active {
    background: rgba(186,230,253,.45);
    color: #0c4a6e;
    border-bottom-color: #0ea5e9;
    font-weight: 800;
}

.tab-count {
    background: rgba(14,165,233,.15);
    border: 1px solid rgba(14,165,233,.2);
    border-radius: 20px; padding: 1px 8px;
    font-size: 12px; font-weight: 700;
    color: #0369a1;
    min-width: 22px; text-align: center;
}
.order-tabs a.active .tab-count {
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    color: #fff; border-color: transparent;
}

/* ============================================================
   SEARCH BOX — glassmorphism
   ============================================================ */
.search-box {
    background: rgba(255,255,255,.82);
    backdrop-filter: blur(14px);
    border: 1px solid rgba(186,230,253,.65);
    border-radius: 16px; padding: 16px 20px;
    margin-bottom: 22px;
    box-shadow: 0 4px 18px rgba(14,165,233,.09);
    display: flex; gap: 10px; align-items: center;
}
.search-box .search-icon {
    color: #7dd3fc; font-size: 16px; flex-shrink: 0;
}
.search-box input {
    flex: 1; border: 1px solid rgba(125,211,252,.5);
    border-radius: 10px; padding: 10px 14px;
    font-size: 14px; outline: none;
    background: rgba(255,255,255,.78); color: #0c4a6e;
    transition: border-color .2s, box-shadow .2s;
}
.search-box input::placeholder { color: #7dd3fc; }
.search-box input:focus {
    border-color: #0ea5e9;
    box-shadow: 0 0 0 3px rgba(14,165,233,.15);
}
.btn-search {
    padding: 10px 20px; border: none; border-radius: 10px;
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    color: #fff; font-weight: 700; font-size: 14px;
    cursor: pointer; white-space: nowrap;
    transition: opacity .2s, transform .15s;
    box-shadow: 0 3px 12px rgba(14,165,233,.3);
}
.btn-search:hover { opacity:.9; transform:translateY(-1px); }

/* ============================================================
   ORDER CARD — glassmorphism
   ============================================================ */
.order-card {
    background: rgba(255,255,255,.84);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(186,230,253,.65);
    border-radius: 18px; overflow: hidden;
    box-shadow: 0 6px 24px rgba(14,165,233,.1);
    margin-bottom: 18px;
    transition: transform .25s cubic-bezier(.16,1,.3,1), box-shadow .25s, border-color .25s;
    position: relative;
}
/* top accent bar */
.order-card::before {
    content: ''; position: absolute; top:0; left:0; right:0;
    height: 3px;
    background: linear-gradient(90deg, #0369a1, #38bdf8);
    border-radius: 18px 18px 0 0;
}
.order-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 14px 36px rgba(14,165,233,.18);
    border-color: #7dd3fc;
}

/* Card header */
.order-card-header {
    display: flex; justify-content: space-between; align-items: center;
    flex-wrap: wrap; gap: 10px;
    padding: 16px 20px 12px;
    border-bottom: 1px solid rgba(186,230,253,.4);
}
.order-id { font-weight: 800; font-size: 14.5px; color: #0c4a6e; }
.order-id span { color: #0ea5e9; }
.order-date { font-size: 12.5px; color: #7dd3fc; margin-top: 2px; }

/* Card body */
.order-card-body {
    display: grid;
    grid-template-columns: 1fr auto auto;
    gap: 20px; align-items: center;
    padding: 16px 20px;
}
@media (max-width: 768px) {
    .order-card-body { grid-template-columns: 1fr; }
}

/* Product preview */
.order-product { display: flex; align-items: center; gap: 14px; }
.order-product img,
.order-product .no-img {
    width: 80px; height: 80px; flex-shrink: 0;
    border-radius: 12px; object-fit: contain;
    border: 1px solid rgba(186,230,253,.6);
    background: linear-gradient(160deg, #f0f9ff, #e0f2fe);
    padding: 4px; box-sizing: border-box;
    transition: transform .3s cubic-bezier(.16,1,.3,1);
}
.order-card:hover .order-product img { transform: scale(1.07); }
.order-product .no-img {
    display: flex; align-items: center; justify-content: center;
    color: #7dd3fc; font-size: 22px;
}
.order-product-name { font-size: 14.5px; font-weight: 700; color: #0c4a6e; margin-bottom: 4px; }
.order-product-qty  { font-size: 13px; color: #0369a1; opacity: .85; }
.order-product-more { font-size: 12.5px; color: #7dd3fc; font-style: italic; }

/* Total */
.order-total-cell { text-align: center; min-width: 130px; }
.order-total-label { font-size: 12px; color: #7dd3fc; margin-bottom: 4px; }
.order-total-amount { font-size: 19px; font-weight: 800; color: #0369a1; }

/* Actions */
.order-actions { display: flex; flex-direction: column; gap: 8px; min-width: 140px; }

/* ============================================================
   STATUS BADGES
   ============================================================ */
.status-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 13px; border-radius: 20px;
    font-size: 12.5px; font-weight: 700;
}
.badge-warning   { background: rgba(251,191,36,.18); color: #b45309; border: 1px solid rgba(251,191,36,.4); }
.badge-info      { background: rgba(56,189,248,.18); color: #0369a1; border: 1px solid rgba(56,189,248,.4); }
.badge-primary   { background: rgba(14,165,233,.18); color: #0c4a6e; border: 1px solid rgba(14,165,233,.4); }
.badge-success   { background: rgba(34,197,94,.15); color: #166534; border: 1px solid rgba(34,197,94,.35); }
.badge-danger    { background: rgba(239,68,68,.12); color: #b91c1c; border: 1px solid rgba(239,68,68,.3); }
.badge-secondary { background: rgba(148,163,184,.18); color: #475569; border: 1px solid rgba(148,163,184,.35); }

/* ============================================================
   ACTION BUTTONS
   ============================================================ */
.btn-action {
    display: flex; align-items: center; justify-content: center; gap: 6px;
    width: 100%; padding: 9px 12px; border-radius: 9px;
    font-size: 13px; font-weight: 700; cursor: pointer;
    transition: opacity .2s, transform .15s, box-shadow .2s;
    text-decoration: none; border: none; font-family: inherit;
    position: relative; overflow: hidden;
}
.btn-action::after {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,.28) 50%, transparent 60%);
    transform: translateX(-120%); transition: transform .5s ease; pointer-events: none;
}
.btn-action:hover::after { transform: translateX(120%); }
.btn-action:hover { opacity:.9; transform:translateY(-1px); }

.btn-detail   { background: rgba(186,230,253,.5); color: #0369a1; border: 1px solid rgba(125,211,252,.5); }
.btn-detail:hover { background: rgba(186,230,253,.75); box-shadow: 0 3px 10px rgba(14,165,233,.15); }

.btn-cancel   { background: rgba(239,68,68,.1); color: #ef4444; border: 1px solid rgba(239,68,68,.28); }
.btn-cancel:hover { background: #ef4444; color: #fff; box-shadow: 0 3px 10px rgba(239,68,68,.3); }

.btn-received { background: rgba(34,197,94,.12); color: #16a34a; border: 1px solid rgba(34,197,94,.35); }
.btn-received:hover { background: #22c55e; color: #fff; box-shadow: 0 3px 10px rgba(34,197,94,.3); }

.btn-reorder  { background: linear-gradient(135deg, #0369a1, #0ea5e9); color: #fff; box-shadow: 0 3px 12px rgba(14,165,233,.3); }
.btn-reorder:hover { box-shadow: 0 6px 18px rgba(14,165,233,.4); }

.btn-review   { background: linear-gradient(135deg, #d97706, #f59e0b); color: #fff; box-shadow: 0 3px 12px rgba(245,158,11,.3); }
.btn-review:hover { box-shadow: 0 6px 18px rgba(245,158,11,.4); }

/* ============================================================
   EMPTY STATE
   ============================================================ */
.orders-empty {
    background: rgba(255,255,255,.82);
    backdrop-filter: blur(16px);
    border: 1px solid rgba(186,230,253,.6);
    border-radius: 18px; padding: 60px 20px;
    text-align: center;
    box-shadow: 0 6px 24px rgba(14,165,233,.1);
}
.orders-empty i {
    font-size: 60px; display: block; margin-bottom: 16px;
    background: linear-gradient(135deg, #7dd3fc, #bae6fd);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    animation: emptyFloat 3s ease-in-out infinite;
}
@keyframes emptyFloat { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
.orders-empty h4 { font-size: 20px; font-weight: 800; color: #0c4a6e; margin-bottom: 8px; }
.orders-empty p  { color: #0369a1; font-size: 14px; opacity: .8; margin-bottom: 20px; }
.btn-shop-now {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 7px 18px; border-radius: 9px;
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    color: #fff; font-weight: 700; font-size: 11px;
    text-decoration: none;
    box-shadow: 0 4px 16px rgba(14,165,233,.35);
    transition: opacity .2s, transform .18s;
}
.btn-shop-now:hover { opacity:.9; transform:translateY(-2px); color:#fff; }

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
    margin: 0 2px;
    font-weight: 600; font-size: 13.5px;
    transition: all .18s;
}
.pagination-wrap .pagination .page-link:hover {
    background: rgba(186,230,253,.5); color: #0c4a6e;
}
.pagination-wrap .pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    border-color: transparent; color: #fff;
    box-shadow: 0 3px 10px rgba(14,165,233,.3);
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 768px) {
    .order-total-cell { text-align: left; }
    .order-actions    { flex-direction: row; flex-wrap: wrap; }
    .btn-action       { flex: 1; min-width: 120px; }
}
</style>
@endpush

@section('content')
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
        <div class="alert-sky alert-sky-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="alert-sky alert-sky-error">
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
                ['shipped',    'Đang giao',      $counts->shipped],
                ['delivered',  'Hoàn thành',     $counts->delivered],
                ['cancelled',  'Đã hủy',         $counts->cancelled],
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
                    <i class="fas fa-search" style="margin-right:5px"></i>Tìm
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
                        <i class="fas fa-calendar-alt" style="margin-right:4px"></i>
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
                        <i class="fas fa-eye"></i> Xem chi tiết
                    </a>

                    @if(in_array($order->status, ['pending','confirmed','processing']))
                    <form method="POST" action="{{ route('profile.order.cancel', $order) }}">
                        @csrf @method('PATCH')
                        <button class="btn-action btn-cancel"
                                onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">
                            <i class="fas fa-ban"></i> Hủy đơn
                        </button>
                    </form>

                    @elseif($order->status === 'shipped')
                    <form method="POST" action="{{ route('profile.order.received', $order) }}">
                        @csrf @method('PATCH')
                        <button class="btn-action btn-received">
                            <i class="fas fa-circle-check"></i> Đã nhận hàng
                        </button>
                    </form>

                    @elseif($order->status === 'delivered')
                    <form method="POST" action="{{ route('profile.order.reorder', $order) }}">
                        @csrf
                        <button class="btn-action btn-reorder">
                            <i class="fas fa-rotate-right"></i> Mua lại
                        </button>
                    </form>

                    @elseif(in_array($order->status, ['cancelled','returned']))
                    <form method="POST" action="{{ route('profile.order.reorder', $order) }}">
                        @csrf
                        <button class="btn-action btn-reorder">
                            <i class="fas fa-rotate-right"></i> Mua lại
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
                <i class="fas fa-store"></i> Mua sắm ngay
            </a>
        </div>
        @endforelse

        </div>{{-- /.stagger-orders --}}

        {{-- Pagination --}}
        <div class="pagination-wrap reveal">
            {{ $orders->links() }}
        </div>

    </div>{{-- /.content --}}
</div>{{-- /.orders-container --}}
</div>{{-- /.orders-page --}}
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
    document.querySelectorAll('.reveal, .stagger-orders').forEach(el => io.observe(el));

    /* ---- Ripple on action buttons ---- */
    document.querySelectorAll('.btn-action').forEach(btn => {
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
