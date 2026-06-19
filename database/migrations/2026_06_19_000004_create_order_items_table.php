<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('restrict');
            $table->string('product_name');
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 15, 0);
            $table->decimal('total_price', 15, 0);
            // Không có timestamps (theo model OrderItem)
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_items');
    }
};