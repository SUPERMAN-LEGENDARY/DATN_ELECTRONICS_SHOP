@extends('layouts.admin')
@section('title', 'Chi tiết liên hệ #' . $contact->id)

@push('styles')
<style>
.page-header { display:flex; align-items:center; gap:14px; margin-bottom:24px; flex-wrap:wrap; }
.page-header h1 { font-size:20px; font-weight:800; color:#1a1a2e; margin:0; }
.btn-back { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; background:#fff; border:1px solid #e0e0e0; border-radius:6px; font-size:13px; font-weight:600; color:#555; text-decoration:none; transition:all .2s; }
.btn-back:hover { background:#f5f5f5; color:#1565C0; border-color:#1565C0; }

/* ── 2-col layout ── */
.layout-grid { display:grid; grid-template-columns:1fr 380px; gap:20px; align-items:start; }
@media(max-width:900px) { .layout-grid { grid-template-columns:1fr; } }

/* ── Card ── */
.card { background:#fff; border-radius:12px; box-shadow:0 1px 6px rgba(0,0,0,.08); overflow:hidden; }
.card-header { padding:16px 22px; border-bottom:1px solid #f0f0f0; display:flex; align-items:center; gap:10px; }
.card-header h2 { font-size:15px; font-weight:800; color:#1a1a2e; margin:0; }
.card-header i { color:#1565C0; font-size:16px; }
.card-body { padding:22px; }

/* ── Contact info ── */
.info-row { display:flex; gap:12px; padding:10px 0; border-bottom:1px solid #f8fafc; font-size:14px; }
.info-row:last-child { border-bottom:none; }
.info-label { font-weight:600; color:#6b7280; min-width:90px; flex-shrink:0; }
.info-value { color:#1a1a2e; }

/* Badge status */
.badge { display:inline-flex; align-items:center; gap:5px; padding:4px 12px; border-radius:20px; font-size:11.5px; font-weight:700; }
.badge-new        { background:#fef3c7; color:#92400e; }
.badge-processing { background:#dbeafe; color:#1e40af; }
.badge-done       { background:#d1fae5; color:#065f46; }

/* ── Message box ── */
.message-box {
    background:#f8fafc; border:1px solid #e2e8f0;
    border-radius:8px; padding:18px;
    font-size:14px; line-height:1.8; color:#334155;
    white-space:pre-wrap; margin-top:6px;
}

/* ── Reply already sent ── */
.reply-sent {
    background:linear-gradient(135deg,#f0fdf4,#dcfce7);
    border:1px solid #86efac;
    border-radius:10px; padding:18px 20px;
    margin-bottom:20px;
}
.reply-sent-head { display:flex; align-items:center; gap:8px; font-weight:700; color:#166534; font-size:14px; margin-bottom:10px; }
.reply-sent-head i { font-size:18px; }
.reply-sent-time { font-size:12px; color:#4ade80; margin-left:auto; }
.reply-sent-body { background:#fff; border-radius:6px; padding:14px; font-size:13px; line-height:1.7; color:#333; white-space:pre-wrap; border:1px solid #bbf7d0; }

/* ── Reply form ── */
.reply-form textarea {
    width:100%; border:1px solid #e0e0e0; border-radius:8px;
    padding:14px; font-size:14px; line-height:1.7; resize:vertical;
    font-family:inherit; min-height:160px; outline:none;
    transition:border-color .2s;
}
.reply-form textarea:focus { border-color:#1565C0; }
.btn-send {
    width:100%; padding:12px; background:linear-gradient(135deg,#1565C0,#1976D2);
    color:#fff; border:none; border-radius:8px; font-size:14px; font-weight:700;
    cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px;
    transition:opacity .2s, transform .15s; margin-top:12px;
}
.btn-send:hover { opacity:.9; transform:translateY(-1px); }

/* Alerts */
.alert-success { background:#E8F5E9;border:1px solid #A5D6A7;color:#2E7D32;padding:10px 16px;border-radius:6px;margin-bottom:16px;font-size:14px; }
.alert-danger  { background:#FFEBEE;border:1px solid #FFCDD2;color:#C62828;padding:10px 16px;border-radius:6px;margin-bottom:16px;font-size:14px; }

/* ── Status update form ── */
.status-form { display:flex; gap:8px; margin-top:16px; padding-top:16px; border-top:1px solid #f0f0f0; }
.status-form select { flex:1; border:1px solid #e0e0e0; border-radius:6px; padding:8px 10px; font-size:13px; outline:none; }
.btn-upd { padding:8px 14px; background:#6b7280; color:#fff; border:none; border-radius:6px; font-size:13px; font-weight:600; cursor:pointer; }
.btn-upd:hover { background:#4b5563; }

/* Quick templates */
.template-btns { display:flex; flex-direction:column; gap:6px; margin-top:10px; }
.tmpl-btn {
    padding:7px 12px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px;
    font-size:12px; color:#475569; cursor:pointer; text-align:left; transition:all .2s;
}
.tmpl-btn:hover { background:#eff6ff; border-color:#93c5fd; color:#1565C0; }
</style>
@endpush

@section('content')
<div class="page-header">
    <a href="{{ route('admin.contacts.index') }}" class="btn-back">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
    <h1>Chi tiết liên hệ #{{ $contact->id }}</h1>
    @if($contact->status === 'new')
        <span class="badge badge-new"><i class="fas fa-circle" style="font-size:7px"></i> Chưa xử lý</span>
    @elseif($contact->status === 'processing')
        <span class="badge badge-processing"><i class="fas fa-spinner"></i> Đang xử lý</span>
    @else
        <span class="badge badge-done"><i class="fas fa-check"></i> Đã phản hồi</span>
    @endif
</div>

{{-- Alerts --}}
@if(session('success'))
    <div class="alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="alert-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}</div>
@endif

<div class="layout-grid">

    {{-- ===== CỘT TRÁI: Thông tin liên hệ ===== --}}
    <div>
        {{-- Thông tin người gửi --}}
        <div class="card" style="margin-bottom:20px">
            <div class="card-header">
                <i class="fas fa-user-circle"></i>
                <h2>Thông tin người gửi</h2>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <span class="info-label">Họ tên</span>
                    <span class="info-value" style="font-weight:700">{{ $contact->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email</span>
                    <span class="info-value">
                        <a href="mailto:{{ $contact->email }}" style="color:#1565C0">{{ $contact->email }}</a>
                    </span>
                </div>
                @if($contact->phone)
                <div class="info-row">
                    <span class="info-label">Điện thoại</span>
                    <span class="info-value">
                        <a href="tel:{{ $contact->phone }}" style="color:#1565C0">{{ $contact->phone }}</a>
                    </span>
                </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Thời gian</span>
                    <span class="info-value">{{ $contact->created_at->format('H:i — d/m/Y') }}</span>
                </div>
                @if($contact->subject)
                <div class="info-row">
                    <span class="info-label">Chủ đề</span>
                    <span class="info-value" style="font-weight:600">{{ $contact->subject }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Nội dung liên hệ --}}
        <div class="card">
            <div class="card-header">
                <i class="fas fa-comment-dots"></i>
                <h2>Nội dung liên hệ</h2>
            </div>
            <div class="card-body">
                <div class="message-box">{{ $contact->message }}</div>
            </div>
        </div>

        {{-- Phản hồi đã gửi (nếu có) --}}
        @if($contact->reply_message)
        <div class="card" style="margin-top:20px">
            <div class="card-header">
                <i class="fas fa-reply-all"></i>
                <h2>Phản hồi đã gửi</h2>
            </div>
            <div class="card-body">
                <div class="reply-sent">
                    <div class="reply-sent-head">
                        <i class="fas fa-check-circle"></i>
                        Email phản hồi đã được gửi thành công
                        @if($contact->replied_at)
                        <span class="reply-sent-time">{{ $contact->replied_at->format('H:i d/m/Y') }}</span>
                        @endif
                    </div>
                    <div class="reply-sent-body">{{ $contact->reply_message }}</div>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- ===== CỘT PHẢI: Form phản hồi + cập nhật trạng thái ===== --}}
    <div>
        {{-- Form phản hồi --}}
        <div class="card" style="margin-bottom:20px">
            <div class="card-header">
                <i class="fas fa-paper-plane"></i>
                <h2>{{ $contact->reply_message ? 'Gửi lại phản hồi' : 'Gửi phản hồi' }}</h2>
            </div>
            <div class="card-body">
                <p style="font-size:12px;color:#6b7280;margin-bottom:10px">
                    Email sẽ được gửi tới: <strong>{{ $contact->email }}</strong>
                </p>

                {{-- Mẫu câu nhanh --}}
                <div style="margin-bottom:12px">
                    <div style="font-size:12px;font-weight:600;color:#6b7280;margin-bottom:6px">
                        <i class="fas fa-magic"></i> Mẫu phản hồi nhanh:
                    </div>
                    <div class="template-btns">
                        <button type="button" class="tmpl-btn" onclick="useTemplate(this)" data-text="Cảm ơn bạn đã liên hệ với ElectronicShop. Chúng tôi đã nhận được yêu cầu của bạn và sẽ xử lý trong vòng 24 giờ làm việc. Nếu cần hỗ trợ khẩn, vui lòng gọi hotline 1900 1234.">
                            📨 Đã nhận, sẽ xử lý sớm
                        </button>
                        <button type="button" class="tmpl-btn" onclick="useTemplate(this)" data-text="Cảm ơn bạn đã quan tâm đến sản phẩm của chúng tôi. Sản phẩm bạn hỏi hiện đang có sẵn tại cửa hàng. Bạn có thể xem thêm tại website hoặc đến trực tiếp tại 123 Nguyễn Văn Linh, Đà Nẵng để được tư vấn miễn phí.">
                            🛍️ Tư vấn sản phẩm
                        </button>
                        <button type="button" class="tmpl-btn" onclick="useTemplate(this)" data-text="Cảm ơn bạn đã phản ánh. Chúng tôi rất tiếc về sự bất tiện này và đã ghi nhận ý kiến của bạn. Bộ phận kỹ thuật sẽ liên hệ lại trong vòng 1-2 ngày làm việc để hỗ trợ bạn tốt nhất.">
                            🔧 Hỗ trợ kỹ thuật / khiếu nại
                        </button>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.contacts.reply', $contact) }}" class="reply-form">
                    @csrf
                    <textarea name="reply_message" id="replyTextarea"
                        placeholder="Nhập nội dung phản hồi gửi tới khách hàng..."
                        required>{{ old('reply_message', $contact->reply_message) }}</textarea>
                    <div style="font-size:11px;color:#9ca3af;margin-top:5px;text-align:right">
                        <span id="charCount">0</span> ký tự
                    </div>
                    <button type="submit" class="btn-send">
                        <i class="fas fa-paper-plane"></i>
                        Gửi phản hồi qua Email
                    </button>
                </form>
            </div>
        </div>

        {{-- Cập nhật trạng thái thủ công --}}
        <div class="card">
            <div class="card-header">
                <i class="fas fa-tasks"></i>
                <h2>Cập nhật trạng thái</h2>
            </div>
            <div class="card-body">
                <p style="font-size:12px;color:#6b7280;margin-bottom:0">Trạng thái hiện tại:
                    @if($contact->status==='new') <strong style="color:#d97706">Chưa xử lý</strong>
                    @elseif($contact->status==='processing') <strong style="color:#2563eb">Đang xử lý</strong>
                    @else <strong style="color:#059669">Đã phản hồi</strong>
                    @endif
                </p>
                <form method="POST" action="{{ route('admin.contacts.status', $contact) }}" class="status-form">
                    @csrf @method('PATCH')
                    <select name="status">
                        <option value="new"        {{ $contact->status === 'new'        ? 'selected' : '' }}>Chưa xử lý</option>
                        <option value="processing" {{ $contact->status === 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                        <option value="done"       {{ $contact->status === 'done'       ? 'selected' : '' }}>Đã phản hồi</option>
                    </select>
                    <button type="submit" class="btn-upd"><i class="fas fa-save"></i> Lưu</button>
                </form>
            </div>
        </div>

        {{-- Xoá --}}
        <div style="margin-top:12px;text-align:center">
            <form method="POST" action="{{ route('admin.contacts.destroy', $contact) }}"
                  onsubmit="return confirm('Xoá liên hệ này? Hành động không thể hoàn tác.')">
                @csrf @method('DELETE')
                <button type="submit" style="background:none;border:none;color:#ef4444;font-size:13px;cursor:pointer;font-weight:600">
                    <i class="fas fa-trash"></i> Xoá liên hệ này
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Đếm ký tự
const ta = document.getElementById('replyTextarea');
const cc = document.getElementById('charCount');
function updateCount() { cc.textContent = ta.value.length; }
ta.addEventListener('input', updateCount);
updateCount();

// Dùng template
function useTemplate(btn) {
    ta.value = btn.dataset.text;
    ta.focus();
    updateCount();
}
</script>
@endpush
