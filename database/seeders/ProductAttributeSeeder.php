<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductAttributeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('product_attributes')->insert([
            // Samsung Galaxy S24 Ultra (product_id = 2)
            ['id' => 8,  'product_id' => 2, 'attribute_id' => 1, 'value' => '12GB'],
            ['id' => 9,  'product_id' => 2, 'attribute_id' => 2, 'value' => '512GB'],
            ['id' => 10, 'product_id' => 2, 'attribute_id' => 3, 'value' => '6.8 inch Dynamic AMOLED 2X'],
            ['id' => 11, 'product_id' => 2, 'attribute_id' => 4, 'value' => '5000 mAh'],
            ['id' => 12, 'product_id' => 2, 'attribute_id' => 5, 'value' => 'Snapdragon 8 Gen 3'],
            ['id' => 13, 'product_id' => 2, 'attribute_id' => 7, 'value' => 'Android 14'],
            ['id' => 14, 'product_id' => 2, 'attribute_id' => 8, 'value' => 'Đen Titan'],

            // MacBook Air M3 (product_id = 3)
            ['id' => 15, 'product_id' => 3, 'attribute_id' => 1, 'value' => '16GB'],
            ['id' => 16, 'product_id' => 3, 'attribute_id' => 2, 'value' => '512GB SSD'],
            ['id' => 17, 'product_id' => 3, 'attribute_id' => 3, 'value' => '13.6 inch Liquid Retina'],
            ['id' => 18, 'product_id' => 3, 'attribute_id' => 4, 'value' => '52.6Wh - 18 giờ'],
            ['id' => 19, 'product_id' => 3, 'attribute_id' => 5, 'value' => 'Apple M3'],
            ['id' => 20, 'product_id' => 3, 'attribute_id' => 7, 'value' => 'macOS Sonoma'],
            ['id' => 21, 'product_id' => 3, 'attribute_id' => 9, 'value' => '1.24 kg'],

            // Dell XPS 15 (product_id = 4)
            ['id' => 22, 'product_id' => 4, 'attribute_id' => 1, 'value' => '16GB DDR5'],
            ['id' => 23, 'product_id' => 4, 'attribute_id' => 2, 'value' => '512GB NVMe SSD'],
            ['id' => 24, 'product_id' => 4, 'attribute_id' => 3, 'value' => '15.6 inch OLED 3.5K'],
            ['id' => 25, 'product_id' => 4, 'attribute_id' => 4, 'value' => '86Wh'],
            ['id' => 26, 'product_id' => 4, 'attribute_id' => 5, 'value' => 'Intel Core i7-13700H'],
            ['id' => 27, 'product_id' => 4, 'attribute_id' => 6, 'value' => 'NVIDIA RTX 4060'],
            ['id' => 28, 'product_id' => 4, 'attribute_id' => 7, 'value' => 'Windows 11 Home'],

            // Sony WH-1000XM5 (product_id = 5)
            ['id' => 29, 'product_id' => 5, 'attribute_id' => 4,  'value' => '30 giờ'],
            ['id' => 30, 'product_id' => 5, 'attribute_id' => 8,  'value' => 'Đen'],
            ['id' => 31, 'product_id' => 5, 'attribute_id' => 9,  'value' => '250g'],
            ['id' => 32, 'product_id' => 5, 'attribute_id' => 10, 'value' => 'Bluetooth 5.2, 3.5mm'],

            // iPhone 15 Pro Max (product_id = 1)
            ['id' => 33, 'product_id' => 1, 'attribute_id' => 2, 'value' => '256GB'],
            ['id' => 34, 'product_id' => 1, 'attribute_id' => 5, 'value' => 'Apple A17 Pro'],
            ['id' => 35, 'product_id' => 1, 'attribute_id' => 7, 'value' => 'iOS 17'],
            ['id' => 36, 'product_id' => 1, 'attribute_id' => 3, 'value' => '6.7 inch Super Retina XDR'],
            ['id' => 37, 'product_id' => 1, 'attribute_id' => 8, 'value' => 'Titan Tự nhiên'],
            ['id' => 38, 'product_id' => 1, 'attribute_id' => 4, 'value' => '4422 mAh'],
            ['id' => 39, 'product_id' => 1, 'attribute_id' => 1, 'value' => '8GB'],
        ]);
    }
}
