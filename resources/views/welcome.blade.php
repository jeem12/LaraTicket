<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://widget.rss.app/v1/wall.js" type="text/javascript" async></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] } } }
        }
    </script>
</head>
<body class="h-full bg-zinc-950 text-zinc-100 antialiased flex flex-col">

    <!-- Navbar -->
    <nav class="border-b border-zinc-800/80 bg-zinc-900/90 backdrop-blur-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3 group">
                <div class="p-2 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
                    </svg>
                </div>
                <span class="font-bold text-sm tracking-wide text-white">{{ config('app.name') }}</span>
            </div>

            @if (Route::has('login'))
                <div class="space-x-4 flex items-center">
                    @auth
                        @php
                            $dashboardRoute = auth()->user()->role === 'admin' ? 'admin.dashboard' : 'user.dashboard';
                        @endphp
                        <a href="{{ route($dashboardRoute) }}" class="px-4 py-2 bg-zinc-900 border border-zinc-700/80 text-zinc-200 rounded-xl text-sm font-medium hover:bg-zinc-800 hover:text-white transition-all shadow-sm" wire:navigate>Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-zinc-400 hover:text-white transition-colors">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-4 py-2 bg-cyan-500 text-zinc-950 font-semibold rounded-xl text-sm hover:bg-cyan-400 transition-all shadow-lg shadow-cyan-500/20">Get Started</a>
                        @endif
                    @endauth
                </div>
            @endif
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-1 w-full">
        <div class="mb-12 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-xs font-semibold mb-4 tracking-wider uppercase">
                Enterprise Support Infrastructure
            </div>
            <h1 class="text-4xl font-extrabold tracking-tight mb-4 text-white">Centralized Ticketing Support</h1>
            <p class="text-zinc-400 max-w-2xl mx-auto text-sm leading-relaxed">Manage your customer inquiries with high performance. A production-ready architecture styled for modern engineering teams.</p>
        </div>

        <!-- News Widget Container -->
        <div class="bg-zinc-900/90 backdrop-blur-xl rounded-2xl border border-zinc-800/80 shadow-xl flex flex-col overflow-hidden">
            <div class="px-6 py-5 border-b border-zinc-800/80 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-cyan-400"></div>
                    <h2 class="font-bold text-sm text-white tracking-wide">Industry News & Trends</h2>
                </div>
                <span class="text-[10px] text-zinc-500 uppercase tracking-wider font-medium">Live Feed</span>
            </div>

            <div class="w-full h-[270px]">
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