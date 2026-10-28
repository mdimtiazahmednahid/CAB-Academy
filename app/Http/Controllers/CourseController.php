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
            'sections' => function($q) {
                $q->orderBy('order');
            },
            'sections.lessons' => function($q) {
                $q->orderBy('order');
            },
            'quizzes'
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

        return response()->view('courses.share_preview', compact('course'));
    }

    public function enroll(Request $request, Course $course)
    {
        abort_if(!$course->is_published, 404);
        
        if (!auth()->check()) {
            session()->put('url.intended', route('courses.show', $course));
            return redirect()->route('login')->with('info', 'Please sign in or create an account to enroll in this course.');
        }

        $user = $request->user();

        if ($course->price > 0) {
            return redirect()->route('courses.checkout', $course);
        }

        if (!$user->enrolledCourses()->where('course_id', $course->id)->exists()) {
            $user->enrolledCourses()->attach($course->id);
        }
        
        return back()->with('success', 'You have successfully enrolled in the course!');
    }

    public function showLesson(Course $course, Lesson $lesson)
    {
        abort_if(!$course->is_published, 404);
        
        $isCompleted = DB::table('lesson_user')
            ->where('user_id', auth()->id())
            ->where('lesson_id', $lesson->id)
            ->exists();

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

        return view('lessons.show', compact('course', 'lesson', 'isCompleted', 'nextLesson'));
    }

    public function completeLesson(Request $request, Course $course, Lesson $lesson)
    {
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

        if ($request->next_lesson_url) {
            return redirect($request->next_lesson_url);
        }

        return back()->with('success', 'Lesson completed' . (!$wasCompleted ? ' and you earned 10 XP!' : ''));
    }
}
