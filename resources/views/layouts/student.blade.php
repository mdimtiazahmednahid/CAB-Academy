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
    @endif
    @hasSection('canonical_url')
        <link rel="canonical" href="@yield('canonical_url')">
    @endif
    @hasSection('og_title')
        <meta property="og:title" content="@yield('og_title')">
    @endif
    @hasSection('og_image')
        <meta property="og:image" content="@yield('og_image')">
    @endif
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
<body class="font-sans antialiased bg-[#f4f7fb] text-slate-900 pb-20 md:pb-0 md:pl-20 lg:pl-64">

    <!-- 1. Desktop Left Sidebar (Native App Navigation Shell) -->
    <aside class="hidden md:flex flex-col w-20 lg:w-64 h-screen fixed top-0 left-0 bg-white border-r border-slate-200/80 z-30 shadow-[4px_0_24px_rgba(5,28,69,0.03)]">
        <!-- Brand Header (Always stays inside Student Portal) -->
        <div class="p-4 lg:px-5 lg:py-4 border-b border-slate-100 flex items-center justify-center lg:justify-start gap-3 min-h-[4.25rem]">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 min-w-0 group" title="Student Portal Dashboard">
                <img src="{{ \App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png') }}" alt="{{ $brandSizing['site_name'] ?? $brandSizing['name'] ?? 'ACS Academy' }}" class="h-9 w-9 shrink-0 rounded-xl object-contain bg-white border border-slate-200/80 p-0.5 shadow-2xs group-hover:scale-105 transition-transform" onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}';">
                <div class="hidden lg:flex flex-col min-w-0">
                    <span class="font-black tracking-tight text-[#051c45] uppercase brand-adaptive-title leading-tight" style="{{ $brandSizing['sidebar_style'] ?? '' }}" title="{{ $brandSizing['site_name'] ?? $brandSizing['name'] ?? 'ACS Academy' }}">{{ $brandSizing['site_name'] ?? $brandSizing['name'] ?? 'ACS Academy' }}</span>
                    <span class="inline-flex items-center gap-1 text-[9px] font-extrabold uppercase tracking-widest text-[#c30102] mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#10b981]"></span>
                        <span>Student Portal</span>
                    </span>
                </div>
            </a>
        </div>
        
        <!-- Categorized Navigation Links -->
        <nav class="flex-1 p-3 lg:px-3.5 lg:py-4 space-y-5 overflow-y-auto">
            <!-- Group 1: Learning Workspace -->
            <div class="space-y-1">
                <div class="hidden lg:block px-3 pb-1 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Learning Hub</div>

                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('dashboard') ? 'bg-[#051c45] text-white shadow-md shadow-[#051c45]/15' : 'text-slate-600 hover:bg-slate-100/80 hover:text-[#051c45]' }}">
                    <i class="fas fa-th-large w-5 text-center text-sm shrink-0 {{ request()->routeIs('dashboard') ? 'text-amber-300' : 'text-slate-400' }}"></i>
                    <span class="hidden lg:block">My Dashboard</span>
                </a>

                <a href="{{ route('catalog.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('catalog.*') || request()->routeIs('courses.*') || request()->routeIs('lessons.*') ? 'bg-[#051c45] text-white shadow-md shadow-[#051c45]/15' : 'text-slate-600 hover:bg-slate-100/80 hover:text-[#051c45]' }}">
                    <i class="fas fa-graduation-cap w-5 text-center text-sm shrink-0 {{ request()->routeIs('catalog.*') || request()->routeIs('courses.*') || request()->routeIs('lessons.*') ? 'text-amber-300' : 'text-slate-400' }}"></i>
                    <span class="hidden lg:block">Browse Courses</span>
                </a>

                <a href="{{ route('wishlist.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('wishlist.*') ? 'bg-[#051c45] text-white shadow-md shadow-[#051c45]/15' : 'text-slate-600 hover:bg-slate-100/80 hover:text-[#051c45]' }}">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-heart w-5 text-center text-sm shrink-0 {{ request()->routeIs('wishlist.*') ? 'text-[#ff6b6b]' : 'text-slate-400' }}"></i>
                        <span class="hidden lg:block">Saved Wishlist</span>
                    </div>
                    <span id="wishlist-count-badge" class="hidden lg:inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-extrabold rounded-full {{ request()->routeIs('wishlist.*') ? 'bg-white/20 text-white' : 'bg-[#c30102]/10 text-[#c30102]' }} {{ $studentWishlistCount > 0 ? '' : 'hidden' }}">
                        {{ $studentWishlistCount }}
                    </span>
                </a>

                <a href="{{ route('performance.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('performance.*') ? 'bg-[#051c45] text-white shadow-md shadow-[#051c45]/15' : 'text-slate-600 hover:bg-slate-100/80 hover:text-[#051c45]' }}">
                    <i class="fas fa-chart-line w-5 text-center text-sm shrink-0 {{ request()->routeIs('performance.*') ? 'text-emerald-300' : 'text-slate-400' }}"></i>
                    <span class="hidden lg:block">Performance &amp; XP</span>
                </a>
            </div>

            <!-- Group 2: Career & Community -->
            <div class="space-y-1">
                <div class="hidden lg:block px-3 pb-1 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Career &amp; Network</div>

                <a href="{{ route('jobs.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('jobs.*') || request()->routeIs('careers.*') ? 'bg-[#051c45] text-white shadow-md shadow-[#051c45]/15' : 'text-slate-600 hover:bg-slate-100/80 hover:text-[#051c45]' }}">
                    <i class="fas fa-briefcase w-5 text-center text-sm shrink-0 {{ request()->routeIs('jobs.*') || request()->routeIs('careers.*') ? 'text-amber-300' : 'text-slate-400' }}"></i>
                    <span class="hidden lg:block">Career Portal</span>
                </a>

                <a href="{{ route('feed.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('feed.*') ? 'bg-[#051c45] text-white shadow-md shadow-[#051c45]/15' : 'text-slate-600 hover:bg-slate-100/80 hover:text-[#051c45]' }}">
                    <i class="fas fa-newspaper w-5 text-center text-sm shrink-0 {{ request()->routeIs('feed.*') ? 'text-sky-300' : 'text-slate-400' }}"></i>
                    <span class="hidden lg:block">Community Feed</span>
                </a>

                <a href="{{ route('notifications.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('notifications.*') ? 'bg-[#051c45] text-white shadow-md shadow-[#051c45]/15' : 'text-slate-600 hover:bg-slate-100/80 hover:text-[#051c45]' }}">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <i class="fas fa-bell w-5 text-center text-sm shrink-0 {{ request()->routeIs('notifications.*') ? 'text-amber-300' : 'text-slate-400' }}"></i>
                            @if($unreadCount > 0)
                                <span class="lg:hidden absolute -top-1 -right-1 w-2.5 h-2.5 bg-[#c30102] rounded-full border border-white"></span>
                            @endif
                        </div>
                        <span class="hidden lg:block">Alerts &amp; Notices</span>
                    </div>
                    @if($unreadCount > 0)
                        <span class="hidden lg:inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-extrabold rounded-full {{ request()->routeIs('notifications.*') ? 'bg-white text-[#051c45]' : 'bg-[#c30102] text-white' }}">
                            {{ $unreadCount }}
                        </span>
                    @endif
                </a>
            </div>

            <!-- Group 3: Account -->
            <div class="space-y-1">
                <div class="hidden lg:block px-3 pb-1 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Account</div>

                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('profile.edit') ? 'bg-[#051c45] text-white shadow-md shadow-[#051c45]/15' : 'text-slate-600 hover:bg-slate-100/80 hover:text-[#051c45]' }}">
                    <i class="fas fa-user-shield w-5 text-center text-sm shrink-0 {{ request()->routeIs('profile.edit') ? 'text-emerald-300' : 'text-slate-400' }}"></i>
                    <span class="hidden lg:block">Profile &amp; Security</span>
                </a>
            </div>
        </nav>

        <!-- Sidebar Footer: Student Card & Sign Out -->
        <div class="p-3 lg:p-3.5 border-t border-slate-100 bg-slate-50/60 space-y-2">
            @if($portalUser)
                <a href="{{ route('profile.edit') }}" class="hidden lg:flex items-center gap-2.5 p-2 rounded-xl bg-white border border-slate-200/80 hover:border-[#051c45]/30 transition-all shadow-2xs">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-[#051c45] to-[#0d3378] text-white font-black text-xs flex items-center justify-center shrink-0">
                        {{ strtoupper(substr($portalUser->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-xs font-extrabold text-slate-800 truncate">{{ $portalUser->name }}</div>
                        <div class="text-[10px] font-semibold text-slate-400 truncate">{{ $portalUser->phone ?: $portalUser->email }}</div>
                    </div>
                </a>
            @endif
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center lg:justify-start gap-2.5 px-3 py-2 rounded-xl text-xs font-bold text-red-600 hover:bg-red-50 border border-transparent hover:border-red-100 transition-colors cursor-pointer" title="Sign Out">
                    <i class="fas fa-sign-out-alt w-5 text-center text-xs shrink-0"></i>
                    <span class="hidden lg:block">Sign Out</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- 2. Unified Sticky Top Bar (Desktop & Mobile Student Portal Header) -->
    <header class="sticky top-0 z-20 bg-white/90 backdrop-blur-xl border-b border-slate-200/80 px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
        <!-- Left: Brand on Mobile / Section Title & Breadcrumb on Desktop -->
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('dashboard') }}" class="md:hidden flex items-center gap-2 shrink-0">
                <img src="{{ \App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png') }}" alt="Logo" class="h-8 w-8 rounded-lg object-contain bg-white border border-slate-200 p-0.5" onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}';">
            </a>
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <h1 class="font-extrabold text-sm sm:text-base lg:text-lg tracking-tight text-[#051c45] truncate">
                        @yield('header_title', 'Student Portal')
                    </h1>
                    <span class="hidden sm:inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 border border-emerald-200/80 text-[10px] font-bold text-emerald-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span>Active Student</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Right: Compact Gamification Pill + Quick Portal Actions -->
        <div class="flex items-center gap-2 sm:gap-2.5 shrink-0">
            @auth
                <!-- Compact Level & XP Pill (Replaces bulky full-page banner) -->
                <a href="{{ route('performance.index') }}" class="hidden sm:inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-gradient-to-r from-[#051c45] to-[#0b2d6b] text-white text-xs font-bold shadow-xs hover:opacity-95 transition-opacity" title="View Learning Progress & XP">
                    <span class="px-1.5 py-0.2 rounded bg-[#c30102] text-white text-[10px] font-black">Lv. {{ $userLevel }}</span>
                    <span class="text-[11px] font-semibold text-slate-200">{{ $userXp }} XP</span>
                    <span class="text-[11px] font-bold text-amber-300 border-l border-white/15 pl-2">🔥 {{ $userStreak }}d</span>
                </a>
            @endauth

            <!-- Quick Course Catalog Pill -->
            <a href="{{ route('catalog.index') }}" class="hidden lg:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200/80 text-xs font-bold text-[#051c45] transition-colors">
                <i class="fas fa-book-open text-[#c30102] text-[11px]"></i>
                <span>Courses</span>
            </a>

            <!-- Quick Career Portal Pill -->
            <a href="{{ route('jobs.index') }}" class="hidden lg:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200/80 text-xs font-bold text-[#051c45] transition-colors">
                <i class="fas fa-briefcase text-[#051c45] text-[11px]"></i>
                <span>Careers</span>
            </a>

            <!-- Notifications Bell -->
            <a href="{{ route('notifications.index') }}" class="relative w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200/80 text-slate-600 flex items-center justify-center transition-colors" title="Alerts & Notifications">
                <i class="far fa-bell text-sm"></i>
                @if($unreadCount > 0)
                    <span class="absolute -top-1 -right-1 flex items-center justify-center min-w-[16px] h-4 px-1 bg-[#c30102] text-white text-[9px] font-black rounded-full border border-white">
                        {{ $unreadCount }}
                    </span>
                @endif
            </a>

            <!-- Profile Shortcut -->
            @if($portalUser)
                <a href="{{ route('profile.edit') }}" class="w-9 h-9 rounded-xl bg-[#051c45] text-white font-black text-xs flex items-center justify-center shadow-xs hover:bg-[#082860] transition-colors" title="Profile & Security Settings">
                    {{ strtoupper(substr($portalUser->name, 0, 1)) }}
                </a>
            @endif
        </div>
    </header>

    <!-- 3. Main Student Portal Content Workspace -->
    <main class="student-portal-workspace w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 min-h-[calc(100vh-4rem)]">
        @yield('content')
        
        <footer class="mt-12 pt-6 border-t border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500 pb-16 md:pb-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="font-bold text-[#051c45] hover:underline">My Dashboard</a>
                <a href="{{ route('catalog.index') }}" class="hover:text-[#051c45] transition-colors">Browse Courses</a>
                <a href="{{ route('jobs.index') }}" class="hover:text-[#051c45] transition-colors">Career Portal</a>
                <a href="{{ route('profile.edit') }}" class="hover:text-[#051c45] transition-colors">Profile Settings</a>
            </div>
            <div>
                Developed by <a href="https://neurasoft.top" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline font-semibold transition-colors">NeuraSoft</a>
            </div>
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
</body>
</html>
