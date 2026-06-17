<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// NOTE: Database đã được tạo sẵn qua file electronic_shop_final.sql
// Migration này chỉ để tham khảo cấu trúc trong Laravel context.
// Chạy: php artisan migrate (nếu chưa import SQL)
//   hoặc: mysql -u root -p < electronic_shop_final.sql (nếu dùng SQL trực tiếp)

return new class extends Migration
{
    public function up(): void
    {
        // categories (dùng chung cho category và brand)
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->enum('type', ['category', 'brand'])->default('category');
            $table->string('logo', 255)->nullable();
            $table->boolean('is_active')->default(true);
        });

        // attributes
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
        });

        // products
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('slug', 220)->unique();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->foreignId('brand_id')->references('id')->on('categories')->restrictOnDelete();
            $table->text('description')->nullable();
            $table->json('images')->nullable();
            $table->decimal('price', 15, 0)->default(0);
            $table->unsignedTinyInteger('discount_percent')->default(0);
            $table->unsignedInteger('stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
        });

        // product_attributes
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attribute_id')->constrained()->restrictOnDelete();
            $table->string('value', 255);
        });

        // reviews
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->text('content')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('product_attributes');
        Schema::dropIfExists('products');
        Schema::dropIfExists('attributes');
        Schema::dropIfExists('categories');
    }
};
