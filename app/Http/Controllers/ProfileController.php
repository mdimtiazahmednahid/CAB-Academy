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
        if (array_key_exists('email', $validated) && empty($validated['email'])) {
            unset($validated['email']);
        }
        
        $user->fill($validated);

        @mkdir(public_path('uploads/profiles'), 0775, true);
        @mkdir(public_path('storage/profiles'), 0775, true);
        @mkdir(public_path('uploads/covers'), 0775, true);
        @mkdir(public_path('storage/covers'), 0775, true);

        if ($request->hasFile('profile_picture')) {
            $user->profile_picture = $request->file('profile_picture')->store('profiles', 'public');
            if (file_exists(public_path('uploads/' . $user->profile_picture))) {
                @copy(public_path('uploads/' . $user->profile_picture), public_path('storage/' . $user->profile_picture));
            }
        } elseif ($request->filled('profile_picture_base64')) {
            $data = $request->input('profile_picture_base64');
            if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                $data = substr($data, strpos($data, ',') + 1);
                $type = strtolower($type[1]);
                $data = base64_decode($data);
                if ($data !== false) {
                    $filename = 'profile_' . time() . '_' . uniqid() . '.' . $type;
                    \Illuminate\Support\Facades\Storage::disk('public')->put('profiles/' . $filename, $data);
                    @file_put_contents(public_path('storage/profiles/' . $filename), $data);
                    $user->profile_picture = 'profiles/' . $filename;
                }
            }
        }

        if ($request->hasFile('cover_photo')) {
            $user->cover_photo = $request->file('cover_photo')->store('covers', 'public');
            if (file_exists(public_path('uploads/' . $user->cover_photo))) {
                @copy(public_path('uploads/' . $user->cover_photo), public_path('storage/' . $user->cover_photo));
            }
        } elseif ($request->filled('cover_photo_base64')) {
            $data = $request->input('cover_photo_base64');
            if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                $data = substr($data, strpos($data, ',') + 1);
                $type = strtolower($type[1]);
                $data = base64_decode($data);
                if ($data !== false) {
                    $filename = 'cover_' . time() . '_' . uniqid() . '.' . $type;
                    \Illuminate\Support\Facades\Storage::disk('public')->put('covers/' . $filename, $data);
                    @file_put_contents(public_path('storage/covers/' . $filename), $data);
                    $user->cover_photo = 'covers/' . $filename;
                }
            }
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->updated_at = now();
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
