@extends('layouts.admin')

@section('header_title', 'Articles & Knowledge Hub')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#051c45] text-white">
                    <i class="fas fa-book-open text-[#10b981] mr-1.5"></i> Knowledge Base
                </span>
                <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Executive Research</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Articles & Knowledge Hub</h1>
            <p class="text-sm text-gray-500 mt-1">Publish and manage trade briefings, customs gazette updates, and faculty research.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('blog.index') }}" target="_blank" class="px-4 py-2.5 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 text-xs font-bold rounded-xl transition-all shadow-sm inline-flex items-center gap-2">
                <i class="fas fa-external-link-alt text-gray-400"></i>
                <span>View Public Hub</span>
            </a>
            <a href="{{ route('admin.blogs.create') }}" class="px-5 py-2.5 bg-[#051c45] hover:bg-[#072559] text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-blue-950/20 inline-flex items-center gap-2">
                <i class="fas fa-pen-nib text-[#10b981]"></i>
                <span>Write New Article</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm font-semibold flex items-center gap-3 shadow-sm">
            <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600">
                <thead class="text-[10px] uppercase bg-gray-50 text-gray-700 border-b border-gray-100 font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Title & Excerpt</th>
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4">Author</th>
                        <th class="px-6 py-4 text-center">Reads</th>
                        <th class="px-6 py-4 text-center">Featured</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($posts as $post)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-6 py-4 max-w-sm">
                                <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="font-bold text-gray-900 hover:text-blue-700 transition-colors block text-sm leading-tight">
                                    {{ $post->title }}
                                </a>
                                <p class="text-[11px] text-gray-400 mt-1 line-clamp-1">{{ $post->excerpt }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-800 rounded-lg text-[10px] font-bold uppercase tracking-wider">
                                    {{ $post->category }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-gray-900">{{ $post->author_name }}</div>
                                <div class="text-[10px] text-gray-400">{{ $post->author_title ?? 'Faculty Fellow' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center font-mono font-bold text-gray-600 whitespace-nowrap">
                                <i class="far fa-eye text-gray-400 mr-1"></i> {{ number_format($post->views_count ?? 0) }}
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <form action="{{ route('admin.blogs.feature', $post) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-bold transition-all {{ $post->is_featured ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-gray-100 text-gray-400 hover:bg-gray-200' }}" title="{{ $post->is_featured ? 'Unfeature from Homepage' : 'Feature on Homepage' }}">
                                        <i class="fas fa-star text-xs {{ $post->is_featured ? 'text-amber-500' : 'text-gray-400' }} mr-1"></i>
                                        <span>{{ $post->is_featured ? 'Featured' : 'Regular' }}</span>
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                @if($post->is_published)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        Published
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600">
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap space-x-1.5">
                                <a href="{{ route('admin.blogs.edit', $post) }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg text-xs font-bold transition-all">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.blogs.destroy', $post) }}" class="inline" onsubmit="return confirm('Delete this article?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-xs font-bold transition-all">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                No articles created yet. Click "Write New Article" to post your first insight.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($posts->hasPages())
            <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
