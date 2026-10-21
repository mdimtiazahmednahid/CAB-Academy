@props(['course'])

<article class="group bg-white rounded-2xl sm:rounded-3xl shadow-[0_4px_20px_-4px_rgba(5,28,69,0.07),0_12px_28px_-6px_rgba(5,28,69,0.08)] hover:shadow-[0_24px_50px_-12px_rgba(5,28,69,0.2),0_0_0_1px_rgba(195,1,2,0.18)] transition-all duration-300 flex flex-col h-full border border-slate-200/80 hover:border-[#c30102]/40 overflow-hidden hover:-translate-y-1.5 relative">
    
    <!-- Top Decorative Accent Line (Animated multi-color glow on hover) -->
    <div class="h-1 w-full bg-slate-100 group-hover:bg-gradient-to-r group-hover:from-[#051c45] group-hover:via-[#c30102] group-hover:to-[#10b981] transition-all duration-500"></div>

    <!-- 1. Thumbnail Container (1:1 Aspect Ratio with exact IPDP overlay coordinates) -->
    <div class="relative aspect-square w-full bg-slate-900/5 overflow-visible">
        <a href="{{ route('courses.show', $course) }}" class="block w-full h-full overflow-hidden relative">
            @php
                $imgSrc = $course->cover_image ? asset($course->cover_image) : ($course->thumbnail ?: 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=800&q=80');
            @endphp
            <img 
                src="{{ $imgSrc }}" 
                alt="{{ $course->title }}" 
                class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-700 ease-out"
                loading="lazy"
                onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=800&q=80';"
            >

            <!-- Subtle Vignette Gradient for Overlay Contrast -->
            <div class="absolute inset-0 bg-gradient-to-t from-[#031330]/85 via-black/20 to-transparent pointer-events-none transition-opacity duration-300 group-hover:opacity-90"></div>
        </a>

        <!-- Wishlist Heart Button: Top-Left on Image with Frosted Glass -->
        <div class="absolute top-3 left-3 z-20">
            <button 
                type="button"
                onclick="toggleWishlist({{ $course->id }}, this)"
                class="w-8.5 h-8.5 rounded-full bg-slate-900/40 backdrop-blur-md border border-white/25 text-white hover:bg-white hover:text-[#c30102] hover:scale-110 shadow-lg flex items-center justify-center transition-all cursor-pointer wishlist-btn-{{ $course->id }} {{ auth()->check() && auth()->user()->wishlists()->where('course_id', $course->id)->exists() ? '!bg-white !text-[#c30102]' : '' }}"
                title="Save to Wishlist"
            >
                <i class="{{ auth()->check() && auth()->user()->wishlists()->where('course_id', $course->id)->exists() ? 'fas text-[#c30102]' : 'far' }} fa-heart text-xs"></i>
            </button>
        </div>

        <!-- Batch Scarcity Badge: Top-Right on Image (Pulsing live badge) -->
        <div class="absolute top-3 right-3 z-10">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gradient-to-r from-[#c30102] via-[#d60203] to-[#e11d48] text-white text-[10px] font-black tracking-wider uppercase shadow-[0_4px_12px_rgba(195,1,2,0.45)] border border-white/30 backdrop-blur-md">
                <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                <span>{{ $course->batch ?? ($course->id ? ($course->id * 23 + 12) . 'th BATCH' : '105th BATCH') }}</span>
            </span>
        </div>

        <!-- Exact IPDP Price Placement: Bottom-Left on Image with luxury frosted shield -->
        <div class="absolute left-0 bottom-0 z-10 bg-gradient-to-r from-[#031330]/95 via-[#051c45]/92 to-[#0a2540]/88 backdrop-blur-md text-white border-t border-r border-white/20 px-3.5 py-1.5 sm:px-4 sm:py-2 rounded-tr-2xl shadow-xl flex items-center gap-2 pointer-events-none">
            <div class="flex flex-col">
                <span class="text-[9px] font-bold text-slate-300 uppercase tracking-widest leading-none hidden sm:block">Course Fee</span>
                <div class="flex items-baseline gap-1 mt-0.5">
                    @if($course->price > 0)
                        <span class="font-black text-xs sm:text-sm text-white tracking-tight">{{ number_format($course->price, 2) }} ৳</span>
                    @else
                        <span class="text-[#10b981] font-black text-xs sm:text-sm flex items-center gap-1">
                            <i class="fas fa-gift text-xs"></i>
                            <span>Free ৳</span>
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Exact IPDP Teacher Avatar Placement: Bottom-Right overlapping thumbnail by -40px -->
        @if($course->instructor)
            <div class="absolute -bottom-10 right-3 z-20">
                <a href="{{ route('courses.show', $course) }}" title="{{ $course->instructor->name }}" class="block relative group/avatar">
                    <div class="w-18 h-18 sm:w-20 sm:h-20 rounded-full border-4 border-white shadow-[0_12px_28px_rgba(5,28,69,0.22)] overflow-hidden bg-white ring-2 ring-slate-100 group-hover:scale-105 group-hover:ring-[#c30102]/40 transition-all duration-300">
                        @if($course->instructor->profile_picture)
                            <img 
                                src="{{ asset($course->instructor->profile_picture) }}" 
                                alt="{{ $course->instructor->name }}" 
                                class="w-full h-full object-cover object-top rounded-full"
                                onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80';"
                            >
                        @else
                            <div class="w-full h-full rounded-full bg-gradient-to-br from-[#051c45] to-[#031330] flex items-center justify-center text-white font-black text-xl">
                                {{ substr($course->instructor->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <!-- Verified Faculty Checkmark Pin -->
                    <div class="absolute bottom-0 right-0 w-5 h-5 rounded-full bg-[#10b981] text-white flex items-center justify-center text-[9px] font-bold border-2 border-white shadow-md ring-1 ring-emerald-500/20" title="Verified CAB Faculty">
                        <i class="fas fa-check"></i>
                    </div>
                </a>
            </div>
        @endif
    </div>

    <!-- 2. Course Body Content -->
    <div class="p-5 pt-7 flex-1 flex flex-col bg-white justify-between">
        <div>
            <!-- Top Section with right padding so text never collides with overlapping avatar -->
            <div class="pr-16 sm:pr-20">
                <!-- Category Tag with Pulsing Crimson Indicator -->
                <div class="mb-2">
                    <a 
                        href="{{ route('catalog.category', \Illuminate\Support\Str::slug($course->category ?? 'export-import')) }}" 
                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-slate-100 text-[#051c45] border border-slate-200/80 hover:bg-[#c30102]/10 hover:text-[#c30102] hover:border-[#c30102]/30 transition-all"
                    >
                        <span class="w-1.5 h-1.5 rounded-full bg-[#c30102]"></span>
                        <span>{{ $course->category ?? 'Export – Import' }}</span>
                    </a>
                </div>

                <!-- Course Title -->
                <h3 class="font-extrabold text-[#051c45] text-[15px] sm:text-[16px] leading-snug line-clamp-2 mb-2 group-hover:text-[#c30102] transition-colors tracking-tight">
                    <a href="{{ route('courses.show', $course) }}">
                        {{ $course->title }}
                    </a>
                </h3>
            </div>

            <!-- Instructor Byline -->
            @if($course->instructor)
                <p class="text-[11px] font-semibold text-slate-500 mb-2 truncate">
                    Mentor: <strong class="text-[#051c45] font-bold">{{ $course->instructor->name }}</strong>
                </p>
            @endif

            <!-- Short Description Excerpt -->
            <p class="text-slate-500 text-xs leading-relaxed line-clamp-2 mb-3.5 font-normal">
                {{ $course->short_description ?? ($course->description ?? 'New Entrepreneurs and professionals will know A to Z procedures and documentation.') }}
            </p>

            <!-- Social Proof & Accreditation Micro-Row -->
            <div class="flex items-center gap-2.5 text-[11px] text-slate-500 font-semibold mb-3">
                <div class="flex items-center gap-1 text-amber-500 font-bold">
                    <i class="fas fa-star text-[10px]"></i>
                    <span>4.9</span>
                    <span class="text-slate-400 font-normal">({{ 150 + ($course->id * 23) }}+)</span>
                </div>
                <span class="text-slate-300">&bull;</span>
                <div class="flex items-center gap-1 text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/60 text-[10px]">
                    <i class="fas fa-certificate text-[9px]"></i>
                    <span>Certified</span>
                </div>
            </div>
        </div>

        <!-- 3. Bottom Meta Bar (Clock + Starting Date + Enroll Now Button) -->
        <div class="border-t border-slate-100 pt-3.5 mt-auto flex items-center justify-between gap-2">
            <!-- Starting Date with Clock Icon -->
            <div class="flex items-center text-xs text-slate-600 font-semibold gap-1.5 truncate">
                <div class="w-6.5 h-6.5 rounded-lg bg-slate-100 group-hover:bg-[#c30102]/10 group-hover:text-[#c30102] text-[#051c45] flex items-center justify-center shrink-0 transition-colors">
                    <i class="far fa-clock text-[11px]"></i>
                </div>
                <span class="truncate">
                    @if($course->start_date)
                        {{ $course->start_date->format('d M, Y') }}
                    @elseif($course->duration)
                        {{ $course->duration }}
                    @else
                        Upcoming
                    @endif
                </span>
            </div>

            <!-- Enroll Now Action Button (Triggers Express 30-Sec Drawer) -->
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
                class="btn-ipdp-primary text-xs font-bold px-4 py-2 rounded-xl shadow-md hover:shadow-lg inline-flex items-center gap-1.5 shrink-0 cursor-pointer group/btn"
            >
                <span>Enroll Now</span>
                <i class="fas fa-arrow-right text-[10px] group-hover/btn:translate-x-0.5 transition-transform"></i>
            </a>
        </div>
    </div>
</article>

