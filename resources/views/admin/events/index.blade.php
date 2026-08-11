@extends('layouts.admin')

@section('title', 'Quản lý Event')

@push('styles')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 22px;
    }

    .page-header h2 {
        font-size: 22px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 4px;
    }

    .page-header p {
        font-size: 13px;
        color: #888;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 18px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        text-decoration: none;
        transition: .15s;
    }

    .btn-primary {
        background: #5B4CF0;
        color: #fff;
        box-shadow: 0 2px 6px rgba(91, 76, 240, .3);
    }

    .btn-primary:hover {
        background: #4a3cd6;
    }

    .btn-sm {
        padding: 6px 10px;
        font-size: 12px;
    }

    .btn-outline {
        background: #fff;
        border: 1px solid #e0e0e0;
        color: #555;
    }

    .btn-outline:hover {
        background: #f5f5f5;
    }

    /* Stat cards */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 20px;
    }

    .stat-card {
        background: #fff;
        border-radius: 12px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .06);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .stat-icon.blue { background: #E3F2FD; color: #1565C0; }
    .stat-icon.green { background: #E8F5E9; color: #2E7D32; }
    .stat-icon.orange { background: #FFF3E0; color: #E65100; }
    .stat-icon.gray { background: #ECEFF1; color: #607D8B; }

    .stat-label {
        font-size: 12px;
        color: #888;
        margin-bottom: 2px;
    }

    .stat-value {
        font-size: 22px;
        font-weight: 700;
        color: #222;
    }

    /* Filter toolbar */
    .toolbar-card {
        background: #fff;
        border-radius: 12px;
        padding: 14px 16px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .06);
        margin-bottom: 18px;
    }

    .toolbar {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .toolbar input[type="text"],
    .toolbar select {
        padding: 9px 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 13px;
        color: #444;
    }

    .toolbar input[type="text"] {
        flex: 1;
        min-width: 200px;
    }

    .toolbar select {
        min-width: 130px;
    }

    .toolbar input:focus,
    .toolbar select:focus {
        outline: none;
        border-color: #5B4CF0;
    }

    /* Main layout: table + sidebar */
    .layout-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 18px;
        align-items: start;
    }

    .card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .06);
        overflow: hidden;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    th {
        background: #fafafa;
        padding: 12px 14px;
        text-align: left;
        font-weight: 700;
        font-size: 12px;
        color: #888;
        border-bottom: 1px solid #eee;
    }

    td {
        padding: 12px 14px;
        border-bottom: 1px solid #f2f2f2;
        vertical-align: middle;
    }

    tr:last-child td {
        border-bottom: none;
    }

    tr:hover td {
        background: #fafbff;
    }

    .ev-thumb {
        width: 64px;
        height: 44px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #eee;
    }

    .ev-title {
        font-weight: 700;
        color: #222;
        margin-bottom: 2px;
    }

    .ev-desc {
        font-size: 12px;
        color: #999;
    }

    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .badge-success { background: #E8F5E9; color: #2E7D32; }
    .badge-muted   { background: #f0f0f0; color: #999; }
    .badge-info    { background: #FFF3E0; color: #E65100; }
    .badge-type-flash { background: #F3E5F5; color: #7B1FA2; }
    .badge-type-promo { background: #E3F2FD; color: #1565C0; }

    .actions {
        display: flex;
        gap: 6px;
    }

    .icon-btn {
        width: 30px;
        height: 30px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #eee;
        background: #fff;
        color: #666;
        cursor: pointer;
        text-decoration: none;
    }

    .icon-btn:hover { background: #f5f5f5; }
    .icon-btn.danger { color: #C62828; }
    .icon-btn.danger:hover { background: #FFEBEE; }

    .pagination-wrap {
        padding: 14px 16px;
        border-top: 1px solid #f0f0f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
        color: #888;
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

    /* Sidebar preview */
    .preview-card {
        padding: 18px;
    }

    .preview-card h3 {
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 14px;
    }

    .preview-banner {
        width: 100%;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 16px;
        aspect-ratio: 16/9;
        background: linear-gradient(135deg, #C62828, #E53935);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .preview-banner img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .preview-section-label {
        font-size: 12px;
        font-weight: 700;
        color: #555;
        margin-bottom: 10px;
    }

    .countdown-row {
        display: flex;
        gap: 8px;
        margin-bottom: 18px;
    }

    .countdown-box {
        flex: 1;
        background: #C62828;
        color: #fff;
        border-radius: 8px;
        text-align: center;
        padding: 8px 0;
    }

    .countdown-box .num {
        font-size: 18px;
        font-weight: 700;
        display: block;
    }

    .countdown-box .unit {
        font-size: 10px;
        opacity: .85;
    }

    .toggle-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 9px 0;
        border-bottom: 1px solid #f2f2f2;
        font-size: 13px;
        color: #444;
    }

    .toggle-row:last-child { border-bottom: none; }

    .switch {
        position: relative;
        width: 38px;
        height: 21px;
        display: inline-block;
    }

    .switch input { opacity: 0; width: 0; height: 0; }

    .slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background: #ccc;
        border-radius: 20px;
        transition: .2s;
    }

    .slider:before {
        content: "";
        position: absolute;
        height: 15px;
        width: 15px;
        left: 3px;
        top: 3px;
        background: #fff;
        border-radius: 50%;
        transition: .2s;
    }

    .switch input:checked + .slider {
        background: #5B4CF0;
    }

    .switch input:checked + .slider:before {
        transform: translateX(17px);
    }

    /* Upcoming timeline */
    .timeline-card {
        margin-top: 20px;
        background: #fff;
        border-radius: 12px;
        padding: 18px 20px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .06);
    }

    .timeline-card h3 {
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 16px;
    }

    .timeline-scroll {
        display: flex;
        gap: 14px;
        overflow-x: auto;
        padding-bottom: 6px;
    }

    .timeline-item {
        flex: 0 0 220px;
        display: flex;
        gap: 10px;
        border: 1px solid #eee;
        border-radius: 10px;
        padding: 12px;
    }

    .timeline-icon {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 15px;
    }

    .timeline-icon.orange { background: #FFF3E0; color: #E65100; }
    .timeline-icon.blue { background: #E3F2FD; color: #1565C0; }
    .timeline-icon.gray { background: #ECEFF1; color: #607D8B; }
    .timeline-icon.purple { background: #F3E5F5; color: #7B1FA2; }

    .timeline-item .t-name {
        font-weight: 700;
        font-size: 13px;
        margin-bottom: 2px;
    }

    .timeline-item .t-date {
        font-size: 11px;
        color: #999;
        margin-bottom: 6px;
    }

    @media (max-width: 1100px) {
        .layout-grid { grid-template-columns: 1fr; }
        .stats-row { grid-template-columns: repeat(2, 1fr); }
    }
</style>
@endpush

@section('content')

<div class="page-header">
    <div>
        <h2><i class="fas fa-gift"></i> Quản lý Event</h2>
        <p>Tạo và quản lý các sự kiện, chương trình khuyến mãi trên website</p>
    </div>
    <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tạo Event mới
    </a>
</div>

@if(session('success'))
<div class="alert-success">✓ {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert-error">✕ {{ session('error') }}</div>
@endif

{{-- Stat cards --}}
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-gift"></i></div>
        <div>
            <div class="stat-label">Tổng Event</div>
            <div class="stat-value">{{ $totalCount ?? $events->total() }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-play"></i></div>
        <div>
            <div class="stat-label">Đang diễn ra</div>
            <div class="stat-value">{{ $ongoingCount ?? 0 }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
        <div>
            <div class="stat-label">Sắp diễn ra</div>
            <div class="stat-value">{{ $upcomingCount ?? 0 }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon gray"><i class="fas fa-box-archive"></i></div>
        <div>
            <div class="stat-label">Đã kết thúc</div>
            <div class="stat-value">{{ $endedCount ?? 0 }}</div>
        </div>
    </div>
</div>

{{-- Filter toolbar --}}
<div class="toolbar-card">
    <form method="GET" class="toolbar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm event...">

        <select name="status">
            <option value="">-- Trạng thái --</option>
            <option value="ongoing" @selected(request('status')=='ongoing' )>Đang diễn ra</option>
            <option value="upcoming" @selected(request('status')=='upcoming' )>Sắp diễn ra</option>
            <option value="draft" @selected(request('status')=='draft' )>Nháp</option>
            <option value="ended" @selected(request('status')=='ended' )>Đã kết thúc</option>
        </select>

        <select name="type">
            <option value="">-- Loại event --</option>
            <option value="flash_sale" @selected(request('type')=='flash_sale' )>Flash Sale</option>
            <option value="promotion" @selected(request('type')=='promotion' )>Khuyến mãi</option>
        </select>

        <input type="text" name="date_range" value="{{ request('date_range') }}" placeholder="Chọn khoảng thời gian" onfocus="this.type='date'">

        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tìm kiếm</button>
        <a href="{{ route('admin.events.index') }}" class="btn btn-outline"><i class="fas fa-rotate-left"></i> Đặt lại</a>
    </form>
</div>

<div class="layout-grid">
    {{-- Table --}}
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
                    <th style="width:40px">#</th>
                    <th>Event</th>
                    <th style="width:150px">Thời gian</th>
                    <th style="width:110px">Trạng thái</th>
                    <th style="width:110px">Loại</th>
                    <th style="width:80px">Sản phẩm</th>
                    <th style="width:120px">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($events as $event)
                <tr>
                    <td style="color:#aaa">{{ $loop->iteration }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            @if($event->image)
                            <img src="{{ $event->image }}" class="ev-thumb" alt="{{ $event->title }}">
                            @else
                            <div class="ev-thumb" style="display:flex;align-items:center;justify-content:center;background:#f0f0f0;color:#bbb"><i class="fas fa-image"></i></div>
                            @endif
                            <div>
                                <div class="ev-title">{{ $event->title }}</div>
                                <div class="ev-desc">{{ $event->offer_text ?: $event->tag }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:12px;color:#666">
                        @if($event->start_date || $event->end_date)
                        {{ $event->start_date?->format('d/m/Y H:i') ?: '...' }}<br>
                        {{ $event->end_date?->format('d/m/Y H:i') ?: '...' }}
                        @else
                        <span style="color:#bbb">Không giới hạn</span>
                        @endif
                    </td>
                    <td>
                        @php($status = $event->statusLabel())
                        <span class="badge
                            {{ $status === 'Đang diễn ra' ? 'badge-success' : '' }}
                            {{ $status === 'Đã ẩn' || $status === 'Nháp' ? 'badge-muted' : '' }}
                            {{ $status === 'Sắp diễn ra' ? 'badge-info' : '' }}
                            {{ $status === 'Đã kết thúc' ? 'badge-muted' : '' }}">
                            {{ $status }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $event->type === 'flash_sale' ? 'badge-type-flash' : 'badge-type-promo' }}">
                            {{ $event->type === 'flash_sale' ? 'Flash Sale' : 'Khuyến mãi' }}
                        </span>
                    </td>
                    <td>{{ $event->products_count ?? 0 }}</td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('admin.events.show', $event) }}" class="icon-btn" title="Xem trước">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.events.edit', $event) }}" class="icon-btn" title="Sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.events.destroy', $event) }}"
                                onsubmit="return confirm('Chuyển sự kiện \'{{ addslashes($event->title) }}\' vào thùng rác?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="icon-btn danger" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="pagination-wrap">
            <span>Hiển thị {{ $events->firstItem() }} đến {{ $events->lastItem() }} trong {{ $events->total() }} kết quả</span>
            {{ $events->links() }}
        </div>
        @endif
    </div>

    {{-- Preview sidebar --}}
    <div class="card preview-card">
        <h3>Xem trước Event</h3>

        <div class="preview-banner">
            @if($previewEvent->image ?? false)
            <img src="{{ $previewEvent->image }}" alt="">
            @else
            <i class="fas fa-image" style="color:#fff;font-size:24px;opacity:.6"></i>
            @endif
        </div>

        <div class="preview-section-label">Thời gian diễn ra</div>
        <div class="countdown-row">
            <div class="countdown-box"><span class="num">{{ $countdown['days'] ?? '00' }}</span><span class="unit">Ngày</span></div>
            <div class="countdown-box"><span class="num">{{ $countdown['hours'] ?? '00' }}</span><span class="unit">Giờ</span></div>
            <div class="countdown-box"><span class="num">{{ $countdown['minutes'] ?? '00' }}</span><span class="unit">Phút</span></div>
            <div class="countdown-box"><span class="num">{{ $countdown['seconds'] ?? '00' }}</span><span class="unit">Giây</span></div>
        </div>

        <div class="preview-section-label">Thông tin hiển thị</div>
        <div class="toggle-row">
            Hiển thị banner trang chủ
            <label class="switch"><input type="checkbox" checked><span class="slider"></span></label>
        </div>
        <div class="toggle-row">
            Hiển thị countdown
            <label class="switch"><input type="checkbox" checked><span class="slider"></span></label>
        </div>
        <div class="toggle-row">
            Hiển thị danh sách sản phẩm
            <label class="switch"><input type="checkbox" checked><span class="slider"></span></label>
        </div>
        <div class="toggle-row">
            Hiển thị voucher
            <label class="switch"><input type="checkbox" checked><span class="slider"></span></label>
        </div>
    </div>
</div>

{{-- Upcoming events timeline --}}
@if(isset($upcomingEvents) && $upcomingEvents->isNotEmpty())
<div class="timeline-card">
    <h3>Lịch trình các Event sắp tới</h3>
    <div class="timeline-scroll">
        @foreach($upcomingEvents as $ue)
        <div class="timeline-item">
            <div class="timeline-icon {{ ['orange','blue','gray','purple'][$loop->index % 4] }}">
                <i class="fas fa-gift"></i>
            </div>
            <div>
                <div class="t-name">{{ $ue->title }}</div>
                <div class="t-date">{{ $ue->start_date?->format('d/m/Y') }} - {{ $ue->end_date?->format('d/m/Y') }}</div>
                <span class="badge badge-info">{{ $ue->statusLabel() }}</span>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

@endsection