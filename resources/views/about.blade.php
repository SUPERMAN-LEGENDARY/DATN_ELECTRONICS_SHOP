@extends('layouts.app')
@section('title', 'ElectronicShop - Giới thiệu')
@php
    $showSearch = true;
@endphp

@push('styles')
<style>
/* ============================================================
   GENERAL
   ============================================================ */
body { background: #ffffff; color: #000000; }

.reveal {
    opacity: 0; transform: translateY(24px);
    transition: opacity .6s cubic-bezier(.16,1,.3,1), transform .6s cubic-bezier(.16,1,.3,1);
}
.reveal.revealed { opacity: 1; transform: translateY(0); }

/* ============================================================
   HERO — Giới thiệu ElectronicShop
   ============================================================ */
.about-hero {
    position: relative; overflow: hidden; color: #fff;
    background: url('{{ asset('images/about-hero-bg.jpg') }}') center/cover no-repeat;
    padding: 88px 0 96px; text-align: center;
}
.about-hero::before {
    content: '';
    position: absolute; inset: 0;
    background: rgba(0,0,0,.55);
    z-index: 1;
}
.about-hero .container { max-width: 900px; margin: 0 auto; padding: 0 24px; position: relative; z-index: 2; }
.about-hero .eyebrow {
    display: inline-block; background: rgba(255,255,255,.1); color: #fff;
    font-size: 13px; font-weight: 700; letter-spacing: .5px; text-transform: uppercase;
    padding: 6px 16px; border-radius: 999px; margin-bottom: 20px;
}
.about-hero h1 { font-size: 40px; font-weight: 800; margin-bottom: 16px; line-height: 1.25; }
.about-hero h1 span { color: #4dabf7; }
.about-hero p { font-size: 16.5px; color: #ccc; line-height: 1.6; max-width: 640px; margin: 0 auto; }
@media (max-width: 640px) { .about-hero h1 { font-size: 30px; } }

/* ============================================================
   STATS STRIP
   ============================================================ */
.about-stats {
    max-width: 1000px; margin: -48px auto 0; position: relative; z-index: 3;
    background: #fff; border-radius: 20px; padding: 32px 24px;
    box-shadow: 0 12px 40px rgba(0,0,0,.1);
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px;
    text-align: center;
}
.about-stats .stat b { display: block; font-size: 30px; font-weight: 800; color: #111; }
.about-stats .stat span { font-size: 13.5px; color: #777; font-weight: 500; }
@media (max-width: 768px) { .about-stats { grid-template-columns: repeat(2, 1fr); } }

/* ============================================================
   OUR STORY
   ============================================================ */
.about-story { padding: 96px 0 72px; }
.about-story .container {
    max-width: 1100px; margin: 0 auto; padding: 0 24px;
    display: grid; grid-template-columns: 1fr 1fr; gap: 56px; align-items: center;
}
@media (max-width: 860px) { .about-story .container { grid-template-columns: 1fr; gap: 32px; } }

.about-story .story-text .tag {
    display: inline-block; color: #0d6efd; font-size: 13px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .5px; margin-bottom: 12px;
}
.about-story h2 { font-size: 28px; font-weight: 800; color: #000; margin-bottom: 18px; line-height: 1.3; }
.about-story p { color: #555; font-size: 15.5px; line-height: 1.75; margin-bottom: 14px; }

.about-story .story-visual {
    background: linear-gradient(135deg, #eef3ff 0%, #dbe7ff 100%);
    border-radius: 24px; padding: 16px; text-align: center;
    display: flex; align-items: center; justify-content: center; min-height: 320px;
    overflow: hidden;
}
.about-story .story-visual img {
    width: 100%; height: 100%; max-height: 420px;
    object-fit: cover; border-radius: 16px;
    display: block;
}

/* ============================================================
   CORE VALUES
   ============================================================ */
.about-values { background: #f5f5f6; padding: 80px 0; }
.about-values .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; text-align: center; }
.about-values .section-tag {
    color: #0d6efd; font-size: 13px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .5px; margin-bottom: 10px; display: block;
}
.about-values h2 { font-size: 28px; font-weight: 800; color: #000; margin-bottom: 12px; }
.about-values .lead { color: #666; font-size: 15.5px; margin-bottom: 48px; }

.values-grid {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px;
}
@media (max-width: 900px) { .values-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 520px) { .values-grid { grid-template-columns: 1fr; } }

.value-card {
    background: #fff; border-radius: 18px; padding: 0 0 24px; text-align: left;
    box-shadow: 0 1px 3px rgba(0,0,0,.05);
    transition: transform .2s, box-shadow .2s;
    overflow: hidden;
}
.value-card:hover { transform: translateY(-4px); box-shadow: 0 10px 24px rgba(0,0,0,.08); }
.value-card .thumb {
    width: 100%; height: 140px; object-fit: cover; display: block;
    margin-bottom: 18px;
}
.value-card .icon {
    width: 48px; height: 48px; border-radius: 14px; background: #eaf2ff;
    color: #0d6efd; font-size: 22px; display: flex; align-items: center; justify-content: center;
    margin: 0 24px 18px;
}
.value-card b { display: block; font-size: 16px; font-weight: 700; color: #000; margin: 0 24px 8px; }
.value-card p { color: #777; font-size: 14px; line-height: 1.6; margin: 0 24px; }

/* ============================================================
   TIMELINE / MILESTONES
   ============================================================ */
.about-timeline { padding: 88px 0; }
.about-timeline .container { max-width: 900px; margin: 0 auto; padding: 0 24px; text-align: center; }
.about-timeline h2 { font-size: 28px; font-weight: 800; color: #000; margin-bottom: 48px; }

.timeline-list { position: relative; text-align: left; }
.timeline-list::before {
    content: ''; position: absolute; left: 19px; top: 6px; bottom: 6px;
    width: 2px; background: #e5e5e5;
}
.timeline-item { position: relative; padding-left: 56px; margin-bottom: 36px; }
.timeline-item:last-child { margin-bottom: 0; }
.timeline-item .dot {
    position: absolute; left: 0; top: 0; width: 40px; height: 40px; border-radius: 50%;
    background: #181818; color: #fff; font-size: 13px; font-weight: 700;
    display: flex; align-items: center; justify-content: center; z-index: 1;
}
.timeline-item b { display: block; font-size: 16px; font-weight: 700; color: #000; margin-bottom: 4px; }
.timeline-item p { color: #666; font-size: 14.5px; margin: 0 0 12px; line-height: 1.6; }
.timeline-item img {
    width: 100%; max-width: 360px; height: 160px; object-fit: cover;
    border-radius: 12px; display: block; margin-top: 4px;
}

/* ============================================================
   CTA
   ============================================================ */
.about-cta {
    max-width: 1000px; margin: 0 auto 96px; padding: 56px 40px;
    background: linear-gradient(135deg, #0d6efd 0%, #0850c4 100%);
    border-radius: 24px; text-align: center; color: #fff;
}
.about-cta h2 { font-size: 26px; font-weight: 800; margin-bottom: 12px; }
.about-cta p { color: #dce8ff; font-size: 15px; margin-bottom: 28px; }
.about-cta .btn-group { display: flex; justify-content: center; gap: 14px; flex-wrap: wrap; }
.about-cta a {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 13px 26px; border-radius: 12px; font-weight: 700; font-size: 14.5px;
    text-decoration: none; transition: opacity .2s;
}
.about-cta a:hover { opacity: .9; }
.about-cta .btn-primary { background: #fff; color: #0d6efd; }
.about-cta .btn-outline { background: transparent; color: #fff; border: 1.5px solid rgba(255,255,255,.6); }
</style>
@endpush

@section('content')

{{-- ===== HERO ===== --}}
<section class="about-hero reveal">
    <div class="container">
        <span class="eyebrow">Về chúng tôi</span>
        <h1>ElectronicShop — Nơi công nghệ <span>đến gần bạn hơn</span></h1>
        <p>Chúng tôi phân phối điện thoại, phụ kiện chính hãng cùng dịch vụ hậu mãi tận tâm, đồng hành cùng hàng trăm nghìn khách hàng trên khắp cả nước.</p>
    </div>
</section>

{{-- ===== STATS STRIP ===== --}}
<div class="about-stats reveal">
    <div class="stat">
        <b>{{ $yearsActive ?? '10+' }}</b>
        <span>Năm hoạt động</span>
    </div>
    <div class="stat">
        <b>{{ $storeCount ?? '50+' }}</b>
        <span>Cửa hàng toàn quốc</span>
    </div>
    <div class="stat">
        <b>{{ $customerCount ?? '500K+' }}</b>
        <span>Khách hàng tin dùng</span>
    </div>
    <div class="stat">
        <b>{{ $brandCount ?? '20+' }}</b>
        <span>Thương hiệu phân phối</span>
    </div>
</div>

{{-- ===== OUR STORY ===== --}}
<section class="about-story reveal">
    <div class="container">
        <div class="story-text">
            <span class="tag">Câu chuyện của chúng tôi</span>
            <h2>Bắt đầu từ niềm đam mê công nghệ</h2>
            <p>ElectronicShop được thành lập với mong muốn mang đến cho người dùng Việt Nam những sản phẩm điện thoại và thiết bị công nghệ chính hãng, chất lượng, với mức giá hợp lý nhất.</p>
            <p>Từ một cửa hàng nhỏ, chúng tôi đã phát triển thành hệ thống bán lẻ điện thoại uy tín với hàng chục chi nhánh, phục vụ hàng trăm nghìn khách hàng mỗi năm. Mỗi sản phẩm bán ra đều đi kèm chế độ bảo hành, đổi trả rõ ràng và đội ngũ tư vấn tận tâm.</p>
            <p>Chúng tôi tin rằng công nghệ nên dễ tiếp cận với tất cả mọi người — đó là lý do chúng tôi không ngừng cải thiện trải nghiệm mua sắm, từ cửa hàng vật lý đến nền tảng trực tuyến.</p>
        </div>
        <div class="story-visual">
            <img src="{{ asset('images/about-story.jpg') }}" alt="ElectronicShop - Câu chuyện thương hiệu">
        </div>
    </div>
</section>

{{-- ===== CORE VALUES ===== --}}
<section class="about-values reveal">
    <div class="container">
        <span class="section-tag">Giá trị cốt lõi</span>
        <h2>Vì sao khách hàng chọn ElectronicShop</h2>
        <p class="lead">Những cam kết chúng tôi luôn giữ vững trong suốt hành trình phục vụ khách hàng.</p>

        <div class="values-grid">
            <div class="value-card">
                <img class="thumb" src="{{ asset('images/value-chinh-hang.jpg') }}" alt="Hàng chính hãng 100%">
                <div class="icon"><i class="bi bi-patch-check-fill"></i></div>
                <b>Hàng chính hãng 100%</b>
                <p>Toàn bộ sản phẩm đều có nguồn gốc rõ ràng, đầy đủ hóa đơn và tem bảo hành từ nhà sản xuất.</p>
            </div>
            <div class="value-card">
                <img class="thumb" src="{{ asset('images/value-bao-hanh.jpg') }}" alt="Bảo hành minh bạch">
                <div class="icon"><i class="bi bi-shield-check"></i></div>
                <b>Bảo hành minh bạch</b>
                <p>Chính sách bảo hành, đổi trả rõ ràng, hỗ trợ nhanh chóng tại hơn 50 trung tâm trên toàn quốc.</p>
            </div>
            <div class="value-card">
                <img class="thumb" src="{{ asset('images/value-giao-hang.jpg') }}" alt="Giao hàng nhanh chóng">
                <div class="icon"><i class="bi bi-truck"></i></div>
                <b>Giao hàng nhanh chóng</b>
                <p>Giao hàng toàn quốc trong 24-48 giờ, hỗ trợ thanh toán linh hoạt và trả góp 0%.</p>
            </div>
            <div class="value-card">
                <img class="thumb" src="{{ asset('images/value-ho-tro.jpg') }}" alt="Hỗ trợ 24/7">
                <div class="icon"><i class="bi bi-headset"></i></div>
                <b>Hỗ trợ 24/7</b>
                <p>Đội ngũ tư vấn viên luôn sẵn sàng lắng nghe và giải đáp mọi thắc mắc của khách hàng.</p>
            </div>
        </div>
    </div>
</section>

{{-- ===== TIMELINE ===== --}}
<section class="about-timeline reveal">
    <div class="container">
        <h2>Hành trình phát triển</h2>

        <div class="timeline-list">
            <div class="timeline-item">
                <div class="dot">1</div>
                <b>2015 — Khởi đầu</b>
                <p>Cửa hàng ElectronicShop đầu tiên được mở tại Đà Nẵng, chuyên bán lẻ điện thoại chính hãng.</p>
                <img src="{{ asset('images/timeline-2015.jpg') }}" alt="Cửa hàng đầu tiên tại Đà Nẵng năm 2015">
            </div>
            <div class="timeline-item">
                <div class="dot">2</div>
                <b>2018 — Mở rộng hệ thống</b>
                <p>Phát triển lên hơn 20 chi nhánh tại các thành phố lớn, hợp tác phân phối chính thức với nhiều thương hiệu điện thoại hàng đầu.</p>
                <img src="{{ asset('images/timeline-2018.jpg') }}" alt="Mở rộng hệ thống chi nhánh năm 2018">
            </div>
            <div class="timeline-item">
                <div class="dot">3</div>
                <b>2021 — Chuyển đổi số</b>
                <p>Ra mắt nền tảng mua sắm trực tuyến, tích hợp dịch vụ sửa chữa và chăm sóc khách hàng đa kênh.</p>
                <img src="{{ asset('images/timeline-2021.jpg') }}" alt="Nền tảng mua sắm trực tuyến năm 2021">
            </div>
            <div class="timeline-item">
                <div class="dot">4</div>
                <b>{{ date('Y') }} — Hiện tại</b>
                <p>Hơn 50 cửa hàng trên toàn quốc, phục vụ hàng trăm nghìn khách hàng với cam kết chất lượng và dịch vụ hàng đầu.</p>
                <img src="{{ asset('images/timeline-hien-tai.jpg') }}" alt="ElectronicShop hiện tại">
            </div>
        </div>
    </div>
</section>

{{-- ===== CTA ===== --}}
<section class="about-cta reveal">
    <h2>Sẵn sàng nâng cấp thiết bị của bạn?</h2>
    <p>Khám phá hàng nghìn sản phẩm chính hãng hoặc liên hệ với đội ngũ tư vấn của chúng tôi ngay hôm nay.</p>
    <div class="btn-group">
        <a href="{{ Route::has('products.index') ? route('products.index') : '#' }}" class="btn-primary">
            <i class="bi bi-bag"></i> Xem sản phẩm
        </a>
        <a href="{{ Route::has('contact.index') ? route('contact.index') : '#' }}" class="btn-outline">
            <i class="bi bi-chat-dots"></i> Liên hệ ngay
        </a>
    </div>
</section>

@endsection

@push('scripts')
<script>
(function () {
    const io = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.classList.add('revealed');
                io.unobserve(e.target);
            }
        });
    }, { threshold: 0.07, rootMargin: '0px 0px -30px 0px' });

    document.querySelectorAll('.reveal').forEach(el => io.observe(el));
})();
</script>
@endpush