@extends('layouts.admin')

@section('header_title', 'Community Feed Control')

@section('content')
<div class="space-y-8" x-data="{ broadcastModalOpen: false, activeCommentsPost: null }">

    <!-- Top Bar -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#051c45] text-white">
                    <i class="fas fa-users-line text-[#10b981] mr-1.5"></i> Community Moderation
                </span>
                <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Live Student Exchange</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Feed & Discussions Control</h1>
            <p class="text-sm text-gray-500 mt-1">Moderate student discussions, pin priority academic notices, broadcast updates, and purge spam.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('feed.index') }}" target="_blank" class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 text-xs font-bold shadow-sm inline-flex items-center gap-2 transition-all">
                <i class="fas fa-external-link-alt text-gray-400"></i>
                <span>View Public Feed</span>
            </a>
            <button @click="broadcastModalOpen = true" class="px-5 py-2.5 rounded-xl bg-[#c30102] hover:bg-[#a50102] text-white text-xs font-bold shadow-md shadow-red-900/20 inline-flex items-center gap-2 transition-all">
                <i class="fas fa-bullhorn"></i>
                <span>Broadcast Official Notice</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm font-semibold flex items-center gap-3 shadow-sm">
            <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Discussions</span>
                <div class="text-2xl font-black text-gray-900 mt-1">{{ number_format($stats['total_posts']) }}</div>
                <div class="text-[11px] text-gray-500 mt-0.5">Active Community Threads</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-[#051c45] flex items-center justify-center text-xl">
                <i class="fas fa-comments"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Pinned Announcements</span>
                <div class="text-2xl font-black text-amber-600 mt-1">{{ number_format($stats['pinned_posts']) }}</div>
                <div class="text-[11px] text-amber-700 mt-0.5">Top of Student Feeds</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                <i class="fas fa-thumbtack"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Replies & Q&A</span>
                <div class="text-2xl font-black text-emerald-700 mt-1">{{ number_format($stats['total_comments']) }}</div>
                <div class="text-[11px] text-emerald-600 mt-0.5">Peer Interaction Volume</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fas fa-reply-all"></i>
            </div>
        </div>
    </div>

    <!-- Feed Management Table -->
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
        
        <!-- Filter Bar -->
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-black text-gray-900 tracking-tight">Active Discussions & Publications</h2>
                <p class="text-xs text-gray-500 mt-0.5">Manage user posts, feature key gazette questions, or delete spam.</p>
            </div>

            <form method="GET" action="{{ route('admin.feeds.index') }}" class="flex flex-wrap items-center gap-2.5">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search author, topic, keywords..." 
                       class="text-xs px-3.5 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-1 focus:ring-[#051c45] w-full sm:w-60">

                <select name="category" class="text-xs px-3 py-2 rounded-xl border border-gray-300 bg-white focus:outline-none">
                    <option value="">All Categories</option>
                    <option value="customs_sro" {{ request('category') === 'customs_sro' ? 'selected' : '' }}>Customs & SRO</option>
                    <option value="export_import" {{ request('category') === 'export_import' ? 'selected' : '' }}>Export-Import</option>
                    <option value="vat_tax" {{ request('category') === 'vat_tax' ? 'selected' : '' }}>VAT & Tax</option>
                    <option value="supply_chain" {{ request('category') === 'supply_chain' ? 'selected' : '' }}>Supply Chain</option>
                    <option value="career_qna" {{ request('category') === 'career_qna' ? 'selected' : '' }}>Career Q&A</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                    Filter
                </button>

                @if(request()->anyFilled(['search', 'category']))
                    <a href="{{ route('admin.feeds.index') }}" class="px-3 py-2 text-xs font-bold text-gray-500 hover:text-gray-900">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600">
                <thead class="bg-gray-50 text-gray-700 uppercase tracking-wider text-[10px] font-bold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3.5">Author</th>
                        <th class="px-6 py-3.5">Discussion Content</th>
                        <th class="px-6 py-3.5">Category</th>
                        <th class="px-6 py-3.5 text-center">Engagement</th>
                        <th class="px-6 py-3.5 text-center">Pin Status</th>
                        <th class="px-6 py-3.5 text-right">Moderation Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($posts as $post)
                        <tr class="hover:bg-gray-50/80 transition-colors {{ $post->is_pinned ? 'bg-amber-50/30' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <img src="{{ storage_asset($post->author_avatar) ?: (\App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png')) }}" alt="{{ $post->author_name }}" class="w-9 h-9 rounded-full object-cover border border-gray-200" onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}';">
                                    <div>
                                        <div class="font-bold text-gray-900 flex items-center gap-1.5">
                                            <span>{{ $post->author_name }}</span>
                                            @if($post->user_id && $post->user && $post->user->role === 'admin')
                                                <span class="px-1.5 py-0.2 rounded bg-[#051c45] text-white text-[9px] font-extrabold uppercase">Admin</span>
                                            @endif
                                        </div>
                                        <div class="text-[10px] text-gray-400">{{ $post->author_title ?? 'Member' }}</div>
                                        <div class="text-[9px] text-gray-400 font-mono">{{ $post->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 max-w-md">
                                @if($post->title)
                                    <div class="font-black text-gray-900 mb-0.5 text-sm">{{ $post->title }}</div>
                                @endif
                                <p class="text-xs text-gray-600 line-clamp-2 leading-relaxed">{{ $post->content }}</p>
                                @if($post->image_url)
                                    <div class="mt-2">
                                        <a href="{{ $post->image_url }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-blue-600 font-bold hover:underline">
                                            <i class="far fa-image"></i> View Attachment
                                        </a>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-700">
                                    {{ str_replace('_', ' ', $post->category ?? 'General') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-3 text-xs font-mono font-bold text-gray-600">
                                    <span title="Likes"><i class="fas fa-heart text-red-500 mr-1"></i> {{ $post->likes_count }}</span>
                                    <span title="Comments"><i class="fas fa-comment text-blue-500 mr-1"></i> {{ $post->comments_count }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                @if($post->is_pinned)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300">
                                        <i class="fas fa-thumbtack text-amber-700 mr-1"></i> Pinned Top
                                    </span>
                                @else
                                    <span class="text-[11px] text-gray-400">Regular</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap space-x-1.5">
                                <!-- Pin / Unpin Action -->
                                <form action="{{ route('admin.feeds.pin', $post) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-2.5 py-1.5 rounded-lg border text-xs font-bold transition-all {{ $post->is_pinned ? 'bg-amber-50 border-amber-300 text-amber-800 hover:bg-amber-100' : 'bg-gray-50 border-gray-200 text-gray-600 hover:bg-gray-100' }}" title="{{ $post->is_pinned ? 'Unpin from Top' : 'Pin to Top' }}">
                                        <i class="fas fa-thumbtack {{ $post->is_pinned ? 'rotate-45' : '' }}"></i>
                                        <span>{{ $post->is_pinned ? 'Unpin' : 'Pin' }}</span>
                                    </button>
                                </form>

                                <!-- Delete Post Action -->
                                <form action="{{ route('admin.feeds.destroy', $post) }}" method="POST" onsubmit="return confirm('Purge this discussion thread and its comments?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 border border-red-200 text-red-600 text-xs font-bold transition-all" title="Delete Post">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                No community discussions found.
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

    <!-- Modal: Broadcast Official Announcement to Feed -->
    <div x-show="broadcastModalOpen" style="display:none;" class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="broadcastModalOpen = false" class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 shadow-2xl border border-gray-100 space-y-6">
            <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                <div>
                    <h3 class="text-lg font-black text-gray-900">Broadcast Official Announcement</h3>
                    <p class="text-xs text-gray-500">Post an official notification to the top of the student community feed.</p>
                </div>
                <button @click="broadcastModalOpen = false" class="text-gray-400 hover:text-gray-700">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <form action="{{ route('admin.feeds.broadcast') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Announcement Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Bangladesh Customs Gazette SRO 2026: Executive Masterclass Schedule" class="w-full text-xs rounded-xl border-gray-300 p-3 focus:ring-[#051c45]">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Notice Category *</label>
                        <select name="category" required class="w-full text-xs rounded-xl border-gray-300 p-2.5 focus:ring-[#051c45]">
                            <option value="customs_sro">Customs & SRO Updates</option>
                            <option value="export_import">Export-Import Advisory</option>
                            <option value="vat_tax">VAT & Corporate Tax</option>
                            <option value="supply_chain">Supply Chain & Logistics</option>
                            <option value="career_qna">Executive Placement & Career</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Official Sub-Title</label>
                        <input type="text" name="author_title" value="Academic Directorate" class="w-full text-xs rounded-xl border-gray-300 p-2.5 focus:ring-[#051c45]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Broadcast Content *</label>
                    <textarea name="content" rows="4" required placeholder="Full message to be displayed to students and industry participants..." class="w-full text-xs rounded-xl border-gray-300 p-3 focus:ring-[#051c45]"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Attach Banner / Circular Image (Optional)</label>
                    <input type="file" name="image_file" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-gray-700 hover:file:bg-slate-200">
                </div>

                <div class="pt-2">
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-gray-700">
                        <input type="checkbox" name="is_pinned" value="1" checked class="rounded border-gray-300 text-[#051c45] focus:ring-[#051c45]">
                        <span>Pin to the very top of Student Feed</span>
                    </label>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2.5">
                    <button type="button" @click="broadcastModalOpen = false" class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-700 text-xs font-bold">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-[#c30102] hover:bg-[#a50102] text-white text-xs font-bold rounded-xl shadow-md">
                        Publish & Pin Broadcast
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
