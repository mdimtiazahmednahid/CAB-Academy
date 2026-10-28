@extends('layouts.public')

@section('title', 'Corporate Career & Placement Opportunities | CAB Academy')
@section('meta_description', 'Discover verified corporate career openings, export-import manager roles, supply chain positions, and executive placements at CAB Academy.')
@section('canonical_url', route('jobs.index'))

@section('content')
<div>
    <!-- Hero Banner (Imperial Navy Masthead) -->
    <section class="bg-gradient-to-r from-[#031330] via-[#051c45] to-[#0a2540] text-white py-14 sm:py-20 border-b border-[#1e2d4a] relative overflow-hidden">
        <!-- Ambient Glow Accents -->
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-[#c30102]/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-[#10b981]/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-[#10b981] text-xs font-black uppercase tracking-wider mb-4 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <i class="fas fa-briefcase text-xs"></i>
                <span>Corporate Placement Cell & Industry Openings</span>
            </div>

            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white mb-3">
                Career & Placement Opportunities
            </h1>
            <p class="text-xs sm:text-base text-slate-300 max-w-2xl mx-auto leading-relaxed">
                Direct recruitment pathways, corporate executive openings, and placement opportunities for CAB Academy alumni & certified practitioners across Bangladesh.
            </p>

            <div class="flex items-center justify-center gap-2 text-xs sm:text-sm text-slate-300 font-medium mt-5">
                <a href="{{ url('/') }}" class="hover:text-white transition-colors">Home</a>
                <span class="text-slate-500">/</span>
                <span class="font-bold text-white underline">Careers & Jobs</span>
            </div>
        </div>
    </section>

    <!-- Main Content Area -->
    <section class="py-12 sm:py-16 bg-[#f8fafc] min-h-[60vh]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Flash Notifications -->
            @if(session('success'))
                <div class="mb-8 p-4 bg-emerald-50 text-emerald-800 rounded-2xl border border-emerald-200 flex items-center gap-3 shadow-sm">
                    <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-8 p-4 bg-red-50 text-red-800 rounded-2xl border border-red-200 flex items-center gap-3 shadow-sm">
                    <i class="fas fa-exclamation-circle text-red-600 text-lg"></i>
                    <span class="text-sm font-semibold">{{ session('error') }}</span>
                </div>
            @endif
            @if($errors->any())
                <div class="mb-8 p-4 bg-red-50 text-red-800 rounded-2xl border border-red-200 shadow-sm">
                    <div class="flex items-center gap-2 mb-2 font-bold text-sm text-red-800">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Please correct the errors below:</span>
                    </div>
                    <ul class="list-disc pl-6 text-xs sm:text-sm space-y-1 text-red-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Search & Filter Controls -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-slate-200/90 shadow-sm mb-8 sm:mb-12">
                <form method="GET" action="{{ route('jobs.index') }}" class="flex flex-col md:flex-row gap-4 items-center justify-between">
                    <!-- Keyword Search -->
                    <div class="relative w-full md:w-96">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}" 
                            placeholder="Search by job title, company, or keyword..." 
                            class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536] transition-all"
                        >
                    </div>

                    <!-- Job Type Filter Pills -->
                    <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                        <a href="{{ route('jobs.index') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ !request('type') ? 'bg-[#041536] text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            All Roles
                        </a>
                        <a href="{{ route('jobs.index', array_merge(request()->query(), ['type' => 'Full-time'])) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ request('type') == 'Full-time' ? 'bg-[#041536] text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Full-time
                        </a>
                        <a href="{{ route('jobs.index', array_merge(request()->query(), ['type' => 'Contract'])) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ request('type') == 'Contract' ? 'bg-[#041536] text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Contract
                        </a>
                        <a href="{{ route('jobs.index', array_merge(request()->query(), ['type' => 'Remote'])) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ request('type') == 'Remote' ? 'bg-[#041536] text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Remote / Hybrid
                        </a>
                    </div>
                </form>
            </div>

            <!-- Job Openings Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                @forelse($jobs as $job)
                    <div class="bg-white rounded-2xl sm:rounded-3xl p-6 sm:p-7 border border-slate-200/90 shadow-[0_4px_16px_rgba(4,21,54,0.04)] hover:shadow-[0_20px_45px_-10px_rgba(4,21,54,0.14)] hover:border-[#c30102]/30 transition-all duration-300 flex flex-col justify-between group hover:-translate-y-1.5 relative overflow-hidden">
                        
                        <!-- Top Accent Line -->
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-[#041536] via-[#c30102] to-[#10b981] opacity-0 group-hover:opacity-100 transition-opacity"></div>

                        <div>
                            <!-- Header: Badges & Logo -->
                            <div class="flex items-start justify-between gap-3 mb-4">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-[#041536]/10 to-[#041536]/5 text-[#041536] border border-slate-200/60 flex items-center justify-center text-lg font-black group-hover:scale-105 transition-transform shrink-0 shadow-sm">
                                    @if(!empty($job->company_logo))
                                        <img src="{{ storage_asset($job->company_logo) }}" alt="{{ $job->company ?? 'Company' }}" class="w-full h-full object-cover rounded-2xl" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                                        <span style="display:none;">{{ substr($job->company ?? 'CAB', 0, 2) }}</span>
                                    @else
                                        <span>{{ substr($job->company ?? 'CAB', 0, 2) }}</span>
                                    @endif
                                </div>
                                <div class="flex flex-col items-end gap-1.5">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                        <i class="fas fa-check-circle text-[9px] mr-1"></i>Active Role
                                    </span>
                                    @if($job->created_at && $job->created_at->diffInDays(now()) < 14)
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-[#c30102]/10 text-[#c30102]">
                                            Recent
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Title & Company -->
                            <h3 class="text-base sm:text-lg font-black text-[#041536] group-hover:text-[#c30102] transition-colors leading-snug mb-1">
                                {{ $job->title }}
                            </h3>
                            <p class="text-xs sm:text-sm font-semibold text-slate-500 mb-4 flex items-center gap-1.5">
                                <i class="fas fa-building text-slate-400 text-xs"></i>
                                <span>{{ $job->company ?? $job->company_name ?? 'Corporate Partner Enterprise' }}</span>
                            </p>

                            <!-- Meta Pills (Location, Job Type, Salary Range) -->
                            <div class="flex flex-wrap gap-2 mb-4">
                                @if(!empty($job->location))
                                <div class="inline-flex items-center gap-1 text-[11px] font-medium text-slate-600 bg-slate-100/90 px-2.5 py-1 rounded-lg">
                                    <i class="fas fa-map-marker-alt text-[#c30102] text-[10px]"></i>
                                    <span>{{ $job->location }}</span>
                                </div>
                                @endif

                                <div class="inline-flex items-center gap-1 text-[11px] font-medium text-slate-600 bg-slate-100/90 px-2.5 py-1 rounded-lg">
                                    <i class="fas fa-clock text-slate-400 text-[10px]"></i>
                                    <span>{{ $job->job_type ?? 'Full-time' }}</span>
                                </div>

                                @if(!empty($job->salary_range))
                                <div class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-100">
                                    <i class="fas fa-money-bill-wave text-[10px]"></i>
                                    <span>{{ $job->salary_range }}</span>
                                </div>
                                @endif
                            </div>

                            <!-- Description Snippet -->
                            <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed mb-5">
                                {{ $job->description }}
                            </p>
                        </div>

                        <!-- Action Button -->
                        <div class="pt-3 border-t border-slate-100 mt-auto">
                            @if(!empty($job->apply_link))
                                <a href="{{ $job->apply_link }}" target="_blank" rel="noopener noreferrer" class="btn-cab-secondary w-full py-2.5 px-4 rounded-xl text-xs font-black flex items-center justify-center gap-2 group-hover:shadow-md transition-all">
                                    <span>Apply on Company Site</span>
                                    <i class="fas fa-external-link-alt text-[10px]"></i>
                                </a>
                            @else
                                <button type="button" onclick='openApplyModal({{ $job->id }}, @json($job->title), @json($job->company ?? "Corporate Partner"))' class="btn-cab-secondary w-full py-2.5 px-4 rounded-xl text-xs font-black flex items-center justify-center gap-2 group-hover:shadow-md transition-all">
                                    <span>Apply via CAB Academy</span>
                                    <i class="fas fa-paper-plane text-[10px]"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 px-6 text-center bg-white rounded-3xl border border-slate-200 shadow-sm max-w-lg mx-auto">
                        <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4 text-2xl">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <h3 class="text-lg font-bold text-[#041536] mb-1">No Active Openings Found</h3>
                        <p class="text-xs sm:text-sm text-slate-500 mb-6">
                            There are currently no open positions matching your search. Check back soon or contact the placement cell.
                        </p>
                        <a href="{{ route('jobs.index') }}" class="btn-ipdp-primary inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-xs font-bold shadow-md">
                            <span>Reset Filters</span>
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Corporate Employer Recruitment Callout Bar -->
            <div class="mt-12 sm:mt-16 bg-white rounded-2xl sm:rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-[#041536] text-white flex items-center justify-center text-xl sm:text-2xl shrink-0 shadow-md">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div>
                        <h4 class="text-base sm:text-lg font-black text-[#041536]">
                            Are You Hiring Commercial, Supply Chain, or Tax Professionals?
                        </h4>
                        <p class="text-xs sm:text-sm text-slate-500 font-medium">
                            Partner with CAB Academy Corporate Placement Cell to access verified, pre-screened alumni and certified practitioners.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3 shrink-0 w-full md:w-auto">
                    <a href="{{ route('contact.index') }}" class="btn-cab-secondary px-6 py-3 rounded-xl text-xs sm:text-sm font-black whitespace-nowrap w-full sm:w-auto text-center shadow-md">
                        <i class="fas fa-bullhorn mr-1.5"></i>Post an Opportunity
                    </a>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- Apply Modal (Dynamic for Authenticated & Guests) -->
<div id="applyModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 hidden p-4">
    <div class="bg-white rounded-2xl sm:rounded-3xl p-6 sm:p-8 w-full max-w-lg shadow-2xl border border-slate-200 relative animate-in fade-in duration-200">
        <div class="flex justify-between items-start mb-6 pb-3 border-b border-slate-100">
            <div>
                <span class="text-[10px] font-black uppercase tracking-wider text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">
                    CAB Academy Placement Cell
                </span>
                <h3 class="text-lg font-black text-[#041536] mt-1">Apply for: <span id="applyJobTitle" class="text-[#c30102]"></span></h3>
                <p id="applyCompany" class="text-xs text-slate-500 font-semibold"></p>
            </div>
            <button onclick="closeApplyModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition-colors">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>

        @auth
            <!-- Authenticated Student Submission -->
            <form method="POST" id="applyForm" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Upload Resume / CV (PDF, DOC, DOCX - Max 5MB) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="file" 
                            name="resume" 
                            accept=".pdf,.doc,.docx" 
                            required 
                            class="w-full text-xs text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-[#041536] file:text-white hover:file:bg-[#c30102] file:cursor-pointer cursor-pointer border border-slate-200 rounded-xl p-1"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Cover Letter / Key Qualifications (Optional)
                        </label>
                        <textarea 
                            name="cover_letter" 
                            rows="4" 
                            class="w-full rounded-xl border border-slate-300 p-3 text-xs sm:text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536] resize-none" 
                            placeholder="Briefly state your relevant supply chain, commercial, or tax experience..."
                        ></textarea>
                    </div>

                    <div class="pt-2 flex items-center gap-3">
                        <button type="submit" class="btn-ipdp-primary w-full py-3 rounded-xl text-xs sm:text-sm font-black shadow-md flex items-center justify-center gap-2">
                            <i class="fas fa-paper-plane"></i>
                            <span>Submit Candidate Application</span>
                        </button>
                    </div>
                </div>
            </form>
        @else
            <!-- Guest Prompt: Sign in or Contact Hotline -->
            <div class="space-y-5 text-center py-2">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto text-2xl border border-amber-200/80">
                    <i class="fas fa-user-lock"></i>
                </div>
                <div>
                    <h4 class="text-base font-bold text-[#041536] mb-1">
                        Verified Student Candidate Verification
                    </h4>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-sm mx-auto">
                        To apply directly through CAB Academy corporate placements, please sign in with your student account so our placement directors can attach your verified certificate transcripts.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    <a href="{{ route('login') }}" class="btn-ipdp-primary w-full py-3 rounded-xl text-xs font-black shadow-md flex items-center justify-center gap-2">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Sign In to Apply</span>
                    </a>
                    <a href="https://wa.me/{{ \App\Models\Setting::getVal('contact_whatsapp', '8801917054664') }}" target="_blank" rel="noopener noreferrer" class="bg-[#25D366] hover:bg-[#20ba5a] text-white w-full py-3 rounded-xl text-xs font-black shadow-md flex items-center justify-center gap-2 transition-all">
                        <i class="fab fa-whatsapp"></i>
                        <span>Inquire via WhatsApp</span>
                    </a>
                </div>

                <p class="text-[11px] text-slate-400">
                    Need assistance? Call Admissions & Placement: <a href="tel:01917054664" class="text-[#041536] font-bold hover:underline">01917-054664</a>
                </p>
            </div>
        @endauth
    </div>
</div>

<script>
    function openApplyModal(jobId, jobTitle, companyName) {
        document.getElementById('applyJobTitle').innerText = jobTitle;
        const compEl = document.getElementById('applyCompany');
        if (compEl) {
            compEl.innerText = companyName || 'Corporate Partner';
        }
        const form = document.getElementById('applyForm');
        if (form) {
            form.action = `/jobs/${jobId}/apply`;
        }
        document.getElementById('applyModal').classList.remove('hidden');
    }

    function closeApplyModal() {
        document.getElementById('applyModal').classList.add('hidden');
    }

    // Close on backdrop click
    document.getElementById('applyModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeApplyModal();
        }
    });
</script>
@endsection
