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
        $instructors = Instructor::orderBy('order', 'asc')
            ->latest('id')
            ->paginate(15);

        return view('admin.instructors.index', compact('instructors'));
    }

    /**
     * Show the form for creating a new instructor.
     */
    public function create()
    {
        return view('admin.instructors.create');
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
            'avatar_file' => 'nullable|image|max:5120',
            'avatar_base64' => 'nullable|string',
            'bio' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('avatar_file')) {
            $path = $request->file('avatar_file')->store('instructors', 'public');
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
                    if (is_dir(public_path('storage/instructors'))) {
                        @file_put_contents(public_path('storage/instructors/' . $filename), $data);
                    }
                    $validated['avatar'] = 'instructors/' . $filename;
                }
            }
        }
        unset($validated['avatar_base64'], $validated['avatar_file']);

        $validated['order'] = $request->input('order', 0);
        $validated['is_active'] = $request->has('is_active');

        $instructor = Instructor::create($validated);

        // Keep linked user record in sync
        $user = null;
        if ($instructor->user_id) {
            $user = \App\Models\User::find($instructor->user_id);
        } elseif (!empty($instructor->email)) {
            $user = \App\Models\User::where('email', $instructor->email)->first();
            if ($user) {
                $instructor->user_id = $user->id;
                $instructor->saveQuietly();
            }
        }
        if ($user) {
            $user->name = $instructor->name;
            if ($instructor->avatar) {
                $user->profile_picture = $instructor->avatar;
            }
            $user->save();
        }

        return redirect()->route('admin.instructors.index')->with('success', 'Instructor profile created successfully.');
    }

    /**
     * Show the form for editing the specified instructor.
     */
    public function edit(Instructor $instructor)
    {
        return view('admin.instructors.edit', compact('instructor'));
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
            'avatar_file' => 'nullable|image|max:5120',
            'avatar_base64' => 'nullable|string',
            'bio' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('avatar_file')) {
            $path = $request->file('avatar_file')->store('instructors', 'public');
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
                    if (is_dir(public_path('storage/instructors'))) {
                        @file_put_contents(public_path('storage/instructors/' . $filename), $data);
                    }
                    $validated['avatar'] = 'instructors/' . $filename;
                }
            }
        }
        unset($validated['avatar_base64'], $validated['avatar_file']);

        $validated['order'] = $request->input('order', 0);
        $validated['is_active'] = $request->has('is_active');

        $instructor->update($validated);

        // Keep linked user record in sync
        $user = null;
        if ($instructor->user_id) {
            $user = \App\Models\User::find($instructor->user_id);
        } elseif (!empty($instructor->email)) {
            $user = \App\Models\User::where('email', $instructor->email)->first();
            if ($user) {
                $instructor->user_id = $user->id;
                $instructor->saveQuietly();
            }
        }
        if ($user) {
            $user->name = $instructor->name;
            if ($instructor->avatar) {
                $user->profile_picture = $instructor->avatar;
            }
            $user->save();
        }

        return redirect()->route('admin.instructors.index')->with('success', 'Instructor profile updated successfully.');
    }

    /**
     * Remove the specified instructor from the frontend and database.
     */
    public function destroy(Instructor $instructor)
    {
        $instructor->delete();

        return redirect()->route('admin.instructors.index')->with('success', 'Instructor profile removed successfully.');
    }
}
