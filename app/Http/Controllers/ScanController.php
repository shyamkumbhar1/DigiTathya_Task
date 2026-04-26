<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScanIngestRequest;
use App\Support\ApiResponse;
use App\Services\ScanService;
use Illuminate\Support\Facades\Log;

class ScanController extends Controller
{
    public function ingest(ScanIngestRequest $request, ScanService $scanService)
    {
        try {
            $result = $scanService->process($request->validated());
            $status = $result['status'] ?? 200;

            if (($result['success'] ?? false) === true) {
                return response()->json(
                    ApiResponse::success(
                        $result['message'] ?? 'Success',
                        $result['data'] ?? null
                    ),
                    $status
                );
            }

            $errorCode = $result['errors']['code'] ?? 'UNKNOWN_ERROR';
            $errorDetails = $result['errors']['details'] ?? null;

            return response()->json(
                ApiResponse::error(
                    $result['message'] ?? 'Request failed',
                    $errorCode,
                    $errorDetails
                ),
                $status
            );
        } catch (\Throwable $e) {
            Log::error('Scan ingest failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(
                ApiResponse::error('Internal server error', 'INTERNAL_ERROR'),
                500
            );
        }
    }
}
