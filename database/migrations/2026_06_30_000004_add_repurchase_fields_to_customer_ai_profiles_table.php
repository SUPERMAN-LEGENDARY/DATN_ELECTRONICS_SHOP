<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_ai_profiles', function (Blueprint $table) {
            // Sản phẩm khách đang quan tâm nhất hiện tại (để đề xuất lên trang đầu)
            $table->foreignId('top_interest_product_id')
                ->nullable()
                ->after('last_seen_product_id')
                ->constrained('products')
                ->nullOnDelete();

            // Dự đoán khả năng mua lại
            $table->float('repurchase_probability')->nullable()->after('suggested_products'); // 0 - 1
            $table->timestamp('predicted_repurchase_at')->nullable()->after('repurchase_probability');
            $table->foreignId('repurchase_product_id')
                ->nullable()
                ->after('predicted_repurchase_at')
                ->constrained('products')
                ->nullOnDelete();

            // Gợi ý ưu đãi
            $table->boolean('voucher_recommended')->default(false)->after('repurchase_product_id');
            $table->string('voucher_reason', 100)->nullable()->after('voucher_recommended'); // vd: "sắp hết hàng dự trữ", "khách trung thành", "giỏ hàng bỏ quên"
        });
    }

    public function down(): void
    {
        Schema::table('customer_ai_profiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('top_interest_product_id');
            $table->dropConstrainedForeignId('repurchase_product_id');
            $table->dropColumn([
                'repurchase_probability',
                'predicted_repurchase_at',
                'voucher_recommended',
                'voucher_reason',
            ]);
        });
    }
};
