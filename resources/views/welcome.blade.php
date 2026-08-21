<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ \App\Models\Setting::getVal('site_name', 'CAB Academy') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
        .hero-pattern {
            background-color: #ffffff;
            background-image: radial-gradient(#e5e7eb 1px, transparent 1px);
            background-size: 20px 20px;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body class="antialiased text-gray-900 selection:bg-primary selection:text-white">

    @php
        $settings = \App\Models\Setting::where('key', 'like', 'landing_%')->pluck('value', 'key')->toArray();
        $mode = $settings['landing_page_mode'] ?? 'default';
        $customHtml = $settings['landing_custom_html'] ?? '';

        if ($mode === 'custom' && !empty(trim($customHtml))) {
            echo $customHtml;
            exit;
        }
        
        $getSetting = function($key, $default) use ($settings) {
            return !empty($settings[$key]) ? $settings[$key] : $default;
        };
        
        $heroTitle = $getSetting('landing_hero_title', 'Unlock Your Full Potential');
        $heroSubtitle = $getSetting('landing_hero_subtitle', 'Learn from industry experts and take your career to the next level with our premium courses.');
        $ctaText = $getSetting('landing_cta_text', 'Get Started Today');
        
        $features = [
            [
                'title' => $getSetting('landing_feature_1_title', 'Expert Instructors'),
                'desc' => $getSetting('landing_feature_1_desc', 'Learn from the best in the industry.'),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />'
            ],
            [
                'title' => $getSetting('landing_feature_2_title', 'Flexible Learning'),
                'desc' => $getSetting('landing_feature_2_desc', 'Study at your own pace, anywhere, anytime.'),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />'
            ],
            [
                'title' => $getSetting('landing_feature_3_title', 'Career Support'),
                'desc' => $getSetting('landing_feature_3_desc', 'Get guidance to land your dream job.'),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />'
            ]
        ];
    @endphp

    <!-- Navigation -->
    <nav class="fixed w-full z-50 glass-card transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center gap-3">
                    @php $logo = \App\Models\Setting::getVal('site_logo'); @endphp
                    @if($logo)
                        <img class="h-10 w-auto" src="{{ $logo }}" alt="Logo">
                    @else
                        <div class="w-10 h-10 bg-primary text-white rounded-xl flex items-center justify-center font-bold text-xl shadow-lg shadow-primary/30">
                            C
                        </div>
                        <span class="font-bold text-xl tracking-tight hidden sm:block">{{ \App\Models\Setting::getVal('site_name', 'CAB Academy') }}</span>
                    @endif
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#" class="text-gray-600 hover:text-primary font-medium transition-colors">Courses</a>
                    <a href="#" class="text-gray-600 hover:text-primary font-medium transition-colors">Pricing</a>
                    <a href="#" class="text-gray-600 hover:text-primary font-medium transition-colors">About</a>
                    
                    <div class="flex items-center space-x-4 pl-4 border-l border-gray-200">
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-primary font-medium transition-colors">Dashboard</a>
                            <a href="{{ route('dashboard') }}" class="px-5 py-2.5 bg-primary text-white font-medium rounded-full hover:opacity-90 transition-all shadow-lg shadow-primary/30 transform hover:-translate-y-0.5">My Account</a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-primary font-medium transition-colors">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-5 py-2.5 bg-primary text-white font-medium rounded-full hover:opacity-90 transition-all shadow-lg shadow-primary/30 transform hover:-translate-y-0.5">Get Started</a>
                            @endif
                        @endauth
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button class="text-gray-600 hover:text-gray-900 focus:outline-none p-2">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden hero-pattern">
        
        <!-- Under Construction Banner -->
        <div class="absolute top-20 left-0 right-0 z-40 bg-yellow-400 text-yellow-900 shadow-md transform -translate-y-2">
            <div class="max-w-7xl mx-auto py-3 px-3 sm:px-6 lg:px-8">
                <div class="flex items-center justify-center flex-wrap gap-2 text-center">
                    <svg class="h-6 w-6 text-yellow-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span class="font-extrabold text-lg md:text-xl uppercase tracking-widest">
                        THIS SITE IS CURRENTLY UNDER CONSTRUCTION
                    </span>
                    <span class="text-sm font-medium">Some features may be incomplete or unavailable.</span>
                </div>
            </div>
        </div>
        <!-- Abstract Shapes -->
        <div class="absolute top-0 right-0 -mt-20 -mr-20 w-96 h-96 bg-primary/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <div class="inline-flex items-center px-4 py-2 rounded-full bg-primary/10 text-primary font-medium text-sm mb-8 animate-fade-in-up">
                <span class="flex h-2 w-2 rounded-full bg-primary mr-2 animate-pulse"></span>
                Join 10,000+ students learning today
            </div>
            
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold tracking-tight text-gray-900 mb-6 leading-tight animate-fade-in-up" style="animation-delay: 0.1s;">
                {{ $heroTitle }}
            </h1>
            
            <p class="mt-4 text-xl md:text-2xl text-gray-500 max-w-3xl mx-auto mb-10 animate-fade-in-up leading-relaxed" style="animation-delay: 0.2s;">
                {{ $heroSubtitle }}
            </p>
            
            <div class="flex flex-col sm:flex-row justify-center items-center gap-4 animate-fade-in-up" style="animation-delay: 0.3s;">
                <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 bg-primary text-white font-bold rounded-full hover:opacity-90 transition-all shadow-xl shadow-primary/30 transform hover:-translate-y-1 text-lg">
                    {{ $ctaText }}
                </a>
                <a href="#features" class="w-full sm:w-auto px-8 py-4 bg-white text-gray-700 font-bold rounded-full border border-gray-200 hover:border-gray-300 hover:bg-gray-50 transition-all shadow-sm text-lg">
                    Explore Features
                </a>
            </div>

            <!-- Hero Graphic -->
            <div class="mt-16 sm:mt-24 w-full max-w-5xl mx-auto animate-fade-in-up relative" style="animation-delay: 0.4s;">
                <div class="absolute inset-0 bg-gradient-to-b from-transparent to-[#f8fafc] z-10 bottom-0 top-1/2"></div>
                <div class="relative rounded-2xl overflow-hidden shadow-2xl border border-gray-200/50 transform transition hover:scale-[1.02] duration-500">
                    <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" alt="Learning Platform Dashboard" class="w-full h-auto object-cover">
                    <div class="absolute inset-0 bg-primary mix-blend-overlay opacity-20"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="py-24 bg-white relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-base text-primary font-semibold tracking-wide uppercase">Why Choose Us</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    Everything you need to succeed
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                @foreach($features as $index => $feature)
                <div class="glass-card rounded-2xl p-8 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 group">
                    <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-primary transition-all duration-300">
                        <svg class="w-7 h-7 text-primary group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $feature['icon'] !!}
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $feature['title'] }}</h3>
                    <p class="text-gray-500 leading-relaxed">
                        {{ $feature['desc'] }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-primary relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-24 relative z-10 text-center">
            <h2 class="text-3xl font-extrabold text-white sm:text-4xl mb-4">
                Ready to dive in?
            </h2>
            <p class="text-xl text-primary-100 mb-8 max-w-2xl mx-auto text-opacity-90 text-white">
                Start your journey today and unlock endless possibilities with our expertly crafted courses.
            </p>
            <a href="{{ route('register') }}" class="inline-block px-8 py-4 bg-white text-primary font-bold rounded-full hover:bg-gray-50 transition-all shadow-lg transform hover:-translate-y-1 text-lg">
                Create Free Account
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 pt-16 pb-8 text-gray-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div class="col-span-1 md:col-span-1">
                    <span class="font-bold text-2xl text-white tracking-tight mb-4 block">{{ \App\Models\Setting::getVal('site_name', 'CAB Academy') }}</span>
                    <p class="text-sm">Empowering learners worldwide with cutting-edge education and career support.</p>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Platform</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition-colors">Browse Courses</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Pricing</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Success Stories</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Support</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition-colors">Help Center</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Terms of Service</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Privacy Policy</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Contact</h4>
                    <p class="text-sm mb-2">Have questions?</p>
                    <a href="mailto:{{ \App\Models\Setting::getVal('contact_email', 'support@example.com') }}" class="text-primary hover:text-white transition-colors text-sm font-medium">
                        {{ \App\Models\Setting::getVal('contact_email', 'support@example.com') }}
                    </a>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center text-sm">
                <p>&copy; {{ date('Y') }} {{ \App\Models\Setting::getVal('site_name', 'CAB Academy') }}. All rights reserved.</p>
                <div class="flex space-x-6 mt-4 md:mt-0">
                    <a href="#" class="hover:text-white transition-colors">
                        <span class="sr-only">Twitter</span>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/></svg>
                    </a>
                    <a href="#" class="hover:text-white transition-colors">
                        <span class="sr-only">GitHub</span>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }
    </style>
</body>
</html>
