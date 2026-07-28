<?php

namespace App\Jobs;

use App\Services\CustomerAiScoringService;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Throwable;

/**
 * Dispatcher cho nút "Chấm điểm toàn bộ" (LeadController::recalculateAll).
 *
 * Trước đây: 1 job chạy tuần tự cho TOÀN BỘ user (loop + Artisan::call trong
 * 1 process). Bây giờ: chia user thành các lô CHUNK_SIZE (50), mỗi lô là 1
 * ScoreCustomerAiProfilesChunkJob riêng, gom vào 1 Bus::batch() -- nhiều lô
 * được nhiều queue worker xử lý SONG SONG.
 *
 * Lưu ý quan trọng: tốc độ thực tế phụ thuộc số queue worker đang chạy đồng
 * thời. Cần chạy nhiều tiến trình worker cho queue "ai-scoring", ví dụ:
 *   - Supervisor: numprocs=4 (hoặc cao hơn tuỳ CPU/DB chịu tải được)
 *   - hoặc Laravel Horizon với nhiều process cho queue này.
 * Nếu chỉ chạy 1 `php artisan queue:work` duy nhất thì các lô vẫn xử lý
 * tuần tự (chỉ đỡ tốn RAM/query hơn bản cũ, chưa có song song thật).
 *
 * Cần bảng job_batches (Laravel batching): nếu project chưa có, chạy
 *   php artisan queue:batches-table && php artisan migrate
 */
class ScoreAllCustomerAiProfilesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public const CACHE_KEY_RUNNING    = 'ai_profiles:recalculate_all:running';
    public const CACHE_KEY_LAST_DONE  = 'ai_profiles:recalculate_all:last_done_at';
    public const CACHE_KEY_LAST_ERROR = 'ai_profiles:recalculate_all:last_error';

    // Kích thước 1 lô -- 50 user/job: đủ nhỏ để nhiều lô chạy song song trên
    // nhiều worker, đủ lớn để không tốn overhead dispatch cho từng job bé.
    public const CHUNK_SIZE = 50;

    // Job này chỉ để chia lô rồi dispatch batch, không tính điểm trực tiếp -> nhanh
    public int $timeout = 60;

    public function handle(CustomerAiScoringService $service): void
    {
        $userIds = $service->eligibleUserIds();

        if ($userIds->isEmpty()) {
            $this->markDone();
            return;
        }

        $jobs = $userIds
            ->chunk(self::CHUNK_SIZE)
            ->map(fn ($chunk) => new ScoreCustomerAiProfilesChunkJob($chunk->values()->all()))
            ->all();

        Bus::batch($jobs)
            ->onQueue('ai-scoring')
            ->name('Chấm điểm AI toàn bộ khách hàng')
            ->finally(function (Batch $batch) {
                Cache::forget(self::CACHE_KEY_RUNNING);
                Cache::put(self::CACHE_KEY_LAST_DONE, now()->toDateTimeString(), now()->addDay());

                if ($batch->hasFailures()) {
                    Cache::put(
                        self::CACHE_KEY_LAST_ERROR,
                        "{$batch->failedJobs}/{$batch->totalJobs} lô chấm điểm bị lỗi, xem log để biết chi tiết.",
                        now()->addDay()
                    );
                } else {
                    Cache::forget(self::CACHE_KEY_LAST_ERROR);
                }
            })
            ->catch(function (Batch $batch, Throwable $e) {
                report($e);
            })
            ->dispatch();
    }

    private function markDone(): void
    {
        Cache::forget(self::CACHE_KEY_RUNNING);
        Cache::put(self::CACHE_KEY_LAST_DONE, now()->toDateTimeString(), now()->addDay());
    }
}