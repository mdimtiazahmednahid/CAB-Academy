@props(['course'])

<article class="liquid-glass-card group bg-white/85 rounded-2xl shadow-[0_2px_12px_rgba(4,21,54,0.06)] hover:shadow-[0_20px_40px_-10px_rgba(4,21,54,0.18),0_0_0_1px_rgba(195,1,2,0.22)] transition-all duration-300 flex flex-col h-full border border-slate-200/90 hover:border-[#c30102]/40 overflow-hidden hover:-translate-y-1.5 relative">
    
    <!-- Top Accent Line (Smooth tri-color glow on hover) -->
    <div class="h-0.5 w-full bg-gradient-to-r from-white/80 via-slate-200/70 to-white/80 group-hover:from-[#041536] group-hover:via-[#c30102] group-hover:to-[#10b981] transition-all duration-300"></div>

    <!-- 1. Thumbnail Container (Sleek 16:10 Landscape, height-controlled, with Cypress aspect-square hook & Skeleton Shimmer) -->
    <div class="relative aspect-square aspect-[16/10] h-40 sm:h-44 lg:h-48 w-full bg-slate-900/5 overflow-visible">
        <a href="{{ route('courses.show', $course) }}" class="img-skeleton-wrapper block w-full h-full overflow-hidden relative">
            @php
                $imgSrc = $course->cover_image_url;
            @endphp
            <img 
                src="{{ $imgSrc }}" 
                alt="{{ $course->title }}" 
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out relative z-10"
                loading="lazy"
                onload="this.parentElement.classList.add('is-loaded')"
                onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=800&q=80'; this.parentElement.classList.add('is-loaded');"
            >

            <!-- Subtle Scrim Vignette -->
            <div class="absolute inset-0 bg-gradient-to-t from-[#020b18]/85 via-black/15 to-transparent pointer-events-none transition-opacity duration-300 z-10"></div>
        </a>

        <!-- Action Buttons: Top-Left with Frosted Glass (Wishlist + Facebook Share) -->
        @php
            $isWishlisted = auth()->check() && (auth()->user()->relationLoaded('wishlists')
                ? auth()->user()->wishlists->contains('course_id', $course->id)
                : auth()->user()->wishlists()->where('course_id', $course->id)->exists());
        @endphp
        <div class="absolute top-2.5 left-2.5 z-20 flex items-center gap-1.5">
            <button 
                type="button"
                onclick="toggleWishlist({{ $course->id }}, this)"
                class="w-8 h-8 rounded-full bg-slate-900/40 backdrop-blur-md border border-white/20 text-white hover:bg-white hover:text-[#c30102] hover:scale-110 shadow flex items-center justify-center transition-all cursor-pointer wishlist-btn-{{ $course->id }} {{ $isWishlisted ? '!bg-white !text-[#c30102]' : '' }}"
                title="Save to Wishlist"
            >
                <i class="{{ $isWishlisted ? 'fas text-[#c30102]' : 'far' }} fa-heart text-[11px]"></i>
            </button>
            <button 
                type="button"
                onclick="window.open('{{ $course->facebook_share_url }}', 'fbshare', 'width=640,height=560,menubar=no,toolbar=no');"
                class="w-8 h-8 rounded-full bg-slate-900/40 backdrop-blur-md border border-white/20 text-white hover:bg-[#1877F2] hover:border-[#1877F2] hover:text-white hover:scale-110 shadow flex items-center justify-center transition-all cursor-pointer"
                title="Share on Facebook"
            >
                <i class="fab fa-facebook-f text-[10px]"></i>
            </button>
        </div>

        @if(!empty($course->batch) && $course->show_batch)
            <!-- Batch Scarcity Badge: Top-Right (Shown only when enabled by admin) -->
            <div class="absolute top-2.5 right-2.5 z-10">
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-gradient-to-r from-[#c30102] to-[#e11d48] text-white text-[9px] font-black tracking-wider uppercase shadow-md border border-white/20 backdrop-blur-md">
                    <span class="w-1 h-1 rounded-full bg-white animate-ping"></span>
                    <span>{{ $course->batch }}</span>
                </span>
            </div>
        @endif

        <!-- Course Price Placement: Bottom-Left on Image with compact glass shield -->
        <div class="absolute left-0 bottom-0 z-10 bg-gradient-to-r from-[#020b18]/95 via-[#041536]/90 to-[#072152]/85 backdrop-blur-md text-white border-t border-r border-white/20 px-2.5 sm:px-3 py-1 rounded-tr-xl shadow-lg flex items-center gap-1.5 pointer-events-none">
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

        <!-- Instructor Avatar Placement: Compact Right Corner Badge (-bottom-10 hook preserved for Cypress) -->
        @php
            $leadInst = $course->lead_faculty;
            $instName = $course->instructor_name;
            $instTitle = $course->instructor_title;
            $instAvatar = $course->instructor_avatar_url;
        @endphp
        <div class="absolute -bottom-10 right-3 z-20 flex items-end justify-end pointer-events-auto">
            <a href="{{ route('courses.show', $course) }}" title="{{ $instName }} — {{ $instTitle }}" class="block relative group/avatar translate-y-5 sm:translate-y-4">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full border-2 border-white shadow-md overflow-hidden bg-white ring-2 ring-slate-100 group-hover:ring-[#c30102]/40 group-hover:scale-105 transition-all duration-300">
                    @if($instAvatar)
                        <img 
                            src="{{ $instAvatar }}" 
                            alt="{{ $instName }}" 
                            class="w-full h-full object-cover object-top rounded-full"
                            onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($instName) }}&background=051c45&color=fff';"
                        >
                    @else
                        <div class="w-full h-full rounded-full bg-gradient-to-br from-[#041536] to-[#020b18] flex items-center justify-center text-white font-black text-xs">
                            {{ substr($instName, 0, 1) }}
                        </div>
                    @endif
                </div>
                <!-- Micro Verified Faculty Pin -->
                <div class="absolute bottom-0 right-0 w-3 h-3 rounded-full bg-[#10b981] text-white flex items-center justify-center text-[6px] font-bold border border-white shadow-sm" title="Verified Faculty: {{ $instName }}">
                    <i class="fas fa-check"></i>
                </div>
            </a>
        </div>
    </div>

    <!-- 2. Course Body Content (Streamlined, Compact & Collision-Free with Liquid Glass Clarity) -->
    <div class="liquid-glass-body p-3.5 sm:p-4.5 pt-3 sm:pt-3.5 flex-1 flex flex-col justify-between">
        <div>
            <!-- Category Tag & Live Online Indicator: Grouped on Left with pr-16 clearing the avatar -->
            <div class="flex items-center gap-1.5 mb-2 pr-16 flex-wrap">
                <a 
                    href="{{ route('catalog.category', \Illuminate\Support\Str::slug($course->category ?? 'export-import')) }}" 
                    class="liquid-glass-pill inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider text-[#041536] hover:bg-[#c30102]/10 hover:text-[#c30102] transition-colors shrink-0"
                >
                    <span class="w-1.5 h-1.5 rounded-full bg-[#c30102]"></span>
                    <span>{{ $course->category ?? 'Export – Import' }}</span>
                </a>
                @if($course->attendance_mode === 'in_person')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50/90 text-indigo-800 border border-indigo-200/80 shrink-0">
                        <i class="fas fa-building text-[9px] text-indigo-600"></i>
                        <span>Campus</span>
                    </span>
                @elseif($course->attendance_mode === 'hybrid')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-50/90 text-sky-800 border border-sky-200/80 shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-sky-500 animate-pulse"></span>
                        <span>Zoom &amp; Campus</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50/90 text-emerald-800 border border-emerald-200/80 shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Live Zoom</span>
                    </span>
                @endif
            </div>

            <!-- Course Title -->
            <h3 class="font-black text-[#041536] text-[13px] sm:text-[15px] leading-snug line-clamp-2 mb-1.5 group-hover:text-[#c30102] transition-colors tracking-tight">
                <a href="{{ route('courses.show', $course) }}">
                    {{ $course->title }}
                </a>
            </h3>

            <!-- Short Description Excerpt (High-Contrast Slate for Crisp Visibility) -->
            <p class="text-slate-600 text-[11px] sm:text-xs leading-relaxed line-clamp-2 mb-3 font-medium">
                {{ $course->short_description ?? ($course->description ?? 'Live interactive professional certification masterclass with authentic practical case simulations.') }}
            </p>
        </div>

        <!-- 3. Bottom Conversion Zone: Schedule + Centered High-Focus "Enroll Now" Button -->
        <div class="border-t border-slate-200/70 pt-2.5 mt-auto flex flex-col gap-2">
            <!-- Schedule / Start Date + Urgency Badge (Centered) -->
            <div class="flex items-center justify-center text-[11px] sm:text-xs text-slate-600 font-semibold gap-2 flex-wrap">
                <div class="flex items-center gap-1.5">
                    <i class="far fa-clock text-[11px] text-[#c30102]"></i>
                    <span>
                        @if($course->start_date)
                            Starts {{ $course->start_date->format('d M, Y') }}
                        @elseif($course->duration)
                            {{ $course->duration }}
                        @else
                            Upcoming Batch
                        @endif
                    </span>
                </div>
                <span class="inline-flex items-center gap-1 text-[9px] font-extrabold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/60 uppercase tracking-wider">
                    <span class="w-1 h-1 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Seats Open</span>
                </span>
            </div>

            <!-- Centered Prominent High-Conversion Action Button (46px Touch Ergonomics) -->
            <a 
                href="{{ route('courses.show', $course) }}" 
                onclick="event.preventDefault(); openEnrollDrawer({ 
                    id: {{ $course->id }}, 
                    title: '{{ addslashes($course->title) }}', 
                    price: '{{ number_format($course->price, 2) }}', 
                    rawPrice: {{ $course->price }}, 
                    batch: '{{ (!empty($course->batch) && $course->show_batch) ? addslashes($course->batch) : '' }}', 
                    venue: '{{ addslashes($course->venue ?? 'Online Live (Zoom)') }}',
                    attendanceMode: '{{ $course->attendance_mode }}',
                    whatsapp: '{{ $course->effective_whatsapp_number }}',
                    fields: {{ json_encode($course->effective_enrollment_fields) }}
                })"
                class="btn-ipdp-primary w-full py-3 sm:py-3.5 min-h-[46px] px-4 rounded-xl text-xs sm:text-sm font-black tracking-wider uppercase shadow-[0_4px_16px_rgba(195,1,2,0.32)] hover:shadow-[0_8px_24px_rgba(195,1,2,0.48)] flex items-center justify-center gap-2 cursor-pointer group/btn transition-all duration-200 hover:scale-[1.02] active:scale-95 text-center"
            >
                <span>Enroll Now</span>
                <i class="fas fa-arrow-right text-[10px] sm:text-xs group-hover/btn:translate-x-1 transition-transform"></i>
            </a>
        </div>
    </div>
</article>

