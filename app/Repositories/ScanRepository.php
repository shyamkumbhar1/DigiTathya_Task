<?php

namespace App\Repositories;

use App\Models\ScanEvent;

class ScanRepository
{
    public function existsByScanIdAndAction(string $scanId, string $action): bool
    {
        return ScanEvent::where('scan_id', $scanId)
            ->where('action', $action)
            ->exists();
    }

    public function findLastByScanId(string $scanId): ?ScanEvent
    {
        return ScanEvent::where('scan_id', $scanId)
            ->latest()
            ->first();
    }

    public function create(array $payload): ScanEvent
    {
        return ScanEvent::create($payload);
    }
}
