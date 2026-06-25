@extends('layouts.admin')
@section('title', 'Quản lý đánh giá')

@push('styles')
<style>
.reviews-wrap { max-width: 1200px; }

/* Filter bar */
.filter-bar { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; margin-bottom: 20px; }
.filter-bar input, .filter-bar select {
    padding: 8px 12px; border: 1px solid #e0e0e0; border-radius: 6px;
    font-size: 13px; outline: none; background: #fff;
}
.filter-bar input:focus, .filter-bar select:focus { border-color: #1565C0; }
.filter-bar input[name=search] { width: 260px; }
.btn-filter { padding: 8px 18px; background: #1565C0; color: #fff; border: none; border-radius: 6px; font-size: 13px; cursor: pointer; }
.btn-reset  { padding: 8px 14px; background: #fff; color: #555; border: 1px solid #e0e0e0; border-radius: 6px; font-size: 13px; cursor: pointer; text-decoration: none; }

/* Bulk */
.bulk-bar { display: none; align-items: center; gap: 10px; background: #EBF3FF; border: 1px solid #BBDEFB; border-radius: 8px; padding: 10px 16px; margin-bottom: 16px; font-size: 13px; }
.bulk-bar.active { display: flex; }
.bulk-bar strong { color: #1565C0; }
.btn-bulk { padding: 6px 14px; border-radius: 6px; font-size: 13px; cursor: pointer; border: none; font-weight: 600; }
.btn-bulk-show  { background: #E8F5E9; color: #2E7D32; }
.btn-bulk-hide  { background: #FFF3E0; color: #E65100; }

/* Table */
.reviews-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.06); }
.reviews-table th { background: #f5f7fa; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #888; padding: 12px 14px; text-align: left; border-bottom: 1px solid #eee; }
.reviews-table td { padding: 12px 14px; font-size: 13px; border-bottom: 1px solid #f5f5f5; vertical-align: top; }
.reviews-table tr:hover td { background: #fafbff; }

/* Stars */
.stars-row { color: #FFA000; font-size: 14px; letter-spacing: 1px; }
.stars-row.grey { color: #ddd; }

/* Badges */
.badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 700; }
.badge-visible { background: #E8F5E9; color: #2E7D32; }
.badge-hidden  { background: #FFEBEE; color: #C62828; }

/* Review content */
.review-content-cell { max-width: 280px; }
.review-content-cell .content-text { color: #444; line-height: 1.5; }
.review-content-cell .no-content { color: #bbb; font-style: italic; }
.admin-reply-box { margin-top: 8px; background: #F3F8FF; border-left: 3px solid #1565C0; padding: 8px 10px; border-radius: 0 6px 6px 0; font-size: 12px; color: #1565C0; }
.admin-reply-box .reply-label { font-weight: 700; margin-bottom: 3px; font-size: 11px; text-transform: uppercase; letter-spacing: .5px; }

/* Reply form */
.reply-form-wrap { margin-top: 10px; }
.reply-form-wrap textarea { width: 100%; border: 1px solid #e0e0e0; border-radius: 6px; padding: 8px 10px; font-size: 12px; font-family: inherit; resize: vertical; outline: none; }
.reply-form-wrap textarea:focus { border-color: #1565C0; }
.reply-actions { display: flex; gap: 6px; margin-top: 6px; }
.btn-reply-save   { padding: 5px 14px; background: #1565C0; color: #fff; border: none; border-radius: 5px; font-size: 12px; cursor: pointer; }
.btn-reply-cancel { padding: 5px 10px; background: #fff; color: #666; border: 1px solid #e0e0e0; border-radius: 5px; font-size: 12px; cursor: pointer; }
.btn-reply-delete { padding: 5px 10px; background: #FFEBEE; color: #C62828; border: none; border-radius: 5px; font-size: 12px; cursor: pointer; }

/* Actions */
.action-btns { display: flex; flex-direction: column; gap: 6px; }
.btn-sm { padding: 5px 12px; border-radius: 5px; font-size: 12px; font-weight: 600; cursor: pointer; border: none; white-space: nowrap; }
.btn-show { background: #E8F5E9; color: #2E7D32; }
.btn-hide { background: #FFF3E0; color: #E65100; }
.btn-del  { background: #FFEBEE; color: #C62828; }
.btn-rep  { background: #EBF3FF; color: #1565C0; }

/* Product cell */
.product-cell a { color: #1565C0; text-decoration: none; font-weight: 600; font-size: 13px; }
.product-cell a:hover { text-decoration: underline; }
.user-cell { font-size: 13px; }
.user-cell small { color: #888; font-size: 11px; display: block; }

/* Empty */
.empty-state { text-align: center; padding: 60px 20px; color: #999; }
.empty-state i { font-size: 40px; margin-bottom: 12px; display: block; }
</style>
@endpush

@section('content')
<div class="reviews-wrap">

    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
        <div>
            <h2 style="font-size:20px;font-weight:800;margin-bottom:2px">Quản lý đánh giá</h2>
            <p style="font-size:13px;color:#888">Xem, ẩn/hiện và phản hồi đánh giá của khách hàng</p>
        </div>
        <div style="font-size:13px;color:#888">
            Tổng: <strong style="color:#333">{{ $reviews->total() }}</strong> đánh giá
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Filter --}}
    <form method="GET" action="{{ route('admin.reviews.index') }}" id="filterForm">
        <div class="filter-bar">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm sản phẩm / người dùng...">
            <select name="status">
                <option value="">-- Trạng thái --</option>
                <option value="visible" {{ request('status')=='visible' ? 'selected' : '' }}>Đang hiển thị</option>
                <option value="hidden"  {{ request('status')=='hidden'  ? 'selected' : '' }}>Đang ẩn</option>
            </select>
            <select name="rating">
                <option value="">-- Sao --</option>
                @for($i=5;$i>=1;$i--)
                <option value="{{ $i }}" {{ request('rating')==$i ? 'selected' : '' }}>{{ $i }} ★</option>
                @endfor
            </select>
            <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Lọc</button>
            <a href="{{ route('admin.reviews.index') }}" class="btn-reset"><i class="fas fa-times"></i> Xoá lọc</a>
        </div>
    </form>

    {{-- Bulk actions --}}
    <form id="bulkForm" method="POST" action="{{ route('admin.reviews.bulk-toggle') }}">
        @csrf
        <div class="bulk-bar" id="bulkBar">
            <strong id="selectedCount">0</strong> đánh giá được chọn
            <button type="submit" name="action" value="show" class="btn-bulk btn-bulk-show">
                <i class="fas fa-eye"></i> Hiển thị
            </button>
            <button type="submit" name="action" value="hide" class="btn-bulk btn-bulk-hide">
                <i class="fas fa-eye-slash"></i> Ẩn
            </button>
        </div>

        {{-- Table --}}
        @if($reviews->isEmpty())
            <div class="empty-state">
                <i class="fas fa-star"></i>
                Không có đánh giá nào.
            </div>
        @else
        <table class="reviews-table">
            <thead>
                <tr>
                    <th width="32"><input type="checkbox" id="checkAll" title="Chọn tất cả"></th>
                    <th>Sản phẩm</th>
                    <th>Người dùng</th>
                    <th width="100">Sao</th>
                    <th>Nội dung / Phản hồi</th>
                    <th width="90">Ngày</th>
                    <th width="80">Trạng thái</th>
                    <th width="110">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reviews as $review)
                <tr>
                    {{-- Checkbox --}}
                    <td><input type="checkbox" name="ids[]" value="{{ $review->id }}" class="row-check"></td>

                    {{-- Sản phẩm --}}
                    <td class="product-cell">
                        <a href="{{ route('products.show', $review->product->slug ?? '#') }}" target="_blank">
                            {{ Str::limit($review->product->name ?? '—', 40) }}
                        </a>
                    </td>

                    {{-- Người dùng --}}
                    <td class="user-cell">
                        {{ $review->user->name ?? 'Ẩn danh' }}
                        <small>{{ $review->user->email ?? '' }}</small>
                    </td>

                    {{-- Sao --}}
                    <td>
                        <span class="stars-row">
                            @for($i=1;$i<=5;$i++){{ $i<=$review->rating ? '★' : '☆' }}@endfor
                        </span>
                        <span style="font-size:12px;color:#888;margin-left:4px">{{ $review->rating }}/5</span>
                    </td>

                    {{-- Nội dung + reply --}}
                    <td class="review-content-cell">
                        @if($review->content)
                            <div class="content-text">{{ $review->content }}</div>
                        @else
                            <div class="no-content">Không có nội dung</div>
                        @endif

                        {{-- Hiển thị reply đã có --}}
                        @if($review->admin_reply)
                        <div class="admin-reply-box">
                            <div class="reply-label"><i class="fas fa-reply"></i> Phản hồi từ Shop</div>
                            <div>{{ $review->admin_reply }}</div>
                        </div>
                        @endif

                        {{-- Form reply (ẩn mặc định) --}}
                        <div class="reply-form-wrap" id="replyForm{{ $review->id }}" style="display:none">
                            <form method="POST" action="{{ route('admin.reviews.reply', $review->id) }}">
                                @csrf
                                <textarea name="admin_reply" rows="3"
                                    placeholder="Nhập phản hồi của shop...">{{ $review->admin_reply }}</textarea>
                                <div class="reply-actions">
                                    <button type="submit" class="btn-reply-save">
                                        <i class="fas fa-save"></i> Lưu
                                    </button>
                                    <button type="button" class="btn-reply-cancel"
                                        onclick="document.getElementById('replyForm{{ $review->id }}').style.display='none'">
                                        Huỷ
                                    </button>
                                    @if($review->admin_reply)
                                    <form method="POST" action="{{ route('admin.reviews.delete-reply', $review->id) }}" style="display:inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-reply-delete"
                                            onclick="return confirm('Xoá phản hồi này?')">
                                            <i class="fas fa-trash"></i> Xoá reply
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </td>

                    {{-- Ngày --}}
                    <td style="color:#888;font-size:12px">
                        {{ $review->created_at->format('d/m/Y') }}<br>
                        <span style="font-size:11px">{{ $review->created_at->format('H:i') }}</span>
                    </td>

                    {{-- Trạng thái --}}
                    <td>
                        <span class="badge {{ $review->is_visible ? 'badge-visible' : 'badge-hidden' }}">
                            {{ $review->is_visible ? 'Hiển thị' : 'Ẩn' }}
                        </span>
                    </td>

                    {{-- Thao tác --}}
                    <td>
                        <div class="action-btns">
                            {{-- Ẩn / Hiện --}}
                            <form method="POST" action="{{ route('admin.reviews.toggle-visible', $review->id) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-sm {{ $review->is_visible ? 'btn-hide' : 'btn-show' }}" style="width:100%">
                                    @if($review->is_visible)
                                        <i class="fas fa-eye-slash"></i> Ẩn
                                    @else
                                        <i class="fas fa-eye"></i> Hiện
                                    @endif
                                </button>
                            </form>

                            {{-- Reply --}}
                            <button type="button" class="btn-sm btn-rep" style="width:100%"
                                onclick="toggleReplyForm({{ $review->id }})">
                                <i class="fas fa-reply"></i>
                                {{ $review->admin_reply ? 'Sửa reply' : 'Phản hồi' }}
                            </button>

                            {{-- Xoá --}}
                            <form method="POST" action="{{ route('admin.reviews.destroy', $review->id) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-sm btn-del" style="width:100%"
                                    onclick="return confirm('Xoá đánh giá này?')">
                                    <i class="fas fa-trash"></i> Xoá
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </form>

    {{-- Pagination --}}
    @if($reviews->hasPages())
    <div style="margin-top:20px">{{ $reviews->links() }}</div>
    @endif

</div>
@endsection

@push('scripts')
<script>
// Toggle reply form
function toggleReplyForm(id) {
    const el = document.getElementById('replyForm' + id);
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}

// Bulk select
const checkAll  = document.getElementById('checkAll');
const rowChecks = document.querySelectorAll('.row-check');
const bulkBar   = document.getElementById('bulkBar');
const countEl   = document.getElementById('selectedCount');

function updateBulk() {
    const checked = document.querySelectorAll('.row-check:checked').length;
    countEl.textContent = checked;
    bulkBar.classList.toggle('active', checked > 0);
}

checkAll?.addEventListener('change', function () {
    rowChecks.forEach(c => c.checked = this.checked);
    updateBulk();
});
rowChecks.forEach(c => c.addEventListener('change', updateBulk));
</script>
@endpush