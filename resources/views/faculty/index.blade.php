@extends(auth()->check() && !auth()->user()->isAdmin() ? 'layouts.student' : 'layouts.public')

@section('title', 'Our Faculty & Learning Departments')
@section('meta_description', 'Meet ACS Academy\'s faculty heads and industry practitioners across our 4 Learning Departments in Bangladesh.')
@section('canonical_url', route('instructors.index'))

@section('content')
<div>

    <!-- Compact Hero Banner -->
    <section class="bg-gradient-to-r from-[#031330] via-[#051c45] to-[#0a2540] text-white py-9 sm:py-12 border-b border-[#1e2d4a] relative overflow-hidden">
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white mb-2">
                Our Faculty
            </h1>
            <div class="flex items-center justify-center gap-2 text-xs text-slate-300 font-medium">
                <a href="{{ url('/') }}" class="hover:text-white transition-colors">Home</a>
                <span class="text-slate-500">/</span>
                <span class="font-bold text-white underline">Faculty</span>
            </div>
        </div>
    </section>

    <!-- 1. ACS 4 Independent Learning Departments (Department Head + Dedicated Team) -->
    @php
        $deptsList = isset($departments) && $departments->isNotEmpty()
            ? $departments
            : \App\Models\Category::active()->ordered()->with('headInstructor')->get();
    @endphp
    @if($deptsList->isNotEmpty())
    <section class="py-8 sm:py-12 bg-white border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-5 sm:mb-6 pb-3 border-b border-slate-200/80">
                <h2 class="text-xl sm:text-2xl font-extrabold text-[#0f131f] tracking-tight">
                    4 Learning Departments
                </h2>
                <a href="{{ route('catalog.index') }}" class="text-xs sm:text-sm font-bold text-[#c30102] hover:text-[#051c45] inline-flex items-center gap-1.5">
                    <span>View Courses</span>
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                @foreach($deptsList as $dIdx => $dept)
                    @php
                        $head = $dept->resolved_head;
                        $team = $dept->resolved_team;
                        $subs = $dept->effective_subcategories;
                    @endphp
                    <div class="rounded-2xl bg-[#f8fafc] border border-slate-200/90 p-4 sm:p-5 flex flex-col justify-between shadow-2xs hover:shadow-md transition-all">
                        <div>
                            <div class="flex items-center gap-2.5 mb-3">
                                <span class="w-8 h-8 rounded-xl bg-[#051c45] text-white flex items-center justify-center text-xs shrink-0">
                                    <i class="fas {{ $dept->icon ?: 'fa-layer-group' }}"></i>
                                </span>
                                <div class="min-w-0">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-none mb-0.5">Dept 0{{ $dIdx + 1 }}</span>
                                    <h3 class="text-sm sm:text-base font-extrabold text-[#041536] leading-tight truncate">
                                        {{ $dept->name }}
                                    </h3>
                                </div>
                            </div>

                            <!-- Compact Department Head Card -->
                            <div class="p-2.5 rounded-xl bg-white border border-slate-200/90 mb-3 flex items-center justify-between gap-2">
                                @if($head)
                                    <div class="flex items-center gap-2 min-w-0">
                                        <img src="{{ $head->avatar_url }}" alt="{{ $head->name }}" class="w-8 h-8 rounded-full object-cover object-top border border-slate-200 shrink-0">
                                        <div class="min-w-0">
                                            <span class="text-[9px] font-extrabold uppercase tracking-wider text-[#c30102] block leading-none">Head</span>
                                            <p class="text-xs font-bold text-[#0f131f] truncate mt-0.5">{{ $head->name }}</p>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-xs font-semibold text-slate-500">Department Head</p>
                                @endif
                                <span class="text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md text-[10px] font-bold shrink-0">
                                    + Team
                                </span>
                            </div>

                            <!-- Sub-Tracks -->
                            <ul class="space-y-1 mb-4">
                                @foreach($subs as $sName)
                                    <li class="text-xs text-slate-600 flex items-center gap-1.5">
                                        <i class="fas fa-check text-[9px] text-[#10b981] shrink-0"></i>
                                        <span class="truncate">{{ $sName }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <a href="{{ route('catalog.category', $dept->slug) }}" class="w-full py-2 px-3 rounded-xl bg-[#051c45] hover:bg-[#c30102] text-white text-xs font-bold text-center transition-colors">
                            View Courses →
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- 2. Faculty Directory -->
    <section class="py-8 sm:py-12 bg-[#f8fafc]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-5 sm:mb-6 pb-3 border-b border-slate-200/80">
                <h2 class="text-xl sm:text-2xl font-extrabold text-[#0f131f] tracking-tight">
                    Faculty Directory
                </h2>
            </div>
            @php
                $roster = isset($instructors) && $instructors->isNotEmpty() 
                    ? $instructors 
                    : \App\Models\Instructor::where('is_active', true)->orderBy('order', 'asc')->get();
            @endphp

            @if($roster->isEmpty())
                <div class="bg-white rounded-2xl border border-slate-200 p-10 text-center max-w-md mx-auto shadow-2xs">
                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-xl">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h3 class="text-base font-bold text-[#041536] mb-1">No Faculty Listed</h3>
                    <p class="text-xs text-slate-500">Faculty profiles will appear here once published.</p>
                </div>
            @else
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                    @foreach($roster as $f)
                        @php
                            $avatarUrl = $f->avatar_url;
                        @endphp
                        <div class="bg-white rounded-2xl border border-[#e3e7f0] p-4 sm:p-5 text-center shadow-2xs hover:shadow-md transition-all duration-200 group flex flex-col justify-between hover:border-[#051c45]/30">
                            <div>
                                <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto rounded-full overflow-hidden mb-3 border-2 border-slate-100 shadow-xs bg-gray-100">
                                    <img src="{{ $avatarUrl }}" alt="{{ $f->name }}" class="w-full h-full object-cover object-top" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($f->name) }}&background=051c45&color=fff'">
                                </div>

                                @if(!empty($f->department))
                                    <div class="mb-2 flex items-center justify-center gap-1 flex-wrap">
                                        <span class="text-[9px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded {{ $f->department_role === 'head' ? 'bg-[#c30102] text-white' : 'bg-[#051c45]/10 text-[#051c45]' }}">
                                            {{ $f->department_role === 'head' ? 'Head' : 'Team' }}
                                        </span>
                                        <span class="text-[10px] font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded truncate max-w-[140px]">
                                            {{ $f->department }}
                                        </span>
                                    </div>
                                @endif

                                <h3 class="text-xs sm:text-sm font-extrabold text-[#0f131f] group-hover:text-[#c30102] transition-colors leading-snug mb-0.5 line-clamp-1">
                                    {{ $f->name }}
                                </h3>
                                <span class="text-[11px] text-[#c30102] font-semibold block mb-2 line-clamp-1">
                                    {{ $f->title }}
                                </span>
                                @if(!empty($f->expertise))
                                    <span class="inline-block bg-emerald-50 text-emerald-800 border border-emerald-200/70 text-[10px] font-semibold px-2.5 py-0.5 rounded-full mb-3 line-clamp-1">
                                        {{ $f->expertise }}
                                    </span>
                                @endif
                            </div>

                            <a href="{{ route('catalog.index') }}" class="btn-ipdp-primary text-center py-2 rounded-xl text-xs font-bold w-full">
                                View Courses →
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

</div>
@endsection
