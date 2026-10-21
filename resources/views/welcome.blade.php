@extends('layouts.public')

@section('title', 'Home')

@section('content')
<div>

    <!-- 1. Executive Hero Section (Billion-Dollar Apple/Stripe Grade Canvas) -->
    <section class="relative min-h-[580px] lg:min-h-[640px] flex items-center bg-gradient-to-br from-[#020b18] via-[#041536] via-[#072152] to-[#092b68] text-white py-16 sm:py-24 overflow-hidden border-b border-slate-800/80">
        <!-- Multi-Stop Ambient Atmospheric Mesh Glows -->
        <div class="absolute -top-32 -left-32 w-[30rem] h-[30rem] bg-gradient-to-tr from-[#c30102]/25 to-[#e11d48]/10 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-[30rem] h-[30rem] bg-gradient-to-tl from-[#10b981]/20 to-[#047857]/10 rounded-full blur-[130px] pointer-events-none"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[42rem] h-[32rem] bg-[#0a3275]/25 rounded-full blur-[140px] pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
                
                <!-- Left Content: CAB Academy Executive Branding -->
                <div class="lg:col-span-7 space-y-6">
                    <!-- Executive Institutional Micro-Badge -->
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white/95 text-xs font-semibold shadow-inner">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Executive Professional Certification & Corporate Programs</span>
                    </div>

                    <div class="space-y-2">
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight text-white uppercase drop-shadow-[0_4px_16px_rgba(0,0,0,0.6)]">
                            CAB Academy
                        </h1>
                        <h3 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-amber-200 via-emerald-300 to-teal-100 tracking-wide drop-shadow-sm">
                            Learn, Grow, Lead.
                        </h3>
                    </div>
                    
                    <p class="text-base sm:text-lg text-slate-200/90 font-normal max-w-xl leading-relaxed">
                        Online Professional Certification Masterclasses & Corporate Programs.
                    </p>

                    <!-- Call-to-Action Buttons -->
                    <div class="pt-2 flex flex-wrap items-center gap-4">
                        <a href="#home-category" class="btn-ipdp-primary px-8 py-4 rounded-xl text-white text-sm font-black uppercase tracking-wider shadow-[0_10px_25px_-5px_rgba(195,1,2,0.45)] flex items-center gap-2 transition-all hover:scale-105 active:scale-95">
                            <span>Explore Courses</span>
                            <i class="fas fa-arrow-down text-xs"></i>
                        </a>

                        <a href="{{ route('catalog.category', 'export-import') }}" class="px-8 py-4 rounded-xl bg-white/10 hover:bg-white/20 backdrop-blur-md text-white border border-white/25 text-sm font-bold uppercase tracking-wider shadow-lg flex items-center gap-2 hover:scale-105 active:scale-95 transition-all">
                            <span>Export – Import</span>
                            <i class="fas fa-chevron-right text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Right Video / Visual Showcase (Apple / MasterClass Luxury Acrylic Bezel) -->
                <div class="lg:col-span-5">
                    <div class="relative p-2 rounded-3xl bg-gradient-to-br from-white/25 via-white/10 to-white/5 backdrop-blur-2xl shadow-[0_30px_70px_-15px_rgba(0,0,0,0.8),0_0_0_1px_rgba(255,255,255,0.2)]">
                        <div class="relative rounded-2xl overflow-hidden aspect-video sm:aspect-[4/3] flex items-center justify-center bg-slate-900 group">
                            <img 
                                src="https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=1000&q=80" 
                                alt="CAB Academy Executive Training" 
                                class="absolute inset-0 w-full h-full object-cover opacity-75 group-hover:scale-105 transition-transform duration-700"
                                onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=1000&q=80';"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-[#020b18] via-black/35 to-transparent"></div>
                            
                            <!-- Glowing Concentric Play Button -->
                            <a 
                                href="{{ route('catalog.index') }}" 
                                class="relative z-10 w-20 h-20 rounded-full bg-gradient-to-tr from-[#c30102] via-[#e11d48] to-[#ff4d6d] text-white flex items-center justify-center shadow-[0_0_40px_rgba(195,1,2,0.7)] hover:scale-110 hover:shadow-[0_0_55px_rgba(195,1,2,0.95)] transition-all duration-300"
                                aria-label="Explore Courses"
                            >
                                <span class="absolute inset-0 rounded-full bg-[#c30102] animate-ping opacity-25"></span>
                                <i class="fas fa-play text-2xl ml-1 text-white relative z-10"></i>
                            </a>

                            <!-- Floating Micro-Capsule Inside Video Preview -->
                            <div class="absolute bottom-3 left-3 right-3 bg-[#020b18]/85 backdrop-blur-md border border-white/15 px-3.5 py-2 rounded-xl flex items-center justify-between text-xs text-slate-200">
                                <span class="flex items-center gap-2 font-semibold">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                    <span>Interactive Masterclasses</span>
                                </span>
                                <span class="text-[10px] text-slate-300 uppercase tracking-wider font-bold">Zoom Pro Live</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 2. "Our Valuable Clients" (Grid of Corporate Partners) -->
    <section class="py-14 bg-gradient-to-b from-slate-50/80 via-white to-slate-50/50 border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <span class="bg-[#c30102]/10 text-[#c30102] px-3.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider mb-2 inline-block">Corporate Trust & Affiliation</span>
                <h2 class="text-xl sm:text-2xl font-black text-[#041536] tracking-tight uppercase">
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
                    <div class="p-3 bg-white hover:bg-white rounded-2xl border border-slate-200/80 hover:border-slate-300 shadow-[0_2px_8px_rgba(4,21,54,0.04)] hover:shadow-[0_12px_24px_-6px_rgba(4,21,54,0.1)] hover:border-[#041536]/20 transition-all duration-300 group flex items-center justify-center h-20">
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

    <!-- 3. Exact IPDP "Running Course Categories" Section (Modernized Ceramic Cards with Ring Glow) -->
    <section id="home-category" class="py-16 bg-slate-50/60 border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-[#c30102] bg-[#c30102]/10 px-3.5 py-1 rounded-full text-xs uppercase tracking-widest font-extrabold inline-block mb-2">Executive Disciplines</span>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-[#041536] tracking-tight">
                    Running Course Categories
                </h2>
                <div class="w-16 h-1 bg-gradient-to-r from-[#c30102] via-[#e11d48] to-[#041536] mx-auto mt-3 rounded-full"></div>
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
                    <a href="{{ route('catalog.category', $cat['slug']) }}" class="group bg-white rounded-2xl p-5 shadow-[0_2px_12px_rgba(4,21,54,0.05)] hover:shadow-[0_16px_36px_-6px_rgba(4,21,54,0.15)] border border-slate-200/90 hover:border-[#c30102]/40 transition-all duration-300 flex flex-col items-center justify-center hover:-translate-y-1.5">
                        <!-- 80x80 Circular Image with double ring glow on hover (Cypress selector #home-category .w-20.h-20.rounded-full) -->
                        <div class="w-20 h-20 mx-auto rounded-full overflow-hidden mb-3.5 ring-2 ring-slate-100 group-hover:ring-[#c30102] group-hover:scale-105 transition-all duration-300 shadow-md bg-slate-100 flex items-center justify-center">
                            <img 
                                src="{{ $cat['img'] }}" 
                                alt="{{ $cat['name'] }}" 
                                class="w-full h-full object-cover object-center"
                                loading="lazy"
                                onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=300&q=80';"
                            >
                        </div>
                        <h3 class="text-xs sm:text-sm font-bold text-[#041536] group-hover:text-[#c30102] transition-colors leading-snug line-clamp-2">
                            {{ $cat['name'] }}
                        </h3>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 4. Exact IPDP Dual-Column Section: Certificate Course & Day Long Course Connected by 'View All Courses' Bracket -->
    <section class="py-16 lg:py-20 bg-white border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Connector Bracket & Centered 'View All Courses' -->
            <div class="relative mb-10 lg:mb-14">
                <div class="relative flex items-center justify-center">
                    <div class="hidden lg:block w-[36%] h-[2px] bg-gradient-to-r from-transparent via-[#041536] to-[#c30102]"></div>
                    
                    <a href="{{ route('catalog.index') }}" class="px-8 py-3.5 bg-white rounded-2xl shadow-[0_4px_20px_rgba(4,21,54,0.08)] border border-slate-200 hover:border-[#c30102]/40 text-xl sm:text-2xl font-black text-[#041536] hover:text-[#c30102] transition-all whitespace-nowrap z-10 mx-2 flex items-center gap-2.5 group hover:scale-[1.02]">
                        <span>View All Courses</span>
                        <i class="fas fa-arrow-right text-base text-[#c30102] group-hover:translate-x-1.5 transition-transform"></i>
                    </a>

                    <div class="hidden lg:block w-[36%] h-[2px] bg-gradient-to-r from-[#c30102] via-[#041536] to-transparent"></div>
                </div>

                <!-- Left and Right vertical drops pointing into each container on LG screens -->
                <div class="hidden lg:grid grid-cols-2 gap-8 px-12 -mt-[2px]">
                    <div class="flex justify-center">
                        <div class="w-[2px] h-8 bg-[#041536]/40"></div>
                    </div>
                    <div class="flex justify-center">
                        <div class="w-[2px] h-8 bg-[#c30102]/40"></div>
                    </div>
                </div>
            </div>

            <!-- Dual Side-By-Side Course Containers -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
                
                <!-- Left Container: Certificate Course (Light Green Box with Subtle Radiant Depth) -->
                <div class="bg-[#f0f8ec] relative rounded-3xl p-6 sm:p-8 border-2 border-emerald-500/30 shadow-[0_4px_25px_-5px_rgba(16,185,129,0.14)] overflow-hidden flex flex-col justify-between">
                    <div class="absolute -top-24 -right-24 w-72 h-72 bg-emerald-400/12 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="relative z-10">
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <span class="px-3.5 py-1 rounded-full bg-emerald-700/12 border border-emerald-600/25 text-emerald-800 text-[11px] font-black uppercase tracking-wider shadow-sm">Multi-Week Diploma</span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-black text-[#041536] text-center mb-6 tracking-tight">
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
                    <div class="text-center mt-8 relative z-10">
                        <a href="{{ route('catalog.certificate') }}" class="btn-ipdp-secondary inline-flex items-center gap-2 px-8 py-3.5 rounded-xl text-sm font-bold shadow-md hover:shadow-xl hover:scale-105 transition-all">
                            <span>View All Certificate Courses</span>
                            <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Right Container: Day Long Course (Light Peach Box with Subtle Warm Glow) -->
                <div class="bg-[#fef4f0] relative rounded-3xl p-6 sm:p-8 border-2 border-[#c30102]/25 shadow-[0_4px_25px_-5px_rgba(195,1,2,0.12)] overflow-hidden flex flex-col justify-between">
                    <div class="absolute -top-24 -right-24 w-72 h-72 bg-[#c30102]/10 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="relative z-10">
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <span class="px-3.5 py-1 rounded-full bg-[#c30102]/12 border border-[#c30102]/25 text-[#c30102] text-[11px] font-black uppercase tracking-wider shadow-sm">Intensive 1-Day Masterclass</span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-black text-[#041536] text-center mb-6 tracking-tight">
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
                    <div class="text-center mt-8 relative z-10">
                        <a href="{{ route('catalog.day_long') }}" class="btn-ipdp-primary inline-flex items-center gap-2 px-8 py-3.5 rounded-xl text-sm font-bold shadow-md hover:shadow-xl hover:scale-105 transition-all">
                            <span>View All Day Long Courses</span>
                            <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 5. Executive Insights & Knowledge Hub (Blog Section) -->
    <section class="py-16 sm:py-20 bg-white border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-12 pb-4 border-b border-slate-200/80">
                <div>
                    <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-[#c30102]/10 text-[#c30102] text-xs font-black uppercase tracking-wider mb-2">
                        <i class="fas fa-newspaper"></i>
                        <span>Executive Insights & Research</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-black text-[#041536] tracking-tight">
                        Knowledge Hub & Publications
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 font-medium">
                        Thought leadership, customs directives, tax reforms, and supply chain insights by CAB Academy Distinguished Faculty.
                    </p>
                </div>
                <a href="{{ route('blog.index') }}" class="mt-4 sm:mt-0 text-sm font-bold text-[#c30102] hover:text-[#041536] transition-colors inline-flex items-center gap-1.5 group">
                    <span>View All Publications</span>
                    <i class="fas fa-arrow-right text-xs group-hover:translate-x-1.5 transition-transform"></i>
                </a>
            </div>

            <!-- 3-Column Blog Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @php
                    $defaultBlogImgs = [
                        'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=800&q=80',
                    ];
                @endphp
                @foreach($recentBlogs as $blog)
                    <article class="group bg-white rounded-3xl shadow-[0_4px_16px_rgba(4,21,54,0.06)] hover:shadow-[0_20px_45px_-10px_rgba(4,21,54,0.16)] border border-slate-200/90 hover:border-[#c30102]/30 overflow-hidden transition-all duration-300 flex flex-col h-full hover:-translate-y-1.5">
                        <!-- Cover Image -->
                        <div class="relative aspect-video w-full overflow-hidden bg-slate-900">
                            <a href="{{ route('blog.show', $blog->slug) }}">
                                @php
                                    $coverUrl = $blog->cover_image ? asset($blog->cover_image) : ($defaultBlogImgs[$loop->index % 3]);
                                @endphp
                                <img 
                                   src="{{ $coverUrl }}" 
                                   alt="{{ $blog->title }}" 
                                   class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-95 group-hover:opacity-100"
                                   loading="lazy"
                                   onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=800&q=80';"
                                >
                            </a>
                            <div class="absolute top-3 left-3">
                                <span class="inline-block px-3 py-1 bg-gradient-to-r from-[#c30102] to-[#e11d48] text-white rounded-lg text-[10px] font-black uppercase tracking-wider shadow-md border border-white/20">
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
                                <h3 class="text-base sm:text-lg font-bold text-[#041536] group-hover:text-[#c30102] transition-colors leading-snug mb-2.5 line-clamp-2">
                                    <a href="{{ route('blog.show', $blog->slug) }}">
                                        {{ $blog->title }}
                                    </a>
                                </h3>

                                <!-- Excerpt -->
                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed line-clamp-3 mb-4 font-normal">
                                    {{ $blog->excerpt }}
                                </p>
                            </div>

                            <!-- Author & Action Footer -->
                            <div class="border-t border-slate-100 pt-4 mt-auto flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center text-xs font-bold text-[#041536]">
                                        @if($blog->author && $blog->author->profile_picture)
                                            <img src="{{ asset($blog->author->profile_picture) }}" alt="{{ $blog->author_name }}" class="w-full h-full object-cover">
                                        @else
                                            {{ substr($blog->author_name ?? 'F', 0, 1) }}
                                        @endif
                                    </div>
                                    <span class="text-xs font-bold text-[#041536] truncate max-w-[130px]">
                                        {{ $blog->author_name ?? 'Distinguished Faculty' }}
                                    </span>
                                </div>

                                <a href="{{ route('blog.show', $blog->slug) }}" class="text-xs font-bold text-[#c30102] group-hover:text-[#041536] flex items-center gap-1 transition-colors">
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

    <!-- 6. Exact IPDP "Need To Learn More ?" CTA Section (Upgraded Jewel Emerald Canvas) -->
    <section class="py-16 sm:py-20 bg-gradient-to-r from-[#022b1e] via-[#045037] via-[#066345] to-[#033726] relative overflow-hidden text-white shadow-2xl">
        <!-- Ambient Jewel Glows -->
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-emerald-400/15 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-amber-400/10 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
            <h2 class="text-3xl sm:text-4xl font-black mb-3 text-white tracking-tight">
                Need To Learn More ?
            </h2>
            <p class="text-sm sm:text-base text-emerald-100/90 max-w-2xl mx-auto mb-8 font-normal leading-relaxed">
                Connect with our academic counselors for corporate training customized for your organization, batch reservations, or curriculum guidance.
            </p>
            <div class="flex flex-wrap justify-center items-center gap-4">
                <a href="tel:01917054664" class="btn-ipdp-primary px-8 py-4 rounded-xl font-black text-sm shadow-[0_10px_25px_rgba(195,1,2,0.45)] flex items-center gap-2 hover:scale-105 transition-all">
                    <i class="fas fa-phone-alt"></i>
                    <span>Call: 01917-054664</span>
                </a>
                <a href="{{ route('catalog.category', 'export-import') }}" class="bg-white text-[#041536] hover:bg-slate-100 px-8 py-4 rounded-xl font-black text-sm shadow-xl transition-all duration-300 flex items-center gap-2 hover:scale-105">
                    <span>Explore Export – Import</span>
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- 7. IPDP "Our Features" Section (Distinguished Faculty & Fellows Terminology) -->
    <section id="features" class="py-16 sm:py-20 bg-slate-50/70 border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <span class="bg-[#c30102]/10 text-[#c30102] px-3.5 py-1 rounded-full text-xs uppercase tracking-widest font-extrabold inline-block mb-2">Institutional Excellence</span>
                <h2 class="text-3xl font-black text-[#041536] tracking-tight">
                    Our Features
                </h2>
                <div class="w-16 h-1 bg-gradient-to-r from-[#c30102] via-[#e11d48] to-[#041536] mx-auto mt-3 rounded-full"></div>
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
                            'title' => '100% Live Interactive Online',
                            'desc' => 'Live Zoom masterclasses with real-time Q&A, session recordings, and cloud course materials accessible from anywhere.'
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
                    <div class="bg-white p-7 rounded-3xl border border-slate-200/90 shadow-[0_2px_12px_rgba(4,21,54,0.04)] hover:shadow-[0_18px_40px_-8px_rgba(4,21,54,0.14)] hover:border-[#c30102]/30 transition-all duration-300 group hover:-translate-y-1.5 flex flex-col justify-between">
                        <div>
                            <div class="w-14 h-14 rounded-2xl bg-[#041536]/5 text-[#041536] group-hover:bg-gradient-to-tr group-hover:from-[#c30102] group-hover:to-[#e11d48] group-hover:text-white transition-all duration-300 flex items-center justify-center text-2xl mb-5 shadow-sm">
                                <i class="fas {{ $feat['icon'] }}"></i>
                            </div>
                            <h4 class="text-lg font-black text-[#041536] mb-2 group-hover:text-[#c30102] transition-colors">
                                {{ $feat['title'] }}
                            </h4>
                            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed font-normal">
                                {{ $feat['desc'] }}
                            </p>
                        </div>
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
