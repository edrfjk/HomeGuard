<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SafetyThreshold extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'temp_warning',
        'temp_critical',
        'humidity_warning',
        'humidity_critical',
        'gas_warning',
        'gas_critical',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}