@extends('layouts.app')

@section('title', $article->title . ' - ElectronicShop')

@php
$showSearch = true;
@endphp

@section('content')

<section class="news-detail">

    <div class="container">

        {{-- Breadcrumb --}}
        <div class="breadcrumb" style="display:block">
            <a href="{{ route('home') }}">Trang chủ</a> /
            <a href="{{ route('news.index') }}">Tin tức</a> /
            <span>{{ \Illuminate\Support\Str::limit($article->title, 60) }}</span>
        </div>

        <div class="detail-layout">

            <article class="article-content">

                <h1>{{ $article->title }}</h1>

                <div class="article-date">

                    @if($article->published_at)
                    {{ $article->published_at->format('d/m/Y') }}
                    @endif

                    · {{ number_format($article->views ?? 0) }} lượt xem

                    @if($article->category)
                    · {{ $article->category->name }}
                    @endif

                    @if($article->author)
                    · {{ $article->author->name }}
                    @endif

                </div>

                @if($article->thumbnail)
                <div class="article-image-wrap">
                    <img
                        class="article-image"
                        src="{{ asset('storage/' . $article->thumbnail) }}"
                        alt="{{ $article->title }}">
                </div>
                @endif

                <div class="article-text">
                    {!! $article->content !!}
                </div>

            </article>

            <aside class="article-sidebar">

                <div class="related-box">

                    <h3>TIN LIÊN QUAN</h3>

                    @forelse($relatedNews as $item)

                    <div class="related-item">

                        @if($item->thumbnail)
                        <img src="{{ asset('storage/' . $item->thumbnail) }}" alt="{{ $item->title }}">
                        @else
                        <div class="related-thumb-placeholder"><i class="fas fa-image"></i></div>
                        @endif

                        <div>
                            <a href="{{ route('news.show', $item->slug) }}">
                                {{ \Illuminate\Support\Str::limit($item->title, 50) }}
                            </a>
                            <span>
                                @if($item->published_at)
                                {{ $item->published_at->format('d/m/Y') }}
                                @endif
                            </span>
                        </div>

                    </div>

                    @empty

                    <p>Chưa có bài viết liên quan.</p>

                    @endforelse

                </div>

                @if($latestNews->count())
                <div class="related-box">

                    <h3>TIN MỚI NHẤT</h3>

                    @foreach($latestNews as $item)

                    <div class="related-item">

                        @if($item->thumbnail)
                        <img src="{{ asset('storage/' . $item->thumbnail) }}" alt="{{ $item->title }}">
                        @else
                        <div class="related-thumb-placeholder"><i class="fas fa-image"></i></div>
                        @endif

                        <div>
                            <a href="{{ route('news.show', $item->slug) }}">
                                {{ \Illuminate\Support\Str::limit($item->title, 50) }}
                            </a>
                            <span>
                                @if($item->published_at)
                                {{ $item->published_at->format('d/m/Y') }}
                                @endif
                            </span>
                        </div>

                    </div>

                    @endforeach

                </div>
                @endif

                <div class="subscribe-box">

                    <h3>ĐĂNG KÝ NHẬN TIN</h3>

                    <p>Nhận thông tin khuyến mãi mới nhất từ ElectronicShop</p>

                    <input type="email" placeholder="Email của bạn">

                    <button>ĐĂNG KÝ</button>

                </div>

            </aside>

        </div>

    </div>

</section>

@endsection

<style>
    .news-detail {
        background: #fff;
        padding: 30px 0;
    }

    .breadcrumb {
        font-size: 13px;
        color: #888;
        margin-bottom: 20px;
    }

    .breadcrumb a {
        color: #2563eb;
        text-decoration: none;
    }

    .breadcrumb span {
        color: #333;
    }

    .detail-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 300px;
        gap: 25px;
    }

    .article-content {
        border: 1px solid #edf2f7;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,.04);
        min-width: 0;
        overflow-wrap: break-word;
        word-break: break-word;
    }

    .article-content h1 {
        font-size: 24px;
        font-weight: 800;
        color: #111827;
        line-height: 1.4;
        margin-bottom: 10px;
    }

    .article-date {
        font-size: 13px;
        color: #9ca3af;
        margin-bottom: 20px;
    }

    .article-image-wrap {
        width: 100%;
        max-height: 500px;
        background: #fff;
        border-radius: 6px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .article-image {
        width: 100%;
        max-height: 500px;
        object-fit: contain;
    }

    .article-text {
        font-size: 15px;
        line-height: 1.8;
        color: #444;
        max-width: 100%;
        overflow-wrap: break-word;
        word-break: break-word;
    }

    .article-text img,
    .article-text video,
    .article-text iframe {
        max-width: 100%;
        height: auto;
    }

    .article-text table {
        display: block;
        max-width: 100%;
        overflow-x: auto;
    }

    .article-text pre,
    .article-text code {
        max-width: 100%;
        overflow-x: auto;
        white-space: pre-wrap;
        word-break: break-word;
    }

    .article-text a {
        overflow-wrap: break-word;
        word-break: break-word;
    }

    .article-text h2 {
        font-size: 18px;
        font-weight: 700;
        margin-top: 25px;
    }

    .related-box,
    .subscribe-box {
        border: 1px solid #edf2f7;
        border-radius: 12px;
        padding: 18px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,.04);
    }

    .related-box h3,
    .subscribe-box h3 {
        font-size: 15px;
        font-weight: 800;
        margin-bottom: 15px;
    }

    .related-item {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
    }

    .related-item:last-child {
        margin-bottom: 0;
    }

    .related-item img,
    .related-thumb-placeholder {
        width: 70px;
        height: 55px;
        object-fit: contain;
        background: #fff;
        flex-shrink: 0;
        border-radius: 8px;
        padding: 2px;
        box-sizing: border-box;
    }

    .related-thumb-placeholder {
        background: #edf2f7;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #cbd5e1;
    }

    .related-item a {
        font-size: 13px;
        font-weight: 600;
        color: #111;
        text-decoration: none;
        display: block;
    }

    .related-item span {
        font-size: 11px;
        color: #999;
    }

    .subscribe-box input {
        width: 100%;
        height: 40px;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 0 10px;
        margin-bottom: 10px;
        box-sizing: border-box;
        outline: none;
    }

    .subscribe-box input:focus {
        border-color: #2563eb;
    }

    .subscribe-box button {
        width: 100%;
        height: 40px;
        background: #2563eb;
        border: none;
        border-radius: 6px;
        color: white;
        font-weight: bold;
        cursor: pointer;
        transition: background .15s;
    }

    .subscribe-box button:hover {
        background: #1d4ed8;
    }

    @media(max-width:900px) {
        .detail-layout {
            grid-template-columns: 1fr;
        }
    }
</style>