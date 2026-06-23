@extends('layouts.app')
@section('title', 'Thanh toán - ElectronicShop')
@php $showSearch = true; @endphp

@push('styles')
<style>
.checkout-page { max-width: 1200px; margin: 0 auto; padding: 16px; }
.checkout-title { font-size: 28px; font-weight: 800; text-transform: uppercase; margin-bottom: 20px; }

/* STEPS */
.steps { display: flex; align-items: center; margin-bottom: 32px; }
.step { display: flex; flex-direction: column; align-items: center; gap: 6px; position: relative; flex: 1; }
.step-num {
    width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 14px; border: 2px solid #e0e0e0; background: #fff; color: #aaa; z-index: 1;
}
.step.active .step-num { background: #1565C0; color: #fff; border-color: #1565C0; }
.step.done .step-num { background: #e8f0fe; color: #1565C0; border-color: #1565C0; }
.step-label { font-size: 12px; color: #aaa; font-weight: 500; }
.step.active .step-label { color: #1565C0; font-weight: 600; }
.step::after {
    content: ''; position: absolute; top: 18px; left: calc(50% + 18px);
    width: calc(100% - 36px); height: 1px; background: #e0e0e0;
}
.step:last-child::after { display: none; }
.step.active::after, .step.done::after { background: #1565C0; }

/* LAYOUT */
.checkout-layout { display: grid; grid-template-columns: 1fr 320px; gap: 24px; align-items: start; }

/* FORMS */
.checkout-section { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 24px; margin-bottom: 16px; }
.section-heading { font-size: 15px; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
.section-heading i { color: #1565C0; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
.form-row.three { grid-template-columns: 1fr 1fr 1fr; }
.form-row.one { grid-template-columns: 1fr; }
.form-group label { display: block; font-size: 12px; font-weight: 600; color: #555; margin-bottom: 5px; text-transform: uppercase; letter-spacing: .3px; }
.form-group input, .form-group select {
    width: 100%; padding: 10px 12px; border: 1px solid #e0e0e0; border-radius: 6px;
    font-size: 14px; outline: none; transition: border-color .15s; font-family: inherit; color: #333;
}
.form-group input:focus, .form-group select:focus { border-color: #1565C0; }
.required { color: #E53935; }

/* SHIPPING OPTIONS */
.shipping-option {
    border: 1.5px solid #e0e0e0; border-radius: 8px; padding: 14px 16px;
    margin-bottom: 10px; cursor: pointer; display: flex; align-items: center; gap: 14px;
    transition: border-color .15s;
}
.shipping-option.selected { border-color: #1565C0; background: #EBF3FF; }
.shipping-option input[type=radio] { accent-color: #1565C0; width: 16px; height: 16px; }
.shipping-option .so-info { flex: 1; }
.shipping-option .so-name { font-size: 14px; font-weight: 600; }
.shipping-option .so-desc { font-size: 12px; color: #888; }
.shipping-option .so-price { font-size: 14px; font-weight: 700; color: #2E7D32; }

/* PAYMENT OPTIONS */
.payment-option {
    border: 1.5px solid #e0e0e0; border-radius: 8px; padding: 14px 16px;
    margin-bottom: 10px; cursor: pointer; display: flex; align-items: center; gap: 14px;
    transition: border-color .15s;
}
.payment-option.selected { border-color: #1565C0; background: #EBF3FF; }
.payment-option input[type=radio] { accent-color: #1565C0; width: 16px; height: 16px; }
.payment-option .po-info { flex: 1; }
.payment-option .po-name { font-size: 14px; font-weight: 600; }
.payment-option .po-desc { font-size: 12px; color: #888; }
.payment-option .po-icon { font-size: 20px; color: #888; }

/* ORDER SUMMARY SIDEBAR */
.order-sidebar { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; position: sticky; top: 80px; }
.order-sidebar h3 { font-size: 14px; font-weight: 700; margin-bottom: 14px; }
.order-item { display: flex; gap: 10px; align-items: flex-start; margin-bottom: 12px; }
.order-item-img { width: 44px; height: 44px; background: #f0f0f0; border-radius: 6px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; color: #ccc; font-size: 12px; }
.order-item-info .name { font-size: 13px; font-weight: 500; line-height: 1.4; }
.order-item-info .variant { font-size: 12px; color: #888; }
.order-item-info .price { font-size: 13px; font-weight: 700; color: #1565C0; }
.summary-divider { border: none; border-top: 1px solid #f0f0f0; margin: 12px 0; }
.sum-row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 8px; }
.sum-row .free { color: #2E7D32; font-weight: 600; }
.sum-total { display: flex; justify-content: space-between; align-items: center; margin-top: 8px; }
.sum-total .tl { font-size: 15px; font-weight: 700; }
.sum-total .tv { font-size: 20px; font-weight: 800; color: #1565C0; }
.sum-vat { font-size: 11px; color: #aaa; text-align: right; margin-bottom: 16px; }
.btn-confirm {
    width: 100%; padding: 14px; background: #1565C0; color: #fff; border: none;
    border-radius: 8px; font-size: 15px; font-weight: 700; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 8px;
    transition: background .2s;
}
.btn-confirm:hover { background: #0D47A1; }
.back-link { display: block; text-align: center; font-size: 13px; color: #1565C0; margin-top: 8px; }
.sidebar-trust { margin-top: 14px; }
.sidebar-trust-item { display: flex; gap: 8px; align-items: flex-start; margin-bottom: 10px; font-size: 13px; }
.sidebar-trust-item i { color: #1565C0; margin-top: 2px; flex-shrink: 0; }
</style>
@endpush

@section('content')
<div class="checkout-page">
    <div class="breadcrumb">
        <a href="{{ route('home') }}">Trang chủ</a>
        <span>›</span>
        <a href="{{ route('cart.index') }}">Giỏ hàng</a>
        <span>›</span>
        <span>Thanh toán</span>
    </div>
    <h1 class="checkout-title">Thanh toán</h1>

    {{-- STEPS --}}
    <div class="steps">
        <div class="step active">
            <div class="step-num">1</div>
            <div class="step-label">Thông tin giao hàng</div>
        </div>
        <div class="step">
            <div class="step-num">2</div>
            <div class="step-label">Phương thức thanh toán</div>
        </div>
        <div class="step">
            <div class="step-num">3</div>
            <div class="step-label">Xác nhận đơn hàng</div>
        </div>
    </div>

    <form action="{{ route('checkout.process') }}" method="POST">
        @csrf
        <div class="checkout-layout">
            {{-- FORMS --}}
            <div>
                {{-- SHIPPING INFO --}}
                <div class="checkout-section">
                    <div class="section-heading"><i class="fas fa-truck"></i> 1. THÔNG TIN GIAO HÀNG</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Họ và tên <span class="required">*</span></label>
                            <input type="text" name="name" placeholder="Nguyễn Văn A" value="{{ old('name', auth()->user()->name ?? '') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Số điện thoại <span class="required">*</span></label>
                            <input type="tel" name="phone" placeholder="0123 456 789" value="{{ old('phone', auth()->user()->phone ?? '') }}" required>
                        </div>
                    </div>
                    <div class="form-row one">
                        <div class="form-group">
                            <label>Email (Không bắt buộc)</label>
                            <input type="email" name="email" placeholder="nguyenvana@gmail.com" value="{{ old('email', auth()->user()->email ?? '') }}">
                        </div>
                    </div>
                    <div class="form-row three">
                        <div class="form-group">
                            <label>Tỉnh / Thành phố <span class="required">*</span></label>
                            <select name="province" required>
                                <option value="">TP. Hồ Chí Minh</option>
                                <option>Hà Nội</option>
                                <option>Đà Nẵng</option>
                                <option>Cần Thơ</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Quận / Huyện <span class="required">*</span></label>
                            <select name="district" required>
                                <option value="">Quận 1</option>
                                <option>Quận 2</option>
                                <option>Quận 3</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Phường / Xã <span class="required">*</span></label>
                            <select name="ward" required>
                                <option value="">Phường Bình Thạnh</option>
                                <option>Phường Bến Nghé</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row one">
                        <div class="form-group">
                            <label>Địa chỉ cụ thể</label>
                            <input type="text" name="address" placeholder="123 Nguyễn Huệ, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh">
                        </div>
                    </div>
                </div>

                {{-- SHIPPING METHOD --}}
                <div class="checkout-section">
                    <div class="section-heading"><i class="fas fa-shipping-fast"></i> 2. PHƯƠNG THỨC GIAO HÀNG</div>
                    <label class="shipping-option selected">
                        <input type="radio" name="shipping" value="standard" checked>
                        <div class="so-info">
                            <div class="so-name">Giao hàng tiêu chuẩn</div>
                            <div class="so-desc">Nhận hàng từ 1 – 2 ngày</div>
                        </div>
                        <div class="so-price">Miễn phí</div>
                    </label>
                    <label class="shipping-option">
                        <input type="radio" name="shipping" value="express">
                        <div class="so-info">
                            <div class="so-name">Giao hàng hỏa tốc</div>
                            <div class="so-desc">Nhận hàng trong ngày (nội thành)</div>
                        </div>
                        <div class="so-price" style="color:#333">30.000đ</div>
                    </label>
                </div>

                {{-- PAYMENT --}}
                <div class="checkout-section">
                    <div class="section-heading"><i class="fas fa-credit-card"></i> 3. PHƯƠNG THỨC THANH TOÁN</div>
                    <label class="payment-option selected">
                        <input type="radio" name="payment" value="cod" checked>
                        <div class="po-info">
                            <div class="po-name">Thanh toán khi nhận hàng (COD)</div>
                            <div class="po-desc">Thanh toán bằng tiền mặt khi nhận hàng</div>
                        </div>
                        <i class="fas fa-money-bill-wave po-icon"></i>
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment" value="ewallet">
                        <div class="po-info">
                            <div class="po-name">Ví điện tử</div>
                            <div class="po-desc">Thanh toán qua ví MoMo, ZaloPay, VNPay</div>
                        </div>
                        <i class="fas fa-wallet po-icon"></i>
                    </label>
                </div>
            </div>

            {{-- ORDER SUMMARY --}}
            <div>
                <div class="order-sidebar">
                    <h3>Đơn hàng của bạn ({{ count($cartItems ?? []) ?: 3 }} sản phẩm)</h3>

                    @forelse($cartItems ?? [] as $item)
                    <div class="order-item">
                        <div class="order-item-img"><i class="fas fa-image"></i></div>
                        <div class="order-item-info">
                            <div class="name">{{ $item['name'] }}</div>
                            <div class="variant">{{ $item['variant'] }} | x{{ $item['quantity'] }}</div>
                            <div class="price">{{ number_format($item['price'] * $item['quantity']) }}đ</div>
                        </div>
                    </div>
                    @empty
                    @foreach([
                        ['iPhone 15 Pro Max 256GB','Titan Tự Nhiên','28.990.000'],
                        ['Samsung Galaxy S24...','1TB - Titanium Violet','26.490.000'],
                        ['Apple AirPods 4','Chống ồn chủ động','4.490.000'],
                    ] as $item)
                    <div class="order-item">
                        <div class="order-item-img"><i class="fas fa-image" style="font-size:10px"></i></div>
                        <div class="order-item-info">
                            <div class="name">{{ $item[0] }}</div>
                            <div class="variant">{{ $item[1] }} | x1</div>
                            <div class="price">{{ $item[2] }}đ</div>
                        </div>
                    </div>
                    @endforeach
                    @endforelse

                    <hr class="summary-divider">
                    <div class="sum-row"><span>Tạm tính (3 sản phẩm)</span><span>59.970.000đ</span></div>
                    <div class="sum-row"><span>Phí vận chuyển</span><span class="free">Miễn phí</span></div>
                    <div class="sum-row"><span>Giảm giá</span><span>- 0đ</span></div>
                    <hr class="summary-divider">
                    <div class="sum-total">
                        <span class="tl">Tổng cộng</span>
                        <span class="tv">59.970.000đ</span>
                    </div>
                    <div class="sum-vat">(ĐÃ BAO GỒM VAT)</div>
                    <button type="submit" class="btn-confirm">
                        <i class="fas fa-shopping-bag"></i> XÁC NHẬN ĐẶT HÀNG
                    </button>
                    <a href="{{ route('cart.index') }}" class="back-link">← Quay lại giỏ hàng</a>

                    <div class="sidebar-trust">
                        <hr class="summary-divider">
                        <div class="sidebar-trust-item"><i class="fas fa-shield-alt"></i> Cam kết 100% chính hãng. Hoàn tiền 200% nếu phát hiện hàng giả</div>
                        <div class="sidebar-trust-item"><i class="fas fa-sync-alt"></i> Đổi trả dễ dàng. Đổi trả trong 30 ngày với mọi lý do</div>
                        <div class="sidebar-trust-item"><i class="fas fa-headset"></i> Hỗ trợ 24/7. Hotline: 0123 456 789</div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.shipping-option').forEach(opt => {
    opt.addEventListener('click', function() {
        document.querySelectorAll('.shipping-option').forEach(o => o.classList.remove('selected'));
        this.classList.add('selected');
    });
});
document.querySelectorAll('.payment-option').forEach(opt => {
    opt.addEventListener('click', function() {
        document.querySelectorAll('.payment-option').forEach(o => o.classList.remove('selected'));
        this.classList.add('selected');
    });
});
</script>
@endpush