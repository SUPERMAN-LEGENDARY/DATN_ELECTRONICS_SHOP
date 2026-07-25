@extends('layouts.app')
@section('title', $article->title . ' - ElectronicShop')
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
    opacity: 0; transform: translateY(24px);
    transition: opacity .6s cubic-bezier(.16,1,.3,1), transform .6s cubic-bezier(.16,1,.3,1);
}
.reveal.revealed { opacity: 1; transform: translateY(0); }

.stagger-children > * {
    opacity: 0; transform: translateY(16px);
    transition: opacity .5s cubic-bezier(.16,1,.3,1), transform .5s cubic-bezier(.16,1,.3,1);
}
.stagger-children.revealed > *:nth-child(1)  { opacity:1; transform:translateY(0); transition-delay:.04s; }
.stagger-children.revealed > *:nth-child(2)  { opacity:1; transform:translateY(0); transition-delay:.09s; }
.stagger-children.revealed > *:nth-child(3)  { opacity:1; transform:translateY(0); transition-delay:.14s; }
.stagger-children.revealed > *:nth-child(4)  { opacity:1; transform:translateY(0); transition-delay:.19s; }
.stagger-children.revealed > *:nth-child(5)  { opacity:1; transform:translateY(0); transition-delay:.24s; }
.stagger-children.revealed > *:nth-child(n+6){ opacity:1; transform:translateY(0); transition-delay:.28s; }

/* ============================================================
   PAGE WRAPPER
   ============================================================ */
.news-detail {
    padding: 28px 0 60px;
    position: relative;
    z-index: 1;
    min-height: 100vh;
}

/* ============================================================
   BREADCRUMB
   ============================================================ */
.breadcrumb-row {
    font-size: 13px; color: #0369a1;
    margin-bottom: 20px; display: block;
}
.breadcrumb-row a {
    color: #0c4a6e; font-weight: 600; text-decoration: none;
}
.breadcrumb-row a:hover { text-decoration: underline; }
.breadcrumb-row span { color: #0369a1; opacity: .75; }

/* ============================================================
   LAYOUT GRID
   ============================================================ */
.detail-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 300px;
    gap: 24px;
    align-items: start;
}
@media (max-width: 900px) { .detail-layout { grid-template-columns: 1fr; } }

/* ============================================================
   ARTICLE CONTENT — glassmorphism card
   ============================================================ */
.article-content {
    background: rgba(255,255,255,.84);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(186,230,253,.6);
    border-radius: 18px;
    padding: 28px 32px;
    box-shadow: 0 4px 24px rgba(14,165,233,.1);
    min-width: 0;
    overflow-wrap: break-word;
    word-break: break-word;
    transition: box-shadow .3s;
}
@media (max-width: 600px) { .article-content { padding: 18px; } }

.article-content h1 {
    font-size: 24px;
    font-weight: 800;
    color: #0c4a6e;
    line-height: 1.4;
    margin-bottom: 10px;
}

.article-date {
    font-size: 12.5px;
    color: #0369a1;
    opacity: .75;
    margin-bottom: 22px;
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
    padding-bottom: 16px;
    border-bottom: 1px solid rgba(186,230,253,.5);
}

/* ============================================================
   ARTICLE HERO IMAGE
   ============================================================ */
.article-image-wrap {
    width: 100%;
    max-height: 480px;
    background: linear-gradient(160deg, #e0f2fe, #bae6fd);
    border-radius: 12px;
    margin-bottom: 24px;
    display: flex; align-items: center; justify-content: center;
    overflow: hidden;
    box-shadow: 0 4px 18px rgba(14,165,233,.12);
}
.article-image {
    width: 100%; max-height: 480px; object-fit: contain;
    transition: transform .5s cubic-bezier(.16,1,.3,1);
}
.article-image-wrap:hover .article-image { transform: scale(1.02); }

/* ============================================================
   ARTICLE BODY TEXT
   ============================================================ */
.article-text {
    font-size: 15px;
    line-height: 1.85;
    color: #0f4c75;
    max-width: 100%;
    overflow-wrap: break-word;
    word-break: break-word;
}
.article-text h2 {
    font-size: 19px; font-weight: 700;
    color: #0c4a6e; margin-top: 28px; margin-bottom: 10px;
    padding-left: 12px;
    border-left: 3px solid #0ea5e9;
}
.article-text h3 { font-size: 16px; font-weight: 700; color: #0c4a6e; margin-top: 20px; }
.article-text p  { margin-bottom: 14px; }
.article-text a  { color: #0369a1; font-weight: 600; overflow-wrap: break-word; word-break: break-word; }
.article-text a:hover { color: #0c4a6e; }
.article-text blockquote {
    border-left: 4px solid #38bdf8;
    background: rgba(186,230,253,.25);
    border-radius: 0 10px 10px 0;
    margin: 18px 0; padding: 12px 18px;
    color: #0369a1; font-style: italic;
}
.article-text img, .article-text video, .article-text iframe { max-width: 100%; height: auto; border-radius: 8px; }
.article-text table { display: block; max-width: 100%; overflow-x: auto; border-collapse: collapse; }
.article-text table td, .article-text table th { border: 1px solid rgba(186,230,253,.6); padding: 8px 12px; }
.article-text table tr:nth-child(even) td { background: rgba(186,230,253,.15); }
.article-text pre, .article-text code { max-width: 100%; overflow-x: auto; white-space: pre-wrap; word-break: break-word; background: rgba(186,230,253,.2); border-radius: 6px; padding: 2px 6px; font-size: 13px; }

/* ============================================================
   SIDEBAR
   ============================================================ */
.article-sidebar { display: flex; flex-direction: column; gap: 16px; }

/* ============================================================
   RELATED BOX — glassmorphism (2 variants: related & latest)
   ============================================================ */
.related-box {
    background: rgba(255,255,255,.8);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border: 1px solid rgba(186,230,253,.6);
    border-radius: 16px;
    padding: 18px 20px;
    box-shadow: 0 4px 16px rgba(14,165,233,.09);
    transition: box-shadow .25s;
}
.related-box:hover { box-shadow: 0 8px 24px rgba(14,165,233,.16); }

.related-box h3 {
    font-size: 12px; font-weight: 800;
    text-transform: uppercase; letter-spacing: .6px;
    color: #0c4a6e; margin-bottom: 14px;
    padding-bottom: 10px;
    border-bottom: 1px solid rgba(186,230,253,.5);
    display: flex; align-items: center; gap: 6px;
}
.related-box h3::before {
    content: '';
    width: 18px; height: 2px;
    background: linear-gradient(90deg, #0ea5e9, #38bdf8);
    border-radius: 2px; display: inline-block;
}

.related-item {
    display: flex; gap: 10px;
    padding: 8px 6px;
    margin: 0 -6px;
    border-radius: 10px;
    transition: background .18s, transform .18s;
    cursor: pointer;
}
.related-item:hover {
    background: rgba(186,230,253,.3);
    transform: translateX(3px);
}
.related-item + .related-item { border-top: 1px solid rgba(186,230,253,.3); }

.related-item img,
.related-thumb-placeholder {
    width: 68px; height: 54px;
    flex-shrink: 0;
    border-radius: 8px;
    object-fit: contain;
    background: linear-gradient(160deg, #e0f2fe, #bae6fd);
    padding: 3px; box-sizing: border-box;
}
.related-thumb-placeholder {
    display: flex; align-items: center; justify-content: center;
    color: #7dd3fc; font-size: 20px;
}

.related-item > div { display: flex; flex-direction: column; gap: 4px; min-width: 0; }

.related-item a {
    font-size: 13px; font-weight: 600;
    color: #0c4a6e; text-decoration: none;
    display: -webkit-box;
    -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    line-height: 1.4;
    transition: color .15s;
}
.related-item a:hover { color: #0369a1; }

.related-item span {
    font-size: 11px; color: #7dd3fc;
    display: flex; align-items: center; gap: 3px;
}

/* ============================================================
   SUBSCRIBE BOX (khớp news-index)
   ============================================================ */
.subscribe-box {
    background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 50%, #7dd3fc 100%);
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 4px 18px rgba(14,165,233,.16);
    position: relative; overflow: hidden;
}
.subscribe-box::before {
    content: ''; position: absolute; top: -50px; right: -50px;
    width: 160px; height: 160px; border-radius: 50%;
    background: rgba(255,255,255,.2); pointer-events: none;
}
.subscribe-box::after {
    content: ''; position: absolute; bottom: -35px; left: -35px;
    width: 110px; height: 110px; border-radius: 50%;
    background: rgba(255,255,255,.15); pointer-events: none;
}

.subscribe-box h3 {
    font-size: 12px; font-weight: 800;
    text-transform: uppercase; letter-spacing: .6px;
    color: #0c4a6e; margin-bottom: 8px;
    display: flex; align-items: center; gap: 6px;
    position: relative; z-index: 1;
}
.subscribe-box h3::before {
    content: '';
    width: 18px; height: 2px;
    background: #0ea5e9; border-radius: 2px; display: inline-block;
}

.subscribe-box p {
    font-size: 13px; color: #0369a1;
    line-height: 1.5; margin-bottom: 12px;
    position: relative; z-index: 1;
}

.subscribe-box input {
    width: 100%; height: 40px;
    border: 1px solid rgba(255,255,255,.6);
    border-radius: 8px; padding: 0 12px;
    margin-bottom: 8px; font-size: 13px; outline: none;
    box-sizing: border-box;
    background: rgba(255,255,255,.7);
    color: #0c4a6e;
    transition: border-color .2s, box-shadow .2s;
    position: relative; z-index: 1;
}
.subscribe-box input::placeholder { color: #7dd3fc; }
.subscribe-box input:focus {
    border-color: #0ea5e9;
    box-shadow: 0 0 0 3px rgba(14,165,233,.18);
}

.subscribe-box button {
    width: 100%; height: 40px; border: none;
    border-radius: 8px;
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    color: #fff; font-weight: 700; font-size: 13px; letter-spacing: .4px;
    cursor: pointer;
    transition: opacity .2s, transform .15s, box-shadow .2s;
    box-shadow: 0 4px 14px rgba(14,165,233,.3);
    position: relative; z-index: 1;
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

<section class="news-detail">
    <div class="container" style="position:relative;z-index:1">

        {{-- Breadcrumb --}}
        <div class="breadcrumb-row reveal">
            <a href="{{ route('home') }}">Trang chủ</a> /
            <a href="{{ route('news.index') }}">Tin tức</a> /
            <span>{{ \Illuminate\Support\Str::limit($article->title, 60) }}</span>
        </div>

        <div class="detail-layout">

            {{-- ===== BÀI VIẾT ===== --}}
            <article class="article-content reveal">

                <h1>{{ $article->title }}</h1>

                <div class="article-date">
                    @if($article->published_at)
                        <i class="far fa-calendar-alt"></i>
                        {{ $article->published_at->format('d/m/Y') }}
                    @endif
                    <span style="opacity:.4">·</span>
                    <i class="far fa-eye"></i>
                    {{ number_format($article->views ?? 0) }} lượt xem
                    @if($article->category)
                        <span style="opacity:.4">·</span>
                        <i class="fas fa-tag" style="font-size:10px"></i>
                        {{ $article->category->name }}
                    @endif
                    @if($article->author)
                        <span style="opacity:.4">·</span>
                        <i class="far fa-user"></i>
                        {{ $article->author->name }}
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
                <div class="related-box reveal">
                    <h3>Tin liên quan</h3>
                    <div class="stagger-children">
                    @forelse($relatedNews as $item)
                    <div class="related-item" onclick="location.href='{{ route('news.show', $item->slug) }}'">
                        @if($item->thumbnail)
                            <img src="{{ asset('storage/' . $item->thumbnail) }}"
                                 alt="{{ $item->title }}" loading="lazy">
                        @else
                            <div class="related-thumb-placeholder"><i class="fas fa-newspaper"></i></div>
                        @endif
                        <div>
                            <a href="{{ route('news.show', $item->slug) }}">
                                {{ \Illuminate\Support\Str::limit($item->title, 50) }}
                            </a>
                            <span>
                                @if($item->published_at)
                                    <i class="far fa-calendar-alt"></i>
                                    {{ $item->published_at->format('d/m/Y') }}
                                @endif
                            </span>
                        </div>
                    </div>
                    @empty
                    <p style="color:#7dd3fc;font-size:13px">Chưa có bài viết liên quan.</p>
                    @endforelse
                    </div>
                </div>

                {{-- Tin mới nhất --}}
                @if($latestNews->count())
                <div class="related-box reveal">
                    <h3>Tin mới nhất</h3>
                    <div class="stagger-children">
                    @foreach($latestNews as $item)
                    <div class="related-item" onclick="location.href='{{ route('news.show', $item->slug) }}'">
                        @if($item->thumbnail)
                            <img src="{{ asset('storage/' . $item->thumbnail) }}"
                                 alt="{{ $item->title }}" loading="lazy">
                        @else
                            <div class="related-thumb-placeholder"><i class="fas fa-newspaper"></i></div>
                        @endif
                        <div>
                            <a href="{{ route('news.show', $item->slug) }}">
                                {{ \Illuminate\Support\Str::limit($item->title, 50) }}
                            </a>
                            <span>
                                @if($item->published_at)
                                    <i class="far fa-calendar-alt"></i>
                                    {{ $item->published_at->format('d/m/Y') }}
                                @endif
                            </span>
                        </div>
                    </div>
                    @endforeach
                    </div>
                </div>
                @endif

                {{-- Đăng ký nhận tin --}}
                <div class="subscribe-box reveal">
                    <h3>Đăng ký nhận tin</h3>
                    <p>Nhận thông tin khuyến mãi mới nhất từ ElectronicShop</p>
                    <form id="newsShowNewsletterForm" onsubmit="return false;">
                        <input type="email" name="email" id="newsShowNewsletterEmail"
                               placeholder="Email của bạn" required>
                        <button type="submit" id="newsShowNewsletterBtn">
                            <i class="fas fa-paper-plane" style="margin-right:6px"></i>ĐĂNG KÝ
                        </button>
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
   NEWSLETTER
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
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

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
        .finally(() => { btn.disabled = false; btn.innerHTML = orig; });
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
            return { x: Math.random()*W*1.2, y: Math.random()*H*.6,
                     r: 50+Math.random()*110, dx: .12+Math.random()*.2,
                     alpha: .05+Math.random()*.1 };
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
            clouds.forEach(c => { drawCloud(c); c.x += c.dx;
                if (c.x-c.r > W*1.2) { c.x=-c.r*2; c.y=Math.random()*H*.6; } });
            requestAnimationFrame(anim);
        })();
    }

    /* ---- Bubbles ---- */
    function spawnBubble() {
        const el = document.createElement('div');
        el.className = 'bubble';
        const size = 4+Math.random()*14, dur = 8+Math.random()*12;
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
    }, { threshold: 0.06, rootMargin: '0px 0px -20px 0px' });
    document.querySelectorAll('.reveal, .stagger-children').forEach(el => io.observe(el));

    /* ---- Article text headings — animated underline ---- */
    document.querySelectorAll('.article-text h2').forEach(h => {
        h.style.transition = 'padding-left .3s, border-left-width .3s';
    });

    /* ---- Image subtle parallax on scroll ---- */
    const heroImg = document.querySelector('.article-image-wrap');
    if (heroImg) {
        window.addEventListener('scroll', () => {
            const rect = heroImg.getBoundingClientRect();
            const py = Math.max(0, Math.min(1, 1 - rect.bottom / window.innerHeight));
            heroImg.querySelector('img').style.transform = `scale(1.04) translateY(${py * -12}px)`;
        }, { passive: true });
    }

    /* ---- Related item hover ripple ---- */
    document.querySelectorAll('.related-item').forEach(item => {
        item.style.position = 'relative';
        item.style.overflow = 'hidden';
        item.addEventListener('click', function (e) {
            const rect = item.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height) * 1.5;
            const rip  = document.createElement('span');
            rip.style.cssText = [
                'position:absolute','border-radius:50%',
                `width:${size}px`,`height:${size}px`,
                `left:${e.clientX-rect.left-size/2}px`,
                `top:${e.clientY-rect.top-size/2}px`,
                'background:rgba(125,211,252,.22)',
                'transform:scale(0)',
                'animation:rippleOut .6s linear',
                'pointer-events:none','z-index:5',
            ].join(';');
            item.appendChild(rip);
            rip.addEventListener('animationend', () => rip.remove());
        });
    });

    /* ---- Inject ripple keyframe if missing ---- */
    if (!document.querySelector('#rippleStyle')) {
        const s = document.createElement('style');
        s.id = 'rippleStyle';
        s.textContent = '@keyframes rippleOut { to { transform:scale(4); opacity:0; } }';
        document.head.appendChild(s);
    }

    /* ---- Reading progress bar ---- */
    const bar = document.createElement('div');
    bar.style.cssText = [
        'position:fixed','top:0','left:0','height:3px','width:0%',
        'background:linear-gradient(90deg,#0369a1,#38bdf8)',
        'z-index:9999','border-radius:0 3px 3px 0',
        'transition:width .1s linear','pointer-events:none',
        'box-shadow:0 0 8px rgba(14,165,233,.5)',
    ].join(';');
    document.body.appendChild(bar);

    const article = document.querySelector('.article-text');
    if (article) {
        window.addEventListener('scroll', () => {
            const rect   = article.getBoundingClientRect();
            const total  = article.offsetHeight;
            const read   = Math.max(0, -rect.top);
            const pct    = Math.min(100, (read / total) * 100);
            bar.style.width = pct + '%';
        }, { passive: true });
    }

})();
</script>
@endpush
