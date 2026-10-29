@php
    $template = request('template') ?: \App\Models\Setting::getVal('certificate_template', 'classic_navy');

    // Configurable Institution Branding & Content (defaults to Commercial Academy Bangladesh)
    $institutionName = \App\Models\Setting::getVal('certificate_institution_name', 'Commercial Academy Bangladesh');
    $tagline = \App\Models\Setting::getVal('certificate_tagline', 'CAB Academy • Learn, Grow, Lead.');
    $campusAddress = \App\Models\Setting::getVal('certificate_campus_address', 'Mirpur-Pallabi Campus • Dhaka, Bangladesh');
    $certTitle = \App\Models\Setting::getVal('certificate_title', 'Executive Certification of Mastery');
    $confermentText = \App\Models\Setting::getVal('certificate_conferment_text', 'This is proudly presented and conferred upon');
    $completionText = \App\Models\Setting::getVal('certificate_completion_text', 'for having successfully completed all prescribed coursework, simulated trade laboratories, case examinations, and demonstrating professional competence in');
    $facultyEndorsement = \App\Models\Setting::getVal('certificate_faculty_endorsement', 'Delivered under the auspices of the CAB Academy Executive Faculty Board.');
    $primarySignatoryName = \App\Models\Setting::getVal('certificate_primary_signatory_name', 'Academic Director');
    $primarySignatoryTitle = \App\Models\Setting::getVal('certificate_primary_signatory_title', 'Academic Director');
    $primarySignatoryBoard = \App\Models\Setting::getVal('certificate_primary_signatory_board', 'CAB Academy Governing Council');
    $showSecondary = \App\Models\Setting::getVal('certificate_show_secondary_signature', '1') == '1';
    $secondarySignatoryName = \App\Models\Setting::getVal('certificate_secondary_signatory_name', 'Lead Executive Trainer');
    $secondarySignatoryTitle = \App\Models\Setting::getVal('certificate_secondary_signatory_title', 'Course Director & Faculty Chair');
    $sealText = \App\Models\Setting::getVal('certificate_seal_text', 'OFFICIAL SEAL • VERIFIED');
    $sealParts = explode('•', $sealText);
    $sealLine1 = trim($sealParts[0] ?? 'OFFICIAL SEAL');
    $sealLine2 = trim($sealParts[1] ?? 'VERIFIED');

    // Theme Palettes
    $themes = [
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

    $theme = $themes[$template] ?? $themes['classic_navy'];
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
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Great+Vibes&family=Montserrat:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .font-cinzel { font-family: 'Cinzel', serif; }
        .font-playfair { font-family: 'Playfair Display', serif; }
        .font-script { font-family: 'Great Vibes', cursive; }
        .font-sans-body { font-family: 'Montserrat', sans-serif; }

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
                border: 8px solid {{ $theme['outer_border_hex'] }} !important;
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
            .certificate-inner {
                height: 100% !important;
                max-height: 100% !important;
                padding: 4mm 8mm !important;
                box-sizing: border-box !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: space-between !important;
                overflow: hidden !important;
            }
            .print-header {
                margin-bottom: 2mm !important;
            }
            .print-header img {
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
                margin-top: 2mm !important;
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
                        {{ explode(' ', $tConfig['name'])[0] }}
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

    <!-- Official Certificate Frame (Prints precisely on 1 A4 Landscape Page) -->
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
                    <img src="{{ \App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png') }}" alt="{{ $institutionName }} Crest" class="w-12 h-12 sm:w-16 sm:h-16 object-contain" onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}';">
                </div>
                <!-- Institution Name (Configurable with default Commercial Academy Bangladesh) -->
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
                    
                    <!-- Col 1: Date & Security Serial -->
                    <div class="text-left space-y-0.5 sm:space-y-1">
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
                        <div class="print-seal w-16 h-16 sm:w-20 sm:h-20 lg:w-22 lg:h-22 rounded-full border-4 {{ $theme['seal_border'] }} bg-gradient-to-tr {{ $theme['seal_bg'] }} p-1 shadow-lg flex items-center justify-center text-center">
                            <div class="w-full h-full rounded-full border border-dashed {{ $theme['seal_inner_border'] }} flex flex-col items-center justify-center {{ $theme['seal_text_color'] }}">
                                <i class="fas {{ $theme['seal_icon'] }} text-xs sm:text-base"></i>
                                <span class="text-[7px] sm:text-[8px] font-cinzel font-black tracking-tighter uppercase mt-0.5">{{ $sealLine1 }}</span>
                                <span class="text-[6px] sm:text-[7px] font-cinzel tracking-wider">{{ $sealLine2 }}</span>
                            </div>
                        </div>
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
                    
                    <!-- Left: Date & Security Serial -->
                    <div class="text-left space-y-0.5 sm:space-y-1">
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
                        <div class="print-seal w-16 h-16 sm:w-20 sm:h-20 lg:w-22 lg:h-22 rounded-full border-4 {{ $theme['seal_border'] }} bg-gradient-to-tr {{ $theme['seal_bg'] }} p-1 shadow-lg flex items-center justify-center text-center">
                            <div class="w-full h-full rounded-full border border-dashed {{ $theme['seal_inner_border'] }} flex flex-col items-center justify-center {{ $theme['seal_text_color'] }}">
                                <i class="fas {{ $theme['seal_icon'] }} text-sm sm:text-lg"></i>
                                <span class="text-[7px] sm:text-[8px] font-cinzel font-black tracking-tighter uppercase mt-0.5">{{ $sealLine1 }}</span>
                                <span class="text-[6px] sm:text-[7px] font-cinzel tracking-wider">{{ $sealLine2 }}</span>
                            </div>
                        </div>
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

</body>
</html>

