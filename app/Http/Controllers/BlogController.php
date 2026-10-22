<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of blog articles.
     */
    public function index(Request $request)
    {
        $query = BlogPost::published()->latest('published_at');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $posts = $query->paginate(9)->withQueryString();
        $categories = BlogPost::published()->distinct()->pluck('category');

        return view('blog.index', compact('posts', 'categories'));
    }

    /**
     * Display a single blog article.
     */
    public function show(BlogPost $post)
    {
        if (!$post->is_published) {
            abort(404);
        }

        $post->incrementViews();

        $relatedPosts = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->where('category', $post->category)
            ->take(3)
            ->get();

        if ($relatedPosts->isEmpty()) {
            $relatedPosts = BlogPost::published()->where('id', '!=', $post->id)->take(3)->get();
        }

        return view('blog.show', compact('post', 'relatedPosts'));
    }
}
