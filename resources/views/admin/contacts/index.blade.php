@extends('layouts.admin')
@section('title', 'Quản lý Liên hệ')

@push('styles')
<style>
/* ── Layout ── */
.page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
.page-header h1 { font-size:20px; font-weight:800; color:#1a1a2e; margin:0; display:flex; align-items:center; gap:8px; }
.page-header h1 i { color:#1565C0; }

/* ── Stats cards ── */
.stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:24px; }
.stat-card {
    background:#fff; border-radius:10px; padding:18px 20px;
    box-shadow:0 1px 4px rgba(0,0,0,.07);
    display:flex; align-items:center; gap:14px;
    border-left:4px solid transparent;
}
.stat-card.total   { border-color:#1565C0; }
.stat-card.new     { border-color:#f59e0b; }
.stat-card.process { border-color:#3b82f6; }
.stat-card.done    { border-color:#10b981; }
.stat-icon { width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0; }
.stat-card.total   .stat-icon { background:#e3f2fd; color:#1565C0; }
.stat-card.new     .stat-icon { background:#fef3c7; color:#d97706; }
.stat-card.process .stat-icon { background:#dbeafe; color:#2563eb; }
.stat-card.done    .stat-icon { background:#d1fae5; color:#059669; }
.stat-val { font-size:26px; font-weight:800; color:#1a1a2e; line-height:1; }
.stat-lbl { font-size:12px; color:#6b7280; margin-top:3px; font-weight:500; }

/* ── Filter bar ── */
.filter-bar { display:flex; gap:10px; margin-bottom:16px; flex-wrap:wrap; }
.filter-bar input,
.filter-bar select { border:1px solid #e0e0e0; border-radius:6px; padding:8px 12px; font-size:13px; outline:none; background:#fff; }
.filter-bar input:focus,
.filter-bar select:focus { border-color:#1565C0; }
.btn-filter { background:#1565C0; color:#fff; border:none; border-radius:6px; padding:8px 18px; font-size:13px; font-weight:600; cursor:pointer; }
.btn-filter:hover { background:#0D47A1; }

/* ── Alerts ── */
.alert-success { background:#E8F5E9;border:1px solid #A5D6A7;color:#2E7D32;padding:10px 16px;border-radius:6px;margin-bottom:16px;font-size:14px; }
.alert-danger  { background:#FFEBEE;border:1px solid #FFCDD2;color:#C62828;padding:10px 16px;border-radius:6px;margin-bottom:16px;font-size:14px; }

/* ── Table ── */
.table-wrap { overflow-x:auto; border-radius:10px; box-shadow:0 1px 4px rgba(0,0,0,.07); background:#fff; }
table.data-table { width:100%; border-collapse:collapse; font-size:13px; }
.data-table thead tr { background:#1565C0; }
.data-table th { padding:12px 14px; text-align:left; font-weight:700; font-size:13px; color:#fff; white-space:nowrap; border:none; }
.data-table th:first-child { border-radius:8px 0 0 0; }
.data-table th:last-child  { border-radius:0 8px 0 0; }
.data-table tbody tr { border-bottom:1px solid #f0f0f0; transition:background .12s; }
.data-table tbody tr:hover td { background:#f0f6ff; }
.data-table td { padding:11px 14px; vertical-align:middle; color:#333; }

/* ── Badge status ── */
.badge { display:inline-flex; align-items:center; gap:5px; padding:4px 12px; border-radius:20px; font-size:11.5px; font-weight:700; white-space:nowrap; }
.badge-new        { background:#fef3c7; color:#92400e; }
.badge-processing { background:#dbeafe; color:#1e40af; }
.badge-done       { background:#d1fae5; color:#065f46; }

/* ── Action buttons ── */
.btn-view   { display:inline-flex; align-items:center; gap:5px; padding:5px 12px; background:#1565C0; color:#fff; border-radius:5px; font-size:12px; font-weight:600; text-decoration:none; transition:background .2s; }
.btn-view:hover { background:#0D47A1; }
.btn-del { display:inline-flex; align-items:center; gap:5px; padding:5px 10px; background:#fff; border:1px solid #fca5a5; color:#ef4444; border-radius:5px; font-size:12px; font-weight:600; cursor:pointer; transition:all .2s; }
.btn-del:hover { background:#fef2f2; }

/* Unread indicator */
.row-new td:first-child { border-left:3px solid #f59e0b; }

/* Message preview */
.msg-preview { max-width:260px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; color:#6b7280; font-size:12px; }

/* Replied badge */
.replied-tag { font-size:11px; color:#059669; font-weight:600; display:inline-flex; align-items:center; gap:3px; }

@media(max-width:900px) { .stats-grid { grid-template-columns:1fr 1fr; } }
@media(max-width:600px) { .stats-grid { grid-template-columns:1fr; } }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-envelope-open-text"></i> Quản lý Liên hệ</h1>
</div>

{{-- Alerts --}}
@if(session('success'))
    <div class="alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
@endif

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card total">
        <div class="stat-icon"><i class="fas fa-envelope"></i></div>
        <div>
            <div class="stat-val">{{ $stats['total'] }}</div>
            <div class="stat-lbl">Tổng liên hệ</div>
        </div>
    </div>
    <div class="stat-card new">
        <div class="stat-icon"><i class="fas fa-star"></i></div>
        <div>
            <div class="stat-val">{{ $stats['new'] }}</div>
            <div class="stat-lbl">Chưa xử lý</div>
        </div>
    </div>
    <div class="stat-card process">
        <div class="stat-icon"><i class="fas fa-spinner"></i></div>
        <div>
            <div class="stat-val">{{ $stats['processing'] }}</div>
            <div class="stat-lbl">Đang xử lý</div>
        </div>
    </div>
    <div class="stat-card done">
        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
        <div>
            <div class="stat-val">{{ $stats['done'] }}</div>
            <div class="stat-lbl">Đã phản hồi</div>
        </div>
    </div>
</div>

{{-- Filter --}}
<form method="GET" class="filter-bar">
    <input type="text" name="search" placeholder="Tìm tên, email, chủ đề..." value="{{ request('search') }}" style="flex:1;min-width:200px">
    <select name="status">
        <option value="">-- Tất cả trạng thái --</option>
        <option value="new"        {{ request('status') === 'new'        ? 'selected' : '' }}>Chưa xử lý</option>
        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Đang xử lý</option>
        <option value="done"       {{ request('status') === 'done'       ? 'selected' : '' }}>Đã phản hồi</option>
    </select>
    <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Tìm</button>
    @if(request()->anyFilled(['search','status']))
        <a href="{{ route('admin.contacts.index') }}" style="padding:8px 14px;color:#666;font-size:13px;text-decoration:none;border:1px solid #e0e0e0;border-radius:6px;background:#fff;">
            <i class="fas fa-times"></i> Xoá lọc
        </a>
    @endif
</form>

{{-- Table --}}
<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Người gửi</th>
                <th>Chủ đề</th>
                <th>Nội dung</th>
                <th>Trạng thái</th>
                <th>Thời gian</th>
                <th style="width:120px">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($contacts as $contact)
            <tr class="{{ $contact->status === 'new' ? 'row-new' : '' }}">
                <td>{{ $contact->id }}</td>
                <td>
                    <div style="font-weight:600;color:#1a1a2e">{{ $contact->name }}</div>
                    <div style="font-size:12px;color:#6b7280">{{ $contact->email }}</div>
                    @if($contact->phone)
                    <div style="font-size:11px;color:#9ca3af"><i class="fas fa-phone" style="font-size:10px"></i> {{ $contact->phone }}</div>
                    @endif
                </td>
                <td>
                    <div style="font-weight:600;max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                        {{ $contact->subject ?: '(Không có chủ đề)' }}
                    </div>
                </td>
                <td><div class="msg-preview">{{ $contact->message }}</div></td>
                <td>
                    @if($contact->status === 'new')
                        <span class="badge badge-new"><i class="fas fa-circle" style="font-size:7px"></i> Chưa xử lý</span>
                    @elseif($contact->status === 'processing')
                        <span class="badge badge-processing"><i class="fas fa-spinner"></i> Đang xử lý</span>
                    @else
                        <span class="badge badge-done"><i class="fas fa-check"></i> Đã phản hồi</span>
                        @if($contact->replied_at)
                        <div class="replied-tag" style="margin-top:4px">
                            <i class="fas fa-reply" style="font-size:10px"></i>
                            {{ $contact->replied_at->diffForHumans() }}
                        </div>
                        @endif
                    @endif
                </td>
                <td style="font-size:12px;color:#6b7280;white-space:nowrap">
                    {{ $contact->created_at->format('d/m/Y H:i') }}
                </td>
                <td>
                    <div style="display:flex;gap:6px;flex-wrap:wrap">
                        <a href="{{ route('admin.contacts.show', $contact) }}" class="btn-view">
                            <i class="fas fa-eye"></i> Xem
                        </a>
                        <form method="POST" action="{{ route('admin.contacts.destroy', $contact) }}"
                              onsubmit="return confirm('Xoá liên hệ này?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-del"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;padding:48px;color:#9ca3af">
                    <i class="fas fa-inbox" style="font-size:36px;margin-bottom:10px;display:block"></i>
                    Không có liên hệ nào
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if($contacts->hasPages())
<div style="margin-top:16px;display:flex;justify-content:flex-end">
    {{ $contacts->links() }}
</div>
@endif
@endsection
