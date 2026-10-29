<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    /**
     * Display Certificate Management Hub: Template Customizer & Issued Registry.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Certificate::with(['user', 'course'])->latest();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('certificate_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('course', function ($cq) use ($search) {
                      $cq->where('title', 'like', "%{$search}%");
                  });
            });
        }

        $certificates = $query->paginate(20)->withQueryString();

        // Certificate Global Customization Settings with sensible defaults
        $settings = [
            'institution_name' => Setting::getVal('certificate_institution_name', 'Commercial Academy Bangladesh'),
            'tagline' => Setting::getVal('certificate_tagline', 'CAB Academy • Learn, Grow, Lead.'),
            'campus_address' => Setting::getVal('certificate_campus_address', 'Mirpur-Pallabi Campus • Dhaka, Bangladesh'),
            'title' => Setting::getVal('certificate_title', 'Executive Certification of Mastery'),
            'conferment_text' => Setting::getVal('certificate_conferment_text', 'This is proudly presented and conferred upon'),
            'completion_text' => Setting::getVal('certificate_completion_text', 'for having successfully completed all prescribed coursework, simulated trade laboratories, case examinations, and demonstrating professional competence in'),
            'faculty_endorsement' => Setting::getVal('certificate_faculty_endorsement', 'Delivered under the auspices of the CAB Academy Executive Faculty Board.'),
            'primary_signatory_name' => Setting::getVal('certificate_primary_signatory_name', 'Academic Director'),
            'primary_signatory_title' => Setting::getVal('certificate_primary_signatory_title', 'Academic Director'),
            'primary_signatory_board' => Setting::getVal('certificate_primary_signatory_board', 'CAB Academy Governing Council'),
            'show_secondary_signature' => Setting::getVal('certificate_show_secondary_signature', '1'),
            'secondary_signatory_name' => Setting::getVal('certificate_secondary_signatory_name', 'Lead Executive Trainer'),
            'secondary_signatory_title' => Setting::getVal('certificate_secondary_signatory_title', 'Course Director & Faculty Chair'),
            'seal_text' => Setting::getVal('certificate_seal_text', 'OFFICIAL SEAL • VERIFIED'),
            'template' => Setting::getVal('certificate_template', 'classic_navy'),
        ];

        // Sample student & course for real-time live preview inside admin
        $sampleUser = (object)[
            'id' => 1,
            'name' => 'Tanvir Ahmed, Lead Commercial Specialist',
            'email' => 'student@cab-academy.com',
        ];
        $sampleCourse = Course::where('is_published', true)->first() ?: (object)[
            'id' => 1,
            'title' => 'Executive Masterclass in Export-Import & L/C Operations',
            'batch' => '205th Executive Batch',
            'show_batch' => true,
        ];
        $sampleCertificate = (object)[
            'id' => 999,
            'certificate_number' => 'CAB-2026-DEMO88',
            'issue_date' => now()->format('Y-m-d'),
            'verification_hash' => hash('sha256', 'sample_certificate_verification_hash'),
        ];

        return view('admin.certificates.index', compact('certificates', 'settings', 'sampleUser', 'sampleCourse', 'sampleCertificate'));
    }

    /**
     * Update certificate template and customization settings.
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'certificate_institution_name' => 'required|string|max:255',
            'certificate_tagline' => 'nullable|string|max:255',
            'certificate_campus_address' => 'nullable|string|max:255',
            'certificate_title' => 'required|string|max:255',
            'certificate_conferment_text' => 'required|string|max:255',
            'certificate_completion_text' => 'required|string',
            'certificate_faculty_endorsement' => 'nullable|string|max:255',
            'certificate_primary_signatory_name' => 'required|string|max:255',
            'certificate_primary_signatory_title' => 'required|string|max:255',
            'certificate_primary_signatory_board' => 'nullable|string|max:255',
            'certificate_show_secondary_signature' => 'nullable|boolean',
            'certificate_secondary_signatory_name' => 'nullable|string|max:255',
            'certificate_secondary_signatory_title' => 'nullable|string|max:255',
            'certificate_seal_text' => 'nullable|string|max:100',
            'certificate_template' => 'required|string|in:classic_navy,modern_emerald,crimson_prestige,minimalist_corporate',
        ]);

        $validated['certificate_show_secondary_signature'] = $request->has('certificate_show_secondary_signature') ? '1' : '0';

        foreach ($validated as $key => $val) {
            Setting::setVal($key, $val, 'text', 'certificate');
        }

        return back()->with('success', 'Certificate template and institution branding updated successfully!');
    }

    /**
     * Standalone live certificate preview for admin testing.
     */
    public function preview(Request $request)
    {
        $user = auth()->user() ?: User::first();
        $course = Course::where('is_published', true)->first();
        if (!$course) {
            $course = Course::first();
        }

        $certificate = (object)[
            'id' => 0,
            'certificate_number' => 'CAB-' . date('Y') . '-PREVIEW',
            'issue_date' => now()->format('Y-m-d'),
            'verification_hash' => hash('sha256', 'preview_' . time()),
        ];

        return view('certificates.show', compact('certificate', 'course', 'user'));
    }

    /**
     * Revoke an issued certificate.
     */
    public function destroy(Certificate $certificate)
    {
        $certificate->delete();
        return back()->with('success', 'Certificate has been revoked.');
    }
}
