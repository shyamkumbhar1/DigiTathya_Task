<?php

namespace App\Services;

use App\Models\ScanEvent;
use App\Models\Alert;

class ScanService
{
    public function process($request)
    {
        // Step 1: duplicate check
        $isDuplicate = ScanEvent::where('scan_id', $request->scan_id)->exists();

        if ($isDuplicate) {
            Alert::create([
                'scan_id' => $request->scan_id,
                'type' => 'duplicate',
                'message' => 'Duplicate scan detected'
            ]);

            return [
                'success' => false,
                'message' => 'Duplicate scan detected'
            ];
        }

        // Step 2: last scan event
        $lastScanEvent = ScanEvent::where('scan_id', $request->scan_id)
            ->latest()
            ->first();

        $currentAction = $request->action;
        $isInvalid = false;

        // Step 3: action validation : receive -> dispatch -> verify
        if (!$lastScanEvent) {
            if ($currentAction !== 'receive') {
                $isInvalid = true;
            }
        } else {
            if ($lastScanEvent->action == 'receive' && $currentAction !== 'dispatch') {
                $isInvalid = true;
            }

            if ($lastScanEvent->action == 'dispatch' && $currentAction !== 'verify') {
                $isInvalid = true;
            }
        }

        // Step 4: invalid alert
        if ($isInvalid) {
            Alert::create([
                'scan_id' => $request->scan_id,
                'type' => 'invalid_action',
                'message' => 'Invalid action sequence'
            ]);
        }

        // Step 5: save
        $scan = ScanEvent::create($request->all());

        return [
            'success' => true,
            'message' => 'Scan stored successfully',
            'data' => $scan
        ];
    }
}