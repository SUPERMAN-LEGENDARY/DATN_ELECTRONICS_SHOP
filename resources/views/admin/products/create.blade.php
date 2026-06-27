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
                <label>{{ $attr->name }}</label>
                <input type="text"
                       name="attributes[{{ $attr->id }}]"
                       value="{{ old('attributes.' . $attr->id, $savedAttrs[$attr->id]->value ?? '') }}"
                       placeholder="Nhập {{ strtolower($attr->name) }}...">
            </div>
            @endforeach
        </div>

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
                            @foreach($attributes as $attr)
                            @php $va = $v->variantAttributes->firstWhere('attribute_id', $attr->id); @endphp
                            <div class="form-group">
                                <label>{{ $attr->name }}</label>
                                <input type="text"
                                       name="variants[{{ $vi }}][attrs][{{ $attr->id }}]"
                                       value="{{ old("variants.$vi.attrs.$attr->id", $va->value ?? '') }}"
                                       placeholder="{{ $attr->name }}...">
                            </div>
                            @endforeach
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
const CSRF   = document.querySelector('meta[name="csrf-token"]').content;
const ROUTES = {
    list:    '{{ route("admin.attributes.list") }}',
    store:   '{{ route("admin.attributes.store") }}',
    destroy: '{{ url("admin/thuoc-tinh") }}/__ID__',
};

// ══════════════════════════════════════════════════════════════
// PHẦN BIẾN THỂ (VARIANTS)
// ══════════════════════════════════════════════════════════════

// Danh sách thuộc tính hiện tại (từ PHP → JS)
const ALL_ATTRIBUTES = @json($attributes->map(fn($a) => ['id' => $a->id, 'name' => $a->name]));

// Value đã lưu trong ProductAttribute — prefill cho biến thể mới
const SAVED_ATTR_VALUES = @json($savedAttrs->mapWithKeys(fn($pa, $attrId) => [(string)$attrId => $pa->value]));

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

    // Build các input thuộc tính — prefill value từ product attributes
    const attrInputs = ALL_ATTRIBUTES.map(a => {
        const prefill = SAVED_ATTR_VALUES[String(a.id)] ?? '';
        return `
        <div class="form-group">
            <label>${esc(a.name)}</label>
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
            <span class="vc-badge" id="vc-badge-${idx}">Chưa nhập giá</span>
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
            <div class="variant-price-row">
                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Giá (đ) *</label>
                    <input type="number" name="variants[${idx}][price]"
                           placeholder="29990000" min="0" required
                           oninput="updateVariantBadge(${idx})">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-percent"></i> Giảm giá (%)</label>
                    <input type="number" name="variants[${idx}][discount_percent]"
                           value="0" min="0" max="100">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-boxes"></i> Tồn kho</label>
                    <input type="number" name="variants[${idx}][stock]"
                           value="0" min="0">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-eye"></i> Hiển thị</label>
                    <div class="toggle-check" style="margin-top:8px">
                        <input type="checkbox" name="variants[${idx}][is_active]"
                               value="1" id="va-${idx}" checked>
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
}

document.getElementById('btnAddAttr').addEventListener('click', addAttr);
document.getElementById('newAttrName').addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); addAttr(); }
});

function addAttr() {
    const input = document.getElementById('newAttrName');
    const name  = input.value.trim();
    if (!name) { showModalError('Vui lòng nhập tên thuộc tính.'); return; }
    const btn = document.getElementById('btnAddAttr');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    hideModalError();
    fetch(ROUTES.store, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ name }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.errors) { showModalError(Object.values(data.errors)[0][0]); return; }
        input.value = '';
        // Thêm vào danh sách ALL_ATTRIBUTES (để variant mới có field này)
        ALL_ATTRIBUTES.push({ id: data.id, name: data.name });
        addFieldToForm(data.id, data.name);
        addAttrToAllVariants(data.id, data.name);
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
function addFieldToForm(id, name) {
    if (document.getElementById('attr-field-' + id)) return;
    document.getElementById('attrEmptyMsg')?.remove();
    const div = document.createElement('div');
    div.className = 'form-group';
    div.id = 'attr-field-' + id;
    div.innerHTML = `
        <label>${esc(name)}</label>
        <input type="text" name="attributes[${id}]" placeholder="Nhập ${esc(name.toLowerCase())}...">`;
    document.getElementById('attrGrid').appendChild(div);
}
function removeFieldFromForm(id) {
    document.getElementById('attr-field-' + id)?.remove();
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
            <label>${esc(name)}</label>
            <input type="text" name="variants[${vcIdx}][attrs][${id}]" placeholder="${esc(name)}...">`;
        grid.appendChild(div);
    });
}
function removeAttrFromAllVariants(id) {
    document.querySelectorAll(`[id^="vca-"][id$="-${id}"]`).forEach(el => el.remove());
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
</script>
@endpush