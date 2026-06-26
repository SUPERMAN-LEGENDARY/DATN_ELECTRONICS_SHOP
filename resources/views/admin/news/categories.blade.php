@extends('layouts.admin')
@section('title', 'Danh mục tin tức')

@push('styles')
<style>
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px
    }

    .page-title {
        font-size: 20px;
        font-weight: 700;
        color: #1a1a1a
    }

    .tab-bar {
        display: flex;
        gap: 4px;
        margin-bottom: 20px;
        background: #fff;
        border-radius: 10px;
        padding: 6px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .07);
        width: fit-content
    }

    .tab-btn {
        padding: 8px 20px;
        border: none;
        border-radius: 7px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        color: #666;
        background: transparent;
        text-decoration: none
    }

    .tab-btn.active {
        background: #1565C0;
        color: #fff
    }

    .layout {
        display: grid;
        grid-template-columns: 360px 1fr;
        gap: 20px;
        align-items: start
    }

    @media(max-width:800px) {
        .layout {
            grid-template-columns: 1fr
        }
    }

    .card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .07);
        padding: 24px
    }

    .card-title {
        font-size: 15px;
        font-weight: 700;
        color: #333;
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f0f0f0
    }

    .form-group {
        margin-bottom: 16px
    }

    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #444;
        margin-bottom: 6px
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        transition: border .2s
    }

    .form-control:focus {
        border-color: #1E88E5;
        box-shadow: 0 0 0 3px rgba(30, 136, 229, .1)
    }

    .error-msg {
        color: #C62828;
        font-size: 12px;
        margin-top: 4px
    }

    .btn-submit {
        padding: 10px 24px;
        background: #1565C0;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer
    }

    .btn-submit:hover {
        background: #0D47A1
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px
    }

    thead th {
        background: #f8f9fb;
        padding: 12px 16px;
        text-align: left;
        font-weight: 600;
        color: #555;
        border-bottom: 1px solid #eee
    }

    tbody td {
        padding: 12px 16px;
        border-bottom: 1px solid #f4f4f4;
        vertical-align: middle
    }

    tbody tr:last-child td {
        border-bottom: none
    }

    tbody tr:hover {
        background: #fafbfc
    }

    .badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600
    }

    .badge-active {
        background: #E8F5E9;
        color: #2E7D32
    }

    .badge-inactive {
        background: #FFEBEE;
        color: #C62828
    }

    .actions {
        display: flex;
        gap: 6px
    }

    .btn-sm {
        padding: 5px 12px;
        border: none;
        border-radius: 6px;
        font-size: 12px;
        cursor: pointer;
        font-weight: 600
    }

    .btn-edit {
        background: #E3F2FD;
        color: #1565C0
    }

    .btn-del {
        background: #FFEBEE;
        color: #C62828
    }

    .btn-del:hover {
        background: #FFCDD2
    }

    .btn-trash {
        padding: 8px 16px;
        background: #fff;
        color: #757575;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        position: relative
    }

    .btn-trash:hover {
        background: #fafafa;
        border-color: #bdbdbd
    }

    .trash-badge {
        background: #E53935;
        color: #fff;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 700;
        padding: 1px 7px;
        margin-left: 2px
    }

    /* Inline edit modal */
    .edit-row {
        display: none;
        background: #f0f4ff
    }

    .edit-row.open {
        display: table-row
    }

    .edit-row td {
        padding: 14px 16px;
        border-bottom: 1px solid #dce8ff
    }

    .inline-form {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap
    }

    .inline-form input,
    .inline-form select {
        padding: 8px 12px;
        border: 1px solid #bcd;
        border-radius: 7px;
        font-size: 13px;
        outline: none
    }

    .inline-form input:focus {
        border-color: #1E88E5
    }

    .btn-save {
        padding: 7px 16px;
        background: #1565C0;
        color: #fff;
        border: none;
        border-radius: 7px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer
    }

    .btn-cancel {
        padding: 7px 14px;
        background: #eee;
        color: #555;
        border: none;
        border-radius: 7px;
        font-size: 13px;
        cursor: pointer
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-folder" style="color:#1E88E5"></i> Danh mục tin tức</h1>
    <a href="{{ route('admin.news.categories.trash') }}" class="btn-trash">
        <i class="fas fa-trash-alt"></i> Thùng rác
        @if($trashedCount > 0)
        <span class="trash-badge">{{ $trashedCount }}</span>
        @endif
    </a>
</div>

@if(session('success'))
<div class="alert-success" style="margin-bottom:16px"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert-error" style="margin-bottom:16px"><i class="fas fa-times-circle"></i> {{ session('error') }}</div>
@endif

<div class="tab-bar">
    <a href="{{ route('admin.news.index') }}" class="tab-btn">
        <i class="fas fa-file-alt"></i> Bài viết
    </a>
    <a href="{{ route('admin.news.categories') }}" class="tab-btn active">
        <i class="fas fa-folder"></i> Danh mục
    </a>
</div>

<div class="layout">
    {{-- Form thêm danh mục --}}
    <div class="card">
        <div class="card-title"><i class="fas fa-plus-circle" style="color:#1E88E5"></i> Thêm danh mục mới</div>
        <form method="POST" action="{{ route('admin.news.categories.store') }}" id="addCatForm" novalidate>
            @csrf
            <div class="form-group">
                <label>Tên danh mục <span style="color:red">*</span></label>
                <input type="text" name="name" id="cat_name" class="form-control"
                    value="{{ old('name') }}" placeholder="VD: Tin tức công nghệ">
                <div class="error-msg" id="cat_name-error">@error('name'){{ $message }}@enderror</div>
            </div>
            <div class="form-group">
                <label>Trạng thái</label>
                <select name="is_active" class="form-control">
                    <option value="1">Hiển thị</option>
                    <option value="0">Ẩn</option>
                </select>
            </div>
            <button type="submit" class="btn-submit"><i class="fas fa-plus"></i> Thêm danh mục</button>
        </form>
    </div>

    {{-- Bảng danh sách --}}
    <div class="card" style="padding:0">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên danh mục</th>
                    <th>Slug</th>
                    <th>Bài viết</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                <tr>
                    <td style="color:#aaa;font-size:13px">{{ $cat->id }}</td>
                    <td style="font-weight:600">{{ $cat->name }}</td>
                    <td style="font-size:12px;color:#999">{{ $cat->slug }}</td>
                    <td>
                        <span style="font-size:13px">{{ $cat->news_count }}</span>
                    </td>
                    <td>
                        @if($cat->is_active)
                        <span class="badge badge-active">Hiển thị</span>
                        @else
                        <span class="badge badge-inactive">Ẩn</span>
                        @endif
                    </td>
                    <td>
                        <div class="actions">
                            <button class="btn-sm btn-edit" onclick="toggleEdit({{ $cat->id }})">
                                <i class="fas fa-edit"></i> Sửa
                            </button>
                            <form method="POST"
                                action="{{ route('admin.news.categories.destroy', $cat) }}"
                                onsubmit="return confirm('Xoá danh mục {{ addslashes($cat->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-sm btn-del"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                {{-- Inline edit row --}}
                <tr class="edit-row" id="edit-row-{{ $cat->id }}">
                    <td colspan="6">
                        <form method="POST" action="{{ route('admin.news.categories.update', $cat) }}" class="inline-form">
                            @csrf @method('PUT')
                            <input type="text" name="name" value="{{ $cat->name }}" required placeholder="Tên danh mục">
                            <select name="is_active">
                                <option value="1" {{ $cat->is_active ? 'selected' : '' }}>Hiển thị</option>
                                <option value="0" {{ !$cat->is_active ? 'selected' : '' }}>Ẩn</option>
                            </select>
                            <button type="submit" class="btn-save"><i class="fas fa-save"></i> Lưu</button>
                            <button type="button" class="btn-cancel" onclick="toggleEdit({{ $cat->id }})">Huỷ</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:32px;color:#aaa">Chưa có danh mục nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($categories->hasPages())
        <div style="padding:16px;display:flex;justify-content:center">{!! $categories->links() !!}</div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleEdit(id) {
        const row = document.getElementById('edit-row-' + id);
        row.classList.toggle('open');
    }

    // ── Validate form thêm danh mục ───────────────────────────────
    (function() {
        const form = document.getElementById('addCatForm');
        const input = document.getElementById('cat_name');
        const errorDiv = document.getElementById('cat_name-error');

        input?.addEventListener('input', function() {
            input.style.borderColor = '';
            if (errorDiv) errorDiv.textContent = '';
        });

        form?.addEventListener('submit', function(e) {
            if (!input.value.trim()) {
                e.preventDefault();
                input.style.borderColor = '#C62828';
                if (errorDiv) errorDiv.textContent = 'Vui lòng nhập tên danh mục.';
                input.focus();
            } else {
                input.style.borderColor = '';
                if (errorDiv) errorDiv.textContent = '';
            }
        });
    })();

    // ── Validate inline edit forms ────────────────────────────────
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!form.classList.contains('inline-form')) return;
        const input = form.querySelector('input[name="name"]');
        if (!input) return;
        if (!input.value.trim()) {
            e.preventDefault();
            input.style.borderColor = '#C62828';
            input.focus();
            input.addEventListener('input', function() {
                input.style.borderColor = '';
            }, {
                once: true
            });
        }
    });
</script>
@endpush