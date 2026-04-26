<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScanEvent;
use App\Models\Alert;   


class StatsController extends Controller
{
    public function index()
{
    $totalScans = ScanEvent::count();

    $totalDuplicates = Alert::where('type', 'duplicate')->count();

    $totalInvalid = Alert::where('type', 'invalid_action')->count();

    return $this->apiResponse(
        true,
        'Stats fetched successfully',
        [
            'total_scans' => $totalScans,
            'total_duplicates' => $totalDuplicates,
            'total_invalid' => $totalInvalid,
        ],
        null,
        200
    );
}
}
