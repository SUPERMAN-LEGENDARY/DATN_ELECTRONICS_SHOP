@extends('layouts.app')
@section('title', $product->name . ' - ElectronicShop')

@push('styles')
<style>
.product-detail-wrap { max-width: 1200px; margin: 0 auto; padding: 16px; }

/* MAIN GRID */
.product-main { display: grid; grid-template-columns: 340px 1fr 260px; gap: 24px; margin-bottom: 32px; }

/* GALLERY */
.gallery-main { border: 1px solid #e0e0e0; border-radius: 8px; height: 300px; display: flex; align-items: center; justify-content: center; position: relative; background: #f8f8f8; margin-bottom: 10px; overflow: hidden; }
.gallery-main img { max-width: 100%; max-height: 100%; object-fit: contain; }
.discount-tag { position: absolute; top: 10px; left: 10px; background: #E53935; color: #fff; font-size: 12px; font-weight: 700; padding: 3px 8px; border-radius: 4px; }
.gallery-thumbs { display: flex; gap: 8px; flex-wrap: wrap; }
.thumb { width: 56px; height: 56px; border: 2px solid transparent; border-radius: 6px; overflow: hidden; cursor: pointer; background: #f0f0f0; display: flex; align-items: center; justify-content: center; }
.thumb.active { border-color: #1565C0; }
.thumb img { width: 100%; height: 100%; object-fit: cover; }

/* INFO */
.product-brand { font-size: 12px; font-weight: 700; color: #1565C0; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 6px; }
.product-name  { font-size: 22px; font-weight: 800; line-height: 1.3; margin-bottom: 10px; }
.product-rating { display: flex; align-items: center; gap: 10px; font-size: 13px; margin-bottom: 14px; }
.product-rating .stars { color: #FFA000; }
.price-current { font-size: 28px; font-weight: 800; color: #1565C0; }
.price-old     { font-size: 14px; color: #aaa; text-decoration: line-through; margin-left: 10px; }
.price-pct     { font-size: 12px; color: #E53935; font-weight: 600; margin-left: 6px; }
.in-stock      { color: #2E7D32; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 4px; margin: 8px 0 16px; }
.out-of-stock  { color: #E53935; font-size: 13px; font-weight: 500; margin: 8px 0 16px; }

.option-group { margin-bottom: 14px; }
.option-label { font-size: 13px; color: #555; margin-bottom: 8px; font-weight: 500; }
.option-btns  { display: flex; gap: 8px; flex-wrap: wrap; }
.opt-btn { padding: 6px 16px; border: 1.5px solid #e0e0e0; border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer; background: #fff; transition: all .15s; }
.opt-btn.active { border-color: #1565C0; color: #1565C0; background: #EBF3FF; }
.opt-btn:hover  { border-color: #1565C0; }

.action-btns { display: flex; gap: 12px; margin-top: 20px; }
.btn-add-cart { flex: 1; padding: 13px; border: 2px solid #1565C0; border-radius: 8px; background: #fff; color: #1565C0; font-size: 15px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all .2s; }
.btn-add-cart:hover { background: #EBF3FF; }
.btn-buy-now  { flex: 1; padding: 13px; border: none; border-radius: 8px; background: #1565C0; color: #fff; font-size: 15px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all .2s; }
.btn-buy-now:hover { background: #0D47A1; }

/* SIDEBAR */
.benefit-box { border: 1px solid #e0e0e0; border-radius: 8px; padding: 16px; margin-bottom: 14px; }
.benefit-item { display: flex; gap: 10px; align-items: flex-start; margin-bottom: 12px; }
.benefit-item:last-child { margin-bottom: 0; }
.benefit-item .bi-icon { color: #1565C0; font-size: 18px; margin-top: 2px; }
.benefit-item .bi-title { font-size: 13px; font-weight: 600; }
.benefit-item .bi-desc  { font-size: 12px; color: #777; }
.commit-box { border: 1px solid #e0e0e0; border-radius: 8px; padding: 16px; }
.commit-box h4 { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 12px; }
.commit-item { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; font-size: 13px; color: #444; }
.commit-item i { color: #1565C0; }

/* TABS */
.product-tabs { border-bottom: 1px solid #e0e0e0; margin-bottom: 24px; display: flex; gap: 0; overflow-x: auto; }
.tab-btn { padding: 12px 24px; background: none; border: none; border-bottom: 2px solid transparent; font-size: 13px; font-weight: 600; cursor: pointer; text-transform: uppercase; letter-spacing: .5px; color: #777; white-space: nowrap; transition: all .2s; }
.tab-btn.active { color: #1565C0; border-bottom-color: #1565C0; }
.tab-content { display: none; }
.tab-content.active { display: block; }

/* SPECS TABLE */
.specs-table { width: 100%; border-collapse: collapse; font-size: 14px; }
.specs-table tr:nth-child(even) { background: #f8f9fa; }
.specs-table td { padding: 10px 16px; border: 1px solid #eee; }
.specs-table td:first-child { color: #666; width: 200px; font-weight: 500; }

/* REVIEWS */
.rating-summary { display: flex; align-items: center; gap: 32px; padding: 24px; background: #f8f9fa; border-radius: 8px; margin-bottom: 24px; }
.rating-avg { text-align: center; }
.rating-avg .big { font-size: 56px; font-weight: 800; color: #1565C0; line-height: 1; }
.rating-avg .stars-lg { font-size: 20px; color: #FFA000; margin: 4px 0; }
.rating-avg small { color: #888; font-size: 13px; }
.rating-bars { flex: 1; }
.rating-bar-row { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; font-size: 13px; }
.rating-bar-row .bar { flex: 1; height: 6px; background: #e0e0e0; border-radius: 3px; overflow: hidden; }
.rating-bar-row .bar-fill { height: 100%; background: #FFA000; border-radius: 3px; }
.rating-bar-row .cnt { width: 30px; text-align: right; color: #888; }

.review-item { border-bottom: 1px solid #f0f0f0; padding: 16px 0; }
.review-item:last-child { border-bottom: none; }
.review-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px; }
.review-user { font-weight: 600; font-size: 14px; }
.review-date { font-size: 12px; color: #aaa; }
.review-stars { color: #FFA000; font-size: 14px; margin-bottom: 6px; }
.review-content { font-size: 14px; color: #444; line-height: 1.6; }

.review-form { background: #f8f9fa; border-radius: 8px; padding: 20px; margin-top: 24px; }
.review-form h4 { font-size: 15px; font-weight: 700; margin-bottom: 16px; }
.star-rating { display: flex; gap: 6px; margin-bottom: 12px; }
.star-rating input { display: none; }
.star-rating label { font-size: 28px; color: #ddd; cursor: pointer; transition: color .1s; }
.star-rating input:checked ~ label,
.star-rating label:hover,
.star-rating label:hover ~ label { color: #FFA000; }
.form-textarea { width: 100%; border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px; font-size: 14px; outline: none; resize: vertical; font-family: inherit; }
.form-textarea:focus { border-color: #1565C0; }
.btn-submit-review { margin-top: 10px; padding: 10px 24px; background: #1565C0; color: #fff; border: none; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; }
.btn-submit-review:hover { background: #0D47A1; }

/* RELATED */
.related-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 14px; }
.product-card { display: block; border: 1px solid #e8e8e8; border-radius: 8px; overflow: hidden; text-decoration: none; color: inherit; transition: box-shadow .2s; position: relative; background: #fff; }
.product-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.1); }
.product-card-img { height: 140px; background: #f5f5f5; display: flex; align-items: center; justify-content: center; overflow: hidden; }
.product-card-img img { width: 100%; height: 100%; object-fit: cover; }
.product-card-body { padding: 10px; }
.product-card-name { font-size: 12px; font-weight: 600; margin-bottom: 4px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.product-card-price { font-size: 14px; font-weight: 800; color: #1565C0; }
.stars { font-size: 11px; color: #FFA000; }
</style>
@endpush

@section('content')
<div class="product-detail-wrap">

    {{-- Breadcrumb --}}
    <div class="breadcrumb" style="font-size:13px;color:#888;margin-bottom:16px">
        <a href="{{ route('home') }}" style="color:#1565C0">Trang chủ</a> /
        <a href="{{ route('products.index') }}" style="color:#1565C0">Sản phẩm</a> /
        @if($product->category)
            <a href="{{ route('products.index', ['category' => $product->category_id]) }}" style="color:#1565C0">
                {{ $product->category->name }}
            </a> /
        @endif
        <span style="color:#333">{{ $product->name }}</span>
    </div>

    {{-- PRODUCT MAIN --}}
    <div class="product-main">

        {{-- Gallery --}}
        <div class="gallery">
            <div class="gallery-main">
                @if($product->discount_percent > 0)
                    <span class="discount-tag">-{{ $product->discount_percent }}%</span>
                @endif
                @if($product->first_image)
                    <img src="{{ $product->first_image }}" alt="{{ $product->name }}" id="mainImg">
                @else
                    <i class="fas fa-image fa-3x" style="color:#ccc"></i>
                @endif
            </div>
            <div class="gallery-thumbs">
                @foreach($product->images ?? [] as $img)
                <div class="thumb {{ $loop->first ? 'active' : '' }}"
                     onclick="switchImage(this, '{{ $img }}')">
                    <img src="{{ $img }}" alt="">
                </div>
                @endforeach
            </div>
        </div>

        {{-- Product Info --}}
        <div class="product-info">
            <div class="product-brand">{{ $product->brand->name ?? '' }}</div>
            <h1 class="product-name">{{ $product->name }}</h1>

            <div class="product-rating">
                <span class="stars">
                    @for($i=1;$i<=5;$i++)
                        {{ $i <= round($product->avg_rating) ? '★' : '☆' }}
                    @endfor
                </span>
                <span>{{ $product->avg_rating }}/5</span>
                <span style="color:#ddd">|</span>
                <span>{{ $product->reviews_count }} đánh giá</span>
                <span style="color:#ddd">|</span>
                <span style="color:#888">{{ number_format($product->stock) }} còn lại</span>
            </div>

            <div class="price-block">
                <span class="price-current">{{ number_format($product->sale_price) }}đ</span>
                @if($product->discount_percent > 0)
                    <span class="price-old">{{ number_format($product->price) }}đ</span>
                    <span class="price-pct">-{{ $product->discount_percent }}%</span>
                @endif
            </div>

            @if($product->stock > 0)
                <div class="in-stock"><i class="fas fa-check-circle"></i> Còn hàng ({{ $product->stock }})</div>
            @else
                <div class="out-of-stock"><i class="fas fa-times-circle"></i> Hết hàng</div>
            @endif

            {{-- Thuộc tính nhóm (vd: Dung lượng, Màu sắc) --}}
            @php
                $storageAttrs = $product->attributes->filter(fn($a) => Str::contains(strtolower($a->attribute->name ?? ''), ['dung lượng','storage','bộ nhớ']));
                $colorAttrs   = $product->attributes->filter(fn($a) => Str::contains(strtolower($a->attribute->name ?? ''), ['màu','color']));
            @endphp

            @if($storageAttrs->isNotEmpty())
            <div class="option-group">
                <div class="option-label">Dung lượng: <strong>{{ $storageAttrs->first()->value }}</strong></div>
                <div class="option-btns">
                    @foreach($storageAttrs as $attr)
                    <button class="opt-btn {{ $loop->first ? 'active' : '' }}">{{ $attr->value }}</button>
                    @endforeach
                </div>
            </div>
            @endif

            @if($colorAttrs->isNotEmpty())
            <div class="option-group">
                <div class="option-label">Màu sắc: <strong>{{ $colorAttrs->first()->value }}</strong></div>
                <div class="option-btns">
                    @foreach($colorAttrs as $attr)
                    <button class="opt-btn {{ $loop->first ? 'active' : '' }}">{{ $attr->value }}</button>
                    @endforeach
                </div>
            </div>
            @endif

            @if($product->stock > 0)
            <div class="action-btns">
                <form action="{{ route('cart.add') }}" method="POST" style="flex:1">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="btn-add-cart" style="width:100%">
                        <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                    </button>
                </form>
                <form action="{{ route('cart.buy-now') }}" method="POST" style="flex:1">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <button type="submit" class="btn-buy-now" style="width:100%">
                        <i class="fas fa-bolt"></i> Mua ngay
                    </button>
                </form>
            </div>
            @endif
        </div>

        {{-- Sidebar Benefits --}}
        <div class="product-sidebar">
            <div class="benefit-box">
                <div class="benefit-item">
                    <i class="fas fa-truck bi-icon"></i>
                    <div>
                        <div class="bi-title">Giao hàng tận nơi</div>
                        <div class="bi-desc">Miễn phí toàn quốc đơn từ 500k</div>
                    </div>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-shield-alt bi-icon"></i>
                    <div>
                        <div class="bi-title">Thanh toán an toàn</div>
                        <div class="bi-desc">Nhận hàng kiểm tra rồi mới thanh toán</div>
                    </div>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-sync-alt bi-icon"></i>
                    <div>
                        <div class="bi-title">Trả góp 0%</div>
                        <div class="bi-desc">Duyệt nhanh qua thẻ tín dụng</div>
                    </div>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-undo bi-icon"></i>
                    <div>
                        <div class="bi-title">Đổi trả 30 ngày</div>
                        <div class="bi-desc">Hoàn tiền nếu lỗi nhà sản xuất</div>
                    </div>
                </div>
            </div>
            <div class="commit-box">
                <h4>Cam kết từ ElectronicShop</h4>
                <div class="commit-item"><i class="fas fa-check-circle"></i> Hàng chính hãng 100%</div>
                <div class="commit-item"><i class="fas fa-map-marker-alt"></i> Bảo hành 12 tháng</div>
                <div class="commit-item"><i class="fas fa-headset"></i> Hỗ trợ 24/7: 1900 1234</div>
                <div class="commit-item"><i class="fas fa-store"></i> 50+ cửa hàng toàn quốc</div>
            </div>
        </div>
    </div>

    {{-- TABS --}}
    <div class="product-tabs">
        <button class="tab-btn active" onclick="switchTab(this,'tab-desc')">Mô tả sản phẩm</button>
        <button class="tab-btn" onclick="switchTab(this,'tab-specs')">Thông số kỹ thuật</button>
        <button class="tab-btn" onclick="switchTab(this,'tab-reviews')">
            Đánh giá ({{ $product->reviews_count }})
        </button>
    </div>

    {{-- Tab: Mô tả --}}
    <div id="tab-desc" class="tab-content active">
        @if($product->description)
            <div style="font-size:14px;line-height:1.8;color:#444;max-width:800px">
                {!! nl2br(e($product->description)) !!}
            </div>
        @else
            <p style="color:#999;padding:24px 0">Chưa có mô tả cho sản phẩm này.</p>
        @endif
    </div>

    {{-- Tab: Thông số kỹ thuật --}}
    <div id="tab-specs" class="tab-content">
        @if($product->attributes->isNotEmpty())
        <table class="specs-table">
            @foreach($product->attributes as $attr)
            <tr>
                <td>{{ $attr->attribute->name }}</td>
                <td>{{ $attr->value }}</td>
            </tr>
            @endforeach
        </table>
        @else
            <p style="color:#999;padding:24px 0">Chưa có thông số kỹ thuật.</p>
        @endif
    </div>

    {{-- Tab: Đánh giá --}}
    <div id="tab-reviews" class="tab-content">

        {{-- Rating Summary --}}
        <div class="rating-summary">
            <div class="rating-avg">
                <div class="big">{{ number_format($product->avg_rating, 1) }}</div>
                <div class="stars-lg">
                    @for($i=1;$i<=5;$i++)
                        {{ $i <= round($product->avg_rating) ? '★' : '☆' }}
                    @endfor
                </div>
                <small>{{ $product->reviews_count }} đánh giá</small>
            </div>
            <div class="rating-bars">
                @for($star=5; $star>=1; $star--)
                @php $cnt = $ratingDistribution[$star] ?? 0; $pct = $product->reviews_count ? ($cnt/$product->reviews_count*100) : 0; @endphp
                <div class="rating-bar-row">
                    <span style="width:14px;text-align:right">{{ $star }}</span>
                    <i class="fas fa-star" style="color:#FFA000;font-size:11px"></i>
                    <div class="bar"><div class="bar-fill" style="width:{{ $pct }}%"></div></div>
                    <span class="cnt">{{ $cnt }}</span>
                </div>
                @endfor
            </div>
        </div>

        {{-- Danh sách đánh giá --}}
        @forelse($reviews as $review)
        <div class="review-item">
            <div class="review-header">
                <span class="review-user">{{ $review->user->name ?? 'Người dùng ẩn danh' }}</span>
                <span class="review-date">{{ $review->created_at->format('d/m/Y') }}</span>
            </div>
            <div class="review-stars">
                @for($i=1;$i<=5;$i++)
                    {{ $i <= $review->rating ? '★' : '☆' }}
                @endfor
            </div>
            @if($review->content)
            <div class="review-content">{{ $review->content }}</div>
            @endif
        </div>
        @empty
        <p style="color:#999;padding:16px 0">Chưa có đánh giá nào. Hãy là người đầu tiên đánh giá!</p>
        @endforelse

        {{-- Phân trang reviews --}}
        @if($reviews->hasPages())
        <div style="margin-top:16px">{{ $reviews->links() }}</div>
        @endif

        {{-- Form đánh giá --}}
        @auth
        <div class="review-form">
            <h4>Viết đánh giá của bạn</h4>

            @if(session('success'))
                <div style="background:#E8F5E9;color:#2E7D32;padding:10px 14px;border-radius:6px;margin-bottom:12px;font-size:14px">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div style="background:#FFEBEE;color:#C62828;padding:10px 14px;border-radius:6px;margin-bottom:12px;font-size:14px">
                    <i class="fas fa-times-circle"></i> {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('products.review', $product->id) }}" method="POST">
                @csrf
                <div style="margin-bottom:12px">
                    <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px">Chọn sao:</label>
                    <div class="star-rating" id="starRating">
                        @for($i=5;$i>=1;$i--)
                        <input type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}"
                               {{ old('rating')==$i ? 'checked' : '' }}>
                        <label for="star{{ $i }}">★</label>
                        @endfor
                    </div>
                    @error('rating')
                        <span style="color:#E53935;font-size:12px">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px">Nội dung (tuỳ chọn):</label>
                    <textarea name="content" rows="4" class="form-textarea"
                              placeholder="Chia sẻ trải nghiệm của bạn...">{{ old('content') }}</textarea>
                </div>
                <button type="submit" class="btn-submit-review">
                    <i class="fas fa-paper-plane"></i> Gửi đánh giá
                </button>
            </form>
        </div>
        @else
        <div style="background:#f8f9fa;border-radius:8px;padding:20px;margin-top:24px;text-align:center;font-size:14px;color:#666">
            <a href="{{ route('login') }}" style="color:#1565C0;font-weight:600">Đăng nhập</a> để viết đánh giá.
        </div>
        @endauth
    </div>

    {{-- RELATED PRODUCTS --}}
    @if($relatedProducts->isNotEmpty())
    <div style="margin-top:48px">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
            <h2 style="font-size:18px;font-weight:800">Sản phẩm liên quan</h2>
            <a href="{{ route('products.index', ['category' => $product->category_id]) }}"
               style="font-size:13px;color:#1565C0">Xem tất cả →</a>
        </div>
        <div class="related-grid">
            @foreach($relatedProducts as $p)
            <a href="{{ route('products.show', $p->slug) }}" class="product-card">
                <div class="product-card-img">
                    @if($p->first_image)
                        <img src="{{ $p->first_image }}" alt="{{ $p->name }}" loading="lazy">
                    @else
                        <i class="fas fa-image fa-2x" style="color:#ccc"></i>
                    @endif
                </div>
                <div class="product-card-body">
                    <div class="product-card-name">{{ $p->name }}</div>
                    <div class="product-card-price">{{ number_format($p->sale_price) }}đ</div>
                    <div class="stars">
                        @for($i=1;$i<=5;$i++){{ $i <= round($p->avg_rating) ? '★' : '☆' }}@endfor
                        <span style="color:#888">({{ $p->reviews_count }})</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

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
function switchImage(thumb, src) {
    document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
    thumb.classList.add('active');
    const img = document.getElementById('mainImg');
    if (img) img.src = src;
}
document.querySelectorAll('.opt-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        this.closest('.option-btns').querySelectorAll('.opt-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
    });
});
</script>
@endpush
