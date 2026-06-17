@extends('layouts.admin')

@section('title', $type === 'brand' ? 'Thương hiệu' : 'Danh mục')

@push('styles')
<style>
.page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
.page-header h2 { font-size:20px; font-weight:700; }
.btn { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:6px;
       font-size:13px; font-weight:600; cursor:pointer; border:none; text-decoration:none; transition:.15s; }
.btn-primary   { background:#1565C0; color:#fff; }
.btn-primary:hover { background:#0D47A1; }
.btn-sm        { padding:5px 10px; font-size:12px; }
.btn-outline   { background:#fff; border:1px solid #ddd; color:#444; }
.btn-outline:hover { background:#f5f5f5; }
.btn-danger    { background:#C62828; color:#fff; }
.btn-danger:hover { background:#B71C1C; }
.btn-success   { background:#2E7D32; color:#fff; }
.btn-success:hover { background:#1B5E20; }
.btn-warning   { background:#F57F17; color:#fff; }
.btn-warning:hover { background:#E65100; }

/* Tab switcher */
.tab-bar { display:flex; gap:4px; margin-bottom:20px; background:#f0f0f0; padding:4px; border-radius:8px; width:fit-content; }
.tab-btn { padding:7px 20px; border-radius:6px; font-size:13px; font-weight:600; cursor:pointer;
           color:#666; background:transparent; border:none; text-decoration:none; transition:.15s; }
.tab-btn.active { background:#fff; color:#1565C0; box-shadow:0 1px 4px rgba(0,0,0,.12); }

/* Search bar */
.toolbar { display:flex; gap:10px; margin-bottom:16px; }
.toolbar input { flex:1; max-width:300px; padding:8px 12px; border:1px solid #ddd; border-radius:6px; font-size:13px; }
.toolbar input:focus { outline:none; border-color:#1565C0; }

/* Table */
.card { background:#fff; border-radius:10px; box-shadow:0 1px 4px rgba(0,0,0,.08); overflow:hidden; }
table { width:100%; border-collapse:collapse; font-size:14px; }
th { background:#f8f9fa; padding:11px 14px; text-align:left; font-weight:700; font-size:12px;
     color:#666; text-transform:uppercase; letter-spacing:.5px; border-bottom:1px solid #e0e0e0; }
td { padding:10px 14px; border-bottom:1px solid #f0f0f0; vertical-align:middle; }
tr:last-child td { border-bottom:none; }
tr:hover td { background:#fafafa; }

.logo-thumb { width:38px; height:38px; object-fit:contain; border-radius:6px; border:1px solid #eee; padding:2px; }
.logo-placeholder { width:38px; height:38px; background:#f0f0f0; border-radius:6px;
                    display:flex; align-items:center; justify-content:center; color:#bbb; font-size:18px; }

.badge { display:inline-block; padding:3px 8px; border-radius:20px; font-size:11px; font-weight:700; }
.badge-success { background:#E8F5E9; color:#2E7D32; }
.badge-muted   { background:#f0f0f0; color:#999; }

.actions { display:flex; gap:6px; flex-wrap:wrap; }

.empty-state { text-align:center; padding:60px 20px; color:#aaa; }
.empty-state i { font-size:40px; margin-bottom:12px; }
.empty-state p { font-size:14px; }

/* Pagination */
.pagination-wrap { padding:14px 16px; border-top:1px solid #f0f0f0; }
</style>
@endpush

@section('content')

{{-- Tiêu đề --}}
<div class="page-header">
    <h2><i class="fas fa-{{ $type === 'brand' ? 'tag' : 'layer-group' }}"></i>
        {{ $type === 'brand' ? 'Thương hiệu' : 'Danh mục sản phẩm' }}</h2>
    <a href="{{ route('admin.categories.create', ['type' => $type]) }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Thêm {{ $type === 'brand' ? 'thương hiệu' : 'danh mục' }}
    </a>
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
    <a href="{{ route('admin.categories.index', ['type' => 'category']) }}"
       class="tab-btn {{ $type === 'category' ? 'active' : '' }}">
        <i class="fas fa-layer-group"></i> Danh mục
    </a>
    <a href="{{ route('admin.categories.index', ['type' => 'brand']) }}"
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
        <a href="{{ route('admin.categories.index', ['type' => $type]) }}" class="btn btn-outline">
            <i class="fas fa-times"></i> Xóa lọc
        </a>
    @endif
</form>

{{-- Bảng --}}
<div class="card">
    @if($items->isEmpty())
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>Chưa có {{ $type === 'brand' ? 'thương hiệu' : 'danh mục' }} nào.</p>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th style="width:50px">#</th>
                    <th style="width:60px">Logo</th>
                    <th>Tên</th>
                    <th>Slug</th>
                    <th style="width:110px">Trạng thái</th>
                    <th style="width:180px">Thao tác</th>
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
                    <td style="font-weight:600">{{ $cat->name }}</td>
                    <td style="color:#888;font-size:12px">{{ $cat->slug }}</td>
                    <td>
                        <span class="badge {{ $cat->is_active ? 'badge-success' : 'badge-muted' }}">
                            {{ $cat->is_active ? 'Hoạt động' : 'Tắt' }}
                        </span>
                    </td>
                    <td>
                        <div class="actions">
                            {{-- Sửa --}}
                            <a href="{{ route('admin.categories.edit', $cat) }}"
                               class="btn btn-sm btn-outline" title="Sửa">
                                <i class="fas fa-edit"></i>
                            </a>

                            {{-- Bật/tắt --}}
                            <form method="POST"
                                  action="{{ route('admin.categories.toggle-active', $cat) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="btn btn-sm {{ $cat->is_active ? 'btn-warning' : 'btn-success' }}"
                                        title="{{ $cat->is_active ? 'Tắt' : 'Kích hoạt' }}">
                                    <i class="fas fa-{{ $cat->is_active ? 'eye-slash' : 'eye' }}"></i>
                                </button>
                            </form>

                            {{-- Xóa --}}
                            <form method="POST"
                                  action="{{ route('admin.categories.destroy', $cat) }}"
                                  onsubmit="return confirm('Xóa \"{{ addslashes($cat->name) }}\"?')">
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
        @if($items->hasPages())
            <div class="pagination-wrap">{{ $items->links() }}</div>
        @endif
    @endif
</div>

@endsection