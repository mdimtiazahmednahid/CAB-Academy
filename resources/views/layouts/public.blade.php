<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Course Catalog') - {{ \App\Models\Setting::getVal('site_name', 'CAB Academy') }}</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}">

    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind & App JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --color-content-primary: #f15a29;
            --color-content-secondary: #90c642;
            --color-content-bg-alt: #f2f4f7;
            --color-content-heading: #0f131f;
            --color-content-text: #35415b;
            --color-content-faded: #727f9f;
            --color-content-border: #e3e7f0;
            --color-footer-bg: #0f131f;
            --color-subfooter-bg: #35415b;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--color-content-bg-alt);
            color: var(--color-content-text);
        }

        .bg-ipdp-primary { background-color: #f15a29; }
        .bg-ipdp-secondary { background-color: #90c642; }
        .text-ipdp-primary { color: #f15a29; }
        .text-ipdp-secondary { color: #90c642; }
        .border-ipdp-primary { border-color: #f15a29; }
        .border-ipdp-secondary { border-color: #90c642; }

        /* Exact IPDP Buttons */
        .btn-ipdp-primary {
            background-color: #f15a29;
            color: #ffffff;
            transition: all 0.3s ease;
        }
        .btn-ipdp-primary:hover {
            background-color: #90c642;
            color: #ffffff;
        }

        .btn-ipdp-secondary {
            background-color: #90c642;
            color: #ffffff;
            transition: all 0.3s ease;
        }
        .btn-ipdp-secondary:hover {
            background-color: #f15a29;
            color: #ffffff;
        }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col selection:bg-[#f15a29] selection:text-white">

    <!-- Header -->
    <header class="sticky top-0 z-50 bg-white border-b border-[#e3e7f0] shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- CAB Academy Brand Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-3 shrink-0 group">
                    <img class="h-12 w-12 sm:h-14 sm:w-14 object-contain rounded-full shadow-sm bg-white border border-gray-100 group-hover:scale-105 transition-transform" src="{{ asset('images/logo.png') }}" alt="CAB Academy">
                    <div class="flex flex-col">
                        <span class="text-lg sm:text-xl font-black tracking-tight text-[#0a2540] uppercase leading-none">CAB Academy</span>
                        <span class="text-[9px] sm:text-[10px] tracking-widest uppercase font-bold text-[#dc2626] mt-1">Learn, Grow, Lead.</span>
                    </div>
                </a>

                <!-- Desktop Navigation Menu -->
                <nav class="hidden lg:flex items-center space-x-6 text-[15px] font-semibold text-[#35415b]" x-data="{ courseDropdown: false }">
                    <a href="{{ url('/') }}" class="hover:text-[#f15a29] transition-colors {{ request()->is('/') ? 'text-[#f15a29]' : '' }}">
                        Home
                    </a>

                    <!-- Course Dropdown -->
                    <div class="relative py-2" @mouseenter="courseDropdown = true" @mouseleave="courseDropdown = false">
                        <a href="{{ route('catalog.index') }}" class="flex items-center gap-1 hover:text-[#f15a29] transition-colors {{ request()->is('catalog*') || request()->is('courses*') ? 'text-[#f15a29]' : '' }}">
                            <span>Course</span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="courseDropdown ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </a>
                        <!-- Submenu -->
                        <div 
                            x-show="courseDropdown" 
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-1"
                            class="absolute top-full left-0 w-64 bg-white rounded-xl shadow-xl border border-[#e3e7f0] py-2 z-50"
                            style="display: none;"
                        >
                            <a href="{{ route('catalog.index') }}" class="block px-4 py-2.5 text-sm hover:bg-[#f2f4f7] hover:text-[#f15a29] transition-colors font-medium">
                                All Courses
                            </a>
                            <a href="{{ route('catalog.certificate') }}" class="block px-4 py-2.5 text-sm hover:bg-[#f2f4f7] hover:text-[#f15a29] transition-colors font-medium">
                                Certificate Course
                            </a>
                            <a href="{{ route('catalog.day_long') }}" class="block px-4 py-2.5 text-sm hover:bg-[#f2f4f7] hover:text-[#f15a29] transition-colors font-medium">
                                Day Long Course
                            </a>
                            <div class="border-t border-gray-100 my-1"></div>
                            <a href="{{ route('catalog.category', 'export-import') }}" class="block px-4 py-2.5 text-sm hover:bg-[#f2f4f7] hover:text-[#f15a29] transition-colors font-medium">
                                Export – Import
                            </a>
                            <a href="{{ route('catalog.category', 'supply-chain-management') }}" class="block px-4 py-2.5 text-sm hover:bg-[#f2f4f7] hover:text-[#f15a29] transition-colors font-medium">
                                Supply Chain Management
                            </a>
                            <a href="{{ route('catalog.category', 'accounting') }}" class="block px-4 py-2.5 text-sm hover:bg-[#f2f4f7] hover:text-[#f15a29] transition-colors font-medium">
                                Accounting & Finance
                            </a>
                        </div>
                    </div>

                    <!-- Distinguished Faculty (Rich terminology replacing 'Teacher') -->
                    <a href="{{ route('faculty.index') }}" class="hover:text-[#f15a29] transition-colors {{ request()->is('faculty*') ? 'text-[#f15a29]' : '' }}">
                        Faculty
                    </a>

                    <!-- Knowledge Hub / Blog -->
                    <a href="{{ route('blog.index') }}" class="hover:text-[#f15a29] transition-colors {{ request()->is('blog*') ? 'text-[#f15a29]' : '' }}">
                        Blog
                    </a>

                    <!-- Community Feed -->
                    <a href="{{ route('feed.index') }}" class="hover:text-[#f15a29] transition-colors {{ request()->is('feed*') || request()->is('community*') ? 'text-[#f15a29]' : '' }}">
                        Feed
                    </a>

                    <a href="{{ route('about.index') }}" class="hover:text-[#f15a29] transition-colors {{ request()->routeIs('about.*') ? 'text-[#f15a29]' : '' }}">
                        About Us
                    </a>

                    <a href="{{ route('jobs.index') }}" class="hover:text-[#f15a29] transition-colors {{ request()->is('jobs*') ? 'text-[#f15a29]' : '' }}">
                        Careers
                    </a>

                    <a href="{{ route('contact.index') }}" class="hover:text-[#f15a29] transition-colors {{ request()->routeIs('contact.*') ? 'text-[#f15a29]' : '' }}">
                        Contact
                    </a>
                </nav>

                <!-- Actions: Wishlist Heart, Auth, Header Search (Exact IPDP Layout) -->
                <div class="hidden sm:flex items-center gap-3">
                    <!-- Wishlist Heart Icon with dynamic badge -->
                    <a href="{{ route('wishlist.index') }}" title="Saved Wishlist" class="relative text-[#f15a29] p-2 hover:scale-110 transition-transform">
                        <i class="fas fa-heart text-lg"></i>
                        <span id="wishlist-count-badge" class="absolute -top-1 -right-1 bg-[#f15a29] text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center {{ auth()->check() && auth()->user()->wishlists()->count() > 0 ? '' : 'hidden' }}">
                            {{ auth()->check() ? auth()->user()->wishlists()->count() : 0 }}
                        </span>
                    </a>

                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-ipdp-primary px-5 py-2.5 rounded text-sm font-semibold shadow-sm flex items-center gap-2">
                            <i class="far fa-user"></i>
                            <span>My Account</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-ipdp-primary px-5 py-2 rounded text-sm font-semibold shadow-sm">
                            Sign In
                        </a>
                    @endauth

                    <!-- Integrated Search Input -->
                    <form action="{{ route('catalog.index') }}" method="GET" class="relative hidden xl:block w-44">
                        <input 
                            type="text" 
                            name="search" 
                            placeholder="Search..." 
                            class="w-full bg-[#f2f4f7] border border-[#e3e7f0] rounded text-xs py-2 pl-3 pr-8 text-[#35415b] focus:ring-1 focus:ring-[#f15a29] focus:outline-none"
                        >
                        <button type="submit" class="absolute right-2.5 top-2.5 text-gray-500 hover:text-[#f15a29]">
                            <i class="fas fa-search text-xs"></i>
                        </button>
                    </form>
                </div>

                <!-- Mobile Hamburger Toggle -->
                <div class="lg:hidden flex items-center" x-data="{ open: false }">
                    <button @click="open = !open" class="p-2 text-gray-700 hover:text-[#f15a29]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <!-- Mobile Drawer -->
                    <div x-show="open" @click.away="open = false" x-transition class="absolute top-20 left-0 w-full bg-white border-b border-[#e3e7f0] shadow-xl p-4 space-y-3 z-50 text-sm font-semibold" style="display: none;">
                        <a href="{{ url('/') }}" class="block py-2 hover:text-[#f15a29]">Home</a>
                        <a href="{{ route('catalog.index') }}" class="block py-2 hover:text-[#f15a29]">Course Catalog</a>
                        <a href="{{ route('faculty.index') }}" class="block py-2 hover:text-[#f15a29]">Distinguished Faculty</a>
                        <a href="{{ route('blog.index') }}" class="block py-2 hover:text-[#f15a29]">Blog & Insights</a>
                        <a href="{{ route('feed.index') }}" class="block py-2 hover:text-[#f15a29]">Community Feed</a>
                        <a href="{{ route('catalog.category', 'export-import') }}" class="block py-2 hover:text-[#f15a29]">Export – Import</a>
                        <a href="{{ route('about.index') }}" class="block py-2 hover:text-[#f15a29]">About Us</a>
                        <a href="{{ route('jobs.index') }}" class="block py-2 hover:text-[#f15a29]">Careers</a>
                        <a href="{{ route('wishlist.index') }}" class="block py-2 hover:text-[#f15a29]">My Wishlist</a>
                        <a href="{{ route('contact.index') }}" class="block py-2 hover:text-[#f15a29]">Contact</a>
                        <div class="pt-3 border-t border-gray-100">
                            @auth
                                <a href="{{ route('dashboard') }}" class="block w-full text-center py-2.5 btn-ipdp-primary rounded font-bold">My Account</a>
                            @else
                                <div class="flex gap-2">
                                    <a href="{{ route('login') }}" class="w-1/2 text-center py-2 btn-ipdp-primary rounded font-bold">Sign In</a>
                                    <a href="{{ route('register') }}" class="w-1/2 text-center py-2 btn-ipdp-secondary rounded font-bold">Register</a>
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
    <footer id="contact" class="bg-[#0f131f] text-[#8d9096] pt-16 pb-8 border-t border-[#2b3242] mt-16 text-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
                <!-- Col 1: About Institute -->
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <img class="h-11 w-11 object-contain rounded-full bg-white p-0.5" src="{{ asset('images/logo.png') }}" alt="CAB Academy">
                        <div>
                            <h3 class="text-white font-black text-lg leading-tight uppercase tracking-tight">CAB Academy</h3>
                            <span class="text-[10px] text-[#dc2626] font-bold tracking-widest uppercase block">Learn, Grow, Lead.</span>
                        </div>
                    </div>
                    <p class="leading-relaxed mb-6 text-xs sm:text-sm">
                        CAB Academy is a specialized professional training institute delivering practical, industry-standard certification programs in Export-Import, Supply Chain, VAT & Tax, and Corporate Leadership.
                    </p>
                    <div class="flex items-center gap-3">
                        <a href="#" class="w-9 h-9 rounded-full bg-[#1e2533] flex items-center justify-center text-white hover:bg-[#f15a29] transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-9 h-9 rounded-full bg-[#1e2533] flex items-center justify-center text-white hover:bg-[#f15a29] transition-colors">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="w-9 h-9 rounded-full bg-[#1e2533] flex items-center justify-center text-white hover:bg-[#f15a29] transition-colors">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>

                <!-- Col 2: Useful Links -->
                <div>
                    <h4 class="text-white font-bold text-base mb-4">Useful Links</h4>
                    <ul class="space-y-2 text-xs sm:text-sm">
                        <li><a href="{{ url('/') }}" class="hover:text-[#f15a29] transition-colors">Home</a></li>
                        <li><a href="{{ route('catalog.index') }}" class="hover:text-[#f15a29] transition-colors">Course</a></li>
                        <li><a href="{{ route('faculty.index') }}" class="hover:text-[#f15a29] transition-colors">Distinguished Faculty</a></li>
                        <li><a href="{{ route('blog.index') }}" class="hover:text-[#f15a29] transition-colors">Blog & Insights</a></li>
                        <li><a href="{{ route('feed.index') }}" class="hover:text-[#f15a29] transition-colors">Community Feed</a></li>
                        <li><a href="{{ route('catalog.category', 'export-import') }}" class="hover:text-[#f15a29] transition-colors">Export – Import</a></li>
                        <li><a href="{{ route('about.index') }}" class="hover:text-[#f15a29] transition-colors">About Us</a></li>
                        <li><a href="{{ route('contact.index') }}" class="hover:text-[#f15a29] transition-colors">Contact & Admissions</a></li>
                        <li><a href="{{ route('jobs.index') }}" class="hover:text-[#f15a29] transition-colors">Careers</a></li>
                    </ul>
                </div>

                <!-- Col 3: Contact & Operational Office -->
                <div>
                    <h4 class="text-white font-bold text-base mb-4">Contact</h4>
                    <div class="space-y-2.5 text-xs sm:text-sm">
                        <p class="leading-relaxed">
                            <strong class="text-white">Operational Office Address:</strong><br>
                            Mirpur-Pallabi, Dhaka, Bangladesh, 1216
                        </p>
                        <p>
                            <a href="mailto:commercialacademy868@gmail.com" class="hover:text-[#f15a29] transition-colors">
                                commercialacademy868@gmail.com
                            </a>
                        </p>
                        <p>
                            <a href="tel:01917054664" class="hover:text-[#f15a29] transition-colors">
                                01917-054664
                            </a>
                        </p>
                    </div>
                </div>

                <!-- Col 4: Legal & Compliance -->
                <div>
                    <h4 class="text-white font-bold text-base mb-4">Legal & Compliance</h4>
                    <ul class="space-y-2 text-xs sm:text-sm">
                        <li><a href="{{ route('legal.privacy') }}" class="hover:text-[#f15a29] transition-colors">Privacy Policy</a></li>
                        <li><a href="{{ route('legal.refund') }}" class="hover:text-[#f15a29] transition-colors">Refund and Returns Policy</a></li>
                        <li><a href="{{ route('legal.terms') }}" class="hover:text-[#f15a29] transition-colors">Terms of Service</a></li>
                    </ul>
                </div>
            </div>

            <!-- Subfooter (With NeuraSoft Attribution) -->
            <div class="border-t border-[#2b3242] pt-6 pb-6 flex flex-col sm:flex-row justify-between items-center text-xs text-[#8d9096] gap-3">
                <p>© {{ date('Y') }} CAB Academy. All Rights Reserved.</p>
                <p class="sm:mr-20">
                    Developed by <a href="https://neurasoft.top" target="_blank" rel="noopener noreferrer" class="text-white hover:text-[#f15a29] font-bold underline underline-offset-4 transition-colors">NeuraSoft</a>
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
        class="fixed bottom-5 left-5 z-40 w-10 h-10 rounded-lg bg-gray-800/80 hover:bg-[#f15a29] text-white flex items-center justify-center shadow-lg transition-all duration-300"
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
                        btn.classList.add('text-[#f15a29]');
                        if (icon) {
                            icon.classList.remove('far');
                            icon.classList.add('fas', 'text-[#f15a29]');
                        }
                        if (text) text.innerText = 'Saved in Wishlist';
                    } else {
                        btn.classList.remove('text-[#f15a29]');
                        if (icon) {
                            icon.classList.remove('fas', 'text-[#f15a29]');
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
