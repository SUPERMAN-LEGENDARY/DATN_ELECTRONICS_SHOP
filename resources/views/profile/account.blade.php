@extends('layouts.app')

@section('title', 'Thông tin của tôi - ElectronicShop')

@push('styles')
<style>
/* ============================================================
   ACCOUNT PAGE - BLACK / WHITE
   ============================================================ */

.account-page {
    min-height: 100vh;
    background: #f5f5f5;
    padding: 42px 0 80px;
    color: #111;
}

.account-container {
    width: min(1100px, calc(100% - 32px));
    margin: 0 auto;
}


/* ============================================================
   HEADER
   ============================================================ */

.account-heading {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 28px;
}

.account-heading-left h1 {
    margin: 0 0 8px;
    color: #000;
    font-size: 32px;
    line-height: 1.15;
    font-weight: 800;
    letter-spacing: -1px;
}

.account-heading-left p {
    margin: 0;
    color: #777;
    font-size: 14px;
    line-height: 1.5;
}

.account-back {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;

    min-height: 42px;
    padding: 0 17px;

    border: 1px solid #ddd;
    border-radius: 999px;

    background: #fff;
    color: #111;

    text-decoration: none;
    font-size: 13px;
    font-weight: 700;

    transition: all .2s ease;
}

.account-back:hover {
    background: #000;
    border-color: #000;
    color: #fff;
    transform: translateY(-1px);
}


/* ============================================================
   SUCCESS ALERT
   ============================================================ */

.account-alert {
    display: flex;
    align-items: center;
    gap: 12px;

    margin-bottom: 20px;
    padding: 15px 18px;

    background: #ecfdf3;
    border: 1px solid #bbf7d0;
    border-radius: 14px;

    color: #166534;
    font-size: 14px;
    font-weight: 600;

    box-shadow: 0 5px 18px rgba(22, 101, 52, .06);

    animation: alertIn .35s ease;
}

.account-alert-icon {
    width: 30px;
    height: 30px;

    display: flex;
    align-items: center;
    justify-content: center;

    flex-shrink: 0;

    border-radius: 50%;

    background: #16a34a;
    color: #fff;

    font-size: 12px;
}

@keyframes alertIn {
    from {
        opacity: 0;
        transform: translateY(-8px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}


/* ============================================================
   CARD
   ============================================================ */

.account-card {
    margin-bottom: 20px;

    overflow: hidden;

    background: #fff;
    border: 1px solid #e5e5e5;
    border-radius: 18px;

    box-shadow: 0 5px 25px rgba(0, 0, 0, .035);

    transition:
        box-shadow .25s ease,
        transform .25s ease;
}

.account-card:hover {
    box-shadow: 0 12px 35px rgba(0, 0, 0, .07);
}

.account-card-header {
    display: flex;
    align-items: center;
    gap: 14px;

    padding: 20px 24px;

    border-bottom: 1px solid #ededed;
}

.account-card-icon {
    width: 44px;
    height: 44px;

    display: flex;
    align-items: center;
    justify-content: center;

    flex-shrink: 0;

    border-radius: 13px;

    background: #000;
    color: #fff;

    font-size: 15px;
}

.account-card-title {
    margin: 0;

    color: #000;
    font-size: 16px;
    line-height: 1.3;
    font-weight: 800;
}

.account-card-subtitle {
    margin: 4px 0 0;

    color: #888;
    font-size: 12.5px;
}

.account-card-body {
    padding: 26px 24px;
}


/* ============================================================
   FORM
   ============================================================ */

.account-form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;

    margin-bottom: 20px;
}

.account-form-group {
    display: flex;
    flex-direction: column;
}

.account-label {
    margin-bottom: 8px;

    color: #222;
    font-size: 13px;
    font-weight: 750;
}

.account-input {
    width: 100%;
    min-height: 48px;

    box-sizing: border-box;

    padding: 0 15px;

    outline: none;

    border: 1px solid #dcdcdc;
    border-radius: 11px;

    background: #fafafa;
    color: #111;

    font-family: inherit;
    font-size: 14px;

    transition:
        border-color .2s ease,
        background .2s ease,
        box-shadow .2s ease;
}

.account-input:hover {
    border-color: #aaa;
}

.account-input:focus {
    background: #fff;
    border-color: #000;
    box-shadow: 0 0 0 3px rgba(0, 0, 0, .06);
}

.account-input.is-invalid {
    border-color: #111;
    box-shadow: 0 0 0 2px rgba(0, 0, 0, .08);
}

.account-error {
    display: block;

    margin-top: 6px;

    color: #111;
    font-size: 12px;
    font-weight: 600;
}

.account-error i {
    margin-right: 4px;
}


/* ============================================================
   ADDRESS COMBOBOX (Tỉnh/Thành phố, Quận/Huyện, Phường/Xã)
   ============================================================ */

.addr-combo {
    position: relative;
}

.addr-combo-input {
    cursor: text;
}

.addr-combo-input.is-disabled {
    background: #f0f0f0;
    color: #999;
    cursor: not-allowed;
}

.addr-combo-list {
    display: none;
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    right: 0;
    z-index: 60;

    max-height: 260px;
    overflow-y: auto;

    background: #fff;
    border: 1px solid #dcdcdc;
    border-radius: 12px;
    box-shadow: 0 14px 34px rgba(0, 0, 0, .10);

    padding: 6px;
}

.addr-combo-list.is-open {
    display: block;
}

.addr-combo-item {
    padding: 10px 12px;
    border-radius: 8px;

    font-size: 14px;
    color: #222;
    cursor: pointer;

    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.addr-combo-item:hover,
.addr-combo-item.is-active {
    background: #f2f2f2;
}

.addr-combo-empty,
.addr-combo-hint {
    padding: 10px 12px;
    font-size: 13px;
    color: #999;
}


/* ============================================================
   MAIN BUTTON
   ============================================================ */

.account-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;

    min-height: 46px;
    padding: 0 22px;

    border: 1px solid #000;
    border-radius: 11px;

    background: #000;
    color: #fff;

    font-family: inherit;
    font-size: 13px;
    font-weight: 750;

    cursor: pointer;
    text-decoration: none;

    transition: all .2s ease;
}

.account-btn:hover {
    background: #fff;
    color: #000;
    transform: translateY(-1px);
}

.account-btn i {
    font-size: 12px;
}


/* ============================================================
   ADDRESS
   ============================================================ */

.address-add-box {
    margin-bottom: 25px;
    padding: 21px;

    background: #f7f7f7;
    border: 1px solid #e5e5e5;
    border-radius: 15px;
}

.address-add-title {
    display: flex;
    align-items: center;
    gap: 8px;

    margin: 0 0 20px;

    color: #111;
    font-size: 14px;
    font-weight: 800;
}

.address-add-title i {
    font-size: 11px;
}

.address-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.address-item {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 20px;

    padding: 18px;

    background: #fff;
    border: 1px solid #e4e4e4;
    border-radius: 14px;

    transition:
        border-color .2s ease,
        box-shadow .2s ease;
}

.address-item:hover {
    border-color: #aaa;
    box-shadow: 0 7px 22px rgba(0, 0, 0, .05);
}

.address-main {
    min-width: 0;
}

.address-name {
    margin-bottom: 6px;

    color: #000;
    font-size: 15px;
    font-weight: 800;
}

.address-phone {
    margin-bottom: 7px;

    color: #555;
    font-size: 13px;
}

.address-phone i {
    margin-right: 6px;
    font-size: 11px;
}

.address-full {
    color: #555;
    font-size: 13px;
    line-height: 1.6;
}

.address-full i {
    margin-right: 6px;
    color: #999;
    font-size: 11px;
}

.address-default {
    display: inline-flex;
    align-items: center;
    gap: 6px;

    margin-top: 10px;
    padding: 5px 10px;

    border-radius: 999px;

    background: #000;
    color: #fff;

    font-size: 11px;
    font-weight: 700;
}

.address-default i {
    font-size: 9px;
}

.address-default-btn {
    display: inline-block;

    margin-top: 10px;
    padding: 0;

    border: 0;
    background: transparent;

    color: #555;

    font-family: inherit;
    font-size: 12px;
    font-weight: 700;

    cursor: pointer;

    text-decoration: underline;
    text-underline-offset: 3px;
}

.address-default-btn:hover {
    color: #000;
}

.address-actions {
    flex-shrink: 0;
}

.address-delete {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;

    min-height: 36px;
    padding: 0 13px;

    border: 1px solid #ddd;
    border-radius: 9px;

    background: #fff;
    color: #555;

    font-family: inherit;
    font-size: 12px;
    font-weight: 700;

    cursor: pointer;

    transition: all .2s ease;
}

.address-delete:hover {
    background: #000;
    border-color: #000;
    color: #fff;
}

.address-empty {
    padding: 38px 20px;

    text-align: center;

    border: 1px dashed #d5d5d5;
    border-radius: 14px;

    color: #999;
    font-size: 13px;
}

.address-empty i {
    display: block;

    margin-bottom: 11px;

    color: #ccc;
    font-size: 30px;
}


/* ============================================================
   PASSWORD
   ============================================================ */

.password-layout {
    display: grid;
    grid-template-columns: 280px minmax(0, 1fr);
    gap: 35px;
    align-items: stretch;
}


/* Password information */

.password-info {
    padding: 25px;

    background: #f6f6f6;
    border: 1px solid #e5e5e5;
    border-radius: 16px;
}

.password-info-icon {
    width: 48px;
    height: 48px;

    display: flex;
    align-items: center;
    justify-content: center;

    margin-bottom: 18px;

    border-radius: 14px;

    background: #000;
    color: #fff;

    font-size: 16px;
}

.password-info h3 {
    margin: 0 0 9px;

    color: #111;
    font-size: 16px;
    font-weight: 800;
}

.password-info p {
    margin: 0 0 20px;

    color: #777;
    font-size: 12.5px;
    line-height: 1.65;
}

.password-rule {
    display: flex;
    align-items: center;
    gap: 9px;

    margin-top: 11px;

    color: #555;
    font-size: 12px;
}

.password-rule i {
    width: 19px;
    height: 19px;

    display: flex;
    align-items: center;
    justify-content: center;

    flex-shrink: 0;

    border-radius: 50%;

    background: #e8e8e8;
    color: #111;

    font-size: 8px;
}


/* Password form */

.password-form-wrap {
    width: 100%;
    max-width: 560px;
}

.password-field {
    margin-bottom: 19px;
}

.password-input-wrap {
    position: relative;
}

.password-input {
    padding-left: 43px;
    padding-right: 48px;
}

.password-field-icon {
    position: absolute;

    left: 15px;
    top: 50%;

    z-index: 2;

    transform: translateY(-50%);

    color: #999;
    font-size: 12px;

    pointer-events: none;
}

.password-toggle {
    position: absolute;

    right: 9px;
    top: 50%;

    width: 35px;
    height: 35px;

    display: flex;
    align-items: center;
    justify-content: center;

    transform: translateY(-50%);

    border: 0;
    border-radius: 8px;

    background: transparent;
    color: #999;

    cursor: pointer;

    transition: all .2s ease;
}

.password-toggle:hover {
    background: #eee;
    color: #000;
}

.password-submit {
    width: 100%;
    min-height: 50px;

    display: flex;
    align-items: center;
    justify-content: center;
    gap: 9px;

    margin-top: 8px;

    border: 1px solid #000;
    border-radius: 11px;

    background: #000;
    color: #fff;

    font-family: inherit;
    font-size: 13px;
    font-weight: 750;

    cursor: pointer;

    transition: all .2s ease;
}

.password-submit:hover {
    background: #fff;
    color: #000;
    transform: translateY(-1px);
}

.password-submit i {
    font-size: 12px;
}


/* ============================================================
   RESPONSIVE
   ============================================================ */

@media (max-width: 800px) {

    .password-layout {
        grid-template-columns: 1fr;
        gap: 22px;
    }

    .password-form-wrap {
        max-width: none;
    }

}

@media (max-width: 768px) {

    .account-page {
        padding: 28px 0 55px;
    }

    .account-heading {
        align-items: flex-start;
        flex-direction: column;
    }

    .account-heading-left h1 {
        font-size: 27px;
    }

    .account-form-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }

    .account-card-header {
        padding: 18px;
    }

    .account-card-body {
        padding: 20px 18px;
    }

    .address-item {
        flex-direction: column;
    }

    .address-actions {
        width: 100%;
    }

    .address-delete {
        width: 100%;
    }

}

@media (max-width: 480px) {

    .account-container {
        width: calc(100% - 20px);
    }

    .account-heading-left h1 {
        font-size: 24px;
    }

    .account-card {
        border-radius: 14px;
    }

    .account-btn {
        width: 100%;
    }

    .account-back {
        width: 100%;
    }

}
</style>
@endpush


@section('content')

<div class="account-page">

    <div class="account-container">


        {{-- ====================================================
             HEADER
             ==================================================== --}}
        <div class="account-heading">

            <div class="account-heading-left">

                <h1>
                    Thông tin của tôi
                </h1>

                <p>
                    Quản lý thông tin cá nhân, địa chỉ và mật khẩu của bạn.
                </p>

            </div>


            {{-- Về trang HỒ SƠ --}}
            <a href="{{ route('profile') }}" class="btn-back-profile"> <i class="fas fa-arrow-left"></i> Hồ sơ </a>

        </div>


        {{-- ====================================================
             SUCCESS MESSAGE
             ==================================================== --}}
        @if(session('success'))

            <div class="account-alert">

                <span class="account-alert-icon">
                    <i class="fas fa-check"></i>
                </span>

                <span>
                    {{ session('success') }}
                </span>

            </div>

        @endif


        {{-- ====================================================
             1. THÔNG TIN CÁ NHÂN
             ==================================================== --}}
        <div class="account-card">

            <div class="account-card-header">

                <div class="account-card-icon">
                    <i class="fas fa-user"></i>
                </div>

                <div>

                    <h2 class="account-card-title">
                        Thông tin cá nhân
                    </h2>

                    <p class="account-card-subtitle">
                        Cập nhật thông tin tài khoản của bạn
                    </p>

                </div>

            </div>


            <div class="account-card-body">

                <form
                    action="{{ route('profile.update') }}"
                    method="POST"
                >

                    @csrf
                    @method('PATCH')


                    <div class="account-form-grid">


                        {{-- HỌ TÊN --}}
                        <div class="account-form-group">

                            <label class="account-label">
                                Họ và tên
                            </label>

                            <input
                                type="text"
                                name="name"
                                value="{{ old('name', $user->name) }}"
                                class="account-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                placeholder="Nhập họ và tên"
                            >

                            @error('name')

                                <span class="account-error">
                                    <i class="fas fa-circle-exclamation"></i>
                                    {{ $message }}
                                </span>

                            @enderror

                        </div>


                        {{-- EMAIL --}}
                        <div class="account-form-group">

                            <label class="account-label">
                                Email
                            </label>

                            <input
                                type="email"
                                name="email"
                                value="{{ old('email', $user->email) }}"
                                class="account-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                placeholder="email@example.com"
                            >

                            @error('email')

                                <span class="account-error">
                                    <i class="fas fa-circle-exclamation"></i>
                                    {{ $message }}
                                </span>

                            @enderror

                        </div>

                    </div>


                    {{-- PHONE --}}
                    <div
                        class="account-form-group"
                        style="max-width:420px;margin-bottom:25px;"
                    >

                        <label class="account-label">
                            Số điện thoại
                        </label>

                        <input
                            type="text"
                            name="phone"
                            value="{{ old('phone', $user->phone) }}"
                            class="account-input {{ $errors->has('phone') ? 'is-invalid' : '' }}"
                            placeholder="0901 234 567"
                        >

                        @error('phone')

                            <span class="account-error">
                                <i class="fas fa-circle-exclamation"></i>
                                {{ $message }}
                            </span>

                        @enderror

                    </div>


                    <button
                        type="submit"
                        class="account-btn"
                    >
                        <i class="fas fa-check"></i>
                        Lưu thay đổi
                    </button>

                </form>

            </div>

        </div>


        {{-- ====================================================
             2. SỔ ĐỊA CHỈ
             ==================================================== --}}
        <div class="account-card">

            <div class="account-card-header">

                <div class="account-card-icon">
                    <i class="fas fa-location-dot"></i>
                </div>

                <div>

                    <h2 class="account-card-title">
                        Sổ địa chỉ
                    </h2>

                    <p class="account-card-subtitle">
                        Quản lý địa chỉ nhận hàng của bạn
                    </p>

                </div>

            </div>


            <div class="account-card-body">


                {{-- THÊM ĐỊA CHỈ --}}
                <div class="address-add-box">

                    <h3 class="address-add-title">

                        <i class="fas fa-plus"></i>

                        Thêm địa chỉ mới

                    </h3>


                    <form
                        action="{{ route('profile.address.store') }}"
                        method="POST"
                    >

                        @csrf


                        {{-- NAME + PHONE --}}
                        <div class="account-form-grid">

                            <div class="account-form-group">

                                <label class="account-label">
                                    Họ tên người nhận
                                </label>

                                <input
                                    class="account-input"
                                    name="full_name"
                                    placeholder="Nguyễn Văn A"
                                    required
                                >

                            </div>


                            <div class="account-form-group">

                                <label class="account-label">
                                    Số điện thoại
                                </label>

                                <input
                                    class="account-input"
                                    name="phone"
                                    placeholder="0901 234 567"
                                    required
                                >

                            </div>

                        </div>


                        {{-- PROVINCE + DISTRICT --}}
                        <div class="account-form-grid">

                            <div class="account-form-group">

                                <label class="account-label">
                                    Tỉnh / Thành phố
                                </label>

                                <div class="addr-combo" id="provinceCombo">
                                    <input
                                        type="text"
                                        class="account-input addr-combo-input"
                                        id="provinceInput"
                                        name="province"
                                        placeholder="Tỉnh / Thành phố"
                                        autocomplete="off"
                                        required
                                    >
                                    <input type="hidden" name="province_code" id="provinceCode">
                                    <div class="addr-combo-list" id="provinceList"></div>
                                </div>

                            </div>


                            <div class="account-form-group">

                                <label class="account-label">
                                    Quận / Huyện
                                </label>

                                <div class="addr-combo" id="districtCombo">
                                    <input
                                        type="text"
                                        class="account-input addr-combo-input is-disabled"
                                        id="districtInput"
                                        name="district"
                                        placeholder="Chọn Tỉnh / Thành phố trước"
                                        autocomplete="off"
                                        disabled
                                        required
                                    >
                                    <input type="hidden" name="district_code" id="districtCode">
                                    <div class="addr-combo-list" id="districtList"></div>
                                </div>

                            </div>

                        </div>


                        {{-- WARD --}}
                        <div
                            class="account-form-group"
                            style="margin-bottom:20px;"
                        >

                            <label class="account-label">
                                Phường / Xã
                            </label>

                            <div class="addr-combo" id="wardCombo">
                                <input
                                    type="text"
                                    class="account-input addr-combo-input is-disabled"
                                    id="wardInput"
                                    name="ward"
                                    placeholder="Chọn Quận / Huyện trước"
                                    autocomplete="off"
                                    disabled
                                    required
                                >
                                <input type="hidden" name="ward_code" id="wardCode">
                                <div class="addr-combo-list" id="wardList"></div>
                            </div>

                        </div>


                        {{-- STREET --}}
                        <div
                            class="account-form-group"
                            style="margin-bottom:20px;"
                        >

                            <label class="account-label">
                                Số nhà, tên đường
                            </label>

                            <input
                                class="account-input"
                                name="street"
                                placeholder="Số nhà, tên đường..."
                                required
                            >

                        </div>


                        <button
                            type="submit"
                            class="account-btn"
                        >

                            <i class="fas fa-plus"></i>

                            Thêm địa chỉ

                        </button>

                    </form>

                </div>


                {{-- DANH SÁCH ĐỊA CHỈ --}}
                <div class="address-list">

                    @forelse($addresses as $address)

                        <div class="address-item">


                            <div class="address-main">

                                <div class="address-name">
                                    {{ $address->full_name }}
                                </div>


                                <div class="address-phone">

                                    <i class="fas fa-phone"></i>

                                    {{ $address->phone }}

                                </div>


                                <div class="address-full">

                                    <i class="fas fa-location-dot"></i>

                                    {{ $address->full_address }}

                                </div>


                                @if($address->is_default)

                                    <span class="address-default">

                                        <i class="fas fa-check"></i>

                                        Địa chỉ mặc định

                                    </span>

                                @else

                                    <form
                                        action="{{ route('profile.address.default', $address) }}"
                                        method="POST"
                                    >

                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="address-default-btn"
                                        >
                                            Đặt làm mặc định
                                        </button>

                                    </form>

                                @endif

                            </div>


                            <div class="address-actions">

                                <form
                                    action="{{ route('profile.address.destroy', $address) }}"
                                    method="POST"
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="address-delete"
                                        onclick="return confirm('Xóa địa chỉ này?')"
                                    >

                                        <i class="fas fa-trash"></i>

                                        Xóa

                                    </button>

                                </form>

                            </div>

                        </div>

                    @empty

                        <div class="address-empty">

                            <i class="fas fa-map-location-dot"></i>

                            Bạn chưa lưu địa chỉ nào.

                        </div>

                    @endforelse

                </div>

            </div>

        </div>


        {{-- ====================================================
             3. ĐỔI MẬT KHẨU
             ==================================================== --}}
        <div class="account-card password-card">

            <div class="account-card-header">

                <div class="account-card-icon">
                    <i class="fas fa-lock"></i>
                </div>

                <div>

                    <h2 class="account-card-title">
                        Đổi mật khẩu
                    </h2>

                    <p class="account-card-subtitle">
                        Cập nhật mật khẩu để bảo vệ tài khoản
                    </p>

                </div>

            </div>


            <div class="account-card-body">

                <div class="password-layout">


                    {{-- PASSWORD INFO --}}
                    <div class="password-info">

                        <div class="password-info-icon">
                            <i class="fas fa-shield-halved"></i>
                        </div>


                        <h3>
                            Bảo mật tài khoản
                        </h3>


                        <p>
                            Sử dụng mật khẩu mạnh để bảo vệ thông tin
                            và đơn hàng của bạn.
                        </p>


                        <div class="password-rule">

                            <i class="fas fa-check"></i>

                            Tối thiểu 8 ký tự

                        </div>


                        <div class="password-rule">

                            <i class="fas fa-check"></i>

                            Có chữ cái và chữ số

                        </div>


                        <div class="password-rule">

                            <i class="fas fa-check"></i>

                            Không sử dụng mật khẩu dễ đoán

                        </div>

                    </div>


                    {{-- PASSWORD FORM --}}
                    <div class="password-form-wrap">

                        <form
                            action="{{ route('profile.password.update') }}"
                            method="POST"
                        >

                            @csrf
                            @method('PATCH')


                            {{-- CURRENT PASSWORD --}}
                            <div class="password-field">

                                <label class="account-label">
                                    Mật khẩu hiện tại
                                </label>


                                <div class="password-input-wrap">

                                    <i class="fas fa-lock password-field-icon"></i>


                                    <input
                                        type="password"
                                        name="current_password"
                                        id="current_password"
                                        class="account-input password-input {{ $errors->has('current_password') ? 'is-invalid' : '' }}"
                                        placeholder="Nhập mật khẩu hiện tại"
                                        autocomplete="current-password"
                                    >


                                    <button
                                        type="button"
                                        class="password-toggle"
                                        data-target="current_password"
                                        aria-label="Hiện mật khẩu"
                                    >

                                        <i class="fas fa-eye"></i>

                                    </button>

                                </div>


                                @error('current_password')

                                    <span class="account-error">

                                        <i class="fas fa-circle-exclamation"></i>

                                        {{ $message }}

                                    </span>

                                @enderror

                            </div>


                            {{-- NEW PASSWORD --}}
                            <div class="password-field">

                                <label class="account-label">
                                    Mật khẩu mới
                                </label>


                                <div class="password-input-wrap">

                                    <i class="fas fa-key password-field-icon"></i>


                                    <input
                                        type="password"
                                        name="password"
                                        id="password"
                                        class="account-input password-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                        placeholder="Nhập mật khẩu mới"
                                        autocomplete="new-password"
                                    >


                                    <button
                                        type="button"
                                        class="password-toggle"
                                        data-target="password"
                                        aria-label="Hiện mật khẩu"
                                    >

                                        <i class="fas fa-eye"></i>

                                    </button>

                                </div>


                                @error('password')

                                    <span class="account-error">

                                        <i class="fas fa-circle-exclamation"></i>

                                        {{ $message }}

                                    </span>

                                @enderror

                            </div>


                            {{-- CONFIRM PASSWORD --}}
                            <div class="password-field">

                                <label class="account-label">
                                    Xác nhận mật khẩu mới
                                </label>


                                <div class="password-input-wrap">

                                    <i class="fas fa-shield-halved password-field-icon"></i>


                                    <input
                                        type="password"
                                        name="password_confirmation"
                                        id="password_confirmation"
                                        class="account-input password-input"
                                        placeholder="Nhập lại mật khẩu mới"
                                        autocomplete="new-password"
                                    >


                                    <button
                                        type="button"
                                        class="password-toggle"
                                        data-target="password_confirmation"
                                        aria-label="Hiện mật khẩu"
                                    >

                                        <i class="fas fa-eye"></i>

                                    </button>

                                </div>

                            </div>


                            {{-- SUBMIT --}}
                            <button
                                type="submit"
                                class="password-submit"
                            >

                                <i class="fas fa-shield-halved"></i>

                                Cập nhật mật khẩu

                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>


    </div>

</div>

@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | HIỆN / ẨN MẬT KHẨU
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll('.password-toggle').forEach(function (button) {

        button.addEventListener('click', function () {

            const targetId = this.dataset.target;
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');

            if (!input) {
                return;
            }


            if (input.type === 'password') {

                input.type = 'text';

                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');

                this.setAttribute(
                    'aria-label',
                    'Ẩn mật khẩu'
                );

            } else {

                input.type = 'password';

                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');

                this.setAttribute(
                    'aria-label',
                    'Hiện mật khẩu'
                );

            }

        });

    });


    /*
    |--------------------------------------------------------------------------
    | ĐỊA CHỈ: TỈNH / QUẬN-HUYỆN / PHƯỜNG-XÃ (combobox có gợi ý + lọc)
    |--------------------------------------------------------------------------
    */

    (function () {

        const provinceInput = document.getElementById('provinceInput');
        if (!provinceInput) return; // trang không có form địa chỉ

        const provinceCode  = document.getElementById('provinceCode');
        const provinceList  = document.getElementById('provinceList');

        const districtInput = document.getElementById('districtInput');
        const districtCode  = document.getElementById('districtCode');
        const districtList  = document.getElementById('districtList');

        const wardInput = document.getElementById('wardInput');
        const wardCode  = document.getElementById('wardCode');
        const wardList  = document.getElementById('wardList');

        const API_BASE = 'https://provinces.open-api.vn/api';

        let provinces      = [];
        let districtsCache = {}; // provinceCode -> [{code,name}]
        let wardsCache      = {}; // districtCode -> [{code,name}]

        // Bỏ dấu tiếng Việt để so khớp không phân biệt dấu/hoa-thường
        function stripAccents(str) {
            return str
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/đ/g, 'd')
                .replace(/Đ/g, 'D')
                .toLowerCase()
                .trim();
        }

        // Bỏ tiền tố hành chính (Thành phố/Tỉnh/Quận/Huyện/Thị xã/Phường/Xã/Thị trấn...)
        // để gõ "h" vẫn ra được "Thành phố Hà Nội", "Tỉnh Hà Giang"
        const ADMIN_PREFIXES = [
            'thanh pho', 'tinh', 'quan', 'huyen', 'thi xa',
            'phuong', 'xa', 'thi tran',
        ];

        function coreName(name) {
            let s = stripAccents(name);
            for (let i = 0; i < ADMIN_PREFIXES.length; i++) {
                const p = ADMIN_PREFIXES[i] + ' ';
                if (s.indexOf(p) === 0) {
                    s = s.slice(p.length);
                    break;
                }
            }
            return s;
        }

        function setDisabled(input, disabled, placeholder) {
            input.disabled = disabled;
            input.classList.toggle('is-disabled', disabled);
            if (placeholder) input.placeholder = placeholder;
        }

        function closeList(listEl) {
            listEl.classList.remove('is-open');
            listEl.innerHTML = '';
        }

        function renderList(listEl, items, onPick, query) {
            listEl.innerHTML = '';

            const q = stripAccents(query || '');

            const filtered = q
                ? items.filter(function (it) {
                    return coreName(it.name).indexOf(q) === 0
                        || stripAccents(it.name).indexOf(q) === 0;
                })
                : items;

            if (!filtered.length) {
                const empty = document.createElement('div');
                empty.className = 'addr-combo-empty';
                empty.textContent = 'Không tìm thấy kết quả phù hợp';
                listEl.appendChild(empty);
                listEl.classList.add('is-open');
                return;
            }

            filtered.slice(0, 200).forEach(function (it) {
                const opt = document.createElement('div');
                opt.className = 'addr-combo-item';
                opt.textContent = it.name;
                opt.addEventListener('mousedown', function (e) {
                    e.preventDefault(); // tránh blur trước khi click
                    onPick(it);
                });
                listEl.appendChild(opt);
            });

            listEl.classList.add('is-open');
        }

        function fetchJSON(url) {
            return fetch(url, { headers: { 'Accept': 'application/json' } })
                .then(function (res) {
                    if (!res.ok) throw new Error('Network error');
                    return res.json();
                });
        }

        function loadProvinces() {
            if (provinces.length) return Promise.resolve(provinces);

            provinceList.innerHTML = '<div class="addr-combo-hint">Đang tải danh sách...</div>';
            provinceList.classList.add('is-open');

            return fetchJSON(API_BASE + '/p/')
                .then(function (data) {
                    provinces = data.map(function (p) {
                        return { code: p.code, name: p.name };
                    });
                    return provinces;
                })
                .catch(function () {
                    provinceList.innerHTML = '<div class="addr-combo-empty">Không thể tải danh sách, vui lòng thử lại</div>';
                    return [];
                });
        }

        function loadDistricts(pCode) {
            if (districtsCache[pCode]) return Promise.resolve(districtsCache[pCode]);

            districtList.innerHTML = '<div class="addr-combo-hint">Đang tải danh sách...</div>';
            districtList.classList.add('is-open');

            return fetchJSON(API_BASE + '/p/' + pCode + '?depth=2')
                .then(function (data) {
                    const list = (data.districts || []).map(function (d) {
                        return { code: d.code, name: d.name };
                    });
                    districtsCache[pCode] = list;
                    return list;
                })
                .catch(function () {
                    districtList.innerHTML = '<div class="addr-combo-empty">Không thể tải danh sách, vui lòng thử lại</div>';
                    return [];
                });
        }

        function loadWards(dCode) {
            if (wardsCache[dCode]) return Promise.resolve(wardsCache[dCode]);

            wardList.innerHTML = '<div class="addr-combo-hint">Đang tải danh sách...</div>';
            wardList.classList.add('is-open');

            return fetchJSON(API_BASE + '/d/' + dCode + '?depth=2')
                .then(function (data) {
                    const list = (data.wards || []).map(function (w) {
                        return { code: w.code, name: w.name };
                    });
                    wardsCache[dCode] = list;
                    return list;
                })
                .catch(function () {
                    wardList.innerHTML = '<div class="addr-combo-empty">Không thể tải danh sách, vui lòng thử lại</div>';
                    return [];
                });
        }

        function resetDistrictAndWard() {
            districtInput.value = '';
            districtCode.value  = '';
            setDisabled(districtInput, true, 'Chọn Tỉnh / Thành phố trước');
            closeList(districtList);
            resetWard();
        }

        function resetWard() {
            wardInput.value = '';
            wardCode.value  = '';
            setDisabled(wardInput, true, 'Chọn Quận / Huyện trước');
            closeList(wardList);
        }

        /* ----- Tỉnh / Thành phố ----- */

        provinceInput.addEventListener('focus', function () {
            loadProvinces().then(function (list) {
                renderList(provinceList, list, pickProvince, '');
            });
        });

        provinceInput.addEventListener('input', function () {
            provinceCode.value = '';
            renderList(provinceList, provinces, pickProvince, provinceInput.value);

            // Nếu người dùng xoá hết / gõ lại, khoá lại quận-huyện & phường-xã
            resetDistrictAndWard();
        });

        function pickProvince(item) {
            provinceInput.value = item.name;
            provinceCode.value  = item.code;
            closeList(provinceList);

            resetDistrictAndWard();
            setDisabled(districtInput, false, 'Quận / Huyện');
        }

        /* ----- Quận / Huyện ----- */

        districtInput.addEventListener('focus', function () {
            if (districtInput.disabled || !provinceCode.value) return;
            loadDistricts(provinceCode.value).then(function (list) {
                renderList(districtList, list, pickDistrict, '');
            });
        });

        districtInput.addEventListener('input', function () {
            if (!provinceCode.value) return;
            districtCode.value = '';
            loadDistricts(provinceCode.value).then(function (list) {
                renderList(districtList, list, pickDistrict, districtInput.value);
            });

            resetWard();
        });

        function pickDistrict(item) {
            districtInput.value = item.name;
            districtCode.value  = item.code;
            closeList(districtList);

            resetWard();
            setDisabled(wardInput, false, 'Phường / Xã');
        }

        /* ----- Phường / Xã ----- */

        wardInput.addEventListener('focus', function () {
            if (wardInput.disabled || !districtCode.value) return;
            loadWards(districtCode.value).then(function (list) {
                renderList(wardList, list, pickWard, '');
            });
        });

        wardInput.addEventListener('input', function () {
            if (!districtCode.value) return;
            wardCode.value = '';
            loadWards(districtCode.value).then(function (list) {
                renderList(wardList, list, pickWard, wardInput.value);
            });
        });

        function pickWard(item) {
            wardInput.value = item.name;
            wardCode.value  = item.code;
            closeList(wardList);
        }

        /* ----- Đóng dropdown khi click ra ngoài ----- */

        document.addEventListener('click', function (e) {
            if (!e.target.closest('#provinceCombo')) closeList(provinceList);
            if (!e.target.closest('#districtCombo')) closeList(districtList);
            if (!e.target.closest('#wardCombo'))      closeList(wardList);
        });

    })();


    /*
    |--------------------------------------------------------------------------
    | CARD ANIMATION
    |--------------------------------------------------------------------------
    */

    const cards = document.querySelectorAll('.account-card');

    cards.forEach(function (card, index) {

        card.style.opacity = '0';
        card.style.transform = 'translateY(15px)';

        setTimeout(function () {

            card.style.transition =
                'opacity .45s ease, transform .45s ease';

            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';

        }, index * 80);

    });


    /*
    |--------------------------------------------------------------------------
    | TỰ ẨN THÔNG BÁO THÀNH CÔNG
    |--------------------------------------------------------------------------
    */

    const alert = document.querySelector('.account-alert');

    if (alert) {

        setTimeout(function () {

            alert.style.transition =
                'opacity .3s ease, transform .3s ease';

            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-8px)';

            setTimeout(function () {
                alert.remove();
            }, 300);

        }, 4000);

    }

});
</script>
@endpush