<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScanEvent;
use App\Models\Alert;

class ScanController extends Controller
{
    public function ingest(Request $request)
    {
        $exists = ScanEvent::where('scan_id', $request->scan_id)->exists();
        if ($exists)
         {
            
            Alert::create([
                'scan_id' => $request->scan_id,
                'type' => 'duplicate',
                'message' => 'Duplicate scan detected'
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Duplicate scan detected'
            ], 200);
        }
        ScanEvent::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Scan stored successfully',
            'data' => $request->all()
            ], 200);

    }
}
