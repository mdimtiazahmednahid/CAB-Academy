<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AdminAnnouncementNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class AnnouncementController extends Controller
{
    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'url' => 'nullable|url',
            'audience' => 'required|string|in:all,students,instructors,announcement_opt_in',
        ]);

        $query = User::query();

        if ($request->audience === 'students') {
            $query->where('role', 'student');
        } elseif ($request->audience === 'instructors') {
            $query->where('role', 'instructor');
        } elseif ($request->audience === 'announcement_opt_in') {
            $query->where(function($q) {
                $q->whereNull('notification_preferences')
                  ->orWhereJsonContains('notification_preferences->announcements', true);
            });
        }

        // To prevent timeout on shared hosting, limit chunk size or just get all
        // For InfinityFree, if the user base grows, this could be a bottleneck.
        // But for now, send synchronously.
        $users = $query->get();

        Notification::send($users, new AdminAnnouncementNotification(
            $request->title,
            $request->message,
            $request->url
        ));

        return redirect()->route('admin.announcements.create')
            ->with('success', 'Announcement sent to ' . $users->count() . ' users.');
    }
}
