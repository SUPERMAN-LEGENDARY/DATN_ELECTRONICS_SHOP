@extends('layouts.app')
@section('title', 'Hồ sơ cá nhân - ElectronicShop')

@push('styles')
<style>
@verbatim
/* ============================================================
   PAGE BACKGROUND — Samsung Minimalist (Trắng/Xám nhạt)
   ============================================================ */
body {
    background: linear-gradient(180deg,
        #f8f9fa 0%,
        #f1f3f5 38%,
        #e9ecef 100%) fixed;
    background-attachment: fixed;
    color: #000000;
}

#sky-canvas {
    position: fixed; inset: 0;
    width: 100%; height: 100%;
    pointer-events: none; z-index: 0; opacity: .6;
}

.bubble {
    position: fixed; border-radius: 50%;
    background: radial-gradient(circle at 35% 35%, rgba(255,255,255,1), rgba(200,200,200,.2));
    border: 1px solid rgba(0,0,0,.05);
    pointer-events: none; z-index: 0;
    animation: bubbleRise linear infinite;
}
@keyframes bubbleRise {
    0%   { transform: translateY(0) scale(1);    opacity: .5; }
    80%  { opacity: .2; }
    100% { transform: translateY(-110vh) scale(1.1); opacity: 0; }
}

/* ============================================================
   SCROLL REVEAL
   ============================================================ */
.reveal {
    opacity: 0; transform: translateY(26px);
    transition: opacity .6s cubic-bezier(.16,1,.3,1), transform .6s cubic-bezier(.16,1,.3,1);
}
.reveal.revealed { opacity: 1; transform: translateY(0); }

.stagger-children > * {
    opacity: 0; transform: translateY(18px);
    transition: opacity .5s cubic-bezier(.16,1,.3,1), transform .5s cubic-bezier(.16,1,.3,1);
}
.stagger-children.revealed > *:nth-child(1)  { opacity:1; transform:none; transition-delay:.05s; }
.stagger-children.revealed > *:nth-child(2)  { opacity:1; transform:none; transition-delay:.12s; }
.stagger-children.revealed > *:nth-child(3)  { opacity:1; transform:none; transition-delay:.19s; }
.stagger-children.revealed > *:nth-child(n+4){ opacity:1; transform:none; transition-delay:.26s; }

/* ripple */
.ripple-wave {
    position: absolute; border-radius: 50%;
    background: rgba(255,255,255,.4);
    transform: scale(0); animation: rippleOut .6s linear;
    pointer-events: none; z-index: 10;
}
@keyframes rippleOut { to { transform:scale(4); opacity:0; } }

/* ============================================================
   PAGE WRAPPER
   ============================================================ */
.profile-page {
    min-height: 100vh;
    padding: 32px 0 60px;
    position: relative; z-index: 1;
}

.profile-container {
    max-width: 1200px; margin: 0 auto;
    padding: 0 16px;
    display: grid;
    grid-template-columns: 260px 1fr;
    gap: 24px;
    align-items: start;
    position: relative; z-index: 1;
}
@media (max-width: 991px) {
    .profile-container { grid-template-columns: 1fr; }
}

/* ============================================================
   SIDEBAR 
   ============================================================ */
.profile-sidebar-wrap {
    position: sticky; top: 88px;
}

/* ============================================================
   SECTION CARD — Clean White
   ============================================================ */
.profile-card {
    background: #ffffff;
    border: 1px solid #ebebeb;
    border-radius: 20px;
    padding: 0;
    box-shadow: 0 4px 20px rgba(0,0,0,.03);
    margin-bottom: 24px;
    overflow: hidden;
    transition: box-shadow .28s, transform .2s;
}
.profile-card:hover {
    box-shadow: 0 8px 30px rgba(0,0,0,.06);
}

.profile-card-header {
    background: #ffffff;
    padding: 20px 24px;
    border-bottom: 1px solid #eeeeee;
    display: flex; align-items: center; justify-content: space-between;
}
.profile-card-header h5 {
    margin: 0;
    font-size: 16px; font-weight: 700;
    color: #000000; letter-spacing: .3px;
    display: flex; align-items: center; gap: 10px;
}
.profile-card-header h5 .header-icon {
    width: 32px; height: 32px; border-radius: 50%;
    background: #f4f4f4;
    color: #000000; font-size: 13px;
    display: flex; align-items: center; justify-content: center;
}

.profile-card-body { padding: 28px 24px; }

/* ============================================================
   ALERT
   ============================================================ */
.alert-success {
    display: flex; align-items: center; gap: 8px;
    background: #e6f4ea;
    color: #137333;
    border: 1px solid #ceead6;
    padding: 14px 18px; border-radius: 12px;
    margin-bottom: 24px; font-weight: 600; font-size: 14px;
    animation: alertIn .4s cubic-bezier(.16,1,.3,1);
}
@keyframes alertIn { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:none; } }

/* ============================================================
   FORM FIELDS — Samsung Style
   ============================================================ */
.form-label {
    display: block; font-size: 14px; font-weight: 600;
    color: #333333; margin-bottom: 8px;
}

.form-input {
    width: 100%; padding: 14px 16px;
    border: 1px solid #cccccc;
    border-radius: 12px; font-size: 15px;
    outline: none; box-sizing: border-box;
    background: #fafafa;
    color: #000000; font-family: inherit;
    transition: all .2s ease;
}
.form-input::placeholder { color: #999999; }
.form-input:focus {
    border-color: #000000;
    background: #ffffff;
    box-shadow: 0 0 0 1px #000000;
}
.form-input.is-invalid {
    border-color: #d93025;
    box-shadow: 0 0 0 1px #d93025;
}
.invalid-msg { color: #d93025; font-size: 12.5px; margin-top: 6px; display: block; }

.form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.form-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
@media (max-width: 640px) {
    .form-grid-2, .form-grid-3 { grid-template-columns: 1fr; }
}
.form-group { display: flex; flex-direction: column; }

/* ============================================================
   BUTTONS — Pill Shape
   ============================================================ */
.btn-main {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 14px 28px; border: none; border-radius: 30px;
    background: #000000;
    color: #ffffff; font-size: 15px; font-weight: 600; cursor: pointer;
    transition: all .2s;
    box-shadow: 0 4px 14px rgba(0,0,0,.15);
    position: relative; overflow: hidden; font-family: inherit;
    text-decoration: none;
}
.btn-main:hover { 
    background: #333333; 
    transform: translateY(-2px); 
    box-shadow: 0 6px 20px rgba(0,0,0,.2); 
}

.btn-blue {
    background: #2189ff; /* Samsung Blue */
    box-shadow: 0 4px 14px rgba(33,137,255,.2);
}
.btn-blue:hover { 
    background: #0066cc;
    box-shadow: 0 6px 20px rgba(33,137,255,.3); 
}

.btn-danger {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 16px; border-radius: 20px;
    background: rgba(217,48,37,.08); color: #d93025;
    border: 1px solid rgba(217,48,37,.2);
    font-size: 13.5px; font-weight: 600; cursor: pointer;
    transition: all .2s;
    font-family: inherit;
}
.btn-danger:hover {
    background: #d93025; color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(217,48,37,.25);
}

.btn-text-link {
    background: none; border: none; padding: 0;
    color: #555555; font-weight: 600; font-size: 13.5px;
    cursor: pointer; font-family: inherit;
    text-decoration: underline; text-underline-offset: 3px;
    transition: color .2s;
}
.btn-text-link:hover { color: #000000; }

/* ============================================================
   ADDRESS ITEMS
   ============================================================ */
.address-item {
    background: #ffffff;
    border: 1px solid #ebebeb;
    border-radius: 16px; padding: 20px;
    margin-bottom: 16px;
    display: flex; justify-content: space-between; align-items: flex-start;
    gap: 16px;
    transition: all .2s;
}
.address-item:hover {
    border-color: #cccccc;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,.04);
}
.address-item:last-child { margin-bottom: 0; }

.addr-name { font-size: 16px; font-weight: 700; color: #000000; margin-bottom: 4px; }
.addr-phone { font-size: 14px; color: #555555; margin-bottom: 8px; }
.addr-full { font-size: 14.5px; color: #333333; margin-bottom: 12px; line-height: 1.5; }

.badge-default {
    display: inline-flex; align-items: center; gap: 6px;
    background: #f4f4f4; color: #000000;
    border: 1px solid #e0e0e0;
    font-size: 12px; font-weight: 600; padding: 4px 12px; border-radius: 20px;
}

.addr-empty {
    text-align: center; padding: 32px 20px;
    color: #999999; font-size: 15px;
}

/* ============================================================
   ADD ADDRESS FORM SECTION
   ============================================================ */
.add-addr-section {
    background: #fafafa;
    border: 1px dashed #cccccc;
    border-radius: 16px; padding: 24px;
    margin-bottom: 28px;
}
.add-addr-section h6 {
    font-size: 14px; font-weight: 700; color: #000000;
    text-transform: uppercase; letter-spacing: .5px;
    margin-bottom: 20px;
    display: flex; align-items: center; gap: 8px;
}
.add-addr-section h6::before {
    content: ''; width: 20px; height: 3px;
    background: #000000;
    border-radius: 2px; display: inline-block;
}
@endverbatim
</style>
@endpush

@section('content')
{{-- Sky Canvas (Updated to Silver/Gray) --}}
<canvas id="sky-canvas" aria-hidden="true"></canvas>

<div class="profile-page">
<div class="profile-container">

    {{-- ===== SIDEBAR ===== --}}
    <div class="profile-sidebar-wrap reveal">
        @include('profile.sidebar')
    </div>

    {{-- ===== CONTENT ===== --}}
    <div class="stagger-children">

        @if(session('success'))
        <div class="alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
        @endif

        {{-- ─── 1. Thông tin cá nhân ─── --}}
        <div class="profile-card">
            <div class="profile-card-header">
                <h5>
                    <span class="header-icon"><i class="fas fa-user"></i></span>
                    Thông tin cá nhân
                </h5>
            </div>
            <div class="profile-card-body">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="form-grid-2" style="margin-bottom:20px">
                        <div class="form-group">
                            <label class="form-label">Họ và tên</label>
                            <input type="text" name="name"
                                   value="{{ old('name', $user->name) }}"
                                   class="form-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                   placeholder="Võ Trần Xuân Thật">
                            @error('name')<span class="invalid-msg"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email"
                                   value="{{ old('email', $user->email) }}"
                                   class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                   placeholder="email@example.com">
                            @error('email')<span class="invalid-msg"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom:28px; max-width:320px">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="phone"
                               value="{{ old('phone', $user->phone) }}"
                               class="form-input {{ $errors->has('phone') ? 'is-invalid' : '' }}"
                               placeholder="0901 234 567">
                        @error('phone')<span class="invalid-msg"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                    </div>

                    <button type="submit" class="btn-main">
                        Lưu thay đổi <i class="fas fa-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>

        {{-- ─── 2. Sổ địa chỉ ─── --}}
        <div class="profile-card">
            <div class="profile-card-header">
                <h5>
                    <span class="header-icon"><i class="fas fa-map-marker-alt"></i></span>
                    Sổ địa chỉ
                </h5>
            </div>
            <div class="profile-card-body">

                {{-- Form thêm địa chỉ --}}
                <div class="add-addr-section">
                    <h6>Thêm địa chỉ mới</h6>
                    <form action="{{ route('profile.address.store') }}" method="POST">
                        @csrf

                        <div class="form-grid-2" style="margin-bottom:20px">
                            <div class="form-group">
                                <input class="form-input" name="full_name" placeholder="Họ tên người nhận" required>
                            </div>
                            <div class="form-group">
                                <input class="form-input" name="phone" placeholder="Số điện thoại" required>
                            </div>
                        </div>

                        <div class="form-grid-3" style="margin-bottom:20px">
                            <div class="form-group">
                                <input class="form-input" name="province" placeholder="Tỉnh / Thành phố" required>
                            </div>
                            <div class="form-group">
                                <input class="form-input" name="district" placeholder="Quận / Huyện" required>
                            </div>
                            <div class="form-group">
                                <input class="form-input" name="ward" placeholder="Phường / Xã" required>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom:24px">
                            <input class="form-input" name="street" placeholder="Số nhà, tên đường..." required>
                        </div>

                        <button type="submit" class="btn-main btn-blue">
                            <i class="fas fa-plus"></i> Thêm địa chỉ mới
                        </button>
                    </form>
                </div>

                {{-- Danh sách địa chỉ --}}
                @forelse($addresses as $address)
                <div class="address-item">
                    <div>
                        <div class="addr-name">{{ $address->full_name }}</div>
                        <div class="addr-phone"><i class="fas fa-phone" style="font-size:12px;margin-right:6px"></i>{{ $address->phone }}</div>
                        <div class="addr-full"><i class="fas fa-map-marker-alt" style="font-size:12px;margin-right:6px;color:#999"></i>{{ $address->full_address }}</div>
                        @if($address->is_default)
                            <span class="badge-default">
                                <i class="fas fa-check"></i> Địa chỉ mặc định
                            </span>
                        @else
                            <form action="{{ route('profile.address.default', $address) }}" method="POST" style="display:inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-text-link">
                                    Đặt làm mặc định
                                </button>
                            </form>
                        @endif
                    </div>
                    <div>
                        <form action="{{ route('profile.address.destroy', $address) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-danger"
                                    onclick="return confirm('Xóa địa chỉ này?')">
                                <i class="fas fa-trash-alt"></i> Xóa
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="addr-empty">
                    <i class="fas fa-map-marked-alt" style="font-size:36px;display:block;margin-bottom:12px;color:#ccc"></i>
                    Bạn chưa lưu địa chỉ nào.
                </div>
                @endforelse

            </div>
        </div>

        {{-- ─── 3. Đổi mật khẩu ─── --}}
        <div class="profile-card">
            <div class="profile-card-header">
                <h5>
                    <span class="header-icon"><i class="fas fa-lock"></i></span>
                    Đổi mật khẩu
                </h5>
            </div>
            <div class="profile-card-body">
                <form action="{{ route('profile.password.update') }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="form-group" style="margin-bottom:20px; max-width:460px">
                        <label class="form-label">Mật khẩu hiện tại</label>
                        <input type="password" name="current_password"
                               class="form-input {{ $errors->has('current_password') ? 'is-invalid' : '' }}"
                               placeholder="••••••••">
                        @error('current_password')<span class="invalid-msg"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                    </div>

                    <div class="form-group" style="margin-bottom:20px; max-width:460px">
                        <label class="form-label">Mật khẩu mới</label>
                        <input type="password" name="password"
                               class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                               placeholder="••••••••">
                        @error('password')<span class="invalid-msg"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                    </div>

                    <div class="form-group" style="margin-bottom:28px; max-width:460px">
                        <label class="form-label">Xác nhận mật khẩu mới</label>
                        <input type="password" name="password_confirmation"
                               class="form-input"
                               placeholder="••••••••">
                    </div>

                    <button type="submit" class="btn-main">
                        Cập nhật mật khẩu <i class="fas fa-check"></i>
                    </button>
                </form>
            </div>
        </div>

    </div>{{-- /.stagger-children --}}
</div>{{-- /.profile-container --}}
</div>{{-- /.profile-page --}}
@endsection

@push('scripts')
<script>
(function () {

    /* ---- Canvas clouds (Samsung Style: Silver/Gray) ---- */
    const canvas = document.getElementById('sky-canvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let W, H, clouds = [];
        function resize() { W = canvas.width = window.innerWidth; H = canvas.height = window.innerHeight; }
        window.addEventListener('resize', resize); resize();
        function makeCloud() {
            return { x: Math.random()*W*1.2, y: Math.random()*H*.6,
                     r: 50+Math.random()*110, dx: .1+Math.random()*.2,
                     alpha: .02+Math.random()*.05 };
        }
        for (let i = 0; i < 6; i++) clouds.push(makeCloud());
        function drawCloud(c) {
            const g = ctx.createRadialGradient(c.x,c.y,0,c.x,c.y,c.r);
            g.addColorStop(0, `rgba(200,205,210,${c.alpha})`);
            g.addColorStop(.6, `rgba(220,224,228,${c.alpha*.6})`);
            g.addColorStop(1, 'rgba(230,230,230,0)');
            ctx.beginPath(); ctx.arc(c.x,c.y,c.r,0,Math.PI*2);
            ctx.fillStyle = g; ctx.fill();
        }
        (function anim() {
            ctx.clearRect(0,0,W,H);
            clouds.forEach(c => { drawCloud(c); c.x += c.dx;
                if (c.x-c.r > W*1.2) { c.x=-c.r*2; c.y=Math.random()*H*.6; } });
            requestAnimationFrame(anim);
        })();
    }

    /* ---- Bubbles ---- */
    function spawnBubble() {
        const el = document.createElement('div'); el.className = 'bubble';
        const size = 3+Math.random()*10, dur = 10+Math.random()*15;
        el.style.cssText = [`width:${size}px`,`height:${size}px`,
            `left:${Math.random()*100}vw`,`bottom:-${size}px`,
            `animation-duration:${dur}s`,`animation-delay:${Math.random()*5}s`].join(';');
        document.body.appendChild(el);
        setTimeout(() => el.remove(), (dur+5)*1000);
    }
    for (let i = 0; i < 6; i++) spawnBubble();
    setInterval(spawnBubble, 4500);

    /* ---- Scroll Reveal ---- */
    const io = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) { e.target.classList.add('revealed'); io.unobserve(e.target); }
        });
    }, { threshold: 0.05, rootMargin: '0px 0px -20px 0px' });
    document.querySelectorAll('.reveal, .stagger-children').forEach(el => io.observe(el));

    /* ---- Button ripple ---- */
    document.querySelectorAll('.btn-main, .btn-blue').forEach(btn => {
        btn.addEventListener('click', function (e) {
            const r    = btn.getBoundingClientRect();
            const size = Math.max(r.width, r.height) * 1.8;
            const rip  = document.createElement('span');
            rip.className = 'ripple-wave';
            rip.style.cssText = [`width:${size}px`,`height:${size}px`,
                `left:${e.clientX-r.left-size/2}px`,
                `top:${e.clientY-r.top-size/2}px`].join(';');
            btn.appendChild(rip);
            rip.addEventListener('animationend', () => rip.remove());
        });
    });

})();
</script>
@endpush