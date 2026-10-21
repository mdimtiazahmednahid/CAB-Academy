<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\PaymentMethod;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
            'sender_number' => ['required', 'string', 'max:11', 'regex:/^01[3-9]\d{8}$/'],
            'transaction_id' => 'required|string|max:255',
        ], [
            'sender_number.regex' => 'Please enter a valid 11-digit Bangladeshi mobile number (e.g., 01712345678).'
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

    public function expressEnroll(Request $request, Course $course)
    {
        abort_if(!$course->is_published, 404);

        $request->validate([
            'name' => 'required|string|max:120',
            'phone' => ['required', 'string', 'max:15', 'regex:/^01[3-9]\d{8}$/'],
            'email' => 'nullable|email|max:150',
            'training_mode' => 'nullable|string|in:in_person,online_zoom',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'transaction_id' => 'nullable|string|max:100',
            'sender_number' => 'nullable|string|max:15',
        ], [
            'phone.regex' => 'Please enter a valid 11-digit Bangladeshi mobile number (e.g., 01917054664).'
        ]);

        // Resolve or create user
        $user = Auth::user();
        if (!$user) {
            $email = $request->email ?: ('student_' . preg_replace('/[^0-9]/', '', $request->phone) . '@cabacademy.com');
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $request->name,
                    'password' => Hash::make('cab_' . $request->phone),
                    'role' => 'student',
                ]
            );
            Auth::login($user);
        }

        // If free course
        if ($course->price <= 0) {
            if (!$user->enrolledCourses()->where('course_id', $course->id)->exists()) {
                $user->enrolledCourses()->attach($course->id);
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'status' => 'enrolled',
                    'message' => 'Congratulations! You have been successfully enrolled in ' . $course->title . '.',
                    'redirect_url' => route('courses.show', $course),
                ]);
            }

            return redirect()->route('courses.show', $course)->with('success', 'You have been enrolled in the course!');
        }

        // Paid course: Record payment / reservation
        $payment = Payment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'payment_method_id' => $request->payment_method_id,
            'amount' => $course->price,
            'sender_number' => $request->sender_number ?: $request->phone,
            'transaction_id' => $request->transaction_id ?: ('RES-' . strtoupper(Str::random(8))),
            'status' => 'pending',
        ]);

        $message = 'Enrollment request received! Your seat has been reserved. Our academic counselors will verify your payment details shortly.';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => 'pending',
                'order_id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'message' => $message,
                'redirect_url' => route('dashboard'),
            ]);
        }

        return redirect()->route('dashboard')->with('success', $message);
    }
}
