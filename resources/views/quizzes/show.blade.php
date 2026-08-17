@extends('layouts.student')

@section('header_title', 'Quiz')

@section('content')
<div class="mb-6">
    <a href="{{ route('courses.show', $course) }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-primary mb-4 transition-colors">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Back to Course
    </a>
    <h1 class="text-2xl font-bold text-gray-900">{{ $quiz->title }}</h1>
    @if($quiz->description)
        <p class="text-gray-500 text-sm mt-2">{{ $quiz->description }}</p>
    @endif
</div>

<div class="pb-12">
    <form method="POST" action="{{ route('quizzes.store', [$course, $quiz]) }}">
        @csrf
        <div class="space-y-6">
            @forelse($quiz->questions as $index => $question)
                <div class="bg-white rounded-3xl p-6 shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-gray-100">
                    <h3 class="font-bold text-gray-900 text-lg mb-4">{{ $index + 1 }}. {{ $question->question_text }}</h3>
                    
                    <div class="space-y-3">
                        @foreach($question->options as $option)
                            <label class="flex items-center p-4 rounded-xl border border-gray-200 cursor-pointer hover:border-primary hover:bg-green-50 transition-colors focus-within:ring-2 focus-within:ring-primary focus-within:border-primary">
                                <input type="radio" name="question_{{ $question->id }}" value="{{ $option->id }}" required class="h-5 w-5 text-primary border-gray-300 focus:ring-primary">
                                <span class="ml-3 text-gray-700 font-medium select-none">{{ $option->option_text }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-3xl p-12 text-center shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-1">No questions yet</h3>
                    <p class="text-gray-500 text-sm">This quiz doesn't have any questions configured.</p>
                </div>
            @endforelse
            
            @if($quiz->questions->count() > 0)
                <button type="submit" class="w-full py-4 bg-primary text-white font-bold rounded-2xl shadow-[0_4px_15px_rgba(31,111,84,0.3)] hover:opacity-90 transition-opacity active:scale-[0.98] text-lg">
                    Submit Quiz
                </button>
            @endif
        </div>
    </form>
</div>
@endsection
