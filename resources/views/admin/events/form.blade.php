@extends('layouts.admin')

@section('title', $event->exists ? 'Sửa Event' : 'Tạo Event mới')

@push('styles')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 6px;
    }

    .page-header h2 {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .breadcrumb {
        font-size: 13px;
        color: #888;
        margin-bottom: 20px;
    }

    .breadcrumb a {
        color: #777;
        text-decoration: none;
    }

    .breadcrumb .current {
        color: #5B4CF0;
        font-weight: 600;
    }

    .header-actions {
        display: flex;
        gap: 10px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 18px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        text-decoration: none;
        transition: .15s;
    }

    .btn-primary {
        background: #5B4CF0;
        color: #fff;
        box-shadow: 0 2px 6px rgba(91, 76, 240, .3);
    }

    .btn-primary:hover { background: #4a3cd6; }

    .btn-outline {
        background: #fff;
        border: 1px solid #ddd;
        color: #444;
    }

    .btn-outline:hover { background: #f5f5f5; }

    .btn-outline-primary {
        background: #fff;
        border: 1px solid #5B4CF0;
        color: #5B4CF0;
    }

    .btn-outline-primary:hover { background: #F3F1FF; }

    .layout-wrap {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 20px;
        align-items: flex-start;
        margin-top: 18px;
    }

    .section-card {
        background: #fff;
        border-radius: 12px;
        padding: 22px 24px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .06);
        margin-bottom: 18px;
    }

    .section-title {
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 18px;
        color: #222;
    }

    .form-group { margin-bottom: 16px; }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
    }

    .form-row-3 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 18px;
        align-items: end;
    }

    label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 6px;
        color: #333;
    }

    .required { color: #C62828; }

    .form-hint {
        font-size: 12px;
        color: #999;
        margin-top: 4px;
    }

    .form-control {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 13px;
        font-family: inherit;
    }

    .form-control:focus {
        outline: none;
        border-color: #5B4CF0;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 90px;
    }

    .char-count {
        text-align: right;
        font-size: 11px;
        color: #aaa;
        margin-top: 4px;
    }

    .is-invalid { border-color: #C62828 !important; }

    .invalid-feedback {
        color: #C62828;
        font-size: 12px;
        margin-top: 4px;
    }

    .status-row {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 42px;
        height: 23px;
        flex-shrink: 0;
    }

    .switch input { opacity: 0; width: 0; height: 0; }

    .slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background: #ccc;
        border-radius: 24px;
        transition: .2s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 17px;
        width: 17px;
        left: 3px;
        top: 3px;
        background: #fff;
        border-radius: 50%;
        transition: .2s;
    }

    .switch input:checked + .slider { background: #5B4CF0; }
    .switch input:checked + .slider:before { transform: translateX(19px); }

    .checkbox-line {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #444;
        margin-top: 8px;
    }

    .checkbox-line input { accent-color: #5B4CF0; width: 15px; height: 15px; }

    .radio-line {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: #444;
    }

    .radio-line input { accent-color: #5B4CF0; }

    .radio-group {
        display: flex;
        gap: 20px;
        margin-bottom: 14px;
    }

    .section-title-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    /* Products table (visual only — needs product relation to be wired up) */
    .products-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        margin-top: 14px;
    }

    .products-table th {
        background: #fafafa;
        padding: 10px 12px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        color: #888;
        border-bottom: 1px solid #eee;
    }

    .products-table td {
        padding: 10px 12px;
        border-bottom: 1px solid #f2f2f2;
        vertical-align: middle;
    }

    .prod-thumb {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        object-fit: cover;
        background: #f0f0f0;
    }

    .discount-input {
        display: flex;
        gap: 6px;
    }

    .discount-input input {
        width: 60px;
        padding: 6px 8px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 13px;
    }

    .discount-input select {
        padding: 6px 8px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 13px;
    }

    .row-remove {
        color: #C62828;
        background: #fff;
        border: 1px solid #f0d0d0;
        border-radius: 6px;
        width: 28px;
        height: 28px;
        cursor: pointer;
    }

    .selected-note {
        font-size: 12px;
        color: #888;
        margin-top: 10px;
    }

    /* Image upload */
    .image-upload {
        border: 2px dashed #ddd;
        border-radius: 10px;
        padding: 18px;
        text-align: center;
        cursor: pointer;
        position: relative;
    }

    .image-upload.is-invalid { border-color: #C62828; }

    .image-upload img {
        max-width: 100%;
        max-height: 160px;
        border-radius: 6px;
        margin-bottom: 8px;
    }

    .image-upload input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
    }

    .image-upload i {
        font-size: 26px;
        color: #bbb;
        margin-bottom: 8px;
        display: block;
    }

    .image-upload .text { font-size: 12px; color: #888; }

    /* Sidebar preview */
    .preview-card {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .06);
        margin-bottom: 18px;
    }

    .preview-card h4 {
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 14px;
    }

    .preview-banner {
        width: 100%;
        aspect-ratio: 16/10;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 14px;
        position: relative;
        background: linear-gradient(160deg, #8B0000, #C62828 55%, #E53935);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .preview-banner img.bg {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: .5;
    }

    .preview-banner .txt {
        position: relative;
        z-index: 1;
        text-align: center;
        color: #fff;
        padding: 10px;
    }

    .preview-banner .p-tag {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .5px;
        opacity: .9;
    }

    .preview-banner .p-title {
        font-size: 20px;
        font-weight: 800;
        line-height: 1.15;
        margin: 4px 0;
    }

    .preview-banner .p-offer {
        font-size: 12px;
        font-weight: 600;
        opacity: .95;
    }

    .preview-name {
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .preview-desc {
        font-size: 12px;
        color: #777;
        line-height: 1.5;
        margin-bottom: 14px;
    }

    .preview-meta {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        font-size: 12px;
        color: #555;
        margin-bottom: 10px;
    }

    .preview-meta i {
        color: #999;
        width: 14px;
        margin-top: 2px;
    }

    .quick-info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 9px 0;
        border-bottom: 1px solid #f2f2f2;
        font-size: 12.5px;
    }

    .quick-info-row:last-child { border-bottom: none; }

    .quick-info-row .label { color: #888; }
    .quick-info-row .value { color: #333; font-weight: 600; }

    .pill {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .pill-success { background: #E8F5E9; color: #2E7D32; }
    .pill-muted { background: #f0f0f0; color: #999; }
</style>
@endpush

@section('content')

<div class="page-header">
    <div>
        <h2><i class="fas fa-gift"></i> {{ $event->exists ? 'Sửa Event' : 'Tạo Event mới' }}</h2>
        <div class="breadcrumb">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a> &gt;
            Marketing &gt;
            <a href="{{ route('admin.events.index') }}">Event</a> &gt;
            <span class="current">{{ $event->exists ? 'Sửa Event' : 'Tạo Event' }}</span>
        </div>
    </div>
    <div class="header-actions">
        <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">Hủy bỏ</a>
        <button type="submit" form="eventForm" class="btn btn-primary">
            <i class="fas fa-save mr-1"></i> {{ $event->exists ? 'Cập nhật Sự kiện' : 'Tạo Sự kiện' }}
        </button>
    </div>
</div>

<form method="POST"
    action="{{ $event->exists ? url('/admin/su-kien/' . $event->getKey()) : url('/admin/su-kien') }}"
    enctype="multipart/form-data" id="eventForm">
    @csrf
    @if($event->exists) @method('PUT') @endif

    <div class="layout-wrap">
        {{-- CỘT TRÁI --}}
        <div>
            {{-- 1. Thông tin Event --}}
            <div class="section-card">
                <div class="section-title">1. Thông tin Event</div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Tên Event <span class="required">*</span></label>
                        <input type="text" id="event_title" name="title" id="f_title" value="{{ old('title', $event->title) }}"
                            class="form-control @error('title') is-invalid @enderror"
                            placeholder="Vd: 8.8 SUPER SALE" oninput="updatePreview()">
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>Loại Event <span class="required">*</span></label>
                        <select name="tag" id="f_tag" class="form-control" onchange="updatePreview()">
                            <option value="Khuyến mãi" @selected(old('tag', $event->tag)=='Khuyến mãi' )>Khuyến mãi</option>
                            <option value="Flash Sale" @selected(old('tag', $event->tag)=='Flash Sale' )>Flash Sale</option>
                            <option value="Sự kiện lớn" @selected(old('tag', $event->tag)=='Sự kiện lớn' )>Sự kiện lớn</option>
                        </select>
                        <div class="form-hint">Lưu tạm vào nhãn (tag) của event.</div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Đường dẫn chuyển tiếp (Link) <span class="required">*</span></label>
                    <input type="text" name="button_link" class="form-control @error('button_link') is-invalid @enderror" value="{{ old('button_link', $event->button_link ?? '') }}" placeholder="Vd: /tin-tuc/su-kien-8-3" required>
                    @error('button_link')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-hint" style="margin-top: 6px; color: #64748b; font-size: 13px;">Khi khách hàng nhấn vào Banner sự kiện trên Trang chủ, họ sẽ được chuyển đến đường dẫn này (thường là link bài viết Tin tức thể lệ chương trình).</div>
                </div>

                <div class="form-group">
                    <div class="status-row">
                        <label style="margin:0">Trạng thái</label>
                        <label class="switch">
                            <input type="checkbox" name="is_active" value="1" id="f_active"
                                {{ old('is_active', $event->is_active ?? true) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                        <span style="font-size:13px;color:#444">Kích hoạt</span>
                    </div>
                </div>

                <div class="form-group">
                    <label>Mô tả</label>
                    <textarea id="event_description" name="description" id="f_desc" maxlength="500"
                        class="form-control @error('description') is-invalid @enderror"
                        placeholder="Mô tả về event, ưu đãi..." oninput="updatePreview(); updateCount()">{{ old('description', $event->description) }}</textarea>
                    <div class="char-count"><span id="descCount">0</span>/500</div>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- 2. Thời gian diễn ra --}}
            <div class="section-card">
                <div class="section-title">2. Thời gian diễn ra</div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Ngày bắt đầu <span class="required">*</span></label>
                        <input type="datetime-local" id="event_start_date" name="start_date"
                            value="{{ old('start_date', $event->start_date?->format('Y-m-d\TH:i')) }}"
                            class="form-control @error('start_date') is-invalid @enderror" onchange="updatePreview()">
                        @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="checkbox-line">
                            <input type="checkbox" id="auto_start" checked>
                            <label style="margin:0;font-weight:400" for="auto_start">Tự động kích hoạt Event khi đến thời gian bắt đầu</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Ngày kết thúc <span class="required">*</span></label>
                        <input type="datetime-local" id="event_end_date" name="end_date"
                            value="{{ old('end_date', $event->end_date?->format('Y-m-d\TH:i')) }}"
                            class="form-control @error('end_date') is-invalid @enderror" onchange="updatePreview()">
                        @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="checkbox-line">
                            <input type="checkbox" id="auto_end" checked>
                            <label style="margin:0;font-weight:400" for="auto_end">Tự động kết thúc Event khi đến thời gian kết thúc</label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Sản phẩm áp dụng --}}
            @php
                $selectedProductIds = old('product_ids', clone $event->products ? $event->products->pluck('id')->toArray() : []);
                $selectedCategoryIds = old('category_ids', clone $event->categories ? $event->categories->pluck('id')->toArray() : []);
            @endphp
            <div class="section-card">
                <div class="section-title">3. Phạm vi áp dụng giảm giá</div>

                <div class="radio-group">
                    <label class="radio-line"><input type="radio" name="apply_scope" value="all" {{ old('apply_scope', $event->apply_scope) == 'all' ? 'checked' : '' }} onchange="toggleScope()"> Tất cả sản phẩm</label>
                    <label class="radio-line"><input type="radio" name="apply_scope" value="category" {{ old('apply_scope', $event->apply_scope) == 'category' ? 'checked' : '' }} onchange="toggleScope()"> Theo danh mục</label>
                    <label class="radio-line"><input type="radio" name="apply_scope" value="select" {{ old('apply_scope', $event->apply_scope) == 'select' ? 'checked' : '' }} onchange="toggleScope()"> Chọn sản phẩm</label>
                </div>

                <div id="scope_category_box" style="display:none; margin-top:15px">
                    <label>Chọn danh mục</label>
                    <div style="max-height:200px;overflow-y:auto;border:1px solid #ddd;padding:10px;border-radius:4px">
                        @foreach($categories as $cat)
                        <label style="display:block;margin-bottom:5px;font-weight:400">
                            <input type="checkbox" name="category_ids[]" value="{{ $cat->id }}" {{ in_array($cat->id, $selectedCategoryIds) ? 'checked' : '' }}>
                            {{ $cat->name }}
                        </label>
                        @endforeach
                    </div>
                </div>

                <div id="scope_product_box" style="display:none; margin-top:15px">
                    <label>Chọn sản phẩm</label>
                    <div style="margin-bottom:10px;">
                        <input type="text" id="productSearch" class="form-control" placeholder="Tìm kiếm sản phẩm..." onkeyup="filterProducts()">
                    </div>
                    <div id="productList" style="max-height:300px;overflow-y:auto;border:1px solid #ddd;padding:10px;border-radius:4px">
                        @foreach($products as $prod)
                        <label class="prod-item" style="display:block;margin-bottom:5px;font-weight:400">
                            <input type="checkbox" name="product_ids[]" value="{{ $prod->id }}" {{ in_array($prod->id, $selectedProductIds) ? 'checked' : '' }}>
                            <span class="prod-name">[{{ $prod->sku }}] {{ $prod->name }}</span> - {{ number_format($prod->price) }}đ
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- 4. Ưu đãi & Voucher --}}
            <div class="section-card">
                <div class="section-title">4. Ưu đãi &amp; Voucher</div>

                <label>Hình thức giảm giá</label>
                <div class="radio-group">
                    <label class="radio-line"><input type="radio" name="discount_type" value="percent" {{ old('discount_type', $event->discount_type) == 'percent' ? 'checked' : '' }}> Theo %</label>
                    <label class="radio-line"><input type="radio" name="discount_type" value="amount" {{ old('discount_type', $event->discount_type) == 'amount' ? 'checked' : '' }}> Theo số tiền</label>
                    <label class="radio-line"><input type="radio" name="discount_type" value="fixed" {{ old('discount_type', $event->discount_type) == 'fixed' ? 'checked' : '' }}> Giá cố định</label>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Giá trị giảm</label>
                        <input type="number" step="0.01" name="discount_value" class="form-control @error('discount_value') is-invalid @enderror" placeholder="Vd: 10 hoặc 500000" value="{{ old('discount_value', $event->discount_value ? floatval($event->discount_value) : '') }}">
                        @error('discount_value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>Giảm tối đa (VNĐ - tùy chọn)</label>
                        <input type="number" step="0.01" name="max_discount" class="form-control" placeholder="Vd: 500000" value="{{ old('max_discount', $event->max_discount ? floatval($event->max_discount) : '') }}">
                    </div>
                    <div class="form-group">
                        <label>Voucher áp dụng (tùy chọn)</label>
                        <select name="voucher_id" class="form-control">
                            <option value="">-- Không áp dụng --</option>
                            @foreach($vouchers as $v)
                            <option value="{{ $v->id }}" {{ old('voucher_id', $event->voucher_id) == $v->id ? 'selected' : '' }}>{{ $v->code }}</option>
                            @endforeach
                        </select>
                        <div class="form-hint">
                            <a href="{{ url('/admin/vouchers/create') }}">+ Tạo voucher mới</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 5. Cài đặt hiển thị --}}
            <div class="section-card">
                <div class="section-title">5. Cài đặt hiển thị</div>

                <div class="form-row-3">
                    <div class="checkbox-line" style="margin-top:0">
                        <input type="checkbox" id="show_countdown" checked>
                        <label style="margin:0;font-weight:400" for="show_countdown">Hiển thị countdown</label>
                    </div>
                    <div class="checkbox-line" style="margin-top:0">
                        <input type="checkbox" id="show_in_list" checked>
                        <label style="margin:0;font-weight:400" for="show_in_list">Hiển thị trong danh sách Event</label>
                    </div>
                    <div class="checkbox-line" style="margin-top:0">
                        <input type="checkbox" id="allow_join" checked>
                        <label style="margin:0;font-weight:400" for="allow_join">Cho phép khách hàng tham gia</label>
                    </div>
                </div>

                <div class="form-group" style="max-width:160px;margin-top:16px">
                    <label>Thứ tự hiển thị</label>
                    <input type="number" id="event_sort_order" name="sort_order" min="0"
                        value="{{ old('sort_order', $event->sort_order ?? 1) }}"
                        class="form-control @error('sort_order') is-invalid @enderror">
                    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Ảnh sự kiện --}}
            <div class="section-card">
                <div class="section-title">Ảnh Event</div>
                <label class="image-upload @error('image') is-invalid @enderror" id="imageDropzone">
                    <input type="file" name="image" accept="image/*" onchange="previewImage(this)">
                    @if($event->image)
                    <img src="{{ $event->image }}" id="previewImg">
                    @else
                    <i class="fas fa-cloud-upload-alt"></i>
                    <img id="previewImg" style="display:none">
                    @endif
                    <div class="text">Bấm để chọn ảnh (khuyến nghị tỉ lệ ngang 16:9)</div>
                </label>
                @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- CỘT PHẢI: xem trước --}}
        <div>
            <div class="preview-card">
                <h4>Xem trước Event</h4>

                <div class="preview-banner" id="previewEvent">
                    <img class="bg" id="previewBg" style="display:none">
                    <div class="txt">
                        <div class="p-tag" id="previewTag">{{ $event->tag ?: 'KHUYẾN MÃI' }}</div>
                        <div class="p-title" id="previewTitle">{{ $event->title ?: 'Tên Event' }}</div>
                        <div class="p-offer" id="previewOffer">{{ $event->offer_text ?: 'Ưu đãi nổi bật' }}</div>
                    </div>
                </div>

                <div class="preview-name" id="previewName2">{{ $event->title ?: 'Tên Event' }}</div>
                <div class="preview-desc" id="previewDesc2">{{ $event->description ?: 'Mô tả sự kiện sẽ hiển thị ở đây.' }}</div>

                <div class="preview-meta">
                    <i class="fas fa-calendar"></i>
                    <span id="previewDates">
                        {{ $event->start_date?->format('d/m/Y H:i') ?: '...' }} - {{ $event->end_date?->format('d/m/Y H:i') ?: '...' }}
                    </span>
                </div>
                <div class="preview-meta">
                    <i class="fas fa-tag"></i>
                    <span id="previewTagLine">{{ $event->tag ?: 'Khuyến mãi' }}</span>
                </div>
                <div class="preview-meta">
                    <i class="fas fa-box"></i>
                    <span>0 sản phẩm áp dụng</span>
                </div>
            </div>

            <div class="preview-card">
                <h4>Thông tin nhanh</h4>
                <div class="quick-info-row">
                    <span class="label">Ngày tạo</span>
                    <span class="value">{{ $event->exists ? $event->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</span>
                </div>
                <div class="quick-info-row">
                    <span class="label">Cập nhật lần cuối</span>
                    <span class="value">{{ $event->exists && $event->updated_at ? $event->updated_at->format('d/m/Y H:i') : '-' }}</span>
                </div>
                <div class="quick-info-row">
                    <span class="label">Người tạo</span>
                    <span class="value">{{ auth()->user()->name ?? 'Admin' }}</span>
                </div>
                <div class="quick-info-row">
                    <span class="label">Trạng thái hiện tại</span>
                    <span class="pill {{ ($event->is_active ?? true) ? 'pill-success' : 'pill-muted' }}">
                        {{ ($event->is_active ?? true) ? 'Kích hoạt' : 'Nháp' }}
                    </span>
                </div>
                <div class="quick-info-row">
                    <span class="label">Hiển thị trên website</span>
                    <span class="pill {{ ($event->is_active ?? true) ? 'pill-success' : 'pill-muted' }}">
                        {{ ($event->is_active ?? true) ? 'Có' : 'Không' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- is_active thật sự gửi lên server qua nút Lưu nháp / Tạo Event ở header (name="is_active") --}}
</form>

<script>
    function previewImage(input) {
        const img = document.getElementById('previewImg');
        const bg = document.getElementById('previewBg');
        if (input.files && input.files[0]) {
            const url = URL.createObjectURL(input.files[0]);
            img.src = url;
            img.style.display = 'block';
            bg.src = url;
            bg.style.display = 'block';
        }
    }

    function toggleScope() {
        const scope = document.querySelector('input[name="apply_scope"]:checked');
        if(!scope) return;
        document.getElementById('scope_category_box').style.display = scope.value === 'category' ? 'block' : 'none';
        document.getElementById('scope_product_box').style.display = scope.value === 'select' ? 'block' : 'none';
    }

    function filterProducts() {
        const input = document.getElementById('productSearch').value.toLowerCase();
        const items = document.querySelectorAll('.prod-item');
        items.forEach(item => {
            const name = item.querySelector('.prod-name').textContent.toLowerCase();
            item.style.display = name.includes(input) ? 'block' : 'none';
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (!document.querySelector('input[name="apply_scope"]:checked')) {
            const allRadio = document.querySelector('input[name="apply_scope"][value="all"]');
            if(allRadio) allRadio.checked = true;
        }
        toggleScope();
    });

    function updateCount() {
        document.getElementById('descCount').textContent = document.getElementById('f_desc').value.length;
    }

    function updatePreview() {
        const title = document.getElementById('f_title').value || 'Tên Event';
        const tag = document.getElementById('f_tag').value || 'KHUYẾN MÃI';

        document.getElementById('previewTitle').textContent = title;
        document.getElementById('previewTag').textContent = tag.toUpperCase();
        document.getElementById('previewName2').textContent = title;
        document.getElementById('previewTagLine').textContent = tag;
        document.getElementById('previewDesc2').textContent = document.getElementById('f_desc').value || 'Mô tả sự kiện sẽ hiển thị ở đây.';
    }

    document.addEventListener('DOMContentLoaded', function () {
        updateCount();
        updatePreview();
    });
</script>

@endsection