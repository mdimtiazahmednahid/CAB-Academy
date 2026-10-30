@extends('layouts.admin')

@section('header_title', 'Accounting & Financial Control')

@section('content')
<div class="space-y-8" x-data="{ tuitionModalOpen: false, expenseModalOpen: false, templateModalOpen: false }">

    <!-- Header & Action Toolbar -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#051c45] text-white">
                    <i class="fas fa-coins text-[#10b981] mr-1.5"></i> {{ $templateSettings['effective_institution_name'] ?? \App\Models\Setting::getVal('site_name', 'CAB Academy') }} &bull; Treasury
                </span>
                <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider">{{ $templateSettings['accounting_fiscal_year'] ?? 'FY 2026-2027' }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Institutional Accounting & Revenue</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $templateSettings['accounting_ledger_subtitle'] ?? 'Real-time ledger, campus cash tuition realization, operational expenses, and profitability analytics.' }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <button type="button" @click="templateModalOpen = true" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 text-xs font-bold shadow-sm inline-flex items-center gap-2 transition-all">
                <i class="fas fa-sliders-h text-[#051c45]"></i>
                <span>Template Options</span>
            </button>
            <a href="{{ route('admin.accounting.statement') }}" target="_blank" class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 text-xs font-bold shadow-sm inline-flex items-center gap-2 transition-all">
                <i class="fas fa-print text-gray-500"></i>
                <span>Print Statement</span>
            </a>
            <button @click="expenseModalOpen = true" class="px-4 py-2.5 rounded-xl border border-red-200 text-red-700 bg-red-50 hover:bg-red-100 text-xs font-bold shadow-sm inline-flex items-center gap-2 transition-all">
                <i class="fas fa-minus-circle text-red-600"></i>
                <span>Record Expense</span>
            </button>
            <button @click="tuitionModalOpen = true" class="px-4 py-2.5 rounded-xl bg-[#051c45] hover:bg-[#072559] text-white text-xs font-bold shadow-md shadow-blue-950/20 inline-flex items-center gap-2 transition-all">
                <i class="fas fa-plus-circle text-[#10b981]"></i>
                <span>Record Campus Receipt</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm font-semibold flex items-center gap-3 shadow-sm">
            <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- 5 Core Financial KPI Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        
        <!-- Total Realized Revenue -->
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Realized Revenue</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold">৳</span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-gray-900 tracking-tight">৳{{ number_format($totalRealizedRevenue, 2) }}</div>
                <div class="text-[11px] font-semibold text-emerald-600 mt-1 flex items-center gap-1">
                    <i class="fas fa-user-check"></i>
                    <span>{{ $verifiedEnrollmentsCount }} Verified Students</span>
                </div>
            </div>
        </div>

        <!-- Current Month Inflow -->
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Monthly Inflow</span>
                <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
                    <i class="fas fa-calendar-alt"></i>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-[#051c45] tracking-tight">৳{{ number_format($monthlyRealizedRevenue, 2) }}</div>
                <div class="text-[11px] font-semibold text-gray-500 mt-1">Current Billing Cycle</div>
            </div>
        </div>

        <!-- Total Operational Expenses -->
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Expenses</span>
                <span class="w-8 h-8 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-sm">
                    <i class="fas fa-file-invoice-dollar"></i>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-red-600 tracking-tight">৳{{ number_format($totalExpenses, 2) }}</div>
                <div class="text-[11px] font-semibold text-gray-500 mt-1">৳{{ number_format($monthlyExpenses, 2) }} this month</div>
            </div>
        </div>

        <!-- Net Cash Surplus -->
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Net Surplus</span>
                <span class="w-8 h-8 rounded-xl {{ $netCashFlow >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' }} flex items-center justify-center text-sm">
                    <i class="fas {{ $netCashFlow >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}"></i>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black {{ $netCashFlow >= 0 ? 'text-emerald-700' : 'text-red-600' }} tracking-tight">
                    ৳{{ number_format($netCashFlow, 2) }}
                </div>
                <div class="text-[11px] font-semibold text-gray-500 mt-1">Operating Cash Margin</div>
            </div>
        </div>

        <!-- Pending Receivables -->
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Pending Receivables</span>
                <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                    <i class="fas fa-clock"></i>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-amber-600 tracking-tight">৳{{ number_format($pendingReceivables, 2) }}</div>
                <div class="text-[11px] font-semibold text-amber-700 mt-1">Awaiting Trx Verification</div>
            </div>
        </div>

    </div>

    <!-- Middle Grid: Payment Channel Breakdown & Course Profitability -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Payment Methods Realization (4 cols) -->
        <div class="lg:col-span-5 bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-wallet text-[#051c45]"></i>
                    <span>Channel Realization</span>
                </h3>
                <span class="text-xs text-gray-400 font-semibold">{{ $paymentMethodStats->count() }} Gateways</span>
            </div>
            
            <div class="space-y-3.5">
                @forelse($paymentMethodStats as $mStat)
                    @php
                        $percentage = $totalRealizedRevenue > 0 ? round(($mStat->realized_amount / $totalRealizedRevenue) * 100, 1) : 0;
                    @endphp
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="flex items-center justify-between text-xs font-bold mb-1">
                            <span class="text-gray-800">{{ $mStat->provider }}</span>
                            <span class="text-emerald-700 font-mono">৳{{ number_format($mStat->realized_amount, 2) }} ({{ $percentage }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-[#051c45] h-1.5 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                        <div class="text-[10px] text-gray-400 mt-1.5 flex justify-between">
                            <span>Total Invocations: {{ $mStat->total_count }}</span>
                            <span>Direct Cleared</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 py-4 text-center">No payment transactions recorded yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Course-by-Course Tuition Realization (7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-graduation-cap text-[#c30102]"></i>
                    <span>Program Tuition Yield</span>
                </h3>
                <span class="text-xs text-gray-400 font-semibold">Active Catalog</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-400 font-bold uppercase text-[10px]">
                            <th class="py-2.5">Course Title</th>
                            <th class="py-2.5 text-center">Enrolled</th>
                            <th class="py-2.5 text-right">Fee</th>
                            <th class="py-2.5 text-right">Yield (৳)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($courseRevenueStats->take(6) as $cStat)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 pr-2 font-bold text-gray-900 max-w-[220px] truncate">
                                    {{ $cStat->title }}
                                </td>
                                <td class="py-3 text-center">
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 font-bold font-mono">
                                        {{ $cStat->verified_students_count }}
                                    </span>
                                </td>
                                <td class="py-3 text-right font-mono text-gray-600">
                                    {{ $cStat->formatted_price }}
                                </td>
                                <td class="py-3 text-right font-mono font-bold text-emerald-700">
                                    ৳{{ number_format($cStat->realized_revenue, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Main Financial Ledger (Transactions & Verified Invoices) -->
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
        
        <!-- Table Toolbar & Filters -->
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-black text-gray-900 tracking-tight">Tuition & Admission Ledger</h2>
                <p class="text-xs text-gray-500 mt-0.5">Comprehensive audit trail of online, campus cash, and corporate tuition receipts.</p>
            </div>

            <form method="GET" action="{{ route('admin.accounting.index') }}" class="flex flex-wrap items-center gap-2.5">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by student, email, TrxID, invoice..." 
                       class="text-xs px-3.5 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-1 focus:ring-[#051c45] w-full sm:w-64">

                <select name="status" class="text-xs px-3 py-2 rounded-xl border border-gray-300 bg-white focus:outline-none">
                    <option value="">All Statuses</option>
                    <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Verified</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                    Filter
                </button>

                @if(request()->anyFilled(['search', 'status', 'course_id', 'date_from', 'date_to']))
                    <a href="{{ route('admin.accounting.index') }}" class="px-3 py-2 text-xs font-bold text-gray-500 hover:text-gray-900">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600">
                <thead class="bg-gray-50 text-gray-700 uppercase tracking-wider text-[10px] font-bold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3.5">Invoice #</th>
                        <th class="px-6 py-3.5">Student</th>
                        <th class="px-6 py-3.5">Program</th>
                        <th class="px-6 py-3.5">Amount</th>
                        <th class="px-6 py-3.5">Channel / TrxID</th>
                        <th class="px-6 py-3.5 text-center">Status</th>
                        <th class="px-6 py-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($ledgerTransactions as $tx)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-6 py-4 font-mono font-bold text-gray-900 whitespace-nowrap">
                                <span class="px-2 py-1 rounded bg-slate-100 border border-slate-200">
                                    {{ $tx->invoice_number ?? 'CAB-INV-'.$tx->id }}
                                </span>
                                <div class="text-[10px] text-gray-400 mt-1 font-sans">{{ $tx->created_at->format('d M Y, h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $tx->user->name ?? 'Direct Applicant' }}</div>
                                <div class="text-[11px] text-gray-400">{{ $tx->user->email ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 max-w-[200px]">
                                <div class="font-bold text-gray-900 truncate">{{ $tx->course->title ?? 'N/A' }}</div>
                                <span class="text-[10px] text-gray-400 font-semibold">{{ $tx->course->batch_no ?? 'Batch 2026' }}</span>
                            </td>
                            <td class="px-6 py-4 font-mono font-extrabold text-sm {{ $tx->status === 'verified' ? 'text-emerald-700' : 'text-gray-900' }}">
                                ৳{{ number_format($tx->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 font-mono">
                                <div class="font-bold text-gray-800">{{ $tx->paymentMethod->provider_name ?? ucfirst($tx->payment_type ?? 'Campus Cash') }}</div>
                                <div class="text-[11px] text-gray-500 font-bold">Trx: {{ $tx->transaction_id }}</div>
                                @if($tx->sender_number)
                                    <div class="text-[10px] text-gray-400">From: {{ $tx->sender_number }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($tx->status === 'verified')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <i class="fas fa-check-circle mr-1 text-[10px]"></i> Verified
                                    </span>
                                @elseif($tx->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        <i class="fas fa-hourglass-half mr-1 text-[10px]"></i> Pending
                                    </span>
                                @elseif($tx->status === 'processing')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                        Processing
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-red-100 text-red-800 border border-red-200">
                                        Rejected
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <form action="{{ route('admin.payments.updateStatus', $tx) }}" method="POST" class="inline-flex items-center gap-1.5">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()" 
                                            class="text-[11px] font-semibold border-gray-300 rounded-lg py-1 pl-2 pr-6 shadow-sm focus:ring-[#051c45]">
                                        <option value="pending" {{ $tx->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="verified" {{ $tx->status === 'verified' ? 'selected' : '' }}>Verify</option>
                                        <option value="rejected" {{ $tx->status === 'rejected' ? 'selected' : '' }}>Reject</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                No ledger transactions found matching the filter criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ledgerTransactions->hasPages())
            <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                {{ $ledgerTransactions->links() }}
            </div>
        @endif
    </div>

    <!-- Operational Expenses Table -->
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-black text-gray-900 tracking-tight">Institutional Operational Expenses</h3>
                <p class="text-xs text-gray-500 mt-0.5">Faculty honorariums, campus venue rent, marketing ads, and gazette printing.</p>
            </div>
            <button @click="expenseModalOpen = true" class="px-3.5 py-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-700 text-xs font-bold transition-all flex items-center gap-1.5">
                <i class="fas fa-plus text-xs"></i>
                <span>Add Expense</span>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-400 font-bold uppercase text-[10px]">
                        <th class="py-2.5">Date</th>
                        <th class="py-2.5">Expense Title & Category</th>
                        <th class="py-2.5">Paid To</th>
                        <th class="py-2.5">Method</th>
                        <th class="py-2.5 text-right">Amount (৳)</th>
                        <th class="py-2.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentExpenses as $exp)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3 font-mono text-gray-500">{{ $exp->expense_date->format('d M Y') }}</td>
                            <td class="py-3">
                                <div class="font-bold text-gray-900">{{ $exp->title }}</div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-600 uppercase tracking-wider">
                                    {{ str_replace('_', ' ', $exp->category) }}
                                </span>
                            </td>
                            <td class="py-3 font-medium text-gray-800">{{ $exp->paid_to }}</td>
                            <td class="py-3 text-gray-500 font-mono">{{ ucfirst(str_replace('_', ' ', $exp->payment_method)) }}</td>
                            <td class="py-3 text-right font-mono font-bold text-red-600">৳{{ number_format($exp->amount, 2) }}</td>
                            <td class="py-3 text-right">
                                <form action="{{ route('admin.accounting.expenses.destroy', $exp) }}" method="POST" onsubmit="return confirm('Delete this expense?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400">
                                No expenses recorded yet. Click "Record Expense" to maintain balanced accounting.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal 1: Record Offline Campus Tuition Receipt -->
    <div x-show="tuitionModalOpen" style="display:none;" class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="tuitionModalOpen = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-gray-100 space-y-6">
            <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                <div>
                    <h3 class="text-lg font-black text-gray-900">Record Campus Tuition Receipt</h3>
                    <p class="text-xs text-gray-500">Record cash, corporate PO, or direct bank transfer admissions.</p>
                </div>
                <button @click="tuitionModalOpen = false" class="text-gray-400 hover:text-gray-700">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <form action="{{ route('admin.accounting.tuition.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Student Full Name *</label>
                        <input type="text" name="student_name" required placeholder="e.g. Md. Tariqul Islam" class="w-full text-xs rounded-xl border-gray-300 p-2.5 focus:ring-[#051c45]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Student Email *</label>
                        <input type="email" name="student_email" required placeholder="tariqul@gmail.com" class="w-full text-xs rounded-xl border-gray-300 p-2.5 focus:ring-[#051c45]">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Phone / WhatsApp</label>
                        <input type="tel" name="student_phone" placeholder="017xxxxxxxx" class="w-full text-xs rounded-xl border-gray-300 p-2.5 focus:ring-[#051c45]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Tuition Fee Paid (৳) *</label>
                        <input type="number" step="0.01" name="amount" required placeholder="e.g. 5000" class="w-full text-xs font-mono font-bold rounded-xl border-gray-300 p-2.5 focus:ring-[#051c45]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Enrolled Program *</label>
                    <select name="course_id" required class="w-full text-xs rounded-xl border-gray-300 p-2.5 focus:ring-[#051c45]">
                        <option value="">Select Course</option>
                        @foreach($allCourses as $c)
                            <option value="{{ $c->id }}">{{ $c->title }} ({{ $c->formatted_price }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Payment Channel *</label>
                        <select name="payment_type" required class="w-full text-xs rounded-xl border-gray-300 p-2.5 focus:ring-[#051c45]">
                            <option value="campus_cash">Mirpur Campus Cash</option>
                            <option value="bank_transfer">Corporate Bank Transfer</option>
                            <option value="bkash_merchant">bKash Merchant Direct</option>
                            <option value="corporate_batch">Corporate Sponsored Batch</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Receipt / Trx Reference</label>
                        <input type="text" name="transaction_id" placeholder="e.g. CASH-1049 or Bank Slip" class="w-full text-xs rounded-xl border-gray-300 p-2.5 focus:ring-[#051c45]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Accounting Notes</label>
                    <textarea name="notes" rows="2" placeholder="e.g. Collected at campus reception, printed invoice given to student." class="w-full text-xs rounded-xl border-gray-300 p-2.5 focus:ring-[#051c45]"></textarea>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2.5">
                    <button type="button" @click="tuitionModalOpen = false" class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-700 text-xs font-bold">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-[#051c45] hover:bg-[#072559] text-white text-xs font-bold rounded-xl shadow-md">
                        Record & Auto-Enroll Student
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 2: Record Operational Expense -->
    <div x-show="expenseModalOpen" style="display:none;" class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="expenseModalOpen = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-gray-100 space-y-6">
            <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                <div>
                    <h3 class="text-lg font-black text-gray-900">Record Institutional Expense</h3>
                    <p class="text-xs text-gray-500">Record honorarium, campus facility rent, gazette printing, or ads.</p>
                </div>
                <button @click="expenseModalOpen = false" class="text-gray-400 hover:text-gray-700">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <form action="{{ route('admin.accounting.expenses.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Expense Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Faculty Honorarium - Customs Masterclass" class="w-full text-xs rounded-xl border-gray-300 p-2.5 focus:ring-red-600">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Expense Category *</label>
                        <select name="category" required class="w-full text-xs rounded-xl border-gray-300 p-2.5 focus:ring-red-600">
                            <option value="faculty_honorarium">Faculty Honorarium</option>
                            <option value="campus_operations">Campus Operations / Rent</option>
                            <option value="materials_printing">Course Gazette / Printing</option>
                            <option value="marketing_ads">Marketing & Meta Ads</option>
                            <option value="it_hosting">IT Hosting & Cloud Infrastructure</option>
                            <option value="corporate_tax">Government Tax & Audit</option>
                            <option value="other">General Administration</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Amount (৳) *</label>
                        <input type="number" step="0.01" name="amount" required placeholder="e.g. 15000" class="w-full text-xs font-mono font-bold rounded-xl border-gray-300 p-2.5 focus:ring-red-600">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Expense Date *</label>
                        <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" required class="w-full text-xs rounded-xl border-gray-300 p-2.5 focus:ring-red-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Paid To (Beneficiary) *</label>
                        <input type="text" name="paid_to" required placeholder="e.g. Faculty Honorarium / Vendor Name" class="w-full text-xs rounded-xl border-gray-300 p-2.5 focus:ring-red-600">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Payment Method *</label>
                        <select name="payment_method" required class="w-full text-xs rounded-xl border-gray-300 p-2.5 focus:ring-red-600">
                            <option value="bank_transfer">Bank Wire Transfer</option>
                            <option value="cash">Campus Petty Cash</option>
                            <option value="bkash">bKash Corporate</option>
                            <option value="cheque">Bank Cheque</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Voucher / Cheque #</label>
                        <input type="text" name="reference_no" placeholder="e.g. VCH-2026-081" class="w-full text-xs rounded-xl border-gray-300 p-2.5 focus:ring-red-600">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Notes & Description</label>
                    <textarea name="notes" rows="2" placeholder="Details of the payment authorization..." class="w-full text-xs rounded-xl border-gray-300 p-2.5 focus:ring-red-600"></textarea>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2.5">
                    <button type="button" @click="expenseModalOpen = false" class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-700 text-xs font-bold">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl shadow-md">
                        Record Expense
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 3: Accounting Statement & Student Invoice Template Options -->
    <div x-show="templateModalOpen" style="display:none;" class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="templateModalOpen = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-gray-100 space-y-5">
            <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                <div>
                    <h3 class="text-lg font-black text-gray-900">Statement & Invoice Template Options</h3>
                    <p class="text-xs text-gray-500">Customize the institution name and branding shown on Financial Statements and Student Tuition Invoices.</p>
                </div>
                <button type="button" @click="templateModalOpen = false" class="text-gray-400 hover:text-gray-700">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <form action="{{ route('admin.accounting.template.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1">
                        Template Institution Name <span class="text-[10px] font-normal text-gray-400">(Leave blank to auto-use Global Site Name: "{{ \App\Models\Setting::getVal('site_name', 'CAB Academy') }}")</span>
                    </label>
                    <input
                        type="text"
                        name="accounting_institution_name"
                        value="{{ $templateSettings['accounting_institution_name'] ?? '' }}"
                        placeholder="{{ \App\Models\Setting::getVal('site_name', 'CAB Academy') }}"
                        class="w-full text-xs rounded-xl border-gray-300 p-2.5 focus:ring-[#051c45]"
                    >
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Invoice Number Prefix</label>
                        <input
                            type="text"
                            name="accounting_invoice_prefix"
                            value="{{ $templateSettings['accounting_invoice_prefix'] ?? 'INV' }}"
                            placeholder="INV"
                            class="w-full text-xs font-mono uppercase rounded-xl border-gray-300 p-2.5 focus:ring-[#051c45]"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Fiscal Year Label</label>
                        <input
                            type="text"
                            name="accounting_fiscal_year"
                            value="{{ $templateSettings['accounting_fiscal_year'] ?? 'FY 2026-27' }}"
                            placeholder="FY 2026-27"
                            class="w-full text-xs font-mono rounded-xl border-gray-300 p-2.5 focus:ring-[#051c45]"
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Statement / Invoice Subtitle</label>
                    <input
                        type="text"
                        name="accounting_ledger_subtitle"
                        value="{{ $templateSettings['accounting_ledger_subtitle'] ?? '' }}"
                        placeholder="Institutional Financial Ledger & Tuition Realization Audit"
                        class="w-full text-xs rounded-xl border-gray-300 p-2.5 focus:ring-[#051c45]"
                    >
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Authorized Signatory Title</label>
                        <input
                            type="text"
                            name="accounting_signatory_title"
                            value="{{ $templateSettings['accounting_signatory_title'] ?? '' }}"
                            placeholder="Director of Finance"
                            class="w-full text-xs rounded-xl border-gray-300 p-2.5 focus:ring-[#051c45]"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Footer / Verification Note</label>
                        <input
                            type="text"
                            name="accounting_footer_note"
                            value="{{ $templateSettings['accounting_footer_note'] ?? '' }}"
                            placeholder="Audit Document Verified & Sealed"
                            class="w-full text-xs rounded-xl border-gray-300 p-2.5 focus:ring-[#051c45]"
                        >
                    </div>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2.5">
                    <button type="button" @click="templateModalOpen = false" class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-700 text-xs font-bold">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-[#051c45] hover:bg-[#072559] text-white text-xs font-bold rounded-xl shadow-md">
                        Save Template Options
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
