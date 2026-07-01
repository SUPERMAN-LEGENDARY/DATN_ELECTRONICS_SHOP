<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_ai_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Phân loại tiềm năng khách hàng
            $table->enum('lead_label', ['cold', 'warm', 'hot'])->default('cold');
            $table->string('lead_stage', 50)->nullable(); // vd: visitor, viewer, comparer, cart, buyer

            // Điểm số hành vi do AI tính toán
            $table->float('total_score')->default(0);
            $table->float('score_view')->default(0);
            $table->float('score_chat')->default(0);
            $table->float('score_order')->default(0);

            // Sở thích / hành vi
            $table->json('interest_categories')->nullable();
            $table->json('price_range')->nullable();

            $table->foreignId('last_seen_product_id')
                ->nullable()
                ->constrained('products')
                ->nullOnDelete();

            $table->json('suggested_products')->nullable();
            $table->json('keywords_history')->nullable();

            $table->timestamp('scored_at')->nullable();
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_ai_profiles');
    }
};
