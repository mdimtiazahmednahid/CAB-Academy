@extends('layouts.public')

@section('title', 'About CAB Academy - Institutional Excellence & Trade Leadership')

@section('content')
<div class="bg-gradient-to-b from-white via-[#f8fafc] to-[#f2f4f7] py-12 lg:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Hero Section -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center mb-20">
            <div class="lg:col-span-7">
                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-black uppercase tracking-wider bg-[#c30102]/10 text-[#c30102] mb-4">
                    <i class="fas fa-award text-xs"></i> Premier Trade & Executive Institute
                </span>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-[#0f131f] tracking-tight leading-tight">
                    Empowering Bangladesh's Next Generation of <span class="text-[#c30102]">Global Trade Leaders</span>
                </h1>
                <p class="mt-5 text-base sm:text-lg text-[#35415b] leading-relaxed">
                    Commercial Academy Bangladesh (CAB Academy) is the national vanguard in specialized executive development. Founded to bridge the critical gap between theoretical academia and high-stakes commercial reality, we provide rigorous, practical masterclasses in Export-Import Management, Supply Chain Logistics, Customs VAT & Tax, and Corporate Governance.
                </p>
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="{{ route('catalog.index') }}" class="btn-ipdp-primary px-6 py-3.5 rounded-xl font-bold shadow-md inline-flex items-center gap-2 text-sm">
                        <i class="fas fa-graduation-cap"></i> Explore Executive Programs
                    </a>
                    <a href="{{ route('contact.index') }}" class="btn-ipdp-secondary px-6 py-3.5 rounded-xl font-bold shadow-sm inline-flex items-center gap-2 text-sm">
                        <i class="fas fa-handshake"></i> Corporate Training Inquiries
                    </a>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="relative">
                    <div class="absolute -inset-2 bg-gradient-to-r from-[#c30102] to-[#051c45] rounded-3xl blur-xl opacity-20 transform -rotate-1"></div>
                    <div class="relative bg-white rounded-3xl p-8 border border-[#e3e7f0] shadow-xl space-y-6">
                        <div class="flex items-center gap-4">
                            <img src="{{ asset('images/logo.png') }}" alt="CAB Academy Crest" class="w-16 h-16 rounded-2xl p-1 bg-white border border-gray-100 shadow-sm object-contain">
                            <div>
                                <h3 class="text-xl font-black text-[#0f131f] uppercase tracking-tight">CAB Academy</h3>
                                <span class="text-xs font-black text-[#c30102] uppercase tracking-widest">Learn, Grow, Lead.</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-100 text-center">
                            <div class="p-3 bg-slate-50 rounded-2xl">
                                <span class="block text-2xl font-black text-[#0f131f]">5,000+</span>
                                <span class="text-xs text-gray-500 font-semibold uppercase">Graduates Trained</span>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-2xl">
                                <span class="block text-2xl font-black text-[#c30102]">98%</span>
                                <span class="text-xs text-gray-500 font-semibold uppercase">Practical Curriculum</span>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-2xl">
                                <span class="block text-2xl font-black text-[#10b981]">15+</span>
                                <span class="text-xs text-gray-500 font-semibold uppercase">Fellow Faculty</span>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-2xl">
                                <span class="block text-2xl font-black text-[#051c45]">120+</span>
                                <span class="text-xs text-gray-500 font-semibold uppercase">Corporate Partners</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mission, Vision & Core Values -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-20">
            <div class="bg-white rounded-3xl p-8 border border-[#e3e7f0] shadow-sm hover:shadow-xl transition-all">
                <div class="w-12 h-12 rounded-2xl bg-[#c30102]/10 text-[#c30102] flex items-center justify-center text-xl mb-6">
                    <i class="fas fa-bullseye"></i>
                </div>
                <h3 class="text-xl font-bold text-[#0f131f] mb-3">Our Mission</h3>
                <p class="text-sm text-[#35415b] leading-relaxed">
                    To engineer real-world competency and career breakthroughs for professionals through industry-vetted coursework, simulated trade laboratories, and personalized mentoring by C-suite practitioners.
                </p>
            </div>

            <div class="bg-white rounded-3xl p-8 border border-[#e3e7f0] shadow-sm hover:shadow-xl transition-all">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-[#10b981] flex items-center justify-center text-xl mb-6">
                    <i class="fas fa-eye"></i>
                </div>
                <h3 class="text-xl font-bold text-[#0f131f] mb-3">Our Vision</h3>
                <p class="text-sm text-[#35415b] leading-relaxed">
                    To be South Asia's preeminent institute for commercial trade, customs navigation, and supply chain excellence, recognized worldwide for ethical leadership and operational mastery.
                </p>
            </div>

            <div class="bg-white rounded-3xl p-8 border border-[#e3e7f0] shadow-sm hover:shadow-xl transition-all">
                <div class="w-12 h-12 rounded-2xl bg-blue-100 text-[#051c45] flex items-center justify-center text-xl mb-6">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3 class="text-xl font-bold text-[#0f131f] mb-3">Core Philosophy</h3>
                <p class="text-sm text-[#35415b] leading-relaxed">
                    Zero Fluff, 100% Applicability. Every case study, L/C draft, bill of lading, and VAT audit calculation directly reflects the current regulatory landscape of the National Board of Revenue (NBR) and Bangladesh Bank.
                </p>
            </div>
        </div>

        <!-- Why Choose CAB Academy -->
        <div class="bg-white rounded-3xl p-8 sm:p-12 border border-[#e3e7f0] shadow-sm mb-20">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-xs font-black tracking-widest text-[#c30102] uppercase block mb-1">Institutional Excellence</span>
                <h2 class="text-2xl sm:text-3xl font-black text-[#0f131f]">The CAB Academy Distinct Advantage</h2>
                <p class="text-sm text-[#727f9f] mt-2">What sets our executive diploma and day-long masterclasses apart.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-[#c30102]/10 text-[#c30102] flex items-center justify-center shrink-0 font-bold">01</div>
                    <div>
                        <h4 class="font-bold text-[#0f131f] text-base mb-1">Authentic Industry Practitioners</h4>
                        <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">Learn directly from Directors, Former Customs Commissioners, and Supply Chain Heads with 20+ years of frontline expertise.</p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-[#10b981] flex items-center justify-center shrink-0 font-bold">02</div>
                    <div>
                        <h4 class="font-bold text-[#0f131f] text-base mb-1">Verifiable Digital Credentials</h4>
                        <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">Instant online QR verification for corporate HR and global employers, authenticating your graduation credentials securely.</p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 font-bold">03</div>
                    <div>
                        <h4 class="font-bold text-[#0f131f] text-base mb-1">Mirpur-Pallabi Campus</h4>
                        <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">State-of-the-art multimedia training suites conveniently located in Mirpur-Pallabi with dedicated hands-on trade simulation workstations.</p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 font-bold">04</div>
                    <div>
                        <h4 class="font-bold text-[#0f131f] text-base mb-1">Job Placement Network</h4>
                        <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">Exclusive talent referrals and career board connecting top-scoring students to leading RMG, freight, and FMCG employers.</p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 font-bold">05</div>
                    <div>
                        <h4 class="font-bold text-[#0f131f] text-base mb-1">Hybrid & Flexible Schedules</h4>
                        <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">Weekend day-long workshops, evening certificate batches, and on-demand video access designed for working executives.</p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shrink-0 font-bold">06</div>
                    <div>
                        <h4 class="font-bold text-[#0f131f] text-base mb-1">Lifetime Alumni Community</h4>
                        <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">Continuous access to trade policy updates, NBR regulatory gazette briefings, and executive peer forums.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Campus & Executive Headquarters Banner -->
        <div class="bg-gradient-to-r from-[#031330] via-[#051c45] to-[#0a2540] rounded-3xl p-8 sm:p-12 text-white flex flex-col md:flex-row justify-between items-center gap-8 shadow-2xl relative overflow-hidden border border-[#1e2d4a]">
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-[#c30102]/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="max-w-2xl relative z-10">
                <span class="text-xs font-black uppercase tracking-widest text-[#10b981] block mb-2">Visit Our Campus</span>
                <h3 class="text-2xl sm:text-3xl font-black">Experience the Campus at Mirpur-Pallabi</h3>
                <p class="text-sm text-slate-300 mt-2 leading-relaxed">
                    Mirpur-Pallabi, Dhaka, Bangladesh, 1216. Our admissions counselors are available for in-person consultation Saturday through Thursday.
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 shrink-0 relative z-10">
                <a href="{{ route('contact.index') }}" class="btn-ipdp-primary px-6 py-3 rounded-xl font-bold text-center text-sm shadow-md">
                    Schedule a Visit
                </a>
                <a href="tel:01917054664" class="px-6 py-3 rounded-xl font-bold text-center text-sm bg-white/10 hover:bg-white/20 text-white transition-colors border border-white/20">
                    Call 01917-054664
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
