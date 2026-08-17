@extends('layouts.admin')

@section('header_title', 'User Details')

@section('content')
<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.users') }}" class="text-gray-400 hover:text-primary transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
    </a>
    <h1 class="text-2xl font-bold">User: {{ $user->name }}</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Left Column: User Profile -->
    <div class="space-y-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 text-center">
            <div class="w-24 h-24 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-3xl mx-auto mb-4">
                {{ substr($user->name, 0, 1) }}
            </div>
            <h2 class="text-xl font-bold text-gray-900">{{ $user->name }}</h2>
            <p class="text-gray-500 mb-4">{{ $user->email }}</p>
            
            <div class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700' }} mb-6">
                {{ ucfirst($user->role) }}
            </div>
            
            <div class="flex justify-between items-center text-sm border-t border-gray-100 pt-4">
                <span class="text-gray-500">Joined</span>
                <span class="font-medium text-gray-900">{{ $user->created_at->format('M d, Y') }}</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-bold text-gray-900 mb-4">Quick Stats</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Lessons Completed</span>
                    <span class="font-bold text-gray-900">{{ $user->completedLessons->count() }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Quizzes Taken</span>
                    <span class="font-bold text-gray-900">{{ $totalQuizzes }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Average Score</span>
                    <span class="font-bold text-gray-900">{{ $averageScore }}%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Activity -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Quiz Attempts -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h3 class="font-bold text-lg text-gray-900">Recent Quiz Attempts</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($user->quizAttempts()->latest()->take(10)->get() as $attempt)
                    <div class="p-6 flex items-center justify-between hover:bg-gray-50 transition-colors">
                        <div>
                            <h4 class="font-bold text-gray-900">{{ $attempt->quiz->title }}</h4>
                            <p class="text-sm text-gray-500">{{ $attempt->quiz->course->title }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $attempt->created_at->diffForHumans() }}</p>
                        </div>
                        
                        @php
                            $percentage = $attempt->total_questions > 0 ? round(($attempt->score / $attempt->total_questions) * 100) : 0;
                            $color = $percentage >= 80 ? 'text-green-600 bg-green-50' : ($percentage >= 50 ? 'text-yellow-600 bg-yellow-50' : 'text-red-600 bg-red-50');
                        @endphp
                        
                        <div class="px-3 py-2 rounded-xl {{ $color }} flex flex-col items-center min-w-[60px]">
                            <span class="font-bold">{{ $percentage }}%</span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        This user hasn't taken any quizzes yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
