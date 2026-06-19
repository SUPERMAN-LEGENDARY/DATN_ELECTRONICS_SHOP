<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->foreignId('address_id')->constrained()->onDelete('restrict');
            $table->foreignId('voucher_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'returned'])
                ->default('pending');
            $table->enum('payment_method', ['cod', 'momo'])->default('cod');
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid');
            $table->decimal('subtotal', 15, 0);
            $table->decimal('discount_amount', 15, 0)->default(0);
            $table->decimal('total', 15, 0);
            $table->text('note')->nullable();
            $table->timestamp('created_at')->useCurrent(); // chỉ có created_at, không updated_at
            $table->softDeletes(); // deleted_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};