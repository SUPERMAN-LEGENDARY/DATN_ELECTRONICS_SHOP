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
    .pay-option#optMomo i { color: #a50064; } /* Brand MoMo Magenta */

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

    /* Voucher Row */
    .voucher-row {
        display: flex;
        gap: 10px;
        margin: 28px 0 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--samsung-gray-light);
    }

    .voucher-row input {
        flex: 1;
        padding: 13px 16px;
        border: 1px solid #cccccc;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        background: #ffffff;
    }
    .voucher-row input:focus { border-color: var(--samsung-gray-dark); }

    .voucher-row button {
        padding: 0 20px;
        border: none;
        background: var(--samsung-gray-dark);
        color: #ffffff;
        border-radius: 8px;
        font-weight: 700;
        cursor: pointer;
        font-size: 14px;
        transition: background 0.2s ease, transform 0.15s ease;
    }
    .voucher-row button:hover {
        background: var(--samsung-gray-dark);
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
                        <span style="font-weight:700;color:#16a34a">
                            <i class="fas fa-check-circle" style="margin-right:4px"></i>Miễn phí
                        </span>
                    </div>
                    <div class="summary-total">
                        <span>Tổng cộng</span>
                        <span>{{ number_format($subtotal) }}đ</span>
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