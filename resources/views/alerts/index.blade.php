@extends('layouts.app')

@section('title', 'Alerts - HomeGuard')
@section('page-title', 'Alerts')
@section('page-subtitle', 'Monitor system alerts')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
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

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
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

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
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

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm font-semibold">Resolved</p>
                    <p class="text-4xl font-bold text-green-600 mt-2">{{ $stats['resolved'] }}</p>
                </div>
                <div class="p-4 bg-green-100 dark:bg-green-900 rounded-full">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-300 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Filters</h3>
        
        <form method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Device Filter -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Device</label>
                    <select name="device" class="w-full px-4 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:border-blue-600 transition">
                        <option value="all" {{ request('device', 'all') === 'all' ? 'selected' : '' }}>All Devices</option>
                        @foreach(auth()->user()->devices as $device)
                            <option value="{{ $device->id }}" {{ request('device') === (string)$device->id ? 'selected' : '' }}>
                                {{ $device->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:border-blue-600 transition">
                        <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="resolved" {{ $statusFilter === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    </select>
                </div>

                <!-- Severity Filter -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Severity</label>
                    <select name="severity" class="w-full px-4 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:border-blue-600 transition">
                        <option value="all" {{ $severityFilter === 'all' ? 'selected' : '' }}>All Severity</option>
                        <option value="critical" {{ $severityFilter === 'critical' ? 'selected' : '' }}>🚨 Critical</option>
                        <option value="warning" {{ $severityFilter === 'warning' ? 'selected' : '' }}>⚠️ Warning</option>
                    </select>
                </div>

                <!-- Date Filter -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Date Range</label>
                    <select name="date" class="w-full px-4 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:border-blue-600 transition">
                        <option value="all" {{ $dateFilter === 'all' ? 'selected' : '' }}>All Time</option>
                        <option value="today" {{ $dateFilter === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="week" {{ $dateFilter === 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ $dateFilter === 'month' ? 'selected' : '' }}>This Month</option>
                    </select>
                </div>

                <!-- Alert Type Filter -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Alert Type</label>
                    <select name="type" class="w-full px-4 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:border-blue-600 transition">
                        <option value="all">All Types</option>
                        <option value="motion_detected">🏃 Motion</option>
                        <option value="temperature_critical">🌡️ Temp Critical</option>
                        <option value="temperature_warning">🌡️ Temp Warning</option>
                        <option value="humidity_critical">💧 Humidity Critical</option>
                        <option value="humidity_warning">💧 Humidity Warning</option>
                        <option value="gas_critical">☠️ Gas Critical</option>
                        <option value="gas_warning">☠️ Gas Warning</option>
                    </select>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-2 pt-2">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-bold text-sm">
                    <i class="fas fa-filter mr-2"></i>Apply Filters
                </button>
                <a href="{{ route('alerts.index') }}" class="px-6 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 transition font-bold text-sm">
                    <i class="fas fa-redo mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Alerts List -->
    <div class="space-y-3">
        @forelse($alerts as $alert)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition border-l-4 {{ 
                $alert->severity === 'critical' ? 'border-red-500' : 
                ($alert->severity === 'warning' ? 'border-yellow-500' : 'border-blue-500')
            }} p-4">
                <div class="flex items-start justify-between">
                    <div class="flex items-start gap-4 flex-1">
                        <span class="text-2xl">
                            @if($alert->severity === 'critical')
                                🚨
                            @elseif($alert->severity === 'warning')
                                ⚠️
                            @else
                                ℹ️
                            @endif
                        </span>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                <h4 class="font-bold text-gray-900 dark:text-white">{{ $alert->device->name }}</h4>
                                <span class="px-2 py-1 text-xs font-bold rounded-full {{ 
                                    $alert->status === 'active' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200' : 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200'
                                }}">
                                    {{ ucfirst($alert->status) }}
                                </span>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $alert->message }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">
                                <i class="fas fa-clock mr-1"></i>{{ $alert->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('alerts.show', $alert) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-bold whitespace-nowrap ml-4">
                        View
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
                <i class="fas fa-inbox text-gray-400 text-4xl mb-4 block"></i>
                <p class="text-gray-600 dark:text-gray-400">No alerts found</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-12">
        <!-- Pagination Info Bar -->
        <div class="bg-white dark:bg-gray-800 rounded-t-xl shadow-lg p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Showing <span class="font-semibold text-gray-900 dark:text-white">{{ $alerts->firstItem() ?? 0 }}</span> 
                    to <span class="font-semibold text-gray-900 dark:text-white">{{ $alerts->lastItem() ?? 0 }}</span> 
                    of <span class="font-semibold text-gray-900 dark:text-white">{{ $alerts->total() }}</span> alerts
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Page <span class="font-semibold text-gray-900 dark:text-white">{{ $alerts->currentPage() }}</span> 
                    of <span class="font-semibold text-gray-900 dark:text-white">{{ $alerts->lastPage() }}</span>
                </div>
            </div>
        </div>

        <!-- Pagination Links -->
        <div class="bg-white dark:bg-gray-800 rounded-b-xl shadow-lg p-6">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                <!-- Previous Button -->
                <div>
                    @if($alerts->onFirstPage())
                        <button disabled class="inline-flex items-center px-4 py-2 rounded-lg text-gray-400 dark:text-gray-600 bg-gray-100 dark:bg-gray-700 cursor-not-allowed">
                            <i class="fas fa-chevron-left mr-2"></i>Previous
                        </button>
                    @else
                        <a href="{{ $alerts->previousPageUrl() }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 active:bg-blue-800 transition-all transform hover:scale-105 shadow-md font-semibold">
                            <i class="fas fa-chevron-left mr-2"></i>Previous
                        </a>
                    @endif
                </div>

                <!-- Page Numbers -->
                <div class="flex items-center gap-2 flex-wrap justify-center">
                    {{-- First Page --}}
                    @if($alerts->currentPage() > 3)
                        <a href="{{ $alerts->url(1) }}" class="px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600 transition font-semibold">
                            1
                        </a>
                        @if($alerts->currentPage() > 4)
                            <span class="text-gray-500 dark:text-gray-400 px-2">...</span>
                        @endif
                    @endif

                    {{-- Pages Around Current --}}
                    @for($i = max(1, $alerts->currentPage() - 1); $i <= min($alerts->lastPage(), $alerts->currentPage() + 1); $i++)
                        @if($i === $alerts->currentPage())
                            <span class="px-3 py-2 rounded-lg bg-blue-600 text-white font-bold shadow-md ring-2 ring-blue-300 dark:ring-blue-700">
                                {{ $i }}
                            </span>
                        @else
                            <a href="{{ $alerts->url($i) }}" class="px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600 transition font-semibold">
                                {{ $i }}
                            </a>
                        @endif
                    @endfor

                    {{-- Last Page --}}
                    @if($alerts->currentPage() < $alerts->lastPage() - 2)
                        @if($alerts->currentPage() < $alerts->lastPage() - 3)
                            <span class="text-gray-500 dark:text-gray-400 px-2">...</span>
                        @endif
                        <a href="{{ $alerts->url($alerts->lastPage()) }}" class="px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600 transition font-semibold">
                            {{ $alerts->lastPage() }}
                        </a>
                    @endif
                </div>

                <!-- Next Button -->
                <div>
                    @if($alerts->hasMorePages())
                        <a href="{{ $alerts->nextPageUrl() }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 active:bg-blue-800 transition-all transform hover:scale-105 shadow-md font-semibold">
                            Next<i class="fas fa-chevron-right ml-2"></i>
                        </a>
                    @else
                        <button disabled class="inline-flex items-center px-4 py-2 rounded-lg text-gray-400 dark:text-gray-600 bg-gray-100 dark:bg-gray-700 cursor-not-allowed">
                            Next<i class="fas fa-chevron-right ml-2"></i>
                        </button>
                    @endif
                </div>
            </div>

            
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection