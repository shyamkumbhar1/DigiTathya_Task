<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScanEvent;
use App\Models\Alert;
use App\Services\ScanService;

class ScanController extends Controller
{
    public function ingest(Request $request , ScanService $scanService)
    {
        $result = $scanService->process($request);

        return response()->json($result, 200);
    }
}
