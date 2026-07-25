@extends('layouts.app')
@section('title', 'Hồ sơ cá nhân - ElectronicShop')

@push('styles')
<style>
@verbatim
/* ============================================================
   PAGE BACKGROUND — sky gradient (khớp trang chủ)
   ============================================================ */
body {
    background: linear-gradient(180deg,
        #bae6fd 0%,
        #e0f2fe 18%,
        #f0f9ff 38%,
        #e0f2fe 62%,
        #bae6fd 100%) fixed;
    background-attachment: fixed;
}

#sky-canvas {
    position: fixed; inset: 0;
    width: 100%; height: 100%;
    pointer-events: none; z-index: 0; opacity: .42;
}

.bubble {
    position: fixed; border-radius: 50%;
    background: radial-gradient(circle at 35% 35%, rgba(255,255,255,.8), rgba(186,230,253,.3));
    border: 1px solid rgba(125,211,252,.4);
    pointer-events: none; z-index: 0;
    animation: bubbleRise linear infinite;
}
@keyframes bubbleRise {
    0%   { transform: translateY(0) scale(1);    opacity: .7; }
    80%  { opacity: .4; }
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
    background: rgba(125,211,252,.28);
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
   SIDEBAR — glassmorphism (wrapper for @include)
   ============================================================ */
.profile-sidebar-wrap {
    position: sticky; top: 88px;
}

/* ============================================================
   SECTION CARD — glassmorphism
   ============================================================ */
.profile-card {
    background: rgba(255,255,255,.82);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(186,230,253,.65);
    border-radius: 18px;
    padding: 0;
    box-shadow: 0 6px 28px rgba(14,165,233,.1);
    margin-bottom: 22px;
    overflow: hidden;
    transition: box-shadow .28s;
}
.profile-card:hover {
    box-shadow: 0 10px 36px rgba(14,165,233,.16);
}

.profile-card-header {
    background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
    padding: 16px 24px;
    border-bottom: 1px solid rgba(186,230,253,.6);
    display: flex; align-items: center; justify-content: space-between;
}
.profile-card-header h5 {
    margin: 0;
    font-size: 15px; font-weight: 800;
    color: #0c4a6e; letter-spacing: .3px;
    display: flex; align-items: center; gap: 8px;
}
.profile-card-header h5 .header-icon {
    width: 28px; height: 28px; border-radius: 8px;
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    color: #fff; font-size: 12px;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 2px 8px rgba(14,165,233,.3);
}

.profile-card-body { padding: 24px; }

/* ============================================================
   ALERT
   ============================================================ */
.alert-success-sky {
    display: flex; align-items: center; gap: 8px;
    background: rgba(220,252,231,.9);
    backdrop-filter: blur(8px);
    color: #166534;
    border: 1px solid rgba(187,247,208,.8);
    padding: 12px 18px; border-radius: 12px;
    margin-bottom: 20px; font-weight: 600; font-size: 14px;
    animation: alertIn .4s cubic-bezier(.16,1,.3,1);
}
@keyframes alertIn { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:none; } }

/* ============================================================
   FORM FIELDS — sky style
   ============================================================ */
.sky-label {
    display: block; font-size: 13px; font-weight: 600;
    color: #0369a1; margin-bottom: 6px;
}

.sky-input {
    width: 100%; padding: 11px 14px;
    border: 1px solid rgba(125,211,252,.55);
    border-radius: 10px; font-size: 14px;
    outline: none; box-sizing: border-box;
    background: rgba(255,255,255,.78);
    color: #0c4a6e; font-family: inherit;
    transition: border-color .2s, box-shadow .2s, background .2s;
}
.sky-input::placeholder { color: #7dd3fc; }
.sky-input:focus {
    border-color: #0ea5e9;
    box-shadow: 0 0 0 3px rgba(14,165,233,.15);
    background: rgba(255,255,255,.95);
}
.sky-input.is-invalid {
    border-color: rgba(239,68,68,.6);
    box-shadow: 0 0 0 3px rgba(239,68,68,.1);
}
.invalid-msg { color: #ef4444; font-size: 12.5px; margin-top: 4px; display: block; }

.form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.form-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; }
@media (max-width: 640px) {
    .form-grid-2, .form-grid-3 { grid-template-columns: 1fr; }
}
.form-group { display: flex; flex-direction: column; }

/* ============================================================
   BUTTONS — primary & secondary sky
   ============================================================ */
.btn-sky {
    display: inline-flex; align-items: center; justify-content: center; gap: 7px;
    padding: 11px 24px; border: none; border-radius: 10px;
    background: linear-gradient(135deg, #0369a1, #0ea5e9);
    color: #fff; font-size: 14px; font-weight: 700; cursor: pointer;
    transition: opacity .2s, transform .18s, box-shadow .2s;
    box-shadow: 0 4px 16px rgba(14,165,233,.32);
    position: relative; overflow: hidden; font-family: inherit;
    text-decoration: none;
}
.btn-sky::after {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,.28) 50%, transparent 60%);
    transform: translateX(-120%); transition: transform .5s ease; pointer-events: none;
}
.btn-sky:hover::after { transform: translateX(120%); }
.btn-sky:hover { opacity:.92; transform:translateY(-2px); color:#fff; box-shadow: 0 8px 22px rgba(14,165,233,.4); }

.btn-sky-green {
    background: linear-gradient(135deg, #16a34a, #22c55e);
    box-shadow: 0 4px 16px rgba(22,163,74,.3);
}
.btn-sky-green:hover { box-shadow: 0 8px 22px rgba(22,163,74,.4); }

.btn-sky-danger {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 7px 14px; border-radius: 8px;
    background: rgba(239,68,68,.1); color: #ef4444;
    border: 1px solid rgba(239,68,68,.28);
    font-size: 13px; font-weight: 700; cursor: pointer;
    transition: background .18s, transform .15s, box-shadow .18s;
    font-family: inherit;
}
.btn-sky-danger:hover {
    background: #ef4444; color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(239,68,68,.3);
}

.btn-sky-link {
    background: none; border: none; padding: 0;
    color: #0ea5e9; font-weight: 700; font-size: 13px;
    cursor: pointer; font-family: inherit;
    text-decoration: underline; text-underline-offset: 2px;
    transition: color .15s;
}
.btn-sky-link:hover { color: #0369a1; }

/* ============================================================
   ADDRESS ITEMS
   ============================================================ */
.addr-divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(186,230,253,.7), transparent);
    margin: 20px 0;
}

.address-item {
    background: rgba(240,249,255,.6);
    backdrop-filter: blur(6px);
    border: 1px solid rgba(186,230,253,.55);
    border-radius: 14px; padding: 16px;
    margin-bottom: 12px;
    display: flex; justify-content: space-between; align-items: flex-start;
    gap: 16px;
    transition: background .2s, border-color .2s, transform .2s, box-shadow .2s;
}
.address-item:hover {
    background: rgba(186,230,253,.3);
    border-color: #7dd3fc;
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(14,165,233,.1);
}
.address-item:last-child { margin-bottom: 0; }

.addr-name { font-size: 15px; font-weight: 700; color: #0c4a6e; margin-bottom: 2px; }
.addr-phone { font-size: 13px; color: #0369a1; opacity: .85; margin-bottom: 6px; }
.addr-full { font-size: 13.5px; color: #0369a1; margin-bottom: 8px; }

.badge-default-sky {
    display: inline-flex; align-items: center; gap: 4px;
    background: rgba(34,197,94,.15); color: #16a34a;
    border: 1px solid rgba(34,197,94,.3);
    font-size: 11.5px; font-weight: 700; padding: 3px 10px; border-radius: 10px;
}

.addr-empty {
    text-align: center; padding: 20px;
    color: #7dd3fc; font-size: 14px; font-style: italic;
}

/* ============================================================
   ADD ADDRESS FORM SECTION
   ============================================================ */
.add-addr-section {
    background: rgba(224,242,254,.5);
    border: 1px dashed rgba(125,211,252,.6);
    border-radius: 14px; padding: 18px;
    margin-bottom: 20px;
}
.add-addr-section h6 {
    font-size: 13px; font-weight: 800; color: #0369a1;
    text-transform: uppercase; letter-spacing: .4px;
    margin-bottom: 14px;
    display: flex; align-items: center; gap: 6px;
}
.add-addr-section h6::before {
    content: ''; width: 16px; height: 2px;
    background: linear-gradient(90deg, #0369a1, #38bdf8);
    border-radius: 2px; display: inline-block;
}
@endverbatim
</style>
@endpush

@section('content')
{{-- Sky Canvas --}}
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
        <div class="alert-success-sky">
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

                    <div class="form-grid-2" style="margin-bottom:14px">
                        <div class="form-group">
                            <label class="sky-label">Họ và tên</label>
                            <input type="text" name="name"
                                   value="{{ old('name', $user->name) }}"
                                   class="sky-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                   placeholder="Nguyễn Văn A">
                            @error('name')<span class="invalid-msg"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="sky-label">Email</label>
                            <input type="email" name="email"
                                   value="{{ old('email', $user->email) }}"
                                   class="sky-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                   placeholder="email@example.com">
                            @error('email')<span class="invalid-msg"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom:20px; max-width:320px">
                        <label class="sky-label">Số điện thoại</label>
                        <input type="text" name="phone"
                               value="{{ old('phone', $user->phone) }}"
                               class="sky-input {{ $errors->has('phone') ? 'is-invalid' : '' }}"
                               placeholder="0901 234 567">
                        @error('phone')<span class="invalid-msg"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                    </div>

                    <button type="submit" class="btn-sky">
                        <i class="fas fa-save"></i> Lưu thay đổi
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

                        <div class="form-grid-2" style="margin-bottom:14px">
                            <div class="form-group">
                                <input class="sky-input" name="full_name" placeholder="Họ tên người nhận" required>
                            </div>
                            <div class="form-group">
                                <input class="sky-input" name="phone" placeholder="Số điện thoại" required>
                            </div>
                        </div>

                        <div class="form-grid-3" style="margin-bottom:14px">
                            <div class="form-group">
                                <input class="sky-input" name="province" placeholder="Tỉnh / Thành phố" required>
                            </div>
                            <div class="form-group">
                                <input class="sky-input" name="district" placeholder="Quận / Huyện" required>
                            </div>
                            <div class="form-group">
                                <input class="sky-input" name="ward" placeholder="Phường / Xã" required>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom:16px">
                            <input class="sky-input" name="street" placeholder="Số nhà, tên đường..." required>
                        </div>

                        <button type="submit" class="btn-sky btn-sky-green">
                            <i class="fas fa-plus"></i> Thêm địa chỉ
                        </button>
                    </form>
                </div>

                {{-- Danh sách địa chỉ --}}
                @forelse($addresses as $address)
                <div class="address-item">
                    <div>
                        <div class="addr-name">{{ $address->full_name }}</div>
                        <div class="addr-phone"><i class="fas fa-phone" style="font-size:11px;margin-right:4px"></i>{{ $address->phone }}</div>
                        <div class="addr-full"><i class="fas fa-map-marker-alt" style="font-size:11px;margin-right:4px;color:#7dd3fc"></i>{{ $address->full_address }}</div>
                        @if($address->is_default)
                            <span class="badge-default-sky">
                                <i class="fas fa-check-circle"></i> Địa chỉ mặc định
                            </span>
                        @else
                            <form action="{{ route('profile.address.default', $address) }}" method="POST" style="display:inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-sky-link">
                                    Đặt làm mặc định
                                </button>
                            </form>
                        @endif
                    </div>
                    <div>
                        <form action="{{ route('profile.address.destroy', $address) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-sky-danger"
                                    onclick="return confirm('Xóa địa chỉ này?')">
                                <i class="fas fa-trash"></i> Xóa
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="addr-empty">
                    <i class="fas fa-map-marked-alt" style="font-size:32px;display:block;margin-bottom:8px"></i>
                    Chưa có địa chỉ nào.
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

                    <div class="form-group" style="margin-bottom:14px; max-width:420px">
                        <label class="sky-label">Mật khẩu hiện tại</label>
                        <input type="password" name="current_password"
                               class="sky-input {{ $errors->has('current_password') ? 'is-invalid' : '' }}"
                               placeholder="••••••••">
                        @error('current_password')<span class="invalid-msg"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                    </div>

                    <div class="form-group" style="margin-bottom:14px; max-width:420px">
                        <label class="sky-label">Mật khẩu mới</label>
                        <input type="password" name="password"
                               class="sky-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                               placeholder="••••••••">
                        @error('password')<span class="invalid-msg"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                    </div>

                    <div class="form-group" style="margin-bottom:20px; max-width:420px">
                        <label class="sky-label">Xác nhận mật khẩu mới</label>
                        <input type="password" name="password_confirmation"
                               class="sky-input"
                               placeholder="••••••••">
                    </div>

                    <button type="submit" class="btn-sky">
                        <i class="fas fa-key"></i> Đổi mật khẩu
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

    /* ---- Canvas clouds ---- */
    const canvas = document.getElementById('sky-canvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let W, H, clouds = [];
        function resize() { W = canvas.width = window.innerWidth; H = canvas.height = window.innerHeight; }
        window.addEventListener('resize', resize); resize();
        function makeCloud() {
            return { x: Math.random()*W*1.2, y: Math.random()*H*.6,
                     r: 50+Math.random()*110, dx: .13+Math.random()*.2,
                     alpha: .05+Math.random()*.1 };
        }
        for (let i = 0; i < 8; i++) clouds.push(makeCloud());
        function drawCloud(c) {
            const g = ctx.createRadialGradient(c.x,c.y,0,c.x,c.y,c.r);
            g.addColorStop(0, `rgba(255,255,255,${c.alpha})`);
            g.addColorStop(.6, `rgba(186,230,253,${c.alpha*.6})`);
            g.addColorStop(1, 'rgba(186,230,253,0)');
            ctx.beginPath(); ctx.arc(c.x,c.y,c.r,0,Math.PI*2);
            ctx.fillStyle = g; ctx.fill();
            [-.5,.5].forEach(o => {
                ctx.beginPath();
                ctx.arc(c.x+c.r*.55*o, c.y-c.r*.18, c.r*.72, 0, Math.PI*2);
                ctx.fillStyle = `rgba(255,255,255,${c.alpha*.7})`; ctx.fill();
            });
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
        const size = 4+Math.random()*14, dur = 8+Math.random()*12;
        el.style.cssText = [`width:${size}px`,`height:${size}px`,
            `left:${Math.random()*100}vw`,`bottom:-${size}px`,
            `animation-duration:${dur}s`,`animation-delay:${Math.random()*5}s`].join(';');
        document.body.appendChild(el);
        setTimeout(() => el.remove(), (dur+5)*1000);
    }
    for (let i = 0; i < 8; i++) spawnBubble();
    setInterval(spawnBubble, 3500);

    /* ---- Scroll Reveal ---- */
    const io = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) { e.target.classList.add('revealed'); io.unobserve(e.target); }
        });
    }, { threshold: 0.05, rootMargin: '0px 0px -20px 0px' });
    document.querySelectorAll('.reveal, .stagger-children').forEach(el => io.observe(el));

    /* ---- Button ripple ---- */
    document.querySelectorAll('.btn-sky').forEach(btn => {
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

    /* ---- Input focus lift ---- */
    document.querySelectorAll('.sky-input').forEach(inp => {
        inp.addEventListener('focus', () => {
            if (inp.parentElement) {
                inp.parentElement.style.transform = 'scale(1.005)';
                inp.parentElement.style.transition = 'transform .2s';
            }
        });
        inp.addEventListener('blur', () => {
            if (inp.parentElement) inp.parentElement.style.transform = 'scale(1)';
        });
    });

})();
</script>
@endpush
