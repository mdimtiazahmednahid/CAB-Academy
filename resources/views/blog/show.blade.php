@extends(auth()->check() && !auth()->user()->isAdmin() ? 'layouts.student' : 'layouts.public')
@php
    $siteName = \App\Models\Setting::getVal('site_name', 'ACS Academy');
@endphp

@section('title', $post->title)
@section('header_title', 'Article & Insights')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($post->meta_description ?: $post->excerpt ?: $post->content), 160))
@section('canonical_url', route('blog.show', $post->slug))
@section('og_image', $post->featured_image_url)
@section('og_title', $post->stylish_og_title)
@section('og_type', 'article')

@section('schema')
@php
    $blogSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'headline' => $post->title,
        'description' => \Illuminate\Support\Str::limit(strip_tags($post->excerpt ?: $post->content), 200),
        'image' => $post->featured_image_url,
        'datePublished' => $post->published_at ? $post->published_at->toISOString() : $post->created_at->toISOString(),
        'dateModified' => $post->updated_at ? $post->updated_at->toISOString() : now()->toISOString(),
        'author' => [
            '@type' => 'Person',
            'name' => $post->author_name ?: ($siteName . ' Editorial Team'),
            'jobTitle' => $post->author_title ?: 'Faculty & Industry Specialist',
        ],
        'publisher' => [
            '@type' => 'EducationalOrganization',
            'name' => $siteName,
            'logo' => [
                '@type' => 'ImageObject',
                'url' => \App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png'),
            ],
        ],
        'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id' => route('blog.show', $post->slug),
        ],
    ];

    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Home',
                'item' => url('/'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => 'Blog & Insights',
                'item' => route('blog.index'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $post->title,
                'item' => route('blog.show', $post->slug),
            ],
        ],
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($blogSchema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
<script type="application/ld+json">
{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endsection

@section('content')
<div class="bg-[#f2f4f7] py-10">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-xs text-slate-500 font-semibold mb-6">
            <a href="{{ url('/') }}" class="hover:text-[#c30102] transition-colors">Home</a>
            <span>/</span>
            <a href="{{ route('blog.index') }}" class="hover:text-[#c30102] transition-colors">Blog & Insights</a>
            <span>/</span>
            <span class="text-[#0f131f] truncate max-w-md font-bold">{{ $post->title }}</span>
        </nav>

        <!-- Article Card Container -->
        <article class="liquid-glass-card rounded-3xl overflow-hidden p-6 sm:p-10 mb-12">
            
            <!-- Category & Metadata Header -->
            <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="bg-[#c30102]/10 text-[#c30102] text-xs font-black uppercase tracking-wider px-3.5 py-1.5 rounded-full">
                        {{ $post->category }}
                    </span>
                    <span class="text-xs text-slate-500 font-semibold flex items-center gap-1.5">
                        <i class="far fa-calendar-alt text-[#c30102]"></i>
                        {{ $post->published_at ? $post->published_at->format('M d, Y') : 'Recent' }}
                    </span>
                    <span class="text-xs text-slate-500 font-semibold flex items-center gap-1.5">
                        <i class="far fa-clock text-[#10b981]"></i>
                        {{ $post->read_time }}
                    </span>
                    @if($post->views_count > 0)
                        <span class="text-xs text-slate-400 font-medium flex items-center gap-1">
                            <i class="far fa-eye"></i> {{ number_format($post->views_count) }} views
                        </span>
                    @endif
                </div>

                @if(auth()->check() && auth()->user()->role === 'admin')
                    <a href="{{ route('admin.blogs.edit', $post) }}" class="px-3 py-1.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-[#c30102] transition-colors inline-flex items-center gap-1.5 shadow-sm">
                        <i class="fas fa-pen text-[10px]"></i> Edit Article
                    </a>
                @endif
            </div>

            <!-- Title -->
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-[#0f131f] tracking-tight leading-tight mb-6">
                {{ $post->title }}
            </h1>

            <!-- Author Card -->
            <div class="flex items-center gap-4 py-4 px-5 rounded-2xl bg-[#f8fafc] border border-slate-100 mb-8">
                <img 
                    src="{{ storage_asset($post->author_avatar) ?: 'https://ui-avatars.com/api/?name=' . urlencode($post->author_name) . '&background=051c45&color=fff' }}" 
                    alt="{{ $post->author_name }}" 
                    class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-sm"
                    onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($post->author_name) }}&background=051c45&color=fff';"
                >
                <div>
                    <span class="text-sm font-bold text-[#0f131f] block">{{ $post->author_name }}</span>
                    <span class="text-xs text-slate-500 block">{{ $post->author_title }} &bull; ACS Academy Faculty</span>
                </div>
            </div>

            <!-- Hero Image -->
            @if($post->featured_image)
                <div class="rounded-2xl overflow-hidden mb-8 shadow-md max-h-[460px]">
                    <img src="{{ $post->featured_image_url }}" alt="{{ $post->title }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=800&q=80';">
                </div>
            @endif

            <!-- Excerpt Callout -->
            @if($post->excerpt)
                <div class="p-5 rounded-2xl bg-emerald-50/60 border-l-4 border-emerald-500 mb-8 text-[#051c45] font-medium text-sm sm:text-base leading-relaxed">
                    {{ $post->excerpt }}
                </div>
            @endif

            <!-- Body Content -->
            <div class="prose max-w-none text-sm sm:text-base text-[#35415b] leading-relaxed space-y-4">
                {!! nl2br(e($post->content)) !!}
            </div>

            <!-- Article Footer CTA -->
            <div class="mt-12 pt-8 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Share:</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-[#c30102] hover:text-white transition-colors" title="Share on Facebook">
                        <i class="fab fa-facebook-f text-xs"></i>
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->fullUrl()) }}" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-[#051c45] hover:text-white transition-colors" title="Share on LinkedIn">
                        <i class="fab fa-linkedin-in text-xs"></i>
                    </a>
                    <a href="https://api.whatsapp.com/send?text={{ urlencode($post->title . ' ' . request()->fullUrl()) }}" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-[#25D366] hover:text-white transition-colors" title="Share on WhatsApp">
                        <i class="fab fa-whatsapp text-xs"></i>
                    </a>
                </div>

                <a href="{{ route('catalog.index') }}" class="btn-ipdp-primary px-6 py-3 rounded-xl text-xs font-bold shadow-md">
                    Explore Related Courses
                </a>
            </div>

        </article>

        <!-- Related Articles Section -->
        @if(isset($relatedPosts) && $relatedPosts->count() > 0)
            <div class="mt-12">
                <h3 class="text-xl font-bold text-[#0f131f] mb-6">More from ACS Academy Faculty</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($relatedPosts as $rel)
                        <div class="bg-white p-5 rounded-3xl border border-[#e3e7f0] shadow-sm hover:shadow-lg transition-all flex flex-col justify-between group">
                            <div>
                                <span class="text-[11px] font-black text-[#c30102] uppercase tracking-wider block mb-2">{{ $rel->category }}</span>
                                <h4 class="text-sm font-bold text-[#0f131f] leading-snug mb-2 group-hover:text-[#c30102] transition-colors">
                                    <a href="{{ route('blog.show', $rel) }}">{{ $rel->title }}</a>
                                </h4>
                            </div>
                            <span class="text-xs text-slate-500 font-medium pt-3 border-t border-slate-100 flex items-center justify-between">
                                <span>{{ $rel->read_time }}</span>
                                <a href="{{ route('blog.show', $rel) }}" class="text-[#c30102] font-bold text-xs hover:underline">Read &rarr;</a>
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
