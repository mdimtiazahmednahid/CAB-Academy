<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_receives_in_app_notifications_for_pending_processing_and_verified_payment(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'student']);
        $course = Course::create([
            'title' => 'Supply Chain Mastery',
            'description' => 'Comprehensive supply chain training',
            'price' => 2500,
            'is_published' => true,
            'instructor_id' => $admin->id,
        ]);
        $method = PaymentMethod::create([
            'provider_name' => 'bKash',
            'account_number' => '01711111111',
            'instructions' => 'Send money',
            'is_active' => true,
        ]);

        // 1. Student submits payment -> Pending notification
        $this->actingAs($student)
            ->post(route('courses.checkout.store', $course), [
                'payment_method_id' => $method->id,
                'sender_number' => '01722222222',
                'transaction_id' => 'TRX998877',
            ])
            ->assertRedirect(route('dashboard'));

        $payment = Payment::where('user_id', $student->id)->where('course_id', $course->id)->firstOrFail();

        $student->refresh();
        $this->assertCount(1, $student->notifications);
        $this->assertSame('pending', $student->notifications->first()->data['status']);
        $this->assertSame('Pending', $student->notifications->first()->data['status_label']);

        // 2. Admin sets payment to processing -> Processing notification
        $this->actingAs($admin)
            ->post(route('admin.payments.updateStatus', $payment), ['status' => 'processing'])
            ->assertRedirect();

        $student->refresh();
        $this->assertCount(2, $student->notifications);
        $processingNotice = $student->notifications->first(fn ($n) => ($n->data['status'] ?? null) === 'processing');
        $this->assertNotNull($processingNotice);
        $this->assertSame('Processing', $processingNotice->data['status_label']);

        // 3. Re-submitting same status does not duplicate notification
        $this->actingAs($admin)
            ->post(route('admin.payments.updateStatus', $payment), ['status' => 'processing'])
            ->assertRedirect();

        $this->assertCount(2, $student->fresh()->notifications);

        // 4. Admin verifies payment -> Congratulations Succeeded notification
        $this->actingAs($admin)
            ->post(route('admin.payments.updateStatus', $payment), ['status' => 'verified'])
            ->assertRedirect();

        $student->refresh();
        $this->assertCount(3, $student->notifications);
        $verifiedNotice = $student->notifications->first(fn ($n) => ($n->data['status'] ?? null) === 'verified');
        $this->assertNotNull($verifiedNotice);
        $this->assertSame('Succeeded', $verifiedNotice->data['status_label']);
        $this->assertStringContainsString('Congratulations!', $verifiedNotice->data['title']);
        $this->assertTrue($student->enrolledCourses()->where('courses.id', $course->id)->exists());

        // 5. Notifications page renders both Processing and Succeeded badges
        $this->actingAs($student)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Congratulations! Payment Verified')
            ->assertSee('Succeeded')
            ->assertSee('Processing');
    }
}
