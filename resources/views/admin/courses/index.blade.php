@extends('layouts.admin')

@section('content')
<div class="mb-8 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold">Courses</h1>
        <p class="text-gray-500 text-sm">Manage your platform's learning content.</p>
    </div>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:opacity-90">
        + New Course
    </button>
</div>

@if(session('success'))
    <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-6 py-4 font-medium">Cover</th>
                <th class="px-6 py-4 font-medium">Title</th>
                @if(auth()->user()->role === 'admin')
                    <th class="px-6 py-4 font-medium">Instructor</th>
                @endif
                <th class="px-6 py-4 font-medium">Metrics</th>
                <th class="px-6 py-4 font-medium">Status</th>
                <th class="px-6 py-4 font-medium">Created</th>
                <th class="px-6 py-4 font-medium text-right">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($courses as $c)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4">
                    <div class="w-16 h-11 rounded-lg overflow-hidden border border-gray-200 bg-gray-100 shadow-sm shrink-0">
                        <img src="{{ $c->cover_image_url }}" alt="{{ $c->title }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=800&q=80';">
                    </div>
                </td>
                <td class="px-6 py-4 font-medium text-gray-900">
                    <a href="{{ route('admin.courses.show', $c) }}" class="hover:text-primary transition-colors">{{ $c->title }}</a>
                    @if($c->category)
                        <div class="text-[11px] text-gray-400 mt-0.5">{{ $c->category }}</div>
                    @endif
                </td>
                @if(auth()->user()->role === 'admin')
                    <td class="px-6 py-4 text-gray-500">
                        {{ $c->instructorProfile ? $c->instructorProfile->name : ($c->instructor ? $c->instructor->name : 'N/A') }}
                    </td>
                @endif
                <td class="px-6 py-4 text-gray-500 text-xs">
                    <div>👁 {{ number_format($c->views) }} views</div>
                </td>
                <td class="px-6 py-4">
                    @if($c->is_published)
                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-medium">Published</span>
                    @else
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-medium">Draft</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-gray-500 text-xs">{{ $c->created_at->format('M d, Y') }}</td>
                <td class="px-6 py-4 text-right whitespace-nowrap space-x-2">
                    <a href="{{ route('admin.courses.show', $c) }}" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold text-primary hover:text-white border border-primary/30 hover:bg-primary rounded-lg transition-colors">Manage →</a>
                    <form action="{{ route('admin.courses.destroy', $c) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this course? It will be moved to the Trash.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold text-red-600 hover:text-white border border-red-200 hover:bg-red-600 rounded-lg transition-colors">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-12 text-center text-gray-500">No courses yet. Create one to get started!</td>
            </tr>
            @endforelse
        </tbody>
        </table>
    </div>
</div>

<!-- Create Modal -->
<div id="createModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">New Course</h3>
            <button onclick="document.getElementById('createModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.courses') }}" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4 max-h-[70vh] overflow-y-auto pr-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                    <textarea name="description" rows="3" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Short Excerpt / Card Snippet (Optional)</label>
                    <input type="text" name="short_description" placeholder="Brief 1-2 sentence overview for course card" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Starting Date</label>
                        <input type="date" name="start_date" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Price (৳ BDT)</label>
                        <input type="number" step="0.01" name="price" value="0.00" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Duration</label>
                        <input type="text" name="duration" placeholder="e.g. 8 Days / 2 Months" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Venue / Format</label>
                        <input type="text" name="venue" placeholder="e.g. Online (Zoom) / Banani" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Batch / Cohort (Optional)</label>
                        <input type="text" name="batch" placeholder="e.g. 105th Batch" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary text-sm">
                    </div>
                    <div class="flex items-end pb-1">
                        <label class="flex items-center gap-2 cursor-pointer p-2.5 rounded-lg border border-gray-200 bg-gray-50/70 hover:bg-gray-100 transition-colors w-full">
                            <input type="checkbox" name="show_batch" value="1" class="rounded text-primary focus:ring-primary">
                            <span class="text-xs font-semibold text-gray-700">Show Batch Publicly</span>
                        </label>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Level (Optional)</label>
                        <input type="text" name="level" placeholder="e.g. Executive / Certificate" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <input list="category-options-create" type="text" name="category" placeholder="Choose or type category..." class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                        <datalist id="category-options-create">
                            @foreach(\App\Models\Category::active()->ordered()->get() as $cat)
                                <option value="{{ $cat->name }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Who Should Attend / Target Audience (Optional)</label>
                    <textarea name="target_audience" rows="2" placeholder="e.g. Commercial Executives, Importers, Exporters, Entrepreneurs..." class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary text-sm"></textarea>
                </div>
                @if(auth()->user()->role === 'admin')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Instructor (Optional)</label>
                    <select name="instructor_id" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                        <option value="">-- Assign Instructor --</option>
                        @foreach($instructors as $instructor)
                            <option value="{{ $instructor->id }}">{{ $instructor->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-200">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Course Cover Photo (Picture)</label>
                    <div class="flex items-center gap-4">
                        <div class="w-24 h-16 rounded-lg overflow-hidden border border-gray-300 bg-gray-200 shrink-0 shadow-inner flex items-center justify-center">
                            <img id="create_course_cover_preview" src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=300&q=80" alt="Preview" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1">
                            <input type="file" id="create_course_cover_input" name="cover_image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:opacity-90 file:cursor-pointer cursor-pointer border border-gray-300 rounded-lg p-1 bg-white">
                            <input type="hidden" name="cover_image_base64" id="create_course_cover_base64">
                            <p class="text-[10px] text-gray-400 mt-1">PNG, JPG, WebP (16:10 or 16:9 ratio recommended)</p>
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
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Registration Form Template</label>
                    <select name="enrollment_template" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary text-xs">
                        <option value="standard">Standard Professional (Name + Phone + Email Required)</option>
                        <option value="quick_lead">Quick Lead (Name + Phone Only - Email Disabled/Not Required)</option>
                        <option value="corporate">Executive & Corporate (Name + Phone + Email + Company + Role)</option>
                        <option value="comprehensive">Comprehensive Application (Full Fields + Questions)</option>
                    </select>
                    <p class="text-[10px] text-gray-400 mt-1">Choose the registration form preset. You can further customize fields anytime.</p>
                </div>
                <div class="p-3 bg-emerald-50/80 rounded-xl border border-emerald-200 flex items-center justify-between">
                    <div>
                        <label for="create_certificate_enabled" class="block text-xs font-bold text-emerald-950">Release Digital Certificates</label>
                        <p class="text-[11px] text-emerald-800 mt-0.5">Allow enrolled students to download their certificate.</p>
                    </div>
                    <div class="ml-4">
                        <input type="checkbox" name="certificate_enabled" id="create_certificate_enabled" value="1" class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-gray-300">
                    </div>
                </div>
                <div class="flex items-center pt-1">
                    <input type="checkbox" name="is_published" id="is_published" value="1" class="rounded text-primary focus:ring-primary mr-2">
                    <label for="is_published" class="text-sm font-medium text-gray-700">Publish immediately</label>
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-primary text-white rounded-lg font-medium hover:opacity-90 mt-2">Create Course</button>
            </div>
        </form>
    </div>
</div>
@endsection
