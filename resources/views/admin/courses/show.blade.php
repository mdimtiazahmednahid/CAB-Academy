@extends('layouts.admin')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.courses') }}" class="text-gray-400 hover:text-primary transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h1 class="text-2xl font-bold">{{ $course->title }}</h1>
        @if($course->is_published)
            <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-medium">Published</span>
        @else
            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-medium">Draft</span>
        @endif
        @if($course->certificate_enabled)
            <span class="px-2 py-1 bg-emerald-100 text-emerald-800 rounded text-xs font-medium flex items-center gap-1">
                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Certificates Released
            </span>
        @else
            <span class="px-2 py-1 bg-amber-100 text-amber-800 rounded text-xs font-medium flex items-center gap-1" title="Digital certificates are hidden from students until released">
                <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                Certificates Locked
            </span>
        @endif
    </div>
    <div class="flex gap-2">
        <button onclick="document.getElementById('editCourseModal').classList.remove('hidden')" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-sm font-medium transition-colors flex items-center gap-2 shadow-sm">
            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            <span>Course Settings</span>
        </button>
        <button onclick="document.getElementById('formBuilderCard')?.scrollIntoView({ behavior: 'smooth' })" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg text-sm font-medium transition-all flex items-center gap-2 shadow-sm">
            <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span>Registration Form Builder</span>
        </button>
        <button onclick="document.getElementById('quizModal').classList.remove('hidden')" class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:opacity-90">
            + Add Quiz
        </button>
        <button onclick="document.getElementById('sectionModal').classList.remove('hidden')" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:opacity-90">
            + Add Section
        </button>
    </div>
</div>

@if(session('success'))
    <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">
        {{ session('success') }}
    </div>
@endif

<!-- Course Banner & Cover Photo Spotlight Card -->
<div class="mb-6 bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
    <div class="flex items-center gap-4 w-full sm:w-auto">
        <div class="relative w-24 h-16 sm:w-32 sm:h-20 rounded-xl overflow-hidden border border-gray-200 bg-gray-100 shadow-inner shrink-0">
            <img src="{{ $course->cover_image_url }}" alt="{{ $course->title }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=800&q=80';">
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-[10px] font-black uppercase tracking-wider text-gray-400">Course Picture</span>
                @if($course->cover_image)
                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded text-[10px] font-bold">Custom Photo Active</span>
                @else
                    <span class="px-2 py-0.5 bg-amber-50 text-amber-700 border border-amber-200 rounded text-[10px] font-bold">Default Photo Active</span>
                @endif
            </div>
            <h3 class="text-sm font-bold text-gray-900 mt-1 truncate">{{ $course->title }}</h3>
            <p class="text-xs text-gray-500 mt-0.5">
                Instructor: <span class="font-medium text-gray-700">{{ $course->instructorProfile ? $course->instructorProfile->name : ($course->instructor ? $course->instructor->name : 'Unassigned') }}</span>
                • Fee: <span class="font-medium text-gray-700">৳{{ number_format($course->price, 2) }}</span>
            </p>
        </div>
    </div>
    <button onclick="document.getElementById('editCourseModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-xl text-xs font-bold transition-all w-full sm:w-auto flex items-center justify-center gap-1.5 shrink-0 shadow-sm">
        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        <span>Upload / Change Picture</span>
    </button>
</div>

<!-- Dynamic Course Registration & Enrollment Form Builder -->
<div id="formBuilderCard" 
     x-data="courseFormBuilder({
        initialFields: @json($course->effective_enrollment_fields),
        templates: @json(\App\Models\Course::getBuiltinTemplates())
     })" 
     class="mb-8 bg-white rounded-2xl border border-gray-200/90 shadow-sm overflow-hidden">
     
    <!-- Card Header -->
    <div class="p-5 sm:p-6 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5 flex-wrap">
                <span class="px-2.5 py-0.5 rounded-full bg-blue-500/20 text-blue-300 border border-blue-400/30 text-[10px] font-black uppercase tracking-wider">
                    Dynamic Enrollment Engine
                </span>
                <span class="text-xs text-slate-300 font-medium" x-text="activeFieldsCount + ' Active Fields'"></span>
                <template x-if="isEmailEnabled">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold" :class="isEmailRequired ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-400/30' : 'bg-amber-500/20 text-amber-300 border border-amber-400/30'" x-text="isEmailRequired ? 'Email: Required (*)' : 'Email: Optional'"></span>
                </template>
                <template x-if="!isEmailEnabled">
                    <span class="px-2.5 py-0.5 rounded bg-rose-500/25 text-rose-300 border border-rose-400/40 text-[10px] font-bold flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                        <span>Email: Not Required / Disabled</span>
                    </span>
                </template>
            </div>
            <h2 class="text-lg sm:text-xl font-bold mt-1.5 tracking-tight text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Per-Course Registration Form Builder</span>
            </h2>
            <p class="text-xs text-slate-300 mt-1 max-w-2xl leading-relaxed">
                Configure which fields students must fill when registering for this course. Switch between 1-click basic templates (such as Name + Phone only without email, or Executive with Company and Role) or customize individual questions.
            </p>
        </div>
        <div class="flex items-center gap-2.5 shrink-0">
            <button type="button" @click="activeTab = (activeTab === 'preview' ? 'editor' : 'preview')" class="px-3.5 py-2 rounded-xl text-xs font-bold border border-white/20 bg-white/10 hover:bg-white/20 text-white transition-all flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                <span x-text="activeTab === 'preview' ? 'Back to Field Editor' : 'Live Student Preview'"></span>
            </button>
            <button type="button" @click="saveForm()" :disabled="isSaving" class="px-4 py-2 bg-[#c30102] hover:bg-[#a50102] disabled:opacity-50 text-white rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-md flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span x-text="isSaving ? 'Saving...' : 'Save Form Setup'"></span>
            </button>
        </div>
    </div>

    <!-- Alert / Toast Messages -->
    <div x-show="saveMessage" x-transition class="p-3.5 bg-emerald-50 border-b border-emerald-200 text-xs text-emerald-800 font-bold flex items-center gap-2" style="display: none;">
        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span x-text="saveMessage"></span>
    </div>
    <div x-show="saveError" x-transition class="p-3.5 bg-rose-50 border-b border-rose-200 text-xs text-rose-800 font-bold flex items-center gap-2" style="display: none;">
        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span x-text="saveError"></span>
    </div>

    <!-- 1-Click Basic Templates Selector Bar -->
    <div class="p-4 sm:p-5 bg-slate-50/90 border-b border-gray-100">
        <div class="text-[11px] font-black uppercase tracking-wider text-slate-600 mb-2.5 flex items-center justify-between">
            <span class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                <span>Choose 1-Click Basic Template</span>
            </span>
            <span class="text-[10px] text-gray-400 font-normal">Clicking a template populates the form below instantly</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <template x-for="(tpl, key) in templates" :key="key">
                <button type="button" 
                        @click="applyTemplate(key)"
                        class="text-left p-3.5 rounded-xl border transition-all relative group flex flex-col justify-between"
                        :class="currentTemplateKey === key ? 'border-indigo-600 bg-indigo-50/60 shadow-xs ring-2 ring-indigo-500/20' : 'border-gray-200 bg-white hover:border-indigo-300 hover:bg-slate-50/60'">
                    <div>
                        <div class="flex items-center justify-between gap-1 mb-1">
                            <span class="text-xs font-bold text-gray-900 group-hover:text-indigo-600 transition-colors" x-text="tpl.name"></span>
                            <span class="text-[9px] font-black px-1.5 py-0.5 rounded uppercase" 
                                  :class="tpl.key === 'quick_lead' ? 'bg-amber-100 text-amber-800' : (tpl.key === 'corporate' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800')"
                                  x-text="tpl.badge || 'Preset'"></span>
                        </div>
                        <p class="text-[11px] text-gray-500 leading-tight" x-text="tpl.description"></p>
                    </div>
                    <div class="mt-2.5 pt-2 border-t border-gray-100 flex items-center justify-between text-[10px]">
                        <span class="text-indigo-600 font-bold flex items-center gap-1">
                            <span>Use Template</span>
                            <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </span>
                        <template x-if="tpl.key === 'quick_lead'">
                            <span class="text-amber-700 font-black uppercase text-[9px] bg-amber-50 px-1 rounded border border-amber-200/50">Email Off</span>
                        </template>
                        <template x-if="tpl.key !== 'quick_lead'">
                            <span class="text-emerald-700 font-black uppercase text-[9px] bg-emerald-50 px-1 rounded border border-emerald-200/50">Email Req.</span>
                        </template>
                    </div>
                </button>
            </template>
        </div>
    </div>

    <!-- TAB 1: FIELD EDITOR -->
    <div x-show="activeTab === 'editor'" class="p-5 sm:p-6 space-y-6">
        <!-- Interactive Fields Table -->
        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-gray-50 text-gray-600 font-bold uppercase tracking-wider text-[11px] border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 w-14 text-center">Order</th>
                        <th class="px-4 py-3">Field Key</th>
                        <th class="px-4 py-3">Display Label</th>
                        <th class="px-4 py-3">Input Type</th>
                        <th class="px-4 py-3 text-center">Required Status</th>
                        <th class="px-4 py-3 text-center">Field Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    <template x-for="(field, idx) in fields" :key="field.name">
                        <tr class="hover:bg-slate-50/70 transition-colors" :class="field.is_enabled === false ? 'opacity-50 bg-slate-50/50' : ''">
                            <!-- Reorder buttons -->
                            <td class="px-3 py-3 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-1">
                                    <button type="button" @click="moveField(idx, -1)" :disabled="idx === 0" class="p-1 rounded text-gray-400 hover:text-gray-700 hover:bg-gray-100 disabled:opacity-20" title="Move Up">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                    </button>
                                    <button type="button" @click="moveField(idx, 1)" :disabled="idx === fields.length - 1" class="p-1 rounded text-gray-400 hover:text-gray-700 hover:bg-gray-100 disabled:opacity-20" title="Move Down">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                </div>
                            </td>

                            <!-- Field Key identifier -->
                            <td class="px-4 py-3 font-mono font-bold text-gray-800">
                                <div class="flex items-center gap-1.5">
                                    <span x-text="field.name"></span>
                                    <template x-if="['name', 'phone'].includes(field.name)">
                                        <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 text-[9px] font-sans font-bold">System Core</span>
                                    </template>
                                    <template x-if="field.name === 'email'">
                                        <span class="px-1.5 py-0.5 rounded bg-blue-100 text-blue-700 text-[9px] font-sans font-bold">Core Channel</span>
                                    </template>
                                </div>
                            </td>

                            <!-- Display Label (Inline Editable) -->
                            <td class="px-4 py-3">
                                <input type="text" 
                                       x-model="field.label" 
                                       placeholder="Label shown to student"
                                       class="w-full rounded-lg border-gray-300 text-xs py-1.5 px-2.5 focus:border-indigo-500 focus:ring-indigo-500">
                            </td>

                            <!-- Input Type -->
                            <td class="px-4 py-3">
                                <select x-model="field.type" 
                                        :disabled="['name', 'phone', 'email', 'training_mode'].includes(field.name)"
                                        class="rounded-lg border-gray-300 text-xs py-1.5 pl-2.5 pr-7 focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100 disabled:text-gray-500">
                                    <option value="text">Text Input</option>
                                    <option value="email">Email</option>
                                    <option value="tel">Telephone / Mobile</option>
                                    <option value="textarea">Textarea (Multi-line)</option>
                                    <option value="select">Dropdown Select</option>
                                    <option value="number">Number</option>
                                </select>
                            </td>

                            <!-- Required Toggle -->
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <template x-if="['name', 'phone'].includes(field.name)">
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black uppercase tracking-wider">
                                        * Mandatory
                                    </span>
                                </template>
                                <template x-if="!['name', 'phone'].includes(field.name)">
                                    <button type="button" 
                                            @click="toggleRequired(idx)"
                                            class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider transition-all"
                                            :class="field.is_required ? 'bg-emerald-100 text-emerald-800 border border-emerald-300 hover:bg-emerald-200' : 'bg-gray-100 text-gray-500 border border-gray-200 hover:bg-gray-200'">
                                        <span x-text="field.is_required ? '* Required' : 'Optional'"></span>
                                    </button>
                                </template>
                            </td>

                            <!-- Enabled / Visible Toggle -->
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <template x-if="['name', 'phone'].includes(field.name)">
                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-[10px] font-bold">Always Active</span>
                                </template>
                                <template x-if="!['name', 'phone'].includes(field.name)">
                                    <button type="button" 
                                            @click="toggleEnabled(idx)"
                                            class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider transition-all"
                                            :class="field.is_enabled !== false ? 'bg-indigo-100 text-indigo-700 border border-indigo-200 hover:bg-indigo-200' : 'bg-rose-100 text-rose-700 border border-rose-200 hover:bg-rose-200'">
                                        <span x-text="field.is_enabled !== false ? 'Active' : 'Disabled / Hidden'"></span>
                                    </button>
                                </template>
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <template x-if="!['name', 'phone', 'email', 'training_mode'].includes(field.name)">
                                    <button type="button" @click="removeField(idx)" class="text-rose-600 hover:text-rose-800 p-1 hover:bg-rose-50 rounded transition-colors" title="Delete custom field">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </template>
                                <template x-if="['name', 'phone', 'email', 'training_mode'].includes(field.name)">
                                    <span class="text-[10px] text-gray-400 italic">Locked</span>
                                </template>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Add Custom Field Inline Box -->
        <div class="p-4 bg-slate-50 rounded-xl border border-gray-200">
            <div class="text-xs font-bold text-gray-800 mb-2 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                <span>Add Custom Field to this Course</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-3">
                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">Field Key (variable)</label>
                    <input type="text" x-model="newField.name" placeholder="e.g. organization, nid" class="w-full rounded-lg border-gray-300 text-xs py-1.5 px-2.5 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="sm:col-span-4">
                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">Display Label</label>
                    <input type="text" x-model="newField.label" placeholder="e.g. Company / Institution" class="w-full rounded-lg border-gray-300 text-xs py-1.5 px-2.5 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">Type</label>
                    <select x-model="newField.type" class="w-full rounded-lg border-gray-300 text-xs py-1.5 pl-2 pr-7 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="text">Text</option>
                        <option value="textarea">Textarea</option>
                        <option value="tel">Telephone</option>
                        <option value="number">Number</option>
                    </select>
                </div>
                <div class="sm:col-span-2 flex items-center pb-2">
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-gray-700">
                        <input type="checkbox" x-model="newField.is_required" class="rounded text-indigo-600 focus:ring-indigo-500">
                        <span>Mandatory *</span>
                    </label>
                </div>
                <div class="sm:col-span-1">
                    <button type="button" @click="addCustomField()" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold transition-colors">
                        + Add
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: LIVE STUDENT DRAWER PREVIEW -->
    <div x-show="activeTab === 'preview'" class="p-6 bg-slate-100/70" style="display: none;">
        <div class="max-w-md mx-auto bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
            <!-- Simulated Drawer Header -->
            <div class="p-5 bg-[#051c45] text-white">
                <div class="flex items-center gap-2 mb-1.5">
                    @if(!empty($course->batch) && $course->show_batch)
                        <span class="px-2 py-0.5 rounded bg-[#c30102] text-white text-[9px] font-black uppercase">
                            {{ $course->batch }}
                        </span>
                    @endif
                    <span class="text-xs text-[#10b981] font-semibold flex items-center gap-1">
                        <i class="fas fa-bolt text-xs"></i> 30-Second Express Enroll
                    </span>
                </div>
                <h3 class="text-sm font-bold text-white leading-tight">{{ $course->title }}</h3>
                <div class="mt-2 pt-2 border-t border-white/10 flex justify-between text-xs">
                    <span class="text-gray-300">Investment:</span>
                    <span class="font-bold text-white">৳{{ number_format($course->price, 2) }}</span>
                </div>
            </div>

            <!-- Simulated Form Body -->
            <div class="p-5 space-y-3.5 bg-white">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-[#051c45] uppercase tracking-wider">Step 1: Student Information</span>
                    <span class="text-[10px] text-gray-400">Live Simulation</span>
                </div>

                <!-- Always Name -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Full Name *</label>
                    <input type="text" disabled placeholder="e.g. Tanvir Ahmed" class="w-full bg-slate-50 border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-500">
                </div>

                <!-- Mobile & Email Dynamic Simulation -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <div :class="isEmailEnabled ? '' : 'sm:col-span-2'">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Mobile (11 Digits) *</label>
                        <input type="tel" disabled placeholder="01917054664" class="w-full bg-slate-50 border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-500">
                    </div>
                    <template x-if="isEmailEnabled">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">
                                <span>Email Address</span>
                                <span x-show="isEmailRequired" class="text-red-500 font-bold">*</span>
                                <span x-show="!isEmailRequired" class="text-gray-400 font-normal text-[10px]">(Optional)</span>
                            </label>
                            <input type="email" disabled placeholder="name@company.com" class="w-full bg-slate-50 border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-500">
                        </div>
                    </template>
                </div>

                <!-- Dynamic Custom Fields Simulation -->
                <template x-for="field in fields.filter(f => !['name', 'phone', 'email', 'training_mode'].includes(f.name) && (f.is_enabled !== false))" :key="field.name">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            <span x-text="field.label"></span>
                            <span x-show="field.is_required" class="text-red-500 font-bold">*</span>
                            <span x-show="!field.is_required" class="text-gray-400 font-normal text-[10px]">(Optional)</span>
                        </label>
                        <input type="text" disabled :placeholder="field.placeholder || ('Enter ' + field.label)" class="w-full bg-slate-50 border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-500">
                    </div>
                </template>

                <!-- Attendance Mode Simulation -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Preferred Attendance Mode</label>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="p-2 rounded-lg border border-[#051c45] bg-[#051c45]/5 text-[#051c45] font-bold text-center">Live Online Zoom</div>
                        <div class="p-2 rounded-lg border border-gray-200 text-gray-500 text-center">Campus In-Person</div>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="button" disabled class="w-full py-2.5 bg-[#c30102] text-white font-bold rounded-xl text-xs text-center opacity-90 cursor-not-allowed">
                        Proceed to Payment / Confirmation
                    </button>
                </div>
            </div>
        </div>
        <p class="text-center text-xs text-gray-400 mt-3">
            This is an interactive simulation of the student enrollment drawer based on your active fields above.
        </p>
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
        currentTemplateKey: '',
        activeTab: 'editor',
        isSaving: false,
        saveMessage: '',
        saveError: '',
        newField: {
            name: '',
            label: '',
            type: 'text',
            is_required: false
        },

        init() {
            this.detectCurrentTemplate();
        },

        get isEmailEnabled() {
            const f = this.fields.find(field => field.name === 'email');
            return f ? f.is_enabled !== false : true;
        },

        get isEmailRequired() {
            const f = this.fields.find(field => field.name === 'email');
            return f ? (f.is_enabled !== false && !!f.is_required) : true;
        },

        get activeFieldsCount() {
            return this.fields.filter(f => f.is_enabled !== false).length;
        },

        detectCurrentTemplate() {
            for (const [key, tpl] of Object.entries(this.templates)) {
                if (tpl.fields.length === this.fields.length) {
                    const match = tpl.fields.every((tf, idx) => {
                        const cur = this.fields[idx];
                        return cur && cur.name === tf.name && (!!cur.is_required === !!tf.is_required) && (cur.is_enabled !== false === tf.is_enabled !== false);
                    });
                    if (match) {
                        this.currentTemplateKey = key;
                        return;
                    }
                }
            }
            this.currentTemplateKey = 'custom';
        },

        applyTemplate(key) {
            if (!this.templates[key]) return;
            this.fields = JSON.parse(JSON.stringify(this.templates[key].fields));
            this.currentTemplateKey = key;
            this.saveMessage = 'Applied "' + this.templates[key].name + '" template. Click "Save Form Setup" to confirm.';
            setTimeout(() => this.saveMessage = '', 4500);
        },

        toggleEnabled(idx) {
            const f = this.fields[idx];
            if (f.name === 'name' || f.name === 'phone') {
                alert('Full Name and Mobile Phone are mandatory student contact identifiers and cannot be disabled.');
                return;
            }
            f.is_enabled = !(f.is_enabled !== false);
            this.detectCurrentTemplate();
        },

        toggleRequired(idx) {
            const f = this.fields[idx];
            if (f.name === 'name' || f.name === 'phone') {
                alert('Full Name and Mobile Phone must remain mandatory.');
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
                alert('System fields cannot be deleted. You can disable the field instead using the toggle.');
                return;
            }
            if (confirm('Remove field "' + (f.label || f.name) + '"?')) {
                this.fields.splice(idx, 1);
                this.detectCurrentTemplate();
            }
        },

        addCustomField() {
            if (!this.newField.name.trim()) {
                alert('Please enter a Field ID / Name (e.g., organization, designation, city).');
                return;
            }
            const cleanName = this.newField.name.toLowerCase().replace(/[^a-z0-9_]/g, '_');
            if (this.fields.some(f => f.name === cleanName)) {
                alert('A field with ID "' + cleanName + '" already exists.');
                return;
            }
            const label = this.newField.label.trim() || cleanName.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());

            this.fields.push({
                name: cleanName,
                label: label,
                type: this.newField.type || 'text',
                is_required: !!this.newField.is_required,
                is_enabled: true,
                is_system: false,
                placeholder: 'Enter ' + label
            });

            this.newField = { name: '', label: '', type: 'text', is_required: false };
            this.detectCurrentTemplate();
        },

        async saveForm() {
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
                        enrollment_fields: this.fields
                    })
                });

                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Failed to save form settings.');
                }

                this.saveMessage = 'Registration form settings saved successfully for this course!';
                setTimeout(() => this.saveMessage = '', 4500);
            } catch (err) {
                this.saveError = err.message || 'An error occurred while saving form settings.';
            } finally {
                this.isSaving = false;
            }
        }
    };
}
</script>

<div class="space-y-6">
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
        <div class="bg-white rounded-2xl border border-purple-100 shadow-sm overflow-hidden mb-4">
            <div class="px-6 py-4 bg-purple-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-900 text-lg flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ $quiz->title }}
                </h3>
                <div class="flex items-center gap-3">
                    @if($quiz->is_published)
                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-medium">Published</span>
                    @else
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-medium">Draft</span>
                    @endif
                    <a href="{{ route('admin.quizzes.show', [$course, $quiz]) }}" class="text-sm font-medium text-purple-600 hover:underline">Manage Questions →</a>
                    <form method="POST" action="{{ route('admin.courses.quizzes.destroy', [$course, $quiz]) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete this quiz?')" class="text-sm font-medium text-red-500 hover:text-red-700 ml-2">Delete</button>
                    </form>
                </div>
            </div>
            @if($quiz->description)
                <div class="px-6 py-3 text-sm text-gray-600 border-t border-purple-100 bg-white">
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
<div class="mb-8">
    <div class="flex justify-between items-center mb-4 border-t pt-8 border-gray-200">
        <h2 class="text-xl font-bold text-gray-900">Course Materials</h2>
        <button onclick="document.getElementById('addMaterialModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-50 text-indigo-700 rounded-lg font-medium hover:bg-indigo-100 transition-colors">
            + Add Material
        </button>
    </div>

    <div class="space-y-4">
        @forelse($course->materials as $material)
            <div class="bg-white rounded-2xl border border-gray-100 p-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-500 flex items-center justify-center shrink-0">
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
                        <a href="{{ $material->external_link }}" target="_blank" class="text-indigo-600 hover:underline text-sm font-medium">View</a>
                    @else
                        <a href="{{ Storage::url($material->file_path) }}" target="_blank" class="text-indigo-600 hover:underline text-sm font-medium">Download</a>
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
<div class="mb-8">
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
                                    <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold mr-3 shrink-0">
                                        {{ substr($student->name, 0, 1) }}
                                    </div>
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
<div class="mb-8">
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
                <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:opacity-90">Create Quiz</button>
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
                    <input type="text" name="title" required class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="type" id="materialType" onchange="toggleMaterialFields()" required class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="link">External Link</option>
                        <option value="pdf">PDF Document</option>
                        <option value="zip">ZIP File</option>
                    </select>
                </div>
                <div id="linkField">
                    <label class="block text-sm font-medium text-gray-700 mb-1">External Link URL</label>
                    <input type="url" name="external_link" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div id="fileField" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">File Upload</label>
                    <input type="file" name="file_path" accept=".pdf,.zip" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="text-xs text-gray-500 mt-1">Max 10MB.</p>
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:opacity-90 mt-2">Add Material</button>
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
                        <input type="text" name="venue" value="{{ $course->venue }}" placeholder="e.g. Online (Zoom) / Banani" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Batch / Cohort (Optional)</label>
                        <input type="text" name="batch" value="{{ $course->batch }}" placeholder="e.g. 105th Batch" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary text-sm">
                    </div>
                    <div class="flex items-end pb-1">
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
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Registration Form Template</label>
                    <select name="enrollment_template" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary text-xs">
                        <option value="">-- Keep Current Form Setup ({{ count($course->effective_enrollment_fields) }} Fields) --</option>
                        <option value="standard">Standard Professional (Name + Phone + Email Required)</option>
                        <option value="quick_lead">Quick Lead (Name + Phone Only - Email Disabled/Not Required)</option>
                        <option value="corporate">Executive & Corporate (Name + Phone + Email + Company + Role)</option>
                        <option value="comprehensive">Comprehensive Application (Full Fields + Questions)</option>
                    </select>
                    <p class="text-[10px] text-gray-400 mt-1">Select a preset to quickly switch basic templates, or use the Form Builder card for granular fields.</p>
                </div>
                <div class="p-3.5 bg-emerald-50/80 rounded-xl border border-emerald-200 flex items-center justify-between">
                    <div>
                        <label for="edit_certificate_enabled" class="block text-xs font-bold text-emerald-950">Release Digital Certificates to Students</label>
                        <p class="text-[11px] text-emerald-800 mt-0.5">When checked, enrolled students who completed the course can view and download their official digital certificate.</p>
                    </div>
                    <div class="ml-4">
                        <input type="checkbox" name="certificate_enabled" id="edit_certificate_enabled" value="1" {{ $course->certificate_enabled ? 'checked' : '' }} class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-gray-300">
                    </div>
                </div>
                <div class="flex items-center pt-2">
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

@endsection
