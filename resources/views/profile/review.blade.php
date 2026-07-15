@extends('layouts.app')

@section('title','Đánh giá đơn hàng #'.$order->id)

@section('content')
<div class="container py-4">
    <div class="row">

        {{-- Sidebar --}}
        <div class="col-lg-3 mb-4">
            @include('profile.sidebar')
        </div>

        {{-- Content --}}
        <div class="col-lg-9">

            @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">Đánh giá sản phẩm - Đơn #{{ $order->id }}</h4>
                <a href="{{ route('profile.order.show',$order) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Trở lại
                </a>
            </div>

            <form action="{{ route('profile.review.store',$order) }}" method="POST">
                @csrf

                @foreach($order->items as $item)
                @php $alreadyReviewed = in_array($item->product_id, $reviewedProductIds ?? []); @endphp

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <div class="d-flex mb-3">
                            @if($item->product && $item->product->first_image)
                            <img src="{{ $item->product->first_image }}" class="review-image me-3">
                            @else
                            <img src="https://placehold.co/90x90" class="review-image me-3">
                            @endif
                            <div>
                                <h6 class="fw-bold mb-1">{{ $item->product_name }}</h6>
                                <div class="text-muted">Số lượng: {{ $item->quantity }}</div>
                            </div>
                        </div>

                        @if($alreadyReviewed)
                        <div class="alert alert-success mb-0">
                            <i class="fas fa-check-circle me-2"></i>Bạn đã đánh giá sản phẩm này.
                        </div>
                        @else
                        <div class="mb-3">
                            <label class="form-label d-block">Chất lượng sản phẩm</label>
                            <div class="star-rating" data-product="{{ $item->product_id }}">
                                @for($i=5;$i>=1;$i--)
                                <input type="radio" id="star{{ $i }}-{{ $item->product_id }}" name="ratings[{{ $item->product_id }}]" value="{{ $i }}">
                                <label for="star{{ $i }}-{{ $item->product_id }}">★</label>
                                @endfor
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Nhận xét của bạn</label>
                            <textarea name="contents[{{ $item->product_id }}]" class="form-control" rows="3"
                                placeholder="Chia sẻ cảm nhận của bạn về sản phẩm..."></textarea>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach

                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-paper-plane"></i> Gửi đánh giá
                </button>
            </form>

        </div>
    </div>
</div>

<style>
    .review-image {
        width: 70px;
        height: 70px;
        object-fit: contain;
        border: 1px solid #eee;
        border-radius: 10px;
    }

    .card {
        border-radius: 12px;
    }

    .btn {
        border-radius: 8px;
    }

    .star-rating {
        display: inline-flex;
        flex-direction: row-reverse;
        font-size: 30px;
    }

    .star-rating input {
        display: none;
    }

    .star-rating label {
        color: #ddd;
        cursor: pointer;
        padding: 0 2px;
        transition: .2s;
    }

    .star-rating input:checked~label,
    .star-rating label:hover,
    .star-rating label:hover~label {
        color: #ffc107;
    }
</style>
@endsection