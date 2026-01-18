@extends('layouts.app')

@section('title', 'Camera Image - HomeGuard')
@section('page-title', 'Camera Image Viewer')
@section('page-subtitle', 'Full-screen camera capture')

@section('content')
<div class="space-y-8">
    <!-- Image Container -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl overflow-hidden">
        <div class="bg-black flex items-center justify-center min-h-[600px]">
            <img src="{{ $image->getImageUrl() }}" 
                 alt="Camera capture" 
                 class="max-w-full max-h-[600px] object-contain">
        </div>

        <!-- Image Information -->
        <div class="p-8 space-y-6">
            <!-- Header -->
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $image->device->name }}</h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">
                        <i class="fas fa-map-marker-alt mr-2"></i>{{ $image->device->location }}
                    </p>
                </div>
                <div class="px-4 py-2 rounded-full text-sm font-bold bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                    {{ ucfirst($image->trigger_type) }}
                </div>
            </div>

            <!-- Image Details -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Date & Time</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white mt-2">
                        {{ $image->created_at->format('M d, Y') }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ $image->created_at->format('H:i:s') }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Capture Type</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white mt-2">
                        @if($image->trigger_type === 'manual')
                            <i class="fas fa-hand-paper text-blue-600 mr-2"></i>Manual
                        @elseif($image->trigger_type === 'auto')
                            <i class="fas fa-robot text-green-600 mr-2"></i>Automatic
                        @else
                            <i class="fas fa-bell text-red-600 mr-2"></i>Alert
                        @endif
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">File Size</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white mt-2">
                        @if($image->file_size)
                            {{ number_format($image->file_size / 1024, 2) }} KB
                        @else
                            N/A
                        @endif
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Saved Status</p>
                    <p class="text-lg font-bold mt-2">
                        @if($image->is_favorite)
                            <span class="text-yellow-600"><i class="fas fa-star"></i> Saved</span>
                        @else
                            <span class="text-gray-600 dark:text-gray-400"><i class="fas fa-star"></i> Not Saved</span>
                        @endif
                    </p>
                </div>
            </div>

            <!-- Caption -->
            @if($image->caption)
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Caption</p>
                    <p class="text-gray-900 dark:text-white mt-2">{{ $image->caption }}</p>
                </div>
            @endif

            <!-- Actions -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6 flex gap-3 flex-wrap">
                <form action="{{ route('camera.favorite', $image) }}" method="POST" class="flex-1 min-w-max">
                    @csrf
                    <button type="submit" class="w-full {{ $image->is_favorite ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-gray-600 hover:bg-gray-700' }} text-white px-6 py-3 rounded-lg transition font-bold">
                        <i class="fas fa-star mr-2"></i>{{ $image->is_favorite ? 'Remove from Favorites' : 'Add to Favorites' }}
                    </button>
                </form>

                <form action="{{ route('camera.delete', $image) }}" method="POST" class="flex-1 min-w-max" onsubmit="return confirm('Delete this image permanently?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg transition font-bold">
                        <i class="fas fa-trash mr-2"></i>Delete Image
                    </button>
                </form>

                <a href="{{ route('camera.gallery', $image->device) }}" class="flex-1 min-w-max bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition font-bold text-center">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Gallery
                </a>
            </div>
        </div>
    </div>

    <!-- Related Images -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Recent Images from {{ $image->device->name }}</h3>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            @foreach($image->device->cameraImages()->latest()->take(10)->get() as $relatedImage)
                <a href="/camera/{{ $relatedImage->id }}" 
                   class="group relative bg-gray-100 dark:bg-gray-700 h-24 rounded-lg overflow-hidden hover:shadow-lg transition {{ $relatedImage->id === $image->id ? 'ring-4 ring-blue-600' : '' }}">
                    <img src="{{ $relatedImage->getImageUrl() }}" 
                         alt="Camera capture" 
                         class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                </a>
            @endforeach
        </div>
    </div>
</div>

@endsection