<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\FinancialExpense;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccountingController extends Controller
{
    /**
     * Display the Executive Accounting, Revenue & Expense Ledger.
     */
    public function index(Request $request)
    {
        // 1. Core Financial KPI Aggregates
        $totalRealizedRevenue = Payment::where('status', 'verified')->sum('amount');
        $monthlyRealizedRevenue = Payment::where('status', 'verified')
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('amount');

        $pendingReceivables = Payment::where('status', 'pending')->sum('amount');
        $totalExpenses = FinancialExpense::sum('amount');
        $monthlyExpenses = FinancialExpense::whereBetween('expense_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
            ->sum('amount');

        $netCashFlow = $totalRealizedRevenue - $totalExpenses;
        $verifiedEnrollmentsCount = Payment::where('status', 'verified')->count();

        // 2. Revenue Breakdown by Payment Method
        $paymentMethodStats = DB::table('payments')
            ->leftJoin('payment_methods', 'payments.payment_method_id', '=', 'payment_methods.id')
            ->select(
                DB::raw("COALESCE(payment_methods.provider_name, 'Campus Cash / Direct') as provider"),
                DB::raw('COUNT(payments.id) as total_count'),
                DB::raw("SUM(CASE WHEN payments.status = 'verified' THEN payments.amount ELSE 0 END) as realized_amount")
            )
            ->groupBy('provider')
            ->get();

        // 3. Course-by-Course Profitability & Tuition Realization
        $courseRevenueStats = Course::withCount([
            'payments as verified_students_count' => function ($q) {
                $q->where('status', 'verified');
            }
        ])->get()->map(function ($course) {
            $realized = Payment::where('course_id', $course->id)->where('status', 'verified')->sum('amount');
            $course->realized_revenue = $realized;
            return $course;
        })->sortByDesc('realized_revenue');

        // 4. Filterable Ledger Query
        $query = Payment::with(['user', 'course', 'paymentMethod', 'verifier'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhere('sender_number', 'like', "%{$search}%")
                  ->orWhere('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $ledgerTransactions = $query->paginate(20)->withQueryString();

        // 5. Recent Institutional Expenses
        $recentExpenses = FinancialExpense::with('creator')->latest('expense_date')->take(10)->get();

        // Helper Datasets for Modals
        $allCourses = Course::where('is_published', true)->orderBy('title')->get();
        $allPaymentMethods = PaymentMethod::where('is_active', true)->get();

        return view('admin.accounting.index', compact(
            'totalRealizedRevenue',
            'monthlyRealizedRevenue',
            'pendingReceivables',
            'totalExpenses',
            'monthlyExpenses',
            'netCashFlow',
            'verifiedEnrollmentsCount',
            'paymentMethodStats',
            'courseRevenueStats',
            'ledgerTransactions',
            'recentExpenses',
            'allCourses',
            'allPaymentMethods'
        ));
    }

    /**
     * Record an offline tuition payment received at campus or via direct wire.
     */
    public function recordTuition(Request $request)
    {
        $validated = $request->validate([
            'student_name' => 'required|string|max:255',
            'student_email' => 'required|email|max:255',
            'student_phone' => 'nullable|string|max:50',
            'course_id' => 'required|exists:courses,id',
            'amount' => 'required|numeric|min:0',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'transaction_id' => 'nullable|string|max:100',
            'payment_type' => 'required|string|in:campus_cash,corporate_batch,bank_transfer,bkash_merchant',
            'notes' => 'nullable|string|max:500',
        ]);

        // Find or create student user
        $user = User::firstOrCreate(
            ['email' => $validated['student_email']],
            [
                'name' => $validated['student_name'],
                'password' => bcrypt(Str::random(12)),
                'role' => 'student',
                'phone' => $validated['student_phone'] ?? null,
                'email_verified_at' => now(),
            ]
        );

        $course = Course::findOrFail($validated['course_id']);
        $trxId = !empty($validated['transaction_id']) ? $validated['transaction_id'] : 'CAMPUS-' . strtoupper(Str::random(8));

        // Create Verified Payment Record
        $payment = Payment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'payment_method_id' => $validated['payment_method_id'] ?? null,
            'amount' => $validated['amount'],
            'sender_number' => $validated['student_phone'] ?? 'Campus Desk',
            'transaction_id' => $trxId,
            'payment_type' => $validated['payment_type'],
            'status' => 'verified',
            'notes' => $validated['notes'] ?? 'Direct campus admission recorded by ' . auth()->user()->name,
            'verified_at' => now(),
            'verified_by' => auth()->id(),
        ]);

        // Enroll Student
        if (!$user->enrolledCourses()->where('course_id', $course->id)->exists()) {
            $user->enrolledCourses()->attach($course->id);
        }

        return redirect()->route('admin.accounting.index')->with('success', "Tuition receipt #{$payment->invoice_number} successfully registered. Student {$user->name} has been enrolled in {$course->title}.");
    }

    /**
     * Record an institutional or faculty operational expense.
     */
    public function recordExpense(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'paid_to' => 'required|string|max:255',
            'payment_method' => 'required|string|max:50',
            'reference_no' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['created_by'] = auth()->id();

        FinancialExpense::create($validated);

        return redirect()->route('admin.accounting.index')->with('success', 'Operational expense recorded successfully in ledger.');
    }

    /**
     * Soft delete an expense entry.
     */
    public function destroyExpense(FinancialExpense $expense)
    {
        $expense->delete();
        return redirect()->route('admin.accounting.index')->with('success', 'Expense entry removed.');
    }

    /**
     * Printable Financial Statement View.
     */
    public function statement(Request $request)
    {
        $totalRealized = Payment::where('status', 'verified')->sum('amount');
        $totalExpenses = FinancialExpense::sum('amount');
        $netMargin = $totalRealized - $totalExpenses;

        $payments = Payment::with(['user', 'course', 'paymentMethod'])
            ->where('status', 'verified')
            ->latest('verified_at')
            ->get();

        $expenses = FinancialExpense::latest('expense_date')->get();

        return view('admin.accounting.statement', compact('totalRealized', 'totalExpenses', 'netMargin', 'payments', 'expenses'));
    }
}
