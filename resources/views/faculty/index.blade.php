@extends(auth()->check() && !auth()->user()->isAdmin() ? 'layouts.student' : 'layouts.public')

@section('title', 'Distinguished Faculty & Industry Fellows')
@section('meta_description', 'Meet ACS Academy\'s veteran faculty, C-suite executives, customs specialists, and international banking advisors delivering executive training programs in Bangladesh.')
@section('canonical_url', route('instructors.index'))

@section('content')
<div>

    <!-- Hero Banner (Modern Imperial Navy Masthead) -->
    <section class="bg-gradient-to-r from-[#031330] via-[#051c45] to-[#0a2540] text-white py-14 sm:py-18 border-b border-[#1e2d4a] relative overflow-hidden">
        <!-- Ambient Glow -->
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-[#c30102]/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-[#10b981]/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white mb-3">
                Distinguished Faculty
            </h1>
            <p class="text-xs sm:text-base text-slate-300 max-w-2xl mx-auto leading-relaxed">
                Instruction and corporate mentorship led by high-profile trade finance specialists, corporate legal counsels, and senior supply chain directors.
            </p>
            <div class="flex items-center justify-center gap-2 text-xs sm:text-sm text-slate-300 font-medium mt-5">
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
    <section class="py-14 bg-white border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center sm:text-left flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div>
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-extrabold uppercase tracking-widest text-[#c30102] mb-1.5">
                        <i class="fas fa-sitemap"></i> ACS Learning Platform Model
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-[#0f131f] tracking-tight">
                        4 Independent Learning Departments
                    </h2>
                </div>
                <p class="text-xs text-[#727f9f] max-w-md">
                    Each core training category is operated as an independent Learning Department led by a Department Head and a dedicated team of industry specialists.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($deptsList as $dIdx => $dept)
                    @php
                        $head = $dept->resolved_head;
                        $team = $dept->resolved_team;
                        $subs = $dept->effective_subcategories;
                    @endphp
                    <div class="rounded-2xl bg-[#f8fafc] border border-slate-200/90 p-5 flex flex-col justify-between shadow-xs hover:shadow-lg transition-all">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-md bg-[#051c45] text-white">
                                    Dept 0{{ $dIdx + 1 }}
                                </span>
                                <span class="w-9 h-9 rounded-xl bg-[#c30102]/10 text-[#c30102] flex items-center justify-center text-sm">
                                    <i class="fas {{ $dept->icon ?: 'fa-layer-group' }}"></i>
                                </span>
                            </div>

                            <h3 class="text-base font-extrabold text-[#041536] mb-3 leading-snug">
                                {{ $dept->name }}
                            </h3>

                            <!-- Department Head Card -->
                            <div class="p-3 rounded-xl bg-white border border-slate-200/90 mb-3 shadow-2xs">
                                <span class="text-[9px] font-extrabold uppercase tracking-wider text-[#c30102] block mb-1">
                                    Department Head
                                </span>
                                @if($head)
                                    <div class="flex items-center gap-2.5">
                                        <img src="{{ $head->avatar_url }}" alt="{{ $head->name }}" class="w-10 h-10 rounded-full object-cover object-top border border-slate-200 shrink-0">
                                        <div class="min-w-0">
                                            <p class="text-xs font-extrabold text-[#0f131f] truncate">{{ $head->name }}</p>
                                            <p class="text-[10px] text-slate-500 truncate">{{ $head->title }}</p>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-xs font-semibold text-slate-500">Assigned Department Head</p>
                                @endif
                            </div>

                            <!-- Dedicated Team Count & Sub-Tracks -->
                            <div class="mb-4">
                                <div class="flex items-center justify-between text-[11px] font-bold text-slate-600 mb-2">
                                    <span>Core Training Tracks (6)</span>
                                    <span class="text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full text-[10px]">
                                        <i class="fas fa-users mr-1"></i>{{ max(1, $team->count()) }} Faculty Team
                                    </span>
                                </div>
                                <ul class="space-y-1">
                                    @foreach($subs as $sName)
                                        <li class="text-[11px] text-slate-600 flex items-center gap-1.5">
                                            <i class="fas fa-check text-[9px] text-[#10b981] shrink-0"></i>
                                            <span class="truncate">{{ $sName }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <a href="{{ route('catalog.category', $dept->slug) }}" class="w-full py-2 px-3 rounded-xl bg-[#051c45] hover:bg-[#c30102] text-white text-xs font-bold text-center transition-colors">
                            View Department Courses
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- 2. Faculty / Instructors Roster (Dynamic from Backend) -->
    <section class="py-16 bg-[#f8fafc]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-[#0f131f] tracking-tight">
                    Department Heads & Faculty Team Directory
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Executive instructors and practitioners across all 4 ACS Learning Departments.
                </p>
            </div>
            @php
                $roster = isset($instructors) && $instructors->isNotEmpty() 
                    ? $instructors 
                    : \App\Models\Instructor::where('is_active', true)->orderBy('order', 'asc')->get();
            @endphp

            @if($roster->isEmpty())
                <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center max-w-lg mx-auto shadow-sm">
                    <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4 text-2xl">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h3 class="text-lg font-bold text-[#041536] mb-1">No Instructors Published</h3>
                    <p class="text-xs text-slate-500">Instructor profiles are currently being updated by the academic council.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    @foreach($roster as $f)
                        @php
                            $avatarUrl = $f->avatar_url;
                        @endphp
                        <div class="liquid-glass-card bg-white/85 rounded-3xl border border-[#e3e7f0] p-6 text-center shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group flex flex-col justify-between hover:border-[#c30102]/30">
                            <div>
                                @if(!empty($f->department))
                                    <div class="mb-3 flex items-center justify-center gap-1.5 flex-wrap">
                                        <span class="text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-0.5 rounded-full {{ $f->department_role === 'head' ? 'bg-[#c30102] text-white' : 'bg-[#051c45]/10 text-[#051c45]' }}">
                                            {{ $f->department_role === 'head' ? 'Department Head' : 'Department Team' }}
                                        </span>
                                        <span class="text-[10px] font-bold text-slate-600 bg-slate-100 px-2.5 py-0.5 rounded-full">
                                            {{ $f->department }}
                                        </span>
                                    </div>
                                @endif
                                <div class="w-28 h-28 mx-auto rounded-full overflow-hidden mb-5 border-4 border-slate-100 shadow-md bg-gray-100 group-hover:scale-105 group-hover:ring-4 group-hover:ring-[#c30102]/20 transition-all duration-300">
                                    <img src="{{ $avatarUrl }}" alt="{{ $f->name }}" class="w-full h-full object-cover object-top" onerror="this.src='https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80'">
                                </div>

                                <h3 class="text-base font-extrabold text-[#0f131f] group-hover:text-[#c30102] transition-colors leading-tight mb-1.5">
                                    {{ $f->name }}
                                </h3>
                                <span class="text-xs text-[#c30102] font-bold block mb-3">
                                    {{ $f->title }}
                                </span>
                                <span class="inline-block bg-emerald-50 text-emerald-800 border border-emerald-200 text-[11px] font-bold px-3 py-1 rounded-full mb-4">
                                    {{ $f->expertise }}
                                </span>
                                <p class="text-xs text-[#727f9f] leading-relaxed line-clamp-3 mb-5">
                                    {{ $f->bio }}
                                </p>
                            </div>

                            <a href="{{ route('catalog.index') }}" class="btn-ipdp-primary text-center py-2.5 rounded-xl text-xs font-bold w-full shadow-sm">
                                Explore Sessions
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

</div>
@endsection
