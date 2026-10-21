@extends('layouts.admin')

@section('content')
<div class="p-6 md:p-8 space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Executive Inquiries & Leads</h1>
            <p class="text-sm text-gray-500 mt-1">Manage student admission queries, corporate training requests, and trade advisory submissions.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-orange-100 text-orange-800">
                {{ $pendingCount }} Pending Actions
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-wrap gap-3 items-center justify-between">
        <form method="GET" action="{{ route('admin.inquiries.index') }}" class="flex flex-wrap items-center gap-3">
            <select name="type" onchange="this.form.submit()" class="text-xs font-medium border-gray-200 rounded-xl bg-gray-50 focus:ring-1 focus:ring-indigo-500 py-2 px-3">
                <option value="">All Inquiry Types</option>
                <option value="course_admission" {{ request('type') === 'course_admission' ? 'selected' : '' }}>Course Admission</option>
                <option value="corporate" {{ request('type') === 'corporate' ? 'selected' : '' }}>Corporate Training</option>
                <option value="consulting" {{ request('type') === 'consulting' ? 'selected' : '' }}>Trade Advisory</option>
                <option value="general" {{ request('type') === 'general' ? 'selected' : '' }}>General Inquiry</option>
            </select>

            <select name="status" onchange="this.form.submit()" class="text-xs font-medium border-gray-200 rounded-xl bg-gray-50 focus:ring-1 focus:ring-indigo-500 py-2 px-3">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="contacted" {{ request('status') === 'contacted' ? 'selected' : '' }}>Contacted</option>
                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
            </select>
        </form>

        <span class="text-xs text-gray-500">Showing {{ $inquiries->total() }} total entries</span>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="text-xs uppercase bg-gray-50 text-gray-700 border-b border-gray-100 font-bold">
                    <tr>
                        <th class="px-6 py-4">Sender & Contact</th>
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4">Subject</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($inquiries as $inquiry)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $inquiry->name }}</div>
                                <div class="text-xs text-gray-500">{{ $inquiry->email }}</div>
                                <div class="text-xs font-mono text-gray-600">{{ $inquiry->phone }}</div>
                                @if($inquiry->organization)
                                    <span class="inline-block mt-1 text-[11px] px-2 py-0.5 bg-gray-100 text-gray-700 rounded-md font-medium">
                                        {{ $inquiry->organization }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($inquiry->inquiry_type === 'corporate')
                                    <span class="px-2.5 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-bold uppercase">Corporate</span>
                                @elseif($inquiry->inquiry_type === 'course_admission')
                                    <span class="px-2.5 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-bold uppercase">Admission</span>
                                @elseif($inquiry->inquiry_type === 'consulting')
                                    <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-bold uppercase">Advisory</span>
                                @else
                                    <span class="px-2.5 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-bold uppercase">General</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 max-w-xs truncate font-medium text-gray-800">
                                {{ $inquiry->subject }}
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500">
                                {{ $inquiry->created_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($inquiry->status === 'pending')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">Pending</span>
                                @elseif($inquiry->status === 'contacted')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">Contacted</span>
                                @elseif($inquiry->status === 'resolved')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Resolved</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-800">{{ ucfirst($inquiry->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('admin.inquiries.show', $inquiry) }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-bold transition-colors">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                No contact inquiries found matching the selected criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($inquiries->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $inquiries->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
