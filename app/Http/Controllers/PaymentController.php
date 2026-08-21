<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\PaymentMethod;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function checkout(Course $course)
    {
        abort_if(!$course->is_published, 404);

        if ($course->price <= 0) {
            return redirect()->route('courses.show', $course);
        }

        // Fetch active payment methods
        $methods = PaymentMethod::where('is_active', true)->get();

        return view('courses.checkout', compact('course', 'methods'));
    }

    public function store(Request $request, Course $course)
    {
        abort_if(!$course->is_published, 404);

        $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
            'sender_number' => 'required|string|max:20',
            'transaction_id' => 'required|string|max:255',
        ]);

        Payment::create([
            'user_id' => $request->user()->id,
            'course_id' => $course->id,
            'payment_method_id' => $request->payment_method_id,
            'amount' => $course->price,
            'sender_number' => $request->sender_number,
            'transaction_id' => $request->transaction_id,
            'status' => 'pending',
        ]);

        return redirect()->route('dashboard')->with('success', 'Your payment has been submitted and is pending verification. You will be enrolled once approved.');
    }
}
