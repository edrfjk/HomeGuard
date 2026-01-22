@extends('layouts.app')

@section('title', $device->name . ' - HomeGuard')
@section('page-title', $device->name)
@section('page-subtitle', $device->location)

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div>
        <a href="/devices" class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-semibold transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back to Devices
        </a>
    </div>

    <!-- Device Info Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Status Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Status</p>
                    <div class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-full text-xs font-bold {{ 
                        $device->status === 'online' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                        'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
                    }}">
                        <span class="w-2 h-2 rounded-full {{ $device->status === 'online' ? 'bg-green-600 animate-pulse' : 'bg-gray-400' }}"></span>
                        <span>{{ ucfirst($device->status) }}</span>
                    </div>
                </div>
                <div class="bg-green-100 dark:bg-green-900 p-3 rounded-full">
                    <i class="fas fa-signal text-2xl {{ $device->status === 'online' ? 'text-green-600 dark:text-green-400' : 'text-gray-400' }}"></i>
                </div>
            </div>
        </div>

        <!-- Temperature Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Temperature</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ $device->latestReading() ? number_format($device->latestReading()->temperature, 1) : '--' }}<span class="text-lg">°C</span>
                    </p>
                </div>
                <div class="bg-orange-100 dark:bg-orange-900 p-3 rounded-full">
                    <i class="fas fa-thermometer-half text-2xl text-orange-600 dark:text-orange-400"></i>
                </div>
            </div>
        </div>

        <!-- Humidity Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Humidity</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ $device->latestReading() ? number_format($device->latestReading()->humidity, 1) : '--' }}<span class="text-lg">%</span>
                    </p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-full">
                    <i class="fas fa-tint text-2xl text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
        </div>

        <!-- Gas Level Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Gas Level</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ $device->latestReading() ? round($device->latestReading()->gas_level) : '--' }}
                    </p>
                </div>
                <div class="bg-red-100 dark:bg-red-900 p-3 rounded-full">
                    <i class="fas fa-burn text-2xl text-red-600 dark:text-red-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section with Time Range Selector -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                <i class="fas fa-chart-line mr-2"></i>Sensor Data History
            </h3>
            
            <!-- Time Range Selector -->
            <div class="flex gap-2 flex-wrap">
                <button onclick="updateChartRange(24)" class="chart-range-btn active px-4 py-2 rounded-lg text-sm font-semibold bg-blue-600 text-white hover:bg-blue-700 transition-all">
                    24 Hours
                </button>
                <button onclick="updateChartRange(168)" class="chart-range-btn px-4 py-2 rounded-lg text-sm font-semibold bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 transition-all">
                    7 Days
                </button>
                <button onclick="updateChartRange(720)" class="chart-range-btn px-4 py-2 rounded-lg text-sm font-semibold bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 transition-all">
                    30 Days
                </button>
            </div>
        </div>

        <!-- Loading Indicator -->
        <div id="chartLoading" class="hidden text-center py-8">
            <i class="fas fa-spinner fa-spin text-4xl text-blue-500 mb-2"></i>
            <p class="text-gray-600 dark:text-gray-400">Loading chart data...</p>
        </div>

        <!-- Charts Grid -->
        <div id="chartsContainer" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Temperature Chart -->
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                    <i class="fas fa-thermometer-half text-orange-500 mr-2"></i>
                    Temperature (°C)
                </h4>
                <div style="height: 250px;">
                    <canvas id="temperatureChart"></canvas>
                </div>
            </div>

            <!-- Humidity Chart -->
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                    <i class="fas fa-tint text-blue-500 mr-2"></i>
                    Humidity (%)
                </h4>
                <div style="height: 250px;">
                    <canvas id="humidityChart"></canvas>
                </div>
            </div>

            <!-- Gas Level Chart -->
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                    <i class="fas fa-burn text-red-500 mr-2"></i>
                    Gas Level
                </h4>
                <div style="height: 250px;">
                    <canvas id="gasChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Motion Detection Gallery -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
            <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">
                    <i class="fas fa-camera mr-2"></i>Motion Detection Gallery
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $device->cameraImages()->count() }} total captures
                </p>
            </div>
            @if($device->cameraImages()->count() > 0)
                <a href="{{ route('camera.gallery', $device->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold text-sm">
                    <i class="fas fa-images mr-2"></i>View Full Gallery
                </a>
            @endif
        </div>

        @if($device->cameraImages()->count() > 0)
            @php
                $latestImage = $device->latestCameraImage();
            @endphp
            
            <!-- Latest Capture Preview -->
            <div class="mb-6">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Latest Capture</h4>
                <div class="relative bg-gray-900 rounded-xl overflow-hidden aspect-video shadow-xl group cursor-pointer"
                     onclick="openImageModal('{{ $latestImage->getImageUrl() }}', '{{ $latestImage->created_at->format('M d, Y - h:i A') }}', {{ $latestImage->id }}, '{{ addslashes($latestImage->caption ?? '') }}', {{ $latestImage->is_favorite ? 'true' : 'false' }})">
                    
                    <img src="{{ $latestImage->getImageUrl() }}" 
                         alt="Latest Capture"
                         class="w-full h-full object-contain transition-transform duration-300 group-hover:scale-105">
                    
                    <!-- Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    
                    <!-- View Icon -->
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <div class="bg-white/20 backdrop-blur-sm rounded-full p-4">
                            <i class="fas fa-search-plus text-white text-3xl"></i>
                        </div>
                    </div>
                    
                    <!-- Trigger Type Badge -->
                    @php
                        $badgeClasses = match($latestImage->trigger_type) {
                            'alert' => 'bg-red-600',
                            'manual' => 'bg-blue-600',
                            default => 'bg-orange-600'
                        };
                        $badgeIcon = match($latestImage->trigger_type) {
                            'alert' => 'fa-exclamation-triangle',
                            'manual' => 'fa-hand-pointer',
                            default => 'fa-running'
                        };
                    @endphp
                    <div class="absolute top-4 left-4 {{ $badgeClasses }} text-white px-4 py-2 rounded-full text-sm font-bold shadow-lg {{ $latestImage->trigger_type === 'alert' ? 'animate-pulse' : '' }}">
                        <i class="fas {{ $badgeIcon }} mr-2"></i>{{ strtoupper($latestImage->trigger_type) }}
                    </div>

                    <!-- Favorite Star -->
                    <button onclick="event.stopPropagation(); toggleFavorite({{ $latestImage->id }}, event)" 
                            class="absolute top-4 right-4 favorite-btn z-10 transform hover:scale-110 transition-transform"
                            id="favorite-{{ $latestImage->id }}"
                            data-favorite="{{ $latestImage->is_favorite ? 'true' : 'false' }}">
                        <i class="fas fa-star text-3xl {{ $latestImage->is_favorite ? 'text-yellow-400' : 'text-white/60' }} drop-shadow-lg"></i>
                    </button>

                    <!-- Info Bar -->
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/90 to-transparent p-4">
                        <div class="flex items-center justify-between text-white">
                            <div class="flex items-center space-x-4">
                                <span class="text-sm font-semibold">
                                    <i class="fas fa-clock mr-2"></i>{{ $latestImage->created_at->diffForHumans() }}
                                </span>
                                @if($latestImage->caption)
                                    <span class="text-sm">
                                        <i class="fas fa-comment mr-2"></i>{{ $latestImage->caption }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter and Thumbnail Gallery -->
            <div class="space-y-4">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Recent Captures
                    </h4>
                    
                    <!-- Filter Buttons -->
                    <div class="flex gap-2 flex-wrap">
                        <button onclick="filterImages('all')" class="filter-btn active px-4 py-2 rounded-lg text-xs font-semibold bg-blue-600 text-white hover:bg-blue-700 transition-all">
                            <i class="fas fa-th mr-1"></i>All
                        </button>
                        <button onclick="filterImages('alert')" class="filter-btn px-4 py-2 rounded-lg text-xs font-semibold bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 transition-all">
                            <i class="fas fa-exclamation-triangle text-red-500 mr-1"></i>Alerts
                        </button>
                        <button onclick="filterImages('motion')" class="filter-btn px-4 py-2 rounded-lg text-xs font-semibold bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 transition-all">
                            <i class="fas fa-running text-orange-500 mr-1"></i>Motion
                        </button>
                        <button onclick="filterImages('favorites')" class="filter-btn px-4 py-2 rounded-lg text-xs font-semibold bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 transition-all">
                            <i class="fas fa-star text-yellow-500 mr-1"></i>Favorites
                        </button>
                    </div>
                </div>

                <!-- No Results Message -->
                <div id="noResults" class="hidden text-center py-8">
                    <i class="fas fa-filter text-gray-400 dark:text-gray-600 text-4xl mb-3"></i>
                    <p class="text-gray-600 dark:text-gray-400">No images match the selected filter</p>
                </div>

                <!-- Image Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4" id="imageGrid">
                    @foreach($device->cameraImages()->latest()->take(24)->get() as $image)
                        <div class="group relative aspect-video bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden cursor-pointer hover:ring-4 hover:ring-blue-500 transition-all duration-300 shadow-md hover:shadow-xl image-item"
                             data-trigger="{{ $image->trigger_type }}"
                             data-favorite="{{ $image->is_favorite ? 'true' : 'false' }}"
                             onclick="openImageModal('{{ $image->getImageUrl() }}', '{{ $image->created_at->format('M d, Y - h:i A') }}', {{ $image->id }}, '{{ addslashes($image->caption ?? '') }}', {{ $image->is_favorite ? 'true' : 'false' }})">
                            
                            <img src="{{ $image->getImageUrl() }}" 
                                 alt="{{ $image->caption ?? 'Capture' }}"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                 loading="lazy">
                            
                            <!-- Overlay on hover -->
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-all duration-300 flex items-center justify-center">
                                <i class="fas fa-search-plus text-white text-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></i>
                            </div>
                            
                            <!-- Trigger Type Badge -->
                            @php
                                $badgeClasses = match($image->trigger_type) {
                                    'alert' => 'bg-red-600',
                                    'manual' => 'bg-blue-600',
                                    default => 'bg-orange-600'
                                };
                            @endphp
                            <div class="absolute top-1.5 left-1.5 {{ $badgeClasses }} text-white px-2 py-0.5 rounded text-xs font-bold shadow-lg">
                                {{ strtoupper(substr($image->trigger_type, 0, 1)) }}
                            </div>

                            <!-- Favorite Star -->
                            @if($image->is_favorite)
                                <div class="absolute top-1.5 right-1.5">
                                    <i class="fas fa-star text-yellow-400 text-sm drop-shadow-lg"></i>
                                </div>
                            @endif
                            
                            <!-- Timestamp -->
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-2">
                                <p class="text-white text-xs font-semibold">{{ $image->created_at->format('M d, h:i A') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($device->cameraImages()->count() > 24)
                    <div class="text-center pt-4">
                        <a href="{{ route('camera.gallery', $device->id) }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all font-semibold text-sm shadow-lg hover:shadow-xl">
                            <i class="fas fa-images mr-2"></i>View All {{ $device->cameraImages()->count() }} Images
                        </a>
                    </div>
                @endif
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="inline-block p-6 bg-gray-100 dark:bg-gray-700 rounded-full mb-4">
                    <i class="fas fa-camera-retro text-gray-400 dark:text-gray-600 text-6xl"></i>
                </div>
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Motion Detected Yet</h4>
                <p class="text-gray-600 dark:text-gray-400">Images will appear here when the sensor detects motion</p>
            </div>
        @endif
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black/95 z-50 hidden items-center justify-center p-4 backdrop-blur-sm" onclick="closeImageModal()">
        <div class="relative max-w-7xl w-full" onclick="event.stopPropagation()">
            <!-- Close Button -->
            <button onclick="closeImageModal()" class="absolute -top-14 right-0 text-white hover:text-gray-300 transition-colors z-10 bg-white/10 rounded-full p-3 hover:bg-white/20">
                <i class="fas fa-times text-2xl"></i>
            </button>
            
            <!-- Image Container -->
            <div class="relative bg-black rounded-xl overflow-hidden shadow-2xl">
                <img id="modalImage" src="" alt="Full Size" class="w-full h-auto max-h-[80vh] object-contain">
                
                <!-- Image Info & Actions -->
                <div class="bg-gradient-to-t from-black via-black/90 to-transparent text-white px-6 py-6">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <p class="text-lg font-semibold mb-2 flex items-center">
                                <i class="fas fa-clock mr-2 text-blue-400"></i>
                                <span id="modalTimestamp"></span>
                            </p>
                            <p class="text-sm text-gray-300 line-clamp-2" id="modalCaption"></p>
                        </div>
                        <div class="flex gap-3 flex-shrink-0">
                            <button onclick="toggleFavoriteModal(event)" id="modalFavoriteBtn" class="hover:scale-110 transition-transform bg-white/10 hover:bg-white/20 rounded-full p-3" data-image-id="" data-favorite="false" title="Toggle Favorite">
                                <i class="fas fa-star text-2xl text-gray-400"></i>
                            </button>
                            <form id="deleteForm" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this image? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="hover:scale-110 transition-transform bg-red-600/20 hover:bg-red-600/40 rounded-full p-3" title="Delete Image">
                                    <i class="fas fa-trash text-2xl text-red-400"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Device Details -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
            <i class="fas fa-info-circle mr-2"></i>Device Information
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex items-center space-x-4 p-4 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-gray-700 dark:to-gray-600 rounded-lg border border-blue-200 dark:border-gray-600">
                <div class="bg-blue-600 p-3 rounded-lg">
                    <i class="fas fa-microchip text-white text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Device ID</p>
                    <p class="font-mono text-sm text-gray-900 dark:text-white truncate">{{ $device->device_id }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-4 p-4 bg-gradient-to-r from-green-50 to-green-100 dark:from-gray-700 dark:to-gray-600 rounded-lg border border-green-200 dark:border-gray-600">
                <div class="bg-green-600 p-3 rounded-lg">
                    <i class="fas fa-network-wired text-white text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">IP Address</p>
                    <p class="font-mono text-sm text-gray-900 dark:text-white">{{ $device->ip_address }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-4 p-4 bg-gradient-to-r from-red-50 to-red-100 dark:from-gray-700 dark:to-gray-600 rounded-lg border border-red-200 dark:border-gray-600">
                <div class="bg-red-600 p-3 rounded-lg">
                    <i class="fas fa-map-marker-alt text-white text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Location</p>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $device->location }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-4 p-4 bg-gradient-to-r from-purple-50 to-purple-100 dark:from-gray-700 dark:to-gray-600 rounded-lg border border-purple-200 dark:border-gray-600">
                <div class="bg-purple-600 p-3 rounded-lg">
                    <i class="fas fa-clock text-white text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Last Seen</p>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $device->last_seen ? $device->last_seen->diffForHumans() : 'Never' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4">
        <a href="/devices/{{ $device->id }}/edit" class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-3 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all text-center font-semibold shadow-lg hover:shadow-xl">
            <i class="fas fa-edit mr-2"></i>Edit Device
        </a>
        <form action="/devices/{{ $device->id }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure you want to delete this device? This action cannot be undone and will delete all associated data.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="w-full bg-gradient-to-r from-red-600 to-red-700 text-white px-6 py-3 rounded-lg hover:from-red-700 hover:to-red-800 transition-all font-semibold shadow-lg hover:shadow-xl">
                <i class="fas fa-trash mr-2"></i>Delete Device
            </button>
        </form>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
let temperatureChart, humidityChart, gasChart;
let currentRange = 24; // Default 24 hours
let currentImageId = null;

// Dark mode detection for charts
const isDarkMode = document.documentElement.classList.contains('dark');
const textColor = isDarkMode ? '#D1D5DB' : '#374151';
const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.05)';

// Chart configuration
const chartConfig = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false
        },
        tooltip: {
            backgroundColor: isDarkMode ? '#1F2937' : '#FFFFFF',
            titleColor: isDarkMode ? '#FFFFFF' : '#000000',
            bodyColor: isDarkMode ? '#D1D5DB' : '#374151',
            borderColor: isDarkMode ? '#374151' : '#E5E7EB',
            borderWidth: 1,
            padding: 12,
            displayColors: true,
            callbacks: {
                title: function(context) {
                    return context[0].label;
                },
                label: function(context) {
                    let label = context.dataset.label || '';
                    if (label) {
                        label += ': ';
                    }
                    label += context.parsed.y;
                    return label;
                }
            }
        }
    },
    scales: {
        x: {
            ticks: {
                maxTicksLimit: 8,
                font: {
                    size: 10
                },
                color: textColor
            },
            grid: {
                display: false
            }
        },
        y: {
            ticks: {
                font: {
                    size: 10
                },
                color: textColor
            },
            grid: {
                color: gridColor
            }
        }
    },
    interaction: {
        intersect: false,
        mode: 'index'
    }
};

// Initialize charts
function initCharts() {
    // Temperature Chart
    const tempCtx = document.getElementById('temperatureChart').getContext('2d');
    temperatureChart = new Chart(tempCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Temperature (°C)',
                data: [],
                borderColor: 'rgb(249, 115, 22)',
                backgroundColor: 'rgba(249, 115, 22, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointHoverRadius: 6,
                pointBackgroundColor: 'rgb(249, 115, 22)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: chartConfig
    });

    // Humidity Chart
    const humCtx = document.getElementById('humidityChart').getContext('2d');
    humidityChart = new Chart(humCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Humidity (%)',
                data: [],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointHoverRadius: 6,
                pointBackgroundColor: 'rgb(59, 130, 246)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: chartConfig
    });

    // Gas Chart
    const gasCtx = document.getElementById('gasChart').getContext('2d');
    gasChart = new Chart(gasCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Gas Level',
                data: [],
                borderColor: 'rgb(239, 68, 68)',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointHoverRadius: 6,
                pointBackgroundColor: 'rgb(239, 68, 68)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: chartConfig
    });

    // Load initial data
    loadChartData(currentRange);
}

// Load chart data
async function loadChartData(hours) {
    const loading = document.getElementById('chartLoading');
    const container = document.getElementById('chartsContainer');
    
    try {
        loading.classList.remove('hidden');
        container.classList.add('opacity-50');
        
        const response = await fetch(`/api/devices/{{ $device->id }}/readings?hours=${hours}`);
        
        if (!response.ok) {
            throw new Error('Failed to fetch data');
        }
        
        const data = await response.json();

        if (data.length === 0) {
            console.warn('No data available for the selected time range');
            // Still update charts with empty data
            updateCharts([], [], [], []);
            return;
        }

        const labels = data.map(reading => {
            const date = new Date(reading.created_at);
            if (hours <= 24) {
                return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
            } else if (hours <= 168) {
                return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', hour: '2-digit' });
            } else {
                return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            }
        });

        const temperatures = data.map(reading => parseFloat(reading.temperature));
        const humidities = data.map(reading => parseFloat(reading.humidity));
        const gasLevels = data.map(reading => parseFloat(reading.gas_level));

        updateCharts(labels, temperatures, humidities, gasLevels);
        
    } catch (error) {
        console.error('Error loading chart data:', error);
        alert('Failed to load chart data. Please try again.');
    } finally {
        loading.classList.add('hidden');
        container.classList.remove('opacity-50');
    }
}

// Update all charts
function updateCharts(labels, temperatures, humidities, gasLevels) {
    // Update temperature chart
    temperatureChart.data.labels = labels;
    temperatureChart.data.datasets[0].data = temperatures;
    temperatureChart.update('none'); // No animation for better performance

    // Update humidity chart
    humidityChart.data.labels = labels;
    humidityChart.data.datasets[0].data = humidities;
    humidityChart.update('none');

    // Update gas chart
    gasChart.data.labels = labels;
    gasChart.data.datasets[0].data = gasLevels;
    gasChart.update('none');
}

// Update chart range
function updateChartRange(hours) {
    currentRange = hours;
    
    // Update button styles
    document.querySelectorAll('.chart-range-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-blue-600', 'text-white');
        btn.classList.add('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
    });
    
    event.target.classList.add('active', 'bg-blue-600', 'text-white');
    event.target.classList.remove('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
    
    loadChartData(hours);
}

// Image Modal Functions
function openImageModal(imageSrc, timestamp, imageId, caption, isFavorite) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const modalTimestamp = document.getElementById('modalTimestamp');
    const modalCaption = document.getElementById('modalCaption');
    const modalFavoriteBtn = document.getElementById('modalFavoriteBtn');
    const deleteForm = document.getElementById('deleteForm');
    
    currentImageId = imageId;
    
    modalImage.src = imageSrc;
    modalTimestamp.textContent = timestamp;
    modalCaption.textContent = caption || 'No caption';
    modalCaption.style.fontStyle = caption ? 'normal' : 'italic';
    
    // Update favorite button
    modalFavoriteBtn.setAttribute('data-image-id', imageId);
    modalFavoriteBtn.setAttribute('data-favorite', isFavorite);
    const starIcon = modalFavoriteBtn.querySelector('i');
    starIcon.className = `fas fa-star text-2xl ${isFavorite === 'true' || isFavorite === true ? 'text-yellow-400' : 'text-gray-400'}`;
    
    // Update delete form action
    deleteForm.action = `/camera/${imageId}`;
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Prevent body scroll
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    currentImageId = null;
    
    // Restore body scroll
    document.body.style.overflow = 'auto';
}

// Toggle favorite
async function toggleFavorite(imageId, event) {
    if (event) event.stopPropagation();
    
    try {
        const response = await fetch(`/camera/${imageId}/favorite`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (response.ok) {
            const result = await response.json();
            
            // Update UI
            const btn = document.getElementById(`favorite-${imageId}`);
            if (btn) {
                btn.setAttribute('data-favorite', result.is_favorite);
                const icon = btn.querySelector('i');
                icon.className = `fas fa-star text-3xl ${result.is_favorite ? 'text-yellow-400' : 'text-white/60'} drop-shadow-lg`;
            }
            
            // Update the image item's data attribute
            const imageItems = document.querySelectorAll(`.image-item[onclick*="${imageId}"]`);
            imageItems.forEach(item => {
                item.setAttribute('data-favorite', result.is_favorite);
                // Update onclick to reflect new favorite status
                const onclickAttr = item.getAttribute('onclick');
                const newOnclick = onclickAttr.replace(/,\s*(true|false)\)$/, `, ${result.is_favorite})`);
                item.setAttribute('onclick', newOnclick);
            });
            
            // If favorites filter is active, re-apply it
            const activeFavoriteBtn = document.querySelector('.filter-btn.active[onclick*="favorites"]');
            if (activeFavoriteBtn) {
                filterImages('favorites');
            }
        }
    } catch (error) {
        console.error('Error toggling favorite:', error);
        alert('Failed to update favorite status');
    }
}

// Toggle favorite in modal
async function toggleFavoriteModal(event) {
    event.stopPropagation();
    
    const btn = document.getElementById('modalFavoriteBtn');
    const imageId = btn.getAttribute('data-image-id');
    
    try {
        const response = await fetch(`/camera/${imageId}/favorite`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (response.ok) {
            const result = await response.json();
            
            btn.setAttribute('data-favorite', result.is_favorite);
            const icon = btn.querySelector('i');
            icon.className = `fas fa-star text-2xl ${result.is_favorite ? 'text-yellow-400' : 'text-gray-400'}`;
            
            // Also update the main favorite button if visible
            const mainBtn = document.getElementById(`favorite-${imageId}`);
            if (mainBtn) {
                mainBtn.setAttribute('data-favorite', result.is_favorite);
                const mainIcon = mainBtn.querySelector('i');
                mainIcon.className = `fas fa-star text-3xl ${result.is_favorite ? 'text-yellow-400' : 'text-white/60'} drop-shadow-lg`;
            }
        }
    } catch (error) {
        console.error('Error toggling favorite:', error);
        alert('Failed to update favorite status');
    }
}

// Filter images
function filterImages(filter) {
    const items = document.querySelectorAll('.image-item');
    const noResults = document.getElementById('noResults');
    let visibleCount = 0;
    
    // Update button styles
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-blue-600', 'text-white');
        btn.classList.add('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
    });
    event.target.classList.add('active', 'bg-blue-600', 'text-white');
    event.target.classList.remove('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
    
    items.forEach(item => {
        let shouldShow = false;
        
        if (filter === 'all') {
            shouldShow = true;
        } else if (filter === 'alert') {
            shouldShow = item.getAttribute('data-trigger') === 'alert';
        } else if (filter === 'motion') {
            shouldShow = item.getAttribute('data-trigger') === 'motion';
        } else if (filter === 'favorites') {
            shouldShow = item.getAttribute('data-favorite') === 'true';
        }
        
        if (shouldShow) {
            item.style.display = 'block';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });
    
    // Show/hide no results message
    if (visibleCount === 0) {
        noResults.classList.remove('hidden');
    } else {
        noResults.classList.add('hidden');
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeImageModal();
    }
});

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    initCharts();
});

// Auto-refresh charts every 30 seconds
setInterval(() => {
    loadChartData(currentRange);
}, 30000);

// Auto-refresh page data every 60 seconds (for status updates)
setInterval(() => {
    location.reload();
}, 60000);
</script>
@endsection