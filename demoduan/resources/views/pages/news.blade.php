{{-- ============================================ --}}
{{-- FILE: resources/views/pages/news.blade.php --}}
{{-- ============================================ --}}
@extends('layouts.app')
@section('title', 'Tin tức - ElectronicShop')
@php $showSearch = true; @endphp

@push('styles')
<style>
.news-page { max-width: 1200px; margin: 0 auto; padding: 24px 16px; }
.news-page h1 { font-size: 22px; font-weight: 800; text-transform: uppercase; margin-bottom: 4px; }
.news-page .subtitle { font-size: 14px; color: #888; margin-bottom: 24px; }
.news-layout { display: grid; grid-template-columns: 1fr 280px; gap: 28px; }

/* FEATURED */
.news-featured {
    border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;
    display: grid; grid-template-columns: 240px 1fr; margin-bottom: 0;
}
.news-featured .featured-img {
    background: #e8e8e8; display: flex; align-items: center; justify-content: center; color: #bbb; min-height: 200px;
}
.news-featured .featured-img img { width: 100%; height: 100%; object-fit: cover; }
.news-featured .featured-body { padding: 20px; display: flex; flex-direction: column; justify-content: center; }
.news-category { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #1565C0; margin-bottom: 8px; background: #EBF3FF; display: inline-block; padding: 2px 8px; border-radius: 4px; }
.featured-title { font-size: 20px; font-weight: 700; line-height: 1.3; margin-bottom: 10px; }
.featured-excerpt { font-size: 13px; color: #666; line-height: 1.6; margin-bottom: 12px; }
.news-meta { font-size: 12px; color: #aaa; display: flex; align-items: center; gap: 12px; }
.news-meta i { font-size: 11px; }
.read-more { display: inline-block; color: #1565C0; font-size: 13px; font-weight: 500; margin-top: 8px; }
.read-more:hover { text-decoration: underline; }

/* NEWS LIST */
.news-list { margin-top: 0; }
.news-list-item {
    border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;
    display: grid; grid-template-columns: 240px 1fr; margin-bottom: 14px; transition: box-shadow .2s;
}
.news-list-item:hover { box-shadow: 0 4px 12px rgba(0,0,0,.08); }
.news-list-item .item-img {
    background: #e8e8e8; display: flex; align-items: center; justify-content: center;
    color: #bbb; min-height: 160px;
}
.news-list-item .item-img img { width: 100%; height: 100%; object-fit: cover; }
.news-list-item .item-body { padding: 16px; display: flex; flex-direction: column; justify-content: center; }
.item-title { font-size: 16px; font-weight: 700; line-height: 1.35; margin-bottom: 6px; color: #1a1a1a; }
.item-excerpt { font-size: 13px; color: #666; line-height: 1.6; margin-bottom: 8px; }
.item-meta { font-size: 12px; color: #aaa; }

/* SIDEBAR */
.news-sidebar { }
.sidebar-box { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 16px; margin-bottom: 16px; }
.sidebar-box h3 { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #1565C0; margin-bottom: 14px; }
.sidebar-newsletter p { font-size: 13px; color: #666; margin-bottom: 10px; }
.sidebar-newsletter input {
    width: 100%; border: 1px solid #e0e0e0; border-radius: 6px; padding: 9px 12px;
    font-size: 13px; margin-bottom: 8px; outline: none;
}
.sidebar-newsletter input:focus { border-color: #1565C0; }
.sidebar-newsletter button {
    width: 100%; background: #1565C0; color: #fff; border: none; border-radius: 6px;
    padding: 10px; font-size: 13px; font-weight: 700; cursor: pointer;
    transition: background .2s; text-transform: uppercase;
}
.sidebar-newsletter button:hover { background: #0D47A1; }

/* PAGINATION */
.pagination { display: flex; justify-content: center; gap: 6px; margin-top: 28px; }
.page-btn { min-width: 36px; height: 36px; border: 1px solid #e0e0e0; border-radius: 6px; background: #fff; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center; color: #333; text-decoration: none; font-weight: 500; transition: all .15s; }
.page-btn:hover { border-color: #1565C0; color: #1565C0; }
.page-btn.active { background: #1565C0; color: #fff; border-color: #1565C0; }
</style>
@endpush

@section('content')
<div class="news-page">
    <h1>Tin tức</h1>
    <p class="subtitle">Cập nhật những thông tin mới nhất về sản phẩm, công nghệ và xu hướng.</p>

    <div class="news-layout">
        <div>
            {{-- FEATURED --}}
            @if(isset($featuredNews))
            <a href="{{ route('news.show', $featuredNews->slug) }}" class="news-featured" style="display:grid;text-decoration:none;color:inherit;margin-bottom:14px">
                <div class="featured-img">
                    @if($featuredNews->thumbnail)<img src="{{ $featuredNews->thumbnail }}" alt="">@else<i class="fas fa-image fa-2x"></i>@endif
                </div>
                <div class="featured-body">
                    <span class="news-category">{{ $featuredNews->category->name ?? 'CÔNG NGHỆ' }}</span>
                    <div class="featured-title">{{ $featuredNews->title }}</div>
                    <div class="featured-excerpt">{{ Str::limit($featuredNews->excerpt, 120) }}</div>
                    <div class="news-meta">
                        <span><i class="fas fa-user"></i> {{ $featuredNews->author ?? 'Admin' }}</span>
                        <span><i class="fas fa-calendar"></i> {{ optional($featuredNews->created_at)->format('d/m/Y') }}</span>
                    </div>
                    <span class="read-more">Đọc tiếp →</span>
                </div>
            </a>
            @else
            <a href="{{ route('news.show', 'danh-gia-iphone-15-pro-max') }}" class="news-featured" style="display:grid;text-decoration:none;color:inherit;margin-bottom:14px">
                <div class="featured-img"><i class="fas fa-image fa-2x"></i></div>
                <div class="featured-body">
                    <span class="news-category">CÔNG NGHỆ</span>
                    <div class="featured-title">Đánh giá chi tiết iPhone 15 Pro Max: Titan mạnh mẽ, camera đột phá</div>
                    <div class="featured-excerpt">Khám phá thiết kế titan mạnh mẽ và những nâng cấp camera ấn tượng trên iPhone 15 Pro Max.</div>
                    <div class="news-meta">
                        <span><i class="fas fa-user"></i> Admin</span>
                        <span><i class="fas fa-calendar"></i> 20/05/2024</span>
                    </div>
                    <span class="read-more">Đọc tiếp →</span>
                </div>
            </a>
            @endif

            {{-- NEWS LIST --}}
            @forelse($newsList ?? [] as $news)
            <a href="{{ route('news.show', $news->slug) }}" class="news-list-item" style="display:grid;text-decoration:none;color:inherit">
                <div class="item-img">
                    @if($news->thumbnail)<img src="{{ $news->thumbnail }}" alt="">@else<i class="fas fa-image fa-2x"></i>@endif
                </div>
                <div class="item-body">
                    <span class="news-category">{{ $news->category->name ?? 'TIN TỨC' }}</span>
                    <div class="item-title">{{ $news->title }}</div>
                    <div class="item-excerpt">{{ Str::limit($news->excerpt, 100) }}</div>
                    <div class="item-meta">Admin · {{ optional($news->created_at)->format('d/m/Y') }}</div>
                </div>
            </a>
        @empty
        @foreach([
            ['ĐÁNH GIÁ','Trên tay Samsung Galaxy S24 Ultra: Đỉnh cao mới của Android','Galaxy S24 Ultra mang đến nhiều cải tiến vượt trội về hiệu năng, camera và trí tuệ nhân tạo Galaxy AI.','18/05/2024','tren-tay-samsung-galaxy-s24-ultra'],
            ['TIN TỨC','Xiaomi 14 Series chính thức ra mắt: Hiệu năng mạnh mẽ, giá hấp dẫn','Xiaomi 14 Series đã chính thức ra mắt với nhiều nâng cấp đáng giá về hiệu năng và camera hợp tác cùng Leica.','11/05/2024','xiaomi-14-series-ra-mat'],
            ['HƯỚNG DẪN','5 mẹo tiết kiệm pin cực hay cho Android bạn nên biết','Tối ưu hóa cài đặt hệ thống giúp điện thoại của bạn duy trì thời lượng pin lâu hơn.','12/05/2024','meo-tiet-kiem-pin-android'],
            ['TIN TỨC','Apple ra mắt AirPods 4: Nâng cấp âm thanh và chống ồn chủ động','AirPods 4 sở hữu thiết kế mới, chất âm được cải thiện và tính năng chống ồn chủ động ấn tượng.','10/05/2024','apple-airpods-4-ra-mat'],
        ] as $n)
        <a href="{{ route('news.show', $n[4]) }}" class="news-list-item" style="display:grid;text-decoration:none;color:inherit">
            <div class="item-img"><i class="fas fa-image fa-2x"></i></div>
            <div class="item-body">
                <span class="news-category">{{ $n[0] }}</span>
                <div class="item-title">{{ $n[1] }}</div>
                <div class="item-excerpt">{{ $n[2] }}</div>
                <div class="item-meta">Admin · {{ $n[3] }}</div>
            </div>
        </a>
        @endforeach
        @endforelse

            {{-- PAGINATION --}}
            <div class="pagination">
                @if(isset($newsList) && $newsList instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    {{ $newsList->links() }}
                @else
                @foreach(range(1,5) as $pg)
                <a href="#" class="page-btn {{ $pg===1?'active':'' }}">{{ $pg }}</a>
                @endforeach
                <span style="color:#aaa;padding:0 4px">...</span>
                <a href="#" class="page-btn">10</a>
                <a href="#" class="page-btn"><i class="fas fa-chevron-right"></i></a>
                @endif
            </div>
        </div>

        {{-- SIDEBAR --}}
        <div class="news-sidebar">
            <div class="sidebar-box sidebar-newsletter">
                <h3>Đăng ký nhận tin</h3>
                <p>Nhận những thông tin và chương trình khuyến mãi mới nhất từ PHONE mỗi ngày.</p>
                <input type="email" placeholder="Nhập email của bạn">
                <button>Đăng ký</button>
            </div>
        </div>
    </div>
</div>
@endsection