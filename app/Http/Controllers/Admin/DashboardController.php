<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\ContactInquiry;
use App\Models\Course;
use App\Models\FinancialExpense;
use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\Payment;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        \App\Support\DatabaseSchemaRepair::ensureSchema();

        $totalStudents = User::where('role', 'student')->count();
        $publishedCourses = Course::where('is_published', true)->count();
        $draftCourses = Course::where('is_published', false)->count();

        $totalAssessments = QuizAttempt::count();
        $totalScore = QuizAttempt::sum('score');
        $totalPossible = QuizAttempt::sum('total_questions');
        $averageScore = $totalPossible > 0 ? round(($totalScore / $totalPossible) * 100) : 0;

        $recentActivity = QuizAttempt::with(['user', 'quiz.course'])->latest()->take(5)->get();

        // Financial & Operational Intelligence
        $totalRealizedRevenue = (float) Payment::where('status', 'verified')->sum('amount');
        $monthlyRevenue = (float) Payment::where('status', 'verified')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $hasExpensesTable = Schema::hasTable('financial_expenses');
        $totalExpenses = $hasExpensesTable ? (float) FinancialExpense::sum('amount') : 0.0;
        $monthlyExpenses = $hasExpensesTable ? (float) FinancialExpense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount') : 0.0;
        $netOperatingSurplus = $totalRealizedRevenue - $totalExpenses;

        // 6-Month Revenue & Enrollment Velocity Trend
        $monthlyTrend = [];
        $maxMonthlyRevenue = 1.0;
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = now()->subMonths($i);
            $mRev = (float) Payment::where('status', 'verified')
                ->whereYear('created_at', $monthDate->year)
                ->whereMonth('created_at', $monthDate->month)
                ->sum('amount');
            $mExp = $hasExpensesTable ? (float) FinancialExpense::whereYear('expense_date', $monthDate->year)
                ->whereMonth('expense_date', $monthDate->month)
                ->sum('amount') : 0.0;
            $mPayments = Payment::whereYear('created_at', $monthDate->year)
                ->whereMonth('created_at', $monthDate->month)
                ->count();
            if ($mRev > $maxMonthlyRevenue) {
                $maxMonthlyRevenue = $mRev;
            }
            $monthlyTrend[] = [
                'label' => $monthDate->format('M Y'),
                'short_label' => $monthDate->format('M'),
                'revenue' => $mRev,
                'expenses' => $mExp,
                'enrollments' => $mPayments,
            ];
        }

        $pendingPaymentsCount = Payment::where('status', 'pending')->count();
        $pendingReceivablesAmount = (float) Payment::where('status', 'pending')->sum('amount');
        $pendingPayments = Payment::with(['user', 'course' => fn ($q) => $q->withTrashed(), 'paymentMethod'])
            ->where('status', 'pending')
            ->latest()
            ->take(6)
            ->get();

        $pendingInquiriesCount = ContactInquiry::where('status', 'pending')->count();
        $recentInquiries = ContactInquiry::latest()->take(4)->get();

        $totalEnrollments = DB::table('course_user')->count();
        $totalCertificates = Certificate::count();

        $activeJobsCount = JobPost::where('is_active', true)->count();
        $pendingJobApplicationsCount = JobApplication::where('status', 'pending')->count();
        $recentJobApplications = JobApplication::with(['user', 'jobPost' => fn ($q) => $q->withTrashed()])
            ->latest()
            ->take(5)
            ->get();

        $recentStudents = User::where('role', 'student')
            ->withCount('enrolledCourses')
            ->latest()
            ->take(5)
            ->get();

        $topCourses = Course::with(['instructorProfile', 'instructor'])
            ->withCount(['students', 'sections', 'lessons'])
            ->withSum(['payments as realized_revenue' => fn ($q) => $q->where('status', 'verified')], 'amount')
            ->orderByDesc('students_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalStudents',
            'publishedCourses',
            'draftCourses',
            'totalAssessments',
            'averageScore',
            'recentActivity',
            'totalRealizedRevenue',
            'monthlyRevenue',
            'totalExpenses',
            'monthlyExpenses',
            'netOperatingSurplus',
            'monthlyTrend',
            'maxMonthlyRevenue',
            'pendingPaymentsCount',
            'pendingReceivablesAmount',
            'pendingPayments',
            'pendingInquiriesCount',
            'recentInquiries',
            'totalEnrollments',
            'totalCertificates',
            'activeJobsCount',
            'pendingJobApplicationsCount',
            'recentJobApplications',
            'recentStudents',
            'topCourses'
        ));
    }
}
