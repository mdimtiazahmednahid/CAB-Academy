<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <title>{{ $lesson->title }} - {{ \App\Models\Setting::getVal('site_name', 'ACS Academy') }}</title>
    <link rel="icon" href="{{ \App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --primary-color: {{ \App\Models\Setting::getVal('primary_color', '#051c45') }};
        }
        {!! \App\Models\Setting::getTypographyCss() !!}
        .bg-primary { background-color: var(--primary-color); }
        .text-primary { color: var(--primary-color); }
        .pb-safe { padding-bottom: max(env(safe-area-inset-bottom), 1.5rem); }
        .pt-safe { padding-top: max(env(safe-area-inset-top), 0.75rem); }
        
        /* Video Container for responsive iframe */
        .video-container {
            position: relative;
            padding-bottom: 56.25%; /* 16:9 */
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
    </style>
</head>
@php
    $completedIds = $completedLessonIds ?? [];
    $allLessons = $course->sections->flatMap->lessons;
    $totalLessons = $allLessons->count();
    $doneLessons = count(array_intersect($completedIds, $allLessons->pluck('id')->toArray()));
    $coursePct = $totalLessons > 0 ? round(($doneLessons / $totalLessons) * 100) : 0;

    // Auto-normalize YouTube / Vimeo URLs into embeddable iframe URLs
    $embedUrl = $lesson->video_url;
    if ($embedUrl) {
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $embedUrl, $matches)) {
            $embedUrl = 'https://www.youtube.com/embed/' . $matches[1];
        } elseif (preg_match('/vimeo\.com\/(\d+)/', $embedUrl, $matches)) {
            $embedUrl = 'https://player.vimeo.com/video/' . $matches[1];
        }
    }
@endphp
<body class="font-sans antialiased bg-[#f8fafc] text-gray-900 pb-28">

    <!-- Top Classroom Navigation -->
    <header class="flex items-center justify-between px-4 sm:px-6 py-3 border-b border-white/10 sticky top-0 bg-[#051c45] text-white z-40 pt-safe shadow-md">
        <div class="flex items-center min-w-0 gap-2 sm:gap-3">
            <a href="{{ route('courses.show', $course) }}" class="p-2 rounded-xl bg-white/10 hover:bg-white/20 text-white transition-colors shrink-0" title="Back to Course Overview">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div class="min-w-0">
                <h1 class="text-sm sm:text-base font-bold text-white truncate">{{ $lesson->title }}</h1>
                <p class="text-[11px] text-slate-300 truncate">{{ $course->title }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2 sm:gap-3 shrink-0">
            <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/10 border border-white/15 text-xs font-bold">
                <span class="text-emerald-400">{{ $coursePct }}%</span>
                <span class="text-slate-300">({{ $doneLessons }}/{{ $totalLessons }})</span>
            </div>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#c30102] hover:bg-[#a50102] text-white text-xs font-bold transition-colors shadow-xs">
                <i class="fas fa-th-large text-[10px]"></i>
                <span>My Dashboard</span>
            </a>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left Column: Video & Lesson Content (8 cols) -->
        <div class="lg:col-span-8 space-y-6">
            @if($embedUrl)
                <div class="w-full bg-[#020b18] rounded-2xl overflow-hidden shadow-xl border border-slate-200">
                    <div class="video-container">
                        <iframe src="{{ $embedUrl }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </div>
            @endif

            <main class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
                <div class="flex flex-wrap items-center justify-between gap-2 pb-4 mb-5 border-b border-slate-100">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-[#c30102] block mb-1">
                            {{ $lesson->section->title ?? 'Course Lesson' }}
                        </span>
                        <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">{{ $lesson->title }}</h2>
                    </div>
                    @if($isCompleted)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold">
                            <i class="fas fa-check-circle"></i> Completed
                        </span>
                    @endif
                </div>
                
                @if($lesson->content)
                    <div class="prose prose-slate max-w-none text-slate-700 leading-relaxed text-sm sm:text-base">
                        {!! nl2br(e($lesson->content)) !!}
                    </div>
                @else
                    @if(!$embedUrl)
                        <div class="p-8 text-center text-gray-400 bg-gray-50 rounded-2xl">
                            No content available for this lesson.
                        </div>
                    @endif
                @endif
            </main>
        </div>

        <!-- Right Column: Course Curriculum, Materials & Quizzes Sidebar (4 cols) -->
        <aside class="lg:col-span-4 space-y-5">
            <!-- Course Modules & Lessons List -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-4 sm:p-5 bg-[#051c45] text-white flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-sm">Course Curriculum</h3>
                        <p class="text-[11px] text-slate-300 mt-0.5">{{ $doneLessons }} of {{ $totalLessons }} lessons completed</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg bg-white/10 text-xs font-extrabold text-emerald-300">{{ $coursePct }}%</span>
                </div>

                <div class="divide-y divide-slate-100 max-h-[420px] overflow-y-auto">
                    @foreach($course->sections as $secIdx => $sec)
                        <div class="p-3.5 bg-slate-50/70">
                            <div class="text-xs font-extrabold text-[#051c45] uppercase tracking-wider mb-2 px-1">
                                Module {{ $secIdx + 1 }}: {{ $sec->title }}
                            </div>
                            <div class="space-y-1">
                                @foreach($sec->lessons as $secLesson)
                                    @php
                                        $secLessonDone = in_array($secLesson->id, $completedIds);
                                        $isCurrent = (int) $secLesson->id === (int) $lesson->id;
                                    @endphp
                                    <a href="{{ route('lessons.show', [$course, $secLesson]) }}" class="flex items-center justify-between gap-2 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ $isCurrent ? 'bg-[#051c45] text-white shadow-xs' : 'text-slate-700 hover:bg-white hover:shadow-2xs' }}">
                                        <div class="flex items-center gap-2 min-w-0">
                                            @if($secLessonDone)
                                                <i class="fas fa-check-circle {{ $isCurrent ? 'text-emerald-300' : 'text-emerald-600' }} shrink-0"></i>
                                            @elseif($isCurrent)
                                                <i class="fas fa-play-circle text-amber-300 shrink-0"></i>
                                            @else
                                                <i class="far fa-circle text-slate-400 shrink-0"></i>
                                            @endif
                                            <span class="truncate">{{ $secLesson->title }}</span>
                                        </div>
                                        @if($secLesson->video_url)
                                            <i class="fas fa-video text-[10px] {{ $isCurrent ? 'text-slate-300' : 'text-slate-400' }} shrink-0"></i>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Course Materials & Quizzes Quick Access -->
            @if(($course->materials && $course->materials->count() > 0) || ($course->quizzes && $course->quizzes->count() > 0))
                <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs space-y-4">
                    @if($course->materials && $course->materials->count() > 0)
                        <div>
                            <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-2.5">Downloadable Resources</h4>
                            <div class="space-y-2">
                                @foreach($course->materials as $mat)
                                    <a href="{{ $mat->type === 'link' ? $mat->external_link : storage_asset($mat->file_path) }}" target="_blank" class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200/70 text-xs font-semibold text-[#051c45] transition-colors">
                                        <span class="truncate pr-2"><i class="fas fa-file-download text-[#c30102] mr-1.5"></i>{{ $mat->title }}</span>
                                        <span class="text-[10px] font-bold text-[#c30102] shrink-0">Open &rarr;</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($course->quizzes && $course->quizzes->count() > 0)
                        <div>
                            <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-2.5">Course Quizzes</h4>
                            <div class="space-y-2">
                                @foreach($course->quizzes as $q)
                                    <a href="{{ route('quizzes.show', [$course, $q]) }}" class="flex items-center justify-between p-2.5 rounded-xl bg-emerald-50/70 hover:bg-emerald-100/70 border border-emerald-200 text-xs font-bold text-emerald-900 transition-colors">
                                        <span class="truncate pr-2"><i class="fas fa-clipboard-check text-emerald-600 mr-1.5"></i>{{ $q->title }}</span>
                                        <span class="px-2 py-0.5 rounded bg-emerald-600 text-white text-[10px] shrink-0">Start Quiz</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </aside>
    </div>

    <!-- Bottom Action Bar (Thumb-friendly completion) -->
    <div class="fixed bottom-0 left-0 w-full bg-white/95 backdrop-blur-md border-t border-gray-200 p-4 pb-safe z-50 shadow-[0_-10px_30px_rgba(0,0,0,0.05)] flex justify-center">
        <form method="POST" action="{{ route('lessons.complete', [$course, $lesson]) }}" class="w-full max-w-md flex items-center gap-3">
            @csrf
            <input type="hidden" name="next_lesson_url" value="{{ $nextLesson ? route('lessons.show', [$course, $nextLesson]) : route('courses.show', $course) }}">
            
            @if($isCompleted)
                <button type="button" disabled class="flex-1 px-4 py-3.5 rounded-xl font-bold text-green-700 bg-green-50 border border-green-200 flex items-center justify-center gap-2 text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    Completed
                </button>
            @else
                <button type="submit" class="flex-1 px-4 py-3.5 rounded-xl font-bold text-white bg-primary hover:opacity-90 active:scale-[0.98] transition-all flex items-center justify-center gap-2 shadow-sm text-sm cursor-pointer">
                    Mark as Complete
                </button>
            @endif

            @if($nextLesson)
                <a href="{{ route('lessons.show', [$course, $nextLesson]) }}" class="px-6 py-3.5 rounded-xl font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 active:scale-[0.98] transition-all whitespace-nowrap text-sm">
                    Next →
                </a>
            @else
                <a href="{{ route('courses.show', $course) }}" class="px-6 py-3.5 rounded-xl font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 active:scale-[0.98] transition-all whitespace-nowrap text-sm">
                    Finish
                </a>
            @endif
        </form>
    </div>

</body>
</html>
