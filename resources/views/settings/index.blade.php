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

                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                System Name
                            </label>
                            <input type="text" 
                                   value="HomeGuard IoT System"
                                   class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:border-blue-600 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                System Timezone
                            </label>
                            <select class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:border-blue-600 transition">
                                <option>UTC</option>
                                <option selected>Asia/Manila (PHT)</option>
                                <option>America/New_York (EST)</option>
                                <option>Europe/London (GMT)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Language
                            </label>
                            <select class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:border-blue-600 transition">
                                <option selected>English</option>
                                <option>Tagalog</option>
                                <option>Spanish</option>
                            </select>
                        </div>

                        <button class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-bold">
                            <i class="fas fa-save mr-2"></i>Save Changes
                        </button>
                    </div>
                </div>
            @endif

            <!-- Notifications Settings -->
            @if(request()->query('section') === 'notifications')
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Notification Preferences</h3>

                    <div class="space-y-4">
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">Critical Alerts</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Get notified for critical safety issues</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">Warning Alerts</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Get notified for warning conditions</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">Device Status Changes</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Notify when device goes online/offline</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <button class="w-full mt-6 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-bold">
                            <i class="fas fa-save mr-2"></i>Save Preferences
                        </button>
                    </div>
                </div>
            @endif

            <!-- Storage & Data -->
            @if(request()->query('section') === 'storage')
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Storage & Data Management</h3>

                    <div class="space-y-6">
                        <!-- Storage Usage -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <p class="font-semibold text-gray-900 dark:text-white">Storage Usage</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">2.4 GB / 5 GB</p>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4">
                                <div class="bg-gradient-to-r from-blue-600 to-blue-700 h-4 rounded-full" style="width: 48%"></div>
                            </div>
                        </div>

                        <!-- Data Retention -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-history mr-2"></i>Data Retention Period
                            </label>
                            <select class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:border-blue-600 transition">
                                <option>7 days</option>
                                <option>30 days</option>
                                <option selected>90 days</option>
                                <option>1 year</option>
                                <option>Keep forever</option>
                            </select>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">
                                Sensor readings and images older than this will be automatically deleted
                            </p>
                        </div>

                        <!-- Cleanup Options -->
                        <div class="p-4 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-lg">
                            <p class="font-semibold text-yellow-900 dark:text-yellow-200 mb-3">
                                <i class="fas fa-exclamation-triangle mr-2"></i>Storage Cleanup
                            </p>
                            <div class="space-y-2">
                                <button class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition text-sm font-semibold">
                                    <i class="fas fa-trash mr-2"></i>Delete Old Sensor Data (Before 30 days)
                                </button>
                                <button class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition text-sm font-semibold">
                                    <i class="fas fa-image mr-2"></i>Delete Old Camera Images (Before 30 days)
                                </button>
                            </div>
                        </div>

                        <button class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-bold">
                            <i class="fas fa-save mr-2"></i>Save Settings
                        </button>
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
                                        <strong>Last Updated:</strong> January 2024 <br>
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
                                    <p class="text-gray-600 dark:text-gray-400">Laravel 10</p>
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