<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityComment;
use App\Models\CommunityPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FeedController extends Controller
{
    /**
     * Display community posts and moderation control center.
     */
    public function index(Request $request)
    {
        $stats = [
            'total_posts' => CommunityPost::count(),
            'pinned_posts' => CommunityPost::where('is_pinned', true)->count(),
            'total_comments' => CommunityComment::count(),
        ];

        $query = CommunityPost::with(['user', 'comments.user'])->withCount(['comments', 'likes'])->latest();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('author_name', 'like', "%{$search}%");
            });
        }

        $posts = $query->paginate(15)->withQueryString();

        return view('admin.feeds.index', compact('posts', 'stats'));
    }

    /**
     * Toggle pinned status of a feed post.
     */
    public function togglePin(CommunityPost $post)
    {
        $post->is_pinned = !$post->is_pinned;
        $post->save();

        $status = $post->is_pinned ? 'pinned to the top of' : 'unpinned from';
        return back()->with('success', "Post has been {$status} the community feed.");
    }

    /**
     * Publish an official executive announcement or broadcast to the feed.
     */
    public function storeBroadcast(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string|max:100',
            'author_title' => 'nullable|string|max:100',
            'image_url' => 'nullable|string|max:500',
            'image_file' => 'nullable|image|max:5120',
            'is_pinned' => 'nullable|boolean',
        ]);

        $imageUrl = $validated['image_url'] ?? null;
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('community', 'public');
            $imageUrl = Storage::url($path);
        }

        CommunityPost::create([
            'user_id' => auth()->id(),
            'author_name' => 'CAB Academy Directorate',
            'author_title' => $validated['author_title'] ?? 'Official Academic Notice',
            'author_avatar' => '/images/logo.png',
            'category' => $validated['category'],
            'title' => $validated['title'],
            'content' => $validated['content'],
            'image_url' => $imageUrl,
            'is_pinned' => $request->has('is_pinned') ? true : true,
            'likes_count' => 0,
            'comments_count' => 0,
        ]);

        return redirect()->route('admin.feeds.index')->with('success', 'Official Academy announcement published to the community feed.');
    }

    /**
     * Remove an inappropriate post from the community feed.
     */
    public function destroy(CommunityPost $post)
    {
        $post->comments()->delete();
        $post->delete();

        return back()->with('success', 'Post and associated comments removed from community feed.');
    }

    /**
     * Remove an individual spam or inappropriate comment.
     */
    public function destroyComment(CommunityComment $comment)
    {
        $post = $comment->post;
        $comment->delete();

        if ($post) {
            $post->decrement('comments_count');
        }

        return back()->with('success', 'Comment deleted successfully.');
    }
}
