@extends('layouts.admin')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#041536] text-white">
                ACS 4 Learning Departments
            </span>
            <span class="text-xs text-gray-500 font-medium">Categories, Sub-Tracks & Department Heads</span>
        </div>
        <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Learning Departments & Categories</h2>
        <p class="text-gray-500 text-sm mt-1">Manage the 4 Core Learning Departments, their 6 specialized training tracks, Department Heads, and dedicated faculty teams.</p>
    </div>
    
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.categories.create') }}" class="px-4 py-2.5 bg-[#c30102] hover:bg-[#a50102] text-white text-sm font-bold rounded-xl shadow-lg shadow-red-900/20 active:scale-95 transition-all inline-flex items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>Add Department / Category</span>
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
                    <th class="py-3.5 px-4">Learning Department & Sub-Tracks</th>
                    <th class="py-3.5 px-4">Department Head & Team</th>
                    <th class="py-3.5 px-4 text-center">Courses</th>
                    <th class="py-3.5 px-4 text-center">Status</th>
                    <th class="py-3.5 px-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($categories as $category)
                    @php
                        $subs = $category->effective_subcategories;
                        $head = $category->resolved_head;
                        $team = $category->resolved_team;
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition-colors align-top">
                        <td class="py-3.5 px-4 text-center font-mono text-xs font-bold text-gray-400">
                            {{ $category->order }}
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-xl bg-slate-100 text-[#051c45] flex items-center justify-center text-sm border border-slate-200 shrink-0 mt-0.5">
                                    <i class="fas {{ str_starts_with($category->icon, 'fa-') ? $category->icon : 'fa-' . $category->icon }}"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900">{{ $category->name }}</div>
                                    <div class="font-mono text-[11px] text-gray-400 mb-1">/category/{{ $category->slug }}</div>
                                    @if(!empty($subs))
                                        <div class="flex flex-wrap gap-1 mt-1.5 max-w-xl">
                                            @foreach($subs as $subItem)
                                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px] font-medium border border-slate-200/70">
                                                    {{ $subItem }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4">
                            @if($head)
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded bg-[#c30102]/10 text-[#c30102] text-[10px] font-extrabold uppercase">Head</span>
                                    <span class="text-xs font-bold text-gray-900">{{ $head->name }}</span>
                                </div>
                                <div class="text-[11px] text-gray-500 mt-1">
                                    <i class="fas fa-users text-gray-400 mr-1"></i>{{ max(1, $team->count()) }} Department Team Member(s)
                                </div>
                            @else
                                <span class="text-xs text-gray-400 italic">No Head Assigned</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @php
                                $deptCourseCount = $category->department_courses_count;
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $deptCourseCount > 0 ? 'bg-blue-50 text-[#051c45] border border-blue-200' : 'bg-gray-100 text-gray-500' }}">
                                {{ $deptCourseCount }} courses
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
