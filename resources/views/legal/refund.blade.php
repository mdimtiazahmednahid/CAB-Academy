@extends('layouts.public')
@php
    $siteName = \App\Models\Setting::getVal('site_name', 'ACS Academy');
@endphp

@section('title', 'Refund & Returns Policy - ' . $siteName)
@section('meta_description', 'Tuition Refund, Cohort Rescheduling, and Cancellation Policy at ' . $siteName . '.')
@section('canonical_url', route('legal.refund'))

@section('content')
<div class="bg-[#f2f4f7] py-12 lg:py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="liquid-glass-card rounded-3xl p-8 sm:p-12">
            
            <div class="border-b border-gray-100 pb-6 mb-8">
                <h1 class="text-3xl font-black text-[#0f131f] tracking-tight">Refund & Cancellation Policy</h1>
                <p class="text-xs text-gray-500 mt-2">Effective Date: October 2026 | Last Updated: October 2026</p>
            </div>

            @if(!empty(\App\Models\Setting::getVal('policy_refund_content')))
                <div class="prose max-w-none text-sm text-[#35415b] leading-relaxed whitespace-pre-line">
                    {!! \App\Models\Setting::getVal('policy_refund_content') !!}
                </div>
            @else
            <div class="space-y-6 text-sm text-[#35415b] leading-relaxed">
                <section>
                    <h2 class="text-lg font-bold text-[#0f131f] mb-2">1. Refund Eligibility Period</h2>
                    <p>
                        We strive for 100% satisfaction in all executive training cohorts. Students are entitled to submit a refund request under the following criteria:
                    </p>
                    <ul class="list-disc list-inside space-y-1 text-gray-600 mt-2">
                        <li><strong>Multi-Week Certificate Programs:</strong> Full refund minus a 10% administrative processing fee if requested at least 3 business days prior to the first scheduled class session.</li>
                        <li><strong>Day-Long Intensive Workshops:</strong> 100% credit transfer to a future scheduled batch or 80% monetary refund if notified 48 hours before workshop date.</li>
                        <li><strong>Post-Commencement:</strong> Once the first live class session has convened and proprietary course materials have been downloaded, tuition fees are non-refundable. However, students may request a batch transfer to the subsequent intake at no additional charge.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-lg font-bold text-[#0f131f] mb-2">2. Academy Cancellation or Rescheduling</h2>
                    <p>
                        In the event that {{ $siteName }} postpones or cancels a scheduled batch due to instructor illness, national holidays, or unforeseen force majeure events, enrolled students will be offered the choice of:
                    </p>
                    <ul class="list-disc list-inside space-y-1 text-gray-600 mt-2">
                        <li>Priority seat enrollment in the revised reschedule dates, OR</li>
                        <li>A 100% full refund returned through their original payment method within 5-7 working days.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-lg font-bold text-[#0f131f] mb-2">3. Refund Processing Procedure</h2>
                    <p>
                        To request a refund, please send an official email to <a href="mailto:{{ \App\Models\Setting::getVal('contact_email', 'commercialacademy868@gmail.com') }}" class="text-[#c30102] font-medium underline">{{ \App\Models\Setting::getVal('contact_email', 'commercialacademy868@gmail.com') }}</a> with the subject header <code>"Refund Request - [Course Name] - [Your Full Name]"</code> along with:
                    </p>
                    <ul class="list-disc list-inside space-y-1 text-gray-600 mt-2">
                        <li>Your registration email and contact number.</li>
                        <li>bKash / Nagad / Bank transaction reference number.</li>
                        <li>Brief reason for cancellation.</li>
                    </ul>
                    <p class="mt-2 text-xs text-gray-500">
                        Refund disbursements are processed via the original payment channel within 5–7 business days of written approval.
                    </p>
                </section>

                <section>
                    <h2 class="text-lg font-bold text-[#0f131f] mb-2">4. Support & Inquiries</h2>
                    <p>
                        If you have questions regarding payment transfers or refund verification, please reach out directly:
                    </p>
                    <div class="mt-3 p-4 bg-gray-50 rounded-xl border border-gray-200 text-xs sm:text-sm">
                        <p class="font-bold text-[#0f131f]">{{ $siteName }} Finance & Accounts Wing</p>
                        <p class="text-gray-600">Hotline: {{ \App\Models\Setting::getVal('contact_hotline', '01917-054664') }}</p>
                        <p class="text-gray-600">Email: {{ \App\Models\Setting::getVal('contact_email', 'commercialacademy868@gmail.com') }}</p>
                        <p class="text-gray-600">Campus: {{ \App\Models\Setting::getVal('contact_address', 'Mirpur-Pallabi, Dhaka, Bangladesh, 1216') }}</p>
                    </div>
                </section>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
