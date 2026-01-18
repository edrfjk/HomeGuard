<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'device_id',
        'location',
        'description',
        'status',
        'ip_address',
        'last_seen',
        'firmware_version',
        'is_active',
    ];

    protected $casts = [
        'last_seen' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sensorReadings()
    {
        return $this->hasMany(SensorReading::class);
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }

    public function cameraImages()
    {
        return $this->hasMany(CameraImage::class);
    }

    public function safetyThreshold()
    {
        return $this->hasOne(SafetyThreshold::class);
    }

    // Get latest sensor reading
    public function latestReading()
    {
        return $this->sensorReadings()->latest('created_at')->first();
    }

    // Get readings for last 24 hours
    public function readingsLast24Hours()
    {
        return $this->sensorReadings()
            ->where('created_at', '>=', now()->subHours(24))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // Get latest camera image
    public function latestCameraImage()
    {
        return $this->cameraImages()->latest('created_at')->first();
    }

    // Get active alerts
    public function activeAlerts()
    {
        return $this->alerts()->where('status', 'active')->get();
    }
}