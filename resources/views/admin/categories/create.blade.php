@extends('layouts.admin')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <a href="{{ route('admin.categories.index') }}" class="text-xs font-semibold text-gray-500 hover:text-gray-900 transition-colors">Categories</a>
            <span class="text-gray-400 text-xs">/</span>
            <span class="text-xs font-bold text-[#051c45]">New Category</span>
        </div>
        <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Create New Category</h2>
    </div>
</div>

<div class="max-w-2xl bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 sm:p-8">
    <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-6">
        @csrf

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Category Name *</label>
            <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Export – Import, Supply Chain Management" 
                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
            @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">URL Slug (Optional)</label>
                <input type="text" name="slug" value="{{ old('slug') }}" placeholder="export-import" 
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm font-mono focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
                <p class="text-[11px] text-gray-400 mt-1">Leave blank to auto-generate from name.</p>
                @error('slug') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">FontAwesome Icon</label>
                <input type="text" name="icon" value="{{ old('icon', 'fa-tag') }}" placeholder="fa-ship, fa-truck-fast, fa-calculator" 
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm font-mono focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
                <p class="text-[11px] text-gray-400 mt-1">e.g. <code>fa-ship</code>, <code>fa-truck-fast</code>, <code>fa-scale-balanced</code></p>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Short Description</label>
            <textarea name="description" rows="2" placeholder="Brief subject summary for catalog header..." 
                      class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536] resize-none">{{ old('description') }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Cover Image URL (Optional)</label>
                <input type="text" name="image" value="{{ old('image') }}" placeholder="https://images.unsplash.com/..." 
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Display Order</label>
                <input type="number" name="order" value="{{ old('order', 0) }}" 
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} 
                   class="w-4 h-4 rounded text-[#051c45] border-gray-300 focus:ring-[#051c45]">
            <label for="is_active" class="text-xs font-bold uppercase tracking-wider text-gray-700">Active (Visible in Homepage Pills & Catalog)</label>
        </div>

        <div class="pt-4 border-t border-gray-100 flex items-center gap-3">
            <button type="submit" class="px-6 py-2.5 bg-[#c30102] hover:bg-[#a50102] text-white text-sm font-bold rounded-xl shadow-lg shadow-red-900/20 active:scale-95 transition-all">
                Save Category
            </button>
            <a href="{{ route('admin.categories.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-all">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
