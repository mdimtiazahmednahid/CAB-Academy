<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateAndInvoiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_enrolled_user_cannot_view_certificate_when_not_enabled_by_admin()
    {
        $user = User::factory()->create();
        $course = Course::where('is_published', true)->first();
        $course->update(['certificate_enabled' => false]);

        $user->enrolledCourses()->attach($course->id);

        $response = $this->actingAs($user)->get("/courses/{$course->id}/certificate");

        $response->assertRedirect("/courses/{$course->id}");
        $response->assertSessionHas('error');

        $this->assertDatabaseMissing('certificates', [
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_enrolled_user_can_view_certificate_when_enabled_by_admin()
    {
        $user = User::factory()->create();
        $course = Course::where('is_published', true)->first();
        $course->update(['certificate_enabled' => true]);

        $user->enrolledCourses()->attach($course->id);

        $response = $this->actingAs($user)->get("/courses/{$course->id}/certificate");

        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee($course->title);
        $response->assertSee('CAB-');

        $this->assertDatabaseHas('certificates', [
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_enrolled_user_can_view_certificate_if_individually_issued_by_admin()
    {
        $user = User::factory()->create();
        $course = Course::where('is_published', true)->first();
        $course->update(['certificate_enabled' => false]);

        $user->enrolledCourses()->attach($course->id);

        // Explicitly issued by admin
        Certificate::generateFor($user, $course);

        $response = $this->actingAs($user)->get("/courses/{$course->id}/certificate");

        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee($course->title);
    }

    public function test_admin_can_issue_and_revoke_certificate_for_student()
    {
        $admin = User::where('role', 'admin')->first();
        $student = User::factory()->create();
        $course = Course::where('is_published', true)->first();
        $student->enrolledCourses()->attach($course->id);

        // 1. Issue certificate
        $issueRes = $this->actingAs($admin)->post("/admin/courses/{$course->id}/students/{$student->id}/certificate");
        $issueRes->assertSessionHas('success');

        $this->assertDatabaseHas('certificates', [
            'user_id' => $student->id,
            'course_id' => $course->id,
        ]);

        // 2. Revoke certificate
        $revokeRes = $this->actingAs($admin)->delete("/admin/courses/{$course->id}/students/{$student->id}/certificate");
        $revokeRes->assertSessionHas('success');

        $this->assertDatabaseMissing('certificates', [
            'user_id' => $student->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_admin_can_always_preview_certificates()
    {
        $admin = User::where('role', 'admin')->first();
        $course = Course::where('is_published', true)->first();
        $course->update(['certificate_enabled' => false]);

        $response = $this->actingAs($admin)->get("/courses/{$course->id}/certificate");

        $response->assertStatus(200);
        $response->assertSee($admin->name);
        $response->assertSee($course->title);
    }

    public function test_public_can_verify_certificate_by_serial()
    {
        $user = User::factory()->create(['name' => 'Verified Executive']);
        $course = Course::where('is_published', true)->first();

        $certificate = Certificate::generateFor($user, $course);

        $response = $this->get("/verify-certificate/{$certificate->certificate_number}");

        $response->assertStatus(200);
        $response->assertSee('Verified Executive');
        $response->assertSee($course->title);
        $response->assertSee($certificate->certificate_number);
    }

    public function test_user_can_view_tuition_invoice_receipt()
    {
        $user = User::factory()->create();
        $course = Course::where('is_published', true)->first();
        $payment = Payment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'amount' => 5000,
            'status' => 'verified',
            'sender_number' => '01917054664',
            'transaction_id' => 'TXN99887766',
        ]);

        $response = $this->actingAs($user)->get("/payments/{$payment->id}/invoice");

        $response->assertStatus(200);
        $response->assertSee('INV-CAB-');
        $response->assertSee('TXN99887766');
        $response->assertSee($course->title);
    }

    public function test_batch_number_is_hidden_by_default_and_only_shown_when_admin_enables_it()
    {
        $admin = User::where('role', 'admin')->first();
        $course = Course::where('is_published', true)->first();
        $course->update([
            'batch' => '205th Executive Batch',
            'show_batch' => false,
            'certificate_enabled' => true,
        ]);

        // 1. When show_batch is false, batch is hidden from course show and certificate
        $showRes = $this->get("/courses/{$course->id}");
        $showRes->assertDontSee('205th Executive Batch');

        $certRes = $this->actingAs($admin)->get("/courses/{$course->id}/certificate");
        $certRes->assertDontSee('205th Executive Batch');

        // 2. Admin enables show_batch via course update
        $updateRes = $this->actingAs($admin)->put("/admin/courses/{$course->id}", [
            'title' => $course->title,
            'batch' => '205th Executive Batch',
            'show_batch' => 1,
            'certificate_enabled' => 1,
            'is_published' => 1,
        ]);
        $updateRes->assertSessionHas('success');
        $this->assertTrue((bool)$course->fresh()->show_batch);

        // 3. When show_batch is true, batch is visible on course page and certificate
        $showResAfter = $this->get("/courses/{$course->id}");
        $showResAfter->assertSee('205th Executive Batch');

        $certResAfter = $this->actingAs($admin)->get("/courses/{$course->id}/certificate");
        $certResAfter->assertSee('205th Executive Batch');
    }

    public function test_certificate_defaults_to_commercial_academy_bangladesh()
    {
        $admin = User::where('role', 'admin')->first();
        $course = Course::where('is_published', true)->first();

        $response = $this->actingAs($admin)->get("/courses/{$course->id}/certificate");
        $response->assertStatus(200);
        $response->assertSee('Commercial Academy Bangladesh');
        $response->assertSee('CAB Academy • Learn, Grow, Lead.');
    }

    public function test_admin_can_access_certificate_customizer_hub_and_update_institution_name_and_template()
    {
        $admin = User::where('role', 'admin')->first();
        $course = Course::where('is_published', true)->first();

        // 1. Admin accesses certificate customizer hub
        $hubRes = $this->actingAs($admin)->get('/admin/certificates');
        $hubRes->assertStatus(200);
        $hubRes->assertSee('Template Customizer');
        $hubRes->assertSee('Commercial Academy Bangladesh');

        // 2. Admin customizes institution name, template, title, and signatories
        $postRes = $this->actingAs($admin)->post('/admin/certificates/settings', [
            'certificate_institution_name' => 'Commercial Academy Bangladesh (Executive Division)',
            'certificate_tagline' => 'Excellence in International Trade & Supply Chain',
            'certificate_campus_address' => 'Mirpur Diplomatic Zone, Dhaka',
            'certificate_title' => 'Executive Fellowship in Commercial Operations',
            'certificate_conferment_text' => 'Proudly awarded and conferred upon',
            'certificate_completion_text' => 'having demonstrated remarkable mastery in international logistics and commerce.',
            'certificate_faculty_endorsement' => 'Under royal charter and board faculty auspices.',
            'certificate_primary_signatory_name' => 'Dr. Kazi Mostafa',
            'certificate_primary_signatory_title' => 'Director General of Academics',
            'certificate_primary_signatory_board' => 'National Commercial Governing Board',
            'certificate_show_secondary_signature' => '1',
            'certificate_secondary_signatory_name' => 'Engr. Saiful Islam',
            'certificate_secondary_signatory_title' => 'Senior Vice President of Logistics',
            'certificate_seal_text' => 'EXECUTIVE SEAL • HONORS',
            'certificate_template' => 'modern_emerald',
        ]);

        $postRes->assertRedirect();
        $postRes->assertSessionHas('success');

        // Verify settings persisted in Setting model
        $this->assertEquals('Commercial Academy Bangladesh (Executive Division)', \App\Models\Setting::getVal('certificate_institution_name'));
        $this->assertEquals('modern_emerald', \App\Models\Setting::getVal('certificate_template'));

        // 3. View certificate and assert the customized name and template are applied
        $certRes = $this->actingAs($admin)->get("/courses/{$course->id}/certificate");
        $certRes->assertStatus(200);
        $certRes->assertSee('Commercial Academy Bangladesh (Executive Division)');
        $certRes->assertSee('Excellence in International Trade & Supply Chain');
        $certRes->assertSee('Executive Fellowship in Commercial Operations');
        $certRes->assertSee('Dr. Kazi Mostafa');
        $certRes->assertSee('Engr. Saiful Islam');
        $certRes->assertSee('EXECUTIVE SEAL');

        // 4. View verification page and assert dynamic institution name
        $user = User::factory()->create();
        $cert = Certificate::generateFor($user, $course);
        $verifyRes = $this->get("/verify-certificate/{$cert->certificate_number}");
        $verifyRes->assertStatus(200);
        $verifyRes->assertSee('Commercial Academy Bangladesh (Executive Division)');
    }

    public function test_admin_can_preview_different_templates_on_the_fly()
    {
        $admin = User::where('role', 'admin')->first();

        // 1. Preview default or requested template
        $resNavy = $this->actingAs($admin)->get('/admin/certificates/preview?template=classic_navy');
        $resNavy->assertStatus(200);
        $resNavy->assertSee('border-[#051c45]');

        $resEmerald = $this->actingAs($admin)->get('/admin/certificates/preview?template=modern_emerald');
        $resEmerald->assertStatus(200);
        $resEmerald->assertSee('border-[#064e3b]');

        $resCrimson = $this->actingAs($admin)->get('/admin/certificates/preview?template=crimson_prestige');
        $resCrimson->assertStatus(200);
        $resCrimson->assertSee('border-[#880102]');

        $resMinimal = $this->actingAs($admin)->get('/admin/certificates/preview?template=minimalist_corporate');
        $resMinimal->assertStatus(200);
        $resMinimal->assertSee('border-slate-900');
    }

    public function test_admin_can_revoke_issued_certificate_from_registry()
    {
        $admin = User::where('role', 'admin')->first();
        $student = User::factory()->create();
        $course = Course::where('is_published', true)->first();

        $certificate = Certificate::generateFor($student, $course);
        $this->assertDatabaseHas('certificates', ['id' => $certificate->id]);

        $deleteRes = $this->actingAs($admin)->delete("/admin/certificates/{$certificate->id}");
        $deleteRes->assertRedirect();
        $deleteRes->assertSessionHas('success');

        $this->assertDatabaseMissing('certificates', ['id' => $certificate->id]);
    }
}

