<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // true = đánh giá bị phát hiện chứa từ không chuẩn mực
            $table->boolean('bad_words_flag')->default(false)->after('is_visible');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('bad_words_flag');
        });
    }
};
