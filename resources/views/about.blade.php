@extends('layouts.app')
 
@section('title', 'ElectronicShop - Về chúng tôi')
 
@php
    $showSearch = true;
@endphp
 
@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
/* ==========================================================
   ELECTRONICSHOP ABOUT
   Premium editorial technology-brand layout.
   Structure inspired by modern OEM "About" pages.
   ========================================================== */
 
.about-page{
    --black:#050505;
    --white:#fff;
    --gray:#8b8b8f;
    --soft:#f4f4f4;
    --line:#dedede;
    --blue:#1677ff;
    background:#fff;
    color:var(--black);
    overflow:hidden;
    font-family:"Inter",-apple-system,BlinkMacSystemFont,"SF Pro Display","SF Pro Text","Helvetica Neue",Arial,system-ui,sans-serif;
}
.about-page *{box-sizing:border-box}
.about-page a{text-decoration:none;color:inherit}
.about-container{width:min(1440px,calc(100% - 48px));margin:auto}
 
/* ---------- reveal ---------- */
.about-reveal{
    opacity:0;
    transform:translateY(45px);
    transition:opacity .9s cubic-bezier(.16,1,.3,1),transform .9s cubic-bezier(.16,1,.3,1);
}
.about-reveal.visible{opacity:1;transform:none}
 
/* ---------- hero ---------- */
.about-hero{
    position:relative;
    height:calc(100vh - 72px);
    min-height:720px;
    background:#000;
    color:#fff;
    overflow:hidden;
}
.about-hero-media,
.about-hero-media video{
    position:absolute;
    inset:0;
    width:100%;
    height:100%;
}
.about-hero-media video{object-fit:cover}
.about-hero-shade{
    position:absolute;
    inset:0;
    background:
        linear-gradient(180deg,rgba(0,0,0,.05) 0%,rgba(0,0,0,.12) 40%,rgba(0,0,0,.9) 100%);
}
.about-hero-content{
    position:absolute;
    left:0;
    right:0;
    bottom:0;
    z-index:2;
    padding-bottom:90px;
}
.about-eyebrow{
    display:block;
    margin-bottom:24px;
    color:rgba(255,255,255,.66);
    font-size:13px;
    font-weight:700;
    letter-spacing:.16em;
    text-transform:uppercase;
}
.about-hero h1{
    max-width:1100px;
    margin:0;
    font-size:clamp(58px,9vw,140px);
    line-height:.88;
    letter-spacing:-.075em;
    font-weight:600;
    color:#fff;
}
.about-hero h1 span{color:rgba(255,255,255,.48)}
.about-hero-description{
    max-width:680px;
    margin:34px 0 0;
    color:rgba(255,255,255,.73);
    font-size:clamp(17px,1.7vw,23px);
    line-height:1.5;
}
.about-scroll{
    position:absolute;
    right:30px;
    bottom:30px;
    z-index:3;
    display:flex;
    gap:10px;
    align-items:center;
    color:rgba(255,255,255,.65);
    font-size:11px;
    letter-spacing:.14em;
    text-transform:uppercase;
}
 
/* ---------- chapter bar ---------- */
.about-nav{
    position:sticky;
    top:0;
    z-index:50;
    background:rgba(255,255,255,.9);
    border-bottom:1px solid rgba(0,0,0,.08);
    backdrop-filter:blur(18px);
    -webkit-backdrop-filter:blur(18px);
}
.about-nav-inner{
    width:min(1440px,calc(100% - 48px));
    min-height:58px;
    margin:auto;
    display:flex;
    align-items:center;
    gap:32px;
    overflow-x:auto;
    scrollbar-width:none;
}
.about-nav-inner::-webkit-scrollbar{display:none}
.about-nav a{
    flex:0 0 auto;
    color:#666;
    font-size:13px;
    white-space:nowrap;
    transition:color .2s ease;
}
.about-nav a:hover{color:#000}
 
/* ---------- brand slogan ---------- */
.about-slogan{
    padding:175px 0 165px;
    background:#fff;
}
.about-slogan-grid{
    display:grid;
    grid-template-columns:.55fr 1.45fr;
    gap:70px;
}
.about-label{
    color:#777;
    font-size:12px;
    font-weight:700;
    letter-spacing:.15em;
    text-transform:uppercase;
}
.about-slogan h2{
    max-width:1050px;
    margin:0;
    font-size:clamp(45px,6.4vw,94px);
    line-height:.98;
    letter-spacing:-.068em;
    font-weight:600;
}
.about-slogan h2 span{color:#9a9a9f}
.about-slogan p{
    max-width:720px;
    margin:40px 0 0;
    color:#6f6f73;
    font-size:19px;
    line-height:1.7;
}
 
/* ---------- users ---------- */
.about-users{
    padding:150px 0;
    background:#f4f4f4;
}
.about-users-head{
    display:grid;
    grid-template-columns:.55fr 1.45fr;
    gap:70px;
    margin-bottom:85px;
}
.about-users-head h2{
    margin:0;
    max-width:900px;
    font-size:clamp(48px,6vw,86px);
    line-height:.94;
    letter-spacing:-.065em;
}
.about-users-head h2 span{color:#999}
.about-user-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    border-top:1px solid #d2d2d2;
    border-bottom:1px solid #d2d2d2;
}
.about-user-card{
    min-height:390px;
    padding:35px 40px 45px;
    border-right:1px solid #d2d2d2;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
}
.about-user-card:last-child{border-right:0}
.about-user-number{font-size:12px;color:#999;letter-spacing:.12em}
.about-user-card h3{
    margin:0 0 16px;
    font-size:31px;
    line-height:1;
    letter-spacing:-.045em;
}
.about-user-card p{
    max-width:350px;
    margin:0;
    color:#737377;
    font-size:16px;
    line-height:1.65;
}
 
/* ---------- company ---------- */
.about-company{
    padding:155px 0;
    background:#fff;
}
.about-company-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    min-height:680px;
}
.about-company-media{
    position:relative;
    min-height:620px;
    overflow:hidden;
    background:#0a0a0a;
}
.about-company-media video{
    width:100%;
    height:100%;
    object-fit:cover;
}
.about-company-media:after{
    content:"";
    position:absolute;
    inset:0;
    background:linear-gradient(180deg,transparent 55%,rgba(0,0,0,.5));
}
.about-company-copy{
    padding:75px clamp(35px,6vw,100px);
    background:#080808;
    color:#fff;
    display:flex;
    flex-direction:column;
    justify-content:center;
}
.about-company-copy .about-label{color:#777}
.about-company-copy h2{
    margin:25px 0 0;
    font-size:clamp(46px,5.2vw,78px);
    line-height:.94;
    letter-spacing:-.065em;
    color:#fff;
}
.about-company-copy h2 span{color:#686868}
.about-company-copy p{
    max-width:570px;
    margin:38px 0 0;
    color:#a1a1a6;
    font-size:18px;
    line-height:1.7;
}
 
/* ---------- products ---------- */
.about-products{
    padding:155px 0;
    background:#fff;
}
.about-products-head{
    display:grid;
    grid-template-columns:.55fr 1.45fr;
    gap:70px;
    margin-bottom:70px;
}
.about-products-head h2{
    margin:0;
    max-width:900px;
    font-size:clamp(48px,6vw,86px);
    line-height:.94;
    letter-spacing:-.065em;
}
.about-products-head h2 span{color:#999}
.about-products-head p{
    max-width:650px;
    margin:25px 0 0;
    color:#707075;
    font-size:18px;
    line-height:1.7;
}
.about-product-list{display:flex;flex-direction:column}
.about-product{
    position:relative;
    min-height:460px;
    padding:50px;
    overflow:hidden;
    display:flex;
    align-items:flex-end;
    border-bottom:1px solid #d8d8d8;
}
.about-product:first-child{border-top:1px solid #d8d8d8}
.about-product:nth-child(1){background:#f3f3f3}
.about-product:nth-child(2){background:#080808;color:#fff}
.about-product:nth-child(3){background:#eaf2ff}
.about-product:before{
    content:"";
    position:absolute;
    width:650px;
    height:650px;
    right:-160px;
    top:-230px;
    border-radius:50%;
    background:radial-gradient(circle,rgba(255,255,255,.98),transparent 67%);
}
.about-product:nth-child(2):before{
    background:radial-gradient(circle,rgba(255,255,255,.15),transparent 68%);
}
.about-product-copy{position:relative;z-index:2;max-width:700px}
.about-product small{
    display:block;
    margin-bottom:17px;
    color:#777;
    font-size:12px;
    font-weight:700;
    letter-spacing:.14em;
    text-transform:uppercase;
}
.about-product:nth-child(2) small{color:#777}
.about-product h3{
    margin:0;
    font-size:clamp(42px,5vw,72px);
    line-height:.94;
    letter-spacing:-.065em;
    color:inherit;
}
.about-product:nth-child(2) h3{color:#fff}
.about-product p{
    max-width:560px;
    margin:25px 0 0;
    color:#737377;
    font-size:17px;
    line-height:1.6;
}
.about-product:nth-child(2) p{color:#999}
 
/* ---------- technology ---------- */
.about-tech{
    padding:160px 0;
    background:#f4f4f4;
}
.about-tech-head{
    display:grid;
    grid-template-columns:.55fr 1.45fr;
    gap:70px;
    margin-bottom:85px;
}
.about-tech-head h2{
    margin:0;
    font-size:clamp(48px,6vw,88px);
    line-height:.94;
    letter-spacing:-.065em;
}
.about-tech-head h2 span{color:#999}
.about-tech-head p{
    max-width:700px;
    margin:30px 0 0;
    color:#707075;
    font-size:18px;
    line-height:1.7;
}
.about-stat-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    border-top:1px solid #cfcfcf;
}
.about-stat{
    min-height:245px;
    padding:35px 25px;
    border-right:1px solid #cfcfcf;
}
.about-stat:last-child{border-right:0}
.about-stat strong{
    display:block;
    font-size:clamp(42px,5vw,70px);
    line-height:1;
    letter-spacing:-.06em;
}
.about-stat b{
    display:block;
    margin-top:28px;
    font-size:16px;
    font-weight:600;
}
.about-stat span{
    display:block;
    max-width:220px;
    margin-top:9px;
    color:#777;
    font-size:14px;
    line-height:1.5;
}
 
/* ---------- partners ---------- */
.about-partners{
    padding:155px 0;
    background:#080808;
    color:#fff;
}
.about-partners-head{
    display:grid;
    grid-template-columns:.55fr 1.45fr;
    gap:70px;
    margin-bottom:80px;
}
.about-partners-head h2{
    margin:0;
    font-size:clamp(48px,6vw,88px);
    line-height:.94;
    letter-spacing:-.065em;
    color:#fff;
}
.about-partners-head h2 span{color:#666}
.about-partners-head p{
    max-width:650px;
    margin:30px 0 0;
    color:#999;
    font-size:18px;
    line-height:1.7;
}
.about-partner-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    border-top:1px solid #292929;
    border-bottom:1px solid #292929;
}
.about-partner{
    min-height:300px;
    padding:35px;
    border-right:1px solid #292929;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
}
.about-partner:last-child{border-right:0}
.about-partner-number{color:#666;font-size:12px}
.about-partner h3{
    margin:0;
    font-size:30px;
    letter-spacing:-.045em;
    color:#fff;
}
.about-partner p{
    max-width:330px;
    margin:12px 0 0;
    color:#888;
    font-size:14px;
    line-height:1.6;
}
 
/* ---------- final ---------- */
.about-final{
    padding:185px 0;
    text-align:center;
    background:#fff;
}
.about-final h2{
    max-width:1100px;
    margin:auto;
    font-size:clamp(56px,8vw,120px);
    line-height:.9;
    letter-spacing:-.075em;
}
.about-final h2 span{color:#999}
.about-final p{
    max-width:600px;
    margin:38px auto 42px;
    color:#777;
    font-size:18px;
    line-height:1.6;
}
.about-actions{
    display:flex;
    justify-content:center;
    flex-wrap:wrap;
    gap:12px;
}
.about-actions a{
    min-height:50px;
    padding:0 27px;
    border-radius:999px;
    display:inline-flex;
    align-items:center;
    gap:9px;
    font-size:14px;
    font-weight:600;
}
.about-primary{background:#080808;color:#fff!important}
.about-secondary{background:#f2f2f2;color:#111!important}
 
/* ---------- section head images ---------- */
.about-head-image{
    width:100%;
    aspect-ratio:4/5;
    object-fit:cover;
    border-radius:18px;
    display:block;
    margin-top:26px;
}
.about-product-image{
    position:absolute;
    inset:0;
    width:100%;
    height:100%;
    object-fit:cover;
    z-index:0;
    opacity:.9;
}
.about-partner-image{
    width:100%;
    aspect-ratio:16/10;
    object-fit:cover;
    border-radius:12px;
    margin-bottom:18px;
}
 
/* ---------- responsive ---------- */
@media(max-width:1000px){
    .about-slogan-grid,
    .about-users-head,
    .about-products-head,
    .about-tech-head,
    .about-partners-head{grid-template-columns:1fr;gap:35px}
    .about-user-grid,
    .about-partner-grid{grid-template-columns:1fr}
    .about-user-card{border-right:0;border-bottom:1px solid #d2d2d2;min-height:300px}
    .about-user-card:last-child{border-bottom:0}
    .about-company-grid{grid-template-columns:1fr}
    .about-company-media{min-height:520px}
    .about-stat-grid{grid-template-columns:repeat(2,1fr)}
    .about-stat:nth-child(2){border-right:0}
    .about-stat:nth-child(-n+2){border-bottom:1px solid #cfcfcf}
    .about-partner{border-right:0;border-bottom:1px solid #292929}
    .about-partner:last-child{border-bottom:0}
}
@media(max-width:700px){
    .about-container,
    .about-nav-inner{width:calc(100% - 32px)}
    .about-hero{min-height:650px;height:calc(100vh - 64px)}
    .about-hero-content{padding-bottom:65px}
    .about-hero h1{font-size:56px}
    .about-hero-description{font-size:17px}
    .about-scroll{display:none}
    .about-slogan,
    .about-users,
    .about-company,
    .about-products,
    .about-tech,
    .about-partners{padding:100px 0}
    .about-final{padding:120px 16px}
    .about-product{min-height:390px;padding:30px}
    .about-company-media{min-height:420px}
    .about-company-copy{padding:55px 30px}
    .about-stat-grid{grid-template-columns:1fr}
    .about-stat{border-right:0!important;border-bottom:1px solid #cfcfcf!important}
    .about-stat:last-child{border-bottom:0!important}
    .about-nav-inner{gap:24px}
}
</style>
@endpush
 
@section('content')
<div class="about-page">
 
    {{-- 01 · BRAND SLOGAN / HERO --}}
    <section class="about-hero">
        <div class="about-hero-media">
            <video autoplay muted loop playsinline preload="metadata">
                <source src="{{ asset('videos/electronics-hero.mp4') }}" type="video/mp4">
            </video>
            <div class="about-hero-shade"></div>
        </div>
 
        <div class="about-hero-content about-container about-reveal">
            <span class="about-eyebrow">ElectronicShop · About Us</span>
            <h1>
                Make technology.<br>
                <span>Make your moment.</span>
            </h1>
            <p class="about-hero-description">
                Công nghệ không chỉ là một thiết bị.
                Đó là cách chúng ta làm việc, sáng tạo, kết nối
                và tận hưởng từng khoảnh khắc.
            </p>
        </div>
 
        <div class="about-scroll">
            Khám phá <i class="bi bi-arrow-down"></i>
        </div>
    </section>
 
    {{-- CHAPTER NAV --}}
    <nav class="about-nav">
        <div class="about-nav-inner">
            <a href="#users">01 · Người dùng</a>
            <a href="#company">02 · Chúng tôi</a>
            <a href="#products">03 · Sản phẩm & dịch vụ</a>
            <a href="#technology">04 · Thế mạnh công nghệ</a>
            <a href="#partners">05 · Hợp tác</a>
        </div>
    </nav>
 
    {{-- 01 · BRAND MESSAGE --}}
    <section class="about-slogan about-reveal">
        <div class="about-container about-slogan-grid">
            <div>
                <div class="about-label">ElectronicShop</div>
                <img class="about-head-image"
                     src="https://images.unsplash.com/photo-1726592139831-8c9208590372?w=1200&q=80&auto=format&fit=crop" loading="lazy"
                     alt="Dòng smartphone tại ElectronicShop">
            </div>
 
            <div>
                <h2>
                    Yêu công nghệ.
                    <span>Trân trọng từng khoảnh khắc.</span>
                </h2>
 
                <p>
                    ElectronicShop được xây dựng với niềm tin rằng công nghệ
                    tốt phải giúp cuộc sống trở nên đơn giản hơn. Chúng tôi
                    tập trung vào sản phẩm, trải nghiệm và dịch vụ để bạn
                    có thể tự tin lựa chọn thiết bị phù hợp với mình.
                </p>
            </div>
        </div>
    </section>
 
    {{-- 02 · USERS --}}
    <section class="about-users about-reveal" id="users">
        <div class="about-container">
            <div class="about-users-head">
                <div>
                    <div class="about-label">Người dùng của chúng tôi</div>
                    <img class="about-head-image"
                         src="https://images.unsplash.com/photo-1764831138635-35873bdd671e?w=1200&q=80&auto=format&fit=crop" loading="lazy"
                         alt="Người dùng trải nghiệm smartphone">
                </div>
 
                <h2>
                    Dành cho những người
                    <span>yêu khám phá và muốn tạo nên điều khác biệt.</span>
                </h2>
            </div>
 
            <div class="about-user-grid">
                <article class="about-user-card">
                    <span class="about-user-number">01</span>
                    <div>
                        <h3>Yêu sáng tạo</h3>
                        <p>
                            Luôn tìm kiếm những công cụ mới để biến ý tưởng
                            thành sản phẩm, hình ảnh, video và những trải nghiệm
                            mang dấu ấn cá nhân.
                        </p>
                    </div>
                </article>
 
                <article class="about-user-card">
                    <span class="about-user-number">02</span>
                    <div>
                        <h3>Không giới hạn</h3>
                        <p>
                            Học tập, làm việc, giải trí hay khám phá thế giới —
                            công nghệ phải đồng hành cùng bạn ở bất kỳ đâu.
                        </p>
                    </div>
                </article>
 
                <article class="about-user-card">
                    <span class="about-user-number">03</span>
                    <div>
                        <h3>Chọn điều phù hợp</h3>
                        <p>
                            Không phải thiết bị đắt nhất luôn là thiết bị tốt nhất.
                            Điều quan trọng là sản phẩm phù hợp với cách bạn sống.
                        </p>
                    </div>
                </article>
            </div>
        </div>
    </section>
 
    {{-- 03 · COMPANY --}}
    <section class="about-company about-reveal" id="company">
        <div class="about-container about-company-grid">
            <div class="about-company-media">
                <video autoplay muted loop playsinline preload="metadata">
                    <source src="{{ asset('videos/electronics-hero.mp4') }}" type="video/mp4">
                </video>
            </div>
 
            <div class="about-company-copy">
                <div class="about-label">Giới thiệu về chúng tôi</div>
 
                <h2>
                    Công nghệ hiện đại.
                    <span>Trải nghiệm đơn giản.</span>
                </h2>
 
                <p>
                    ElectronicShop là nền tảng bán lẻ công nghệ hướng đến
                    trải nghiệm mua sắm hiện đại. Chúng tôi tập trung xây dựng
                    cách trình bày sản phẩm rõ ràng, giao diện trực quan và
                    quy trình mua hàng thuận tiện.
                </p>
 
                <p>
                    Người dùng luôn là trung tâm trong cách chúng tôi xây dựng
                    sản phẩm, nội dung và dịch vụ.
                </p>
            </div>
        </div>
    </section>
 
    {{-- 04 · PRODUCTS & SERVICES --}}
    <section class="about-products about-reveal" id="products">
        <div class="about-container">
            <div class="about-products-head">
                <div>
                    <div class="about-label">Sản phẩm và dịch vụ</div>
                    <img class="about-head-image"
                         src="https://images.unsplash.com/photo-1758745175160-3ff94c3da514?w=1200&q=80&auto=format&fit=crop" loading="lazy"
                         alt="Chi tiết camera và thiết kế smartphone">
                </div>
 
                <div>
                    <h2>
                        Khám phá.
                        <span>Trải nghiệm. Kết nối.</span>
                    </h2>
 
                    <p>
                        Từ smartphone đến phụ kiện đi kèm, ElectronicShop
                        xây dựng một danh mục công nghệ di động để phục vụ
                        học tập, công việc, sáng tạo và giải trí.
                    </p>
                </div>
            </div>
 
            <div class="about-product-list">
                <article class="about-product">
                    <img class="about-product-image"
                         src="https://images.unsplash.com/photo-1764831138635-35873bdd671e?w=1200&q=80&auto=format&fit=crop" loading="lazy"
                         alt="Smartphone flagship">
                    <div class="about-product-copy">
                        <small>01 · Smartphone</small>
                        <h3>Sống trọn từng khoảnh khắc.</h3>
                        <p>
                            Smartphone với thiết kế, camera, hiệu năng và
                            trải nghiệm phù hợp cho từng nhu cầu.
                        </p>
                    </div>
                </article>
 
                <article class="about-product">
                    <img class="about-product-image"
                         src="https://images.unsplash.com/photo-1758745175160-3ff94c3da514?w=1200&q=80&auto=format&fit=crop" loading="lazy"
                         alt="Smartphone hiệu năng cao">
                    <div class="about-product-copy">
                        <small>02 · Hiệu năng & Camera</small>
                        <h3>Bứt phá hiệu suất.</h3>
                        <p>
                            Chip xử lý mạnh mẽ, camera đa dạng và màn hình
                            sắc nét cho học tập, làm việc, chơi game và
                            sáng tạo nội dung.
                        </p>
                    </div>
                </article>
 
                <article class="about-product">
                    <img class="about-product-image"
                         src="https://images.unsplash.com/photo-1553775501-23714990d973?w=1200&q=80&auto=format&fit=crop" loading="lazy"
                         alt="Phụ kiện smartphone">
                    <div class="about-product-copy">
                        <small>03 · Accessories & Connected Life</small>
                        <h3>Hoàn thiện hệ sinh thái.</h3>
                        <p>
                            Ốp lưng, tai nghe, sạc nhanh và thiết bị thông minh
                            giúp chiếc điện thoại của bạn kết nối liền mạch
                            với cuộc sống hàng ngày.
                        </p>
                    </div>
                </article>
            </div>
        </div>
    </section>
 
    {{-- 05 · TECHNOLOGY --}}
    <section class="about-tech about-reveal" id="technology">
        <div class="about-container">
            <div class="about-tech-head">
                <div>
                    <div class="about-label">Thế mạnh công nghệ</div>
                    <img class="about-head-image"
                         src="https://images.unsplash.com/photo-1530961915006-1cbd0d169f28?w=1200&q=80&auto=format&fit=crop" loading="lazy"
                         alt="Giao diện mua sắm smartphone trên ứng dụng">
                </div>
 
                <div>
                    <h2>
                        Tập trung vào
                        <span>những gì thực sự quan trọng.</span>
                    </h2>
 
                    <p>
                        ElectronicShop kết hợp thiết kế giao diện, dữ liệu
                        sản phẩm và trải nghiệm mua sắm để tạo nên một nền tảng
                        công nghệ dễ sử dụng trên mọi thiết bị.
                    </p>
                </div>
            </div>
 
            <div class="about-stat-grid">
                <div class="about-stat">
                    <strong>01</strong>
                    <b>Khám phá</b>
                    <span>Tìm kiếm sản phẩm nhanh và trực quan.</span>
                </div>
 
                <div class="about-stat">
                    <strong>02</strong>
                    <b>So sánh</b>
                    <span>Thông tin và thông số được trình bày rõ ràng.</span>
                </div>
 
                <div class="about-stat">
                    <strong>03</strong>
                    <b>Mua sắm</b>
                    <span>Quy trình từ giỏ hàng đến thanh toán đơn giản.</span>
                </div>
 
                <div class="about-stat">
                    <strong>04</strong>
                    <b>Trải nghiệm</b>
                    <span>Responsive trên desktop, tablet và mobile.</span>
                </div>
            </div>
        </div>
    </section>
 
    {{-- 06 · PARTNERS --}}
    <section class="about-partners about-reveal" id="partners">
        <div class="about-container">
            <div class="about-partners-head">
                <div>
                    <div class="about-label" style="color:#666">Hợp tác thương hiệu</div>
                    <img class="about-head-image"
                         src="https://images.unsplash.com/photo-1726592139831-8c9208590372?w=1200&q=80&auto=format&fit=crop" loading="lazy"
                         alt="Các thương hiệu smartphone hợp tác">
                </div>
 
                <div>
                    <h2>
                        Hợp tác mở ra
                        <span>những trải nghiệm công nghệ mới.</span>
                    </h2>
 
                    <p>
                        ElectronicShop hướng đến việc kết nối nhiều nhóm sản phẩm
                        và thương hiệu để người dùng có thêm lựa chọn phù hợp.
                    </p>
                </div>
            </div>
 
            <div class="about-partner-grid">
                <article class="about-partner">
                    <div>
                        <img class="about-partner-image"
                             src="https://images.unsplash.com/photo-1726592139831-8c9208590372?w=800&q=80&auto=format&fit=crop" loading="lazy"
                             alt="Thương hiệu smartphone flagship">
                        <span class="about-partner-number">01</span>
                        <h3>Smartphone</h3>
                        <p>Những thiết bị trung tâm của trải nghiệm công nghệ cá nhân.</p>
                    </div>
                </article>
 
                <article class="about-partner">
                    <div>
                        <img class="about-partner-image"
                             src="https://images.unsplash.com/photo-1758745175160-3ff94c3da514?w=800&q=80&auto=format&fit=crop" loading="lazy"
                             alt="Công nghệ camera trên smartphone">
                        <span class="about-partner-number">02</span>
                        <h3>Camera & Hiệu năng</h3>
                        <p>Cảm biến camera, chip xử lý và màn hình cho trải nghiệm mượt mà.</p>
                    </div>
                </article>
 
                <article class="about-partner">
                    <div>
                        <img class="about-partner-image"
                             src="https://images.unsplash.com/photo-1553775501-23714990d973?w=800&q=80&auto=format&fit=crop" loading="lazy"
                             alt="Phụ kiện và thiết bị đeo thông minh">
                        <span class="about-partner-number">03</span>
                        <h3>Connected Life</h3>
                        <p>Phụ kiện và thiết bị đeo thông minh kết nối cuộc sống mỗi ngày.</p>
                    </div>
                </article>
            </div>
        </div>
    </section>
 
    {{-- FINAL CTA --}}
    <section class="about-final about-reveal">
        <div class="about-container">
            <h2>
                Tạo nên khoảnh khắc.
                <span>Chọn công nghệ phù hợp.</span>
            </h2>
 
            <p>
                Khám phá những sản phẩm mới nhất tại ElectronicShop
                và tìm thiết bị phù hợp với bạn.
            </p>
 
            <div class="about-actions">
                <a class="about-primary"
                   href="{{ Route::has('products.index') ? route('products.index') : '#' }}">
                    Khám phá sản phẩm
                    <i class="bi bi-arrow-right"></i>
                </a>
 
                <a class="about-secondary"
                   href="{{ Route::has('contact.index') ? route('contact.index') : '#' }}">
                    Liên hệ
                    <i class="bi bi-chat"></i>
                </a>
            </div>
        </div>
    </section>
 
</div>
@endsection
 
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const elements = document.querySelectorAll('.about-reveal');
 
    if (!('IntersectionObserver' in window)) {
        elements.forEach(el => el.classList.add('visible'));
        return;
    }
 
    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.08,
        rootMargin: '0px 0px -60px 0px'
    });
 
    elements.forEach(el => observer.observe(el));
});
</script>
@endpush
 