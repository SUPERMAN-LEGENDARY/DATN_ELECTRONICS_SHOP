@extends('layouts.admin')

@section('title', 'Thêm mã giảm giá mới')

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
    .error {
        color: #E53935;
        font-size: 12px;
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
</style>
@endpush

@section('content')
<div class="container">
    <h1>Thêm mã giảm giá mới</h1>

    <div class="form-container">
        <form id="voucherForm" action="{{ route('admin.vouchers.store') }}" method="POST" novalidate>
            @csrf

            {{-- Mã giảm giá --}}
            <div class="form-group">
                <label for="code">Mã giảm giá <span class="required">*</span></label>
                <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror"
                       value="{{ old('code') }}" placeholder="Ví dụ: SUMMER2025"
                       required pattern="[A-Z0-9_]+" title="Chỉ chấp nhận chữ hoa, số và dấu gạch dưới">
                @error('code')
                    <div class="error">{{ $message }}</div>
                @enderror
                <div class="error" id="code-error" style="display:none;"></div>
                <small class="text-muted">Mã code duy nhất, chỉ chứa chữ hoa, số và dấu gạch dưới.</small>
            </div>

            {{-- Phần trăm giảm --}}
            <div class="form-group">
                <label for="discount_percent">Phần trăm giảm (%) <span class="required">*</span></label>
                <input type="number" name="discount_percent" id="discount_percent" class="form-control @error('discount_percent') is-invalid @enderror"
                       value="{{ old('discount_percent') }}" placeholder="1 - 100" min="1" max="100" required>
                @error('discount_percent')
                    <div class="error">{{ $message }}</div>
                @enderror
                <div class="error" id="discount_percent-error" style="display:none;"></div>
            </div>

            {{-- Giá trị tối thiểu --}}
            <div class="form-group">
                <label for="min_order_value">Giá trị đơn hàng tối thiểu (VNĐ) <span class="required">*</span></label>
                <input type="number" name="min_order_value" id="min_order_value" class="form-control @error('min_order_value') is-invalid @enderror"
                       value="{{ old('min_order_value') }}" placeholder="0" min="0" required>
                @error('min_order_value')
                    <div class="error">{{ $message }}</div>
                @enderror
                <div class="error" id="min_order_value-error" style="display:none;"></div>
                <small class="text-muted">Đơn hàng phải có tổng giá trị ≥ giá trị này mới áp dụng được voucher.</small>
            </div>

            {{-- Số lượt sử dụng --}}
            <div class="form-group">
                <label for="usage_limit">Số lượt sử dụng tối đa <span class="required">*</span></label>
                <input type="number" name="usage_limit" id="usage_limit" class="form-control @error('usage_limit') is-invalid @enderror"
                       value="{{ old('usage_limit', 1) }}" min="1" required>
                @error('usage_limit')
                    <div class="error">{{ $message }}</div>
                @enderror
                <div class="error" id="usage_limit-error" style="display:none;"></div>
                <small class="text-muted">Tổng số lần voucher này có thể được sử dụng.</small>
            </div>

            {{-- Ngày bắt đầu --}}
            <div class="form-group">
                <label for="starts_at">Ngày bắt đầu <span class="required">*</span></label>
                <input type="datetime-local" name="starts_at" id="starts_at" class="form-control @error('starts_at') is-invalid @enderror"
                       value="{{ old('starts_at', \Carbon\Carbon::now()->format('Y-m-d\TH:i')) }}" required>
                @error('starts_at')
                    <div class="error">{{ $message }}</div>
                @enderror
                <div class="error" id="starts_at-error" style="display:none;"></div>
            </div>

            {{-- Ngày kết thúc --}}
            <div class="form-group">
                <label for="expires_at">Ngày kết thúc <span class="required">*</span></label>
                <input type="datetime-local" name="expires_at" id="expires_at" class="form-control @error('expires_at') is-invalid @enderror"
                       value="{{ old('expires_at', \Carbon\Carbon::now()->addDays(30)->format('Y-m-d\TH:i')) }}" required>
                @error('expires_at')
                    <div class="error">{{ $message }}</div>
                @enderror
                <div class="error" id="expires_at-error" style="display:none;"></div>
                <small class="text-muted">Phải sau ngày bắt đầu.</small>
            </div>

            {{-- Trạng thái kích hoạt --}}
            <div class="checkbox-group">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                <label for="is_active">Kích hoạt ngay sau khi tạo</label>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Lưu mã giảm giá</button>
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

        // Hàm hiển thị lỗi cho một trường
        function setError(inputId, message) {
            const input = document.getElementById(inputId);
            const errorDiv = document.getElementById(inputId + '-error');
            if (input) {
                input.classList.add('is-invalid');
            }
            if (errorDiv) {
                errorDiv.textContent = message;
                errorDiv.style.display = 'block';
            }
        }

        // Hàm xóa lỗi
        function clearError(inputId) {
            const input = document.getElementById(inputId);
            const errorDiv = document.getElementById(inputId + '-error');
            if (input) {
                input.classList.remove('is-invalid');
            }
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
            if (isNaN(minVal) || minVal < 0) {
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
            }
        });
    });
</script>
@endpush