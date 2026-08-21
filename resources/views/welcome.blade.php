<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ \App\Models\Setting::getVal('site_name', 'CAB Academy') }}</title>
    <link rel="icon" href="{{ \App\Models\Setting::getVal('site_logo') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: {{ \App\Models\Setting::getVal('primary_color', '#1F6F54') }};
        }
        .bg-primary { background-color: var(--primary-color); }
        .text-primary { color: var(--primary-color); }
        .border-primary { border-color: var(--primary-color); }
        .focus-ring-primary:focus-within { outline: 2px solid var(--primary-color); outline-offset: 2px; }
        
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
        
        $heroTitle = $getSetting('landing_hero_title', 'Master Your Future with ' . \App\Models\Setting::getVal('site_name', 'CAB Academy'));
        $heroSubtitle = $getSetting('landing_hero_subtitle', 'Join thousands of students learning in-demand skills from industry experts. Start your journey today and transform your career.');
        $ctaText = $getSetting('landing_hero_cta', 'Explore Courses');
        
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
    <nav x-data="{ open: false }" class="fixed w-full z-50 glass-card transition-all duration-300">
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
                    <a href="{{ route('catalog.index') }}" class="text-gray-600 hover:text-primary font-medium transition-colors">Courses</a>
                    <a href="{{ route('jobs.index') }}" class="text-gray-600 hover:text-primary font-medium transition-colors">Careers</a>
                    <a href="{{ route('register') }}" class="text-gray-600 hover:text-primary font-medium transition-colors">Pricing</a>
                    <a href="#features" class="text-gray-600 hover:text-primary font-medium transition-colors">About</a>
                    
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
                    <button @click="open = !open" class="text-gray-600 hover:text-gray-900 focus:outline-none p-2">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Panel -->
        <div x-show="open" @click.away="open = false" x-transition class="md:hidden bg-white shadow-lg absolute w-full left-0 border-t border-gray-100 z-50" style="display: none;">
            <div class="px-4 pt-2 pb-3 space-y-1">
                <a href="{{ route('catalog.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50 rounded-md">Courses</a>
                <a href="{{ route('jobs.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50 rounded-md">Careers</a>
                <a href="{{ route('register') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50 rounded-md">Pricing</a>
                <a href="#features" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50 rounded-md">About</a>
            </div>
            <div class="pt-4 pb-4 border-t border-gray-200">
                <div class="px-5 space-y-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="block w-full text-center px-4 py-2 border border-transparent text-base font-medium rounded-md text-white bg-primary hover:bg-primary/90">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="block w-full text-center px-4 py-2 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="block w-full text-center px-4 py-2 border border-transparent text-base font-medium rounded-md text-white bg-primary hover:bg-primary/90">Get Started</a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden hero-pattern">
        
        @if(\App\Models\Setting::getVal('under_construction_mode') == '1')
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
        @endif        <!-- Abstract Shapes -->
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

    <!-- Latest Courses Section -->
    @if(isset($courses) && $courses->count() > 0)
    <div id="courses" class="py-24 bg-gray-50 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-base text-primary font-semibold tracking-wide uppercase">Latest Courses</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    Expand Your Knowledge
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pb-8">
                @foreach($courses as $course)
                    <a href="{{ route('courses.show', $course) }}" class="group bg-white rounded-3xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-gray-100 overflow-hidden hover:shadow-[0_4px_25px_rgba(0,0,0,0.06)] transition-all duration-300 flex flex-col active:scale-[0.98]">
                        <!-- Thumbnail Placeholder -->
                        <div class="w-full h-48 bg-gray-100 relative overflow-hidden flex items-center justify-center">
                            @if($course->cover_image)
                                <img src="{{ Storage::url($course->cover_image) }}" alt="{{ $course->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @elseif($course->thumbnail)
                                <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="absolute inset-0 bg-gradient-to-br from-primary/80 to-primary"></div>
                                <span class="relative text-white font-bold text-3xl opacity-80">{{ substr($course->title, 0, 1) }}</span>
                            @endif
                            
                            @if($course->price > 0)
                                <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm text-gray-900 font-bold px-3 py-1 rounded-full text-sm shadow-sm">
                                    ৳{{ number_format($course->price, 2) }}
                                </div>
                            @else
                                <div class="absolute top-3 right-3 bg-green-500/90 backdrop-blur-sm text-white font-bold px-3 py-1 rounded-full text-sm shadow-sm">
                                    Free
                                </div>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="p-5 flex-1 flex flex-col">
                            <h3 class="font-bold text-gray-900 text-lg mb-2 line-clamp-2 group-hover:text-primary transition-colors">{{ $course->title }}</h3>
                            <p class="text-sm text-gray-500 line-clamp-2 mb-4 flex-1">{{ $course->description ?? 'No description available for this course.' }}</p>
                            
                            <div class="flex flex-wrap items-center gap-3 text-xs font-medium text-gray-500 mb-4">
                                <div class="flex items-center gap-1 bg-gray-50 px-2 py-1 rounded-lg">
                                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477-4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                    {{ $course->sections->flatMap->lessons->count() }} Lessons
                                </div>
                                @if($course->duration)
                                <div class="flex items-center gap-1 bg-gray-50 px-2 py-1 rounded-lg">
                                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ $course->duration }}
                                </div>
                                @endif
                            </div>
                            
                            <div class="flex items-center justify-between mt-auto pt-4 border-t border-gray-50">
                                <span class="text-sm font-bold text-primary flex items-center gap-1 w-full justify-center bg-primary/5 py-2 rounded-xl group-hover:bg-primary group-hover:text-white transition-colors">
                                    View Course <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            
            <div class="text-center mt-4">
                <a href="{{ route('catalog.index') }}" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    View All Courses
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Latest Jobs Section -->
    @if(isset($jobs) && $jobs->count() > 0)
    <div id="jobs" class="py-24 bg-white relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-base text-primary font-semibold tracking-wide uppercase">Career Opportunities</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    Land Your Dream Job
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pb-8">
                @foreach($jobs as $job)
                    <a href="{{ route('jobs.index') }}" class="bg-white rounded-3xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-gray-100 overflow-hidden flex flex-col p-6 hover:shadow-[0_4px_25px_rgba(0,0,0,0.06)] transition-all duration-300 active:scale-[0.98] group">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center gap-4">
                                @if($job->company_logo)
                                    <img src="{{ Storage::url($job->company_logo) }}" alt="{{ $job->company }}" class="w-12 h-12 rounded-xl object-cover border border-gray-100 shadow-sm shrink-0">
                                @else
                                    <div class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center shrink-0 border border-gray-100 shadow-sm">
                                        <span class="text-lg font-bold text-gray-400">{{ substr($job->company, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <h3 class="font-bold text-gray-900 text-xl leading-tight group-hover:text-primary transition-colors">{{ $job->title }}</h3>
                                    <p class="text-primary font-medium mt-0.5">{{ $job->company }}</p>
                                </div>
                            </div>
                            @if($job->created_at->diffInDays(now()) < 7)
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-lg">New</span>
                            @endif
                        </div>

                        <div class="flex flex-wrap gap-3 mb-4">
                            @if($job->location)
                                <div class="flex items-center text-sm text-gray-600 gap-1 bg-gray-50 px-2 py-1 rounded-lg">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.242-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    {{ $job->location }}
                                </div>
                            @endif
                            @if($job->salary_range)
                                <div class="flex items-center text-sm text-gray-600 gap-1 bg-gray-50 px-2 py-1 rounded-lg">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ $job->salary_range }}
                                </div>
                            @endif
                        </div>

                        <div class="text-sm text-gray-600 mb-6 flex-1 line-clamp-3">
                            {{ $job->description }}
                        </div>

                        <div class="flex items-center justify-between mt-auto pt-4 border-t border-gray-50">
                            <span class="text-sm font-bold text-primary flex items-center gap-1 w-full justify-center bg-primary/5 py-2 rounded-xl group-hover:bg-primary group-hover:text-white transition-colors">
                                Apply Now <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
            
            <div class="text-center mt-4">
                <a href="{{ route('jobs.index') }}" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    View All Jobs
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- CTA Section -->
    <div class="bg-primary relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:py-20 lg:px-8 lg:flex lg:items-center lg:justify-between relative z-10">
            <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                <span class="block">{{ \App\Models\Setting::getVal('landing_cta_title', 'Ready to start learning?') }}</span>
                <span class="block text-green-300 text-2xl mt-2 font-normal">{{ \App\Models\Setting::getVal('landing_cta_subtitle', 'Join our community today and get access to all premium features.') }}</span>
            </h2>
            <div class="mt-8 flex lg:mt-0 lg:flex-shrink-0">
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 border border-transparent text-base font-bold rounded-full text-primary bg-white hover:bg-gray-50 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    {{ \App\Models\Setting::getVal('landing_cta_button', 'Get Started for Free') }}
                </a>
            </div>
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
                        <li><a href="{{ route('catalog.index') }}" class="hover:text-white transition-colors">Browse Courses</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white transition-colors">Pricing</a></li>
                        <li><a href="#features" class="hover:text-white transition-colors">Success Stories</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Support</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="mailto:{{ \App\Models\Setting::getVal('contact_email', 'support@example.com') }}" class="hover:text-white transition-colors">Help Center</a></li>
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
                <div class="flex flex-col md:flex-row items-center gap-2 mb-4 md:mb-0">
                    <p>&copy; {{ date('Y') }} {{ \App\Models\Setting::getVal('site_name', 'CAB Academy') }}. All rights reserved.</p>
                    <span class="hidden md:inline text-gray-600">|</span>
                    <p>Developed by <a href="http://www.neurasoft.top" target="_blank" class="text-primary hover:text-white font-medium transition-colors">@NeuraSoft</a></p>
                </div>
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
