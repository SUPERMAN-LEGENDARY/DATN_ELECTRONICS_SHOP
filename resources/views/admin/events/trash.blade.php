@extends('layouts.admin')

@section('title', 'Thùng rác sự kiện')

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

    .btn-outline {
        background: #fff;
        border: 1px solid #ddd;
        color: #444;
    }

    .btn-outline:hover {
        background: #f5f5f5;
    }

    .btn-success {
        background: #2E7D32;
        color: #fff;
    }

    .btn-success:hover {
        background: #1B5E20;
    }

    .btn-danger {
        background: #C62828;
        color: #fff;
    }

    .btn-danger:hover {
        background: #B71C1C;
    }

    .btn-sm {
        padding: 5px 10px;
        font-size: 12px;
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
        border-bottom: 1px solid #e0e0e0;
    }

    td {
        padding: 10px 14px;
        border-bottom: 1px solid #f0f0f0;
        vertical-align: middle;
    }

    .ev-thumb {
        width: 70px;
        height: 44px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #eee;
    }

    .actions {
        display: flex;
        gap: 6px;
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

    .pagination-wrap {
        padding: 14px 16px;
        border-top: 1px solid #f0f0f0;
    }
</style>
@endpush

@section('content')

<div class="page-header">
    <h2><i class="fas fa-trash-alt"></i> Thùng rác sự kiện</h2>
    <div style="display:flex;gap:10px">
        <a href="{{ route('admin.events.index') }}" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Quay lại</a>
        @if($events->count() > 0)
        <form method="POST" action="{{ route('admin.events.restore-all') }}">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-success"><i class="fas fa-undo"></i> Khôi phục tất cả</button>
        </form>
        <form method="POST" action="{{ route('admin.events.empty-trash') }}" onsubmit="return confirm('Xóa vĩnh viễn toàn bộ thùng rác sự kiện?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Dọn sạch</button>
        </form>
        @endif
    </div>
</div>

@if(session('success'))
<div class="alert-success">✓ {{ session('success') }}</div>
@endif

<div class="card">
    @if($events->isEmpty())
    <div class="empty-state">
        <i class="fas fa-trash-alt"></i>
        <p>Thùng rác trống.</p>
    </div>
    @else
    <table>
        <thead>
            <tr>
                <th style="width:50px">#</th>
                <th style="width:90px">Ảnh</th>
                <th>Tên sự kiện</th>
                <th style="width:160px">Đã xoá</th>
                <th style="width:200px">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @foreach($events as $event)
            <tr>
                <td style="color:#aaa">{{ $event->id }}</td>
                <td>
                    @if($event->image)
                    <img src="{{ $event->image }}" class="ev-thumb" alt="{{ $event->title }}">
                    @endif
                </td>
                <td style="font-weight:600">{{ $event->title }}</td>
                <td style="font-size:12px;color:#888">{{ $event->deleted_at?->format('d/m/Y H:i') }}</td>
                <td>
                    <div class="actions">
                        <form method="POST" action="{{ route('admin.events.restore', $event->id) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-undo"></i> Khôi phục</button>
                        </form>
                        <form method="POST" action="{{ route('admin.events.force-delete', $event->id) }}" onsubmit="return confirm('Xóa vĩnh viễn sự kiện này?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Xoá vĩnh viễn</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($events->hasPages())
    <div class="pagination-wrap">{{ $events->links() }}</div>
    @endif
    @endif
</div>

@endsection