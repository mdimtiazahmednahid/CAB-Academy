<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">

    <title>{{ \App\Models\Setting::getVal('site_name', 'EdTech Platform') }} - Admin</title>
    <link rel="icon" href="{{ \App\Models\Setting::getVal('site_logo') }}">

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
<body class="font-sans antialiased bg-slate-50 text-gray-900 md:pl-64">

    <!-- Mobile Top Bar -->
    <header class="md:hidden flex items-center justify-between p-4 bg-gray-900 text-white shadow-sm z-10 sticky top-0">
        <a href="{{ url('/') }}" class="flex items-center gap-2.5">
            <img src="{{ \App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png') }}" alt="CAB Academy" class="h-8 w-8 rounded-full object-contain bg-white border border-gray-100" onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}';">
            <h1 class="font-bold text-lg tracking-tight uppercase truncate">{{ \App\Models\Setting::getVal('site_name', 'CAB Academy') }}</h1>
        </a>
        <!-- Mobile Menu Button (Alpine could be used here to toggle sidebar) -->
        <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="p-2 text-gray-400 hover:text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
    </header>

    <!-- Desktop Sidebar -->
    <aside class="hidden md:flex flex-col w-64 h-screen fixed top-0 left-0 bg-gray-900 text-white border-r border-gray-800 z-20">
        <div class="p-6 border-b border-gray-800 flex items-center gap-3">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                <img src="{{ \App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png') }}" alt="CAB Academy" class="h-8 w-8 rounded-full object-contain bg-white border border-gray-100" onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}';">
                <span class="font-bold text-lg tracking-tight uppercase truncate">{{ \App\Models\Setting::getVal('site_name', 'CAB Academy') }}</span>
            </a>
        </div>
        <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Dashboard
            </a>
            
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.users') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('admin.users*') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                Users
            </a>
            @endif
            
            <a href="{{ route('admin.courses') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('admin.courses*') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition-colors">
                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <span class="hidden lg:block">Courses</span>
            </a>

            <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('admin.categories*') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition-colors">
                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                <span class="hidden lg:block">Categories</span>
            </a>

            <a href="{{ route('admin.instructors.index') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('admin.instructors*') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition-colors">
                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <span class="hidden lg:block">Instructors</span>
            </a>

            <a href="{{ route('admin.inquiries.index') }}" class="flex items-center justify-between p-3 rounded-xl {{ request()->routeIs('admin.inquiries*') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition-colors">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    <span class="hidden lg:block">Inquiries</span>
                </div>
                @php $pendingInqCount = \App\Models\ContactInquiry::where('status', 'pending')->count(); @endphp
                @if($pendingInqCount > 0)
                    <span class="hidden lg:inline-flex px-2 py-0.5 text-[10px] font-bold bg-orange-500 text-white rounded-full">{{ $pendingInqCount }}</span>
                @endif
            </a>

            <a href="{{ route('admin.feeds.index') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('admin.feeds*') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition-colors">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                <span class="hidden lg:block">Community Feed</span>
            </a>

            <a href="{{ route('admin.blogs.index') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('admin.blogs*') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition-colors">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                <span class="hidden lg:block">Articles & Blogs</span>
            </a>
            
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.jobs') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('admin.jobs*') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition-colors">
                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                <span class="hidden lg:block">Jobs</span>
            </a>

            <a href="{{ route('admin.accounting.index') }}" class="flex items-center justify-between p-3 rounded-xl {{ request()->routeIs('admin.accounting*') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition-colors">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="hidden lg:block">Accounting & Ledger</span>
                </div>
                @php $pendingPayCount = \App\Models\Payment::where('status', 'pending')->count(); @endphp
                @if($pendingPayCount > 0)
                    <span class="hidden lg:inline-flex px-2 py-0.5 text-[10px] font-bold bg-amber-500 text-white rounded-full">{{ $pendingPayCount }}</span>
                @endif
            </a>

            <a href="{{ route('admin.payment-methods.index') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('admin.payment-methods*') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition-colors">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                <span class="hidden lg:block">Payment Methods</span>
            </a>

            <a href="{{ route('admin.payments.index') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('admin.payments*') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition-colors">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                <span class="hidden lg:block">Pending Payments</span>
            </a>

            <a href="{{ route('admin.frontend.index') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('admin.frontend.*') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition-colors">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                <span class="hidden lg:block">Frontend</span>
            </a>

            <a href="{{ route('admin.announcements.create') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('admin.announcements*') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition-colors">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                <span class="hidden lg:block">Announcements</span>
            </a>

            <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('admin.settings*') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition-colors">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span class="hidden lg:block">Settings</span>
            </a>

            <a href="{{ route('admin.trash.index') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('admin.trash*') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition-colors">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                <span class="hidden lg:block">Trash Bin</span>
            </a>
            @endif

            <form method="POST" action="{{ route('logout') }}" class="mt-4 pt-4 border-t border-gray-800">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 p-3 rounded-xl text-gray-400 hover:bg-gray-800 hover:text-white transition-colors">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span class="hidden lg:block">Log Out</span>
                </button>
            </form>
        </nav>
    </aside>

    <!-- Mobile Sidebar Toggle Menu -->
    <div id="mobile-menu" class="hidden md:hidden fixed inset-0 bg-gray-900 bg-opacity-75 z-40">
        <div class="w-64 bg-gray-900 h-full p-4 flex flex-col space-y-2">
            <div class="flex justify-between items-center mb-4">
                <span class="text-white font-bold text-xl">Menu</span>
                <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="block text-gray-300 hover:text-white py-2">Dashboard</a>
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.users') }}" class="block text-gray-300 hover:text-white py-2">Users</a>
            @endif
            <a href="{{ route('admin.courses') }}" class="block text-gray-300 hover:text-white py-2">Courses</a>
            <a href="{{ route('admin.categories.index') }}" class="block text-gray-300 hover:text-white py-2">Categories</a>
            <a href="{{ route('admin.instructors.index') }}" class="block text-gray-300 hover:text-white py-2">Instructors</a>
            <a href="{{ route('admin.inquiries.index') }}" class="block text-gray-300 hover:text-white py-2">Inquiries</a>
            <a href="{{ route('admin.feeds.index') }}" class="block text-gray-300 hover:text-white py-2">Community Feed</a>
            <a href="{{ route('admin.blogs.index') }}" class="block text-gray-300 hover:text-white py-2">Articles & Blogs</a>
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.jobs') }}" class="block text-gray-300 hover:text-white py-2">Jobs</a>
            <a href="{{ route('admin.accounting.index') }}" class="block text-gray-300 hover:text-white py-2">Accounting & Ledger</a>
            <a href="{{ route('admin.payment-methods.index') }}" class="block text-gray-300 hover:text-white py-2">Payment Methods</a>
            <a href="{{ route('admin.payments.index') }}" class="block text-gray-300 hover:text-white py-2">Pending Payments</a>
            <a href="{{ route('admin.frontend.index') }}" class="block text-gray-300 hover:text-white py-2">Frontend</a>
            <a href="{{ route('admin.settings') }}" class="block text-gray-300 hover:text-white py-2 {{ request()->routeIs('admin.settings') ? 'font-bold text-white' : '' }}">Settings</a>
            <a href="{{ route('admin.announcements.create') }}" class="block text-gray-300 hover:text-white py-2 {{ request()->routeIs('admin.announcements.*') ? 'font-bold text-white' : '' }}">Announcements</a>
            <a href="{{ route('admin.trash.index') }}" class="block text-gray-300 hover:text-white py-2 text-red-400">Trash Bin</a>
            @endif
            <div class="mt-auto border-t border-gray-800 pt-2">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left block text-red-400 hover:text-red-300 py-2">Log Out</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="w-full max-w-7xl mx-auto p-4 md:p-8">
        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 text-red-800 rounded-xl border border-red-200 shadow-sm">
                <div class="flex items-center gap-2 mb-2 font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    There were some errors with your submission:
                </div>
                <ul class="list-disc pl-5 text-sm space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        @yield('content')
        
        <footer class="mt-12 pt-6 border-t border-gray-200 text-center text-sm text-gray-500 pb-8">
            Developed by <a href="https://neurasoft.top" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline font-semibold transition-colors">NeuraSoft</a>
        </footer>
    </main>

</body>
</html>
