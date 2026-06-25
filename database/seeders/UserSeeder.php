<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'id'         => 1,
                'name'       => 'admin',
                'email'      => 'test@admin.com',
                'phone'      => null,
                'role'       => 'staff',
                'email_verified_at' => '2026-06-17 03:11:54',
                'password'   => '$2y$12$YS3GJSsYqktGMcesDfV2AOe9klT9JmIyTghgaDeE5Xj1xfW.cJaQy',
                'remember_token' => 'oHXQLF0UfZ',
                'is_active'  => 1,
                'created_at' => '2026-06-17 03:11:55',
                'updated_at' => '2026-06-17 12:03:49',
            ],
            [
                'id'         => 2,
                'name'       => 'Admin',
                'email'      => 'admin@gmail.com',
                'phone'      => null,
                'role'       => 'admin',
                'email_verified_at' => null,
                'password'   => '$2y$12$dIQpTFSAKK8Ko14gUDHWyuWS/zXibMcKqBvKa3HlI7KDQjavdPSe2',
                'remember_token' => 'EG2C9OvuxPY8L7ZK69v9X60MJGpyVUOHq5WFQhZH9EDRTddZ1qWneZODS4f4',
                'is_active'  => 1,
                'created_at' => '2026-06-17 12:02:47',
                'updated_at' => '2026-06-17 12:02:47',
            ],
        ]);
    }
}
