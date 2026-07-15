@extends('layouts.app')

@section('title','Đơn mua')

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
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <h2 class="fw-bold mb-4">Đơn mua</h2>

            {{-- Tabs --}}
            <ul class="nav nav-pills nav-fill order-tabs flex-wrap">

                <li class="nav-item">
                    <a href="{{ route('profile.order',['status'=>'all']) }}"
                        class="nav-link {{ $status=='all' ? 'active' : '' }}">
                        Tất cả
                        <span>{{ $counts->total }}</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('profile.order',['status'=>'pending']) }}"
                        class="nav-link {{ $status=='pending' ? 'active' : '' }}">
                        Chờ xác nhận
                        <span>{{ $counts->pending }}</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('profile.order',['status'=>'processing']) }}"
                        class="nav-link {{ $status=='processing' ? 'active' : '' }}">
                        Đang xử lý
                        <span>{{ $counts->processing }}</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('profile.order',['status'=>'shipped']) }}"
                        class="nav-link {{ $status=='shipped' ? 'active' : '' }}">
                        Đang giao
                        <span>{{ $counts->shipped }}</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('profile.order',['status'=>'delivered']) }}"
                        class="nav-link {{ $status=='delivered' ? 'active' : '' }}">
                        Hoàn thành
                        <span>{{ $counts->delivered }}</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('profile.order',['status'=>'cancelled']) }}"
                        class="nav-link {{ $status=='cancelled' ? 'active' : '' }}">
                        Đã hủy
                        <span>{{ $counts->cancelled }}</span>
                    </a>
                </li>

            </ul>

            {{-- Search --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('profile.order') }}" class="input-group">
                        <input type="hidden" name="status" value="{{ $status }}">
                        <span class="input-group-text bg-white">
                            <i class="fa fa-search"></i>
                        </span>
                        <input
                            type="text"
                            name="keyword"
                            value="{{ $keyword }}"
                            class="form-control border-start-0"
                            placeholder="Tìm theo mã đơn hoặc tên sản phẩm...">
                        <button class="btn btn-primary" type="submit">Tìm</button>
                    </form>
                </div>
            </div>

            {{-- Orders --}}

            @forelse($orders as $order)

            @php
            $item = $order->items->first();
            $product = $item?->product;

            switch ($order->status) {
            case 'pending':
            $badge = 'warning'; $text = 'Chờ xác nhận'; break;
            case 'confirmed':
            $badge = 'info'; $text = 'Đã xác nhận'; break;
            case 'processing':
            $badge = 'info'; $text = 'Đang xử lý'; break;
            case 'shipped':
            $badge = 'primary'; $text = 'Đang giao hàng'; break;
            case 'delivered':
            $badge = 'success'; $text = 'Hoàn thành'; break;
            case 'cancelled':
            $badge = 'danger'; $text = 'Đã hủy'; break;
            case 'returned':
            $badge = 'secondary'; $text = 'Đã hoàn trả'; break;
            default:
            $badge = 'secondary'; $text = $order->status;
            }
            @endphp

            <div class="card shadow-sm border-0 mb-4 order-card">
                <div class="card-body">

                    {{-- Header --}}
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div>
                            <div class="fw-bold">
                                Mã đơn:
                                <span class="text-primary">#{{ $order->id }}</span>
                            </div>
                            <small class="text-muted">
                                {{ $order->created_at->format('d/m/Y H:i') }}
                            </small>
                        </div>
                        <span class="badge bg-{{ $badge }}">{{ $text }}</span>
                    </div>

                    <hr>

                    {{-- Product --}}
                    <div class="row align-items-center">

                        <div class="col-lg-8">
                            <div class="d-flex">
                                <img
                                    src="{{ $product?->first_image ?? asset('images/no-image.png') }}"
                                    width="90" height="90"
                                    class="rounded border object-fit-cover"
                                    alt="{{ $product?->name }}">

                                <div class="ms-3">
                                    <h5 class="mb-2">
                                        {{ $item?->product_name ?? $product?->name }}
                                    </h5>
                                    <div class="text-muted">
                                        x{{ $item?->quantity }}
                                        @if($order->items->count() > 1)
                                        <span class="text-muted">và {{ $order->items->count() - 1 }} sản phẩm khác</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-2 text-center">
                            <div class="text-muted small">Thành tiền</div>
                            <div class="fw-bold text-primary fs-4">
                                {{ number_format($order->total, 0, ',', '.') }}₫
                            </div>
                        </div>

                        <div class="col-lg-2 text-end">

                            <a href="{{ route('profile.order.show',$order) }}"
                                class="btn btn-outline-primary w-100 mb-2">
                                <i class="fa fa-eye"></i>
                                Xem chi tiết
                            </a>

                            @if(in_array($order->status, ['pending','confirmed','processing']))

                            <form method="POST" action="{{ route('profile.order.cancel',$order) }}">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-outline-danger w-100" onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">
                                    Hủy đơn
                                </button>
                            </form>

                            @elseif($order->status == 'shipped')

                            <form method="POST" action="{{ route('profile.order.received',$order) }}">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-outline-success w-100">
                                    Đã nhận hàng
                                </button>
                            </form>

                            @elseif($order->status == 'delivered')

                            <form method="POST" action="{{ route('profile.order.reorder',$order) }}" class="mb-2">
                                @csrf
                                <button class="btn btn-primary w-100">
                                    <i class="fa fa-rotate-right"></i>
                                    Mua lại
                                </button>
                            </form>

                            <a href="{{ route('profile.review.create',$order) }}" class="btn btn-warning w-100">
                                <i class="fa fa-star"></i>
                                Đánh giá
                            </a>

                            @elseif(in_array($order->status, ['cancelled','returned']))

                            <form method="POST" action="{{ route('profile.order.reorder',$order) }}">
                                @csrf
                                <button class="btn btn-primary w-100">
                                    <i class="fa fa-rotate-right"></i>
                                    Mua lại
                                </button>
                            </form>

                            @endif

                        </div>

                    </div>

                </div>
            </div>

            @empty

            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fa fa-box-open fa-4x text-muted mb-3"></i>
                    <h4>Chưa có đơn hàng</h4>
                    <a href="{{ route('products.index') }}" class="btn btn-primary mt-3">Mua sắm ngay</a>
                </div>
            </div>

            @endforelse

            <div class="mt-4">
                {{ $orders->links() }}
            </div>

        </div>

    </div>

</div>
@endsection

@push('styles')
<style>
    .order-card {
        border-radius: 12px;
    }

    .order-tabs .nav-link {
        color: #333;
        padding: 14px 10px;
        border-radius: 0;
        font-size: 14px;
    }

    .order-tabs .nav-link.active {
        background: #fff;
        color: #0d6efd;
        border-bottom: 3px solid #0d6efd;
        font-weight: 600;
    }

    .order-tabs span {
        background: #eef3ff;
        border-radius: 20px;
        padding: 2px 8px;
        font-size: 13px;
        margin-left: 6px;
    }

    .object-fit-cover {
        object-fit: cover;
    }

    .badge {
        font-size: 14px;
        padding: 8px 14px;
    }

    .card {
        border-radius: 12px;
    }

    .btn {
        border-radius: 8px;
    }

    @media(max-width:992px) {
        .order-card .text-end {
            margin-top: 20px;
        }
    }
</style>
@endpush