<?php

namespace App\Services;

use App\Models\ScanEvent;
use App\Models\Alert;
use App\Models\DailyStat;
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

            // stats update (duplicate)
            $date = now()->toDateString();

            $stats = DailyStat::firstOrCreate(
                ['date' => $date],
                [
                    'total_scans' => 0,
                    'total_duplicates' => 0,
                    'total_invalid' => 0
                ]
            );

            $stats->increment('total_duplicates');

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

        // Step 6: stats update
        $date = now()->toDateString();

        $stats = DailyStat::firstOrCreate(
            ['date' => $date],
            [
                'total_scans' => 0,
                'total_duplicates' => 0,
                'total_invalid' => 0
            ]
        );

        $stats->increment('total_scans');

        if ($isInvalid) {
            $stats->increment('total_invalid');
        }

        // Step 7: response
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