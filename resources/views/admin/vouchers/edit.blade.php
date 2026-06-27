@extends('layouts.admin')

@push('styles')
<style>
    .form-container {
        background: #fff;
        border-radius: 8px;
        padding: 24px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
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

    .mt-3 {
        margin-top: 16px;
    }

    .text-muted {
        color: #6c757d;
        font-size: 13px;
    }

    .text-danger {
        color: #dc3545;
        font-size: 13px;
    }

    .mb-3 {
        margin-bottom: 16px;
    }
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
                    pattern="[A-Z0-9_]+" title="Chỉ chấp nhận chữ hoa, số và dấu gạch dưới">
                <div class="invalid-feedback" id="code-error">@error('code'){{ $message }}@enderror</div>
                <small class="text-muted">Mã code duy nhất, chỉ chứa chữ hoa, số và dấu gạch dưới.</small>
            </div>

            {{-- Phần trăm giảm --}}
            <div class="form-group">
                <label for="discount_percent">Phần trăm giảm (%) <span class="required">*</span></label>
                <input type="number" name="discount_percent" id="discount_percent" class="form-control @error('discount_percent') is-invalid @enderror"
                    value="{{ old('discount_percent', $voucher->discount_percent) }}" min="1" max="100">
                <div class="invalid-feedback" id="discount_percent-error">@error('discount_percent'){{ $message }}@enderror</div>
            </div>

            {{-- Giá trị tối thiểu --}}
            <div class="form-group">
                <label for="min_order_value">Giá trị đơn hàng tối thiểu (VNĐ) <span class="required">*</span></label>
                <input type="number" name="min_order_value" id="min_order_value" class="form-control @error('min_order_value') is-invalid @enderror"
                    value="{{ old('min_order_value', $voucher->min_order_value) }}" min="0">
                <div class="invalid-feedback" id="min_order_value-error">@error('min_order_value'){{ $message }}@enderror</div>
                <small class="text-muted">Đơn hàng phải có tổng giá trị ≥ giá trị này mới áp dụng được voucher.</small>
            </div>

            {{-- Số lượt sử dụng --}}
            <div class="form-group">
                <label for="usage_limit">Số lượt sử dụng tối đa <span class="required">*</span></label>
                <input type="number" name="usage_limit" id="usage_limit" class="form-control @error('usage_limit') is-invalid @enderror"
                    value="{{ old('usage_limit', $voucher->usage_limit) }}" min="1">
                <div class="invalid-feedback" id="usage_limit-error">@error('usage_limit'){{ $message }}@enderror</div>
                <small class="text-muted">Tổng số lần voucher này có thể được sử dụng.</small>
            </div>

            {{-- Ngày bắt đầu --}}
            <div class="form-group">
                <label for="starts_at">Ngày bắt đầu <span class="required">*</span></label>
                <input type="datetime-local" name="starts_at" id="starts_at" class="form-control @error('starts_at') is-invalid @enderror"
                    value="{{ old('starts_at', \Carbon\Carbon::parse($voucher->starts_at)->format('Y-m-d\TH:i')) }}">
                <div class="invalid-feedback" id="starts_at-error">@error('starts_at'){{ $message }}@enderror</div>
            </div>

            {{-- Ngày kết thúc --}}
            <div class="form-group">
                <label for="expires_at">Ngày kết thúc <span class="required">*</span></label>
                <input type="datetime-local" name="expires_at" id="expires_at" class="form-control @error('expires_at') is-invalid @enderror"
                    value="{{ old('expires_at', \Carbon\Carbon::parse($voucher->expires_at)->format('Y-m-d\TH:i')) }}">
                <div class="invalid-feedback" id="expires_at-error">@error('expires_at'){{ $message }}@enderror</div>
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
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('voucherForm');

        // Hàm hiển thị lỗi cho từng trường
        function setError(inputId, message) {
            const input = document.getElementById(inputId);
            const errorDiv = document.getElementById(inputId + '-error');
            if (input) input.classList.add('is-invalid');
            if (errorDiv) {
                errorDiv.textContent = message;
                errorDiv.style.display = 'block';
            }
        }

        // Hàm xóa lỗi
        function clearError(inputId) {
            const input = document.getElementById(inputId);
            const errorDiv = document.getElementById(inputId + '-error');
            if (input) input.classList.remove('is-invalid');
            if (errorDiv) {
                errorDiv.textContent = '';
                errorDiv.style.display = 'none';
            }
        }

        // Xóa lỗi khi người dùng nhập vào trường
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('input', function() {
                clearError(this.id);
            });
            input.addEventListener('change', function() {
                clearError(this.id);
            });
        });

        form.addEventListener('submit', function(e) {
            let isValid = true;

            // Code
            const code = document.getElementById('code');
            if (!code.value.trim()) {
                setError('code', 'Vui lòng nhập mã giảm giá.');
                isValid = false;
            } else {
                clearError('code');
            }

            // Discount percent
            const discount = document.getElementById('discount_percent');
            const discVal = parseInt(discount.value);
            if (isNaN(discVal) || discVal < 1 || discVal > 100) {
                setError('discount_percent', 'Phần trăm giảm phải từ 1 đến 100.');
                isValid = false;
            } else {
                clearError('discount_percent');
            }

            // Min order value
            const minOrder = document.getElementById('min_order_value');
            const minVal = parseFloat(minOrder.value);
            if (minOrder.value === '' || isNaN(minVal) || minVal < 0) {
                setError('min_order_value', 'Giá trị tối thiểu phải ≥ 0.');
                isValid = false;
            } else {
                clearError('min_order_value');
            }

            // Usage limit
            const usage = document.getElementById('usage_limit');
            const usageVal = parseInt(usage.value);
            if (isNaN(usageVal) || usageVal < 1) {
                setError('usage_limit', 'Số lượt sử dụng phải ≥ 1.');
                isValid = false;
            } else {
                clearError('usage_limit');
            }

            // Starts at
            const starts = document.getElementById('starts_at');
            if (!starts.value) {
                setError('starts_at', 'Vui lòng chọn ngày bắt đầu.');
                isValid = false;
            } else {
                clearError('starts_at');
            }

            // Expires at
            const expires = document.getElementById('expires_at');
            if (!expires.value) {
                setError('expires_at', 'Vui lòng chọn ngày kết thúc.');
                isValid = false;
            } else {
                clearError('expires_at');
            }

            // So sánh ngày
            if (starts.value && expires.value) {
                const start = new Date(starts.value);
                const end = new Date(expires.value);
                if (end <= start) {
                    setError('expires_at', 'Ngày kết thúc phải sau ngày bắt đầu.');
                    isValid = false;
                }
            }

            if (!isValid) {
                e.preventDefault();
                form.querySelector('.is-invalid')?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        });
    });
</script>
@endpush