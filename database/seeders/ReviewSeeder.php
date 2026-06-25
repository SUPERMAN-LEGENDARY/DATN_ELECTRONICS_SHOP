<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('reviews')->insert([
            [
                'id'         => 1,
                'product_id' => 1,
                'user_id'    => 2,
                'rating'     => 5,
                'content'    => 'Sản phẩm rất tốt, camera chụp đẹp, pin trâu, thiết kế sang trọng. Rất hài lòng!',
                'is_visible' => 1,
                'created_at' => '2026-06-25 01:27:29',
            ],
            [
                'id'         => 2,
                'product_id' => 2,
                'user_id'    => 1,
                'rating'     => 5,
                'content'    => 'Galaxy S24 Ultra xứng đáng với số tiền bỏ ra. Bút S Pen rất tiện dụng cho công việc.',
                'is_visible' => 1,
                'created_at' => '2026-06-25 01:27:29',
            ],
            [
                'id'         => 3,
                'product_id' => 3,
                'user_id'    => 2,
                'rating'     => 5,
                'content'    => 'MacBook Air M3 siêu nhẹ, hiệu năng tốt, pin dùng cả ngày không cần sạc.',
                'is_visible' => 1,
                'created_at' => '2026-06-25 01:27:29',
            ],
            [
                'id'         => 4,
                'product_id' => 5,
                'user_id'    => 1,
                'rating'     => 4,
                'content'    => 'Tai nghe chống ồn cực tốt, âm thanh rất hay. Chỉ hơi nặng khi đeo lâu.',
                'is_visible' => 1,
                'created_at' => '2026-06-25 01:27:29',
            ],
        ]);
    }
}
