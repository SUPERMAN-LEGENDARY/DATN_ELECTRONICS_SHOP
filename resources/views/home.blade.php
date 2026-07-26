@extends('layouts.app')
@section('title', 'ElectronicShop - Trang chủ')
@php $showSearch = true; @endphp

@push('styles')
<style>
    /* ===== LAYOUT WRAPPER ===== */
    .page-body {
        background: linear-gradient(180deg,
            #bae6fd 0%,
            #e0f2fe 18%,
            #f0f9ff 38%,
            #e0f2fe 62%,
            #bae6fd 100%);
        min-height: 100vh;
        padding-bottom: 40px;
        position: relative;
    }

    /* Floating cloud texture */
    .page-body::before {
        content: '';
        position: fixed;
        inset: 0;
        pointer-events: none;
        background:
            radial-gradient(ellipse 600px 300px at 10% 15%, rgba(125,211,252,.35) 0%, transparent 70%),
            radial-gradient(ellipse 500px 260px at 85% 30%, rgba(186,230,253,.4) 0%, transparent 70%),
            radial-gradient(ellipse 700px 350px at 50% 75%, rgba(224,242,254,.5) 0%, transparent 70%);
        z-index: 0;
        animation: cloudDrift 20s ease-in-out infinite alternate;
    }

    @keyframes cloudDrift {
        0%   { background-position: 0% 0%; opacity: .8; }
        33%  { background-position: 5% 8%; opacity: 1; }
        66%  { background-position: -4% 4%; opacity: .85; }
        100% { background-position: 3% -5%; opacity: 1; }
    }

    .page-body > * {
        position: relative;
        z-index: 1;
    }

    .page-body .container {
        padding-top: 16px;
    }

    /* ===== CANVAS CLOUDS ===== */
    #sky-canvas {
        position: fixed;
        inset: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 0;
        opacity: .55;
    }

    /* ===== BUBBLES ===== */
    .bubble {
        position: fixed;
        border-radius: 50%;
        background: radial-gradient(circle at 35% 35%, rgba(255,255,255,.8), rgba(186,230,253,.3));
        border: 1px solid rgba(125,211,252,.4);
        pointer-events: none;
        z-index: 0;
        animation: bubbleRise linear infinite;
    }

    @keyframes bubbleRise {
        0%   { transform: translateY(0) scale(1);   opacity: .7; }
        80%  { opacity: .5; }
        100% { transform: translateY(-110vh) scale(1.1); opacity: 0; }
    }

    /* ===== SCROLL REVEAL ===== */
    .reveal {
        opacity: 0;
        transform: translateY(36px);
        transition: opacity .6s cubic-bezier(.16,1,.3,1), transform .6s cubic-bezier(.16,1,.3,1);
    }

    .reveal.revealed {
        opacity: 1;
        transform: translateY(0);
    }

    .reveal-left {
        opacity: 0;
        transform: translateX(-40px);
        transition: opacity .6s cubic-bezier(.16,1,.3,1), transform .6s cubic-bezier(.16,1,.3,1);
    }

    .reveal-left.revealed {
        opacity: 1;
        transform: translateX(0);
    }

    /* stagger children */
    .stagger-children > * {
        opacity: 0;
        transform: translateY(24px);
        transition: opacity .5s cubic-bezier(.16,1,.3,1), transform .5s cubic-bezier(.16,1,.3,1);
    }

    .stagger-children.revealed > *:nth-child(1) { opacity:1; transform:translateY(0); transition-delay: .05s; }
    .stagger-children.revealed > *:nth-child(2) { opacity:1; transform:translateY(0); transition-delay: .12s; }
    .stagger-children.revealed > *:nth-child(3) { opacity:1; transform:translateY(0); transition-delay: .19s; }
    .stagger-children.revealed > *:nth-child(4) { opacity:1; transform:translateY(0); transition-delay: .26s; }
    .stagger-children.revealed > *:nth-child(5) { opacity:1; transform:translateY(0); transition-delay: .33s; }
    .stagger-children.revealed > *:nth-child(6) { opacity:1; transform:translateY(0); transition-delay: .40s; }
    .stagger-children.revealed > *:nth-child(n+7) { opacity:1; transform:translateY(0); transition-delay: .46s; }

    /* ===== CARD SHINE ===== */
    .product-card,
    .news-card {
        overflow: hidden;
    }

    .product-card::after,
    .news-card::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(105deg,
            transparent 40%,
            rgba(255,255,255,.45) 50%,
            transparent 60%);
        transform: translateX(-120%);
        transition: transform .55s ease;
        pointer-events: none;
        z-index: 3;
    }

    .product-card:hover::after,
    .news-card:hover::after {
        transform: translateX(120%);
    }

    /* ===== RIPPLE ===== */
    .ripple-wave {
        position: absolute;
        border-radius: 50%;
        background: rgba(125,211,252,.35);
        transform: scale(0);
        animation: rippleOut .6s linear;
        pointer-events: none;
        z-index: 10;
    }

    @keyframes rippleOut {
        to { transform: scale(4); opacity: 0; }
    }

    /* ===== HERO ENTRANCE ===== */
    .hero-content > * {
        opacity: 0;
        animation: heroIn .7s cubic-bezier(.16,1,.3,1) forwards;
    }

    .hero-content > *:nth-child(1) { animation-delay: .1s; }
    .hero-content > *:nth-child(2) { animation-delay: .22s; }
    .hero-content > *:nth-child(3) { animation-delay: .34s; }
    .hero-content > *:nth-child(4) { animation-delay: .44s; }
    .hero-content > *:nth-child(5) { animation-delay: .54s; }

    @keyframes heroIn {
        from { opacity: 0; transform: translateY(18px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ===== BRAND CHIP FLOAT ===== */
    .brand-chip:hover .brand-chip-logo {
        animation: chipBounce .4s cubic-bezier(.34,1.56,.64,1);
    }

    @keyframes chipBounce {
        0%   { transform: translateY(0)     scale(1); }
        40%  { transform: translateY(-8px)  scale(1.08); }
        70%  { transform: translateY(-3px)  scale(1.04); }
        100% { transform: translateY(-4px)  scale(1.05); }
    }

    /* ===== TRUST BAR ICON PULSE ===== */
    .trust-bar-item .tbi-icon {
        animation: iconPulse 3s ease-in-out infinite;
    }

    .trust-bar-item:nth-child(2) .tbi-icon { animation-delay: .4s; }
    .trust-bar-item:nth-child(3) .tbi-icon { animation-delay: .8s; }
    .trust-bar-item:nth-child(4) .tbi-icon { animation-delay: 1.2s; }
    .trust-bar-item:nth-child(5) .tbi-icon { animation-delay: 1.6s; }

    @keyframes iconPulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(14,165,233,.0); transform: scale(1); }
        50%       { box-shadow: 0 0 0 6px rgba(14,165,233,.15); transform: scale(1.08); }
    }

    /* ===== SECTION HEADER UNDERLINE GROW ===== */
    .section-title {
        position: relative;
    }

    .section-title::after {
        content: '';
        position: absolute;
        left: 10px;
        bottom: -13px;
        height: 2px;
        width: 0;
        background: linear-gradient(90deg, #38bdf8, #7dd3fc);
        transition: width .5s cubic-bezier(.16,1,.3,1);
        border-radius: 2px;
    }

    .section.revealed .section-title::after {
        width: calc(100% - 10px);
    }

    /* ===== EVENTS STRIP SLIDE ===== */
    .event-card {
        animation: none;
    }

    /* ===== HERO ===== */
    .hero {
        position: relative;
        border-radius: 0;
        overflow: hidden;
        margin: 0 0 8px;
        background: linear-gradient(135deg, #cfe8fb 0%, #bae6fd 100%);
    }

    .hero-inner {
        display: grid;
        grid-template-columns: 42% 58%;
        min-height: 340px;
    }

    .hero-content {
        padding: 36px 32px 36px 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 8px;
    }

    .hero-label {
        color: #2563eb;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .6px;
    }

    .hero-title {
        font-size: 28px;
        font-weight: 800;
        line-height: 1.25;
        color: #111827;
        margin: 0;
    }

    .hero-desc {
        color: #6b7280;
        font-size: 13px;
        line-height: 1.6;
        margin: 0;
        max-width: 280px;
    }

    .hero-price {
        color: #e53935;
        font-size: 20px;
        font-weight: 800;
        margin-top: 4px;
    }

    .hero-content .btn-primary {
        background: #2563eb;
        border: none;
        border-radius: 6px;
        font-weight: 700;
        font-size: 12px;
        padding: 9px 20px;
        color: #fff;
        cursor: pointer;
        width: fit-content;
        text-decoration: none;
        display: inline-block;
        margin-top: 4px;
    }

    .hero-content .btn-primary:hover {
        background: #1d4ed8;
    }

    .hero-img {
        height: 100%;
        overflow: hidden;
    }

    .hero-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .hero-img-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #7dd3fc;
        font-size: 40px;
        background: linear-gradient(135deg, #bae6fd, #e0f2fe);
    }

    /* Arrow buttons */
    .hero-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #fff;
        border: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #374151;
        cursor: pointer;
        z-index: 3;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .12);
        font-size: 11px;
    }

    .hero-arrow.left {
        left: 20px;
    }

    .hero-arrow.right {
        right: 20px;
    }

    .hero-dots {
        position: absolute;
        left: 40px;
        bottom: 18px;
        display: flex;
        gap: 5px;
        z-index: 3;
    }

    .hero-dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: #bfdbfe;
        cursor: pointer;
        transition: .2s;
    }

    .hero-dot.active {
        width: 16px;
        border-radius: 3px;
        background: #2563eb;
    }

    /* ===== EVENTS (dưới banner) ===== */
    .events-strip {
        max-width: 1200px;
        margin: 14px auto 0;
        padding: 0 15px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 14px;
    }

    .event-card {
        position: relative;
        display: flex;
        align-items: center;
        min-height: 140px;
        border-radius: 12px;
        overflow: hidden;
        text-decoration: none;
        background: #263238;
        color: #fff;
        padding: 22px 26px;
        transition: transform .2s, box-shadow .2s;
    }

    .event-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(0, 0, 0, .16);
    }

    .event-card-bg {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: .35;
    }

    .event-card-body {
        position: relative;
        z-index: 1;
        min-width: 0;
    }

    .event-card-tag {
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: .85;
        margin-bottom: 6px;
    }

    .event-card-title {
        font-size: 21px;
        font-weight: 800;
        line-height: 1.25;
        margin-bottom: 6px;
        white-space: normal;
    }

    .event-card-offer {
        font-size: 14.5px;
        font-weight: 700;
        color: #FFD54F;
    }

    /* ===== TRUST BAR ===== */
    .trust-bar {
        background: rgba(255,255,255,.75);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        margin: 14px auto 0;
        max-width: 1200px;
        border-radius: 14px;
        box-shadow: 0 4px 20px rgba(14,165,233,.12), 0 0 0 1px rgba(186,230,253,.6);
        border: 1px solid rgba(186,230,253,.7);
    }

    .trust-bar .inner {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        padding: 14px 16px;
        gap: 8px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .trust-bar-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 4px 6px;
    }

    .trust-bar-item .tbi-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }

    .trust-bar-item div.tbi-text {
        display: flex;
        flex-direction: column;
        gap: 1px;
    }

    .trust-bar-item b {
        font-size: 12px;
        color: #0c4a6e;
        font-weight: 700;
    }

    .trust-bar-item span {
        font-size: 10.5px;
        color: #0284c7;
        opacity: .75;
    }

    .trust-bar-item .tbi-icon {
        background: linear-gradient(135deg, #bae6fd, #7dd3fc);
        color: #0369a1;
    }

    /* ===== SECTION ===== */
    .section {
        background: rgba(255,255,255,.82);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border-radius: 14px;
        padding: 20px 22px;
        margin-bottom: 16px;
        box-shadow: 0 4px 20px rgba(14,165,233,.1), 0 0 0 1px rgba(186,230,253,.5);
        border: 1px solid rgba(186,230,253,.55);
    }

    /* ===== SẢN PHẨM MỚI NHẤT — nền giống danh mục thương hiệu ===== */
    .section--products {
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 35%, #7dd3fc 65%, #38bdf8 100%);
        border: none;
        box-shadow: 0 4px 20px rgba(14,165,233,.18);
        overflow: hidden;
        position: relative;
    }

    .section--products::before {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: rgba(255,255,255,.18);
        pointer-events: none;
    }

    .section--products::after {
        content: '';
        position: absolute;
        bottom: -40px;
        left: -40px;
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: rgba(255,255,255,.14);
        pointer-events: none;
    }

    .section--products .section-header {
        border-bottom: 1px solid rgba(12,74,110,.15);
        position: relative;
        z-index: 2;
    }

    .section--products .section-title {
        color: #0c4a6e;
        border-left-color: #0369a1;
    }

    .section--products .section-title::after {
        background: linear-gradient(90deg, #0369a1, #0284c7);
    }

    .section--products .section-link {
        color: #0c4a6e;
        background: rgba(255,255,255,.55);
        border-color: rgba(255,255,255,.8);
    }

    .section--products .section-link:hover {
        background: rgba(255,255,255,.8);
    }

    .section--products .products-grid {
        position: relative;
        z-index: 2;
    }

    .section--products .product-card {
        background: rgba(255,255,255,.85);
        border-color: rgba(255,255,255,.7);
        backdrop-filter: blur(6px);
    }

    .section--products .product-card:hover {
        background: rgba(255,255,255,.96);
        box-shadow: 0 10px 28px rgba(3,105,161,.22);
        border-color: rgba(255,255,255,.9);
    }

    /* ===== TIN TỨC CÔNG NGHỆ — nền giống danh mục thương hiệu ===== */
    .section--news {
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 35%, #7dd3fc 65%, #38bdf8 100%);
        border: none;
        box-shadow: 0 4px 20px rgba(14,165,233,.18);
        overflow: hidden;
        position: relative;
    }

    .section--news::before {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: rgba(255,255,255,.18);
        pointer-events: none;
    }

    .section--news::after {
        content: '';
        position: absolute;
        bottom: -40px;
        left: -40px;
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: rgba(255,255,255,.14);
        pointer-events: none;
    }

    .section--news .section-header {
        border-bottom: 1px solid rgba(12,74,110,.15);
        position: relative;
        z-index: 2;
    }

    .section--news .section-title {
        color: #0c4a6e;
        border-left-color: #0369a1;
    }

    .section--news .section-title::after {
        background: linear-gradient(90deg, #0369a1, #0284c7);
    }

    .section--news .section-link {
        color: #0c4a6e;
        background: rgba(255,255,255,.55);
        border-color: rgba(255,255,255,.8);
    }

    .section--news .section-link:hover {
        background: rgba(255,255,255,.8);
    }

    .section--news .news-grid {
        position: relative;
        z-index: 2;
    }

    .section--news .news-card {
        background: rgba(255,255,255,.85);
        border-color: rgba(255,255,255,.7);
        backdrop-filter: blur(6px);
    }

    .section--news .news-card:hover {
        background: rgba(255,255,255,.96);
        box-shadow: 0 10px 28px rgba(3,105,161,.22);
        border-color: rgba(255,255,255,.9);
    }

    .section--news .news-card-title {
        color: #0c4a6e;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid #bae6fd;
    }

    .section-title {
        font-size: 16px;
        font-weight: 700;
        color: #0c4a6e;
        text-transform: uppercase;
        letter-spacing: .3px;
        margin: 0;
        padding-left: 10px;
        border-left: 3px solid #0ea5e9;
        line-height: 1.4;
    }

    .section-link {
        color: #0284c7;
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
        background: rgba(186,230,253,.4);
        padding: 5px 12px;
        border-radius: 20px;
        border: 1px solid rgba(125,211,252,.5);
        transition: background .2s;
    }

    .section-link:hover {
        background: rgba(125,211,252,.5);
        text-decoration: none;
    }

    /* ===== BRANDS SECTION ===== */
    .brands-section {
        position: relative;
        margin: 20px auto 0;
        max-width: 1200px;
        padding: 0 15px;
        margin-bottom: 0;
    }

    .brands-bg {
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 35%, #7dd3fc 65%, #38bdf8 100%);
        border-radius: 16px;
        padding: 28px 28px 24px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(14, 165, 233, 0.18);
    }

    /* decorative clouds / circles */
    .brands-bg::before {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: rgba(255,255,255,.18);
        pointer-events: none;
    }

    .brands-bg::after {
        content: '';
        position: absolute;
        bottom: -40px;
        left: -40px;
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: rgba(255,255,255,.14);
        pointer-events: none;
    }

    .brands-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        position: relative;
        z-index: 1;
    }

    .brands-title-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .brands-title-icon {
        width: 36px;
        height: 36px;
        background: rgba(255,255,255,.7);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0284c7;
        font-size: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,.08);
    }

    .brands-title {
        font-size: 16px;
        font-weight: 800;
        color: #0c4a6e;
        text-transform: uppercase;
        letter-spacing: .4px;
        margin: 0;
    }

    .brands-subtitle {
        font-size: 11.5px;
        color: #0369a1;
        margin: 0;
        font-weight: 500;
    }

    .brands-link {
        font-size: 12.5px;
        font-weight: 700;
        color: #0c4a6e;
        text-decoration: none;
        background: rgba(255,255,255,.55);
        padding: 6px 14px;
        border-radius: 20px;
        border: 1px solid rgba(255,255,255,.8);
        backdrop-filter: blur(4px);
        transition: background .2s, transform .15s;
        white-space: nowrap;
        position: relative;
        z-index: 1;
    }

    .brands-link:hover {
        background: rgba(255,255,255,.8);
        transform: translateY(-1px);
    }

    .brands-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 12px;
        position: relative;
        z-index: 1;
    }

    .brand-chip {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        cursor: pointer;
        transition: transform .2s;
    }

    .brand-chip:hover {
        transform: translateY(-4px);
    }

    .brand-chip-logo {
        width: 100%;
        aspect-ratio: 1;
        background: rgba(255,255,255,.85);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 10px rgba(0,0,0,.08), 0 0 0 1px rgba(255,255,255,.6);
        overflow: hidden;
        padding: 10px;
        box-sizing: border-box;
        backdrop-filter: blur(4px);
        transition: box-shadow .2s, background .2s;
    }

    .brand-chip:hover .brand-chip-logo {
        box-shadow: 0 8px 24px rgba(0, 120, 200, .22), 0 0 0 2px rgba(255,255,255,.9);
        background: #fff;
    }

    .brand-chip-logo img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    /* fallback khi không có logo */
    .brand-chip-logo .brand-chip-initials {
        font-size: 13px;
        font-weight: 800;
        color: #0369a1;
        letter-spacing: -.5px;
        text-align: center;
        line-height: 1.1;
    }

    .brand-chip-name {
        font-size: 11px;
        font-weight: 700;
        color: #0c4a6e;
        text-align: center;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
    }

    /* ===== PRODUCT GRID ===== */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 16px;
    }

    .product-card {
        position: relative;
        background: rgba(255,255,255,.88);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(186,230,253,.6);
        border-radius: 12px;
        overflow: hidden;
        text-decoration: none;
        color: #000;
        transition: box-shadow .25s, transform .25s, border-color .25s;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 28px rgba(14,165,233,.18);
        border-color: #7dd3fc;
    }

    .product-card-img {
        height: 170px;
        background: linear-gradient(160deg, #f0f9ff 0%, #e0f2fe 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
    }

    .product-card-img img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 10px;
    }

    .product-card-body {
        padding: 12px 12px 14px;
        border-top: 1px solid rgba(186,230,253,.6);
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .product-card-name {
        font-size: 13px;
        color: #0f4c75;
        min-height: 36px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-card-price {
        color: #e53935;
        font-size: 14px;
        font-weight: 700;
        display: block;
        margin-top: 4px;
    }

    .stars {
        color: #f59e0b;
        font-size: 11px;
        margin-top: auto;
        padding-top: 4px;
    }

    .review-count {
        color: #9ca3af;
        margin-left: 2px;
        font-size: 11px;
    }

    .badge-tag {
        position: absolute;
        top: 8px;
        left: 8px;
        background: #e53935;
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        padding: 3px 7px;
        border-radius: 5px;
        text-transform: uppercase;
        letter-spacing: .3px;
        z-index: 2;
    }

    .wish {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 26px;
        height: 26px;
        background: rgba(255,255,255,.85);
        backdrop-filter: blur(6px);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #7dd3fc;
        font-size: 12px;
        box-shadow: 0 1px 6px rgba(14,165,233,.18);
        z-index: 2;
    }

    /* ===== IMAGE PLACEHOLDER ===== */
    .img-placeholder {
        background: linear-gradient(135deg, #e0f2fe, #bae6fd);
        background-image:
            linear-gradient(45deg, transparent calc(50% - 1px), rgba(125,211,252,.4) calc(50% - 1px), rgba(125,211,252,.4) calc(50% + 1px), transparent calc(50% + 1px)),
            linear-gradient(-45deg, transparent calc(50% - 1px), rgba(125,211,252,.4) calc(50% - 1px), rgba(125,211,252,.4) calc(50% + 1px), transparent calc(50% + 1px));
        color: #7dd3fc;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* ===== PROMO BANNERS ===== */
    .promo-banners {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
        margin-bottom: 16px;
    }

    .promo-banner {
        border-radius: 12px;
        padding: 20px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        overflow: hidden;
    }

    .promo-banner .content {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .promo-banner .tag {
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .promo-banner h3 {
        margin: 0;
        font-size: 15.5px;
        font-weight: 700;
        color: #1e293b;
    }

    .promo-banner .btn-sm {
        border: none;
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 11.5px;
        font-weight: 600;
        color: #fff;
        cursor: pointer;
        align-self: flex-start;
    }

    .promo-img {
        width: 84px;
        height: 84px;
        border-radius: 14px;
        flex-shrink: 0;
        object-fit: contain;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, .06);
        box-shadow: 0 2px 8px rgba(0, 0, 0, .06);
        padding: 8px;
        box-sizing: border-box;
    }

    .promo-banner.apple {
        background: #eff6ff;
    }

    .promo-banner.apple .tag {
        color: #2563eb;
    }

    .promo-banner.apple .btn-sm {
        background: #2563eb;
    }

    .promo-banner.samsung {
        background: #f3e8ff;
    }

    .promo-banner.samsung .tag {
        color: #7c3aed;
    }

    .promo-banner.samsung .btn-sm {
        background: #7c3aed;
    }

    .promo-banner.bao-hanh {
        background: #fff7ed;
    }

    .promo-banner.bao-hanh .tag {
        color: #f97316;
    }

    .promo-banner.bao-hanh .btn-sm {
        background: #f97316;
    }

    /* ===== NEWS ===== */
    .news-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }

    .news-card {
        background: rgba(255,255,255,.82);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(186,230,253,.6);
        border-radius: 12px;
        overflow: hidden;
        text-decoration: none;
        color: #000;
        transition: box-shadow .25s, transform .25s, border-color .25s;
        display: block;
    }

    .news-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 28px rgba(14,165,233,.16);
        border-color: #7dd3fc;
    }

    .news-card-img {
        height: 160px;
        overflow: hidden;
        background: linear-gradient(160deg, #f0f9ff 0%, #e0f2fe 100%);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .news-card-img img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 6px;
        box-sizing: border-box;
    }

    .news-card-body {
        padding: 14px;
    }

    .news-card-title {
        font-size: 13px;
        font-weight: 600;
        color: #0c4a6e;
        line-height: 1.5;
        min-height: 40px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .news-card-excerpt {
        color: #6b7280;
        font-size: 12px;
        line-height: 1.5;
        margin: 5px 0 6px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .news-card-meta {
        color: #0284c7;
        opacity: .7;
        font-size: 11px;
    }

    /* ===== FOOTER ===== */
    .footer {
        margin-top: 0;
        background: #fff;
        border-top: 1px solid #e5e7eb;
        padding: 32px 0;
    }

    .footer-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
    }

    /* ===== RESPONSIVE ===== */
    @media(max-width:1100px) {
        .products-grid {
            grid-template-columns: repeat(4, 1fr);
        }

        .brands-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    @media(max-width:860px) {
        .products-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .news-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .promo-banners {
            grid-template-columns: 1fr;
        }

        .hero-inner {
            grid-template-columns: 1fr;
        }

        .trust-bar .inner {
            grid-template-columns: repeat(2, 1fr);
        }

        .footer-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .brands-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    @media(max-width:540px) {
        .products-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .news-grid {
            grid-template-columns: 1fr;
        }

        .trust-bar .inner {
            grid-template-columns: repeat(2, 1fr);
        }

        .brands-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
</style>
@endpush

@section('content')
<div class="page-body">

    {{-- Sky canvas (clouds) --}}
    <canvas id="sky-canvas" aria-hidden="true"></canvas>

    {{-- ===== HERO SLIDER ===== --}}
    @if($banners->isNotEmpty())
    <section class="hero">
        @if($banners->count() > 1)
        <button class="hero-arrow left" id="heroPrev"><i class="fas fa-chevron-left"></i></button>
        @endif

        @foreach($banners as $i => $banner)
        @if($banner->isImageOnly())
        <div class="hero-inner hero-slide hero-slide-image {{ $i === 0 ? 'active' : '' }}" data-slide="{{ $i }}" style="{{ $i === 0 ? '' : 'display:none' }}">
            @if($banner->button_link)
            <a href="{{ $banner->button_link }}" style="display:block;width:100%;height:100%">
                @endif
                @if($banner->image)
                <img src="{{ $banner->image }}" alt="banner" style="width:100%;height:100%;object-fit:cover;display:block">
                @else
                <div class="hero-img-placeholder" style="width:100%;height:100%"><i class="fas fa-image fa-2x"></i></div>
                @endif
                @if($banner->button_link)
            </a>
            @endif
        </div>
        @else
        <div class="hero-inner hero-slide {{ $i === 0 ? 'active' : '' }}" data-slide="{{ $i }}"
            style="{{ $i === 0 ? '' : 'display:none' }}{{ $banner->bg_color ? ';background:'.$banner->bg_color.';' : '' }}">
            <div class="hero-content" style="{{ $banner->text_color ? 'color:'.$banner->text_color.';' : '' }}">
                @if($banner->label)
                <div class="hero-label" style="{{ $banner->text_color ? 'color:'.$banner->text_color.';opacity:.85;' : '' }}">{{ $banner->label }}</div>
                @endif
                @if($banner->title)
                <h1 class="hero-title" style="{{ $banner->text_color ? 'color:'.$banner->text_color.';' : '' }}">{!! nl2br(e($banner->title)) !!}</h1>
                @endif
                @if($banner->description)
                <p class="hero-desc" style="{{ $banner->text_color ? 'color:'.$banner->text_color.';opacity:.8;' : '' }}">{{ $banner->description }}</p>
                @endif
                @if($banner->price_text)
                <div class="hero-price">{{ $banner->price_text }}</div>
                @endif
                @if($banner->button_text)
                <a href="{{ $banner->button_link ?: '#' }}" class="btn-primary" style="width:fit-content">{{ $banner->button_text }}</a>
                @endif
            </div>
            <div class="hero-img">
                @if($banner->image)
                <img src="{{ $banner->image }}" alt="{{ $banner->title }}">
                @else
                <div class="hero-img-placeholder"><i class="fas fa-image fa-2x"></i></div>
                @endif
            </div>
        </div>
        @endif
        @endforeach

        @if($banners->count() > 1)
        <button class="hero-arrow right" id="heroNext"><i class="fas fa-chevron-right"></i></button>
        <div class="hero-dots">
            @foreach($banners as $i => $banner)
            <div class="hero-dot {{ $i === 0 ? 'active' : '' }}" data-slide="{{ $i }}"></div>
            @endforeach
        </div>
        @endif
    </section>
    @endif

    {{-- ===== SỰ KIỆN / KHUYẾN MÃI ===== --}}
    @if(isset($events) && $events->isNotEmpty())
    <div class="events-strip">
        @foreach($events as $event)
        <a href="{{ $event->button_link ?: '#' }}" class="event-card"
            style="background:{{ $event->bg_color ?: '#263238' }};color:{{ $event->text_color ?: '#fff' }}">
            @if($event->image)
            <img src="{{ $event->image }}" alt="{{ $event->title }}" class="event-card-bg">
            @endif
            <div class="event-card-body">
                @if($event->tag)
                <div class="event-card-tag">{{ $event->tag }}</div>
                @endif
                <div class="event-card-title">{{ $event->title }}</div>
                @if($event->offer_text)
                <div class="event-card-offer">{{ $event->offer_text }}</div>
                @endif
            </div>
        </a>
        @endforeach
    </div>
    @endif

    {{-- ===== TRUST BAR ===== --}}
    <div class="trust-bar">
        <div class="inner">
            <div class="trust-bar-item">
                <div class="tbi-icon"><i class="fas fa-truck"></i></div>
                <div class="tbi-text"><b>Giao hàng miễn phí</b><span>Đơn hàng từ 500k</span></div>
            </div>
            <div class="trust-bar-item">
                <div class="tbi-icon"><i class="fas fa-shield-alt"></i></div>
                <div class="tbi-text"><b>Chính hãng 100%</b><span>Bảo hành toàn quốc</span></div>
            </div>
            <div class="trust-bar-item">
                <div class="tbi-icon"><i class="fas fa-sync-alt"></i></div>
                <div class="tbi-text"><b>Đổi trả dễ dàng</b><span>Trong vòng 30 ngày</span></div>
            </div>
            <div class="trust-bar-item">
                <div class="tbi-icon"><i class="fas fa-credit-card"></i></div>
                <div class="tbi-text"><b>Trả góp 0%</b><span>Thủ tục nhanh chóng</span></div>
            </div>
            <div class="trust-bar-item">
                <div class="tbi-icon"><i class="fas fa-headset"></i></div>
                <div class="tbi-text"><b>Hỗ trợ 24/7</b><span>Hotline: 1900 1234</span></div>
            </div>
        </div>
    </div>

    {{-- ===== DANH MỤC THƯƠNG HIỆU ===== --}}
    @if(isset($brands) && $brands->isNotEmpty())
    <div class="brands-section">
        <div class="brands-bg">
            <div class="brands-header">
                <div class="brands-title-wrap">
                    <div class="brands-title-icon">
                        <i class="fas fa-tag"></i>
                    </div>
                    <div>
                        <div class="brands-title">Danh mục thương hiệu</div>
                        <div class="brands-subtitle">Khám phá sản phẩm theo thương hiệu yêu thích</div>
                    </div>
                </div>
                <a href="{{ route('products.index') }}" class="brands-link">Xem tất cả →</a>
            </div>

            <div class="brands-grid">
                @foreach($brands as $brand)
                <a href="{{ route('products.index', ['brand' => $brand->id]) }}" class="brand-chip">
                    <div class="brand-chip-logo">
                        @if($brand->logo)
                            <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}" loading="lazy">
                        @else
                            <div class="brand-chip-initials">{{ strtoupper(mb_substr($brand->name, 0, 2)) }}</div>
                        @endif
                    </div>
                    <div class="brand-chip-name">{{ $brand->name }}</div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <div class="container">

        {{-- ===== GỢI Ý DÀNH CHO BẠN (AI cá nhân hóa) ===== --}}
        @auth
        @if(!empty($suggestedProducts) && $suggestedProducts->isNotEmpty())
        <section class="section">
            <div class="section-header">
                <h2 class="section-title">Gợi ý dành cho bạn</h2>
            </div>
            <div class="products-grid">
                @foreach($suggestedProducts as $product)
                <a href="{{ route('products.show', ['slug' => $product->slug, 'from' => 'suggestion', 'via' => 'homepage']) }}"
                    class="product-card">
                    <span class="wish"><i class="far fa-heart"></i></span>
                    <div class="product-card-img">
                        @if($product->first_image)
                        <img src="{{ $product->first_image }}" alt="{{ $product->name }}">
                        @else
                        <div class="img-placeholder"><i class="fas fa-image"></i></div>
                        @endif
                    </div>
                    <div class="product-card-body">
                        <div class="product-card-name">{{ $product->name }}</div>
                        <div class="product-card-price">{{ number_format($product->sale_price) }}đ</div>
                    </div>
                </a>
                @endforeach
            </div>
        </section>
        @endif
        @endauth

        {{-- ===== SẢN PHẨM MỚI NHẤT ===== --}}
        <section class="section section--products">
            <div class="section-header">
                <h2 class="section-title">Sản phẩm mới nhất</h2>
                <a href="{{ route('products.index') }}" class="section-link">
                    Xem tất cả →
                </a>
            </div>

            <div class="products-grid">

                @forelse($newProducts as $product)
                <a href="{{ route('products.show', $product->slug) }}"
                    class="product-card">

                    <span class="wish">
                        <i class="far fa-heart"></i>
                    </span>

                    <div class="product-card-img">

                        @if($product->first_image)
                        <img src="{{ $product->first_image }}" alt="{{ $product->name }}">
                        @else
                        <div class="img-placeholder"
                            style="width:100%;height:100%">
                            <i class="fas fa-image"></i>
                        </div>
                        @endif

                    </div>

                    <div class="product-card-body">

                        <div class="product-card-name">
                            {{ $product->name }}
                        </div>

                        <div style="font-size:11px;color:#6b7280;margin-top:4px;min-height:14px">
                            {{ $product->brand->name ?? '' }}
                        </div>

                        <span class="product-card-price">
                            {{ number_format($product->price) }}đ
                        </span>

                        <div class="stars">
                            ★★★★★
                            <span class="review-count">
                                ({{ $product->reviews_count ?? 0 }})
                            </span>
                        </div>

                    </div>

                </a>

                @empty

                <div style="grid-column:1/-1;padding:40px;text-align:center">
                    Chưa có sản phẩm nào.
                </div>

                @endforelse

            </div>
        </section>

        {{-- ===== PROMO BANNERS ===== --}}
        <div class="promo-banners">
            <div class="promo-banner apple">
                <div class="content">
                    <div class="tag">THẾ GIỚI APPLE</div>
                    <h3>Giảm đến 4TR</h3>
                    <button class="btn-sm">Săn Ngay</button>
                </div>
                <img class="promo-img" src="{{ asset('images/promo-apple.png') }}" alt="Thế giới Apple">
            </div>
            <div class="promo-banner samsung">
                <div class="content">
                    <div class="tag">SAMSUNG STORE</div>
                    <h3>Thu cũ đổi mới</h3>
                    <button class="btn-sm">Xem Thêm</button>
                </div>
                <img class="promo-img" src="{{ asset('images/promo-samsung.png') }}" alt="Samsung Store">
            </div>
            <div class="promo-banner bao-hanh">
                <div class="content">
                    <div class="tag">BẢO HÀNH CHÍNH HÃNG</div>
                    <h3>An tâm 12 tháng</h3>
                    <button class="btn-sm">Xem Chi Tiết</button>
                </div>
                <img class="promo-img" src="{{ asset('images/promo-baohanh.png') }}" alt="Bảo hành chính hãng">
            </div>
        </div>

        {{-- ===== TIN TỨC CÔNG NGHỆ ===== --}}
        @if($latestNews->isNotEmpty())
        <section class="section section--news">

            <div class="section-header">
                <h2 class="section-title">Tin tức công nghệ</h2>

                <a href="{{ route('news.index') }}"
                    class="section-link">
                    Xem tin mới nhất →
                </a>
            </div>

            <div class="news-grid">

                @foreach($latestNews as $news)

                <a href="{{ route('news.show', $news->slug) }}"
                    class="news-card">

                    <div class="news-card-img">

                        @if(!empty($news->thumbnail))
                        <img src="{{ asset('storage/' . $news->thumbnail) }}"
                            alt="{{ $news->title }}">
                        @else
                        <div class="img-placeholder"
                            style="height:100%">
                            <i class="fas fa-image fa-2x"></i>
                        </div>
                        @endif

                    </div>

                    <div class="news-card-body">

                        <div class="news-card-title">
                            {{ $news->title }}
                        </div>

                        <div class="news-card-excerpt">
                            {{ \Illuminate\Support\Str::limit(strip_tags($news->content), 100) }}
                        </div>

                        <div class="news-card-meta">

                            @if($news->published_at)
                            {{ $news->published_at->diffForHumans() }}
                            @endif

                            · {{ number_format($news->views ?? 0) }} lượt xem

                            @if($news->category)
                            · {{ $news->category->name }}
                            @endif

                        </div>

                    </div>

                </a>

                @endforeach

            </div>

        </section>
        @endif

    </div><!-- /.container -->
</div><!-- /.page-body -->
@endsection

@push('scripts')
<script>
    (function() {
        const slides = document.querySelectorAll('.hero-slide');
        const dots = document.querySelectorAll('.hero-dot');
        if (slides.length <= 1) return;

        let current = 0;
        let autoTimer;

        function showSlide(index) {
            slides.forEach((s, i) => s.style.display = i === index ? '' : 'none');
            dots.forEach((d, i) => d.classList.toggle('active', i === index));
            current = index;
        }

        function nextSlide() {
            showSlide((current + 1) % slides.length);
        }

        function prevSlide() {
            showSlide((current - 1 + slides.length) % slides.length);
        }

        function resetAutoplay() {
            clearInterval(autoTimer);
            autoTimer = setInterval(nextSlide, 5000);
        }

        document.getElementById('heroNext')?.addEventListener('click', () => {
            nextSlide();
            resetAutoplay();
        });
        document.getElementById('heroPrev')?.addEventListener('click', () => {
            prevSlide();
            resetAutoplay();
        });
        dots.forEach((dot, i) => dot.addEventListener('click', () => {
            showSlide(i);
            resetAutoplay();
        }));

        resetAutoplay();
    })();
</script>

<script>
/* ============================================================
   HOMEPAGE ANIMATIONS
   ============================================================ */
(function () {

    /* ----------------------------------------------------------
       1. CANVAS CLOUDS
    ---------------------------------------------------------- */
    const canvas = document.getElementById('sky-canvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let W, H, clouds = [];

        function resize() {
            W = canvas.width  = window.innerWidth;
            H = canvas.height = window.innerHeight;
        }
        window.addEventListener('resize', resize);
        resize();

        function makeCloud() {
            return {
                x:    Math.random() * W * 1.2,
                y:    Math.random() * H * .6,
                r:    60 + Math.random() * 120,
                dx:   .18 + Math.random() * .28,
                alpha: .06 + Math.random() * .12,
            };
        }

        for (let i = 0; i < 9; i++) clouds.push(makeCloud());

        function drawCloud(c) {
            const g = ctx.createRadialGradient(c.x, c.y, 0, c.x, c.y, c.r);
            g.addColorStop(0,   `rgba(255,255,255,${c.alpha})`);
            g.addColorStop(.6,  `rgba(186,230,253,${c.alpha * .6})`);
            g.addColorStop(1,   'rgba(186,230,253,0)');
            ctx.beginPath();
            ctx.arc(c.x, c.y, c.r, 0, Math.PI * 2);
            ctx.fillStyle = g;
            ctx.fill();

            // puff
            [-.5, .5].forEach(o => {
                ctx.beginPath();
                ctx.arc(c.x + c.r * .55 * o, c.y - c.r * .18, c.r * .72, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(255,255,255,${c.alpha * .7})`;
                ctx.fill();
            });
        }

        function animateClouds() {
            ctx.clearRect(0, 0, W, H);
            clouds.forEach(c => {
                drawCloud(c);
                c.x += c.dx;
                if (c.x - c.r > W * 1.2) { c.x = -c.r * 2; c.y = Math.random() * H * .6; }
            });
            requestAnimationFrame(animateClouds);
        }
        animateClouds();
    }

    /* ----------------------------------------------------------
       2. RISING BUBBLES
    ---------------------------------------------------------- */
    function spawnBubble() {
        const el = document.createElement('div');
        el.className = 'bubble';
        const size = 6 + Math.random() * 18;
        const dur  = 8 + Math.random() * 14;
        el.style.cssText = [
            `width:${size}px`, `height:${size}px`,
            `left:${Math.random() * 100}vw`,
            `bottom:-${size}px`,
            `animation-duration:${dur}s`,
            `animation-delay:${Math.random() * 6}s`,
        ].join(';');
        document.querySelector('.page-body').appendChild(el);
        setTimeout(() => el.remove(), (dur + 6) * 1000);
    }
    for (let i = 0; i < 14; i++) spawnBubble();
    setInterval(spawnBubble, 3000);

    /* ----------------------------------------------------------
       3. SCROLL REVEAL  (IntersectionObserver)
    ---------------------------------------------------------- */
    const revealEls = document.querySelectorAll(
        '.section, .trust-bar, .brands-section, .promo-banner, .event-card, .events-strip'
    );
    revealEls.forEach(el => el.classList.add('reveal'));

    // Products & news grids — stagger
    document.querySelectorAll('.products-grid, .news-grid, .brands-grid').forEach(el => {
        el.classList.add('stagger-children');
    });

    const io = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                io.unobserve(entry.target);
            }
        });
    }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });

    document.querySelectorAll('.reveal, .stagger-children').forEach(el => io.observe(el));

    /* ----------------------------------------------------------
       4. RIPPLE on cards
    ---------------------------------------------------------- */
    document.querySelectorAll('.product-card, .news-card, .brand-chip, .event-card').forEach(card => {
        card.style.position = 'relative';
        card.style.overflow = 'hidden';
        card.addEventListener('click', function (e) {
            const rect = card.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height) * 1.6;
            const ripple = document.createElement('span');
            ripple.className = 'ripple-wave';
            ripple.style.cssText = [
                `width:${size}px`, `height:${size}px`,
                `left:${e.clientX - rect.left - size/2}px`,
                `top:${e.clientY - rect.top  - size/2}px`,
            ].join(';');
            card.appendChild(ripple);
            ripple.addEventListener('animationend', () => ripple.remove());
        });
    });

    /* ----------------------------------------------------------
       5. 3-D TILT on product & news cards
    ---------------------------------------------------------- */
    document.querySelectorAll('.product-card, .news-card').forEach(card => {
        card.addEventListener('mousemove', function (e) {
            const rect  = card.getBoundingClientRect();
            const cx    = rect.left + rect.width  / 2;
            const cy    = rect.top  + rect.height / 2;
            const dx    = (e.clientX - cx) / (rect.width  / 2);
            const dy    = (e.clientY - cy) / (rect.height / 2);
            const rotX  = -dy * 6;
            const rotY  =  dx * 6;
            card.style.transform = `perspective(600px) rotateX(${rotX}deg) rotateY(${rotY}deg) translateY(-4px) scale(1.02)`;
        });
        card.addEventListener('mouseleave', function () {
            card.style.transform = '';
            card.style.transition = 'transform .4s cubic-bezier(.16,1,.3,1), box-shadow .25s, border-color .25s';
            setTimeout(() => card.style.transition = '', 400);
        });
    });

    /* ----------------------------------------------------------
       6. TRUST BAR — count-up numbers (if any)
    ---------------------------------------------------------- */
    // Nothing numeric right now — reserved for future

})();
</script>
@endpush