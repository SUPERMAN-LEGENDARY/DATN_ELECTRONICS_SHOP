@extends('layouts.app')
@section('title', 'Thanh toán - ElectronicShop')

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
.stagger-children.revealed > *:nth-child(n+5){ opacity:1; transform:none; transition-delay:.24s; }

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
.checkout-page {
    min-height: 100vh;
    padding: 28px 0 60px;
    position: relative; z-index: 1;
}

.checkout-wrap {
    max-width: 1200px; margin: 0 auto; padding: 0 16px;
    position: relative; z-index: 1;
}

.checkout-title {
    font-size: 26px; font-weight: 800; margin-bottom: 24px;
    color: #0c4a6e; letter-spacing: .5px;
    display: flex; align-items: center; gap: 10px;
}
.checkout-title i {
    width: 44px; height: 44px; border-radius: 12px;
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    color: #fff; font-size: 18px;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 14px rgba(14,165,233,.35);
}

/* ============================================================
   ALERTS
   ============================================================ */
.alert-error {
    background: rgba(254,226,226,.92);
    backdrop-filter: blur(8px);
    color: #991b1b;
    border: 1px solid rgba(252,165,165,.6);
    padding: 14px 18px; border-radius: 12px;
    margin-bottom: 22px; font-size: 14px;
    box-shadow: 0 4px 16px rgba(225,29,72,.1);
    animation: alertIn .4s cubic-bezier(.16,1,.3,1);
}
@keyframes alertIn { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:none; } }
.alert-error div { margin-bottom: 4px; display: flex; align-items: center; gap: 8px; }
.alert-error div:last-child { margin-bottom: 0; }

/* ============================================================
   STEP INDICATOR — glassmorphism
   ============================================================ */
.checkout-step {
    display: flex; justify-content: center; align-items: center;
    margin-bottom: 32px;
    background: rgba(255,255,255,.65);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(186,230,253,.6);
    border-radius: 16px; padding: 16px 24px;
    box-shadow: 0 4px 20px rgba(14,165,233,.08);
}
.step {
    display: flex; flex-direction: column; align-items: center;
    color: #7dd3fc; font-size: 13px; font-weight: 600; gap: 6px;
    transition: color .2s;
}
.step span {
    width: 38px; height: 38px; border-radius: 50%;
    background: rgba(186,230,253,.5);
    color: #0369a1;
    display: flex; justify-content: center; align-items: center;
    font-weight: 700; font-size: 14px;
    transition: all .3s cubic-bezier(.34,1.56,.64,1);
}
.step.active { color: #0c4a6e; }
.step.active span {
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    color: #fff;
    box-shadow: 0 4px 14px rgba(14,165,233,.38);
    transform: scale(1.08);
}
.step.done span {
    background: linear-gradient(135deg, #16a34a, #4ade80);
    color: #fff;
}
.step-line {
    width: 100px; height: 3px;
    background: rgba(186,230,253,.6);
    margin: 0 14px; border-radius: 2px;
}
@media (max-width: 640px) { .step-line { width: 36px; } .step p { display: none; } }

/* ============================================================
   LAYOUT GRID
   ============================================================ */
.checkout-layout {
    display: grid; grid-template-columns: 1fr 380px;
    gap: 24px; align-items: start;
}
@media (max-width: 991px) { .checkout-layout { grid-template-columns: 1fr; } .checkout-right { order: -1; } }

/* ============================================================
   CHECKOUT BOX — glassmorphism card
   ============================================================ */
.checkout-box {
    background: rgba(255,255,255,.82);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(186,230,253,.65);
    border-radius: 18px; padding: 24px;
    margin-bottom: 20px;
    box-shadow: 0 6px 28px rgba(14,165,233,.1);
    transition: box-shadow .25s;
}
.checkout-box:hover { box-shadow: 0 8px 32px rgba(14,165,233,.16); }

.checkout-box h2 {
    font-size: 16px; font-weight: 800;
    color: #0c4a6e; margin-bottom: 20px;
    display: flex; align-items: center; gap: 10px;
    padding-bottom: 12px;
    border-bottom: 1px solid rgba(186,230,253,.55);
}
.checkout-box h2 .num {
    width: 28px; height: 28px; border-radius: 50%;
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    color: #fff; font-size: 13px; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(14,165,233,.3);
}

/* ============================================================
   ADDRESS SELECTION
   ============================================================ */
.addr-option {
    border: 1.5px solid rgba(186,230,253,.7);
    border-radius: 14px; padding: 14px 16px;
    margin-bottom: 10px; cursor: pointer;
    display: flex; gap: 12px; align-items: flex-start;
    transition: all .22s cubic-bezier(.16,1,.3,1);
    background: rgba(255,255,255,.72);
    backdrop-filter: blur(4px);
}
.addr-option:hover {
    border-color: #0ea5e9;
    background: rgba(186,230,253,.35);
    transform: translateY(-2px);
    box-shadow: 0 4px 14px rgba(14,165,233,.12);
}
.addr-option input[type=radio] {
    width: 18px; height: 18px; margin-top: 3px;
    accent-color: #0ea5e9; flex-shrink: 0;
}
.addr-option .name { font-weight: 700; font-size: 14.5px; color: #0c4a6e; }
.addr-option .detail { font-size: 13px; color: #0369a1; opacity: .85; margin-top: 3px; }
.badge-default {
    background: rgba(186,230,253,.55); color: #0369a1;
    font-size: 11px; padding: 2px 9px; border-radius: 10px;
    margin-left: 8px; font-weight: 700; border: 1px solid rgba(125,211,252,.5);
}

/* ============================================================
   FORM FIELDS
   ============================================================ */
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.form-grid .full { grid-column: 1 / -1; }
@media (max-width: 640px) { .form-grid { grid-template-columns: 1fr; } }

.form-group label {
    display: block; font-size: 13px; font-weight: 600;
    margin-bottom: 6px; color: #0369a1;
}
.form-group input,
.form-group textarea {
    width: 100%; padding: 11px 14px;
    border: 1px solid rgba(125,211,252,.55);
    border-radius: 10px; font-size: 14px;
    outline: none; box-sizing: border-box;
    background: rgba(255,255,255,.75);
    color: #0c4a6e;
    transition: border-color .2s, box-shadow .2s, background .2s;
    font-family: inherit;
}
.form-group input::placeholder,
.form-group textarea::placeholder { color: #7dd3fc; }

.form-group input:focus,
.form-group textarea:focus {
    border-color: #0ea5e9;
    box-shadow: 0 0 0 3px rgba(14,165,233,.15);
    background: rgba(255,255,255,.92);
}

/* ============================================================
   PAYMENT OPTIONS
   ============================================================ */
.pay-option {
    border: 1.5px solid rgba(186,230,253,.7);
    border-radius: 14px; padding: 16px;
    margin-bottom: 12px; cursor: pointer;
    display: flex; align-items: center; gap: 14px;
    transition: all .22s cubic-bezier(.16,1,.3,1);
    background: rgba(255,255,255,.72);
    backdrop-filter: blur(4px);
}
.pay-option:hover {
    border-color: #0ea5e9;
    background: rgba(186,230,253,.35);
    transform: translateY(-2px);
}
.pay-option.active {
    border-color: #0ea5e9;
    background: rgba(186,230,253,.5);
    box-shadow: 0 0 0 2px rgba(14,165,233,.2) inset, 0 4px 16px rgba(14,165,233,.12);
}
.pay-option i {
    width: 32px; font-size: 24px; text-align: center;
    color: #0ea5e9; flex-shrink: 0;
}
.pay-option .title { font-weight: 700; font-size: 14.5px; color: #0c4a6e; }
.pay-option .desc { font-size: 12.5px; color: #0369a1; opacity: .8; }

/* ============================================================
   RIGHT ORDER SUMMARY — glassmorphism
   ============================================================ */
.order-box {
    background: rgba(255,255,255,.84);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(186,230,253,.65);
    border-radius: 18px; padding: 24px;
    box-shadow: 0 6px 28px rgba(14,165,233,.12);
    position: sticky; top: 90px;
}
.order-box h2 {
    font-size: 16px; font-weight: 800;
    color: #0c4a6e; margin-bottom: 18px;
    padding-bottom: 12px;
    border-bottom: 1px solid rgba(186,230,253,.55);
}

.order-product {
    display: flex; gap: 12px; margin-bottom: 14px;
    padding-bottom: 12px;
    border-bottom: 1px solid rgba(186,230,253,.35);
}
.order-product:last-of-type { border-bottom: none; }

.order-product img,
.order-product .no-img {
    width: 58px; height: 58px;
    object-fit: contain;
    background: linear-gradient(160deg, #f0f9ff, #e0f2fe);
    padding: 3px; box-sizing: border-box;
    border-radius: 10px;
    border: 1px solid rgba(186,230,253,.6);
    flex-shrink: 0;
}
.order-product .no-img {
    display: flex; align-items: center; justify-content: center;
    color: #7dd3fc; font-size: 18px;
}
.order-product .name { font-size: 13.5px; font-weight: 700; margin: 0 0 3px; line-height: 1.3; color: #0c4a6e; }
.order-product .meta { font-size: 12.5px; color: #0369a1; opacity: .85; }
.order-product .variant-tag {
    font-size: 11.5px; color: #0ea5e9; font-weight: 600;
    background: rgba(186,230,253,.4); border-radius: 4px;
    padding: 1px 6px; display: inline-block; margin-bottom: 2px;
}

/* Voucher */
.voucher-row { display: flex; gap: 8px; margin: 16px 0; }
.voucher-row input {
    flex: 1; padding: 10px 12px;
    border: 1px solid rgba(125,211,252,.55);
    border-radius: 8px; font-size: 13.5px; outline: none;
    background: rgba(255,255,255,.75); color: #0c4a6e;
    transition: border-color .2s, box-shadow .2s;
}
.voucher-row input::placeholder { color: #7dd3fc; }
.voucher-row input:focus {
    border-color: #0ea5e9;
    box-shadow: 0 0 0 3px rgba(14,165,233,.15);
}
.voucher-row button {
    padding: 0 16px; border: none;
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    color: #fff; border-radius: 8px; font-weight: 700;
    cursor: pointer; font-size: 13.5px;
    transition: opacity .2s, transform .15s;
    box-shadow: 0 2px 10px rgba(14,165,233,.3);
}
.voucher-row button:hover { opacity: .9; transform: translateY(-1px); }

.summary-row {
    display: flex; justify-content: space-between;
    font-size: 14px; margin: 10px 0; color: #0369a1;
}
.summary-total {
    font-size: 18px; font-weight: 800; color: #0c4a6e;
    border-top: 2px solid rgba(186,230,253,.55);
    padding-top: 14px; margin-top: 8px;
    display: flex; justify-content: space-between;
}
.summary-total span:last-child { color: #0369a1; }

.btn-place-order {
    display: block; width: 100%; margin-top: 18px; padding: 14px;
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    color: #fff; border: none; border-radius: 12px;
    font-size: 15.5px; font-weight: 700; cursor: pointer;
    transition: opacity .2s, transform .18s, box-shadow .2s;
    box-shadow: 0 4px 18px rgba(14,165,233,.38);
    position: relative; overflow: hidden;
}
.btn-place-order::after {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,.28) 50%, transparent 60%);
    transform: translateX(-120%); transition: transform .5s ease; pointer-events: none;
}
.btn-place-order:hover::after { transform: translateX(120%); }
.btn-place-order:hover {
    opacity: .92; transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(14,165,233,.48);
}

.small-box {
    text-align: center; font-weight: 700; color: #0c4a6e; font-size: 13px;
    background: rgba(255,255,255,.75); backdrop-filter: blur(10px);
    border: 1px solid rgba(186,230,253,.6);
    border-radius: 14px; padding: 14px; margin-top: 14px;
    box-shadow: 0 4px 16px rgba(14,165,233,.08);
    display: flex; align-items: center; justify-content: center; gap: 8px;
}
</style>
@endpush

@section('content')
{{-- Sky Canvas --}}
<canvas id="sky-canvas" aria-hidden="true"></canvas>

<div class="checkout-page">
<div class="checkout-wrap">
    <h1 class="checkout-title reveal">
        <i class="fas fa-credit-card"></i>
        Thanh toán
    </h1>

    @if(session('error'))
    <div class="alert-error reveal">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
    @endif
    @if($errors->any())
    <div class="alert-error reveal">
        @foreach($errors->all() as $e)
        <div><i class="fas fa-exclamation-circle"></i> {{ $e }}</div>
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
                        <div>
                            <div class="name">
                                {{ $addr->full_name }} — {{ $addr->phone }}
                                @if($addr->is_default)<span class="badge-default">Mặc định</span>@endif
                            </div>
                            <div class="detail">{{ $addr->full_address }}</div>
                        </div>
                    </label>
                    @endforeach
                    <label class="addr-option" style="margin-top:4px">
                        <input type="radio" name="address_id" value="" onchange="toggleNewAddressForm(true)">
                        <div class="name"><i class="fas fa-plus-circle" style="margin-right:6px;color:#0ea5e9"></i> Giao tới địa chỉ khác</div>
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
                        <i class="fas fa-wallet" style="color:#d946ef"></i>
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
                        <textarea name="note" rows="3" placeholder="Ví dụ: giao giờ hành chính, gọi trước khi giao...">{{ old('note') }}</textarea>
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
                        <button type="button">Áp dụng</button>
                    </div>

                    <div class="summary-row">
                        <span>Tạm tính</span>
                        <strong>{{ number_format($subtotal) }}đ</strong>
                    </div>
                    <div class="summary-row">
                        <span>Phí vận chuyển</span>
                        <span style="color:#16a34a;font-weight:700">
                            <i class="fas fa-check-circle" style="margin-right:4px"></i>Miễn phí
                        </span>
                    </div>
                    <div class="summary-total">
                        <span>Tổng cộng</span>
                        <span>{{ number_format($subtotal) }}đ</span>
                    </div>

                    <button type="submit" class="btn-place-order" id="btnPlaceOrder">
                        <i class="fas fa-shield-alt" style="margin-right:6px"></i>Đặt hàng ngay
                    </button>
                </div>

                <!-- <div class="small-box">
                    <i class="fas fa-lock" style="color:#0ea5e9"></i>
                    Thanh toán an toàn &amp; bảo mật 100%
                </div> -->
            </div>

        </div>
    </form>
</div>
</div>
@endsection

@push('scripts')
<script>
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
}

document.getElementById('optCod')?.addEventListener('click', () => {
    const r = document.querySelector('input[value=cod]');
    if (r) { r.checked = true; selectPay('cod'); }
});
document.getElementById('optMomo')?.addEventListener('click', () => {
    const r = document.querySelector('input[value=momo]');
    if (r) { r.checked = true; selectPay('momo'); }
});

/* Form submission spinner */
document.getElementById('checkoutForm')?.addEventListener('submit', function () {
    const btn = document.getElementById('btnPlaceOrder');
    if (btn) {
        btn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right:6px"></i>Đang xử lý đơn hàng...';
        btn.disabled = true;
        btn.style.opacity = '.85';
    }
});

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
