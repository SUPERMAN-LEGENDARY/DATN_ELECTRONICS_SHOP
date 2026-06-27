<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Cho phép NULL để giữ lịch sử khi user bị xoá vĩnh viễn
            $table->dropForeign(['user_id']);
            $table->dropForeign(['address_id']);

            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->unsignedBigInteger('address_id')->nullable()->change();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('address_id')->references('id')->on('addresses')->nullOnDelete();
        });

        Schema::table('news', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['address_id']);
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->unsignedBigInteger('address_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('address_id')->references('id')->on('addresses')->restrictOnDelete();
        });

        Schema::table('news', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
        });
    }
};
