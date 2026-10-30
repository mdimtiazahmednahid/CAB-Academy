<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Notifications\AdminAnnouncementNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class AnnouncementController extends Controller
{
    public function create()
    {
        $courses = Course::withCount('students')->orderBy('title')->get();
        return view('admin.announcements.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'url' => 'nullable|url',
            'audience' => 'required|string',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        $audience = $request->audience;
        $targetCourse = null;

        if (str_starts_with($audience, 'course:')) {
            $courseId = (int) substr($audience, 7);
            $targetCourse = Course::findOrFail($courseId);
            $audience = 'course';
        } elseif ($audience === 'course') {
            $request->validate(['course_id' => 'required|exists:courses,id']);
            $targetCourse = Course::findOrFail($request->course_id);
        } elseif (!in_array($audience, ['all', 'students', 'instructors', 'announcement_opt_in'], true)) {
            return back()->withErrors(['audience' => 'Invalid target audience selected.'])->withInput();
        }

        $query = User::query();

        if ($audience === 'course' && $targetCourse) {
            $query->whereHas('enrolledCourses', function ($q) use ($targetCourse) {
                $q->where('courses.id', $targetCourse->id);
            });
        } elseif ($audience === 'students') {
            $query->where('role', 'student');
        } elseif ($audience === 'instructors') {
            $query->where('role', 'instructor');
        } elseif ($audience === 'announcement_opt_in') {
            $query->where(function ($q) {
                $q->whereNull('notification_preferences')
                  ->orWhereJsonContains('notification_preferences->announcements', true);
            });
        }

        $users = $query->get();

        $notificationUrl = $request->url ?: ($targetCourse ? route('courses.show', $targetCourse) : null);

        Notification::send($users, new AdminAnnouncementNotification(
            $request->title,
            $request->message,
            $notificationUrl
        ));

        // If requested, also broadcast and pin to Community Feed (/feed)
        if ($request->has('post_to_feed')) {
            $siteName = \App\Models\Setting::getVal('site_name', 'CAB Academy');
            \App\Models\CommunityPost::create([
                'user_id' => auth()->id(),
                'author_name' => $siteName . ' Directorate',
                'author_title' => $targetCourse ? ('Course Notice • ' . $targetCourse->title) : 'Official Academic Notice',
                'author_avatar' => \App\Models\Setting::getVal('site_logo') ?: '/images/logo.png',
                'category' => 'Official Insights',
                'title' => $request->title,
                'content' => $request->message,
                'is_pinned' => true,
                'likes_count' => 0,
                'comments_count' => 0,
            ]);
        }

        $audienceLabel = $targetCourse
            ? ('enrolled students of "' . $targetCourse->title . '"')
            : 'users';

        return redirect()->route('admin.announcements.create')
            ->with('success', 'Announcement sent to ' . $users->count() . ' ' . $audienceLabel . ' and broadcasted across platform.');
    }
}
