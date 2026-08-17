<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $lesson->title }} - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --primary-color: {{ \App\Models\Setting::getVal('primary_color', '#1F6F54') }};
        }
        .bg-primary { background-color: var(--primary-color); }
        .text-primary { color: var(--primary-color); }
        .pb-safe { padding-bottom: max(env(safe-area-inset-bottom), 1.5rem); }
        .pt-safe { padding-top: max(env(safe-area-inset-top), 1rem); }
        
        /* Video Container for responsive iframe */
        .video-container {
            position: relative;
            padding-bottom: 56.25%; /* 16:9 */
            height: 0;
            overflow: hidden;
            width: 100%;
            background: #000;
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
<body class="font-sans antialiased bg-white text-gray-900 pb-28">

    <!-- Top Navigation -->
    <header class="flex items-center px-4 py-3 border-b border-gray-100 sticky top-0 bg-white/95 backdrop-blur-md z-40 pt-safe">
        <a href="{{ route('courses.show', $course) }}" class="p-2 -ml-2 text-gray-600 hover:text-gray-900">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div class="ml-2 flex-1 min-w-0">
            <h1 class="text-sm font-semibold text-gray-900 truncate">{{ $lesson->title }}</h1>
            <p class="text-[11px] text-gray-500 truncate">{{ $course->title }}</p>
        </div>
    </header>

    <!-- Video Player Area -->
    @if($lesson->video_url)
        <div class="w-full bg-black sticky top-[calc(env(safe-area-inset-top,0)+52px)] z-30 shadow-md">
            <div class="video-container">
                <iframe src="{{ $lesson->video_url }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
        </div>
    @endif

    <!-- Content Area -->
    <main class="p-5 max-w-3xl mx-auto">
        <h2 class="text-2xl font-bold mb-4">{{ $lesson->title }}</h2>
        
        @if($lesson->content)
            <div class="prose prose-green max-w-none text-gray-600">
                {!! nl2br(e($lesson->content)) !!}
            </div>
        @else
            @if(!$lesson->video_url)
                <div class="p-8 text-center text-gray-400 bg-gray-50 rounded-2xl">
                    No content available for this lesson.
                </div>
            @endif
        @endif
    </main>

    <!-- Bottom Action Bar (Thumb-friendly completion) -->
    <div class="fixed bottom-0 left-0 w-full bg-white border-t border-gray-100 p-4 pb-safe z-50 shadow-[0_-10px_30px_rgba(0,0,0,0.05)] flex justify-center">
        <form method="POST" action="{{ route('lessons.complete', [$course, $lesson]) }}" class="w-full max-w-md flex items-center gap-3">
            @csrf
            <input type="hidden" name="next_lesson_url" value="{{ $nextLesson ? route('lessons.show', [$course, $nextLesson]) : route('courses.show', $course) }}">
            
            @if($isCompleted)
                <button type="button" disabled class="flex-1 px-4 py-4 rounded-xl font-bold text-green-700 bg-green-50 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    Completed
                </button>
            @else
                <button type="submit" class="flex-1 px-4 py-4 rounded-xl font-bold text-white bg-primary hover:opacity-90 active:scale-[0.98] transition-all flex items-center justify-center gap-2 shadow-sm">
                    Mark as Complete
                </button>
            @endif

            @if($nextLesson)
                <a href="{{ route('lessons.show', [$course, $nextLesson]) }}" class="px-6 py-4 rounded-xl font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 active:scale-[0.98] transition-all whitespace-nowrap">
                    Next →
                </a>
            @else
                <a href="{{ route('courses.show', $course) }}" class="px-6 py-4 rounded-xl font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 active:scale-[0.98] transition-all whitespace-nowrap">
                    Finish
                </a>
            @endif
        </form>
    </div>

</body>
</html>
