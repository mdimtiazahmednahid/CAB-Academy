@extends('layouts.admin')

@section('content')
<div class="mb-6 md:mb-8">
    <h2 class="text-2xl md:text-3xl font-bold">Announcements</h2>
    <p class="text-gray-500 mt-1 text-sm md:text-base">Send notifications directly to users.</p>
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-green-50 text-green-800 rounded-lg border border-green-200 text-sm font-medium">
        {{ session('success') }}
    </div>
@endif

<form action="{{ route('admin.announcements.store') }}" method="POST" class="space-y-6 max-w-2xl bg-white p-6 rounded-xl shadow-sm border border-gray-100">
    @csrf

    <div>
        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Announcement Title</label>
        <input type="text" id="title" name="title" required placeholder="e.g. Server Maintenance" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-primary focus:border-primary transition-shadow">
    </div>

    <div>
        <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message Body</label>
        <textarea id="message" name="message" rows="4" required placeholder="Write your message here..." class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-primary focus:border-primary transition-shadow"></textarea>
    </div>

    <div>
        <label for="url" class="block text-sm font-medium text-gray-700 mb-1">Action URL (Optional)</label>
        <input type="url" id="url" name="url" placeholder="https://example.com" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-primary focus:border-primary transition-shadow">
        <p class="text-xs text-gray-500 mt-1">If provided, clicking the notification will redirect the user here.</p>
    </div>

    <div>
        <label for="audience" class="block text-sm font-medium text-gray-700 mb-2">Target Audience</label>
        <select id="audience" name="audience" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-primary focus:border-primary transition-shadow">
            <option value="all">All Users</option>
            <option value="students">Students Only</option>
            <option value="instructors">Instructors Only</option>
            <option value="announcement_opt_in">Users Who Opted In for Announcements</option>
        </select>
    </div>

    <button type="submit" class="w-full bg-primary hover:bg-green-700 text-white font-bold py-3 px-4 rounded-xl transition-colors flex items-center justify-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
        Send Announcement
    </button>
</form>
@endsection
