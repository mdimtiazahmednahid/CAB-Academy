@extends(auth()->check() ? 'layouts.student' : 'layouts.public')

@section('title', 'Community Feed & Blogs')
@section('header_title', 'Feed')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-12">

    <!-- 1. Header Banner -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-[#e3e7f0] shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-[#c30102]/10 text-[#c30102] text-xs font-black uppercase tracking-wider mb-2">
                <i class="fas fa-users"></i>
                <span>Open Community & Knowledge Hub</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-[#0f131f] tracking-tight">
                Community Feed
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 font-medium max-w-xl leading-relaxed">
                Connect with fellow learners, share import-export updates, ask customs questions, and explore faculty research papers.
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            @auth
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

    <!-- 2. Facebook-Style Post Composer (Anyone Can Write Anything) -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-[#e3e7f0] shadow-sm" x-data="{ expanded: false, showImageInput: false }">
        <form method="POST" action="{{ route('feed.posts.store') }}">
            @csrf

            <div class="flex items-start gap-3.5 mb-3">
                <!-- User Avatar -->
                <div class="w-11 h-11 rounded-full bg-[#f2f4f7] border border-gray-200 overflow-hidden shrink-0 flex items-center justify-center font-bold text-sm text-[#051c45]">
                    @auth
                        @if(auth()->user()->profile_picture)
                            <img src="{{ asset(auth()->user()->profile_picture) }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                        @else
                            {{ substr(auth()->user()->name, 0, 1) }}
                        @endif
                    @else
                        <i class="fas fa-user text-gray-400"></i>
                    @endauth
                </div>

                <!-- Textarea -->
                <div class="flex-1">
                    <textarea 
                        name="content" 
                        rows="2" 
                        @focus="expanded = true"
                        placeholder="What's on your mind? Share an industry update, ask a trade question, or post a discussion..." 
                        class="w-full bg-[#f8fafc] border border-slate-200 rounded-2xl p-3.5 text-sm text-[#0f131f] placeholder:text-[#727f9f] focus:outline-none focus:ring-2 focus:ring-[#c30102] focus:bg-white transition-all resize-none"
                        required
                    ></textarea>
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
                            @if($post->author_avatar)
                                <img src="{{ asset($post->author_avatar) }}" alt="{{ $post->author_name }}" class="w-full h-full object-cover">
                            @else
                                {{ substr($post->author_name, 0, 1) }}
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
                    @if($post->title)
                        <h4 class="text-base sm:text-lg font-bold text-[#0f131f] leading-snug">
                            {{ $post->title }}
                        </h4>
                    @endif

                    <div class="text-[#35415b] text-sm leading-relaxed whitespace-pre-line">
                        {!! nl2br(e($post->content)) !!}
                    </div>

                    <!-- Embedded Blog Card (If Linked to an Official BlogPost) -->
                    @if($post->blogPost)
                        <div class="mt-4 p-4 rounded-2xl bg-[#f8fafc] border border-gray-200 flex flex-col sm:flex-row items-center gap-4">
                            @if($post->blogPost->featured_image)
                                <img src="{{ $post->blogPost->featured_image }}" alt="{{ $post->blogPost->title }}" class="w-full sm:w-40 h-28 rounded-xl object-cover">
                            @endif
                            <div class="flex-1">
                                <span class="text-[11px] font-bold uppercase text-[#c30102] tracking-wider block mb-1">
                                    {{ $post->blogPost->category }}
                                </span>
                                <h5 class="font-bold text-sm text-[#0f131f] mb-1 leading-snug">
                                    {{ $post->blogPost->title }}
                                </h5>
                                <a href="{{ route('blog.show', $post->blogPost->slug) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#c30102] hover:text-[#051c45] transition-colors mt-2">
                                    <span>Read Full Article</span>
                                    <i class="fas fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    @elseif($post->image_url)
                        <div class="mt-3 rounded-2xl overflow-hidden max-h-[420px] bg-gray-100 border border-gray-100">
                            <img src="{{ $post->image_url }}" alt="Post image" class="w-full h-full object-cover">
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
                                    @if($comment->author_avatar)
                                        <img src="{{ asset($comment->author_avatar) }}" alt="{{ $comment->author_name }}" class="w-full h-full object-cover">
                                    @else
                                        {{ substr($comment->author_name, 0, 1) }}
                                    @endif
                                </div>
                                <div class="bg-[#f8fafc] border border-gray-200/80 rounded-2xl px-4 py-2.5 flex-1">
                                    <div class="flex items-center justify-between gap-2 mb-1">
                                        <span class="text-xs font-bold text-[#0f131f]">{{ $comment->author_name }}</span>
                                        <span class="text-[10px] text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-xs text-[#35415b] leading-relaxed">
                                        {{ $comment->content }}
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
                    Be the first to share an industry insight, ask a question, or start a discussion with CAB Academy learners!
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
