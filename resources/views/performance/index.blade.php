@extends('layouts.student')

@section('header_title', 'Performance')

@section('content')
@if(session('success'))
    <div class="p-3.5 mb-5 text-xs text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-200 font-semibold">
        {{ session('success') }}
    </div>
@endif

<!-- Compact 4-Metric Bar -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 flex items-center gap-3 shadow-2xs">
        <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
            <i class="fas fa-chart-pie text-sm"></i>
        </div>
        <div>
            <div class="text-lg font-black text-slate-900 leading-none">{{ $averageScore }}%</div>
            <div class="text-[11px] font-bold text-slate-500 mt-1">Avg Score</div>
        </div>
    </div>

    <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 flex items-center gap-3 shadow-2xs">
        <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
            <i class="fas fa-clipboard-check text-sm"></i>
        </div>
        <div>
            <div class="text-lg font-black text-slate-900 leading-none">{{ $totalQuizzes }}</div>
            <div class="text-[11px] font-bold text-slate-500 mt-1">Quizzes Done</div>
        </div>
    </div>

    <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 flex items-center gap-3 shadow-2xs">
        <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 text-base">
            🔥
        </div>
        <div>
            <div class="text-lg font-black text-slate-900 leading-none">{{ auth()->user()->current_streak }}</div>
            <div class="text-[11px] font-bold text-slate-500 mt-1">Day Streak</div>
        </div>
    </div>

    <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 flex items-center gap-3 shadow-2xs">
        <div class="w-9 h-9 rounded-lg bg-[#051c45]/10 text-[#051c45] flex items-center justify-center shrink-0 text-base">
            ✨
        </div>
        <div>
            <div class="text-lg font-black text-slate-900 leading-none">{{ auth()->user()->xp }}</div>
            <div class="text-[11px] font-bold text-slate-500 mt-1">Total XP</div>
        </div>
    </div>
</div>

<!-- Recent Quizzes -->
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
        <h2 class="text-sm font-extrabold text-[#051c45]">Recent Quizzes</h2>
        <span class="text-[11px] font-bold text-slate-400">{{ $attempts->count() }} Attempts</span>
    </div>

    <div class="divide-y divide-slate-100">
        @forelse($attempts as $attempt)
            @php
                $percentage = $attempt->total_questions > 0 ? round(($attempt->score / $attempt->total_questions) * 100) : 0;
                $color = $percentage >= 80 ? 'text-emerald-700 bg-emerald-50 border-emerald-200' : ($percentage >= 50 ? 'text-amber-700 bg-amber-50 border-amber-200' : 'text-red-700 bg-red-50 border-red-200');
            @endphp
            <div class="p-3.5 flex items-center justify-between gap-3 hover:bg-slate-50/60 transition-colors">
                <div class="min-w-0">
                    <h3 class="font-bold text-xs text-slate-900 line-clamp-1">{{ $attempt->quiz->title }}</h3>
                    <p class="text-[11px] text-slate-500 truncate mt-0.5">
                        {{ $attempt->quiz->course->title }} &bull; {{ $attempt->created_at->diffForHumans() }}
                    </p>
                </div>
                <div class="flex items-center gap-2 px-2.5 py-1 rounded-xl border {{ $color }} shrink-0">
                    <span class="text-xs font-black">{{ $percentage }}%</span>
                    <span class="text-[10px] font-semibold opacity-75">({{ $attempt->score }}/{{ $attempt->total_questions }})</span>
                </div>
            </div>
        @empty
            <div class="p-8 text-center">
                <h3 class="text-sm font-extrabold text-slate-800">No Quizzes Yet</h3>
                <p class="text-xs text-slate-500 mt-1">Complete course quizzes to see your scores here.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
