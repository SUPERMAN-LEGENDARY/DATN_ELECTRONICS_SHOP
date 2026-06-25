<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('products')->insert([
            [
                'id'               => 1,
                'name'             => 'iPhone 15 Pro Max 256GB',
                'slug'             => 'iphone-15-pro-max-256gb',
                'category_id'      => 1,
                'brand_id'         => 6,
                'description'      => 'iPhone 15 Pro Max với chip A17 Pro, màn hình Super Retina XDR 6.7 inch, camera 48MP.',
                'images'           => json_encode(['/storage/products/0dNqJ72VA0vS2D1sdDfblnKh7P4Xas2LMz5i9Plr.png']),
                'price'            => 34990000,
                'discount_percent' => 5,
                'stock'            => 50,
                'is_active'        => 1,
                'created_at'       => '2026-06-25 01:27:29',
                'deleted_at'       => null,
            ],
            [
                'id'               => 2,
                'name'             => 'Samsung Galaxy S24 Ultra 512GB',
                'slug'             => 'samsung-galaxy-s24-ultra-512gb',
                'category_id'      => 1,
                'brand_id'         => 7,
                'description'      => 'Galaxy S24 Ultra trang bị bút S Pen, chip Snapdragon 8 Gen 3, màn hình 6.8 inch Dynamic AMOLED.',
                'images'           => json_encode(['products/s24ultra.jpg']),
                'price'            => 31990000,
                'discount_percent' => 10,
                'stock'            => 30,
                'is_active'        => 1,
                'created_at'       => '2026-06-25 01:27:29',
                'deleted_at'       => null,
            ],
            [
                'id'               => 3,
                'name'             => 'MacBook Air M3 13 inch 16GB/512GB',
                'slug'             => 'macbook-air-m3-13-inch-16gb-512gb',
                'category_id'      => 2,
                'brand_id'         => 6,
                'description'      => 'MacBook Air M3 siêu mỏng nhẹ, hiệu năng vượt trội, pin lên đến 18 giờ.',
                'images'           => json_encode(['products/macbook-air-m3.jpg']),
                'price'            => 32990000,
                'discount_percent' => 0,
                'stock'            => 20,
                'is_active'        => 1,
                'created_at'       => '2026-06-25 01:27:29',
                'deleted_at'       => null,
            ],
            [
                'id'               => 4,
                'name'             => 'Dell XPS 15 9530 Intel Core i7',
                'slug'             => 'dell-xps-15-9530-core-i7',
                'category_id'      => 2,
                'brand_id'         => 10,
                'description'      => 'Dell XPS 15 màn hình OLED 3.5K, Intel Core i7-13700H, RAM 16GB, SSD 512GB.',
                'images'           => json_encode(['products/dell-xps15.jpg']),
                'price'            => 42990000,
                'discount_percent' => 8,
                'stock'            => 15,
                'is_active'        => 1,
                'created_at'       => '2026-06-25 01:27:29',
                'deleted_at'       => null,
            ],
            [
                'id'               => 5,
                'name'             => 'Sony WH-1000XM5 Wireless',
                'slug'             => 'sony-wh-1000xm5-wireless',
                'category_id'      => 3,
                'brand_id'         => 9,
                'description'      => 'Tai nghe chống ồn hàng đầu Sony WH-1000XM5, pin 30 giờ, kết nối Bluetooth 5.2.',
                'images'           => json_encode(['products/sony-wh1000xm5.jpg']),
                'price'            => 8490000,
                'discount_percent' => 15,
                'stock'            => 40,
                'is_active'        => 1,
                'created_at'       => '2026-06-25 01:27:29',
                'deleted_at'       => null,
            ],
        ]);
    }
}
