<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <title>Certificate of Completion - {{ $course->title }} - CAB Academy</title>
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
                border: 8px solid #051c45 !important;
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
            <a href="{{ route('dashboard') }}" class="p-2 px-3 rounded-xl bg-slate-700 hover:bg-slate-600 transition-colors text-xs font-semibold flex items-center gap-1.5">
                <i class="fas fa-arrow-left"></i> Dashboard
            </a>
            <span class="text-xs text-slate-300">
                Official Credential: <strong class="text-emerald-400 font-mono">{{ $certificate->certificate_number }}</strong>
            </span>
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
            <button 
                onclick="window.print()" 
                class="px-4 py-2 bg-[#c30102] hover:bg-[#a50001] text-white text-xs font-bold rounded-xl shadow-md transition-all flex items-center gap-2 cursor-pointer"
            >
                <i class="fas fa-print"></i> Print / Save Official PDF
            </button>
            <a 
                href="{{ route('certificate.verify', $certificate->certificate_number) }}" 
                target="_blank"
                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl shadow-md transition-all flex items-center gap-2"
            >
                <i class="fas fa-check-circle"></i> Verify Public URL
            </a>
        </div>
    </div>

    <!-- Official Certificate Frame (Prints precisely on 1 A4 Landscape Page) -->
    <div class="certificate-wrapper w-full max-w-5xl bg-[#fffdf9] rounded-3xl p-3 sm:p-8 shadow-2xl border-[10px] sm:border-[12px] border-[#051c45] relative overflow-hidden">
        
        <!-- Ornate Inner Guilloche Border -->
        <div class="certificate-inner border-2 border-[#b89758] p-4 sm:p-8 rounded-xl relative bg-gradient-to-b from-[#fffefc] via-white to-[#faf6ef]">
            
            <!-- Corner Decorative Badges -->
            <div class="absolute top-2 left-2 text-[#b89758] text-lg sm:text-xl opacity-60"><i class="fas fa-award"></i></div>
            <div class="absolute top-2 right-2 text-[#b89758] text-lg sm:text-xl opacity-60"><i class="fas fa-award"></i></div>
            <div class="absolute bottom-2 left-2 text-[#b89758] text-lg sm:text-xl opacity-60"><i class="fas fa-award"></i></div>
            <div class="absolute bottom-2 right-2 text-[#b89758] text-lg sm:text-xl opacity-60"><i class="fas fa-award"></i></div>

            <!-- Header: Institute Crest & Logo -->
            <div class="text-center mb-3 sm:mb-5 print-header">
                <div class="inline-flex items-center justify-center gap-2 mb-1 sm:mb-2">
                    <img src="{{ \App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png') }}" alt="{{ \App\Models\Setting::getVal('site_name', 'CAB Academy') }} Crest" class="w-12 h-12 sm:w-16 sm:h-16 object-contain" onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}';">
                </div>
                <h1 class="font-cinzel text-xl sm:text-3xl lg:text-4xl font-black tracking-widest text-[#051c45] uppercase">
                    Commercial Academy Bangladesh
                </h1>
                <span class="text-[9px] sm:text-xs font-cinzel font-bold text-[#c30102] tracking-[0.25em] uppercase block mt-0.5">
                    CAB Academy &bull; Learn, Grow, Lead.
                </span>
                <p class="text-[9px] sm:text-[10px] text-gray-500 mt-0.5 uppercase tracking-wider">
                    Mirpur-Pallabi Campus &bull; Dhaka, Bangladesh
                </p>
            </div>

            <!-- Title of Honor & Recipient -->
            <div class="text-center my-2 sm:my-4 print-honor-block">
                <span class="text-[11px] sm:text-xs font-cinzel text-[#b89758] uppercase tracking-[0.3em] block mb-0.5 font-bold">
                    Executive Certification of Mastery
                </span>
                <p class="text-[11px] sm:text-xs text-gray-600 italic">This is proudly presented and conferred upon</p>
                
                <!-- Recipient Name -->
                <div class="my-2 sm:my-3 py-1 sm:py-1.5 border-b-2 border-dotted border-[#b89758] inline-block min-w-[280px] sm:min-w-[440px]">
                    <h2 class="font-playfair text-2xl sm:text-4xl lg:text-5xl font-black text-[#051c45] tracking-tight">
                        {{ $user->name }}
                    </h2>
                </div>

                <p class="text-[11px] sm:text-xs text-gray-700 max-w-2xl mx-auto leading-relaxed mt-1">
                    for having successfully completed all prescribed coursework, simulated trade laboratories, case examinations, and demonstrating professional competence in
                </p>

                <!-- Course Title -->
                <h3 class="font-cinzel text-lg sm:text-2xl font-bold text-[#c30102] mt-2 uppercase tracking-wide">
                    {{ $course->title }}
                </h3>

                {{-- Batch only shown when explicitly enabled by admin --}}
                @if(!empty($course->batch) && $course->show_batch)
                    <p class="text-[10px] sm:text-xs font-cinzel font-bold text-[#051c45] mt-1 tracking-wider uppercase">
                        Cohort Intake: {{ $course->batch }}
                    </p>
                @endif

                <p class="text-[10px] sm:text-xs text-gray-500 mt-1">
                    Delivered under the auspices of the CAB Academy Executive Faculty Board.
                </p>
            </div>

            <!-- Footer Meta & Signatures -->
            <div class="mt-4 sm:mt-8 pt-3 sm:pt-6 border-t border-[#e2d5bd] grid grid-cols-3 items-end text-center print-footer-grid">
                
                <!-- Left: Date & Security Serial -->
                <div class="text-left space-y-0.5 sm:space-y-1">
                    @if(!empty($course->batch) && $course->show_batch)
                        <span class="text-[9px] sm:text-[10px] font-mono text-emerald-700 font-bold block">Batch: {{ $course->batch }}</span>
                    @endif
                    <span class="text-[9px] sm:text-[10px] font-mono text-gray-500 block">Issue Date: {{ \Carbon\Carbon::parse($certificate->issue_date)->format('F d, Y') }}</span>
                    <span class="text-[9px] sm:text-[10px] font-mono text-[#051c45] font-bold block">Serial: {{ $certificate->certificate_number }}</span>
                    <span class="text-[8px] font-mono text-gray-400 block truncate max-w-[180px] sm:max-w-[200px]" title="{{ $certificate->verification_hash }}">
                        Hash: {{ substr($certificate->verification_hash, 0, 16) }}...
                    </span>
                </div>

                <!-- Center: Official Golden Seal Simulation -->
                <div class="flex flex-col items-center justify-center">
                    <div class="print-seal w-16 h-16 sm:w-20 sm:h-20 lg:w-22 lg:h-22 rounded-full border-4 border-[#b89758] bg-gradient-to-tr from-[#dfba73] via-[#fae3a6] to-[#b89758] p-1 shadow-lg flex items-center justify-center text-center">
                        <div class="w-full h-full rounded-full border border-dashed border-[#8d6a2a] flex flex-col items-center justify-center text-[#5c4010]">
                            <i class="fas fa-stamp text-sm sm:text-lg"></i>
                            <span class="text-[7px] sm:text-[8px] font-cinzel font-black tracking-tighter uppercase mt-0.5">OFFICIAL SEAL</span>
                            <span class="text-[6px] sm:text-[7px] font-cinzel tracking-wider">VERIFIED</span>
                        </div>
                    </div>
                </div>

                <!-- Right: Director Signature -->
                <div class="text-right">
                    <div class="inline-block border-b border-gray-700 pb-0.5 px-3 sm:px-4 text-center">
                        <span class="font-script text-xl sm:text-2xl lg:text-3xl text-[#051c45] block">
                            Academic Director
                        </span>
                    </div>
                    <span class="text-[9px] sm:text-[10px] font-bold text-[#051c45] uppercase tracking-wider block mt-0.5">
                        Academic Director
                    </span>
                    <span class="text-[8px] sm:text-[9px] text-gray-500 uppercase block">
                        CAB Academy Governing Council
                    </span>
                </div>

            </div>

            <!-- Bottom QR Verification Link Hint -->
            <div class="mt-3 sm:mt-5 text-center text-[8px] sm:text-[9px] text-gray-400 print-qr-note">
                Online Registry Verification: <a href="{{ route('certificate.verify', $certificate->certificate_number) }}" class="underline text-gray-600 hover:text-[#c30102]">{{ url('/verify-certificate/' . $certificate->certificate_number) }}</a>
            </div>

        </div>

    </div>

</body>
</html>
