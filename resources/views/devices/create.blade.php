@extends('layouts.app')

@section('title', 'Add Device - HomeGuard')
@section('page-title', 'Add New Device')
@section('page-subtitle', 'Connect your ESP32-CAM device')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 animate-fadeIn">
        <!-- Instructions -->
        <div class="mb-8 p-6 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-lg">
            <h3 class="font-bold text-blue-900 dark:text-blue-200 mb-2">
                <i class="fas fa-info-circle mr-2"></i>How to add your ESP32 device:
            </h3>
            <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-1 ml-6 list-disc">
                <li>Give your device a meaningful name (e.g., "Living Room Camera")</li>
                <li>Find your ESP32's MAC address (printed on device or check router)</li>
                <li>Enter the location where the device will be placed</li>
                <li>Your device will appear in the dashboard after successful connection</li>
            </ul>
        </div>

        <!-- Form -->
        <form method="POST" action="/devices" class="space-y-6">
            @csrf

            <!-- Device Name -->
            <div>
                <label for="name" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-tag mr-2 text-blue-600"></i>Device Name *
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-600 dark:bg-gray-700 dark:text-white transition"
                       placeholder="e.g., Living Room Camera"
                       value="{{ old('name') }}"
                       required>
                @error('name')
                    <p class="text-red-600 text-sm mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Device ID (MAC Address) -->
            <div>
                <label for="device_id" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-microchip mr-2 text-blue-600"></i>Device ID (MAC Address) *
                </label>
                <input type="text" 
                       id="device_id" 
                       name="device_id" 
                       class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-600 dark:bg-gray-700 dark:text-white transition font-mono"
                       placeholder="e.g., ESP32-A4C138D4FBC8"
                       value="{{ old('device_id') }}"
                       required>
                @error('device_id')
                    <p class="text-red-600 text-sm mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Location -->
            <div>
                <label for="location" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-map-marker-alt mr-2 text-blue-600"></i>Location *
                </label>
                <select id="location" 
                        name="location" 
                        class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-600 dark:bg-gray-700 dark:text-white transition"
                        required>
                    <option value="">Select a location...</option>
                    <option value="Living Room">Living Room</option>
                    <option value="Bedroom">Bedroom</option>
                    <option value="Kitchen">Kitchen</option>
                    <option value="Bathroom">Bathroom</option>
                    <option value="Hallway">Hallway</option>
                    <option value="Garage">Garage</option>
                    <option value="Back Door">Back Door</option>
                    <option value="Front Door">Front Door</option>
                    <option value="Garden">Garden</option>
                    <option value="Other">Other</option>
                </select>
                @error('location')
                    <p class="text-red-600 text-sm mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-align-left mr-2 text-blue-600"></i>Description
                </label>
                <textarea id="description" 
                          name="description" 
                          class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-600 dark:bg-gray-700 dark:text-white transition"
                          placeholder="Optional: Add any notes about this device..."
                          rows="4">{{ old('description') }}</textarea>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 pt-6">
                <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 rounded-lg hover:from-blue-700 hover:to-blue-800 transition shadow-lg font-bold">
                    <i class="fas fa-check-circle mr-2"></i>Add Device
                </button>
                <a href="/devices" class="flex-1 bg-gray-600 text-white py-3 rounded-lg hover:bg-gray-700 transition font-bold text-center">
                    <i class="fas fa-times-circle mr-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fadeIn {
        animation: fadeIn 0.6s ease-out forwards;
        opacity: 0;
    }
</style>
@endsection