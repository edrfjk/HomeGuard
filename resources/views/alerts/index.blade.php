@extends('layouts.app')

@section('title', 'Alerts - HomeGuard')
@section('page-title', 'Alerts Management')
@section('page-subtitle', 'Monitor all safety alerts and notifications')

@section('content')
<div class="space-y-8">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 animate-fadeIn">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm font-semibold">Total Alerts</p>
                    <p class="text-4xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['total'] }}</p>
                </div>
                <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded-full">
                    <i class="fas fa-list text-gray-600 dark:text-gray-400 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 animate-fadeIn" style="animation-delay: 0.1s;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm font-semibold">Active</p>
                    <p class="text-4xl font-bold text-orange-600 mt-2">{{ $stats['active'] }}</p>
                </div>
                <div class="p-4 bg-orange-100 dark:bg-orange-900 rounded-full">
                    <i class="fas fa-clock text-orange-600 dark:text-orange-300 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 animate-fadeIn" style="animation-delay: 0.2s;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm font-semibold">Critical</p>
                    <p class="text-4xl font-bold text-red-600 mt-2">{{ $stats['critical'] }}</p>
                </div>
                <div class="p-4 bg-red-100 dark:bg-red-900 rounded-full">
                    <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-300 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 animate-fadeIn" style="animation-delay: 0.3s;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm font-semibold">Acknowledged</p>
                    <p class="text-4xl font-bold text-green-600 mt-2">{{ $stats['acknowledged'] }}</p>
                </div>
                <div class="p-4 bg-green-100 dark:bg-green-900 rounded-full">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-300 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Buttons -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 flex flex-wrap gap-3">
        <a href="/alerts" 
           class="px-4 py-2 rounded-lg font-semibold transition {{ !request()->query('status') && !request()->query('severity') ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600' }}">
            <i class="fas fa-list mr-2"></i>All Alerts
        </a>

        <div class="border-l border-gray-300 dark:border-gray-600 mx-2"></div>

        <a href="?severity=critical" 
           class="px-4 py-2 rounded-lg font-semibold transition {{ request()->query('severity') === 'critical' ? 'bg-red-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600' }}">
            <i class="fas fa-exclamation-triangle mr-2"></i>Critical
        </a>

        <a href="?severity=warning" 
           class="px-4 py-2 rounded-lg font-semibold transition {{ request()->query('severity') === 'warning' ? 'bg-yellow-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600' }}">
            <i class="fas fa-exclamation-circle mr-2"></i>Warning
        </a>

        <a href="?severity=info" 
           class="px-4 py-2 rounded-lg font-semibold transition {{ request()->query('severity') === 'info' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600' }}">
            <i class="fas fa-info-circle mr-2"></i>Info
        </a>
    </div>

    <!-- Alerts List -->
    <div class="space-y-4">
        @forelse($alerts as $alert)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border-l-4 {{ 
                $alert->severity === 'critical' ? 'border-red-500' : 
                ($alert->severity === 'warning' ? 'border-yellow-500' : 'border-blue-500')
            }} p-6 hover:shadow-xl transition transform hover:scale-101 animate-fadeIn"
                 style="animation-delay: {{ ($loop->index % 3) * 0.1 }}s;">
                
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <!-- Header -->
                        <div class="flex items-center space-x-3 mb-2">
                            <span class="text-3xl">{{ $alert->getSeverityEmoji() }}</span>
                            <div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-bold px-3 py-1 rounded {{ 
                                        $alert->severity === 'critical' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
                                        ($alert->severity === 'warning' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 
                                        'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200')
                                    }}">
                                        {{ strtoupper($alert->severity) }}
                                    </span>
                                    <span class="text-xs font-bold px-3 py-1 rounded {{ 
                                        $alert->status === 'active' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' :
                                        ($alert->status === 'acknowledged' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' :
                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200')
                                    }}">
                                        {{ strtoupper($alert->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Device & Type -->
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $alert->device->name }} - {{ ucfirst(str_replace('_', ' ', $alert->type)) }}
                        </h3>

                        <!-- Message -->
                        <p class="text-gray-700 dark:text-gray-300 mt-2">{{ $alert->message }}</p>

                        <!-- Metadata -->
                        <div class="flex flex-wrap gap-4 mt-3 text-sm text-gray-600 dark:text-gray-400">
                            <span><i class="fas fa-calendar mr-1"></i>{{ $alert->created_at->format('M d, Y H:i:s') }}</span>
                            @if($alert->reading_value && $alert->threshold_value)
                                <span><i class="fas fa-tachometer-alt mr-1"></i>Reading: {{ $alert->reading_value }} / Threshold: {{ $alert->threshold_value }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="ml-4 flex flex-col gap-2">
                        <a href="/alerts/{{ $alert->id }}" 
                           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-semibold whitespace-nowrap">
                            <i class="fas fa-eye mr-1"></i>View
                        </a>

                        @if($alert->status === 'active')
                            <form action="{{ route('alerts.acknowledge', $alert) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition text-sm font-semibold">
                                    <i class="fas fa-check mr-1"></i>Acknowledge
                                </button>
                            </form>
                        @endif

                        @if($alert->status !== 'resolved')
                            <form action="{{ route('alerts.resolve', $alert) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-semibold">
                                    <i class="fas fa-check-circle mr-1"></i>Resolve
                                </button>
                            </form>
                        @else
                            <span class="px-4 py-2 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-lg text-sm font-semibold text-center">
                                <i class="fas fa-check-circle mr-1"></i>Resolved
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-12 text-center">
                <i class="fas fa-inbox text-gray-400 dark:text-gray-600 text-5xl mb-4 block"></i>
                <p class="text-gray-600 dark:text-gray-400 text-lg">No alerts found</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="flex justify-center">
        {{ $alerts->links() }}
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