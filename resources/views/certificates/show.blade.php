<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Completion - {{ $course->title }} - CAB Academy</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}">
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

        @media print {
            body {
                background: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .certificate-wrapper {
                box-shadow: none !important;
                border: none !important;
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
            }
            @page {
                size: landscape;
                margin: 0.5cm;
            }
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-800 font-sans-body min-h-screen flex flex-col items-center justify-center p-4 sm:p-8">

    <!-- Top Action Toolbar (Hidden during print) -->
    <div class="no-print w-full max-w-5xl mb-6 flex flex-wrap items-center justify-between gap-4 bg-slate-800/80 backdrop-blur-md p-4 rounded-2xl border border-slate-700 text-white shadow-xl">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="p-2 rounded-lg bg-slate-700 hover:bg-slate-600 transition-colors text-xs font-semibold flex items-center gap-1.5">
                <i class="fas fa-arrow-left"></i> Dashboard
            </a>
            <span class="text-xs text-slate-300">
                Official Credential: <strong class="text-emerald-400 font-mono">{{ $certificate->certificate_number }}</strong>
            </span>
        </div>

        <div class="flex items-center gap-3">
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

    <!-- Official Certificate Frame -->
    <div class="certificate-wrapper w-full max-w-5xl bg-[#fffdf9] rounded-3xl p-4 sm:p-10 shadow-2xl border-[12px] border-[#051c45] relative overflow-hidden">
        
        <!-- Ornate Inner Guilloche Border -->
        <div class="border-2 border-[#b89758] p-6 sm:p-10 rounded-xl relative bg-gradient-to-b from-[#fffefc] via-white to-[#faf6ef]">
            
            <!-- Corner Decorative Badges -->
            <div class="absolute top-2 left-2 text-[#b89758] text-xl opacity-60"><i class="fas fa-award"></i></div>
            <div class="absolute top-2 right-2 text-[#b89758] text-xl opacity-60"><i class="fas fa-award"></i></div>
            <div class="absolute bottom-2 left-2 text-[#b89758] text-xl opacity-60"><i class="fas fa-award"></i></div>
            <div class="absolute bottom-2 right-2 text-[#b89758] text-xl opacity-60"><i class="fas fa-award"></i></div>

            <!-- Header: Institute Crest & Logo -->
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center gap-3 mb-2">
                    <img src="{{ asset('images/logo.png') }}" alt="CAB Academy Crest" class="w-16 h-16 sm:w-20 sm:h-20 object-contain">
                </div>
                <h1 class="font-cinzel text-2xl sm:text-4xl font-black tracking-widest text-[#051c45] uppercase">
                    Commercial Academy Bangladesh
                </h1>
                <span class="text-[10px] sm:text-xs font-cinzel font-bold text-[#c30102] tracking-[0.25em] uppercase block mt-1">
                    CAB Academy • Learn, Grow, Lead.
                </span>
                <p class="text-[10px] text-gray-500 mt-1 uppercase tracking-wider">
                    Mirpur-Pallabi Campus • Dhaka, Bangladesh
                </p>
            </div>

            <!-- Title of Honor -->
            <div class="text-center my-6">
                <span class="text-xs sm:text-sm font-cinzel text-[#b89758] uppercase tracking-[0.3em] block mb-1">
                    Executive Certification of Mastery
                </span>
                <p class="text-xs text-gray-600 italic">This is proudly presented and conferred upon</p>
                
                <!-- Recipient Name -->
                <div class="my-4 py-2 border-b-2 border-dotted border-[#b89758] inline-block min-w-[320px] sm:min-w-[480px]">
                    <h2 class="font-playfair text-3xl sm:text-5xl font-black text-[#051c45] tracking-tight">
                        {{ $user->name }}
                    </h2>
                </div>

                <p class="text-xs sm:text-sm text-gray-700 max-w-2xl mx-auto leading-relaxed mt-2">
                    for having successfully completed all prescribed coursework, simulated trade laboratories, case examinations, and demonstrating professional competence in
                </p>

                <!-- Course Title -->
                <h3 class="font-cinzel text-xl sm:text-2xl font-bold text-[#c30102] mt-3 uppercase tracking-wide">
                    {{ $course->title }}
                </h3>

                <p class="text-xs text-gray-500 mt-2">
                    Delivered under the auspices of the CAB Academy Executive Faculty Board.
                </p>
            </div>

            <!-- Footer Meta & Signatures -->
            <div class="mt-12 pt-8 border-t border-[#e2d5bd] grid grid-cols-3 items-end text-center">
                
                <!-- Left: Date & Security Serial -->
                <div class="text-left space-y-1">
                    <span class="text-[10px] font-mono text-gray-500 block">Issue Date: {{ \Carbon\Carbon::parse($certificate->issue_date)->format('F d, Y') }}</span>
                    <span class="text-[10px] font-mono text-[#051c45] font-bold block">Serial: {{ $certificate->certificate_number }}</span>
                    <span class="text-[8px] font-mono text-gray-400 block truncate max-w-[200px]" title="{{ $certificate->verification_hash }}">
                        Hash: {{ substr($certificate->verification_hash, 0, 16) }}...
                    </span>
                </div>

                <!-- Center: Official Golden Seal Simulation -->
                <div class="flex flex-col items-center justify-center">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full border-4 border-[#b89758] bg-gradient-to-tr from-[#dfba73] via-[#fae3a6] to-[#b89758] p-1 shadow-lg flex items-center justify-center text-center">
                        <div class="w-full h-full rounded-full border border-dashed border-[#8d6a2a] flex flex-col items-center justify-center text-[#5c4010]">
                            <i class="fas fa-stamp text-lg sm:text-xl"></i>
                            <span class="text-[8px] sm:text-[9px] font-cinzel font-black tracking-tighter uppercase mt-0.5">OFFICIAL SEAL</span>
                            <span class="text-[7px] font-cinzel tracking-wider">VERIFIED</span>
                        </div>
                    </div>
                </div>

                <!-- Right: Director Signature -->
                <div class="text-right">
                    <div class="inline-block border-b border-gray-700 pb-1 px-4 text-center">
                        <span class="font-script text-2xl sm:text-3xl text-[#051c45] block">
                            Prof. M. Rahman
                        </span>
                    </div>
                    <span class="text-[10px] font-bold text-[#051c45] uppercase tracking-wider block mt-1">
                        Academic Director
                    </span>
                    <span class="text-[9px] text-gray-500 uppercase block">
                        CAB Academy Governing Council
                    </span>
                </div>

            </div>

            <!-- Bottom QR Verification Link Hint -->
            <div class="mt-8 text-center text-[9px] text-gray-400">
                Online Registry Verification: <a href="{{ route('certificate.verify', $certificate->certificate_number) }}" class="underline text-gray-600 hover:text-[#c30102]">{{ url('/verify-certificate/' . $certificate->certificate_number) }}</a>
            </div>

        </div>

    </div>

</body>
</html>
