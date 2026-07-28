<?php

namespace App\Jobs;

use App\Services\CustomerAiScoringService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Chấm điểm AI cho 1 lô user_id (mặc định 50 user/lô, xem
 * ScoreAllCustomerAiProfilesJob::CHUNK_SIZE). Nhiều job này được gom vào
 * 1 Bus::batch() và có thể chạy SONG SONG trên nhiều queue worker khác nhau
 * -- đây là điểm tăng tốc chính so với trước (1 job chạy tuần tự toàn bộ user).
 */
class ScoreCustomerAiProfilesChunkJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    /**
     * @param array<int> $userIds
     */
    public function __construct(public readonly array $userIds)
    {
    }

    public function handle(CustomerAiScoringService $service): void
    {
        // Nếu cả batch đã bị huỷ (vd admin bấm huỷ giữa chừng) thì bỏ qua,
        // không tính điểm nữa để tránh lãng phí tài nguyên.
        if ($this->batch()?->cancelled()) {
            return;
        }

        $service->scoreUsers($this->userIds);
    }
}