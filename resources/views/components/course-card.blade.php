@props(['course'])

<article class="group bg-white rounded-2xl shadow-[0_0.1rem_0.2rem_rgba(0,0,0,0.08),0_0.33rem_1rem_rgba(0,0,0,0.08)] hover:shadow-[0_0.2rem_0.4rem_rgba(0,0,0,0.12),0_0.67rem_2rem_rgba(0,0,0,0.15)] transition-all duration-300 flex flex-col h-full border border-[#e3e7f0]/60 overflow-hidden">
    <!-- 1. Thumbnail Container (1:1 Ratio with exact IPDP overlay positions) -->
    <div class="relative aspect-square w-full bg-gray-100 overflow-visible">
        <a href="{{ route('courses.show', $course) }}" class="block w-full h-full overflow-hidden">
            @if($course->cover_image)
                <img 
                    src="{{ asset($course->cover_image) }}" 
                    alt="{{ $course->title }}" 
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                    loading="lazy"
                >
            @elseif($course->thumbnail)
                <img 
                    src="{{ $course->thumbnail }}" 
                    alt="{{ $course->title }}" 
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                    loading="lazy"
                >
            @else
                <div class="w-full h-full bg-gradient-to-br from-[#0f131f] via-[#1e2533] to-[#0f131f] flex items-center justify-center text-white">
                    <span class="font-extrabold text-5xl opacity-30">{{ substr($course->title, 0, 1) }}</span>
                </div>
            @endif
        </a>

        <!-- Wishlist Heart Button: Top-Left on Image -->
        <div class="absolute top-2.5 left-2.5 z-20">
            <button 
                type="button"
                onclick="toggleWishlist({{ $course->id }}, this)"
                class="w-8 h-8 rounded-full bg-white/90 backdrop-blur-sm shadow-md hover:bg-white flex items-center justify-center transition-all cursor-pointer wishlist-btn-{{ $course->id }} {{ auth()->check() && auth()->user()->wishlists()->where('course_id', $course->id)->exists() ? 'text-[#f15a29]' : 'text-gray-400 hover:text-[#f15a29]' }}"
                title="Save to Wishlist"
            >
                <i class="{{ auth()->check() && auth()->user()->wishlists()->where('course_id', $course->id)->exists() ? 'fas text-[#f15a29]' : 'far' }} fa-heart text-sm"></i>
            </button>
        </div>

        <!-- Exact IPDP Batch Badge: Top-Right on Image -->
        <div class="absolute top-2.5 right-2.5 z-10">
            <span class="inline-flex items-center px-2.5 py-1 rounded bg-[#f15a29] text-white text-[10px] sm:text-[11px] font-black tracking-wider uppercase shadow-md">
                {{ $course->batch ?? ($course->id ? ($course->id * 23 + 12) . 'th BATCH' : '105th BATCH') }}
            </span>
        </div>

        <!-- Exact IPDP Price Badge: Bottom-Left on Image (rgba(0,0,0,0.75)) -->
        <div class="absolute left-0 bottom-0 z-10 bg-black/80 text-white font-bold text-xs sm:text-sm px-3.5 py-1.5 pointer-events-none">
            @if($course->price > 0)
                <span>{{ number_format($course->price, 2) }} ৳</span>
            @else
                <span class="text-[#90c642]">Free</span>
            @endif
        </div>

        <!-- Exact IPDP Teacher/Faculty Avatar: Bottom-Right overlapping thumbnail by -40px -->
        @if($course->instructor)
            <div class="absolute -bottom-10 right-3 z-20">
                <a href="{{ route('courses.show', $course) }}" title="{{ $course->instructor->name }}">
                    <div class="w-20 h-20 rounded-full border-4 border-white shadow-md overflow-hidden bg-white">
                        @if($course->instructor->profile_picture)
                            <img 
                                src="{{ asset($course->instructor->profile_picture) }}" 
                                alt="{{ $course->instructor->name }}" 
                                class="w-full h-full object-cover object-top"
                            >
                        @else
                            <div class="w-full h-full bg-[#f2f4f7] flex items-center justify-center text-[#f15a29] font-bold text-xl">
                                {{ substr($course->instructor->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                </a>
            </div>
        @endif
    </div>

    <!-- 2. Course Body Content (.usg_vwrapper_2: padding 20px, white bg) -->
    <div class="p-5 pt-8 flex-1 flex flex-col bg-white">
        <!-- Category Pill Badge (.usg_post_taxonomy_1) -->
        <div class="mb-2">
            <a 
                href="{{ route('catalog.category', \Illuminate\Support\Str::slug($course->category ?? 'export-import')) }}" 
                class="text-[12px] uppercase text-[#727f9f] font-semibold tracking-wider hover:text-[#f15a29] transition-colors"
            >
                {{ $course->category ?? 'Export – Import' }}
            </a>
        </div>

        <!-- Course Title (.usg_post_title_1: 1rem, bold, line-clamp-2) -->
        <h3 class="font-bold text-[#0f131f] text-base leading-snug line-clamp-2 mb-2 group-hover:text-[#f15a29] transition-colors">
            <a href="{{ route('courses.show', $course) }}">
                {{ $course->title }}
            </a>
        </h3>

        <!-- Short Description Excerpt (.course_short_details: 0.8rem, line-clamp-2) -->
        <p class="text-[#35415b] text-xs leading-relaxed line-clamp-2 mb-4 flex-1">
            {{ $course->short_description ?? ($course->description ?? 'New Entrepreneurs and professionals will know A to Z procedures and documentation.') }}
        </p>

        <!-- Bottom Meta Bar (.usg_hwrapper_1: border-top, clock + date, Enroll Now button) -->
        <div class="border-t border-[#e3e7f0] pt-3 mt-auto flex items-center justify-between gap-2">
            <!-- Starting Date with Clock Icon -->
            <div class="flex items-center text-xs text-[#727f9f] font-medium gap-1.5 truncate">
                <i class="far fa-clock text-[#727f9f]"></i>
                <span class="truncate">
                    @if($course->start_date)
                        {{ $course->start_date->format('d F, Y') }}
                    @elseif($course->duration)
                        {{ $course->duration }}
                    @else
                        Upcoming
                    @endif
                </span>
            </div>

            <!-- Enroll Now Action Button (.usg_add_to_cart_1: 30-Second Express Drawer Trigger) -->
            <a 
                href="{{ route('courses.show', $course) }}" 
                onclick="event.preventDefault(); openEnrollDrawer({ 
                    id: {{ $course->id }}, 
                    title: '{{ addslashes($course->title) }}', 
                    price: '{{ number_format($course->price, 2) }}', 
                    rawPrice: {{ $course->price }}, 
                    batch: '{{ $course->batch ?? '105th BATCH' }}', 
                    venue: '{{ addslashes($course->venue ?? 'Mirpur-Pallabi Campus & Online Zoom') }}' 
                })"
                class="btn-ipdp-primary text-xs font-semibold px-4 py-1.5 rounded shadow-sm shrink-0 cursor-pointer"
            >
                Enroll Now
            </a>
        </div>
    </div>
</article>
