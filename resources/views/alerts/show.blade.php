@extends('layouts.app')

@section('title', 'Alert Details - HomeGuard')
@section('page-title', 'Alert Details')
@section('page-subtitle', 'View complete alert information')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Alert Card -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
            <!-- Alert Badge -->
            <div class="flex items-center space-x-4 mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                <div class="text-6xl">{{ $alert->getSeverityEmoji() }}</div>
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $alert->type)) }}</h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">{{ $alert->device->name }}</p>
                </div>
            </div>

            <!-- Alert Message -->
            <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-700 rounded-lg border-2 {{ 
                $alert->severity === 'critical' ? 'border-red-500' : 
                ($alert->severity === 'warning' ? 'border-yellow-500' : 'border-blue-500')
            }}">
                <p class="text-lg text-gray-900 dark:text-white leading-relaxed">{{ $alert->message }}</p>
            </div>

            <!-- Alert Details -->
            <div class="grid grid-cols-2 gap-6 mb-8">
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Status</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white mt-2">{{ ucfirst($alert->status) }}</p>
                </div>

                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Severity</p>
                    <p class="text-lg font-bold {{ 
                        $alert->severity === 'critical' ? 'text-red-600' : 
                        ($alert->severity === 'warning' ? 'text-yellow-600' : 'text-blue-600')
                    }} mt-2">{{ ucfirst($alert->severity) }}</p>
                </div>

                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Triggered At</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white mt-2">{{ $alert->created_at->format('M d, Y H:i:s') }}</p>
                </div>

                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Device</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white mt-2">{{ $alert->device->name }}</p>
                </div>

                @if($alert->reading_value)
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Reading Value</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white mt-2">{{ $alert->reading_value }}</p>
                    </div>
                @endif

                @if($alert->threshold_value)
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Threshold Value</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white mt-2">{{ $alert->threshold_value }}</p>
                    </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3">
                @if($alert->status === 'active')
                    <form action="{{ route('alerts.acknowledge', $alert) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full px-6 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition font-bold">
                            <i class="fas fa-check mr-2"></i>Acknowledge Alert
                        </button>
                    </form>
                @endif

                @if($alert->status !== 'resolved')
                    <form action="{{ route('alerts.resolve', $alert) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-bold">
                            <i class="fas fa-check-circle mr-2"></i>Resolve Alert
                        </button>
                    </form>
                @endif

                <a href="/alerts" class="flex-1 px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition font-bold text-center">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Alerts
                </a>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Timeline -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Alert Timeline</h3>
                
                <div class="space-y-4">
                    <!-- Triggered -->
                    <div class="flex gap-3">
                        <div class="flex flex-col items-center">
                            <div class="w-3 h-3 bg-blue-600 rounded-full"></div>
                            <div class="w-0.5 h-8 bg-gray-300 dark:bg-gray-600"></div>
                        </div>
                        <div class="pb-4">
                            <p class="font-semibold text-gray-900 dark:text-white">Alert Triggered</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $alert->created_at->format('M d, Y H:i:s') }}</p>
                        </div>
                    </div>

                    <!-- Acknowledged -->
                    @if($alert->acknowledged_at)
                        <div class="flex gap-3">
                            <div class="flex flex-col items-center">
                                <div class="w-3 h-3 bg-yellow-600 rounded-full"></div>
                                <div class="w-0.5 h-8 bg-gray-300 dark:bg-gray-600"></div>
                            </div>
                            <div class="pb-4">
                                <p class="font-semibold text-gray-900 dark:text-white">Alert Acknowledged</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $alert->acknowledged_at->format('M d, Y H:i:s') }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Resolved -->
                    @if($alert->resolved_at)
                        <div class="flex gap-3">
                            <div class="flex flex-col items-center">
                                <div class="w-3 h-3 bg-green-600 rounded-full"></div>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">Alert Resolved</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $alert->resolved_at->format('M d, Y H:i:s') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Device Info -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Device Information</h3>
                
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Device Name</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $alert->device->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Location</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $alert->device->location }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Status</p>
                        <p class="font-semibold {{ $alert->device->status === 'online' ? 'text-green-600' : 'text-red-600' }}">
                            {{ ucfirst($alert->device->status) }}
                        </p>
                    </div>
                    <a href="/device/{{ $alert->device->id }}" class="mt-4 block text-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-semibold">
                        <i class="fas fa-eye mr-1"></i>View Device
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection