<?php

namespace Tests\Feature;

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseShortLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_short_link_renders_beautiful_share_banner_with_clickable_thumbnail_and_action_buttons()
    {
        $course = Course::create([
            'title' => 'Executive Export Import Course',
            'short_description' => 'Comprehensive masterclass in international trade and documentation.',
            'description' => 'Full practical curriculum covering L/C, customs clearance, and HS code tariffs.',
            'thumbnail' => 'course_covers/exim_master.png',
            'price' => 4500,
            'is_published' => true,
        ]);

        $response = $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
        ])->get("/c/{$course->id}");

        $response->assertStatus(200);
        $response->assertSee(e($course->title));
        $response->assertSee('Enroll Now (30 Sec)');
        $response->assertSee('WhatsApp Counselor');
        $response->assertSee('Full Syllabus');
        $response->assertSee('Copy Link');
        $response->assertSee('Call Hotline');
        // Clickable thumbnail link leading to enrollment
        $response->assertSee(route('courses.show', $course) . '?enroll=true', false);
        // OpenGraph large image tags present
        $response->assertSee('property="og:image"', false);
        $response->assertSee('property="og:image:width" content="1200"', false);
        $response->assertSee('property="og:image:height" content="630"', false);
    }

    public function test_short_link_direct_param_redirects_to_course_details()
    {
        $course = Course::create([
            'title' => 'Direct Redirection Program',
            'price' => 3000,
            'is_published' => true,
        ]);

        $response = $this->get("/c/{$course->id}?direct=1");

        $response->assertStatus(302);
        $response->assertRedirect(route('courses.show', $course));
    }

    public function test_facebook_crawler_receives_rich_opengraph_preview_with_image_dimensions()
    {
        $course = Course::create([
            'title' => 'Supply Chain Optimization & Logistics',
            'short_description' => 'Master global supply chain management with CAB Academy experts.',
            'description' => 'Full curriculum covering procurement, freight forwarding, and warehousing.',
            'thumbnail' => 'course_covers/supply_chain.png',
            'price' => 5500,
            'is_published' => true,
        ]);

        $response = $this->withHeaders([
            'User-Agent' => 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)',
        ])->get("/c/{$course->id}");

        $response->assertStatus(200);
        $response->assertSee('property="og:title"', false);
        $response->assertSee(e($course->title), false);
        $response->assertSee('property="og:image"', false);
        $response->assertSee('content="http://localhost/storage/course_covers/supply_chain.png"', false);
        $response->assertSee('property="og:image:width" content="1200"', false);
        $response->assertSee('property="og:image:height" content="630"', false);
        $response->assertSee('property="og:image:secure_url"', false);
        $response->assertSee('name="twitter:card" content="summary_large_image"', false);
    }

    public function test_course_show_page_renders_organized_opengraph_metadata_and_share_buttons()
    {
        $course = Course::create([
            'title' => 'VAT & Tax Compliance Masterclass',
            'short_description' => 'Practical assessment and filing for corporate professionals.',
            'thumbnail' => 'https://images.unsplash.com/photo-1554224154-26032ffc0d07',
            'price' => 3000,
            'is_published' => true,
        ]);

        $response = $this->get(route('courses.show', $course));

        $response->assertStatus(200);
        $response->assertSee('property="og:image"', false);
        $response->assertSee('property="og:image:width" content="1200"', false);
        $response->assertSee('property="og:image:height" content="630"', false);
        $response->assertSee($course->facebook_share_url, false);
        $response->assertSee($course->short_url, false);
        $response->assertSee('Enroll Now (30 Sec)');
        $response->assertSee('WhatsApp Counselor');
    }

    public function test_course_model_generates_valid_short_and_share_urls()
    {
        $course = Course::create([
            'title' => 'Letter of Credit Banking',
            'price' => 6000,
            'is_published' => true,
        ]);

        $this->assertStringContainsString("/c/{$course->id}", $course->short_url);
        $this->assertStringContainsString('facebook.com/sharer/sharer.php', $course->facebook_share_url);
        $this->assertStringContainsString(urlencode($course->short_url), $course->facebook_share_url);
        $this->assertStringContainsString('api.whatsapp.com/send', $course->whatsapp_share_url);
        $this->assertStringContainsString('wa.me/8801917054664', $course->counselor_whatsapp_url);
        $this->assertStringContainsString('linkedin.com/sharing/share-offsite', $course->linkedin_share_url);
        $this->assertStringStartsWith('http', $course->og_image_url);
    }
}
