<?php

namespace App\Helpers;

use App\Models\CameraImage;
use App\Models\SensorReading;

class StorageHelper
{
    /**
     * Get total storage used by user (in bytes)
     */
    public static function getTotalStorageUsedBytes($userId)
    {
        try {
            $images = CameraImage::where('user_id', $userId)->get();
            return $images->sum('file_size') ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get total storage used (formatted)
     */
    public static function getTotalStorageUsed($userId)
    {
        $bytes = self::getTotalStorageUsedBytes($userId);
        return self::formatBytes($bytes);
    }

    /**
     * Get storage limit in bytes
     */
    public static function getStorageLimit()
    {
        return 5 * 1024 * 1024 * 1024; // 5GB
    }

    /**
     * Get storage limit formatted
     */
    public static function getStorageLimitFormatted()
    {
        return self::formatBytes(self::getStorageLimit());
    }

    /**
     * Get percentage used
     */
    public static function getStoragePercentage($userId)
    {
        $used = self::getTotalStorageUsedBytes($userId);
        $limit = self::getStorageLimit();
        
        if ($limit == 0) return 0;
        
        return min(round(($used / $limit) * 100, 2), 100);
    }

    /**
     * Format bytes to human readable
     */
    public static function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Get count of sensor readings
     */
    public static function getReadingCount($userId)
    {
        try {
            return SensorReading::where('user_id', $userId)->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get count of camera images
     */
    public static function getImageCount($userId)
    {
        try {
            return CameraImage::where('user_id', $userId)->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get oldest reading date
     */
    public static function getOldestReadingDate($userId)
    {
        try {
            $reading = SensorReading::where('user_id', $userId)->oldest('created_at')->first();
            return $reading ? $reading->created_at->format('M d, Y') : 'N/A';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Get oldest image date
     */
    public static function getOldestImageDate($userId)
    {
        try {
            $image = CameraImage::where('user_id', $userId)->oldest('created_at')->first();
            return $image ? $image->created_at->format('M d, Y') : 'N/A';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }
}