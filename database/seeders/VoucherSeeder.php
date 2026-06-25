<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('vouchers')->insert([
            [
                'id'               => 1,
                'code'             => 'WELCOME20',
                'discount_percent' => 20,
                'min_order_value'  => 1000000,
                'usage_limit'      => 100,
                'used_count'       => 0,
                'starts_at'        => '2026-05-31 17:00:00',
                'expires_at'       => '2026-12-31 16:59:59',
                'is_active'        => 1,
                'deleted_at'       => null,
            ],
            [
                'id'               => 2,
                'code'             => 'SALE10',
                'discount_percent' => 10,
                'min_order_value'  => 500000,
                'usage_limit'      => 200,
                'used_count'       => 15,
                'starts_at'        => '2026-05-31 17:00:00',
                'expires_at'       => '2026-08-31 16:59:59',
                'is_active'        => 1,
                'deleted_at'       => null,
            ],
            [
                'id'               => 3,
                'code'             => 'SUMMER15',
                'discount_percent' => 15,
                'min_order_value'  => 2000000,
                'usage_limit'      => 50,
                'used_count'       => 3,
                'starts_at'        => '2026-06-14 17:00:00',
                'expires_at'       => '2026-07-15 16:59:59',
                'is_active'        => 1,
                'deleted_at'       => null,
            ],
        ]);
    }
}
