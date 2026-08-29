{{-- resources/views/members/news-and-tips.blade.php --}}
@extends('layouts.app')

@section('title', 'ElectronicShop - Tin tức')

@php $showSearch = true; @endphp

@push('styles')
<style>
    /* ============================================================
       SAMSUNG UI GIAO DIỆN MỚI
       ============================================================ */
    .samsung-news {
        color: #111;
        background: #fff;
        font-family: Arial, Helvetica, sans-serif;
    }

    .news-hero {
        position: relative;
        isolation: isolate;
        padding: 72px 6%;
        min-height: 500px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        overflow: hidden;

        /* Ảnh nền */
        background-image: url("{{ asset('images/news-banner.png') }}");
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-color: #f5f5f5;
    }

    /* Lớp phủ: trong suốt ở trên (ảnh rõ nét), tối dần nhẹ ở đáy để chữ trắng nổi bật */
.news-hero::before {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom,
        rgba(0, 0, 0, 0) 0%,
        rgba(0, 0, 0, 0) 40%,
        rgba(0, 0, 0, 0.55) 100%);
    z-index: 0;
}

    /* Nội dung luôn hiển thị phía trên ảnh nền */
    .news-hero h1,
    .news-hero p,
    .news-hero .samsung-search {
        position: relative;
        z-index: 1;
    }

    .news-hero h1 {
        margin: 0;
        font-size: clamp(36px, 6vw, 72px);
        font-weight: 700;
        letter-spacing: -3px;
        color: #fff;
    }

    .news-hero p {
        max-width: 620px;
        margin: 22px auto 0;
        color: #e5e5e5;
        font-size: 18px;
        line-height: 1.6;
    }

    /* Form tìm kiếm chuẩn style Samsung */
    .samsung-search {
        max-width: 500px;
        margin: 32px auto 0;
        display: flex;
        gap: 8px;
    }

    .samsung-search input {
        flex: 1;
        padding: 12px 18px;
        border: 1px solid #ccc;
        border-radius: 24px;
        font-size: 15px;
        outline: none;
        transition: border-color 0.2s;
        background: rgba(255, 255, 255, 0.95);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    }

    .samsung-search input:focus {
        border-color: #000;
    }

    .samsung-search button {
        padding: 0 24px;
        background: #000;
        color: #fff;
        border: none;
        border-radius: 24px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: opacity 0.2s;
    }

    .samsung-search button:hover {
        opacity: 0.8;
    }

    .news-tabs {
        display: flex;
        justify-content: center;
        gap: 34px;
        padding: 24px 6%;
        border-bottom: 1px solid #ddd;
        overflow-x: auto;
        white-space: nowrap;
    }

    .news-tabs a {
        color: #555;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: color 0.2s;
    }
    
    .news-tabs a:hover {
        color: #000;
    }

    /* Thay đổi từ :first-child sang class .active để xử lý logic backend */
    .news-tabs a.active {
        color: #000;
        border-bottom: 2px solid #000;
        padding-bottom: 8px;
    }

    .news-container {
        width: min(1280px, 88%);
        margin: auto;
        padding: 56px 0 90px;
    }

    .news-section-heading {
        display: flex;
        align-items: end;
        justify-content: space-between;
        gap: 24px;
        margin-bottom: 28px;
    }

    .news-section-heading h2 {
        margin: 0;
        font-size: 32px;
        letter-spacing: -1px;
    }

    .news-section-heading a {
        color: #1428a0;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
    }

    .featured-news {
        display: grid;
        grid-template-columns: 1.4fr 1fr;
        min-height: 420px;
        overflow: hidden;
        background: #f1f1f1;
        border-radius: 16px;
    }

    .featured-news img {
        width: 100%;
        height: 100%;
        min-height: 420px;
        object-fit: cover;
        background: #e9e9e9;
    }

    .featured-content {
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 48px;
    }

    .news-label {
        margin-bottom: 18px;
        color: #555;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .featured-content h3 {
        margin: 0;
        font-size: clamp(28px, 4vw, 40px);
        line-height: 1.2;
        letter-spacing: -1px;
        color: #000;
    }

    .featured-content p {
        margin: 24px 0;
        color: #555;
        font-size: 16px;
        line-height: 1.6;
    }

    .news-link {
        width: fit-content;
        color: #000;
        font-size: 14px;
        font-weight: 700;
        text-decoration: underline;
        text-underline-offset: 5px;
    }

    .news-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 28px;
    }

    .news-card {
        overflow: hidden;
        background: #fff;
        transition: transform 0.2s;
    }
    
    .news-card:hover {
        transform: translateY(-4px);
    }

    .news-card img {
        display: block;
        width: 100%;
        aspect-ratio: 1.35;
        object-fit: cover;
        background: #f2f2f2;
        border-radius: 12px;
    }

    .news-card-content {
        padding: 20px 0 0;
    }

    .news-card h3 {
        margin: 0;
        font-size: 21px;
        line-height: 1.3;
        color: #000;
    }

    .news-card p {
        margin: 12px 0 18px;
        color: #666;
        font-size: 14px;
        line-height: 1.55;
    }

    .news-date {
        color: #777;
        font-size: 12px;
    }

    .samsung-pagination {
        margin-top: 56px;
        display: flex;
        justify-content: center;
    }

    .news-promo {
        display: grid;
        grid-template-columns: 1fr 1fr;
        align-items: stretch;
        gap: 36px;
        margin-top: 86px;
        padding: 48px;
        background: #f5f5f5;
        border-radius: 24px;
        overflow: hidden;
    }

    .news-promo-content {
        min-width: 0;
    }

    .news-promo-image {
        width: 100%;
        height: 100%;
        min-height: 300px;
        overflow: hidden;
        border-radius: 16px;
        background: #e9e9e9;
    }

    .news-promo-image img {
        display: block;
        width: 100%;
        height: 100%;
        min-height: 300px;
        object-fit: cover;
        object-position: center;
        border-radius: 16px;
        transition: transform 0.4s ease;
    }

    .news-promo-image:hover img {
        transform: scale(1.03);
    }

    .news-promo h2 {
        margin: 0;
        font-size: 34px;
        letter-spacing: -1px;
    }

    .news-promo p {
        margin: 16px 0 0;
        color: #555;
        line-height: 1.6;
    }

    /* Đăng ký nhận tin */
    .samsung-newsletter {
        margin-top: 24px;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .samsung-newsletter input {
        flex: 1;
        min-width: 200px;
        padding: 14px 18px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 15px;
        outline: none;
    }

    .samsung-newsletter input:focus {
        border-color: #000;
    }

    .samsung-newsletter button {
        padding: 14px 28px;
        background: #000;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: opacity 0.2s;
    }

    .samsung-newsletter button:hover {
        opacity: 0.8;
    }

    .news-promo img {
        max-width: 100%;
    }

    @media (max-width: 800px) {
        .news-hero {
            min-height: 420px;
            padding: 52px 6%;
            background-position: center;
        }

        .news-container {
            padding: 40px 0 64px;
        }

        .featured-news,
        .news-promo {
            grid-template-columns: 1fr;
        }

        .featured-news img {
            min-height: 260px;
        }

        .featured-content,
        .news-promo {
            padding: 30px;
        }

        .news-promo-image {
            width: 100%;
            height: 240px;
            min-height: 240px;
            border-radius: 14px;
        }

        .news-promo-image img {
            width: 100%;
            height: 240px;
            min-height: 240px;
            object-fit: cover;
            object-position: center;
            border-radius: 14px;
        }

        .samsung-newsletter {
            flex-direction: column;
        }

        .samsung-newsletter input,
        .samsung-newsletter button {
            width: 100%;
            min-width: 100%;
            box-sizing: border-box;
        }

        .news-grid {
            grid-template-columns: 1fr;
            gap: 42px;
        }

        .news-section-heading {
            align-items: start;
            flex-direction: column;
            gap: 12px;
        }
    }
</style>
@endpush

@section('content')
<div class="samsung-news">
    <section class="news-hero">
        <h1>{{ isset($activeCategory) ? strtoupper($activeCategory->name) : 'Tin tức' }}</h1>
        <p>
            Khám phá những câu chuyện mới nhất, mẹo hữu ích và nguồn cảm hứng từ thế giới công nghệ.
        </p>

        {{-- Form tìm kiếm --}}
        <form method="GET" action="{{ route('news.index') }}" class="samsung-search">
            <input 
                type="text" 
                name="keyword" 
                value="{{ request('keyword') }}" 
                placeholder="Tìm kiếm bài viết..."
            >
            <button type="submit" aria-label="Tìm kiếm">Tìm kiếm</button>
        </form>
    </section>

    {{-- Danh mục tin tức --}}
    <nav class="news-tabs" aria-label="Danh mục tin tức">
        <a href="{{ route('news.index') }}" class="{{ request('category') ? '' : 'active' }}">Tất cả</a>
        
        @if($categories->count())
            @foreach($categories as $cat)
                <a href="{{ route('news.index', ['category' => $cat->slug]) }}"
                   class="{{ request('category') == $cat->slug ? 'active' : '' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        @endif
    </nav>

    <main class="news-container">
        @if($news->count() > 0)
            {{-- Lấy bài viết đầu tiên làm Tin Nổi Bật --}}
            @php $featured = $news->first(); @endphp
            <section>
                <div class="news-section-heading">
                    <h2>Nổi bật</h2>
                </div>

                <article class="featured-news">
                    @if($featured->thumbnail)
                        <img src="{{ asset('storage/' . $featured->thumbnail) }}" alt="{{ $featured->title }}" loading="lazy">
                    @else
                        <img src="/images/samsung-featured-news.jpg" alt="No image" loading="lazy">
                    @endif

                    <div class="featured-content">
                        @if($featured->category)
                            <span class="news-label">{{ $featured->category->name }}</span>
                        @endif
                        
                        <h3>{{ $featured->title }}</h3>
                        
                        <p>{{ \Illuminate\Support\Str::limit(strip_tags($featured->content), 150) }}</p>
                        
                        <div class="news-date" style="margin-bottom: 24px;">
                            @if($featured->published_at)
                                <span>{{ $featured->published_at->format('d/m/Y') }}</span>
                            @endif
                            <span style="margin: 0 6px;">|</span>
                            <span>{{ number_format($featured->views ?? 0) }} lượt xem</span>
                        </div>

                        <a href="{{ route('news.show', $featured->slug) }}" class="news-link">Đọc thêm</a>
                    </div>
                </article>
            </section>

            {{-- Các bài viết tiếp theo đưa vào Grid --}}
            @if($news->count() > 1)
            <section style="margin-top: 76px;">
                <div class="news-section-heading">
                    <h2>Tin mới nhất</h2>
                </div>

                <!-- Skeleton Container -->
                <div class="sm-skeleton-container" data-target="real-news-list">
                    <div class="sm-skeleton-grid" style="--cols: 3">
                        @for($i = 0; $i < 6; $i++)
                        <div class="sm-skeleton-card">
                            <div class="sm-skel-img sm-skel-shimmer" style="aspect-ratio: 16/9; margin-bottom: 16px;"></div>
                            <div class="sm-skel-line sm-skel-shimmer"></div>
                            <div class="sm-skel-line sm-skel-shimmer"></div>
                            <div class="sm-skel-line short sm-skel-shimmer"></div>
                        </div>
                        @endfor
                    </div>
                </div>

                <div id="real-news-list" style="display:none;">
                    <div class="news-grid">
                    @foreach($news as $index => $post)
                        @if($index > 0)
                            <article class="news-card">
                                <a href="{{ route('news.show', $post->slug) }}" style="text-decoration:none; color:inherit;">
                                    @if($post->thumbnail)
                                        <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="{{ $post->title }}" loading="lazy">
                                    @else
                                        <img src="/images/samsung-news-1.jpg" alt="No image" loading="lazy">
                                    @endif
                                    
                                    <div class="news-card-content">
                                        @if($post->category)
                                            <span class="news-label">{{ $post->category->name }}</span>
                                        @endif
                                        
                                        <h3>{{ $post->title }}</h3>
                                        <p>{{ \Illuminate\Support\Str::limit(strip_tags($post->content), 90) }}</p>
                                        
                                        <div class="news-date">
                                            @if($post->published_at)
                                                <span>{{ $post->published_at->format('d/m/Y') }}</span>
                                                <span style="margin: 0 4px;">·</span>
                                            @endif
                                            <span>{{ number_format($post->views ?? 0) }} lượt xem</span>
                                        </div>
                                    </div>
                                </a>
                            </article>
                        @endif
                    @endforeach
                </div>
            </section>
            @endif

            {{-- Phân trang --}}
            @if(method_exists($news, 'links'))
                <div class="samsung-pagination">
                    {{ $news->links() }}
                </div>
            @endif
            </div>

        @else
            <section style="text-align: center; padding: 60px 0;">
                <h2>Chưa có bài viết nào</h2>
                <p style="color: #666;">Hiện chưa có dữ liệu tin tức trong hệ thống.</p>
            </section>
        @endif

        {{-- Form Đăng ký nhận tin --}}
        <section class="news-promo">

            <div class="news-promo-content">
                <h2>Luôn cập nhật cùng chúng tôi</h2>

                <p>
                    Nhận thông tin khuyến mãi mới nhất, xu hướng công nghệ và những câu
                    chuyện truyền cảm hứng từ ElectronicShop.
                </p>

                <form
                    id="newsNewsletterForm"
                    onsubmit="return false;"
                    class="samsung-newsletter"
                >
                    <input
                        type="email"
                        name="email"
                        id="newsNewsletterEmail"
                        placeholder="Email của bạn"
                        autocomplete="email"
                        required
                    >

                    <button type="submit" id="newsNewsletterBtn">
                        ĐĂNG KÝ
                    </button>
                </form>

                <div
                    id="newsNewsletterMsg"
                    style="font-size:14px; font-weight:600; margin-top:12px; display:none"
                ></div>
            </div>

            <div class="news-promo-image">
                <img
                    src="{{ asset('images/samsung-news-promo.png') }}"
                    alt="Hệ sinh thái công nghệ ElectronicShop"
                    loading="lazy"
                >
            </div>

        </section>
    </main>
</div>
@endsection

@push('scripts')
<script>
/* ============================================================
   NEWSLETTER SCRIPT (Giữ nguyên logic từ file gốc)
   ============================================================ */
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
        const orig = btn.innerHTML;
        btn.innerHTML = 'ĐANG XỬ LÝ...';

        fetch('{{ route('newsletter.subscribe') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            body: JSON.stringify({ email, source: 'news_sidebar' }),
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