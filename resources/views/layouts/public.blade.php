<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Course Catalog') - {{ \App\Models\Setting::getVal('site_name', 'CAB Academy') }}</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}">

    <!-- Google Fonts: Inter & Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind & App JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --color-content-primary: #c30102;
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

        .bg-ipdp-primary { background-color: #c30102 !important; }
        .bg-ipdp-secondary { background-color: #041536 !important; }
        .text-ipdp-primary { color: #c30102 !important; }
        .text-ipdp-secondary { color: #041536 !important; }
        .border-ipdp-primary { border-color: #c30102 !important; }
        .border-ipdp-secondary { border-color: #041536 !important; }

        /* Modernized Billion-Dollar Luxury Buttons */
        .btn-ipdp-primary {
            background: linear-gradient(135deg, #c30102 0%, #d61726 50%, #b00102 100%) !important;
            color: #ffffff !important;
            font-weight: 800;
            border-radius: 0.625rem;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 16px rgba(195, 1, 2, 0.32), inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }
        .btn-ipdp-primary:hover {
            background: linear-gradient(135deg, #b00102 0%, #c30102 50%, #9a0102 100%) !important;
            transform: translateY(-1.5px);
            box-shadow: 0 8px 24px rgba(195, 1, 2, 0.42), inset 0 1px 0 rgba(255, 255, 255, 0.25);
        }

        .btn-ipdp-secondary {
            background: linear-gradient(135deg, #041536 0%, #072152 50%, #020b18 100%) !important;
            color: #ffffff !important;
            font-weight: 800;
            border-radius: 0.625rem;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 16px rgba(4, 21, 54, 0.28), inset 0 1px 0 rgba(255, 255, 255, 0.15);
        }
        .btn-ipdp-secondary:hover {
            background: linear-gradient(135deg, #020b18 0%, #041536 50%, #01060e 100%) !important;
            transform: translateY(-1.5px);
            box-shadow: 0 8px 24px rgba(4, 21, 54, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col selection:bg-[#c30102] selection:text-white" x-data="{ searchModalOpen: false }">

    <!-- 0. Top Executive Announcement Bar -->
    <div class="bg-gradient-to-r from-[#020b18] via-[#041536] to-[#072152] text-white py-2 px-4 text-xs font-medium border-b border-white/10 hidden sm:block relative z-50">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-gradient-to-r from-[#c30102] to-[#e11d48] text-white text-[10px] font-black uppercase tracking-wider shadow-sm border border-white/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                    <span>Live Admissions</span>
                </span>
                <span class="text-slate-200 text-xs font-medium">
                    Batch 105th Enrolling &bull; 100% Live Interactive Online Masterclasses (Zoom)
                </span>
            </div>
            <div class="flex items-center gap-4 text-xs text-slate-300">
                <a href="tel:01917054664" class="hover:text-white flex items-center gap-1.5 transition-colors font-semibold">
                    <i class="fas fa-phone-alt text-[#c30102]"></i>
                    <span>01917-054664</span>
                </a>
                <span class="text-white/30">&bull;</span>
                <a href="https://wa.me/8801917054664" target="_blank" rel="noopener noreferrer" class="text-emerald-400 hover:text-emerald-300 flex items-center gap-1.5 transition-colors font-semibold">
                    <i class="fab fa-whatsapp text-sm"></i>
                    <span>Instant WhatsApp</span>
                </a>
                <span class="text-white/30">&bull;</span>
                <a href="{{ route('contact.index') }}" class="text-slate-300 hover:text-white transition-colors">
                    Mirpur-Pallabi, Dhaka-1216
                </a>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-xl border-b border-slate-200/80 shadow-[0_4px_25px_-5px_rgba(5,28,69,0.06)] transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- CAB Academy Brand Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-3 shrink-0 group">
                    <div class="relative">
                        <img class="h-12 w-12 sm:h-14 sm:w-14 object-contain rounded-full shadow-md bg-white border border-slate-100 ring-2 ring-slate-100 group-hover:ring-[#c30102]/30 group-hover:scale-105 transition-all duration-300" src="{{ asset('images/logo.png') }}" alt="CAB Academy">
                    </div>
                    <div class="flex flex-col">
                        <span class="text-lg sm:text-xl font-black tracking-tight text-[#041536] uppercase leading-none">CAB Academy</span>
                        <span class="text-[9px] sm:text-[10px] tracking-widest uppercase font-black text-[#c30102] mt-1">Learn, Grow, Lead.</span>
                    </div>
                </a>

                <!-- Desktop Navigation Menu -->
                <nav class="hidden lg:flex items-center space-x-7 text-[15px] font-semibold text-[#041536]" x-data="{ courseDropdown: false }">
                    <a href="{{ url('/') }}" class="hover:text-[#c30102] transition-colors relative py-2 {{ request()->is('/') ? 'text-[#c30102] font-bold' : '' }}">
                        <span>Home</span>
                        @if(request()->is('/'))
                            <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#c30102] rounded-full"></span>
                        @endif
                    </a>

                    <!-- Course Dropdown -->
                    <div class="relative py-2" @mouseenter="courseDropdown = true" @mouseleave="courseDropdown = false">
                        <a href="{{ route('catalog.index') }}" class="flex items-center gap-1 hover:text-[#c30102] transition-colors {{ request()->is('catalog*') || request()->is('courses*') ? 'text-[#c30102] font-bold' : '' }}">
                            <span>Course</span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="courseDropdown ? 'rotate-180 text-[#c30102]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
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
                            class="absolute top-full left-0 w-64 bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl border border-slate-200/80 p-2 z-50"
                            style="display: none;"
                        >
                            <a href="{{ route('catalog.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl text-sm hover:bg-slate-50 hover:text-[#c30102] transition-colors font-semibold text-slate-800">
                                <i class="fas fa-layer-group text-xs text-[#c30102]"></i>
                                <span>All Courses</span>
                            </a>
                            <a href="{{ route('catalog.certificate') }}" class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl text-sm hover:bg-slate-50 hover:text-[#c30102] transition-colors font-semibold text-slate-800">
                                <i class="fas fa-award text-xs text-[#051c45]"></i>
                                <span>Certificate Programs</span>
                            </a>
                            <a href="{{ route('catalog.day_long') }}" class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl text-sm hover:bg-slate-50 hover:text-[#c30102] transition-colors font-semibold text-slate-800">
                                <i class="fas fa-calendar-day text-xs text-[#10b981]"></i>
                                <span>Day Long Masterclasses</span>
                            </a>
                            <div class="border-t border-slate-100 my-1"></div>
                            <a href="{{ route('catalog.category', 'export-import') }}" class="flex items-center gap-2.5 px-4 py-2 rounded-xl text-xs hover:bg-slate-50 hover:text-[#c30102] transition-colors font-medium text-slate-600">
                                <i class="fas fa-ship text-[11px] text-slate-400"></i>
                                <span>Export – Import</span>
                            </a>
                            <a href="{{ route('catalog.category', 'supply-chain-management') }}" class="flex items-center gap-2.5 px-4 py-2 rounded-xl text-xs hover:bg-slate-50 hover:text-[#c30102] transition-colors font-medium text-slate-600">
                                <i class="fas fa-boxes text-[11px] text-slate-400"></i>
                                <span>Supply Chain Management</span>
                            </a>
                            <a href="{{ route('catalog.category', 'accounting') }}" class="flex items-center gap-2.5 px-4 py-2 rounded-xl text-xs hover:bg-slate-50 hover:text-[#c30102] transition-colors font-medium text-slate-600">
                                <i class="fas fa-calculator text-[11px] text-slate-400"></i>
                                <span>Accounting & Finance</span>
                            </a>
                        </div>
                    </div>

                    <!-- Distinguished Faculty -->
                    <a href="{{ route('faculty.index') }}" class="hover:text-[#c30102] transition-colors relative py-2 {{ request()->is('faculty*') ? 'text-[#c30102] font-bold' : '' }}">
                        <span>Faculty</span>
                    </a>

                    <!-- Knowledge Hub / Blog -->
                    <a href="{{ route('blog.index') }}" class="hover:text-[#c30102] transition-colors relative py-2 {{ request()->is('blog*') ? 'text-[#c30102] font-bold' : '' }}">
                        <span>Blog</span>
                    </a>

                    <!-- Community Feed -->
                    <a href="{{ route('feed.index') }}" class="hover:text-[#c30102] transition-colors relative py-2 {{ request()->is('feed*') || request()->is('community*') ? 'text-[#c30102] font-bold' : '' }}">
                        <span>Feed</span>
                    </a>

                    <a href="{{ route('about.index') }}" class="hover:text-[#c30102] transition-colors relative py-2 {{ request()->routeIs('about.*') ? 'text-[#c30102] font-bold' : '' }}">
                        <span>About Us</span>
                    </a>

                    <a href="{{ route('jobs.index') }}" class="hover:text-[#c30102] transition-colors relative py-2 {{ request()->is('jobs*') ? 'text-[#c30102] font-bold' : '' }}">
                        <span>Careers</span>
                    </a>

                    <a href="{{ route('contact.index') }}" class="hover:text-[#c30102] transition-colors relative py-2 {{ request()->routeIs('contact.*') ? 'text-[#c30102] font-bold' : '' }}">
                        <span>Contact</span>
                    </a>
                </nav>

                <!-- Actions: Wishlist Heart, Auth, Header Search -->
                <div class="hidden sm:flex items-center gap-3">
                    <!-- Wishlist Heart Icon with dynamic badge -->
                    <a href="{{ route('wishlist.index') }}" title="Saved Wishlist" class="relative text-[#c30102] p-2 hover:scale-110 transition-transform">
                        <i class="fas fa-heart text-xl"></i>
                        <span id="wishlist-count-badge" class="absolute -top-1 -right-1 bg-[#c30102] text-white text-[10px] font-black w-4 h-4 rounded-full flex items-center justify-center shadow {{ auth()->check() && auth()->user()->wishlists()->count() > 0 ? '' : 'hidden' }}">
                            {{ auth()->check() ? auth()->user()->wishlists()->count() : 0 }}
                        </span>
                    </a>

                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-ipdp-primary px-5 py-2.5 rounded-xl text-sm font-bold shadow-md flex items-center gap-2">
                            <i class="far fa-user"></i>
                            <span>My Account</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-ipdp-primary px-5 py-2.5 rounded-xl text-sm font-bold shadow-md flex items-center gap-1.5">
                            <i class="fas fa-lock text-xs"></i>
                            <span>Sign In</span>
                        </a>
                    @endauth

                    <!-- Integrated Search Input with Keyboard Shortcut Pill -->
                    <form action="{{ route('catalog.index') }}" method="GET" class="relative hidden xl:block w-48">
                        <input 
                            type="text" 
                            name="search" 
                            placeholder="Quick search..." 
                            class="w-full bg-slate-100 hover:bg-slate-50 focus:bg-white border border-slate-200 rounded-xl text-xs py-2 pl-3.5 pr-8 text-slate-800 focus:ring-2 focus:ring-[#c30102]/20 focus:border-[#c30102] transition-all outline-none"
                        >
                        <button type="submit" class="absolute right-3 top-2.5 text-slate-400 hover:text-[#c30102] transition-colors">
                            <i class="fas fa-search text-xs"></i>
                        </button>
                    </form>
                </div>

                <!-- Mobile Hamburger Toggle -->
                <div class="lg:hidden flex items-center" x-data="{ open: false }">
                    <button @click="open = !open" class="p-2 text-slate-700 hover:text-[#c30102] focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <!-- Mobile Drawer -->
                    <div x-show="open" @click.away="open = false" x-transition class="absolute top-20 left-0 w-full bg-white/98 backdrop-blur-xl border-b border-slate-200 shadow-2xl p-5 space-y-3 z-50 text-sm font-semibold" style="display: none;">
                        <a href="{{ url('/') }}" class="block py-2 hover:text-[#c30102] border-b border-slate-100">Home</a>
                        <a href="{{ route('catalog.index') }}" class="block py-2 hover:text-[#c30102] border-b border-slate-100">Course Catalog</a>
                        <a href="{{ route('faculty.index') }}" class="block py-2 hover:text-[#c30102] border-b border-slate-100">Distinguished Faculty</a>
                        <a href="{{ route('blog.index') }}" class="block py-2 hover:text-[#c30102] border-b border-slate-100">Blog & Insights</a>
                        <a href="{{ route('feed.index') }}" class="block py-2 hover:text-[#c30102] border-b border-slate-100">Community Feed</a>
                        <a href="{{ route('catalog.category', 'export-import') }}" class="block py-2 hover:text-[#c30102] border-b border-slate-100">Export – Import</a>
                        <a href="{{ route('about.index') }}" class="block py-2 hover:text-[#c30102] border-b border-slate-100">About Us</a>
                        <a href="{{ route('jobs.index') }}" class="block py-2 hover:text-[#c30102] border-b border-slate-100">Careers</a>
                        <a href="{{ route('wishlist.index') }}" class="block py-2 hover:text-[#c30102] border-b border-slate-100">My Wishlist</a>
                        <a href="{{ route('contact.index') }}" class="block py-2 hover:text-[#c30102] border-b border-slate-100">Contact</a>
                        <div class="pt-3">
                            @auth
                                <a href="{{ route('dashboard') }}" class="block w-full text-center py-2.5 btn-ipdp-primary rounded-xl font-bold">My Account</a>
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
                        <img class="h-11 w-11 object-contain rounded-full bg-white p-0.5 shadow-md" src="{{ asset('images/logo.png') }}" alt="CAB Academy">
                        <div>
                            <h3 class="text-white font-black text-lg leading-tight uppercase tracking-tight">CAB Academy</h3>
                            <span class="text-[10px] text-[#c30102] font-black tracking-widest uppercase block">Learn, Grow, Lead.</span>
                        </div>
                    </div>
                    <p class="leading-relaxed mb-6 text-xs sm:text-sm text-slate-300">
                        CAB Academy is a specialized professional training institute delivering practical, industry-standard certification programs in Export-Import, Supply Chain, VAT & Tax, and Corporate Leadership.
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
                        <li><a href="{{ route('catalog.index') }}" class="hover:text-white transition-colors">Course</a></li>
                        <li><a href="{{ route('faculty.index') }}" class="hover:text-white transition-colors">Distinguished Faculty</a></li>
                        <li><a href="{{ route('blog.index') }}" class="hover:text-white transition-colors">Blog & Insights</a></li>
                        <li><a href="{{ route('feed.index') }}" class="hover:text-white transition-colors">Community Feed</a></li>
                        <li><a href="{{ route('catalog.category', 'export-import') }}" class="hover:text-white transition-colors">Export – Import</a></li>
                        <li><a href="{{ route('about.index') }}" class="hover:text-white transition-colors">About Us</a></li>
                        <li><a href="{{ route('contact.index') }}" class="hover:text-white transition-colors">Contact & Admissions</a></li>
                        <li><a href="{{ route('jobs.index') }}" class="hover:text-white transition-colors">Careers</a></li>
                    </ul>
                </div>

                <!-- Col 3: Contact & Operational Office -->
                <div>
                    <h4 class="text-white font-bold text-base mb-4 tracking-wide uppercase text-xs">Contact</h4>
                    <div class="space-y-2.5 text-xs sm:text-sm">
                        <p class="leading-relaxed">
                            <strong class="text-white">Operational Office Address:</strong><br>
                            Mirpur-Pallabi, Dhaka, Bangladesh, 1216
                        </p>
                        <p>
                            <a href="mailto:commercialacademy868@gmail.com" class="hover:text-white transition-colors">
                                commercialacademy868@gmail.com
                            </a>
                        </p>
                        <p>
                            <a href="tel:01917054664" class="hover:text-white transition-colors">
                                01917-054664
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
        href="https://wa.me/8801917054664" 
        target="_blank" 
        rel="noopener noreferrer" 
        class="fixed bottom-5 right-5 z-40 w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-[#25D366] text-white flex items-center justify-center shadow-2xl hover:scale-110 transition-all duration-300"
        aria-label="Chat on WhatsApp"
        title="Chat on WhatsApp"
    >
        <i class="fab fa-whatsapp text-2xl sm:text-3xl"></i>
    </a>

    <!-- Floating Scroll-To-Top Button (Matches Reference Screenshot) -->
    <button 
        onclick="window.scrollTo({top: 0, behavior: 'smooth'})" 
        class="fixed bottom-5 left-5 z-40 w-10 h-10 rounded-xl bg-slate-900/80 hover:bg-[#c30102] text-white flex items-center justify-center shadow-lg transition-all duration-300"
        aria-label="Scroll to Top"
        title="Scroll to Top"
    >
        <i class="fas fa-chevron-up text-sm"></i>
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
            .catch(err => console.error('Wishlist error:', err));
        };
    </script>

    <!-- Global Express Course Enrollment Drawer -->
    <x-express-enroll-drawer />

</body>
</html>
