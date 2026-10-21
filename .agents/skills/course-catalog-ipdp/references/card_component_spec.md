# Course Card Component Specification (Exact IPDP Pattern)

This document provides the exact Blade and Tailwind markup extracted from `ipdpbd.com` for the signature course card.

---

## 1. Exact CSS Coordinates & Layout

| Element | IPDP CSS Rule | Position / Styling |
| :--- | :--- | :--- |
| **Card Container** | `.w-grid-item-h` | White background, `border-radius: 1rem; box-shadow: 0 0.1rem 0.2rem rgba(0,0,0,0.1), 0 0.33rem 1rem rgba(0,0,0,0.1);` |
| **Thumbnail** | `aspect-square` | 1:1 square ratio with hover scale effect (`scale-105`) |
| **Price Tag** | `.usg_product_field_3` | **Bottom-Left of Thumbnail**: `position: absolute; left: 0; bottom: 0; z-index: 10; background: rgba(0,0,0,0.7); padding: 10px 20px; font-weight: 700; color: #fff;` |
| **Instructor Avatar** | `.usg_html_1 .couse-grid-teacher` | **Bottom-Right of Thumbnail**: `position: absolute; bottom: -40px; right: 10px; z-index: 20;` with `width: 80px; height: 80px; border-radius: 100%; border: 4px solid #fff;` |
| **Category Pill** | `.usg_post_taxonomy_1` | `font-size: 0.8rem; text-transform: uppercase; color: #727f9f;` |
| **Course Title** | `.usg_post_title_1` | `font-size: 1rem; font-weight: 700; color: #0f131f; line-height: 1.35; line-clamp-2;` |
| **Course Excerpt** | `.course_short_details` | `font-size: 0.8rem; line-height: 1.25rem; color: #35415b; line-clamp-2;` |
| **Meta Bar** | `.usg_hwrapper_1` | `border-top: 1px solid #e3e7f0; padding-top: 10px; margin-top: 20px;` |
| **Starting Date** | `.course_starting_date` | Clock icon `<i class="far fa-clock"></i>` + `23 October, 2026` (`font-size: 0.8rem; color: #727f9f;`) |
| **Enroll Button** | `.usg_add_to_cart_1` | `background: #f15a29; color: #fff; padding: 6px 16px; border-radius: 4px; font-weight: 600; font-size: 0.8rem;` (Hover: `#90c642`) |

---

## 2. Standard Blade Component Markup

```html
<article class="group bg-white rounded-2xl shadow-[0_0.1rem_0.2rem_rgba(0,0,0,0.08),0_0.33rem_1rem_rgba(0,0,0,0.08)] hover:shadow-[0_0.2rem_0.4rem_rgba(0,0,0,0.12),0_0.67rem_2rem_rgba(0,0,0,0.15)] transition-all duration-300 flex flex-col h-full border border-[#e3e7f0]/60 overflow-hidden">
    <!-- 1:1 Aspect Ratio Thumbnail -->
    <div class="relative aspect-square w-full bg-gray-100 overflow-visible">
        <a href="{{ route('courses.show', $course) }}" class="block w-full h-full overflow-hidden">
            <img 
                src="{{ asset($course->cover_image ?? $course->thumbnail) }}" 
                alt="{{ $course->title }}" 
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            />
        </a>

        <!-- Bottom-Left Semi-Transparent Black Price Overlay -->
        <div class="absolute left-0 bottom-0 z-10 bg-black/70 text-white font-bold text-sm sm:text-base px-4 py-2 pointer-events-none">
            {{ number_format($course->price, 2) }} ৳
        </div>

        <!-- Bottom-Right Floating 80x80 Circular Teacher Avatar -->
        @if($course->instructor)
            <div class="absolute -bottom-10 right-3 z-20">
                <a href="{{ route('courses.show', $course) }}">
                    <div class="w-20 h-20 rounded-full border-4 border-white shadow-md overflow-hidden bg-white">
                        <img 
                            src="{{ asset($course->instructor->profile_picture) }}" 
                            alt="{{ $course->instructor->name }}" 
                            class="w-full h-full object-cover object-top"
                        />
                    </div>
                </a>
            </div>
        @endif
    </div>

    <!-- Body Content -->
    <div class="p-5 pt-8 flex-1 flex flex-col bg-white">
        <a href="{{ route('catalog.category', Str::slug($course->category)) }}" class="text-[12px] uppercase text-[#727f9f] font-semibold tracking-wider hover:text-[#f15a29] transition-colors mb-2">
            {{ $course->category }}
        </a>

        <h3 class="font-bold text-[#0f131f] text-base leading-snug line-clamp-2 mb-2 group-hover:text-[#f15a29] transition-colors">
            <a href="{{ route('courses.show', $course) }}">
                {{ $course->title }}
            </a>
        </h3>

        <p class="text-[#35415b] text-xs leading-relaxed line-clamp-2 mb-4 flex-1">
            {{ $course->short_description }}
        </p>

        <!-- Footer Meta -->
        <div class="border-t border-[#e3e7f0] pt-3 mt-auto flex items-center justify-between gap-2">
            <div class="flex items-center text-xs text-[#727f9f] font-medium gap-1.5 truncate">
                <i class="far fa-clock text-[#727f9f]"></i>
                <span>{{ $course->start_date?->format('d F, Y') ?? $course->duration }}</span>
            </div>
            <a href="{{ route('courses.show', $course) }}" class="btn-ipdp-primary text-xs font-semibold px-4 py-1.5 rounded shadow-sm">
                Enroll Now
            </a>
        </div>
    </div>
</article>
```
