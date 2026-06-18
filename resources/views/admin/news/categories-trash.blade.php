@extends('layouts.admin')
@section('title', 'Thùng rác danh mục tin tức')

@push('styles')
<style>
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 12px
    }

    .page-title {
        font-size: 20px;
        font-weight: 700;
        color: #1a1a1a
    }

    .header-actions {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap
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

    .btn {
        padding: 9px 18px;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px
    }

    .btn:disabled {
        opacity: .5;
        cursor: not-allowed
    }

    .btn-outline {
        background: #fff;
        color: #444;
        border: 1px solid #ddd
    }

    .btn-outline:hover {
        background: #f5f5f5
    }

    .btn-primary {
        background: #1565C0;
        color: #fff
    }

    .btn-primary:hover {
        background: #0D47A1
    }

    .btn-danger-outline {
        background: #fff;
        color: #E53935;
        border: 1px solid #E53935
    }

    .btn-danger-outline:hover {
        background: #FFEBEE
    }

    .card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .07);
        overflow: hidden
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

    .name-trashed {
        font-weight: 600;
        color: #888;
        text-decoration: line-through
    }

    .deleted-at {
        font-size: 13px;
        color: #666;
        white-space: nowrap
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

    .btn-restore {
        background: #E8F5E9;
        color: #2E7D32
    }

    .btn-restore:hover {
        background: #C8E6C9
    }

    .btn-del {
        background: #FFEBEE;
        color: #C62828
    }

    .btn-del:hover {
        background: #FFCDD2
    }

    .empty {
        text-align: center;
        padding: 48px;
        color: #aaa
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-trash-alt" style="color:#E53935"></i> Thùng rác danh mục tin tức</h1>
    <div class="header-actions">
        <a href="{{ route('admin.news.categories') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Quay lại danh mục
        </a>
        <form action="{{ route('admin.news.categories.restore-all') }}" method="POST"
            onsubmit="return confirm('Khôi phục tất cả danh mục trong thùng rác?')">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-primary" {{ $categories->isEmpty() ? 'disabled' : '' }}>
                <i class="fas fa-undo"></i> Khôi phục tất cả
            </button>
        </form>
        <form action="{{ route('admin.news.categories.empty-trash') }}" method="POST"
            onsubmit="return confirm('Xóa VĨNH VIỄN toàn bộ danh mục trong thùng rác? Hành động này không thể hoàn tác!')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger-outline" {{ $categories->isEmpty() ? 'disabled' : '' }}>
                <i class="fas fa-broom"></i> Dọn thùng rác
            </button>
        </form>
    </div>
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

<div class="card">
    @if($categories->isEmpty())
    <div class="empty">
        <i class="fas fa-trash-alt" style="font-size:40px;display:block;margin-bottom:10px"></i>
        Thùng rác danh mục đang trống.
    </div>
    @else
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Tên danh mục</th>
                <th>Slug</th>
                <th>Đã xóa lúc</th>
                <th style="width:140px">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $cat)
            <tr>
                <td style="color:#aaa;font-size:13px">{{ $cat->id }}</td>
                <td><span class="name-trashed">{{ $cat->name }}</span></td>
                <td style="font-size:12px;color:#999">{{ $cat->slug }}</td>
                <td class="deleted-at">
                    {{ $cat->deleted_at ? $cat->deleted_at->format('d/m/Y H:i') : '—' }}
                </td>
                <td>
                    <div class="actions">
                        <form method="POST" action="{{ route('admin.news.categories.restore', $cat->id) }}"
                            onsubmit="return confirm('Khôi phục danh mục {{ addslashes($cat->name) }}?')">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-sm btn-restore" title="Khôi phục">
                                <i class="fas fa-undo"></i> Khôi phục
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.news.categories.force-delete', $cat->id) }}"
                            onsubmit="return confirm('Xóa VĨNH VIỄN danh mục {{ addslashes($cat->name) }}? Hành động này không thể hoàn tác!')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-sm btn-del" title="Xóa vĩnh viễn"><i class="fas fa-times"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($categories->hasPages())
    <div style="padding:16px;display:flex;justify-content:center">{!! $categories->links() !!}</div>
    @endif
    @endif
</div>
@endsection