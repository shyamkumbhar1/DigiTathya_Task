<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScanIngestRequest;
use App\Models\DailyStat;
use App\Support\ApiResponse;
use App\Services\ScanService;
use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessScanJob;

class ScanController extends Controller
{
    public function stats()
    {
        $stats = DailyStat::latest()->first();

        return response()->json(
            ApiResponse::success(
                'Stats fetched successfully',
                $stats
            ),
            200
        );
    }

    public function ingest(ScanIngestRequest $request, ScanService $scanService)
    {
        try {
            $result = $scanService->process($request->validated());
            ProcessScanJob::dispatch($result['stats'] ?? ['date' => now()->toDateString()]);
            return $this->respondFromServiceResult($result);
        } catch (\Throwable $e) {
            Log::error('Scan ingest failed', [
                'error' => $e->getMessage(),
                'scan_id' => $request->input('scan_id'),
            ]);
            $details = config('app.debug')
                ? ['message' => $e->getMessage()]
                : null;

            return response()->json(
                ApiResponse::error('Internal server error', 'INTERNAL_ERROR', $details),
                500
            );
        }
    }

    private function respondFromServiceResult(array $result)
    {
        if (($result['success'] ?? false) === true) {
            return response()->json(
                ApiResponse::success(
                    $result['message'] ?? 'Success',
                    $result['data'] ?? null
                ),
                $result['status'] ?? 200
            );
        }

        return response()->json(
            ApiResponse::error(
                $result['message'] ?? 'Request failed',
                $result['errors']['code'] ?? 'UNKNOWN_ERROR',
                $result['errors']['details'] ?? null
            ),
            $result['status'] ?? 400
        );
    }
}
