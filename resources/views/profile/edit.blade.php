@extends(auth()->check() && auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.student')

@section('title', 'Profile & Security Settings')
@section('header_title', 'Profile & Security')

@section('content')
    @php
        $profileUser = auth()->user();
        $enrolledCount = $profileUser ? $profileUser->enrolledCourses()->count() : 0;
    @endphp

    <!-- Upgraded Student Profile Hero Card with Direct Portal Navigation -->
    <div class="bg-gradient-to-br from-[#031029] via-[#051c45] to-[#0b2d6b] rounded-3xl p-5 sm:p-7 text-white shadow-xl border border-white/10 mb-6 relative overflow-hidden">
        <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full bg-[#c30102]/20 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            <div class="flex items-center gap-4">
                @if($profileUser && $profileUser->hasCustomProfilePicture())
                    <img id="hero_profile_avatar_img" src="{{ $profileUser->profile_picture_url }}" alt="{{ $profileUser->name }}" class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl object-cover border-2 border-white/30 shadow-md shrink-0" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($profileUser->name) }}&background=051c45&color=fff';">
                @else
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center text-2xl font-black text-white shadow-inner shrink-0 overflow-hidden">
                        <img id="hero_profile_avatar_img" src="" alt="" class="w-full h-full object-cover hidden">
                        <span id="hero_profile_avatar_initial">{{ strtoupper(substr($profileUser->name, 0, 1)) }}</span>
                    </div>
                @endif
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight">{{ __('Profile & Account Security') }}</h1>
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-[10px] font-bold uppercase tracking-wider">
                            {{ $enrolledCount }} {{ \Illuminate\Support\Str::plural('Course', $enrolledCount) }} Enrolled
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1">
                        {{ $profileUser->name }} &bull; {{ $profileUser->phone ?: $profileUser->email }}
                    </p>
                </div>
            </div>

            <!-- Seamless Student Portal Journey Links -->
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ $profileUser->isAdmin() ? route('admin.dashboard') : route('dashboard') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-white text-[#051c45] text-xs font-extrabold hover:bg-slate-100 shadow-sm transition-all">
                    <i class="fas fa-arrow-left text-[10px] text-[#c30102]"></i>
                    <span>Back to Dashboard</span>
                </a>
                @if(!$profileUser->isAdmin())
                    <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-xs font-bold text-white transition-all">
                        <i class="fas fa-graduation-cap text-amber-300 text-xs"></i>
                        <span>Browse Courses</span>
                    </a>
                    <a href="{{ route('jobs.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-xs font-bold text-white transition-all">
                        <i class="fas fa-briefcase text-emerald-300 text-xs"></i>
                        <span>Career Portal</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pb-8">
        <div class="liquid-glass-card p-5 sm:p-7 bg-white rounded-3xl border border-slate-200/80">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="liquid-glass-card p-5 sm:p-7 bg-white rounded-3xl border border-slate-200/80">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="liquid-glass-card p-5 sm:p-7 bg-white rounded-3xl border border-slate-200/80">
            <div class="max-w-xl">
                @include('profile.partials.update-notification-preferences-form')
            </div>
        </div>

        <div class="liquid-glass-card p-5 sm:p-7 bg-white rounded-3xl border border-slate-200/80">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
@endsection
