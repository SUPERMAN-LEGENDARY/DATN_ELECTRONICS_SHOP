<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');                 // Tên sự kiện: Giáng Sinh, Tết Nguyên Đán...
            $table->string('tag')->nullable();        // Nhãn nhỏ phía trên tiêu đề, vd: ƯU ĐÃI ĐẶC BIỆT
            $table->string('offer_text')->nullable(); // Ưu đãi nổi bật, vd: Giảm đến 50%
            $table->text('description')->nullable();  // Mô tả chi tiết
            $table->string('image')->nullable();      // Ảnh sự kiện
            $table->string('button_text')->nullable();
            $table->string('button_link')->nullable();
            $table->string('bg_color')->nullable();   // Màu nền cho thẻ sự kiện
            $table->string('text_color')->nullable();
            $table->date('start_date')->nullable();   // Ngày bắt đầu sự kiện
            $table->date('end_date')->nullable();      // Ngày kết thúc sự kiện
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
