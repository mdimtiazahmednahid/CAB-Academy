<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">

    <title>@yield('header_title', 'Command Center') — {{ \App\Models\Setting::getVal('site_name', 'ACS Academy') }} Admin</title>
    <link rel="icon" href="{{ \App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png') }}">
    <link rel="manifest" href="{{ route('pwa.manifest') }}">
    <meta name="theme-color" content="#051c45">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS & Alpine.js -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary-color: {{ \App\Models\Setting::getVal('primary_color', '#051c45') }};
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .bg-primary { background-color: var(--primary-color); }
        .text-primary { color: var(--primary-color); }
        .border-primary { border-color: var(--primary-color); }
        .focus-ring-primary:focus-within { outline: 2px solid var(--primary-color); outline-offset: 2px; }
        .liquid-glass-card {
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.96) 0%, rgba(255, 255, 255, 0.86) 52%, rgba(248, 250, 252, 0.94) 100%) !important;
            backdrop-filter: blur(22px) saturate(190%);
            -webkit-backdrop-filter: blur(22px) saturate(190%);
            border: 1px solid rgba(255, 255, 255, 0.92) !important;
            box-shadow:
                0 12px 32px -8px rgba(4, 21, 54, 0.07),
                0 4px 12px -2px rgba(4, 21, 54, 0.04),
                inset 0 1.5px 0 rgba(255, 255, 255, 0.98),
                0 0 0 1px rgba(4, 21, 54, 0.06);
            transition: transform 0.28s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.28s cubic-bezier(0.22, 1, 0.36, 1), border-color 0.28s ease;
        }
        .liquid-glass-card:hover {
            border-color: rgba(5, 28, 69, 0.22) !important;
            box-shadow: 0 18px 38px -12px rgba(4, 21, 54, 0.12), 0 0 0 1px rgba(5, 28, 69, 0.14);
        }
        {!! \App\Models\Setting::getTypographyCss() !!}
    </style>
</head>
<body class="font-sans antialiased bg-[#f8fafc] text-slate-900 md:pl-64 pb-16 md:pb-0 min-h-screen flex flex-col selection:bg-[#051c45] selection:text-white">
    @php
        $pendingPayCount = \App\Models\Payment::where('status', 'pending')->count();
        $pendingInqCount = \App\Models\ContactInquiry::where('status', 'pending')->count();
        $siteName = \App\Models\Setting::getVal('site_name', 'ACS Academy');
        $siteLogo = \App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png');
        $brandSizing = \App\Models\Setting::getBrandSizing($siteName);
    @endphp

    <!-- Mobile Top Bar -->
    <header class="md:hidden flex items-center justify-between px-4 py-3 bg-[#03112b] text-white border-b border-white/10 z-30 sticky top-0 gap-2">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 min-w-0">
            <img src="{{ $siteLogo }}" alt="{{ $siteName }}" class="h-8 w-8 shrink-0 rounded-full object-contain bg-white border border-white/20" onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}';">
            <div class="min-w-0">
                <h1 class="font-black tracking-tight uppercase brand-adaptive-title" style="{{ $brandSizing['sidebar_style'] }}">{{ $siteName }}</h1>
                <span class="text-[9px] font-bold uppercase tracking-widest text-emerald-400 block">Admin Console</span>
            </div>
        </a>
        <div class="flex items-center gap-2 shrink-0">
            @if($pendingPayCount > 0)
                <a href="{{ route('admin.payments.index') }}" class="px-2 py-1 rounded-lg bg-amber-500/20 border border-amber-400/30 text-amber-300 text-[10px] font-bold">
                    {{ $pendingPayCount }} Pending
                </a>
            @endif
            <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="p-2 text-slate-300 hover:text-white rounded-lg hover:bg-white/10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </div>
    </header>

    <!-- Desktop Executive Sidebar -->
    <aside class="hidden md:flex flex-col w-64 h-screen fixed top-0 left-0 bg-[#03112b] text-white border-r border-slate-800/80 z-30 select-none">
        <!-- Brand Header (Auto-Responsive for Short & Long Institution Names) -->
        <div class="px-5 py-4 border-b border-white/[0.07] flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group min-w-0">
                <img src="{{ $siteLogo }}" alt="{{ $siteName }}" class="h-9 w-9 rounded-xl object-contain bg-white p-0.5 shadow-sm shrink-0" onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}';">
                <div class="min-w-0">
                    <span class="font-black tracking-tight uppercase text-white brand-adaptive-title" style="{{ $brandSizing['sidebar_style'] }}" title="{{ $siteName }}">{{ $siteName }}</span>
                    <span class="block text-[10px] font-bold uppercase tracking-widest text-emerald-400 mt-0.5">Executive OS</span>
                </div>
            </a>
        </div>

        <!-- Categorized Navigation -->
        <nav class="flex-1 px-3 py-4 space-y-5 overflow-y-auto text-[13px]">
            <!-- 1. Overview & Comms -->
            <div>
                <div class="px-3 mb-1.5 text-[10px] font-black uppercase tracking-widest text-slate-400/70">Command Center</div>
                <div class="space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-white/12 text-white font-bold shadow-xs border border-white/10' : 'text-slate-300/80 hover:bg-white/[0.06] hover:text-white' }} transition-all">
                        <i class="fas fa-chart-pie w-4 text-center {{ request()->routeIs('admin.dashboard') ? 'text-emerald-400' : 'text-slate-400' }}"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('admin.inquiries.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.inquiries*') ? 'bg-white/12 text-white font-bold shadow-xs border border-white/10' : 'text-slate-300/80 hover:bg-white/[0.06] hover:text-white' }} transition-all">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-inbox w-4 text-center {{ request()->routeIs('admin.inquiries*') ? 'text-emerald-400' : 'text-slate-400' }}"></i>
                            <span>Inquiries</span>
                        </div>
                        @if($pendingInqCount > 0)
                            <span class="inline-flex px-2 py-0.5 text-[10px] font-black bg-[#c30102] text-white rounded-full">{{ $pendingInqCount }}</span>
                        @endif
                    </a>

                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.announcements.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.announcements*') ? 'bg-white/12 text-white font-bold shadow-xs border border-white/10' : 'text-slate-300/80 hover:bg-white/[0.06] hover:text-white' }} transition-all">
                        <i class="fas fa-bullhorn w-4 text-center {{ request()->routeIs('admin.announcements*') ? 'text-emerald-400' : 'text-slate-400' }}"></i>
                        <span>Announcements</span>
                    </a>
                    @endif
                </div>
            </div>

            <!-- 2. Academic & LMS -->
            <div>
                <div class="px-3 mb-1.5 text-[10px] font-black uppercase tracking-widest text-slate-400/70">Academic & LMS</div>
                <div class="space-y-1">
                    <a href="{{ route('admin.courses') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.courses*') || request()->routeIs('admin.quizzes*') ? 'bg-white/12 text-white font-bold shadow-xs border border-white/10' : 'text-slate-300/80 hover:bg-white/[0.06] hover:text-white' }} transition-all">
                        <i class="fas fa-graduation-cap w-4 text-center {{ request()->routeIs('admin.courses*') ? 'text-emerald-400' : 'text-slate-400' }}"></i>
                        <span>Courses</span>
                    </a>

                    <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.categories*') ? 'bg-white/12 text-white font-bold shadow-xs border border-white/10' : 'text-slate-300/80 hover:bg-white/[0.06] hover:text-white' }} transition-all">
                        <i class="fas fa-layer-group w-4 text-center {{ request()->routeIs('admin.categories*') ? 'text-emerald-400' : 'text-slate-400' }}"></i>
                        <span>Categories</span>
                    </a>

                    <a href="{{ route('admin.instructors.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.instructors*') ? 'bg-white/12 text-white font-bold shadow-xs border border-white/10' : 'text-slate-300/80 hover:bg-white/[0.06] hover:text-white' }} transition-all">
                        <i class="fas fa-chalkboard-user w-4 text-center {{ request()->routeIs('admin.instructors*') ? 'text-emerald-400' : 'text-slate-400' }}"></i>
                        <span>Instructors</span>
                    </a>

                    <a href="{{ route('admin.certificates.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.certificates*') ? 'bg-white/12 text-white font-bold shadow-xs border border-white/10' : 'text-slate-300/80 hover:bg-white/[0.06] hover:text-white' }} transition-all">
                        <i class="fas fa-award w-4 text-center {{ request()->routeIs('admin.certificates*') ? 'text-emerald-400' : 'text-slate-400' }}"></i>
                        <span>Certificates</span>
                    </a>
                </div>
            </div>

            @if(auth()->user()->isAdmin())
            <!-- 3. Treasury & Finance -->
            <div>
                <div class="px-3 mb-1.5 text-[10px] font-black uppercase tracking-widest text-slate-400/70">Treasury & Finance</div>
                <div class="space-y-1">
                    <a href="{{ route('admin.payments.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.payments*') ? 'bg-white/12 text-white font-bold shadow-xs border border-white/10' : 'text-slate-300/80 hover:bg-white/[0.06] hover:text-white' }} transition-all">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-check-double w-4 text-center {{ request()->routeIs('admin.payments*') ? 'text-emerald-400' : 'text-slate-400' }}"></i>
                            <span>Pending Payments</span>
                        </div>
                        @if($pendingPayCount > 0)
                            <span class="inline-flex px-2 py-0.5 text-[10px] font-black bg-amber-500 text-slate-950 rounded-full">{{ $pendingPayCount }}</span>
                        @endif
                    </a>

                    <a href="{{ route('admin.accounting.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.accounting*') ? 'bg-white/12 text-white font-bold shadow-xs border border-white/10' : 'text-slate-300/80 hover:bg-white/[0.06] hover:text-white' }} transition-all">
                        <i class="fas fa-coins w-4 text-center {{ request()->routeIs('admin.accounting*') ? 'text-emerald-400' : 'text-slate-400' }}"></i>
                        <span>Accounting & Ledger</span>
                    </a>

                    <a href="{{ route('admin.payment-methods.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.payment-methods*') ? 'bg-white/12 text-white font-bold shadow-xs border border-white/10' : 'text-slate-300/80 hover:bg-white/[0.06] hover:text-white' }} transition-all">
                        <i class="fas fa-credit-card w-4 text-center {{ request()->routeIs('admin.payment-methods*') ? 'text-emerald-400' : 'text-slate-400' }}"></i>
                        <span>Payment Methods</span>
                    </a>
                </div>
            </div>
            @endif

            <!-- 4. Growth & Community -->
            <div>
                <div class="px-3 mb-1.5 text-[10px] font-black uppercase tracking-widest text-slate-400/70">Growth & Community</div>
                <div class="space-y-1">
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.users*') ? 'bg-white/12 text-white font-bold shadow-xs border border-white/10' : 'text-slate-300/80 hover:bg-white/[0.06] hover:text-white' }} transition-all">
                        <i class="fas fa-users w-4 text-center {{ request()->routeIs('admin.users*') ? 'text-emerald-400' : 'text-slate-400' }}"></i>
                        <span>Users</span>
                    </a>

                    <a href="{{ route('admin.jobs') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.jobs*') ? 'bg-white/12 text-white font-bold shadow-xs border border-white/10' : 'text-slate-300/80 hover:bg-white/[0.06] hover:text-white' }} transition-all">
                        <i class="fas fa-briefcase w-4 text-center {{ request()->routeIs('admin.jobs*') ? 'text-emerald-400' : 'text-slate-400' }}"></i>
                        <span>Jobs</span>
                    </a>
                    @endif

                    <a href="{{ route('admin.blogs.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.blogs*') ? 'bg-white/12 text-white font-bold shadow-xs border border-white/10' : 'text-slate-300/80 hover:bg-white/[0.06] hover:text-white' }} transition-all">
                        <i class="fas fa-newspaper w-4 text-center {{ request()->routeIs('admin.blogs*') ? 'text-emerald-400' : 'text-slate-400' }}"></i>
                        <span>Articles & Blogs</span>
                    </a>

                    <a href="{{ route('admin.feeds.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.feeds*') ? 'bg-white/12 text-white font-bold shadow-xs border border-white/10' : 'text-slate-300/80 hover:bg-white/[0.06] hover:text-white' }} transition-all">
                        <i class="fas fa-comments w-4 text-center {{ request()->routeIs('admin.feeds*') ? 'text-emerald-400' : 'text-slate-400' }}"></i>
                        <span>Community Feed</span>
                    </a>
                </div>
            </div>

            @if(auth()->user()->isAdmin())
            <!-- 5. Platform Control -->
            <div>
                <div class="px-3 mb-1.5 text-[10px] font-black uppercase tracking-widest text-slate-400/70">Platform Control</div>
                <div class="space-y-1">
                    <a href="{{ route('admin.frontend.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.frontend.*') ? 'bg-white/12 text-white font-bold shadow-xs border border-white/10' : 'text-slate-300/80 hover:bg-white/[0.06] hover:text-white' }} transition-all">
                        <i class="fas fa-wand-magic-sparkles w-4 text-center {{ request()->routeIs('admin.frontend.*') ? 'text-emerald-400' : 'text-slate-400' }}"></i>
                        <span>Frontend</span>
                    </a>

                    <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.settings*') ? 'bg-white/12 text-white font-bold shadow-xs border border-white/10' : 'text-slate-300/80 hover:bg-white/[0.06] hover:text-white' }} transition-all">
                        <i class="fas fa-sliders w-4 text-center {{ request()->routeIs('admin.settings*') ? 'text-emerald-400' : 'text-slate-400' }}"></i>
                        <span>Settings</span>
                    </a>

                    <a href="{{ route('admin.trash.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.trash*') ? 'bg-rose-500/20 text-rose-300 font-bold border border-rose-400/20' : 'text-slate-400 hover:bg-rose-500/10 hover:text-rose-300' }} transition-all">
                        <i class="fas fa-trash-can w-4 text-center"></i>
                        <span>Trash Bin</span>
                    </a>
                </div>
            </div>
            @endif
        </nav>

        <!-- Sidebar Footer: User Identity & Logout -->
        <div class="p-3 border-t border-white/[0.07] bg-[#020b1c]">
            <div class="flex items-center justify-between gap-2 px-2 py-1.5">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-lg bg-[#051c45] border border-white/15 text-white flex items-center justify-center font-bold text-xs shrink-0">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">{{ auth()->user()->role }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                    @csrf
                    <button type="submit" title="Log Out" class="p-2 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-white/5 transition-colors">
                        <i class="fas fa-right-from-bracket text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Mobile Sidebar Drawer -->
    <div id="mobile-menu" class="hidden md:hidden fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex" onclick="if(event.target === this) this.classList.add('hidden')">
        <div class="w-72 max-w-[85vw] bg-[#03112b] h-full p-5 flex flex-col overflow-y-auto shadow-2xl text-white">
            <div class="flex justify-between items-center mb-5 pb-3 border-b border-white/10">
                <div class="flex items-center gap-2.5">
                    <img src="{{ $siteLogo }}" alt="{{ $siteName }}" class="h-8 w-8 rounded-lg object-contain bg-white p-0.5" onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}';">
                    <span class="text-white font-black text-base tracking-tight uppercase truncate">{{ $siteName }}</span>
                </div>
                <button type="button" onclick="document.getElementById('mobile-menu').classList.add('hidden')" class="text-slate-400 hover:text-white p-1 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <nav class="flex-1 space-y-1 text-sm">
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-xl font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-white/15 text-white' : 'text-slate-300 hover:bg-white/10' }}">Dashboard</a>
                <a href="{{ route('admin.courses') }}" class="block px-3 py-2 rounded-xl font-medium {{ request()->routeIs('admin.courses*') ? 'bg-white/15 text-white' : 'text-slate-300 hover:bg-white/10' }}">Courses</a>
                <a href="{{ route('admin.categories.index') }}" class="block px-3 py-2 rounded-xl font-medium {{ request()->routeIs('admin.categories*') ? 'bg-white/15 text-white' : 'text-slate-300 hover:bg-white/10' }}">Categories</a>
                <a href="{{ route('admin.instructors.index') }}" class="block px-3 py-2 rounded-xl font-medium {{ request()->routeIs('admin.instructors*') ? 'bg-white/15 text-white' : 'text-slate-300 hover:bg-white/10' }}">Instructors</a>
                <a href="{{ route('admin.certificates.index') }}" class="block px-3 py-2 rounded-xl font-medium {{ request()->routeIs('admin.certificates*') ? 'bg-white/15 text-white' : 'text-slate-300 hover:bg-white/10' }}">Certificates</a>
                <a href="{{ route('admin.inquiries.index') }}" class="block px-3 py-2 rounded-xl font-medium {{ request()->routeIs('admin.inquiries*') ? 'bg-white/15 text-white' : 'text-slate-300 hover:bg-white/10' }}">Inquiries</a>
                <a href="{{ route('admin.feeds.index') }}" class="block px-3 py-2 rounded-xl font-medium {{ request()->routeIs('admin.feeds*') ? 'bg-white/15 text-white' : 'text-slate-300 hover:bg-white/10' }}">Community Feed</a>
                <a href="{{ route('admin.blogs.index') }}" class="block px-3 py-2 rounded-xl font-medium {{ request()->routeIs('admin.blogs*') ? 'bg-white/15 text-white' : 'text-slate-300 hover:bg-white/10' }}">Articles & Blogs</a>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.payments.index') }}" class="block px-3 py-2 rounded-xl font-medium {{ request()->routeIs('admin.payments*') ? 'bg-white/15 text-white' : 'text-slate-300 hover:bg-white/10' }}">Pending Payments</a>
                <a href="{{ route('admin.accounting.index') }}" class="block px-3 py-2 rounded-xl font-medium {{ request()->routeIs('admin.accounting*') ? 'bg-white/15 text-white' : 'text-slate-300 hover:bg-white/10' }}">Accounting & Ledger</a>
                <a href="{{ route('admin.payment-methods.index') }}" class="block px-3 py-2 rounded-xl font-medium {{ request()->routeIs('admin.payment-methods*') ? 'bg-white/15 text-white' : 'text-slate-300 hover:bg-white/10' }}">Payment Methods</a>
                <a href="{{ route('admin.users') }}" class="block px-3 py-2 rounded-xl font-medium {{ request()->routeIs('admin.users*') ? 'bg-white/15 text-white' : 'text-slate-300 hover:bg-white/10' }}">Users</a>
                <a href="{{ route('admin.jobs') }}" class="block px-3 py-2 rounded-xl font-medium {{ request()->routeIs('admin.jobs*') ? 'bg-white/15 text-white' : 'text-slate-300 hover:bg-white/10' }}">Jobs</a>
                <a href="{{ route('admin.frontend.index') }}" class="block px-3 py-2 rounded-xl font-medium {{ request()->routeIs('admin.frontend.*') ? 'bg-white/15 text-white' : 'text-slate-300 hover:bg-white/10' }}">Frontend</a>
                <a href="{{ route('admin.settings') }}" class="block px-3 py-2 rounded-xl font-medium {{ request()->routeIs('admin.settings') ? 'bg-white/15 text-white' : 'text-slate-300 hover:bg-white/10' }}">Settings</a>
                <a href="{{ route('admin.announcements.create') }}" class="block px-3 py-2 rounded-xl font-medium {{ request()->routeIs('admin.announcements.*') ? 'bg-white/15 text-white' : 'text-slate-300 hover:bg-white/10' }}">Announcements</a>
                <a href="{{ route('admin.trash.index') }}" class="block px-3 py-2 rounded-xl font-medium text-rose-400 hover:bg-rose-500/10">Trash Bin</a>
                @endif
            </nav>
            <div class="mt-4 pt-4 border-t border-white/10 space-y-2">
                <a href="{{ url('/') }}" target="_blank" class="block px-3 py-2 rounded-xl text-xs font-bold text-emerald-400 bg-emerald-500/10 text-center">Visit Live Platform ↗</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left block px-3 py-2 rounded-xl text-sm font-medium text-rose-400 hover:bg-rose-500/10">Log Out</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Desktop Sticky Executive Top Bar -->
    <header class="hidden md:flex items-center justify-between px-8 h-16 bg-white/90 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-20">
        <div class="flex items-center gap-3">
            <span class="w-2 h-2 rounded-full bg-[#10b981]"></span>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Workspace</span>
            <span class="text-slate-300">/</span>
            <span class="text-sm font-bold text-[#051c45]">@yield('header_title', 'Executive Overview')</span>
        </div>

        <div class="flex items-center gap-3">
            @if(auth()->user()->isAdmin() && $pendingPayCount > 0)
                <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200/80 text-xs font-bold transition-colors">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <span>{{ $pendingPayCount }} Pending {{ \Illuminate\Support\Str::plural('Payment', $pendingPayCount) }}</span>
                </a>
            @endif

            @if($pendingInqCount > 0)
                <a href="{{ route('admin.inquiries.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200/80 text-xs font-bold transition-colors">
                    <i class="fas fa-inbox text-[11px]"></i>
                    <span>{{ $pendingInqCount }} {{ \Illuminate\Support\Str::plural('Inquiry', $pendingInqCount) }}</span>
                </a>
            @endif

            <a href="{{ route('admin.courses') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-[#051c45] text-xs font-bold transition-colors">
                <i class="fas fa-graduation-cap text-[11px]"></i>
                <span>Courses</span>
            </a>

            <a href="{{ url('/') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-[#051c45] hover:bg-[#0a2540] text-white text-xs font-bold transition-colors shadow-xs">
                <span>Live Site</span>
                <i class="fas fa-arrow-up-right-from-square text-[10px] text-emerald-400"></i>
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 w-full max-w-7xl mx-auto p-4 md:p-8">
        @if($errors->any())
            <div class="mb-6 p-4 bg-rose-50 text-rose-800 rounded-2xl border border-rose-200 shadow-xs">
                <div class="flex items-center gap-2 mb-1.5 font-bold text-sm">
                    <i class="fas fa-circle-exclamation text-rose-600"></i>
                    <span>Please resolve the following input errors:</span>
                </div>
                <ul class="list-disc pl-5 text-xs space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="mt-auto py-5 px-8 border-t border-slate-200/70 bg-white/60 text-center text-xs text-slate-500">
        {{ $siteName }} Executive Console &bull; Developed by <a href="https://neurasoft.top" target="_blank" rel="noopener noreferrer" class="text-[#051c45] hover:underline font-bold transition-colors">NeuraSoft</a>
    </footer>

    <!-- Native Mobile Admin Bottom Tab Navigation Bar -->
    <nav class="md:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-xl border-t border-slate-200/80 shadow-[0_-8px_25px_-5px_rgba(5,28,69,0.08)] pb-safe">
        <div class="grid grid-cols-5 h-16">
            <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center justify-center gap-1 transition-colors {{ request()->routeIs('admin.dashboard') ? 'text-[#051c45] font-black' : 'text-slate-500' }}">
                <i class="fas fa-chart-pie text-base"></i>
                <span class="text-[10px]">Overview</span>
            </a>
            <a href="{{ route('admin.courses') }}" class="flex flex-col items-center justify-center gap-1 transition-colors {{ request()->routeIs('admin.courses*') ? 'text-[#051c45] font-black' : 'text-slate-500' }}">
                <i class="fas fa-graduation-cap text-base"></i>
                <span class="text-[10px]">Courses</span>
            </a>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.payments.index') }}" class="flex flex-col items-center justify-center gap-1 relative transition-colors {{ request()->routeIs('admin.payments*') ? 'text-[#051c45] font-black' : 'text-slate-500' }}">
                <i class="fas fa-check-double text-base"></i>
                @if($pendingPayCount > 0)
                    <span class="absolute top-1.5 right-4 w-4 h-4 rounded-full bg-amber-500 text-slate-950 text-[9px] font-black flex items-center justify-center">{{ $pendingPayCount }}</span>
                @endif
                <span class="text-[10px]">Payments</span>
            </a>
            <a href="{{ route('admin.frontend.index') }}" class="flex flex-col items-center justify-center gap-1 transition-colors {{ request()->routeIs('admin.frontend.*') ? 'text-[#051c45] font-black' : 'text-slate-500' }}">
                <i class="fas fa-wand-magic-sparkles text-base"></i>
                <span class="text-[10px]">CMS</span>
            </a>
            @else
            <a href="{{ route('admin.blogs.index') }}" class="flex flex-col items-center justify-center gap-1 transition-colors {{ request()->routeIs('admin.blogs*') ? 'text-[#051c45] font-black' : 'text-slate-500' }}">
                <i class="fas fa-newspaper text-base"></i>
                <span class="text-[10px]">Blogs</span>
            </a>
            <a href="{{ route('admin.feeds.index') }}" class="flex flex-col items-center justify-center gap-1 transition-colors {{ request()->routeIs('admin.feeds*') ? 'text-[#051c45] font-black' : 'text-slate-500' }}">
                <i class="fas fa-comments text-base"></i>
                <span class="text-[10px]">Feed</span>
            </a>
            @endif
            <button type="button" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="flex flex-col items-center justify-center gap-1 text-slate-500">
                <i class="fas fa-bars text-base"></i>
                <span class="text-[10px]">Menu</span>
            </button>
        </div>
    </nav>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js').catch(function() {});
            });
        }
    </script>
</body>
</html>
