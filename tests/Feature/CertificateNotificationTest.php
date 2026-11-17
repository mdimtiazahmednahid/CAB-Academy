<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use App\Notifications\AdminAnnouncementNotification;
use App\Notifications\CertificateIssuedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CertificateNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_issuing_certificate_notifies_and_emails_student(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create([
            'role' => 'student',
            'email' => 'graduate@example.com',
        ]);
        $course = Course::create([
            'instructor_id' => $admin->id,
            'title' => 'Executive L/C Operations',
            'slug' => 'executive-lc-operations',
            'description' => 'Trade finance mastery.',
            'price' => 0,
            'is_free' => true,
            'certificate_enabled' => true,
            'is_published' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.courses.certificates.issue', [
            'course' => $course->id,
            'student' => $student->id,
        ]));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Notification::assertSentTo(
            $student,
            CertificateIssuedNotification::class,
            function (CertificateIssuedNotification $notification, array $channels) use ($course) {
                return in_array('database', $channels, true)
                    && in_array('mail', $channels, true)
                    && $notification->certificate->course_id === $course->id;
            }
        );
    }

    public function test_one_click_notify_and_issue_all_and_announcement_email(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create([
            'role' => 'student',
            'email' => 'cohort@example.com',
        ]);
        $course = Course::create([
            'instructor_id' => $admin->id,
            'title' => 'Supply Chain Strategy',
            'slug' => 'supply-chain-strategy',
            'description' => 'Supply chain mastery.',
            'price' => 0,
            'is_free' => true,
            'is_published' => true,
        ]);
        $student->enrolledCourses()->attach($course->id);

        // 1. Issue & Notify All enrolled students in 1 click
        $this->actingAs($admin)
            ->post(route('admin.courses.certificates.issue_all', $course))
            ->assertRedirect()
            ->assertSessionHas('success');

        $cert = Certificate::where('user_id', $student->id)->where('course_id', $course->id)->first();
        $this->assertNotNull($cert);

        // 2. 1-Click Notify & Email button on Certificates Hub
        $this->actingAs($admin)
            ->post(route('admin.certificates.notify', $cert))
            ->assertRedirect()
            ->assertSessionHas('success');

        Notification::assertSentToTimes($student, CertificateIssuedNotification::class, 2);

        // 3. Announcement with send_email = 1
        $this->actingAs($admin)
            ->post(route('admin.announcements.store'), [
                'title' => 'Live Session Tomorrow',
                'message' => 'Join at 8:30 PM.',
                'audience' => 'students',
                'send_email' => '1',
            ])
            ->assertRedirect();

        Notification::assertSentTo(
            $student,
            AdminAnnouncementNotification::class,
            fn (AdminAnnouncementNotification $n, array $channels) => in_array('database', $channels, true) && in_array('mail', $channels, true)
        );
    }
}
