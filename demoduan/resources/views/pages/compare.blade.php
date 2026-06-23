@extends('layouts.app')
@section('title', 'So sánh sản phẩm - ElectronicShop')
@php $showSearch = true; @endphp

@push('styles')
<style>
.compare-page { max-width: 1200px; margin: 0 auto; padding: 16px 16px 48px; }
.compare-page h1 { font-size: 20px; font-weight: 800; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 20px; }

.compare-header { display: grid; grid-template-columns: 200px 1fr 1fr; gap: 0; border: 1px solid #e0e0e0; border-radius: 10px 10px 0 0; overflow: hidden; }
.compare-header .col-label { background: #f8f9fa; padding: 20px 16px; display: flex; align-items: center; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: #888; border-right: 1px solid #e0e0e0; }
.compare-slot { background: #fff; padding: 20px; border-right: 1px solid #e0e0e0; }
.compare-slot:last-child { border-right: none; }

.slot-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 220px; gap: 12px; color: #bbb; }
.slot-empty i { font-size: 36px; }
.slot-empty p { font-size: 13px; }
.btn-pick { padding: 9px 20px; border: 2px dashed #1565C0; border-radius: 8px; background: #EBF3FF; color: #1565C0; font-size: 13px; font-weight: 600; cursor: pointer; transition: all .2s; }
.btn-pick:hover { background: #1565C0; color: #fff; }

.slot-filled { display: flex; flex-direction: column; align-items: center; text-align: center; }
.slot-img { width: 140px; height: 140px; background: #f5f5f5; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #ccc; margin-bottom: 12px; overflow: hidden; }
.slot-img img { width: 100%; height: 100%; object-fit: contain; }
.slot-name { font-size: 14px; font-weight: 700; line-height: 1.4; margin-bottom: 6px; color: #1a1a1a; }
.slot-price { font-size: 20px; font-weight: 800; color: #1565C0; margin-bottom: 8px; }
.slot-oldprice { font-size: 12px; color: #aaa; text-decoration: line-through; margin-bottom: 10px; }
.slot-stars { color: #FFA000; font-size: 13px; margin-bottom: 12px; }
.btn-slot-buy { width: 100%; padding: 10px; background: #1565C0; color: #fff; border: none; border-radius: 6px; font-size: 13px; font-weight: 700; cursor: pointer; margin-bottom: 8px; transition: background .2s; }
.btn-slot-buy:hover { background: #0D47A1; }
.btn-slot-remove { background: none; border: none; color: #aaa; font-size: 12px; cursor: pointer; display: flex; align-items: center; gap: 4px; }
.btn-slot-remove:hover { color: #E53935; }

.compare-body { border: 1px solid #e0e0e0; border-top: none; border-radius: 0 0 10px 10px; overflow: hidden; }
.compare-row { display: grid; grid-template-columns: 200px 1fr 1fr; gap: 0; border-top: 1px solid #f0f0f0; }
.compare-row:nth-child(even) { background: #fafafa; }
.compare-row .row-label { padding: 14px 16px; font-size: 13px; font-weight: 600; color: #555; border-right: 1px solid #e0e0e0; display: flex; align-items: center; background: #f8f9fa; }
.compare-row .row-val { padding: 14px 20px; font-size: 13px; color: #333; border-right: 1px solid #e0e0e0; display: flex; align-items: center; justify-content: center; text-align: center; }
.compare-row .row-val:last-child { border-right: none; }
.compare-row .row-val.better { color: #2E7D32; font-weight: 700; }
.compare-row .row-val.worse { color: #aaa; }
.compare-row .row-val i.fa-check { color: #2E7D32; }
.compare-row .row-val i.fa-times { color: #E53935; }

.compare-section-header { display: grid; grid-template-columns: 200px 1fr 1fr; background: #1565C0; }
.compare-section-header .sh-label { padding: 10px 16px; color: #fff; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: .5px; }
.compare-section-header .sh-empty { padding: 10px 16px; }

.modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 200; align-items: center; justify-content: center; }
.modal-overlay.open { display: flex; }
.modal-box { background: #fff; border-radius: 10px; width: 560px; max-height: 80vh; display: flex; flex-direction: column; overflow: hidden; }
.modal-header { padding: 18px 20px; border-bottom: 1px solid #e0e0e0; display: flex; align-items: center; justify-content: space-between; }
.modal-header h3 { font-size: 16px; font-weight: 700; }
.modal-close { background: none; border: none; font-size: 20px; color: #aaa; cursor: pointer; }
.modal-search { padding: 14px 20px; border-bottom: 1px solid #e0e0e0; }
.modal-search input { width: 100%; border: 1px solid #e0e0e0; border-radius: 6px; padding: 9px 14px; font-size: 14px; outline: none; }
.modal-search input:focus { border-color: #1565C0; }
.modal-list { overflow-y: auto; padding: 12px; }
.modal-product-item { display: flex; gap: 12px; align-items: center; padding: 10px 12px; border-radius: 8px; cursor: pointer; transition: background .15s; }
.modal-product-item:hover { background: #f0f6ff; }
.modal-product-item .mp-img { width: 56px; height: 56px; background: #f5f5f5; border-radius: 6px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; color: #ccc; }
.modal-product-item .mp-name { font-size: 14px; font-weight: 600; margin-bottom: 2px; }
.modal-product-item .mp-price { font-size: 13px; color: #1565C0; font-weight: 700; }
</style>
@endpush

@section('content')
<div class="compare-page">
    <div class="breadcrumb">
        <a href="{{ route('home') }}">Trang chủ</a>
        <span>›</span>
        <span>So sánh sản phẩm</span>
    </div>

    <h1>So sánh sản phẩm</h1>

    <div class="compare-header">
        <div class="col-label">Sản phẩm</div>

        <div class="compare-slot" id="slot-1">
            <div class="slot-filled">
                <div class="slot-img"><i class="fas fa-image fa-2x"></i></div>
                <div class="slot-name">iPhone 15 Pro Max 256GB Titan Tự Nhiên</div>
                <div class="slot-price">28.990.000đ</div>
                <div class="slot-oldprice">30.990.000đ</div>
                <div class="slot-stars">★★★★★ <span style="color:#aaa;font-size:12px">(128)</span></div>
                <button class="btn-slot-buy"><i class="fas fa-shopping-cart"></i> Thêm vào giỏ</button>
                <button class="btn-slot-remove" onclick="clearSlot(1)"><i class="fas fa-times"></i> Xóa</button>
            </div>
        </div>

        <div class="compare-slot" id="slot-2">
            <div class="slot-filled">
                <div class="slot-img"><i class="fas fa-image fa-2x"></i></div>
                <div class="slot-name">Samsung Galaxy S24 Ultra 5G 256GB</div>
                <div class="slot-price">26.490.000đ</div>
                <div class="slot-oldprice">28.990.000đ</div>
                <div class="slot-stars">★★★★★ <span style="color:#aaa;font-size:12px">(95)</span></div>
                <button class="btn-slot-buy"><i class="fas fa-shopping-cart"></i> Thêm vào giỏ</button>
                <button class="btn-slot-remove" onclick="clearSlot(2)"><i class="fas fa-times"></i> Xóa</button>
            </div>
        </div>
    </div>

    <div class="compare-body">

        <div class="compare-section-header">
            <div class="sh-label"><i class="fas fa-mobile-alt" style="margin-right:6px"></i> Thiết kế & Màn hình</div>
            <div class="sh-empty"></div><div class="sh-empty"></div>
        </div>
        @foreach([
            ['Kích thước màn hình', '6.7 inch', '6.8 inch', 1],
            ['Công nghệ màn hình', 'Super Retina XDR OLED', 'Dynamic AMOLED 2X', 0],
            ['Độ phân giải', '2796 × 1290 px', '3088 × 1440 px', 1],
            ['Tần số quét', '120Hz', '120Hz', 0],
            ['Độ sáng tối đa', '2000 nits', '2600 nits', 1],
            ['Chất liệu khung', 'Titan cấp hàng không', 'Armor Aluminum', -1],
            ['Khối lượng', '221g', '232g', -1],
        ] as $row)
        <div class="compare-row">
            <div class="row-label">{{ $row[0] }}</div>
            <div class="row-val {{ $row[3] === -1 ? 'better' : ($row[3] === 1 ? 'worse' : '') }}">{{ $row[1] }}</div>
            <div class="row-val {{ $row[3] === 1 ? 'better' : ($row[3] === -1 ? 'worse' : '') }}">{{ $row[2] }}</div>
        </div>
        @endforeach

        <div class="compare-section-header">
            <div class="sh-label"><i class="fas fa-microchip" style="margin-right:6px"></i> Hiệu năng</div>
            <div class="sh-empty"></div><div class="sh-empty"></div>
        </div>
        @foreach([
            ['Chip xử lý', 'Apple A17 Pro', 'Snapdragon 8 Gen 3', 0],
            ['RAM', '8GB', '12GB', 1],
            ['Bộ nhớ trong', '256GB / 512GB / 1TB', '256GB / 512GB / 1TB', 0],
            ['Thẻ nhớ ngoài', 'Không', 'Không', 0],
        ] as $row)
        <div class="compare-row">
            <div class="row-label">{{ $row[0] }}</div>
            <div class="row-val {{ $row[3] === -1 ? 'better' : ($row[3] === 1 ? 'worse' : '') }}">{{ $row[1] }}</div>
            <div class="row-val {{ $row[3] === 1 ? 'better' : ($row[3] === -1 ? 'worse' : '') }}">{{ $row[2] }}</div>
        </div>
        @endforeach

        <div class="compare-section-header">
            <div class="sh-label"><i class="fas fa-camera" style="margin-right:6px"></i> Camera</div>
            <div class="sh-empty"></div><div class="sh-empty"></div>
        </div>
        @foreach([
            ['Camera chính', '48MP f/1.78', '200MP f/1.7', 1],
            ['Camera góc rộng', '12MP f/2.2', '12MP f/2.2', 0],
            ['Camera tele', '12MP 5x optical zoom', '10MP 10x optical zoom', 1],
            ['Camera trước', '12MP TrueDepth', '12MP f/2.2', 0],
            ['Quay video', '4K 60fps ProRes', '8K 30fps', 1],
        ] as $row)
        <div class="compare-row">
            <div class="row-label">{{ $row[0] }}</div>
            <div class="row-val {{ $row[3] === -1 ? 'better' : ($row[3] === 1 ? 'worse' : '') }}">{{ $row[1] }}</div>
            <div class="row-val {{ $row[3] === 1 ? 'better' : ($row[3] === -1 ? 'worse' : '') }}">{{ $row[2] }}</div>
        </div>
        @endforeach

        <div class="compare-section-header">
            <div class="sh-label"><i class="fas fa-battery-three-quarters" style="margin-right:6px"></i> Pin & Sạc</div>
            <div class="sh-empty"></div><div class="sh-empty"></div>
        </div>
        @foreach([
            ['Dung lượng pin', '4422 mAh', '5000 mAh', 1],
            ['Sạc có dây', '27W', '45W', 1],
            ['Sạc không dây', 'MagSafe 15W', '15W Wireless', 0],
            ['Thời lượng xem video', '29 giờ', '27 giờ', -1],
        ] as $row)
        <div class="compare-row">
            <div class="row-label">{{ $row[0] }}</div>
            <div class="row-val {{ $row[3] === -1 ? 'better' : ($row[3] === 1 ? 'worse' : '') }}">{{ $row[1] }}</div>
            <div class="row-val {{ $row[3] === 1 ? 'better' : ($row[3] === -1 ? 'worse' : '') }}">{{ $row[2] }}</div>
        </div>
        @endforeach

        <div class="compare-section-header">
            <div class="sh-label"><i class="fas fa-star" style="margin-right:6px"></i> Tính năng</div>
            <div class="sh-empty"></div><div class="sh-empty"></div>
        </div>
        @foreach([
            ['Hệ điều hành', 'iOS 17', 'Android 14', 0],
            ['Chống nước', 'IP68', 'IP68', 0],
            ['Nhận diện khuôn mặt', true, true, 0],
            ['Mở khóa vân tay', false, true, 1],
            ['NFC', true, true, 0],
            ['5G', true, true, 0],
            ['Wi-Fi', 'Wi-Fi 6E', 'Wi-Fi 7', 1],
            ['USB', 'USB-C 3.0', 'USB-C 3.2', 1],
        ] as $row)
        <div class="compare-row">
            <div class="row-label">{{ $row[0] }}</div>
            <div class="row-val {{ $row[3] === -1 ? 'better' : ($row[3] === 1 ? 'worse' : '') }}">
                @if(is_bool($row[1]))<i class="fas {{ $row[1] ? 'fa-check' : 'fa-times' }}"></i>@else{{ $row[1] }}@endif
            </div>
            <div class="row-val {{ $row[3] === 1 ? 'better' : ($row[3] === -1 ? 'worse' : '') }}">
                @if(is_bool($row[2]))<i class="fas {{ $row[2] ? 'fa-check' : 'fa-times' }}"></i>@else{{ $row[2] }}@endif
            </div>
        </div>
        @endforeach

    </div>

    <div style="display:flex;gap:20px;margin-top:14px;font-size:12px;color:#888">
        <span style="color:#2E7D32;font-weight:600">● Tốt hơn</span>
        <span style="color:#aaa">● Tương đương</span>
    </div>
</div>

<div class="modal-overlay" id="pickModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Chọn sản phẩm để so sánh</h3>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <div class="modal-search">
            <input type="text" placeholder="Tìm kiếm sản phẩm..." id="modalSearch" oninput="filterModal(this.value)">
        </div>
        <div class="modal-list" id="modalList">
            @foreach([
                ['iPhone 15 Pro Max 256GB','28.990.000đ'],
                ['iPhone 15 Pro 256GB','25.990.000đ'],
                ['iPhone 15 128GB','19.990.000đ'],
                ['Samsung Galaxy S24 Ultra','26.490.000đ'],
                ['Samsung Galaxy S24+','21.990.000đ'],
                ['Samsung Galaxy S24','18.990.000đ'],
                ['Xiaomi 14 Pro','22.990.000đ'],
                ['Xiaomi 14 5G','16.990.000đ'],
                ['OPPO Find X7','19.990.000đ'],
                ['Google Pixel 8 Pro','21.990.000đ'],
            ] as $p)
            <div class="modal-product-item" onclick="selectProduct('{{ $p[0] }}', '{{ $p[1] }}')">
                <div class="mp-img"><i class="fas fa-image"></i></div>
                <div>
                    <div class="mp-name">{{ $p[0] }}</div>
                    <div class="mp-price">{{ $p[1] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let activeSlot = null;

function openModal(slot) {
    activeSlot = slot;
    document.getElementById('pickModal').classList.add('open');
    document.getElementById('modalSearch').value = '';
    filterModal('');
}

function closeModal() {
    document.getElementById('pickModal').classList.remove('open');
}

function clearSlot(slot) {
    const el = document.getElementById('slot-' + slot);
    el.innerHTML = `
        <div class="slot-empty">
            <i class="fas fa-plus-circle"></i>
            <p>Thêm sản phẩm để so sánh</p>
            <button class="btn-pick" onclick="openModal(${slot})">Chọn sản phẩm</button>
        </div>`;
}

function selectProduct(name, price) {
    const el = document.getElementById('slot-' + activeSlot);
    el.innerHTML = `
        <div class="slot-filled">
            <div class="slot-img"><i class="fas fa-image fa-2x"></i></div>
            <div class="slot-name">${name}</div>
            <div class="slot-price">${price}</div>
            <div class="slot-stars">★★★★★ <span style="color:#aaa;font-size:12px">(--)</span></div>
            <button class="btn-slot-buy"><i class="fas fa-shopping-cart"></i> Thêm vào giỏ</button>
            <button class="btn-slot-remove" onclick="clearSlot(${activeSlot})"><i class="fas fa-times"></i> Xóa</button>
        </div>`;
    closeModal();
}

function filterModal(val) {
    document.querySelectorAll('.modal-product-item').forEach(item => {
        const name = item.querySelector('.mp-name').textContent.toLowerCase();
        item.style.display = name.includes(val.toLowerCase()) ? '' : 'none';
    });
}

document.getElementById('pickModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>
@endpush