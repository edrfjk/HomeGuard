@extends('layouts.app')

@section('title', 'Profile - HomeGuard')
@section('page-title', 'My Profile')
@section('page-subtitle', 'Manage your account settings')

@section('content')
<div class="space-y-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="lg:col-span-1 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 text-center">
            <div class="w-24 h-24 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-6 text-white text-4xl font-bold shadow-lg">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>

            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ auth()->user()->name }}</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-2">{{ auth()->user()->email }}</p>

            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 space-y-2 text-sm">
                <div class="flex items-center justify-between text-gray-600 dark:text-gray-400">
                    <span>Member Since</span>
                    <strong>{{ auth()->user()->created_at->format('M d, Y') }}</strong>
                </div>
                <div class="flex items-center justify-between text-gray-600 dark:text-gray-400">
                    <span>Total Devices</span>
                    <strong>{{ auth()->user()->devices()->count() }}</strong>
                </div>
                <div class="flex items-center justify-between text-gray-600 dark:text-gray-400">
                    <span>Total Alerts</span>
                    <strong>{{ auth()->user()->alerts()->count() }}</strong>
                </div>
            </div>
        </div>

        <!-- Settings Forms -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Basic Information</h3>

                <form action="#" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Full Name
                        </label>
                        <input type="text" 
                               value="{{ auth()->user()->name }}"
                               class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:border-blue-600 transition"
                               placeholder="Your full name"
                               disabled>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Contact support to change your name
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Email Address
                        </label>
                        <input type="email" 
                               value="{{ auth()->user()->email }}"
                               class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:border-blue-600 transition"
                               placeholder="your@email.com"
                               disabled>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Contact support to change your email
                        </p>
                    </div>

                    <div class="pt-4">
                        <button class="px-6 py-2 bg-gray-400 text-white rounded-lg cursor-not-allowed opacity-50">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Security -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                    <i class="fas fa-lock mr-2 text-blue-600"></i>Security
                </h3>

                <div class="space-y-4">
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-lg">
                        <p class="text-sm font-semibold text-blue-900 dark:text-blue-200 mb-2">
                            Last Login
                        </p>
                        <p class="text-gray-700 dark:text-gray-300">
                            @if(auth()->user()->loginHistories()->latest()->first())
                                {{ auth()->user()->loginHistories()->latest()->first()->created_at->format('M d, Y H:i:s') }}
                            @else
                                No login history
                            @endif
                        </p>
                    </div>

                    <button class="w-full px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition font-semibold">
                        <i class="fas fa-key mr-2"></i>Change Password
                    </button>

                    <button class="w-full px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout All Sessions
                    </button>
                </div>
            </div>

            <!-- System Preferences -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                    <i class="fas fa-sliders-h mr-2 text-blue-600"></i>Preferences
                </h3>

                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">Email Notifications</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Receive alert emails</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">Push Notifications</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Browser notifications for alerts</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">Dark Mode</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Use dark theme for dashboard</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="bg-red-50 dark:bg-red-900/20 border-2 border-red-200 dark:border-red-700 rounded-xl p-8">
        <h3 class="text-xl font-bold text-red-900 dark:text-red-200 mb-4">
            <i class="fas fa-exclamation-triangle mr-2"></i>Danger Zone
        </h3>
        <p class="text-red-800 dark:text-red-200 mb-6">
            These actions cannot be undone. Please proceed with caution.
        </p>
        <div class="flex gap-4">
            <button class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-bold" onclick="alert('Delete account feature coming soon')">
                <i class="fas fa-trash mr-2"></i>Delete Account
            </button>
        </div>
    </div>
</div>

@endsection