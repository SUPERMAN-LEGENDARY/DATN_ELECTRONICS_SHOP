@extends('layouts.app')
@section('title', 'Đánh giá của tôi - ElectronicShop')

@push('styles')
<style>
/* ============================================================
   PAGE BACKGROUND — Samsung Minimalist
   ============================================================ */
body {
    background-color: #f4f4f4;
    color: #000;
    font-family: 'SamsungOne', 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
}

/* ============================================================
   SCROLL REVEAL
   ============================================================ */
.reveal {
    opacity: 0; transform: translateY(20px);
    transition: opacity .6s cubic-bezier(.16,1,.3,1), transform .6s cubic-bezier(.16,1,.3,1);
}
.reveal.revealed { opacity: 1; transform: translateY(0); }

.stagger-reviews > * {
    opacity: 0; transform: translateY(15px);
    transition: opacity .5s cubic-bezier(.16,1,.3,1), transform .5s cubic-bezier(.16,1,.3,1);
}
.stagger-reviews.revealed > *:nth-child(1)  { opacity:1; transform:none; transition-delay:.04s; }
.stagger-reviews.revealed > *:nth-child(2)  { opacity:1; transform:none; transition-delay:.10s; }
.stagger-reviews.revealed > *:nth-child(3)  { opacity:1; transform:none; transition-delay:.16s; }
.stagger-reviews.revealed > *:nth-child(4)  { opacity:1; transform:none; transition-delay:.22s; }
.stagger-reviews.revealed > *:nth-child(n+5){ opacity:1; transform:none; transition-delay:.28s; }

/* ============================================================
   PAGE WRAPPER
   ============================================================ */
.myreviews-page {
    min-height: 100vh;
    padding: 40px 0 80px;
    position: relative; z-index: 1;
}
.myreviews-container {
    max-width: 900px; margin: 0 auto; padding: 0 16px;
    position: relative; z-index: 1;
}

/* ============================================================
   BACK BUTTON
   ============================================================ */
.myreviews-toprow {
    display: flex; align-items: center; margin-bottom: 18px;
}
.btn-back-profile {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 9px 18px; border-radius: 10px;
    background: #ffffff;
    border: 1px solid #ebebeb;
    color: #000; font-weight: 700; font-size: 13.5px;
    text-decoration: none;
    transition: background .2s, transform .18s, box-shadow .2s, border-color .2s;
    box-shadow: 0 2px 10px rgba(0,0,0,.03);
}
.btn-back-profile:hover {
    background: #f4f4f4; color: #000;
    border-color: #d0d0d0;
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(0,0,0,.06);
}

/* ============================================================
   MAIN CARD — Clean & Flat
   ============================================================ */
.myreviews-card {
    background: #ffffff;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    border: 1px solid #ebebeb;
}

.myreviews-card-header {
    background: #ffffff;
    padding: 24px 32px;
    border-bottom: 1px solid #ebebeb;
    display: flex; align-items: center; gap: 12px;
}
.myreviews-card-header h4 {
    margin: 0; font-size: 20px; font-weight: 700; color: #000;
    display: flex; align-items: center; gap: 12px;
}
.myreviews-card-header .header-icon {
    width: 36px; height: 36px; border-radius: 50%;
    background: #000;
    color: #fff; font-size: 14px;
    display: flex; align-items: center; justify-content: center;
}

.myreviews-card-body { padding: 28px 32px; }
@media (max-width: 768px) { .myreviews-card-body { padding: 20px; } }

/* ============================================================
   REVIEW ITEM CARD
   ============================================================ */
.review-item {
    background: #ffffff;
    border: 1px solid #ebebeb !important;
    border-radius: 16px !important;
    padding: 20px 24px;
    margin-bottom: 16px;
    position: relative;
    transition: transform .25s, box-shadow .25s, border-color .25s;
}
.review-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.06);
    border-color: #000 !important;
}
.review-item:last-child { margin-bottom: 0; }

/* product row */
.review-product-row { display: flex; gap: 16px; align-items: flex-start; }

.review-thumb {
    width: 80px; height: 80px; flex-shrink: 0;
    object-fit: contain; border-radius: 12px;
    border: 1px solid #ebebeb;
    background: #f9f9f9;
    padding: 8px; box-sizing: border-box;
    transition: transform .3s;
}
.review-item:hover .review-thumb { transform: scale(1.05); }

.review-product-name {
    font-size: 16px; font-weight: 700; color: #000;
    text-decoration: none; margin-bottom: 6px; display: block;
    transition: color .15s;
}
.review-product-name:hover { color: #2189ff; } /* Samsung link blue */

/* Star display */
.star-display {
    display: flex; gap: 2px; margin-bottom: 8px; align-items: center;
}
.star-display .star {
    font-size: 18px; line-height: 1;
    transition: transform .15s;
}
/* Black stars for modern premium look */
.star-display .star.filled { color: #000; }
.star-display .star.empty  { color: #dfdfdf; }
.star-display:hover .star.filled { transform: scale(1.1); }

.review-rating-num {
    font-size: 12px; font-weight: 700; color: #000;
    background: #f4f4f4; 
    border-radius: 20px; padding: 2px 8px; margin-left: 8px;
}

.review-content {
    font-size: 14px; color: #333; margin-bottom: 8px; line-height: 1.6;
}
.review-date {
    font-size: 12px; color: #888;
    display: flex; align-items: center; gap: 6px;
}

/* top-right badge */
.review-badges { display: flex; align-items: flex-start; flex-shrink: 0; }
.badge-pending {
    display: inline-flex; align-items: center; gap: 6px;
    background: #f4f4f4; color: #555;
    padding: 6px 12px; border-radius: 20px;
    font-size: 12px; font-weight: 700;
}

/* ============================================================
   ADMIN REPLY
   ============================================================ */
.admin-reply {
    margin-top: 16px; padding: 16px 20px;
    background: #f8f9fa;
    border-left: 3px solid #000;
    border-radius: 0 12px 12px 0; 
    font-size: 14px;
    color: #333; line-height: 1.6;
    animation: replySlide .4s cubic-bezier(.16,1,.3,1);
}
@keyframes replySlide { from{opacity:0;transform:translateY(-8px)} to{opacity:1;transform:none} }
.admin-reply strong {
    display: flex; align-items: center; gap: 8px;
    font-size: 13px; font-weight: 700; color: #000; margin-bottom: 8px;
}

/* ============================================================
   EMPTY STATE
   ============================================================ */
.reviews-empty {
    text-align: center; padding: 80px 20px;
}
.reviews-empty .empty-icon {
    font-size: 64px; display: block; margin-bottom: 24px;
    color: #000;
}
.reviews-empty h5 { font-size: 24px; font-weight: 700; color: #000; margin-bottom: 12px; }
.reviews-empty p  { color: #555; font-size: 15px; margin-bottom: 32px; }
.btn-explore {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 14px 32px; border-radius: 30px; /* Capsule shape */
    background: #000;
    color: #fff; font-weight: 700; font-size: 14px;
    text-decoration: none;
    transition: background .2s, transform .18s;
}
.btn-explore:hover { background: #333; color:#fff; }

/* ============================================================
   PAGINATION
   ============================================================ */
.pagination-wrap { margin-top: 32px; display: flex; justify-content: center; }
.pagination-wrap .pagination .page-link {
    border: 1px solid #ebebeb;
    color: #000;
    background: #fff;
    border-radius: 50% !important; /* Circular buttons */
    margin: 0 4px; font-weight: 600; font-size: 14px;
    width: 40px; height: 40px;
    display: flex; align-items: center; justify-content: center;
    transition: all .2s;
}
.pagination-wrap .pagination .page-link:hover { background: #f4f4f4; color: #000; }
.pagination-wrap .pagination .page-item.active .page-link {
    background: #000;
    border-color: #000; color: #fff;
}

/* ============================================================
   MOBILE
   ============================================================ */
@media (max-width: 576px) {
    .myreviews-page { padding: 20px 0 50px; }
    .myreviews-container { padding: 0 12px; gap: 16px; }

    .myreviews-toprow { margin-bottom: 12px; }
    .btn-back-profile { padding: 8px 14px; font-size: 12.5px; }

    .myreviews-card { border-radius: 16px; }
    .myreviews-card-header { padding: 16px 18px; }
    .myreviews-card-header h4 { font-size: 17px; gap: 8px; }
    .myreviews-card-header .header-icon { width: 30px; height: 30px; font-size: 12px; }
    .myreviews-card-body { padding: 14px; }

    .review-item { padding: 14px 16px; margin-bottom: 12px; border-radius: 14px !important; }
    .review-product-row { gap: 12px; }
    .review-thumb { width: 60px; height: 60px; border-radius: 10px; padding: 6px; }
    .review-product-name { font-size: 14px; margin-bottom: 4px; }

    .star-display { flex-wrap: wrap; gap: 1px; margin-bottom: 6px; }
    .star-display .star { font-size: 15px; }
    .review-rating-num { font-size: 11px; padding: 1px 7px; }

    .review-content { font-size: 13px; margin-bottom: 6px; }
    .review-date { font-size: 11px; }

    .badge-pending { font-size: 11px; padding: 5px 10px; }
    .review-badges { width: 100%; justify-content: flex-end; }

    .admin-reply { padding: 12px 14px; margin-top: 12px; font-size: 13px; }
    .admin-reply strong { font-size: 12px; }

    .reviews-empty { padding: 56px 16px; }
    .reviews-empty .empty-icon { font-size: 48px; margin-bottom: 16px; }
    .reviews-empty h5 { font-size: 18px; margin-bottom: 8px; }
    .reviews-empty p { font-size: 13px; margin-bottom: 22px; }
    .btn-explore { padding: 11px 24px; font-size: 13px; }

    .pagination-wrap { margin-top: 20px; }
    .pagination-wrap .pagination { flex-wrap: wrap; justify-content: center; }
    .pagination-wrap .pagination .page-link { width: 34px; height: 34px; font-size: 12.5px; }
}
</style>
@endpush

@section('content')

<div class="myreviews-page">
<div class="myreviews-container">

    {{-- ===== CONTENT ===== --}}
    <div class="reveal">

        {{-- Back to profile --}}
        <div class="myreviews-toprow">
            <a href="{{ route('profile') }}" class="btn-back-profile">
                <i class="fas fa-arrow-left"></i> Quay lại Hồ sơ
            </a>
        </div>

        <div class="myreviews-card">

            {{-- Header --}}
            <div class="myreviews-card-header">
                <h4>
                    <span class="header-icon"><i class="fas fa-star"></i></span>
                    Đánh giá của tôi
                </h4>
            </div>

            {{-- Body --}}
            <div class="myreviews-card-body">

                @forelse($reviews as $review)
                <div class="review-item stagger-reviews">

                    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap">

                        {{-- Product info + stars + content --}}
                        <div class="review-product-row" style="flex:1; min-width:0">
                            @if($review->product && $review->product->first_image)
                            <img src="{{ $review->product->first_image }}"
                                 class="review-thumb" alt="{{ $review->product->name ?? '' }}">
                            @endif
                            <div style="min-width:0">
                                @if($review->product)
                                <a href="{{ route('products.show', $review->product->slug) }}"
                                   class="review-product-name">
                                    {{ $review->product->name }}
                                </a>
                                @else
                                <span class="review-product-name" style="color:#94a3b8;cursor:default">
                                    Sản phẩm đã xóa
                                </span>
                                @endif

                                {{-- Stars --}}
                                <div class="star-display">
                                    @for($i = 1; $i <= 5; $i++)
                                    <span class="star {{ $i <= $review->rating ? 'filled' : 'empty' }}">★</span>
                                    @endfor
                                    <span class="review-rating-num">{{ $review->rating }}/5</span>
                                </div>

                                <p class="review-content">
                                    "{{ $review->content ?: 'Không có nhận xét.' }}"
                                </p>

                                <div class="review-date">
                                    <i class="far fa-clock"></i>
                                    {{ optional($review->created_at)->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>

                        {{-- Pending badge --}}
                        @if(!$review->is_visible)
                        <div class="review-badges">
                            <span class="badge-pending">
                                <i class="fas fa-clock"></i> Đang chờ kiểm duyệt
                            </span>
                        </div>
                        @endif

                    </div>

                    {{-- Admin reply --}}
                    @if($review->admin_reply)
                    <div class="admin-reply">
                        <strong>
                            <i class="fas fa-store"></i> Phản hồi từ ElectronicShop:
                        </strong>
                        <p style="margin:0">{{ $review->admin_reply }}</p>
                    </div>
                    @endif

                </div>
                @empty

                <div class="reviews-empty">
                    <i class="far fa-star empty-icon"></i>
                    <h5>Bạn chưa có đánh giá nào</h5>
                    <p>Hãy mua sắm và chia sẻ cảm nhận của bạn về sản phẩm!</p>
                    <a href="{{ route('products.index') }}" class="btn-explore">
                        Khám phá sản phẩm
                    </a>
                </div>

                @endforelse

                {{-- Pagination --}}
                @if($reviews->hasPages())
                <div class="pagination-wrap">
                    {{ $reviews->links() }}
                </div>
                @endif

            </div>
        </div>
    </div>

</div>{{-- /.myreviews-container --}}
</div>{{-- /.myreviews-page --}}
@endsection

@push('scripts')
<script>
(function () {
    /* ---- Scroll Reveal ---- */
    const io = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) { e.target.classList.add('revealed'); io.unobserve(e.target); }
        });
    }, { threshold: 0.05, rootMargin: '0px 0px -20px 0px' });
    document.querySelectorAll('.reveal, .stagger-reviews').forEach(el => io.observe(el));

    /* ---- Star sparkle on hover ---- */
    document.querySelectorAll('.star-display').forEach(row => {
        row.addEventListener('mouseenter', () => {
            row.querySelectorAll('.star.filled').forEach((s, i) => {
                setTimeout(() => {
                    s.style.transform = 'scale(1.2) rotate(5deg)';
                    setTimeout(() => { s.style.transform = ''; }, 200);
                }, i * 40);
            });
        });
    });

})();
</script>
@endpush