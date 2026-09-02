{{-- resources/views/checkout/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Thanh toán - ElectronicShop')

@push('styles')
<style>
    /* ============================================================
       SAMSUNG OFFICIAL UI - GIAO DIỆN THANH TOÁN (SAMSUNG BLUE THEME)
       ============================================================ */
    :root {
        --samsung-gray-dark: #363636;
        --samsung-gray-dark-hover:  #1f1f1f;
        --samsung-light-gray-dark: #f0f4ff;
        --samsung-accent-blue: #2189ff;
        --samsung-black: #000000;
        --samsung-dark: #111111;
        --samsung-gray-dark: #363636;
        --samsung-gray-mid: #707070;
        --samsung-gray-light: #e0e0e0;
        --samsung-bg-section: #f8f9fa;
        --samsung-radius-pill: 30px;
        --samsung-radius-card: 16px;
    }

    body {
        background: #ffffff;
        color: var(--samsung-dark);
        font-family: "SamsungOne", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        -webkit-font-smoothing: antialiased;
    }

    /* Ẩn các hiệu ứng canvas và bong bóng của bản cũ */
    #sky-canvas, .bubble, .ripple-wave {
        display: none !important;
    }

    /* Scroll Reveal đơn giản */
    .reveal {
        opacity: 0; transform: translateY(15px);
        transition: opacity 0.5s cubic-bezier(0.16, 1, 0.3, 1), transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .reveal.revealed { opacity: 1; transform: translateY(0); }

    .samsung-checkout-page {
        padding: 48px 20px 80px;
        min-height: 100vh;
        background: #ffffff;
    }

    .checkout-wrap {
        max-width: 1200px;
        margin: 0 auto;
    }

    .checkout-title {
        font-size: clamp(28px, 4vw, 38px);
        font-weight: 800;
        margin-bottom: 36px;
        letter-spacing: -0.8px;
        color: var(--samsung-black);
    }

    /* ============================================================
       ALERTS
       ============================================================ */
    .alert-error {
        background: #fff5f5;
        color: #d32f2f;
        border: 1px solid #ffcdd2;
        padding: 16px 20px;
        border-radius: 12px;
        margin-bottom: 28px;
        font-size: 14px;
        font-weight: 600;
    }
    .alert-error div {
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .alert-error div:last-child {
        margin-bottom: 0;
    }

    /* ============================================================
       STEP INDICATOR (SAMSUNG STYLE)
       ============================================================ */
    .checkout-step {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 44px;
        font-size: 14px;
        font-weight: 600;
        color: var(--samsung-gray-mid);
        overflow-x: auto;
        white-space: nowrap;
        padding-bottom: 8px;
    }
    
    .checkout-step .step {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .checkout-step .step.active {
        color: var(--samsung-black);
        font-weight: 700;
    }

    .checkout-step .step span {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #f1f3f5;
        color: var(--samsung-gray-mid);
        font-size: 13px;
        font-weight: 700;
        transition: all 0.25s ease;
    }

    .checkout-step .step.active span {
        background: var(--samsung-gray-dark);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(20, 40, 160, 0.25);
    }

    .checkout-step .step-line {
        width: 48px;
        height: 2px;
        background: var(--samsung-gray-light);
    }

    /* ============================================================
       LAYOUT GRID
       ============================================================ */
    .checkout-layout {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 56px;
        align-items: start;
    }

    @media (max-width: 991px) {
        .checkout-layout {
            grid-template-columns: 1fr;
            gap: 40px;
        }
        .checkout-right {
            order: -1;
        }
    }

    /* ============================================================
       SECTIONS & HEADINGS
       ============================================================ */
    .checkout-box {
        margin-bottom: 44px;
    }

    .checkout-box h2 {
        font-size: 20px;
        font-weight: 800;
        color: var(--samsung-black);
        margin-bottom: 24px;
        padding-bottom: 14px;
        border-bottom: 2px solid var(--samsung-black);
        display: flex;
        align-items: center;
        gap: 12px;
        letter-spacing: -0.3px;
    }

    .checkout-box h2 .num {
        display: none;
    }

    /* ============================================================
       RADIO OPTIONS (ADDRESS & PAYMENT) — SAMSUNG ACTIVE BLUE
       ============================================================ */
    .addr-option, .pay-option {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        border: 1.5px solid var(--samsung-gray-light);
        border-radius: var(--samsung-radius-card);
        padding: 20px;
        margin-bottom: 16px;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #ffffff;
    }

    .addr-option:hover, .pay-option:hover {
        border-color: var(--samsung-gray-dark);
    }

    /* Active state (JS trigger hoặc :has input:checked) */
    .pay-option.active,
    .pay-option:has(input[type="radio"]:checked),
    .addr-option:has(input[type="radio"]:checked) {
        border-color: var(--samsung-gray-dark);
        border-width: 2px;
        background: var(--samsung-light-gray-dark);
        box-shadow: 0 4px 16px rgba(20, 40, 160, 0.08);
    }

    .addr-option input[type=radio], .pay-option input[type=radio] {
        width: 20px;
        height: 20px;
        margin-top: 2px;
        accent-color: var(--samsung-gray-dark);
        flex-shrink: 0;
        cursor: pointer;
    }

    .option-content {
        flex: 1;
    }

    .option-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--samsung-black);
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .option-desc {
        font-size: 14px;
        color: var(--samsung-gray-mid);
        line-height: 1.5;
    }

    .badge-default {
        background: #e8f0fe;
        color: var(--samsung-gray-dark);
        font-size: 12px;
        padding: 3px 10px;
        border-radius: 12px;
        font-weight: 700;
    }

    .pay-option i {
        font-size: 24px;
        color: var(--samsung-gray-dark);
        margin-top: 2px;
    }
    .pay-option#optMomo i { color: #a50064; }
    .pay-option#optVnpay i { color: #005a9e; } /* Brand VNPay Blue */

    /* ============================================================
       FORM FIELDS
       ============================================================ */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        background: var(--samsung-bg-section);
        padding: 24px;
        border-radius: var(--samsung-radius-card);
        border: 1px solid #e9ecef;
    }

    @media (max-width: 640px) {
        .form-grid { grid-template-columns: 1fr; }
    }

    .form-group.full { grid-column: 1 / -1; }

    .form-group label {
        display: block;
        font-size: 13.5px;
        font-weight: 700;
        margin-bottom: 8px;
        color: var(--samsung-gray-dark);
    }

    .form-group input, .form-group textarea {
        width: 100%;
        padding: 14px 16px;
        border: 1px solid #cccccc;
        border-radius: 10px;
        font-size: 14.5px;
        outline: none;
        box-sizing: border-box;
        transition: border-color 0.2s, box-shadow 0.2s;
        background: #ffffff;
        color: var(--samsung-black);
        font-family: inherit;
    }

    .form-group input:focus, .form-group textarea:focus {
        border-color: var(--samsung-gray-dark);
        box-shadow: 0 0 0 3px rgba(20, 40, 160, 0.12);
    }

    /* ============================================================
       ADDRESS COMBOBOX (Tỉnh/Thành phố, Quận/Huyện, Phường/Xã)
       ============================================================ */
    .addr-combo { position: relative; }

    .addr-combo-input.is-disabled {
        background: #f0f0f0;
        color: #999;
        cursor: not-allowed;
    }

    .addr-combo-list {
        display: none;
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        right: 0;
        z-index: 60;
        max-height: 260px;
        overflow-y: auto;
        background: #fff;
        border: 1px solid #cccccc;
        border-radius: 10px;
        box-shadow: 0 14px 34px rgba(0, 0, 0, .10);
        padding: 6px;
    }

    .addr-combo-list.is-open { display: block; }

    .addr-combo-item {
        padding: 10px 12px;
        border-radius: 8px;
        font-size: 14.5px;
        color: var(--samsung-black);
        cursor: pointer;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .addr-combo-item:hover,
    .addr-combo-item.is-active { background: #f2f2f2; }

    .addr-combo-empty,
    .addr-combo-hint {
        padding: 10px 12px;
        font-size: 13px;
        color: #999;
    }

    /* ============================================================
       ORDER SUMMARY BOX — SAMSUNG STYLE
       ============================================================ */
    .order-box {
        background: var(--samsung-bg-section);
        border-radius: var(--samsung-radius-card);
        padding: 32px;
        border: 1px solid #e9ecef;
        position: sticky;
        top: 40px;
    }

    .order-box h2 {
        font-size: 20px;
        font-weight: 800;
        color: var(--samsung-black);
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--samsung-gray-light);
    }

    .order-product {
        display: flex;
        gap: 16px;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid #e9ecef;
    }
    .order-product:last-of-type {
        border-bottom: none;
        padding-bottom: 0;
    }

    .order-product img, .order-product .no-img {
        width: 76px;
        height: 76px;
        object-fit: contain;
        background: #ffffff;
        border-radius: 10px;
        border: 1px solid #e5e5e5;
        padding: 4px;
        flex-shrink: 0;
    }

    .order-product .no-img {
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--samsung-gray-mid);
        font-size: 13px;
    }

    .product-info .name {
        font-size: 14.5px;
        font-weight: 700;
        margin: 0 0 4px;
        line-height: 1.4;
        color: var(--samsung-black);
    }

    .product-info .variant-tag {
        font-size: 12.5px;
        color: var(--samsung-gray-dark);
        background: var(--samsung-light-gray-dark);
        padding: 2px 8px;
        border-radius: 6px;
        font-weight: 600;
        display: inline-block;
        margin-bottom: 6px;
    }

    .product-info .meta {
        font-size: 14.5px;
        font-weight: 700;
        color: var(--samsung-black);
    }

    .voucher-row {
        display: flex;
        gap: 10px;
        margin-bottom: 16px;
    }
    .voucher-row input {
        flex: 1;
        border: 1.5px solid var(--samsung-gray-light);
        border-radius: 10px;
        padding: 11px 14px;
        font-size: 14px;
        font-family: inherit;
        outline: none;
        transition: border-color .2s;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .voucher-row input:focus { border-color: var(--samsung-gray-dark); }
    .voucher-row input.is-valid   { border-color: #16a34a; background: #f0fdf4; }
    .voucher-row input.is-invalid { border-color: #dc2626; background: #fff5f5; }

    .voucher-row button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;

        padding: 0 22px;

        background: var(--samsung-gray-dark);
        color: #ffffff;
        border: none;
        border-radius: var(--samsung-radius-pill);

        font-weight: 700;
        cursor: pointer;
        font-size: 14px;
        white-space: nowrap;

        box-shadow: 0 6px 20px rgba(20, 40, 160, 0.25);
        transition: background 0.2s ease, transform 0.18s ease, box-shadow 0.2s ease;
    }
    .voucher-row button:hover {
        background: var(--samsung-gray-dark-hover);
        transform: translateY(-1px);
        box-shadow: 0 8px 24px rgba(20, 40, 160, 0.35);
    }
    .voucher-row button:disabled {
        background: var(--samsung-gray-dark);
        box-shadow: none;
        cursor: not-allowed;
        transform: none;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        font-size: 14.5px;
        margin: 14px 0;
        color: var(--samsung-gray-dark);
    }

    .summary-total {
        font-size: 21px;
        font-weight: 800;
        color: var(--samsung-black);
        border-top: 2px solid var(--samsung-black);
        padding-top: 20px;
        margin-top: 20px;
        display: flex;
        justify-content: space-between;
    }
    .summary-total span:last-child {
        color: var(--samsung-gray-dark);
    }

    /* SAMSUNG PILL BUTTON */
    .btn-place-order {
        display: block;
        width: 100%;
        margin-top: 28px;
        padding: 16px;
        background: var(--samsung-gray-dark);
        color: #ffffff;
        border: none;
        border-radius: var(--samsung-radius-pill);
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s ease, transform 0.18s ease, box-shadow 0.2s ease;
        box-shadow: 0 6px 20px rgba(20, 40, 160, 0.25);
    }
    .btn-place-order:hover {
        background: var(--samsung-gray-dark-hover);
        transform: translateY(-1px);
        box-shadow: 0 8px 24px rgba(20, 40, 160, 0.35);
    }
    .btn-place-order:disabled {
        background: var(--samsung-gray-dark);
        box-shadow: none;
        cursor: not-allowed;
    }
</style>
@endpush

@section('content')
{{-- Giữ nguyên HTML element này để script JS không bị lỗi undefined --}}
<canvas id="sky-canvas" aria-hidden="true"></canvas>

<div class="samsung-checkout-page">
<div class="checkout-wrap">
    <h1 class="checkout-title reveal">Thanh toán</h1>

    @if(session('error'))
    <div class="alert-error reveal">
        <i class="fas fa-exclamation-circle" style="margin-right:6px"></i> {{ session('error') }}
    </div>
    @endif
    @if($errors->any())
    <div class="alert-error reveal">
        @foreach($errors->all() as $e)
        <div><i class="fas fa-exclamation-circle" style="margin-right:6px"></i> {{ $e }}</div>
        @endforeach
    </div>
    @endif

    {{-- Step indicator --}}
    <div class="checkout-step reveal">
        <div class="step active"><span>1</span><p>Thông tin đơn hàng</p></div>
        <div class="step-line"></div>
        <div class="step"><span>2</span><p>Xác nhận</p></div>
        <div class="step-line"></div>
        <div class="step"><span>3</span><p>Hoàn tất</p></div>
    </div>

    <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm">
        @csrf
        <div class="checkout-layout">

            {{-- ===== LEFT COLUMN ===== --}}
            <div class="stagger-children">

                {{-- ─── 1. Địa chỉ giao hàng ─── --}}
                <div class="checkout-box">
                    <h2><span class="num">1</span> Địa chỉ giao hàng</h2>

                    @if($addresses->count())
                        @foreach($addresses as $addr)
                        <label class="addr-option">
                            <input type="radio" name="address_id" value="{{ $addr->id }}"
                                {{ $loop->first ? 'checked' : '' }} onchange="toggleNewAddressForm(false)">
                            <div class="option-content">
                                <div class="option-title">
                                    {{ $addr->full_name }} — {{ $addr->phone }}
                                    @if($addr->is_default)<span class="badge-default">Mặc định</span>@endif
                                </div>
                                <div class="option-desc">{{ $addr->full_address }}</div>
                            </div>
                        </label>
                        @endforeach
                        
                        <label class="addr-option">
                            <input type="radio" name="address_id" value="" onchange="toggleNewAddressForm(true)">
                            <div class="option-content">
                                <div class="option-title"><i class="fas fa-plus-circle" style="color:var(--samsung-gray-dark);margin-right:6px"></i> Giao tới địa chỉ khác</div>
                            </div>
                        </label>
                    @endif

                    <div id="newAddressForm" class="form-grid" style="{{ $addresses->count() ? 'display:none;' : '' }}margin-top:16px">
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
                            <div class="addr-combo" id="provinceCombo">
                                <input type="text" class="addr-combo-input" id="provinceInput" name="province" value="{{ old('province') }}" autocomplete="off" placeholder="Tỉnh / Thành phố">
                                <input type="hidden" name="province_code" id="provinceCode">
                                <div class="addr-combo-list" id="provinceList"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Quận / Huyện</label>
                            <div class="addr-combo" id="districtCombo">
                                <input type="text" class="addr-combo-input is-disabled" id="districtInput" name="district" value="{{ old('district') }}" autocomplete="off" placeholder="Chọn Tỉnh / Thành phố trước" disabled>
                                <input type="hidden" name="district_code" id="districtCode">
                                <div class="addr-combo-list" id="districtList"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Phường / Xã</label>
                            <div class="addr-combo" id="wardCombo">
                                <input type="text" class="addr-combo-input is-disabled" id="wardInput" name="ward" value="{{ old('ward') }}" autocomplete="off" placeholder="Chọn Quận / Huyện trước" disabled>
                                <input type="hidden" name="ward_code" id="wardCode">
                                <div class="addr-combo-list" id="wardList"></div>
                            </div>
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

                    {{-- ID optCod và optMomo được giữ nguyên để trigger active class bằng JS --}}
                    <label class="pay-option active" id="optCod">
                        <input type="radio" name="payment_method" value="cod" checked onchange="selectPay('cod')" style="display:none">
                        <i class="fas fa-money-bill-wave"></i>
                        <div class="option-content">
                            <div class="option-title">Thanh toán khi nhận hàng (COD)</div>
                            <div class="option-desc">Trả tiền mặt trực tiếp cho người giao hàng khi nhận hàng</div>
                        </div>
                    </label>

                    <label class="pay-option" id="optMomo">
                        <input type="radio" name="payment_method" value="momo" onchange="selectPay('momo')" style="display:none">
                        <i class="fas fa-wallet"></i>
                        <div class="option-content">
                            <div class="option-title">Ví điện tử MoMo</div>
                            <div class="option-desc">Thanh toán an toàn & nhanh chóng qua ứng dụng MoMo</div>
                        </div>
                    </label>
                    <label class="pay-option" id="optVnpay">
                        <input type="radio" name="payment_method" value="vnpay" onchange="selectPay('vnpay')" style="display:none">
                        <i class="fas fa-credit-card"></i>
                        <div class="option-content">
                            <div class="option-title">Thanh toán qua VNPAY</div>
                            <div class="option-desc">Thanh toán an toàn qua Cổng VNPAY (Thẻ ATM, Visa, QR Code)</div>
                        </div>
                    </label>
                </div>

                {{-- ─── 3. Ghi chú ─── --}}
                <div class="checkout-box">
                    <h2><span class="num">3</span> Ghi chú đơn hàng (Tùy chọn)</h2>
                    <div class="form-group">
                        <textarea name="note" rows="4" placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi giao...">{{ old('note') }}</textarea>
                    </div>
                </div>

            </div>

            {{-- ===== RIGHT COLUMN ===== --}}
            <div class="checkout-right reveal">
                <div class="order-box">
                    <h2>Đơn hàng ({{ count($items) }} sản phẩm)</h2>

                    @foreach($items as $it)
                    <div class="order-product">
                        @if($it['product']->first_image)
                        <img src="{{ $it['product']->first_image }}" alt="{{ $it['product']->name }}">
                        @else
                        <div class="no-img">No Image</div>
                        @endif
                        <div class="product-info">
                            <p class="name">{{ $it['product']->name }}</p>
                            @php
        $variantText = '';
        if ($it['variant']) {
            $variantText = $it['variant']->attributes_text;
        } elseif ($it['product'] && $it['product']->variants->isNotEmpty()) {
            $variantAttrIds = \App\Models\ProductVariantAttribute::whereIn('variant_id', $it['product']->variants->pluck('id'))->pluck('attribute_id')->unique();
            if ($variantAttrIds->isNotEmpty()) {
                $variantText = (string) \App\Models\ProductAttribute::where('product_id', $it['product']->id)
                    ->whereIn('attribute_id', $variantAttrIds)
                    ->get()->sortBy('attribute_id')->pluck('value')->implode(' - ');
            }
        }
    @endphp
    @if($variantText)
    <span class="variant-tag">{{ $variantText }}</span>
    @endif
                            <span class="meta">{{ number_format($it['price']) }}đ × {{ $it['quantity'] }}</span>
                        </div>
                    </div>
                    @endforeach

                    <div class="voucher-row">
                        <input type="text" name="voucher_code" id="voucherInput"
                               placeholder="Nhập mã giảm giá..." value="{{ old('voucher_code') }}"
                               autocomplete="off" style="text-transform:uppercase">
                        <button type="button" id="voucherBtn">
                            <i class="fas fa-tag" id="voucherBtnIcon"></i>
                            <span id="voucherBtnText">Áp dụng</span>
                        </button>
                    </div>

                    {{-- Feedback áp mã --}}
                    <div class="voucher-feedback" id="voucherFeedback"></div>

                    <div class="summary-row">
                        <span>Tạm tính</span>
                        <strong id="summarySubtotal">{{ number_format($subtotal) }}đ</strong>
                    </div>
                    <div class="summary-discount" id="discountRow">
                        <span><i class="fas fa-tag" style="color:#16a34a"></i> Giảm giá (<span id="discountPct"></span>%)</span>
                        <span class="discount-val" id="summaryDiscount"></span>
                    </div>
                    <div class="summary-row">
                        <span>Phí vận chuyển</span>
                        <span style="font-weight:700;color:#16a34a">
                            <i class="fas fa-check-circle" style="margin-right:4px"></i>Miễn phí
                        </span>
                    </div>
                    <div class="summary-total">
                        <span>Tổng cộng</span>
                        <span id="summaryTotal">{{ number_format($subtotal) }}đ</span>
                    </div>

                    {{-- Nút Submit giữ nguyên ID để JS xử lý spinner --}}
                    <button type="submit" class="btn-place-order" id="btnPlaceOrder">
                        Đặt hàng ngay
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>
</div>
@endsection

@push('scripts')
{{-- =========================================================================
     KHÔNG THAY ĐỔI ĐOẠN SCRIPT DƯỚI ĐÂY ĐỂ ĐẢM BẢO 100% NGHIỆP VỤ NHƯ YÊU CẦU
     ========================================================================= --}}
<script>
/* ============================================================
   ĐỊA CHỈ: TỈNH / QUẬN-HUYỆN / PHƯỜNG-XÃ (combobox có gợi ý + lọc)
   ============================================================ */
(function () {

    const provinceInput = document.getElementById('provinceInput');
    if (!provinceInput) return;

    const provinceCode  = document.getElementById('provinceCode');
    const provinceList  = document.getElementById('provinceList');

    const districtInput = document.getElementById('districtInput');
    const districtCode  = document.getElementById('districtCode');
    const districtList  = document.getElementById('districtList');

    const wardInput = document.getElementById('wardInput');
    const wardCode  = document.getElementById('wardCode');
    const wardList  = document.getElementById('wardList');

    const API_BASE = 'https://provinces.open-api.vn/api';

    let provinces      = [];
    let districtsCache = {};
    let wardsCache      = {};

    function stripAccents(str) {
        return str
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/đ/g, 'd')
            .replace(/Đ/g, 'D')
            .toLowerCase()
            .trim();
    }

    const ADMIN_PREFIXES = [
        'thanh pho', 'tinh', 'quan', 'huyen', 'thi xa',
        'phuong', 'xa', 'thi tran',
    ];

    function coreName(name) {
        let s = stripAccents(name);
        for (let i = 0; i < ADMIN_PREFIXES.length; i++) {
            const p = ADMIN_PREFIXES[i] + ' ';
            if (s.indexOf(p) === 0) {
                s = s.slice(p.length);
                break;
            }
        }
        return s;
    }

    function setDisabled(input, disabled, placeholder) {
        input.disabled = disabled;
        input.classList.toggle('is-disabled', disabled);
        if (placeholder) input.placeholder = placeholder;
    }

    function closeList(listEl) {
        listEl.classList.remove('is-open');
        listEl.innerHTML = '';
    }

    function renderList(listEl, items, onPick, query) {
        listEl.innerHTML = '';

        const q = stripAccents(query || '');

        const filtered = q
            ? items.filter(function (it) {
                return coreName(it.name).indexOf(q) === 0
                    || stripAccents(it.name).indexOf(q) === 0;
            })
            : items;

        if (!filtered.length) {
            const empty = document.createElement('div');
            empty.className = 'addr-combo-empty';
            empty.textContent = 'Không tìm thấy kết quả phù hợp';
            listEl.appendChild(empty);
            listEl.classList.add('is-open');
            return;
        }

        filtered.slice(0, 200).forEach(function (it) {
            const opt = document.createElement('div');
            opt.className = 'addr-combo-item';
            opt.textContent = it.name;
            opt.addEventListener('mousedown', function (e) {
                e.preventDefault();
                onPick(it);
            });
            listEl.appendChild(opt);
        });

        listEl.classList.add('is-open');
    }

    function fetchJSON(url) {
        return fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(function (res) {
                if (!res.ok) throw new Error('Network error');
                return res.json();
            });
    }

    function loadProvinces() {
        if (provinces.length) return Promise.resolve(provinces);

        provinceList.innerHTML = '<div class="addr-combo-hint">Đang tải danh sách...</div>';
        provinceList.classList.add('is-open');

        return fetchJSON(API_BASE + '/p/')
            .then(function (data) {
                provinces = data.map(function (p) {
                    return { code: p.code, name: p.name };
                });
                return provinces;
            })
            .catch(function () {
                provinceList.innerHTML = '<div class="addr-combo-empty">Không thể tải danh sách, vui lòng thử lại</div>';
                return [];
            });
    }

    function loadDistricts(pCode) {
        if (districtsCache[pCode]) return Promise.resolve(districtsCache[pCode]);

        districtList.innerHTML = '<div class="addr-combo-hint">Đang tải danh sách...</div>';
        districtList.classList.add('is-open');

        return fetchJSON(API_BASE + '/p/' + pCode + '?depth=2')
            .then(function (data) {
                const list = (data.districts || []).map(function (d) {
                    return { code: d.code, name: d.name };
                });
                districtsCache[pCode] = list;
                return list;
            })
            .catch(function () {
                districtList.innerHTML = '<div class="addr-combo-empty">Không thể tải danh sách, vui lòng thử lại</div>';
                return [];
            });
    }

    function loadWards(dCode) {
        if (wardsCache[dCode]) return Promise.resolve(wardsCache[dCode]);

        wardList.innerHTML = '<div class="addr-combo-hint">Đang tải danh sách...</div>';
        wardList.classList.add('is-open');

        return fetchJSON(API_BASE + '/d/' + dCode + '?depth=2')
            .then(function (data) {
                const list = (data.wards || []).map(function (w) {
                    return { code: w.code, name: w.name };
                });
                wardsCache[dCode] = list;
                return list;
            })
            .catch(function () {
                wardList.innerHTML = '<div class="addr-combo-empty">Không thể tải danh sách, vui lòng thử lại</div>';
                return [];
            });
    }

    function resetDistrictAndWard() {
        districtInput.value = '';
        districtCode.value  = '';
        setDisabled(districtInput, true, 'Chọn Tỉnh / Thành phố trước');
        closeList(districtList);
        resetWard();
    }

    function resetWard() {
        wardInput.value = '';
        wardCode.value  = '';
        setDisabled(wardInput, true, 'Chọn Quận / Huyện trước');
        closeList(wardList);
    }

    /* ----- Tỉnh / Thành phố ----- */
    provinceInput.addEventListener('focus', function () {
        loadProvinces().then(function (list) {
            renderList(provinceList, list, pickProvince, '');
        });
    });

    provinceInput.addEventListener('input', function () {
        provinceCode.value = '';
        loadProvinces().then(function (list) {
            renderList(provinceList, list, pickProvince, provinceInput.value);
        });
        resetDistrictAndWard();
    });

    function pickProvince(item) {
        provinceInput.value = item.name;
        provinceCode.value  = item.code;
        closeList(provinceList);
        resetDistrictAndWard();
        setDisabled(districtInput, false, 'Quận / Huyện');
    }

    /* ----- Quận / Huyện ----- */
    districtInput.addEventListener('focus', function () {
        if (districtInput.disabled || !provinceCode.value) return;
        loadDistricts(provinceCode.value).then(function (list) {
            renderList(districtList, list, pickDistrict, '');
        });
    });

    districtInput.addEventListener('input', function () {
        if (!provinceCode.value) return;
        districtCode.value = '';
        loadDistricts(provinceCode.value).then(function (list) {
            renderList(districtList, list, pickDistrict, districtInput.value);
        });
        resetWard();
    });

    function pickDistrict(item) {
        districtInput.value = item.name;
        districtCode.value  = item.code;
        closeList(districtList);
        resetWard();
        setDisabled(wardInput, false, 'Phường / Xã');
    }

    /* ----- Phường / Xã ----- */
    wardInput.addEventListener('focus', function () {
        if (wardInput.disabled || !districtCode.value) return;
        loadWards(districtCode.value).then(function (list) {
            renderList(wardList, list, pickWard, '');
        });
    });

    wardInput.addEventListener('input', function () {
        if (!districtCode.value) return;
        wardCode.value = '';
        loadWards(districtCode.value).then(function (list) {
            renderList(wardList, list, pickWard, wardInput.value);
        });
    });

    function pickWard(item) {
        wardInput.value = item.name;
        wardCode.value  = item.code;
        closeList(wardList);
    }

    document.addEventListener('click', function (e) {
        if (!e.target.closest('#provinceCombo')) closeList(provinceList);
        if (!e.target.closest('#districtCombo')) closeList(districtList);
        if (!e.target.closest('#wardCombo'))      closeList(wardList);
    });

})();

function toggleNewAddressForm(show) {
    const el = document.getElementById('newAddressForm');
    if (!el) return;
    if (show) {
        el.style.display = 'grid';
        el.style.opacity = '0';
        el.style.transform = 'translateY(-8px)';
        el.style.transition = 'opacity .3s, transform .3s';
        setTimeout(() => { el.style.opacity = '1'; el.style.transform = 'none'; }, 20);
    } else {
        el.style.display = 'none';
    }
}

function selectPay(method) {
    document.getElementById('optCod').classList.toggle('active', method === 'cod');
    document.getElementById('optMomo').classList.toggle('active', method === 'momo');
    document.getElementById('optVnpay')?.classList.toggle('active', method === 'vnpay');
}

document.getElementById('optCod')?.addEventListener('click', () => {
    const r = document.querySelector('input[value=cod]');
    if (r) { r.checked = true; selectPay('cod'); }
});
document.getElementById('optMomo')?.addEventListener('click', () => {
    const r = document.querySelector('input[value=momo]');
    if (r) { r.checked = true; selectPay('momo'); }
});
document.getElementById('optVnpay')?.addEventListener('click', () => {
    const r = document.querySelector('input[value=vnpay]');
    if (r) { r.checked = true; selectPay('vnpay'); }
});

/* Form submission spinner */
document.getElementById('checkoutForm')?.addEventListener('submit', function () {
    const btn = document.getElementById('btnPlaceOrder');
    if (btn) {
        btn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right:6px"></i>Đang xử lý đơn hàng...';
        btn.disabled = true;
    }
});

/* ============================================================
   VOUCHER AJAX - Áp mã giảm giá ngay lập tức
   ============================================================ */
(function () {
    const input      = document.getElementById('voucherInput');
    const btn        = document.getElementById('voucherBtn');
    const btnIcon    = document.getElementById('voucherBtnIcon');
    const btnText    = document.getElementById('voucherBtnText');
    const feedback   = document.getElementById('voucherFeedback');
    const discRow    = document.getElementById('discountRow');
    const discPct    = document.getElementById('discountPct');
    const discVal    = document.getElementById('summaryDiscount');
    const totalEl    = document.getElementById('summaryTotal');
    const CSRF       = document.querySelector('meta[name="csrf-token"]').content;
    const API_URL    = '{{ route("checkout.check-voucher") }}';

    let currentCode  = null; // mã đang áp dụng thành công

    function showFeedback(msg, type) {
        feedback.innerHTML = (type === 'success' ? '✔️ ' : '⚠️ ') + msg;
        feedback.className = 'voucher-feedback ' + type;
        feedback.style.display = 'block';
    }

    function updateSummary(data) {
        discPct.textContent = data.discount_percent;
        discVal.textContent = data.discount_fmt;
        totalEl.textContent = data.total_fmt;
        discRow.style.display = 'flex';
    }

    function resetDiscount() {
        discRow.style.display = 'none';
        // Khôi phục total về subtotal gốc
        const sub = document.getElementById('summarySubtotal').textContent;
        totalEl.textContent = sub;
        currentCode = null;
        input.classList.remove('is-valid', 'is-invalid');
    }

    function applyVoucher() {
        const code = input.value.trim().toUpperCase();
        if (!code) {
            showFeedback('Vui lòng nhập mã giảm giá.', 'error');
            input.focus();
            return;
        }

        // Trạng thái loading
        btn.disabled = true;
        btnIcon.className = 'fas fa-spinner fa-spin';
        btnText.textContent = 'Đang kiểm tra...';
        feedback.style.display = 'none';

        fetch(API_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept':        'application/json',
                'X-CSRF-TOKEN':  CSRF,
            },
            body: JSON.stringify({ voucher_code: code }),
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;

            if (data.valid) {
                // Thành công
                currentCode = code;
                input.classList.add('is-valid');
                input.classList.remove('is-invalid');
                btnIcon.className = 'fas fa-check';
                btnText.textContent = 'Đã áp';
                showFeedback(data.message, 'success');
                updateSummary(data);
            } else {
                // Thất bại
                input.classList.add('is-invalid');
                input.classList.remove('is-valid');
                btnIcon.className = 'fas fa-tag';
                btnText.textContent = 'Áp dụng';
                showFeedback(data.message, 'error');
                resetDiscount();
            }
        })
        .catch(() => {
            btn.disabled = false;
            btnIcon.className = 'fas fa-tag';
            btnText.textContent = 'Áp dụng';
            showFeedback('Không thể kết nối, vui lòng thử lại.', 'error');
        });
    }

    // Click nút áp dụng
    btn.addEventListener('click', applyVoucher);

    // Nhấn Enter trong ô input
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); applyVoucher(); }
    });

    // Xóa mã thì reset lại
    input.addEventListener('input', function () {
        if (!this.value.trim()) {
            feedback.style.display = 'none';
            resetDiscount();
            btnIcon.className = 'fas fa-tag';
            btnText.textContent = 'Áp dụng';
        }
        // Tự in hoa
        this.value = this.value.toUpperCase();
    });
})();

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

    /* ---- Ripple on place order button ---- */
    const placeBtn = document.getElementById('btnPlaceOrder');
    if (placeBtn) {
        placeBtn.addEventListener('click', function (e) {
            const r    = placeBtn.getBoundingClientRect();
            const size = Math.max(r.width, r.height) * 1.8;
            const rip  = document.createElement('span');
            rip.className = 'ripple-wave';
            rip.style.cssText = [`width:${size}px`,`height:${size}px`,
                `left:${e.clientX-r.left-size/2}px`,
                `top:${e.clientY-r.top-size/2}px`].join(';');
            placeBtn.appendChild(rip);
            rip.addEventListener('animationend', () => rip.remove());
        });
    }

})();
</script>
@endpush