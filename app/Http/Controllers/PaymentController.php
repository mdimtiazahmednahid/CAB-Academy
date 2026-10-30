<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\PaymentMethod;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        DB::transaction(function () use ($request, $course) {
            Payment::updateOrCreate(
                [
                    'user_id' => $request->user()->id,
                    'course_id' => $course->id,
                    'status' => 'pending',
                ],
                [
                    'payment_method_id' => $request->payment_method_id,
                    'amount' => $course->price,
                    'sender_number' => $request->sender_number,
                    'transaction_id' => $request->transaction_id,
                ]
            );
        });

        return redirect()->route('dashboard')->with('success', 'Your payment has been submitted and is pending verification. You will be enrolled once approved.');
    }

    public function expressEnroll(Request $request, Course $course)
    {
        abort_if(!$course->is_published, 404);

        \App\Support\DatabaseSchemaRepair::ensureSchema();

        // Autofill name, email, and phone from authenticated user if omitted in request
        if (Auth::check()) {
            if (empty($request->email) && !empty(Auth::user()->email)) {
                $request->merge(['email' => Auth::user()->email]);
            }
            if (empty($request->name) && !empty(Auth::user()->name)) {
                $request->merge(['name' => Auth::user()->name]);
            }
            if (empty($request->phone)) {
                $fallbackPhone = !empty(Auth::user()->phone) ? Auth::user()->phone : $request->sender_number;
                if (!empty($fallbackPhone)) {
                    $request->merge(['phone' => preg_replace('/[^0-9]/', '', (string) $fallbackPhone)]);
                }
            }
        }

        if (empty($request->sender_number) && !empty($request->phone)) {
            $request->merge(['sender_number' => preg_replace('/[^0-9]/', '', (string) $request->phone)]);
        }

        $isPaidCourse = (float) $course->price > 0;

        // Build dynamic validation rules based on course enrollment fields & price
        $rules = [
            'name' => 'required|string|max:120',
            'phone' => ['required', 'string', 'max:15', 'regex:/^01[3-9]\d{8}$/'],
            'password' => 'nullable|string|min:4|max:100',
            'training_mode' => 'nullable|string|in:in_person,online_zoom',
            'payment_method_id' => 'nullable|integer',
            'transaction_id' => $isPaidCourse
                ? ['required', 'string', 'min:4', 'max:100', 'not_in:RESERVED-OFFLINE,reserved-offline,FREE,free']
                : 'nullable|string|max:100',
            'sender_number' => $isPaidCourse
                ? ['required', 'string', 'max:15', 'regex:/^01[3-9]\d{8}$/']
                : 'nullable|string|max:15',
            'custom_fields' => 'nullable|array',
        ];

        $messages = [
            'phone.regex' => 'Please enter a valid 11-digit Bangladeshi mobile number (e.g., 01917054664).',
            'sender_number.required' => 'Sender mobile number is required to verify your payment.',
            'sender_number.regex' => 'Please enter a valid 11-digit Bangladeshi sender mobile number (e.g., 01917054664).',
            'transaction_id.required' => 'Payment Transaction ID (TrxID) is required to enroll in a paid course.',
            'transaction_id.not_in' => 'Offline reservation without payment is disabled. Please enter your valid payment Transaction ID (TrxID).',
            'password.min' => 'Your login password / PIN must be at least 4 digits or characters.',
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

        // Enforce course attendance mode when course is strictly Online or strictly In-Person
        $effectiveTrainingMode = $request->training_mode ?? 'online_zoom';
        if ($course->isOnlineOnly()) {
            $effectiveTrainingMode = 'online_zoom';
        } elseif ($course->isCampusOnly()) {
            $effectiveTrainingMode = 'in_person';
        }
        $request->merge(['training_mode' => $effectiveTrainingMode]);

        // Compile all dynamic form responses
        $formData = [
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'training_mode' => $effectiveTrainingMode,
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
        $cleanPhone = preg_replace('/[^0-9]/', '', (string) $request->phone);
        $rawPassword = $request->filled('password')
            ? (string) $request->password
            : substr($cleanPhone, -4);

        if (!$user) {
            $email = strtolower(trim($request->email ?: ('student_' . $cleanPhone . '@cabacademy.com')));
            // Check by email first, or by phone if email was not explicitly matched
            $user = User::where('email', $email)->orWhere('phone', $cleanPhone)->first();
            if (!$user) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $email,
                    'phone' => $cleanPhone,
                    'password' => Hash::make($rawPassword),
                    'role' => 'student',
                    'email_verified_at' => now(),
                    'preferences' => [
                        'level' => 'Executive & Corporate Professional',
                        'subjects' => [$course->category ?: 'Export-Import & L/C Operations'],
                        'goals' => ['Earn Verified Executive Certification'],
                    ],
                ]);
            } else {
                $updates = [];
                if (empty($user->phone) && !empty($cleanPhone)) {
                    $updates['phone'] = $cleanPhone;
                }
                if ($user->preferences === null) {
                    $updates['preferences'] = [
                        'level' => 'Executive & Corporate Professional',
                        'subjects' => [$course->category ?: 'Export-Import & L/C Operations'],
                        'goals' => ['Earn Verified Executive Certification'],
                    ];
                }
                if (!empty($updates)) {
                    $user->update($updates);
                }
            }

            // Security: Auto-authenticate newly created student accounts, OR existing non-admin students who provided their matching password
            if (!$user->isAdmin()) {
                if ($user->wasRecentlyCreated) {
                    Auth::login($user);
                } elseif ($request->filled('password') && Hash::check($request->password, $user->password)) {
                    Auth::login($user);
                } elseif ($request->filled('password') && str_starts_with((string) $user->email, 'student_') && (string) $user->phone === $cleanPhone) {
                    // Allow auto-generated student account re-enrolling with same phone to set/update their PIN
                    $user->password = Hash::make($rawPassword);
                    $user->saveQuietly();
                    Auth::login($user);
                }
            }
        } else {
            // Authenticated user: sync phone number & preferences if missing on profile
            $updates = [];
            if (empty($user->phone) && !empty($cleanPhone)) {
                $updates['phone'] = $cleanPhone;
            }
            if ($user->preferences === null) {
                $updates['preferences'] = [
                    'level' => 'Executive & Corporate Professional',
                    'subjects' => [$course->category ?: 'Export-Import & L/C Operations'],
                    'goals' => ['Earn Verified Executive Certification'],
                ];
            }
            if (!empty($updates)) {
                $user->update($updates);
            }
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
            DB::transaction(function () use ($user, $course, $request, $notes, $formData) {
                $user->enrolledCourses()->syncWithoutDetaching([$course->id]);

                // Create or update zero-amount verified registration record to persist form responses
                Payment::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'course_id' => $course->id,
                        'amount' => 0.00,
                    ],
                    [
                        'payment_method_id' => null,
                        'sender_number' => $request->sender_number ?: $request->phone,
                        'transaction_id' => 'FREE-ENROLL-' . strtoupper(Str::random(6)),
                        'status' => 'verified',
                        'notes' => $notes,
                        'form_data' => $formData,
                        'verified_at' => now(),
                    ]
                );
            });

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'status' => 'enrolled',
                    'login_id' => $cleanPhone . ($request->email ? ' / ' . $request->email : ''),
                    'message' => 'Congratulations! You have been successfully enrolled in ' . $course->title . '.',
                    'redirect_url' => route('courses.show', $course),
                ]);
            }

            return redirect()->route('courses.show', $course)->with('success', 'You have been enrolled in the course!');
        }

        // Paid course: Record payment / reservation with dynamic form data (reusing any existing pending record for this course)
        $resolvedPaymentMethodId = ($request->payment_method_id && PaymentMethod::where('id', $request->payment_method_id)->exists())
            ? $request->payment_method_id
            : PaymentMethod::where('is_active', true)->value('id');

        $payment = DB::transaction(function () use ($user, $course, $request, $notes, $formData, $resolvedPaymentMethodId) {
            return Payment::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'status' => 'pending',
                ],
                [
                    'payment_method_id' => $resolvedPaymentMethodId,
                    'amount' => $course->price,
                    'sender_number' => $request->sender_number ?: $request->phone,
                    'transaction_id' => trim((string) $request->transaction_id),
                    'notes' => $notes,
                    'form_data' => $formData,
                ]
            );
        });

        $message = 'Enrollment request received! Your seat has been reserved. Our academic counselors will verify your payment details shortly.';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => 'pending',
                'order_id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'login_id' => $cleanPhone . ($request->email ? ' / ' . $request->email : ''),
                'message' => $message,
                'redirect_url' => route('dashboard'),
            ]);
        }

        return redirect()->route('dashboard')->with('success', $message);
    }
}
