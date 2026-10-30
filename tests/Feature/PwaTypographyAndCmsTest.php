<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PwaTypographyAndCmsTest extends TestCase
{
    use RefreshDatabase;

    public function test_pwa_manifest_endpoint_returns_valid_json_and_adapts_to_long_brand_names(): void
    {
        Setting::setVal('site_name', 'Academy of Commercial and Supply Chain');
        Setting::setVal('site_short_name', '');

        $response = $this->get('/manifest.json');

        $response->assertStatus(200);
        $response->assertJson([
            'name' => 'Academy of Commercial and Supply Chain',
            'display' => 'standalone',
            'theme_color' => '#051c45',
        ]);

        $data = $response->json();
        $this->assertNotEmpty($data['short_name']);
        $this->assertLessThanOrEqual(15, strlen($data['short_name']));
    }

    public function test_auto_responsive_brand_sizing_and_typography_engine_scales_short_and_long_names(): void
    {
        // 1. Short Name: ACS Academy
        Setting::setVal('site_name', 'ACS Academy');
        Setting::setVal('brand_name_size_mode', 'auto');
        Setting::setVal('ui_min_font_size', '15');
        Setting::setVal('ui_font_scale', 'comfortable');

        $shortSizing = Setting::getBrandSizing();
        $this->assertFalse($shortSizing['is_long']);
        $this->assertStringContainsString('1.25rem', $shortSizing['header_style']);

        // 2. Long Name: Academy of Commercial and Supply Chain
        Setting::setVal('site_name', 'Academy of Commercial and Supply Chain');
        $longSizing = Setting::getBrandSizing();
        $this->assertTrue($longSizing['is_long']);
        $this->assertStringContainsString('0.72rem', $longSizing['header_style']);

        $css = Setting::getTypographyCss();
        $this->assertStringContainsString('clamp(16px', $css);

        // Verify public homepage renders the adaptive brand title and PWA manifest link
        $home = $this->get('/');
        $home->assertStatus(200);
        $home->assertSee('Academy of Commercial and Supply Chain');
        $home->assertSee(route('pwa.manifest'));
    }

    public function test_admin_can_create_publish_and_hide_custom_standalone_pages(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $customPages = [
            [
                'title' => 'Corporate Supply Chain Advisory',
                'slug' => 'corporate-supply-chain-advisory',
                'subtitle' => 'Executive B2B Training Solutions',
                'content' => '<p>Tailored trade finance and supply chain bootcamps for commercial banks.</p>',
                'meta_description' => 'Corporate Supply Chain Advisory at CAB Academy',
                'is_published' => true,
                'show_in_header' => true,
                'show_in_footer' => true,
            ],
            [
                'title' => 'Internal Draft Curriculum Page',
                'slug' => 'internal-draft-curriculum',
                'subtitle' => 'Draft Subtitle',
                'content' => '<p>Confidential draft content.</p>',
                'meta_description' => '',
                'is_published' => false,
                'show_in_header' => true,
                'show_in_footer' => false,
            ],
        ];

        $this->actingAs($admin)->post(route('admin.frontend.store'), [
            'landing_cms_form_submitted' => '1',
            'cms_custom_pages_json' => json_encode($customPages),
        ])->assertRedirect();

        // Published custom page is accessible to guests and shown in header nav
        $pubResponse = $this->get('/page/corporate-supply-chain-advisory');
        $pubResponse->assertStatus(200);
        $pubResponse->assertSee('Corporate Supply Chain Advisory');
        $pubResponse->assertSee('Tailored trade finance and supply chain bootcamps');

        // Hidden draft page returns 404 to guests
        auth()->logout();
        $this->get('/page/internal-draft-curriculum')->assertStatus(404);

        // Admin can still preview hidden draft page
        $adminPreview = $this->actingAs($admin)->get('/page/internal-draft-curriculum');
        $adminPreview->assertStatus(200);
        $adminPreview->assertSee('Hidden Draft Preview Mode');
    }

    public function test_admin_can_hide_and_show_built_in_public_pages(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Hide the About Us and Jobs pages
        $this->actingAs($admin)->post(route('admin.frontend.store'), [
            'landing_cms_form_submitted' => '1',
            'page_about_enabled' => '0',
            'page_jobs_enabled' => '0',
        ])->assertRedirect();

        $this->assertFalse(Setting::isPageEnabled('about'));
        $this->assertFalse(Setting::isPageEnabled('jobs'));

        // Guest receives 404 when visiting hidden built-in pages
        auth()->logout();
        $this->get('/about')->assertStatus(404);
        $this->get('/jobs')->assertStatus(404);

        // Re-enable pages
        $this->actingAs($admin)->post(route('admin.frontend.store'), [
            'landing_cms_form_submitted' => '1',
            'page_about_enabled' => '1',
            'page_jobs_enabled' => '1',
        ])->assertRedirect();

        auth()->logout();
        $this->get('/about')->assertStatus(200);
        $this->get('/jobs')->assertStatus(200);
    }

    public function test_admin_can_create_and_toggle_custom_homepage_sections(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $customSections = [
            [
                'id' => 'sec_scholarship',
                'title' => 'Executive Trade Fellowship 2026',
                'subtitle' => 'Merit scholarships for commercial banking professionals.',
                'badge' => 'Fellowship Program',
                'content' => '<p>Apply now for the upcoming cohort.</p>',
                'cta_text' => 'Apply for Fellowship',
                'cta_url' => '/contact',
                'theme' => 'navy',
                'is_visible' => true,
            ],
            [
                'id' => 'sec_hidden',
                'title' => 'Unpublished Internal Section',
                'subtitle' => 'Should not appear on homepage',
                'badge' => 'Draft',
                'content' => '<p>Hidden section body</p>',
                'cta_text' => '',
                'cta_url' => '',
                'theme' => 'white',
                'is_visible' => false,
            ],
        ];

        $this->actingAs($admin)->post(route('admin.frontend.store'), [
            'landing_cms_form_submitted' => '1',
            'landing_custom_sections_json' => json_encode($customSections),
        ])->assertRedirect();

        $home = $this->get('/');
        $home->assertStatus(200);
        $home->assertSee('Executive Trade Fellowship 2026');
        $home->assertSee('Apply for Fellowship');
        $home->assertDontSee('Unpublished Internal Section');
    }

    public function test_guest_express_enrollment_sets_4_digit_password_and_allows_returning_login_by_phone_or_email(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $course = \App\Models\Course::create([
            'title' => 'Masterclass on Letter of Credit & Customs',
            'description' => 'Comprehensive trade finance training.',
            'price' => 2500,
            'is_published' => true,
            'instructor_id' => $instructor->id,
        ]);

        // 1. Guest enrolls via Express Enrollment with a 4-digit login PIN ('4829')
        $enrollResp = $this->postJson(route('courses.express_enroll', $course), [
            'name' => 'Tanvir Hossain',
            'phone' => '01917054664',
            'email' => 'tanvir.lc@example.com',
            'password' => '4829',
            'training_mode' => 'online_zoom',
            'transaction_id' => 'TRX998877',
        ]);

        $enrollResp->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'pending',
            ]);

        // Log out to simulate returning later on another device
        auth()->logout();
        $this->assertGuest();

        // 2. Student logs back in using their 11-digit mobile number + 4-digit PIN
        $phoneLogin = $this->post('/login', [
            'email' => '01917054664',
            'password' => '4829',
        ]);
        $phoneLogin->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $this->assertEquals('tanvir.lc@example.com', auth()->user()->email);

        // 3. Log out and log back in using Email + 4-digit PIN
        auth()->logout();
        $emailLogin = $this->post('/login', [
            'email' => 'tanvir.lc@example.com',
            'password' => '4829',
        ]);
        $emailLogin->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_accounting_statement_and_invoice_templates_dynamically_reflect_site_name_and_template_options(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'student']);
        $course = \App\Models\Course::create([
            'title' => 'Supply Chain Executive Certification',
            'description' => 'SCM Certification',
            'price' => 4500,
            'is_published' => true,
            'instructor_id' => $admin->id,
        ]);

        $payment = \App\Models\Payment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'amount' => 4500,
            'sender_number' => '01711223344',
            'transaction_id' => 'SCM4500',
            'status' => 'verified',
            'verified_at' => now(),
        ]);

        // 1. When only global site_name is set, statement and invoice dynamically show site_name
        Setting::setVal('site_name', 'ACS Academy of Supply Chain');
        Setting::setVal('accounting_institution_name', '');

        $statementResp = $this->actingAs($admin)->get(route('admin.accounting.statement'));
        $statementResp->assertStatus(200);
        $statementResp->assertSee('ACS Academy of Supply Chain');

        $invoiceResp = $this->actingAs($student)->get(route('payments.invoice', $payment));
        $invoiceResp->assertStatus(200);
        $invoiceResp->assertSee('ACS Academy of Supply Chain');

        // 2. Admin customizes template options from Accounting Template Settings
        $this->actingAs($admin)->post(route('admin.accounting.template.store'), [
            'accounting_institution_name' => 'Academy of Commercial and Supply Chain (ACSC)',
            'accounting_ledger_subtitle' => 'Chartered Treasury & Audit Division',
            'accounting_invoice_prefix' => 'ACSC-INV',
            'accounting_signatory_title' => 'Chief Treasury Comptroller',
            'accounting_footer_note' => 'Verified Digital Receipt • ACSC Directorate',
            'accounting_fiscal_year' => 'FY 2026-2027',
        ])->assertRedirect(route('admin.accounting.index'));

        $customStatement = $this->actingAs($admin)->get(route('admin.accounting.statement'));
        $customStatement->assertStatus(200);
        $customStatement->assertSee('Academy of Commercial and Supply Chain (ACSC)');
        $customStatement->assertSee('Chartered Treasury & Audit Division');
        $customStatement->assertSee('Chief Treasury Comptroller');

        $customInvoice = $this->actingAs($student)->get(route('payments.invoice', $payment));
        $customInvoice->assertStatus(200);
        $customInvoice->assertSee('Academy of Commercial and Supply Chain (ACSC)');
        $customInvoice->assertSee('Verified Digital Receipt • ACSC Directorate');
    }

    public function test_announcements_can_target_enrolled_students_by_course_name(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $studentInCourseA = User::factory()->create(['role' => 'student', 'name' => 'Enrolled Student A']);
        $studentInCourseB = User::factory()->create(['role' => 'student', 'name' => 'Other Student B']);

        $courseA = \App\Models\Course::create([
            'title' => 'Advanced VAT & Customs Compliance',
            'description' => 'VAT Course',
            'price' => 3000,
            'is_published' => true,
            'instructor_id' => $admin->id,
        ]);
        $courseB = \App\Models\Course::create([
            'title' => 'Global Freight Forwarding',
            'description' => 'Freight Course',
            'price' => 3000,
            'is_published' => true,
            'instructor_id' => $admin->id,
        ]);

        $courseA->students()->attach($studentInCourseA->id);
        $courseB->students()->attach($studentInCourseB->id);

        // Send announcement targeted specifically to Course A using 'course:{id}'
        $this->actingAs($admin)->post(route('admin.announcements.store'), [
            'title' => 'Live Zoom Link for VAT Batch',
            'message' => 'Join tonight at 8:00 PM sharp.',
            'audience' => 'course:' . $courseA->id,
        ])->assertRedirect(route('admin.announcements.create'));

        $this->assertEquals(1, $studentInCourseA->fresh()->notifications()->count());
        $this->assertEquals(0, $studentInCourseB->fresh()->notifications()->count());
    }

    public function test_frontend_renders_liquid_glass_cards_across_courses_and_jobs(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        \App\Models\Course::create([
            'title' => 'Liquid Glass Export Masterclass',
            'description' => 'Export Masterclass',
            'price' => 2000,
            'is_published' => true,
            'instructor_id' => $admin->id,
        ]);
        \App\Models\JobPost::create([
            'title' => 'Senior Supply Chain Manager',
            'company' => 'Apex Global Logistics',
            'location' => 'Dhaka',
            'job_type' => 'Full-time',
            'description' => 'Lead commercial import-export operations.',
            'is_active' => true,
        ]);

        $home = $this->get('/');
        $home->assertStatus(200);
        $home->assertSee('liquid-glass-card');

        $catalog = $this->get('/catalog');
        $catalog->assertStatus(200);
        $catalog->assertSee('liquid-glass-card');

        $jobs = $this->get('/jobs');
        $jobs->assertStatus(200);
        $jobs->assertSee('liquid-glass-card');
    }

    public function test_student_portal_navigation_and_classroom_continuity(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create([
            'role' => 'student',
            'name' => 'Tanvir Student',
            'preferences' => ['level' => 'Professional'],
        ]);

        $course = \App\Models\Course::create([
            'title' => 'Executive L/C & Trade Finance Masterclass',
            'description' => 'Comprehensive L/C operations',
            'price' => 3500,
            'is_published' => true,
            'instructor_id' => $admin->id,
        ]);

        $section = $course->sections()->create([
            'title' => 'Module 1: Documentary Credits',
            'order' => 1,
        ]);

        $lesson1 = $section->lessons()->create([
            'title' => 'UCP 600 Fundamentals',
            'content' => 'Detailed UCP 600 guidelines.',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'order' => 1,
        ]);

        $lesson2 = $section->lessons()->create([
            'title' => 'Back-to-Back L/C Clauses',
            'content' => 'Master back-to-back L/C documentation.',
            'order' => 2,
        ]);

        $course->students()->attach($student->id);
        // Complete lesson 1 so lesson 2 is the next incomplete lesson
        $student->completedLessons()->attach($lesson1->id, ['completed_at' => now()]);

        // 1. Student Dashboard shows 50% progress (1/2 Lessons) and links directly to Lesson 2
        $dashboard = $this->actingAs($student)->get(route('dashboard'));
        $dashboard->assertStatus(200);
        $dashboard->assertSee('1/2 Lessons');
        $dashboard->assertSee('50%');
        $dashboard->assertSee(route('lessons.show', [$course, $lesson2]), false);
        $dashboard->assertSee('Sign Out');

        // 2. Wishlist & Profile keep the student inside the Student Portal layout
        $wishlist = $this->actingAs($student)->get(route('wishlist.index'));
        $wishlist->assertStatus(200);
        $wishlist->assertSee('Student Portal');
        $wishlist->assertSee(route('dashboard'), false);

        $profile = $this->actingAs($student)->get(route('profile.edit'));
        $profile->assertStatus(200);
        $profile->assertSee('Profile & Account Security');
        $profile->assertSee('Back to Dashboard');

        // 3. Course Catalog, Career Portal, and Course Show page all keep the student inside the Student Portal shell
        $studentCatalog = $this->actingAs($student)->get(route('catalog.index'));
        $studentCatalog->assertStatus(200);
        $studentCatalog->assertSee('Student Portal');
        $studentCatalog->assertSee('Active Student');

        $studentJobs = $this->actingAs($student)->get(route('jobs.index'));
        $studentJobs->assertStatus(200);
        $studentJobs->assertSee('Student Portal');
        $studentJobs->assertSee('Active Student');

        $courseShow = $this->actingAs($student)->get(route('courses.show', $course));
        $courseShow->assertStatus(200);
        $courseShow->assertSee('Student Portal');
        $courseShow->assertSee('Continue Classroom');
        $courseShow->assertSee('Active Student');
        $courseShow->assertSee(route('lessons.show', [$course, $lesson2]), false);

        // 4. Lesson Classroom renders curriculum sidebar and normalizes YouTube watch URL to embed URL
        $classroom = $this->actingAs($student)->get(route('lessons.show', [$course, $lesson1]));
        $classroom->assertStatus(200);
        $classroom->assertSee('https://www.youtube.com/embed/dQw4w9WgXcQ', false);
        $classroom->assertSee('Course Curriculum');
        $classroom->assertSee('Back-to-Back L/C Clauses');
        $classroom->assertSee(route('dashboard'), false);
    }

    public function test_self_healing_schema_repair_and_legacy_student_login_and_admin_mutations_without_500_errors(): void
    {
        // 1. Verify self-healing schema repair runs cleanly via class and Artisan command
        $logs = \App\Support\DatabaseSchemaRepair::ensureSchema(true);
        $this->assertNotEmpty($logs);
        $this->artisan('schema:repair')->assertExitCode(0);

        // 2. Verify newly express-enrolled student lands directly on /dashboard (200 OK) without onboarding redirect loop
        $admin = User::factory()->create(['role' => 'admin']);
        $course = \App\Models\Course::create([
            'title' => 'Supply Chain Analytics Bootcamp',
            'description' => 'Analytics Bootcamp',
            'price' => 2500,
            'is_published' => true,
            'instructor_id' => $admin->id,
        ]);

        $enrollResp = $this->post(route('courses.express_enroll', $course), [
            'name' => 'Nadia Islam',
            'email' => 'nadia@example.com',
            'phone' => '01819556677',
            'password' => '5566',
            'sender_number' => '01819556677',
            'transaction_id' => 'TRX556677',
        ]);
        $enrollResp->assertRedirect(route('dashboard'));
        $dashboardResp = $this->get(route('dashboard'));
        $dashboardResp->assertStatus(200);
        $dashboardResp->assertSee('Nadia Islam');

        // 3. Verify legacy auto-created student account (student_{phone}@cabacademy.com) can log in using last 4 digits of phone
        auth()->logout();
        $legacyStudent = User::create([
            'name' => 'Legacy Enrolled Student',
            'email' => 'student_01911223344@cabacademy.com',
            'phone' => '01911223344',
            'password' => bcrypt(\Illuminate\Support\Str::random(16)),
            'role' => 'student',
        ]);
        $course->students()->attach($legacyStudent->id);

        $legacyLogin = $this->post('/login', [
            'email' => '01911223344',
            'password' => '3344', // Last 4 digits of phone number
        ]);
        $legacyLogin->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($legacyStudent);

        // Visiting /dashboard auto-populates preferences for enrolled legacy student and returns 200 OK
        $this->get('/dashboard')->assertStatus(200);

        // 4. Verify Admin Panel Course & Job creation/updates work cleanly without JSON/migration errors
        $this->actingAs($admin)->post(route('admin.courses.store'), [
            'title' => 'Chartered Customs & Bond Management',
            'description' => 'Customs & Bond Course',
            'price' => 4000,
            'is_published' => 1,
        ])->assertSessionHas('success');

        $this->actingAs($admin)->post(route('admin.jobs.store'), [
            'title' => 'Commercial Manager (Import & Export)',
            'company' => 'Square Textiles PLC',
            'job_type' => 'Full-time',
            'location' => 'Dhaka',
            'description' => 'Manage L/C and customs documentation.',
            'is_active' => 1,
        ])->assertSessionHas('success');

        // 5. Verify Online-Only courses disable Executive Campus mode and enforce online_zoom on enrollment
        $onlineCourse = \App\Models\Course::create([
            'title' => 'Online L/C Documentary Credit Workshop',
            'description' => '100% Online Zoom Workshop',
            'venue' => 'Online Live (Zoom)',
            'price' => 1500,
            'is_published' => true,
            'instructor_id' => $admin->id,
        ]);
        $this->assertEquals('online', $onlineCourse->attendance_mode);
        $this->assertTrue($onlineCourse->isOnlineOnly());
        $this->assertFalse($onlineCourse->isCampusOnly());

        auth()->logout();
        $this->post(route('courses.express_enroll', $onlineCourse), [
            'name' => 'Online Student',
            'email' => 'onlinestudent@example.com',
            'phone' => '01715889900',
            'password' => '8899',
            'training_mode' => 'in_person', // Attempt to pass in_person on an online-only course
            'sender_number' => '01715889900',
            'transaction_id' => 'TRXONLINE99',
        ])->assertRedirect(route('dashboard'));

        $latestPayment = \App\Models\Payment::where('transaction_id', 'TRXONLINE99')->first();
        $this->assertNotNull($latestPayment);
        $this->assertEquals('online_zoom', $latestPayment->form_data['training_mode'] ?? null);

        // 6. Verify paid courses strictly require valid payment (reject missing TrxID or RESERVED-OFFLINE)
        $unpaidAttempt = $this->postJson(route('courses.express_enroll', $onlineCourse), [
            'name' => 'Bypass Attempt',
            'email' => 'bypass@example.com',
            'phone' => '01715889911',
            'password' => '1234',
            'sender_number' => '01715889911',
            'transaction_id' => 'RESERVED-OFFLINE',
        ]);
        $unpaidAttempt->assertStatus(422);
        $unpaidAttempt->assertJsonValidationErrors(['transaction_id']);

        $emptyTrxAttempt = $this->postJson(route('courses.express_enroll', $onlineCourse), [
            'name' => 'Empty Trx Attempt',
            'email' => 'emptytrx@example.com',
            'phone' => '01715889922',
            'password' => '1234',
        ]);
        $emptyTrxAttempt->assertStatus(422);
        $emptyTrxAttempt->assertJsonValidationErrors(['transaction_id']);

        // 7. Verify logged-in student can enroll in a paid course WITHOUT re-submitting name or email
        $loggedInStudent = User::factory()->create([
            'name' => 'Verified LoggedIn Student',
            'email' => 'verified.student@example.com',
            'phone' => null,
            'role' => 'student',
        ]);
        $loggedInPaidResp = $this->actingAs($loggedInStudent)->postJson(route('courses.express_enroll', $onlineCourse), [
            'sender_number' => '01917054664',
            'transaction_id' => 'BKASH-LOGGEDIN-77',
            'training_mode' => 'online_zoom',
        ]);
        $loggedInPaidResp->assertStatus(200)->assertJson([
            'success' => true,
            'status' => 'pending',
        ]);
        $this->assertEquals('01917054664', $loggedInStudent->fresh()->phone);

        // 8. Verify Free course (price = 0 / 0.00) requires NO payment or TrxID and enrolls immediately
        $freeCourse = \App\Models\Course::create([
            'title' => 'Free Export Readiness Orientation',
            'description' => 'Complimentary Orientation Session',
            'venue' => 'Online Live (Zoom)',
            'price' => 0.00,
            'is_published' => true,
            'instructor_id' => $admin->id,
        ]);
        $freeResp = $this->actingAs($loggedInStudent)->postJson(route('courses.express_enroll', $freeCourse), [
            'training_mode' => 'online_zoom',
        ]);
        $freeResp->assertStatus(200)->assertJson([
            'success' => true,
            'status' => 'enrolled',
        ]);
        $this->assertTrue($loggedInStudent->fresh()->enrolledCourses()->where('courses.id', $freeCourse->id)->exists());

        // 9. Verify redesigned Admin Dashboard & Student Dashboard render all new executive telemetry & UI widgets cleanly
        $studentDash = $this->actingAs($loggedInStudent)->get(route('dashboard'));
        $studentDash->assertStatus(200);
        $studentDash->assertSee('Active Classroom Spotlight');
        $studentDash->assertSee('Reserved Seat');
        $studentDash->assertSee('BKASH-LOGGEDIN-77');
        $studentDash->assertSee('Tuition Payments &amp; Official Invoices', false);

        $adminDash = $this->actingAs($admin)->get(route('admin.dashboard'));
        $adminDash->assertStatus(200);
        $adminDash->assertSee('6-Month Revenue &amp; Enrollment Velocity', false);
        $adminDash->assertSee('Treasury Summary');
        $adminDash->assertSee('Recent Student Accounts');
        $adminDash->assertSee('Recent Career Applications');
        $adminDash->assertSee('Pending Payment Approvals');
    }
}
