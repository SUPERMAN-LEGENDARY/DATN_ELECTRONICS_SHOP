@extends('layouts.app')
@section('title', 'Giỏ hàng - ElectronicShop')
@php $showSearch = true; @endphp

@push('styles')
<style>
.cart-page { max-width: 1200px; margin: 0 auto; padding: 16px; }
.cart-layout { display: grid; grid-template-columns: 1fr 320px; gap: 24px; align-items: start; }
.cart-title { font-size: 20px; font-weight: 800; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 16px; }

/* CART LIST */
.cart-select-all {
    background: #fff; border: 1px solid #e0e0e0; border-radius: 8px;
    padding: 12px 16px; display: flex; align-items: center; gap: 10px;
    margin-bottom: 10px; font-size: 14px; font-weight: 500;
}
.cart-select-all input { accent-color: #1565C0; width: 16px; height: 16px; cursor: pointer; }
.cart-item {
    background: #fff; border: 1px solid #e0e0e0; border-radius: 8px;
    padding: 14px 16px; display: flex; align-items: center; gap: 14px;
    margin-bottom: 10px;
}
.cart-item input[type=checkbox] { accent-color: #1565C0; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0; }
.cart-item-img {
    width: 90px; height: 90px; border-radius: 6px; overflow: hidden;
    background: #f5f5f5; display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; color: #ccc;
}
.cart-item-img img { width: 100%; height: 100%; object-fit: cover; }
.cart-item-info { flex: 1; }
.cart-item-name { font-size: 14px; font-weight: 600; color: #1565C0; margin-bottom: 2px; }
.cart-item-variant { font-size: 12px; color: #888; margin-bottom: 4px; }
.cart-item-status { font-size: 12px; color: #2E7D32; font-weight: 500; margin-bottom: 8px; }
.cart-item-actions { display: flex; align-items: center; gap: 14px; }
.cart-item-actions a { font-size: 12px; color: #555; display: flex; align-items: center; gap: 4px; }
.cart-item-actions a:hover { color: #E53935; }
.cart-item-right { display: flex; flex-direction: column; align-items: flex-end; gap: 12px; }
.cart-item-price { font-size: 16px; font-weight: 700; color: #1565C0; }
.qty-control { display: flex; align-items: center; gap: 0; border: 1px solid #e0e0e0; border-radius: 6px; overflow: hidden; }
.qty-control button {
    width: 32px; height: 32px; background: #f5f5f5; border: none; cursor: pointer;
    font-size: 16px; font-weight: 700; color: #555; transition: background .15s;
}
.qty-control button:hover { background: #e8e8e8; }
.qty-control input {
    width: 40px; height: 32px; border: none; border-left: 1px solid #e0e0e0; border-right: 1px solid #e0e0e0;
    text-align: center; font-size: 14px; font-weight: 600; outline: none;
}
.cart-bottom-actions { display: flex; align-items: center; justify-content: space-between; margin-top: 10px; }
.cart-bottom-actions .left { display: flex; gap: 10px; }
.btn-outline-sm {
    padding: 8px 16px; border: 1px solid #e0e0e0; border-radius: 6px;
    background: #fff; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 6px;
    color: #555; font-weight: 500; transition: all .15s;
}
.btn-outline-sm:hover { border-color: #E53935; color: #E53935; }
.continue-link { color: #1565C0; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 4px; }

/* ORDER SUMMARY */
.order-summary { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; position: sticky; top: 80px; }
.order-summary h3 { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #888; margin-bottom: 16px; }
.summary-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; font-size: 14px; }
.summary-row .label { color: #555; }
.summary-row .value { font-weight: 600; }
.discount-row .value { color: #E53935; }
.free-row .value { color: #2E7D32; font-weight: 600; }
.coupon-row { display: flex; gap: 8px; margin: 14px 0; }
.coupon-row input {
    flex: 1; border: 1px solid #e0e0e0; border-radius: 6px; padding: 8px 12px;
    font-size: 13px; outline: none;
}
.coupon-row input:focus { border-color: #1565C0; }
.coupon-row button {
    padding: 8px 14px; background: #1565C0; color: #fff; border: none;
    border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; white-space: nowrap;
}
.summary-divider { border: none; border-top: 1px solid #f0f0f0; margin: 14px 0; }
.total-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; }
.total-row .total-label { font-size: 15px; font-weight: 600; }
.total-row .total-value { font-size: 24px; font-weight: 800; color: #1565C0; }
.total-vat { font-size: 11px; color: #aaa; text-align: right; margin-bottom: 16px; }
.btn-checkout {
    width: 100%; padding: 14px; background: #1565C0; color: #fff; border: none;
    border-radius: 8px; font-size: 15px; font-weight: 700; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 10px;
    transition: background .2s;
}
.btn-checkout:hover { background: #0D47A1; }
.btn-cod {
    width: 100%; padding: 14px; background: #fff; color: #1565C0; border: 2px solid #1565C0;
    border-radius: 8px; font-size: 15px; font-weight: 700; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    transition: all .2s;
}
.btn-cod:hover { background: #EBF3FF; }

/* TRUST GRID */
.trust-mini-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 14px; }
.trust-mini {
    background: #f5f5f5; border-radius: 8px; padding: 12px;
    display: flex; flex-direction: column; align-items: center; gap: 4px; text-align: center;
}
.trust-mini i { color: #1565C0; font-size: 20px; }
.trust-mini .tm-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .3px; }
.trust-mini .tm-sub { font-size: 11px; color: #888; }
</style>
@endpush

@section('content')
<div class="cart-page">
    <div class="breadcrumb">
        <a href="{{ route('home') }}">Trang chủ</a>
        <span>›</span>
        <span>Giỏ hàng</span>
    </div>

    <div class="cart-title">Giỏ hàng <span style="font-size:16px;color:#888;font-weight:500">({{ count($cartItems ?? []) ?: 3 }} sản phẩm)</span></div>

    <div class="cart-layout">
        {{-- LEFT: CART ITEMS --}}
        <div>
            <div class="cart-select-all">
                <input type="checkbox" id="selectAll" checked>
                <label for="selectAll">Chọn tất cả</label>
            </div>

            @forelse($cartItems ?? [] as $item)
            <div class="cart-item">
                <input type="checkbox" name="selected[]" value="{{ $item['id'] }}" checked>
                <div class="cart-item-img">
                    @if($item['image'])<img src="{{ $item['image'] }}" alt="">
                    @else<i class="fas fa-image fa-lg"></i>@endif
                </div>
                <div class="cart-item-info">
                    <div class="cart-item-name">{{ $item['name'] }}</div>
                    <div class="cart-item-variant">{{ $item['variant'] }}</div>
                    <div class="cart-item-status">Còn hàng</div>
                    <div class="cart-item-actions">
                        <a href="{{ route('cart.remove', $item['id']) }}"><i class="fas fa-trash-alt"></i> Xóa</a>
                        <a href="#"><i class="far fa-heart"></i> Lưu để mua sau</a>
                    </div>
                </div>
                <div class="cart-item-right">
                    <div class="cart-item-price">{{ number_format($item['price']) }}đ</div>
                    <div class="qty-control">
                        <button type="button" onclick="changeQty(this,-1)">−</button>
                        <input type="number" value="{{ $item['quantity'] }}" min="1">
                        <button type="button" onclick="changeQty(this,1)">+</button>
                    </div>
                </div>
            </div>
            @empty
            {{-- DEMO DATA --}}
            @foreach([
                ['Samsung Galaxy S24 Ultra 5G','1TB - Titanium Violet','26.490.000'],
                ['Apple AirPods 4','Chống ồn chủ động','4.490.000'],
            ] as $item)
            <div class="cart-item">
                <input type="checkbox" checked>
                <div class="cart-item-img"><i class="fas fa-image fa-lg"></i></div>
                <div class="cart-item-info">
                    <div class="cart-item-name">{{ $item[0] }}</div>
                    <div class="cart-item-variant">{{ $item[1] }}</div>
                    <div class="cart-item-status">Còn hàng</div>
                    <div class="cart-item-actions">
                        <a href="#"><i class="fas fa-trash-alt"></i> Xóa</a>
                        <a href="#"><i class="far fa-heart"></i> Lưu để mua sau</a>
                    </div>
                </div>
                <div class="cart-item-right">
                    <div class="cart-item-price">{{ $item[2] }}đ</div>
                    <div class="qty-control">
                        <button type="button" onclick="changeQty(this,-1)">−</button>
                        <input type="number" value="1" min="1">
                        <button type="button" onclick="changeQty(this,1)">+</button>
                    </div>
                </div>
            </div>
            @endforeach
            @endforelse

            <div class="cart-bottom-actions">
                <div class="left">
                    <button class="btn-outline-sm"><i class="fas fa-trash"></i> Xóa đã chọn</button>
                    <button class="btn-outline-sm"><i class="fas fa-save"></i> Lưu giỏ hàng</button>
                </div>
                <a href="{{ route('products.index') }}" class="continue-link">← Tiếp tục mua hàng</a>
            </div>
        </div>

        {{-- RIGHT: ORDER SUMMARY --}}
        <div>
            <div class="order-summary">
                <h3>Đơn hàng của bạn</h3>
                <div class="summary-row">
                    <span class="label">Tổng ({{ count($cartItems ?? []) ?: 3 }} sản phẩm)</span>
                    <span class="value">59.970.000đ</span>
                </div>
                <div class="summary-row discount-row">
                    <span class="label">Giảm giá</span>
                    <span class="value">- 0đ</span>
                </div>
                <div class="coupon-row">
                    <input type="text" placeholder="Nhập mã giảm giá">
                    <button>Áp dụng</button>
                </div>
                <div class="summary-row free-row">
                    <span class="label">Phí vận chuyển</span>
                    <span class="value">Miễn phí</span>
                </div>
                <hr class="summary-divider">
                <div class="total-row">
                    <span class="total-label">Tổng cộng</span>
                    <span class="total-value">59.970.000đ</span>
                </div>
                <div class="total-vat">(Đã bao gồm VAT)</div>
                <a href="{{ route('checkout') }}" class="btn-checkout">
                    <i class="fas fa-shopping-bag"></i> THANH TOÁN
                </a>
                <button class="btn-cod">
                    <i class="fas fa-truck"></i> Thanh toán khi nhận hàng
                </button>
            </div>

            <div class="trust-mini-grid">
                <div class="trust-mini">
                    <i class="fas fa-truck"></i>
                    <div class="tm-title">Giao miễn phí</div>
                    <div class="tm-sub">Đơn hàng từ 500k</div>
                </div>
                <div class="trust-mini">
                    <i class="fas fa-sync-alt"></i>
                    <div class="tm-title">Đổi trả dễ dàng</div>
                    <div class="tm-sub">Trong vòng 30 ngày</div>
                </div>
                <div class="trust-mini">
                    <i class="fas fa-shield-alt"></i>
                    <div class="tm-title">Cam kết chính hãng</div>
                    <div class="tm-sub">100% bảo hành</div>
                </div>
                <div class="trust-mini">
                    <i class="fas fa-headset"></i>
                    <div class="tm-title">Hỗ trợ 24/7</div>
                    <div class="tm-sub">Hotline: 1900 1234</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function changeQty(btn, delta) {
    const input = btn.parentElement.querySelector('input');
    const val = parseInt(input.value) + delta;
    if (val >= 1) input.value = val;
}
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.cart-item input[type=checkbox]').forEach(cb => cb.checked = this.checked);
});
</script>
@endpush