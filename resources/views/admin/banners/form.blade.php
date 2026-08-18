@extends('layouts.admin')

@section('title', 'Tạo Banner Mới')

@push('styles')
<style>
    * { box-sizing: border-box; }

    .breadcrumb {
        font-size: 13px;
        color: #888;
        margin-bottom: 4px;
    }

    .breadcrumb a {
        color: #1565C0;
        text-decoration: none;
    }

    .page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 22px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .page-title {
        font-size: 22px;
        font-weight: 800;
        color: #111;
        margin-bottom: 4px;
    }

    .page-actions {
        display: flex;
        gap: 10px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 10px 18px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        text-decoration: none;
        white-space: nowrap;
    }

    .btn-primary {
        background: #1565C0;
        color: #fff;
    }

    .btn-primary:hover { background: #0D47A1; }

    .btn-outline {
        background: #fff;
        border: 1px solid #ddd;
        color: #444;
    }

    .btn-outline:hover { background: #f5f5f5; }

    /* ===== Layout chính: form trái - preview phải ===== */
    .builder-wrap {
        display: grid;
        grid-template-columns: minmax(0, 420px) 1fr;
        gap: 22px;
        align-items: flex-start;
    }

    @media (max-width: 1100px) {
        .builder-wrap { grid-template-columns: 1fr; }
    }

    .form-card {
        background: #fff;
        border-radius: 12px;
        padding: 22px 24px;
        box-shadow: 0 1px 4px rgba(0,0,0,.06);
    }

    .step-section {
        margin-bottom: 24px;
    }

    .step-label {
        font-size: 12.5px;
        font-weight: 800;
        color: #333;
        text-transform: uppercase;
        letter-spacing: .4px;
        margin-bottom: 12px;
    }

    label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #444;
        margin-bottom: 6px;
    }

    .form-group { margin-bottom: 16px; }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .form-control {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid #ddd;
        border-radius: 7px;
        font-size: 14px;
        outline: none;
        font-family: inherit;
    }

    .form-control:focus { border-color: #1565C0; }

    textarea.form-control {
        resize: vertical;
        min-height: 62px;
    }

    .char-count {
        float: right;
        font-size: 11.5px;
        font-weight: 500;
        color: #999;
    }

    .invalid-feedback {
        color: #C62828;
        font-size: 12px;
        margin-top: 4px;
    }

    .is-invalid { border-color: #C62828 !important; }

    /* Chọn cách tạo banner (3 lựa chọn) */
    .method-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }

    .method-option {
        border: 2px solid #e6e6e6;
        border-radius: 10px;
        padding: 14px 10px;
        cursor: pointer;
        text-align: center;
        transition: .15s;
    }

    .method-option:hover { border-color: #90caf9; }

    .method-option.active {
        border-color: #1565C0;
        background: #F2F8FF;
    }

    .method-option input { display: none; }

    .method-option i {
        font-size: 19px;
        color: #1565C0;
        margin-bottom: 6px;
        display: block;
    }

    .method-option .opt-title {
        font-weight: 700;
        font-size: 12.5px;
    }

    .method-option .opt-desc {
        font-size: 11px;
        color: #888;
        margin-top: 2px;
    }

    /* Template gallery */
    .template-nav {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .template-scroll {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        flex: 1;
    }

    .tpl-arrow {
        border: 1px solid #ddd;
        background: #fff;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #666;
    }

    .tpl-arrow:hover { background: #f5f5f5; }

    .tpl-page { display: none; }
    .tpl-page.active { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }

    .tpl-card {
        border: 2px solid #e6e6e6;
        border-radius: 10px;
        overflow: hidden;
        cursor: pointer;
        position: relative;
        transition: .15s;
        background: #fff;
    }

    .tpl-card:hover { border-color: #90caf9; }

    .tpl-card.active { border-color: #1565C0; }

    .tpl-card input { display: none; }

    .tpl-thumb {
        height: 62px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        text-align: center;
        padding: 6px;
        line-height: 1.3;
    }

    .tpl-name {
        font-size: 11.5px;
        font-weight: 600;
        text-align: center;
        padding: 6px 4px;
        color: #333;
        background: #fff;
    }

    .tpl-check {
        position: absolute;
        top: 6px;
        left: 6px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #1565C0;
        color: #fff;
        display: none;
        align-items: center;
        justify-content: center;
        font-size: 10px;
    }

    .tpl-card.active .tpl-check { display: flex; }

    .tpl-samsung { background: linear-gradient(135deg,#0b1120,#1b2a4a); }
    .tpl-apple { background: linear-gradient(135deg,#f4f4f4,#e8e8e8); color:#333 !important; }
    .tpl-xiaomi { background: linear-gradient(135deg,#ff7a1a,#ff5a00); }
    .tpl-gaming { background: linear-gradient(135deg,#1a0b2e,#3a0ca3); }
    .tpl-flash { background: linear-gradient(135deg,#8e0000,#c0392b); }
    .tpl-lifestyle { background: linear-gradient(135deg,#d8cfc0,#bfae94); color:#333 !important; }

    .info-box {
        background: #F2F8FF;
        border: 1px solid #D6E9FF;
        color: #1565C0;
        font-size: 12.5px;
        padding: 10px 14px;
        border-radius: 8px;
        display: flex;
        gap: 8px;
        align-items: flex-start;
        margin-top: 14px;
    }

    .info-box.muted {
        background: #F7F8FA;
        border-color: #ECEDEF;
        color: #777;
    }

    .info-box i { margin-top: 1px; }

    .section-divider {
        margin: 22px 0 16px;
        border-top: 1px solid #eee;
        padding-top: 16px;
    }

    .toggle-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .toggle-input {
        position: relative;
        display: inline-block;
        width: 42px;
        height: 24px;
        flex-shrink: 0;
    }

    .toggle-input input { opacity: 0; width: 0; height: 0; }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background: #ccc;
        border-radius: 24px;
        transition: .2s;
    }

    .toggle-slider:before {
        content: "";
        position: absolute;
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background: #fff;
        border-radius: 50%;
        transition: .2s;
    }

    .toggle-input input:checked + .toggle-slider { background: #2E7D32; }
    .toggle-input input:checked + .toggle-slider:before { transform: translateX(18px); }

    .schedule-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 14px;
    }

    .schedule-row .form-group { min-width: 0; }

    .schedule-row input.form-control { width: 100%; min-width: 0; }

    .form-actions-mobile {
        display: none;
        margin-top: 22px;
        gap: 10px;
    }

    @media (max-width: 1100px) {
        .form-actions-mobile { display: flex; }
    }

    /* ===== Panel xem trước ===== */
    .preview-card {
        background: #fff;
        border-radius: 12px;
        padding: 22px 24px;
        box-shadow: 0 1px 4px rgba(0,0,0,.06);
        position: sticky;
        top: 20px;
    }

    .preview-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 18px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .preview-title {
        font-size: 15px;
        font-weight: 800;
        margin-bottom: 3px;
    }

    .preview-subtitle {
        font-size: 12.5px;
        color: #888;
    }

    .device-toggle {
        display: flex;
        gap: 6px;
        background: #F3F4F6;
        padding: 4px;
        border-radius: 9px;
    }

    .device-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        border-radius: 7px;
        font-size: 13px;
        font-weight: 600;
        color: #555;
        cursor: pointer;
        border: none;
        background: transparent;
    }

    .device-btn.active {
        background: #1565C0;
        color: #fff;
    }

    /* Banner preview desktop */
    .banner-preview {
        border-radius: 12px;
        overflow: hidden;
        position: relative;
        min-height: 260px;
        background: linear-gradient(120deg,#0a0f1e 0%,#101a33 55%,#1c2b52 100%);
        display: flex;
        align-items: center;
        padding: 40px 48px;
        color: #fff;
    }

    .banner-preview.tpl-apple-bg { background: linear-gradient(120deg,#f7f7f7,#e9e9e9); color:#222; }
    .banner-preview.tpl-xiaomi-bg { background: linear-gradient(120deg,#ff8a2b,#ff5a00); color:#fff; }
    .banner-preview.tpl-gaming-bg { background: linear-gradient(120deg,#150826,#3a0ca3); color:#fff; }
    .banner-preview.tpl-flash-bg { background: linear-gradient(120deg,#7a0000,#c0392b); color:#fff; }
    .banner-preview.tpl-lifestyle-bg { background: linear-gradient(120deg,#e7ddc9,#c9b697); color:#3a2f22; }

    .banner-text { max-width: 60%; position: relative; z-index: 2; }

    .banner-badge {
        display: inline-block;
        background: #1565C0;
        color: #fff;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .5px;
        padding: 5px 12px;
        border-radius: 20px;
        margin-bottom: 14px;
    }

    .banner-title-text {
        font-size: 30px;
        font-weight: 800;
        line-height: 1.2;
        margin-bottom: 10px;
    }

    .banner-desc-text {
        font-size: 14px;
        opacity: .85;
        line-height: 1.5;
        margin-bottom: 18px;
        white-space: pre-line;
    }

    .banner-price {
        display: none;
        align-items: baseline;
        gap: 10px;
        margin-bottom: 16px;
        font-size: 22px;
        font-weight: 800;
    }

    .banner-price .old-price {
        font-size: 14px;
        font-weight: 500;
        opacity: .6;
        text-decoration: line-through;
    }

    .banner-preview.mini .banner-price { font-size: 16px; margin-bottom: 10px; }
    .banner-preview.mini .banner-price .old-price { font-size: 11.5px; }

    .banner-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #fff;
        color: #0a0f1e;
        font-weight: 700;
        font-size: 13.5px;
        padding: 10px 20px;
        border-radius: 24px;
        width: fit-content;
    }

    .banner-visual {
        position: absolute;
        right: 30px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 90px;
        opacity: .18;
        z-index: 1;
    }

    /* ===== Upload banner: chọn Ảnh / Video ===== */
    .method-toggle-mini {
        display: inline-flex;
        border: 1px solid #ddd;
        border-radius: 7px;
        overflow: hidden;
        margin-bottom: 10px;
    }

    .mt-btn {
        border: none;
        background: #fff;
        padding: 7px 16px;
        font-size: 12.5px;
        font-weight: 600;
        color: #666;
        cursor: pointer;
    }

    .mt-btn + .mt-btn { border-left: 1px solid #ddd; }

    .mt-btn:hover { background: #f5f5f5; }

    .mt-btn.active { background: #1565C0; color: #fff; }

    .banner-video-bg {
        display: none;
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: 0;
    }

    .banner-preview.has-video-bg { background: #000; }
    .banner-preview.has-video-bg .banner-video-bg { display: block; }
    .banner-preview.has-video-bg .banner-visual { display: none; }
    .banner-preview.has-video-bg::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, rgba(0,0,0,.65) 0%, rgba(0,0,0,.15) 60%, rgba(0,0,0,0) 100%);
        z-index: 0;
    }

    .device-frames {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
        margin-top: 20px;
    }

    @media (max-width: 700px) {
        .device-frames { grid-template-columns: 1fr; }
    }

    .device-frame-title {
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 8px;
        color: #333;
    }

    .device-frame-title span {
        font-weight: 500;
        color: #999;
        font-size: 12px;
    }

    .banner-preview.mini {
        min-height: 200px;
        padding: 26px 22px;
        border-radius: 10px;
    }

    .banner-preview.mini .banner-title-text { font-size: 20px; }
    .banner-preview.mini .banner-desc-text { font-size: 12.5px; margin-bottom: 12px; }
    .banner-preview.mini .banner-btn { font-size: 12px; padding: 8px 16px; }
    .banner-preview.mini .banner-visual { font-size: 60px; right: 14px; }

    .banner-preview.phone {
        min-height: 340px;
        padding: 24px 18px;
        align-items: flex-start;
        flex-direction: column;
        justify-content: center;
    }

    .banner-preview.phone .banner-text { max-width: 100%; }
    .banner-preview.phone .banner-visual {
        position: static;
        transform: none;
        display: block;
        margin: 16px 0 0 auto;
        opacity: .2;
    }

    .single-device-wrap { display: none; }
    .single-device-wrap.active { display: block; }

    .note-box {
        margin-top: 18px;
        background: #F2F8FF;
        border: 1px solid #D6E9FF;
        color: #1565C0;
        font-size: 12.5px;
        padding: 12px 16px;
        border-radius: 8px;
        display: flex;
        gap: 8px;
    }

    /* ===== Upload ảnh thủ công (Banner thủ công) ===== */
    .sub-heading {
        font-size: 12.5px;
        font-weight: 700;
        color: #666;
        letter-spacing: .3px;
        margin: 0 0 10px;
    }

    .image-upload-box {
        border: 2px dashed #ddd;
        border-radius: 10px;
        height: 110px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        overflow: hidden;
        position: relative;
        background: #fafafa;
    }

    .image-upload-box:hover { border-color: #90caf9; }

    .image-upload-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .image-upload-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        color: #1565C0;
        font-size: 12.5px;
        font-weight: 600;
    }

    .image-upload-placeholder i { font-size: 18px; }

    .hint-text {
        font-size: 11px;
        color: #999;
        margin-top: 6px;
    }

    .color-field {
        display: flex;
        align-items: center;
        gap: 8px;
        border: 1px solid #ddd;
        border-radius: 7px;
        padding: 6px 10px;
    }

    .color-field input[type=color] {
        width: 30px;
        height: 30px;
        padding: 0;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }

    .color-field input[type=text] {
        border: none;
        outline: none;
        font-size: 13px;
        width: 100%;
        color: #333;
    }

    .fx-checks {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        margin-top: 6px;
    }

    .fx-checks label {
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 500;
        font-size: 13px;
        margin: 0;
        cursor: pointer;
    }

    .fx-checks input { width: auto; margin: 0; }

    /* ===== Upload mẫu riêng (Banner từ mẫu) ===== */
    .custom-tpl-upload-inner {
        border: 2px dashed #ddd;
        border-radius: 10px;
        padding: 14px;
        text-align: center;
        color: #1565C0;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        gap: 6px;
        align-items: center;
    }

    .custom-tpl-upload-inner:hover { border-color: #90caf9; background: #F8FBFF; }

    /* ===== Căn chỉnh văn bản ===== */
    .align-group {
        display: inline-flex;
        border: 1px solid #ddd;
        border-radius: 7px;
        overflow: hidden;
    }

    .align-btn {
        border: none;
        background: #fff;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #666;
    }

    .align-btn + .align-btn { border-left: 1px solid #ddd; }

    .align-btn:hover { background: #f5f5f5; }

    .align-btn.active { background: #1565C0; color: #fff; }

    /* Lớp phủ gradient khi bật hiệu ứng ở banner thủ công */
    .banner-preview.has-gradient-overlay::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,.55) 100%);
        z-index: 1;
    }
</style>
@endpush

@section('content')

<div class="breadcrumb">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> &rsaquo;
    <a href="{{ route('admin.banners.index') }}">Banner</a> &rsaquo; {{ $banner->exists ? 'Cập nhật' : 'Tạo mới' }}
</div>

<div class="page-header">
    <div>
        <div class="page-title">{{ $banner->exists ? 'Cập Nhật Banner' : 'Tạo Banner Mới' }}</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.banners.index') }}" class="btn btn-outline">
            <i class="fas fa-eye"></i> Xem danh sách
        </a>
        <button type="submit" form="bannerForm" class="btn btn-primary">
            <i class="fas fa-save"></i> Lưu Banner
        </button>
    </div>
</div>

<form method="POST" action="{{ $banner->exists ? route('admin.banners.update', $banner) : route('admin.banners.store') }}" enctype="multipart/form-data" id="bannerForm">
    @csrf
    @if($banner->exists)
        @method('PUT')
    @endif

    <div class="builder-wrap">
        {{-- ============ CỘT TRÁI: FORM ============ --}}
        <div class="form-card">

            {{-- 1. CHỌN CÁCH TẠO BANNER --}}
            <div class="step-section">
                <div class="step-label">1. Chọn cách tạo banner</div>
                <div class="method-grid">
                    <label class="method-option active" data-method-option="template">
                        <input type="radio" name="creation_method" value="template" checked>
                        <i class="fas fa-layer-group"></i>
                        <div class="opt-title">Banner từ mẫu</div>
                        <div class="opt-desc">Chọn mẫu có sẵn</div>
                    </label>
                    <label class="method-option" data-method-option="custom">
                        <input type="radio" name="creation_method" value="custom">
                        <i class="fas fa-pencil-ruler"></i>
                        <div class="opt-title">Banner thủ công</div>
                        <div class="opt-desc">Tự thiết kế</div>
                    </label>
                    <label class="method-option" data-method-option="upload">
                        <input type="radio" name="creation_method" value="upload">
                        <i class="fas fa-upload"></i>
                        <div class="opt-title">Upload banner</div>
                        <div class="opt-desc">Upload ảnh có sẵn</div>
                    </label>
                </div>
            </div>

            {{-- 2. CHỌN LOẠI BANNER --}}
            <div class="step-section">
                <div class="step-label">2. Chọn loại banner</div>
                <div class="form-group" style="margin-bottom:0">
                    <select name="banner_type" id="f_banner_type" class="form-control">
                        <option value="hero">👑 Hero Banner</option>
                        <option value="promo">🔥 Promo Banner</option>
                        <option value="category">🏷️ Category Banner</option>
                        <option value="sidebar">📌 Sidebar Banner</option>
                    </select>
                </div>
            </div>

            {{-- 3. CHỌN MẪU (TEMPLATE) --}}
            <div class="step-section" id="templateSection">
                <div class="step-label">3. Chọn mẫu (Template)</div>
                <div class="template-nav">
                    <div class="tpl-arrow" id="tplPrev"><i class="fas fa-chevron-left"></i></div>
                    <div style="flex:1;overflow:hidden">
                        <div class="tpl-page active" data-page="0">
                            <label class="tpl-card active" data-template="samsung_dark">
                                <input type="radio" name="template" value="samsung_dark" checked>
                                <span class="tpl-check"><i class="fas fa-check"></i></span>
                                <div class="tpl-thumb tpl-samsung">Galaxy S24 Ultra</div>
                                <div class="tpl-name">Samsung Dark</div>
                            </label>
                            <label class="tpl-card" data-template="apple_minimal">
                                <input type="radio" name="template" value="apple_minimal">
                                <span class="tpl-check"><i class="fas fa-check"></i></span>
                                <div class="tpl-thumb tpl-apple">iPhone 15 Pro</div>
                                <div class="tpl-name">Apple Minimal</div>
                            </label>
                            <label class="tpl-card" data-template="xiaomi_orange">
                                <input type="radio" name="template" value="xiaomi_orange">
                                <span class="tpl-check"><i class="fas fa-check"></i></span>
                                <div class="tpl-thumb tpl-xiaomi">Camera đỉnh cao</div>
                                <div class="tpl-name">Xiaomi Orange</div>
                            </label>
                        </div>
                        <div class="tpl-page" data-page="1">
                            <label class="tpl-card" data-template="gaming_rgb">
                                <input type="radio" name="template" value="gaming_rgb">
                                <span class="tpl-check"><i class="fas fa-check"></i></span>
                                <div class="tpl-thumb tpl-gaming">Gaming Monster</div>
                                <div class="tpl-name">Gaming RGB</div>
                            </label>
                            <label class="tpl-card" data-template="flash_sale_red">
                                <input type="radio" name="template" value="flash_sale_red">
                                <span class="tpl-check"><i class="fas fa-check"></i></span>
                                <div class="tpl-thumb tpl-flash">FLASH SALE<br>Giảm đến 70%</div>
                                <div class="tpl-name">Flash Sale Red</div>
                            </label>
                            <label class="tpl-card" data-template="lifestyle_clean">
                                <input type="radio" name="template" value="lifestyle_clean">
                                <span class="tpl-check"><i class="fas fa-check"></i></span>
                                <div class="tpl-thumb tpl-lifestyle">Lifestyle Clean</div>
                                <div class="tpl-name">Lifestyle Clean</div>
                            </label>
                        </div>
                    </div>
                    <div class="tpl-arrow" id="tplNext"><i class="fas fa-chevron-right"></i></div>
                </div>

                <div class="form-group" style="margin-top:14px;margin-bottom:0">
                    <label>Hoặc tải ảnh mẫu của riêng bạn</label>
                    <input type="file" name="template_image" id="f_template_image" accept="image/*" style="display:none">
                    <div class="custom-tpl-upload-inner" id="customTplUploadInner">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>Chọn ảnh nền để dùng làm mẫu riêng (thay cho mẫu có sẵn)</span>
                    </div>
                </div>

                <div class="info-box">
                    <i class="fas fa-circle-info"></i>
                    <span>Khi chọn mẫu có sẵn, bố cục, màu sắc và kiểu chữ sẽ được áp dụng tự động. Bạn chỉ cần nhập nội dung.</span>
                </div>
            </div>

            {{-- 3b. HÌNH ẢNH & GIAO DIỆN (BANNER THỦ CÔNG) --}}
            <div class="step-section" id="customSection" style="display:none">
                <div class="step-label">3. Hình ảnh &amp; Giao diện (thủ công)</div>

                <div class="sub-heading">HÌNH ẢNH</div>
                <div class="form-row" style="margin-bottom:18px">
                    <div class="form-group" style="margin-bottom:0">
                        <label>Ảnh Desktop *</label>
                        <div class="image-upload-box" id="box_custom_desktop">
                            <img id="preview_custom_desktop" style="display:none">
                            <div class="image-upload-placeholder" id="placeholder_custom_desktop">
                                <i class="fas fa-image"></i>
                                <span>Thay đổi</span>
                            </div>
                        </div>
                        <input type="file" name="custom_image_desktop" id="f_custom_image_desktop" accept="image/*" style="display:none">
                        <div class="hint-text">Kích thước khuyến nghị: 1920x800px</div>
                        @error('custom_image_desktop')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label>Ảnh Mobile (tùy chọn)</label>
                        <div class="image-upload-box" id="box_custom_mobile">
                            <img id="preview_custom_mobile" style="display:none">
                            <div class="image-upload-placeholder" id="placeholder_custom_mobile">
                                <i class="fas fa-image"></i>
                                <span>Thay đổi</span>
                            </div>
                        </div>
                        <input type="file" name="custom_image_mobile" id="f_custom_image_mobile" accept="image/*" style="display:none">
                        <div class="hint-text">Kích thước khuyến nghị: 750x1334px</div>
                    </div>
                </div>

                <div class="sub-heading">GIAO DIỆN</div>
                <div class="form-group">
                    <label>Layout</label>
                    <select name="custom_layout" id="f_custom_layout" class="form-control">
                        <option value="text_left">Text trái - Ảnh phải</option>
                        <option value="text_right">Text phải - Ảnh trái</option>
                        <option value="text_center">Text giữa - Ảnh nền</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Màu nền</label>
                        <div class="color-field">
                            <input type="color" id="f_custom_bg" value="#0B1020">
                            <input type="text" name="custom_bg_color" id="f_custom_bg_text" value="#0B1020">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Màu chữ</label>
                        <div class="color-field">
                            <input type="color" id="f_custom_text" value="#FFFFFF">
                            <input type="text" name="custom_text_color" id="f_custom_text_text" value="#FFFFFF">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Màu nút</label>
                    <div class="color-field" style="max-width:220px">
                        <input type="color" id="f_custom_btn" value="#2563EB">
                        <input type="text" name="custom_btn_color" id="f_custom_btn_text" value="#2563EB">
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:0">
                    <label>Hiệu ứng</label>
                    <div class="fx-checks">
                        <label><input type="checkbox" name="fx_shadow" id="f_fx_shadow" value="1" {{ old('fx_shadow', $banner->fx_shadow ?? true) ? 'checked' : '' }}> Đổ bóng</label>
                        <label><input type="checkbox" name="fx_gradient" id="f_fx_gradient" value="1" {{ old('fx_gradient', $banner->fx_gradient ?? true) ? 'checked' : '' }}> Lớp phủ gradient</label>
                        <label><input type="checkbox" name="fx_radius" id="f_fx_radius" value="1" {{ old('fx_radius', $banner->fx_radius ?? false) ? 'checked' : '' }}> Bo góc</label>
                    </div>
                </div>
            </div>

            {{-- 4. NỘI DUNG BANNER --}}
            <div class="step-section">
                <div class="step-label">4. Nội dung banner</div>

                <div class="form-group" style="display:flex;align-items:center;justify-content:space-between;gap:10px">
                    <label style="margin-bottom:0">Căn chỉnh văn bản</label>
                    <div class="align-group">
                        <button type="button" class="align-btn active" data-align="left" title="Căn trái"><i class="fas fa-align-left"></i></button>
                        <button type="button" class="align-btn" data-align="center" title="Căn giữa"><i class="fas fa-align-center"></i></button>
                        <button type="button" class="align-btn" data-align="right" title="Căn phải"><i class="fas fa-align-right"></i></button>
                    </div>
                    <input type="hidden" name="text_align" id="f_text_align" value="left">
                </div>

                <div class="form-group">
                    <label>Nhãn nhỏ (Badge) <span class="char-count" id="cnt_badge">0/20</span></label>
                    <input type="text" name="badge" id="f_badge" maxlength="20" value="{{ old('badge', $banner->label) }}"
                        class="form-control @error('badge') is-invalid @enderror" placeholder="VD: NEW">
                    @error('badge')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label>Tiêu đề chính <span class="char-count" id="cnt_title">0/50</span></label>
                    <input type="text" name="title" id="f_title" maxlength="50" value="{{ old('title', $banner->title) }}"
                        class="form-control @error('title') is-invalid @enderror" placeholder="VD: Galaxy S24 Ultra">
                    @error('title')<div class="invalid-feedback" id="f_title-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label>Mô tả <span class="char-count" id="cnt_desc">0/150</span></label>
                    <textarea name="description" id="f_desc" rows="2" maxlength="150"
                        class="form-control @error('description') is-invalid @enderror"
                        placeholder="VD: AI Camera. Chạm đến tương lai.&#10;Hiệu năng vượt trội.">{{ old('description', $banner->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-row-2" id="group_price">
                    <div class="form-group">
                        <label>Giá bán (VNĐ)</label>
                        <input type="number" name="price" id="f_price" min="0" step="1000" value="{{ old('price', $banner->price) }}"
                            class="form-control @error('price') is-invalid @enderror" placeholder="VD: 25990000">
                        @error('price')<div class="invalid-feedback" id="f_price-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>Giá gốc (VNĐ)</label>
                        <input type="number" name="compare_price" id="f_compare_price" min="0" step="1000" value="{{ old('compare_price', $banner->compare_price) }}"
                            class="form-control @error('compare_price') is-invalid @enderror" placeholder="VD: 29990000">
                        @error('compare_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-row-2">
                    <div class="form-group">
                        <label>Nút bấm (Text)</label>
                        <input type="text" name="button_text" id="f_btntext" maxlength="20" value="{{ old('button_text', $banner->button_text ?? 'Mua ngay') }}"
                            class="form-control @error('button_text') is-invalid @enderror" placeholder="VD: Mua ngay">
                        @error('button_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>Đường dẫn Nút</label>
                        <input type="text" name="button_link" id="f_link" value="{{ old('button_link', $banner->button_link) }}"
                            class="form-control @error('button_link') is-invalid @enderror" placeholder="/products/...">
                        @error('button_link')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:0" id="mediaUploadGroup">
                    <label>Ảnh / Video sản phẩm banner</label>

                    <div class="method-toggle-mini" id="mediaTypeToggle" style="display:none">
                        <button type="button" class="mt-btn active" data-media="image">Ảnh</button>
                        <button type="button" class="mt-btn" data-media="video">Video</button>
                    </div>
                    <input type="hidden" name="media_type" id="f_media_type" value="image">

                    <input type="file" name="image" id="f_image" accept="image/*"
                        class="form-control @error('image') is-invalid @enderror">
                    @error('image')<div class="invalid-feedback" id="f_image-error">{{ $message }}</div>@enderror

                    <input type="file" name="video" id="f_video" accept="video/mp4,.mp4" style="display:none"
                        class="form-control @error('video') is-invalid @enderror">
                    @error('video')<div class="invalid-feedback" id="f_video-error">{{ $message }}</div>@enderror

                    <div class="info-box muted" id="videoSpecBox" style="display:none;margin-top:10px">
                        <i class="fas fa-circle-info"></i>
                        <span>
                            Khuyến nghị banner video full màn hình cao cấp (web bán điện thoại):
                            độ phân giải <strong>1920×600</strong> hoặc <strong>1920×700px</strong>,
                            định dạng <strong>MP4 (H.264)</strong>, <strong>24–30 fps</strong>,
                            bitrate <strong>1–3 Mbps</strong>, không cần âm thanh.
                            Dung lượng tối đa <strong>10MB</strong>/video.
                        </span>
                    </div>
                </div>
            </div>

            {{-- 5. THỜI GIAN HIỂN THỊ --}}
            <div class="step-section" style="margin-bottom: 0">
                <div class="step-label">5. Thời gian hiển thị</div>

                <div class="schedule-row">
                    <div class="form-group" style="margin-bottom:0">
                        <label>Bắt đầu từ</label>
                        <input type="datetime-local" name="start_at" value="{{ old('start_at', $banner->start_at?->format('Y-m-d\TH:i')) }}" class="form-control @error('start_at') is-invalid @enderror">
                        @error('start_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label>Hiển thị đến</label>
                        <input type="datetime-local" name="end_at" value="{{ old('end_at', $banner->end_at?->format('Y-m-d\TH:i')) }}" class="form-control @error('end_at') is-invalid @enderror">
                        @error('end_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-group" style="margin-top:16px;margin-bottom:0">
                    <label>Thứ tự hiển thị</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $banner->sort_order ?? 0) }}" class="form-control @error('sort_order') is-invalid @enderror" min="0">
                    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group" style="margin-top:16px;margin-bottom:0">
                    <div class="toggle-wrap">
                        <label class="toggle-input">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $banner->exists ? $banner->is_active : true) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                        <span style="font-size:13.5px;font-weight:600;color:#333">Kích hoạt</span>
                    </div>
                </div>
            </div>

            <div class="form-actions-mobile">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu Banner</button>
                <a href="{{ route('admin.banners.index') }}" class="btn btn-outline">Hủy</a>
            </div>
        </div>

        {{-- ============ CỘT PHẢI: XEM TRƯỚC ============ --}}
        <div class="preview-card">
            <div class="preview-header">
                <div>
                    <div class="preview-title">Xem trước banner</div>
                    <div class="preview-subtitle">Banner sẽ hiển thị như thế này trên các thiết bị</div>
                </div>
                <div class="device-toggle">
                    <button type="button" class="device-btn active" data-device="desktop"><i class="fas fa-desktop"></i> Desktop</button>
                    <button type="button" class="device-btn" data-device="tablet"><i class="fas fa-tablet-alt"></i> Tablet</button>
                    <button type="button" class="device-btn" data-device="mobile"><i class="fas fa-mobile-alt"></i> Mobile</button>
                </div>
            </div>

            <div class="single-device-wrap active" id="deviceDesktop">
                <div class="banner-preview" id="previewDesktop">
                    <video class="banner-video-bg" id="pv_video_d" muted loop autoplay playsinline></video>
                    <div class="banner-text">
                        <span class="banner-badge" id="pv_badge_d">NEW</span>
                        <div class="banner-title-text" id="pv_title_d">Galaxy S24 Ultra</div>
                        <div class="banner-desc-text" id="pv_desc_d">AI Camera. Chạm đến tương lai.
Hiệu năng vượt trội.</div>
                        <div class="banner-price" id="pv_price_d"></div>
                        <div class="banner-btn" id="pv_btn_d">Mua ngay <i class="fas fa-arrow-right"></i></div>
                    </div>
                    <i class="fas fa-mobile-alt banner-visual"></i>
                </div>
            </div>

            <div class="single-device-wrap" id="deviceTabletMobile">
                <div class="device-frames">
                    <div>
                        <div class="device-frame-title">Tablet <span>(1024x768)</span></div>
                        <div class="banner-preview mini" id="previewTablet">
                            <video class="banner-video-bg" id="pv_video_t" muted loop autoplay playsinline></video>
                            <div class="banner-text">
                                <span class="banner-badge" id="pv_badge_t">NEW</span>
                                <div class="banner-title-text" id="pv_title_t">Galaxy S24 Ultra</div>
                                <div class="banner-desc-text" id="pv_desc_t">AI Camera. Chạm đến tương lai.
Hiệu năng vượt trội.</div>
                                <div class="banner-price" id="pv_price_t"></div>
                                <div class="banner-btn" id="pv_btn_t">Mua ngay <i class="fas fa-arrow-right"></i></div>
                            </div>
                            <i class="fas fa-mobile-alt banner-visual"></i>
                        </div>
                    </div>
                    <div>
                        <div class="device-frame-title">Mobile <span>(375x812)</span></div>
                        <div class="banner-preview mini phone" id="previewMobile">
                            <video class="banner-video-bg" id="pv_video_m" muted loop autoplay playsinline></video>
                            <div class="banner-text">
                                <span class="banner-badge" id="pv_badge_m">NEW</span>
                                <div class="banner-title-text" id="pv_title_m">Galaxy S24 Ultra</div>
                                <div class="banner-desc-text" id="pv_desc_m">AI Camera. Chạm đến tương lai.
Hiệu năng vượt trội.</div>
                                <div class="banner-price" id="pv_price_m"></div>
                                <div class="banner-btn" id="pv_btn_m">Mua ngay <i class="fas fa-arrow-right"></i></div>
                            </div>
                            <i class="fas fa-mobile-alt banner-visual"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="note-box">
                <i class="fas fa-circle-info"></i>
                <span>Lưu ý: Đây là bản xem trước. Banner thực tế có thể chênh lệch một chút về màu sắc tùy thiết bị.</span>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
    // ---- Helper hiển thị / xóa lỗi (dùng chung) ----
    function showFieldError(id, msg) {
        const input = document.getElementById(id);
        const err = document.getElementById(id + '-error');
        if (input) input.classList.add('is-invalid');
        if (err) { err.textContent = msg; err.style.display = 'block'; }
    }
    function clearFieldError(id) {
        const input = document.getElementById(id);
        const err = document.getElementById(id + '-error');
        if (input) input.classList.remove('is-invalid');
        if (err) { err.textContent = ''; err.style.display = 'none'; }
    }

    function resetVideoPreview() {
        ['pv_video_d', 'pv_video_t', 'pv_video_m'].forEach(id => {
            const vid = document.getElementById(id);
            if (!vid) return;
            vid.pause();
            vid.removeAttribute('src');
            vid.load();
        });
        ['previewDesktop', 'previewTablet', 'previewMobile'].forEach(id => {
            document.getElementById(id).classList.remove('has-video-bg');
        });
    }

    // ---- Chọn cách tạo banner ----
    document.querySelectorAll('input[name=creation_method]').forEach(r => {
        r.addEventListener('change', () => {
            document.querySelectorAll('.method-option').forEach(opt => {
                opt.classList.toggle('active', opt.querySelector('input').checked);
            });
            const method = document.querySelector('input[name=creation_method]:checked').value;
            document.getElementById('templateSection').style.display = method === 'template' ? '' : 'none';
            document.getElementById('customSection').style.display = method === 'custom' ? '' : 'none';

            // Toggle Ảnh / Video chỉ áp dụng cho "Upload banner"
            document.getElementById('mediaTypeToggle').style.display = method === 'upload' ? 'inline-flex' : 'none';
            if (method !== 'upload') {
                document.querySelectorAll('.mt-btn').forEach(b => b.classList.toggle('active', b.dataset.media === 'image'));
                document.getElementById('f_media_type').value = 'image';
                document.getElementById('f_image').style.display = '';
                document.getElementById('f_video').style.display = 'none';
                document.getElementById('f_video').value = '';
                document.getElementById('videoSpecBox').style.display = 'none';
                clearFieldError('f_video');
                resetVideoPreview();
            }

            if (method === 'custom') {
                resetCustomPreviewStyles();
                applyCustomColors();
                applyEffects();
                if (window._customBgDesktopUrl) applyCustomBgImage(window._customBgDesktopUrl);
            } else {
                resetCustomPreviewStyles();
                if (method === 'template') applyTemplateStyle();
            }
        });
    });

    // ---- Toggle Giá bán dựa trên Loại Banner ----
    const bannerTypeSelect = document.getElementById('f_banner_type');
    const groupPrice = document.getElementById('group_price');
    if (bannerTypeSelect && groupPrice) {
        function togglePrice() {
            if (bannerTypeSelect.value === 'promo') {
                groupPrice.style.display = 'flex';
            } else {
                groupPrice.style.display = 'none';
                document.getElementById('f_price').value = '';
                document.getElementById('f_compare_price').value = '';
                if (typeof updatePricePreview === 'function') updatePricePreview();
            }
        }
        bannerTypeSelect.addEventListener('change', togglePrice);
        togglePrice();
    }

    // ---- Upload banner: chọn Ảnh hoặc Video ----
    document.querySelectorAll('.mt-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.mt-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const type = btn.dataset.media;
            document.getElementById('f_media_type').value = type;
            const isVideo = type === 'video';

            document.getElementById('f_image').style.display = isVideo ? 'none' : '';
            document.getElementById('f_video').style.display = isVideo ? '' : 'none';
            document.getElementById('videoSpecBox').style.display = isVideo ? 'flex' : 'none';

            if (isVideo) {
                document.getElementById('f_image').value = '';
                clearFieldError('f_image');
            } else {
                document.getElementById('f_video').value = '';
                clearFieldError('f_video');
                resetVideoPreview();
            }
        });
    });

    // ---- Validate & xem trước video banner (MP4, tối đa 10MB) ----
    document.getElementById('f_video').addEventListener('change', function() {
        const file = this.files[0];
        if (!file) { resetVideoPreview(); return; }

        const isMp4 = file.type === 'video/mp4' || /\.mp4$/i.test(file.name);
        const maxSize = 10 * 1024 * 1024; // 10MB

        if (!isMp4) {
            showFieldError('f_video', 'Chỉ chấp nhận định dạng video MP4 (H.264).');
            this.value = '';
            resetVideoPreview();
            return;
        }
        if (file.size > maxSize) {
            showFieldError('f_video', 'Video vượt quá dung lượng cho phép (tối đa 10MB).');
            this.value = '';
            resetVideoPreview();
            return;
        }

        clearFieldError('f_video');
        const url = URL.createObjectURL(file);
        ['previewDesktop', 'previewTablet', 'previewMobile'].forEach((wrapId, i) => {
            const wrap = document.getElementById(wrapId);
            const vid = wrap.querySelector('.banner-video-bg');
            vid.src = url;
            vid.play().catch(() => {});
            wrap.classList.add('has-video-bg');
        });
    });

    // ---- Điều hướng carousel template ----
    const pages = document.querySelectorAll('.tpl-page');
    let currentPage = 0;
    function showPage(i) {
        currentPage = (i + pages.length) % pages.length;
        pages.forEach(p => p.classList.toggle('active', +p.dataset.page === currentPage));
    }
    document.getElementById('tplPrev').addEventListener('click', () => showPage(currentPage - 1));
    document.getElementById('tplNext').addEventListener('click', () => showPage(currentPage + 1));

    // ---- Chọn template ----
    const templateStyles = {
        samsung_dark: '',
        apple_minimal: 'tpl-apple-bg',
        xiaomi_orange: 'tpl-xiaomi-bg',
        gaming_rgb: 'tpl-gaming-bg',
        flash_sale_red: 'tpl-flash-bg',
        lifestyle_clean: 'tpl-lifestyle-bg',
    };

    function applyTemplateStyle() {
        const tpl = document.querySelector('input[name=template]:checked')?.value || 'samsung_dark';
        const cls = templateStyles[tpl] || '';
        ['previewDesktop', 'previewTablet', 'previewMobile'].forEach(id => {
            const el = document.getElementById(id);
            Object.values(templateStyles).forEach(c => c && el.classList.remove(c));
            el.style.backgroundImage = '';
            if (cls) el.classList.add(cls);
        });
    }

    document.querySelectorAll('input[name=template]').forEach(r => {
        r.addEventListener('change', () => {
            document.querySelectorAll('.tpl-card').forEach(card => {
                card.classList.toggle('active', card.querySelector('input').checked);
            });
            document.getElementById('customTplUploadInner').innerHTML =
                '<i class="fas fa-cloud-upload-alt"></i><span>Chọn ảnh nền để dùng làm mẫu riêng (thay cho mẫu có sẵn)</span>';
            applyTemplateStyle();
        });
    });

    // ---- Tải mẫu (ảnh nền) của riêng người dùng ----
    document.getElementById('customTplUploadInner').addEventListener('click', () => {
        document.getElementById('f_template_image').click();
    });
    document.getElementById('f_template_image').addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => {
            document.querySelectorAll('input[name=template]').forEach(r => r.checked = false);
            document.querySelectorAll('.tpl-card').forEach(c => c.classList.remove('active'));
            document.getElementById('customTplUploadInner').innerHTML =
                `<i class="fas fa-check-circle"></i><span>${esc(file.name)} (đã chọn)</span>`;
            ['previewDesktop', 'previewTablet', 'previewMobile'].forEach(id => {
                const el = document.getElementById(id);
                Object.values(templateStyles).forEach(c => c && el.classList.remove(c));
                el.style.backgroundImage = `url(${e.target.result})`;
                el.style.backgroundSize = 'cover';
                el.style.backgroundPosition = 'center';
            });
        };
        reader.readAsDataURL(file);
    });

    // ---- Toggle Desktop / Tablet / Mobile ----
    document.querySelectorAll('.device-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.device-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const isDesktop = btn.dataset.device === 'desktop';
            document.getElementById('deviceDesktop').classList.toggle('active', isDesktop);
            document.getElementById('deviceTabletMobile').classList.toggle('active', !isDesktop);
        });
    });

    // ---- Đồng bộ nội dung nhập -> preview + đếm ký tự ----
    function esc(s) {
        return (s || '').replace(/[&<>"']/g, c => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[c]));
    }

    function bindField(inputId, counterId, previewIds, max, fallback) {
        const input = document.getElementById(inputId);
        const counter = document.getElementById(counterId);

        function update() {
            const val = input.value;
            if (counter) counter.textContent = `${val.length}/${max}`;
            previewIds.forEach(pid => {
                const el = document.getElementById(pid);
                if (!el) return;
                const text = val.trim() ? esc(val) : (fallback || '');
                el.innerHTML = pid.startsWith('pv_desc') ? text.replace(/\n/g, '<br>') : text;
            });
        }
        input.addEventListener('input', update);
        update();
    }

    bindField('f_badge', 'cnt_badge', ['pv_badge_d', 'pv_badge_t', 'pv_badge_m'], 20, 'NEW');
    bindField('f_title', 'cnt_title', ['pv_title_d', 'pv_title_t', 'pv_title_m'], 50, '(Chưa có tiêu đề)');
    bindField('f_desc', 'cnt_desc', ['pv_desc_d', 'pv_desc_t', 'pv_desc_m'], 100, '');

    // ---- Giá bán / Giá gốc -> preview ----
    function formatVND(n) {
        return new Intl.NumberFormat('vi-VN').format(n) + '₫';
    }
    function updatePricePreview() {
        const price = parseFloat(document.getElementById('f_price').value);
        const compare = parseFloat(document.getElementById('f_compare_price').value);
        ['pv_price_d', 'pv_price_t', 'pv_price_m'].forEach(id => {
            const el = document.getElementById(id);
            if (!el) return;
            if (!price || price <= 0) {
                el.style.display = 'none';
                el.innerHTML = '';
                return;
            }
            el.style.display = 'flex';
            let html = `<span>${formatVND(price)}</span>`;
            if (compare && compare > price) {
                html += `<span class="old-price">${formatVND(compare)}</span>`;
            }
            el.innerHTML = html;
        });
    }
    document.getElementById('f_price').addEventListener('input', updatePricePreview);
    document.getElementById('f_compare_price').addEventListener('input', updatePricePreview);
    updatePricePreview();

    function bindButtonText() {
        const input = document.getElementById('f_btntext');
        const counter = document.getElementById('cnt_btn');
        const ids = ['pv_btn_d', 'pv_btn_t', 'pv_btn_m'];
        function update() {
            const val = input.value.trim() || 'Mua ngay';
            if (counter) counter.textContent = `${input.value.length}/20`;
            ids.forEach(pid => {
                document.getElementById(pid).innerHTML = `${esc(val)} <i class="fas fa-arrow-right"></i>`;
            });
        }
        input.addEventListener('input', update);
        update();
    }
    bindButtonText();

    applyTemplateStyle();

    // ---- Banner thủ công: upload ảnh Desktop/Mobile ----
    function bindCustomImageUpload(boxId, inputId, imgId, placeholderId, onLoad) {
        const box = document.getElementById(boxId);
        const input = document.getElementById(inputId);
        box.addEventListener('click', () => input.click());
        input.addEventListener('change', () => {
            const file = input.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById(imgId).src = e.target.result;
                document.getElementById(imgId).style.display = 'block';
                document.getElementById(placeholderId).style.display = 'none';
                if (onLoad) onLoad(e.target.result);
            };
            reader.readAsDataURL(file);
        });
    }

    bindCustomImageUpload('box_custom_desktop', 'f_custom_image_desktop', 'preview_custom_desktop', 'placeholder_custom_desktop', url => {
        window._customBgDesktopUrl = url;
        if (document.querySelector('input[name=creation_method]:checked')?.value === 'custom') {
            applyCustomBgImage(url);
        }
    });
    bindCustomImageUpload('box_custom_mobile', 'f_custom_image_mobile', 'preview_custom_mobile', 'placeholder_custom_mobile');

    function applyCustomBgImage(url) {
        ['previewDesktop', 'previewTablet', 'previewMobile'].forEach(id => {
            const el = document.getElementById(id);
            el.style.backgroundImage = `url(${url})`;
            el.style.backgroundSize = 'cover';
            el.style.backgroundPosition = 'center';
        });
    }

    // ---- Banner thủ công: màu nền / màu chữ / màu nút ----
    function syncColorPair(colorId, textId) {
        const colorEl = document.getElementById(colorId);
        const textEl = document.getElementById(textId);
        colorEl.addEventListener('input', () => { textEl.value = colorEl.value.toUpperCase(); applyCustomColors(); });
        textEl.addEventListener('input', () => {
            if (/^#([0-9A-Fa-f]{3}|[0-9A-Fa-f]{6})$/.test(textEl.value)) {
                colorEl.value = textEl.value;
                applyCustomColors();
            }
        });
    }
    syncColorPair('f_custom_bg', 'f_custom_bg_text');
    syncColorPair('f_custom_text', 'f_custom_text_text');
    syncColorPair('f_custom_btn', 'f_custom_btn_text');

    function applyCustomColors() {
        if (document.querySelector('input[name=creation_method]:checked')?.value !== 'custom') return;
        const bg = document.getElementById('f_custom_bg').value;
        const text = document.getElementById('f_custom_text').value;
        const btn = document.getElementById('f_custom_btn').value;
        ['previewDesktop', 'previewTablet', 'previewMobile'].forEach(id => {
            const el = document.getElementById(id);
            if (!el.style.backgroundImage) el.style.background = bg;
            el.style.color = text;
            const btnEl = el.querySelector('.banner-btn');
            if (btnEl) { btnEl.style.background = btn; btnEl.style.color = '#fff'; }
        });
    }

    // ---- Banner thủ công: hiệu ứng (đổ bóng / gradient / bo góc) ----
    function applyEffects() {
        if (document.querySelector('input[name=creation_method]:checked')?.value !== 'custom') return;
        const shadow = document.getElementById('f_fx_shadow').checked;
        const gradient = document.getElementById('f_fx_gradient').checked;
        const radius = document.getElementById('f_fx_radius').checked;
        ['previewDesktop', 'previewTablet', 'previewMobile'].forEach(id => {
            const el = document.getElementById(id);
            el.style.boxShadow = shadow ? '0 10px 30px rgba(0,0,0,.25)' : 'none';
            el.style.borderRadius = radius ? '20px' : '';
            el.classList.toggle('has-gradient-overlay', gradient);
        });
    }
    ['f_fx_shadow', 'f_fx_gradient', 'f_fx_radius'].forEach(id => {
        document.getElementById(id).addEventListener('change', applyEffects);
    });

    function resetCustomPreviewStyles() {
        ['previewDesktop', 'previewTablet', 'previewMobile'].forEach(id => {
            const el = document.getElementById(id);
            el.style.backgroundImage = '';
            el.style.background = '';
            el.style.color = '';
            el.style.boxShadow = '';
            el.style.borderRadius = '';
            el.classList.remove('has-gradient-overlay');
            const btnEl = el.querySelector('.banner-btn');
            if (btnEl) { btnEl.style.background = ''; btnEl.style.color = ''; }
        });
    }

    // ---- Nội dung: căn chỉnh văn bản trái / giữa / phải ----
    document.querySelectorAll('.align-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.align-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const align = btn.dataset.align;
            document.getElementById('f_text_align').value = align;

            ['previewDesktop', 'previewTablet', 'previewMobile'].forEach(id => {
                const wrap = document.getElementById(id);
                const textEl = wrap.querySelector('.banner-text');
                const btnEl = wrap.querySelector('.banner-btn');
                textEl.style.textAlign = align;
                wrap.style.justifyContent = align === 'center' ? 'center' : (align === 'right' ? 'flex-end' : 'flex-start');
                textEl.style.marginLeft = align !== 'left' ? 'auto' : '';
                textEl.style.marginRight = align !== 'right' ? (align === 'center' ? 'auto' : '') : '';
                if (btnEl) {
                    btnEl.style.marginLeft = align === 'left' ? '0' : 'auto';
                    btnEl.style.marginRight = align === 'right' ? '0' : (align === 'center' ? 'auto' : '');
                }
            });
        });
    });

    // ---- Validate cơ bản trước khi submit ----
    (function() {
        const form = document.getElementById('bannerForm');

        function setError(inputId, message) {
            const input = document.getElementById(inputId);
            const errorDiv = document.getElementById(inputId + '-error');
            if (input) input.classList.add('is-invalid');
            if (errorDiv) { errorDiv.textContent = message; errorDiv.style.display = 'block'; }
        }
        function clearError(inputId) {
            const input = document.getElementById(inputId);
            const errorDiv = document.getElementById(inputId + '-error');
            if (input) input.classList.remove('is-invalid');
            if (errorDiv) { errorDiv.textContent = ''; errorDiv.style.display = 'none'; }
        }

        document.getElementById('f_title')?.addEventListener('input', () => clearError('f_title'));
        document.getElementById('f_price')?.addEventListener('input', () => clearError('f_price'));
        document.getElementById('f_image')?.addEventListener('change', () => clearError('f_image'));

        form.addEventListener('submit', function(e) {
            let isValid = true;

            const title = document.getElementById('f_title');
            if (!title || !title.value.trim()) {
                setError('f_title', 'Vui lòng nhập tiêu đề banner.');
                isValid = false;
            } else {
                clearError('f_title');
            }

            const price = document.getElementById('f_price');
            if (price && price.value && parseFloat(price.value) < 0) {
                setError('f_price', 'Vui lòng nhập giá bán hợp lệ.');
                isValid = false;
            } else {
                clearError('f_price');
            }

            const method = document.querySelector('input[name=creation_method]:checked')?.value;
            const mediaType = document.getElementById('f_media_type')?.value || 'image';
            const imageInput = document.getElementById('f_image');
            const videoInput = document.getElementById('f_video');

            const hasImage = {{ $banner->image ? 'true' : 'false' }};
            const hasVideo = {{ $banner->video ? 'true' : 'false' }};

            if (method === 'upload' && mediaType === 'video') {
                if (!hasVideo && (!videoInput || !videoInput.files.length)) {
                    setError('f_video', 'Vui lòng chọn video cho banner.');
                    isValid = false;
                } else {
                    clearError('f_video');
                }
                clearError('f_image');
            } else if (method === 'upload') {
                if (!hasImage && (!imageInput || !imageInput.files.length)) {
                    setError('f_image', 'Vui lòng chọn ảnh cho banner.');
                    isValid = false;
                } else {
                    clearError('f_image');
                }
                clearError('f_video');
            } else {
                clearError('f_image');
                clearError('f_video');
            }

            const customImageInput = document.getElementById('f_custom_image_desktop');
            if (method === 'custom' && !hasImage && (!customImageInput || !customImageInput.files.length)) {
                setError('f_custom_image_desktop', 'Vui lòng chọn ảnh Desktop cho banner.');
                isValid = false;
            } else if (customImageInput) {
                clearError('f_custom_image_desktop');
            }

            if (!isValid) e.preventDefault();
        });
    })();
</script>
@endpush