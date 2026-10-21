@extends('layouts.public')

@section('title', ($categoryTitle ?? 'Export - Import'))

@section('content')
<div>

    <!-- 1. Exact IPDP Category Banner (.color_secondary: #90c642 background, centered white title) -->
    <section class="bg-[#90c642] text-white py-10 sm:py-12 border-b border-[#e3e7f0]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-white mb-2">
                {{ $categoryTitle ?? 'Export – Import' }}
            </h1>
            <!-- Breadcrumbs Navigation -->
            <div class="flex items-center justify-center gap-2 text-xs sm:text-sm text-white/90 font-medium">
                <a href="{{ url('/') }}" class="hover:underline">Home</a>
                <span>/</span>
                <a href="{{ route('catalog.index') }}" class="hover:underline">Course</a>
                @if($categoryTitle)
                    <span>/</span>
                    <span class="font-bold underline">{{ $categoryTitle }}</span>
                @endif
            </div>
        </div>
    </section>

    <!-- 2. Main Course Grid Section (.product-list) -->
    <section class="py-12 bg-[#f2f4f7]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Category Navigation Chips & Sorting Bar -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-8 pb-4 border-b border-[#e3e7f0]">
                <!-- Category Filters -->
                <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto pb-2 sm:pb-0 scrollbar-none">
                    <a 
                        href="{{ route('catalog.index') }}" 
                        class="px-4 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-colors {{ empty($selectedCategory) ? 'bg-[#f15a29] text-white' : 'bg-white text-[#35415b] hover:bg-[#e3e7f0] border border-[#e3e7f0]' }}"
                    >
                        All Categories
                    </a>
                    @foreach(array_slice($runningCategories, 0, 6) as $cat)
                        <a 
                            href="{{ route('catalog.category', $cat['slug']) }}" 
                            class="px-4 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-colors {{ ($selectedCategory && \Illuminate\Support\Str::slug($selectedCategory) === $cat['slug']) ? 'bg-[#f15a29] text-white' : 'bg-white text-[#35415b] hover:bg-[#e3e7f0] border border-[#e3e7f0]' }}"
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
                    <label for="sort" class="text-xs font-semibold text-[#727f9f]">Sort by:</label>
                    <select 
                        name="sort" 
                        id="sort" 
                        onchange="this.form.submit()" 
                        class="bg-white border border-[#e3e7f0] text-[#35415b] text-xs rounded-lg focus:ring-[#f15a29] focus:border-[#f15a29] py-1.5 pl-3 pr-8 shadow-sm font-medium"
                    >
                        <option value="recommended" {{ request('sort') == 'recommended' ? 'selected' : '' }}>Recommended</option>
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                    </select>
                </form>
            </div>

            <!-- 3-Column Course Grid (Exact IPDP Structure) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8 pb-10">
                @forelse($courses as $course)
                    <x-course-card :course="$course" />
                @empty
                    <div class="col-span-full py-16 px-6 text-center bg-white rounded-2xl border border-dashed border-[#e3e7f0]">
                        <i class="fas fa-graduation-cap text-4xl text-[#727f9f] mb-3"></i>
                        <h3 class="text-lg font-bold text-[#0f131f] mb-1">No Courses Currently Listed</h3>
                        <p class="text-xs text-[#727f9f] mb-4">Please check back soon or explore other professional disciplines.</p>
                        <a href="{{ route('catalog.index') }}" class="btn-ipdp-primary px-5 py-2 rounded text-xs font-bold inline-block">
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

    <!-- 3. Exact IPDP "Running Course Categories" Section (6 Columns Grid, 80x80 Circular Avatars) -->
    <section class="py-14 bg-white border-t border-[#e3e7f0]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Heading (Exact IPDP text) -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-[#0f131f] tracking-tight">
                    Running Course Categories
                </h2>
                <div class="w-12 h-1 bg-[#f15a29] mt-2 rounded-full"></div>
            </div>

            <!-- 6 Columns Categories Grid (Matches IPDP --columns: 6; --gap: 10px) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
                @foreach($runningCategories as $catItem)
                    <a 
                        href="{{ route('catalog.category', $catItem['slug']) }}" 
                        class="group bg-white rounded-2xl p-4 border border-[#e3e7f0]/70 shadow-[0_0.1rem_0.2rem_rgba(0,0,0,0.06),0_0.33rem_1rem_rgba(0,0,0,0.06)] hover:shadow-[0_0.2rem_0.4rem_rgba(0,0,0,0.1),0_0.67rem_2rem_rgba(0,0,0,0.12)] transition-all duration-300 text-center flex flex-col items-center justify-between hover:-translate-y-1"
                    >
                        <!-- 80x80px Circular Thumbnail (Exact IPDP .teacher-list img style) -->
                        <div class="w-20 h-20 rounded-full overflow-hidden mb-3 border-2 border-white shadow-sm bg-gray-100 group-hover:scale-105 transition-transform duration-300 shrink-0">
                            <img 
                                src="{{ $catItem['image'] }}" 
                                alt="{{ $catItem['name'] }}" 
                                class="w-full h-full object-cover object-top"
                                loading="lazy"
                            >
                        </div>

                        <!-- Category Title (.usg_post_title_1: centered, font-size: 1rem) -->
                        <div class="w-full">
                            <h3 class="text-xs sm:text-sm font-bold text-[#0f131f] group-hover:text-[#f15a29] transition-colors leading-tight line-clamp-2 mb-1">
                                {{ $catItem['name'] }}
                            </h3>
                            <span class="text-[11px] text-[#727f9f]">
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
