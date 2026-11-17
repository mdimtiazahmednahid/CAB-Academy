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
            'certificate_conferment_text' => 'This certificate is presented to',
            'certificate_completion_text' => 'Successfully participated in the training program.',
            'certificate_primary_signatory_name' => 'Saidur Rahman',
            'certificate_primary_signatory_title' => 'ACS GOVERNING BODY',
            'certificate_template' => 'acs_signature',
            'certificate_title_font' => 'Playfair Display',
            'certificate_recipient_font' => 'Great Vibes',
            'certificate_course_font' => 'Montserrat',
            'certificate_body_font' => 'Lora',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertSame('Playfair Display', Setting::getVal('certificate_title_font'));
        $this->assertSame('Great Vibes', Setting::getVal('certificate_recipient_font'));
        $this->assertSame('Montserrat', Setting::getVal('certificate_course_font'));
        $this->assertSame('Lora', Setting::getVal('certificate_body_font'));

        $certResponse = $this->actingAs($admin)->get(route('courses.certificate', [
            'course' => $course->id,
            'as_user' => $student->id,
        ]));

        $certResponse->assertOk();
        $certResponse->assertSee('Playfair Display', false);
        $certResponse->assertSee('Great Vibes', false);
        $certResponse->assertSee('Montserrat', false);
        $certResponse->assertSee('Lora', false);
        $certResponse->assertSee('fitCertRecipientNames', false);
        $certResponse->assertSee($student->name);
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
