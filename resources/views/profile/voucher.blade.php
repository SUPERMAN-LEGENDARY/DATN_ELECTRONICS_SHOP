@extends('layouts.app')

@section('title','Kho Voucher')

@section('content')
<div class="container py-4">
    <div class="row">

        {{-- Sidebar --}}
        <div class="col-lg-3 mb-4">
            @include('profile.sidebar')
        </div>

        {{-- Content --}}
        <div class="col-lg-9">

            <div class="voucher-page">

                <div class="voucher-title">
                    <h2>🎁 Kho Voucher</h2>

                    <form action="{{ route('profile.voucher') }}" method="GET" class="voucher-search">
                        <input type="text" name="code" value="{{ $keyword ?? '' }}" placeholder="Nhập mã voucher...">
                        <button>Tìm</button>
                    </form>
                </div>

                @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
                @endif

                @if($vouchers->count())

                <div class="voucher-list">

                    @foreach($vouchers as $voucher)
                    <div class="voucher-card">

                        <div class="voucher-left">
                            <i class="fas fa-ticket-alt"></i>
                            <span>Electronic<br>Shop</span>
                        </div>

                        <div class="voucher-center">
                            <h4>Giảm {{ $voucher->discount_percent }}%</h4>

                            <div class="voucher-code" id="code-{{ $voucher->id }}">
                                {{ $voucher->code }}
                            </div>

                            <p>
                                Đơn tối thiểu:
                                <strong>{{ number_format($voucher->min_order_value) }}đ</strong>
                            </p>

                            <small>
                                @if($voucher->expires_at)
                                HSD: {{ \Carbon\Carbon::parse($voucher->expires_at)->format('d/m/Y') }}
                                @else
                                Không giới hạn
                                @endif

                                @if($voucher->assigned_user_id)
                                <span class="badge-personal">Dành riêng cho bạn</span>
                                @endif
                            </small>
                        </div>

                        <div class="voucher-right">
                            <button type="button" class="btn-use" onclick="copyVoucher('{{ $voucher->code }}', this)">
                                <i class="fas fa-copy"></i> Sao chép mã
                            </button>
                        </div>

                    </div>
                    @endforeach

                </div>

                <div class="mt-4">
                    {{ $vouchers->links() }}
                </div>

                @else

                <div class="voucher-empty">
                    <i class="fas fa-ticket-alt"></i>
                    <h3>Không có voucher phù hợp</h3>
                    <p>Hãy quay lại sau để nhận thêm nhiều ưu đãi hấp dẫn.</p>
                    <a href="{{ route('products.index') }}" class="btn-find">Mua sắm ngay</a>
                </div>

                @endif

            </div>

        </div>
    </div>
</div>

<script>
    function copyVoucher(code, btn) {
        navigator.clipboard.writeText(code).then(function() {
            const original = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i> Đã sao chép';
            setTimeout(function() {
                btn.innerHTML = original;
            }, 1500);
        });
    }
</script>

<style>
    :root {
        --primary: #2F6BFF;
        --primary-hover: #1E5BFF;
        --border: #E5E7EB;
        --text: #1F2937;
        --text-light: #6B7280;
        --bg: #F8FAFC;
    }

    .voucher-page {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 5px 15px rgba(47, 107, 255, .08);
    }

    .voucher-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        margin-bottom: 20px;
    }

    .voucher-title h2 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
        color: var(--text);
    }

    .voucher-search {
        display: flex;
        gap: 10px;
    }

    .voucher-search input {
        width: 220px;
        height: 42px;
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 0 15px;
        outline: none;
        transition: .3s;
    }

    .voucher-search input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(47, 107, 255, .15);
    }

    .voucher-search button {
        height: 42px;
        padding: 0 22px;
        border: none;
        border-radius: 10px;
        background: var(--primary);
        color: #fff;
        font-weight: 600;
        cursor: pointer;
        transition: .3s;
    }

    .voucher-search button:hover {
        background: var(--primary-hover);
    }

    .voucher-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .voucher-card {
        display: flex;
        align-items: center;
        overflow: hidden;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 14px;
        min-height: 145px;
        transition: .3s;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .05);
    }

    .voucher-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 25px rgba(47, 107, 255, .12);
    }

    .voucher-left {
        width: 140px;
        height: 145px;
        background: linear-gradient(135deg, #2F6BFF, #5F8FFF);
        color: #fff;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        position: relative;
        flex-shrink: 0;
    }

    .voucher-left::after {
        content: "";
        position: absolute;
        right: -10px;
        top: 0;
        width: 20px;
        height: 100%;
        background: radial-gradient(circle at left, #fff 9px, transparent 10px);
        background-size: 20px 22px;
    }

    .voucher-left i {
        font-size: 34px;
        margin-bottom: 10px;
    }

    .voucher-left span {
        text-align: center;
        font-size: 16px;
        font-weight: 700;
        line-height: 1.4;
    }

    .voucher-center {
        flex: 1;
        padding: 18px 24px;
    }

    .voucher-center h4 {
        margin: 0 0 10px;
        font-size: 22px;
        color: var(--text);
    }

    .voucher-code {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 8px;
        border: 1px dashed var(--primary);
        background: #EFF6FF;
        color: var(--primary);
        font-weight: 700;
        margin-bottom: 12px;
    }

    .voucher-center p {
        margin: 6px 0;
        font-size: 16px;
        color: var(--text-light);
    }

    .voucher-center strong {
        color: var(--text);
    }

    .voucher-center small {
        color: #9CA3AF;
    }

    .badge-personal {
        display: inline-block;
        margin-left: 8px;
        padding: 2px 8px;
        border-radius: 20px;
        background: #FFF4E5;
        color: #B45309;
        font-size: 11px;
        font-weight: 700;
    }

    .voucher-right {
        width: 150px;
        flex-shrink: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        border-left: 1px dashed var(--border);
    }

    .btn-use {
        background: var(--primary);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 10px 18px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: .3s;
    }

    .btn-use:hover {
        background: var(--primary-hover);
    }

    .voucher-empty {
        text-align: center;
        padding: 70px 20px;
    }

    .voucher-empty i {
        font-size: 70px;
        color: #B6CBFF;
        margin-bottom: 15px;
    }

    .voucher-empty h3 {
        font-size: 24px;
        margin-bottom: 10px;
        color: var(--text);
    }

    .voucher-empty p {
        color: var(--text-light);
        margin-bottom: 25px;
    }

    .btn-find {
        display: inline-block;
        padding: 12px 28px;
        border-radius: 10px;
        background: var(--primary);
        color: #fff;
        text-decoration: none;
        transition: .3s;
    }

    .btn-find:hover {
        background: var(--primary-hover);
        color: #fff;
    }

    .alert-success {
        margin-bottom: 15px;
        padding: 12px 16px;
        border-radius: 10px;
        border: 1px solid #C8DBFF;
        background: #EAF2FF;
        color: var(--primary);
    }

    @media(max-width:992px) {
        .voucher-title {
            flex-direction: column;
            align-items: flex-start;
        }

        .voucher-search {
            width: 100%;
        }

        .voucher-search input {
            flex: 1;
            width: 100%;
        }

        .voucher-left {
            width: 120px;
            height: 130px;
        }

        .voucher-left i {
            font-size: 28px;
        }

        .voucher-left span {
            font-size: 14px;
        }

        .voucher-center {
            padding: 16px;
        }

        .voucher-center h4 {
            font-size: 20px;
        }

        .voucher-right {
            width: 130px;
        }
    }

    @media(max-width:768px) {
        .voucher-page {
            padding: 15px;
        }

        .voucher-title {
            flex-direction: column;
            align-items: stretch;
        }

        .voucher-title h2 {
            font-size: 24px;
        }

        .voucher-search {
            flex-direction: column;
        }

        .voucher-search input,
        .voucher-search button {
            width: 100%;
        }

        .voucher-card {
            flex-direction: column;
        }

        .voucher-left {
            width: 100%;
            height: 110px;
        }

        .voucher-left::after {
            display: none;
        }

        .voucher-center {
            width: 100%;
        }

        .voucher-center h4 {
            font-size: 20px;
        }

        .voucher-right {
            width: 100%;
            padding: 15px;
            border-left: none;
            border-top: 1px dashed var(--border);
        }

        .btn-use {
            width: 100%;
        }
    }
</style>
@endsection