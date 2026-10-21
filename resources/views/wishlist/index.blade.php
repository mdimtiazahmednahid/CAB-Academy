@extends('layouts.public')

@section('title', 'Saved Courses & Wishlist - CAB Academy')

@section('content')
<div class="bg-gradient-to-b from-white via-[#f8fafc] to-[#f2f4f7] py-12 lg:py-16 min-h-[70vh]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-10 pb-6 border-b border-[#e3e7f0]">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-[#c30102]/10 text-[#c30102] border border-[#c30102]/20 mb-2">
                    <i class="fas fa-heart text-xs"></i> Student Wishlist
                </span>
                <h1 class="text-3xl sm:text-4xl font-black text-[#0f131f] tracking-tight">
                    Saved Programs & Courses
                </h1>
                <p class="text-sm text-[#727f9f] mt-1">
                    Keep track of executive certifications and day-long workshops you plan to attend.
                </p>
            </div>
            <div>
                <a href="{{ route('catalog.index') }}" class="btn-ipdp-secondary px-5 py-2.5 rounded-xl text-xs font-bold shadow-sm inline-flex items-center gap-2">
                    <i class="fas fa-search"></i> Explore More Courses
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-8 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center gap-3 text-sm shadow-sm">
                <i class="fas fa-check-circle text-emerald-600 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($courses->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($courses as $course)
                    <x-course-card :course="$course" />
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-3xl p-12 text-center border border-[#e3e7f0] shadow-sm max-w-2xl mx-auto my-12">
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-[#c30102]/15 to-[#051c45]/10 text-[#c30102] border border-[#c30102]/20 flex items-center justify-center mx-auto text-3xl mb-6 shadow-sm">
                    <i class="far fa-heart"></i>
                </div>
                <h3 class="text-2xl font-black text-[#0f131f]">Your Wishlist is Empty</h3>
                <p class="text-sm text-[#727f9f] mt-2 mb-8 max-w-md mx-auto leading-relaxed">
                    You haven't saved any courses yet. Browse our comprehensive curriculum of Export-Import, Supply Chain, and Customs VAT masterclasses to save your favorites.
                </p>
                <div class="flex flex-wrap justify-center gap-3">
                    <a href="{{ route('catalog.index') }}" class="btn-ipdp-primary px-6 py-3 rounded-xl font-bold text-sm shadow-md">
                        Browse Course Catalog
                    </a>
                    <a href="{{ route('catalog.certificate') }}" class="btn-ipdp-secondary px-6 py-3 rounded-xl font-bold text-sm shadow-sm">
                        View Certificate Programs
                    </a>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
