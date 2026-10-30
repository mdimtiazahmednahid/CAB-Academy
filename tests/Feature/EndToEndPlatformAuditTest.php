<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\FinancialExpense;
use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\Lesson;
use App\Models\Option;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Section;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EndToEndPlatformAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_end_to_end_platform_lifecycle_across_public_student_and_admin_portals(): void
    {
        Storage::fake('public');

        // 1. Seed initial environment & payment method
        $admin = User::factory()->create([
            'name' => 'Executive Director',
            'email' => 'director@cabacademy.com',
            'role' => 'admin',
        ]);

        $bkash = PaymentMethod::create([
            'provider_name' => 'bKash Merchant',
            'account_number' => '01917-054664',
            'account_type' => 'Merchant',
            'instructions' => 'Send payment via bKash Merchant.',
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Export-Import & L/C Operations',
            'slug' => 'export-import-lc',
            'icon' => 'fa-ship',
            'is_active' => true,
            'order' => 1,
        ]);

        $onlineCourse = Course::create([
            'title' => 'Executive Masterclass in L/C & Customs Procedure',
            'description' => 'Comprehensive L/C and customs mastery.',
            'category' => $category->name,
            'level' => 'Certificate',
            'venue' => 'Online Live (Zoom)',
            'price' => 4500.00,
            'is_published' => true,
            'certificate_enabled' => true,
            'instructor_id' => $admin->id,
        ]);

        $section = Section::create([
            'course_id' => $onlineCourse->id,
            'title' => 'Module 1: UCP 600 & Documentary Credit',
            'order' => 1,
        ]);

        $lesson1 = Lesson::create([
            'section_id' => $section->id,
            'title' => 'Lesson 1: Opening Irrevocable L/C',
            'content' => 'Detailed UCP 600 walkthrough.',
            'order' => 1,
        ]);

        $lesson2 = Lesson::create([
            'section_id' => $section->id,
            'title' => 'Lesson 2: Bill of Lading & Customs Assessment',
            'content' => 'Shipping & customs clearance guide.',
            'order' => 2,
        ]);

        $quiz = Quiz::create([
            'course_id' => $onlineCourse->id,
            'title' => 'UCP 600 Final Assessment',
            'description' => 'Certification Quiz',
            'is_published' => true,
        ]);

        $question = Question::create([
            'quiz_id' => $quiz->id,
            'question_text' => 'Which ICC publication governs Documentary Credits?',
        ]);

        $correctOpt = Option::create([
            'question_id' => $question->id,
            'option_text' => 'UCP 600',
            'is_correct' => true,
        ]);
        Option::create([
            'question_id' => $question->id,
            'option_text' => 'INCOTERMS 2010',
            'is_correct' => false,
        ]);

        $jobPost = JobPost::create([
            'title' => 'Head of Commercial & Export-Import',
            'company' => 'Apex Global Apparel PLC',
            'location' => 'Dhaka',
            'job_type' => 'Full-time',
            'salary_range' => '৳85,000 - ৳120,000',
            'description' => 'Lead international trade operations and L/C negotiations.',
            'is_active' => true,
        ]);

        // 2. Public Discovery Pages Verification
        $this->get('/')->assertStatus(200)->assertSee('Executive Masterclass in L/C');
        $this->get('/catalog')->assertStatus(200)->assertSee('Executive Masterclass in L/C');
        $this->get('/jobs')->assertStatus(200)->assertSee('Head of Commercial & Export-Import');
        $this->get('/instructors')->assertStatus(200);
        $this->get('/blog')->assertStatus(200);
        $this->get('/contact')->assertStatus(200);
        $this->get('/sitemap.xml')->assertStatus(200);

        // Submit public counseling inquiry
        $this->post('/contact', [
            'name' => 'Corporate HR Lead',
            'email' => 'hr@apexglobal.com',
            'phone' => '01711334455',
            'inquiry_type' => 'corporate',
            'subject' => 'Group Enrollment for 15 Executives',
            'message' => 'We would like to enroll 15 commercial officers.',
        ])->assertRedirect();

        // 3. Guest Express Enrollment -> Enforces Payment on Paid Course & Auto-Authenticates Student
        $unpaidAttempt = $this->postJson(route('courses.express_enroll', $onlineCourse), [
            'name' => 'Rafiqul Islam',
            'phone' => '01918223344',
            'email' => 'rafiq@trade.com',
            'password' => '7788',
            'training_mode' => 'online_zoom',
            'transaction_id' => '',
        ]);
        $unpaidAttempt->assertStatus(422)->assertJsonValidationErrors(['transaction_id']);

        $paidEnroll = $this->postJson(route('courses.express_enroll', $onlineCourse), [
            'name' => 'Rafiqul Islam',
            'phone' => '01918223344',
            'email' => 'rafiq@trade.com',
            'password' => '7788',
            'training_mode' => 'in_person', // Should auto-lock to online_zoom since course is Online Live (Zoom)
            'payment_method_id' => $bkash->id,
            'sender_number' => '01918223344',
            'transaction_id' => 'BKASH-E2E-9001',
        ]);
        $paidEnroll->assertStatus(200)->assertJson([
            'success' => true,
            'status' => 'pending',
        ]);

        $student = User::where('email', 'rafiq@trade.com')->firstOrFail();
        $this->assertAuthenticatedAs($student);

        // 4. Student Visits Redesigned Dashboard While Payment is Pending
        $pendingDash = $this->actingAs($student)->get(route('dashboard'));
        $pendingDash->assertStatus(200);
        $pendingDash->assertSee('Reserved Seat');
        $pendingDash->assertSee('BKASH-E2E-9001');
        $pendingDash->assertSee('Payment Verification in Progress');

        // 5. Admin Visits Redesigned Command Center Dashboard & Approves Payment + Broadcasts Course Announcement
        FinancialExpense::create([
            'title' => 'Zoom Enterprise Cloud License',
            'category' => 'Software & Infrastructure',
            'amount' => 1200.00,
            'paid_to' => 'Zoom Video Communications',
            'expense_date' => now()->toDateString(),
            'created_by' => $admin->id,
        ]);

        $adminDash = $this->actingAs($admin)->get(route('admin.dashboard'));
        $adminDash->assertStatus(200);
        $adminDash->assertSee('Platform Overview &amp; Executive Command', false);
        $adminDash->assertSee('6-Month Revenue &amp; Enrollment Velocity', false);
        $adminDash->assertSee('Treasury Summary');
        $adminDash->assertSee('BKASH-E2E-9001');
        $adminDash->assertSee('Rafiqul Islam');

        $payment = Payment::where('transaction_id', 'BKASH-E2E-9001')->firstOrFail();
        $this->actingAs($admin)->post(route('admin.payments.updateStatus', $payment), [
            'status' => 'verified',
        ])->assertRedirect();

        $this->assertTrue($student->fresh()->enrolledCourses()->where('courses.id', $onlineCourse->id)->exists());

        // Admin sends targeted announcement to this course
        $this->actingAs($admin)->post(route('admin.announcements.store'), [
            'title' => 'Orientation Zoom Link Ready',
            'message' => 'Welcome to Batch 206! Join via your classroom dashboard.',
            'audience' => 'course:' . $onlineCourse->id,
        ])->assertRedirect();

        // 6. Student Completes Lesson, Takes Quiz, Applies to Job, and Views Updated Dashboard
        $this->actingAs($student)->post(route('lessons.complete', [$onlineCourse, $lesson1]))->assertRedirect();

        $this->actingAs($student)->post(route('quizzes.store', [$onlineCourse, $quiz]), [
            'question_' . $question->id => $correctOpt->id,
        ])->assertRedirect();

        $this->actingAs($student)->post(route('jobs.apply', $jobPost), [
            'resume' => UploadedFile::fake()->create('rafiq_cv.pdf', 120, 'application/pdf'),
            'cover_letter' => 'Certified in L/C and Customs from CAB Academy.',
        ])->assertRedirect();

        $verifiedDash = $this->actingAs($student)->get(route('dashboard'));
        $verifiedDash->assertStatus(200);
        $verifiedDash->assertSee('Active Classroom Spotlight');
        $verifiedDash->assertSee('50% Complete');
        $verifiedDash->assertSee('Lesson 2: Bill of Lading &amp; Customs Assessment', false);
        $verifiedDash->assertSee('Orientation Zoom Link Ready');
        $verifiedDash->assertSee('Digital Certificate');
        $verifiedDash->assertSee('Head of Commercial &amp; Export-Import', false);

        // 7. Student Generates & Verifies Official Certificate & Printable Tuition Invoice
        $certResp = $this->actingAs($student)->get(route('courses.certificate', $onlineCourse));
        $certResp->assertStatus(200)->assertSee('Rafiqul Islam');

        $certRecord = Certificate::where('user_id', $student->id)->where('course_id', $onlineCourse->id)->firstOrFail();
        $this->get(route('certificate.verify', $certRecord->certificate_number))
            ->assertStatus(200)
            ->assertSee('Rafiqul Islam');

        $this->actingAs($student)->get(route('payments.invoice', $payment))
            ->assertStatus(200)
            ->assertSee('BKASH-E2E-9001');
    }

    public function test_instructor_profile_and_avatar_sync_everywhere_across_platform(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create([
            'role' => 'student',
            'preferences' => ['level' => 'Certificate', 'subjects' => ['Export – Import'], 'goals' => ['Certification']],
        ]);

        // Create 2 instructors where Instructor #1 has user_id = 3 and Instructor #3 has id = 3 (testing ID collision immunity)
        $instructor1 = \App\Models\Instructor::create([
            'name' => 'Faculty Chair – Trade & Customs',
            'title' => 'Executive Fellow in Trade',
            'expertise' => 'L/C & Customs',
            'email' => 'trade.faculty@cab-academy.com',
            'order' => 1,
            'is_active' => true,
        ]);

        $instructor2 = \App\Models\Instructor::create([
            'name' => 'Faculty Chair – Supply Chain',
            'title' => 'Executive Fellow in SCM',
            'expertise' => 'Supply Chain',
            'email' => 'scm.faculty@cab-academy.com',
            'order' => 2,
            'is_active' => true,
        ]);

        $course1 = Course::create([
            'title' => 'Certificate Course in L/C, Banking, Customs & Shipping',
            'description' => 'Comprehensive trade training.',
            'price' => 9000,
            'is_published' => true,
            'level' => 'Certificate',
            'category' => 'Export – Import',
            'instructor_id' => $instructor1->user_id,
        ]);

        $course2 = Course::create([
            'title' => 'Certificate Course in Supply Chain Management',
            'description' => 'Comprehensive SCM training.',
            'price' => 8500,
            'is_published' => true,
            'level' => 'Certificate',
            'category' => 'Supply Chain Management',
            'instructor_id' => $instructor2->user_id,
        ]);

        // Verify Course::booted() did NOT corrupt course1->instructor_id
        $this->assertEquals($instructor1->user_id, $course1->fresh()->instructor_id);

        // Admin updates Instructor #1 with a new Name, Title, Bio, and Base64 Avatar
        $fakePngBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        $this->actingAs($admin)->post(route('admin.instructors.update', $instructor1), [
            'name' => 'Barrister Zubair Chowdhury',
            'title' => 'Principal International Trade & Customs Advisor',
            'expertise' => 'UCP 600, L/C & Customs Valuation',
            'bio' => 'Veteran customs and trade finance specialist with 22 years of advisory leadership.',
            'email' => 'trade.faculty@cab-academy.com',
            'phone' => '01917054664',
            'order' => 1,
            'is_active' => 1,
            'avatar_base64' => $fakePngBase64,
        ])->assertRedirect(route('admin.instructors.index'));

        $instructor1->refresh();
        $this->assertEquals('Barrister Zubair Chowdhury', $instructor1->name);
        $this->assertNotNull($instructor1->avatar);
        $this->assertEquals('Barrister Zubair Chowdhury', $instructor1->user->fresh()->name);
        $this->assertEquals($instructor1->avatar, $instructor1->user->fresh()->profile_picture);

        $expectedAvatarUrl = $instructor1->avatar_url;

        // 1. Home Page (/): Course cards corner avatar & Faculty section must show updated name and avatar URL
        $homeResp = $this->get('/');
        $homeResp->assertStatus(200);
        $homeResp->assertSee('Barrister Zubair Chowdhury');
        $homeResp->assertSee('Principal International Trade &amp; Customs Advisor', false);
        $homeResp->assertSee($expectedAvatarUrl, false);

        // 2. Course Catalog (/catalog & /courses): Course card corner avatar must show updated name and avatar URL
        $catalogResp = $this->get(route('catalog.index'));
        $catalogResp->assertStatus(200);
        $catalogResp->assertSee('Barrister Zubair Chowdhury');
        $catalogResp->assertSee($expectedAvatarUrl, false);

        // 3. Single Course Overview (/courses/{course}): Hero badge, JSON-LD schema, and Instructor Profile card must all sync
        $courseShowResp = $this->get(route('courses.show', $course1));
        $courseShowResp->assertStatus(200);
        $courseShowResp->assertSee('Barrister Zubair Chowdhury');
        $courseShowResp->assertSee('Principal International Trade &amp; Customs Advisor', false);
        $courseShowResp->assertSee('Veteran customs and trade finance specialist with 22 years of advisory leadership.');
        $courseShowResp->assertSee($expectedAvatarUrl, false);

        // Even Course #2 (whose assigned instructor2 was still an uncustomized 'Faculty Chair – ...' placeholder) syncs to the customized faculty!
        $course2ShowResp = $this->get(route('courses.show', $course2));
        $course2ShowResp->assertStatus(200);
        $course2ShowResp->assertSee('Barrister Zubair Chowdhury');
        $course2ShowResp->assertSee($expectedAvatarUrl, false);

        // 4. Short Share Preview (/c/{course}): Must show updated instructor
        $shareResp = $this->get(route('courses.short', $course1));
        $shareResp->assertStatus(200);
        $shareResp->assertSee('Barrister Zubair Chowdhury');
        $shareResp->assertSee($expectedAvatarUrl, false);

        // 5. Public & Student Portal Faculty Directory (/instructors):
        $facultyResp = $this->actingAs($student)->get(route('instructors.index'));
        $facultyResp->assertStatus(200);
        $facultyResp->assertSee('Barrister Zubair Chowdhury');
        $facultyResp->assertSee('Student Portal'); // Confirms layouts.student is used for logged-in student
        $facultyResp->assertSee($expectedAvatarUrl, false);

        // 6. Admin Courses List & Admin Course Show:
        $this->actingAs($admin)->get(route('admin.courses'))
            ->assertStatus(200)
            ->assertSee('Barrister Zubair Chowdhury');

        $this->actingAs($admin)->get(route('admin.courses.show', $course1))
            ->assertStatus(200)
            ->assertSee('Barrister Zubair Chowdhury');
    }
}
