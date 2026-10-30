@props([
    'preview' => null,
    'url' => null,
    'compact' => false,
    'showPostHeader' => false,
])

@php
    $data = is_array($preview) ? $preview : ($url ? \App\Support\LinkPreviewResolver::resolve($url) : null);
@endphp

@if(is_array($data) && !empty($data['url']))
    @php
        $targetUrl = $data['url'];
        $domain = $data['domain'] ?? strtoupper(parse_url($targetUrl, PHP_URL_HOST) ?: 'WEBSITE');
        $displayDomain = $data['display_domain'] ?? strtolower(preg_replace('/^www\./i', '', $domain));
        $title = $data['title'] ?? $domain;
        $stylishTitle = $data['stylish_title'] ?? \App\Support\StylishTextFormatter::toBoldSerif($title);
        $postHeader = $data['post_header'] ?? \App\Support\StylishTextFormatter::toUpperBoldSerif($title);
        $description = $data['description'] ?? '';
        $image = $data['image'] ?? null;
        $badge = $data['badge'] ?? null;
    @endphp

    <a href="{{ $targetUrl }}"
       target="_blank"
       rel="noopener noreferrer"
       aria-label="{{ $title }}"
       data-domain="{{ $domain }}"
       x-data="{ imgError: {{ empty($image) ? 'true' : 'false' }} }"
       class="fb-link-preview-card group block w-full rounded-2xl overflow-hidden border border-[#dadde1] bg-white hover:bg-[#f7f8fa] transition-all duration-200 shadow-2xs hover:shadow-md text-left no-underline focus:outline-none focus:ring-2 focus:ring-[#051c45]/30">

        @if($showPostHeader && !empty($postHeader))
            <!-- Facebook-Style Bold Serif Post Caption Header -->
            <div class="px-4 py-3 bg-white border-b border-[#ebedf0] text-[13px] sm:text-sm font-serif font-bold text-[#050505] tracking-wide leading-snug">
                {{ $postHeader }}
            </div>
        @endif

        @if(!empty($image) && !$compact)
            <!-- Top Course Photo / Thumbnail with Facebook-Style Top-Right (i) Info Badge -->
            <div x-show="!imgError" class="relative w-full bg-white overflow-hidden border-b border-[#dadde1]">
                <img src="{{ $image }}"
                     alt="{{ $title }}"
                     loading="lazy"
                     x-on:error="imgError = true"
                     class="w-full h-auto max-h-[440px] object-cover group-hover:scale-[1.01] transition-transform duration-300">

                <!-- Facebook-style (i) info pill in top-right corner -->
                <span class="absolute top-3 right-3 w-7 h-7 rounded-full bg-white/95 text-[#1c1e21] border border-black/10 shadow-md flex items-center justify-center font-serif italic font-bold text-xs select-none" title="{{ $domain }}">
                    i
                </span>
            </div>
        @endif

        <!-- Fallback Compact Banner when URL has no Open Graph image or image fails to load -->
        <div x-show="imgError || {{ $compact ? 'true' : 'false' }}"
             style="{{ (!empty($image) && !$compact) ? 'display: none;' : '' }}"
             class="flex items-center gap-3.5 px-4 pt-3.5 pb-1 bg-[#f8f9fa]">
            <div class="w-11 h-11 rounded-xl bg-white border border-slate-200/90 flex items-center justify-center text-[#051c45] shadow-2xs shrink-0 group-hover:border-[#c30102]/40 transition-colors">
                @if(!empty($image))
                    <img src="{{ $image }}" alt="{{ $domain }}" class="w-7 h-7 object-contain rounded" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
                    <i class="fas fa-globe text-base text-[#051c45]" style="display:none;"></i>
                @else
                    <i class="fas fa-globe text-base text-[#051c45]"></i>
                @endif
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-xs font-medium text-[#65676b] truncate flex items-center gap-1.5">
                    <span>{{ $displayDomain }}</span>
                    <i class="fas fa-arrow-up-right-from-square text-[9px] opacity-60"></i>
                </div>
                <div class="text-[11px] text-slate-400 truncate">{{ $targetUrl }}</div>
            </div>
        </div>

        <!-- Bottom Metadata Bar (Lowercase Domain + Stylish Bold Serif Headline) -->
        <div class="px-4 py-3 bg-[#f2f3f5]/60 group-hover:bg-[#ebedf0]/70 transition-colors border-t border-[#dadde1]">
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs sm:text-[13px] font-normal text-[#65676b] truncate">
                    {{ $displayDomain }}
                </span>
            </div>

            <h4 class="text-[15px] sm:text-base font-serif font-bold text-[#050505] group-hover:text-[#c30102] transition-colors line-clamp-2 leading-snug mt-0.5 tracking-tight">
                {{ $stylishTitle }}
                <span class="sr-only">{{ $title }}</span>
            </h4>

            @if(!empty($description) && (empty($image) || $compact))
                <p class="text-xs text-[#65676b] line-clamp-2 mt-1 leading-relaxed font-normal">
                    {{ $description }}
                </p>
            @endif
        </div>
    </a>
@endif
