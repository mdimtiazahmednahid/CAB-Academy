@extends('layouts.admin')

@section('header_title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Platform Overview</h1>
            <p class="text-gray-500 text-sm">Key metrics and recent activity across your platform.</p>
        </div>
    </div>

    <!-- Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Students -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Students</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalStudents) }}</p>
            </div>
        </div>

        <!-- Published Courses -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-green-50 text-green-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Published Courses</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($publishedCourses) }}</p>
            </div>
        </div>

        <!-- Total Assessments -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Quizzes Taken</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalAssessments) }}</p>
            </div>
        </div>

        <!-- Average Score -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Avg Score</p>
                <p class="text-2xl font-bold text-gray-900">{{ $averageScore }}%</p>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="font-bold text-lg text-gray-900">Recent Quiz Activity</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-gray-500 text-sm uppercase tracking-wide">
                    <tr>
                        <th class="px-6 py-4 font-medium">Student</th>
                        <th class="px-6 py-4 font-medium">Quiz</th>
                        <th class="px-6 py-4 font-medium">Course</th>
                        <th class="px-6 py-4 font-medium text-right">Score</th>
                        <th class="px-6 py-4 font-medium text-right">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentActivity as $attempt)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $attempt->user->name ?? 'Unknown Student' }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $attempt->quiz->title }}
                            </td>
                            <td class="px-6 py-4 text-gray-600 text-sm">
                                {{ $attempt->quiz->course->title }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                @php
                                    $percentage = $attempt->total_questions > 0 ? round(($attempt->score / $attempt->total_questions) * 100) : 0;
                                    $color = $percentage >= 80 ? 'text-green-600 bg-green-50' : ($percentage >= 50 ? 'text-yellow-600 bg-yellow-50' : 'text-red-600 bg-red-50');
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $color }}">
                                    {{ $percentage }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm text-gray-400">
                                {{ $attempt->created_at->diffForHumans() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p>No recent quiz activity found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
