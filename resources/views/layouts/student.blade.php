<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="@yield('robots', 'noindex, nofollow, noarchive, nosnippet')">

    <title>@hasSection('title') @yield('title') - @endif{{ \App\Models\Setting::getVal('site_name', 'ACS Academy') }}</title>
    @hasSection('meta_description')
        <meta name="description" content="@yield('meta_description')">
        <meta property="og:description" content="@yield('meta_description')">
    @endif
    @hasSection('canonical_url')
        <link rel="canonical" href="@yield('canonical_url')">
        <meta property="og:url" content="@yield('canonical_url')">
    @else
        <meta property="og:url" content="{{ url()->current() }}">
    @endif
    @hasSection('og_title')
        <meta property="og:title" content="@yield('og_title')">
    @endif
    @hasSection('og_image')
        <meta property="og:image" content="@yield('og_image')">
    @endif
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="{{ \App\Models\Setting::getVal('site_name', 'ACS Academy') }}">
    <link rel="icon" href="{{ \App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png') }}">
    <link rel="manifest" href="{{ route('pwa.manifest') }}">
    <meta name="theme-color" content="#051c45">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @yield('schema')

    <!-- Tailwind CSS & Alpine.js -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {!! acs_inline_compiled_css() !!}

    <style>
        :root {
            --primary-color: {{ \App\Models\Setting::getVal('primary_color', '#051c45') }};
        }
        {!! \App\Models\Setting::getTypographyCss() !!}
        .bg-primary { background-color: var(--primary-color); }
        .text-primary { color: var(--primary-color); }
        .border-primary { border-color: var(--primary-color); }
        .focus-ring-primary:focus-within { outline: 2px solid var(--primary-color); outline-offset: 2px; }

        .btn-ipdp-primary {
            background: linear-gradient(135deg, #c30102 0%, #9b0000 100%);
            color: #ffffff;
            transition: all 0.25s ease;
        }
        .btn-ipdp-primary:hover {
            background: linear-gradient(135deg, #a50102 0%, #7f0000 100%);
            transform: translateY(-1px);
        }
        .btn-ipdp-secondary {
            background: #ffffff;
            color: #051c45;
            border: 1px solid #cbd5e1;
            transition: all 0.25s ease;
        }
        .btn-ipdp-secondary:hover {
            background: #f8fafc;
            border-color: #051c45;
        }

        /* Liquid Glass Card & Skeleton System */
        .liquid-glass-card {
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.94) 0%, rgba(255, 255, 255, 0.82) 52%, rgba(248, 250, 252, 0.92) 100%) !important;
            backdrop-filter: blur(22px) saturate(190%);
            -webkit-backdrop-filter: blur(22px) saturate(190%);
            border: 1px solid rgba(255, 255, 255, 0.9) !important;
            box-shadow:
                0 12px 34px -8px rgba(4, 21, 54, 0.08),
                0 4px 12px -2px rgba(4, 21, 54, 0.04),
                inset 0 1.5px 0 rgba(255, 255, 255, 0.98),
                0 0 0 1px rgba(4, 21, 54, 0.06);
            transition: transform 0.32s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.32s cubic-bezier(0.22, 1, 0.36, 1), border-color 0.32s ease;
        }
        .liquid-glass-card:hover {
            border-color: rgba(195, 1, 2, 0.28) !important;
            box-shadow: 0 20px 42px -12px rgba(4, 21, 54, 0.14), 0 0 0 1px rgba(195, 1, 2, 0.18);
        }
        .liquid-glass-body {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.65) 0%, rgba(255, 255, 255, 0.9) 100%) !important;
        }
        .liquid-glass-pill {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px) saturate(180%);
            -webkit-backdrop-filter: blur(12px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.9);
        }
        @keyframes cabSkeletonShimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .skeleton-shimmer {
            background: linear-gradient(90deg, rgba(226, 232, 240, 0.72) 25%, rgba(248, 250, 252, 0.96) 50%, rgba(226, 232, 240, 0.72) 75%);
            background-size: 200% 100%;
            animation: cabSkeletonShimmer 1.45s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        }
        .img-skeleton-wrapper {
            position: relative;
            overflow: hidden;
        }
        .img-skeleton-wrapper::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(226, 232, 240, 0.75) 25%, rgba(241, 245, 249, 0.95) 50%, rgba(226, 232, 240, 0.75) 75%);
            background-size: 200% 100%;
            animation: cabSkeletonShimmer 1.45s cubic-bezier(0.4, 0, 0.2, 1) infinite;
            z-index: 0;
            transition: opacity 0.25s ease;
        }
        .img-skeleton-wrapper.is-loaded::before {
            opacity: 0;
            pointer-events: none;
            animation: none;
        }
        
        /* Glassmorphism fallbacks */
        .glass-panel {
            background-color: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-top: 1px solid rgba(226, 232, 240, 0.85);
        }
        
        /* Mobile Active Tab Styling */
        .tab-active {
            color: var(--primary-color);
            position: relative;
        }
        .tab-active::after {
            content: '';
            position: absolute;
            top: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 28px;
            height: 3px;
            background-color: #c30102;
            border-radius: 0 0 4px 4px;
        }

        /* Round full-bleed hero sections cleanly inside the student portal workspace */
        .student-portal-workspace > div > section:first-child,
        .student-portal-workspace > div > header:first-child {
            border-radius: 1.5rem;
            overflow: hidden;
            margin-bottom: 1.5rem;
            box-shadow: 0 12px 32px -10px rgba(5, 28, 69, 0.18);
        }

        .pb-safe { padding-bottom: max(env(safe-area-inset-bottom), 0.5rem); }
    </style>
</head>
@php
    $brandSizing = \App\Models\Setting::getBrandSizing();
    $portalUser = auth()->user();
    $studentWishlistCount = $portalUser ? $portalUser->wishlists()->count() : 0;
    $unreadCount = $portalUser ? $portalUser->unreadNotifications->count() : 0;
    $userLevel = $portalUser ? $portalUser->level : 1;
    $userXp = $portalUser ? $portalUser->xp : 0;
    $userStreak = $portalUser ? $portalUser->current_streak : 0;
@endphp
<body class="font-sans antialiased bg-[#f4f7fb] text-slate-900 pb-20 md:pb-0 md:pl-16 lg:pl-56">

    <!-- 1. Desktop Left Sidebar (Compact App Navigation Shell) -->
    <aside class="hidden md:flex flex-col w-16 lg:w-56 h-screen fixed top-0 left-0 bg-white border-r border-slate-200/80 z-30">
        <!-- Brand Header -->
        <div class="px-3 lg:px-4 h-14 border-b border-slate-100 flex items-center justify-center lg:justify-start gap-2.5">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 min-w-0 group" title="Student Portal">
                <img src="{{ \App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png') }}" alt="{{ $brandSizing['site_name'] ?? $brandSizing['name'] ?? 'ACS Academy' }}" class="h-8 w-8 shrink-0 rounded-lg object-contain bg-white border border-slate-200/80 p-0.5 shadow-2xs" onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}';">
                <div class="hidden lg:flex flex-col min-w-0">
                    <span class="font-black tracking-tight text-[#051c45] uppercase brand-adaptive-title leading-tight truncate text-xs" title="{{ $brandSizing['site_name'] ?? $brandSizing['name'] ?? 'ACS Academy' }}">{{ $brandSizing['site_name'] ?? $brandSizing['name'] ?? 'ACS Academy' }}</span>
                    <span class="inline-flex items-center gap-1 text-[9px] font-extrabold uppercase tracking-widest text-[#c30102]">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#10b981]"></span>
                        <span>Student Portal</span>
                    </span>
                </div>
            </a>
        </div>
        
        <!-- Direct Navigation Links (Zero Redundancy) -->
        <nav class="flex-1 p-2 lg:p-3 space-y-1 overflow-y-auto">
            <a href="{{ route('dashboard') }}" class="flex items-center justify-center lg:justify-start gap-2.5 px-2.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('dashboard') ? 'bg-[#051c45] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-[#051c45]' }}" title="Dashboard">
                <i class="fas fa-th-large w-4 text-center text-xs shrink-0 {{ request()->routeIs('dashboard') ? 'text-amber-300' : 'text-slate-400' }}"></i>
                <span class="hidden lg:block">Dashboard</span>
            </a>

            <a href="{{ route('catalog.index') }}" class="flex items-center justify-center lg:justify-start gap-2.5 px-2.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('catalog.*') || request()->routeIs('courses.*') || request()->routeIs('lessons.*') ? 'bg-[#051c45] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-[#051c45]' }}" title="Courses">
                <i class="fas fa-graduation-cap w-4 text-center text-xs shrink-0 {{ request()->routeIs('catalog.*') || request()->routeIs('courses.*') || request()->routeIs('lessons.*') ? 'text-amber-300' : 'text-slate-400' }}"></i>
                <span class="hidden lg:block">Courses</span>
            </a>

            <a href="{{ route('wishlist.index') }}" class="flex items-center justify-center lg:justify-between px-2.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('wishlist.*') ? 'bg-[#051c45] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-[#051c45]' }}" title="Wishlist">
                <div class="flex items-center gap-2.5">
                    <i class="fas fa-heart w-4 text-center text-xs shrink-0 {{ request()->routeIs('wishlist.*') ? 'text-[#ff6b6b]' : 'text-slate-400' }}"></i>
                    <span class="hidden lg:block">Wishlist</span>
                </div>
                <span id="wishlist-count-badge" class="hidden lg:inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-extrabold rounded-full {{ request()->routeIs('wishlist.*') ? 'bg-white/20 text-white' : 'bg-[#c30102]/10 text-[#c30102]' }} {{ $studentWishlistCount > 0 ? '' : 'hidden' }}">
                    {{ $studentWishlistCount }}
                </span>
            </a>

            <a href="{{ route('performance.index') }}" class="flex items-center justify-center lg:justify-start gap-2.5 px-2.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('performance.*') ? 'bg-[#051c45] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-[#051c45]' }}" title="Performance">
                <i class="fas fa-chart-line w-4 text-center text-xs shrink-0 {{ request()->routeIs('performance.*') ? 'text-emerald-300' : 'text-slate-400' }}"></i>
                <span class="hidden lg:block">Performance</span>
            </a>

            <a href="{{ route('jobs.index') }}" class="flex items-center justify-center lg:justify-start gap-2.5 px-2.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('jobs.*') || request()->routeIs('careers.*') ? 'bg-[#051c45] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-[#051c45]' }}" title="Careers">
                <i class="fas fa-briefcase w-4 text-center text-xs shrink-0 {{ request()->routeIs('jobs.*') || request()->routeIs('careers.*') ? 'text-amber-300' : 'text-slate-400' }}"></i>
                <span class="hidden lg:block">Careers</span>
            </a>

            <a href="{{ route('feed.index') }}" class="flex items-center justify-center lg:justify-start gap-2.5 px-2.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('feed.*') ? 'bg-[#051c45] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-[#051c45]' }}" title="Community">
                <i class="fas fa-newspaper w-4 text-center text-xs shrink-0 {{ request()->routeIs('feed.*') ? 'text-sky-300' : 'text-slate-400' }}"></i>
                <span class="hidden lg:block">Community</span>
            </a>

            <a href="{{ route('notifications.index') }}" class="flex items-center justify-center lg:justify-between px-2.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('notifications.*') ? 'bg-[#051c45] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-[#051c45]' }}" title="Notifications">
                <div class="flex items-center gap-2.5">
                    <div class="relative">
                        <i class="fas fa-bell w-4 text-center text-xs shrink-0 {{ request()->routeIs('notifications.*') ? 'text-amber-300' : 'text-slate-400' }}"></i>
                        @if($unreadCount > 0)
                            <span class="lg:hidden absolute -top-1 -right-1 w-2 h-2 bg-[#c30102] rounded-full border border-white"></span>
                        @endif
                    </div>
                    <span class="hidden lg:block">Notifications</span>
                </div>
                @if($unreadCount > 0)
                    <span class="hidden lg:inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-extrabold rounded-full {{ request()->routeIs('notifications.*') ? 'bg-white text-[#051c45]' : 'bg-[#c30102] text-white' }}">
                        {{ $unreadCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('profile.edit') }}" class="flex items-center justify-center lg:justify-start gap-2.5 px-2.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('profile.edit') ? 'bg-[#051c45] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-[#051c45]' }}" title="Profile">
                <i class="fas fa-user-shield w-4 text-center text-xs shrink-0 {{ request()->routeIs('profile.edit') ? 'text-emerald-300' : 'text-slate-400' }}"></i>
                <span class="hidden lg:block">Profile</span>
            </a>
        </nav>

        <!-- Sidebar Footer: Student Card & Sign Out -->
        <div class="p-2.5 border-t border-slate-100 bg-slate-50/60 space-y-1.5">
            @if($portalUser)
                <a href="{{ route('profile.edit') }}" class="hidden lg:flex items-center gap-2 p-1.5 rounded-xl bg-white border border-slate-200/80 hover:border-[#051c45]/30 transition-all">
                    @if($portalUser->hasCustomProfilePicture())
                        <img src="{{ $portalUser->profile_picture_url }}" alt="{{ $portalUser->name }}" class="w-7 h-7 rounded-lg object-cover border border-slate-200 shrink-0" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($portalUser->name) }}&background=051c45&color=fff';">
                    @else
                        <div class="w-7 h-7 rounded-lg bg-[#051c45] text-white font-black text-[11px] flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($portalUser->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <div class="text-[11px] font-extrabold text-slate-800 truncate">{{ $portalUser->name }}</div>
                        <div class="text-[10px] font-medium text-slate-400 truncate">{{ $portalUser->phone ?: $portalUser->email }}</div>
                    </div>
                </a>
            @endif
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center lg:justify-start gap-2.5 px-2.5 py-1.5 rounded-xl text-xs font-bold text-red-600 hover:bg-red-50 transition-colors cursor-pointer" title="Sign Out">
                    <i class="fas fa-sign-out-alt w-4 text-center text-xs shrink-0"></i>
                    <span class="hidden lg:block">Sign Out</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- 2. Unified Sticky Top Bar -->
    <header class="sticky top-0 z-20 bg-white/95 backdrop-blur-md border-b border-slate-200/80 px-3 sm:px-5 lg:px-6 h-14 flex items-center justify-between gap-3">
        <!-- Left: Brand on Mobile / Page Title -->
        <div class="flex items-center gap-2.5 min-w-0">
            <a href="{{ route('dashboard') }}" class="md:hidden flex items-center shrink-0">
                <img src="{{ \App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png') }}" alt="Logo" class="h-7 w-7 rounded-lg object-contain bg-white border border-slate-200 p-0.5" onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}';">
            </a>
            <h1 class="font-extrabold text-sm sm:text-base tracking-tight text-[#051c45] truncate">
                @yield('header_title', 'Student Portal')
            </h1>
            <span class="sr-only">Active Student</span>
        </div>

        <!-- Right: Level/XP Pill + Notifications + Profile -->
        <div class="flex items-center gap-2 shrink-0">
            @auth
                <a href="{{ route('performance.index') }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#051c45] text-white text-[11px] font-bold hover:opacity-95 transition-opacity" title="Performance & XP">
                    <span class="px-1.5 py-0.2 rounded bg-[#c30102] text-white text-[10px] font-black">Lv.{{ $userLevel }}</span>
                    <span class="hidden sm:inline text-slate-200">{{ $userXp }} XP</span>
                    <span class="text-amber-300">🔥 {{ $userStreak }}d</span>
                </a>
            @endauth

            <a href="{{ route('notifications.index') }}" class="relative w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200/80 text-slate-600 flex items-center justify-center transition-colors" title="Notifications">
                <i class="far fa-bell text-xs"></i>
                @if($unreadCount > 0)
                    <span class="absolute -top-1 -right-1 flex items-center justify-center min-w-[15px] h-3.5 px-1 bg-[#c30102] text-white text-[9px] font-black rounded-full border border-white">
                        {{ $unreadCount }}
                    </span>
                @endif
            </a>

            @if($portalUser)
                <a href="{{ route('profile.edit') }}" class="w-8 h-8 rounded-lg bg-[#051c45] text-white font-black text-xs flex items-center justify-center hover:bg-[#082860] transition-colors overflow-hidden border border-slate-200/80" title="Profile">
                    @if($portalUser->hasCustomProfilePicture())
                        <img src="{{ $portalUser->profile_picture_url }}" alt="{{ $portalUser->name }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($portalUser->name) }}&background=051c45&color=fff';">
                    @else
                        {{ strtoupper(substr($portalUser->name, 0, 1)) }}
                    @endif
                </a>
            @endif
        </div>
    </header>

    <!-- 3. Main Student Portal Content Workspace -->
    <main class="student-portal-workspace w-full max-w-6xl mx-auto px-3 sm:px-5 lg:px-6 py-4 sm:py-5 min-h-[calc(100vh-3.5rem)]">
        @yield('content')
        
        <footer class="mt-10 pt-4 border-t border-slate-200/70 flex items-center justify-between text-[11px] text-slate-400 pb-16 md:pb-4">
            <span>&copy; {{ date('Y') }} {{ \App\Models\Setting::getVal('site_name', 'Academy of Commercial & Supply Chain (ACS)') }}</span>
            <span>Developed by <a href="https://neurasoft.top" target="_blank" rel="noopener noreferrer" class="text-[#051c45] hover:underline font-semibold">NeuraSoft</a></span>
        </footer>
    </main>

    <!-- 4. Mobile App Bottom Navigation (5 Essential Student Portal Tabs) -->
    <nav class="md:hidden fixed bottom-0 w-full glass-panel flex justify-around items-center px-1 py-1.5 pb-safe z-40 shadow-[0_-4px_24px_rgba(5,28,69,0.06)]">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center py-1.5 px-2 w-16 {{ request()->routeIs('dashboard') ? 'tab-active font-extrabold text-[#051c45]' : 'text-slate-400 hover:text-slate-600' }} transition-colors">
            <i class="fas fa-th-large text-base mb-1"></i>
            <span class="text-[10px] font-bold tracking-tight">Dashboard</span>
        </a>
        
        <a href="{{ route('catalog.index') }}" class="flex flex-col items-center justify-center py-1.5 px-2 w-16 {{ request()->routeIs('catalog.*') || request()->routeIs('courses.*') || request()->routeIs('lessons.*') ? 'tab-active font-extrabold text-[#051c45]' : 'text-slate-400 hover:text-slate-600' }} transition-colors">
            <i class="fas fa-graduation-cap text-base mb-1"></i>
            <span class="text-[10px] font-bold tracking-tight">Courses</span>
        </a>
        
        <a href="{{ route('jobs.index') }}" class="flex flex-col items-center justify-center py-1.5 px-2 w-16 {{ request()->routeIs('jobs.*') || request()->routeIs('careers.*') ? 'tab-active font-extrabold text-[#051c45]' : 'text-slate-400 hover:text-slate-600' }} transition-colors">
            <i class="fas fa-briefcase text-base mb-1"></i>
            <span class="text-[10px] font-bold tracking-tight">Careers</span>
        </a>

        <a href="{{ route('wishlist.index') }}" class="relative flex flex-col items-center justify-center py-1.5 px-2 w-16 {{ request()->routeIs('wishlist.*') ? 'tab-active font-extrabold text-[#051c45]' : 'text-slate-400 hover:text-slate-600' }} transition-colors">
            <i class="fas fa-heart text-base mb-1"></i>
            <span class="text-[10px] font-bold tracking-tight">Wishlist</span>
        </a>
        
        <a href="{{ route('profile.edit') }}" class="flex flex-col items-center justify-center py-1.5 px-2 w-16 {{ request()->routeIs('profile.edit') ? 'tab-active font-extrabold text-[#051c45]' : 'text-slate-400 hover:text-slate-600' }} transition-colors">
            <i class="fas fa-user-circle text-base mb-1"></i>
            <span class="text-[10px] font-bold tracking-tight">Profile</span>
        </a>
    </nav>
    
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js').catch(function() {});
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.img-skeleton-wrapper img').forEach(function(img) {
                if (img.complete) {
                    img.parentElement.classList.add('is-loaded');
                }
            });
        });

        window.openEnrollDrawer = function(courseData) {
            window.dispatchEvent(new CustomEvent('open-enroll-drawer', { detail: courseData }));
        };

        window.openCourseShareModal = function(courseData) {
            window.dispatchEvent(new CustomEvent('open-course-share-modal', { detail: courseData }));
        };

        window.toggleWishlist = function(courseId, buttonEl) {
            fetch('/wishlist/' + courseId + '/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(res => {
                if (res.status === 401) {
                    window.location.href = '{{ route("login") }}';
                    return null;
                }
                return res.json();
            })
            .then(data => {
                if (!data) return;
                const badge = document.getElementById('wishlist-count-badge');
                if (badge) {
                    badge.innerText = data.count;
                    if (data.count > 0) {
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }
                const buttons = document.querySelectorAll('.wishlist-btn-' + courseId);
                buttons.forEach(btn => {
                    const icon = btn.querySelector('i');
                    const text = btn.querySelector('span');
                    if (data.status === 'added') {
                        btn.classList.add('text-[#c30102]');
                        if (icon) {
                            icon.classList.remove('far');
                            icon.classList.add('fas', 'text-[#c30102]');
                        }
                        if (text) text.innerText = 'Saved in Wishlist';
                    } else {
                        btn.classList.remove('text-[#c30102]');
                        if (icon) {
                            icon.classList.remove('fas', 'text-[#c30102]');
                            icon.classList.add('far');
                        }
                        if (text) text.innerText = 'Save to Wishlist';
                    }
                });
            });
        };

        window.copyShortLink = function(url, btn) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(url);
            }
        };
    </script>

    <!-- Global Express Course Enrollment Drawer -->
    <x-express-enroll-drawer />

    <!-- Global Facebook Rich Link Preview & Stylish Share Modal -->
    <x-course-share-modal />
</body>
</html>
