@extends('layouts.app')

@section('title', $device->name . ' - Camera Gallery - HomeGuard')
@section('page-title', 'Camera Gallery')
@section('page-subtitle', 'ESP32-CAM image history for ' . $device->name)

@section('content')
<div class="space-y-8">
    <!-- Device Header -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $device->name }}</h2>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    <i class="fas fa-map-marker-alt mr-2"></i>{{ $device->location }}
                </p>
                <p class="text-gray-600 dark:text-gray-400 text-sm mt-2">
                    <i class="fas fa-images mr-2"></i>Total Images: {{ $device->cameraImages()->count() }}
                </p>
            </div>
            <div class="flex gap-2">
                <a href="/device/{{ $device->id }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Device
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Options -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <div class="flex flex-wrap gap-4 items-center">
            <div class="flex-1 min-w-max">
                <label class="text-sm font-semibold text-gray-700 dark:text-gray-300 block mb-2">Filter by Type:</label>
                <div class="flex gap-2 flex-wrap">
                    <a href="?filter=all" class="px-4 py-2 rounded-lg font-semibold transition {{ !request()->query('filter') || request()->query('filter') === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white hover:bg-gray-200' }}">
                        All Images
                    </a>
                    <a href="?filter=manual" class="px-4 py-2 rounded-lg font-semibold transition {{ request()->query('filter') === 'manual' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white hover:bg-gray-200' }}">
                        <i class="fas fa-hand-paper mr-1"></i>Manual
                    </a>
                    <a href="?filter=auto" class="px-4 py-2 rounded-lg font-semibold transition {{ request()->query('filter') === 'auto' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white hover:bg-gray-200' }}">
                        <i class="fas fa-robot mr-1"></i>Automatic
                    </a>
                    <a href="?filter=alert" class="px-4 py-2 rounded-lg font-semibold transition {{ request()->query('filter') === 'alert' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white hover:bg-gray-200' }}">
                        <i class="fas fa-bell mr-1"></i>Alert
                    </a>
                </div>
            </div>
            <div>
                <button onclick="window.location.href='/devices'" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition font-semibold">
                    <i class="fas fa-sync mr-2"></i>Refresh
                </button>
            </div>
        </div>
    </div>

    @if($images->isEmpty())
        <!-- Empty State -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-12 text-center">
            <i class="fas fa-image text-gray-400 dark:text-gray-600 text-6xl mb-4 block"></i>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">No Images Yet</h3>
            <p class="text-gray-600 dark:text-gray-400">
                Camera images from your ESP32 will appear here when available
            </p>
        </div>
    @else
        <!-- Images Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($images as $image)
                <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition transform hover:scale-105 animate-fadeIn"
                     style="animation-delay: {{ ($loop->index % 12) * 0.05 }}s;">
                    
                    <!-- Image Container -->
                    <div class="relative bg-gray-100 dark:bg-gray-700 h-48 overflow-hidden">
                        <img src="{{ $image->getImageUrl() }}" 
                             alt="Camera capture" 
                             class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                        
                        <!-- Overlay -->
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition duration-300 flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100">
                            <a href="/camera/{{ $image->id }}" 
                               class="p-3 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition">
                                <i class="fas fa-expand"></i>
                            </a>
                        </div>

                        <!-- Badge -->
                        <div class="absolute top-2 right-2 px-3 py-1 rounded-full text-xs font-bold bg-white/90 dark:bg-gray-900/90 text-gray-900 dark:text-white">
                            {{ ucfirst($image->trigger_type) }}
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="p-4">
                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">
                            <i class="fas fa-clock mr-1"></i>{{ $image->created_at->format('M d, Y H:i') }}
                        </p>
                        
                        @if($image->caption)
                            <p class="text-sm text-gray-900 dark:text-white mb-3 truncate">{{ $image->caption }}</p>
                        @endif

                        <!-- Actions -->
                        <div class="flex gap-2">
                            <a href="/camera/{{ $image->id }}" class="flex-1 bg-blue-600 text-white px-3 py-2 rounded text-center text-xs font-semibold hover:bg-blue-700 transition">
                                <i class="fas fa-eye mr-1"></i>View
                            </a>
                            <form action="{{ route('camera.favorite', $image) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full {{ $image->is_favorite ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-gray-500 hover:bg-gray-600' }} text-white px-3 py-2 rounded text-xs font-semibold transition">
                                    <i class="fas fa-star mr-1"></i>{{ $image->is_favorite ? 'Saved' : 'Save' }}
                                </button>
                            </form>
                            <form action="{{ route('camera.delete', $image) }}" method="POST" class="flex-1" onsubmit="return confirm('Delete this image?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded text-xs font-semibold transition">
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="flex justify-center mt-8">
            {{ $images->links() }}
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