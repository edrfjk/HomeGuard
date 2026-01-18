<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'HomeGuard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="hidden lg:flex w-64 bg-gradient-to-b from-blue-600 to-blue-800 text-white flex-col shadow-2xl fixed h-screen">
            <!-- Logo -->
            <div class="p-6 border-b border-blue-500">
                <div class="flex items-center justify-center space-x-3">
                    <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center animate-pulse-glow">
                        <i class="fas fa-home text-blue-600 text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">HomeGuard</h1>
                        <p class="text-xs text-blue-200">IoT Safety System</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 px-4 py-8 space-y-2 overflow-y-auto">
                <a href="/dashboard" 
                   class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ request()->is('dashboard') ? 'bg-blue-500 shadow-lg' : 'hover:bg-blue-700' }}">
                    <i class="fas fa-chart-line w-5"></i>
                    <span class="font-semibold">Dashboard</span>
                </a>

                <a href="/devices" 
                   class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ request()->is('devices*') ? 'bg-blue-500 shadow-lg' : 'hover:bg-blue-700' }}">
                    <i class="fas fa-cctv w-5"></i>
                    <span class="font-semibold">My Devices</span>
                    <span class="ml-auto text-xs bg-blue-400 px-2 py-1 rounded-full">{{ auth()->user()->devices()->count() }}</span>
                </a>

                <a href="/alerts" 
                   class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ request()->is('alerts*') ? 'bg-blue-500 shadow-lg' : 'hover:bg-blue-700' }}">
                    <i class="fas fa-bell w-5"></i>
                    <span class="font-semibold">Alerts</span>
                    @if(auth()->user()->unreadCriticalAlerts() > 0)
                        <span class="ml-auto bg-red-500 px-2 py-1 rounded-full text-xs font-bold animate-pulse">
                            {{ auth()->user()->unreadCriticalAlerts() }}
                        </span>
                    @endif
                </a>

                <hr class="border-blue-500 my-4">

                <a href="/profile" 
                   class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg transition hover:bg-blue-700">
                    <i class="fas fa-user-circle w-5"></i>
                    <span class="font-semibold">Profile</span>
                </a>

                <a href="/settings" 
                   class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg transition hover:bg-blue-700">
                    <i class="fas fa-cog w-5"></i>
                    <span class="font-semibold">Settings</span>
                </a>
            </nav>

            <!-- User Section -->
            <div class="p-4 border-t border-blue-500">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 bg-blue-300 rounded-full flex items-center justify-center font-bold text-blue-700">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="text-sm flex-1">
                        <p class="font-semibold truncate">{{ auth()->user()->name }}</p>
                        <p class="text-blue-200 text-xs truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>

                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm font-semibold">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 lg:ml-64 flex flex-col">
            <!-- Top Bar -->
            <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-30 shadow-sm">
                <div class="px-6 py-4 flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                            @yield('page-title', 'Dashboard')
                        </h1>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            @yield('page-subtitle', '')
                        </p>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Notifications Bell -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="relative p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-bell text-xl"></i>
                                @if(auth()->user()->alerts()->where('status', 'active')->count() > 0)
                                    <span class="absolute top-0 right-0 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center animate-pulse font-bold">
                                        {{ auth()->user()->alerts()->where('status', 'active')->count() }}
                                    </span>
                                @endif
                            </button>

                            <!-- Notification Dropdown -->
                            <div x-show="open" 
                                @click.outside="open = false"
                                class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-2xl z-50 max-h-96 overflow-y-auto">
                                
                                <div class="sticky top-0 bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4 border-b border-blue-500">
                                    <h3 class="font-bold text-lg">
                                        <i class="fas fa-bell mr-2"></i>Notifications
                                    </h3>
                                    <p class="text-xs text-blue-200">{{ auth()->user()->alerts()->where('status', 'active')->count() }} active alerts</p>
                                </div>

                                @if(auth()->user()->alerts()->where('status', 'active')->count() > 0)
                                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach(auth()->user()->alerts()->where('status', 'active')->latest()->take(5)->get() as $alert)
                                            <a href="/alerts/{{ $alert->id }}" 
                                            class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition border-l-4 {{ 
                                                $alert->severity === 'critical' ? 'border-red-500' : 
                                                ($alert->severity === 'warning' ? 'border-yellow-500' : 'border-blue-500')
                                            }}">
                                                <div class="flex items-start gap-3">
                                                    <span class="text-xl">{{ $alert->getSeverityEmoji() }}</span>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="font-semibold text-gray-900 dark:text-white text-sm">{{ $alert->device->name }}</p>
                                                        <p class="text-xs text-gray-600 dark:text-gray-400 truncate">{{ $alert->message }}</p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">{{ $alert->created_at->diffForHumans() }}</p>
                                                    </div>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>

                                    <div class="border-t border-gray-200 dark:border-gray-700 p-3">
                                        <a href="/alerts" class="block text-center text-sm font-semibold text-blue-600 hover:text-blue-700">
                                            View All Alerts <i class="fas fa-arrow-right ml-1"></i>
                                        </a>
                                    </div>
                                @else
                                    <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-check-circle text-3xl text-green-500 mb-2 block"></i>
                                        <p class="text-sm">No active alerts</p>
                                    </div>
                                @endif
                            </div>
                        </div>


            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <!-- Flash Messages -->
                    @if($message = Session::get('success'))
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center space-x-3 animate-fadeIn"
                             x-data="{ show: true }"
                             x-show="show"
                             @click="show = false"
                             style="cursor: pointer;">
                            <i class="fas fa-check-circle text-xl"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg animate-fadeIn">
                            <div class="flex items-center space-x-3 mb-2">
                                <i class="fas fa-exclamation-circle text-xl"></i>
                                <span class="font-semibold">Errors occurred:</span>
                            </div>
                            <ul class="list-disc list-inside space-y-1 ml-8">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Content Slot -->
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Alpine.js for interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Auto-hide alerts -->
    <script>
        setTimeout(() => {
            const alerts = document.querySelectorAll('[x-show]');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.remove();
                }, 5000);
            });
        }, 100);
    </script>

    @yield('scripts')
</body>
</html>