@extends('layouts.public')
@php
    $siteName = \App\Models\Setting::getVal('site_name', 'ACS Academy');
@endphp

@section('title', 'Terms of Service - ' . $siteName)
@section('meta_description', 'Terms of Service, Enrollment Agreement, and Institutional Policies for students and corporate trainees at ' . $siteName . '.')
@section('canonical_url', route('legal.terms'))

@section('content')
<div class="bg-[#f2f4f7] py-12 lg:py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="liquid-glass-card rounded-3xl p-8 sm:p-12">
            
            <div class="border-b border-gray-100 pb-6 mb-8">
                <h1 class="text-3xl font-black text-[#0f131f] tracking-tight">Terms of Service</h1>
                <p class="text-xs text-gray-500 mt-2">Effective Date: October 2026 | Last Updated: October 2026</p>
            </div>

            @if(!empty(\App\Models\Setting::getVal('policy_terms_content')))
                <div class="prose max-w-none text-sm text-[#35415b] leading-relaxed whitespace-pre-line">
                    {!! \App\Models\Setting::getVal('policy_terms_content') !!}
                </div>
            @else
            <div class="space-y-6 text-sm text-[#35415b] leading-relaxed">
                <section>
                    <h2 class="text-lg font-bold text-[#0f131f] mb-2">1. Acceptance of Terms</h2>
                    <p>
                        By enrolling in any program, workshop, day-long course, or accessing the {{ $siteName }} online portal, you agree to comply with and be legally bound by these Terms of Service. If you do not accept these terms, you should not register or submit payment.
                    </p>
                </section>

                <section>
                    <h2 class="text-lg font-bold text-[#0f131f] mb-2">2. Admissions & Course Access</h2>
                    <ul class="list-disc list-inside space-y-1 text-gray-600">
                        <li>Course enrollment is confirmed upon successful verification of tuition fee payment.</li>
                        <li>Digital course access credentials and materials are personal to the enrolled learner and may not be shared, duplicated, or resold.</li>
                        <li>Batch rescheduling requests must be submitted at least 48 hours prior to the official batch commencement date.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-lg font-bold text-[#0f131f] mb-2">3. Intellectual Property Rights</h2>
                    <p>
                        All course syllabi, lecture presentations, trade simulation worksheets, sample documentation templates, and videos provided by {{ $siteName }} are protected under copyright law. Unauthorized distribution or public commercial exploitation is strictly prohibited and subject to legal action.
                    </p>
                </section>

                <section>
                    <h2 class="text-lg font-bold text-[#0f131f] mb-2">4. Certificate Issuance & Authenticity</h2>
                    <p>
                        Official {{ $siteName }} Executive Certificates are awarded exclusively upon satisfactory completion of required attendance, course assignments, and passing scores on the module evaluation quiz. Certificates include a unique alphanumeric verification hash verifiable on the official {{ $siteName }} registry.
                    </p>
                </section>

                <section>
                    <h2 class="text-lg font-bold text-[#0f131f] mb-2">5. Governing Law & Jurisdiction</h2>
                    <p>
                        These terms are governed by and construed in accordance with the laws of the People's Republic of Bangladesh. Any disputes arising under these terms shall be subject to the exclusive jurisdiction of the competent courts of Dhaka, Bangladesh.
                    </p>
                </section>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
