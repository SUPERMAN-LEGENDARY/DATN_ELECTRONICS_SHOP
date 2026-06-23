@extends('layouts.app')
@section('title', 'Tất cả sản phẩm - ElectronicShop')
@php $showSearch = true; @endphp

@push('styles')
<style>
.products-page { max-width: 1200px; margin: 0 auto; padding: 16px; }
.page-header-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px; }
.page-header-row h1 { font-size: 22px; font-weight: 800; }
.results-count { font-size: 13px; color: #888; margin-bottom: 16px; }
.nav-active { color: #1565C0 !important; font-weight: 700; }

.layout-row { display: grid; grid-template-columns: 200px 1fr; gap: 24px; }

/* SIDEBAR FILTER */
.filter-sidebar { }
.filter-group { margin-bottom: 20px; }
.filter-group h3 { font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: .5px; color: #333; margin-bottom: 10px; }
.filter-group ul { }
.filter-group ul li { margin-bottom: 6px; }
.filter-group ul li a { font-size: 13px; color: #444; display: block; padding: 3px 0; }
.filter-group ul li a:hover { color: #1565C0; }
.filter-group ul li a.active { color: #1565C0; font-weight: 600; }
.price-check { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; cursor: pointer; }
.price-check input { cursor: pointer; accent-color: #1565C0; }
.price-check label { font-size: 13px; color: #444; cursor: pointer; }
.price-check .cnt { color: #aaa; font-size: 12px; }
.price-range { display: flex; align-items: center; gap: 6px; margin-top: 10px; margin-bottom: 10px; }
.price-range input {
    flex: 1; border: 1px solid #e0e0e0; border-radius: 4px; padding: 6px 8px;
    font-size: 12px; outline: none; min-width: 0;
}
.price-range span { font-size: 12px; color: #888; }
.btn-filter { width: 100%; padding: 8px; background: #1565C0; color: #fff; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; }

/* PRODUCT GRID */
.sort-bar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
.sort-select { border: 1px solid #e0e0e0; padding: 7px 12px; border-radius: 6px; font-size: 13px; outline: none; }
.view-toggle { display: flex; gap: 4px; }
.view-btn {
    width: 32px; height: 32px; border: 1px solid #e0e0e0; border-radius: 4px;
    background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center;
    color: #aaa; font-size: 14px;
}
.view-btn.active { background: #1565C0; color: #fff; border-color: #1565C0; }
.grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
.product-card-img { height: 160px; }

/* PAGINATION */
.pagination { display: flex; justify-content: center; align-items: center; gap: 6px; margin-top: 32px; }
.page-btn {
    min-width: 36px; height: 36px; border: 1px solid #e0e0e0; border-radius: 6px;
    background: #fff; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center;
    color: #333; text-decoration: none; font-weight: 500; transition: all .15s;
}
.page-btn:hover { border-color: #1565C0; color: #1565C0; }
.page-btn.active { background: #1565C0; color: #fff; border-color: #1565C0; }
.page-btn.disabled { color: #ccc; cursor: not-allowed; }
</style>
@endpush

@section('content')
<div class="products-page">
    <div class="breadcrumb">
        <a href="{{ route('home') }}">Trang chủ</a>
        <span>›</span>
        <span>Sản phẩm</span>
    </div>

    <div class="page-header-row">
        <h1>Tất cả sản phẩm</h1>
        <div class="sort-bar" style="margin-bottom:0">
            <select class="sort-select" name="sort" onchange="this.form && this.form.submit()">
                <option value="">Sắp xếp: Mặc định</option>
                <option value="price_asc" {{ request('sort')=='price_asc'?'selected':'' }}>Giá: Thấp đến cao</option>
                <option value="price_desc" {{ request('sort')=='price_desc'?'selected':'' }}>Giá: Cao đến thấp</option>
                <option value="newest" {{ request('sort')=='newest'?'selected':'' }}>Mới nhất</option>
                <option value="best_seller" {{ request('sort')=='best_seller'?'selected':'' }}>Bán chạy nhất</option>
                <option value="rating" {{ request('sort')=='rating'?'selected':'' }}>Đánh giá cao</option>
            </select>
            <div class="view-toggle" style="margin-left:10px">
                <button class="view-btn active" id="gridView"><i class="fas fa-th"></i></button>
                <button class="view-btn" id="listView"><i class="fas fa-list"></i></button>
            </div>
        </div>
    </div>
    <div class="results-count">Hiển thị 1–24 của {{ $totalProducts ?? 128 }} kết quả</div>

    <div class="layout-row">
        {{-- FILTER SIDEBAR --}}
        <aside class="filter-sidebar">
            <form action="{{ route('products.index') }}" method="GET">
                @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif

                <div class="filter-group">
                    <h3>Thương hiệu</h3>
                    <ul>
                        @foreach(['Apple','Samsung','Xiaomi','OPPO','vivo','Realme'] as $brand)
                        <li><a href="#" class="{{ request('brand') == strtolower($brand) ? 'active' : '' }}">{{ $brand }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div class="filter-group">
                    <h3>Khoảng giá</h3>
                    @foreach([
                        ['Dưới 5 triệu','0','5000000','24'],
                        ['5 - 10 triệu','5000000','10000000','35'],
                        ['10 - 20 triệu','10000000','20000000','40'],
                        ['20 - 30 triệu','20000000','30000000','15'],
                        ['Trên 30 triệu','30000000','','14'],
                    ] as $range)
                    <label class="price-check">
                        <input type="checkbox" name="price[]" value="{{ $range[1] }}_{{ $range[2] }}"
                            {{ in_array($range[1].'_'.$range[2], request('price', [])) ? 'checked' : '' }}>
                        <label>{{ $range[0] }}</label>
                        <span class="cnt">({{ $range[3] }})</span>
                    </label>
                    @endforeach
                    <div class="price-range">
                        <input type="number" name="price_from" placeholder="Từ" value="{{ request('price_from') }}">
                        <span>đ</span>
                        <input type="number" name="price_to" placeholder="Đến" value="{{ request('price_to') }}">
                        <span>đ</span>
                    </div>
                    <button type="submit" class="btn-filter">Lọc</button>
                </div>
            </form>
        </aside>

        {{-- PRODUCTS --}}
        <div>
            <div class="grid-4" id="productsGrid">
                @forelse($products ?? [] as $product)
                <a href="{{ route('products.show', $product->slug) }}" class="product-card">
                    <span class="wish"><i class="far fa-heart"></i></span>
                    <div class="product-card-img img-placeholder"><i class="fas fa-image"></i></div>
                    <div class="product-card-body">
                        <div class="product-card-name">{{ $product->name }}</div>
                        <div><span class="product-card-price">{{ number_format($product->price) }}đ</span></div>
                        <div class="stars">★★★★★ <span class="review-count">({{ $product->reviews_count }})</span></div>
                    </div>
                </a>
                @empty
                @foreach([
                    ['iPhone 15 Pro Max 256GB Titan','29.990.000','128'],
                    ['Samsung Galaxy S24 Ultra 5G','24.990.000','95'],
                    ['Xiaomi 14 5G 256GB','16.990.000','76'],
                    ['OPPO Reno11 5G','9.990.000','47'],
                    ['vivo V30 5G','10.990.000','58'],
                    ['iPhone 15 128GB','22.990.000','89'],
                    ['Samsung Galaxy S24 5G','19.990.000','67'],
                    ['Xiaomi 13T Pro 5G','13.990.000','45'],
                ] as $p)
                <a href="{{ route('products.show', 'demo-product-'.$loop->index) }}" class="product-card">
                    <span class="wish"><i class="far fa-heart"></i></span>
                    <div class="product-card-img img-placeholder"><i class="fas fa-image"></i></div>
                    <div class="product-card-body">
                        <div class="product-card-name">{{ $p[0] }}</div>
                        <div><span class="product-card-price">{{ $p[1] }}đ</span></div>
                        <div class="stars">★★★★★ <span class="review-count">({{ $p[2] }})</span></div>
                    </div>
                </a>
                @endforeach
                @endforelse
            </div>

            {{-- PAGINATION --}}
            <div class="pagination">
                <a href="#" class="page-btn disabled"><i class="fas fa-chevron-left"></i></a>
                @if(isset($products) && $products instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    {{ $products->links() }}
                @else
                @foreach(range(1,6) as $pg)
                <a href="#" class="page-btn {{ $pg===1?'active':'' }}">{{ $pg }}</a>
                @endforeach
                <span style="color:#aaa;font-size:13px">...</span>
                <a href="#" class="page-btn">6</a>
                <a href="#" class="page-btn"><i class="fas fa-chevron-right"></i></a>
                @endif
            </div>
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
</script>
@endpush