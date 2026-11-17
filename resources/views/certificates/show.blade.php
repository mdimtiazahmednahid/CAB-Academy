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
    $trainerSigUrl = trim((string) \App\Models\Setting::getVal('certificate_trainer_signature_url', '/images/cert-official/sig-trainer.svg'));
    $governingSigUrl = trim((string) \App\Models\Setting::getVal('certificate_governing_signature_url', '/images/cert-official/sig-governing.svg'));
    $trainerSigSrc = $trainerSigUrl !== '' ? (str_starts_with($trainerSigUrl, 'http') ? $trainerSigUrl : asset(ltrim($trainerSigUrl, '/'))) : asset('images/cert-official/sig-trainer.svg');
    $governingSigSrc = $governingSigUrl !== '' ? (str_starts_with($governingSigUrl, 'http') ? $governingSigUrl : asset(ltrim($governingSigUrl, '/'))) : asset('images/cert-official/sig-governing.svg');
    $idLabel = \App\Models\Setting::getVal('certificate_id_label', 'CERTIFICATE ID:');
    $dateLabel = \App\Models\Setting::getVal('certificate_date_label', 'DATE:');
    $batchLabel = \App\Models\Setting::getVal('certificate_batch_label', 'BATCH:');
    $instFontSize = (float) \App\Models\Setting::getVal('certificate_institution_font_size', '31') ?: 31;
    $titleFontSize = (float) \App\Models\Setting::getVal('certificate_title_font_size', '35') ?: 35;
    $recipientFontSize = (float) \App\Models\Setting::getVal('certificate_recipient_font_size', '32') ?: 32;
    $courseFontSize = (float) \App\Models\Setting::getVal('certificate_course_font_size', '30') ?: 30;
    $bodyFontSize = (float) \App\Models\Setting::getVal('certificate_body_font_size', '16.5') ?: 16.5;
    $titleFont = \App\Models\Setting::getVal('certificate_title_font', 'Cinzel') ?: 'Cinzel';
    $recipientFont = \App\Models\Setting::getVal('certificate_recipient_font', 'Alex Brush') ?: 'Alex Brush';
    $courseFont = \App\Models\Setting::getVal('certificate_course_font', 'Anton') ?: 'Anton';
    $bodyFont = \App\Models\Setting::getVal('certificate_body_font', 'Aleo') ?: 'Aleo';
    $titleFontCss = "'{$titleFont}', 'Cinzel', serif";
    $recipientFontCss = "'{$recipientFont}', 'Alex Brush', cursive";
    $courseFontCss = "'{$courseFont}', 'Anton', sans-serif";
    $bodyFontCss = "'{$bodyFont}', 'Aleo', serif";
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
    <link href="https://fonts.googleapis.com/css2?family=Aleo:wght@400;700&family=Alex+Brush&family=Allura&family=Anton&family=Archivo+Black&family=Cinzel:wght@500;600;700;800;900&family=Cormorant+Garamond:wght@500;600;700&family=Great+Vibes&family=Libre+Baskerville:wght@700&family=Lora:wght@400;700&family=Merriweather:wght@400;700&family=Montserrat:wght@400;500;600;700;800;900&family=Oswald:wght@600;700&family=Pinyon+Script&family=Playfair+Display:ital,wght@0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@700;800&family=Roboto:wght@700&family=Sacramento&display=swap" rel="stylesheet">
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
        .font-aleo { font-family: 'Aleo', serif; }
        .font-anton { font-family: 'Anton', sans-serif; }
        .font-amoresa { font-family: 'Alex Brush', 'Great Vibes', cursive; }
        .font-canva-sans { font-family: 'Plus Jakarta Sans', 'Montserrat', sans-serif; }
        .font-roboto-bold { font-family: 'Roboto', sans-serif; font-weight: 700; }

        .acs-portrait-cert {
            container-type: inline-size;
        }

        @page {
            size: {{ $template === 'acs_signature' ? 'A4 portrait' : 'A4 landscape' }};
            margin: 0;
        }

        @media print {
            html, body {
                display: block !important;
                width: {{ $template === 'acs_signature' ? '210mm' : '297mm' }} !important;
                height: {{ $template === 'acs_signature' ? '297mm' : '210mm' }} !important;
                min-height: 0 !important;
                max-height: {{ $template === 'acs_signature' ? '297mm' : '210mm' }} !important;
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
            @if($template === 'acs_signature')
            .acs-portrait-cert {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                width: 210mm !important;
                height: 297mm !important;
                max-width: 210mm !important;
                max-height: 297mm !important;
                margin: 0 !important;
                padding: 0 !important;
                page-break-inside: avoid !important;
                page-break-before: avoid !important;
                page-break-after: avoid !important;
                break-inside: avoid !important;
                overflow: hidden !important;
            }
            @else
            .certificate-wrapper {
                box-shadow: none !important;
                border: 7px solid {{ $theme['outer_border_hex'] }} !important;
                border-radius: 0 !important;
                width: 297mm !important;
                height: 210mm !important;
                max-height: 210mm !important;
                max-width: 297mm !important;
                margin: 0 auto !important;
                padding: 6mm !important;
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
            @endif
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
<body class="bg-slate-900 text-slate-800 font-sans-body min-h-screen flex flex-col items-center justify-start p-3 sm:p-6 lg:p-8">

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
        <!-- DEFAULT TEMPLATE: EXACT 1:1 BLUE & GOLD MODERN ACS CERTIFICATE (A4 PORTRAIT) -->
        <!-- ========================================================================= -->
        <div class="acs-portrait-cert certificate-wrapper w-full bg-white shadow-2xl relative overflow-hidden select-none mx-auto"
             style="max-width: 794px; aspect-ratio: 595.28 / 841.89; container-type: inline-size;">

            <!-- Hidden SEO & Test Compatibility Anchor -->
            <span class="sr-only">ACS Academy • Learn, Grow, Lead. {{ $institutionName }} {{ $facultyEndorsement }}</span>

            <!-- Left Vertical Royal Blue Gradient Sidebar (13.6% width) -->
            <div class="absolute inset-y-0 left-0 z-0"
                 style="width: 13.6%; background: linear-gradient(180deg, #0b4db4 0%, #07398d 28%, #042868 55%, #0945a4 82%, #04296b 100%);">
            </div>

            <!-- Metallic Gold Outer Frame (Top, Right, Bottom + Left Vertical Divider) -->
            <div class="absolute top-0 right-0 z-10 pointer-events-none"
                 style="left: 13.6%; height: 1.65%; background: linear-gradient(90deg, #e7bb4b 0%, #f5d87e 45%, #d69c39 100%);"></div>
            <div class="absolute bottom-0 right-0 z-10 pointer-events-none"
                 style="left: 13.6%; height: 1.65%; background: linear-gradient(90deg, #d69c39 0%, #f5d87e 55%, #e7bb4b 100%);"></div>
            <div class="absolute top-0 bottom-0 right-0 z-10 pointer-events-none"
                 style="width: 2.35%; background: linear-gradient(180deg, #f3cd6e 0%, #dca43f 35%, #f7d983 68%, #cf9533 100%);"></div>
            <div class="absolute top-0 bottom-0 z-10 pointer-events-none"
                 style="left: 13.6%; width: 2.35%; background: linear-gradient(180deg, #f3cd6e 0%, #dca43f 35%, #f7d983 68%, #cf9533 100%);"></div>

            <!-- Inner Content Area (Right of Blue+Gold Sidebar) -->
            <div class="absolute overflow-hidden flex flex-col"
                 style="top: 1.65%; bottom: 1.65%; left: 15.95%; right: 2.35%; background-color: #f9fafc;">

                <!-- Subtle Geometric Background Texture (Exact PDF bg-blue.jpg) -->
                <img src="{{ asset('images/cert-official/bg-blue.jpg') }}"
                     alt=""
                     class="pointer-events-none absolute inset-0 w-full h-full object-cover z-0"
                     style="opacity: 0.22;">

                <!-- Center Faint Circular ACS Watermark (Exact PDF acs-logo-circle.png) -->
                @if($showWatermark)
                    <div class="pointer-events-none absolute inset-0 flex items-center justify-center z-0" style="transform: translateY(6%);">
                        <img src="{{ asset('images/cert-official/acs-logo-circle.png') }}"
                             alt=""
                             class="h-auto object-contain"
                             style="width: 62%; opacity: 0.14;">
                    </div>
                @endif

                <!-- Foreground Certificate Content (Exact PDF Percentage Coordinates) -->
                <div class="relative z-10 w-full h-full">

                    <!-- 1. Top ACS Circular Crest Logo with Ribbon (Transparent PNG, Exact PDF Position) -->
                    <div class="absolute left-0 right-0 flex justify-center" style="top: 1.5%;">
                        <img src="{{ asset('images/cert-official/acs-logo-top.png') }}"
                             alt="{{ $institutionName }}"
                             class="object-contain"
                             style="width: 34.5%; height: auto;">
                    </div>

                    <!-- 2. CERTIFICATE / OF PARTICIPATION -->
                    <div class="absolute left-0 right-0 text-center" style="top: 18.6%;">
                        <h1 class="font-bold uppercase m-0"
                            style="font-family: {!! $titleFontCss !!}; color: #1c2a4c; font-size: 7.5cqw; line-height: 1.02; letter-spacing: 0.02em;">
                            CERTIFICATE
                        </h1>
                        <p class="font-canva-sans font-bold uppercase m-0"
                           style="color: #1c2a4c; font-size: 3.02cqw; line-height: 1.2; letter-spacing: 0.04em; margin-top: 0.6%;">
                            OF PARTICIPATION
                        </p>
                    </div>

                    <!-- 3. Conferment Text -->
                    <div class="absolute left-0 right-0 text-center" style="top: 29.8%;">
                        <p class="font-bold m-0"
                           style="font-family: {!! $bodyFontCss !!}; color: #1c2a4c; font-size: 3.19cqw; line-height: 1.2;">
                            This certificate is presented to
                        </p>
                    </div>

                    <!-- 4. Recipient Name (Admin-Configurable Font in Gold #ac8444) + Gold Rule -->
                    @php
                        $recipientNameStr = trim((string) $user->name);
                        $recipientLen = mb_strlen($recipientNameStr);
                        $recipientCqw = match (true) {
                            $recipientLen > 30 => '3.7cqw',
                            $recipientLen > 24 => '4.5cqw',
                            $recipientLen > 18 => '5.4cqw',
                            default => '6.8cqw',
                        };
                    @endphp
                    <div class="absolute left-0 right-0 flex flex-col items-center text-center px-[6%]" style="top: 34.8%;">
                        <div class="acs-cert-recipient-name whitespace-nowrap inline-block max-w-full"
                             data-base-cqw="{{ $recipientCqw }}"
                             style="font-family: {!! $recipientFontCss !!}; color: #ac8444; font-size: {{ $recipientCqw }}; line-height: 1.12;">
                            {{ $user->name }}
                        </div>
                        <div style="width: 86%; height: 0.22cqw; background-color: #e7bb4b; margin-top: 0.9%;"></div>
                    </div>

                    <!-- 5. Participation Body Text -->
                    <div class="absolute left-0 right-0 flex justify-center text-center" style="top: 45.4%;">
                        <p class="font-normal m-0"
                           style="font-family: {!! $bodyFontCss !!}; color: #1c2a4c; font-size: 2.32cqw; line-height: 1.42; max-width: 86%;">
                            Successfully participated in the training program, demonstrating active engagement and commitment to professional development.
                        </p>
                    </div>

                    <!-- 6. Course Title (Admin-Configurable Font in #003878) -->
                    <div class="absolute left-0 right-0 text-center" style="top: 54.0%; padding: 0 3.5%;">
                        <h2 class="font-normal uppercase m-0"
                            style="font-family: {!! $courseFontCss !!}; color: #003878; font-size: {{ $courseTitleLen > 48 ? '3.35cqw' : '4.35cqw' }}; line-height: 1.16; letter-spacing: 0.015em;">
                            &ldquo;{{ mb_strtoupper($course->title) }}&rdquo;
                        </h2>
                    </div>

                    <!-- 7. Certificate ID & Date (Emerald Green #23bf70, Roboto Bold, Left-Aligned) -->
                    <div class="absolute text-left font-roboto-bold"
                         style="top: 66.8%; left: 10.5%; right: 4%; color: #23bf70; font-size: 2.47cqw; line-height: 1.42;">
                        @if(!empty($course->batch) && $course->show_batch)
                            <div class="uppercase">{{ $batchLabel }} {{ $course->batch }}</div>
                        @endif
                        <div class="uppercase">CERTIFICATE ID: {{ $certificate->certificate_number }}</div>
                        <div class="uppercase">DATE: {{ $certificate->issue_date ? \Carbon\Carbon::parse($certificate->issue_date)->format('d M Y') : '' }}</div>
                    </div>

                    <!-- 8. Bottom 3-Column Footer: Lead Trainer | Gold Seal | ACS Governing Body -->
                    <div class="absolute left-0 right-0"
                         style="bottom: 6.2%; padding: 0 3.2%; display: grid; grid-template-columns: 38% 20% 42%; align-items: end;">

                        <!-- Left Column: Lead Trainer -->
                        <div style="display: flex; flex-direction: column; align-items: center; text-align: center; padding-bottom: 1.5cqw;">
                            <div style="height: 10.5cqw; width: 100%; display: flex; align-items: flex-end; justify-content: center; margin-bottom: 0.8cqw;">
                                <img src="{{ $trainerSigSrc }}"
                                     alt="Lead Trainer Signature"
                                     style="max-width: 78%; max-height: 10.2cqw; width: auto; height: auto; object-fit: contain;">
                            </div>
                            <div style="width: 84%; height: 0.22cqw; background-color: #e7bb4b;"></div>
                            <span class="font-aleo font-normal uppercase"
                                  style="color: #ac8444; font-size: 2.02cqw; letter-spacing: 0.02em; margin-top: 0.9cqw; line-height: 1.2;">
                                LEAD TRAINER
                            </span>
                            <span class="font-aleo font-bold whitespace-nowrap"
                                  style="color: #1c2a4c; font-size: 1.50cqw; line-height: 1.2; margin-top: 0.35cqw;">
                                Academy of Commercial &amp; Supply Chain
                            </span>
                        </div>

                        <!-- Center Column: Exact Gold Seal with Red Ribbon -->
                        <div style="display: flex; align-items: flex-end; justify-content: center;">
                            @if($showSeal)
                                <img src="{{ asset('images/cert-official/gold-seal.png') }}"
                                     alt="Official ACS Seal"
                                     style="width: 14.8cqw; height: auto; object-fit: contain; display: block; margin: 0 auto;">
                            @endif
                        </div>

                        <!-- Right Column: ACS Governing Body -->
                        <div style="display: flex; flex-direction: column; align-items: center; text-align: center; padding-bottom: 1.5cqw;">
                            <div style="height: 10.5cqw; width: 100%; display: flex; align-items: flex-end; justify-content: center; margin-bottom: 0.8cqw;">
                                <img src="{{ $governingSigSrc }}"
                                     alt="ACS Governing Body Signature"
                                     style="max-width: 78%; max-height: 10.5cqw; width: auto; height: auto; object-fit: contain;">
                            </div>
                            <div style="width: 88%; height: 0.22cqw; background-color: #e7bb4b;"></div>
                            <span class="font-aleo font-normal uppercase"
                                  style="color: #ac8444; font-size: 2.00cqw; letter-spacing: 0.02em; margin-top: 0.9cqw; line-height: 1.2;">
                                ACS GOVORNING BODY
                            </span>
                            <span class="font-aleo font-bold whitespace-nowrap"
                                  style="color: #1c2a4c; font-size: 1.50cqw; line-height: 1.2; margin-top: 0.35cqw;">
                                Academy of Commercial &amp; Supply Chain
                            </span>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Left Edge White QR Code Box (Overlapping Blue Sidebar + Gold Border, Exact PDF Position) -->
            @if($showQrCode)
                <a href="{{ $verifyUrl }}" target="_blank" title="Verify Certificate Authenticity"
                   class="absolute left-0 bg-white z-20 flex items-center justify-center"
                   style="top: 62%; width: 19.8%; aspect-ratio: 118 / 115; padding: 2.2% 2.2% 2.2% 3.2%;">
                    {!! \App\Support\QrCodeGenerator::svg($verifyUrl, '#000000', '#ffffff', 1) !!}
                </a>
            @endif
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
                    <span class="text-[11px] sm:text-xs {{ $theme['title_color'] }} uppercase tracking-[0.3em] block mb-0.5 font-bold"
                          style="font-family: {!! $titleFontCss !!};">
                        {{ $certTitle }}
                    </span>
                    <p class="text-[11px] sm:text-xs text-gray-600 italic" style="font-family: {!! $bodyFontCss !!};">{{ $confermentText }}</p>
                    
                    <!-- Recipient Name -->
                    <div class="my-2 sm:my-3 py-1 sm:py-1.5 border-b-2 border-dotted {{ $theme['recipient_border'] }} inline-block w-full max-w-[92%] sm:max-w-[82%] px-2">
                        <h2 class="acs-cert-recipient-name text-2xl sm:text-4xl lg:text-5xl font-black {{ $theme['recipient_color'] }} tracking-tight whitespace-nowrap inline-block"
                            style="font-family: {!! $recipientFontCss !!};">
                            {{ $user->name }}
                        </h2>
                    </div>

                    <p class="text-[11px] sm:text-xs text-gray-700 max-w-2xl mx-auto leading-relaxed mt-1" style="font-family: {!! $bodyFontCss !!};">
                        {{ $completionText }}
                    </p>

                    <!-- Course Title -->
                    <h3 class="text-lg sm:text-2xl font-bold {{ $theme['course_color'] }} mt-2 uppercase tracking-wide"
                        style="font-family: {!! $courseFontCss !!};">
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
                                <a href="{{ $verifyUrl }}" target="_blank" title="Verify Credential: {{ $certificate->certificate_number }}" class="inline-block bg-white p-1.5 rounded-xs shadow-xs border border-slate-200 mb-1 hover:scale-105 transition-transform w-14 h-14 sm:w-16 sm:h-16">
                                    {!! \App\Support\QrCodeGenerator::svg($verifyUrl, '#111827', '#ffffff', 1) !!}
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
                                <a href="{{ $verifyUrl }}" target="_blank" title="Verify Credential: {{ $certificate->certificate_number }}" class="inline-block bg-white p-1.5 rounded-xs shadow-xs border border-slate-200 mb-1 hover:scale-105 transition-transform w-14 h-14 sm:w-16 sm:h-16">
                                    {!! \App\Support\QrCodeGenerator::svg($verifyUrl, '#111827', '#ffffff', 1) !!}
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

    <script>
        (function () {
            function fitCertRecipientNames() {
                document.querySelectorAll('.acs-cert-recipient-name').forEach(function (el) {
                    var parent = el.parentElement;
                    if (!parent) return;

                    var baseCqw = el.getAttribute('data-base-cqw');
                    if (baseCqw) {
                        el.style.fontSize = baseCqw;
                    } else {
                        el.style.fontSize = '';
                    }

                    var style = window.getComputedStyle(parent);
                    var padLeft = parseFloat(style.paddingLeft) || 0;
                    var padRight = parseFloat(style.paddingRight) || 0;
                    var maxWidth = (parent.clientWidth - padLeft - padRight) * 0.94;
                    if (maxWidth <= 0) return;

                    var currentPx = parseFloat(window.getComputedStyle(el).fontSize) || 24;
                    var guard = 0;
                    while (el.scrollWidth > maxWidth && currentPx > 12 && guard < 40) {
                        var ratio = maxWidth / el.scrollWidth;
                        var nextPx = Math.max(12, Math.floor(currentPx * ratio * 10) / 10);
                        if (nextPx >= currentPx) {
                            nextPx = currentPx - 1;
                        }
                        el.style.fontSize = nextPx + 'px';
                        currentPx = nextPx;
                        guard++;
                    }
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', fitCertRecipientNames);
            } else {
                fitCertRecipientNames();
            }
            window.addEventListener('load', fitCertRecipientNames);
            window.addEventListener('resize', fitCertRecipientNames);
            window.addEventListener('beforeprint', fitCertRecipientNames);
            if (document.fonts && document.fonts.ready) {
                document.fonts.ready.then(fitCertRecipientNames);
            }
        })();
    </script>
</body>
</html>
