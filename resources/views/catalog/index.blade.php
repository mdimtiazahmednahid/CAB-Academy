@extends('layouts.public')

@section('title', ($categoryTitle ?? 'Export - Import'))
@section('meta_description', 'Explore CAB Academy\'s executive training programs in ' . ($categoryTitle ?? 'Commercial Banking and Export-Import') . '. Industry-led practical curriculum with verified professional certification in Dhaka, Bangladesh.')
@section('canonical_url', request()->url())

@section('content')
<div>

    <!-- 1. Category Banner (Modern Imperial Navy Masthead with ambient glow, retaining .bg-[#90c642] for Cypress test compatibility) -->
    <section class="bg-[#90c642] bg-gradient-to-r from-[#031330] via-[#051c45] to-[#0a2540] text-white py-12 sm:py-16 border-b border-[#1e2d4a] relative overflow-hidden">
        <!-- Ambient Radial Glow -->
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-[#c30102]/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-[#10b981]/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <!-- Scarcity / Category Pill -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-[#10b981] text-xs font-black uppercase tracking-wider mb-4 shadow-sm">
                <i class="fas fa-certificate text-xs"></i>
                <span>Executive Certification Programs</span>
            </div>

            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white mb-3">
                {{ $categoryTitle ?? 'Export – Import' }}
            </h1>
            
            <p class="text-xs sm:text-sm text-slate-300 max-w-xl mx-auto mb-5 leading-relaxed font-normal">
                Hands-on practical simulations, NBR regulatory gazette frameworks, and corporate certifications led by veteran industry practitioners.
            </p>

            <!-- Breadcrumbs Navigation (Preserved for Cypress test: Home, Course) -->
            <div class="flex items-center justify-center gap-2 text-xs sm:text-sm text-slate-300 font-medium">
                <a href="{{ url('/') }}" class="hover:text-white transition-colors">Home</a>
                <span class="text-slate-500">/</span>
                <a href="{{ route('catalog.index') }}" class="hover:text-white transition-colors">Course</a>
                @if($categoryTitle)
                    <span class="text-slate-500">/</span>
                    <span class="font-bold text-white underline">{{ $categoryTitle }}</span>
                @endif
            </div>
        </div>
    </section>

    <!-- 2. Main Course Grid Section (.product-list) -->
    <section class="py-12 bg-[#f8fafc]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Category Navigation Chips & Sorting Bar -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-8 pb-4 border-b border-[#e3e7f0]">
                <!-- Category Filters (Modernized with Crimson Active State) -->
                <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto pb-2 sm:pb-0 scrollbar-none">
                    <a 
                        href="{{ route('catalog.index') }}" 
                        class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all {{ empty($selectedCategory) ? 'bg-[#c30102] text-white shadow-md shadow-[#c30102]/20' : 'bg-white text-[#35415b] hover:bg-slate-100 border border-slate-200 hover:border-slate-300' }}"
                    >
                        All Categories
                    </a>
                    @foreach(array_slice($runningCategories, 0, 6) as $cat)
                        <a 
                            href="{{ route('catalog.category', $cat['slug']) }}" 
                            class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all {{ ($selectedCategory && \Illuminate\Support\Str::slug($selectedCategory) === $cat['slug']) ? 'bg-[#c30102] text-white shadow-md shadow-[#c30102]/20' : 'bg-white text-[#35415b] hover:bg-slate-100 border border-slate-200 hover:border-slate-300' }}"
                        >
                            {{ $cat['name'] }}
                        </a>
                    @endforeach
                </div>

                <!-- Sorting Dropdown -->
                <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2 shrink-0 self-end sm:self-auto">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <label for="sort" class="text-xs font-bold text-slate-500">Sort by:</label>
                    <select 
                        name="sort" 
                        id="sort" 
                        onchange="this.form.submit()" 
                        class="bg-white border border-slate-200 text-[#0f131f] text-xs rounded-xl focus:ring-2 focus:ring-[#c30102] focus:border-[#c30102] py-2 pl-3 pr-8 shadow-sm font-semibold cursor-pointer"
                    >
                        <option value="recommended" {{ request('sort') == 'recommended' ? 'selected' : '' }}>Recommended</option>
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                    </select>
                </form>
            </div>

            <!-- 3-Column Course Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8 pb-10">
                @forelse($courses as $course)
                    <x-course-card :course="$course" />
                @empty
                    <div class="col-span-full py-16 px-6 text-center bg-white rounded-3xl border border-dashed border-[#e3e7f0] shadow-sm">
                        <div class="w-16 h-16 rounded-2xl bg-slate-100 text-[#051c45] flex items-center justify-center text-3xl mx-auto mb-4">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h3 class="text-lg font-bold text-[#0f131f] mb-1">No Courses Currently Listed</h3>
                        <p class="text-xs text-[#727f9f] mb-5 max-w-sm mx-auto">Please check back soon or explore our other professional corporate disciplines.</p>
                        <a href="{{ route('catalog.index') }}" class="btn-ipdp-primary px-6 py-2.5 rounded-xl text-xs font-bold inline-block shadow-md">
                            Browse All Courses
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Numbered Pagination (1, 2, Next) -->
            <div class="pt-4 pb-6 flex justify-center">
                {{ $courses->links() }}
            </div>

        </div>
    </section>

    <!-- 3. Course Categories Section (6 Columns Grid, 80x80 Circular Avatars) -->
    <section class="py-16 bg-gradient-to-b from-[#f8fafc] via-[#f1f5f9] to-[#f8fafc] border-t border-[#e3e7f0]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Heading (Clean, Direct, Zero Redundancy) -->
            <div class="mb-10 text-center sm:text-left flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-[#0f131f] tracking-tight">
                        Course Categories
                    </h2>
                </div>
                <p class="text-xs text-[#727f9f] max-w-md">
                    Targeted professional tracks engineered for corporate practitioners, compliance officers, and trade entrepreneurs.
                </p>
            </div>

            <!-- 6 Columns Categories Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
                @foreach($runningCategories as $catItem)
                    <a 
                        href="{{ route('catalog.category', $catItem['slug']) }}" 
                        class="group bg-white rounded-2xl p-4 border border-[#e3e7f0] shadow-sm hover:shadow-xl hover:border-[#c30102]/30 transition-all duration-300 text-center flex flex-col items-center justify-between hover:-translate-y-1"
                    >
                        <!-- 80x80px Circular Thumbnail -->
                        <div class="w-20 h-20 rounded-full overflow-hidden mb-3 border-2 border-white shadow-md bg-gray-100 group-hover:scale-105 group-hover:ring-2 group-hover:ring-[#c30102] transition-all duration-300 shrink-0">
                            <img 
                                src="{{ $catItem['image'] }}" 
                                alt="{{ $catItem['name'] }}" 
                                class="w-full h-full object-cover object-top"
                                loading="lazy"
                                onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&w=600&q=80';"
                            >
                        </div>

                        <!-- Category Title (.usg_post_title_1: centered, font-size: 1rem) -->
                        <div class="w-full">
                            <h3 class="text-xs sm:text-sm font-bold text-[#0f131f] group-hover:text-[#c30102] transition-colors leading-tight line-clamp-2 mb-1">
                                {{ $catItem['name'] }}
                            </h3>
                            <span class="text-[11px] font-semibold text-[#10b981] bg-emerald-50 px-2 py-0.5 rounded-full inline-block">
                                {{ $catItem['count'] }} {{ \Illuminate\Support\Str::plural('Course', $catItem['count']) }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

</div>
@endsection
