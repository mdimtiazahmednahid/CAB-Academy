<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['user', 'course', 'paymentMethod'])->latest()->get();
        return view('admin.payments.index', compact('payments'));
    }

    public function updateStatus(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,verified,rejected'
        ]);

        if ($payment->status === 'verified' && $validated['status'] !== 'verified') {
            // Technically we could un-enroll them if the admin un-verifies, but let's keep it simple
            // We just update the status
        }

        $payment->update(['status' => $validated['status']]);

        // If changed to verified, enroll the user in the course
        if ($validated['status'] === 'verified') {
            if (!$payment->user->enrolledCourses()->where('course_id', $payment->course_id)->exists()) {
                $payment->user->enrolledCourses()->attach($payment->course_id);
            }
        }

        return back()->with('success', "Payment status updated to {$validated['status']}.");
    }
}
