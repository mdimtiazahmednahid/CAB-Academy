<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ \App\Models\Setting::getVal('site_name', 'EdTech Platform') }} - Admin</title>

    <!-- Tailwind CSS & Alpine.js -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary-color: {{ \App\Models\Setting::getVal('primary_color', '#1F6F54') }};
        }
        .bg-primary { background-color: var(--primary-color); }
        .text-primary { color: var(--primary-color); }
        .border-primary { border-color: var(--primary-color); }
        .focus-ring-primary:focus-within { outline: 2px solid var(--primary-color); outline-offset: 2px; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900 pb-20 md:pb-0 md:pl-64">

    <!-- Mobile Top Bar -->
    <header class="md:hidden flex items-center justify-between p-4 bg-white shadow-sm border-b border-gray-100 z-10 sticky top-0">
        <div class="flex items-center gap-3">
            @if(\App\Models\Setting::getVal('site_logo'))
                <img src="{{ \App\Models\Setting::getVal('site_logo') }}" alt="Logo" class="h-8 w-auto rounded-md object-contain">
            @endif
            <h1 class="font-semibold text-lg truncate">{{ \App\Models\Setting::getVal('site_name', 'EdTech Admin') }}</h1>
        </div>
    </header>

    <!-- Desktop Sidebar (Hidden on Mobile) -->
    <aside class="hidden md:flex flex-col w-64 h-screen fixed top-0 left-0 bg-white border-r border-gray-100 z-20">
        <div class="p-6 border-b border-gray-100 flex items-center gap-3">
            @if(\App\Models\Setting::getVal('site_logo'))
                <img src="{{ \App\Models\Setting::getVal('site_logo') }}" alt="Logo" class="h-8 w-auto rounded-md object-contain">
            @endif
            <span class="font-bold text-xl truncate">{{ \App\Models\Setting::getVal('site_name', 'Admin') }}</span>
        </div>
        <nav class="flex-1 p-4 space-y-2">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-primary/10 text-primary font-medium' : 'text-gray-500 hover:bg-gray-50' }} transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Dashboard
            </a>
            
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.users') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('admin.users*') ? 'bg-primary/10 text-primary font-medium' : 'text-gray-500 hover:bg-gray-50' }} transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                Users
            </a>
            @endif
            
            <a href="{{ route('admin.courses') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('admin.courses*') ? 'bg-primary/10 text-primary font-medium' : 'text-gray-500 hover:bg-gray-50' }} transition-colors">
                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <span class="hidden lg:block">Courses</span>
            </a>
            
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.jobs') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('admin.jobs*') ? 'bg-primary/10 text-primary font-medium' : 'text-gray-500 hover:bg-gray-50' }} transition-colors">
                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                <span class="hidden lg:block">Jobs</span>
            </a>

            <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('admin.settings*') ? 'bg-primary/10 text-primary font-medium' : 'text-gray-500 hover:bg-gray-50' }} transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Settings
            </a>
            @endif
        </nav>
    </aside>

    <!-- Mobile Bottom Navigation -->
    <nav class="md:hidden fixed bottom-0 w-full bg-white border-t border-gray-200 flex justify-around p-3 z-50 safe-area-bottom">
        <a href="#" class="flex flex-col items-center text-gray-500 hover:text-primary p-2">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span class="text-xs font-medium">Home</span>
        </a>
        <a href="#" class="flex flex-col items-center text-gray-500 hover:text-primary p-2">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            <span class="text-xs font-medium">Users</span>
        </a>
        <a href="{{ route('admin.settings') }}" class="flex flex-col items-center text-primary p-2">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            <span class="text-xs font-medium">Settings</span>
        </a>
    </nav>

    <!-- Main Content -->
    <main class="w-full max-w-5xl mx-auto p-4 md:p-8">
        @yield('content')
    </main>

</body>
</html>
