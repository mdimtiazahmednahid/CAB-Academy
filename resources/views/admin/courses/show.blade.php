@extends('layouts.admin')

@section('header_title', $course->title)

@section('content')
@php
    $totalLessonsCount = $course->sections->sum(fn($s) => $s->lessons->count());
    $realizedCourseRevenue = (float) $course->payments->where('status', 'verified')->sum('amount');
    $pendingCoursePayments = $course->payments->where('status', 'pending')->count();
@endphp

<div x-data="{ wsTab: 'all', editingTitle: false, titleDraft: @js($course->title), editingPrice: false, priceDraft: @js($course->price) }">
<div class="mb-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
    <div class="flex items-center gap-3 flex-wrap flex-1 min-w-0">
        <a href="{{ route('admin.courses') }}" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-[#051c45] hover:border-[#051c45] flex items-center justify-center transition-colors shadow-xs shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <!-- Inline Title Display & 1-Click Quick Editor -->
                <div x-show="!editingTitle" class="flex items-center gap-2 group">
                    <h1 @click="editingTitle = true; $nextTick(() => $refs.inlineTitleInput.focus())"
                        title="Click to edit title only"
                        class="text-xl sm:text-2xl font-black text-[#051c45] tracking-tight cursor-pointer hover:text-[#c30102] transition-colors">
                        {{ $course->title }}
                    </h1>
                    <button type="button"
                            @click="editingTitle = true; $nextTick(() => $refs.inlineTitleInput.focus())"
                            title="Quick-edit course title only"
                            class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-[#051c45] text-slate-500 hover:text-white text-[11px] font-bold transition-all inline-flex items-center gap-1">
                        <i class="fas fa-pen text-[9px]"></i>
                        <span>Edit Title</span>
                    </button>
                </div>

                <!-- Inline Title Form (Saves ONLY Title) -->
                <form x-show="editingTitle" x-cloak method="POST" action="{{ route('admin.courses.update', $course) }}" class="flex items-center gap-2 w-full max-w-xl">
                    @csrf
                    <input type="hidden" name="_section_label" value="Course Title">
                    <input x-ref="inlineTitleInput" type="text" name="title" x-model="titleDraft" required
                           @keydown.escape.window="editingTitle = false; titleDraft = @js($course->title)"
                           class="flex-1 rounded-xl border-slate-300 focus:border-[#051c45] focus:ring-[#051c45] text-sm font-bold text-[#051c45] py-1.5 px-3">
                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-extrabold shadow-xs shrink-0">
                        Save Title
                    </button>
                    <button type="button" @click="editingTitle = false; titleDraft = @js($course->title)" class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold shrink-0">
                        Cancel
                    </button>
                </form>

                <!-- 1-Click Publish / Draft Toggle (Saves ONLY Publish Status) -->
                <form method="POST" action="{{ route('admin.courses.update', $course) }}" class="inline-block">
                    @csrf
                    <input type="hidden" name="_section_label" value="Publish Status">
                    <input type="hidden" name="is_published" value="{{ $course->is_published ? '0' : '1' }}">
                    @if($course->is_published)
                        <button type="submit" title="Click to switch to Draft" class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-full text-xs font-bold inline-flex items-center gap-1.5 transition-all cursor-pointer">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span>Published</span>
                            <i class="fas fa-rotate text-[9px] opacity-60"></i>
                        </button>
                    @else
                        <button type="submit" title="Click to Publish immediately" class="px-2.5 py-1 bg-slate-100 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 border border-slate-200 hover:border-emerald-200 rounded-full text-xs font-bold inline-flex items-center gap-1.5 transition-all cursor-pointer">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                            <span>Draft (Click to Publish)</span>
                        </button>
                    @endif
                </form>

                <!-- 1-Click Certificates Toggle (Saves ONLY Certificate Release Status) -->
                <form method="POST" action="{{ route('admin.courses.update', $course) }}" class="inline-block">
                    @csrf
                    <input type="hidden" name="_section_label" value="Certificate Release Status">
                    <input type="hidden" name="certificate_enabled" value="{{ $course->certificate_enabled ? '0' : '1' }}">
                    @if($course->certificate_enabled)
                        <button type="submit" title="Click to Lock Certificates" class="px-2.5 py-1 bg-emerald-100 hover:bg-amber-50 text-emerald-800 hover:text-amber-800 border border-emerald-200 hover:border-amber-200 rounded-full text-xs font-bold inline-flex items-center gap-1 transition-all cursor-pointer">
                            <i class="fas fa-award text-emerald-600"></i>
                            <span>Certificates Released</span>
                        </button>
                    @else
                        <button type="submit" title="Click to Release Digital Certificates to Students" class="px-2.5 py-1 bg-amber-50 hover:bg-emerald-50 text-amber-800 hover:text-emerald-800 border border-amber-200 hover:border-emerald-200 rounded-full text-xs font-bold inline-flex items-center gap-1 transition-all cursor-pointer">
                            <i class="fas fa-lock text-amber-600 text-[10px]"></i>
                            <span>Certificates Locked (Click to Release)</span>
                        </button>
                    @endif
                </form>
            </div>
        </div>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('courses.show', $course) }}" target="_blank" class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5 shadow-xs">
            <i class="fas fa-arrow-up-right-from-square text-[10px] text-[#c30102]"></i>
            <span>Public Page</span>
        </a>
        <button type="button" @click="wsTab = 'settings'; $nextTick(() => document.getElementById('modularCourseSettings')?.scrollIntoView({ behavior: 'smooth', block: 'start' }))" class="px-3.5 py-2 bg-white hover:bg-slate-50 text-[#051c45] border border-slate-200 rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5 shadow-xs">
            <i class="fas fa-sliders text-emerald-600"></i>
            <span>Modular Settings</span>
        </button>
        <button onclick="document.getElementById('quizModal').classList.remove('hidden')" class="px-3.5 py-2 bg-[#051c45] hover:bg-[#0a2540] text-white rounded-xl text-xs font-bold transition-colors shadow-xs">
            + Add Quiz
        </button>
        <button onclick="document.getElementById('sectionModal').classList.remove('hidden')" class="px-3.5 py-2 bg-[#c30102] hover:bg-[#a50102] text-white rounded-xl text-xs font-bold transition-colors shadow-xs">
            + Add Section
        </button>
    </div>
</div>

@if(session('success'))
    <div class="p-4 mb-6 text-sm text-emerald-800 rounded-2xl bg-emerald-50 border border-emerald-200 font-semibold flex items-center gap-2 shadow-xs">
        <i class="fas fa-check-circle text-emerald-600"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<!-- Course At-A-Glance KPI Strip (with 1-Click Inline Fee Edit) -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="flex items-center justify-between">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Tuition & Revenue</div>
            <button type="button" @click="editingPrice = !editingPrice" class="text-[10px] font-bold text-indigo-600 hover:text-indigo-800">
                <i class="fas fa-pen text-[9px]"></i> Edit Fee
            </button>
        </div>
        <div x-show="!editingPrice" class="text-xl font-black text-[#051c45] mt-1">৳{{ number_format($realizedCourseRevenue, 0) }}</div>
        <div x-show="!editingPrice" class="text-[11px] text-slate-500 mt-0.5">
            Fee: <strong class="text-slate-700">{{ $course->formatted_price }}</strong>
            @if($pendingCoursePayments > 0) &bull; <span class="text-amber-600 font-bold">{{ $pendingCoursePayments }} Pending</span> @endif
        </div>
        <form x-show="editingPrice" x-cloak method="POST" action="{{ route('admin.courses.update', $course) }}" class="mt-2 flex items-center gap-1.5">
            @csrf
            <input type="hidden" name="_section_label" value="Course Tuition Fee">
            <input type="number" step="0.01" min="0" name="price" x-model="priceDraft" class="w-24 rounded-lg border-slate-300 text-xs font-bold py-1 px-2">
            <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-600 text-white text-[11px] font-bold">Save</button>
            <button type="button" @click="priceDraft = 0" class="px-2 py-1 rounded-lg bg-slate-100 text-slate-600 text-[10px] font-bold" title="Set Free (0 BDT)">Free</button>
        </form>
    </div>
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Enrolled Students</div>
        <div class="text-xl font-black text-[#051c45] mt-1">{{ $course->students->count() }}</div>
        <div class="text-[11px] text-emerald-600 font-semibold mt-0.5">{{ $course->certificates->count() }} Certificates Issued</div>
    </div>
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Curriculum Outline</div>
        <div class="text-xl font-black text-[#051c45] mt-1">{{ $course->sections->count() }} Sections</div>
        <div class="text-[11px] text-slate-500 mt-0.5">{{ $totalLessonsCount }} Video & Text Lessons</div>
    </div>
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Assessments & Files</div>
        <div class="text-xl font-black text-[#051c45] mt-1">{{ $course->quizzes->count() }} Quizzes</div>
        <div class="text-[11px] text-slate-500 mt-0.5">{{ $course->materials->count() }} Downloadable Resources</div>
    </div>
</div>

<!-- Workspace Filter Tabs -->
<div class="mb-6 bg-white p-2 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-1.5 overflow-x-auto">
    <button type="button" @click="wsTab = 'all'" :class="wsTab === 'all' ? 'bg-[#051c45] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap">
        All Workspace Modules
    </button>
    <button type="button" @click="wsTab = 'settings'" :class="wsTab === 'settings' ? 'bg-[#051c45] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-1.5">
        <i class="fas fa-sliders text-emerald-500"></i>
        <span>Course Settings (Modular)</span>
    </button>
    <button type="button" @click="wsTab = 'curriculum'" :class="wsTab === 'curriculum' ? 'bg-[#051c45] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap">
        Curriculum & Quizzes ({{ $course->sections->count() }})
    </button>
    <button type="button" @click="wsTab = 'students'" :class="wsTab === 'students' ? 'bg-[#051c45] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap">
        Enrolled Students ({{ $course->students->count() }})
    </button>
    <button type="button" @click="wsTab = 'payments'" :class="wsTab === 'payments' ? 'bg-[#051c45] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap">
        Payments ({{ $course->payments->count() }})
    </button>
    <button type="button" @click="wsTab = 'form'" :class="wsTab === 'form' ? 'bg-[#051c45] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap">
        Registration Form Builder
    </button>
</div>

<!-- Direct 1-Click Course Cover Photo Card (Saves ONLY Picture, Never Touches Any Other Setting) -->
<div class="mb-6 bg-white rounded-2xl border border-slate-200/90 shadow-xs p-4 sm:p-5 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4"
     x-data="{ photoPreview: @js($course->cover_image_url), photoSelected: false, photoBase64: '' }">
    <div class="flex items-center gap-4 w-full lg:w-auto">
        <label for="quick_cover_file_input" title="Click thumbnail to pick a new cover photo" class="relative w-28 h-16 sm:w-36 sm:h-20 rounded-xl overflow-hidden border border-slate-200 bg-slate-900 shadow-inner shrink-0 cursor-pointer group">
            <img :src="photoPreview" alt="{{ $course->title }}" class="w-full h-full object-contain" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=800&q=80';">
            <div class="absolute inset-0 bg-slate-950/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-[10px] font-bold gap-1">
                <i class="fas fa-camera"></i>
                <span>Change</span>
            </div>
        </label>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">1-Click Course Cover Poster</span>
                @if($course->cover_image)
                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded text-[10px] font-bold">Custom Photo Active</span>
                @else
                    <span class="px-2 py-0.5 bg-amber-50 text-amber-700 border border-amber-200 rounded text-[10px] font-bold">Default Photo Active</span>
                @endif
                <span x-show="photoSelected" x-cloak class="px-2 py-0.5 bg-indigo-50 text-indigo-700 border border-indigo-200 rounded text-[10px] font-extrabold animate-pulse">New Photo Ready to Save</span>
            </div>
            <h3 class="text-sm font-bold text-slate-900 mt-1 truncate">{{ $course->title }}</h3>
            <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1.5 flex-wrap">
                <span>Faculty:</span>
                <img src="{{ $course->instructor_avatar_url }}" alt="{{ $course->instructor_name }}" class="w-4 h-4 rounded-full object-cover border border-slate-300 inline-block" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($course->instructor_name) }}&background=051c45&color=fff';">
                <span class="font-semibold text-slate-800">{{ $course->instructor_name }}</span>
                <span>&bull; Fee: <span class="font-bold text-[#051c45]">{{ $course->formatted_price }}</span></span>
                <span>&bull; Format: <span class="font-semibold text-slate-700">{{ $course->venue ?: 'Online Live (Zoom)' }}</span></span>
            </p>
        </div>
    </div>

    <!-- Dedicated Photo-Only Form -->
    <form method="POST" action="{{ route('admin.courses.update', $course) }}" enctype="multipart/form-data" class="flex flex-wrap items-center gap-2 w-full lg:w-auto justify-end">
        @csrf
        <input type="hidden" name="_section_label" value="Course Cover Photo">
        <input type="hidden" name="cover_image_base64" :value="photoBase64">
        <input type="file" id="quick_cover_file_input" name="cover_image" accept="image/*" class="hidden"
               @change="
                   const file = $event.target.files[0];
                   if (file) {
                       photoSelected = true;
                       const reader = new FileReader();
                       reader.onload = (evt) => {
                           photoPreview = evt.target.result;
                           photoBase64 = evt.target.result;
                       };
                       reader.readAsDataURL(file);
                   }
               ">
        <label for="quick_cover_file_input" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-[#051c45] border border-slate-200 rounded-xl text-xs font-bold transition-all cursor-pointer inline-flex items-center gap-1.5 shadow-2xs">
            <i class="fas fa-image text-[#051c45]"></i>
            <span x-text="photoSelected ? 'Choose Different Picture' : 'Select New Picture'"></span>
        </label>
        <button x-show="photoSelected" x-cloak type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-extrabold transition-all inline-flex items-center gap-1.5 shadow-sm">
            <i class="fas fa-cloud-arrow-up"></i>
            <span>Save Picture Only</span>
        </button>
    </form>
</div>

<!-- Modular Course Settings Studio (3 Independent Non-Destructive Cards) -->
<div id="modularCourseSettings" x-show="wsTab === 'settings'" x-cloak class="mb-8 space-y-5">
    <div class="bg-gradient-to-r from-[#051c45] to-[#0a2540] text-white p-5 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
        <div>
            <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 text-[10px] font-black uppercase tracking-wider">Modular Non-Destructive Settings</span>
            <h2 class="text-lg font-black mt-1">Independent Course Settings Cards</h2>
            <p class="text-xs text-slate-300">Save only the specific section you edited — other fields and your custom registration form remain 100% untouched.</p>
        </div>
        <button type="button" @click="wsTab = 'all'" class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold border border-white/15 self-start sm:self-auto">
            &larr; Back to All Modules
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Card 1: Core Identity & Faculty (Saves ONLY Identity Fields) -->
        <form method="POST" action="{{ route('admin.courses.update', $course) }}" class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-5 flex flex-col justify-between space-y-4">
            @csrf
            <input type="hidden" name="_section_label" value="Course Identity & Faculty">
            <div class="space-y-3.5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-wider text-indigo-600">Module 1</span>
                        <h3 class="text-sm font-black text-[#051c45]">Identity, Category & Faculty</h3>
                    </div>
                    <i class="fas fa-id-card text-slate-300 text-lg"></i>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Course Title <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" value="{{ $course->title }}" required class="w-full rounded-xl border-slate-200 focus:border-[#051c45] focus:ring-[#051c45] text-xs font-bold text-[#051c45]">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Category</label>
                        <input list="category-options-modular" type="text" name="category" value="{{ $course->category }}" placeholder="e.g. Leadership" class="w-full rounded-xl border-slate-200 focus:border-[#051c45] focus:ring-[#051c45] text-xs">
                        <datalist id="category-options-modular">
                            @foreach(\App\Models\Category::active()->ordered()->get() as $cat)
                                <option value="{{ $cat->name }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Level</label>
                        <input type="text" name="level" value="{{ $course->level }}" placeholder="e.g. Executive / Certificate" class="w-full rounded-xl border-slate-200 focus:border-[#051c45] focus:ring-[#051c45] text-xs">
                    </div>
                </div>
                @if(auth()->user()->isAdmin() && isset($instructors))
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Assigned Faculty / Instructor</label>
                    <select name="instructor_id" class="w-full rounded-xl border-slate-200 focus:border-[#051c45] focus:ring-[#051c45] text-xs font-semibold">
                        <option value="">-- No Instructor Assigned --</option>
                        @foreach($instructors as $instructor)
                            <option value="{{ $instructor->id }}" {{ $course->instructor_id == $instructor->id ? 'selected' : '' }}>
                                {{ $instructor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Short Card Excerpt (1-2 sentences)</label>
                    <input type="text" name="short_description" value="{{ $course->short_description }}" placeholder="Brief summary shown on catalog cards" class="w-full rounded-xl border-slate-200 focus:border-[#051c45] focus:ring-[#051c45] text-xs">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Full Overview / Description</label>
                    <textarea name="description" rows="3" class="w-full rounded-xl border-slate-200 focus:border-[#051c45] focus:ring-[#051c45] text-xs">{{ $course->description }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Target Audience / Who Should Attend</label>
                    <textarea name="target_audience" rows="2" placeholder="e.g. Commercial Executives, Entrepreneurs..." class="w-full rounded-xl border-slate-200 focus:border-[#051c45] focus:ring-[#051c45] text-xs">{{ $course->target_audience }}</textarea>
                </div>
            </div>
            <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-[#051c45] hover:bg-[#0a2540] text-white text-xs font-extrabold transition-all shadow-xs flex items-center justify-center gap-2">
                <i class="fas fa-check text-emerald-400"></i>
                <span>Save Identity & Faculty Only</span>
            </button>
        </form>

        <!-- Card 2: Tuition Fee, Schedule, Batch & Attendance Mode (Saves ONLY Schedule/Pricing) -->
        <form method="POST" action="{{ route('admin.courses.update', $course) }}" class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-5 flex flex-col justify-between space-y-4"
              x-data="{ modVenue: @js($course->venue ?: 'Online Live (Zoom)'), modPrice: @js($course->price) }">
            @csrf
            <input type="hidden" name="_section_label" value="Tuition Fee & Schedule">
            <div class="space-y-3.5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-wider text-emerald-600">Module 2</span>
                        <h3 class="text-sm font-black text-[#051c45]">Tuition Fee, Schedule & Format</h3>
                    </div>
                    <i class="fas fa-calendar-check text-slate-300 text-lg"></i>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-slate-700">Price (৳ BDT)</label>
                            <button type="button" @click="modPrice = 0" class="text-[10px] font-bold text-emerald-600 hover:underline">Set Free (৳0)</button>
                        </div>
                        <input type="number" step="0.01" min="0" name="price" x-model="modPrice" class="w-full rounded-xl border-slate-200 focus:border-[#051c45] focus:ring-[#051c45] text-xs font-black text-[#051c45]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Starting Date</label>
                        <input type="date" name="start_date" value="{{ $course->start_date ? $course->start_date->format('Y-m-d') : '' }}" class="w-full rounded-xl border-slate-200 focus:border-[#051c45] focus:ring-[#051c45] text-xs">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Duration / Schedule</label>
                    <input type="text" name="duration" value="{{ $course->duration }}" placeholder="e.g. 1.5 Hours / 8 Weeks" class="w-full rounded-xl border-slate-200 focus:border-[#051c45] focus:ring-[#051c45] text-xs">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Attendance Format / Venue</label>
                    <input type="text" name="venue" x-model="modVenue" class="w-full rounded-xl border-slate-200 focus:border-[#051c45] focus:ring-[#051c45] text-xs font-semibold">
                    <div class="grid grid-cols-3 gap-1.5 mt-1.5">
                        <button type="button" @click="modVenue = 'Online Live (Zoom)'" class="px-2 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-[10px] font-bold transition-colors">Online Only</button>
                        <button type="button" @click="modVenue = 'Executive Campus'" class="px-2 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 text-[10px] font-bold transition-colors">Campus Only</button>
                        <button type="button" @click="modVenue = 'Hybrid (Online Zoom & Executive Campus)'" class="px-2 py-1.5 rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-700 border border-sky-200 text-[10px] font-bold transition-colors">Hybrid (Both)</button>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3 pt-1">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Batch Label</label>
                        <input type="text" name="batch" value="{{ $course->batch }}" placeholder="e.g. 105th Batch" class="w-full rounded-xl border-slate-200 focus:border-[#051c45] focus:ring-[#051c45] text-xs">
                    </div>
                    <div class="flex items-end">
                        <input type="hidden" name="show_batch" value="0">
                        <label class="flex items-center gap-2 cursor-pointer p-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 transition-colors w-full">
                            <input type="checkbox" name="show_batch" value="1" {{ $course->show_batch ? 'checked' : '' }} class="rounded text-[#051c45] focus:ring-[#051c45]">
                            <span class="text-[11px] font-bold text-slate-700">Show Batch</span>
                        </label>
                    </div>
                </div>
            </div>
            <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-extrabold transition-all shadow-xs flex items-center justify-center gap-2">
                <i class="fas fa-check"></i>
                <span>Save Tuition & Schedule Only</span>
            </button>
        </form>

        <!-- Card 3: Counselor WhatsApp, Hotline & Status Controls (Saves ONLY WhatsApp & Visibility) -->
        <form method="POST" action="{{ route('admin.courses.update', $course) }}" class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-5 flex flex-col justify-between space-y-4"
              x-data="{ waMsg: @js($course->whatsapp_message ?? '') }">
            @csrf
            <input type="hidden" name="_section_label" value="WhatsApp & Visibility Settings">
            <div class="space-y-3.5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-wider text-emerald-600">Module 3</span>
                        <h3 class="text-sm font-black text-[#051c45]">Counselor WhatsApp & Visibility</h3>
                    </div>
                    <i class="fab fa-whatsapp text-emerald-500 text-lg"></i>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Per-Course Counselor Phone / WhatsApp</label>
                    <input type="text" name="counselor_phone" value="{{ $course->counselor_phone }}" placeholder="Blank = Global ({{ \App\Models\Setting::getVal('contact_hotline', '01917-054664') }})" class="w-full rounded-xl border-slate-200 focus:border-[#051c45] focus:ring-[#051c45] text-xs font-semibold">
                    <p class="text-[10px] text-slate-400 mt-1">Active on page: <strong class="text-slate-700">{{ $course->effective_counselor_phone }}</strong></p>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold text-slate-700">Custom WhatsApp Inquiry Message</label>
                        <button type="button" @click="waMsg = ''" class="text-[10px] font-bold text-slate-400 hover:text-rose-600">Reset Default</button>
                    </div>
                    <textarea name="whatsapp_message" x-model="waMsg" rows="3" placeholder="Assalamu Alaikum! I would like to inquire/enroll in *{title}* (Fee: {price}). Please share batch details and enrollment procedure: {url}" class="w-full rounded-xl border-slate-200 focus:border-[#051c45] focus:ring-[#051c45] text-xs"></textarea>
                    <div class="flex flex-wrap gap-1 mt-1.5">
                        <span class="text-[10px] text-slate-400 mr-1">Insert tag:</span>
                        <button type="button" @click="waMsg = (waMsg || '') + ' *{title}*'" class="px-1.5 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-[10px] font-mono text-slate-700">{title}</button>
                        <button type="button" @click="waMsg = (waMsg || '') + ' (Fee: {price})'" class="px-1.5 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-[10px] font-mono text-slate-700">{price}</button>
                        <button type="button" @click="waMsg = (waMsg || '') + ' {batch}'" class="px-1.5 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-[10px] font-mono text-slate-700">{batch}</button>
                        <button type="button" @click="waMsg = (waMsg || '') + ' {url}'" class="px-1.5 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-[10px] font-mono text-slate-700">{url}</button>
                    </div>
                </div>
                <div class="pt-2 border-t border-slate-100 space-y-2">
                    <input type="hidden" name="is_published" value="0">
                    <label class="flex items-center justify-between p-2.5 rounded-xl border border-slate-200 bg-slate-50/70 cursor-pointer">
                        <span class="text-xs font-bold text-slate-700">Published on Catalog</span>
                        <input type="checkbox" name="is_published" value="1" {{ $course->is_published ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500">
                    </label>
                    <input type="hidden" name="certificate_enabled" value="0">
                    <label class="flex items-center justify-between p-2.5 rounded-xl border border-emerald-200 bg-emerald-50/50 cursor-pointer">
                        <span class="text-xs font-bold text-emerald-950">Release Digital Certificates</span>
                        <input type="checkbox" name="certificate_enabled" value="1" {{ $course->certificate_enabled ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500">
                    </label>
                </div>
            </div>
            <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-[#051c45] hover:bg-[#0a2540] text-white text-xs font-extrabold transition-all shadow-xs flex items-center justify-center gap-2">
                <i class="fab fa-whatsapp text-emerald-400"></i>
                <span>Save WhatsApp & Status Only</span>
            </button>
        </form>
    </div>

    <!-- Module 4: Facebook Rich Link Preview & Stylish Social Caption Kit -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-5"
         x-data="{ copiedKey: '' }">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4 mb-4">
            <div>
                <span class="text-[10px] font-black uppercase tracking-wider text-[#1877F2]">Module 4 • Social & Open Graph Studio</span>
                <h3 class="text-sm font-black text-[#051c45]">Facebook Rich Link Preview & Auto-Generated Stylish Captions</h3>
                <p class="text-xs text-slate-500">Automatically formatted in Unicode Bold Serif (<span class="font-serif">{{ $course->stylish_post_header }}</span>) with dynamic start date.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button type="button"
                        @click="navigator.clipboard.writeText({{ Js::from($course->stylish_post_header) }}); copiedKey = 'header'; setTimeout(() => copiedKey = '', 2000)"
                        class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-[#051c45] text-xs font-bold transition-all inline-flex items-center gap-1.5">
                    <i class="fas fa-font text-[#c30102]"></i>
                    <span x-text="copiedKey === 'header' ? 'Copied Headline!' : 'Copy Bold Headline'"></span>
                </button>
                <button type="button"
                        @click="navigator.clipboard.writeText({{ Js::from($course->stylish_social_caption) }}); copiedKey = 'full'; setTimeout(() => copiedKey = '', 2000)"
                        class="px-3.5 py-2 rounded-xl bg-[#1877F2] hover:bg-[#166fe5] text-white text-xs font-extrabold transition-all inline-flex items-center gap-1.5 shadow-2xs">
                    <i class="fas fa-wand-magic-sparkles"></i>
                    <span x-text="copiedKey === 'full' ? 'Copied Full Caption!' : 'Copy Full Stylish Post + Link'"></span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
            <!-- Left: Live Facebook Mobile Feed Card Preview -->
            <div>
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Live Facebook Link Card Preview</div>
                <x-link-preview-card :preview="\App\Support\LinkPreviewResolver::resolve($course->short_url)" :showPostHeader="true" />
            </div>

            <!-- Right: Ready-to-Paste Stylish Promotional Caption -->
            <div class="space-y-3">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Clean Facebook Post Caption (1-Line Title + Link)</span>
                        <span class="text-[10px] font-semibold text-emerald-600">Matches Reference Feed Style</span>
                    </div>
                    <textarea readonly rows="4" class="w-full rounded-2xl border border-slate-200 bg-slate-50 p-3.5 text-sm text-slate-900 font-serif font-bold leading-relaxed focus:outline-none select-all">{{ $course->stylish_social_caption }}</textarea>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                    <div class="text-[11px] text-slate-600 leading-snug">
                        <strong class="text-[#051c45] block">Updated Course Photo or Start Date?</strong>
                        Click Refresh on Facebook Sharing Debugger to clear Facebook's cached preview.
                    </div>
                    <a href="{{ $course->facebook_debugger_url }}" target="_blank" rel="noopener noreferrer" class="px-3 py-2 rounded-xl bg-white hover:bg-slate-100 text-[#1877F2] border border-slate-200 text-xs font-extrabold whitespace-nowrap inline-flex items-center gap-1.5 shadow-2xs shrink-0">
                        <i class="fab fa-facebook"></i>
                        <span>Refresh FB Cache</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dynamic Course Registration & Enrollment Form Builder Studio -->
<script>
    window.__courseFormBuilderConfig = {
        initialFields: @json($course->effective_enrollment_fields),
        templates: @json(\App\Models\Course::getBuiltinTemplates()),
        quickQuestions: @json(\App\Models\Course::getQuickAddQuestions()),
        isOnlineOnly: {{ $course->isOnlineOnly() ? 'true' : 'false' }},
        isCampusOnly: {{ $course->isCampusOnly() ? 'true' : 'false' }}
    };
</script>
<div id="formBuilderCard" 
     x-show="wsTab === 'all' || wsTab === 'form'"
     x-data="courseFormBuilder(window.__courseFormBuilderConfig)" 
     class="mb-8 bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
     
    <!-- Studio Header -->
    <div class="p-5 sm:p-6 bg-gradient-to-r from-[#020b18] via-[#051c45] to-[#0a2540] text-white flex flex-col lg:flex-row lg:items-center justify-between gap-4 relative overflow-hidden">
        <div class="pointer-events-none absolute -top-24 -right-24 w-72 h-72 rounded-full bg-indigo-500/15 blur-3xl"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="px-2.5 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-400/30 text-[10px] font-black uppercase tracking-wider flex items-center gap-1.5">
                    <i class="fas fa-bolt text-amber-400"></i>
                    <span>Dynamic Enrollment Engine</span>
                </span>
                <span class="px-2.5 py-0.5 rounded-full bg-white/10 text-white border border-white/15 text-[10px] font-bold" x-text="activeFieldsCount + ' Active Questions (' + requiredFieldsCount + ' Mandatory)'"></span>
                <template x-if="isEmailEnabled">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold" :class="isEmailRequired ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-400/30' : 'bg-amber-500/20 text-amber-300 border border-amber-400/30'" x-text="isEmailRequired ? '✉ Email: Required (*)' : '✉ Email: Optional'"></span>
                </template>
                <template x-if="!isEmailEnabled">
                    <span class="px-2.5 py-0.5 rounded-full bg-rose-500/25 text-rose-300 border border-rose-400/40 text-[10px] font-bold flex items-center gap-1">
                        <i class="fas fa-ban text-[9px]"></i>
                        <span>Email: Disabled (Phone-Only Mode)</span>
                    </span>
                </template>
            </div>
            <h2 class="text-lg sm:text-xl font-black mt-2 tracking-tight text-white flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-white/10 border border-white/15 inline-flex items-center justify-center text-indigo-300 text-sm">
                    <i class="fas fa-sliders-h"></i>
                </span>
                <span>Per-Course Registration Form Studio</span>
            </h2>
            <p class="text-xs text-slate-300 mt-1 max-w-2xl leading-relaxed">
                Switch between 6 instant conversion templates, add smart 1-click question blocks (dropdowns, choice pills, company/role, expectations), and test the live student checkout drawer side-by-side.
            </p>
        </div>

        <div class="relative z-10 flex items-center gap-2.5 flex-wrap shrink-0">
            <a href="{{ route('admin.courses.export_registrations', $course) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold border border-white/20 bg-white/10 hover:bg-white/20 text-white transition-all flex items-center gap-1.5" title="Download all student registrations & custom question answers as CSV">
                <i class="fas fa-file-csv text-emerald-400"></i>
                <span>Export Responses CSV</span>
            </a>

            <button type="button" @click="isCollapsed = !isCollapsed" class="px-3.5 py-2 rounded-xl text-xs font-bold border border-white/20 bg-white/10 hover:bg-white/20 text-white transition-all flex items-center gap-1.5 cursor-pointer">
                <i class="fas" :class="isCollapsed ? 'fa-expand-alt' : 'fa-compress-alt'"></i>
                <span x-text="isCollapsed ? 'Expand Studio' : 'Compact View'"></span>
            </button>

            <button type="button" @click="saveForm(false)" :disabled="isSaving" class="px-4 py-2.5 bg-gradient-to-r from-[#c30102] to-[#a50102] hover:from-[#a50102] hover:to-[#8b0001] disabled:opacity-50 text-white rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-lg flex items-center gap-1.5 cursor-pointer">
                <i class="fas" :class="isSaving ? 'fa-spinner fa-spin' : 'fa-check-circle'"></i>
                <span x-text="isSaving ? 'Saving...' : 'Save Form Setup'"></span>
            </button>
        </div>
    </div>

    <!-- Alert / Toast Messages -->
    <div x-show="saveMessage" x-transition class="p-3.5 bg-emerald-50 border-b border-emerald-200 text-xs text-emerald-800 font-bold flex items-center justify-between gap-2" style="display: none;">
        <div class="flex items-center gap-2">
            <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
            <span x-text="saveMessage"></span>
        </div>
        <button type="button" @click="saveMessage = ''" class="text-emerald-600 hover:text-emerald-900 text-xs font-black">&times;</button>
    </div>
    <div x-show="saveError" x-transition class="p-3.5 bg-rose-50 border-b border-rose-200 text-xs text-rose-800 font-bold flex items-center justify-between gap-2" style="display: none;">
        <div class="flex items-center gap-2">
            <i class="fas fa-exclamation-circle text-rose-600 text-sm"></i>
            <span x-text="saveError"></span>
        </div>
        <button type="button" @click="saveError = ''" class="text-rose-600 hover:text-rose-900 text-xs font-black">&times;</button>
    </div>

    <!-- Collapsible Studio Workspace -->
    <div x-show="!isCollapsed" x-transition>
        <!-- 1. 1-Click Conversion Templates Bar (6 Presets) -->
        <div class="p-4 sm:p-5 bg-slate-50/90 border-b border-slate-200/80">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                <div class="text-[11px] font-black uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                    <i class="fas fa-magic text-indigo-600"></i>
                    <span>Step 1: Choose a 1-Click Registration Template (or Customize Below)</span>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" @click="toggleEmailQuick()" class="px-2.5 py-1 rounded-lg border text-[11px] font-bold transition-all cursor-pointer flex items-center gap-1.5"
                            :class="isEmailEnabled ? 'bg-white border-slate-200 text-slate-700 hover:bg-slate-100' : 'bg-amber-50 border-amber-200 text-amber-800'">
                        <i class="fas" :class="isEmailEnabled ? 'fa-envelope-open text-emerald-600' : 'fa-envelope-slash text-amber-600'"></i>
                        <span x-text="isEmailEnabled ? '1-Click Disable Email Field' : '1-Click Enable Email Field'"></span>
                    </button>
                    <button type="button" @click="saveForm(true)" :disabled="isSaving" class="px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 text-indigo-700 text-[11px] font-bold transition-all cursor-pointer flex items-center gap-1" title="Apply this form setup across all courses in your catalog">
                        <i class="fas fa-clone text-[10px]"></i>
                        <span>Apply to All Courses</span>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <template x-for="(tpl, key) in templates" :key="key">
                    <div class="text-left p-3.5 rounded-2xl border transition-all relative group flex flex-col justify-between"
                         :class="currentTemplateKey === key ? 'border-[#051c45] bg-[#051c45]/[0.04] shadow-sm ring-2 ring-[#051c45]/15' : 'border-slate-200/90 bg-white hover:border-indigo-300 hover:shadow-xs'">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 text-xs"
                                          :class="currentTemplateKey === key ? 'bg-[#051c45] text-white' : 'bg-slate-100 text-slate-700 group-hover:bg-indigo-50 group-hover:text-indigo-600'">
                                        <i class="fas" :class="tpl.icon || 'fa-layer-group'"></i>
                                    </span>
                                    <span class="text-xs font-extrabold text-slate-900 truncate" x-text="tpl.name"></span>
                                </div>
                                <span class="text-[9px] font-black px-2 py-0.5 rounded-full uppercase shrink-0" 
                                      :class="tpl.key === 'quick_lead' ? 'bg-amber-100 text-amber-800' : (tpl.key === 'corporate' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800')"
                                      x-text="tpl.badge || 'Preset'"></span>
                            </div>
                            <p class="text-[11px] text-slate-500 leading-snug mb-2.5" x-text="tpl.description"></p>
                            <!-- Field Pill Tags inside Preset -->
                            <div class="flex items-center gap-1 flex-wrap mb-2">
                                <template x-for="f in tpl.fields.filter(x => x.is_enabled !== false)" :key="f.name">
                                    <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 text-[9px] font-semibold border border-slate-200/70" x-text="f.label"></span>
                                </template>
                            </div>
                        </div>
                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2 text-[10px]">
                            <button type="button" @click="applyTemplate(key, false)" class="text-indigo-600 hover:text-indigo-800 font-bold flex items-center gap-1 cursor-pointer">
                                <span>Load Preset</span>
                                <i class="fas fa-arrow-right text-[9px]"></i>
                            </button>
                            <button type="button" @click="applyTemplate(key, true)" class="px-2.5 py-1 rounded-lg bg-[#051c45] hover:bg-[#0a2540] text-white font-bold text-[10px] transition-colors cursor-pointer flex items-center gap-1">
                                <i class="fas fa-bolt text-amber-400 text-[9px]"></i>
                                <span>1-Click Apply & Save</span>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- 2. 1-Click Smart Question Bank Bar -->
        <div class="px-5 py-3.5 bg-indigo-50/50 border-b border-indigo-100/80">
            <div class="flex items-center justify-between gap-2 mb-2">
                <span class="text-[11px] font-black uppercase tracking-wider text-indigo-950 flex items-center gap-1.5">
                    <i class="fas fa-plus-circle text-indigo-600"></i>
                    <span>Step 2: 1-Click Quick-Add Smart Questions (Click any chip to add/remove)</span>
                </span>
                <span class="text-[10px] text-indigo-600 font-semibold hidden sm:inline">Includes pre-built Dropdowns, Choice Pills & Executive Fields</span>
            </div>
            <div class="flex items-center gap-1.5 flex-wrap">
                <template x-for="q in quickQuestions" :key="q.name">
                    <button type="button"
                            @click="toggleQuickQuestion(q)"
                            class="px-2.5 py-1.5 rounded-xl text-[11px] font-bold transition-all flex items-center gap-1.5 cursor-pointer border"
                            :class="hasActiveField(q.name)
                                ? 'bg-emerald-600 text-white border-emerald-600 shadow-2xs'
                                : 'bg-white hover:bg-indigo-50 text-slate-700 border-slate-200/90 hover:border-indigo-300'">
                        <i class="fas text-[10px]" :class="hasActiveField(q.name) ? 'fa-check' : (q.icon || 'fa-plus')"></i>
                        <span x-text="q.label"></span>
                        <template x-if="q.type === 'select' || q.type === 'radio'">
                            <span class="text-[9px] px-1 py-0.2 rounded uppercase font-black"
                                  :class="hasActiveField(q.name) ? 'bg-white/20 text-white' : 'bg-indigo-100 text-indigo-700'"
                                  x-text="q.type === 'radio' ? 'Pills' : 'Dropdown'"></span>
                        </template>
                    </button>
                </template>
            </div>
        </div>

        <!-- 3. Split-Pane Interactive Studio: Left = Visual Field Cards, Right = Live Interactive Student Drawer -->
        <div class="p-5 sm:p-6 grid grid-cols-1 xl:grid-cols-12 gap-6 items-start bg-slate-50/30">
            
            <!-- LEFT COLUMN (7 Cols): Visual Question Cards & Custom Question Creator -->
            <div class="xl:col-span-7 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-700 flex items-center gap-2">
                        <i class="fas fa-list-check text-[#051c45]"></i>
                        <span>Configured Registration Questions (<span x-text="fields.length"></span>)</span>
                    </h3>
                    <span class="text-[11px] text-slate-400">Changes reflect immediately in the live preview &rarr;</span>
                </div>

                <!-- Interactive Field Cards List -->
                <div class="space-y-3">
                    <template x-for="(field, idx) in fields" :key="field.name + '_' + idx">
                        <div class="rounded-2xl border transition-all p-3.5 sm:p-4 bg-white"
                             :class="field.is_enabled === false ? 'border-slate-200/70 bg-slate-50/60 opacity-60' : 'border-slate-200/90 shadow-2xs hover:border-slate-300'">
                            
                            <!-- Top Row: Order, Icon, Question Label, Type Selector, Required & Active Toggles -->
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                                
                                <!-- Reorder & Field Key Badge (3 Cols) -->
                                <div class="sm:col-span-3 flex items-center gap-2">
                                    <div class="flex flex-col gap-0.5 shrink-0">
                                        <button type="button" @click="moveField(idx, -1)" :disabled="idx === 0" class="p-1 rounded hover:bg-slate-100 text-slate-400 hover:text-slate-700 disabled:opacity-20 cursor-pointer" title="Move Up">
                                            <i class="fas fa-chevron-up text-[10px]"></i>
                                        </button>
                                        <button type="button" @click="moveField(idx, 1)" :disabled="idx === fields.length - 1" class="p-1 rounded hover:bg-slate-100 text-slate-400 hover:text-slate-700 disabled:opacity-20 cursor-pointer" title="Move Down">
                                            <i class="fas fa-chevron-down text-[10px]"></i>
                                        </button>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-mono text-[11px] font-bold text-[#051c45] truncate" x-text="field.name"></span>
                                            <template x-if="['name', 'phone'].includes(field.name)">
                                                <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 text-[9px] font-bold">Core</span>
                                            </template>
                                        </div>
                                        <span class="text-[10px] text-slate-400 block" x-text="field.is_system ? 'System Field' : 'Custom Question'"></span>
                                    </div>
                                </div>

                                <!-- Question Display Label (4 Cols) -->
                                <div class="sm:col-span-4">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">Question Label</label>
                                    <input type="text" 
                                           x-model="field.label" 
                                           placeholder="Label shown to student"
                                           class="w-full rounded-xl border-slate-200 bg-slate-50/70 focus:bg-white text-xs font-semibold text-slate-800 py-1.5 px-2.5 focus:border-[#051c45] focus:ring-[#051c45]/20">
                                </div>

                                <!-- Field Input Type (3 Cols) -->
                                <div class="sm:col-span-3">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">Input Type</label>
                                    <select x-model="field.type" 
                                            @change="onFieldTypeChange(field)"
                                            :disabled="['name', 'phone', 'email', 'training_mode'].includes(field.name)"
                                            class="w-full rounded-xl border-slate-200 bg-slate-50/70 focus:bg-white text-xs font-semibold text-slate-700 py-1.5 pl-2.5 pr-7 focus:border-[#051c45] focus:ring-[#051c45]/20 disabled:opacity-60">
                                        <option value="text">Short Text</option>
                                        <option value="select">Dropdown Select</option>
                                        <option value="radio">Choice Pills (1-Tap)</option>
                                        <option value="textarea">Paragraph Textarea</option>
                                        <option value="tel">Mobile / Phone</option>
                                        <option value="email">Email Address</option>
                                        <option value="number">Number</option>
                                        <option value="date">Date Picker</option>
                                        <option value="url">Website / LinkedIn URL</option>
                                        <option value="checkbox">Checkbox Consent</option>
                                    </select>
                                </div>

                                <!-- Toggles & Delete (2 Cols) -->
                                <div class="sm:col-span-2 flex items-center justify-end gap-1.5">
                                    <!-- Required / Optional Pill -->
                                    <template x-if="['name', 'phone'].includes(field.name)">
                                        <span class="px-2 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-black uppercase">Req *</span>
                                    </template>
                                    <template x-if="!['name', 'phone'].includes(field.name)">
                                        <button type="button" 
                                                @click="toggleRequired(idx)"
                                                class="px-2 py-1 rounded-lg text-[10px] font-black uppercase transition-all cursor-pointer"
                                                :class="field.is_required ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-slate-100 text-slate-500 border border-slate-200 hover:bg-slate-200'"
                                                title="Toggle Mandatory vs Optional">
                                            <span x-text="field.is_required ? 'Req *' : 'Opt'"></span>
                                        </button>
                                    </template>

                                    <!-- Enable / Disable Toggle -->
                                    <template x-if="!['name', 'phone'].includes(field.name)">
                                        <button type="button" 
                                                @click="toggleEnabled(idx)"
                                                class="px-2 py-1 rounded-lg text-[10px] font-black uppercase transition-all cursor-pointer"
                                                :class="field.is_enabled !== false ? 'bg-indigo-100 text-indigo-700 border border-indigo-200' : 'bg-rose-100 text-rose-700 border border-rose-200'"
                                                title="Show or Hide this field on the registration form">
                                            <i class="fas" :class="field.is_enabled !== false ? 'fa-eye' : 'fa-eye-slash'"></i>
                                        </button>
                                    </template>

                                    <!-- Remove Custom Field -->
                                    <template x-if="!['name', 'phone', 'email', 'training_mode'].includes(field.name)">
                                        <button type="button" @click="removeField(idx)" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer" title="Delete question">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <!-- Second Row: Placeholder & Dropdown/Choice Options Editor -->
                            <div x-show="field.is_enabled !== false && field.name !== 'training_mode'" class="mt-2.5 pt-2.5 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-12 gap-2.5 items-center">
                                <div :class="['select', 'radio'].includes(field.type) ? 'sm:col-span-5' : 'sm:col-span-12'">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase whitespace-nowrap">Placeholder:</span>
                                        <input type="text"
                                               x-model="field.placeholder"
                                               placeholder="Helper text inside input (e.g. Square Group)"
                                               class="w-full rounded-lg border-slate-200 bg-slate-50/60 focus:bg-white text-[11px] text-slate-600 py-1 px-2.5 focus:border-[#051c45] focus:ring-[#051c45]/15">
                                    </div>
                                </div>

                                <!-- Options Editor when type is Select or Radio -->
                                <template x-if="['select', 'radio'].includes(field.type)">
                                    <div class="sm:col-span-7">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-bold text-indigo-600 uppercase whitespace-nowrap">
                                                <i class="fas fa-list-ul mr-0.5"></i> Choices (comma-separated):
                                            </span>
                                            <input type="text"
                                                   :value="formatOptionsInput(field)"
                                                   @input="updateFieldOptions(field, $event.target.value)"
                                                   placeholder="Option 1, Option 2, Option 3"
                                                   class="w-full rounded-lg border-indigo-200 bg-indigo-50/40 focus:bg-white text-[11px] font-semibold text-indigo-950 py-1 px-2.5 focus:border-indigo-500 focus:ring-indigo-500/20">
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Create Custom Question Builder Box -->
                <div class="p-4 sm:p-5 bg-gradient-to-br from-slate-50 to-indigo-50/30 rounded-2xl border border-indigo-100 shadow-2xs space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-black text-[#051c45] uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fas fa-plus-square text-indigo-600"></i>
                            <span>Create Your Own Custom Question</span>
                        </div>
                        <span class="text-[10px] text-slate-400 font-mono" x-show="newField.label" x-text="'ID: ' + autoSlug(newField.name || newField.label)"></span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                        <div class="sm:col-span-5">
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Question Label <span class="text-[#c30102]">*</span></label>
                            <input type="text" 
                                   x-model="newField.label" 
                                   @input="if (!newField.customKeyEdited) newField.name = autoSlug(newField.label)"
                                   placeholder="e.g. Years of Banking Experience" 
                                   class="w-full rounded-xl border-slate-300 bg-white text-xs font-medium py-2 px-3 focus:border-[#051c45] focus:ring-[#051c45]/20">
                        </div>

                        <div class="sm:col-span-3">
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Question Type</label>
                            <select x-model="newField.type" class="w-full rounded-xl border-slate-300 bg-white text-xs font-semibold py-2 pl-3 pr-7 focus:border-[#051c45] focus:ring-[#051c45]/20">
                                <option value="text">Short Text</option>
                                <option value="select">Dropdown Select</option>
                                <option value="radio">Choice Pills (1-Tap)</option>
                                <option value="textarea">Multi-line Textarea</option>
                                <option value="tel">Mobile / Phone</option>
                                <option value="number">Number</option>
                                <option value="date">Date Picker</option>
                                <option value="url">Website / URL</option>
                                <option value="checkbox">Checkbox Consent</option>
                            </select>
                        </div>

                        <div class="sm:col-span-2 pb-2">
                            <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700">
                                <input type="checkbox" x-model="newField.is_required" class="rounded text-[#051c45] focus:ring-[#051c45]">
                                <span>Mandatory *</span>
                            </label>
                        </div>

                        <div class="sm:col-span-2">
                            <button type="button" @click="addCustomField()" class="w-full py-2 px-3 bg-[#051c45] hover:bg-[#0a2540] text-white rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-xs cursor-pointer flex items-center justify-center gap-1">
                                <i class="fas fa-plus text-[10px]"></i>
                                <span>Add</span>
                            </button>
                        </div>
                    </div>

                    <!-- Extra row for Dropdown/Radio choices or Placeholder when creating a new field -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 pt-1">
                        <div :class="['select', 'radio'].includes(newField.type) ? 'sm:col-span-5' : 'sm:col-span-12'">
                            <input type="text" x-model="newField.placeholder" placeholder="Optional placeholder / hint text (e.g. Enter your current department)" class="w-full rounded-xl border-slate-200 bg-white/80 text-xs py-1.5 px-3 focus:border-[#051c45]">
                        </div>
                        <template x-if="['select', 'radio'].includes(newField.type)">
                            <div class="sm:col-span-7">
                                <input type="text" x-model="newField.optionsText" placeholder="Enter choices separated by comma (e.g. Beginner, Intermediate, Advanced)" class="w-full rounded-xl border-indigo-300 bg-indigo-50/50 focus:bg-white text-xs font-semibold text-indigo-950 py-1.5 px-3 focus:border-indigo-600">
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN (5 Cols): Always-On Live Interactive Student Drawer Simulator -->
            <div class="xl:col-span-5 xl:sticky xl:top-6">
                <div class="flex items-center justify-between mb-2.5">
                    <span class="text-xs font-black uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Live Interactive Student Drawer Preview</span>
                    </span>
                    <div class="inline-flex rounded-lg bg-slate-200/80 p-0.5 text-[10px] font-bold">
                        <button type="button" @click="previewAsLoggedIn = false" :class="!previewAsLoggedIn ? 'bg-white text-[#051c45] shadow-2xs' : 'text-slate-600'" class="px-2 py-0.5 rounded-md transition-all cursor-pointer">Guest View</button>
                        <button type="button" @click="previewAsLoggedIn = true" :class="previewAsLoggedIn ? 'bg-white text-[#051c45] shadow-2xs' : 'text-slate-600'" class="px-2 py-0.5 rounded-md transition-all cursor-pointer">Logged-In View</button>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-xl border border-slate-200/90 overflow-hidden">
                    <!-- Simulated Drawer Header -->
                    <div class="p-5 bg-gradient-to-br from-[#020b18] via-[#051c45] to-[#0a2a66] text-white">
                        <div class="flex items-center gap-2 mb-1.5">
                            @if(!empty($course->batch) && $course->show_batch)
                                <span class="px-2 py-0.5 rounded bg-[#c30102] text-white text-[9px] font-black uppercase">
                                    {{ $course->batch }}
                                </span>
                            @endif
                            <span class="text-[11px] text-[#10b981] font-bold flex items-center gap-1">
                                <i class="fas fa-bolt text-xs"></i> 30-Second Express Enroll
                            </span>
                        </div>
                        <h3 class="text-sm font-extrabold text-white leading-snug">{{ $course->title }}</h3>
                        <div class="mt-3 pt-2.5 border-t border-white/10 flex justify-between items-center text-xs">
                            <span class="text-slate-300">Total Investment:</span>
                            <span class="font-black text-base text-white">
                                {{ (float) $course->price > 0 ? '৳' . number_format($course->price, 0) : 'Free Access (৳0)' }}
                            </span>
                        </div>
                    </div>

                    <!-- Simulated Interactive Form Body -->
                    <div class="p-5 space-y-3.5 bg-white max-h-[540px] overflow-y-auto">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                            <span class="text-[11px] font-black text-[#051c45] uppercase tracking-wider">1. Student Information</span>
                            <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">Interactive Test Mode</span>
                        </div>

                        <!-- Logged-In Banner Simulation -->
                        <template x-if="previewAsLoggedIn">
                            <div class="p-3 rounded-xl bg-emerald-50/70 border border-emerald-200/80 flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-[#051c45] text-white font-black text-xs flex items-center justify-center">S</div>
                                    <div>
                                        <div class="text-xs font-extrabold text-[#0f131f]">Enrolling as Logged-In Student</div>
                                        <div class="text-[10px] text-slate-500">Name & Email auto-filled from profile</div>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[9px] font-black uppercase">Verified</span>
                            </div>
                        </template>

                        <!-- Guest Name, Phone, Email Simulation -->
                        <template x-if="!previewAsLoggedIn">
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">
                                        <span x-text="getFieldProp('name', 'label', 'Full Name')"></span>
                                        <span class="text-[#c30102]">*</span>
                                    </label>
                                    <input type="text" :placeholder="getFieldProp('name', 'placeholder', 'e.g. Tanvir Ahmed')" class="w-full bg-[#f8fafc] border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700">
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                    <div :class="isEmailEnabled ? '' : 'sm:col-span-2'">
                                        <label class="block text-xs font-bold text-slate-700 mb-1">
                                            <span x-text="getFieldProp('phone', 'label', 'Mobile (11 Digits)')"></span>
                                            <span class="text-[#c30102]">*</span>
                                        </label>
                                        <input type="tel" :placeholder="getFieldProp('phone', 'placeholder', '017xxxxxxxx')" class="w-full bg-[#f8fafc] border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700">
                                    </div>
                                    <template x-if="isEmailEnabled">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                                <span x-text="getFieldProp('email', 'label', 'Email Address')"></span>
                                                <span x-show="isEmailRequired" class="text-[#c30102] font-bold">*</span>
                                                <span x-show="!isEmailRequired" class="text-slate-400 font-normal text-[10px]">(Optional)</span>
                                            </label>
                                            <input type="email" :placeholder="getFieldProp('email', 'placeholder', 'name@company.com')" class="w-full bg-[#f8fafc] border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700">
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- Dynamic Custom Fields Simulation (Select, Radio Pills, Textarea, Checkbox, Text) -->
                        <template x-for="field in fields.filter(f => !['name', 'phone', 'email', 'training_mode'].includes(f.name) && (f.is_enabled !== false))" :key="'sim_' + field.name">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                    <span x-text="field.label || field.name"></span>
                                    <span x-show="field.is_required" class="text-[#c30102] font-bold">*</span>
                                    <span x-show="!field.is_required" class="text-slate-400 font-normal text-[10px]">(Optional)</span>
                                </label>

                                <template x-if="field.type === 'textarea'">
                                    <textarea rows="2" :placeholder="field.placeholder || ('Enter ' + field.label)" class="w-full bg-[#f8fafc] border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700"></textarea>
                                </template>

                                <template x-if="field.type === 'select'">
                                    <select class="w-full bg-[#f8fafc] border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-700">
                                        <option value="" x-text="field.placeholder || ('Select ' + field.label)"></option>
                                        <template x-for="opt in getOptionsList(field)" :key="opt">
                                            <option :value="opt" x-text="opt"></option>
                                        </template>
                                    </select>
                                </template>

                                <template x-if="field.type === 'radio'">
                                    <div class="grid grid-cols-2 gap-1.5">
                                        <template x-for="opt in getOptionsList(field)" :key="opt">
                                            <button type="button"
                                                    @click="simValues[field.name] = opt"
                                                    class="px-2.5 py-2 rounded-xl border text-left text-[11px] font-bold transition-all flex items-center justify-between gap-1 cursor-pointer"
                                                    :class="simValues[field.name] === opt ? 'border-[#051c45] bg-[#051c45] text-white' : 'border-slate-200 bg-[#f8fafc] text-slate-700 hover:bg-white'">
                                                <span class="truncate" x-text="opt"></span>
                                                <i class="fas fa-check-circle text-[10px]" :class="simValues[field.name] === opt ? 'text-[#10b981]' : 'text-transparent'"></i>
                                            </button>
                                        </template>
                                    </div>
                                </template>

                                <template x-if="field.type === 'checkbox'">
                                    <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-[#f8fafc] text-xs font-medium text-slate-700 cursor-pointer">
                                        <input type="checkbox" class="rounded text-[#051c45]">
                                        <span x-text="field.placeholder || field.label"></span>
                                    </label>
                                </template>

                                <template x-if="!['textarea', 'select', 'radio', 'checkbox'].includes(field.type)">
                                    <input :type="field.type || 'text'" :placeholder="field.placeholder || ('Enter ' + field.label)" class="w-full bg-[#f8fafc] border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700">
                                </template>
                            </div>
                        </template>

                        <!-- Attendance Mode Simulation -->
                        <div x-show="isTrainingModeEnabled">
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-bold text-slate-700" x-text="getFieldProp('training_mode', 'label', 'Preferred Attendance Mode')"></label>
                                <template x-if="isOnlineOnly">
                                    <span class="text-[9px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded">Online Only</span>
                                </template>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div class="p-2.5 rounded-xl border border-[#051c45] bg-[#051c45]/5 text-[#051c45] font-bold text-center flex items-center justify-center gap-1.5">
                                    <i class="fas fa-video text-[10px]"></i>
                                    <span>Live Online Zoom</span>
                                </div>
                                <div class="p-2.5 rounded-xl border border-slate-200 text-slate-400 text-center flex items-center justify-center gap-1.5"
                                     :class="isOnlineOnly ? 'opacity-50 line-through bg-slate-100' : ''">
                                    <i class="fas fa-building text-[10px]"></i>
                                    <span>Executive Campus</span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="button" @click="saveForm(false)" class="w-full py-3 bg-gradient-to-r from-[#c30102] to-[#a50102] text-white font-black uppercase tracking-wider rounded-xl text-xs text-center shadow-md cursor-pointer">
                                {{ (float) $course->price > 0 ? '→ Complete Express Enrollment' : '→ Enroll in Free Course' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Hidden form for standard fallbacks -->
    <form id="saveCourseFormBuilderForm" method="POST" action="{{ route('admin.courses.update_enrollment_form', $course) }}" class="hidden">
        @csrf
        <input type="hidden" name="enrollment_fields_json" id="enrollment_fields_json_input">
    </form>
</div>

<script>
function courseFormBuilder(config) {
    return {
        fields: JSON.parse(JSON.stringify(config.initialFields || [])),
        templates: config.templates || {},
        quickQuestions: config.quickQuestions || [],
        isOnlineOnly: !!config.isOnlineOnly,
        isCampusOnly: !!config.isCampusOnly,
        currentTemplateKey: '',
        isCollapsed: false,
        previewAsLoggedIn: false,
        simValues: {},
        isSaving: false,
        saveMessage: '',
        saveError: '',
        newField: {
            name: '',
            label: '',
            type: 'text',
            placeholder: '',
            optionsText: '',
            is_required: false,
            customKeyEdited: false
        },

        init() {
            this.detectCurrentTemplate();
        },

        autoSlug(text) {
            return (text || '')
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9]+/g, '_')
                .replace(/^_+|_+$/g, '')
                .substring(0, 32);
        },

        get isEmailEnabled() {
            const f = this.fields.find(field => field.name === 'email');
            return f ? f.is_enabled !== false : true;
        },

        get isEmailRequired() {
            const f = this.fields.find(field => field.name === 'email');
            return f ? (f.is_enabled !== false && !!f.is_required) : true;
        },

        get isTrainingModeEnabled() {
            const f = this.fields.find(field => field.name === 'training_mode');
            return f ? f.is_enabled !== false : true;
        },

        get activeFieldsCount() {
            return this.fields.filter(f => f.is_enabled !== false).length;
        },

        get requiredFieldsCount() {
            return this.fields.filter(f => f.is_enabled !== false && !!f.is_required).length;
        },

        getFieldProp(name, prop, fallback) {
            const f = this.fields.find(x => x.name === name);
            return (f && f[prop]) ? f[prop] : fallback;
        },

        hasActiveField(name) {
            const f = this.fields.find(x => x.name === name);
            return !!(f && f.is_enabled !== false);
        },

        getOptionsList(field) {
            if (!field || !field.options) return ['Option 1', 'Option 2'];
            if (Array.isArray(field.options)) return field.options;
            if (typeof field.options === 'string') {
                return field.options.split(/[,;\n]+/).map(s => s.trim()).filter(Boolean);
            }
            if (typeof field.options === 'object') {
                return Object.values(field.options);
            }
            return [];
        },

        formatOptionsInput(field) {
            if (!field || !field.options) return '';
            if (Array.isArray(field.options)) return field.options.join(', ');
            if (typeof field.options === 'string') return field.options;
            if (typeof field.options === 'object') return Object.values(field.options).join(', ');
            return '';
        },

        updateFieldOptions(field, rawText) {
            field.options = (rawText || '')
                .split(',')
                .map(s => s.trim())
                .filter(Boolean);
        },

        onFieldTypeChange(field) {
            if (['select', 'radio'].includes(field.type) && (!field.options || field.options.length === 0)) {
                field.options = ['Option 1', 'Option 2', 'Option 3'];
            }
            this.detectCurrentTemplate();
        },

        toggleEmailQuick() {
            let f = this.fields.find(x => x.name === 'email');
            if (!f) {
                this.fields.splice(2, 0, {
                    name: 'email',
                    label: 'Email Address',
                    type: 'email',
                    is_required: true,
                    is_enabled: true,
                    is_system: true,
                    placeholder: 'name@company.com'
                });
            } else {
                f.is_enabled = !(f.is_enabled !== false);
            }
            this.detectCurrentTemplate();
        },

        toggleQuickQuestion(q) {
            const existingIdx = this.fields.findIndex(f => f.name === q.name);
            if (existingIdx !== -1) {
                if (this.fields[existingIdx].is_enabled === false) {
                    this.fields[existingIdx].is_enabled = true;
                } else {
                    this.fields.splice(existingIdx, 1);
                }
            } else {
                // Insert right before training_mode if present, otherwise at end
                const tmIdx = this.fields.findIndex(f => f.name === 'training_mode');
                const newObj = {
                    name: q.name,
                    label: q.label,
                    type: q.type || 'text',
                    is_required: !!q.is_required,
                    is_enabled: true,
                    is_system: false,
                    placeholder: q.placeholder || '',
                    options: q.options ? JSON.parse(JSON.stringify(q.options)) : null
                };
                if (tmIdx !== -1) {
                    this.fields.splice(tmIdx, 0, newObj);
                } else {
                    this.fields.push(newObj);
                }
            }
            this.detectCurrentTemplate();
        },

        detectCurrentTemplate() {
            for (const [key, tpl] of Object.entries(this.templates)) {
                if (tpl.fields.length === this.fields.length) {
                    const match = tpl.fields.every((tf, idx) => {
                        const cur = this.fields[idx];
                        return cur && cur.name === tf.name && (!!cur.is_required === !!tf.is_required) && ((cur.is_enabled !== false) === (tf.is_enabled !== false));
                    });
                    if (match) {
                        this.currentTemplateKey = key;
                        return;
                    }
                }
            }
            this.currentTemplateKey = 'custom';
        },

        applyTemplate(key, autoSave = false) {
            if (!this.templates[key]) return;
            this.fields = JSON.parse(JSON.stringify(this.templates[key].fields));
            this.currentTemplateKey = key;
            if (autoSave) {
                this.saveForm(false);
            } else {
                this.saveMessage = 'Loaded "' + this.templates[key].name + '" preset into the studio. Click "Save Form Setup" to publish.';
                setTimeout(() => this.saveMessage = '', 4500);
            }
        },

        toggleEnabled(idx) {
            const f = this.fields[idx];
            if (f.name === 'name' || f.name === 'phone') {
                return;
            }
            f.is_enabled = !(f.is_enabled !== false);
            this.detectCurrentTemplate();
        },

        toggleRequired(idx) {
            const f = this.fields[idx];
            if (f.name === 'name' || f.name === 'phone') {
                return;
            }
            f.is_required = !f.is_required;
            this.detectCurrentTemplate();
        },

        moveField(idx, dir) {
            const targetIdx = idx + dir;
            if (targetIdx < 0 || targetIdx >= this.fields.length) return;
            const temp = this.fields[idx];
            this.fields[idx] = this.fields[targetIdx];
            this.fields[targetIdx] = temp;
            this.detectCurrentTemplate();
        },

        removeField(idx) {
            const f = this.fields[idx];
            if (['name', 'phone', 'email', 'training_mode'].includes(f.name)) {
                return;
            }
            this.fields.splice(idx, 1);
            this.detectCurrentTemplate();
        },

        addCustomField() {
            const rawLabel = (this.newField.label || '').trim();
            const rawName = (this.newField.name || this.autoSlug(rawLabel)).trim();
            if (!rawLabel && !rawName) {
                this.saveError = 'Please enter a Question Label (e.g. Years of Experience).';
                return;
            }
            const cleanName = this.autoSlug(rawName || rawLabel);
            if (this.fields.some(f => f.name === cleanName)) {
                this.saveError = 'A question with ID "' + cleanName + '" already exists in this form.';
                return;
            }
            const label = rawLabel || cleanName.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            let options = null;
            if (['select', 'radio'].includes(this.newField.type)) {
                options = (this.newField.optionsText || 'Option 1, Option 2')
                    .split(',')
                    .map(s => s.trim())
                    .filter(Boolean);
            }

            const newObj = {
                name: cleanName,
                label: label,
                type: this.newField.type || 'text',
                is_required: !!this.newField.is_required,
                is_enabled: true,
                is_system: false,
                placeholder: (this.newField.placeholder || '').trim() || ('Enter ' + label),
                options: options
            };

            const tmIdx = this.fields.findIndex(f => f.name === 'training_mode');
            if (tmIdx !== -1) {
                this.fields.splice(tmIdx, 0, newObj);
            } else {
                this.fields.push(newObj);
            }

            this.newField = { name: '', label: '', type: 'text', placeholder: '', optionsText: '', is_required: false, customKeyEdited: false };
            this.saveError = '';
            this.detectCurrentTemplate();
        },

        async saveForm(applyToAll = false) {
            if (applyToAll && !confirm('Apply this registration form configuration to ALL courses in your catalog?')) {
                return;
            }
            this.isSaving = true;
            this.saveMessage = '';
            this.saveError = '';

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const response = await fetch('{{ route("admin.courses.update_enrollment_form", $course) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        enrollment_fields: this.fields,
                        apply_to_all_courses: applyToAll
                    })
                });

                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Failed to save form settings.');
                }

                this.saveMessage = data.message || 'Registration form settings saved successfully!';
                setTimeout(() => this.saveMessage = '', 5000);
            } catch (err) {
                this.saveError = err.message || 'An error occurred while saving form settings.';
            } finally {
                this.isSaving = false;
            }
        }
    };
}
</script>

<div class="space-y-6" x-show="wsTab === 'all' || wsTab === 'curriculum'">
    @forelse($course->sections as $section)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-gray-900 text-lg flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    {{ $section->title }}
                </h3>
                <div class="flex items-center gap-3">
                    <button onclick='openEditSectionModal({{ $section->id }}, @json($section->title), {{ $section->order }})' class="text-sm font-medium text-gray-500 hover:text-gray-900">Edit</button>
                    <form method="POST" action="{{ route('admin.courses.sections.destroy', [$course, $section]) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete this section?')" class="text-sm font-medium text-red-500 hover:text-red-700">Delete</button>
                    </form>
                    <button onclick="openLessonModal({{ $section->id }})" class="text-sm font-medium text-primary hover:underline ml-2">+ Add Lesson</button>
                </div>
            </div>
            
            <div class="divide-y divide-gray-100">
                @forelse($section->lessons as $lesson)
                    <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="font-medium text-gray-900">{{ $lesson->title }}</span>
                        </div>
                        <div class="text-sm text-gray-500 truncate max-w-xs flex items-center gap-3">
                            {{ $lesson->video_url ? 'Video attached' : 'Text lesson' }}
                            <button onclick='openEditLessonModal({{ $section->id }}, {{ $lesson->id }}, @json($lesson->title), @json($lesson->video_url ?? ""), @json($lesson->content ?? ""), {{ $lesson->order }})' class="text-xs font-medium text-gray-500 hover:text-gray-900 ml-2">Edit</button>
                            <form method="POST" action="{{ route('admin.courses.lessons.destroy', [$course, $section, $lesson]) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Delete this lesson?')" class="text-xs font-medium text-red-500 hover:text-red-700">Delete</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-4 text-sm text-gray-500 italic">No lessons in this section.</div>
                @endforelse
            </div>
        </div>
    @empty
        <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            <h3 class="text-lg font-bold text-gray-900 mb-1">No sections yet</h3>
            <p class="text-gray-500 text-sm">Add a section to start building your course outline.</p>
        </div>
    @endforelse

    <!-- Quizzes List -->
    <h2 class="text-xl font-bold mt-8 mb-4 border-t pt-8 border-gray-200">Course Quizzes</h2>
    @forelse($course->quizzes as $quiz)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-4">
            <div class="px-6 py-4 bg-slate-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-900 text-lg flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#051c45]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ $quiz->title }}
                </h3>
                <div class="flex items-center gap-3">
                    @if($quiz->is_published)
                        <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded text-xs font-medium">Published</span>
                    @else
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-medium">Draft</span>
                    @endif
                    <a href="{{ route('admin.quizzes.show', [$course, $quiz]) }}" class="text-sm font-semibold text-[#051c45] hover:underline">Manage Questions →</a>
                    <form method="POST" action="{{ route('admin.courses.quizzes.destroy', [$course, $quiz]) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete this quiz?')" class="text-sm font-medium text-red-500 hover:text-red-700 ml-2">Delete</button>
                    </form>
                </div>
            </div>
            @if($quiz->description)
                <div class="px-6 py-3 text-sm text-gray-600 border-t border-slate-100 bg-white">
                    {{ $quiz->description }}
                </div>
            @endif
        </div>
    @empty
        <div class="bg-white rounded-2xl border border-gray-100 p-8 text-center text-gray-500 text-sm">
            No quizzes added yet.
        </div>
    @endforelse
</div>

<!-- Course Materials -->
<div class="mb-8" x-show="wsTab === 'all' || wsTab === 'curriculum'">
    <div class="flex justify-between items-center mb-4 border-t pt-8 border-gray-200">
        <h2 class="text-xl font-bold text-gray-900">Course Materials</h2>
        <button onclick="document.getElementById('addMaterialModal').classList.remove('hidden')" class="px-4 py-2 bg-slate-100 text-[#051c45] rounded-lg font-medium hover:bg-slate-200 transition-colors">
            + Add Material
        </button>
    </div>

    <div class="space-y-4">
        @forelse($course->materials as $material)
            <div class="bg-white rounded-2xl border border-gray-100 p-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-slate-100 text-[#051c45] flex items-center justify-center shrink-0">
                        @if($material->type === 'pdf')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        @elseif($material->type === 'zip')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                        @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                        @endif
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900">{{ $material->title }}</h4>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">{{ $material->type }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @if($material->type === 'link')
                        <a href="{{ $material->external_link }}" target="_blank" class="text-[#051c45] hover:underline text-sm font-semibold">View</a>
                    @else
                        <a href="{{ storage_asset($material->file_path) }}" target="_blank" class="text-[#051c45] hover:underline text-sm font-semibold">Download</a>
                    @endif
                    <form method="POST" action="{{ route('admin.courses.materials.destroy', [$course, $material]) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete this material?')" class="text-red-500 hover:text-red-700 text-sm font-medium">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-gray-100 p-8 text-center text-gray-500 text-sm">
                No materials attached to this course.
            </div>
        @endforelse
    </div>
</div>

<!-- Enrolled Students -->
<div class="mb-8" x-show="wsTab === 'all' || wsTab === 'students'">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-gray-900">Enrolled Students</h2>
        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">{{ $course->students->count() }} Total</span>
    </div>
    
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-500 text-sm uppercase tracking-wide border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 font-medium">Student</th>
                        <th class="px-6 py-4 font-medium">Email Status</th>
                        <th class="px-6 py-4 font-medium">Enrolled On</th>
                        <th class="px-6 py-4 font-medium text-right">Certificate Status & Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($course->students as $student)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if($student->hasCustomProfilePicture())
                                        <img src="{{ $student->profile_picture_url }}" alt="{{ $student->name }}" class="w-8 h-8 rounded-full object-cover mr-3 shrink-0 border border-slate-200" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=051c45&color=fff';">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold mr-3 shrink-0">
                                            {{ substr($student->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $student->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $student->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($student->email_verified_at)
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-medium">Verified</span>
                                @else
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs font-medium">Unverified</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $student->pivot->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                @php
                                    $studentCert = $course->certificates->firstWhere('user_id', $student->id);
                                @endphp
                                @if($studentCert)
                                    <div class="inline-flex items-center gap-2 justify-end">
                                        <a href="{{ route('courses.certificate', $course) }}?as_user={{ $student->id }}" target="_blank" class="px-2 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded font-mono text-[11px] font-bold transition-colors flex items-center gap-1" title="Click to view & print certificate">
                                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            {{ $studentCert->certificate_number }}
                                        </a>
                                        <form method="POST" action="{{ route('admin.courses.certificates.revoke', [$course, $student]) }}" class="inline" onsubmit="return confirm('Revoke certificate for {{ $student->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-red-500 hover:text-red-700 hover:bg-red-50 rounded text-xs transition-colors" title="Revoke Certificate">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <div class="inline-flex items-center gap-2 justify-end">
                                        @if($course->certificate_enabled)
                                            <span class="text-[11px] text-emerald-600 font-semibold bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100">Cohort Unlocked</span>
                                        @else
                                            <span class="text-[11px] text-slate-400 bg-slate-50 px-2 py-0.5 rounded border border-slate-100">Pending Release</span>
                                        @endif
                                        <form method="POST" action="{{ route('admin.courses.certificates.issue', [$course, $student]) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-lg text-xs font-bold transition-all shadow-sm flex items-center gap-1">
                                                <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                Issue Cert
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                No students enrolled yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Course Payments & Verifications -->
<div class="mb-8" x-show="wsTab === 'all' || wsTab === 'payments'">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-gray-900">Payments & Verifications</h2>
        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">{{ $course->payments->count() }} Payments</span>
    </div>
    
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-500 text-sm uppercase tracking-wide border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 font-medium">Student</th>
                        <th class="px-6 py-4 font-medium">Transaction Details</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($course->payments as $payment)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $payment->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $payment->user->email }}</div>
                                @if(!empty($payment->form_data) && is_array($payment->form_data))
                                    <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                        @foreach($payment->form_data as $fieldKey => $fieldVal)
                                            @if(!in_array($fieldKey, ['name', 'phone', 'email', 'training_mode']) && !empty($fieldVal))
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-slate-100 border border-slate-200 text-slate-700 text-[10px] font-medium" title="{{ ucfirst(str_replace('_', ' ', $fieldKey)) }}">
                                                    <span class="text-gray-400 capitalize">{{ str_replace('_', ' ', $fieldKey) }}:</span>
                                                    <span class="font-bold text-gray-900">{{ $fieldVal }}</span>
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                @elseif(!empty($payment->notes))
                                    <div class="text-[10px] text-gray-500 mt-1 italic">{{ $payment->notes }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">৳{{ $payment->amount }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    <span class="font-semibold">{{ $payment->paymentMethod->provider_name ?? 'Unknown' }}</span> • 
                                    Trx: <span class="font-mono text-gray-700">{{ $payment->transaction_id }}</span>
                                </div>
                                <div class="text-xs text-gray-500 mt-0.5">Sender: {{ $payment->sender_number }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('admin.payments.updateStatus', $payment) }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    <select name="status" class="text-sm rounded border-gray-300 py-1 pl-2 pr-8 focus:ring-primary focus:border-primary">
                                        <option value="pending" {{ $payment->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="processing" {{ $payment->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                        <option value="verified" {{ $payment->status == 'verified' ? 'selected' : '' }}>Verified (Enrolled)</option>
                                        <option value="rejected" {{ $payment->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                    <button type="submit" class="px-2 py-1 bg-gray-100 text-gray-600 rounded hover:bg-gray-200 text-xs font-medium">Update</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                No payments recorded for this course.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Section Modal -->
<div id="sectionModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">New Section</h3>
            <button onclick="document.getElementById('sectionModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.courses.sections.store', $course) }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Order</label>
                    <input type="number" name="order" value="0" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-primary text-white rounded-lg font-medium hover:opacity-90">Create Section</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Section Modal -->
<div id="editSectionModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Edit Section</h3>
            <button onclick="document.getElementById('editSectionModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form method="POST" id="editSectionForm" action="">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" id="editSectionTitle" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Order</label>
                    <input type="number" name="order" id="editSectionOrder" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-primary text-white rounded-lg font-medium hover:opacity-90">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Lesson Modal -->
<div id="lessonModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-lg shadow-xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">New Lesson</h3>
            <button onclick="document.getElementById('lessonModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form method="POST" id="lessonForm" action="">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lesson Title</label>
                    <input type="text" name="title" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Video Embed URL (Optional)</label>
                    <input type="url" name="video_url" placeholder="https://www.youtube.com/embed/..." class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                    <p class="text-xs text-gray-500 mt-1">Use the embed URL for YouTube/Vimeo.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Text Content (Optional)</label>
                    <textarea name="content" rows="4" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Order</label>
                    <input type="number" name="order" value="0" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-primary text-white rounded-lg font-medium hover:opacity-90">Create Lesson</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Lesson Modal -->
<div id="editLessonModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-lg shadow-xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Edit Lesson</h3>
            <button onclick="document.getElementById('editLessonModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form method="POST" id="editLessonForm" action="">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" id="editLessonTitle" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Video URL (Optional)</label>
                    <input type="url" name="video_url" id="editLessonVideoUrl" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Text Content (Optional)</label>
                    <textarea name="content" id="editLessonContent" rows="4" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Order</label>
                    <input type="number" name="order" id="editLessonOrder" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-primary text-white rounded-lg font-medium hover:opacity-90">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Quiz Modal -->
<div id="quizModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">New Quiz</h3>
            <button onclick="document.getElementById('quizModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.courses.quizzes.store', $course) }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                    <textarea name="description" rows="3" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"></textarea>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_published" id="quiz_is_published" value="1" class="rounded text-primary focus:ring-primary mr-2">
                    <label for="quiz_is_published" class="text-sm font-medium text-gray-700">Publish immediately</label>
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-[#051c45] text-white rounded-lg font-medium hover:opacity-90">Create Quiz</button>
            </div>
        </form>
    </div>
</div>

<!-- Material Modal -->
<div id="addMaterialModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Add Course Material</h3>
            <button onclick="document.getElementById('addMaterialModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.courses.materials.store', $course) }}" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="type" id="materialType" onchange="toggleMaterialFields()" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                        <option value="link">External Link</option>
                        <option value="pdf">PDF Document</option>
                        <option value="zip">ZIP File</option>
                    </select>
                </div>
                <div id="linkField">
                    <label class="block text-sm font-medium text-gray-700 mb-1">External Link URL</label>
                    <input type="url" name="external_link" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                </div>
                <div id="fileField" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">File Upload</label>
                    <input type="file" name="file_path" accept=".pdf,.zip" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-slate-100 file:text-[#051c45] hover:file:bg-slate-200">
                    <p class="text-xs text-gray-500 mt-1">Max 10MB.</p>
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-[#051c45] text-white rounded-lg font-medium hover:opacity-90 mt-2">Add Material</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleMaterialFields() {
        const type = document.getElementById('materialType').value;
        const linkField = document.getElementById('linkField');
        const fileField = document.getElementById('fileField');
        
        if (type === 'link') {
            linkField.classList.remove('hidden');
            fileField.classList.add('hidden');
        } else {
            linkField.classList.add('hidden');
            fileField.classList.remove('hidden');
        }
    }
    function openLessonModal(sectionId) {
        const form = document.getElementById('lessonForm');
        form.reset();
        // Set the action URL dynamically
        form.action = `/admin/courses/{{ $course->id }}/sections/${sectionId}/lessons`;
        document.getElementById('lessonModal').classList.remove('hidden');
    }

    function openEditSectionModal(sectionId, title, order) {
        document.getElementById('editSectionTitle').value = title;
        document.getElementById('editSectionOrder').value = order;
        document.getElementById('editSectionForm').action = `/admin/courses/{{ $course->id }}/sections/${sectionId}`;
        document.getElementById('editSectionModal').classList.remove('hidden');
    }

    function openEditLessonModal(sectionId, lessonId, title, video_url, content, order) {
        document.getElementById('editLessonTitle').value = title;
        document.getElementById('editLessonVideoUrl').value = video_url;
        document.getElementById('editLessonContent').value = content;
        document.getElementById('editLessonOrder').value = order;
        document.getElementById('editLessonForm').action = `/admin/courses/{{ $course->id }}/sections/${sectionId}/lessons/${lessonId}`;
        document.getElementById('editLessonModal').classList.remove('hidden');
    }
</script>

<!-- Edit Course Modal -->
<div id="editCourseModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50 {{ $errors->any() ? '' : 'hidden' }}">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Course Settings</h3>
            <button onclick="document.getElementById('editCourseModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        @if($errors->any())
            <div class="mb-4 p-3.5 bg-red-50 border border-red-200 text-red-700 rounded-xl text-xs space-y-1">
                <div class="font-bold">Please correct the following errors:</div>
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('admin.courses.update', $course) }}" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4 max-h-[70vh] overflow-y-auto pr-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ $course->title }}" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                    <textarea name="description" rows="3" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">{{ $course->description }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Short Excerpt / Card Snippet (Optional)</label>
                    <input type="text" name="short_description" value="{{ $course->short_description }}" placeholder="Brief 1-2 sentence overview for course card" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Starting Date</label>
                        <input type="date" name="start_date" value="{{ $course->start_date ? $course->start_date->format('Y-m-d') : '' }}" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Price (৳ BDT)</label>
                        <input type="number" step="0.01" name="price" value="{{ $course->price }}" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Duration</label>
                        <input type="text" name="duration" value="{{ $course->duration }}" placeholder="e.g. 8 Days / 10 Hours" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Venue / Format</label>
                        <input type="text" id="edit-course-venue" name="venue" value="{{ $course->venue }}" placeholder="e.g. Online Live (Zoom) / Executive Campus" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                        <div class="flex flex-wrap gap-1.5 mt-1.5">
                            <button type="button" onclick="document.getElementById('edit-course-venue').value='Online Live (Zoom)'" class="px-2 py-0.5 rounded-md bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-[10px] font-bold transition-colors">Online Only (Disables Campus)</button>
                            <button type="button" onclick="document.getElementById('edit-course-venue').value='Executive Campus'" class="px-2 py-0.5 rounded-md bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 text-[10px] font-bold transition-colors">Campus Only</button>
                            <button type="button" onclick="document.getElementById('edit-course-venue').value='Hybrid (Online Zoom & Executive Campus)'" class="px-2 py-0.5 rounded-md bg-sky-50 hover:bg-sky-100 text-sky-700 border border-sky-200 text-[10px] font-bold transition-colors">Hybrid (Both)</button>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Batch / Cohort (Optional)</label>
                        <input type="text" name="batch" value="{{ $course->batch }}" placeholder="e.g. 105th Batch" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary text-sm">
                    </div>
                    <div class="flex items-end pb-1">
                        <input type="hidden" name="show_batch" value="0">
                        <label class="flex items-center gap-2 cursor-pointer p-2.5 rounded-lg border border-gray-200 bg-gray-50/70 hover:bg-gray-100 transition-colors w-full">
                            <input type="checkbox" name="show_batch" value="1" {{ $course->show_batch ? 'checked' : '' }} class="rounded text-primary focus:ring-primary">
                            <span class="text-xs font-semibold text-gray-700">Show Batch Publicly</span>
                        </label>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Level (Optional)</label>
                        <input type="text" name="level" value="{{ $course->level }}" placeholder="e.g. Executive / Certificate" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <input list="category-options-edit" type="text" name="category" value="{{ $course->category }}" placeholder="Choose or type category..." class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                        <datalist id="category-options-edit">
                            @foreach(\App\Models\Category::active()->ordered()->get() as $cat)
                                <option value="{{ $cat->name }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Who Should Attend / Target Audience (Optional)</label>
                    <textarea name="target_audience" rows="2" placeholder="e.g. Commercial Executives, Importers, Exporters, Entrepreneurs..." class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary text-sm">{{ $course->target_audience }}</textarea>
                </div>
                <div class="p-3.5 bg-emerald-50/50 rounded-xl border border-emerald-200/80 space-y-3">
                    <div class="text-[11px] font-black uppercase tracking-wider text-emerald-900 flex items-center gap-1.5">
                        <i class="fab fa-whatsapp text-emerald-600 text-sm"></i>
                        <span>Course Counselor WhatsApp & Phone (Optional Override)</span>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Course Counselor WhatsApp / Phone Number</label>
                        <input type="text" name="counselor_phone" value="{{ $course->counselor_phone }}" placeholder="Leave blank to use Global Hotline ({{ \App\Models\Setting::getVal('contact_hotline', '01917-054664') }})" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Custom WhatsApp Inquiry Message (Optional)</label>
                        <textarea name="whatsapp_message" rows="2" placeholder="Assalamu Alaikum! I would like to inquire/enroll in *{title}* (Fee: {price}). Please share batch details and enrollment procedure: {url}" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary text-xs">{{ $course->whatsapp_message }}</textarea>
                        <p class="text-[10px] text-gray-500 mt-1">Supports placeholders: <code>{title}</code>, <code>{price}</code>, <code>{batch}</code>, <code>{url}</code></p>
                    </div>
                </div>
                @if(auth()->user()->role === 'admin' && isset($instructors))
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Assigned Instructor</label>
                    <select name="instructor_id" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary text-sm">
                        <option value="">-- No Instructor Assigned --</option>
                        @foreach($instructors as $instructor)
                            <option value="{{ $instructor->id }}" {{ $course->instructor_id == $instructor->id ? 'selected' : '' }}>
                                {{ $instructor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-200">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Course Cover Photo (Picture)</label>
                    <div class="flex items-center gap-4">
                        <div class="w-24 h-16 rounded-lg overflow-hidden border border-gray-300 bg-gray-200 shrink-0 shadow-inner flex items-center justify-center">
                            <img id="edit_course_cover_preview" src="{{ $course->cover_image_url }}" alt="Cover" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=800&q=80';">
                        </div>
                        <div class="flex-1">
                            <input type="file" id="course_cover_file_input" name="cover_image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:opacity-90 file:cursor-pointer cursor-pointer border border-gray-300 rounded-lg p-1 bg-white">
                            <input type="hidden" name="cover_image_base64" id="course_cover_image_base64">
                            <p class="text-[10px] text-gray-400 mt-1">PNG, JPG, WebP (16:10 or 16:9 ratio)</p>
                        </div>
                    </div>
                </div>
                <script>
                    document.getElementById('course_cover_file_input')?.addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(evt) {
                                document.getElementById('course_cover_image_base64').value = evt.target.result;
                                const preview = document.getElementById('edit_course_cover_preview');
                                if (preview) preview.src = evt.target.result;
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                </script>
                <div class="p-3.5 bg-emerald-50/80 rounded-xl border border-emerald-200 flex items-center justify-between">
                    <div>
                        <label for="edit_certificate_enabled" class="block text-xs font-bold text-emerald-950">Release Digital Certificates to Students</label>
                        <p class="text-[11px] text-emerald-800 mt-0.5">When checked, enrolled students who completed the course can view and download their official digital certificate.</p>
                    </div>
                    <div class="ml-4">
                        <input type="hidden" name="certificate_enabled" value="0">
                        <input type="checkbox" name="certificate_enabled" id="edit_certificate_enabled" value="1" {{ $course->certificate_enabled ? 'checked' : '' }} class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-gray-300">
                    </div>
                </div>
                <div class="flex items-center pt-2">
                    <input type="hidden" name="is_published" value="0">
                    <input type="checkbox" name="is_published" id="is_published" value="1" {{ $course->is_published ? 'checked' : '' }} class="rounded text-primary focus:ring-primary mr-2">
                    <label for="is_published" class="text-sm font-medium text-gray-700">Publish immediately</label>
                </div>
                <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-100">
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:opacity-90">Save Changes</button>
                    <button type="button" onclick="if(confirm('Are you sure you want to delete this course? This action cannot be undone.')) { document.getElementById('deleteCourseForm').submit(); }" class="text-sm text-red-600 hover:text-red-800 font-medium">Delete Course</button>
                </div>
            </div>
        </form>
        <form id="deleteCourseForm" action="{{ route('admin.courses.destroy', $course) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
</div>

@endsection
