@extends('layouts.admin')
@section('title', isset($product) ? 'Sửa sản phẩm' : 'Thêm sản phẩm mới')

@push('styles')
<style>
.form-wrap { max-width: 900px; }
.form-section { background:#fff; border:1px solid #e0e0e0; border-radius:8px; padding:20px; margin-bottom:20px; }
.form-section h3 { font-size:14px; font-weight:800; text-transform:uppercase; letter-spacing:.5px; color:#1565C0; margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid #f0f0f0; display:flex; align-items:center; gap:10px; }
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
.attr-field-row { display:flex; align-items:center; justify-content:space-between; gap:6px; }
.btn-field-remove { background:none; border:none; color:#ccc; cursor:pointer; font-size:12px; padding:0 2px; line-height:1; flex-shrink:0; }
.btn-field-remove:hover { color:#E53935; }
.removed-chip-row { display:flex; flex-wrap:wrap; gap:6px; margin-top:10px; }
.removed-chip { display:inline-flex; align-items:center; gap:6px; background:#F5F5F5; border:1px dashed #ccc; color:#888; font-size:12px; padding:4px 10px; border-radius:14px; cursor:pointer; }
.removed-chip:hover { background:#E3F2FD; border-color:#1565C0; color:#1565C0; }
.removed-chip i { font-size:10px; }
.btn-back { color:#666; text-decoration:none; font-size:13px; display:inline-flex; align-items:center; gap:4px; margin-bottom:16px; }
.btn-back:hover { color:#1565C0; }
.action-row { display:flex; gap:12px; margin-top:8px; }
.btn-save { background:#1565C0; color:#fff; border:none; padding:11px 28px; border-radius:6px; font-size:14px; font-weight:700; cursor:pointer; }
.btn-save:hover { background:#0D47A1; }
.btn-cancel { background:#fff; color:#666; border:1px solid #ddd; padding:11px 20px; border-radius:6px; font-size:14px; text-decoration:none; }
.btn-manage-attr { background:none; border:1px solid #1565C0; color:#1565C0; padding:5px 12px; border-radius:5px; font-size:12px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:5px; }
.btn-manage-attr:hover { background:#E3F2FD; }
/* ── Modal ── */
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:1000; align-items:center; justify-content:center; }
.modal-overlay.open { display:flex; }
.modal-box { background:#fff; border-radius:10px; width:480px; max-width:95vw; max-height:85vh; display:flex; flex-direction:column; box-shadow:0 8px 32px rgba(0,0,0,.18); }
.modal-header { padding:16px 20px; border-bottom:1px solid #f0f0f0; display:flex; align-items:center; justify-content:space-between; }
.modal-header h3 { font-size:15px; font-weight:800; margin:0; }
.modal-close { background:none; border:none; font-size:20px; cursor:pointer; color:#888; line-height:1; }
.modal-close:hover { color:#333; }
.modal-body { padding:16px 20px; overflow-y:auto; flex:1; }
.modal-add-row { display:flex; gap:8px; margin-bottom:16px; }
.modal-add-row input { flex:1; border:1px solid #e0e0e0; border-radius:6px; padding:8px 12px; font-size:13px; outline:none; }
.modal-add-row input:focus { border-color:#1565C0; }
.btn-modal-add { background:#1565C0; color:#fff; border:none; padding:8px 16px; border-radius:6px; font-size:13px; font-weight:700; cursor:pointer; white-space:nowrap; }
.btn-modal-add:hover { background:#0D47A1; }
.modal-error { color:#E53935; font-size:12px; margin:-10px 0 10px; }
.attr-modal-list { list-style:none; margin:0; padding:0; }
.attr-modal-list li { display:flex; align-items:center; justify-content:space-between; padding:9px 0; border-bottom:1px solid #f5f5f5; font-size:13px; }
.attr-modal-list li:last-child { border-bottom:none; }
.attr-item-name { font-weight:600; flex:1; }
.attr-item-used { color:#aaa; font-size:11px; margin-left:8px; }
.btn-attr-del { background:none; border:none; color:#E53935; cursor:pointer; padding:3px 6px; border-radius:4px; font-size:13px; }
.btn-attr-del:hover { background:#FFEBEE; }
.btn-attr-del:disabled { color:#ccc; cursor:not-allowed; }
.btn-attr-del:disabled:hover { background:none; }
.modal-footer { padding:12px 20px; border-top:1px solid #f0f0f0; display:flex; justify-content:flex-end; }
.btn-modal-close { background:#fff; color:#666; border:1px solid #ddd; padding:8px 20px; border-radius:6px; font-size:13px; cursor:pointer; font-weight:600; }
.btn-modal-close:hover { background:#f5f5f5; }
.modal-loading { text-align:center; color:#aaa; padding:24px; font-size:13px; }

/* ── Variants ── */
.variant-toggle-bar {
    display: flex; align-items: center; justify-content: space-between;
    background: #F0F4FF; border: 1px solid #C5D5F5; border-radius: 8px;
    padding: 12px 16px; margin-bottom: 0; cursor: pointer;
    user-select: none;
}
.variant-toggle-bar .vtb-left { display:flex; align-items:center; gap:10px; }
.vtb-label { font-size:13px; font-weight:700; color:#1565C0; }
.vtb-desc  { font-size:12px; color:#888; margin-top:2px; }
.vtb-switch {
    position:relative; width:42px; height:24px; flex-shrink:0;
}
.vtb-switch input { opacity:0; width:0; height:0; }
.vtb-slider {
    position:absolute; inset:0; background:#ccc; border-radius:24px; transition:.2s;
}
.vtb-slider:before {
    content:''; position:absolute; height:18px; width:18px; left:3px; bottom:3px;
    background:#fff; border-radius:50%; transition:.2s;
}
.vtb-switch input:checked + .vtb-slider { background:#1565C0; }
.vtb-switch input:checked + .vtb-slider:before { transform:translateX(18px); }

#variantSection { margin-top:0; }

.variant-card {
    border: 1px solid #E0E0E0; border-radius: 8px; margin-bottom: 12px;
    overflow: hidden; background: #fff;
}
.variant-card-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 14px; background: #FAFAFA; border-bottom: 1px solid #F0F0F0;
    cursor: pointer;
}
.variant-card-head .vc-title { font-size:13px; font-weight:700; color:#333; }
.variant-card-head .vc-badge {
    font-size:11px; background:#E3F2FD; color:#1565C0; border-radius:12px;
    padding:2px 8px; font-weight:600;
}
.variant-card-head .vc-actions { display:flex; gap:8px; }
.btn-vc-remove {
    background:none; border:none; color:#E53935; cursor:pointer; font-size:13px;
    padding:4px 8px; border-radius:4px;
}
.btn-vc-remove:hover { background:#FFEBEE; }
.variant-card-body { padding:14px; }
.variant-attr-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:12px; }
.variant-image-block { padding-top:12px; margin-bottom:12px; border-top:1px dashed #E8E8E8; }
.variant-image-block .image-preview-row img { width:60px; height:60px; object-fit:cover; border-radius:6px; border:1px solid #1565C0; }
.variant-price-row {
    display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:10px;
    padding-top:12px; border-top:1px dashed #E8E8E8;
}
.variant-price-row .form-group { margin-bottom:0; }
.variant-price-row label { color:#1565C0; }

.btn-add-variant {
    width:100%; border:2px dashed #C5D5F5; background:#F8FAFF; color:#1565C0;
    border-radius:8px; padding:12px; font-size:13px; font-weight:700;
    cursor:pointer; display:flex; align-items:center; justify-content:center; gap:6px;
    transition: background .15s;
}
.btn-add-variant:hover { background:#EEF3FF; }
.variant-empty { text-align:center; color:#aaa; font-size:13px; padding:20px 0; }
</style>
@endpush

@section('content')

@php
    $savedAttrs    = isset($product) ? $product->attributes->keyBy('attribute_id') : collect();
    $savedVariants = isset($product) ? $product->variants()->with('variantAttributes')->get() : collect();
    $hasVariants   = $savedVariants->isNotEmpty();
@endphp

<a href="{{ route('admin.products.index') }}" class="btn-back">← Quay lại danh sách</a>

<div class="admin-header" style="margin-bottom:16px">
    <h1 style="font-size:20px;font-weight:800">
        {{ isset($product) ? 'Sửa: ' . Str::limit($product->name, 40) : 'Thêm sản phẩm mới' }}
    </h1>
</div>

<form action="{{ isset($product) ? route('admin.products.update', $product) : route('admin.products.store') }}"
    method="POST" enctype="multipart/form-data" class="form-wrap" id="productForm" novalidate>
    @csrf
    @if(isset($product)) @method('PUT') @endif

    {{-- Thông tin cơ bản --}}
    <div class="form-section">
        <h3>Thông tin cơ bản</h3>
        <div class="form-group">
            <label>Tên sản phẩm <span style="color:#E53935">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name', $product->name ?? '') }}"
                placeholder="VD: iPhone 15 Pro Max 256GB Titan Tự Nhiên">
            <div class="error" id="name-error">@error('name'){{ $message }}@enderror</div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Danh mục <span style="color:#E53935">*</span></label>
                <select name="category_id" id="category_id">
                    <option value="">-- Chọn danh mục --</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}"
                        {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                    @endforeach
                </select>
                <div class="error" id="category_id-error">@error('category_id'){{ $message }}@enderror</div>
            </div>
            <div class="form-group">
                <label>Thương hiệu <span style="color:#E53935">*</span></label>
                <select name="brand_id" id="brand_id">
                    <option value="">-- Chọn thương hiệu --</option>
                    @foreach($brands as $brand)
                    <option value="{{ $brand->id }}"
                        {{ old('brand_id', $product->brand_id ?? '') == $brand->id ? 'selected' : '' }}>
                        {{ $brand->name }}
                    </option>
                    @endforeach
                </select>
                <div class="error" id="brand_id-error">@error('brand_id'){{ $message }}@enderror</div>
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
                <input type="number" name="price" id="price" value="{{ old('price', $product->price ?? '') }}"
                    placeholder="29990000" min="0">
                <div class="error" id="price-error">@error('price'){{ $message }}@enderror</div>
            </div>
            <div class="form-group">
                <label>Giảm giá (%)</label>
                <input type="number" name="discount_percent"
                    value="{{ old('discount_percent', $product->discount_percent ?? 0) }}"
                    min="0" max="100">
                @error('discount_percent')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Số Lượng</label>
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

        {{-- Ảnh đại diện --}}
        <div class="form-group">
            <label>Ảnh đại diện <span style="color:#888;font-weight:400">(hiển thị ở danh sách, giỏ hàng)</span></label>
            <input type="file" name="thumbnail" accept="image/*" id="thumbnailInput">
            @error('thumbnail')<div class="error">{{ $message }}</div>@enderror
            @if(isset($product) && $product->thumbnail)
            <div style="margin-top:8px">
                <p style="font-size:12px;color:#888;margin-bottom:4px">Ảnh đại diện hiện tại:</p>
                <img src="{{ $product->thumbnail }}" alt="" style="width:100px;height:100px;object-fit:cover;border-radius:6px;border:1px solid #e0e0e0">
                <p style="font-size:12px;color:#888;margin-top:4px">Tải ảnh mới sẽ thay thế ảnh cũ.</p>
            </div>
            @endif
            <div id="thumbnailPreview" style="margin-top:8px"></div>
        </div>

        <hr style="border:none;border-top:1px solid #f0f0f0;margin:16px 0">

        {{-- Album ảnh --}}
        <div class="form-group">
            <label>Album ảnh <span style="color:#888;font-weight:400">(tối đa 6 ảnh, hiển thị ở trang chi tiết)</span></label>
            <input type="file" name="images[]" multiple accept="image/*" id="imageInput">
            @error('images.*')<div class="error">{{ $message }}</div>@enderror
            @if(isset($product) && !empty($product->images))
            <div style="margin-top:8px">
                <p style="font-size:12px;color:#888;margin-bottom:4px">Album hiện tại:</p>
                <div class="image-preview-row">
                    @foreach($product->images as $img)
                    <img src="{{ $img }}" alt="">
                    @endforeach
                </div>
                <p style="font-size:12px;color:#888;margin-top:4px">Tải ảnh mới sẽ thay thế toàn bộ album cũ.</p>
            </div>
            @endif
            <div class="image-preview-row" id="newImagePreview"></div>
        </div>
    </div>

    {{-- Thông số kỹ thuật (ProductAttribute - flat, không giá) --}}
    <div class="form-section">
        <h3>
            Thông số kỹ thuật
            <button type="button" class="btn-manage-attr" id="btnOpenAttrModal">
                <i class="fas fa-cog"></i> Quản lý thuộc tính
            </button>
        </h3>

        <div class="attr-grid" id="attrGrid">
            @foreach($attributes as $attr)
            <div class="form-group" id="attr-field-{{ $attr->id }}">
                <label class="attr-field-row">
                    <span>{{ $attr->name }} @if($attr->is_variant)<em style="color:#1565C0;font-weight:600;font-style:normal">(chính)</em>@else<em style="color:#aaa;font-weight:400;font-style:normal">(phụ)</em>@endif</span>
                    <button type="button" class="btn-field-remove" data-id="{{ $attr->id }}" data-name="{{ $attr->name }}"
                        title="Sản phẩm này không cần thuộc tính này">
                        <i class="fas fa-times"></i>
                    </button>
                </label>
                <input type="text"
                    name="attributes[{{ $attr->id }}]"
                    value="{{ old('attributes.' . $attr->id, $savedAttrs[$attr->id]->value ?? '') }}"
                    placeholder="Nhập {{ strtolower($attr->name) }}...">
            </div>
            @endforeach
        </div>

        <p style="color:#999;font-size:12px;margin-top:8px">
            <i class="fas fa-circle-info"></i>
            Thuộc tính đánh dấu <strong>"chính"</strong> (xem trong "Quản lý thuộc tính") sẽ hiện thành nút chọn ở phần Biến thể bên dưới;
            thuộc tính <strong>"phụ"</strong> chỉ hiện trong bảng thông số này.
        </p>

        <div class="removed-chip-row" id="attrRemovedChips"></div>

        @if($attributes->isEmpty())
        <p style="color:#aaa;font-size:13px" id="attrEmptyMsg">
            Chưa có thuộc tính nào. Nhấn <strong>Quản lý thuộc tính</strong> để thêm.
        </p>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════════
         PHẦN BIẾN THỂ (VARIANTS) — có thể bật/tắt
    ═══════════════════════════════════════════════════ --}}
    <div class="form-section" style="padding-bottom:20px">

        {{-- Toggle bar --}}
        <label class="variant-toggle-bar" for="enableVariants" style="margin-bottom:0">
            <div class="vtb-left">
                <i class="fas fa-layer-group" style="color:#1565C0;font-size:15px"></i>
                <div>
                    <div class="vtb-label">Biến thể sản phẩm (Màu / RAM / ROM...)</div>
                    <div class="vtb-desc">Bật nếu sản phẩm có nhiều phiên bản với giá khác nhau</div>
                </div>
            </div>
            <label class="vtb-switch" onclick="event.stopPropagation()">
                <input type="checkbox" id="enableVariants" name="has_variants" value="1"
                       {{ $hasVariants || old('has_variants') ? 'checked' : '' }}>
                <span class="vtb-slider"></span>
            </label>
        </label>

        {{-- Nội dung variants (ẩn/hiện theo toggle) --}}
        <div id="variantSection" style="{{ $hasVariants || old('has_variants') ? '' : 'display:none' }};margin-top:16px">

            <div id="variantList">
                {{-- PHP: render lại các variant đã lưu (edit mode) --}}
                @foreach($savedVariants as $vi => $v)
                <div class="variant-card" id="vc-{{ $vi }}">
                    <div class="variant-card-head" onclick="toggleVariantCard({{ $vi }})">
                        <span class="vc-title">
                            <i class="fas fa-cubes" style="color:#1565C0;margin-right:4px"></i>
                            Biến thể #{{ $vi + 1 }}
                        </span>
                        <span class="vc-badge">{{ number_format($v->price) }}đ / {{ $v->stock }} cái</span>
                        <div class="vc-actions">
                            <button type="button" class="btn-vc-remove"
                                    onclick="event.stopPropagation(); removeVariant({{ $vi }})"
                                    title="Xóa biến thể">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="variant-card-body" id="vcb-{{ $vi }}">
                        {{-- Hidden ID để update --}}
                        <input type="hidden" name="variants[{{ $vi }}][id]" value="{{ $v->id }}">
                        {{-- Thuộc tính của biến thể --}}
                        <div class="variant-attr-grid">
                            @foreach($attributes->where('is_variant', true) as $attr)
                            @php $va = $v->variantAttributes->firstWhere('attribute_id', $attr->id); @endphp
                            <div class="form-group" id="vca-{{ $vi }}-{{ $attr->id }}">
                                <label class="attr-field-row">
                                    <span>{{ $attr->name }}</span>
                                    <button type="button" class="btn-field-remove" data-id="{{ $attr->id }}" data-name="{{ $attr->name }}"
                                        title="Biến thể này không cần thuộc tính này">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </label>
                                <input type="text"
                                       name="variants[{{ $vi }}][attrs][{{ $attr->id }}]"
                                       value="{{ old("variants.$vi.attrs.$attr->id", $va->value ?? '') }}"
                                       placeholder="{{ $attr->name }}...">
                            </div>
                            @endforeach
                        </div>
                        <div class="removed-chip-row" id="vcRemovedChips-{{ $vi }}"></div>

                        {{-- Ảnh riêng của biến thể (đại diện + album) --}}
                        <div class="variant-image-block">
                            <div class="form-row">
                                <div class="form-group">
                                    <label><i class="fas fa-image"></i> Ảnh đại diện biến thể
                                        <span style="color:#888;font-weight:400">(để trống sẽ dùng ảnh chung của sản phẩm)</span>
                                    </label>
                                    <input type="file" name="variants[{{ $vi }}][thumbnail]" accept="image/*"
                                           class="variant-thumb-input" data-vidx="{{ $vi }}">
                                    @if($v->thumbnail)
                                    <div style="margin-top:6px">
                                        <img src="{{ $v->thumbnail }}" alt="" style="width:60px;height:60px;object-fit:cover;border-radius:6px;border:1px solid #e0e0e0">
                                        <p style="font-size:11px;color:#888;margin-top:4px">Tải ảnh mới sẽ thay thế ảnh đại diện biến thể này.</p>
                                    </div>
                                    @endif
                                    <div class="image-preview-row" id="vThumbPreview-{{ $vi }}" style="margin-top:6px"></div>
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-images"></i> Album ảnh biến thể
                                        <span style="color:#888;font-weight:400">(tối đa 6 ảnh)</span>
                                    </label>
                                    <input type="file" name="variants[{{ $vi }}][images][]" multiple accept="image/*"
                                           class="variant-images-input" data-vidx="{{ $vi }}">
                                    @if(!empty($v->images))
                                    <div style="margin-top:6px">
                                        <div class="image-preview-row">
                                            @foreach($v->images as $img)
                                            <img src="{{ $img }}" alt="" style="width:60px;height:60px">
                                            @endforeach
                                        </div>
                                        <p style="font-size:11px;color:#888;margin-top:4px">Tải ảnh mới sẽ thay thế toàn bộ album ảnh biến thể này.</p>
                                    </div>
                                    @endif
                                    <div class="image-preview-row" id="vImagesPreview-{{ $vi }}" style="margin-top:6px"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Giá riêng của biến thể --}}
                        <div class="variant-price-row">
                            <div class="form-group">
                                <label><i class="fas fa-tag"></i> Giá (đ) *</label>
                                <input type="number" name="variants[{{ $vi }}][price]"
                                       value="{{ old("variants.$vi.price", $v->price) }}"
                                       placeholder="29990000" min="0" required>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-percent"></i> Giảm giá (%)</label>
                                <input type="number" name="variants[{{ $vi }}][discount_percent]"
                                       value="{{ old("variants.$vi.discount_percent", $v->discount_percent) }}"
                                       min="0" max="100">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-boxes"></i> Tồn kho</label>
                                <input type="number" name="variants[{{ $vi }}][stock]"
                                       value="{{ old("variants.$vi.stock", $v->stock) }}"
                                       min="0">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-eye"></i> Hiển thị</label>
                                <div class="toggle-check" style="margin-top:8px">
                                    <input type="checkbox" name="variants[{{ $vi }}][is_active]"
                                           value="1" id="va-{{ $vi }}"
                                           {{ $v->is_active ? 'checked' : '' }}>
                                    <label for="va-{{ $vi }}" style="margin:0;font-size:12px;cursor:pointer">Bật</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Thêm biến thể mới --}}
            <button type="button" class="btn-add-variant" id="btnAddVariant">
                <i class="fas fa-plus-circle"></i> Thêm biến thể
            </button>

            @if($savedVariants->isEmpty())
            <p class="variant-empty" id="variantEmptyMsg">
                Chưa có biến thể nào. Nhấn <strong>+ Thêm biến thể</strong> để bắt đầu.
            </p>
            @endif
        </div>

    </div>
    {{-- /variants --}}

    {{-- Actions --}}
    <div class="action-row">
        <button type="submit" class="btn-save">
            <i class="fas fa-save"></i>
            {{ isset($product) ? 'Cập nhật sản phẩm' : 'Thêm sản phẩm' }}
        </button>
        <a href="{{ route('admin.products.index') }}" class="btn-cancel">Hủy</a>
    </div>
</form>

{{-- Modal quản lý thuộc tính --}}
<div class="modal-overlay" id="attrModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fas fa-tags"></i> Quản lý thuộc tính</h3>
            <button class="modal-close" id="btnCloseModal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="modal-add-row">
                <input type="text" id="newAttrName" placeholder="VD: RAM, CPU, Màn hình, Pin...">
                <button type="button" class="btn-modal-add" id="btnAddAttr">
                    <i class="fas fa-plus"></i> Thêm
                </button>
            </div>
            <label style="display:flex;align-items:center;gap:6px;font-size:12.5px;color:#555;margin:-4px 0 10px">
                <input type="checkbox" id="newAttrIsVariant" checked style="cursor:pointer">
                Thuộc tính chính (tạo nút chọn ở phần Biến thể — VD: Màu sắc, Dung lượng)
            </label>
            <div class="modal-error" id="modalError" style="display:none"></div>
            <div id="modalAttrListWrap">
                <div class="modal-loading">Đang tải...</div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-modal-close" id="btnModalDone">Xong</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const ROUTES = {
        list: '{{ route("admin.attributes.list") }}',
        store: '{{ route("admin.attributes.store") }}',
        destroy: '{{ url("admin/thuoc-tinh") }}/__ID__',
        toggleVariant: '{{ url("admin/thuoc-tinh") }}/__ID__/toggle-variant',
    };

// ══════════════════════════════════════════════════════════════
// PHẦN BIẾN THỂ (VARIANTS)
// ══════════════════════════════════════════════════════════════

// Danh sách thuộc tính hiện tại (từ PHP → JS)
const ALL_ATTRIBUTES = @json($attributes->map(fn($a) => ['id' => $a->id, 'name' => $a->name, 'is_variant' => (bool) $a->is_variant]));

// Value đã lưu trong ProductAttribute — prefill cho biến thể mới
const SAVED_ATTR_VALUES = @json($savedAttrs->mapWithKeys(fn($pa, $attrId) => [(string)$attrId => $pa->value]));

// ── Theo dõi thuộc tính bị "x" (ẩn riêng cho sp/biến thể), để có thể thêm lại ──
const removedBaseAttrs    = {};  // { [attrId]: { name, value } }
const removedVariantAttrs = {};  // { [variantIdx]: { [attrId]: { name, value } } }

// Lấy value hiện tại của thuộc tính gốc (ưu tiên input đang gõ trên form,
// nếu trống thì lấy giá trị đã lưu trong DB) — dùng để prefill cho biến thể.
function getBaseAttrValue(id) {
    const input = document.querySelector(`#attrGrid input[name="attributes[${id}]"]`);
    if (input && input.value.trim() !== '') return input.value;
    return SAVED_ATTR_VALUES[String(id)] ?? '';
}

// Index tăng dần cho variant mới (tránh trùng với variant PHP đã render)
let variantIdx = {{ $savedVariants->count() }};

// Toggle bật/tắt phần variants
document.getElementById('enableVariants').addEventListener('change', function () {
    document.getElementById('variantSection').style.display = this.checked ? '' : 'none';
});

// Thêm biến thể mới
document.getElementById('btnAddVariant').addEventListener('click', addVariant);

function addVariant() {
    const idx  = variantIdx++;
    const card = document.createElement('div');
    card.className = 'variant-card';
    card.id = 'vc-' + idx;

    // Lấy sẵn giá / giảm giá / tồn kho / trạng thái từ form gốc của sản phẩm
    // để bê nguyên xuống biến thể mới — người dùng chỉ cần chỉnh ảnh (và sửa lại nếu cần)
    const basePrice    = document.getElementById('price')?.value || '';
    const baseDiscount = document.querySelector('[name="discount_percent"]')?.value || '0';
    const baseStock    = document.querySelector('[name="stock"]')?.value || '0';
    const baseIsActive = document.getElementById('is_active')?.checked ?? true;

    // Build các input thuộc tính — prefill value từ thuộc tính gốc của sản phẩm
    // Bỏ qua những thuộc tính đang bị ẩn ở khu vực base (removedBaseAttrs)
    const attrInputs = ALL_ATTRIBUTES.filter(a => !removedBaseAttrs[a.id] && a.is_variant).map(a => {
        const prefill = getBaseAttrValue(a.id);
        return `
        <div class="form-group" id="vca-${idx}-${a.id}">
            <label class="attr-field-row">
                <span>${esc(a.name)}</span>
                <button type="button" class="btn-field-remove" data-id="${a.id}" data-name="${esc(a.name)}"
                    title="Biến thể này không cần thuộc tính này">
                    <i class="fas fa-times"></i>
                </button>
            </label>
            <input type="text" name="variants[${idx}][attrs][${a.id}]"
                   value="${esc(prefill)}"
                   placeholder="${esc(a.name)}...">
        </div>`;
    }).join('');

    card.innerHTML = `
        <div class="variant-card-head" onclick="toggleVariantCard(${idx})">
            <span class="vc-title">
                <i class="fas fa-cubes" style="color:#1565C0;margin-right:4px"></i>
                Biến thể #${idx + 1}
            </span>
            <span class="vc-badge" id="vc-badge-${idx}">${basePrice ? parseInt(basePrice).toLocaleString('vi-VN') + 'đ' : 'Chưa nhập giá'}</span>
            <div class="vc-actions">
                <button type="button" class="btn-vc-remove"
                        onclick="event.stopPropagation(); removeVariant(${idx})"
                        title="Xóa biến thể">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <div class="variant-card-body" id="vcb-${idx}">
            <div class="variant-attr-grid">
                ${attrInputs || '<p style="color:#aaa;font-size:12px;grid-column:1/-1">Chưa có thuộc tính. Thêm trong <strong>Quản lý thuộc tính</strong>.</p>'}
            </div>
            <div class="removed-chip-row" id="vcRemovedChips-${idx}"></div>

            <div class="variant-image-block">
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-image"></i> Ảnh đại diện biến thể
                            <span style="color:#888;font-weight:400">(để trống sẽ dùng ảnh chung của sản phẩm)</span>
                        </label>
                        <input type="file" name="variants[${idx}][thumbnail]" accept="image/*"
                               class="variant-thumb-input" data-vidx="${idx}">
                        <div class="image-preview-row" id="vThumbPreview-${idx}" style="margin-top:6px"></div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-images"></i> Album ảnh biến thể
                            <span style="color:#888;font-weight:400">(tối đa 6 ảnh)</span>
                        </label>
                        <input type="file" name="variants[${idx}][images][]" multiple accept="image/*"
                               class="variant-images-input" data-vidx="${idx}">
                        <div class="image-preview-row" id="vImagesPreview-${idx}" style="margin-top:6px"></div>
                    </div>
                </div>
            </div>

            <div class="variant-price-row">
                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Giá (đ) *</label>
                    <input type="number" name="variants[${idx}][price]"
                           value="${esc(basePrice)}"
                           placeholder="29990000" min="0" required
                           oninput="updateVariantBadge(${idx})">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-percent"></i> Giảm giá (%)</label>
                    <input type="number" name="variants[${idx}][discount_percent]"
                           value="${esc(baseDiscount)}" min="0" max="100">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-boxes"></i> Tồn kho</label>
                    <input type="number" name="variants[${idx}][stock]"
                           value="${esc(baseStock)}" min="0">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-eye"></i> Hiển thị</label>
                    <div class="toggle-check" style="margin-top:8px">
                        <input type="checkbox" name="variants[${idx}][is_active]"
                               value="1" id="va-${idx}" ${baseIsActive ? 'checked' : ''}>
                        <label for="va-${idx}" style="margin:0;font-size:12px;cursor:pointer">Bật</label>
                    </div>
                </div>
            </div>
        </div>`;

    document.getElementById('variantList').appendChild(card);
    document.getElementById('variantEmptyMsg')?.remove();
}

function removeVariant(idx) {
    if (!confirm('Xóa biến thể này?')) return;
    document.getElementById('vc-' + idx)?.remove();
    delete removedVariantAttrs[idx];

    const list = document.getElementById('variantList');
    if (!list.children.length) {
        const msg = document.createElement('p');
        msg.className = 'variant-empty';
        msg.id = 'variantEmptyMsg';
        msg.innerHTML = 'Chưa có biến thể nào. Nhấn <strong>+ Thêm biến thể</strong> để bắt đầu.';
        document.getElementById('btnAddVariant').before(msg);
    }
}

function toggleVariantCard(idx) {
    const body = document.getElementById('vcb-' + idx);
    body.style.display = body.style.display === 'none' ? '' : 'none';
}

// ══════════════════════════════════════════════════════════════
// ẨN / THÊM LẠI THUỘC TÍNH RIÊNG CHO TỪNG SẢN PHẨM / BIẾN THỂ
// (chỉ ẩn khỏi form này, KHÔNG xóa thuộc tính khỏi hệ thống)
// ══════════════════════════════════════════════════════════════

// ── Khu vực "Thông số kỹ thuật" (thuộc tính gốc của sản phẩm) ──
function removeBaseAttrField(id, name) {
    const field = document.getElementById('attr-field-' + id);
    const input = field?.querySelector('input');
    removedBaseAttrs[id] = { name, value: input ? input.value : '' };
    field?.remove();

    const grid = document.getElementById('attrGrid');
    if (!grid.children.length && !document.getElementById('attrEmptyMsg')) {
        const msg = document.createElement('p');
        msg.id = 'attrEmptyMsg';
        msg.style.cssText = 'color:#aaa;font-size:13px';
        msg.innerHTML = 'Chưa có thuộc tính nào. Nhấn <strong>Quản lý thuộc tính</strong> để thêm.';
        grid.after(msg);
    }
    renderBaseRemovedChips();

    // ── Đồng bộ: ẩn thuộc tính này ở TẤT CẢ biến thể ──
    document.querySelectorAll('.variant-card').forEach(card => {
        const vcIdx = card.id.replace('vc-', '');
        const vcField = document.getElementById(`vca-${vcIdx}-${id}`);
        if (!vcField) return; // đã bị ẩn riêng trước đó → bỏ qua
        const vcInput = vcField.querySelector('input');
        if (!removedVariantAttrs[vcIdx]) removedVariantAttrs[vcIdx] = {};
        // chỉ lưu nếu chưa bị ẩn riêng (tránh ghi đè)
        if (!removedVariantAttrs[vcIdx][id]) {
            removedVariantAttrs[vcIdx][id] = { name, value: vcInput ? vcInput.value : '', hiddenByBase: true };
        }
        vcField.remove();
        const vcGrid = card.querySelector('.variant-attr-grid');
        if (vcGrid && !vcGrid.children.length && !vcGrid.querySelector('p')) {
            const msg = document.createElement('p');
            msg.style.cssText = 'color:#aaa;font-size:12px;grid-column:1/-1';
            msg.textContent = 'Biến thể này không áp dụng thuộc tính nào.';
            vcGrid.appendChild(msg);
        }
        renderVariantRemovedChips(vcIdx);
    });
}

function addBackBaseAttrField(id) {
    const data = removedBaseAttrs[id];
    if (!data) return;
    addFieldToForm(id, data.name);
    const input = document.querySelector(`#attrGrid input[name="attributes[${id}]"]`);
    if (input) input.value = data.value;
    delete removedBaseAttrs[id];
    renderBaseRemovedChips();

    // ── Đồng bộ: hiện lại thuộc tính này ở TẤT CẢ biến thể (chỉ những cái hiddenByBase) ──
    document.querySelectorAll('.variant-card').forEach(card => {
        const vcIdx = card.id.replace('vc-', '');
        const vcData = removedVariantAttrs[vcIdx]?.[id];
        if (!vcData || !vcData.hiddenByBase) return; // bị ẩn riêng bởi user → không tự hiện lại
        addBackVariantAttrField(vcIdx, id);
    });
}

function renderBaseRemovedChips() {
    const wrap = document.getElementById('attrRemovedChips');
    if (!wrap) return;
    wrap.innerHTML = '';
    Object.entries(removedBaseAttrs).forEach(([id, data]) => {
        const chip = document.createElement('button');
        chip.type = 'button';
        chip.className = 'removed-chip';
        chip.innerHTML = `<i class="fas fa-plus"></i> ${esc(data.name)}`;
        chip.addEventListener('click', () => addBackBaseAttrField(id));
        wrap.appendChild(chip);
    });
}

// Click vào nút x trong attrGrid (event delegation — bắt cả field render từ PHP và JS)
document.getElementById('attrGrid').addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-field-remove');
    if (!btn) return;
    removeBaseAttrField(btn.dataset.id, btn.dataset.name);
});

// ── Khu vực thuộc tính trong từng biến thể ─────────────────────
function removeVariantAttrField(idx, id, name) {
    const field = document.getElementById(`vca-${idx}-${id}`);
    const input = field?.querySelector('input');
    if (!removedVariantAttrs[idx]) removedVariantAttrs[idx] = {};
    removedVariantAttrs[idx][id] = { name, value: input ? input.value : '', hiddenByBase: false };
    field?.remove();

    const grid = document.querySelector(`#vc-${idx} .variant-attr-grid`);
    if (grid && !grid.children.length && !grid.querySelector('p')) {
        const msg = document.createElement('p');
        msg.style.cssText = 'color:#aaa;font-size:12px;grid-column:1/-1';
        msg.textContent = 'Biến thể này không áp dụng thuộc tính nào.';
        grid.appendChild(msg);
    }
    renderVariantRemovedChips(idx);
}

function addBackVariantAttrField(idx, id) {
    const data = removedVariantAttrs[idx]?.[id];
    if (!data) return;
    const grid = document.querySelector(`#vc-${idx} .variant-attr-grid`);
    if (!grid) return;
    grid.querySelector('p')?.remove();

    // Ưu tiên value đã có trước khi bị ẩn, nếu trống thì lấy theo thuộc tính gốc
    const value = data.value || getBaseAttrValue(id);

    const div = document.createElement('div');
    div.className = 'form-group';
    div.id = `vca-${idx}-${id}`;
    div.innerHTML = `
        <label class="attr-field-row">
            <span>${esc(data.name)}</span>
            <button type="button" class="btn-field-remove" data-id="${id}" data-name="${esc(data.name)}"
                title="Biến thể này không cần thuộc tính này">
                <i class="fas fa-times"></i>
            </button>
        </label>
        <input type="text" name="variants[${idx}][attrs][${id}]" value="${esc(value)}" placeholder="${esc(data.name)}...">`;
    grid.appendChild(div);

    delete removedVariantAttrs[idx][id];
    renderVariantRemovedChips(idx);
}

function renderVariantRemovedChips(idx) {
    const wrap = document.getElementById('vcRemovedChips-' + idx);
    if (!wrap) return;
    wrap.innerHTML = '';
    const map = removedVariantAttrs[idx] || {};
    Object.entries(map).forEach(([id, data]) => {
        const chip = document.createElement('button');
        chip.type = 'button';
        chip.className = 'removed-chip';
        chip.innerHTML = `<i class="fas fa-plus"></i> ${esc(data.name)}`;
        chip.addEventListener('click', () => addBackVariantAttrField(idx, id));
        wrap.appendChild(chip);
    });
}

// Click vào nút x trong bất kỳ variant-attr-grid nào (event delegation)
document.getElementById('variantList').addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-field-remove');
    if (!btn) return;
    const card = btn.closest('.variant-card');
    if (!card) return;
    const idx = card.id.replace('vc-', '');
    removeVariantAttrField(idx, btn.dataset.id, btn.dataset.name);
});

function updateVariantBadge(idx) {
    const priceEl = document.querySelector(`[name="variants[${idx}][price]"]`);
    const badge   = document.getElementById('vc-badge-' + idx);
    if (!priceEl || !badge) return;
    const v = parseInt(priceEl.value);
    badge.textContent = v ? v.toLocaleString('vi-VN') + 'đ' : 'Chưa nhập giá';
}

// ══════════════════════════════════════════════════════════════
// MODAL QUẢN LÝ THUỘC TÍNH (giữ nguyên logic cũ)
// ══════════════════════════════════════════════════════════════

document.getElementById('btnOpenAttrModal').addEventListener('click', () => {
    document.getElementById('attrModal').classList.add('open');
    loadAttrList();
});
document.getElementById('btnCloseModal').addEventListener('click', closeModal);
document.getElementById('btnModalDone').addEventListener('click', closeModal);
document.getElementById('attrModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

function closeModal() {
    document.getElementById('attrModal').classList.remove('open');
    document.getElementById('newAttrName').value = '';
    document.getElementById('newAttrIsVariant').checked = true;
    hideModalError();
}

function loadAttrList() {
    const wrap = document.getElementById('modalAttrListWrap');
    wrap.innerHTML = '<div class="modal-loading"><i class="fas fa-spinner fa-spin"></i> Đang tải...</div>';
    fetch(ROUTES.list, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(renderAttrList)
        .catch(() => {
            wrap.innerHTML = '<div class="modal-loading" style="color:#E53935">Lỗi tải dữ liệu.</div>';
        });
}

function renderAttrList(attrs) {
    const wrap = document.getElementById('modalAttrListWrap');
    if (!attrs.length) {
        wrap.innerHTML = '<p style="color:#aaa;font-size:13px;text-align:center;padding:16px 0">Chưa có thuộc tính nào.</p>';
        return;
    }
    const ul = document.createElement('ul');
    ul.className = 'attr-modal-list';
    attrs.forEach(attr => {
        const li = document.createElement('li');
        li.id = 'modal-attr-' + attr.id;
        li.innerHTML = `
        <span class="attr-item-name">${esc(attr.name)}</span>
        <button class="btn-attr-toggle" data-id="${attr.id}"
            style="border:1px solid ${attr.is_variant ? '#1565C0' : '#ccc'};color:${attr.is_variant ? '#1565C0' : '#888'};background:${attr.is_variant ? '#EBF3FF' : '#f5f5f5'};font-size:11px;font-weight:700;border-radius:12px;padding:3px 10px;cursor:pointer;white-space:nowrap"
            title="Bấm để chuyển thành thuộc tính ${attr.is_variant ? 'phụ' : 'chính'}">
            ${attr.is_variant ? 'Chính' : 'Phụ'}
        </button>
        <span class="attr-item-used">${attr.used_count > 0 ? attr.used_count + ' sản phẩm' : 'Chưa dùng'}</span>
        <button class="btn-attr-del" data-id="${attr.id}" data-name="${esc(attr.name)}"
            ${attr.used_count > 0 ? 'disabled title="Đang được dùng, không thể xóa"' : ''}>
            <i class="fas fa-trash"></i>
        </button>`;
        ul.appendChild(li);
    });
    wrap.innerHTML = '';
    wrap.appendChild(ul);
    wrap.querySelectorAll('.btn-attr-del:not(:disabled)').forEach(btn => {
        btn.addEventListener('click', () => deleteAttr(btn.dataset.id, btn.dataset.name));
    });
    wrap.querySelectorAll('.btn-attr-toggle').forEach(btn => {
        btn.addEventListener('click', () => toggleAttrVariant(btn.dataset.id));
    });
}

// ── Chuyển "Chính" ⇄ "Phụ" ─────────────────────────────────────────
function toggleAttrVariant(id) {
    fetch(ROUTES.toggleVariant.replace('__ID__', id), {
        method: 'PATCH',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
    })
    .then(r => r.json())
    .then(data => {
        const entry = ALL_ATTRIBUTES.find(a => a.id == data.id);
        if (entry) entry.is_variant = data.is_variant;

        // Cập nhật nhãn "(chính)"/"(phụ)" trên form thông số
        const nameEl = document.querySelector('#attr-field-' + data.id + ' .attr-field-row span');
        if (nameEl && entry) {
            nameEl.innerHTML = esc(entry.name) + (data.is_variant
                ? ' <em style="color:#1565C0;font-weight:600;font-style:normal">(chính)</em>'
                : ' <em style="color:#aaa;font-weight:400;font-style:normal">(phụ)</em>');
        }

        // Nếu chuyển sang "phụ": bỏ cột này khỏi tất cả biến thể hiện có
        // Nếu chuyển sang "chính": thêm cột này vào tất cả biến thể hiện có
        if (!data.is_variant) {
            removeAttrFromAllVariants(data.id);
        } else if (entry) {
            addAttrToAllVariants(data.id, entry.name);
        }

        loadAttrList();
    })
    .catch(() => alert('Lỗi kết nối, thử lại.'));
}

// ── Thêm ─────────────────────────────────────────────────────────
document.getElementById('btnAddAttr').addEventListener('click', addAttr);
document.getElementById('newAttrName').addEventListener('keydown', e => {
    if (e.key === 'Enter') {
        e.preventDefault();
        addAttr();
    }
});

function addAttr() {
    const input = document.getElementById('newAttrName');
    const name  = input.value.trim();
    const isVariant = document.getElementById('newAttrIsVariant').checked;
    if (!name) { showModalError('Vui lòng nhập tên thuộc tính.'); return; }
    const btn = document.getElementById('btnAddAttr');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    hideModalError();
    fetch(ROUTES.store, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ name, is_variant: isVariant }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.errors) { showModalError(Object.values(data.errors)[0][0]); return; }
        input.value = '';
        // Thêm vào danh sách ALL_ATTRIBUTES (để variant mới có field này)
        ALL_ATTRIBUTES.push({ id: data.id, name: data.name, is_variant: data.is_variant });
        addFieldToForm(data.id, data.name, data.is_variant);
        if (data.is_variant) addAttrToAllVariants(data.id, data.name);
        loadAttrList();
    })
    .catch(() => showModalError('Lỗi kết nối, thử lại.'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-plus"></i> Thêm';
    });
}

function deleteAttr(id, name) {
    if (!confirm(`Xóa thuộc tính "${name}"?`)) return;
    fetch(ROUTES.destroy.replace('__ID__', id), {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) { showModalError(data.error); return; }
        document.getElementById('modal-attr-' + id)?.remove();
        removeFieldFromForm(id);
        removeAttrFromAllVariants(id);
        const idx = ALL_ATTRIBUTES.findIndex(a => a.id == id);
        if (idx !== -1) ALL_ATTRIBUTES.splice(idx, 1);
    })
    .catch(() => showModalError('Lỗi kết nối, thử lại.'));
}

// ── Sync field thông số kỹ thuật ─────────────────────────────
function addFieldToForm(id, name, isVariant) {
    if (document.getElementById('attr-field-' + id)) return;
    document.getElementById('attrEmptyMsg')?.remove();
    const div = document.createElement('div');
    div.className = 'form-group';
    div.id = 'attr-field-' + id;
    const subLabel = isVariant
        ? ' <em style="color:#1565C0;font-weight:600;font-style:normal">(chính)</em>'
        : ' <em style="color:#aaa;font-weight:400;font-style:normal">(phụ)</em>';
    div.innerHTML = `
        <label class="attr-field-row">
            <span>${esc(name)}${subLabel}</span>
            <button type="button" class="btn-field-remove" data-id="${id}" data-name="${esc(name)}"
                title="Sản phẩm này không cần thuộc tính này">
                <i class="fas fa-times"></i>
            </button>
        </label>
        <input type="text" name="attributes[${id}]" placeholder="Nhập ${esc(name.toLowerCase())}...">`;
    document.getElementById('attrGrid').appendChild(div);
}
function removeFieldFromForm(id) {
    document.getElementById('attr-field-' + id)?.remove();
    delete removedBaseAttrs[id];
    renderBaseRemovedChips();
    const grid = document.getElementById('attrGrid');
    if (!grid.children.length) {
        const msg = document.createElement('p');
        msg.id = 'attrEmptyMsg';
        msg.style.cssText = 'color:#aaa;font-size:13px';
        msg.innerHTML = 'Chưa có thuộc tính nào. Nhấn <strong>Quản lý thuộc tính</strong> để thêm.';
        grid.after(msg);
    }
}

// ── Sync field trong TẤT CẢ variant cards ────────────────────
function addAttrToAllVariants(id, name) {
    document.querySelectorAll('.variant-card').forEach(card => {
        const vcIdx = card.id.replace('vc-', '');
        const grid  = card.querySelector('.variant-attr-grid');
        if (!grid) return;
        // Xóa thông báo "chưa có thuộc tính" nếu có
        grid.querySelector('p')?.remove();
        const div = document.createElement('div');
        div.className = 'form-group';
        div.id = `vca-${vcIdx}-${id}`;
        div.innerHTML = `
            <label class="attr-field-row">
                <span>${esc(name)}</span>
                <button type="button" class="btn-field-remove" data-id="${id}" data-name="${esc(name)}"
                    title="Biến thể này không cần thuộc tính này">
                    <i class="fas fa-times"></i>
                </button>
            </label>
            <input type="text" name="variants[${vcIdx}][attrs][${id}]" placeholder="${esc(name)}...">`;
        grid.appendChild(div);
    });
}
function removeAttrFromAllVariants(id) {
    document.querySelectorAll(`[id^="vca-"][id$="-${id}"]`).forEach(el => el.remove());
    Object.keys(removedVariantAttrs).forEach(idx => {
        if (removedVariantAttrs[idx][id]) {
            delete removedVariantAttrs[idx][id];
            renderVariantRemovedChips(idx);
        }
    });
}

// ── Helpers ───────────────────────────────────────────────────
function showModalError(msg) {
    const el = document.getElementById('modalError');
    el.textContent = msg; el.style.display = 'block';
}
function hideModalError() {
    document.getElementById('modalError').style.display = 'none';
}
function esc(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Preview ảnh ───────────────────────────────────────────────
document.getElementById('thumbnailInput').addEventListener('change', function() {
    const preview = document.getElementById('thumbnailPreview');
    preview.innerHTML = '';
    if (this.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.cssText = 'width:100px;height:100px;object-fit:cover;border-radius:6px;border:2px solid #1565C0';
            preview.appendChild(img);
        };
        reader.readAsDataURL(this.files[0]);
    }
});

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

// ── Preview ảnh riêng của biến thể (đại diện + album) — dùng event delegation
//    vì các variant-card có thể được thêm động sau khi trang đã load ──
document.getElementById('variantList').addEventListener('change', function (e) {
    const target = e.target;

    if (target.classList.contains('variant-thumb-input')) {
        const idx = target.dataset.vidx;
        const preview = document.getElementById('vThumbPreview-' + idx);
        preview.innerHTML = '';
        if (target.files[0]) {
            const reader = new FileReader();
            reader.onload = ev => {
                const img = document.createElement('img');
                img.src = ev.target.result;
                preview.appendChild(img);
            };
            reader.readAsDataURL(target.files[0]);
        }
        return;
    }

    if (target.classList.contains('variant-images-input')) {
        const idx = target.dataset.vidx;
        const preview = document.getElementById('vImagesPreview-' + idx);
        preview.innerHTML = '';
        Array.from(target.files).slice(0, 6).forEach(file => {
            const reader = new FileReader();
            reader.onload = ev => {
                const img = document.createElement('img');
                img.src = ev.target.result;
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    }
});
</script>
@endpush