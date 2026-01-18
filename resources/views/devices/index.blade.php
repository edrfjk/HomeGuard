@extends('layouts.app')

@section('title', 'My Devices - HomeGuard')
@section('page-title', 'My Devices')
@section('page-subtitle', 'Manage all your ESP32-CAM devices')

@section('content')
<div class="space-y-8">
    <!-- Header with Add Button -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Total Devices: {{ $devices->count() }}</h2>
        </div>
        <a href="/devices/create" class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-3 rounded-lg hover:from-blue-700 hover:to-blue-800 transition shadow-lg font-bold">
            <i class="fas fa-plus mr-2"></i>Add New Device
        </a>
    </div>

    @if($devices->isEmpty())
        <!-- Empty State -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-12 text-center">
            <i class="fas fa-inbox text-gray-400 dark:text-gray-600 text-6xl mb-4 block"></i>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">No Devices Yet</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">You haven't added any ESP32-CAM devices yet.</p>
            <a href="/devices/create" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-bold">
                <i class="fas fa-plus mr-2"></i>Create Your First Device
            </a>
        </div>
    @else
        <!-- Devices Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($devices as $device)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition transform hover:scale-105 animate-fadeIn"
                     style="animation-delay: {{ $loop->index * 0.1 }}s;">
                    
                    <!-- Header with gradient based on status -->
                    <div class="h-3 bg-gradient-to-r {{ $device->status === 'online' ? 'from-green-400 to-green-600' : 'from-gray-400 to-gray-600' }}"></div>

                    <div class="p-6">
                        <!-- Device Name & Location -->
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $device->name }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">
                            <i class="fas fa-map-marker-alt mr-1"></i>{{ $device->location }}
                        </p>

                        <!-- Status Badge -->
                        <div class="mt-3 inline-flex items-center space-x-2 px-3 py-1 rounded-full text-xs font-bold {{ 
                            $device->status === 'online' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                            'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
                        }}">
                            <span class="w-2 h-2 rounded-full {{ $device->status === 'online' ? 'bg-green-600 animate-pulse' : 'bg-gray-400' }}"></span>
                            <span>{{ ucfirst($device->status) }}</span>
                        </div>

                        <!-- Latest Reading -->
                        @if($device->latestReading())
                            <div class="mt-6 space-y-2 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">🌡️ Temperature</span>
                                    <span class="font-bold text-gray-900 dark:text-white">{{ $device->latestReading()->temperature }}°C</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">💧 Humidity</span>
                                    <span class="font-bold text-gray-900 dark:text-white">{{ $device->latestReading()->humidity }}%</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">🔥 Gas Level</span>
                                    <span class="font-bold text-gray-900 dark:text-white">{{ round($device->latestReading()->gas_level) }}</span>
                                </div>
                            </div>
                        @else
                            <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg text-center text-sm text-gray-600 dark:text-gray-400">
                                No readings yet
                            </div>
                        @endif

                        <!-- Device Info -->
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 text-xs text-gray-600 dark:text-gray-400 space-y-1">
                            <p><i class="fas fa-microchip mr-2"></i>Device ID: {{ substr($device->device_id, 0, 12) }}...</p>
                            <p><i class="fas fa-history mr-2"></i>Last Seen: {{ $device->last_seen ? $device->last_seen->diffForHumans() : 'Never' }}</p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-6 flex gap-2">
                            <a href="/device/{{ $device->id }}" class="flex-1 bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700 transition text-center text-sm font-semibold">
                                <i class="fas fa-eye mr-1"></i>View
                            </a>
                            <a href="/devices/{{ $device->id }}/edit" class="flex-1 bg-gray-600 text-white px-3 py-2 rounded-lg hover:bg-gray-700 transition text-center text-sm font-semibold">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </a>
                            <form action="/devices/{{ $device->id }}" method="POST" class="flex-1" onsubmit="return confirm('Delete this device?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-red-600 text-white px-3 py-2 rounded-lg hover:bg-red-700 transition text-sm font-semibold">
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
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