@extends('layouts.admin')

@section('header_title', 'Corporate Job Posts')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-xl sm:text-2xl font-black text-gray-900 mb-1">Manage Jobs</h1>
        <p class="text-gray-500 text-xs sm:text-sm">Manage executive and commercial placement postings for ACS Academy alumni & students.</p>
    </div>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-primary text-white rounded-xl font-bold text-xs sm:text-sm hover:opacity-90 transition-all shadow-sm shrink-0 cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        <span>Post New Job</span>
    </button>
</div>

@if(session('success'))
    <div class="p-4 mb-6 text-sm text-green-800 rounded-xl bg-green-50 border border-green-200 flex items-center gap-2.5">
        <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span>{{ session('success') }}</span>
    </div>
@endif

<div class="bg-white rounded-2xl sm:rounded-3xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[700px]">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-100 text-xs uppercase tracking-wider text-gray-700">
                    <th class="p-3.5 sm:p-4 font-bold">Job Title & Type</th>
                    <th class="p-3.5 sm:p-4 font-bold">Company</th>
                    <th class="p-3.5 sm:p-4 font-bold">Location & Salary</th>
                    <th class="p-3.5 sm:p-4 font-bold">Status</th>
                    <th class="p-3.5 sm:p-4 font-bold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-xs sm:text-sm divide-y divide-gray-50">
                @forelse($jobs as $job)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="p-3.5 sm:p-4">
                            <div class="font-bold text-gray-900">{{ $job->title }}</div>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                    {{ $job->job_type ?? 'Full-time' }}
                                </span>
                                @if($job->apply_link)
                                    <span class="inline-flex items-center text-[10px] text-gray-400 gap-1" title="External link">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        External
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-[10px] text-emerald-600 font-semibold gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Direct Portal
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="p-3.5 sm:p-4 text-gray-600">
                            @php
                                $aComp = $job->company ?: 'Company';
                                $aWords = array_values(array_filter(explode(' ', preg_replace('/[^A-Za-z0-9\s]/', '', $aComp))));
                                $aInit = count($aWords) >= 2
                                    ? strtoupper(substr($aWords[0], 0, 1) . substr($aWords[1], 0, 1))
                                    : strtoupper(substr($aWords[0] ?? 'CA', 0, 2));
                            @endphp
                            <div class="flex items-center gap-2.5">
                                @if($job->company_logo)
                                    <div class="w-10 h-10 rounded-xl bg-white border border-gray-200 p-1 flex items-center justify-center shrink-0 shadow-xs overflow-hidden">
                                        <img src="{{ storage_asset($job->company_logo) }}" alt="{{ $job->company }}" class="w-full h-full object-contain object-center" onerror="this.parentElement.style.display='none'; this.parentElement.nextElementSibling.style.display='flex';">
                                    </div>
                                    <div style="display:none;" class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#051c45] to-[#1e3a8a] text-white items-center justify-center shrink-0 shadow-xs">
                                        <span class="text-xs font-black tracking-wider">{{ $aInit }}</span>
                                    </div>
                                @else
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#051c45] to-[#1e3a8a] text-white flex items-center justify-center shrink-0 shadow-xs">
                                        <span class="text-xs font-black tracking-wider">{{ $aInit }}</span>
                                    </div>
                                @endif
                                <span class="font-bold text-gray-800 truncate max-w-[150px] sm:max-w-xs">{{ $job->company }}</span>
                            </div>
                        </td>
                        <td class="p-3.5 sm:p-4">
                            <div class="text-gray-700 font-medium">{{ $job->location ?? 'Remote' }}</div>
                            <div class="text-gray-400 text-xs">{{ $job->salary_range ?? 'Negotiable' }}</div>
                        </td>
                        <td class="p-3.5 sm:p-4 whitespace-nowrap">
                            @if($job->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-600 mr-1.5"></span>Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-1.5"></span>Inactive
                                </span>
                            @endif
                        </td>
                        <td class="p-3.5 sm:p-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('admin.jobs.show', $job) }}" class="text-primary hover:text-[#03112b] font-bold text-xs bg-primary/10 hover:bg-primary/20 px-2.5 py-1.5 rounded-lg transition-colors inline-flex items-center gap-1">
                                    <span>Applicants</span>
                                    <span class="bg-primary text-white text-[10px] px-1.5 py-0.5 rounded-full font-black">{{ $job->applications_count ?? $job->applications->count() }}</span>
                                </a>
                                
                                <button type="button" 
                                    data-job="{{ json_encode([
                                        'id' => $job->id,
                                        'title' => $job->title,
                                        'company' => $job->company,
                                        'job_type' => $job->job_type ?? 'Full-time',
                                        'location' => $job->location ?? '',
                                        'salary_range' => $job->salary_range ?? '',
                                        'apply_link' => $job->apply_link ?? '',
                                        'description' => $job->description,
                                        'company_logo_url' => $job->company_logo ? storage_asset($job->company_logo) : '',
                                        'is_active' => (bool)$job->is_active
                                    ]) }}"
                                    onclick="openEditJobModal(this)" 
                                    class="text-gray-600 hover:text-gray-900 font-medium text-xs bg-gray-100 hover:bg-gray-200 px-2.5 py-1.5 rounded-lg transition-colors cursor-pointer">
                                    Edit
                                </button>

                                <form method="POST" action="{{ route('admin.jobs.destroy', $job) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Are you sure you want to delete this job posting?')" class="text-red-600 hover:text-red-800 font-medium text-xs hover:bg-red-50 px-2 py-1.5 rounded-lg transition-colors cursor-pointer">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-12 text-center text-gray-500">
                            <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3 text-xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <div class="font-bold text-gray-700">No jobs posted yet</div>
                            <div class="text-xs text-gray-400 mt-1">Click "+ Post New Job" to create your first placement role.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Create Modal -->
<div id="createModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4 sm:p-6 hidden overflow-y-auto">
    <div class="bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-6 w-full max-w-lg shadow-2xl my-auto">
        <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-100">
            <h3 class="text-base sm:text-lg font-bold text-gray-900">New Corporate Job Post</h3>
            <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-2xl font-bold p-1 cursor-pointer leading-none">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.jobs.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="space-y-3.5 max-h-[72vh] overflow-y-auto pr-1">
                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1">Job Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required placeholder="e.g. Commercial Executive (Export & L/C)" class="w-full text-xs sm:text-sm rounded-xl border-gray-300 focus:border-primary focus:ring-primary py-2 px-3">
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1">Company <span class="text-red-500">*</span></label>
                        <input type="text" name="company" id="create_company" oninput="updateCreateLogoPreview()" required placeholder="e.g. Square Group" class="w-full text-xs sm:text-sm rounded-xl border-gray-300 focus:border-primary focus:ring-primary py-2 px-3">
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1">Job Type</label>
                        <select name="job_type" class="w-full text-xs sm:text-sm rounded-xl border-gray-300 focus:border-primary focus:ring-primary py-2 px-3">
                            <option value="Full-time">Full-time</option>
                            <option value="Contract">Contract</option>
                            <option value="Remote">Remote / Hybrid</option>
                            <option value="Part-time">Part-time</option>
                            <option value="Internship">Internship</option>
                        </select>
                    </div>
                </div>

                <!-- Smart Auto-Fitted Company Logo Uploader with Fallback Emblem Preview -->
                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200/80 flex items-center gap-3.5">
                    <div class="w-14 h-14 rounded-2xl shrink-0 relative overflow-hidden">
                        <div id="create_logo_img_wrap" class="w-full h-full rounded-2xl bg-white border border-slate-200 p-1.5 items-center justify-center hidden">
                            <img id="create_logo_preview_img" src="" alt="Preview" class="w-full h-full object-contain object-center">
                        </div>
                        <div id="create_logo_emblem_wrap" class="w-full h-full rounded-2xl bg-gradient-to-br from-[#051c45] to-[#1e3a8a] text-white flex items-center justify-center shadow-sm">
                            <span id="create_logo_initials" class="text-sm font-black tracking-wider">SG</span>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <label class="block text-xs font-bold text-gray-800 mb-1">Company Logo (Optional • Auto-Fitted)</label>
                        <input type="file" name="company_logo" id="create_job_logo_file" accept="image/*" onchange="previewCreateJobLogo(this)" class="w-full text-xs rounded-xl border border-gray-300 bg-white p-1.5 focus:border-primary focus:ring-primary">
                        <p class="text-[10px] text-gray-500 mt-1">If left empty, a clean corporate monogram emblem is generated automatically. Uploaded logos are auto-fitted without cropping.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1">Location</label>
                        <input type="text" name="location" placeholder="e.g. Dhaka (Mirpur)" class="w-full text-xs sm:text-sm rounded-xl border-gray-300 focus:border-primary focus:ring-primary py-2 px-3">
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1">Salary Range</label>
                        <input type="text" name="salary_range" placeholder="e.g. ৳50,000 - ৳70,000" class="w-full text-xs sm:text-sm rounded-xl border-gray-300 focus:border-primary focus:ring-primary py-2 px-3">
                    </div>
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1">Application URL (Optional - leave empty for direct candidate uploads)</label>
                    <input type="url" name="apply_link" placeholder="https://..." class="w-full text-xs sm:text-sm rounded-xl border-gray-300 focus:border-primary focus:ring-primary py-2 px-3">
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1">Job Description & Requirements <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="4" required class="w-full text-xs sm:text-sm rounded-xl border-gray-300 focus:border-primary focus:ring-primary p-3" placeholder="Key responsibilities, required experience in L/C, customs clearance, VAT..."></textarea>
                </div>

                <div class="flex items-center pt-1">
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked class="rounded text-primary focus:ring-primary mr-2 cursor-pointer">
                    <label for="is_active" class="text-xs sm:text-sm font-medium text-gray-700 cursor-pointer">Publish immediately to students & career portal</label>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 bg-primary text-white rounded-xl font-bold text-xs sm:text-sm hover:opacity-90 shadow-md transition-all cursor-pointer">Post Job</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editJobModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4 sm:p-6 hidden overflow-y-auto">
    <div class="bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-6 w-full max-w-lg shadow-2xl my-auto">
        <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-100">
            <h3 class="text-base sm:text-lg font-bold text-gray-900">Edit Corporate Job Post</h3>
            <button type="button" onclick="document.getElementById('editJobModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-2xl font-bold p-1 cursor-pointer leading-none">&times;</button>
        </div>
        <form method="POST" id="editJobForm" action="" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="space-y-3.5 max-h-[72vh] overflow-y-auto pr-1">
                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1">Job Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="edit_title" required class="w-full text-xs sm:text-sm rounded-xl border-gray-300 focus:border-primary focus:ring-primary py-2 px-3">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1">Company <span class="text-red-500">*</span></label>
                        <input type="text" name="company" id="edit_company" oninput="updateEditLogoInitials()" required class="w-full text-xs sm:text-sm rounded-xl border-gray-300 focus:border-primary focus:ring-primary py-2 px-3">
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1">Job Type</label>
                        <select name="job_type" id="edit_job_type" class="w-full text-xs sm:text-sm rounded-xl border-gray-300 focus:border-primary focus:ring-primary py-2 px-3">
                            <option value="Full-time">Full-time</option>
                            <option value="Contract">Contract</option>
                            <option value="Remote">Remote / Hybrid</option>
                            <option value="Part-time">Part-time</option>
                            <option value="Internship">Internship</option>
                        </select>
                    </div>
                </div>

                <!-- Smart Auto-Fitted Company Logo Updater & Fallback Emblem Preview -->
                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200/80 flex items-center gap-3.5">
                    <div class="w-14 h-14 rounded-2xl shrink-0 relative overflow-hidden">
                        <div id="edit_logo_img_wrap" class="w-full h-full rounded-2xl bg-white border border-slate-200 p-1.5 items-center justify-center hidden">
                            <img id="edit_logo_preview_img" src="" alt="Preview" class="w-full h-full object-contain object-center">
                        </div>
                        <div id="edit_logo_emblem_wrap" class="w-full h-full rounded-2xl bg-gradient-to-br from-[#051c45] to-[#1e3a8a] text-white flex items-center justify-center shadow-sm">
                            <span id="edit_logo_initials" class="text-sm font-black tracking-wider">CA</span>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-gray-800">Update Company Logo (Auto-Fitted)</label>
                            <label class="inline-flex items-center gap-1 text-[10px] font-bold text-red-600 cursor-pointer">
                                <input type="checkbox" name="remove_company_logo" id="edit_remove_logo" value="1" onchange="toggleRemoveEditLogo(this)" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                <span>Use Auto Emblem</span>
                            </label>
                        </div>
                        <input type="file" name="company_logo" id="edit_job_logo_file" accept="image/*" class="w-full text-xs rounded-xl border border-gray-300 bg-white p-1.5 focus:border-primary focus:ring-primary">
                        <input type="hidden" name="company_logo_base64" id="edit_company_logo_base64">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1">Location</label>
                        <input type="text" name="location" id="edit_location" placeholder="e.g. Remote, NY" class="w-full text-xs sm:text-sm rounded-xl border-gray-300 focus:border-primary focus:ring-primary py-2 px-3">
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1">Salary Range</label>
                        <input type="text" name="salary_range" id="edit_salary" placeholder="e.g. ৳50k - ৳70k" class="w-full text-xs sm:text-sm rounded-xl border-gray-300 focus:border-primary focus:ring-primary py-2 px-3">
                    </div>
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1">Application URL (Optional)</label>
                    <input type="url" name="apply_link" id="edit_apply_link" class="w-full text-xs sm:text-sm rounded-xl border-gray-300 focus:border-primary focus:ring-primary py-2 px-3">
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1">Job Description <span class="text-red-500">*</span></label>
                    <textarea name="description" id="edit_description" rows="4" required class="w-full text-xs sm:text-sm rounded-xl border-gray-300 focus:border-primary focus:ring-primary p-3"></textarea>
                </div>

                <div class="flex items-center pt-1">
                    <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="rounded text-primary focus:ring-primary mr-2 cursor-pointer">
                    <label for="edit_is_active" class="text-xs sm:text-sm font-medium text-gray-700 cursor-pointer">Published / Active</label>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 bg-primary text-white rounded-xl font-bold text-xs sm:text-sm hover:opacity-90 shadow-md transition-all cursor-pointer">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function computeCompanyInitials(name) {
        const clean = (name || '').replace(/[^A-Za-z0-9\s]/g, '').trim();
        if (!clean) return 'CA';
        const parts = clean.split(/\s+/).filter(Boolean);
        if (parts.length >= 2) {
            return (parts[0][0] + parts[1][0]).toUpperCase();
        }
        return parts[0].substring(0, 2).toUpperCase();
    }

    function updateCreateLogoPreview() {
        const comp = document.getElementById('create_company')?.value || '';
        const initEl = document.getElementById('create_logo_initials');
        if (initEl) initEl.innerText = computeCompanyInitials(comp);
    }

    function previewCreateJobLogo(input) {
        const file = input.files && input.files[0];
        const imgWrap = document.getElementById('create_logo_img_wrap');
        const imgEl = document.getElementById('create_logo_preview_img');
        const embWrap = document.getElementById('create_logo_emblem_wrap');
        if (file) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                imgEl.src = evt.target.result;
                imgWrap.classList.remove('hidden');
                imgWrap.classList.add('flex');
                embWrap.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            imgWrap.classList.add('hidden');
            imgWrap.classList.remove('flex');
            embWrap.classList.remove('hidden');
        }
    }

    function updateEditLogoInitials() {
        const comp = document.getElementById('edit_company')?.value || '';
        const initEl = document.getElementById('edit_logo_initials');
        if (initEl) initEl.innerText = computeCompanyInitials(comp);
    }

    function toggleRemoveEditLogo(checkbox) {
        const imgWrap = document.getElementById('edit_logo_img_wrap');
        const embWrap = document.getElementById('edit_logo_emblem_wrap');
        if (checkbox.checked) {
            imgWrap.classList.add('hidden');
            imgWrap.classList.remove('flex');
            embWrap.classList.remove('hidden');
        }
    }

    document.getElementById('edit_job_logo_file')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        const imgWrap = document.getElementById('edit_logo_img_wrap');
        const imgEl = document.getElementById('edit_logo_preview_img');
        const embWrap = document.getElementById('edit_logo_emblem_wrap');
        const removeCb = document.getElementById('edit_remove_logo');
        if (file) {
            if (removeCb) removeCb.checked = false;
            const reader = new FileReader();
            reader.onload = function(evt) {
                document.getElementById('edit_company_logo_base64').value = evt.target.result;
                imgEl.src = evt.target.result;
                imgWrap.classList.remove('hidden');
                imgWrap.classList.add('flex');
                embWrap.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }
    });

    function openEditJobModal(buttonEl) {
        try {
            const raw = buttonEl.getAttribute('data-job');
            const job = JSON.parse(raw);
            document.getElementById('edit_title').value = job.title || '';
            document.getElementById('edit_company').value = job.company || '';
            document.getElementById('edit_job_type').value = job.job_type || 'Full-time';
            document.getElementById('edit_location').value = job.location || '';
            document.getElementById('edit_salary').value = job.salary_range || '';
            document.getElementById('edit_apply_link').value = job.apply_link || '';
            document.getElementById('edit_description').value = job.description || '';
            document.getElementById('edit_is_active').checked = !!job.is_active;
            const fileInput = document.getElementById('edit_job_logo_file');
            if (fileInput) fileInput.value = '';
            const base64Input = document.getElementById('edit_company_logo_base64');
            if (base64Input) base64Input.value = '';
            const removeCb = document.getElementById('edit_remove_logo');
            if (removeCb) removeCb.checked = false;

            updateEditLogoInitials();
            const imgWrap = document.getElementById('edit_logo_img_wrap');
            const imgEl = document.getElementById('edit_logo_preview_img');
            const embWrap = document.getElementById('edit_logo_emblem_wrap');
            if (job.company_logo_url) {
                imgEl.src = job.company_logo_url;
                imgWrap.classList.remove('hidden');
                imgWrap.classList.add('flex');
                embWrap.classList.add('hidden');
            } else {
                imgWrap.classList.add('hidden');
                imgWrap.classList.remove('flex');
                embWrap.classList.remove('hidden');
            }
            
            document.getElementById('editJobForm').action = `/admin/jobs/${job.id}`;
            document.getElementById('editJobModal').classList.remove('hidden');
        } catch (err) {
            console.error('Failed to parse job data for edit:', err);
        }
    }

    // Close modals on backdrop click
    ['createModal', 'editJobModal'].forEach(id => {
        const modal = document.getElementById(id);
        modal?.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });
    });

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.getElementById('createModal')?.classList.add('hidden');
            document.getElementById('editJobModal')?.classList.add('hidden');
        }
    });
</script>
@endsection
