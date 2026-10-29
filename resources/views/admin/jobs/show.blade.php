@extends('layouts.admin')

@section('header_title', 'Job Post Details')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-start sm:items-center gap-3">
        <a href="{{ route('admin.jobs') }}" class="p-2 rounded-xl bg-white border border-gray-200 text-gray-500 hover:text-primary hover:border-primary transition-all shrink-0 shadow-sm" title="Back to Jobs">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-lg sm:text-2xl font-black text-gray-900">{{ $job->title }}</h1>
                <span class="text-gray-500 text-sm font-semibold">at {{ $job->company }}</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100">
                    {{ $job->job_type ?? 'Full-time' }}
                </span>
                @if($job->is_active)
                    <span class="px-2.5 py-0.5 bg-green-100 text-green-800 rounded-full text-xs font-bold inline-flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span> Active
                    </span>
                @else
                    <span class="px-2.5 py-0.5 bg-gray-100 text-gray-700 rounded-full text-xs font-bold inline-flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Inactive
                    </span>
                @endif
            </div>
            <p class="text-xs text-gray-400 mt-0.5">Posted on {{ $job->created_at->format('M d, Y') }} &bull; {{ $job->applications->count() }} candidates applied</p>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="p-4 mb-6 text-sm text-green-800 rounded-xl bg-green-50 border border-green-200 flex items-center gap-2.5">
        <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span>{{ session('success') }}</span>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Candidates Table (2 cols on desktop) -->
    <div class="lg:col-span-2 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-base sm:text-lg font-bold text-gray-900 flex items-center gap-2">
                <span>Candidate Applications</span>
                <span class="text-xs bg-gray-100 text-gray-700 font-black px-2 py-0.5 rounded-full">{{ $job->applications->count() }}</span>
            </h2>
        </div>

        <div class="bg-white rounded-2xl sm:rounded-3xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm text-gray-600 min-w-[580px]">
                    <thead class="text-xs text-gray-700 uppercase tracking-wider bg-gray-50/80 border-b border-gray-100">
                        <tr>
                            <th class="p-3.5 sm:p-4 font-bold">Candidate</th>
                            <th class="p-3.5 sm:p-4 font-bold">Resume & Details</th>
                            <th class="p-3.5 sm:p-4 font-bold">Application Status</th>
                            <th class="p-3.5 sm:p-4 font-bold text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($job->applications as $app)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="p-3.5 sm:p-4">
                                <div class="font-bold text-gray-900">{{ $app->user?->name ?? 'Candidate' }}</div>
                                <div class="text-gray-500 text-xs">{{ $app->user?->email ?? 'No email' }}</div>
                                @if($app->user?->phone)
                                    <div class="text-gray-400 text-xs mt-0.5">{{ $app->user->phone }}</div>
                                @endif
                                <div class="text-[11px] text-gray-400 mt-1">Applied: {{ $app->created_at->format('M d, Y - h:i A') }}</div>
                            </td>
                            <td class="p-3.5 sm:p-4">
                                <a href="{{ storage_asset($app->resume_path) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold text-xs transition-colors border border-emerald-200/60">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <span>Download Resume</span>
                                </a>
                                @if($app->cover_letter)
                                    <div class="mt-2 text-xs text-gray-600 bg-gray-50 p-2.5 rounded-xl border border-gray-100 leading-relaxed max-w-xs" title="{{ $app->cover_letter }}">
                                        <span class="font-bold text-gray-700 block mb-0.5">Note:</span>
                                        "{{ Str::limit($app->cover_letter, 120) }}"
                                    </div>
                                @endif
                            </td>
                            <td class="p-3.5 sm:p-4">
                                <form method="POST" action="{{ route('admin.jobs.applications.status', $app) }}" class="inline-block">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()" class="text-xs font-bold rounded-lg border-gray-300 py-1.5 px-2.5 cursor-pointer transition-all
                                        {{ $app->status == 'accepted' ? 'bg-green-50 text-green-800 border-green-200' : '' }}
                                        {{ $app->status == 'rejected' ? 'bg-red-50 text-red-800 border-red-200' : '' }}
                                        {{ $app->status == 'reviewed' ? 'bg-blue-50 text-blue-800 border-blue-200' : '' }}
                                        {{ $app->status == 'pending' ? 'bg-amber-50 text-amber-800 border-amber-200' : '' }}
                                    ">
                                        <option value="pending" {{ $app->status == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                        <option value="reviewed" {{ $app->status == 'reviewed' ? 'selected' : '' }}>🔍 Reviewed</option>
                                        <option value="accepted" {{ $app->status == 'accepted' ? 'selected' : '' }}>✅ Shortlisted / Accepted</option>
                                        <option value="rejected" {{ $app->status == 'rejected' ? 'selected' : '' }}>❌ Rejected</option>
                                    </select>
                                </form>
                            </td>
                            <td class="p-3.5 sm:p-4 text-right whitespace-nowrap">
                                <form method="POST" action="{{ route('admin.jobs.applications.destroy', $app) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Remove this candidate application?')" class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors" title="Delete application">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-10 text-center text-gray-400">
                                <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-2 text-xl">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                </div>
                                <div class="font-bold text-gray-600">No applicants yet</div>
                                <div class="text-xs text-gray-400 mt-0.5">When candidates apply via CAB Academy portal, their CVs will appear here.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Job Info Sidebar -->
    <div class="space-y-4">
        <h2 class="text-base sm:text-lg font-bold text-gray-900">Job Overview</h2>
        <div class="bg-white rounded-2xl sm:rounded-3xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-gray-100 p-5 sm:p-6 space-y-4">
            @if($job->company_logo)
                <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                    <img src="{{ storage_asset($job->company_logo) }}" alt="{{ $job->company }}" class="w-12 h-12 rounded-xl object-cover border border-gray-200 shrink-0">
                    <div>
                        <div class="font-black text-gray-900 text-sm">{{ $job->company }}</div>
                        <div class="text-xs text-gray-500">{{ $job->location ?? 'Location unassigned' }}</div>
                    </div>
                </div>
            @endif

            <div>
                <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Role Type</span>
                <span class="text-xs sm:text-sm font-semibold text-gray-900">{{ $job->job_type ?? 'Full-time' }}</span>
            </div>

            <div>
                <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Location</span>
                <span class="text-xs sm:text-sm font-semibold text-gray-900">{{ $job->location ?? 'Not specified / Remote' }}</span>
            </div>

            <div>
                <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Salary Range</span>
                <span class="text-xs sm:text-sm font-semibold text-emerald-700">{{ $job->salary_range ?? 'Negotiable' }}</span>
            </div>

            <div>
                <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Application Route</span>
                @if($job->apply_link)
                    <a href="{{ $job->apply_link }}" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline break-all text-xs font-semibold inline-flex items-center gap-1 mt-0.5">
                        <span>{{ $job->apply_link }}</span>
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    </a>
                @else
                    <span class="text-xs text-gray-500 font-medium">Internal candidate resume submission</span>
                @endif
            </div>

            <div class="pt-2 border-t border-gray-100">
                <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Description</span>
                <p class="text-xs text-gray-600 leading-relaxed whitespace-pre-line">{{ $job->description }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
