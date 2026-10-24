@extends('layouts.admin')

@section('content')
<div class="p-6 md:p-8 max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Edit Instructor: {{ $instructor->name }}</h1>
            <p class="text-sm text-gray-500 mt-1">Update profile details, contact information, expertise, or active status.</p>
        </div>
        <a href="{{ route('admin.instructors.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-colors">
            Back to Instructors
        </a>
    </div>

    @if($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl text-sm space-y-1">
            <div class="font-bold">Please correct the following errors:</div>
            <ul class="list-disc list-inside text-xs">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.instructors.update', $instructor) }}" method="POST" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Instructor Name *</label>
                <input type="text" name="name" value="{{ old('name', $instructor->name) }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Professional Title *</label>
                <input type="text" name="title" value="{{ old('title', $instructor->title) }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Specialization / Expertise *</label>
                <input type="text" name="expertise" value="{{ old('expertise', $instructor->expertise) }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Display Order</label>
                <input type="number" name="order" value="{{ old('order', $instructor->order) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Email Address</label>
                <input type="email" name="email" value="{{ old('email', $instructor->email) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone', $instructor->phone) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
            </div>
        </div>

        <!-- Unified Login Account & Access Control -->
        <div class="bg-indigo-50/70 p-5 rounded-2xl border border-indigo-100/90 space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-indigo-900 font-bold text-xs uppercase tracking-wider">
                    <i class="fas fa-user-shield text-indigo-600"></i>
                    <span>Linked Educator / User Account</span>
                </div>
                @if($instructor->user)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                        <i class="fas fa-check-circle text-emerald-500"></i> Login Account Active
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                        <i class="fas fa-magic text-amber-600"></i> Auto-Synced
                    </span>
                @endif
            </div>
            <p class="text-xs text-indigo-950/70 leading-relaxed">
                This faculty member can log in to the CAB Academy portal using their email (<strong class="text-indigo-950">{{ $instructor->email ?: 'N/A' }}</strong>) to manage their assigned sessions and interact with enrolled learners.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Set / Change Login Password</label>
                    <input type="password" name="password" placeholder="Leave blank to keep existing password" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-xs focus:border-[#041536] focus:ring-1 focus:ring-[#041536] bg-white">
                </div>
                <div class="flex items-end">
                    @if($instructor->user)
                        <a href="{{ route('admin.users.show', $instructor->user) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-white hover:bg-indigo-50 text-indigo-700 text-xs font-bold rounded-xl border border-indigo-200 transition-colors shadow-xs">
                            <i class="fas fa-external-link-alt text-[10px]"></i> View User Activity & Progress →
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-gray-50/70 p-5 rounded-2xl border border-gray-200/80 space-y-4">
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700">Instructor Profile Picture</label>
            <div class="flex flex-col sm:flex-row items-center gap-5">
                <!-- Avatar Preview (Current & Live Update) -->
                <div class="relative w-24 h-24 rounded-full overflow-hidden border-4 border-white shadow-md bg-slate-200 shrink-0 ring-2 ring-gray-200">
                    <img id="avatar_live_preview" src="{{ $instructor->avatar_url }}" alt="{{ $instructor->name }}" class="w-full h-full object-cover">
                </div>

                <div class="flex-1 w-full space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Choose Picture from Computer</label>
                        <input type="file" id="avatar_file_input" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#041536] file:text-white hover:file:bg-[#c30102] file:cursor-pointer cursor-pointer border border-gray-300 rounded-xl p-1 bg-white">
                        <input type="hidden" name="avatar_base64" id="avatar_base64_input">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Or Direct Photo URL / Path</label>
                        <input type="text" name="avatar" id="avatar_url_input" value="{{ old('avatar', $instructor->avatar) }}" placeholder="e.g. https://... or leave blank" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
                    </div>
                </div>
            </div>

            <script>
                document.getElementById('avatar_file_input')?.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(evt) {
                            document.getElementById('avatar_base64_input').value = evt.target.result;
                            const preview = document.getElementById('avatar_live_preview');
                            if (preview) {
                                preview.src = evt.target.result;
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                });

                document.getElementById('avatar_url_input')?.addEventListener('input', function(e) {
                    const val = e.target.value.trim();
                    if (val && (val.startsWith('http') || val.startsWith('data:'))) {
                        const preview = document.getElementById('avatar_live_preview');
                        if (preview) preview.src = val;
                    }
                });
            </script>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Professional Bio</label>
            <textarea name="bio" rows="4" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">{{ old('bio', $instructor->bio) }}</textarea>
        </div>

        <div class="pt-2">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $instructor->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-[#041536] focus:ring-[#041536]">
                <span class="text-sm font-semibold text-gray-800">Publish actively on the platform</span>
            </label>
        </div>

        <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
            <button type="button" onclick="if(confirm('Are you sure you want to permanently remove this instructor from the platform?')) { document.getElementById('deleteInstructorForm').submit(); }" class="px-4 py-2.5 bg-red-50 hover:bg-red-600 text-red-600 hover:text-white border border-red-200 text-xs font-bold rounded-xl transition-all">
                <i class="fas fa-trash-alt mr-1.5"></i> Delete Instructor
            </button>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.instructors.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-[#041536] hover:bg-[#c30102] text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                    Update Instructor
                </button>
            </div>
        </div>
    </form>
    <form id="deleteInstructorForm" action="{{ route('admin.instructors.destroy', $instructor) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection
