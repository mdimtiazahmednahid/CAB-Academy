@extends('layouts.public')

@section('title', 'Distinguished Faculty & Industry Fellows')

@section('content')
<div>

    <!-- Hero Banner (Modern Imperial Navy Masthead) -->
    <section class="bg-gradient-to-r from-[#031330] via-[#051c45] to-[#0a2540] text-white py-14 sm:py-18 border-b border-[#1e2d4a] relative overflow-hidden">
        <!-- Ambient Glow -->
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-[#c30102]/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-[#10b981]/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-[#10b981] text-xs font-black uppercase tracking-wider mb-4 shadow-sm">
                <i class="fas fa-user-tie text-xs"></i>
                <span>Corporate Mentorship & Governance</span>
            </div>

            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white mb-3">
                Distinguished Faculty & Fellows
            </h1>
            <p class="text-xs sm:text-base text-slate-300 max-w-2xl mx-auto leading-relaxed">
                Instruction and corporate mentorship led by high-profile trade finance specialists, corporate legal counsels, and senior supply chain directors.
            </p>
            <div class="flex items-center justify-center gap-2 text-xs sm:text-sm text-slate-300 font-medium mt-5">
                <a href="{{ url('/') }}" class="hover:text-white transition-colors">Home</a>
                <span class="text-slate-500">/</span>
                <span class="font-bold text-white underline">Faculty</span>
            </div>
        </div>
    </section>

    <!-- Faculty Roster -->
    <section class="py-16 bg-[#f8fafc]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @php
                $facultyMembers = [
                    [
                        'name' => 'Md. Delwar Hossain Bhuiyan',
                        'title' => 'Principal Trade Finance Fellow & Customs Consultant',
                        'expertise' => 'Banking, L/C, Customs & Shipping Procedure',
                        'avatar' => asset('images/faculty/delwar-hossain.jpg'),
                        'bio' => 'Over 22 years of senior corporate experience advising multinational commercial ventures and leading banking clearing operations across Chittagong and Dhaka ports.'
                    ],
                    [
                        'name' => 'Asif Ahmed, FCS',
                        'title' => 'Distinguished Faculty & Corporate Legal Counsel',
                        'expertise' => 'VAT, Corporate Taxation & Business Compliance',
                        'avatar' => asset('images/faculty/asif-ahmmed.jpg'),
                        'bio' => 'Renowned corporate tax advisor and fellow chartered secretary specializing in corporate tax assessment, audit dispute resolution, and NBR compliance.'
                    ],
                    [
                        'name' => 'Engr. Tariqul Islam',
                        'title' => 'Faculty Chair & Head of SCM Operations',
                        'expertise' => 'Supply Chain Strategy & Sourcing Excellence',
                        'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=300&q=80',
                        'bio' => 'Supply chain architect with extensive background directing international procurement pipelines, safety stock forecasting, and vendor negotiations.'
                    ],
                    [
                        'name' => 'Dr. K. M. Saiful Islam',
                        'title' => 'Senior Industry Fellow in Human Resource Governance',
                        'expertise' => 'Labor Laws, Compliance & Organizational Strategy',
                        'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=300&q=80',
                        'bio' => 'Leading labor court mediator and senior human resource consultant specializing in industrial disputes, workplace safety, and talent performance.'
                    ],
                ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($facultyMembers as $f)
                    <div class="bg-white rounded-3xl border border-[#e3e7f0] p-6 text-center shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group flex flex-col justify-between hover:border-[#c30102]/30">
                        <div>
                            <div class="w-28 h-28 mx-auto rounded-full overflow-hidden mb-5 border-4 border-slate-100 shadow-md bg-gray-100 group-hover:scale-105 group-hover:ring-4 group-hover:ring-[#c30102]/20 transition-all duration-300">
                                <img src="{{ $f['avatar'] }}" alt="{{ $f['name'] }}" class="w-full h-full object-cover object-top" onerror="this.src='https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80'">
                            </div>

                            <h3 class="text-base font-extrabold text-[#0f131f] group-hover:text-[#c30102] transition-colors leading-tight mb-1.5">
                                {{ $f['name'] }}
                            </h3>
                            <span class="text-xs text-[#c30102] font-bold block mb-3">
                                {{ $f['title'] }}
                            </span>
                            <span class="inline-block bg-emerald-50 text-emerald-800 border border-emerald-200 text-[11px] font-bold px-3 py-1 rounded-full mb-4">
                                {{ $f['expertise'] }}
                            </span>
                            <p class="text-xs text-[#727f9f] leading-relaxed line-clamp-3 mb-5">
                                {{ $f['bio'] }}
                            </p>
                        </div>

                        <a href="{{ route('catalog.index') }}" class="btn-ipdp-primary text-center py-2.5 rounded-xl text-xs font-bold w-full shadow-sm">
                            Explore Sessions
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

</div>
@endsection
