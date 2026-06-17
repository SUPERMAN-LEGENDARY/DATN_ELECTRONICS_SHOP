@extends('layouts.admin')
@section('title', isset($product) ? 'Sửa sản phẩm' : 'Thêm sản phẩm mới')

@push('styles')
<style>
.form-wrap { max-width: 900px; }
.form-section { background:#fff; border:1px solid #e0e0e0; border-radius:8px; padding:20px; margin-bottom:20px; }
.form-section h3 { font-size:14px; font-weight:800; text-transform:uppercase; letter-spacing:.5px; color:#1565C0; margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid #f0f0f0; }
.form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
.form-row.three { grid-template-columns:1fr 1fr 1fr; }
.form-group { margin-bottom:14px; }
.form-group label { display:block; font-size:13px; font-weight:600; color:#555; margin-bottom:6px; }
.form-group input[type=text],
.form-group input[type=number],
.form-group select,
.form-group textarea { width:100%; border:1px solid #e0e0e0; border-radius:6px; padding:9px 12px; font-size:13px; outline:none; font-family:inherit; box-sizing:border-box; transition:border-color .15s; }
.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus { border-color:#1565C0; }
.form-group .error { color:#E53935; font-size:12px; margin-top:4px; }
.toggle-check { display:flex; align-items:center; gap:8px; }
.toggle-check input { width:18px; height:18px; cursor:pointer; accent-color:#1565C0; }
.image-preview-row { display:flex; gap:10px; flex-wrap:wrap; margin-top:8px; }
.image-preview-row img { width:80px; height:80px; object-fit:cover; border-radius:6px; border:1px solid #e0e0e0; }
.attr-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
.btn-back { color:#666; text-decoration:none; font-size:13px; display:inline-flex; align-items:center; gap:4px; margin-bottom:16px; }
.btn-back:hover { color:#1565C0; }
.action-row { display:flex; gap:12px; margin-top:8px; }
.btn-save { background:#1565C0; color:#fff; border:none; padding:11px 28px; border-radius:6px; font-size:14px; font-weight:700; cursor:pointer; }
.btn-save:hover { background:#0D47A1; }
.btn-cancel { background:#fff; color:#666; border:1px solid #ddd; padding:11px 20px; border-radius:6px; font-size:14px; text-decoration:none; }
</style>
@endpush

@section('content')
<a href="{{ route('admin.products.index') }}" class="btn-back">← Quay lại danh sách</a>

<div class="admin-header" style="margin-bottom:16px">
    <h1 style="font-size:20px;font-weight:800">
        {{ isset($product) ? 'Sửa: ' . Str::limit($product->name, 40) : 'Thêm sản phẩm mới' }}
    </h1>
</div>

@if($errors->any())
<div style="background:#FFEBEE;border:1px solid #FFCDD2;color:#C62828;padding:12px 16px;border-radius:6px;margin-bottom:16px;font-size:13px">
    <strong>Vui lòng kiểm tra lại:</strong>
    <ul style="margin:6px 0 0 16px">
        @foreach($errors->all() as $err)
        <li>{{ $err }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ isset($product) ? route('admin.products.update', $product) : route('admin.products.store') }}"
      method="POST" enctype="multipart/form-data" class="form-wrap">
    @csrf
    @if(isset($product)) @method('PUT') @endif

    {{-- Thông tin cơ bản --}}
    <div class="form-section">
        <h3>Thông tin cơ bản</h3>

        <div class="form-group">
            <label>Tên sản phẩm <span style="color:#E53935">*</span></label>
            <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}"
                   placeholder="VD: iPhone 15 Pro Max 256GB Titan Tự Nhiên">
            @error('name')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Danh mục <span style="color:#E53935">*</span></label>
                <select name="category_id">
                    <option value="">-- Chọn danh mục --</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}"
                            {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                    @endforeach
                </select>
                @error('category_id')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Thương hiệu <span style="color:#E53935">*</span></label>
                <select name="brand_id">
                    <option value="">-- Chọn thương hiệu --</option>
                    @foreach($brands as $brand)
                    <option value="{{ $brand->id }}"
                            {{ old('brand_id', $product->brand_id ?? '') == $brand->id ? 'selected' : '' }}>
                        {{ $brand->name }}
                    </option>
                    @endforeach
                </select>
                @error('brand_id')<div class="error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-group">
            <label>Mô tả sản phẩm</label>
            <textarea name="description" rows="5"
                      placeholder="Nhập mô tả chi tiết sản phẩm...">{{ old('description', $product->description ?? '') }}</textarea>
        </div>
    </div>

    {{-- Giá & Tồn kho --}}
    <div class="form-section">
        <h3>Giá & Tồn kho</h3>
        <div class="form-row three">
            <div class="form-group">
                <label>Giá gốc (đ) <span style="color:#E53935">*</span></label>
                <input type="number" name="price" value="{{ old('price', $product->price ?? '') }}"
                       placeholder="29990000" min="0">
                @error('price')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Giảm giá (%)</label>
                <input type="number" name="discount_percent"
                       value="{{ old('discount_percent', $product->discount_percent ?? 0) }}"
                       min="0" max="100">
                @error('discount_percent')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Tồn kho</label>
                <input type="number" name="stock"
                       value="{{ old('stock', $product->stock ?? 0) }}" min="0">
            </div>
        </div>
        <div class="form-group">
            <div class="toggle-check">
                <input type="checkbox" id="is_active" name="is_active" value="1"
                       {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
                <label for="is_active" style="margin:0;font-size:13px;font-weight:600;cursor:pointer">
                    Hiển thị sản phẩm trên cửa hàng
                </label>
            </div>
        </div>
    </div>

    {{-- Hình ảnh --}}
    <div class="form-section">
        <h3>Hình ảnh sản phẩm</h3>
        <div class="form-group">
            <label>Tải ảnh lên (tối đa 6 ảnh, mỗi ảnh ≤ 3MB)</label>
            <input type="file" name="images[]" multiple accept="image/*" id="imageInput">
            @error('images.*')<div class="error">{{ $message }}</div>@enderror
        </div>
        {{-- Preview ảnh hiện có --}}
        @if(isset($product) && !empty($product->images))
        <div class="image-preview-row" id="existingImages">
            @foreach($product->images as $img)
            <img src="{{ $img }}" alt="">
            @endforeach
        </div>
        <p style="font-size:12px;color:#888;margin-top:6px">
            Tải ảnh mới sẽ thay thế toàn bộ ảnh cũ.
        </p>
        @endif
        {{-- Preview ảnh mới chọn --}}
        <div class="image-preview-row" id="newImagePreview"></div>
    </div>

    {{-- Thuộc tính kỹ thuật --}}
    <div class="form-section">
        <h3>Thông số kỹ thuật</h3>
        <div class="attr-grid">
            @foreach($attributes as $attr)
            <div class="form-group">
                <label>{{ $attr->name }}</label>
                <input type="text"
                       name="attributes[{{ $attr->id }}]"
                       value="{{ old('attributes.'.$attr->id, $productAttrs[$attr->id]->value ?? '') }}"
                       placeholder="Nhập {{ strtolower($attr->name) }}...">
            </div>
            @endforeach
        </div>
        @if($attributes->isEmpty())
        <p style="color:#aaa;font-size:13px">Chưa có thuộc tính nào. Thêm trong trang quản lý Thuộc tính.</p>
        @endif
    </div>

    {{-- Actions --}}
    <div class="action-row">
        <button type="submit" class="btn-save">
            <i class="fas fa-save"></i>
            {{ isset($product) ? 'Cập nhật sản phẩm' : 'Thêm sản phẩm' }}
        </button>
        <a href="{{ route('admin.products.index') }}" class="btn-cancel">Hủy</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
// Preview ảnh khi chọn
document.getElementById('imageInput').addEventListener('change', function() {
    const preview = document.getElementById('newImagePreview');
    preview.innerHTML = '';
    Array.from(this.files).slice(0, 6).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.cssText = 'width:80px;height:80px;object-fit:cover;border-radius:6px;border:1px solid #1565C0';
            preview.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endpush
