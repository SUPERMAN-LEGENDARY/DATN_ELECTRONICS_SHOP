@extends('layouts.app')
@section('title', 'Thanh toán - ElectronicShop')

@push('styles')
<style>
.checkout-page { background: #f5f7fa; min-height: 100vh; padding: 24px 0 60px; }
.checkout-wrap { max-width: 1200px; margin: 0 auto; padding: 0 16px; }
.checkout-title { font-size: 26px; font-weight: 800; margin-bottom: 26px; }

.alert-error { background: #fee2e2; color: #991b1b; padding: 14px 18px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; }
.alert-error div { margin-bottom: 2px; }

/* ===== STEP INDICATOR (mang tính minh họa) ===== */
.checkout-step { display: flex; justify-content: center; align-items: center; margin-bottom: 32px; }
.step { display: flex; flex-direction: column; align-items: center; color: #9ca3af; font-size: 13px; gap: 6px; }
.step span { width: 36px; height: 36px; border-radius: 50%; background: #ddd; display: flex; justify-content: center; align-items: center; font-weight: 700; font-size: 14px; }
.step.active { color: #2563eb; }
.step.active span { background: #2563eb; color: #fff; }
.step.done span { background: #16a34a; color: #fff; }
.step-line { width: 100px; height: 2px; background: #ddd; margin: 0 14px; }
@media (max-width: 640px) { .step-line { width: 36px; } .step p { display: none; } }

.checkout-layout { display: grid; grid-template-columns: 1fr 380px; gap: 24px; align-items: start; }
@media (max-width: 991px) { .checkout-layout { grid-template-columns: 1fr; } .checkout-right { order: -1; } }

.checkout-box { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.06); padding: 22px; margin-bottom: 20px; }
.checkout-box h2 { font-size: 17px; font-weight: 700; margin-bottom: 18px; display: flex; align-items: center; gap: 10px; }
.checkout-box h2 .num { width: 26px; height: 26px; border-radius: 50%; background: #2563eb; color: #fff; font-size: 13px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }

/* Address */
.addr-option { border: 2px solid #e5e7eb; border-radius: 12px; padding: 14px 16px; margin-bottom: 10px; cursor: pointer; display: flex; gap: 12px; align-items: flex-start; transition: .2s; background: #fff; }
.addr-option:hover { border-color: #2563eb; background: #f8fbff; }
.addr-option input[type=radio] { width: 18px; height: 18px; margin-top: 3px; accent-color: #2563eb; flex-shrink: 0; }
.addr-option .name { font-weight: 600; font-size: 14.5px; color: #111827; }
.addr-option .detail { font-size: 13px; color: #6b7280; margin-top: 3px; }
.badge-default { background: #dbeafe; color: #2563eb; font-size: 11px; padding: 2px 9px; border-radius: 10px; margin-left: 8px; font-weight: 600; }

.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.form-grid .full { grid-column: 1 / -1; }
@media (max-width: 640px) { .form-grid { grid-template-columns: 1fr; } }
.form-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #374151; }
.form-group input, .form-group textarea {
    width: 100%; padding: 11px 14px; border: 1px solid #d1d5db; border-radius: 8px;
    font-size: 14px; outline: none; box-sizing: border-box; transition: .2s;
}
.form-group input:focus, .form-group textarea:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.12); }

/* Payment */
.pay-option { border: 2px solid #e5e7eb; border-radius: 12px; padding: 16px; margin-bottom: 12px; cursor: pointer; display: flex; align-items: center; gap: 14px; transition: .2s; background: #fff; }
.pay-option:hover { border-color: #2563eb; background: #f8fbff; }
.pay-option.active { border-color: #2563eb; background: #eff6ff; }
.pay-option i { width: 30px; font-size: 24px; text-align: center; color: #2563eb; flex-shrink: 0; }
.pay-option .title { font-weight: 600; font-size: 14.5px; color: #111827; }
.pay-option .desc { font-size: 12.5px; color: #6b7280; }

/* Order summary (right) */
.order-box { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.06); padding: 22px; position: sticky; top: 90px; }
.order-box h2 { font-size: 17px; font-weight: 700; margin-bottom: 18px; }

.order-product { display: flex; gap: 12px; margin-bottom: 16px; }
.order-product img, .order-product .no-img { width: 60px; height: 60px; object-fit: contain; background: #fff; padding: 3px; box-sizing: border-box; border-radius: 8px; border: 1px solid #eee; flex-shrink: 0; }
.order-product .no-img { display: flex; align-items: center; justify-content: center; background: #f3f4f6; color: #cbd5e1; }
.order-product .name { font-size: 13.5px; font-weight: 600; margin: 0 0 3px; line-height: 1.3; }
.order-product .meta { font-size: 12.5px; color: #6b7280; }
.order-product .variant-tag { font-size: 12px; color: #2563eb; font-weight: 600; display: block; }

.voucher-row { display: flex; gap: 8px; margin: 16px 0; }
.voucher-row input { flex: 1; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13.5px; outline: none; }
.voucher-row input:focus { border-color: #2563eb; }
.voucher-row button { padding: 0 16px; border: none; background: #2563eb; color: #fff; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 13.5px; }
.voucher-row button:hover { background: #1d4ed8; }

.summary-row { display: flex; justify-content: space-between; font-size: 14.5px; margin: 12px 0; color: #374151; }
.summary-total { font-size: 19px; font-weight: 800; color: #2563eb; border-top: 1px solid #e5e7eb; padding-top: 14px; margin-top: 8px; }

.btn-place-order { display: block; width: 100%; margin-top: 18px; padding: 15px; background: #2563eb; color: #fff; border: none; border-radius: 10px; font-size: 15.5px; font-weight: 700; cursor: pointer; transition: .2s; }
.btn-place-order:hover { background: #1d4ed8; }

.small-box { text-align: center; font-weight: 600; color: #444; font-size: 13px; background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.06); padding: 14px; margin-top: 14px; }
</style>
@endpush

@section('content')
<div class="checkout-page">
<div class="checkout-wrap">
    <h1 class="checkout-title">THANH TOÁN</h1>

    @if(session('error'))
    <div class="alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif
    @if($errors->any())
    <div class="alert-error">
        @foreach($errors->all() as $e)<div><i class="fas fa-exclamation-circle"></i> {{ $e }}</div>@endforeach
    </div>
    @endif

    <div class="checkout-step">
        <div class="step active"><span>1</span><p>Thông tin đơn hàng</p></div>
        <div class="step-line"></div>
        <div class="step"><span>2</span><p>Xác nhận</p></div>
        <div class="step-line"></div>
        <div class="step"><span>3</span><p>Hoàn tất</p></div>
    </div>

    <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm">
        @csrf
        <div class="checkout-layout">
            <div>
                {{-- ─── 1. Địa chỉ giao hàng ─── --}}
                <div class="checkout-box">
                    <h2><span class="num">1</span> Địa chỉ giao hàng</h2>

                    @if($addresses->count())
                    @foreach($addresses as $addr)
                    <label class="addr-option">
                        <input type="radio" name="address_id" value="{{ $addr->id }}"
                            {{ $loop->first ? 'checked' : '' }} onchange="toggleNewAddressForm(false)">
                        <div>
                            <div class="name">{{ $addr->full_name }} - {{ $addr->phone }}
                                @if($addr->is_default)<span class="badge-default">Mặc định</span>@endif
                            </div>
                            <div class="detail">{{ $addr->full_address }}</div>
                        </div>
                    </label>
                    @endforeach
                    <label class="addr-option" style="margin-top:4px">
                        <input type="radio" name="address_id" value="" onchange="toggleNewAddressForm(true)">
                        <div class="name">+ Giao tới địa chỉ khác</div>
                    </label>
                    @endif

                    <div id="newAddressForm" class="form-grid" style="{{ $addresses->count() ? 'display:none;' : '' }}margin-top:8px">
                        <div class="form-group">
                            <label>Họ tên người nhận</label>
                            <input type="text" name="full_name" value="{{ old('full_name', auth()->user()->name) }}">
                        </div>
                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input type="text" name="phone" value="{{ old('phone') }}">
                        </div>
                        <div class="form-group">
                            <label>Tỉnh / Thành phố</label>
                            <input type="text" name="province" value="{{ old('province') }}">
                        </div>
                        <div class="form-group">
                            <label>Quận / Huyện</label>
                            <input type="text" name="district" value="{{ old('district') }}">
                        </div>
                        <div class="form-group">
                            <label>Phường / Xã</label>
                            <input type="text" name="ward" value="{{ old('ward') }}">
                        </div>
                        <div class="form-group">
                            <label>Số nhà, tên đường</label>
                            <input type="text" name="street" value="{{ old('street') }}">
                        </div>
                    </div>
                </div>

                {{-- ─── 2. Phương thức thanh toán ─── --}}
                <div class="checkout-box">
                    <h2><span class="num">2</span> Phương thức thanh toán</h2>

                    <label class="pay-option active" id="optCod">
                        <input type="radio" name="payment_method" value="cod" checked onchange="selectPay('cod')" style="display:none">
                        <i class="fas fa-money-bill-wave"></i>
                        <div>
                            <div class="title">Thanh toán khi nhận hàng (COD)</div>
                            <div class="desc">Trả tiền mặt trực tiếp cho shipper khi nhận hàng</div>
                        </div>
                    </label>

                    <label class="pay-option" id="optMomo">
                        <input type="radio" name="payment_method" value="momo" onchange="selectPay('momo')" style="display:none">
                        <i class="fas fa-wallet" style="color:#A50064"></i>
                        <div>
                            <div class="title">Ví điện tử MoMo</div>
                            <div class="desc">Thanh toán online qua ứng dụng MoMo</div>
                        </div>
                    </label>
                </div>

                {{-- ─── 3. Ghi chú ─── --}}
                <div class="checkout-box">
                    <h2><span class="num">3</span> Ghi chú đơn hàng</h2>
                    <div class="form-group">
                        <textarea name="note" rows="3" placeholder="Ví dụ: giao giờ hành chính...">{{ old('note') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="checkout-right">
                <div class="order-box">
                    <h2>Đơn hàng ({{ count($items) }} sản phẩm)</h2>

                    @foreach($items as $it)
                    <div class="order-product">
                        @if($it['product']->first_image)
                        <img src="{{ $it['product']->first_image }}" alt="{{ $it['product']->name }}">
                        @else
                        <div class="no-img"><i class="fas fa-image"></i></div>
                        @endif
                        <div>
                            <p class="name">{{ $it['product']->name }}</p>
                            @if($it['variant'])
                            <span class="variant-tag">{{ $it['variant']->label }}</span>
                            @endif
                            <span class="meta">{{ number_format($it['price']) }}đ × {{ $it['quantity'] }}</span>
                        </div>
                    </div>
                    @endforeach

                    <div class="voucher-row">
                        <input type="text" name="voucher_code" placeholder="Mã giảm giá (nếu có)" value="{{ old('voucher_code') }}">
                    </div>

                    <div class="summary-row"><span>Tạm tính</span><strong>{{ number_format($subtotal) }}đ</strong></div>
                    <div class="summary-row"><span>Phí vận chuyển</span><span style="color:#16a34a;font-weight:600">Miễn phí</span></div>
                    <div class="summary-row summary-total"><span>Tổng cộng</span><span>{{ number_format($subtotal) }}đ</span></div>

                    <button type="submit" class="btn-place-order">Đặt hàng</button>
                </div>

                <div class="small-box"><i class="fas fa-shield-alt" style="color:#2563eb"></i> Thanh toán an toàn &amp; bảo mật</div>
            </div>
        </div>
    </form>
</div>
</div>

<script>
    function toggleNewAddressForm(show) {
        document.getElementById('newAddressForm').style.display = show ? 'grid' : 'none';
    }

    function selectPay(method) {
        document.getElementById('optCod').classList.toggle('active', method === 'cod');
        document.getElementById('optMomo').classList.toggle('active', method === 'momo');
    }
    document.getElementById('optCod').addEventListener('click', () => {
        document.querySelector('input[value=cod]').checked = true;
        selectPay('cod');
    });
    document.getElementById('optMomo').addEventListener('click', () => {
        document.querySelector('input[value=momo]').checked = true;
        selectPay('momo');
    });
</script>
@endsection