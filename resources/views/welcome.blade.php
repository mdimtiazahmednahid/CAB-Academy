@extends('layouts.public')

@section('title', 'Home')

@section('content')
<div>

@if(\App\Models\Setting::getVal('landing_page_mode', 'default') === 'custom' && !empty(\App\Models\Setting::getVal('landing_custom_html')))
    {!! \App\Models\Setting::getVal('landing_custom_html') !!}
@else

{{-- 1. Executive Hero Section (Billion-Dollar Apple/Stripe Grade Canvas) --}}
    @if(\App\Models\Setting::getVal('landing_hero_enabled', '1') == '1')
    <section class="relative min-h-[auto] sm:min-h-[580px] lg:min-h-[640px] flex items-center bg-gradient-to-br from-[#020b18] via-[#041536] via-[#072152] to-[#092b68] text-white py-12 sm:py-20 lg:py-24 overflow-hidden border-b border-slate-800/80">
        <!-- Multi-Stop Ambient Atmospheric Mesh Glows -->
        <div class="absolute -top-32 -left-32 w-[30rem] h-[30rem] bg-gradient-to-tr from-[#c30102]/25 to-[#e11d48]/10 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-[30rem] h-[30rem] bg-gradient-to-tl from-[#10b981]/20 to-[#047857]/10 rounded-full blur-[130px] pointer-events-none"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[42rem] h-[32rem] bg-[#0a3275]/25 rounded-full blur-[140px] pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-14 items-center">
                
                <!-- Left Content: CAB Academy Executive Branding -->
                <div class="lg:col-span-7 space-y-5 sm:space-y-6 text-center lg:text-left">

                    <div class="space-y-2">
                        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight text-white uppercase drop-shadow-[0_4px_16px_rgba(0,0,0,0.6)]">
                            {{ \App\Models\Setting::getVal('landing_hero_title', 'CAB Academy') }}
                        </h1>
                        <div>
                            <h3 class="inline-block bg-[#c30102] text-white px-2.5 sm:px-3.5 py-0.5 sm:py-1 rounded-md sm:rounded-lg text-xl sm:text-3xl lg:text-4xl font-black tracking-tight shadow-[0_6px_20px_rgba(195,1,2,0.35)]">
                                {{ \App\Models\Setting::getVal('landing_hero_tagline', 'Learn, Grow, Lead.') }}
                            </h3>
                        </div>
                    </div>
                    
                    <p class="text-sm sm:text-lg text-slate-200/90 font-normal max-w-xl mx-auto lg:mx-0 leading-relaxed">
                        {{ \App\Models\Setting::getVal('landing_hero_subtitle', 'Online Professional Certification Masterclasses & Corporate Programs.') }}
                    </p>

                    <!-- Call-to-Action Buttons -->
                    <div class="pt-2 flex flex-wrap items-center justify-center lg:justify-start gap-3 sm:gap-4">
                        <a href="{{ \App\Models\Setting::getVal('landing_hero_cta1_url', '#home-category') }}" class="btn-ipdp-primary px-7 sm:px-9 py-3.5 sm:py-4 rounded-xl text-white text-xs sm:text-sm font-black uppercase tracking-wider shadow-[0_10px_25px_-5px_rgba(195,1,2,0.45)] flex items-center justify-center gap-2.5 transition-all hover:scale-105 active:scale-95 flex-1 sm:flex-initial text-center group">
                            <span>{{ \App\Models\Setting::getVal('landing_hero_cta1_text', \App\Models\Setting::getVal('landing_cta_text', 'Explore Courses')) }}</span>
                            <i class="fas fa-arrow-down text-xs group-hover:translate-y-0.5 transition-transform"></i>
                        </a>

                        <a href="{{ \App\Models\Setting::getVal('landing_hero_cta2_url', route('catalog.category', 'export-import')) }}" class="px-7 sm:px-8 py-3.5 sm:py-4 rounded-xl bg-white/10 hover:bg-white/20 backdrop-blur-md text-white border border-white/25 text-xs sm:text-sm font-bold uppercase tracking-wider shadow-lg flex items-center justify-center gap-2 hover:scale-105 active:scale-95 transition-all flex-1 sm:flex-initial text-center">
                            <span>{{ \App\Models\Setting::getVal('landing_hero_cta2_text', 'Export – Import') }}</span>
                            <i class="fas fa-chevron-right text-xs"></i>
                        </a>
                    </div>

                    <!-- Marketing Trust Reassurance Line -->
                    <div class="flex items-center justify-center lg:justify-start gap-4 text-[11px] sm:text-xs text-slate-300/80 pt-1 font-medium">
                        <span class="text-white/20">&bull;</span>
                        <span class="flex items-center gap-1.5"><i class="fas fa-user-graduate text-[#c30102]"></i> {{ \App\Models\Setting::getVal('landing_hero_alumni_text', '10,000+ Corporate Alumni') }}</span>
                    </div>
                </div>

                <!-- Right Video / Visual Showcase (Apple / MasterClass Luxury Acrylic Bezel) -->
                <div class="lg:col-span-5">
                    <div class="relative p-1.5 sm:p-2 rounded-2xl sm:rounded-3xl bg-gradient-to-br from-white/25 via-white/10 to-white/5 backdrop-blur-2xl shadow-[0_30px_70px_-15px_rgba(0,0,0,0.8),0_0_0_1px_rgba(255,255,255,0.2)]">
                        <div class="relative rounded-xl sm:rounded-2xl overflow-hidden aspect-video sm:aspect-[4/3] flex items-center justify-center bg-slate-900 group">
                            @php
                                $heroMediaImg = \App\Models\Setting::getVal('landing_hero_media_image', 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=1000&q=80');
                                $heroMediaLink = \App\Models\Setting::getVal('landing_hero_media_link', route('catalog.index'));
                            @endphp
                            <img 
                                src="{{ $heroMediaImg }}" 
                                alt="{{ \App\Models\Setting::getVal('landing_hero_title', 'CAB Academy') }} Executive Training" 
                                class="absolute inset-0 w-full h-full object-cover opacity-75 group-hover:scale-105 transition-transform duration-700"
                                onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=1000&q=80';"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-[#020b18] via-black/35 to-transparent"></div>
                            
                            <!-- Glowing Concentric Play Button -->
                            <a 
                                href="{{ $heroMediaLink }}" 
                                class="relative z-10 w-14 h-14 sm:w-20 sm:h-20 rounded-full bg-gradient-to-tr from-[#c30102] via-[#e11d48] to-[#ff4d6d] text-white flex items-center justify-center shadow-[0_0_40px_rgba(195,1,2,0.7)] hover:scale-110 hover:shadow-[0_0_55px_rgba(195,1,2,0.95)] transition-all duration-300"
                                aria-label="Explore Courses"
                            >
                                <span class="absolute inset-0 rounded-full bg-[#c30102] animate-ping opacity-25"></span>
                                <i class="fas fa-play text-lg sm:text-2xl ml-0.5 sm:ml-1 text-white relative z-10"></i>
                            </a>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    @endif

{{-- 2. "Our Valuable Clients" (Grid of Corporate Partners) --}}
    @if(\App\Models\Setting::getVal('landing_clients_enabled', '1') == '1')
    <section class="py-10 sm:py-14 bg-gradient-to-b from-slate-50/80 via-white to-slate-50/50 border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-6 sm:mb-8">
                <h2 class="text-lg sm:text-2xl font-black text-[#041536] tracking-tight uppercase">
                    {{ \App\Models\Setting::getVal('landing_clients_title', 'Our Valuable Clients') }}
                </h2>
            </div>

            <div class="grid grid-cols-4 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-8 gap-2.5 sm:gap-6 items-center">
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
                            ['name' => 'SSG', 'logo' => asset('images/clients/ssg.jpg')],
                            ['name' => 'United Hospital', 'logo' => asset('images/clients/united-hospital.jpg')],
                            ['name' => 'Square', 'logo' => asset('images/clients/square.jpg')],
                            ['name' => 'Robi', 'logo' => asset('images/clients/robi.jpg')],
                            ['name' => 'Grameenphone', 'logo' => asset('images/clients/grameenphone.jpg')],
                            ['name' => 'Incepta', 'logo' => asset('images/clients/incepta.jpg')],
                            ['name' => 'IPDC', 'logo' => asset('images/clients/ipdc.jpg')],
                            ['name' => 'Runner', 'logo' => asset('images/clients/runner.jpg')],
                        ];
                    }
                @endphp

                @foreach($clients as $client)
                    <div class="p-2 sm:p-3 bg-white hover:bg-white rounded-xl sm:rounded-2xl border border-slate-200/80 hover:border-slate-300 shadow-[0_2px_8px_rgba(4,21,54,0.04)] hover:shadow-[0_12px_24px_-6px_rgba(4,21,54,0.1)] hover:border-[#041536]/20 transition-all duration-300 group flex items-center justify-center h-16 sm:h-20">
                        @if(!empty($client['logo']))
                            <img 
                                src="{{ $client['logo'] }}" 
                                alt="{{ $client['name'] }}" 
                                class="max-h-8 sm:max-h-12 max-w-full object-contain filter grayscale group-hover:grayscale-0 transition-all duration-300 group-hover:scale-105"
                                loading="lazy"
                            >
                        @else
                            <span class="text-xs sm:text-sm font-bold text-slate-700 tracking-tight text-center">{{ $client['name'] }}</span>
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
        <section id="home-category" class="sticky top-20 z-40 bg-white/90 backdrop-blur-2xl border-y border-slate-200/75 shadow-[0_10px_28px_-6px_rgba(4,21,54,0.08)] py-2.5 transition-all" x-data="{
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
                                class="category-nav-pill shrink-0 inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/90 hover:bg-gradient-to-r hover:from-[#041536] hover:via-[#072152] hover:to-[#041536] text-[#0f172a] hover:text-white border border-slate-200/90 hover:border-[#0a2e6b] text-xs font-bold transition-all duration-300 group shadow-[0_2px_8px_-2px_rgba(4,21,54,0.06)] hover:shadow-[0_8px_20px_-4px_rgba(4,21,54,0.28)] hover:scale-105 active:scale-95 cursor-pointer"
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

        {{-- 4. Course Showcase: Certificate Course & Day Long Course Connected by 'View All Courses' Bracket --}}
        @if(\App\Models\Setting::getVal('landing_courses_enabled', '1') == '1')
        <section class="py-10 sm:py-16 lg:py-20 bg-gradient-to-b from-[#f8fafc] via-[#edf2f7] to-[#f8fafc] border-b border-slate-200/80">
            <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
                
                <!-- Connector Bracket & Centered 'View All Courses' -->
                <div class="relative mb-8 sm:mb-10 lg:mb-14">
                    <div class="relative flex items-center justify-center">
                        <div class="hidden lg:block w-[36%] h-[2px] bg-gradient-to-r from-transparent via-[#041536] via-[#072152] to-[#c30102]"></div>
                        
                        <a href="{{ \App\Models\Setting::getVal('landing_courses_bracket_url', route('catalog.index')) }}" class="px-6 py-3 sm:px-9 sm:py-3.5 bg-gradient-to-b from-white via-white to-slate-50/90 rounded-2xl shadow-[0_8px_30px_rgba(4,21,54,0.1),0_0_0_1px_rgba(4,21,54,0.06)] hover:shadow-[0_14px_40px_rgba(195,1,2,0.2),0_0_0_1px_rgba(195,1,2,0.35)] border border-slate-200/80 hover:border-[#c30102]/30 text-base sm:text-2xl font-black text-[#041536] hover:text-[#c30102] transition-all whitespace-nowrap z-10 mx-2 flex items-center gap-2.5 group hover:scale-[1.02]">
                            <span class="tracking-tight">{{ \App\Models\Setting::getVal('landing_courses_bracket_text', 'View All Courses') }}</span>
                            <span class="w-7 h-7 rounded-full bg-[#c30102]/10 group-hover:bg-[#c30102] flex items-center justify-center transition-all">
                                <i class="fas fa-arrow-right text-xs sm:text-sm text-[#c30102] group-hover:text-white group-hover:translate-x-0.5 transition-all"></i>
                            </span>
                        </a>

                        <div class="hidden lg:block w-[36%] h-[2px] bg-gradient-to-r from-[#c30102] via-[#072152] via-[#041536] to-transparent"></div>
                    </div>

                    <!-- Left and Right vertical drops pointing into each container on LG screens -->
                    <div class="hidden lg:grid grid-cols-2 gap-8 px-12 -mt-[2px]">
                        <div class="flex justify-center">
                            <div class="w-[2px] h-8 bg-gradient-to-b from-[#041536]/60 to-emerald-500/60"></div>
                        </div>
                        <div class="flex justify-center">
                            <div class="w-[2px] h-8 bg-gradient-to-b from-[#c30102]/60 to-[#c30102]/80"></div>
                        </div>
                    </div>
                </div>

                <!-- Dual Side-By-Side Course Containers -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8 items-start">
                    
                    <!-- Left Container: Certificate Course (Light Green Box with Subtle Radiant Depth) -->
                    <div class="bg-[#f0f8ec] bg-gradient-to-br from-[#f2f9ee]/95 via-[#f0f8ec] to-[#e8f5e2]/80 relative rounded-2xl sm:rounded-3xl p-3.5 sm:p-6 lg:p-8 border-2 border-emerald-500/35 shadow-[0_12px_36px_-6px_rgba(16,185,129,0.18)] overflow-hidden flex flex-col justify-between">
                        <div class="absolute -top-24 -right-24 w-72 h-72 bg-emerald-400/12 rounded-full blur-3xl pointer-events-none"></div>

                        <div class="relative z-10">
                            <div class="text-center mb-4 sm:mb-6">
                                <span class="inline-block px-3 py-1 rounded-full bg-emerald-600/10 text-emerald-800 text-[10px] font-black uppercase tracking-widest border border-emerald-500/20 mb-2">
                                    {{ \App\Models\Setting::getVal('landing_cert_badge', 'Executive Master Track') }}
                                </span>
                                <h2 class="text-xl sm:text-2xl lg:text-3xl font-black text-[#041536] tracking-tight">
                                    {{ \App\Models\Setting::getVal('landing_cert_title', 'Certificate Course') }}
                                </h2>
                            </div>

                            <!-- 2-Column Course Grid Inside Box -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-5">
                                @foreach($certificateCourses as $course)
                                    <x-course-card :course="$course" />
                                @endforeach
                            </div>
                        </div>

                        <!-- Bottom Action Button -->
                        <div class="text-center mt-6 sm:mt-8 relative z-10">
                            <a href="{{ \App\Models\Setting::getVal('landing_cert_btn_url', route('catalog.certificate')) }}" class="btn-ipdp-secondary inline-flex items-center justify-center gap-2 px-8 py-3.5 min-h-[46px] rounded-xl text-xs sm:text-sm font-black uppercase tracking-wider shadow-md hover:shadow-xl hover:scale-105 transition-all w-full sm:w-auto">
                                <span>{{ \App\Models\Setting::getVal('landing_cert_btn_text', 'View All Certificate Courses') }}</span>
                                <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Right Container: Day Long Course (Light Peach Box with Subtle Warm Glow) -->
                    <div class="bg-[#fef4f0] bg-gradient-to-br from-[#fff7f5]/95 via-[#fef4f0] to-[#fdebe4]/80 relative rounded-2xl sm:rounded-3xl p-3.5 sm:p-6 lg:p-8 border-2 border-[#c30102]/25 shadow-[0_12px_36px_-6px_rgba(195,1,2,0.16)] overflow-hidden flex flex-col justify-between">
                        <div class="absolute -top-24 -right-24 w-72 h-72 bg-[#c30102]/10 rounded-full blur-3xl pointer-events-none"></div>

                        <div class="relative z-10">
                            <div class="text-center mb-4 sm:mb-6">
                                <span class="inline-block px-3 py-1 rounded-full bg-[#c30102]/10 text-[#c30102] text-[10px] font-black uppercase tracking-widest border border-[#c30102]/20 mb-2">
                                    {{ \App\Models\Setting::getVal('landing_daylong_badge', '1-Day Accelerator') }}
                                </span>
                                <h2 class="text-xl sm:text-2xl lg:text-3xl font-black text-[#041536] tracking-tight">
                                    {{ \App\Models\Setting::getVal('landing_daylong_title', 'Day Long Course') }}
                                </h2>
                            </div>

                            <!-- 2-Column Course Grid Inside Box -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-5">
                                @foreach($dayLongCourses as $course)
                                    <x-course-card :course="$course" />
                                @endforeach
                            </div>
                        </div>

                        <!-- Bottom Action Button -->
                        <div class="text-center mt-6 sm:mt-8 relative z-10">
                            <a href="{{ \App\Models\Setting::getVal('landing_daylong_btn_url', route('catalog.day_long')) }}" class="btn-ipdp-primary inline-flex items-center justify-center gap-2 px-8 py-3.5 min-h-[46px] rounded-xl text-xs sm:text-sm font-black uppercase tracking-wider shadow-md hover:shadow-xl hover:scale-105 transition-all w-full sm:w-auto">
                                <span>{{ \App\Models\Setting::getVal('landing_daylong_btn_text', 'View All Day Long Courses') }}</span>
                                <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </section>
        @endif
    </div>

    {{-- 5. Executive Insights & Knowledge Hub (Blog Section) --}}
    @if(\App\Models\Setting::getVal('landing_blog_enabled', '1') == '1')
    <section class="py-10 sm:py-16 lg:py-20 bg-gradient-to-b from-[#f8fafc] via-[#f4f7fb] to-[#f8fafc] border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 sm:mb-12 pb-4 border-b border-slate-200/80 gap-3">
                <div>
                    <h2 class="text-xl sm:text-2xl lg:text-3xl font-black text-[#041536] tracking-tight">
                        {{ \App\Models\Setting::getVal('landing_blog_title', 'Knowledge Hub & Publications') }}
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 font-medium">
                        {{ \App\Models\Setting::getVal('landing_blog_subtitle', 'Thought leadership, customs directives, tax reforms, and supply chain insights by CAB Academy Distinguished Faculty.') }}
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
                    <article class="group bg-white rounded-2xl sm:rounded-3xl shadow-[0_4px_16px_rgba(4,21,54,0.06)] hover:shadow-[0_20px_45px_-10px_rgba(4,21,54,0.16)] border border-slate-200/90 hover:border-[#c30102]/30 overflow-hidden transition-all duration-300 flex flex-col h-full hover:-translate-y-1.5">
                        <!-- Cover Image -->
                        <div class="relative aspect-video w-full overflow-hidden bg-slate-900">
                            <a href="{{ route('blog.show', $blog->slug) }}">
                                @php
                                    $coverUrl = $blog->cover_image ? asset($blog->cover_image) : ($defaultBlogImgs[$loop->index % 3]);
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
                                    <span>{{ $blog->reading_time ?? '5 min' }} read</span>
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
                                        @if($blog->author && $blog->author->profile_picture)
                                            <img src="{{ asset($blog->author->profile_picture) }}" alt="{{ $blog->author_name }}" class="w-full h-full object-cover">
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

    {{-- 6. Executive Counseling & Admissions Hotline Section --}}
    @if(\App\Models\Setting::getVal('landing_counseling_enabled', '1') == '1')
    <section class="py-12 sm:py-20 bg-gradient-to-r from-[#022b1e] via-[#045037] via-[#066345] to-[#033726] relative overflow-hidden text-white shadow-2xl">
        <!-- Ambient Jewel Glows -->
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-emerald-400/15 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-amber-400/10 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
            <h2 class="text-2xl sm:text-4xl font-black mb-3 text-white tracking-tight">
                {{ \App\Models\Setting::getVal('landing_counseling_title', 'Need To Learn More ?') }}
            </h2>
            <p class="text-xs sm:text-base text-emerald-100/90 max-w-2xl mx-auto mb-6 sm:mb-8 font-normal leading-relaxed">
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
                <a href="tel:{{ $phoneTel }}" class="btn-ipdp-primary px-7 sm:px-8 py-3.5 sm:py-4 rounded-xl font-black text-xs sm:text-sm shadow-[0_10px_25px_rgba(195,1,2,0.45)] flex items-center justify-center gap-2 hover:scale-105 transition-all w-full sm:w-auto">
                    <i class="fas fa-phone-alt"></i>
                    <span>{{ $phoneLabel }}</span>
                </a>
                <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener noreferrer" class="bg-[#25D366] hover:bg-[#20ba5a] text-white px-7 sm:px-8 py-3.5 sm:py-4 rounded-xl font-black text-xs sm:text-sm shadow-xl transition-all duration-300 flex items-center justify-center gap-2 hover:scale-105 w-full sm:w-auto">
                    <i class="fab fa-whatsapp text-base"></i>
                    <span>{{ $waLabel }}</span>
                </a>
                <a href="{{ $btn3Url }}" class="bg-white text-[#041536] hover:bg-slate-100 px-7 sm:px-8 py-3.5 sm:py-4 rounded-xl font-black text-xs sm:text-sm shadow-xl transition-all duration-300 flex items-center justify-center gap-2 hover:scale-105 w-full sm:w-auto">
                    <span>{{ $btn3Text }}</span>
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>
    </section>
    @endif

    {{-- 7. Our Features Section (Distinguished Faculty & Fellows) --}}
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
                    <div class="bg-white p-5 sm:p-7 rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-[0_2px_12px_rgba(4,21,54,0.04)] hover:shadow-[0_18px_40px_-8px_rgba(4,21,54,0.14)] hover:border-[#c30102]/30 transition-all duration-300 group hover:-translate-y-1.5 flex flex-col justify-between">
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

    {{-- 8. Student Career & Job Placement Section (Preserving Platform Job Board Feature) --}}
    @if(\App\Models\Setting::getVal('landing_jobs_enabled', '1') == '1' && isset($jobs) && $jobs->count() > 0)
        <section class="py-16 bg-gradient-to-b from-[#f8fafc] via-[#edf2f7] to-[#f8fafc]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10 pb-4 border-b border-slate-200">
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-black text-[#051c45] tracking-tight">
                            {{ \App\Models\Setting::getVal('landing_jobs_title', 'Career & Placement Opportunities') }}
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1 font-medium">
                            {{ \App\Models\Setting::getVal('landing_jobs_subtitle', 'Explore openings posted exclusively for CAB Academy certified professionals') }}
                        </p>
                    </div>
                    <a href="{{ \App\Models\Setting::getVal('landing_jobs_btn_url', route('jobs.index')) }}" class="mt-4 sm:mt-0 text-sm font-bold text-[#c30102] hover:text-[#051c45] transition-colors inline-flex items-center gap-1 group">
                        <span>{{ \App\Models\Setting::getVal('landing_jobs_btn_text', 'Browse All Openings') }}</span>
                        <i class="fas fa-chevron-right text-xs group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($jobs as $job)
                        <div class="bg-slate-50 p-6 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-lg transition-all flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-[#c30102]/10 text-[#c30102]">
                                        {{ $job->job_type ?? 'Full-time' }}
                                    </span>
                                    <span class="text-xs text-slate-500">
                                        {{ $job->location ?? 'Dhaka' }}
                                    </span>
                                </div>
                                <h4 class="text-base font-bold text-[#051c45] mb-1">
                                    {{ $job->title }}
                                </h4>
                                <p class="text-xs text-slate-500 font-semibold mb-3">
                                    {{ $job->company_name ?? 'Partner Enterprise' }}
                                </p>
                                <p class="text-xs text-slate-600 line-clamp-2 mb-4">
                                    {{ $job->description }}
                                </p>
                            </div>
                            <a href="{{ route('jobs.index') }}" class="btn-ipdp-primary text-center py-2.5 rounded-xl text-xs font-bold w-full shadow-md">
                                View Details & Apply
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

@endif

</div>
@endsection
