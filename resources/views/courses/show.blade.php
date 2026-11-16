@extends(auth()->check() && !auth()->user()->isAdmin() ? 'layouts.student' : 'layouts.public')
@php
    $siteName = \App\Models\Setting::getVal('site_name', 'ACS Academy');
    $counselorPhone = $course->effective_counselor_phone;
    $counselorTel = $course->effective_counselor_tel;
@endphp

@section('title', $course->title . ' - ' . $siteName)
@section('header_title', 'Course Overview')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($course->short_description ?: $course->description ?: 'Enroll in ' . $course->title . ' at ' . $siteName . '. Professional certification, live masterclasses, and career training.'), 200))
@section('canonical_url', route('courses.show', $course))
@section('og_image', $course->og_image_url)
@section('og_title', $course->stylish_og_title)
@section('og_type', 'website')
@section('og_image_width', '1200')
@section('og_image_height', '630')
@section('og_image_alt', $course->social_card_headline)

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
            'name' => $siteName,
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
        'educationalCredentialAwarded' => $siteName . ' Verified Certificate of Completion',
        'hasCourseInstance' => [
            '@type' => 'CourseInstance',
            'courseMode' => 'blended',
            'location' => $course->venue ?? ($siteName . ' Campus & Online Zoom'),
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
                    'text' => 'Upon completing the course curriculum and cohort evaluation, your digital certificate is officially issued by ' . $siteName . ' with a cryptographically verifiable serial number and QR registry link recognized by employers and institutions.',
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
    $isRegistrationOpen = $course->isRegistrationOpen();
    $canShowEnrollCta = $isRegistrationOpen && (!$isEnrolled || (auth()->check() && auth()->user()->isAdmin()));
@endphp
<div class="relative bg-gradient-to-b from-[#eef4fb] via-[#f5f8fc] to-[#eef2f9] min-h-screen pb-20 overflow-hidden">
    <!-- Ambient Liquid Glass Glow Orbs -->
    <div class="pointer-events-none absolute inset-0 overflow-hidden z-0">
        <div class="absolute top-[28rem] -left-24 w-96 h-96 rounded-full bg-blue-500/10 blur-[110px]"></div>
        <div class="absolute top-[48rem] -right-24 w-96 h-96 rounded-full bg-[#c4040a]/8 blur-[120px]"></div>
        <div class="absolute bottom-24 left-1/3 w-96 h-96 rounded-full bg-emerald-500/8 blur-[110px]"></div>
    </div>

    <!-- 1. Executive Hero Banner Header with Clickable Thumbnail & Direct Action Buttons -->
    <header class="relative z-10 bg-gradient-to-br from-[#03122f] via-[#063b96] to-[#0b2b66] text-white pt-6 pb-12 border-b border-white/15 overflow-hidden">
        <div class="pointer-events-none absolute -top-24 right-1/4 w-96 h-96 rounded-full bg-blue-400/15 blur-[110px]"></div>
        <div class="pointer-events-none absolute bottom-0 left-10 w-80 h-80 rounded-full bg-[#c4040a]/15 blur-[100px]"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumbs (Desktop only to keep mobile hero clean above the fold) -->
            <nav class="hidden sm:flex flex-wrap items-center gap-2 text-xs text-slate-300 mb-5">
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

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-10 items-center">
                <!-- Left Details & Action Gateway (7 Columns on Desktop, Below Cover on Mobile) -->
                <div class="order-last lg:order-first lg:col-span-7 space-y-4">
                    <div class="flex items-center gap-2 flex-wrap text-xs">
                        <span class="px-3 py-1 bg-white/10 backdrop-blur-md text-white border border-white/20 rounded-full font-bold uppercase tracking-wider text-[10px] sm:text-[11px]">
                            {{ $course->category ?? 'Certification' }}
                        </span>
                        @if(!empty($course->batch) && $course->show_batch)
                            <span class="px-3 py-1 bg-[#c4040a] text-white rounded-full font-bold uppercase tracking-wider text-[10px] sm:text-[11px] shadow-sm">
                                {{ $course->batch }}
                            </span>
                        @endif
                        @if($isRegistrationOpen)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-500/15 border border-emerald-400/30 text-emerald-200 rounded-full font-semibold text-[10px] sm:text-[11px]">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span>Admissions Open</span>
                            </span>
                        @endif
                    </div>

                    <h1 class="text-2xl sm:text-3xl lg:text-[2.5rem] font-extrabold text-white tracking-tight leading-[1.18]">
                        {{ $course->title }}
                    </h1>

                    @if($course->short_description || $course->description)
                        <p class="text-slate-200/90 text-sm sm:text-base leading-relaxed max-w-2xl font-normal line-clamp-3 sm:line-clamp-none">
                            {{ $course->short_description ?? \Illuminate\Support\Str::limit($course->description, 160) }}
                        </p>
                    @endif

                    <!-- Faculty Authority Byline -->
                    <div class="flex items-center gap-3 pt-1">
                        <div class="w-9 h-9 rounded-full border border-white/30 overflow-hidden bg-white/10 shrink-0">
                            @if($course->instructor_avatar_url)
                                <img src="{{ $course->instructor_avatar_url }}" alt="{{ $course->instructor_name }}" class="w-full h-full object-cover object-top">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-xs font-bold text-white">
                                    {{ substr($course->instructor_name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs sm:text-sm font-bold text-white truncate">
                                Led by {{ $course->instructor_name }}
                            </p>
                            @if($course->instructor_title)
                                <p class="text-[11px] text-slate-300 truncate">{{ $course->instructor_title }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Glass Metadata Chips (Starts · Duration · Format) -->
                    <div class="flex flex-wrap items-center gap-2 pt-1 text-xs text-slate-100">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/10 border border-white/15 backdrop-blur-md">
                            <i class="far fa-calendar-alt text-rose-300"></i>
                            <span class="font-medium">{{ $course->start_date ? $course->start_date->format('d M, Y') : ($course->duration ?? 'Upcoming') }}</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/10 border border-white/15 backdrop-blur-md">
                            <i class="far fa-clock text-amber-300"></i>
                            <span class="font-medium">{{ $course->duration ?? '8 Weeks' }}</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/10 border border-white/15 backdrop-blur-md">
                            <i class="fas fa-video text-cyan-300"></i>
                            <span class="font-medium">{{ $course->venue ?? 'Online (Zoom)' }}</span>
                        </span>
                    </div>

                    <!-- Desktop Hero CTA (Hidden on Mobile where Sticky Bottom Bar handles enrollment cleanly) -->
                    <div class="hidden lg:flex pt-2 flex-wrap items-center gap-3">
                        @if($canShowEnrollCta)
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
                                    whatsapp: '{{ $course->effective_whatsapp_number }}',
                                    fields: {{ json_encode($course->effective_enrollment_fields) }}
                                })"
                                class="btn-ipdp-primary enroll-dance-cta px-6 py-3.5 rounded-xl text-white font-extrabold text-sm tracking-wide uppercase shadow-[0_8px_28px_rgba(196,4,10,0.45)] flex items-center gap-2.5 transition-all duration-200 hover:scale-[1.02] active:scale-95 cursor-pointer border border-white/20"
                            >
                                <i class="fas fa-bolt text-amber-300 text-xs"></i>
                                <span>Enroll Now (30 Sec)</span>
                                <i class="fas fa-arrow-right text-[10px]"></i>
                            </button>
                        @endif

                        @if($isEnrolled)
                            <a 
                                href="{{ $resumeLesson ? route('lessons.show', [$course, $resumeLesson]) : route('courses.classroom', $course) }}"
                                class="px-6 py-3.5 rounded-xl bg-gradient-to-r from-[#10b981] to-[#059669] hover:from-[#059669] hover:to-[#047857] text-white font-extrabold text-sm tracking-wide uppercase shadow-[0_6px_22px_rgba(16,185,129,0.4)] flex items-center gap-2 transition-all duration-200 hover:scale-[1.02] active:scale-95"
                            >
                                <i class="fas fa-play-circle text-white text-sm"></i>
                                <span>Continue Classroom</span>
                                <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                        @endif

                        <a 
                            href="{{ $course->counselor_whatsapp_url }}" 
                            target="_blank" 
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 px-4 py-3.5 rounded-xl bg-white/10 hover:bg-white/20 text-white border border-white/20 font-bold text-xs tracking-wide transition-all backdrop-blur-md"
                        >
                            <i class="fab fa-whatsapp text-[#25D366] text-sm"></i>
                            <span>WhatsApp Counselor</span>
                        </a>
                    </div>
                </div>

                <!-- Right Hero Banner Showcase (First on Mobile, Right 5 Columns on Desktop) -->
                <div class="order-first lg:order-last lg:col-span-5">
                    <div class="liquid-glass-dark rounded-3xl p-2 border border-white/25 shadow-[0_25px_60px_-15px_rgba(0,0,0,0.65)] overflow-hidden">
                        <div class="relative w-full aspect-[16/9] rounded-2xl overflow-hidden group/herobanner bg-slate-950">
                            @if($isEnrolled && $resumeLesson)
                                <a href="{{ route('lessons.show', [$course, $resumeLesson]) }}"
                                   title="Continue Classroom"
                                   class="block w-full h-full relative cursor-pointer">
                            @else
                                <div class="block w-full h-full relative">
                            @endif
                                <img 
                                    src="{{ $course->cover_image_url }}" 
                                    alt="" 
                                    aria-hidden="true"
                                    class="absolute inset-0 w-full h-full object-cover object-center blur-xl opacity-45 scale-110 pointer-events-none"
                                >
                                <img 
                                    src="{{ $course->cover_image_url }}" 
                                    alt="{{ $course->title }}" 
                                    class="relative z-10 w-full h-full object-contain object-center group-hover/herobanner:scale-[1.02] transition-transform duration-700 ease-out"
                                    onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=1200&h=630&q=85';"
                                >

                                @if($isEnrolled)
                                    <div class="absolute inset-0 z-10 flex items-center justify-center opacity-0 group-hover/herobanner:opacity-100 transition-opacity duration-300 bg-black/40 backdrop-blur-[2px]">
                                        <span class="px-4 py-2 rounded-full bg-[#10b981] text-white font-extrabold text-xs shadow-xl flex items-center gap-1.5 transform group-hover/herobanner:scale-105 transition-transform border border-white/20">
                                            <i class="fas fa-play-circle text-white text-xs"></i>
                                            <span>Continue Classroom</span>
                                        </span>
                                    </div>
                                @endif
                            @if($isEnrolled && $resumeLesson)
                                </a>
                            @else
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- 2. Liquid Glass Two-Column Body -->
    <main class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 sm:pt-10 pb-24 lg:pb-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- LEFT COLUMN: Liquid Glass Content Modules (8 Columns on Desktop) -->
            <div class="lg:col-span-8 space-y-6">

                <!-- Enrolled Progress Bar (If Enrolled) -->
                @if($isEnrolled)
                    @php
                        $totalLessons = $course->sections->flatMap->lessons->count();
                        $completedCount = count(array_intersect($completedLessonIds, $course->sections->flatMap->lessons->pluck('id')->toArray()));
                        $progress = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;
                    @endphp
                    <div class="liquid-glass-card rounded-3xl p-5">
                        <div class="flex items-center justify-between text-xs font-bold mb-2">
                            <span class="text-[#063b96]">Your Learning Progress</span>
                            <span class="text-[#c4040a]">{{ $progress }}% Completed</span>
                        </div>
                        <div class="w-full bg-slate-200/70 rounded-full h-2 mb-2 overflow-hidden">
                            <div class="bg-[#c4040a] h-2 rounded-full transition-all" style="width: {{ $progress }}%"></div>
                        </div>
                        <div class="flex justify-between items-center text-xs text-slate-500">
                            <span>{{ $completedCount }} of {{ $totalLessons }} lessons completed</span>
                            @if($resumeLesson)
                                <a href="{{ route('lessons.show', [$course, $resumeLesson]) }}" class="text-[#c4040a] font-semibold hover:underline">
                                    Resume Lesson &rarr;
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Section 1: Course Overview -->
                <div class="liquid-glass-card rounded-3xl p-6 sm:p-8 space-y-5">
                    <div class="flex items-center gap-3 border-b border-slate-200/70 pb-3.5">
                        <span class="w-1 h-5 rounded-full bg-[#c4040a]"></span>
                        <h2 class="text-lg sm:text-xl font-extrabold text-[#063b96] tracking-tight">Course Overview</h2>
                    </div>

                    <p class="text-slate-700 leading-7 text-sm sm:text-[15px]">
                        {{ $course->description ?? 'This program offers deep practical insight, live interactive case studies, and compliance methodologies essential for corporate mastery in international trade finance, letters of credit (L/C), customs regulations, and logistics management.' }}
                    </p>

                    <!-- 3-Pillar Program Highlights -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                        <div class="p-3.5 rounded-2xl bg-slate-50/80 border border-slate-200/70 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-[#063b96]/10 text-[#063b96] flex items-center justify-center shrink-0">
                                <i class="fas fa-chalkboard-teacher text-xs"></i>
                            </div>
                            <div>
                                <h3 class="text-xs font-extrabold text-[#063b96]">Live Practical</h3>
                                <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">Interactive sessions with real-world Q&A.</p>
                            </div>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-slate-50/80 border border-slate-200/70 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-[#c4040a]/10 text-[#c4040a] flex items-center justify-center shrink-0">
                                <i class="fas fa-file-contract text-xs"></i>
                            </div>
                            <div>
                                <h3 class="text-xs font-extrabold text-[#063b96]">Case Templates</h3>
                                <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">Ready-to-use industry frameworks & tools.</p>
                            </div>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-slate-50/80 border border-slate-200/70 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-700 flex items-center justify-center shrink-0">
                                <i class="fas fa-award text-xs"></i>
                            </div>
                            <div>
                                <h3 class="text-xs font-extrabold text-[#063b96]">QR Certificate</h3>
                                <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">Employer-verifiable digital credential.</p>
                            </div>
                        </div>
                    </div>

                    @if($course->target_audience)
                        <div class="pt-4 border-t border-slate-200/60">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-2.5">Designed For</span>
                            <div class="flex flex-wrap gap-2">
                                @foreach(explode(',', $course->target_audience) as $audience)
                                    @if(trim($audience))
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white text-[#063b96] border border-slate-200/90 rounded-xl text-xs font-semibold shadow-2xs">
                                            <i class="fas fa-check text-emerald-600 text-[10px]"></i>
                                            <span>{{ trim($audience) }}</span>
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Section 2: Course Curriculum (Required by Cypress: cy.get('button.bg-[#90c642]')) -->
                <div class="liquid-glass-card rounded-3xl p-6 sm:p-8">
                    <div class="flex items-center justify-between gap-3 mb-5 border-b border-slate-200/70 pb-3.5">
                        <div class="flex items-center gap-3">
                            <span class="w-1 h-5 rounded-full bg-[#063b96]"></span>
                            <h2 class="text-lg sm:text-xl font-extrabold text-[#063b96] tracking-tight">Course Curriculum</h2>
                        </div>
                        <span class="text-xs font-bold text-[#063b96] bg-slate-100 border border-slate-200/80 px-3 py-1 rounded-full">
                            {{ $course->sections->count() }} Modules &bull; {{ $course->sections->flatMap->lessons->count() }} Lessons
                        </span>
                    </div>

                    <div class="space-y-3">
                        @forelse($course->sections as $index => $section)
                            <div class="border border-slate-200/90 rounded-2xl overflow-hidden bg-white shadow-2xs transition-all" x-data="{ open: {{ $index === 0 ? 'true' : 'false' }} }">
                                <!-- Accordion Header: clean white when collapsed, Imperial Navy (#063b96) when expanded; retains .bg-[#90c642] for Cypress test compatibility -->
                                <button 
                                    @click="open = !open" 
                                    type="button"
                                    class="w-full px-4 sm:px-5 py-4 flex items-center justify-between bg-[#90c642] font-bold transition-colors text-left cursor-pointer"
                                    :style="open ? 'background-color: #063b96 !important; color: #ffffff !important;' : 'background-color: #ffffff !important; color: #063b96 !important;'"
                                >
                                    <div class="flex items-center gap-3 min-w-0">
                                        <span class="w-7 h-7 rounded-lg font-extrabold text-xs flex items-center justify-center shrink-0 transition-colors"
                                              :class="open ? 'bg-[#c4040a] text-white' : 'bg-[#063b96]/8 text-[#063b96]'">
                                            {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                        </span>
                                        <h3 class="font-bold text-xs sm:text-sm truncate" :class="open ? 'text-white' : 'text-[#063b96]'">{{ $section->title }}</h3>
                                    </div>
                                    <div class="flex items-center gap-2.5 shrink-0 ml-2">
                                        <span class="text-[11px] font-medium hidden sm:inline" :class="open ? 'text-slate-300' : 'text-slate-500'">{{ $section->lessons->count() }} lessons</span>
                                        <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180 text-white' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                </button>

                                <!-- Accordion Body (Lessons List) -->
                                <div x-show="open" x-collapse>
                                    <div class="divide-y divide-slate-100 bg-slate-50/40 border-t border-slate-100">
                                        @forelse($section->lessons as $lesson)
                                            @php
                                                $isCompleted = in_array($lesson->id, $completedLessonIds);
                                            @endphp
                                            @if($isEnrolled)
                                                <a href="{{ route('lessons.show', [$course, $lesson]) }}" class="flex items-center justify-between px-4 sm:px-5 py-3.5 hover:bg-white transition-colors text-xs sm:text-sm text-slate-800">
                                            @else
                                                <div class="flex items-center justify-between px-4 sm:px-5 py-3.5 text-xs sm:text-sm text-slate-700">
                                            @endif
                                                <div class="flex items-center gap-2.5 min-w-0">
                                                    @if($isCompleted)
                                                        <i class="fas fa-check-circle text-emerald-600 text-xs shrink-0"></i>
                                                    @else
                                                        <i class="far fa-play-circle text-slate-400 text-xs shrink-0"></i>
                                                    @endif
                                                    <span class="truncate">{{ $lesson->title }}</span>
                                                </div>
                                                <div class="flex items-center gap-2 text-xs text-slate-400 shrink-0 ml-2">
                                                    @if($lesson->is_live_class)
                                                        <span class="px-2 py-0.5 rounded-full bg-rose-50 text-[#c4040a] border border-rose-200 text-[10px] font-extrabold uppercase tracking-wider">Live</span>
                                                    @else
                                                        <span class="text-[11px] font-medium text-slate-500">{{ $lesson->video_url ? 'Video' : 'Reading' }}</span>
                                                    @endif
                                                    @if(!$isEnrolled)
                                                        <i class="fas fa-lock text-slate-300 text-[11px]"></i>
                                                    @endif
                                                </div>
                                            @if($isEnrolled)
                                                </a>
                                            @else
                                                </div>
                                            @endif
                                        @empty
                                            <div class="p-4 text-xs text-slate-400 text-center">Module syllabus will be released prior to cohort start.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-slate-500 text-xs bg-white/60 rounded-2xl border border-slate-200/60">
                                Course syllabus is being compiled for the upcoming cohort.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Section 3: Why Corporate Leaders Choose This Masterclass (Required by Cypress: cy.contains('Why Corporate Leaders Choose This Masterclass')) -->
                <div class="liquid-glass-card rounded-3xl p-6 sm:p-8 space-y-5">
                    <div class="flex items-center gap-3 border-b border-slate-200/70 pb-3.5">
                        <span class="w-1 h-5 rounded-full bg-[#c4040a]"></span>
                        <h2 class="text-lg sm:text-xl font-extrabold text-[#063b96] tracking-tight">
                            Why Corporate Leaders Choose This Masterclass
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs sm:text-sm">
                        <!-- Academy Standard -->
                        <div class="p-5 rounded-2xl bg-gradient-to-br from-[#063b96]/[0.03] to-white border-2 border-[#063b96]/15 space-y-3 shadow-2xs">
                            <span class="text-[#063b96] font-extrabold uppercase text-[11px] tracking-wider flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-[#c4040a] text-white flex items-center justify-center text-[10px]">
                                    <i class="fas fa-check"></i>
                                </span>
                                <span>{{ $siteName }} Standard</span>
                            </span>
                            <ul class="space-y-2.5 text-slate-700 text-xs sm:text-[13px]">
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-check-circle text-emerald-600 mt-0.5 shrink-0"></i>
                                    <span>Real-world practical case simulations and live industry documentation.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-check-circle text-emerald-600 mt-0.5 shrink-0"></i>
                                    <span>Direct mentoring from seasoned industry practitioners.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-check-circle text-emerald-600 mt-0.5 shrink-0"></i>
                                    <span>Cryptographically verifiable certificate recognized across leading organizations.</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Conventional Training -->
                        <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200/80 space-y-3 text-slate-500">
                            <span class="text-slate-500 font-bold uppercase text-[11px] tracking-wider flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center text-[10px]">
                                    <i class="fas fa-times"></i>
                                </span>
                                <span>Conventional Training</span>
                            </span>
                            <ul class="space-y-2.5 text-xs sm:text-[13px]">
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-times-circle text-slate-400 mt-0.5 shrink-0"></i>
                                    <span>Rote textbook theory without practical industry execution exposure.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-times-circle text-slate-400 mt-0.5 shrink-0"></i>
                                    <span>Static slides with outdated frameworks and generic examples.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-times-circle text-slate-400 mt-0.5 shrink-0"></i>
                                    <span>Unverified paper certificates without digital validation links.</span>
                                </li>
                            </ul>
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
                <div class="liquid-glass-card rounded-3xl p-6 sm:p-8">
                    <div class="flex items-center gap-3 border-b border-slate-200/70 pb-3.5 mb-5">
                        <span class="w-1 h-5 rounded-full bg-[#063b96]"></span>
                        <h2 class="text-lg sm:text-xl font-extrabold text-[#063b96] tracking-tight">Instructor Profile</h2>
                    </div>
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                        <div class="w-[150px] h-[150px] rounded-full border-4 border-white ring-4 ring-[#063b96]/10 shadow-lg shrink-0 overflow-hidden bg-slate-100">
                            @if($instructorAvatar)
                                <img src="{{ $instructorAvatar }}" alt="{{ $instructorName }}" class="w-full h-full object-cover object-top" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($instructorName) }}&background=051c45&color=fff';">
                            @else
                                <div class="w-full h-full bg-[#063b96] text-white flex items-center justify-center font-bold text-2xl">
                                    {{ substr($instructorName, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="text-center sm:text-left space-y-2 flex-1">
                            <span class="inline-block px-2.5 py-0.5 rounded-full bg-[#c4040a]/10 text-[#c4040a] text-[10px] font-extrabold uppercase tracking-wider">Lead Instructor</span>
                            <h3 class="text-lg sm:text-xl font-extrabold text-[#063b96]">{{ $instructorName }}</h3>
                            <p class="text-xs sm:text-sm text-slate-600 font-semibold">{{ $instructorTitle }}</p>
                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed pt-2.5 border-t border-slate-200/60">
                                {{ $instructorBio }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Section 5: Quizzes & Downloadable Materials (Rendered Only When Present) -->
                @if($course->quizzes->count() > 0 || (method_exists($course, 'materials') && $course->materials->count() > 0))
                    <div class="liquid-glass-card rounded-3xl p-6 sm:p-7 space-y-4">
                        @if($course->quizzes->count() > 0)
                            <div>
                                <h3 class="font-bold text-[#063b96] text-sm mb-3">Assessments & Quizzes</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach($course->quizzes as $quiz)
                                        <div class="p-3 rounded-2xl border border-slate-200/80 bg-white/80 flex items-center justify-between text-xs">
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
                                <h3 class="font-bold text-[#063b96] text-sm mb-3">Course Materials</h3>
                                <div class="space-y-2">
                                    @foreach($course->materials as $mat)
                                        <div class="p-3 rounded-2xl border border-slate-200/80 bg-white/80 flex items-center justify-between text-xs">
                                            <span class="font-medium text-slate-800">{{ $mat->title }}</span>
                                            @if($isEnrolled)
                                                <a href="{{ $mat->type === 'link' ? $mat->external_link : storage_asset($mat->file_path) }}" target="_blank" class="font-bold text-[#c4040a] hover:underline">
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
                <div class="liquid-glass-card rounded-3xl p-6 sm:p-7">
                    <h2 class="text-lg font-extrabold text-[#063b96] border-b border-slate-200/60 pb-3 mb-4">Frequently Asked Questions</h2>
                    <div class="space-y-2.5" x-data="{ activeFaq: 1 }">
                        <!-- FAQ 1 (Required by Cypress: cy.contains('How do I receive my verified digital certificate?')) -->
                        <div class="border border-slate-200/80 bg-white/75 rounded-2xl overflow-hidden">
                            <button 
                                @click="activeFaq = (activeFaq === 1 ? null : 1)"
                                type="button"
                                class="w-full px-4 py-3 text-left font-semibold text-xs sm:text-sm text-slate-900 flex items-center justify-between hover:bg-white/90 transition-colors"
                            >
                                <span>How do I receive my verified digital certificate?</span>
                                <i class="fas fa-chevron-down text-xs transition-transform" :class="activeFaq === 1 ? 'rotate-180 text-[#c4040a]' : 'text-slate-400'"></i>
                            </button>
                            <div x-show="activeFaq === 1" x-collapse class="px-4 pb-3 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-2.5 bg-white/60">
                                Upon completing the course curriculum and cohort evaluation, your digital certificate is officially issued by {{ $siteName }} with a cryptographically verifiable serial number and QR registry link recognized by employers and institutions.
                            </div>
                        </div>

                        <!-- FAQ 2 -->
                        <div class="border border-slate-200/80 bg-white/75 rounded-2xl overflow-hidden">
                            <button 
                                @click="activeFaq = (activeFaq === 2 ? null : 2)"
                                type="button"
                                class="w-full px-4 py-3 text-left font-semibold text-xs sm:text-sm text-slate-900 flex items-center justify-between hover:bg-white/90 transition-colors"
                            >
                                <span>What payment methods are supported?</span>
                                <i class="fas fa-chevron-down text-xs transition-transform" :class="activeFaq === 2 ? 'rotate-180 text-[#c4040a]' : 'text-slate-400'"></i>
                            </button>
                            <div x-show="activeFaq === 2" x-collapse class="px-4 pb-3 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-2.5 bg-white/60">
                                Instant bKash, Nagad, and Rocket mobile banking, as well as institutional bank transfers (BEFTN/NPSB). Official digital tax invoices are generated automatically in your portal.
                            </div>
                        </div>

                        <!-- FAQ 3 -->
                        <div class="border border-slate-200/80 bg-white/75 rounded-2xl overflow-hidden">
                            <button 
                                @click="activeFaq = (activeFaq === 3 ? null : 3)"
                                type="button"
                                class="w-full px-4 py-3 text-left font-semibold text-xs sm:text-sm text-slate-900 flex items-center justify-between hover:bg-white/90 transition-colors"
                            >
                                <span>What if I miss a live masterclass session?</span>
                                <i class="fas fa-chevron-down text-xs transition-transform" :class="activeFaq === 3 ? 'rotate-180 text-[#c4040a]' : 'text-slate-400'"></i>
                            </button>
                            <div x-show="activeFaq === 3" x-collapse class="px-4 pb-3 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-2.5 bg-white/60">
                                Full session recordings and course slides are archived within your student learning player for on-demand playback at your convenience.
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: Liquid Glass Purchase Card (4 Columns on Desktop, Sticky on Desktop) -->
            <aside class="lg:col-span-4 space-y-6 lg:sticky lg:top-8">
                <div class="liquid-glass-card rounded-3xl overflow-hidden shadow-[0_20px_50px_-15px_rgba(5,28,69,0.18)] border border-slate-200/80">
                    <!-- Top Cohort Status Header -->
                    <div class="bg-gradient-to-r from-[#063b96] to-[#0b2b66] px-6 py-3.5 flex items-center justify-between text-white">
                        <div class="flex items-center gap-2 text-xs font-bold">
                            @if($isRegistrationOpen)
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span>Admissions Open</span>
                            @else
                                <i class="fas fa-lock text-slate-300 text-[11px]"></i>
                                <span class="text-slate-200">Registration Closed</span>
                            @endif
                        </div>
                        @if(!empty($course->batch) && $course->show_batch)
                            <span class="px-2.5 py-0.5 rounded-full bg-white/15 border border-white/20 text-[10px] font-extrabold uppercase tracking-wider">
                                {{ $course->batch }}
                            </span>
                        @endif
                    </div>

                    <!-- Enrollment & Price -->
                    <div class="p-6 space-y-5">
                        <!-- Price TK Display (Required by Cypress) -->
                        <div class="pb-4 border-b border-slate-200/70">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Price TK:</span>
                            <div class="flex items-baseline justify-between gap-2 flex-wrap">
                                <div class="flex items-baseline gap-2.5">
                                    <span class="text-3xl font-black text-[#063b96] tracking-tight">
                                        {{ $course->price > 0 ? '৳' . number_format($course->price, 2) : 'Free Access' }}
                                    </span>
                                    @if($course->price > 0)
                                        <span class="text-sm text-slate-400 line-through font-medium">৳{{ number_format($course->price * 1.25, 0) }}</span>
                                    @endif
                                </div>
                                @if($course->price > 0)
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-extrabold">
                                        Save 20%
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        @if(!$isEnrolled)
                            @php
                                $sidebarPendingPayment = auth()->check()
                                    ? auth()->user()->payments()->where('course_id', $course->id)->whereIn('status', ['pending', 'processing'])->latest()->first()
                                    : null;
                            @endphp
                            <div class="space-y-3">
                                @if($sidebarPendingPayment)
                                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-900 space-y-1">
                                        <p class="font-bold flex items-center gap-1.5">
                                            <i class="fas fa-clock text-amber-600"></i>
                                            <span>Payment Pending Verification</span>
                                        </p>
                                        <p class="text-[11px] text-amber-800">
                                            TrxID: <span class="font-mono font-bold">{{ $sidebarPendingPayment->transaction_id }}</span>
                                        </p>
                                    </div>
                                @endif
                                @if($isRegistrationOpen)
                                    @if($course->price <= 0 && auth()->check())
                                        <form id="enroll-card" method="POST" action="{{ route('courses.enroll', $course) }}">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="btn-ipdp-primary enroll-dance-cta w-full py-3.5 text-white font-extrabold rounded-xl text-sm tracking-wide transition-all shadow-[0_8px_24px_rgba(196,4,10,0.3)] hover:scale-[1.01] active:scale-[0.99] cursor-pointer"
                                            >
                                                Enroll Now
                                            </button>
                                        </form>
                                    @else
                                        <button
                                            id="enroll-card"
                                            type="button"
                                            onclick="openEnrollDrawer({ 
                                                id: {{ $course->id }}, 
                                                title: '{{ addslashes($course->title) }}', 
                                                price: '{{ number_format($course->price, 2) }}', 
                                                rawPrice: {{ $course->price }}, 
                                                batch: '{{ (!empty($course->batch) && $course->show_batch) ? addslashes($course->batch) : '' }}', 
                                                venue: '{{ addslashes($course->venue ?? 'Online Live (Zoom)') }}',
                                                attendanceMode: '{{ $course->attendance_mode }}',
                                                whatsapp: '{{ $course->effective_whatsapp_number }}',
                                                fields: {{ json_encode($course->effective_enrollment_fields) }}
                                            })"
                                            class="btn-ipdp-primary enroll-dance-cta w-full py-3.5 text-white font-extrabold rounded-xl text-sm tracking-wide transition-all shadow-[0_8px_24px_rgba(196,4,10,0.3)] hover:scale-[1.01] active:scale-[0.99] flex items-center justify-center gap-2 cursor-pointer"
                                        >
                                            <span>Enroll Now</span>
                                            <i class="fas fa-arrow-right text-xs"></i>
                                        </button>
                                    @endif
                                    <p class="text-[11px] text-center text-slate-500 font-medium">
                                        Instant bKash, Nagad &amp; Bank &bull; Verified Certificate
                                    </p>
                                @else
                                    <div id="enroll-card" class="w-full py-3.5 bg-slate-200 text-slate-600 font-extrabold rounded-xl text-xs uppercase tracking-wider text-center flex items-center justify-center gap-1.5">
                                        <i class="fas fa-lock text-[11px]"></i>
                                        <span>Registration Closed</span>
                                    </div>
                                @endif

                                <!-- Wishlist Button (Required by Cypress) -->
                                <button 
                                    type="button" 
                                    onclick="toggleWishlist({{ $course->id }}, this)" 
                                    class="w-full py-2.5 bg-white hover:bg-slate-50 text-slate-600 font-semibold text-xs border border-slate-200 rounded-xl transition-colors flex items-center justify-center gap-1.5 cursor-pointer wishlist-btn-{{ $course->id }} {{ auth()->check() && auth()->user()->wishlists()->where('course_id', $course->id)->exists() ? 'text-[#c4040a] border-red-200 bg-red-50/50' : '' }}"
                                >
                                    <i class="{{ auth()->check() && auth()->user()->wishlists()->where('course_id', $course->id)->exists() ? 'fas text-[#c4040a]' : 'far' }} fa-heart text-xs"></i>
                                    <span>{{ auth()->check() && auth()->user()->wishlists()->where('course_id', $course->id)->exists() ? 'Saved in Wishlist' : 'Save to Wishlist' }}</span>
                                </button>
                            </div>
                        @else
                            <div class="space-y-2.5">
                                <div class="w-full py-2.5 bg-emerald-50 text-emerald-800 font-bold rounded-xl text-center text-xs border border-emerald-200 flex items-center justify-center gap-1.5">
                                    <i class="fas fa-check-circle text-emerald-600"></i>
                                    <span>Enrolled in Course</span>
                                </div>
                                <a 
                                    href="{{ $resumeLesson ? route('lessons.show', [$course, $resumeLesson]) : route('courses.classroom', $course) }}" 
                                    class="block w-full py-3 bg-[#063b96] hover:bg-[#082860] text-white font-bold rounded-xl text-center text-xs transition-colors shadow-sm"
                                >
                                    Continue Learning
                                </a>
                                @if($course->isCertificateAvailableFor(auth()->user()))
                                    <a 
                                        href="{{ route('courses.certificate', $course) }}" 
                                        target="_blank"
                                        class="block w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-[#063b96] font-bold rounded-xl text-center text-xs transition-colors"
                                    >
                                        View Verified Certificate
                                    </a>
                                @endif
                            </div>
                        @endif

                        <!-- Program Inclusions -->
                        <div class="pt-4 border-t border-slate-100 space-y-2.5 text-xs text-slate-600">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">This Program Includes</span>
                            <div class="flex items-center gap-2.5">
                                <i class="fas fa-video text-[#063b96] text-xs w-4 text-center"></i>
                                <span>Live Zoom Masterclasses + Class Recordings</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <i class="fas fa-certificate text-[#c4040a] text-xs w-4 text-center"></i>
                                <span>Official Verified Digital Certificate</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <i class="fas fa-folder-open text-[#063b96] text-xs w-4 text-center"></i>
                                <span>Practical Trade Documentation &amp; Templates</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <i class="fas fa-file-invoice text-emerald-700 text-xs w-4 text-center"></i>
                                <span>Corporate VAT &amp; Tax Invoice Available</span>
                            </div>
                        </div>

                        <!-- Subtle Counselor Link -->
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                            <span class="text-slate-500">Questions about this cohort?</span>
                            <a href="{{ $course->counselor_whatsapp_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 font-bold text-emerald-700 hover:text-emerald-800">
                                <i class="fab fa-whatsapp"></i>
                                <span>Chat Now</span>
                            </a>
                        </div>

                        <!-- Compact Single-Row Share Strip -->
                        @php
                            $sidebarShareDomain = strtolower(preg_replace('/^www\./i', '', parse_url(config('app.url'), PHP_URL_HOST) ?: 'acsacademy.bd'));
                            $sidebarSharePayload = [
                                'id' => $course->id,
                                'title' => $course->title,
                                'postHeader' => $course->stylish_post_header,
                                'stylishTitle' => $course->stylish_og_title,
                                'socialCaption' => $course->stylish_social_caption,
                                'shortUrl' => $course->short_url,
                                'courseUrl' => route('courses.show', $course),
                                'image' => $course->cover_image_url,
                                'displayDomain' => $sidebarShareDomain,
                                'facebookShareUrl' => $course->facebook_share_url,
                                'whatsappShareUrl' => $course->whatsapp_share_url,
                            ];
                        @endphp
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2 fb-link-preview-card">
                            <span class="text-[11px] font-bold text-slate-500">Share:</span>
                            <div class="flex items-center gap-1.5">
                                <a 
                                    href="{{ $course->facebook_share_url }}" 
                                    target="_blank" 
                                    rel="noopener noreferrer"
                                    data-share-payload="{{ json_encode($sidebarSharePayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}"
                                    onclick="event.preventDefault(); openCourseShareModal(JSON.parse(this.dataset.sharePayload)); return false;"
                                    class="px-2.5 py-1.5 rounded-lg bg-[#1877F2] hover:bg-[#166fe5] text-white text-[11px] font-bold flex items-center gap-1 transition-all cursor-pointer"
                                    title="Share on Facebook"
                                >
                                    <i class="fab fa-facebook-f text-[10px]"></i>
                                    <span>Facebook</span>
                                </a>
                                <button 
                                    type="button" 
                                    onclick="copyShortLink('{{ $course->short_url }}', this)" 
                                    class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-[#063b96] text-[11px] font-bold flex items-center gap-1 transition-all cursor-pointer"
                                    title="Copy Short Link ({{ $course->short_url }})"
                                >
                                    <i class="far fa-copy text-[10px]"></i>
                                    <span>Copy Link</span>
                                </button>
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
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

        </div>
    </main>

    <!-- 3. Mobile Sticky Action Bar (.lg:hidden.fixed.bottom-3 for Mobile Viewports - Full Bar Clickable) -->
    <style>
        @keyframes enrollDance {
            0%, 45%, 100% { transform: translateY(0) rotate(0deg) scale(1); }
            52% { transform: translateY(-6px) rotate(-3deg) scale(1.07); }
            60% { transform: translateY(0) rotate(3deg) scale(1.05); }
            68% { transform: translateY(-5px) rotate(-2.5deg) scale(1.07); }
            76% { transform: translateY(0) rotate(2deg) scale(1.03); }
            84% { transform: translateY(-2px) rotate(-1deg) scale(1.02); }
        }
        @keyframes enrollPulseRing {
            0% { box-shadow: 0 4px 16px rgba(195, 1, 2, 0.45), 0 0 0 0 rgba(225, 29, 72, 0.55); }
            65% { box-shadow: 0 8px 24px rgba(225, 29, 72, 0.65), 0 0 0 9px rgba(225, 29, 72, 0); }
            100% { box-shadow: 0 4px 16px rgba(195, 1, 2, 0.45), 0 0 0 0 rgba(225, 29, 72, 0); }
        }
        @keyframes enrollSheen {
            0%, 45% { transform: translateX(-140%) skewX(-20deg); }
            85%, 100% { transform: translateX(240%) skewX(-20deg); }
        }
        .enroll-dance-pill {
            position: relative;
            overflow: hidden;
            animation: enrollDance 2.2s ease-in-out infinite, enrollPulseRing 2.2s ease-out infinite;
            will-change: transform;
        }
        .enroll-dance-pill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 45%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            animation: enrollSheen 2.2s ease-in-out infinite;
            pointer-events: none;
        }
        .enroll-dance-bar:active .enroll-dance-pill,
        .enroll-dance-bar:active .enroll-dance-pill::after {
            animation: none;
        }
        @media (prefers-reduced-motion: reduce) {
            .enroll-dance-pill,
            .enroll-dance-pill::after { animation: none; }
        }
    </style>
    <div class="lg:hidden fixed bottom-3 left-4 right-4 z-30">
        @if($isEnrolled)
            <a 
                href="{{ $resumeLesson ? route('lessons.show', [$course, $resumeLesson]) : route('dashboard') }}"
                class="w-full bg-white/95 backdrop-blur-md border border-slate-200/90 rounded-2xl px-4 py-2.5 shadow-[0_10px_30px_rgba(4,21,54,0.18)] flex items-center justify-between gap-3 active:scale-[0.99] transition-transform"
            >
                <div class="min-w-0 text-left">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 block leading-none">Enrolled</span>
                    <span class="text-xs font-extrabold text-[#021f59] truncate block mt-0.5">{{ $course->title }}</span>
                </div>
                <span class="px-5 py-2.5 bg-[#063b96] text-white font-bold rounded-xl text-xs shrink-0 shadow-sm">
                    Continue
                </span>
            </a>
        @elseif($isRegistrationOpen)
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
                    whatsapp: '{{ $course->effective_whatsapp_number }}',
                    fields: {{ json_encode($course->effective_enrollment_fields) }}
                })"
                class="enroll-dance-bar w-full rounded-2xl px-4 py-2.5 flex items-center justify-between gap-3 text-left cursor-pointer active:scale-[0.99] transition-transform"
                style="background: linear-gradient(145deg, rgba(255, 255, 255, 0.88) 0%, rgba(248, 250, 252, 0.78) 100%); backdrop-filter: blur(24px) saturate(210%); -webkit-backdrop-filter: blur(24px) saturate(210%); border: 1px solid rgba(220, 162, 44, 0.48); box-shadow: 0 12px 32px -6px rgba(2, 31, 89, 0.24), inset 0 1.5px 0 rgba(255, 255, 255, 0.96);"
            >
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block leading-none">Course Fee</span>
                    <span class="text-base font-black leading-tight" style="color: #c4040a;">
                        {{ $course->price > 0 ? '৳' . number_format($course->price, 0) : 'Free' }}
                    </span>
                </div>
                <span class="enroll-dance-pill btn-ipdp-primary px-5 py-2.5 text-white font-bold rounded-xl shadow-md text-xs tracking-wide flex items-center gap-1.5 shrink-0"
                      style="background: linear-gradient(135deg, #ff1e1e 0%, #c4040a 52%, #850004 100%); color: #ffffff;">
                    <i class="fas fa-bolt text-amber-300 text-[11px]"></i>
                    <span>Enroll Now</span>
                </span>
            </button>
        @else
            <div class="w-full bg-white/95 backdrop-blur-md border border-slate-200/90 rounded-2xl px-4 py-2.5 shadow-[0_10px_30px_rgba(4,21,54,0.18)] flex items-center justify-between gap-3">
                <span class="text-xs font-bold text-slate-500">Registration Status</span>
                <span class="px-4 py-2 bg-slate-200 text-slate-600 font-bold rounded-xl text-xs flex items-center gap-1.5">
                    <i class="fas fa-lock text-[10px]"></i>
                    <span>Closed</span>
                </span>
            </div>
        @endif
    </div>

    @if($canShowEnrollCta)
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
                            whatsapp: '{{ $course->effective_whatsapp_number }}',
                            fields: @json($course->effective_enrollment_fields)
                        });
                    }
                }, 350);
            }
        });
    </script>
    @endif
</div>
@endsection
