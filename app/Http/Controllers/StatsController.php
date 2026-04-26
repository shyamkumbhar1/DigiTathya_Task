<?php

namespace App\Http\Controllers;

use App\Models\DailyStat;
use App\Support\ApiResponse;


class StatsController extends Controller
{
    public function index()
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
}
