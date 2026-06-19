@extends('layouts.admin')

@push('styles')
<style>
    .form-container {
        background: #fff;
        border-radius: 8px;
        padding: 24px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        max-width: 700px;
        margin-top: 16px;
    }
    .form-group {
        margin-bottom: 16px;
    }
    .form-group label {
        display: block;
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 4px;
        color: #495057;
    }
    .form-group label .required {
        color: #dc3545;
        margin-left: 2px;
    }
    .form-control {
        width: 100%;
        padding: 8px 12px;
        font-size: 14px;
        border: 1px solid #ced4da;
        border-radius: 6px;
        background: #fff;
        transition: border-color 0.15s;
    }
    .form-control:focus {
        border-color: #1565C0;
        outline: none;
        box-shadow: 0 0 0 0.2rem rgba(21, 101, 192, 0.25);
    }
    .form-control.is-invalid {
        border-color: #dc3545;
    }
    .invalid-feedback {
        color: #dc3545;
        font-size: 13px;
        margin-top: 4px;
    }
    .btn {
        display: inline-block;
        font-weight: 500;
        font-size: 13px;
        padding: 8px 20px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        transition: all 0.15s;
        text-decoration: none;
    }
    .btn-primary {
        background: #1565C0;
        color: #fff;
    }
    .btn-primary:hover {
        background: #0D47A1;
    }
    .btn-secondary {
        background: #6c757d;
        color: #fff;
    }
    .btn-secondary:hover {
        background: #5a6268;
    }
    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 8px 0;
    }
    .checkbox-group input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
    .checkbox-group label {
        margin: 0;
        font-weight: 400;
        cursor: pointer;
    }
    .mt-3 { margin-top: 16px; }
    .text-muted { color: #6c757d; font-size: 13px; }
    .text-danger { color: #dc3545; font-size: 13px; }
    .mb-3 { margin-bottom: 16px; }
</style>
@endpush

@section('content')
<div class="container">
    <h1>Chỉnh sửa mã giảm giá</h1>
    <p class="text-muted">Mã: <strong>{{ $voucher->code }}</strong></p>

    <div class="form-container">
        <form id="voucherForm" action="{{ route('admin.vouchers.update', $voucher) }}" method="POST" novalidate>
            @csrf
            @method('PUT')

            {{-- Mã giảm giá --}}
            <div class="form-group">
                <label for="code">Mã giảm giá <span class="required">*</span></label>
                <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" 
                       value="{{ old('code', $voucher->code) }}" placeholder="Ví dụ: SUMMER2025" 
                       required pattern="[A-Z0-9_]+" title="Chỉ chấp nhận chữ hoa, số và dấu gạch dưới">
                @error('code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Mã code duy nhất, chỉ chứa chữ hoa, số và dấu gạch dưới.</small>
            </div>

            {{-- Phần trăm giảm --}}
            <div class="form-group">
                <label for="discount_percent">Phần trăm giảm (%) <span class="required">*</span></label>
                <input type="number" name="discount_percent" id="discount_percent" class="form-control @error('discount_percent') is-invalid @enderror" 
                       value="{{ old('discount_percent', $voucher->discount_percent) }}" min="1" max="100" required>
                @error('discount_percent')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Giá trị tối thiểu --}}
            <div class="form-group">
                <label for="min_order_value">Giá trị đơn hàng tối thiểu (VNĐ) <span class="required">*</span></label>
                <input type="number" name="min_order_value" id="min_order_value" class="form-control @error('min_order_value') is-invalid @enderror" 
                       value="{{ old('min_order_value', $voucher->min_order_value) }}" min="0" required>
                @error('min_order_value')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Đơn hàng phải có tổng giá trị ≥ giá trị này mới áp dụng được voucher.</small>
            </div>

            {{-- Số lượt sử dụng --}}
            <div class="form-group">
                <label for="usage_limit">Số lượt sử dụng tối đa <span class="required">*</span></label>
                <input type="number" name="usage_limit" id="usage_limit" class="form-control @error('usage_limit') is-invalid @enderror" 
                       value="{{ old('usage_limit', $voucher->usage_limit) }}" min="1" required>
                @error('usage_limit')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Tổng số lần voucher này có thể được sử dụng.</small>
            </div>

            {{-- Ngày bắt đầu --}}
            <div class="form-group">
                <label for="starts_at">Ngày bắt đầu <span class="required">*</span></label>
                <input type="datetime-local" name="starts_at" id="starts_at" class="form-control @error('starts_at') is-invalid @enderror" 
                       value="{{ old('starts_at', \Carbon\Carbon::parse($voucher->starts_at)->format('Y-m-d\TH:i')) }}" required>
                @error('starts_at')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Ngày kết thúc --}}
            <div class="form-group">
                <label for="expires_at">Ngày kết thúc <span class="required">*</span></label>
                <input type="datetime-local" name="expires_at" id="expires_at" class="form-control @error('expires_at') is-invalid @enderror" 
                       value="{{ old('expires_at', \Carbon\Carbon::parse($voucher->expires_at)->format('Y-m-d\TH:i')) }}" required>
                @error('expires_at')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Phải sau ngày bắt đầu.</small>
            </div>

            {{-- Trạng thái kích hoạt --}}
            <div class="checkbox-group">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $voucher->is_active) ? 'checked' : '' }}>
                <label for="is_active">Kích hoạt</label>
            </div>

            {{-- Thông tin sử dụng (readonly) --}}
            <div class="form-group">
                <label>Số lượt đã dùng</label>
                <input type="text" class="form-control" value="{{ $voucher->used_count }}" readonly disabled>
                <small class="text-muted">Không thể chỉnh sửa số lượt đã dùng.</small>
            </div>

            {{-- Vùng hiển thị lỗi chung --}}
            <div id="form-error" class="text-danger" style="display:none; margin-top:10px;"></div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function() {
        const form = document.getElementById('voucherForm');
        const errorContainer = document.getElementById('form-error');

        function showError(message) {
            errorContainer.textContent = message;
            errorContainer.style.display = 'block';
        }

        function clearError() {
            errorContainer.textContent = '';
            errorContainer.style.display = 'none';
        }

        form.addEventListener('submit', function(e) {
            clearError();

            const code = document.getElementById('code');
            const discount = document.getElementById('discount_percent');
            const minOrder = document.getElementById('min_order_value');
            const usageLimit = document.getElementById('usage_limit');
            const startsAt = document.getElementById('starts_at');
            const expiresAt = document.getElementById('expires_at');

            let isValid = true;
            let errorMsg = '';

            // Kiểm tra rỗng (nếu browser không hỗ trợ required)
            if (!code.value.trim()) {
                isValid = false;
                errorMsg = 'Vui lòng nhập mã giảm giá.';
                code.classList.add('is-invalid');
            } else {
                code.classList.remove('is-invalid');
            }

            if (!discount.value || discount.value < 1 || discount.value > 100) {
                isValid = false;
                if (!errorMsg) errorMsg = 'Vui lòng nhập phần trăm giảm (1-100).';
                discount.classList.add('is-invalid');
            } else {
                discount.classList.remove('is-invalid');
            }

            if (!minOrder.value || parseFloat(minOrder.value) < 0) {
                isValid = false;
                if (!errorMsg) errorMsg = 'Vui lòng nhập giá trị đơn hàng tối thiểu (≥ 0).';
                minOrder.classList.add('is-invalid');
            } else {
                minOrder.classList.remove('is-invalid');
            }

            if (!usageLimit.value || parseInt(usageLimit.value) < 1) {
                isValid = false;
                if (!errorMsg) errorMsg = 'Vui lòng nhập số lượt sử dụng tối đa (≥ 1).';
                usageLimit.classList.add('is-invalid');
            } else {
                usageLimit.classList.remove('is-invalid');
            }

            if (!startsAt.value) {
                isValid = false;
                if (!errorMsg) errorMsg = 'Vui lòng chọn ngày bắt đầu.';
                startsAt.classList.add('is-invalid');
            } else {
                startsAt.classList.remove('is-invalid');
            }

            if (!expiresAt.value) {
                isValid = false;
                if (!errorMsg) errorMsg = 'Vui lòng chọn ngày kết thúc.';
                expiresAt.classList.add('is-invalid');
            } else {
                expiresAt.classList.remove('is-invalid');
            }

            // Kiểm tra logic: expires_at > starts_at
            if (startsAt.value && expiresAt.value) {
                const start = new Date(startsAt.value);
                const end = new Date(expiresAt.value);
                if (end <= start) {
                    isValid = false;
                    if (!errorMsg) errorMsg = 'Ngày kết thúc phải sau ngày bắt đầu.';
                    expiresAt.classList.add('is-invalid');
                } else {
                    expiresAt.classList.remove('is-invalid');
                }
            }

            if (!isValid) {
                e.preventDefault();
                showError(errorMsg || 'Vui lòng kiểm tra lại các trường có dấu * (bắt buộc).');
            }
        });

        // Xóa class lỗi khi người dùng nhập
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
                clearError();
            });
        });
    })();
</script>
@endpush