@extends('layouts.app')
@section('title', 'Sản phẩm yêu thích - ElectronicShop')

@push('styles')
<style>
/* ============================================================
   PAGE BACKGROUND — sky gradient (khớp các trang profile khác)
   ============================================================ */
body {
    background: linear-gradient(180deg,
        #bae6fd 0%, #e0f2fe 18%, #f0f9ff 38%,
        #e0f2fe 62%, #bae6fd 100%) fixed;
    background-attachment: fixed;
}
#sky-canvas {
    position: fixed; inset: 0; width: 100%; height: 100%;
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
    opacity: 0; transform: translateY(26px);
    transition: opacity .6s cubic-bezier(.16,1,.3,1), transform .6s cubic-bezier(.16,1,.3,1);
}
.reveal.revealed { opacity: 1; transform: translateY(0); }

/* ============================================================
   PAGE WRAPPER
   ============================================================ */
.profile-page-wrap {
    max-width: 1200px;
    margin: 0 auto;
    padding: 32px 16px 64px;
    position: relative;
    z-index: 1;
}

/* ============================================================
   BREADCRUMB
   ============================================================ */
.breadcrumb-row {
    font-size: 13px; color: #0369a1;
    margin-bottom: 24px;
}
.breadcrumb-row a { color: #0c4a6e; font-weight: 600; text-decoration: none; }
.breadcrumb-row a:hover { text-decoration: underline; }

/* ============================================================
   LAYOUT (sidebar + content)
   ============================================================ */
.profile-layout {
    display: grid;
    grid-template-columns: 260px 1fr;
    gap: 28px;
    align-items: start;
}
@media (max-width: 900px) {
    .profile-layout { grid-template-columns: 1fr; }
}

/* ============================================================
   CONTENT CARD
   ============================================================ */
.content-card {
    background: rgba(255,255,255,.72);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border: 1px solid rgba(186,230,253,.6);
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(14,116,144,.08);
    padding: 28px 32px;
}

.content-card h1 {
    font-size: 22px;
    font-weight: 800;
    color: #0c4a6e;
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 6px;
}
.content-card h1 i { color: #ef4444; font-size: 20px; }
.content-card .sub {
    font-size: 13.5px; color: #0369a1;
    margin-bottom: 24px;
}

/* ============================================================
   WISHLIST GRID
   ============================================================ */
.wishlist-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
}

/* ============================================================
   PRODUCT CARD (style riêng trong trang wishlist)
   ============================================================ */
.wl-card {
    background: rgba(255,255,255,.85);
    border-radius: 16px;
    border: 1px solid rgba(186,230,253,.55);
    box-shadow: 0 4px 18px rgba(14,116,144,.07);
    overflow: hidden;
    transition: transform .25s, box-shadow .25s;
    position: relative;
    display: flex;
    flex-direction: column;
}
.wl-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 32px rgba(14,116,144,.15);
}

.wl-card-img {
    width: 100%;
    aspect-ratio: 1;
    background: #f0f9ff;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}
.wl-card-img img {
    width: 100%; height: 100%;
    object-fit: contain;
    transition: transform .35s;
}
.wl-card:hover .wl-card-img img { transform: scale(1.06); }

.wl-card-body {
    padding: 12px 14px 14px;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.wl-card-name {
    font-size: 14px;
    font-weight: 600;
    color: #0c4a6e;
    line-height: 1.35;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.wl-card-price {
    font-size: 16px;
    font-weight: 700;
    color: #0369a1;
}
.wl-card-stars {
    font-size: 12px;
    color: #f59e0b;
}
.wl-card-stars span { color: #94a3b8; font-size: 11px; }

.wl-card-actions {
    display: flex;
    gap: 8px;
    margin-top: auto;
    padding-top: 10px;
}
.wl-btn-view {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 12px;
    border-radius: 10px;
    background: linear-gradient(135deg, #0ea5e9, #0369a1);
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: opacity .2s, transform .15s;
}
.wl-btn-view:hover { opacity: .88; transform: translateY(-1px); color: #fff; }

.wl-btn-remove {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    border: 1.5px solid rgba(239,68,68,.4);
    background: rgba(254,226,226,.6);
    color: #ef4444;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    cursor: pointer;
    transition: background .2s, transform .15s;
    flex-shrink: 0;
}
.wl-btn-remove:hover { background: rgba(239,68,68,.15); transform: scale(1.07); }

/* Badge giảm giá */
.wl-badge {
    position: absolute;
    top: 10px; left: 10px;
    background: linear-gradient(135deg, #f59e0b, #ef4444);
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 20px;
    z-index: 2;
}

/* ============================================================
   EMPTY STATE
   ============================================================ */
.empty-wishlist {
    text-align: center;
    padding: 60px 20px;
    color: #64748b;
}
.empty-wishlist i {
    font-size: 56px;
    color: #e2e8f0;
    margin-bottom: 18px;
    display: block;
}
.empty-wishlist h3 {
    font-size: 20px;
    font-weight: 700;
    color: #475569;
    margin-bottom: 8px;
}
.empty-wishlist p { font-size: 14px; margin-bottom: 22px; }
.empty-wishlist a {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 28px;
    border-radius: 12px;
    background: linear-gradient(135deg, #0ea5e9, #0369a1);
    color: #fff;
    font-weight: 600;
    font-size: 15px;
    text-decoration: none;
    transition: opacity .2s;
}
.empty-wishlist a:hover { opacity: .88; color: #fff; }

/* ============================================================
   PAGINATION
   ============================================================ */
.pagination-wrap {
    margin-top: 32px;
    display: flex;
    justify-content: center;
}
.pagination { display: flex; gap: 6px; list-style: none; padding: 0; margin: 0; }
.page-item .page-link {
    display: flex; align-items: center; justify-content: center;
    width: 38px; height: 38px;
    border-radius: 10px;
    background: rgba(255,255,255,.7);
    border: 1px solid rgba(186,230,253,.6);
    color: #0369a1;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    transition: background .2s;
}
.page-item.active .page-link, .page-item .page-link:hover {
    background: linear-gradient(135deg, #0ea5e9, #0369a1);
    color: #fff;
    border-color: transparent;
}
.page-item.disabled .page-link { opacity: .45; pointer-events: none; }

/* ============================================================
   TOAST NOTIFICATION
   ============================================================ */
.wl-toast {
    position: fixed;
    bottom: 28px; right: 28px;
    background: rgba(15,23,42,.92);
    color: #fff;
    padding: 12px 20px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 10px;
    z-index: 9999;
    opacity: 0;
    transform: translateY(12px);
    transition: opacity .3s, transform .3s;
    pointer-events: none;
}
.wl-toast.show { opacity: 1; transform: translateY(0); }
.wl-toast i { color: #34d399; font-size: 16px; }
.wl-toast.error i { color: #f87171; }

@media (max-width: 600px) {
    .wishlist-grid { grid-template-columns: repeat(2, 1fr); gap: 14px; }
    .content-card { padding: 20px 16px; }
    .wl-btn-view { font-size: 12px; padding: 7px 8px; }
}
</style>
@endpush

@section('content')
<canvas id="sky-canvas"></canvas>

<div class="profile-page-wrap">

    {{-- Breadcrumb --}}
    <div class="breadcrumb-row reveal">
        <a href="{{ route('home') }}">Trang chủ</a>
        <span class="mx-2">/</span>
        <a href="{{ route('profile.account') }}">Tài khoản</a>
        <span class="mx-2">/</span>
        <span>Yêu thích</span>
    </div>

    <div class="profile-layout">

        {{-- ===== SIDEBAR ===== --}}
        <aside class="reveal">
            @include('profile.sidebar')
        </aside>

        {{-- ===== MAIN CONTENT ===== --}}
        <main>
            <div class="content-card reveal">

                <h1>
                    <i class="fas fa-heart"></i>
                    Sản phẩm yêu thích
                </h1>
                <p class="sub">
                    {{ $products->total() }} sản phẩm đang được lưu trong danh sách yêu thích của bạn
                </p>

                @if($products->isEmpty())
                    <div class="empty-wishlist">
                        <i class="far fa-heart"></i>
                        <h3>Chưa có sản phẩm nào</h3>
                        <p>Hãy khám phá và thêm những sản phẩm yêu thích vào đây nhé!</p>
                        <a href="{{ route('products.index') }}">
                            <i class="fas fa-search"></i> Khám phá sản phẩm
                        </a>
                    </div>
                @else
                    <div class="wishlist-grid stagger-children" id="wishlistGrid">
                        @foreach($products as $product)
                        <div class="wl-card" id="wl-product-{{ $product->id }}">
                            @if($product->discount_percent > 0)
                                <span class="wl-badge">-{{ $product->discount_percent }}%</span>
                            @endif

                            <a href="{{ route('products.show', $product->slug) }}" class="wl-card-img">
                                @if($product->first_image)
                                    <img src="{{ $product->first_image }}" alt="{{ $product->name }}" loading="lazy">
                                @else
                                    <i class="fas fa-image fa-3x" style="color:#cbd5e1"></i>
                                @endif
                            </a>

                            <div class="wl-card-body">
                                <div class="wl-card-name">{{ $product->name }}</div>

                                <div class="wl-card-price">
                                    @if($product->has_price_range)
                                        Từ {{ number_format($product->min_price) }}đ
                                    @else
                                        {{ number_format($product->sale_price) }}đ
                                    @endif
                                </div>

                                <div class="wl-card-stars">
                                    @for($i=1; $i<=5; $i++)
                                        {{ $i <= round($product->avg_rating) ? '★' : '☆' }}
                                    @endfor
                                    <span>({{ $product->reviews_count }})</span>
                                </div>

                                <div class="wl-card-actions">
                                    <a href="{{ route('products.show', $product->slug) }}" class="wl-btn-view">
                                        <i class="fas fa-eye"></i> Xem
                                    </a>
                                    <button class="wl-btn-remove"
                                            data-product-id="{{ $product->id }}"
                                            data-url="{{ route('wishlist.toggle', $product->id) }}"
                                            title="Bỏ yêu thích">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if($products->hasPages())
                    <div class="pagination-wrap reveal">
                        <nav aria-label="Phân trang">
                            <ul class="pagination">
                                @if($products->onFirstPage())
                                    <li class="page-item disabled"><span class="page-link">&lsaquo;</span></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $products->previousPageUrl() }}" rel="prev">&lsaquo;</a></li>
                                @endif

                                @foreach($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                                    @if($page == $products->currentPage())
                                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                                    @else
                                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                                    @endif
                                @endforeach

                                @if($products->hasMorePages())
                                    <li class="page-item"><a class="page-link" href="{{ $products->nextPageUrl() }}" rel="next">&rsaquo;</a></li>
                                @else
                                    <li class="page-item disabled"><span class="page-link">&rsaquo;</span></li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                    @endif
                @endif

            </div>
        </main>

    </div>
</div>

{{-- Toast --}}
<div class="wl-toast" id="wlToast">
    <i class="fas fa-check-circle"></i>
    <span id="wlToastMsg"></span>
</div>
@endsection

@push('scripts')
<script>
// ─── Sky canvas ───────────────────────────────────────────────────────
(function() {
    const canvas = document.getElementById('sky-canvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    let W, H, clouds = [];

    function resize() { W = canvas.width = window.innerWidth; H = canvas.height = window.innerHeight; }
    function initClouds() {
        clouds = Array.from({ length: 7 }, () => ({
            x: Math.random() * W, y: Math.random() * H * .6,
            r: 60 + Math.random() * 90, vx: .08 + Math.random() * .12, op: .12 + Math.random() * .14
        }));
    }
    function draw() {
        ctx.clearRect(0, 0, W, H);
        clouds.forEach(c => {
            const g = ctx.createRadialGradient(c.x, c.y, 0, c.x, c.y, c.r);
            g.addColorStop(0, `rgba(255,255,255,${c.op})`);
            g.addColorStop(1, 'rgba(255,255,255,0)');
            ctx.beginPath(); ctx.arc(c.x, c.y, c.r, 0, Math.PI * 2);
            ctx.fillStyle = g; ctx.fill();
            c.x += c.vx;
            if (c.x - c.r > W) c.x = -c.r;
        });
        requestAnimationFrame(draw);
    }
    resize(); initClouds(); draw();
    window.addEventListener('resize', () => { resize(); initClouds(); });
})();

// ─── Floating bubbles ─────────────────────────────────────────────────
(function() {
    const sizes = [24, 32, 18, 40, 26, 36, 22, 30];
    sizes.forEach((s, i) => {
        const b = document.createElement('div');
        b.className = 'bubble';
        Object.assign(b.style, {
            width: s + 'px', height: s + 'px',
            left: (10 + i * 11) + '%',
            bottom: '-60px',
            animationDuration: (12 + i * 3.5) + 's',
            animationDelay: (i * 2.1) + 's',
        });
        document.body.appendChild(b);
    });
})();

// ─── Scroll reveal ────────────────────────────────────────────────────
const revealEls = document.querySelectorAll('.reveal, .stagger-children');
const io = new IntersectionObserver(entries => {
    entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('revealed'); io.unobserve(e.target); } });
}, { threshold: .08 });
revealEls.forEach(el => io.observe(el));

// ─── Toast helper ─────────────────────────────────────────────────────
let toastTimer;
function showToast(msg, isError = false) {
    const t = document.getElementById('wlToast');
    const icon = t.querySelector('i');
    document.getElementById('wlToastMsg').textContent = msg;
    t.classList.toggle('error', isError);
    icon.className = isError ? 'fas fa-times-circle' : 'fas fa-check-circle';
    t.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => t.classList.remove('show'), 2800);
}

// ─── Remove from wishlist ─────────────────────────────────────────────
document.querySelectorAll('.wl-btn-remove').forEach(btn => {
    btn.addEventListener('click', function() {
        const productId = this.dataset.productId;
        const url       = this.dataset.url;
        const card      = document.getElementById('wl-product-' + productId);

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        })
        .then(res => res.json())
        .then(data => {
            if (!data.wishlisted) {
                // Animate out card
                card.style.transition = 'opacity .35s, transform .35s';
                card.style.opacity = '0';
                card.style.transform = 'scale(.92)';
                setTimeout(() => {
                    card.remove();
                    // Update count
                    const sub = document.querySelector('.content-card .sub');
                    if (sub) {
                        const remaining = document.querySelectorAll('.wl-card').length;
                        sub.textContent = remaining + ' sản phẩm đang được lưu trong danh sách yêu thích của bạn';
                        // Show empty state if no cards left
                        if (remaining === 0) location.reload();
                    }
                }, 360);
                showToast('Đã bỏ khỏi danh sách yêu thích');
            }
        })
        .catch(() => showToast('Có lỗi xảy ra, vui lòng thử lại', true));
    });
});
</script>
@endpush
