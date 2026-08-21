<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Section;
use App\Models\Lesson;
use App\Models\User;
use App\Notifications\NewCourseNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class CourseController extends Controller
{
    public function index()
    {
        $query = Course::latest();
        if (auth()->user()->role === 'instructor') {
            $query->where('instructor_id', auth()->id());
        }
        $courses = $query->get();
        $instructors = \App\Models\User::whereIn('role', ['admin', 'instructor'])->get();
        return view('admin.courses.index', compact('courses', 'instructors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_published' => 'boolean',
            'duration' => 'nullable|string|max:100',
            'price' => 'nullable|numeric|min:0',
            'cover_image' => 'nullable|image|max:2048',
            'level' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'instructor_id' => 'nullable|exists:users,id'
        ]);
        
        $data['is_published'] = $request->has('is_published');
        $data['price'] = $data['price'] ?? 0.00;

        if (auth()->user()->role === 'instructor') {
            $data['instructor_id'] = auth()->id();
        }

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('course_covers', 'public');
        }

        $course = Course::create($data);

        if ($course->is_published) {
            $users = User::where(function($q) {
                $q->whereNull('notification_preferences')
                  ->orWhereJsonContains('notification_preferences->new_courses', true);
            })->get();
            if ($users->isNotEmpty()) {
                Notification::send($users, new NewCourseNotification($course));
            }
        }

        return back()->with('success', 'Course created successfully');
    }

    public function show(Course $course)
    {
        if (auth()->user()->role === 'instructor' && $course->instructor_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $course->load(['sections.lessons', 'students', 'payments.user', 'payments.paymentMethod']);
        return view('admin.courses.show', compact('course'));
    }

    public function storeSection(Request $request, Course $course)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'order' => 'integer'
        ]);

        $course->sections()->create($data);
        return back()->with('success', 'Section created successfully');
    }

    public function storeLesson(Request $request, Course $course, Section $section)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'video_url' => 'nullable|url',
            'content' => 'nullable|string',
            'order' => 'integer'
        ]);

        $section->lessons()->create($data);
        return back()->with('success', 'Lesson created successfully');
    }

    public function storeMaterial(Request $request, Course $course)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:pdf,link,zip',
            'external_link' => 'nullable|url|required_if:type,link',
            'file_path' => 'nullable|file|mimes:pdf,zip|max:10240|required_unless:type,link'
        ]);

        if ($request->hasFile('file_path')) {
            $data['file_path'] = $request->file('file_path')->store('materials', 'public');
        }

        $course->materials()->create($data);
        return back()->with('success', 'Material added successfully!');
    }

    public function update(Request $request, Course $course)
    {
        if (auth()->user()->role === 'instructor' && $course->instructor_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_published' => 'boolean',
            'duration' => 'nullable|string|max:100',
            'price' => 'nullable|numeric|min:0',
            'cover_image' => 'nullable|image|max:2048',
            'level' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'instructor_id' => 'nullable|exists:users,id'
        ]);

        $data['is_published'] = $request->has('is_published');
        $data['price'] = $data['price'] ?? 0.00;

        if (auth()->user()->role === 'instructor') {
            $data['instructor_id'] = auth()->id();
        }

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('course_covers', 'public');
        }

        $wasPublished = $course->is_published;
        
        $course->update($data);

        if (!$wasPublished && $course->is_published) {
            $users = User::where(function($q) {
                $q->whereNull('notification_preferences')
                  ->orWhereJsonContains('notification_preferences->new_courses', true);
            })->get();
            if ($users->isNotEmpty()) {
                Notification::send($users, new NewCourseNotification($course));
            }
        }

        return back()->with('success', 'Course updated successfully');
    }

    public function destroy(Course $course)
    {
        if (auth()->user()->role === 'instructor' && $course->instructor_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $course->delete();
        return redirect()->route('admin.courses')->with('success', 'Course deleted successfully');
    }

    public function updateSection(Request $request, Course $course, Section $section)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'order' => 'integer'
        ]);

        $section->update($data);
        return back()->with('success', 'Section updated successfully');
    }

    public function destroySection(Course $course, Section $section)
    {
        $section->delete();
        return back()->with('success', 'Section deleted successfully');
    }

    public function updateLesson(Request $request, Course $course, Section $section, Lesson $lesson)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'video_url' => 'nullable|url',
            'content' => 'nullable|string',
            'order' => 'integer'
        ]);

        $lesson->update($data);
        return back()->with('success', 'Lesson updated successfully');
    }

    public function destroyLesson(Course $course, Section $section, Lesson $lesson)
    {
        $lesson->delete();
        return back()->with('success', 'Lesson deleted successfully');
    }

    public function destroyMaterial(Course $course, \App\Models\Material $material)
    {
        $material->delete();
        return back()->with('success', 'Material deleted successfully');
    }
}
