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
        // 1. Thêm cột thumbnail vào bảng products
        Schema::table('products', function (Blueprint $table) {
            $table->string('thumbnail', 255)
                  ->nullable()
                  ->after('description')
                  ->comment('Đường dẫn ảnh đại diện sản phẩm');
        });

        // 2. Tạo bảng product_variants
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->cascadeOnDelete();
            $table->string('label', 200)
                  ->comment('VD: 8GB / 128GB / Đen');
            $table->decimal('price', 15, 0)->default(0);
            $table->unsignedTinyInteger('discount_percent')->default(0);
            $table->unsignedInteger('stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('created_at')->useCurrent();
        });

        // 3. Tạo bảng product_variant_attributes
        Schema::create('product_variant_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')
                  ->constrained('product_variants')
                  ->cascadeOnDelete();
            $table->foreignId('attribute_id')
                  ->constrained('attributes')
                  ->cascadeOnDelete();
            $table->string('value', 255);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variant_attributes');
        Schema::dropIfExists('product_variants');

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('thumbnail');
        });
    }
};
