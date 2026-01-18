@extends('layouts.app')

@section('title', 'Edit Device - HomeGuard')
@section('page-title', 'Edit Device')
@section('page-subtitle', 'Update device information')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
        <form method="POST" action="/devices/{{ $device->id }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Device Name -->
            <div>
                <label for="name" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-tag mr-2 text-blue-600"></i>Device Name
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-600 dark:bg-gray-700 dark:text-white transition"
                       value="{{ $device->name }}"
                       required>
            </div>

            <!-- Location -->
            <div>
                <label for="location" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-map-marker-alt mr-2 text-blue-600"></i>Location
                </label>
                <select id="location" 
                        name="location" 
                        class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-600 dark:bg-gray-700 dark:text-white transition">
                    <option value="">Select a location...</option>
                    <option value="Living Room" {{ $device->location === 'Living Room' ? 'selected' : '' }}>Living Room</option>
                    <option value="Bedroom" {{ $device->location === 'Bedroom' ? 'selected' : '' }}>Bedroom</option>
                    <option value="Kitchen" {{ $device->location === 'Kitchen' ? 'selected' : '' }}>Kitchen</option>
                    <option value="Bathroom" {{ $device->location === 'Bathroom' ? 'selected' : '' }}>Bathroom</option>
                    <option value="Hallway" {{ $device->location === 'Hallway' ? 'selected' : '' }}>Hallway</option>
                    <option value="Garage" {{ $device->location === 'Garage' ? 'selected' : '' }}>Garage</option>
                    <option value="Back Door" {{ $device->location === 'Back Door' ? 'selected' : '' }}>Back Door</option>
                    <option value="Front Door" {{ $device->location === 'Front Door' ? 'selected' : '' }}>Front Door</option>
                    <option value="Garden" {{ $device->location === 'Garden' ? 'selected' : '' }}>Garden</option>
                    <option value="Other" {{ $device->location === 'Other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-align-left mr-2 text-blue-600"></i>Description
                </label>
                <textarea id="description" 
                          name="description" 
                          class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-600 dark:bg-gray-700 dark:text-white transition"
                          rows="4">{{ $device->description }}</textarea>
            </div>

            <!-- Device Information (Read-only) -->
            <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Device Information</h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Device ID</p>
                        <p class="font-mono font-bold text-gray-900 dark:text-white mt-1">{{ $device->device_id }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                        <p class="font-bold {{ $device->status === 'online' ? 'text-green-600' : 'text-red-600' }} mt-1">
                            {{ ucfirst($device->status) }}
                        </p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-400">IP Address</p>
                        <p class="font-mono font-bold text-gray-900 dark:text-white mt-1">{{ $device->ip_address ?? 'N/A' }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Last Seen</p>
                        <p class="font-bold text-gray-900 dark:text-white mt-1">{{ $device->last_seen ? $device->last_seen->diffForHumans() : 'Never' }}</p>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 pt-6">
                <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 rounded-lg hover:from-blue-700 hover:to-blue-800 transition shadow-lg font-bold">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
                <a href="/devices" class="flex-1 bg-gray-600 text-white py-3 rounded-lg hover:bg-gray-700 transition font-bold text-center">
                    <i class="fas fa-times-circle mr-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection