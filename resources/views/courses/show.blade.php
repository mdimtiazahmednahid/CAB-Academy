@extends('layouts.student')

@section('header_title', 'Course Overview')

@section('content')
<div class="space-y-6">
    <!-- Course Header -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2 leading-tight">{{ $course->title }}</h1>
        <p class="text-gray-500 mb-6">{{ $course->description ?? 'No description available.' }}</p>
        
        @if($isEnrolled)
            <!-- Progress Bar -->
            @php
                $totalLessons = $course->sections->flatMap->lessons->count();
                $completedCount = count(array_intersect($completedLessonIds, $course->sections->flatMap->lessons->pluck('id')->toArray()));
                $progress = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;
            @endphp
            <div class="flex items-center justify-between text-sm mb-2 font-medium">
                <span class="text-gray-700">Course Progress</span>
                <span class="text-primary">{{ $progress }}%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2 mb-2">
                <div class="bg-primary h-2 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
            </div>
            <p class="text-xs text-gray-400">{{ $completedCount }} of {{ $totalLessons }} lessons completed</p>
        @else
            <form method="POST" action="{{ route('courses.enroll', $course) }}" class="mt-4">
                @csrf
                <button type="submit" class="w-full py-4 bg-primary text-white font-bold rounded-2xl shadow-[0_4px_15px_rgba(31,111,84,0.3)] hover:opacity-90 transition-opacity active:scale-[0.98] text-lg flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Enroll Now
                </button>
            </form>
        @endif
    </div>

    <!-- Sections & Lessons -->
    <div class="space-y-4 pb-8">
        @forelse($course->sections as $index => $section)
            <div class="bg-white rounded-2xl shadow-[0_2px_10px_rgba(0,0,0,0.02)] border border-gray-100 overflow-hidden" x-data="{ expanded: {{ $index === 0 ? 'true' : 'false' }} }">
                <!-- Section Header (Accordion Toggle) -->
                <button @click="expanded = !expanded" class="w-full px-6 py-5 flex items-center justify-between bg-gray-50/50 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center gap-3 text-left">
                        <div class="w-8 h-8 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-bold text-sm">
                            {{ $index + 1 }}
                        </div>
                        <h2 class="font-bold text-gray-900 text-lg">{{ $section->title }}</h2>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 transition-transform duration-300" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <!-- Lessons List -->
                <div x-show="expanded" x-collapse>
                    <div class="divide-y divide-gray-100 px-2 pb-2">
                        @forelse($section->lessons as $lesson)
                            @php
                                $isCompleted = in_array($lesson->id, $completedLessonIds);
                            @endphp
                            @if($isEnrolled)
                                <a href="{{ route('lessons.show', [$course, $lesson]) }}" class="flex items-center p-4 mx-2 my-1 rounded-xl hover:bg-gray-50 transition-colors group">
                            @else
                                <div class="flex items-center p-4 mx-2 my-1 rounded-xl opacity-60 bg-gray-50">
                            @endif
                                <!-- Status Icon -->
                                <div class="mr-4 flex-shrink-0">
                                    @if($isCompleted)
                                        <div class="w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                    @else
                                        <div class="w-6 h-6 rounded-full border-2 border-gray-300 group-hover:border-primary transition-colors flex items-center justify-center">
                                            <svg class="w-3 h-3 text-gray-300 group-hover:text-primary opacity-0 group-hover:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"></path></svg>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Title -->
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-[15px] font-semibold text-gray-900 truncate group-hover:text-primary transition-colors">{{ $lesson->title }}</h4>
                                    <p class="text-xs text-gray-500 mt-0.5 flex items-center gap-2">
                                        @if($lesson->video_url)
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                            Video
                                        @else
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            Reading
                                        @endif
                                    </p>
                                </div>
                                
                                @if(!$isEnrolled)
                                    <svg class="w-4 h-4 text-gray-400 ml-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                @endif
                            @if($isEnrolled)
                                </a>
                            @else
                                </div>
                            @endif
                        @empty
                            <div class="p-6 text-center text-sm text-gray-500">No lessons available in this section.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center p-8 bg-gray-50 rounded-2xl border border-gray-100">
                <p class="text-gray-500">This course doesn't have any content yet.</p>
            </div>
        @endforelse
    </div>

    <!-- Quizzes -->
    @if($course->quizzes->where('is_published', true)->count() > 0)
        <h2 class="text-xl font-bold text-gray-900 mt-2 mb-4 px-2">Assessments</h2>
        <div class="space-y-4 pb-8">
            @foreach($course->quizzes->where('is_published', true) as $quiz)
                <a href="{{ route('quizzes.show', [$course, $quiz]) }}" class="bg-white rounded-2xl shadow-[0_2px_10px_rgba(0,0,0,0.02)] border border-purple-100 overflow-hidden flex items-center p-5 hover:bg-purple-50 transition-colors group">
                    <div class="w-12 h-12 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center mr-4 flex-shrink-0 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-900 text-lg group-hover:text-purple-700 transition-colors">{{ $quiz->title }}</h3>
                        <p class="text-sm text-gray-500">{{ $quiz->questions->count() }} Questions</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 group-hover:text-purple-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            @endforeach
        </div>
    @endif

    <!-- Course Materials -->
    @if($course->materials->count() > 0)
        <h2 class="text-xl font-bold text-gray-900 mt-6 mb-4 px-2">Course Materials</h2>
        <div class="space-y-4 pb-8">
            @foreach($course->materials as $material)
                <div class="bg-white rounded-2xl shadow-[0_2px_10px_rgba(0,0,0,0.02)] border border-gray-100 overflow-hidden flex items-center p-4">
                    <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center mr-4 flex-shrink-0">
                        @if($material->type === 'pdf')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        @elseif($material->type === 'zip')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-900">{{ $material->title }}</h3>
                        <p class="text-xs text-gray-500 uppercase tracking-wider">{{ $material->type }}</p>
                    </div>
                    @if($isEnrolled)
                        @if($material->type === 'link')
                            <a href="{{ $material->external_link }}" target="_blank" class="px-4 py-2 bg-indigo-50 text-indigo-600 rounded-lg text-sm font-medium hover:bg-indigo-100 transition-colors">Open Link</a>
                        @else
                            <a href="{{ Storage::url($material->file_path) }}" target="_blank" class="px-4 py-2 bg-indigo-50 text-indigo-600 rounded-lg text-sm font-medium hover:bg-indigo-100 transition-colors">Download</a>
                        @endif
                    @else
                        <span class="text-xs text-gray-400 font-medium">Enroll to access</span>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
