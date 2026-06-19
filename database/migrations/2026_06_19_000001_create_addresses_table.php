<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('full_name');
            $table->string('phone', 20);
            $table->string('province');
            $table->string('district');
            $table->string('ward');
            $table->string('street');
            $table->boolean('is_default')->default(false);
            // Không có timestamps (theo model Address)
        });
    }

    public function down()
    {
        Schema::dropIfExists('addresses');
    }
};