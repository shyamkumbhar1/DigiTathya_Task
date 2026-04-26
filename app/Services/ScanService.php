<?php

namespace App\Services;

use App\Repositories\AlertRepository;
use App\Repositories\ScanRepository;

class ScanService
{
    private const ACTION_TRANSITIONS = [
        'receive' => 'dispatch',
        'dispatch' => 'verify',
        'verify' => 'return',
    ];

    public function __construct(
        private readonly ScanRepository $scanRepository,
        private readonly AlertRepository $alertRepository
    ) {
    }

    public function process(array $payload)
    {
        $scanId = $payload['scan_id'];
        $currentAction = $payload['action'];

        if ($this->scanRepository->existsByScanIdAndAction($scanId, $currentAction)) {
            $this->alertRepository->createDuplicateAlert($scanId);
            return $this->errorResponse(
                'Duplicate scan detected',
                'DUPLICATE_SCAN',
                409,
                $this->buildStatsPayload(0, 1, 0)
            );
        }

        $lastScanEvent = $this->scanRepository->findLastByScanId($scanId);

        if ($this->isInvalidSequence($lastScanEvent?->action, $currentAction)) {
            $this->alertRepository->createInvalidActionAlert($scanId);
            return $this->errorResponse(
                'Invalid action sequence',
                'INVALID_SEQUENCE',
                422,
                $this->buildStatsPayload(0, 0, 1)
            );
        }

        $scan = $this->scanRepository->create($payload);
        return $this->successResponse(
            'Scan stored successfully',
            $scan,
            201,
            $this->buildStatsPayload(1, 0, 0)
        );
    }

    private function isInvalidSequence(?string $lastAction, string $currentAction): bool
    {
        if (!$lastAction) {
            return $currentAction !== 'receive';
        }

        $expectedNextAction = self::ACTION_TRANSITIONS[$lastAction] ?? null;

        return $expectedNextAction !== $currentAction;
    }

    private function buildStatsPayload(int $totalScans, int $totalDuplicates, int $totalInvalid): array
    {
        return [
            'date' => now()->toDateString(),
            'total_scans' => $totalScans,
            'total_duplicates' => $totalDuplicates,
            'total_invalid' => $totalInvalid,
        ];
    }

    private function successResponse(string $message, $data, int $status, array $stats): array
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => null,
            'status' => $status,
            'stats' => $stats,
        ];
    }

    private function errorResponse(string $message, string $code, int $status, array $stats): array
    {
        return [
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => [
                'code' => $code,
                'details' => [],
            ],
            'status' => $status,
            'stats' => $stats,
        ];
    }
}