@extends('layouts.student')

@section('header_title', 'Careers')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 mb-2">Job Opportunities</h1>
    <p class="text-gray-500 text-sm">Discover exclusive career opportunities handpicked for our students.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-8">
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 text-green-800 rounded-lg border border-green-200">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 text-red-800 rounded-lg border border-red-200">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 text-red-800 rounded-lg border border-red-200">
            <ul class="list-disc pl-5 text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @forelse($jobs as $job)
        <div class="bg-white rounded-3xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-gray-100 overflow-hidden flex flex-col p-6">
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
                        <h3 class="font-bold text-gray-900 text-xl leading-tight">{{ $job->title }}</h3>
                        <p class="text-primary font-medium mt-0.5">{{ $job->company }}</p>
                    </div>
                </div>
                @if($job->created_at->diffInDays(now()) < 7)
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-lg">New</span>
                @endif
            </div>

            <div class="flex flex-wrap gap-3 mb-4">
                @if($job->location)
                    <div class="flex items-center text-sm text-gray-600 gap-1 bg-gray-50 px-2 py-1 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.242-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        {{ $job->location }}
                    </div>
                @endif
                @if($job->salary_range)
                    <div class="flex items-center text-sm text-gray-600 gap-1 bg-gray-50 px-2 py-1 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ $job->salary_range }}
                    </div>
                @endif
            </div>

            <div class="text-sm text-gray-600 mb-6 flex-1 line-clamp-3">
                {{ $job->description }}
            </div>

            @if($job->apply_link)
                <a href="{{ $job->apply_link }}" target="_blank" class="w-full block text-center px-4 py-2.5 bg-primary text-white rounded-xl font-medium hover:opacity-90 transition-opacity">
                    Apply Externally
                </a>
            @else
                <button onclick='openApplyModal({{ $job->id }}, @json($job->title))' class="w-full block text-center px-4 py-2.5 bg-primary text-white rounded-xl font-medium hover:opacity-90 transition-opacity">
                    Apply Now
                </button>
            @endif
        </div>
    @empty
        <div class="col-span-full p-12 text-center bg-gray-50 rounded-3xl border border-gray-100">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            <h3 class="text-xl font-bold text-gray-900 mb-2">No Jobs Currently</h3>
            <p class="text-gray-500 max-w-sm mx-auto">Check back later for new career opportunities!</p>
        </div>
    @endforelse
</div>

<!-- Apply Modal -->
<div id="applyModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Apply for: <span id="applyJobTitle" class="text-primary"></span></h3>
            <button onclick="document.getElementById('applyModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form method="POST" id="applyForm" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Resume (PDF/DOC, max 5MB) <span class="text-red-500">*</span></label>
                    <input type="file" name="resume" accept=".pdf,.doc,.docx" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cover Letter (Optional)</label>
                    <textarea name="cover_letter" rows="4" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary" placeholder="Why are you a good fit?"></textarea>
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-primary text-white rounded-lg font-medium hover:opacity-90">Submit Application</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openApplyModal(jobId, jobTitle) {
        document.getElementById('applyJobTitle').innerText = jobTitle;
        document.getElementById('applyForm').action = `/jobs/${jobId}/apply`;
        document.getElementById('applyModal').classList.remove('hidden');
    }
</script>
@endsection
