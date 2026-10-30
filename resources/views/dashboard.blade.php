@extends('layouts.student')

@section('header_title', 'My Dashboard')

@section('content')
@php
    $hour = (int) now()->format('H');
    $timeGreeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
    $completedIds = $completedLessonIds ?? (auth()->check() ? auth()->user()->completedLessons()->pluck('lesson_id')->toArray() : []);
    $pendingPaymentsList = $pendingPayments ?? ($payments ?? collect())->whereIn('status', ['pending', 'processing']);

    // Compute overall learning metrics across enrolled courses
    $totalAllLessons = 0;
    $doneAllLessons = 0;
    $earnedCertificatesCount = 0;
    foreach ($enrolledCourses as $ec) {
        $ecLessons = $ec->relationLoaded('sections') ? $ec->sections->flatMap->lessons : collect();
        $totalAllLessons += $ecLessons->count();
        $doneAllLessons += $ecLessons->whereIn('id', $completedIds)->count();
        if ($ec->certificate_enabled || ($ec->relationLoaded('certificates') ? $ec->certificates->isNotEmpty() : $ec->isCertificateAvailableFor(auth()->user()))) {
            $earnedCertificatesCount++;
        }
    }
    $overallProgressPct = $totalAllLessons > 0 ? round(($doneAllLessons / $totalAllLessons) * 100) : 0;

    // Spotlight active course for quick 1-click resume
    $spotlightCourse = $enrolledCourses->first();
    $spotlightLessons = ($spotlightCourse && $spotlightCourse->relationLoaded('sections')) ? $spotlightCourse->sections->flatMap->lessons : collect();
    $spotlightNextLesson = $spotlightLessons->first(fn($l) => !in_array($l->id, $completedIds)) ?? $spotlightLessons->first();
    $spotlightDoneCount = $spotlightLessons->whereIn('id', $completedIds)->count();
    $spotlightTotalCount = $spotlightLessons->count();
    $spotlightPct = $spotlightTotalCount > 0 ? round(($spotlightDoneCount / $spotlightTotalCount) * 100) : 0;
@endphp

<div class="space-y-6">
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-bold flex items-center gap-2.5 shadow-2xs">
            <i class="fas fa-check-circle text-emerald-600 text-base shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm font-bold flex items-center gap-2.5 shadow-2xs">
            <i class="fas fa-exclamation-circle text-[#c30102] text-base shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- 1. Executive Student Command Hero Banner (With Integrated Active Classroom Spotlight) -->
    <div class="bg-gradient-to-br from-[#020c21] via-[#051c45] to-[#0b2d6b] rounded-3xl p-5 sm:p-7 text-white shadow-xl border border-white/10 relative overflow-hidden">
        <div class="absolute -top-24 -right-24 w-72 h-72 rounded-full bg-[#c30102]/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-72 h-72 rounded-full bg-emerald-500/15 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
            <div class="{{ $spotlightCourse ? 'lg:col-span-7' : 'lg:col-span-12' }}">
                <div class="flex flex-wrap items-center gap-2 mb-2.5">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-500/15 border border-emerald-400/30 text-[10px] font-extrabold uppercase tracking-wider text-emerald-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Executive Learning Workspace</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/10 border border-white/15 text-[10px] font-bold text-sky-200">
                        <i class="fas fa-user-shield text-[9px]"></i>
                        <span>{{ Auth::user()->name }}</span>
                    </span>
                </div>

                <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-white">{{ $timeGreeting }}, {{ explode(' ', Auth::user()->name)[0] }}.</h2>
                <p class="text-slate-300 text-xs sm:text-sm mt-1 max-w-xl leading-relaxed">
                    Ready to continue your {{ Auth::user()->preferences['level'] ?? 'executive studies' }}? Access your classrooms, verified certificates, tuition receipts, and career placement opportunities below.
                </p>

                <div class="flex items-center gap-2 flex-wrap mt-4">
                    <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-[#c30102] hover:bg-[#a50102] text-xs font-extrabold text-white shadow-lg shadow-[#c30102]/25 transition-all">
                        <i class="fas fa-graduation-cap text-xs"></i>
                        <span>Browse Courses</span>
                    </a>
                    <a href="{{ route('jobs.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-xs font-bold text-white transition-all">
                        <i class="fas fa-briefcase text-amber-300 text-xs"></i>
                        <span>Career Portal</span>
                    </a>
                    <a href="{{ route('wishlist.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-xs font-bold text-white transition-all">
                        <i class="fas fa-heart text-[#ff6b6b] text-xs"></i>
                        <span>Wishlist</span>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-xs font-bold text-white transition-all">
                        <i class="fas fa-user-cog text-sky-300 text-xs"></i>
                        <span>Profile</span>
                    </a>
                </div>
            </div>

            @if($spotlightCourse)
                <!-- Embedded Active Course Resume Spotlight Card -->
                <div class="lg:col-span-5 bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-4 sm:p-5 shadow-lg">
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <span class="text-[10px] font-black uppercase tracking-wider text-emerald-300 flex items-center gap-1.5">
                            <i class="fas fa-play-circle"></i>
                            <span>Active Classroom Spotlight</span>
                        </span>
                        <span class="px-2 py-0.5 rounded-full bg-white/15 text-[10px] font-bold text-white">{{ $spotlightPct }}% Complete</span>
                    </div>
                    <h3 class="font-extrabold text-sm sm:text-base text-white line-clamp-1">{{ $spotlightCourse->title }}</h3>
                    <p class="text-[11px] text-slate-300 mt-0.5 truncate">
                        @if($spotlightNextLesson)
                            Next up: <span class="text-white font-semibold">{{ $spotlightNextLesson->title }}</span>
                        @else
                            {{ $spotlightCourse->venue ?? 'Online Live (Zoom)' }}
                        @endif
                    </p>
                    <div class="w-full bg-white/15 rounded-full h-1.5 overflow-hidden my-3">
                        <div class="bg-gradient-to-r from-emerald-400 to-sky-400 h-1.5 rounded-full transition-all duration-500" style="width: {{ max(6, $spotlightPct) }}%"></div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ $spotlightNextLesson ? route('lessons.show', [$spotlightCourse, $spotlightNextLesson]) : route('courses.show', $spotlightCourse) }}" class="flex-1 py-2 px-3 rounded-xl bg-white text-[#051c45] hover:bg-slate-100 font-extrabold text-xs text-center shadow-xs transition-colors">
                            Resume Classroom &rarr;
                        </a>
                        <a href="{{ route('courses.show', $spotlightCourse) }}" class="py-2 px-3 rounded-xl bg-white/10 hover:bg-white/20 border border-white/15 text-white font-bold text-xs transition-colors">
                            Syllabus
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- 2. 4-Card Liquid Glass Student KPI Strip -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Enrolled Courses -->
        <div class="liquid-glass-card bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Enrolled Programs</span>
                <span class="w-8 h-8 rounded-xl bg-[#051c45]/[0.08] text-[#051c45] flex items-center justify-center text-xs">
                    <i class="fas fa-graduation-cap"></i>
                </span>
            </div>
            <p class="text-2xl font-black text-[#051c45]">{{ $enrolledCourses->count() }}</p>
            <p class="text-[11px] font-semibold text-slate-500 mt-1">
                @if($pendingPaymentsList->count() > 0)
                    <span class="text-amber-600">{{ $pendingPaymentsList->count() }} Seat Pending Approval</span>
                @else
                    Active Executive Cohorts
                @endif
            </p>
        </div>

        <!-- Lessons Completed -->
        <div class="liquid-glass-card bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Lessons Completed</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                    <i class="fas fa-check-double"></i>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <p class="text-2xl font-black text-[#051c45]">{{ count($completedIds) }}</p>
                @if($totalAllLessons > 0)
                    <span class="text-xs font-bold text-emerald-600">({{ $overallProgressPct }}%)</span>
                @endif
            </div>
            <p class="text-[11px] font-semibold text-slate-500 mt-1">
                {{ $doneAllLessons }}/{{ $totalAllLessons }} Syllabus Modules Done
            </p>
        </div>

        <!-- Earned / Available Certificates -->
        <a href="{{ route('certificate.verify') }}" class="liquid-glass-card bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 hover:border-[#051c45]/30 transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Credentials</span>
                <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xs">
                    <i class="fas fa-award"></i>
                </span>
            </div>
            <p class="text-2xl font-black text-[#051c45]">{{ $earnedCertificatesCount }}</p>
            <p class="text-[11px] font-semibold text-emerald-700 mt-1">
                Verified Certificate Registry &rarr;
            </p>
        </a>

        <!-- Level, XP & Streak -->
        <a href="{{ route('performance.index') }}" class="liquid-glass-card bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 hover:border-[#051c45]/30 transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Level {{ Auth::user()->level }}</span>
                <span class="px-2 py-0.5 rounded-full bg-orange-50 border border-orange-200/70 text-orange-600 text-[10px] font-extrabold">
                    🔥 {{ Auth::user()->current_streak }} Days
                </span>
            </div>
            <div class="flex items-baseline justify-between">
                <p class="text-lg sm:text-xl font-black text-[#051c45]">{{ Auth::user()->xp }} / {{ Auth::user()->next_level_xp }} XP</p>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2 overflow-hidden">
                <div class="bg-gradient-to-r from-[#051c45] to-[#c30102] h-1.5 rounded-full" style="width: {{ min(100, (Auth::user()->xp / max(1, Auth::user()->next_level_xp)) * 100) }}%"></div>
            </div>
        </a>
    </div>

    <!-- 3. Pending Seat Verification Spotlight (Shown immediately when student has a pending payment) -->
    @if($pendingPaymentsList->isNotEmpty())
        <div class="liquid-glass-card rounded-2xl border border-amber-300/90 bg-gradient-to-r from-amber-50/90 via-white to-amber-50/50 p-5 shadow-xs space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2.5 border-b border-amber-200/60">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center text-xs shadow-xs">
                        <i class="fas fa-hourglass-half"></i>
                    </span>
                    <div>
                        <h3 class="text-sm font-black text-[#051c45] uppercase tracking-wider">Reserved Seat &bull; Payment Verification in Progress</h3>
                        <p class="text-xs text-slate-600">Your tuition submission is queued with our academic counselors. Classroom access unlocks automatically upon verification.</p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-full bg-amber-100 border border-amber-300 text-amber-800 text-[11px] font-extrabold uppercase tracking-wider shrink-0 self-start sm:self-center">
                    {{ $pendingPaymentsList->count() }} Pending {{ \Illuminate\Support\Str::plural('Verification', $pendingPaymentsList->count()) }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($pendingPaymentsList as $pendPay)
                    <div class="p-3.5 rounded-xl bg-white border border-slate-200/80 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-xs font-extrabold text-[#051c45] truncate">{{ $pendPay->course?->title ?? 'Executive Program' }}</p>
                            <div class="flex flex-wrap items-center gap-2 text-[11px] text-slate-500 mt-1">
                                <span>Fee: <strong class="text-[#051c45]">৳{{ number_format($pendPay->amount, 2) }}</strong></span>
                                <span>&bull;</span>
                                <span>TrxID: <strong class="font-mono text-[#c30102]">{{ $pendPay->transaction_id }}</strong></span>
                            </div>
                        </div>
                        <span class="px-3 py-1.5 rounded-xl bg-amber-50 border border-amber-200/80 text-amber-800 text-[10px] font-bold shrink-0 inline-flex items-center gap-1.5 select-none" title="Official invoice download unlocks once payment is verified by an administrator">
                            <i class="fas fa-lock text-[9px] text-amber-600"></i>
                            <span>Awaiting Approval</span>
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- 4. Latest Academic Announcements & Course Notices (If any) -->
    @if(isset($recentNotifications) && $recentNotifications->isNotEmpty())
        <div class="liquid-glass-card bg-white rounded-2xl border border-slate-200/80 p-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                <div class="flex items-center gap-2">
                    <i class="fas fa-bullhorn text-[#c30102] text-xs"></i>
                    <h3 class="font-extrabold text-xs sm:text-sm text-[#051c45] uppercase tracking-wider">Academic Notices &amp; Course Announcements</h3>
                </div>
                <a href="{{ route('notifications.index') }}" class="text-xs font-bold text-[#051c45] hover:text-[#c30102] transition-colors">
                    All Notices &rarr;
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                @foreach($recentNotifications as $notif)
                    <a href="{{ route('notifications.index') }}" class="p-3.5 rounded-xl border {{ $notif->read_at ? 'border-slate-100 bg-slate-50/50' : 'border-[#051c45]/20 bg-[#051c45]/[0.03]' }} hover:border-[#051c45]/40 transition-all block">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="text-xs font-extrabold text-[#051c45] truncate">{{ $notif->data['title'] ?? 'Academic Notice' }}</span>
                            @if(!$notif->read_at)
                                <span class="w-2 h-2 rounded-full bg-[#c30102] shrink-0"></span>
                            @endif
                        </div>
                        <p class="text-[11px] text-slate-600 line-clamp-2">{{ $notif->data['message'] ?? '' }}</p>
                        <span class="block text-[10px] text-slate-400 font-semibold mt-1.5">{{ $notif->created_at?->diffForHumans() }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- 5. Enrolled Courses Section -->
    @if($enrolledCourses->isNotEmpty())
        <div>
            <div class="flex justify-between items-end mb-4">
                <div>
                    <h3 class="font-extrabold text-xl text-[#051c45]">My Enrolled Courses ({{ $enrolledCourses->count() }})</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Access your live Zoom sessions, video modules, downloadable resources, and certificates.</p>
                </div>
                <a href="{{ route('catalog.index') }}" class="text-[#051c45] hover:text-[#c30102] text-xs sm:text-sm font-bold transition-colors">Browse Catalog &rarr;</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($enrolledCourses as $course)
                    @php
                        $courseLessons = $course->relationLoaded('sections') ? $course->sections->flatMap->lessons : collect();
                        $totalCourseLessons = $courseLessons->count();
                        $doneCourseLessons = $totalCourseLessons > 0 ? $courseLessons->whereIn('id', $completedIds)->count() : 0;
                        $coursePct = $totalCourseLessons > 0 ? round(($doneCourseLessons / $totalCourseLessons) * 100) : 0;
                        $nextLesson = $courseLessons->first(fn($l) => !in_array($l->id, $completedIds)) ?? $courseLessons->first();
                    @endphp
                    <div class="liquid-glass-card bg-white rounded-2xl border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)] overflow-hidden group hover:shadow-[0_8px_30px_rgba(5,28,69,0.08)] transition-all duration-300 flex flex-col">
                        @if($course->cover_image_url)
                            <div class="w-full h-44 img-skeleton-wrapper relative overflow-hidden">
                                <img src="{{ $course->cover_image_url }}" alt="{{ $course->title }}" onload="this.parentElement.classList.add('is-loaded')" class="w-full h-full object-cover relative z-10 group-hover:scale-105 transition-transform duration-500">
                                <span class="absolute top-3 left-3 z-20 px-2.5 py-1 rounded-lg bg-[#051c45]/90 backdrop-blur-md text-white text-[10px] font-bold uppercase tracking-wider">
                                    {{ $course->category ?? 'Enrolled' }}
                                </span>
                                <span class="absolute top-3 right-3 z-20 px-2.5 py-1 rounded-lg bg-white/95 backdrop-blur-md text-[#051c45] text-[10px] font-extrabold">
                                    {{ $course->isOnlineOnly() ? 'Online Zoom' : ($course->isCampusOnly() ? 'Campus' : 'Hybrid') }}
                                </span>
                            </div>
                        @else
                            <div class="w-full h-44 bg-gradient-to-br from-[#051c45] to-[#0a2e6e] flex items-center justify-center text-white font-bold text-4xl shadow-inner">
                                {{ substr($course->title, 0, 1) }}
                            </div>
                        @endif
                        <div class="p-5 flex-1 flex flex-col">
                            <h4 class="font-bold text-base sm:text-lg text-gray-900 mb-1.5 line-clamp-1">{{ $course->title }}</h4>
                            <p class="text-xs sm:text-sm text-gray-500 mb-3 line-clamp-2">{{ Str::limit($course->description, 80) }}</p>

                            <!-- Lesson Progress Bar -->
                            @if($totalCourseLessons > 0)
                                <div class="mb-4 bg-slate-50 rounded-xl p-3 border border-slate-100">
                                    <div class="flex items-center justify-between text-[11px] font-bold mb-1.5">
                                        <span class="text-slate-600">{{ $doneCourseLessons }}/{{ $totalCourseLessons }} Lessons</span>
                                        <span class="text-[#051c45]">{{ $coursePct }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-gradient-to-r from-[#051c45] to-[#10b981] h-1.5 rounded-full transition-all duration-500" style="width: {{ $coursePct }}%"></div>
                                    </div>
                                </div>
                            @endif

                            <div class="mt-auto pt-3 border-t border-gray-100 space-y-2">
                                <a href="{{ $nextLesson ? route('lessons.show', [$course, $nextLesson]) : route('courses.show', $course) }}" class="block w-full py-2.5 px-4 bg-[#051c45] text-white font-semibold rounded-xl text-center hover:bg-[#03112b] transition-colors shadow-sm text-xs sm:text-sm">
                                    Continue Learning
                                </a>
                                @if($nextLesson)
                                    <a href="{{ route('courses.show', $course) }}" class="block w-full py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-center transition-colors text-xs">
                                        Course Syllabus &amp; Materials
                                    </a>
                                @endif
                                @php
                                    $hasCertificate = $course->certificate_enabled || ($course->relationLoaded('certificates') ? $course->certificates->isNotEmpty() : $course->isCertificateAvailableFor(auth()->user()));
                                @endphp
                                @if($hasCertificate)
                                    <a href="{{ route('courses.certificate', $course) }}" target="_blank" class="block w-full py-2 px-4 bg-emerald-50 text-emerald-700 font-semibold rounded-xl text-center hover:bg-emerald-100 transition-colors shadow-sm text-xs flex items-center justify-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                                        Digital Certificate
                                    </a>
                                @else
                                    <div class="w-full py-2 px-3 bg-slate-50 text-slate-400 font-medium rounded-xl text-center text-xs flex items-center justify-center gap-1.5 border border-slate-100 cursor-not-allowed select-none" title="Certificate pending administrative release upon cohort evaluation">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                        <span>Certificate Pending Release</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @elseif($pendingPaymentsList->isEmpty())
        <div class="bg-gradient-to-br from-[#051c45] to-[#0a2e6e] rounded-3xl p-6 md:p-8 text-white shadow-lg relative overflow-hidden group">
            <svg class="absolute right-0 bottom-0 opacity-10 transform translate-x-4 translate-y-4 group-hover:scale-110 transition-transform duration-500" width="160" height="160" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            <div class="relative z-10">
                <span class="text-slate-300 font-medium text-sm tracking-wide uppercase mb-1 block">Welcome</span>
                <h3 class="text-2xl font-bold mb-4 max-w-md leading-tight">You are not enrolled in any courses yet.</h3>
                <p class="text-slate-200 mb-6 max-w-md">Discover our catalog and start your executive certification journey today.</p>
                <a href="{{ route('catalog.index') }}" class="inline-block bg-[#c30102] hover:bg-[#a60102] text-white font-bold px-6 py-3 rounded-xl transition-colors shadow-sm">
                    Browse Catalog
                </a>
            </div>
        </div>
    @endif

    <!-- 6. Recommended Courses Feed -->
    @if(isset($feedCourses) && $feedCourses->isNotEmpty())
    <div class="pt-2">
        <div class="flex justify-between items-end mb-4">
            <div>
                <h3 class="font-extrabold text-xl text-[#051c45]">Recommended Executive Courses</h3>
                <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Explore our latest additions to expand your commercial and supply chain mastery.</p>
            </div>
            <a href="{{ route('catalog.index') }}" class="text-[#051c45] hover:text-[#c30102] text-xs sm:text-sm font-bold transition-colors">View all &rarr;</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($feedCourses as $course)
                <a href="{{ route('courses.show', $course) }}" class="liquid-glass-card group bg-white rounded-3xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-gray-100 overflow-hidden hover:shadow-[0_4px_25px_rgba(5,28,69,0.08)] transition-all duration-300 flex flex-col active:scale-[0.98]">
                    <!-- Thumbnail -->
                    <div class="w-full h-44 bg-gray-100 relative overflow-hidden flex items-center justify-center img-skeleton-wrapper">
                        <img src="{{ $course->cover_image_url }}" alt="{{ $course->title }}" onload="this.parentElement.classList.add('is-loaded')" class="w-full h-full object-cover relative z-10 group-hover:scale-105 transition-transform duration-500">
                        
                        @if($course->price > 0)
                            <div class="absolute top-3 right-3 z-20 bg-white/95 backdrop-blur-sm text-[#051c45] font-bold px-3 py-1 rounded-full text-xs shadow-sm">
                                ৳{{ number_format($course->price, 2) }}
                            </div>
                        @else
                            <div class="absolute top-3 right-3 z-20 bg-[#10b981] text-white font-bold px-3 py-1 rounded-full text-xs shadow-sm">
                                Free
                            </div>
                        @endif
                    </div>
                    <!-- Content -->
                    <div class="p-5 flex-1 flex flex-col">
                        <h3 class="font-bold text-gray-900 text-base sm:text-lg mb-2 line-clamp-2 group-hover:text-[#051c45] transition-colors">{{ $course->title }}</h3>
                        <p class="text-xs sm:text-sm text-gray-500 line-clamp-2 mb-4 flex-1">{{ $course->description }}</p>
                        <div class="flex items-center justify-between mt-auto pt-3 border-t border-gray-50">
                            <span class="text-xs sm:text-sm font-bold text-[#051c45] flex items-center gap-1 w-full justify-center bg-slate-100 py-2 rounded-xl group-hover:bg-[#051c45] group-hover:text-white transition-colors">
                                View Details <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- 7. Career Opportunities Feed -->
    @if(isset($feedJobs) && $feedJobs->isNotEmpty())
    <div class="pt-2">
        <div class="flex justify-between items-end mb-4">
            <div>
                <h3 class="font-extrabold text-xl text-[#051c45]">Career Opportunities</h3>
                <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Exclusive corporate placement openings for our verified students.</p>
            </div>
            <a href="{{ route('jobs.index') }}" class="text-[#051c45] hover:text-[#c30102] text-xs sm:text-sm font-bold transition-colors">View all &rarr;</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($feedJobs as $job)
                @php
                    $words = preg_split('/[\s,\-&]+/', trim($job->company ?? 'Company'));
                    $initials = count($words) >= 2
                        ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1))
                        : strtoupper(substr($job->company ?? 'CO', 0, 2));
                @endphp
                <a href="{{ route('jobs.index', ['search' => $job->title]) }}" class="liquid-glass-card bg-white rounded-3xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-gray-100 overflow-hidden flex flex-col p-5 hover:shadow-[0_4px_25px_rgba(5,28,69,0.08)] transition-all duration-300 active:scale-[0.98] group">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex items-center gap-3.5 min-w-0">
                            @if($job->company_logo)
                                <div class="w-12 h-12 rounded-xl bg-white border border-gray-200 p-1 shadow-xs shrink-0 flex items-center justify-center overflow-hidden">
                                    <img src="{{ storage_asset($job->company_logo) }}" alt="{{ $job->company }}" class="w-full h-full object-contain" onerror="this.parentElement.style.display='none'; this.parentElement.nextElementSibling.style.display='flex';">
                                </div>
                                <div style="display:none;" class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#051c45] to-[#1e3a8a] text-white flex items-center justify-center shrink-0 shadow-xs">
                                    <span class="text-xs font-black tracking-wider">{{ $initials }}</span>
                                </div>
                            @else
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#051c45] to-[#1e3a8a] text-white flex items-center justify-center shrink-0 shadow-xs">
                                    <span class="text-xs font-black tracking-wider">{{ $initials }}</span>
                                </div>
                            @endif
                            <div class="min-w-0">
                                <h3 class="font-bold text-gray-900 text-base leading-tight group-hover:text-[#c30102] transition-colors line-clamp-1">{{ $job->title }}</h3>
                                <p class="text-xs text-[#051c45] font-semibold mt-0.5 truncate">{{ $job->company }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 mb-3">
                        @if($job->location)
                            <div class="flex items-center text-xs text-gray-600 gap-1 bg-gray-50 px-2.5 py-1 rounded-lg border border-gray-100">
                                <i class="fas fa-map-marker-alt text-[#c30102] text-[10px]"></i>
                                <span>{{ $job->location }}</span>
                            </div>
                        @endif
                        @if($job->salary_range)
                            <div class="flex items-center text-xs text-emerald-700 font-semibold gap-1 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-100">
                                <i class="fas fa-coins text-emerald-600 text-[10px]"></i>
                                <span>{{ $job->salary_range }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="text-xs text-gray-600 mb-4 flex-1 line-clamp-2">
                        {{ $job->description }}
                    </div>

                    <div class="flex items-center justify-between mt-auto pt-3 border-t border-gray-50">
                        <span class="text-xs font-bold text-[#051c45] flex items-center gap-1.5 w-full justify-center bg-slate-100 py-2 rounded-xl group-hover:bg-[#051c45] group-hover:text-white transition-colors">
                            <span>View &amp; Apply</span>
                            <i class="fas fa-arrow-right text-[10px]"></i>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- 8. Tuition Payment History & Official Invoices + Job Applications Tracker -->
    @if($payments->isNotEmpty() || $jobApplications->isNotEmpty())
        <div class="pt-4 border-t border-slate-200/80 grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
            @if($payments->isNotEmpty())
                <div class="{{ $jobApplications->isNotEmpty() ? 'xl:col-span-7' : 'xl:col-span-12' }}">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-extrabold text-base sm:text-lg text-[#051c45]">Tuition Payments &amp; Official Invoices</h3>
                        <span class="text-xs font-semibold text-slate-400">{{ $payments->count() }} {{ \Illuminate\Support\Str::plural('Record', $payments->count()) }}</span>
                    </div>
                    <div class="liquid-glass-card bg-white rounded-2xl border border-gray-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs sm:text-sm text-gray-600">
                                <thead class="text-[11px] text-slate-500 uppercase bg-slate-50/90 border-b border-slate-100">
                                    <tr>
                                        <th scope="col" class="px-5 py-3.5 font-bold">Course &amp; TrxID</th>
                                        <th scope="col" class="px-5 py-3.5 font-bold">Amount</th>
                                        <th scope="col" class="px-5 py-3.5 font-bold">Date</th>
                                        <th scope="col" class="px-5 py-3.5 font-bold text-center">Status</th>
                                        <th scope="col" class="px-5 py-3.5 font-bold text-right">Invoice</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($payments as $payment)
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <td class="px-5 py-3.5">
                                            <div class="font-bold text-slate-900">{{ $payment->course?->title ?? 'Deleted Course' }}</div>
                                            <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                                                TrxID: {{ $payment->transaction_id }} &bull; {{ $payment->sender_number }}
                                            </div>
                                        </td>
                                        <td class="px-5 py-3.5 text-[#051c45] font-extrabold">৳{{ number_format($payment->amount, 2) }}</td>
                                        <td class="px-5 py-3.5 text-xs whitespace-nowrap">{{ $payment->created_at->format('M d, Y') }}</td>
                                        <td class="px-5 py-3.5 text-center">
                                            @if($payment->status === 'pending')
                                                <span class="bg-amber-50 border border-amber-200 text-amber-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full inline-flex items-center gap-1">
                                                    <i class="fas fa-clock text-[9px]"></i> Pending
                                                </span>
                                            @elseif($payment->status === 'processing')
                                                <span class="bg-sky-50 border border-sky-200 text-sky-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full inline-flex items-center gap-1">
                                                    <i class="fas fa-sync fa-spin text-[9px]"></i> Processing
                                                </span>
                                            @elseif($payment->status === 'verified')
                                                <span class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full inline-flex items-center gap-1">
                                                    <i class="fas fa-check-circle text-[9px]"></i> Verified
                                                </span>
                                            @else
                                                <span class="bg-rose-50 border border-rose-200 text-rose-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full inline-flex items-center gap-1">
                                                    <i class="fas fa-times-circle text-[9px]"></i> Rejected
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 text-right">
                                            @if($payment->status === 'verified')
                                                <a href="{{ route('payments.invoice', $payment) }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold text-[#051c45] bg-slate-100 hover:bg-[#051c45] hover:text-white transition-colors">
                                                    <i class="fas fa-file-invoice text-[10px]"></i>
                                                    <span>Invoice</span>
                                                </a>
                                            @elseif($payment->status === 'rejected')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-semibold text-rose-500 bg-rose-50/70 border border-rose-100 select-none" title="Payment was declined">
                                                    <i class="fas fa-ban text-[9px]"></i>
                                                    <span>Unavailable</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-semibold text-slate-400 bg-slate-50 border border-slate-200/70 select-none cursor-not-allowed" title="Invoice download unlocks after admin approves payment">
                                                    <i class="fas fa-lock text-[9px]"></i>
                                                    <span>Pending Approval</span>
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            @if($jobApplications->isNotEmpty())
                <div class="{{ $payments->isNotEmpty() ? 'xl:col-span-5' : 'xl:col-span-12' }}">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-extrabold text-base sm:text-lg text-[#051c45]">My Job Applications</h3>
                        <a href="{{ route('jobs.index') }}" class="text-xs font-bold text-[#051c45] hover:text-[#c30102]">Career Portal &rarr;</a>
                    </div>
                    <div class="liquid-glass-card bg-white rounded-2xl border border-gray-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs sm:text-sm text-gray-600">
                                <thead class="text-[11px] text-slate-500 uppercase bg-slate-50/90 border-b border-slate-100">
                                    <tr>
                                        <th scope="col" class="px-5 py-3.5 font-bold">Role &amp; Employer</th>
                                        <th scope="col" class="px-5 py-3.5 font-bold">Applied</th>
                                        <th scope="col" class="px-5 py-3.5 font-bold text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($jobApplications as $application)
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <td class="px-5 py-3.5">
                                            <div class="font-bold text-slate-900">{{ $application->jobPost?->title ?? 'Archived Position' }}</div>
                                            <div class="text-[11px] text-slate-500">{{ $application->jobPost?->company ?? 'Corporate Partner' }}</div>
                                        </td>
                                        <td class="px-5 py-3.5 text-xs whitespace-nowrap">{{ $application->created_at->format('M d, Y') }}</td>
                                        <td class="px-5 py-3.5 text-right">
                                            @if($application->status === 'pending')
                                                <span class="bg-amber-50 border border-amber-200 text-amber-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full inline-flex items-center gap-1">
                                                    Pending
                                                </span>
                                            @elseif($application->status === 'reviewed')
                                                <span class="bg-sky-50 border border-sky-200 text-sky-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full inline-flex items-center gap-1">
                                                    Reviewed
                                                </span>
                                            @elseif($application->status === 'accepted')
                                                <span class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full inline-flex items-center gap-1">
                                                    Accepted
                                                </span>
                                            @else
                                                <span class="bg-rose-50 border border-rose-200 text-rose-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full inline-flex items-center gap-1">
                                                    Rejected
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
