@extends('layouts.app')

@section('title', $device->name . ' - Camera Gallery - HomeGuard')
@section('page-title', 'Camera Gallery')
@section('page-subtitle', 'ESP32-CAM image history for ' . $device->name)

@section('content')
<div class="space-y-8">
<!-- Back Button -->
    <div>
        <a href="{{ route('devices.show', $device->id) }}" class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-semibold transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back to Device
        </a>
    </div>

    <!-- Device Header with Stats -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 dark:from-gray-800 dark:to-gray-900 rounded-xl shadow-lg p-6 text-white">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <h2 class="text-3xl font-bold mb-2">{{ $device->name }}</h2>
                <div class="flex flex-wrap items-center gap-4 text-sm">
                    <span class="flex items-center">
                        <i class="fas fa-map-marker-alt mr-2"></i>{{ $device->location }}
                    </span>
                    <span class="flex items-center">
                        <i class="fas fa-images mr-2"></i>{{ $device->cameraImages()->count() }} Total Images
                    </span>
                    <span class="flex items-center">
                        <i class="fas fa-star mr-2 text-yellow-400"></i>{{ $device->cameraImages()->where('is_favorite', true)->count() }} Favorites
                    </span>
                </div>
            </div>
            <div class="flex gap-2">
                <button onclick="downloadSelected()" id="downloadBtn" class="hidden px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold shadow-lg">
                    <i class="fas fa-download mr-2"></i>Download Selected (<span id="selectedCount">0</span>)
                </button>
                <button onclick="deleteSelected()" id="deleteBtn" class="hidden px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold shadow-lg">
                    <i class="fas fa-trash mr-2"></i>Delete Selected
                </button>
            </div>
        </div>
    </div>

    <!-- Filter and Search Bar -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <div class="flex flex-col lg:flex-row gap-4">
            <!-- Search Box -->
            <div class="flex-1">
                <div class="relative">
                    <input type="text" 
                           id="searchInput" 
                           placeholder="Search by date, type, or caption..." 
                           class="w-full px-4 py-3 pl-10 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                           onkeyup="searchImages()">
                    <i class="fas fa-search absolute left-3 top-4 text-gray-400"></i>
                </div>
            </div>

            <!-- Filter Buttons -->
            <div class="flex flex-wrap gap-2">
                <button onclick="filterImages('all')" class="filter-btn active px-4 py-3 rounded-lg font-semibold transition-all bg-blue-600 text-white hover:bg-blue-700">
                    <i class="fas fa-th mr-2"></i>All
                </button>
                <button onclick="filterImages('alert')" class="filter-btn px-4 py-3 rounded-lg font-semibold transition-all bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600">
                    <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>Alerts
                </button>
                <button onclick="filterImages('motion')" class="filter-btn px-4 py-3 rounded-lg font-semibold transition-all bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600">
                    <i class="fas fa-running text-orange-500 mr-2"></i>Motion
                </button>
                <button onclick="filterImages('manual')" class="filter-btn px-4 py-3 rounded-lg font-semibold transition-all bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600">
                    <i class="fas fa-hand-pointer text-blue-500 mr-2"></i>Manual
                </button>
                <button onclick="filterImages('favorites')" class="filter-btn px-4 py-3 rounded-lg font-semibold transition-all bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600">
                    <i class="fas fa-star text-yellow-500 mr-2"></i>Favorites
                </button>
            </div>

            <!-- View Toggle -->
            <div class="flex gap-2 bg-gray-100 dark:bg-gray-700 p-1 rounded-lg">
                <button onclick="setViewMode('grid')" id="gridViewBtn" class="view-btn active px-4 py-2 rounded-lg transition-all bg-white dark:bg-gray-600 shadow">
                    <i class="fas fa-th"></i>
                </button>
                <button onclick="setViewMode('list')" id="listViewBtn" class="view-btn px-4 py-2 rounded-lg transition-all">
                    <i class="fas fa-list"></i>
                </button>
            </div>
        </div>

        <!-- Bulk Actions Bar -->
        <div class="mt-4 flex items-center justify-between">
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <span class="ml-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Select All</span>
            </label>
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Showing <span id="visibleCount">0</span> of {{ $images->total() }} images
            </div>
        </div>
    </div>

    <!-- No Results Message -->
    <div id="noResults" class="hidden bg-white dark:bg-gray-800 rounded-xl shadow-lg p-12 text-center">
        <i class="fas fa-search text-gray-400 dark:text-gray-600 text-6xl mb-4"></i>
        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">No Images Found</h3>
        <p class="text-gray-600 dark:text-gray-400">Try adjusting your filters or search terms</p>
    </div>

    @if($images->isEmpty())
        <!-- Empty State -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-16 text-center">
            <div class="inline-block p-6 bg-gray-100 dark:bg-gray-700 rounded-full mb-4">
                <i class="fas fa-camera-retro text-gray-400 dark:text-gray-600 text-6xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">No Images Yet</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                Camera images from your ESP32 will appear here when available
            </p>
            <a href="{{ route('devices.show', $device->id) }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                <i class="fas fa-arrow-left mr-2"></i>Return to Device
            </a>
        </div>
    @else
        <!-- Images Container -->
        <div id="imagesContainer">
            <!-- Grid View -->
            <div id="gridView" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-6">
                @foreach($images as $image)
                    <div class="image-card bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 animate-fadeIn"
                         data-trigger="{{ $image->trigger_type }}"
                         data-favorite="{{ $image->is_favorite ? 'true' : 'false' }}"
                         data-caption="{{ strtolower($image->caption ?? '') }}"
                         data-date="{{ $image->created_at->format('Y-m-d') }}"
                         data-image-id="{{ $image->id }}"
                         style="animation-delay: {{ ($loop->index % 20) * 0.03 }}s;">
                        
                        <!-- Checkbox for bulk selection -->
                        <div class="absolute top-3 left-3 z-10">
                            <input type="checkbox" 
                                   class="image-checkbox w-5 h-5 text-blue-600 border-2 border-white rounded focus:ring-blue-500 shadow-lg cursor-pointer"
                                   data-image-id="{{ $image->id }}"
                                   onchange="updateBulkActions()">
                        </div>

                        <!-- Image Container -->
                        <div class="relative bg-gray-100 dark:bg-gray-700 h-56 overflow-hidden cursor-pointer group"
                             onclick="openImageModal('{{ $image->getImageUrl() }}', '{{ $image->created_at->format('M d, Y - h:i A') }}', {{ $image->id }}, '{{ addslashes($image->caption ?? '') }}', {{ $image->is_favorite ? 'true' : 'false' }}, '{{ $image->trigger_type }}')">
                            
                            <img src="{{ $image->getImageUrl() }}" 
                                 alt="{{ $image->caption ?? 'Camera capture' }}" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                 loading="lazy">
                            
                            <!-- Gradient Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            
                            <!-- View Icon -->
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <div class="bg-white/20 backdrop-blur-sm rounded-full p-4">
                                    <i class="fas fa-search-plus text-white text-2xl"></i>
                                </div>
                            </div>

                            <!-- Trigger Type Badge -->
                            @php
                                $badgeClasses = match($image->trigger_type) {
                                    'alert' => 'bg-red-600',
                                    'manual' => 'bg-blue-600',
                                    default => 'bg-orange-600'
                                };
                                $badgeIcon = match($image->trigger_type) {
                                    'alert' => 'fa-exclamation-triangle',
                                    'manual' => 'fa-hand-pointer',
                                    default => 'fa-running'
                                };
                            @endphp
                            <div class="absolute top-3 right-3 {{ $badgeClasses }} text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg">
                                <i class="fas {{ $badgeIcon }} mr-1"></i>{{ strtoupper($image->trigger_type) }}
                            </div>

                            <!-- Favorite Star -->
                            @if($image->is_favorite)
                                <div class="absolute bottom-3 right-3">
                                    <i class="fas fa-star text-yellow-400 text-xl drop-shadow-lg"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Card Info -->
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-xs text-gray-600 dark:text-gray-400 flex items-center">
                                    <i class="fas fa-clock mr-2"></i>{{ $image->created_at->format('M d, Y') }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-500">
                                    {{ $image->created_at->format('h:i A') }}
                                </p>
                            </div>
                            
                            @if($image->caption)
                                <p class="text-sm text-gray-900 dark:text-white mb-3 line-clamp-2 min-h-[2.5rem]">
                                    {{ $image->caption }}
                                </p>
                            @else
                                <p class="text-sm text-gray-400 dark:text-gray-600 mb-3 italic min-h-[2.5rem]">
                                    No caption
                                </p>
                            @endif

                            <!-- Action Buttons -->
                            <div class="flex gap-2">
                                <button onclick="event.stopPropagation(); toggleFavorite({{ $image->id }})" 
                                        id="favorite-btn-{{ $image->id }}"
                                        data-favorite="{{ $image->is_favorite ? 'true' : 'false' }}"
                                        class="flex-1 {{ $image->is_favorite ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700' }} text-white px-3 py-2 rounded-lg text-xs font-semibold transition-all transform hover:scale-105">
                                    <i class="fas fa-star mr-1"></i>{{ $image->is_favorite ? 'Saved' : 'Save' }}
                                </button>
                                <button onclick="event.stopPropagation(); confirmDelete({{ $image->id }})" 
                                        class="flex-1 bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-xs font-semibold transition-all transform hover:scale-105">
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- List View (Hidden by default) -->
            <div id="listView" class="hidden space-y-4">
                @foreach($images as $image)
                    <div class="image-card bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 animate-fadeIn"
                         data-trigger="{{ $image->trigger_type }}"
                         data-favorite="{{ $image->is_favorite ? 'true' : 'false' }}"
                         data-caption="{{ strtolower($image->caption ?? '') }}"
                         data-date="{{ $image->created_at->format('Y-m-d') }}"
                         data-image-id="{{ $image->id }}">
                        
                        <div class="flex flex-col md:flex-row">
                            <!-- Checkbox -->
                            <div class="absolute md:relative top-3 left-3 z-10">
                                <input type="checkbox" 
                                       class="image-checkbox w-5 h-5 text-blue-600 border-2 border-white md:border-gray-300 rounded focus:ring-blue-500 shadow-lg md:shadow-none cursor-pointer md:m-4"
                                       data-image-id="{{ $image->id }}"
                                       onchange="updateBulkActions()">
                            </div>

                            <!-- Image -->
                            <div class="relative w-full md:w-64 h-48 bg-gray-100 dark:bg-gray-700 flex-shrink-0 overflow-hidden cursor-pointer group"
                                 onclick="openImageModal('{{ $image->getImageUrl() }}', '{{ $image->created_at->format('M d, Y - h:i A') }}', {{ $image->id }}, '{{ addslashes($image->caption ?? '') }}', {{ $image->is_favorite ? 'true' : 'false' }}, '{{ $image->trigger_type }}')">
                                
                                <img src="{{ $image->getImageUrl() }}" 
                                     alt="{{ $image->caption ?? 'Camera capture' }}"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                     loading="lazy">
                                
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all duration-300 flex items-center justify-center">
                                    <i class="fas fa-search-plus text-white text-2xl opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                </div>

                                <!-- Badge -->
                                @php
                                    $badgeClasses = match($image->trigger_type) {
                                        'alert' => 'bg-red-600',
                                        'manual' => 'bg-blue-600',
                                        default => 'bg-orange-600'
                                    };
                                @endphp
                                <div class="absolute top-3 right-3 {{ $badgeClasses }} text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg">
                                    {{ strtoupper($image->trigger_type) }}
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="flex-1 p-6 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-start justify-between mb-3">
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">
                                                {{ $image->caption ?? 'Untitled Capture' }}
                                            </h3>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                <i class="fas fa-clock mr-2"></i>{{ $image->created_at->format('M d, Y - h:i A') }}
                                                <span class="mx-2">•</span>
                                                {{ $image->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        @if($image->is_favorite)
                                            <i class="fas fa-star text-yellow-400 text-xl"></i>
                                        @endif
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex gap-2 mt-4">
                                    <button onclick="toggleFavorite({{ $image->id }})" 
                                            id="favorite-btn-{{ $image->id }}"
                                            data-favorite="{{ $image->is_favorite ? 'true' : 'false' }}"
                                            class="{{ $image->is_favorite ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-gray-500 hover:bg-gray-600' }} text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all">
                                        <i class="fas fa-star mr-2"></i>{{ $image->is_favorite ? 'Saved' : 'Save' }}
                                    </button>
                                    <button onclick="confirmDelete({{ $image->id }})" 
                                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all">
                                        <i class="fas fa-trash mr-2"></i>Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-12">
            <!-- Pagination Info Bar -->
            <div class="bg-white dark:bg-gray-800 rounded-t-xl shadow-lg p-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Showing <span class="font-semibold text-gray-900 dark:text-white">{{ $images->firstItem() ?? 0 }}</span> 
                        to <span class="font-semibold text-gray-900 dark:text-white">{{ $images->lastItem() ?? 0 }}</span> 
                        of <span class="font-semibold text-gray-900 dark:text-white">{{ $images->total() }}</span> images
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Page <span class="font-semibold text-gray-900 dark:text-white">{{ $images->currentPage() }}</span> 
                        of <span class="font-semibold text-gray-900 dark:text-white">{{ $images->lastPage() }}</span>
                    </div>
                </div>
            </div>

            <!-- Pagination Links -->
            <div class="bg-white dark:bg-gray-800 rounded-b-xl shadow-lg p-6">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                    <!-- Previous Button -->
                    <div>
                        @if($images->onFirstPage())
                            <button disabled class="inline-flex items-center px-4 py-2 rounded-lg text-gray-400 dark:text-gray-600 bg-gray-100 dark:bg-gray-700 cursor-not-allowed">
                                <i class="fas fa-chevron-left mr-2"></i>Previous
                            </button>
                        @else
                            <a href="{{ $images->previousPageUrl() }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 active:bg-blue-800 transition-all transform hover:scale-105 shadow-md font-semibold">
                                <i class="fas fa-chevron-left mr-2"></i>Previous
                            </a>
                        @endif
                    </div>

                    <!-- Page Numbers -->
                    <div class="flex items-center gap-2 flex-wrap justify-center">
                        {{-- First Page --}}
                        @if($images->currentPage() > 3)
                            <a href="{{ $images->url(1) }}" class="px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600 transition font-semibold">
                                1
                            </a>
                            @if($images->currentPage() > 4)
                                <span class="text-gray-500 dark:text-gray-400 px-2">...</span>
                            @endif
                        @endif

                        {{-- Pages Around Current --}}
                        @for($i = max(1, $images->currentPage() - 1); $i <= min($images->lastPage(), $images->currentPage() + 1); $i++)
                            @if($i === $images->currentPage())
                                <span class="px-3 py-2 rounded-lg bg-blue-600 text-white font-bold shadow-md ring-2 ring-blue-300 dark:ring-blue-700">
                                    {{ $i }}
                                </span>
                            @else
                                <a href="{{ $images->url($i) }}" class="px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600 transition font-semibold">
                                    {{ $i }}
                                </a>
                            @endif
                        @endfor

                        {{-- Last Page --}}
                        @if($images->currentPage() < $images->lastPage() - 2)
                            @if($images->currentPage() < $images->lastPage() - 3)
                                <span class="text-gray-500 dark:text-gray-400 px-2">...</span>
                            @endif
                            <a href="{{ $images->url($images->lastPage()) }}" class="px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600 transition font-semibold">
                                {{ $images->lastPage() }}
                            </a>
                        @endif
                    </div>

                    <!-- Next Button -->
                    <div>
                        @if($images->hasMorePages())
                            <a href="{{ $images->nextPageUrl() }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 active:bg-blue-800 transition-all transform hover:scale-105 shadow-md font-semibold">
                                Next<i class="fas fa-chevron-right ml-2"></i>
                            </a>
                        @else
                            <button disabled class="inline-flex items-center px-4 py-2 rounded-lg text-gray-400 dark:text-gray-600 bg-gray-100 dark:bg-gray-700 cursor-not-allowed">
                                Next<i class="fas fa-chevron-right ml-2"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Items Per Page Selector -->
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <form method="GET" class="flex items-center gap-3">
                            <label for="per_page" class="text-sm font-semibold text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                Images per page:
                            </label>
                            <select name="per_page" id="per_page" onchange="this.form.submit()" class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 cursor-pointer font-semibold transition">
                                <option value="12" {{ request('per_page') == 12 ? 'selected' : '' }}>12 per page</option>
                                <option value="24" {{ request('per_page') == 24 ? 'selected' : '' }}>24 per page</option>
                                <option value="48" {{ request('per_page') == 48 ? 'selected' : '' }}>48 per page</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 per page</option>
                            </select>
                        </form>
                    </div>

                    <!-- Jump to Page -->
                    <div class="flex items-center gap-3">
                        <form method="GET" class="flex items-center gap-2">
                            <label for="jump_page" class="text-sm font-semibold text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                Jump to:
                            </label>
                            <input type="number" name="page" id="jump_page" min="1" max="{{ $images->lastPage() }}" 
                                   class="w-16 px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition text-center" 
                                   placeholder="{{ $images->currentPage() }}">
                            <button type="submit" class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition font-semibold text-sm">
                                Go
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Image Modal - Improved Professional Layout -->
<div id="imageModal" class="fixed inset-0 bg-black/90 z-50 hidden items-center justify-center p-4 backdrop-blur-sm overflow-y-auto" onclick="closeImageModal()">
    <div class="relative w-full max-w-5xl my-auto" onclick="event.stopPropagation()">
        <!-- Header with Close Button -->
        <div class="bg-white dark:bg-gray-800 rounded-t-2xl shadow-2xl border-b border-gray-200 dark:border-gray-700 p-6 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Image Details</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Camera capture information and controls</p>
            </div>
            <button onclick="closeImageModal()" class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>

        <!-- Image Container -->
        <div class="bg-gray-950 p-8">
            <div class="bg-gray-900 rounded-xl overflow-hidden flex items-center justify-center max-h-96">
                <img id="modalImage" src="" alt="Full Size" class="w-full h-auto object-contain max-h-96">
            </div>
        </div>

        <!-- Info and Actions Section -->
        <div class="bg-white dark:bg-gray-800 rounded-b-2xl shadow-2xl p-6 space-y-6">
            
            <!-- Metadata Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                
                <!-- Trigger Type Badge -->
                <div>
                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2">Capture Type</p>
                    <span id="modalTriggerBadge" class="inline-block px-4 py-2 rounded-lg text-sm font-bold text-white"></span>
                </div>

                <!-- Timestamp -->
                <div>
                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2">Timestamp</p>
                    <p class="text-gray-900 dark:text-white font-semibold flex items-center">
                        <i class="fas fa-clock mr-2 text-blue-600 dark:text-blue-400"></i>
                        <span id="modalTimestamp"></span>
                    </p>
                </div>

                <!-- File Info -->
                <div>
                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2">Image ID</p>
                    <p class="text-gray-900 dark:text-white font-mono text-sm" id="modalImageId">-</p>
                </div>
            </div>

            <!-- Caption Section -->
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2">Caption</p>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                    <p class="text-gray-900 dark:text-white text-sm leading-relaxed" id="modalCaption">-</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                <button onclick="toggleFavoriteModal(event)" 
                        id="modalFavoriteBtn" 
                        class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 rounded-lg font-semibold transition-all transform hover:scale-105 active:scale-95 border-2" 
                        data-image-id="" 
                        data-favorite="false" 
                        title="Toggle Favorite">
                    <i class="fas fa-star text-lg"></i>
                    <span id="favoriteBtnText">Save</span>
                </button>

                <button onclick="downloadImage()" 
                        class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition-all transform hover:scale-105 active:scale-95 shadow-md" 
                        title="Download Image">
                    <i class="fas fa-download text-lg"></i>
                    <span>Download</span>
                </button>

                <button onclick="confirmDeleteModal()" 
                        class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition-all transform hover:scale-105 active:scale-95 shadow-md" 
                        title="Delete Image">
                    <i class="fas fa-trash text-lg"></i>
                    <span>Delete</span>
                </button>
            </div>

            <!-- Keyboard Hint -->
            <p class="text-xs text-gray-500 dark:text-gray-500 text-center pt-2">
                Press <kbd class="px-2 py-1 bg-gray-200 dark:bg-gray-700 rounded border border-gray-300 dark:border-gray-600 font-mono text-xs">ESC</kbd> to close
            </p>
        </div>
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
        animation: fadeIn 0.5s ease-out forwards;
        opacity: 0;
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Modal Scrolling */
    #imageModal {
        overflow-y: auto;
    }

    #imageModal > div {
        margin: auto;
    }

    /* Favorite button styling */
    #modalFavoriteBtn {
        border-color: currentColor;
    }

    #modalFavoriteBtn.favorite {
        @apply bg-yellow-500 border-yellow-500 text-white hover:bg-yellow-600;
    }

    #modalFavoriteBtn.not-favorite {
        @apply bg-gray-200 dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-600;
    }

    /* Smooth transitions */
    #modalFavoriteBtn i {
        transition: transform 0.2s ease;
    }

    #modalFavoriteBtn:hover i {
        transform: scale(1.2);
    }
</style>

<script>
let currentViewMode = 'grid';
let currentFilter = 'all';
let currentImageId = null;
let selectedImages = new Set();

// Filter images
function filterImages(filter) {
    currentFilter = filter;
    const cards = document.querySelectorAll('.image-card');
    const noResults = document.getElementById('noResults');
    let visibleCount = 0;
    
    // Update button styles
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-blue-600', 'text-white');
        btn.classList.add('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
    });
    event.target.classList.add('active', 'bg-blue-600', 'text-white');
    event.target.classList.remove('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
    
    cards.forEach(card => {
        let shouldShow = false;
        
        if (filter === 'all') {
            shouldShow = true;
        } else if (filter === 'favorites') {
            shouldShow = card.getAttribute('data-favorite') === 'true';
        } else {
            shouldShow = card.getAttribute('data-trigger') === filter;
        }
        
        if (shouldShow) {
            card.style.display = currentViewMode === 'grid' ? 'block' : 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Show/hide no results message
    if (visibleCount === 0) {
        noResults.classList.remove('hidden');
        document.getElementById('imagesContainer').classList.add('hidden');
    } else {
        noResults.classList.add('hidden');
        document.getElementById('imagesContainer').classList.remove('hidden');
    }
    
    updateVisibleCount(visibleCount);
}

// Search images
function searchImages() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const cards = document.querySelectorAll('.image-card');
    const noResults = document.getElementById('noResults');
    let visibleCount = 0;
    
    cards.forEach(card => {
        const caption = card.getAttribute('data-caption');
        const date = card.getAttribute('data-date');
        const trigger = card.getAttribute('data-trigger');
        
        const matchesSearch = caption.includes(searchTerm) || 
                            date.includes(searchTerm) || 
                            trigger.includes(searchTerm);
        
        const matchesFilter = currentFilter === 'all' || 
                            (currentFilter === 'favorites' && card.getAttribute('data-favorite') === 'true') ||
                            card.getAttribute('data-trigger') === currentFilter;
        
        if (matchesSearch && matchesFilter) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    if (visibleCount === 0) {
        noResults.classList.remove('hidden');
        document.getElementById('imagesContainer').classList.add('hidden');
    } else {
        noResults.classList.add('hidden');
        document.getElementById('imagesContainer').classList.remove('hidden');
    }
    
    updateVisibleCount(visibleCount);
}

// Set view mode
function setViewMode(mode) {
    currentViewMode = mode;
    const gridView = document.getElementById('gridView');
    const listView = document.getElementById('listView');
    const gridBtn = document.getElementById('gridViewBtn');
    const listBtn = document.getElementById('listViewBtn');
    
    if (mode === 'grid') {
        gridView.classList.remove('hidden');
        listView.classList.add('hidden');
        gridBtn.classList.add('active', 'bg-white', 'dark:bg-gray-600', 'shadow');
        listBtn.classList.remove('active', 'bg-white', 'dark:bg-gray-600', 'shadow');
    } else {
        gridView.classList.add('hidden');
        listView.classList.remove('hidden');
        listBtn.classList.add('active', 'bg-white', 'dark:bg-gray-600', 'shadow');
        gridBtn.classList.remove('active', 'bg-white', 'dark:bg-gray-600', 'shadow');
    }
}

// Toggle select all
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.image-checkbox');
    
    checkboxes.forEach(checkbox => {
        const card = checkbox.closest('.image-card');
        if (card.style.display !== 'none') {
            checkbox.checked = selectAll.checked;
            if (selectAll.checked) {
                selectedImages.add(parseInt(checkbox.getAttribute('data-image-id')));
            } else {
                selectedImages.delete(parseInt(checkbox.getAttribute('data-image-id')));
            }
        }
    });
    
    updateBulkActions();
}

// Update bulk actions
function updateBulkActions() {
    selectedImages.clear();
    document.querySelectorAll('.image-checkbox:checked').forEach(checkbox => {
        selectedImages.add(parseInt(checkbox.getAttribute('data-image-id')));
    });
    
    const count = selectedImages.size;
    document.getElementById('selectedCount').textContent = count;
    
    const downloadBtn = document.getElementById('downloadBtn');
    const deleteBtn = document.getElementById('deleteBtn');
    
    if (count > 0) {
        downloadBtn.classList.remove('hidden');
        deleteBtn.classList.remove('hidden');
    } else {
        downloadBtn.classList.add('hidden');
        deleteBtn.classList.add('hidden');
    }
}

// Update visible count
function updateVisibleCount(count) {
    document.getElementById('visibleCount').textContent = count;
}

// Toggle favorite
async function toggleFavorite(imageId) {
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
            
            // Update button
            const btn = document.getElementById(`favorite-btn-${imageId}`);
            if (btn) {
                btn.setAttribute('data-favorite', result.is_favorite);
                btn.className = `flex-1 ${result.is_favorite ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700'} text-white px-3 py-2 rounded-lg text-xs font-semibold transition-all transform hover:scale-105`;
                btn.innerHTML = `<i class="fas fa-star mr-1"></i>${result.is_favorite ? 'Saved' : 'Save'}`;
            }
            
            // Update card data
            const cards = document.querySelectorAll(`[data-image-id="${imageId}"]`);
            cards.forEach(card => {
                if (card.classList.contains('image-card')) {
                    card.setAttribute('data-favorite', result.is_favorite);
                }
            });
            
            // Re-apply filter if favorites is active
            if (currentFilter === 'favorites') {
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
            
            // Update modal button
            btn.setAttribute('data-favorite', result.is_favorite);
            const favoriteBtnText = document.getElementById('favoriteBtnText');
            
            if (result.is_favorite) {
                btn.classList.remove('not-favorite');
                btn.classList.add('favorite');
                favoriteBtnText.textContent = 'Saved';
            } else {
                btn.classList.remove('favorite');
                btn.classList.add('not-favorite');
                favoriteBtnText.textContent = 'Save';
            }
            
            // Update the card favorite button
            toggleFavorite(imageId);
        }
    } catch (error) {
        console.error('Error toggling favorite:', error);
        alert('Failed to update favorite status');
    }
}

// Confirm delete single image
function confirmDelete(imageId) {
    if (confirm('Are you sure you want to delete this image? This action cannot be undone.')) {
        deleteImage(imageId);
    }
}

// Confirm delete from modal
function confirmDeleteModal() {
    const imageId = document.getElementById('modalFavoriteBtn').getAttribute('data-image-id');
    if (confirm('Are you sure you want to delete this image? This action cannot be undone.')) {
        deleteImage(imageId);
        closeImageModal();
    }
}

// Delete image
async function deleteImage(imageId) {
    try {
        const response = await fetch(`/camera/${imageId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (response.ok) {
            // Remove card from DOM
            const cards = document.querySelectorAll(`[data-image-id="${imageId}"]`);
            cards.forEach(card => {
                if (card.classList.contains('image-card')) {
                    card.style.animation = 'fadeOut 0.3s ease-out';
                    setTimeout(() => card.remove(), 300);
                }
            });
            
            // Update counts
            setTimeout(() => {
                const visibleCards = document.querySelectorAll('.image-card:not([style*="display: none"])');
                updateVisibleCount(visibleCards.length);
            }, 350);
        } else {
            alert('Failed to delete image');
        }
    } catch (error) {
        console.error('Error deleting image:', error);
        alert('Failed to delete image');
    }
}

// Delete selected images
async function deleteSelected() {
    if (selectedImages.size === 0) return;
    
    if (confirm(`Are you sure you want to delete ${selectedImages.size} selected image(s)? This action cannot be undone.`)) {
        const promises = Array.from(selectedImages).map(id => deleteImage(id));
        await Promise.all(promises);
        
        selectedImages.clear();
        document.getElementById('selectAll').checked = false;
        updateBulkActions();
    }
}

// Download image
function downloadImage() {
    const imageSrc = document.getElementById('modalImage').src;
    const link = document.createElement('a');
    link.href = imageSrc;
    link.download = `camera-capture-${Date.now()}.jpg`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Download selected images
function downloadSelected() {
    if (selectedImages.size === 0) return;
    
    selectedImages.forEach(imageId => {
        const card = document.querySelector(`[data-image-id="${imageId}"].image-card`);
        const img = card.querySelector('img');
        const link = document.createElement('a');
        link.href = img.src;
        link.download = `camera-capture-${imageId}.jpg`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
}

// Open image modal
function openImageModal(imageSrc, timestamp, imageId, caption, isFavorite, triggerType) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const modalTimestamp = document.getElementById('modalTimestamp');
    const modalCaption = document.getElementById('modalCaption');
    const modalFavoriteBtn = document.getElementById('modalFavoriteBtn');
    const modalTriggerBadge = document.getElementById('modalTriggerBadge');
    const modalImageId = document.getElementById('modalImageId');
    const favoriteBtnText = document.getElementById('favoriteBtnText');
    
    currentImageId = imageId;
    
    // Set image
    modalImage.src = imageSrc;
    
    // Set timestamp
    modalTimestamp.textContent = timestamp;
    
    // Set caption
    if (caption) {
        modalCaption.textContent = caption;
    } else {
        modalCaption.innerHTML = '<em class="text-gray-500 dark:text-gray-400">No caption provided</em>';
    }
    
    // Set image ID
    modalImageId.textContent = `#${imageId}`;
    
    // Update favorite button
    modalFavoriteBtn.setAttribute('data-image-id', imageId);
    modalFavoriteBtn.setAttribute('data-favorite', isFavorite);
    const isFav = isFavorite === 'true' || isFavorite === true;
    if (isFav) {
        modalFavoriteBtn.classList.remove('not-favorite');
        modalFavoriteBtn.classList.add('favorite');
        favoriteBtnText.textContent = 'Saved';
    } else {
        modalFavoriteBtn.classList.remove('favorite');
        modalFavoriteBtn.classList.add('not-favorite');
        favoriteBtnText.textContent = 'Save';
    }
    
    // Update trigger badge
    const badgeClasses = {
        'alert': 'bg-red-600 text-white',
        'manual': 'bg-blue-600 text-white',
        'motion': 'bg-orange-600 text-white'
    };
    const badgeIcons = {
        'alert': 'fa-exclamation-triangle',
        'manual': 'fa-hand-pointer',
        'motion': 'fa-running'
    };
    const badgeLabels = {
        'alert': 'Alert Triggered',
        'manual': 'Manual Capture',
        'motion': 'Motion Detected'
    };
    
    modalTriggerBadge.className = `px-4 py-2 rounded-lg text-sm font-bold ${badgeClasses[triggerType] || 'bg-gray-600 text-white'}`;
    modalTriggerBadge.innerHTML = `<i class="fas ${badgeIcons[triggerType] || 'fa-camera'} mr-2"></i>${badgeLabels[triggerType] || triggerType.toUpperCase()}`;
    
    // Show modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Prevent body scroll
    document.body.style.overflow = 'hidden';
}

// Close image modal
function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    currentImageId = null;
    
    // Restore body scroll
    document.body.style.overflow = 'auto';
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeImageModal();
    }
});

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Set initial visible count
    const visibleCards = document.querySelectorAll('.image-card');
    updateVisibleCount(visibleCards.length);
});

// Add fadeOut animation
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeOut {
        from {
            opacity: 1;
            transform: scale(1);
        }
        to {
            opacity: 0;
            transform: scale(0.9);
        }
    }
`;
document.head.appendChild(style);
</script>

@endsection