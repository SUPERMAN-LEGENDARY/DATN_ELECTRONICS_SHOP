@extends('layouts.app')
@section('title', 'Quên mật khẩu - ElectronicShop')

@push('styles')
<style>
body { background: #f5f5f5; }
.forgot-wrapper {
    max-width: 480px; margin: 60px auto; background: #fff;
    border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,.1);
    overflow: hidden;
}
.forgot-header {
    background: linear-gradient(135deg, #1565C0, #0D47A1);
    padding: 36px 40px; text-align: center;
}
.forgot-header .icon {
    width: 64px; height: 64px; background: rgba(255,255,255,.2);
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    margin: 0 auto 16px; font-size: 28px; color: #fff;
}
.forgot-header h1 { font-size: 22px; font-weight: 800; color: #fff; margin-bottom: 8px; }
.forgot-header p { font-size: 14px; color: rgba(255,255,255,.8); line-height: 1.6; }
.forgot-body { padding: 36px 40px; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; font-size: 12px; font-weight: 700; color: #555; margin-bottom: 6px; text-transform: uppercase; letter-spacing: .3px; }
.input-wrap { position: relative; }
.input-wrap i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #bbb; font-size: 15px; }
.input-wrap input {
    width: 100%; border: 1.5px solid #e0e0e0; border-radius: 8px;
    padding: 11px 14px 11px 38px; font-size: 14px; outline: none;
    transition: border-color .15s;
}
.input-wrap input:focus { border-color: #1565C0; }
.btn-submit {
    width: 100%; padding: 13px; background: #1565C0; color: #fff; border: none;
    border-radius: 8px; font-size: 15px; font-weight: 700; cursor: pointer;
    transition: background .2s; margin-bottom: 16px;
}
.btn-submit:hover { background: #0D47A1; }
.back-link { text-align: center; font-size: 14px; color: #888; }
.back-link a { color: #1565C0; font-weight: 600; }
.success-box {
    background: #E8F5E9; border: 1px solid #C8E6C9; border-radius: 8px;
    padding: 14px 16px; margin-bottom: 20px; color: #2E7D32; font-size: 14px;
    display: flex; align-items: center; gap: 10px;
}
</style>
@endpush

@section('content')
@section('hide_trust', true)
<div class="forgot-wrapper">
    <div class="forgot-header">
        <div class="icon"><i class="fas fa-lock"></i></div>
        <h1>Quên mật khẩu?</h1>
        <p>Nhập email đã đăng ký, chúng tôi sẽ gửi link đặt lại mật khẩu cho bạn.</p>
    </div>
    <div class="forgot-body">
        @if(session('success'))
        <div class="success-box">
            <i class="fas fa-check-circle fa-lg"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        <form action="{{ route('password.send') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Địa chỉ Email <span style="color:#E53935">*</span></label>
                <div class="input-wrap">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="Nhập email đã đăng ký" value="{{ old('email') }}" required>
                </div>
                @error('email')
                <span style="color:#E53935;font-size:12px">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="btn-submit">
                <i class="fas fa-paper-plane"></i> Gửi link đặt lại mật khẩu
            </button>
        </form>
        <div class="back-link">
            Nhớ mật khẩu rồi? <a href="{{ route('login') }}">Đăng nhập</a>
        </div>
    </div>
</div>
@endsection