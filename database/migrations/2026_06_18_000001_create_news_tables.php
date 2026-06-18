<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->boolean('is_active')->default(true);
        });

        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('news_category_id')->constrained('news_categories')->restrictOnDelete();
            $table->string('title', 255);
            $table->string('slug', 270)->unique();
            $table->longText('content');
            $table->string('thumbnail', 255)->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('published_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news');
        Schema::dropIfExists('news_categories');
    }
};
