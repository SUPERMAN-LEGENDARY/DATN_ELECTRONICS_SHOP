<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_sessions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable() // cho phép khách vãng lai (chưa đăng nhập) chat với AI
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('profile_id')
                ->nullable()
                ->constrained('customer_ai_profiles')
                ->nullOnDelete();

            $table->string('session_token', 100)->unique();

            // Ý định của phiên chat: tìm kiếm, so sánh, mua hàng, hỗ trợ...
            $table->enum('intent_label', ['search', 'compare', 'support', 'buy', 'unknown'])
                ->default('unknown');

            $table->float('sentiment_score')->nullable();

            $table->json('messages')->nullable();          // toàn bộ hội thoại
            $table->json('search_keywords')->nullable();   // từ khóa AI trích xuất
            $table->json('product_interactions')->nullable(); // sản phẩm đã xem/so sánh trong phiên

            $table->unsignedInteger('total_messages')->default(0);
            $table->unsignedInteger('tokens_used')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_sessions');
    }
};
