@extends('layouts.app')
@section('title', $article->title . ' - ElectronicShop')
@php $showSearch = true; @endphp

@push('styles')
<style>
    /* ============================================================
       SAMSUNG UI GIAO DIỆN MỚI — News Detail (đồng bộ với news-index)
       ============================================================ */
    .samsung-news-detail {
        color: #111;
        background: #fff;
        font-family: Arial, Helvetica, sans-serif;
        padding: 40px 0 90px;
    }

    .news-detail-container {
        width: min(1280px, 88%);
        margin: auto;
    }

    /* ============================================================
       BREADCRUMB
       ============================================================ */
    .breadcrumb-row {
        font-size: 13px;
        color: #777;
        margin-bottom: 28px;
    }

    .breadcrumb-row a {
        color: #555;
        font-weight: 600;
        text-decoration: none;
    }

    .breadcrumb-row a:hover {
        color: #000;
        text-decoration: underline;
    }

    .breadcrumb-row span {
        color: #999;
    }

    /* ============================================================
       LAYOUT GRID
       ============================================================ */
    .detail-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 320px;
        gap: 48px;
        align-items: start;
    }

    @media (max-width: 900px) {
        .detail-layout {
            grid-template-columns: 1fr;
        }
    }

    /* ============================================================
       ARTICLE CONTENT
       ============================================================ */
    .article-content {
        min-width: 0;
        overflow-wrap: break-word;
        word-break: break-word;
    }

    .article-content h1 {
        font-size: clamp(26px, 3.6vw, 40px);
        font-weight: 700;
        color: #000;
        line-height: 1.2;
        letter-spacing: -1px;
        margin: 0 0 16px;
    }

    .article-date {
        font-size: 13px;
        color: #777;
        margin-bottom: 28px;
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
        padding-bottom: 20px;
        border-bottom: 1px solid #ddd;
    }

    /* ============================================================
       ARTICLE HERO IMAGE
       ============================================================ */
    .article-image-wrap {
        width: 100%;
        max-height: 480px;
        background: #f2f2f2;
        border-radius: 16px;
        margin-bottom: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .article-image {
        width: 100%;
        max-height: 480px;
        object-fit: contain;
    }

    /* ============================================================
       ARTICLE BODY TEXT
       ============================================================ */
    .article-text {
        font-size: 16px;
        line-height: 1.75;
        color: #333;
        max-width: 100%;
        overflow-wrap: break-word;
        word-break: break-word;
    }

    .article-text h2 {
        font-size: 22px;
        font-weight: 700;
        color: #000;
        margin-top: 32px;
        margin-bottom: 12px;
    }

    .article-text h3 {
        font-size: 18px;
        font-weight: 700;
        color: #000;
        margin-top: 24px;
        margin-bottom: 10px;
    }

    .article-text p {
        margin-bottom: 16px;
    }

    .article-text a {
        color: #1428a0;
        font-weight: 600;
        overflow-wrap: break-word;
        word-break: break-word;
    }

    .article-text a:hover {
        text-decoration: underline;
    }

    .article-text blockquote {
        border-left: 3px solid #000;
        background: #f5f5f5;
        border-radius: 0 12px 12px 0;
        margin: 20px 0;
        padding: 14px 20px;
        color: #444;
        font-style: italic;
    }

    .article-text img,
    .article-text video,
    .article-text iframe {
        max-width: 100%;
        height: auto;
        border-radius: 12px;
    }

    .article-text table {
        display: block;
        max-width: 100%;
        overflow-x: auto;
        border-collapse: collapse;
    }

    .article-text table td,
    .article-text table th {
        border: 1px solid #ddd;
        padding: 8px 12px;
    }

    .article-text table tr:nth-child(even) td {
        background: #f8f8f8;
    }

    .article-text pre,
    .article-text code {
        max-width: 100%;
        overflow-x: auto;
        white-space: pre-wrap;
        word-break: break-word;
        background: #f5f5f5;
        border-radius: 6px;
        padding: 2px 6px;
        font-size: 14px;
    }

    /* ============================================================
       SIDEBAR
       ============================================================ */
    .article-sidebar {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* ============================================================
       RELATED BOX
       ============================================================ */
    .related-box {
        background: #f5f5f5;
        border-radius: 16px;
        padding: 22px;
    }

    .related-box h3 {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #000;
        margin: 0 0 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid #ddd;
    }

    .related-item {
        display: flex;
        gap: 12px;
        padding: 10px 0;
        cursor: pointer;
    }

    .related-item + .related-item {
        border-top: 1px solid #e2e2e2;
    }

    .related-item img,
    .related-thumb-placeholder {
        width: 68px;
        height: 54px;
        flex-shrink: 0;
        border-radius: 8px;
        object-fit: cover;
        background: #e9e9e9;
    }

    .related-thumb-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        color: #aaa;
        font-size: 18px;
    }

    .related-item > div {
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 0;
    }

    .related-item a {
        font-size: 13.5px;
        font-weight: 600;
        color: #000;
        text-decoration: none;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.4;
    }

    .related-item a:hover {
        text-decoration: underline;
    }

    .related-item span {
        font-size: 11.5px;
        color: #888;
    }

    .related-empty {
        color: #888;
        font-size: 13px;
    }

    /* ============================================================
       SUBSCRIBE BOX (đồng bộ với news-index)
       ============================================================ */
    .subscribe-box {
        background: #f5f5f5;
        border-radius: 16px;
        padding: 22px;
    }

    .subscribe-box h3 {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #000;
        margin: 0 0 10px;
    }

    .subscribe-box p {
        font-size: 13.5px;
        color: #555;
        line-height: 1.55;
        margin: 0 0 16px;
    }

    .subscribe-box input {
        width: 100%;
        height: 42px;
        border: 1px solid #ccc;
        border-radius: 8px;
        padding: 0 14px;
        margin-bottom: 10px;
        font-size: 14px;
        outline: none;
        box-sizing: border-box;
        transition: border-color 0.2s;
    }

    .subscribe-box input:focus {
        border-color: #000;
    }

    .subscribe-box button {
        width: 100%;
        height: 42px;
        border: none;
        border-radius: 8px;
        background: #000;
        color: #fff;
        font-weight: 700;
        font-size: 13.5px;
        letter-spacing: 0.4px;
        cursor: pointer;
        transition: opacity 0.2s;
    }

    .subscribe-box button:hover {
        opacity: 0.8;
    }

    @media (max-width: 600px) {
        .article-content,
        .related-box,
        .subscribe-box {
            padding-left: 0;
            padding-right: 0;
        }
    }
</style>
@endpush

@section('content')
<section class="samsung-news-detail">
    <div class="news-detail-container">

        {{-- Breadcrumb --}}
        <div class="breadcrumb-row">
            <a href="{{ route('home') }}">Trang chủ</a> /
            <a href="{{ route('news.index') }}">Tin tức</a> /
            <span>{{ \Illuminate\Support\Str::limit($article->title, 60) }}</span>
        </div>

        <div class="detail-layout">

            {{-- ===== BÀI VIẾT ===== --}}
            <article class="article-content">

                <h1>{{ $article->title }}</h1>

                <div class="article-date">
                    @if($article->published_at)
                        <span>{{ $article->published_at->format('d/m/Y') }}</span>
                    @endif
                    <span>·</span>
                    <span>{{ number_format($article->views ?? 0) }} lượt xem</span>
                    @if($article->category)
                        <span>·</span>
                        <span>{{ $article->category->name }}</span>
                    @endif
                    @if($article->author)
                        <span>·</span>
                        <span>{{ $article->author->name }}</span>
                    @endif
                </div>

                @if($article->thumbnail)
                <div class="article-image-wrap">
                    <img class="article-image"
                         src="{{ asset('storage/' . $article->thumbnail) }}"
                         alt="{{ $article->title }}">
                </div>
                @endif

                <div class="article-text">
                    {!! $article->content !!}
                </div>

            </article>

            {{-- ===== SIDEBAR ===== --}}
            <aside class="article-sidebar">

                {{-- Tin liên quan --}}
                <div class="related-box">
                    <h3>Tin liên quan</h3>
                    @forelse($relatedNews as $item)
                    <div class="related-item" onclick="location.href='{{ route('news.show', $item->slug) }}'">
                        @if($item->thumbnail)
                            <img src="{{ asset('storage/' . $item->thumbnail) }}"
                                 alt="{{ $item->title }}" loading="lazy">
                        @else
                            <div class="related-thumb-placeholder">–</div>
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
                    <p class="related-empty">Chưa có bài viết liên quan.</p>
                    @endforelse
                </div>

                {{-- Tin mới nhất --}}
                @if($latestNews->count())
                <div class="related-box">
                    <h3>Tin mới nhất</h3>
                    @foreach($latestNews as $item)
                    <div class="related-item" onclick="location.href='{{ route('news.show', $item->slug) }}'">
                        @if($item->thumbnail)
                            <img src="{{ asset('storage/' . $item->thumbnail) }}"
                                 alt="{{ $item->title }}" loading="lazy">
                        @else
                            <div class="related-thumb-placeholder">–</div>
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

                {{-- Đăng ký nhận tin --}}
                <div class="subscribe-box">
                    <h3>Đăng ký nhận tin</h3>
                    <p>Nhận thông tin khuyến mãi mới nhất từ ElectronicShop</p>
                    <form id="newsShowNewsletterForm" onsubmit="return false;">
                        <input type="email" name="email" id="newsShowNewsletterEmail"
                               placeholder="Email của bạn" required>
                        <button type="submit" id="newsShowNewsletterBtn">ĐĂNG KÝ</button>
                    </form>
                    <div id="newsShowNewsletterMsg" style="font-size:12.5px;margin-top:8px;display:none"></div>
                </div>

            </aside>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
/* ============================================================
   NEWSLETTER (Giữ nguyên logic từ file gốc)
   ============================================================ */
(function () {
    const form  = document.getElementById('newsShowNewsletterForm');
    if (!form) return;
    const input = document.getElementById('newsShowNewsletterEmail');
    const btn   = document.getElementById('newsShowNewsletterBtn');
    const msg   = document.getElementById('newsShowNewsletterMsg');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const email = input.value.trim();
        if (!email) return;

        btn.disabled = true;
        const orig = btn.innerHTML;
        btn.innerHTML = 'ĐANG XỬ LÝ...';

        fetch('{{ route('newsletter.subscribe') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            body: JSON.stringify({ email, source: 'news_detail' }),
        })
        .then(async res => {
            const data = await res.json();
            msg.style.display = 'block';
            if (res.ok) {
                msg.style.color = '#16a34a';
                msg.textContent = data.message;
                input.value = '';
            } else {
                msg.style.color = '#e53935';
                msg.textContent = data.message || 'Có lỗi xảy ra, vui lòng thử lại.';
            }
        })
        .catch(() => {
            msg.style.display = 'block';
            msg.style.color = '#e53935';
            msg.textContent = 'Không thể kết nối, vui lòng thử lại.';
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = orig;
        });
    });
})();
</script>
@endpush