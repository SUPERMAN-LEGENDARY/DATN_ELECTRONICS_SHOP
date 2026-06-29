@extends('layouts.admin')

@section('title', 'Thêm đơn hàng mới')

@push('styles')
<style>
    .form-container {
        background: #fff;
        border-radius: 8px;
        padding: 24px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        max-width: 1000px;
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
    .btn-danger {
        background: #dc3545;
        color: #fff;
        padding: 4px 10px;
        font-size: 12px;
    }
    .btn-danger:hover {
        background: #c82333;
    }
    .btn-info {
        background: #17a2b8;
        color: #fff;
        padding: 4px 12px;
        font-size: 12px;
    }
    .btn-info:hover {
        background: #138496;
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
    .row {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -8px;
    }
    .col-md-6, .col-md-5, .col-md-4, .col-md-3, .col-md-2 {
        padding: 0 8px;
        flex: 0 0 auto;
    }
    .col-md-6 { width: 50%; }
    .col-md-5 { width: 41.6667%; }
    .col-md-4 { width: 33.3333%; }
    .col-md-3 { width: 25%; }
    .col-md-2 { width: 16.6667%; }
    .text-right { text-align: right; }
    .product-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        margin-bottom: 8px;
        padding: 8px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .product-row .form-group {
        margin-bottom: 0;
    }
    .mb-2 { margin-bottom: 8px; }
    .form-check-inline {
        display: inline-block;
        margin-right: 16px;
    }
    .form-check-input {
        margin-right: 4px;
    }
    #address_new_block {
        margin-top: 10px;
        padding: 12px;
        background: #f8f9fa;
        border-radius: 6px;
    }
    .help-text {
        font-size: 12px;
        color: #6c757d;
        margin-top: 2px;
    }
    .summary-box {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        padding: 16px;
        margin-top: 16px;
    }
    .summary-box table {
        width: 100%;
        max-width: 400px;
        margin-left: auto;
    }
    .summary-box td {
        padding: 6px 0;
    }
    .summary-box .label {
        font-weight: 600;
        color: #495057;
    }
    .summary-box .amount {
        text-align: right;
        font-weight: 600;
    }
    .summary-box .total {
        font-size: 18px;
        color: #1565C0;
        border-top: 2px solid #dee2e6;
        padding-top: 10px;
    }
    .summary-box .discount-text {
        color: #dc3545;
    }
</style>
@endpush

@section('content')
<div class="container">
    <h1><i class="fas fa-plus-circle"></i> Thêm đơn hàng mới</h1>

    <div class="form-container">
        {{-- Hiển thị lỗi --}}
    @if($errors->any())
        <div style="background:#fff5f5; border-left:4px solid #dc3545; padding:12px 16px; border-radius:6px; margin-bottom:16px;">
            <ul style="margin:0; padding-left:18px;">
                @foreach($errors->all() as $error)
                    <li style="color:#dc3545; font-size:13px; margin-bottom:3px;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
        <form id="orderForm" action="{{ route('admin.orders.store') }}" method="POST" novalidate>
            @csrf

            {{-- Khách hàng --}}
            <div class="form-group">
                <label for="user_id">Khách hàng <span class="required">*</span></label>
                <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                    <option value="">-- Chọn khách hàng --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <div class="error">{{ $message }}</div>
                @enderror
                <div class="error" id="user_id-error" style="display:none;"></div>
            </div>

            {{-- Địa chỉ --}}
            <div class="form-group">
                <label>Địa chỉ giao hàng <span class="required">*</span></label>
                <div class="mb-2">
                    <div class="form-check-inline">
                        <input class="form-check-input" type="radio" name="address_option" id="address_existing" value="existing" checked>
                        <label class="form-check-label" for="address_existing">Chọn địa chỉ có sẵn</label>
                    </div>
                    <div class="form-check-inline">
                        <input class="form-check-input" type="radio" name="address_option" id="address_new" value="new">
                        <label class="form-check-label" for="address_new">Nhập địa chỉ mới</label>
                    </div>
                </div>

                {{-- Dropdown địa chỉ có sẵn --}}
                <div id="address_existing_block">
                    <select name="address_id" id="address_id" class="form-control @error('address_id') is-invalid @enderror">
                        <option value="">-- Chọn địa chỉ --</option>
                        @foreach($addresses as $address)
                            <option value="{{ $address->id }}" data-user="{{ $address->user_id }}" {{ old('address_id') == $address->id ? 'selected' : '' }}>
                                {{ $address->full_name }} - {{ $address->phone }} - {{ $address->street }}, {{ $address->ward }}, {{ $address->district }}, {{ $address->province }}
                            </option>
                        @endforeach
                    </select>
                    @error('address_id')
                        <div class="error">{{ $message }}</div>
                    @enderror
                    <div class="error" id="address_id-error" style="display:none;"></div>
                </div>

                {{-- Nhập địa chỉ mới --}}
                <div id="address_new_block" style="display:none;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="address_name">Tên người nhận <span class="required">*</span></label>
                                <input type="text" name="address_name" id="address_name" class="form-control" value="{{ old('address_name') }}" placeholder="Họ tên">
                                <div class="error" id="address_name-error" style="display:none;"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="address_phone">Số điện thoại <span class="required">*</span></label>
                                <input type="text" name="address_phone" id="address_phone" class="form-control" value="{{ old('address_phone') }}" placeholder="Số điện thoại">
                                <div class="error" id="address_phone-error" style="display:none;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="address_detail">Địa chỉ chi tiết <span class="required">*</span></label>
                        <input type="text" name="address_detail" id="address_detail" class="form-control" value="{{ old('address_detail') }}" placeholder="Số nhà, tên đường">
                        <div class="error" id="address_detail-error" style="display:none;"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="address_ward">Phường / Xã <span class="required">*</span></label>
                                <input type="text" name="address_ward" id="address_ward" class="form-control" value="{{ old('address_ward') }}" placeholder="Phường/Xã">
                                <div class="error" id="address_ward-error" style="display:none;"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="address_district">Quận / Huyện <span class="required">*</span></label>
                                <input type="text" name="address_district" id="address_district" class="form-control" value="{{ old('address_district') }}" placeholder="Quận/Huyện">
                                <div class="error" id="address_district-error" style="display:none;"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="address_province">Tỉnh / Thành phố <span class="required">*</span></label>
                                <input type="text" name="address_province" id="address_province" class="form-control" value="{{ old('address_province') }}" placeholder="Tỉnh/Thành phố">
                                <div class="error" id="address_province-error" style="display:none;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Thanh toán --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="payment_method">Phương thức thanh toán <span class="required">*</span></label>
                        <select name="payment_method" id="payment_method" class="form-control @error('payment_method') is-invalid @enderror" required>
                            <option value="cod" {{ old('payment_method') == 'cod' ? 'selected' : '' }}>COD</option>
                            <option value="momo" {{ old('payment_method') == 'momo' ? 'selected' : '' }}>MoMo</option>
                        </select>
                        @error('payment_method')
                            <div class="error">{{ $message }}</div>
                        @enderror
                        <div class="error" id="payment_method-error" style="display:none;"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="payment_status">Trạng thái thanh toán <span class="required">*</span></label>
                        <select name="payment_status" id="payment_status" class="form-control @error('payment_status') is-invalid @enderror" required>
                            <option value="unpaid" {{ old('payment_status') == 'unpaid' ? 'selected' : '' }}>Chưa thanh toán</option>
                            <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                            <option value="refunded" {{ old('payment_status') == 'refunded' ? 'selected' : '' }}>Đã hoàn tiền</option>
                        </select>
                        @error('payment_status')
                            <div class="error">{{ $message }}</div>
                        @enderror
                        <div class="error" id="payment_status-error" style="display:none;"></div>
                    </div>
                </div>
            </div>
            {{-- Voucher --}}
<div class="form-group">
    <label for="voucher_id">Voucher (nếu có)</label>
    <select name="voucher_id" id="voucher_id" class="form-control @error('voucher_id') is-invalid @enderror">
        <option value="">-- Không sử dụng --</option>
        @foreach($vouchers as $voucher)
            <option value="{{ $voucher->id }}"
                data-discount-percent="{{ $voucher->discount_percent }}"
                data-min-order="{{ $voucher->min_order_value ?? 0 }}"
                {{ old('voucher_id') == $voucher->id ? 'selected' : '' }}>
                {{ $voucher->code }} ({{ $voucher->discount_percent }}%)
            </option>
        @endforeach
    </select>
    @error('voucher_id')
        <div class="error">{{ $message }}</div>
    @enderror
</div>
            {{-- Trạng thái đơn hàng --}}
<div class="form-group">
    <label for="status">Trạng thái đơn hàng <span class="required">*</span></label>
    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
        <option value="pending"    {{ old('status', 'pending') == 'pending'    ? 'selected' : '' }}>Chờ xác nhận</option>
        <option value="confirmed"  {{ old('status') == 'confirmed'  ? 'selected' : '' }}>Đã xác nhận</option>
        <option value="processing" {{ old('status') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
        <option value="shipped"    {{ old('status') == 'shipped'    ? 'selected' : '' }}>Đang giao</option>
        <option value="delivered"  {{ old('status') == 'delivered'  ? 'selected' : '' }}>Đã giao</option>
        <option value="cancelled"  {{ old('status') == 'cancelled'  ? 'selected' : '' }}>Đã hủy</option>
        <option value="returned"   {{ old('status') == 'returned'   ? 'selected' : '' }}>Đã hoàn trả</option>
    </select>
    @error('status')
        <div class="error">{{ $message }}</div>
    @enderror
</div>

            {{-- Danh sách sản phẩm --}}
            <div class="form-group">
                <label>Danh sách sản phẩm <span class="required">*</span></label>
                <div id="product-list">
                    <div class="product-row">
                        <div class="col-md-5">
                            <select name="items[0][product_id]" class="form-control product-select" required>
                                <option value="">-- Chọn sản phẩm --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                        {{ $product->name }} ({{ number_format($product->price, 0, ',', '.') }} VNĐ)
                                    </option>
                                @endforeach
                            </select>
                            <div class="error product-error" style="display:none;"></div>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="items[0][quantity]" class="form-control quantity" placeholder="SL" min="1" value="1" required>
                            <div class="error quantity-error" style="display:none;"></div>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="items[0][unit_price]" class="form-control unit-price" placeholder="Giá (VNĐ)" step="1000" min="0">
                        </div>
                        <div class="col-md-2 text-right">
                            <button type="button" class="btn btn-danger remove-product">Xóa</button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-info" id="add-product">+ Thêm sản phẩm</button>
                <div class="error" id="product-list-error" style="display:none;"></div>
            </div>

            {{-- Tổng hợp đơn hàng --}}
            <div class="summary-box">
                <h5 style="margin-bottom: 12px; font-weight: 700;">Tổng hợp đơn hàng</h5>
                <table>
                    <tr>
                        <td class="label">Tạm tính:</td>
                        <td class="amount" id="subtotal-display">0 VNĐ</td>
                    </tr>
                    <tr>
                        <td class="label">Giảm giá:</td>
                        <td class="amount discount-text" id="discount-display">0 VNĐ</td>
                    </tr>
                    <tr>
                        <td class="label" style="font-weight: 700; font-size: 18px;">Tổng cộng:</td>
                        <td class="amount total" id="total-display">0 VNĐ</td>
                    </tr>
                </table>
            </div>

            {{-- Ghi chú --}}
            <div class="form-group">
                <label for="note">Ghi chú</label>
                <textarea name="note" id="note" class="form-control @error('note') is-invalid @enderror" rows="3">{{ old('note') }}</textarea>
                @error('note')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Nút submit --}}
            <div class="mt-3">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Tạo đơn hàng</button>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ─── 1. XỬ LÝ CHỌN/NHẬP ĐỊA CHỈ ──────────────────────────
        const addressExistingBlock = document.getElementById('address_existing_block');
        const addressNewBlock = document.getElementById('address_new_block');
        const addressOptions = document.querySelectorAll('input[name="address_option"]');

        addressOptions.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'existing') {
                    addressExistingBlock.style.display = 'block';
                    addressNewBlock.style.display = 'none';
                    document.querySelectorAll('#address_new_block input').forEach(inp => inp.removeAttribute('required'));
                } else {
                    addressExistingBlock.style.display = 'none';
                    addressNewBlock.style.display = 'block';
                    document.querySelectorAll('#address_new_block input').forEach(inp => inp.setAttribute('required', 'required'));
                }
            });
        });

        // ─── 2. LỌC ĐỊA CHỈ THEO USER ─────────────────────────────
        const userSelect = document.getElementById('user_id');
        const addressSelect = document.getElementById('address_id');
        const allAddressOptions = Array.from(addressSelect.options);

        function filterAddresses() {
            const userId = userSelect.value;
            addressSelect.innerHTML = '<option value="">-- Chọn địa chỉ --</option>';
            allAddressOptions.forEach(opt => {
                if (opt.value === '') return;
                const dataUser = opt.getAttribute('data-user');
                if (dataUser == userId || userId === '') {
                    addressSelect.appendChild(opt.cloneNode(true));
                }
            });
            if (addressSelect.options.length === 1) {
                const emptyOpt = document.createElement('option');
                emptyOpt.value = '';
                emptyOpt.textContent = '-- Không có địa chỉ, vui lòng nhập mới --';
                addressSelect.appendChild(emptyOpt);
            }
        }

        userSelect.addEventListener('change', filterAddresses);
        filterAddresses();

        // ─── 3. THÊM / XÓA DÒNG SẢN PHẨM ──────────────────────
        let itemIndex = 1;
        document.getElementById('add-product').addEventListener('click', function() {
            const container = document.getElementById('product-list');
            const firstRow = container.querySelector('.product-row');
            const newRow = firstRow.cloneNode(true);

            const selects = newRow.querySelectorAll('select, input');
            selects.forEach(el => {
                const name = el.getAttribute('name');
                if (name) {
                    el.setAttribute('name', name.replace(/\[\d+\]/, '[' + itemIndex + ']'));
                }
                if (el.tagName === 'SELECT') {
                    el.value = '';
                } else if (el.type === 'number') {
                    el.value = (el.classList.contains('quantity')) ? '1' : '';
                }
                const error = el.closest('.product-row').querySelector('.product-error, .quantity-error');
                if (error) error.style.display = 'none';
                el.classList.remove('is-invalid');
            });

            container.appendChild(newRow);
            itemIndex++;
            updateSummary();
        });

        document.getElementById('product-list').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-product')) {
                const row = e.target.closest('.product-row');
                if (row && document.querySelectorAll('.product-row').length > 1) {
                    row.remove();
                    updateSummary();
                } else {
                    alert('Phải có ít nhất một sản phẩm.');
                }
            }
        });

        // ─── 4. TỰ ĐỘNG ĐIỀN GIÁ ────────────────────────────────
        document.getElementById('product-list').addEventListener('change', function(e) {
            if (e.target.classList.contains('product-select')) {
                const row = e.target.closest('.product-row');
                const unitPriceInput = row.querySelector('.unit-price');
                const selected = e.target.options[e.target.selectedIndex];
                const price = selected.getAttribute('data-price');
                if (unitPriceInput && !unitPriceInput.value) {
                    unitPriceInput.value = price || '';
                }
                updateSummary();
            }
        });

        // Lắng nghe thay đổi số lượng và giá
        document.getElementById('product-list').addEventListener('input', function(e) {
            if (e.target.classList.contains('quantity') || e.target.classList.contains('unit-price')) {
                updateSummary();
            }
        });

        // ─── 5. TÍNH TỔNG ĐƠN HÀNG ──────────────────────────────
        function updateSummary() {
            let subtotal = 0;
            const rows = document.querySelectorAll('.product-row');

            rows.forEach(row => {
                const productSelect = row.querySelector('.product-select');
                const quantityInput = row.querySelector('.quantity');
                const unitPriceInput = row.querySelector('.unit-price');

                if (productSelect && productSelect.value) {
                    const price = parseFloat(unitPriceInput.value) || parseFloat(productSelect.options[productSelect.selectedIndex]?.getAttribute('data-price')) || 0;
                    const qty = parseInt(quantityInput.value) || 0;
                    subtotal += price * qty;
                }
            });

            // Tính giảm giá
let discount = 0;
const voucherSelect = document.getElementById('voucher_id');
if (voucherSelect && voucherSelect.value) {
    const selectedOption = voucherSelect.options[voucherSelect.selectedIndex];
    const discountPercent = parseFloat(selectedOption.getAttribute('data-discount-percent')) || 0;
    const minOrder = parseFloat(selectedOption.getAttribute('data-min-order')) || 0;

    if (subtotal >= minOrder) {
        discount = subtotal * (discountPercent / 100);
    }
}

const total = Math.max(0, subtotal - discount);
            // Hiển thị
            document.getElementById('subtotal-display').textContent = subtotal.toLocaleString('vi-VN') + ' VNĐ';
            document.getElementById('discount-display').textContent = discount.toLocaleString('vi-VN') + ' VNĐ';
            document.getElementById('total-display').textContent = total.toLocaleString('vi-VN') + ' VNĐ';
        }

        document.getElementById('voucher_id').addEventListener('change', updateSummary);

        // ─── 6. VALIDATION CLIENT-SIDE ──────────────────────────
        const form = document.getElementById('orderForm');

        function setError(fieldId, message) {
            const errorDiv = document.getElementById(fieldId + '-error');
            if (errorDiv) {
                errorDiv.textContent = message;
                errorDiv.style.display = 'block';
            }
            const input = document.getElementById(fieldId);
            if (input) input.classList.add('is-invalid');
        }

        function clearError(fieldId) {
            const errorDiv = document.getElementById(fieldId + '-error');
            if (errorDiv) {
                errorDiv.textContent = '';
                errorDiv.style.display = 'none';
            }
            const input = document.getElementById(fieldId);
            if (input) input.classList.remove('is-invalid');
        }

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

            // User
            const userId = document.getElementById('user_id');
            if (!userId.value) {
                setError('user_id', 'Vui lòng chọn khách hàng.');
                isValid = false;
            } else {
                clearError('user_id');
            }

            // Địa chỉ
            const addressOption = document.querySelector('input[name="address_option"]:checked');
            if (addressOption.value === 'existing') {
                const addressId = document.getElementById('address_id');
                if (!addressId.value) {
                    setError('address_id', 'Vui lòng chọn địa chỉ.');
                    isValid = false;
                } else {
                    clearError('address_id');
                }
            } else {
                const fields = ['address_name', 'address_phone', 'address_detail', 'address_ward', 'address_district', 'address_province'];
                fields.forEach(field => {
                    const input = document.getElementById(field);
                    if (!input.value.trim()) {
                        setError(field, 'Vui lòng nhập ' + input.placeholder.toLowerCase());
                        isValid = false;
                    } else {
                        clearError(field);
                    }
                });
            }

            // Payment method
            const paymentMethod = document.getElementById('payment_method');
            if (!paymentMethod.value) {
                setError('payment_method', 'Vui lòng chọn phương thức thanh toán.');
                isValid = false;
            } else {
                clearError('payment_method');
            }

            const paymentStatus = document.getElementById('payment_status');
            if (!paymentStatus.value) {
                setError('payment_status', 'Vui lòng chọn trạng thái thanh toán.');
                isValid = false;
            } else {
                clearError('payment_status');
            }

            // Sản phẩm
            const productRows = document.querySelectorAll('.product-row');
            let hasProductError = false;
            productRows.forEach((row) => {
                const productSelect = row.querySelector('.product-select');
                const quantityInput = row.querySelector('.quantity');
                const productError = row.querySelector('.product-error');
                const quantityError = row.querySelector('.quantity-error');

                if (!productSelect.value) {
                    productError.textContent = 'Chọn sản phẩm.';
                    productError.style.display = 'block';
                    productSelect.classList.add('is-invalid');
                    hasProductError = true;
                } else {
                    productError.style.display = 'none';
                    productSelect.classList.remove('is-invalid');
                }

                const qty = parseInt(quantityInput.value);
                if (isNaN(qty) || qty < 1) {
                    quantityError.textContent = 'Số lượng phải ≥ 1.';
                    quantityError.style.display = 'block';
                    quantityInput.classList.add('is-invalid');
                    hasProductError = true;
                } else {
                    quantityError.style.display = 'none';
                    quantityInput.classList.remove('is-invalid');
                }
            });

            if (hasProductError) {
                document.getElementById('product-list-error').textContent = 'Vui lòng kiểm tra lại các sản phẩm.';
                document.getElementById('product-list-error').style.display = 'block';
                isValid = false;
            } else {
                document.getElementById('product-list-error').style.display = 'none';
            }

            if (!isValid) {
                e.preventDefault();
                const firstError = document.querySelector('.is-invalid');
                if (firstError) firstError.focus();
            }
        });

        // Gọi updateSummary lần đầu
        updateSummary();
    });
</script>
@endpush