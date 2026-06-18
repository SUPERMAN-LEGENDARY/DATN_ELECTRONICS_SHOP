@extends('layouts.admin')
@section('title', 'Quản lý tin tức')

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

    .btn-add {
        padding: 10px 20px;
        background: #1565C0;
        color: #fff;
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

    .btn-add:hover {
        background: #0D47A1
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
        background: transparent
    }

    .tab-btn.active {
        background: #1565C0;
        color: #fff
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

    .filter-bar select,
    .filter-bar input {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 7px;
        font-size: 13px;
        color: #333;
        outline: none;
        min-width: 160px
    }

    .filter-bar select:focus,
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

    .btn-reset {
        padding: 8px 14px;
        background: #eee;
        color: #555;
        border: none;
        border-radius: 7px;
        font-size: 13px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px
    }

    .btn-trash {
        padding: 10px 20px;
        background: #fff;
        color: #757575;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
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
        background: #f0f0f0
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
        gap: 6px;
        flex-wrap: wrap
    }

    .btn-sm {
        padding: 5px 12px;
        border: none;
        border-radius: 6px;
        font-size: 12px;
        cursor: pointer;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px
    }

    .btn-edit {
        background: #E3F2FD;
        color: #1565C0
    }

    .btn-edit:hover {
        background: #BBDEFB
    }

    .btn-toggle-on {
        background: #E8F5E9;
        color: #2E7D32
    }

    .btn-toggle-on:hover {
        background: #C8E6C9
    }

    .btn-toggle-off {
        background: #FFF3E0;
        color: #E65100
    }

    .btn-toggle-off:hover {
        background: #FFE0B2
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

    .news-title {
        font-weight: 600;
        color: #222;
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
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-newspaper" style="color:#1E88E5"></i> Quản lý tin tức</h1>
    <div style="display:flex;gap:10px;align-items:center">
        <a href="{{ route('admin.news.trash') }}" class="btn-trash">
            <i class="fas fa-trash-alt"></i> Thùng rác
            @if($trashedCount > 0)
            <span class="trash-badge">{{ $trashedCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.news.create') }}" class="btn-add">
            <i class="fas fa-plus"></i> Thêm bài viết
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert-success" style="margin-bottom:16px"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert-error" style="margin-bottom:16px"><i class="fas fa-times-circle"></i> {{ session('error') }}</div>
@endif

{{-- Tab điều hướng --}}
<div class="tab-bar">
    <a href="{{ route('admin.news.index') }}" class="tab-btn {{ !request()->routeIs('admin.news.categories') ? 'active' : '' }}" style="text-decoration:none">
        <i class="fas fa-file-alt"></i> Bài viết
    </a>
    <a href="{{ route('admin.news.categories') }}" class="tab-btn {{ request()->routeIs('admin.news.categories') ? 'active' : '' }}" style="text-decoration:none">
        <i class="fas fa-folder"></i> Danh mục
    </a>
</div>

{{-- Bộ lọc --}}
<form method="GET" class="filter-bar">
    <input type="text" name="search" placeholder="Tìm tiêu đề…" value="{{ request('search') }}">
    <select name="category">
        <option value="">-- Tất cả danh mục --</option>
        @foreach($categories as $cat)
        <option value="{{ $cat->id }}" @selected(request('category')==$cat->id)>{{ $cat->name }}</option>
        @endforeach
    </select>
    <select name="status">
        <option value="">-- Tất cả trạng thái --</option>
        <option value="active" @selected(request('status')==='active' )>Đang hiển thị</option>
        <option value="inactive" @selected(request('status')==='inactive' )>Đang ẩn</option>
    </select>
    <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Tìm</button>
    <a href="{{ route('admin.news.index') }}" class="btn-reset"><i class="fas fa-redo"></i> Đặt lại</a>
</form>

<div class="card">
    @if($newsList->isEmpty())
    <div class="empty">
        <i class="fas fa-newspaper" style="font-size:40px;display:block;margin-bottom:10px"></i>
        Chưa có bài viết nào.
    </div>
    @else
    <table>
        <thead>
            <tr>
                <th style="width:70px">Ảnh</th>
                <th>Tiêu đề</th>
                <th>Danh mục</th>
                <th>Tác giả</th>
                <th>Trạng thái</th>
                <th>Ngày đăng</th>
                <th style="width:180px">Hành động</th>
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
                    <span class="news-title">{{ $item->title }}</span>
                    <div class="news-meta">ID: {{ $item->id }} · {{ $item->views }} lượt xem</div>
                </td>
                <td style="font-size:13px">{{ $item->category->name ?? '—' }}</td>
                <td style="font-size:13px;color:#666">{{ $item->author->name ?? '—' }}</td>
                <td>
                    @if($item->is_active)
                    <span class="badge badge-active">Hiển thị</span>
                    @else
                    <span class="badge badge-inactive">Ẩn</span>
                    @endif
                </td>
                <td style="font-size:13px;color:#666;white-space:nowrap">
                    {{ $item->published_at ? $item->published_at->format('d/m/Y') : '—' }}
                </td>
                <td>
                    <div class="actions">
                        <a href="{{ route('admin.news.edit', $item) }}" class="btn-sm btn-edit">
                            <i class="fas fa-edit"></i> Sửa
                        </a>
                        <form method="POST" action="{{ route('admin.news.toggle-active', $item) }}" style="display:inline">
                            @csrf @method('PATCH')
                            <button class="btn-sm {{ $item->is_active ? 'btn-toggle-off' : 'btn-toggle-on' }}">
                                <i class="fas {{ $item->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                {{ $item->is_active ? 'Ẩn' : 'Hiện' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.news.destroy', $item) }}" style="display:inline"
                            onsubmit="return confirm('Xoá bài viết này?')">
                            @csrf @method('DELETE')
                            <button class="btn-sm btn-del"><i class="fas fa-trash"></i></button>
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