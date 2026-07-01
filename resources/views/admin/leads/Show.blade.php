@extends('layouts.admin')

@section('title', 'Khách hàng: ' . $user->name)

@section('content')
<div style="max-width:1200px">

    <input type="hidden" id="csrf-token" value="{{ csrf_token() }}">

    {{-- ── Flash messages ─────────────────────────────────── --}}
    <div id="flash-box">
        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert-error">{{ session('error') }}</div>
        @endif
    </div>

    {{-- ── Tiêu đề ─────────────────────────────────────────── --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;gap:16px;flex-wrap:wrap">
        <div>
            <a href="{{ route('admin.leads.index') }}" style="color:#1E88E5;text-decoration:none;font-size:13px">&larr; Quay lại danh sách</a>
            <h1 style="font-size:22px;font-weight:700;margin:6px 0 0">{{ $user->name }}</h1>
            <div style="color:#888;font-size:13px;margin-top:4px">
                {{ $user->email }}
                @if($user->phone) &middot; {{ $user->phone }} @endif
            </div>
        </div>
        <button id="btn-recalculate"
            data-url="{{ route('admin.leads.recalculate', $user->id) }}"
            style="background:#1E88E5;color:#fff;border:none;border-radius:8px;padding:11px 20px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:8px;white-space:nowrap">
            <span id="btn-recalculate-spinner" style="display:none;width:13px;height:13px;border:2px solid #fff;border-top-color:transparent;border-radius:50%;animation:spin .7s linear infinite"></span>
            <span id="btn-recalculate-label">⚡ Tính lại điểm AI</span>
        </button>
    </div>

    <style>
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>

    @if(!$profile)
    <div style="background:#fffbeb;border:1px solid #fcd34d;border-radius:10px;padding:20px;color:#92400e;font-size:13px;margin-bottom:20px">
        Khách hàng này chưa có dữ liệu điểm AI (chưa xem sản phẩm / chat / mua hàng nào được ghi nhận).
        Bấm <strong>"Tính lại điểm AI"</strong> phía trên để hệ thống thử tính lại ngay.
    </div>
    @endif

    {{-- ── AI Summary (nhận định do Gemini viết) ────────────── --}}
    <div id="ai-summary-card" style="background:linear-gradient(135deg,#eff6ff,#f5f3ff);border:1px solid #c7d2fe;border-radius:10px;padding:18px 20px;margin-bottom:20px; {{ !$profile?->ai_summary ? 'display:none' : '' }}">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px">
            <span style="font-size:16px">🤖</span>
            <span style="font-weight:700;font-size:13px;color:#3730a3">Nhận định AI</span>
            <span id="ai-summary-time" style="font-size:11px;color:#9ca3af;margin-left:auto">
                {{ $profile?->ai_summary_generated_at ? $profile->ai_summary_generated_at->format('d/m/Y H:i') : '' }}
            </span>
        </div>
        <div id="ai-summary-text" style="font-size:13px;color:#374151;line-height:1.6">{{ $profile?->ai_summary }}</div>
    </div>

    {{-- ── Score cards ─────────────────────────────────────── --}}
    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:20px">
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:16px">
            <div style="font-size:12px;color:#888;font-weight:600">Nhãn tiềm năng</div>
            <div id="lead-label-badge" style="margin-top:8px">
                @php
                    $labelMeta = [
                        'hot'  => ['bg'=>'#fee2e2','color'=>'#b91c1c','icon'=>'🔥'],
                        'warm' => ['bg'=>'#fef9c3','color'=>'#92400e','icon'=>'🌡'],
                        'cold' => ['bg'=>'#dbeafe','color'=>'#1e40af','icon'=>'❄️'],
                    ][$profile->lead_label ?? 'cold'] ?? ['bg'=>'#f3f4f6','color'=>'#374151','icon'=>'—'];
                @endphp
                <span style="background:{{ $labelMeta['bg'] }};color:{{ $labelMeta['color'] }};padding:4px 12px;border-radius:20px;font-weight:700;font-size:13px">
                    {{ $labelMeta['icon'] }} {{ strtoupper($profile->lead_label ?? '—') }}
                </span>
            </div>
            <div id="lead-stage-text" style="font-size:11px;color:#888;margin-top:6px">{{ $profile->lead_stage ?? '' }}</div>
        </div>
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:16px">
            <div style="font-size:12px;color:#888;font-weight:600">Tổng điểm AI</div>
            <div id="score-total" style="font-size:26px;font-weight:800;color:#1E88E5;margin-top:6px">{{ number_format($profile->total_score ?? 0, 1) }}</div>
        </div>
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:16px">
            <div style="font-size:12px;color:#888;font-weight:600">Điểm xem SP</div>
            <div id="score-view" style="font-size:26px;font-weight:800;color:#374151;margin-top:6px">{{ number_format($profile->score_view ?? 0, 1) }}</div>
        </div>
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:16px">
            <div style="font-size:12px;color:#888;font-weight:600">Điểm chat AI</div>
            <div id="score-chat" style="font-size:26px;font-weight:800;color:#374151;margin-top:6px">{{ number_format($profile->score_chat ?? 0, 1) }}</div>
        </div>
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:16px">
            <div style="font-size:12px;color:#888;font-weight:600">Điểm đơn hàng</div>
            <div id="score-order" style="font-size:26px;font-weight:800;color:#374151;margin-top:6px">{{ number_format($profile->score_order ?? 0, 1) }}</div>
        </div>
    </div>

    {{-- ── Voucher gợi ý + từ khoá quan tâm ─────────────────── --}}
    <div style="display:grid;grid-template-columns:1fr 2fr;gap:14px;margin-bottom:20px">
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:16px">
            <div style="font-size:12px;color:#888;font-weight:600;margin-bottom:8px">Gợi ý voucher</div>
            <div id="voucher-badge">
                @if($profile?->voucher_recommended)
                    <span style="background:#d1fae5;color:#065f46;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:700">🎁 {{ $profile->voucher_reason }}</span>
                @else
                    <span style="color:#d1d5db;font-size:13px">Chưa cần gợi ý</span>
                @endif
            </div>
        </div>
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:16px">
            <div style="font-size:12px;color:#888;font-weight:600;margin-bottom:8px">Từ khoá quan tâm (từ tìm kiếm &amp; chat AI)</div>
            <div id="keywords-history-box" style="display:flex;flex-wrap:wrap;gap:6px">
                @forelse($profile->keywords_history ?? [] as $kw)
                    <span style="background:#f3f4f6;color:#374151;padding:3px 10px;border-radius:20px;font-size:12px">{{ $kw }}</span>
                @empty
                    <span style="color:#d1d5db;font-size:13px">Chưa có dữ liệu</span>
                @endforelse
            </div>
        </div>
    </div>

    <div id="scored-at-note" style="font-size:11px;color:#9ca3af;margin-bottom:24px">
        Lần tính điểm gần nhất: {{ $profile?->scored_at ? $profile->scored_at->format('d/m/Y H:i') : '—' }}
    </div>

    {{-- ── Sản phẩm gợi ý AI + đề xuất thủ công ─────────────── --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px">
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:18px 20px">
            <div style="font-weight:700;font-size:14px;margin-bottom:12px">🎯 Sản phẩm AI đang gợi ý</div>
            @forelse($suggestedProducts as $p)
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f3f4f6;font-size:13px">
                    <span>{{ $p->name }}</span>
                    <span style="color:#1E88E5;font-weight:600">{{ number_format($p->price) }}đ</span>
                </div>
            @empty
                <div style="color:#d1d5db;font-size:13px">Chưa có gợi ý nào</div>
            @endforelse
        </div>

        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:18px 20px">
            <div style="font-weight:700;font-size:14px;margin-bottom:12px">✏️ Đề xuất thủ công</div>
            <form method="POST" action="{{ route('admin.leads.update-suggestions', $user->id) }}">
                @csrf
                <div style="max-height:220px;overflow-y:auto;border:1px solid #e5e7eb;border-radius:8px;padding:10px;margin-bottom:12px">
                    @foreach($productOptions as $opt)
                        <label style="display:flex;align-items:center;gap:8px;font-size:12px;padding:4px 0;cursor:pointer">
                            <input type="checkbox" name="product_ids[]" value="{{ $opt->id }}"
                                {{ in_array($opt->id, $profile->suggested_products ?? []) ? 'checked' : '' }}>
                            {{ $opt->name }}
                        </label>
                    @endforeach
                </div>
                <button type="submit" style="background:#374151;color:#fff;border:none;border-radius:6px;padding:8px 16px;font-size:13px;font-weight:600;cursor:pointer">
                    Lưu đề xuất thủ công
                </button>
            </form>
        </div>
    </div>

    {{-- ── Lịch sử xem sản phẩm & Đơn hàng ──────────────────── --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px">
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:18px 20px">
            <div style="font-weight:700;font-size:14px;margin-bottom:12px">👀 Lịch sử hành vi gần đây</div>
            <div style="max-height:280px;overflow-y:auto">
                @forelse($recentViews as $view)
                    <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid #f3f4f6;font-size:12px">
                        <span>{{ $view->product->name ?? '—' }}</span>
                        <span style="color:#9ca3af">{{ $view->event_type }} &middot; {{ $view->created_at->format('d/m H:i') }}</span>
                    </div>
                @empty
                    <div style="color:#d1d5db;font-size:13px">Chưa có lịch sử xem</div>
                @endforelse
            </div>
        </div>

        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:18px 20px">
            <div style="font-weight:700;font-size:14px;margin-bottom:12px">🧾 Đơn hàng gần đây</div>
            <div style="max-height:280px;overflow-y:auto">
                @forelse($orders as $order)
                    <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid #f3f4f6;font-size:12px">
                        <span>#{{ $order->id }} &middot; {{ $order->items->count() }} SP</span>
                        <span>
                            <span style="color:#9ca3af">{{ $order->status }}</span>
                            &middot; <strong>{{ number_format($order->total) }}đ</strong>
                        </span>
                    </div>
                @empty
                    <div style="color:#d1d5db;font-size:13px">Chưa có đơn hàng nào</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── Voucher đã tặng + Form tặng voucher ──────────────── --}}
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:18px 20px;margin-bottom:40px">
        <div style="font-weight:700;font-size:14px;margin-bottom:12px">🎁 Voucher cá nhân hoá</div>

        @if($personalVouchers->isNotEmpty())
        <div style="margin-bottom:16px">
            @foreach($personalVouchers as $v)
                <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid #f3f4f6;font-size:12px">
                    <span><strong>{{ $v->code }}</strong> (-{{ $v->discount_percent }}%)</span>
                    <span style="color:#9ca3af">{{ $v->note }} &middot; hết hạn {{ optional($v->expires_at)->format('d/m/Y') }}</span>
                </div>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('admin.leads.gift-voucher', $user->id) }}" style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end">
            @csrf
            <div>
                <label style="font-size:12px;font-weight:600;color:#555;display:block;margin-bottom:4px">% Giảm giá</label>
                <input type="number" name="discount_percent" min="1" max="90" required
                    style="border:1px solid #d1d5db;border-radius:6px;padding:7px 10px;font-size:13px;width:100px">
            </div>
            <div>
                <label style="font-size:12px;font-weight:600;color:#555;display:block;margin-bottom:4px">Đơn tối thiểu</label>
                <input type="number" name="min_order_value" min="0"
                    style="border:1px solid #d1d5db;border-radius:6px;padding:7px 10px;font-size:13px;width:140px">
            </div>
            <div>
                <label style="font-size:12px;font-weight:600;color:#555;display:block;margin-bottom:4px">Hết hạn</label>
                <input type="date" name="expires_at" required
                    style="border:1px solid #d1d5db;border-radius:6px;padding:7px 10px;font-size:13px">
            </div>
            <div style="flex:1;min-width:180px">
                <label style="font-size:12px;font-weight:600;color:#555;display:block;margin-bottom:4px">Ghi chú</label>
                <input type="text" name="note" placeholder="Vd: cảm ơn khách hàng thân thiết"
                    style="border:1px solid #d1d5db;border-radius:6px;padding:7px 10px;font-size:13px;width:100%">
            </div>
            <button type="submit" style="background:#16a34a;color:#fff;border:none;border-radius:6px;padding:9px 18px;font-size:13px;font-weight:700;cursor:pointer">
                Tặng voucher
            </button>
        </form>
    </div>

</div>

<script>
(function () {
    const btn = document.getElementById('btn-recalculate');
    const spinner = document.getElementById('btn-recalculate-spinner');
    const label = document.getElementById('btn-recalculate-label');
    const csrfToken = document.getElementById('csrf-token').value;

    function fmt1(n) {
        return Number(n ?? 0).toLocaleString('vi-VN', { minimumFractionDigits: 1, maximumFractionDigits: 1 });
    }

    function showFlash(message, type) {
        const box = document.getElementById('flash-box');
        const cls = type === 'error' ? 'alert-error' : 'alert-success';
        box.innerHTML = '<div class="' + cls + '">' + message + '</div>';
    }

    btn.addEventListener('click', function () {
        if (btn.disabled) return;
        btn.disabled = true;
        spinner.style.display = 'inline-block';
        label.textContent = 'Đang tính điểm & phân tích AI...';

        fetch(btn.dataset.url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (!data.success) {
                throw new Error(data.message || 'Có lỗi xảy ra');
            }

            showFlash(data.message, 'success');

            if (data.profile) {
                const p = data.profile;

                document.getElementById('score-total').textContent = fmt1(p.total_score);
                document.getElementById('score-view').textContent = fmt1(p.score_view);
                document.getElementById('score-chat').textContent = fmt1(p.score_chat);
                document.getElementById('score-order').textContent = fmt1(p.score_order);

                const labelMeta = {
                    hot:  { bg: '#fee2e2', color: '#b91c1c', icon: '🔥' },
                    warm: { bg: '#fef9c3', color: '#92400e', icon: '🌡' },
                    cold: { bg: '#dbeafe', color: '#1e40af', icon: '❄️' },
                }[p.lead_label] || { bg: '#f3f4f6', color: '#374151', icon: '—' };

                document.getElementById('lead-label-badge').innerHTML =
                    '<span style="background:' + labelMeta.bg + ';color:' + labelMeta.color + ';padding:4px 12px;border-radius:20px;font-weight:700;font-size:13px">' +
                    labelMeta.icon + ' ' + (p.lead_label || '—').toUpperCase() + '</span>';
                document.getElementById('lead-stage-text').textContent = p.lead_stage || '';

                document.getElementById('voucher-badge').innerHTML = p.voucher_recommended
                    ? '<span style="background:#d1fae5;color:#065f46;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:700">🎁 ' + (p.voucher_reason || '') + '</span>'
                    : '<span style="color:#d1d5db;font-size:13px">Chưa cần gợi ý</span>';

                const kwBox = document.getElementById('keywords-history-box');
                if (p.keywords_history && p.keywords_history.length) {
                    kwBox.innerHTML = p.keywords_history.map(function (kw) {
                        return '<span style="background:#f3f4f6;color:#374151;padding:3px 10px;border-radius:20px;font-size:12px">' + kw + '</span>';
                    }).join('');
                } else {
                    kwBox.innerHTML = '<span style="color:#d1d5db;font-size:13px">Chưa có dữ liệu</span>';
                }

                document.getElementById('scored-at-note').textContent = 'Lần tính điểm gần nhất: ' + (p.scored_at || '—');

                const summaryCard = document.getElementById('ai-summary-card');
                if (p.ai_summary) {
                    document.getElementById('ai-summary-text').textContent = p.ai_summary;
                    document.getElementById('ai-summary-time').textContent = p.ai_summary_generated_at || '';
                    summaryCard.style.display = 'block';
                }
            } else {
                showFlash('Chưa đủ dữ liệu hành vi để tính điểm cho khách này.', 'error');
            }
        })
        .catch(function (err) {
            showFlash(err.message || 'Không thể tính lại điểm, vui lòng thử lại.', 'error');
        })
        .finally(function () {
            btn.disabled = false;
            spinner.style.display = 'none';
            label.textContent = '⚡ Tính lại điểm AI';
        });
    });
})();
</script>
@endsection