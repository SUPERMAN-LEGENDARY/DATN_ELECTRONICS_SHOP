@extends('layouts.admin')
@section('title', 'Thùng rác tin tức')

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

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px
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

    .filter-bar {
        background: #fff;
        border-radius: 10px;
        padding: 14px 20px;
        margin-bottom: 20px;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: flex-end;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .07)
    }

    .filter-bar input {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 7px;
        font-size: 13px;
        color: #333;
        outline: none;
        min-width: 220px
    }

    .filter-bar input:focus {
        border-color: #1E88E5
    }

    .btn-filter {
        padding: 8px 18px;
        background: #1565C0;
        color: #fff;
        border: none;
        border-radius: 7px;
        font-size: 13px;
        cursor: pointer
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

    .thumb {
        width: 60px;
        height: 44px;
        border-radius: 6px;
        object-fit: cover;
        background: #f0f0f0;
        opacity: .6
    }

    .thumb-empty {
        width: 60px;
        height: 44px;
        border-radius: 6px;
        background: #f0f4f8;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #bbb;
        font-size: 18px
    }

    .news-title-trashed {
        font-weight: 600;
        color: #888;
        text-decoration: line-through;
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 280px
    }

    .news-meta {
        font-size: 12px;
        color: #999;
        margin-top: 2px
    }

    .deleted-at {
        font-size: 13px;
        color: #666;
        white-space: nowrap
    }

    .actions {
        display: flex;
        gap: 6px;
        flex-wrap: wrap
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
    <h1 class="page-title"><i class="fas fa-trash-alt" style="color:#E53935"></i> Thùng rác tin tức</h1>
    <div class="header-actions">
        <a href="{{ route('admin.news.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Quay lại danh sách
        </a>
        <form action="{{ route('admin.news.restore-all') }}" method="POST"
            onsubmit="return confirm('Khôi phục tất cả bài viết trong thùng rác?')">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-primary" {{ $newsList->isEmpty() ? 'disabled' : '' }}>
                <i class="fas fa-undo"></i> Khôi phục tất cả
            </button>
        </form>
        <form action="{{ route('admin.news.empty-trash') }}" method="POST"
            onsubmit="return confirm('Xóa VĨNH VIỄN toàn bộ bài viết trong thùng rác? Hành động này không thể hoàn tác!')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger-outline" {{ $newsList->isEmpty() ? 'disabled' : '' }}>
                <i class="fas fa-broom"></i> Dọn thùng rác
            </button>
        </form>
    </div>
</div>

@if(session('success'))
<div class="alert-success" style="margin-bottom:16px"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

<form method="GET" class="filter-bar">
    <input type="text" name="search" placeholder="Tìm tiêu đề…" value="{{ request('search') }}">
    <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Tìm</button>
    @if(request('search'))
    <a href="{{ route('admin.news.trash') }}" class="btn btn-outline"><i class="fas fa-redo"></i> Đặt lại</a>
    @endif
</form>

<div class="card">
    @if($newsList->isEmpty())
    <div class="empty">
        <i class="fas fa-trash-alt" style="font-size:40px;display:block;margin-bottom:10px"></i>
        @if(request('search'))
        Không tìm thấy bài viết nào trong thùng rác khớp với "{{ request('search') }}".
        @else
        Thùng rác đang trống.
        @endif
    </div>
    @else
    <table>
        <thead>
            <tr>
                <th style="width:70px">Ảnh</th>
                <th>Tiêu đề</th>
                <th>Danh mục</th>
                <th>Tác giả</th>
                <th>Đã xóa lúc</th>
                <th style="width:140px">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($newsList as $item)
            <tr>
                <td>
                    @if($item->thumbnail)
                    <img src="{{ asset('storage/'.$item->thumbnail) }}" class="thumb" alt="">
                    @else
                    <div class="thumb-empty"><i class="fas fa-image"></i></div>
                    @endif
                </td>
                <td>
                    <span class="news-title-trashed">{{ $item->title }}</span>
                    <div class="news-meta">ID: {{ $item->id }} · {{ $item->views }} lượt xem</div>
                </td>
                <td style="font-size:13px">{{ $item->category->name ?? '—' }}</td>
                <td style="font-size:13px;color:#666">{{ $item->author->name ?? '—' }}</td>
                <td class="deleted-at">
                    {{ $item->deleted_at ? $item->deleted_at->format('d/m/Y H:i') : '—' }}
                </td>
                <td>
                    <div class="actions">
                        <form method="POST" action="{{ route('admin.news.restore', $item->id) }}" style="display:inline"
                            onsubmit="return confirm('Khôi phục bài viết này?')">
                            @csrf @method('PATCH')
                            <button class="btn-sm btn-restore" title="Khôi phục">
                                <i class="fas fa-undo"></i> Khôi phục
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.news.force-delete', $item->id) }}" style="display:inline"
                            onsubmit="return confirm('Xóa VĨNH VIỄN bài viết này? Hành động này không thể hoàn tác!')">
                            @csrf @method('DELETE')
                            <button class="btn-sm btn-del" title="Xóa vĩnh viễn"><i class="fas fa-times"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($newsList->hasPages())
    <div style="padding:16px;display:flex;justify-content:center">
        {!! $newsList->links() !!}
    </div>
    @endif
    @endif
</div>
@endsection