@extends('layouts.admin')

@section('header_title', 'Job Posts')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Manage Jobs</h1>
        <p class="text-gray-500 text-sm">Create and manage job postings for students.</p>
    </div>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:opacity-90 transition-opacity">
        + Post Job
    </button>
</div>

@if(session('success'))
    <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-3xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-100">
                <th class="p-4 font-semibold text-gray-900">Job Title</th>
                <th class="p-4 font-semibold text-gray-900">Company</th>
                <th class="p-4 font-semibold text-gray-900">Location</th>
                <th class="p-4 font-semibold text-gray-900">Status</th>
                <th class="p-4 font-semibold text-gray-900 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jobs as $job)
                <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                    <td class="p-4 font-medium text-gray-900">{{ $job->title }}</td>
                    <td class="p-4 text-gray-500">
                        <div class="flex items-center gap-3">
                            @if($job->company_logo)
                                <img src="{{ Storage::url($job->company_logo) }}" alt="{{ $job->company }}" class="w-8 h-8 rounded-full object-cover border border-gray-200 shrink-0">
                            @else
                                <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center shrink-0 border border-gray-200">
                                    <span class="text-xs font-bold text-gray-500">{{ substr($job->company, 0, 1) }}</span>
                                </div>
                            @endif
                            <span>{{ $job->company }}</span>
                        </div>
                    </td>
                    <td class="p-4 text-gray-500">{{ $job->location ?? 'Remote' }}</td>
                    <td class="p-4">
                        @if($job->is_active)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Active</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Inactive</span>
                        @endif
                    </td>
                    <td class="p-4 text-right whitespace-nowrap">
                        <a href="{{ route('admin.jobs.show', $job) }}" class="text-primary hover:text-green-700 font-medium text-sm mr-3">View Applicants</a>
                        <button onclick='openEditJobModal({{ $job->id }}, @json($job->title), @json($job->company), @json($job->location ?? ""), @json($job->salary_range ?? ""), @json($job->apply_link ?? ""), @json($job->description), {{ $job->is_active ? "true" : "false" }})' class="text-gray-500 hover:text-gray-900 font-medium text-sm mr-3">Edit</button>
                        <form method="POST" action="{{ route('admin.jobs.destroy', $job) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Are you sure?')" class="text-red-600 hover:text-red-900 font-medium text-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-gray-500">No jobs posted yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Create Modal -->
<div id="createModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-lg shadow-xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">New Job Post</h3>
            <button onclick="document.getElementById('createModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.jobs.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4 max-h-[70vh] overflow-y-auto pr-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Job Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Company <span class="text-red-500">*</span></label>
                    <input type="text" name="company" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Company Logo (Optional)</label>
                    <input type="file" name="company_logo" accept="image/*" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                        <input type="text" name="location" placeholder="e.g. Remote, NY" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Salary Range</label>
                        <input type="text" name="salary_range" placeholder="e.g. $50k - $70k" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Application Link (URL) (Optional)</label>
                    <input type="url" name="apply_link" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Job Description <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="4" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"></textarea>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked class="rounded text-primary focus:ring-primary mr-2">
                    <label for="is_active" class="text-sm font-medium text-gray-700">Publish immediately</label>
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-primary text-white rounded-lg font-medium hover:opacity-90">Post Job</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editJobModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-lg shadow-xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Edit Job Post</h3>
            <button onclick="document.getElementById('editJobModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form method="POST" id="editJobForm" action="" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="space-y-4 max-h-[70vh] overflow-y-auto pr-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Job Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="edit_title" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Company <span class="text-red-500">*</span></label>
                    <input type="text" name="company" id="edit_company" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Company Logo (Optional)</label>
                    <input type="file" name="company_logo" accept="image/*" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                        <input type="text" name="location" id="edit_location" placeholder="e.g. Remote, NY" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Salary Range</label>
                        <input type="text" name="salary_range" id="edit_salary" placeholder="e.g. $50k - $70k" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Application Link (URL) (Optional)</label>
                    <input type="url" name="apply_link" id="edit_apply_link" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Job Description <span class="text-red-500">*</span></label>
                    <textarea name="description" id="edit_description" rows="4" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"></textarea>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="rounded text-primary focus:ring-primary mr-2">
                    <label for="edit_is_active" class="text-sm font-medium text-gray-700">Publish immediately</label>
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-primary text-white rounded-lg font-medium hover:opacity-90">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditJobModal(id, title, company, location, salary, apply_link, description, is_active) {
        document.getElementById('edit_title').value = title;
        document.getElementById('edit_company').value = company;
        document.getElementById('edit_location').value = location;
        document.getElementById('edit_salary').value = salary;
        document.getElementById('edit_apply_link').value = apply_link;
        document.getElementById('edit_description').value = description;
        document.getElementById('edit_is_active').checked = is_active;
        
        document.getElementById('editJobForm').action = `/admin/jobs/${id}`;
        document.getElementById('editJobModal').classList.remove('hidden');
    }
</script>
@endsection
