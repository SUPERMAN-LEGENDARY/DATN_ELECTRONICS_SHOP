@extends('layouts.app')

@section('title','Chi tiết đơn hàng #'.$order->id)

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

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">

                <a href="{{ route('profile.order') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Trở lại
                </a>

                <div class="text-end">
                    <h5 class="mb-1">Mã đơn hàng #{{ $order->id }}</h5>

                    @php
                    $statusLabels = [
                    'pending' => ['Chờ xác nhận', 'warning'],
                    'confirmed' => ['Đã xác nhận', 'info'],
                    'processing' => ['Đang xử lý', 'info'],
                    'shipped' => ['Đang giao hàng', 'primary'],
                    'delivered' => ['Hoàn thành', 'success'],
                    'cancelled' => ['Đã hủy', 'danger'],
                    'returned' => ['Đã hoàn trả', 'secondary'],
                    ];
                    [$label, $badge] = $statusLabels[$order->status] ?? [$order->status, 'secondary'];
                    @endphp

                    <span class="badge bg-{{ $badge }}">{{ strtoupper($label) }}</span>
                </div>

            </div>

            {{-- Timeline --}}
            @if(!in_array($order->status, ['cancelled','returned']))
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="timeline">

                        <div class="timeline-item active">
                            <div class="timeline-icon"><i class="fas fa-receipt"></i></div>
                            <div class="timeline-text">Đặt hàng</div>
                        </div>

                        <div class="timeline-line {{ in_array($order->status,['confirmed','processing','shipped','delivered']) ? 'active':'' }}"></div>

                        <div class="timeline-item {{ in_array($order->status,['confirmed','processing','shipped','delivered']) ? 'active':'' }}">
                            <div class="timeline-icon"><i class="fas fa-clipboard-check"></i></div>
                            <div class="timeline-text">Xác nhận</div>
                        </div>

                        <div class="timeline-line {{ in_array($order->status,['shipped','delivered']) ? 'active':'' }}"></div>

                        <div class="timeline-item {{ in_array($order->status,['shipped','delivered']) ? 'active':'' }}">
                            <div class="timeline-icon"><i class="fas fa-truck"></i></div>
                            <div class="timeline-text">Đang giao</div>
                        </div>

                        <div class="timeline-line {{ $order->status=='delivered' ? 'active':'' }}"></div>

                        <div class="timeline-item {{ $order->status=='delivered' ? 'active':'' }}">
                            <div class="timeline-icon"><i class="fas fa-box-open"></i></div>
                            <div class="timeline-text">Hoàn thành</div>
                        </div>

                    </div>
                </div>
            </div>
            @else
            <div class="alert alert-secondary">
                <i class="fas fa-info-circle me-2"></i>
                Đơn hàng này đã {{ $order->status == 'cancelled' ? 'bị hủy' : 'được hoàn trả' }}.
            </div>
            @endif

            {{-- Địa chỉ --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header">
                    <strong><i class="fas fa-map-marker-alt text-danger me-2"></i>Địa chỉ nhận hàng</strong>
                </div>
                <div class="card-body">
                    @if($order->address)
                    <div class="d-flex">
                        <i class="fas fa-map-marker-alt text-danger fs-3 me-3 mt-1"></i>
                        <div>
                            <h5 class="mb-2">
                                {{ $order->address->full_name }}
                                <span class="ms-3">{{ $order->address->phone }}</span>
                            </h5>
                            <div class="text-muted">{{ $order->address->full_address }}</div>
                        </div>
                    </div>
                    @else
                    <p class="text-muted mb-0">Không có thông tin địa chỉ.</p>
                    @endif
                </div>
            </div>

            {{-- Danh sách sản phẩm --}}
            <div class="card shadow-sm border-0">
                <div class="card-header"><strong>Sản phẩm</strong></div>
                <div class="card-body">

                    @foreach($order->items as $item)
                    <div class="row align-items-center product-row">
                        <div class="col-md-2">
                            @if($item->product && $item->product->first_image)
                            <img src="{{ $item->product->first_image }}" class="product-image">
                            @else
                            <img src="https://placehold.co/120x120" class="product-image">
                            @endif
                        </div>

                        <div class="col-md-5">
                            <h6 class="fw-bold">
                                @if($item->product)
                                <a href="{{ route('products.show', $item->product->slug) }}" class="text-dark text-decoration-none">
                                    {{ $item->product_name }}
                                </a>
                                @else
                                {{ $item->product_name }}
                                @endif
                            </h6>
                            <div class="text-muted">Số lượng: {{ $item->quantity }}</div>
                        </div>

                        <div class="col-md-2 text-end">
                            {{ number_format($item->unit_price) }}đ
                        </div>

                        <div class="col-md-3 text-end">
                            <strong class="text-danger">{{ number_format($item->total_price) }}đ</strong>
                        </div>
                    </div>
                    <hr>
                    @endforeach

                    {{-- Thanh toán --}}
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td>Tạm tính</td>
                                    <td class="text-end">{{ number_format($order->subtotal) }}đ</td>
                                </tr>
                                <tr>
                                    <td>Giảm giá</td>
                                    <td class="text-end text-success">-{{ number_format($order->discount_amount) }}đ</td>
                                </tr>
                                <tr class="border-top">
                                    <th>Tổng thanh toán</th>
                                    <th class="text-end text-danger fs-4">{{ number_format($order->total) }}đ</th>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <div class="payment-box">
                                <h6 class="fw-bold mb-3">Thông tin thanh toán</h6>
                                <p>
                                    <strong>Phương thức:</strong>
                                    {{ $order->payment_method == 'momo' ? 'Ví MoMo' : 'Thanh toán khi nhận hàng (COD)' }}
                                </p>
                                <p>
                                    <strong>Trạng thái:</strong>
                                    @if($order->payment_status=='paid')
                                    <span class="badge bg-success">Đã thanh toán</span>
                                    @elseif($order->payment_status=='refunded')
                                    <span class="badge bg-secondary">Đã hoàn tiền</span>
                                    @else
                                    <span class="badge bg-warning">Chưa thanh toán</span>
                                    @endif
                                </p>
                                @if($order->voucher)
                                <p><strong>Mã giảm giá:</strong> {{ $order->voucher->code }}</p>
                                @endif
                                @if($order->note)
                                <p><strong>Ghi chú:</strong> {{ $order->note }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Action --}}
            <div class="text-end mt-4">

                @if(in_array($order->status, ['pending','confirmed','processing']))
                <form action="{{ route('profile.order.cancel',$order) }}" method="POST" class="d-inline">
                    @csrf @method('PATCH')
                    <button class="btn btn-danger" onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">
                        <i class="fas fa-times"></i> Hủy đơn
                    </button>
                </form>
                @endif

                @if($order->status=='shipped')
                <form action="{{ route('profile.order.received',$order) }}" method="POST" class="d-inline">
                    @csrf @method('PATCH')
                    <button class="btn btn-success">
                        <i class="fas fa-check"></i> Đã nhận hàng
                    </button>
                </form>
                @endif

                @if(in_array($order->status, ['delivered','cancelled','returned']))
                <form action="{{ route('profile.order.reorder',$order) }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-primary">
                        <i class="fas fa-rotate-right"></i> Mua lại
                    </button>
                </form>
                @endif

                @if($order->status=='delivered')
                <a href="{{ route('profile.review.create',$order) }}" class="btn btn-warning">
                    <i class="fas fa-star"></i> Đánh giá
                </a>
                @endif

            </div>

        </div>
    </div>
</div>

<style>
    .product-row {
        padding: 18px 0;
    }

    .product-image {
        width: 90px;
        height: 90px;
        object-fit: contain;
        border: 1px solid #eee;
        border-radius: 10px;
    }

    .timeline {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .timeline-item {
        text-align: center;
        width: 120px;
    }

    .timeline-icon {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: #eee;
        color: #999;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        margin: auto;
    }

    .timeline-item.active .timeline-icon {
        background: #28a745;
        color: #fff;
    }

    .timeline-line {
        flex: 1;
        height: 4px;
        background: #ddd;
        margin: 0 10px;
    }

    .timeline-line.active {
        background: #28a745;
    }

    .timeline-text {
        margin-top: 10px;
        font-size: 14px;
        font-weight: 600;
    }

    .payment-box {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
    }

    .card {
        border-radius: 12px;
    }

    .btn {
        border-radius: 8px;
        padding: 10px 20px;
    }

    @media(max-width:768px) {
        .timeline {
            overflow: auto;
        }

        .timeline-item {
            min-width: 110px;
        }

        .product-row {
            text-align: center;
        }

        .product-image {
            margin-bottom: 15px;
        }
    }
</style>
@endsection