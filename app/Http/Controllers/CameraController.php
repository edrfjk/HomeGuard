<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\CameraImage;
use Illuminate\Http\Request;
use Auth;

class CameraController extends Controller
{
    public function gallery(Device $device)
    {
        if ($device->user_id !== Auth::id()) {
            abort(403);
        }

        $images = $device->cameraImages()
            ->latest()
            ->paginate(12);

        return view('camera.gallery', [
            'device' => $device,
            'images' => $images,
        ]);
    }

    public function view(CameraImage $image)
    {
        if ($image->user_id !== Auth::id()) {
            abort(403);
        }

        return view('camera.view', ['image' => $image]);
    }

    public function toggleFavorite(CameraImage $image)
    {
        if ($image->user_id !== Auth::id()) {
            abort(403);
        }

        $image->update(['is_favorite' => !$image->is_favorite]);

        return back()->with('success', 'Favorite toggled');
    }

    public function delete(CameraImage $image)
    {
        if ($image->user_id !== Auth::id()) {
            abort(403);
        }

        $image->delete();
        return back()->with('success', 'Image deleted');
    }
}