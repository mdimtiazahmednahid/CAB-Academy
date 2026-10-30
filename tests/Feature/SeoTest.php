<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Category;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_robots_txt_protects_sensitive_areas_and_references_sitemap(): void
    {
        $robotsPath = public_path('robots.txt');
        $this->assertFileExists($robotsPath);

        $content = file_get_contents($robotsPath);

        // Explicit Meta / Facebook & Social Crawler Allowlist
        $this->assertStringContainsString('User-agent: facebookexternalhit', $content);
        $this->assertStringContainsString('User-agent: Facebot', $content);
        $this->assertStringContainsString('User-agent: meta-externalagent', $content);

        // Strict sensitive disallows
        $this->assertStringContainsString('Disallow: /admin/', $content);
        $this->assertStringContainsString('Disallow: /dashboard', $content);
        $this->assertStringContainsString('Disallow: /login', $content);
        $this->assertStringContainsString('Disallow: /register', $content);
        $this->assertStringContainsString('Disallow: /password/', $content);
        $this->assertStringContainsString('Disallow: /profile', $content);
        $this->assertStringContainsString('Disallow: /settings', $content);
        $this->assertStringContainsString('Disallow: /feed', $content);
        $this->assertStringContainsString('Disallow: /wishlist', $content);
        $this->assertStringContainsString('Disallow: /courses/*/lessons/', $content);
        $this->assertStringContainsString('Disallow: /courses/*/quizzes/', $content);
        $this->assertStringContainsString('Disallow: /courses/*/checkout', $content);
        $this->assertStringContainsString('Disallow: /payments/', $content);

        // Explicit public allows
        $this->assertStringContainsString('Allow: /', $content);
        $this->assertStringContainsString('Allow: /catalog', $content);
        $this->assertStringContainsString('Allow: /courses', $content);
        $this->assertStringContainsString('Allow: /category/', $content);
        $this->assertStringContainsString('Allow: /blog', $content);
        $this->assertStringContainsString('Allow: /instructors', $content);

        // Sitemap reference
        $this->assertStringContainsString('sitemap.xml', $content);
    }

    public function test_sitemap_xml_generates_valid_xml_with_public_pages_and_zero_sensitive_urls(): void
    {
        $course = Course::create([
            'title' => 'Executive Export Import Mastery',
            'description' => 'Comprehensive masterclass on export import procedures',
            'is_published' => true,
            'category' => 'Export-Import',
            'price' => 8500,
        ]);

        $unpublishedCourse = Course::create([
            'title' => 'Confidential Internal Draft Course',
            'description' => 'Draft course not ready for public',
            'is_published' => false,
            'price' => 5000,
        ]);

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml; charset=utf-8');

        $content = $response->getContent();

        // Valid XML structure
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?>', $content);
        $this->assertStringContainsString('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"', $content);

        // Published course must be included
        $this->assertStringContainsString(route('courses.show', $course), $content);

        // Core public routes must be included
        $this->assertStringContainsString(route('catalog.index'), $content);
        $this->assertStringContainsString(route('courses.index'), $content);
        $this->assertStringContainsString(route('blog.index'), $content);
        $this->assertStringContainsString(route('instructors.index'), $content);
        $this->assertStringContainsString(route('about.index'), $content);
        $this->assertStringContainsString(route('contact.index'), $content);

        // Draft/unpublished course must NOT be included
        $this->assertStringNotContainsString(route('courses.show', $unpublishedCourse), $content);

        // Sensitive paths must NEVER appear in sitemap
        $this->assertStringNotContainsString('/admin', $content);
        $this->assertStringNotContainsString('/dashboard', $content);
        $this->assertStringNotContainsString('/login', $content);
        $this->assertStringNotContainsString('/register', $content);
        $this->assertStringNotContainsString('/checkout', $content);
        $this->assertStringNotContainsString('/lessons', $content);
    }

    public function test_sensitive_auth_routes_have_strict_noindex_robots_meta_tags(): void
    {
        $loginResponse = $this->get('/login');
        $loginResponse->assertStatus(200);
        $loginResponse->assertSee('<meta name="robots" content="noindex, nofollow, noarchive, nosnippet">', false);

        $registerResponse = $this->get('/register');
        $registerResponse->assertStatus(200);
        $registerResponse->assertSee('<meta name="robots" content="noindex, nofollow, noarchive, nosnippet">', false);

        $user = User::factory()->create();
        $onboardingResponse = $this->actingAs($user)->get('/onboarding');
        $onboardingResponse->assertStatus(200);
        $onboardingResponse->assertSee('<meta name="robots" content="noindex, nofollow, noarchive, nosnippet">', false);

        $admin = User::factory()->create(['role' => 'admin']);
        $statementResponse = $this->actingAs($admin)->get('/admin/accounting/statement');
        $statementResponse->assertStatus(200);
        $statementResponse->assertSee('<meta name="robots" content="noindex, nofollow, noarchive, nosnippet">', false);
    }

    public function test_public_pages_have_index_follow_and_educational_organization_schema(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('index, follow', false);
        $response->assertSee('"@type": "EducationalOrganization"', false);
        $response->assertSee('"name": "ACS Academy"', false);
        $response->assertSee('<link rel="canonical"', false);
        $response->assertSee('https://www.facebook.com/academyofcommercialandsupplychain', false);
        $response->assertSee('<meta property="article:publisher" content="https://www.facebook.com/academyofcommercialandsupplychain">', false);

        $contactResponse = $this->get('/contact');
        $contactResponse->assertStatus(200);
        $contactResponse->assertSee('https://www.facebook.com/academyofcommercialandsupplychain', false);
        $contactResponse->assertSee('Official Facebook Page', false);
    }

    public function test_course_show_page_includes_course_faq_and_breadcrumb_schemas(): void
    {
        $course = Course::create([
            'title' => 'Mastering LC and Trade Finance',
            'is_published' => true,
            'price' => 12000,
            'category' => 'Commercial Banking',
            'short_description' => 'Comprehensive masterclass on letter of credit operations in Dhaka.',
            'description' => 'In-depth commercial banking masterclass detailing LC structures, uniform customs and practice (UCP 600), and customs clearance.',
        ]);

        $response = $this->get(route('courses.show', $course));
        $response->assertStatus(200);

        // SEO meta tags
        $response->assertSee('<link rel="canonical" href="' . route('courses.show', $course) . '"', false);
        $response->assertSee('Comprehensive masterclass on letter of credit operations in Dhaka.');

        // Schema.org JSON-LD
        $response->assertSee('"@type": "Course"', false);
        $response->assertSee('"name": "Mastering LC and Trade Finance"', false);
        $response->assertSee('"price": 12000', false);
        $response->assertSee('"priceCurrency": "BDT"', false);
        $response->assertSee('"@type": "FAQPage"', false);
        $response->assertSee('"@type": "BreadcrumbList"', false);
    }

    public function test_admin_can_connect_search_console_and_configure_selective_non_sensitive_auto_indexing(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@cab-academy.com',
        ]);

        $course = Course::create([
            'title' => 'Certified Supply Chain Director',
            'is_published' => true,
            'price' => 9500,
        ]);

        // Admin pastes full HTML meta tag for Google Search Console & disables job portal indexing
        $response = $this->actingAs($admin)->post(route('admin.settings.store'), [
            'site_name' => 'ACS Academy',
            'google_site_verification' => '<meta name="google-site-verification" content="GSC_VERIFY_TOKEN_98765" />',
            'bing_site_verification' => 'BING_VERIFY_TOKEN_54321',
            'google_analytics_id' => 'G-ACS1234567',
            'seo_auto_index_enabled' => '1',
            'seo_index_courses' => '1',
            'seo_index_catalog' => '1',
            'seo_index_blog' => '1',
            'seo_index_instructors' => '1',
            'seo_index_jobs' => '0',
            'seo_index_about_contact' => '1',
            'seo_index_legal' => '1',
        ]);

        $response->assertRedirect();

        // 1. Clean token extracted and rendered on public home page
        $home = $this->get('/');
        $home->assertStatus(200);
        $home->assertSee('<meta name="google-site-verification" content="GSC_VERIFY_TOKEN_98765">', false);
        $home->assertSee('<meta name="msvalidate.01" content="BING_VERIFY_TOKEN_54321">', false);
        $home->assertSee('G-ACS1234567', false);

        // 2. Google HTML file verification endpoint works automatically
        $googleFile = $this->get('/googleGSC_VERIFY_TOKEN_98765.html');
        $googleFile->assertStatus(200);
        $googleFile->assertSee('google-site-verification: googleGSC_VERIFY_TOKEN_98765.html', false);

        // 3. IndexNow key file endpoint works automatically
        $indexNowKey = \App\Support\SeoIndexingManager::getIndexNowKey();
        $indexNowResp = $this->get('/' . $indexNowKey . '.txt');
        $indexNowResp->assertStatus(200);
        $this->assertSame($indexNowKey, trim($indexNowResp->getContent()));

        // 4. Sitemap includes enabled public courses, excludes disabled /jobs section, and excludes all sensitive routes
        $sitemap = $this->get('/sitemap.xml');
        $sitemap->assertStatus(200);
        $this->assertStringContainsString(route('courses.show', $course), $sitemap->getContent());
        $this->assertStringNotContainsString(route('jobs.index'), $sitemap->getContent());
        $this->assertStringNotContainsString('/admin', $sitemap->getContent());
        $this->assertStringNotContainsString('/cmd.php', $sitemap->getContent());

        // 5. Disabled public section (/jobs) receives noindex, nofollow directive
        $jobsPage = $this->get('/jobs');
        $jobsPage->assertStatus(200);
        $jobsPage->assertSee('<meta name="robots" content="noindex, nofollow">', false);

        // 6. Admin can trigger manual Search Console / IndexNow sync ping
        $pingResp = $this->actingAs($admin)->post(route('admin.settings.seo_ping'));
        $pingResp->assertRedirect();
        $this->assertNotEmpty(\App\Models\Setting::getVal('seo_last_ping_at'));
    }
}
