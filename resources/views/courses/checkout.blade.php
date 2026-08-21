@extends('layouts.student')

@section('header_title', 'Checkout')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gray-100">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Complete Your Enrollment</h2>
        
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Course Summary -->
            <div class="md:w-1/3">
                <div class="bg-gray-50 rounded-xl p-5 border border-gray-100 h-full">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Order Summary</h3>
                    <div class="font-bold text-lg text-gray-900 mb-1">{{ $course->title }}</div>
                    <div class="text-gray-500 text-sm mb-4">Course Enrollment</div>
                    
                    <div class="border-t border-gray-200 pt-4 mt-auto">
                        <div class="flex justify-between items-center font-bold text-lg">
                            <span>Total</span>
                            <span class="text-green-600">৳{{ number_format($course->price, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="md:w-2/3">
                @if($methods->isEmpty())
                    <div class="p-4 bg-yellow-50 text-yellow-800 rounded-lg">
                        No payment methods are currently available. Please contact support.
                    </div>
                @else
                    <form action="{{ route('courses.checkout.store', $course) }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Payment Method</label>
                            <div class="space-y-3">
                                @foreach($methods as $index => $method)
                                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none">
                                        <input type="radio" name="payment_method_id" value="{{ $method->id }}" data-is-bank="{{ stripos($method->provider_name, 'bank') !== false ? 'true' : 'false' }}" class="peer sr-only" {{ $index === 0 ? 'checked' : '' }} onchange="updateInstructions('instructions-{{ $method->id }}', this)">
                                        <span class="peer-checked:border-primary peer-checked:ring-1 peer-checked:ring-primary absolute inset-0 rounded-lg border-2 border-transparent pointer-events-none"></span>
                                        <div class="flex flex-1 items-center justify-between">
                                            <div class="flex flex-col">
                                                <span class="block text-sm font-bold text-gray-900">{{ $method->provider_name }}</span>
                                                <span class="mt-1 flex items-center text-sm text-gray-500">{{ $method->account_number }} ({{ $method->account_type }})</span>
                                            </div>
                                        </div>
                                    </label>
                                    
                                    <div id="instructions-{{ $method->id }}" class="payment-instructions text-sm text-gray-600 bg-gray-50 p-3 rounded-lg {{ $index === 0 ? 'block' : 'hidden' }}">
                                        <strong>Instructions:</strong> {{ $method->instructions ?? 'Please send the exact amount to the number above.' }}
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sender Number / Account Number</label>
                                <input type="text" id="sender_number" name="sender_number" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Transaction ID (TrxID)</label>
                                <input type="text" name="transaction_id" required placeholder="e.g. 8N4XXXXX" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary focus:border-primary">
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-primary hover:bg-green-700 text-white font-bold py-3 px-4 rounded-xl transition-colors text-lg flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Confirm Payment Submission
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    function updateInstructions(id, radio) {
        document.querySelectorAll('.payment-instructions').forEach(el => el.classList.add('hidden'));
        document.getElementById(id).classList.remove('hidden');
        
        let senderInput = document.getElementById('sender_number');
        if (radio && radio.getAttribute('data-is-bank') === 'true') {
            senderInput.removeAttribute('maxlength');
            senderInput.removeAttribute('minlength');
            senderInput.removeAttribute('pattern');
            senderInput.setAttribute('title', 'Enter your bank account number');
            senderInput.setAttribute('placeholder', 'e.g. 12345678901234');
        } else {
            senderInput.setAttribute('maxlength', '11');
            senderInput.setAttribute('minlength', '11');
            senderInput.setAttribute('pattern', '01[3-9][0-9]{8}');
            senderInput.setAttribute('title', 'Must be exactly 11 digits starting with 01');
            senderInput.setAttribute('placeholder', 'e.g. 01712345678');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        let checked = document.querySelector('input[name="payment_method_id"]:checked');
        if (checked) {
            updateInstructions('instructions-' + checked.value, checked);
        }
    });
</script>
@endsection
