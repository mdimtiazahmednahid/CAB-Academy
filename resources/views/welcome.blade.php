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
                    <div class="space-y-2">
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight text-white uppercase drop-shadow-sm">
                            CAB Academy
                        </h1>
                        <h3 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 via-teal-300 to-amber-300 tracking-wide">
                            Learn, Grow, Lead.
                        </h3>
                    </div>
                    
                    <p class="text-base sm:text-lg text-slate-300 font-normal max-w-xl leading-relaxed">
                        Online Professional Certification Masterclasses & Corporate Programs.
                    </p>

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
                            onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=1000&q=80';"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-[#031330] via-black/40 to-transparent"></div>
                        <a 
                            href="{{ route('catalog.index') }}" 
                            class="relative z-10 w-20 h-20 rounded-full bg-[#c30102] text-white flex items-center justify-center shadow-[0_0_35px_rgba(195,1,2,0.6)] hover:bg-[#a50102] hover:scale-110 transition-all duration-300"
                            aria-label="Explore Courses"
                        >
                            <i class="fas fa-play text-2xl ml-1 text-white"></i>
                        </a>
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
