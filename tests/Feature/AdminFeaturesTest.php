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
}
