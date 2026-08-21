@extends('layouts.student')

@section('header_title', 'Home')

@section('content')
<div class="space-y-6">
    <!-- Greeting (Minimalist) -->
    <div>
        <h2 class="text-3xl font-bold tracking-tight text-gray-900">Good morning, {{ explode(' ', Auth::user()->name)[0] }}.</h2>
        <p class="text-gray-500 text-lg mt-1">Ready to continue your {{ Auth::user()->preferences['level'] ?? 'studies' }}?</p>
    </div>

    <!-- Enrolled Courses -->
    @if($enrolledCourses->isNotEmpty())
        <div>
            <div class="flex justify-between items-end mb-4">
                <h3 class="font-bold text-xl text-gray-900">My Courses</h3>
                <a href="{{ route('catalog.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-semibold">Browse more &rarr;</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($enrolledCourses as $course)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)] overflow-hidden group hover:shadow-[0_8px_30px_rgba(0,0,0,0.06)] transition-all duration-300 flex flex-col">
                        @if($course->thumbnail)
                            <img src="{{ Storage::url($course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-4xl shadow-inner">
                                {{ substr($course->title, 0, 1) }}
                            </div>
                        @endif
                        <div class="p-5 flex-1 flex flex-col">
                            <h4 class="font-bold text-lg text-gray-900 mb-2 line-clamp-1">{{ $course->title }}</h4>
                            <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ Str::limit($course->description, 80) }}</p>
                            <div class="mt-auto pt-4 border-t border-gray-50">
                                <a href="{{ route('courses.show', $course) }}" class="block w-full py-2.5 px-4 bg-indigo-50 text-indigo-700 font-semibold rounded-xl text-center hover:bg-indigo-100 transition-colors shadow-sm">
                                    Continue Learning
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="bg-gradient-to-br from-indigo-700 to-purple-900 rounded-3xl p-6 md:p-8 text-white shadow-lg relative overflow-hidden group">
            <svg class="absolute right-0 bottom-0 opacity-10 transform translate-x-4 translate-y-4 group-hover:scale-110 transition-transform duration-500" width="160" height="160" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            <div class="relative z-10">
                <span class="text-indigo-100 font-medium text-sm tracking-wide uppercase mb-1 block">Welcome</span>
                <h3 class="text-2xl font-bold mb-4 max-w-md leading-tight">You are not enrolled in any courses yet.</h3>
                <p class="text-indigo-100 mb-6 max-w-md">Discover our catalog and start your learning journey today.</p>
                <a href="{{ route('catalog.index') }}" class="inline-block bg-white text-indigo-900 font-bold px-6 py-3 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">
                    Browse Catalog
                </a>
            </div>
        </div>
    @endif

    <!-- Gamification Snapshot -->
    <div>
        <div class="flex justify-between items-end mb-4">
            <h3 class="font-bold text-lg text-gray-900">Your Progress</h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Level -->
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                <div class="text-gray-500 text-xs font-semibold uppercase tracking-wider mb-2">Current Level</div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-indigo-600">Level {{ Auth::user()->level }}</span>
                </div>
                <div class="text-sm text-gray-400 font-medium mt-1">Keep it up!</div>
            </div>

            <!-- XP Progress -->
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)] md:col-span-2 flex flex-col justify-center">
                <div class="flex justify-between items-end mb-2">
                    <div class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Experience Points (XP)</div>
                    <div class="text-sm font-bold text-gray-900">{{ Auth::user()->xp }} / {{ Auth::user()->next_level_xp }} XP</div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ min(100, (Auth::user()->xp / max(1, Auth::user()->next_level_xp)) * 100) }}%"></div>
                </div>
                <div class="text-xs text-gray-400 font-medium mt-2">{{ Auth::user()->next_level_xp - Auth::user()->xp }} XP to next level</div>
            </div>
            
            <!-- Streak -->
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)] md:col-span-3 flex items-center justify-between">
                <div>
                    <div class="text-gray-500 text-xs font-semibold uppercase tracking-wider mb-2">Learning Streak</div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-bold text-orange-500">{{ Auth::user()->current_streak }} Days</span>
                    </div>
                </div>
                <div class="text-orange-500">
                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Recommended Courses Feed -->
    @if(isset($feedCourses) && $feedCourses->isNotEmpty())
    <div class="mt-8">
        <div class="flex justify-between items-end mb-4">
            <div>
                <h3 class="font-bold text-xl text-gray-900">Recommended Courses</h3>
                <p class="text-sm text-gray-500 mt-1">Explore our latest additions to expand your skills.</p>
            </div>
            <a href="{{ route('catalog.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-semibold">View all &rarr;</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($feedCourses as $course)
                <a href="{{ route('courses.show', $course) }}" class="group bg-white rounded-3xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-gray-100 overflow-hidden hover:shadow-[0_4px_25px_rgba(0,0,0,0.06)] transition-all duration-300 flex flex-col active:scale-[0.98]">
                    <!-- Thumbnail -->
                    <div class="w-full h-48 bg-gray-100 relative overflow-hidden flex items-center justify-center">
                        @if($course->cover_image)
                            <img src="{{ Storage::url($course->cover_image) }}" alt="{{ $course->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @elseif($course->thumbnail)
                            <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="absolute inset-0 bg-gradient-to-br from-indigo-500 to-purple-600"></div>
                            <span class="relative text-white font-bold text-3xl opacity-80">{{ substr($course->title, 0, 1) }}</span>
                        @endif
                        
                        @if($course->price > 0)
                            <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm text-gray-900 font-bold px-3 py-1 rounded-full text-sm shadow-sm">
                                ৳{{ number_format($course->price, 2) }}
                            </div>
                        @else
                            <div class="absolute top-3 right-3 bg-green-500/90 backdrop-blur-sm text-white font-bold px-3 py-1 rounded-full text-sm shadow-sm">
                                Free
                            </div>
                        @endif
                    </div>
                    <!-- Content -->
                    <div class="p-5 flex-1 flex flex-col">
                        <h3 class="font-bold text-gray-900 text-lg mb-2 line-clamp-2 group-hover:text-indigo-600 transition-colors">{{ $course->title }}</h3>
                        <p class="text-sm text-gray-500 line-clamp-2 mb-4 flex-1">{{ $course->description }}</p>
                        <div class="flex items-center justify-between mt-auto pt-4 border-t border-gray-50">
                            <span class="text-sm font-bold text-indigo-600 flex items-center gap-1 w-full justify-center bg-indigo-50 py-2 rounded-xl group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                View Details <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Career Opportunities Feed -->
    @if(isset($feedJobs) && $feedJobs->isNotEmpty())
    <div class="mt-8">
        <div class="flex justify-between items-end mb-4">
            <div>
                <h3 class="font-bold text-xl text-gray-900">Career Opportunities</h3>
                <p class="text-sm text-gray-500 mt-1">Exclusive job openings for our students.</p>
            </div>
            <a href="{{ route('jobs.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-semibold">View all &rarr;</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($feedJobs as $job)
                <a href="{{ route('jobs.index') }}" class="bg-white rounded-3xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-gray-100 overflow-hidden flex flex-col p-6 hover:shadow-[0_4px_25px_rgba(0,0,0,0.06)] transition-all duration-300 active:scale-[0.98] group">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-4">
                            @if($job->company_logo)
                                <img src="{{ Storage::url($job->company_logo) }}" alt="{{ $job->company }}" class="w-12 h-12 rounded-xl object-cover border border-gray-100 shadow-sm shrink-0">
                            @else
                                <div class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center shrink-0 border border-gray-100 shadow-sm">
                                    <span class="text-lg font-bold text-gray-400">{{ substr($job->company, 0, 1) }}</span>
                                </div>
                            @endif
                            <div>
                                <h3 class="font-bold text-gray-900 text-xl leading-tight group-hover:text-indigo-600 transition-colors">{{ $job->title }}</h3>
                                <p class="text-indigo-600 font-medium mt-0.5">{{ $job->company }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3 mb-4">
                        @if($job->location)
                            <div class="flex items-center text-sm text-gray-600 gap-1 bg-gray-50 px-2 py-1 rounded-lg">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.242-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                {{ $job->location }}
                            </div>
                        @endif
                        @if($job->salary_range)
                            <div class="flex items-center text-sm text-gray-600 gap-1 bg-gray-50 px-2 py-1 rounded-lg">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ $job->salary_range }}
                            </div>
                        @endif
                    </div>

                    <div class="text-sm text-gray-600 mb-6 flex-1 line-clamp-3">
                        {{ $job->description }}
                    </div>

                    <div class="flex items-center justify-between mt-auto pt-4 border-t border-gray-50">
                        <span class="text-sm font-bold text-indigo-600 flex items-center gap-1 w-full justify-center bg-indigo-50 py-2 rounded-xl group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                            Apply Now <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Spacer before payments -->
    <div class="pt-4 border-t border-gray-100"></div>

    <!-- My Payment Requests -->
    @if($payments->isNotEmpty())
    <div>
        <h3 class="font-bold text-lg text-gray-900 mb-4">My Payment Requests</h3>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-semibold">Course</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Amount</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Date</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($payments as $payment)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900">{{ $payment->course->title }}</td>
                            <td class="px-6 py-4 text-green-600 font-medium">৳{{ number_format($payment->amount, 2) }}</td>
                            <td class="px-6 py-4">{{ $payment->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                @if($payment->status === 'pending')
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full inline-flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Pending
                                    </span>
                                @elseif($payment->status === 'processing')
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full inline-flex items-center gap-1">
                                        <svg class="w-3 h-3 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> Processing
                                    </span>
                                @elseif($payment->status === 'verified')
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full inline-flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Verified
                                    </span>
                                @else
                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full inline-flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg> Rejected
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Job Applications Widget -->
    @if($jobApplications->isNotEmpty())
    <div>
        <h3 class="font-bold text-lg text-gray-900 mb-4">My Job Applications</h3>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-semibold">Job Title</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Applied On</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($jobApplications as $application)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900">{{ $application->jobPost->title }}</td>
                            <td class="px-6 py-4">{{ $application->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                @if($application->status === 'pending')
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full inline-flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Pending
                                    </span>
                                @elseif($application->status === 'reviewed')
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full inline-flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg> Reviewed
                                    </span>
                                @elseif($application->status === 'accepted')
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full inline-flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Accepted
                                    </span>
                                @else
                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full inline-flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg> Rejected
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
