<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('attributes')->insert([
            ['id' => 1,  'name' => 'RAM'],
            ['id' => 2,  'name' => 'Bộ nhớ trong'],
            ['id' => 3,  'name' => 'Màn hình'],
            ['id' => 4,  'name' => 'Pin'],
            ['id' => 5,  'name' => 'CPU'],
            ['id' => 6,  'name' => 'GPU'],
            ['id' => 7,  'name' => 'Hệ điều hành'],
            ['id' => 8,  'name' => 'Màu sắc'],
            ['id' => 9,  'name' => 'Trọng lượng'],
            ['id' => 10, 'name' => 'Kết nối'],
        ]);
    }
}
