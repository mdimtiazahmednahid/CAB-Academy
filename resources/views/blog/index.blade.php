@extends('layouts.public')

@section('title', 'Insights & Knowledge Hub')

@section('content')
<div>

    <!-- Hero Banner (Modern Imperial Navy Masthead) -->
    <section class="bg-gradient-to-r from-[#031330] via-[#051c45] to-[#0a2540] text-white py-14 sm:py-18 border-b border-[#1e2d4a] relative overflow-hidden">
        <!-- Ambient Glow -->
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-[#c30102]/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-[#10b981]/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-[#10b981] text-xs font-black uppercase tracking-wider mb-4 shadow-sm">
                <i class="fas fa-book-reader text-xs"></i>
                <span>Executive Research & Regulatory Gazette</span>
            </div>

            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white mb-3">
                Insights & Knowledge Hub
            </h1>
            <p class="text-xs sm:text-base text-slate-300 max-w-2xl mx-auto leading-relaxed">
                Industry-standard research, regulatory frameworks, commercial guidelines, and executive insights curated by CAB Academy faculty.
            </p>
            <div class="flex items-center justify-center gap-2 text-xs sm:text-sm text-slate-300 font-medium mt-5">
                <a href="{{ url('/') }}" class="hover:text-white transition-colors">Home</a>
                <span class="text-slate-500">/</span>
                <span class="font-bold text-white underline">Blog & Insights</span>
            </div>

            @if(auth()->check() && auth()->user()->role === 'admin')
                <div class="mt-6 flex justify-center">
                    <a href="{{ route('admin.blogs.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold border border-white/20 backdrop-blur-md transition-all shadow-sm">
                        <i class="fas fa-edit text-[#10b981]"></i>
                        <span>Admin Knowledge Hub Manager</span>
                        <span class="px-2 py-0.5 rounded-full bg-[#c30102] text-[10px] font-mono">{{ \App\Models\BlogPost::count() }} Total</span>
                    </a>
                </div>
            @endif
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-16 bg-[#f8fafc]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Category Filtering & Search Bar -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-10 pb-4 border-b border-[#e3e7f0]">
                <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto pb-2 sm:pb-0 scrollbar-none">
                    <a 
                        href="{{ route('blog.index') }}" 
                        class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all {{ !request('category') ? 'bg-[#c30102] text-white shadow-md shadow-[#c30102]/20' : 'bg-white text-[#35415b] hover:bg-slate-100 border border-slate-200' }}"
                    >
                        All Insights
                    </a>
                    @foreach($categories as $cat)
                        <a 
                            href="{{ route('blog.index', ['category' => $cat]) }}" 
                            class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all {{ request('category') === $cat ? 'bg-[#c30102] text-white shadow-md shadow-[#c30102]/20' : 'bg-white text-[#35415b] hover:bg-slate-100 border border-slate-200' }}"
                        >
                            {{ $cat }}
                        </a>
                    @endforeach
                </div>

                <form method="GET" action="{{ route('blog.index') }}" class="w-full sm:w-72">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <div class="relative">
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}" 
                            placeholder="Search articles..." 
                            class="w-full bg-white border border-slate-200 text-[#0f131f] text-xs rounded-xl py-2.5 pl-3.5 pr-10 focus:ring-2 focus:ring-[#c30102] focus:border-[#c30102] shadow-sm font-medium"
                        >
                        <button type="submit" class="absolute right-3 top-3 text-slate-400 hover:text-[#c30102]">
                            <i class="fas fa-search text-xs"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Articles Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($posts as $post)
                    <article class="bg-white rounded-3xl border border-[#e3e7f0] overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group hover:border-[#c30102]/30">
                        <div>
                            <!-- Featured Image -->
                            <div class="relative aspect-video overflow-hidden bg-gray-100">
                                @php
                                    $blogImg = $post->featured_image_url;
                                @endphp
                                <img 
                                    src="{{ $blogImg }}" 
                                    alt="{{ $post->title }}" 
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                >
                                <span class="absolute top-3 left-3 bg-[#c30102] text-white text-[11px] font-black uppercase tracking-wider px-3 py-1 rounded-full shadow-md">
                                    {{ $post->category }}
                                </span>
                            </div>

                            <!-- Post Body -->
                            <div class="p-6">
                                <div class="flex items-center gap-3 text-xs text-slate-500 font-semibold mb-2.5">
                                    <span><i class="far fa-calendar-alt text-[#c30102] mr-1"></i> {{ $post->published_at ? $post->published_at->format('M d, Y') : 'Recent' }}</span>
                                    <span>&bull;</span>
                                    <span><i class="far fa-clock text-[#10b981] mr-1"></i> {{ $post->read_time }}</span>
                                </div>

                                <h3 class="text-base sm:text-lg font-bold text-[#0f131f] group-hover:text-[#c30102] transition-colors leading-snug mb-3">
                                    <a href="{{ route('blog.show', $post) }}">
                                        {{ $post->title }}
                                    </a>
                                </h3>

                                <p class="text-xs sm:text-sm text-[#727f9f] line-clamp-3 leading-relaxed mb-4">
                                    {{ $post->excerpt }}
                                </p>
                            </div>
                        </div>

                        <!-- Author & Read CTA -->
                        <div class="px-6 py-4 border-t border-slate-100 bg-[#f8fafc] flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <img 
                                    src="{{ $post->author_avatar ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80' }}" 
                                    alt="{{ $post->author_name }}" 
                                    class="w-8 h-8 rounded-full object-cover border border-gray-200"
                                >
                                <div class="leading-tight">
                                    <span class="text-xs font-bold text-[#0f131f] block">{{ $post->author_name }}</span>
                                    <span class="text-[10px] text-[#727f9f] block">{{ $post->author_title }}</span>
                                </div>
                            </div>

                            <a href="{{ route('blog.show', $post) }}" class="text-xs font-bold text-[#c30102] hover:text-[#051c45] transition-colors inline-flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                                <span>Read</span>
                                <i class="fas fa-chevron-right text-[10px]"></i>
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-dashed border-[#e3e7f0] shadow-sm">
                        <i class="fas fa-newspaper text-4xl text-[#727f9f] mb-3"></i>
                        <h3 class="text-lg font-bold text-[#0f131f] mb-1">No Articles Found</h3>
                        <p class="text-xs text-[#727f9f] mb-4">Check back soon for latest publications from CAB Academy faculty.</p>
                        <a href="{{ route('blog.index') }}" class="btn-ipdp-primary px-5 py-2.5 rounded-xl text-xs font-bold inline-block shadow-sm">
                            Reset Filters
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="pt-8 flex justify-center">
                {{ $posts->links() }}
            </div>

        </div>
    </section>

</div>
@endsection
