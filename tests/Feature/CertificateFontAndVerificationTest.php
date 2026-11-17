<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateFontAndVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_save_preferred_certificate_fonts_and_certificate_renders_them(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create([
            'role' => 'student',
            'name' => 'MD IMTIAZ AHMED NOMAN CHOWDHURY',
        ]);
        $course = Course::create([
            'instructor_id' => $admin->id,
            'title' => 'Executive Trade & L/C Operations',
            'slug' => 'executive-trade-lc-operations',
            'description' => 'Comprehensive executive program.',
            'price' => 0,
            'is_free' => true,
            'duration' => '6 Weeks (24 Hours)',
            'certificate_enabled' => true,
            'is_published' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.certificates.settings.update'), [
            'certificate_institution_name' => 'ACADEMY OF COMMERCIAL & SUPPLY CHAIN-ACS',
            'certificate_title' => 'CERTIFICATE OF PARTICIPATION',
            'certificate_subtitle' => 'OF EXECUTIVE HONORS',
            'certificate_conferment_text' => 'This certificate is presented to',
            'certificate_completion_text' => 'Successfully completed the executive mastery cohort.',
            'certificate_primary_signatory_name' => 'Saidur Rahman',
            'certificate_primary_signatory_title' => 'ACS GOVERNING BODY',
            'certificate_template' => 'acs_signature',
            'certificate_title_font' => 'Playfair Display',
            'certificate_recipient_font' => 'Great Vibes',
            'certificate_course_font' => 'Montserrat',
            'certificate_body_font' => 'Lora',
            'certificate_meta_font_size' => 10,
            'certificate_seal_font_size' => 18,
            'certificate_sidebar_color' => '#102a5c',
            'certificate_accent_color' => '#AF804F',
            'certificate_recipient_color' => '#410200',
            'certificate_title_color' => '#111827',
            'certificate_body_color' => '#334155',
            'certificate_course_color' => '#0a2540',
            'certificate_logo_width' => 38,
            'certificate_meta_left' => 6.5,
            'certificate_seal_size' => 16.5,
            'certificate_meta_color' => '#15803d',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertSame('Playfair Display', Setting::getVal('certificate_title_font'));
        $this->assertSame('Great Vibes', Setting::getVal('certificate_recipient_font'));
        $this->assertSame('Montserrat', Setting::getVal('certificate_course_font'));
        $this->assertSame('Lora', Setting::getVal('certificate_body_font'));
        $this->assertSame('OF EXECUTIVE HONORS', Setting::getVal('certificate_subtitle'));
        $this->assertSame('10', Setting::getVal('certificate_meta_font_size'));
        $this->assertSame('18', Setting::getVal('certificate_seal_font_size'));
        $this->assertSame('#102a5c', Setting::getVal('certificate_sidebar_color'));
        $this->assertSame('#AF804F', Setting::getVal('certificate_accent_color'));
        $this->assertSame('#410200', Setting::getVal('certificate_recipient_color'));
        $this->assertSame('#111827', Setting::getVal('certificate_title_color'));
        $this->assertSame('#334155', Setting::getVal('certificate_body_color'));
        $this->assertSame('#0a2540', Setting::getVal('certificate_course_color'));
        $this->assertSame('38', Setting::getVal('certificate_logo_width'));
        $this->assertSame('6.5', Setting::getVal('certificate_meta_left'));
        $this->assertSame('16.5', Setting::getVal('certificate_seal_size'));
        $this->assertSame('#15803d', Setting::getVal('certificate_meta_color'));

        $certResponse = $this->actingAs($admin)->get(route('courses.certificate', [
            'course' => $course->id,
            'as_user' => $student->id,
        ]));

        $certResponse->assertOk();
        $certResponse->assertSee('Playfair Display', false);
        $certResponse->assertSee('Great Vibes', false);
        $certResponse->assertSee('Montserrat', false);
        $certResponse->assertSee('Lora', false);
        $certResponse->assertSee('OF EXECUTIVE HONORS');
        $certResponse->assertSee('Successfully completed the executive mastery cohort.');
        $certResponse->assertSee('#102a5c', false);
        $certResponse->assertSee('#AF804F', false);
        $certResponse->assertSee('#410200', false);
        $certResponse->assertSee('#111827', false);
        $certResponse->assertSee('#334155', false);
        $certResponse->assertSee('#0a2540', false);
        $certResponse->assertSee('images/logo.png', false);
        $certResponse->assertSee('width: 38%', false);
        $certResponse->assertSee('left: 6.5%', false);
        $certResponse->assertSee('width: 16.5cqw', false);
        $certResponse->assertSee('#15803d', false);
        $certResponse->assertSee('fitCertRecipientNames', false);
        $certResponse->assertSee('acsSigSealTop', false);
        $certResponse->assertSee($student->name);

        // Verify another enrolled student also receives the updated font settings immediately
        $student->enrolledCourses()->attach($course->id);
        $studentResponse = $this->actingAs($student)->get(route('courses.certificate', $course));
        $studentResponse->assertOk();
        $studentResponse->assertSee('Playfair Display', false);
        $studentResponse->assertSee('Great Vibes', false);
        $studentResponse->assertSee('Montserrat', false);
        $studentResponse->assertSee('Lora', false);
    }

    public function test_certificate_verification_page_displays_course_duration(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'student']);
        $course = Course::create([
            'instructor_id' => $admin->id,
            'title' => 'INCOTERMS 2020 Masterclass',
            'slug' => 'incoterms-2020-masterclass',
            'description' => 'Global trade terms.',
            'price' => 0,
            'is_free' => true,
            'duration' => '4 Weeks Live Cohort',
            'is_published' => true,
        ]);

        $certificate = Certificate::generateFor($student, $course);

        $response = $this->get(route('certificate.verify', $certificate->certificate_number));

        $response->assertOk();
        $response->assertSee('Course Duration');
        $response->assertSee('4 Weeks Live Cohort');
        $response->assertSee($certificate->certificate_number);
    }
}
