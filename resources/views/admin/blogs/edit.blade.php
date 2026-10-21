@extends('layouts.admin')

@section('content')
<div class="p-6 md:p-8 space-y-6 max-w-4xl mx-auto">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.blogs.index') }}" class="text-xs font-bold text-gray-500 hover:text-gray-900 flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Articles
        </a>
    </div>

    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-gray-100 shadow-sm">
        <h1 class="text-2xl font-black text-gray-900 mb-6">Edit Article: {{ $blog->title }}</h1>

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-xs space-y-1">
                @foreach($errors->all() as $error)
                    <div>&bull; {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('admin.blogs.update', $blog) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-2">Article Title *</label>
                    <input type="text" name="title" value="{{ old('title', $blog->title) }}" required class="w-full text-sm rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 p-3">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-2">Category *</label>
                    <input type="text" name="category" value="{{ old('category', $blog->category) }}" required class="w-full text-sm rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 p-3">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-gray-700 mb-2">Brief Summary / Excerpt *</label>
                <textarea name="excerpt" rows="2" required class="w-full text-sm rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 p-3">{{ old('excerpt', $blog->excerpt) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-gray-700 mb-2">Article Content (Markdown / HTML Supported) *</label>
                <textarea name="content" rows="12" required class="w-full text-sm rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 p-3 font-mono leading-relaxed">{{ old('content', $blog->content) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-2">Author Name *</label>
                    <input type="text" name="author_name" value="{{ old('author_name', $blog->author_name) }}" required class="w-full text-sm rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 p-3">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-2">Author Title</label>
                    <input type="text" name="author_title" value="{{ old('author_title', $blog->author_title) }}" class="w-full text-sm rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 p-3">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-2">Read Time</label>
                    <input type="text" name="read_time" value="{{ old('read_time', $blog->read_time) }}" class="w-full text-sm rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 p-3">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-gray-700 mb-2">Featured Image URL (Optional)</label>
                <input type="text" name="featured_image" value="{{ old('featured_image', $blog->featured_image) }}" class="w-full text-sm rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 p-3">
            </div>

            <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_published" value="1" {{ old('is_published', $blog->is_published) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-xs font-bold text-gray-700">Published to Public Knowledge Hub</span>
                </label>

                <button type="submit" class="px-6 py-3 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-xl transition-colors shadow-sm">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
