@extends('layouts.admin')

@section('title', 'Banner trang chủ')

@push('styles')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .page-header h2 {
        font-size: 20px;
        font-weight: 700;
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

    .btn-success {
        background: #2E7D32;
        color: #fff;
    }

    .btn-success:hover {
        background: #1B5E20;
    }

    .btn-warning {
        background: #F57F17;
        color: #fff;
    }

    .btn-warning:hover {
        background: #E65100;
    }

    .btn-trash {
        background: #fff;
        color: #757575;
        border: 1px solid #e0e0e0;
        position: relative;
    }

    .btn-trash:hover {
        background: #fafafa;
        border-color: #bdbdbd;
    }

    .trash-badge {
        background: #E53935;
        color: #fff;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 700;
        padding: 1px 7px;
        margin-left: 4px;
    }

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

    .banner-thumb {
        width: 70px;
        height: 44px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #eee;
    }

    .banner-placeholder {
        width: 70px;
        height: 44px;
        background: #f0f0f0;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #bbb;
        font-size: 16px;
    }

    .badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .badge-success {
        background: #E8F5E9;
        color: #2E7D32;
    }

    .badge-muted {
        background: #f0f0f0;
        color: #999;
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

<div class="page-header">
    <h2><i class="fas fa-images"></i> Banner trang chủ</h2>
    <div style="display:flex;gap:10px;align-items:center">
        <a href="{{ route('admin.banners.trash') }}" class="btn btn-trash">
            <i class="fas fa-trash-alt"></i> Thùng rác
            @if($trashedCount > 0)
            <span class="trash-badge">{{ $trashedCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Thêm banner
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert-success">✓ {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert-error">✕ {{ session('error') }}</div>
@endif

<form method="GET" class="toolbar">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo tiêu đề, nhãn...">
    <button type="submit" class="btn btn-outline"><i class="fas fa-search"></i> Tìm</button>
    @if(request('search'))
    <a href="{{ route('admin.banners.index') }}" class="btn btn-outline">
        <i class="fas fa-times"></i> Xóa lọc
    </a>
    @endif
</form>

<div class="card">
    @if($banners->isEmpty())
    <div class="empty-state">
        <i class="fas fa-image"></i>
        <p>Chưa có banner nào.</p>
    </div>
    @else
    <table>
        <thead>
            <tr>
                <th style="width:50px">#</th>
                <th style="width:90px">Ảnh</th>
                <th>Nhãn / Tiêu đề</th>
                <th>Giá</th>
                <th style="width:80px">Thứ tự</th>
                <th style="width:110px">Trạng thái</th>
                <th style="width:200px">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @foreach($banners as $banner)
            <tr>
                <td style="color:#aaa">{{ $banner->id }}</td>
                <td>
                    @if($banner->image)
                    <img src="{{ $banner->image }}" class="banner-thumb" alt="{{ $banner->title }}">
                    @else
                    <div class="banner-placeholder"><i class="fas fa-image"></i></div>
                    @endif
                </td>
                <td>
                    @if($banner->isImageOnly())
                    <span class="badge" style="background:#EDE7F6;color:#5E35B1"><i class="fas fa-panorama"></i> Chỉ ảnh</span>
                    @else
                    @if($banner->label)
                    <div style="font-size:11px;font-weight:700;color:#1565C0;text-transform:uppercase">{{ $banner->label }}</div>
                    @endif
                    <div style="font-weight:600">{{ $banner->title ?: '(Chưa có tiêu đề)' }}</div>
                    @endif
                </td>
                <td style="color:#E53935;font-weight:700;font-size:13px">{{ $banner->isImageOnly() ? '—' : ($banner->price ? number_format($banner->price, 0, ',', '.') . '₫' : '') }}</td>
                <td>{{ $banner->sort_order }}</td>
                <td>
                    <span class="badge {{ $banner->is_active ? 'badge-success' : 'badge-muted' }}">
                        {{ $banner->is_active ? 'Hiển thị' : 'Đã ẩn' }}
                    </span>
                </td>
                <td>
                    <div class="actions">
                        <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-sm btn-outline" title="Sửa">
                            <i class="fas fa-edit"></i>
                        </a>

                        <form method="POST" action="{{ route('admin.banners.toggle-active', $banner) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="btn btn-sm {{ $banner->is_active ? 'btn-warning' : 'btn-success' }}"
                                title="{{ $banner->is_active ? 'Ẩn' : 'Hiển thị' }}">
                                <i class="fas fa-{{ $banner->is_active ? 'eye-slash' : 'eye' }}"></i>
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}"
                            onsubmit="return confirm('Xóa banner \'{{ addslashes($banner->title ?: 'Banner #'.$banner->id) }}\'?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($banners->hasPages())
    <div class="pagination-wrap">{{ $banners->links() }}</div>
    @endif
    @endif
</div>

@endsection