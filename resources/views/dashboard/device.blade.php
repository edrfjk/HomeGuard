@extends('layouts.app')

@section('title', $device->name . ' - HomeGuard')
@section('page-title', $device->name)
@section('page-subtitle', 'Real-time monitoring & control')

@section('content')
<div class="space-y-8">
    <!-- Device Status Header -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Device Info Card -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $device->name }}</h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">
                        <i class="fas fa-map-marker-alt mr-2"></i>{{ $device->location }}
                    </p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="px-4 py-2 rounded-full text-sm font-bold flex items-center space-x-2 {{ $device->status === 'online' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700' }}">
                        <span class="w-3 h-3 rounded-full {{ $device->status === 'online' ? 'bg-green-600 animate-pulse' : 'bg-gray-400' }}"></span>
                        <span>{{ ucfirst($device->status) }}</span>
                    </div>
                    <a href="/devices/{{ $device->id }}/edit" class="p-2 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-800 transition">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <p class="text-xs text-gray-600 dark:text-gray-400">Device ID</p>
                    <p class="text-sm font-mono mt-1 text-gray-900 dark:text-white">{{ substr($device->device_id, 0, 8) }}...</p>
                </div>
                <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <p class="text-xs text-gray-600 dark:text-gray-400">Firmware</p>
                    <p class="text-sm font-bold mt-1 text-gray-900 dark:text-white">{{ $device->firmware_version ?? 'N/A' }}</p>
                </div>
                <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <p class="text-xs text-gray-600 dark:text-gray-400">IP Address</p>
                    <p class="text-sm font-mono mt-1 text-gray-900 dark:text-white">{{ $device->ip_address ?? 'N/A' }}</p>
                </div>
                <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <p class="text-xs text-gray-600 dark:text-gray-400">Last Seen</p>
                    <p class="text-sm font-bold mt-1 text-gray-900 dark:text-white">{{ $device->last_seen ? $device->last_seen->diffForHumans() : 'Never' }}</p>
                </div>
            </div>
        </div>

        <!-- Latest Reading Card -->
        @if($latestReading)
            <div class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl shadow-lg p-6 text-white">
                <h3 class="text-xl font-bold mb-4">Latest Reading</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-white/20 rounded-lg">
                        <span class="text-sm"><i class="fas fa-thermometer-half mr-2"></i>Temperature</span>
                        <span class="text-2xl font-bold">{{ $latestReading->temperature }}°C</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-white/20 rounded-lg">
                        <span class="text-sm"><i class="fas fa-droplets mr-2"></i>Humidity</span>
                        <span class="text-2xl font-bold">{{ $latestReading->humidity }}%</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-white/20 rounded-lg">
                        <span class="text-sm"><i class="fas fa-fire mr-2"></i>Gas Level</span>
                        <span class="text-2xl font-bold">{{ round($latestReading->gas_level) }}</span>
                    </div>
                </div>
                <p class="text-xs text-white/70 mt-4">
                    Updated {{ $latestReading->created_at->diffForHumans() }}
                </p>
            </div>
        @endif
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Temperature Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Temperature Trend (24h)</h3>
            <canvas id="tempChart" height="200"></canvas>
        </div>

        <!-- Humidity Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Humidity Trend (24h)</h3>
            <canvas id="humidityChart" height="200"></canvas>
        </div>
    </div>

    <!-- Safety Thresholds -->
    @if($threshold)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Safety Thresholds</h3>
                <button class="text-blue-600 hover:underline text-sm font-semibold" onclick="document.getElementById('thresholdForm').classList.toggle('hidden')">
                    <i class="fas fa-edit mr-1"></i>Edit
                </button>
            </div>

            <div id="thresholdForm" class="hidden bg-gray-50 dark:bg-gray-700 p-6 rounded-lg mb-6">
                <form action="{{ route('devices.updateThresholds', $device) }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-thermometer-half mr-2 text-red-500"></i>Temp Warning (°C)
                        </label>
                        <input type="number" name="temp_warning" value="{{ $threshold->temp_warning }}" step="0.1" class="w-full px-4 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white focus:border-blue-600 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-thermometer-half mr-2 text-orange-500"></i>Temp Critical (°C)
                        </label>
                        <input type="number" name="temp_critical" value="{{ $threshold->temp_critical }}" step="0.1" class="w-full px-4 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white focus:border-blue-600 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-droplets mr-2 text-blue-500"></i>Humidity Warning (%)
                        </label>
                        <input type="number" name="humidity_warning" value="{{ $threshold->humidity_warning }}" step="0.1" class="w-full px-4 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white focus:border-blue-600 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-droplets mr-2 text-blue-500"></i>Humidity Critical (%)
                        </label>
                        <input type="number" name="humidity_critical" value="{{ $threshold->humidity_critical }}" step="0.1" class="w-full px-4 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white focus:border-blue-600 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-fire mr-2 text-yellow-500"></i>Gas Warning (PPM)
                        </label>
                        <input type="number" name="gas_warning" value="{{ $threshold->gas_warning }}" step="1" class="w-full px-4 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white focus:border-blue-600 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-fire mr-2 text-red-500"></i>Gas Critical (PPM)
                        </label>
                        <input type="number" name="gas_critical" value="{{ $threshold->gas_critical }}" step="1" class="w-full px-4 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white focus:border-blue-600 transition">
                    </div>

                    <div class="md:col-span-3 flex gap-3">
                        <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-semibold">
                            <i class="fas fa-save mr-2"></i>Save Thresholds
                        </button>
                        <button type="button" onclick="document.getElementById('thresholdForm').classList.add('hidden')" class="flex-1 bg-gray-400 text-white px-4 py-2 rounded-lg hover:bg-gray-500 transition font-semibold">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-4 bg-blue-50 dark:bg-blue-900/30 rounded-lg border border-blue-200 dark:border-blue-700">
                    <p class="text-sm text-gray-700 dark:text-gray-300">🌡️ Temperature</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-2">
                        {{ $threshold->temp_warning }}°C / {{ $threshold->temp_critical }}°C
                    </p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Warning / Critical</p>
                </div>

                <div class="p-4 bg-cyan-50 dark:bg-cyan-900/30 rounded-lg border border-cyan-200 dark:border-cyan-700">
                    <p class="text-sm text-gray-700 dark:text-gray-300">💧 Humidity</p>
                    <p class="text-2xl font-bold text-cyan-600 dark:text-cyan-400 mt-2">
                        {{ $threshold->humidity_warning }}% / {{ $threshold->humidity_critical }}%
                    </p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Warning / Critical</p>
                </div>

                <div class="p-4 bg-yellow-50 dark:bg-yellow-900/30 rounded-lg border border-yellow-200 dark:border-yellow-700">
                    <p class="text-sm text-gray-700 dark:text-gray-300">🔥 Gas Level</p>
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-2">
                        {{ $threshold->gas_warning }} / {{ $threshold->gas_critical }}
                    </p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Warning / Critical (PPM)</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Recent Alerts -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Recent Alerts</h3>

        @if($alerts->isEmpty())
            <div class="text-center py-8 text-gray-600 dark:text-gray-400">
                <i class="fas fa-check-circle text-3xl text-green-500 mb-2 block"></i>
                <p>No alerts - This device is operating normally</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($alerts as $alert)
                    <div class="border-l-4 {{ 
                        $alert->severity === 'critical' ? 'border-red-500 bg-red-50 dark:bg-red-900/20' : 
                        ($alert->severity === 'warning' ? 'border-yellow-500 bg-yellow-50 dark:bg-yellow-900/20' : 
                        'border-blue-500 bg-blue-50 dark:bg-blue-900/20') 
                    }} p-4 rounded-r-lg">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">
                                    <span class="text-lg mr-2">{{ $alert->getSeverityEmoji() }}</span>
                                    {{ ucfirst(str_replace('_', ' ', $alert->type)) }}
                                </p>
                                <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ $alert->message }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">
                                    {{ $alert->created_at->format('M d, Y H:i') }}
                                </p>
                            </div>
                            @if($alert->status !== 'resolved')
                                <form action="{{ route('alerts.resolve', $alert) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition">
                                        Resolve
                                    </button>
                                </form>
                            @else
                                <span class="px-3 py-1 bg-green-100 text-green-800 text-sm rounded dark:bg-green-900 dark:text-green-200">
                                    Resolved
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@section('scripts')
<script>
    // Temperature Chart
    const tempCtx = document.getElementById('tempChart').getContext('2d');
    const tempLabels = {!! json_encode($readings24h->map(fn($r) => $r->created_at->format('H:i'))->reverse()) !!};
    const tempData = {!! json_encode($readings24h->map(fn($r) => $r->temperature)->reverse()) !!};

    new Chart(tempCtx, {
        type: 'line',
        data: {
            labels: tempLabels,
            datasets: [{
                label: 'Temperature (°C)',
                data: tempData,
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointBackgroundColor: '#ef4444',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    ticks: { color: '#666' }
                },
                x: {
                    ticks: { color: '#666' }
                }
            }
        }
    });

    // Humidity Chart
    const humidityCtx = document.getElementById('humidityChart').getContext('2d');
    const humidityLabels = {!! json_encode($readings24h->map(fn($r) => $r->created_at->format('H:i'))->reverse()) !!};
    const humidityData = {!! json_encode($readings24h->map(fn($r) => $r->humidity)->reverse()) !!};

    new Chart(humidityCtx, {
        type: 'line',
        data: {
            labels: humidityLabels,
            datasets: [{
                label: 'Humidity (%)',
                data: humidityData,
                borderColor: '#06b6d4',
                backgroundColor: 'rgba(6, 182, 212, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointBackgroundColor: '#06b6d4',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    max: 100,
                    ticks: { color: '#666' }
                },
                x: {
                    ticks: { color: '#666' }
                }
            }
        }
    });
</script>
@endsection
@endsection