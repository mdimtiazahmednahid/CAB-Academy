<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\CommunityComment;
use App\Models\CommunityPost;
use App\Models\CommunityPostLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!\App\Models\Setting::isPageEnabled('feed') && (!auth()->check() || !auth()->user()->isAdmin()), 404);
        $filter = $request->query('filter', 'all');
        $category = $request->query('category');
        $search = $request->query('search');

        $query = CommunityPost::with(['user', 'blogPost', 'comments'])
            ->orderBy('is_pinned', 'desc')
            ->latest();

        if ($filter === 'blogs') {
            $query->whereNotNull('blog_post_id');
        } elseif ($filter === 'discussions') {
            $query->whereNull('blog_post_id');
        } elseif ($filter === 'my' && Auth::check()) {
            $query->where('user_id', Auth::id());
        }

        if ($category) {
            $query->where('category', $category);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('author_name', 'like', "%{$search}%");
            });
        }

        $posts = $query->paginate(15)->withQueryString();

        // Also fetch published featured blogs for carousel / quick links
        $featuredBlogs = BlogPost::published()->latest('published_at')->take(4)->get();

        $categories = [
            'General Discussion',
            'Export – Import',
            'Supply Chain Management',
            'Tax, VAT & Compliance',
            'Career Advice',
            'Case Studies & News',
        ];

        return view('feed.index', compact('posts', 'featuredBlogs', 'filter', 'category', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:5000',
            'category' => 'nullable|string|max:100',
            'title' => 'nullable|string|max:255',
            'image_url' => 'nullable|url|max:1000',
            'author_name' => 'nullable|string|max:100',
        ]);

        $user = Auth::user();
        $authorName = $user ? $user->name : ($request->input('author_name') ?: 'Community Member');
        $authorTitle = null;
        $authorAvatar = null;

        if ($user) {
            $authorTitle = match ($user->role) {
                'admin' => \App\Models\Setting::getVal('site_name', 'ACS Academy') . ' Director',
                'instructor' => 'Distinguished Faculty',
                default => 'Student Member',
            };
            $authorAvatar = $user->profile_picture;
            // Award gamification XP
            $user->awardXp(15);
        }

        $post = CommunityPost::create([
            'user_id' => $user?->id,
            'author_name' => $authorName,
            'author_title' => $authorTitle,
            'author_avatar' => $authorAvatar,
            'category' => $request->input('category', 'General Discussion'),
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'image_url' => $request->input('image_url'),
            'likes_count' => 0,
            'comments_count' => 0,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Post published successfully! (+15 XP)',
                'post' => $post,
            ]);
        }

        return redirect()->route('feed.index')->with('success', 'Your update has been posted to the Community Feed! (+15 XP)');
    }

    public function like(Request $request, CommunityPost $post)
    {
        $user = Auth::user();
        $ip = $request->ip();

        $likeQuery = CommunityPostLike::where('community_post_id', $post->id);

        if ($user) {
            $likeQuery->where('user_id', $user->id);
        } else {
            $likeQuery->where('ip_address', $ip);
        }

        $existingLike = $likeQuery->first();

        if ($existingLike) {
            $existingLike->delete();
            $post->decrement('likes_count');
            $liked = false;
        } else {
            CommunityPostLike::create([
                'community_post_id' => $post->id,
                'user_id' => $user?->id,
                'ip_address' => $ip,
            ]);
            $post->increment('likes_count');
            $liked = true;

            if ($user && $post->user_id !== $user->id) {
                $user->awardXp(2);
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'liked' => $liked,
                'likes_count' => (int) $post->fresh()->likes_count,
            ]);
        }

        return back();
    }

    public function comment(Request $request, CommunityPost $post)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
            'author_name' => 'nullable|string|max:100',
        ]);

        $user = Auth::user();
        $authorName = $user ? $user->name : ($request->input('author_name') ?: 'Fellow Learner');
        $authorAvatar = $user?->profile_picture;

        $comment = CommunityComment::create([
            'community_post_id' => $post->id,
            'user_id' => $user?->id,
            'author_name' => $authorName,
            'author_avatar' => $authorAvatar,
            'content' => $request->input('content'),
        ]);

        $post->increment('comments_count');

        if ($user) {
            $user->awardXp(5);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'comment' => $comment,
                'comments_count' => (int) $post->fresh()->comments_count,
            ]);
        }

        return back()->with('success', 'Comment posted! (+5 XP)');
    }

    public function destroy(CommunityPost $post)
    {
        if (Auth::check() && (Auth::id() === $post->user_id || Auth::user()->role === 'admin')) {
            $post->delete();
            return redirect()->route('feed.index')->with('success', 'Post removed.');
        }

        abort(403, 'Unauthorized action.');
    }
}
