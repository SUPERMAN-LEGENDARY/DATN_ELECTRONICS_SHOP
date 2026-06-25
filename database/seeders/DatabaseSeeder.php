<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            AttributeSeeder::class,
            ProductSeeder::class,
            ProductAttributeSeeder::class,
            NewsCategorySeeder::class,
            NewsSeeder::class,
            VoucherSeeder::class,
            BannerSeeder::class,
            ReviewSeeder::class,
        ]);
    }
}
