<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Course Catalog') - {{ \App\Models\Setting::getVal('site_name', 'CAB Academy') }}</title>
    <link rel="icon" href="{{ \App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png') }}">

    <!-- Canonical URL -->
    <link rel="canonical" href="@yield('canonical_url', url()->current())">

    <!-- Search Engine Indexing & Crawl Directives -->
    <meta name="robots" content="@yield('robots', 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1')">
    <meta name="googlebot" content="@yield('robots', 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1')">
    <meta name="bingbot" content="@yield('robots', 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1')">

    <!-- Meta Description & Keywords -->
    <meta name="description" content="@yield('meta_description', 'CAB Academy is Bangladesh\'s premier professional training institute offering executive certifications in Commercial Banking, Export-Import (EXIM) Procedures, Letter of Credit (LC) Operations, Customs Clearance, and Supply Chain Management in Dhaka.')">
    <meta name="keywords" content="@yield('meta_keywords', 'Commercial Banking course Dhaka, Export Import training Bangladesh, Letter of Credit LC course Dhaka, Customs clearance training NBR, Supply chain certification Dhaka, professional training Mirpur Pallabi, CAB Academy')">
    <meta name="author" content="CAB Academy">

    <!-- Geo & Local SEO Tags (Dhaka, Bangladesh) -->
    <meta name="geo.region" content="BD-13">
    <meta name="geo.placename" content="Dhaka, Bangladesh">
    <meta name="geo.position" content="23.8223;90.3654">
    <meta name="ICBM" content="23.8223, 90.3654">

    <!-- Open Graph (Facebook, LinkedIn, Social Previews) -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="{{ \App\Models\Setting::getVal('site_name', 'CAB Academy') }}">
    <meta property="og:locale" content="en_US">
    <meta property="og:title" content="@yield('og_title', View::hasSection('title') ? View::getSection('title') . ' - CAB Academy' : 'CAB Academy | Commercial Banking & Trade Training')">
    <meta property="og:description" content="@yield('meta_description', 'CAB Academy is Bangladesh\'s premier professional training institute offering executive certifications in Commercial Banking, Export-Import Procedures, and Supply Chain Operations.')">
    <meta property="og:url" content="@yield('canonical_url', url()->current())">
    @php
        $rawOgImg = trim(View::getSection('og_image') ?: (\App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png')));
        $fullOgImg = (str_starts_with($rawOgImg, 'http://') || str_starts_with($rawOgImg, 'https://')) ? $rawOgImg : url($rawOgImg);
    @endphp
    <meta property="og:image" content="{{ $fullOgImg }}">
    <meta property="og:image:secure_url" content="{{ $fullOgImg }}">
    <meta property="og:image:width" content="@yield('og_image_width', '1200')">
    <meta property="og:image:height" content="@yield('og_image_height', '630')">
    <meta property="og:image:alt" content="@yield('og_image_alt', \App\Models\Setting::getVal('site_name', 'CAB Academy'))">
    <meta property="og:image:type" content="image/jpeg">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', View::hasSection('title') ? View::getSection('title') . ' - CAB Academy' : 'CAB Academy | Commercial Banking & Trade Training')">
    <meta name="twitter:description" content="@yield('meta_description', 'CAB Academy is Bangladesh\'s premier professional training institute offering executive certifications in Commercial Banking, Export-Import Procedures, and Supply Chain Operations.')">
    <meta name="twitter:image" content="{{ $fullOgImg }}">
    <meta name="twitter:image:alt" content="@yield('og_image_alt', \App\Models\Setting::getVal('site_name', 'CAB Academy'))">

    <!-- Schema.org EducationalOrganization JSON-LD -->
    @php
        $siteLogoUrl = \App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png');
        $siteNameVal = \App\Models\Setting::getVal('site_name', 'CAB Academy');
        $orgSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'EducationalOrganization',
            'name' => $siteNameVal,
            'alternateName' => 'Commercial Academy Bangladesh',
            'url' => url('/'),
            'logo' => $siteLogoUrl,
            'image' => $siteLogoUrl,
            'description' => "CAB Academy is Bangladesh's premier executive training and professional certification academy for commercial banking, export-import procedures, LC operations, and supply chain management.",
            'telephone' => '+8801917054664',
            'email' => 'info@cab-academy.com',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => 'House 28, Road 2, Block B, Pallabi, Mirpur 12',
                'addressLocality' => 'Dhaka',
                'postalCode' => '1216',
                'addressCountry' => 'BD',
            ],
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => 23.8223,
                'longitude' => 90.3654,
            ],
            'sameAs' => [
                'https://www.facebook.com/cabacademybd',
                'https://www.linkedin.com/company/cab-academy-bd',
            ],
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => url('/catalog') . '?q={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    @endphp
    <script type="application/ld+json">
    {!! json_encode($orgSchema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
    @yield('schema')

    <!-- Google Fonts: Inter & Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind & App JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --color-content-primary: {{ \App\Models\Setting::getVal('primary_color', '#c30102') }};
            --color-content-secondary: #041536;
            --color-content-accent: #10b981;
            --color-content-bg-alt: #f8fafc;
            --color-content-heading: #041536;
            --color-content-text: #334155;
            --color-content-faded: #64748b;
            --color-content-border: #e2e8f0;
            --color-footer-bg: #020b18;
            --color-subfooter-bg: #01060e;
        }

        body {
            font-family: 'Inter', 'Poppins', sans-serif;
            background-color: var(--color-content-bg-alt);
            color: var(--color-content-text);
        }

        .bg-cab-primary, .bg-ipdp-primary { background-color: #c30102 !important; }
        .bg-cab-secondary, .bg-ipdp-secondary { background-color: #041536 !important; }
        .text-cab-primary, .text-ipdp-primary { color: #c30102 !important; }
        .text-cab-secondary, .text-ipdp-secondary { color: #041536 !important; }
        .border-cab-primary, .border-ipdp-primary { border-color: #c30102 !important; }
        .border-cab-secondary, .border-ipdp-secondary { border-color: #041536 !important; }

        /* Ultra-Luxury Billion-Dollar Crimson & Obsidian Navy Buttons */
        .btn-cab-primary, .btn-ipdp-primary {
            background: linear-gradient(135deg, #c30102 0%, #d8182b 40%, #b00102 85%, #8f0102 100%) !important;
            color: #ffffff !important;
            font-weight: 800;
            border-radius: 0.75rem;
            transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 16px -2px rgba(195, 1, 2, 0.38), 0 2px 6px -1px rgba(195, 1, 2, 0.25), inset 0 1px 0 rgba(255, 255, 255, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.15);
            letter-spacing: 0.025em;
        }
        .btn-cab-primary:hover, .btn-ipdp-primary:hover {
            background: linear-gradient(135deg, #a50102 0%, #c30102 50%, #880102 100%) !important;
            transform: translateY(-1.5px);
            box-shadow: 0 10px 28px -2px rgba(195, 1, 2, 0.55), 0 4px 12px -2px rgba(195, 1, 2, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.38);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .btn-cab-secondary, .btn-ipdp-secondary {
            background: linear-gradient(135deg, #041536 0%, #072152 45%, #051c45 80%, #020b18 100%) !important;
            color: #ffffff !important;
            font-weight: 800;
            border-radius: 0.75rem;
            transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 16px -2px rgba(4, 21, 54, 0.35), 0 2px 6px -1px rgba(4, 21, 54, 0.22), inset 0 1px 0 rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.12);
            letter-spacing: 0.025em;
        }
        .btn-cab-secondary:hover, .btn-ipdp-secondary:hover {
            background: linear-gradient(135deg, #020b18 0%, #041536 50%, #01060e 100%) !important;
            transform: translateY(-1.5px);
            box-shadow: 0 10px 28px -2px rgba(4, 21, 54, 0.52), 0 4px 12px -2px rgba(4, 21, 54, 0.32), inset 0 1px 0 rgba(255, 255, 255, 0.28);
            border-color: rgba(255, 255, 255, 0.25);
        }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col selection:bg-[#c30102] selection:text-white" x-data="{ searchModalOpen: false }">

    <!-- 0. Top Minimalist Contact Bar (Zero Fluff, Pure Utility) -->
    <div class="bg-gradient-to-r from-[#020b18] via-[#041536] to-[#072152] text-white py-2 px-4 text-xs font-medium border-b border-white/10 hidden sm:block relative z-50">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs text-slate-300 font-medium">
                <i class="fas fa-map-marker-alt text-[#c30102] text-[11px]"></i>
                <a href="{{ route('contact.index') }}" class="hover:text-white transition-colors">
                    {{ \App\Models\Setting::getVal('contact_address', 'Mirpur-Pallabi, Dhaka-1216') }}
                </a>
            </div>
            <div class="flex items-center gap-4 text-xs text-slate-300">
                @php
                    $topHotline = \App\Models\Setting::getVal('contact_hotline', '01917-054664');
                    $topHotlineTel = preg_replace('/[^0-9+]/', '', $topHotline) ?: '01917054664';
                    $topWhatsapp = \App\Models\Setting::getVal('contact_whatsapp', '8801917054664');
                @endphp
                <a href="tel:{{ $topHotlineTel }}" class="hover:text-white flex items-center gap-1.5 transition-colors font-semibold">
                    <i class="fas fa-phone-alt text-[#c30102]"></i>
                    <span>{{ $topHotline }}</span>
                </a>
                <span class="text-white/30">&bull;</span>
                <a href="https://wa.me/{{ $topWhatsapp }}" target="_blank" rel="noopener noreferrer" class="text-emerald-400 hover:text-emerald-300 flex items-center gap-1.5 transition-colors font-semibold">
                    <i class="fab fa-whatsapp text-sm"></i>
                    <span>WhatsApp Support</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-xl border-b border-slate-200/80 shadow-[0_4px_25px_-5px_rgba(5,28,69,0.06)] transition-all">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 w-full">
            <div class="flex justify-between items-center h-16 sm:h-20 gap-2 lg:gap-4">
                
                <!-- CAB Academy Brand Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-2 sm:gap-3 shrink-0 group">
                    <div class="relative">
                        <img class="h-10 w-10 sm:h-12 sm:w-12 xl:h-13 xl:w-13 object-contain rounded-full shadow-sm bg-white border border-slate-100 ring-2 ring-slate-100 group-hover:ring-[#c30102]/30 group-hover:scale-105 transition-all duration-300" 
                             src="{{ \App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png') }}" 
                             alt="{{ \App\Models\Setting::getVal('site_name', 'CAB Academy') }}"
                             onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}';">
                    </div>
                    <div class="flex flex-col">
                        <span class="text-base sm:text-lg xl:text-xl font-black tracking-tight text-[#041536] uppercase leading-none">{{ \App\Models\Setting::getVal('site_name', 'CAB Academy') }}</span>
                        <span class="text-[8px] sm:text-[9.5px] tracking-widest uppercase font-black text-[#c30102] mt-0.5">{{ \App\Models\Setting::getVal('site_tagline', \App\Models\Setting::getVal('landing_hero_tagline', 'Learn, Grow, Lead.')) }}</span>
                    </div>
                </a>

                <!-- Desktop Navigation Menu (Careers & Jobs Prominently Featured) -->
                <nav class="hidden lg:flex items-center gap-0.5 xl:gap-1.5 2xl:gap-3 text-xs xl:text-[13.5px] 2xl:text-[14.5px] font-semibold text-[#041536]" x-data="{ courseDropdown: false, communityDropdown: false }">
                    <a href="{{ url('/') }}" class="px-2 xl:px-2.5 py-1.5 rounded-lg hover:text-[#c30102] hover:bg-slate-50 transition-all whitespace-nowrap {{ request()->is('/') ? 'text-[#c30102] font-bold bg-slate-50/80' : '' }}">
                        <span>Home</span>
                    </a>

                    <!-- Course Dropdown -->
                    <div class="relative py-2" @mouseenter="courseDropdown = true" @mouseleave="courseDropdown = false" @click.away="courseDropdown = false">
                        <a href="{{ route('catalog.index') }}" @click="if (window.innerWidth < 1280) { $event.preventDefault(); courseDropdown = !courseDropdown; }" class="flex items-center gap-1 px-2 xl:px-2.5 py-1.5 rounded-lg hover:text-[#c30102] hover:bg-slate-50 transition-all whitespace-nowrap {{ request()->is('catalog*') || request()->is('courses*') ? 'text-[#c30102] font-bold bg-slate-50/80' : '' }}">
                            <span>Courses</span>
                            <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="courseDropdown ? 'rotate-180 text-[#c30102]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </a>
                        <!-- Submenu -->
                        <div 
                            x-show="courseDropdown" 
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                            x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                            class="absolute top-full left-0 w-64 bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl border border-slate-200/80 p-2 z-50 pt-2"
                            style="display: none;"
                        >
                            <a href="{{ route('catalog.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl text-xs sm:text-sm hover:bg-slate-50 hover:text-[#c30102] transition-colors font-semibold text-slate-800">
                                <i class="fas fa-layer-group text-xs text-[#c30102]"></i>
                                <span>All Courses</span>
                            </a>
                            @php
                                $publicCertCount = 0;
                                $publicDayLongCount = 0;
                                try {
                                    $publicCertCount = \App\Models\Course::where('is_published', true)->where(function($q) {
                                        $q->where('level', 'Certificate')->orWhere('title', 'like', '%Certificate%');
                                    })->count();
                                    $publicDayLongCount = \App\Models\Course::where('is_published', true)->where(function($q) {
                                        $q->where('level', 'Day Long')
                                          ->orWhere('duration', 'like', '%Day%')
                                          ->orWhere('title', 'like', '%Day-Long%')
                                          ->orWhere('title', 'like', '%Day Long%')
                                          ->orWhere('title', 'like', '%Masterclass%');
                                    })->count();
                                } catch (\Throwable $e) {
                                    $publicCertCount = 0;
                                    $publicDayLongCount = 0;
                                }
                            @endphp
                            <a href="{{ route('catalog.certificate') }}" class="flex items-center justify-between gap-2.5 px-4 py-2.5 rounded-xl text-xs sm:text-sm hover:bg-slate-50 hover:text-[#c30102] transition-colors font-semibold text-slate-800">
                                <span class="flex items-center gap-2.5">
                                    <i class="fas fa-award text-xs text-[#051c45]"></i>
                                    <span>Certificate Programs</span>
                                </span>
                                @if($publicCertCount > 0)
                                    <span class="text-[10px] px-2 py-0.5 rounded-full font-bold bg-slate-100 text-slate-700">{{ $publicCertCount }}</span>
                                @endif
                            </a>
                            <a href="{{ route('catalog.day_long') }}" class="flex items-center justify-between gap-2.5 px-4 py-2.5 rounded-xl text-xs sm:text-sm hover:bg-slate-50 hover:text-[#c30102] transition-colors font-semibold text-slate-800">
                                <span class="flex items-center gap-2.5">
                                    <i class="fas fa-calendar-day text-xs text-[#10b981]"></i>
                                    <span>Day Long Masterclasses</span>
                                </span>
                                @if($publicDayLongCount > 0)
                                    <span class="text-[10px] px-2 py-0.5 rounded-full font-bold bg-slate-100 text-slate-700">{{ $publicDayLongCount }}</span>
                                @endif
                            </a>
                            @php
                                $publicNavCategories = collect();
                                try {
                                    $publicNavCategories = \App\Models\Category::active()->ordered()->take(5)->get();
                                    if ($publicNavCategories->isEmpty()) {
                                        $fallbackCatNames = \App\Models\Course::where('is_published', true)->whereNotNull('category')->where('category', '!=', '')->distinct()->pluck('category')->take(5);
                                        $publicNavCategories = $fallbackCatNames->map(function($name) {
                                            return (object)['name' => $name, 'slug' => \Illuminate\Support\Str::slug($name), 'icon' => 'fa-tag'];
                                        });
                                    }
                                } catch (\Throwable $e) {
                                    $publicNavCategories = collect();
                                }
                            @endphp
                            @if($publicNavCategories->isNotEmpty())
                                <div class="border-t border-slate-100 my-1"></div>
                                @foreach($publicNavCategories as $pCat)
                                    <a href="{{ route('catalog.category', $pCat->slug) }}" class="flex items-center gap-2.5 px-4 py-2 rounded-xl text-xs hover:bg-slate-50 hover:text-[#c30102] transition-colors font-medium text-slate-600">
                                        <i class="fas {{ $pCat->icon ?? 'fa-tag' }} text-[11px] text-slate-400"></i>
                                        <span>{{ $pCat->name }}</span>
                                    </a>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- Career & Placement Opportunities (Front & Center!) -->
                    <a href="{{ route('jobs.index') }}" class="flex items-center gap-1.5 px-2 xl:px-2.5 py-1.5 rounded-lg hover:text-[#c30102] hover:bg-slate-50 transition-all whitespace-nowrap {{ request()->is('jobs*') ? 'text-[#c30102] font-bold bg-slate-50/80' : '' }}">
                        <i class="fas fa-briefcase text-xs text-[#051c45]"></i>
                        <span>Careers & Jobs</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[9px] bg-emerald-500 text-white font-black uppercase tracking-wider shadow-sm">Hiring</span>
                    </a>

                    <!-- Instructors Directory -->
                    <a href="{{ route('instructors.index') }}" class="px-2 xl:px-2.5 py-1.5 rounded-lg hover:text-[#c30102] hover:bg-slate-50 transition-all whitespace-nowrap {{ request()->is('instructors*') || request()->is('faculty*') ? 'text-[#c30102] font-bold bg-slate-50/80' : '' }}">
                        <span>Instructors</span>
                    </a>

                    <!-- About Us -->
                    <a href="{{ route('about.index') }}" class="px-2 xl:px-2.5 py-1.5 rounded-lg hover:text-[#c30102] hover:bg-slate-50 transition-all whitespace-nowrap {{ request()->routeIs('about.*') ? 'text-[#c30102] font-bold bg-slate-50/80' : '' }}">
                        <span>About</span>
                    </a>

                    <!-- Contact -->
                    <a href="{{ route('contact.index') }}" class="px-2 xl:px-2.5 py-1.5 rounded-lg hover:text-[#c30102] hover:bg-slate-50 transition-all whitespace-nowrap {{ request()->routeIs('contact.*') ? 'text-[#c30102] font-bold bg-slate-50/80' : '' }}">
                        <span>Contact</span>
                    </a>

                    <!-- Community & Insights Dropdown (Feed, Blog, Verification, Policies) -->
                    <div class="relative py-2" @mouseenter="communityDropdown = true" @mouseleave="communityDropdown = false" @click.away="communityDropdown = false">
                        <button type="button" @click="communityDropdown = !communityDropdown" class="flex items-center gap-1 px-2 xl:px-2.5 py-1.5 rounded-lg hover:text-[#c30102] hover:bg-slate-50 transition-all whitespace-nowrap {{ request()->is('feed*') || request()->is('blog*') || request()->is('verify-certificate*') ? 'text-[#c30102] font-bold bg-slate-50/80' : '' }}">
                            <span>Community & Insights</span>
                            <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="communityDropdown ? 'rotate-180 text-[#c30102]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div 
                            x-show="communityDropdown" 
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                            x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                            class="absolute top-full right-0 w-60 bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl border border-slate-200/80 p-2 z-50 pt-2"
                            style="display: none;"
                        >
                            <a href="{{ route('feed.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl text-xs sm:text-sm hover:bg-slate-50 hover:text-[#c30102] transition-colors font-semibold text-slate-800">
                                <i class="fas fa-comments text-xs text-[#051c45]"></i>
                                <span>Community Discussion Feed</span>
                            </a>
                            <a href="{{ route('blog.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl text-xs sm:text-sm hover:bg-slate-50 hover:text-[#c30102] transition-colors font-semibold text-slate-800">
                                <i class="fas fa-book-reader text-xs text-[#c30102]"></i>
                                <span>Knowledge Hub & Blogs</span>
                            </a>
                            <a href="{{ route('certificate.verify') }}" class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl text-xs sm:text-sm hover:bg-slate-50 hover:text-[#c30102] transition-colors font-semibold text-slate-800">
                                <i class="fas fa-shield-alt text-xs text-[#10b981]"></i>
                                <span>Verify Certificate</span>
                            </a>
                            <div class="border-t border-slate-100 my-1"></div>
                            <a href="{{ route('legal.privacy') }}" class="flex items-center gap-2.5 px-4 py-2 rounded-xl text-xs hover:bg-slate-50 hover:text-[#c30102] transition-colors font-medium text-slate-600">
                                <i class="fas fa-user-shield text-[11px] text-slate-400"></i>
                                <span>Privacy Policy</span>
                            </a>
                            <a href="{{ route('legal.terms') }}" class="flex items-center gap-2.5 px-4 py-2 rounded-xl text-xs hover:bg-slate-50 hover:text-[#c30102] transition-colors font-medium text-slate-600">
                                <i class="fas fa-file-contract text-[11px] text-slate-400"></i>
                                <span>Terms of Service</span>
                            </a>
                            <a href="{{ route('legal.refund') }}" class="flex items-center gap-2.5 px-4 py-2 rounded-xl text-xs hover:bg-slate-50 hover:text-[#c30102] transition-colors font-medium text-slate-600">
                                <i class="fas fa-undo-alt text-[11px] text-slate-400"></i>
                                <span>Refund Policy</span>
                            </a>
                        </div>
                    </div>
                </nav>

                <!-- Actions: Wishlist Heart, Auth, Smooth Search (Responsive at all screen sizes) -->
                <div class="hidden sm:flex items-center gap-2 xl:gap-3 shrink-0">
                    <!-- Quick Expanding Search Pill -->
                    <div class="relative hidden lg:block" x-data="{ expanded: false }">
                        <form action="{{ route('catalog.index') }}" method="GET" class="relative flex items-center">
                            <input 
                                type="text" 
                                name="search" 
                                placeholder="Search..." 
                                @focus="expanded = true" 
                                @blur="expanded = false"
                                :class="expanded ? 'w-40 xl:w-52 ring-2 ring-[#c30102]/20 border-[#c30102] bg-white' : 'w-24 xl:w-32 bg-slate-100/90 border-slate-200'"
                                class="transition-all duration-300 text-xs py-1.5 xl:py-2 pl-7 xl:pl-8 pr-2.5 rounded-xl border text-slate-800 outline-none placeholder:text-slate-400 font-medium"
                            >
                            <i class="fas fa-search text-[11px] text-slate-400 absolute left-2.5 pointer-events-none"></i>
                        </form>
                    </div>

                    <!-- Wishlist Heart Icon with dynamic badge -->
                    <a href="{{ route('wishlist.index') }}" title="Saved Wishlist" class="relative text-[#c30102] p-1.5 xl:p-2 hover:scale-110 transition-transform">
                        <i class="fas fa-heart text-base xl:text-lg"></i>
                        <span id="wishlist-count-badge" class="absolute -top-0.5 -right-0.5 bg-[#c30102] text-white text-[9px] font-black w-4 h-4 rounded-full flex items-center justify-center shadow {{ auth()->check() && auth()->user()->wishlists()->count() > 0 ? '' : 'hidden' }}">
                            {{ auth()->check() ? auth()->user()->wishlists()->count() : 0 }}
                        </span>
                    </a>

                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-ipdp-primary px-3 xl:px-4 py-2 xl:py-2.5 rounded-xl text-xs xl:text-sm font-bold shadow-md flex items-center gap-1.5 whitespace-nowrap">
                            <i class="far fa-user text-xs"></i>
                            <span>Dashboard</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-ipdp-primary px-3 xl:px-4 py-2 xl:py-2.5 rounded-xl text-xs xl:text-sm font-bold shadow-md flex items-center gap-1.5 whitespace-nowrap">
                            <i class="fas fa-lock text-xs"></i>
                            <span>Sign In</span>
                        </a>
                    @endauth
                </div>

                <!-- Mobile & Tablet Hamburger Toggle -->
                <div class="lg:hidden flex items-center" x-data="{ open: false }">
                    <button @click="open = !open" class="p-2 text-slate-700 hover:text-[#c30102] focus:outline-none" aria-label="Toggle navigation">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <!-- Mobile Drawer -->
                    <div x-show="open" @click.away="open = false" x-transition class="absolute top-16 sm:top-20 left-0 w-full bg-white border-b border-slate-200 shadow-[0_25px_50px_-12px_rgba(4,21,54,0.25)] p-5 space-y-3 z-50 text-sm font-semibold max-h-[85vh] overflow-y-auto" style="display: none;">
                        <!-- Quick Search in Mobile Drawer -->
                        <form action="{{ route('catalog.index') }}" method="GET" class="relative pb-2">
                            <input 
                                type="text" 
                                name="search" 
                                placeholder="Search courses & masterclasses..." 
                                class="w-full bg-slate-100 hover:bg-slate-50 focus:bg-white border border-slate-200 rounded-xl text-xs py-2.5 pl-3.5 pr-9 text-slate-800 focus:ring-2 focus:ring-[#c30102]/20 focus:border-[#c30102] outline-none transition-all"
                            >
                            <button type="submit" class="absolute right-3 top-2.5 text-slate-400 hover:text-[#c30102]">
                                <i class="fas fa-search text-xs"></i>
                            </button>
                        </form>
                        <a href="{{ url('/') }}" class="block py-2 hover:text-[#c30102] border-b border-slate-100">Home</a>
                        <a href="{{ route('catalog.index') }}" class="block py-2 hover:text-[#c30102] border-b border-slate-100">Course Catalog</a>
                        <a href="{{ route('catalog.certificate') }}" class="flex items-center justify-between py-2 hover:text-[#c30102] border-b border-slate-100 text-xs pl-2 text-slate-600">
                            <span>↳ Certificate Programs</span>
                            @if(isset($publicCertCount) && $publicCertCount > 0)
                                <span class="text-[10px] px-1.5 py-0.5 rounded font-bold bg-slate-100 text-slate-700">{{ $publicCertCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('catalog.day_long') }}" class="flex items-center justify-between py-2 hover:text-[#c30102] border-b border-slate-100 text-xs pl-2 text-slate-600">
                            <span>↳ Day Long Masterclasses</span>
                            @if(isset($publicDayLongCount) && $publicDayLongCount > 0)
                                <span class="text-[10px] px-1.5 py-0.5 rounded font-bold bg-slate-100 text-slate-700">{{ $publicDayLongCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('instructors.index') }}" class="block py-2 hover:text-[#c30102] border-b border-slate-100">Instructors</a>
                        <a href="{{ route('blog.index') }}" class="block py-2 hover:text-[#c30102] border-b border-slate-100">Blog & Insights</a>
                        <a href="{{ route('feed.index') }}" class="block py-2 hover:text-[#c30102] border-b border-slate-100">Community Feed</a>
                        @if(isset($publicNavCategories) && $publicNavCategories->isNotEmpty())
                            @foreach($publicNavCategories->take(3) as $mCat)
                                <a href="{{ route('catalog.category', $mCat->slug) }}" class="block py-2 hover:text-[#c30102] border-b border-slate-100">{{ $mCat->name }}</a>
                            @endforeach
                        @endif
                        <a href="{{ route('about.index') }}" class="block py-2 hover:text-[#c30102] border-b border-slate-100">About Us</a>
                        <a href="{{ route('jobs.index') }}" class="flex items-center justify-between py-2 hover:text-[#c30102] border-b border-slate-100">
                            <span>Careers & Jobs</span>
                            <span class="px-1.5 py-0.5 rounded-full text-[9px] bg-emerald-500 text-white font-black uppercase tracking-wider">Hiring</span>
                        </a>
                        <a href="{{ route('certificate.verify') }}" class="flex items-center justify-between py-2 hover:text-[#c30102] border-b border-slate-100">
                            <span>Verify Certificate</span>
                            <span class="text-[9px] px-2 py-0.5 rounded-full font-bold bg-emerald-100 text-emerald-700">Official</span>
                        </a>
                        <a href="{{ route('wishlist.index') }}" class="block py-2 hover:text-[#c30102] border-b border-slate-100">My Wishlist</a>
                        <a href="{{ route('contact.index') }}" class="block py-2 hover:text-[#c30102] border-b border-slate-100">Contact & Support</a>
                        <div class="pt-2 pb-1 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Institutional Policies</div>
                        <div class="grid grid-cols-3 gap-2 pb-2 border-b border-slate-100 text-[11px]">
                            <a href="{{ route('legal.privacy') }}" class="text-slate-600 hover:text-[#c30102]">Privacy</a>
                            <a href="{{ route('legal.terms') }}" class="text-slate-600 hover:text-[#c30102]">Terms</a>
                            <a href="{{ route('legal.refund') }}" class="text-slate-600 hover:text-[#c30102]">Refund</a>
                        </div>
                        <div class="pt-3">
                            @auth
                                <a href="{{ route('dashboard') }}" class="block w-full text-center py-2.5 btn-ipdp-primary rounded-xl font-bold">Dashboard</a>
                            @else
                                <div class="flex gap-2">
                                    <a href="{{ route('login') }}" class="w-1/2 text-center py-2.5 btn-ipdp-primary rounded-xl font-bold">Sign In</a>
                                    <a href="{{ route('register') }}" class="w-1/2 text-center py-2.5 btn-ipdp-secondary rounded-xl font-bold">Register</a>
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Slot -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer & Subfooter -->
    <footer id="contact" class="bg-[#020b18] text-slate-300 pt-16 pb-8 border-t border-slate-800/80 mt-16 text-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
                <!-- Col 1: About Institute -->
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <img class="h-11 w-11 object-contain rounded-full bg-white p-0.5 shadow-md" 
                             src="{{ \App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png') }}" 
                             alt="{{ \App\Models\Setting::getVal('site_name', 'CAB Academy') }}"
                             onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}';">
                        <div>
                            <h3 class="text-white font-black text-lg leading-tight uppercase tracking-tight">{{ \App\Models\Setting::getVal('site_name', 'CAB Academy') }}</h3>
                            <span class="text-[10px] text-[#c30102] font-black tracking-widest uppercase block">{{ \App\Models\Setting::getVal('site_tagline', \App\Models\Setting::getVal('landing_hero_tagline', 'Learn, Grow, Lead.')) }}</span>
                        </div>
                    </div>
                    <p class="leading-relaxed mb-6 text-xs sm:text-sm text-slate-300">
                        {{ \App\Models\Setting::getVal('site_name', 'CAB Academy') }} is a specialized professional training institute delivering practical, industry-standard certification programs in Export-Import, Supply Chain, VAT & Tax, and Corporate Leadership.
                    </p>
                    <div class="flex items-center gap-3">
                        <a href="#" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center text-white hover:bg-[#c30102] transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center text-white hover:bg-[#c30102] transition-colors">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center text-white hover:bg-[#c30102] transition-colors">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>

                <!-- Col 2: Useful Links -->
                <div>
                    <h4 class="text-white font-bold text-base mb-4 tracking-wide uppercase text-xs">Useful Links</h4>
                    <ul class="space-y-2 text-xs sm:text-sm">
                        <li><a href="{{ url('/') }}" class="hover:text-white transition-colors">Home</a></li>
                        <li><a href="{{ route('catalog.index') }}" class="hover:text-white transition-colors">Course Catalog</a></li>
                        <li><a href="{{ route('instructors.index') }}" class="hover:text-white transition-colors">Instructors</a></li>
                        <li><a href="{{ route('blog.index') }}" class="hover:text-white transition-colors">Blog & Insights</a></li>
                        <li><a href="{{ route('feed.index') }}" class="hover:text-white transition-colors">Community Feed</a></li>
                        <li><a href="{{ route('certificate.verify') }}" class="hover:text-white transition-colors text-emerald-400 font-semibold flex items-center gap-1.5"><i class="fas fa-shield-alt text-[11px]"></i><span>Verify Certificate</span></a></li>
                        <li><a href="{{ route('about.index') }}" class="hover:text-white transition-colors">About Us</a></li>
                        <li><a href="{{ route('contact.index') }}" class="hover:text-white transition-colors">Contact & Admissions</a></li>
                        <li><a href="{{ route('jobs.index') }}" class="hover:text-white transition-colors">Careers & Jobs</a></li>
                        <li><a href="{{ route('legal.privacy') }}" class="hover:text-white transition-colors text-slate-400">Privacy Policy</a></li>
                    </ul>
                </div>

                <!-- Col 3: Contact & Operational Office -->
                <div>
                    <h4 class="text-white font-bold text-base mb-4 tracking-wide uppercase text-xs">Contact</h4>
                    <div class="space-y-2.5 text-xs sm:text-sm">
                        <p class="leading-relaxed">
                            <strong class="text-white">Operational Office Address:</strong><br>
                            {{ \App\Models\Setting::getVal('contact_address', 'Mirpur-Pallabi, Dhaka, Bangladesh, 1216') }}
                        </p>
                        <p>
                            <a href="mailto:{{ \App\Models\Setting::getVal('contact_email', 'commercialacademy868@gmail.com') }}" class="hover:text-white transition-colors">
                                {{ \App\Models\Setting::getVal('contact_email', 'commercialacademy868@gmail.com') }}
                            </a>
                        </p>
                        <p>
                            @php
                                $ftHotline = \App\Models\Setting::getVal('contact_hotline', '01917-054664');
                                $ftHotlineTel = preg_replace('/[^0-9+]/', '', $ftHotline) ?: '01917054664';
                            @endphp
                            <a href="tel:{{ $ftHotlineTel }}" class="hover:text-white transition-colors">
                                {{ $ftHotline }}
                            </a>
                        </p>
                    </div>
                </div>

                <!-- Col 4: Legal & Compliance -->
                <div>
                    <h4 class="text-white font-bold text-base mb-4 tracking-wide uppercase text-xs">Legal & Compliance</h4>
                    <ul class="space-y-2 text-xs sm:text-sm">
                        <li><a href="{{ route('legal.privacy') }}" class="hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="{{ route('legal.refund') }}" class="hover:text-white transition-colors">Refund and Returns Policy</a></li>
                        <li><a href="{{ route('legal.terms') }}" class="hover:text-white transition-colors">Terms of Service</a></li>
                    </ul>
                </div>
            </div>

            <!-- Subfooter (With NeuraSoft Attribution) -->
            <div class="border-t border-white/10 pt-6 pb-6 flex flex-col sm:flex-row justify-between items-center text-xs text-slate-400 gap-3">
                <p>© {{ date('Y') }} CAB Academy. All Rights Reserved.</p>
                <p class="sm:mr-20">
                    Developed by <a href="https://neurasoft.top" target="_blank" rel="noopener noreferrer" class="text-white hover:text-[#c30102] font-bold underline underline-offset-4 transition-colors">NeuraSoft</a>
                </p>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp Action Button (Matches Reference Screenshot) -->
    <a 
        href="https://wa.me/{{ \App\Models\Setting::getVal('contact_whatsapp', '8801917054664') }}" 
        target="_blank" 
        rel="noopener noreferrer" 
        class="fixed bottom-16 sm:bottom-5 right-4 sm:right-5 z-40 w-11 h-11 sm:w-14 sm:h-14 rounded-full bg-[#25D366] text-white flex items-center justify-center shadow-2xl hover:scale-110 transition-all duration-300"
        aria-label="Chat on WhatsApp"
        title="Chat on WhatsApp"
    >
        <i class="fab fa-whatsapp text-xl sm:text-3xl"></i>
    </a>

    <!-- Floating Scroll-To-Top Button (Matches Reference Screenshot) -->
    <button 
        onclick="window.scrollTo({top: 0, behavior: 'smooth'})" 
        class="fixed bottom-16 sm:bottom-5 left-4 sm:left-5 z-40 w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-900/80 hover:bg-[#c30102] text-white flex items-center justify-center shadow-lg transition-all duration-300"
        aria-label="Scroll to Top"
        title="Scroll to Top"
    >
        <i class="fas fa-chevron-up text-xs sm:text-sm"></i>
    </button>

    <!-- Global Helpers for Express Drawer & Wishlist -->
    <script>
        window.openEnrollDrawer = function(courseData) {
            window.dispatchEvent(new CustomEvent('open-enroll-drawer', { detail: courseData }));
        };

        window.toggleWishlist = function(courseId, buttonEl) {
            fetch('/wishlist/' + courseId + '/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(res => {
                if (res.status === 401) {
                    window.location.href = '{{ route("login") }}';
                    return null;
                }
                return res.json();
            })
            .then(data => {
                if (!data) return;
                const badge = document.getElementById('wishlist-count-badge');
                if (badge) {
                    badge.innerText = data.count;
                    if (data.count > 0) {
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }
                const buttons = document.querySelectorAll('.wishlist-btn-' + courseId);
                buttons.forEach(btn => {
                    const icon = btn.querySelector('i');
                    const text = btn.querySelector('span');
                    if (data.status === 'added') {
                        btn.classList.add('text-[#c30102]');
                        if (icon) {
                            icon.classList.remove('far');
                            icon.classList.add('fas', 'text-[#c30102]');
                        }
                        if (text) text.innerText = 'Saved in Wishlist';
                    } else {
                        btn.classList.remove('text-[#c30102]');
                        if (icon) {
                            icon.classList.remove('fas', 'text-[#c30102]');
                            icon.classList.add('far');
                        }
                        if (text) text.innerText = 'Save to Wishlist';
                    }
                });
            })
        };

        window.copyShortLink = function(url, btn) {
            function showSuccess() {
                if (!btn) return;
                const origHTML = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check text-[10px]"></i><span>Copied!</span>';
                btn.classList.add('bg-emerald-600', 'text-white');
                setTimeout(() => {
                    btn.innerHTML = origHTML;
                    btn.classList.remove('bg-emerald-600');
                }, 2200);
            }

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(url).then(showSuccess).catch(() => fallback(url));
            } else {
                fallback(url);
            }

            function fallback(text) {
                const ta = document.createElement("textarea");
                ta.value = text;
                ta.style.position = "fixed";
                ta.style.left = "-9999px";
                document.body.appendChild(ta);
                ta.focus();
                ta.select();
                try {
                    document.execCommand('copy');
                    showSuccess();
                } catch (e) {
                    prompt('Copy this short link:', text);
                }
                document.body.removeChild(ta);
            }
        };
    </script>

    <!-- Global Express Course Enrollment Drawer -->
    <x-express-enroll-drawer />

</body>
</html>
