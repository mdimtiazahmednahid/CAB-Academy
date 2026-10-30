@extends('layouts.public')
@php
    $siteName = \App\Models\Setting::getVal('site_name', 'ACS Academy');
@endphp

@section('title', 'Privacy Policy - ' . $siteName)
@section('meta_description', 'Privacy Policy and Student Data Protection Charter at ' . $siteName . '.')
@section('canonical_url', route('legal.privacy'))

@section('content')
<div class="bg-[#f2f4f7] py-12 lg:py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="liquid-glass-card rounded-3xl p-8 sm:p-12">
            
            <div class="border-b border-gray-100 pb-6 mb-8">
                <h1 class="text-3xl font-black text-[#0f131f] tracking-tight">Privacy Policy</h1>
                <p class="text-xs text-gray-500 mt-2">Effective Date: October 2026 | Last Updated: October 2026</p>
            </div>

            @if(!empty(\App\Models\Setting::getVal('policy_privacy_content')))
                <div class="prose max-w-none text-sm text-[#35415b] leading-relaxed whitespace-pre-line">
                    {!! \App\Models\Setting::getVal('policy_privacy_content') !!}
                </div>
            @else
            <div class="space-y-6 text-sm text-[#35415b] leading-relaxed">
                <section>
                    <h2 class="text-lg font-bold text-[#0f131f] mb-2">1. Overview & Commitment</h2>
                    <p>
                        {{ $siteName }} operates in full compliance with Bangladesh digital commerce policies, the Information and Communication Technology (ICT) Act, and international data protection standards. This document details how we collect, store, safeguard, and process your personal and educational information.
                    </p>
                </section>

                <section>
                    <h2 class="text-lg font-bold text-[#0f131f] mb-2">2. Information We Collect</h2>
                    <ul class="list-disc list-inside space-y-1 text-gray-600">
                        <li><strong>Personal Identification:</strong> Full name, national identity/passport reference (for certificate verification), email address, and mobile number.</li>
                        <li><strong>Academic & Enrollment Data:</strong> Enrolled courses, quiz evaluations, attendance logs, and digital certificate serial records.</li>
                        <li><strong>Financial & Transactional Records:</strong> Mobile financial service (bKash/Nagad/Rocket) or bank transfer transaction IDs. Note: {{ $siteName }} does not store credit card CVVs or bank PIN credentials.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-lg font-bold text-[#0f131f] mb-2">3. How We Use Your Data</h2>
                    <p>Your data is used strictly for:</p>
                    <ul class="list-disc list-inside space-y-1 text-gray-600 mt-2">
                        <li>Processing admissions, issuing student ID badges, and course access credentials.</li>
                        <li>Issuing tamper-evident digital certificates with cryptographically verifiable serial numbers.</li>
                        <li>Notifying you regarding batch schedules, lecture notes, workshop venue updates, and exam results.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-lg font-bold text-[#0f131f] mb-2">4. Data Security & Storage</h2>
                    <p>
                        We employ enterprise-grade SSL/TLS encryption, strictly managed database permissions, and multi-factor authentication for administrative accounts. We never sell, rent, or lease your private contact information to third-party telemarketers.
                    </p>
                </section>

                <section>
                    <h2 class="text-lg font-bold text-[#0f131f] mb-2">5. Contact Our Data Compliance Officer</h2>
                    <p>
                        For questions regarding your private information or data deletion requests, contact our executive office:
                    </p>
                    <div class="mt-3 p-4 bg-gray-50 rounded-xl border border-gray-200 text-xs sm:text-sm">
                        <p class="font-bold text-[#0f131f]">{{ $siteName }} Compliance Desk</p>
                        <p class="text-gray-600">Operational Campus: {{ \App\Models\Setting::getVal('contact_address', 'Mirpur-Pallabi, Dhaka, Bangladesh, 1216') }}</p>
                        <p class="text-gray-600">Email: <a href="mailto:{{ \App\Models\Setting::getVal('contact_email', 'commercialacademy868@gmail.com') }}" class="text-[#c30102] font-medium underline">{{ \App\Models\Setting::getVal('contact_email', 'commercialacademy868@gmail.com') }}</a></p>
                        <p class="text-gray-600">Phone: {{ \App\Models\Setting::getVal('contact_hotline', '01917-054664') }}</p>
                    </div>
                </section>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
