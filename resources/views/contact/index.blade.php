@extends('layouts.public')

@section('title', 'Contact Us & Corporate Training Hub')
@section('meta_description', 'Contact CAB Academy campus in Mirpur-Pallabi, Dhaka. Official hotlines, corporate training inquiries, admissions counseling, and WhatsApp support.')
@section('canonical_url', route('contact.index'))

@section('content')
<div class="bg-gradient-to-b from-white via-[#f8fafc] to-[#f2f4f7] py-12 lg:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Breadcrumb & Title -->
        <div class="text-center max-w-3xl mx-auto mb-12">
            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-black uppercase tracking-wider bg-[#c30102]/10 text-[#c30102] mb-4">
                <i class="fas fa-headset text-xs"></i> Executive Helpdesk & Corporate Advisory
            </span>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-[#0f131f] tracking-tight">
                Connect With <span class="text-[#c30102]">CAB Academy</span>
            </h1>
            <p class="mt-4 text-base sm:text-lg text-[#35415b] leading-relaxed">
                Have questions about our professional certification programs, enterprise corporate training, or admission enrollment? Our advisory team is ready to assist you.
            </p>
        </div>

        @if(session('success'))
            <div class="max-w-4xl mx-auto mb-8 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center gap-3 shadow-sm animate-fade-in">
                <div class="w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0">
                    <i class="fas fa-check"></i>
                </div>
                <div>
                    <h4 class="font-bold text-sm">Message Sent Successfully</h4>
                    <p class="text-xs sm:text-sm text-emerald-700 mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="max-w-4xl mx-auto mb-8 p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl shadow-sm">
                <h4 class="font-bold text-sm mb-1 flex items-center gap-2">
                    <i class="fas fa-exclamation-circle text-red-600"></i> Please check the following details:
                </h4>
                <ul class="list-disc list-inside text-xs space-y-1 text-red-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
            
            <!-- Left Column: Contact Cards & Campus Details (5 cols) -->
            <div class="lg:col-span-5 space-y-6">
                
                <!-- Quick Contact Card -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-[#e3e7f0] shadow-sm">
                    <h3 class="text-xl font-bold text-[#0f131f] mb-6 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-[#051c45] text-white flex items-center justify-center text-sm">
                            <i class="fas fa-map-marker-alt"></i>
                        </span>
                        Campus & Headquarters
                    </h3>

                    <div class="space-y-6 text-sm text-[#35415b]">
                        <!-- Address -->
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-[#c30102]/10 text-[#c30102] flex items-center justify-center shrink-0">
                                <i class="fas fa-building text-base"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-[#0f131f] text-sm">Operational Campus Address</h4>
                                <p class="text-xs sm:text-sm text-gray-600 mt-1 leading-relaxed">
                                    {{ \App\Models\Setting::getVal('contact_address', 'Mirpur-Pallabi, Dhaka, Bangladesh, 1216') }}
                                </p>
                            </div>
                        </div>

                        <!-- Phone / Hotlines -->
                        @php
                            $contactHotline = \App\Models\Setting::getVal('contact_hotline', '01917-054664');
                            $contactHotlineTel = preg_replace('/[^0-9+]/', '', $contactHotline) ?: '01917054664';
                            $contactEmail = \App\Models\Setting::getVal('contact_email', 'commercialacademy868@gmail.com');
                            $contactWhatsapp = \App\Models\Setting::getVal('contact_whatsapp', '8801917054664');
                            $contactHours = \App\Models\Setting::getVal('contact_hours', '9:00 AM – 8:00 PM, Saturday to Thursday');
                        @endphp
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-[#10b981] flex items-center justify-center shrink-0">
                                <i class="fas fa-phone-alt text-base"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-[#0f131f] text-sm">Executive Hotline</h4>
                                <p class="text-xs sm:text-sm text-gray-600 mt-1">
                                    <a href="tel:{{ $contactHotlineTel }}" class="font-semibold text-[#0f131f] hover:text-[#c30102] transition-colors">
                                        {{ $contactHotline }}
                                    </a>
                                    <span class="text-xs text-gray-500 block">({{ $contactHours }})</span>
                                </p>
                            </div>
                        </div>

                        <!-- Email Support -->
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-envelope text-base"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-[#0f131f] text-sm">Official Inquiries & Admissions</h4>
                                <p class="text-xs sm:text-sm text-gray-600 mt-1">
                                    <a href="mailto:{{ $contactEmail }}" class="font-semibold text-[#0f131f] hover:text-[#c30102] transition-colors">
                                        {{ $contactEmail }}
                                    </a>
                                </p>
                            </div>
                        </div>

                        <!-- WhatsApp Instant Chat -->
                        <div class="pt-2">
                            <a 
                                href="https://wa.me/{{ $contactWhatsapp }}?text=Hello%20CAB%20Academy%2C%20I%20am%20inquiring%20about%20your%20executive%20courses." 
                                target="_blank"
                                rel="noopener noreferrer"
                                class="w-full flex items-center justify-center gap-3 py-3 px-4 bg-[#25D366] text-white font-bold rounded-xl hover:bg-[#20ba59] transition-colors shadow-sm text-sm"
                            >
                                <i class="fab fa-whatsapp text-lg"></i>
                                Instant WhatsApp Advisory
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Executive Corporate Brief -->
                <div class="bg-gradient-to-br from-[#031330] via-[#051c45] to-[#0a2540] rounded-3xl p-6 sm:p-8 text-white shadow-xl border border-[#1e2d4a]">
                    <span class="px-2.5 py-0.5 rounded text-[11px] font-black uppercase tracking-wider bg-[#c30102] text-white">B2B Customized Programs</span>
                    <h3 class="text-xl font-bold mt-3 mb-2">Corporate Workforce Upskilling</h3>
                    <p class="text-xs sm:text-sm text-slate-200 leading-relaxed mb-4">
                        We deliver customized on-site and virtual bootcamps in Export-Import documentation, Supply Chain, and Customs VAT/Taxation for multinational corporations, RMG manufacturers, and financial institutions.
                    </p>
                    <div class="flex items-center gap-2 text-xs font-semibold text-emerald-400">
                        <i class="fas fa-check-circle"></i> ISO-Aligned Executive Modules
                    </div>
                </div>

            </div>

            <!-- Right Column: Interactive Inquiry Form (7 cols) -->
            <div class="lg:col-span-7" x-data="{ inquiryType: 'course_admission' }">
                <div class="bg-white rounded-3xl p-6 sm:p-10 border border-[#e3e7f0] shadow-xl">
                    
                    <div class="mb-6">
                        <h2 class="text-2xl font-black text-[#0f131f]">Send an Executive Inquiry</h2>
                        <p class="text-xs sm:text-sm text-[#727f9f] mt-1">
                            Fill out the form below. An admission advisor or corporate representative will reach out within 24 hours.
                        </p>
                    </div>

                    <!-- Type Switcher Tabs -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 p-1.5 bg-[#f2f4f7] rounded-2xl mb-6 text-xs font-bold">
                        <button 
                            type="button"
                            @click="inquiryType = 'course_admission'"
                            :class="inquiryType === 'course_admission' ? 'bg-white text-[#c30102] shadow-sm' : 'text-[#35415b] hover:text-[#0f131f]'"
                            class="py-2.5 px-2 rounded-xl text-center transition-all cursor-pointer"
                        >
                            Course Admission
                        </button>
                        <button 
                            type="button"
                            @click="inquiryType = 'corporate'"
                            :class="inquiryType === 'corporate' ? 'bg-white text-[#c30102] shadow-sm' : 'text-[#35415b] hover:text-[#0f131f]'"
                            class="py-2.5 px-2 rounded-xl text-center transition-all cursor-pointer"
                        >
                            Corporate Training
                        </button>
                        <button 
                            type="button"
                            @click="inquiryType = 'consulting'"
                            :class="inquiryType === 'consulting' ? 'bg-white text-[#c30102] shadow-sm' : 'text-[#35415b] hover:text-[#0f131f]'"
                            class="py-2.5 px-2 rounded-xl text-center transition-all cursor-pointer"
                        >
                            Trade Advisory
                        </button>
                        <button 
                            type="button"
                            @click="inquiryType = 'general'"
                            :class="inquiryType === 'general' ? 'bg-white text-[#c30102] shadow-sm' : 'text-[#35415b] hover:text-[#0f131f]'"
                            class="py-2.5 px-2 rounded-xl text-center transition-all cursor-pointer"
                        >
                            General Inquiry
                        </button>
                    </div>

                    <form action="{{ route('contact.submit') }}" method="POST" class="space-y-5">
                        @csrf
                        <input type="hidden" name="inquiry_type" :value="inquiryType">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="name" class="block text-xs font-bold text-[#0f131f] uppercase tracking-wider mb-2">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="name" 
                                    name="name" 
                                    value="{{ old('name', auth()->user()?->name ?? '') }}" 
                                    required
                                    placeholder="e.g. Md. Imtiaz Ahmed"
                                    class="w-full px-4 py-3 rounded-xl border border-[#e3e7f0] bg-[#f8fafc] text-sm text-[#0f131f] focus:outline-none focus:ring-2 focus:ring-[#c30102] focus:bg-white transition-all font-medium"
                                >
                            </div>

                            <div>
                                <label for="email" class="block text-xs font-bold text-[#0f131f] uppercase tracking-wider mb-2">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    value="{{ old('email', auth()->user()?->email ?? '') }}" 
                                    required
                                    placeholder="e.g. name@company.com"
                                    class="w-full px-4 py-3 rounded-xl border border-[#e3e7f0] bg-[#f8fafc] text-sm text-[#0f131f] focus:outline-none focus:ring-2 focus:ring-[#c30102] focus:bg-white transition-all font-medium"
                                >
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="phone" class="block text-xs font-bold text-[#0f131f] uppercase tracking-wider mb-2">
                                    Mobile Number <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="phone" 
                                    name="phone" 
                                    value="{{ old('phone') }}" 
                                    required
                                    placeholder="e.g. 01917-054664"
                                    class="w-full px-4 py-3 rounded-xl border border-[#e3e7f0] bg-[#f8fafc] text-sm text-[#0f131f] focus:outline-none focus:ring-2 focus:ring-[#c30102] focus:bg-white transition-all font-medium"
                                >
                            </div>

                            <div>
                                <label for="organization" class="block text-xs font-bold text-[#0f131f] uppercase tracking-wider mb-2">
                                    Organization / University <span class="text-gray-400 font-normal">(Optional)</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="organization" 
                                    name="organization" 
                                    value="{{ old('organization') }}" 
                                    placeholder="e.g. Global Logistics Ltd. / University"
                                    class="w-full px-4 py-3 rounded-xl border border-[#e3e7f0] bg-[#f8fafc] text-sm text-[#0f131f] focus:outline-none focus:ring-2 focus:ring-[#c30102] focus:bg-white transition-all font-medium"
                                >
                            </div>
                        </div>

                        <div>
                            <label for="subject" class="block text-xs font-bold text-[#0f131f] uppercase tracking-wider mb-2">
                                Subject <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="subject" 
                                name="subject" 
                                value="{{ old('subject') }}" 
                                required
                                placeholder="e.g. Inquiry regarding L/C & Export-Import Batch Schedule"
                                class="w-full px-4 py-3 rounded-xl border border-[#e3e7f0] bg-[#f8fafc] text-sm text-[#0f131f] focus:outline-none focus:ring-2 focus:ring-[#c30102] focus:bg-white transition-all font-medium"
                            >
                        </div>

                        <div>
                            <label for="message" class="block text-xs font-bold text-[#0f131f] uppercase tracking-wider mb-2">
                                Detailed Message <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                id="message" 
                                name="message" 
                                rows="4" 
                                required
                                placeholder="Please let us know your queries, preferred batch timing, or corporate participant volume..."
                                class="w-full px-4 py-3 rounded-xl border border-[#e3e7f0] bg-[#f8fafc] text-sm text-[#0f131f] focus:outline-none focus:ring-2 focus:ring-[#c30102] focus:bg-white transition-all font-medium"
                            >{{ old('message') }}</textarea>
                        </div>

                        <div class="pt-2">
                            <button 
                                type="submit" 
                                class="w-full py-4 px-6 bg-[#c30102] hover:bg-[#a50102] text-white font-bold rounded-xl transition-all duration-300 shadow-xl hover:shadow-2xl flex items-center justify-center gap-2 cursor-pointer shadow-[#c30102]/25"
                            >
                                <i class="fas fa-paper-plane text-sm"></i>
                                Submit Message to Advisory Desk
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <!-- Campus Interactive Map & Directions Section -->
        <div class="mt-16 bg-white rounded-2xl border border-[#e3e7f0] p-6 sm:p-8 shadow-sm">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <div>
                    <h3 class="text-xl font-bold text-[#0f131f]">Mirpur-Pallabi Campus Location</h3>
                    <p class="text-xs sm:text-sm text-[#727f9f] mt-1">Conveniently accessible via Metro Rail (Pallabi Station) and Mirpur-10 transit hubs.</p>
                </div>
                <a 
                    href="https://maps.google.com/?q=Mirpur+Pallabi+Dhaka+1216" 
                    target="_blank" 
                    rel="noopener noreferrer"
                    class="btn-ipdp-secondary px-5 py-2.5 rounded-lg text-xs font-bold shadow-sm inline-flex items-center gap-2"
                >
                    <i class="fas fa-directions"></i> Get Directions on Google Maps
                </a>
            </div>

            <!-- Stylized Campus Map Frame -->
            <div class="w-full h-72 sm:h-96 rounded-xl overflow-hidden border border-gray-200 bg-slate-100 relative">
                <iframe 
                    title="CAB Academy Mirpur-Pallabi Campus"
                    src="https://maps.google.com/maps?q=Mirpur-Pallabi,%20Dhaka%201216,%20Bangladesh&t=&z=15&ie=UTF8&iwloc=&output=embed" 
                    width="100%" 
                    height="100%" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade"
                ></iframe>
            </div>
        </div>

    </div>
</div>
@endsection
