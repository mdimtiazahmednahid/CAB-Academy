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
}

