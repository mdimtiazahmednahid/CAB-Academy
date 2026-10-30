@extends('layouts.admin')

@section('header_title', 'Tuition Payments & Verification')

@section('content')
@php
    $pendingList = $payments->where('status', 'pending');
    $verifiedList = $payments->where('status', 'verified');
    $rejectedList = $payments->where('status', 'rejected');
@endphp

<div class="space-y-6" x-data="{ statusTab: 'all', search: '' }">
    <!-- Header & Cross-Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl sm:text-3xl font-black text-[#051c45] tracking-tight">Payments Verification</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Verify bKash, Nagad, Rocket, and Bank tuition transfers to automatically enroll students.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.payment-methods.index') }}" class="px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-bold shadow-xs inline-flex items-center gap-1.5 transition-colors">
                <i class="fas fa-credit-card text-slate-400"></i>
                <span>Gateways</span>
            </a>
            <a href="{{ route('admin.accounting.index') }}" class="px-4 py-2 rounded-xl bg-[#051c45] hover:bg-[#0a2540] text-white text-xs font-bold shadow-xs inline-flex items-center gap-2 transition-colors">
                <i class="fas fa-coins text-emerald-400"></i>
                <span>Full Accounting Ledger →</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs sm:text-sm font-semibold flex items-center gap-2.5 shadow-xs">
            <i class="fas fa-check-circle text-emerald-600"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- 4-Card KPI Summary Strip -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 sm:p-5 rounded-2xl border {{ $pendingList->count() > 0 ? 'border-amber-300 bg-amber-50/20' : 'border-slate-200/80' }} shadow-xs">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pending Review</div>
            <div class="text-2xl font-black {{ $pendingList->count() > 0 ? 'text-amber-600' : 'text-[#051c45]' }} mt-1">{{ $pendingList->count() }}</div>
            <div class="text-[11px] text-slate-500 mt-0.5">৳{{ number_format((float) $pendingList->sum('amount'), 0) }} awaiting approval</div>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Verified & Enrolled</div>
            <div class="text-2xl font-black text-emerald-600 mt-1">{{ $verifiedList->count() }}</div>
            <div class="text-[11px] text-slate-500 mt-0.5">৳{{ number_format((float) $verifiedList->sum('amount'), 0) }} realized</div>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Transactions</div>
            <div class="text-2xl font-black text-[#051c45] mt-1">{{ $payments->count() }}</div>
            <div class="text-[11px] text-slate-500 mt-0.5">All recorded submissions</div>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Rejected / Cancelled</div>
            <div class="text-2xl font-black text-rose-600 mt-1">{{ $rejectedList->count() }}</div>
            <div class="text-[11px] text-slate-500 mt-0.5">Invalid TrxID or duplicates</div>
        </div>
    </div>

    <!-- Filter Tabs & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <div class="flex items-center gap-1.5 overflow-x-auto">
            <button type="button" @click="statusTab = 'all'" :class="statusTab === 'all' ? 'bg-[#051c45] text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-colors whitespace-nowrap">
                All ({{ $payments->count() }})
            </button>
            <button type="button" @click="statusTab = 'pending'" :class="statusTab === 'pending' ? 'bg-amber-500 text-slate-950' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-colors whitespace-nowrap">
                Pending ({{ $pendingList->count() }})
            </button>
            <button type="button" @click="statusTab = 'verified'" :class="statusTab === 'verified' ? 'bg-[#10b981] text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-colors whitespace-nowrap">
                Verified ({{ $verifiedList->count() }})
            </button>
            <button type="button" @click="statusTab = 'rejected'" :class="statusTab === 'rejected' ? 'bg-rose-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-colors whitespace-nowrap">
                Rejected ({{ $rejectedList->count() }})
            </button>
        </div>

        <div class="relative w-full sm:w-72">
            <input type="text" x-model="search" placeholder="Search TrxID, phone, student, or course..." class="w-full pl-9 pr-4 py-2 rounded-xl border-slate-200 focus:border-[#051c45] focus:ring-[#051c45] text-xs">
            <i class="fas fa-search text-slate-400 text-xs absolute left-3 top-2.5"></i>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="text-[11px] text-slate-500 uppercase tracking-wider bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-bold">Student</th>
                        <th scope="col" class="px-6 py-4 font-bold">Course & Amount</th>
                        <th scope="col" class="px-6 py-4 font-bold">Payment Details</th>
                        <th scope="col" class="px-6 py-4 font-bold">Status</th>
                        <th scope="col" class="px-6 py-4 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($payments as $payment)
                    <tr class="hover:bg-slate-50/80 transition-colors"
                        x-show="(statusTab === 'all' || statusTab === '{{ $payment->status }}') && (search === '' || {{ json_encode(strtolower(($payment->user?->name ?? '') . ' ' . ($payment->user?->email ?? '') . ' ' . ($payment->course?->title ?? '') . ' ' . ($payment->transaction_id ?? '') . ' ' . ($payment->sender_number ?? ''))) }}.includes(search.toLowerCase()))">
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-900">{{ $payment->user?->name ?? 'Deleted Student' }}</div>
                            <div class="text-xs text-slate-400">{{ $payment->user?->email ?? '' }}</div>
                            @if(!empty($payment->form_data) && is_array($payment->form_data))
                                <div class="mt-1.5 flex items-center gap-1 flex-wrap">
                                    @foreach($payment->form_data as $fk => $fv)
                                        @if(!in_array($fk, ['name', 'email']) && !empty($fv))
                                            <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px] font-medium">
                                                {{ str_replace('_', ' ', $fk) }}: <strong class="text-slate-800">{{ $fv }}</strong>
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-900 line-clamp-1">{{ $payment->course?->title ?? 'Deleted Course' }}</div>
                            <div class="text-sm font-black text-[#051c45] mt-0.5">৳{{ number_format($payment->amount, 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-slate-100 text-[#051c45] text-xs font-bold">
                                {{ $payment->paymentMethod?->provider_name ?? 'Direct / Cash' }}
                            </div>
                            <div class="text-xs mt-1">Sender: <span class="font-mono text-slate-900 font-semibold">{{ $payment->sender_number }}</span></div>
                            <div class="text-xs">TrxID: <span class="font-mono font-bold text-[#051c45]">{{ $payment->transaction_id }}</span></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($payment->status === 'pending')
                                <span class="bg-amber-50 text-amber-800 border border-amber-200 text-xs font-bold px-2.5 py-0.5 rounded-full">Pending</span>
                            @elseif($payment->status === 'processing')
                                <span class="bg-blue-50 text-blue-800 border border-blue-200 text-xs font-bold px-2.5 py-0.5 rounded-full">Processing</span>
                            @elseif($payment->status === 'verified')
                                <span class="bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold px-2.5 py-0.5 rounded-full">Verified</span>
                            @else
                                <span class="bg-rose-50 text-rose-800 border border-rose-200 text-xs font-bold px-2.5 py-0.5 rounded-full">Rejected</span>
                            @endif
                            <div class="text-[11px] text-slate-400 mt-1">{{ $payment->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('payments.invoice', $payment) }}" target="_blank" title="View Receipt" class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-[#051c45] transition-colors text-xs">
                                    <i class="fas fa-file-invoice"></i>
                                </a>
                                <form action="{{ route('admin.payments.updateStatus', $payment) }}" method="POST" class="inline-flex items-center gap-1.5">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()" class="border-slate-200 rounded-xl text-xs font-bold text-slate-700 shadow-xs focus:ring-[#051c45] focus:border-[#051c45] py-1.5 pl-2.5 pr-7">
                                        <option value="pending" {{ $payment->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="processing" {{ $payment->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                        <option value="verified" {{ $payment->status === 'verified' ? 'selected' : '' }}>Verified</option>
                                        <option value="rejected" {{ $payment->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-slate-400">
                            No payment requests found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
