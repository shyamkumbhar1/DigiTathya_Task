<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScanController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/scan/ingest', [ScanController::class, 'ingest'])->middleware('throttle:60,1');;
Route::get('/stats', [ScanController::class, 'stats']);