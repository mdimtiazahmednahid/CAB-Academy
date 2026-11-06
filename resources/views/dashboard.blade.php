@extends('layouts.student')

@section('title', 'Dashboard - ' . \App\Models\Setting::getVal('site_name', 'Academy of Commercial & Supply Chain (ACS)'))
@section('header_title', 'Dashboard')

@section('content')
@php
    $user = auth()->user();
    $hour = (int) now()->format('H');
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');

    $completedCoursesCount = 0;
    $totalLessonsCompleted = count($completedLessonIds ?? []);
    $certificatesCount = isset($userCertificates) ? count($userCertificates) : 0;

    $courseCardsData = [];
    foreach ($enrolledCourses as $course) {
        $allCourseLessons = $course->sections->flatMap->lessons;
        $totalLessons = $allCourseLessons->count();
        $doneLessons = count(array_intersect($completedLessonIds, $allCourseLessons->pluck('id')->toArray()));
        $progress = $totalLessons > 0 ? round(($doneLessons / $totalLessons) * 100) : 0;
        if ($progress >= 100 && $totalLessons > 0) {
            $completedCoursesCount++;
        }

        $nextLesson = $allCourseLessons->first(function ($l) use ($completedLessonIds) {
            return !in_array($l->id, $completedLessonIds);
        }) ?? $allCourseLessons->first();

        $courseCardsData[] = [
            'course' => $course,
            'total' => $totalLessons,
            'done' => $doneLessons,
            'progress' => $progress,
            'next_lesson' => $nextLesson,
            'cert' => $userCertificates[$course->id] ?? null,
        ];
    }

    $primaryClassroom = collect($courseCardsData)->first(fn ($item) => $item['progress'] < 100) ?? ($courseCardsData[0] ?? null);
    $unreadNotices = $user->unreadNotifications()->take(3)->get();
@endphp

<!-- 1. Compact Welcome & Active Classroom Spotlight -->
<div class="bg-gradient-to-br from-[#031029] via-[#051c45] to-[#0a2a66] rounded-2xl p-4 sm:p-5 text-white shadow-sm mb-5 border border-white/10">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-center">
        <!-- Left: Concise Welcome -->
        <div class="{{ $primaryClassroom ? 'lg:col-span-6' : 'lg:col-span-12' }} flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-lg sm:text-xl font-black tracking-tight text-white">
                    {{ $greeting }}, {{ explode(' ', trim($user->name))[0] }}
                </h2>
                <p class="text-xs text-slate-300 mt-0.5">
                    {{ $enrolledCourses->count() }} Enrolled &bull; {{ $completedCoursesCount }} Completed &bull; {{ $certificatesCount }} Certificates
                </p>
            </div>
            @if(!$primaryClassroom)
                <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#c30102] hover:bg-[#a50102] text-white text-xs font-extrabold transition-all shrink-0 self-start sm:self-auto">
                    <span>Explore Courses</span>
                    <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            @endif
        </div>

        <!-- Right: Active Classroom Spotlight -->
        @if($primaryClassroom)
            @php
                $spotCourse = $primaryClassroom['course'];
                $spotNext = $primaryClassroom['next_lesson'];
                $spotUrl = $spotNext ? route('lessons.show', [$spotCourse, $spotNext]) : route('courses.show', $spotCourse);
            @endphp
            <div class="lg:col-span-6 bg-white/10 backdrop-blur-md rounded-xl p-3.5 border border-white/15">
                <div class="flex items-center justify-between gap-2 mb-1.5">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-300 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        Active Classroom Spotlight
                    </span>
                    <span class="text-[10px] font-black text-emerald-300 bg-emerald-500/20 px-2 py-0.5 rounded-md border border-emerald-400/30">
                        {{ $primaryClassroom['progress'] }}% Complete
                    </span>
                </div>
                <h3 class="text-sm font-extrabold text-white line-clamp-1">{{ $spotCourse->title }}</h3>
                <div class="flex items-center justify-between gap-3 mt-2.5">
                    <div class="min-w-0 flex-1">
                        <div class="w-full bg-white/15 rounded-full h-1.5 overflow-hidden mb-1">
                            <div class="bg-gradient-to-r from-emerald-400 to-amber-300 h-1.5 rounded-full" style="width: {{ max(6, $primaryClassroom['progress']) }}%"></div>
                        </div>
                        <p class="text-[11px] text-slate-300 truncate">
                            @if($spotNext)
                                Next: {{ $spotNext->title }}
                            @else
                                {{ $primaryClassroom['done'] }}/{{ $primaryClassroom['total'] }} Lessons
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <a href="{{ $spotUrl }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#c30102] hover:bg-[#a80102] text-white text-xs font-extrabold transition-all">
                            <i class="fas fa-play text-[9px]"></i>
                            <span>Resume</span>
                        </a>
                        <a href="{{ route('courses.show', $spotCourse) }}" class="inline-flex items-center justify-center px-2.5 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-bold border border-white/15 transition-all" title="Course Overview">
                            Syllabus
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="p-3.5 mb-5 text-xs text-emerald-900 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center gap-2.5 font-semibold">
        <i class="fas fa-check-circle text-emerald-600 text-sm shrink-0"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="p-3.5 mb-5 text-xs text-red-900 rounded-xl bg-red-50 border border-red-200 flex items-center gap-2.5 font-semibold">
        <i class="fas fa-exclamation-circle text-red-600 text-sm shrink-0"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

<!-- 2. Compact 4-Metric Bar -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
    <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 flex items-center gap-3 shadow-2xs">
        <div class="w-9 h-9 rounded-lg bg-[#051c45]/10 text-[#051c45] flex items-center justify-center shrink-0">
            <i class="fas fa-book-reader text-sm"></i>
        </div>
        <div class="min-w-0">
            <div class="text-lg font-black text-slate-900 leading-none">{{ $enrolledCourses->count() }}</div>
            <div class="text-[11px] font-bold text-slate-500 mt-1 truncate">Enrolled</div>
        </div>
    </div>

    <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 flex items-center gap-3 shadow-2xs">
        <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
            <i class="fas fa-check-double text-sm"></i>
        </div>
        <div class="min-w-0">
            <div class="text-lg font-black text-slate-900 leading-none">{{ $completedCoursesCount }}</div>
            <div class="text-[11px] font-bold text-slate-500 mt-1 truncate">Completed ({{ $totalLessonsCompleted }} Lessons)</div>
        </div>
    </div>

    <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 flex items-center gap-3 shadow-2xs">
        <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
            <i class="fas fa-award text-sm"></i>
        </div>
        <div class="min-w-0">
            <div class="text-lg font-black text-slate-900 leading-none">{{ $certificatesCount }}</div>
            <div class="text-[11px] font-bold text-slate-500 mt-1 truncate">Certificates</div>
        </div>
    </div>

    <a href="{{ route('performance.index') }}" class="bg-white rounded-xl p-3.5 border border-slate-200/80 hover:border-[#051c45]/30 flex items-center gap-3 shadow-2xs transition-colors">
        <div class="w-9 h-9 rounded-lg bg-[#c30102]/10 text-[#c30102] flex items-center justify-center shrink-0">
            <i class="fas fa-bolt text-sm"></i>
        </div>
        <div class="min-w-0">
            <div class="text-lg font-black text-slate-900 leading-none">Lv. {{ $user->level }}</div>
            <div class="text-[11px] font-bold text-slate-500 mt-1 truncate">{{ $user->xp }} XP &bull; 🔥 {{ $user->current_streak }}d</div>
        </div>
    </a>
</div>

<!-- 3. Conditional Alerts: Pending Seat Verification & Unread Notices -->
@if(isset($pendingPayments) && $pendingPayments->count() > 0)
    <div class="mb-5 bg-amber-50/90 border border-amber-300/80 rounded-2xl p-4">
        <div class="flex items-center justify-between gap-2 mb-2.5">
            <h2 class="text-xs font-black uppercase tracking-wider text-amber-950 flex items-center gap-1.5">
                <i class="fas fa-clock text-amber-600"></i>
                <span>Reserved Seat — Payment Verification in Progress ({{ $pendingPayments->count() }})</span>
            </h2>
            <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-amber-200/70 text-amber-900">Awaiting Approval</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5">
            @foreach($pendingPayments as $pendingPayment)
                <div class="bg-white rounded-xl p-3 border border-amber-200/90 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <div class="font-extrabold text-slate-900 text-xs truncate">
                            {{ $pendingPayment->course?->title ?? 'Course Enrollment' }}
                        </div>
                        <div class="text-[11px] text-slate-500 mt-0.5">
                            ৳{{ number_format((float) $pendingPayment->amount) }} &bull;
                            <span class="uppercase font-semibold">{{ $pendingPayment->gateway }}</span>
                            @if($pendingPayment->transaction_id)
                                &bull; <span class="font-mono font-bold text-[#051c45]">{{ $pendingPayment->transaction_id }}</span>
                            @endif
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-amber-100 text-amber-800 shrink-0">Pending</span>
                </div>
            @endforeach
        </div>
    </div>
@endif

@if($unreadNotices->count() > 0)
    <div class="mb-5 bg-white rounded-2xl p-3.5 border border-slate-200/80 shadow-2xs">
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-xs font-extrabold uppercase tracking-wider text-[#051c45] flex items-center gap-1.5">
                <i class="fas fa-bullhorn text-[#c30102]"></i>
                <span>Unread Notices ({{ $unreadNotices->count() }})</span>
            </h2>
            <a href="{{ route('notifications.index') }}" class="text-[11px] font-bold text-[#c30102] hover:underline">View All &rarr;</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-2.5">
            @foreach($unreadNotices as $notice)
                <a href="{{ $notice->data['url'] ?? route('notifications.index') }}" class="p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200/70 transition-all block">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs font-extrabold text-[#051c45] line-clamp-1">{{ $notice->data['title'] ?? 'Notice' }}</span>
                        <span class="text-[10px] text-slate-400 shrink-0">{{ $notice->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-[11px] text-slate-600 line-clamp-1 mt-0.5">{{ $notice->data['message'] ?? '' }}</p>
                </a>
            @endforeach
        </div>
    </div>
@endif

<!-- 4. My Enrolled Courses (Compact Widescreen Cards) -->
<div class="mb-7">
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-base sm:text-lg font-extrabold text-[#051c45] tracking-tight">My Courses</h2>
        <a href="{{ route('catalog.index') }}" class="text-xs font-extrabold text-[#c30102] hover:underline">Browse More &rarr;</a>
    </div>

    @if(count($courseCardsData) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($courseCardsData as $item)
                @php
                    $course = $item['course'];
                    $progress = $item['progress'];
                    $nextLesson = $item['next_lesson'];
                    $cert = $item['cert'];
                    $resumeUrl = $nextLesson ? route('lessons.show', [$course, $nextLesson]) : route('courses.show', $course);
                @endphp
                <div class="bg-white rounded-2xl overflow-hidden border border-slate-200/80 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <a href="{{ $resumeUrl }}" class="block h-32 w-full bg-slate-100 relative overflow-hidden group">
                            <img src="{{ $course->cover_image_url }}" alt="{{ $course->title }}" loading="lazy" decoding="async" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" onerror="this.onerror=null;this.src='{{ asset('images/course-placeholder.svg') }}';">
                            <div class="absolute inset-0 bg-gradient-to-t from-[#051c45]/75 via-transparent to-transparent"></div>
                            <span class="absolute top-2.5 left-2.5 bg-white/95 text-[#051c45] text-[10px] font-extrabold px-2 py-0.5 rounded-md">
                                {{ $course->category ?? 'Program' }}
                            </span>
                            <span class="absolute bottom-2.5 right-2.5 bg-[#051c45]/90 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-md">
                                {{ $item['done'] }}/{{ $item['total'] }} Lessons &bull; {{ $progress }}%
                            </span>
                        </a>

                        <div class="p-3.5">
                            <a href="{{ $resumeUrl }}" class="block">
                                <h3 class="font-extrabold text-sm text-slate-900 line-clamp-1 hover:text-[#c30102] transition-colors">{{ $course->title }}</h3>
                            </a>
                            <div class="text-[11px] font-semibold text-slate-500 mt-1 truncate">
                                {{ $course->instructor_name }}
                                @if($course->instructor_designation)
                                    &bull; <span class="text-slate-400">{{ $course->instructor_designation }}</span>
                                @endif
                            </div>

                            <!-- Slim Progress Bar -->
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden mt-2.5">
                                <div class="bg-gradient-to-r from-[#051c45] to-[#10b981] h-1.5 rounded-full transition-all duration-500" style="width: {{ max(4, $progress) }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Single Inline Action Row -->
                    <div class="px-3.5 pb-3.5 pt-1 flex items-center gap-1.5">
                        <a href="{{ $resumeUrl }}" class="flex-1 py-2 px-3 rounded-xl font-extrabold text-white bg-[#051c45] hover:bg-[#082b66] transition-all flex items-center justify-center gap-1.5 text-xs">
                            <i class="fas fa-play text-[9px] text-amber-300"></i>
                            <span>{{ $progress > 0 ? 'Continue' : 'Start' }}</span>
                        </a>
                        <a href="{{ route('courses.show', $course) }}" class="py-2 px-2.5 rounded-xl font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-all text-xs" title="Course Syllabus">
                            Syllabus
                        </a>
                        @if($course->isCertificateAvailableFor(auth()->user()))
                            <a href="{{ route('courses.certificate', $course) }}" target="_blank" class="py-2 px-2.5 rounded-xl font-extrabold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-all text-xs inline-flex items-center gap-1" title="Download Digital Certificate">
                                <i class="fas fa-award text-emerald-600"></i>
                                <span class="hidden sm:inline">Digital Certificate</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-2xl p-6 text-center border border-slate-200/80 shadow-2xs flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-left">
                <h3 class="text-sm font-extrabold text-[#051c45]">No Enrolled Courses Yet</h3>
                <p class="text-xs text-slate-500 mt-0.5">Choose a course from our catalog to begin learning.</p>
            </div>
            <a href="{{ route('catalog.index') }}" class="px-4 py-2 rounded-xl font-extrabold text-white bg-[#c30102] hover:bg-[#a50102] transition-all text-xs shrink-0">
                Explore Courses &rarr;
            </a>
        </div>
    @endif
</div>

<!-- 5. Payments & Applications (Side-by-Side Compact Panels) -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-5 mb-7">
    <!-- Tuition Payments & Official Invoices -->
    <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-extrabold text-sm text-[#051c45]">Tuition Payments &amp; Official Invoices</h3>
            <span class="text-[11px] font-bold text-slate-400">{{ isset($payments) ? $payments->count() : 0 }} Records</span>
        </div>

        @if(isset($payments) && $payments->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 text-slate-400 text-[10px] uppercase tracking-wider border-b border-slate-100">
                            <th class="py-2.5 px-4 font-extrabold">Course</th>
                            <th class="py-2.5 px-3 font-extrabold">Amount</th>
                            <th class="py-2.5 px-3 font-extrabold">Status</th>
                            <th class="py-2.5 px-4 font-extrabold text-right">Invoice</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @foreach($payments as $payment)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-2.5 px-4">
                                    <div class="font-bold text-slate-800 line-clamp-1">{{ $payment->course?->title ?? 'Course Enrollment' }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">
                                        {{ $payment->transaction_id ?: 'N/A' }} &bull; {{ $payment->created_at->format('d M Y') }}
                                    </div>
                                </td>
                                <td class="py-2.5 px-3 font-black text-slate-800 whitespace-nowrap">
                                    ৳{{ number_format((float) $payment->amount) }}
                                </td>
                                <td class="py-2.5 px-3 whitespace-nowrap">
                                    @if($payment->status === 'verified')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">Verified</span>
                                    @elseif($payment->status === 'rejected')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-red-50 text-red-700 border border-red-200">Rejected</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200">Pending Approval</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-4 text-right whitespace-nowrap">
                                    @if($payment->status === 'verified')
                                        <a href="{{ route('payments.invoice', $payment) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-[#051c45] text-white text-[10px] font-bold hover:bg-[#c30102] transition-colors">
                                            <i class="fas fa-file-invoice"></i>
                                            <span>Invoice</span>
                                        </a>
                                    @elseif($payment->status === 'rejected')
                                        <span class="text-[10px] font-bold text-red-500">Declined</span>
                                    @else
                                        <span class="text-[10px] font-bold text-amber-700">Awaiting Approval</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-6 text-center text-slate-400 text-xs">
                No payment records yet.
            </div>
        @endif
    </div>

    <!-- Job Applications -->
    <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-extrabold text-sm text-[#051c45]">Job Applications</h3>
            <a href="{{ route('jobs.index') }}" class="text-[11px] font-bold text-[#c30102] hover:underline">Job Board &rarr;</a>
        </div>

        @if(isset($jobApplications) && $jobApplications->count() > 0)
            <div class="divide-y divide-slate-100">
                @foreach($jobApplications as $app)
                    <div class="p-3.5 hover:bg-slate-50/60 transition-colors flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            @if($app->jobPost)
                                <div class="font-bold text-xs text-slate-900 line-clamp-1">
                                    {{ $app->jobPost->title }}
                                </div>
                                <div class="text-[11px] text-slate-500 truncate">
                                    {{ $app->jobPost->company ?? $app->jobPost->company_name ?? 'Corporate Employer' }} &bull; {{ $app->created_at->format('d M Y') }}
                                </div>
                            @else
                                <span class="font-semibold text-xs text-slate-400 italic">Position Closed</span>
                            @endif
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">
                            Submitted
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-6 text-center text-slate-400 text-xs">
                No job applications submitted yet.
            </div>
        @endif
    </div>
</div>

<!-- 6. Recommended Courses (Compact 3-Col Strip) -->
@if(isset($recommendedCourses) && $recommendedCourses->count() > 0)
    <div class="mb-6">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-base font-extrabold text-[#051c45] tracking-tight">Recommended Courses</h2>
            <a href="{{ route('catalog.index') }}" class="text-xs font-extrabold text-[#c30102] hover:underline">View Catalog &rarr;</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($recommendedCourses->take(3) as $recCourse)
                <x-course-card :course="$recCourse" />
            @endforeach
        </div>
    </div>
@endif
@endsection
