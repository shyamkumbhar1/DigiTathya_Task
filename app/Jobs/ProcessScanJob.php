<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\ScanService;
use Illuminate\Support\Facades\Log;
class ProcessScanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $payload;
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     */
    public function handle(ScanService $scanService): void
    {
        $scanService->process($this->payload);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('ProcessScanJob failed', [
            'scan_id' => $this->payload['scan_id'] ?? null,
            'error' => $e->getMessage(),
        ]);
    }
}
