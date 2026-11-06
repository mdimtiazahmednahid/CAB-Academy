@extends('layouts.public')

@section('title', 'Executive Training & Professional Certifications')
@section('og_title', \App\Models\Setting::getVal('site_tagline', 'Learn, Grow, Lead.') . ' – ' . \App\Models\Setting::getVal('site_name', 'Academy of Commercial & Supply Chain (ACS)'))
@section('og_image', asset('images/og-banner.jpg'))
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
                        \App\Support\DatabaseSchemaRepair::ensureSchema();
                        $dbCategories = \App\Models\Category::active()->ordered()->with('headInstructor')->get();
                        if ($dbCategories->isNotEmpty()) {
                            $runningCategories = $dbCategories->map(function ($cat) {
                                return [
                                    'name' => $cat->name,
                                    'slug' => $cat->slug,
                                    'icon' => $cat->icon ?: 'fa-tag',
                                    'description' => $cat->description,
                                    'subcategories' => $cat->effective_subcategories,
                                    'head' => $cat->resolved_head,
                                    'team' => $cat->resolved_team,
                                ];
                            })->toArray();
                        } else {
                            $runningCategories = collect(\App\Models\Category::getCoreDepartmentsBlueprint())->map(function ($dept) {
                                return [
                                    'name' => $dept['name'],
                                    'slug' => $dept['slug'],
                                    'icon' => $dept['icon'],
                                    'description' => $dept['description'],
                                    'subcategories' => $dept['subcategories'],
                                    'head' => null,
                                    'team' => collect(),
                                ];
                            })->toArray();
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
        <section class="py-8 sm:py-12 bg-gradient-to-b from-[#f8fafc] via-[#f1f5f9]/70 to-[#ffffff] border-b border-slate-200/80" x-data="{ activeCourseTab: 'all' }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Compact Section Header + Tabs Bar -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-5 sm:mb-6 gap-3">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-xl sm:text-2xl font-extrabold text-[#041536] tracking-tight">
                            @php
                                $rawCourseTitle = \App\Models\Setting::getVal('landing_courses_title', 'Courses & Programs');
                                $cleanCourseTitle = ($rawCourseTitle === 'Industry Masterclasses & Courses') ? 'Courses & Programs' : $rawCourseTitle;
                            @endphp
                            {{ $cleanCourseTitle }}
                        </h2>
                        <a href="{{ \App\Models\Setting::getVal('landing_courses_bracket_url', route('catalog.index')) }}" 
                           class="sm:hidden inline-flex items-center gap-1 text-xs font-bold text-[#c30102] hover:text-[#051c45]">
                            <span>View All</span>
                            <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>

                    <div class="flex items-center justify-between sm:justify-end gap-2 sm:gap-3">
                        <!-- Fluid Track Filter Tab Pills -->
                        <div class="flex items-center gap-1.5 overflow-x-auto scrollbar-none">
                            <button type="button" 
                                    @click="activeCourseTab = 'all'"
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all whitespace-nowrap flex items-center gap-1.5"
                                    :class="activeCourseTab === 'all' 
                                        ? 'bg-[#051c45] text-white shadow-xs' 
                                        : 'bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-slate-200'">
                                <span>All</span>
                                <span class="px-1.5 py-0.2 rounded text-[10px] font-extrabold"
                                      :class="activeCourseTab === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'">
                                    {{ $courses->count() }}
                                </span>
                            </button>

                            <button type="button" 
                                    @click="activeCourseTab = 'certificate'"
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all whitespace-nowrap flex items-center gap-1.5"
                                    :class="activeCourseTab === 'certificate' 
                                        ? 'bg-[#051c45] text-white shadow-xs' 
                                        : 'bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-slate-200'">
                                <span>Certificate</span>
                                <span class="px-1.5 py-0.2 rounded text-[10px] font-extrabold"
                                      :class="activeCourseTab === 'certificate' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'">
                                    {{ $certificateCourses->count() }}
                                </span>
                            </button>

                            <button type="button" 
                                    @click="activeCourseTab = 'day_long'"
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all whitespace-nowrap flex items-center gap-1.5"
                                    :class="activeCourseTab === 'day_long' 
                                        ? 'bg-[#051c45] text-white shadow-xs' 
                                        : 'bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-slate-200'">
                                <span>Day-Long</span>
                                <span class="px-1.5 py-0.2 rounded text-[10px] font-extrabold"
                                      :class="activeCourseTab === 'day_long' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'">
                                    {{ $dayLongCourses->count() }}
                                </span>
                            </button>
                        </div>

                        <a href="{{ \App\Models\Setting::getVal('landing_courses_bracket_url', route('catalog.index')) }}" 
                           class="hidden sm:inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-[#c30102] hover:text-[#051c45] transition-colors shrink-0 ml-2 group">
                            <span>View All</span>
                            <i class="fas fa-arrow-right text-xs group-hover:translate-x-0.5 transition-transform"></i>
                        </a>
                    </div>
                </div>

                <!-- Unified 4-Column Clean Course Grid (Preserves .bg-[#f0f8ec] for Cypress test hook) -->
                <div class="bg-[#f0f8ec] !bg-transparent">
                    
                    <!-- TAB 1: ALL COURSES (Default) -->
                    <div x-show="activeCourseTab === 'all'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-5">
                            @foreach($courses->take(8) as $course)
                                <x-course-card :course="$course" />
                            @endforeach
                        </div>
                    </div>

                    <!-- TAB 2: CERTIFICATE COURSES -->
                    <div x-show="activeCourseTab === 'certificate'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-5">
                            @foreach($certificateCourses as $course)
                                <x-course-card :course="$course" />
                            @endforeach
                        </div>
                    </div>

                    <!-- TAB 3: DAY LONG COURSES -->
                    <div x-show="activeCourseTab === 'day_long'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-5">
                            @foreach($dayLongCourses as $course)
                                <x-course-card :course="$course" />
                            @endforeach
                        </div>
                    </div>

                </div>

                <!-- Bottom Gateway Action Button -->
                <div class="mt-6 sm:mt-8 text-center">
                    <a href="{{ route('catalog.index') }}" class="btn-ipdp-secondary inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-xs font-extrabold shadow-sm hover:shadow-md transition-all">
                        <span>Explore Courses</span>
                        <i class="fas fa-arrow-right text-[10px]"></i>
                    </a>
                </div>

            </div>
        </section>
        @endif
    </div>

    {{-- 4B. ACS 4 Learning Departments (Compact & Scannable) --}}
    <section id="learning-departments" class="py-8 sm:py-12 bg-white border-b border-slate-200/80 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-5 sm:mb-6 pb-3 border-b border-slate-200/80 gap-3">
                <h2 class="text-xl sm:text-2xl font-extrabold text-[#041536] tracking-tight">
                    4 Learning Departments
                </h2>
                <a href="{{ route('catalog.index') }}" class="text-xs sm:text-sm font-bold text-[#c30102] hover:text-[#041536] transition-colors inline-flex items-center gap-1.5 group shrink-0">
                    <span>View All</span>
                    <i class="fas fa-arrow-right text-xs group-hover:translate-x-0.5 transition-transform"></i>
                </a>
            </div>

            @php
                $coreDepts = isset($runningCategories) && !empty($runningCategories)
                    ? $runningCategories
                    : collect(\App\Models\Category::getCoreDepartmentsBlueprint())->map(fn($d) => [
                        'name' => $d['name'],
                        'slug' => $d['slug'],
                        'icon' => $d['icon'],
                        'description' => $d['description'],
                        'subcategories' => $d['subcategories'],
                        'head' => null,
                        'team' => collect(),
                    ])->toArray();
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                @foreach($coreDepts as $dIdx => $dept)
                    <div class="group rounded-2xl bg-slate-50/60 hover:bg-white border border-slate-200/90 hover:border-[#051c45]/30 p-4 sm:p-5 shadow-2xs hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <!-- Compact Header: Icon + Department Name -->
                            <a href="{{ route('catalog.category', $dept['slug']) }}" class="flex items-center gap-2.5 mb-3">
                                <div class="w-9 h-9 rounded-xl bg-[#051c45] group-hover:bg-[#c30102] text-white flex items-center justify-center text-sm shrink-0 transition-colors">
                                    <i class="fas {{ $dept['icon'] ?? 'fa-layer-group' }}"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-none mb-0.5">Dept 0{{ $dIdx + 1 }}</span>
                                    <h3 class="text-sm sm:text-base font-extrabold text-[#041536] group-hover:text-[#c30102] transition-colors leading-tight truncate">
                                        {{ $dept['name'] }}
                                    </h3>
                                </div>
                            </a>

                            <!-- Compact 1-Line Department Head Pill -->
                            <div class="mb-3 px-2.5 py-1.5 rounded-xl bg-white border border-slate-200/80 flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <i class="fas fa-user-tie text-[11px] text-[#c30102] shrink-0"></i>
                                    <span class="text-[11px] font-bold text-slate-700 truncate">
                                        {{ !empty($dept['head']) ? $dept['head']->name : 'Department Head' }}
                                    </span>
                                </div>
                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 shrink-0">
                                    + Team
                                </span>
                            </div>

                            <!-- 6 Sub-Tracks (Clean & Compact) -->
                            @if(!empty($dept['subcategories']))
                                <ul class="space-y-1 mb-4">
                                    @foreach($dept['subcategories'] as $subTrack)
                                        <li>
                                            <a 
                                                href="{{ route('catalog.category', ['category' => $dept['slug'], 'subtrack' => $subTrack]) }}"
                                                class="flex items-center gap-1.5 text-xs text-slate-600 hover:text-[#c30102] font-medium py-0.5 transition-colors"
                                            >
                                                <i class="fas fa-angle-right text-[#c30102] text-[10px] shrink-0"></i>
                                                <span class="truncate">{{ $subTrack }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        <a 
                            href="{{ route('catalog.category', $dept['slug']) }}" 
                            class="w-full py-2 px-3 rounded-xl bg-white group-hover:bg-[#051c45] text-[#051c45] group-hover:text-white border border-slate-200/90 group-hover:border-[#051c45] text-xs font-bold flex items-center justify-center gap-1.5 transition-all"
                        >
                            <span>Explore</span>
                            <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 5. Career Opportunities (High-Density Sleek Executive Job Cards) --}}
    @if(\App\Models\Setting::getVal('landing_jobs_enabled', '1') == '1' && isset($jobs) && $jobs->count() > 0)
    <section class="py-8 sm:py-12 bg-[#f8fafc] border-b border-slate-200/80 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Compact Header Bar -->
            <div class="flex items-center justify-between mb-5 sm:mb-6 pb-3 border-b border-slate-200/80 gap-3">
                <h2 class="text-xl sm:text-2xl font-extrabold text-[#041536] tracking-tight">
                    @php
                        $rawJobsTitle = \App\Models\Setting::getVal('landing_jobs_title', 'Career Opportunities');
                        $cleanJobsTitle = ($rawJobsTitle === 'Career & Placement Opportunities') ? 'Career Opportunities' : $rawJobsTitle;
                    @endphp
                    {{ $cleanJobsTitle }}
                </h2>
                <a href="{{ \App\Models\Setting::getVal('landing_jobs_btn_url', route('jobs.index')) }}" class="text-xs sm:text-sm font-bold text-[#c30102] hover:text-[#041536] transition-colors inline-flex items-center gap-1.5 group shrink-0">
                    <span>View All</span>
                    <i class="fas fa-arrow-right text-xs group-hover:translate-x-0.5 transition-transform"></i>
                </a>
            </div>

            <!-- Compact Executive Career Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                @php
                    $homeJobGradients = [
                        'from-[#051c45] to-[#1e3a8a]',
                        'from-[#7f1d1d] to-[#c30102]',
                        'from-[#064e3b] to-[#059669]',
                        'from-[#1e1b4b] to-[#4338ca]',
                        'from-[#0f172a] to-[#334155]',
                    ];
                @endphp
                @foreach($jobs as $job)
                    @php
                        $jComp = $job->company ?? $job->company_name ?? 'Corporate Partner';
                        $jWords = array_values(array_filter(explode(' ', preg_replace('/[^A-Za-z0-9\s]/', '', $jComp))));
                        $jInit = count($jWords) >= 2
                            ? strtoupper(substr($jWords[0], 0, 1) . substr($jWords[1], 0, 1))
                            : strtoupper(substr($jWords[0] ?? 'CA', 0, 2));
                        $jGrad = $homeJobGradients[crc32($jComp) % count($homeJobGradients)];
                        $jUrl = !empty($job->apply_link) ? $job->apply_link : route('jobs.index');
                        $jExternal = !empty($job->apply_link);
                    @endphp
                    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-[#051c45]/30 transition-all duration-200 flex flex-col justify-between group">
                        <div>
                            <!-- Top Row: Monogram/Logo + Title & Company Inline + Type Badge -->
                            <div class="flex items-start gap-3 mb-3">
                                <div class="relative w-11 h-11 rounded-xl shrink-0">
                                    @if(!empty($job->company_logo))
                                        <div class="w-full h-full rounded-xl bg-white border border-slate-200 p-1 flex items-center justify-center overflow-hidden">
                                            <img src="{{ storage_asset($job->company_logo) }}" alt="{{ $jComp }}" class="w-full h-full object-contain object-center" onerror="this.parentElement.style.display='none'; this.parentElement.nextElementSibling.style.display='flex';">
                                        </div>
                                        <div style="display:none;" class="w-full h-full rounded-xl bg-gradient-to-br {{ $jGrad }} text-white items-center justify-center font-extrabold text-xs">
                                            {{ $jInit }}
                                        </div>
                                    @else
                                        <div class="w-full h-full rounded-xl bg-gradient-to-br {{ $jGrad }} text-white flex items-center justify-center font-extrabold text-xs shadow-2xs">
                                            {{ $jInit }}
                                        </div>
                                    @endif
                                    <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-emerald-500 text-white border-2 border-white flex items-center justify-center text-[6px]">
                                        <i class="fas fa-check"></i>
                                    </span>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-2">
                                        <h3 class="text-sm sm:text-base font-extrabold text-[#041536] group-hover:text-[#c30102] transition-colors leading-snug line-clamp-1">
                                            {{ $job->title }}
                                        </h3>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600 shrink-0">
                                            {{ $job->job_type ?? 'Full-time' }}
                                        </span>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-500 truncate mt-0.5">
                                        {{ $jComp }}
                                    </p>
                                </div>
                            </div>

                            <!-- Compact Metadata Chips (Location & Salary) -->
                            <div class="flex flex-wrap items-center gap-1.5 mb-3.5">
                                @if(!empty($job->location))
                                <span class="inline-flex items-center gap-1 text-[11px] font-medium text-slate-600 bg-slate-50 border border-slate-200/70 px-2 py-0.5 rounded-lg">
                                    <i class="fas fa-map-marker-alt text-[#c30102] text-[9px]"></i>
                                    <span class="truncate max-w-[140px]">{{ $job->location }}</span>
                                </span>
                                @endif

                                @if(!empty($job->salary_range))
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/60 px-2 py-0.5 rounded-lg">
                                    <i class="fas fa-money-bill-wave text-[9px]"></i>
                                    <span>{{ $job->salary_range }}</span>
                                </span>
                                @endif
                            </div>
                        </div>

                        <!-- Crisp 1-Line Footer: Verified/Posted Status + Direct Apply Button -->
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2 mt-auto">
                            <span class="text-[11px] font-medium text-slate-400 flex items-center gap-1">
                                <i class="far fa-clock text-[10px]"></i>
                                <span>{{ $job->created_at ? $job->created_at->diffForHumans(null, true) . ' ago' : 'Open Role' }}</span>
                            </span>

                            <a href="{{ $jUrl }}" @if($jExternal) target="_blank" rel="noopener noreferrer" @endif class="px-3.5 py-1.5 rounded-xl bg-[#051c45] hover:bg-[#c30102] text-white text-xs font-bold inline-flex items-center gap-1.5 transition-colors shadow-2xs">
                                <span>Apply Now</span>
                                <i class="fas {{ $jExternal ? 'fa-external-link-alt' : 'fa-arrow-right' }} text-[9px]"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Compact Corporate Hiring Callout Bar -->
            <div class="mt-5 bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-3 text-center sm:text-left">
                    <div class="w-9 h-9 rounded-xl bg-[#041536] text-white hidden sm:flex items-center justify-center text-sm shrink-0">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div>
                        <h4 class="text-xs sm:text-sm font-extrabold text-[#041536]">
                            Hiring Commercial or Supply Chain Professionals?
                        </h4>
                        <p class="text-[11px] text-slate-500">
                            Recruit verified ACS Academy alumni directly for your organization.
                        </p>
                    </div>
                </div>
                <a href="{{ route('contact.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-[#051c45] text-[#051c45] hover:text-white text-xs font-bold whitespace-nowrap transition-colors shrink-0">
                    Post a Job →
                </a>
            </div>

        </div>
    </section>
    @endif

    {{-- 6. Executive Counseling & Admissions Hotline Section (Compact Horizontal Bar) --}}
    @if(\App\Models\Setting::getVal('landing_counseling_enabled', '1') == '1')
    <section class="py-8 sm:py-10 bg-white border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#051c45] rounded-2xl p-5 sm:p-7 text-white relative overflow-hidden shadow-md border border-white/10 flex flex-col lg:flex-row items-center justify-between gap-5">
                <div class="text-center lg:text-left">
                    <h2 class="text-lg sm:text-xl font-extrabold text-white tracking-tight">
                        {{ \App\Models\Setting::getVal('landing_counseling_title', 'Need Admission Guidance?') }}
                    </h2>
                    <p class="text-xs text-slate-300 mt-1 max-w-xl">
                        Speak with our academic counselors for batch schedules, corporate training, or course selection.
                    </p>
                </div>
                <div class="flex flex-wrap justify-center items-center gap-2.5 shrink-0">
                    @php
                        $phoneLabel = \App\Models\Setting::getVal('landing_counseling_phone_label', 'Call: 01917-054664');
                        $phoneTel = \App\Models\Setting::getVal('landing_counseling_phone_tel', '01917054664');
                        $waLabel = \App\Models\Setting::getVal('landing_counseling_wa_label', 'WhatsApp');
                        $waNumber = \App\Models\Setting::getVal('landing_counseling_wa_number', '8801917054664');
                    @endphp
                    <a href="tel:{{ $phoneTel }}" class="btn-ipdp-primary px-4 py-2.5 rounded-xl font-bold text-xs flex items-center justify-center gap-1.5 transition-all">
                        <i class="fas fa-phone-alt text-[11px]"></i>
                        <span>{{ $phoneLabel }}</span>
                    </a>
                    <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener noreferrer" class="bg-[#25D366] hover:bg-[#20ba5a] text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow-xs transition-all flex items-center justify-center gap-1.5">
                        <i class="fab fa-whatsapp text-sm"></i>
                        <span>{{ $waLabel }}</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- 7. Our Faculty Section (Compact, Zero-Redundancy Cards) --}}
    @if(\App\Models\Setting::getVal('landing_faculty_enabled', '1') == '1')
    @php
        $landingInstructors = \App\Models\Instructor::where('is_active', true)->orderBy('order', 'asc')->take(4)->get();
        $rawFacultyTitle = \App\Models\Setting::getVal('landing_faculty_title', 'Our Faculty');
        $cleanFacultyTitle = ($rawFacultyTitle === 'Distinguished Faculty & Industry Fellows') ? 'Our Faculty' : $rawFacultyTitle;
    @endphp
    @if($landingInstructors->isNotEmpty())
    <section id="faculty" class="py-8 sm:py-12 bg-white border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-5 sm:mb-6 pb-3 border-b border-slate-200/80 gap-3">
                <h2 class="text-xl sm:text-2xl font-extrabold text-[#041536] tracking-tight">
                    {{ $cleanFacultyTitle }}
                </h2>
                <a href="{{ route('instructors.index') }}" class="text-xs sm:text-sm font-bold text-[#c30102] hover:text-[#041536] transition-colors inline-flex items-center gap-1.5 group shrink-0">
                    <span>View All</span>
                    <i class="fas fa-arrow-right text-xs group-hover:translate-x-0.5 transition-transform"></i>
                </a>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-5">
                @foreach($landingInstructors as $inst)
                    <a href="{{ route('instructors.index') }}" class="bg-slate-50/70 hover:bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 text-center shadow-2xs hover:shadow-md transition-all duration-200 group flex flex-col items-center justify-center hover:border-[#051c45]/30">
                        <div class="relative w-14 h-14 sm:w-16 sm:h-16 mx-auto rounded-full overflow-hidden mb-2.5 border-2 border-white shadow-sm bg-white ring-1 ring-slate-200 group-hover:ring-[#c30102]/40 transition-all">
                            <img src="{{ $inst->avatar_url }}" alt="{{ $inst->name }}" class="w-full h-full object-cover object-top" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($inst->name) }}&background=051c45&color=fff';">
                        </div>
                        <h3 class="text-xs sm:text-sm font-extrabold text-[#041536] group-hover:text-[#c30102] transition-colors leading-snug line-clamp-1 mb-0.5">
                            {{ $inst->name }}
                        </h3>
                        <p class="text-[11px] sm:text-xs text-slate-500 font-semibold line-clamp-1">
                            {{ $inst->title }}
                        </p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif
    @endif

    {{-- 8. Our Features Section (Compact Horizontal Cards) --}}
    @if(\App\Models\Setting::getVal('landing_features_enabled', '1') == '1')
    <section id="features" class="py-8 sm:py-12 bg-slate-50/70 border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-5 sm:mb-6 pb-3 border-b border-slate-200/80">
                <h2 class="text-xl sm:text-2xl font-extrabold text-[#041536] tracking-tight">
                    {{ \App\Models\Setting::getVal('landing_features_title', 'Why ACS Academy') }}
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-5">
                @php
                    $defaultFeatures = [
                        1 => [
                            'icon' => 'fa-graduation-cap',
                            'title' => 'Industry-Ready Curriculum',
                            'desc' => 'Practical training built with trade, customs, and supply chain practitioners.'
                        ],
                        2 => [
                            'icon' => 'fa-chalkboard-teacher',
                            'title' => 'Veteran Industry Faculty',
                            'desc' => 'Led by corporate leaders, banking specialists, and supply chain directors.'
                        ],
                        3 => [
                            'icon' => 'fa-laptop-house',
                            'title' => 'Live Interactive Sessions',
                            'desc' => 'Live Zoom classes with real-time Q&A, recordings, and digital study kits.'
                        ],
                        4 => [
                            'icon' => 'fa-certificate',
                            'title' => 'Verified Certification',
                            'desc' => 'QR-verified credentials recognized by corporate employers across Bangladesh.'
                        ],
                        5 => [
                            'icon' => 'fa-headset',
                            'title' => 'Career & Post-Training Support',
                            'desc' => 'Direct job placement assistance, CV reviews, and alumni mentorship.'
                        ],
                        6 => [
                            'icon' => 'fa-layer-group',
                            'title' => '4 Specialized Departments',
                            'desc' => '24 focused training tracks across Commercial, SCM, Career, and Leadership.'
                        ],
                    ];

                    $features = [];
                    for ($i = 1; $i <= 6; $i++) {
                        $featTitle = \App\Models\Setting::getVal("landing_feature_{$i}_title", $defaultFeatures[$i]['title']);
                        if ($featTitle === 'Distinguished Faculty & Fellows') {
                            $featTitle = 'Veteran Industry Faculty';
                        }
                        $featDesc = $defaultFeatures[$i]['desc'];
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
                    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-[#051c45]/30 transition-all duration-200 group flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-[#041536]/5 text-[#041536] group-hover:bg-[#c30102] group-hover:text-white transition-colors flex items-center justify-center text-base shrink-0 mt-0.5">
                            <i class="fas {{ $feat['icon'] }}"></i>
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-sm sm:text-base font-extrabold text-[#041536] mb-1 group-hover:text-[#c30102] transition-colors">
                                {{ $feat['title'] }}
                            </h4>
                            <p class="text-xs text-slate-500 leading-relaxed line-clamp-2">
                                {{ $feat['desc'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- 9. Insights & Articles (Compact Blog Section) --}}
    @if(\App\Models\Setting::getVal('landing_blog_enabled', '1') == '1' && isset($recentBlogs) && $recentBlogs->count() > 0)
    <section class="py-8 sm:py-12 bg-white border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-5 sm:mb-6 pb-3 border-b border-slate-200/80 gap-3">
                <h2 class="text-xl sm:text-2xl font-extrabold text-[#041536] tracking-tight">
                    @php
                        $rawBlogTitle = \App\Models\Setting::getVal('landing_blog_title', 'Insights & Articles');
                        $cleanBlogTitle = ($rawBlogTitle === 'Knowledge Hub & Publications') ? 'Insights & Articles' : $rawBlogTitle;
                    @endphp
                    {{ $cleanBlogTitle }}
                </h2>
                <a href="{{ \App\Models\Setting::getVal('landing_blog_btn_url', route('blog.index')) }}" class="text-xs sm:text-sm font-bold text-[#c30102] hover:text-[#041536] transition-colors inline-flex items-center gap-1.5 group shrink-0">
                    <span>View All</span>
                    <i class="fas fa-arrow-right text-xs group-hover:translate-x-0.5 transition-transform"></i>
                </a>
            </div>

            <!-- 3-Column Compact Blog Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
                @foreach($recentBlogs as $blog)
                    <article class="group bg-white rounded-2xl shadow-2xs hover:shadow-md border border-slate-200/90 hover:border-[#051c45]/30 overflow-hidden transition-all duration-200 flex flex-col h-full">
                        <!-- Cover Image -->
                        <div class="relative aspect-video w-full overflow-hidden bg-slate-900">
                            <a href="{{ route('blog.show', $blog->slug) }}">
                                <img 
                                   src="{{ $blog->featured_image_url }}" 
                                   alt="{{ $blog->title }}" 
                                   class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                   loading="lazy"
                                   onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=800&q=80';"
                                >
                            </a>
                            <div class="absolute top-2.5 left-2.5">
                                <span class="inline-block px-2.5 py-0.5 bg-[#051c45]/90 text-white rounded-md text-[10px] font-bold uppercase tracking-wider">
                                    {{ $blog->category ?? 'Insights' }}
                                </span>
                            </div>
                        </div>

                        <!-- Article Content -->
                        <div class="p-4 flex-1 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center gap-2 text-[11px] text-slate-400 font-medium mb-1.5">
                                    <span>{{ $blog->published_at ? $blog->published_at->format('M d, Y') : date('M d, Y') }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $blog->read_time ?? '5 min' }}</span>
                                </div>

                                <h3 class="text-sm sm:text-base font-extrabold text-[#041536] group-hover:text-[#c30102] transition-colors leading-snug mb-1.5 line-clamp-2">
                                    <a href="{{ route('blog.show', $blog->slug) }}">
                                        {{ $blog->title }}
                                    </a>
                                </h3>

                                <p class="text-xs text-slate-500 leading-relaxed line-clamp-2 mb-3">
                                    {{ $blog->excerpt }}
                                </p>
                            </div>

                            <div class="border-t border-slate-100 pt-2.5 mt-auto flex items-center justify-between">
                                <span class="text-[11px] font-semibold text-slate-600 truncate max-w-[140px]">
                                    {{ $blog->author_name ?? 'ACS Faculty' }}
                                </span>
                                <a href="{{ route('blog.show', $blog->slug) }}" class="text-xs font-bold text-[#c30102] group-hover:text-[#041536] flex items-center gap-1 transition-colors">
                                    <span>Read</span>
                                    <i class="fas fa-arrow-right text-[9px]"></i>
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
