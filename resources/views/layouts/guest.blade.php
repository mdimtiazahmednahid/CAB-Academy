<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ \App\Models\Setting::getVal('site_name', 'CAB Academy') }}</title>
        <link rel="icon" href="{{ \App\Models\Setting::getVal('site_logo') }}">

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            :root {
                --primary-color: {{ \App\Models\Setting::getVal('primary_color', '#1F6F54') }};
            }
            .bg-primary { background-color: var(--primary-color); }
            .text-primary { color: var(--primary-color); }
            .border-primary { border-color: var(--primary-color); }
            .focus-ring-primary:focus-within { outline: 2px solid var(--primary-color); outline-offset: 2px; }

            body { font-family: 'Inter', sans-serif; }
            .bg-glass {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
        </style>
    </head>
    <body class="antialiased text-gray-900 bg-white">
        <div class="min-h-screen flex w-full">
            
            <!-- Left Side - Graphic & Branding -->
            <div class="hidden lg:flex lg:w-1/2 relative bg-primary items-center justify-center overflow-hidden">
                <!-- Background Image -->
                <div class="absolute inset-0 z-0">
                    <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" alt="Students learning" class="w-full h-full object-cover opacity-20 mix-blend-overlay">
                </div>
                
                <!-- Abstract Shapes -->
                <div class="absolute top-0 right-0 -mt-20 -mr-20 w-96 h-96 bg-white rounded-full opacity-10 blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-80 h-80 bg-blue-300 rounded-full opacity-20 blur-3xl"></div>

                <div class="relative z-10 w-full max-w-lg px-12">
                    <div class="mb-12">
                        @php $logo = \App\Models\Setting::getVal('site_logo'); @endphp
                        @if($logo)
                            <img class="h-12 w-auto brightness-0 invert" src="{{ $logo }}" alt="Logo">
                        @else
                            <div class="w-12 h-12 bg-white text-primary rounded-xl flex items-center justify-center font-bold text-2xl shadow-lg">
                                {{ substr(\App\Models\Setting::getVal('site_name', 'CAB'), 0, 1) }}
                            </div>
                        @endif
                    </div>

                    <h1 class="text-4xl lg:text-5xl font-extrabold text-white mb-6 leading-tight">
                        Unlock Your Next <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-200 to-white">Career Milestone</span>
                    </h1>
                    <p class="text-primary-100 text-lg mb-8 opacity-90 text-white">
                        Join thousands of learners elevating their skills through industry-leading courses and hands-on projects.
                    </p>

                    <!-- Testimonial / Quote Card -->
                    <div class="bg-glass rounded-2xl p-6 text-white">
                        <svg class="w-8 h-8 text-white/50 mb-4" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                        <p class="font-medium text-lg italic mb-4">"The curriculum is directly aligned with what tech companies are looking for. I landed my dream job within weeks of graduating."</p>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center font-bold">SD</div>
                            <div>
                                <h4 class="font-bold text-sm">Sarah Davis</h4>
                                <span class="text-xs opacity-75">Software Engineer</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Auth Form -->
            <div class="w-full lg:w-1/2 flex flex-col items-center justify-between p-8 sm:p-12 lg:p-24 bg-gray-50/50 relative overflow-y-auto">
                
                <div class="w-full flex-1 flex flex-col justify-center max-w-md my-8">
                    <!-- Mobile Logo -->
                    <div class="mb-8 lg:hidden">
                        <a href="/" class="flex items-center gap-2">
                            @php $logo = \App\Models\Setting::getVal('site_logo'); @endphp
                            @if($logo)
                                <img class="h-8 w-auto" src="{{ $logo }}" alt="Logo">
                            @else
                                <div class="w-8 h-8 bg-primary text-white rounded-lg flex items-center justify-center font-bold text-lg shadow-sm">
                                    {{ substr(\App\Models\Setting::getVal('site_name', 'CAB'), 0, 1) }}
                                </div>
                                <span class="font-bold text-lg tracking-tight text-gray-900">{{ \App\Models\Setting::getVal('site_name', 'CAB Academy') }}</span>
                            @endif
                        </a>
                    </div>
                    
                    {{ $slot }}
                </div>

                <footer class="w-full text-center text-sm text-gray-500 mt-8">
                    Developed by <a href="http://www.neurasoft.top" target="_blank" class="text-primary hover:underline font-medium transition-colors">@NeuraSoft</a>
                </footer>
            </div>

        </div>
    </body>
</html>
