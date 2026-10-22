<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();
        
        // Handle file uploads separately
        unset($validated['profile_picture'], $validated['cover_photo'], $validated['profile_picture_base64'], $validated['cover_photo_base64']);
        
        $user->fill($validated);

        if ($request->hasFile('profile_picture')) {
            $user->profile_picture = $request->file('profile_picture')->store('profiles', 'public');
        } elseif ($request->filled('profile_picture_base64')) {
            $data = $request->input('profile_picture_base64');
            if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                $data = substr($data, strpos($data, ',') + 1);
                $type = strtolower($type[1]);
                $data = base64_decode($data);
                if ($data !== false) {
                    $filename = 'profile_' . time() . '_' . uniqid() . '.' . $type;
                    \Illuminate\Support\Facades\Storage::disk('public')->put('profiles/' . $filename, $data);
                    $user->profile_picture = 'profiles/' . $filename;
                }
            }
        }

        if ($request->hasFile('cover_photo')) {
            $user->cover_photo = $request->file('cover_photo')->store('covers', 'public');
        } elseif ($request->filled('cover_photo_base64')) {
            $data = $request->input('cover_photo_base64');
            if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                $data = substr($data, strpos($data, ',') + 1);
                $type = strtolower($type[1]);
                $data = base64_decode($data);
                if ($data !== false) {
                    $filename = 'cover_' . time() . '_' . uniqid() . '.' . $type;
                    \Illuminate\Support\Facades\Storage::disk('public')->put('covers/' . $filename, $data);
                    $user->cover_photo = 'covers/' . $filename;
                }
            }
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function updateNotifications(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        $preferences = [
            'new_courses' => $request->has('notify_new_courses'),
            'new_jobs' => $request->has('notify_new_jobs'),
            'announcements' => $request->has('notify_announcements'),
        ];
        
        $user->notification_preferences = $preferences;
        $user->save();
        
        return Redirect::route('profile.edit')->with('status', 'notifications-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
