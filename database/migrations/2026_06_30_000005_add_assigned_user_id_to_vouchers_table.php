<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Thêm khả năng "tặng voucher riêng cho 1 khách hàng" (voucher cá nhân hoá)
     * mà không phá vỡ các voucher dùng chung hiện có (assigned_user_id = NULL).
     */
    public function up(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->foreignId('assigned_user_id')
                ->nullable()
                ->after('code')
                ->constrained('users')
                ->nullOnDelete();

            // Lý do/ghi chú khi admin tặng voucher (vd: "Tặng do sắp đến hạn mua lại")
            $table->string('note', 255)->nullable()->after('assigned_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('assigned_user_id');
            $table->dropColumn('note');
        });
    }
};
