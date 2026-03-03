@extends('layouts.app')

@section('title', 'Edit ' . $device->name . ' — HomeGuard')
@section('page-title', 'Edit Device')
@section('page-subtitle', $device->device_id)

@section('content')
<div style="max-width:620px;">
    <a href="/devices" class="btn btn-ghost btn-sm" style="margin-bottom:20px;display:inline-flex;">
        <i class="fas fa-arrow-left"></i> Back
    </a>

    <div class="card card-p fade-up">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:22px;">
            <div style="width:42px;height:42px;border-radius:10px;background:rgba(34,211,238,.1);display:flex;align-items:center;justify-content:center;font-size:18px;color:var(--accent);">
                <i class="fas fa-pen"></i>
            </div>
            <div>
                <div style="font-size:16px;font-weight:700;color:#fff;">{{ $device->name }}</div>
                <div style="font-size:11px;color:var(--text-muted);font-family:'Space Mono',monospace;">{{ $device->device_id }}</div>
            </div>
            <span class="status-pill {{ $device->status }}" style="margin-left:auto;">
                <span class="status-dot {{ $device->status }}"></span>
                {{ ucfirst($device->status) }}
            </span>
        </div>

        <form method="POST" action="/devices/{{ $device->id }}">
            @csrf @method('PUT')

            <div class="form-group">
                <label class="form-label">Device Name</label>
                <input type="text" name="name" class="form-control" value="{{ $device->name }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Location</label>
                <select name="location" class="form-control">
                    @foreach(['Living Room','Bedroom','Kitchen','Bathroom','Hallway','Garage','Back Door','Front Door','Garden','Office','Basement','Attic','Other'] as $loc)
                        <option value="{{ $loc }}" {{ $device->location === $loc ? 'selected' : '' }}>{{ $loc }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Description <span style="color:var(--text-dim);">(optional)</span></label>
                <textarea name="description" class="form-control" rows="3">{{ $device->description }}</textarea>
            </div>

            {{-- Read-only info --}}
            <div style="border-top:1px solid var(--border);padding-top:18px;margin:18px 0;">
                <div style="font-size:11px;color:var(--text-muted);font-family:'Space Mono',monospace;margin-bottom:12px;">DEVICE INFO (read-only)</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div class="sensor-box">
                        <div class="s-label">Device ID</div>
                        <div style="font-size:11px;font-family:'Space Mono',monospace;color:#fff;margin-top:4px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $device->device_id }}</div>
                    </div>
                    <div class="sensor-box">
                        <div class="s-label">IP Address</div>
                        <div style="font-size:12px;font-family:'Space Mono',monospace;color:#fff;margin-top:4px;">{{ $device->ip_address ?? 'N/A' }}</div>
                    </div>
                    <div class="sensor-box">
                        <div class="s-label">Last Seen</div>
                        <div style="font-size:12px;color:#fff;margin-top:4px;">{{ $device->last_seen ? $device->last_seen->diffForHumans() : 'Never' }}</div>
                    </div>
                    <div class="sensor-box">
                        <div class="s-label">Firmware</div>
                        <div style="font-size:12px;font-family:'Space Mono',monospace;color:#fff;margin-top:4px;">{{ $device->firmware_version ?? 'Unknown' }}</div>
                    </div>
                </div>
            </div>

            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <a href="/devices" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>

    {{-- Danger zone --}}
    <div class="card card-p fade-up" style="animation-delay:.1s;margin-top:14px;border-color:rgba(248,113,113,.15);">
        <div style="font-size:11px;color:var(--danger);font-family:'Space Mono',monospace;margin-bottom:12px;"><i class="fas fa-triangle-exclamation" style="margin-right:6px;"></i>DANGER ZONE</div>
        <p style="font-size:13px;color:var(--text-muted);margin-bottom:14px;">Deleting this device will remove all sensor readings, alerts, and camera images permanently.</p>
        <form method="POST" action="/devices/{{ $device->id }}" onsubmit="return confirm('Delete {{ $device->name }} and all its data? This cannot be undone.')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">
                <i class="fas fa-trash"></i> Delete Device
            </button>
        </form>
    </div>
</div>
@endsection
