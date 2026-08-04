@extends('layouts.app')
@section('title', 'Giỏ hàng - ElectronicShop')

@push('styles')
<style>
/* ============================================================
   DESIGN TOKENS
   ============================================================ */
.cart-page {
    --samsung-gray-dark:  #e5e5e5;
    --samsung-gray-dark-hover:  #e5e5e5;
    --black:              #000000;
    --gray-100:           #F4F4F4;
    --gray-200:           #E5E5E5;
    --gray-400:           #B0B0B0;
    --gray-600:           #767676;
    --white:              #FFFFFF;
    --green:              #0A8A00;
    --red:                #c0392b;
    --radius:             4px;
}

.cart-page {
    background: var(--white);
    padding: 32px 0 80px;
}

.cart-container {
    width: min(1200px, 90%);
    margin: 0 auto;
}

/* ============================================================
   TITLE
   ============================================================ */
.cart-title-row {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 16px;
    padding-bottom: 20px;
    border-bottom: 2px solid var(--black);
    margin-bottom: 32px;
}
.cart-title-row h1 {
    margin: 0;
    font-size: 38px;
    font-weight: 700;
    letter-spacing: -1px;
    line-height: 1.1;
    color: var(--black);
}
.cart-count {
    font-size: 14px;
    color: var(--gray-600);
    font-weight: 500;
    white-space: nowrap;
}

/* ============================================================
   ALERTS
   ============================================================ */
.alert-box {
    padding: 13px 18px;
    border-radius: var(--radius);
    margin-bottom: 18px;
    font-size: 14px;
    font-weight: 500;
    border: 1px solid transparent;
}
.alert-success { background: #eafaf0; color: #0A8A00; border-color: #cdeedd; }
.alert-error   { background: #fdecec; color: #c0392b; border-color: #f6cfcf; }

/* ============================================================
   EMPTY CART
   ============================================================ */
.empty-cart {
    padding: 90px 20px 100px;
    text-align: center;
}
.empty-cart .empty-icon {
    width: 88px;
    height: 88px;
    margin: 0 auto 24px;
    border-radius: 50%;
    border: 1px solid var(--gray-200);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
    color: var(--black);
}
.empty-cart h2 {
    margin: 0 0 12px;
    font-size: 26px;
    font-weight: 700;
    color: var(--black);
}
.empty-cart p {
    margin: 0 0 32px;
    color: var(--gray-600);
    font-size: 15px;
    line-height: 1.6;
}
.btn-pill-primary {
    display: inline-block;
    padding: 15px 32px;
    background: var(--samsung-gray-dark);
    color: var(--white);
    font-weight: 700;
    font-size: 15px;
    text-decoration: none;
    border-radius: 999px;
    border: none;
    cursor: pointer;
    transition: background .18s;
}
.btn-pill-primary:hover { background: var(--samsung-gray-dark-hover); color: var(--white); }

/* ============================================================
   LAYOUT
   ============================================================ */
.cart-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 340px;
    gap: 48px;
    align-items: start;
}
@media (max-width: 991px) {
    .cart-layout { grid-template-columns: 1fr; }
    .cart-right { position: static !important; }
}

/* ============================================================
   TOOLBAR
   ============================================================ */
.cart-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--gray-200);
    font-size: 14px;
    margin-bottom: 4px;
}
.cart-toolbar strong { font-weight: 700; color: var(--black); }

.btn-text {
    padding: 0;
    border: 0;
    background: transparent;
    color: var(--gray-600);
    font-size: 13px;
    cursor: pointer;
    text-decoration: underline;
    text-underline-offset: 3px;
}
.btn-text:hover { color: var(--samsung-gray-dark); }

/* ============================================================
   PRODUCT ROW
   ============================================================ */
.product-row {
    display: grid;
    grid-template-columns: 130px 1fr auto;
    align-items: center;
    gap: 22px;
    padding: 24px 0;
    border-bottom: 1px solid var(--gray-200);
}

.product-image-wrap {
    width: 130px;
    height: 130px;
    background: var(--gray-100);
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
}
.product-image-wrap img {
    width: 110px;
    height: 110px;
    object-fit: contain;
}
.product-image-wrap .no-img {
    color: var(--gray-400);
    font-size: 26px;
}

.product-info { min-width: 0; }
.product-info h5 {
    margin: 0 0 6px;
    font-size: 16px;
    font-weight: 700;
    line-height: 1.3;
}
.product-info h5 a { color: var(--black); text-decoration: none; }
.product-info h5 a:hover { color: var(--samsung-gray-dark); }

.variant-tag {
    display: inline-block;
    margin: 0 0 6px;
    font-size: 12px;
    color: var(--gray-600);
    background: var(--gray-100);
    border-radius: var(--radius);
    padding: 2px 8px;
}
.brand {
    margin: 0 0 16px;
    font-size: 12px;
    color: var(--gray-600);
}
.price-cell {
    font-size: 14px;
    color: var(--gray-600);
    margin: 0 0 16px;
}

.qty-control {
    display: inline-flex;
    align-items: center;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius);
    overflow: hidden;
}
.qty-input {
    width: 56px;
    padding: 8px;
    border: 0;
    text-align: center;
    background: var(--white);
    color: var(--black);
    font-size: 14px;
    font-weight: 600;
}
.qty-input:focus { outline: 2px solid var(--samsung-gray-dark); outline-offset: -2px; }

.product-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 14px;
    min-width: 120px;
}
.subtotal-cell {
    font-size: 17px;
    font-weight: 700;
    color: var(--samsung-gray-dark);
    white-space: nowrap;
}
.btn-remove {
    padding: 0;
    border: 0;
    background: transparent;
    color: var(--gray-600);
    font-size: 13px;
    cursor: pointer;
    text-decoration: underline;
    text-underline-offset: 3px;
}
.btn-remove:hover { color: var(--red); }

/* ============================================================
   CART BOTTOM
   ============================================================ */
.cart-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 20px;
    flex-wrap: wrap;
    gap: 12px;
}
.cart-bottom a {
    color: var(--black);
    font-weight: 600;
    font-size: 13px;
    text-decoration: none;
}
.cart-bottom a:hover { color: var(--samsung-gray-dark); }
.cart-bottom .clear-btn {
    background: none;
    border: 0;
    color: var(--gray-600);
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    text-decoration: underline;
    text-underline-offset: 3px;
}
.cart-bottom .clear-btn:hover { color: var(--red); }

/* ============================================================
   SUMMARY BOX
   ============================================================ */
.cart-right { }
.summary-box {
    background: var(--gray-100);
    border-radius: var(--radius);
    padding: 28px 24px;
    position: sticky;
    top: 90px;
}
.summary-box h3 {
    margin: 0 0 22px;
    font-size: 20px;
    font-weight: 700;
    letter-spacing: -.3px;
    color: var(--black);
}
.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    padding: 12px 0;
    font-size: 14px;
    color: var(--black);
    border-bottom: 1px solid var(--gray-200);
}
.summary-row .green { color: var(--green); font-weight: 700; }
.summary-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    padding: 16px 0 0;
    font-size: 18px;
    color: var(--black);
}
.summary-total span:last-child {
    font-size: 22px;
    font-weight: 800;
    color: var(--samsung-gray-dark);
}
.checkout-btn {
    display: block;
    width: 100%;
    margin-top: 20px;
    padding: 15px 20px;
    background: var(--samsung-gray-dark);
    color: var(--white);
    text-align: center;
    text-decoration: none;
    font-weight: 700;
    font-size: 15px;
    border-radius: 999px;
    box-sizing: border-box;
    transition: background .18s;
}
.checkout-btn:hover { background: var(--samsung-gray-dark-hover); color: var(--white); }

/* ============================================================
   CROSS-SELL
   ============================================================ */
.cross-sell {
    margin-top: 64px;
}
.cross-sell-header {
    margin-bottom: 24px;
}
.cross-sell-eyebrow {
    display: block;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: var(--gray-600);
    margin-bottom: 8px;
}
.cross-sell h2 {
    margin: 0;
    font-size: 30px;
    font-weight: 700;
    letter-spacing: -.5px;
    color: var(--black);
}

.cross-sell-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 20px;
}

.cross-sell-card {
    display: block;
    text-decoration: none;
    color: inherit;
}
.cross-sell-card .cross-sell-image {
    width: 100%;
    aspect-ratio: 1 / 1;
    background: var(--gray-100);
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    margin-bottom: 16px;
}
.cross-sell-card img {
    width: 78%;
    height: 78%;
    object-fit: contain;
}
.cross-sell-card .no-img {
    color: var(--gray-400);
    font-size: 26px;
}
.cross-sell-name {
    font-size: 15px;
    font-weight: 600;
    color: var(--black);
    line-height: 1.4;
    margin-bottom: 8px;
}
.cross-sell-price {
    font-size: 16px;
    font-weight: 700;
    color: var(--black);
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 680px) {
    .cart-title-row h1 { font-size: 28px; letter-spacing: -.5px; }

    .product-row {
        grid-template-columns: 96px 1fr;
        gap: 14px;
    }
    .product-image-wrap { width: 96px; height: 96px; }
    .product-image-wrap img { width: 80px; height: 80px; }

    .product-right {
        grid-column: 1 / -1;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        min-width: 0;
    }

    .cross-sell h2 { font-size: 24px; }
}
</style>
@endpush

@section('content')
<div class="cart-page">
<div class="cart-container">

    <div class="cart-title-row">
        <h1>Giỏ hàng</h1>
        <span class="cart-count">{{ count($products) }} sản phẩm</span>
    </div>

    @if(session('success'))
    <div class="alert-box alert-success">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="alert-box alert-error">
        {{ session('error') }}
    </div>
    @endif

    @if(empty($products))
    {{-- ===== EMPTY ===== --}}
    <div class="empty-cart">
        <div class="empty-icon">🛍</div>
        <h2>Giỏ hàng của bạn đang trống</h2>
        <p>Khám phá những sản phẩm Galaxy mới nhất và tìm sản phẩm phù hợp với bạn.</p>
        <a href="{{ route('products.index') }}" class="btn-pill-primary">
            Tiếp tục mua sắm
        </a>
    </div>

    @else
    <div class="cart-layout">

        {{-- ===== LEFT ===== --}}
        <div class="cart-left">
            <div class="cart-toolbar">
                <strong>{{ count($products) }} sản phẩm</strong>
            </div>

            @foreach($products as $item)
            <div class="product-row">
                <div class="product-image-wrap">
                    @if($item['product']->first_image)
                        <img src="{{ $item['product']->first_image }}" alt="{{ $item['product']->name }}">
                    @else
                        <div class="no-img"><i class="fas fa-image"></i></div>
                    @endif
                </div>

                <div class="product-info">
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
                    <div class="price-cell">{{ number_format($item['price']) }}đ</div>

                    <form action="{{ route('cart.update', $item['key']) }}" method="POST">
                        @csrf @method('PATCH')
                        <div class="qty-control">
                            <input type="number" name="quantity" value="{{ $item['quantity'] }}"
                                   min="1" max="{{ max($item['stock'], 1) }}" class="qty-input"
                                   onchange="this.form.submit()">
                        </div>
                    </form>
                </div>

                <div class="product-right">
                    <div class="subtotal-cell">{{ number_format($item['subtotal']) }}đ</div>
                    <form action="{{ route('cart.remove', $item['key']) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-remove"
                                onclick="return confirm('Bạn muốn xóa sản phẩm này?')">
                            Xóa
                        </button>
                    </form>
                </div>
            </div>
            @endforeach

            <div class="cart-bottom">
                <a href="{{ route('products.index') }}">
                    Tiếp tục mua sắm
                </a>
                <form action="{{ route('cart.clear') }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="clear-btn"
                            onclick="return confirm('Xóa toàn bộ giỏ hàng?')">
                        Xóa tất cả
                    </button>
                </form>
            </div>
        </div>

        {{-- ===== RIGHT ===== --}}
        <div class="cart-right">
            <div class="summary-box">
                <h3>Thông tin đơn hàng</h3>
                <div class="summary-row">
                    <span>Tạm tính</span>
                    <strong>{{ number_format($total) }}đ</strong>
                </div>
                <div class="summary-row">
                    <span>Phí vận chuyển</span>
                    <span class="green">Miễn phí</span>
                </div>
                <div class="summary-total">
                    <span>Tổng cộng</span>
                    <span>{{ number_format($total) }}đ</span>
                </div>
                <a href="{{ route('checkout.index') }}" class="checkout-btn" id="checkoutBtn">
                    Tiến hành thanh toán
                </a>
            </div>
        </div>

    </div>
    @endif

    {{-- ===== CROSS-SELL ===== --}}
    @if(!empty($crossSell) && $crossSell->isNotEmpty())
    <div class="cross-sell">
        <div class="cross-sell-header">
            <span class="cross-sell-eyebrow">Khám phá thêm</span>
            <h2>Có thể bạn cũng thích</h2>
        </div>
        <div class="cross-sell-grid">
            @foreach($crossSell as $p)
            <a href="{{ route('products.show', ['slug' => $p->slug, 'from' => 'suggestion', 'via' => 'cart']) }}"
               class="cross-sell-card">
                <div class="cross-sell-image">
                    @if($p->first_image)
                        <img src="{{ $p->first_image }}" alt="{{ $p->name }}" loading="lazy">
                    @else
                        <div class="no-img"><i class="fas fa-image fa-lg"></i></div>
                    @endif
                </div>
                <div class="cross-sell-name">{{ $p->name }}</div>
                <div class="cross-sell-price">{{ number_format($p->sale_price) }}đ</div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

</div>
</div>
@endsection