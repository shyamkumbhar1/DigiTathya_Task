<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScanIngestRequest;
use App\Services\ScanService;

class ScanController extends Controller
{
    public function ingest(ScanIngestRequest $request, ScanService $scanService)
    {
        $result = $scanService->process($request->validated());
        return $this->apiResponse(
            $result['success'] ?? false,
            $result['message'] ?? 'Unexpected response',
            $result['data'] ?? null,
            $result['errors'] ?? null,
            $result['status'] ?? 200
        );
    }
}
