@extends('layouts.admin')

@section('content')
<div class="p-6 md:p-8 space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Articles & Knowledge Hub</h1>
            <p class="text-sm text-gray-500 mt-1">Publish and manage trade briefings, customs gazette updates, and faculty insights.</p>
        </div>
        <div>
            <a href="{{ route('admin.blogs.create') }}" class="px-5 py-2.5 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-xl transition-colors shadow-sm inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Write New Article
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="text-xs uppercase bg-gray-50 text-gray-700 border-b border-gray-100 font-bold">
                    <tr>
                        <th class="px-6 py-4">Title & Excerpt</th>
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4">Author</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($posts as $post)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-6 py-4 max-w-md">
                                <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="font-bold text-gray-900 hover:text-indigo-600 transition-colors block text-base leading-tight">
                                    {{ $post->title }}
                                </a>
                                <p class="text-xs text-gray-500 mt-1 line-clamp-1">{{ $post->excerpt }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 bg-gray-100 text-gray-700 rounded-lg text-xs font-semibold">
                                    {{ $post->category }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <div class="font-bold text-gray-800">{{ $post->author_name }}</div>
                                <div class="text-gray-400">{{ $post->author_title ?? 'Faculty Member' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($post->is_published)
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Published</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600">Draft</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('admin.blogs.edit', $post) }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-bold transition-colors">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.blogs.destroy', $post) }}" class="inline" onsubmit="return confirm('Delete this article?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-xs font-bold transition-colors">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                No articles created yet. Click "Write New Article" to post your first insight.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($posts->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
