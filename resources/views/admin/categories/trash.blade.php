@extends('layouts.admin')

@section('title', $type === 'brand' ? 'Thùng rác thương hiệu' : 'Thùng rác danh mục')

@push('styles')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .page-header h2 {
        font-size: 20px;
        font-weight: 700;
    }

    .header-actions {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
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

    .btn-sm {
        padding: 5px 10px;
        font-size: 12px;
    }

    .btn-outline {
        background: #fff;
        border: 1px solid #ddd;
        color: #444;
    }

    .btn-outline:hover {
        background: #f5f5f5;
    }

    .btn-danger {
        background: #C62828;
        color: #fff;
    }

    .btn-danger:hover {
        background: #B71C1C;
    }

    .btn-danger-outline {
        background: #fff;
        color: #E53935;
        border: 1px solid #E53935;
    }

    .btn-danger-outline:hover {
        background: #FFEBEE;
    }

    .btn:disabled {
        opacity: .5;
        cursor: not-allowed;
    }

    .btn-restore {
        background: #2E7D32;
        color: #fff;
    }

    .btn-restore:hover {
        background: #1B5E20;
    }

    /* Tab switcher */
    .tab-bar {
        display: flex;
        gap: 4px;
        margin-bottom: 20px;
        background: #f0f0f0;
        padding: 4px;
        border-radius: 8px;
        width: fit-content;
    }

    .tab-btn {
        padding: 7px 20px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        color: #666;
        background: transparent;
        border: none;
        text-decoration: none;
        transition: .15s;
    }

    .tab-btn.active {
        background: #fff;
        color: #1565C0;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .12);
    }

    /* Search bar */
    .toolbar {
        display: flex;
        gap: 10px;
        margin-bottom: 16px;
    }

    .toolbar input {
        flex: 1;
        max-width: 300px;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 13px;
    }

    .toolbar input:focus {
        outline: none;
        border-color: #1565C0;
    }

    /* Table */
    .card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .08);
        overflow: hidden;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    th {
        background: #f8f9fa;
        padding: 11px 14px;
        text-align: left;
        font-weight: 700;
        font-size: 12px;
        color: #666;
        text-transform: uppercase;
        letter-spacing: .5px;
        border-bottom: 1px solid #e0e0e0;
    }

    td {
        padding: 10px 14px;
        border-bottom: 1px solid #f0f0f0;
        vertical-align: middle;
    }

    tr:last-child td {
        border-bottom: none;
    }

    tr:hover td {
        background: #fafafa;
    }

    .logo-thumb {
        width: 38px;
        height: 38px;
        object-fit: contain;
        border-radius: 6px;
        border: 1px solid #eee;
        padding: 2px;
        opacity: .6;
    }

    .logo-placeholder {
        width: 38px;
        height: 38px;
        background: #f0f0f0;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #bbb;
        font-size: 18px;
    }

    .name-trashed {
        font-weight: 600;
        color: #888;
        text-decoration: line-through;
    }

    .deleted-at {
        color: #888;
        font-size: 12px;
        white-space: nowrap;
    }

    .actions {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #aaa;
    }

    .empty-state i {
        font-size: 40px;
        margin-bottom: 12px;
        display: block;
    }

    .empty-state p {
        font-size: 14px;
    }

    .pagination-wrap {
        padding: 14px 16px;
        border-top: 1px solid #f0f0f0;
    }
</style>
@endpush

@section('content')

{{-- Tiêu đề --}}
<div class="page-header">
    <h2><i class="fas fa-trash-alt"></i> Thùng rác {{ $type === 'brand' ? 'thương hiệu' : 'danh mục' }}</h2>
    <div class="header-actions">
        <a href="{{ route('admin.categories.index', ['type' => $type]) }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Quay lại danh sách
        </a>

        <form action="{{ route('admin.categories.restore-all', ['type' => $type]) }}" method="POST"
            onsubmit="return confirm('Khôi phục tất cả mục trong thùng rác?')">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-primary" {{ $items->isEmpty() ? 'disabled' : '' }}>
                <i class="fas fa-undo"></i> Khôi phục tất cả
            </button>
        </form>

        <form action="{{ route('admin.categories.empty-trash', ['type' => $type]) }}" method="POST"
            onsubmit="return confirm('Xóa VĨNH VIỄN toàn bộ mục trong thùng rác? Hành động này không thể hoàn tác!')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger-outline" {{ $items->isEmpty() ? 'disabled' : '' }}>
                <i class="fas fa-broom"></i> Dọn thùng rác
            </button>
        </form>
    </div>
</div>

{{-- Flash messages --}}
@if(session('success'))
<div class="alert-success">✓ {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert-error">✕ {{ session('error') }}</div>
@endif

{{-- Tab chuyển loại --}}
<div class="tab-bar">
    <a href="{{ route('admin.categories.trash', ['type' => 'category']) }}"
        class="tab-btn {{ $type === 'category' ? 'active' : '' }}">
        <i class="fas fa-layer-group"></i> Danh mục
    </a>
    <a href="{{ route('admin.categories.trash', ['type' => 'brand']) }}"
        class="tab-btn {{ $type === 'brand' ? 'active' : '' }}">
        <i class="fas fa-tag"></i> Thương hiệu
    </a>
</div>

{{-- Tìm kiếm --}}
<form method="GET" class="toolbar">
    <input type="hidden" name="type" value="{{ $type }}">
    <input type="text" name="search" value="{{ $search }}" placeholder="Tìm theo tên...">
    <button type="submit" class="btn btn-outline"><i class="fas fa-search"></i> Tìm</button>
    @if($search)
    <a href="{{ route('admin.categories.trash', ['type' => $type]) }}" class="btn btn-outline">
        <i class="fas fa-times"></i> Xóa lọc
    </a>
    @endif
</form>

{{-- Bảng --}}
<div class="card">
    @if($items->isEmpty())
    <div class="empty-state">
        <i class="fas fa-trash-alt"></i>
        @if($search)
        <p>Không tìm thấy mục nào trong thùng rác khớp với "{{ $search }}".</p>
        @else
        <p>Thùng rác {{ $type === 'brand' ? 'thương hiệu' : 'danh mục' }} đang trống.</p>
        @endif
    </div>
    @else
    <table>
        <thead>
            <tr>
                <th style="width:50px">#</th>
                <th style="width:60px">Logo</th>
                <th>Tên</th>
                <th>Slug</th>
                <th style="width:150px">Đã xóa lúc</th>
                <th style="width:160px">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $cat)
            <tr>
                <td style="color:#aaa">{{ $cat->id }}</td>
                <td>
                    @if($cat->logo)
                    <img src="{{ asset('storage/'.$cat->logo) }}" class="logo-thumb" alt="{{ $cat->name }}">
                    @else
                    <div class="logo-placeholder"><i class="fas fa-image"></i></div>
                    @endif
                </td>
                <td><span class="name-trashed">{{ $cat->name }}</span></td>
                <td style="color:#888;font-size:12px">{{ $cat->slug }}</td>
                <td class="deleted-at">
                    {{ $cat->deleted_at ? $cat->deleted_at->format('d/m/Y H:i') : '—' }}
                </td>
                <td>
                    <div class="actions">
                        {{-- Khôi phục --}}
                        <form method="POST"
                            action="{{ route('admin.categories.restore', $cat->id) }}"
                            onsubmit="return confirm('Khôi phục \" {{ addslashes($cat->name) }}\"?')">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-restore" title="Khôi phục">
                                <i class="fas fa-undo"></i>
                            </button>
                        </form>

                        {{-- Xóa vĩnh viễn --}}
                        <form method="POST"
                            action="{{ route('admin.categories.force-delete', $cat->id) }}"
                            onsubmit="return confirm('Xóa VĨNH VIỄN \" {{ addslashes($cat->name) }}\"? Hành động này không thể hoàn tác!')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Xóa vĩnh viễn">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($items->hasPages())
    <div class="pagination-wrap">{{ $items->links() }}</div>
    @endif
    @endif
</div>

@endsection