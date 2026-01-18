@extends('layouts.app')

@section('title', 'Settings - HomeGuard')
@section('page-title', 'System Settings')
@section('page-subtitle', 'Configure your HomeGuard system')

@section('content')
<div class="space-y-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Settings Menu -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden sticky top-24">
                <div class="p-6 bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                    <h3 class="text-lg font-bold">Settings</h3>
                </div>

                <nav class="divide-y divide-gray-200 dark:divide-gray-700">
                    <a href="?section=general" class="block px-6 py-4 {{ !request()->query('section') || request()->query('section') === 'general' ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border-l-4 border-blue-600' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }} font-semibold transition">
                        <i class="fas fa-cog mr-2"></i>General
                    </a>
                    <a href="?section=notifications" class="block px-6 py-4 {{ request()->query('section') === 'notifications' ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border-l-4 border-blue-600' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }} font-semibold transition">
                        <i class="fas fa-bell mr-2"></i>Notifications
                    </a>
                    <a href="?section=thresholds" class="block px-6 py-4 {{ request()->query('section') === 'thresholds' ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border-l-4 border-blue-600' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }} font-semibold transition">
                        <i class="fas fa-sliders-h mr-2"></i>Thresholds
                    </a>
                    <a href="?section=storage" class="block px-6 py-4 {{ request()->query('section') === 'storage' ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border-l-4 border-blue-600' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }} font-semibold transition">
                        <i class="fas fa-database mr-2"></i>Storage
                    </a>
                    <a href="?section=about" class="block px-6 py-4 {{ request()->query('section') === 'about' ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border-l-4 border-blue-600' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }} font-semibold transition">
                        <i class="fas fa-info-circle mr-2"></i>About
                    </a>
                </nav>
            </div>
        </div>

        <!-- Settings Content -->
        <div class="lg:col-span-3 space-y-6">
            <!-- General Settings -->
            @if(!request()->query('section') || request()->query('section') === 'general')
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">General Settings</h3>

                    <form action="/settings/general" method="POST" class="space-y-6">
                        @csrf

                        <div class="p-4 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-lg">
                            <p class="text-sm font-semibold text-blue-900 dark:text-blue-200">
                                <i class="fas fa-info-circle mr-2"></i>System timezone is used for all sensor readings and logs
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-globe mr-2 text-blue-600"></i>System Timezone
                            </label>
                            <select name="timezone" class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:border-blue-600 transition">
                                @php
                                    $timezones = \DateTimeZone::listIdentifiers();
                                    $userTimezone = auth()->user()->timezone ?? 'UTC';
                                @endphp
                                @foreach($timezones as $tz)
                                    <option value="{{ $tz }}" {{ $userTimezone === $tz ? 'selected' : '' }}>
                                        {{ $tz }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">
                                Current: <strong>{{ $userTimezone }}</strong>
                            </p>
                        </div>

                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-bold">
                                <i class="fas fa-save mr-2"></i>Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <!-- Notifications Settings -->
            @if(request()->query('section') === 'notifications')
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Notification Preferences</h3>

                    <form action="/settings/notifications" method="POST" class="space-y-4">
                        @csrf

                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">🚨 Critical Alerts</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Get notified for critical safety issues</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="critical_alerts" class="sr-only peer" {{ $notificationPrefs->critical_alerts ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">⚠️ Warning Alerts</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Get notified for warning conditions</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="warning_alerts" class="sr-only peer" {{ $notificationPrefs->warning_alerts ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">🔌 Device Status Changes</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Notify when device goes online/offline</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="device_status" class="sr-only peer" {{ $notificationPrefs->device_status ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <button type="submit" class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-bold">
                                <i class="fas fa-save mr-2"></i>Save Preferences
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <!-- Thresholds -->
            @if(request()->query('section') === 'thresholds')
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                        <i class="fas fa-sliders-h mr-2"></i>Safety Thresholds
                    </h3>

                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        Configure safety thresholds for each device. When sensor values exceed these thresholds, alerts will be automatically generated.
                    </p>

                    @if(auth()->user()->devices()->count() === 0)
                        <div class="p-6 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-lg text-center">
                            <i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400 text-3xl mb-2 block"></i>
                            <p class="text-yellow-800 dark:text-yellow-200">
                                You don't have any devices yet. <a href="/devices/create" class="font-bold hover:underline">Create a device</a> to manage thresholds.
                            </p>
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach(auth()->user()->devices()->where('is_active', true)->get() as $device)
                                <form action="{{ route('devices.updateThresholds', $device) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="border-2 border-gray-200 dark:border-gray-700 rounded-lg p-6">
                                        <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                                            <i class="fas fa-cctv mr-2 text-blue-600"></i>{{ $device->name }}
                                        </h4>

                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                            <!-- Temperature -->
                                            <div>
                                                <p class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                                    <i class="fas fa-thermometer-half mr-2 text-red-600"></i>Temperature (°C)
                                                </p>
                                                <div class="space-y-2">
                                                    <div>
                                                        <label class="text-xs text-gray-600 dark:text-gray-400">Warning</label>
                                                        <input type="number" name="temp_warning" value="{{ $device->safetyThreshold->temp_warning }}" step="0.1" class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:border-yellow-600 transition">
                                                    </div>
                                                    <div>
                                                        <label class="text-xs text-gray-600 dark:text-gray-400">Critical</label>
                                                        <input type="number" name="temp_critical" value="{{ $device->safetyThreshold->temp_critical }}" step="0.1" class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:border-red-600 transition">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Humidity -->
                                            <div>
                                                <p class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                                    <i class="fas fa-droplets mr-2 text-blue-600"></i>Humidity (%)
                                                </p>
                                                <div class="space-y-2">
                                                    <div>
                                                        <label class="text-xs text-gray-600 dark:text-gray-400">Warning</label>
                                                        <input type="number" name="humidity_warning" value="{{ $device->safetyThreshold->humidity_warning }}" step="0.1" class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:border-yellow-600 transition">
                                                    </div>
                                                    <div>
                                                        <label class="text-xs text-gray-600 dark:text-gray-400">Critical</label>
                                                        <input type="number" name="humidity_critical" value="{{ $device->safetyThreshold->humidity_critical }}" step="0.1" class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:border-red-600 transition">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Gas -->
                                            <div>
                                                <p class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                                    <i class="fas fa-fire mr-2 text-yellow-600"></i>Gas Level (PPM)
                                                </p>
                                                <div class="space-y-2">
                                                    <div>
                                                        <label class="text-xs text-gray-600 dark:text-gray-400">Warning</label>
                                                        <input type="number" name="gas_warning" value="{{ $device->safetyThreshold->gas_warning }}" step="1" class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:border-yellow-600 transition">
                                                    </div>
                                                    <div>
                                                        <label class="text-xs text-gray-600 dark:text-gray-400">Critical</label>
                                                        <input type="number" name="gas_critical" value="{{ $device->safetyThreshold->gas_critical }}" step="1" class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:border-red-600 transition">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <button type="submit" class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-bold text-sm">
                                            <i class="fas fa-save mr-2"></i>Save for {{ $device->name }}
                                        </button>
                                    </div>
                                </form>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            <!-- Storage -->
            @if(request()->query('section') === 'storage')
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Storage & Data Management</h3>

                    <div class="space-y-6">
                        <!-- Storage Usage -->
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <p class="font-semibold text-gray-900 dark:text-white">Storage Usage</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ \App\Helpers\StorageHelper::getTotalStorageUsed(auth()->id()) }} / {{ \App\Helpers\StorageHelper::getStorageLimitFormatted() }}
                                </p>
                            </div>
                            @php
                                $percentage = \App\Helpers\StorageHelper::getStoragePercentage(auth()->id());
                            @endphp
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4 overflow-hidden">
                                <div class="bg-gradient-to-r {{ 
                                    $percentage > 90 ? 'from-red-600 to-red-700' : 
                                    ($percentage > 70 ? 'from-yellow-600 to-yellow-700' : 
                                    'from-blue-600 to-blue-700')
                                }} h-4 rounded-full transition-all duration-300" style="width: {{ min($percentage, 100) }}%"></div>
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">
                                {{ $percentage }}% used
                                @if($percentage > 90)
                                    <span class="text-red-600 dark:text-red-400 font-bold">- Running out of storage!</span>
                                @elseif($percentage > 70)
                                    <span class="text-yellow-600 dark:text-yellow-400 font-bold">- Getting full</span>
                                @endif
                            </p>
                        </div>

                        <!-- Statistics -->
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <div class="p-4 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-lg text-center">
                                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ \App\Helpers\StorageHelper::getImageCount(auth()->id()) }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Camera Images</p>
                            </div>
                            <div class="p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-lg text-center">
                                <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ \App\Helpers\StorageHelper::getReadingCount(auth()->id()) }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Sensor Readings</p>
                            </div>
                            <div class="p-4 bg-purple-50 dark:bg-purple-900/30 border border-purple-200 dark:border-purple-700 rounded-lg text-center">
                                <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ \App\Helpers\StorageHelper::getTotalStorageUsed(auth()->id()) }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Total Size</p>
                            </div>
                        </div>

                        <!-- Data Retention -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-history mr-2"></i>Auto-Delete Old Data (Days)
                            </label>
                            <input type="number" value="90" min="1" max="365" class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:border-blue-600 transition">
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">
                                Sensor readings and images older than 90 days will be automatically deleted
                            </p>
                        </div>

                        <!-- Cleanup Actions -->
                        <div class="p-4 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-lg">
                            <p class="font-semibold text-yellow-900 dark:text-yellow-200 mb-3">
                                <i class="fas fa-exclamation-triangle mr-2"></i>Free Up Storage
                            </p>
                            <div class="space-y-2">
                                <button class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition text-sm font-semibold" onclick="if(confirm('Delete all sensor readings older than 30 days?')) { alert('Feature coming soon'); }">
                                    <i class="fas fa-trash mr-2"></i>Delete Old Sensor Data (Before 30 days)
                                </button>
                                <button class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition text-sm font-semibold" onclick="if(confirm('Delete all camera images older than 30 days?')) { alert('Feature coming soon'); }">
                                    <i class="fas fa-image mr-2"></i>Delete Old Camera Images (Before 30 days)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- About -->
            @if(request()->query('section') === 'about')
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">About HomeGuard</h3>

                    <div class="space-y-6">
                        <div class="p-6 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-lg">
                            <div class="flex items-start gap-4">
                                <i class="fas fa-home text-4xl text-blue-600 dark:text-blue-400 mt-2"></i>
                                <div>
                                    <h4 class="text-lg font-bold text-gray-900 dark:text-white">HomeGuard IoT Dashboard</h4>
                                    <p class="text-gray-700 dark:text-gray-300 mt-2">
                                        Smart Home Safety & Monitoring System using ESP32-CAM and Web-Based Dashboard
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-3">
                                        <strong>Version:</strong> 1.0.0 <br>
                                        <strong>Last Updated:</strong> January 2026 <br>
                                        <strong>Built with:</strong> Laravel, Tailwind CSS, Chart.js
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Features List -->
                        <div>
                            <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Key Features</h4>
                            <ul class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 mt-1"></i>
                                    <span>Real-time sensor monitoring</span>
                                </li>
                                <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 mt-1"></i>
                                    <span>Live ESP32-CAM image capture</span>
                                </li>
                                <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 mt-1"></i>
                                    <span>Instant alert notifications</span>
                                </li>
                                <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 mt-1"></i>
                                    <span>Historical data & charts</span>
                                </li>
                                <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 mt-1"></i>
                                    <span>Customizable thresholds</span>
                                </li>
                                <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 mt-1"></i>
                                    <span>Mobile responsive design</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Tech Stack -->
                        <div class="p-6 bg-gray-100 dark:bg-gray-700 rounded-lg">
                            <h4 class="font-bold text-gray-900 dark:text-white mb-4">Technology Stack</h4>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                                <div class="text-center">
                                    <p class="font-semibold text-gray-900 dark:text-white">Backend</p>
                                    <p class="text-gray-600 dark:text-gray-400">Laravel 11</p>
                                </div>
                                <div class="text-center">
                                    <p class="font-semibold text-gray-900 dark:text-white">Frontend</p>
                                    <p class="text-gray-600 dark:text-gray-400">Tailwind CSS</p>
                                </div>
                                <div class="text-center">
                                    <p class="font-semibold text-gray-900 dark:text-white">Database</p>
                                    <p class="text-gray-600 dark:text-gray-400">MySQL</p>
                                </div>
                                <div class="text-center">
                                    <p class="font-semibold text-gray-900 dark:text-white">Charts</p>
                                    <p class="text-gray-600 dark:text-gray-400">Chart.js</p>
                                </div>
                                <div class="text-center">
                                    <p class="font-semibold text-gray-900 dark:text-white">IoT Device</p>
                                    <p class="text-gray-600 dark:text-gray-400">ESP32-CAM</p>
                                </div>
                                <div class="text-center">
                                    <p class="font-semibold text-gray-900 dark:text-white">Sensors</p>
                                    <p class="text-gray-600 dark:text-gray-400">DHT22, MQ-2</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection