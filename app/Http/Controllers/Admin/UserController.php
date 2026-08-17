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

        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = $request->file('profile_picture')->store('profiles', 'public');
        }

        if ($request->hasFile('cover_photo')) {
            $data['cover_photo'] = $request->file('cover_photo')->store('covers', 'public');
        }

        User::create($data);

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
            'cover_photo' => 'nullable|image|max:10240',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = $request->file('profile_picture')->store('profiles', 'public');
        }

        if ($request->hasFile('cover_photo')) {
            $data['cover_photo'] = $request->file('cover_photo')->store('covers', 'public');
        }

        $user->update($data);

        return back()->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot delete yourself.']);
        }
        
        $user->delete();
        
        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
    }
}
