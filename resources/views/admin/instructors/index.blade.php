@extends('layouts.admin')

@section('content')
<div class="p-6 md:p-8 space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Executive Instructors & Faculty</h1>
            <p class="text-sm text-gray-500 mt-1">Manage, add, edit, or remove instructors showcased on the public platform.</p>
        </div>
        <div>
            <a href="{{ route('admin.instructors.create') }}" class="px-5 py-2.5 bg-[#041536] hover:bg-[#c30102] text-white text-xs font-bold rounded-xl transition-all shadow-sm inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add New Instructor
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="text-xs uppercase bg-gray-50 text-gray-700 border-b border-gray-100 font-bold">
                    <tr>
                        <th class="px-6 py-4">Instructor</th>
                        <th class="px-6 py-4">Title & Expertise</th>
                        <th class="px-6 py-4">Contact Info</th>
                        <th class="px-6 py-4 text-center">Display Order</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($instructors as $instructor)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-12 h-12 rounded-full overflow-hidden border border-gray-200 shrink-0 bg-slate-100">
                                        @php
                                            $avatar = $instructor->avatar 
                                                ? (str_starts_with($instructor->avatar, 'http') || str_starts_with($instructor->avatar, '/') ? asset($instructor->avatar) : asset('storage/' . $instructor->avatar))
                                                : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80';
                                        @endphp
                                        <img src="{{ $avatar }}" alt="{{ $instructor->name }}" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 text-sm leading-tight">{{ $instructor->name }}</div>
                                        <div class="text-[11px] text-gray-400 mt-0.5">ID: #{{ $instructor->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 max-w-xs">
                                <div class="font-semibold text-gray-800 text-xs">{{ $instructor->title }}</div>
                                <span class="inline-block mt-1 px-2 py-0.5 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded text-[10px] font-bold">
                                    {{ $instructor->expertise }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <div class="text-gray-700 font-medium">{{ $instructor->email ?: 'N/A' }}</div>
                                <div class="text-gray-400 text-[11px]">{{ $instructor->phone ?: 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-xs text-gray-700">
                                {{ $instructor->order }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($instructor->is_active)
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">Active</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-gray-100 text-gray-500">Hidden</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                <a href="{{ route('admin.instructors.edit', $instructor) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:text-white border border-indigo-200 hover:bg-indigo-600 rounded-lg transition-colors">
                                    Edit
                                </a>
                                <form action="{{ route('admin.instructors.destroy', $instructor) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to remove this instructor from the platform?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-red-600 hover:text-white border border-red-200 hover:bg-red-600 rounded-lg transition-colors">
                                        Remove
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                No instructors found. Click "Add New Instructor" to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($instructors->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $instructors->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
