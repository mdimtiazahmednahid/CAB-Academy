@extends(auth()->check() && auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.student')

@section('title', 'Profile Settings')
@section('header_title', 'Profile')

@section('content')
    @php
        $profileUser = auth()->user();
        $enrolledCount = $profileUser ? $profileUser->enrolledCourses()->count() : 0;
    @endphp

    <!-- Compact Profile Bar -->
    <div class="bg-[#051c45] rounded-2xl p-4 sm:p-5 text-white shadow-xs mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3.5">
            @if($profileUser && $profileUser->hasCustomProfilePicture())
                <img id="hero_profile_avatar_img" src="{{ $profileUser->profile_picture_url }}" alt="{{ $profileUser->name }}" class="w-12 h-12 rounded-xl object-cover border border-white/25 shrink-0" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($profileUser->name) }}&background=051c45&color=fff';">
            @else
                <div class="w-12 h-12 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center text-lg font-black text-white shrink-0 overflow-hidden">
                    <img id="hero_profile_avatar_img" src="" alt="" class="w-full h-full object-cover hidden">
                    <span id="hero_profile_avatar_initial">{{ strtoupper(substr($profileUser->name, 0, 1)) }}</span>
                </div>
            @endif
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-base sm:text-lg font-black text-white">{{ $profileUser->name }}</h1>
                    <span class="sr-only">Profile &amp; Account Security</span>
                    <span class="px-2 py-0.5 rounded-md bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-[10px] font-bold">
                        {{ $enrolledCount }} {{ \Illuminate\Support\Str::plural('Course', $enrolledCount) }}
                    </span>
                </div>
                <p class="text-xs text-slate-300 mt-0.5">
                    {{ $profileUser->phone ?: $profileUser->email }}
                </p>
            </div>
        </div>

        <a href="{{ $profileUser->isAdmin() ? route('admin.dashboard') : route('dashboard') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white text-[#051c45] text-xs font-extrabold hover:bg-slate-100 transition-all self-start sm:self-auto shrink-0">
            <i class="fas fa-arrow-left text-[10px] text-[#c30102]"></i>
            <span>Back to Dashboard</span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 pb-6">
        <div class="p-4 sm:p-6 bg-white rounded-2xl border border-slate-200/80 shadow-2xs">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="p-4 sm:p-6 bg-white rounded-2xl border border-slate-200/80 shadow-2xs">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="p-4 sm:p-6 bg-white rounded-2xl border border-slate-200/80 shadow-2xs">
            <div class="max-w-xl">
                @include('profile.partials.update-notification-preferences-form')
            </div>
        </div>

        <div class="p-4 sm:p-6 bg-white rounded-2xl border border-slate-200/80 shadow-2xs">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
@endsection
