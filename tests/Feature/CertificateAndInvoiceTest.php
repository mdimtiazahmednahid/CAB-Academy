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
}
