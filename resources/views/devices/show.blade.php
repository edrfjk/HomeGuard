@extends('layouts.app')

@section('title', $device->name . ' — HomeGuard')
@section('page-title', $device->name)
@section('page-subtitle', $device->location)

@section('content')
{{-- Redirect to the main device detail (dashboard/device) --}}
<script>window.location.replace('/device/{{ $device->id }}');</script>
<div style="text-align:center;padding:60px 20px;color:var(--text-muted);">
    <i class="fas fa-spinner fa-spin" style="font-size:32px;display:block;margin-bottom:12px;color:var(--accent);"></i>
    <p style="font-size:14px;">Redirecting to device monitor...</p>
</div>
@endsection
