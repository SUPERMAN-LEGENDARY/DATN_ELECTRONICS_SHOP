@extends('layouts.app')
@section('title', 'Giỏ hàng - ElectronicShop')

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
    opacity: 0; transform: translateY(26px);
    transition: opacity .6s cubic-bezier(.16,1,.3,1), transform .6s cubic-bezier(.16,1,.3,1);
}
.reveal.revealed { opacity: 1; transform: translateY(0); }

.stagger-children > * {
    opacity: 0; transform: translateY(18px);
    transition: opacity .5s cubic-bezier(.16,1,.3,1), transform .5s cubic-bezier(.16,1,.3,1);
}
.stagger-children.revealed > *:nth-child(1)  { opacity:1; transform:none; transition-delay:.04s; }
.stagger-children.revealed > *:nth-child(2)  { opacity:1; transform:none; transition-delay:.09s; }
.stagger-children.revealed > *:nth-child(3)  { opacity:1; transform:none; transition-delay:.14s; }
.stagger-children.revealed > *:nth-child(4)  { opacity:1; transform:none; transition-delay:.19s; }
.stagger-children.revealed > *:nth-child(5)  { opacity:1; transform:none; transition-delay:.24s; }
.stagger-children.revealed > *:nth-child(n+6){ opacity:1; transform:none; transition-delay:.28s; }

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
.cart-page {
    min-height: 100vh;
    padding: 28px 0 60px;
    position: relative; z-index: 1;
}

.cart-container {
    max-width: 1200px; margin: 0 auto; padding: 0 16px;
    position: relative; z-index: 1;
}

.cart-container h1 {
    font-size: 24px; font-weight: 800; margin-bottom: 22px;
    color: #0c4a6e;
    display: flex; align-items: center; gap: 10px;
}
.cart-container h1 i {
    width: 44px; height: 44px; border-radius: 12px;
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    color: #fff; font-size: 18px;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 14px rgba(14,165,233,.35);
}

/* ============================================================
   ALERTS
   ============================================================ */
.alert-box {
    padding: 13px 18px; border-radius: 10px;
    margin-bottom: 18px; font-size: 14px; font-weight: 500;
    backdrop-filter: blur(6px);
    animation: alertIn .4s cubic-bezier(.16,1,.3,1);
    display: flex; align-items: center; gap: 8px;
}
@keyframes alertIn { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:none; } }
.alert-success { background: rgba(220,252,231,.9); color: #166534; border: 1px solid rgba(187,247,208,.8); }
.alert-error   { background: rgba(254,226,226,.9); color: #b91c1c; border: 1px solid rgba(252,165,165,.5); }

/* ============================================================
   EMPTY CART
   ============================================================ */
.empty-cart {
    background: rgba(255,255,255,.82);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(186,230,253,.6);
    border-radius: 18px; padding: 60px 20px;
    text-align: center;
    box-shadow: 0 6px 28px rgba(14,165,233,.1);
}
.empty-cart i {
    font-size: 56px; display: block; margin-bottom: 16px;
    background: linear-gradient(135deg, #0369a1, #38bdf8);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    animation: emptyFloat 3s ease-in-out infinite;
}
@keyframes emptyFloat {
    0%,100% { transform: translateY(0);  }
    50%      { transform: translateY(-10px); }
}
.empty-cart p { font-size: 15px; color: #0369a1; margin-bottom: 18px; opacity: .85; font-weight: 600; }

/* ============================================================
   LAYOUT
   ============================================================ */
.cart-wrapper { display: flex; gap: 24px; align-items: flex-start; }
.cart-left    { flex: 1; min-width: 0; }
.cart-right   { width: 340px; flex-shrink: 0; }
@media (max-width: 991px) { .cart-wrapper { flex-direction: column; } .cart-right { width: 100%; } }

/* ============================================================
   CART TABLE — glassmorphism
   ============================================================ */
.cart-table {
    width: 100%; border-collapse: collapse;
    background: rgba(255,255,255,.82);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border-radius: 16px; overflow: hidden;
    box-shadow: 0 4px 22px rgba(14,165,233,.1);
    border: 1px solid rgba(186,230,253,.55);
}

.cart-table thead { background: linear-gradient(135deg, #0369a1, #0ea5e9); }
.cart-table th {
    padding: 14px 16px; font-size: 12.5px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .5px;
    color: #fff; text-align: left;
}

.cart-table td {
    padding: 16px 15px;
    border-bottom: 1px solid rgba(186,230,253,.4);
    vertical-align: middle; font-size: 14px; color: #0369a1;
    transition: background .15s;
}
.cart-table tr:last-child td { border-bottom: none; }
.cart-table tbody tr:hover td { background: rgba(186,230,253,.18); }
.cart-table tbody tr {
    transition: background .15s;
}

/* ============================================================
   CART PRODUCT CELL
   ============================================================ */
.cart-product { display: flex; align-items: center; gap: 14px; }

.cart-product img,
.cart-product .no-img {
    width: 72px; height: 72px;
    object-fit: contain;
    border-radius: 10px;
    border: 1px solid rgba(186,230,253,.6);
    flex-shrink: 0;
    padding: 4px; box-sizing: border-box;
    background: linear-gradient(160deg, #f0f9ff, #e0f2fe);
    transition: transform .3s cubic-bezier(.16,1,.3,1);
}
.cart-table tbody tr:hover .cart-product img { transform: scale(1.08); }

.cart-product .no-img {
    display: flex; align-items: center; justify-content: center;
    color: #7dd3fc; font-size: 20px;
}

.cart-product h5 { margin: 0 0 4px; font-size: 14.5px; font-weight: 700; }
.cart-product h5 a { color: #0c4a6e; text-decoration: none; transition: color .15s; }
.cart-product h5 a:hover { color: #0369a1; }
.cart-product .variant-tag {
    font-size: 12px; color: #0ea5e9; font-weight: 600;
    background: rgba(186,230,253,.4); border-radius: 5px;
    padding: 2px 7px; display: inline-block; margin-bottom: 2px;
}
.cart-product .brand { font-size: 12px; color: #7dd3fc; }

/* ============================================================
   QTY INPUT
   ============================================================ */
.qty-input {
    width: 62px; height: 38px;
    border: 1px solid rgba(125,211,252,.55);
    border-radius: 8px; text-align: center;
    font-size: 14px; font-weight: 600;
    outline: none; color: #0c4a6e;
    background: rgba(255,255,255,.75);
    transition: border-color .2s, box-shadow .2s;
}
.qty-input:focus {
    border-color: #0ea5e9;
    box-shadow: 0 0 0 3px rgba(14,165,233,.15);
}

/* ============================================================
   PRICE CELLS
   ============================================================ */
.price-cell    { color: #0369a1; font-weight: 500; }
.subtotal-cell { color: #0369a1; font-size: 15.5px; font-weight: 800; }

/* ============================================================
   REMOVE BUTTON
   ============================================================ */
.btn-remove {
    border: none; background: none;
    color: rgba(239,68,68,.6); font-size: 16px;
    cursor: pointer; padding: 6px 8px; border-radius: 8px;
    transition: color .15s, background .15s, transform .15s;
}
.btn-remove:hover {
    color: #ef4444;
    background: rgba(239,68,68,.1);
    transform: scale(1.15);
}

/* ============================================================
   CART BOTTOM — compact buttons
   ============================================================ */
.cart-bottom {
    display: flex; justify-content: space-between;
    align-items: center; margin-top: 16px;
    flex-wrap: wrap; gap: 10px;
}
.cart-bottom a {
    text-decoration: none; color: #0369a1;
    font-weight: 700; font-size: 13px;
    display: flex; align-items: center; gap: 6px;
    padding: 7px 14px; border-radius: 8px;
    background: rgba(186, 230, 253, 0.35);
    border: 1px solid rgba(125, 211, 252, 0.45);
    transition: all .2s;
}
.cart-bottom a:hover {
    color: #0c4a6e;
    background: rgba(186, 230, 253, 0.65);
    transform: translateX(-2px);
}
.cart-bottom .clear-btn {
    background: none; border: 1px solid rgba(239,68,68,.25);
    color: rgba(239,68,68,.8); cursor: pointer;
    font-size: 13px; font-weight: 600;
    display: flex; align-items: center; gap: 5px;
    padding: 7px 14px; border-radius: 8px;
    transition: all .15s;
}
.cart-bottom .clear-btn:hover {
    color: #ef4444; background: rgba(239,68,68,.1);
    border-color: rgba(239,68,68,.4);
}

/* ============================================================
   SUMMARY BOX — glassmorphism
   ============================================================ */
.summary-box {
    background: rgba(255,255,255,.84);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(186,230,253,.65);
    border-radius: 18px; padding: 24px;
    box-shadow: 0 6px 28px rgba(14,165,233,.12);
    position: sticky; top: 90px;
}

.summary-box h3 {
    font-size: 15px; font-weight: 800;
    text-transform: uppercase; letter-spacing: .5px;
    color: #0c4a6e; margin-bottom: 18px;
    padding-bottom: 14px;
    border-bottom: 1px solid rgba(186,230,253,.55);
    display: flex; align-items: center; gap: 8px;
}
.summary-box h3::before {
    content: ''; width: 20px; height: 3px;
    background: linear-gradient(90deg, #0369a1, #38bdf8);
    border-radius: 2px; display: inline-block;
}

.summary-row {
    display: flex; justify-content: space-between;
    align-items: center; padding: 10px 0;
    font-size: 14px; color: #0369a1;
    border-bottom: 1px solid rgba(186,230,253,.3);
}
.summary-row:last-of-type { border-bottom: none; }
.summary-row .green { color: #16a34a; font-weight: 700; }

.summary-total {
    display: flex; justify-content: space-between;
    align-items: center; margin-top: 14px;
    padding-top: 14px;
    border-top: 2px solid rgba(186,230,253,.55);
    font-size: 18px; font-weight: 800; color: #0c4a6e;
}
.summary-total span:last-child { color: #0369a1; }

/* ============================================================
   CHECKOUT BUTTON
   ============================================================ */
.checkout-btn {
    display: block; width: 100%; margin-top: 18px; padding: 14px;
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    color: #fff; text-align: center; text-decoration: none;
    border-radius: 12px; font-size: 15px; font-weight: 700;
    transition: opacity .2s, transform .18s, box-shadow .2s;
    box-shadow: 0 4px 18px rgba(14,165,233,.38);
    position: relative; overflow: hidden;
}
.checkout-btn::after {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,.28) 50%, transparent 60%);
    transform: translateX(-120%); transition: transform .5s ease; pointer-events: none;
}
.checkout-btn:hover::after { transform: translateX(120%); }
.checkout-btn:hover {
    opacity: .92; transform: translateY(-2px); color: #fff;
    box-shadow: 0 8px 24px rgba(14,165,233,.48);
}

/* ============================================================
   SHARED BTN-SHOP (Compact Size)
   ============================================================ */
.btn-shop {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 18px;
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    color: #fff; text-decoration: none;
    border-radius: 8px; font-weight: 700; font-size: 13px;
    box-shadow: 0 3px 12px rgba(14,165,233,.3);
    transition: opacity .2s, transform .18s, box-shadow .2s;
}
.btn-shop:hover { opacity:.9; transform:translateY(-1px); color:#fff; box-shadow:0 6px 18px rgba(14,165,233,.4); }

/* ============================================================
   CROSS-SELL SECTION — matching "related products" style
   ============================================================ */
.cross-sell {
    margin-top: 44px;
    background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 35%, #7dd3fc 65%, #38bdf8 100%);
    border-radius: 16px; padding: 24px 24px 28px;
    position: relative; overflow: hidden;
    box-shadow: 0 4px 22px rgba(14,165,233,.18);
}
.cross-sell::before {
    content: ''; position: absolute; top:-60px; right:-60px;
    width:200px; height:200px; border-radius:50%;
    background:rgba(255,255,255,.18); pointer-events:none;
}
.cross-sell::after {
    content: ''; position: absolute; bottom:-40px; left:-40px;
    width:140px; height:140px; border-radius:50%;
    background:rgba(255,255,255,.14); pointer-events:none;
}

.cross-sell-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 18px; position: relative; z-index: 1;
}
.cross-sell h2 {
    font-size: 15px; font-weight: 800; margin: 0;
    color: #0c4a6e; text-transform: uppercase; letter-spacing: .3px;
    padding-left: 10px; border-left: 3px solid #0ea5e9;
}

.cross-sell-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(155px, 1fr));
    gap: 14px; position: relative; z-index: 1;
}

.cross-sell-card {
    display: block;
    background: rgba(255,255,255,.85);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,.7);
    border-radius: 12px; overflow: hidden;
    text-decoration: none; color: inherit;
    box-shadow: 0 2px 10px rgba(14,165,233,.08);
    transition: transform .22s cubic-bezier(.16,1,.3,1), box-shadow .22s, border-color .22s;
    position: relative; cursor: pointer;
}
.cross-sell-card:hover {
    transform: translateY(-5px) scale(1.02);
    box-shadow: 0 14px 32px rgba(14,165,233,.22);
    border-color: rgba(255,255,255,.95);
}
/* shine on hover */
.cross-sell-card::after {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,.38) 50%, transparent 60%);
    transform: translateX(-120%); transition: transform .5s ease;
    pointer-events: none; z-index: 3;
}
.cross-sell-card:hover::after { transform: translateX(120%); }

.cross-sell-card img,
.cross-sell-card .no-img {
    width: 100%; height: 130px; display: block;
    object-fit: contain;
    background: linear-gradient(160deg, #f0f9ff, #e0f2fe);
    padding: 6px; box-sizing: border-box;
    transition: transform .35s cubic-bezier(.16,1,.3,1);
}
.cross-sell-card:hover img { transform: scale(1.07); }
.cross-sell-card .no-img {
    display: flex; align-items: center; justify-content: center;
    color: #7dd3fc; font-size: 22px;
}

.cross-sell-body { padding: 12px; }
.cross-sell-name {
    font-size: 12.5px; font-weight: 600; color: #0f4c75;
    line-height: 1.4; margin-bottom: 6px; min-height: 34px;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.cross-sell-price { font-size: 14px; font-weight: 800; color: #0369a1; }

/* ============================================================
   MOBILE
   ============================================================ */
@media (max-width: 768px) {
    .cart-table, .cart-table thead, .cart-table tbody, .cart-table tr, .cart-table td {
        display: block; width: 100%;
    }
    .cart-table thead { display: none; }
    .cart-table tr {
        margin-bottom: 14px; border-radius: 14px; overflow: hidden;
        box-shadow: 0 4px 16px rgba(14,165,233,.1);
        background: rgba(255,255,255,.84);
    }
    .cart-table td { border-bottom: 1px solid rgba(186,230,253,.35); }
    .cart-bottom { flex-direction: column; align-items: flex-start; }
    .cross-sell-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>
@endpush

@section('content')
{{-- Sky Canvas --}}
<canvas id="sky-canvas" aria-hidden="true"></canvas>

<div class="cart-page">
<div class="cart-container">

    <h1 class="reveal">
        <i class="fas fa-shopping-cart"></i>
        Giỏ hàng
    </h1>

    @if(session('success'))
    <div class="alert-box alert-success reveal">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="alert-box alert-error reveal">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
    @endif

    @if(empty($products))
    {{-- ===== EMPTY ===== --}}
    <div class="empty-cart reveal">
        <i class="fas fa-shopping-cart"></i>
        <p>Giỏ hàng của bạn đang trống</p>
        <a href="{{ route('products.index') }}" class="btn-shop">
            <i class="fas fa-store"></i> Tiếp tục mua sắm
        </a>
    </div>

    @else
    <div class="cart-wrapper">

        {{-- ===== LEFT ===== --}}
        <div class="cart-left reveal">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Đơn giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $item)
                    <tr>
                        <td>
                            <div class="cart-product">
                                @if($item['product']->first_image)
                                    <img src="{{ $item['product']->first_image }}" alt="{{ $item['product']->name }}">
                                @else
                                    <div class="no-img"><i class="fas fa-image"></i></div>
                                @endif
                                <div>
                                    <h5>
                                        <a href="{{ route('products.show', $item['product']->slug) }}">
                                            {{ $item['product']->name }}
                                        </a>
                                    </h5>
                                    @if($item['variant'])
                                    <div class="variant-tag">
                                        {{ $item['variant']->variantAttributes->pluck('value')->implode(' - ') }}
                                    </div>
                                    @endif
                                    <div class="brand">{{ $item['product']->brand->name ?? 'ElectronicShop' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="price-cell">{{ number_format($item['price']) }}đ</td>
                        <td>
                            <form action="{{ route('cart.update', $item['key']) }}" method="POST">
                                @csrf @method('PATCH')
                                <input type="number" name="quantity" value="{{ $item['quantity'] }}"
                                       min="1" max="{{ max($item['stock'], 1) }}" class="qty-input"
                                       onchange="this.form.submit()">
                            </form>
                        </td>
                        <td class="subtotal-cell">{{ number_format($item['subtotal']) }}đ</td>
                        <td>
                            <form action="{{ route('cart.remove', $item['key']) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-remove" title="Xóa"
                                        onclick="return confirm('Bạn muốn xóa sản phẩm này?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="cart-bottom">
                <a href="{{ route('products.index') }}">
                    <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
                </a>
                <form action="{{ route('cart.clear') }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="clear-btn"
                            onclick="return confirm('Xóa toàn bộ giỏ hàng?')">
                        <i class="fas fa-trash"></i> Xóa tất cả
                    </button>
                </form>
            </div>
        </div>

        {{-- ===== RIGHT ===== --}}
        <div class="cart-right reveal">
            <div class="summary-box">
                <h3>Thông tin đơn hàng</h3>
                <div class="summary-row">
                    <span>Tạm tính</span>
                    <strong>{{ number_format($total) }}đ</strong>
                </div>
                <div class="summary-row">
                    <span>Phí vận chuyển</span>
                    <span class="green"><i class="fas fa-check-circle" style="margin-right:4px"></i>Miễn phí</span>
                </div>
                <div class="summary-total">
                    <span>Tổng cộng</span>
                    <span>{{ number_format($total) }}đ</span>
                </div>
                <a href="{{ route('checkout.index') }}" class="checkout-btn" id="checkoutBtn">
                    <i class="fas fa-lock" style="margin-right:6px"></i>Tiến hành thanh toán
                </a>
            </div>
        </div>

    </div>
    @endif

    {{-- ===== CROSS-SELL ===== --}}
    @if(!empty($crossSell) && $crossSell->isNotEmpty())
    <div class="cross-sell reveal">
        <div class="cross-sell-header">
            <h2>Có thể bạn cũng thích</h2>
        </div>
        <div class="cross-sell-grid stagger-children">
            @foreach($crossSell as $p)
            <a href="{{ route('products.show', ['slug' => $p->slug, 'from' => 'suggestion', 'via' => 'cart']) }}"
               class="cross-sell-card">
                @if($p->first_image)
                    <img src="{{ $p->first_image }}" alt="{{ $p->name }}" loading="lazy">
                @else
                    <div class="no-img"><i class="fas fa-image fa-lg"></i></div>
                @endif
                <div class="cross-sell-body">
                    <div class="cross-sell-name">{{ $p->name }}</div>
                    <div class="cross-sell-price">{{ number_format($p->sale_price) }}đ</div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

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
    }, { threshold: 0.06, rootMargin: '0px 0px -24px 0px' });
    document.querySelectorAll('.reveal, .stagger-children').forEach(el => io.observe(el));

    /* ---- Checkout button ripple ---- */
    const checkoutBtn = document.getElementById('checkoutBtn');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function (e) {
            const r    = checkoutBtn.getBoundingClientRect();
            const size = Math.max(r.width, r.height) * 1.8;
            const rip  = document.createElement('span');
            rip.className = 'ripple-wave';
            rip.style.cssText = [`width:${size}px`,`height:${size}px`,
                `left:${e.clientX-r.left-size/2}px`,
                `top:${e.clientY-r.top-size/2}px`].join(';');
            checkoutBtn.appendChild(rip);
            rip.addEventListener('animationend', () => rip.remove());
        });
    }

    /* ---- Cross-sell card: 3D tilt ---- */
    document.querySelectorAll('.cross-sell-card').forEach(card => {
        card.addEventListener('mousemove', function (e) {
            const r  = card.getBoundingClientRect();
            const dx = (e.clientX-r.left-r.width/2)/(r.width/2);
            const dy = (e.clientY-r.top-r.height/2)/(r.height/2);
            card.style.transform = `perspective(500px) rotateX(${-dy*5}deg) rotateY(${dx*5}deg) translateY(-5px) scale(1.02)`;
        });
        card.addEventListener('mouseleave', function () {
            card.style.transform = '';
            card.style.transition = 'transform .4s cubic-bezier(.16,1,.3,1), box-shadow .22s, border-color .22s';
            setTimeout(() => card.style.transition = '', 420);
        });
        /* ripple */
        card.style.position = 'relative'; card.style.overflow = 'hidden';
        card.addEventListener('click', function (e) {
            const r = card.getBoundingClientRect();
            const size = Math.max(r.width, r.height) * 1.6;
            const rip  = document.createElement('span');
            rip.className = 'ripple-wave';
            rip.style.cssText = [`width:${size}px`,`height:${size}px`,
                `left:${e.clientX-r.left-size/2}px`,`top:${e.clientY-r.top-size/2}px`].join(';');
            card.appendChild(rip);
            rip.addEventListener('animationend', () => rip.remove());
        });
    });

    /* ---- Qty input: subtle pulse on change ---- */
    document.querySelectorAll('.qty-input').forEach(input => {
        input.addEventListener('change', function () {
            input.style.transform = 'scale(1.08)';
            input.style.transition = 'transform .2s';
            setTimeout(() => { input.style.transform = ''; }, 250);
        });
    });

    /* ---- Remove btn: shake confirm ---- */
    document.querySelectorAll('.btn-remove').forEach(btn => {
        btn.form?.addEventListener('submit', function (e) {
            btn.style.animation = 'none';
        });
    });

    /* ---- Summary row count-up on load ---- */
    document.querySelectorAll('.subtotal-cell, .summary-total span:last-child').forEach(el => {
        const raw = el.textContent.replace(/[^\d]/g,'');
        if (!raw) return;
        const target = parseInt(raw, 10);
        let current  = 0;
        const step   = Math.ceil(target / 40);
        const fmt    = n => n.toLocaleString('vi-VN') + 'đ';
        const timer  = setInterval(() => {
            current = Math.min(current + step, target);
            el.textContent = fmt(current);
            if (current >= target) clearInterval(timer);
        }, 18);
    });

})();
</script>
@endpush
