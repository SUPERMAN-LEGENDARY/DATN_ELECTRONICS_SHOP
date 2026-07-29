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
    .form-control[readonly] {
        background: #f1f3f5;
        color: #495057;
        cursor: not-allowed;
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
    .btn-page {
        background: #fff;
        color: #1565C0;
        border: 1px solid #1565C0;
        padding: 4px 10px;
        font-size: 12px;
    }
    .btn-page:hover {
        background: #e7f1fd;
    }
    .btn-page.active {
        background: #1565C0;
        color: #fff;
    }
    .btn-page:disabled {
        opacity: 0.5;
        cursor: not-allowed;
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
    #customer_info_block {
        margin-top: 10px;
        padding: 12px 16px;
        background: #eef6ff;
        border: 1px solid #cfe3fb;
        border-radius: 6px;
        font-size: 13px;
        display: none;
    }
    #customer_info_block .info-row {
        display: flex;
        gap: 6px;
        margin-bottom: 4px;
    }
    #customer_info_block .info-row:last-child {
        margin-bottom: 0;
    }
    #customer_info_block .info-label {
        font-weight: 600;
        color: #495057;
        min-width: 90px;
    }
    #customer_info_block .info-value {
        color: #1565C0;
        font-weight: 500;
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
    .product-picker-wrap {
        overflow-y: auto;
        margin-top: 8px;
        border: 1px solid #e9ecef;
        border-radius: 6px;
    }
    .product-picker-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .product-picker-table thead tr {
        background: #1565C0;
        color: #fff;
    }
    .product-picker-table th, .product-picker-table td {
        padding: 8px 12px;
    }
    .product-picker-table th {
        text-align: left;
    }
    .product-picker-row:hover td {
        background: #f0f6ff;
    }
    .product-picker-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 8px 12px;
        border-top: 1px solid #e9ecef;
        background: #fafafa;
    }
    .product-picker-pagination .page-numbers {
        display: flex;
        gap: 4px;
    }
    .product-picker-pagination .page-info {
        font-size: 12px;
        color: #6c757d;
    }
</style>
@endpush

@section('content')
<div class="container">
    <h1><i class="fas fa-plus-circle"></i> Thêm đơn hàng mới</h1>

    <div class="form-container">
        {{-- Hiển thị lỗi validate (Validator) --}}
    @if($errors->any())
        <div style="background:#fff5f5; border-left:4px solid #dc3545; padding:12px 16px; border-radius:6px; margin-bottom:16px;">
            <ul style="margin:0; padding-left:18px;">
                @foreach($errors->all() as $error)
                    <li style="color:#dc3545; font-size:13px; margin-bottom:3px;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

        {{-- Hiển thị lỗi nghiệp vụ (return back()->with('error', ...)) — trước đây KHÔNG có đoạn này nên lỗi "Địa chỉ không hợp lệ" bị mất, form fail trong im lặng --}}
    @if(session('error'))
        <div style="background:#fff5f5; border-left:4px solid #dc3545; padding:12px 16px; border-radius:6px; margin-bottom:16px; color:#dc3545; font-size:13px; font-weight:600;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif
        <form id="orderForm" action="{{ route('admin.orders.store') }}" method="POST" novalidate>
            @csrf

            {{-- Khách hàng --}}
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="customer_phone_input">Số điện thoại <span class="required">*</span></label>
                            <input type="text" name="customer_phone" id="customer_phone_input" class="form-control @error('customer_phone') is-invalid @enderror"
                                   placeholder="Nhập số điện thoại khách hàng" value="{{ old('customer_phone') }}" autocomplete="off">
                            @error('customer_phone')
                                <div class="error">{{ $message }}</div>
                            @enderror
                            <div class="error" id="customer_phone_input-error" style="display:none;"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="customer_name">Tên khách hàng <span class="required">*</span></label>
                            <input type="text" name="customer_name" id="customer_name" class="form-control @error('customer_name') is-invalid @enderror"
                                   placeholder="Nhập tên khách hàng" value="{{ old('customer_name') }}">
                            @error('customer_name')
                                <div class="error">{{ $message }}</div>
                            @enderror
                            <div class="error" id="customer_name-error" style="display:none;"></div>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="user_id" id="user_id" value="{{ old('user_id') }}">
                @error('user_id')
                    <div class="error">{{ $message }}</div>
                @enderror
                <div class="error" id="user_id-error" style="display:none;"></div>

                {{-- Thông tin tự động hiện ra khi SĐT trùng với tài khoản có sẵn --}}
                <div id="customer_info_block">
                    <div class="info-row" style="color:#2e7d32; font-weight:600; margin-bottom:6px;">
                        <i class="fas fa-check-circle"></i>&nbsp;Đã tìm thấy tài khoản với số điện thoại này
                    </div>
                    <div class="info-row"><span class="info-label">Tên:</span><span class="info-value" id="customer_info_name"></span></div>
                    <div class="info-row"><span class="info-label">Email:</span><span class="info-value" id="customer_info_email"></span></div>
                    <div class="info-row"><span class="info-label">Số điện thoại:</span><span class="info-value" id="customer_info_phone"></span></div>
                </div>
                <div id="customer_new_hint" class="help-text" style="display:none;">
                    Số điện thoại này chưa có tài khoản — hệ thống sẽ tạo khách hàng mới với tên bạn nhập ở trên.
                </div>

                {{-- Nguồn dữ liệu khách hàng để JS đối chiếu theo SĐT (không hiển thị) --}}
                <select id="customerDataSource" style="display:none;" aria-hidden="true">
                    @foreach($users as $user)
                        <option value="{{ $user->id }}"
                            data-name="{{ $user->name }}"
                            data-email="{{ $user->email }}"
                            data-phone="{{ $user->phone ?? '' }}"></option>
                    @endforeach
                </select>
            </div>

            {{-- Địa chỉ --}}
            <div class="form-group">
                <label>Địa chỉ giao hàng <span class="required">*</span></label>
                <div class="mb-2">
                    <div class="form-check-inline">
                        <input class="form-check-input" type="radio" name="address_option" id="address_existing" value="existing" {{ old('address_option', 'existing') == 'existing' ? 'checked' : '' }}>
                        <label class="form-check-label" for="address_existing">Chọn địa chỉ có sẵn</label>
                    </div>
                    <div class="form-check-inline">
                        <input class="form-check-input" type="radio" name="address_option" id="address_new" value="new" {{ old('address_option') == 'new' ? 'checked' : '' }}>
                        <label class="form-check-label" for="address_new">Nhập địa chỉ mới</label>
                    </div>
                </div>

                {{-- Dropdown địa chỉ có sẵn --}}
                <div id="address_existing_block" style="{{ old('address_option') == 'new' ? 'display:none;' : '' }}">
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
                <div id="address_new_block" style="{{ old('address_option') == 'new' ? '' : 'display:none;' }}">
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
    <div class="help-text" id="voucher-warning" style="display:none; color:#dc3545;"></div>
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

            {{-- Chọn sản phẩm từ bảng --}}
            <div class="form-group">
                <label>Chọn sản phẩm <span class="required">*</span></label>
                <input type="text" id="productSearch" class="form-control" placeholder="Tìm sản phẩm theo tên...">
                <div class="product-picker-wrap">
                    <table class="product-picker-table">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th class="text-right">Giá</th>
                                <th class="text-right">Tồn kho</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="productPickerBody">
                            @foreach($products as $product)
                            <tr class="product-picker-row" data-id="{{ $product->id }}" data-name="{{ strtolower($product->name) }}" data-price="{{ $product->price }}" data-stock="{{ $product->stock }}">
                                <td>{{ $product->name }}</td>
                                <td class="text-right">{{ number_format($product->price) }}đ</td>
                                <td class="text-right">{{ $product->stock }}</td>
                                <td class="text-right">
                                    <button type="button" class="btn btn-info btn-add-product">+ Thêm</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="product-picker-pagination" id="productPickerPagination">
                        <div class="page-info" id="productPickerInfo"></div>
                        <div class="page-numbers" id="productPickerPages"></div>
                    </div>
                </div>
            </div>

            {{-- Sản phẩm đã chọn --}}
            <div class="form-group">
                <label>Sản phẩm đã chọn <span class="required">*</span></label>
                <div id="product-list">
                    {{-- Dựng lại danh sách sản phẩm từ old('items') khi submit trước đó bị lỗi,
                         để nhân viên không phải chọn lại từ đầu mỗi lần validate fail --}}
                    @foreach(old('items', []) as $idx => $oldItem)
                        @php $p = $products->firstWhere('id', (int) ($oldItem['product_id'] ?? 0)); @endphp
                        @if($p)
                        <div class="product-row" data-product-id="{{ $p->id }}" data-stock="{{ $p->stock }}">
                            <div class="col-md-5">
                                <strong>{{ $p->name }}</strong>
                                <input type="hidden" name="items[{{ $idx }}][product_id]" value="{{ $p->id }}">
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="items[{{ $idx }}][quantity]" class="form-control quantity" min="1" max="{{ $p->stock }}" value="{{ $oldItem['quantity'] ?? 1 }}" required>
                                <div class="error quantity-error" style="display:none;"></div>
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="items[{{ $idx }}][unit_price]" class="form-control unit-price" step="1000" min="0" value="{{ $oldItem['unit_price'] ?? $p->price }}" readonly tabindex="-1">
                            </div>
                            <div class="col-md-2 text-right">
                                <button type="button" class="btn btn-danger remove-product">Xóa</button>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
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
                <textarea name="note" id="note" class="form-control @error('note') is-invalid @enderror" rows="3" maxlength="500">{{ old('note') }}</textarea>
                <div class="help-text" id="note-counter">0/500</div>
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
        // ─── 0. LỌC ĐỊA CHỈ THEO USER (khai báo trước vì được gọi ngay bên dưới) ──
        const userSelect = document.getElementById('user_id');
        const addressSelect = document.getElementById('address_id');
        const allAddressOptions = Array.from(addressSelect.options);

        function filterAddresses() {
            const userId = userSelect.value;
            addressSelect.innerHTML = '<option value="">-- Chọn địa chỉ --</option>';

            // QUAN TRỌNG: nếu chưa xác định được user_id (khách mới / SĐT chưa đủ số),
            // KHÔNG được hiện địa chỉ của người khác. Trước đây điều kiện `userId === ''`
            // khiến toàn bộ địa chỉ của MỌI khách hàng hiện ra, nhân viên có thể lỡ chọn
            // nhầm địa chỉ của khách khác -> server từ chối tạo đơn nhưng không có thông
            // báo lỗi rõ ràng.
            if (userId === '') {
                const emptyOpt = document.createElement('option');
                emptyOpt.value = '';
                emptyOpt.textContent = '-- Khách hàng mới, vui lòng chọn "Nhập địa chỉ mới" --';
                addressSelect.appendChild(emptyOpt);
                return;
            }

            allAddressOptions.forEach(opt => {
                if (opt.value === '') return;
                const dataUser = opt.getAttribute('data-user');
                if (dataUser == userId) {
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

        // ─── 1. NHẬP SĐT → TỰ ĐỘNG DÒ TÀI KHOẢN CÓ SẴN ──────────
        const customerPhoneInput = document.getElementById('customer_phone_input');
        const customerNameInput = document.getElementById('customer_name');
        const userIdInput = document.getElementById('user_id');
        const customerInfoBlock = document.getElementById('customer_info_block');
        const customerNewHint = document.getElementById('customer_new_hint');
        const customerInfoName = document.getElementById('customer_info_name');
        const customerInfoEmail = document.getElementById('customer_info_email');
        const customerInfoPhone = document.getElementById('customer_info_phone');

        const customerDataSource = document.getElementById('customerDataSource');
        const usersData = Array.from(customerDataSource.options).map(opt => ({
            id: opt.value,
            name: opt.getAttribute('data-name') || '',
            email: opt.getAttribute('data-email') || '',
            phone: (opt.getAttribute('data-phone') || '').trim()
        }));

        function normalizePhone(str) {
            return (str || '').replace(/\D/g, '');
        }

        function lookupCustomerByPhone() {
            const phone = normalizePhone(customerPhoneInput.value);
            const addressNewRadio = document.getElementById('address_new');

            // SĐT chưa đủ số, chưa dò
            if (phone.length < 8) {
                userIdInput.value = '';
                customerInfoBlock.style.display = 'none';
                customerNewHint.style.display = 'none';
                filterAddresses();
                return;
            }

            const matched = usersData.find(u => normalizePhone(u.phone) === phone);

            if (matched) {
                userIdInput.value = matched.id;
                customerInfoName.textContent = matched.name;
                customerInfoEmail.textContent = matched.email;
                customerInfoPhone.textContent = matched.phone || 'Chưa cập nhật';
                customerInfoBlock.style.display = 'block';
                customerNewHint.style.display = 'none';
                clearError('customer_phone_input');

                // Tự điền tên nếu ô tên đang trống, để nhân viên không phải gõ lại
                if (!customerNameInput.value.trim()) {
                    customerNameInput.value = matched.name;
                }
            } else {
                userIdInput.value = '';
                customerInfoBlock.style.display = 'none';
                customerNewHint.style.display = 'block';

                // Khách hàng mới → chắc chắn chưa có địa chỉ lưu sẵn
                addressNewRadio.checked = true;
                addressNewRadio.dispatchEvent(new Event('change'));
            }

            filterAddresses();
        }

        customerPhoneInput.addEventListener('input', lookupCustomerByPhone);
        lookupCustomerByPhone();

        // ─── 2. XỬ LÝ CHỌN/NHẬP ĐỊA CHỈ ──────────────────────────
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

        // ─── 3. GỌI LỌC ĐỊA CHỈ LẦN ĐẦU (hàm filterAddresses đã khai báo ở trên) ──
        filterAddresses();

        // ─── 4. TÌM KIẾM + PHÂN TRANG SẢN PHẨM TRONG BẢNG ────────
        const PAGE_SIZE = 10;
        let currentPage = 1;
        const allProductRows = Array.from(document.querySelectorAll('#productPickerBody .product-picker-row'));
        const productSearchInput = document.getElementById('productSearch');
        const pickerPagesEl = document.getElementById('productPickerPages');
        const pickerInfoEl = document.getElementById('productPickerInfo');

        function getFilteredRows() {
            const keyword = productSearchInput.value.trim().toLowerCase();
            if (keyword === '') return allProductRows;
            return allProductRows.filter(row => row.dataset.name.includes(keyword));
        }

        function renderProductPicker() {
            const filtered = getFilteredRows();
            const totalPages = Math.max(1, Math.ceil(filtered.length / PAGE_SIZE));
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            const startIdx = (currentPage - 1) * PAGE_SIZE;
            const endIdx = startIdx + PAGE_SIZE;
            const visibleSet = new Set(filtered.slice(startIdx, endIdx));

            // Ẩn tất cả trước, chỉ hiện các dòng thuộc trang hiện tại
            allProductRows.forEach(row => {
                row.style.display = visibleSet.has(row) ? '' : 'none';
            });

            // Thông tin số lượng
            if (filtered.length === 0) {
                pickerInfoEl.textContent = 'Không tìm thấy sản phẩm nào';
            } else {
                pickerInfoEl.textContent = `Hiển thị ${startIdx + 1}-${Math.min(endIdx, filtered.length)} / ${filtered.length} sản phẩm`;
            }

            // Vẽ lại các nút phân trang
            pickerPagesEl.innerHTML = '';

            const prevBtn = document.createElement('button');
            prevBtn.type = 'button';
            prevBtn.className = 'btn btn-page';
            prevBtn.textContent = '‹';
            prevBtn.disabled = currentPage === 1;
            prevBtn.addEventListener('click', () => { currentPage--; renderProductPicker(); });
            pickerPagesEl.appendChild(prevBtn);

            for (let p = 1; p <= totalPages; p++) {
                const pageBtn = document.createElement('button');
                pageBtn.type = 'button';
                pageBtn.className = 'btn btn-page' + (p === currentPage ? ' active' : '');
                pageBtn.textContent = p;
                pageBtn.addEventListener('click', () => { currentPage = p; renderProductPicker(); });
                pickerPagesEl.appendChild(pageBtn);
            }

            const nextBtn = document.createElement('button');
            nextBtn.type = 'button';
            nextBtn.className = 'btn btn-page';
            nextBtn.textContent = '›';
            nextBtn.disabled = currentPage === totalPages;
            nextBtn.addEventListener('click', () => { currentPage++; renderProductPicker(); });
            pickerPagesEl.appendChild(nextBtn);
        }

        productSearchInput.addEventListener('input', function() {
            currentPage = 1;
            renderProductPicker();
        });

        renderProductPicker();

        // ─── 5. THÊM SẢN PHẨM TỪ BẢNG VÀO DANH SÁCH ĐÃ CHỌN ──────
        // Bắt đầu đếm từ sau các dòng đã được dựng lại từ old('items') ở trên,
        // để tránh trùng index với input name="items[n][...]" đã có sẵn.
        let itemIndex = {{ count(old('items', [])) }};
        document.getElementById('productPickerBody').addEventListener('click', function(e) {
            if (!e.target.classList.contains('btn-add-product')) return;
            const row = e.target.closest('.product-picker-row');
            const id = row.dataset.id;
            const name = row.querySelector('td').textContent.trim();
            const price = row.dataset.price;
            const stock = parseInt(row.dataset.stock) || 0;

            if (stock < 1) {
                alert('Sản phẩm "' + name + '" đã hết hàng, không thể thêm.');
                return;
            }

            const existing = document.querySelector(`.product-row[data-product-id="${id}"]`);
            if (existing) {
                const qtyInput = existing.querySelector('.quantity');
                const nextQty = parseInt(qtyInput.value || 0) + 1;
                if (nextQty > stock) {
                    alert('Số lượng "' + name + '" đã đạt tối đa tồn kho (' + stock + ').');
                    return;
                }
                qtyInput.value = nextQty;
                updateSummary();
                return;
            }

            const newRow = document.createElement('div');
            newRow.className = 'product-row';
            newRow.setAttribute('data-product-id', id);
            newRow.setAttribute('data-stock', stock);
            newRow.innerHTML = `
                <div class="col-md-5">
                    <strong>${name}</strong>
                    <input type="hidden" name="items[${itemIndex}][product_id]" value="${id}">
                </div>
                <div class="col-md-2">
                    <input type="number" name="items[${itemIndex}][quantity]" class="form-control quantity" min="1" max="${stock}" value="1" required>
                    <div class="error quantity-error" style="display:none;"></div>
                </div>
                <div class="col-md-3">
                    <input type="number" name="items[${itemIndex}][unit_price]" class="form-control unit-price" step="1000" min="0" value="${price}" readonly tabindex="-1">
                </div>
                <div class="col-md-2 text-right">
                    <button type="button" class="btn btn-danger remove-product">Xóa</button>
                </div>`;
            document.getElementById('product-list').appendChild(newRow);
            itemIndex++;
            updateSummary();
        });

        document.getElementById('product-list').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-product')) {
                e.target.closest('.product-row').remove();
                updateSummary();
            }
        });

        document.getElementById('product-list').addEventListener('input', function(e) {
            if (e.target.classList.contains('quantity')) {
                const max = parseInt(e.target.getAttribute('max'));
                if (!isNaN(max) && parseInt(e.target.value) > max) {
                    e.target.value = max;
                }
            }
            if (e.target.classList.contains('quantity') || e.target.classList.contains('unit-price')) {
                updateSummary();
            }
        });

        // ─── 6. TÍNH TỔNG ĐƠN HÀNG ──────────────────────────────
        function updateSummary() {
            let subtotal = 0;
            const rows = document.querySelectorAll('.product-row');

            rows.forEach(row => {
                const price = parseFloat(row.querySelector('.unit-price').value) || 0;
                const qty = parseInt(row.querySelector('.quantity').value) || 0;
                subtotal += price * qty;
            });

            // Tính giảm giá
            let discount = 0;
            const voucherSelect = document.getElementById('voucher_id');
            const voucherWarning = document.getElementById('voucher-warning');
            voucherWarning.style.display = 'none';

            if (voucherSelect && voucherSelect.value) {
                const selectedOption = voucherSelect.options[voucherSelect.selectedIndex];
                const discountPercent = parseFloat(selectedOption.getAttribute('data-discount-percent')) || 0;
                const minOrder = parseFloat(selectedOption.getAttribute('data-min-order')) || 0;

                if (subtotal >= minOrder) {
                    discount = subtotal * (discountPercent / 100);
                } else if (minOrder > 0) {
                    voucherWarning.textContent = `Đơn tối thiểu ${minOrder.toLocaleString('vi-VN')} VNĐ để áp dụng voucher này (hiện tại: ${subtotal.toLocaleString('vi-VN')} VNĐ). Giảm giá sẽ không được áp dụng.`;
                    voucherWarning.style.display = 'block';
                }
            }

            const total = Math.max(0, subtotal - discount);
            // Hiển thị
            document.getElementById('subtotal-display').textContent = subtotal.toLocaleString('vi-VN') + ' VNĐ';
            document.getElementById('discount-display').textContent = discount.toLocaleString('vi-VN') + ' VNĐ';
            document.getElementById('total-display').textContent = total.toLocaleString('vi-VN') + ' VNĐ';
        }

        document.getElementById('voucher_id').addEventListener('change', updateSummary);

        // ─── 6b. ĐẾM KÝ TỰ GHI CHÚ ───────────────────────────────
        const noteInput = document.getElementById('note');
        const noteCounter = document.getElementById('note-counter');
        function updateNoteCounter() {
            noteCounter.textContent = noteInput.value.length + '/500';
        }
        noteInput.addEventListener('input', updateNoteCounter);
        updateNoteCounter();

        // ─── 7. VALIDATION CLIENT-SIDE ──────────────────────────
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

        function isValidVNPhone(value) {
            return /^(0|\+84)\d{9,10}$/.test((value || '').trim());
        }

        form.addEventListener('submit', function(e) {
            let isValid = true;

            // Khách hàng: bắt buộc có SĐT và tên (dù là khách có sẵn hay khách mới)
            const phoneField = document.getElementById('customer_phone_input');
            if (!phoneField.value.trim()) {
                setError('customer_phone_input', 'Vui lòng nhập số điện thoại khách hàng.');
                isValid = false;
            } else if (!isValidVNPhone(phoneField.value)) {
                setError('customer_phone_input', 'Số điện thoại không hợp lệ (VD: 0912345678).');
                isValid = false;
            } else {
                clearError('customer_phone_input');
            }

            const customerNameField = document.getElementById('customer_name');
            if (!customerNameField.value.trim()) {
                setError('customer_name', 'Vui lòng nhập tên khách hàng.');
                isValid = false;
            } else {
                clearError('customer_name');
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
                    } else if (field === 'address_phone' && !isValidVNPhone(input.value)) {
                        setError(field, 'Số điện thoại không hợp lệ (VD: 0912345678).');
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

            if (productRows.length === 0) {
                hasProductError = true;
            } else {
                productRows.forEach((row) => {
                    const quantityInput = row.querySelector('.quantity');
                    const quantityError = row.querySelector('.quantity-error');
                    const qty = parseInt(quantityInput.value);
                    const maxStock = parseInt(row.getAttribute('data-stock'));

                    if (isNaN(qty) || qty < 1) {
                        quantityError.textContent = 'Số lượng phải ≥ 1.';
                        quantityError.style.display = 'block';
                        quantityInput.classList.add('is-invalid');
                        hasProductError = true;
                    } else if (!isNaN(maxStock) && qty > maxStock) {
                        quantityError.textContent = 'Vượt quá tồn kho (còn ' + maxStock + ').';
                        quantityError.style.display = 'block';
                        quantityInput.classList.add('is-invalid');
                        hasProductError = true;
                    } else {
                        quantityError.style.display = 'none';
                        quantityInput.classList.remove('is-invalid');
                    }
                });
            }

            if (hasProductError) {
                document.getElementById('product-list-error').textContent = 'Vui lòng chọn ít nhất 1 sản phẩm và kiểm tra lại số lượng.';
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