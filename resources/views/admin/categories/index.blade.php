@extends('layouts.admin')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#041536] text-white">
                Course Taxonomies
            </span>
            <span class="text-xs text-gray-500 font-medium">Dynamic Database Categories</span>
        </div>
        <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Categories Management</h2>
        <p class="text-gray-500 text-sm mt-1">Manage, re-order, and customize icons for course categories rendered across the homepage and catalog.</p>
    </div>
    
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.categories.create') }}" class="px-4 py-2.5 bg-[#c30102] hover:bg-[#a50102] text-white text-sm font-bold rounded-xl shadow-lg shadow-red-900/20 active:scale-95 transition-all inline-flex items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>Add New Category</span>
        </a>
    </div>
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 text-emerald-800 rounded-xl border border-emerald-200 text-sm font-semibold flex items-center gap-3 shadow-sm">
        <i class="fas fa-check-circle text-emerald-600 text-base"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 border-b border-gray-200 text-xs font-bold uppercase tracking-wider text-gray-500">
                <tr>
                    <th class="py-3.5 px-4 w-12 text-center">Order</th>
                    <th class="py-3.5 px-4">Icon & Category Name</th>
                    <th class="py-3.5 px-4">Slug (URL)</th>
                    <th class="py-3.5 px-4 text-center">Linked Courses</th>
                    <th class="py-3.5 px-4 text-center">Status</th>
                    <th class="py-3.5 px-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($categories as $category)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3.5 px-4 text-center font-mono text-xs font-bold text-gray-400">
                            {{ $category->order }}
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-slate-100 text-[#051c45] flex items-center justify-center text-sm border border-slate-200 shrink-0">
                                    <i class="fas {{ str_starts_with($category->icon, 'fa-') ? $category->icon : 'fa-' . $category->icon }}"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900">{{ $category->name }}</div>
                                    @if($category->description)
                                        <p class="text-xs text-gray-500 line-clamp-1 max-w-md">{{ $category->description }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 font-mono text-xs text-gray-500">
                            /category/{{ $category->slug }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $category->courses_count > 0 ? 'bg-blue-50 text-[#051c45] border border-blue-200' : 'bg-gray-100 text-gray-500' }}">
                                {{ $category->courses_count }} courses
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @if($category->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-gray-100 text-gray-500">
                                    Hidden
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 hover:text-[#051c45] transition-colors" title="Edit Category">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this category?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg hover:bg-red-50 text-gray-400 hover:text-[#c30102] transition-colors" title="Delete Category">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-gray-400">
                            <i class="fas fa-folder-open text-3xl mb-2 text-gray-300 block"></i>
                            <span class="text-sm font-semibold">No dynamic categories found. Run seed or click "Add New Category".</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
