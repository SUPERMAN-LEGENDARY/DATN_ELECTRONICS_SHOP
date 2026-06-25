<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewsCategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('news_categories')->insert([
            ['id' => 1, 'name' => 'Khuyến mãi',           'slug' => 'khuyen-mai',          'is_active' => 1, 'deleted_at' => null],
            ['id' => 2, 'name' => 'Tin tức công nghệ',    'slug' => 'tin-tuc-cong-nghe',   'is_active' => 1, 'deleted_at' => null],
            ['id' => 3, 'name' => 'Đánh giá sản phẩm',   'slug' => 'danh-gia-san-pham',   'is_active' => 1, 'deleted_at' => null],
        ]);
    }
}
