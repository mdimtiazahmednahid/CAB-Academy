@props(['course'])

<article class="group bg-white rounded-2xl shadow-[0_2px_12px_rgba(5,28,69,0.06)] hover:shadow-[0_16px_36px_rgba(5,28,69,0.12)] transition-all duration-300 flex flex-col h-full border border-slate-200/80 hover:border-slate-300 overflow-hidden hover:-translate-y-1">
    <!-- 1. Thumbnail Container (1:1 Ratio with exact IPDP overlay positions) -->
    <div class="relative aspect-square w-full bg-slate-100 overflow-visible">
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
                <div class="w-full h-full bg-gradient-to-br from-[#051c45] via-[#09214d] to-[#031330] flex items-center justify-center text-white">
                    <span class="font-extrabold text-5xl opacity-30">{{ substr($course->title, 0, 1) }}</span>
                </div>
            @endif
        </a>

        <!-- Wishlist Heart Button: Top-Left on Image -->
        <div class="absolute top-2.5 left-2.5 z-20">
            <button 
                type="button"
                onclick="toggleWishlist({{ $course->id }}, this)"
                class="w-8 h-8 rounded-full bg-white/90 backdrop-blur-md shadow-md hover:bg-white hover:scale-110 flex items-center justify-center transition-all cursor-pointer wishlist-btn-{{ $course->id }} {{ auth()->check() && auth()->user()->wishlists()->where('course_id', $course->id)->exists() ? 'text-[#c30102]' : 'text-slate-400 hover:text-[#c30102]' }}"
                title="Save to Wishlist"
            >
                <i class="{{ auth()->check() && auth()->user()->wishlists()->where('course_id', $course->id)->exists() ? 'fas text-[#c30102]' : 'far' }} fa-heart text-sm"></i>
            </button>
        </div>

        <!-- Batch Scarcity Badge: Top-Right on Image -->
        <div class="absolute top-2.5 right-2.5 z-10">
            <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-[#c30102] text-white text-[10px] sm:text-[11px] font-black tracking-wider uppercase shadow-md">
                {{ $course->batch ?? ($course->id ? ($course->id * 23 + 12) . 'th BATCH' : '105th BATCH') }}
            </span>
        </div>

        <!-- Price Badge: Bottom-Left on Image -->
        <div class="absolute left-0 bottom-0 z-10 bg-slate-900/90 backdrop-blur-sm text-white font-black text-xs sm:text-sm px-3.5 py-1.5 rounded-tr-lg pointer-events-none">
            @if($course->price > 0)
                <span>{{ number_format($course->price, 2) }} ৳</span>
            @else
                <span class="text-emerald-400">Free</span>
            @endif
        </div>

        <!-- Exact IPDP Teacher/Faculty Avatar: Bottom-Right overlapping thumbnail by -40px -->
        @if($course->instructor)
            <div class="absolute -bottom-10 right-3 z-20">
                <a href="{{ route('courses.show', $course) }}" title="{{ $course->instructor->name }}">
                    <div class="w-20 h-20 rounded-full border-4 border-white shadow-xl overflow-hidden bg-white ring-2 ring-slate-100 hover:scale-105 transition-transform">
                        @if($course->instructor->profile_picture)
                            <img 
                                src="{{ asset($course->instructor->profile_picture) }}" 
                                alt="{{ $course->instructor->name }}" 
                                class="w-full h-full object-cover object-top"
                            >
                        @else
                            <div class="w-full h-full bg-slate-100 flex items-center justify-center text-[#051c45] font-black text-xl">
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
                class="text-[11px] uppercase text-slate-500 font-bold tracking-wider hover:text-[#c30102] transition-colors"
            >
                {{ $course->category ?? 'Export – Import' }}
            </a>
        </div>

        <!-- Course Title (.usg_post_title_1) -->
        <h3 class="font-extrabold text-[#051c45] text-base leading-snug line-clamp-2 mb-2 group-hover:text-[#c30102] transition-colors">
            <a href="{{ route('courses.show', $course) }}">
                {{ $course->title }}
            </a>
        </h3>

        <!-- Short Description Excerpt (.course_short_details) -->
        <p class="text-slate-600 text-xs leading-relaxed line-clamp-2 mb-4 flex-1">
            {{ $course->short_description ?? ($course->description ?? 'New Entrepreneurs and professionals will know A to Z procedures and documentation.') }}
        </p>

        <!-- Bottom Meta Bar (.usg_hwrapper_1: border-top, clock + date, Enroll Now button) -->
        <div class="border-t border-slate-100 pt-3 mt-auto flex items-center justify-between gap-2">
            <!-- Starting Date with Clock Icon -->
            <div class="flex items-center text-xs text-slate-500 font-medium gap-1.5 truncate">
                <i class="far fa-clock text-slate-400"></i>
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
                class="btn-ipdp-primary text-xs font-bold px-4 py-2 rounded-xl shadow-md shrink-0 cursor-pointer"
            >
                Enroll Now
            </a>
        </div>
    </div>
</article>
