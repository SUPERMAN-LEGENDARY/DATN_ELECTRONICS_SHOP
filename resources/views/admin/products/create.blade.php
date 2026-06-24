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
.btn-manage-attr { background:none; border:1px solid #1565C0; color:#1565C0; padding:5px 12px; border-radius:5px; font-size:12px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:5px; margin-left:10px; vertical-align:middle; }
.btn-manage-attr:hover { background:#E3F2FD; }
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
</style>
@endpush

@section('content')

@php
    // Dùng chung cho cả create lẫn edit
    $savedAttrs = isset($product) ? $product->attributes->keyBy('attribute_id') : collect();
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
        <div class="form-group">
            <label>Tải ảnh lên (tối đa 6 ảnh, mỗi ảnh ≤ 3MB)</label>
            <input type="file" name="images[]" multiple accept="image/*" id="imageInput">
            @error('images.*')<div class="error">{{ $message }}</div>@enderror
        </div>
        @if(isset($product) && !empty($product->images))
        <div class="image-preview-row">
            @foreach($product->images as $img)
            <img src="{{ $img }}" alt="">
            @endforeach
        </div>
        <p style="font-size:12px;color:#888;margin-top:6px">Tải ảnh mới sẽ thay thế toàn bộ ảnh cũ.</p>
        @endif
        <div class="image-preview-row" id="newImagePreview"></div>
    </div>

    {{-- Thuộc tính kỹ thuật --}}
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

// ── Mở / đóng modal ──────────────────────────────────────────────
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

// ── Load danh sách ────────────────────────────────────────────────
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

// ── Thêm ─────────────────────────────────────────────────────────
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
        addFieldToForm(data.id, data.name);
        loadAttrList();
    })
    .catch(() => showModalError('Lỗi kết nối, thử lại.'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-plus"></i> Thêm';
    });
}

// ── Xóa ──────────────────────────────────────────────────────────
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
    })
    .catch(() => showModalError('Lỗi kết nối, thử lại.'));
}

// ── Sync field trong form sản phẩm ───────────────────────────────
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

// ── Helpers ───────────────────────────────────────────────────────
function showModalError(msg) {
    const el = document.getElementById('modalError');
    el.textContent = msg; el.style.display = 'block';
}
function hideModalError() {
    document.getElementById('modalError').style.display = 'none';
}
function esc(str) {
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Preview ảnh ───────────────────────────────────────────────────
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