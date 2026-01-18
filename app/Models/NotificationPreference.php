<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'critical_alerts',
        'warning_alerts',
        'device_status',
        'push_enabled',
        'email_enabled',
    ];

    protected $casts = [
        'critical_alerts' => 'boolean',
        'warning_alerts' => 'boolean',
        'device_status' => 'boolean',
        'push_enabled' => 'boolean',
        'email_enabled' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}