{{-- ============================================ --}}
{{-- FILE: resources/views/pages/news-detail.blade.php --}}
{{-- ============================================ --}}
@extends('layouts.app')
@section('title', ($news->title ?? 'Chi tiết tin tức') . ' - ElectronicShop')

@push('styles')
<style>
.news-detail-page { max-width: 1200px; margin: 0 auto; padding: 24px 16px; }
.news-detail-layout { display: grid; grid-template-columns: 1fr 300px; gap: 32px; align-items: start; }
.article-category { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #1565C0; background: #EBF3FF; display: inline-block; padding: 3px 10px; border-radius: 4px; margin-bottom: 12px; }
.article-title { font-size: 28px; font-weight: 800; line-height: 1.3; margin-bottom: 14px; }
.article-meta { display: flex; align-items: center; gap: 20px; font-size: 13px; color: #888; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #f0f0f0; }
.article-meta i { margin-right: 4px; color: #aaa; }
.article-cover { width: 100%; border-radius: 8px; overflow: hidden; background: #e8e8e8; display: flex; align-items: center; justify-content: center; min-height: 320px; color: #ccc; margin-bottom: 24px; }
.article-cover img { width: 100%; max-height: 420px; object-fit: cover; }
.article-body { font-size: 15px; line-height: 1.8; color: #333; }
.article-body h2 { font-size: 20px; font-weight: 700; margin: 28px 0 12px; }
.article-body h3 { font-size: 17px; font-weight: 700; margin: 22px 0 10px; }
.article-body p { margin-bottom: 16px; }
.article-body table { width: 100%; border-collapse: collapse; margin: 20px 0; }
.article-body table th, .article-body table td { padding: 10px 16px; border: 1px solid #e0e0e0; font-size: 14px; }
.article-body table th { background: #f5f5f5; font-weight: 700; }
.article-body .highlight { color: #1565C0; font-weight: 600; }
.compare-cards { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin: 20px 0; }
.compare-card { background: #f8f9fa; border: 1px solid #e0e0e0; border-radius: 8px; padding: 16px; font-size: 14px; }
.compare-card h4 { font-size: 15px; font-weight: 700; margin-bottom: 6px; }
.article-footer { border-top: 1px solid #f0f0f0; padding-top: 20px; margin-top: 32px; display: flex; align-items: center; justify-content: space-between; }
.feedback-btns { display: flex; gap: 12px; align-items: center; font-size: 14px; color: #555; }
.feedback-btn { display: flex; align-items: center; gap: 6px; padding: 8px 16px; border: 1px solid #e0e0e0; border-radius: 20px; cursor: pointer; background: #fff; font-size: 14px; transition: all .15s; }
.feedback-btn:hover { border-color: #1565C0; color: #1565C0; }
.share-btn { display: flex; align-items: center; gap: 8px; font-size: 14px; color: #555; }
/* SIDEBAR */
.news-sidebar { }
.sidebar-box { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 16px; margin-bottom: 16px; }
.sidebar-box h3 { font-size: 14px; font-weight: 700; margin-bottom: 14px; }
.related-post { display: flex; gap: 10px; align-items: flex-start; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #f5f5f5; }
.related-post:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
.related-post-img { width: 68px; height: 52px; border-radius: 6px; background: #f0f0f0; flex-shrink: 0; display: flex; align-items: center; justify-content: center; color: #ccc; overflow: hidden; }
.related-post-img img { width: 100%; height: 100%; object-fit: cover; }
.related-post-title { font-size: 13px; font-weight: 500; line-height: 1.4; color: #333; margin-bottom: 4px; }
.related-post-title:hover { color: #1565C0; }
.related-post-date { font-size: 11px; color: #aaa; }
.sidebar-newsletter input { width: 100%; border: 1px solid #e0e0e0; border-radius: 6px; padding: 9px 12px; font-size: 13px; margin-bottom: 8px; outline: none; }
.sidebar-newsletter input:focus { border-color: #1565C0; }
.sidebar-newsletter button { width: 100%; background: #1565C0; color: #fff; border: none; border-radius: 6px; padding: 10px; font-size: 13px; font-weight: 700; cursor: pointer; text-transform: uppercase; }
</style>
@endpush

@section('content')
<div class="news-detail-page">
    <div class="breadcrumb">
        <a href="{{ route('home') }}">Trang chủ</a>
        <span>›</span>
        <a href="{{ route('news.index') }}">Tin tức</a>
        <span>›</span>
        <span>{{ Str::limit($news->title ?? 'So sánh iPhone 15 và iPhone 14', 40) }}</span>
    </div>

    <div class="news-detail-layout">
        <article>
            <span class="article-category">{{ $news->category->name ?? 'CÔNG NGHỆ' }}</span>
            <h1 class="article-title">{{ $news->title ?? 'So sánh iPhone 15 và iPhone 14: Nâng cấp đáng giá?' }}</h1>
            <div class="article-meta">
                <span><i class="fas fa-user"></i> {{ $news->author ?? 'Phonge Team' }}</span>
                <span><i class="fas fa-calendar"></i> {{ optional($news->created_at)->format('d/m/Y') ?? '20/05/2024' }}</span>
                <span><i class="fas fa-eye"></i> {{ number_format($news->views ?? 12500) }} lượt xem</span>
                <span><i class="fas fa-clock"></i> 8 phút đọc</span>
            </div>

            <div class="article-cover">
                @if(isset($news->thumbnail))
                <img src="{{ $news->thumbnail }}" alt="{{ $news->title }}">
                @else
                <i class="fas fa-image fa-3x"></i>
                @endif
            </div>

            <div class="article-body">
                {!! $news->content ?? '
                <p>Apple vừa ra mắt iPhone 15 với nhiều nâng cấp đáng chú ý. Vậy so với iPhone 14, những thay đổi này có thực sự tạo nên sự khác biệt? Hãy cùng PHONE phân tích chi tiết để xem đâu là lựa chọn phù hợp nhất cho bạn.</p>

                <h2>1. Thiết kế và màu sắc</h2>
                <p>iPhone 15 tiếp tục giữ nguyên ngôn ngữ thiết kế quen thuộc nhưng có một số thay đổi tinh tế. Khung viền được bo cong nhẹ hơn, mặt lưng kính mờ sang trọng và cụm camera được cải tiến.</p>
                <div class="compare-cards">
                    <div class="compare-card"><h4>iPhone 15</h4>5 màu mới trẻ trung: Hồng, Vàng, Xanh lá, Xanh dương, Đen</div>
                    <div class="compare-card"><h4>iPhone 14</h4>5 màu: Tím, Xanh dương, Ánh sao, Đen, Đỏ</div>
                </div>

                <h2>2. Màn hình</h2>
                <p>Cả hai đều sở hữu màn hình Super Retina XDR OLED 6.1 inch với độ sáng cao và hiển thị sắc nét. Tuy nhiên, iPhone 15 đạt độ sáng tối đa lên đến 2000 nits, giúp hiển thị tốt hơn ngoài trời.</p>
                <table>
                    <tr><th>Thông số</th><th>iPhone 15</th><th>iPhone 14</th></tr>
                    <tr><td>Kích thước</td><td>6.1 inch</td><td>6.1 inch</td></tr>
                    <tr><td>Công nghệ</td><td>Super Retina XDR OLED</td><td>Super Retina XDR OLED</td></tr>
                    <tr><td>Độ sáng tối đa</td><td><span class="highlight">2000 nits</span></td><td>1200 nits</td></tr>
                    <tr><td>Tần số quét</td><td>60Hz</td><td>60Hz</td></tr>
                </table>

                <h2>3. Hiệu năng</h2>
                <p>iPhone 15 được trang bị chip A16 Bionic mạnh mẽ (tương tự iPhone 14 Pro), mang đến hiệu suất vượt trội và tối ưu khả năng tiết kiệm pin.</p>
                <p>✅ iPhone 15: Chip A16 Bionic, RAM 6GB<br>✅ iPhone 14: Chip A15 Bionic, RAM 6GB</p>
                ' !!}
            </div>

            <div class="article-footer">
                <div>
                    <span style="font-size:14px;color:#555;margin-right:10px">Bạn thấy bài viết này hữu ích?</span>
                    <span style="display:inline-flex;gap:10px">
                        <button class="feedback-btn"><i class="fas fa-thumbs-up"></i> {{ $news->likes ?? 128 }}</button>
                        <button class="feedback-btn"><i class="fas fa-thumbs-down"></i> {{ $news->dislikes ?? 12 }}</button>
                    </span>
                </div>
                <div class="share-btn">
                    Chia sẻ: <i class="fas fa-share-alt fa-lg" style="color:#1565C0;cursor:pointer"></i>
                </div>
            </div>
        </article>

        <div class="news-sidebar">
            <div class="sidebar-box">
                <h3>Bài viết liên quan</h3>
                @foreach($relatedNews ?? [
                    ['Đánh giá iPhone 15 Pro Max: Có xứng đáng với mức giá?','18/05/2024'],
                    ['iOS 17 có gì mới? Những tính năng nổi bật...','15/05/2024'],
                    ['Hướng dẫn chuyển dữ liệu sang iPhone mới...','14/05/2024'],
                ] as $r)
                <div class="related-post">
                    <div class="related-post-img"><i class="fas fa-image" style="font-size:12px"></i></div>
                    <div>
                        <a href="{{ isset($r->slug) ? route('news.show', $r->slug) : '#' }}" class="related-post-title">{{ is_array($r) ? $r[0] : $r->title }}</a>
                        <div class="related-post-date">{{ is_array($r) ? $r[1] : optional($r->created_at)->format('d/m/Y') }}</div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="sidebar-box sidebar-newsletter">
                <h3 style="color:#1565C0">Đăng ký nhận tin</h3>
                <p style="font-size:13px;color:#666;margin-bottom:10px">Nhận thông tin khuyến mãi mới nhất từ PHONE mỗi ngày.</p>
                <input type="email" placeholder="Nhập email của bạn">
                <button>ĐĂNG KÝ NGAY</button>
            </div>
        </div>
    </div>
</div>
@endsection