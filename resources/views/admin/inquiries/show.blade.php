@extends('layouts.admin')

@section('content')
<div class="p-6 md:p-8 space-y-6 max-w-5xl mx-auto">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.inquiries.index') }}" class="text-xs font-bold text-gray-500 hover:text-gray-900 flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Inquiries
        </a>
        <div class="flex items-center gap-2">
            <form method="POST" action="{{ route('admin.inquiries.destroy', $inquiry) }}" onsubmit="return confirm('Are you sure you want to delete this inquiry?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3.5 py-2 text-xs font-bold text-red-600 hover:bg-red-50 rounded-xl transition-colors">
                    Delete Inquiry
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Message Card (2 Cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 sm:p-8 border border-gray-100 shadow-sm space-y-6">
            <div class="border-b border-gray-100 pb-4">
                <span class="px-2.5 py-1 bg-gray-100 text-gray-800 rounded-md text-xs font-bold uppercase">
                    {{ ucfirst(str_replace('_', ' ', $inquiry->inquiry_type)) }}
                </span>
                <h1 class="text-xl sm:text-2xl font-black text-gray-900 mt-3">{{ $inquiry->subject }}</h1>
                <span class="text-xs text-gray-400 mt-1 block">Received: {{ $inquiry->created_at->format('l, F d, Y - h:i A') }}</span>
            </div>

            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Message Content</h3>
                <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100 text-gray-800 text-sm whitespace-pre-line leading-relaxed">
                    {{ $inquiry->message }}
                </div>
            </div>

            <!-- Quick Communication Actions -->
            <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-100">
                <a href="mailto:{{ $inquiry->email }}?subject={{ urlencode('Re: ' . $inquiry->subject . ' - CAB Academy') }}" class="px-4 py-2.5 bg-gray-900 hover:bg-black text-white rounded-xl text-xs font-bold transition-colors inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    Reply via Email
                </a>
                <a href="tel:{{ $inquiry->phone }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-colors inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    Call {{ $inquiry->phone }}
                </a>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $inquiry->phone) }}?text={{ urlencode('Hello ' . $inquiry->name . ', regarding your inquiry with CAB Academy: ') }}" target="_blank" class="px-4 py-2.5 bg-[#25D366] hover:bg-[#20ba59] text-white rounded-xl text-xs font-bold transition-colors inline-flex items-center gap-2">
                    <i class="fab fa-whatsapp"></i> Chat on WhatsApp
                </a>
            </div>
        </div>

        <!-- Sidebar Details & Status Control (1 Col) -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Sender Details</h3>
                
                <div class="space-y-3 text-xs sm:text-sm">
                    <div>
                        <span class="text-gray-400 block text-xs">Name</span>
                        <span class="font-bold text-gray-900">{{ $inquiry->name }}</span>
                    </div>

                    <div>
                        <span class="text-gray-400 block text-xs">Email</span>
                        <a href="mailto:{{ $inquiry->email }}" class="font-medium text-indigo-600 hover:underline">{{ $inquiry->email }}</a>
                    </div>

                    <div>
                        <span class="text-gray-400 block text-xs">Phone</span>
                        <span class="font-mono font-medium text-gray-800">{{ $inquiry->phone }}</span>
                    </div>

                    @if($inquiry->organization)
                    <div>
                        <span class="text-gray-400 block text-xs">Organization / Institution</span>
                        <span class="font-medium text-gray-800">{{ $inquiry->organization }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Update Status Form -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Update Status</h3>
                <form action="{{ route('admin.inquiries.status', $inquiry) }}" method="POST" class="space-y-3">
                    @csrf
                    @method('PATCH')
                    
                    <select name="status" class="w-full text-xs font-bold border-gray-200 rounded-xl bg-gray-50 focus:ring-1 focus:ring-indigo-500 py-2.5 px-3">
                        <option value="pending" {{ $inquiry->status === 'pending' ? 'selected' : '' }}>Pending Action</option>
                        <option value="contacted" {{ $inquiry->status === 'contacted' ? 'selected' : '' }}>Contacted / In Progress</option>
                        <option value="resolved" {{ $inquiry->status === 'resolved' ? 'selected' : '' }}>Resolved & Closed</option>
                        <option value="archived" {{ $inquiry->status === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>

                    <button type="submit" class="w-full py-2.5 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-xl transition-colors shadow-sm">
                        Update Workflow Status
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
