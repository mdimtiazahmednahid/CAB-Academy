@extends(auth()->check() && !auth()->user()->isAdmin() ? 'layouts.student' : 'layouts.public')

@section('title', 'Distinguished Faculty & Industry Fellows')
@section('meta_description', 'Meet CAB Academy\'s veteran faculty, C-suite executives, customs specialists, and international banking advisors delivering executive training programs in Bangladesh.')
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

    <!-- Faculty / Instructors Roster (Dynamic from Backend) -->
    <section class="py-16 bg-[#f8fafc]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
