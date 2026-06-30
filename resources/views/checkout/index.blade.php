@extends('layouts.app')
@section('title', 'Thanh toán - ElectronicShop')

@push('styles')
<style>
    .checkout-wrap {
        max-width: 1100px;
        margin: 0 auto;
        padding: 24px 16px;
    }

    .checkout-wrap h1 {
        font-size: 22px;
        font-weight: 800;
        margin-bottom: 20px;
    }

    .checkout-grid {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 24px;
        align-items: start;
    }

    @media (max-width: 860px) {
        .checkout-grid {
            grid-template-columns: 1fr;
        }
    }

    .box {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .box h2 {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .box h2 .num {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #1565C0;
        color: #fff;
        font-size: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .addr-option {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 12px 14px;
        margin-bottom: 10px;
        cursor: pointer;
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    .addr-option:hover {
        border-color: #1565C0;
    }

    .addr-option input[type=radio] {
        margin-top: 3px;
    }

    .addr-option .name {
        font-weight: 600;
        font-size: 14px;
    }

    .addr-option .detail {
        font-size: 13px;
        color: #777;
        margin-top: 2px;
    }

    .badge-default {
        background: #E3F2FD;
        color: #1565C0;
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 10px;
        margin-left: 8px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .form-grid .full {
        grid-column: 1 / -1;
    }

    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 6px;
        color: #444;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        font-size: 14px;
        outline: none;
        box-sizing: border-box;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        border-color: #1565C0;
    }

    .pay-option {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 14px;
        margin-bottom: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .pay-option:hover {
        border-color: #1565C0;
    }

    .pay-option.active {
        border-color: #1565C0;
        background: #F5F9FF;
    }

    .pay-option img,
    .pay-option i {
        width: 28px;
        height: 28px;
        font-size: 24px;
        text-align: center;
        color: #1565C0;
    }

    .pay-option .title {
        font-weight: 600;
        font-size: 14px;
    }

    .pay-option .desc {
        font-size: 12px;
        color: #888;
    }

    .summary-box {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        padding: 20px;
        position: sticky;
        top: 20px;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        font-size: 13px;
        margin-bottom: 10px;
        color: #555;
    }

    .summary-item .name {
        max-width: 220px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        font-size: 14px;
        margin-bottom: 8px;
    }

    .summary-total {
        font-size: 18px;
        font-weight: 800;
        color: #1565C0;
        border-top: 1px solid #e0e0e0;
        padding-top: 12px;
        margin-top: 8px;
    }

    .btn-place-order {
        display: block;
        width: 100%;
        margin-top: 16px;
        padding: 14px;
        background: #1565C0;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
    }

    .btn-place-order:hover {
        background: #0D47A1;
    }

    .voucher-row {
        display: flex;
        gap: 8px;
        margin-bottom: 4px;
    }

    .voucher-row input {
        flex: 1;
    }

    .voucher-row button {
        padding: 0 16px;
        border: 1px solid #1565C0;
        background: #fff;
        color: #1565C0;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
    }

    .alert-error {
        background: #FFEBEE;
        color: #C62828;
        padding: 10px 14px;
        border-radius: 6px;
        margin-bottom: 16px;
        font-size: 14px
    }
</style>
@endpush

@section('content')
<div class="checkout-wrap">
    <h1><i class="fas fa-credit-card"></i> Thanh toán đơn hàng</h1>

    @if(session('error'))
    <div class="alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif
    @if($errors->any())
    <div class="alert-error">
        <i class="fas fa-exclamation-circle"></i>
        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
    </div>
    @endif

    <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm">
        @csrf
        <div class="checkout-grid">
            <div>
                {{-- ─── 1. Địa chỉ giao hàng ─── --}}
                <div class="box">
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

                    <div id="newAddressForm" class="form-grid" style="{{ $addresses->count() ? 'display:none;' : '' }}margin-top:12px">
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
                <div class="box">
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
                <div class="box">
                    <h2><span class="num">3</span> Ghi chú đơn hàng</h2>
                    <div class="form-group">
                        <textarea name="note" rows="3" placeholder="Ví dụ: giao giờ hành chính...">{{ old('note') }}</textarea>
                    </div>
                </div>
            </div>

            <div>
                <div class="summary-box">
                    <h2 style="font-size:16px;font-weight:700;margin-bottom:14px">Đơn hàng ({{ count($items) }} sản phẩm)</h2>

                    @foreach($items as $it)
                    <div class="summary-item">
                        <span class="name">{{ $it['product']->name }}{{ $it['variant'] ? ' ('.$it['variant']->label.')' : '' }} x{{ $it['quantity'] }}</span>
                        <span>{{ number_format($it['line_total']) }}đ</span>
                    </div>
                    @endforeach

                    <hr style="border:none;border-top:1px solid #f0f0f0;margin:12px 0">

                    <div class="voucher-row">
                        <input type="text" name="voucher_code" placeholder="Mã giảm giá (nếu có)" value="{{ old('voucher_code') }}">
                    </div>

                    <div class="summary-row" style="margin-top:10px"><span>Tạm tính</span><span>{{ number_format($subtotal) }}đ</span></div>
                    <div class="summary-row"><span>Phí vận chuyển</span><span style="color:#2E7D32">Miễn phí</span></div>
                    <div class="summary-row summary-total"><span>Tổng cộng</span><span>{{ number_format($subtotal) }}đ</span></div>

                    <button type="submit" class="btn-place-order">Đặt hàng</button>
                </div>
            </div>
        </div>
    </form>
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