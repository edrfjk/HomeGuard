<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\CameraImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Auth;

class CameraController extends Controller
{
    /**
     * Show the camera gallery for a device.
     * All images are motion-triggered (captured on motion detection).
     */
    public function gallery(Device $device)
    {
        if ($device->user_id !== Auth::id()) {
            abort(403);
        }

        $filter = request('filter', 'all'); // all | favorites

        $query = $device->cameraImages()
            ->with('alert')
            ->latest();

        if ($filter === 'favorites') {
            $query->where('is_favorite', true);
        }

        $images     = $query->paginate(24)->withQueryString();
        $totalCount = $device->cameraImages()->count();
        $favCount   = $device->cameraImages()->where('is_favorite', true)->count();

        return view('camera.gallery', compact(
            'device', 'images', 'filter', 'totalCount', 'favCount'
        ));
    }

    /**
     * View a single image with its linked alert.
     */
    public function view(CameraImage $image)
    {
        if ($image->user_id !== Auth::id()) {
            abort(403);
        }

        $image->load('alert', 'device');

        return view('camera.view', compact('image'));
    }

    /**
     * Toggle favorite — always returns JSON (AJAX-safe).
     */
    public function toggleFavorite(CameraImage $image)
    {
        if ($image->user_id !== Auth::id()) {
            abort(403);
        }

        $image->update(['is_favorite' => !$image->is_favorite]);

        return response()->json([
            'success'     => true,
            'is_favorite' => (bool) $image->is_favorite,
            'image_id'    => $image->id,
        ]);
    }

    /**
     * Delete an image + its file on disk.
     */
    public function delete(CameraImage $image)
    {
        if ($image->user_id !== Auth::id()) {
            abort(403);
        }

        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }

        $deviceId = $image->device_id;
        $image->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()
            ->route('camera.gallery', $deviceId)
            ->with('success', 'Image deleted.');
    }
}
