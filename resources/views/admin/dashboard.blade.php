@extends('layouts.admin')

@section('header_title', 'Executive Command Center')

@section('content')
<div class="space-y-6">
    <!-- 1. Executive Command Masthead Banner (Imperial Navy + Ambient Glow + Quick Launch Bar) -->
    <div class="bg-gradient-to-br from-[#020c21] via-[#051c45] to-[#0b2d6b] rounded-3xl p-6 sm:p-7 text-white shadow-xl border border-white/10 relative overflow-hidden">
        <div class="absolute -top-24 -right-24 w-72 h-72 rounded-full bg-[#c30102]/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-72 h-72 rounded-full bg-emerald-500/15 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col xl:flex-row xl:items-center justify-between gap-6">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-2.5">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-500/15 border border-emerald-400/30 text-[10px] font-black uppercase tracking-wider text-emerald-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Executive Telemetry Active</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/10 border border-white/15 text-[10px] font-bold text-sky-200">
                        <i class="fas fa-calendar-check text-[9px]"></i>
                        <span>{{ now()->format('F d, Y') }}</span>
                    </span>
                    @if(($pendingPaymentsCount ?? 0) > 0)
                        <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-400/20 border border-amber-300/40 text-[10px] font-black uppercase tracking-wider text-amber-300 hover:bg-amber-400/30 transition-colors">
                            <i class="fas fa-bolt text-[9px]"></i>
                            <span>{{ $pendingPaymentsCount }} Payment {{ \Illuminate\Support\Str::plural('Approval', $pendingPaymentsCount) }} Needed</span>
                        </a>
                    @endif
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">Platform Overview &amp; Executive Command</h1>
                <p class="text-slate-300 text-xs sm:text-sm mt-1 max-w-2xl">Real-time financial realization, enrollment velocity, pending tuition approvals, career placement queue, and academic telemetry.</p>
            </div>

            <!-- Quick Launch Action Pills -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.courses') }}" class="px-4 py-2.5 rounded-xl bg-[#c30102] hover:bg-[#a50102] text-white text-xs font-extrabold shadow-lg shadow-[#c30102]/25 inline-flex items-center gap-2 transition-all">
                    <i class="fas fa-plus"></i>
                    <span>Manage Courses</span>
                </a>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.announcements.create') }}" class="px-3.5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white border border-white/15 text-xs font-bold inline-flex items-center gap-2 transition-all">
                        <i class="fas fa-bullhorn text-amber-300"></i>
                        <span>Broadcast Notice</span>
                    </a>
                    <a href="{{ route('admin.jobs') }}" class="px-3.5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white border border-white/15 text-xs font-bold inline-flex items-center gap-2 transition-all">
                        <i class="fas fa-briefcase text-sky-300"></i>
                        <span>Post Job</span>
                    </a>
                    <a href="{{ route('admin.accounting.index') }}" class="px-3.5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white border border-white/15 text-xs font-bold inline-flex items-center gap-2 transition-all">
                        <i class="fas fa-coins text-emerald-300"></i>
                        <span>Treasury &amp; Ledger</span>
                    </a>
                    <a href="{{ route('admin.frontend.index') }}" class="px-3.5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white border border-white/15 text-xs font-bold inline-flex items-center gap-2 transition-all">
                        <i class="fas fa-wand-magic-sparkles text-pink-300"></i>
                        <span>Customize Frontend</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs sm:text-sm font-semibold flex items-center gap-2.5 shadow-xs">
            <i class="fas fa-check-circle text-emerald-600"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- 2. 5-Card Liquid Glass Executive KPI Strip -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Realized Revenue -->
        <a href="{{ auth()->user()->isAdmin() ? route('admin.accounting.index') : '#' }}" class="liquid-glass-card bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-[#051c45]/30 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Realized Revenue</span>
                <span class="w-9 h-9 rounded-xl bg-emerald-50 border border-emerald-200/60 text-emerald-600 flex items-center justify-center font-black text-sm">৳</span>
            </div>
            <p class="text-2xl font-black text-[#051c45] tracking-tight">৳{{ number_format($totalRealizedRevenue ?? 0, 0) }}</p>
            <div class="flex items-center justify-between mt-1.5 text-[11px] font-semibold">
                <span class="text-emerald-600">+৳{{ number_format($monthlyRevenue ?? 0, 0) }} this mo</span>
                <span class="text-slate-400">Net: ৳{{ number_format($netOperatingSurplus ?? 0, 0) }}</span>
            </div>
        </a>

        <!-- Pending Approvals -->
        <a href="{{ auth()->user()->isAdmin() ? route('admin.payments.index') : '#' }}" class="liquid-glass-card bg-white p-5 rounded-2xl border {{ ($pendingPaymentsCount ?? 0) > 0 ? 'border-amber-300/90 bg-amber-50/20' : 'border-slate-200/80' }} shadow-xs hover:border-amber-400 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Pending Payments</span>
                <span class="w-9 h-9 rounded-xl {{ ($pendingPaymentsCount ?? 0) > 0 ? 'bg-amber-100 text-amber-700 border border-amber-300/60' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center text-sm">
                    <i class="fas fa-clock"></i>
                </span>
            </div>
            <p class="text-2xl font-black {{ ($pendingPaymentsCount ?? 0) > 0 ? 'text-amber-600' : 'text-[#051c45]' }} tracking-tight">
                {{ number_format($pendingPaymentsCount ?? 0) }}
            </p>
            <p class="text-[11px] font-semibold text-slate-500 mt-1.5">
                ৳{{ number_format($pendingReceivablesAmount ?? 0, 0) }} awaiting review
            </p>
        </a>

        <!-- Total Students & Enrollments -->
        <a href="{{ auth()->user()->isAdmin() ? route('admin.users') : '#' }}" class="liquid-glass-card bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-[#051c45]/30 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Students</span>
                <span class="w-9 h-9 rounded-xl bg-[#051c45]/[0.08] text-[#051c45] flex items-center justify-center text-sm">
                    <i class="fas fa-user-graduate"></i>
                </span>
            </div>
            <p class="text-2xl font-black text-[#051c45] tracking-tight">{{ number_format($totalStudents) }}</p>
            <p class="text-[11px] font-semibold text-slate-500 mt-1.5">
                {{ number_format($totalEnrollments ?? 0) }} Active Seat Enrollments
            </p>
        </a>

        <!-- Published Courses & Certificates -->
        <a href="{{ route('admin.courses') }}" class="liquid-glass-card bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-[#051c45]/30 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Published Courses</span>
                <span class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                    <i class="fas fa-book-open"></i>
                </span>
            </div>
            <p class="text-2xl font-black text-[#051c45] tracking-tight">{{ number_format($publishedCourses) }}</p>
            <p class="text-[11px] font-semibold text-slate-500 mt-1.5">
                {{ $draftCourses ?? 0 }} Drafts &bull; {{ number_format($totalCertificates ?? 0) }} Certs
            </p>
        </a>

        <!-- Quizzes & Career Placement -->
        <a href="{{ auth()->user()->isAdmin() ? route('admin.jobs') : '#' }}" class="liquid-glass-card bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-[#051c45]/30 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Quizzes &amp; Careers</span>
                <span class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-sm">
                    <i class="fas fa-chart-line"></i>
                </span>
            </div>
            <div class="flex items-baseline justify-between">
                <p class="text-2xl font-black text-[#051c45] tracking-tight">{{ number_format($totalAssessments) }}</p>
                <span class="px-2 py-0.5 rounded-full bg-emerald-50 border border-emerald-200/60 text-emerald-700 text-xs font-bold">{{ $averageScore }}% Avg</span>
            </div>
            <p class="text-[11px] font-semibold text-slate-500 mt-1.5">
                {{ $activeJobsCount ?? 0 }} Active Jobs &bull; {{ $pendingJobApplicationsCount ?? 0 }} Pending CVs
            </p>
        </a>
    </div>

    <!-- 3. Financial Telemetry Chart (8 cols) & Treasury Snapshot (4 cols) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
        <!-- 6-Month Revenue & Enrollment Velocity Chart -->
        <div class="lg:col-span-8 liquid-glass-card bg-white rounded-2xl border border-slate-200/80 p-6 flex flex-col justify-between">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-slate-100">
                <div>
                    <h2 class="font-extrabold text-sm text-[#051c45] uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-chart-column text-[#c30102]"></i>
                        <span>6-Month Revenue &amp; Enrollment Velocity</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Monthly verified tuition inflow vs operating expenses and student enrollment volume.</p>
                </div>
                <div class="flex items-center gap-3 text-[11px] font-bold">
                    <span class="inline-flex items-center gap-1.5 text-[#051c45]">
                        <span class="w-2.5 h-2.5 rounded-xs bg-[#051c45]"></span> Realized Tuition
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-rose-600">
                        <span class="w-2.5 h-2.5 rounded-xs bg-rose-500"></span> Expenses
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-6 gap-2 sm:gap-4 items-end pt-6 pb-2 h-48">
                @foreach($monthlyTrend ?? [] as $m)
                    @php
                        $revPct = max(8, min(100, round((($m['revenue'] ?? 0) / max(1, $maxMonthlyRevenue ?? 1)) * 100)));
                        $expPct = max(4, min(100, round((($m['expenses'] ?? 0) / max(1, $maxMonthlyRevenue ?? 1)) * 100)));
                    @endphp
                    <div class="flex flex-col items-center h-full justify-end group">
                        <div class="text-[10px] font-bold text-[#051c45] mb-1 opacity-85 group-hover:opacity-100">
                            ৳{{ $m['revenue'] >= 1000 ? number_format($m['revenue'] / 1000, 1) . 'k' : number_format($m['revenue'], 0) }}
                        </div>
                        <div class="w-full max-w-[54px] flex items-end justify-center gap-1 h-28 bg-slate-50/80 rounded-xl p-1.5 border border-slate-100">
                            <div class="w-1/2 bg-gradient-to-t from-[#051c45] to-[#1e40af] rounded-md transition-all duration-500" style="height: {{ $m['revenue'] > 0 ? $revPct : 6 }}%" title="Revenue: ৳{{ number_format($m['revenue'], 0) }}"></div>
                            <div class="w-1/2 bg-gradient-to-t from-[#c30102] to-rose-400 rounded-md transition-all duration-500" style="height: {{ $m['expenses'] > 0 ? $expPct : 4 }}%" title="Expenses: ৳{{ number_format($m['expenses'], 0) }}"></div>
                        </div>
                        <span class="text-[11px] font-extrabold text-slate-700 mt-2">{{ $m['short_label'] }}</span>
                        <span class="text-[10px] text-slate-400 font-semibold">{{ $m['enrollments'] }} {{ \Illuminate\Support\Str::plural('seat', $m['enrollments']) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Executive Treasury & Compliance Summary Card -->
        <div class="lg:col-span-4 liquid-glass-card bg-white rounded-2xl border border-slate-200/80 p-6 flex flex-col justify-between space-y-4">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h2 class="font-extrabold text-sm text-[#051c45] uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-vault text-emerald-600"></i>
                        <span>Treasury Summary</span>
                    </h2>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.accounting.statement') }}" target="_blank" class="text-[11px] font-bold text-[#051c45] hover:text-[#c30102] inline-flex items-center gap-1">
                            <i class="fas fa-print text-[10px]"></i> Statement
                        </a>
                    @endif
                </div>

                <div class="space-y-3 pt-3 text-xs">
                    <div class="flex items-center justify-between p-3 rounded-xl bg-emerald-50/70 border border-emerald-200/60">
                        <span class="font-semibold text-emerald-900">Gross Verified Tuition</span>
                        <span class="font-black text-emerald-700 text-sm">৳{{ number_format($totalRealizedRevenue ?? 0, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-xl bg-rose-50/70 border border-rose-200/60">
                        <span class="font-semibold text-rose-900">Total Operating Expenses</span>
                        <span class="font-black text-rose-700 text-sm">৳{{ number_format($totalExpenses ?? 0, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-xl bg-[#051c45]/[0.05] border border-[#051c45]/15">
                        <span class="font-extrabold text-[#051c45]">Net Operating Surplus</span>
                        <span class="font-black {{ ($netOperatingSurplus ?? 0) >= 0 ? 'text-[#051c45]' : 'text-rose-600' }} text-sm">
                            ৳{{ number_format($netOperatingSurplus ?? 0, 2) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-xl bg-amber-50/70 border border-amber-200/60">
                        <span class="font-semibold text-amber-900">Pending Receivables ({{ $pendingPaymentsCount ?? 0 }})</span>
                        <span class="font-black text-amber-700 text-sm">৳{{ number_format($pendingReceivablesAmount ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100">
                <a href="{{ route('admin.certificates.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-[#051c45] text-[#051c45] hover:text-white text-center text-xs font-bold transition-colors">
                    <i class="fas fa-award mr-1"></i> Certificates
                </a>
                <a href="{{ route('admin.categories.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-[#051c45] text-[#051c45] hover:text-white text-center text-xs font-bold transition-colors">
                    <i class="fas fa-layer-group mr-1"></i> Categories
                </a>
            </div>
        </div>
    </div>

    <!-- 4. Middle 2-Column Operational Workspace: Pending Verifications & Top Courses -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left (6 cols): Action Required — Pending Tuition Payments -->
        <div class="lg:col-span-6 liquid-glass-card bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full {{ ($pendingPaymentsCount ?? 0) > 0 ? 'bg-amber-500 animate-pulse' : 'bg-emerald-500' }}"></span>
                    <h2 class="font-extrabold text-sm text-[#051c45] uppercase tracking-wider">Pending Payment Approvals</h2>
                </div>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.payments.index') }}" class="text-xs font-bold text-[#051c45] hover:text-[#c30102] transition-colors">
                        View All ({{ $pendingPaymentsCount ?? 0 }}) →
                    </a>
                @endif
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($pendingPayments ?? [] as $payment)
                    @php
                        $pMode = $payment->form_data['training_mode'] ?? 'online_zoom';
                    @endphp
                    <div class="px-6 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/80 transition-colors">
                        <div class="min-w-0">
                            <div class="flex items-center flex-wrap gap-1.5">
                                <span class="text-xs font-extrabold text-slate-900 truncate">{{ $payment->user?->name ?? 'Student' }}</span>
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-[#051c45] font-mono text-[10px] font-bold">
                                    {{ $payment->paymentMethod?->provider_name ?? 'bKash/Direct' }}: {{ $payment->transaction_id }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold {{ $pMode === 'in_person' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-sky-50 text-sky-700 border border-sky-200' }}">
                                    {{ $pMode === 'in_person' ? 'Campus' : 'Online Zoom' }}
                                </span>
                            </div>
                            <div class="text-xs text-slate-500 truncate mt-1">
                                {{ $payment->course?->title ?? 'Course' }} &bull; Sender: <span class="font-mono font-bold text-slate-700">{{ $payment->sender_number }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="text-xs font-black text-[#051c45] mr-1">৳{{ number_format($payment->amount, 0) }}</span>
                            <a href="{{ route('payments.invoice', $payment) }}" target="_blank" title="View Receipt" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs transition-colors">
                                <i class="fas fa-file-invoice"></i>
                            </a>
                            @if(auth()->user()->isAdmin())
                                <form action="{{ route('admin.payments.updateStatus', $payment) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="status" value="verified">
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-[#10b981] hover:bg-[#059669] text-white text-[11px] font-bold transition-colors shadow-xs cursor-pointer">
                                        Approve
                                    </button>
                                </form>
                                <form action="{{ route('admin.payments.updateStatus', $payment) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-[11px] font-bold transition-colors cursor-pointer" title="Reject Payment">
                                        Reject
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-10 text-center">
                        <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-check"></i>
                        </div>
                        <p class="text-xs font-bold text-slate-700">All tuition payments are verified!</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">No pending transactions in the queue.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right (6 cols): Top Performing Courses & Cohorts -->
        <div class="lg:col-span-6 liquid-glass-card bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas fa-trophy text-[#051c45] text-xs"></i>
                    <h2 class="font-extrabold text-sm text-[#051c45] uppercase tracking-wider">Course Performance</h2>
                </div>
                <a href="{{ route('admin.courses') }}" class="text-xs font-bold text-[#051c45] hover:text-[#c30102] transition-colors">
                    All Courses →
                </a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($topCourses ?? [] as $course)
                    <div class="px-6 py-3.5 flex items-center justify-between gap-4 hover:bg-slate-50/80 transition-colors">
                        <div class="flex items-center gap-3 min-w-0">
                            <img src="{{ $course->cover_image_url }}" alt="{{ $course->title }}" class="w-12 h-9 rounded-lg object-cover border border-slate-200 shrink-0" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=300&q=80';">
                            <div class="min-w-0">
                                <a href="{{ route('admin.courses.show', $course) }}" class="text-xs font-bold text-slate-900 hover:text-[#051c45] truncate block">
                                    {{ $course->title }}
                                </a>
                                <div class="text-[11px] text-slate-500 flex items-center flex-wrap gap-1.5 mt-0.5">
                                    <span class="font-semibold text-[#051c45]">{{ $course->students_count }} {{ \Illuminate\Support\Str::plural('Student', $course->students_count) }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $course->lessons_count }} Lessons</span>
                                    <span>&bull;</span>
                                    <span class="px-1.5 py-0.2 rounded bg-slate-100 text-slate-600 text-[10px] font-bold">
                                        {{ $course->isOnlineOnly() ? 'Online Zoom' : ($course->isCampusOnly() ? 'Campus' : 'Hybrid') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <div class="text-right">
                                <div class="text-xs font-black text-emerald-700">৳{{ number_format((float) ($course->realized_revenue ?? 0), 0) }}</div>
                                <div class="text-[10px] text-slate-400">Fee: {{ $course->formatted_price }}</div>
                            </div>
                            <a href="{{ route('admin.courses.show', $course) }}" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-[#051c45] text-[#051c45] hover:text-white text-[11px] font-bold transition-colors">
                                Manage
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-10 text-center text-xs text-slate-400">
                        No courses created yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- 5. Recent Student Registrations & Career Job Applications Queue -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Recent Student Registrations (6 cols) -->
        <div class="lg:col-span-6 liquid-glass-card bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas fa-users text-[#051c45] text-xs"></i>
                    <h3 class="font-extrabold text-sm text-[#051c45] uppercase tracking-wider">Recent Student Accounts</h3>
                </div>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.users') }}" class="text-xs font-bold text-[#051c45] hover:text-[#c30102] transition-colors">
                        Directory ({{ $totalStudents }}) →
                    </a>
                @endif
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($recentStudents ?? [] as $stu)
                    <div class="px-6 py-3 flex items-center justify-between gap-3 hover:bg-slate-50/70 transition-colors">
                        <div class="flex items-center gap-3 min-w-0">
                            @if($stu->hasCustomProfilePicture())
                                <img src="{{ $stu->profile_picture_url }}" alt="{{ $stu->name }}" class="w-8 h-8 rounded-lg object-cover border border-slate-200 shrink-0" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($stu->name) }}&background=051c45&color=fff';">
                            @else
                                <div class="w-8 h-8 rounded-lg bg-[#051c45] text-white font-black text-xs flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($stu->name ?? 'S', 0, 1)) }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <div class="text-xs font-bold text-slate-900 truncate">{{ $stu->name }}</div>
                                <div class="text-[11px] text-slate-500 truncate">
                                    {{ $stu->phone ?: $stu->email }}
                                </div>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="px-2 py-0.5 rounded-full bg-emerald-50 border border-emerald-200/70 text-emerald-700 text-[10px] font-bold">
                                {{ $stu->enrolled_courses_count }} {{ \Illuminate\Support\Str::plural('Course', $stu->enrolled_courses_count) }}
                            </span>
                            <div class="text-[10px] text-slate-400 mt-0.5">{{ $stu->created_at?->diffForHumans() }}</div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-xs text-slate-400">No students registered yet.</div>
                @endforelse
            </div>
        </div>

        <!-- Career Portal Job Applications Queue (6 cols) -->
        <div class="lg:col-span-6 liquid-glass-card bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas fa-briefcase text-[#c30102] text-xs"></i>
                    <h3 class="font-extrabold text-sm text-[#051c45] uppercase tracking-wider">Recent Career Applications</h3>
                </div>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.jobs') }}" class="text-xs font-bold text-[#051c45] hover:text-[#c30102] transition-colors">
                        Manage Jobs ({{ $activeJobsCount ?? 0 }}) →
                    </a>
                @endif
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($recentJobApplications ?? [] as $app)
                    <div class="px-6 py-3 flex items-center justify-between gap-3 hover:bg-slate-50/70 transition-colors">
                        <div class="min-w-0">
                            <div class="text-xs font-bold text-slate-900 truncate">
                                {{ $app->user?->name ?? 'Candidate' }}
                                <span class="text-slate-400 font-normal">&rarr;</span>
                                <span class="text-[#051c45]">{{ $app->jobPost?->title ?? 'Position' }}</span>
                            </div>
                            <div class="text-[11px] text-slate-500 truncate mt-0.5">
                                {{ $app->jobPost?->company ?? 'Employer' }} &bull; Applied {{ $app->created_at?->diffForHumans() }}
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $app->status === 'accepted' ? 'bg-emerald-50 text-emerald-700' : ($app->status === 'rejected' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700 border border-amber-200') }}">
                                {{ ucfirst($app->status ?? 'pending') }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-xs text-slate-400">No job applications submitted yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- 6. Bottom 2-Column Feed: Recent Quiz Activity & Recent Inquiries -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Recent Quiz Activity (7 cols) -->
        <div class="lg:col-span-7 liquid-glass-card bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-extrabold text-sm text-[#051c45] uppercase tracking-wider">Recent Quiz Activity</h3>
                <span class="text-xs text-slate-400 font-medium">Latest Assessments</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[11px] border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5 font-semibold">Student</th>
                            <th class="px-6 py-3.5 font-semibold">Quiz</th>
                            <th class="px-6 py-3.5 font-semibold">Course</th>
                            <th class="px-6 py-3.5 font-semibold text-right">Score</th>
                            <th class="px-6 py-3.5 font-semibold text-right">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentActivity as $attempt)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-6 py-3.5 font-bold text-slate-900">
                                    {{ $attempt->user?->name ?? 'Unknown Student' }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-600">
                                    {{ $attempt->quiz?->title ?? 'Deleted Quiz' }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-500 truncate max-w-[160px]">
                                    {{ $attempt->quiz?->course?->title ?? 'Deleted Course' }}
                                </td>
                                <td class="px-6 py-3.5 text-right">
                                    @php
                                        $percentage = $attempt->total_questions > 0 ? round(($attempt->score / $attempt->total_questions) * 100) : 0;
                                        $color = $percentage >= 80 ? 'text-emerald-700 bg-emerald-50 border border-emerald-200' : ($percentage >= 50 ? 'text-amber-700 bg-amber-50 border border-amber-200' : 'text-rose-700 bg-rose-50 border border-rose-200');
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $color }}">
                                        {{ $percentage }}%
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-right text-slate-400 whitespace-nowrap">
                                    {{ $attempt->created_at->diffForHumans() }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-slate-400">
                                    No recent quiz activity found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Student & Corporate Inquiries (5 cols) -->
        <div class="lg:col-span-5 liquid-glass-card bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-extrabold text-sm text-[#051c45] uppercase tracking-wider">Recent Inquiries</h3>
                <a href="{{ route('admin.inquiries.index') }}" class="text-xs font-bold text-[#051c45] hover:text-[#c30102] transition-colors">
                    Inbox ({{ $pendingInquiriesCount ?? 0 }} Pending) →
                </a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($recentInquiries ?? [] as $inq)
                    <div class="px-6 py-3.5 hover:bg-slate-50/70 transition-colors">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-bold text-slate-900">{{ $inq->name }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $inq->status === 'pending' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-emerald-50 text-emerald-700' }}">
                                {{ ucfirst($inq->status) }}
                            </span>
                        </div>
                        <div class="text-[11px] text-slate-500 mt-0.5">
                            {{ $inq->phone ?? $inq->email }} &bull; {{ $inq->subject ?? 'Course Counseling' }}
                        </div>
                        <p class="text-xs text-slate-600 line-clamp-1 mt-1">{{ $inq->message }}</p>
                    </div>
                @empty
                    <div class="px-6 py-10 text-center text-xs text-slate-400">
                        No counseling inquiries received yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
