<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <title>{{ $lesson ? $lesson->title . ' - ' : 'Classroom - ' }}{{ $course->title }} - {{ \App\Models\Setting::getVal('site_name', 'ACS Academy') }}</title>
    <link rel="icon" href="{{ \App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {!! acs_inline_compiled_css() !!}
    <style>
        :root {
            --primary-color: {{ \App\Models\Setting::getVal('primary_color', '#051c45') }};
        }
        {!! \App\Models\Setting::getTypographyCss() !!}
        .bg-primary { background-color: var(--primary-color); }
        .text-primary { color: var(--primary-color); }
        .pb-safe { padding-bottom: max(env(safe-area-inset-bottom), 1.25rem); }
        .pt-safe { padding-top: max(env(safe-area-inset-top), 0.75rem); }

        .video-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            width: 100%;
            background: #020b18;
        }
        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
@php
    $user = auth()->user();
    $completedIds = $completedLessonIds ?? [];
    $allLessons = $course->sections->flatMap->lessons;
    $totalLessons = $allLessons->count();
    $doneLessons = count(array_intersect($completedIds, $allLessons->pluck('id')->toArray()));
    $coursePct = $totalLessons > 0 ? round(($doneLessons / $totalLessons) * 100) : 0;

    $stage = $lesson ? $lesson->resolveClassroomStage($user) : null;
    $embedUrl = $stage['embed_url'] ?? null;
    $materialsCount = $course->materials ? $course->materials->count() : 0;
    $quizzesCount = $course->quizzes ? $course->quizzes->count() : 0;
    $defaultTab = (!$lesson && $materialsCount > 0) ? 'resources' : 'notes';
@endphp
<body class="font-sans antialiased bg-[#f1f5f9] text-slate-900 pb-24"
      x-data="{
          classTab: @js($defaultTab),
          theaterMode: false,
          openModuleId: {{ $lesson ? (int) $lesson->section_id : (int) ($course->sections->first()->id ?? 0) }}
      }">

    <!-- 10MS Top Classroom Header Bar -->
    <header class="flex items-center justify-between px-3 sm:px-6 py-2.5 border-b border-white/10 sticky top-0 bg-[#051c45] text-white z-40 pt-safe shadow-sm">
        <div class="flex items-center min-w-0 gap-3">
            <a href="{{ route('dashboard') }}" class="p-2 rounded-xl bg-white/10 hover:bg-white/20 text-white transition-colors shrink-0" title="Back to Student Dashboard">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <h1 class="text-xs sm:text-sm font-extrabold text-white line-clamp-1">
                        {{ $lesson ? $lesson->title : $course->title }}
                    </h1>
                    @if($stage && $stage['is_live'])
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-[#c30102] text-white text-[10px] font-black uppercase tracking-wider shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                            <span>LIVE</span>
                        </span>
                    @endif
                </div>
                <p class="text-[11px] text-slate-300 truncate">{{ $course->title }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <!-- Course Progress Pill -->
            <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/10 border border-white/15 text-xs font-bold">
                <div class="w-16 bg-white/15 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-emerald-400 h-1.5 rounded-full" style="width: {{ max(5, $coursePct) }}%"></div>
                </div>
                <span class="text-emerald-300 font-extrabold">{{ $coursePct }}%</span>
                <span class="text-slate-300 text-[11px]">({{ $doneLessons }}/{{ $totalLessons }})</span>
            </div>

            <a href="{{ route('courses.show', $course) }}" class="hidden md:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition-colors">
                <span>Course Info</span>
            </a>

            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#c30102] hover:bg-[#a50102] text-white text-xs font-extrabold transition-colors">
                <i class="fas fa-th-large text-[10px]"></i>
                <span>My Courses</span>
            </a>
        </div>
    </header>

    <!-- 10MS Split-Screen Classroom Workspace -->
    <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-4 sm:py-5 grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

        <!-- LEFT COLUMN: Primary Media Stage + Action Bar + 10MS Content Tabs -->
        <div :class="theaterMode ? 'lg:col-span-12' : 'lg:col-span-8'" class="lg:col-span-8 min-w-0 space-y-4">

            @if($lesson && $stage && $stage['is_live'] && $stage['can_iframe'])
                <!-- CASE A: IN-BROWSER LIVE CLASSROOM STAGE (JITSI MEET / ZOOM WEB CLIENT) -->
                <div id="live-classroom-stage" class="w-full bg-[#020b18] rounded-2xl overflow-hidden shadow-lg border border-slate-800">
                    <!-- Live Stage Top Control Bar -->
                    <div class="px-4 py-2.5 bg-[#051c45] border-b border-white/10 flex flex-wrap items-center justify-between gap-2 text-white">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#c30102] text-white text-[10px] font-black uppercase tracking-wider shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                <span>{{ $stage['provider_label'] }}</span>
                            </span>
                            <span class="text-xs font-semibold text-slate-200 truncate hidden sm:inline">
                                Joining as: <strong class="text-white">{{ $user->name }}</strong>
                            </span>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button"
                                    @click="theaterMode = !theaterMode"
                                    class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-white text-[11px] font-bold transition-colors inline-flex items-center gap-1.5 cursor-pointer"
                                    title="Toggle Wide Stage">
                                <i class="fas" :class="theaterMode ? 'fa-compress' : 'fa-expand'"></i>
                                <span x-text="theaterMode ? 'Standard View' : 'Wide Stage'"></span>
                            </button>

                            <button type="button"
                                    onclick="const el = document.getElementById('live-classroom-stage'); if (el && el.requestFullscreen) { el.requestFullscreen(); }"
                                    class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-white text-[11px] font-bold transition-colors inline-flex items-center gap-1.5 cursor-pointer"
                                    title="Full Screen">
                                <i class="fas fa-up-right-and-down-left-from-center text-[10px]"></i>
                                <span class="hidden sm:inline">Fullscreen</span>
                            </button>

                            @if($stage['external_url'])
                                <a href="{{ $stage['external_url'] }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="px-3 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] font-extrabold transition-colors inline-flex items-center gap-1.5">
                                    <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                                    <span>{{ $stage['provider'] === 'zoom' ? 'Open Zoom App' : 'Open in New Tab' }}</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Responsive WebRTC Live Class Iframe -->
                    <div class="relative w-full min-h-[460px] sm:min-h-[540px] bg-[#020b18]">
                        <iframe id="live-classroom-iframe"
                                src="{{ $stage['embed_url'] }}"
                                class="w-full h-[460px] sm:h-[540px] border-0"
                                allow="camera; microphone; fullscreen; display-capture; autoplay; clipboard-read; clipboard-write; speaker-selection"
                                allowfullscreen></iframe>
                    </div>
                </div>

            @elseif($lesson && $stage && $stage['provider'] === 'google_meet')
                <!-- CASE B: GOOGLE MEET LIVE STAGE (POP-OUT CLASSROOM LAUNCHER) -->
                <div class="w-full bg-gradient-to-br from-[#020b18] via-[#051c45] to-[#0a2a66] rounded-2xl p-6 sm:p-10 text-white shadow-lg border border-white/15 relative overflow-hidden">
                    <div class="max-w-xl mx-auto text-center space-y-4 relative z-10">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#c30102] text-white text-[11px] font-black uppercase tracking-wider">
                            <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                            <span>Google Meet Live Classroom</span>
                        </div>

                        <h2 class="text-xl sm:text-2xl font-black tracking-tight text-white">
                            {{ $lesson->title }}
                        </h2>

                        <p class="text-xs sm:text-sm text-slate-200 leading-relaxed">
                            Joining as <strong class="text-white">{{ $user->name }}</strong> ({{ $user->email }}).
                            @if($stage['meeting_id'])
                                &bull; Room Code: <code class="px-2 py-0.5 rounded bg-white/10 text-amber-300 font-mono">{{ $stage['meeting_id'] }}</code>
                            @endif
                        </p>

                        <div class="pt-2 flex flex-wrap items-center justify-center gap-3">
                            <button type="button"
                                    onclick="window.open('{{ $stage['external_url'] }}', 'acs_gmeet_{{ $lesson->id }}', 'width=1240,height=820,menubar=no,toolbar=no,location=yes,status=no'); return false;"
                                    class="px-6 py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs sm:text-sm shadow-lg inline-flex items-center gap-2 transition-all cursor-pointer">
                                <i class="fas fa-video"></i>
                                <span>Join Google Meet (Pop-Out Classroom)</span>
                            </button>

                            <a href="{{ $stage['external_url'] }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="px-4 py-3.5 rounded-xl bg-white/10 hover:bg-white/20 text-white border border-white/20 font-bold text-xs sm:text-sm inline-flex items-center gap-2 transition-all">
                                <span>Open in New Tab</span>
                                <i class="fas fa-arrow-up-right-from-square text-xs"></i>
                            </a>
                        </div>
                    </div>
                </div>

            @elseif($lesson && $embedUrl)
                <!-- CASE C: RECORDED VIDEO STAGE (YOUTUBE / VIMEO / CUSTOM) -->
                <div class="w-full bg-[#020b18] rounded-2xl overflow-hidden shadow-md border border-slate-200">
                    <div class="video-container">
                        <iframe src="{{ $embedUrl }}"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen"
                                allowfullscreen></iframe>
                    </div>
                </div>

            @elseif(!$lesson)
                <!-- CASE D: 0-LESSON LIVE BATCH ORIENTATION STAGE (E.G., COURSE #18) -->
                <div class="w-full bg-gradient-to-br from-[#031029] via-[#051c45] to-[#0a2a66] rounded-2xl p-5 sm:p-7 text-white shadow-md border border-white/15">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="space-y-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 text-[11px] font-extrabold uppercase tracking-wider">
                                <i class="fas fa-check-circle"></i>
                                <span>Enrolled Classroom Hub</span>
                            </span>
                            <h2 class="text-lg sm:text-xl font-black text-white">{{ $course->title }}</h2>
                            <p class="text-xs text-slate-300 flex flex-wrap items-center gap-3">
                                <span><i class="fas fa-chalkboard-teacher text-amber-300 mr-1"></i>{{ $course->instructor_name }}</span>
                                <span>&bull; <i class="fas fa-video text-cyan-300 mr-1"></i>{{ $course->venue ?: 'Online Live (Zoom)' }}</span>
                                @if($course->start_date)
                                    <span>&bull; <i class="far fa-calendar-alt text-emerald-300 mr-1"></i>Starts: {{ $course->start_date->format('d M, Y') }}</span>
                                @endif
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 shrink-0">
                            @if($materialsCount > 0)
                                <button type="button" @click="classTab = 'resources'" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-extrabold inline-flex items-center gap-1.5 cursor-pointer">
                                    <i class="fas fa-folder-open"></i>
                                    <span>Open Resources ({{ $materialsCount }})</span>
                                </button>
                            @endif
                            <a href="{{ $course->counselor_whatsapp_url }}" target="_blank" rel="noopener noreferrer" class="px-4 py-2.5 rounded-xl bg-[#25D366] hover:bg-[#20ba5a] text-white text-xs font-extrabold inline-flex items-center gap-1.5">
                                <i class="fab fa-whatsapp"></i>
                                <span>Batch Support</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- 10MS Lesson Header & Quick Completion Bar -->
            @if($lesson)
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-[11px] font-extrabold uppercase tracking-wider text-[#c30102]">
                                {{ $lesson->section->title ?? 'Module' }}
                            </span>
                            @if($stage && $stage['is_live'])
                                <span class="px-2 py-0.5 rounded bg-rose-50 text-[#c30102] border border-rose-200 text-[10px] font-extrabold">
                                    {{ $stage['provider_label'] }}
                                </span>
                            @endif
                            @if($isCompleted)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-bold">
                                    <i class="fas fa-check-circle"></i> Completed
                                </span>
                            @endif
                        </div>
                        <h2 class="text-base sm:text-lg font-extrabold text-slate-900 mt-0.5">{{ $lesson->title }}</h2>
                    </div>

                    <form method="POST" action="{{ route('lessons.complete', [$course, $lesson]) }}" class="flex items-center gap-2 shrink-0">
                        @csrf
                        <input type="hidden" name="next_lesson_url" value="{{ $nextLesson ? route('lessons.show', [$course, $nextLesson]) : route('courses.classroom', $course) }}">

                        @if($isCompleted)
                            <button type="button" disabled class="px-4 py-2.5 rounded-xl font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 flex items-center gap-1.5 text-xs">
                                <i class="fas fa-check"></i>
                                <span>Completed</span>
                            </button>
                        @else
                            <button type="submit" class="px-4 py-2.5 rounded-xl font-extrabold text-white bg-[#051c45] hover:bg-[#082b66] transition-all flex items-center gap-1.5 text-xs cursor-pointer shadow-2xs">
                                <i class="fas fa-check-circle text-emerald-400"></i>
                                <span>Mark as Complete</span>
                            </button>
                        @endif

                        @if($nextLesson)
                            <a href="{{ route('lessons.show', [$course, $nextLesson]) }}" class="px-4 py-2.5 rounded-xl font-extrabold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-all text-xs whitespace-nowrap">
                                Next &rarr;
                            </a>
                        @endif
                    </form>
                </div>
            @endif

            <!-- 10MS Horizontal Classroom Tabs (Notes | Resources | Quizzes | Certificate) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
                <div class="flex items-center gap-1 p-2 bg-slate-50 border-b border-slate-200/70 overflow-x-auto">
                    <button type="button"
                            @click="classTab = 'notes'"
                            :class="classTab === 'notes' ? 'bg-[#051c45] text-white shadow-2xs' : 'text-slate-600 hover:bg-white'"
                            class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap inline-flex items-center gap-1.5 cursor-pointer">
                        <i class="fas fa-book-open text-[11px]"></i>
                        <span>Class Notes</span>
                    </button>

                    <button type="button"
                            @click="classTab = 'resources'"
                            :class="classTab === 'resources' ? 'bg-[#051c45] text-white shadow-2xs' : 'text-slate-600 hover:bg-white'"
                            class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap inline-flex items-center gap-1.5 cursor-pointer">
                        <i class="fas fa-folder-open text-[11px]"></i>
                        <span>Resources ({{ $materialsCount }})</span>
                    </button>

                    <button type="button"
                            @click="classTab = 'quizzes'"
                            :class="classTab === 'quizzes' ? 'bg-[#051c45] text-white shadow-2xs' : 'text-slate-600 hover:bg-white'"
                            class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap inline-flex items-center gap-1.5 cursor-pointer">
                        <i class="fas fa-clipboard-check text-[11px]"></i>
                        <span>Quizzes ({{ $quizzesCount }})</span>
                    </button>

                    <button type="button"
                            @click="classTab = 'certificate'"
                            :class="classTab === 'certificate' ? 'bg-[#051c45] text-white shadow-2xs' : 'text-slate-600 hover:bg-white'"
                            class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap inline-flex items-center gap-1.5 cursor-pointer">
                        <i class="fas fa-award text-[11px]"></i>
                        <span>Certificate</span>
                    </button>
                </div>

                <!-- Tab 1: Class Notes -->
                <div x-show="classTab === 'notes'" class="p-4 sm:p-6">
                    @if($lesson && $lesson->content)
                        <div class="prose prose-slate max-w-none text-slate-700 leading-relaxed text-xs sm:text-sm">
                            {!! nl2br(e($lesson->content)) !!}
                        </div>
                    @elseif(!$lesson && ($course->description || $course->short_description))
                        <div class="prose prose-slate max-w-none text-slate-700 leading-relaxed text-xs sm:text-sm">
                            {!! nl2br(e($course->description ?: $course->short_description)) !!}
                        </div>
                    @else
                        <div class="p-6 text-center text-slate-400 bg-slate-50 rounded-xl text-xs">
                            No additional text notes for this session. Check the <strong>Resources</strong> tab for downloadable sheets.
                        </div>
                    @endif
                </div>

                <!-- Tab 2: Resources & Sheets -->
                <div x-show="classTab === 'resources'" x-cloak class="p-4 sm:p-6">
                    @if($materialsCount > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($course->materials as $mat)
                                <a href="{{ $mat->type === 'link' ? $mat->external_link : storage_asset($mat->file_path) }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200/80 text-xs font-bold text-[#051c45] transition-all group">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <span class="w-9 h-9 rounded-lg bg-[#c30102]/10 text-[#c30102] flex items-center justify-center shrink-0">
                                            <i class="fas {{ $mat->type === 'link' ? 'fa-link' : 'fa-file-pdf' }}"></i>
                                        </span>
                                        <div class="min-w-0">
                                            <div class="truncate group-hover:text-[#c30102] transition-colors">{{ $mat->title }}</div>
                                            <div class="text-[10px] text-slate-400 uppercase font-semibold">{{ $mat->type }} Resource</div>
                                        </div>
                                    </div>
                                    <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-[11px] font-extrabold text-[#051c45] shrink-0">
                                        Open &rarr;
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="p-6 text-center text-slate-400 bg-slate-50 rounded-xl text-xs">
                            No downloadable resources uploaded for this course yet.
                        </div>
                    @endif
                </div>

                <!-- Tab 3: Quizzes -->
                <div x-show="classTab === 'quizzes'" x-cloak class="p-4 sm:p-6">
                    @if($quizzesCount > 0)
                        <div class="space-y-2.5">
                            @foreach($course->quizzes as $q)
                                <a href="{{ route('quizzes.show', [$course, $q]) }}"
                                   class="flex items-center justify-between p-3.5 rounded-xl bg-emerald-50/60 hover:bg-emerald-100/70 border border-emerald-200 text-xs font-extrabold text-emerald-950 transition-all">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <span class="w-9 h-9 rounded-lg bg-emerald-600 text-white flex items-center justify-center shrink-0">
                                            <i class="fas fa-clipboard-question"></i>
                                        </span>
                                        <div class="min-w-0">
                                            <div class="truncate">{{ $q->title }}</div>
                                            @if($q->description)
                                                <div class="text-[11px] text-emerald-700 font-medium truncate">{{ $q->description }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-[11px] font-extrabold shrink-0">
                                        Start Quiz &rarr;
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="p-6 text-center text-slate-400 bg-slate-50 rounded-xl text-xs">
                            No quizzes assigned to this course yet.
                        </div>
                    @endif
                </div>

                <!-- Tab 4: Certificate Status -->
                <div x-show="classTab === 'certificate'" x-cloak class="p-4 sm:p-6">
                    @if($course->isCertificateAvailableFor($user))
                        <div class="p-5 rounded-xl bg-emerald-50 border border-emerald-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div>
                                <div class="text-xs font-black uppercase tracking-wider text-emerald-800">Certificate Unlocked</div>
                                <p class="text-xs text-emerald-700 mt-0.5">Your official verifiable digital certificate for <strong>{{ $course->title }}</strong> is ready.</p>
                            </div>
                            <a href="{{ route('courses.certificate', $course) }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-extrabold inline-flex items-center gap-2 shrink-0">
                                <i class="fas fa-award"></i>
                                <span>Download Certificate</span>
                            </a>
                        </div>
                    @else
                        <div class="p-5 rounded-xl bg-slate-50 border border-slate-200/80 text-xs text-slate-600 flex items-center justify-between gap-4">
                            <div>
                                <div class="font-extrabold text-slate-900">Digital Certificate Locked</div>
                                <p class="text-[11px] text-slate-500 mt-0.5">Complete all course modules ({{ $coursePct }}% completed) and await instructor certificate release.</p>
                            </div>
                            <span class="px-3 py-1 rounded-lg bg-slate-200 text-slate-700 font-bold text-[11px] shrink-0">{{ $coursePct }}% Done</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: 10MS Collapsible Module Accordions + Quick Resources -->
        <aside :class="theaterMode ? 'lg:col-span-12' : 'lg:col-span-4'" class="lg:col-span-4 min-w-0 space-y-4">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
                <div class="p-4 bg-[#051c45] text-white flex items-center justify-between">
                    <div>
                        <h3 class="font-extrabold text-xs sm:text-sm">Course Curriculum</h3>
                        <p class="text-[11px] text-slate-300 mt-0.5">{{ $course->sections->count() }} Modules &bull; {{ $doneLessons }}/{{ $totalLessons }} Completed</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg bg-white/10 text-xs font-black text-emerald-300">{{ $coursePct }}%</span>
                </div>

                @if($course->sections->count() > 0)
                    <div class="divide-y divide-slate-100 max-h-[520px] overflow-y-auto">
                        @foreach($course->sections as $secIdx => $sec)
                            @php
                                $secLessonsCount = $sec->lessons->count();
                                $secDoneCount = $sec->lessons->filter(fn($l) => in_array($l->id, $completedIds))->count();
                            @endphp
                            <div class="bg-white">
                                <!-- Module Accordion Header -->
                                <button type="button"
                                        @click="openModuleId = (openModuleId === {{ (int) $sec->id }} ? 0 : {{ (int) $sec->id }})"
                                        class="w-full px-4 py-3 bg-slate-50/80 hover:bg-slate-100/80 flex items-center justify-between gap-2 text-left transition-colors cursor-pointer">
                                    <div class="min-w-0">
                                        <div class="text-xs font-extrabold text-[#051c45] truncate">
                                            {{ str_starts_with(strtolower($sec->title), 'module') ? $sec->title : 'Module ' . ($secIdx + 1) . ': ' . $sec->title }}
                                        </div>
                                        <div class="text-[10px] font-semibold text-slate-500 mt-0.5">
                                            {{ $secDoneCount }}/{{ $secLessonsCount }} Lessons Completed
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200 shrink-0"
                                       :class="openModuleId === {{ (int) $sec->id }} ? 'rotate-180 text-[#051c45]' : ''"></i>
                                </button>

                                <!-- Module Lessons List -->
                                <div x-show="openModuleId === {{ (int) $sec->id }}" class="p-2 space-y-1 bg-white border-t border-slate-100">
                                    @forelse($sec->lessons as $secLesson)
                                        @php
                                            $secLessonDone = in_array($secLesson->id, $completedIds);
                                            $isCurrent = $lesson && (int) $secLesson->id === (int) $lesson->id;
                                            $isLiveItem = $secLesson->is_live_class;
                                        @endphp
                                        <a href="{{ route('lessons.show', [$course, $secLesson]) }}"
                                           class="flex items-center justify-between gap-2 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ $isCurrent ? 'bg-[#051c45] text-white shadow-2xs' : 'text-slate-700 hover:bg-slate-50' }}">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                @if($secLessonDone)
                                                    <i class="fas fa-check-circle {{ $isCurrent ? 'text-emerald-300' : 'text-emerald-600' }} shrink-0"></i>
                                                @elseif($isCurrent)
                                                    <i class="fas fa-play-circle text-amber-300 shrink-0"></i>
                                                @elseif($isLiveItem)
                                                    <span class="w-2 h-2 rounded-full bg-[#c30102] shrink-0"></span>
                                                @else
                                                    <i class="far fa-circle text-slate-400 shrink-0"></i>
                                                @endif
                                                <span class="truncate">{{ $secLesson->title }}</span>
                                            </div>

                                            @if($isLiveItem)
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-black uppercase tracking-wider {{ $isCurrent ? 'bg-[#c30102] text-white' : 'bg-rose-50 text-[#c30102] border border-rose-200' }} shrink-0">
                                                    LIVE
                                                </span>
                                            @elseif($secLesson->video_url)
                                                <i class="fas fa-play-circle text-[11px] {{ $isCurrent ? 'text-slate-300' : 'text-slate-400' }} shrink-0"></i>
                                            @else
                                                <i class="fas fa-file-lines text-[11px] {{ $isCurrent ? 'text-slate-300' : 'text-slate-400' }} shrink-0"></i>
                                            @endif
                                        </a>
                                    @empty
                                        <div class="px-3 py-2 text-[11px] text-slate-400 italic">No lessons in this module yet.</div>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-6 text-center space-y-2">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                            <i class="fas fa-video"></i>
                        </div>
                        <div class="text-xs font-extrabold text-slate-700">Live Batch Schedule</div>
                        <p class="text-[11px] text-slate-500">Live class links and recorded modules will appear here as sessions begin.</p>
                    </div>
                @endif
            </div>
        </aside>
    </div>

</body>
</html>
