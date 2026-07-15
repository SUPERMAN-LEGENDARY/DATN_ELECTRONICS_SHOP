<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Thêm cột is_variant để phân loại thuộc tính:
     * - is_variant = true  → "Thuộc tính chính": dùng để tạo lựa chọn mua hàng
     *   (hiện thành nút bấm ở trang chi tiết sản phẩm, ví dụ: Màu sắc, Dung lượng).
     * - is_variant = false → "Thuộc tính phụ": chỉ hiển thị trong bảng
     *   "Thông số kỹ thuật", không tạo nút chọn.
     *
     * Mặc định là true để không phá vỡ các sản phẩm/biến thể đã tạo trước đó;
     * admin có thể vào "Quản lý thuộc tính" để chuyển từng thuộc tính
     * sang "phụ" nếu không muốn nó hiện thành nút chọn.
     */
    public function up(): void
    {
        Schema::table('attributes', function (Blueprint $table) {
            $table->boolean('is_variant')->default(true)->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('attributes', function (Blueprint $table) {
            $table->dropColumn('is_variant');
        });
    }
};
