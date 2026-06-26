@extends('layouts.admin')

@section('title', $banner->exists ? 'Sửa banner' : 'Thêm banner')

@push('styles')
<style>
    .breadcrumb {
        font-size: 13px;
        color: #888;
        margin-bottom: 16px;
    }

    .breadcrumb a {
        color: #1565C0;
        text-decoration: none;
    }

    .layout-wrap {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 22px;
        align-items: flex-start;
    }

    .form-card {
        background: #fff;
        border-radius: 10px;
        padding: 26px 28px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .07);
    }

    .form-title {
        font-size: 17px;
        font-weight: 700;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-subtitle {
        font-size: 13px;
        color: #888;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
    }

    label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #444;
        margin-bottom: 6px;
    }

    .form-control {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid #ddd;
        border-radius: 7px;
        font-size: 14px;
        outline: none;
    }

    .form-control:focus {
        border-color: #1565C0;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 70px;
    }

    .invalid-feedback {
        color: #C62828;
        font-size: 12px;
        margin-top: 4px;
    }

    .is-invalid {
        border-color: #C62828 !important;
    }

    .form-hint {
        font-size: 12px;
        color: #999;
        margin-top: 5px;
    }

    /* Toggle kiểu banner */
    .layout-toggle {
        display: flex;
        gap: 10px;
        margin-bottom: 22px;
    }

    .layout-option {
        flex: 1;
        border: 2px solid #e6e6e6;
        border-radius: 10px;
        padding: 14px;
        cursor: pointer;
        text-align: center;
        transition: .15s;
    }

    .layout-option:hover {
        border-color: #90caf9;
    }

    .layout-option.active {
        border-color: #1565C0;
        background: #F2F8FF;
    }

    .layout-option input {
        display: none;
    }

    .layout-option i {
        font-size: 22px;
        color: #1565C0;
        margin-bottom: 6px;
        display: block;
    }

    .layout-option .opt-title {
        font-weight: 700;
        font-size: 13px;
    }

    .layout-option .opt-desc {
        font-size: 11.5px;
        color: #888;
        margin-top: 2px;
    }

    .section-divider {
        margin: 22px 0 16px;
        border-top: 1px solid #eee;
        padding-top: 16px;
    }

    .section-label {
        font-size: 12px;
        font-weight: 700;
        color: #888;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .color-row {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .color-row input[type=color] {
        width: 42px;
        height: 36px;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 2px;
        cursor: pointer;
    }

    .color-row input[type=text] {
        flex: 1;
    }

    .clear-color {
        font-size: 12px;
        color: #1565C0;
        cursor: pointer;
        white-space: nowrap;
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
    }

    .toggle-input input {
        opacity: 0;
        width: 0;
        height: 0;
    }

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

    .toggle-input input:checked+.toggle-slider {
        background: #2E7D32;
    }

    .toggle-input input:checked+.toggle-slider:before {
        transform: translateX(18px);
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 22px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 20px;
        border-radius: 7px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        text-decoration: none;
    }

    .btn-primary {
        background: #1565C0;
        color: #fff;
    }

    .btn-primary:hover {
        background: #0D47A1;
    }

    .btn-outline {
        background: #fff;
        border: 1px solid #ddd;
        color: #444;
    }

    .btn-outline:hover {
        background: #f5f5f5;
    }

    .field-group-text {
        display: none;
    }

    .field-group-text.show {
        display: block;
    }

    /* Preview panel */
    .preview-card {
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .07);
        position: sticky;
        top: 20px;
    }

    .preview-label {
        font-size: 12px;
        font-weight: 700;
        color: #888;
        text-transform: uppercase;
        margin-bottom: 10px;
        letter-spacing: .5px;
    }

    .preview-box {
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #eee;
        min-height: 180px;
        position: relative;
        background: #EBF3FF;
    }

    .preview-split {
        display: grid;
        grid-template-columns: 1fr 1fr;
        min-height: 180px;
    }

    .preview-content {
        padding: 18px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 6px;
    }

    .preview-label-text {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: .85;
    }

    .preview-title-text {
        font-size: 17px;
        font-weight: 800;
        line-height: 1.25;
    }

    .preview-desc-text {
        font-size: 12px;
        opacity: .85;
    }

    .preview-price-text {
        font-size: 14px;
        font-weight: 800;
        color: #E53935;
    }

    .preview-btn {
        display: inline-block;
        background: #1565C0;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 6px;
        width: fit-content;
        margin-top: 4px;
    }

    .preview-img-side {
        background: #dce8f5;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .preview-img-side img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .preview-img-full {
        width: 100%;
        height: 100%;
        min-height: 180px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .preview-img-full img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .preview-placeholder {
        color: #aaa;
        font-size: 13px;
        text-align: center;
        padding: 20px;
    }
</style>
@endpush

@section('content')

<div class="breadcrumb">
    <a href="{{ route('admin.banners.index') }}">Banner trang chủ</a>
    &rsaquo; {{ $banner->exists ? 'Sửa: ' . ($banner->title ?: 'Banner #'.$banner->id) : 'Thêm banner mới' }}
</div>

<form method="POST" action="{{ $banner->exists ? route('admin.banners.update', $banner) : route('admin.banners.store') }}" enctype="multipart/form-data" id="bannerForm">
    @csrf
    @if($banner->exists) @method('PUT') @endif

    <div class="layout-wrap">
        <div class="form-card">
            <div class="form-title">
                <i class="fas fa-image" style="color:#1565C0"></i>
                {{ $banner->exists ? 'Sửa banner' : 'Thêm banner mới' }}
            </div>
            <div class="form-subtitle">Banner dùng cho mọi mục đích: giới thiệu sản phẩm, khuyến mãi, sự kiện, thông báo... không bắt buộc gắn với sản phẩm nào.</div>

            {{-- Chọn kiểu banner --}}
            <label>Kiểu banner</label>
            <div class="layout-toggle">
                <label class="layout-option {{ old('layout', $banner->layout ?: 'split') === 'split' ? 'active' : '' }}" data-layout-option="split">
                    <input type="radio" name="layout" value="split" {{ old('layout', $banner->layout ?: 'split') === 'split' ? 'checked' : '' }}>
                    <i class="fas fa-columns"></i>
                    <div class="opt-title">Chữ + Ảnh</div>
                    <div class="opt-desc">Có tiêu đề, mô tả, nút bấm bên cạnh ảnh</div>
                </label>
                <label class="layout-option {{ old('layout', $banner->layout) === 'image' ? 'active' : '' }}" data-layout-option="image">
                    <input type="radio" name="layout" value="image" {{ old('layout', $banner->layout) === 'image' ? 'checked' : '' }}>
                    <i class="fas fa-panorama"></i>
                    <div class="opt-title">Chỉ ảnh</div>
                    <div class="opt-desc">Banner ảnh full, bấm vào để đi tới liên kết</div>
                </label>
            </div>

            {{-- Các trường chữ - chỉ hiện khi chọn "Chữ + Ảnh" --}}
            <div id="textFields" class="field-group-text">
                <div class="form-group">
                    <label>Nhãn nhỏ phía trên</label>
                    <input type="text" name="label" id="f_label" value="{{ old('label', $banner->label) }}"
                        class="form-control @error('label') is-invalid @enderror" placeholder="VD: ƯU ĐÃI HÔM NAY / SỰ KIỆN ĐẶC BIỆT">
                    @error('label')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label>Tiêu đề</label>
                    <input type="text" name="title" id="f_title" value="{{ old('title', $banner->title) }}"
                        class="form-control @error('title') is-invalid @enderror" placeholder="Tiêu đề chính của banner">
                    @error('title')<div class="invalid-feedback" id="f_title-error">{{ $message }}</div>@enderror
                    @unless($errors->has('title'))<div class="invalid-feedback" id="f_title-error"></div>@endunless
                </div>

                <div class="form-group">
                    <label>Mô tả ngắn</label>
                    <textarea name="description" id="f_desc" class="form-control @error('description') is-invalid @enderror"
                        placeholder="Nội dung mô tả thêm cho banner...">{{ old('description', $banner->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Dòng nhấn mạnh (giá, ưu đãi...)</label>
                        <input type="text" name="price_text" id="f_price" value="{{ old('price_text', $banner->price_text) }}"
                            class="form-control @error('price_text') is-invalid @enderror" placeholder="VD: Giá từ 29.990.000đ / Giảm đến 50%">
                        @error('price_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>Chữ trên nút bấm</label>
                        <input type="text" name="button_text" id="f_btntext" value="{{ old('button_text', $banner->button_text) }}"
                            class="form-control @error('button_text') is-invalid @enderror" placeholder="VD: MUA NGAY, XEM NGAY, KHÁM PHÁ... (để trống = không có nút)">
                        @error('button_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Màu nền khu vực chữ</label>
                        <div class="color-row">
                            <input type="color" id="f_bgcolor_picker" value="{{ old('bg_color', $banner->bg_color ?: '#EBF3FF') }}">
                            <input type="text" name="bg_color" id="f_bgcolor" value="{{ old('bg_color', $banner->bg_color) }}" class="form-control" placeholder="Để trống = mặc định">
                            <span class="clear-color" onclick="document.getElementById('f_bgcolor').value='';updatePreview()">Xoá</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Màu chữ</label>
                        <div class="color-row">
                            <input type="color" id="f_textcolor_picker" value="{{ old('text_color', $banner->text_color ?: '#0D1B2A') }}">
                            <input type="text" name="text_color" id="f_textcolor" value="{{ old('text_color', $banner->text_color) }}" class="form-control" placeholder="Để trống = mặc định">
                            <span class="clear-color" onclick="document.getElementById('f_textcolor').value='';updatePreview()">Xoá</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section-divider">
                <div class="section-label">Hình ảnh &amp; liên kết</div>
            </div>

            <div class="form-group">
                <label>Ảnh banner</label>
                <input type="file" name="image" id="f_image" accept="image/*" class="form-control @error('image') is-invalid @enderror">
                @error('image')<div class="invalid-feedback" id="f_image-error">{{ $message }}</div>@enderror
                @unless($errors->has('image'))<div class="invalid-feedback" id="f_image-error"></div>@endunless
                @if($banner->image)
                <div class="form-hint">Để trống nếu không muốn thay ảnh hiện tại.</div>
                @endif
                <input type="hidden" id="existingImageUrl" value="{{ $banner->image }}">
            </div>

            <div class="form-group">
                <label>Đường dẫn khi bấm vào banner / nút bấm</label>
                <input type="text" name="button_link" id="f_link" value="{{ old('button_link', $banner->button_link) }}"
                    class="form-control @error('button_link') is-invalid @enderror" placeholder="VD: /san-pham/abc, /khuyen-mai, https://...">
                @error('button_link')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-hint">Có thể dẫn tới sản phẩm, danh mục, trang khuyến mãi, hoặc bất kỳ URL nào — không bắt buộc.</div>
            </div>

            <div class="section-divider">
                <div class="section-label">Hiển thị</div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Thứ tự hiển thị</label>
                    <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $banner->sort_order) }}"
                        class="form-control @error('sort_order') is-invalid @enderror" placeholder="0">
                    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Trạng thái</label>
                    <div class="toggle-wrap" style="margin-top:8px">
                        <label class="toggle-input">
                            <input type="checkbox" name="is_active" value="1"
                                {{ old('is_active', $banner->exists ? $banner->is_active : true) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                        <span>Hiển thị trên trang chủ</span>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ $banner->exists ? 'Lưu thay đổi' : 'Thêm banner' }}
                </button>
                <a href="{{ route('admin.banners.index') }}" class="btn btn-outline">Hủy</a>
            </div>
        </div>

        {{-- PREVIEW --}}
        <div class="preview-card">
            <div class="preview-label">Xem trước</div>
            <div class="preview-box" id="previewBox"></div>
            <div class="form-hint" style="margin-top:10px">Xem trước mang tính minh hoạ, hiển thị thật có thể khác đôi chút theo từng thiết bị.</div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
    const existingImage = document.getElementById('existingImageUrl').value || null;
    let currentImageDataUrl = existingImage || null;

    function getLayout() {
        return document.querySelector('input[name=layout]:checked')?.value || 'split';
    }

    function syncLayoutUI() {
        const layout = getLayout();
        document.querySelectorAll('.layout-option').forEach(opt => {
            opt.classList.toggle('active', opt.dataset.layoutOption === layout);
        });
        document.getElementById('textFields').classList.toggle('show', layout === 'split');
        updatePreview();
    }

    document.querySelectorAll('input[name=layout]').forEach(r => r.addEventListener('change', syncLayoutUI));

    // Đồng bộ color picker <-> input text
    function bindColor(pickerId, textId) {
        const picker = document.getElementById(pickerId);
        const text = document.getElementById(textId);
        picker.addEventListener('input', () => {
            text.value = picker.value;
            updatePreview();
        });
        text.addEventListener('input', updatePreview);
    }
    bindColor('f_bgcolor_picker', 'f_bgcolor');
    bindColor('f_textcolor_picker', 'f_textcolor');

    document.getElementById('f_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) {
            currentImageDataUrl = existingImage;
            updatePreview();
            return;
        }
        const reader = new FileReader();
        reader.onload = ev => {
            currentImageDataUrl = ev.target.result;
            updatePreview();
        };
        reader.readAsDataURL(file);
    });

    ['f_label', 'f_title', 'f_desc', 'f_price', 'f_btntext'].forEach(id => {
        document.getElementById(id)?.addEventListener('input', updatePreview);
    });

    function esc(s) {
        return (s || '').replace(/[&<>"']/g, c => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        } [c]));
    }

    function updatePreview() {
        const layout = getLayout();
        const box = document.getElementById('previewBox');
        const imgHtml = currentImageDataUrl ?
            `<img src="${currentImageDataUrl}">` :
            `<div class="preview-placeholder"><i class="fas fa-image fa-2x"></i><br>Chưa có ảnh</div>`;

        if (layout === 'image') {
            box.innerHTML = `<div class="preview-img-full">${imgHtml}</div>`;
            return;
        }

        const bg = document.getElementById('f_bgcolor').value || '#EBF3FF';
        const color = document.getElementById('f_textcolor').value || '#0D1B2A';
        const label = esc(document.getElementById('f_label').value);
        const title = esc(document.getElementById('f_title').value) || '<span style="opacity:.4">(Chưa có tiêu đề)</span>';
        const desc = esc(document.getElementById('f_desc').value);
        const price = esc(document.getElementById('f_price').value);
        const btn = esc(document.getElementById('f_btntext').value);

        box.innerHTML = `
        <div class="preview-split">
            <div class="preview-content" style="background:${bg};color:${color}">
                ${label ? `<div class="preview-label-text">${label}</div>` : ''}
                <div class="preview-title-text">${title}</div>
                ${desc ? `<div class="preview-desc-text">${desc}</div>` : ''}
                ${price ? `<div class="preview-price-text">${price}</div>` : ''}
                ${btn ? `<div class="preview-btn">${btn}</div>` : ''}
            </div>
            <div class="preview-img-side">${imgHtml}</div>
        </div>
    `;
    }

    syncLayoutUI();

    // ── Validate form ─────────────────────────────────────────────
    (function() {
        const form = document.getElementById('bannerForm');
        const existingImageVal = document.getElementById('existingImageUrl').value;

        function setError(inputId, message) {
            const input = document.getElementById(inputId);
            const errorDiv = document.getElementById(inputId + '-error');
            if (input) input.classList.add('is-invalid');
            if (errorDiv) {
                errorDiv.textContent = message;
                errorDiv.style.display = 'block';
            }
        }

        function clearError(inputId) {
            const input = document.getElementById(inputId);
            const errorDiv = document.getElementById(inputId + '-error');
            if (input) input.classList.remove('is-invalid');
            if (errorDiv) {
                errorDiv.textContent = '';
                errorDiv.style.display = 'none';
            }
        }

        document.getElementById('f_title')?.addEventListener('input', () => clearError('f_title'));
        document.getElementById('f_image')?.addEventListener('change', () => clearError('f_image'));

        form.addEventListener('submit', function(e) {
            let isValid = true;
            const layout = getLayout();

            if (layout === 'split') {
                // Tiêu đề bắt buộc khi dạng split
                const title = document.getElementById('f_title');
                if (!title || !title.value.trim()) {
                    setError('f_title', 'Vui lòng nhập tiêu đề banner.');
                    isValid = false;
                } else {
                    clearError('f_title');
                }
            } else {
                clearError('f_title');
            }

            // Ảnh bắt buộc khi thêm mới (chưa có ảnh cũ)
            const imageInput = document.getElementById('f_image');
            if (!existingImageVal && (!imageInput || !imageInput.files.length)) {
                setError('f_image', 'Vui lòng chọn ảnh cho banner.');
                isValid = false;
            } else {
                clearError('f_image');
            }

            if (!isValid) e.preventDefault();
        });
    })();
</script>
@endpush