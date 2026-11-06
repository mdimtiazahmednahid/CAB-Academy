<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function show(Request $request, Course $course)
    {
        abort_if(!$course->is_published, 404);
        
        $course->increment('views');
        
        $course->load([
            'instructor',
            'instructorProfile',
            'materials',
            'sections' => function($q) {
                $q->orderBy('order');
            },
            'sections.lessons' => function($q) {
                $q->orderBy('order');
            },
            'quizzes.questions'
        ]);
        
        $user = $request->user();
        $completedLessonIds = $user ? $user->completedLessons()->pluck('lesson_id')->toArray() : [];
        $isEnrolled = $user ? $user->enrolledCourses()->where('course_id', $course->id)->exists() : false;

        return view('courses.show', compact('course', 'completedLessonIds', 'isEnrolled'));
    }

    /**
     * Social Short Link Resolver (/c/{course})
     * Serves high-fidelity Open Graph / Twitter Card preview for Facebook, WhatsApp, and social crawlers,
     * and seamlessly redirects human visitors to the full course page.
     */
    public function shortLink(Request $request, $identifier)
    {
        $course = null;
        if (is_numeric($identifier)) {
            $course = Course::where('is_published', true)->find($identifier);
        }
        if (!$course) {
            $course = Course::where('is_published', true)->where('id', $identifier)->first();
        }
        if (!$course) {
            $course = Course::where('is_published', true)->get()->first(function ($c) use ($identifier) {
                return \Illuminate\Support\Str::slug($c->title) === $identifier;
            });
        }

        abort_if(!$course, 404);

        if ($request->has('direct') || $request->has('redirect')) {
            return redirect()->route('courses.show', $course);
        }

        return response()
            ->view('courses.share_preview', compact('course'))
            ->header('Cache-Control', 'public, max-age=3600');
    }

    public function enroll(Request $request, Course $course)
    {
        abort_if(!$course->is_published, 404);

        if (!$course->isRegistrationOpen()) {
            return back()->with('error', 'Registration for this course is currently closed.');
        }
        
        if (!auth()->check()) {
            session()->put('url.intended', route('courses.show', $course));
            return redirect()->route('login')->with('info', 'Please sign in or create an account to enroll in this course.');
        }

        $user = $request->user();

        if (!$user->isAdmin() && $user->enrolledCourses()->where('courses.id', $course->id)->exists()) {
            return redirect()->route('courses.show', $course)->with('info', 'You are already enrolled in this course.');
        }

        if ($course->price > 0) {
            return redirect()->route('courses.checkout', $course);
        }

        DB::transaction(function () use ($user, $course) {
            $user->enrolledCourses()->syncWithoutDetaching([$course->id]);

            $phone = $user->effective_phone;
            $formData = array_merge($user->saved_enrollment_fields, [
                'name' => $user->name,
                'phone' => $phone,
                'email' => $user->email,
                'training_mode' => $course->isCampusOnly() ? 'in_person' : 'online_zoom',
            ]);

            \App\Models\Payment::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'amount' => 0.00,
                ],
                [
                    'payment_method_id' => null,
                    'sender_number' => $phone ?: ($user->last_sender_number ?: 'FREE'),
                    'transaction_id' => 'FREE-ENROLL-' . strtoupper(\Illuminate\Support\Str::random(6)),
                    'status' => 'verified',
                    'notes' => 'Mode: ' . ($course->isCampusOnly() ? 'Campus' : 'Online Zoom'),
                    'form_data' => $formData,
                    'verified_at' => now(),
                ]
            );
        });
        
        return back()->with('success', 'You have successfully enrolled in the course!');
    }

    public function showLesson(Course $course, Lesson $lesson)
    {
        abort_if(!$course->is_published, 404);
        abort_if(!$lesson->section || (int) $lesson->section->course_id !== (int) $course->id, 404);

        $user = auth()->user();
        if (!$user->isAdmin() && $course->instructor_id !== $user->id && !$user->enrolledCourses()->where('course_id', $course->id)->exists()) {
            return redirect()->route('courses.show', $course)->with('error', 'Please enroll in this course to access its lessons.');
        }
        
        $course->load([
            'materials',
            'sections' => function ($q) {
                $q->orderBy('order');
            },
            'sections.lessons' => function ($q) {
                $q->orderBy('order');
            },
            'quizzes.questions',
        ]);

        $completedLessonIds = $user ? $user->completedLessons()->pluck('lesson_id')->toArray() : [];
        $isCompleted = in_array($lesson->id, $completedLessonIds);

        $nextLesson = Lesson::where('section_id', $lesson->section_id)
            ->where('order', '>', $lesson->order)
            ->orderBy('order')
            ->first();

        if (!$nextLesson) {
            $nextSection = $course->sections()
                ->where('order', '>', $lesson->section->order)
                ->orderBy('order')
                ->first();
                
            if ($nextSection) {
                $nextLesson = $nextSection->lessons()->orderBy('order')->first();
            }
        }

        return view('lessons.show', compact('course', 'lesson', 'isCompleted', 'nextLesson', 'completedLessonIds'));
    }

    public function completeLesson(Request $request, Course $course, Lesson $lesson)
    {
        abort_if(!$lesson->section || (int) $lesson->section->course_id !== (int) $course->id, 404);

        $user = $request->user();
        if (!$user->isAdmin() && $course->instructor_id !== $user->id && !$user->enrolledCourses()->where('course_id', $course->id)->exists()) {
            return redirect()->route('courses.show', $course)->with('error', 'Please enroll in this course to complete lessons.');
        }

        $wasCompleted = DB::table('lesson_user')
            ->where('user_id', auth()->id())
            ->where('lesson_id', $lesson->id)
            ->exists();

        DB::table('lesson_user')->updateOrInsert(
            ['user_id' => auth()->id(), 'lesson_id' => $lesson->id],
            ['completed_at' => now()]
        );

        if (!$wasCompleted) {
            $request->user()->awardXp(10);
        }

        if ($request->filled('next_lesson_url')) {
            $nextUrl = (string) $request->next_lesson_url;
            if (str_starts_with($nextUrl, '/') || str_starts_with($nextUrl, url('/'))) {
                return redirect($nextUrl);
            }
        }

        return back()->with('success', 'Lesson completed' . (!$wasCompleted ? ' and you earned 10 XP!' : ''));
    }
}
