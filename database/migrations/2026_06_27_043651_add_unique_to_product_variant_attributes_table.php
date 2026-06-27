<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variant_attributes', function (Blueprint $table) {
            $table->unique(['variant_id', 'attribute_id'], 'uniq_variant_attribute');
        });
    }

    public function down(): void
    {
        Schema::table('product_variant_attributes', function (Blueprint $table) {
            $table->dropUnique('uniq_variant_attribute');
        });
    }
};