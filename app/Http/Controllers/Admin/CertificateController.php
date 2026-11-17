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
            'institution_name' => Setting::getVal('certificate_institution_name', 'ACADEMY OF COMMERCIAL & SUPPLY CHAIN-ACS'),
            'tagline' => Setting::getVal('certificate_tagline', 'LEARN .GROW.LEAD'),
            'campus_address' => Setting::getVal('certificate_campus_address', 'Mirpur-Pallabi Campus • Dhaka, Bangladesh'),
            'title' => Setting::getVal('certificate_title', 'CERTIFICATE OF APPRECIATION'),
            'conferment_text' => Setting::getVal('certificate_conferment_text', 'This certificate is proudly presented to'),
            'completion_text' => Setting::getVal('certificate_completion_text', 'Successfully participated in the training program, demonstrating active engagement and commitment to professional development.'),
            'faculty_endorsement' => Setting::getVal('certificate_faculty_endorsement', 'Delivered under the auspices of the ACS Academy Executive Faculty Board.'),
            'primary_signatory_name' => Setting::getVal('certificate_primary_signatory_name', 'Saidur Rahman'),
            'primary_signatory_title' => Setting::getVal('certificate_primary_signatory_title', 'ACS EXECUTIVE GOVORNING'),
            'primary_signatory_board' => Setting::getVal('certificate_primary_signatory_board', 'ACS EXECUTIVE GOVORNING'),
            'show_secondary_signature' => Setting::getVal('certificate_show_secondary_signature', '1'),
            'secondary_signatory_name' => Setting::getVal('certificate_secondary_signatory_name', 'Lead Trainer'),
            'secondary_signatory_title' => Setting::getVal('certificate_secondary_signatory_title', 'LEAD TRAINER'),
            'seal_text' => Setting::getVal('certificate_seal_text', 'OFFICIAL SEAL • VERIFIED'),
            'show_seal' => Setting::getVal('certificate_show_seal', '1'),
            'show_seal_logo' => Setting::getVal('certificate_show_seal_logo', '1'),
            'show_seal_text' => Setting::getVal('certificate_show_seal_text', '1'),
            'show_qr_code' => Setting::getVal('certificate_show_qr_code', '1'),
            'show_watermark' => Setting::getVal('certificate_show_watermark', '1'),
            'logo_url' => Setting::getVal('certificate_logo_url', '/images/logo.png'),
            'seal_logo_url' => Setting::getVal('certificate_seal_logo_url', '/images/certificate-logo-gold.png'),
            'seal_bg_url' => Setting::getVal('certificate_seal_bg_url', '/images/certificate-gold-seal.png'),
            'trainer_signature_url' => Setting::getVal('certificate_trainer_signature_url', '/images/cert-official/sig-trainer.svg'),
            'governing_signature_url' => Setting::getVal('certificate_governing_signature_url', '/images/cert-official/sig-governing.svg'),
            'id_label' => Setting::getVal('certificate_id_label', 'CERTIFICATE ID:'),
            'date_label' => Setting::getVal('certificate_date_label', 'DATE:'),
            'batch_label' => Setting::getVal('certificate_batch_label', 'BATCH:'),
            'institution_font_size' => Setting::getVal('certificate_institution_font_size', '31'),
            'title_font_size' => Setting::getVal('certificate_title_font_size', '35'),
            'recipient_font_size' => Setting::getVal('certificate_recipient_font_size', '32'),
            'course_font_size' => Setting::getVal('certificate_course_font_size', '30'),
            'body_font_size' => Setting::getVal('certificate_body_font_size', '16.5'),
            'meta_font_size' => Setting::getVal('certificate_meta_font_size', '11'),
            'seal_font_size' => Setting::getVal('certificate_seal_font_size', '15'),
            'subtitle' => Setting::getVal('certificate_subtitle', 'OF PARTICIPATION'),
            'sidebar_color' => Setting::getVal('certificate_sidebar_color', '#07398d'),
            'accent_color' => Setting::getVal('certificate_accent_color', '#AF804F'),
            'recipient_color' => Setting::getVal('certificate_recipient_color', '#410200'),
            'title_color' => Setting::getVal('certificate_title_color', '#1c2a4c'),
            'body_color' => Setting::getVal('certificate_body_color', '#1c2a4c'),
            'course_color' => Setting::getVal('certificate_course_color', '#003878'),
            'meta_color' => Setting::getVal('certificate_meta_color', '#23bf70'),
            'title_font' => Setting::getVal('certificate_title_font', 'Cinzel'),
            'recipient_font' => Setting::getVal('certificate_recipient_font', 'Alex Brush'),
            'course_font' => Setting::getVal('certificate_course_font', 'Anton'),
            'body_font' => Setting::getVal('certificate_body_font', 'Aleo'),
            'logo_top' => Setting::getVal('certificate_logo_top', '1.5'),
            'logo_width' => Setting::getVal('certificate_logo_width', '34.5'),
            'title_top' => Setting::getVal('certificate_title_top', '18.6'),
            'conferment_top' => Setting::getVal('certificate_conferment_top', '29.8'),
            'recipient_top' => Setting::getVal('certificate_recipient_top', '34.8'),
            'body_top' => Setting::getVal('certificate_body_top', '45.4'),
            'course_top' => Setting::getVal('certificate_course_top', '54.0'),
            'meta_top' => Setting::getVal('certificate_meta_top', '67.2'),
            'meta_left' => Setting::getVal('certificate_meta_left', '5.2'),
            'qr_top' => Setting::getVal('certificate_qr_top', '62.0'),
            'qr_width' => Setting::getVal('certificate_qr_width', '19.8'),
            'footer_bottom' => Setting::getVal('certificate_footer_bottom', '6.2'),
            'seal_size' => Setting::getVal('certificate_seal_size', '14.8'),
            'template' => Setting::getVal('certificate_template', 'acs_signature'),
        ];

        // Sample student & course for real-time live preview inside admin
        $sampleUser = (object)[
            'id' => 1,
            'name' => 'Tanvir Ahmed, Lead Commercial Specialist',
            'email' => 'student@acs-academy.com',
        ];
        $sampleCourse = Course::where('is_published', true)->first() ?: (object)[
            'id' => 1,
            'title' => 'Executive Masterclass in Export-Import & L/C Operations',
            'batch' => '205th Executive Batch',
            'show_batch' => true,
        ];
        $sampleCertificate = (object)[
            'id' => 999,
            'certificate_number' => 'ACS-2026-DEMO88',
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
            'certificate_show_seal' => 'nullable|boolean',
            'certificate_show_seal_logo' => 'nullable|boolean',
            'certificate_show_seal_text' => 'nullable|boolean',
            'certificate_show_qr_code' => 'nullable|boolean',
            'certificate_show_watermark' => 'nullable|boolean',
            'certificate_logo_url' => 'nullable|string|max:500',
            'certificate_seal_logo_url' => 'nullable|string|max:500',
            'certificate_seal_bg_url' => 'nullable|string|max:500',
            'certificate_id_label' => 'nullable|string|max:100',
            'certificate_date_label' => 'nullable|string|max:100',
            'certificate_batch_label' => 'nullable|string|max:100',
            'certificate_institution_font_size' => 'nullable|numeric|min:14|max:60',
            'certificate_title_font_size' => 'nullable|numeric|min:16|max:64',
            'certificate_recipient_font_size' => 'nullable|numeric|min:16|max:64',
            'certificate_course_font_size' => 'nullable|numeric|min:14|max:56',
            'certificate_body_font_size' => 'nullable|numeric|min:10|max:32',
            'certificate_meta_font_size' => 'nullable|numeric|min:8|max:24',
            'certificate_seal_font_size' => 'nullable|numeric|min:8|max:26',
            'certificate_subtitle' => 'nullable|string|max:120',
            'certificate_sidebar_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'certificate_accent_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'certificate_recipient_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'certificate_title_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'certificate_body_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'certificate_course_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'certificate_meta_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'certificate_title_font' => 'nullable|string|in:Cinzel,Cinzel Decorative,Playfair Display,Cormorant Garamond,EB Garamond,Libre Baskerville,Merriweather,Montserrat,Plus Jakarta Sans,Poppins',
            'certificate_recipient_font' => 'nullable|string|in:Alex Brush,Great Vibes,Allura,Pinyon Script,Sacramento,Parisienne,Tangerine,Dancing Script,Playfair Display,Cinzel,Cormorant Garamond,EB Garamond',
            'certificate_course_font' => 'nullable|string|in:Anton,Bebas Neue,Oswald,Cinzel,Playfair Display,Montserrat,Poppins,Lora',
            'certificate_body_font' => 'nullable|string|in:Aleo,Lora,Merriweather,EB Garamond,Libre Baskerville,Montserrat,Plus Jakarta Sans,Poppins,Roboto,Open Sans',
            'certificate_logo_top' => 'nullable|numeric|min:0|max:25',
            'certificate_logo_width' => 'nullable|numeric|min:10|max:70',
            'certificate_title_top' => 'nullable|numeric|min:5|max:40',
            'certificate_conferment_top' => 'nullable|numeric|min:15|max:50',
            'certificate_recipient_top' => 'nullable|numeric|min:20|max:60',
            'certificate_body_top' => 'nullable|numeric|min:25|max:70',
            'certificate_course_top' => 'nullable|numeric|min:35|max:80',
            'certificate_meta_top' => 'nullable|numeric|min:45|max:85',
            'certificate_meta_left' => 'nullable|numeric|min:0|max:45',
            'certificate_qr_top' => 'nullable|numeric|min:40|max:85',
            'certificate_qr_width' => 'nullable|numeric|min:8|max:32',
            'certificate_footer_bottom' => 'nullable|numeric|min:1|max:20',
            'certificate_seal_size' => 'nullable|numeric|min:6|max:24',
            'certificate_logo_file' => 'nullable|image|max:4096',
            'certificate_seal_logo_file' => 'nullable|image|max:4096',
            'certificate_seal_bg_file' => 'nullable|image|max:4096',
            'certificate_trainer_signature_file' => 'nullable|file|mimes:png,svg,jpg,jpeg,webp|max:2048',
            'certificate_governing_signature_file' => 'nullable|file|mimes:png,svg,jpg,jpeg,webp|max:2048',
            'certificate_trainer_signature_url' => 'nullable|string|max:500',
            'certificate_governing_signature_url' => 'nullable|string|max:500',
            'certificate_template' => 'required|string|in:acs_signature,classic_navy,modern_emerald,crimson_prestige,minimalist_corporate',
        ]);

        unset(
            $validated['certificate_logo_file'],
            $validated['certificate_seal_logo_file'],
            $validated['certificate_seal_bg_file'],
            $validated['certificate_trainer_signature_file'],
            $validated['certificate_governing_signature_file']
        );

        // Default to the clean transparent PNG ACS logo if cleared
        if (empty($validated['certificate_logo_url'])) {
            $validated['certificate_logo_url'] = '/images/logo.png';
        }
        if (empty($validated['certificate_seal_logo_url'])) {
            $validated['certificate_seal_logo_url'] = '/images/certificate-logo-gold.png';
        }
        if (empty($validated['certificate_seal_bg_url'])) {
            $validated['certificate_seal_bg_url'] = '/images/certificate-gold-seal.png';
        }
        if (empty($validated['certificate_id_label'])) {
            $validated['certificate_id_label'] = 'CERTIFICATE ID:';
        }
        if (empty($validated['certificate_date_label'])) {
            $validated['certificate_date_label'] = 'DATE:';
        }
        if (empty($validated['certificate_batch_label'])) {
            $validated['certificate_batch_label'] = 'BATCH:';
        }
        $fontDefaults = [
            'certificate_institution_font_size' => '31',
            'certificate_title_font_size' => '35',
            'certificate_recipient_font_size' => '32',
            'certificate_course_font_size' => '30',
            'certificate_body_font_size' => '16.5',
            'certificate_meta_font_size' => '11',
            'certificate_seal_font_size' => '15',
            'certificate_subtitle' => 'OF PARTICIPATION',
            'certificate_sidebar_color' => '#07398d',
            'certificate_accent_color' => '#AF804F',
            'certificate_recipient_color' => '#410200',
            'certificate_title_color' => '#1c2a4c',
            'certificate_body_color' => '#1c2a4c',
            'certificate_course_color' => '#003878',
            'certificate_meta_color' => '#23bf70',
            'certificate_title_font' => 'Cinzel',
            'certificate_recipient_font' => 'Alex Brush',
            'certificate_course_font' => 'Anton',
            'certificate_body_font' => 'Aleo',
            'certificate_logo_top' => '1.5',
            'certificate_logo_width' => '34.5',
            'certificate_title_top' => '18.6',
            'certificate_conferment_top' => '29.8',
            'certificate_recipient_top' => '34.8',
            'certificate_body_top' => '45.4',
            'certificate_course_top' => '54.0',
            'certificate_meta_top' => '67.2',
            'certificate_meta_left' => '5.2',
            'certificate_qr_top' => '62.0',
            'certificate_qr_width' => '19.8',
            'certificate_footer_bottom' => '6.2',
            'certificate_seal_size' => '14.8',
        ];
        foreach ($fontDefaults as $fontKey => $fontDefault) {
            if (empty($validated[$fontKey])) {
                $validated[$fontKey] = $fontDefault;
            }
        }

        // Handle optional file uploads (InfinityFree compatible direct public/uploads/certificates path)
        if ($request->hasFile('certificate_logo_file')) {
            $file = $request->file('certificate_logo_file');
            $dir = public_path('uploads/certificates');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $filename = 'cert_logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $filename);
            $validated['certificate_logo_url'] = '/uploads/certificates/' . $filename;
        }

        if ($request->hasFile('certificate_seal_logo_file')) {
            $file = $request->file('certificate_seal_logo_file');
            $dir = public_path('uploads/certificates');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $filename = 'cert_seal_logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $filename);
            $validated['certificate_seal_logo_url'] = '/uploads/certificates/' . $filename;
        }

        if ($request->hasFile('certificate_seal_bg_file')) {
            $file = $request->file('certificate_seal_bg_file');
            $dir = public_path('uploads/certificates');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $filename = 'cert_seal_bg_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $filename);
            $validated['certificate_seal_bg_url'] = '/uploads/certificates/' . $filename;
        }

        if ($request->hasFile('certificate_trainer_signature_file')) {
            $file = $request->file('certificate_trainer_signature_file');
            $dir = public_path('uploads/certificates');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $filename = 'cert_sig_trainer_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $filename);
            $validated['certificate_trainer_signature_url'] = '/uploads/certificates/' . $filename;
        }

        if ($request->hasFile('certificate_governing_signature_file')) {
            $file = $request->file('certificate_governing_signature_file');
            $dir = public_path('uploads/certificates');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $filename = 'cert_sig_governing_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $filename);
            $validated['certificate_governing_signature_url'] = '/uploads/certificates/' . $filename;
        }

        $validated['certificate_show_secondary_signature'] = $request->has('certificate_show_secondary_signature') ? '1' : '0';

        // For new checkboxes, if the form submits the customizer_toggles_present marker, read checkboxes; otherwise default to '1' for backwards compatibility with existing tests
        if ($request->has('customizer_toggles_present')) {
            $validated['certificate_show_seal'] = $request->has('certificate_show_seal') ? '1' : '0';
            $validated['certificate_show_seal_logo'] = $request->has('certificate_show_seal_logo') ? '1' : '0';
            $validated['certificate_show_seal_text'] = $request->has('certificate_show_seal_text') ? '1' : '0';
            $validated['certificate_show_qr_code'] = $request->has('certificate_show_qr_code') ? '1' : '0';
            $validated['certificate_show_watermark'] = $request->has('certificate_show_watermark') ? '1' : '0';
        } else {
            $validated['certificate_show_seal'] = $request->has('certificate_show_seal') ? '1' : Setting::getVal('certificate_show_seal', '1');
            $validated['certificate_show_seal_logo'] = $request->has('certificate_show_seal_logo') ? '1' : Setting::getVal('certificate_show_seal_logo', '1');
            $validated['certificate_show_seal_text'] = $request->has('certificate_show_seal_text') ? '1' : Setting::getVal('certificate_show_seal_text', '1');
            $validated['certificate_show_qr_code'] = $request->has('certificate_show_qr_code') ? '1' : Setting::getVal('certificate_show_qr_code', '1');
            $validated['certificate_show_watermark'] = $request->has('certificate_show_watermark') ? '1' : Setting::getVal('certificate_show_watermark', '1');
        }

        foreach ($validated as $key => $val) {
            Setting::setVal($key, $val, 'text', 'certificate');
        }

        return back()->with('success', 'Certificate template, seal, QR code, and institution branding updated successfully!');
    }

    /**
     * One-click notify & email student for an issued certificate.
     */
    public function notifyStudent(Certificate $certificate)
    {
        $certificate->loadMissing(['user', 'course']);
        if (!$certificate->user) {
            return back()->with('error', 'Student account not found for this certificate.');
        }

        $delivery = \App\Notifications\CertificateIssuedNotification::sendSafely($certificate->user, $certificate, true);
        $msg = $delivery['mail']
            ? "In-app notification & email sent to {$certificate->user->name} ({$certificate->user->email})."
            : "In-app notification sent to {$certificate->user->name}.";

        return back()->with('success', $msg);
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
            'certificate_number' => 'ACS-' . date('Y') . '-PREVIEW',
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
