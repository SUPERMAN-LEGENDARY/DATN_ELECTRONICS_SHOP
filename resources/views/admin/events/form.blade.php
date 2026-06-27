@extends('layouts.admin')

@section('title', $event->exists ? 'Sửa sự kiện' : 'Thêm sự kiện')

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
        margin-bottom: 6px;
        color: #333;
    }

    .form-hint {
        font-size: 12px;
        color: #999;
        margin-top: 4px;
    }

    .form-control {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 13px;
        font-family: inherit;
    }

    .form-control:focus {
        outline: none;
        border-color: #1565C0;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 80px;
    }

    .is-invalid {
        border-color: #C62828 !important;
    }

    .invalid-feedback {
        color: #C62828;
        font-size: 12px;
        margin-top: 4px;
    }

    input[type="color"].form-control {
        padding: 2px;
        width: 44px;
        height: 36px;
        flex-shrink: 0;
    }

    .color-row {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .color-row input[type="text"] {
        flex: 1;
    }

    .switch-row {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
        flex-shrink: 0;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

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
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background: #fff;
        border-radius: 50%;
        transition: .2s;
    }

    .switch input:checked+.slider {
        background: #2E7D32;
    }

    .switch input:checked+.slider:before {
        transform: translateX(20px);
    }

    .image-upload {
        border: 2px dashed #ddd;
        border-radius: 10px;
        padding: 18px;
        text-align: center;
        cursor: pointer;
        position: relative;
    }

    .image-upload.is-invalid {
        border-color: #C62828;
    }

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
        font-size: 28px;
        color: #bbb;
        margin-bottom: 8px;
        display: block;
    }

    .image-upload .text {
        font-size: 13px;
        color: #888;
    }

    .preview-card {
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .07);
        margin-bottom: 18px;
    }

    .preview-card h4 {
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 12px;
        color: #555;
    }

    .preview-event {
        border-radius: 10px;
        overflow: hidden;
        min-height: 140px;
        display: flex;
        align-items: center;
        background: #263238;
        color: #fff;
        position: relative;
        padding: 18px;
    }

    .preview-event img.bg {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: .35;
    }

    .preview-event .content {
        position: relative;
        z-index: 1;
    }

    .preview-event .tag {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        opacity: .85;
        margin-bottom: 4px;
    }

    .preview-event .title {
        font-size: 18px;
        font-weight: 800;
        margin-bottom: 4px;
    }

    .preview-event .offer {
        font-size: 13px;
        font-weight: 700;
        color: #FFD54F;
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
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        text-decoration: none;
        transition: .15s;
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
</style>
@endpush

@section('content')

<div class="breadcrumb">
    <a href="{{ route('admin.events.index') }}">Sự kiện / Khuyến mãi</a> /
    {{ $event->exists ? 'Sửa sự kiện' : 'Thêm sự kiện' }}
</div>

<form method="POST" action="{{ $event->exists ? route('admin.events.update', $event) : route('admin.events.store') }}" enctype="multipart/form-data" id="eventForm">
    @csrf
    @if($event->exists) @method('PUT') @endif

    <div class="layout-wrap">
        {{-- CỘT TRÁI: thông tin --}}
        <div class="form-card">
            <div class="form-title"><i class="fas fa-gift"></i> Thông tin sự kiện</div>
            <div class="form-subtitle">Ví dụ: Giáng Sinh, Tết Nguyên Đán, Black Friday, Sinh nhật shop...</div>

            <div class="form-group">
                <label>Tên sự kiện <span style="color:#C62828">*</span></label>
                <input type="text" name="title" id="f_title" value="{{ old('title', $event->title) }}"
                    class="form-control @error('title') is-invalid @enderror"
                    placeholder="Vd: Giáng Sinh An Lành 2026" oninput="updatePreview()">
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Nhãn nhỏ (tag)</label>
                    <input type="text" name="tag" id="f_tag" value="{{ old('tag', $event->tag) }}"
                        class="form-control @error('tag') is-invalid @enderror"
                        placeholder="Vd: ƯU ĐÃI ĐẶC BIỆT" oninput="updatePreview()">
                    @error('tag')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Ưu đãi nổi bật</label>
                    <input type="text" name="offer_text" id="f_offer" value="{{ old('offer_text', $event->offer_text) }}"
                        class="form-control @error('offer_text') is-invalid @enderror"
                        placeholder="Vd: Giảm đến 50%" oninput="updatePreview()">
                    @error('offer_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label>Mô tả chi tiết</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                    placeholder="Mô tả thêm về sự kiện, điều kiện áp dụng...">{{ old('description', $event->description) }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Ngày bắt đầu <span style="color:#C62828">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date', $event->start_date?->format('Y-m-d')) }}"
                        class="form-control @error('start_date') is-invalid @enderror">
                    @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Ngày kết thúc <span style="color:#C62828">*</span></label>
                    <input type="date" name="end_date" value="{{ old('end_date', $event->end_date?->format('Y-m-d')) }}"
                        class="form-control @error('end_date') is-invalid @enderror">
                    @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Văn bản nút bấm</label>
                    <input type="text" name="button_text" value="{{ old('button_text', $event->button_text) }}"
                        class="form-control @error('button_text') is-invalid @enderror"
                        placeholder="Vd: Mua sắm ngay">
                    @error('button_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Đường dẫn nút bấm</label>
                    <input type="text" name="button_link" value="{{ old('button_link', $event->button_link) }}"
                        class="form-control @error('button_link') is-invalid @enderror"
                        placeholder="Vd: /san-pham?category=khuyen-mai">
                    @error('button_link')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Màu nền thẻ sự kiện</label>
                    <div class="color-row">
                        <input type="color" class="form-control" id="bg_color_picker"
                            value="{{ old('bg_color', $event->bg_color ?: '#C62828') }}"
                            oninput="document.getElementById('bg_color_text').value=this.value; updatePreview()">
                        <input type="text" name="bg_color" id="bg_color_text" value="{{ old('bg_color', $event->bg_color) }}"
                            class="form-control @error('bg_color') is-invalid @enderror"
                            placeholder="#C62828"
                            oninput="document.getElementById('bg_color_picker').value=this.value||'#C62828'; updatePreview()">
                    </div>
                    <div class="form-hint">Định dạng mã hex, vd: #C62828.</div>
                    @error('bg_color')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Màu chữ</label>
                    <div class="color-row">
                        <input type="color" class="form-control" id="text_color_picker"
                            value="{{ old('text_color', $event->text_color ?: '#FFFFFF') }}"
                            oninput="document.getElementById('text_color_text').value=this.value; updatePreview()">
                        <input type="text" name="text_color" id="text_color_text" value="{{ old('text_color', $event->text_color) }}"
                            class="form-control @error('text_color') is-invalid @enderror"
                            placeholder="#FFFFFF"
                            oninput="document.getElementById('text_color_picker').value=this.value||'#ffffff'; updatePreview()">
                    </div>
                    <div class="form-hint">Định dạng mã hex, vd: #FFFFFF.</div>
                    @error('text_color')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Thứ tự hiển thị</label>
                    <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $event->sort_order ?? 0) }}"
                        class="form-control @error('sort_order') is-invalid @enderror">
                    <div class="form-hint">Số nhỏ hơn hiển thị trước.</div>
                    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Trạng thái</label>
                    <div class="switch-row" style="margin-top:8px">
                        <label class="switch">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $event->is_active ?? true) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                        <span>Hiển thị trên trang chủ</span>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ $event->exists ? 'Cập nhật' : 'Thêm sự kiện' }}
                </button>
                <a href="{{ route('admin.events.index') }}" class="btn btn-outline">Hủy</a>
            </div>
        </div>

        {{-- CỘT PHẢI: ảnh + xem trước --}}
        <div>
            <div class="preview-card">
                <h4>ẢNH SỰ KIỆN</h4>
                <label class="image-upload @error('image') is-invalid @enderror" id="imageDropzone">
                    <input type="file" name="image" accept="image/*" onchange="previewImage(this)">
                    @if($event->image)
                    <img src="{{ $event->image }}" id="previewImg">
                    @else
                    <i class="fas fa-cloud-upload-alt"></i>
                    <img id="previewImg" style="display:none">
                    @endif
                    <div class="text">Bấm để chọn ảnh (khuyến nghị tỉ lệ ngang)</div>
                </label>
                @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="preview-card">
                <h4>XEM TRƯỚC TRÊN TRANG CHỦ</h4>
                <div class="preview-event" id="previewEvent" style="background:{{ $event->bg_color ?: '#C62828' }};color:{{ $event->text_color ?: '#FFFFFF' }}">
                    @if($event->image)<img src="{{ $event->image }}" class="bg" id="previewBg">@else<img class="bg" id="previewBg" style="display:none">@endif
                    <div class="content">
                        <div class="tag" id="previewTag">{{ $event->tag ?: 'NHÃN SỰ KIỆN' }}</div>
                        <div class="title" id="previewTitle">{{ $event->title ?: 'Tên sự kiện' }}</div>
                        <div class="offer" id="previewOffer">{{ $event->offer_text ?: 'Ưu đãi nổi bật' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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

    function updatePreview() {
        document.getElementById('previewTag').textContent = document.getElementById('f_tag').value || 'NHÃN SỰ KIỆN';
        document.getElementById('previewTitle').textContent = document.getElementById('f_title').value || 'Tên sự kiện';
        document.getElementById('previewOffer').textContent = document.getElementById('f_offer').value || 'Ưu đãi nổi bật';
        document.getElementById('previewEvent').style.background = document.getElementById('bg_color_text').value || '#C62828';
        document.getElementById('previewEvent').style.color = document.getElementById('text_color_text').value || '#FFFFFF';
    }
</script>

@endsection