<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\CommunityPost;
use App\Models\ContactInquiry;
use App\Models\Course;
use App\Models\FinancialExpense;
use App\Models\Instructor;
use App\Models\JobPost;
use App\Models\Lesson;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Quiz;
use App\Models\Section;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCanUpdateEverythingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@cab-academy.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    /** 0. Academy Director (commercialacademy868@gmail.com) Can Also Update */
    public function test_academy_director_email_can_update_settings_and_courses()
    {
        $director = User::create([
            'name' => 'Academy Director',
            'email' => 'commercialacademy868@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $course = Course::create(['title' => 'Original', 'price' => 1000]);

        $res = $this->actingAs($director)->put("/admin/courses/{$course->id}", [
            'title' => 'Updated by Director',
            'price' => 2000,
        ]);

        $res->assertSessionHas('success');
        $this->assertDatabaseHas('courses', ['id' => $course->id, 'title' => 'Updated by Director']);
    }

    /** 1. Course Updates */
    public function test_admin_can_update_course()
    {
        $course = Course::create([
            'title' => 'Initial Title',
            'description' => 'Initial Description',
            'price' => 5000,
            'is_published' => false,
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/courses/{$course->id}", [
            'title' => 'Updated Course Title',
            'description' => 'Updated Course Description',
            'price' => 7500,
            'is_published' => '1',
            'level' => 'Certificate',
            'category' => 'Export – Import',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'title' => 'Updated Course Title',
            'price' => 7500,
            'is_published' => true,
        ]);
    }

    /** 2. Section & Lesson Updates */
    public function test_admin_can_update_section_and_lesson()
    {
        $course = Course::create(['title' => 'Master Course', 'price' => 5000]);
        $section = Section::create(['course_id' => $course->id, 'title' => 'Old Section', 'order' => 1]);
        $lesson = Lesson::create(['section_id' => $section->id, 'title' => 'Old Lesson', 'order' => 1]);

        // Update Section
        $secRes = $this->actingAs($this->admin)->put("/admin/courses/{$course->id}/sections/{$section->id}", [
            'title' => 'New Section Title',
            'order' => 2,
        ]);
        $secRes->assertSessionHas('success');
        $this->assertDatabaseHas('sections', ['id' => $section->id, 'title' => 'New Section Title']);

        // Update Lesson
        $lesRes = $this->actingAs($this->admin)->put("/admin/courses/{$course->id}/sections/{$section->id}/lessons/{$lesson->id}", [
            'title' => 'New Lesson Title',
            'content' => 'Updated lecture content.',
            'order' => 2,
        ]);
        $lesRes->assertSessionHas('success');
        $this->assertDatabaseHas('lessons', ['id' => $lesson->id, 'title' => 'New Lesson Title']);
    }

    /** 3. Quiz Updates */
    public function test_admin_can_update_quiz()
    {
        $course = Course::create(['title' => 'Quiz Course', 'price' => 5000]);
        $quiz = Quiz::create(['course_id' => $course->id, 'title' => 'Old Quiz Title', 'is_published' => false]);

        $response = $this->actingAs($this->admin)->put("/admin/courses/{$course->id}/quizzes/{$quiz->id}", [
            'title' => 'New Quiz Title',
            'description' => 'Updated quiz syllabus.',
            'is_published' => '1',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('quizzes', [
            'id' => $quiz->id,
            'title' => 'New Quiz Title',
            'is_published' => true,
        ]);
    }

    /** 4. Category Updates */
    public function test_admin_can_update_category()
    {
        $cat = Category::create([
            'name' => 'Old Category',
            'slug' => 'old-category',
            'icon' => 'fa-book',
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/categories/{$cat->id}", [
            'name' => 'New Executive Category',
            'slug' => 'new-executive-category',
            'icon' => 'fa-graduation-cap',
            'is_active' => '1',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('categories', [
            'id' => $cat->id,
            'name' => 'New Executive Category',
            'slug' => 'new-executive-category',
        ]);
    }

    /** 5. Instructor Updates */
    public function test_admin_can_update_instructor()
    {
        $inst = Instructor::create([
            'name' => 'Old Faculty Title',
            'title' => 'Old Fellow',
            'expertise' => 'Trade',
            'email' => 'old@cab-academy.com',
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/instructors/{$inst->id}", [
            'name' => 'Faculty Chair – Trade & Customs',
            'title' => 'Executive Fellow in Trade Finance',
            'expertise' => 'Banking, L/C, Customs & Shipping Procedure',
            'email' => 'trade.faculty@cab-academy.com',
            'phone' => '01917-054664',
            'is_active' => '1',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('instructors', [
            'id' => $inst->id,
            'name' => 'Faculty Chair – Trade & Customs',
            'email' => 'trade.faculty@cab-academy.com',
        ]);
    }

    /** 6. Payment Method Updates */
    public function test_admin_can_update_payment_method()
    {
        $pm = PaymentMethod::create([
            'provider_name' => 'bKash Merchant',
            'account_number' => '01917-054664',
            'account_type' => 'Merchant',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/payment-methods/{$pm->id}", [
            'provider_name' => 'bKash Corporate Merchant',
            'account_number' => '01917-054664',
            'account_type' => 'Corporate Account',
            'is_active' => '1',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('payment_methods', [
            'id' => $pm->id,
            'provider_name' => 'bKash Corporate Merchant',
            'account_type' => 'Corporate Account',
        ]);
    }

    /** 7. Payment Status (Verification & Enrollment) */
    public function test_admin_can_update_payment_status()
    {
        $student = User::create(['name' => 'Learner', 'email' => 'learner@test.com', 'password' => bcrypt('password'), 'role' => 'student']);
        $course = Course::create(['title' => 'Admission Course', 'price' => 5000]);
        $payment = Payment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'amount' => 5000,
            'sender_number' => '01700000000',
            'transaction_id' => 'TRXTEST123',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/payments/{$payment->id}/status", [
            'status' => 'verified',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => 'verified']);
        $this->assertTrue($student->fresh()->enrolledCourses->contains($course->id));
    }

    /** 8. Inquiry Status */
    public function test_admin_can_update_inquiry_status()
    {
        $inquiry = ContactInquiry::create([
            'name' => 'Inquirer',
            'email' => 'inq@test.com',
            'phone' => '01700000000',
            'subject' => 'Corporate Training Inquiry',
            'message' => 'Need batch discount.',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/inquiries/{$inquiry->id}/status", [
            'status' => 'contacted',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('contact_inquiries', ['id' => $inquiry->id, 'status' => 'contacted']);
    }

    /** 9. Accounting Updates (Expenses & Tuition) */
    public function test_admin_can_record_tuition_and_expense()
    {
        $course = Course::create(['title' => 'Accounting Course', 'price' => 6000]);

        // Record Tuition
        $tuitionRes = $this->actingAs($this->admin)->post('/admin/accounting/tuition', [
            'student_name' => 'Executive Batch Student',
            'student_email' => 'executive@batch.com',
            'student_phone' => '01917054664',
            'course_id' => $course->id,
            'amount' => 6000,
            'payment_type' => 'campus_cash',
            'notes' => 'Received at campus desk.',
        ]);
        $tuitionRes->assertSessionHas('success');
        $this->assertDatabaseHas('payments', ['amount' => 6000, 'status' => 'verified']);

        // Record Expense
        $expRes = $this->actingAs($this->admin)->post('/admin/accounting/expenses', [
            'title' => 'Classroom Facility Rent',
            'category' => 'Infrastructure',
            'amount' => 15000,
            'expense_date' => date('Y-m-d'),
            'paid_to' => 'Facility Vendor Ltd.',
            'payment_method' => 'bank_transfer',
        ]);
        $expRes->assertSessionHas('success');
        $this->assertDatabaseHas('financial_expenses', ['title' => 'Classroom Facility Rent', 'amount' => 15000]);
    }

    /** 10. Community Feed Updates (Pin & Broadcast) */
    public function test_admin_can_moderate_feed_and_pin_posts()
    {
        $post = CommunityPost::create([
            'user_id' => $this->admin->id,
            'author_name' => 'Faculty Chair',
            'category' => 'General',
            'title' => 'Discussion Topic',
            'content' => 'Sample discussion text.',
            'is_pinned' => false,
        ]);

        // Toggle Pin
        $pinRes = $this->actingAs($this->admin)->post("/admin/feeds/{$post->id}/pin");
        $pinRes->assertSessionHas('success');
        $this->assertTrue($post->fresh()->is_pinned);

        // Broadcast Announcement
        $broadRes = $this->actingAs($this->admin)->post('/admin/feeds/broadcast', [
            'title' => 'Official Campus Notice',
            'content' => 'Friday live workshop details.',
            'category' => 'Official Insights',
            'author_title' => 'Academy Directorate',
            'is_pinned' => '1',
        ]);
        $broadRes->assertSessionHas('success');
        $this->assertDatabaseHas('community_posts', ['title' => 'Official Campus Notice', 'is_pinned' => true]);
    }

    /** 11. Blog Post Updates & Feature Toggle */
    public function test_admin_can_update_blog_and_toggle_featured()
    {
        $blog = BlogPost::create([
            'title' => 'Old Article Title',
            'slug' => 'old-article-title',
            'category' => 'Export – Import',
            'excerpt' => 'Short summary.',
            'content' => 'Full article body content.',
            'author_name' => 'Faculty Chair – Trade & Customs',
            'is_published' => false,
            'is_featured' => false,
        ]);

        // Update Blog Post
        $updateRes = $this->actingAs($this->admin)->put("/admin/blogs/{$blog->id}", [
            'title' => 'Updated Article Title',
            'category' => 'Supply Chain Management',
            'excerpt' => 'Updated executive summary.',
            'content' => 'Updated full research article.',
            'author_name' => 'Faculty Chair – Supply Chain',
            'is_published' => '1',
            'is_featured' => '1',
        ]);
        $updateRes->assertSessionHas('success');
        $this->assertDatabaseHas('blog_posts', [
            'id' => $blog->id,
            'title' => 'Updated Article Title',
            'is_published' => true,
            'is_featured' => true,
        ]);

        // Toggle Feature
        $featureRes = $this->actingAs($this->admin)->post("/admin/blogs/{$blog->id}/feature");
        $this->assertFalse($blog->fresh()->is_featured);
    }

    /** 12. Global Platform Settings */
    public function test_admin_can_update_global_settings()
    {
        $response = $this->actingAs($this->admin)->post('/admin/settings', [
            'site_name' => 'CAB Academy Corporate',
            'contact_phone' => '01917-054664',
            'contact_email' => 'commercialacademy868@gmail.com',
            'contact_address' => 'Mirpur-Pallabi Campus, Dhaka',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('CAB Academy Corporate', Setting::getVal('site_name'));
        $this->assertEquals('01917-054664', Setting::getVal('contact_phone'));
    }

    /** 13. Landing Page Frontend Customizer */
    public function test_admin_can_update_frontend_landing_page()
    {
        $response = $this->actingAs($this->admin)->post('/admin/frontend', [
            'landing_cms_form_submitted' => '1',
            'landing_hero_badge' => 'Executive Master Track',
            'landing_hero_title' => 'Premier Academy for International Trade',
            'landing_clients_title' => 'Industry Sectors & Enterprise Training',
            'landing_clients_data' => "Trade Finance\nSupply Chain",
            'landing_hero_enabled' => '1',
            'landing_clients_enabled' => '1',
            'landing_categories_enabled' => '1',
            'landing_courses_enabled' => '1',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('Executive Master Track', Setting::getVal('landing_hero_badge'));
        $this->assertEquals('Industry Sectors & Enterprise Training', Setting::getVal('landing_clients_title'));
    }

    /** 14. User Profile & Preferences Updates */
    public function test_admin_can_update_user_and_preferences()
    {
        $user = User::create([
            'name' => 'Old Name',
            'email' => 'user@cab.com',
            'password' => bcrypt('password'),
            'role' => 'student',
        ]);

        // Update User Details
        $userRes = $this->actingAs($this->admin)->put("/admin/users/{$user->id}", [
            'name' => 'Updated User Name',
            'email' => 'updated.user@cab.com',
            'role' => 'instructor',
        ]);
        $userRes->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated User Name',
            'role' => 'instructor',
        ]);

        // Update Preferences
        $prefRes = $this->actingAs($this->admin)->put("/admin/users/{$user->id}/preferences", [
            'level' => 'Certificate',
            'subjects' => 'Export – Import, Supply Chain',
            'goals' => 'Executive Mastery',
        ]);
        $prefRes->assertSessionHas('success');
        $this->assertEquals('Certificate', $user->fresh()->preferences['level']);
    }

    /** 15. Job Post Updates */
    public function test_admin_can_update_job_post()
    {
        $job = JobPost::create([
            'title' => 'Junior Role',
            'company' => 'Old Logistics Ltd.',
            'location' => 'Dhaka',
            'salary_range' => '৳30k - ৳40k',
            'description' => 'Initial job description.',
            'apply_link' => '#',
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/jobs/{$job->id}", [
            'title' => 'Commercial Operations Specialist',
            'company' => 'Global Logistics & Trade Solutions Ltd.',
            'location' => 'Dhaka (Mirpur)',
            'salary_range' => '৳50,000 - ৳70,000',
            'description' => 'Updated comprehensive job requirements.',
            'apply_link' => '#',
            'is_active' => '1',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('job_posts', [
            'id' => $job->id,
            'title' => 'Commercial Operations Specialist',
            'is_active' => true,
        ]);
    }

    /** 16. Recycle Bin / Trash (Restore & Purge) */
    public function test_admin_can_restore_and_force_delete_from_trash()
    {
        $course = Course::create(['title' => 'Trashed Course', 'price' => 1000]);
        $course->delete();

        // Restore
        $restoreRes = $this->actingAs($this->admin)->post("/admin/trash/course/{$course->id}/restore");
        $restoreRes->assertSessionHas('success');
        $this->assertNull($course->fresh()->deleted_at);

        // Delete again and Force Delete
        $course->delete();
        $forceRes = $this->actingAs($this->admin)->delete("/admin/trash/course/{$course->id}/force");
        $forceRes->assertSessionHas('success');
        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }
}
