<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = User::where('role', 'student')->count();
        $publishedCourses = Course::where('is_published', true)->count();
        
        $totalAssessments = QuizAttempt::count();
        $totalScore = QuizAttempt::sum('score');
        $totalPossible = QuizAttempt::sum('total_questions');
        $averageScore = $totalPossible > 0 ? round(($totalScore / $totalPossible) * 100) : 0;
        
        $recentActivity = QuizAttempt::with('user', 'quiz')->latest()->take(5)->get();
        
        return view('admin.dashboard', compact(
            'totalStudents',
            'publishedCourses',
            'totalAssessments',
            'averageScore',
            'recentActivity'
        ));
    }
}
