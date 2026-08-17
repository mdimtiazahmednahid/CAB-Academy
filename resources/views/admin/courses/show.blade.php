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
    </div>
    <div class="flex gap-2">
        <button onclick="document.getElementById('editCourseModal').classList.remove('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200">
            Settings
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

<div class="space-y-6">
    @forelse($course->sections as $section)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-gray-900 text-lg flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    {{ $section->title }}
                </h3>
                <div class="flex items-center gap-3">
                    <button onclick="openEditSectionModal({{ $section->id }}, '{{ addslashes($section->title) }}', {{ $section->order }})" class="text-sm font-medium text-gray-500 hover:text-gray-900">Edit</button>
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
                            <button onclick="openEditLessonModal({{ $section->id }}, {{ $lesson->id }}, '{{ addslashes($lesson->title) }}', '{{ addslashes($lesson->video_url ?? '') }}', `{{ addslashes($lesson->content ?? '') }}`, {{ $lesson->order }})" class="text-xs font-medium text-gray-500 hover:text-gray-900 ml-2">Edit</button>
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                No students enrolled yet.
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
                    <input type="checkbox" name="is_published" id="quiz_is_published" class="rounded text-primary focus:ring-primary mr-2">
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
<div id="editCourseModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Course Settings</h3>
            <button onclick="document.getElementById('editCourseModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.courses.update', $course) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="space-y-4 max-h-[70vh] overflow-y-auto pr-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ $course->title }}" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                    <textarea name="description" rows="3" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">{{ $course->description }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Duration</label>
                        <input type="text" name="duration" value="{{ $course->duration }}" placeholder="e.g. 10 Hours" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Price ($)</label>
                        <input type="number" step="0.01" name="price" value="{{ $course->price }}" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cover Image (Optional)</label>
                    @if($course->cover_image)
                        <div class="mb-2">
                            <img src="{{ Storage::url($course->cover_image) }}" alt="Cover" class="w-32 h-20 object-cover rounded">
                        </div>
                    @endif
                    <input type="file" name="cover_image" accept="image/*" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
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
