@extends('layouts.admin')

@section('header_title', 'Announcements & Course Notifications')

@section('content')
<div class="mb-6 md:mb-8">
    <h2 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight">Announcements & Course Notifications</h2>
    <p class="text-gray-500 mt-1 text-sm md:text-base">Broadcast instant notifications to all users, specific roles, or enrolled students of a specific course.</p>
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 text-emerald-800 rounded-xl border border-emerald-200 text-sm font-semibold flex items-center gap-2.5">
        <i class="fas fa-check-circle text-emerald-600"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if($errors->any())
    <div class="mb-6 p-4 bg-red-50 text-red-800 rounded-xl border border-red-200 text-sm font-medium">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.announcements.store') }}" method="POST" x-data="{ audience: '{{ old('audience', 'all') }}' }" class="space-y-6 max-w-2xl bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100">
    @csrf

    <div>
        <label for="title" class="block text-sm font-bold text-gray-800 mb-1">Announcement Title *</label>
        <input type="text" id="title" name="title" value="{{ old('title') }}" required placeholder="e.g. Live Zoom Link & Batch Schedule Update" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-primary focus:border-primary transition-shadow text-sm">
    </div>

    <div>
        <label for="message" class="block text-sm font-bold text-gray-800 mb-1">Message Body *</label>
        <textarea id="message" name="message" rows="4" required placeholder="Write your announcement or class update here..." class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-primary focus:border-primary transition-shadow text-sm">{{ old('message') }}</textarea>
    </div>

    <div>
        <label for="audience" class="block text-sm font-bold text-gray-800 mb-2">Target Audience *</label>
        <select id="audience" name="audience" x-model="audience" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-primary focus:border-primary transition-shadow text-sm font-semibold text-slate-800">
            <optgroup label="Platform-Wide Broadcast">
                <option value="all">All Users (Students, Instructors & Staff)</option>
                <option value="students">All Students Only</option>
                <option value="instructors">All Instructors Only</option>
                <option value="announcement_opt_in">Users Who Opted In for Announcements</option>
                <option value="course">Specific Course (Select Course Below)</option>
            </optgroup>
            @if(isset($courses) && $courses->isNotEmpty())
                <optgroup label="Direct Target by Course Name (Enrolled Students Only)">
                    @foreach($courses as $c)
                        <option value="course:{{ $c->id }}">
                            🎓 {{ $c->title }} {{ $c->batch ? '(' . $c->batch . ')' : '' }} — {{ $c->students_count ?? 0 }} Enrolled Student(s)
                        </option>
                    @endforeach
                </optgroup>
            @endif
        </select>
        <p class="text-xs text-gray-500 mt-1.5">
            Select a course above (or choose <strong>Specific Course</strong>) to notify only the students enrolled in that course.
        </p>
    </div>

    <!-- Dedicated Course Selector when "Specific Course" is selected -->
    <div x-show="audience === 'course'" x-cloak class="p-4 bg-blue-50/60 border border-blue-200 rounded-xl space-y-2">
        <label for="course_id" class="block text-xs font-black uppercase tracking-wider text-[#051c45]">
            <i class="fas fa-graduation-cap text-[#c30102] mr-1"></i> Select Target Course *
        </label>
        <select id="course_id" name="course_id" :required="audience === 'course'" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-white text-sm font-semibold text-slate-800 focus:ring-[#051c45]">
            <option value="">-- Choose a Course --</option>
            @foreach($courses ?? [] as $c)
                <option value="{{ $c->id }}" {{ old('course_id') == $c->id ? 'selected' : '' }}>
                    {{ $c->title }} {{ $c->batch ? '[' . $c->batch . ']' : '' }} ({{ $c->students_count ?? 0 }} Enrolled)
                </option>
            @endforeach
        </select>
        <p class="text-[11px] text-slate-600">
            Only students enrolled in this course will receive this notification in their Student Portal bell & notifications inbox.
        </p>
    </div>

    <div>
        <label for="url" class="block text-sm font-bold text-gray-800 mb-1">Action URL (Optional)</label>
        <input type="url" id="url" name="url" value="{{ old('url') }}" placeholder="https://example.com (Defaults to Course Page if a course is selected)" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-primary focus:border-primary transition-shadow text-sm">
        <p class="text-xs text-gray-500 mt-1">If provided, clicking the notification will redirect the student here.</p>
    </div>

    <div class="flex items-center gap-2.5 p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
        <input type="checkbox" id="post_to_feed" name="post_to_feed" value="1" checked class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary">
        <label for="post_to_feed" class="text-xs font-bold text-gray-800 cursor-pointer">Also Pin & Broadcast to Community Feed (/feed)</label>
    </div>

    <button type="submit" class="w-full bg-[#051c45] hover:bg-[#072559] text-white font-bold py-3.5 px-4 rounded-xl transition-colors flex items-center justify-center gap-2 shadow-md">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
        Send Targeted Notification
    </button>
</form>
@endsection
