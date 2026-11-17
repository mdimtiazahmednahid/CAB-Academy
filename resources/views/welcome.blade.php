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

{{-- 1. Executive Hero Section (10MS-Inspired Clean Architecture + Integrated 4 Core Tracks) --}}
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
    @endphp

    @if(\App\Models\Setting::getVal('landing_hero_enabled', '1') == '1')
    <section class="relative text-white pt-10 pb-8 sm:pt-12 sm:pb-10 overflow-hidden border-b-2 border-[#dca22c]/50"
             style="background: linear-gradient(135deg, #01143d 0%, #021f59 42%, #063b96 85%, #0066e6 100%);">
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 lg:gap-8 items-center">
                
                <!-- Left Content (3rd on Mobile, 1st on Desktop): ACS Title + Animation + Direct Actions -->
                <div class="lg:col-span-7 order-3 lg:order-1 space-y-3.5 text-center lg:text-left">
                    @php
                        $rawHeroTitle = trim((string) \App\Models\Setting::getVal('landing_hero_title', \App\Models\Setting::getVal('site_name', 'Academy of Commercial & Supply Chain (ACS)')));
                        $rawHeroTagline = trim((string) \App\Models\Setting::getVal('landing_hero_tagline', 'Learn, Grow, Lead.'));
                        $rawHeroSubtitle = trim((string) \App\Models\Setting::getVal('landing_hero_subtitle', 'Online Professional Certification Masterclasses & Corporate Programs.'));

                        if (in_array(strtolower($rawHeroTitle), ['acs', 'acs academy', 'cab academy'], true) && stripos($rawHeroTagline, 'Academy of Commercial') !== false) {
                            $heroTitleText = 'Academy of Commercial & Supply Chain (ACS)';
                            $heroTaglineText = '';
                        } else {
                            $heroTitleText = $rawHeroTitle ?: 'Academy of Commercial & Supply Chain (ACS)';
                            $heroTaglineText = (strcasecmp($rawHeroTagline, 'Learn, Grow, Lead.') === 0 || strcasecmp($rawHeroTagline, $heroTitleText) === 0) ? '' : $rawHeroTagline;
                        }

                        if (stripos($rawHeroSubtitle, 'Join thousands of students learning in-demand skills') !== false) {
                            $heroSubtitleText = 'Practical executive certifications and live masterclasses in Export–Import, Letter of Credit (L/C), Customs, VAT & Supply Chain.';
                        } else {
                            $heroSubtitleText = $rawHeroSubtitle;
                        }

                        $heroTitleSizing = \App\Models\Setting::getBrandSizing($heroTitleText);
                    @endphp

                    @if(!empty($heroTaglineText))
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-xs font-bold text-emerald-300">
                            <span>{!! \App\Models\Setting::formatBrandNameHtml($heroTaglineText) !!}</span>
                        </div>
                    @endif

                    <h1 class="acs-hero-title-sequence font-black tracking-tight text-white leading-[1.15]" style="{{ $heroTitleSizing['hero_style'] }}">
                        {!! $heroTitleSizing['formatted_html'] ?? e($heroTitleText) !!}
                    </h1>
                    
                    <p class="text-sm sm:text-base text-slate-300 font-normal max-w-xl mx-auto lg:mx-0 leading-relaxed">
                        {{ $heroSubtitleText }}
                    </p>

                    <div class="pt-1 flex flex-nowrap items-center justify-center lg:justify-start gap-2.5 sm:gap-3">
                        <a href="{{ \App\Models\Setting::getVal('landing_hero_cta1_url', '#home-category') }}" class="btn-ipdp-primary px-3.5 sm:px-6 py-2.5 sm:py-3 rounded-xl text-white text-xs sm:text-sm font-bold tracking-wide inline-flex items-center justify-center gap-1.5 sm:gap-2 whitespace-nowrap transition-all">
                            <span>{{ \App\Models\Setting::getVal('landing_hero_cta1_text', \App\Models\Setting::getVal('landing_cta_text', 'Explore Courses')) }}</span>
                            <i class="fas fa-arrow-down text-[10px] sm:text-xs"></i>
                        </a>

                        <a href="{{ \App\Models\Setting::getVal('landing_hero_cta2_url', route('catalog.category', 'export-import')) }}" class="px-3.5 sm:px-6 py-2.5 sm:py-3 rounded-xl bg-white/10 hover:bg-white/15 text-white border border-white/15 text-xs sm:text-sm font-semibold tracking-wide inline-flex items-center justify-center gap-1.5 sm:gap-2 whitespace-nowrap transition-all">
                            <span>{{ \App\Models\Setting::getVal('landing_hero_cta2_text', 'Export – Import') }}</span>
                            <i class="fas fa-chevron-right text-[10px] opacity-80"></i>
                        </a>
                    </div>

                    @if(\App\Models\Setting::getVal('show_visitor_counter', '1') == '1')
                        @php
                            $heroTraffic = \App\Support\SiteTraffic::stats();
                        @endphp
                        <div class="pt-1 flex justify-center lg:justify-start">
                            <div class="inline-flex items-center gap-2.5 px-3 py-1 rounded-full bg-white/[0.08] border border-white/15 text-[11px] font-semibold text-slate-200 backdrop-blur-md">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span class="inline-flex items-center gap-1">
                                    <i class="far fa-eye text-[#ffe27a] text-[10px]"></i>
                                    <span>{{ number_format($heroTraffic['total_views']) }} Views</span>
                                </span>
                                <span class="text-white/25">&bull;</span>
                                <span class="inline-flex items-center gap-1">
                                    <i class="fas fa-users text-emerald-300 text-[10px]"></i>
                                    <span>{{ number_format($heroTraffic['unique_visitors']) }} Visitors</span>
                                </span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column (1st on Top on Mobile!): Split 2-Row Promotional Display (Row 1: Latest Courses | Row 2: Sliding Circular Faculty) -->
                <div class="lg:col-span-5 order-1 lg:order-2">
                    @php
                        $spotlightCourses = isset($courses) ? $courses->take(3)->values() : collect();
                        $heroPromoSeconds = (float) \App\Models\Setting::getVal('landing_hero_promo_duration', '3.5');
                        $heroPromoMs = max(1500, min(15000, (int) round(($heroPromoSeconds > 0 ? $heroPromoSeconds : 3.5) * 1000)));
                        $heroFacultyCircles = (isset($instructors) && $instructors->isNotEmpty())
                            ? $instructors
                            : \App\Models\Instructor::orderBy('order')->orderBy('id')->get();
                    @endphp
                    @if($spotlightCourses->isNotEmpty())
                        <div class="rounded-2xl overflow-hidden relative transition-all duration-300 hover:-translate-y-1"
                             style="background: linear-gradient(155deg, #021f59 0%, #01143d 100%); border: 1.5px solid rgba(255, 226, 122, 0.65); box-shadow: 0 22px 54px -10px rgba(1, 20, 61, 0.76), 0 0 32px -4px rgba(220, 162, 44, 0.44);"
                             x-data="{
                                 active: 0,
                                 total: {{ $spotlightCourses->count() }},
                                 durationMs: {{ $heroPromoMs }},
                                 timer: null,
                                 inView: false,
                                 paused: false,
                                 start() {
                                     if (this.total <= 1 || !this.inView || this.paused) return;
                                     this.stop();
                                     this.timer = setInterval(() => { this.active = (this.active + 1) % this.total; }, this.durationMs);
                                 },
                                 stop() {
                                     if (this.timer) { clearInterval(this.timer); this.timer = null; }
                                 }
                             }"
                             x-init="
                                 const io = new IntersectionObserver((entries) => {
                                     entries.forEach(e => { inView = e.isIntersecting; e.isIntersecting ? start() : stop(); });
                                 }, { threshold: 0.35 });
                                 io.observe($el);
                             "
                             @mouseenter="paused = true; stop()"
                             @mouseleave="paused = false; start()"
                             @focusin="paused = true; stop()"
                             @focusout="paused = false; start()"
                             @touchstart.passive="paused = true; stop()"
                             @touchend.passive="paused = false; start()"
                             @touchcancel.passive="paused = false; start()">
                            <!-- Top 24K Gold -> Cobalt -> Crimson Metallic Accent Line -->
                            <div class="h-[3px] w-full" style="background: linear-gradient(90deg, #0066e6 0%, #ffe27a 50%, #d62839 100%);"></div>

                            <div style="position: relative; width: 100%; aspect-ratio: 16 / 10; overflow: hidden; background-color: #01143d;">
                                @foreach($spotlightCourses as $sIdx => $sCourse)
                                    <div x-show="active === {{ $sIdx }}"
                                         x-transition:enter="transition ease-out duration-300"
                                         x-transition:enter-start="opacity-0"
                                         x-transition:enter-end="opacity-100"
                                         style="position: absolute; inset: 0; {{ $sIdx === 0 ? '' : 'display: none;' }}">
                                        <a href="{{ route('courses.show', $sCourse) }}" style="display: block; width: 100%; height: 100%;">
                                            <img src="{{ $sCourse->cover_image_url }}"
                                                 alt="{{ $sCourse->title }}"
                                                 @if($sIdx === 0) fetchpriority="high" @else loading="lazy" @endif
                                                 decoding="async"
                                                 onerror="this.onerror=null;this.src='{{ asset('uploads/covers/cover_1794680723_6af8a79363ac1.jpeg') }}';"
                                                 class="w-full h-full object-cover object-center transition-transform duration-[6000ms] ease-out"
                                                 :class="active === {{ $sIdx }} ? 'scale-105' : 'scale-100'">
                                        </a>
                                        <div class="pointer-events-none" style="position: absolute; left: 0; right: 0; bottom: 0; height: 96px; background: linear-gradient(to top, rgba(1, 20, 61, 0.92), rgba(1, 20, 61, 0.35), transparent);"></div>
                                        <div class="pointer-events-none" style="position: absolute; left: 0; right: 0; bottom: 14px; display: flex; justify-content: center; z-index: 25;">
                                            <a href="{{ route('courses.show', $sCourse) }}"
                                               class="pointer-events-auto btn-ipdp-primary px-6 py-2.5 rounded-full text-xs font-extrabold uppercase tracking-wider shadow-2xl whitespace-nowrap inline-flex items-center gap-2"
                                               style="border: 1.5px solid rgba(255, 226, 122, 0.78);">
                                                <span>Enroll Now</span>
                                                <i class="fas fa-arrow-right text-[10px]"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach

                                @if($spotlightCourses->count() > 1)
                                    <!-- Frosted Glass Edge Arrows (Dead-Center Vertically) -->
                                    <button type="button"
                                            @click="stop(); active = (active - 1 + total) % total"
                                            class="w-8 h-8 rounded-full text-white flex items-center justify-center transition-all cursor-pointer"
                                            style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); z-index: 30; background: rgba(1, 20, 61, 0.68); border: 1px solid rgba(255, 255, 255, 0.3); backdrop-filter: blur(8px);"
                                            aria-label="Previous Promotion">
                                        <i class="fas fa-chevron-left text-[10px]"></i>
                                    </button>
                                    <button type="button"
                                            @click="stop(); active = (active + 1) % total"
                                            class="w-8 h-8 rounded-full text-white flex items-center justify-center transition-all cursor-pointer"
                                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); z-index: 30; background: rgba(1, 20, 61, 0.68); border: 1px solid rgba(255, 255, 255, 0.3); backdrop-filter: blur(8px);"
                                            aria-label="Next Promotion">
                                        <i class="fas fa-chevron-right text-[10px]"></i>
                                    </button>
                                @endif
                            </div>

                            @if($spotlightCourses->count() > 1)
                                <!-- Visual Mini-Thumbnail Strip (Zero Text, Pure Instant Visual Switch) -->
                                <div class="px-3 py-2 flex items-center justify-center gap-2 bg-[#01143d]/95 border-t border-[#dca22c]/25">
                                    @foreach($spotlightCourses as $sIdx => $sCourse)
                                        <button type="button"
                                                @click="stop(); active = {{ $sIdx }}"
                                                class="relative w-14 h-8 rounded-lg overflow-hidden transition-all cursor-pointer shrink-0"
                                                :class="active === {{ $sIdx }} ? 'ring-2 ring-[#ffe27a] scale-105 opacity-100' : 'opacity-50 hover:opacity-85 ring-1 ring-white/20'"
                                                aria-label="Promo {{ $sIdx + 1 }}">
                                            <img src="{{ $sCourse->cover_image_url }}" alt="" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ asset('uploads/covers/cover_1794680723_6af8a79363ac1.jpeg') }}';" class="w-full h-full object-cover object-center">
                                        </button>
                                    @endforeach
                                </div>
                            @endif

                            @if($heroFacultyCircles->isNotEmpty())
                                <!-- Row 2: Smoothly Sliding Circular Faculty Profiles (Pauses on Hover/Focus) -->
                                <div class="px-3 py-2.5 bg-[#01143d] border-t border-[#dca22c]/35 overflow-hidden">
                                    <div class="flex items-center gap-3 w-max animate-dept-team-slide"
                                         :style="paused ? 'animation-play-state: paused;' : ''">
                                        @for($fLoop = 0; $fLoop < 2; $fLoop++)
                                            @foreach($heroFacultyCircles as $fMember)
                                                @php
                                                    $fFallback = 'https://ui-avatars.com/api/?name=' . urlencode($fMember->name) . '&background=063b96&color=fff';
                                                @endphp
                                                <a href="{{ route('instructors.index') }}"
                                                   title="{{ $fMember->name }}{{ !empty($fMember->title) ? ' — ' . $fMember->title : '' }}"
                                                   class="shrink-0 block rounded-full transition-transform duration-200 hover:scale-110">
                                                    <img src="{{ $fMember->avatar_url }}"
                                                         alt="{{ $fMember->name }}"
                                                         loading="lazy"
                                                         decoding="async"
                                                         width="44"
                                                         height="44"
                                                         onerror="this.onerror=null; this.src='{{ $fFallback }}';"
                                                         class="w-10 h-10 sm:w-11 sm:h-11 rounded-full object-cover object-top border-2 border-[#ffe27a]/80 shadow-md bg-[#021f59]">
                                                </a>
                                            @endforeach
                                        @endfor
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="relative rounded-2xl overflow-hidden aspect-video flex items-center justify-center bg-slate-900 border border-white/15 shadow-2xl group">
                            @php
                                $heroMediaImg = \App\Models\Setting::getVal('landing_hero_media_image', 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=1000&q=80');
                                $heroMediaLink = \App\Models\Setting::getVal('landing_hero_media_link', route('catalog.index'));
                            @endphp
                            <img 
                                src="{{ $heroMediaImg }}" 
                                alt="{{ \App\Models\Setting::getVal('landing_hero_title', 'ACS Academy') }} Executive Training" 
                                class="absolute inset-0 w-full h-full object-cover opacity-85 group-hover:scale-105 transition-transform duration-500"
                                onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=1000&q=80';"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-[#01143d]/70 via-black/20 to-transparent"></div>
                            <a href="{{ $heroMediaLink }}" class="relative z-10 w-14 h-14 rounded-full bg-[#c4040a] text-white flex items-center justify-center shadow-xl border border-white/25" aria-label="Explore Courses">
                                <i class="fas fa-play text-base ml-0.5 text-white"></i>
                            </a>
                        </div>
                    @endif
                </div>

            {{-- 2nd on Mobile / Bottom Row on Desktop: Compact Smoothly Sliding Department Pills --}}
            @if(\App\Models\Setting::getVal('landing_categories_enabled', '1') == '1')
            <div id="home-category" class="lg:col-span-12 order-2 lg:order-3 pt-1 lg:pt-4 lg:border-t lg:border-white/10 overflow-hidden">
                <!-- Mobile: Ultra-Compact Single-Row Smooth Marquee -->
                <div class="lg:hidden overflow-hidden">
                    <div class="flex items-center gap-2 w-max animate-dept-team-slide hover:[animation-play-state:paused]">
                        @for($catLoop = 0; $catLoop < 2; $catLoop++)
                            @foreach(array_slice($runningCategories, 0, 4) as $cat)
                                <a href="{{ route('catalog.category', $cat['slug']) }}"
                                   class="category-nav-pill group flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/[0.08] hover:bg-white/[0.15] border border-[#dca22c]/35 transition-all shrink-0 whitespace-nowrap">
                                    <span class="w-6 h-6 rounded-lg bg-[#c4040a] text-white flex items-center justify-center shrink-0 text-[10px]">
                                        <i class="fas {{ $cat['icon'] }}"></i>
                                    </span>
                                    <span class="text-[11px] font-bold text-white">{{ $cat['name'] }}</span>
                                </a>
                            @endforeach
                        @endfor
                    </div>
                </div>

                <!-- Desktop: 4-Col Grid -->
                <div class="hidden lg:grid lg:grid-cols-4 gap-3.5">
                    @foreach(array_slice($runningCategories, 0, 4) as $cat)
                        <a 
                            href="{{ route('catalog.category', $cat['slug']) }}" 
                            class="category-nav-pill group flex items-center justify-between gap-2.5 p-3.5 rounded-xl bg-white/[0.06] hover:bg-white/[0.12] border border-white/10 hover:border-white/25 transition-all"
                        >
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="w-8 h-8 rounded-lg bg-[#c4040a] text-white flex items-center justify-center shrink-0 text-xs shadow-xs">
                                    <i class="fas {{ $cat['icon'] }}"></i>
                                </span>
                                <span class="text-[13px] font-bold text-white leading-snug">
                                    {{ $cat['name'] }}
                                </span>
                            </div>
                            <i class="fas fa-chevron-right text-[10px] text-slate-400 group-hover:text-white group-hover:translate-x-0.5 transition-all shrink-0"></i>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
            </div>
        </div>
    </section>
    @endif

    {{-- 2. Course Showcase: Immediate Above-the-Fold 10MS Course Grid --}}
    <div id="courses-section-wrapper" class="relative">
        @if(\App\Models\Setting::getVal('landing_courses_enabled', '1') == '1')
        <section class="py-7 sm:py-10 bg-transparent border-b border-[#063b96]/10" x-data="{ activeCourseTab: 'all' }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Compact Section Header + Tabs Bar -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-5 sm:mb-6 gap-3">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-xl sm:text-2xl font-extrabold text-[#021f59] tracking-tight">
                            @php
                                $rawCourseTitle = \App\Models\Setting::getVal('landing_courses_title', 'Courses & Programs');
                                $cleanCourseTitle = ($rawCourseTitle === 'Industry Masterclasses & Courses') ? 'Courses & Programs' : $rawCourseTitle;
                            @endphp
                            {{ $cleanCourseTitle }}
                        </h2>
                        <a href="{{ \App\Models\Setting::getVal('landing_courses_bracket_url', route('catalog.index')) }}" 
                           class="sm:hidden inline-flex items-center gap-1 text-xs font-bold text-[#c4040a] hover:text-[#063b96]">
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
                                        ? 'bg-[#063b96] text-white shadow-xs' 
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
                                        ? 'bg-[#063b96] text-white shadow-xs' 
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
                                        ? 'bg-[#063b96] text-white shadow-xs' 
                                        : 'bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-slate-200'">
                                <span>Day-Long</span>
                                <span class="px-1.5 py-0.2 rounded text-[10px] font-extrabold"
                                      :class="activeCourseTab === 'day_long' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'">
                                    {{ $dayLongCourses->count() }}
                                </span>
                            </button>
                        </div>

                        <a href="{{ \App\Models\Setting::getVal('landing_courses_bracket_url', route('catalog.index')) }}" 
                           class="hidden sm:inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-[#c4040a] hover:text-[#063b96] transition-colors shrink-0 ml-2 group">
                            <span>View All</span>
                            <i class="fas fa-arrow-right text-xs group-hover:translate-x-0.5 transition-transform"></i>
                        </a>
                    </div>
                </div>

                <!-- Unified 4-Column Clean Course Grid (Preserves .bg-[#f0f8ec] for Cypress test hook) -->
                <div class="bg-[#f0f8ec] !bg-transparent">
                    
                    <!-- TAB 1: ALL COURSES (Default) -->
                    <div x-show="activeCourseTab === 'all'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3.5 sm:gap-4">
                            @foreach($courses->take(8) as $course)
                                <x-course-card :course="$course" />
                            @endforeach
                        </div>
                    </div>

                    <!-- TAB 2: CERTIFICATE COURSES -->
                    <div x-show="activeCourseTab === 'certificate'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3.5 sm:gap-4">
                            @foreach($certificateCourses as $course)
                                <x-course-card :course="$course" />
                            @endforeach
                        </div>
                    </div>

                    <!-- TAB 3: DAY LONG COURSES -->
                    <div x-show="activeCourseTab === 'day_long'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3.5 sm:gap-4">
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
    <section id="learning-departments" class="py-8 sm:py-12 bg-white/35 border-b border-[#063b96]/10 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-5 sm:mb-6 pb-3 border-b border-slate-200/80 gap-3">
                <h2 class="text-xl sm:text-2xl font-extrabold text-[#021f59] tracking-tight">
                    4 Learning Departments
                </h2>
                <a href="{{ route('catalog.index') }}" class="text-xs sm:text-sm font-bold text-[#c4040a] hover:text-[#021f59] transition-colors inline-flex items-center gap-1.5 group shrink-0">
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

            <div class="flex overflow-x-auto snap-x snap-mandatory gap-3.5 pb-2 lg:grid lg:grid-cols-4 lg:gap-5 lg:overflow-visible lg:pb-0">
                @foreach($coreDepts as $dIdx => $dept)
                    <div class="w-[80%] sm:w-[46%] lg:w-auto shrink-0 snap-start group rounded-2xl bg-slate-50/60 hover:bg-white border border-slate-200/90 hover:border-[#063b96]/30 p-4 sm:p-5 shadow-2xs hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <!-- Compact Header: Icon + Department Name -->
                            <a href="{{ route('catalog.category', $dept['slug']) }}" class="flex items-center gap-2.5 mb-3">
                                <div class="w-9 h-9 rounded-xl bg-[#063b96] group-hover:bg-[#c4040a] text-white flex items-center justify-center text-sm shrink-0 transition-colors">
                                    <i class="fas {{ $dept['icon'] ?? 'fa-layer-group' }}"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-none mb-0.5">Dept 0{{ $dIdx + 1 }}</span>
                                    <h3 class="text-xs sm:text-sm font-extrabold text-[#021f59] group-hover:text-[#c4040a] transition-colors leading-snug">
                                        {{ $dept['name'] }}
                                    </h3>
                                </div>
                            </a>

                            @php
                                $deptHead = $dept['head'] ?? null;
                                $deptHeadName = !empty($deptHead) ? $deptHead->name : 'Department Head';
                                $deptHeadDefaultAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($deptHeadName) . '&background=051c45&color=fff';
                                $deptHeadAvatar = !empty($deptHead) ? $deptHead->avatar_url : $deptHeadDefaultAvatar;
                                $deptTeamProfiles = collect($dept['team'] ?? [])
                                    ->filter(fn($m) => empty($deptHead) || ($m->id ?? null) !== ($deptHead->id ?? null))
                                    ->values();
                                $needsTeamSlide = $deptTeamProfiles->count() > 5;
                            @endphp

                            <!-- Compact 1-Line Department Head Pill with Real Photo / Avatar Fallback -->
                            <div class="mb-2.5 px-2.5 py-1.5 rounded-xl bg-white border border-slate-200/80 flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <img src="{{ $deptHeadAvatar }}"
                                         alt="{{ $deptHeadName }}"
                                         onerror="this.onerror=null; this.src='{{ $deptHeadDefaultAvatar }}';"
                                         class="w-6 h-6 rounded-full object-cover object-top border border-slate-200 shrink-0 bg-slate-100">
                                    <span class="text-[11px] font-bold text-slate-700 truncate">
                                        {{ $deptHeadName }}
                                    </span>
                                </div>
                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 shrink-0">
                                    + Team{{ $deptTeamProfiles->isNotEmpty() ? ' (' . $deptTeamProfiles->count() . ')' : '' }}
                                </span>
                            </div>

                            <!-- Team Member Profiles Row (Only Avatars; Smooth Slide if > 5, Static Otherwise) -->
                            @if($deptTeamProfiles->isNotEmpty())
                                <div class="mb-3 px-1 overflow-hidden">
                                    @if($needsTeamSlide)
                                        <div class="flex items-center gap-2 w-max animate-dept-team-slide hover:[animation-play-state:paused]">
                                            @for($loopCopy = 0; $loopCopy < 2; $loopCopy++)
                                                @foreach($deptTeamProfiles as $member)
                                                    @php
                                                        $memberFallback = 'https://ui-avatars.com/api/?name=' . urlencode($member->name) . '&background=051c45&color=fff';
                                                    @endphp
                                                    <a href="{{ route('instructors.index') }}"
                                                       title="{{ $member->name }}{{ !empty($member->title) ? ' — ' . $member->title : '' }}"
                                                       class="shrink-0 block rounded-full">
                                                        <img src="{{ $member->avatar_url }}"
                                                             alt="{{ $member->name }}"
                                                             onerror="this.onerror=null; this.src='{{ $memberFallback }}';"
                                                             class="w-7 h-7 rounded-full object-cover object-top border-2 border-white ring-1 ring-slate-200 hover:ring-[#c4040a] transition-all shadow-2xs bg-slate-100">
                                                    </a>
                                                @endforeach
                                            @endfor
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2 flex-wrap">
                                            @foreach($deptTeamProfiles as $member)
                                                @php
                                                    $memberFallback = 'https://ui-avatars.com/api/?name=' . urlencode($member->name) . '&background=051c45&color=fff';
                                                @endphp
                                                <a href="{{ route('instructors.index') }}"
                                                   title="{{ $member->name }}{{ !empty($member->title) ? ' — ' . $member->title : '' }}"
                                                   class="shrink-0 block rounded-full">
                                                    <img src="{{ $member->avatar_url }}"
                                                         alt="{{ $member->name }}"
                                                         onerror="this.onerror=null; this.src='{{ $memberFallback }}';"
                                                         class="w-7 h-7 rounded-full object-cover object-top border-2 border-white ring-1 ring-slate-200 hover:ring-[#c4040a] transition-all shadow-2xs bg-slate-100">
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- 6 Sub-Tracks (Clean & Compact) -->
                            @if(!empty($dept['subcategories']))
                                <ul class="space-y-1 mb-4">
                                    @foreach(array_slice($dept['subcategories'], 0, 3) as $subTrack)
                                        <li>
                                            <a 
                                                href="{{ route('catalog.category', ['category' => $dept['slug'], 'subtrack' => $subTrack]) }}"
                                                class="flex items-center gap-1.5 text-xs text-slate-600 hover:text-[#c4040a] font-medium py-0.5 transition-colors"
                                            >
                                                <i class="fas fa-angle-right text-[#c4040a] text-[10px] shrink-0"></i>
                                                <span class="truncate">{{ $subTrack }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        <a 
                            href="{{ route('catalog.category', $dept['slug']) }}" 
                            class="w-full py-2 px-3 rounded-xl bg-white group-hover:bg-[#063b96] text-[#063b96] group-hover:text-white border border-slate-200/90 group-hover:border-[#063b96] text-xs font-bold flex items-center justify-center gap-1.5 transition-all"
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
    <section class="py-8 sm:py-12 bg-transparent border-b border-[#063b96]/10 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Compact Header Bar -->
            <div class="flex items-center justify-between mb-5 sm:mb-6 pb-3 border-b border-slate-200/80 gap-3">
                <h2 class="text-xl sm:text-2xl font-extrabold text-[#021f59] tracking-tight">
                    @php
                        $rawJobsTitle = \App\Models\Setting::getVal('landing_jobs_title', 'Career Opportunities');
                        $cleanJobsTitle = ($rawJobsTitle === 'Career & Placement Opportunities') ? 'Career Opportunities' : $rawJobsTitle;
                    @endphp
                    {{ $cleanJobsTitle }}
                </h2>
                <a href="{{ \App\Models\Setting::getVal('landing_jobs_btn_url', route('jobs.index')) }}" class="text-xs sm:text-sm font-bold text-[#c4040a] hover:text-[#021f59] transition-colors inline-flex items-center gap-1.5 group shrink-0">
                    <span>View All</span>
                    <i class="fas fa-arrow-right text-xs group-hover:translate-x-0.5 transition-transform"></i>
                </a>
            </div>

            <!-- Compact Executive Career Cards Horizontal Track (Mobile) / Grid (Desktop) -->
            <div class="flex overflow-x-auto snap-x snap-mandatory gap-3 pb-2 md:grid md:grid-cols-3 lg:grid-cols-4 md:gap-4 md:overflow-visible md:pb-0">
                @php
                    $homeJobGradients = [
                        'from-[#063b96] to-[#1e3a8a]',
                        'from-[#7f1d1d] to-[#c4040a]',
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
                    <div class="w-[260px] sm:w-[280px] md:w-auto shrink-0 snap-start bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-[#063b96]/30 transition-all duration-200 flex flex-col justify-between group">
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
                                        <h3 class="text-sm sm:text-base font-extrabold text-[#021f59] group-hover:text-[#c4040a] transition-colors leading-snug line-clamp-1">
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
                                    <i class="fas fa-map-marker-alt text-[#c4040a] text-[9px]"></i>
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

                            <a href="{{ $jUrl }}" @if($jExternal) target="_blank" rel="noopener noreferrer" @endif class="px-3.5 py-1.5 rounded-xl bg-[#063b96] hover:bg-[#c4040a] text-white text-xs font-bold inline-flex items-center gap-1.5 transition-colors shadow-2xs">
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
                    <div class="w-9 h-9 rounded-xl bg-[#021f59] text-white hidden sm:flex items-center justify-center text-sm shrink-0">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div>
                        <h4 class="text-xs sm:text-sm font-extrabold text-[#021f59]">
                            Hiring Commercial or Supply Chain Professionals?
                        </h4>
                        <p class="text-[11px] text-slate-500">
                            Recruit verified ACS Academy alumni directly for your organization.
                        </p>
                    </div>
                </div>
                <a href="{{ route('contact.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-[#063b96] text-[#063b96] hover:text-white text-xs font-bold whitespace-nowrap transition-colors shrink-0">
                    Post a Job →
                </a>
            </div>

        </div>
    </section>
    @endif

    {{-- 6. Executive Counseling & Admissions Hotline Section (Compact Horizontal Bar) --}}
    @if(\App\Models\Setting::getVal('landing_counseling_enabled', '1') == '1')
    <section class="py-8 sm:py-10 bg-white/35 border-b border-[#063b96]/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#063b96] rounded-2xl p-5 sm:p-7 text-white relative overflow-hidden shadow-md border border-white/10 flex flex-col lg:flex-row items-center justify-between gap-5">
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

    {{-- 7. Our Faculty Section (Smooth Row-by-Row Carousel: max 4 per row, max 3 slides / 12 profiles) --}}
    @if(\App\Models\Setting::getVal('landing_faculty_enabled', '1') == '1')
    @php
        $landingInstructors = \App\Models\Instructor::where('is_active', true)->orderBy('order', 'asc')->take(12)->get();
        $facultySlides = $landingInstructors->chunk(4);
        $facultySlideCount = $facultySlides->count();
        $rawFacultyTitle = \App\Models\Setting::getVal('landing_faculty_title', 'Our Faculty');
        $cleanFacultyTitle = ($rawFacultyTitle === 'Distinguished Faculty & Industry Fellows') ? 'Our Faculty' : $rawFacultyTitle;
    @endphp
    @if($landingInstructors->isNotEmpty())
    <section id="faculty" class="py-8 sm:py-12 bg-white/35 border-b border-[#063b96]/10"
             x-data="{
                 activeSlide: 0,
                 totalSlides: {{ $facultySlideCount }},
                 timer: null,
                 inView: false,
                 start() {
                     if (this.totalSlides <= 1 || !this.inView) return;
                     this.stop();
                     this.timer = setInterval(() => {
                         this.activeSlide = (this.activeSlide + 1) % this.totalSlides;
                     }, 2800);
                 },
                 stop() {
                     if (this.timer) {
                         clearInterval(this.timer);
                         this.timer = null;
                     }
                 },
                 prev() {
                     this.stop();
                     this.activeSlide = (this.activeSlide - 1 + this.totalSlides) % this.totalSlides;
                 },
                 next() {
                     this.stop();
                     this.activeSlide = (this.activeSlide + 1) % this.totalSlides;
                 }
             }"
             x-init="
                 const io = new IntersectionObserver((entries) => {
                     entries.forEach(e => {
                         inView = e.isIntersecting;
                         e.isIntersecting ? start() : stop();
                     });
                 }, { threshold: 0.1 });
                 io.observe($el);
             "
             @mouseenter="stop()"
             @mouseleave="start()"
             @focusin="stop()"
             @focusout="start()"
             @touchstart.passive="stop()"
             @touchend.passive="start()"
             @touchcancel.passive="start()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-5 sm:mb-6 pb-3 border-b border-slate-200/80 gap-3">
                <h2 class="text-xl sm:text-2xl font-extrabold text-[#021f59] tracking-tight">
                    {{ $cleanFacultyTitle }}
                </h2>
                <div class="flex items-center gap-3 shrink-0">
                    @if($facultySlideCount > 1)
                        <div class="flex items-center gap-1.5">
                            <button type="button" @click="prev()" aria-label="Previous faculty row"
                                    class="w-7 h-7 sm:w-8 sm:h-8 rounded-full border border-slate-200 bg-white hover:bg-[#021f59] hover:text-white hover:border-[#021f59] text-slate-600 flex items-center justify-center transition-colors cursor-pointer">
                                <i class="fas fa-chevron-left text-[10px]"></i>
                            </button>
                            <button type="button" @click="next()" aria-label="Next faculty row"
                                    class="w-7 h-7 sm:w-8 sm:h-8 rounded-full border border-slate-200 bg-white hover:bg-[#021f59] hover:text-white hover:border-[#021f59] text-slate-600 flex items-center justify-center transition-colors cursor-pointer">
                                <i class="fas fa-chevron-right text-[10px]"></i>
                            </button>
                        </div>
                    @endif
                    <a href="{{ route('instructors.index') }}" class="text-xs sm:text-sm font-bold text-[#c4040a] hover:text-[#021f59] transition-colors inline-flex items-center gap-1.5 group shrink-0">
                        <span>View All</span>
                        <i class="fas fa-arrow-right text-xs group-hover:translate-x-0.5 transition-transform"></i>
                    </a>
                </div>
            </div>

            <div class="overflow-hidden">
                <div class="flex transition-transform duration-500 ease-out will-change-transform"
                     :style="'transform: translateX(-' + (activeSlide * 100) + '%)'">
                    @foreach($facultySlides as $slideInstructors)
                        <div class="w-full shrink-0 grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-5 px-0.5">
                            @foreach($slideInstructors as $inst)
                                <a href="{{ route('instructors.index') }}" class="bg-slate-50/70 hover:bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 text-center shadow-2xs hover:shadow-md transition-all duration-200 group flex flex-col items-center justify-center hover:border-[#063b96]/30">
                                    <div class="relative w-14 h-14 sm:w-16 sm:h-16 mx-auto rounded-full overflow-hidden mb-2.5 border-2 border-white shadow-sm bg-white ring-1 ring-slate-200 group-hover:ring-[#c4040a]/40 transition-all">
                                        <img src="{{ $inst->avatar_url }}" alt="{{ $inst->name }}" class="w-full h-full object-cover object-top" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($inst->name) }}&background=051c45&color=fff';">
                                    </div>
                                    <h3 class="text-xs sm:text-sm font-extrabold text-[#021f59] group-hover:text-[#c4040a] transition-colors leading-snug line-clamp-1 mb-0.5">
                                        {{ $inst->name }}
                                    </h3>
                                    <p class="text-[11px] sm:text-xs text-slate-500 font-semibold line-clamp-1">
                                        {{ $inst->title }}
                                    </p>
                                </a>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            @if($facultySlideCount > 1)
                <div class="flex items-center justify-center gap-2 mt-5">
                    @for($s = 0; $s < $facultySlideCount; $s++)
                        <button type="button"
                                @click="activeSlide = {{ $s }}"
                                aria-label="Show faculty row {{ $s + 1 }}"
                                class="h-1.5 rounded-full transition-all duration-300 cursor-pointer"
                                :class="activeSlide === {{ $s }} ? 'w-6 bg-[#c4040a]' : 'w-2 bg-slate-300 hover:bg-slate-400'">
                        </button>
                    @endfor
                </div>
            @endif
        </div>
    </section>
    @endif
    @endif

    {{-- 8. Our Features Section (Single-Row Smoothly Sliding Strip) --}}
    @if(\App\Models\Setting::getVal('landing_features_enabled', '1') == '1')
    <section id="features" class="py-5 sm:py-7 bg-transparent border-b border-[#063b96]/10 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-3.5 pb-2 border-b border-slate-200/80">
                <h2 class="text-base sm:text-xl font-extrabold text-[#021f59] tracking-tight">
                    {{ \App\Models\Setting::getVal('landing_features_title', 'Why ACS Academy') }}
                </h2>
            </div>

            @php
                $defaultFeatures = [
                    1 => ['icon' => 'fa-graduation-cap', 'title' => 'Industry-Ready Curriculum'],
                    2 => ['icon' => 'fa-chalkboard-teacher', 'title' => 'Veteran Industry Faculty'],
                    3 => ['icon' => 'fa-laptop-house', 'title' => 'Live Interactive Sessions'],
                    4 => ['icon' => 'fa-certificate', 'title' => 'Verified Certification'],
                    5 => ['icon' => 'fa-headset', 'title' => 'Career & Post-Training Support'],
                    6 => ['icon' => 'fa-layer-group', 'title' => '4 Specialized Departments'],
                ];

                $features = [];
                for ($i = 1; $i <= 6; $i++) {
                    $featTitle = \App\Models\Setting::getVal("landing_feature_{$i}_title", $defaultFeatures[$i]['title']);
                    if ($featTitle === 'Distinguished Faculty & Fellows') {
                        $featTitle = 'Veteran Industry Faculty';
                    }
                    $featIcon = \App\Models\Setting::getVal("landing_feature_{$i}_icon", $defaultFeatures[$i]['icon']);
                    if (!empty($featTitle)) {
                        $features[] = [
                            'icon' => str_starts_with($featIcon, 'fa-') ? $featIcon : ('fa-' . $featIcon),
                            'title' => $featTitle,
                        ];
                    }
                }
            @endphp

            <div class="overflow-hidden">
                <div class="flex items-center gap-3 w-max animate-dept-team-slide hover:[animation-play-state:paused]">
                    @for($featLoop = 0; $featLoop < 2; $featLoop++)
                        @foreach($features as $feat)
                            <div class="bg-white/90 backdrop-blur-md px-3.5 py-2.5 rounded-xl border border-[#dca22c]/35 shadow-2xs flex items-center gap-2.5 shrink-0 whitespace-nowrap group hover:border-[#c4040a]/40 transition-colors">
                                <div class="w-8 h-8 rounded-lg bg-[#021f59]/8 text-[#021f59] group-hover:bg-[#c4040a] group-hover:text-white transition-colors flex items-center justify-center text-xs shrink-0">
                                    <i class="fas {{ $feat['icon'] }}"></i>
                                </div>
                                <span class="text-xs sm:text-sm font-extrabold text-[#021f59] group-hover:text-[#c4040a] transition-colors">
                                    {{ $feat['title'] }}
                                </span>
                            </div>
                        @endforeach
                    @endfor
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- 9. Insights & Articles (Compact Blog Section) --}}
    @if(\App\Models\Setting::getVal('landing_blog_enabled', '1') == '1' && isset($recentBlogs) && $recentBlogs->count() > 0)
    <section class="py-8 sm:py-12 bg-white/35 border-b border-[#063b96]/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-5 sm:mb-6 pb-3 border-b border-slate-200/80 gap-3">
                <h2 class="text-xl sm:text-2xl font-extrabold text-[#021f59] tracking-tight">
                    @php
                        $rawBlogTitle = \App\Models\Setting::getVal('landing_blog_title', 'Insights & Articles');
                        $cleanBlogTitle = ($rawBlogTitle === 'Knowledge Hub & Publications') ? 'Insights & Articles' : $rawBlogTitle;
                    @endphp
                    {{ $cleanBlogTitle }}
                </h2>
                <a href="{{ \App\Models\Setting::getVal('landing_blog_btn_url', route('blog.index')) }}" class="text-xs sm:text-sm font-bold text-[#c4040a] hover:text-[#021f59] transition-colors inline-flex items-center gap-1.5 group shrink-0">
                    <span>View All</span>
                    <i class="fas fa-arrow-right text-xs group-hover:translate-x-0.5 transition-transform"></i>
                </a>
            </div>

            <!-- 4-Column Compact Blog Cards Horizontal Track (Mobile) / Grid (Desktop) -->
            <div class="flex overflow-x-auto snap-x snap-mandatory gap-3.5 pb-2 md:grid md:grid-cols-3 lg:grid-cols-4 md:gap-4 md:overflow-visible md:pb-0">
                @foreach($recentBlogs as $blog)
                    <article class="w-[260px] sm:w-[280px] md:w-auto shrink-0 snap-start group bg-white rounded-2xl shadow-2xs hover:shadow-md border border-slate-200/90 hover:border-[#063b96]/30 overflow-hidden transition-all duration-200 flex flex-col h-full">
                        <!-- Cover Image -->
                        <div class="relative h-36 sm:h-40 w-full overflow-hidden bg-slate-900">
                            <a href="{{ route('blog.show', $blog->slug) }}" class="block w-full h-full">
                                <img 
                                   src="{{ $blog->featured_image_url }}" 
                                   alt="{{ $blog->title }}" 
                                   class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                   loading="lazy"
                                   onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=800&q=80';"
                                >
                            </a>
                            <div class="absolute top-2 left-2">
                                <span class="inline-block px-2 py-0.5 bg-[#063b96]/90 text-white rounded-md text-[9px] font-bold uppercase tracking-wider">
                                    {{ $blog->category ?? 'Insights' }}
                                </span>
                            </div>
                        </div>

                        <!-- Article Content -->
                        <div class="p-3.5 flex-1 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center gap-1.5 text-[10px] text-slate-400 font-medium mb-1">
                                    <span>{{ $blog->published_at ? $blog->published_at->format('M d, Y') : date('M d, Y') }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $blog->read_time ?? '5 min' }}</span>
                                </div>

                                <h3 class="text-sm font-extrabold text-[#021f59] group-hover:text-[#c4040a] transition-colors leading-snug mb-1 line-clamp-2">
                                    <a href="{{ route('blog.show', $blog->slug) }}">
                                        {{ $blog->title }}
                                    </a>
                                </h3>

                                <p class="text-[11px] text-slate-500 leading-relaxed line-clamp-2 mb-2.5">
                                    {{ $blog->excerpt }}
                                </p>
                            </div>

                            <div class="border-t border-slate-100 pt-2.5 mt-auto flex items-center justify-between">
                                <span class="text-[11px] font-semibold text-slate-600 truncate max-w-[140px]">
                                    {{ $blog->author_name ?? 'ACS Faculty' }}
                                </span>
                                <a href="{{ route('blog.show', $blog->slug) }}" class="text-xs font-bold text-[#c4040a] group-hover:text-[#021f59] flex items-center gap-1 transition-colors">
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
                    'navy' => 'bg-gradient-to-br from-[#020b1c] via-[#063b96] to-[#0a2540] text-white border-b border-white/10',
                    'slate' => 'bg-[#f8fafc] text-slate-900 border-b border-slate-200/80',
                    default => 'bg-white text-slate-900 border-b border-slate-200/80',
                };
                $secTitleClass = $secTheme === 'navy' ? 'text-white' : 'text-[#063b96]';
                $secSubClass = $secTheme === 'navy' ? 'text-slate-300' : 'text-slate-600';
            @endphp
            <section id="section-{{ $cSec['id'] ?? $loop->index }}" class="py-12 sm:py-16 {{ $secBgClass }}">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="max-w-3xl mx-auto text-center space-y-4">
                        @if(!empty($cSec['badge']))
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider {{ $secTheme === 'navy' ? 'bg-white/10 text-emerald-300 border border-white/15' : 'bg-[#063b96]/8 text-[#063b96]' }}">
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
