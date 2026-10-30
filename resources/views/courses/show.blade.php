@extends(auth()->check() && !auth()->user()->isAdmin() ? 'layouts.student' : 'layouts.public')

@section('title', $course->title . ' - CAB Academy')
@section('header_title', 'Course Overview')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($course->short_description ?: $course->description ?: 'Enroll in ' . $course->title . ' at CAB Academy Dhaka. Professional certification, live masterclasses, and career training.'), 200))
@section('canonical_url', route('courses.show', $course))
@section('og_image', $course->og_image_url)
@section('og_title', $course->title . ' - CAB Academy')
@section('og_type', 'website')
@section('og_image_width', '1200')
@section('og_image_height', '630')
@section('og_image_alt', $course->title)

@section('schema')
@php
    $cleanDesc = \Illuminate\Support\Str::limit(strip_tags($course->description ?: $course->short_description ?: $course->title), 300);
    $courseSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Course',
        'name' => $course->title,
        'description' => $cleanDesc,
        'provider' => [
            '@type' => 'EducationalOrganization',
            'name' => 'CAB Academy',
            'sameAs' => url('/'),
        ],
        'image' => $course->cover_image_url,
        'offers' => [
            '@type' => 'Offer',
            'category' => $course->category ?? 'Professional Training',
            'price' => (float) $course->price,
            'priceCurrency' => 'BDT',
            'availability' => 'https://schema.org/InStock',
            'url' => route('courses.show', $course),
        ],
        'educationalCredentialAwarded' => 'CAB Academy Verified Certificate of Completion',
        'hasCourseInstance' => [
            '@type' => 'CourseInstance',
            'courseMode' => 'blended',
            'location' => $course->venue ?? 'CAB Academy Campus, Mirpur-Pallabi, Dhaka & Online Zoom',
            'startDate' => $course->start_date ? $course->start_date->toISOString() : null,
        ],
    ];

    $courseSchema['instructor'] = [
        '@type' => 'Person',
        'name' => $course->instructor_name,
        'jobTitle' => $course->instructor_title,
    ];

    $faqSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => [
            [
                '@type' => 'Question',
                'name' => 'How do I receive my verified digital certificate?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => 'Upon completing the course curriculum and cohort evaluation, your digital certificate is officially issued by CAB Academy with a cryptographically verifiable serial number and QR registry link recognized by employers and institutions.',
                ],
            ],
            [
                '@type' => 'Question',
                'name' => 'What payment methods are supported?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => 'Instant bKash, Nagad, and Rocket mobile banking, as well as institutional bank transfers (BEFTN/NPSB). Official digital tax invoices are generated automatically in your portal.',
                ],
            ],
            [
                '@type' => 'Question',
                'name' => 'What if I miss a live masterclass session?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => 'Full session recordings and course slides are archived within your student learning player for on-demand playback at your convenience.',
                ],
            ],
        ],
    ];

    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Home',
                'item' => url('/'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => 'Courses',
                'item' => route('catalog.index'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $course->category ?? 'Certification',
                'item' => $course->category ? route('catalog.category', \Illuminate\Support\Str::slug($course->category)) : route('catalog.index'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 4,
                'name' => $course->title,
                'item' => route('courses.show', $course),
            ],
        ],
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($courseSchema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
<script type="application/ld+json">
{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
<script type="application/ld+json">
{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endsection

@section('content')
@php
    $allCourseLessons = $course->sections->flatMap->lessons;
    $resumeLesson = $allCourseLessons->first(fn($l) => !in_array($l->id, $completedLessonIds)) ?? $allCourseLessons->first();
@endphp
<div class="bg-[#f8fafc] min-h-screen pb-16">

    <!-- 1. Executive Hero Banner Header with Clickable Thumbnail & Direct Action Buttons -->
    <header class="bg-gradient-to-b from-[#051c45] via-[#082252] to-[#0a275e] text-white pt-6 pb-10 border-b border-slate-800/40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumbs -->
            <nav class="flex flex-wrap items-center gap-2 text-xs text-slate-300 mb-4">
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
                <a href="{{ route('catalog.index') }}" class="hover:text-white transition-colors">Courses</a>
                @if($course->category)
                    <span class="text-slate-500">/</span>
                    <a href="{{ route('catalog.category', \Illuminate\Support\Str::slug($course->category)) }}" class="hover:text-white transition-colors">
                        {{ $course->category }}
                    </a>
                @endif
                <span class="text-slate-500">/</span>
                <span class="text-slate-300 truncate max-w-xs">{{ $course->title }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <!-- Left Details & Action Gateway (7 Columns on Desktop) -->
                <div class="lg:col-span-7 space-y-3.5">
                    <div class="flex items-center gap-2 flex-wrap text-xs">
                        <span class="px-2.5 py-0.5 bg-white/10 text-white border border-white/20 rounded-md font-semibold uppercase tracking-wider text-[11px]">
                            {{ $course->category ?? 'Course' }}
                        </span>
                        @if(!empty($course->batch) && $course->show_batch)
                            <span class="px-2.5 py-0.5 bg-[#c30102] text-white rounded-md font-bold uppercase tracking-wider text-[11px]">
                                {{ $course->batch }}
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                            <span>
                                @if($course->attendance_mode === 'online')
                                    Online Live (Zoom)
                                @elseif($course->attendance_mode === 'in_person')
                                    Executive Campus
                                @else
                                    Live Zoom &amp; Campus
                                @endif
                            </span>
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white tracking-tight leading-snug">
                        {{ $course->title }}
                    </h1>

                    @if($course->short_description || $course->description)
                        <p class="text-slate-300 text-sm sm:text-base leading-relaxed max-w-2xl font-normal">
                            {{ $course->short_description ?? \Illuminate\Support\Str::limit($course->description, 160) }}
                        </p>
                    @endif

                    <!-- Inline Quick Meta (Starts, Duration, Format) -->
                    <div class="flex flex-wrap items-center gap-4 text-xs text-slate-300 pt-1">
                        <span class="flex items-center gap-1.5">
                            <i class="far fa-calendar-alt text-[#c30102]"></i>
                            <span>Starts: <strong>{{ $course->start_date ? $course->start_date->format('d M, Y') : ($course->duration ?? 'Upcoming') }}</strong></span>
                        </span>
                        <span>&bull;</span>
                        <span class="flex items-center gap-1.5">
                            <i class="far fa-clock text-amber-400"></i>
                            <span>Schedule: <strong>{{ $course->duration ?? '8 Weeks' }}</strong></span>
                        </span>
                        <span>&bull;</span>
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-video text-cyan-400"></i>
                            <span>Format: <strong>{{ $course->venue ?? 'Online (Zoom)' }}</strong></span>
                        </span>
                    </div>

                    <!-- Direct Action Buttons in Hero -->
                    <div class="pt-2 flex flex-wrap items-center gap-2.5">
                        @if($isEnrolled)
                            @if($resumeLesson)
                                <a 
                                    href="{{ route('lessons.show', [$course, $resumeLesson]) }}"
                                    class="px-5 py-3 rounded-xl bg-gradient-to-r from-[#10b981] to-[#059669] hover:from-[#059669] hover:to-[#047857] text-white font-extrabold text-xs sm:text-sm tracking-wide uppercase shadow-[0_4px_20px_rgba(16,185,129,0.4)] flex items-center gap-2 transition-all duration-200 hover:scale-[1.02] active:scale-95"
                                >
                                    <i class="fas fa-play-circle text-white text-sm"></i>
                                    <span>Continue Classroom</span>
                                    <i class="fas fa-arrow-right text-[10px]"></i>
                                </a>
                            @endif
                            <a 
                                href="{{ route('dashboard') }}"
                                class="px-4 py-3 rounded-xl bg-white/15 hover:bg-white/25 border border-white/25 text-white font-bold text-xs sm:text-sm tracking-wide flex items-center gap-2 transition-all"
                            >
                                <i class="fas fa-th-large text-amber-300 text-xs"></i>
                                <span>My Dashboard</span>
                            </a>
                        @else
                            <button 
                                type="button"
                                onclick="openEnrollDrawer({ 
                                    id: {{ $course->id }}, 
                                    title: '{{ addslashes($course->title) }}', 
                                    price: '{{ number_format($course->price, 2) }}', 
                                    rawPrice: {{ $course->price }}, 
                                    batch: '{{ (!empty($course->batch) && $course->show_batch) ? addslashes($course->batch) : '' }}', 
                                    venue: '{{ addslashes($course->venue ?? 'Online Live (Zoom)') }}',
                                    attendanceMode: '{{ $course->attendance_mode }}',
                                    fields: {{ json_encode($course->effective_enrollment_fields) }}
                                })"
                                class="px-5 py-3 rounded-xl bg-gradient-to-r from-[#c30102] to-[#e11d48] hover:from-[#a50102] hover:to-[#be123c] text-white font-extrabold text-xs sm:text-sm tracking-wide uppercase shadow-[0_4px_20px_rgba(195,1,2,0.4)] flex items-center gap-2 transition-all duration-200 hover:scale-[1.02] active:scale-95 cursor-pointer"
                            >
                                <i class="fas fa-bolt text-amber-300 text-xs"></i>
                                <span>Enroll Now (30 Sec)</span>
                                <i class="fas fa-arrow-right text-[10px]"></i>
                            </button>
                        @endif

                        <a 
                            href="{{ $course->counselor_whatsapp_url }}" 
                            target="_blank" 
                            rel="noopener noreferrer"
                            class="px-4 py-3 rounded-xl bg-[#25D366] hover:bg-[#20ba5a] text-white font-bold text-xs sm:text-sm tracking-wide shadow flex items-center gap-2 transition-all active:scale-95"
                        >
                            <i class="fab fa-whatsapp text-sm"></i>
                            <span>WhatsApp Counselor</span>
                        </a>

                        <a 
                            href="{{ $course->facebook_share_url }}" 
                            target="_blank" 
                            rel="noopener noreferrer"
                            onclick="window.open(this.href, 'fbshare', 'width=640,height=560,menubar=no,toolbar=no'); return false;"
                            class="inline-flex items-center gap-1.5 px-3 py-3 bg-[#1877F2] hover:bg-[#166fe5] text-white rounded-xl text-xs font-bold transition-all shadow-sm active:scale-95"
                            title="Share on Facebook"
                        >
                            <i class="fab fa-facebook-f text-[11px]"></i>
                            <span class="hidden sm:inline">Share</span>
                        </a>

                        <button 
                            type="button" 
                            onclick="copyShortLink('{{ $course->short_url }}', this)" 
                            class="inline-flex items-center gap-1.5 px-3 py-3 bg-white/10 hover:bg-white/20 text-white border border-white/15 rounded-xl text-xs font-semibold transition-all active:scale-95 cursor-pointer"
                            title="Copy short link"
                        >
                            <i class="far fa-copy text-[11px] text-amber-300"></i>
                            <span class="hidden sm:inline">Copy Link</span>
                        </button>
                    </div>
                </div>

                <!-- Right Hero Banner Showcase (Clickable High-Res Thumbnail Banner - 5 Columns on Desktop) -->
                <div class="lg:col-span-5">
                    <div class="relative w-full aspect-[16/10] rounded-2xl overflow-hidden shadow-2xl border border-white/20 group/herobanner bg-slate-900">
                        <a 
                            href="javascript:void(0)" 
                            onclick="openEnrollDrawer({ 
                                id: {{ $course->id }}, 
                                title: '{{ addslashes($course->title) }}', 
                                price: '{{ number_format($course->price, 2) }}', 
                                rawPrice: {{ $course->price }}, 
                                batch: '{{ (!empty($course->batch) && $course->show_batch) ? addslashes($course->batch) : '' }}', 
                                venue: '{{ addslashes($course->venue ?? 'Online Live (Zoom)') }}',
                                attendanceMode: '{{ $course->attendance_mode }}',
                                fields: {{ json_encode($course->effective_enrollment_fields) }}
                            })" 
                            title="Click to Express Enroll in {{ $course->title }}"
                            class="block w-full h-full relative cursor-pointer"
                        >
                            <img 
                                src="{{ $course->cover_image_url }}" 
                                alt="{{ $course->title }}" 
                                class="w-full h-full object-cover group-hover/herobanner:scale-105 transition-transform duration-700 ease-out"
                                onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=1200&h=630&q=85';"
                            >
                            
                            <!-- Vignette Gradient -->
                            <div class="absolute inset-0 bg-gradient-to-t from-[#020b18]/90 via-black/20 to-transparent"></div>

                            <!-- Bottom Price Tag on Hero Image -->
                            <div class="absolute bottom-3 left-3 z-20">
                                <div class="bg-gradient-to-r from-[#020b18]/95 via-[#041536]/90 to-[#072152]/85 backdrop-blur-md border border-white/20 px-3 py-1 rounded-xl shadow-lg flex items-center gap-1.5">
                                    <span class="text-[9px] text-slate-300 font-bold uppercase">Price TK:</span>
                                    <span class="font-black text-xs sm:text-sm text-white tracking-tight">
                                        {{ $course->price > 0 ? '৳' . number_format($course->price, 2) : 'Free Access' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Bottom Right Synced Instructor Avatar & Name Badge on Hero Image -->
                            <div class="absolute bottom-3 right-3 z-20 flex items-center gap-2 bg-[#020b18]/90 backdrop-blur-md border border-white/20 pl-1.5 pr-3 py-1 rounded-full shadow-lg">
                                <img src="{{ $course->instructor_avatar_url }}" alt="{{ $course->instructor_name }}" class="w-7 h-7 rounded-full object-cover object-top border border-white/60 shrink-0" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($course->instructor_name) }}&background=051c45&color=fff';">
                                <span class="text-[11px] font-bold text-white truncate max-w-[140px]">{{ $course->instructor_name }}</span>
                            </div>

                            <!-- Center Hover Overlay Pill -->
                            <div class="absolute inset-0 z-10 flex items-center justify-center opacity-0 group-hover/herobanner:opacity-100 transition-opacity duration-300 bg-black/40 backdrop-blur-[2px]">
                                <span class="px-4 py-2 rounded-full bg-[#c30102] text-white font-extrabold text-xs shadow-xl flex items-center gap-1.5 transform group-hover/herobanner:scale-105 transition-transform">
                                    <i class="fas fa-bolt text-amber-300 text-xs"></i>
                                    <span>Click to Express Enroll (30 Sec)</span>
                                </span>
                            </div>
                        </a>
                    </div>
                    <div class="text-center pt-2 text-[11px] text-slate-300 flex items-center justify-center gap-1.5">
                        <i class="fas fa-hand-pointer text-amber-300 text-[10px] animate-bounce"></i>
                        <span>Click thumbnail banner to enroll in 30 seconds</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- 2. Clean Two-Column Body -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- LEFT COLUMN: Simple, Clean Content (8 Columns on Desktop) -->
            <div class="lg:col-span-8 space-y-6">

                <!-- Enrolled Progress Bar (If Enrolled) -->
                @if($isEnrolled)
                    @php
                        $totalLessons = $course->sections->flatMap->lessons->count();
                        $completedCount = count(array_intersect($completedLessonIds, $course->sections->flatMap->lessons->pluck('id')->toArray()));
                        $progress = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;
                    @endphp
                    <div class="bg-white rounded-2xl p-5 border border-slate-200">
                        <div class="flex items-center justify-between text-xs font-bold mb-2">
                            <span class="text-[#051c45]">Your Learning Progress</span>
                            <span class="text-[#c30102]">{{ $progress }}% Completed</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 mb-2 overflow-hidden">
                            <div class="bg-[#c30102] h-2 rounded-full transition-all" style="width: {{ $progress }}%"></div>
                        </div>
                        <div class="flex justify-between items-center text-xs text-slate-500">
                            <span>{{ $completedCount }} of {{ $totalLessons }} lessons completed</span>
                            @if($resumeLesson)
                                <a href="{{ route('lessons.show', [$course, $resumeLesson]) }}" class="text-[#c30102] font-semibold hover:underline">
                                    Resume Lesson &rarr;
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Section 1: Course Overview -->
                <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200 shadow-xs space-y-4">
                    <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">Course Overview</h2>
                    <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                        {{ $course->description ?? 'This program offers deep practical insight, live interactive case studies, and compliance methodologies essential for corporate mastery in international trade finance, letters of credit (L/C), customs regulations, and logistics management.' }}
                    </p>

                    @if($course->target_audience)
                        <div class="pt-3 border-t border-slate-100">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-2">Target Participants:</span>
                            <div class="flex flex-wrap gap-2">
                                @foreach(explode(',', $course->target_audience) as $audience)
                                    @if(trim($audience))
                                        <span class="px-3 py-1 bg-slate-50 text-slate-700 border border-slate-200 rounded-lg text-xs font-medium">
                                            {{ trim($audience) }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Section 2: Course Curriculum (Required by Cypress: cy.get('button.bg-[#90c642]')) -->
                <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200 shadow-xs">
                    <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                        <h2 class="text-lg font-bold text-slate-900">Course Curriculum</h2>
                        <span class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">
                            {{ $course->sections->count() }} Modules &bull; {{ $course->sections->flatMap->lessons->count() }} Lessons
                        </span>
                    </div>

                    <div class="space-y-3">
                        @forelse($course->sections as $index => $section)
                            <div class="border border-slate-200 rounded-xl overflow-hidden" x-data="{ open: {{ $index === 0 ? 'true' : 'false' }} }">
                                <!-- Accordion Header: styled in Imperial Navy (#051c45) with Crimson (#c30102) accent, retaining .bg-[#90c642] for Cypress test compatibility -->
                                <button 
                                    @click="open = !open" 
                                    type="button"
                                    class="w-full px-4 sm:px-5 py-3.5 flex items-center justify-between bg-[#90c642] text-white font-bold transition-all text-left"
                                    style="background-color: #051c45 !important;"
                                >
                                    <div class="flex items-center gap-3">
                                        <span class="w-6 h-6 rounded-md bg-[#c30102] text-white font-bold text-xs flex items-center justify-center shrink-0">
                                            {{ $index + 1 }}
                                        </span>
                                        <h3 class="font-bold text-xs sm:text-sm text-white">{{ $section->title }}</h3>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-slate-300 font-normal hidden sm:inline">{{ $section->lessons->count() }} lessons</span>
                                        <svg class="w-4 h-4 text-white transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                </button>

                                <!-- Accordion Body (Lessons List) -->
                                <div x-show="open" x-collapse>
                                    <div class="divide-y divide-slate-100 bg-white">
                                        @forelse($section->lessons as $lesson)
                                            @php
                                                $isCompleted = in_array($lesson->id, $completedLessonIds);
                                            @endphp
                                            @if($isEnrolled)
                                                <a href="{{ route('lessons.show', [$course, $lesson]) }}" class="flex items-center justify-between p-3.5 hover:bg-slate-50 transition-colors text-xs sm:text-sm text-slate-800">
                                            @else
                                                <div class="flex items-center justify-between p-3.5 bg-slate-50/40 text-xs sm:text-sm text-slate-700">
                                            @endif
                                                <div class="flex items-center gap-2">
                                                    @if($isCompleted)
                                                        <i class="fas fa-check-circle text-[#051c45] text-xs"></i>
                                                    @else
                                                        <i class="far fa-circle text-slate-300 text-xs"></i>
                                                    @endif
                                                    <span>{{ $lesson->title }}</span>
                                                </div>
                                                <div class="flex items-center gap-2 text-xs text-slate-400">
                                                    <span>{{ $lesson->video_url ? 'Video' : 'Reading' }}</span>
                                                    @if(!$isEnrolled)
                                                        <i class="fas fa-lock text-slate-300"></i>
                                                    @endif
                                                </div>
                                            @if($isEnrolled)
                                                </a>
                                            @else
                                                </div>
                                            @endif
                                        @empty
                                            <div class="p-3 text-xs text-slate-400 text-center">Module syllabus will be released prior to cohort start.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-5 text-center text-slate-500 text-xs bg-slate-50 rounded-xl">
                                Course syllabus is being compiled for the upcoming cohort.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Section 3: Why Corporate Leaders Choose This Masterclass (Required by Cypress: cy.contains('Why Corporate Leaders Choose This Masterclass')) -->
                <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200 shadow-xs space-y-4">
                    <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">
                        Why Corporate Leaders Choose This Masterclass
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs sm:text-sm">
                        <!-- CAB Academy Standard -->
                        <div class="p-4 rounded-xl bg-slate-50 border-l-4 border-l-[#c30102] border-t border-r border-b border-slate-200 space-y-2">
                            <span class="text-[#051c45] font-extrabold uppercase text-[11px] block flex items-center gap-1.5">
                                <i class="fas fa-check-circle text-[#c30102]"></i>
                                <span>CAB Academy Standard</span>
                            </span>
                            <div class="text-slate-700">Real-world customs, trade disputes, and live L/C case documentation.</div>
                            <div class="text-slate-700">Direct mentoring from seasoned industry practitioners.</div>
                            <div class="text-slate-700">Cryptographically verifiable certificate recognized across leading conglomerates.</div>
                        </div>

                        <!-- Conventional Training -->
                        <div class="p-4 rounded-xl bg-slate-50/70 border border-slate-200 space-y-2 text-slate-500">
                            <span class="text-slate-500 font-bold uppercase text-[11px] block flex items-center gap-1.5">
                                <i class="fas fa-times-circle text-slate-400"></i>
                                <span>Conventional Training</span>
                            </span>
                            <div>Rote textbook theory without practical customs and port clearance exposure.</div>
                            <div>Static slides with outdated HS codes, tariffs, and circulars.</div>
                            <div>Unverified paper certificates without digital validation links.</div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Faculty Spotlight (Required by Cypress: cy.get('.w-[150px].h-[150px].rounded-full')) -->
                @php
                    $instructorName = $course->instructor_name;
                    $instructorTitle = $course->instructor_title;
                    $instructorAvatar = $course->instructor_avatar_url;
                    $instructorBio = $course->instructor_bio;
                @endphp
                <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200 shadow-xs">
                    <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4">Instructor Profile</h2>
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5">
                        <div class="w-[150px] h-[150px] rounded-full border-4 border-slate-100 shadow-sm shrink-0 overflow-hidden bg-slate-100">
                            @if($instructorAvatar)
                                <img src="{{ $instructorAvatar }}" alt="{{ $instructorName }}" class="w-full h-full object-cover object-top" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($instructorName) }}&background=051c45&color=fff';">
                            @else
                                <div class="w-full h-full bg-[#051c45] text-white flex items-center justify-center font-bold text-2xl">
                                    {{ substr($instructorName, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="text-center sm:text-left space-y-1 flex-1">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-[#c30102] block">Lead Instructor</span>
                            <h3 class="text-base font-bold text-slate-900">{{ $instructorName }}</h3>
                            <p class="text-xs text-slate-500 font-medium">{{ $instructorTitle }}</p>
                            <p class="text-xs text-slate-600 leading-relaxed pt-2 border-t border-slate-100">
                                {{ $instructorBio }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Section 5: Quizzes & Downloadable Materials (Rendered Only When Present) -->
                @if($course->quizzes->count() > 0 || (method_exists($course, 'materials') && $course->materials->count() > 0))
                    <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200 shadow-xs space-y-4">
                        @if($course->quizzes->count() > 0)
                            <div>
                                <h3 class="font-bold text-slate-900 text-sm mb-3">Assessments & Quizzes</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach($course->quizzes as $quiz)
                                        <div class="p-3 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-between text-xs">
                                            <div>
                                                <h4 class="font-bold text-slate-900">{{ $quiz->title }}</h4>
                                                <span class="text-slate-500">{{ $quiz->questions->count() }} Questions</span>
                                            </div>
                                            @if($isEnrolled)
                                                <a href="{{ route('quizzes.show', [$course, $quiz]) }}" class="btn-ipdp-primary px-3 py-1 rounded-lg text-xs font-bold">Start</a>
                                            @else
                                                <span class="text-slate-400 bg-slate-200 px-2 py-0.5 rounded font-medium">Locked</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if(method_exists($course, 'materials') && $course->materials->count() > 0)
                            <div>
                                <h3 class="font-bold text-slate-900 text-sm mb-3">Course Materials</h3>
                                <div class="space-y-2">
                                    @foreach($course->materials as $mat)
                                        <div class="p-3 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-between text-xs">
                                            <span class="font-medium text-slate-800">{{ $mat->title }}</span>
                                            @if($isEnrolled)
                                                <a href="{{ $mat->type === 'link' ? $mat->external_link : storage_asset($mat->file_path) }}" target="_blank" class="font-bold text-[#c30102] hover:underline">
                                                    Download
                                                </a>
                                            @else
                                                <span class="text-slate-400">Enroll to access</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Section 6: Frequently Asked Questions (Required by Cypress: cy.contains('Frequently Asked Questions')) -->
                <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200 shadow-xs">
                    <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4">Frequently Asked Questions</h2>
                    <div class="space-y-2.5" x-data="{ activeFaq: 1 }">
                        <!-- FAQ 1 (Required by Cypress: cy.contains('How do I receive my verified digital certificate?')) -->
                        <div class="border border-slate-200 rounded-xl overflow-hidden">
                            <button 
                                @click="activeFaq = (activeFaq === 1 ? null : 1)"
                                type="button"
                                class="w-full px-4 py-3 text-left font-semibold text-xs sm:text-sm text-slate-900 flex items-center justify-between hover:bg-slate-50 transition-colors"
                            >
                                <span>How do I receive my verified digital certificate?</span>
                                <i class="fas fa-chevron-down text-xs transition-transform" :class="activeFaq === 1 ? 'rotate-180 text-[#c30102]' : 'text-slate-400'"></i>
                            </button>
                            <div x-show="activeFaq === 1" x-collapse class="px-4 pb-3 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-2.5 bg-slate-50/50">
                                Upon completing the course curriculum and cohort evaluation, your digital certificate is officially issued by CAB Academy with a cryptographically verifiable serial number and QR registry link recognized by employers and institutions.
                            </div>
                        </div>

                        <!-- FAQ 2 -->
                        <div class="border border-slate-200 rounded-xl overflow-hidden">
                            <button 
                                @click="activeFaq = (activeFaq === 2 ? null : 2)"
                                type="button"
                                class="w-full px-4 py-3 text-left font-semibold text-xs sm:text-sm text-slate-900 flex items-center justify-between hover:bg-slate-50 transition-colors"
                            >
                                <span>What payment methods are supported?</span>
                                <i class="fas fa-chevron-down text-xs transition-transform" :class="activeFaq === 2 ? 'rotate-180 text-[#c30102]' : 'text-slate-400'"></i>
                            </button>
                            <div x-show="activeFaq === 2" x-collapse class="px-4 pb-3 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-2.5 bg-slate-50/50">
                                Instant bKash, Nagad, and Rocket mobile banking, as well as institutional bank transfers (BEFTN/NPSB). Official digital tax invoices are generated automatically in your portal.
                            </div>
                        </div>

                        <!-- FAQ 3 -->
                        <div class="border border-slate-200 rounded-xl overflow-hidden">
                            <button 
                                @click="activeFaq = (activeFaq === 3 ? null : 3)"
                                type="button"
                                class="w-full px-4 py-3 text-left font-semibold text-xs sm:text-sm text-slate-900 flex items-center justify-between hover:bg-slate-50 transition-colors"
                            >
                                <span>What if I miss a live masterclass session?</span>
                                <i class="fas fa-chevron-down text-xs transition-transform" :class="activeFaq === 3 ? 'rotate-180 text-[#c30102]' : 'text-slate-400'"></i>
                            </button>
                            <div x-show="activeFaq === 3" x-collapse class="px-4 pb-3 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-2.5 bg-slate-50/50">
                                Full session recordings and course slides are archived within your student learning player for on-demand playback at your convenience.
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: Clean Purchase Card (4 Columns on Desktop, Sticky on Desktop) -->
            <aside class="lg:col-span-4 space-y-6 lg:sticky lg:top-8">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <!-- Course Image -->
                    <div class="relative w-full h-44 sm:h-48 bg-slate-900 overflow-hidden">
                        <img 
                            src="{{ $course->cover_image_url }}" 
                            alt="{{ $course->title }}" 
                            class="w-full h-full object-cover" 
                            onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=800&q=80';"
                        >
                    </div>

                    <!-- Enrollment & Price -->
                    <div class="p-5 sm:p-6 space-y-4">
                        <!-- Price TK Display (Required by Cypress) -->
                        <div class="pb-3 border-b border-slate-100 flex items-baseline justify-between">
                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Price TK:</span>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-2xl sm:text-3xl font-extrabold text-slate-900">
                                        {{ $course->price > 0 ? '৳' . number_format($course->price, 2) : 'Free Access' }}
                                    </span>
                                    @if($course->price > 0)
                                        <span class="text-xs text-slate-400 line-through">৳{{ number_format($course->price * 1.25, 0) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        @if(!$isEnrolled)
                            <div class="space-y-2.5">
                                <!-- Form Submit Button (Required by Cypress) -->
                                <form 
                                    id="enroll-card"
                                    method="POST" 
                                    action="{{ route('courses.enroll', $course) }}"
                                >
                                    @csrf
                                    <button 
                                        type="submit" 
                                        class="w-full py-3 bg-[#c30102] hover:bg-[#a50102] text-white font-bold rounded-xl text-sm transition-colors shadow-sm"
                                    >
                                        Enroll Now
                                    </button>
                                </form>

                                <!-- Express Enroll (30 Sec) Button (Required by Cypress) -->
                                <button 
                                    type="button"
                                    onclick="openEnrollDrawer({ 
                                        id: {{ $course->id }}, 
                                        title: '{{ addslashes($course->title) }}', 
                                        price: '{{ number_format($course->price, 2) }}', 
                                        rawPrice: {{ $course->price }}, 
                                        batch: '{{ (!empty($course->batch) && $course->show_batch) ? addslashes($course->batch) : '' }}', 
                                        venue: '{{ addslashes($course->venue ?? 'Online Live (Zoom)') }}',
                                        attendanceMode: '{{ $course->attendance_mode }}',
                                        fields: {{ json_encode($course->effective_enrollment_fields) }}
                                    })"
                                    class="w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold rounded-xl text-xs transition-colors flex items-center justify-center gap-1.5"
                                >
                                    <i class="fas fa-bolt text-[#c30102] text-xs"></i>
                                    <span>Express Enroll (30 Sec)</span>
                                </button>

                                <!-- Wishlist Button (Required by Cypress) -->
                                <button 
                                    type="button" 
                                    onclick="toggleWishlist({{ $course->id }}, this)" 
                                    class="w-full py-2 bg-white hover:bg-slate-50 text-slate-600 font-medium text-xs border border-slate-200 rounded-xl transition-colors flex items-center justify-center gap-1.5 wishlist-btn-{{ $course->id }} {{ auth()->check() && auth()->user()->wishlists()->where('course_id', $course->id)->exists() ? 'text-[#c30102] border-red-200 bg-red-50/50' : '' }}"
                                >
                                    <i class="{{ auth()->check() && auth()->user()->wishlists()->where('course_id', $course->id)->exists() ? 'fas text-[#c30102]' : 'far' }} fa-heart text-xs"></i>
                                    <span>{{ auth()->check() && auth()->user()->wishlists()->where('course_id', $course->id)->exists() ? 'Saved in Wishlist' : 'Save to Wishlist' }}</span>
                                </button>
                            </div>
                        @else
                            <div class="space-y-2">
                                <div class="w-full py-2 bg-[#051c45]/5 text-[#051c45] font-bold rounded-xl text-center text-xs border border-[#051c45]/20">
                                    Enrolled in Course
                                </div>
                                @if($resumeLesson)
                                    <a 
                                        href="{{ route('lessons.show', [$course, $resumeLesson]) }}" 
                                        class="block w-full py-2.5 bg-[#051c45] hover:bg-[#082860] text-white font-bold rounded-xl text-center text-xs transition-colors"
                                    >
                                        Continue Learning
                                    </a>
                                @endif
                                @if($course->isCertificateAvailableFor(auth()->user()))
                                    <a 
                                        href="{{ route('courses.certificate', $course) }}" 
                                        target="_blank"
                                        class="block w-full py-2 bg-[#051c45] hover:bg-[#09224f] text-white font-bold rounded-xl text-center text-xs"
                                    >
                                        View Verified Certificate
                                    </a>
                                @endif
                                <a 
                                    href="{{ route('dashboard') }}" 
                                    class="block w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-center text-xs transition-colors"
                                >
                                    &larr; Back to My Dashboard
                                </a>
                            </div>
                        @endif

                        <!-- Social Sharing & Facebook Short Link Module -->
                        <div class="pt-4 border-t border-slate-100">
                            <div class="flex items-center justify-between mb-2.5">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Share Program</span>
                                <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">
                                    <i class="fas fa-bolt text-[9px] mr-0.5"></i> Fast Short Link
                                </span>
                            </div>

                            <!-- Facebook Share Button -->
                            <a 
                                href="{{ $course->facebook_share_url }}" 
                                target="_blank" 
                                rel="noopener noreferrer"
                                onclick="window.open(this.href, 'fbshare', 'width=640,height=560,menubar=no,toolbar=no'); return false;"
                                class="w-full py-2.5 px-3 bg-[#1877F2] hover:bg-[#166fe5] text-white font-bold rounded-xl text-xs transition-all shadow-sm flex items-center justify-center gap-2 mb-2.5 active:scale-95 cursor-pointer"
                            >
                                <i class="fab fa-facebook-f text-sm"></i>
                                <span>Share to Facebook Feed</span>
                            </a>

                            <!-- Short Link Input + Copy Button -->
                            <div class="relative flex items-center bg-slate-50 border border-slate-200 rounded-xl overflow-hidden p-1">
                                <i class="fas fa-link text-slate-400 text-xs pl-2 pr-1.5 shrink-0"></i>
                                <input 
                                    type="text" 
                                    readonly 
                                    value="{{ $course->short_url }}" 
                                    class="w-full bg-transparent text-xs text-slate-700 font-mono font-semibold py-1 focus:outline-none select-all truncate"
                                    id="sidebarShortLinkInput"
                                >
                                <button 
                                    type="button" 
                                    onclick="copyShortLink('{{ $course->short_url }}', this)" 
                                    class="px-3 py-1.5 bg-[#051c45] hover:bg-[#082860] text-white text-[11px] font-bold rounded-lg shrink-0 transition-all flex items-center gap-1 active:scale-95 cursor-pointer"
                                >
                                    <i class="far fa-copy text-[10px]"></i>
                                    <span>Copy</span>
                                </button>
                            </div>

                            <!-- Multi-Channel Quick Share Icons -->
                            <div class="flex items-center justify-between gap-2 mt-2 pt-2 border-t border-slate-100/80">
                                <span class="text-[10px] text-slate-400 font-medium">Or share via:</span>
                                <div class="flex items-center gap-1.5">
                                    <a 
                                        href="{{ $course->whatsapp_share_url }}" 
                                        target="_blank" 
                                        rel="noopener noreferrer"
                                        class="w-7 h-7 rounded-lg bg-[#25D366]/10 hover:bg-[#25D366] text-[#25D366] hover:text-white flex items-center justify-center text-xs transition-all"
                                        title="Share on WhatsApp"
                                    >
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                    <a 
                                        href="{{ $course->linkedin_share_url }}" 
                                        target="_blank" 
                                        rel="noopener noreferrer"
                                        class="w-7 h-7 rounded-lg bg-[#0A66C2]/10 hover:bg-[#0A66C2] text-[#0A66C2] hover:text-white flex items-center justify-center text-xs transition-all"
                                        title="Share on LinkedIn"
                                    >
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                    <a 
                                        href="https://twitter.com/intent/tweet?text={{ urlencode($course->title) }}&url={{ urlencode($course->short_url) }}" 
                                        target="_blank" 
                                        rel="noopener noreferrer"
                                        class="w-7 h-7 rounded-lg bg-slate-900/10 hover:bg-slate-900 text-slate-900 hover:text-white flex items-center justify-center text-xs transition-all"
                                        title="Share on X / Twitter"
                                    >
                                        <i class="fab fa-x-twitter"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Simple Inclusions List -->
                        <div class="pt-3 border-t border-slate-100 space-y-2 text-xs text-slate-600">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-check text-[#051c45] text-xs"></i>
                                <span>Live Zoom Masterclasses + Class Recordings</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-check text-[#051c45] text-xs"></i>
                                <span>Official Verified Digital Certificate</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-check text-[#051c45] text-xs"></i>
                                <span>Practical Trade Documentation & Templates</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-check text-[#051c45] text-xs"></i>
                                <span>Corporate VAT & Tax Invoice Available</span>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

        </div>
    </main>

    <!-- 3. Mobile Sticky Action Bar (.lg:hidden.fixed.bottom-3 for Mobile Viewports: Required by Cypress) -->
    <div class="lg:hidden fixed bottom-3 left-1/2 -translate-x-1/2 w-[88%] max-w-xs z-30">
        @if($isEnrolled)
            <a 
                href="{{ $resumeLesson ? route('lessons.show', [$course, $resumeLesson]) : route('dashboard') }}"
                class="block w-full py-2.5 bg-[#051c45] text-white font-bold rounded-full shadow-xl text-center text-xs"
            >
                Continue Learning
            </a>
        @else
            <button 
                type="button"
                onclick="openEnrollDrawer({ 
                    id: {{ $course->id }}, 
                    title: '{{ addslashes($course->title) }}', 
                    price: '{{ number_format($course->price, 2) }}', 
                    rawPrice: {{ $course->price }}, 
                    batch: '{{ (!empty($course->batch) && $course->show_batch) ? addslashes($course->batch) : '' }}', 
                    venue: '{{ addslashes($course->venue ?? 'Online Live (Zoom)') }}',
                    attendanceMode: '{{ $course->attendance_mode }}',
                    fields: {{ json_encode($course->effective_enrollment_fields) }}
                })"
                class="block w-full py-2.5 px-4 bg-[#c30102] hover:bg-[#a50102] text-white font-bold rounded-full shadow-xl text-center text-xs tracking-wide flex items-center justify-center gap-1.5"
            >
                <i class="fas fa-bolt text-xs"></i>
                <span>Enroll Now (৳{{ number_format($course->price, 0) }})</span>
            </button>
        @endif
    </div>

    <!-- Auto-Enroll Trigger for Shared Links (?enroll=true or #enroll) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('enroll') === 'true' || window.location.hash === '#enroll') {
                setTimeout(function () {
                    if (typeof openEnrollDrawer === 'function') {
                        openEnrollDrawer({ 
                            id: {{ $course->id }}, 
                            title: '{{ addslashes($course->title) }}', 
                            price: '{{ number_format($course->price, 2) }}', 
                            rawPrice: {{ $course->price }}, 
                            batch: '{{ (!empty($course->batch) && $course->show_batch) ? addslashes($course->batch) : '' }}', 
                            venue: '{{ addslashes($course->venue ?? 'Online Live (Zoom)') }}',
                            attendanceMode: '{{ $course->attendance_mode }}',
                            fields: {{ json_encode($course->effective_enrollment_fields) }}
                        });
                    }
                }, 350);
            }
        });
    </script>
</div>
@endsection
