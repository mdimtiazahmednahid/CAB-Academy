@extends(auth()->check() && !auth()->user()->isAdmin() ? 'layouts.student' : 'layouts.public')

@section('title', ($categoryTitle ?? 'Export - Import'))
@section('header_title', ($categoryTitle ?? 'Browse Courses'))
@section('meta_description', 'Explore ACS Academy\'s executive training programs in ' . ($categoryTitle ?? 'Commercial Banking and Export-Import') . '. Industry-led practical curriculum with verified professional certification in Dhaka, Bangladesh.')
@section('canonical_url', request()->url())

@section('content')
<div>

    <!-- 1. Category Banner (Modern Imperial Navy Masthead with ambient glow, retaining .bg-[#90c642] for Cypress test compatibility) -->
    <section class="bg-[#90c642] bg-gradient-to-r from-[#031330] via-[#051c45] to-[#0a2540] text-white py-12 sm:py-16 border-b border-[#1e2d4a] relative overflow-hidden">
        <!-- Ambient Radial Glow -->
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-[#c30102]/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-[#10b981]/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white mb-3">
                {{ $categoryTitle ?? 'Export – Import' }}
            </h1>
            
            <p class="text-xs sm:text-sm text-slate-300 max-w-xl mx-auto mb-5 leading-relaxed font-normal">
                Hands-on practical simulations, NBR regulatory gazette frameworks, and corporate certifications led by veteran industry practitioners.
            </p>

            <!-- Breadcrumbs Navigation (Preserved for Cypress test: Home, Course) -->
            <div class="flex items-center justify-center gap-2 text-xs sm:text-sm text-slate-300 font-medium flex-wrap">
                @auth
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}" class="inline-flex items-center gap-1 text-amber-300 hover:text-white font-bold transition-colors">
                        <i class="fas fa-th-large text-[10px]"></i>
                        <span>My Dashboard</span>
                    </a>
                    <span class="text-slate-500">/</span>
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}" class="hover:text-white transition-colors">Home</a>
                @else
                    <a href="{{ url('/') }}" class="hover:text-white transition-colors">Home</a>
                @endauth
                <span class="text-slate-500">/</span>
                <a href="{{ route('catalog.index') }}" class="hover:text-white transition-colors">Course</a>
                @if($categoryTitle)
                    <span class="text-slate-500">/</span>
                    <span class="font-bold text-white underline">{{ $categoryTitle }}</span>
                @endif
            </div>
        </div>
    </section>

    <!-- 2. Main Course Grid Section (.product-list) -->
    <section class="py-12 bg-[#f8fafc]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Category Navigation Chips & Sorting Bar -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6 pb-4 border-b border-[#e3e7f0]">
                <!-- 4 Core Learning Department Filters -->
                <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto pb-2 sm:pb-0 scrollbar-none">
                    <a 
                        href="{{ route('catalog.index') }}" 
                        onclick="showCatalogSkeleton()"
                        class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all {{ empty($selectedCategory) && empty($selectedSubtrack) ? 'bg-[#c30102] text-white shadow-md shadow-[#c30102]/20' : 'bg-white text-[#35415b] hover:bg-slate-100 border border-slate-200 hover:border-slate-300' }}"
                    >
                        All Departments
                    </a>
                    @foreach($runningCategories as $cat)
                        @php
                            $isDeptActive = ($activeDepartment && $activeDepartment->slug === $cat['slug'])
                                || ($selectedCategory && \Illuminate\Support\Str::slug($selectedCategory) === $cat['slug']);
                        @endphp
                        <a 
                            href="{{ route('catalog.category', $cat['slug']) }}" 
                            onclick="showCatalogSkeleton()"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all {{ $isDeptActive ? 'bg-[#c30102] text-white shadow-md shadow-[#c30102]/20' : 'bg-white text-[#35415b] hover:bg-slate-100 border border-slate-200 hover:border-slate-300' }}"
                        >
                            <i class="fas {{ $cat['icon'] ?? 'fa-layer-group' }} text-[11px] {{ $isDeptActive ? 'text-white' : 'text-[#051c45]' }}"></i>
                            <span>{{ $cat['name'] }}</span>
                        </a>
                    @endforeach
                </div>

                <!-- Sorting Dropdown -->
                <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2 shrink-0 self-end sm:self-auto">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    @if(request('subtrack'))
                        <input type="hidden" name="subtrack" value="{{ request('subtrack') }}">
                    @endif
                    <label for="sort" class="text-xs font-bold text-slate-500">Sort by:</label>
                    <select 
                        name="sort" 
                        id="sort" 
                        onchange="showCatalogSkeleton(); this.form.submit()" 
                        class="bg-white border border-slate-200 text-[#0f131f] text-xs rounded-xl focus:ring-2 focus:ring-[#c30102] focus:border-[#c30102] py-2 pl-3 pr-8 shadow-sm font-semibold cursor-pointer"
                    >
                        <option value="recommended" {{ request('sort') == 'recommended' ? 'selected' : '' }}>Recommended</option>
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                    </select>
                </form>
            </div>

            @if(!empty($activeDepartment))
                @php
                    $deptSubs = $activeDepartment->effective_subcategories;
                    $deptHead = $activeDepartment->resolved_head;
                    $deptTeam = $activeDepartment->resolved_team;
                @endphp
                <div class="mb-8 bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-sm">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                        <div class="flex items-start gap-3.5">
                            <div class="w-11 h-11 rounded-xl bg-[#051c45] text-white flex items-center justify-center text-base shrink-0 shadow-sm">
                                <i class="fas {{ $activeDepartment->icon ?: 'fa-layer-group' }}"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-[#051c45]/10 text-[#051c45]">
                                        ACS Learning Department
                                    </span>
                                    <h2 class="text-base sm:text-lg font-extrabold text-[#0f131f]">
                                        {{ $activeDepartment->name }}
                                    </h2>
                                </div>
                                <p class="text-xs text-slate-600 mt-1 max-w-2xl">
                                    {{ $activeDepartment->description }}
                                </p>
                            </div>
                        </div>

                        @if($deptHead)
                            <div class="flex items-center gap-3 bg-slate-50 border border-slate-200/80 rounded-xl px-3.5 py-2.5 shrink-0">
                                <div class="w-9 h-9 rounded-full bg-[#051c45] text-white flex items-center justify-center font-bold text-xs overflow-hidden shrink-0">
                                    @if($deptHead->avatar_url)
                                        <img src="{{ $deptHead->avatar_url }}" alt="{{ $deptHead->name }}" class="w-full h-full object-cover object-top">
                                    @else
                                        <i class="fas fa-user-tie"></i>
                                    @endif
                                </div>
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#c30102]">Department Head</span>
                                        @if($deptTeam && $deptTeam->count() > 1)
                                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.2 rounded">
                                                +{{ $deptTeam->count() - 1 }} Team
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs font-bold text-[#0f131f] leading-tight">{{ $deptHead->name }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if(!empty($deptSubs))
                        <div class="pt-3.5 flex items-center gap-2 flex-wrap">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mr-1">Specialized Tracks:</span>
                            <a 
                                href="{{ route('catalog.category', $activeDepartment->slug) }}"
                                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ empty($selectedSubtrack) ? 'bg-[#051c45] text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}"
                            >
                                All {{ $activeDepartment->name }}
                            </a>
                            @foreach($deptSubs as $subName)
                                <a 
                                    href="{{ route('catalog.category', ['category' => $activeDepartment->slug, 'subtrack' => $subName]) }}"
                                    class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ ($selectedSubtrack && strcasecmp($selectedSubtrack, $subName) === 0) ? 'bg-[#c30102] text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}"
                                >
                                    {{ $subName }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            <!-- 3-Column Course Grid Skeleton Loader (Shown during Category & Sort transitions) -->
            <div id="catalog-grid-skeleton" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8 pb-10 hidden" aria-hidden="true">
                @for($sk = 0; $sk < 6; $sk++)
                    <div class="liquid-glass-card rounded-2xl overflow-hidden flex flex-col h-full">
                        <div class="h-44 w-full skeleton-shimmer"></div>
                        <div class="p-4.5 space-y-3 flex-1 flex flex-col justify-between">
                            <div class="space-y-2.5">
                                <div class="flex items-center gap-2">
                                    <div class="h-5 w-24 rounded-full skeleton-shimmer"></div>
                                    <div class="h-5 w-16 rounded-full skeleton-shimmer"></div>
                                </div>
                                <div class="h-5 w-full rounded-lg skeleton-shimmer"></div>
                                <div class="h-5 w-3/4 rounded-lg skeleton-shimmer"></div>
                                <div class="h-3 w-full rounded skeleton-shimmer"></div>
                                <div class="h-3 w-5/6 rounded skeleton-shimmer"></div>
                            </div>
                            <div class="pt-3 border-t border-slate-100 space-y-2">
                                <div class="h-4 w-36 mx-auto rounded skeleton-shimmer"></div>
                                <div class="h-11 w-full rounded-xl skeleton-shimmer"></div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>

            <!-- 3-Column Course Grid -->
            <div id="catalog-grid-results" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8 pb-10">
                @forelse($courses as $course)
                    <x-course-card :course="$course" />
                @empty
                    <div class="col-span-full py-16 px-6 text-center bg-white rounded-3xl border border-dashed border-[#e3e7f0] shadow-sm">
                        <div class="w-16 h-16 rounded-2xl bg-slate-100 text-[#051c45] flex items-center justify-center text-3xl mx-auto mb-4">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h3 class="text-lg font-bold text-[#0f131f] mb-1">No Courses Currently Listed</h3>
                        <p class="text-xs text-[#727f9f] mb-5 max-w-sm mx-auto">Please check back soon or explore our other professional corporate disciplines.</p>
                        <a href="{{ route('catalog.index') }}" class="btn-ipdp-primary px-6 py-2.5 rounded-xl text-xs font-bold inline-block shadow-md">
                            Browse All Courses
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Numbered Pagination (1, 2, Next) -->
            <div class="pt-4 pb-6 flex justify-center">
                {{ $courses->links() }}
            </div>

        </div>
    </section>

    <!-- 3. Course Categories & 4 Core Learning Departments Section -->
    <section class="py-16 bg-gradient-to-b from-[#f8fafc] via-[#f1f5f9] to-[#f8fafc] border-t border-[#e3e7f0]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Heading -->
            <div class="mb-10 text-center sm:text-left flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div>
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-extrabold uppercase tracking-widest text-[#c30102] mb-1.5">
                        <i class="fas fa-sitemap"></i> ACS 4 Independent Learning Departments
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-[#0f131f] tracking-tight">
                        Course Categories
                    </h2>
                </div>
                <p class="text-xs text-[#727f9f] max-w-md">
                    Operated as 4 specialized Learning Departments — each led by a Department Head with a dedicated faculty team and 6 core training tracks.
                </p>
            </div>

            <!-- 4 Core Learning Departments Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach($runningCategories as $index => $catItem)
                    <div class="liquid-glass-card group bg-white rounded-2xl p-5 border border-[#e3e7f0] shadow-sm hover:shadow-xl hover:border-[#c30102]/30 transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <!-- Top Header: Circular Thumbnail + Dept Badge + Course Count -->
                            <div class="flex items-center justify-between gap-3 mb-4">
                                <a href="{{ route('catalog.category', $catItem['slug']) }}" class="flex items-center gap-3">
                                    <div class="w-14 h-14 rounded-full overflow-hidden border-2 border-white shadow-md bg-gray-100 group-hover:ring-2 group-hover:ring-[#c30102] transition-all duration-300 shrink-0">
                                        <img 
                                            src="{{ $catItem['image'] }}" 
                                            alt="{{ $catItem['name'] }}" 
                                            class="w-full h-full object-cover object-top"
                                            loading="lazy"
                                            onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&w=600&q=80';"
                                        >
                                    </div>
                                    <div>
                                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#051c45] bg-[#051c45]/10 px-2 py-0.5 rounded-md inline-block mb-1">
                                            Dept {{ $index + 1 }}
                                        </span>
                                        <h3 class="text-sm sm:text-base font-extrabold text-[#0f131f] group-hover:text-[#c30102] transition-colors leading-snug">
                                            {{ $catItem['name'] }}
                                        </h3>
                                    </div>
                                </a>
                            </div>

                            <!-- Department Head & Team Pill -->
                            <div class="mb-3.5 px-3 py-2 rounded-xl bg-slate-50 border border-slate-200/70 flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <i class="fas fa-user-tie text-xs text-[#c30102] shrink-0"></i>
                                    <div class="truncate">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-none">Department Head</span>
                                        <span class="text-xs font-bold text-[#0f131f] truncate block mt-0.5">
                                            {{ $catItem['head']?->name ?? 'Assigned Department Head' }}
                                        </span>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold text-[#051c45] bg-blue-50 px-2 py-0.5 rounded-md shrink-0">
                                    <i class="fas fa-users text-[9px] mr-0.5"></i> Dedicated Team
                                </span>
                            </div>

                            <!-- 6 Sub-Tracks List -->
                            @if(!empty($catItem['subcategories']))
                                <ul class="space-y-1.5 mb-4">
                                    @foreach($catItem['subcategories'] as $subTrack)
                                        <li>
                                            <a 
                                                href="{{ route('catalog.category', ['category' => $catItem['slug'], 'subtrack' => $subTrack]) }}"
                                                class="flex items-center gap-2 text-xs text-slate-600 hover:text-[#c30102] font-medium py-0.5 transition-colors"
                                            >
                                                <i class="fas fa-check-circle text-[10px] text-[#10b981] shrink-0"></i>
                                                <span class="truncate">{{ $subTrack }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        <!-- Footer CTA -->
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-[11px] font-semibold text-[#10b981] bg-emerald-50 px-2.5 py-0.5 rounded-full">
                                {{ $catItem['count'] }} {{ \Illuminate\Support\Str::plural('Course', $catItem['count']) }}
                            </span>
                            <a 
                                href="{{ route('catalog.category', $catItem['slug']) }}" 
                                class="text-xs font-extrabold text-[#051c45] group-hover:text-[#c30102] inline-flex items-center gap-1 transition-colors"
                            >
                                <span>Explore Dept</span>
                                <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

</div>
<script>
    function showCatalogSkeleton() {
        const skel = document.getElementById('catalog-grid-skeleton');
        const res = document.getElementById('catalog-grid-results');
        if (skel && res) {
            res.classList.add('hidden');
            skel.classList.remove('hidden');
        }
    }
</script>
@endsection
