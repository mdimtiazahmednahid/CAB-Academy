@props(['course', 'compact' => false])

<article class="liquid-glass-card group rounded-2xl transition-all duration-300 flex flex-col h-full overflow-hidden hover:-translate-y-1.5 relative"
         style="background: linear-gradient(155deg, rgba(255, 255, 255, 0.94) 0%, rgba(248, 250, 252, 0.88) 100%); border: 1px solid rgba(220, 162, 44, 0.35); box-shadow: 0 6px 24px -6px rgba(2, 31, 89, 0.10), inset 0 1px 0 rgba(255, 255, 255, 0.95);">
    
    <!-- Top Accent Line (ACS Royal Cobalt -> 24K Gold -> Metallic Crimson) -->
    <div class="h-[2.5px] w-full transition-all duration-300" style="background: linear-gradient(90deg, #0066e6 0%, #dca22c 50%, #c4040a 100%);"></div>

    <!-- 1. Thumbnail Container (Clean Auto-Fit 16:10 Image, Zero Black Bars) -->
    <div class="relative w-full bg-[#021f59] overflow-visible" style="aspect-ratio: 16 / 10;">
        <a href="{{ route('courses.show', $course) }}" class="img-skeleton-wrapper block w-full h-full overflow-hidden relative bg-[#021f59]">
            @php
                $imgSrc = $course->cover_image_url;
            @endphp
            <img 
                src="{{ $imgSrc }}" 
                alt="{{ $course->title }}" 
                class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500 ease-out relative z-10"
                loading="lazy"
                onload="this.parentElement.classList.add('is-loaded')"
                onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=800&q=80'; this.parentElement.classList.add('is-loaded');"
            >

            <!-- Subtle Scrim Vignette -->
            <div class="absolute inset-0 bg-gradient-to-t from-[#01143d]/85 via-black/15 to-transparent pointer-events-none transition-opacity duration-300 z-10"></div>
        </a>

        <!-- Action Buttons: Top-Left with Frosted Glass (Wishlist + Facebook Share) -->
        @php
            $isWishlisted = auth()->check() && (auth()->user()->relationLoaded('wishlists')
                ? auth()->user()->wishlists->contains('course_id', $course->id)
                : auth()->user()->wishlists()->where('course_id', $course->id)->exists());
            $shareDisplayDomain = strtolower(preg_replace('/^www\./i', '', parse_url(config('app.url'), PHP_URL_HOST) ?: 'acsacademy.bd'));
            $courseSharePayload = [
                'id' => $course->id,
                'title' => $course->title,
                'postHeader' => $course->stylish_post_header,
                'stylishTitle' => $course->stylish_og_title,
                'socialCaption' => $course->stylish_social_caption,
                'shortUrl' => $course->short_url,
                'courseUrl' => route('courses.show', $course),
                'image' => $course->cover_image_url,
                'displayDomain' => $shareDisplayDomain,
                'facebookShareUrl' => $course->facebook_share_url,
                'whatsappShareUrl' => $course->whatsapp_share_url,
            ];
        @endphp
        <div class="absolute top-2.5 left-2.5 z-20 flex items-center gap-1.5">
            <button 
                type="button"
                onclick="toggleWishlist({{ $course->id }}, this)"
                class="w-8 h-8 rounded-full bg-slate-900/40 backdrop-blur-md border border-white/20 text-white hover:bg-white hover:text-[#c4040a] hover:scale-110 shadow flex items-center justify-center transition-all cursor-pointer wishlist-btn-{{ $course->id }} {{ $isWishlisted ? '!bg-white !text-[#c4040a]' : '' }}"
                title="Save to Wishlist"
            >
                <i class="{{ $isWishlisted ? 'fas text-[#c4040a]' : 'far' }} fa-heart text-[11px]"></i>
            </button>
            <button 
                type="button"
                data-fb-share-url="{{ $course->facebook_share_url }}"
                data-share-payload="{{ json_encode($courseSharePayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}"
                onclick="event.preventDefault(); event.stopPropagation(); openCourseShareModal(JSON.parse(this.dataset.sharePayload));"
                class="course-card-share-btn w-8 h-8 rounded-full bg-slate-900/40 backdrop-blur-md border border-white/20 text-white hover:bg-[#1877F2] hover:border-[#1877F2] hover:text-white hover:scale-110 shadow flex items-center justify-center transition-all cursor-pointer"
                title="Copy Stylish Caption & Preview Facebook Share"
            >
                <i class="fab fa-facebook-f text-[10px]"></i>
            </button>
        </div>

        @if(!empty($course->batch) && $course->show_batch)
            <!-- Batch Scarcity Badge: Top-Right (Shown only when enabled by admin) -->
            <div class="absolute top-2.5 right-2.5 z-10">
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-gradient-to-r from-[#c4040a] to-[#e11d48] text-white text-[9px] font-black tracking-wider uppercase shadow-md border border-white/20 backdrop-blur-md">
                    <span class="w-1 h-1 rounded-full bg-white animate-ping"></span>
                    <span>{{ $course->batch }}</span>
                </span>
            </div>
        @endif

        <!-- Course Price Placement: Metallic Crimson Gradient Shield for High Focus -->
        <div class="absolute left-0 bottom-0 z-10 backdrop-blur-md text-white {{ $compact ? 'px-2 sm:px-3 py-0.5 sm:py-1' : 'px-2.5 sm:px-3 py-1' }} rounded-tr-xl shadow-lg flex items-center gap-1 sm:gap-1.5 pointer-events-none"
             style="background: linear-gradient(135deg, #ff1e1e 0%, #c4040a 52%, #850004 100%); border-top: 1px solid rgba(255, 226, 122, 0.55); border-right: 1px solid rgba(255, 226, 122, 0.55);">
            @if($course->price > 0)
                <span class="text-[9px] text-amber-100/90 font-bold uppercase hidden sm:inline">Fee:</span>
                <span class="font-black {{ $compact ? 'text-[11px] sm:text-sm' : 'text-xs sm:text-sm' }} text-white tracking-tight">{{ number_format($course->price, 0) }} ৳</span>
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
        <div class="absolute -bottom-8 sm:-bottom-10 right-2 sm:right-3 z-20 flex items-end justify-end pointer-events-auto">
            <a href="{{ route('courses.show', $course) }}" title="{{ $instName }} — {{ $instTitle }}" class="block relative group/avatar -translate-y-4">
                <div class="{{ $compact ? 'w-7 h-7 sm:w-10 sm:h-10' : 'w-9 h-9 sm:w-10 sm:h-10' }} rounded-full border-2 border-white shadow-md overflow-hidden bg-white ring-2 ring-slate-100 group-hover:ring-[#c4040a]/40 group-hover:scale-105 transition-all duration-300">
                    @if($instAvatar)
                        <img 
                            src="{{ $instAvatar }}" 
                            alt="{{ $instName }}" 
                            class="w-full h-full object-cover object-top rounded-full"
                            onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($instName) }}&background=051c45&color=fff';"
                        >
                    @else
                        <div class="w-full h-full rounded-full bg-gradient-to-br from-[#021f59] to-[#01143d] flex items-center justify-center text-white font-black text-xs">
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
    <div class="liquid-glass-body {{ $compact ? 'p-2.5 sm:p-4 pt-2.5 sm:pt-3.5' : 'p-3.5 sm:p-4.5 pt-3 sm:pt-3.5' }} flex-1 flex flex-col justify-between">
        <div>
            <!-- Category Tag & Live Online Indicator (Clean, Collision-Free) -->
            <div class="flex items-center gap-1.5 mb-1.5 sm:mb-2 {{ $compact ? 'pr-8 sm:pr-12' : 'pr-16' }} flex-wrap">
                <a 
                    href="{{ route('catalog.category', \Illuminate\Support\Str::slug($course->category ?? 'export-import')) }}" 
                    class="liquid-glass-pill inline-flex items-center gap-1 px-2 sm:px-2.5 py-0.5 rounded-full text-[9px] sm:text-[10px] font-black uppercase tracking-wider text-[#021f59] hover:bg-[#c4040a]/10 hover:text-[#c4040a] transition-colors max-w-full truncate"
                >
                    <span class="w-1.5 h-1.5 rounded-full bg-[#c4040a] shrink-0"></span>
                    <span class="truncate">{{ $course->category ?? 'Export – Import' }}</span>
                </a>
                @if($course->attendance_mode === 'in_person')
                    <span class="{{ $compact ? 'hidden sm:inline-flex' : 'inline-flex' }} items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50/90 text-indigo-800 border border-indigo-200/80 shrink-0">
                        <i class="fas fa-building text-[9px] text-indigo-600"></i>
                        <span>Campus</span>
                    </span>
                @elseif($course->attendance_mode === 'hybrid')
                    <span class="{{ $compact ? 'hidden sm:inline-flex' : 'inline-flex' }} items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-50/90 text-sky-800 border border-sky-200/80 shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-sky-500 animate-pulse"></span>
                        <span>Zoom &amp; Campus</span>
                    </span>
                @else
                    <span class="{{ $compact ? 'hidden sm:inline-flex' : 'inline-flex' }} items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50/90 text-emerald-800 border border-emerald-200/80 shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Live Zoom</span>
                    </span>
                @endif
            </div>

            <!-- Course Title -->
            <h3 class="font-black text-[#021f59] {{ $compact ? 'text-xs sm:text-[15px]' : 'text-[13px] sm:text-[15px]' }} leading-snug line-clamp-2 mb-1.5 group-hover:text-[#c4040a] transition-colors tracking-tight">
                <a href="{{ route('courses.show', $course) }}">
                    {{ $course->title }}
                </a>
            </h3>

            <!-- Short Description Excerpt (Concise 1-line on Desktop, Hidden on Mobile for Clean Visual Cards) -->
            <p class="hidden sm:line-clamp-1 text-slate-600 text-xs leading-relaxed mb-2.5 font-medium">
                {{ $course->short_description ?? ($course->description ?? 'Live interactive professional certification masterclass with authentic practical case simulations.') }}
            </p>
        </div>

        <!-- 3. Bottom Conversion Zone: Schedule + Centered High-Focus "Enroll Now" Button -->
        @php
            $isEnrolledOnCard = auth()->check() && !auth()->user()->isAdmin() && (
                auth()->user()->relationLoaded('enrolledCourses')
                    ? auth()->user()->enrolledCourses->contains('id', $course->id)
                    : auth()->user()->enrolledCourses()->where('courses.id', $course->id)->exists()
            );
        @endphp
        <div class="border-t border-slate-200/70 pt-2 mt-auto flex flex-col gap-1.5 sm:gap-2">
            <!-- Schedule / Start Date + Urgency Badge (Centered) -->
            <div class="flex items-center justify-center text-[10px] sm:text-xs text-slate-600 font-semibold gap-1.5 sm:gap-2 flex-wrap">
                <div class="flex items-center gap-1">
                    <i class="far fa-clock text-[10px] sm:text-[11px] text-[#c4040a]"></i>
                    <span>
                        @if($course->start_date)
                            {{ $course->start_date->format('d M, Y') }}
                        @elseif($course->duration)
                            {{ $course->duration }}
                        @else
                            Upcoming Batch
                        @endif
                    </span>
                </div>
                @if($isEnrolledOnCard)
                    <span class="inline-flex items-center gap-1 text-[9px] font-extrabold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/60 uppercase tracking-wider">
                        <i class="fas fa-check-circle text-[8px] text-emerald-600"></i>
                        <span>Enrolled</span>
                    </span>
                @elseif($course->isRegistrationOpen())
                    <span class="{{ $compact ? 'hidden sm:inline-flex' : 'inline-flex' }} items-center gap-1 text-[9px] font-extrabold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/60 uppercase tracking-wider">
                        <span class="w-1 h-1 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Seats Open</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 text-[9px] font-extrabold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full border border-slate-300/80 uppercase tracking-wider">
                        <i class="fas fa-lock text-[8px]"></i>
                        <span>Closed</span>
                    </span>
                @endif
            </div>

            <!-- Centered Prominent High-Conversion Action Button (46px Touch Ergonomics) -->
            @if($isEnrolledOnCard)
                <a 
                    href="{{ route('courses.show', $course) }}" 
                    class="w-full py-3 sm:py-3.5 min-h-[46px] px-4 rounded-xl text-xs sm:text-sm font-black tracking-wider uppercase bg-[#063b96] hover:bg-[#082b68] text-white flex items-center justify-center gap-2 transition-all text-center shadow-sm"
                >
                    <span>Continue Learning</span>
                    <i class="fas fa-arrow-right text-[10px] sm:text-xs"></i>
                </a>
            @elseif($course->isRegistrationOpen())
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
                    class="btn-ipdp-primary w-full {{ $compact ? 'py-2 sm:py-3 min-h-[38px] sm:min-h-[46px] px-2.5 sm:px-4 text-[11px] sm:text-sm' : 'py-3 sm:py-3.5 min-h-[46px] px-4 text-xs sm:text-sm' }} rounded-xl font-black tracking-wider uppercase shadow-[0_4px_16px_rgba(196,4,10,0.34)] hover:shadow-[0_8px_24px_rgba(196,4,10,0.48)] flex items-center justify-center gap-1.5 sm:gap-2 cursor-pointer group/btn transition-all duration-200 hover:scale-[1.02] active:scale-95 text-center"
                >
                    <span>Enroll Now</span>
                    <i class="fas fa-arrow-right text-[10px] sm:text-xs group-hover/btn:translate-x-1 transition-transform"></i>
                </a>
            @else
                <a 
                    href="{{ route('courses.show', $course) }}" 
                    class="w-full py-3 sm:py-3.5 min-h-[46px] px-4 rounded-xl text-xs sm:text-sm font-black tracking-wider uppercase bg-slate-200 text-slate-600 hover:bg-slate-300 flex items-center justify-center gap-2 transition-all text-center"
                >
                    <i class="fas fa-lock text-[10px]"></i>
                    <span>Registration Closed</span>
                </a>
            @endif
        </div>
    </div>
</article>

