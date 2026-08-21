@extends('layouts.admin')

@section('header_title', 'Pending Payments')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900">Payments Verification</h2>
    </div>

    <!-- Payments List -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold">Student</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Course & Amount</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Payment Details</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Status</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">{{ $payment->user->name }}</div>
                            <div class="text-xs text-gray-400">{{ $payment->user->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">{{ $payment->course->title }}</div>
                            <div class="text-sm font-medium text-green-600">৳{{ number_format($payment->amount, 2) }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-700">{{ $payment->paymentMethod->provider_name ?? 'N/A' }}</div>
                            <div class="text-xs mt-1">Sender: <span class="font-mono text-gray-900">{{ $payment->sender_number }}</span></div>
                            <div class="text-xs">TrxID: <span class="font-mono font-bold text-gray-900">{{ $payment->transaction_id }}</span></div>
                        </td>
                        <td class="px-6 py-4">
                            @if($payment->status === 'pending')
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Pending</span>
                            @elseif($payment->status === 'processing')
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Processing</span>
                            @elseif($payment->status === 'verified')
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Verified</span>
                            @else
                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Rejected</span>
                            @endif
                            <div class="text-xs text-gray-400 mt-1">{{ $payment->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form action="{{ route('admin.payments.updateStatus', $payment) }}" method="POST" class="flex justify-end">
                                @csrf
                                <select name="status" onchange="this.form.submit()" class="border-gray-300 rounded-lg text-sm shadow-sm focus:ring-primary focus:border-primary py-1.5 pl-3 pr-8">
                                    <option value="pending" {{ $payment->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ $payment->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="verified" {{ $payment->status === 'verified' ? 'selected' : '' }}>Verified</option>
                                    <option value="rejected" {{ $payment->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
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
