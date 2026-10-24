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
    public function show(Course $course, Request $request)
    {
        $currentUser = auth()->user();
        $isActualAdmin = $currentUser->isAdmin();

        // Allow administrator to inspect or print certificate for a specific student (?as_user=ID)
        $user = $currentUser;
        if ($isActualAdmin && $request->filled('as_user')) {
            $targetUser = \App\Models\User::find($request->as_user);
            if ($targetUser) {
                $user = $targetUser;
            }
        }

        // Access Gate:
        // Non-admin users can ONLY access if admin has released certificates (course-wide or individually)
        if (!$isActualAdmin && !$course->isCertificateAvailableFor($user)) {
            $isEnrolled = $user->enrolledCourses()->where('course_id', $course->id)->exists();
            $isVerifiedPaid = Payment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->where('status', 'verified')
                ->exists();

            if (!$isEnrolled && !$isVerifiedPaid) {
                return redirect()->route('courses.show', $course)
                    ->with('error', 'You must be enrolled and have completed course verification to access the digital certificate.');
            }

            return redirect()->route('courses.show', $course)
                ->with('error', 'Digital certificates have not been released for this course yet by the administration. Certificates will become available following final cohort evaluation.');
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
