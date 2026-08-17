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
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-xs">
            <tr>
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
                <td class="px-6 py-4 font-medium text-gray-900">{{ $c->title }}</td>
                @if(auth()->user()->role === 'admin')
                    <td class="px-6 py-4 text-gray-500">
                        {{ $c->instructor ? $c->instructor->name : 'N/A' }}
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
                <td class="px-6 py-4 text-gray-500">{{ $c->created_at->format('M d, Y') }}</td>
                <td class="px-6 py-4 text-right">
                    <a href="{{ route('admin.courses.show', $c) }}" class="text-primary hover:underline font-medium">Manage →</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-12 text-center text-gray-500">No courses yet. Create one to get started!</td>
            </tr>
            @endforelse
        </tbody>
    </table>
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
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Duration</label>
                        <input type="text" name="duration" placeholder="e.g. 10 Hours" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Price ($)</label>
                        <input type="number" step="0.01" name="price" value="0.00" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                    </div>
                </div>
                @if(auth()->user()->role === 'admin')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Instructor (Optional)</label>
                    <select name="instructor_id" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                        <option value="">-- Assign Instructor --</option>
                        @foreach($instructors as $instructor)
                            <option value="{{ $instructor->id }}">{{ $instructor->name }} ({{ ucfirst($instructor->role) }})</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cover Image (Optional)</label>
                    <input type="file" name="cover_image" accept="image/*" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                </div>
                <div class="flex items-center pt-2">
                    <input type="checkbox" name="is_published" id="is_published" class="rounded text-primary focus:ring-primary mr-2">
                    <label for="is_published" class="text-sm font-medium text-gray-700">Publish immediately</label>
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-primary text-white rounded-lg font-medium hover:opacity-90 mt-2">Create Course</button>
            </div>
        </form>
    </div>
</div>
@endsection
