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
        Schema::table('banners', function (Blueprint $table) {
            $table->string('creation_method')->default('custom')->after('id');
            $table->string('banner_type')->nullable()->after('creation_method');
            $table->string('template')->nullable()->after('banner_type');
            $table->string('image_mobile')->nullable()->after('image');
            $table->string('btn_color')->nullable()->after('text_color');
            $table->boolean('fx_shadow')->default(false)->after('btn_color');
            $table->boolean('fx_gradient')->default(false)->after('fx_shadow');
            $table->boolean('fx_radius')->default(false)->after('fx_gradient');
            $table->string('text_align')->default('left')->after('fx_radius');
            $table->bigInteger('price')->nullable()->after('price_text');
            $table->bigInteger('compare_price')->nullable()->after('price');
            $table->string('media_type')->default('image')->after('image_mobile');
            $table->string('video')->nullable()->after('media_type');
            $table->timestamp('start_at')->nullable()->after('is_active');
            $table->timestamp('end_at')->nullable()->after('start_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn([
                'creation_method',
                'banner_type',
                'template',
                'image_mobile',
                'btn_color',
                'fx_shadow',
                'fx_gradient',
                'fx_radius',
                'text_align',
                'price',
                'compare_price',
                'media_type',
                'video',
                'start_at',
                'end_at',
            ]);
        });
    }
};
