<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" prefix="og: https://ogp.me/ns# fb: https://ogp.me/ns/fb#">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Course Catalog') - {{ \App\Models\Setting::getVal('site_name', 'ACS Academy') }}</title>
    <link rel="icon" href="{{ \App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ \App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png') }}">
    <link rel="manifest" href="{{ route('pwa.manifest') }}">
    <meta name="theme-color" content="#051c45">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ \App\Models\Setting::getBrandSizing()['short_name'] }}">
    <meta name="mobile-web-app-capable" content="yes">

    <!-- Canonical URL -->
    <link rel="canonical" href="@yield('canonical_url', url()->current())">

    @php
        $computedRobots = \App\Support\SeoIndexingManager::getRobotsDirectiveForRequest(
            request(),
            View::hasSection('robots') ? View::getSection('robots') : null
        );
        $googleVerifyToken = \App\Support\SeoIndexingManager::extractVerificationCode(\App\Models\Setting::getVal('google_site_verification', ''));
        $bingVerifyToken = \App\Support\SeoIndexingManager::extractVerificationCode(\App\Models\Setting::getVal('bing_site_verification', ''));
        $yandexVerifyToken = \App\Support\SeoIndexingManager::extractVerificationCode(\App\Models\Setting::getVal('yandex_site_verification', ''));
        $gaMeasurementId = trim((string) \App\Models\Setting::getVal('google_analytics_id', ''));
        $fbPixelId = trim((string) \App\Models\Setting::getVal('facebook_pixel_id', ''));
        $defaultSeoKeywords = \App\Models\Setting::getVal('seo_keywords', 'Commercial Banking course Dhaka, Export Import training Bangladesh, Letter of Credit LC course Dhaka, Customs clearance training NBR, Supply chain certification Dhaka, professional training Mirpur Pallabi, ACS Academy');
    @endphp

    <!-- Search Engine Indexing & Crawl Directives (Non-Sensitive Pages Only) -->
    <meta name="robots" content="{{ $computedRobots }}">
    <meta name="googlebot" content="{{ $computedRobots }}">
    <meta name="bingbot" content="{{ $computedRobots }}">

    <!-- Search Console & Webmaster Verification Tags -->
    @if($googleVerifyToken !== '')
    <meta name="google-site-verification" content="{{ $googleVerifyToken }}">
    @endif
    @if($bingVerifyToken !== '')
    <meta name="msvalidate.01" content="{{ $bingVerifyToken }}">
    @endif
    @if($yandexVerifyToken !== '')
    <meta name="yandex-verification" content="{{ $yandexVerifyToken }}">
    @endif

    <!-- Meta Description & Keywords -->
    <meta name="description" content="@yield('meta_description', 'ACS Academy is Bangladesh\'s premier professional training institute offering executive certifications in Commercial Banking, Export-Import (EXIM) Procedures, Letter of Credit (LC) Operations, Customs Clearance, and Supply Chain Management in Dhaka.')">
    <meta name="keywords" content="@yield('meta_keywords', $defaultSeoKeywords)">
    <meta name="author" content="ACS Academy">

    <!-- Geo & Local SEO Tags (Dhaka, Bangladesh) -->
    <meta name="geo.region" content="BD-13">
    <meta name="geo.placename" content="Dhaka, Bangladesh">
    <meta name="geo.position" content="23.8223;90.3654">
    <meta name="ICBM" content="23.8223, 90.3654">

    <!-- Open Graph (Facebook, LinkedIn, Social Previews) -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="{{ \App\Models\Setting::getVal('site_name', 'ACS Academy') }}">
    <meta property="og:locale" content="en_US">
    <meta property="og:title" content="@yield('og_title', View::hasSection('title') ? View::getSection('title') . ' - ACS Academy' : 'ACS Academy | Commercial Banking & Trade Training')">
    <meta property="og:description" content="@yield('meta_description', 'ACS Academy is Bangladesh\'s premier professional training institute offering executive certifications in Commercial Banking, Export-Import Procedures, and Supply Chain Operations.')">
    <meta property="og:url" content="@yield('canonical_url', url()->current())">
    @php
        $rawOgImg = trim(View::getSection('og_image') ?: (\App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png')));
        while (str_contains($rawOgImg, '&amp;')) {
            $rawOgImg = html_entity_decode($rawOgImg, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        $fullOgImg = (str_starts_with($rawOgImg, 'http://') || str_starts_with($rawOgImg, 'https://')) ? $rawOgImg : url($rawOgImg);
        $secureOgImg = str_starts_with($fullOgImg, 'https://') ? $fullOgImg : (str_starts_with($fullOgImg, 'http://') ? 'https://' . substr($fullOgImg, 7) : $fullOgImg);
        $ogExt = strtolower(pathinfo(parse_url($fullOgImg, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
        $ogMime = match($ogExt) {
            'png' => 'image/png',
            'webp' => 'image/webp',
            default => 'image/jpeg',
        };
    @endphp
    <meta property="og:image" content="{!! $fullOgImg !!}">
    <meta property="og:image:secure_url" content="{!! $secureOgImg !!}">
    <meta property="og:image:type" content="{{ $ogMime }}">
    <meta property="og:image:width" content="@yield('og_image_width', '1200')">
    <meta property="og:image:height" content="@yield('og_image_height', '630')">
    <meta property="og:image:alt" content="@yield('og_image_alt', \App\Models\Setting::getVal('site_name', 'ACS Academy'))">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', View::hasSection('title') ? View::getSection('title') . ' - ACS Academy' : 'ACS Academy | Commercial Banking & Trade Training')">
    <meta name="twitter:description" content="@yield('meta_description', 'ACS Academy is Bangladesh\'s premier professional training institute offering executive certifications in Commercial Banking, Export-Import Procedures, and Supply Chain Operations.')">
    <meta name="twitter:image" content="{!! $fullOgImg !!}">
    <meta name="twitter:image:alt" content="@yield('og_image_alt', \App\Models\Setting::getVal('site_name', 'ACS Academy'))">

    <!-- Schema.org EducationalOrganization JSON-LD -->
    @php
        $siteLogoUrl = \App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png');
        $siteNameVal = \App\Models\Setting::getVal('site_name', 'ACS Academy');
        $sitePhoneVal = \App\Models\Setting::getVal('contact_hotline', '+8801917054664');
        $siteEmailVal = \App\Models\Setting::getVal('contact_email', 'info@acsacademy.bd');
        $officialFacebookUrl = trim((string) \App\Models\Setting::getVal('social_facebook_url', 'https://www.facebook.com/academyofcommercialandsupplychain')) ?: 'https://www.facebook.com/academyofcommercialandsupplychain';
        $officialLinkedinUrl = trim((string) \App\Models\Setting::getVal('social_linkedin_url', 'https://www.linkedin.com/company/acs-academy-bd')) ?: 'https://www.linkedin.com/company/acs-academy-bd';
        $officialYoutubeUrl = trim((string) \App\Models\Setting::getVal('social_youtube_url', ''));
        $sameAsLinks = array_values(array_filter([$officialFacebookUrl, $officialLinkedinUrl, $officialYoutubeUrl]));
        $orgSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'EducationalOrganization',
            'name' => $siteNameVal,
            'alternateName' => $siteNameVal,
            'url' => url('/'),
            'logo' => $siteLogoUrl,
            'image' => $siteLogoUrl,
            'description' => $siteNameVal . " is Bangladesh's premier executive training and professional certification academy.",
            'telephone' => $sitePhoneVal,
            'email' => $siteEmailVal,
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
            'sameAs' => $sameAsLinks,
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
    <meta property="article:publisher" content="{{ $officialFacebookUrl }}">
    <script type="application/ld+json">
    {!! json_encode($orgSchema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
    @yield('schema')

    @if($gaMeasurementId !== '' && preg_match('/^(G|UA|AW|GTM)-[A-Z0-9\-]+$/i', $gaMeasurementId))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaMeasurementId }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $gaMeasurementId }}');
    </script>
    @endif

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

        /* ==========================================================================
           Apple-Inspired Liquid Glass Card Architecture (High-Contrast & High-Visibility)
           ========================================================================== */
        .liquid-glass-card {
            background: linear-gradient(155deg, rgba(255, 255, 255, 0.91) 0%, rgba(255, 255, 255, 0.80) 50%, rgba(248, 250, 252, 0.90) 100%) !important;
            backdrop-filter: blur(22px) saturate(195%);
            -webkit-backdrop-filter: blur(22px) saturate(195%);
            border: 1px solid rgba(255, 255, 255, 0.92) !important;
            box-shadow:
                0 12px 34px -8px rgba(4, 21, 54, 0.09),
                0 4px 12px -2px rgba(4, 21, 54, 0.05),
                inset 0 1.5px 0 rgba(255, 255, 255, 0.98),
                inset 1px 0 0 rgba(255, 255, 255, 0.78),
                0 0 0 1px rgba(4, 21, 54, 0.065);
            transition: transform 0.32s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.32s cubic-bezier(0.22, 1, 0.36, 1), border-color 0.32s ease;
        }
        .liquid-glass-card:hover {
            background: linear-gradient(155deg, rgba(255, 255, 255, 0.96) 0%, rgba(255, 255, 255, 0.87) 55%, rgba(248, 250, 252, 0.94) 100%) !important;
            border-color: rgba(195, 1, 2, 0.28) !important;
            box-shadow:
                0 22px 46px -12px rgba(4, 21, 54, 0.17),
                0 8px 20px -4px rgba(195, 1, 2, 0.08),
                inset 0 1.5px 0 rgba(255, 255, 255, 1),
                0 0 0 1px rgba(195, 1, 2, 0.2);
        }
        .liquid-glass-body {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.65) 0%, rgba(255, 255, 255, 0.88) 100%) !important;
        }
        .liquid-glass-dark {
            background: linear-gradient(150deg, rgba(15, 29, 58, 0.78) 0%, rgba(7, 21, 48, 0.68) 55%, rgba(4, 15, 35, 0.82) 100%) !important;
            backdrop-filter: blur(22px) saturate(190%);
            -webkit-backdrop-filter: blur(22px) saturate(190%);
            border: 1px solid rgba(255, 255, 255, 0.18) !important;
            box-shadow:
                0 16px 38px -10px rgba(0, 0, 0, 0.45),
                inset 0 1px 0 rgba(255, 255, 255, 0.26),
                inset 1px 0 0 rgba(255, 255, 255, 0.12);
            transition: transform 0.32s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.32s ease, border-color 0.32s ease;
        }
        .liquid-glass-dark:hover {
            border-color: rgba(16, 185, 129, 0.45) !important;
            box-shadow:
                0 22px 48px -10px rgba(0, 0, 0, 0.55),
                0 0 24px -4px rgba(16, 185, 129, 0.18),
                inset 0 1.5px 0 rgba(255, 255, 255, 0.35);
        }
        .liquid-glass-pill {
            background: rgba(255, 255, 255, 0.78);
            backdrop-filter: blur(12px) saturate(180%);
            -webkit-backdrop-filter: blur(12px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.9);
            box-shadow: 0 2px 8px rgba(4, 21, 54, 0.06), inset 0 1px 0 rgba(255, 255, 255, 0.95);
        }

        /* ==========================================================================
           Liquid Glass Skeleton Shimmer System (Zero-Jank Loading & Transitions)
           ========================================================================== */
        @keyframes cabSkeletonShimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .skeleton-shimmer {
            background: linear-gradient(
                90deg,
                rgba(226, 232, 240, 0.72) 25%,
                rgba(248, 250, 252, 0.96) 50%,
                rgba(226, 232, 240, 0.72) 75%
            );
            background-size: 200% 100%;
            animation: cabSkeletonShimmer 1.45s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        }
        .img-skeleton-wrapper {
            position: relative;
            overflow: hidden;
        }
        .img-skeleton-wrapper::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(
                90deg,
                rgba(226, 232, 240, 0.75) 25%,
                rgba(241, 245, 249, 0.95) 50%,
                rgba(226, 232, 240, 0.75) 75%
            );
            background-size: 200% 100%;
            animation: cabSkeletonShimmer 1.45s cubic-bezier(0.4, 0, 0.2, 1) infinite;
            z-index: 0;
            transition: opacity 0.25s ease;
        }
        .img-skeleton-wrapper.is-loaded::before {
            opacity: 0;
            pointer-events: none;
            animation: none;
        }
        {!! \App\Models\Setting::getTypographyCss() !!}
    </style>
</head>
@php
    $brandSizing = \App\Models\Setting::getBrandSizing();
    $customPagesList = \App\Models\Setting::getCustomPages(true);
    $headerCustomPages = collect($customPagesList)->filter(fn($p) => !empty($p['show_in_header']))->values();
    $footerCustomPages = collect($customPagesList)->filter(fn($p) => !empty($p['show_in_footer']))->values();
@endphp
<body class="antialiased min-h-screen flex flex-col selection:bg-[#c30102] selection:text-white pb-16 lg:pb-0" x-data="{ searchModalOpen: false }">

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
                <span class="text-white/30">&bull;</span>
                <a href="{{ $officialFacebookUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Visit Academy of Commercial & Supply Chain (ACS) on Facebook" title="Official Facebook Page — Academy of Commercial & Supply Chain (ACS)" class="text-[#60a5fa] hover:text-white flex items-center gap-1.5 transition-colors font-semibold">
                    <i class="fab fa-facebook text-sm text-[#1877F2] bg-white rounded-full leading-none"></i>
                    <span>Facebook Page</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-xl border-b border-slate-200/80 shadow-[0_4px_25px_-5px_rgba(5,28,69,0.06)] transition-all w-full">
        <div class="max-w-7xl mx-auto px-3 sm:px-5 lg:px-6 w-full">
            <div class="flex justify-between items-center h-16 sm:h-20 gap-2 xl:gap-3 w-full min-w-0">
                
                <!-- Auto-Responsive Institution Brand Lockup (Two-Part Name: "Academy of" <br> "Commercial & Supply Chain (ACS)") -->
                <a href="{{ url('/') }}" class="flex items-center gap-2 sm:gap-2.5 shrink-0 group max-w-[220px] sm:max-w-[260px] lg:max-w-[240px] xl:max-w-[270px]">
                    <div class="relative shrink-0">
                        <img class="h-10 w-10 sm:h-11 sm:w-11 xl:h-12 xl:w-12 object-contain rounded-full shadow-sm bg-white border border-slate-100 ring-2 ring-slate-100 group-hover:ring-[#c30102]/30 group-hover:scale-105 transition-all duration-300" 
                             src="{{ \App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png') }}" 
                             alt="{{ \App\Models\Setting::getVal('site_name', 'ACS Academy') }}"
                             onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}';">
                    </div>
                    <div class="flex flex-col min-w-0">
                        <span class="brand-adaptive-title font-black tracking-tight text-[#041536] uppercase leading-[1.15]" style="{{ $brandSizing['header_style'] ?? '' }}" title="{{ $brandSizing['site_name'] ?? $brandSizing['name'] ?? 'ACS Academy' }}">
                            {!! $brandSizing['formatted_html'] ?? e($brandSizing['site_name'] ?? $brandSizing['name'] ?? 'ACS Academy') !!}
                        </span>
                        <span class="text-[8px] sm:text-[9px] tracking-widest uppercase font-black text-[#c30102] mt-0.5 truncate">{{ \App\Models\Setting::getVal('site_tagline', \App\Models\Setting::getVal('landing_hero_tagline', 'Learn, Grow, Lead.')) }}</span>
                    </div>
                </a>

                <!-- Desktop Navigation Menu (Fluid & Zero-Overflow at all Laptop/Desktop widths) -->
                <nav class="hidden lg:flex items-center justify-center flex-1 min-w-0 gap-0.5 xl:gap-1 2xl:gap-2 text-[11.5px] xl:text-[12.5px] 2xl:text-[13.5px] font-semibold text-[#041536]" x-data="{ courseDropdown: false, communityDropdown: false }">
                    <a href="{{ url('/') }}" class="px-1.5 xl:px-2 2xl:px-2.5 py-1.5 rounded-lg hover:text-[#c30102] hover:bg-slate-50 transition-all whitespace-nowrap {{ request()->is('/') ? 'text-[#c30102] font-bold bg-slate-50/80' : '' }}">
                        <span>Home</span>
                    </a>

                    <!-- Course Dropdown -->
                    <div class="relative py-2" @mouseenter="courseDropdown = true" @mouseleave="courseDropdown = false" @click.away="courseDropdown = false">
                        <a href="{{ route('catalog.index') }}" @click="if (window.innerWidth < 1280) { $event.preventDefault(); courseDropdown = !courseDropdown; }" class="flex items-center gap-1 px-1.5 xl:px-2 2xl:px-2.5 py-1.5 rounded-lg hover:text-[#c30102] hover:bg-slate-50 transition-all whitespace-nowrap {{ request()->is('catalog*') || request()->is('courses*') ? 'text-[#c30102] font-bold bg-slate-50/80' : '' }}">
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

                    @if(\App\Models\Setting::isPageEnabled('jobs'))
                    <!-- Career & Placement Opportunities -->
                    <a href="{{ route('jobs.index') }}" class="flex items-center gap-1 px-1.5 xl:px-2 2xl:px-2.5 py-1.5 rounded-lg hover:text-[#c30102] hover:bg-slate-50 transition-all whitespace-nowrap {{ request()->is('jobs*') ? 'text-[#c30102] font-bold bg-slate-50/80' : '' }}">
                        <i class="fas fa-briefcase text-[11px] text-[#051c45] hidden xl:inline-block"></i>
                        <span>Careers & Jobs</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[8.5px] bg-emerald-500 text-white font-black uppercase tracking-wider shadow-xs">Hiring</span>
                    </a>
                    @endif

                    @if(\App\Models\Setting::isPageEnabled('instructors'))
                    <!-- Instructors Directory -->
                    <a href="{{ route('instructors.index') }}" class="px-1.5 xl:px-2 2xl:px-2.5 py-1.5 rounded-lg hover:text-[#c30102] hover:bg-slate-50 transition-all whitespace-nowrap {{ request()->is('instructors*') || request()->is('faculty*') ? 'text-[#c30102] font-bold bg-slate-50/80' : '' }}">
                        <span>Instructors</span>
                    </a>
                    @endif

                    @if(\App\Models\Setting::isPageEnabled('about'))
                    <!-- About Us -->
                    <a href="{{ route('about.index') }}" class="px-1.5 xl:px-2 2xl:px-2.5 py-1.5 rounded-lg hover:text-[#c30102] hover:bg-slate-50 transition-all whitespace-nowrap {{ request()->routeIs('about.*') ? 'text-[#c30102] font-bold bg-slate-50/80' : '' }}">
                        <span>About</span>
                    </a>
                    @endif

                    @if(\App\Models\Setting::isPageEnabled('contact'))
                    <!-- Contact -->
                    <a href="{{ route('contact.index') }}" class="px-1.5 xl:px-2 2xl:px-2.5 py-1.5 rounded-lg hover:text-[#c30102] hover:bg-slate-50 transition-all whitespace-nowrap {{ request()->routeIs('contact.*') ? 'text-[#c30102] font-bold bg-slate-50/80' : '' }}">
                        <span>Contact</span>
                    </a>
                    @endif

                    @foreach($headerCustomPages as $customNavPage)
                        <a href="{{ route('pages.show', $customNavPage['slug']) }}" class="px-1.5 xl:px-2 2xl:px-2.5 py-1.5 rounded-lg hover:text-[#c30102] hover:bg-slate-50 transition-all whitespace-nowrap {{ request()->is('page/' . $customNavPage['slug']) ? 'text-[#c30102] font-bold bg-slate-50/80' : '' }}">
                            <span>{{ $customNavPage['title'] }}</span>
                        </a>
                    @endforeach

                    <!-- Community & Insights Dropdown (Feed, Blog, Verification, Policies) -->
                    <div class="relative py-2" @mouseenter="communityDropdown = true" @mouseleave="communityDropdown = false" @click.away="communityDropdown = false">
                        <button type="button" @click="communityDropdown = !communityDropdown" class="flex items-center gap-1 px-1.5 xl:px-2 2xl:px-2.5 py-1.5 rounded-lg hover:text-[#c30102] hover:bg-slate-50 transition-all whitespace-nowrap {{ request()->is('feed*') || request()->is('blog*') || request()->is('verify-certificate*') ? 'text-[#c30102] font-bold bg-slate-50/80' : '' }}">
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
                            @if(\App\Models\Setting::isPageEnabled('feed'))
                            <a href="{{ route('feed.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl text-xs sm:text-sm hover:bg-slate-50 hover:text-[#c30102] transition-colors font-semibold text-slate-800">
                                <i class="fas fa-comments text-xs text-[#051c45]"></i>
                                <span>Community Discussion Feed</span>
                            </a>
                            @endif
                            @if(\App\Models\Setting::isPageEnabled('blog'))
                            <a href="{{ route('blog.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl text-xs sm:text-sm hover:bg-slate-50 hover:text-[#c30102] transition-colors font-semibold text-slate-800">
                                <i class="fas fa-book-reader text-xs text-[#c30102]"></i>
                                <span>Knowledge Hub & Blogs</span>
                            </a>
                            @endif
                            @if(\App\Models\Setting::isPageEnabled('verify_certificate'))
                            <a href="{{ route('certificate.verify') }}" class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl text-xs sm:text-sm hover:bg-slate-50 hover:text-[#c30102] transition-colors font-semibold text-slate-800">
                                <i class="fas fa-shield-alt text-xs text-[#10b981]"></i>
                                <span>Verify Certificate</span>
                            </a>
                            @endif
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
                <div class="hidden sm:flex items-center gap-1.5 xl:gap-2.5 shrink-0">
                    <!-- Compact Search Trigger on lg (1024px-1279px) -->
                    <button type="button" @click="searchModalOpen = true" class="hidden lg:inline-flex xl:hidden w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200/80 text-slate-600 items-center justify-center transition-colors" title="Search Courses">
                        <i class="fas fa-search text-xs"></i>
                    </button>

                    <!-- Quick Expanding Search Pill on xl+ (1280px+) -->
                    <div class="relative hidden xl:block" x-data="{ expanded: false }">
                        <form action="{{ route('catalog.index') }}" method="GET" class="relative flex items-center">
                            <input 
                                type="text" 
                                name="search" 
                                placeholder="Search..." 
                                @focus="expanded = true" 
                                @blur="expanded = false"
                                :class="expanded ? 'w-32 2xl:w-44 ring-2 ring-[#c30102]/20 border-[#c30102] bg-white' : 'w-22 2xl:w-28 bg-slate-100/90 border-slate-200'"
                                class="transition-all duration-300 text-xs py-1.5 pl-7 pr-2 rounded-xl border text-slate-800 outline-none placeholder:text-slate-400 font-medium"
                            >
                            <i class="fas fa-search text-[11px] text-slate-400 absolute left-2.5 pointer-events-none"></i>
                        </form>
                    </div>

                    <!-- Wishlist Heart Icon with dynamic badge -->
                    <a href="{{ route('wishlist.index') }}" title="Saved Wishlist" class="relative text-[#c30102] p-1.5 hover:scale-110 transition-transform shrink-0">
                        <i class="fas fa-heart text-base xl:text-lg"></i>
                        <span id="wishlist-count-badge" class="absolute -top-0.5 -right-0.5 bg-[#c30102] text-white text-[9px] font-black w-4 h-4 rounded-full flex items-center justify-center shadow {{ auth()->check() && auth()->user()->wishlists()->count() > 0 ? '' : 'hidden' }}">
                            {{ auth()->check() ? auth()->user()->wishlists()->count() : 0 }}
                        </span>
                    </a>

                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-ipdp-primary px-2.5 xl:px-3.5 py-1.5 xl:py-2 rounded-xl text-xs xl:text-[13px] font-bold shadow-md flex items-center gap-1.5 whitespace-nowrap shrink-0">
                            @if(auth()->user()->hasCustomProfilePicture())
                                <img src="{{ auth()->user()->profile_picture_url }}" alt="{{ auth()->user()->name }}" class="w-5 h-5 xl:w-6 xl:h-6 rounded-full object-cover border border-white/40 shrink-0" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=051c45&color=fff';">
                            @else
                                <i class="far fa-user text-xs"></i>
                            @endif
                            <span>Dashboard</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-ipdp-primary px-3 xl:px-4 py-1.5 xl:py-2 rounded-xl text-xs xl:text-[13px] font-bold shadow-md flex items-center gap-1.5 whitespace-nowrap shrink-0">
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
                        @if(\App\Models\Setting::isPageEnabled('instructors'))
                        <a href="{{ route('instructors.index') }}" class="block py-2 hover:text-[#c30102] border-b border-slate-100">Instructors</a>
                        @endif
                        @if(\App\Models\Setting::isPageEnabled('blog'))
                        <a href="{{ route('blog.index') }}" class="block py-2 hover:text-[#c30102] border-b border-slate-100">Blog & Insights</a>
                        @endif
                        @if(\App\Models\Setting::isPageEnabled('feed'))
                        <a href="{{ route('feed.index') }}" class="block py-2 hover:text-[#c30102] border-b border-slate-100">Community Feed</a>
                        @endif
                        @if(isset($publicNavCategories) && $publicNavCategories->isNotEmpty())
                            @foreach($publicNavCategories->take(3) as $mCat)
                                <a href="{{ route('catalog.category', $mCat->slug) }}" class="block py-2 hover:text-[#c30102] border-b border-slate-100">{{ $mCat->name }}</a>
                            @endforeach
                        @endif
                        @if(\App\Models\Setting::isPageEnabled('about'))
                        <a href="{{ route('about.index') }}" class="block py-2 hover:text-[#c30102] border-b border-slate-100">About Us</a>
                        @endif
                        @if(\App\Models\Setting::isPageEnabled('jobs'))
                        <a href="{{ route('jobs.index') }}" class="flex items-center justify-between py-2 hover:text-[#c30102] border-b border-slate-100">
                            <span>Careers & Jobs</span>
                            <span class="px-1.5 py-0.5 rounded-full text-[9px] bg-emerald-500 text-white font-black uppercase tracking-wider">Hiring</span>
                        </a>
                        @endif
                        @if(\App\Models\Setting::isPageEnabled('verify_certificate'))
                        <a href="{{ route('certificate.verify') }}" class="flex items-center justify-between py-2 hover:text-[#c30102] border-b border-slate-100">
                            <span>Verify Certificate</span>
                            <span class="text-[9px] px-2 py-0.5 rounded-full font-bold bg-emerald-100 text-emerald-700">Official</span>
                        </a>
                        @endif
                        @foreach($headerCustomPages as $customNavPage)
                            <a href="{{ route('pages.show', $customNavPage['slug']) }}" class="block py-2 hover:text-[#c30102] border-b border-slate-100">{{ $customNavPage['title'] }}</a>
                        @endforeach
                        <a href="{{ route('wishlist.index') }}" class="block py-2 hover:text-[#c30102] border-b border-slate-100">My Wishlist</a>
                        @if(\App\Models\Setting::isPageEnabled('contact'))
                        <a href="{{ route('contact.index') }}" class="block py-2 hover:text-[#c30102] border-b border-slate-100">Contact & Support</a>
                        @endif
                        <a href="{{ $officialFacebookUrl }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between py-2 hover:text-[#1877F2] border-b border-slate-100 text-[#1877F2] font-bold">
                            <span class="inline-flex items-center gap-2"><i class="fab fa-facebook text-sm"></i> Official Facebook Page</span>
                            <i class="fas fa-external-link-alt text-[10px] opacity-70"></i>
                        </a>
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

    @auth
        <!-- Authenticated Portal Quick-Navigation Bar (Prevents Logged-In Students from Getting Lost on Public Pages) -->
        <div class="bg-[#051c45] text-white border-b border-white/10 py-2 px-4 text-xs relative z-40">
            <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="font-semibold text-slate-200">
                        {{ auth()->user()->isAdmin() ? 'Admin Session:' : 'Student Portal Active:' }}
                        <strong class="text-white">{{ auth()->user()->name }}</strong>
                    </span>
                    @if(!auth()->user()->isAdmin())
                        <span class="hidden sm:inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-white/10 text-[10px] font-bold text-amber-300">
                            ✨ {{ auth()->user()->xp }} XP &bull; 🔥 {{ auth()->user()->current_streak }}d Streak
                        </span>
                    @endif
                </div>
                <div class="flex items-center gap-2 sm:gap-3 flex-wrap">
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-[#c30102] hover:bg-[#a50102] text-white font-bold text-[11px] shadow-xs transition-all">
                        <i class="fas fa-arrow-left text-[9px]"></i>
                        <span>{{ auth()->user()->isAdmin() ? 'Admin Console' : 'My Dashboard' }}</span>
                    </a>
                    @if(!auth()->user()->isAdmin())
                        <a href="{{ route('feed.index') }}" class="hidden md:inline-flex items-center gap-1 text-slate-300 hover:text-white font-semibold text-[11px] transition-colors">
                            <i class="fas fa-comments text-[10px] text-emerald-400"></i>
                            <span>Community Feed</span>
                        </a>
                        <a href="{{ route('performance.index') }}" class="hidden md:inline-flex items-center gap-1 text-slate-300 hover:text-white font-semibold text-[11px] transition-colors">
                            <i class="fas fa-chart-line text-[10px] text-amber-300"></i>
                            <span>Performance</span>
                        </a>
                        <a href="{{ route('wishlist.index') }}" class="hidden sm:inline-flex items-center gap-1 text-slate-300 hover:text-white font-semibold text-[11px] transition-colors">
                            <i class="fas fa-heart text-[10px] text-[#c30102]"></i>
                            <span>Saved Wishlist</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endauth

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
                        <img class="h-11 w-11 shrink-0 object-contain rounded-full bg-white p-0.5 shadow-md" 
                             src="{{ \App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png') }}" 
                             alt="{{ \App\Models\Setting::getVal('site_name', 'ACS Academy') }}"
                             onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}';">
                        <div class="min-w-0">
                            <h3 class="text-white font-black uppercase tracking-tight brand-adaptive-title leading-tight" style="{{ $brandSizing['sidebar_style'] ?? '' }}">{!! $brandSizing['formatted_html'] ?? e($brandSizing['site_name'] ?? $brandSizing['name'] ?? 'ACS Academy') !!}</h3>
                            <span class="text-[10px] text-[#c30102] font-black tracking-widest uppercase block truncate mt-0.5">{{ \App\Models\Setting::getVal('site_tagline', \App\Models\Setting::getVal('landing_hero_tagline', 'Learn, Grow, Lead.')) }}</span>
                        </div>
                    </div>
                    <p class="leading-relaxed mb-6 text-xs sm:text-sm text-slate-300">
                        {{ \App\Models\Setting::getVal('site_name', 'ACS Academy') }} is a specialized professional training institute delivering practical, industry-standard certification programs in Export-Import, Supply Chain, VAT & Tax, and Corporate Leadership.
                    </p>
                    <div class="flex items-center gap-3">
                        <a href="{{ $officialFacebookUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Follow {{ \App\Models\Setting::getVal('site_name', 'ACS Academy') }} on Facebook" title="Official Facebook Page — {{ \App\Models\Setting::getVal('site_name', 'ACS Academy') }}" class="w-9 h-9 rounded-full bg-[#1877F2] flex items-center justify-center text-white hover:bg-[#166fe5] shadow-md transition-all hover:scale-105">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        @if(!empty($officialLinkedinUrl))
                        <a href="{{ $officialLinkedinUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Follow {{ \App\Models\Setting::getVal('site_name', 'ACS Academy') }} on LinkedIn" title="Official LinkedIn — {{ \App\Models\Setting::getVal('site_name', 'ACS Academy') }}" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center text-white hover:bg-[#0A66C2] transition-colors">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        @endif
                        @if(!empty($officialYoutubeUrl))
                        <a href="{{ $officialYoutubeUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Subscribe to {{ \App\Models\Setting::getVal('site_name', 'ACS Academy') }} on YouTube" title="Official YouTube — {{ \App\Models\Setting::getVal('site_name', 'ACS Academy') }}" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center text-white hover:bg-[#FF0000] transition-colors">
                            <i class="fab fa-youtube"></i>
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Col 2: Useful Links -->
                <div>
                    <h4 class="text-white font-bold text-base mb-4 tracking-wide uppercase text-xs">Useful Links</h4>
                    <ul class="space-y-2 text-xs sm:text-sm">
                        <li><a href="{{ url('/') }}" class="hover:text-white transition-colors">Home</a></li>
                        <li><a href="{{ route('catalog.index') }}" class="hover:text-white transition-colors">Course Catalog</a></li>
                        @if(\App\Models\Setting::isPageEnabled('instructors'))
                        <li><a href="{{ route('instructors.index') }}" class="hover:text-white transition-colors">Instructors</a></li>
                        @endif
                        @if(\App\Models\Setting::isPageEnabled('blog'))
                        <li><a href="{{ route('blog.index') }}" class="hover:text-white transition-colors">Blog & Insights</a></li>
                        @endif
                        @if(\App\Models\Setting::isPageEnabled('feed'))
                        <li><a href="{{ route('feed.index') }}" class="hover:text-white transition-colors">Community Feed</a></li>
                        @endif
                        @if(\App\Models\Setting::isPageEnabled('verify_certificate'))
                        <li><a href="{{ route('certificate.verify') }}" class="hover:text-white transition-colors text-emerald-400 font-semibold flex items-center gap-1.5"><i class="fas fa-shield-alt text-[11px]"></i><span>Verify Certificate</span></a></li>
                        @endif
                        @if(\App\Models\Setting::isPageEnabled('about'))
                        <li><a href="{{ route('about.index') }}" class="hover:text-white transition-colors">About Us</a></li>
                        @endif
                        @if(\App\Models\Setting::isPageEnabled('contact'))
                        <li><a href="{{ route('contact.index') }}" class="hover:text-white transition-colors">Contact & Admissions</a></li>
                        @endif
                        @if(\App\Models\Setting::isPageEnabled('jobs'))
                        <li><a href="{{ route('jobs.index') }}" class="hover:text-white transition-colors">Careers & Jobs</a></li>
                        @endif
                        @foreach($footerCustomPages as $customFooterPage)
                            <li><a href="{{ route('pages.show', $customFooterPage['slug']) }}" class="hover:text-white transition-colors">{{ $customFooterPage['title'] }}</a></li>
                        @endforeach
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
                        <p class="pt-1">
                            <a href="{{ $officialFacebookUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-[#60a5fa] hover:text-white font-semibold transition-colors">
                                <i class="fab fa-facebook text-[#1877F2] bg-white rounded-full leading-none"></i>
                                <span>facebook.com/academyofcommercialandsupplychain</span>
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
                <p>© {{ date('Y') }} {{ \App\Models\Setting::getVal('site_name', 'ACS Academy') }}. All Rights Reserved.</p>
                <p class="sm:mr-20">
                    Developed by <a href="https://neurasoft.top" target="_blank" rel="noopener noreferrer" class="text-white hover:text-[#c30102] font-bold underline underline-offset-4 transition-colors">NeuraSoft</a>
                </p>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp Action Button (Matches Reference Screenshot) -->
    @php
        $globalWaRaw = \App\Models\Setting::getVal('contact_whatsapp', \App\Models\Setting::getVal('contact_hotline', '8801917054664'));
        $globalWaDigits = preg_replace('/[^0-9]/', '', (string) $globalWaRaw) ?: '8801917054664';
        if (strlen($globalWaDigits) === 11 && str_starts_with($globalWaDigits, '01')) {
            $globalWaDigits = '88' . $globalWaDigits;
        }
    @endphp
    <a 
        href="https://wa.me/{{ $globalWaDigits }}" 
        target="_blank" 
        rel="noopener noreferrer" 
        class="fixed bottom-20 lg:bottom-5 right-4 sm:right-5 z-40 w-11 h-11 sm:w-14 sm:h-14 rounded-full bg-[#25D366] text-white flex items-center justify-center shadow-2xl hover:scale-110 transition-all duration-300"
        aria-label="Chat on WhatsApp"
        title="Chat on WhatsApp"
    >
        <i class="fab fa-whatsapp text-xl sm:text-3xl"></i>
    </a>

    <!-- Floating Scroll-To-Top Button (Matches Reference Screenshot) -->
    <button 
        onclick="window.scrollTo({top: 0, behavior: 'smooth'})" 
        class="fixed bottom-20 lg:bottom-5 left-4 sm:left-5 z-40 w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-900/80 hover:bg-[#c30102] text-white flex items-center justify-center shadow-lg transition-all duration-300"
        aria-label="Scroll to Top"
        title="Scroll to Top"
    >
        <i class="fas fa-chevron-up text-xs sm:text-sm"></i>
    </button>

    <!-- Native Mobile App Bottom Tab Navigation Bar (PWA Ready) -->
    @if(!request()->routeIs('courses.show'))
    <nav class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-xl border-t border-slate-200/80 shadow-[0_-8px_25px_-5px_rgba(5,28,69,0.08)] pb-safe">
        <div class="grid grid-cols-5 h-16">
            <a href="{{ url('/') }}" class="flex flex-col items-center justify-center gap-1 transition-colors {{ request()->is('/') ? 'text-[#c30102] font-bold' : 'text-slate-500 hover:text-[#051c45]' }}">
                <i class="fas fa-home text-base"></i>
                <span class="text-[10px] tracking-tight">Home</span>
            </a>
            <a href="{{ route('catalog.index') }}" class="flex flex-col items-center justify-center gap-1 transition-colors {{ request()->is('catalog*') || request()->is('courses*') ? 'text-[#c30102] font-bold' : 'text-slate-500 hover:text-[#051c45]' }}">
                <i class="fas fa-layer-group text-base"></i>
                <span class="text-[10px] tracking-tight">Courses</span>
            </a>
            <button type="button" @click="searchModalOpen = true" class="flex flex-col items-center justify-center gap-1 text-slate-500 hover:text-[#051c45] transition-colors">
                <div class="w-9 h-9 rounded-full bg-[#051c45] text-white flex items-center justify-center shadow-md -mt-3 border-2 border-white">
                    <i class="fas fa-search text-xs"></i>
                </div>
                <span class="text-[10px] tracking-tight font-semibold text-[#051c45]">Search</span>
            </button>
            @if(\App\Models\Setting::isPageEnabled('jobs'))
            <a href="{{ route('jobs.index') }}" class="flex flex-col items-center justify-center gap-1 transition-colors {{ request()->is('jobs*') ? 'text-[#c30102] font-bold' : 'text-slate-500 hover:text-[#051c45]' }}">
                <i class="fas fa-briefcase text-base"></i>
                <span class="text-[10px] tracking-tight">Careers</span>
            </a>
            @else
            <a href="{{ route('certificate.verify') }}" class="flex flex-col items-center justify-center gap-1 transition-colors {{ request()->is('verify-certificate*') ? 'text-[#c30102] font-bold' : 'text-slate-500 hover:text-[#051c45]' }}">
                <i class="fas fa-shield-alt text-base"></i>
                <span class="text-[10px] tracking-tight">Verify</span>
            </a>
            @endif
            @auth
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center gap-1 transition-colors {{ request()->is('dashboard*') || request()->is('student*') || request()->is('admin*') ? 'text-[#c30102] font-bold' : 'text-slate-500 hover:text-[#051c45]' }}">
                <i class="fas fa-user-circle text-base"></i>
                <span class="text-[10px] tracking-tight">Portal</span>
            </a>
            @else
            <a href="{{ route('login') }}" class="flex flex-col items-center justify-center gap-1 transition-colors {{ request()->is('login*') ? 'text-[#c30102] font-bold' : 'text-slate-500 hover:text-[#051c45]' }}">
                <i class="fas fa-sign-in-alt text-base"></i>
                <span class="text-[10px] tracking-tight">Sign In</span>
            </a>
            @endauth
        </div>
    </nav>
    @endif

    <!-- Quick Search Sheet Modal -->
    <div x-show="searchModalOpen" x-transition.opacity class="fixed inset-0 z-50 bg-[#051c45]/60 backdrop-blur-sm flex items-start justify-center p-4 pt-20" style="display: none;">
        <div @click.away="searchModalOpen = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 p-4">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-[#051c45]">Search Programs & Masterclasses</span>
                <button type="button" @click="searchModalOpen = false" class="w-7 h-7 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            <form action="{{ route('catalog.index') }}" method="GET" class="flex gap-2">
                <input type="text" name="search" placeholder="Type LC, Export, Supply Chain, VAT..." class="flex-1 rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-[#051c45] focus:ring-2 focus:ring-[#051c45]/20 outline-none" required>
                <button type="submit" class="btn-ipdp-primary px-4 py-2.5 rounded-xl text-xs font-bold">Search</button>
            </form>
        </div>
    </div>

    <!-- Global Helpers for Express Drawer, Wishlist & PWA Service Worker -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js').catch(function() {});
            });
        }

        // Automatically clear skeleton shimmer for cached or completed images across all views
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.img-skeleton-wrapper img').forEach(function(img) {
                if (img.complete) {
                    img.parentElement.classList.add('is-loaded');
                }
            });
        });

        window.openEnrollDrawer = function(courseData) {
            window.dispatchEvent(new CustomEvent('open-enroll-drawer', { detail: courseData }));
        };

        window.openCourseShareModal = function(courseData) {
            window.dispatchEvent(new CustomEvent('open-course-share-modal', { detail: courseData }));
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

    <!-- Global Facebook Rich Link Preview & Stylish Share Modal -->
    <x-course-share-modal />

</body>
</html>
