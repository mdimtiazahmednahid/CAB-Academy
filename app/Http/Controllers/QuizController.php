<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function show(Course $course, Quiz $quiz)
    {
        abort_if(!$quiz->is_published || (int) $quiz->course_id !== (int) $course->id, 404);

        $user = auth()->user();
        if (!$user->isAdmin() && $course->instructor_id !== $user->id && !$user->enrolledCourses()->where('course_id', $course->id)->exists()) {
            return redirect()->route('courses.show', $course)->with('error', 'Please enroll in this course to take its quizzes.');
        }
        
        $quiz->load('questions.options');
        return view('quizzes.show', compact('course', 'quiz'));
    }

    public function store(Request $request, Course $course, Quiz $quiz)
    {
        abort_if(!$quiz->is_published || (int) $quiz->course_id !== (int) $course->id, 404);

        $user = $request->user();
        if (!$user->isAdmin() && $course->instructor_id !== $user->id && !$user->enrolledCourses()->where('course_id', $course->id)->exists()) {
            return redirect()->route('courses.show', $course)->with('error', 'Please enroll in this course to submit quizzes.');
        }

        $quiz->load('questions.options');
        
        $score = 0;
        $total = $quiz->questions->count();
        
        foreach ($quiz->questions as $question) {
            $selectedOptionId = $request->input('question_' . $question->id);
            $correctOption = $question->options->where('is_correct', true)->first();
            
            if ($correctOption && $correctOption->id == $selectedOptionId) {
                $score++;
            }
        }
        
        $alreadyAttempted = QuizAttempt::where('user_id', $user->id)->where('quiz_id', $quiz->id)->exists();

        QuizAttempt::create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'score' => $score,
            'total_questions' => $total,
        ]);

        // Gamification: Award 10 XP base + 5 XP per correct answer on first completion to prevent infinite XP farming
        $xpEarned = 0;
        if (!$alreadyAttempted) {
            $xpEarned = 10 + ($score * 5);
            $user->awardXp($xpEarned);
        }

        return redirect()->route('performance.index')->with('success', "Quiz completed! You scored {$score} out of {$total}" . ($xpEarned > 0 ? " and earned {$xpEarned} XP." : "."));
    }
}
