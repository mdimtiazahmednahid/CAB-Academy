@extends('layouts.admin')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.jobs') }}" class="text-gray-400 hover:text-primary transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h1 class="text-2xl font-bold">{{ $job->title }} <span class="text-gray-500 font-normal text-lg">at {{ $job->company }}</span></h1>
        @if($job->is_active)
            <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-medium">Active</span>
        @else
            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-medium">Inactive</span>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="col-span-2">
        <!-- Applicants List -->
        <h2 class="text-xl font-bold mb-4">Applicants <span class="text-gray-500 text-sm font-normal">({{ $job->applications->count() }})</span></h2>
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left text-sm text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Candidate</th>
                        <th class="px-6 py-4 font-semibold">Resume / Cover Letter</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($job->applications as $app)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">{{ $app->user->name }}</div>
                            <div class="text-gray-500 text-xs">{{ $app->user->email }}</div>
                            <div class="text-gray-400 text-xs mt-1">Applied: {{ $app->created_at->format('M d, Y') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ Storage::url($app->resume_path) }}" target="_blank" class="text-primary hover:underline font-medium inline-flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                Resume
                            </a>
                            @if($app->cover_letter)
                                <div class="mt-2 text-xs text-gray-600 bg-gray-100 p-2 rounded line-clamp-2" title="{{ $app->cover_letter }}">
                                    "{{ $app->cover_letter }}"
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <!-- In a full implementation, you'd have a route to update this status. For now we just display it. -->
                            @if($app->status == 'pending')
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Pending</span>
                            @elseif($app->status == 'reviewed')
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Reviewed</span>
                            @elseif($app->status == 'accepted')
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Accepted</span>
                            @elseif($app->status == 'rejected')
                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Rejected</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                            No candidates have applied yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div>
        <!-- Job Info -->
        <h2 class="text-xl font-bold mb-4">Job Info</h2>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
            <div>
                <span class="block text-xs font-bold text-gray-400 uppercase">Location</span>
                <span class="text-gray-900">{{ $job->location ?? 'Not specified' }}</span>
            </div>
            <div>
                <span class="block text-xs font-bold text-gray-400 uppercase">Salary Range</span>
                <span class="text-gray-900">{{ $job->salary_range ?? 'Not specified' }}</span>
            </div>
            <div>
                <span class="block text-xs font-bold text-gray-400 uppercase">External Link</span>
                @if($job->apply_link)
                    <a href="{{ $job->apply_link }}" target="_blank" class="text-primary hover:underline break-all">{{ $job->apply_link }}</a>
                @else
                    <span class="text-gray-500">Internal application only</span>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
