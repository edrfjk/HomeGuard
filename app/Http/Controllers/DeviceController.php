<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\SafetyThreshold;
use Illuminate\Http\Request;
use Auth;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = Auth::user()->devices;
        return view('devices.index', ['devices' => $devices]);
    }

    public function create()
    {
        return view('devices.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'device_id' => 'required|unique:devices,device_id',
            'location' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $device = Auth::user()->devices()->create($validated);

        // Create default safety thresholds
        SafetyThreshold::create([
            'device_id' => $device->id,
            'temp_warning' => 32,
            'temp_critical' => 38,
            'humidity_warning' => 65,
            'humidity_critical' => 85,
            'gas_warning' => 400,
            'gas_critical' => 800,
        ]);

        return redirect('/devices')->with('success', 'Device created successfully!');
    }

    public function show(Device $device)
    {
        if ($device->user_id !== Auth::id()) {
            abort(403);
        }
        return view('devices.show', ['device' => $device]);
    }

    public function edit(Device $device)
    {
        if ($device->user_id !== Auth::id()) {
            abort(403);
        }
        return view('devices.edit', ['device' => $device]);
    }

    public function update(Request $request, Device $device)
    {
        if ($device->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $device->update($validated);

        return redirect("/devices/{$device->id}")->with('success', 'Device updated successfully!');
    }

    public function destroy(Device $device)
    {
        if ($device->user_id !== Auth::id()) {
            abort(403);
        }

        $device->delete();
        return redirect('/devices')->with('success', 'Device deleted!');
    }

    public function updateThresholds(Request $request, Device $device)
    {
        if ($device->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'temp_warning' => 'required|numeric',
            'temp_critical' => 'required|numeric',
            'humidity_warning' => 'required|numeric',
            'humidity_critical' => 'required|numeric',
            'gas_warning' => 'required|numeric',
            'gas_critical' => 'required|numeric',
        ]);

        $device->safetyThreshold->update($validated);

        return back()->with('success', 'Safety thresholds updated!');
    }
}