@extends('layouts.app')
@section('title', 'Tất cả sản phẩm - ElectronicShop')
@php $showSearch = true; @endphp

@push('styles')
<style>
.products-page { max-width: 1200px; margin: 0 auto; padding: 16px; }
.page-header-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px; }
.page-header-row h1 { font-size: 22px; font-weight: 800; }
.results-count { font-size: 13px; color: #888; margin-bottom: 16px; }

.layout-row { display: grid; grid-template-columns: 200px 1fr; gap: 24px; }

/* SIDEBAR */
.filter-sidebar { }
.filter-group { margin-bottom: 20px; }
.filter-group h3 { font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: .5px; color: #333; margin-bottom: 10px; }
.filter-group ul li { margin-bottom: 6px; }
.filter-group ul li a { font-size: 13px; color: #444; display: block; padding: 3px 0; }
.filter-group ul li a:hover, .filter-group ul li a.active { color: #1565C0; font-weight: 600; }
.price-check { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; cursor: pointer; }
.price-check input { cursor: pointer; accent-color: #1565C0; }
.price-check label { font-size: 13px; color: #444; cursor: pointer; }
.price-check .cnt { color: #aaa; font-size: 12px; }
.price-range { display: flex; align-items: center; gap: 6px; margin: 10px 0; }
.price-range input { flex: 1; border: 1px solid #e0e0e0; border-radius: 4px; padding: 6px 8px; font-size: 12px; outline: none; min-width: 0; }
.price-range span { font-size: 12px; color: #888; }
.attr-check { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; cursor: pointer; }
.attr-check input { cursor: pointer; accent-color: #1565C0; }
.attr-check label { font-size: 13px; color: #444; cursor: pointer; }
.attr-values-wrap { max-height: 180px; overflow-y: auto; padding-right: 4px; }
.btn-filter { width: 100%; padding: 8px; background: #1565C0; color: #fff; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; }
.btn-advanced-toggle { width: 100%; display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 9px 10px; background: #fff; border: 1px dashed #1565C0; border-radius: 6px; font-size: 13px; font-weight: 600; color: #1565C0; cursor: pointer; margin-bottom: 14px; transition: background .15s; }
.btn-advanced-toggle:hover { background: #f0f6fd; }
.btn-advanced-toggle .badge-count { background: #1565C0; color: #fff; font-size: 11px; font-weight: 700; border-radius: 10px; padding: 1px 7px; margin-left: auto; }

/* MODAL LỌC NÂNG CAO */
.advanced-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 1000; align-items: center; justify-content: center; padding: 20px; }
.advanced-overlay.open { display: flex; }
.advanced-modal { background: #fff; border-radius: 10px; width: 100%; max-width: 520px; max-height: 80vh; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 12px 40px rgba(0,0,0,.2); }
.advanced-modal-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid #eee; flex-shrink: 0; }
.advanced-modal-header h3 { font-size: 15px; font-weight: 800; margin: 0; }
.advanced-modal-close { border: none; background: none; font-size: 18px; color: #999; cursor: pointer; line-height: 1; padding: 4px; }
.advanced-modal-close:hover { color: #333; }
.advanced-modal-body { padding: 16px 20px; overflow-y: auto; flex: 1; }
.advanced-modal-body .filter-group:last-child { margin-bottom: 0; }
.advanced-modal-footer { padding: 14px 20px; border-top: 1px solid #eee; display: flex; gap: 10px; flex-shrink: 0; }
.advanced-modal-footer .btn-filter { margin: 0; }
.btn-advanced-clear { flex: 0 0 auto; padding: 8px 16px; background: #fff; color: #666; border: 1px solid #e0e0e0; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; }

/* PRODUCT GRID */
.sort-bar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
.sort-select { border: 1px solid #e0e0e0; padding: 7px 12px; border-radius: 6px; font-size: 13px; outline: none; }
.view-toggle { display: flex; gap: 4px; }
.view-btn { width: 32px; height: 32px; border: 1px solid #e0e0e0; border-radius: 4px; background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #aaa; font-size: 14px; }
.view-btn.active { background: #1565C0; color: #fff; border-color: #1565C0; }
.grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }

/* PRODUCT CARD */
.product-card { display: block; border: 1px solid #e8e8e8; border-radius: 8px; overflow: hidden; text-decoration: none; color: inherit; transition: box-shadow .2s; position: relative; background: #fff; }
.product-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.1); }
.product-card-img { height: 160px; background: #f5f5f5; display: flex; align-items: center; justify-content: center; overflow: hidden; }
.product-card-img img { width: 100%; height: 100%; object-fit: cover; }
.img-placeholder { color: #ccc; }
.product-card-body { padding: 10px; }
.product-card-name { font-size: 13px; font-weight: 600; margin-bottom: 4px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.product-card-price { font-size: 15px; font-weight: 800; color: #1565C0; }
.stars { font-size: 12px; color: #FFA000; margin-top: 4px; }
.review-count { color: #888; }
.wish { position: absolute; top: 8px; right: 8px; color: #ccc; font-size: 16px; z-index: 1; }
.badge-tag { position: absolute; top: 8px; left: 8px; background: #E53935; color: #fff; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 3px; z-index: 1; }

/* PAGINATION */
.pagination-wrap { display: flex; justify-content: center; margin-top: 32px; }
.pagination-wrap .pagination { display: flex; gap: 4px; }
.pagination-wrap .page-item .page-link { min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border: 1px solid #e0e0e0; border-radius: 6px; background: #fff; color: #333; font-size: 14px; text-decoration: none; transition: all .15s; }
.pagination-wrap .page-item.active .page-link { background: #1565C0; color: #fff; border-color: #1565C0; }
.pagination-wrap .page-item.disabled .page-link { color: #ccc; pointer-events: none; }
</style>
@endpush

@section('content')
<div class="products-page">
    {{-- Breadcrumb --}}
    <div class="breadcrumb" style="font-size:13px;color:#888;margin-bottom:12px">
        <a href="{{ route('home') }}" style="color:#1565C0">Trang chủ</a>
        <span> › </span>
        <span>Sản phẩm</span>
        @if(request('q'))
            <span> › </span>
            <span>Tìm: "{{ request('q') }}"</span>
        @endif
    </div>

    <div class="page-header-row">
        <h1>
            @if(request('q'))
                Kết quả tìm kiếm: "{{ request('q') }}"
            @else
                Tất cả sản phẩm
            @endif
        </h1>
        <div class="sort-bar" style="margin-bottom:0">
            <form id="sortForm" method="GET" action="{{ route('products.index') }}" style="display:flex;align-items:center;gap:8px">
                @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
                @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                @if(request('brand'))<input type="hidden" name="brand" value="{{ request('brand') }}">@endif

                <select class="sort-select" name="sort" onchange="document.getElementById('sortForm').submit()">
                    <option value="">Sắp xếp: Mặc định</option>
                    <option value="price_asc"  {{ request('sort')=='price_asc'  ? 'selected' : '' }}>Giá: Thấp đến cao</option>
                    <option value="price_desc" {{ request('sort')=='price_desc' ? 'selected' : '' }}>Giá: Cao đến thấp</option>
                    <option value="newest"     {{ request('sort')=='newest'     ? 'selected' : '' }}>Mới nhất</option>
                    <option value="rating"     {{ request('sort')=='rating'     ? 'selected' : '' }}>Đánh giá cao</option>
                </select>
            </form>
            <div class="view-toggle" style="margin-left:10px">
                <button class="view-btn active" id="gridView" title="Lưới"><i class="fas fa-th"></i></button>
                <button class="view-btn" id="listView" title="Danh sách"><i class="fas fa-list"></i></button>
            </div>
        </div>
    </div>

    <div class="results-count">
        Hiển thị {{ $products->firstItem() }}–{{ $products->lastItem() }} trong {{ number_format($totalProducts) }} sản phẩm
    </div>

    <div class="layout-row">
        {{-- SIDEBAR FILTER --}}
        <aside class="filter-sidebar">
            <form action="{{ route('products.index') }}" method="GET" id="filterForm">
                @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
                @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif
                @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                @if(request('brand'))<input type="hidden" name="brand" value="{{ request('brand') }}">@endif

                {{-- Lọc nâng cao theo thuộc tính kỹ thuật (dạng modal) --}}
                @if(count($attributesFilter))
                    @php
                        $selectedAttrCount = collect(request('attr', []))
                            ->flatten()
                            ->filter(fn ($v) => $v !== null && $v !== '')
                            ->count();
                    @endphp
                    <button type="button" id="advancedToggle" class="btn-advanced-toggle">
                        <span><i class="fas fa-sliders-h"></i> Lọc nâng cao</span>
                        @if($selectedAttrCount)
                            <span class="badge-count">{{ $selectedAttrCount }}</span>
                        @endif
                    </button>

                    {{-- Modal nổi giữa màn hình, không đẩy layout trang --}}
                    <div id="advancedOverlay" class="advanced-overlay">
                        <div class="advanced-modal">
                            <div class="advanced-modal-header">
                                <h3>Lọc nâng cao</h3>
                                <button type="button" class="advanced-modal-close" id="advancedClose">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="advanced-modal-body">
                                @foreach($attributesFilter as $attr)
                                <div class="filter-group">
                                    <h3>{{ $attr['name'] }}</h3>
                                    <div class="attr-values-wrap">
                                        @foreach($attr['values'] as $value)
                                        <label class="attr-check">
                                            <input type="checkbox" name="attr[{{ $attr['id'] }}][]" value="{{ $value }}"
                                                {{ in_array($value, request('attr.'.$attr['id'], [])) ? 'checked' : '' }}>
                                            <span>{{ $value }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="advanced-modal-footer">
                                <button type="button" class="btn-advanced-clear" id="advancedClear">Bỏ chọn</button>
                                <button type="submit" class="btn-filter">Áp dụng</button>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Danh mục --}}
                <div class="filter-group">
                    <h3>Danh mục</h3>
                    <ul>
                        <li>
                            <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}"
                               class="{{ !request('category') ? 'active' : '' }}">
                                Tất cả
                            </a>
                        </li>
                        @foreach($categories as $cat)
                        <li>
                            <a href="{{ request()->fullUrlWithQuery(['category' => $cat->id]) }}"
                               class="{{ request('category') == $cat->id ? 'active' : '' }}">
                                {{ $cat->name }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Thương hiệu --}}
                <div class="filter-group">
                    <h3>Thương hiệu</h3>
                    <ul>
                        @foreach($brands as $brand)
                        <li>
                            <a href="{{ request()->fullUrlWithQuery(['brand' => $brand->id]) }}"
                               class="{{ request('brand') == $brand->id ? 'active' : '' }}">
                                {{ $brand->name }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Khoảng giá --}}
                <div class="filter-group">
                    <h3>Khoảng giá</h3>
                    @foreach([
                        ['Dưới 5 triệu',    '0',        '5000000'],
                        ['5 – 10 triệu',    '5000000',  '10000000'],
                        ['10 – 20 triệu',   '10000000', '20000000'],
                        ['20 – 30 triệu',   '20000000', '30000000'],
                        ['Trên 30 triệu',   '30000000', ''],
                    ] as $range)
                    <label class="price-check">
                        <input type="checkbox" name="price[]"
                               value="{{ $range[1] }}_{{ $range[2] }}"
                               {{ in_array($range[1].'_'.$range[2], request('price', [])) ? 'checked' : '' }}>
                        <span>{{ $range[0] }}</span>
                    </label>
                    @endforeach

                    <div class="price-range">
                        <input type="number" name="price_from" placeholder="Từ" value="{{ request('price_from') }}">
                        <span>–</span>
                        <input type="number" name="price_to" placeholder="Đến" value="{{ request('price_to') }}">
                    </div>
                </div>


                <div class="filter-group">
                    <button type="submit" class="btn-filter">Lọc ngay</button>
                </div>
            </form>
        </aside>

        {{-- PRODUCT GRID --}}
        <div>
            @if($products->isEmpty())
                <div style="text-align:center;padding:60px 0;color:#999">
                    <i class="fas fa-search fa-3x" style="margin-bottom:16px;opacity:.3"></i>
                    <p>Không tìm thấy sản phẩm phù hợp.</p>
                    <a href="{{ route('products.index') }}" style="color:#1565C0">Xem tất cả sản phẩm</a>
                </div>
            @else
            <div class="grid-4" id="productsGrid">
                @foreach($products as $product)
                <a href="{{ route('products.show', $product->slug) }}" class="product-card">
                    @if($product->discount_percent > 0)
                        <span class="badge-tag">-{{ $product->discount_percent }}%</span>
                    @endif
                    <span class="wish"><i class="far fa-heart"></i></span>
                    <div class="product-card-img">
                        @if($product->first_image)
                            <img src="{{ $product->first_image }}" alt="{{ $product->name }}" loading="lazy">
                        @else
                            <i class="fas fa-image fa-2x img-placeholder"></i>
                        @endif
                    </div>
                    <div class="product-card-body">
                        <div class="product-card-name">{{ $product->name }}</div>
                        <div>
                            <span class="product-card-price">{{ number_format($product->sale_price) }}đ</span>
                            @if($product->discount_percent > 0)
                                <span style="font-size:12px;color:#aaa;text-decoration:line-through;margin-left:4px">
                                    {{ number_format($product->price) }}đ
                                </span>
                            @endif
                        </div>
                        <div class="stars">
                            @for($i=1;$i<=5;$i++)
                                {{ $i <= round($product->avg_rating) ? '★' : '☆' }}
                            @endfor
                            <span class="review-count">({{ $product->reviews_count }})</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            {{-- PAGINATION --}}
            <div class="pagination-wrap">
                {{ $products->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('gridView').addEventListener('click', function() {
    this.classList.add('active');
    document.getElementById('listView').classList.remove('active');
    document.getElementById('productsGrid').style.gridTemplateColumns = 'repeat(4, 1fr)';
});
document.getElementById('listView').addEventListener('click', function() {
    this.classList.add('active');
    document.getElementById('gridView').classList.remove('active');
    document.getElementById('productsGrid').style.gridTemplateColumns = '1fr';
});

const advancedToggle = document.getElementById('advancedToggle');
const advancedOverlay = document.getElementById('advancedOverlay');
const advancedClose = document.getElementById('advancedClose');
const advancedClear = document.getElementById('advancedClear');

if (advancedToggle && advancedOverlay) {
    advancedToggle.addEventListener('click', function() {
        advancedOverlay.classList.add('open');
    });
    advancedClose.addEventListener('click', function() {
        advancedOverlay.classList.remove('open');
    });
    // Bấm ra ngoài modal để đóng
    advancedOverlay.addEventListener('click', function(e) {
        if (e.target === advancedOverlay) {
            advancedOverlay.classList.remove('open');
        }
    });
    // Nhấn ESC để đóng
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            advancedOverlay.classList.remove('open');
        }
    });
    advancedClear.addEventListener('click', function() {
        advancedOverlay.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
    });
}
</script>
@endpush