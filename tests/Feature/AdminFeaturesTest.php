<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\ContactInquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_and_update_inquiries()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $inquiry = ContactInquiry::create([
            'name' => 'Corporate Partner',
            'email' => 'partner@company.com',
            'phone' => '01917054664',
            'inquiry_type' => 'corporate',
            'subject' => 'Executive Batch Requirement',
            'message' => 'Need batch for 20 executives.',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->get('/admin/inquiries');
        $response->assertStatus(200);
        $response->assertSee('Corporate Partner');

        $patchResponse = $this->actingAs($admin)->patch("/admin/inquiries/{$inquiry->id}/status", [
            'status' => 'contacted',
        ]);
        $patchResponse->assertSessionHas('success');
        $this->assertEquals('contacted', $inquiry->fresh()->status);
    }

    public function test_admin_can_manage_blog_posts()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/admin/blogs', [
            'title' => 'Understanding New SRO on Bonded Warehouse 2026',
            'category' => 'Bonded Warehouse',
            'excerpt' => 'An in-depth regulatory breakdown of bonded facility compliance.',
            'content' => 'Full article detailing sub-section rules and audit requirements.',
            'author_name' => 'Trade Counsel',
            'is_published' => '1',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('blog_posts', [
            'title' => 'Understanding New SRO on Bonded Warehouse 2026',
            'category' => 'Bonded Warehouse',
            'is_published' => true,
        ]);
    }

    public function test_admin_can_access_landing_page_customizer()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get('/admin/frontend');
        $response->assertStatus(200);
        $response->assertSee('Landing Page Customizer');
        $response->assertSee('1. Executive Hero');
        $response->assertSee('2. Valuable Clients');
        $response->assertSee('3. Sticky Categories');
        $response->assertSee('4. Course Showcase');
    }

    public function test_admin_can_update_landing_page_sections()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $postData = [
            'landing_page_mode' => 'default',
            'landing_hero_enabled' => '1',
            'landing_hero_title' => 'CAB Executive Institute',
            'landing_hero_tagline' => 'Empowering Leaders',
            'landing_hero_subtitle' => 'Exclusive custom masterclass subtitle',
            'landing_hero_cta1_text' => 'Join Masterclasses',
            'landing_hero_cta1_url' => '#courses',
            'landing_clients_enabled' => '0', // disabled section
            'landing_counseling_title' => 'Direct Executive Advisory',
        ];

        $response = $this->actingAs($admin)->post('/admin/frontend', $postData);
        $response->assertSessionHas('success');

        // Check homepage reflects updated settings
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('CAB Executive Institute');
        $homeResponse->assertSee('Empowering Leaders');
        $homeResponse->assertSee('Exclusive custom masterclass subtitle');
        $homeResponse->assertSee('Join Masterclasses');
        $homeResponse->assertSee('Direct Executive Advisory');
        // Since clients section was disabled:
        $homeResponse->assertDontSee('Our Valuable Clients');
    }

    public function test_admin_can_manage_and_remove_instructors()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // 1. Create Instructor via admin backend
        $postResponse = $this->actingAs($admin)->post('/admin/instructors', [
            'name' => 'Faculty Chair – Trade & Customs',
            'title' => 'Principal Trade Finance Fellow',
            'expertise' => 'Banking, L/C, Customs & Shipping Procedure',
            'email' => 'trade.faculty@cab-academy.com',
            'phone' => '01917-054664',
            'bio' => 'Distinguished advisory expert.',
            'order' => 1,
            'is_active' => '1',
        ]);

        $postResponse->assertSessionHas('success');
        $this->assertDatabaseHas('instructors', [
            'name' => 'Faculty Chair – Trade & Customs',
            'expertise' => 'Banking, L/C, Customs & Shipping Procedure',
            'is_active' => true,
        ]);

        $instructor = \App\Models\Instructor::where('name', 'Faculty Chair – Trade & Customs')->first();

        // 2. Public directory displays instructor
        $publicResponse = $this->get('/instructors');
        $publicResponse->assertStatus(200);
        $publicResponse->assertSee('Faculty Chair – Trade & Customs');

        // 3. Admin can remove instructor from backend
        $deleteResponse = $this->actingAs($admin)->delete("/admin/instructors/{$instructor->id}");
        $deleteResponse->assertSessionHas('success');
        $this->assertSoftDeleted('instructors', ['id' => $instructor->id]);

        // 4. Removed instructor no longer appears on public directory
        $publicAfterDelete = $this->get('/instructors');
        $publicAfterDelete->assertDontSee('Faculty Chair – Trade & Customs');
    }

    public function test_admin_can_update_about_and_contact_pages_dynamically()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Update About and Contact settings
        $response = $this->actingAs($admin)->post('/admin/frontend', [
            'about_hero_title' => 'CAB Academy World Class Trade Leadership',
            'about_stat_graduates' => '12,500+ Executives',
            'contact_hotline' => '01917-999888',
            'contact_address' => 'Pallabi Executive Tower, Dhaka 1216',
        ]);

        $response->assertSessionHas('success');

        // Check public About page reflects changes
        $aboutRes = $this->get('/about');
        $aboutRes->assertStatus(200);
        $aboutRes->assertSee('CAB Academy World Class Trade Leadership');
        $aboutRes->assertSee('12,500+ Executives');

        // Check public Contact page reflects changes
        $contactRes = $this->get('/contact');
        $contactRes->assertStatus(200);
        $contactRes->assertSee('01917-999888');
        $contactRes->assertSee('Pallabi Executive Tower, Dhaka 1216');
    }

    public function test_admin_can_override_legal_policies_dynamically()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/admin/frontend', [
            'policy_privacy_content' => 'Dynamic Custom Privacy Policy for CAB Enterprise.',
            'policy_terms_content' => 'Dynamic Custom Terms of Service for CAB Enterprise.',
        ]);

        $response->assertSessionHas('success');

        $privacyRes = $this->get('/privacy-policy');
        $privacyRes->assertStatus(200);
        $privacyRes->assertSee('Dynamic Custom Privacy Policy for CAB Enterprise.');

        $termsRes = $this->get('/terms-of-service');
        $termsRes->assertStatus(200);
        $termsRes->assertSee('Dynamic Custom Terms of Service for CAB Enterprise.');
    }

    public function test_admin_can_manage_categories_dynamically()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // List categories
        $res = $this->actingAs($admin)->get('/admin/categories');
        $res->assertStatus(200);

        // Create new category
        $createRes = $this->actingAs($admin)->post('/admin/categories', [
            'name' => 'Maritime Customs & Port Logistics',
            'slug' => 'maritime-customs',
            'icon' => 'fa-anchor',
            'description' => 'Complete Chittagong port logistics and sea customs clearance.',
            'order' => 1,
            'is_active' => '1',
        ]);
        $createRes->assertRedirect('/admin/categories');
        $this->assertDatabaseHas('categories', [
            'slug' => 'maritime-customs',
            'name' => 'Maritime Customs & Port Logistics',
        ]);

        $cat = \App\Models\Category::where('slug', 'maritime-customs')->first();

        // Update category
        $updateRes = $this->actingAs($admin)->put("/admin/categories/{$cat->id}", [
            'name' => 'Maritime Logistics & Port Operations',
            'slug' => 'maritime-customs',
            'icon' => 'fa-ship',
            'description' => 'Updated port handling and marine insurance.',
            'order' => 2,
            'is_active' => '1',
        ]);
        $updateRes->assertRedirect('/admin/categories');
        $this->assertEquals('Maritime Logistics & Port Operations', $cat->fresh()->name);

        // Delete category
        $delRes = $this->actingAs($admin)->delete("/admin/categories/{$cat->id}");
        $delRes->assertRedirect('/admin/categories');
        $this->assertDatabaseMissing('categories', ['id' => $cat->id]);
    }

    public function test_seeder_produces_exact_eight_courses_and_twelve_categories()
    {
        $this->seed(\Database\Seeders\DemoDataSeeder::class);

        $this->assertEquals(8, \App\Models\Course::count(), 'Database must contain exactly 8 courses.');
        $this->assertEquals(12, \App\Models\Category::count(), 'Database must contain exactly 12 categories.');

        // Test homepage renders both sections without duplicates
        $homeRes = $this->get('/');
        $homeRes->assertStatus(200);
        $homeRes->assertSee('Certificate Course in L/C');
        $homeRes->assertSee('Commercial Guideline with Customs');

        // Test catalog renders
        $catalogRes = $this->get('/catalog');
        $catalogRes->assertStatus(200);
        $catalogRes->assertSee('Export – Import');
        $catalogRes->assertSee('Supply Chain Management');

        // Test type filters
        $certRes = $this->get('/certificate-course');
        $certRes->assertRedirect('/catalog?type=certificate');

        $dayRes = $this->get('/day-long-course');
        $dayRes->assertRedirect('/catalog?type=day_long');
    }
}

