<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_id',
        'temperature',
        'humidity',
        'gas_level',
        'gas_status',
        'signal_strength',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function alert()
    {
        return $this->hasOne(Alert::class);
    }

    // Get status based on thresholds
    public function checkStatus()
    {
        $threshold = $this->device->safetyThreshold;
        
        if ($this->temperature >= $threshold->temp_critical || 
            $this->humidity >= $threshold->humidity_critical || 
            $this->gas_level >= $threshold->gas_critical) {
            return 'critical';
        }
        
        if ($this->temperature >= $threshold->temp_warning || 
            $this->humidity >= $threshold->humidity_warning || 
            $this->gas_level >= $threshold->gas_warning) {
            return 'warning';
        }
        
        return 'safe';
    }
}