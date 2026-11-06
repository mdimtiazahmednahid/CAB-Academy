@extends(auth()->check() && !auth()->user()->isAdmin() ? 'layouts.student' : 'layouts.public')

@section('title', 'Saved Courses - ' . \App\Models\Setting::getVal('site_name', 'Academy of Commercial & Supply Chain (ACS)'))
@section('header_title', 'Wishlist')
@section('robots', 'noindex, nofollow, noarchive, nosnippet')

@section('content')
<div class="{{ auth()->check() && !auth()->user()->isAdmin() ? '' : 'bg-gradient-to-b from-white via-[#f8fafc] to-[#f2f4f7] py-10 lg:py-14 min-h-[70vh]' }}">
    <div class="{{ auth()->check() && !auth()->user()->isAdmin() ? '' : 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8' }}">
        
        <!-- Compact Header -->
        <div class="flex items-center justify-between gap-3 mb-5 pb-3.5 border-b border-slate-200/80">
            <h1 class="text-lg sm:text-xl font-extrabold text-[#051c45] tracking-tight">
                Saved Courses ({{ $courses->count() }})
            </h1>
            <a href="{{ route('catalog.index') }}" class="px-3.5 py-2 rounded-xl bg-[#051c45] hover:bg-[#082860] text-white text-xs font-bold inline-flex items-center gap-1.5 transition-colors">
                <i class="fas fa-search text-[10px]"></i>
                <span>Explore Courses</span>
            </a>
        </div>

        @if(session('success'))
            <div class="mb-5 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-2.5 text-xs font-semibold">
                <i class="fas fa-check-circle text-emerald-600"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($courses->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 {{ auth()->check() && !auth()->user()->isAdmin() ? 'lg:grid-cols-3' : 'lg:grid-cols-3 xl:grid-cols-4' }} gap-5">
                @foreach($courses as $course)
                    <x-course-card :course="$course" />
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-2xl p-8 text-center border border-slate-200/80 shadow-2xs max-w-md mx-auto my-6">
                <div class="w-12 h-12 rounded-xl bg-[#c30102]/10 text-[#c30102] flex items-center justify-center mx-auto text-lg mb-3">
                    <i class="far fa-heart"></i>
                </div>
                <h3 class="text-base font-extrabold text-[#051c45]">Your Wishlist is Empty</h3>
                <p class="text-xs text-slate-500 mt-1 mb-5">Save courses from the catalog to compare and enroll later.</p>
                <a href="{{ route('catalog.index') }}" class="btn-ipdp-primary px-5 py-2.5 rounded-xl font-bold text-xs inline-block">
                    Explore Courses
                </a>
            </div>
        @endif

    </div>
</div>
@endsection
