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
        abort_if(!$quiz->is_published, 404);
        
        $quiz->load('questions.options');
        return view('quizzes.show', compact('course', 'quiz'));
    }

    public function store(Request $request, Course $course, Quiz $quiz)
    {
        $quiz->load('questions.options');
        $user = $request->user();
        
        $score = 0;
        $total = $quiz->questions->count();
        
        foreach ($quiz->questions as $question) {
            $selectedOptionId = $request->input('question_' . $question->id);
            $correctOption = $question->options->where('is_correct', true)->first();
            
            if ($correctOption && $correctOption->id == $selectedOptionId) {
                $score++;
            }
        }
        
        $attempt = QuizAttempt::create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'score' => $score,
            'total_questions' => $total,
        ]);

        // Gamification: Award 10 XP base + 5 XP per correct answer
        $xpEarned = 10 + ($score * 5);
        $user->awardXp($xpEarned);

        return redirect()->route('performance.index')->with('success', "Quiz completed! You scored {$score} out of {$total} and earned {$xpEarned} XP.");
    }
}
