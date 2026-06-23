@extends('layouts.app')
@section('title', ($product->name ?? 'iPhone 15 Pro Max 256GB Titan Tự Nhiên') . ' - ElectronicShop')

@push('styles')
<style>
.product-detail-wrap { max-width: 1200px; margin: 0 auto; padding: 16px; }
.breadcrumb { margin-bottom: 16px; }

/* MAIN PRODUCT */
.product-main { display: grid; grid-template-columns: 340px 1fr 260px; gap: 24px; margin-bottom: 24px; }

/* GALLERY */
.gallery { }
.gallery-main {
    border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;
    height: 300px; display: flex; align-items: center; justify-content: center;
    position: relative; background: #f8f8f8; margin-bottom: 10px;
}
.gallery-main img { max-width: 100%; max-height: 100%; object-fit: contain; }
.gallery-main .discount-tag {
    position: absolute; top: 10px; left: 10px; background: #E53935; color: #fff;
    font-size: 12px; font-weight: 700; padding: 3px 8px; border-radius: 4px;
}
.gallery-main .wish-btn {
    position: absolute; top: 10px; right: 10px; background: #fff;
    border: 1px solid #e0e0e0; width: 32px; height: 32px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center; cursor: pointer;
}
.gallery-thumbs { display: flex; gap: 8px; }
.gallery-thumbs .thumb {
    width: 56px; height: 56px; border: 2px solid transparent; border-radius: 6px;
    overflow: hidden; cursor: pointer; background: #f0f0f0;
    display: flex; align-items: center; justify-content: center;
}
.gallery-thumbs .thumb.active { border-color: #1565C0; }
.gallery-thumbs .thumb img { width: 100%; height: 100%; object-fit: cover; }

/* INFO */
.product-info { }
.product-brand { font-size: 12px; font-weight: 700; color: #1565C0; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 6px; }
.product-name { font-size: 22px; font-weight: 800; line-height: 1.3; margin-bottom: 10px; }
.product-rating { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; font-size: 13px; }
.product-rating .stars { color: #FFA000; }
.product-rating .sep { color: #ddd; }
.product-rating .sold { color: #888; }
.price-block { margin-bottom: 14px; }
.price-current { font-size: 28px; font-weight: 800; color: #1565C0; }
.price-old { font-size: 14px; color: #aaa; text-decoration: line-through; margin-left: 10px; }
.price-pct { font-size: 12px; color: #E53935; font-weight: 600; margin-left: 6px; }
.in-stock { color: #2E7D32; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 4px; }
.option-group { margin-bottom: 14px; }
.option-label { font-size: 13px; color: #555; margin-bottom: 8px; font-weight: 500; }
.option-label strong { color: #111; }
.option-btns { display: flex; gap: 8px; flex-wrap: wrap; }
.opt-btn {
    padding: 6px 16px; border: 1.5px solid #e0e0e0; border-radius: 6px;
    font-size: 13px; font-weight: 500; cursor: pointer; background: #fff;
    transition: all .15s;
}
.opt-btn.active { border-color: #1565C0; color: #1565C0; background: #EBF3FF; }
.opt-btn:hover { border-color: #1565C0; }
.color-dot {
    width: 28px; height: 28px; border-radius: 50%; border: 2px solid transparent;
    cursor: pointer; transition: border-color .15s;
}
.color-dot.active { border-color: #1565C0; box-shadow: 0 0 0 2px #fff inset; }
.color-dots { display: flex; gap: 8px; }
.action-btns { display: flex; gap: 12px; margin-top: 20px; }
.btn-add-cart {
    flex: 1; padding: 13px; border: 2px solid #1565C0; border-radius: 8px;
    background: #fff; color: #1565C0; font-size: 15px; font-weight: 700; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    transition: all .2s;
}
.btn-add-cart:hover { background: #EBF3FF; }
.btn-buy-now {
    flex: 1; padding: 13px; border: none; border-radius: 8px;
    background: #1565C0; color: #fff; font-size: 15px; font-weight: 700; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    transition: all .2s;
}
.btn-buy-now:hover { background: #0D47A1; }

/* SIDEBAR BENEFITS */
.product-sidebar { }
.benefit-box { border: 1px solid #e0e0e0; border-radius: 8px; padding: 16px; margin-bottom: 14px; }
.benefit-item { display: flex; gap: 10px; align-items: flex-start; margin-bottom: 12px; }
.benefit-item:last-child { margin-bottom: 0; }
.benefit-item .bi-icon { color: #1565C0; font-size: 18px; margin-top: 2px; }
.benefit-item .bi-title { font-size: 13px; font-weight: 600; }
.benefit-item .bi-desc { font-size: 12px; color: #777; }
.commit-box { border: 1px solid #e0e0e0; border-radius: 8px; padding: 16px; }
.commit-box h4 { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 12px; }
.commit-item { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; font-size: 13px; color: #444; }
.commit-item i { color: #1565C0; }

/* TABS */
.product-tabs { border-bottom: 1px solid #e0e0e0; margin-bottom: 24px; display: flex; gap: 0; }
.tab-btn {
    padding: 12px 24px; background: none; border: none; border-bottom: 2px solid transparent;
    font-size: 13px; font-weight: 600; cursor: pointer; text-transform: uppercase;
    letter-spacing: .5px; color: #777; transition: all .2s;
}
.tab-btn.active { color: #1565C0; border-bottom-color: #1565C0; }
.tab-content { display: none; }
.tab-content.active { display: block; }
.desc-section { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px; }
.desc-text h2 { font-size: 22px; font-weight: 800; margin-bottom: 14px; line-height: 1.3; }
.desc-text p { color: #555; font-size: 14px; line-height: 1.7; margin-bottom: 14px; }
.desc-features { list-style: none; }
.desc-features li { display: flex; align-items: flex-start; gap: 8px; font-size: 13px; margin-bottom: 8px; color: #444; }
.desc-features li i { color: #1565C0; margin-top: 2px; }
.see-more { color: #1565C0; font-size: 13px; font-weight: 500; margin-top: 12px; display: inline-block; }
.desc-image { border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; background: #f5f5f5; display: flex; align-items: center; justify-content: center; min-height: 240px; color: #ccc; }

/* RELATED */
.related-section { margin-top: 48px; }
.related-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 14px; }
</style>
@endpush

@section('content')
<div class="product-detail-wrap">
    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('home') }}">Trang chủ</a>
        <span>/</span>
        <a href="{{ route('products.index') }}">Điện thoại</a>
        <span>/</span>
        <a href="#">Apple</a>
        <span>/</span>
        <span style="color:#333">{{ $product->name ?? 'iPhone 15 Pro Max' }}</span>
    </div>

    {{-- PRODUCT MAIN --}}
    <div class="product-main">
        {{-- Gallery --}}
        <div class="gallery">
            <div class="gallery-main">
                <span class="discount-tag">-7%</span>
                <button class="wish-btn"><i class="far fa-heart" style="color:#aaa"></i></button>
                @if(isset($product->images[0]))
                    <img src="{{ $product->images[0] }}" alt="{{ $product->name }}" id="mainImg">
                @else
                    <div class="img-placeholder" style="width:100%;height:100%"><i class="fas fa-image fa-3x"></i></div>
                @endif
            </div>
            <div class="gallery-thumbs">
                @foreach($product->images ?? [] as $img)
                <div class="thumb {{ $loop->first ? 'active' : '' }}" onclick="document.getElementById('mainImg').src='{{ $img }}'">
                    <img src="{{ $img }}" alt="">
                </div>
                @endforeach
                @if(empty($product->images ?? []))
                @for($i=0;$i<5;$i++)
                <div class="thumb {{ $i===0?'active':'' }}" style="background:#{{ ['1a1a2e','2d1b33','1a2a1a','2a1a0a','1a0a1a'][$i] }}">
                </div>
                @endfor
                @endif
            </div>
        </div>

        {{-- Product Info --}}
        <div class="product-info">
            <div class="product-brand">{{ $product->brand->name ?? 'APPLE' }}</div>
            <h1 class="product-name">{{ $product->name ?? 'iPhone 15 Pro Max 256GB Titan Tự Nhiên' }}</h1>
            <div class="product-rating">
                <span class="stars">★★★★★</span>
                <span>{{ $product->rating ?? '4.9' }}</span>
                <span class="sep">|</span>
                <span>{{ $product->reviews_count ?? 128 }} đánh giá</span>
                <span class="sep">|</span>
                <span class="sold">{{ $product->sold_count ?? 256 }} đã bán</span>
            </div>
            <div class="price-block">
                <span class="price-current">{{ number_format($product->price ?? 28990000) }}đ</span>
                @if(isset($product->original_price))
                <span class="price-old">{{ number_format($product->original_price) }}đ</span>
                <span class="price-pct">-{{ round((1 - $product->price/$product->original_price)*100) }}%</span>
                @else
                <span class="price-old">30.990.000đ</span>
                <span class="price-pct">-7%</span>
                @endif
            </div>
            <div class="in-stock"><i class="fas fa-check-circle"></i> Còn hàng</div>

            <div style="margin-top:16px">
                <div class="option-group">
                    <div class="option-label">Dung lượng: <strong>256GB</strong></div>
                    <div class="option-btns">
                        <button class="opt-btn active">256GB</button>
                        <button class="opt-btn">512GB</button>
                        <button class="opt-btn">1TB</button>
                    </div>
                </div>
                <div class="option-group">
                    <div class="option-label">Màu sắc: <strong>Titan Tự Nhiên</strong></div>
                    <div class="color-dots">
                        <div class="color-dot active" style="background:#C4B69A" title="Titan Tự Nhiên"></div>
                        <div class="color-dot" style="background:#D4D4D4" title="Titan Trắng"></div>
                        <div class="color-dot" style="background:#4A4A4A" title="Titan Đen"></div>
                        <div class="color-dot" style="background:#1C2B4A" title="Titan Xanh"></div>
                    </div>
                </div>
            </div>

            <div class="action-btns">
                <form action="{{ route('cart.add') }}" method="POST" style="flex:1">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id ?? 1 }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="btn-add-cart" style="width:100%">
                        <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                    </button>
                </form>
                <form action="{{ route('cart.buy-now') }}" method="POST" style="flex:1">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id ?? 1 }}">
                    <button type="submit" class="btn-buy-now" style="width:100%">Mua ngay</button>
                </form>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="product-sidebar">
            <a href="{{ route('products.compare') }}" style="font-size:13px;color:#1565C0;display:flex;align-items:center;gap:6px;margin-top:10px">
    <i class="fas fa-exchange-alt"></i> So sánh sản phẩm này
</a>
            <div class="benefit-box">
                <div class="benefit-item">
                    <i class="fas fa-truck bi-icon"></i>
                    <div><div class="bi-title">Giao hàng tận nơi</div><div class="bi-desc">Giao hàng miễn phí toàn quốc</div></div>
                </div>
                
                <div class="benefit-item">
                    <i class="fas fa-shield-alt bi-icon"></i>
                    <div><div class="bi-title">Thanh toán an toàn</div><div class="bi-desc">Nhận hàng và thanh toán tại nhà</div></div>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-sync-alt bi-icon"></i>
                    <div><div class="bi-title">Trả góp 0%</div><div class="bi-desc">Duyệt nhanh qua thẻ tín dụng</div></div>
                </div>
            </div>
            <div class="commit-box">
                <h4>Cam kết từ PHONE</h4>
                <div class="commit-item"><i class="fas fa-check-circle"></i> Hàng chính hãng 100%</div>
                <div class="commit-item"><i class="fas fa-map-marker-alt"></i> Bảo hành 12 tháng</div>
                <div class="commit-item"><i class="fas fa-headset"></i> Hỗ trợ 24/7: 0123 456 789</div>
            </div>
        </div>
    </div>

    {{-- TABS --}}
    <div class="product-tabs">
        <button class="tab-btn active" onclick="switchTab(this,'tab-desc')">Mô tả sản phẩm</button>
        <button class="tab-btn" onclick="switchTab(this,'tab-specs')">Thông số kỹ thuật</button>
        <button class="tab-btn" onclick="switchTab(this,'tab-reviews')">Đánh giá ({{ $product->reviews_count ?? 128 }})</button>
        <button class="tab-btn" onclick="switchTab(this,'tab-qa')">Hỏi đáp (32)</button>
    </div>

    <div id="tab-desc" class="tab-content active">
        <div class="desc-section">
            <div class="desc-text">
                <h2>iPhone 15 Pro Max. Titan. Mạnh mẽ. Nhẹ. Pro vượt trội.</h2>
                <p>iPhone 15 Pro Max được chế tác từ titan cấp hàng không vũ trụ, mang đến sự bền bỉ vượt trội trong một thiết kế nhẹ nhàng đột phá. Chip A17 Pro với GPU 6 lõi cho hiệu năng đồ họa đỉnh cao, hệ thống camera Pro 48MP chụp ảnh siêu nét và quay video ProRes 4K 60fps chuyên nghiệp.</p>
                <ul class="desc-features">
                    <li><i class="fas fa-check-circle"></i> Thiết kế titan nhẹ và bền bỉ</li>
                    <li><i class="fas fa-microchip"></i> Chip A17 Pro – Hiệu năng đột phá</li>
                    <li><i class="fas fa-camera"></i> Camera chính 48MP – Chi tiết siêu nét</li>
                    <li><i class="fas fa-battery-full"></i> Pin cả ngày dài – Lên đến 29 giờ xem video</li>
                </ul>
                <a href="#" class="see-more">XEM THÊM →</a>
            </div>
            <div class="desc-image img-placeholder"><i class="fas fa-image fa-3x"></i></div>
        </div>
    </div>

    <div id="tab-specs" class="tab-content">
        <table style="width:100%;border-collapse:collapse;font-size:14px">
            @foreach([
                ['Màn hình','6.7 inch Super Retina XDR OLED'],
                ['Chip','Apple A17 Pro'],
                ['Camera sau','48MP + 12MP + 12MP'],
                ['Camera trước','12MP TrueDepth'],
                ['RAM','8GB'],
                ['Bộ nhớ','256GB / 512GB / 1TB'],
                ['Pin','4422 mAh'],
                ['Hệ điều hành','iOS 17'],
                ['Kích thước','159.9 × 76.7 × 8.25 mm'],
                ['Trọng lượng','221g'],
            ] as $i => $spec)
            <tr style="background:{{ $i%2===0?'#f8f9fa':'#fff' }}">
                <td style="padding:10px 16px;color:#666;width:200px;border:1px solid #eee">{{ $spec[0] }}</td>
                <td style="padding:10px 16px;border:1px solid #eee">{{ $spec[1] }}</td>
            </tr>
            @endforeach
        </table>
    </div>

    <div id="tab-reviews" class="tab-content">
        <p style="color:#888;padding:24px 0">Chức năng đánh giá sẽ được hiển thị ở đây.</p>
    </div>
    <div id="tab-qa" class="tab-content">
        <p style="color:#888;padding:24px 0">Chức năng hỏi đáp sẽ được hiển thị ở đây.</p>
    </div>

    {{-- RELATED PRODUCTS --}}
    <div class="related-section">
        <div class="section-header">
            <h2 class="section-title">Sản phẩm liên quan</h2>
        </div>
        <div class="related-grid">
            @forelse($relatedProducts ?? [] as $p)
            <a href="{{ route('products.show', $p->slug) }}" class="product-card">
                <div class="product-card-img img-placeholder"><i class="fas fa-image"></i></div>
                <div class="product-card-body">
                    <div class="product-card-name">{{ $p->name }}</div>
                    <div class="product-card-price">{{ number_format($p->price) }}đ</div>
                    <div class="stars">★★★★☆ <span class="review-count">({{ $p->reviews_count }})</span></div>
                </div>
            </a>
            @empty
            @foreach([
                ['iPhone 15 Pro 256GB','25.990.000','4.5','98'],
                ['iPhone 15 128GB','19.990.000','4.6','74'],
                ['iPhone 14 Pro Max 256GB','24.990.000','4.7','56'],
                ['iPhone 14 128GB','16.990.000','4.6','42'],
                ['iPhone 13 128GB','12.990.000','4.5','38'],
            ] as $p)
            <a href="#" class="product-card">
                <div class="product-card-img img-placeholder"><i class="fas fa-image"></i></div>
                <div class="product-card-body">
                    <div class="product-card-name">{{ $p[0] }}</div>
                    <div><span class="product-card-price">{{ $p[1] }}đ</span></div>
                    <div class="stars">★★★★☆ <span class="review-count">({{ $p[3] }})</span></div>
                </div>
            </a>
            @endforeach
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function switchTab(btn, tabId) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById(tabId).classList.add('active');
}
document.querySelectorAll('.opt-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        this.closest('.option-btns').querySelectorAll('.opt-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
    });
});
document.querySelectorAll('.color-dot').forEach(dot => {
    dot.addEventListener('click', function() {
        this.closest('.color-dots').querySelectorAll('.color-dot').forEach(d => d.classList.remove('active'));
        this.classList.add('active');
    });
});
</script>
@endpush