@extends('layouts.student')

@section('header_title', 'Home')

@section('content')
<div class="space-y-6">
    <!-- Greeting (Minimalist) -->
    <div>
        <h2 class="text-3xl font-bold tracking-tight text-gray-900">Good morning, {{ explode(' ', Auth::user()->name)[0] }}.</h2>
        <p class="text-gray-500 text-lg mt-1">Ready to continue your {{ Auth::user()->preferences['level'] ?? 'studies' }}?</p>
    </div>

    <!-- Continue Learning Card (Primary Action) -->
    <div class="bg-gradient-to-br from-green-700 to-green-900 rounded-3xl p-6 md:p-8 text-white shadow-lg relative overflow-hidden group cursor-pointer transition-transform active:scale-[0.98]">
        <!-- Decorative SVG -->
        <svg class="absolute right-0 bottom-0 opacity-10 transform translate-x-4 translate-y-4 group-hover:scale-110 transition-transform duration-500" width="160" height="160" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
        
        <div class="relative z-10">
            <span class="text-green-100 font-medium text-sm tracking-wide uppercase mb-1 block">Continue learning</span>
            <h3 class="text-2xl font-bold mb-6 max-w-[200px] leading-tight">Electromagnetic Induction</h3>
            
            <div class="flex items-center justify-between mt-auto">
                <div class="flex-1 mr-4">
                    <div class="flex justify-between text-sm mb-1 text-green-50 font-medium">
                        <span>Progress</span>
                        <span>72%</span>
                    </div>
                    <div class="w-full bg-green-900/50 rounded-full h-1.5">
                        <div class="bg-white h-1.5 rounded-full" style="width: 72%"></div>
                    </div>
                </div>
                <div class="bg-white/20 hover:bg-white/30 backdrop-blur-md p-3 rounded-xl transition-colors">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Snapshot -->
    <div>
        <div class="flex justify-between items-end mb-4">
            <h3 class="font-bold text-lg text-gray-900">Your Performance</h3>
            <a href="#" class="text-sm font-semibold text-primary">View details</a>
        </div>
        
        <div class="grid grid-cols-2 gap-4">
            <!-- Overall Score -->
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                <div class="text-gray-500 text-xs font-semibold uppercase tracking-wider mb-2">Overall Score</div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-gray-900">78%</span>
                    <span class="text-sm font-medium text-green-600 flex items-center">
                        <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                        6.4%
                    </span>
                </div>
                <div class="text-sm text-gray-400 font-medium mt-1">Good standing</div>
            </div>

            <!-- Focus Area -->
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                <div class="text-gray-500 text-xs font-semibold uppercase tracking-wider mb-2">Focus Area</div>
                <div class="text-lg font-bold text-gray-900 leading-tight">Transformers</div>
                <div class="text-sm text-red-500 font-medium mt-1">39% mastery</div>
            </div>
        </div>
    </div>

    <!-- Upcoming -->
    <div>
        <h3 class="font-bold text-lg text-gray-900 mb-4">Upcoming Schedule</h3>
        <div class="space-y-3">
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)] flex items-center gap-4">
                <div class="bg-orange-50 text-orange-600 p-3 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900">Physics Quiz 4</h4>
                    <p class="text-sm text-gray-500 mt-0.5">Tomorrow, 10:00 AM</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
