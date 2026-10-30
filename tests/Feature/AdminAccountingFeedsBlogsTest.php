<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\CommunityPost;
use App\Models\Course;
use App\Models\FinancialExpense;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccountingFeedsBlogsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Course $course;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->course = Course::create([
            'title' => 'Executive VAT & Tax Masterclass',
            'slug' => 'executive-vat-tax-masterclass',
            'category' => 'VAT & Tax',
            'price' => 5000,
            'is_published' => true,
        ]);
    }

    public function test_guest_is_redirected_to_login_when_accessing_accounting()
    {
        $response = $this->get('/admin/accounting');
        $response->assertRedirect('/login');
    }

    public function test_guest_is_redirected_to_login_when_accessing_feeds()
    {
        $response = $this->get('/admin/feeds');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_access_accounting_dashboard()
    {
        Payment::create([
            'user_id' => $this->admin->id,
            'course_id' => $this->course->id,
            'amount' => 5000,
            'sender_number' => '01917054664',
            'transaction_id' => 'TEST-TRX-101',
            'status' => 'verified',
            'verified_at' => now(),
            'verified_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/accounting');

        $response->assertStatus(200);
        $response->assertSee('Institutional Accounting & Revenue', false);
        $response->assertSee('5,000.00');
    }

    public function test_admin_can_record_campus_cash_tuition_and_auto_enroll_student()
    {
        $response = $this->actingAs($this->admin)->post('/admin/accounting/tuition', [
            'student_name' => 'Campus Student',
            'student_email' => 'campus_student@cab.test',
            'student_phone' => '01711223344',
            'course_id' => $this->course->id,
            'amount' => 4500,
            'payment_type' => 'campus_cash',
            'transaction_id' => 'CAMPUS-RECEIPT-99',
            'notes' => 'Direct counter registration',
        ]);

        $response->assertRedirect('/admin/accounting');

        $this->assertDatabaseHas('users', ['email' => 'campus_student@cab.test']);
        $student = User::where('email', 'campus_student@cab.test')->first();

        $this->assertDatabaseHas('payments', [
            'user_id' => $student->id,
            'course_id' => $this->course->id,
            'amount' => 4500,
            'status' => 'verified',
        ]);

        // Student should be automatically enrolled
        $this->assertTrue($student->enrolledCourses()->where('course_id', $this->course->id)->exists());
    }

    public function test_admin_can_record_and_delete_operational_expense()
    {
        $response = $this->actingAs($this->admin)->post('/admin/accounting/expenses', [
            'title' => 'Mirpur Campus Auditorium Venue',
            'category' => 'campus_operations',
            'amount' => 12000,
            'expense_date' => now()->toDateString(),
            'paid_to' => 'Facility Manager',
            'payment_method' => 'cash',
            'reference_no' => 'VCH-100',
            'notes' => 'Auditorium booking fee',
        ]);

        $response->assertRedirect('/admin/accounting');
        $this->assertDatabaseHas('financial_expenses', [
            'title' => 'Mirpur Campus Auditorium Venue',
            'amount' => 12000,
        ]);

        $expense = FinancialExpense::first();
        $delResponse = $this->actingAs($this->admin)->delete("/admin/accounting/expenses/{$expense->id}");
        $delResponse->assertRedirect('/admin/accounting');
        $this->assertSoftDeleted('financial_expenses', ['id' => $expense->id]);
    }

    public function test_admin_can_view_printable_financial_statement()
    {
        $response = $this->actingAs($this->admin)->get('/admin/accounting/statement');
        $response->assertStatus(200);
        $response->assertSee('Financial Statement & Revenue Audit', false);
    }

    public function test_admin_can_toggle_pin_on_community_post()
    {
        $post = CommunityPost::create([
            'user_id' => $this->admin->id,
            'author_name' => 'Student Leader',
            'category' => 'customs_sro',
            'title' => 'Important Question on HS Code',
            'content' => 'Can anyone clarify the SRO 2026 tariff?',
            'is_pinned' => false,
            'likes_count' => 0,
            'comments_count' => 0,
        ]);

        $response = $this->actingAs($this->admin)->patch("/admin/feeds/{$post->id}/pin");
        $response->assertRedirect();
        $this->assertTrue($post->fresh()->is_pinned);

        $response2 = $this->actingAs($this->admin)->patch("/admin/feeds/{$post->id}/pin");
        $response2->assertRedirect();
        $this->assertFalse($post->fresh()->is_pinned);
    }

    public function test_admin_can_broadcast_official_announcement_to_feed()
    {
        $response = $this->actingAs($this->admin)->post('/admin/feeds/broadcast', [
            'title' => 'Official Notice: Semester Batch Commencement',
            'category' => 'customs_sro',
            'author_title' => 'Director of Academic Affairs',
            'content' => 'All enrolled participants please review your zoom credentials.',
            'is_pinned' => '1',
        ]);

        $response->assertRedirect('/admin/feeds');
        $this->assertDatabaseHas('community_posts', [
            'title' => 'Official Notice: Semester Batch Commencement',
            'author_name' => 'ACS Academy Directorate',
            'is_pinned' => true,
        ]);
    }

    public function test_admin_can_toggle_featured_on_blog_post()
    {
        $blog = BlogPost::create([
            'title' => 'Strategic Trade Finance Guide',
            'slug' => 'strategic-trade-finance-guide',
            'category' => 'Trade Finance',
            'excerpt' => 'A guide for exporters.',
            'content' => 'Full article body here.',
            'author_name' => 'Dr. Rahman',
            'is_published' => true,
            'is_featured' => false,
        ]);

        $response = $this->actingAs($this->admin)->patch("/admin/blogs/{$blog->id}/feature");
        $response->assertRedirect();
        $this->assertTrue($blog->fresh()->is_featured);
    }
}
