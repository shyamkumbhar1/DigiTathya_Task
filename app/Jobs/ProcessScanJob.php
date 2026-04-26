<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Repositories\DailyStatRepository;
use Illuminate\Support\Facades\Log;
class ProcessScanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $statsPayload;
    public $tries = 3;
    public $timeout = 30;

    public function __construct(array $statsPayload)
    {
        $this->statsPayload = $statsPayload;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $date = $this->statsPayload['date'] ?? now()->toDateString();
        app(DailyStatRepository::class)->incrementByDate(
            $date,
            $this->statsPayload['total_scans'] ?? 0,
            $this->statsPayload['total_duplicates'] ?? 0,
            $this->statsPayload['total_invalid'] ?? 0
        );
    }

    public function failed(\Throwable $e): void
    {
        Log::error('ProcessScanJob failed', [
            'date' => $this->statsPayload['date'] ?? null,
            'error' => $e->getMessage(),
        ]);
    }
}
