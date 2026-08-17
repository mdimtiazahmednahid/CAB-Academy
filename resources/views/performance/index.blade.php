@extends('layouts.student')

@section('header_title', 'Performance')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 mb-2">Your Performance</h1>
    <p class="text-gray-500 text-sm">Track your quiz scores and overall progress.</p>
</div>

@if(session('success'))
    <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200 shadow-sm">
        {{ session('success') }}
    </div>
@endif

<!-- Stats Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-3xl p-5 shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-gray-100 flex flex-col items-center justify-center">
        <div class="w-12 h-12 rounded-full bg-green-100 text-primary flex items-center justify-center mb-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
        </div>
        <span class="text-3xl font-black text-gray-900">{{ $averageScore }}%</span>
        <span class="text-xs font-bold text-gray-400 tracking-wider uppercase mt-1 text-center">Avg Score</span>
    </div>

    <div class="bg-white rounded-3xl p-5 shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-gray-100 flex flex-col items-center justify-center">
        <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mb-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
        </div>
        <span class="text-3xl font-black text-gray-900">{{ $totalQuizzes }}</span>
        <span class="text-xs font-bold text-gray-400 tracking-wider uppercase mt-1 text-center">Completed</span>
    </div>

    <div class="bg-white rounded-3xl p-5 shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-gray-100 flex flex-col items-center justify-center">
        <div class="w-12 h-12 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center mb-3 text-2xl">
            🔥
        </div>
        <span class="text-3xl font-black text-gray-900">{{ auth()->user()->current_streak }}</span>
        <span class="text-xs font-bold text-gray-400 tracking-wider uppercase mt-1 text-center">Day Streak</span>
    </div>

    <div class="bg-white rounded-3xl p-5 shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-gray-100 flex flex-col items-center justify-center">
        <div class="w-12 h-12 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center mb-3 text-2xl">
            ✨
        </div>
        <span class="text-3xl font-black text-gray-900">{{ auth()->user()->xp }}</span>
        <span class="text-xs font-bold text-gray-400 tracking-wider uppercase mt-1 text-center">Total XP</span>
    </div>
</div>

<!-- Recent History -->
<h2 class="text-xl font-bold text-gray-900 mb-4">Recent Quizzes</h2>
<div class="space-y-4 pb-8">
    @forelse($attempts as $attempt)
        <div class="bg-white rounded-3xl p-5 shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-gray-900 mb-1 line-clamp-1">{{ $attempt->quiz->title }}</h3>
                <p class="text-xs text-gray-500">{{ $attempt->quiz->course->title }}</p>
                <p class="text-[10px] text-gray-400 mt-2">{{ $attempt->created_at->diffForHumans() }}</p>
            </div>
            
            @php
                $percentage = $attempt->total_questions > 0 ? round(($attempt->score / $attempt->total_questions) * 100) : 0;
                $color = $percentage >= 80 ? 'text-green-500 bg-green-50 border-green-100' : ($percentage >= 50 ? 'text-yellow-600 bg-yellow-50 border-yellow-100' : 'text-red-500 bg-red-50 border-red-100');
            @endphp
            
            <div class="flex flex-col items-center justify-center p-3 rounded-2xl border {{ $color }} min-w-[70px]">
                <span class="text-lg font-black">{{ $percentage }}%</span>
                <span class="text-[10px] font-medium opacity-80">{{ $attempt->score }}/{{ $attempt->total_questions }}</span>
            </div>
        </div>
    @empty
        <div class="bg-gray-50 rounded-3xl p-12 text-center border border-gray-100">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            <h3 class="text-xl font-bold text-gray-900 mb-2">No Quizzes Yet</h3>
            <p class="text-gray-500 max-w-sm mx-auto text-sm">You haven't completed any quizzes. Check out the courses to test your knowledge!</p>
        </div>
    @endforelse
</div>
@endsection
