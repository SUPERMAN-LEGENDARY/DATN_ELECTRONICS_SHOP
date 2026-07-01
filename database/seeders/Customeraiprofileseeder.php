<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CustomerAiProfile;
use Illuminate\Database\Seeder;

class CustomerAiProfileSeeder extends Seeder
{
    public function run(): void
    {
        // Chỉ tạo profile cho customer (không tạo cho admin/staff)
        $users = User::where('role', 'customer')->get();

        foreach ($users as $user) {
            CustomerAiProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'lead_label'  => 'cold',
                    'lead_stage'  => 'visitor',
                    'total_score' => 0,
                    'score_view'  => 0,
                    'score_chat'  => 0,
                    'score_order' => 0,
                ]
            );
        }

        $this->command->info("Đã tạo customer_ai_profiles cho {$users->count()} user.");
    }
}