<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();

            // Kiểu banner: 'split' (chữ + ảnh như mặc định) hoặc 'image' (chỉ ảnh full, có thể bấm vào)
            $table->string('layout')->default('split');

            // Toàn bộ nội dung chữ đều không bắt buộc -> banner có thể là quảng cáo,
            // sự kiện, thông báo... không nhất thiết phải gắn với 1 sản phẩm cụ thể.
            $table->string('label')->nullable();        // Nhãn nhỏ phía trên (VD: "ƯU ĐÃI HÔM NAY")
            $table->string('title')->nullable();         // Tiêu đề chính
            $table->text('description')->nullable();     // Mô tả ngắn
            $table->string('price_text')->nullable();    // Dòng giá / nhấn mạnh (tuỳ chọn)
            $table->string('button_text')->nullable();   // Chữ trên nút (để trống = không hiện nút)
            $table->string('button_link')->nullable();   // Có thể là link sản phẩm, link ngoài, trang khuyến mãi...

            $table->string('image')->nullable();         // Ảnh banner
            $table->string('bg_color')->nullable();      // Màu nền khu vực chữ (mã hex, tuỳ chọn)
            $table->string('text_color')->nullable();    // Màu chữ (mã hex, tuỳ chọn)

            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
