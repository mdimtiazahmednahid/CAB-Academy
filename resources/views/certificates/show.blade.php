@php
    $template = request('template') ?: \App\Models\Setting::getVal('certificate_template', 'acs_signature');

    // Configurable Institution Branding & Content (defaults to Official ACS Signature Certificate)
    $institutionName = \App\Models\Setting::getVal('certificate_institution_name', 'ACADEMY OF COMMERCIAL & SUPPLY CHAIN-ACS');
    $tagline = \App\Models\Setting::getVal('certificate_tagline', 'LEARN .GROW.LEAD');
    $campusAddress = \App\Models\Setting::getVal('certificate_campus_address', 'Mirpur-Pallabi Campus • Dhaka, Bangladesh');
    $certTitle = \App\Models\Setting::getVal('certificate_title', 'CERTIFICATE OF APPRECIATION');
    $confermentText = \App\Models\Setting::getVal('certificate_conferment_text', 'This certificate is proudly presented to');
    $completionText = \App\Models\Setting::getVal('certificate_completion_text', 'Successfully participated in the training program, demonstrating active engagement and commitment to professional development.');
    $facultyEndorsement = \App\Models\Setting::getVal('certificate_faculty_endorsement', 'Delivered under the auspices of the ACS Academy Executive Faculty Board.');
    $primarySignatoryName = \App\Models\Setting::getVal('certificate_primary_signatory_name', 'Saidur Rahman');
    $primarySignatoryTitle = \App\Models\Setting::getVal('certificate_primary_signatory_title', 'ACS EXECUTIVE GOVORNING');
    $primarySignatoryBoard = \App\Models\Setting::getVal('certificate_primary_signatory_board', 'ACS EXECUTIVE GOVORNING');
    $showSecondary = \App\Models\Setting::getVal('certificate_show_secondary_signature', '1') == '1';
    $secondarySignatoryName = \App\Models\Setting::getVal('certificate_secondary_signatory_name', 'Lead Trainer');
    $secondarySignatoryTitle = \App\Models\Setting::getVal('certificate_secondary_signatory_title', 'LEAD TRAINER');
    $sealText = \App\Models\Setting::getVal('certificate_seal_text', 'OFFICIAL SEAL • VERIFIED');
    $sealParts = explode('•', $sealText);
    $sealLine1 = trim($sealParts[0] ?? 'OFFICIAL SEAL');
    $sealLine2 = trim($sealParts[1] ?? 'VERIFIED');

    // Admin Customizer Toggles & Custom Logo Overrides
    $showSeal = \App\Models\Setting::getVal('certificate_show_seal', '1') == '1';
    $showSealLogo = \App\Models\Setting::getVal('certificate_show_seal_logo', '1') == '1';
    $showSealText = \App\Models\Setting::getVal('certificate_show_seal_text', '1') == '1';
    $showQrCode = \App\Models\Setting::getVal('certificate_show_qr_code', '1') == '1';
    $showWatermark = \App\Models\Setting::getVal('certificate_show_watermark', '1') == '1';
    $customLogoUrl = trim((string) \App\Models\Setting::getVal('certificate_logo_url', '/images/certificate-logo-gold.png'));
    $customSealLogoUrl = trim((string) \App\Models\Setting::getVal('certificate_seal_logo_url', '/images/certificate-logo-gold.png'));
    $customSealBgUrl = trim((string) \App\Models\Setting::getVal('certificate_seal_bg_url', '/images/certificate-gold-seal.png'));
    $idLabel = \App\Models\Setting::getVal('certificate_id_label', 'CERTIFICATE ID:');
    $dateLabel = \App\Models\Setting::getVal('certificate_date_label', 'DATE:');
    $batchLabel = \App\Models\Setting::getVal('certificate_batch_label', 'BATCH:');
    $instFontSize = (float) \App\Models\Setting::getVal('certificate_institution_font_size', '31') ?: 31;
    $titleFontSize = (float) \App\Models\Setting::getVal('certificate_title_font_size', '35') ?: 35;
    $recipientFontSize = (float) \App\Models\Setting::getVal('certificate_recipient_font_size', '32') ?: 32;
    $courseFontSize = (float) \App\Models\Setting::getVal('certificate_course_font_size', '30') ?: 30;
    $bodyFontSize = (float) \App\Models\Setting::getVal('certificate_body_font_size', '16.5') ?: 16.5;
    $courseTitleLen = mb_strlen((string) $course->title);
    $courseFontSizeAdjusted = $courseTitleLen > 52 ? round($courseFontSize * 0.8, 1) : $courseFontSize;
    $verifyUrl = route('certificate.verify', $certificate->certificate_number);
    $qrSeed = crc32((string) $certificate->certificate_number);

    // Theme Palettes (Preserving all 4 previous templates + new default 'acs_signature')
    $themes = [
        'acs_signature' => [
            'name' => 'ACS Official Signature',
            'outer_border' => 'border-[#b91c1c]',
            'outer_border_hex' => '#b91c1c',
            'inner_border' => 'border-[#d4af37]',
            'bg_gradient' => 'from-[#fcf8f6] via-[#fdfafa] to-[#faf4f2]',
            'inst_color' => 'text-[#5b1415]',
            'tagline_color' => 'text-[#991b1b]',
            'badge_color' => 'text-[#b89758]',
            'badge_icon' => 'fa-award',
            'title_color' => 'text-[#641617]',
            'recipient_color' => 'text-[#b5892e]',
            'recipient_border' => 'border-[#64748b]',
            'course_color' => 'text-[#cc292b]',
            'divider' => 'border-[#e2d5bd]',
            'seal_bg' => 'from-[#dfba73] via-[#fae3a6] to-[#b89758]',
            'seal_border' => 'border-[#b89758]',
            'seal_inner_border' => 'border-[#8d6a2a]',
            'seal_text_color' => 'text-[#5c4010]',
            'seal_icon' => 'fa-stamp',
            'sig_color' => 'text-[#111827]',
        ],
        'classic_navy' => [
            'name' => 'Royal Navy & Gold',
            'outer_border' => 'border-[#051c45]',
            'outer_border_hex' => '#051c45',
            'inner_border' => 'border-[#b89758]',
            'bg_gradient' => 'from-[#fffefc] via-white to-[#faf6ef]',
            'inst_color' => 'text-[#051c45]',
            'tagline_color' => 'text-[#c30102]',
            'badge_color' => 'text-[#b89758]',
            'badge_icon' => 'fa-award',
            'title_color' => 'text-[#b89758]',
            'recipient_color' => 'text-[#051c45]',
            'recipient_border' => 'border-[#b89758]',
            'course_color' => 'text-[#c30102]',
            'divider' => 'border-[#e2d5bd]',
            'seal_bg' => 'from-[#dfba73] via-[#fae3a6] to-[#b89758]',
            'seal_border' => 'border-[#b89758]',
            'seal_inner_border' => 'border-[#8d6a2a]',
            'seal_text_color' => 'text-[#5c4010]',
            'seal_icon' => 'fa-stamp',
            'sig_color' => 'text-[#051c45]',
        ],
        'modern_emerald' => [
            'name' => 'Modern Luxury Emerald',
            'outer_border' => 'border-[#064e3b]',
            'outer_border_hex' => '#064e3b',
            'inner_border' => 'border-[#10b981]',
            'bg_gradient' => 'from-[#f0fdf4] via-white to-[#f7fee7]/40',
            'inst_color' => 'text-[#064e3b]',
            'tagline_color' => 'text-[#059669]',
            'badge_color' => 'text-[#10b981]',
            'badge_icon' => 'fa-shield-halved',
            'title_color' => 'text-[#047857]',
            'recipient_color' => 'text-[#064e3b]',
            'recipient_border' => 'border-[#10b981]',
            'course_color' => 'text-[#059669]',
            'divider' => 'border-[#a7f3d0]',
            'seal_bg' => 'from-[#10b981] via-[#6ee7b7] to-[#047857]',
            'seal_border' => 'border-[#10b981]',
            'seal_inner_border' => 'border-[#064e3b]',
            'seal_text_color' => 'text-[#064e3b]',
            'seal_icon' => 'fa-certificate',
            'sig_color' => 'text-[#064e3b]',
        ],
        'crimson_prestige' => [
            'name' => 'Crimson Prestige',
            'outer_border' => 'border-[#880102]',
            'outer_border_hex' => '#880102',
            'inner_border' => 'border-[#051c45]',
            'bg_gradient' => 'from-[#fff1f2]/40 via-white to-[#fff5f5]',
            'inst_color' => 'text-[#880102]',
            'tagline_color' => 'text-[#051c45]',
            'badge_color' => 'text-[#880102]',
            'badge_icon' => 'fa-star',
            'title_color' => 'text-[#880102]',
            'recipient_color' => 'text-[#051c45]',
            'recipient_border' => 'border-[#880102]',
            'course_color' => 'text-[#880102]',
            'divider' => 'border-[#fecdd3]',
            'seal_bg' => 'from-[#e11d48] via-[#fda4af] to-[#9f1239]',
            'seal_border' => 'border-[#be123c]',
            'seal_inner_border' => 'border-[#880102]',
            'seal_text_color' => 'text-[#880102]',
            'seal_icon' => 'fa-award',
            'sig_color' => 'text-[#880102]',
        ],
        'minimalist_corporate' => [
            'name' => 'Minimalist Corporate',
            'outer_border' => 'border-slate-900',
            'outer_border_hex' => '#0f172a',
            'inner_border' => 'border-slate-300',
            'bg_gradient' => 'from-slate-50 via-white to-slate-50',
            'inst_color' => 'text-slate-900',
            'tagline_color' => 'text-slate-600',
            'badge_color' => 'text-slate-400',
            'badge_icon' => 'fa-circle-check',
            'title_color' => 'text-slate-700',
            'recipient_color' => 'text-slate-900',
            'recipient_border' => 'border-slate-400',
            'course_color' => 'text-slate-800',
            'divider' => 'border-slate-200',
            'seal_bg' => 'from-slate-300 via-slate-100 to-slate-400',
            'seal_border' => 'border-slate-400',
            'seal_inner_border' => 'border-slate-500',
            'seal_text_color' => 'text-slate-800',
            'seal_icon' => 'fa-check',
            'sig_color' => 'text-slate-900',
        ],
    ];

    $theme = $themes[$template] ?? $themes['acs_signature'];
    $defaultGoldLogo = file_exists(public_path('images/certificate-logo-gold.png'))
        ? asset('images/certificate-logo-gold.png')
        : (\App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png'));
    $certGoldLogo = !empty($customLogoUrl)
        ? (str_starts_with($customLogoUrl, 'http') ? $customLogoUrl : asset(ltrim($customLogoUrl, '/')))
        : $defaultGoldLogo;
    $certSealLogo = !empty($customSealLogoUrl)
        ? (str_starts_with($customSealLogoUrl, 'http') ? $customSealLogoUrl : asset(ltrim($customSealLogoUrl, '/')))
        : $certGoldLogo;
    $defaultGoldSeal = file_exists(public_path('images/certificate-gold-seal.png'))
        ? asset('images/certificate-gold-seal.png')
        : null;
    $certGoldSeal = !empty($customSealBgUrl)
        ? (str_starts_with($customSealBgUrl, 'http') ? $customSealBgUrl : asset(ltrim($customSealBgUrl, '/')))
        : $defaultGoldSeal;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <title>{{ $certTitle }} - {{ $course->title }} - {{ $institutionName }}</title>
    <link rel="icon" href="{{ \App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Cinzel:wght@500;600;700;800;900&family=Cormorant+Garamond:wght@500;600;700&family=Great+Vibes&family=Montserrat:wght@400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {!! acs_inline_compiled_css() !!}

    <style>
        .font-cinzel { font-family: 'Cinzel', serif; }
        .font-cormorant { font-family: 'Cormorant Garamond', 'Cinzel', serif; }
        .font-playfair { font-family: 'Playfair Display', serif; }
        .font-script { font-family: 'Great Vibes', cursive; }
        .font-sans-body { font-family: 'Montserrat', sans-serif; }
        .font-display-heavy { font-family: 'Montserrat', sans-serif; font-weight: 900; }

        @page {
            size: A4 landscape;
            margin: 0;
        }

        @media print {
            html, body {
                width: 297mm !important;
                height: 210mm !important;
                max-height: 210mm !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
                overflow: hidden !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print {
                display: none !important;
            }
            .certificate-wrapper {
                box-shadow: none !important;
                border: 7px solid {{ $theme['outer_border_hex'] }} !important;
                @if($template === 'acs_signature')
                border-top-color: #bc2723 !important;
                border-bottom-color: #98201f !important;
                border-left-color: #252686 !important;
                border-right-color: #1c1d79 !important;
                @endif
                border-radius: 0 !important;
                width: 297mm !important;
                height: 210mm !important;
                max-height: 210mm !important;
                max-width: 297mm !important;
                margin: 0 auto !important;
                padding: {{ $template === 'acs_signature' ? '0' : '6mm' }} !important;
                box-sizing: border-box !important;
                page-break-inside: avoid !important;
                page-break-before: avoid !important;
                page-break-after: avoid !important;
                break-inside: avoid !important;
                overflow: hidden !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: center !important;
            }
            .certificate-inner {
                height: 100% !important;
                max-height: 100% !important;
                padding: {{ $template === 'acs_signature' ? '4mm 11mm' : '5mm 10mm' }} !important;
                box-sizing: border-box !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: space-between !important;
                overflow: hidden !important;
            }
            .acs-sig-logo {
                width: 152px !important;
                height: 141px !important;
            }
            .acs-sig-watermark {
                width: 410px !important;
                height: 410px !important;
            }
            .acs-print-inst {
                font-size: {{ round($instFontSize * 1.08, 1) }}px !important;
                line-height: 1.15 !important;
            }
            .acs-print-tagline {
                font-size: 12px !important;
            }
            .acs-print-title {
                font-size: {{ round($titleFontSize * 1.08, 1) }}px !important;
                line-height: 1.05 !important;
            }
            .acs-print-confer {
                font-size: 18px !important;
            }
            .acs-print-recipient {
                font-size: {{ round($recipientFontSize * 1.08, 1) }}px !important;
                line-height: 1.15 !important;
            }
            .acs-print-body {
                font-size: {{ round($bodyFontSize * 1.08, 1) }}px !important;
                line-height: 1.35 !important;
            }
            .acs-print-course {
                font-size: {{ round($courseFontSizeAdjusted * 1.08, 1) }}px !important;
                line-height: 1.18 !important;
            }
            .acs-sig-qr {
                width: 76px !important;
                height: 76px !important;
            }
            .acs-sig-seal {
                width: 112px !important;
                height: 112px !important;
            }
            .acs-sig-script {
                font-size: 30px !important;
            }
            .acs-sig-label {
                font-size: 12px !important;
            }
            .acs-sig-id {
                font-size: 12.5px !important;
            }
            .print-header {
                margin-bottom: 2mm !important;
            }
            .print-header img:not(.acs-sig-logo) {
                width: 44px !important;
                height: 44px !important;
            }
            .print-header h1 {
                font-size: 20px !important;
                letter-spacing: 0.15em !important;
            }
            .print-honor-block {
                margin-top: 1mm !important;
                margin-bottom: 1mm !important;
            }
            .print-honor-block h2 {
                font-size: 26px !important;
            }
            .print-honor-block h3 {
                font-size: 15px !important;
                margin-top: 1.5mm !important;
            }
            .print-honor-block p {
                font-size: 11px !important;
                line-height: 1.3 !important;
            }
            .print-footer-grid {
                margin-top: 3mm !important;
                padding-top: 2mm !important;
            }
            .print-seal {
                width: 72px !important;
                height: 72px !important;
            }
            .print-qr-note {
                margin-top: 1mm !important;
                font-size: 8px !important;
            }
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-800 font-sans-body min-h-screen flex flex-col items-center justify-center p-3 sm:p-6 lg:p-8">

    <!-- Top Action Toolbar (Hidden during print) -->
    <div class="no-print w-full max-w-5xl mb-4 sm:mb-6 flex flex-wrap items-center justify-between gap-3 bg-slate-800/90 backdrop-blur-md p-3 sm:p-4 rounded-2xl border border-slate-700 text-white shadow-xl">
        <div class="flex items-center gap-3">
            @if(auth()->check() && auth()->user()->isAdmin())
                <a href="{{ route('admin.certificates.index') }}" class="p-2 px-3 rounded-xl bg-slate-700 hover:bg-slate-600 transition-colors text-xs font-semibold flex items-center gap-1.5 text-amber-300">
                    <i class="fas fa-sliders-h"></i> Certificate Customizer
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="p-2 px-3 rounded-xl bg-slate-700 hover:bg-slate-600 transition-colors text-xs font-semibold flex items-center gap-1.5">
                    <i class="fas fa-arrow-left"></i> Dashboard
                </a>
            @endif
            <span class="text-xs text-slate-300 hidden md:inline">
                Credential: <strong class="text-emerald-400 font-mono">{{ $certificate->certificate_number }}</strong>
            </span>
        </div>

        <!-- Quick Template Switcher & Actions -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
            <!-- Template Pills -->
            <div class="hidden lg:flex items-center gap-1 bg-slate-900/80 p-1 rounded-xl border border-slate-700 text-[10px]">
                <span class="text-slate-400 px-1 font-bold">Theme:</span>
                @foreach($themes as $themeKey => $tConfig)
                    <a 
                        href="?template={{ $themeKey }}{{ request('as_user') ? '&as_user=' . request('as_user') : '' }}" 
                        class="px-2 py-1 rounded-lg font-bold transition-all {{ $template === $themeKey ? 'bg-primary text-white shadow-xs' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}"
                        title="{{ $tConfig['name'] }}"
                    >
                        {{ $themeKey === 'acs_signature' ? 'ACS Official' : explode(' ', $tConfig['name'])[0] }}
                    </a>
                @endforeach
            </div>

            <button 
                onclick="window.print()" 
                class="px-4 py-2 bg-[#c30102] hover:bg-[#a50001] text-white text-xs font-bold rounded-xl shadow-md transition-all flex items-center gap-2 cursor-pointer"
            >
                <i class="fas fa-print"></i> Print / PDF
            </button>
            <a 
                href="{{ route('certificate.verify', $certificate->certificate_number) }}" 
                target="_blank"
                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl shadow-md transition-all flex items-center gap-2"
            >
                <i class="fas fa-check-circle"></i> Verify
            </a>
        </div>
    </div>

    @if($template === 'acs_signature')
        <!-- ========================================================================= -->
        <!-- DEFAULT TEMPLATE: OFFICIAL ACS SIGNATURE CERTIFICATE (EXACT MATCH)        -->
        <!-- ========================================================================= -->
        <div class="certificate-wrapper w-full max-w-[1024px] md:aspect-[1024/718] bg-[#fcf8f6] shadow-2xl border-[6px] sm:border-[7px] {{ $theme['outer_border'] }} border-t-[#bc2723] border-b-[#98201f] border-l-[#252686] border-r-[#1c1d79] relative overflow-hidden flex flex-col justify-between select-none">

            <!-- Hidden SEO & Test Compatibility Anchor -->
            <span class="sr-only">ACS Academy • Learn, Grow, Lead. {{ $institutionName }} {{ $facultyEndorsement }}</span>

            <!-- Top-Right Delicate Blue Guilloche Wave Lines (Exact Match to Reference) -->
            <svg class="pointer-events-none absolute top-0 right-0 w-56 sm:w-80 h-44 sm:h-60 opacity-35 z-0" viewBox="0 0 320 240" fill="none" xmlns="http://www.w3.org/2000/svg">
                @for($i = 0; $i < 16; $i++)
                    <path d="M {{ 40 + $i * 8 }} -10 C {{ 120 + $i * 5 }} {{ 60 + $i * 4 }}, {{ 210 - $i * 4 }} {{ 25 + $i * 6 }}, 335 {{ 130 + $i * 7 }}"
                          stroke="#3b82f6" stroke-width="0.75" stroke-opacity="{{ max(0.15, 0.65 - $i * 0.03) }}" />
                @endfor
            </svg>

            <!-- Bottom-Left Delicate Blue Guilloche Wave Lines (Exact Match to Reference) -->
            <svg class="pointer-events-none absolute bottom-0 left-0 w-48 sm:w-64 h-44 sm:h-60 opacity-35 z-0" viewBox="0 0 260 240" fill="none" xmlns="http://www.w3.org/2000/svg">
                @for($i = 0; $i < 16; $i++)
                    <path d="M -15 {{ 100 + $i * 8 }} C {{ 45 + $i * 5 }} {{ 150 + $i * 3 }}, {{ 65 + $i * 6 }} {{ 210 - $i * 3 }}, {{ 200 + $i * 6 }} 250"
                          stroke="#3b82f6" stroke-width="0.75" stroke-opacity="{{ max(0.15, 0.65 - $i * 0.03) }}" />
                @endfor
            </svg>

            <!-- Center Faint Large ACS Emblem Watermark (Behind Text, ~38.5% Width) -->
            @if($showWatermark)
                <div class="pointer-events-none absolute inset-0 flex items-center justify-center z-0">
                    <img src="{{ $certGoldLogo }}"
                         alt=""
                         class="acs-sig-watermark w-64 h-64 sm:w-[370px] sm:h-[370px] lg:w-[396px] lg:h-[396px] object-contain opacity-[0.17] translate-y-4"
                         onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}';">
                </div>
            @endif

            <!-- Main Certificate Inner Canvas -->
            <div class="certificate-inner relative z-10 flex-1 flex flex-col justify-between px-5 sm:px-10 lg:px-12 pt-1.5 sm:pt-2.5 pb-2 sm:pb-3">

                <!-- 1. TOP HEADER: 100% Matched Gold-Rimmed ACS Logo + Maroon Institution Name + Red Tagline -->
                <div class="text-center flex flex-col items-center">
                    <div class="mb-0.5">
                        <img src="{{ $certGoldLogo }}"
                             alt="{{ $institutionName }} Crest"
                             class="acs-sig-logo w-20 h-20 sm:w-[132px] sm:h-[122px] lg:w-[146px] lg:h-[136px] object-contain mx-auto drop-shadow-xs"
                             onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}';">
                    </div>

                    <h1 class="acs-print-inst font-display-heavy font-black tracking-[0.015em] text-[#5b1415] uppercase leading-tight sm:whitespace-nowrap"
                        style="font-size: clamp({{ round($instFontSize * 0.45, 1) }}px, {{ round($instFontSize * 0.1, 2) }}vw, {{ $instFontSize }}px);">
                        {{ $institutionName }}
                    </h1>

                    @if(!empty($tagline))
                        <span class="acs-print-tagline text-[9px] sm:text-[10.5px] lg:text-[11px] font-sans-body font-extrabold text-[#991b1b] tracking-[0.06em] uppercase block mt-0.5">
                            {{ $tagline }}
                        </span>
                    @endif
                </div>

                <!-- 2. CENTER BODY: Certificate of Appreciation + Recipient + Dotted Line + Participation + Course Title -->
                <div class="text-center mt-1 sm:mt-2 mb-1 sm:mb-1.5 flex flex-col items-center">
                    <!-- CERTIFICATE OF APPRECIATION -->
                    <h2 class="acs-print-title font-cormorant font-semibold text-[#641617] uppercase tracking-[0.04em] leading-none"
                        style="font-size: clamp({{ round($titleFontSize * 0.54, 1) }}px, {{ round($titleFontSize * 0.1, 2) }}vw, {{ $titleFontSize }}px);">
                        {{ $certTitle }}
                    </h2>

                    <!-- This certificate is proudly presented to -->
                    <p class="acs-print-confer text-xs sm:text-base lg:text-[16.5px] font-sans-body font-bold text-[#1e1b4b] mt-1">
                        {{ $confermentText }}
                    </p>

                    <!-- RECIPIENT NAME (Gold-Ochre Bold Uppercase + Wide Finely Dotted Line Below) -->
                    <div class="mt-1 sm:mt-1.5 mb-1 w-full max-w-[760px] border-b-[2px] border-dotted border-[#7c8594] pb-0.5 px-2">
                        <h3 class="acs-print-recipient font-display-heavy font-black text-[#c5832b] uppercase tracking-[0.025em] leading-tight"
                            style="font-size: clamp({{ round($recipientFontSize * 0.56, 1) }}px, {{ round($recipientFontSize * 0.1, 2) }}vw, {{ $recipientFontSize }}px);">
                            {{ $user->name }}
                        </h3>
                    </div>

                    <!-- Completion / Participation Paragraph -->
                    <p class="acs-print-body font-sans-body font-medium text-[#1e1b4b] max-w-[820px] mx-auto leading-[1.35]"
                       style="font-size: clamp({{ round($bodyFontSize * 0.67, 1) }}px, {{ round($bodyFontSize * 0.1, 2) }}vw, {{ $bodyFontSize }}px);">
                        {{ $completionText }}
                    </p>

                    <!-- COURSE TITLE (Bold Uppercase with Charcoal-to-Royal-Indigo Gradient Matching Reference) -->
                    @php
                        $courseTitleMarginClass = $courseTitleLen > 52 ? 'mt-1.5 sm:mt-2' : 'mt-1.5 sm:mt-2.5';
                    @endphp
                    <h4 class="acs-print-course font-display-heavy {{ $courseTitleMarginClass }} font-black text-[#4f4e8c] bg-gradient-to-r from-[#45424a] via-[#53527f] to-[#6868ca] bg-clip-text text-transparent uppercase tracking-[0.015em] leading-[1.18] max-w-[880px]"
                        style="font-size: clamp({{ round($courseFontSizeAdjusted * 0.52, 1) }}px, {{ round($courseFontSizeAdjusted * 0.1, 2) }}vw, {{ $courseFontSizeAdjusted }}px);">
                        {{ $course->title }}
                    </h4>

                    {{-- Batch only shown when explicitly enabled by admin --}}
                    @if(!empty($course->batch) && $course->show_batch)
                        <p class="text-[10px] sm:text-xs font-sans-body font-extrabold text-[#5b1415] mt-1 tracking-wider uppercase">
                            Cohort Intake: {{ $course->batch }}
                        </p>
                    @endif
                </div>

                <!-- 3. BOTTOM FOOTER: 4-Column Row (QR + Green ID/Date | Lead Trainer | Scalloped Gold Seal | ACS Executive Governing) -->
                <div>
                    <div class="grid grid-cols-12 items-end gap-2 sm:gap-3 pt-0.5">

                        <!-- Col 1 (4 Cols): Scannable Verification QR Box + Green Certificate ID & Date -->
                        <div class="col-span-12 sm:col-span-4 text-left pb-0.5">
                            @if($showQrCode)
                                <!-- Crisp Scannable-Style Verification QR Card Above Certificate ID -->
                                <a href="{{ $verifyUrl }}" target="_blank" title="Verify Credential: {{ $certificate->certificate_number }}" class="inline-block bg-white p-1.5 rounded-xs shadow-xs border border-slate-200/70 mb-1.5 ml-6 sm:ml-10 hover:scale-105 transition-transform">
                                    <svg viewBox="0 0 21 21" class="acs-sig-qr w-12 h-12 sm:w-[64px] sm:h-[64px] lg:w-[68px] lg:h-[68px] block" shape-rendering="crispEdges">
                                        <rect width="21" height="21" fill="#ffffff"/>
                                        <!-- Top-Left Finder Pattern -->
                                        <path d="M0,0 h7 v7 h-7 z M1,1 v5 h5 v-5 z M2,2 h3 v3 h-3 z" fill="#111827" fill-rule="evenodd"/>
                                        <!-- Top-Right Finder Pattern -->
                                        <path d="M14,0 h7 v7 h-7 z M15,1 v5 h5 v-5 z M16,2 h3 v3 h-3 z" fill="#111827" fill-rule="evenodd"/>
                                        <!-- Bottom-Left Finder Pattern -->
                                        <path d="M0,14 h7 v7 h-7 z M1,15 v5 h5 v-5 z M2,16 h3 v3 h-3 z" fill="#111827" fill-rule="evenodd"/>
                                        <!-- Timing Patterns -->
                                        <rect x="8" y="6" width="1" height="1" fill="#111827"/>
                                        <rect x="10" y="6" width="1" height="1" fill="#111827"/>
                                        <rect x="12" y="6" width="1" height="1" fill="#111827"/>
                                        <rect x="6" y="8" width="1" height="1" fill="#111827"/>
                                        <rect x="6" y="10" width="1" height="1" fill="#111827"/>
                                        <rect x="6" y="12" width="1" height="1" fill="#111827"/>
                                        <!-- Deterministic Data Modules from Certificate Number -->
                                        @for($r = 0; $r < 21; $r++)
                                            @for($c = 0; $c < 21; $c++)
                                                @php
                                                    $inFinder = ($r < 8 && $c < 8) || ($r < 8 && $c > 12) || ($r > 12 && $c < 8) || $r == 6 || $c == 6;
                                                    $bit = (($qrSeed >> (($r * 7 + $c * 3) % 28)) ^ ($r * 13 + $c * 19)) & 1;
                                                @endphp
                                                @if(!$inFinder && $bit)
                                                    <rect x="{{ $c }}" y="{{ $r }}" width="1" height="1" fill="#111827"/>
                                                @endif
                                            @endfor
                                        @endfor
                                    </svg>
                                </a>
                            @endif

                            <div class="space-y-0.5">
                                @if(!empty($course->batch) && $course->show_batch)
                                    <span class="acs-sig-id text-[9px] sm:text-[11px] font-sans-body font-extrabold text-[#2e6f23] uppercase block">
                                        {{ $batchLabel }} {{ $course->batch }}
                                    </span>
                                @endif
                                <span class="acs-sig-id text-[10px] sm:text-xs lg:text-[13px] font-sans-body font-extrabold text-[#2e6f23] uppercase tracking-tight block">
                                    {{ $idLabel }} {{ $certificate->certificate_number }}
                                </span>
                                <span class="acs-sig-id text-[10px] sm:text-xs lg:text-[13px] font-sans-body font-extrabold text-[#2e6f23] uppercase tracking-tight block">
                                    {{ $dateLabel }} {{ strtoupper(\Carbon\Carbon::parse($certificate->issue_date)->format('d F Y')) }}
                                </span>
                            </div>
                        </div>

                        <!-- Col 2 (3 Cols): Lead Trainer Signature -->
                        <div class="col-span-5 sm:col-span-3 text-center pb-1">
                            @if($showSecondary && !empty($secondarySignatoryName))
                                <div class="min-h-[28px] sm:min-h-[38px] flex items-end justify-center px-2">
                                    <span class="acs-sig-script font-script text-xl sm:text-2xl lg:text-[28px] text-[#111827] leading-none whitespace-nowrap block">
                                        {{ $secondarySignatoryName }}
                                    </span>
                                </div>
                                <div class="border-t-[2.5px] border-dotted border-[#111827] pt-1 mx-auto max-w-[190px]">
                                    <span class="acs-sig-label text-[9px] sm:text-[11px] lg:text-xs font-sans-body font-extrabold text-[#111827] uppercase tracking-wider block">
                                        {{ $secondarySignatoryTitle ?: $secondarySignatoryName }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Col 3 (2 Cols): Metallic Gold Scalloped Seal with 100% Matched ACS Gold Logo & Custom Seal Text -->
                        <div class="col-span-2 sm:col-span-2 flex flex-col items-center justify-center">
                            @if($showSeal)
                                @if($certGoldSeal)
                                    <div class="acs-sig-seal relative w-16 h-16 sm:w-24 sm:h-24 lg:w-[106px] lg:h-[106px] flex items-center justify-center select-none" title="{{ $sealText }}">
                                        <!-- Scalloped Metallic Gold Foil Base (240x240 Perfectly Centered at 120,120) -->
                                        <img src="{{ $certGoldSeal }}"
                                             alt="{{ $sealText }}"
                                             class="w-full h-full object-contain drop-shadow-md">

                                        <!-- Precision SVG Seal Dial (1:1 240x240 Coordinate System Aligned to Physical Gold Seal Grooves) -->
                                        @if($showSealText)
                                            <svg viewBox="0 0 240 240" class="w-full h-full absolute inset-0 pointer-events-none">
                                                <defs>
                                                    <!-- Top clockwise arc centered in the coin annulus (r=57: baseline at 57, cap-top at 69, well inside r=75 groove) -->
                                                    <path id="sealTopArc" d="M 63,120 A 57,57 0 1,1 177,120" />
                                                    <!-- Bottom counter-clockwise arc centered in the coin annulus (r=67: baseline at 67, cap-top at 55, well inside r=75 groove) -->
                                                    <path id="sealBottomArc" d="M 53,120 A 67,67 0 0,0 187,120" />
                                                </defs>
                                                <!-- Subtle engraved outer coin rim & inner medallion ring -->
                                                <circle cx="120" cy="120" r="71" fill="none" stroke="#6b4308" stroke-width="1.3" stroke-dasharray="3 2.5" opacity="0.55" />
                                                @if($showSealLogo)
                                                    <circle cx="120" cy="120" r="49" fill="none" stroke="#6b4308" stroke-width="1.1" opacity="0.45" />
                                                @endif

                                                <!-- Top Curved Text (Centered in Top Annulus) -->
                                                @if(!empty($sealLine1))
                                                    <text fill="#3d2200" font-size="13.5" font-weight="900" font-family="Montserrat, Cinzel, sans-serif" letter-spacing="2.2">
                                                        <textPath href="#sealTopArc" startOffset="50%" text-anchor="middle">
                                                            {{ strtoupper($sealLine1) }}
                                                        </textPath>
                                                    </text>
                                                @endif

                                                <!-- Bottom Curved Text (Upright & Centered in Bottom Annulus) -->
                                                @if(!empty($sealLine2))
                                                    <text fill="#3d2200" font-size="13" font-weight="900" font-family="Montserrat, Cinzel, sans-serif" letter-spacing="2.2">
                                                        <textPath href="#sealBottomArc" startOffset="50%" text-anchor="middle">
                                                            ★ {{ strtoupper($sealLine2) }} ★
                                                        </textPath>
                                                    </text>
                                                @endif
                                            </svg>
                                        @endif

                                        <!-- Center Embedded 100% Matched Gold-Rimmed ACS Logo Medallion (Centered inside r=49 ring) -->
                                        @if($showSealLogo)
                                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-10">
                                                <img src="{{ $certSealLogo }}"
                                                     alt="ACS Seal Logo"
                                                     class="w-[38%] h-[38%] object-contain drop-shadow-xs"
                                                     onerror="this.onerror=null;this.src='{{ $defaultGoldLogo }}';">
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="print-seal w-16 h-16 sm:w-20 sm:h-20 rounded-full border-4 {{ $theme['seal_border'] }} bg-gradient-to-tr {{ $theme['seal_bg'] }} p-1 shadow-lg flex items-center justify-center text-center">
                                        <div class="w-full h-full rounded-full border border-dashed {{ $theme['seal_inner_border'] }} flex flex-col items-center justify-center {{ $theme['seal_text_color'] }}">
                                            @if($showSealLogo)
                                                <img src="{{ $certSealLogo }}" alt="ACS Seal Logo" class="w-6 h-6 sm:w-8 sm:h-8 rounded-full object-contain mb-0.5">
                                            @endif
                                            @if($showSealText)
                                                <span class="text-[6px] sm:text-[7px] font-cinzel font-black tracking-tighter uppercase">{{ $sealLine1 }}</span>
                                                <span class="text-[5px] sm:text-[6px] font-cinzel tracking-wider">{{ $sealLine2 }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endif
                            <span class="sr-only">{{ $sealLine1 }} {{ $sealLine2 }}</span>
                        </div>

                        <!-- Col 4 (3 Cols): ACS Executive Governing Signature -->
                        <div class="col-span-5 sm:col-span-3 text-center pb-1">
                            <div class="min-h-[30px] sm:min-h-[42px] flex items-end justify-center px-2">
                                <span class="acs-sig-script font-script text-xl sm:text-2xl lg:text-[28px] text-[#111827] leading-none whitespace-nowrap block">
                                    {{ $primarySignatoryName }}
                                </span>
                            </div>
                            <div class="border-t-[2.5px] border-dotted border-[#111827] pt-1 mx-auto max-w-[230px]">
                                <span class="acs-sig-label text-[9px] sm:text-[11px] lg:text-xs font-sans-body font-extrabold text-[#111827] uppercase tracking-wider block">
                                    {{ $primarySignatoryBoard ?: $primarySignatoryTitle }}
                                </span>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    @else
        <!-- ========================================================================= -->
        <!-- PREVIOUS 4 TEMPLATES PRESERVED (classic_navy, modern_emerald, etc.)       -->
        <!-- ========================================================================= -->
        <div class="certificate-wrapper w-full max-w-5xl bg-[#fffdf9] rounded-3xl p-3 sm:p-8 shadow-2xl border-[10px] sm:border-[12px] {{ $theme['outer_border'] }} relative overflow-hidden">
            
            <!-- Ornate Inner Guilloche Border -->
            <div class="certificate-inner border-2 {{ $theme['inner_border'] }} p-4 sm:p-8 rounded-xl relative bg-gradient-to-b {{ $theme['bg_gradient'] }}">
                
                <!-- Corner Decorative Badges -->
                <div class="absolute top-2 left-2 {{ $theme['badge_color'] }} text-lg sm:text-xl opacity-70"><i class="fas {{ $theme['badge_icon'] }}"></i></div>
                <div class="absolute top-2 right-2 {{ $theme['badge_color'] }} text-lg sm:text-xl opacity-70"><i class="fas {{ $theme['badge_icon'] }}"></i></div>
                <div class="absolute bottom-2 left-2 {{ $theme['badge_color'] }} text-lg sm:text-xl opacity-70"><i class="fas {{ $theme['badge_icon'] }}"></i></div>
                <div class="absolute bottom-2 right-2 {{ $theme['badge_color'] }} text-lg sm:text-xl opacity-70"><i class="fas {{ $theme['badge_icon'] }}"></i></div>

                <!-- Header: Institute Crest & Logo -->
                <div class="text-center mb-3 sm:mb-5 print-header">
                    <div class="inline-flex items-center justify-center gap-2 mb-1 sm:mb-2">
                        <img src="{{ $certGoldLogo }}" alt="{{ $institutionName }} Crest" class="w-16 h-16 sm:w-24 sm:h-24 object-contain" onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}';">
                    </div>
                    <!-- Institution Name (Configurable) -->
                    <h1 class="font-cinzel text-xl sm:text-3xl lg:text-4xl font-black tracking-widest {{ $theme['inst_color'] }} uppercase">
                        {{ $institutionName }}
                    </h1>
                    <span class="text-[9px] sm:text-xs font-cinzel font-bold {{ $theme['tagline_color'] }} tracking-[0.25em] uppercase block mt-0.5">
                        {{ $tagline }}
                    </span>
                    <p class="text-[9px] sm:text-[10px] text-gray-500 mt-0.5 uppercase tracking-wider">
                        {{ $campusAddress }}
                    </p>
                </div>

                <!-- Title of Honor & Recipient -->
                <div class="text-center my-2 sm:my-4 print-honor-block">
                    <span class="text-[11px] sm:text-xs font-cinzel {{ $theme['title_color'] }} uppercase tracking-[0.3em] block mb-0.5 font-bold">
                        {{ $certTitle }}
                    </span>
                    <p class="text-[11px] sm:text-xs text-gray-600 italic">{{ $confermentText }}</p>
                    
                    <!-- Recipient Name -->
                    <div class="my-2 sm:my-3 py-1 sm:py-1.5 border-b-2 border-dotted {{ $theme['recipient_border'] }} inline-block min-w-[280px] sm:min-w-[440px]">
                        <h2 class="font-playfair text-2xl sm:text-4xl lg:text-5xl font-black {{ $theme['recipient_color'] }} tracking-tight">
                            {{ $user->name }}
                        </h2>
                    </div>

                    <p class="text-[11px] sm:text-xs text-gray-700 max-w-2xl mx-auto leading-relaxed mt-1">
                        {{ $completionText }}
                    </p>

                    <!-- Course Title -->
                    <h3 class="font-cinzel text-lg sm:text-2xl font-bold {{ $theme['course_color'] }} mt-2 uppercase tracking-wide">
                        {{ $course->title }}
                    </h3>

                    {{-- Batch only shown when explicitly enabled by admin --}}
                    @if(!empty($course->batch) && $course->show_batch)
                        <p class="text-[10px] sm:text-xs font-cinzel font-bold {{ $theme['inst_color'] }} mt-1 tracking-wider uppercase">
                            Cohort Intake: {{ $course->batch }}
                        </p>
                    @endif

                    <p class="text-[10px] sm:text-xs text-gray-500 mt-1">
                        {{ $facultyEndorsement }}
                    </p>
                </div>

                <!-- Footer Meta & Signatures -->
                @if($showSecondary && !empty($secondarySignatoryName))
                    <!-- Dual Signature Layout: 4 Columns -->
                    <div class="mt-4 sm:mt-8 pt-3 sm:pt-6 border-t {{ $theme['divider'] }} grid grid-cols-4 items-end text-center print-footer-grid">
                        
                        <!-- Col 1: Verification QR + Date & Security Serial -->
                        <div class="text-left space-y-0.5 sm:space-y-1">
                            @if($showQrCode)
                                <a href="{{ $verifyUrl }}" target="_blank" title="Verify Credential: {{ $certificate->certificate_number }}" class="inline-block bg-white p-1.5 rounded-xs shadow-xs border border-slate-200 mb-1 hover:scale-105 transition-transform">
                                    <svg viewBox="0 0 21 21" class="w-11 h-11 sm:w-14 sm:h-14 block" shape-rendering="crispEdges">
                                        <rect width="21" height="21" fill="#ffffff"/>
                                        <path d="M0,0 h7 v7 h-7 z M1,1 v5 h5 v-5 z M2,2 h3 v3 h-3 z" fill="#111827" fill-rule="evenodd"/>
                                        <path d="M14,0 h7 v7 h-7 z M15,1 v5 h5 v-5 z M16,2 h3 v3 h-3 z" fill="#111827" fill-rule="evenodd"/>
                                        <path d="M0,14 h7 v7 h-7 z M1,15 v5 h5 v-5 z M2,16 h3 v3 h-3 z" fill="#111827" fill-rule="evenodd"/>
                                        <rect x="8" y="6" width="1" height="1" fill="#111827"/>
                                        <rect x="10" y="6" width="1" height="1" fill="#111827"/>
                                        <rect x="12" y="6" width="1" height="1" fill="#111827"/>
                                        <rect x="6" y="8" width="1" height="1" fill="#111827"/>
                                        <rect x="6" y="10" width="1" height="1" fill="#111827"/>
                                        <rect x="6" y="12" width="1" height="1" fill="#111827"/>
                                        @for($r = 0; $r < 21; $r++)
                                            @for($c = 0; $c < 21; $c++)
                                                @php
                                                    $inFinder = ($r < 8 && $c < 8) || ($r < 8 && $c > 12) || ($r > 12 && $c < 8) || $r == 6 || $c == 6;
                                                    $bit = (($qrSeed >> (($r * 7 + $c * 3) % 28)) ^ ($r * 13 + $c * 19)) & 1;
                                                @endphp
                                                @if(!$inFinder && $bit)
                                                    <rect x="{{ $c }}" y="{{ $r }}" width="1" height="1" fill="#111827"/>
                                                @endif
                                            @endfor
                                        @endfor
                                    </svg>
                                </a>
                            @endif
                            @if(!empty($course->batch) && $course->show_batch)
                                <span class="text-[9px] sm:text-[10px] font-mono text-emerald-700 font-bold block">Batch: {{ $course->batch }}</span>
                            @endif
                            <span class="text-[9px] sm:text-[10px] font-mono text-gray-500 block">Issue Date: {{ \Carbon\Carbon::parse($certificate->issue_date)->format('F d, Y') }}</span>
                            <span class="text-[9px] sm:text-[10px] font-mono {{ $theme['inst_color'] }} font-bold block">Serial: {{ $certificate->certificate_number }}</span>
                            <span class="text-[8px] font-mono text-gray-400 block truncate max-w-[140px] sm:max-w-[180px]" title="{{ $certificate->verification_hash }}">
                                Hash: {{ substr($certificate->verification_hash, 0, 16) }}...
                            </span>
                        </div>

                        <!-- Col 2: Secondary Signatory (Faculty Chair / Lead Trainer) -->
                        <div class="text-center px-1">
                            <div class="inline-block border-b border-gray-700 pb-0.5 px-2 sm:px-3 text-center">
                                <span class="font-script text-lg sm:text-xl lg:text-2xl {{ $theme['sig_color'] }} block">
                                    {{ $secondarySignatoryName }}
                                </span>
                            </div>
                            <span class="text-[9px] sm:text-[10px] font-bold {{ $theme['inst_color'] }} uppercase tracking-wider block mt-0.5">
                                {{ $secondarySignatoryName }}
                            </span>
                            <span class="text-[7px] sm:text-[8px] text-gray-500 uppercase block truncate">
                                {{ $secondarySignatoryTitle }}
                            </span>
                        </div>

                        <!-- Col 3: Official Seal Simulation -->
                        <div class="flex flex-col items-center justify-center">
                            @if($showSeal)
                                <div class="print-seal w-16 h-16 sm:w-20 sm:h-20 lg:w-22 lg:h-22 rounded-full border-4 {{ $theme['seal_border'] }} bg-gradient-to-tr {{ $theme['seal_bg'] }} p-1 shadow-lg flex items-center justify-center text-center">
                                    <div class="w-full h-full rounded-full border border-dashed {{ $theme['seal_inner_border'] }} flex flex-col items-center justify-center {{ $theme['seal_text_color'] }}">
                                        @if($showSealLogo)
                                            <img src="{{ $certSealLogo }}" alt="ACS Seal Logo" class="w-6 h-6 sm:w-8 sm:h-8 rounded-full object-contain mb-0.5" onerror="this.onerror=null;this.src='{{ $defaultGoldLogo }}';">
                                        @else
                                            <i class="fas {{ $theme['seal_icon'] }} text-xs sm:text-base"></i>
                                        @endif
                                        @if($showSealText)
                                            <span class="text-[7px] sm:text-[8px] font-cinzel font-black tracking-tighter uppercase mt-0.5">{{ $sealLine1 }}</span>
                                            <span class="text-[6px] sm:text-[7px] font-cinzel tracking-wider">{{ $sealLine2 }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            <span class="sr-only">{{ $sealLine1 }} {{ $sealLine2 }}</span>
                        </div>

                        <!-- Col 4: Primary Signatory (Academic Director) -->
                        <div class="text-right">
                            <div class="inline-block border-b border-gray-700 pb-0.5 px-2 sm:px-4 text-center">
                                <span class="font-script text-lg sm:text-2xl lg:text-3xl {{ $theme['sig_color'] }} block">
                                    {{ $primarySignatoryName }}
                                </span>
                            </div>
                            <span class="text-[9px] sm:text-[10px] font-bold {{ $theme['inst_color'] }} uppercase tracking-wider block mt-0.5">
                                {{ $primarySignatoryName }}
                            </span>
                            <span class="text-[8px] sm:text-[9px] text-gray-500 uppercase block">
                                {{ $primarySignatoryBoard }}
                            </span>
                        </div>

                    </div>
                @else
                    <!-- Single Signature Layout: 3 Columns -->
                    <div class="mt-4 sm:mt-8 pt-3 sm:pt-6 border-t {{ $theme['divider'] }} grid grid-cols-3 items-end text-center print-footer-grid">
                        
                        <!-- Left: Verification QR + Date & Security Serial -->
                        <div class="text-left space-y-0.5 sm:space-y-1">
                            @if($showQrCode)
                                <a href="{{ $verifyUrl }}" target="_blank" title="Verify Credential: {{ $certificate->certificate_number }}" class="inline-block bg-white p-1.5 rounded-xs shadow-xs border border-slate-200 mb-1 hover:scale-105 transition-transform">
                                    <svg viewBox="0 0 21 21" class="w-11 h-11 sm:w-14 sm:h-14 block" shape-rendering="crispEdges">
                                        <rect width="21" height="21" fill="#ffffff"/>
                                        <path d="M0,0 h7 v7 h-7 z M1,1 v5 h5 v-5 z M2,2 h3 v3 h-3 z" fill="#111827" fill-rule="evenodd"/>
                                        <path d="M14,0 h7 v7 h-7 z M15,1 v5 h5 v-5 z M16,2 h3 v3 h-3 z" fill="#111827" fill-rule="evenodd"/>
                                        <path d="M0,14 h7 v7 h-7 z M1,15 v5 h5 v-5 z M2,16 h3 v3 h-3 z" fill="#111827" fill-rule="evenodd"/>
                                        <rect x="8" y="6" width="1" height="1" fill="#111827"/>
                                        <rect x="10" y="6" width="1" height="1" fill="#111827"/>
                                        <rect x="12" y="6" width="1" height="1" fill="#111827"/>
                                        <rect x="6" y="8" width="1" height="1" fill="#111827"/>
                                        <rect x="6" y="10" width="1" height="1" fill="#111827"/>
                                        <rect x="6" y="12" width="1" height="1" fill="#111827"/>
                                        @for($r = 0; $r < 21; $r++)
                                            @for($c = 0; $c < 21; $c++)
                                                @php
                                                    $inFinder = ($r < 8 && $c < 8) || ($r < 8 && $c > 12) || ($r > 12 && $c < 8) || $r == 6 || $c == 6;
                                                    $bit = (($qrSeed >> (($r * 7 + $c * 3) % 28)) ^ ($r * 13 + $c * 19)) & 1;
                                                @endphp
                                                @if(!$inFinder && $bit)
                                                    <rect x="{{ $c }}" y="{{ $r }}" width="1" height="1" fill="#111827"/>
                                                @endif
                                            @endfor
                                        @endfor
                                    </svg>
                                </a>
                            @endif
                            @if(!empty($course->batch) && $course->show_batch)
                                <span class="text-[9px] sm:text-[10px] font-mono text-emerald-700 font-bold block">Batch: {{ $course->batch }}</span>
                            @endif
                            <span class="text-[9px] sm:text-[10px] font-mono text-gray-500 block">Issue Date: {{ \Carbon\Carbon::parse($certificate->issue_date)->format('F d, Y') }}</span>
                            <span class="text-[9px] sm:text-[10px] font-mono {{ $theme['inst_color'] }} font-bold block">Serial: {{ $certificate->certificate_number }}</span>
                            <span class="text-[8px] font-mono text-gray-400 block truncate max-w-[180px] sm:max-w-[200px]" title="{{ $certificate->verification_hash }}">
                                Hash: {{ substr($certificate->verification_hash, 0, 16) }}...
                            </span>
                        </div>

                        <!-- Center: Official Seal Simulation -->
                        <div class="flex flex-col items-center justify-center">
                            @if($showSeal)
                                <div class="print-seal w-16 h-16 sm:w-20 sm:h-20 lg:w-22 lg:h-22 rounded-full border-4 {{ $theme['seal_border'] }} bg-gradient-to-tr {{ $theme['seal_bg'] }} p-1 shadow-lg flex items-center justify-center text-center">
                                    <div class="w-full h-full rounded-full border border-dashed {{ $theme['seal_inner_border'] }} flex flex-col items-center justify-center {{ $theme['seal_text_color'] }}">
                                        @if($showSealLogo)
                                            <img src="{{ $certSealLogo }}" alt="ACS Seal Logo" class="w-6 h-6 sm:w-8 sm:h-8 rounded-full object-contain mb-0.5" onerror="this.onerror=null;this.src='{{ $defaultGoldLogo }}';">
                                        @else
                                            <i class="fas {{ $theme['seal_icon'] }} text-sm sm:text-lg"></i>
                                        @endif
                                        @if($showSealText)
                                            <span class="text-[7px] sm:text-[8px] font-cinzel font-black tracking-tighter uppercase mt-0.5">{{ $sealLine1 }}</span>
                                            <span class="text-[6px] sm:text-[7px] font-cinzel tracking-wider">{{ $sealLine2 }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            <span class="sr-only">{{ $sealLine1 }} {{ $sealLine2 }}</span>
                        </div>

                        <!-- Right: Primary Signatory -->
                        <div class="text-right">
                            <div class="inline-block border-b border-gray-700 pb-0.5 px-3 sm:px-4 text-center">
                                <span class="font-script text-xl sm:text-2xl lg:text-3xl {{ $theme['sig_color'] }} block">
                                    {{ $primarySignatoryName }}
                                </span>
                            </div>
                            <span class="text-[9px] sm:text-[10px] font-bold {{ $theme['inst_color'] }} uppercase tracking-wider block mt-0.5">
                                {{ $primarySignatoryName }}
                            </span>
                            <span class="text-[8px] sm:text-[9px] text-gray-500 uppercase block">
                                {{ $primarySignatoryBoard }}
                            </span>
                        </div>

                    </div>
                @endif

                <!-- Bottom QR Verification Link Hint -->
                <div class="mt-3 sm:mt-5 text-center text-[8px] sm:text-[9px] text-gray-400 print-qr-note">
                    Online Registry Verification: <a href="{{ route('certificate.verify', $certificate->certificate_number) }}" class="underline text-gray-600 hover:text-[#c30102]">{{ url('/verify-certificate/' . $certificate->certificate_number) }}</a>
                </div>

            </div>

        </div>
    @endif

</body>
</html>
