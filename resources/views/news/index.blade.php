@extends('layouts.app')
@section('title', 'ElectronicShop - Tin tức')
@php $showSearch = true; @endphp

@push('styles')
<style>
/* ============================================================
   PAGE BACKGROUND — sky gradient (khớp trang chủ)
   ============================================================ */
body {
    background: linear-gradient(180deg,
        #bae6fd 0%,
        #e0f2fe 18%,
        #f0f9ff 38%,
        #e0f2fe 62%,
        #bae6fd 100%) fixed;
    background-attachment: fixed;
}

#sky-canvas {
    position: fixed; inset: 0;
    width: 100%; height: 100%;
    pointer-events: none; z-index: 0; opacity: .42;
}

.bubble {
    position: fixed; border-radius: 50%;
    background: radial-gradient(circle at 35% 35%, rgba(255,255,255,.8), rgba(186,230,253,.3));
    border: 1px solid rgba(125,211,252,.4);
    pointer-events: none; z-index: 0;
    animation: bubbleRise linear infinite;
}
@keyframes bubbleRise {
    0%   { transform: translateY(0) scale(1);    opacity: .7; }
    80%  { opacity: .4; }
    100% { transform: translateY(-110vh) scale(1.1); opacity: 0; }
}

/* ============================================================
   SCROLL REVEAL
   ============================================================ */
.reveal {
    opacity: 0; transform: translateY(28px);
    transition: opacity .6s cubic-bezier(.16,1,.3,1), transform .6s cubic-bezier(.16,1,.3,1);
}
.reveal.revealed { opacity: 1; transform: translateY(0); }

.stagger-children > * {
    opacity: 0; transform: translateY(20px);
    transition: opacity .5s cubic-bezier(.16,1,.3,1), transform .5s cubic-bezier(.16,1,.3,1);
}
.stagger-children.revealed > *:nth-child(1)  { opacity:1; transform:translateY(0); transition-delay:.04s; }
.stagger-children.revealed > *:nth-child(2)  { opacity:1; transform:translateY(0); transition-delay:.09s; }
.stagger-children.revealed > *:nth-child(3)  { opacity:1; transform:translateY(0); transition-delay:.14s; }
.stagger-children.revealed > *:nth-child(4)  { opacity:1; transform:translateY(0); transition-delay:.19s; }
.stagger-children.revealed > *:nth-child(5)  { opacity:1; transform:translateY(0); transition-delay:.24s; }
.stagger-children.revealed > *:nth-child(6)  { opacity:1; transform:translateY(0); transition-delay:.29s; }
.stagger-children.revealed > *:nth-child(7)  { opacity:1; transform:translateY(0); transition-delay:.34s; }
.stagger-children.revealed > *:nth-child(n+8){ opacity:1; transform:translateY(0); transition-delay:.38s; }

/* ripple */
.ripple-wave {
    position: absolute; border-radius: 50%;
    background: rgba(125,211,252,.25);
    transform: scale(0);
    animation: rippleOut .7s linear;
    pointer-events: none; z-index: 10;
}
@keyframes rippleOut { to { transform: scale(4); opacity: 0; } }

/* ============================================================
   PAGE WRAPPER
   ============================================================ */
.news-page {
    padding: 28px 0 60px;
    position: relative;
    z-index: 1;
    min-height: 100vh;
}

/* ============================================================
   PAGE HEADER
   ============================================================ */
.news-title {
    font-size: 22px;
    font-weight: 800;
    color: #0c4a6e;
    margin-bottom: 20px;
    padding-left: 12px;
    border-left: 4px solid #0ea5e9;
    display: inline-block;
    line-height: 1.4;
}

/* ============================================================
   LAYOUT GRID
   ============================================================ */
.news-layout {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 24px;
    align-items: start;
}
@media (max-width: 900px) { .news-layout { grid-template-columns: 1fr; } }

/* ============================================================
   SEARCH FILTER — glassmorphism
   ============================================================ */
.news-filter {
    display: flex;
    gap: 8px;
    margin-bottom: 18px;
}

.news-filter input {
    flex: 1;
    height: 44px;
    border: 1px solid rgba(125,211,252,.55);
    border-radius: 10px;
    padding: 0 16px;
    font-size: 14px;
    outline: none;
    background: rgba(255,255,255,.75);
    backdrop-filter: blur(8px);
    color: #0c4a6e;
    transition: border-color .2s, box-shadow .2s;
}
.news-filter input::placeholder { color: #7dd3fc; }
.news-filter input:focus {
    border-color: #0ea5e9;
    box-shadow: 0 0 0 3px rgba(14,165,233,.15);
}

.news-filter button {
    width: 44px; height: 44px;
    border: none;
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    color: #fff;
    border-radius: 10px;
    cursor: pointer;
    font-size: 15px;
    transition: opacity .2s, transform .15s, box-shadow .2s;
    box-shadow: 0 4px 12px rgba(14,165,233,.35);
}
.news-filter button:hover {
    opacity: .9; transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(14,165,233,.45);
}

/* ============================================================
   NEWS LIST
   ============================================================ */
.news-list {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

/* ============================================================
   NEWS ITEM CARD — glassmorphism
   ============================================================ */
.news-item {
    display: flex;
    background: rgba(255,255,255,.82);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border: 1px solid rgba(186,230,253,.6);
    border-radius: 16px;
    padding: 14px;
    min-height: 130px;
    box-shadow: 0 4px 18px rgba(14,165,233,.09);
    transition: box-shadow .25s, transform .25s, border-color .25s;
    position: relative;
    overflow: hidden;
    cursor: pointer;
}

/* shine sweep on hover */
.news-item::after {
    content: '';
    position: absolute; inset: 0;
    background: linear-gradient(105deg,
        transparent 40%,
        rgba(255,255,255,.38) 50%,
        transparent 60%);
    transform: translateX(-120%);
    transition: transform .55s ease;
    pointer-events: none;
}
.news-item:hover::after { transform: translateX(120%); }

.news-item:hover {
    box-shadow: 0 10px 28px rgba(14,165,233,.18);
    transform: translateY(-3px);
    border-color: #7dd3fc;
}

/* ============================================================
   NEWS IMAGE
   ============================================================ */
.news-image {
    width: 210px;
    height: 120px;
    flex-shrink: 0;
    border-radius: 10px;
    overflow: hidden;
    background: linear-gradient(160deg, #e0f2fe, #bae6fd);
    display: flex;
    align-items: center;
    justify-content: center;
}
@media (max-width: 600px) { .news-image { width: 110px; height: 90px; } }

.news-image img {
    width: 100%; height: 100%;
    object-fit: contain;
    padding: 6px; box-sizing: border-box;
    transition: transform .4s cubic-bezier(.16,1,.3,1);
}
.news-item:hover .news-image img { transform: scale(1.06); }

.image-placeholder {
    width: 100%; height: 100%;
    display: flex; justify-content: center; align-items: center;
    color: #7dd3fc; font-size: 30px;
}

/* ============================================================
   NEWS CONTENT
   ============================================================ */
.news-content {
    padding-left: 16px;
    display: flex;
    flex-direction: column;
    min-width: 0;
    gap: 4px;
}

.news-content h2 {
    font-size: 15.5px;
    font-weight: 700;
    color: #0c4a6e;
    margin: 0 0 4px;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.news-date {
    font-size: 12px;
    color: #0369a1;
    opacity: .7;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 4px;
    flex-wrap: wrap;
}

.news-content p {
    font-size: 13px;
    color: #0369a1;
    line-height: 1.55;
    margin: 0;
    opacity: .85;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.read-more {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 12.5px;
    font-weight: 600;
    color: #0369a1;
    text-decoration: none;
    margin-top: auto;
    padding: 4px 0;
    transition: color .2s, gap .2s;
}
.read-more:hover { color: #0c4a6e; gap: 8px; }

/* ============================================================
   PAGINATION
   ============================================================ */
.pagination-wrap { margin-top: 24px; }

/* ============================================================
   SIDEBAR
   ============================================================ */
.news-sidebar { display: flex; flex-direction: column; gap: 16px; }

/* ============================================================
   CATEGORY BOX
   ============================================================ */
.category-box {
    background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 50%, #7dd3fc 100%);
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 4px 18px rgba(14,165,233,.16);
    position: relative;
    overflow: hidden;
}

/* Decorative circles (giống brands section trang chủ) */
.category-box::before {
    content: '';
    position: absolute; top: -50px; right: -50px;
    width: 160px; height: 160px;
    border-radius: 50%;
    background: rgba(255,255,255,.2);
    pointer-events: none;
}
.category-box::after {
    content: '';
    position: absolute; bottom: -35px; left: -35px;
    width: 110px; height: 110px;
    border-radius: 50%;
    background: rgba(255,255,255,.15);
    pointer-events: none;
}

.category-box h3 {
    font-size: 12px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: #0c4a6e;
    margin-bottom: 14px;
    padding-bottom: 10px;
    border-bottom: 1px solid rgba(255,255,255,.45);
    position: relative; z-index: 1;
    display: flex; align-items: center; gap: 6px;
}
.category-box h3::before {
    content: '';
    width: 18px; height: 2px;
    background: #0ea5e9; border-radius: 2px;
    display: inline-block;
}

.category-box ul {
    list-style: none; margin: 0; padding: 0;
    position: relative; z-index: 1;
}
.category-box li { margin-bottom: 4px; }
.category-box a {
    display: block;
    font-size: 13px;
    color: #0369a1;
    text-decoration: none;
    padding: 7px 10px;
    border-radius: 8px;
    font-weight: 500;
    transition: background .18s, color .18s, transform .18s;
}
.category-box a:hover {
    background: rgba(255,255,255,.55);
    color: #0c4a6e;
    transform: translateX(4px);
}
.category-box a.active {
    background: rgba(255,255,255,.75);
    color: #0c4a6e;
    font-weight: 700;
    border-left: 3px solid #0ea5e9;
    padding-left: 12px;
}

/* ============================================================
   SUBSCRIBE BOX
   ============================================================ */
.subscribe-box {
    background: rgba(255,255,255,.82);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border: 1px solid rgba(186,230,253,.6);
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 4px 18px rgba(14,165,233,.1);
}

.subscribe-box h3 {
    font-size: 12px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: #0c4a6e;
    margin-bottom: 8px;
    display: flex; align-items: center; gap: 6px;
}
.subscribe-box h3::before {
    content: '';
    width: 18px; height: 2px;
    background: #0ea5e9; border-radius: 2px;
    display: inline-block;
}

.subscribe-box p {
    font-size: 13px;
    color: #0369a1;
    line-height: 1.5;
    margin-bottom: 12px;
    opacity: .85;
}

.subscribe-box input {
    width: 100%; height: 40px;
    border: 1px solid rgba(125,211,252,.55);
    border-radius: 8px;
    padding: 0 12px;
    margin-bottom: 8px;
    font-size: 13px; outline: none;
    box-sizing: border-box;
    background: rgba(255,255,255,.75);
    color: #0c4a6e;
    transition: border-color .2s, box-shadow .2s;
}
.subscribe-box input::placeholder { color: #7dd3fc; }
.subscribe-box input:focus {
    border-color: #0ea5e9;
    box-shadow: 0 0 0 3px rgba(14,165,233,.15);
}

.subscribe-box button {
    width: 100%; height: 40px;
    border: none; border-radius: 8px;
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    color: #fff; font-weight: 700; font-size: 13px;
    cursor: pointer; letter-spacing: .4px;
    transition: opacity .2s, transform .15s, box-shadow .2s;
    box-shadow: 0 4px 14px rgba(14,165,233,.3);
}
.subscribe-box button:hover {
    opacity: .9; transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(14,165,233,.4);
}
</style>
@endpush

@section('content')
{{-- Sky Canvas --}}
<canvas id="sky-canvas" aria-hidden="true"></canvas>

<section class="news-page">
    <div class="container" style="position:relative;z-index:1">

        <h1 class="news-title reveal">
            {{ isset($activeCategory) ? strtoupper($activeCategory->name) : 'TIN TỨC' }}
        </h1>

        <div class="news-layout">

            {{-- ===== DANH SÁCH TIN TỨC ===== --}}
            <div class="news-list">

                {{-- Bộ lọc --}}
                <form method="GET" action="{{ route('news.index') }}" class="news-filter reveal">
                    <input
                        type="text"
                        name="keyword"
                        value="{{ request('keyword') }}"
                        placeholder="Tìm kiếm bài viết...">
                    <button type="submit" aria-label="Tìm kiếm">
                        <i class="fas fa-search"></i>
                    </button>
                </form>

                <div class="stagger-children">
                @forelse($news as $post)
                <article class="news-item">

                    <div class="news-image">
                        @if($post->thumbnail)
                            <img src="{{ asset('storage/' . $post->thumbnail) }}"
                                 alt="{{ $post->title }}" loading="lazy">
                        @else
                            <div class="image-placeholder">
                                <i class="fas fa-newspaper"></i>
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
                                <i class="far fa-calendar-alt" style="opacity:.7"></i>
                                {{ $post->published_at->format('d/m/Y') }}
                            @endif
                            @if($post->category)
                                <span style="opacity:.5">·</span>
                                <i class="fas fa-tag" style="opacity:.7"></i>
                                {{ $post->category->name }}
                            @endif
                            <span style="opacity:.5">·</span>
                            <i class="far fa-eye" style="opacity:.7"></i>
                            {{ number_format($post->views ?? 0) }} lượt xem
                        </div>

                        <p>{{ \Illuminate\Support\Str::limit(strip_tags($post->content), 150) }}</p>

                        <a href="{{ route('news.show', $post->slug) }}" class="read-more">
                            Xem thêm <i class="fas fa-arrow-right" style="font-size:11px"></i>
                        </a>
                    </div>

                </article>
                @empty
                <article class="news-item" style="display:block">
                    <div class="news-content" style="padding-left:0">
                        <h2 style="color:#7dd3fc">Chưa có bài viết nào</h2>
                        <p>Hiện chưa có dữ liệu tin tức trong hệ thống.</p>
                    </div>
                </article>
                @endforelse
                </div>

                @if(method_exists($news, 'links'))
                <div class="pagination-wrap reveal">
                    {{ $news->links() }}
                </div>
                @endif

            </div>

            {{-- ===== SIDEBAR ===== --}}
            <aside class="news-sidebar">

                @if($categories->count())
                <div class="category-box reveal">
                    <h3>Danh mục</h3>
                    <ul>
                        <li>
                            <a href="{{ route('news.index') }}"
                               class="{{ request('category') ? '' : 'active' }}">
                                <i class="fas fa-border-all" style="margin-right:5px;opacity:.6"></i> Tất cả
                            </a>
                        </li>
                        @foreach($categories as $cat)
                        <li>
                            <a href="{{ route('news.index', ['category' => $cat->slug]) }}"
                               class="{{ request('category') == $cat->slug ? 'active' : '' }}">
                                <i class="fas fa-chevron-right" style="margin-right:5px;opacity:.5;font-size:10px"></i>
                                {{ $cat->name }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="subscribe-box reveal">
                    <h3>Đăng ký nhận tin</h3>
                    <p>Nhận thông tin khuyến mãi mới nhất từ ElectronicShop</p>
                    <form id="newsNewsletterForm" onsubmit="return false;">
                        <input type="email" name="email" id="newsNewsletterEmail"
                               placeholder="Email của bạn" required>
                        <button type="submit" id="newsNewsletterBtn">
                            <i class="fas fa-paper-plane" style="margin-right:6px"></i>ĐĂNG KÝ
                        </button>
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
/* ============================================================
   NEWSLETTER
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
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

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

/* ============================================================
   ANIMATIONS
   ============================================================ */
(function () {

    /* ---- Canvas clouds ---- */
    const canvas = document.getElementById('sky-canvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let W, H, clouds = [];
        function resize() { W = canvas.width = window.innerWidth; H = canvas.height = window.innerHeight; }
        window.addEventListener('resize', resize); resize();
        function makeCloud() {
            return { x: Math.random() * W * 1.2, y: Math.random() * H * .6,
                     r: 50 + Math.random() * 110, dx: .13 + Math.random() * .22,
                     alpha: .05 + Math.random() * .1 };
        }
        for (let i = 0; i < 8; i++) clouds.push(makeCloud());
        function drawCloud(c) {
            const g = ctx.createRadialGradient(c.x,c.y,0,c.x,c.y,c.r);
            g.addColorStop(0, `rgba(255,255,255,${c.alpha})`);
            g.addColorStop(.6, `rgba(186,230,253,${c.alpha*.6})`);
            g.addColorStop(1, 'rgba(186,230,253,0)');
            ctx.beginPath(); ctx.arc(c.x,c.y,c.r,0,Math.PI*2);
            ctx.fillStyle = g; ctx.fill();
            [-.5,.5].forEach(o => {
                ctx.beginPath();
                ctx.arc(c.x+c.r*.55*o, c.y-c.r*.18, c.r*.72, 0, Math.PI*2);
                ctx.fillStyle = `rgba(255,255,255,${c.alpha*.7})`; ctx.fill();
            });
        }
        (function anim() {
            ctx.clearRect(0,0,W,H);
            clouds.forEach(c => {
                drawCloud(c); c.x += c.dx;
                if (c.x-c.r > W*1.2) { c.x=-c.r*2; c.y=Math.random()*H*.6; }
            });
            requestAnimationFrame(anim);
        })();
    }

    /* ---- Bubbles ---- */
    function spawnBubble() {
        const el = document.createElement('div');
        el.className = 'bubble';
        const size = 4 + Math.random() * 14, dur = 8 + Math.random() * 12;
        el.style.cssText = [`width:${size}px`,`height:${size}px`,
            `left:${Math.random()*100}vw`,`bottom:-${size}px`,
            `animation-duration:${dur}s`,`animation-delay:${Math.random()*5}s`].join(';');
        document.body.appendChild(el);
        setTimeout(() => el.remove(), (dur+5)*1000);
    }
    for (let i = 0; i < 8; i++) spawnBubble();
    setInterval(spawnBubble, 3500);

    /* ---- Scroll Reveal ---- */
    const io = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) { e.target.classList.add('revealed'); io.unobserve(e.target); }
        });
    }, { threshold: 0.07, rootMargin: '0px 0px -30px 0px' });
    document.querySelectorAll('.reveal, .stagger-children').forEach(el => io.observe(el));

    /* ---- Ripple on news items ---- */
    document.querySelectorAll('.news-item').forEach(card => {
        card.addEventListener('click', function (e) {
            const rect = card.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height) * 1.4;
            const ripple = document.createElement('span');
            ripple.className = 'ripple-wave';
            ripple.style.cssText = [`width:${size}px`,`height:${size}px`,
                `left:${e.clientX-rect.left-size/2}px`,
                `top:${e.clientY-rect.top-size/2}px`].join(';');
            card.appendChild(ripple);
            ripple.addEventListener('animationend', () => ripple.remove());
        });
    });

    /* ---- Sidebar category link stagger ---- */
    const catLinks = document.querySelectorAll('.category-box li');
    catLinks.forEach((li, i) => {
        li.style.opacity = '0';
        li.style.transform = 'translateX(-10px)';
        li.style.transition = `opacity .4s ${.1+i*.06}s, transform .4s ${.1+i*.06}s`;
    });
    const catBox = document.querySelector('.category-box');
    if (catBox) {
        const catIo = new IntersectionObserver(entries => {
            if (entries[0].isIntersecting) {
                catLinks.forEach(li => { li.style.opacity='1'; li.style.transform='translateX(0)'; });
                catIo.disconnect();
            }
        }, { threshold: .2 });
        catIo.observe(catBox);
    }

})();
</script>
@endpush
