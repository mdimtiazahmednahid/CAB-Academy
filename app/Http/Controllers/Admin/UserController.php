<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }
        
        $users = $query->latest()->paginate(15);
        
        return view('admin.users.index', compact('users'));
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,instructor,student',
            'profile_picture' => 'nullable|image|max:5120',
            'cover_photo' => 'nullable|image|max:10240',
        ]);

        $data['password'] = bcrypt($data['password']);

        $user = new User();
        $user->fill($data);

        if ($request->hasFile('profile_picture')) {
            $user->profile_picture = $request->file('profile_picture')->store('profiles', 'public');
        }

        if ($request->hasFile('cover_photo')) {
            $user->cover_photo = $request->file('cover_photo')->store('covers', 'public');
        }

        $user->save();

        return redirect()->route('admin.users')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load(['completedLessons', 'quizAttempts.quiz.course']);
        
        $totalQuizzes = $user->quizAttempts->count();
        $averageScore = 0;
        
        if ($totalQuizzes > 0) {
            $totalPoints = $user->quizAttempts->sum('score');
            $maxPossible = $user->quizAttempts->sum('total_questions');
            $averageScore = $maxPossible > 0 ? round(($totalPoints / $maxPossible) * 100) : 0;
        }

        return view('admin.users.show', compact('user', 'totalQuizzes', 'averageScore'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:admin,instructor,student',
            'profile_picture' => 'nullable|image|max:5120',
            'profile_picture_base64' => 'nullable|string',
            'cover_photo' => 'nullable|image|max:10240',
            'cover_photo_base64' => 'nullable|string',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        if ($request->hasFile('profile_picture')) {
            $user->profile_picture = $request->file('profile_picture')->store('profiles', 'public');
            unset($data['profile_picture']);
        } elseif ($request->filled('profile_picture_base64')) {
            $dataBase64 = $request->input('profile_picture_base64');
            if (preg_match('/^data:image\/(\w+);base64,/', $dataBase64, $type)) {
                $dataBase64 = substr($dataBase64, strpos($dataBase64, ',') + 1);
                $type = strtolower($type[1]);
                $decoded = base64_decode($dataBase64);
                if ($decoded !== false) {
                    $filename = 'profile_' . time() . '_' . uniqid() . '.' . $type;
                    \Illuminate\Support\Facades\Storage::disk('public')->put('profiles/' . $filename, $decoded);
                    $user->profile_picture = 'profiles/' . $filename;
                }
            }
        }
        unset($data['profile_picture_base64']);

        if ($request->hasFile('cover_photo')) {
            $user->cover_photo = $request->file('cover_photo')->store('covers', 'public');
            unset($data['cover_photo']);
        } elseif ($request->filled('cover_photo_base64')) {
            $dataBase64 = $request->input('cover_photo_base64');
            if (preg_match('/^data:image\/(\w+);base64,/', $dataBase64, $type)) {
                $dataBase64 = substr($dataBase64, strpos($dataBase64, ',') + 1);
                $type = strtolower($type[1]);
                $decoded = base64_decode($dataBase64);
                if ($decoded !== false) {
                    $filename = 'cover_' . time() . '_' . uniqid() . '.' . $type;
                    \Illuminate\Support\Facades\Storage::disk('public')->put('covers/' . $filename, $decoded);
                    $user->cover_photo = 'covers/' . $filename;
                }
            }
        }
        unset($data['cover_photo_base64']);

        $user->fill($data);
        $user->save();

        // Keep linked instructor profile in sync with updated user details
        if ($instructor = \App\Models\Instructor::where('user_id', $user->id)->first()) {
            $instructor->name = $user->name;
            if ($user->profile_picture) {
                $instructor->avatar = $user->profile_picture;
            }
            $instructor->save();
        }

        return back()->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot delete yourself.']);
        }

        // Dissociate instructor profile if linked
        if ($instructor = \App\Models\Instructor::where('user_id', $user->id)->first()) {
            $instructor->user_id = null;
            $instructor->save();
        }

        // Detach enrollments safely
        $user->enrolledCourses()->detach();

        // Nullify instructor foreign key on authored courses
        \App\Models\Course::where('instructor_id', $user->id)->update(['instructor_id' => null]);
        
        $user->delete();
        
        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
    }

    public function updatePreferences(Request $request, User $user)
    {
        $validated = $request->validate([
            'level' => 'nullable|string',
            'subjects' => 'nullable|string',
            'goals' => 'nullable|string',
        ]);

        $subjects = array_filter(array_map('trim', explode(',', $validated['subjects'] ?? '')));
        $goals = array_filter(array_map('trim', explode(',', $validated['goals'] ?? '')));

        $user->preferences = [
            'level' => $validated['level'] ?? '',
            'subjects' => $subjects,
            'goals' => $goals,
        ];
        $user->save();

        return back()->with('success', 'User preferences updated successfully.');
    }
}
