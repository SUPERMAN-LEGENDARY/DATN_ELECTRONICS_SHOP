<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('news')->insert([
            [
                'id'               => 1,
                'user_id'          => 2,
                'news_category_id' => 1,
                'title'            => 'Hot khuyến mãi',
                'slug'             => 'hot-khuyen-mai',
                'content'          => 'Săn VOUCHER khuyến mãi giảm ngay 20%',
                'thumbnail'        => 'news/DnAevcwEXvGvI3tqA3EFM7Fs9MabCpqeHF9ANFwN.jpg',
                'views'            => 0,
                'is_active'        => 1,
                'published_at'     => '2026-06-17 21:07:00',
                'deleted_at'       => null,
            ],
            [
                'id'               => 2,
                'user_id'          => 2,
                'news_category_id' => 2,
                'title'            => 'iPhone 15 Pro Max chính thức ra mắt tại Việt Nam',
                'slug'             => 'iphone-15-pro-max-chinh-thuc-ra-mat-tai-viet-nam',
                'content'          => 'Apple chính thức ra mắt iPhone 15 Pro Max tại Việt Nam với nhiều cải tiến vượt trội về camera và hiệu năng chip A17 Pro.',
                'thumbnail'        => null,
                'views'            => 120,
                'is_active'        => 1,
                'published_at'     => '2026-06-20 01:00:00',
                'deleted_at'       => null,
            ],
        ]);
    }
}
