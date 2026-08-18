<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('apply_scope')->default('all')->after('end_date')->comment('all, category, select');
            $table->string('discount_type')->nullable()->after('apply_scope')->comment('percent, amount, fixed');
            $table->decimal('discount_value', 15, 2)->nullable()->after('discount_type');
            $table->decimal('max_discount', 15, 2)->nullable()->after('discount_value');
            $table->unsignedBigInteger('voucher_id')->nullable()->after('max_discount');

            $table->foreign('voucher_id')->references('id')->on('vouchers')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['voucher_id']);
            $table->dropColumn(['apply_scope', 'discount_type', 'discount_value', 'max_discount', 'voucher_id']);
        });
    }
};
