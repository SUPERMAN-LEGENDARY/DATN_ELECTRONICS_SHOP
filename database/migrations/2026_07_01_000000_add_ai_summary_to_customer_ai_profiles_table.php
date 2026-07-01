<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CHỈ THÊM CỘT MỚI (additive) — không rename/drop cột nào của customer_ai_profiles
 * để tránh lặp lại sự cố mất dữ liệu như lần đổi stock -> quantity trước đây.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_ai_profiles', function (Blueprint $table) {
            $table->text('ai_summary')->nullable()->after('voucher_reason');
            $table->timestamp('ai_summary_generated_at')->nullable()->after('ai_summary');
        });
    }

    public function down(): void
    {
        Schema::table('customer_ai_profiles', function (Blueprint $table) {
            $table->dropColumn(['ai_summary', 'ai_summary_generated_at']);
        });
    }
};
