<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Snapshot thông tin liên hệ tại thời điểm đặt hàng.
            // Bắt buộc phải có để khách vãng lai (không có tài khoản, user_id = null)
            // vẫn nhận được email xác nhận đơn hàng.
            $table->string('customer_name')->nullable()->after('user_id');
            $table->string('customer_phone', 20)->nullable()->after('customer_name');
            $table->string('customer_email')->nullable()->after('customer_phone');

            $table->index('customer_phone');
            $table->index('customer_email');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['customer_phone']);
            $table->dropIndex(['customer_email']);
            $table->dropColumn(['customer_name', 'customer_phone', 'customer_email']);
        });
    }
};