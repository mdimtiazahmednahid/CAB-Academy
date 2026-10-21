@extends('layouts.public')

@section('title', 'Public Credential Verification Registry - CAB Academy')

@section('content')
<div class="bg-gradient-to-b from-white via-[#f8fafc] to-[#f2f4f7] py-12 lg:py-20">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Verification Title -->
        <div class="text-center mb-10">
            <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-100 text-[#10b981] mb-3">
                <i class="fas fa-shield-alt text-xs"></i> Official Credential Registry
            </span>
            <h1 class="text-3xl sm:text-4xl font-black text-[#0f131f] tracking-tight">
                Certificate Verification Engine
            </h1>
            <p class="mt-2 text-sm text-[#727f9f]">
                Authenticating professional qualifications issued by Commercial Academy Bangladesh (CAB Academy).
            </p>
        </div>

        @if($certificate)
            <!-- Verified Card -->
            <div class="bg-white rounded-3xl p-8 sm:p-10 border-2 border-emerald-500 shadow-xl relative overflow-hidden">
                <!-- Top Emerald Banner -->
                <div class="bg-emerald-500 text-white text-center py-2.5 px-4 -mx-8 sm:-mx-10 -mt-8 sm:-mt-10 mb-8 flex items-center justify-center gap-2 text-xs sm:text-sm font-black uppercase tracking-wider">
                    <i class="fas fa-check-circle text-base"></i> Authentic Credential Confirmed & Validated
                </div>

                <div class="space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-gray-100">
                        <div>
                            <span class="text-xs text-gray-500 uppercase tracking-wider block">Conferred Recipient</span>
                            <h2 class="text-2xl font-black text-[#0f131f] mt-1">{{ $certificate->user->name }}</h2>
                        </div>
                        <div class="sm:text-right">
                            <span class="text-xs text-gray-500 uppercase tracking-wider block">Credential ID</span>
                            <span class="font-mono text-base font-bold text-[#f15a29]">{{ $certificate->certificate_number }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pb-6 border-b border-gray-100">
                        <div>
                            <span class="text-xs text-gray-500 uppercase tracking-wider block">Executive Program</span>
                            <h3 class="text-base font-bold text-[#0f131f] mt-1">{{ $certificate->course->title }}</h3>
                            <a href="{{ route('courses.show', $certificate->course) }}" class="text-xs font-semibold text-[#f15a29] hover:underline mt-1 inline-block">
                                View Course Curriculum &rarr;
                            </a>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 uppercase tracking-wider block">Official Issue Date</span>
                            <p class="text-base font-bold text-[#0f131f] mt-1">{{ \Carbon\Carbon::parse($certificate->issue_date)->format('F d, Y') }}</p>
                        </div>
                    </div>

                    <!-- Security & Cryptography Box -->
                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-200">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500 flex items-center gap-1.5 mb-1">
                            <i class="fas fa-fingerprint text-emerald-600"></i> Cryptographic Hash Verification
                        </span>
                        <div class="font-mono text-xs text-gray-700 break-all select-all">
                            {{ $certificate->verification_hash }}
                        </div>
                        <p class="text-[11px] text-gray-400 mt-2">
                            This hash represents an immutable digital signature verified against CAB Academy institutional servers.
                        </p>
                    </div>

                    <div class="pt-4 flex flex-wrap gap-4 items-center justify-between text-xs text-gray-500">
                        <span>Issuing Body: <strong>Commercial Academy Bangladesh</strong></span>
                        <span>Campus: <strong>Mirpur-Pallabi, Dhaka-1216</strong></span>
                    </div>
                </div>
            </div>
        @else
            <!-- Not Found Card -->
            <div class="bg-white rounded-3xl p-8 sm:p-10 border border-red-200 shadow-xl text-center">
                <div class="w-16 h-16 rounded-full bg-red-100 text-red-600 flex items-center justify-center mx-auto text-2xl mb-4">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h2 class="text-2xl font-black text-[#0f131f]">Credential Not Found</h2>
                <p class="text-sm text-gray-600 mt-2 max-w-md mx-auto leading-relaxed">
                    The certificate identifier <code class="bg-gray-100 px-2 py-0.5 rounded font-mono text-red-600 font-bold">{{ $certificate_number }}</code> could not be located in our official academic database.
                </p>

                <!-- Search Form -->
                <div class="mt-8 max-w-md mx-auto">
                    <form action="#" onsubmit="event.preventDefault(); window.location.href='/verify-certificate/' + document.getElementById('searchCert').value.trim();" class="flex gap-2">
                        <input 
                            type="text" 
                            id="searchCert" 
                            placeholder="Enter Serial (e.g. CAB-2026-XXXXXXXX)" 
                            required
                            class="flex-1 px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#f15a29]"
                        >
                        <button type="submit" class="btn-ipdp-primary px-5 py-2.5 rounded-xl text-xs font-bold shadow-sm">
                            Validate
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <!-- Manual Registry Lookup Widget -->
        <div class="mt-12 bg-white rounded-2xl p-6 border border-[#e3e7f0] shadow-sm text-center">
            <h4 class="font-bold text-sm text-[#0f131f] mb-1">Employers & HR Verification Desk</h4>
            <p class="text-xs text-gray-500 mb-4">
                To request formal verification letters or bulk credential audits, please contact our registry desk directly at 
                <a href="mailto:commercialacademy868@gmail.com" class="text-[#f15a29] font-bold">commercialacademy868@gmail.com</a> or call <strong>01917-054664</strong>.
            </p>
        </div>

    </div>
</div>
@endsection
