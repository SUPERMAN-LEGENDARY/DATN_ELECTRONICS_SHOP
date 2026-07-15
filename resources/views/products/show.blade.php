@extends('layouts.app')
@section('title', $product->name . ' - ElectronicShop')

@push('styles')
<style>
.product-detail-wrap { max-width: 1200px; margin: 0 auto; padding: 16px; }

/* MAIN GRID */
.product-main { display: grid; grid-template-columns: 340px 1fr 260px; gap: 24px; margin-bottom: 32px; }

/* GALLERY */
.gallery-main { border: 1px solid #eef0f2; border-radius: 12px; height: 300px; display: flex; align-items: center; justify-content: center; position: relative; background: #fff; margin-bottom: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,.04); }
.gallery-main img { max-width: 100%; max-height: 100%; object-fit: contain; }
.discount-tag { position: absolute; top: 10px; left: 10px; background: #E53935; color: #fff; font-size: 12px; font-weight: 700; padding: 3px 8px; border-radius: 5px; }
.gallery-thumbs { display: flex; gap: 10px; flex-wrap: wrap; }
.thumb { width: 58px; height: 58px; border: 2px solid #eef0f2; border-radius: 8px; overflow: hidden; cursor: pointer; background: #fff; display: flex; align-items: center; justify-content: center; transition: border-color .15s; }
.thumb:hover { border-color: #90caf9; }
.thumb.active { border-color: #1565C0; }
.thumb img { width: 100%; height: 100%; object-fit: contain; padding: 4px; box-sizing: border-box; }

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

#variantSelector { display: flex; flex-direction: column; gap: 14px; margin-bottom: 6px; }
.option-group { margin-bottom: 0; }
.option-label { font-size: 13px; color: #555; margin-bottom: 8px; font-weight: 600; }
.option-label strong { color: #1e293b; font-weight: 700; }
.option-btns  { display: flex; gap: 10px; flex-wrap: wrap; }
.opt-btn { padding: 8px 18px; min-width: 56px; border: 1.5px solid #e0e0e0; border-radius: 8px; font-size: 13.5px; font-weight: 600; cursor: pointer; background: #fff; color: #333; transition: all .15s; }
.opt-btn.active { border-color: #1565C0; color: #1565C0; background: #EBF3FF; box-shadow: 0 0 0 1px #1565C0 inset; }
.opt-btn:hover:not(.opt-btn-disabled)  { border-color: #1565C0; color: #1565C0; }
.opt-btn-disabled { opacity: .4; text-decoration: line-through; cursor: not-allowed; }
.opt-btn-disabled:hover { border-color: #e0e0e0; color: #333; }

.action-btns { display: flex; gap: 12px; margin-top: 20px; }
.btn-add-cart { flex: 1; padding: 13px; border: 2px solid #1565C0; border-radius: 8px; background: #fff; color: #1565C0; font-size: 15px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all .2s; }
.btn-add-cart:hover { background: #EBF3FF; }
.btn-buy-now  { flex: 1; padding: 13px; border: none; border-radius: 8px; background: #1565C0; color: #fff; font-size: 15px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all .2s; }
.btn-buy-now:hover { background: #0D47A1; }

/* SIDEBAR */
.benefit-box { border: 1px solid #f1f3f5; border-radius: 12px; padding: 18px; margin-bottom: 16px; box-shadow: 0 2px 10px rgba(0,0,0,.04); background: #fff; }
.benefit-item { display: flex; gap: 10px; align-items: flex-start; margin-bottom: 14px; }
.benefit-item:last-child { margin-bottom: 0; }
.benefit-item .bi-icon { color: #1565C0; font-size: 18px; margin-top: 2px; }
.benefit-item .bi-title { font-size: 13px; font-weight: 600; }
.benefit-item .bi-desc  { font-size: 12px; color: #777; }
.commit-box { border: 1px solid #f1f3f5; border-radius: 12px; padding: 18px; box-shadow: 0 2px 10px rgba(0,0,0,.04); background: #fff; }
.commit-box h4 { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 14px; }
.commit-item { display: flex; align-items: center; gap: 8px; margin-bottom: 10px; font-size: 13px; color: #444; }
.commit-item:last-child { margin-bottom: 0; }
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
.rating-summary { display: flex; align-items: center; gap: 32px; padding: 26px; background: #f8f9fa; border-radius: 12px; margin-bottom: 26px; }
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

.review-form { background: #f8f9fa; border-radius: 12px; padding: 22px; margin-top: 26px; }
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
.related-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; }
.product-card { display: flex; flex-direction: column; height: 100%; border: 1px solid #e8e8e8; border-radius: 10px; overflow: hidden; text-decoration: none; color: inherit; transition: box-shadow .2s, transform .2s; position: relative; background: #fff; }
.product-card:hover { box-shadow: 0 8px 20px rgba(0,0,0,.1); transform: translateY(-3px); border-color: transparent; }
.product-card-img { height: 140px; background: #fff; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; }
.product-card-img img { width: 100%; height: 100%; object-fit: contain; padding: 8px; box-sizing: border-box; }
.product-card-body { padding: 12px; display: flex; flex-direction: column; flex: 1; }
.product-card-name { font-size: 12.5px; font-weight: 600; margin-bottom: 6px; line-height: 1.4; min-height: 35px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.product-card-price { font-size: 14.5px; font-weight: 800; color: #1565C0; margin-top: auto; }
.stars { font-size: 11px; color: #FFA000; }
</style>
@endpush

@section('content')
<div class="product-detail-wrap">

    {{-- Breadcrumb --}}
    <div class="breadcrumb" style="font-size:13px;color:#888;margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
        <div>
            <a href="{{ route('home') }}" style="color:#1565C0">Trang chủ</a> /
            <a href="{{ route('products.index') }}" style="color:#1565C0">Sản phẩm</a> /
            @if($product->category)
                <a href="{{ route('products.index', ['category' => $product->category_id]) }}" style="color:#1565C0">
                    {{ $product->category->name }}
                </a> /
            @endif
            <span style="color:#333">{{ $product->name }}</span>
        </div>
        <form action="{{ route('compare.add', $product) }}" method="POST" style="margin:0">
            @csrf
            <button type="submit" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-code-compare"></i>
                So sánh
            </button>
        </form>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div style="background:#E8F5E9;color:#2E7D32;padding:10px 14px;border-radius:6px;margin-bottom:16px;font-size:14px">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background:#FFEBEE;color:#C62828;padding:10px 14px;border-radius:6px;margin-bottom:16px;font-size:14px">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div style="background:#FFEBEE;color:#C62828;padding:10px 14px;border-radius:6px;margin-bottom:16px;font-size:14px">
            <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
        </div>
    @endif

    {{-- PRODUCT MAIN --}}
    <div class="product-main">

        {{-- Gallery --}}
        @php
            // Gộp thumbnail + album, thumbnail luôn đứng đầu
            $galleryImages = collect();
            if ($product->thumbnail) {
                $galleryImages->push($product->thumbnail);
            }
            foreach ($product->images ?? [] as $img) {
                if ($img !== $product->thumbnail) {
                    $galleryImages->push($img);
                }
            }
        @endphp
        <div class="gallery">
            <div class="gallery-main">
                @if($product->discount_percent > 0)
                    <span class="discount-tag">-{{ $product->discount_percent }}%</span>
                @endif
                @if($galleryImages->isNotEmpty())
                    <img src="{{ $galleryImages->first() }}" alt="{{ $product->name }}" id="mainImg">
                @else
                    <i class="fas fa-image fa-3x" style="color:#ccc"></i>
                @endif
            </div>
            <div class="gallery-thumbs">
                @foreach($galleryImages as $img)
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
                @if($product->variants->isEmpty())
                <span style="color:#ddd">|</span>
                <span style="color:#888">{{ number_format($product->stock) }} còn lại</span>
                @endif
            </div>

            {{-- Giá & tồn kho (cập nhật theo variant) --}}
            <div class="price-block" id="priceBlock">
                <span class="price-current" id="priceDisplay">{{ number_format($product->sale_price) }}đ</span>
                @if($product->discount_percent > 0)
                <span class="price-old" id="priceOldDisplay">{{ number_format($product->price) }}đ</span>
                <span class="price-pct" id="pricePctDisplay">-{{ $product->discount_percent }}%</span>
                @else
                <span class="price-old" id="priceOldDisplay" style="display:none"></span>
                <span class="price-pct" id="pricePctDisplay" style="display:none"></span>
                @endif
            </div>

            <div id="stockDisplay" style="margin:8px 0 16px">
                @if($product->stock > 0)
                    <div class="in-stock"><i class="fas fa-check-circle"></i> Còn hàng ({{ $product->stock }})</div>
                @else
                    <div class="out-of-stock"><i class="fas fa-times-circle"></i> Hết hàng</div>
                @endif
            </div>


            {{-- Chọn biến thể theo từng thuộc tính --}}
            @if($product->variants->isNotEmpty())
            <div id="variantSelector">
                {{-- Nhóm nút sẽ được render bởi JS --}}
            </div>

            {{-- Thông báo nếu không tìm được variant --}}
            <div id="variantAlert" style="display:none;color:#E53935;font-size:13px;margin-bottom:10px">
                <i class="fas fa-exclamation-triangle"></i> Phiên bản này hiện không có sẵn.
            </div>
            @endif

            {{-- Nút thêm giỏ / mua ngay --}}
            <div class="action-btns" id="actionBtns" style="display:none">
                <form action="{{ route('cart.add') }}" method="POST" style="flex:1" id="formAddCart">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="variant_id" id="inputVariantId" value="">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="btn-add-cart" style="width:100%" id="btnAddCart">
                        <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                    </button>
                </form>
                <form action="{{ route('cart.buy-now') }}" method="POST" style="flex:1" id="formBuyNow">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="variant_id" id="inputVariantIdBuy" value="">
                    <button type="submit" class="btn-buy-now" style="width:100%" id="btnBuyNow">
                        <i class="fas fa-bolt"></i> Mua ngay
                    </button>
                </form>
            </div>

            {{-- Sản phẩm đơn giản (không có variant): hiển thị nút trực tiếp --}}
            @if($product->variants->isEmpty())
            <div class="action-btns">
                @if($product->stock > 0)
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
                @else
                <button class="btn-add-cart" style="width:100%;opacity:.5;cursor:not-allowed" disabled>
                    <i class="fas fa-ban"></i> Hết hàng
                </button>
                @endif
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
                    <i class="fas fa-credit-card bi-icon"></i>
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
                <div class="commit-item"><i class="fas fa-award"></i> Bảo hành 12 tháng</div>
                <div class="commit-item"><i class="fas fa-headset"></i> Hỗ trợ 24/7: 1900 1234</div>
                <div class="commit-item"><i class="fas fa-map-marker-alt"></i> 50+ cửa hàng toàn quốc</div>
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
        <table class="specs-table" id="specsTable" style="{{ $product->attributes->isEmpty() ? 'display:none' : '' }}">
            <tbody id="specsTableBody">
                @foreach($product->attributes as $attr)
                <tr>
                    <td>{{ $attr->attribute->name }}</td>
                    <td>{{ $attr->value }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p id="specsEmptyMsg" style="color:#999;padding:24px 0;{{ $product->attributes->isNotEmpty() ? 'display:none' : '' }}">
            Chưa có thông số kỹ thuật.
        </p>
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

// ══════════════════════════════════════════════════════
// VARIANT SELECTION — chọn biến thể theo thuộc tính
// ══════════════════════════════════════════════════════

@php
$variantsForJs = $product->variants->map(function($v) {
    return [
        'id'               => $v->id,
        'price'            => (float) $v->price,
        'discount_percent' => (int)   $v->discount_percent,
        'stock'            => (int)   $v->stock,
        'is_active'        => (bool)  $v->is_active,
        // Chỉ những thuộc tính "chính" (is_variant = true) mới tạo nút chọn;
        // thuộc tính "phụ" chỉ hiện trong bảng thông số kỹ thuật (BASE_ATTRS).
        'attrs'            => $v->variantAttributes
            ->filter(fn($va) => $va->attribute->is_variant)
            ->mapWithKeys(function($va) {
                return [$va->attribute->name => $va->value];
            })->toArray(),
    ];
})->values()->toArray();
@endphp

@if($product->variants->isNotEmpty())
(function() {

// Dữ liệu variants từ PHP
const VARIANTS = {!! json_encode($variantsForJs, JSON_UNESCAPED_UNICODE) !!};

// Thông số kỹ thuật gốc của sản phẩm (product_attributes)
const BASE_ATTRS = @json(
    $product->attributes->mapWithKeys(fn($pa) => [$pa->attribute->name => $pa->value])
);

// Tên các thuộc tính "chính" (is_variant = true) - chỉ những thuộc tính này
// mới được dùng để tạo nút chọn; phần còn lại chỉ hiện trong bảng thông số.
const MAIN_ATTR_NAMES = @json(
    $product->attributes->pluck('attribute')->filter(fn($a) => $a && $a->is_variant)->pluck('name')->values()
);

// Sản phẩm gốc (fallback khi chưa chọn variant)
const BASE_PRICE    = {{ (float)$product->price }};
const BASE_DISCOUNT = {{ (int)$product->discount_percent }};
const BASE_STOCK    = {{ (int)$product->stock }};

// Format tiền VNĐ
function fmt(n) {
    return Math.round(n).toLocaleString('vi-VN') + 'đ';
}

function esc(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Base attrs dùng riêng cho việc dựng nút chọn - chỉ giữ thuộc tính "chính"
const BASE_MAIN_ATTRS = {};
MAIN_ATTR_NAMES.forEach(name => { if (BASE_ATTRS[name] !== undefined) BASE_MAIN_ATTRS[name] = BASE_ATTRS[name]; });

// ── Tất cả lựa chọn có thể có: option "gốc" (sản phẩm chính) + các variant ──
const ALL_OPTIONS = [
    { id: null, price: BASE_PRICE, discount_percent: BASE_DISCOUNT, stock: BASE_STOCK, is_active: true, attrs: BASE_MAIN_ATTRS },
    ...VARIANTS,
];

// Tính các thuộc tính thực sự khác nhau giữa các lựa chọn (chỉ những thuộc tính
// này mới cần hiển thị dưới dạng nút chọn, ví dụ: Màu sắc, Dung lượng...)
function getDiffKeys() {
    const keys = [];
    ALL_OPTIONS.forEach(o => {
        Object.keys(o.attrs).forEach(k => { if (!keys.includes(k)) keys.push(k); });
    });
    return keys.filter(key => {
        const vals = ALL_OPTIONS.map(o => o.attrs[key] ?? '');
        return new Set(vals).size > 1; // có ít nhất 2 giá trị khác nhau
    });
}
const DIFF_KEYS = getDiffKeys();

// Giá trị khả dụng (không trùng) cho từng thuộc tính, giữ đúng thứ tự xuất hiện
function valuesForKey(key) {
    const vals = [];
    ALL_OPTIONS.forEach(o => {
        const v = o.attrs[key];
        if (v && !vals.includes(v)) vals.push(v);
    });
    return vals;
}

// Trạng thái lựa chọn hiện tại: { "Màu sắc": "Đen", "Dung lượng": "128GB", ... }
const selectedAttrs = {};
DIFF_KEYS.forEach(key => { selectedAttrs[key] = BASE_ATTRS[key] ?? valuesForKey(key)[0]; });

// Tìm lựa chọn (base hoặc variant) khớp chính xác với tất cả thuộc tính đang chọn
function findMatchingOption() {
    return ALL_OPTIONS.find(o => DIFF_KEYS.every(key => (o.attrs[key] ?? '') === selectedAttrs[key])) || null;
}

// Một giá trị thuộc tính có khả dụng không (còn ít nhất 1 lựa chọn active + còn hàng chứa giá trị đó)
function isValueAvailable(key, value) {
    return ALL_OPTIONS.some(o => o.attrs[key] === value && o.is_active !== false && o.stock > 0);
}

// Áp dụng lên UI (giá, tồn kho, variant_id, thông số kỹ thuật)
function applyOption(option) {
    const alertEl    = document.getElementById('variantAlert');
    const actionBtns = document.getElementById('actionBtns');
    const addBtn      = document.getElementById('btnAddCart');
    const buyBtn      = document.getElementById('btnBuyNow');

    if (!option) {
        // Không có lựa chọn nào khớp với tổ hợp thuộc tính hiện tại
        if (alertEl) alertEl.style.display = '';
        if (actionBtns) actionBtns.style.display = 'none';
        renderSpecs(selectedAttrs);
        return;
    }

    if (alertEl) alertEl.style.display = 'none';
    if (actionBtns) actionBtns.style.display = '';

    ['inputVariantId', 'inputVariantIdBuy'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = option.id ?? '';
    });

    const soldOut = option.stock <= 0 || option.is_active === false;
    if (addBtn) { addBtn.disabled = soldOut; addBtn.style.opacity = soldOut ? '.5' : '1'; }
    if (buyBtn) { buyBtn.disabled = soldOut; buyBtn.style.opacity = soldOut ? '.5' : '1'; }

    renderPrice(option.price, option.discount_percent);
    renderStock(option.stock);
    renderSpecs(option.attrs);
}

// Cập nhật bảng "Thông số kỹ thuật" theo thuộc tính đang chọn
// (giữ nguyên các thông số cố định, ghi đè những thông số thay đổi theo biến thể)
function renderSpecs(currentAttrs) {
    const tbody   = document.getElementById('specsTableBody');
    const table   = document.getElementById('specsTable');
    const emptyEl = document.getElementById('specsEmptyMsg');
    if (!tbody) return;

    const merged = Object.assign({}, BASE_ATTRS, currentAttrs);
    const keys   = Object.keys(merged);

    if (keys.length === 0) {
        if (table) table.style.display = 'none';
        if (emptyEl) emptyEl.style.display = '';
        return;
    }

    tbody.innerHTML = keys.map(k => `<tr><td>${esc(k)}</td><td>${esc(merged[k])}</td></tr>`).join('');
    if (table) table.style.display = '';
    if (emptyEl) emptyEl.style.display = 'none';
}

// Build UI selector — mỗi thuộc tính (Màu sắc, Dung lượng...) hiển thị thành một nhóm nút riêng
const selector = document.getElementById('variantSelector');

if (selector && DIFF_KEYS.length > 0) {
    DIFF_KEYS.forEach(key => {
        const group = document.createElement('div');
        group.className = 'option-group';

        const label = document.createElement('div');
        label.className = 'option-label';
        label.innerHTML = `${esc(key)}: <strong id="lbl-${esc(key)}">${esc(selectedAttrs[key] ?? '')}</strong>`;
        group.appendChild(label);

        const btnWrap = document.createElement('div');
        btnWrap.className = 'option-btns';

        valuesForKey(key).forEach(value => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'opt-btn' + (selectedAttrs[key] === value ? ' active' : '');
            btn.textContent = value;

            if (!isValueAvailable(key, value)) {
                btn.classList.add('opt-btn-disabled');
                btn.title = 'Hết hàng / không khả dụng';
            }

            btn.addEventListener('click', function () {
                selectedAttrs[key] = value;
                btnWrap.querySelectorAll('.opt-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                document.getElementById(`lbl-${key}`).textContent = value;
                applyOption(findMatchingOption());
            });

            btnWrap.appendChild(btn);
        });

        group.appendChild(btnWrap);
        selector.appendChild(group);
    });
}

function renderPrice(price, discount) {
    const salePrice = price * (1 - discount / 100);
    document.getElementById('priceDisplay').textContent    = fmt(salePrice);
    const oldEl = document.getElementById('priceOldDisplay');
    const pctEl = document.getElementById('pricePctDisplay');
    if (discount > 0) {
        oldEl.textContent = fmt(price);
        pctEl.textContent = `-${discount}%`;
        oldEl.style.display = '';
        pctEl.style.display = '';
    } else {
        oldEl.textContent = '';
        pctEl.textContent = '';
        oldEl.style.display = 'none';
        pctEl.style.display = 'none';
    }
}

function renderStock(stock) {
    const el = document.getElementById('stockDisplay');
    if (!el) return;
    if (stock > 0) {
        el.innerHTML = `<div class="in-stock"><i class="fas fa-check-circle"></i> Còn hàng (${stock})</div>`;
    } else {
        el.innerHTML = `<div class="out-of-stock"><i class="fas fa-times-circle"></i> Hết hàng</div>`;
    }
}

// Khởi tạo: áp dụng tổ hợp thuộc tính mặc định (theo product_attributes gốc)
(function initVariant() {
    applyOption(findMatchingOption() || ALL_OPTIONS[0]);
})();


})(); // IIFE
@endif

</script>
@endpush