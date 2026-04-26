<?php

namespace App\Repositories;

use App\Models\Alert;

class AlertRepository
{
    public function createDuplicateAlert(string $scanId): Alert
    {
        return Alert::create([
            'scan_id' => $scanId,
            'type' => 'duplicate',
            'message' => 'Duplicate scan detected',
        ]);
    }

    public function createInvalidActionAlert(string $scanId): Alert
    {
        return Alert::create([
            'scan_id' => $scanId,
            'type' => 'invalid_action',
            'message' => 'Invalid action sequence',
        ]);
    }
}
