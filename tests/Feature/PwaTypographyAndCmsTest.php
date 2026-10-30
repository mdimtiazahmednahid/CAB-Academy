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
        $this->assertEquals('Academy of<br>Commercial and Supply Chain', $longSizing['formatted_html']);

        // Verify two-part formatting with (ACS)
        $this->assertEquals(
            'Academy of<br>Commercial &amp; Supply Chain (ACS)',
            Setting::formatBrandNameHtml('Academy of Commercial & Supply Chain (ACS)')
        );

        // Verify legacy CAB logo hashes fall back to official ACS logo
        $this->assertStringContainsString(
            '/images/logo.png',
            Setting::resolveLogoUrl('storage/logos/090dXs1BPxkT2d2liIcwLlCwDhpMZYMtHvwucsRP.jpg')
        );

        $css = Setting::getTypographyCss();
        $this->assertStringContainsString('clamp(16px', $css);
        $this->assertStringContainsString('overflow-x: clip', $css);

        // Verify public homepage renders the adaptive two-part brand title and PWA manifest link
        Setting::setVal('site_name', 'Academy of Commercial & Supply Chain (ACS)');
        $home = $this->get('/');
        $home->assertStatus(200);
        $home->assertSee('Academy of<br>Commercial &amp; Supply Chain (ACS)', false);
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

    public function test_dynamic_per_course_counselor_whatsapp_phone_liquid_glass_and_demo_purge(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // 1. Configure global hotline & WhatsApp settings
        Setting::updateOrCreate(['key' => 'site_name'], ['value' => 'ACS Academy']);
        Setting::updateOrCreate(['key' => 'contact_hotline'], ['value' => '01744542955']);
        Setting::updateOrCreate(['key' => 'contact_whatsapp'], ['value' => '8801744542955']);

        // 2. Create a course using global counselor phone fallback
        $courseGlobal = \App\Models\Course::create([
            'title' => 'Communication Skills for Career Success.',
            'description' => 'Master professional communication and workplace etiquette.',
            'venue' => 'Online Live (Zoom)',
            'price' => 0,
            'is_published' => true,
            'instructor_id' => $admin->id,
        ]);

        // Verify WhatsApp counselor message does NOT contain "CAB Academy"
        $this->assertStringNotContainsString('CAB Academy', $courseGlobal->effective_whatsapp_message);
        $this->assertStringContainsString(
            'Assalamu Alaikum! I would like to inquire/enroll in *Communication Skills for Career Success.* (Fee: ৳0). Please share batch details and enrollment procedure:',
            $courseGlobal->effective_whatsapp_message
        );
        $this->assertEquals('01744542955', $courseGlobal->effective_counselor_phone);
        $this->assertEquals('8801744542955', $courseGlobal->effective_whatsapp_number);

        // Verify /courses/{course} and /c/{course} render dynamic phone & Liquid Glass and zero hardcoded 01917054664
        $showGlobal = $this->get(route('courses.show', $courseGlobal));
        $showGlobal->assertStatus(200);
        $showGlobal->assertSee('01744542955');
        $showGlobal->assertDontSee('01917054664');
        $showGlobal->assertSee('liquid-glass-card');
        $showGlobal->assertSee('liquid-glass-dark');

        $shareGlobal = $this->get('/c/' . $courseGlobal->id);
        $shareGlobal->assertStatus(200);
        $shareGlobal->assertSee('01744542955');
        $shareGlobal->assertDontSee('01917054664');
        $shareGlobal->assertSee('liquid-glass-dark');

        // 3. Configure per-course counselor_phone and custom whatsapp_message via Admin update
        $this->actingAs($admin)->put(route('admin.courses.update', $courseGlobal), [
            'title' => 'Communication Skills for Career Success.',
            'description' => 'Master professional communication and workplace etiquette.',
            'venue' => 'Online Live (Zoom)',
            'price' => 0,
            'counselor_phone' => '01888999000',
            'whatsapp_message' => 'Assalamu Alaikum! Please enroll me in *{title}* ({price}) -> {url}',
            'is_published' => 1,
        ])->assertRedirect();

        $courseGlobal->refresh();
        $this->assertEquals('01888999000', $courseGlobal->effective_counselor_phone);
        $this->assertEquals('8801888999000', $courseGlobal->effective_whatsapp_number);
        $this->assertStringContainsString('wa.me/8801888999000', $courseGlobal->counselor_whatsapp_url);
        $this->assertStringNotContainsString('CAB%20Academy', $courseGlobal->counselor_whatsapp_url);

        $shareOverride = $this->get('/c/' . $courseGlobal->id);
        $shareOverride->assertStatus(200);
        $shareOverride->assertSee('01888999000');

        // 4. Verify Demo Data Purge removes demo courses/faculty while keeping real courses intact
        $demoCourse = \App\Models\Course::create([
            'title' => 'PGD in Supply Chain Management (PGDSCM)',
            'description' => 'Seeded demo course',
            'price' => 5000,
            'is_published' => true,
            'instructor_id' => $admin->id,
        ]);
        $demoInstructor = \App\Models\Instructor::create([
            'name' => 'Faculty Chair — Trade & Banking',
            'title' => 'Senior Trade Faculty',
            'expertise' => 'Trade & Banking',
            'bio' => 'Demo bio',
            'is_active' => true,
        ]);
        $realInstructor = \App\Models\Instructor::create([
            'name' => 'MD. Shahneoage Hossain',
            'title' => 'Lead Faculty & Consultant',
            'expertise' => 'Communication & Leadership',
            'bio' => 'Real faculty bio',
            'is_active' => true,
        ]);

        $purgeResult = \App\Support\DatabaseSchemaRepair::purgeDemoData();
        $this->assertEquals('ok', $purgeResult['status']);
        $this->assertNull(\App\Models\Course::withTrashed()->find($demoCourse->id));
        $this->assertNotNull(\App\Models\Course::find($courseGlobal->id));
        $this->assertNull(\App\Models\Instructor::find($demoInstructor->id));
        $this->assertNotNull(\App\Models\Instructor::find($realInstructor->id));
    }

    public function test_dynamic_enrollment_form_studio_and_csv_export(): void
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        $course1 = \App\Models\Course::create([
            'title' => 'Executive Leadership Masterclass',
            'description' => 'Testing dynamic form studio',
            'price' => 0,
            'is_published' => true,
            'instructor_id' => $admin->id,
        ]);
        $course2 = \App\Models\Course::create([
            'title' => 'Corporate Finance Strategy',
            'description' => 'Second course for apply_to_all_courses',
            'price' => 3000,
            'is_published' => true,
            'instructor_id' => $admin->id,
        ]);

        // Verify built-in presets and quick-add question blocks
        $templates = \App\Models\Course::getBuiltinTemplates();
        $this->assertArrayHasKey('standard', $templates);
        $this->assertArrayHasKey('quick_lead', $templates);
        $this->assertArrayHasKey('corporate', $templates);
        $this->assertArrayHasKey('comprehensive', $templates);
        $this->assertArrayHasKey('online_webinar', $templates);
        $this->assertArrayHasKey('academic_cohort', $templates);

        $quickBlocks = \App\Models\Course::getQuickAddQuestions();
        $this->assertGreaterThanOrEqual(10, count($quickBlocks));

        // Update enrollment form with radio/select options and apply_to_all_courses = 1
        $fieldsPayload = [
            ['name' => 'name', 'label' => 'Participant Name', 'type' => 'text', 'is_enabled' => true, 'is_required' => true],
            ['name' => 'phone', 'label' => 'WhatsApp Mobile', 'type' => 'tel', 'is_enabled' => true, 'is_required' => true],
            ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'is_enabled' => false, 'is_required' => false],
            ['name' => 'organization', 'label' => 'Organization', 'type' => 'text', 'is_enabled' => true, 'is_required' => true],
            ['name' => 'experience_level', 'label' => 'Experience Level', 'type' => 'radio', 'is_enabled' => true, 'is_required' => true, 'options' => 'Fresh Graduate, 1-3 Years, 3-7 Years, 8+ Years'],
            ['name' => 'industry_sector', 'label' => 'Industry Sector', 'type' => 'select', 'is_enabled' => true, 'is_required' => false, 'options' => ['Banking & Finance', 'RMG & Textile', 'IT & Telecom']],
        ];

        $this->actingAs($admin)->post(route('admin.courses.update_enrollment_form', $course1), [
            'enrollment_fields' => $fieldsPayload,
            'apply_to_all_courses' => '1',
        ])->assertRedirect();

        $course1->refresh();
        $course2->refresh();

        $resolved1 = collect($course1->effective_enrollment_fields)->keyBy('name');
        $resolved2 = collect($course2->effective_enrollment_fields)->keyBy('name');
        $this->assertFalse($resolved1['email']['is_enabled']);
        $this->assertFalse($resolved2['email']['is_enabled']);
        $this->assertEquals('radio', $resolved1['experience_level']['type']);
        $this->assertEquals(['Fresh Graduate', '1-3 Years', '3-7 Years', '8+ Years'], $resolved1['experience_level']['options']);
        $this->assertEquals('select', $resolved1['industry_sector']['type']);
        $this->assertCount(3, $resolved1['industry_sector']['options']);
        $this->assertEquals('select', $resolved2['industry_sector']['type']);

        // Record a registration with custom form_data and export CSV
        $student = \App\Models\User::factory()->create(['role' => 'student', 'name' => 'Rahim Uddin', 'phone' => '01712345678']);
        \App\Models\Payment::create([
            'user_id' => $student->id,
            'course_id' => $course1->id,
            'amount' => 0,
            'sender_number' => '01712345678',
            'status' => 'approved',
            'transaction_id' => 'FREE-TEST-001',
            'form_data' => [
                'name' => 'Rahim Uddin',
                'phone' => '01712345678',
                'training_mode' => 'online_zoom',
                'organization' => 'Apex Group',
                'experience_level' => '3-7 Years',
                'industry_sector' => 'RMG & Textile',
            ],
        ]);

        $csvResponse = $this->actingAs($admin)->get(route('admin.courses.export_registrations', $course1));
        $csvResponse->assertStatus(200);
        $this->assertStringContainsString('text/csv', $csvResponse->headers->get('Content-Type'));
        $csvContent = $csvResponse->streamedContent();
        $this->assertStringContainsString('Rahim Uddin', $csvContent);
        $this->assertStringContainsString('Apex Group', $csvContent);
        $this->assertStringContainsString('3-7 Years', $csvContent);
        $this->assertStringContainsString('RMG & Textile', $csvContent);
    }

    public function test_modular_partial_course_updates_never_overwrite_untouched_fields(): void
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        $customFields = [
            ['name' => 'name', 'label' => 'Full Name', 'type' => 'text', 'is_enabled' => true, 'is_required' => true],
            ['name' => 'phone', 'label' => 'WhatsApp Mobile', 'type' => 'tel', 'is_enabled' => true, 'is_required' => true],
            ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'is_enabled' => false, 'is_required' => false],
            ['name' => 'organization', 'label' => 'Company', 'type' => 'text', 'is_enabled' => true, 'is_required' => true],
        ];

        $course = \App\Models\Course::create([
            'title' => 'Original Title',
            'description' => 'Original Description',
            'price' => 4500,
            'is_published' => true,
            'certificate_enabled' => true,
            'batch' => '105th Batch',
            'show_batch' => true,
            'counselor_phone' => '01744542955',
            'enrollment_fields' => $customFields,
            'instructor_id' => $admin->id,
        ]);

        // 1. Update ONLY Title (e.g. inline title editor) — must NOT reset price, is_published, certificate_enabled, batch, or enrollment_fields
        $this->actingAs($admin)->post(route('admin.courses.update', $course), [
            '_section_label' => 'Course Title',
            'title' => 'New Simplified Title Only',
        ])->assertRedirect();

        $course->refresh();
        $this->assertEquals('New Simplified Title Only', $course->title);
        $this->assertEquals(4500, (float) $course->price);
        $this->assertTrue((bool) $course->is_published);
        $this->assertTrue((bool) $course->certificate_enabled);
        $this->assertEquals('105th Batch', $course->batch);
        $this->assertTrue((bool) $course->show_batch);
        $this->assertEquals('01744542955', $course->counselor_phone);
        $this->assertFalse(collect($course->effective_enrollment_fields)->keyBy('name')['email']['is_enabled']);

        // 2. Update ONLY Cover Photo (base64 1x1 PNG) without sending title or any other field
        $tinyPngBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
        $this->actingAs($admin)->post(route('admin.courses.update', $course), [
            '_section_label' => 'Course Cover Photo',
            'cover_image_base64' => $tinyPngBase64,
        ])->assertRedirect();

        $course->refresh();
        $this->assertNotNull($course->cover_image);
        $this->assertStringContainsString('course_covers/', $course->cover_image);
        $this->assertEquals('New Simplified Title Only', $course->title);
        $this->assertEquals(4500, (float) $course->price);
        $this->assertTrue((bool) $course->is_published);
        $this->assertTrue((bool) $course->certificate_enabled);
        $this->assertFalse(collect($course->effective_enrollment_fields)->keyBy('name')['email']['is_enabled']);

        // 3. Toggle ONLY Certificate Status in 1 click
        $this->actingAs($admin)->post(route('admin.courses.update', $course), [
            'certificate_enabled' => '0',
        ])->assertRedirect();

        $course->refresh();
        $this->assertFalse((bool) $course->certificate_enabled);
        $this->assertTrue((bool) $course->is_published);
        $this->assertEquals(4500, (float) $course->price);
        $this->assertEquals('New Simplified Title Only', $course->title);
    }

    public function test_universal_profile_picture_sync_everywhere_including_top_right_avatar_and_community_feed(): void
    {
        $student = User::factory()->create([
            'name' => 'Mahmudul Hasan',
            'email' => 'mahmudul@acsacademy.bd',
            'role' => 'student',
            'profile_picture' => null,
            'preferences' => ['level' => 'Professional'],
        ]);

        // Before uploading a custom profile picture, the user has no custom picture
        $this->assertFalse($student->hasCustomProfilePicture());

        // Create a community post and comment while avatar is still null
        $post = \App\Models\CommunityPost::create([
            'user_id' => $student->id,
            'author_name' => $student->name,
            'author_title' => 'Student Member',
            'author_avatar' => null,
            'category' => 'General Discussion',
            'content' => 'Excited to join ACS Academy!',
        ]);

        $comment = \App\Models\CommunityComment::create([
            'community_post_id' => $post->id,
            'user_id' => $student->id,
            'author_name' => $student->name,
            'author_avatar' => null,
            'content' => 'Great community discussion!',
        ]);

        // Upload a profile picture via PATCH /profile (even without resending email)
        $fakeAvatar = \Illuminate\Http\UploadedFile::fake()->image('avatar.jpg', 300, 300);

        $this->actingAs($student)->patch(route('profile.update'), [
            'name' => 'Mahmudul Hasan',
            'profile_picture' => $fakeAvatar,
        ])->assertRedirect(route('profile.edit'));

        $student->refresh();
        $this->assertTrue($student->hasCustomProfilePicture());
        $this->assertNotNull($student->profile_picture);
        $this->assertStringContainsString('profiles/', $student->profile_picture_url);

        // Verify CommunityPost and CommunityComment records were synced automatically
        $this->assertEquals($student->profile_picture, $post->fresh()->author_avatar);
        $this->assertEquals($student->profile_picture, $comment->fresh()->author_avatar);
        $this->assertEquals($student->profile_picture_url, $post->fresh()->author_avatar_url);
        $this->assertEquals($student->profile_picture_url, $comment->fresh()->author_avatar_url);

        // Verify the top-right header in Student Portal (/dashboard and /profile) renders the uploaded profile picture URL instead of the static 'M' letter avatar
        $dashboardResponse = $this->actingAs($student)->get(route('dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee($student->profile_picture_url, false);

        $profileResponse = $this->actingAs($student)->get(route('profile.edit'));
        $profileResponse->assertStatus(200);
        $profileResponse->assertSee($student->profile_picture_url, false);
    }

    public function test_facebook_style_rich_link_preview_and_open_graph_metadata(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $course = \App\Models\Course::create([
            'title' => 'Communication Skills for Career Success.',
            'short_description' => 'Master professional communication, workplace etiquette, and leadership conversations.',
            'description' => 'Comprehensive communication training.',
            'price' => 0,
            'start_date' => '2026-10-30',
            'is_published' => true,
            'instructor_id' => $admin->id,
            'cover_image' => 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=1200',
        ]);

        // Verify StylishTextFormatter converts text to Unicode Mathematical Bold Serif (like "𝐏𝐆𝐃 𝐈𝐍 𝐒𝐔𝐏𝐏𝐋𝐘 𝐂𝐇𝐀𝐈𝐍 𝐌𝐀𝐍𝐀𝐆𝐄𝐌𝐄𝐍𝐓")
        $this->assertEquals(
            '𝐏𝐆𝐃 𝐈𝐍 𝐒𝐔𝐏𝐏𝐋𝐘 𝐂𝐇𝐀𝐈𝐍 𝐌𝐀𝐍𝐀𝐆𝐄𝐌𝐄𝐍𝐓',
            \App\Support\StylishTextFormatter::toUpperBoldSerif('PGD in Supply Chain Management')
        );
        $this->assertEquals(
            '𝐏𝐆𝐃 𝐢𝐧 𝐒𝐮𝐩𝐩𝐥𝐲 𝐂𝐡𝐚𝐢𝐧 𝐌𝐚𝐧𝐚𝐠𝐞𝐦𝐞𝐧𝐭. 𝐒𝐭𝐚𝐫𝐭𝐢𝐧𝐠 𝟑𝟎 𝐎𝐜𝐭𝐨𝐛𝐞𝐫',
            \App\Support\StylishTextFormatter::toBoldSerif('PGD in Supply Chain Management. Starting 30 October')
        );
        $this->assertEquals(
            'Communication Skills for Career Success. Starting 30 October',
            $course->social_card_headline
        );
        $this->assertEquals(
            '𝐂𝐎𝐌𝐌𝐔𝐍𝐈𝐂𝐀𝐓𝐈𝐎𝐍 𝐒𝐊𝐈𝐋𝐋𝐒 𝐅𝐎𝐑 𝐂𝐀𝐑𝐄𝐄𝐑 𝐒𝐔𝐂𝐂𝐄𝐒𝐒',
            $course->stylish_post_header
        );
        $this->assertEquals(
            '𝐂𝐨𝐦𝐦𝐮𝐧𝐢𝐜𝐚𝐭𝐢𝐨𝐧 𝐒𝐤𝐢𝐥𝐥𝐬 𝐟𝐨𝐫 𝐂𝐚𝐫𝐞𝐞𝐫 𝐒𝐮𝐜𝐜𝐞𝐬𝐬. 𝐒𝐭𝐚𝐫𝐭𝐢𝐧𝐠 𝟑𝟎 𝐎𝐜𝐭𝐨𝐛𝐞𝐫',
            $course->stylish_og_title
        );
        $this->assertEquals(
            "𝐂𝐎𝐌𝐌𝐔𝐍𝐈𝐂𝐀𝐓𝐈𝐎𝐍 𝐒𝐊𝐈𝐋𝐋𝐒 𝐅𝐎𝐑 𝐂𝐀𝐑𝐄𝐄𝐑 𝐒𝐔𝐂𝐂𝐄𝐒𝐒\n\n" . $course->short_url,
            $course->stylish_social_caption
        );
        $this->assertStringNotContainsString('📅', $course->stylish_social_caption);

        // 1. Verify Open Graph metadata tags on course page (/courses/{id}) and short link (/c/{id})
        $coursePage = $this->get(route('courses.show', $course));
        $coursePage->assertStatus(200);
        $coursePage->assertSee('property="og:title"', false);
        $coursePage->assertSee($course->stylish_og_title, false);
        $coursePage->assertSee('property="og:description"', false);
        $coursePage->assertSee('property="og:image"', false);
        $coursePage->assertSee('property="og:url"', false);
        $coursePage->assertSee('fb-link-preview-card', false);

        $shortPage = $this->get(route('courses.short', $course->id));
        $shortPage->assertStatus(200);
        $shortPage->assertSee('<link rel="canonical" href="' . $course->short_url . '">', false);
        $shortPage->assertSee('property="og:url" content="' . $course->short_url . '"', false);
        $shortPage->assertSee('property="og:title" content="' . $course->stylish_og_title . '"', false);
        $shortPage->assertSee('property="og:description"', false);
        $shortPage->assertSee('property="og:image"', false);
        $shortPage->assertSee('property="og:url"', false);

        // 2. Verify LinkPreviewResolver resolves internal course URLs (/c/{id} and /courses/{id})
        $preview = \App\Support\LinkPreviewResolver::resolve(route('courses.short', $course->id));
        $this->assertNotNull($preview);
        $this->assertEquals(route('courses.show', $course), $preview['url']);
        $this->assertStringContainsString('Communication Skills for Career Success. Starting 30 October', $preview['title']);
        $this->assertEquals($course->stylish_og_title, $preview['stylish_title']);
        $this->assertEquals($course->stylish_post_header, $preview['post_header']);
        $this->assertStringContainsString('Master professional communication', $preview['description']);
        $this->assertNotEmpty($preview['image']);
        $this->assertNotEmpty($preview['domain']);

        // 3. Verify LinkPreviewResolver parses Open Graph HTML metadata from external HTML
        $sampleHtml = <<<'HTML'
        <!DOCTYPE html>
        <html>
        <head>
            <title>Fallback HTML Title</title>
            <meta property="og:title" content="Global Trade & L/C Summit 2026">
            <meta property="og:description" content="Join 500+ supply chain leaders for live trade finance simulations.">
            <meta property="og:image" content="/assets/banners/lc-summit.jpg">
            <meta property="og:url" content="https://example.org/events/lc-summit">
            <meta property="og:site_name" content="Example Trade Institute">
        </head>
        <body></body>
        </html>
        HTML;

        $parsedOg = \App\Support\LinkPreviewResolver::parseHtmlMetadata($sampleHtml, 'https://example.org/lc-summit');
        $this->assertEquals('Global Trade & L/C Summit 2026', $parsedOg['title']);
        $this->assertEquals('Join 500+ supply chain leaders for live trade finance simulations.', $parsedOg['description']);
        $this->assertEquals('https://example.org/assets/banners/lc-summit.jpg', $parsedOg['image']);
        $this->assertEquals('https://example.org/events/lc-summit', $parsedOg['url']);
        $this->assertEquals('EXAMPLE.ORG', $parsedOg['domain']);

        // 4. Verify sensible fallback when a URL does NOT provide Open Graph metadata
        $minimalHtml = '<html><head></head><body>No OG tags here</body></html>';
        $fallbackOg = \App\Support\LinkPreviewResolver::parseHtmlMetadata($minimalHtml, 'https://customs-portal.gov.bd/tariff-guide-2026');
        $this->assertEquals('CUSTOMS-PORTAL.GOV.BD', $fallbackOg['domain']);
        $this->assertEquals('Tariff Guide 2026 — CUSTOMS-PORTAL.GOV.BD', $fallbackOg['title']);
        $this->assertNull($fallbackOg['image']);
        $this->assertStringContainsString('customs-portal.gov.bd', $fallbackOg['description']);

        // 5. Verify live /link-preview JSON API endpoint
        $apiResponse = $this->getJson(route('link.preview', ['url' => route('courses.short', $course->id)]));
        $apiResponse->assertStatus(200)
            ->assertJsonPath('ok', true)
            ->assertJsonPath('preview.url', route('courses.show', $course));

        // 6. Verify sharing a URL in the Community Feed renders the clickable Facebook-style rich preview card
        $this->actingAs($admin)->post(route('feed.posts.store'), [
            'category' => 'General Discussion',
            'content' => 'Check out our upcoming batch: ' . route('courses.short', $course->id),
        ])->assertRedirect();

        $feedPage = $this->get(route('feed.index'));
        $feedPage->assertStatus(200);
        $feedPage->assertSee('fb-link-preview-card', false);
        $feedPage->assertSee('Communication Skills for Career Success.');
        $feedPage->assertSee(route('courses.show', $course), false);
    }

    public function test_e2e_security_lockdown_and_zero_error_leakage(): void
    {
        // 1. Verify NO standalone dangerous administrative/migration scripts exist in root or public/
        $forbiddenScripts = [
            'migrate.php',
            'migrate-fresh.php',
            'migrateseed.php',
            'migratenotifications.php',
            'unzip.php',
            'fix-env.php',
            'force-fix.php',
            'check-db.php',
            'check-jobs.php',
            'connfigclear.php',
            'optimizeclear.php',
            'storagelink.php',
            'symlink.php',
        ];

        foreach ($forbiddenScripts as $script) {
            $this->assertFileDoesNotExist(base_path($script), "Standalone script {$script} must not exist in project root.");
            $this->assertFileDoesNotExist(public_path($script), "Standalone script public/{$script} must not exist in public directory.");
        }

        // 2. Verify only index.php and cmd.php exist in root and public/
        $this->assertFileExists(base_path('index.php'));
        $this->assertFileExists(public_path('index.php'));
        $this->assertFileExists(base_path('cmd.php'));
        $this->assertFileExists(public_path('cmd.php'));

        // 3. Verify public/index.php, 500.blade.php, and database.blade.php never expose raw errors, paths, migrate.php, or cmd.php PINs
        $publicIndex = file_get_contents(public_path('index.php'));
        $this->assertStringNotContainsString('migrate.php', $publicIndex);
        $this->assertStringNotContainsString('unzip.php', $publicIndex);
        $this->assertStringNotContainsString('cab2026', $publicIndex);
        $this->assertStringNotContainsString('Run Database Setup Wizard', $publicIndex);
        $this->assertStringNotContainsString('EXCEPTION DETAIL', $publicIndex);

        $error500 = file_get_contents(resource_path('views/errors/500.blade.php'));
        $this->assertStringNotContainsString('migrate.php', $error500);
        $this->assertStringNotContainsString('cmd.php', $error500);
        $this->assertStringNotContainsString('cab2026', $error500);
        $this->assertStringNotContainsString('getMessage()', $error500);

        $errorDb = file_get_contents(resource_path('views/errors/database.blade.php'));
        $this->assertStringNotContainsString('migrate.php', $errorDb);
        $this->assertStringNotContainsString('cmd.php', $errorDb);
        $this->assertStringNotContainsString('cab2026', $errorDb);
        $this->assertStringNotContainsString('$message', $errorDb);

        // 4. Verify .env.production and .env.production.example enforce https://acsacademy.bd and APP_DEBUG=false
        $envProd = file_get_contents(base_path('.env.production'));
        $this->assertStringContainsString('APP_URL=https://acsacademy.bd', $envProd);
        $this->assertStringContainsString('APP_DEBUG=false', $envProd);
        $this->assertStringNotContainsString('cabacademy.bd', $envProd);

        // 5. Verify Composer ClassLoader safely checks is_file() and Faker\Generator loads cleanly for Artisan commands
        $classLoader = file_get_contents(base_path('vendor/composer/ClassLoader.php'));
        $this->assertStringContainsString('is_file(', $classLoader);
        $this->assertTrue(class_exists(\Faker\Generator::class));
    }
}
