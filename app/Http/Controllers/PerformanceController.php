<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    public function index(Request $request)
    {
        $attempts = $request->user()->quizAttempts()->with('quiz.course')->latest()->get();
        
        $totalQuizzes = $attempts->count();
        $averageScore = 0;
        
        if ($totalQuizzes > 0) {
            $totalPoints = $attempts->sum('score');
            $maxPossible = $attempts->sum('total_questions');
            $averageScore = $maxPossible > 0 ? round(($totalPoints / $maxPossible) * 100) : 0;
        }

        return view('performance.index', compact('attempts', 'totalQuizzes', 'averageScore'));
    }
}
