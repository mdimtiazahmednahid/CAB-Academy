<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use Illuminate\Http\Request;

class InstructorController extends Controller
{
    /**
     * Display a listing of instructors in the admin panel.
     */
    public function index()
    {
        \App\Support\DatabaseSchemaRepair::repairInstructorCourseLinks();

        $instructors = Instructor::with('courses')
            ->orderBy('order', 'asc')
            ->latest('id')
            ->paginate(15);

        return view('admin.instructors.index', compact('instructors'));
    }

    /**
     * Show the form for creating a new instructor.
     */
    public function create()
    {
        $courses = \App\Models\Course::orderBy('id')->get();
        return view('admin.instructors.create', compact('courses'));
    }

    /**
     * Store a newly created instructor in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'expertise' => 'required|string|max:255',
            'avatar' => 'nullable|string|max:500',
            'avatar_file' => 'nullable|image|max:10240',
            'avatar_base64' => 'nullable|string',
            'bio' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'password' => 'nullable|string|min:6',
            'course_ids' => 'nullable|array',
            'course_ids.*' => 'integer|exists:courses,id',
            'sync_all_courses' => 'nullable|boolean',
        ]);

        $initialPassword = $request->input('password');
        unset($validated['password'], $validated['course_ids'], $validated['sync_all_courses']);

        @mkdir(public_path('uploads/instructors'), 0775, true);
        @mkdir(public_path('storage/instructors'), 0775, true);

        if ($request->hasFile('avatar_file')) {
            $path = $request->file('avatar_file')->store('instructors', 'public');
            try {
                if (file_exists(public_path('uploads/' . $path))) {
                    @copy(public_path('uploads/' . $path), public_path('storage/' . $path));
                }
            } catch (\Throwable $e) {}
            $validated['avatar'] = $path;
        } elseif ($request->filled('avatar_base64')) {
            $data = $request->input('avatar_base64');
            if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                $data = substr($data, strpos($data, ',') + 1);
                $type = strtolower($type[1]);
                $data = base64_decode($data);
                if ($data !== false) {
                    $filename = 'instructor_' . time() . '_' . uniqid() . '.' . $type;
                    \Illuminate\Support\Facades\Storage::disk('public')->put('instructors/' . $filename, $data);
                    @file_put_contents(public_path('storage/instructors/' . $filename), $data);
                    $validated['avatar'] = 'instructors/' . $filename;
                }
            }
        }
        unset($validated['avatar_base64'], $validated['avatar_file']);

        $validated['order'] = $request->input('order', 0);
        $validated['is_active'] = $request->has('is_active');

        // Check if an instructor with this email or linked user already exists to prevent conflicting duplicates
        $existingInstructor = null;
        if (!empty($validated['email'])) {
            $existingInstructor = Instructor::where('email', $validated['email'])->first();
        }
        if (!$existingInstructor && !empty($validated['email'])) {
            $existingUser = \App\Models\User::where('email', $validated['email'])->first();
            if ($existingUser) {
                $existingInstructor = Instructor::where('user_id', $existingUser->id)->first();
            }
        }

        if ($existingInstructor) {
            $existingInstructor->update($validated);
            $instructor = $existingInstructor;
        } else {
            $instructor = Instructor::create($validated);
        }

        // Keep linked user record in sync safely
        $user = null;
        if (!empty($instructor->email)) {
            $userWithEmail = \App\Models\User::where('email', $instructor->email)->first();
            if ($userWithEmail) {
                $instructor->user_id = $userWithEmail->id;
                $instructor->saveQuietly();
                $user = $userWithEmail;
            }
        }

        if (!$user && $instructor->user_id) {
            $user = \App\Models\User::find($instructor->user_id);
        }

        if ($user) {
            $user->name = $instructor->name;
            if (!empty($instructor->email) && $user->email !== $instructor->email) {
                $otherUser = \App\Models\User::where('email', $instructor->email)->where('id', '!=', $user->id)->first();
                if (!$otherUser) {
                    $user->email = $instructor->email;
                }
            }
            if ($instructor->avatar) {
                $user->profile_picture = $instructor->avatar;
            }
            if (!empty($instructor->phone)) {
                $user->phone = $instructor->phone;
            }
            if (!empty($initialPassword)) {
                $user->password = bcrypt($initialPassword);
            }
            if ($user->role !== 'admin') {
                $user->role = 'instructor';
            }
            try {
                $user->saveQuietly();
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Could not sync user profile in InstructorController@store: " . $e->getMessage());
            }
        }

        if ($instructor->user_id) {
            if ($request->boolean('sync_all_courses')) {
                \App\Models\Course::query()->update(['instructor_id' => $instructor->user_id]);
            } elseif ($request->filled('course_ids')) {
                \App\Models\Course::whereIn('id', (array) $request->input('course_ids'))->update(['instructor_id' => $instructor->user_id]);
            }
        }

        return redirect()->route('admin.instructors.index')->with('success', 'Instructor profile created and synced across courses successfully.');
    }

    /**
     * Show the form for editing the specified instructor.
     */
    public function edit(Instructor $instructor)
    {
        \App\Support\DatabaseSchemaRepair::repairInstructorCourseLinks();
        $courses = \App\Models\Course::orderBy('id')->get();
        return view('admin.instructors.edit', compact('instructor', 'courses'));
    }

    /**
     * Update the specified instructor in storage.
     */
    public function update(Request $request, Instructor $instructor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'expertise' => 'required|string|max:255',
            'avatar' => 'nullable|string|max:500',
            'avatar_file' => 'nullable|image|max:10240',
            'avatar_base64' => 'nullable|string',
            'bio' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'password' => 'nullable|string|min:6',
            'course_ids' => 'nullable|array',
            'course_ids.*' => 'integer|exists:courses,id',
            'sync_all_courses' => 'nullable|boolean',
            'remove_avatar' => 'nullable|boolean',
        ]);

        @mkdir(public_path('uploads/instructors'), 0775, true);
        @mkdir(public_path('storage/instructors'), 0775, true);

        if ($request->hasFile('avatar_file')) {
            $path = $request->file('avatar_file')->store('instructors', 'public');
            try {
                if (file_exists(public_path('uploads/' . $path))) {
                    @copy(public_path('uploads/' . $path), public_path('storage/' . $path));
                }
            } catch (\Throwable $e) {}
            $validated['avatar'] = $path;
        } elseif ($request->filled('avatar_base64')) {
            $data = $request->input('avatar_base64');
            if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                $data = substr($data, strpos($data, ',') + 1);
                $type = strtolower($type[1]);
                $data = base64_decode($data);
                if ($data !== false) {
                    $filename = 'instructor_' . time() . '_' . uniqid() . '.' . $type;
                    \Illuminate\Support\Facades\Storage::disk('public')->put('instructors/' . $filename, $data);
                    @file_put_contents(public_path('storage/instructors/' . $filename), $data);
                    $validated['avatar'] = 'instructors/' . $filename;
                }
            }
        } elseif ($request->boolean('remove_avatar')) {
            $validated['avatar'] = null;
        } elseif (!$request->filled('avatar') && $instructor->avatar) {
            // Preserve existing uploaded avatar if URL input was left blank
            $validated['avatar'] = $instructor->avatar;
        }
        unset($validated['avatar_base64'], $validated['avatar_file'], $validated['course_ids'], $validated['sync_all_courses'], $validated['remove_avatar']);

        $newPassword = $request->input('password');
        unset($validated['password']);

        $validated['order'] = $request->input('order', 0);
        $validated['is_active'] = $request->has('is_active');

        $instructor->update($validated);
        $instructor->refresh();

        // Keep linked user record in sync safely
        $user = null;
        if (!empty($instructor->email)) {
            $userWithEmail = \App\Models\User::where('email', $instructor->email)->first();
            if ($userWithEmail && (!$instructor->user_id || $instructor->user_id !== $userWithEmail->id)) {
                $instructor->user_id = $userWithEmail->id;
                $instructor->saveQuietly();
                $user = $userWithEmail;
            }
        }

        if (!$user && $instructor->user_id) {
            $user = \App\Models\User::find($instructor->user_id);
        }

        if ($user) {
            $user->name = $instructor->name;
            if (!empty($instructor->email) && $user->email !== $instructor->email) {
                $otherUser = \App\Models\User::where('email', $instructor->email)->where('id', '!=', $user->id)->first();
                if (!$otherUser) {
                    $user->email = $instructor->email;
                }
            }
            if ($instructor->avatar) {
                $user->profile_picture = $instructor->avatar;
            } elseif ($request->boolean('remove_avatar')) {
                $user->profile_picture = null;
            }
            if (!empty($instructor->phone)) {
                $user->phone = $instructor->phone;
            }
            if (!empty($newPassword)) {
                $user->password = bcrypt($newPassword);
            }
            try {
                $user->saveQuietly();
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Could not sync user profile in InstructorController@update: " . $e->getMessage());
            }
        }

        if ($instructor->user_id) {
            if ($request->boolean('sync_all_courses')) {
                \App\Models\Course::query()->update(['instructor_id' => $instructor->user_id]);
            } elseif ($request->has('course_ids')) {
                \App\Models\Course::whereIn('id', (array) $request->input('course_ids', []))->update(['instructor_id' => $instructor->user_id]);
            }
        }

        return redirect()->route('admin.instructors.index')->with('success', 'Instructor profile updated and synced across all pages successfully.');
    }

    /**
     * Remove the specified instructor from the frontend and database.
     */
    public function destroy(Instructor $instructor)
    {
        if ($instructor->user_id) {
            \App\Models\Course::where('instructor_id', $instructor->user_id)->update(['instructor_id' => null]);
            $user = \App\Models\User::find($instructor->user_id);
            if ($user && $user->role === 'instructor') {
                $user->role = 'student';
                $user->saveQuietly();
            }
        }
        $instructor->delete();

        return redirect()->route('admin.instructors.index')->with('success', 'Instructor profile removed successfully.');
    }
}
