@extends('layouts.admin')
@section('title', 'Quản lý sản phẩm')

@push('styles')
<style>
.admin-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; }
.admin-header h1 { font-size:20px; font-weight:800; }
.btn-primary { background:#1565C0; color:#fff; border:none; padding:9px 18px; border-radius:6px; font-size:14px; font-weight:600; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
.btn-primary:hover { background:#0D47A1; }
.btn-danger  { background:#E53935; color:#fff; border:none; padding:6px 12px; border-radius:4px; font-size:12px; font-weight:600; cursor:pointer; }
.btn-warning { background:#F57C00; color:#fff; border:none; padding:6px 12px; border-radius:4px; font-size:12px; font-weight:600; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; }
.btn-trash   { background:#fff; color:#757575; border:1px solid #e0e0e0; padding:9px 18px; border-radius:6px; font-size:14px; font-weight:600; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:6px; position:relative; }
.btn-trash:hover { background:#fafafa; border-color:#bdbdbd; }
.trash-badge { background:#E53935; color:#fff; border-radius:10px; font-size:11px; font-weight:700; padding:1px 7px; margin-left:4px; }
.filter-bar { display:flex; gap:10px; margin-bottom:16px; flex-wrap:wrap; }
.filter-bar input, .filter-bar select { border:1px solid #e0e0e0; border-radius:6px; padding:8px 12px; font-size:13px; outline:none; }
.filter-bar button { background:#1565C0; color:#fff; border:none; border-radius:6px; padding:8px 16px; font-size:13px; cursor:pointer; }
.table-wrap { overflow-x:auto; }
table.data-table { width:100%; border-collapse:collapse; font-size:13px; }
.data-table th { background:#f5f5f5; padding:10px 12px; text-align:left; font-weight:700; border-bottom:2px solid #e0e0e0; white-space:nowrap; }
.data-table td { padding:10px 12px; border-bottom:1px solid #f0f0f0; vertical-align:middle; }
.data-table tr:hover td { background:#fafafa; }
.product-thumb { width:48px; height:48px; object-fit:cover; border-radius:4px; background:#f0f0f0; display:flex; align-items:center; justify-content:center; }
.status-badge { display:inline-block; padding:2px 10px; border-radius:10px; font-size:11px; font-weight:700; }
.status-on  { background:#E8F5E9; color:#2E7D32; }
.status-off { background:#FFEBEE; color:#C62828; }
.toggle-btn { background:none; border:none; cursor:pointer; font-size:18px; }
.alert-success { background:#E8F5E9; border:1px solid #A5D6A7; color:#2E7D32; padding:10px 16px; border-radius:6px; margin-bottom:16px; font-size:14px; }
</style>
@endpush

@section('content')
<div class="admin-header">
    <h1><i class="fas fa-box"></i> Quản lý sản phẩm</h1>
    <div style="display:flex;gap:10px;align-items:center">
        <a href="{{ route('admin.products.trash') }}" class="btn-trash">
            <i class="fas fa-trash-alt"></i> Thùng rác
            @if($trashedCount > 0)
                <span class="trash-badge">{{ $trashedCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.products.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i> Thêm sản phẩm
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

{{-- Thanh lọc --}}
<form class="filter-bar" method="GET" action="{{ route('admin.products.index') }}">
    <input type="text"   name="q"        placeholder="Tìm theo tên..." value="{{ request('q') }}" style="flex:1;min-width:160px">
    <select name="category">
        <option value="">Tất cả danh mục</option>
        @foreach($categories as $cat)
        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
        @endforeach
    </select>
    <select name="brand">
        <option value="">Tất cả thương hiệu</option>
        @foreach($brands as $b)
        <option value="{{ $b->id }}" {{ request('brand') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
        @endforeach
    </select>
    <select name="status">
        <option value="">Tất cả trạng thái</option>
        <option value="active"   {{ request('status')=='active'   ? 'selected' : '' }}>Đang hiển thị</option>
        <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>Đã ẩn</option>
    </select>
    <button type="submit"><i class="fas fa-search"></i> Lọc</button>
    <a href="{{ route('admin.products.index') }}" style="padding:8px 12px;font-size:13px;color:#666;text-decoration:none">Xóa bộ lọc</a>
</form>

{{-- Bảng sản phẩm --}}
<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Ảnh</th>
                <th>Tên sản phẩm</th>
                <th>Danh mục</th>
                <th>Thương hiệu</th>
                <th>Giá (đ)</th>
                <th>Giảm</th>
                <th>Số lượng</th>
                <th>ĐG</th>
                <th>Hiển thị</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
            <tr>
                <td style="color:#aaa">{{ $product->id }}</td>
                <td>
                    @if($product->first_image)
                        <img class="product-thumb" src="{{ $product->first_image }}" alt="">
                    @else
                        <div class="product-thumb"><i class="fas fa-image" style="color:#ccc"></i></div>
                    @endif
                </td>
                <td>
                    <a href="{{ route('products.show', $product->slug) }}" target="_blank"
                       style="font-weight:600;color:#1565C0;text-decoration:none">
                        {{ Str::limit($product->name, 50) }}
                    </a>
                </td>
                <td>{{ $product->category->name ?? '—' }}</td>
                <td>{{ $product->brand->name ?? '—' }}</td>
                <td>{{ number_format($product->price) }}</td>
                <td>{{ $product->discount_percent > 0 ? $product->discount_percent.'%' : '—' }}</td>
                <td>
                    <span class="stock-wrap" style="{{ $product->stock <= 5 ? 'color:#E53935;font-weight:600' : '' }}">
                        <span class="stock-val">{{ number_format($product->stock) }}</span>
                        <button class="btn-add-stock" data-id="{{ $product->id }}" title="Thêm số lượng"
                            style="background:none;border:1px solid #1565C0;color:#1565C0;border-radius:4px;padding:1px 6px;font-size:11px;cursor:pointer;margin-left:4px">
                            +
                        </button>
                    </span>
                </td>
                <td>
                    <span style="color:#FFA000">★</span>
                    {{ $product->avg_rating }}
                    <span style="color:#aaa">({{ $product->reviews_count }})</span>
                </td>
                <td>
                    <button class="toggle-btn"
                            data-id="{{ $product->id }}"
                            data-active="{{ $product->is_active ? '1' : '0' }}"
                            title="{{ $product->is_active ? 'Ẩn sản phẩm' : 'Hiển thị sản phẩm' }}">
                        @if($product->is_active)
                            <span class="status-badge status-on">Hiển thị</span>
                        @else
                            <span class="status-badge status-off">Đã ẩn</span>
                        @endif
                    </button>
                </td>
                <td>
                    <div style="display:flex;gap:6px;align-items:center">
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                              onsubmit="return confirm('Chuyển sản phẩm này vào thùng rác?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="11" style="text-align:center;padding:40px;color:#aaa">
                    Chưa có sản phẩm nào.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:16px">{{ $products->links() }}</div>
@endsection

@push('scripts')
<script>
// Toggle hiển thị / ẩn sản phẩm
document.querySelectorAll('.toggle-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        fetch(`/admin/san-pham/${id}/toggle-active`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        })
        .then(r => r.json())
        .then(data => {
            const badge = this.querySelector('.status-badge');
            if (data.is_active) {
                badge.className = 'status-badge status-on';
                badge.textContent = 'Hiển thị';
            } else {
                badge.className = 'status-badge status-off';
                badge.textContent = 'Đã ẩn';
            }
        });
    });
});

// Thêm số lượng
document.querySelectorAll('.btn-add-stock').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.stopPropagation();
        const id = this.dataset.id;
        const qty = prompt('Nhập số lượng cần thêm:', '');
        if (!qty || isNaN(qty) || parseInt(qty) <= 0) return;
        fetch(`/admin/san-pham/${id}/them-so-luong`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ quantity: parseInt(qty) })
        })
        .then(r => r.json())
        .then(data => {
            if (data.stock !== undefined) {
                const wrap = this.closest('.stock-wrap');
                wrap.querySelector('.stock-val').textContent = data.stock.toLocaleString('vi-VN');
                wrap.style = data.stock <= 5 ? 'color:#E53935;font-weight:600' : '';
            }
        });
    });
});
</script>
@endpush