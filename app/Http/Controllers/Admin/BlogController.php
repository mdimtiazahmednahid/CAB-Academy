<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * Display a listing of blog and knowledge posts.
     */
    public function index()
    {
        $posts = BlogPost::latest()->paginate(15);
        return view('admin.blogs.index', compact('posts'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create()
    {
        return view('admin.blogs.create');
    }

    /**
     * Store a newly created post in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'author_name' => 'required|string|max:255',
            'author_title' => 'nullable|string|max:255',
            'read_time' => 'nullable|string|max:50',
            'featured_image' => 'nullable|string|max:255',
            'is_published' => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . rand(100, 999);
        $validated['is_published'] = $request->has('is_published');
        $validated['published_at'] = $validated['is_published'] ? now() : null;

        BlogPost::create($validated);

        return redirect()->route('admin.blogs.index')->with('success', 'Article published successfully.');
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit(BlogPost $blog)
    {
        return view('admin.blogs.edit', compact('blog'));
    }

    /**
     * Update the specified post in storage.
     */
    public function update(Request $request, BlogPost $blog)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'author_name' => 'required|string|max:255',
            'author_title' => 'nullable|string|max:255',
            'read_time' => 'nullable|string|max:50',
            'featured_image' => 'nullable|string|max:255',
            'is_published' => 'nullable|boolean',
        ]);

        $validated['is_published'] = $request->has('is_published');
        if ($validated['is_published'] && !$blog->published_at) {
            $validated['published_at'] = now();
        }

        $blog->update($validated);

        return redirect()->route('admin.blogs.index')->with('success', 'Article updated successfully.');
    }

    /**
     * Remove the specified post from storage.
     */
    public function destroy(BlogPost $blog)
    {
        $blog->delete();

        return redirect()->route('admin.blogs.index')->with('success', 'Article deleted successfully.');
    }
}
