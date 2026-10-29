@php
    $siteName = \App\Models\Setting::getVal('site_name', 'CAB Academy');
    $siteLogo = \App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png');
    $siteLogoUrl = (str_starts_with($siteLogo, 'http://') || str_starts_with($siteLogo, 'https://')) ? $siteLogo : url($siteLogo);
    $cleanDesc = \Illuminate\Support\Str::limit(strip_tags($course->short_description ?: $course->description ?: 'Enroll in ' . $course->title . ' at CAB Academy Dhaka. Executive certification, practical industry training, and career advancement.'), 220);
    $imageUrl = $course->og_image_url;
    while (str_contains($imageUrl, '&amp;')) {
        $imageUrl = html_entity_decode($imageUrl, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    $secureImageUrl = str_starts_with($imageUrl, 'https://') ? $imageUrl : (str_starts_with($imageUrl, 'http://') ? 'https://' . substr($imageUrl, 7) : $imageUrl);
    $ext = strtolower(pathinfo(parse_url($imageUrl, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
    $ogMime = match($ext) {
        'png' => 'image/png',
        'webp' => 'image/webp',
        default => 'image/jpeg',
    };
    $targetUrl = route('courses.show', $course);
    $shortUrl = $course->short_url;
    $enrollUrl = route('courses.show', $course) . '?enroll=true';
    
    $leadInst = $course->lead_faculty ?? $course->instructorProfile ?? $course->instructor;
    $instName = $leadInst ? $leadInst->name : 'CAB Academy Senior Faculty';
    $instAvatar = $leadInst ? ($leadInst->avatar_url ?? $leadInst->profile_picture_url ?? null) : null;
    $instTitle = $leadInst ? ($leadInst->title ?? 'Executive Trade & Customs Specialist') : 'Executive Trade & Customs Specialist';
@endphp
<!DOCTYPE html>
<html lang="en" prefix="og: https://ogp.me/ns# fb: https://ogp.me/ns/fb#">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>{{ $course->title }} - {{ $siteName }}</title>
    <meta name="description" content="{{ $cleanDesc }}">
    <link rel="canonical" href="{{ $targetUrl }}">

    <!-- Open Graph / Facebook Meta Tags (1200x630 Large Card Standard) -->
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $shortUrl }}">
    <meta property="og:title" content="{{ $course->title }} - {{ $siteName }}">
    <meta property="og:description" content="{{ $cleanDesc }}">
    <meta property="og:image" content="{!! $imageUrl !!}">
    <meta property="og:image:secure_url" content="{!! $secureImageUrl !!}">
    <meta property="og:image:type" content="{{ $ogMime }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $course->title }}">
    <meta property="og:locale" content="en_US">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ $shortUrl }}">
    <meta name="twitter:title" content="{{ $course->title }} - {{ $siteName }}">
    <meta name="twitter:description" content="{{ $cleanDesc }}">
    <meta name="twitter:image" content="{!! $imageUrl !!}">
    <meta name="twitter:image:alt" content="{{ $course->title }}">

    <!-- Google Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
    </style>
</head>
<body class="bg-[#020b18] text-white min-h-screen flex flex-col justify-between antialiased selection:bg-[#c30102] selection:text-white">

    <!-- 1. Top Brand & Counseling Bar -->
    <header class="w-full bg-[#051c45]/90 backdrop-blur-md border-b border-white/10 sticky top-0 z-40">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                <img src="{{ $siteLogoUrl }}" alt="{{ $siteName }}" class="h-8 sm:h-9 w-auto object-contain" onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}';">
                <div class="hidden sm:block">
                    <span class="text-sm font-extrabold text-white tracking-wide block leading-none">{{ $siteName }}</span>
                    <span class="text-[10px] text-slate-300 font-medium tracking-wider uppercase">Executive Professional Certification</span>
                </div>
            </a>

            <!-- Counseling & Direct Hotline -->
            <div class="flex items-center gap-2 sm:gap-3">
                <a 
                    href="{{ $course->counselor_whatsapp_url }}" 
                    target="_blank" 
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-[#25D366]/15 text-[#25D366] hover:bg-[#25D366] hover:text-white border border-[#25D366]/30 text-xs font-bold transition-all shadow-sm"
                >
                    <i class="fab fa-whatsapp text-sm"></i>
                    <span class="hidden md:inline">WhatsApp Counselor</span>
                    <span class="md:hidden">WhatsApp</span>
                </a>
                <a 
                    href="tel:01917054664" 
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/10 hover:bg-white/20 text-white border border-white/15 text-xs font-bold transition-all"
                >
                    <i class="fas fa-phone-alt text-[10px] text-amber-300"></i>
                    <span class="hidden sm:inline">01917054664</span>
                    <span class="sm:hidden">Call</span>
                </a>
            </div>
        </div>
    </header>

    <!-- 2. Main Executive Course Showcase Banner -->
    <main class="flex-1 max-w-5xl w-full mx-auto px-4 sm:px-6 py-6 sm:py-10 flex flex-col justify-center">
        
        <div class="bg-gradient-to-b from-[#051c45] via-[#072152] to-[#041536] rounded-3xl border border-white/15 shadow-[0_25px_60px_-15px_rgba(0,0,0,0.7)] overflow-hidden">
            
            <!-- Clickable Course Thumbnail Banner (Large 16:9 Screen Format) -->
            <div class="relative w-full aspect-[16/9] sm:aspect-[21/9] bg-slate-900 overflow-hidden group/thumb">
                <a href="{{ $enrollUrl }}" title="Click to Enroll in {{ $course->title }}" class="block w-full h-full relative cursor-pointer">
                    <img 
                        src="{{ $imageUrl }}" 
                        alt="{{ $course->title }}" 
                        class="w-full h-full object-cover group-hover/thumb:scale-105 transition-transform duration-700 ease-out"
                        onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=1200&h=630&q=85';"
                    >

                    <!-- Gradient Scrim Vignette -->
                    <div class="absolute inset-0 bg-gradient-to-t from-[#041536] via-[#041536]/30 to-black/30 pointer-events-none transition-opacity duration-300"></div>

                    <!-- Floating Badges: Top-Left Category & Top-Right Batch -->
                    <div class="absolute top-3 sm:top-4 left-3 sm:left-4 z-20 flex items-center gap-2 flex-wrap">
                        <span class="px-3 py-1 rounded-full bg-slate-900/80 backdrop-blur-md border border-white/20 text-white text-[11px] font-black uppercase tracking-wider shadow">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#c30102] inline-block mr-1.5"></span>
                            {{ $course->category ?? 'Professional Training' }}
                        </span>
                        <span class="px-3 py-1 rounded-full bg-emerald-500/90 text-white text-[11px] font-black uppercase tracking-wider shadow backdrop-blur-md flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                            <span>Live Zoom + Campus</span>
                        </span>
                    </div>

                    @if(!empty($course->batch) && $course->show_batch)
                        <div class="absolute top-3 sm:top-4 right-3 sm:right-4 z-20">
                            <span class="px-3 py-1 rounded-full bg-gradient-to-r from-[#c30102] to-[#e11d48] text-white text-[11px] font-black uppercase tracking-wider shadow-lg border border-white/20 backdrop-blur-md">
                                {{ $course->batch }}
                            </span>
                        </div>
                    @endif

                    <!-- Bottom Price Shield over Thumbnail -->
                    <div class="absolute bottom-3 sm:bottom-4 left-3 sm:left-4 z-20">
                        <div class="bg-gradient-to-r from-[#020b18]/95 via-[#041536]/90 to-[#072152]/85 backdrop-blur-md border border-white/20 px-3.5 py-1.5 rounded-xl shadow-lg flex items-center gap-2">
                            <span class="text-[10px] text-slate-300 font-bold uppercase">Price TK:</span>
                            <span class="font-black text-sm sm:text-base text-white tracking-tight">
                                {{ $course->price > 0 ? '৳' . number_format($course->price, 2) : 'Free Access' }}
                            </span>
                            @if($course->price > 0)
                                <span class="text-[10px] text-slate-400 line-through hidden sm:inline">৳{{ number_format($course->price * 1.25, 0) }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Center Hover Action Pill Overlay -->
                    <div class="absolute inset-0 z-10 flex items-center justify-center opacity-0 group-hover/thumb:opacity-100 transition-opacity duration-300 bg-black/40 backdrop-blur-[2px]">
                        <span class="px-5 py-2.5 rounded-full bg-[#c30102] text-white font-extrabold text-xs sm:text-sm shadow-2xl flex items-center gap-2 transform group-hover/thumb:scale-105 transition-transform duration-200">
                            <i class="fas fa-bolt text-amber-300"></i>
                            <span>Click Thumbnail to Express Enroll (30 Sec)</span>
                            <i class="fas fa-arrow-right text-xs"></i>
                        </span>
                    </div>
                </a>
            </div>

            <!-- Clickable Hint Ribbon -->
            <div class="bg-[#c30102]/20 border-y border-white/10 px-4 sm:px-6 py-2 flex items-center justify-between text-xs text-white/90">
                <a href="{{ $enrollUrl }}" class="flex items-center gap-2 font-semibold hover:text-white transition-colors">
                    <i class="fas fa-hand-pointer text-amber-300 animate-bounce"></i>
                    <span>Click the thumbnail banner above to enroll instantly or explore full syllabus below</span>
                </a>
                <span class="hidden sm:inline text-slate-400 text-[11px] font-mono">Admission in 30 Seconds</span>
            </div>

            <!-- Course Title, Faculty & Details Content -->
            <div class="p-5 sm:p-8 space-y-6">
                <div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-black text-white tracking-tight leading-snug mb-3">
                        {{ $course->title }}
                    </h1>
                    <p class="text-slate-300 text-xs sm:text-sm leading-relaxed max-w-3xl font-normal">
                        {{ $course->short_description ?? ($course->description ?? 'Live interactive professional certification masterclass with authentic practical case simulations, export-import documentation, and verifiable credentials.') }}
                    </p>
                </div>

                <!-- Instructor Profile Summary -->
                <div class="flex items-center gap-3.5 p-3 rounded-2xl bg-white/5 border border-white/10">
                    <div class="w-11 h-11 rounded-full border-2 border-white/20 overflow-hidden bg-slate-800 shrink-0">
                        @if($instAvatar)
                            <img src="{{ $instAvatar }}" alt="{{ $instName }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($instName) }}&background=051c45&color=fff';">
                        @else
                            <div class="w-full h-full bg-[#051c45] text-white flex items-center justify-center font-bold text-sm">
                                {{ substr($instName, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-[10px] uppercase font-extrabold text-[#c30102] tracking-wider block">Lead Faculty</span>
                        <h4 class="text-xs sm:text-sm font-bold text-white truncate">{{ $instName }}</h4>
                        <p class="text-[11px] text-slate-400 truncate">{{ $instTitle }}</p>
                    </div>
                    <span class="hidden sm:inline-flex items-center gap-1 text-[10px] font-bold text-emerald-400 bg-emerald-500/10 px-2.5 py-1 rounded-full border border-emerald-500/20">
                        <i class="fas fa-certificate text-[9px]"></i> Verifiable Certificate
                    </span>
                </div>

                <!-- Key Meta Specifications Cards -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div class="p-3 rounded-xl bg-white/5 border border-white/10 space-y-1">
                        <span class="text-[10px] text-slate-400 uppercase font-semibold block flex items-center gap-1">
                            <i class="far fa-calendar-alt text-[#c30102]"></i> Starts
                        </span>
                        <span class="font-bold text-white block truncate">
                            {{ $course->start_date ? $course->start_date->format('d M, Y') : ($course->duration ?? 'Upcoming') }}
                        </span>
                    </div>

                    <div class="p-3 rounded-xl bg-white/5 border border-white/10 space-y-1">
                        <span class="text-[10px] text-slate-400 uppercase font-semibold block flex items-center gap-1">
                            <i class="far fa-clock text-amber-400"></i> Duration
                        </span>
                        <span class="font-bold text-white block truncate">
                            {{ $course->duration ?? '8 Weeks' }}
                        </span>
                    </div>

                    <div class="p-3 rounded-xl bg-white/5 border border-white/10 space-y-1">
                        <span class="text-[10px] text-slate-400 uppercase font-semibold block flex items-center gap-1">
                            <i class="fas fa-video text-cyan-400"></i> Format
                        </span>
                        <span class="font-bold text-white block truncate">
                            {{ $course->venue ?? 'Live Zoom & Campus' }}
                        </span>
                    </div>

                    <div class="p-3 rounded-xl bg-white/5 border border-white/10 space-y-1">
                        <span class="text-[10px] text-slate-400 uppercase font-semibold block flex items-center gap-1">
                            <i class="fas fa-tags text-emerald-400"></i> Tuition
                        </span>
                        <span class="font-bold text-white block truncate">
                            {{ $course->price > 0 ? '৳' . number_format($course->price, 0) : 'Free Access' }}
                        </span>
                    </div>
                </div>

                <!-- High-Impact Action Buttons Section -->
                <div class="space-y-3 pt-2">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- 1. Primary Action: Express Enroll in 30 Seconds -->
                        <a 
                            href="{{ $enrollUrl }}" 
                            class="w-full py-3.5 px-5 rounded-2xl bg-gradient-to-r from-[#c30102] to-[#e11d48] hover:from-[#a50102] hover:to-[#be123c] text-white font-black text-sm tracking-wide uppercase shadow-[0_6px_25px_rgba(195,1,2,0.4)] flex items-center justify-center gap-2.5 transition-all duration-200 hover:scale-[1.02] active:scale-95 text-center group cursor-pointer"
                        >
                            <i class="fas fa-bolt text-amber-300 text-sm group-hover:scale-125 transition-transform"></i>
                            <span>Enroll Now (30 Sec)</span>
                            <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                        </a>

                        <!-- 2. WhatsApp Counselor Direct Inquiry -->
                        <a 
                            href="{{ $course->counselor_whatsapp_url }}" 
                            target="_blank" 
                            rel="noopener noreferrer"
                            class="w-full py-3.5 px-5 rounded-2xl bg-[#25D366] hover:bg-[#20ba5a] text-white font-extrabold text-sm tracking-wide shadow-[0_6px_25px_rgba(37,211,102,0.3)] flex items-center justify-center gap-2 transition-all duration-200 hover:scale-[1.02] active:scale-95 text-center cursor-pointer"
                        >
                            <i class="fab fa-whatsapp text-lg"></i>
                            <span>WhatsApp Counselor</span>
                        </a>
                    </div>

                    <!-- Secondary Action Buttons: Full Syllabus + Hotline + Share + Copy Link -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 pt-1">
                        <!-- 3. View Full Syllabus & Curriculum -->
                        <a 
                            href="{{ $targetUrl }}" 
                            class="py-2.5 px-3 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-xs border border-white/15 flex items-center justify-center gap-1.5 transition-all active:scale-95 text-center"
                        >
                            <i class="fas fa-book-open text-slate-300"></i>
                            <span>Full Syllabus</span>
                        </a>

                        <!-- 4. Direct Call Hotline -->
                        <a 
                            href="tel:01917054664" 
                            class="py-2.5 px-3 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-xs border border-white/15 flex items-center justify-center gap-1.5 transition-all active:scale-95 text-center"
                        >
                            <i class="fas fa-phone text-amber-300 text-[11px]"></i>
                            <span>Call Hotline</span>
                        </a>

                        <!-- 5. Copy Short Link -->
                        <button 
                            type="button" 
                            onclick="copyShortLink('{{ $shortUrl }}', this)" 
                            class="py-2.5 px-3 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-xs border border-white/15 flex items-center justify-center gap-1.5 transition-all active:scale-95 cursor-pointer text-center"
                            title="Copy link to clipboard"
                        >
                            <i class="far fa-copy text-amber-300 text-[11px]"></i>
                            <span>Copy Link</span>
                        </button>

                        <!-- 6. Share to Facebook Feed -->
                        <a 
                            href="{{ $course->facebook_share_url }}" 
                            target="_blank" 
                            rel="noopener noreferrer"
                            onclick="window.open(this.href, 'fbshare', 'width=640,height=560,menubar=no,toolbar=no'); return false;"
                            class="py-2.5 px-3 rounded-xl bg-[#1877F2] hover:bg-[#166fe5] text-white font-bold text-xs flex items-center justify-center gap-1.5 transition-all shadow-sm active:scale-95 text-center cursor-pointer"
                        >
                            <i class="fab fa-facebook-f text-xs"></i>
                            <span>Share</span>
                        </a>
                    </div>
                </div>

                <!-- 3. Countdown & Auto-Proceed Controller -->
                <div class="pt-4 border-t border-white/10 flex flex-wrap items-center justify-between text-xs text-slate-400 gap-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Auto-advancing to syllabus in <strong id="cdSeconds" class="text-white font-bold">12</strong>s</span>
                        <button type="button" id="pauseBtn" onclick="toggleAutoRedirect()" class="text-xs text-amber-300 hover:underline cursor-pointer ml-1">[Pause Timer]</button>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ $targetUrl }}" class="text-white hover:text-[#c30102] font-bold transition-colors flex items-center gap-1">
                            <span>View Full Course Syllabus Immediately</span>
                            <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </main>

    <!-- 4. Minimalist Trust Footer -->
    <footer class="w-full border-t border-white/10 py-4 text-center text-xs text-slate-400">
        <div class="max-w-6xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <span>&copy; {{ date('Y') }} {{ $siteName }}. All rights reserved. Mirpur-Pallabi, Dhaka.</span>
            <div class="flex items-center gap-4 text-slate-300">
                <a href="{{ url('/') }}" class="hover:text-white transition-colors">Home</a>
                <a href="{{ route('catalog.index') }}" class="hover:text-white transition-colors">Courses</a>
                <a href="tel:01917054664" class="hover:text-white transition-colors">Hotline: 01917054664</a>
            </div>
        </div>
    </footer>

    <!-- Toast Container -->
    <div id="toast" class="fixed bottom-6 right-6 z-50 bg-[#10b981] text-white px-4 py-2.5 rounded-xl shadow-2xl text-xs font-bold transition-all duration-300 transform translate-y-20 opacity-0 pointer-events-none flex items-center gap-2">
        <i class="fas fa-check-circle"></i>
        <span id="toastMsg">Short Link Copied to Clipboard!</span>
    </div>

    <script>
        let seconds = 12;
        let isPaused = false;
        const cdElement = document.getElementById('cdSeconds');
        const pauseBtn = document.getElementById('pauseBtn');

        const timer = setInterval(() => {
            if (!isPaused) {
                seconds--;
                if (cdElement) cdElement.innerText = seconds;
                if (seconds <= 0) {
                    clearInterval(timer);
                    window.location.href = "{{ $targetUrl }}";
                }
            }
        }, 1000);

        function toggleAutoRedirect() {
            isPaused = !isPaused;
            if (pauseBtn) {
                pauseBtn.innerText = isPaused ? '[Resume Timer]' : '[Pause Timer]';
            }
            if (isPaused) {
                showToast('Auto-redirect paused. Take your time to review the banner!');
            }
        }

        function copyShortLink(url, btn) {
            // Pause timer if user interacts with copy button
            isPaused = true;
            if (pauseBtn) pauseBtn.innerText = '[Resume Timer]';

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(url).then(() => {
                    showToast('Short Link Copied! Ready to share.');
                }).catch(() => fallbackCopy(url));
            } else {
                fallbackCopy(url);
            }
        }

        function fallbackCopy(text) {
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'fixed';
            ta.style.left = '-999999px';
            document.body.appendChild(ta);
            ta.focus();
            ta.select();
            try {
                document.execCommand('copy');
                showToast('Short Link Copied! Ready to share.');
            } catch (err) {
                prompt('Copy this link:', text);
            }
            document.body.removeChild(ta);
        }

        function showToast(msg) {
            const toast = document.getElementById('toast');
            const toastMsg = document.getElementById('toastMsg');
            if (toast && toastMsg) {
                toastMsg.innerText = msg;
                toast.classList.remove('translate-y-20', 'opacity-0', 'pointer-events-none');
                toast.classList.add('translate-y-0', 'opacity-100');
                setTimeout(() => {
                    toast.classList.remove('translate-y-0', 'opacity-100');
                    toast.classList.add('translate-y-20', 'opacity-0', 'pointer-events-none');
                }, 3000);
            }
        }

        // Pause countdown if user clicks anywhere inside the main container
        document.querySelector('main')?.addEventListener('click', () => {
            isPaused = true;
            if (pauseBtn) pauseBtn.innerText = '[Resume Timer]';
        });
    </script>
</body>
</html>
