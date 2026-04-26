<?php

namespace App\Services;

use App\Models\ScanEvent;
use App\Models\Alert;

class ScanService
{
    public function process(array $payload)
    {
        $scanId = $payload['scan_id'];
        $currentAction = $payload['action'];

        // Step 1: duplicate check
        $isDuplicate = ScanEvent::where('scan_id', $scanId)->exists();

        if ($isDuplicate) {
            Alert::create([
                'scan_id' => $scanId,
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
        $lastScanEvent = ScanEvent::where('scan_id', $scanId)
            ->latest()
            ->first();

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
                'scan_id' => $scanId,
                'type' => 'invalid_action',
                'message' => 'Invalid action sequence'
            ]);
        }

        // Step 5: save
        $scan = ScanEvent::create($payload);

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