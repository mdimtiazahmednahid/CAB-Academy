@extends('layouts.admin')

@section('header_title', 'Courses & Curriculum')

@section('content')
<div class="space-y-6" x-data="{ search: '', statusFilter: 'all', quickEditId: null }">
    <!-- Header & Primary Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-[#051c45] tracking-tight">Courses</h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">Manage executive programs, batch schedules, tuition fees, and curriculum modules.</p>
        </div>
        <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-4 py-2.5 bg-[#051c45] hover:bg-[#0a2540] text-white rounded-xl text-xs sm:text-sm font-bold shadow-xs inline-flex items-center gap-2 transition-all">
            <i class="fas fa-plus text-emerald-400"></i>
            <span>+ New Course</span>
        </button>
    </div>

    @if(session('success'))
        <div class="p-4 text-sm text-emerald-800 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center gap-2.5 font-semibold shadow-xs">
            <i class="fas fa-check-circle text-emerald-600"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- 4-Card KPI Summary Strip -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Courses</div>
            <div class="text-2xl font-black text-[#051c45] mt-1">{{ $courses->count() }}</div>
            <div class="text-[11px] text-slate-500 mt-0.5">{{ $courses->pluck('category')->filter()->unique()->count() }} Active Disciplines</div>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Published Tracks</div>
            <div class="text-2xl font-black text-emerald-600 mt-1">{{ $courses->where('is_published', true)->count() }}</div>
            <div class="text-[11px] text-slate-500 mt-0.5">{{ $courses->where('is_published', false)->count() }} in Draft Mode</div>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Enrolled Students</div>
            <div class="text-2xl font-black text-[#051c45] mt-1">{{ number_format($courses->sum('students_count')) }}</div>
            <div class="text-[11px] text-slate-500 mt-0.5">{{ number_format($courses->sum('certificates_count')) }} Certificates Issued</div>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Catalog Views</div>
            <div class="text-2xl font-black text-[#051c45] mt-1">{{ number_format($courses->sum('views')) }}</div>
            <div class="text-[11px] text-slate-500 mt-0.5">{{ number_format($courses->sum('lessons_count')) }} Total Lessons</div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <div class="flex items-center gap-1.5 overflow-x-auto">
            <button type="button" @click="statusFilter = 'all'" :class="statusFilter === 'all' ? 'bg-[#051c45] text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-colors">
                All ({{ $courses->count() }})
            </button>
            <button type="button" @click="statusFilter = 'published'" :class="statusFilter === 'published' ? 'bg-[#10b981] text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-colors">
                Published ({{ $courses->where('is_published', true)->count() }})
            </button>
            <button type="button" @click="statusFilter = 'draft'" :class="statusFilter === 'draft' ? 'bg-slate-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-colors">
                Drafts ({{ $courses->where('is_published', false)->count() }})
            </button>
        </div>

        <div class="relative w-full sm:w-72">
            <input type="text" x-model="search" placeholder="Filter by title, category, or batch..." class="w-full pl-9 pr-4 py-2 rounded-xl border-slate-200 focus:border-[#051c45] focus:ring-[#051c45] text-xs">
            <i class="fas fa-search text-slate-400 text-xs absolute left-3 top-2.5"></i>
        </div>
    </div>

    <!-- Courses Directory Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase text-[11px] tracking-wider">
                    <tr>
                        <th class="px-6 py-4 font-bold">Course & Track</th>
                        @if(auth()->user()->isAdmin())
                            <th class="px-6 py-4 font-bold">Instructor</th>
                        @endif
                        <th class="px-6 py-4 font-bold">Tuition & Batch</th>
                        <th class="px-6 py-4 font-bold">Cohort & Content</th>
                        <th class="px-6 py-4 font-bold">Status</th>
                        <th class="px-6 py-4 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($courses as $c)
                    <tr class="hover:bg-slate-50/80 transition-colors"
                        x-show="(statusFilter === 'all' || (statusFilter === 'published' && {{ $c->is_published ? 'true' : 'false' }}) || (statusFilter === 'draft' && {{ !$c->is_published ? 'true' : 'false' }})) && (search === '' || {{ json_encode(strtolower($c->title . ' ' . ($c->category ?? '') . ' ' . ($c->batch ?? ''))) }}.includes(search.toLowerCase()))">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3.5">
                                <!-- 1-Click Direct Cover Photo Upload Form -->
                                <form method="POST" action="{{ route('admin.courses.update', $c) }}" enctype="multipart/form-data" class="shrink-0">
                                    @csrf
                                    <input type="hidden" name="_section_label" value="Course Cover Photo">
                                    <input type="file" id="row_cover_{{ $c->id }}" name="cover_image" accept="image/*" class="hidden" onchange="this.form.submit()">
                                    <label for="row_cover_{{ $c->id }}" title="Click camera to replace cover photo in 1 click" class="relative w-16 h-11 rounded-xl overflow-hidden border border-slate-200 bg-slate-900 shadow-xs block cursor-pointer group">
                                        <img src="{{ $c->cover_image_url }}" alt="{{ $c->title }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=800&q=80';">
                                        <span class="absolute inset-0 bg-slate-950/65 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-[10px] font-bold">
                                            <i class="fas fa-camera"></i>
                                        </span>
                                    </label>
                                </form>

                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-1.5">
                                        <a href="{{ route('admin.courses.show', $c) }}" class="font-bold text-[#051c45] hover:text-[#c30102] transition-colors line-clamp-1">
                                            {{ $c->title }}
                                        </a>
                                        <button type="button" @click="quickEditId = (quickEditId === {{ $c->id }} ? null : {{ $c->id }})" title="Quick-edit title, price, or batch inline" class="text-slate-400 hover:text-[#051c45] text-[11px] px-1.5 py-0.5 rounded hover:bg-slate-100 transition-colors shrink-0">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                                        @if($c->category)
                                            <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[10px] font-bold">{{ $c->category }}</span>
                                        @endif
                                        @if($c->level)
                                            <span class="text-[11px] text-slate-400">{{ $c->level }}</span>
                                        @endif
                                        <span class="text-[10px] text-slate-400">&bull; {{ $c->venue ?: 'Online Live (Zoom)' }}</span>
                                    </div>

                                    <!-- Inline Quick-Edit Micro-Form (Saves ONLY Edited Fields) -->
                                    <form x-show="quickEditId === {{ $c->id }}" x-cloak method="POST" action="{{ route('admin.courses.update', $c) }}" class="mt-2.5 p-2.5 rounded-xl bg-slate-100/90 border border-slate-200 space-y-2">
                                        @csrf
                                        <input type="hidden" name="_section_label" value="Quick Course Info">
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 uppercase">Title</label>
                                            <input type="text" name="title" value="{{ $c->title }}" required class="w-full rounded-lg border-slate-300 text-xs font-bold text-[#051c45] py-1 px-2">
                                        </div>
                                        <div class="grid grid-cols-3 gap-2">
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase">Fee (৳)</label>
                                                <input type="number" step="0.01" min="0" name="price" value="{{ $c->price }}" class="w-full rounded-lg border-slate-300 text-xs font-bold py-1 px-2">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase">Batch</label>
                                                <input type="text" name="batch" value="{{ $c->batch }}" placeholder="e.g. 105th" class="w-full rounded-lg border-slate-300 text-xs py-1 px-2">
                                            </div>
                                            <div class="flex items-end gap-1">
                                                <button type="submit" class="flex-1 py-1.5 px-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] font-extrabold">Save</button>
                                                <button type="button" @click="quickEditId = null" class="py-1.5 px-2 rounded-lg bg-white text-slate-600 border border-slate-200 text-[11px] font-bold">&times;</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                        @if(auth()->user()->isAdmin())
                            <td class="px-6 py-4 text-slate-600 text-xs font-medium">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $c->instructor_avatar_url }}" alt="{{ $c->instructor_name }}" class="w-6 h-6 rounded-full object-cover border border-slate-200 shrink-0" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($c->instructor_name) }}&background=051c45&color=fff';">
                                    <span class="truncate max-w-[160px] font-semibold text-slate-700">{{ $c->instructor_name }}</span>
                                </div>
                            </td>
                        @endif
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-black text-[#051c45] text-sm cursor-pointer hover:text-indigo-600" @click="quickEditId = (quickEditId === {{ $c->id }} ? null : {{ $c->id }})" title="Click to quick-edit price">
                                {{ $c->price > 0 ? '৳' . number_format($c->price, 0) : 'Free' }}
                            </div>
                            <div class="text-[11px] text-slate-500 mt-0.5">
                                @if($c->batch)
                                    <span class="text-[#c30102] font-bold">{{ $c->batch }}</span>
                                @elseif($c->start_date)
                                    Starts {{ $c->start_date->format('d M, Y') }}
                                @else
                                    Open Schedule
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-500 text-xs whitespace-nowrap">
                            <div class="font-bold text-slate-800">{{ $c->students_count ?? 0 }} Enrolled</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">
                                {{ $c->sections_count ?? 0 }} Sec &bull; {{ $c->lessons_count ?? 0 }} Les &bull; 👁 {{ number_format($c->views) }} views
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col items-start gap-1.5">
                                <!-- 1-Click Publish / Draft Toggle -->
                                <form method="POST" action="{{ route('admin.courses.update', $c) }}">
                                    @csrf
                                    <input type="hidden" name="_section_label" value="Publish Status">
                                    <input type="hidden" name="is_published" value="{{ $c->is_published ? '0' : '1' }}">
                                    @if($c->is_published)
                                        <button type="submit" title="Click to switch to Draft" class="px-2.5 py-0.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-full text-[11px] font-bold cursor-pointer transition-colors">
                                            ● Published
                                        </button>
                                    @else
                                        <button type="submit" title="Click to Publish" class="px-2.5 py-0.5 bg-slate-100 hover:bg-emerald-50 text-slate-600 hover:text-emerald-700 border border-slate-200 rounded-full text-[11px] font-bold cursor-pointer transition-colors">
                                            ○ Draft
                                        </button>
                                    @endif
                                </form>

                                <!-- 1-Click Certificate Release Toggle -->
                                <form method="POST" action="{{ route('admin.courses.update', $c) }}">
                                    @csrf
                                    <input type="hidden" name="_section_label" value="Certificate Status">
                                    <input type="hidden" name="certificate_enabled" value="{{ $c->certificate_enabled ? '0' : '1' }}">
                                    @if($c->certificate_enabled)
                                        <button type="submit" title="Click to lock certificates" class="text-[10px] text-emerald-600 hover:text-amber-600 font-semibold flex items-center gap-1 cursor-pointer">
                                            <i class="fas fa-award"></i> Certs Active
                                        </button>
                                    @else
                                        <button type="submit" title="Click to release certificates" class="text-[10px] text-slate-400 hover:text-emerald-600 font-semibold flex items-center gap-1 cursor-pointer">
                                            <i class="fas fa-lock text-[9px]"></i> Certs Locked
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap space-x-1.5">
                            <button type="button" @click="quickEditId = (quickEditId === {{ $c->id }} ? null : {{ $c->id }})" title="Quick Inline Edit" class="inline-flex items-center px-2.5 py-1.5 text-xs font-bold text-slate-600 hover:text-[#051c45] bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                                <i class="fas fa-pen text-[10px]"></i>
                            </button>
                            <a href="{{ route('courses.show', $c) }}" target="_blank" title="Preview Live Page" class="inline-flex items-center px-2.5 py-1.5 text-xs font-bold text-slate-600 hover:text-[#051c45] bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                                <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                            </a>
                            <a href="{{ route('admin.courses.show', $c) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-white bg-[#051c45] hover:bg-[#0a2540] rounded-lg transition-colors shadow-xs">
                                Manage →
                            </a>
                            <form action="{{ route('admin.courses.destroy', $c) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this course? It will be moved to the Trash.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-rose-600 hover:text-white border border-rose-200 hover:bg-rose-600 rounded-lg transition-colors">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">No courses yet. Create one to get started!</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Express Create Course Modal (4 Essentials First + Collapsible Optional Details) -->
<div id="createModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm flex items-center justify-center z-50 hidden p-4" x-data="{ showAdvancedCreate: false, createPrice: '0.00' }">
    <div class="bg-white rounded-2xl p-6 w-full max-w-lg shadow-2xl border border-slate-200">
        <div class="flex justify-between items-center mb-4 pb-3 border-b border-slate-100">
            <div>
                <span class="text-[10px] font-black uppercase tracking-wider text-emerald-600">10-Second Launchpad</span>
                <h3 class="text-lg font-black text-[#051c45]">Create New Course</h3>
            </div>
            <button onclick="document.getElementById('createModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl leading-none">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4 max-h-[74vh] overflow-y-auto pr-2">
                <!-- Essential 1: Title -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Course Title <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" required placeholder="e.g. Communication Skills for Career Success" class="w-full rounded-xl border-slate-300 focus:border-[#051c45] focus:ring-[#051c45] text-sm font-bold text-[#051c45]">
                </div>

                <!-- Essential 2: Tuition Fee & Category -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-slate-700">Price (৳ BDT)</label>
                            <button type="button" @click="createPrice = '0.00'" class="text-[10px] font-bold text-emerald-600 hover:underline">Set Free (৳0)</button>
                        </div>
                        <input type="number" step="0.01" min="0" name="price" x-model="createPrice" class="w-full rounded-xl border-slate-300 focus:border-[#051c45] focus:ring-[#051c45] text-sm font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Category</label>
                        <input list="category-options-create" type="text" name="category" placeholder="Choose or type..." class="w-full rounded-xl border-slate-300 focus:border-[#051c45] focus:ring-[#051c45] text-sm">
                        <datalist id="category-options-create">
                            @foreach(\App\Models\Category::active()->ordered()->get() as $cat)
                                <option value="{{ $cat->name }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                </div>

                <!-- Essential 3: Format -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Attendance Format / Venue</label>
                    <input type="text" id="create-course-venue" name="venue" value="Online Live (Zoom)" class="w-full rounded-xl border-slate-300 focus:border-[#051c45] focus:ring-[#051c45] text-sm">
                    <div class="flex flex-wrap gap-1.5 mt-1.5">
                        <button type="button" onclick="document.getElementById('create-course-venue').value='Online Live (Zoom)'" class="px-2.5 py-1 rounded-md bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-[10px] font-bold transition-colors">Online Only (Zoom)</button>
                        <button type="button" onclick="document.getElementById('create-course-venue').value='Executive Campus'" class="px-2.5 py-1 rounded-md bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 text-[10px] font-bold transition-colors">Campus Only</button>
                        <button type="button" onclick="document.getElementById('create-course-venue').value='Hybrid (Online Zoom & Executive Campus)'" class="px-2.5 py-1 rounded-md bg-sky-50 hover:bg-sky-100 text-sky-700 border border-sky-200 text-[10px] font-bold transition-colors">Hybrid (Both)</button>
                    </div>
                </div>

                <!-- Essential 4: Cover Photo -->
                <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Course Cover Photo (Optional)</label>
                    <div class="flex items-center gap-4">
                        <div class="w-24 h-14 rounded-lg overflow-hidden border border-slate-300 bg-slate-900 shrink-0 shadow-inner flex items-center justify-center">
                            <img id="create_course_cover_preview" src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=300&q=80" alt="Preview" class="w-full h-full object-contain">
                        </div>
                        <div class="flex-1">
                            <input type="file" id="create_course_cover_input" name="cover_image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#051c45] file:text-white hover:file:opacity-90 file:cursor-pointer cursor-pointer border border-slate-300 rounded-lg p-1 bg-white">
                            <input type="hidden" name="cover_image_base64" id="create_course_cover_base64">
                        </div>
                    </div>
                </div>
                <script>
                    document.getElementById('create_course_cover_input')?.addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(evt) {
                                document.getElementById('create_course_cover_base64').value = evt.target.result;
                                const preview = document.getElementById('create_course_cover_preview');
                                if (preview) preview.src = evt.target.result;
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                </script>

                <!-- Collapsible Optional Details -->
                <div class="border-t border-slate-100 pt-2">
                    <button type="button" @click="showAdvancedCreate = !showAdvancedCreate" class="w-full py-2 px-3 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-bold flex items-center justify-between transition-colors">
                        <span>+ Optional Details (Schedule, Description, Faculty, WhatsApp)</span>
                        <i class="fas" :class="showAdvancedCreate ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>

                    <div x-show="showAdvancedCreate" x-cloak class="space-y-3.5 mt-3 pt-2">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Starting Date</label>
                                <input type="date" name="start_date" class="w-full rounded-xl border-slate-300 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Duration</label>
                                <input type="text" name="duration" placeholder="e.g. 1.5 Hours / 8 Days" class="w-full rounded-xl border-slate-300 text-xs">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Batch Label</label>
                                <input type="text" name="batch" placeholder="e.g. 105th Batch" class="w-full rounded-xl border-slate-300 text-xs">
                            </div>
                            <div class="flex items-end">
                                <label class="flex items-center gap-2 cursor-pointer p-2 rounded-xl border border-slate-200 bg-slate-50 w-full">
                                    <input type="checkbox" name="show_batch" value="1" class="rounded text-[#051c45]">
                                    <span class="text-xs font-semibold text-slate-700">Show Batch</span>
                                </label>
                            </div>
                        </div>
                        @if(auth()->user()->isAdmin())
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Assign Faculty / Instructor</label>
                            <select name="instructor_id" class="w-full rounded-xl border-slate-300 text-xs">
                                <option value="">-- Assign Instructor Later --</option>
                                @foreach($instructors as $instructor)
                                    <option value="{{ $instructor->id }}">{{ $instructor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Short Excerpt / Card Snippet</label>
                            <input type="text" name="short_description" placeholder="Brief 1-2 sentence overview" class="w-full rounded-xl border-slate-300 text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Full Description</label>
                            <textarea name="description" rows="2" class="w-full rounded-xl border-slate-300 text-xs"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Course Counselor WhatsApp Override</label>
                            <input type="text" name="counselor_phone" placeholder="Leave blank for Global Hotline ({{ \App\Models\Setting::getVal('contact_hotline', '01917-054664') }})" class="w-full rounded-xl border-slate-300 text-xs">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_published" value="1" checked class="rounded text-[#051c45] focus:ring-[#051c45]">
                        <span class="text-xs font-bold text-slate-700">Publish immediately</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="certificate_enabled" value="1" class="rounded text-emerald-600 focus:ring-emerald-500">
                        <span class="text-xs font-bold text-emerald-800">Enable Certificates</span>
                    </label>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
                    <button type="submit" class="w-full px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-[#051c45] rounded-xl font-bold text-xs transition-colors">
                        Create & Stay on List
                    </button>
                    <button type="submit" name="open_workspace" value="1" class="w-full px-4 py-2.5 bg-[#051c45] hover:bg-[#0a2540] text-white rounded-xl font-extrabold text-xs transition-colors shadow-xs">
                        Create & Open Workspace &rarr;
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
