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
}
