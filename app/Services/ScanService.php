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
                'message' => 'Duplicate scan detected',
                'data' => null,
                'errors' => [
                    'code' => 'DUPLICATE_SCAN',
                    'details' => [],
                ],
                'status' => 409,
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

        $message = 'Scan stored successfully';
        $errors = null;
        $status = 201;

        if ($isInvalid) {
            $message = 'Scan stored with invalid action warning';
            $errors = [
                'code' => 'INVALID_ACTION_SEQUENCE',
                'details' => [],
            ];
            $status = 202;
        }

        return [
            'success' => true,
            'message' => $message,
            'data' => $scan,
            'errors' => $errors,
            'status' => $status,
        ];
    }
}