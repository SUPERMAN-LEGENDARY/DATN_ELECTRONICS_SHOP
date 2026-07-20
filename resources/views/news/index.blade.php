@extends('layouts.app')

@section('title', 'ElectronicShop - Tin tức')

@php
$showSearch = true;
@endphp

@section('content')

<section class="news-page">

    <div class="container">

        <h1 class="news-title">
            {{ isset($activeCategory) ? strtoupper($activeCategory->name) : 'TIN TỨC' }}
        </h1>

        <div class="news-layout">

            <div class="news-list">

                {{-- Bộ lọc tìm kiếm --}}
                <form method="GET" action="{{ route('news.index') }}" class="news-filter">
                    <input
                        type="text"
                        name="keyword"
                        value="{{ request('keyword') }}"
                        placeholder="Tìm kiếm bài viết...">
                    <button type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>

                @forelse($news as $post)

                <article class="news-item">

                    <div class="news-image">

                        @if($post->thumbnail)
                        <img src="{{ asset('storage/' . $post->thumbnail) }}"
                            alt="{{ $post->title }}">
                        @else
                        <div class="image-placeholder">
                            <i class="fas fa-image"></i>
                        </div>
                        @endif

                    </div>

                    <div class="news-content">

                        <h2>
                            <a href="{{ route('news.show', $post->slug) }}"
                                style="text-decoration:none;color:inherit">
                                {{ $post->title }}
                            </a>
                        </h2>

                        <div class="news-date">

                            @if($post->published_at)
                            {{ $post->published_at->format('d/m/Y') }}
                            @endif

                            @if($post->category)
                            · {{ $post->category->name }}
                            @endif

                            · {{ number_format($post->views ?? 0) }} lượt xem

                        </div>

                        <p>
                            {{ \Illuminate\Support\Str::limit(strip_tags($post->content), 150) }}
                        </p>

                        <a href="{{ route('news.show', $post->slug) }}" class="read-more">
                            Xem thêm →
                        </a>

                    </div>

                </article>

                @empty

                <article class="news-item" style="display:block">
                    <div class="news-content" style="padding-left:0">
                        <h2>Chưa có bài viết nào</h2>
                        <p>Hiện chưa có dữ liệu tin tức trong hệ thống.</p>
                    </div>
                </article>

                @endforelse

                @if(method_exists($news, 'links'))
                <div style="margin-top:20px">
                    {{ $news->links() }}
                </div>
                @endif

            </div>

            <aside class="news-sidebar">

                {{-- Danh mục tin tức --}}
                @if($categories->count())
                <div class="category-box">

                    <h3>DANH MỤC</h3>

                    <ul>
                        <li>
                            <a href="{{ route('news.index') }}"
                                class="{{ request('category') ? '' : 'active' }}">
                                Tất cả
                            </a>
                        </li>
                        @foreach($categories as $cat)
                        <li>
                            <a href="{{ route('news.index', ['category' => $cat->slug]) }}"
                                class="{{ request('category') == $cat->slug ? 'active' : '' }}">
                                {{ $cat->name }}
                            </a>
                        </li>
                        @endforeach
                    </ul>

                </div>
                @endif

                <div class="subscribe-box">

                    <h3>ĐĂNG KÝ NHẬN TIN</h3>

                    <p>Nhận thông tin khuyến mãi mới nhất từ ElectronicShop</p>

                    <form id="newsNewsletterForm" onsubmit="return false;">
                        <input type="email" name="email" id="newsNewsletterEmail" placeholder="Email của bạn" required>
                        <button type="submit" id="newsNewsletterBtn">ĐĂNG KÝ</button>
                    </form>
                    <div id="newsNewsletterMsg" style="font-size:12.5px;margin-top:8px;display:none"></div>

                </div>

            </aside>

        </div>

    </div>

</section>

@endsection

@push('scripts')
<script>
    (function () {
        const form  = document.getElementById('newsNewsletterForm');
        if (!form) return;
        const input = document.getElementById('newsNewsletterEmail');
        const btn   = document.getElementById('newsNewsletterBtn');
        const msg   = document.getElementById('newsNewsletterMsg');

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const email = input.value.trim();
            if (!email) return;

            btn.disabled = true;
            const originalText = btn.textContent;
            btn.textContent = '...';

            fetch('{{ route('newsletter.subscribe') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify({ email: email, source: 'news_sidebar' }),
            })
            .then(async (res) => {
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
                btn.textContent = originalText;
            });
        });
    })();
</script>
@endpush

<style>
    /* ===== NEWS ===== */
    .news-page {
        background: #fff;
        padding: 30px 0;
    }

    .news-title {
        font-size: 22px;
        font-weight: 800;
        color: #111;
        margin-bottom: 20px;
    }

    .news-layout {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 25px;
    }

    /* filter */
    .news-filter {
        display: flex;
        gap: 8px;
        margin-bottom: 15px;
    }

    .news-filter input {
        flex: 1;
        height: 42px;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 0 14px;
        font-size: 14px;
        outline: none;
    }

    .news-filter input:focus {
        border-color: #2563eb;
    }

    .news-filter button {
        width: 42px;
        height: 42px;
        border: none;
        background: #2563eb;
        color: #fff;
        border-radius: 6px;
        cursor: pointer;
    }

    .news-filter button:hover {
        background: #1d4ed8;
    }

    /* danh sách */
    .news-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .news-item {
        display: flex;
        border: 1px solid #edf2f7;
        background: white;
        padding: 12px;
        min-height: 130px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,.04);
        transition: box-shadow .2s, transform .2s;
    }

    .news-item:hover {
        box-shadow: 0 8px 20px rgba(0,0,0,.08);
        transform: translateY(-2px);
    }

    .news-image {
        width: 220px;
        height: 120px;
        flex-shrink: 0;
        border-radius: 8px;
        overflow: hidden;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .news-image img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 4px;
        box-sizing: border-box;
    }

    .image-placeholder {
        width: 100%;
        height: 100%;
        background: #edf2f7;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #cbd5e1;
        font-size: 35px;
    }

    .news-content {
        padding-left: 16px;
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .news-content h2 {
        font-size: 16px;
        font-weight: 700;
        color: #111827;
        margin: 0 0 6px;
    }

    .news-date {
        font-size: 12px;
        color: #9ca3af;
        margin-bottom: 8px;
    }

    .news-content p {
        font-size: 13px;
        color: #6b7280;
        line-height: 1.5;
        margin: 0;
    }

    .read-more {
        font-size: 13px;
        color: #2563eb;
        text-decoration: none;
    }

    /* Sidebar */
    .category-box {
        background: #f8fafc;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,.04);
    }

    .category-box h3 {
        font-size: 14px;
        font-weight: 800;
        color: #111;
        margin-bottom: 14px;
    }

    .category-box ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .category-box li {
        margin-bottom: 8px;
    }

    .category-box a {
        display: block;
        font-size: 13px;
        color: #555;
        text-decoration: none;
        padding: 8px 10px;
        border-radius: 6px;
        transition: .2s;
    }

    .category-box a:hover {
        background: #eef6ff;
        color: #2563eb;
    }

    .category-box a.active {
        background: #2563eb;
        color: #fff;
        font-weight: 600;
    }

    .subscribe-box {
        background: #f8fafc;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,.04);
    }

    .subscribe-box h3 {
        font-size: 14px;
        font-weight: 800;
        color: #111;
    }

    .subscribe-box p {
        font-size: 13px;
        color: #666;
        line-height: 1.5;
    }

    .subscribe-box input {
        width: 100%;
        height: 40px;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 0 12px;
        margin: 10px 0;
        font-size: 13px;
        outline: none;
        box-sizing: border-box;
    }

    .subscribe-box input:focus {
        border-color: #2563eb;
    }

    .subscribe-box button {
        width: 100%;
        height: 40px;
        border: none;
        border-radius: 6px;
        background: #2563eb;
        color: #fff;
        font-weight: 700;
        cursor: pointer;
        transition: background .15s;
    }

    .subscribe-box button:hover {
        background: #1d4ed8;
    }

    /* pagination */
    .pagination {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-top: 25px;
    }

    @media(max-width:900px) {
        .news-layout {
            grid-template-columns: 1fr;
        }

        .news-image {
            width: 150px;
        }
    }
</style>