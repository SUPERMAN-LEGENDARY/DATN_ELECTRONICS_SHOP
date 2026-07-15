@extends('layouts.app')

@section('title','Đánh giá của tôi')

@section('content')
<div class="container py-4">
    <div class="row">

        {{-- Sidebar --}}
        <div class="col-lg-3 mb-4">
            @include('profile.sidebar')
        </div>

        {{-- Content --}}
        <div class="col-lg-9">

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h4 class="mb-0">Đánh giá của tôi</h4>
                </div>

                <div class="card-body">

                    @forelse($reviews as $review)
                    <div class="border rounded p-3 mb-3 review-item">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div class="d-flex">
                                @if($review->product && $review->product->first_image)
                                <img src="{{ $review->product->first_image }}" class="review-thumb me-3">
                                @endif
                                <div>
                                    <h5 class="mb-1">
                                        @if($review->product)
                                        <a href="{{ route('products.show', $review->product->slug) }}" class="text-dark text-decoration-none">
                                            {{ $review->product->name }}
                                        </a>
                                        @else
                                        Sản phẩm đã xóa
                                        @endif
                                    </h5>

                                    <div class="text-warning mb-2">
                                        @for($i=1;$i<=5;$i++)
                                            @if($i <=$review->rating) ★ @else ☆ @endif
                                            @endfor
                                    </div>

                                    <p class="mb-1">{{ $review->content ?: 'Không có nhận xét.' }}</p>

                                    <small class="text-muted">
                                        {{ optional($review->created_at)->format('d/m/Y H:i') }}
                                    </small>
                                </div>
                            </div>

                            @if(!$review->is_visible)
                            <span class="badge bg-secondary">Đang chờ kiểm duyệt</span>
                            @endif
                        </div>

                        @if($review->admin_reply)
                        <div class="admin-reply mt-3">
                            <strong><i class="fas fa-store me-1"></i> Phản hồi từ ElectronicShop:</strong>
                            <p class="mb-0">{{ $review->admin_reply }}</p>
                        </div>
                        @endif
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <i class="fas fa-star fa-4x text-secondary mb-3"></i>
                        <h5>Bạn chưa có đánh giá nào</h5>
                        <a href="{{ route('products.index') }}" class="btn btn-primary mt-3">Khám phá sản phẩm</a>
                    </div>
                    @endforelse

                    <div class="mt-3">
                        {{ $reviews->links() }}
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 12px;
    }

    .review-item {
        border-radius: 10px !important;
    }

    .review-thumb {
        width: 70px;
        height: 70px;
        object-fit: contain;
        border: 1px solid #eee;
        border-radius: 10px;
    }

    .admin-reply {
        background: #eef4ff;
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 14px;
        color: #333;
    }
</style>
@endsection