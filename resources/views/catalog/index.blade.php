@extends('layouts.student')

@section('header_title', 'Course Catalog')

@section('content')
<div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Explore Courses</h1>
        <p class="text-gray-500 text-sm">Discover your next learning journey.</p>
    </div>
    
    <!-- Sorting Dropdown -->
    <form method="GET" action="{{ route('catalog.index') }}" class="flex items-center gap-2">
        <label for="sort" class="text-sm font-medium text-gray-700">Sort by:</label>
        <select name="sort" id="sort" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm focus:ring-primary focus:border-primary py-1.5 pl-3 pr-8">
            <option value="recommended" {{ request('sort') == 'recommended' ? 'selected' : '' }}>Recommended</option>
            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
        </select>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pb-8">
    @forelse($courses as $course)
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
                        ${{ number_format($course->price, 2) }}
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
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
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
    @empty
        <div class="col-span-full p-12 text-center bg-gray-50 rounded-3xl border border-gray-100">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            <h3 class="text-xl font-bold text-gray-900 mb-2">No Courses Available</h3>
            <p class="text-gray-500 max-w-sm mx-auto">There are currently no published courses. Please check back later!</p>
        </div>
    @endforelse
</div>
@endsection
