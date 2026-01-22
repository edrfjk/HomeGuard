@extends('layouts.app')

@section('title', $device->name . ' - HomeGuard')
@section('page-title', $device->name)
@section('page-subtitle', $device->location)

@section('content')
@php $latest = $device->latestReading(); @endphp
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
                <div class="{{ $device->status === 'online' ? 'bg-green-100 dark:bg-green-900' : 'bg-gray-200 dark:bg-gray-700' }} p-3 rounded-full">
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
                        {{ $latest ? number_format($latest->temperature, 1) : '--' }}<span class="text-lg">°C</span>
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
                        {{ $latest ? number_format($latest->humidity, 1) : '--' }}<span class="text-lg">%</span>
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
                        {{ $latest ? round($latest->gas_level) : '--' }}
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
                <button onclick="updateChartRange(24, event)" class="chart-range-btn active px-4 py-2 rounded-lg text-sm font-semibold bg-blue-600 text-white hover:bg-blue-700 transition-all">
                    24 Hours
                </button>
                <button onclick="updateChartRange(168, event)" class="chart-range-btn px-4 py-2 rounded-lg text-sm font-semibold bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 transition-all">
                    7 Days
                </button>
                <button onclick="updateChartRange(720, event)" class="chart-range-btn px-4 py-2 rounded-lg text-sm font-semibold bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 transition-all">
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
            @php $latestImage = $device->latestCameraImage(); @endphp
            
            <!-- Latest Capture Preview -->
            <div class="mb-6">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Latest Capture</h4>
                <div class="relative bg-gray-900 rounded-xl overflow-hidden aspect-video shadow-xl group cursor-pointer"
                     onclick="openImageModal('{{ $latestImage->getImageUrl() }}', '{{ $latestImage->created_at->format('M d, Y - h:i A') }}', {{ $latestImage->id }}, {!! json_encode($latestImage->caption ?? '') !!}, {{ $latestImage->is_favorite ? 'true' : 'false' }})">
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
                    <div class="flex gap-2 flex-wrap items-center">
                        <button onclick="filterImages('all', event)" class="filter-btn active px-4 py-2 rounded-lg text-xs font-semibold bg-blue-600 text-white hover:bg-blue-700 transition-all">
                            <i class="fas fa-th mr-1"></i>All
                        </button>
                        <button onclick="filterImages('alert', event)" class="filter-btn px-4 py-2 rounded-lg text-xs font-semibold bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 transition-all">
                            <i class="fas fa-exclamation-triangle text-red-500 mr-1"></i>Alerts
                        </button>
                        <button onclick="filterImages('motion', event)" class="filter-btn px-4 py-2 rounded-lg text-xs font-semibold bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 transition-all">
                            <i class="fas fa-running text-orange-500 mr-1"></i>Motion
                        </button>
                        <button onclick="filterImages('favorites', event)" class="filter-btn px-4 py-2 rounded-lg text-xs font-semibold bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 transition-all">
                            <i class="fas fa-star text-yellow-500 mr-1"></i>Favorites
                        </button>

                        <!-- Date Filter -->
                        <input type="date" id="dateFilter" onchange="filterByDate()" class="px-3 py-1.5 rounded-lg border border-gray-300 dark:border-gray-600 text-sm text-gray-700 dark:text-gray-200">
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
                             onclick="openImageModal('{{ $image->getImageUrl() }}', '{{ $image->created_at->format('M d, Y - h:i A') }}', {{ $image->id }}, {!! json_encode($image->caption ?? '') !!}, {{ $image->is_favorite ? 'true' : 'false' }})">
                            
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
                                <p class="text-white text-xs">{{ $image->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <p class="text-gray-600 dark:text-gray-400 text-center py-8">No images captured yet.</p>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script>
let currentRange = 24;

// Chart update function
function updateChartRange(hours, event) {
    currentRange = hours;
    document.querySelectorAll('.chart-range-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-blue-600', 'text-white');
        btn.classList.add('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
    });
    if(event) {
        event.target.classList.add('active', 'bg-blue-600', 'text-white');
        event.target.classList.remove('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
    }
    loadChartData(hours); // Assume function exists to load chart
}

// Filter gallery
function filterImages(filter, event) {
    const items = document.querySelectorAll('.image-item');
    let visibleCount = 0;

    items.forEach(item => {
        let show = false;
        switch(filter) {
            case 'all': show = true; break;
            case 'alert': show = item.dataset.trigger === 'alert'; break;
            case 'motion': show = item.dataset.trigger === 'motion'; break;
            case 'favorites': show = item.dataset.favorite === 'true'; break;
        }
        item.style.display = show ? 'block' : 'none';
        if(show) visibleCount++;
    });

    document.getElementById('noResults').classList.toggle('hidden', visibleCount > 0);

    // Button active state
    if(event) {
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active', 'bg-blue-600', 'text-white');
            btn.classList.add('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
        });
        event.target.classList.add('active', 'bg-blue-600', 'text-white');
        event.target.classList.remove('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
    }
}

// Date filter
function filterByDate() {
    const selectedDate = document.getElementById('dateFilter').value;
    const items = document.querySelectorAll('.image-item');
    let visibleCount = 0;

    items.forEach(item => {
        const timestampText = item.querySelector('div.absolute.bottom-0 p')?.textContent || '';
        const itemDate = new Date(timestampText);
        const itemDateStr = itemDate.toISOString().split('T')[0];
        if(!selectedDate || itemDateStr === selectedDate) {
            item.style.display = 'block';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });

    document.getElementById('noResults').classList.toggle('hidden', visibleCount > 0);
}

// Toggle favorite
function toggleFavorite(id, event) {
    event.stopPropagation();
    const btn = document.getElementById('favorite-' + id);
    const isFav = btn.dataset.favorite === 'true';
    btn.dataset.favorite = !isFav;
    btn.querySelector('i').classList.toggle('text-yellow-400', !isFav);
    btn.querySelector('i').classList.toggle('text-white/60', isFav);
    // TODO: AJAX call to save favorite in backend
}
</script>
@endsection
