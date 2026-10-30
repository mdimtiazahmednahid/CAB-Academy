<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        DB::transaction(function () use ($validated, $payment) {
            if ($validated['status'] === 'verified') {
                $payment->update([
                    'status' => 'verified',
                    'verified_at' => now(),
                    'verified_by' => auth()->id(),
                ]);

                // Enroll the user in the course
                if ($payment->user) {
                    $payment->user->enrolledCourses()->syncWithoutDetaching([$payment->course_id]);
                }
            } elseif ($validated['status'] === 'rejected') {
                $payment->update(['status' => 'rejected']);

                // Safely detach enrollment only if the user has no other verified payment for this course
                if ($payment->user) {
                    $hasOtherVerifiedPayment = Payment::where('user_id', $payment->user_id)
                        ->where('course_id', $payment->course_id)
                        ->where('status', 'verified')
                        ->where('id', '!=', $payment->id)
                        ->exists();

                    if (!$hasOtherVerifiedPayment) {
                        $payment->user->enrolledCourses()->detach($payment->course_id);
                    }
                }
            } else {
                $payment->update(['status' => $validated['status']]);
            }
        });

        return back()->with('success', "Payment status updated to " . ucfirst($validated['status']) . ".");
    }
}
