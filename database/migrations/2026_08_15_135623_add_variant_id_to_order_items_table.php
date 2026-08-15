<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * order_items hiện chưa có cột lưu biến thể sản phẩm (variant_id),
     * trong khi OrderController@store đã tính toán variant_id cho từng
     * dòng đơn hàng (giá / tồn kho theo biến thể). Thiếu cột này khiến
     * variant_id bị Eloquent "âm thầm" bỏ qua khi tạo OrderItem (không có
     * trong $fillable + không tồn tại trong DB), nên dữ liệu biến thể của
     * đơn hàng bị mất ngay sau khi tạo.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');

            $table->foreign('variant_id')
                ->references('id')->on('product_variants')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['variant_id']);
            $table->dropColumn('variant_id');
        });
    }
};