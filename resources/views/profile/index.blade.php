@extends('layouts.app')

@section('title', 'Profile - HomeGuard')
@section('page-title', 'My Profile')
@section('page-subtitle', 'Manage your account settings')

@section('content')
<div class="space-y-6">
    <!-- Profile Header -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl shadow-lg p-8 text-white">
        <div class="flex items-center gap-6">
            <div class="w-24 h-24 bg-white/20 backdrop-blur rounded-full flex items-center justify-center text-5xl font-bold">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-3xl font-bold">{{ auth()->user()->name }}</h2>
                <p class="text-blue-100">{{ auth()->user()->email }}</p>
                <p class="text-sm text-blue-200 mt-2">
                    <i class="fas fa-calendar mr-1"></i>Member since {{ auth()->user()->created_at->format('M d, Y') }}
                </p>
            </div>
        </div>

        <!-- Stats Row -->
        <div class="grid grid-cols-3 gap-6 mt-8 pt-8 border-t border-white/20">
            <div>
                <p class="text-blue-100 text-sm font-semibold uppercase">Total Devices</p>
                <p class="text-3xl font-bold mt-2">{{ auth()->user()->devices()->count() }}</p>
            </div>
            <div>
                <p class="text-blue-100 text-sm font-semibold uppercase">Total Alerts</p>
                <p class="text-3xl font-bold mt-2">{{ auth()->user()->alerts()->count() }}</p>
            </div>
            <div>
                <p class="text-blue-100 text-sm font-semibold uppercase">Active Alerts</p>
                <p class="text-3xl font-bold mt-2">{{ auth()->user()->alerts()->where('status', 'active')->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Account Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                <i class="fas fa-user-circle mr-2 text-blue-600"></i>Account
            </h3>

            <div class="space-y-5">
                <div>
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Full Name</p>
                    <p class="text-gray-900 dark:text-white font-semibold mt-1">{{ auth()->user()->name }}</p>
                </div>

                <div>
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Email Address</p>
                    <p class="text-gray-900 dark:text-white font-semibold mt-1">{{ auth()->user()->email }}</p>
                </div>

                <div>
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Member Since</p>
                    <p class="text-gray-900 dark:text-white font-semibold mt-1">{{ auth()->user()->created_at->format('F d, Y') }}</p>
                </div>

                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold text-sm" onclick="alert('Coming soon')">
                        <i class="fas fa-edit mr-1"></i>Edit Profile
                    </button>
                </div>
            </div>
        </div>

        <!-- Security Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                <i class="fas fa-shield-alt mr-2 text-blue-600"></i>Security
            </h3>

            <div class="space-y-5">
                <div>
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Last Login</p>
                    <p class="text-gray-900 dark:text-white font-semibold mt-1">
                        @if(auth()->user()->loginHistories && auth()->user()->loginHistories()->latest()->first())
                            {{ auth()->user()->loginHistories()->latest()->first()->created_at->format('M d, Y H:i') }}
                            <span class="text-xs text-gray-500 dark:text-gray-400 block">IP: {{ auth()->user()->loginHistories()->latest()->first()->ip_address }}</span>
                        @else
                            <span class="text-gray-500 dark:text-gray-400 italic">First login</span>
                        @endif
                    </p>
                </div>

                <div class="pt-4 border-t border-gray-200 dark:border-gray-700 space-y-2">
                    <button class="w-full px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition font-semibold text-sm" onclick="alert('Coming soon')">
                        <i class="fas fa-key mr-1"></i>Change Password
                    </button>
                    <button class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold text-sm" onclick="alert('Coming soon')">
                        <i class="fas fa-sign-out-alt mr-1"></i>Logout All Sessions
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Preferences Section - FULLY WORKING TOGGLES -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
            <i class="fas fa-cog mr-2 text-blue-600"></i>Preferences
        </h3>

        <form id="preferencesForm" class="space-y-0 divide-y divide-gray-200 dark:divide-gray-700">
            <!-- Email Notifications -->
            <div class="py-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="text-3xl">📧</div>
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white">Email Notifications</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Receive important alerts via email</p>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <button type="button" class="toggle-btn w-12 h-7 bg-blue-600 rounded-full relative transition-all flex items-center" data-name="email_notifications" data-checked="true" onclick="toggleButton(event)">
                        <div class="w-5 h-5 bg-white rounded-full absolute left-1 transition-transform"></div>
                    </button>
                </div>
            </div>

            <!-- Push Notifications -->
            <div class="py-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="text-3xl">🔔</div>
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white">Push Notifications</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Browser notifications for real-time alerts</p>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <button type="button" class="toggle-btn w-12 h-7 bg-blue-600 rounded-full relative transition-all flex items-center" data-name="push_notifications" data-checked="true" onclick="toggleButton(event)">
                        <div class="w-5 h-5 bg-white rounded-full absolute left-1 transition-transform"></div>
                    </button>
                </div>
            </div>

            <!-- Dark Mode -->
            <div class="py-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="text-3xl">🌙</div>
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white">Dark Mode</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Use dark theme for better visibility at night</p>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <button type="button" class="toggle-btn w-12 h-7 bg-gray-300 dark:bg-gray-600 rounded-full relative transition-all flex items-center" data-name="dark_mode" data-checked="false" onclick="toggleDarkMode(event)">
                        <div class="w-5 h-5 bg-white rounded-full absolute left-1 transition-transform"></div>
                    </button>
                </div>
            </div>

            <!-- Two Factor Auth -->
            <div class="py-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="text-3xl">🔐</div>
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white">Two-Factor Authentication</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Add extra security to your account</p>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <button type="button" class="toggle-btn w-12 h-7 bg-gray-300 dark:bg-gray-600 rounded-full relative transition-all flex items-center" data-name="two_factor" data-checked="false" onclick="toggleButton(event)">
                        <div class="w-5 h-5 bg-white rounded-full absolute left-1 transition-transform"></div>
                    </button>
                </div>
            </div>

            <!-- Auto-logout -->
            <div class="py-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="text-3xl">⏱️</div>
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white">Auto-Logout</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Automatically logout after 30 minutes of inactivity</p>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <button type="button" class="toggle-btn w-12 h-7 bg-blue-600 rounded-full relative transition-all flex items-center" data-name="auto_logout" data-checked="true" onclick="toggleButton(event)">
                        <div class="w-5 h-5 bg-white rounded-full absolute left-1 transition-transform"></div>
                    </button>
                </div>
            </div>

            <!-- Weekly Digest -->
            <div class="py-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="text-3xl">📰</div>
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white">Weekly Digest</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Receive a weekly summary of your home activity</p>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <button type="button" class="toggle-btn w-12 h-7 bg-blue-600 rounded-full relative transition-all flex items-center" data-name="weekly_digest" data-checked="true" onclick="toggleButton(event)">
                        <div class="w-5 h-5 bg-white rounded-full absolute left-1 transition-transform"></div>
                    </button>
                </div>
            </div>
        </form>

        <div class="pt-6 border-t border-gray-200 dark:border-gray-700 mt-6">
            <button type="button" onclick="savePreferences()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold text-sm">
                <i class="fas fa-save mr-1"></i>Save All Preferences
            </button>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="bg-red-50 dark:bg-red-900/20 border-2 border-red-200 dark:border-red-700 rounded-xl p-6">
        <h3 class="text-lg font-bold text-red-900 dark:text-red-200 mb-3">
            <i class="fas fa-exclamation-triangle mr-2"></i>Danger Zone
        </h3>
        <p class="text-sm text-red-800 dark:text-red-200 mb-4">
            These actions cannot be undone. Please proceed with caution.
        </p>
        <button class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold text-sm" onclick="if(confirm('Are you sure you want to delete your account? This cannot be undone.')) { alert('Account deletion coming soon'); }">
            <i class="fas fa-trash mr-2"></i>Delete Account
        </button>
    </div>
</div>


<script>
// Toggle Button Function
function toggleButton(event) {
    event.preventDefault();
    const btn = event.target.closest('.toggle-btn');
    
    if (!btn) return;
    
    const isChecked = btn.dataset.checked === 'true';
    const newState = !isChecked;
    
    // Update button state
    btn.dataset.checked = newState;
    
    if (newState) {
        btn.classList.remove('bg-gray-300', 'dark:bg-gray-600');
        btn.classList.add('bg-blue-600');
        btn.querySelector('div').style.transform = 'translateX(20px)';
    } else {
        btn.classList.remove('bg-blue-600');
        btn.classList.add('bg-gray-300', 'dark:bg-gray-600');
        btn.querySelector('div').style.transform = 'translateX(0)';
    }
}

// Dark Mode Toggle Function
function toggleDarkMode(event) {
    event.preventDefault();
    const btn = event.target.closest('.toggle-btn');
    
    if (!btn) return;
    
    const isChecked = btn.dataset.checked === 'true';
    const newState = !isChecked;
    const html = document.documentElement;
    
    // Update button state
    btn.dataset.checked = newState;
    
    if (newState) {
        btn.classList.remove('bg-gray-300', 'dark:bg-gray-600');
        btn.classList.add('bg-blue-600');
        btn.querySelector('div').style.transform = 'translateX(20px)';
        html.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        btn.classList.remove('bg-blue-600');
        btn.classList.add('bg-gray-300', 'dark:bg-gray-600');
        btn.querySelector('div').style.transform = 'translateX(0)';
        html.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
}

// Load dark mode preference on page load
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    const darkModeBtn = document.querySelector('[data-name="dark_mode"]');
    
    if (savedTheme === 'dark') {
        document.documentElement.classList.add('dark');
        darkModeBtn.dataset.checked = 'true';
        darkModeBtn.classList.remove('bg-gray-300', 'dark:bg-gray-600');
        darkModeBtn.classList.add('bg-blue-600');
        darkModeBtn.querySelector('div').style.transform = 'translateX(20px)';
    } else {
        document.documentElement.classList.remove('dark');
        darkModeBtn.dataset.checked = 'false';
    }
});

// Save All Preferences
function savePreferences() {
    const buttons = document.querySelectorAll('.toggle-btn');
    const preferences = {};
    
    buttons.forEach(btn => {
        const name = btn.dataset.name;
        const checked = btn.dataset.checked === 'true';
        preferences[name] = checked;
    });
    
    // Save to localStorage
    localStorage.setItem('userPreferences', JSON.stringify(preferences));
    
    // Show success message
    const btn = event.target;
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check mr-1"></i>Saved!';
    btn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
    btn.classList.add('bg-green-600');
    
    setTimeout(() => {
        btn.innerHTML = originalHTML;
        btn.classList.remove('bg-green-600');
        btn.classList.add('bg-blue-600', 'hover:bg-blue-700');
    }, 2000);
}

// Load preferences on page load
document.addEventListener('DOMContentLoaded', function() {
    const savedPrefs = localStorage.getItem('userPreferences');
    if (savedPrefs) {
        const prefs = JSON.parse(savedPrefs);
        const buttons = document.querySelectorAll('.toggle-btn');
        
        buttons.forEach(btn => {
            const name = btn.dataset.name;
            if (name in prefs) {
                const isChecked = prefs[name];
                btn.dataset.checked = isChecked;
                
                if (isChecked) {
                    btn.classList.remove('bg-gray-300', 'dark:bg-gray-600');
                    btn.classList.add('bg-blue-600');
                    btn.querySelector('div').style.transform = 'translateX(20px)';
                } else {
                    btn.classList.remove('bg-blue-600');
                    btn.classList.add('bg-gray-300', 'dark:bg-gray-600');
                    btn.querySelector('div').style.transform = 'translateX(0)';
                }
            }
        });
    }
});
</script>


@endsection