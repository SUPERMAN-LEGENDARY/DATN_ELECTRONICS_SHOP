<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('banners')->insert([
            [
                'id'          => 1,
                'layout'      => 'image',
                'label'       => null,
                'title'       => null,
                'description' => null,
                'price_text'  => null,
                'button_text' => null,
                'button_link' => 'mua ngay',
                'image'       => '/storage/banners/MzxfxollKSfdq3BWoEntBZsrHsF5YkGCD8YEmZRg.jpg',
                'bg_color'    => null,
                'text_color'  => null,
                'sort_order'  => 1,
                'is_active'   => 1,
                'created_at'  => '2026-06-25 03:22:35',
                'updated_at'  => '2026-06-25 03:22:35',
                'deleted_at'  => null,
            ],
            [
                'id'          => 2,
                'layout'      => 'split',
                'label'       => 'ưu đãi ngay hôm nay',
                'title'       => 'hot',
                'description' => 'khuyến mãi nhập mã giảm 10%',
                'price_text'  => 'Cho đơn từ 1k',
                'button_text' => 'XEM NGAY',
                'button_link' => 'kkk',
                'image'       => '/storage/banners/I7gk7uYWqF1hVyOO7QZaPju1uVa923YcL23XqG8U.png',
                'bg_color'    => '#c7ddff',
                'text_color'  => '#000000',
                'sort_order'  => 2,
                'is_active'   => 1,
                'created_at'  => '2026-06-25 03:24:53',
                'updated_at'  => '2026-06-25 03:24:53',
                'deleted_at'  => null,
            ],
        ]);
    }
}
