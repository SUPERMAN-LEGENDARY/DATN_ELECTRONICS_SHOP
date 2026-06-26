@extends('layouts.admin')

@section('title', 'Sự kiện / Khuyến mãi')

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

    .ev-thumb {
        width: 70px;
        height: 44px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #eee;
    }

    .ev-placeholder {
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
        white-space: nowrap;
    }

    .badge-success {
        background: #E8F5E9;
        color: #2E7D32;
    }

    .badge-muted {
        background: #f0f0f0;
        color: #999;
    }

    .badge-info {
        background: #E3F2FD;
        color: #1565C0;
    }

    .badge-warn {
        background: #FFF3E0;
        color: #E65100;
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

    .bulk-bar {
        display: none;
        align-items: center;
        gap: 12px;
        background: #E3F2FD;
        border: 1px solid #BBDEFB;
        border-radius: 8px;
        padding: 10px 14px;
        margin-bottom: 14px;
        font-size: 13px;
        color: #0D47A1;
    }

    .bulk-bar.active {
        display: flex;
    }

    .btn-bulk {
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        border: none;
        cursor: pointer;
    }

    .btn-bulk-show {
        background: #2E7D32;
        color: #fff;
    }

    .btn-bulk-show:hover {
        background: #1B5E20;
    }

    .btn-bulk-hide {
        background: #F57F17;
        color: #fff;
    }

    .btn-bulk-hide:hover {
        background: #E65100;
    }

    th.checkbox-col,
    td.checkbox-col {
        width: 36px;
        text-align: center;
    }
</style>
@endpush

@section('content')

<div class="page-header">
    <h2><i class="fas fa-gift"></i> Sự kiện / Khuyến mãi</h2>
    <div style="display:flex;gap:10px;align-items:center">
        <a href="{{ route('admin.events.trash') }}" class="btn btn-trash">
            <i class="fas fa-trash-alt"></i> Thùng rác
            @if($trashedCount > 0)
            <span class="trash-badge">{{ $trashedCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Thêm sự kiện
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
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo tên sự kiện, nhãn...">
    <button type="submit" class="btn btn-outline"><i class="fas fa-search"></i> Tìm</button>
    @if(request('search'))
    <a href="{{ route('admin.events.index') }}" class="btn btn-outline">
        <i class="fas fa-times"></i> Xóa lọc
    </a>
    @endif
</form>

<div class="bulk-bar" id="bulkBar">
    <strong id="selectedCount">0</strong> sự kiện được chọn
    <button type="button" class="btn-bulk btn-bulk-show" onclick="submitBulk('show')">
        <i class="fas fa-eye"></i> Hiển thị
    </button>
    <button type="button" class="btn-bulk btn-bulk-hide" onclick="submitBulk('hide')">
        <i class="fas fa-eye-slash"></i> Ẩn
    </button>
</div>

<div class="card">
    @if($events->isEmpty())
    <div class="empty-state">
        <i class="fas fa-gift"></i>
        <p>Chưa có sự kiện nào. Hãy thêm sự kiện như Giáng Sinh, Tết... để hiển thị ưu đãi trên trang chủ.</p>
    </div>
    @else
    <table>
        <thead>
            <tr>
                <th class="checkbox-col"><input type="checkbox" id="checkAll" title="Chọn tất cả"></th>
                <th style="width:50px">#</th>
                <th style="width:90px">Ảnh</th>
                <th>Tên sự kiện</th>
                <th>Ưu đãi</th>
                <th style="width:170px">Thời gian</th>
                <th style="width:80px">Thứ tự</th>
                <th style="width:110px">Trạng thái</th>
                <th style="width:160px">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @foreach($events as $event)
            <tr>
                <td class="checkbox-col"><input type="checkbox" class="row-check" value="{{ $event->id }}"></td>
                <td style="color:#aaa">{{ $event->id }}</td>
                <td>
                    @if($event->image)
                    <img src="{{ $event->image }}" class="ev-thumb" alt="{{ $event->title }}">
                    @else
                    <div class="ev-placeholder"><i class="fas fa-image"></i></div>
                    @endif
                </td>
                <td>
                    @if($event->tag)
                    <div style="font-size:11px;font-weight:700;color:#C62828;text-transform:uppercase">{{ $event->tag }}</div>
                    @endif
                    <div style="font-weight:600">{{ $event->title }}</div>
                </td>
                <td style="color:#E53935;font-weight:700;font-size:13px">{{ $event->offer_text ?: '—' }}</td>
                <td style="font-size:12px;color:#666">
                    @if($event->start_date || $event->end_date)
                    {{ $event->start_date?->format('d/m/Y') ?: '...' }} → {{ $event->end_date?->format('d/m/Y') ?: '...' }}
                    @else
                    <span style="color:#bbb">Không giới hạn</span>
                    @endif
                </td>
                <td>{{ $event->sort_order }}</td>
                <td>
                    @php($status = $event->statusLabel())
                    <span class="badge
                        {{ $status === 'Đang diễn ra' ? 'badge-success' : '' }}
                        {{ $status === 'Đã ẩn' ? 'badge-muted' : '' }}
                        {{ $status === 'Chưa bắt đầu' ? 'badge-info' : '' }}
                        {{ $status === 'Đã kết thúc' ? 'badge-warn' : '' }}">
                        {{ $status }}
                    </span>
                </td>
                <td>
                    <div class="actions">
                        <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-sm btn-outline" title="Sửa">
                            <i class="fas fa-edit"></i>
                        </a>

                        <form method="POST" action="{{ route('admin.events.toggle-active', $event) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="btn btn-sm {{ $event->is_active ? 'btn-warning' : 'btn-success' }}"
                                title="{{ $event->is_active ? 'Ẩn' : 'Hiển thị' }}">
                                <i class="fas fa-{{ $event->is_active ? 'eye-slash' : 'eye' }}"></i>
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.events.destroy', $event) }}"
                            onsubmit="return confirm('Chuyển sự kiện \'{{ addslashes($event->title) }}\' vào thùng rác?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Xóa (chuyển vào thùng rác)">
                                <i class="fas fa-trash"></i>
                            </button>
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

@push('scripts')
<script>
    const checkAll = document.getElementById('checkAll');
    const rowChecks = document.querySelectorAll('.row-check');
    const bulkBar = document.getElementById('bulkBar');
    const countEl = document.getElementById('selectedCount');

    function updateBulk() {
        const checked = document.querySelectorAll('.row-check:checked').length;
        countEl.textContent = checked;
        bulkBar.classList.toggle('active', checked > 0);
    }

    checkAll?.addEventListener('change', function() {
        rowChecks.forEach(c => c.checked = this.checked);
        updateBulk();
    });
    rowChecks.forEach(c => c.addEventListener('change', updateBulk));

    // Gửi bulk show/hide bằng form tạo động (không bọc cả bảng trong <form> để tránh lồng form)
    function submitBulk(action) {
        const ids = Array.from(document.querySelectorAll('.row-check:checked')).map(c => c.value);
        if (ids.length === 0) return;

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('admin.events.bulk-toggle') }}";
        form.style.display = 'none';

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = "{{ csrf_token() }}";
        form.appendChild(csrf);

        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = action;
        form.appendChild(actionInput);

        ids.forEach(id => {
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'ids[]';
            idInput.value = id;
            form.appendChild(idInput);
        });

        document.body.appendChild(form);
        form.submit();
    }
</script>
@endpush