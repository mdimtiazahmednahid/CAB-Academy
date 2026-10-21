@props(['course'])

<article class="group bg-white rounded-2xl shadow-[0_2px_12px_rgba(5,28,69,0.06)] hover:shadow-[0_16px_36px_-8px_rgba(5,28,69,0.18),0_0_0_1px_rgba(195,1,2,0.2)] transition-all duration-300 flex flex-col h-full border border-slate-200/80 hover:border-[#c30102]/40 overflow-hidden hover:-translate-y-1 relative">
    
    <!-- Top Accent Line (Smooth glow on hover) -->
    <div class="h-0.5 w-full bg-slate-100 group-hover:bg-gradient-to-r group-hover:from-[#051c45] group-hover:via-[#c30102] group-hover:to-[#10b981] transition-all duration-300"></div>

    <!-- 1. Thumbnail Container (1:1 Aspect Ratio with exact IPDP overlay hooks) -->
    <div class="relative aspect-square w-full bg-slate-900/5 overflow-visible">
        <a href="{{ route('courses.show', $course) }}" class="block w-full h-full overflow-hidden relative">
            @php
                $imgSrc = $course->cover_image ? asset($course->cover_image) : ($course->thumbnail ?: 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=800&q=80');
            @endphp
            <img 
                src="{{ $imgSrc }}" 
                alt="{{ $course->title }}" 
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                loading="lazy"
                onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=800&q=80';"
            >

            <!-- Subtle Scrim Vignette -->
            <div class="absolute inset-0 bg-gradient-to-t from-[#031330]/80 via-black/10 to-transparent pointer-events-none transition-opacity duration-300"></div>
        </a>

        <!-- Wishlist Heart Button: Top-Left with Frosted Glass -->
        <div class="absolute top-2.5 left-2.5 z-20">
            <button 
                type="button"
                onclick="toggleWishlist({{ $course->id }}, this)"
                class="w-7.5 h-7.5 rounded-full bg-slate-900/40 backdrop-blur-md border border-white/20 text-white hover:bg-white hover:text-[#c30102] hover:scale-110 shadow flex items-center justify-center transition-all cursor-pointer wishlist-btn-{{ $course->id }} {{ auth()->check() && auth()->user()->wishlists()->where('course_id', $course->id)->exists() ? '!bg-white !text-[#c30102]' : '' }}"
                title="Save to Wishlist"
            >
                <i class="{{ auth()->check() && auth()->user()->wishlists()->where('course_id', $course->id)->exists() ? 'fas text-[#c30102]' : 'far' }} fa-heart text-[11px]"></i>
            </button>
        </div>

        <!-- Batch Scarcity Badge: Top-Right (Minimalist Live Pill) -->
        <div class="absolute top-2.5 right-2.5 z-10">
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-gradient-to-r from-[#c30102] to-[#e11d48] text-white text-[9px] font-black tracking-wider uppercase shadow-md border border-white/20 backdrop-blur-md">
                <span class="w-1 h-1 rounded-full bg-white animate-ping"></span>
                <span>{{ $course->batch ?? ($course->id ? ($course->id * 23 + 12) . 'th BATCH' : '105th BATCH') }}</span>
            </span>
        </div>

        <!-- Exact IPDP Price Placement: Bottom-Left on Image with compact glass shield -->
        <div class="absolute left-0 bottom-0 z-10 bg-gradient-to-r from-[#031330]/95 via-[#051c45]/90 to-[#0a2540]/85 backdrop-blur-md text-white border-t border-r border-white/20 px-2.5 py-1 rounded-tr-xl shadow-lg flex items-center gap-1.5 pointer-events-none">
            @if($course->price > 0)
                <span class="text-[9px] text-slate-300 font-bold uppercase hidden sm:inline">Fee:</span>
                <span class="font-black text-xs sm:text-sm text-white tracking-tight">{{ number_format($course->price, 2) }} ৳</span>
            @else
                <span class="text-[#10b981] font-black text-xs flex items-center gap-1">
                    <i class="fas fa-gift text-[10px]"></i>
                    <span>Free ৳</span>
                </span>
            @endif
        </div>

        <!-- Exact IPDP Teacher Avatar Placement: Compact Right Corner Badge (-bottom-10 hook preserved for Cypress) -->
        @if($course->instructor)
            <div class="absolute -bottom-10 right-3 z-20 flex items-end justify-end pointer-events-auto">
                <a href="{{ route('courses.show', $course) }}" title="{{ $course->instructor->name }}" class="block relative group/avatar translate-y-4">
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-full border-2 border-white shadow-md overflow-hidden bg-white ring-1 ring-slate-200 group-hover:ring-[#c30102]/40 group-hover:scale-105 transition-all duration-300">
                        @if($course->instructor->profile_picture)
                            <img 
                                src="{{ asset($course->instructor->profile_picture) }}" 
                                alt="{{ $course->instructor->name }}" 
                                class="w-full h-full object-cover object-top rounded-full"
                                onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80';"
                            >
                        @else
                            <div class="w-full h-full rounded-full bg-gradient-to-br from-[#051c45] to-[#031330] flex items-center justify-center text-white font-black text-sm">
                                {{ substr($course->instructor->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <!-- Micro Verified Faculty Pin -->
                    <div class="absolute bottom-0 right-0 w-3.5 h-3.5 rounded-full bg-[#10b981] text-white flex items-center justify-center text-[7px] font-bold border border-white shadow-sm" title="Verified Faculty">
                        <i class="fas fa-check"></i>
                    </div>
                </a>
            </div>
        @endif
    </div>

    <!-- 2. Course Body Content (Streamlined & Compact) -->
    <div class="p-4 sm:p-4.5 pt-3.5 flex-1 flex flex-col bg-white justify-between">
        <div>
            <!-- Category Tag & Live Online Indicator -->
            <div class="flex items-center justify-between gap-2 mb-1.5 pr-12">
                <a 
                    href="{{ route('catalog.category', \Illuminate\Support\Str::slug($course->category ?? 'export-import')) }}" 
                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-100 text-[#051c45] hover:bg-[#c30102]/10 hover:text-[#c30102] transition-colors truncate"
                >
                    <span class="w-1 h-1 rounded-full bg-[#c30102]"></span>
                    <span class="truncate">{{ $course->category ?? 'Export – Import' }}</span>
                </a>
                <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded flex items-center gap-1 shrink-0">
                    <span class="w-1 h-1 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Live Zoom</span>
                </span>
            </div>

            <!-- Course Title -->
            <h3 class="font-extrabold text-[#051c45] text-sm sm:text-[15px] leading-snug line-clamp-2 mb-1.5 group-hover:text-[#c30102] transition-colors tracking-tight">
                <a href="{{ route('courses.show', $course) }}">
                    {{ $course->title }}
                </a>
            </h3>

            <!-- Short Description Excerpt (Clean 2-line clamp) -->
            <p class="text-slate-500 text-xs leading-relaxed line-clamp-2 mb-3 font-normal">
                {{ $course->short_description ?? ($course->description ?? 'Live interactive professional certification masterclass with authentic practical case simulations.') }}
            </p>
        </div>

        <!-- 3. Bottom Meta Bar (Clock + Start Date + Enroll Now Button) -->
        <div class="border-t border-slate-100 pt-3 mt-auto flex items-center justify-between gap-2">
            <!-- Starting Date with Clock Icon -->
            <div class="flex items-center text-xs text-slate-500 font-semibold gap-1.5 truncate">
                <i class="far fa-clock text-[11px] text-[#051c45]"></i>
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

            <!-- Enroll Now Action Button -->
            <a 
                href="{{ route('courses.show', $course) }}" 
                onclick="event.preventDefault(); openEnrollDrawer({ 
                    id: {{ $course->id }}, 
                    title: '{{ addslashes($course->title) }}', 
                    price: '{{ number_format($course->price, 2) }}', 
                    rawPrice: {{ $course->price }}, 
                    batch: '{{ $course->batch ?? '105th BATCH' }}', 
                    venue: 'Interactive Live Zoom Masterclass' 
                })"
                class="btn-ipdp-primary text-xs font-bold px-3.5 py-1.5 rounded-lg shadow-sm hover:shadow-md inline-flex items-center gap-1 shrink-0 cursor-pointer group/btn transition-all"
            >
                <span>Enroll Now</span>
                <i class="fas fa-arrow-right text-[9px] group-hover/btn:translate-x-0.5 transition-transform"></i>
            </a>
        </div>
    </div>
</article>

