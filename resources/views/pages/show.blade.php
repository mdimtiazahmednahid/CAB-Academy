@extends('layouts.public')

@section('title', $page['title'] ?? 'Executive Page')
@section('meta_description', $page['meta_description'] ?? ($page['subtitle'] ?? 'Official institutional page at ' . \App\Models\Setting::getVal('site_name', 'ACS Academy')))

@section('content')
<div>
    @if(!empty($isPreview))
        <div class="bg-amber-500 text-slate-950 px-4 py-2.5 text-center text-xs font-black uppercase tracking-wider flex items-center justify-center gap-2">
            <i class="fas fa-eye-slash"></i>
            <span>Hidden Draft Preview Mode — Only Administrators can see this page right now.</span>
            <a href="{{ route('admin.frontend.index') }}" class="underline ml-2">Manage in CMS →</a>
        </div>
    @endif

    <!-- Minimalist Executive Masthead -->
    <section class="bg-gradient-to-b from-[#020b1c] via-[#051c45] to-[#041536] text-white py-14 sm:py-20 border-b border-white/10 relative overflow-hidden">
        <div class="absolute -top-32 -right-32 w-96 h-96 bg-[#c30102]/12 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            @if(!empty($page['badge']))
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-white/10 border border-white/15 text-xs font-bold text-emerald-300 mb-4">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    <span>{{ $page['badge'] }}</span>
                </div>
            @endif
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-white leading-tight">
                {{ $page['title'] }}
            </h1>
            @if(!empty($page['subtitle']))
                <p class="text-sm sm:text-base lg:text-lg text-slate-300 mt-3 max-w-2xl mx-auto leading-relaxed">
                    {{ $page['subtitle'] }}
                </p>
            @endif
        </div>
    </section>

    <!-- Page Body Content -->
    <section class="py-12 sm:py-16 bg-[#f8fafc]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-10 lg:p-12 prose prose-slate max-w-none text-slate-700 leading-relaxed space-y-4">
                {!! $page['content'] ?? '' !!}

                @if(!empty($page['cta_text']) && !empty($page['cta_url']))
                    <div class="pt-6 mt-8 border-t border-slate-100 flex justify-center sm:justify-start not-prose">
                        <a href="{{ $page['cta_url'] }}" class="btn-ipdp-primary px-7 py-3.5 rounded-xl text-white text-xs sm:text-sm font-bold inline-flex items-center gap-2">
                            <span>{{ $page['cta_text'] }}</span>
                            <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection

