@extends('layouts.admin')

@section('title', isset($category) ? 'Sửa ' . ($type === 'brand' ? 'thương hiệu' : 'danh mục')
: 'Thêm ' . ($type === 'brand' ? 'thương hiệu' : 'danh mục'))

@push('styles')
<style>
    .form-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .08);
        max-width: 600px;
        padding: 28px;
    }

    .breadcrumb {
        font-size: 13px;
        color: #888;
        margin-bottom: 18px;
    }

    .breadcrumb a {
        color: #1565C0;
        text-decoration: none;
    }

    .breadcrumb a:hover {
        text-decoration: underline;
    }

    .form-title {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 24px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #444;
        margin-bottom: 6px;
    }

    .form-group label .req {
        color: #C62828;
        margin-left: 2px;
    }

    .form-control {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        transition: .15s;
    }

    .form-control:focus {
        outline: none;
        border-color: #1565C0;
        box-shadow: 0 0 0 3px rgba(21, 101, 192, .1);
    }

    .form-control.is-invalid {
        border-color: #C62828;
    }

    .invalid-feedback {
        color: #C62828;
        font-size: 12px;
        margin-top: 4px;
    }

    /* Logo preview */
    .logo-preview-wrap {
        margin-top: 10px;
    }

    .logo-preview {
        width: 80px;
        height: 80px;
        object-fit: contain;
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 4px;
        display: block;
    }

    .logo-hint {
        font-size: 12px;
        color: #999;
        margin-top: 6px;
    }

    /* Toggle switch */
    .toggle-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .toggle-input {
        position: relative;
        width: 44px;
        height: 24px;
    }

    .toggle-input input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        inset: 0;
        background: #ccc;
        border-radius: 12px;
        cursor: pointer;
        transition: .2s;
    }

    .toggle-slider:before {
        content: '';
        position: absolute;
        width: 18px;
        height: 18px;
        left: 3px;
        top: 3px;
        background: #fff;
        border-radius: 50%;
        transition: .2s;
    }

    .toggle-input input:checked+.toggle-slider {
        background: #1565C0;
    }

    .toggle-input input:checked+.toggle-slider:before {
        transform: translateX(20px);
    }

    .toggle-label {
        font-size: 13px;
        color: #555;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 28px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 20px;
        border-radius: 6px;
        font-size: 14px;
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

{{-- Breadcrumb --}}
<div class="breadcrumb">
    <a href="{{ route('admin.categories.index', ['type' => $type]) }}">
        {{ $type === 'brand' ? 'Thương hiệu' : 'Danh mục' }}
    </a>
    &rsaquo;
    {{ isset($category) ? 'Sửa: '.$category->name : 'Thêm mới' }}
</div>

<div class="form-card">
    <div class="form-title">
        <i class="fas fa-{{ isset($category) ? 'edit' : 'plus-circle' }}"></i>
        {{ isset($category) ? 'Sửa' : 'Thêm' }} {{ $type === 'brand' ? 'thương hiệu' : 'danh mục' }}
    </div>

    <form method="POST"
        action="{{ isset($category)
                    ? route('admin.categories.update', $category)
                    : route('admin.categories.store') }}"
        enctype="multipart/form-data" id="categoryForm" novalidate>
        @csrf
        @if(isset($category)) @method('PUT') @endif
        <input type="hidden" name="type" value="{{ $type }}">

        {{-- Tên --}}
        <div class="form-group">
            <label>Tên {{ $type === 'brand' ? 'thương hiệu' : 'danh mục' }} <span class="req">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name', $category->name ?? '') }}"
                class="form-control @error('name') is-invalid @enderror"
                placeholder="Ví dụ: {{ $type === 'brand' ? 'Samsung' : 'Điện thoại' }}">
            <div class="invalid-feedback" id="name-error">@error('name'){{ $message }}@enderror</div>
        </div>

        {{-- Logo --}}
        <div class="form-group">
            <label>Logo <span class="logo-hint" style="font-weight:400">(JPG, PNG, SVG — tối đa 2MB)</span></label>
            <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror"
                accept="image/*" onchange="previewLogo(this)">
            @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror

            <div class="logo-preview-wrap">
                @if(isset($category) && $category->logo)
                <img id="logo-preview" src="{{ asset('storage/'.$category->logo) }}"
                    class="logo-preview" alt="Logo hiện tại">
                <div class="logo-hint">Logo hiện tại — chọn file mới để thay thế</div>
                @else
                <img id="logo-preview" src="" class="logo-preview" alt="" style="display:none">
                @endif
            </div>
        </div>

        {{-- Trạng thái --}}
        <div class="form-group">
            <label>Trạng thái</label>
            <div class="toggle-wrap">
                <label class="toggle-input">
                    <input type="checkbox" name="is_active" value="1"
                        {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}>
                    <span class="toggle-slider"></span>
                </label>
                <span class="toggle-label">Kích hoạt (hiển thị trên cửa hàng)</span>
            </div>
        </div>

        {{-- Nút --}}
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                {{ isset($category) ? 'Cập nhật' : 'Lưu mới' }}
            </button>
            <a href="{{ route('admin.categories.index', ['type' => $type]) }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
    function previewLogo(input) {
        const img = document.getElementById('logo-preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                img.src = e.target.result;
                img.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // ── Validate form ─────────────────────────────────────────────────
    (function() {
        const form = document.getElementById('categoryForm');

        function setError(inputId, message) {
            const input = document.getElementById(inputId);
            const errorDiv = document.getElementById(inputId + '-error');
            if (input) input.classList.add('is-invalid');
            if (errorDiv) errorDiv.textContent = message;
        }

        function clearError(inputId) {
            const input = document.getElementById(inputId);
            const errorDiv = document.getElementById(inputId + '-error');
            if (input) input.classList.remove('is-invalid');
            if (errorDiv) errorDiv.textContent = '';
        }

        document.getElementById('name')?.addEventListener('input', () => clearError('name'));

        form.addEventListener('submit', function(e) {
            let isValid = true;
            const name = document.getElementById('name');
            if (!name || !name.value.trim()) {
                setError('name', 'Vui lòng nhập tên.');
                isValid = false;
            } else {
                clearError('name');
            }
            if (!isValid) e.preventDefault();
        });
    })();
</script>
@endpush