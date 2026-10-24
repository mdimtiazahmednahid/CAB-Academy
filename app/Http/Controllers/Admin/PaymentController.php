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

        if ($validated['status'] === 'verified') {
            $payment->update([
                'status' => 'verified',
                'verified_at' => now(),
                'verified_by' => auth()->id(),
            ]);

            // Enroll the user in the course
            if ($payment->user && !$payment->user->enrolledCourses()->where('course_id', $payment->course_id)->exists()) {
                $payment->user->enrolledCourses()->attach($payment->course_id);
            }
        } elseif ($validated['status'] === 'rejected') {
            $payment->update(['status' => 'rejected']);

            // Safely detach enrollment if previously verified
            if ($payment->user) {
                $payment->user->enrolledCourses()->detach($payment->course_id);
            }
        } else {
            $payment->update(['status' => $validated['status']]);
        }

        return back()->with('success', "Payment status updated to " . ucfirst($validated['status']) . ".");
    }
}
