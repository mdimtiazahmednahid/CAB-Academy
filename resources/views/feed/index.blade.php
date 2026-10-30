@extends(auth()->check() ? 'layouts.student' : 'layouts.public')

@section('title', 'Community Feed & Blogs')
@section('header_title', 'Feed')
@section('robots', 'noindex, nofollow, noarchive, nosnippet')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-12">

    <!-- 1. Header Banner -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-[#e3e7f0] shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-[#0f131f] tracking-tight">
                Community Feed
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 font-medium max-w-xl leading-relaxed">
                Connect with fellow learners, share import-export updates, ask customs questions, and explore faculty research papers.
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0 flex-wrap">
            @auth
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.feeds.index') }}" class="px-4 py-2 rounded-2xl bg-[#051c45] hover:bg-[#072559] text-xs font-bold text-white flex items-center gap-2 shadow-sm transition-all">
                        <i class="fas fa-shield-alt text-amber-400"></i>
                        <span>Admin Moderation Desk</span>
                    </a>
                @endif
                <div class="px-4 py-2 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs font-bold text-emerald-800 flex items-center gap-2">
                    <span class="text-base">✨</span>
                    <span>Earn +15 XP per post</span>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn-ipdp-primary px-5 py-2.5 rounded-xl text-xs font-bold shadow-sm">
                    Sign In to Earn XP
                </a>
            @endauth
        </div>
    </div>

    <!-- 2. Facebook-Style Post Composer (With Live Rich Link Preview) -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-[#e3e7f0] shadow-sm"
         x-data="{
            expanded: false,
            showImageInput: false,
            postContent: '',
            detectedUrl: '',
            loadingPreview: false,
            livePreview: null,
            previewTimer: null,
            checkUrlInContent() {
                clearTimeout(this.previewTimer);
                const match = (this.postContent || '').match(/https?:\/\/[^\s<>'\x22]+/i);
                const cleanUrl = match ? match[0].replace(/[.,;:!?)\]}'\\x22]+$/, '') : '';
                if (!cleanUrl) {
                    this.detectedUrl = '';
                    this.livePreview = null;
                    return;
                }
                if (cleanUrl === this.detectedUrl && this.livePreview) {
                    return;
                }
                this.detectedUrl = cleanUrl;
                this.previewTimer = setTimeout(() => {
                    this.fetchLinkPreview(cleanUrl);
                }, 320);
            },
            async fetchLinkPreview(url) {
                this.loadingPreview = true;
                try {
                    const res = await fetch('{{ route('link.preview') }}?url=' + encodeURIComponent(url), {
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await res.json();
                    if (data && data.success && data.preview) {
                        this.livePreview = data.preview;
                        // If the user only pasted the raw URL so far, auto-prepend the stylish bold serif caption header
                        if (this.postContent.trim() === url && data.preview.post_header) {
                            this.postContent = data.preview.post_header + '\n\n' + url;
                        }
                    }
                } catch (e) {
                    // Fallback client-side preview if network request fails
                    try {
                        const parsed = new URL(url);
                        const domain = parsed.hostname.replace(/^www\./i, '').toUpperCase();
                        this.livePreview = {
                            url: url,
                            domain: domain,
                            display_domain: domain.toLowerCase(),
                            title: domain,
                            stylish_title: domain,
                            description: url,
                            image: null,
                            has_image: false
                        };
                    } catch (_) {}
                } finally {
                    this.loadingPreview = false;
                }
            },
            stylizeFirstLine() {
                if (!this.postContent) return;
                const toUpperBoldSerif = (str) => {
                    return Array.from(str.toUpperCase()).map(ch => {
                        const code = ch.codePointAt(0);
                        if (code >= 65 && code <= 90) return String.fromCodePoint(0x1D400 + (code - 65));
                        if (code >= 48 && code <= 57) return String.fromCodePoint(0x1D7CE + (code - 48));
                        return ch;
                    }).join('');
                };
                const lines = this.postContent.split('\n');
                if (lines.length > 0 && !/^https?:\/\//i.test(lines[0].trim())) {
                    lines[0] = toUpperBoldSerif(lines[0]);
                    this.postContent = lines.join('\n');
                } else if (this.livePreview && this.livePreview.post_header) {
                    this.postContent = this.livePreview.post_header + '\n\n' + this.postContent;
                }
            },
            insertFullStylishCaption() {
                if (this.livePreview && this.livePreview.social_caption) {
                    this.postContent = this.livePreview.social_caption;
                } else if (this.livePreview && this.livePreview.post_header) {
                    this.postContent = this.livePreview.post_header + '\n\n' + (this.livePreview.description ? this.livePreview.description + '\n\n' : '') + this.livePreview.url;
                }
            }
         }">
        <form method="POST" action="{{ route('feed.posts.store') }}">
            @csrf

            <div class="flex items-start gap-3.5 mb-3">
                <!-- User Avatar -->
                <div class="w-11 h-11 rounded-full bg-[#f2f4f7] border border-gray-200 overflow-hidden shrink-0 flex items-center justify-center font-bold text-sm text-[#051c45]">
                    @auth
                        @if(auth()->user()->hasCustomProfilePicture())
                            <img src="{{ auth()->user()->profile_picture_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=051c45&color=fff';">
                        @else
                            {{ substr(auth()->user()->name, 0, 1) }}
                        @endif
                    @else
                        <i class="fas fa-user text-gray-400"></i>
                    @endauth
                </div>

                <!-- Textarea -->
                <div class="flex-1 space-y-3">
                    <textarea 
                        name="content" 
                        rows="2" 
                        x-model="postContent"
                        @focus="expanded = true"
                        @input="expanded = true; checkUrlInContent()"
                        @paste="setTimeout(() => { expanded = true; checkUrlInContent(); }, 50)"
                        placeholder="What's on your mind? Paste any link (e.g. https://acsacademy.bd/c/18) or share an industry update..." 
                        class="w-full bg-[#f8fafc] border border-slate-200 rounded-2xl p-3.5 text-sm text-[#0f131f] placeholder:text-[#727f9f] focus:outline-none focus:ring-2 focus:ring-[#c30102] focus:bg-white transition-all resize-none"
                        required
                    ></textarea>

                    <!-- Live Loading Indicator for Link Preview -->
                    <div x-show="loadingPreview" x-cloak class="p-3.5 rounded-2xl bg-[#f0f2f5] border border-[#dadde1] flex items-center gap-3 text-xs text-slate-600 font-semibold animate-pulse">
                        <i class="fas fa-circle-notch fa-spin text-[#051c45]"></i>
                        <span>Generating rich link preview...</span>
                    </div>

                    <!-- Live Facebook-Style Rich Link Preview Card in Composer -->
                    <template x-if="livePreview && !loadingPreview">
                        <div class="space-y-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <button type="button" @click="stylizeFirstLine()" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#051c45]/10 hover:bg-[#051c45] text-[#051c45] hover:text-white text-[11px] font-bold transition-colors">
                                    <i class="fas fa-font text-[10px]"></i>
                                    <span>𝐏𝐆𝐃 Bold Serif Headline</span>
                                </button>
                                <button type="button" x-show="livePreview.social_caption || livePreview.post_header" @click="insertFullStylishCaption()" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#c30102]/10 hover:bg-[#c30102] text-[#c30102] hover:text-white text-[11px] font-bold transition-colors">
                                    <i class="fas fa-wand-magic-sparkles text-[10px]"></i>
                                    <span>Auto-Fill Full Stylish Caption</span>
                                </button>
                            </div>
                            <div class="relative">
                                <a :href="livePreview.url"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="fb-link-preview-card group block w-full rounded-2xl overflow-hidden border border-[#dadde1] bg-white hover:bg-[#f7f8fa] transition-all duration-200 shadow-2xs hover:shadow-md text-left no-underline">
                                    <template x-if="livePreview.post_header">
                                        <div class="px-4 py-3 bg-white border-b border-[#ebedf0] text-[13px] font-serif font-bold text-[#050505] tracking-wide" x-text="livePreview.post_header"></div>
                                    </template>
                                    <template x-if="livePreview.image">
                                        <div class="relative w-full bg-white overflow-hidden border-b border-[#dadde1]">
                                            <img :src="livePreview.image" :alt="livePreview.title" class="w-full h-auto max-h-[380px] object-cover" x-on:error="livePreview.image = null">
                                        </div>
                                    </template>
                                    <div class="px-4 py-3 bg-[#f2f3f5]/80 group-hover:bg-[#ebedf0]/90 transition-colors flex items-center justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <div class="text-xs font-normal text-[#65676b] uppercase tracking-tight truncate" x-text="livePreview.display_domain || livePreview.domain.toLowerCase()"></div>
                                            <h4 class="text-[15px] font-serif font-bold text-[#050505] group-hover:text-[#c30102] underline decoration-[#050505]/40 group-hover:decoration-[#c30102] line-clamp-2 leading-snug mt-0.5" x-text="livePreview.stylish_title || livePreview.title"></h4>
                                            <p x-show="!livePreview.image && livePreview.description" class="text-xs text-[#65676b] line-clamp-2 mt-1 leading-relaxed" x-text="livePreview.description"></p>
                                        </div>
                                        <span class="px-4 py-2 rounded-xl bg-gradient-to-r from-[#c30102] to-[#e11d48] text-white font-extrabold text-xs sm:text-[13px] shrink-0 transition-all shadow-[0_0_18px_rgba(195,1,2,0.55)] group-hover:scale-105 inline-flex items-center gap-1.5 select-none">
                                            <i class="fas fa-bolt text-amber-300 text-[11px] animate-pulse"></i>
                                            <span>Enroll Now</span>
                                        </span>
                                    </div>
                                </a>
                                <button type="button" @click="livePreview = null" title="Dismiss preview" class="absolute top-2.5 right-2.5 w-7 h-7 rounded-full bg-slate-900/75 hover:bg-slate-900 text-white text-xs flex items-center justify-center shadow-sm">
                                    &times;
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Guest Name & Role Inputs (If not logged in, anyone can still write!) -->
            @guest
                <div x-show="expanded" class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3 pt-2 border-t border-gray-100" style="display: none;">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Your Name</label>
                        <input 
                            type="text" 
                            name="author_name" 
                            placeholder="e.g. Tanvir Ahmed" 
                            class="w-full bg-[#f8fafc] border border-gray-200 rounded-xl px-3 py-2 text-xs focus:ring-1 focus:ring-[#c30102] focus:outline-none"
                            required
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Your Role / Enterprise</label>
                        <input 
                            type="text" 
                            name="author_title" 
                            placeholder="e.g. Commercial Executive, Dhaka" 
                            class="w-full bg-[#f8fafc] border border-gray-200 rounded-xl px-3 py-2 text-xs focus:ring-1 focus:ring-[#c30102] focus:outline-none"
                        >
                    </div>
                </div>
            @endguest

            <!-- Expanded Composer Controls -->
            <div x-show="expanded" class="pt-3 border-t border-gray-100 space-y-3" style="display: none;">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <!-- Category Select -->
                    <div class="flex items-center gap-2">
                        <label class="text-xs font-bold text-[#727f9f]">Topic:</label>
                        <select name="category" class="bg-[#f8fafc] border border-gray-200 rounded-xl text-xs px-3 py-1.5 text-[#35415b] font-medium focus:outline-none focus:ring-1 focus:ring-[#c30102]">
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <!-- Bold Serif Stylizer Button -->
                        <button 
                            type="button" 
                            @click="stylizeFirstLine()" 
                            class="text-xs font-bold text-[#051c45] hover:text-[#c30102] flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 transition-colors"
                            title="Convert first line to Stylish Bold Serif (𝐏𝐆𝐃 𝐈𝐍...)"
                        >
                            <span class="font-serif">𝐀𝐁𝐂</span>
                            <span>Stylish Headline</span>
                        </button>

                        <!-- Image Attachment Toggle -->
                        <button 
                            type="button" 
                            @click="showImageInput = !showImageInput" 
                            class="text-xs font-semibold text-gray-600 hover:text-[#c30102] flex items-center gap-1.5 px-3 py-1.5 rounded-lg hover:bg-gray-100 transition-colors"
                        >
                            <i class="far fa-image text-sm text-[#10b981]"></i>
                            <span>Add Photo / URL</span>
                        </button>
                    </div>
                </div>

                <!-- Image URL Input -->
                <div x-show="showImageInput" class="transition-all" style="display: none;">
                    <input 
                        type="url" 
                        name="image_url" 
                        placeholder="Paste image URL (e.g. https://images.unsplash.com/...)" 
                        class="w-full bg-[#f8fafc] border border-gray-200 rounded-xl px-3 py-2 text-xs text-[#35415b] focus:ring-1 focus:ring-[#c30102] focus:outline-none"
                    >
                </div>

                <!-- Submit Action Row -->
                <div class="flex items-center justify-between pt-2">
                    <span class="text-[11px] text-gray-500 font-medium">
                        Respect community guidelines &bull; Friendly & professional
                    </span>
                    <div class="flex items-center gap-2">
                        <button 
                            type="button" 
                            @click="expanded = false" 
                            class="px-4 py-2 text-xs font-bold text-gray-500 hover:text-gray-700 transition-colors"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="btn-ipdp-primary px-6 py-2 rounded-xl text-xs font-bold shadow-md flex items-center gap-1.5"
                        >
                            <i class="fas fa-paper-plane text-xs"></i>
                            <span>Publish Post</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- 3. Feed Navigation & Filter Tabs -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <!-- Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto pb-2 sm:pb-0 scrollbar-none">
            <a 
                href="{{ route('feed.index', ['filter' => 'all']) }}" 
                class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all {{ $filter === 'all' ? 'bg-[#051c45] text-white shadow-md' : 'bg-white text-[#35415b] border border-slate-200 hover:bg-slate-50' }}"
            >
                All Posts
            </a>
            <a 
                href="{{ route('feed.index', ['filter' => 'blogs']) }}" 
                class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all {{ $filter === 'blogs' ? 'bg-[#051c45] text-white shadow-md' : 'bg-white text-[#35415b] border border-slate-200 hover:bg-slate-50' }}"
            >
                <i class="fas fa-newspaper text-xs mr-1 text-[#c30102]"></i>
                Official Insights & Blogs
            </a>
            <a 
                href="{{ route('feed.index', ['filter' => 'discussions']) }}" 
                class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all {{ $filter === 'discussions' ? 'bg-[#051c45] text-white shadow-md' : 'bg-white text-[#35415b] border border-slate-200 hover:bg-slate-50' }}"
            >
                <i class="fas fa-comments text-xs mr-1 text-[#10b981]"></i>
                Peer Discussions
            </a>
            @auth
                <a 
                    href="{{ route('feed.index', ['filter' => 'my']) }}" 
                    class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all {{ $filter === 'my' ? 'bg-[#051c45] text-white shadow-md' : 'bg-white text-[#35415b] border border-slate-200 hover:bg-slate-50' }}"
                >
                    My Posts
                </a>
            @endauth
        </div>

        <!-- Search Form -->
        <form method="GET" action="{{ route('feed.index') }}" class="w-full sm:w-64">
            @if($filter !== 'all')
                <input type="hidden" name="filter" value="{{ $filter }}">
            @endif
            <div class="relative">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Search posts or topics..." 
                    class="w-full bg-white border border-[#e3e7f0] text-[#35415b] text-xs rounded-full py-2 pl-4 pr-9 focus:ring-1 focus:ring-[#c30102] focus:outline-none shadow-sm font-medium"
                >
                <button type="submit" class="absolute right-3 top-2.5 text-gray-400 hover:text-[#c30102]">
                    <i class="fas fa-search text-xs"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- 4. Community Feed Stream -->
    <div class="space-y-6">
        @forelse($posts as $post)
            <article class="bg-white rounded-3xl border border-[#e3e7f0] shadow-sm hover:shadow-md transition-all p-5 sm:p-7 flex flex-col justify-between" x-data="{ showComments: false, liked: {{ $post->isLikedBy(auth()->user(), request()->ip()) ? 'true' : 'false' }}, likeCount: {{ $post->likes_count }} }">
                
                <!-- Author Header -->
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div class="flex items-center gap-3">
                        <!-- Avatar -->
                        <div class="w-12 h-12 rounded-full bg-[#051c45]/10 border-2 border-white shadow-sm overflow-hidden shrink-0 flex items-center justify-center font-bold text-base text-[#051c45]">
                            @if($post->author_avatar_url)
                                <img src="{{ $post->author_avatar_url }}" alt="{{ $post->author_name }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr($post->author_name, 0, 1)) }}
                            @endif
                        </div>

                        <!-- Name & Meta -->
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="text-sm font-bold text-[#0f131f] hover:text-[#c30102] transition-colors">
                                    {{ $post->author_name }}
                                </h3>
                                
                                @if($post->author_title)
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ str_contains(strtolower($post->author_title), 'faculty') || str_contains(strtolower($post->author_title), 'director') ? 'bg-[#c30102]/10 text-[#c30102] border border-[#c30102]/20' : 'bg-[#10b981]/10 text-[#10b981] border border-[#10b981]/20' }}">
                                        {{ $post->author_title }}
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center gap-2 text-xs text-[#727f9f] mt-0.5">
                                <span>{{ $post->created_at->diffForHumans() }}</span>
                                <span>&bull;</span>
                                <span class="font-medium text-[#35415b]">{{ $post->category }}</span>
                                @if($post->is_pinned)
                                    <span>&bull;</span>
                                    <span class="text-[#c30102] font-bold flex items-center gap-1">
                                        <i class="fas fa-thumbtack text-[10px]"></i>
                                        <span>Pinned</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Delete button for post author or admin -->
                    @if(auth()->check() && (auth()->id() === $post->user_id || auth()->user()->role === 'admin'))
                        <form method="POST" action="{{ route('feed.posts.destroy', $post) }}" onsubmit="return confirm('Delete this post?');" class="m-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-500 p-1 text-xs transition-colors" title="Delete Post">
                                <i class="far fa-trash-alt"></i>
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Post Body Content -->
                <div class="space-y-3 mb-5">
                    @if($post->stylish_title)
                        <h4 class="text-sm sm:text-base font-serif font-bold text-[#050505] tracking-wide leading-snug">
                            {{ $post->stylish_title }}
                            @if($post->title)
                                <span class="sr-only">{{ $post->title }}</span>
                            @endif
                        </h4>
                    @endif

                    <div class="text-[#35415b] text-sm leading-relaxed whitespace-pre-line break-words">
                        {!! $post->formatted_content_html !!}
                    </div>

                    <!-- Embedded Blog Card (If Linked to an Official BlogPost) -->
                    @if($post->blogPost)
                        <div class="mt-3">
                            <x-link-preview-card :preview="[
                                'url' => route('blog.show', $post->blogPost->slug),
                                'title' => $post->blogPost->title,
                                'stylish_title' => $post->blogPost->stylish_og_title,
                                'post_header' => $post->blogPost->stylish_post_header,
                                'description' => \Illuminate\Support\Str::limit(strip_tags($post->blogPost->meta_description ?: $post->blogPost->excerpt ?: $post->blogPost->content), 160),
                                'image' => $post->blogPost->featured_image_url,
                                'domain' => strtoupper(preg_replace('/^www\./i', '', parse_url(config('app.url'), PHP_URL_HOST) ?: 'acsacademy.bd')),
                                'display_domain' => strtolower(preg_replace('/^www\./i', '', parse_url(config('app.url'), PHP_URL_HOST) ?: 'acsacademy.bd')),
                                'site_name' => \App\Models\Setting::getVal('site_name', 'ACS Academy'),
                                'type' => 'article',
                            ]" />
                        </div>
                    @elseif($post->image_url)
                        <div class="mt-3 rounded-2xl overflow-hidden max-h-[420px] bg-gray-100 border border-gray-100">
                            <img src="{{ $post->image_url }}" alt="Post image" class="w-full h-full object-cover">
                        </div>
                    @endif

                    @if(!$post->blogPost && $post->link_preview)
                        <div class="mt-3">
                            <x-link-preview-card :preview="$post->link_preview" />
                        </div>
                    @endif
                </div>

                <!-- Engagement Action Bar (Like, Comment, Share) -->
                <div class="border-t border-gray-100 pt-3 flex items-center justify-between">
                    <div class="flex items-center gap-6">
                        <!-- Like Button (Interactive with Alpine / AJAX) -->
                        <form 
                            method="POST" 
                            action="{{ route('feed.posts.like', $post) }}"
                            @submit.prevent="
                                liked = !liked;
                                likeCount = liked ? likeCount + 1 : Math.max(0, likeCount - 1);
                                fetch('{{ route('feed.posts.like', $post) }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    }
                                });
                            "
                            class="m-0"
                        >
                            @csrf
                            <button 
                                type="submit" 
                                class="flex items-center gap-2 text-xs font-bold transition-all"
                                :class="liked ? 'text-red-500 scale-105' : 'text-gray-500 hover:text-red-500'"
                            >
                                <i :class="liked ? 'fas fa-heart text-red-500' : 'far fa-heart'"></i>
                                <span x-text="likeCount"></span>
                                <span class="hidden sm:inline" x-text="likeCount === 1 ? 'Like' : 'Likes'"></span>
                            </button>
                        </form>

                        <!-- Comments Toggle -->
                        <button 
                            type="button" 
                            @click="showComments = !showComments"
                            class="flex items-center gap-2 text-xs font-bold text-gray-500 hover:text-[#c30102] transition-colors"
                        >
                            <i class="far fa-comment-dots text-sm"></i>
                            <span>{{ $post->comments_count }}</span>
                            <span class="hidden sm:inline">{{ $post->comments_count === 1 ? 'Comment' : 'Comments' }}</span>
                        </button>
                    </div>

                    <!-- Share Button (Copy Link) -->
                    <button 
                        type="button" 
                        onclick="navigator.clipboard.writeText('{{ url('/feed') }}#post-{{ $post->id }}'); alert('Post link copied to clipboard!');" 
                        class="text-xs font-semibold text-gray-500 hover:text-[#0f131f] flex items-center gap-1.5 transition-colors"
                    >
                        <i class="fas fa-share-alt text-xs"></i>
                        <span class="hidden sm:inline">Share</span>
                    </button>
                </div>

                <!-- 5. Expandable Comment Thread (Facebook-style) -->
                <div x-show="showComments" class="mt-4 pt-4 border-t border-gray-100 space-y-4" style="display: none;">
                    <!-- Existing Comments -->
                    <div class="space-y-3">
                        @foreach($post->comments as $comment)
                            <div class="flex items-start gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-[#051c45]/10 border border-[#051c45]/20 overflow-hidden shrink-0 flex items-center justify-center font-bold text-xs text-[#051c45]">
                                    @if($comment->author_avatar_url)
                                        <img src="{{ $comment->author_avatar_url }}" alt="{{ $comment->author_name }}" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr($comment->author_name, 0, 1)) }}
                                    @endif
                                </div>
                                <div class="bg-[#f8fafc] border border-gray-200/80 rounded-2xl px-4 py-2.5 flex-1">
                                    <div class="flex items-center justify-between gap-2 mb-1">
                                        <span class="text-xs font-bold text-[#0f131f]">{{ $comment->author_name }}</span>
                                        <span class="text-[10px] text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-xs text-[#35415b] leading-relaxed break-words">
                                        {!! $comment->formatted_content_html !!}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Add Comment Form -->
                    <form method="POST" action="{{ route('feed.posts.comments.store', $post) }}" class="flex items-center gap-2 pt-2">
                        @csrf
                        <input 
                            type="text" 
                            name="content" 
                            placeholder="Write a reply..." 
                            class="flex-1 bg-[#f8fafc] border border-gray-200 rounded-full px-4 py-2 text-xs text-[#35415b] placeholder:text-gray-400 focus:outline-none focus:ring-1 focus:ring-[#c30102] focus:bg-white"
                            required
                        >
                        @guest
                            <input type="hidden" name="author_name" value="Community Guest">
                        @endguest
                        <button type="submit" class="btn-ipdp-primary px-4 py-2 rounded-full text-xs font-bold shrink-0">
                            Reply
                        </button>
                    </form>
                </div>

            </article>
        @empty
            <div class="bg-white rounded-3xl p-12 text-center border border-[#e3e7f0] shadow-sm">
                <div class="w-16 h-16 rounded-full bg-[#c30102]/10 flex items-center justify-center mx-auto mb-4 text-[#c30102] text-2xl">
                    <i class="far fa-newspaper"></i>
                </div>
                <h3 class="text-lg font-bold text-[#0f131f] mb-1">No community posts yet</h3>
                <p class="text-xs sm:text-sm text-[#727f9f] max-w-md mx-auto mb-6">
                    Be the first to share an industry insight, ask a question, or start a discussion with ACS Academy learners!
                </p>
            </div>
        @endforelse

        <!-- Pagination -->
        @if($posts->hasPages())
            <div class="pt-4">
                {{ $posts->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
