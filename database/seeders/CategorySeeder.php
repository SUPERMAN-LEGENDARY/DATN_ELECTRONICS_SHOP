<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->insert([
            ['id' => 1,  'name' => 'Điện thoại',    'slug' => 'dien-thoai',    'type' => 'category', 'logo' => null, 'is_active' => 1, 'deleted_at' => null],
            ['id' => 2,  'name' => 'Laptop',         'slug' => 'laptop',        'type' => 'category', 'logo' => null, 'is_active' => 1, 'deleted_at' => null],
            ['id' => 3,  'name' => 'Tai nghe',       'slug' => 'tai-nghe',      'type' => 'category', 'logo' => null, 'is_active' => 1, 'deleted_at' => null],
            ['id' => 4,  'name' => 'Máy tính bảng',  'slug' => 'may-tinh-bang', 'type' => 'category', 'logo' => null, 'is_active' => 1, 'deleted_at' => null],
            ['id' => 5,  'name' => 'Phụ kiện',       'slug' => 'phu-kien',      'type' => 'category', 'logo' => null, 'is_active' => 1, 'deleted_at' => null],
            ['id' => 6,  'name' => 'Apple',          'slug' => 'apple',         'type' => 'brand',    'logo' => null, 'is_active' => 1, 'deleted_at' => null],
            ['id' => 7,  'name' => 'Samsung',        'slug' => 'samsung',       'type' => 'brand',    'logo' => null, 'is_active' => 1, 'deleted_at' => null],
            ['id' => 8,  'name' => 'Xiaomi',         'slug' => 'xiaomi',        'type' => 'brand',    'logo' => null, 'is_active' => 1, 'deleted_at' => null],
            ['id' => 9,  'name' => 'Sony',           'slug' => 'sony',          'type' => 'brand',    'logo' => null, 'is_active' => 1, 'deleted_at' => null],
            ['id' => 10, 'name' => 'Dell',           'slug' => 'dell',          'type' => 'brand',    'logo' => null, 'is_active' => 1, 'deleted_at' => null],
        ]);
    }
}
