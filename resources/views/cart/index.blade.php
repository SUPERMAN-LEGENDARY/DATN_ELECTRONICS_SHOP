@extends('layouts.app')
@section('title', 'Giỏ hàng - ElectronicShop')

@push('styles')
<style>
.cart-page { background: #f5f7fa; min-height: 100vh; padding: 24px 0 60px; }
.cart-container { max-width: 1200px; margin: 0 auto; padding: 0 16px; }
.cart-container h1 { font-size: 24px; font-weight: 800; margin-bottom: 20px; }

.alert-box { padding: 14px 18px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; font-weight: 500; }
.alert-success { background: #dcfce7; color: #166534; }
.alert-error   { background: #fee2e2; color: #b91c1c; }

/* ===== EMPTY ===== */
.empty-cart { background: #fff; border-radius: 14px; padding: 80px 20px; text-align: center; box-shadow: 0 2px 12px rgba(0,0,0,.06); }
.empty-cart i { font-size: 64px; color: #d1d5db; margin-bottom: 20px; display: block; }
.empty-cart p { font-size: 16px; color: #6b7280; margin-bottom: 20px; }
.btn-shop { display: inline-block; padding: 12px 30px; background: #2563eb; color: #fff; text-decoration: none; border-radius: 8px; font-weight: 600; }
.btn-shop:hover { background: #1d4ed8; color: #fff; }

/* ===== LAYOUT ===== */
.cart-wrapper { display: flex; gap: 24px; align-items: flex-start; }
.cart-left { flex: 1; min-width: 0; }
.cart-right { width: 340px; flex-shrink: 0; }
@media (max-width: 991px) { .cart-wrapper { flex-direction: column; } .cart-right { width: 100%; } }

/* ===== TABLE ===== */
.cart-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
.cart-table thead { background: #f8fafc; }
.cart-table th { padding: 15px; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: #374151; text-align: left; }
.cart-table td { padding: 16px 15px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; font-size: 14px; }
.cart-table tr:last-child td { border-bottom: none; }

.cart-product { display: flex; align-items: center; gap: 14px; }
.cart-product img, .cart-product .no-img { width: 72px; height: 72px; object-fit: contain; background: #fff; padding: 4px; box-sizing: border-box; border-radius: 10px; border: 1px solid #e5e7eb; flex-shrink: 0; }
.cart-product .no-img { display: flex; align-items: center; justify-content: center; background: #f3f4f6; color: #cbd5e1; font-size: 20px; }
.cart-product h5 { margin: 0 0 4px; font-size: 15px; font-weight: 600; }
.cart-product h5 a { color: #111827; text-decoration: none; }
.cart-product h5 a:hover { color: #2563eb; }
.cart-product .variant-tag { font-size: 12.5px; color: #2563eb; font-weight: 600; }
.cart-product .brand { font-size: 12.5px; color: #6b7280; }

.qty-input { width: 62px; height: 38px; border: 1px solid #d1d5db; border-radius: 8px; text-align: center; font-size: 14px; outline: none; }
.qty-input:focus { border-color: #2563eb; }

.price-cell { color: #374151; }
.subtotal-cell { color: #2563eb; font-size: 15.5px; font-weight: 700; }

.btn-remove { border: none; background: none; color: #ef4444; font-size: 17px; cursor: pointer; padding: 4px 8px; }
.btn-remove:hover { color: #dc2626; }

.cart-bottom { display: flex; justify-content: space-between; align-items: center; margin-top: 18px; flex-wrap: wrap; gap: 10px; }
.cart-bottom a { text-decoration: none; color: #2563eb; font-weight: 600; font-size: 14px; }
.cart-bottom button { background: none; border: none; color: #ef4444; cursor: pointer; font-size: 14px; font-weight: 600; }

/* ===== SUMMARY ===== */
.summary-box { background: #fff; border-radius: 14px; padding: 24px; box-shadow: 0 3px 12px rgba(0,0,0,.06); position: sticky; top: 90px; }
.summary-box h3 { font-size: 20px; font-weight: 700; margin-bottom: 18px; }
.summary-row { display: flex; justify-content: space-between; align-items: center; margin: 14px 0; font-size: 14.5px; color: #374151; }
.summary-row .green { color: #16a34a; font-weight: 600; }
.summary-total { font-size: 19px; font-weight: 800; color: #2563eb; border-top: 1px solid #e5e7eb; padding-top: 14px; margin-top: 6px; }
.checkout-btn { display: block; width: 100%; margin-top: 18px; padding: 14px; background: #2563eb; color: #fff; text-align: center; text-decoration: none; border-radius: 10px; font-size: 15.5px; font-weight: 700; }
.checkout-btn:hover { background: #1d4ed8; color: #fff; }

/* ===== CROSS-SELL ===== */
.cross-sell { margin-top: 40px; }
.cross-sell h2 { font-size: 19px; font-weight: 800; margin-bottom: 16px; }
.cross-sell-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 16px; }
.cross-sell-card { display: block; background: #fff; border-radius: 12px; overflow: hidden; text-decoration: none; color: inherit; box-shadow: 0 2px 8px rgba(0,0,0,.06); transition: transform .15s, box-shadow .15s; }
.cross-sell-card:hover { transform: translateY(-3px); box-shadow: 0 6px 16px rgba(0,0,0,.1); }
.cross-sell-card img, .cross-sell-card .no-img { width: 100%; height: 130px; object-fit: contain; background: #fff; padding: 6px; box-sizing: border-box; display: block; }
.cross-sell-card .no-img { display: flex; align-items: center; justify-content: center; background: #f3f4f6; color: #cbd5e1; font-size: 22px; }
.cross-sell-body { padding: 12px; }
.cross-sell-name { font-size: 13px; font-weight: 600; line-height: 1.3; margin-bottom: 6px; min-height: 34px; }
.cross-sell-price { font-size: 14.5px; font-weight: 700; color: #2563eb; }

/* ===== MOBILE ===== */
@media (max-width: 768px) {
    .cart-table, .cart-table thead, .cart-table tbody, .cart-table tr, .cart-table td { display: block; width: 100%; }
    .cart-table thead { display: none; }
    .cart-table tr { margin-bottom: 14px; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
    .cart-table td { border-bottom: 1px solid #f1f5f9; }
    .cart-bottom { flex-direction: column; align-items: flex-start; }
}
</style>
@endpush

@section('content')
<div class="cart-page">
<div class="cart-container">
    <h1><i class="fas fa-shopping-cart"></i> Giỏ hàng</h1>

    @if(session('success'))
    <div class="alert-box alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert-box alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif

    @if(empty($products))
    <div class="empty-cart">
        <i class="fas fa-shopping-cart"></i>
        <p>Giỏ hàng của bạn đang trống</p>
        <a href="{{ route('products.index') }}" class="btn-shop">Tiếp tục mua sắm</a>
    </div>
    @else
    <div class="cart-wrapper">

        {{-- LEFT --}}
        <div class="cart-left">
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
                                        <a href="{{ route('products.show', $item['product']->slug) }}">{{ $item['product']->name }}</a>
                                    </h5>
                                    @if($item['variant'])
                                    <div class="variant-tag">
                                        Phiên bản: {{ $item['variant']->variantAttributes->pluck('value')->implode(' - ') }}
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
                                <button type="submit" class="btn-remove" title="Xóa" onclick="return confirm('Bạn muốn xóa sản phẩm này?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="cart-bottom">
                <a href="{{ route('products.index') }}">← Tiếp tục mua sắm</a>
                <form action="{{ route('cart.clear') }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" onclick="return confirm('Xóa toàn bộ giỏ hàng?')">Xóa tất cả</button>
                </form>
            </div>
        </div>

        {{-- RIGHT --}}
        <div class="cart-right">
            <div class="summary-box">
                <h3>Thông tin đơn hàng</h3>
                <div class="summary-row"><span>Tạm tính</span><strong>{{ number_format($total) }}đ</strong></div>
                <div class="summary-row"><span>Phí vận chuyển</span><span class="green">Miễn phí</span></div>
                <div class="summary-row summary-total"><span>Tổng cộng</span><span>{{ number_format($total) }}đ</span></div>
                <a href="{{ route('checkout.index') }}" class="checkout-btn">Tiến hành thanh toán</a>
            </div>
        </div>
    </div>
    @endif

    {{-- ===== CÓ THỂ BẠN CŨNG THÍCH ===== --}}
    @if(!empty($crossSell) && $crossSell->isNotEmpty())
    <div class="cross-sell">
        <h2>Có thể bạn cũng thích</h2>
        <div class="cross-sell-grid">
            @foreach($crossSell as $p)
            <a href="{{ route('products.show', ['slug' => $p->slug, 'from' => 'suggestion', 'via' => 'cart']) }}" class="cross-sell-card">
                @if($p->first_image)
                <img src="{{ $p->first_image }}" alt="{{ $p->name }}">
                @else
                <div class="no-img"><i class="fas fa-image"></i></div>
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