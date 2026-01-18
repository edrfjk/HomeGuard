<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CameraImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_id',
        'image_path',
        'filename',
        'mime_type',
        'file_size',
        'trigger_type',
        'caption',
        'is_favorite',
    ];

    protected $casts = [
        'is_favorite' => 'boolean',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Get image URL
    public function getImageUrl()
    {
        return asset('storage/' . $this->image_path);
    }
}