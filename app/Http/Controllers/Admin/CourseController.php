<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Section;
use App\Models\Lesson;
use App\Models\User;
use App\Models\Certificate;
use App\Notifications\NewCourseNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function index()
    {
        \App\Support\DatabaseSchemaRepair::repairInstructorCourseLinks();
        $query = Course::with(['instructorProfile', 'instructor'])
            ->withCount(['students', 'sections', 'lessons', 'quizzes', 'certificates'])
            ->latest();
        if (auth()->user()->role === 'instructor' && !auth()->user()->isAdmin()) {
            $query->where('instructor_id', auth()->id());
        }
        $courses = $query->get();
        $instructors = \App\Models\User::whereIn('role', ['admin', 'instructor'])->with('instructorProfile')->orderBy('name')->get();
        return view('admin.courses.index', compact('courses', 'instructors'));
    }

    public function store(Request $request)
    {
        if ($request->filled('instructor_id') && !\App\Models\User::where('id', $request->input('instructor_id'))->exists()) {
            if ($inst = \App\Models\Instructor::find($request->input('instructor_id'))) {
                $inst->save();
                $request->merge(['instructor_id' => $inst->user_id]);
            }
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'is_published' => 'boolean',
            'duration' => 'nullable|string|max:100',
            'start_date' => 'nullable|date',
            'venue' => 'nullable|string|max:255',
            'target_audience' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'cover_image' => 'nullable|image|max:10240',
            'cover_image_base64' => 'nullable|string',
            'level' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'instructor_id' => 'nullable|exists:users,id',
            'certificate_enabled' => 'nullable|boolean',
            'batch' => 'nullable|string|max:100',
            'show_batch' => 'nullable|boolean',
            'enrollment_fields' => 'nullable',
            'enrollment_fields_json' => 'nullable|string',
            'enrollment_template' => 'nullable|string',
        ]);
        
        $data['is_published'] = $request->has('is_published');
        $data['certificate_enabled'] = $request->boolean('certificate_enabled');
        $data['show_batch'] = $request->boolean('show_batch');
        $data['batch'] = $request->input('batch');
        $data['price'] = $data['price'] ?? 0.00;

        // Process dynamic enrollment form fields if provided
        $data['enrollment_fields'] = $this->parseEnrollmentFields($request);

        if (auth()->user()->role === 'instructor') {
            $data['instructor_id'] = auth()->id();
        }

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('course_covers', 'public');
            try {
                if (is_dir(public_path('storage/course_covers')) && file_exists(public_path('uploads/' . $data['cover_image']))) {
                    @copy(public_path('uploads/' . $data['cover_image']), public_path('storage/' . $data['cover_image']));
                }
            } catch (\Throwable $e) {}
        } elseif ($request->filled('cover_image_base64')) {
            $base64 = $request->input('cover_image_base64');
            if (preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
                $base64 = substr($base64, strpos($base64, ',') + 1);
                $type = strtolower($type[1]);
                $decoded = base64_decode($base64);
                if ($decoded !== false) {
                    $filename = 'course_' . time() . '_' . uniqid() . '.' . $type;
                    Storage::disk('public')->put('course_covers/' . $filename, $decoded);
                    if (is_dir(public_path('storage/course_covers'))) {
                        @file_put_contents(public_path('storage/course_covers/' . $filename), $decoded);
                    }
                    $data['cover_image'] = 'course_covers/' . $filename;
                }
            }
        }
        unset($data['cover_image_base64']);

        $course = Course::create($data);

        if ($course->is_published) {
            try {
                $users = User::all()->filter(function ($u) {
                    $prefs = is_array($u->notification_preferences) ? $u->notification_preferences : null;
                    return $prefs === null || ($prefs['new_courses'] ?? true) === true;
                });
                if ($users->isNotEmpty()) {
                    Notification::send($users, new NewCourseNotification($course));
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Course publish notification skipped: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Course created successfully');
    }

    public function show(Course $course)
    {
        if (!auth()->user()->isAdmin() && auth()->user()->role === 'instructor' && $course->instructor_id && $course->instructor_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        \App\Support\DatabaseSchemaRepair::repairInstructorCourseLinks();
        $course->load([
            'instructorProfile',
            'instructor',
            'sections.lessons',
            'quizzes.questions',
            'materials',
            'students',
            'payments.user',
            'payments.paymentMethod',
            'certificates'
        ]);
        $instructors = \App\Models\User::whereIn('role', ['admin', 'instructor'])->with('instructorProfile')->orderBy('name')->get();
        return view('admin.courses.show', compact('course', 'instructors'));
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
        if (!auth()->user()->isAdmin() && auth()->user()->role === 'instructor' && $course->instructor_id && $course->instructor_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        if ($request->filled('instructor_id') && !\App\Models\User::where('id', $request->input('instructor_id'))->exists()) {
            if ($inst = \App\Models\Instructor::find($request->input('instructor_id'))) {
                $inst->save();
                $request->merge(['instructor_id' => $inst->user_id]);
            }
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'is_published' => 'boolean',
            'duration' => 'nullable|string|max:100',
            'start_date' => 'nullable|date',
            'venue' => 'nullable|string|max:255',
            'target_audience' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'cover_image' => 'nullable|image|max:10240',
            'cover_image_base64' => 'nullable|string',
            'level' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'instructor_id' => 'nullable|exists:users,id',
            'certificate_enabled' => 'nullable|boolean',
            'batch' => 'nullable|string|max:100',
            'show_batch' => 'nullable|boolean',
            'enrollment_fields' => 'nullable',
            'enrollment_fields_json' => 'nullable|string',
            'enrollment_template' => 'nullable|string',
        ]);

        $data['is_published'] = $request->has('is_published');
        $data['certificate_enabled'] = $request->boolean('certificate_enabled');
        $data['show_batch'] = $request->boolean('show_batch');
        $data['batch'] = $request->input('batch');
        $data['price'] = $data['price'] ?? 0.00;

        // Process dynamic enrollment form fields if provided in request
        if ($request->has('enrollment_fields') || $request->filled('enrollment_fields_json') || $request->filled('enrollment_template')) {
            $data['enrollment_fields'] = $this->parseEnrollmentFields($request);
        }

        if (auth()->user()->role === 'instructor' && !auth()->user()->isAdmin()) {
            $data['instructor_id'] = auth()->id();
        }

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('course_covers', 'public');
            try {
                if (is_dir(public_path('storage/course_covers')) && file_exists(public_path('uploads/' . $data['cover_image']))) {
                    @copy(public_path('uploads/' . $data['cover_image']), public_path('storage/' . $data['cover_image']));
                }
            } catch (\Throwable $e) {}
        } elseif ($request->filled('cover_image_base64')) {
            $base64 = $request->input('cover_image_base64');
            if (preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
                $base64 = substr($base64, strpos($base64, ',') + 1);
                $type = strtolower($type[1]);
                $decoded = base64_decode($base64);
                if ($decoded !== false) {
                    $filename = 'course_' . time() . '_' . uniqid() . '.' . $type;
                    Storage::disk('public')->put('course_covers/' . $filename, $decoded);
                    if (is_dir(public_path('storage/course_covers'))) {
                        @file_put_contents(public_path('storage/course_covers/' . $filename), $decoded);
                    }
                    $data['cover_image'] = 'course_covers/' . $filename;
                }
            }
        }
        unset($data['cover_image_base64']);

        $wasPublished = $course->is_published;
        
        $course->update($data);

        if (!$wasPublished && $course->is_published) {
            try {
                $users = User::all()->filter(function ($u) {
                    $prefs = is_array($u->notification_preferences) ? $u->notification_preferences : null;
                    return $prefs === null || ($prefs['new_courses'] ?? true) === true;
                });
                if ($users->isNotEmpty()) {
                    Notification::send($users, new NewCourseNotification($course));
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Course update notification skipped: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Course updated successfully');
    }

    public function destroy(Course $course)
    {
        if (!auth()->user()->isAdmin() && auth()->user()->role === 'instructor' && $course->instructor_id && $course->instructor_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $course->delete();
        return redirect()->route('admin.courses')->with('success', 'Course deleted successfully');
    }

    public function updateSection(Request $request, Course $course, Section $section)
    {
        abort_if((int) $section->course_id !== (int) $course->id, 404);
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'order' => 'integer'
        ]);

        $section->update($data);
        return back()->with('success', 'Section updated successfully');
    }

    public function destroySection(Course $course, Section $section)
    {
        abort_if((int) $section->course_id !== (int) $course->id, 404);
        $section->delete();
        return back()->with('success', 'Section deleted successfully');
    }

    public function updateLesson(Request $request, Course $course, Section $section, Lesson $lesson)
    {
        abort_if((int) $section->course_id !== (int) $course->id || (int) $lesson->section_id !== (int) $section->id, 404);
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
        abort_if((int) $section->course_id !== (int) $course->id || (int) $lesson->section_id !== (int) $section->id, 404);
        $lesson->delete();
        return back()->with('success', 'Lesson deleted successfully');
    }

    public function destroyMaterial(Course $course, \App\Models\Material $material)
    {
        abort_if((int) $material->course_id !== (int) $course->id, 404);
        $material->delete();
        return back()->with('success', 'Material deleted successfully');
    }

    public function issueCertificate(Course $course, User $student)
    {
        Certificate::generateFor($student, $course);
        return back()->with('success', "Official digital certificate issued for {$student->name}.");
    }

    public function revokeCertificate(Course $course, User $student)
    {
        Certificate::where('user_id', $student->id)->where('course_id', $course->id)->delete();
        return back()->with('success', "Certificate revoked for {$student->name}.");
    }

    /**
     * Dedicated action to update course dynamic enrollment form fields and presets.
     */
    public function updateEnrollmentForm(Request $request, Course $course)
    {
        if (!auth()->user()->isAdmin() && auth()->user()->role === 'instructor' && $course->instructor_id && $course->instructor_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $fields = $this->parseEnrollmentFields($request);
        $course->update([
            'enrollment_fields' => $fields
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Course registration form updated successfully.',
                'fields' => $course->fresh()->effective_enrollment_fields
            ]);
        }

        return back()->with('success', 'Course registration form updated successfully.');
    }

    /**
     * Parse and sanitize dynamic registration/enrollment form fields from request.
     */
    protected function parseEnrollmentFields(Request $request): ?array
    {
        $rawFields = null;

        if ($request->filled('enrollment_template')) {
            $tplKey = $request->input('enrollment_template');
            $templates = Course::getBuiltinTemplates();
            if (isset($templates[$tplKey])) {
                $rawFields = $templates[$tplKey]['fields'];
            }
        }

        if ($request->filled('enrollment_fields_json')) {
            $decoded = json_decode($request->input('enrollment_fields_json'), true);
            if (is_array($decoded)) {
                $rawFields = $decoded;
            }
        } elseif ($request->has('enrollment_fields')) {
            $input = $request->input('enrollment_fields');
            if (is_string($input)) {
                $decoded = json_decode($input, true);
                if (is_array($decoded)) {
                    $rawFields = $decoded;
                }
            } elseif (is_array($input)) {
                $rawFields = $input;
            }
        }

        if ($rawFields === null) {
            return null;
        }

        $sanitized = [];
        foreach ($rawFields as $field) {
            if (!is_array($field)) continue;
            $name = trim($field['name'] ?? '');
            if ($name === '') continue;

            $name = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $name));
            $label = trim($field['label'] ?? ucfirst(str_replace('_', ' ', $name)));
            $type = in_array($field['type'] ?? '', ['text', 'email', 'tel', 'select', 'textarea', 'number']) 
                ? $field['type'] 
                : 'text';

            $sanitized[] = [
                'name' => $name,
                'label' => $label,
                'type' => $type,
                'is_required' => !empty($field['is_required']),
                'is_enabled' => isset($field['is_enabled']) ? !empty($field['is_enabled']) : true,
                'is_system' => !empty($field['is_system']),
                'placeholder' => trim($field['placeholder'] ?? ''),
                'options' => $field['options'] ?? null,
            ];
        }

        return $sanitized;
    }
}

