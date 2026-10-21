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

    public function test_enrolled_user_can_view_certificate()
    {
        $user = User::factory()->create();
        $course = Course::where('is_published', true)->first();

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
}
