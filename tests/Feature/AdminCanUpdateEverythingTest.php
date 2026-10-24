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

    /** 17. Course Deletion (Soft Delete & Catalog Removal) */
    public function test_admin_can_delete_course_and_it_disappears_from_catalog()
    {
        $course = Course::create([
            'title' => 'Course To Be Deleted',
            'price' => 5000,
            'is_published' => true,
        ]);

        $res = $this->actingAs($this->admin)->delete("/admin/courses/{$course->id}");
        $res->assertRedirect(route('admin.courses'));
        $res->assertSessionHas('success');

        // Verify soft-deleted
        $this->assertNotNull($course->fresh()->deleted_at);
        $this->assertFalse(Course::published()->where('id', $course->id)->exists());
    }

    /** 18. Category Update Propagates to Courses & Category Deletion Nullifies Safely */
    public function test_category_update_and_delete_propagates_to_courses()
    {
        $cat = Category::create([
            'name' => 'Customs & Port',
            'slug' => 'customs-port',
            'icon' => 'fa-ship',
        ]);

        $course = Course::create([
            'title' => 'Customs Clearance Diploma',
            'category' => 'Customs & Port',
            'price' => 4500,
            'is_published' => true,
        ]);

        // 1. Update Category name - courses must update
        $updateRes = $this->actingAs($this->admin)->put("/admin/categories/{$cat->id}", [
            'name' => 'Customs, Bond & Seaport Logistics',
            'slug' => 'customs-bond-seaport',
            'icon' => 'fa-anchor',
            'is_active' => '1',
        ]);
        $updateRes->assertSessionHas('success');
        $this->assertEquals('Customs, Bond & Seaport Logistics', $course->fresh()->category);

        // 2. Delete Category - course category gracefully nullified
        $deleteRes = $this->actingAs($this->admin)->delete("/admin/categories/{$cat->id}");
        $deleteRes->assertSessionHas('success');
        $this->assertNull($course->fresh()->category);
        $this->assertDatabaseMissing('categories', ['id' => $cat->id]);
    }

    /** 19. Instructor Deletion Safely Unlinks From Assigned Courses */
    public function test_instructor_deletion_unassigns_from_courses()
    {
        $instUser = User::create([
            'name' => 'Senior Fellow',
            'email' => 'fellow@cab-academy.com',
            'password' => bcrypt('password'),
            'role' => 'instructor',
        ]);

        $instructor = Instructor::create([
            'user_id' => $instUser->id,
            'name' => 'Senior Fellow',
            'title' => 'Director of Port Operations',
            'expertise' => 'Logistics Law',
            'is_active' => true,
            'order' => 1,
        ]);

        $course = Course::create([
            'title' => 'Port Operations Masterclass',
            'instructor_id' => $instUser->id,
            'price' => 4000,
        ]);

        $res = $this->actingAs($this->admin)->delete("/admin/instructors/{$instructor->id}");
        $res->assertSessionHas('success');
        $this->assertSoftDeleted('instructors', ['id' => $instructor->id]);
        $this->assertNull($course->fresh()->instructor_id);
    }

    /** 20. User Deletion Safely Cleans Up Enrollments and Authored Courses */
    public function test_user_deletion_cleans_up_relations_gracefully()
    {
        $student = User::create([
            'name' => 'Enrolled Student',
            'email' => 'student@test.com',
            'password' => bcrypt('password'),
            'role' => 'student',
        ]);

        $course = Course::create(['title' => 'Test Course', 'price' => 2000]);
        $student->enrolledCourses()->attach($course->id);

        $res = $this->actingAs($this->admin)->delete("/admin/users/{$student->id}");
        $res->assertSessionHas('success');
        $this->assertSoftDeleted('users', ['id' => $student->id]);
        $this->assertDatabaseMissing('course_user', ['user_id' => $student->id]);
    }

    /** 21. Blog Post Deletion */
    public function test_admin_can_delete_blog_post()
    {
        $blog = BlogPost::create([
            'title' => 'Article to Delete',
            'slug' => 'article-to-delete',
            'category' => 'Customs',
            'excerpt' => 'Excerpt',
            'content' => 'Content',
            'author_name' => 'Director',
            'is_published' => true,
        ]);

        $res = $this->actingAs($this->admin)->delete("/admin/blogs/{$blog->id}");
        $res->assertSessionHas('success');
        $this->assertNotNull($blog->fresh()->deleted_at);
        $this->assertFalse(BlogPost::published()->where('id', $blog->id)->exists());
    }

    /** 22. Job Post Deletion */
    public function test_admin_can_delete_job_post()
    {
        $job = JobPost::create([
            'title' => 'Job to Delete',
            'company' => 'Freight Ltd.',
            'description' => 'Requirements',
            'is_active' => true,
        ]);

        $res = $this->actingAs($this->admin)->delete("/admin/jobs/{$job->id}");
        $res->assertSessionHas('success');
        $this->assertSoftDeleted('job_posts', ['id' => $job->id]);
    }

    /** 23. Payment Method Deletion Safely Nullifies Existing Payments */
    public function test_payment_method_deletion_safely_nullifies_existing_payments()
    {
        $pm = PaymentMethod::create([
            'provider_name' => 'Rocket Merchant',
            'account_number' => '01917-054664-8',
            'account_type' => 'Merchant',
            'is_active' => true,
        ]);

        $student = User::create(['name' => 'Payer', 'email' => 'payer@test.com', 'password' => bcrypt('password'), 'role' => 'student']);
        $course = Course::create(['title' => 'Tuition Course', 'price' => 3000]);

        $payment = Payment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'payment_method_id' => $pm->id,
            'amount' => 3000,
            'sender_number' => '01711111111',
            'transaction_id' => 'TRXROCKET',
            'status' => 'verified',
        ]);

        $res = $this->actingAs($this->admin)->delete("/admin/payment-methods/{$pm->id}");
        $res->assertSessionHas('success');
        $this->assertSoftDeleted('payment_methods', ['id' => $pm->id]);
        $this->assertNull($payment->fresh()->payment_method_id);
    }

    /** 24. Unified System: Creating Instructor Auto-Provisions & Links User Login Account */
    public function test_creating_instructor_auto_provisions_and_links_user_login_account()
    {
        $response = $this->actingAs($this->admin)->post('/admin/instructors', [
            'name' => 'Dr. Farhan Customs',
            'title' => 'Distinguished Customs Law Fellow',
            'expertise' => 'Harmonized Tariff Schedule & Sea Customs',
            'email' => 'farhan.customs@cab-academy.com',
            'password' => 'FacultySecret123',
            'phone' => '01711223344',
            'is_active' => '1',
        ]);

        $response->assertSessionHas('success');

        $instructor = Instructor::where('email', 'farhan.customs@cab-academy.com')->first();
        $this->assertNotNull($instructor);
        $this->assertNotNull($instructor->user_id);

        $user = User::find($instructor->user_id);
        $this->assertNotNull($user);
        $this->assertEquals('Dr. Farhan Customs', $user->name);
        $this->assertEquals('farhan.customs@cab-academy.com', $user->email);
        $this->assertEquals('instructor', $user->role);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('FacultySecret123', $user->password));
    }

    /** 25. Unified System: Registering/Creating User with Role Instructor Auto-Provisions Faculty Profile */
    public function test_creating_user_with_instructor_role_auto_provisions_faculty_profile()
    {
        $user = User::create([
            'name' => 'Prof. Shamim Supply Chain',
            'email' => 'shamim.scm@cab-academy.com',
            'password' => bcrypt('password123'),
            'role' => 'instructor',
            'phone' => '01888999000',
        ]);

        $instructor = Instructor::where('user_id', $user->id)->first();
        $this->assertNotNull($instructor);
        $this->assertEquals('Prof. Shamim Supply Chain', $instructor->name);
        $this->assertEquals('shamim.scm@cab-academy.com', $instructor->email);
        $this->assertTrue($instructor->is_active);
    }

    /** 26. Unified System: Announcement Broadcasting can Post to Community Feed */
    public function test_announcement_broadcasting_can_post_to_community_feed()
    {
        $res = $this->actingAs($this->admin)->post('/admin/announcements', [
            'title' => 'Special WTO Trade Advisory',
            'type' => 'important',
            'audience' => 'all',
            'message' => 'New customs valuation rules applicable starting next month.',
            'is_active' => '1',
            'post_to_feed' => '1',
        ]);

        $res->assertSessionHas('success');

        $post = CommunityPost::where('title', 'Special WTO Trade Advisory')->first();
        $this->assertNotNull($post);
        $this->assertTrue((bool)$post->is_pinned);
        $this->assertStringContainsString('customs valuation rules', $post->content);
    }

    /** 27. Unified System: Payment Verification Auto-Enrolls Student and Rejection Revokes Enrollment */
    public function test_payment_verification_auto_enrolls_and_rejection_revokes_enrollment()
    {
        $student = User::create([
            'name' => 'Enrolling Student',
            'email' => 'student.enrolled@cab-academy.com',
            'password' => bcrypt('password'),
            'role' => 'student',
        ]);

        $course = Course::create([
            'title' => 'Commercial Banking Masterclass',
            'price' => 5000,
        ]);

        $payment = Payment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'amount' => 5000,
            'sender_number' => '01700000000',
            'transaction_id' => 'TRXAUTOPAY1',
            'status' => 'pending',
        ]);

        // Student is not enrolled yet
        $this->assertFalse($student->enrolledCourses()->where('course_id', $course->id)->exists());

        // Verify payment
        $verifyRes = $this->actingAs($this->admin)->put("/admin/payments/{$payment->id}/status", [
            'status' => 'verified',
        ]);
        $verifyRes->assertSessionHas('success');
        $this->assertTrue($student->enrolledCourses()->where('course_id', $course->id)->exists());

        // Reject payment -> should detach enrollment
        $rejectRes = $this->actingAs($this->admin)->put("/admin/payments/{$payment->id}/status", [
            'status' => 'rejected',
        ]);
        $rejectRes->assertSessionHas('success');
        $this->assertFalse($student->enrolledCourses()->where('course_id', $course->id)->exists());
    }
}
