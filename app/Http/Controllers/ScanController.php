<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScanEvent;

class ScanController extends Controller
{
    public function ingest(Request $request)
{
    ScanEvent::create($request->all());

    return response()->json([
        'success' => true,
        'message' => 'Scan stored successfully',
        'data' => $request->all()
        ], 200);

}
}
