@extends('layouts.app')

@section('content')

<div class="container py-4">

    <div class="row">

        {{-- Sidebar --}}
        <div class="col-lg-3 mb-4">
            @include('profile.sidebar')
        </div>

        {{-- Content --}}
        <div class="col-lg-9">

            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            {{-- ================= THÔNG TIN CÁ NHÂN ================= --}}
            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        Thông tin cá nhân
                    </h5>
                </div>

                <div class="card-body">

                    <form action="{{ route('profile.update') }}"
                        method="POST">

                        @csrf
                        @method('PATCH')

                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Họ và tên
                                </label>

                                <input type="text"
                                    name="name"
                                    value="{{ old('name',$user->name) }}"
                                    class="form-control @error('name') is-invalid @enderror">

                                @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Email</label>

                                <input type="email"
                                    name="email"
                                    value="{{ old('email',$user->email) }}"
                                    class="form-control @error('email') is-invalid @enderror">

                                @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Số điện thoại</label>

                                <input type="text"
                                    name="phone"
                                    value="{{ old('phone',$user->phone) }}"
                                    class="form-control @error('phone') is-invalid @enderror">

                                @error('phone')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                        </div>

                        <button class="btn btn-primary">
                            Lưu thay đổi
                        </button>

                    </form>

                </div>

            </div>

            {{-- ================= SỔ ĐỊA CHỈ ================= --}}
            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header bg-white d-flex justify-content-between align-items-center">

                    <h5 class="mb-0">
                        Sổ địa chỉ
                    </h5>

                </div>

                <div class="card-body">

                    {{-- Thêm địa chỉ --}}

                    <form action="{{ route('profile.address.store') }}"
                        method="POST">

                        @csrf

                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <input class="form-control"
                                    name="full_name"
                                    placeholder="Họ tên người nhận"
                                    required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <input class="form-control"
                                    name="phone"
                                    placeholder="Số điện thoại"
                                    required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <input class="form-control"
                                    name="province"
                                    placeholder="Tỉnh / Thành phố"
                                    required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <input class="form-control"
                                    name="district"
                                    placeholder="Quận / Huyện"
                                    required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <input class="form-control"
                                    name="ward"
                                    placeholder="Phường / Xã"
                                    required>
                            </div>

                            <div class="col-md-12 mb-3">
                                <input class="form-control"
                                    name="street"
                                    placeholder="Số nhà, tên đường..."
                                    required>
                            </div>

                        </div>

                        <button class="btn btn-success">
                            Thêm địa chỉ
                        </button>

                    </form>

                    <hr>

                    @forelse($addresses as $address)

                    <div class="border rounded p-3 mb-3">

                        <div class="d-flex justify-content-between">

                            <div>

                                <h6 class="mb-1">
                                    {{ $address->full_name }}
                                </h6>

                                <div class="text-muted">
                                    {{ $address->phone }}
                                </div>

                                <div class="mt-2">
                                    {{ $address->full_address }}
                                </div>

                                @if($address->is_default)

                                <span class="badge bg-success mt-2">
                                    Địa chỉ mặc định
                                </span>

                                @else

                                <form action="{{ route('profile.address.default',$address) }}"
                                    method="POST"
                                    class="d-inline">

                                    @csrf
                                    @method('PATCH')

                                    <button class="btn btn-link p-0 mt-2">
                                        Đặt mặc định
                                    </button>

                                </form>

                                @endif

                            </div>

                            <div class="text-end">

                                <form action="{{ route('profile.address.destroy',$address) }}"
                                    method="POST">

                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Xóa địa chỉ này?')">
                                        Xóa
                                    </button>

                                </form>

                            </div>

                        </div>

                    </div>

                    @empty

                    <p class="text-muted mb-0">
                        Chưa có địa chỉ nào.
                    </p>

                    @endforelse

                </div>

            </div>

            {{-- ================= ĐỔI MẬT KHẨU ================= --}}
            <div class="card shadow-sm border-0">

                <div class="card-header bg-white">

                    <h5 class="mb-0">
                        Đổi mật khẩu
                    </h5>

                </div>

                <div class="card-body">

                    <form action="{{ route('profile.password.update') }}"
                        method="POST">

                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label>Mật khẩu hiện tại</label>
                            <input type="password"
                                name="current_password"
                                class="form-control @error('current_password') is-invalid @enderror">

                            @error('current_password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label>Mật khẩu mới</label>
                            <input type="password"
                                name="password"
                                class="form-control @error('password') is-invalid @enderror">

                            @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label>Xác nhận mật khẩu</label>
                            <input type="password"
                                name="password_confirmation"
                                class="form-control">
                        </div>
                        <button class="btn btn-primary">
                            Đổi mật khẩu
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection