<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
        $categories = Category::orderBy('name')->get();
        return view('admin.blogs.create', compact('categories'));
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
            'meta_description' => 'nullable|string|max:500',
            'content' => 'required|string',
            'author_name' => 'required|string|max:255',
            'author_title' => 'nullable|string|max:255',
            'read_time' => 'nullable|string|max:50',
            'featured_image' => 'nullable|string|max:500',
            'featured_image_file' => 'nullable|image|max:5120',
            'featured_image_base64' => 'nullable|string',
            'is_published' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ]);

        if ($request->hasFile('featured_image_file')) {
            $path = $request->file('featured_image_file')->store('blogs', 'public');
            $validated['featured_image'] = Storage::url($path);
        } elseif ($request->filled('featured_image_base64')) {
            $data = $request->input('featured_image_base64');
            if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                $data = substr($data, strpos($data, ',') + 1);
                $type = strtolower($type[1]);
                $data = base64_decode($data);
                if ($data !== false) {
                    $filename = 'blog_' . time() . '_' . uniqid() . '.' . $type;
                    Storage::disk('public')->put('blogs/' . $filename, $data);
                    if (is_dir(public_path('storage/blogs'))) {
                        @file_put_contents(public_path('storage/blogs/' . $filename), $data);
                    }
                    $validated['featured_image'] = 'blogs/' . $filename;
                }
            }
        }
        unset($validated['featured_image_base64'], $validated['featured_image_file']);

        $validated['slug'] = Str::slug($validated['title']) . '-' . rand(100, 999);
        $validated['is_published'] = $request->has('is_published');
        $validated['is_featured'] = $request->has('is_featured');
        $validated['published_at'] = $validated['is_published'] ? now() : null;

        BlogPost::create($validated);

        return redirect()->route('admin.blogs.index')->with('success', 'Article published successfully.');
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit(BlogPost $blog)
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.blogs.edit', compact('blog', 'categories'));
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
            'meta_description' => 'nullable|string|max:500',
            'content' => 'required|string',
            'author_name' => 'required|string|max:255',
            'author_title' => 'nullable|string|max:255',
            'read_time' => 'nullable|string|max:50',
            'featured_image' => 'nullable|string|max:500',
            'featured_image_file' => 'nullable|image|max:5120',
            'featured_image_base64' => 'nullable|string',
            'is_published' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ]);

        if ($request->hasFile('featured_image_file')) {
            $path = $request->file('featured_image_file')->store('blogs', 'public');
            $validated['featured_image'] = Storage::url($path);
        } elseif ($request->filled('featured_image_base64')) {
            $data = $request->input('featured_image_base64');
            if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                $data = substr($data, strpos($data, ',') + 1);
                $type = strtolower($type[1]);
                $data = base64_decode($data);
                if ($data !== false) {
                    $filename = 'blog_' . time() . '_' . uniqid() . '.' . $type;
                    Storage::disk('public')->put('blogs/' . $filename, $data);
                    if (is_dir(public_path('storage/blogs'))) {
                        @file_put_contents(public_path('storage/blogs/' . $filename), $data);
                    }
                    $validated['featured_image'] = 'blogs/' . $filename;
                }
            }
        }
        unset($validated['featured_image_base64'], $validated['featured_image_file']);

        $validated['is_published'] = $request->has('is_published');
        $validated['is_featured'] = $request->has('is_featured');

        if ($validated['is_published'] && !$blog->published_at) {
            $validated['published_at'] = now();
        }

        $blog->update($validated);

        return redirect()->route('admin.blogs.index')->with('success', 'Article updated successfully.');
    }

    /**
     * Toggle featured status for homepage showcase.
     */
    public function toggleFeatured(BlogPost $blog)
    {
        $blog->is_featured = !$blog->is_featured;
        $blog->save();

        $msg = $blog->is_featured ? 'Article featured on homepage.' : 'Article unfeatured from homepage.';
        return back()->with('success', $msg);
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
