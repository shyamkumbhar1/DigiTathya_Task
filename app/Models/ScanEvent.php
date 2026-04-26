<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScanEvent extends Model
{
    use HasFactory;
    protected $fillable = [
        'scan_id',
        'session_id',
        'operator_id',
        'partner_id',
        'device_id',
        'action',
        'gps_lat',
        'gps_lng',
        'device_timestamp'
    ];
}
