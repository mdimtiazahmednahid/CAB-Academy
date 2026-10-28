<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseDynamicFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_course_defaults_to_standard_professional_template(): void
    {
        $course = Course::where('is_published', true)->first();
        $this->assertNotNull($course);

        $fields = $course->effective_enrollment_fields;
        $this->assertIsArray($fields);
        $this->assertTrue($course->isEmailRequired());
        $this->assertTrue($course->isEmailEnabled());
    }

    public function test_admin_can_update_course_enrollment_form_via_api(): void
    {
        $admin = User::where('role', 'admin')->first();
        $course = Course::where('is_published', true)->first();

        // Apply Quick Lead template (No email required)
        $response = $this->actingAs($admin)->postJson("/admin/courses/{$course->id}/enrollment-form", [
            'enrollment_template' => 'quick_lead'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $course->refresh();
        $this->assertFalse($course->isEmailRequired());
        $this->assertFalse($course->isEmailEnabled());
    }

    public function test_guest_can_enroll_in_quick_lead_course_without_email(): void
    {
        $course = Course::where('is_published', true)->first();
        $course->update([
            'enrollment_fields' => Course::getBuiltinTemplates()['quick_lead']['fields']
        ]);

        $this->assertFalse($course->isEmailRequired());

        // Guest submits enrollment without providing email
        $response = $this->postJson("/courses/{$course->id}/express-enroll", [
            'name' => 'Kazi Nazrul',
            'phone' => '01812345678',
            'training_mode' => 'online_zoom',
            'payment_method_id' => 2,
            'sender_number' => '01812345678',
            'transaction_id' => 'QL-TRX-1234',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => 'pending'
        ]);

        // Student created automatically with phone-based synthetic email
        $this->assertDatabaseHas('users', [
            'name' => 'Kazi Nazrul',
            'phone' => '01812345678',
        ]);

        // Payment record created
        $this->assertDatabaseHas('payments', [
            'course_id' => $course->id,
            'transaction_id' => 'QL-TRX-1234',
        ]);
    }

    public function test_standard_course_requires_email(): void
    {
        $course = Course::where('is_published', true)->first();
        $course->update([
            'enrollment_fields' => Course::getBuiltinTemplates()['standard']['fields']
        ]);

        $this->assertTrue($course->isEmailRequired());

        // Guest attempts to submit without email
        $response = $this->postJson("/courses/{$course->id}/express-enroll", [
            'name' => 'Jasim Uddin',
            'phone' => '01712345678',
            'training_mode' => 'online_zoom',
            'payment_method_id' => 2,
            'sender_number' => '01712345678',
            'transaction_id' => 'STD-TRX-1234',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_corporate_course_captures_and_stores_custom_fields(): void
    {
        $course = Course::where('is_published', true)->first();
        $course->update([
            'enrollment_fields' => Course::getBuiltinTemplates()['corporate']['fields']
        ]);

        // Submit enrollment with organization and designation
        $response = $this->postJson("/courses/{$course->id}/express-enroll", [
            'name' => 'Mahmudul Hasan',
            'phone' => '01912345678',
            'email' => 'mahmud@apexfootwear.com',
            'custom_fields' => [
                'organization' => 'Apex Footwear Ltd.',
                'designation' => 'Commercial Manager',
            ],
            'training_mode' => 'in_person',
            'payment_method_id' => 2,
            'sender_number' => '01912345678',
            'transaction_id' => 'CORP-TRX-8899',
        ]);

        $response->assertStatus(200);

        // Verify payment record has form_data saved with organization & designation
        $payment = Payment::where('transaction_id', 'CORP-TRX-8899')->first();
        $this->assertNotNull($payment);
        $this->assertIsArray($payment->form_data);
        $this->assertEquals('Apex Footwear Ltd.', $payment->form_data['organization']);
        $this->assertEquals('Commercial Manager', $payment->form_data['designation']);
        $this->assertStringContainsString('Apex Footwear Ltd.', $payment->notes);
    }
}
