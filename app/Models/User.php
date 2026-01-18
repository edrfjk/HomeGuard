<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relationships
    public function devices()
    {
        return $this->hasMany(Device::class);
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

    // Count unread critical alerts
    public function unreadCriticalAlerts()
    {
        return $this->alerts()
            ->where('status', 'active')
            ->where('severity', 'critical')
            ->count();
    }
}