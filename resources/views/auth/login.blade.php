<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HomeGuard</title>
    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-600 via-blue-700 to-blue-900 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md animate-slideUp">
        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-blue-800 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <i class="fas fa-home text-white text-2xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900">HomeGuard</h1>
                <p class="text-gray-600 text-sm mt-2">Smart Home Safety System</p>
            </div>

            <!-- Errors -->
            @if($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    @foreach($errors->all() as $error)
                        <p><i class="fas fa-exclamation-circle mr-2"></i>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="/login" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2 text-blue-600"></i>Email Address
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 transition"
                           placeholder="you@example.com"
                           required>
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2 text-blue-600"></i>Password
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 transition"
                           placeholder="••••••••"
                           required>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember" class="w-4 h-4 text-blue-600 rounded">
                    <label for="remember" class="ml-2 text-sm text-gray-700">Remember me</label>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 rounded-lg font-bold hover:from-blue-700 hover:to-blue-800 transition shadow-lg transform hover:scale-105">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login to HomeGuard
                </button>
            </form>

            <!-- Register Link -->
            <div class="mt-6 text-center border-t pt-6">
                <p class="text-gray-600 text-sm">
                    Don't have an account?
                    <a href="/register" class="text-blue-600 font-bold hover:underline">Register here</a>
                </p>
            </div>

            <!-- Test Credentials -->
            <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg text-xs text-gray-700">
                <p class="font-semibold text-blue-900 mb-2"><i class="fas fa-info-circle mr-1"></i>Test Credentials:</p>
                <p>📧 Email: <code class="bg-white px-2 py-1 rounded">test@example.com</code></p>
                <p>🔑 Password: <code class="bg-white px-2 py-1 rounded">password</code></p>
            </div>
        </div>
    </div>

    <style>
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-slideUp {
            animation: slideUp 0.5s ease-out;
        }
    </style>
</body>
</html>