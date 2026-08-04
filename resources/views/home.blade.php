@extends('layouts.app')
@section('title', 'ElectronicShop - Trang chủ')

@push('styles')
<style>
/* ============================================================
   HOMEPAGE — SAMSUNG STYLE
   ============================================================ */

.sm-page { overflow-x: hidden; }

/* Khung nội dung chuẩn Samsung */
.sm-wrap { max-width: 1440px; margin: 0 auto; padding: 0 24px; }
.sm-wrap--narrow { max-width: 1120px; }

/* Eyebrow (nhãn xanh nhỏ phía trên tiêu đề) */
.sm-eyebrow {
    font-size: 13px; font-weight: 700; letter-spacing: .06em;
    text-transform: uppercase; color: var(--sm-blue);
    margin: 0 0 14px;
}

/* Tiêu đề khổng lồ, canh giữa */
.sm-h2 {
    font-size: clamp(30px, 5.4vw, 62px);
    line-height: 1.08;
    margin: 0 0 18px;
    text-wrap: balance;
}
.sm-h3 {
    font-size: clamp(24px, 3.4vw, 40px);
    line-height: 1.12; margin: 0 0 14px;
    text-wrap: balance;
}
.sm-lead {
    font-size: clamp(15px, 1.3vw, 18px);
    line-height: 1.65; color: var(--sm-gray);
    max-width: 62ch; margin: 0 auto;
}
.sm-center { text-align: center; }

/* Khoảng nghỉ lớn giữa các section */
.sm-section { padding: clamp(64px, 9vw, 130px) 0; }
.sm-section--tight { padding: clamp(44px, 6vw, 80px) 0; }
.sm-section--surface { background: var(--sm-surface); }
.sm-section--dark { background: var(--sm-black); color: #fff; }
.sm-section--dark .sm-h2, .sm-section--dark .sm-h3 { color: #fff; }
.sm-section--dark .sm-lead { color: rgba(255,255,255,.72); }

/* ============================================================
   1. HERO VIDEO (full-bleed cinematic)
   ============================================================ */
.sm-hero {
    position: relative;
    height: min(100svh, 900px);
    min-height: 560px;
    display: flex; align-items: center; justify-content: center;
    overflow: hidden;
    background: #0b0b0f;
    isolation: isolate;
}
.sm-hero video,
.sm-hero .sm-hero-fallback {
    position: absolute; inset: 0;
    width: 100%; height: 100%;
    object-fit: cover;
    z-index: -2;
}
.sm-hero::after {
    content: ''; position: absolute; inset: 0; z-index: -1;
    background:
        radial-gradient(120% 90% at 50% 15%, rgba(0,0,0,.05) 0%, rgba(0,0,0,.55) 70%, rgba(0,0,0,.78) 100%);
}
.sm-hero-inner {
    position: relative; z-index: 2;
    text-align: center; color: #fff;
    padding: 0 24px; max-width: 900px;
}
.sm-hero-eyebrow {
    font-size: 13px; font-weight: 700; letter-spacing: .18em; text-transform: uppercase;
    color: rgba(255,255,255,.82); margin-bottom: 18px;
}
.sm-hero h1 {
    font-size: clamp(38px, 7.6vw, 92px);
    line-height: .98; color: #fff; margin: 0 0 20px;
    text-shadow: 0 4px 40px rgba(0,0,0,.35);
    text-wrap: balance;
}
.sm-hero h1 .glow {
    background: linear-gradient(100deg, #ffffff 20%, #a9d4ff 55%, #ffffff 85%);
    -webkit-background-clip: text; background-clip: text;
    -webkit-text-fill-color: transparent;
}
.sm-hero p {
    font-size: clamp(15px, 1.5vw, 19px); line-height: 1.6;
    color: rgba(255,255,255,.86); max-width: 56ch; margin: 0 auto 34px;
}
.sm-hero-cta { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }

/* Animation vào trang cho hero */
.sm-hero-inner > * {
    opacity: 0; transform: translateY(28px);
    animation: smHeroIn .95s var(--sm-ease) forwards;
}
.sm-hero-inner > *:nth-child(1) { animation-delay: .15s; }
.sm-hero-inner > *:nth-child(2) { animation-delay: .30s; }
.sm-hero-inner > *:nth-child(3) { animation-delay: .45s; }
.sm-hero-inner > *:nth-child(4) { animation-delay: .60s; }
@keyframes smHeroIn { to { opacity: 1; transform: translateY(0); } }

/* Nút tắt/mở tiếng + play video */
.sm-hero-controls {
    position: absolute; right: 22px; bottom: 26px; z-index: 3;
    display: flex; gap: 8px;
}
.sm-hero-controls button {
    width: 42px; height: 42px; border-radius: 50%;
    background: rgba(0,0,0,.42); border: 1px solid rgba(255,255,255,.42);
    color: #fff; font-size: 14px; cursor: pointer;
    backdrop-filter: blur(6px);
    transition: all .25s var(--sm-ease);
}
.sm-hero-controls button:hover { background: #fff; color: #000; }

/* Mũi tên cuộn xuống */
.sm-scroll-cue {
    position: absolute; left: 50%; bottom: 26px; z-index: 3;
    transform: translateX(-50%);
    color: rgba(255,255,255,.85); font-size: 20px;
    animation: smBounce 2.1s var(--sm-ease) infinite;
    background: none; border: none; cursor: pointer;
}
@keyframes smBounce {
    0%,100% { transform: translate(-50%, 0); opacity: .55; }
    50%     { transform: translate(-50%, 10px); opacity: 1; }
}

/* ============================================================
   2. BANNER SLIDER (fade, dot progress)
   ============================================================ */
.sm-slider { position: relative; background: var(--sm-surface); overflow: hidden; }
.sm-slider-track { position: relative; height: clamp(380px, 46vw, 620px); }
.sm-slide {
    position: absolute; inset: 0;
    opacity: 0; visibility: hidden;
    transition: opacity .8s var(--sm-ease), visibility .8s;
    display: flex; align-items: center;
}
.sm-slide.active { opacity: 1; visibility: visible; }
.sm-slide-img-only img,
.sm-slide-bg {
    position: absolute; inset: 0; width: 100%; height: 100%;
    object-fit: cover;
}
/* Ken Burns nhẹ khi slide active */
.sm-slide.active .sm-kb { animation: smKb 8s linear forwards; }
@keyframes smKb { from { transform: scale(1.0); } to { transform: scale(1.07); } }

.sm-slide-content {
    position: relative; z-index: 2;
    max-width: 1440px; width: 100%; margin: 0 auto; padding: 0 clamp(24px, 5vw, 72px);
    display: grid; grid-template-columns: 1fr 1fr; gap: 30px; align-items: center;
}
.sm-slide-text > * {
    opacity: 0; transform: translateY(24px);
    transition: opacity .7s var(--sm-ease), transform .7s var(--sm-ease);
}
.sm-slide.active .sm-slide-text > * { opacity: 1; transform: translateY(0); }
.sm-slide.active .sm-slide-text > *:nth-child(2) { transition-delay: .1s; }
.sm-slide.active .sm-slide-text > *:nth-child(3) { transition-delay: .2s; }
.sm-slide.active .sm-slide-text > *:nth-child(4) { transition-delay: .3s; }
.sm-slide.active .sm-slide-text > *:nth-child(5) { transition-delay: .4s; }

.sm-slide-label { font-size: 13px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; margin-bottom: 12px; opacity: .8; }
.sm-slide-title { font-size: clamp(28px, 4.2vw, 56px); line-height: 1.05; margin: 0 0 14px; }
.sm-slide-desc  { font-size: clamp(14px, 1.2vw, 17px); line-height: 1.6; margin: 0 0 16px; opacity: .82; max-width: 46ch; }
.sm-slide-price { font-family: 'Manrope', sans-serif; font-size: clamp(18px, 1.8vw, 26px); font-weight: 800; margin-bottom: 22px; }
.sm-slide-visual { display: flex; justify-content: center; }
.sm-slide-visual img { max-height: clamp(260px, 34vw, 480px); width: auto; object-fit: contain; filter: drop-shadow(0 30px 60px rgba(0,0,0,.22)); }

.sm-slide-ph {
    display: flex; align-items: center; justify-content: center;
    background: #ececec; color: #b0b0b0; border-radius: var(--sm-radius);
    width: 100%; min-height: 240px;
}

/* Điều hướng slider */
.sm-slider-arrow {
    position: absolute; top: 50%; transform: translateY(-50%);
    z-index: 5; width: 46px; height: 46px; border-radius: 50%;
    background: rgba(255,255,255,.9); border: 1px solid rgba(0,0,0,.08);
    color: var(--sm-black); font-size: 15px; cursor: pointer;
    opacity: 0; transition: all .3s var(--sm-ease);
    box-shadow: 0 4px 18px rgba(0,0,0,.1);
}
.sm-slider:hover .sm-slider-arrow { opacity: 1; }
.sm-slider-arrow:hover { background: var(--sm-black); color: #fff; }
.sm-slider-arrow.left  { left: 18px; }
.sm-slider-arrow.right { right: 18px; }

.sm-dots {
    position: absolute; left: 50%; bottom: 22px; transform: translateX(-50%);
    z-index: 5; display: flex; align-items: center; gap: 8px;
    background: rgba(255,255,255,.85); backdrop-filter: blur(8px);
    padding: 8px 14px; border-radius: 999px;
    box-shadow: 0 4px 16px rgba(0,0,0,.08);
}
.sm-dot {
    width: 8px; height: 8px; border-radius: 999px;
    background: rgba(0,0,0,.24); border: none; padding: 0; cursor: pointer;
    transition: all .4s var(--sm-ease);
}
.sm-dot.active { width: 32px; background: var(--sm-black); }

/* ============================================================
   3. TRUST BAR (hairline grid)
   ============================================================ */
.sm-trust {
    display: grid; grid-template-columns: repeat(5, 1fr);
    border-top: 1px solid var(--sm-line); border-bottom: 1px solid var(--sm-line);
}
.sm-trust-item {
    padding: 26px 22px; display: flex; align-items: center; gap: 14px;
    border-left: 1px solid var(--sm-line);
    transition: background .3s var(--sm-ease);
}
.sm-trust-item:first-child { border-left: none; }
.sm-trust-item:hover { background: var(--sm-surface); }
.sm-trust-item i { font-size: 20px; color: var(--sm-black); transition: transform .35s var(--sm-ease); }
.sm-trust-item:hover i { transform: translateY(-3px) scale(1.12); }
.sm-trust-item b { display: block; font-size: 13.5px; font-weight: 700; color: var(--sm-black); }
.sm-trust-item span { font-size: 12.5px; color: var(--sm-gray); }

/* ============================================================
   4. STICKY SHOWCASE (visual dính + text cuộn)
   ============================================================ */
.sm-showcase { position: relative; }
.sm-showcase-grid { display: grid; grid-template-columns: 1fr 1fr; gap: clamp(24px, 5vw, 80px); align-items: start; }
.sm-showcase-visual {
    position: sticky; top: calc(var(--sm-header-h) + 40px);
    aspect-ratio: 4/5;
    border-radius: var(--sm-radius); overflow: hidden;
    background: var(--sm-surface);
    display: flex; align-items: center; justify-content: center;
}
.sm-showcase-visual img {
    position: absolute; inset: 0; width: 100%; height: 100%;
    object-fit: contain; padding: 8%;
    opacity: 0; transform: scale(.94);
    transition: opacity .7s var(--sm-ease), transform .7s var(--sm-ease);
}
.sm-showcase-visual img.on { opacity: 1; transform: scale(1); }
.sm-showcase-steps { display: flex; flex-direction: column; }
.sm-step {
    min-height: 72svh; display: flex; flex-direction: column; justify-content: center;
    padding: 30px 0;
    opacity: .28; transition: opacity .55s var(--sm-ease);
}
.sm-step.on { opacity: 1; }
.sm-step-num {
    font-family: 'Manrope', sans-serif; font-size: 13px; font-weight: 800;
    letter-spacing: .14em; color: var(--sm-blue); margin-bottom: 12px;
}
.sm-step h3 { font-size: clamp(24px, 3vw, 40px); line-height: 1.1; margin: 0 0 14px; }
.sm-step p { font-size: 16px; line-height: 1.7; color: var(--sm-gray); margin: 0 0 20px; max-width: 46ch; }

/* ============================================================
   5. SPEC CALLOUTS (số to + đường dẫn, kiểu 50MP)
   ============================================================ */
.sm-specs { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; }
.sm-spec {
    padding: 30px 4px; border-top: 1px solid rgba(255,255,255,.16);
}
.sm-section--dark .sm-spec-num,
.sm-spec-num {
    font-family: 'Manrope', sans-serif; font-weight: 800;
    font-size: clamp(34px, 4.6vw, 60px); line-height: 1;
    letter-spacing: -.04em; margin-bottom: 10px;
    color: #fff;
}
.sm-spec-label { font-size: 14px; color: rgba(255,255,255,.66); line-height: 1.5; }

/* ============================================================
   6. EVENT CARDS
   ============================================================ */
.sm-events { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; }
.sm-event {
    position: relative; min-height: 230px; border-radius: var(--sm-radius);
    overflow: hidden; display: flex; align-items: flex-end;
    color: #fff; isolation: isolate;
    transition: transform .45s var(--sm-ease), box-shadow .45s var(--sm-ease);
}
.sm-event:hover { transform: translateY(-6px); box-shadow: 0 20px 44px rgba(0,0,0,.18); }
.sm-event img {
    position: absolute; inset: 0; width: 100%; height: 100%;
    object-fit: cover; z-index: -2;
    transition: transform .8s var(--sm-ease);
}
.sm-event:hover img { transform: scale(1.07); }
.sm-event::after {
    content: ''; position: absolute; inset: 0; z-index: -1;
    background: linear-gradient(180deg, rgba(0,0,0,.05) 30%, rgba(0,0,0,.72) 100%);
}
.sm-event-body { padding: 24px; width: 100%; }
.sm-event-tag {
    display: inline-flex; font-size: 11px; font-weight: 800; letter-spacing: .1em;
    text-transform: uppercase; padding: 5px 11px; border-radius: 999px;
    background: rgba(255,255,255,.2); border: 1px solid rgba(255,255,255,.5);
    backdrop-filter: blur(4px); margin-bottom: 12px;
}
.sm-event-title { font-family: 'Manrope', sans-serif; font-weight: 800; font-size: 21px; line-height: 1.2; letter-spacing: -.02em; }
.sm-event-offer { font-size: 13.5px; opacity: .88; margin-top: 6px; }

/* ============================================================
   7. BRAND TICKER (cuộn ngang + kéo)
   ============================================================ */
.sm-brands-rail {
    display: flex; gap: 14px; overflow-x: auto; scroll-snap-type: x mandatory;
    padding: 8px 24px 22px; scrollbar-width: none;
    max-width: 1440px; margin: 0 auto; cursor: grab;
}
.sm-brands-rail::-webkit-scrollbar { display: none; }
.sm-brands-rail.dragging { cursor: grabbing; scroll-snap-type: none; }
.sm-brand {
    flex: 0 0 auto; scroll-snap-align: start;
    width: 138px; padding: 22px 14px; border-radius: var(--sm-radius);
    background: var(--sm-surface); text-align: center;
    transition: all .35s var(--sm-ease);
}
.sm-brand:hover { background: #fff; box-shadow: 0 14px 34px rgba(0,0,0,.1); transform: translateY(-5px); }
.sm-brand-logo {
    height: 52px; display: flex; align-items: center; justify-content: center; margin-bottom: 12px;
}
.sm-brand-logo img {
    max-height: 46px; max-width: 88px; object-fit: contain;
    filter: grayscale(1); opacity: .62;
    transition: filter .35s var(--sm-ease), opacity .35s var(--sm-ease);
}
.sm-brand:hover .sm-brand-logo img { filter: grayscale(0); opacity: 1; }
.sm-brand-initials {
    font-family: 'Manrope', sans-serif; font-weight: 800; font-size: 20px;
    width: 46px; height: 46px; border-radius: 50%;
    background: var(--sm-black); color: #fff;
    display: flex; align-items: center; justify-content: center;
}
.sm-brand-name { font-size: 13px; font-weight: 700; color: var(--sm-ink); }

/* ============================================================
   8. PRODUCT CARDS (chuẩn Samsung Shop)
   ============================================================ */
.sm-section-head {
    display: flex; align-items: flex-end; justify-content: space-between;
    gap: 20px; margin-bottom: 34px; flex-wrap: wrap;
}
.sm-section-head .sm-h3 { margin: 0; }

.sm-products { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; }
.sm-product {
    position: relative; display: flex; flex-direction: column;
    background: #fff; border-radius: var(--sm-radius);
    padding: 14px 14px 20px; color: var(--sm-ink);
    transition: transform .4s var(--sm-ease), box-shadow .4s var(--sm-ease);
}
.sm-product:hover { transform: translateY(-6px); box-shadow: 0 18px 44px rgba(0,0,0,.1); }
.sm-product-media {
    position: relative; aspect-ratio: 1/1; border-radius: 16px;
    background: var(--sm-surface); overflow: hidden; margin-bottom: 16px;
}
.sm-product-media img {
    width: 100%; height: 100%; object-fit: contain; padding: 10%;
    transition: transform .6s var(--sm-ease);
}
.sm-product:hover .sm-product-media img { transform: scale(1.07); }
.sm-product-ph {
    width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;
    color: #c2c2c2; font-size: 26px;
}
.sm-wish {
    position: absolute; top: 10px; right: 10px; z-index: 3;
    width: 34px; height: 34px; border-radius: 50%;
    background: rgba(255,255,255,.9); border: 1px solid rgba(0,0,0,.06);
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 14px; color: var(--sm-ink); cursor: pointer;
    transition: all .25s var(--sm-ease);
}
.sm-wish:hover { background: var(--sm-black); color: #fff; }
.sm-wish.on, .sm-wish.active { background: #d0021b; color: #fff; border-color: #d0021b; }
.sm-product-brand { font-size: 11.5px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: #8c8c8c; min-height: 15px; }
.sm-product-name {
    font-size: 15px; font-weight: 700; line-height: 1.35; color: var(--sm-black);
    margin: 6px 0 10px;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.sm-product-price { font-family: 'Manrope', sans-serif; font-size: 18px; font-weight: 800; letter-spacing: -.02em; color: var(--sm-black); }
.sm-stars { display: flex; align-items: center; gap: 6px; margin-top: 10px; font-size: 12.5px; color: #ffb400; letter-spacing: 1px; }
.sm-stars span { color: #8c8c8c; letter-spacing: 0; }
.sm-product-buy {
    margin-top: 16px; opacity: 0; transform: translateY(8px);
    transition: all .35s var(--sm-ease);
}
.sm-product:hover .sm-product-buy { opacity: 1; transform: translateY(0); }
@media (hover: none) { .sm-product-buy { opacity: 1; transform: none; } }

.sm-empty { grid-column: 1/-1; padding: 60px 20px; text-align: center; color: var(--sm-gray); }

/* ============================================================
   9. PROMO CARDS
   ============================================================ */
.sm-promos { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
.sm-promo {
    position: relative; overflow: hidden; border-radius: var(--sm-radius);
    min-height: 300px; padding: 32px; display: flex; flex-direction: column;
    background: var(--sm-surface);
    transition: transform .45s var(--sm-ease), box-shadow .45s var(--sm-ease);
}
.sm-promo:hover { transform: translateY(-6px); box-shadow: 0 22px 48px rgba(0,0,0,.12); }
.sm-promo--dark { background: var(--sm-black); }
.sm-promo--dark .sm-promo-tag { color: #7fbaff; }
.sm-promo--dark h3, .sm-promo--dark .sm-promo-sub { color: #fff; }
.sm-promo-tag { font-size: 11.5px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; color: var(--sm-blue); margin-bottom: 10px; }
.sm-promo h3 { font-size: clamp(22px, 2.4vw, 32px); line-height: 1.08; margin: 0 0 8px; }
.sm-promo-sub { font-size: 14px; color: var(--sm-gray); margin-bottom: 20px; }
.sm-promo .sm-btn { align-self: flex-start; position: relative; z-index: 2; }
.sm-promo img {
    position: absolute; right: -8%; bottom: -6%;
    width: 62%; max-width: 300px; object-fit: contain;
    transition: transform .7s var(--sm-ease);
    pointer-events: none;
}
.sm-promo:hover img { transform: scale(1.08) translateY(-8px) rotate(-2deg); }

/* ============================================================
   10. NEWS
   ============================================================ */
.sm-news { display: grid; grid-template-columns: repeat(3, 1fr); gap: 22px; }
.sm-news-card { display: block; color: var(--sm-ink); }
.sm-news-media {
    aspect-ratio: 16/10; border-radius: var(--sm-radius);
    overflow: hidden; background: var(--sm-surface); margin-bottom: 16px;
}
.sm-news-media img { width: 100%; height: 100%; object-fit: cover; transition: transform .7s var(--sm-ease); }
.sm-news-card:hover .sm-news-media img { transform: scale(1.06); }
.sm-news-ph { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #c2c2c2; font-size: 26px; }
.sm-news-title {
    font-family: 'Manrope', sans-serif; font-weight: 800; font-size: 19px; line-height: 1.28;
    letter-spacing: -.025em; color: var(--sm-black); margin-bottom: 8px;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.sm-news-card:hover .sm-news-title { text-decoration: underline; text-underline-offset: 4px; }
.sm-news-excerpt {
    font-size: 14px; line-height: 1.6; color: var(--sm-gray); margin-bottom: 12px;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.sm-news-meta { font-size: 12.5px; color: #8c8c8c; }

/* ============================================================
   11. FAQ ACCORDION
   ============================================================ */
.sm-faq { max-width: 860px; margin: 0 auto; border-top: 1px solid var(--sm-line); }
.sm-faq-item { border-bottom: 1px solid var(--sm-line); }
.sm-faq-q {
    width: 100%; background: none; border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: space-between; gap: 20px;
    padding: 24px 4px; text-align: left;
    font-family: 'Manrope', sans-serif; font-weight: 700; font-size: clamp(16px, 1.6vw, 20px);
    letter-spacing: -.02em; color: var(--sm-black);
}
.sm-faq-q i { font-size: 14px; transition: transform .35s var(--sm-ease); flex-shrink: 0; }
.sm-faq-item.open .sm-faq-q i { transform: rotate(180deg); }
.sm-faq-a {
    max-height: 0; overflow: hidden;
    transition: max-height .45s var(--sm-ease), opacity .35s var(--sm-ease), padding .35s var(--sm-ease);
    opacity: 0; padding: 0 4px;
}
.sm-faq-item.open .sm-faq-a { opacity: 1; padding: 0 4px 24px; }
.sm-faq-a p { font-size: 15px; line-height: 1.75; color: var(--sm-gray); margin: 0; max-width: 68ch; }

/* ============================================================
   12. CTA BAND
   ============================================================ */
.sm-cta { text-align: center; }
.sm-cta .sm-btn { margin: 6px; }

/* ============================================================
   13. SCROLL REVEAL
   ============================================================ */
.sm-reveal { opacity: 0; transform: translateY(42px); transition: opacity .85s var(--sm-ease), transform .85s var(--sm-ease); }
.sm-reveal.in { opacity: 1; transform: none; }

.sm-stagger > * { opacity: 0; transform: translateY(34px); transition: opacity .7s var(--sm-ease), transform .7s var(--sm-ease); }
.sm-stagger.in > * { opacity: 1; transform: none; }
.sm-stagger.in > *:nth-child(1) { transition-delay: .04s; }
.sm-stagger.in > *:nth-child(2) { transition-delay: .10s; }
.sm-stagger.in > *:nth-child(3) { transition-delay: .16s; }
.sm-stagger.in > *:nth-child(4) { transition-delay: .22s; }
.sm-stagger.in > *:nth-child(5) { transition-delay: .28s; }
.sm-stagger.in > *:nth-child(6) { transition-delay: .34s; }
.sm-stagger.in > *:nth-child(7) { transition-delay: .40s; }
.sm-stagger.in > *:nth-child(8) { transition-delay: .46s; }

/* Video card cuộn-phóng (scroll-linked) */
.sm-videocard {
    border-radius: var(--sm-radius); overflow: hidden;
    background: #000; aspect-ratio: 16/9;
    transform: scale(var(--sc, .9));
    transition: transform .12s linear;
    max-width: 1200px; margin: 0 auto;
    box-shadow: 0 30px 80px rgba(0,0,0,.16);
}
.sm-videocard video { width: 100%; height: 100%; object-fit: cover; display: block; }

/* ============================================================
   14. 3D TILT + RIPPLE (hiệu ứng nghiêng 3D & gợn sóng khi tương tác)
   ============================================================ */
.sm-product,
.sm-news-card,
.sm-event,
.sm-promo {
    will-change: transform;
    transform-style: preserve-3d;
}
.ripple-wave {
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,.5);
    transform: scale(0);
    animation: smRippleOut .6s linear;
    pointer-events: none;
    z-index: 20;
}
@keyframes smRippleOut { to { transform: scale(4); opacity: 0; } }

/* ============================================================
   15. RESPONSIVE
   ============================================================ */
@media (max-width: 1100px) {
    .sm-products { grid-template-columns: repeat(3, 1fr); }
    .sm-specs { grid-template-columns: repeat(2, 1fr); }
    .sm-trust { grid-template-columns: repeat(2, 1fr); }
    .sm-trust-item:nth-child(odd) { border-left: none; }
}
@media (max-width: 900px) {
    .sm-slide-content { grid-template-columns: 1fr; text-align: center; }
    .sm-slide-text { order: 2; }
    .sm-slide-visual { order: 1; }
    .sm-slide-desc { margin-left: auto; margin-right: auto; }
    .sm-slide .sm-btn { margin: 0 auto; }
    .sm-showcase-grid { grid-template-columns: 1fr; }
    .sm-showcase-visual { position: relative; top: auto; aspect-ratio: 1/1; }
    .sm-step { min-height: auto; padding: 22px 0; opacity: 1; }
    .sm-news { grid-template-columns: 1fr 1fr; }
    .sm-promos { grid-template-columns: 1fr; }
}
@media (max-width: 640px) {
    .sm-products { grid-template-columns: repeat(2, 1fr); gap: 12px; }
    .sm-news { grid-template-columns: 1fr; }
    .sm-trust { grid-template-columns: 1fr; }
    .sm-trust-item { border-left: none !important; border-top: 1px solid var(--sm-line); }
    .sm-trust-item:first-child { border-top: none; }
}
</style>
@endpush

@section('content')
<div class="sm-page">

    {{-- ============================================================
         HERO VIDEO
         ============================================================ --}}
    <section class="sm-hero" id="smHero">
        <video id="smHeroVideo" autoplay muted loop playsinline preload="auto"
               poster="{{ asset('images/promo-samsung.png') }}">
            <source src="https://images.samsung.com/vn/smartphones/galaxy-z-fold8/videos/galaxy-z-fold8-features-highlights-new-shape.webm?imbypass=true" type="video/webm">
            <source src="https://images.samsung.com/vn/smartphones/galaxy-z-fold8/videos/galaxy-z-fold8-features-highlights-new-shape.mp4?imbypass=true" type="video/mp4">
        </video>

        <div class="sm-hero-inner">
            <div class="sm-hero-eyebrow">ElectronicShop · Hàng chính hãng</div>
            <h1>Công nghệ mới nhất.<br><span class="glow">Trải nghiệm đỉnh cao.</span></h1>
            <p>Điện thoại, laptop, thiết bị đeo và phụ kiện chính hãng — bảo hành toàn quốc, trả góp 0%, giao nhanh trong 2 giờ.</p>
            <div class="sm-hero-cta">
                <a href="{{ route('products.index') }}" class="sm-btn sm-btn--primary">Mua ngay</a>
                <a href="#sm-highlight" class="sm-btn sm-btn--light">Khám phá thêm</a>
            </div>
        </div>

        <div class="sm-hero-controls">
            <button type="button" id="smHeroPlay" aria-label="Tạm dừng video"><i class="bi bi-pause-fill"></i></button>
            <button type="button" id="smHeroMute" aria-label="Bật tiếng"><i class="bi bi-volume-mute-fill"></i></button>
        </div>

        <button type="button" class="sm-scroll-cue" id="smScrollCue" aria-label="Cuộn xuống">
            <i class="bi bi-chevron-down"></i>
        </button>
    </section>

    {{-- ============================================================
         BANNER SLIDER
         ============================================================ --}}
    @if($banners->isNotEmpty())
    <section class="sm-slider" id="smSlider" aria-label="Khuyến mãi nổi bật">
        <div class="sm-slider-track">
            @foreach($banners as $i => $banner)
                @if($banner->isImageOnly())
                <div class="sm-slide sm-slide-img-only {{ $i === 0 ? 'active' : '' }}" data-slide="{{ $i }}">
                    @if($banner->button_link)
                    <a href="{{ $banner->button_link }}" style="position:absolute;inset:0;z-index:3"></a>
                    @endif
                    @if($banner->image)
                    <img class="sm-kb" src="{{ $banner->image }}" alt="{{ $banner->title ?: 'Khuyến mãi' }}">
                    @else
                    <div class="sm-slide-ph" style="position:absolute;inset:0"><i class="bi bi-image"></i></div>
                    @endif
                </div>
                @else
                <div class="sm-slide {{ $i === 0 ? 'active' : '' }}" data-slide="{{ $i }}"
                     style="{{ $banner->bg_color ? 'background:'.$banner->bg_color.';' : '' }}">
                    <div class="sm-slide-content" style="{{ $banner->text_color ? 'color:'.$banner->text_color.';' : '' }}">
                        <div class="sm-slide-text">
                            @if($banner->label)
                            <div class="sm-slide-label" style="{{ $banner->text_color ? 'color:'.$banner->text_color.';' : '' }}">{{ $banner->label }}</div>
                            @endif
                            @if($banner->title)
                            <h2 class="sm-slide-title" style="{{ $banner->text_color ? 'color:'.$banner->text_color.';' : '' }}">{!! nl2br(e($banner->title)) !!}</h2>
                            @endif
                            @if($banner->description)
                            <p class="sm-slide-desc">{{ $banner->description }}</p>
                            @endif
                            @if($banner->price_text)
                            <div class="sm-slide-price">{{ $banner->price_text }}</div>
                            @endif
                            @if($banner->button_text)
                            <a href="{{ $banner->button_link ?: '#' }}" class="sm-btn sm-btn--dark">{{ $banner->button_text }}</a>
                            @endif
                        </div>
                        <div class="sm-slide-visual">
                            @if($banner->image)
                            <img src="{{ $banner->image }}" alt="{{ $banner->title }}">
                            @else
                            <div class="sm-slide-ph"><i class="bi bi-image"></i></div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>

        @if($banners->count() > 1)
        <button class="sm-slider-arrow left" id="smSlidePrev" aria-label="Trước"><i class="bi bi-chevron-left"></i></button>
        <button class="sm-slider-arrow right" id="smSlideNext" aria-label="Sau"><i class="bi bi-chevron-right"></i></button>
        <div class="sm-dots" id="smDots">
            @foreach($banners as $i => $banner)
            <button class="sm-dot {{ $i === 0 ? 'active' : '' }}" data-slide="{{ $i }}" aria-label="Slide {{ $i + 1 }}"></button>
            @endforeach
        </div>
        @endif
    </section>
    @endif

    {{-- ============================================================
         TRUST BAR
         ============================================================ --}}
    <div class="sm-wrap">
        <div class="sm-trust sm-stagger">
            <div class="sm-trust-item">
                <i class="bi bi-truck"></i>
                <div><b>Giao hàng miễn phí</b><span>Đơn hàng từ 500k</span></div>
            </div>
            <div class="sm-trust-item">
                <i class="bi bi-patch-check"></i>
                <div><b>Chính hãng 100%</b><span>Bảo hành toàn quốc</span></div>
            </div>
            <div class="sm-trust-item">
                <i class="bi bi-arrow-repeat"></i>
                <div><b>Đổi trả dễ dàng</b><span>Trong vòng 30 ngày</span></div>
            </div>
            <div class="sm-trust-item">
                <i class="bi bi-credit-card-2-front"></i>
                <div><b>Trả góp 0%</b><span>Thủ tục nhanh chóng</span></div>
            </div>
            <div class="sm-trust-item">
                <i class="bi bi-headset"></i>
                <div><b>Hỗ trợ 24/7</b><span>Hotline: 1900 1234</span></div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         HIGHLIGHT VIDEO (cuộn để phóng)
         ============================================================ --}}
    <section class="sm-section" id="sm-highlight">
        <div class="sm-wrap sm-center sm-reveal" style="margin-bottom:44px">
            <p class="sm-eyebrow">Điểm nhấn</p>
            <h2 class="sm-h2">Mỏng hơn. Mạnh hơn.<br>Sẵn sàng cho mọi khoảnh khắc</h2>
            <p class="sm-lead">Những thiết bị mới nhất tại ElectronicShop được tuyển chọn kỹ lưỡng: hiệu năng cao, thời lượng pin bền và thiết kế tinh giản đến từng chi tiết.</p>
        </div>

        <div class="sm-wrap">
            <div class="sm-videocard" id="smVideoCard">
                <video autoplay muted loop playsinline preload="none"
                       poster="{{ asset('images/promo-apple.png') }}">
                    <source src="https://images.samsung.com/vn/smartphones/galaxy-z-fold8/videos/galaxy-z-fold8-features-design-intro.webm?imbypass=true" type="video/webm">
                    <source src="https://images.samsung.com/vn/smartphones/galaxy-z-fold8/videos/galaxy-z-fold8-features-design-intro.mp4?imbypass=true" type="video/mp4">
                </video>
            </div>
        </div>
    </section>

    {{-- ============================================================
         SỰ KIỆN / KHUYẾN MÃI
         ============================================================ --}}
    @if(isset($events) && $events->isNotEmpty())
    <section class="sm-section sm-section--tight">
        <div class="sm-wrap">
            <div class="sm-section-head sm-reveal">
                <div>
                    <p class="sm-eyebrow">Ưu đãi</p>
                    <h2 class="sm-h3">Sự kiện đang diễn ra</h2>
                </div>
                <a href="{{ route('products.index') }}" class="sm-link">Xem tất cả <i class="bi bi-arrow-right"></i></a>
            </div>

            <div class="sm-events sm-stagger">
                @foreach($events as $event)
                <a href="{{ $event->button_link ?: '#' }}" class="sm-event"
                   style="background:{{ $event->bg_color ?: '#111111' }};color:{{ $event->text_color ?: '#fff' }}">
                    @if($event->image)
                    <img src="{{ $event->image }}" alt="{{ $event->title }}" loading="lazy">
                    @endif
                    <div class="sm-event-body">
                        @if($event->tag)
                        <div class="sm-event-tag">{{ $event->tag }}</div>
                        @endif
                        <div class="sm-event-title">{{ $event->title }}</div>
                        @if($event->offer_text)
                        <div class="sm-event-offer">{{ $event->offer_text }}</div>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ============================================================
         STICKY SHOWCASE — 3 lý do chọn ElectronicShop
         ============================================================ --}}
    @php
        $showcaseItems = collect($newProducts ?? [])->take(3);
    @endphp
    @if($showcaseItems->count() >= 1)
    <section class="sm-section sm-section--surface sm-showcase">
        <div class="sm-wrap sm-center sm-reveal" style="margin-bottom:56px">
            <p class="sm-eyebrow">Trải nghiệm</p>
            <h2 class="sm-h2">Chuẩn khung hình.<br>Xem gì cũng đỉnh</h2>
        </div>

        <div class="sm-wrap sm-wrap--narrow">
            <div class="sm-showcase-grid">
                <div class="sm-showcase-visual" id="smShowcaseVisual">
                    @foreach($showcaseItems as $k => $sp)
                        @if($sp->first_image)
                        <img src="{{ $sp->first_image }}" alt="{{ $sp->name }}" data-visual="{{ $k }}" class="{{ $k === 0 ? 'on' : '' }}" loading="lazy">
                        @else
                        <img src="{{ asset('images/promo-samsung.png') }}" alt="{{ $sp->name }}" data-visual="{{ $k }}" class="{{ $k === 0 ? 'on' : '' }}" loading="lazy">
                        @endif
                    @endforeach
                </div>

                <div class="sm-showcase-steps">
                    @foreach($showcaseItems as $k => $sp)
                    <div class="sm-step {{ $k === 0 ? 'on' : '' }}" data-step="{{ $k }}">
                        <div class="sm-step-num">0{{ $k + 1 }} — {{ $sp->category->name ? mb_strtoupper($sp->category->name, 'UTF-8') : 'ĐIỂM NỔI BẬT' }}</div>
                        <h3>{{ $sp->name }}</h3>
                        <p>{{ $sp->description ? \Illuminate\Support\Str::limit(strip_tags($sp->description), 160) : 'Sản phẩm chính hãng, bảo hành toàn quốc, sẵn hàng giao ngay.' }}</p>
                        <div>
                            <a href="{{ route('products.show', $sp->slug) }}" class="sm-btn sm-btn--ghost sm-btn--sm">Xem chi tiết</a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ============================================================
         SPEC / SỐ LIỆU (nền đen, đếm số)
         ============================================================ --}}
    <section class="sm-section sm-section--dark">
        <div class="sm-wrap sm-center sm-reveal" style="margin-bottom:48px">
            <p class="sm-eyebrow">ElectronicShop</p>
            <h2 class="sm-h2">Bí quyết để bạn mua sắm an tâm</h2>
        </div>
        <div class="sm-wrap">
            <div class="sm-specs sm-stagger" id="smSpecs">
                <div class="sm-spec">
                    <div class="sm-spec-num" data-count="120" data-suffix="+">0</div>
                    <div class="sm-spec-label">Cửa hàng &amp; điểm bảo hành trên toàn quốc</div>
                </div>
                <div class="sm-spec">
                    <div class="sm-spec-num" data-count="98" data-suffix="%">0</div>
                    <div class="sm-spec-label">Khách hàng đánh giá hài lòng sau khi mua</div>
                </div>
                <div class="sm-spec">
                    <div class="sm-spec-num" data-count="2" data-suffix="h">0</div>
                    <div class="sm-spec-label">Giao hàng siêu tốc nội thành</div>
                </div>
                <div class="sm-spec">
                    <div class="sm-spec-num" data-count="24" data-suffix="/7">0</div>
                    <div class="sm-spec-label">Tư vấn kỹ thuật &amp; hậu mãi</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
         THƯƠNG HIỆU
         ============================================================ --}}
    @if(isset($brands) && $brands->isNotEmpty())
    <section class="sm-section sm-section--tight">
        <div class="sm-wrap">
            <div class="sm-section-head sm-reveal">
                <div>
                    <p class="sm-eyebrow">Thương hiệu</p>
                    <h2 class="sm-h3">Chọn theo hãng bạn yêu thích</h2>
                </div>
                <a href="{{ route('products.index') }}" class="sm-link">Xem tất cả <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
        <div class="sm-brands-rail sm-stagger" id="smBrandsRail">
            @foreach($brands as $brand)
            <a href="{{ route('products.index', ['brand' => $brand->id]) }}" class="sm-brand">
                <div class="sm-brand-logo">
                    @if($brand->logo)
                    <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}" loading="lazy">
                    @else
                    <div class="sm-brand-initials">{{ strtoupper(mb_substr($brand->name, 0, 2)) }}</div>
                    @endif
                </div>
                <div class="sm-brand-name">{{ $brand->name }}</div>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ============================================================
         GỢI Ý DÀNH CHO BẠN
         ============================================================ --}}
    @auth
    @if(!empty($suggestedProducts) && $suggestedProducts->isNotEmpty())
    <section class="sm-section sm-section--tight sm-section--surface">
        <div class="sm-wrap">
            <div class="sm-section-head sm-reveal">
                <div>
                    <p class="sm-eyebrow">Cá nhân hoá</p>
                    <h2 class="sm-h3">Gợi ý dành riêng cho bạn</h2>
                </div>
            </div>
            <div class="sm-products sm-stagger">
                @foreach($suggestedProducts as $product)
                <a href="{{ route('products.show', ['slug' => $product->slug, 'from' => 'suggestion', 'via' => 'homepage']) }}" class="sm-product">
                    <div class="sm-product-media">
                        @auth
                        <button type="button" class="sm-wish sm-wish-ajax {{ auth()->user()->wishlists->contains('product_id', $product->id) ? 'active' : '' }}"
                            data-product-id="{{ $product->id }}"
                            data-url="{{ route('wishlist.toggle', $product->id) }}"
                            aria-label="Yêu thích">
                            <i class="bi {{ auth()->user()->wishlists->contains('product_id', $product->id) ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                        </button>
                        @else
                        <a href="{{ route('login') }}" class="sm-wish" aria-label="Yêu thích" onclick="event.stopPropagation()">
                            <i class="bi bi-heart"></i>
                        </a>
                        @endauth
                        @if($product->first_image)
                        <img src="{{ $product->first_image }}" alt="{{ $product->name }}" loading="lazy">
                        @else
                        <div class="sm-product-ph"><i class="bi bi-image"></i></div>
                        @endif
                    </div>
                    <div class="sm-product-brand">{{ $product->brand->name ?? '' }}</div>
                    <div class="sm-product-name">{{ $product->name }}</div>
                    <div class="sm-product-price">{{ number_format($product->sale_price) }}đ</div>
                    <div class="sm-product-buy">
                        <span class="sm-btn sm-btn--dark sm-btn--sm">Xem chi tiết</span>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif
    @endauth

    {{-- ============================================================
         SẢN PHẨM MỚI NHẤT
         ============================================================ --}}
    <section class="sm-section sm-section--tight">
        <div class="sm-wrap">
            <div class="sm-section-head sm-reveal">
                <div>
                    <p class="sm-eyebrow">Mới ra mắt</p>
                    <h2 class="sm-h3">Sản phẩm mới nhất</h2>
                </div>
                <a href="{{ route('products.index') }}" class="sm-link">Xem tất cả <i class="bi bi-arrow-right"></i></a>
            </div>

            <div class="sm-products sm-stagger">
                @forelse($newProducts as $product)
                <a href="{{ route('products.show', $product->slug) }}" class="sm-product">
                    <div class="sm-product-media">
                        @auth
                        <button type="button" class="sm-wish sm-wish-ajax {{ auth()->user()->wishlists->contains('product_id', $product->id) ? 'active' : '' }}"
                            data-product-id="{{ $product->id }}"
                            data-url="{{ route('wishlist.toggle', $product->id) }}"
                            aria-label="Yêu thích">
                            <i class="bi {{ auth()->user()->wishlists->contains('product_id', $product->id) ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                        </button>
                        @else
                        <a href="{{ route('login') }}" class="sm-wish" aria-label="Yêu thích" onclick="event.stopPropagation()">
                            <i class="bi bi-heart"></i>
                        </a>
                        @endauth
                        @if($product->first_image)
                        <img src="{{ $product->first_image }}" alt="{{ $product->name }}" loading="lazy">
                        @else
                        <div class="sm-product-ph"><i class="bi bi-image"></i></div>
                        @endif
                    </div>
                    <div class="sm-product-brand">{{ $product->brand->name ?? '' }}</div>
                    <div class="sm-product-name">{{ $product->name }}</div>
                    <div class="sm-product-price">{{ number_format($product->price) }}đ</div>
                    <div class="sm-stars">
                        ★★★★★ <span>({{ $product->reviews_count ?? 0 }})</span>
                    </div>
                    <div class="sm-product-buy">
                        <span class="sm-btn sm-btn--dark sm-btn--sm">Xem chi tiết</span>
                    </div>
                </a>
                @empty
                <div class="sm-empty">Chưa có sản phẩm nào.</div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ============================================================
         PROMO CARDS
         ============================================================ --}}
    <section class="sm-section sm-section--tight">
        <div class="sm-wrap">
            <div class="sm-promos sm-stagger">
                <div class="sm-promo">
                    <div class="sm-promo-tag">Thế giới Apple</div>
                    <h3>Giảm đến 4 triệu</h3>
                    <div class="sm-promo-sub">Ưu đãi có hạn cho iPhone, iPad và Mac.</div>
                    <a href="{{ route('products.index') }}" class="sm-btn sm-btn--dark sm-btn--sm">Săn ngay</a>
                    <img src="{{ asset('images/promo-apple.png') }}" alt="Ưu đãi thế giới Apple" loading="lazy">
                </div>
                <div class="sm-promo sm-promo--dark">
                    <div class="sm-promo-tag">Samsung Store</div>
                    <h3>Thu cũ đổi mới</h3>
                    <div class="sm-promo-sub" style="color:rgba(255,255,255,.68)">Trợ giá thêm đến 3 triệu khi lên đời.</div>
                    <a href="{{ route('products.index') }}" class="sm-btn sm-btn--primary sm-btn--sm">Xem thêm</a>
                    <img src="{{ asset('images/promo-samsung.png') }}" alt="Thu cũ đổi mới Samsung" loading="lazy">
                </div>
                <div class="sm-promo">
                    <div class="sm-promo-tag">Bảo hành chính hãng</div>
                    <h3>An tâm 12 tháng</h3>
                    <div class="sm-promo-sub">Đổi mới trong 30 ngày nếu có lỗi nhà sản xuất.</div>
                    <a href="#sm-faq" class="sm-btn sm-btn--dark sm-btn--sm">Xem chi tiết</a>
                    <img src="{{ asset('images/promo-baohanh.png') }}" alt="Bảo hành chính hãng" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
         TIN TỨC
         ============================================================ --}}
    @if($latestNews->isNotEmpty())
    <section class="sm-section sm-section--tight sm-section--surface">
        <div class="sm-wrap">
            <div class="sm-section-head sm-reveal">
                <div>
                    <p class="sm-eyebrow">Newsroom</p>
                    <h2 class="sm-h3">Tin tức công nghệ</h2>
                </div>
                <a href="{{ route('news.index') }}" class="sm-link">Xem tin mới nhất <i class="bi bi-arrow-right"></i></a>
            </div>

            <div class="sm-news sm-stagger">
                @foreach($latestNews as $news)
                <a href="{{ route('news.show', $news->slug) }}" class="sm-news-card">
                    <div class="sm-news-media">
                        @if(!empty($news->thumbnail))
                        <img src="{{ asset('storage/' . $news->thumbnail) }}" alt="{{ $news->title }}" loading="lazy">
                        @else
                        <div class="sm-news-ph"><i class="bi bi-image"></i></div>
                        @endif
                    </div>
                    <div class="sm-news-title">{{ $news->title }}</div>
                    <div class="sm-news-excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($news->content), 110) }}</div>
                    <div class="sm-news-meta">
                        @if($news->published_at){{ $news->published_at->diffForHumans() }}@endif
                        · {{ number_format($news->views ?? 0) }} lượt xem
                        @if($news->category) · {{ $news->category->name }} @endif
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ============================================================
         FAQ ACCORDION
         ============================================================ --}}
    <section class="sm-section sm-section--tight" id="sm-faq">
        <div class="sm-wrap sm-center sm-reveal" style="margin-bottom:40px">
            <p class="sm-eyebrow">Hỗ trợ</p>
            <h2 class="sm-h3">Câu hỏi thường gặp</h2>
        </div>
        <div class="sm-wrap">
            <div class="sm-faq sm-reveal" id="smFaq">
                <div class="sm-faq-item open">
                    <button type="button" class="sm-faq-q">
                        Sản phẩm tại ElectronicShop có phải hàng chính hãng?
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <div class="sm-faq-a">
                        <p>Toàn bộ sản phẩm đều là hàng chính hãng, nguyên seal, có đầy đủ hoá đơn VAT và được bảo hành theo chính sách của nhà sản xuất tại hệ thống trung tâm bảo hành uỷ quyền trên toàn quốc.</p>
                    </div>
                </div>
                <div class="sm-faq-item">
                    <button type="button" class="sm-faq-q">
                        Tôi có thể trả góp 0% không?
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <div class="sm-faq-a">
                        <p>Có. Bạn có thể trả góp 0% qua thẻ tín dụng của các ngân hàng liên kết, kỳ hạn 3 – 12 tháng. Thủ tục xét duyệt nhanh, chỉ cần thẻ và giấy tờ tuỳ thân.</p>
                    </div>
                </div>
                <div class="sm-faq-item">
                    <button type="button" class="sm-faq-q">
                        Chính sách đổi trả như thế nào?
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <div class="sm-faq-a">
                        <p>Bạn được đổi mới trong 30 ngày đầu nếu sản phẩm có lỗi từ nhà sản xuất, và hoàn tiền trong 7 ngày nếu sản phẩm chưa qua sử dụng, còn nguyên hộp và phụ kiện.</p>
                    </div>
                </div>
                <div class="sm-faq-item">
                    <button type="button" class="sm-faq-q">
                        Thời gian giao hàng mất bao lâu?
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <div class="sm-faq-a">
                        <p>Nội thành Đà Nẵng, Hà Nội và TP.HCM giao trong 2 giờ. Các tỉnh thành khác nhận hàng sau 1 – 3 ngày làm việc. Miễn phí vận chuyển cho đơn từ 500.000đ.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
         CTA BAND
         ============================================================ --}}
    <section class="sm-section sm-section--dark sm-cta">
        <div class="sm-wrap sm-reveal">
            <p class="sm-eyebrow">Bắt đầu ngay</p>
            <h2 class="sm-h2">Sẵn sàng nâng cấp thiết bị của bạn?</h2>
            <p class="sm-lead" style="margin-bottom:30px">Đặt hàng online, nhận tư vấn 1:1 từ chuyên viên và trải nghiệm sản phẩm tại hơn 120 cửa hàng ElectronicShop.</p>
            <div>
                <a href="{{ route('products.index') }}" class="sm-btn sm-btn--primary">Mua sắm ngay</a>
                <a href="{{ route('contact.index') }}" class="sm-btn sm-btn--light">Liên hệ tư vấn</a>
            </div>
        </div>
    </section>

</div>
@endsection

@push('scripts')
<script>
/* ============================================================
   HOMEPAGE INTERACTIONS — SAMSUNG STYLE
   ============================================================ */
(function () {
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ----------------------------------------------------------
       1. SCROLL REVEAL (fade-up + stagger)
    ---------------------------------------------------------- */
    const revealTargets = document.querySelectorAll('.sm-reveal, .sm-stagger');
    if (reduce) {
        revealTargets.forEach(el => el.classList.add('in'));
    } else {
        const io = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('in');
                    io.unobserve(e.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -60px 0px' });
        revealTargets.forEach(el => io.observe(el));
    }

    /* ----------------------------------------------------------
       2. HERO VIDEO CONTROLS
    ---------------------------------------------------------- */
    const heroVideo = document.getElementById('smHeroVideo');
    const playBtn   = document.getElementById('smHeroPlay');
    const muteBtn   = document.getElementById('smHeroMute');

    if (heroVideo && playBtn && muteBtn) {
        playBtn.addEventListener('click', function () {
            if (heroVideo.paused) {
                heroVideo.play();
                playBtn.innerHTML = '<i class="bi bi-pause-fill"></i>';
            } else {
                heroVideo.pause();
                playBtn.innerHTML = '<i class="bi bi-play-fill"></i>';
            }
        });
        muteBtn.addEventListener('click', function () {
            heroVideo.muted = !heroVideo.muted;
            muteBtn.innerHTML = heroVideo.muted
                ? '<i class="bi bi-volume-mute-fill"></i>'
                : '<i class="bi bi-volume-up-fill"></i>';
        });
        // Nếu trình duyệt chặn autoplay, đổi icon cho đúng trạng thái
        heroVideo.addEventListener('pause', () => { playBtn.innerHTML = '<i class="bi bi-play-fill"></i>'; });
        heroVideo.addEventListener('play',  () => { playBtn.innerHTML = '<i class="bi bi-pause-fill"></i>'; });
    }

    const cue = document.getElementById('smScrollCue');
    if (cue) {
        cue.addEventListener('click', function () {
            const target = document.getElementById('smSlider') || document.getElementById('sm-highlight');
            target?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    }

    /* Tiết kiệm pin: tạm dừng video khi ra khỏi khung nhìn */
    if ('IntersectionObserver' in window) {
        const vio = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                const v = e.target;
                if (e.isIntersecting) { v.play().catch(() => {}); }
                else { v.pause(); }
            });
        }, { threshold: 0.2 });
        document.querySelectorAll('video[loop]').forEach(v => vio.observe(v));
    }

    /* ----------------------------------------------------------
       3. BANNER SLIDER (fade + autoplay + swipe)
    ---------------------------------------------------------- */
    (function slider() {
        const root   = document.getElementById('smSlider');
        if (!root) return;
        const slides = root.querySelectorAll('.sm-slide');
        const dots   = root.querySelectorAll('.sm-dot');
        if (slides.length <= 1) return;

        let index = 0, timer = null;

        function go(i) {
            index = (i + slides.length) % slides.length;
            slides.forEach((s, k) => s.classList.toggle('active', k === index));
            dots.forEach((d, k) => d.classList.toggle('active', k === index));
        }
        function next() { go(index + 1); }
        function prev() { go(index - 1); }
        function auto() { clearInterval(timer); if (!reduce) timer = setInterval(next, 6000); }

        document.getElementById('smSlideNext')?.addEventListener('click', () => { next(); auto(); });
        document.getElementById('smSlidePrev')?.addEventListener('click', () => { prev(); auto(); });
        dots.forEach((d, k) => d.addEventListener('click', () => { go(k); auto(); }));

        root.addEventListener('mouseenter', () => clearInterval(timer));
        root.addEventListener('mouseleave', auto);

        /* Swipe trên mobile */
        let x0 = null;
        root.addEventListener('touchstart', e => { x0 = e.touches[0].clientX; }, { passive: true });
        root.addEventListener('touchend', e => {
            if (x0 === null) return;
            const dx = e.changedTouches[0].clientX - x0;
            if (Math.abs(dx) > 45) { dx < 0 ? next() : prev(); auto(); }
            x0 = null;
        });

        /* Điều hướng bằng bàn phím */
        root.addEventListener('keydown', e => {
            if (e.key === 'ArrowRight') { next(); auto(); }
            if (e.key === 'ArrowLeft')  { prev(); auto(); }
        });
        root.tabIndex = 0;

        auto();
    })();

    /* ----------------------------------------------------------
       4. VIDEO CARD — phóng to theo tiến trình cuộn
    ---------------------------------------------------------- */
    (function scrollScale() {
        const card = document.getElementById('smVideoCard');
        if (!card || reduce) return;

        let raf = null;
        function update() {
            const r  = card.getBoundingClientRect();
            const vh = window.innerHeight;
            // progress 0 -> 1 khi card đi từ dưới màn hình lên giữa màn hình
            let p = 1 - (r.top - vh * 0.15) / (vh * 0.85);
            p = Math.max(0, Math.min(1, p));
            card.style.setProperty('--sc', (0.9 + p * 0.1).toFixed(4));
            raf = null;
        }
        window.addEventListener('scroll', () => { if (!raf) raf = requestAnimationFrame(update); }, { passive: true });
        window.addEventListener('resize', update);
        update();
    })();

    /* ----------------------------------------------------------
       5. STICKY SHOWCASE — đổi ảnh theo bước đang đọc
    ---------------------------------------------------------- */
    (function showcase() {
        const steps   = document.querySelectorAll('.sm-step');
        const visuals = document.querySelectorAll('#smShowcaseVisual img');
        if (!steps.length || !visuals.length) return;

        const io = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (!e.isIntersecting) return;
                const i = e.target.dataset.step;
                steps.forEach(s => s.classList.toggle('on', s === e.target));
                visuals.forEach(v => v.classList.toggle('on', v.dataset.visual === i));
            });
        }, { threshold: 0.5, rootMargin: '-20% 0px -20% 0px' });

        steps.forEach(s => io.observe(s));
    })();

    /* ----------------------------------------------------------
       6. COUNT-UP SỐ LIỆU
    ---------------------------------------------------------- */
    (function countUp() {
        const nums = document.querySelectorAll('.sm-spec-num[data-count]');
        if (!nums.length) return;

        function run(el) {
            const target = parseFloat(el.dataset.count);
            const suffix = el.dataset.suffix || '';
            if (reduce) { el.textContent = target + suffix; return; }

            const dur = 1400;
            const t0  = performance.now();
            function tick(now) {
                const p = Math.min(1, (now - t0) / dur);
                const eased = 1 - Math.pow(1 - p, 3);
                el.textContent = Math.round(target * eased) + suffix;
                if (p < 1) requestAnimationFrame(tick);
            }
            requestAnimationFrame(tick);
        }

        const io = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) { run(e.target); io.unobserve(e.target); }
            });
        }, { threshold: 0.6 });
        nums.forEach(n => io.observe(n));
    })();

    /* ----------------------------------------------------------
       7. BRAND RAIL — kéo chuột để cuộn ngang
    ---------------------------------------------------------- */
    (function dragRail() {
        const rail = document.getElementById('smBrandsRail');
        if (!rail) return;
        let down = false, startX = 0, startScroll = 0;

        rail.addEventListener('mousedown', e => {
            down = true; startX = e.pageX; startScroll = rail.scrollLeft;
            rail.classList.add('dragging');
        });
        window.addEventListener('mouseup', () => { down = false; rail.classList.remove('dragging'); });
        rail.addEventListener('mousemove', e => {
            if (!down) return;
            e.preventDefault();
            rail.scrollLeft = startScroll - (e.pageX - startX) * 1.15;
        });
    })();

    /* ----------------------------------------------------------
       8. FAQ ACCORDION
    ---------------------------------------------------------- */
    (function faq() {
        const items = document.querySelectorAll('#smFaq .sm-faq-item');
        if (!items.length) return;

        function setHeight(item) {
            const a = item.querySelector('.sm-faq-a');
            a.style.maxHeight = item.classList.contains('open') ? (a.scrollHeight + 40) + 'px' : '0px';
        }

        items.forEach(item => {
            item.querySelector('.sm-faq-q').addEventListener('click', () => {
                const willOpen = !item.classList.contains('open');
                items.forEach(i => { i.classList.remove('open'); setHeight(i); });
                if (willOpen) { item.classList.add('open'); setHeight(item); }
            });
            setHeight(item);
        });
        window.addEventListener('resize', () => items.forEach(setHeight));
    })();

    /* ----------------------------------------------------------
       9. WISHLIST (toggle AJAX)
    ---------------------------------------------------------- */
    let _hmToastTimer;
    function showHomeWishToast(msg, isErr) {
        let t = document.getElementById('_homeWlToast');
        if (!t) {
            t = document.createElement('div');
            t.id = '_homeWlToast';
            t.style.cssText = 'position:fixed;bottom:24px;right:24px;background:rgba(15,23,42,.92);color:#fff;padding:12px 20px;border-radius:12px;font-size:14px;font-weight:500;display:flex;align-items:center;gap:10px;z-index:9999;opacity:0;transform:translateY(10px);transition:opacity .3s,transform .3s;pointer-events:none;';
            t.innerHTML = '<i style="font-size:16px"></i><span></span>';
            document.body.appendChild(t);
        }
        const icon = t.querySelector('i');
        icon.style.color = isErr ? '#f87171' : '#34d399';
        icon.className = isErr ? 'fas fa-times-circle' : 'fas fa-check-circle';
        t.querySelector('span').textContent = msg;
        t.style.opacity = '1'; t.style.transform = 'translateY(0)';
        clearTimeout(_hmToastTimer);
        _hmToastTimer = setTimeout(() => { t.style.opacity='0'; t.style.transform='translateY(10px)'; }, 2800);
    }

    // AJAX-enabled buttons
    document.querySelectorAll('.sm-wish-ajax').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault(); e.stopPropagation();
            const icon = this.querySelector('i');
            const wasActive = this.classList.contains('active');

            this.classList.toggle('active', !wasActive);
            icon.className = wasActive ? 'bi bi-heart' : 'bi bi-heart-fill';

            fetch(this.dataset.url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            })
            .then(r => r.json())
            .then(data => {
                this.classList.toggle('active', data.wishlisted);
                icon.className = data.wishlisted ? 'bi bi-heart-fill' : 'bi bi-heart';
                showHomeWishToast(data.wishlisted ? '♥ Đã thêm vào yêu thích' : 'Nhấp khỏi yêu thích');
            })
            .catch(() => {
                this.classList.toggle('active', wasActive);
                icon.className = wasActive ? 'bi bi-heart-fill' : 'bi bi-heart';
                showHomeWishToast('Có lỗi, vui lòng thử lại', true);
            });
        });
    });

    // Legacy static buttons (fallback, gữ lại cho tương thích)
    document.querySelectorAll('.sm-wish:not(.sm-wish-ajax)').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault(); e.stopPropagation();
            const on = btn.classList.toggle('on');
            btn.innerHTML = on ? '<i class="bi bi-heart-fill"></i>' : '<i class="bi bi-heart"></i>';
        });
    });

    /* ----------------------------------------------------------
       10. 3D TILT — nghiêng theo con trỏ chuột (sản phẩm/tin tức/event/promo)
    ---------------------------------------------------------- */
    if (!reduce) {
        document.querySelectorAll('.sm-product, .sm-news-card, .sm-event, .sm-promo').forEach(card => {
            card.addEventListener('mousemove', function (e) {
                const rect = card.getBoundingClientRect();
                const cx = rect.left + rect.width / 2;
                const cy = rect.top + rect.height / 2;
                const dx = (e.clientX - cx) / (rect.width / 2);
                const dy = (e.clientY - cy) / (rect.height / 2);
                const rotX = -dy * 7;
                const rotY = dx * 7;
                card.style.transition = 'none';
                card.style.transform = `perspective(800px) rotateX(${rotX}deg) rotateY(${rotY}deg) translateY(-6px) scale(1.015)`;
            });
            card.addEventListener('mouseleave', function () {
                card.style.transition = 'transform .5s cubic-bezier(.16,1,.3,1)';
                card.style.transform = '';
            });
        });
    }

    /* ----------------------------------------------------------
       11. RIPPLE — gợn sóng khi click vào thẻ sản phẩm/tin tức/event/brand
    ---------------------------------------------------------- */
    document.querySelectorAll('.sm-product, .sm-news-card, .sm-event, .sm-brand, .sm-promo').forEach(card => {
        card.style.overflow = card.style.overflow || 'hidden';
        card.addEventListener('click', function (e) {
            const rect = card.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height) * 1.6;
            const ripple = document.createElement('span');
            ripple.className = 'ripple-wave';
            ripple.style.cssText = [
                `width:${size}px`, `height:${size}px`,
                `left:${e.clientX - rect.left - size / 2}px`,
                `top:${e.clientY - rect.top - size / 2}px`,
            ].join(';');
            card.appendChild(ripple);
            ripple.addEventListener('animationend', () => ripple.remove());
        });
    });
})();
</script>
@endpush