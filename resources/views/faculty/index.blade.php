@extends('layouts.public')

@section('title', 'Distinguished Faculty & Industry Fellows')

@section('content')
<div>

    <!-- Hero Banner -->
    <section class="bg-[#90c642] text-white py-12 border-b border-[#e3e7f0]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white mb-3">
                Distinguished Faculty & Fellows
            </h1>
            <p class="text-sm sm:text-base text-white/90 max-w-2xl mx-auto">
                Instruction and corporate mentorship led by high-profile trade finance specialists, corporate legal counsels, and senior supply chain directors.
            </p>
            <div class="flex items-center justify-center gap-2 text-xs sm:text-sm text-white/90 font-medium mt-4">
                <a href="{{ url('/') }}" class="hover:underline">Home</a>
                <span>/</span>
                <span class="font-bold underline">Faculty</span>
            </div>
        </div>
    </section>

    <!-- Faculty Roster -->
    <section class="py-14 bg-[#f2f4f7]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @php
                $facultyMembers = [
                    [
                        'name' => 'Md. Delwar Hossain Bhuiyan',
                        'title' => 'Principal Trade Finance Fellow & Customs Consultant',
                        'expertise' => 'Banking, L/C, Customs & Shipping Procedure',
                        'avatar' => 'https://ipdpbd.com/wp-content/uploads/2025/06/Md.-Delwar-Hossain-Bhuiyan.jpg',
                        'bio' => 'Over 22 years of senior corporate experience advising multinational commercial ventures and leading banking clearing operations across Chittagong and Dhaka ports.'
                    ],
                    [
                        'name' => 'Asif Ahmed, FCS',
                        'title' => 'Distinguished Faculty & Corporate Legal Counsel',
                        'expertise' => 'VAT, Corporate Taxation & Business Compliance',
                        'avatar' => 'https://ipdpbd.com/wp-content/uploads/2025/06/Asif-Ahmmed.jpg',
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
                    <div class="bg-white rounded-2xl border border-[#e3e7f0] p-6 text-center shadow-sm hover:shadow-xl transition-all duration-300 group flex flex-col justify-between">
                        <div>
                            <div class="w-28 h-28 mx-auto rounded-full overflow-hidden mb-5 border-4 border-white shadow-md bg-gray-100 group-hover:scale-105 transition-transform duration-300">
                                <img src="{{ $f['avatar'] }}" alt="{{ $f['name'] }}" class="w-full h-full object-cover object-top" onerror="this.src='https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80'">
                            </div>

                            <h3 class="text-base font-bold text-[#0f131f] group-hover:text-[#f15a29] transition-colors leading-tight mb-1">
                                {{ $f['name'] }}
                            </h3>
                            <span class="text-xs text-[#f15a29] font-bold block mb-3">
                                {{ $f['title'] }}
                            </span>
                            <span class="inline-block bg-[#f2f4f7] text-[#35415b] text-[11px] font-semibold px-2.5 py-1 rounded-full mb-3">
                                {{ $f['expertise'] }}
                            </span>
                            <p class="text-xs text-[#727f9f] leading-relaxed line-clamp-3 mb-4">
                                {{ $f['bio'] }}
                            </p>
                        </div>

                        <a href="{{ route('catalog.index') }}" class="btn-ipdp-primary text-center py-2 rounded text-xs font-bold w-full">
                            Explore Sessions
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

</div>
@endsection
