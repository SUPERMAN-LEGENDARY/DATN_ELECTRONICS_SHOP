<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng payments: nhật ký giao dịch thanh toán cho từng đơn hàng
     * (tách riêng khỏi orders.payment_method/payment_status để lưu lại
     * lịch sử giao dịch thật sự với cổng thanh toán — mã giao dịch,
     * số tiền, trạng thái, thời điểm thanh toán).
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->enum('gateway', ['cod', 'momo'])->default('cod');
            $table->string('transaction_id', 100)->nullable();
            $table->decimal('amount', 15, 0);
            $table->enum('status', ['pending', 'success', 'failed', 'refunded'])->default('pending');
            $table->timestamp('paid_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
