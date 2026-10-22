@extends('layouts.admin')

@section('content')
<div class="p-6 md:p-8 max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Add New Instructor</h1>
            <p class="text-sm text-gray-500 mt-1">Add an instructor profile to appear on the platform and course pages.</p>
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

    <form action="{{ route('admin.instructors.store') }}" method="POST" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Instructor Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Faculty Chair – Trade & Customs" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Professional Title *</label>
                <input type="text" name="title" value="{{ old('title') }}" required placeholder="e.g. Principal Trade Finance Fellow & Customs Consultant" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Specialization / Expertise *</label>
                <input type="text" name="expertise" value="{{ old('expertise') }}" required placeholder="e.g. Banking, L/C, Customs & Shipping Procedure" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Display Order</label>
                <input type="number" name="order" value="{{ old('order', 0) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Email Address (Optional)</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="instructor@cab-academy.com" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Phone Number (Optional)</label>
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="01917-054664" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Upload Photo</label>
                <input type="file" id="create_avatar_file_input" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-xl text-sm">
                <input type="hidden" name="avatar_base64" id="create_avatar_base64_input">
                <script>
                    document.getElementById('create_avatar_file_input').addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(evt) {
                                document.getElementById('create_avatar_base64_input').value = evt.target.result;
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                </script>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Or Photo URL / Path</label>
                <input type="text" name="avatar" value="{{ old('avatar') }}" placeholder="https://images.unsplash.com/photo-..." class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Professional Bio</label>
            <textarea name="bio" rows="4" placeholder="Brief background, corporate advisory experience, credentials, and achievements..." class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">{{ old('bio') }}</textarea>
        </div>

        <div class="pt-2">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} class="rounded border-gray-300 text-[#041536] focus:ring-[#041536]">
                <span class="text-sm font-semibold text-gray-800">Publish actively on the platform</span>
            </label>
        </div>

        <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
            <a href="{{ route('admin.instructors.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 bg-[#041536] hover:bg-[#c30102] text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                Save Instructor
            </button>
        </div>
    </form>
</div>
@endsection
