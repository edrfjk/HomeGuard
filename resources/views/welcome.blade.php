<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HomeGuard - Smart Home Safety</title>
    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-600 via-blue-700 to-blue-900">
    <div class="min-h-screen flex items-center justify-center">
        <div class="text-center text-white px-4">
            <div class="mb-8 animate-fadeIn">
                <i class="fas fa-home text-6xl mb-4 block"></i>
                <h1 class="text-5xl font-bold mb-4">HomeGuard</h1>
                <p class="text-xl text-blue-200">Smart Home Safety & Monitoring System</p>
                <p class="text-blue-300 mt-2">Real-time monitoring with ESP32-CAM and environmental sensors</p>
            </div>

            <div class="space-x-4 mt-8">
                <a href="/login" class="inline-block px-8 py-3 bg-white text-blue-600 rounded-lg font-bold hover:bg-gray-100 transition shadow-lg">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                </a>
                <a href="/register" class="inline-block px-8 py-3 bg-blue-500 text-white rounded-lg font-bold hover:bg-blue-400 transition shadow-lg">
                    <i class="fas fa-user-plus mr-2"></i>Register
                </a>
            </div>

            <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6 text-left">
                <div class="bg-white/10 backdrop-blur p-6 rounded-lg">
                    <i class="fas fa-camera text-3xl mb-3"></i>
                    <h3 class="font-bold text-lg mb-2">Live Camera</h3>
                    <p class="text-blue-200">ESP32-CAM captures real-time images</p>
                </div>
                <div class="bg-white/10 backdrop-blur p-6 rounded-lg">
                    <i class="fas fa-thermometer-half text-3xl mb-3"></i>
                    <h3 class="font-bold text-lg mb-2">Smart Sensors</h3>
                    <p class="text-blue-200">Temperature, humidity & gas monitoring</p>
                </div>
                <div class="bg-white/10 backdrop-blur p-6 rounded-lg">
                    <i class="fas fa-bell text-3xl mb-3"></i>
                    <h3 class="font-bold text-lg mb-2">Instant Alerts</h3>
                    <p class="text-blue-200">Get notified of safety hazards</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>