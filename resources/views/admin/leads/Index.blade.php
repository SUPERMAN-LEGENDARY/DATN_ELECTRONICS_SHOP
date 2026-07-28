@extends('layouts.admin')

@section('title', 'Phân tích khách hàng tiềm năng')

@section('content')
<div style="max-width:1200px">

    {{-- ── Flash messages ─────────────────────────────────── --}}
    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

    {{-- ── Tiêu đề ─────────────────────────────────────────── --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;gap:16px;flex-wrap:wrap">
        <div>
            <h1 style="font-size:22px;font-weight:700;margin:0">Phân tích khách hàng tiềm năng</h1>
            <p style="color:#888;font-size:13px;margin:4px 0 0">Dữ liệu hành vi & điểm AI từ hệ thống</p>
        </div>
        <div style="text-align:right">
            <input type="hidden" id="csrf-token-all" value="{{ csrf_token() }}">
            <button id="btn-recalculate-all"
                data-url="{{ route('admin.leads.recalculate-all') }}"
                data-status-url="{{ route('admin.leads.recalculate-all.status') }}"
                style="background:#1E88E5;color:#fff;border:none;border-radius:8px;padding:10px 18px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:8px">
                <span id="btn-recalc-all-spinner" style="display:none;width:13px;height:13px;border:2px solid #fff;border-top-color:transparent;border-radius:50%;animation:spin .7s linear infinite"></span>
                <span id="btn-recalc-all-label">🔄 Chấm điểm lại toàn bộ</span>
            </button>
            <div id="recalc-all-note" style="font-size:11px;color:#9ca3af;margin-top:6px">
                @if(($lastScoredAll ?? null))
                    Lần chạy gần nhất: {{ $lastScoredAll }}
                @endif
            </div>
        </div>
    </div>

    <style>
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>

    {{-- ── Stats cards ─────────────────────────────────────── --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px">
        @foreach([
            ['label'=>'🔥 Hot', 'value'=>$stats['hot'],  'color'=>'#fff5f5','border'=>'#fca5a5','text'=>'#b91c1c'],
            ['label'=>'🌡 Warm','value'=>$stats['warm'], 'color'=>'#fffbeb','border'=>'#fcd34d','text'=>'#92400e'],
            ['label'=>'❄️ Cold', 'value'=>$stats['cold'], 'color'=>'#eff6ff','border'=>'#93c5fd','text'=>'#1e40af'],
            ['label'=>'🎁 Gợi voucher','value'=>$stats['voucher_suggested'],'color'=>'#f0fdf4','border'=>'#86efac','text'=>'#166534'],
        ] as $card)
        <div style="background:{{ $card['color'] }};border:1px solid {{ $card['border'] }};border-radius:10px;padding:18px 20px">
            <div style="font-size:13px;color:{{ $card['text'] }};font-weight:600">{{ $card['label'] }}</div>
            <div style="font-size:32px;font-weight:800;color:{{ $card['text'] }};margin-top:6px">{{ $card['value'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- ── Bộ lọc ──────────────────────────────────────────── --}}
    <form method="GET" style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:16px 20px;margin-bottom:20px;display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end">
        <div>
            <label style="font-size:12px;font-weight:600;color:#555;display:block;margin-bottom:4px">Nhãn Lead</label>
            <select name="label" style="border:1px solid #d1d5db;border-radius:6px;padding:7px 10px;font-size:13px">
                <option value="">-- Tất cả --</option>
                <option value="hot"  {{ request('label')=='hot'  ? 'selected':'' }}>🔥 Hot</option>
                <option value="warm" {{ request('label')=='warm' ? 'selected':'' }}>🌡 Warm</option>
                <option value="cold" {{ request('label')=='cold' ? 'selected':'' }}>❄️ Cold</option>
            </select>
        </div>
        <div>
            <label style="font-size:12px;font-weight:600;color:#555;display:block;margin-bottom:4px">Giai đoạn</label>
            <select name="stage" style="border:1px solid #d1d5db;border-radius:6px;padding:7px 10px;font-size:13px">
                <option value="">-- Tất cả --</option>
                @foreach(['awareness','consideration','intent','purchase','retention'] as $s)
                <option value="{{ $s }}" {{ request('stage')==$s ? 'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="font-size:12px;font-weight:600;color:#555;display:block;margin-bottom:4px">Tìm kiếm</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Tên / Email / SĐT..."
                style="border:1px solid #d1d5db;border-radius:6px;padding:7px 12px;font-size:13px;width:220px">
        </div>
        <div style="display:flex;align-items:center;gap:6px;padding-bottom:2px">
            <input type="checkbox" name="voucher_only" value="1" id="vo" {{ request('voucher_only') ? 'checked':'' }}>
            <label for="vo" style="font-size:13px;cursor:pointer">Chỉ hiển thị gợi voucher</label>
        </div>
        <div style="display:flex;gap:8px">
            <button type="submit" style="background:#1E88E5;color:#fff;border:none;border-radius:6px;padding:8px 18px;font-size:13px;font-weight:600;cursor:pointer">
                <i class="fas fa-search"></i> Lọc
            </button>
            <a href="{{ route('admin.leads.index') }}" style="background:#f3f4f6;color:#374151;border:1px solid #d1d5db;border-radius:6px;padding:8px 14px;font-size:13px;font-weight:600;text-decoration:none">
                Xoá lọc
            </a>
        </div>
    </form>

    {{-- ── Bảng danh sách ──────────────────────────────────── --}}
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden">
        <table style="width:100%;border-collapse:collapse;font-size:13px">
            <thead>
                <tr style="background:#f9fafb;border-bottom:1px solid #e5e7eb">
                    <th style="padding:12px 16px;text-align:left;font-weight:700;color:#374151">Khách hàng</th>
                    <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151">Nhãn</th>
                    <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151">Điểm AI</th>
                    <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151">View</th>
                    <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151">Chat</th>
                    <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151">Order</th>
                    <th style="padding:12px 16px;text-align:left;font-weight:700;color:#374151">SP quan tâm nhất</th>
                    <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151">Voucher</th>
                    <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151">Cập nhật</th>
                    <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($profiles as $profile)
                @php
                    $labelMeta = [
                        'hot'  => ['bg'=>'#fee2e2','color'=>'#b91c1c','icon'=>'🔥'],
                        'warm' => ['bg'=>'#fef9c3','color'=>'#92400e','icon'=>'🌡'],
                        'cold' => ['bg'=>'#dbeafe','color'=>'#1e40af','icon'=>'❄️'],
                    ][$profile->lead_label] ?? ['bg'=>'#f3f4f6','color'=>'#374151','icon'=>'—'];
                @endphp
                <tr style="border-bottom:1px solid #f3f4f6" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background=''">
                    <td style="padding:14px 16px">
                        <div style="font-weight:600;color:#111">{{ $profile->user->name }}</div>
                        <div style="color:#888;font-size:12px">{{ $profile->user->email }}</div>
                        @if($profile->user->phone)
                        <div style="color:#aaa;font-size:11px">{{ $profile->user->phone }}</div>
                        @endif
                    </td>
                    <td style="padding:14px 16px;text-align:center">
                        <span style="background:{{ $labelMeta['bg'] }};color:{{ $labelMeta['color'] }};padding:3px 10px;border-radius:20px;font-weight:700;font-size:12px">
                            {{ $labelMeta['icon'] }} {{ strtoupper($profile->lead_label) }}
                        </span>
                        @if($profile->lead_stage)
                        <div style="font-size:11px;color:#888;margin-top:3px">{{ $profile->lead_stage }}</div>
                        @endif
                    </td>
                    <td style="padding:14px 16px;text-align:center;font-weight:800;font-size:16px;color:#1E88E5">
                        {{ number_format($profile->total_score, 1) }}
                    </td>
                    <td style="padding:14px 16px;text-align:center;color:#6b7280">{{ number_format($profile->score_view, 1) }}</td>
                    <td style="padding:14px 16px;text-align:center;color:#6b7280">{{ number_format($profile->score_chat, 1) }}</td>
                    <td style="padding:14px 16px;text-align:center;color:#6b7280">{{ number_format($profile->score_order, 1) }}</td>
                    <td style="padding:14px 16px;max-width:200px">
                        @if($profile->topInterestProduct)
                            <div style="font-size:12px;color:#374151;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                {{ $profile->topInterestProduct->name }}
                            </div>
                        @else
                            <span style="color:#ccc;font-size:12px">—</span>
                        @endif
                    </td>
                    <td style="padding:14px 16px;text-align:center">
                        @if($profile->voucher_recommended)
                            <span style="background:#d1fae5;color:#065f46;padding:3px 8px;border-radius:20px;font-size:11px;font-weight:700">🎁 Gợi ý</span>
                        @else
                            <span style="color:#d1d5db">—</span>
                        @endif
                    </td>
                    <td style="padding:14px 16px;text-align:center;font-size:11px;color:#9ca3af">
                        {{ $profile->scored_at ? $profile->scored_at->format('d/m H:i') : '—' }}
                    </td>
                    <td style="padding:14px 16px;text-align:center">
                        <a href="{{ route('admin.leads.show', $profile->user_id) }}"
                            style="background:#1E88E5;color:#fff;text-decoration:none;padding:6px 14px;border-radius:6px;font-size:12px;font-weight:600">
                            Chi tiết
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" style="padding:48px;text-align:center;color:#aaa">
                        <div style="font-size:40px;margin-bottom:8px">🔍</div>
                        Không tìm thấy khách hàng nào phù hợp
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── Phân trang ──────────────────────────────────────── --}}
    @if($profiles->hasPages())
    <div style="margin-top:20px;display:flex;justify-content:flex-end">
        {{ $profiles->links() }}
    </div>
    @endif

</div>

<script>
(function () {
    const btn = document.getElementById('btn-recalculate-all');
    const spinner = document.getElementById('btn-recalc-all-spinner');
    const label = document.getElementById('btn-recalc-all-label');
    const note = document.getElementById('recalc-all-note');
    const csrfToken = document.getElementById('csrf-token-all').value;

    let pollTimer = null;

    function setRunningUI(isRunning) {
        btn.disabled = isRunning;
        spinner.style.display = isRunning ? 'inline-block' : 'none';
        label.textContent = isRunning ? 'Đang chấm điểm toàn bộ...' : '🔄 Chấm điểm lại toàn bộ';
    }

    function pollStatus() {
        fetch(btn.dataset.statusUrl, { headers: { 'Accept': 'application/json' } })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                setRunningUI(data.running);

                if (data.last_error) {
                    note.textContent = 'Lỗi lần chạy gần nhất: ' + data.last_error;
                    note.style.color = '#b91c1c';
                } else if (data.last_completed_at) {
                    note.textContent = 'Lần chạy gần nhất: ' + data.last_completed_at;
                    note.style.color = '#9ca3af';
                }

                if (data.running) {
                    pollTimer = setTimeout(pollStatus, 4000);
                } else if (pollTimer) {
                    clearTimeout(pollTimer);
                    pollTimer = null;
                    // Job vừa xong -> tải lại danh sách để thấy điểm mới nhất
                    if (btn.dataset.wasPolling === '1') {
                        window.location.reload();
                    }
                }
            })
            .catch(function () {
                // Bỏ qua lỗi polling tạm thời, không làm phiền admin
            });
    }

    btn.addEventListener('click', function () {
        if (btn.disabled) return;
        setRunningUI(true);

        fetch(btn.dataset.url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        })
        .then(function (res) { return res.json().then(function (data) { return { status: res.status, data: data }; }); })
        .then(function (result) {
            if (!result.data.success) {
                throw new Error(result.data.message || 'Có lỗi xảy ra');
            }
            note.textContent = result.data.message;
            note.style.color = '#9ca3af';
            btn.dataset.wasPolling = '1';
            pollTimer = setTimeout(pollStatus, 3000);
        })
        .catch(function (err) {
            setRunningUI(false);
            note.textContent = err.message || 'Không thể bắt đầu chấm điểm, vui lòng thử lại.';
            note.style.color = '#b91c1c';
        });
    });

    // Nếu vào trang mà job đang chạy sẵn (do admin khác bấm), tự động hiện trạng thái
    pollStatus();
})();
</script>
@endsection