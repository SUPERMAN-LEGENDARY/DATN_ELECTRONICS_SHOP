<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_views', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable() // khách vãng lai vẫn ghi nhận được qua session_token
                ->constrained('users')
                ->nullOnDelete();

            $table->string('session_token', 100)->nullable(); // map với ai_sessions.session_token nếu là khách chưa đăng nhập

            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade');

            // Loại hành vi: xem trang, thêm giỏ hàng, so sánh, click từ gợi ý AI...
            $table->enum('event_type', ['view', 'add_to_cart', 'compare', 'click_suggestion', 'wishlist'])
                ->default('view');

            $table->unsignedInteger('duration_seconds')->nullable(); // thời gian xem trang sản phẩm
            $table->string('source', 50)->nullable(); // vd: chatbot, search, homepage, category

            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'product_id']);
            $table->index(['product_id', 'event_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_views');
    }
};
