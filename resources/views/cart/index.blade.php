@extends('layouts.app')
@section('title', 'Giỏ hàng - ElectronicShop')

@push('styles')
<style>
.cart-wrap { max-width: 1000px; margin: 0 auto; padding: 24px 16px; }
.cart-wrap h1 { font-size: 22px; font-weight: 800; margin-bottom: 20px; }
.cart-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; border: 1px solid #e0e0e0; }
.cart-table th { background: #f5f5f5; padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #555; }
.cart-table td { padding: 14px 16px; border-top: 1px solid #f0f0f0; vertical-align: middle; font-size: 14px; }
.cart-product { display: flex; align-items: center; gap: 14px; }
.cart-product img { width: 64px; height: 64px; object-fit: cover; border-radius: 6px; background: #f5f5f5; }
.cart-product-name { font-weight: 600; font-size: 14px; }
.cart-product-brand { font-size: 12px; color: #888; }
.qty-input { width: 60px; border: 1px solid #e0e0e0; border-radius: 6px; padding: 6px; text-align: center; font-size: 14px; outline: none; }
.btn-remove { background: none; border: none; color: #E53935; cursor: pointer; font-size: 16px; padding: 4px 8px; }
.btn-remove:hover { color: #B71C1C; }
.cart-summary { margin-top: 20px; display: flex; justify-content: flex-end; }
.summary-box { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; min-width: 280px; }
.summary-row { display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 10px; }
.summary-total { font-size: 18px; font-weight: 800; color: #1565C0; border-top: 1px solid #e0e0e0; padding-top: 12px; margin-top: 4px; }
.btn-checkout { display: block; width: 100%; margin-top: 14px; padding: 13px; background: #1565C0; color: #fff; border: none; border-radius: 8px; font-size: 15px; font-weight: 700; cursor: pointer; text-align: center; text-decoration: none; }
.btn-checkout:hover { background: #0D47A1; color: #fff; }
.empty-cart { text-align: center; padding: 60px 0; color: #999; }
.empty-cart i { font-size: 48px; opacity: .3; margin-bottom: 16px; display: block; }
</style>
@endpush

@section('content')
<div class="cart-wrap">
    <h1><i class="fas fa-shopping-cart"></i> Giỏ hàng</h1>

    @if(session('success'))
        <div style="background:#E8F5E9;color:#2E7D32;padding:10px 14px;border-radius:6px;margin-bottom:16px;font-size:14px">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(empty($products))
        <div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <p style="font-size:16px;margin-bottom:16px">Giỏ hàng trống</p>
            <a href="{{ route('products.index') }}" style="color:#1565C0;font-weight:600">← Tiếp tục mua sắm</a>
        </div>
    @else
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
                            <div style="width:64px;height:64px;background:#f0f0f0;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#ccc">
                                <i class="fas fa-image"></i>
                            </div>
                        @endif
                        <div>
                            <div class="cart-product-name">
                                <a href="{{ route('products.show', $item['product']->slug) }}" style="color:inherit;text-decoration:none">
                                    {{ $item['product']->name }}
                                </a>
                            </div>
                            @if($item['variant'])
                            <div class="cart-product-brand" style="color:#1565C0">
                                Phiên bản:
                                {{ $item['variant']->variantAttributes->pluck('value')->implode(' - ') }}
                            </div>
                            @endif
                            <div class="cart-product-brand">{{ $item['product']->brand->name ?? '' }}</div>
                        </div>
                    </div>
                </td>
                <td>{{ number_format($item['price']) }}đ</td>
                <td>
                    <form action="{{ route('cart.update', $item['key']) }}" method="POST" style="display:inline">
                        @csrf @method('PATCH')
                        <input type="number" name="quantity" value="{{ $item['quantity'] }}"
                               min="1" max="{{ max($item['stock'], 1) }}" class="qty-input"
                               onchange="this.form.submit()">
                    </form>
                </td>
                <td style="font-weight:700;color:#1565C0">{{ number_format($item['subtotal']) }}đ</td>
                <td>
                    <form action="{{ route('cart.remove', $item['key']) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-remove" title="Xóa">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:14px">
        <div>
            <a href="{{ route('products.index') }}" style="color:#1565C0;font-size:14px">← Tiếp tục mua sắm</a>
            &nbsp;&nbsp;
            <form action="{{ route('cart.clear') }}" method="POST" style="display:inline">
                @csrf @method('DELETE')
                <button type="submit" style="color:#E53935;font-size:14px;background:none;border:none;cursor:pointer">
                    Xóa tất cả
                </button>
            </form>
        </div>
    </div>

    <div class="cart-summary">
        <div class="summary-box">
            <div class="summary-row"><span>Tạm tính</span><span>{{ number_format($total) }}đ</span></div>
            <div class="summary-row"><span>Phí vận chuyển</span><span style="color:#2E7D32">Miễn phí</span></div>
            <div class="summary-row summary-total"><span>Tổng cộng</span><span>{{ number_format($total) }}đ</span></div>
            <a href="#" class="btn-checkout">Tiến hành thanh toán →</a>
        </div>
    </div>
    @endif
</div>
@endsection