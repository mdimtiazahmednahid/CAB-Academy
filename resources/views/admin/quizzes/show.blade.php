@extends('layouts.admin')

@section('content')
<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.courses.show', $course) }}" class="text-gray-400 hover:text-primary transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
    </a>
    <h1 class="text-2xl font-bold">Quiz: {{ $quiz->title }}</h1>
</div>

@if(session('success'))
    <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="p-4 mb-6 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-6">
        @forelse($quiz->questions as $index => $question)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden p-6">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="font-bold text-gray-900 text-lg">{{ $index + 1 }}. {{ $question->question_text }}</h3>
                    <form method="POST" action="{{ route('admin.quizzes.questions.destroy', [$course, $quiz, $question]) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete this question?')" class="text-sm font-medium text-red-500 hover:text-red-700">Delete</button>
                    </form>
                </div>
                <div class="space-y-2">
                    @foreach($question->options as $option)
                        <div class="px-4 py-3 rounded-lg border {{ $option->is_correct ? 'border-green-500 bg-green-50 text-green-800' : 'border-gray-200 bg-gray-50 text-gray-700' }} flex justify-between items-center">
                            <span>{{ $option->option_text }}</span>
                            @if($option->is_correct)
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
                <h3 class="text-lg font-bold text-gray-900 mb-1">No questions yet</h3>
                <p class="text-gray-500 text-sm">Use the form on the right to add questions to this quiz.</p>
            </div>
        @endforelse
    </div>

    <div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sticky top-6">
            <h3 class="text-lg font-bold mb-4">Add Question</h3>
            <form method="POST" action="{{ route('admin.quizzes.questions.store', [$course, $quiz]) }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Question Text</label>
                        <textarea name="question_text" rows="3" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"></textarea>
                    </div>
                    
                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-gray-700">Options</label>
                        
                        <!-- Option 1 -->
                        <div class="flex items-center gap-2">
                            <input type="radio" name="correct_option" value="0" required class="text-primary focus:ring-primary h-4 w-4">
                            <input type="text" name="options[]" placeholder="Option 1" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                        </div>
                        
                        <!-- Option 2 -->
                        <div class="flex items-center gap-2">
                            <input type="radio" name="correct_option" value="1" class="text-primary focus:ring-primary h-4 w-4">
                            <input type="text" name="options[]" placeholder="Option 2" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                        </div>
                        
                        <!-- Option 3 -->
                        <div class="flex items-center gap-2">
                            <input type="radio" name="correct_option" value="2" class="text-primary focus:ring-primary h-4 w-4">
                            <input type="text" name="options[]" placeholder="Option 3 (Optional)" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                        </div>
                        
                        <!-- Option 4 -->
                        <div class="flex items-center gap-2">
                            <input type="radio" name="correct_option" value="3" class="text-primary focus:ring-primary h-4 w-4">
                            <input type="text" name="options[]" placeholder="Option 4 (Optional)" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                        </div>
                    </div>
                    
                    <p class="text-xs text-gray-500">Select the radio button next to the correct answer. Empty optional options will be ignored.</p>
                    
                    <button type="submit" class="w-full px-4 py-2 bg-primary text-white rounded-lg font-medium hover:opacity-90">Save Question</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
