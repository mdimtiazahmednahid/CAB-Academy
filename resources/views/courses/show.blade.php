@extends('layouts.public')

@section('title', $course->title)

@section('content')
<div class="space-y-8 pb-16">

    <!-- 1. Course Header / Banner (Apple-Grade Imperial Navy with Logo Palette) -->
    <div class="relative bg-gradient-to-br from-[#051c45] via-[#09214d] to-[#0f131f] text-white py-12 sm:py-16 border-b border-[#1e2d4a] overflow-hidden">
        <!-- Ambient Radial Glow -->
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-[#c30102]/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-[#90c642]/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumbs -->
            <nav class="flex flex-wrap items-center gap-2 text-xs text-gray-300 mb-4 font-medium">
                <a href="{{ url('/') }}" class="hover:text-white transition-colors">Home</a>
                <span>/</span>
                <a href="{{ route('catalog.index') }}" class="hover:text-white transition-colors">Course</a>
                @if($course->category)
                    <span>/</span>
                    <a href="{{ route('catalog.category', \Illuminate\Support\Str::slug($course->category)) }}" class="hover:text-white transition-colors">
                        {{ $course->category }}
                    </a>
                @endif
                <span>/</span>
                <span class="text-white font-semibold truncate max-w-xs">{{ $course->title }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <div class="lg:col-span-8">
                    <!-- Batch Scarcity Pill & Category Badge -->
                    <div class="flex flex-wrap items-center gap-2.5 mb-3">
                        <span class="px-3 py-1 rounded bg-[#c30102] text-white text-[11px] font-black tracking-wider uppercase shadow-md animate-pulse">
                            {{ $course->batch ?? '105th BATCH' }}
                        </span>
                        <span class="inline-block px-3 py-1 bg-white/10 backdrop-blur-sm text-[#90c642] border border-white/15 rounded text-xs font-bold uppercase tracking-wider">
                            {{ $course->category ?? 'Certificate Course' }}
                        </span>
                        <span class="text-xs text-emerald-400 font-semibold flex items-center gap-1">
                            <i class="fas fa-check-circle text-xs"></i> Seats Open for Mirpur & Zoom
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white tracking-tight leading-tight mb-4">
                        {{ $course->title }}
                    </h1>

                    <p class="text-gray-200 text-sm sm:text-base leading-relaxed mb-6 max-w-3xl font-normal">
                        {{ $course->short_description ?? ($course->description ?? 'Comprehensive professional training curriculum designed for industry mastery.') }}
                    </p>

                    <!-- Exact IPDP Course Price Display (.course-price: Price TK) -->
                    <div class="text-2xl sm:text-3xl font-black text-[#90c642] mb-6 flex items-baseline gap-2">
                        <span class="text-white text-base font-normal">Price TK:</span>
                        <span>{{ number_format($course->price, 2) }}</span>
                        @if($course->price > 0)
                            <span class="text-xs text-gray-400 font-normal line-through ml-2">৳ {{ number_format($course->price * 1.25, 0) }}</span>
                        @endif
                    </div>

                    <!-- Meta Badges -->
                    <div class="flex flex-wrap items-center gap-3 sm:gap-5 text-xs text-gray-200 mb-6">
                        <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm px-3.5 py-2 rounded-lg border border-white/15">
                            <i class="far fa-clock text-[#f15a29]"></i>
                            <span>Start Date: <strong>{{ $course->start_date ? $course->start_date->format('d F, Y') : ($course->duration ?? 'Upcoming') }}</strong></span>
                        </div>

                        <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm px-3.5 py-2 rounded-lg border border-white/15">
                            <i class="fas fa-map-marker-alt text-[#f15a29]"></i>
                            <span>{{ $course->venue ?? 'Mirpur-Pallabi Campus & Online Zoom' }}</span>
                        </div>

                        <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm px-3.5 py-2 rounded-lg border border-white/15">
                            <i class="fas fa-book-open text-[#f15a29]"></i>
                            <span>{{ $course->sections->flatMap->lessons->count() }} Lessons</span>
                        </div>
                    </div>

                    <!-- Instant Fast-Track Actions in Hero -->
                    @if(!$isEnrolled)
                        <div class="flex flex-wrap items-center gap-3.5">
                            <button 
                                type="button"
                                onclick="openEnrollDrawer({ 
                                    id: {{ $course->id }}, 
                                    title: '{{ addslashes($course->title) }}', 
                                    price: '{{ number_format($course->price, 2) }}', 
                                    rawPrice: {{ $course->price }}, 
                                    batch: '{{ $course->batch ?? '105th BATCH' }}', 
                                    venue: '{{ addslashes($course->venue ?? 'Mirpur-Pallabi Campus & Online Zoom') }}' 
                                })"
                                class="px-7 py-3 rounded-xl bg-[#c30102] hover:bg-[#a50102] text-white font-black text-sm uppercase tracking-wider shadow-xl hover:shadow-2xl transition-all duration-200 flex items-center gap-2 hover:scale-[1.02]"
                            >
                                <i class="fas fa-bolt text-xs"></i>
                                <span>Express Enroll (30 Sec)</span>
                            </button>

                            <a 
                                href="https://wa.me/8801917054664?text={{ urlencode('Hello CAB Academy, I would like to reserve a seat for ' . $course->title . ' (' . ($course->batch ?? '105th BATCH') . '). My name is: ') }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="px-6 py-3 rounded-xl bg-[#25D366] hover:bg-[#20ba5a] text-white font-bold text-sm shadow-lg flex items-center gap-2 transition-all hover:scale-[1.02]"
                            >
                                <i class="fab fa-whatsapp text-lg"></i>
                                <span>Reserve via WhatsApp</span>
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Right Video / Certification Badge Preview -->
                <div class="lg:col-span-4 hidden lg:block">
                    <div class="p-6 bg-white/10 backdrop-blur-md rounded-3xl border border-white/20 text-center space-y-4 shadow-2xl">
                        <div class="w-16 h-16 rounded-2xl bg-[#c30102]/20 border border-[#c30102]/40 text-[#c30102] flex items-center justify-center text-3xl mx-auto shadow-inner">
                            <i class="fas fa-award"></i>
                        </div>
                        <h4 class="text-base font-bold text-white leading-tight">Recognized Certification</h4>
                        <p class="text-xs text-gray-300 leading-relaxed">
                            Verified credential recognized across leading national and multinational industrial groups in Bangladesh.
                        </p>
                        <div class="pt-2 border-t border-white/10 flex items-center justify-center gap-4 text-xs text-gray-300">
                            <span><i class="fas fa-check text-[#90c642] mr-1"></i> Lifetime Access</span>
                            <span><i class="fas fa-check text-[#90c642] mr-1"></i> Physical + Zoom</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Course Main Grid Details -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- Left Main Column (8 Cols) -->
            <div class="lg:col-span-8 space-y-8">
                
                <!-- Course Banner Image -->
                <div class="rounded-2xl overflow-hidden shadow-sm border border-[#e3e7f0] bg-white">
                    @if($course->cover_image)
                        <img src="{{ asset($course->cover_image) }}" alt="{{ $course->title }}" class="w-full h-80 sm:h-96 object-cover">
                    @elseif($course->thumbnail)
                        <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}" class="w-full h-80 sm:h-96 object-cover">
                    @else
                        <div class="w-full h-80 bg-gradient-to-br from-[#f15a29] to-[#90c642] flex items-center justify-center text-white">
                            <span class="font-extrabold text-6xl opacity-30">{{ substr($course->title, 0, 1) }}</span>
                        </div>
                    @endif
                </div>

                <!-- Enrolled Progress Bar (If Enrolled) -->
                @if($isEnrolled)
                    @php
                        $totalLessons = $course->sections->flatMap->lessons->count();
                        $completedCount = count(array_intersect($completedLessonIds, $course->sections->flatMap->lessons->pluck('id')->toArray()));
                        $progress = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;
                    @endphp
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-green-200 bg-gradient-to-r from-green-50/50 to-white">
                        <div class="flex items-center justify-between text-sm mb-2 font-bold">
                            <span class="text-gray-800 flex items-center gap-2">
                                <i class="fas fa-check-circle text-green-600"></i>
                                Your Learning Progress
                            </span>
                            <span class="text-[#f15a29]">{{ $progress }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 mb-2 overflow-hidden">
                            <div class="bg-[#f15a29] h-3 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                        </div>
                        <div class="flex justify-between items-center text-xs text-gray-500 mt-2">
                            <span>{{ $completedCount }} of {{ $totalLessons }} lessons completed</span>
                            @if($course->sections->first() && $course->sections->first()->lessons->first())
                                <a href="{{ route('lessons.show', [$course, $course->sections->first()->lessons->first()]) }}" class="text-[#f15a29] font-bold hover:underline">
                                    Open Lesson Player &rarr;
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Course Description & Overview -->
                <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-[#e3e7f0]">
                    <h2 class="text-xl font-bold text-[#0f131f] mb-4 pb-2 border-b border-[#e3e7f0] flex items-center gap-2">
                        <span class="w-2 h-5 bg-[#f15a29] rounded inline-block"></span>
                        Course Overview
                    </h2>
                    <div class="text-[#35415b] leading-relaxed text-sm space-y-4">
                        <p>{{ $course->description ?? 'This program offers deep practical insight, live interactive case studies, and compliance methodologies essential for corporate mastery.' }}</p>
                    </div>
                </div>

                <!-- Apple-Grade Executive Transformation Matrix -->
                <div class="bg-gradient-to-br from-[#051c45] to-[#0a2540] rounded-3xl p-6 sm:p-8 text-white shadow-xl border border-white/10 relative overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-[#c30102]/10 rounded-full blur-2xl pointer-events-none"></div>

                    <div class="relative z-10 mb-6">
                        <span class="text-xs uppercase font-black tracking-widest text-[#90c642]">The CAB Academy Standard</span>
                        <h3 class="text-xl sm:text-2xl font-black text-white tracking-tight mt-1">
                            Why Corporate Leaders Choose This Masterclass
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 relative z-10 text-xs sm:text-sm">
                        <!-- Traditional Training -->
                        <div class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-2.5">
                            <span class="text-gray-400 font-bold uppercase text-[11px] block">Conventional Training</span>
                            <div class="flex items-start gap-2 text-gray-300">
                                <i class="fas fa-times text-red-400 mt-0.5"></i>
                                <span>Rote textbook theory without real port/customs cases</span>
                            </div>
                            <div class="flex items-start gap-2 text-gray-300">
                                <i class="fas fa-times text-red-400 mt-0.5"></i>
                                <span>Static slides with outdated HS codes & tariff rates</span>
                            </div>
                            <div class="flex items-start gap-2 text-gray-300">
                                <i class="fas fa-times text-red-400 mt-0.5"></i>
                                <span>Zero post-training peer networking or direct mentor Q&A</span>
                            </div>
                        </div>

                        <!-- CAB Academy Executive Masterclass -->
                        <div class="p-4 rounded-2xl bg-[#0a1f4a]/90 border border-[#90c642]/40 shadow-inner space-y-2.5">
                            <span class="text-[#90c642] font-black uppercase text-[11px] block flex items-center gap-1">
                                <i class="fas fa-check-circle"></i> CAB Academy Advantage
                            </span>
                            <div class="flex items-start gap-2 text-white font-medium">
                                <i class="fas fa-check text-[#90c642] mt-0.5"></i>
                                <span>Hands-on L/C, Bill of Entry, and Mushak 6.3 live simulations</span>
                            </div>
                            <div class="flex items-start gap-2 text-white font-medium">
                                <i class="fas fa-check text-[#90c642] mt-0.5"></i>
                                <span>Instruction directly from active NBR counsels & trade directors</span>
                            </div>
                            <div class="flex items-start gap-2 text-white font-medium">
                                <i class="fas fa-check text-[#90c642] mt-0.5"></i>
                                <span>Verified industrial credential recognized by top corporate groups</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Course Accordions (Exact IPDP .course-accordian with #90c642 header) -->
                <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-[#e3e7f0]">
                    <div class="flex items-center justify-between mb-6 pb-2 border-b border-[#e3e7f0]">
                        <h2 class="text-xl font-bold text-[#0f131f] flex items-center gap-2">
                            <span class="w-2 h-5 bg-[#f15a29] rounded inline-block"></span>
                            Course Curriculum
                        </h2>
                        <span class="text-xs font-semibold text-[#727f9f]">
                            {{ $course->sections->count() }} Modules &bull; {{ $course->sections->flatMap->lessons->count() }} Lessons
                        </span>
                    </div>

                    <div class="space-y-4">
                        @forelse($course->sections as $index => $section)
                            <div class="border border-[#e3e7f0] rounded-xl overflow-hidden shadow-sm" x-data="{ open: {{ $index === 0 ? 'true' : 'false' }} }">
                                <!-- Exact IPDP Accordion Header: #90c642 background with white text -->
                                <button 
                                    @click="open = !open" 
                                    class="w-full px-5 py-4 flex items-center justify-between bg-[#90c642] hover:bg-[#82b53b] text-white font-semibold transition-colors text-left"
                                >
                                    <div class="flex items-center gap-3">
                                        <div class="w-6 h-6 rounded-full bg-white/20 text-white font-bold text-xs flex items-center justify-center">
                                            {{ $index + 1 }}
                                        </div>
                                        <h3 class="font-bold text-sm sm:text-base text-white">{{ $section->title }}</h3>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs text-white/80 hidden sm:inline">{{ $section->lessons->count() }} {{ \Illuminate\Support\Str::plural('lesson', $section->lessons->count()) }}</span>
                                        <svg class="w-5 h-5 text-white transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                </button>

                                <!-- Accordion Body (Lessons List) -->
                                <div x-show="open" x-collapse>
                                    <div class="divide-y divide-gray-100 bg-white">
                                        @forelse($section->lessons as $lesson)
                                            @php
                                                $isCompleted = in_array($lesson->id, $completedLessonIds);
                                            @endphp
                                            @if($isEnrolled)
                                                <a href="{{ route('lessons.show', [$course, $lesson]) }}" class="flex items-center justify-between p-4 hover:bg-[#f2f4f7] transition-colors group">
                                            @else
                                                <div class="flex items-center justify-between p-4 bg-gray-50/50">
                                            @endif
                                                <div class="flex items-center gap-3">
                                                    @if($isCompleted)
                                                        <i class="fas fa-check-circle text-green-500"></i>
                                                    @else
                                                        <i class="far fa-circle text-gray-300"></i>
                                                    @endif
                                                    <span class="text-xs sm:text-sm font-medium text-[#0f131f] group-hover:text-[#f15a29] transition-colors">
                                                        {{ $lesson->title }}
                                                    </span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    @if($lesson->video_url)
                                                        <span class="text-[11px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded">Video</span>
                                                    @else
                                                        <span class="text-[11px] font-semibold text-gray-500 bg-gray-100 px-2 py-0.5 rounded">Reading</span>
                                                    @endif

                                                    @if(!$isEnrolled)
                                                        <i class="fas fa-lock text-gray-300 text-xs"></i>
                                                    @endif
                                                </div>
                                            @if($isEnrolled)
                                                </a>
                                            @else
                                                </div>
                                            @endif
                                        @empty
                                            <div class="p-4 text-xs text-gray-500 text-center">No lessons added to this section yet.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-gray-500 text-sm bg-gray-50 rounded-xl">
                                Course curriculum is being compiled and will be available shortly.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Who Should Attend / Target Audience -->
                @if($course->target_audience)
                    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-[#e3e7f0]">
                        <h2 class="text-xl font-bold text-[#0f131f] mb-4 pb-2 border-b border-[#e3e7f0] flex items-center gap-2">
                            <span class="w-2 h-5 bg-[#f15a29] rounded inline-block"></span>
                            Who Should Attend
                        </h2>
                        <div class="text-[#35415b] leading-relaxed text-sm space-y-2">
                            <p>{{ $course->target_audience }}</p>
                        </div>
                    </div>
                @endif

                <!-- Course Quizzes & Materials -->
                @if($course->quizzes->count() > 0 || $course->materials->count() > 0)
                    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-[#e3e7f0] space-y-6">
                        @if($course->quizzes->count() > 0)
                            <div>
                                <h3 class="font-bold text-[#0f131f] text-base mb-3">Assessments & Quizzes</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    @foreach($course->quizzes as $quiz)
                                        <div class="p-4 rounded-xl border border-[#e3e7f0] bg-gray-50 flex items-center justify-between">
                                            <div>
                                                <h4 class="font-bold text-sm text-[#0f131f]">{{ $quiz->title }}</h4>
                                                <span class="text-xs text-[#727f9f]">{{ $quiz->questions->count() }} Questions</span>
                                            </div>
                                            @if($isEnrolled)
                                                <a href="{{ route('quizzes.show', [$course, $quiz]) }}" class="btn-ipdp-primary px-3 py-1 rounded text-xs font-bold">Start</a>
                                            @else
                                                <span class="text-xs text-gray-400">Locked</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($course->materials->count() > 0)
                            <div>
                                <h3 class="font-bold text-[#0f131f] text-base mb-3">Downloadable Materials</h3>
                                <div class="space-y-2.5">
                                    @foreach($course->materials as $mat)
                                        <div class="p-3 rounded-lg border border-[#e3e7f0] bg-gray-50 flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded bg-[#f15a29]/10 text-[#f15a29] flex items-center justify-center font-bold text-xs uppercase">
                                                    {{ $mat->type ?? 'FILE' }}
                                                </div>
                                                <span class="font-semibold text-xs sm:text-sm text-[#0f131f]">{{ $mat->title }}</span>
                                            </div>
                                            @if($isEnrolled)
                                                <a href="{{ asset($mat->file_path) }}" target="_blank" class="text-xs font-bold text-[#f15a29] hover:underline">Download</a>
                                            @else
                                                <span class="text-xs text-gray-400">Enroll to access</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Executive Course FAQ Accordion -->
                <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-[#e3e7f0]">
                    <div class="flex items-center justify-between mb-6 pb-2 border-b border-[#e3e7f0]">
                        <h2 class="text-xl font-bold text-[#0f131f] flex items-center gap-2">
                            <span class="w-2 h-5 bg-[#f15a29] rounded inline-block"></span>
                            Frequently Asked Questions
                        </h2>
                        <span class="text-xs font-semibold text-[#727f9f]">Admissions & Certification</span>
                    </div>

                    <div class="space-y-3" x-data="{ activeFaq: null }">
                        <!-- FAQ 1 -->
                        <div class="border border-[#e3e7f0] rounded-xl overflow-hidden">
                            <button 
                                @click="activeFaq = (activeFaq === 1 ? null : 1)"
                                class="w-full px-5 py-3.5 text-left font-bold text-sm text-[#0f131f] flex items-center justify-between hover:bg-gray-50 transition-colors"
                            >
                                <span>How do I receive my verified digital certificate?</span>
                                <i class="fas fa-chevron-down text-xs transition-transform" :class="activeFaq === 1 ? 'rotate-180 text-[#f15a29]' : 'text-gray-400'"></i>
                            </button>
                            <div x-show="activeFaq === 1" x-collapse class="px-5 pb-4 text-xs sm:text-sm text-[#35415b] leading-relaxed border-t border-gray-100 pt-3 bg-gray-50/50">
                                Upon completing the course curriculum and module evaluation quiz, your digital certificate is automatically generated with an immutable serial number and QR validation link verifiable anywhere in the world by corporate employers.
                            </div>
                        </div>

                        <!-- FAQ 2 -->
                        <div class="border border-[#e3e7f0] rounded-xl overflow-hidden">
                            <button 
                                @click="activeFaq = (activeFaq === 2 ? null : 2)"
                                class="w-full px-5 py-3.5 text-left font-bold text-sm text-[#0f131f] flex items-center justify-between hover:bg-gray-50 transition-colors"
                            >
                                <span>What payment methods are accepted?</span>
                                <i class="fas fa-chevron-down text-xs transition-transform" :class="activeFaq === 2 ? 'rotate-180 text-[#f15a29]' : 'text-gray-400'"></i>
                            </button>
                            <div x-show="activeFaq === 2" x-collapse class="px-5 pb-4 text-xs sm:text-sm text-[#35415b] leading-relaxed border-t border-gray-100 pt-3 bg-gray-50/50">
                                We accept instant bKash, Nagad, and Rocket mobile payments, as well as electronic fund transfers (BEFTN/NPSB) to our official corporate bank accounts. Formal downloadable tax receipts are generated instantly in your student portal.
                            </div>
                        </div>

                        <!-- FAQ 3 -->
                        <div class="border border-[#e3e7f0] rounded-xl overflow-hidden">
                            <button 
                                @click="activeFaq = (activeFaq === 3 ? null : 3)"
                                class="w-full px-5 py-3.5 text-left font-bold text-sm text-[#0f131f] flex items-center justify-between hover:bg-gray-50 transition-colors"
                            >
                                <span>What if I miss a live class or workshop session?</span>
                                <i class="fas fa-chevron-down text-xs transition-transform" :class="activeFaq === 3 ? 'rotate-180 text-[#f15a29]' : 'text-gray-400'"></i>
                            </button>
                            <div x-show="activeFaq === 3" x-collapse class="px-5 pb-4 text-xs sm:text-sm text-[#35415b] leading-relaxed border-t border-gray-100 pt-3 bg-gray-50/50">
                                All lectures, simulation worksheets, and case reviews are archived within your student learning player. In addition, you have free transfer privileges to retake the session in the upcoming intake.
                            </div>
                        </div>

                        <!-- FAQ 4 -->
                        <div class="border border-[#e3e7f0] rounded-xl overflow-hidden">
                            <button 
                                @click="activeFaq = (activeFaq === 4 ? null : 4)"
                                class="w-full px-5 py-3.5 text-left font-bold text-sm text-[#0f131f] flex items-center justify-between hover:bg-gray-50 transition-colors"
                            >
                                <span>Can my employer sponsor my course tuition?</span>
                                <i class="fas fa-chevron-down text-xs transition-transform" :class="activeFaq === 4 ? 'rotate-180 text-[#f15a29]' : 'text-gray-400'"></i>
                            </button>
                            <div x-show="activeFaq === 4" x-collapse class="px-5 pb-4 text-xs sm:text-sm text-[#35415b] leading-relaxed border-t border-gray-100 pt-3 bg-gray-50/50">
                                Yes! We provide corporate billing invoices with institutional VAT/tax deduction details. Contact our corporate advisory desk at 01917-054664 or via the Contact page for enterprise purchase orders.
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Sticky Sidebar (4 Cols) -->
            <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">

                <!-- 1. Price & Enrollment Action Box -->
                <div class="bg-white rounded-2xl p-6 sm:p-7 shadow-md border border-[#e3e7f0]">
                    <div class="mb-5 pb-5 border-b border-[#e3e7f0]">
                        <span class="text-xs font-semibold uppercase tracking-wider text-[#727f9f] block mb-1">
                            Tuition Investment
                        </span>
                        <div class="flex items-baseline gap-2">
                            @if($course->price > 0)
                                <span class="text-3xl font-bold text-[#0f131f]">
                                    {{ number_format($course->price, 2) }} ৳
                                </span>
                            @else
                                <span class="text-3xl font-bold text-[#90c642]">Free Access</span>
                            @endif
                        </div>
                    </div>

                    <!-- Enrollment CTA -->
                    @if($isEnrolled)
                        <div class="space-y-3">
                            <div class="w-full py-2.5 bg-green-50 text-green-700 font-bold rounded text-center text-xs sm:text-sm border border-green-200 flex items-center justify-center gap-2">
                                <i class="fas fa-check-circle"></i>
                                Enrolled in Course
                            </div>
                            @if($course->sections->first() && $course->sections->first()->lessons->first())
                                <a 
                                    href="{{ route('lessons.show', [$course, $course->sections->first()->lessons->first()]) }}" 
                                    class="block w-full py-3.5 btn-ipdp-primary font-bold rounded text-center shadow-md"
                                >
                                    Open Learning Player
                                </a>
                            @endif
                            <a 
                                href="{{ route('courses.certificate', $course) }}" 
                                target="_blank"
                                class="block w-full py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold rounded-xl text-center text-xs border border-emerald-200 transition-colors flex items-center justify-center gap-1.5"
                            >
                                <i class="fas fa-award text-emerald-600"></i>
                                <span>Digital Certificate</span>
                            </a>
                        </div>
                    @else
                        <form 
                            method="POST" 
                            action="{{ route('courses.enroll', $course) }}"
                            onsubmit="event.preventDefault(); openEnrollDrawer({ 
                                id: {{ $course->id }}, 
                                title: '{{ addslashes($course->title) }}', 
                                price: '{{ number_format($course->price, 2) }}', 
                                rawPrice: {{ $course->price }}, 
                                batch: '{{ $course->batch ?? '105th BATCH' }}', 
                                venue: '{{ addslashes($course->venue ?? 'Mirpur-Pallabi Campus & Online Zoom') }}' 
                            })"
                            class="space-y-2.5"
                        >
                            @csrf
                            <button 
                                type="submit" 
                                class="w-full py-3.5 bg-[#c30102] hover:bg-[#a50102] text-white font-black rounded-xl text-base shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2 hover:scale-[1.01]"
                            >
                                <i class="fas fa-bolt text-xs"></i>
                                <span>Enroll Now</span>
                            </button>
                        </form>

                        <a 
                            href="https://wa.me/8801917054664?text={{ urlencode('Hello CAB Academy, I want to reserve a seat for: ' . $course->title . ' (' . ($course->batch ?? '105th BATCH') . '). My name is: ') }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="w-full py-3 mt-2.5 bg-[#25D366] hover:bg-[#20ba5a] text-white font-bold rounded-xl text-xs sm:text-sm shadow-md transition-all flex items-center justify-center gap-2"
                        >
                            <i class="fab fa-whatsapp text-base"></i>
                            <span>Reserve via WhatsApp</span>
                        </a>
                    @endif

                    <!-- Wishlist Toggle Button -->
                    <button 
                        type="button" 
                        onclick="toggleWishlist({{ $course->id }}, this)" 
                        class="w-full py-2.5 mt-2.5 bg-gray-100 hover:bg-gray-200 text-[#0f131f] font-bold rounded-xl text-xs transition-all flex items-center justify-center gap-2 wishlist-btn-{{ $course->id }} {{ auth()->check() && auth()->user()->wishlists()->where('course_id', $course->id)->exists() ? 'text-[#f15a29] bg-orange-50' : '' }}"
                    >
                        <i class="{{ auth()->check() && auth()->user()->wishlists()->where('course_id', $course->id)->exists() ? 'fas text-[#f15a29]' : 'far' }} fa-heart"></i>
                        <span>{{ auth()->check() && auth()->user()->wishlists()->where('course_id', $course->id)->exists() ? 'Saved in Wishlist' : 'Save to Wishlist' }}</span>
                    </button>

                    <!-- Value Features List -->
                    <div class="mt-6 pt-6 border-t border-[#e3e7f0] space-y-2.5 text-xs text-[#35415b]">
                        <div class="flex items-center gap-2.5">
                            <i class="fas fa-certificate text-[#f15a29]"></i>
                            <span>Official Certificate of Completion</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <i class="fas fa-folder-open text-[#f15a29]"></i>
                            <span>Lifetime access to course materials</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <i class="fas fa-credit-card text-[#f15a29]"></i>
                            <span>Supports bKash, Nagad, Rocket, Bank</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Exact IPDP Instructor Card (.course-teacher-image: 150px circle) -->
                @if($course->instructor)
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#e3e7f0] text-center">
                        <div class="w-[150px] h-[150px] rounded-full border-4 border-gray-100 shadow-md mx-auto mb-4 overflow-hidden bg-gray-100">
                            @if($course->instructor->profile_picture)
                                <img src="{{ asset($course->instructor->profile_picture) }}" alt="{{ $course->instructor->name }}" class="w-full h-full object-cover object-top">
                            @else
                                <div class="w-full h-full bg-[#f2f4f7] text-[#f15a29] flex items-center justify-center font-bold text-3xl">
                                    {{ substr($course->instructor->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <h4 class="font-bold text-[#0f131f] text-base mb-1">{{ $course->instructor->name }}</h4>
                        <p class="text-xs text-[#f15a29] font-semibold uppercase tracking-wider mb-2">Senior Faculty & Specialist</p>
                        <p class="text-xs text-[#727f9f] leading-relaxed">
                            Distinguished Faculty Member with extensive international trade, corporate compliance, and executive advisory experience.
                        </p>
                    </div>
                @endif

                <!-- 3. Operational Office & Venue Box -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#e3e7f0] space-y-2 text-xs text-[#35415b]">
                    <h4 class="font-bold text-[#0f131f] text-sm uppercase tracking-wider">Venue & Schedule</h4>
                    <p class="text-[#727f9f] leading-relaxed">
                        <strong class="text-[#0f131f]">Campus:</strong> {{ $course->venue ?? 'Mirpur-Pallabi, Dhaka, Bangladesh, 1216' }}
                    </p>
                    <p class="text-[#727f9f]">
                        <strong class="text-[#0f131f]">Format:</strong> In-person classroom or live Zoom sessions.
                    </p>
                </div>

            </div>

        </div>
    </div>

    <!-- 3. Exact IPDP Mobile Sticky Action Bar (.add-to-cart-button: bottom 10px, 60% width) -->
    <div class="lg:hidden fixed bottom-3 left-1/2 -translate-x-1/2 w-[75%] max-w-sm z-50">
        @if($isEnrolled)
            <a 
                href="{{ $course->sections->first() && $course->sections->first()->lessons->first() ? route('lessons.show', [$course, $course->sections->first()->lessons->first()]) : '#' }}"
                class="block w-full py-3 bg-[#051c45] text-white font-bold rounded-xl shadow-2xl text-center text-sm"
            >
                Continue Learning
            </a>
        @else
            <form 
                method="POST" 
                action="{{ route('courses.enroll', $course) }}" 
                onsubmit="event.preventDefault(); openEnrollDrawer({ 
                    id: {{ $course->id }}, 
                    title: '{{ addslashes($course->title) }}', 
                    price: '{{ number_format($course->price, 2) }}', 
                    rawPrice: {{ $course->price }}, 
                    batch: '{{ $course->batch ?? '105th BATCH' }}', 
                    venue: '{{ addslashes($course->venue ?? 'Mirpur-Pallabi Campus & Online Zoom') }}' 
                })"
                class="m-0"
            >
                @csrf
                <button type="submit" class="w-full py-3.5 bg-[#c30102] hover:bg-[#a50102] text-white font-black rounded-xl shadow-2xl text-center text-sm flex items-center justify-center gap-2">
                    <i class="fas fa-bolt text-xs"></i>
                    <span>Enroll Now (৳{{ number_format($course->price, 0) }})</span>
                </button>
            </form>
        @endif
    </div>

</div>
@endsection
