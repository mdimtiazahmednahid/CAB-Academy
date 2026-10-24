<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">

    <title>@hasSection('title') @yield('title') - @endif{{ \App\Models\Setting::getVal('site_name', 'CAB Academy') }}</title>
    <link rel="icon" href="{{ \App\Models\Setting::getVal('site_logo') }}">

    <!-- Tailwind CSS & Alpine.js -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary-color: {{ \App\Models\Setting::getVal('primary_color', '#051c45') }};
        }
        .bg-primary { background-color: var(--primary-color); }
        .text-primary { color: var(--primary-color); }
        .border-primary { border-color: var(--primary-color); }
        .focus-ring-primary:focus-within { outline: 2px solid var(--primary-color); outline-offset: 2px; }
        
        /* Glassmorphism fallbacks */
        .glass-panel {
            background-color: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-top: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        /* Mobile Active Tab Styling */
        .tab-active {
            color: var(--primary-color);
            position: relative;
        }
        .tab-active::after {
            content: '';
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            width: 32px;
            height: 3px;
            background-color: var(--primary-color);
            border-radius: 0 0 4px 4px;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900 pb-20 md:pb-0 md:pl-20 lg:pl-64">

    <!-- Desktop Sidebar (Progressive Enhancement) -->
    <aside class="hidden md:flex flex-col w-20 lg:w-64 h-screen fixed top-0 left-0 bg-white border-r border-gray-100 z-20">
        <!-- Logo -->
        <div class="p-4 lg:p-6 border-b border-gray-100 flex items-center justify-center lg:justify-start gap-3 h-16">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                <img src="{{ \App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png') }}" alt="CAB Academy" class="h-8 w-8 rounded-full object-contain bg-white border border-gray-100">
                <span class="hidden lg:block font-black text-lg tracking-tight text-[#0a2540] uppercase truncate">{{ \App\Models\Setting::getVal('site_name', 'CAB Academy') }}</span>
            </a>
        </div>
        
        <!-- Nav -->
        <nav class="flex-1 p-3 lg:p-4 space-y-2 overflow-y-auto">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('dashboard') ? 'bg-green-50 text-primary font-medium' : 'text-gray-500 hover:bg-gray-50' }} transition-colors">
                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span class="hidden lg:block">Home</span>
            </a>
            <a href="{{ route('feed.index') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('feed.*') ? 'bg-green-50 text-primary font-medium' : 'text-gray-500 hover:bg-gray-50' }} transition-colors">
                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                <span class="hidden lg:block">Feed & Blogs</span>
            </a>
            <a href="{{ route('performance.index') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('performance.index') ? 'bg-green-50 text-primary font-medium' : 'text-gray-500 hover:bg-gray-50' }} transition-colors">
                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                <span class="hidden lg:block">Performance</span>
            </a>
            <a href="{{ route('jobs.index') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('jobs.*') ? 'bg-green-50 text-primary font-medium' : 'text-gray-500 hover:bg-gray-50' }} transition-colors">
                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                <span class="hidden lg:block">Careers</span>
            </a>

            <a href="{{ route('wishlist.index') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('wishlist.*') ? 'bg-green-50 text-primary font-medium' : 'text-gray-500 hover:bg-gray-50' }} transition-colors">
                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                <span class="hidden lg:block">Saved Wishlist</span>
            </a>
            
            <a href="{{ route('notifications.index') }}" class="flex items-center p-3 rounded-xl {{ request()->routeIs('notifications.*') ? 'bg-green-50 text-primary font-medium' : 'text-gray-500 hover:bg-gray-50' }} transition-colors">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="absolute -top-1 -right-1 flex items-center justify-center w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full border border-white">
                                {{ auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </div>
                    <span class="hidden lg:block">Alerts</span>
                </div>
            </a>

            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('profile.edit') ? 'bg-green-50 text-primary font-medium' : 'text-gray-500 hover:bg-gray-50' }} transition-colors">
                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                <span class="hidden lg:block">Profile</span>
            </a>
        </nav>
    </aside>

    <!-- Mobile Top Bar (Optional depending on screen) -->
    <header class="md:hidden flex items-center justify-between px-5 py-4 bg-gray-50 z-10 sticky top-0">
        <h1 class="font-bold text-2xl tracking-tight text-gray-900">@yield('header_title', 'Dashboard')</h1>
        
        <!-- Quick Actions (e.g. Notifications) -->
        <a href="{{ route('notifications.index') }}" class="relative p-2 -mr-2 text-gray-500 hover:bg-gray-200 rounded-full transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            @if(auth()->user()->unreadNotifications->count() > 0)
                <span class="absolute top-1 right-1 flex items-center justify-center w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full border border-white">
                    {{ auth()->user()->unreadNotifications->count() }}
                </span>
            @endif
        </a>
    </header>

    <!-- Main Content Area -->
    <main class="w-full max-w-5xl mx-auto px-4 md:px-8 pb-8 md:pt-8 min-h-screen">
        <!-- Gamification Bar -->
        @auth
            @php
                $user = auth()->user();
                $level = $user->level;
                $currentXp = $user->xp;
                $nextLevelXp = $user->next_level_xp;
                $prevLevelXp = ($level > 1) ? (($level - 1) * $level / 2) * 100 : 0;
                $progress = ($nextLevelXp - $prevLevelXp > 0) ? (($currentXp - $prevLevelXp) / ($nextLevelXp - $prevLevelXp)) * 100 : 0;
            @endphp
            <div class="bg-gradient-to-r from-[#031330] via-[#051c45] to-[#0a2540] border border-[#1e2d4a] rounded-2xl p-4 text-white shadow-xl mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4 w-full md:w-auto flex-1">
                    <div class="w-12 h-12 rounded-full bg-[#c30102] flex items-center justify-center font-bold text-xl border-2 border-white/40 shrink-0 shadow-md">
                        {{ $level }}
                    </div>
                    <div class="flex-1 max-w-sm">
                        <div class="flex justify-between text-sm font-medium mb-1">
                            <span>Level {{ $level }}</span>
                            <span class="opacity-90">{{ $currentXp }} / {{ $nextLevelXp }} XP</span>
                        </div>
                        <div class="w-full bg-black/20 rounded-full h-2">
                            <div class="bg-white h-2 rounded-full transition-all duration-500 shadow-[0_0_10px_rgba(255,255,255,0.5)]" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-6 md:justify-end w-full md:w-auto">
                    <div class="flex flex-col items-center">
                        <span class="text-2xl" title="Daily Streak">🔥</span>
                        <span class="text-xs font-bold mt-1 bg-black/20 px-2 py-0.5 rounded-full">{{ $user->current_streak }} Day{{ $user->current_streak !== 1 ? 's' : '' }}</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <span class="text-2xl" title="Total XP">✨</span>
                        <span class="text-xs font-bold mt-1 bg-black/20 px-2 py-0.5 rounded-full">{{ $user->xp }} XP</span>
                    </div>
                </div>
            </div>
        @endauth

        @yield('content')
        
        <footer class="mt-12 pt-6 border-t border-gray-200 text-center text-sm text-gray-500 pb-16 md:pb-4">
            Developed by <a href="https://neurasoft.top" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline font-semibold transition-colors">NeuraSoft</a>
        </footer>
    </main>

    <!-- Mobile Bottom Navigation (Principle #4) -->
    <nav class="md:hidden fixed bottom-0 w-full glass-panel flex justify-around p-2 pb-safe z-50 shadow-[0_-4px_20px_rgba(0,0,0,0.03)]">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center p-2 w-16 {{ request()->routeIs('dashboard') ? 'tab-active' : 'text-gray-400 hover:text-gray-600' }} transition-colors">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span class="text-[10px] font-semibold tracking-wide">Home</span>
        </a>
        
        <a href="{{ route('feed.index') }}" class="flex flex-col items-center justify-center p-2 w-16 {{ request()->routeIs('feed.*') ? 'tab-active' : 'text-gray-400 hover:text-gray-600' }} transition-colors">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
            <span class="text-[10px] font-semibold tracking-wide">Feed</span>
        </a>
        
        <a href="{{ route('performance.index') }}" class="flex flex-col items-center justify-center p-2 w-16 {{ request()->routeIs('performance.index') ? 'tab-active' : 'text-gray-400 hover:text-gray-600' }} transition-colors">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            <span class="text-[10px] font-semibold tracking-wide">Stats</span>
        </a>

        <a href="{{ route('jobs.index') }}" class="flex flex-col items-center justify-center p-2 w-16 {{ request()->routeIs('jobs.index') ? 'tab-active' : 'text-gray-400 hover:text-gray-600' }} transition-colors">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            <span class="text-[10px] font-semibold tracking-wide">Careers</span>
        </a>
        
        <a href="{{ route('profile.edit') }}" class="flex flex-col items-center justify-center p-2 w-16 {{ request()->routeIs('profile.edit') ? 'tab-active' : 'text-gray-400 hover:text-gray-600' }} transition-colors">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            <span class="text-[10px] font-semibold tracking-wide">Profile</span>
        </a>
    </nav>
    
    <style>
        /* Safe area padding for modern iPhones with home indicator */
        .pb-safe { padding-bottom: max(env(safe-area-inset-bottom), 0.5rem); }
    </style>
</body>
</html>
