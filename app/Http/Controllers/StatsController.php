<?php

namespace App\Http\Controllers;

use App\Models\Alert;   
use App\Models\ScanEvent;
use App\Support\ApiResponse;


class StatsController extends Controller
{
    public function index()
{
    $totalScans = ScanEvent::count();

    $totalDuplicates = Alert::where('type', 'duplicate')->count();

    $totalInvalid = Alert::where('type', 'invalid_action')->count();

    return response()->json(
        ApiResponse::success(
            'Stats fetched successfully',
            [
                'total_scans' => $totalScans,
                'total_duplicates' => $totalDuplicates,
                'total_invalid' => $totalInvalid,
            ]
        ),
        200
    );
}
}
