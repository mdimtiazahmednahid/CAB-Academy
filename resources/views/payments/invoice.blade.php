<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <title>Tuition Receipt & Invoice #INV-CAB-{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }} - CAB Academy</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Montserrat', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .invoice-box { border: none !important; box-shadow: none !important; max-width: 100% !important; padding: 0 !important; }
            @page { margin: 1.5cm; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen py-10 px-4 sm:px-6">

    <!-- Top Toolbar (Hidden on Print) -->
    <div class="no-print max-w-4xl mx-auto mb-6 flex justify-between items-center bg-white p-4 rounded-2xl border border-gray-200 shadow-sm">
        <a href="{{ route('dashboard') }}" class="text-xs font-bold text-gray-600 hover:text-gray-900 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Return to Dashboard
        </a>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-5 py-2.5 bg-[#c30102] hover:bg-[#a50001] text-white text-xs font-bold rounded-xl shadow-sm transition-all flex items-center gap-2 cursor-pointer">
                <i class="fas fa-print"></i> Print Official Receipt
            </button>
        </div>
    </div>

    <!-- Official Invoice Container -->
    <div class="invoice-box max-w-4xl mx-auto bg-white rounded-3xl p-8 sm:p-12 border border-gray-200 shadow-xl relative">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-8 border-b-2 border-gray-100 gap-6">
            <div class="flex items-center gap-4">
                <img src="{{ asset('images/logo.png') }}" alt="CAB Academy" class="w-16 h-16 object-contain rounded-2xl bg-white border border-gray-100 p-1 shadow-sm">
                <div>
                    <h1 class="text-2xl font-black text-[#051c45] uppercase tracking-tight">Commercial Academy Bangladesh</h1>
                    <span class="text-xs font-bold text-[#c30102] uppercase tracking-widest block">CAB Academy • Official Tuition Invoice</span>
                    <p class="text-xs text-gray-500 mt-1">Mirpur-Pallabi, Dhaka, Bangladesh, 1216 | 01917-054664</p>
                </div>
            </div>

            <div class="text-left sm:text-right">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block">Invoice Number</span>
                <span class="text-lg font-mono font-black text-[#051c45]">INV-CAB-{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}</span>
                <span class="text-xs text-gray-500 block mt-1">Date: {{ $payment->created_at->format('M d, Y') }}</span>
                <div class="mt-2">
                    @if($payment->status === 'verified')
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold uppercase rounded-full">
                            <i class="fas fa-check-circle"></i> Paid & Verified
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-bold uppercase rounded-full">
                            <i class="fas fa-clock"></i> {{ ucfirst($payment->status) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Student & Payment Information -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 my-8 pb-8 border-b border-gray-100">
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Billed To (Student)</h3>
                <h4 class="text-base font-bold text-[#051c45]">{{ $payment->user->name }}</h4>
                <p class="text-xs text-gray-600 mt-1">{{ $payment->user->email }}</p>
                @if($payment->phone)
                    <p class="text-xs text-gray-600 mt-0.5">Phone: {{ $payment->phone }}</p>
                @endif
                <p class="text-xs text-gray-500 mt-1">Student ID: #STU-{{ str_pad($payment->user->id, 5, '0', STR_PAD_LEFT) }}</p>
            </div>

            <div class="sm:text-right">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Payment Details</h3>
                <p class="text-xs text-gray-600">
                    Channel: <strong class="text-gray-900">{{ $payment->paymentMethod?->name ?? 'MFS / Bank Transfer' }}</strong>
                </p>
                <p class="text-xs text-gray-600 mt-1 font-mono">
                    Transaction ID: <strong class="text-gray-900">{{ $payment->transaction_id ?: 'MANUAL-SYS' }}</strong>
                </p>
                <p class="text-xs text-gray-600 mt-1">
                    Currency: <strong>BDT (Bangladeshi Taka)</strong>
                </p>
            </div>
        </div>

        <!-- Line Items Table -->
        <div class="mb-8">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-xs uppercase tracking-wider text-gray-500">
                        <th class="py-3 font-bold">Course Description</th>
                        <th class="py-3 font-bold text-center">Batch / Category</th>
                        <th class="py-3 font-bold text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr>
                        <td class="py-4">
                            <span class="font-bold text-gray-900 block text-base">{{ $payment->course->title }}</span>
                            <span class="text-xs text-gray-500">Professional certification module with evaluation & verifiable credential</span>
                        </td>
                        <td class="py-4 text-center text-xs text-gray-600 font-medium">
                            {{ $payment->course->category ?? 'Executive Program' }}
                        </td>
                        <td class="py-4 text-right font-mono font-bold text-gray-900 text-base">
                            ৳{{ number_format($payment->amount, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Total Calculation -->
        <div class="border-t-2 border-gray-100 pt-4 flex flex-col items-end">
            <div class="w-full sm:w-64 space-y-2 text-xs">
                <div class="flex justify-between text-gray-600">
                    <span>Tuition Subtotal:</span>
                    <span class="font-mono">৳{{ number_format($payment->amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>VAT (Included):</span>
                    <span class="font-mono">৳0.00</span>
                </div>
                <div class="flex justify-between text-base font-black text-[#051c45] pt-2 border-t border-gray-200">
                    <span>Total Paid:</span>
                    <span class="font-mono text-[#c30102]">৳{{ number_format($payment->amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Paid Seal & Signature Block -->
        <div class="mt-12 pt-8 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-6">
            <div class="text-xs text-gray-500 text-center sm:text-left">
                <p class="font-bold text-[#051c45]">Thank you for your enrollment with CAB Academy!</p>
                <p class="mt-1">For questions regarding billing or invoices, email accounts: <strong>commercialacademy868@gmail.com</strong></p>
            </div>

            <!-- Official Stamp -->
            <div class="w-28 h-28 rounded-full border-4 border-emerald-600 flex flex-col items-center justify-center text-emerald-700 transform -rotate-12 select-none">
                <span class="text-[9px] font-black uppercase tracking-widest">CAB ACADEMY</span>
                <span class="text-lg font-black tracking-tight my-0.5">PAID</span>
                <span class="text-[8px] font-mono font-semibold">{{ $payment->created_at->format('d-m-Y') }}</span>
            </div>
        </div>

    </div>

</body>
</html>
