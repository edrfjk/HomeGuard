@extends('layouts.app')

@section('title', 'Alert Details - HomeGuard')
@section('page-title', 'Alert Details')
@section('page-subtitle', 'View complete alert information')

@section('content')
<div class="space-y-6">
    <!-- Alert Header with Status -->
    <div class="bg-gradient-to-r {{ 
        $alert->severity === 'critical' ? 'from-red-600 to-red-700' : 
        ($alert->severity === 'warning' ? 'from-yellow-600 to-yellow-700' : 'from-blue-600 to-blue-700')
    }} text-white rounded-xl shadow-lg p-8">
        <div class="flex items-start justify-between">
            <div class="flex items-start gap-4">
                <div class="text-6xl">
                    @if($alert->severity === 'critical')
                        🚨
                    @elseif($alert->severity === 'warning')
                        ⚠️
                    @else
                        ℹ️
                    @endif
                </div>
                <div class="flex-1">
                    <h1 class="text-4xl font-bold">{{ ucfirst(str_replace('_', ' ', $alert->type)) }}</h1>
                    <p class="text-white/80 mt-2">{{ $alert->device->name }} • {{ $alert->device->location }}</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-block px-4 py-2 rounded-full font-bold text-sm {{ 
                    $alert->status === 'active' ? 'bg-white/20 border-2 border-white' : 'bg-green-500/30 border-2 border-green-200'
                }}">
                    {{ strtoupper($alert->status) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
            <p class="text-xs text-gray-600 dark:text-gray-400 font-bold uppercase">Severity</p>
            <p class="text-2xl font-bold {{ 
                $alert->severity === 'critical' ? 'text-red-600' : 
                ($alert->severity === 'warning' ? 'text-yellow-600' : 'text-blue-600')
            }} mt-1">{{ ucfirst($alert->severity) }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
            <p class="text-xs text-gray-600 dark:text-gray-400 font-bold uppercase">Triggered</p>
            <p class="text-sm font-bold text-gray-900 dark:text-white mt-1">{{ $alert->created_at->format('M d, H:i') }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
            <p class="text-xs text-gray-600 dark:text-gray-400 font-bold uppercase">Age</p>
            <p class="text-sm font-bold text-gray-900 dark:text-white mt-1">{{ $alert->created_at->diffForHumans() }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
            <p class="text-xs text-gray-600 dark:text-gray-400 font-bold uppercase">Device Status</p>
            <p class="text-sm font-bold {{ $alert->device->status === 'online' ? 'text-green-600' : 'text-red-600' }} mt-1">
                <i class="fas fa-circle text-xs mr-1"></i>{{ ucfirst($alert->device->status) }}
            </p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Alert Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Alert Message -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Alert Message</h2>
                <div class="p-6 bg-gray-50 dark:bg-gray-700 rounded-lg border-l-4 {{ 
                    $alert->severity === 'critical' ? 'border-red-500' : 
                    ($alert->severity === 'warning' ? 'border-yellow-500' : 'border-blue-500')
                }}">
                    <p class="text-lg text-gray-900 dark:text-white leading-relaxed">{{ $alert->message }}</p>
                </div>
            </div>

            <!-- Motion Detection Image -->
            @if($alert->type === 'motion_detected' && $alert->cameraImage)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-images mr-2 text-blue-600"></i>Captured Image
                    </h2>
                    <div class="relative bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden">
                        <img src="{{ asset('storage/' . $alert->cameraImage->image_path) }}" 
                             alt="Motion detection image"
                             class="w-full h-auto object-cover rounded-lg shadow-lg max-h-96">
                        <div class="absolute top-4 right-4 px-3 py-1 bg-blue-600 text-white rounded-full text-xs font-bold">
                            <i class="fas fa-person-running mr-1"></i>Motion Capture
                        </div>
                    </div>
                    <div class="mt-4 space-y-2">
                        @if($alert->cameraImage->caption)
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <i class="fas fa-comment mr-2 text-blue-600"></i>{{ $alert->cameraImage->caption }}
                            </p>
                        @endif
                        <p class="text-xs text-gray-500 dark:text-gray-500">
                            <i class="fas fa-clock mr-1"></i>Captured {{ $alert->cameraImage->created_at->diffForHumans() }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-500">
                            <i class="fas fa-image mr-1"></i>{{ number_format($alert->cameraImage->file_size / 1024, 2) }} KB
                        </p>
                    </div>
                    <a href="{{ route('camera.view', $alert->cameraImage) }}" class="w-full mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-bold text-sm text-center block">
                        <i class="fas fa-expand mr-1"></i>View Full Image
                    </a>
                </div>
            @endif

            <!-- Alert Details Grid -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Details</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-xs text-gray-600 dark:text-gray-400 font-bold uppercase mb-2">Status</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ ucfirst($alert->status) }}</p>
                    </div>

                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-xs text-gray-600 dark:text-gray-400 font-bold uppercase mb-2">Severity</p>
                        <p class="text-lg font-bold {{ 
                            $alert->severity === 'critical' ? 'text-red-600' : 
                            ($alert->severity === 'warning' ? 'text-yellow-600' : 'text-blue-600')
                        }}">{{ ucfirst($alert->severity) }}</p>
                    </div>

                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-xs text-gray-600 dark:text-gray-400 font-bold uppercase mb-2">Device</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $alert->device->name }}</p>
                    </div>

                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-xs text-gray-600 dark:text-gray-400 font-bold uppercase mb-2">Location</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $alert->device->location }}</p>
                    </div>

                    @if($alert->reading_value)
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <p class="text-xs text-gray-600 dark:text-gray-400 font-bold uppercase mb-2">Reading Value</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $alert->reading_value }}</p>
                        </div>
                    @endif

                    @if($alert->threshold_value)
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <p class="text-xs text-gray-600 dark:text-gray-400 font-bold uppercase mb-2">Threshold Value</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $alert->threshold_value }}</p>
                        </div>
                    @endif

                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-xs text-gray-600 dark:text-gray-400 font-bold uppercase mb-2">Triggered At</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $alert->created_at->format('M d, Y H:i:s') }}</p>
                    </div>

                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-xs text-gray-600 dark:text-gray-400 font-bold uppercase mb-2">Alert Type</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $alert->type)) }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Actions</h2>
                
                <div class="flex gap-3 flex-wrap">
                    @if($alert->status === 'active')
                        <form action="{{ route('alerts.resolve', $alert) }}" method="POST" class="flex-1 min-w-max">
                            @csrf
                            <button type="submit" class="w-full px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-bold text-sm">
                                <i class="fas fa-check-circle mr-2"></i>Resolve Alert
                            </button>
                        </form>
                    @else
                        <div class="flex-1 min-w-max px-6 py-3 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 rounded-lg text-center font-bold text-sm border-2 border-green-200 dark:border-green-700">
                            <i class="fas fa-check-circle mr-2"></i>Alert Resolved
                        </div>
                    @endif

                    <a href="{{ route('alerts.index') }}" class="flex-1 min-w-max px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition font-bold text-center text-sm">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Alerts
                    </a>
                </div>
            </div>
        </div>

        <!-- Right Column - Timeline & Info -->
        <div class="space-y-6">
            <!-- Timeline -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Timeline</h2>
                
                <div class="space-y-4">
                    <!-- Triggered -->
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-3 h-3 bg-blue-600 rounded-full"></div>
                            <div class="w-0.5 h-12 bg-gray-300 dark:bg-gray-600"></div>
                        </div>
                        <div class="pb-2">
                            <p class="font-bold text-gray-900 dark:text-white text-sm">Alert Triggered</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">{{ $alert->created_at->format('M d, Y H:i:s') }}</p>
                        </div>
                    </div>

                    <!-- Status Change -->
                    @if($alert->status === 'resolved')
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div class="w-3 h-3 bg-green-600 rounded-full"></div>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 dark:text-white text-sm">Alert Resolved</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ $alert->resolved_at ? $alert->resolved_at->format('M d, Y H:i:s') : 'Marked as resolved' }}</p>
                            </div>
                        </div>
                    @else
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div class="w-3 h-3 bg-yellow-600 rounded-full opacity-50"></div>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 dark:text-white text-sm opacity-50">Pending Resolution</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Awaiting action</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Device Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Device Info</h2>
                
                <div class="space-y-3">
                    <div class="p-4 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-900/30 rounded-lg border border-blue-200 dark:border-blue-700">
                        <p class="text-xs text-gray-600 dark:text-gray-400 font-bold uppercase mb-1">Name</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $alert->device->name }}</p>
                    </div>

                    <div class="p-4 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-900/30 rounded-lg border border-purple-200 dark:border-purple-700">
                        <p class="text-xs text-gray-600 dark:text-gray-400 font-bold uppercase mb-1">Location</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $alert->device->location }}</p>
                    </div>

                    <div class="p-4 bg-gradient-to-br {{ $alert->device->status === 'online' ? 'from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-900/30 border-green-200 dark:border-green-700' : 'from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-900/30 border-red-200 dark:border-red-700' }} rounded-lg border">
                        <p class="text-xs text-gray-600 dark:text-gray-400 font-bold uppercase mb-1">Status</p>
                        <p class="text-lg font-bold {{ $alert->device->status === 'online' ? 'text-green-600' : 'text-red-600' }}">
                            <i class="fas fa-circle text-xs mr-2"></i>{{ ucfirst($alert->device->status) }}
                        </p>
                    </div>
                </div>

                <a href="/device/{{ $alert->device->id }}" class="w-full mt-4 px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-bold text-sm text-center block">
                    <i class="fas fa-arrow-right mr-2"></i>View Device
                </a>
            </div>
        </div>
    </div>
</div>

@endsection