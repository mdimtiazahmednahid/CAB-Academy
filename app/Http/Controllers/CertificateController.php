<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Payment;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    /**
     * View and print official digital certificate for a course.
     */
    public function show(Course $course)
    {
        $user = auth()->user();

        // Check if user is enrolled, has a verified payment, completed lessons, or is admin
        $isEnrolled = $user->enrolledCourses()->where('course_id', $course->id)->exists();
        $isVerifiedPaid = Payment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'verified')
            ->exists();
        $isAdmin = in_array($user->role, ['admin', 'super_admin']);

        if (!$isEnrolled && !$isVerifiedPaid && !$isAdmin) {
            return redirect()->route('courses.show', $course)
                ->with('error', 'You must be enrolled and have completed course verification to access the digital certificate.');
        }

        $certificate = Certificate::generateFor($user, $course);

        return view('certificates.show', compact('certificate', 'course', 'user'));
    }

    /**
     * Public credential verification registry endpoint.
     */
    public function verify($certificate_number)
    {
        $certificate = Certificate::where('certificate_number', $certificate_number)
            ->with(['user', 'course'])
            ->first();

        return view('certificates.verify', compact('certificate', 'certificate_number'));
    }
}
