<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->unsignedTinyInteger('discount_percent');
            $table->decimal('min_order_value', 15, 0)->default(0);
            $table->unsignedInteger('usage_limit');
            $table->unsignedInteger('used_count')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vouchers');
    }
};