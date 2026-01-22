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
        'timezone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
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

    public function loginHistories()
    {
        return $this->hasMany(UserLoginHistory::class);
    }

    public function notificationPreference()
    {
        return $this->hasOne(NotificationPreference::class);
    }

    // Helper methods
    public function unreadCriticalAlerts()
    {
        return $this->alerts()
            ->where('severity', 'critical')
            ->where('status', 'active')
            ->count();
    }

    
}