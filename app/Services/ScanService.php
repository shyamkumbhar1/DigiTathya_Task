<?php

namespace App\Services;

use App\Models\ScanEvent;
use App\Models\Alert;
use Illuminate\Support\Facades\Log;

class ScanService
{
    public function process(array $payload)
    {
        $scanId = $payload['scan_id'];
        $currentAction = $payload['action'];

        // Step 1: duplicate check
        $isDuplicate = ScanEvent::where('scan_id', $scanId)->exists();

        if ($isDuplicate) {
            Log::warning('Duplicate scan detected', [
                'scan_id' => $scanId,
            ]);

            // alert
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
                'stats' => [
                    'date' => now()->toDateString(),
                    'total_scans' => 0,
                    'total_duplicates' => 1,
                    'total_invalid' => 0,
                ],
            ];
        }

        // Step 2: last scan event
        $lastScanEvent = ScanEvent::where('scan_id', $scanId)
            ->latest()
            ->first();

        $isInvalid = false;

        // Step 3: action validation
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
            if ($lastScanEvent->action == 'verify' && $currentAction !== 'return') {
                $isInvalid = true;
            }
        }

        // Step 4: invalid alert
        if ($isInvalid) {
            Log::warning('Invalid action sequence detected', [
                'scan_id' => $scanId,
                'action' => $currentAction,
                'last_action' => $lastScanEvent?->action,
            ]);

            Alert::create([
                'scan_id' => $scanId,
                'type' => 'invalid_action',
                'message' => 'Invalid action sequence'
            ]);
        }

        // Step 5: save
        $scan = ScanEvent::create($payload);

        // Step 6: response
        $message = 'Scan stored successfully';
        $errors = null;
        $status = 201;
        $statsPayload = [
            'date' => now()->toDateString(),
            'total_scans' => 1,
            'total_duplicates' => 0,
            'total_invalid' => 0,
        ];

        if ($isInvalid) {
            $message = 'Scan stored with invalid action warning';
            $errors = [
                'code' => 'INVALID_ACTION_SEQUENCE',
                'details' => [],
            ];
            $status = 202;
            $statsPayload['total_invalid'] = 1;
        }

        return [
            'success' => true,
            'message' => $message,
            'data' => $scan,
            'errors' => $errors,
            'status' => $status,
            'stats' => $statsPayload,
        ];
    }
}