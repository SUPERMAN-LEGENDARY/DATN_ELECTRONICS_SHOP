<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tạo bảng notifications — lưu thông báo tin tức mới cho user đã subscribe.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->string('type', 50)->default('news');         // mở rộng sau: 'order', 'promo'...
            $table->unsignedBigInteger('reference_id')->nullable(); // news.id
            $table->string('title', 255);
            $table->string('body', 500)->nullable();
            $table->string('url', 500)->nullable();
            $table->string('image', 500)->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'is_read']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
