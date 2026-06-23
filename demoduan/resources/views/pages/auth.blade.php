{{-- ============================================ --}}
{{-- FILE: resources/views/pages/auth.blade.php --}}
{{-- (Dùng chung cho login & register, toggle bằng JS) --}}
{{-- ============================================ --}}
@extends('layouts.app')
@section('title', 'Đăng nhập / Đăng ký - ElectronicShop')

@push('styles')
<style>
/* Reset footer trust for auth page */
body { background: #f5f5f5; }

.auth-wrapper {
    min-height: calc(100vh - 120px); display: flex; align-items: stretch;
    max-width: 1100px; margin: 32px auto; border-radius: 12px;
    overflow: hidden; box-shadow: 0 8px 32px rgba(0,0,0,.12); background: #fff;
}

/* LEFT PANEL */
.auth-left {
    width: 520px; flex-shrink: 0;
    background: linear-gradient(160deg, #EBF3FF 0%, #dce8fb 100%);
    padding: 48px 44px; display: flex; flex-direction: column; justify-content: space-between;
    position: relative; overflow: hidden;
}
.auth-left::after {
    content: ''; position: absolute; bottom: -60px; right: -60px;
    width: 240px; height: 240px; border-radius: 50%;
    background: rgba(21,101,192,.07); pointer-events: none;
}
.auth-brand { font-size: 26px; font-weight: 800; color: #1a1a1a; margin-bottom: 8px; }
.auth-tagline { font-size: 15px; color: #555; margin-bottom: 36px; line-height: 1.6; }
.auth-features { list-style: none; margin-bottom: 32px; }
.auth-features li { display: flex; align-items: flex-start; gap: 14px; margin-bottom: 18px; }
.auth-features li .feat-icon {
    width: 40px; height: 40px; border-radius: 10px; background: #fff;
    display: flex; align-items: center; justify-content: center;
    color: #1565C0; font-size: 17px; flex-shrink: 0; box-shadow: 0 2px 8px rgba(21,101,192,.15);
}
.auth-features li .feat-text strong { font-size: 14px; font-weight: 700; display: block; margin-bottom: 2px; }
.auth-features li .feat-text span { font-size: 12px; color: #777; }
.auth-phone-img {
    margin-top: auto; display: flex; justify-content: center; align-items: flex-end;
}
.auth-phone-img img { max-height: 240px; object-fit: contain; }
.auth-phone-placeholder {
    width: 160px; height: 260px; background: linear-gradient(160deg,#1565C0,#0d47a1);
    border-radius: 28px; display: flex; align-items: center; justify-content: center;
    color: rgba(255,255,255,.3); font-size: 48px;
    box-shadow: 0 16px 40px rgba(21,101,192,.3);
}

/* RIGHT PANEL */
.auth-right { flex: 1; padding: 52px 48px; display: flex; flex-direction: column; justify-content: center; }
.auth-tab-bar { display: flex; gap: 0; border-bottom: 2px solid #e0e0e0; margin-bottom: 32px; }
.auth-tab {
    padding: 10px 0; margin-right: 32px; font-size: 18px; font-weight: 700;
    color: #aaa; cursor: pointer; border-bottom: 2px solid transparent;
    margin-bottom: -2px; transition: all .2s; background: none; border-top: none; border-left: none; border-right: none;
}
.auth-tab.active { color: #1a1a1a; border-bottom-color: #1565C0; }
.auth-subtitle { font-size: 14px; color: #888; margin-top: 4px; margin-bottom: 24px; }

/* FORM */
.auth-form { display: none; }
.auth-form.active { display: block; }
.auth-input-group { position: relative; margin-bottom: 16px; }
.auth-input-group label { display: block; font-size: 12px; font-weight: 600; color: #555; margin-bottom: 5px; text-transform: uppercase; letter-spacing: .3px; }
.auth-input-wrap { position: relative; }
.auth-input-wrap i.prefix {
    position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
    color: #bbb; font-size: 15px;
}
.auth-input-wrap input {
    width: 100%; border: 1.5px solid #e0e0e0; border-radius: 8px;
    padding: 11px 14px 11px 38px; font-size: 14px; outline: none; font-family: inherit;
    transition: border-color .15s; color: #333;
}
.auth-input-wrap input:focus { border-color: #1565C0; }
.auth-input-wrap .toggle-pw {
    position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
    color: #bbb; cursor: pointer; background: none; border: none; font-size: 15px;
}
.auth-remember { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; font-size: 13px; }
.auth-remember label { display: flex; align-items: center; gap: 7px; cursor: pointer; color: #555; }
.auth-remember input { accent-color: #1565C0; }
.auth-remember a { color: #1565C0; font-weight: 500; }
.btn-auth {
    width: 100%; padding: 13px; background: #1565C0; color: #fff; border: none;
    border-radius: 8px; font-size: 15px; font-weight: 700; cursor: pointer;
    transition: background .2s; margin-bottom: 20px;
}
.btn-auth:hover { background: #0D47A1; }
.auth-divider { display: flex; align-items: center; gap: 12px; margin-bottom: 18px; font-size: 12px; color: #bbb; text-transform: uppercase; letter-spacing: .5px; }
.auth-divider::before, .auth-divider::after { content: ''; flex: 1; height: 1px; background: #e0e0e0; }
.social-auth { display: flex; gap: 12px; margin-bottom: 24px; }
.btn-social {
    flex: 1; padding: 11px; border: 1.5px solid #e0e0e0; border-radius: 8px;
    background: #fff; font-size: 13px; font-weight: 600; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    transition: border-color .15s, box-shadow .15s;
}
.btn-social:hover { border-color: #1565C0; box-shadow: 0 2px 8px rgba(21,101,192,.1); }
.btn-social.google { color: #333; }
.btn-social.facebook { color: #1877F2; }
.btn-social img { width: 18px; height: 18px; }
.auth-switch { text-align: center; font-size: 14px; color: #777; }
.auth-switch a { color: #1565C0; font-weight: 600; cursor: pointer; }

/* Register extra */
.form-row-two { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
</style>
@endpush

@section('content')
@section('hide_trust', true)
<div class="auth-wrapper">
    {{-- LEFT --}}
    <div class="auth-left">
        <div>
            <div class="auth-brand">Chào mừng bạn đến với<br><strong>EletronShop</strong></div>
            <p class="auth-tagline">Đăng nhập để trải nghiệm mua sắm dễ dàng và nhận nhiều ưu đãi hấp dẫn!</p>
            <ul class="auth-features">
                <li>
                    <div class="feat-icon"><i class="fas fa-tag"></i></div>
                    <div class="feat-text">
                        <strong>Nhiều ưu đãi</strong>
                        <span>Dành riêng cho thành viên</span>
                    </div>
                </li>
                <li>
                    <div class="feat-icon"><i class="fas fa-shield-alt"></i></div>
                    <div class="feat-text">
                        <strong>Bảo mật thông tin</strong>
                        <span>Cam kết bảo mật tuyệt đối</span>
                    </div>
                </li>
                <li>
                    <div class="feat-icon"><i class="fas fa-shipping-fast"></i></div>
                    <div class="feat-text">
                        <strong>Theo dõi đơn hàng</strong>
                        <span>Dễ dàng mọi lúc mọi nơi</span>
                    </div>
                </li>
            </ul>
        </div>
        <div class="auth-phone-img">
            @if(isset($phoneImage))
                <img src="{{ $phoneImage }}" alt="Phone">
            @else
                <div class="auth-phone-placeholder"><i class="fas fa-mobile-alt"></i></div>
            @endif
        </div>
    </div>

    {{-- RIGHT --}}
    <div class="auth-right">
        <div class="auth-tab-bar">
            <button class="auth-tab active" onclick="switchAuthTab('login', this)">Đăng nhập</button>
            <button class="auth-tab" onclick="switchAuthTab('register', this)">Đăng ký</button>
        </div>

        {{-- LOGIN --}}
        <div id="tab-login" class="auth-form active">
            <p class="auth-subtitle">Chào mừng bạn quay trở lại!</p>
            @if(session('error'))
            <div style="background:#FFEBEE;border:1px solid #FFCDD2;border-radius:6px;padding:10px 14px;margin-bottom:14px;color:#C62828;font-size:13px">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
            @endif
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="auth-input-group">
                    <label>Email hoặc số điện thoại</label>
                    <div class="auth-input-wrap">
                        <i class="fas fa-user prefix"></i>
                        <input type="text" name="login" placeholder="Nhập email hoặc số điện thoại" value="{{ old('login') }}" required>
                    </div>
                    @error('login')<span style="color:#E53935;font-size:12px">{{ $message }}</span>@enderror
                </div>
                <div class="auth-input-group">
                    <label>Mật khẩu</label>
                    <div class="auth-input-wrap">
                        <i class="fas fa-lock prefix"></i>
                        <input type="password" name="password" id="loginPw" placeholder="Nhập mật khẩu" required>
                        <button type="button" class="toggle-pw" onclick="togglePw('loginPw',this)"><i class="fas fa-eye"></i></button>
                    </div>
                    @error('password')<span style="color:#E53935;font-size:12px">{{ $message }}</span>@enderror
                </div>
                <div class="auth-remember">
                    <label><input type="checkbox" name="remember"> Ghi nhớ đăng nhập</label>
                    <a href="{{ route('password.request') }}">Quên mật khẩu?</a>
                </div>
                <button type="submit" class="btn-auth">Đăng nhập</button>
            </form>
            <div class="auth-divider">Hoặc đăng nhập với</div>
            <div class="social-auth">
                <a href="{{ route('auth.google') ?? '#' }}" class="btn-social google">
                    <svg width="18" height="18" viewBox="0 0 48 48"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.29-8.16 2.29-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg>
                    Google
                </a>
                <a href="{{ route('auth.facebook') ?? '#' }}" class="btn-social facebook">
                    <i class="fab fa-facebook-f"></i> Facebook
                </a>
            </div>
            <div class="auth-switch">Chưa có tài khoản? <a onclick="switchAuthTab('register', document.querySelectorAll('.auth-tab')[1])">Đăng ký ngay</a></div>
        </div>

        {{-- REGISTER --}}
        <div id="tab-register" class="auth-form">
            <p class="auth-subtitle">Tạo tài khoản miễn phí ngay hôm nay!</p>
            <form action="{{ route('register') }}" method="POST">
                @csrf
                <div class="form-row-two" style="margin-bottom:16px">
                    <div class="auth-input-group" style="margin-bottom:0">
                        <label>Họ và tên <span style="color:#E53935">*</span></label>
                        <div class="auth-input-wrap">
                            <i class="fas fa-user prefix"></i>
                            <input type="text" name="name" placeholder="Nguyễn Văn A" value="{{ old('name') }}" required>
                        </div>
                    </div>
                    <div class="auth-input-group" style="margin-bottom:0">
                        <label>Số điện thoại <span style="color:#E53935">*</span></label>
                        <div class="auth-input-wrap">
                            <i class="fas fa-phone prefix"></i>
                            <input type="tel" name="phone" placeholder="0123 456 789" value="{{ old('phone') }}" required>
                        </div>
                    </div>
                </div>
                <div class="auth-input-group">
                    <label>Email <span style="color:#E53935">*</span></label>
                    <div class="auth-input-wrap">
                        <i class="fas fa-envelope prefix"></i>
                        <input type="email" name="email" placeholder="email@example.com" value="{{ old('email') }}" required>
                    </div>
                    @error('email')<span style="color:#E53935;font-size:12px">{{ $message }}</span>@enderror
                </div>
                <div class="auth-input-group">
                    <label>Mật khẩu <span style="color:#E53935">*</span></label>
                    <div class="auth-input-wrap">
                        <i class="fas fa-lock prefix"></i>
                        <input type="password" name="password" id="regPw" placeholder="Tối thiểu 8 ký tự" required>
                        <button type="button" class="toggle-pw" onclick="togglePw('regPw',this)"><i class="fas fa-eye"></i></button>
                    </div>
                    @error('password')<span style="color:#E53935;font-size:12px">{{ $message }}</span>@enderror
                </div>
                <div class="auth-input-group">
                    <label>Xác nhận mật khẩu <span style="color:#E53935">*</span></label>
                    <div class="auth-input-wrap">
                        <i class="fas fa-lock prefix"></i>
                        <input type="password" name="password_confirmation" id="regPwC" placeholder="Nhập lại mật khẩu" required>
                        <button type="button" class="toggle-pw" onclick="togglePw('regPwC',this)"><i class="fas fa-eye"></i></button>
                    </div>
                </div>
                <div style="margin-bottom:20px;font-size:13px;color:#555">
                    <label style="display:flex;align-items:flex-start;gap:8px;cursor:pointer">
                        <input type="checkbox" name="agree_terms" required style="margin-top:2px;accent-color:#1565C0">
                        <span>Tôi đồng ý với <a href="#" style="color:#1565C0">Điều khoản dịch vụ</a> và <a href="#" style="color:#1565C0">Chính sách bảo mật</a> của ElectronicShop</span>
                    </label>
                </div>
                <button type="submit" class="btn-auth">Đăng ký ngay</button>
            </form>
            <div class="auth-switch">Đã có tài khoản? <a onclick="switchAuthTab('login', document.querySelectorAll('.auth-tab')[0])">Đăng nhập</a></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function switchAuthTab(tab, btn) {
    document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.auth-form').forEach(f => f.classList.remove('active'));
    if (btn) btn.classList.add('active');
    document.getElementById('tab-' + tab).classList.add('active');
}
function togglePw(id, btn) {
    const input = document.getElementById(id);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}
// Auto switch tab based on URL hash
if (window.location.hash === '#register') {
    switchAuthTab('register', document.querySelectorAll('.auth-tab')[1]);
}
</script>
@endpush
