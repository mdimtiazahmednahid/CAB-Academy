@extends('layouts.public')

@section('title', 'Executive Training & Professional Certifications')
@section('meta_description', 'ACS Academy is Bangladesh\'s premier executive certification institute for Commercial Banking, Letter of Credit (LC), Customs Clearance, and Supply Chain Management.')
@section('canonical_url', url('/'))

@section('content')
<div>

@if(\App\Models\Setting::getVal('landing_page_mode', 'default') === 'custom' && !empty(\App\Models\Setting::getVal('landing_custom_html')))
    {!! \App\Models\Setting::getVal('landing_custom_html') !!}
@else

{{-- 1. Executive Hero Section (Modern Minimalist Apple/Stripe Architecture) --}}
    @if(\App\Models\Setting::getVal('landing_hero_enabled', '1') == '1')
    <section class="relative min-h-[auto] sm:min-h-[540px] lg:min-h-[600px] flex items-center bg-[#030f26] bg-gradient-to-b from-[#020b1c] via-[#051c45] to-[#041536] text-white py-14 sm:py-20 lg:py-24 overflow-hidden border-b border-white/10">
        <!-- Subtle Architectural Ambient Glow -->
        <div class="absolute -top-40 right-0 w-[36rem] h-[36rem] bg-[#c30102]/12 rounded-full blur-[140px] pointer-events-none"></div>
        <div class="absolute -bottom-40 left-0 w-[36rem] h-[36rem] bg-[#10b981]/10 rounded-full blur-[140px] pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-center">
                
                <!-- Left Content: Minimalist Executive Branding -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">

                    <!-- Minimalist Trust Pill -->
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/[0.06] border border-white/15 text-xs font-medium text-slate-200">
                        <span class="w-2 h-2 rounded-full bg-[#10b981]"></span>
                        <span>{{ \App\Models\Setting::getVal('landing_hero_alumni_text', '10,000+ Corporate Alumni') }}</span>
                    </div>

                    @php
                        $heroTitleText = \App\Models\Setting::getVal('landing_hero_title', \App\Models\Setting::getVal('site_name', 'ACS Academy'));
                        $heroTitleSizing = \App\Models\Setting::getBrandSizing($heroTitleText);
                    @endphp
                    <div class="space-y-3">
                        <h1 class="font-black tracking-tight text-white leading-tight" style="{{ $heroTitleSizing['hero_style'] }}">
                            {!! $heroTitleSizing['formatted_html'] ?? e($heroTitleText) !!}
                        </h1>
                        <h3 class="text-xl sm:text-2xl lg:text-3xl font-extrabold tracking-tight text-slate-200 leading-snug">
                            {!! \App\Models\Setting::formatBrandNameHtml(\App\Models\Setting::getVal('landing_hero_tagline', 'Learn, Grow, Lead.')) !!}
                        </h3>
                    </div>
                    
                    <p class="text-sm sm:text-base lg:text-lg text-slate-300/90 font-normal max-w-xl mx-auto lg:mx-0 leading-relaxed">
                        {{ \App\Models\Setting::getVal('landing_hero_subtitle', 'Online Professional Certification Masterclasses & Corporate Programs.') }}
                    </p>

                    <!-- Call-to-Action Buttons -->
                    <div class="pt-1 flex flex-wrap items-center justify-center lg:justify-start gap-3 sm:gap-4">
                        <a href="{{ \App\Models\Setting::getVal('landing_hero_cta1_url', '#home-category') }}" class="btn-ipdp-primary px-7 sm:px-8 py-3.5 sm:py-4 rounded-xl text-white text-xs sm:text-sm font-bold tracking-wide flex items-center justify-center gap-2.5 transition-all flex-1 sm:flex-initial text-center group">
                            <span>{{ \App\Models\Setting::getVal('landing_hero_cta1_text', \App\Models\Setting::getVal('landing_cta_text', 'Explore Courses')) }}</span>
                            <i class="fas fa-arrow-down text-xs group-hover:translate-y-0.5 transition-transform"></i>
                        </a>

                        <a href="{{ \App\Models\Setting::getVal('landing_hero_cta2_url', route('catalog.category', 'export-import')) }}" class="px-7 sm:px-8 py-3.5 sm:py-4 rounded-xl bg-white/[0.07] hover:bg-white/[0.14] text-white border border-white/15 text-xs sm:text-sm font-semibold tracking-wide flex items-center justify-center gap-2 transition-all flex-1 sm:flex-initial text-center">
                            <span>{{ \App\Models\Setting::getVal('landing_hero_cta2_text', 'Export – Import') }}</span>
                            <i class="fas fa-chevron-right text-[10px] opacity-80"></i>
                        </a>
                    </div>
                </div>

                <!-- Right Video / Visual Showcase (Minimalist Architectural Frame) -->
                <div class="lg:col-span-5">
                    <div class="relative p-2 rounded-2xl bg-white/[0.06] border border-white/15 shadow-2xl">
                        <div class="relative rounded-xl overflow-hidden aspect-video sm:aspect-[4/3] flex items-center justify-center bg-slate-900 group">
                            @php
                                $heroMediaImg = \App\Models\Setting::getVal('landing_hero_media_image', 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=1000&q=80');
                                $heroMediaLink = \App\Models\Setting::getVal('landing_hero_media_link', route('catalog.index'));
                            @endphp
                            <img 
                                src="{{ $heroMediaImg }}" 
                                alt="{{ \App\Models\Setting::getVal('landing_hero_title', 'ACS Academy') }} Executive Training" 
                                class="absolute inset-0 w-full h-full object-cover opacity-80 group-hover:scale-105 transition-transform duration-700"
                                onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=1000&q=80';"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-[#020b18]/80 via-black/25 to-transparent"></div>
                            
                            <!-- Minimalist Play Trigger -->
                            <a 
                                href="{{ $heroMediaLink }}" 
                                class="relative z-10 w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-[#c30102] hover:bg-[#a50102] text-white flex items-center justify-center shadow-xl hover:scale-105 transition-all duration-300 border border-white/20"
                                aria-label="Explore Courses"
                            >
                                <i class="fas fa-play text-base sm:text-lg ml-0.5 text-white"></i>
                            </a>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    @endif

{{-- 2. "Our Valuable Clients" (Minimalist Enterprise Sector Grid) --}}
    @if(\App\Models\Setting::getVal('landing_clients_enabled', '1') == '1')
    <section class="py-10 sm:py-14 bg-white border-b border-slate-200/70">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-6 sm:mb-8">
                <h2 class="text-lg sm:text-xl font-bold text-[#051c45] tracking-tight">
                    {{ \App\Models\Setting::getVal('landing_clients_title', 'Industry Sectors & Enterprise Training') }}
                </h2>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-3 sm:gap-4 items-stretch">
                @php
                    $rawClients = \App\Models\Setting::getVal('landing_clients_data');
                    if (!empty(trim($rawClients))) {
                        $clientLines = array_filter(array_map('trim', explode("\n", $rawClients)));
                        $clients = [];
                        foreach ($clientLines as $line) {
                            $parts = array_map('trim', explode('|', $line));
                            $cName = $parts[0] ?? '';
                            $cLogo = $parts[1] ?? '';
                            if ($cName !== '') {
                                $clients[] = [
                                    'name' => $cName,
                                    'logo' => $cLogo ?: null,
                                ];
                            }
                        }
                    } else {
                        $clients = [
                            ['name' => 'Import – Export', 'logo' => null],
                            ['name' => 'Supply Chain', 'logo' => null],
                            ['name' => 'Trade Finance', 'logo' => null],
                            ['name' => 'Customs & Port', 'logo' => null],
                            ['name' => 'VAT & Tax', 'logo' => null],
                            ['name' => 'Corporate Law', 'logo' => null],
                            ['name' => 'Human Resource', 'logo' => null],
                            ['name' => 'Project Mgmt', 'logo' => null],
                        ];
                    }
                @endphp

                @foreach($clients as $client)
                    <div class="p-3 sm:p-4 bg-white hover:bg-white rounded-xl sm:rounded-2xl border border-slate-200/80 hover:border-slate-300 shadow-[0_2px_8px_rgba(4,21,54,0.04)] hover:shadow-[0_12px_24px_-6px_rgba(4,21,54,0.1)] hover:border-[#041536]/20 transition-all duration-300 group flex items-center justify-center min-h-[4.25rem] sm:min-h-[4.75rem] h-auto">
                        @if(!empty($client['logo']))
                            <img 
                                src="{{ storage_asset($client['logo']) }}" 
                                alt="{{ $client['name'] }}" 
                                class="max-h-8 sm:max-h-12 max-w-full object-contain filter grayscale group-hover:grayscale-0 transition-all duration-300 group-hover:scale-105"
                                loading="lazy"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';"
                            >
                            <span style="display:none;" class="text-xs sm:text-sm font-bold text-slate-700 tracking-tight text-center leading-snug break-words">{{ $client['name'] }}</span>
                        @else
                            <span class="text-xs sm:text-sm font-bold text-slate-700 tracking-tight text-center leading-snug break-words">{{ $client['name'] }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- 3 & 4. Course Showcase & Sticky Smart Category Navigation --}}
    <div id="courses-section-wrapper" class="relative">
        
        {{-- 3. Sticky Smart Category Navigation Bar (Compact, Icon-Driven, Sticky across Course Section) --}}
        @if(\App\Models\Setting::getVal('landing_categories_enabled', '1') == '1')
        <section id="home-category" class="sticky top-16 sm:top-20 z-40 bg-white/95 backdrop-blur-2xl border-y border-slate-200/75 shadow-[0_10px_28px_-6px_rgba(4,21,54,0.08)] py-2.5 transition-all" x-data="{
            scrollLeft() { $refs.catSlider.scrollBy({ left: -260, behavior: 'smooth' }) },
            scrollRight() { $refs.catSlider.scrollBy({ left: 260, behavior: 'smooth' }) }
        }">
            <!-- Ultra-fine luxury Crimson accent hairline -->
            <div class="absolute inset-x-0 top-0 h-[2px] bg-gradient-to-r from-transparent via-[#c30102]/45 to-transparent pointer-events-none"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between gap-3">
                    <!-- Compact Category Section Title & Visual Cue (Hidden on mobile for maximum pill space) -->
                    <div class="hidden sm:flex items-center gap-2 shrink-0 pr-3.5 border-r border-slate-200/90">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#c30102] opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-[#c30102]"></span>
                        </span>
                        <h2 class="text-[11px] font-black uppercase tracking-wider text-[#041536] whitespace-nowrap">
                            {{ \App\Models\Setting::getVal('landing_categories_title', 'Course Categories') }}
                        </h2>
                    </div>

                    @php
                        $dbCategories = \App\Models\Category::active()->ordered()->get();
                        if ($dbCategories->isNotEmpty()) {
                            $runningCategories = $dbCategories->map(function ($cat) {
                                return [
                                    'name' => $cat->name,
                                    'slug' => $cat->slug,
                                    'icon' => $cat->icon ?: 'fa-tag',
                                ];
                            })->toArray();
                        } else {
                            $rawCats = \App\Models\Setting::getVal('landing_categories_data');
                            if (!empty(trim($rawCats))) {
                                $catLines = array_filter(array_map('trim', explode("\n", $rawCats)));
                                $runningCategories = [];
                                foreach ($catLines as $line) {
                                    $parts = array_map('trim', explode('|', $line));
                                    $name = $parts[0] ?? '';
                                    $slug = $parts[1] ?? \Illuminate\Support\Str::slug($name);
                                    $icon = $parts[2] ?? 'fa-tag';
                                    if ($name !== '') {
                                        $runningCategories[] = [
                                            'name' => $name,
                                            'slug' => $slug,
                                            'icon' => str_starts_with($icon, 'fa-') ? $icon : ('fa-' . $icon),
                                        ];
                                    }
                                }
                            } else {
                                $runningCategories = [
                                    ['name' => 'Soft Skills', 'slug' => 'soft-skills', 'icon' => 'fa-comments'],
                                    ['name' => 'Supply Chain Management', 'slug' => 'supply-chain-management', 'icon' => 'fa-truck-fast'],
                                    ['name' => 'Value Added Tax', 'slug' => 'value-added-tax', 'icon' => 'fa-file-invoice-dollar'],
                                    ['name' => 'Export – Import', 'slug' => 'export-import', 'icon' => 'fa-ship'],
                                    ['name' => 'Accounting', 'slug' => 'accounting', 'icon' => 'fa-calculator'],
                                    ['name' => 'Human Resource', 'slug' => 'human-resource', 'icon' => 'fa-people-group'],
                                    ['name' => 'Civil Construction Project', 'slug' => 'civil-construction-project', 'icon' => 'fa-helmet-safety'],
                                    ['name' => 'Fire Safety', 'slug' => 'fire-safety', 'icon' => 'fa-fire-extinguisher'],
                                    ['name' => 'Law & Compliance', 'slug' => 'law', 'icon' => 'fa-scale-balanced'],
                                    ['name' => 'Leadership', 'slug' => 'leadership', 'icon' => 'fa-award'],
                                    ['name' => 'Production & Ops', 'slug' => 'production', 'icon' => 'fa-industry'],
                                    ['name' => 'Project Management', 'slug' => 'project-management', 'icon' => 'fa-diagram-project'],
                                ];
                            }
                        }
                        $allCatUrl = \App\Models\Setting::getVal('landing_categories_all_url', route('catalog.index'));
                        $allCatText = \App\Models\Setting::getVal('landing_categories_all_text', 'All');
                    @endphp

                    <!-- Horizontal Smart Category Navigation Pills with Semantic Icons -->
                    <div 
                        x-ref="catSlider" 
                        class="flex items-center gap-2 overflow-x-auto no-scrollbar scroll-smooth py-1 px-1 w-full"
                    >
                        @foreach($runningCategories as $cat)
                            <a 
                                href="{{ route('catalog.category', $cat['slug']) }}" 
                                class="liquid-glass-pill category-nav-pill shrink-0 inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/90 hover:bg-gradient-to-r hover:from-[#041536] hover:via-[#072152] hover:to-[#041536] text-[#0f172a] hover:text-white border border-slate-200/90 hover:border-[#0a2e6b] text-xs font-bold transition-all duration-300 group shadow-[0_2px_8px_-2px_rgba(4,21,54,0.06)] hover:shadow-[0_8px_20px_-4px_rgba(4,21,54,0.28)] hover:scale-105 active:scale-95 cursor-pointer"
                            >
                                <span class="w-6 h-6 rounded-full bg-slate-100 group-hover:bg-[#c30102] text-[#c30102] group-hover:text-white flex items-center justify-center shrink-0 transition-all duration-300 shadow-inner group-hover:shadow-[0_0_10px_rgba(195,1,2,0.6)]">
                                    <i class="fas {{ $cat['icon'] }} text-[10px] transition-colors"></i>
                                </span>
                                <span class="whitespace-nowrap tracking-tight">{{ $cat['name'] }}</span>
                            </a>
                        @endforeach
                    </div>

                    <!-- Navigation Slider Arrows & View All -->
                    <div class="hidden md:flex items-center gap-2 shrink-0 pl-3.5 border-l border-slate-200/90">
                        <a href="{{ $allCatUrl }}" class="text-[11px] font-black uppercase tracking-wider text-[#c30102] hover:text-[#041536] transition-colors whitespace-nowrap mr-1 flex items-center gap-1">
                            <span>{{ $allCatText }}</span>
                            <i class="fas fa-chevron-right text-[8px]"></i>
                        </a>
                        <button 
                            type="button" 
                            @click="scrollLeft()" 
                            class="w-7 h-7 rounded-full bg-white hover:bg-[#041536] border border-slate-200/90 hover:border-[#041536] text-slate-600 hover:text-white flex items-center justify-center transition-all cursor-pointer shadow-sm hover:scale-105 active:scale-95"
                            aria-label="Previous Category"
                        >
                            <i class="fas fa-chevron-left text-[9px]"></i>
                        </button>
                        <button 
                            type="button" 
                            @click="scrollRight()" 
                            class="w-7 h-7 rounded-full bg-white hover:bg-[#041536] border border-slate-200/90 hover:border-[#041536] text-slate-600 hover:text-white flex items-center justify-center transition-all cursor-pointer shadow-sm hover:scale-105 active:scale-95"
                            aria-label="Next Category"
                        >
                            <i class="fas fa-chevron-right text-[9px]"></i>
                        </button>
                    </div>
                </div>
            </div>
        </section>
        @endif

        {{-- 4. Course Showcase: Clean 10ms-Style Unified Course Grid with Dynamic Track Tabs --}}
        @if(\App\Models\Setting::getVal('landing_courses_enabled', '1') == '1')
        <section class="py-10 sm:py-14 lg:py-16 bg-gradient-to-b from-[#f8fafc] via-[#f1f5f9]/70 to-[#ffffff] border-b border-slate-200/80" x-data="{ activeCourseTab: 'all' }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- 10ms-Style Clean Section Masthead -->
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 sm:mb-10 gap-4">
                    <div>
                        @if(\App\Models\Setting::getVal('landing_courses_badge'))
                        <div class="flex items-center gap-2 mb-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#051c45]/10 text-[#051c45] text-[10px] font-black uppercase tracking-widest border border-[#051c45]/15">
                                <i class="fas fa-graduation-cap text-[#c30102]"></i>
                                <span>{{ \App\Models\Setting::getVal('landing_courses_badge') }}</span>
                            </span>
                        </div>
                        @endif
                        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-[#041536] tracking-tight">
                            {{ \App\Models\Setting::getVal('landing_courses_title', 'Industry Masterclasses & Courses') }}
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1.5 font-normal max-w-2xl leading-relaxed">
                            {{ \App\Models\Setting::getVal('landing_courses_subtitle', 'Practical, verified executive courses designed to accelerate your corporate career in customs, VAT, banking, supply chain, and international trade.') }}
                        </p>
                    </div>

                    <!-- Clean 'Browse All Courses' Action Link -->
                    <div class="shrink-0 flex items-center gap-3">
                        <a href="{{ \App\Models\Setting::getVal('landing_courses_bracket_url', route('catalog.index')) }}" 
                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-white hover:bg-[#051c45] text-[#051c45] hover:text-white rounded-xl border border-slate-200 text-xs sm:text-sm font-bold shadow-xs hover:shadow-md transition-all duration-200 group">
                            <span>{{ \App\Models\Setting::getVal('landing_courses_bracket_text', 'Browse All Courses') }}</span>
                            <i class="fas fa-arrow-right text-xs text-[#c30102] group-hover:text-white group-hover:translate-x-0.5 transition-transform"></i>
                        </a>
                    </div>
                </div>

                <!-- 10ms-Style Fluid Track Filter Tab Pills -->
                <div class="mb-8 flex items-center gap-2 sm:gap-3 overflow-x-auto pb-2 scrollbar-none">
                    <button type="button" 
                            @click="activeCourseTab = 'all'"
                            class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all whitespace-nowrap flex items-center gap-2"
                            :class="activeCourseTab === 'all' 
                                ? 'bg-[#051c45] text-white shadow-md ring-2 ring-[#051c45]/20' 
                                : 'bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-slate-200'">
                        <i class="fas fa-th-large text-xs" :class="activeCourseTab === 'all' ? 'text-emerald-400' : 'text-slate-400'"></i>
                        <span>All Programs</span>
                        <span class="px-1.5 py-0.5 rounded-md text-[10px] font-black"
                              :class="activeCourseTab === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'">
                            {{ $courses->count() }}
                        </span>
                    </button>

                    <button type="button" 
                            @click="activeCourseTab = 'certificate'"
                            class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all whitespace-nowrap flex items-center gap-2"
                            :class="activeCourseTab === 'certificate' 
                                ? 'bg-[#051c45] text-white shadow-md ring-2 ring-[#051c45]/20' 
                                : 'bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-slate-200'">
                        <i class="fas fa-certificate text-xs" :class="activeCourseTab === 'certificate' ? 'text-emerald-400' : 'text-slate-400'"></i>
                        <span>{{ \App\Models\Setting::getVal('landing_cert_title', 'Certificate Courses') }}</span>
                        <span class="px-1.5 py-0.5 rounded-md text-[10px] font-black"
                              :class="activeCourseTab === 'certificate' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'">
                            {{ $certificateCourses->count() }}
                        </span>
                    </button>

                    <button type="button" 
                            @click="activeCourseTab = 'day_long'"
                            class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all whitespace-nowrap flex items-center gap-2"
                            :class="activeCourseTab === 'day_long' 
                                ? 'bg-[#051c45] text-white shadow-md ring-2 ring-[#051c45]/20' 
                                : 'bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-slate-200'">
                        <i class="fas fa-bolt text-xs" :class="activeCourseTab === 'day_long' ? 'text-[#c30102]' : 'text-slate-400'"></i>
                        <span>{{ \App\Models\Setting::getVal('landing_daylong_title', 'Day Long Courses') }}</span>
                        <span class="px-1.5 py-0.5 rounded-md text-[10px] font-black"
                              :class="activeCourseTab === 'day_long' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'">
                            {{ $dayLongCourses->count() }}
                        </span>
                    </button>
                </div>

                <!-- Unified 4-Column Clean Course Grid (Preserves .bg-[#f0f8ec] for Cypress test hook) -->
                <div class="bg-[#f0f8ec] !bg-transparent">
                    
                    <!-- TAB 1: ALL COURSES (Default) -->
                    <div x-show="activeCourseTab === 'all'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                            @foreach($courses->take(8) as $course)
                                <x-course-card :course="$course" />
                            @endforeach
                        </div>
                    </div>

                    <!-- TAB 2: CERTIFICATE COURSES -->
                    <div x-show="activeCourseTab === 'certificate'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                            @foreach($certificateCourses as $course)
                                <x-course-card :course="$course" />
                            @endforeach
                        </div>
                    </div>

                    <!-- TAB 3: DAY LONG COURSES -->
                    <div x-show="activeCourseTab === 'day_long'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                            @foreach($dayLongCourses as $course)
                                <x-course-card :course="$course" />
                            @endforeach
                        </div>
                    </div>

                </div>

                <!-- Bottom Gateway Action Button -->
                <div class="mt-8 sm:mt-10 text-center">
                    <a href="{{ route('catalog.index') }}" class="btn-ipdp-secondary inline-flex items-center justify-center gap-2.5 px-8 py-3.5 min-h-[46px] rounded-xl text-xs sm:text-sm font-black uppercase tracking-wider shadow-md hover:shadow-xl hover:scale-105 transition-all">
                        <span>View All {{ $courses->count() }} Courses & Masterclasses</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>

            </div>
        </section>
        @endif
    </div>

    {{-- 5. Executive Career & Job Placement Opportunities (Spotlighted on Front View) --}}
    @if(\App\Models\Setting::getVal('landing_jobs_enabled', '1') == '1' && isset($jobs) && $jobs->count() > 0)
    <section class="py-12 sm:py-16 lg:py-20 bg-gradient-to-b from-[#f8fafc] via-[#f1f5f9] to-[#ffffff] border-b border-slate-200/80 relative overflow-hidden">
        <!-- Subtle Ambient Accents -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-[#c30102]/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-[#041536]/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Header Bar -->
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 sm:mb-12 pb-4 border-b border-slate-200/80 gap-4">
                <div>
                    <h2 class="text-xl sm:text-2xl lg:text-3xl font-black text-[#041536] tracking-tight">
                        {{ \App\Models\Setting::getVal('landing_jobs_title', 'Career & Placement Opportunities') }}
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 font-medium max-w-2xl">
                        {{ \App\Models\Setting::getVal('landing_jobs_subtitle', 'Explore active corporate openings, commercial executive roles, and direct recruitment pathways for ACS Academy certified professionals.') }}
                    </p>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ \App\Models\Setting::getVal('landing_jobs_btn_url', route('jobs.index')) }}" class="btn-ipdp-primary inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold shadow-md hover:shadow-xl transition-all">
                        <span>{{ \App\Models\Setting::getVal('landing_jobs_btn_text', 'Browse All Openings') }}</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- Job Cards Grid (Liquid Glass Architecture) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                @php
                    $homeJobGradients = [
                        'from-[#051c45] via-[#0a2540] to-[#1e3a8a]',
                        'from-[#7f1d1d] via-[#991b1b] to-[#c30102]',
                        'from-[#064e3b] via-[#065f46] to-[#059669]',
                        'from-[#1e1b4b] via-[#312e81] to-[#4338ca]',
                        'from-[#0f172a] via-[#1e293b] to-[#334155]',
                    ];
                @endphp
                @foreach($jobs as $job)
                    @php
                        $jComp = $job->company ?? 'Corporate Partner';
                        $jWords = array_values(array_filter(explode(' ', preg_replace('/[^A-Za-z0-9\s]/', '', $jComp))));
                        $jInit = count($jWords) >= 2
                            ? strtoupper(substr($jWords[0], 0, 1) . substr($jWords[1], 0, 1))
                            : strtoupper(substr($jWords[0] ?? 'CA', 0, 2));
                        $jGrad = $homeJobGradients[crc32($jComp) % count($homeJobGradients)];
                    @endphp
                    <div class="liquid-glass-card bg-white/85 rounded-2xl sm:rounded-3xl p-6 sm:p-7 border border-slate-200/90 shadow-[0_4px_16px_rgba(4,21,54,0.05)] hover:shadow-[0_20px_45px_-10px_rgba(4,21,54,0.14)] hover:border-[#c30102]/30 transition-all duration-300 flex flex-col justify-between group hover:-translate-y-1.5 relative overflow-hidden">
                        
                        <!-- Top Accent Line -->
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-[#041536] via-[#c30102] to-[#10b981] opacity-0 group-hover:opacity-100 transition-opacity"></div>

                        <div>
                            <!-- Header: Badges & Auto-Fitted Logo / Rich Monogram Emblem -->
                            <div class="flex items-start justify-between gap-3 mb-4">
                                <div class="relative w-14 h-14 rounded-2xl group-hover:scale-105 transition-transform shrink-0">
                                    @if(!empty($job->company_logo))
                                        <div class="w-full h-full rounded-2xl bg-white border border-slate-200/90 shadow-sm p-1.5 flex items-center justify-center overflow-hidden">
                                            <img src="{{ storage_asset($job->company_logo) }}" alt="{{ $jComp }}" class="w-full h-full object-contain object-center" onerror="this.parentElement.style.display='none'; this.parentElement.nextElementSibling.style.display='flex';">
                                        </div>
                                        <div style="display:none;" class="w-full h-full rounded-2xl bg-gradient-to-br {{ $jGrad }} text-white shadow-md border border-white/25 items-center justify-center relative overflow-hidden">
                                            <i class="fas fa-building absolute -bottom-2 -right-2 text-2xl text-white/10 pointer-events-none"></i>
                                            <span class="text-base font-black tracking-wider relative z-10">{{ $jInit }}</span>
                                        </div>
                                    @else
                                        <div class="w-full h-full rounded-2xl bg-gradient-to-br {{ $jGrad }} text-white shadow-md border border-white/25 flex items-center justify-center relative overflow-hidden">
                                            <i class="fas fa-building absolute -bottom-2 -right-2 text-2xl text-white/10 pointer-events-none"></i>
                                            <span class="text-base font-black tracking-wider relative z-10">{{ $jInit }}</span>
                                        </div>
                                    @endif
                                    <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-emerald-500 text-white border-2 border-white flex items-center justify-center text-[7px] shadow-xs">
                                        <i class="fas fa-check"></i>
                                    </span>
                                </div>
                                <div class="flex flex-col items-end gap-1.5">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                        <i class="fas fa-check-circle text-[9px] mr-1"></i>Active Role
                                    </span>
                                    @if($job->created_at && $job->created_at->diffInDays(now()) < 14)
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-[#c30102]/10 text-[#c30102]">
                                            Recent
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Title & Company -->
                            <h3 class="text-base sm:text-lg font-black text-[#041536] group-hover:text-[#c30102] transition-colors leading-snug mb-1">
                                {{ $job->title }}
                            </h3>
                            <p class="text-xs sm:text-sm font-semibold text-slate-500 mb-4 flex items-center gap-1.5">
                                <i class="fas fa-building text-slate-400 text-xs"></i>
                                <span>{{ $job->company ?? $job->company_name ?? 'Corporate Partner Enterprise' }}</span>
                            </p>

                            <!-- Meta Pills (Location, Job Type, Salary Range) -->
                            <div class="flex flex-wrap gap-2 mb-4">
                                @if(!empty($job->location))
                                <div class="inline-flex items-center gap-1 text-[11px] font-medium text-slate-600 bg-slate-100/90 px-2.5 py-1 rounded-lg">
                                    <i class="fas fa-map-marker-alt text-[#c30102] text-[10px]"></i>
                                    <span>{{ $job->location }}</span>
                                </div>
                                @endif

                                <div class="inline-flex items-center gap-1 text-[11px] font-medium text-slate-600 bg-slate-100/90 px-2.5 py-1 rounded-lg">
                                    <i class="fas fa-clock text-slate-400 text-[10px]"></i>
                                    <span>{{ $job->job_type ?? 'Full-time' }}</span>
                                </div>

                                @if(!empty($job->salary_range))
                                <div class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-100">
                                    <i class="fas fa-money-bill-wave text-[10px]"></i>
                                    <span>{{ $job->salary_range }}</span>
                                </div>
                                @endif
                            </div>

                            <!-- Description Snippet -->
                            <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed mb-5">
                                {{ $job->description }}
                            </p>
                        </div>

                        <!-- Action Button -->
                        <div class="pt-3 border-t border-slate-100 mt-auto">
                            @if(!empty($job->apply_link))
                                <a href="{{ $job->apply_link }}" target="_blank" rel="noopener noreferrer" class="btn-cab-secondary w-full py-2.5 px-4 rounded-xl text-xs font-black flex items-center justify-center gap-2 group-hover:shadow-md transition-all">
                                    <span>Apply on Company Site</span>
                                    <i class="fas fa-external-link-alt text-[10px]"></i>
                                </a>
                            @else
                                <a href="{{ route('jobs.index') }}" class="btn-cab-secondary w-full py-2.5 px-4 rounded-xl text-xs font-black flex items-center justify-center gap-2 group-hover:shadow-md transition-all">
                                    <span>View Details & Apply</span>
                                    <i class="fas fa-arrow-right text-[10px]"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Corporate Hiring Partner Callout Bar -->
            <div class="liquid-glass-card mt-10 sm:mt-12 bg-white/85 rounded-2xl sm:rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-[#041536] text-white flex items-center justify-center text-xl sm:text-2xl shrink-0 shadow-md">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div>
                        <h4 class="text-base sm:text-lg font-black text-[#041536]">
                            Are You Hiring Commercial, Supply Chain, or Tax Professionals?
                        </h4>
                        <p class="text-xs sm:text-sm text-slate-500 font-medium">
                            Partner with ACS Academy Corporate Placement Cell to access verified, pre-screened alumni and certified practitioners.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3 shrink-0 w-full md:w-auto">
                    <a href="{{ route('contact.index') }}" class="btn-cab-secondary px-6 py-3 rounded-xl text-xs sm:text-sm font-black whitespace-nowrap w-full sm:w-auto text-center shadow-md">
                        <i class="fas fa-bullhorn mr-1.5"></i>Post an Opportunity
                    </a>
                </div>
            </div>

        </div>
    </section>
    @endif

    {{-- 6. Executive Counseling & Admissions Hotline Section (Minimalist Imperial Navy) --}}
    @if(\App\Models\Setting::getVal('landing_counseling_enabled', '1') == '1')
    <section class="py-12 sm:py-16 bg-[#f8fafc] border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#051c45] rounded-3xl p-8 sm:p-12 lg:p-14 text-center text-white relative overflow-hidden shadow-xl border border-white/10">
                <div class="absolute -top-24 -right-24 w-80 h-80 bg-[#c30102]/15 rounded-full blur-[90px] pointer-events-none"></div>
                <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-[#10b981]/12 rounded-full blur-[90px] pointer-events-none"></div>

                <div class="relative z-10 max-w-2xl mx-auto">
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black mb-3 text-white tracking-tight">
                        {{ \App\Models\Setting::getVal('landing_counseling_title', 'Need To Learn More ?') }}
                    </h2>
                    <p class="text-xs sm:text-base text-slate-300 mb-7 font-normal leading-relaxed">
                        {{ \App\Models\Setting::getVal('landing_counseling_subtitle', 'Connect with our academic counselors for corporate training customized for your organization, batch reservations, or curriculum guidance.') }}
                    </p>
                    <div class="flex flex-wrap justify-center items-center gap-3 sm:gap-4">
                        @php
                            $phoneLabel = \App\Models\Setting::getVal('landing_counseling_phone_label', 'Call: 01917-054664');
                            $phoneTel = \App\Models\Setting::getVal('landing_counseling_phone_tel', '01917054664');
                            $waLabel = \App\Models\Setting::getVal('landing_counseling_wa_label', 'Instant WhatsApp');
                            $waNumber = \App\Models\Setting::getVal('landing_counseling_wa_number', '8801917054664');
                            $btn3Text = \App\Models\Setting::getVal('landing_counseling_btn3_text', 'Explore Export – Import');
                            $btn3Url = \App\Models\Setting::getVal('landing_counseling_btn3_url', route('catalog.category', 'export-import'));
                        @endphp
                        <a href="tel:{{ $phoneTel }}" class="btn-ipdp-primary px-7 py-3.5 rounded-xl font-bold text-xs sm:text-sm flex items-center justify-center gap-2 transition-all w-full sm:w-auto">
                            <i class="fas fa-phone-alt"></i>
                            <span>{{ $phoneLabel }}</span>
                        </a>
                        <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener noreferrer" class="bg-[#25D366] hover:bg-[#20ba5a] text-white px-7 py-3.5 rounded-xl font-bold text-xs sm:text-sm shadow-md transition-all flex items-center justify-center gap-2 w-full sm:w-auto">
                            <i class="fab fa-whatsapp text-base"></i>
                            <span>{{ $waLabel }}</span>
                        </a>
                        <a href="{{ $btn3Url }}" class="bg-white/10 hover:bg-white/20 text-white border border-white/20 px-7 py-3.5 rounded-xl font-bold text-xs sm:text-sm transition-all flex items-center justify-center gap-2 w-full sm:w-auto">
                            <span>{{ $btn3Text }}</span>
                            <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- 7. Distinguished Faculty & Industry Fellows Section --}}
    @if(\App\Models\Setting::getVal('landing_faculty_enabled', '1') == '1')
    @php
        $landingInstructors = \App\Models\Instructor::where('is_active', true)->orderBy('order', 'asc')->take(4)->get();
    @endphp
    @if($landingInstructors->isNotEmpty())
    <section id="faculty" class="py-12 sm:py-16 lg:py-20 bg-white border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 sm:mb-12 pb-4 border-b border-slate-200/80 gap-3">
                <div>
                    <h2 class="text-xl sm:text-2xl lg:text-3xl font-black text-[#041536] tracking-tight">
                        {{ \App\Models\Setting::getVal('landing_faculty_title', 'Distinguished Faculty & Industry Fellows') }}
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 font-medium">
                        Direct executive mentorship guided by high-profile corporate leaders, senior chartered accountants, NBR tax advocates, and logistics directors.
                    </p>
                </div>
                <a href="{{ route('instructors.index') }}" class="text-xs sm:text-sm font-bold text-[#c30102] hover:text-[#041536] transition-colors inline-flex items-center gap-1.5 group shrink-0">
                    <span>View All Faculty Profiles</span>
                    <i class="fas fa-arrow-right text-xs group-hover:translate-x-1.5 transition-transform"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
                @foreach($landingInstructors as $inst)
                    <div class="liquid-glass-card bg-white/85 rounded-3xl border border-slate-200/80 p-5 sm:p-6 text-center shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 group flex flex-col justify-between hover:border-[#c30102]/30">
                        <div>
                            <div class="relative w-24 h-24 sm:w-28 sm:h-28 mx-auto rounded-full overflow-hidden mb-4 border-4 border-white shadow-md bg-white ring-2 ring-slate-200 group-hover:ring-[#c30102]/40 transition-all duration-300">
                                <img src="{{ $inst->avatar_url }}" alt="{{ $inst->name }}" class="w-full h-full object-cover object-top" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($inst->name) }}&background=051c45&color=fff';">
                            </div>
                            <h3 class="text-sm sm:text-base font-extrabold text-[#041536] group-hover:text-[#c30102] transition-colors leading-tight mb-1">
                                {{ $inst->name }}
                            </h3>
                            <p class="text-[11px] sm:text-xs text-[#c30102] font-bold line-clamp-1 mb-2">
                                {{ $inst->title }}
                            </p>
                            <span class="inline-block bg-emerald-50 text-emerald-800 border border-emerald-200 text-[10px] font-bold px-2.5 py-0.5 rounded-full mb-3">
                                {{ $inst->expertise }}
                            </span>
                            @if($inst->bio)
                                <p class="text-xs text-slate-500 leading-relaxed line-clamp-2 mb-4 font-normal">
                                    {{ $inst->bio }}
                                </p>
                            @endif
                        </div>
                        <a href="{{ route('catalog.index') }}" class="py-2 px-3 bg-white hover:bg-[#041536] text-[#041536] hover:text-white border border-slate-200 rounded-xl text-xs font-bold transition-all text-center shadow-xs">
                            Explore Sessions →
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif
    @endif

    {{-- 8. Our Features Section --}}
    @if(\App\Models\Setting::getVal('landing_features_enabled', '1') == '1')
    <section id="features" class="py-10 sm:py-16 lg:py-20 bg-slate-50/70 border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10 sm:mb-14">
                <h2 class="text-2xl sm:text-3xl font-black text-[#041536] tracking-tight">
                    {{ \App\Models\Setting::getVal('landing_features_title', 'Our Features') }}
                </h2>
                <div class="w-16 h-1 bg-gradient-to-r from-[#c30102] via-[#e11d48] to-[#041536] mx-auto mt-3 rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-8">
                @php
                    $defaultFeatures = [
                        1 => [
                            'icon' => 'fa-graduation-cap',
                            'title' => 'Industry Level Curriculum',
                            'desc' => 'Designed alongside top corporate practitioners and customs specialists to fulfill active corporate requirements.'
                        ],
                        2 => [
                            'icon' => 'fa-chalkboard-teacher',
                            'title' => 'Distinguished Faculty & Fellows',
                            'desc' => 'Direct executive mentorship guided by high-profile corporate leaders, senior chartered accountants, NBR tax advocates, and logistics directors.'
                        ],
                        3 => [
                            'icon' => 'fa-laptop-house',
                            'title' => '100% Live Interactive Online',
                            'desc' => 'Live Zoom masterclasses with real-time Q&A, session recordings, and cloud course materials accessible from anywhere.'
                        ],
                        4 => [
                            'icon' => 'fa-certificate',
                            'title' => 'Recognized Certifications',
                            'desc' => 'Credential verified certifications recognized by leading industrial groups across Bangladesh.'
                        ],
                        5 => [
                            'icon' => 'fa-headset',
                            'title' => 'Support Services',
                            'desc' => 'Post-training support, consultation, documentation guidance, and active peer professional networking.'
                        ],
                        6 => [
                            'icon' => 'fa-layer-group',
                            'title' => 'Wide Range of Courses',
                            'desc' => 'Over 14 professional sectors covering Import-Export, SCM, VAT, HR, Civil Engineering, and Corporate Tax.'
                        ],
                    ];

                    $features = [];
                    for ($i = 1; $i <= 6; $i++) {
                        $featTitle = \App\Models\Setting::getVal("landing_feature_{$i}_title", $defaultFeatures[$i]['title']);
                        $featDesc = \App\Models\Setting::getVal("landing_feature_{$i}_desc", $defaultFeatures[$i]['desc']);
                        $featIcon = \App\Models\Setting::getVal("landing_feature_{$i}_icon", $defaultFeatures[$i]['icon']);
                        
                        if (!empty($featTitle)) {
                            $features[] = [
                                'icon' => str_starts_with($featIcon, 'fa-') ? $featIcon : ('fa-' . $featIcon),
                                'title' => $featTitle,
                                'desc' => $featDesc,
                            ];
                        }
                    }
                @endphp

                @foreach($features as $feat)
                    <div class="liquid-glass-card bg-white/85 p-5 sm:p-7 rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-[0_2px_12px_rgba(4,21,54,0.04)] hover:shadow-[0_18px_40px_-8px_rgba(4,21,54,0.14)] hover:border-[#c30102]/30 transition-all duration-300 group hover:-translate-y-1.5 flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-[#041536]/5 text-[#041536] group-hover:bg-gradient-to-tr group-hover:from-[#c30102] group-hover:to-[#e11d48] group-hover:text-white transition-all duration-300 flex items-center justify-center text-xl sm:text-2xl mb-4 sm:mb-5 shadow-sm">
                                <i class="fas {{ $feat['icon'] }}"></i>
                            </div>
                            <h4 class="text-base sm:text-lg font-black text-[#041536] mb-1.5 sm:mb-2 group-hover:text-[#c30102] transition-colors">
                                {{ $feat['title'] }}
                            </h4>
                            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed font-normal">
                                {{ $feat['desc'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- 8. Executive Insights & Knowledge Hub (Blog Section - Below Features) --}}
    @if(\App\Models\Setting::getVal('landing_blog_enabled', '1') == '1' && isset($recentBlogs) && $recentBlogs->count() > 0)
    <section class="py-10 sm:py-16 lg:py-20 bg-gradient-to-b from-[#f8fafc] via-[#f4f7fb] to-[#f8fafc] border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 sm:mb-12 pb-4 border-b border-slate-200/80 gap-3">
                <div>
                    <h2 class="text-xl sm:text-2xl lg:text-3xl font-black text-[#041536] tracking-tight">
                        {{ \App\Models\Setting::getVal('landing_blog_title', 'Knowledge Hub & Publications') }}
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 font-medium">
                        {{ \App\Models\Setting::getVal('landing_blog_subtitle', 'Thought leadership, customs directives, tax reforms, and supply chain insights by ACS Academy Distinguished Faculty.') }}
                    </p>
                </div>
                <a href="{{ \App\Models\Setting::getVal('landing_blog_btn_url', route('blog.index')) }}" class="text-xs sm:text-sm font-bold text-[#c30102] hover:text-[#041536] transition-colors inline-flex items-center gap-1.5 group shrink-0">
                    <span>{{ \App\Models\Setting::getVal('landing_blog_btn_text', 'View All Publications') }}</span>
                    <i class="fas fa-arrow-right text-xs group-hover:translate-x-1.5 transition-transform"></i>
                </a>
            </div>

            <!-- 3-Column Blog Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                @php
                    $defaultBlogImgs = [
                        'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=800&q=80',
                    ];
                @endphp
                @foreach($recentBlogs as $blog)
                    <article class="liquid-glass-card group bg-white/85 rounded-2xl sm:rounded-3xl shadow-[0_4px_16px_rgba(4,21,54,0.06)] hover:shadow-[0_20px_45px_-10px_rgba(4,21,54,0.16)] border border-slate-200/90 hover:border-[#c30102]/30 overflow-hidden transition-all duration-300 flex flex-col h-full hover:-translate-y-1.5">
                        <!-- Cover Image -->
                        <div class="relative aspect-video w-full overflow-hidden bg-slate-900">
                            <a href="{{ route('blog.show', $blog->slug) }}">
                                @php
                                    $coverUrl = $blog->featured_image_url;
                                @endphp
                                <img 
                                   src="{{ $coverUrl }}" 
                                   alt="{{ $blog->title }}" 
                                   class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-95 group-hover:opacity-100"
                                   loading="lazy"
                                   onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=800&q=80';"
                                >
                            </a>
                            <div class="absolute top-3 left-3">
                                <span class="inline-block px-3 py-1 bg-gradient-to-r from-[#c30102] to-[#e11d48] text-white rounded-lg text-[10px] font-black uppercase tracking-wider shadow-md border border-white/20">
                                    {{ $blog->category ?? 'Insights' }}
                                </span>
                            </div>
                        </div>

                        <!-- Article Content -->
                        <div class="p-4 sm:p-6 flex-1 flex flex-col justify-between">
                            <div>
                                <!-- Meta info: Date & Reading Time -->
                                <div class="flex items-center gap-2 text-xs text-slate-500 font-medium mb-2">
                                    <i class="far fa-calendar-alt text-[#c30102]"></i>
                                    <span>{{ $blog->published_at ? $blog->published_at->format('M d, Y') : date('M d, Y') }}</span>
                                    <span>&bull;</span>
                                    <i class="far fa-clock text-emerald-600"></i>
                                    <span>{{ $blog->read_time ?? '5 min' }}</span>
                                </div>

                                <!-- Title -->
                                <h3 class="text-sm sm:text-base lg:text-lg font-bold text-[#041536] group-hover:text-[#c30102] transition-colors leading-snug mb-2 line-clamp-2">
                                    <a href="{{ route('blog.show', $blog->slug) }}">
                                        {{ $blog->title }}
                                    </a>
                                </h3>

                                <!-- Excerpt -->
                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed line-clamp-2 sm:line-clamp-3 mb-4 font-normal">
                                    {{ $blog->excerpt }}
                                </p>
                            </div>

                            <!-- Author & Action Footer -->
                            <div class="border-t border-slate-100 pt-3 sm:pt-4 mt-auto flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center text-xs font-bold text-[#041536]">
                                        @if($blog->author && ($blog->author->profile_picture || ($blog->author->instructorProfile && $blog->author->instructorProfile->avatar)))
                                            <img src="{{ $blog->author->profile_picture_url }}" alt="{{ $blog->author_name }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($blog->author_name ?? 'Faculty') }}&background=051c45&color=fff';">
                                        @else
                                            {{ substr($blog->author_name ?? 'F', 0, 1) }}
                                        @endif
                                    </div>
                                    <span class="text-xs font-bold text-[#041536] truncate max-w-[110px] sm:max-w-[130px]">
                                        {{ $blog->author_name ?? 'Distinguished Faculty' }}
                                    </span>
                                </div>

                                <a href="{{ route('blog.show', $blog->slug) }}" class="text-xs font-bold text-[#c30102] group-hover:text-[#041536] flex items-center gap-1 transition-colors">
                                    <span>Read Brief</span>
                                    <i class="fas fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- 10. Dynamic Admin-Created Custom Homepage Sections --}}
    @php
        $customHomeSections = \App\Models\Setting::getCustomSections(true);
    @endphp
    @if(!empty($customHomeSections))
        @foreach($customHomeSections as $cSec)
            @php
                $secTheme = $cSec['theme'] ?? 'white';
                $secBgClass = match($secTheme) {
                    'navy' => 'bg-gradient-to-br from-[#020b1c] via-[#051c45] to-[#0a2540] text-white border-b border-white/10',
                    'slate' => 'bg-[#f8fafc] text-slate-900 border-b border-slate-200/80',
                    default => 'bg-white text-slate-900 border-b border-slate-200/80',
                };
                $secTitleClass = $secTheme === 'navy' ? 'text-white' : 'text-[#051c45]';
                $secSubClass = $secTheme === 'navy' ? 'text-slate-300' : 'text-slate-600';
            @endphp
            <section id="section-{{ $cSec['id'] ?? $loop->index }}" class="py-12 sm:py-16 {{ $secBgClass }}">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="max-w-3xl mx-auto text-center space-y-4">
                        @if(!empty($cSec['badge']))
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider {{ $secTheme === 'navy' ? 'bg-white/10 text-emerald-300 border border-white/15' : 'bg-[#051c45]/8 text-[#051c45]' }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#10b981]"></span>
                                <span>{{ $cSec['badge'] }}</span>
                            </span>
                        @endif
                        <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight {{ $secTitleClass }}">
                            {{ $cSec['title'] }}
                        </h2>
                        @if(!empty($cSec['subtitle']))
                            <p class="text-sm sm:text-base font-medium {{ $secSubClass }}">
                                {{ $cSec['subtitle'] }}
                            </p>
                        @endif
                        @if(!empty($cSec['content']))
                            <div class="pt-2 text-sm sm:text-base leading-relaxed {{ $secSubClass }} prose max-w-none {{ $secTheme === 'navy' ? 'prose-invert' : '' }}">
                                {!! $cSec['content'] !!}
                            </div>
                        @endif
                        @if(!empty($cSec['cta_text']))
                            <div class="pt-4">
                                <a href="{{ $cSec['cta_url'] ?: route('catalog.index') }}" class="btn-ipdp-primary inline-flex items-center gap-2 px-7 py-3.5 rounded-xl text-xs sm:text-sm font-bold text-white shadow-md">
                                    <span>{{ $cSec['cta_text'] }}</span>
                                    <i class="fas fa-arrow-right text-[11px]"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        @endforeach
    @endif

@endif

</div>
@endsection
