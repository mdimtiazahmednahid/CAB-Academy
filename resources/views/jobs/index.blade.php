@extends(auth()->check() && !auth()->user()->isAdmin() ? 'layouts.student' : 'layouts.public')

@php
    $siteName = \App\Models\Setting::getVal('site_name', 'CAB Academy');
    $totalOpenings = $totalActiveJobs ?? $jobs->count();
    $partnerCount = max($distinctCompaniesCount ?? 1, 1);
    $appliedIds = $appliedJobIds ?? [];
    $counts = $typeCounts ?? [
        'all' => $totalOpenings,
        'Full-time' => 0,
        'Remote' => 0,
        'Contract' => 0,
        'Part-time' => 0,
        'Internship' => 0,
    ];
    $emblemGradients = [
        'from-[#051c45] via-[#0a2540] to-[#1e3a8a]',
        'from-[#7f1d1d] via-[#991b1b] to-[#c30102]',
        'from-[#064e3b] via-[#065f46] to-[#059669]',
        'from-[#1e1b4b] via-[#312e81] to-[#4338ca]',
        'from-[#0f172a] via-[#1e293b] to-[#334155]',
    ];
@endphp

@section('title', 'Job Portal & Career Placement Board | ' . $siteName)
@section('header_title', 'Career & Job Portal')
@section('meta_description', 'Browse verified corporate jobs, export-import roles, supply chain openings, and banking careers on the ' . $siteName . ' Job Portal.')
@section('canonical_url', route('jobs.index'))

@section('content')
<div class="bg-[#f4f7fb] min-h-screen pb-16">

    <!-- 1. Classic Job Portal Search Header -->
    <section class="bg-gradient-to-r from-[#031330] via-[#051c45] to-[#0a2540] text-white pt-8 pb-10 sm:pt-10 sm:pb-12 border-b border-white/10 relative overflow-hidden">
        <div class="absolute -top-24 -right-24 w-80 h-80 bg-[#c30102]/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-[#10b981]/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Top Row: Title + Quick Stats -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
                <div>
                    <div class="flex items-center gap-2 text-xs text-slate-300 font-medium mb-2 flex-wrap">
                        @auth
                            <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}" class="inline-flex items-center gap-1 text-amber-300 hover:text-white font-bold transition-colors">
                                <i class="fas fa-th-large text-[10px]"></i>
                                <span>My Dashboard</span>
                            </a>
                            <span class="text-slate-500">/</span>
                        @else
                            <a href="{{ url('/') }}" class="hover:text-white transition-colors">Home</a>
                            <span class="text-slate-500">/</span>
                        @endauth
                        <span class="text-white font-bold">Job Portal</span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-[10px] font-bold ml-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            {{ $totalOpenings }} Live {{ \Illuminate\Support\Str::plural('Job', $totalOpenings) }}
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight text-white">
                        Corporate Job & Placement Portal
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl">
                        Verified openings in Commercial Banking, L/C Operations, Supply Chain, Customs & VAT across {{ $partnerCount }}+ hiring companies.
                    </p>
                </div>

                <div class="flex items-center gap-2.5 shrink-0">
                    <a href="#employer-post-callout" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white text-xs font-bold transition-all">
                        <i class="fas fa-plus-circle text-emerald-400"></i>
                        <span>For Employers: Post a Job</span>
                    </a>
                </div>
            </div>

            <!-- Job Portal Search Bar (Indeed / LinkedIn Jobs Style) -->
            <form method="GET" action="{{ route('jobs.index') }}" onsubmit="showJobPortalSkeleton()" class="bg-white p-2 sm:p-2.5 rounded-2xl shadow-xl border border-white/20 flex flex-col md:flex-row items-stretch gap-2">
                <!-- Keyword / Role / Company / City Input -->
                <div class="relative flex-1 flex items-center">
                    <i class="fas fa-search absolute left-4 text-slate-400 text-sm pointer-events-none"></i>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Job title, keywords, company, or location (e.g. Commercial Manager, Chittagong, L/C)..." 
                        class="w-full pl-11 pr-9 py-3 rounded-xl bg-slate-50/70 focus:bg-white border border-transparent focus:border-slate-200 text-xs sm:text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:outline-none transition-all"
                    >
                    @if(request('search'))
                        <a href="{{ route('jobs.index', request()->except('search')) }}" onclick="showJobPortalSkeleton()" class="absolute right-3 text-slate-400 hover:text-red-500 text-xs" title="Clear search">
                            <i class="fas fa-times-circle"></i>
                        </a>
                    @endif
                </div>

                <!-- Employment Type Dropdown -->
                <div class="relative md:w-56 flex items-center border-t md:border-t-0 md:border-l border-slate-200 pt-2 md:pt-0 md:pl-2">
                    <i class="fas fa-briefcase absolute left-5 text-slate-400 text-xs pointer-events-none"></i>
                    @php
                        $currentType = (string) request('type', '');
                    @endphp
                    <select 
                        name="type" 
                        onchange="showJobPortalSkeleton(); this.form.submit()"
                        class="w-full pl-9 pr-8 py-3 rounded-xl bg-slate-50/70 focus:bg-white text-xs sm:text-sm font-bold text-slate-800 focus:outline-none cursor-pointer"
                    >
                        <option value="" {{ $currentType === '' ? 'selected' : '' }}>All Job Types ({{ $counts['all'] ?? $totalOpenings }})</option>
                        <option value="Full-time" {{ strcasecmp($currentType, 'Full-time') === 0 ? 'selected' : '' }}>Full-time</option>
                        <option value="Remote" {{ strcasecmp($currentType, 'Remote') === 0 ? 'selected' : '' }}>Remote / Hybrid</option>
                        <option value="Contract" {{ strcasecmp($currentType, 'Contract') === 0 ? 'selected' : '' }}>Contract</option>
                        <option value="Part-time" {{ strcasecmp($currentType, 'Part-time') === 0 ? 'selected' : '' }}>Part-time</option>
                        <option value="Internship" {{ strcasecmp($currentType, 'Internship') === 0 ? 'selected' : '' }}>Internship</option>
                    </select>
                </div>

                <!-- Find Jobs Submit Button -->
                <button type="submit" class="btn-ipdp-primary px-7 py-3 rounded-xl text-xs sm:text-sm font-black flex items-center justify-center gap-2 shrink-0 cursor-pointer">
                    <i class="fas fa-search text-xs"></i>
                    <span>Find Jobs</span>
                </button>
            </form>
        </div>
    </section>

    <!-- 2. Main 2-Column Job Board Body -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 sm:pt-8">

        <!-- Flash Notifications -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-center gap-3 shadow-sm">
                <i class="fas fa-check-circle text-emerald-600 text-lg shrink-0"></i>
                <span class="text-xs sm:text-sm font-bold">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-900 flex items-center gap-3 shadow-sm">
                <i class="fas fa-exclamation-circle text-red-600 text-lg shrink-0"></i>
                <span class="text-xs sm:text-sm font-bold">{{ session('error') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-900 shadow-sm">
                <div class="font-bold text-xs uppercase tracking-wider text-red-800 mb-1">Please fix the following errors:</div>
                <ul class="list-disc pl-5 text-xs space-y-1 text-red-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- LEFT SIDEBAR (3 Cols on Desktop): Classic Job Portal Filters -->
            <aside class="lg:col-span-3 space-y-5 lg:sticky lg:top-24">
                <div class="liquid-glass-card rounded-2xl p-5 space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-200/80 pb-3">
                        <h2 class="text-xs font-black uppercase tracking-wider text-[#041536] flex items-center gap-2">
                            <i class="fas fa-filter text-[#c30102]"></i>
                            <span>Filter Jobs</span>
                        </h2>
                        @if(request()->filled('search') || request()->filled('type') || request()->filled('sort'))
                            <a href="{{ route('jobs.index') }}" class="text-[11px] font-bold text-[#c30102] hover:underline">
                                Reset All
                            </a>
                        @endif
                    </div>

                    <!-- Employment Type Filter List -->
                    <div>
                        <h3 class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-2.5">Job Type</h3>
                        @php
                            $sidebarTypes = [
                                '' => ['label' => 'All Openings', 'count' => $counts['all'] ?? $totalOpenings],
                                'Full-time' => ['label' => 'Full-time', 'count' => $counts['Full-time'] ?? 0],
                                'Remote' => ['label' => 'Remote / Hybrid', 'count' => $counts['Remote'] ?? 0],
                                'Contract' => ['label' => 'Contractual', 'count' => $counts['Contract'] ?? 0],
                                'Part-time' => ['label' => 'Part-time', 'count' => $counts['Part-time'] ?? 0],
                                'Internship' => ['label' => 'Internship', 'count' => $counts['Internship'] ?? 0],
                            ];
                        @endphp
                        <div class="space-y-1">
                            @foreach($sidebarTypes as $typeVal => $meta)
                                @php
                                    $isSelected = ($typeVal === '' && $currentType === '') || (strcasecmp($currentType, $typeVal) === 0);
                                    $urlParams = $typeVal === ''
                                        ? request()->except('type')
                                        : array_merge(request()->query(), ['type' => $typeVal]);
                                @endphp
                                <a 
                                    href="{{ route('jobs.index', $urlParams) }}" 
                                    onclick="showJobPortalSkeleton()"
                                    class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition-all {{ $isSelected ? 'bg-[#051c45] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100/90' }}"
                                >
                                    <span class="flex items-center gap-2">
                                        <span class="w-3.5 h-3.5 rounded flex items-center justify-center border {{ $isSelected ? 'bg-[#c30102] border-[#c30102] text-white' : 'border-slate-300 bg-white' }}">
                                            @if($isSelected)
                                                <i class="fas fa-check text-[8px]"></i>
                                            @endif
                                        </span>
                                        <span>{{ $meta['label'] }}</span>
                                    </span>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full font-extrabold {{ $isSelected ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $meta['count'] }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Sort Order -->
                    <div class="pt-4 border-t border-slate-200/80">
                        <h3 class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-2">Sort Listings</h3>
                        <div class="grid grid-cols-2 gap-1.5">
                            <a 
                                href="{{ route('jobs.index', array_merge(request()->query(), ['sort' => 'newest'])) }}"
                                onclick="showJobPortalSkeleton()"
                                class="px-3 py-2 rounded-xl text-center text-xs font-bold border transition-all {{ request('sort', 'newest') === 'newest' ? 'bg-[#051c45]/10 border-[#051c45] text-[#051c45]' : 'border-slate-200 text-slate-600 hover:bg-slate-50' }}"
                            >
                                Newest
                            </a>
                            <a 
                                href="{{ route('jobs.index', array_merge(request()->query(), ['sort' => 'company'])) }}"
                                onclick="showJobPortalSkeleton()"
                                class="px-3 py-2 rounded-xl text-center text-xs font-bold border transition-all {{ request('sort') === 'company' ? 'bg-[#051c45]/10 border-[#051c45] text-[#051c45]' : 'border-slate-200 text-slate-600 hover:bg-slate-50' }}"
                            >
                                Company A-Z
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Candidate Placement Support Box -->
                <div class="liquid-glass-card rounded-2xl p-5 space-y-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-[#051c45] text-white flex items-center justify-center text-sm shrink-0">
                            <i class="fas fa-user-shield text-emerald-400"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-black text-[#041536]">Verified Placement Cell</h3>
                            <p class="text-[11px] text-slate-500">Direct CV & Certificate Referral</p>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        When you apply through {{ $siteName }}, your verified course certificates are automatically attached for corporate HR shortlisting.
                    </p>
                </div>
            </aside>

            <!-- RIGHT MAIN JOB FEED (9 Cols on Desktop): Classic Horizontal Job Portal Cards -->
            <div class="lg:col-span-9 space-y-4">

                <!-- Feed Header Bar -->
                <div class="liquid-glass-card rounded-2xl px-4 py-3 flex flex-wrap items-center justify-between gap-2">
                    <div class="text-xs sm:text-sm font-bold text-slate-700">
                        Showing <span class="font-black text-[#041536]">{{ $jobs->count() }}</span>
                        {{ $currentType ? $currentType : '' }}
                        {{ \Illuminate\Support\Str::plural('job', $jobs->count()) }}
                        @if(request('search'))
                            matching <span class="text-[#c30102] font-black">"{{ request('search') }}"</span>
                        @endif
                    </div>

                    @if(request()->filled('search') || request()->filled('type'))
                        <div class="flex items-center gap-2">
                            @if(request('type'))
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#051c45]/10 text-[#051c45] text-xs font-bold">
                                    <span>{{ request('type') }}</span>
                                    <a href="{{ route('jobs.index', request()->except('type')) }}" class="hover:text-red-600"><i class="fas fa-times text-[10px]"></i></a>
                                </span>
                            @endif
                            @if(request('search'))
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#c30102]/10 text-[#c30102] text-xs font-bold">
                                    <span>"{{ request('search') }}"</span>
                                    <a href="{{ route('jobs.index', request()->except('search')) }}" class="hover:text-red-700"><i class="fas fa-times text-[10px]"></i></a>
                                </span>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Job Portal Skeleton Loader (Shown during Search, Filter & Sort transitions) -->
                <div id="job-feed-skeleton" class="space-y-4 hidden" aria-hidden="true">
                    @for($s = 0; $s < 4; $s++)
                        <div class="liquid-glass-card rounded-2xl p-5 sm:p-6">
                            <div class="flex flex-col sm:flex-row items-start justify-between gap-4 sm:gap-5">
                                <div class="flex items-start gap-4 flex-1 w-full">
                                    <!-- 16x16 Company Logo Skeleton -->
                                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl skeleton-shimmer shrink-0"></div>
                                    <div class="flex-1 space-y-2.5 w-full">
                                        <!-- Job Title Skeleton -->
                                        <div class="h-5 w-3/5 rounded-lg skeleton-shimmer"></div>
                                        <!-- Company & Location Skeleton -->
                                        <div class="flex items-center gap-2">
                                            <div class="h-3.5 w-28 rounded-md skeleton-shimmer"></div>
                                            <div class="h-3.5 w-24 rounded-md skeleton-shimmer"></div>
                                            <div class="h-3.5 w-16 rounded-md skeleton-shimmer"></div>
                                        </div>
                                        <!-- Metadata Chips Skeleton -->
                                        <div class="flex items-center gap-2 pt-0.5">
                                            <div class="h-6 w-20 rounded-lg skeleton-shimmer"></div>
                                            <div class="h-6 w-28 rounded-lg skeleton-shimmer"></div>
                                            <div class="h-6 w-20 rounded-lg skeleton-shimmer"></div>
                                        </div>
                                        <!-- Description Excerpt Skeleton -->
                                        <div class="space-y-1.5 pt-1">
                                            <div class="h-3 w-full rounded skeleton-shimmer"></div>
                                            <div class="h-3 w-4/5 rounded skeleton-shimmer"></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Right Action Column Skeleton -->
                                <div class="flex sm:flex-col items-center sm:items-end justify-between w-full sm:w-36 shrink-0 gap-2 pt-3 sm:pt-0">
                                    <div class="h-10 w-full rounded-xl skeleton-shimmer"></div>
                                    <div class="h-9 w-full rounded-xl skeleton-shimmer"></div>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>

                <!-- Live Job Listing Rows Container -->
                <div id="job-feed-results" class="space-y-4">
                @forelse($jobs as $job)
                    @php
                        $hasApplied = in_array($job->id, $appliedIds);
                        $companyName = $job->company ?: 'Corporate Partner Enterprise';
                        $cleanWords = array_values(array_filter(explode(' ', preg_replace('/[^A-Za-z0-9\s]/', '', $companyName))));
                        if (count($cleanWords) >= 2) {
                            $initials = strtoupper(substr($cleanWords[0], 0, 1) . substr($cleanWords[1], 0, 1));
                        } else {
                            $initials = strtoupper(substr($cleanWords[0] ?? 'CA', 0, 2));
                        }
                        $gradientClass = $emblemGradients[crc32($companyName) % count($emblemGradients)];
                        $postedAgo = $job->created_at ? $job->created_at->diffForHumans() : 'Recently posted';
                        $isRecent = $job->created_at && $job->created_at->diffInDays(now()) <= 14;
                        $logoUrl = !empty($job->company_logo) ? storage_asset($job->company_logo) : null;

                        $jobBriefPayload = [
                            'id' => $job->id,
                            'title' => $job->title,
                            'company' => $companyName,
                            'initials' => $initials,
                            'logo_url' => $logoUrl,
                            'gradient' => $gradientClass,
                            'location' => $job->location ?: 'Dhaka, Bangladesh',
                            'job_type' => $job->job_type ?: 'Full-time',
                            'salary_range' => $job->salary_range ?: 'Negotiable / Competitive',
                            'description' => $job->description,
                            'apply_link' => $job->apply_link,
                            'posted_ago' => $postedAgo,
                            'has_applied' => $hasApplied,
                        ];
                    @endphp

                    <article class="liquid-glass-card rounded-2xl p-5 sm:p-6 transition-all duration-300 group hover:-translate-y-0.5 relative overflow-hidden">
                        <!-- Left Accent Bar on Hover -->
                        <div class="absolute top-0 bottom-0 left-0 w-1 bg-gradient-to-b from-[#041536] via-[#c30102] to-[#10b981] opacity-70 group-hover:opacity-100 transition-opacity"></div>

                        <div class="flex flex-col sm:flex-row items-start justify-between gap-4 sm:gap-5">

                            <!-- 1. Left: Auto-Fitted Company Logo OR Rich Curated Monogram Emblem -->
                            <div class="flex items-start gap-4 flex-1 min-w-0 w-full">
                                <div 
                                    onclick='openJobBriefDrawer(@json($jobBriefPayload))'
                                    class="relative w-14 h-14 sm:w-16 sm:h-16 rounded-2xl shrink-0 cursor-pointer transition-transform duration-300 group-hover:scale-105"
                                >
                                    @if($logoUrl)
                                        <!-- Auto-Fitted Image Frame with Skeleton Shimmer: Works for wide, square, or tall company logos without cropping -->
                                        <div class="img-skeleton-wrapper w-full h-full rounded-2xl bg-white border border-slate-200/90 shadow-sm p-1.5 flex items-center justify-center overflow-hidden">
                                            <img 
                                                src="{{ $logoUrl }}" 
                                                alt="{{ $companyName }}" 
                                                class="w-full h-full object-contain object-center relative z-10 transition-opacity duration-300"
                                                loading="lazy"
                                                onload="this.parentElement.classList.add('is-loaded')"
                                                onerror="this.parentElement.style.display='none'; this.parentElement.nextElementSibling.style.display='flex';"
                                            >
                                        </div>
                                        <!-- Fallback Emblem if image URL fails -->
                                        <div style="display:none;" class="w-full h-full rounded-2xl bg-gradient-to-br {{ $gradientClass }} text-white shadow-md border border-white/25 items-center justify-center relative overflow-hidden">
                                            <i class="fas fa-building absolute -bottom-2 -right-2 text-3xl text-white/10 pointer-events-none"></i>
                                            <span class="text-base sm:text-lg font-black tracking-wider relative z-10">{{ $initials }}</span>
                                        </div>
                                    @else
                                        <!-- Beautiful Curated Corporate Emblem when no image is used -->
                                        <div class="w-full h-full rounded-2xl bg-gradient-to-br {{ $gradientClass }} text-white shadow-md border border-white/25 flex items-center justify-center relative overflow-hidden">
                                            <i class="fas fa-building absolute -bottom-2 -right-2 text-3xl text-white/10 pointer-events-none"></i>
                                            <span class="text-base sm:text-lg font-black tracking-wider relative z-10">{{ $initials }}</span>
                                        </div>
                                    @endif

                                    <!-- Verified Employer Badge Pin -->
                                    <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-emerald-500 text-white border-2 border-white flex items-center justify-center text-[8px] shadow-xs" title="Verified Corporate Employer">
                                        <i class="fas fa-check"></i>
                                    </span>
                                </div>

                                <!-- 2. Center: Job Portal Details -->
                                <div class="flex-1 min-w-0">
                                    <!-- Top Badges & Title -->
                                    <div class="flex flex-wrap items-center gap-2 mb-1">
                                        <h2 class="text-base sm:text-lg font-black text-[#041536] group-hover:text-[#c30102] transition-colors leading-snug">
                                            <button 
                                                type="button" 
                                                onclick='openJobBriefDrawer(@json($jobBriefPayload))'
                                                class="text-left hover:underline cursor-pointer"
                                            >
                                                {{ $job->title }}
                                            </button>
                                        </h2>

                                        @if($isRecent)
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-[#c30102]/10 text-[#c30102]">
                                                New
                                            </span>
                                        @endif

                                        @if($hasApplied)
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                <i class="fas fa-check-circle mr-1"></i>Applied
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Company Name • Location • Posted Time -->
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs font-semibold text-slate-600 mb-3">
                                        <span class="font-bold text-[#051c45] flex items-center gap-1.5">
                                            <i class="far fa-building text-[#c30102] text-[11px]"></i>
                                            <span>{{ $companyName }}</span>
                                        </span>

                                        @if(!empty($job->location))
                                            <span class="text-slate-300">&bull;</span>
                                            <span class="flex items-center gap-1 text-slate-600">
                                                <i class="fas fa-map-marker-alt text-[#c30102] text-[11px]"></i>
                                                <span>{{ $job->location }}</span>
                                            </span>
                                        @endif

                                        <span class="text-slate-300">&bull;</span>
                                        <span class="text-slate-400 text-[11px] flex items-center gap-1">
                                            <i class="far fa-clock text-[10px]"></i>
                                            <span>{{ $postedAgo }}</span>
                                        </span>
                                    </div>

                                    <!-- Job Portal Metadata Pills (Job Type, Salary, Apply Method) -->
                                    <div class="flex flex-wrap items-center gap-2 mb-3">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-[#051c45]/8 text-[#051c45] border border-[#051c45]/12">
                                            <i class="fas fa-briefcase text-[10px]"></i>
                                            <span>{{ $job->job_type ?: 'Full-time' }}</span>
                                        </span>

                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-200/80">
                                            <i class="fas fa-money-bill-wave text-emerald-600 text-[10px]"></i>
                                            <span>{{ $job->salary_range ?: 'Negotiable' }}</span>
                                        </span>

                                        @if(empty($job->apply_link))
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200/60">
                                                <i class="fas fa-bolt text-[10px]"></i>
                                                <span>Easy Apply</span>
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Short Role Excerpt -->
                                    <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">
                                        {{ $job->description }}
                                    </p>
                                </div>
                            </div>

                            <!-- 3. Right: Job Portal Action Column -->
                            <div class="flex sm:flex-col items-center sm:items-end justify-between w-full sm:w-auto shrink-0 gap-2 pt-3 sm:pt-0 border-t sm:border-t-0 border-slate-200/70 sm:min-w-[148px]">
                                @if(!empty($job->apply_link))
                                    <a 
                                        href="{{ $job->apply_link }}" 
                                        target="_blank" 
                                        rel="noopener noreferrer" 
                                        class="btn-ipdp-primary flex-1 sm:flex-initial w-full py-2.5 px-4 rounded-xl text-xs font-black flex items-center justify-center gap-1.5 transition-all"
                                    >
                                        <span>Apply Externally</span>
                                        <i class="fas fa-external-link-alt text-[10px]"></i>
                                    </a>
                                @elseif($hasApplied)
                                    <button 
                                        type="button" 
                                        disabled 
                                        class="flex-1 sm:flex-initial w-full py-2.5 px-4 rounded-xl text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center justify-center gap-1.5 cursor-not-allowed"
                                    >
                                        <i class="fas fa-check-circle"></i>
                                        <span>Applied</span>
                                    </button>
                                @else
                                    <button 
                                        type="button" 
                                        onclick='openApplyModal({{ $job->id }}, @json($job->title), @json($companyName))' 
                                        class="btn-ipdp-primary flex-1 sm:flex-initial w-full py-2.5 px-4 rounded-xl text-xs font-black flex items-center justify-center gap-1.5 cursor-pointer transition-all"
                                    >
                                        <span>Apply Now</span>
                                        <i class="fas fa-paper-plane text-[10px]"></i>
                                    </button>
                                @endif

                                <button 
                                    type="button"
                                    onclick='openJobBriefDrawer(@json($jobBriefPayload))'
                                    class="liquid-glass-pill flex-1 sm:flex-initial w-full py-2 px-3.5 rounded-xl text-xs font-bold text-[#041536] hover:bg-[#041536] hover:text-white transition-all flex items-center justify-center gap-1.5 cursor-pointer"
                                >
                                    <i class="far fa-file-alt text-[11px]"></i>
                                    <span>View Details</span>
                                </button>
                            </div>

                        </div>
                    </article>
                @empty
                    <div class="py-16 px-6 text-center liquid-glass-card rounded-3xl">
                        <div class="w-16 h-16 rounded-2xl bg-[#051c45]/10 text-[#051c45] flex items-center justify-center mx-auto mb-4 text-2xl">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <h3 class="text-lg font-black text-[#041536] mb-1">No Matching Jobs Found</h3>
                        <p class="text-xs sm:text-sm text-slate-600 mb-6 max-w-md mx-auto">
                            We couldn't find any active positions matching your current search or filter. Try clearing your filters to browse all open roles.
                        </p>
                        <a href="{{ route('jobs.index') }}" class="btn-ipdp-primary inline-flex items-center gap-2 px-6 py-3 rounded-xl text-xs font-black shadow-md">
                            <i class="fas fa-sync-alt text-[10px]"></i>
                            <span>Show All Open Jobs</span>
                        </a>
                    </div>
                @endforelse
                </div>

                <!-- Employer Recruitment Banner -->
                <div id="employer-post-callout" class="liquid-glass-card rounded-2xl p-6 sm:p-7 mt-6 flex flex-col sm:flex-row items-center justify-between gap-5">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-[#051c45] text-white flex items-center justify-center text-xl shrink-0 shadow-md">
                            <i class="fas fa-handshake text-emerald-400"></i>
                        </div>
                        <div>
                            <h3 class="text-sm sm:text-base font-black text-[#041536]">
                                Hiring Commercial, L/C, Supply Chain, or VAT Professionals?
                            </h3>
                            <p class="text-xs text-slate-600 mt-0.5">
                                Post your organization's job openings for free on the {{ $siteName }} Placement Portal.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5 shrink-0 w-full sm:w-auto">
                        <a href="{{ route('contact.index') }}" class="btn-cab-secondary px-5 py-2.5 rounded-xl text-xs font-black whitespace-nowrap w-full sm:w-auto text-center">
                            <i class="fas fa-bullhorn mr-1.5"></i>Post a Job Opening
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>

<!-- Slide-Over Drawer: Full Job Description & Company Brief -->
<div id="jobBriefDrawer" class="fixed inset-0 z-50 hidden">
    <div onclick="closeJobBriefDrawer()" class="fixed inset-0 bg-[#041536]/60 backdrop-blur-sm transition-opacity"></div>
    <div class="fixed inset-y-0 right-0 max-w-full w-full sm:max-w-xl bg-white shadow-2xl z-10 flex flex-col justify-between overflow-hidden border-l border-slate-200">
        <!-- Drawer Header -->
        <div class="p-6 bg-gradient-to-r from-[#020b1c] via-[#051c45] to-[#0a2540] text-white relative">
            <button onclick="closeJobBriefDrawer()" class="absolute top-5 right-5 w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors cursor-pointer">
                <i class="fas fa-times text-sm"></i>
            </button>

            <div class="flex items-start gap-4 pr-8">
                <!-- Auto-Fitted Logo or Monogram inside Drawer -->
                <div id="briefLogoBox" class="w-14 h-14 rounded-2xl bg-white p-1.5 flex items-center justify-center shrink-0 shadow-md overflow-hidden">
                    <img id="briefLogoImg" src="" alt="" class="w-full h-full object-contain hidden">
                    <span id="briefLogoInitials" class="text-base font-black text-[#051c45]">CA</span>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span id="briefJobType" class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-[10px] font-black uppercase tracking-wider">
                            Full-time
                        </span>
                        <span id="briefPostedAgo" class="text-[11px] text-slate-300 font-medium"></span>
                    </div>
                    <h3 id="briefTitle" class="text-lg sm:text-xl font-black text-white leading-snug"></h3>
                    <p id="briefCompany" class="text-xs sm:text-sm text-slate-300 font-semibold mt-0.5"></p>
                </div>
            </div>
        </div>

        <!-- Drawer Body -->
        <div class="flex-1 overflow-y-auto p-6 space-y-6">
            <!-- Skeleton Shimmer inside Drawer during role switch -->
            <div id="briefSkeleton" class="space-y-5 hidden">
                <div class="grid grid-cols-2 gap-3">
                    <div class="h-16 rounded-2xl skeleton-shimmer"></div>
                    <div class="h-16 rounded-2xl skeleton-shimmer"></div>
                </div>
                <div class="space-y-2.5">
                    <div class="h-4 w-48 rounded skeleton-shimmer"></div>
                    <div class="h-36 rounded-2xl skeleton-shimmer"></div>
                </div>
            </div>

            <div id="briefContent" class="space-y-6">
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Location</span>
                        <span id="briefLocation" class="text-xs sm:text-sm font-extrabold text-[#041536] mt-0.5 block"></span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-200/80">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 block">Salary / Package</span>
                        <span id="briefSalary" class="text-xs sm:text-sm font-extrabold text-emerald-900 mt-0.5 block"></span>
                    </div>
                </div>

                <div>
                    <h4 class="text-xs font-black uppercase tracking-wider text-[#041536] mb-2.5 flex items-center gap-1.5">
                        <i class="fas fa-file-alt text-[#c30102]"></i>
                        <span>Full Job Description & Requirements</span>
                    </h4>
                    <div id="briefDescription" class="text-xs sm:text-sm text-slate-700 leading-relaxed whitespace-pre-line bg-slate-50/70 p-4 rounded-2xl border border-slate-200/70"></div>
                </div>
            </div>
        </div>

        <!-- Drawer Footer CTA -->
        <div class="p-5 bg-slate-50 border-t border-slate-200/80 flex items-center justify-between gap-3">
            <button type="button" onclick="closeJobBriefDrawer()" class="px-4 py-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-100 cursor-pointer">
                Close
            </button>
            <a id="briefExternalApplyBtn" href="#" target="_blank" rel="noopener noreferrer" class="btn-ipdp-primary flex-1 py-3 px-5 rounded-xl text-xs font-black text-center hidden items-center justify-center gap-2">
                <span>Apply on Company Website</span>
                <i class="fas fa-external-link-alt text-[10px]"></i>
            </a>
            <button id="briefModalApplyBtn" type="button" class="btn-ipdp-primary flex-1 py-3 px-5 rounded-xl text-xs font-black flex items-center justify-center gap-2 cursor-pointer">
                <span>Apply Now</span>
                <i class="fas fa-paper-plane text-[10px]"></i>
            </button>
        </div>
    </div>
</div>

<!-- Candidate Application Modal (Authenticated & Guest Support) -->
<div id="applyModal" class="fixed inset-0 bg-[#041536]/65 backdrop-blur-sm flex items-center justify-center z-50 hidden p-4">
    <div class="liquid-glass-card !bg-white rounded-3xl p-6 sm:p-8 w-full max-w-lg shadow-2xl border border-slate-200 relative">
        <div class="flex justify-between items-start mb-5 pb-4 border-b border-slate-200/80">
            <div>
                <span class="inline-flex items-center gap-1.5 text-[10px] font-black uppercase tracking-wider text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 rounded-full">
                    <i class="fas fa-shield-alt text-[9px]"></i>
                    <span>{{ $siteName }} Easy Apply</span>
                </span>
                <h3 class="text-lg font-black text-[#041536] mt-1.5 leading-snug">
                    Apply for: <span id="applyJobTitle" class="text-[#c30102]"></span>
                </h3>
                <p id="applyCompany" class="text-xs text-slate-500 font-bold mt-0.5"></p>
            </div>
            <button onclick="closeApplyModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors cursor-pointer">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>

        @auth
            <form method="POST" id="applyForm" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between text-xs">
                        <div>
                            <span class="text-slate-400 font-bold block text-[10px] uppercase">Applicant</span>
                            <span class="font-extrabold text-[#041536]">{{ auth()->user()->name }}</span>
                            <span class="text-slate-500 block text-[11px]">{{ auth()->user()->email }}</span>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[10px] border border-emerald-200">
                            Verified Profile
                        </span>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-[#041536] uppercase tracking-wider mb-1.5">
                            Upload Resume / CV (PDF, DOC, DOCX • Max 5MB) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="file" 
                            name="resume" 
                            accept=".pdf,.doc,.docx" 
                            required 
                            class="w-full text-xs text-slate-700 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-[#041536] file:text-white hover:file:bg-[#c30102] file:cursor-pointer cursor-pointer border border-slate-300 rounded-xl p-1.5 bg-white"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-black text-[#041536] uppercase tracking-wider mb-1.5">
                            Cover Note / Key Qualifications (Optional)
                        </label>
                        <textarea 
                            name="cover_letter" 
                            rows="4" 
                            class="w-full rounded-xl border border-slate-300 p-3 text-xs sm:text-sm text-[#041536] focus:border-[#051c45] focus:ring-2 focus:ring-[#051c45]/15 focus:outline-none resize-none" 
                            placeholder="Briefly mention your relevant commercial, L/C, supply chain, or tax experience..."
                        ></textarea>
                    </div>

                    <div class="pt-2 flex items-center gap-3">
                        <button type="button" onclick="closeApplyModal()" class="px-4 py-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-600 hover:bg-slate-100 cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" class="btn-ipdp-primary flex-1 py-3 rounded-xl text-xs sm:text-sm font-black shadow-md flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fas fa-paper-plane"></i>
                            <span>Submit Application</span>
                        </button>
                    </div>
                </div>
            </form>
        @else
            <div class="space-y-5 text-center py-2">
                <div class="w-14 h-14 rounded-2xl bg-[#051c45]/10 text-[#051c45] flex items-center justify-center mx-auto text-2xl border border-[#051c45]/20">
                    <i class="fas fa-user-check"></i>
                </div>
                <div>
                    <h4 class="text-base font-black text-[#041536] mb-1">
                        Sign In to Submit Your CV
                    </h4>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-sm mx-auto">
                        Sign in using your <strong>mobile number or email</strong> and your 4+ digit password/PIN so our placement desk can attach your verified {{ $siteName }} certificates.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    <a href="{{ route('login') }}" class="btn-ipdp-primary w-full py-3 rounded-xl text-xs font-black shadow-md flex items-center justify-center gap-2">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Sign In to Apply</span>
                    </a>
                    <a href="https://wa.me/{{ \App\Models\Setting::getVal('contact_whatsapp', '8801917054664') }}" target="_blank" rel="noopener noreferrer" class="bg-[#25D366] hover:bg-[#20ba5a] text-white w-full py-3 rounded-xl text-xs font-black shadow-md flex items-center justify-center gap-2 transition-all">
                        <i class="fab fa-whatsapp"></i>
                        <span>Apply via WhatsApp</span>
                    </a>
                </div>
            </div>
        @endauth
    </div>
</div>

<script>
    function showJobPortalSkeleton() {
        const skel = document.getElementById('job-feed-skeleton');
        const res = document.getElementById('job-feed-results');
        if (skel && res) {
            res.classList.add('hidden');
            skel.classList.remove('hidden');
        }
    }

    // Mark already-cached logo images as loaded immediately
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.img-skeleton-wrapper img').forEach(function(img) {
            if (img.complete) {
                img.parentElement.classList.add('is-loaded');
            }
        });
    });

    function openApplyModal(jobId, jobTitle, companyName) {
        closeJobBriefDrawer();
        document.getElementById('applyJobTitle').innerText = jobTitle;
        const compEl = document.getElementById('applyCompany');
        if (compEl) {
            compEl.innerText = companyName || 'Corporate Partner';
        }
        const form = document.getElementById('applyForm');
        if (form) {
            form.action = `/jobs/${jobId}/apply`;
        }
        document.getElementById('applyModal').classList.remove('hidden');
    }

    function closeApplyModal() {
        document.getElementById('applyModal').classList.add('hidden');
    }

    function openJobBriefDrawer(job) {
        document.getElementById('briefTitle').innerText = job.title || '';
        document.getElementById('briefCompany').innerText = job.company || '';
        document.getElementById('briefLocation').innerText = job.location || 'Dhaka, Bangladesh';
        document.getElementById('briefJobType').innerText = job.job_type || 'Full-time';
        document.getElementById('briefSalary').innerText = job.salary_range || 'Negotiable';
        document.getElementById('briefPostedAgo').innerText = job.posted_ago ? ('• ' + job.posted_ago) : '';
        document.getElementById('briefDescription').innerText = job.description || '';

        const logoImg = document.getElementById('briefLogoImg');
        const logoInit = document.getElementById('briefLogoInitials');
        if (job.logo_url) {
            logoImg.src = job.logo_url;
            logoImg.alt = job.company || '';
            logoImg.classList.remove('hidden');
            logoInit.classList.add('hidden');
            logoImg.onerror = function() {
                logoImg.classList.add('hidden');
                logoInit.classList.remove('hidden');
                logoInit.innerText = job.initials || 'CA';
            };
        } else {
            logoImg.classList.add('hidden');
            logoInit.classList.remove('hidden');
            logoInit.innerText = job.initials || 'CA';
        }

        const extBtn = document.getElementById('briefExternalApplyBtn');
        const modBtn = document.getElementById('briefModalApplyBtn');

        if (job.apply_link) {
            extBtn.href = job.apply_link;
            extBtn.classList.remove('hidden');
            extBtn.classList.add('inline-flex');
            modBtn.classList.add('hidden');
        } else {
            extBtn.classList.add('hidden');
            extBtn.classList.remove('inline-flex');
            modBtn.classList.remove('hidden');
            if (job.has_applied) {
                modBtn.innerText = 'Application Already Submitted ✓';
                modBtn.disabled = true;
                modBtn.onclick = null;
            } else {
                modBtn.innerHTML = '<span>Apply Now</span> <i class="fas fa-paper-plane text-[10px]"></i>';
                modBtn.disabled = false;
                modBtn.onclick = function() {
                    openApplyModal(job.id, job.title, job.company);
                };
            }
        }

        document.getElementById('jobBriefDrawer').classList.remove('hidden');
        const bSkel = document.getElementById('briefSkeleton');
        const bCont = document.getElementById('briefContent');
        if (bSkel && bCont) {
            bCont.classList.add('hidden');
            bSkel.classList.remove('hidden');
            setTimeout(function() {
                bSkel.classList.add('hidden');
                bCont.classList.remove('hidden');
            }, 160);
        }
    }

    function closeJobBriefDrawer() {
        document.getElementById('jobBriefDrawer')?.classList.add('hidden');
    }

    document.getElementById('applyModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeApplyModal();
        }
    });
</script>
@endsection
