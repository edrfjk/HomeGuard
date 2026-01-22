<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alert extends Model
{
    protected $fillable = [
        'user_id',
        'device_id',
        'type',
        'severity',
        'status',
        'message',
        'reading_value',
        'threshold_value',
        'sensor_reading_id',
        'camera_image_id',
        'resolved_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function sensorReading(): BelongsTo
    {
        return $this->belongsTo(SensorReading::class);
    }

    public function cameraImage(): BelongsTo
    {
        return $this->belongsTo(CameraImage::class);
    }

    public function getSeverityEmoji()
    {
        return match($this->severity) {
            'critical' => '🚨',
            'warning' => '⚠️',
            default => 'ℹ️',
        };
    }
}