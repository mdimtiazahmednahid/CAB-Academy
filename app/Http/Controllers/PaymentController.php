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

        // Autofill email and phone from authenticated user if omitted in request
        if (Auth::check()) {
            if (empty($request->email) && !empty(Auth::user()->email)) {
                $request->merge(['email' => Auth::user()->email]);
            }
            if (empty($request->name) && !empty(Auth::user()->name)) {
                $request->merge(['name' => Auth::user()->name]);
            }
        }

        // Build dynamic validation rules based on course enrollment fields
        $rules = [
            'name' => 'required|string|max:120',
            'phone' => ['required', 'string', 'max:15', 'regex:/^01[3-9]\d{8}$/'],
            'training_mode' => 'nullable|string|in:in_person,online_zoom',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'transaction_id' => 'nullable|string|max:100',
            'sender_number' => 'nullable|string|max:15',
            'custom_fields' => 'nullable|array',
        ];

        $messages = [
            'phone.regex' => 'Please enter a valid 11-digit Bangladeshi mobile number (e.g., 01917054664).'
        ];

        // Dynamic email validation: check if email is required by this course
        if ($course->isEmailRequired()) {
            $rules['email'] = 'required|email|max:150';
            $messages['email.required'] = 'Email address is required for this course.';
        } else {
            $rules['email'] = 'nullable|email|max:150';
        }

        // Validate custom fields if configured
        $configuredFields = $course->effective_enrollment_fields;
        foreach ($configuredFields as $field) {
            $fName = $field['name'] ?? '';
            if (in_array($fName, ['name', 'phone', 'email', 'training_mode']) || empty($fName)) {
                continue;
            }
            if (!empty($field['is_enabled']) && !empty($field['is_required'])) {
                $rules['custom_fields.' . $fName] = 'required|string|max:500';
                $messages['custom_fields.' . $fName . '.required'] = 'Please enter your ' . ($field['label'] ?? $fName) . '.';
            } else {
                $rules['custom_fields.' . $fName] = 'nullable|string|max:1000';
            }
        }

        $request->validate($rules, $messages);

        // Compile all dynamic form responses
        $formData = [
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'training_mode' => $request->training_mode ?? 'online_zoom',
        ];

        if ($request->has('custom_fields') && is_array($request->input('custom_fields'))) {
            foreach ($request->input('custom_fields') as $k => $v) {
                if ($v !== null && trim((string)$v) !== '') {
                    $formData[$k] = trim((string)$v);
                }
            }
        }
        foreach (['organization', 'designation', 'city', 'comments'] as $directField) {
            if ($request->filled($directField) && !isset($formData[$directField])) {
                $formData[$directField] = trim((string)$request->input($directField));
            }
        }

        // Resolve or create student user (fallback to auto-generated email if not provided)
        $user = Auth::user();
        if (!$user) {
            $email = $request->email ?: ('student_' . preg_replace('/[^0-9]/', '', $request->phone) . '@cabacademy.com');
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'password' => Hash::make('cab_' . $request->phone),
                    'role' => 'student',
                ]
            );
            if (empty($user->phone) && !empty($request->phone)) {
                $user->update(['phone' => $request->phone]);
            }
            Auth::login($user);
        }

        // Prepare human-readable summary notes for academic counselors
        $notes = 'Mode: ' . ($request->training_mode === 'in_person' ? 'Campus' : 'Online Zoom');
        $extraParts = [];
        foreach ($formData as $k => $v) {
            if (!in_array($k, ['name', 'phone', 'email', 'training_mode'])) {
                $extraParts[] = ucfirst(str_replace('_', ' ', $k)) . ': ' . $v;
            }
        }
        if (!empty($extraParts)) {
            $notes .= ' | ' . implode(', ', $extraParts);
        }

        // If free course
        if ($course->price <= 0) {
            if (!$user->enrolledCourses()->where('course_id', $course->id)->exists()) {
                $user->enrolledCourses()->attach($course->id);
            }

            // Create zero-amount verified registration record to persist form responses
            Payment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'payment_method_id' => null,
                'amount' => 0.00,
                'sender_number' => $request->sender_number ?: $request->phone,
                'transaction_id' => 'FREE-ENROLL-' . strtoupper(Str::random(6)),
                'status' => 'verified',
                'notes' => $notes,
                'form_data' => $formData,
                'verified_at' => now(),
            ]);

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

        // Paid course: Record payment / reservation with dynamic form data
        $payment = Payment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'payment_method_id' => $request->payment_method_id,
            'amount' => $course->price,
            'sender_number' => $request->sender_number ?: $request->phone,
            'transaction_id' => $request->transaction_id ?: ('RES-' . strtoupper(Str::random(8))),
            'status' => 'pending',
            'notes' => $notes,
            'form_data' => $formData,
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
