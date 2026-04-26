<?php

namespace App\Repositories;

use App\Models\DailyStat;

class DailyStatRepository
{
    public function incrementByDate(
        string $date,
        int $totalScans,
        int $totalDuplicates,
        int $totalInvalid
    ): void {
        $stats = DailyStat::firstOrCreate(
            ['date' => $date],
            [
                'total_scans' => 0,
                'total_duplicates' => 0,
                'total_invalid' => 0,
            ]
        );

        $stats->increment('total_scans', $totalScans);
        $stats->increment('total_duplicates', $totalDuplicates);
        $stats->increment('total_invalid', $totalInvalid);
    }
}
