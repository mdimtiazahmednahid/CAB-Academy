<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">

        <title>{{ \App\Models\Setting::getVal('site_name', 'CAB Academy') }}</title>
        <link rel="icon" href="{{ \App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png') }}">

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            :root {
                --primary-color: {{ \App\Models\Setting::getVal('primary_color', '#051c45') }};
                --accent-color: #c30102;
            }
            .bg-primary { background-color: var(--primary-color); }
            .text-primary { color: var(--primary-color); }
            .border-primary { border-color: var(--primary-color); }
            .focus-ring-primary:focus-within { outline: 2px solid var(--primary-color); outline-offset: 2px; }

            body { font-family: 'Inter', sans-serif; }
            .bg-glass {
                background: rgba(255, 255, 255, 0.08);
                backdrop-filter: blur(16px);
                border: 1px solid rgba(255, 255, 255, 0.15);
            }
        </style>
    </head>
    <body class="antialiased text-gray-900 bg-white">
        <div class="min-h-screen flex w-full">
            
            <!-- Left Side - Graphic & Branding -->
            <div class="hidden lg:flex lg:w-1/2 relative bg-gradient-to-br from-[#031330] via-[#051c45] to-[#0a2540] border-r border-[#1e2d4a] items-center justify-center overflow-hidden">
                <!-- Background Image -->
                <div class="absolute inset-0 z-0">
                    <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" alt="Students learning" class="w-full h-full object-cover opacity-10 mix-blend-overlay">
                </div>
                
                <!-- Abstract Radial Glows -->
                <div class="absolute top-0 right-0 -mt-20 -mr-20 w-96 h-96 bg-[#c30102]/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-80 h-80 bg-[#10b981]/15 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 w-full max-w-lg px-12">
                    <div class="mb-10">
                        <a href="{{ url('/') }}" class="inline-flex items-center gap-3.5 group">
                            <img class="h-14 w-14 rounded-full bg-white p-1 object-contain shadow-lg group-hover:scale-105 transition-transform" src="{{ \App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png') }}" alt="CAB Academy">
                            <div class="flex flex-col">
                                <span class="text-2xl font-black text-white tracking-tight uppercase leading-none">{{ \App\Models\Setting::getVal('site_name', 'CAB Academy') }}</span>
                                <span class="text-[10px] text-[#c30102] font-black uppercase tracking-widest mt-1">Learn, Grow, Lead.</span>
                            </div>
                        </a>
                    </div>

                    <h1 class="text-3xl lg:text-4xl font-black text-white mb-4 leading-tight tracking-tight">
                        Elevate Your Executive <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-400 via-rose-300 to-white">Career Milestone</span>
                    </h1>
                    <p class="text-slate-300 text-sm mb-8 leading-relaxed">
                        Join 5,000+ business professionals mastering Export-Import, Supply Chain, and Customs VAT through real-world simulations.
                    </p>

                    <!-- Testimonial / Quote Card -->
                    <div class="bg-glass rounded-2xl p-6 text-white shadow-2xl">
                        <i class="fas fa-quote-left text-white/30 text-2xl mb-3 block"></i>
                        <p class="font-normal text-sm leading-relaxed mb-4 text-slate-200">"The hands-on L/C documentation drills and Customs Act 2023 modules gave me immediate operational mastery. I was promoted to Senior Commercial Manager within months of graduating."</p>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-[#c30102] text-white flex items-center justify-center font-black text-sm shadow">TA</div>
                            <div>
                                <h4 class="font-bold text-sm text-white">Tanvir Ahmed</h4>
                                <span class="text-xs text-slate-300">Head of Supply Chain & Commercial Operations</span>
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
                    Developed by <a href="https://neurasoft.top" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline font-semibold transition-colors">NeuraSoft</a>
                </footer>
            </div>

        </div>
    </body>
</html>
