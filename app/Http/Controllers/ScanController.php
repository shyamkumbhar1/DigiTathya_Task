<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScanIngestRequest;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessScanJob;

class ScanController extends Controller
{
    public function ingest(ScanIngestRequest $request)
    {
        try {
            ProcessScanJob::dispatch($request->validated());
            return response()->json(
                ApiResponse::success('Scan queued for processing'),
                202
            );
        } catch (\Throwable $e) {
            Log::error('Scan ingest failed', [
                'error' => $e->getMessage(),
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
}
