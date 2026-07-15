@extends('layouts.app')

@section('title', 'So sánh sản phẩm - ElectronicShop')

@php
$showSearch = true;
@endphp

@section('content')

<section class="compare-page">

    <div class="container">

        <h1 class="compare-title">
            <i class="fas fa-code-compare"></i>
            So sánh sản phẩm
        </h1>

        @if(session('success'))
        <div class="alert-success">
            <i class="fas fa-circle-check"></i>
            {{ session('success') }}
        </div>
        @endif

        @if($products->count() == 0)

        <div class="compare-empty">

            <i class="fas fa-mobile-screen compare-empty-icon"></i>

            <h2>Chưa có sản phẩm để so sánh</h2>

            <p>Hãy chọn tối đa 3 sản phẩm để bắt đầu so sánh.</p>

            <a href="{{ route('products.index') }}" class="btn-primary">
                Xem sản phẩm
            </a>

        </div>

        @else

        {{-- ================= HEADER ================= --}}

        <div class="compare-header">

            @foreach($products as $product)

            <div class="compare-card">

                <div class="compare-image">
                    @if($product->first_image)
                    <img src="{{ $product->first_image }}" alt="{{ $product->name }}">
                    @else
                    <i class="fas fa-image" style="font-size:60px;color:#cbd5e1"></i>
                    @endif
                </div>

                <h3>{{ $product->name }}</h3>

                <div class="price">{{ number_format($product->sale_price) }}đ</div>

                @if($product->discount_percent > 0)
                <div class="old-price">{{ number_format($product->price) }}đ</div>
                @endif

                <div class="card-buttons">

                    <a href="{{ route('products.show', $product->slug) }}" class="buy-btn">
                        <i class="fas fa-cart-shopping"></i>
                        Mua ngay
                    </a>

                    <form action="{{ route('compare.remove', $product) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="remove-btn">
                            <i class="fas fa-trash"></i>
                            Xóa
                        </button>
                    </form>

                </div>

            </div>

            @endforeach

            @if($products->count() < 3)

                <div class="compare-card compare-add">

                <i class="fas fa-plus-circle"></i>

                <h3>Chọn thêm sản phẩm</h3>

                <p>Bạn có thể so sánh tối đa 3 sản phẩm.</p>

                <a href="{{ route('products.index') }}" class="btn-primary">
                    Chọn sản phẩm
                </a>

        </div>

        @endif

    </div>

    {{-- ================= TABLE ================= --}}

    <div class="compare-table">

        <table>

            <thead>
                <tr>
                    <th width="220">Thông số</th>

                    @foreach($products as $product)
                    <th>{{ $product->name }}</th>
                    @endforeach

                    @for($i = $products->count(); $i < 3; $i++)
                        <th>Sản phẩm thứ {{ $i + 1 }}</th>
                        @endfor
                </tr>
            </thead>

            <tbody>

                <tr>
                    <td class="attribute-name">Danh mục</td>
                    @foreach($products as $product)
                    <td>{{ $product->category->name ?? '-' }}</td>
                    @endforeach
                    @for($i = $products->count(); $i < 3; $i++)
                        <td class="empty-cell">—</td>
                        @endfor
                </tr>

                <tr>
                    <td class="attribute-name">Thương hiệu</td>
                    @foreach($products as $product)
                    <td>{{ $product->brand->name ?? '-' }}</td>
                    @endforeach
                    @for($i = $products->count(); $i < 3; $i++)
                        <td class="empty-cell">—</td>
                        @endfor
                </tr>

                @foreach($attributes as $attribute)

                <tr>

                    <td class="attribute-name">{{ $attribute->name }}</td>

                    @foreach($products as $product)

                    @php
                    $value = optional(
                    $product->attributes
                    ->where('attribute_id', $attribute->id)
                    ->first()
                    )->value;
                    @endphp

                    <td>{{ $value ?? '-' }}</td>

                    @endforeach

                    @for($i = $products->count(); $i < 3; $i++)
                        <td class="empty-cell">—</td>
                        @endfor

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

    @endif

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Highlight các thông số khác nhau
            const rows = document.querySelectorAll('.compare-table tbody tr');
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length <= 2) return;
                const values = [];
                for (let i = 1; i < cells.length; i++) {
                    values.push(cells[i].innerText.trim());
                }
                const unique = [...new Set(values)];
                if (unique.length > 1) {
                    for (let i = 1; i < cells.length; i++) {
                        cells[i].classList.add('diff');
                    }
                }
            });

            // Hover hiệu ứng Card
            document.querySelectorAll('.compare-card').forEach(card => {
                card.addEventListener('mouseenter', () => {
                    card.style.boxShadow = '0 20px 45px rgba(37,99,235,.20)';
                });
                card.addEventListener('mouseleave', () => {
                    card.style.boxShadow = '0 10px 35px rgba(0,0,0,.08)';
                });
            });

            // Đồng bộ chiều cao card
            const cards = document.querySelectorAll('.compare-card');
            let maxHeight = 0;
            cards.forEach(card => {
                if (card.offsetHeight > maxHeight) maxHeight = card.offsetHeight;
            });
            cards.forEach(card => {
                card.style.minHeight = maxHeight + 'px';
            });

            // Sticky Header
            const table = document.querySelector('.compare-table table');
            if (table) {
                const thead = table.querySelector('thead');
                window.addEventListener('scroll', () => {
                    const rect = table.getBoundingClientRect();
                    if (rect.top < 0 && rect.bottom > 100) {
                        thead.style.position = 'sticky';
                        thead.style.top = '70px';
                        thead.style.zIndex = '999';
                        thead.style.boxShadow = '0 4px 15px rgba(0,0,0,.12)';
                    } else {
                        thead.style.position = '';
                        thead.style.top = '';
                        thead.style.boxShadow = '';
                    }
                });
            }

            // Animation hiện dần
            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) entry.target.classList.add('show');
                });
            });
            document.querySelectorAll('.compare-card').forEach(card => {
                card.classList.add('fade-card');
                observer.observe(card);
            });

        });
    </script>

    <style>
        .fade-card {
            opacity: 0;
            transform: translateY(40px);
            transition: .6s;
        }

        .fade-card.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>

</section>

@endsection

<style>
    /* ========================== COMPARE PAGE =========================== */
    .compare-page {
        background: #f5f7fb;
        min-height: 100vh;
        padding: 40px 0 70px;
    }

    .compare-title {
        text-align: center;
        font-size: 34px;
        font-weight: 800;
        color: #111827;
        margin-bottom: 35px;
    }

    .compare-title i {
        color: #2563eb;
        margin-right: 10px;
    }

    .alert-success {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 30px;
        font-weight: 600;
    }

    /* ========================== EMPTY =========================== */
    .compare-empty {
        background: #fff;
        border-radius: 20px;
        padding: 80px 30px;
        text-align: center;
        box-shadow: 0 15px 35px rgba(0, 0, 0, .08);
    }

    .compare-empty-icon {
        font-size: 80px;
        color: #2563eb;
        margin-bottom: 20px;
    }

    .compare-empty h2 {
        margin-bottom: 15px;
        font-size: 30px;
    }

    .compare-empty p {
        color: #6b7280;
        margin-bottom: 25px;
    }

    .btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #2563eb;
        color: #fff;
        padding: 12px 28px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 700;
        transition: .3s;
    }

    .btn-primary:hover {
        background: #1d4ed8;
        color: #fff;
    }

    /* ========================== PRODUCT HEADER =========================== */
    .compare-header {
        display: flex;
        justify-content: center;
        gap: 35px;
        flex-wrap: wrap;
        margin-bottom: 40px;
    }

    .compare-card {
        width: 320px;
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 10px 35px rgba(0, 0, 0, .08);
        padding: 25px;
        transition: .35s;
        position: relative;
    }

    .compare-card:hover {
        transform: translateY(-8px);
    }

    .compare-image {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 210px;
    }

    .compare-image img {
        width: 190px;
        height: 190px;
        object-fit: contain;
        transition: .35s;
    }

    .compare-card:hover img {
        transform: scale(1.06);
    }

    .compare-card h3 {
        font-size: 18px;
        font-weight: 700;
        margin: 18px 0;
        min-height: 50px;
        color: #111827;
    }

    .price {
        color: #ef4444;
        font-size: 27px;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .old-price {
        color: #9ca3af;
        text-decoration: line-through;
        margin-bottom: 18px;
    }

    .card-buttons {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    /* ========================== BUTTON =========================== */
    .buy-btn {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        background: #2563eb;
        color: #fff;
        border-radius: 10px;
        padding: 12px;
        text-decoration: none;
        font-weight: 700;
        transition: .3s;
    }

    .buy-btn:hover {
        background: #1d4ed8;
        color: #fff;
    }

    .remove-btn {
        width: 100%;
        border: none;
        background: #ef4444;
        color: #fff;
        padding: 12px;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 700;
        transition: .3s;
    }

    .remove-btn:hover {
        background: #dc2626;
    }

    /* ========================== ADD PRODUCT =========================== */
    .compare-add {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: #6b7280;
        border: 3px dashed #cbd5e1;
        background: #fafafa;
    }

    .compare-add i {
        font-size: 60px;
        color: #2563eb;
        margin-bottom: 20px;
    }

    .compare-add p {
        margin: 15px 0 25px;
        text-align: center;
    }

    /* ========================== TABLE =========================== */
    .compare-table {
        overflow: auto;
    }

    .compare-table table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        background: #fff;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 35px rgba(0, 0, 0, .08);
    }

    .compare-table thead th {
        background: #2563eb;
        color: #fff;
        padding: 18px;
        font-size: 16px;
        white-space: nowrap;
    }

    .compare-table td {
        padding: 16px;
        border-bottom: 1px solid #e5e7eb;
        text-align: center;
    }

    .compare-table tbody tr:nth-child(even) {
        background: #fafafa;
    }

    .compare-table tbody tr:hover {
        background: #eef6ff;
    }

    .attribute-name {
        text-align: left !important;
        font-weight: 700;
        color: #111827;
        background: #f8fafc;
        min-width: 200px;
    }

    .empty-cell {
        color: #cbd5e1;
    }

    /* ========================== DIFFERENT VALUE =========================== */
    .diff {
        background: #dcfce7 !important;
        color: #166534;
        font-weight: 700;
    }

    /* ========================== RESPONSIVE =========================== */
    @media(max-width:992px) {
        .compare-header {
            flex-direction: column;
            align-items: center;
        }

        .compare-card {
            width: 100%;
            max-width: 430px;
        }
    }

    @media(max-width:768px) {
        .compare-title {
            font-size: 28px;
        }

        .compare-image {
            height: 170px;
        }

        .compare-image img {
            width: 150px;
            height: 150px;
        }

        .compare-card h3 {
            font-size: 16px;
        }

        .price {
            font-size: 22px;
        }

        .compare-table th,
        .compare-table td {
            padding: 12px;
            font-size: 14px;
        }

        .attribute-name {
            min-width: 150px;
        }
    }

    @media(max-width:576px) {
        .compare-page {
            padding: 25px 0;
        }

        .compare-card {
            padding: 18px;
        }

        .buy-btn,
        .remove-btn,
        .btn-primary {
            font-size: 14px;
        }
    }
</style>