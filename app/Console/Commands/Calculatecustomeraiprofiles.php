<?php

namespace App\Console\Commands;

use App\Services\CustomerAiScoringService;
use Illuminate\Console\Command;

class CalculateCustomerAiProfiles extends Command
{
    /**
     * php artisan customer:score-ai-profiles
     * Chạy định kỳ (vd: mỗi giờ) qua Task Scheduler, hoặc debug 1 user qua --user=.
     *
     * Toàn bộ logic chấm điểm đã chuyển sang CustomerAiScoringService để dùng
     * chung với ScoreCustomerAiProfilesChunkJob (chạy theo lô 50 user, dùng
     * cho nút "Chấm điểm toàn bộ" ở trang admin).
     */
    protected $signature = 'customer:score-ai-profiles {--user= : Chỉ tính cho 1 user_id cụ thể (debug)}';

    protected $description = 'Tính điểm tiềm năng, sở thích, dự đoán mua lại & gợi ý voucher cho từng khách hàng (customer_ai_profiles)';

    // Kích thước lô khi chạy CLI tuần tự (không liên quan tới CHUNK_SIZE của
    // job, chỉ để progress bar cập nhật mượt và không load hết user vào RAM 1 lần).
    private const CLI_BATCH_SIZE = 200;

    public function handle(CustomerAiScoringService $service): int
    {
        $userIds = $service->eligibleUserIds(
            $this->option('user') ? (int) $this->option('user') : null
        );

        if ($userIds->isEmpty()) {
            $this->info('Không có khách hàng nào có dữ liệu hành vi để tính điểm.');
            return self::SUCCESS;
        }

        $this->info("Đang tính điểm cho {$userIds->count()} khách hàng...");
        $bar = $this->output->createProgressBar($userIds->count());

        foreach ($userIds->chunk(self::CLI_BATCH_SIZE) as $chunk) {
            $service->scoreUsers($chunk->values()->all());
            $bar->advance($chunk->count());
        }

        $bar->finish();
        $this->newLine();
        $this->info('Hoàn tất.');

        return self::SUCCESS;
    }
}