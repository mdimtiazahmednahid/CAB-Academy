<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        \App\Support\DatabaseSchemaRepair::ensureSchema();
        $categories = Category::with('headInstructor')->withCount('courses')->ordered()->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        \App\Support\DatabaseSchemaRepair::ensureSchema();
        $instructors = \App\Models\Instructor::where('is_active', true)->orderBy('order')->get();
        return view('admin.categories.create', compact('instructors'));
    }

    public function store(Request $request)
    {
        \App\Support\DatabaseSchemaRepair::ensureSchema();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'icon' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'subcategories_text' => 'nullable|string',
            'head_instructor_id' => 'nullable|integer',
            'image' => 'nullable|string|max:500',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $data['icon'] = $data['icon'] ?: 'fa-tag';
        $data['order'] = $data['order'] ?? 0;
        $data['is_active'] = $request->has('is_active');

        $subLines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) ($data['subcategories_text'] ?? '')))));
        unset($data['subcategories_text']);
        $data['subcategories'] = $subLines;

        $category = Category::create($data);

        if (!empty($category->head_instructor_id)) {
            \App\Models\Instructor::where('id', $category->head_instructor_id)->update([
                'department' => $category->name,
                'department_role' => 'head',
            ]);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Learning Department / Category created successfully!');
    }

    public function edit(Category $category)
    {
        \App\Support\DatabaseSchemaRepair::ensureSchema();
        $instructors = \App\Models\Instructor::where('is_active', true)->orderBy('order')->get();
        return view('admin.categories.edit', compact('category', 'instructors'));
    }

    public function update(Request $request, Category $category)
    {
        \App\Support\DatabaseSchemaRepair::ensureSchema();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
            'icon' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'subcategories_text' => 'nullable|string',
            'head_instructor_id' => 'nullable|integer',
            'image' => 'nullable|string|max:500',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $data['icon'] = $data['icon'] ?: 'fa-tag';
        $data['order'] = $data['order'] ?? 0;
        $data['is_active'] = $request->has('is_active');

        $subLines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) ($data['subcategories_text'] ?? '')))));
        unset($data['subcategories_text']);
        $data['subcategories'] = $subLines;

        $oldName = $category->name;
        $category->update($data);

        // Propagate category name change to all corresponding courses and instructors
        if ($oldName !== $category->name) {
            \App\Models\Course::where('category', $oldName)->update(['category' => $category->name]);
            \App\Models\Instructor::where('department', $oldName)->update(['department' => $category->name]);
        }

        if (!empty($category->head_instructor_id)) {
            \App\Models\Instructor::where('id', $category->head_instructor_id)->update([
                'department' => $category->name,
                'department_role' => 'head',
            ]);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Learning Department / Category updated successfully!');
    }

    public function destroy(Category $category)
    {
        // Unlink deleted category from corresponding courses
        \App\Models\Course::where('category', $category->name)->update(['category' => null]);
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category removed successfully!');
    }
}
