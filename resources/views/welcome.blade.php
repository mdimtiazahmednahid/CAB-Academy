@extends('layouts.public')

@section('title', 'Home')

@section('content')
<div>

    <!-- 1. Executive Hero Section (Billion-Dollar Apple/Stripe Grade Canvas) -->
    <section class="relative min-h-[580px] lg:min-h-[640px] flex items-center bg-gradient-to-br from-[#031330] via-[#051c45] to-[#092552] text-white py-16 sm:py-24 overflow-hidden border-b border-[#0a2540]">
        <!-- Ambient Mesh Glows -->
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-[#c30102]/20 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-[#10b981]/15 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
                
                <!-- Left Content: CAB Academy Executive Branding -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white text-xs font-bold uppercase tracking-wider shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-[#c30102] animate-ping"></span>
                        <span class="text-emerald-400 font-extrabold">Admissions Active</span>
                        <span class="text-white/40">&bull;</span>
                        <span class="text-slate-200">Mirpur Campus & Zoom Live</span>
                    </div>

                    <div class="space-y-2">
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight text-white uppercase drop-shadow-sm">
                            CAB Academy
                        </h1>
                        <h3 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 via-teal-300 to-amber-300 tracking-wide">
                            Learn, Grow, Lead.
                        </h3>
                    </div>
                    
                    <p class="text-base sm:text-lg text-slate-300 tracking-normal font-normal max-w-xl leading-relaxed">
                        Empowering Bangladesh's trade professionals through hands-on simulations in Export-Import, Supply Chain, NBR VAT & Tax, and Corporate Leadership guided by frontline directors.
                    </p>

                    <!-- Quick Metrics Strip -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 py-2 border-y border-white/10">
                        <div>
                            <span class="text-xl sm:text-2xl font-black text-white block">5,000+</span>
                            <span class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold">Alumni Trained</span>
                        </div>
                        <div>
                            <span class="text-xl sm:text-2xl font-black text-emerald-400 block">15+</span>
                            <span class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold">Faculty Fellows</span>
                        </div>
                        <div>
                            <span class="text-xl sm:text-2xl font-black text-white block">100%</span>
                            <span class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold">Practical Simulation</span>
                        </div>
                        <div>
                            <span class="text-xl sm:text-2xl font-black text-[#c30102] block">Batch 105</span>
                            <span class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold">Now Enrolling</span>
                        </div>
                    </div>

                    <!-- Call-to-Action Buttons -->
                    <div class="pt-2 flex flex-wrap items-center gap-4">
                        <a href="#home-category" class="px-8 py-4 rounded-xl bg-[#c30102] hover:bg-[#a50102] text-white text-sm font-black uppercase tracking-wider shadow-[0_4px_20px_rgba(195,1,2,0.4)] flex items-center gap-2 transition-all hover:scale-105 active:scale-95">
                            <span>Explore Courses</span>
                            <i class="fas fa-arrow-down text-xs"></i>
                        </a>

                        <a href="{{ route('catalog.category', 'export-import') }}" class="btn-ipdp-secondary px-8 py-4 rounded-xl text-sm font-bold uppercase tracking-wider shadow-lg flex items-center gap-2 hover:scale-105 transition-all">
                            <span>Export – Import</span>
                            <i class="fas fa-chevron-right text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Right Video / Visual Showcase -->
                <div class="lg:col-span-5">
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl border-2 border-white/20 bg-slate-900/60 backdrop-blur-xl group aspect-video sm:aspect-[4/3] flex items-center justify-center">
                        <img 
                            src="https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=1000&q=80" 
                            alt="CAB Academy Executive Training" 
                            class="absolute inset-0 w-full h-full object-cover opacity-75 group-hover:scale-105 transition-transform duration-700"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-[#031330] via-black/40 to-transparent"></div>
                        <a 
                            href="{{ route('catalog.index') }}" 
                            class="relative z-10 w-20 h-20 rounded-full bg-[#c30102] text-white flex items-center justify-center shadow-[0_0_35px_rgba(195,1,2,0.6)] hover:bg-[#a50102] hover:scale-110 transition-all duration-300"
                            aria-label="Watch Introduction"
                        >
                            <i class="fas fa-play text-2xl ml-1 text-white"></i>
                        </a>
                        <div class="absolute bottom-5 left-5 right-5 text-white z-10">
                            <span class="inline-block px-2.5 py-0.5 rounded bg-emerald-500/80 text-[10px] font-black uppercase tracking-wider text-white mb-1.5 backdrop-blur-sm">Practical Case Studies</span>
                            <p class="text-sm font-bold truncate">Live L/C, Customs Bill of Entry & Mushak 6.3 Simulation</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 2. "Our Valuable Clients" (Grid of Corporate Partners) -->
    <section class="py-14 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <span class="text-xs uppercase tracking-widest font-extrabold text-[#c30102] block mb-1">Corporate Trust & Affiliation</span>
                <h2 class="text-xl sm:text-2xl font-black text-[#051c45] tracking-tight uppercase">
                    Our Valuable Clients
                </h2>
            </div>

            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-8 gap-4 sm:gap-6 items-center">
                @php
                    $clients = [
                        ['name' => 'SSG', 'logo' => 'https://ipdpbd.com/wp-content/uploads/2025/06/SSG.jpg'],
                        ['name' => 'United Hospital', 'logo' => 'https://ipdpbd.com/wp-content/uploads/2025/06/United-Hospital.jpg'],
                        ['name' => 'Square', 'logo' => 'https://ipdpbd.com/wp-content/uploads/2025/06/Square.jpg'],
                        ['name' => 'Robi', 'logo' => 'https://ipdpbd.com/wp-content/uploads/2025/06/Robi.jpg'],
                        ['name' => 'Grameenphone', 'logo' => 'https://ipdpbd.com/wp-content/uploads/2025/06/Grameenphone.jpg'],
                        ['name' => 'Incepta', 'logo' => 'https://ipdpbd.com/wp-content/uploads/2025/06/Incepta.jpg'],
                        ['name' => 'IPDC', 'logo' => 'https://ipdpbd.com/wp-content/uploads/2025/06/IPDC.jpg'],
                        ['name' => 'Runner', 'logo' => 'https://ipdpbd.com/wp-content/uploads/2025/06/Runner.jpg'],
                    ];
                @endphp

                @foreach($clients as $client)
                    <div class="p-3 bg-slate-50 hover:bg-white rounded-2xl border border-slate-200/80 flex items-center justify-center h-20 shadow-sm hover:shadow-lg hover:border-[#051c45]/20 transition-all duration-300 group">
                        <img 
                            src="{{ $client['logo'] }}" 
                            alt="{{ $client['name'] }}" 
                            class="max-h-12 max-w-full object-contain filter grayscale group-hover:grayscale-0 transition-all duration-300 group-hover:scale-105"
                            loading="lazy"
                        >
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 3. Exact IPDP "Running Course Categories" Section (Modernized Cards with Soft Shadow) -->
    <section id="home-category" class="py-16 bg-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-xs uppercase tracking-widest font-extrabold text-[#c30102] block mb-1">Executive Disciplines</span>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-[#051c45] tracking-tight">
                    Running Course Categories
                </h2>
                <div class="w-16 h-1 bg-[#c30102] mx-auto mt-3 rounded-full"></div>
            </div>

            @php
                $runningCategories = [
                    ['name' => 'Soft Skills', 'slug' => 'soft-skills', 'img' => 'https://ipdpbd.com/wp-content/uploads/2025/06/Soft-Skills.jpg'],
                    ['name' => 'Supply Chain Management', 'slug' => 'supply-chain-management', 'img' => 'https://ipdpbd.com/wp-content/uploads/2025/06/Supply-Chain-Management.jpg'],
                    ['name' => 'Value Added Tax', 'slug' => 'value-added-tax', 'img' => 'https://ipdpbd.com/wp-content/uploads/2025/06/Income-Tax-Management.jpg'],
                    ['name' => 'Export – Import', 'slug' => 'export-import', 'img' => 'https://ipdpbd.com/wp-content/uploads/2025/06/Import-Export-879.png'],
                    ['name' => 'Accounting', 'slug' => 'accounting', 'img' => 'https://ipdpbd.com/wp-content/uploads/2025/06/Accounting.jpg'],
                    ['name' => 'Human Resource', 'slug' => 'human-resource', 'img' => 'https://ipdpbd.com/wp-content/uploads/2025/06/Human-Recourse.png'],
                    ['name' => 'Civil Construction Project', 'slug' => 'civil-construction-project', 'img' => 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&w=600&q=80'],
                    ['name' => 'Fire Safety', 'slug' => 'fire-safety', 'img' => 'https://ipdpbd.com/wp-content/uploads/2025/06/Fire-Safety.jpg'],
                    ['name' => 'Law & Compliance', 'slug' => 'law', 'img' => 'https://ipdpbd.com/wp-content/uploads/2025/06/Law.jpg'],
                    ['name' => 'Leadership', 'slug' => 'leadership', 'img' => 'https://ipdpbd.com/wp-content/uploads/2025/06/Leadership.jpg'],
                    ['name' => 'Production & Ops', 'slug' => 'production', 'img' => 'https://ipdpbd.com/wp-content/uploads/2025/06/47d4e0_329ee2938ea94e5d8a507a6547c9686dmv2.avif'],
                    ['name' => 'Project Management', 'slug' => 'project-management', 'img' => 'https://ipdpbd.com/wp-content/uploads/2025/06/Project-Management.jpg'],
                ];
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 sm:gap-6 text-center">
                @foreach($runningCategories as $cat)
                    <a href="{{ route('catalog.category', $cat['slug']) }}" class="group bg-white rounded-2xl p-5 shadow-[0_2px_12px_rgba(5,28,69,0.06)] hover:shadow-xl border border-slate-200/80 hover:border-[#c30102]/40 transition-all duration-300 flex flex-col items-center justify-center hover:-translate-y-1">
                        <!-- 80x80 Circular Image with red ring on hover -->
                        <div class="w-20 h-20 mx-auto rounded-full overflow-hidden mb-3 border-2 border-slate-100 group-hover:border-[#c30102] group-hover:scale-105 transition-all duration-300 shadow-sm bg-slate-50 flex items-center justify-center">
                            <img 
                                src="{{ $cat['img'] }}" 
                                alt="{{ $cat['name'] }}" 
                                class="w-full h-full object-cover object-center"
                                loading="lazy"
                                onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=300&q=80';"
                            >
                        </div>
                        <h3 class="text-xs sm:text-sm font-bold text-[#051c45] group-hover:text-[#c30102] transition-colors leading-snug line-clamp-2">
                            {{ $cat['name'] }}
                        </h3>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 4. Exact IPDP Dual-Column Section: Certificate Course & Day Long Course Connected by 'View All Courses' Bracket -->
    <section class="py-16 lg:py-20 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Connector Bracket & Centered 'View All Courses' -->
            <div class="relative mb-10 lg:mb-14">
                <div class="relative flex items-center justify-center">
                    <div class="hidden lg:block w-[36%] h-[2px] bg-gradient-to-r from-transparent via-[#051c45] to-[#c30102]"></div>
                    
                    <a href="{{ route('catalog.index') }}" class="px-7 py-3 bg-white text-2xl sm:text-3xl font-black text-[#051c45] hover:text-[#c30102] transition-colors whitespace-nowrap z-10 mx-2 flex items-center gap-2 group">
                        <span>View All Courses</span>
                        <i class="fas fa-arrow-right text-base text-[#c30102] group-hover:translate-x-1 transition-transform"></i>
                    </a>

                    <div class="hidden lg:block w-[36%] h-[2px] bg-gradient-to-r from-[#c30102] via-[#051c45] to-transparent"></div>
                </div>

                <!-- Left and Right vertical drops pointing into each container on LG screens -->
                <div class="hidden lg:grid grid-cols-2 gap-8 px-12 -mt-[2px]">
                    <div class="flex justify-center">
                        <div class="w-[2px] h-8 bg-[#051c45]/40"></div>
                    </div>
                    <div class="flex justify-center">
                        <div class="w-[2px] h-8 bg-[#c30102]/40"></div>
                    </div>
                </div>
            </div>

            <!-- Dual Side-By-Side Course Containers -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
                
                <!-- Left Container: Certificate Course (Light Green Box) -->
                <div class="bg-[#f0f8ec] border border-emerald-500/30 rounded-3xl p-6 sm:p-8 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <span class="px-3 py-1 rounded-full bg-emerald-600/10 text-emerald-800 text-[11px] font-black uppercase tracking-wider">Multi-Week Diploma</span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-black text-[#051c45] text-center mb-6 tracking-tight">
                            Certificate Course
                        </h2>

                        <!-- 2-Column Course Grid Inside Box -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            @foreach($certificateCourses->take(4) as $course)
                                <x-course-card :course="$course" />
                            @endforeach
                        </div>
                    </div>

                    <!-- Bottom Action Button -->
                    <div class="text-center mt-8">
                        <a href="{{ route('catalog.certificate') }}" class="btn-ipdp-secondary inline-flex items-center gap-2 px-8 py-3.5 rounded-xl text-sm font-bold shadow-md hover:shadow-lg transition-all">
                            <span>View All Certificate Courses</span>
                            <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Right Container: Day Long Course (Light Peach Box) -->
                <div class="bg-[#fef4f0] border border-[#c30102]/30 rounded-3xl p-6 sm:p-8 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <span class="px-3 py-1 rounded-full bg-[#c30102]/10 text-[#c30102] text-[11px] font-black uppercase tracking-wider">Intensive 1-Day Masterclass</span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-black text-[#051c45] text-center mb-6 tracking-tight">
                            Day Long Course
                        </h2>

                        <!-- 2-Column Course Grid Inside Box -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            @foreach($dayLongCourses->take(4) as $course)
                                <x-course-card :course="$course" />
                            @endforeach
                        </div>
                    </div>

                    <!-- Bottom Action Button -->
                    <div class="text-center mt-8">
                        <a href="{{ route('catalog.day_long') }}" class="btn-ipdp-primary inline-flex items-center gap-2 px-8 py-3.5 rounded-xl text-sm font-bold shadow-md hover:shadow-lg transition-all">
                            <span>View All Day Long Courses</span>
                            <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 4.5. Apple-Grade Executive Transformation Scrollytelling Showcase (Billion-Dollar Flagship Hybrid) -->
    <section class="py-16 sm:py-24 bg-gradient-to-b from-[#051c45] via-[#09214d] to-[#041433] text-white relative overflow-hidden border-b border-[#1e2d4a]" x-data="{ activeStep: 1 }">
        <!-- Ambient Radial Glows -->
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-[#c30102]/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-[#10b981]/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header & Scrollytelling Hook -->
            <div class="text-center max-w-3xl mx-auto mb-14">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-[#10b981] text-xs font-black uppercase tracking-wider mb-4 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-[#c30102] animate-ping"></span>
                    <span>The CAB Academy Standard</span>
                    <span class="text-white/40">&bull;</span>
                    <span class="text-gray-200">Apple-Grade Executive Journey</span>
                </div>
                
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-tight">
                    The Executive Transformation Journey
                </h2>
                
                <p class="text-gray-300 text-sm sm:text-base leading-relaxed mt-4 font-normal">
                    Traditional academic courses teach outdated theory. CAB Academy delivers direct operational mastery through four interactive execution phases designed by corporate directors and NBR practitioners.
                </p>
            </div>

            <!-- Interactive Scrollytelling Interface (Tabs + Dynamic Visual Stage) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                
                <!-- Left Column: 4 Progressive Milestones -->
                <div class="lg:col-span-5 space-y-3.5">
                    
                    <!-- Milestone 1 -->
                    <div 
                        @click="activeStep = 1" 
                        class="p-5 rounded-2xl cursor-pointer transition-all duration-300 border"
                        :class="activeStep === 1 ? 'bg-white/10 border-[#10b981] shadow-2xl backdrop-blur-md translate-x-2' : 'bg-white/5 border-white/10 hover:bg-white/[0.08] opacity-75 hover:opacity-100'"
                    >
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-black tracking-widest uppercase" :class="activeStep === 1 ? 'text-[#10b981]' : 'text-gray-400'">
                                Phase 01 &bull; Regulatory Intelligence
                            </span>
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold" :class="activeStep === 1 ? 'bg-[#10b981] text-white' : 'bg-white/10 text-gray-400'">
                                1
                            </span>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-white mb-1.5">NBR, VAT & Customs Tariff Architecture</h3>
                        <p class="text-xs sm:text-sm text-gray-300 leading-relaxed">
                            Master the Customs Act 2023, SRO classification, duty exemption strategies, and zero-penalty compliance frameworks directly from active NBR counsels.
                        </p>
                    </div>

                    <!-- Milestone 2 -->
                    <div 
                        @click="activeStep = 2" 
                        class="p-5 rounded-2xl cursor-pointer transition-all duration-300 border"
                        :class="activeStep === 2 ? 'bg-white/10 border-[#10b981] shadow-2xl backdrop-blur-md translate-x-2' : 'bg-white/5 border-white/10 hover:bg-white/[0.08] opacity-75 hover:opacity-100'"
                    >
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-black tracking-widest uppercase" :class="activeStep === 2 ? 'text-[#10b981]' : 'text-gray-400'">
                                Phase 02 &bull; Operational Execution
                            </span>
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold" :class="activeStep === 2 ? 'bg-[#10b981] text-white' : 'bg-white/10 text-gray-400'">
                                2
                            </span>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-white mb-1.5">Live Port & Documentation Simulations</h3>
                        <p class="text-xs sm:text-sm text-gray-300 leading-relaxed">
                            Hands-on creation and audit of Letter of Credit (L/C), Bill of Entry, Commercial Invoices, and bonded warehouse clearance documents with real customs data.
                        </p>
                    </div>

                    <!-- Milestone 3 -->
                    <div 
                        @click="activeStep = 3" 
                        class="p-5 rounded-2xl cursor-pointer transition-all duration-300 border"
                        :class="activeStep === 3 ? 'bg-white/10 border-[#10b981] shadow-2xl backdrop-blur-md translate-x-2' : 'bg-white/5 border-white/10 hover:bg-white/[0.08] opacity-75 hover:opacity-100'"
                    >
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-black tracking-widest uppercase" :class="activeStep === 3 ? 'text-[#10b981]' : 'text-gray-400'">
                                Phase 03 &bull; Risk Mitigation
                            </span>
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold" :class="activeStep === 3 ? 'bg-[#10b981] text-white' : 'bg-white/10 text-gray-400'">
                                3
                            </span>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-white mb-1.5">Trade Finance & Post-Clearance Audit Shield</h3>
                        <p class="text-xs sm:text-sm text-gray-300 leading-relaxed">
                            Mitigate foreign exchange exposure under Bangladesh Bank FE circulars, resolve discrepancies, and defend against post-clearance customs audits.
                        </p>
                    </div>

                    <!-- Milestone 4 -->
                    <div 
                        @click="activeStep = 4" 
                        class="p-5 rounded-2xl cursor-pointer transition-all duration-300 border"
                        :class="activeStep === 4 ? 'bg-white/10 border-[#10b981] shadow-2xl backdrop-blur-md translate-x-2' : 'bg-white/5 border-white/10 hover:bg-white/[0.08] opacity-75 hover:opacity-100'"
                    >
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-black tracking-widest uppercase" :class="activeStep === 4 ? 'text-[#10b981]' : 'text-gray-400'">
                                Phase 04 &bull; Executive Credential
                            </span>
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold" :class="activeStep === 4 ? 'bg-[#10b981] text-white' : 'bg-white/10 text-gray-400'">
                                4
                            </span>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-white mb-1.5">Verified Industrial Certification & Alumni Network</h3>
                        <p class="text-xs sm:text-sm text-gray-300 leading-relaxed">
                            Receive an official verified certificate recognized across top national enterprises, gaining lifetime access to peer forums and executive mastermind circles.
                        </p>
                    </div>

                </div>

                <!-- Right Column: Apple-Style Dynamic Visual Stage -->
                <div class="lg:col-span-7">
                    <div class="relative rounded-3xl bg-[#031535]/80 backdrop-blur-xl border border-white/15 p-6 sm:p-8 shadow-2xl overflow-hidden min-h-[440px] flex flex-col justify-between">
                        
                        <!-- Stage Ambient Glow -->
                        <div class="absolute top-0 right-0 w-80 h-80 bg-[#c30102]/10 rounded-full blur-2xl pointer-events-none"></div>

                        <!-- Stage Top Bar (Mac-Style Window Dots) -->
                        <div class="flex items-center justify-between pb-4 border-b border-white/10 mb-6">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-red-500/80 inline-block"></span>
                                <span class="w-3 h-3 rounded-full bg-yellow-500/80 inline-block"></span>
                                <span class="w-3 h-3 rounded-full bg-green-500/80 inline-block"></span>
                                <span class="text-xs text-gray-400 font-mono ml-2">CAB_SIMULATOR_CORE_v2.4</span>
                            </div>
                            <span class="px-2.5 py-1 rounded bg-[#c30102] text-[10px] font-black uppercase tracking-wider text-white">
                                Live Simulation Active
                            </span>
                        </div>

                        <!-- Dynamic Slide Content 1: Regulatory -->
                        <div x-show="activeStep === 1" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-[#10b981]/20 border border-[#10b981]/40 text-[#10b981] flex items-center justify-center text-2xl font-bold">
                                    <i class="fas fa-balance-scale"></i>
                                </div>
                                <div>
                                    <span class="text-[11px] text-[#10b981] font-black uppercase tracking-wider">NBR Compliance Architecture</span>
                                    <h4 class="text-lg font-black text-white">Customs Tariff & SRO Analysis Engine</h4>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3 pt-2">
                                <div class="p-3.5 rounded-xl bg-white/5 border border-white/10">
                                    <span class="text-[10px] text-gray-400 uppercase font-bold block mb-1">Standard Regulatory Error Rate</span>
                                    <span class="text-xl font-black text-red-400">32% Fines / Delays</span>
                                    <p class="text-[11px] text-gray-400 mt-1">Due to improper HS code & tariff filing</p>
                                </div>
                                <div class="p-3.5 rounded-xl bg-[#0a1f4a] border border-[#10b981]/40 shadow-inner">
                                    <span class="text-[10px] text-[#10b981] uppercase font-bold block mb-1">CAB Academy Trained Standard</span>
                                    <span class="text-xl font-black text-[#10b981]">0% Penalty Guarantee</span>
                                    <p class="text-[11px] text-gray-300 mt-1">Verified with Mushak 6.3 & SRO protocols</p>
                                </div>
                            </div>

                            <div class="p-4 rounded-xl bg-black/30 border border-white/10 font-mono text-xs text-gray-300 space-y-1.5">
                                <div class="flex items-center justify-between text-[#10b981]">
                                    <span>&gt; SRO_VERIFICATION: PASS</span>
                                    <span class="text-[10px] bg-[#10b981]/20 text-[#10b981] px-2 py-0.5 rounded">AUTHENTICATED</span>
                                </div>
                                <div class="text-gray-400">&gt; HS CODE: 8471.30.00 &bull; Duty Exemption SRO No. 142-AIN/2023/Customs</div>
                                <div class="text-gray-400">&gt; VAT Rebate Form Mushak 6.3: Generated & Validated</div>
                            </div>
                        </div>

                        <!-- Dynamic Slide Content 2: Live Operations -->
                        <div x-show="activeStep === 2" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4" style="display: none;">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-[#c30102]/20 border border-[#c30102]/40 text-[#c30102] flex items-center justify-center text-2xl font-bold">
                                    <i class="fas fa-ship"></i>
                                </div>
                                <div>
                                    <span class="text-[11px] text-[#c30102] font-black uppercase tracking-wider">Port & Customs Operations</span>
                                    <h4 class="text-lg font-black text-white">Live L/C & Bill of Entry Clearance</h4>
                                </div>
                            </div>

                            <div class="p-4 rounded-xl bg-white/5 border border-white/10 space-y-2.5">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold text-white">Chittagong Port Customs Simulation:</span>
                                    <span class="text-[#10b981] font-mono font-bold">CLEARANCE APPROVED</span>
                                </div>
                                <div class="w-full bg-white/10 rounded-full h-2 overflow-hidden">
                                    <div class="bg-[#10b981] h-2 rounded-full w-[95%]"></div>
                                </div>
                                <div class="grid grid-cols-3 gap-2 text-[11px] text-gray-300 pt-1">
                                    <div>&bull; Bill of Lading (B/L): OK</div>
                                    <div>&bull; Commercial Inv: OK</div>
                                    <div>&bull; Certificate of Origin: OK</div>
                                </div>
                            </div>

                            <div class="p-3.5 rounded-xl bg-black/30 border border-white/10 text-xs text-gray-300 flex items-center justify-between">
                                <span>Real-world document bundle provided for every student</span>
                                <span class="text-[#10b981] font-bold"><i class="fas fa-check-circle"></i> 100% Practical</span>
                            </div>
                        </div>

                        <!-- Dynamic Slide Content 3: Risk Mitigation -->
                        <div x-show="activeStep === 3" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4" style="display: none;">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-blue-500/20 border border-blue-500/40 text-blue-400 flex items-center justify-center text-2xl font-bold">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div>
                                    <span class="text-[11px] text-blue-400 font-black uppercase tracking-wider">Audit Defense Shield</span>
                                    <h4 class="text-lg font-black text-white">Foreign Exchange & Post-Clearance Defense</h4>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="p-3.5 rounded-xl bg-white/5 border border-white/10 space-y-1">
                                    <span class="text-[10px] text-gray-400 uppercase font-bold">Bangladesh Bank Circulars</span>
                                    <p class="text-xs text-white font-bold">FE Circular Compliance & Hedging</p>
                                    <p class="text-[11px] text-gray-300">Prevent export proceeds delay and currency loss</p>
                                </div>
                                <div class="p-3.5 rounded-xl bg-white/5 border border-white/10 space-y-1">
                                    <span class="text-[10px] text-gray-400 uppercase font-bold">Post-Clearance Audit (PCA)</span>
                                    <p class="text-xs text-white font-bold">Customs Audit Shield</p>
                                    <p class="text-[11px] text-gray-300">Protect corporate accounts against retroactive duties</p>
                                </div>
                            </div>
                        </div>

                        <!-- Dynamic Slide Content 4: Credential & Alumni -->
                        <div x-show="activeStep === 4" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4" style="display: none;">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-[#c30102]/20 border border-[#c30102]/40 text-[#c30102] flex items-center justify-center text-2xl font-bold">
                                    <i class="fas fa-award"></i>
                                </div>
                                <div>
                                    <span class="text-[11px] text-[#c30102] font-black uppercase tracking-wider">Institutional Credential</span>
                                    <h4 class="text-lg font-black text-white">Verified Certificate Recognized by Industry</h4>
                                </div>
                            </div>

                            <div class="p-4 rounded-2xl bg-gradient-to-r from-white/10 to-white/5 border border-white/20 text-center space-y-2">
                                <span class="text-[10px] text-[#10b981] font-black uppercase tracking-widest">CAB ACADEMY CERTIFICATE OF EXECUTIVE MASTERY</span>
                                <h5 class="text-base font-bold text-white">Issued to Industry Professionals & Executives</h5>
                                <p class="text-xs text-gray-300">Verified by unique cryptographic QR code recognized by top corporate recruiters across Bangladesh.</p>
                            </div>
                        </div>

                        <!-- Stage Footer Conversion Bar -->
                        <div class="pt-6 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4 mt-6">
                            <div class="flex items-center gap-2 text-xs text-gray-300">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#c30102] animate-pulse"></span>
                                <span><strong>Batch 105th Enrolling:</strong> 19 of 25 Seats Reserved</span>
                            </div>

                            <div class="flex items-center gap-3 w-full sm:w-auto">
                                @if(isset($certificateCourses) && $certificateCourses->first())
                                    @php $flagship = $certificateCourses->first(); @endphp
                                    <button 
                                        type="button"
                                        @click="$dispatch('open-enroll-drawer', { 
                                            id: {{ $flagship->id }}, 
                                            title: '{{ addslashes($flagship->title) }}', 
                                            price: '{{ number_format($flagship->price, 2) }}', 
                                            rawPrice: {{ $flagship->price }}, 
                                            batch: '{{ $flagship->batch ?? '105th BATCH' }}', 
                                            venue: '{{ addslashes($flagship->venue ?? 'Mirpur-Pallabi Campus & Online Zoom') }}' 
                                        })"
                                        class="px-5 py-2.5 rounded-xl bg-[#c30102] hover:bg-[#a50102] text-white text-xs font-black uppercase tracking-wider shadow-lg flex items-center justify-center gap-2 transition-all hover:scale-105 w-full sm:w-auto"
                                    >
                                        <i class="fas fa-bolt text-xs"></i>
                                        <span>Express Enroll (30 Sec)</span>
                                    </button>
                                @endif

                                <a 
                                    href="https://wa.me/8801917054664?text={{ urlencode('Hello CAB Academy, I want to inquire about upcoming executive batches and admission guidance.') }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="px-4 py-2.5 rounded-xl bg-[#25D366] hover:bg-[#20ba5a] text-white text-xs font-bold shadow flex items-center justify-center gap-1.5 transition-all hover:scale-105 shrink-0"
                                >
                                    <i class="fab fa-whatsapp text-sm"></i>
                                    <span>WhatsApp</span>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 4.8. Executive Comparison Matrix (Apple-Style Architectural Clarity) -->
    <section class="py-16 sm:py-20 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-14">
                <span class="text-xs uppercase tracking-widest font-extrabold text-[#c30102] block mb-1">Architectural Superiority</span>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-[#051c45] tracking-tight">
                    Why Corporate Leaders Choose CAB Academy
                </h2>
                <p class="text-slate-500 text-sm sm:text-base mt-2">
                    A rigorous comparison between conventional academic diplomas and CAB Academy practical trade laboratories.
                </p>
            </div>

            <!-- Modern Comparison Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                <!-- Left: Conventional Centers -->
                <div class="p-8 rounded-3xl bg-slate-50 border border-slate-200/90 shadow-sm space-y-6">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-200">
                        <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center text-lg font-bold">
                            <i class="fas fa-times"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-slate-800">Conventional Training Centers</h3>
                            <span class="text-xs text-slate-500">Traditional textbook methodology</span>
                        </div>
                    </div>
                    <ul class="space-y-4 text-xs sm:text-sm text-slate-600">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-times text-red-400 mt-1 shrink-0"></i>
                            <span>Rote theoretical lectures without authentic Customs Bill of Entry or L/C files</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-times text-red-400 mt-1 shrink-0"></i>
                            <span>Instructors lacking active NBR or customs clearance operational experience</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-times text-red-400 mt-1 shrink-0"></i>
                            <span>Paper-only non-verifiable attendance certificates vulnerable to fraud</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-times text-red-400 mt-1 shrink-0"></i>
                            <span>Zero post-training regulatory advisory or SRO update briefings</span>
                        </li>
                    </ul>
                </div>

                <!-- Right: CAB Academy Advantage -->
                <div class="p-8 rounded-3xl bg-gradient-to-br from-[#051c45] to-[#0a2540] text-white border-2 border-[#c30102]/40 shadow-2xl space-y-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-[#c30102]/20 rounded-full blur-2xl pointer-events-none"></div>
                    <div class="flex items-center justify-between pb-4 border-b border-white/15 relative z-10">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-400/40 flex items-center justify-center text-lg font-bold">
                                <i class="fas fa-check"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-white">The CAB Academy Standard</h3>
                                <span class="text-xs text-emerald-300 font-bold">Executive Frontline Mastery</span>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded bg-[#c30102] text-[10px] font-black uppercase tracking-wider text-white">Verified Advantage</span>
                    </div>
                    <ul class="space-y-4 text-xs sm:text-sm text-slate-200 relative z-10">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check text-emerald-400 mt-1 shrink-0"></i>
                            <span>Live hands-on audit of actual L/C, Mushak 6.3, and Chittagong Port documentation</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check text-emerald-400 mt-1 shrink-0"></i>
                            <span>Mentorship directly from active NBR legal advocates and C-suite supply chain directors</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check text-emerald-400 mt-1 shrink-0"></i>
                            <span>Immutable SHA-256 cryptographic QR credentials instantly verifiable by employers worldwide</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check text-emerald-400 mt-1 shrink-0"></i>
                            <span>Lifetime access to regulatory circular alerts and private executive mastermind forums</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- 4.9. Executive Alumni Testimonials & Social Proof -->
    <section class="py-16 sm:py-20 bg-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-14">
                <span class="text-xs uppercase tracking-widest font-extrabold text-[#c30102] block mb-1">Proven Executive Impact</span>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-[#051c45] tracking-tight">
                    Trusted by Bangladesh's Foremost Commercial Leaders
                </h2>
                <p class="text-slate-500 text-sm sm:text-base mt-2">
                    Hear directly from alumni who advanced to senior commercial, customs, and logistics leadership roles.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="bg-white p-7 rounded-3xl border border-slate-200/80 shadow-[0_4px_16px_rgba(5,28,69,0.05)] hover:shadow-xl transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-1 text-amber-400 text-sm mb-4">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                        <p class="text-slate-700 text-xs sm:text-sm leading-relaxed mb-6 font-medium italic">
                            "The Export-Import masterclass at CAB Academy transformed our factory's commercial clearance time. The L/C discrepancy checklist and SRO classification strategies saved us substantial customs demurrage."
                        </p>
                    </div>
                    <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                        <div class="w-11 h-11 rounded-full bg-[#051c45] text-white flex items-center justify-center font-bold text-base shadow">
                            TA
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-[#051c45]">Tanvir Ahmed</h4>
                            <p class="text-[11px] text-slate-500 font-semibold">Senior Commercial Manager, Square Group</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="bg-white p-7 rounded-3xl border border-slate-200/80 shadow-[0_4px_16px_rgba(5,28,69,0.05)] hover:shadow-xl transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-1 text-amber-400 text-sm mb-4">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                        <p class="text-slate-700 text-xs sm:text-sm leading-relaxed mb-6 font-medium italic">
                            "Direct guidance from active NBR counsels on VAT rebate Form Mushak 6.3 gave our financial team 100% audit defense certainty. Outstanding practical rigor not found in any university."
                        </p>
                    </div>
                    <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                        <div class="w-11 h-11 rounded-full bg-[#c30102] text-white flex items-center justify-center font-bold text-base shadow">
                            NH
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-[#051c45]">Nazrul Hassan, FCA</h4>
                            <p class="text-[11px] text-slate-500 font-semibold">Head of Corporate Tax & VAT, Leading RMG Group</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="bg-white p-7 rounded-3xl border border-slate-200/80 shadow-[0_4px_16px_rgba(5,28,69,0.05)] hover:shadow-xl transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-1 text-amber-400 text-sm mb-4">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                        <p class="text-slate-700 text-xs sm:text-sm leading-relaxed mb-6 font-medium italic">
                            "The digital verifiable certificate with QR registry gave immediate confidence to our European buyers during a supply chain compliance audit. CAB Academy is the benchmark for trade education."
                        </p>
                    </div>
                    <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                        <div class="w-11 h-11 rounded-full bg-emerald-700 text-white flex items-center justify-center font-bold text-base shadow">
                            SR
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-[#051c45]">Shahriar Rahman</h4>
                            <p class="text-[11px] text-slate-500 font-semibold">Director of Logistics, SSG Global Ventures</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. Executive Insights & Knowledge Hub (Blog Section) -->
    <section class="py-16 sm:py-20 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-12 pb-4 border-b border-slate-200">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#c30102]/10 text-[#c30102] text-xs font-black uppercase tracking-wider mb-2">
                        <i class="fas fa-newspaper"></i>
                        <span>Executive Insights & Research</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-black text-[#051c45] tracking-tight">
                        Knowledge Hub & Publications
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 font-medium">
                        Thought leadership, customs directives, tax reforms, and supply chain insights by CAB Academy Distinguished Faculty.
                    </p>
                </div>
                <a href="{{ route('blog.index') }}" class="mt-4 sm:mt-0 text-sm font-bold text-[#c30102] hover:text-[#051c45] transition-colors inline-flex items-center gap-1.5 group">
                    <span>View All Publications</span>
                    <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <!-- 3-Column Blog Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($recentBlogs as $blog)
                    <article class="group bg-white rounded-3xl shadow-[0_2px_12px_rgba(5,28,69,0.06)] hover:shadow-xl border border-slate-200/80 overflow-hidden transition-all duration-300 flex flex-col h-full hover:-translate-y-1">
                        <!-- Cover Image -->
                        <div class="relative aspect-video w-full overflow-hidden bg-slate-100">
                            <a href="{{ route('blog.show', $blog->slug) }}">
                                @if($blog->cover_image)
                                    <img 
                                       src="{{ asset($blog->cover_image) }}" 
                                       alt="{{ $blog->title }}" 
                                       class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                       loading="lazy"
                                    >
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-[#051c45] to-[#031330] flex items-center justify-center text-white">
                                        <i class="fas fa-file-alt text-4xl text-white/40"></i>
                                    </div>
                                @endif
                            </a>
                            <div class="absolute top-3 left-3">
                                <span class="inline-block px-3 py-1 bg-[#c30102] text-white rounded-lg text-[10px] font-black uppercase tracking-wider shadow">
                                    {{ $blog->category ?? 'Insights' }}
                                </span>
                            </div>
                        </div>

                        <!-- Article Content -->
                        <div class="p-6 flex-1 flex flex-col justify-between">
                            <div>
                                <!-- Meta info: Date & Reading Time -->
                                <div class="flex items-center gap-2 text-xs text-slate-500 font-medium mb-2.5">
                                    <i class="far fa-calendar-alt text-[#c30102]"></i>
                                    <span>{{ $blog->published_at ? $blog->published_at->format('M d, Y') : date('M d, Y') }}</span>
                                    <span>&bull;</span>
                                    <i class="far fa-clock text-emerald-600"></i>
                                    <span>{{ $blog->reading_time ?? '5 min' }} read</span>
                                </div>

                                <!-- Title -->
                                <h3 class="text-base sm:text-lg font-bold text-[#051c45] group-hover:text-[#c30102] transition-colors leading-snug mb-2.5 line-clamp-2">
                                    <a href="{{ route('blog.show', $blog->slug) }}">
                                        {{ $blog->title }}
                                    </a>
                                </h3>

                                <!-- Excerpt -->
                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed line-clamp-3 mb-4">
                                    {{ $blog->excerpt }}
                                </p>
                            </div>

                            <!-- Author & Action Footer -->
                            <div class="border-t border-slate-100 pt-4 mt-auto flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center text-xs font-bold text-[#051c45]">
                                        @if($blog->author && $blog->author->profile_picture)
                                            <img src="{{ asset($blog->author->profile_picture) }}" alt="{{ $blog->author_name }}" class="w-full h-full object-cover">
                                        @else
                                            {{ substr($blog->author_name ?? 'F', 0, 1) }}
                                        @endif
                                    </div>
                                    <span class="text-xs font-bold text-[#051c45] truncate max-w-[130px]">
                                        {{ $blog->author_name ?? 'Distinguished Faculty' }}
                                    </span>
                                </div>

                                <a href="{{ route('blog.show', $blog->slug) }}" class="text-xs font-bold text-[#c30102] group-hover:text-[#051c45] flex items-center gap-1 transition-colors">
                                    <span>Read Brief</span>
                                    <i class="fas fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 6. Exact IPDP "Need To Learn More ?" CTA Section (Upgraded Emerald/Navy Canvas) -->
    <section class="py-16 bg-gradient-to-r from-[#059669] via-[#10b981] to-[#047857] text-white shadow-inner">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl sm:text-4xl font-black mb-3 text-white tracking-tight">
                Need To Learn More ?
            </h2>
            <p class="text-sm sm:text-base text-emerald-50 max-w-2xl mx-auto mb-8 font-normal leading-relaxed">
                Connect with our academic counselors for corporate training customized for your organization, batch reservations, or curriculum guidance.
            </p>
            <div class="flex flex-wrap justify-center items-center gap-4">
                <a href="tel:01917054664" class="btn-ipdp-primary px-8 py-4 rounded-xl font-black text-sm shadow-xl flex items-center gap-2 hover:scale-105 transition-all">
                    <i class="fas fa-phone-alt"></i>
                    <span>Call: 01917-054664</span>
                </a>
                <a href="{{ route('catalog.category', 'export-import') }}" class="bg-white text-[#051c45] hover:bg-[#031330] hover:text-white px-8 py-4 rounded-xl font-bold text-sm shadow-xl transition-all duration-300 flex items-center gap-2 hover:scale-105">
                    <span>Explore Export – Import</span>
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- 7. IPDP "Our Features" Section (Distinguished Faculty & Fellows Terminology) -->
    <section id="features" class="py-16 sm:py-20 bg-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <span class="text-xs uppercase tracking-widest font-extrabold text-[#c30102] block mb-1">Institutional Excellence</span>
                <h2 class="text-3xl font-black text-[#051c45] tracking-tight">
                    Our Features
                </h2>
                <div class="w-16 h-1 bg-[#c30102] mx-auto mt-3 rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @php
                    $features = [
                        [
                            'icon' => 'fa-graduation-cap',
                            'title' => 'Industry Level Curriculum',
                            'desc' => 'Designed alongside top corporate practitioners and customs specialists to fulfill active corporate requirements.'
                        ],
                        [
                            'icon' => 'fa-chalkboard-teacher',
                            'title' => 'Distinguished Faculty & Fellows',
                            'desc' => 'Direct executive mentorship guided by high-profile corporate leaders, senior chartered accountants, NBR tax advocates, and logistics directors.'
                        ],
                        [
                            'icon' => 'fa-laptop-house',
                            'title' => 'Flexible Learning Options',
                            'desc' => 'Interactive physical classroom sessions in Mirpur-Pallabi campus as well as remote live video stream options.'
                        ],
                        [
                            'icon' => 'fa-certificate',
                            'title' => 'Recognized Certifications',
                            'desc' => 'Credential verified certifications recognized by leading industrial groups across Bangladesh.'
                        ],
                        [
                            'icon' => 'fa-headset',
                            'title' => 'Support Services',
                            'desc' => 'Post-training support, consultation, documentation guidance, and active peer professional networking.'
                        ],
                        [
                            'icon' => 'fa-layer-group',
                            'title' => 'Wide Range of Courses',
                            'desc' => 'Over 14 professional sectors covering Import-Export, SCM, VAT, HR, Civil Engineering, and Corporate Tax.'
                        ],
                    ];
                @endphp

                @foreach($features as $feat)
                    <div class="bg-white p-7 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-xl hover:border-[#c30102]/30 transition-all duration-300 group hover:-translate-y-1">
                        <div class="w-14 h-14 rounded-2xl bg-[#051c45]/5 text-[#051c45] group-hover:bg-[#c30102] group-hover:text-white transition-colors duration-300 flex items-center justify-center text-2xl mb-5 shadow-sm">
                            <i class="fas {{ $feat['icon'] }}"></i>
                        </div>
                        <h4 class="text-lg font-black text-[#051c45] mb-2 group-hover:text-[#c30102] transition-colors">
                            {{ $feat['title'] }}
                        </h4>
                        <p class="text-xs sm:text-sm text-slate-500 leading-relaxed font-normal">
                            {{ $feat['desc'] }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 8. Student Career & Job Placement Section (Preserving Platform Job Board Feature) -->
    @if(isset($jobs) && $jobs->count() > 0)
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10 pb-4 border-b border-slate-200">
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-black text-[#051c45] tracking-tight">
                            Career & Placement Opportunities
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1 font-medium">
                            Explore openings posted exclusively for CAB Academy certified professionals
                        </p>
                    </div>
                    <a href="{{ route('jobs.index') }}" class="mt-4 sm:mt-0 text-sm font-bold text-[#c30102] hover:text-[#051c45] transition-colors inline-flex items-center gap-1 group">
                        <span>Browse All Openings</span>
                        <i class="fas fa-chevron-right text-xs group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($jobs as $job)
                        <div class="bg-slate-50 p-6 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-lg transition-all flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-[#c30102]/10 text-[#c30102]">
                                        {{ $job->job_type ?? 'Full-time' }}
                                    </span>
                                    <span class="text-xs text-slate-500">
                                        {{ $job->location ?? 'Dhaka' }}
                                    </span>
                                </div>
                                <h4 class="text-base font-bold text-[#051c45] mb-1">
                                    {{ $job->title }}
                                </h4>
                                <p class="text-xs text-slate-500 font-semibold mb-3">
                                    {{ $job->company_name ?? 'Partner Enterprise' }}
                                </p>
                                <p class="text-xs text-slate-600 line-clamp-2 mb-4">
                                    {{ $job->description }}
                                </p>
                            </div>
                            <a href="{{ route('jobs.index') }}" class="btn-ipdp-primary text-center py-2.5 rounded-xl text-xs font-bold w-full shadow-md">
                                View Details & Apply
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

</div>
@endsection
