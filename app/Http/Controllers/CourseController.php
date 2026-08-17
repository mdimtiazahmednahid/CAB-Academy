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
        
        $course->load(['sections' => function($q) {
            $q->orderBy('order');
        }, 'sections.lessons' => function($q) {
            $q->orderBy('order');
        }, 'quizzes']);
        
        $completedLessonIds = $request->user()->completedLessons()->pluck('lesson_id')->toArray();
        $isEnrolled = $request->user()->enrolledCourses()->where('course_id', $course->id)->exists();

        return view('courses.show', compact('course', 'completedLessonIds', 'isEnrolled'));
    }

    public function enroll(Request $request, Course $course)
    {
        abort_if(!$course->is_published, 404);
        
        $user = $request->user();
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
        DB::table('lesson_user')->updateOrInsert(
            ['user_id' => auth()->id(), 'lesson_id' => $lesson->id],
            ['completed_at' => now()]
        );

        if ($request->next_lesson_url) {
            return redirect($request->next_lesson_url);
        }

        return back()->with('success', 'Lesson completed');
    }
}
