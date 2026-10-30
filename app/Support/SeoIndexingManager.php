<?php

namespace App\Support;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Course;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SeoIndexingManager
{
    /**
     * Sensitive path prefixes/patterns that must NEVER be indexed by search engines
     * or included in sitemaps or auto-index pings.
     */
    public const SENSITIVE_PATTERNS = [
        'admin',
        'admin/*',
        'cmd.php',
        'cmd.php*',
        'dashboard',
        'dashboard/*',
        'student',
        'student/*',
        'profile',
        'profile/*',
        'settings',
        'settings/*',
        'notifications',
        'notifications/*',
        'onboarding',
        'onboarding/*',
        'performance',
        'performance/*',
        'login',
        'register',
        'forgot-password',
        'reset-password',
        'reset-password/*',
        'password/*',
        'verify-email',
        'verify-email/*',
        'feed',
        'feed/*',
        'community',
        'community/*',
        'wishlist',
        'wishlist/*',
        'courses/*/lessons',
        'courses/*/lessons/*',
        'courses/*/quizzes',
        'courses/*/quizzes/*',
        'courses/*/checkout',
        'courses/*/checkout/*',
        'courses/*/certificate',
        'courses/*/certificate/*',
        'payments',
        'payments/*',
        'invoices',
        'invoices/*',
        'api/*',
        'reset-db',
        'up',
    ];

    /**
     * Extract a clean verification token whether the admin pasted a full <meta ... content="TOKEN"> tag,
     * a "google-site-verification=TOKEN" string, a "googleTOKEN.html" filename, or the raw token itself.
     */
    public static function extractVerificationCode(?string $rawInput): string
    {
        $val = trim((string) $rawInput);
        if ($val === '') {
            return '';
        }

        // Case 1: Full HTML <meta name="..." content="TOKEN"> tag
        if (preg_match('/content\s*=\s*["\']([^"\']+)["\']/i', $val, $matches)) {
            return trim($matches[1]);
        }

        // Case 2: google-site-verification=TOKEN
        if (preg_match('/^(?:google-site-verification|msvalidate\.01|yandex-verification)\s*=\s*(.+)$/i', $val, $matches)) {
            return trim($matches[1], " \t\n\r\0\x0B\"'");
        }

        // Case 3: google1234567890abcdef.html file name
        if (preg_match('/^google([a-zA-Z0-9_-]+)\.html$/i', $val, $matches)) {
            return trim($matches[1]);
        }

        // Strip any stray HTML tags or quotes
        $clean = strip_tags($val);
        return trim($clean, " \t\n\r\0\x0B\"'<>");
    }

    /**
     * Get or generate the IndexNow API key for instant search engine indexing.
     */
    public static function getIndexNowKey(): string
    {
        $key = trim((string) Setting::getVal('seo_indexnow_key', ''));
        if ($key !== '' && preg_match('/^[a-zA-Z0-9\-]{8,128}$/', $key)) {
            return $key;
        }

        return substr(md5(config('app.url', 'https://acsacademy.bd') . '_acs_indexnow_v1'), 0, 32);
    }

    /**
     * Check whether a specific non-sensitive section is enabled for auto-indexing.
     */
    public static function isSectionIndexable(string $sectionKey): bool
    {
        $masterEnabled = (string) Setting::getVal('seo_auto_index_enabled', '1');
        if ($masterEnabled === '0' || strtolower($masterEnabled) === 'false') {
            return false;
        }

        $val = (string) Setting::getVal($sectionKey, '1');
        return $val !== '0' && strtolower($val) !== 'false';
    }

    /**
     * Determine if a given URL or URI path belongs to a sensitive/private area.
     */
    public static function isSensitiveUrlOrPath(string $urlOrPath): bool
    {
        $parsedPath = parse_url($urlOrPath, PHP_URL_PATH);
        $path = ltrim((string) ($parsedPath ?? $urlOrPath), '/');

        foreach (self::SENSITIVE_PATTERNS as $pattern) {
            if (Str::is($pattern, $path)) {
                return true;
            }
        }

        $query = (string) parse_url($urlOrPath, PHP_URL_QUERY);
        if ($query !== '' && preg_match('/(?:token|auth|session|password|signature)=/i', $query)) {
            return true;
        }

        return false;
    }

    /**
     * Compute the appropriate robots meta directive for the current HTTP request.
     * Sensitive routes ALWAYS receive 'noindex, nofollow, noarchive, nosnippet'.
     */
    public static function getRobotsDirectiveForRequest(?Request $request = null, ?string $sectionOverride = null): string
    {
        $request = $request ?: request();

        // Explicit view @section('robots') override if already set to noindex
        if ($sectionOverride !== null && str_contains(strtolower($sectionOverride), 'noindex')) {
            return $sectionOverride;
        }

        $path = ltrim($request->path(), '/');
        if (self::isSensitiveUrlOrPath($path)) {
            return 'noindex, nofollow, noarchive, nosnippet';
        }

        // Check master indexing toggle
        if (!self::isSectionIndexable('seo_auto_index_enabled')) {
            return 'noindex, nofollow';
        }

        // Check section-level indexing toggles
        if (($request->is('courses/*') || $request->is('c/*')) && !self::isSectionIndexable('seo_index_courses')) {
            return 'noindex, nofollow';
        }
        if (($request->is('catalog*') || $request->is('courses') || $request->is('category/*')) && !self::isSectionIndexable('seo_index_catalog')) {
            return 'noindex, nofollow';
        }
        if ($request->is('blog*') && !self::isSectionIndexable('seo_index_blog')) {
            return 'noindex, nofollow';
        }
        if (($request->is('instructors*') || $request->is('faculty*')) && !self::isSectionIndexable('seo_index_instructors')) {
            return 'noindex, nofollow';
        }
        if (($request->is('jobs*') || $request->is('careers*')) && !self::isSectionIndexable('seo_index_jobs')) {
            return 'noindex, nofollow';
        }
        if (($request->is('about*') || $request->is('contact*') || $request->is('verify-certificate*')) && !self::isSectionIndexable('seo_index_about_contact')) {
            return 'noindex, nofollow';
        }
        if (($request->is('privacy-policy*') || $request->is('terms-of-service*') || $request->is('refund-policy*')) && !self::isSectionIndexable('seo_index_legal')) {
            return 'noindex, nofollow';
        }

        return $sectionOverride ?: 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';
    }

    /**
     * Build the complete list of strictly non-sensitive, public sitemap URL entries.
     */
    public static function getSitemapUrls(): array
    {
        if (!self::isSectionIndexable('seo_auto_index_enabled')) {
            return [];
        }

        $urls = [];

        // 1. Core Landing Page (Always Priority 1.0 when auto-indexing is enabled)
        $urls[] = [
            'loc' => url('/'),
            'lastmod' => now()->startOfDay()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => '1.0',
        ];

        // 2. Public Course Catalog & Category Pages
        if (self::isSectionIndexable('seo_index_catalog')) {
            $urls[] = [
                'loc' => route('catalog.index'),
                'lastmod' => now()->startOfDay()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '0.9',
            ];
            $urls[] = [
                'loc' => route('courses.index'),
                'lastmod' => now()->startOfDay()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '0.9',
            ];
            $urls[] = [
                'loc' => route('catalog.certificate'),
                'lastmod' => now()->startOfWeek()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
            $urls[] = [
                'loc' => route('catalog.day_long'),
                'lastmod' => now()->startOfWeek()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];

            try {
                $categories = Category::active()->get();
                if ($categories->isNotEmpty()) {
                    foreach ($categories as $cat) {
                        $urls[] = [
                            'loc' => route('catalog.category', $cat->slug),
                            'lastmod' => $cat->updated_at ? $cat->updated_at->toAtomString() : now()->toAtomString(),
                            'changefreq' => 'weekly',
                            'priority' => '0.8',
                        ];
                    }
                } else {
                    $catNames = Course::where('is_published', true)
                        ->whereNotNull('category')
                        ->where('category', '!=', '')
                        ->distinct()
                        ->pluck('category');
                    foreach ($catNames as $cName) {
                        $urls[] = [
                            'loc' => route('catalog.category', Str::slug($cName)),
                            'lastmod' => now()->startOfWeek()->toAtomString(),
                            'changefreq' => 'weekly',
                            'priority' => '0.8',
                        ];
                    }
                }
            } catch (\Throwable $e) {
            }
        }

        // 3. Published Courses (Strictly Active, Non-Draft Courses Only)
        if (self::isSectionIndexable('seo_index_courses')) {
            try {
                $courses = Course::where('is_published', true)->get();
                foreach ($courses as $c) {
                    $urls[] = [
                        'loc' => route('courses.show', $c),
                        'lastmod' => $c->updated_at ? $c->updated_at->toAtomString() : now()->toAtomString(),
                        'changefreq' => 'weekly',
                        'priority' => '0.9',
                    ];
                }
            } catch (\Throwable $e) {
            }
        }

        // 4. Executive Faculty & Instructors
        if (self::isSectionIndexable('seo_index_instructors')) {
            $urls[] = [
                'loc' => route('instructors.index'),
                'lastmod' => now()->startOfMonth()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ];
            $urls[] = [
                'loc' => route('faculty.index'),
                'lastmod' => now()->startOfMonth()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ];
        }

        // 5. Blog & Knowledge Hub Articles (Published Only)
        if (self::isSectionIndexable('seo_index_blog')) {
            $urls[] = [
                'loc' => route('blog.index'),
                'lastmod' => now()->startOfDay()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '0.8',
            ];
            try {
                $blogs = BlogPost::published()->get();
                foreach ($blogs as $b) {
                    $urls[] = [
                        'loc' => route('blog.show', $b->slug),
                        'lastmod' => $b->updated_at ? $b->updated_at->toAtomString() : now()->toAtomString(),
                        'changefreq' => 'monthly',
                        'priority' => '0.7',
                    ];
                }
            } catch (\Throwable $e) {
            }
        }

        // 6. Placement & Career Portal
        if (self::isSectionIndexable('seo_index_jobs')) {
            $urls[] = [
                'loc' => route('jobs.index'),
                'lastmod' => now()->startOfWeek()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ];
            $urls[] = [
                'loc' => route('careers.index'),
                'lastmod' => now()->startOfWeek()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ];
        }

        // 7. Institutional Trust & Contact Pages
        if (self::isSectionIndexable('seo_index_about_contact')) {
            $urls[] = [
                'loc' => route('about.index'),
                'lastmod' => now()->startOfMonth()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ];
            $urls[] = [
                'loc' => route('contact.index'),
                'lastmod' => now()->startOfMonth()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ];
            $urls[] = [
                'loc' => route('certificate.verify'),
                'lastmod' => now()->startOfMonth()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ];
        }

        // 8. Legal & Compliance Pages
        if (self::isSectionIndexable('seo_index_legal')) {
            $urls[] = [
                'loc' => route('legal.privacy'),
                'lastmod' => now()->startOfYear()->toAtomString(),
                'changefreq' => 'yearly',
                'priority' => '0.4',
            ];
            $urls[] = [
                'loc' => route('legal.terms'),
                'lastmod' => now()->startOfYear()->toAtomString(),
                'changefreq' => 'yearly',
                'priority' => '0.4',
            ];
            $urls[] = [
                'loc' => route('legal.refund'),
                'lastmod' => now()->startOfYear()->toAtomString(),
                'changefreq' => 'yearly',
                'priority' => '0.4',
            ];
        }

        // Final safety filter: strictly strip any URL matching sensitive patterns
        return array_values(array_filter($urls, function (array $entry) {
            return !self::isSensitiveUrlOrPath($entry['loc']);
        }));
    }

    /**
     * Generate the dynamic robots.txt content protecting all sensitive directories
     * while allowing configured non-sensitive public sections.
     */
    public static function generateRobotsTxt(): string
    {
        $sitemapUrl = rtrim(config('app.url', 'https://acsacademy.bd'), '/') . '/sitemap.xml';

        $lines = [
            '# ==============================================================================',
            '# EXPLICIT ALLOWLIST FOR META / FACEBOOK & SOCIAL MEDIA LINK PREVIEW CRAWLERS',
            '# ==============================================================================',
            'User-agent: facebookexternalhit',
            'User-agent: facebookexternalhit/1.1',
            'User-agent: Facebot',
            'User-agent: meta-externalagent',
            'User-agent: meta-externalagent/1.1',
            'User-agent: FacebookBot',
            'User-agent: WhatsApp',
            'User-agent: Twitterbot',
            'User-agent: LinkedInBot',
            'User-agent: TelegramBot',
            'Allow: /',
            'Allow: /c/',
            'Allow: /courses/',
            'Allow: /storage/',
            'Allow: /uploads/',
            'Allow: /images/',
            '',
            'User-agent: *',
            '',
            '# ==============================================================================',
            '# SENSITIVE & PRIVATE DIRECTORIES (STRICTLY DISALLOWED FROM SEARCH ENGINE INDEXING)',
            '# ==============================================================================',
            '# Administrative & Governance Desks',
            'Disallow: /admin/',
            'Disallow: /admin',
            'Disallow: /cmd.php',
            '',
            '# Authenticated Student Dashboard & User Portals',
            'Disallow: /dashboard',
            'Disallow: /student/',
            'Disallow: /profile',
            'Disallow: /settings',
            'Disallow: /notifications',
            'Disallow: /onboarding',
            'Disallow: /performance',
            '',
            '# Authentication, Credentials & Verification Routes',
            'Disallow: /login',
            'Disallow: /register',
            'Disallow: /forgot-password',
            'Disallow: /reset-password',
            'Disallow: /password/',
            'Disallow: /verify-email',
            '',
            '# User Interactive Submissions & Personal Data',
            'Disallow: /feed',
            'Disallow: /community',
            'Disallow: /wishlist',
            '',
            '# Sensitive Course Curriculum & Learning Assets (Restricted to Enrolled Students)',
            'Disallow: /courses/*/lessons/',
            'Disallow: /courses/*/quizzes/',
            'Disallow: /courses/*/checkout',
            'Disallow: /courses/*/certificate',
            'Disallow: /payments/',
            'Disallow: /invoices/',
            '',
            '# Internal Utilities, API & Server Endpoints',
            'Disallow: /api/',
            'Disallow: /reset-db',
            'Disallow: /*.json$',
            'Disallow: /*?*token=',
            'Disallow: /*?*auth=',
            'Disallow: /*?*session=',
            '',
            '# ==============================================================================',
            '# PUBLIC, SEARCH-OPTIMIZED AREAS (EXPLICITLY ALLOWED FOR MAXIMUM CRAWL VELOCITY)',
            '# ==============================================================================',
            'Allow: /',
        ];

        if (self::isSectionIndexable('seo_index_catalog')) {
            $lines[] = 'Allow: /catalog';
            $lines[] = 'Allow: /courses';
            $lines[] = 'Allow: /category/';
        } else {
            $lines[] = 'Disallow: /catalog';
            $lines[] = 'Disallow: /category/';
        }

        if (self::isSectionIndexable('seo_index_courses')) {
            $lines[] = 'Allow: /c/';
        }

        if (self::isSectionIndexable('seo_index_blog')) {
            $lines[] = 'Allow: /blog';
            $lines[] = 'Allow: /blog/*';
        } else {
            $lines[] = 'Disallow: /blog';
        }

        if (self::isSectionIndexable('seo_index_instructors')) {
            $lines[] = 'Allow: /instructors';
            $lines[] = 'Allow: /faculty';
        } else {
            $lines[] = 'Disallow: /instructors';
            $lines[] = 'Disallow: /faculty';
        }

        if (self::isSectionIndexable('seo_index_jobs')) {
            $lines[] = 'Allow: /jobs';
            $lines[] = 'Allow: /careers';
        } else {
            $lines[] = 'Disallow: /jobs';
            $lines[] = 'Disallow: /careers';
        }

        if (self::isSectionIndexable('seo_index_about_contact')) {
            $lines[] = 'Allow: /about';
            $lines[] = 'Allow: /contact';
            $lines[] = 'Allow: /verify-certificate/';
        }

        if (self::isSectionIndexable('seo_index_legal')) {
            $lines[] = 'Allow: /privacy-policy';
            $lines[] = 'Allow: /terms-of-service';
            $lines[] = 'Allow: /refund-policy';
        }

        $lines[] = 'Allow: /build/';
        $lines[] = 'Allow: /images/';
        $lines[] = 'Allow: /storage/';
        $lines[] = 'Allow: /uploads/';
        $lines[] = '';
        $lines[] = '# ==============================================================================';
        $lines[] = '# SITEMAP SPECIFICATION';
        $lines[] = 'Sitemap: ' . $sitemapUrl;

        return implode("\n", $lines) . "\n";
    }

    /**
     * Synchronize physical public/robots.txt and root robots.txt files when settings change (if writable).
     */
    public static function syncPhysicalRobotsTxt(): void
    {
        try {
            $content = self::generateRobotsTxt();
            foreach ([public_path('robots.txt'), base_path('robots.txt')] as $robotsPath) {
                $dir = dirname($robotsPath);
                if (is_writable($robotsPath) || is_writable($dir)) {
                    @file_put_contents($robotsPath, $content);
                }
            }
        } catch (\Throwable $e) {
            // Ignore filesystem write errors on read-only environments
        }
    }

    /**
     * Notify search engines (IndexNow / Bing / Yandex / Sitemap Ping) of updated non-sensitive public URLs.
     * Never throws exceptions and uses a short timeout so production requests never stall.
     */
    public static function pingSearchEngines(?array $specificUrls = null, bool $forceInAnyEnv = false): array
    {
        if (!self::isSectionIndexable('seo_auto_index_enabled')) {
            return [
                'status' => 'disabled',
                'url_count' => 0,
                'message' => 'Auto-indexing is currently disabled in SEO settings.',
            ];
        }

        $urlsToSubmit = [];
        if ($specificUrls !== null) {
            foreach ($specificUrls as $u) {
                if (!self::isSensitiveUrlOrPath($u)) {
                    $urlsToSubmit[] = $u;
                }
            }
        } else {
            foreach (self::getSitemapUrls() as $entry) {
                $urlsToSubmit[] = $entry['loc'];
            }
        }

        $urlsToSubmit = array_values(array_unique($urlsToSubmit));
        $count = count($urlsToSubmit);

        if ($count === 0) {
            return [
                'status' => 'skipped',
                'url_count' => 0,
                'message' => 'No indexable public URLs to submit.',
            ];
        }

        $host = parse_url(config('app.url', 'https://acsacademy.bd'), PHP_URL_HOST) ?: 'acsacademy.bd';
        $key = self::getIndexNowKey();
        $keyLocation = rtrim(config('app.url', 'https://acsacademy.bd'), '/') . '/' . $key . '.txt';

        $dispatched = false;
        if (($forceInAnyEnv || app()->environment('production')) && !in_array($host, ['localhost', '127.0.0.1'], true)) {
            try {
                Http::timeout(2)->acceptJson()->post('https://api.indexnow.org/indexnow', [
                    'host' => $host,
                    'key' => $key,
                    'keyLocation' => $keyLocation,
                    'urlList' => array_slice($urlsToSubmit, 0, 500),
                ]);
                $dispatched = true;
            } catch (\Throwable $e) {
                // Non-blocking: ignore network restrictions on shared hosting or sandbox
            }
        }

        try {
            Setting::updateOrCreate(
                ['key' => 'seo_last_ping_at'],
                ['value' => now()->toDateTimeString(), 'type' => 'text']
            );
            Setting::updateOrCreate(
                ['key' => 'seo_last_ping_count'],
                ['value' => (string) $count, 'type' => 'text']
            );
            \Illuminate\Support\Facades\Cache::forget('global_settings');
        } catch (\Throwable $e) {
        }

        return [
            'status' => 'success',
            'dispatched_remote' => $dispatched,
            'url_count' => $count,
            'indexnow_key' => $key,
            'sitemap_url' => url('/sitemap.xml'),
            'message' => "Prepared & synced {$count} non-sensitive public URLs for Search Console & IndexNow.",
        ];
    }
}

