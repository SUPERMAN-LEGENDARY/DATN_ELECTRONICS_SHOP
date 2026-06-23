{{-- ============================================ --}}
{{-- FILE: resources/views/pages/thank-you.blade.php --}}
{{-- ============================================ --}}
@extends('layouts.app')
@section('title', 'Đặt hàng thành công - ElectronicShop')

@push('styles')
<style>
.thankyou-page { max-width: 1200px; margin: 0 auto; padding: 32px 16px 48px; }

/* HERO BANNER */
.thankyou-hero {
    text-align: center; padding: 40px 20px 36px; background: #f8fbff;
    border-radius: 12px; margin-bottom: 32px;
    border: 1px solid #e3edf9;
}
.thankyou-icon {
    width: 72px; height: 72px; background: #1565C0; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 18px; box-shadow: 0 4px 20px rgba(21,101,192,.3);
}
.thankyou-icon i { color: #fff; font-size: 32px; }
.thankyou-title { font-size: 30px; font-weight: 800; color: #1565C0; margin-bottom: 12px; }
.thankyou-desc { font-size: 15px; color: #666; margin-bottom: 24px; line-height: 1.6; }
.thankyou-actions { display: flex; gap: 14px; justify-content: center; }
.btn-view-order {
    padding: 12px 28px; background: #1565C0; color: #fff; border: none; border-radius: 8px;
    font-size: 14px; font-weight: 700; cursor: pointer; text-decoration: none;
    display: inline-flex; align-items: center; gap: 8px; transition: background .2s;
}
.btn-view-order:hover { background: #0D47A1; }
.btn-continue {
    padding: 12px 28px; background: #fff; color: #1565C0; border: 2px solid #1565C0;
    border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; text-decoration: none;
    display: inline-flex; align-items: center; gap: 8px; transition: all .2s;
}
.btn-continue:hover { background: #EBF3FF; }

/* LAYOUT */
.thankyou-layout { display: grid; grid-template-columns: 1fr 360px; gap: 24px; margin-bottom: 36px; }

/* ORDER STATUS */
.status-card { background: #fff; border: 1px solid #e0e0e0; border-radius: 10px; padding: 24px; }
.status-card-title { font-size: 15px; font-weight: 700; color: #1565C0; margin-bottom: 20px; }
.order-steps { display: flex; align-items: flex-start; gap: 0; margin-bottom: 24px; }
.order-step { flex: 1; display: flex; flex-direction: column; align-items: center; position: relative; }
.order-step::after {
    content: ''; position: absolute; top: 20px; left: 50%; width: 100%;
    height: 2px; background: #e0e0e0; z-index: 0;
}
.order-step:last-child::after { display: none; }
.order-step.done::after { background: #1565C0; }
.step-circle {
    width: 40px; height: 40px; border-radius: 50%; border: 2px solid #e0e0e0;
    background: #fff; display: flex; align-items: center; justify-content: center;
    font-size: 16px; color: #bbb; z-index: 1; position: relative; transition: all .3s;
}
.order-step.done .step-circle { background: #1565C0; border-color: #1565C0; color: #fff; }
.order-step.active .step-circle { border-color: #1565C0; color: #1565C0; }
.step-name { font-size: 11px; font-weight: 700; text-align: center; margin-top: 8px; color: #888; }
.order-step.done .step-name { color: #1565C0; }
.step-date { font-size: 10px; color: #aaa; text-align: center; margin-top: 3px; }

/* RECEIVER + SHIPPING INFO */
.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0; }
.info-block { padding: 16px 0; }
.info-block + .info-block { border-left: 1px solid #f0f0f0; padding-left: 20px; }
.info-block-title { font-size: 13px; font-weight: 700; color: #1565C0; text-transform: uppercase; letter-spacing: .3px; margin-bottom: 12px; }
.info-row { font-size: 14px; margin-bottom: 6px; color: #444; }
.info-row strong { color: #1a1a1a; display: block; font-size: 15px; font-weight: 700; margin-bottom: 2px; }
.info-row .order-code { color: #1565C0; font-weight: 700; }

/* ORDER DETAIL SIDEBAR */
.order-detail-card { background: #fff; border: 1px solid #e0e0e0; border-radius: 10px; padding: 22px; }
.order-detail-title { font-size: 15px; font-weight: 700; color: #1565C0; margin-bottom: 16px; }
.order-product { display: flex; gap: 12px; margin-bottom: 14px; padding-bottom: 14px; border-bottom: 1px solid #f5f5f5; }
.order-product:last-of-type { }
.order-product-img {
    width: 60px; height: 60px; background: #f5f5f5; border-radius: 6px;
    flex-shrink: 0; display: flex; align-items: center; justify-content: center; color: #ccc;
}
.order-product-img img { width: 100%; height: 100%; object-fit: cover; border-radius: 6px; }
.order-product-name { font-size: 13px; font-weight: 600; line-height: 1.4; margin-bottom: 2px; }
.order-product-variant { font-size: 12px; color: #888; }
.order-product-price { font-size: 14px; font-weight: 700; color: #1565C0; }
.sum-row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px; color: #555; }
.sum-total-row { display: flex; justify-content: space-between; align-items: center; margin-top: 10px; padding-top: 10px; border-top: 1px solid #e0e0e0; }
.sum-total-row .tl { font-size: 15px; font-weight: 700; }
.sum-total-row .tv { font-size: 22px; font-weight: 800; color: #1565C0; }

/* SUGGESTED PRODUCTS */
.suggested-section { margin-top: 8px; }
.suggested-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
.suggested-title { font-size: 16px; font-weight: 800; color: #1565C0; }
.suggested-link { font-size: 13px; color: #1565C0; font-weight: 500; }
.suggested-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 14px; }
.suggested-card {
    background: #fff; border: 1px solid #e0e0e0; border-radius: 8px;
    overflow: hidden; transition: box-shadow .2s, transform .2s; position: relative;
}
.suggested-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,.1); transform: translateY(-2px); }
.sg-badge {
    position: absolute; top: 8px; left: 8px; font-size: 10px; font-weight: 700;
    padding: 2px 7px; border-radius: 4px; text-transform: uppercase; z-index:1;
}
.sg-badge.new { background: #1565C0; color: #fff; }
.sg-badge.best { background: #FF6F00; color: #fff; }
.sg-badge.hot { background: #E53935; color: #fff; }
.sg-badge.exclusive { background: #6A1B9A; color: #fff; }
.sg-img { height: 140px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #ccc; }
.sg-img img { width: 100%; height: 100%; object-fit: cover; }
.sg-body { padding: 10px 12px; }
.sg-name { font-size: 13px; font-weight: 500; line-height: 1.4; margin-bottom: 5px; }
.sg-price { color: #E53935; font-weight: 700; font-size: 14px; margin-bottom: 4px; }
.sg-stars { color: #FFA000; font-size: 11px; }
</style>
@endpush

@section('content')
<div class="thankyou-page">
    {{-- HERO --}}
    <div class="thankyou-hero">
        <div class="thankyou-icon"><i class="fas fa-check"></i></div>
        <h1 class="thankyou-title">Cảm ơn bạn đã đặt hàng!</h1>
        <p class="thankyou-desc">
            Đơn hàng của bạn đã được tiếp nhận và đang được xử lý.<br>
            Chúng tôi sẽ gửi thông tin xác nhận đến email của bạn trong ít phút nữa.
        </p>
        <div class="thankyou-actions">
            <a href="{{ route('account.orders') ?? '#' }}" class="btn-view-order">
                <i class="fas fa-clipboard-list"></i> Xem chi tiết đơn hàng
            </a>
            <a href="{{ route('products.index') }}" class="btn-continue">
                <i class="fas fa-shopping-bag"></i> Tiếp tục mua sắm
            </a>
        </div>
    </div>

    {{-- ORDER STATUS + DETAIL --}}
    <div class="thankyou-layout">
        {{-- LEFT: STATUS TRACKER + INFO --}}
        <div class="status-card">
            <div class="status-card-title">Trạng thái đơn hàng</div>
            <div class="order-steps">
                <div class="order-step done">
                    <div class="step-circle"><i class="fas fa-clipboard-check"></i></div>
                    <div class="step-name">Đặt hàng thành công</div>
                    <div class="step-date">{{ optional($order->created_at ?? now())->format('d/m/Y') }} – {{ optional($order->created_at ?? now())->format('H:i') }}</div>
                </div>
                <div class="order-step">
                    <div class="step-circle"><i class="fas fa-cog"></i></div>
                    <div class="step-name">Đang xử lý</div>
                    <div class="step-date">Dự kiến: {{ now()->format('d/m/Y') }}</div>
                </div>
                <div class="order-step">
                    <div class="step-circle"><i class="fas fa-truck"></i></div>
                    <div class="step-name">Đang giao hàng</div>
                    <div class="step-date">Dự kiến: {{ now()->addDays(1)->format('d/m/Y') }}</div>
                </div>
                <div class="order-step">
                    <div class="step-circle"><i class="fas fa-check-circle"></i></div>
                    <div class="step-name">Đã giao hàng</div>
                    <div class="step-date">Dự kiến: {{ now()->addDays(2)->format('d/m/Y') }}</div>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-block">
                    <div class="info-block-title">Thông tin người nhận</div>
                    <div class="info-row">
                        <strong>{{ $order->name ?? 'Nguyễn Văn A' }}</strong>
                        {{ $order->phone ?? '0123 456 789' }}
                    </div>
                    <div class="info-row" style="font-size:13px;color:#555">
                        {{ $order->full_address ?? '123 Đường Công Nghệ, P. Tân Thuận, Quận 7, TP. Hồ Chí Minh' }}
                    </div>
                </div>
                <div class="info-block">
                    <div class="info-block-title">Chi tiết giao hàng</div>
                    <div class="info-row">Mã đơn hàng: <span class="order-code">{{ $order->code ?? 'DH2024052012345' }}</span></div>
                    <div class="info-row">Hình thức: {{ $order->shipping_method_label ?? 'Giao hàng tiêu chuẩn' }}</div>
                    <div class="info-row">Thanh toán: {{ $order->payment_method_label ?? 'Tiền mặt khi nhận hàng (COD)' }}</div>
                </div>
            </div>
        </div>

        {{-- RIGHT: ORDER DETAIL --}}
        <div class="order-detail-card">
            <div class="order-detail-title">Chi tiết đơn hàng</div>

            @forelse($order->items ?? [] as $item)
            <div class="order-product">
                <div class="order-product-img">
                    @if($item->image)<img src="{{ $item->image }}" alt="">@else<i class="fas fa-image"></i>@endif
                </div>
                <div style="flex:1">
                    <div class="order-product-name">{{ $item->name }}</div>
                    <div class="order-product-variant">{{ $item->variant }} | SL: x{{ $item->quantity }}</div>
                </div>
                <div class="order-product-price">{{ number_format($item->price * $item->quantity) }}đ</div>
            </div>
            @empty
            {{-- Demo --}}
            <div class="order-product" style="border-bottom:1px solid #f5f5f5;padding-bottom:14px;margin-bottom:14px">
                <div class="order-product-img"><i class="fas fa-image"></i></div>
                <div style="flex:1">
                    <div class="order-product-name">iPhone 15 Pro Max 256GB Titan Tự Nhiên</div>
                    <div class="order-product-variant">Titan Tự Nhiên | SL: x1</div>
                </div>
                <div class="order-product-price">28.990.000đ</div>
            </div>
            @endforelse

            <div class="sum-row"><span>Tạm tính:</span><span>{{ number_format($order->subtotal ?? 28990000) }}đ</span></div>
            <div class="sum-row"><span>Phí vận chuyển:</span><span>{{ $order->shipping_fee ?? 0 > 0 ? number_format($order->shipping_fee).'đ' : '0đ' }}</span></div>
            <div class="sum-row" style="color:#E53935"><span>Giảm giá:</span><span>-{{ number_format($order->discount ?? 0) }}đ</span></div>
            <div class="sum-total-row">
                <span class="tl">Tổng cộng:</span>
                <span class="tv">{{ number_format($order->total ?? 28990000) }}đ</span>
            </div>
        </div>
    </div>

    {{-- SUGGESTED PRODUCTS --}}
    <div class="suggested-section">
        <div class="suggested-header">
            <span class="suggested-title">Sản phẩm gợi ý cho bạn</span>
            <a href="{{ route('products.index') }}" class="suggested-link">XEM TẤT CẢ →</a>
        </div>
        <div class="suggested-grid">
            @forelse($suggestedProducts ?? [] as $p)
            <a href="{{ route('products.show', $p->slug) }}" class="suggested-card">
                <div class="sg-img">
                    @if($p->image)<img src="{{ $p->image }}" alt="">@else<i class="fas fa-image fa-2x"></i>@endif
                </div>
                <div class="sg-body">
                    <div class="sg-name">{{ $p->name }}</div>
                    <div class="sg-price">{{ number_format($p->price) }}đ</div>
                    <div class="sg-stars">★★★★★ <span style="color:#aaa;font-size:11px">({{ $p->reviews_count }})</span></div>
                </div>
            </a>
            @empty
            @foreach([
                ['Samsung Galaxy S24','24.990.000','NEW','95'],
                ['Xiaomi 14 5G 256GB','16.990.000','BEST SELLER','76'],
                ['OPPO Reno11 5G','9.990.000','HOT','47'],
                ['vivo V30 5G','10.990.000','NEW','58'],
                ['Google Pixel 8 Pro','21.990.000','EXCLUSIVE','124'],
            ] as $p)
            <a href="#" class="suggested-card">
                <span class="sg-badge {{ strtolower(str_replace(' ','',trim($p[2]))) === 'bestseller' ? 'best' : (strtolower($p[2])==='hot'?'hot':(strtolower($p[2])==='exclusive'?'exclusive':'new')) }}">{{ $p[2] }}</span>
                <div class="sg-img img-placeholder"><i class="fas fa-image fa-2x"></i></div>
                <div class="sg-body">
                    <div class="sg-name">{{ $p[0] }}</div>
                    <div class="sg-price">{{ $p[1] }}đ</div>
                    <div class="sg-stars">★★★★★ <span style="color:#aaa;font-size:11px">({{ $p[3] }})</span></div>
                </div>
            </a>
            @endforeach
            @endforelse
        </div>
    </div>
</div>
@endsection
