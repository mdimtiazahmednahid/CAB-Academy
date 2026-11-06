<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\Lesson;
use App\Models\Payment;
use App\Models\Quiz;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataIntegrityAndSecurityAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_express_enroll_does_not_allow_account_takeover_of_existing_admin(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin Director',
            'email' => 'admin@cab-academy.com',
            'role' => 'admin',
        ]);

        $course = Course::create([
            'title' => 'Executive Trade Finance',
            'price' => 5000,
            'is_published' => true,
        ]);

        $response = $this->postJson(route('courses.express_enroll', $course), [
            'name' => 'Attacker Guest',
            'phone' => '01917054664',
            'email' => 'admin@cab-academy.com',
            'training_mode' => 'online_zoom',
            'sender_number' => '01917054664',
            'transaction_id' => 'TRX-ATTACK-1',
        ]);

        $response->assertStatus(200);
        // Must NOT authenticate the guest into the existing admin session
        $this->assertGuest();
    }

    public function test_express_enroll_deduplicates_pending_payments_for_same_course(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = Course::create([
            'title' => 'Bonded Warehouse Operations',
            'price' => 4500,
            'is_published' => true,
        ]);

        $this->actingAs($student)->postJson(route('courses.express_enroll', $course), [
            'name' => $student->name,
            'phone' => '01917054664',
            'training_mode' => 'online_zoom',
            'sender_number' => '01917054664',
            'transaction_id' => 'TRX-FIRST',
        ])->assertOk();

        $this->actingAs($student)->postJson(route('courses.express_enroll', $course), [
            'name' => $student->name,
            'phone' => '01917054664',
            'training_mode' => 'in_person',
            'sender_number' => '01917054664',
            'transaction_id' => 'TRX-UPDATED',
        ])->assertOk();

        $this->assertEquals(
            1,
            Payment::where('user_id', $student->id)
                ->where('course_id', $course->id)
                ->where('status', 'pending')
                ->count()
        );
        $this->assertDatabaseHas('payments', [
            'user_id' => $student->id,
            'course_id' => $course->id,
            'transaction_id' => 'TRX-UPDATED',
        ]);
    }

    public function test_unenrolled_student_cannot_access_paid_course_lessons_or_quizzes(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = Course::create([
            'title' => 'Restricted Masterclass',
            'price' => 6000,
            'is_published' => true,
        ]);
        $section = Section::create([
            'course_id' => $course->id,
            'title' => 'Module 1',
            'order' => 1,
        ]);
        $lesson = Lesson::create([
            'section_id' => $section->id,
            'title' => 'Secret Lesson',
            'order' => 1,
        ]);
        $quiz = Quiz::create([
            'course_id' => $course->id,
            'title' => 'Final Assessment',
            'is_published' => true,
        ]);

        $this->actingAs($student)
            ->get(route('lessons.show', [$course, $lesson]))
            ->assertRedirect(route('courses.show', $course));

        $this->actingAs($student)
            ->get(route('quizzes.show', [$course, $quiz]))
            ->assertRedirect(route('courses.show', $course));
    }

    public function test_soft_deleted_course_or_job_does_not_crash_student_dashboard_or_invoice(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'preferences' => ['level' => 'Certificate'],
        ]);

        $course = Course::create([
            'title' => 'Archived Course',
            'price' => 3000,
            'is_published' => true,
        ]);

        $job = JobPost::create([
            'title' => 'Archived Trade Role',
            'company' => 'Global Trade Corp',
            'description' => 'Trade compliance role',
            'is_active' => true,
        ]);

        $payment = Payment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'amount' => 3000,
            'sender_number' => '01917054664',
            'transaction_id' => 'TRX-SOFT-DEL',
            'status' => 'verified',
        ]);

        JobApplication::create([
            'user_id' => $student->id,
            'job_post_id' => $job->id,
            'resume_path' => 'resumes/sample.pdf',
            'status' => 'pending',
        ]);

        // Soft-delete both the course and job post
        $course->delete();
        $job->delete();

        $this->actingAs($student)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Archived Course')
            ->assertSee('Archived Trade Role');

        $this->actingAs($student)
            ->get(route('payments.invoice', $payment))
            ->assertOk()
            ->assertSee('Archived Course');
    }

    public function test_storage_stream_blocks_path_traversal(): void
    {
        $this->get('/storage/../../.env')->assertStatus(404);
        $this->get('/uploads/..%2F..%2F.env')->assertStatus(404);
    }

    public function test_course_registration_status_manual_close_and_auto_close_on_start_date(): void
    {
        $course = Course::create([
            'title' => 'LC & Trade Finance Cohort',
            'price' => 2500,
            'is_published' => true,
            'registration_status' => 'open',
            'start_date' => now()->subDays(2),
        ]);

        // 1. Default 'open' remains open even if start_date is in the past
        $this->assertTrue($course->isRegistrationOpen());

        // 2. Switching to 'auto' closes registration when start_date is in the past
        $course->update(['registration_status' => 'auto']);
        $this->assertFalse($course->fresh()->isRegistrationOpen());

        // Express enroll is blocked when registration is closed
        $this->postJson("/courses/{$course->id}/express-enroll", [
            'name' => 'Test Learner',
            'phone' => '01712345678',
            'sender_number' => '01712345678',
            'transaction_id' => 'TRX-CLOSED-123',
        ])->assertStatus(422)->assertJson(['success' => false]);

        // 3. With 'auto' and a future start_date, registration is open
        $course->update(['start_date' => now()->addDays(5)]);
        $this->assertTrue($course->fresh()->isRegistrationOpen());

        // 4. Manual 'closed' closes registration regardless of future start_date
        $course->update(['registration_status' => 'closed']);
        $this->assertFalse($course->fresh()->isRegistrationOpen());
        $this->get(route('courses.show', $course))
            ->assertOk()
            ->assertSee('Registration Closed');
    }

    public function test_returning_user_phone_and_payment_autofill_and_duplicate_enrollment_prevention(): void
    {
        // 1. Phone normalization handles +880, 880, dashes, and 10-digit formats
        $this->assertSame('01712345678', User::normalizeBdPhone('+880 1712-345678'));
        $this->assertSame('01917054664', User::normalizeBdPhone('8801917054664'));
        $this->assertSame('01811223344', User::normalizeBdPhone('1811223344'));

        // 2. Mismatched user with null profile phone self-heals from previous payment
        $courseA = Course::create([
            'title' => 'Export Documentation Masterclass',
            'price' => 1500,
            'is_published' => true,
            'registration_status' => 'open',
        ]);

        $courseB = Course::create([
            'title' => 'Supply Chain Analytics Cohort',
            'price' => 2000,
            'is_published' => true,
            'registration_status' => 'open',
        ]);

        $student = User::factory()->create([
            'name' => 'Returning Executive',
            'email' => 'returning@example.com',
            'phone' => null,
            'role' => 'student',
        ]);

        $student->enrolledCourses()->attach($courseA->id);

        Payment::create([
            'user_id' => $student->id,
            'course_id' => $courseA->id,
            'amount' => 1500,
            'sender_number' => '+8801819001122',
            'transaction_id' => 'TRX-FIRST-COURSE',
            'status' => 'verified',
            'form_data' => [
                'name' => 'Returning Executive',
                'phone' => '01715998877',
                'organization' => 'Apex Logistics Ltd.',
                'designation' => 'Supply Chain Manager',
            ],
        ]);

        // Accessing effective_phone and last_sender_number resolves and normalizes both numbers
        $this->assertSame('01715998877', $student->effective_phone);
        $this->assertSame('01715998877', $student->fresh()->phone);
        $this->assertSame('01819001122', $student->last_sender_number);
        $this->assertSame('Apex Logistics Ltd.', $student->saved_enrollment_fields['organization'] ?? null);

        // 3. Logged-in user can enroll in Course B without re-sending phone or sender_number (or can edit them)
        $this->actingAs($student)
            ->postJson("/courses/{$courseB->id}/express-enroll", [
                'transaction_id' => 'TRX-SECOND-COURSE',
            ])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'status' => 'pending',
            ]);

        $secondPayment = Payment::where('user_id', $student->id)
            ->where('course_id', $courseB->id)
            ->first();
        $this->assertNotNull($secondPayment);
        $this->assertSame('01715998877', $secondPayment->form_data['phone'] ?? null);
        $this->assertSame('01715998877', $secondPayment->sender_number);

        // 4. Logged-in user can also edit their phone & sender_number during enrollment
        $this->actingAs($student)
            ->postJson("/courses/{$courseB->id}/express-enroll", [
                'phone' => '+8801611223344',
                'sender_number' => '01511223344',
                'transaction_id' => 'TRX-SECOND-UPDATED',
            ])
            ->assertOk();

        $this->assertSame('01611223344', $student->fresh()->phone);
        $this->assertSame('01511223344', $secondPayment->fresh()->sender_number);

        // 5. Duplicate enrollment is blocked for Course A (both when logged in and when submitting as guest with same phone/email)
        $this->actingAs($student)
            ->postJson("/courses/{$courseA->id}/express-enroll", [
                'transaction_id' => 'TRX-DUP-ATTEMPT',
            ])
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'status' => 'already_enrolled',
            ]);

        auth()->logout();

        $this->postJson("/courses/{$courseA->id}/express-enroll", [
            'name' => 'Returning Executive',
            'phone' => '01611223344',
            'email' => 'returning@example.com',
            'password' => '1234',
            'sender_number' => '01611223344',
            'transaction_id' => 'TRX-GUEST-DUP',
        ])
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'status' => 'already_enrolled',
            ]);

        // 6. Duplicate Transaction ID reuse across courses/users is blocked (even with lowercase / whitespace)
        $this->actingAs($student)
            ->postJson("/courses/{$courseB->id}/express-enroll", [
                'transaction_id' => '  trx-first-course  ',
            ])
            ->assertStatus(422)
            ->assertJsonPath('errors.transaction_id.0', 'This Transaction ID (TrxID) has already been used. Please enter a valid Transaction ID.');

        // 7. Guest enrollment resolves existing user even if their phone was only stored in payments.sender_number (no duplicate account)
        $studentNoProfilePhone = User::factory()->create([
            'name' => 'Legacy Student',
            'email' => 'legacy@example.com',
            'phone' => null,
            'role' => 'student',
        ]);
        Payment::create([
            'user_id' => $studentNoProfilePhone->id,
            'course_id' => $courseA->id,
            'amount' => 1500,
            'sender_number' => '01999887766',
            'transaction_id' => 'TRX-LEGACY-99',
            'status' => 'verified',
        ]);

        auth()->logout();
        $userCountBefore = User::count();

        $this->postJson("/courses/{$courseB->id}/express-enroll", [
            'name' => 'Legacy Student',
            'phone' => '+8801999-887766',
            'password' => '7766',
            'sender_number' => '01999887766',
            'transaction_id' => 'TRX-LEGACY-NEW-COURSE',
        ])->assertOk();

        $this->assertSame($userCountBefore, User::count());
        $this->assertSame('01999887766', $studentNoProfilePhone->fresh()->phone);
    }
}
