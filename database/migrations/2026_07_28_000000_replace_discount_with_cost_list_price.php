<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Bỏ discount_percent, thay bằng mô hình 3 mức giá:
 *  - cost_price  (Giá vốn)   > 0
 *  - list_price  (Giá niêm yết) >= Giá vốn
 *  - price       (Giá bán, GIỮ NGUYÊN tên cột cũ) >= Giá vốn, <= Giá niêm yết
 *
 * Lưu ý: cột `price` đã tồn tại từ trước và tiếp tục được dùng cho lọc/sắp xếp/
 * hiển thị ở phía khách hàng — nay chính thức mang ý nghĩa "Giá bán".
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── products ──
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('cost_price', 15, 0)->default(0)->after('price');
            $table->decimal('list_price', 15, 0)->nullable()->after('cost_price');
        });

        // Dữ liệu cũ: chưa có "giá vốn" nên tạm để 0 (admin cần cập nhật lại sau khi migrate);
        // giá niêm yết tạm lấy bằng giá bán hiện tại để không vi phạm ràng buộc "Giá bán <= Giá niêm yết".
        // Cột để nullable ở DB (tránh phụ thuộc doctrine/dbal khi đổi NOT NULL); bắt buộc nhập ở ProductRequest.
        DB::table('products')->update([
            'list_price' => DB::raw('price'),
        ]);

        if (Schema::hasColumn('products', 'discount_percent')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('discount_percent');
            });
        }

        // ── product_variants ──
        Schema::table('product_variants', function (Blueprint $table) {
            $table->decimal('cost_price', 15, 0)->default(0)->after('price');
            $table->decimal('list_price', 15, 0)->nullable()->after('cost_price');
        });

        DB::table('product_variants')->update([
            'list_price' => DB::raw('price'),
        ]);

        if (Schema::hasColumn('product_variants', 'discount_percent')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->dropColumn('discount_percent');
            });
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedTinyInteger('discount_percent')->default(0)->after('price');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['cost_price', 'list_price']);
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->unsignedTinyInteger('discount_percent')->default(0)->after('price');
        });
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['cost_price', 'list_price']);
        });
    }
};
