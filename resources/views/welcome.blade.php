<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ticketing System</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://widget.rss.app/v1/wall.js" type="text/javascript" async></script>
    <script>
        tailwind.config = {
            darkMode: 'media',
            theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] } } }
        }
    </script>
</head>
<body class="h-full bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 antialiased">

    <!-- Navbar -->
    <nav class="border-b border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="font-bold text-xl tracking-tight text-blue-600">{{ config('app.name') }}</div>
            @if (Route::has('login'))
                <div class="space-x-4">
                    @auth
                        <a href="{{ route('user.dashboard') }}" class="text-sm font-medium hover:text-blue-600 transition">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium hover:text-blue-600 transition">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">Get Started</a>
                        @endif
                    @endauth
                </div>
            @endif
        </div>
    </nav>

    <!-- Hero Section -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
        <div class="mb-12 text-center">
            <h1 class="text-4xl font-extrabold tracking-tight mb-4">Centralized Ticketing Support</h1>
            <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">Manage your customer inquiries with efficiency. A professional system built for high-performance teams.</p>
        </div>

        <!-- Metrics Grid -->
        {{-- <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            @foreach([['Open', '12', 'text-yellow-600'], ['Resolved', '48', 'text-green-600'], ['Avg Response', '2h', 'text-blue-600']] as $stat)
            <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm">
                <p class="text-sm font-medium text-gray-500">{{ $stat[0] }} Tickets</p>
                <h3 class="text-3xl font-bold mt-2 {{ $stat[2] }}">{{ $stat[1] }}</h3>
            </div>
            @endforeach
        </div> --}}

            <!-- News Widget Container -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm flex flex-col">
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800">
                    <h2 class="font-bold text-gray-900 dark:text-white">Industry News & Trends</h2>
                </div>

                <!-- We apply clip-path to match the border-radius of the card -->
                <div class="w-full h-[270px]" style="clip-path: inset(0 round 0 0 1rem 1rem);">
                    <iframe 
                        src="https://rss.app/embed/v1/carousel/tWQuFbtCu2fZH8L0" 
                        frameborder="0" 
                        class="w-full h-full"
                        style="display: block;">
                    </iframe>
                </div>
            </div>

    </main>

</body>
</html>