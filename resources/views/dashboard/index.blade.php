@extends('layouts.app')

@section('title', 'Dashboard - HomeGuard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Welcome back! Here\'s your home safety overview')

@section('content')
<div class="space-y-8">
    <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Devices -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition transform hover:scale-105 animate-fadeIn">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-semibold">Total Devices</p>
                        <p class="text-4xl font-bold text-gray-900 dark:text-white mt-2">{{ $totalDevices }}</p>
                    </div>
                    <div class="p-4 bg-blue-100 dark:bg-blue-900 rounded-full">
                        <i class="fa-solid fa-microchip text-blue-600 dark:text-blue-300 text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs text-green-600">
                    <i class="fas fa-arrow-up mr-1"></i>
                    <span>All devices connected</span>
                </div>
            </div>

            <!-- Online Devices -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition transform hover:scale-105 animate-fadeIn" style="animation-delay: 0.1s;">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-semibold">Online Devices</p>
                        <p class="text-4xl font-bold text-green-600 mt-2">{{ $onlineDevices }}/{{ $totalDevices }}</p>
                    </div>
                    <div class="p-4 bg-green-100 dark:bg-green-900 rounded-full">
                        <i class="fas fa-wifi text-green-600 dark:text-green-300 text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs text-green-600">
                    <i class="fas fa-check-circle mr-1"></i>
                    <span>Ready to monitor</span>
                </div>
            </div>

            <!-- Active Alerts -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition transform hover:scale-105 animate-fadeIn" style="animation-delay: 0.2s;">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-semibold">Active Alerts</p>
                        <p class="text-4xl font-bold text-orange-600 mt-2">{{ $stats['active'] }}</p>
                    </div>
                    <div class="p-4 bg-orange-100 dark:bg-orange-900 rounded-full">
                        <i class="fas fa-bell text-orange-600 dark:text-orange-300 text-2xl"></i>
                    </div>
                </div>
                @if($stats['active'] > 0)
                    <div class="mt-4 flex items-center text-xs text-orange-600 animate-pulse">
                        <i class="fas fa-circle mr-1"></i>
                        <span>Requires attention</span>
                    </div>
                @else
                    <div class="mt-4 flex items-center text-xs text-green-600">
                        <i class="fas fa-check-circle mr-1"></i>
                        <span>All clear</span>
                    </div>
                @endif
                <a href="/alerts" class="mt-4 flex items-center text-xs text-blue-600 hover:underline">
                    <span>View all</span>
                    <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>

            <!-- Critical Alerts -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition transform hover:scale-105 animate-fadeIn" style="animation-delay: 0.3s;">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-semibold">Critical Alerts</p>
                        <p class="text-4xl font-bold text-red-600 mt-2">{{ $stats['critical'] }}</p>
                    </div>
                    <div class="p-4 bg-red-100 dark:bg-red-900 rounded-full">
                        <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-300 text-2xl"></i>
                    </div>
                </div>
                @if($stats['critical'] > 0)
                    <div class="mt-4 flex items-center text-xs text-red-600 animate-pulse">
                        <i class="fas fa-circle mr-1"></i>
                        <span>Requires attention</span>
                    </div>
                @else
                    <div class="mt-4 flex items-center text-xs text-green-600">
                        <i class="fas fa-check-circle mr-1"></i>
                        <span>All safe</span>
                    </div>
                @endif
            </div>
        </div>


    <!-- Devices Grid -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 animate-fadeIn" style="animation-delay: 0.4s;">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Your Devices</h2>
                <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Real-time monitoring</p>
            </div>
            <a href="/devices/create" class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-2 rounded-lg hover:from-blue-700 hover:to-blue-800 transition shadow-lg font-semibold">
                <i class="fas fa-plus mr-2"></i>Add Device
            </a>
        </div>

        @if($devices->isEmpty())
            <div class="text-center py-12">
                <i class="fas fa-inbox text-gray-400 text-5xl mb-4 block"></i>
                <p class="text-gray-600 dark:text-gray-400 mb-4">No devices yet</p>
                <a href="/devices/create" class="text-blue-600 hover:underline font-semibold">Create your first device</a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($devices as $device)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-6 hover:shadow-lg transition transform hover:scale-105 animate-fadeIn"
                         style="animation-delay: {{ $loop->index * 0.1 }}s;">
                        <!-- Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $device->name }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <i class="fas fa-map-marker-alt mr-1"></i>{{ $device->location }}
                                </p>
                            </div>
                            <div class="px-3 py-1 rounded-full text-xs font-bold flex items-center space-x-1 {{ $device->status === 'online' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                                <span class="w-2 h-2 rounded-full {{ $device->status === 'online' ? 'bg-green-600 animate-pulse' : 'bg-gray-400' }}"></span>
                                <span>{{ ucfirst($device->status) }}</span>
                            </div>
                        </div>

                        <!-- Sensor Data -->
                        @if($latestReadings[$device->id])
                            <div class="grid grid-cols-3 gap-3 mb-4">
                                <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Temperature</p>
                                    <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">
                                        {{ $latestReadings[$device->id]->temperature }}°C
                                    </p>
                                </div>
                                <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Humidity</p>
                                    <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">
                                        {{ $latestReadings[$device->id]->humidity }}%
                                    </p>
                                </div>
                                <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Gas</p>
                                    <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">
                                        {{ round($latestReadings[$device->id]->gas_level) }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="flex gap-2">
                            <a href="/device/{{ $device->id }}" class="flex-1 bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700 transition text-center text-sm font-semibold">
                                <i class="fas fa-eye mr-1"></i>View
                            </a>
                            <a href="/devices/{{ $device->id }}/edit" class="flex-1 bg-gray-600 text-white px-3 py-2 rounded-lg hover:bg-gray-700 transition text-center text-sm font-semibold">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Recent Alerts -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition animate-fadeIn col-span-1 md:col-span-2 lg:col-span-3" style="animation-delay: 0.2s;">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-gray-600 dark:text-gray-400 text-sm font-semibold">Recent Alerts</p>
            </div>
        </div>

        <!-- Recent Alerts List -->
        <div class="mt-6 space-y-2 max-h-64 overflow-y-auto">
            @php
                $recentAlerts = auth()->user()->alerts()->where('status', 'active')->latest()->take(8)->get();
            @endphp

            @forelse($recentAlerts as $alert)
                <a href="{{ route('alerts.show', $alert) }}" class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition border-l-4 {{ 
                    $alert->severity === 'critical' ? 'border-red-500 bg-red-50 dark:bg-red-900/20' : 
                    ($alert->severity === 'warning' ? 'border-yellow-500 bg-yellow-50 dark:bg-yellow-900/20' : 'border-blue-500 bg-blue-50 dark:bg-blue-900/20')
                }}">
                    <span class="text-lg flex-shrink-0">
                        @if($alert->severity === 'critical')
                            🚨
                        @elseif($alert->severity === 'warning')
                            ⚠️
                        @else
                            ℹ️
                        @endif
                    </span>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="font-semibold text-gray-900 dark:text-white text-sm truncate">{{ $alert->device->name }}</p>
                            <span class="text-xs font-bold {{ 
                                $alert->severity === 'critical' ? 'text-red-600' : 
                                ($alert->severity === 'warning' ? 'text-yellow-600' : 'text-blue-600')
                            }}">
                                {{ ucfirst($alert->severity) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-400 truncate">{{ $alert->message }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                            <i class="fas fa-clock mr-1"></i>{{ $alert->created_at->diffForHumans() }}
                        </p>
                    </div>
                </a>
            @empty
                <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                    <i class="fas fa-check-circle text-2xl text-green-500 mb-2 block"></i>
                    <p class="text-sm">No active alerts</p>
                </div>
            @endforelse
        </div>

        <a href="/alerts" class="mt-4 block text-center text-xs text-blue-600 dark:text-blue-400 hover:underline font-semibold">
            <span>View all alerts</span>
            <i class="fas fa-arrow-right ml-1"></i>
        </a>
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

    @keyframes pulseGlow {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7);
        }
        50% {
            box-shadow: 0 0 0 10px rgba(59, 130, 246, 0);
        }
    }

    .animate-pulse-glow {
        animation: pulseGlow 2s infinite;
    }
</style>
@endsection