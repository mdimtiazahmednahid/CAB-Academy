<?php

namespace App\Support;

use App\Models\BlogPost;
use App\Models\Course;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class LinkPreviewResolver
{
    /**
     * Extract the first valid http/https URL from a block of text.
     */
    public static function extractFirstUrl(?string $text): ?string
    {
        if (!$text || trim($text) === '') {
            return null;
        }

        if (preg_match('/https?:\/\/[^\s<>"\']+/i', $text, $matches)) {
            $url = rtrim($matches[0], ".,;:!?)]}'\"");
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                return $url;
            }
        }

        return null;
    }

    /**
     * Convert plain text into safe HTML with clickable anchor links.
     */
    public static function formatTextWithClickableLinks(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        $escaped = e($text);

        $linked = preg_replace_callback(
            '/https?:\/\/[^\s<>"\']+/i',
            function (array $matches) {
                $raw = $matches[0];
                $cleanUrl = rtrim(html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8'), ".,;:!?)]}'\"");
                $trailing = substr($raw, strlen(e($cleanUrl)));
                if (!filter_var($cleanUrl, FILTER_VALIDATE_URL)) {
                    return $raw;
                }
                $safeHref = e($cleanUrl);
                return '<a href="' . $safeHref . '" target="_blank" rel="noopener noreferrer" class="text-[#c30102] hover:text-[#051c45] underline font-semibold break-all transition-colors">' . $safeHref . '</a>' . $trailing;
            },
            $escaped
        );

        return nl2br($linked ?? $escaped);
    }

    /**
     * Resolve Open Graph metadata (with sensible fallbacks) for any URL.
     */
    public static function resolve(?string $rawUrl): ?array
    {
        if (!$rawUrl) {
            return null;
        }

        $url = trim($rawUrl);
        if (!preg_match('/^https?:\/\//i', $url)) {
            $url = 'https://' . ltrim($url, '/');
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $parsed = parse_url($url);
        $scheme = strtolower($parsed['scheme'] ?? 'https');
        if (!in_array($scheme, ['http', 'https'], true)) {
            return null;
        }

        $host = strtolower($parsed['host'] ?? '');
        if ($host === '') {
            return null;
        }

        $domain = strtoupper(preg_replace('/^www\./i', '', $host));

        // 1. Fast-path: Check if this URL points to our own platform (Course, Short Link, Blog, or Portal Page)
        $internalPreview = self::resolveInternalUrl($url, $host, $parsed['path'] ?? '/', $domain);
        if ($internalPreview !== null) {
            return $internalPreview;
        }

        // 2. External URL: Resolve via cached Open Graph scraper with sensible fallback
        $cacheKey = 'fb_link_preview_v1_' . md5($url);

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($url, $domain, $scheme, $host, $parsed) {
            return self::fetchExternalOpenGraph($url, $domain, $scheme, $host, $parsed['path'] ?? '/');
        });
    }

    /**
     * Resolve internal ACS Academy URLs directly from the database without network round-trips.
     */
    protected static function resolveInternalUrl(string $url, string $host, string $path, string $domain): ?array
    {
        $appHost = strtolower(parse_url(config('app.url', ''), PHP_URL_HOST) ?: '');
        $reqHost = request() ? strtolower(request()->getHost()) : '';
        $knownInternalHosts = array_filter([
            $appHost,
            $reqHost,
            'acsacademy.bd',
            'www.acsacademy.bd',
            'cabacademy.bd',
            'www.cabacademy.bd',
            '127.0.0.1',
            'localhost',
        ]);

        if (!in_array($host, $knownInternalHosts, true)) {
            return null;
        }

        $siteName = Setting::getVal('site_name', 'ACS Academy');
        $siteLogo = Setting::getVal('site_logo') ?: asset('images/logo.png');
        $siteLogoUrl = (str_starts_with($siteLogo, 'http://') || str_starts_with($siteLogo, 'https://')) ? $siteLogo : url($siteLogo);
        $cleanPath = '/' . ltrim($path, '/');

        // Match /courses/{id} or /c/{id}
        if (preg_match('#^/(?:courses|c)/(\d+)\b#i', $cleanPath, $m)) {
            $course = Course::find((int) $m[1]);
            if ($course) {
                $desc = Str::limit(
                    trim(strip_tags($course->short_description ?: $course->description ?: "Enroll in {$course->title} at {$siteName}. Professional certification and practical industry training.")),
                    180
                );
                $image = $course->cover_image_url ?: $course->og_image_url ?: $siteLogoUrl;
                $displayDomain = strtolower(preg_replace('/^www\./i', '', $host));
                if (in_array($displayDomain, ['localhost', '127.0.0.1'], true)) {
                    $displayDomain = strtolower(preg_replace('/^www\./i', '', parse_url(config('app.url'), PHP_URL_HOST) ?: 'acsacademy.bd'));
                }
                return [
                    'url' => route('courses.show', $course),
                    'canonical_url' => route('courses.show', $course),
                    'short_url' => $course->short_url,
                    'domain' => $domain,
                    'display_domain' => $displayDomain,
                    'site_name' => $siteName,
                    'title' => $course->social_card_headline,
                    'stylish_title' => $course->stylish_og_title,
                    'post_header' => $course->stylish_post_header,
                    'social_caption' => $course->stylish_social_caption,
                    'description' => $desc,
                    'image' => $image,
                    'has_image' => !empty($image),
                    'type' => 'course',
                    'badge' => null,
                ];
            }
        }

        // Match /blog/{slug}
        if (preg_match('#^/blog/([a-zA-Z0-9\-_]+)#i', $cleanPath, $m)) {
            $blog = BlogPost::where('slug', $m[1])->first();
            if ($blog) {
                $desc = Str::limit(
                    trim(strip_tags($blog->excerpt ?: $blog->content ?: "Read {$blog->title} on {$siteName}.")),
                    180
                );
                $image = $blog->featured_image ? storage_asset($blog->featured_image, $siteLogoUrl) : $siteLogoUrl;
                $displayDomain = strtolower(preg_replace('/^www\./i', '', $host));
                return [
                    'url' => $url,
                    'canonical_url' => route('blog.show', $blog->slug),
                    'domain' => $domain,
                    'display_domain' => $displayDomain,
                    'site_name' => $siteName,
                    'title' => $blog->title . ' - ' . $siteName,
                    'stylish_title' => $blog->stylish_og_title,
                    'post_header' => $blog->stylish_post_header,
                    'description' => $desc,
                    'image' => $image,
                    'has_image' => !empty($image),
                    'badge' => $blog->category ?? 'Article',
                ];
            }
        }

        // Default internal platform page preview
        $tagline = Setting::getVal('site_tagline', 'Learn, Grow, Lead.');
        $defaultDesc = Setting::getVal(
            'meta_description',
            "{$siteName} offers executive certifications and practical training in Commercial Banking, Export-Import, Supply Chain, VAT & Tax, and Leadership."
        );

        $pageTitle = match (true) {
            str_starts_with($cleanPath, '/catalog') || str_starts_with($cleanPath, '/courses') => "Course Catalog — {$siteName}",
            str_starts_with($cleanPath, '/jobs') || str_starts_with($cleanPath, '/careers') => "Career Portal & Job Board — {$siteName}",
            str_starts_with($cleanPath, '/verify-certificate') => "Official Certificate Verification Portal — {$siteName}",
            str_starts_with($cleanPath, '/about') => "About {$siteName} — {$tagline}",
            str_starts_with($cleanPath, '/contact') => "Contact & Admissions Support — {$siteName}",
            default => "{$siteName} | {$tagline}",
        };

        return [
            'url' => $url,
            'canonical_url' => $url,
            'domain' => $domain,
            'display_domain' => strtolower($domain),
            'site_name' => $siteName,
            'title' => $pageTitle,
            'stylish_title' => StylishTextFormatter::toBoldSerif($pageTitle),
            'post_header' => StylishTextFormatter::toUpperBoldSerif($pageTitle),
            'description' => Str::limit(strip_tags($defaultDesc), 180),
            'image' => $siteLogoUrl,
            'has_image' => !empty($siteLogoUrl),
            'badge' => null,
        ];
    }

    /**
     * Fetch and parse Open Graph metadata from an external URL with SSRF protection & sensible fallbacks.
     */
    protected static function fetchExternalOpenGraph(string $url, string $domain, string $scheme, string $host, string $path): array
    {
        $fallback = self::buildFallbackPreview($url, $domain, $path);

        // SSRF Guard: Do not make outbound HTTP requests to private/loopback IPs
        if (self::isPrivateOrReservedHost($host)) {
            return $fallback;
        }

        try {
            $response = Http::timeout(4)
                ->connectTimeout(2)
                ->maxRedirects(3)
                ->withHeaders([
                    'User-Agent' => 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php) ACSAcademyBot/1.0',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language' => 'en-US,en;q=0.9',
                ])
                ->get($url);

            if (!$response->successful()) {
                return $fallback;
            }

            $html = substr((string) $response->body(), 0, 160000);
            if (trim($html) === '') {
                return $fallback;
            }

            return self::parseHtmlMetadata($html, $url, $domain, $scheme . '://' . $host, $fallback);
        } catch (\Throwable $e) {
            return $fallback;
        }
    }

    /**
     * Parse Open Graph, Twitter Card, and HTML fallback tags from raw HTML string.
     */
    public static function parseHtmlMetadata(string $html, string $url, ?string $domain = null, ?string $baseOrigin = null, ?array $fallback = null): array
    {
        $parsed = parse_url($url);
        $host = strtolower($parsed['host'] ?? 'website.com');
        $domain = $domain ?: strtoupper(preg_replace('/^www\./i', '', $host));
        $baseOrigin = $baseOrigin ?: (($parsed['scheme'] ?? 'https') . '://' . $host);
        $fallback = $fallback ?: self::buildFallbackPreview($url, $domain, $parsed['path'] ?? '/');

        $meta = [];

        // Extract all <meta> tags
        if (preg_match_all('/<meta\b[^>]*>/i', $html, $metaTags)) {
            foreach ($metaTags[0] as $tag) {
                $key = null;
                if (preg_match('/\b(?:property|name|itemprop)\s*=\s*["\']([^"\']+)["\']/i', $tag, $km)) {
                    $key = strtolower(trim($km[1]));
                }
                $content = null;
                if (preg_match('/\bcontent\s*=\s*["\']([^"\']*)["\']/i', $tag, $cm)) {
                    $content = html_entity_decode(trim($cm[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                }
                if ($key && $content !== null && $content !== '' && !isset($meta[$key])) {
                    $meta[$key] = $content;
                }
            }
        }

        // Resolve <title> tag fallback
        $docTitle = null;
        if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $tm)) {
            $docTitle = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($tm[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        }

        // Resolve first <h1> fallback
        $h1Title = null;
        if (!$docTitle && preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $html, $hm)) {
            $h1Title = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($hm[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        }

        // Resolve first meaningful <p> paragraph fallback for description
        $firstParagraph = null;
        if (preg_match_all('/<p[^>]*>(.*?)<\/p>/is', $html, $pm)) {
            foreach ($pm[1] as $pText) {
                $cleanP = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($pText), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
                if (strlen($cleanP) >= 35) {
                    $firstParagraph = $cleanP;
                    break;
                }
            }
        }

        $title = $meta['og:title']
            ?? $meta['twitter:title']
            ?? $docTitle
            ?? $h1Title
            ?? $fallback['title'];

        $description = $meta['og:description']
            ?? $meta['twitter:description']
            ?? $meta['description']
            ?? $firstParagraph
            ?? $fallback['description'];

        $rawImage = $meta['og:image']
            ?? $meta['og:image:secure_url']
            ?? $meta['twitter:image']
            ?? $meta['twitter:image:src']
            ?? null;

        // Fallback to apple-touch-icon if no og:image
        if (!$rawImage && preg_match('/<link\b[^>]*rel\s*=\s*["\'](?:apple-touch-icon|image_src)["\'][^>]*href\s*=\s*["\']([^"\']+)["\']/i', $html, $lm)) {
            $rawImage = html_entity_decode(trim($lm[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        $resolvedImage = self::resolveImageUrl($rawImage, $baseOrigin);

        $canonicalUrl = $meta['og:url'] ?? $url;
        if ($canonicalUrl && !preg_match('/^https?:\/\//i', $canonicalUrl)) {
            $canonicalUrl = rtrim($baseOrigin, '/') . '/' . ltrim($canonicalUrl, '/');
        }

        $siteName = $meta['og:site_name'] ?? $domain;

        $cleanTitle = Str::limit(trim($title), 120);

        return [
            'url' => $canonicalUrl ?: $url,
            'canonical_url' => $canonicalUrl ?: $url,
            'domain' => $domain,
            'display_domain' => strtolower($domain),
            'site_name' => $siteName,
            'title' => $cleanTitle,
            'stylish_title' => StylishTextFormatter::toBoldSerif($cleanTitle),
            'post_header' => StylishTextFormatter::toUpperBoldSerif($cleanTitle),
            'description' => Str::limit(trim($description), 200),
            'image' => $resolvedImage,
            'has_image' => !empty($resolvedImage),
            'badge' => null,
        ];
    }

    /**
     * Sensible fallback preview when a URL has no Open Graph metadata or is unreachable.
     */
    public static function buildFallbackPreview(string $url, string $domain, string $path = '/'): array
    {
        $cleanPath = trim($path, '/');
        $humanSlug = '';
        if ($cleanPath !== '') {
            $lastSegment = basename($cleanPath);
            $lastSegment = preg_replace('/\.[a-zA-Z0-9]{2,5}$/', '', $lastSegment);
            $humanSlug = ucwords(str_replace(['-', '_'], ' ', urldecode($lastSegment)));
        }

        $fallbackTitle = $humanSlug !== '' && strlen($humanSlug) > 2
            ? "{$humanSlug} — {$domain}"
            : $domain;

        return [
            'url' => $url,
            'canonical_url' => $url,
            'domain' => $domain,
            'display_domain' => strtolower($domain),
            'site_name' => $domain,
            'title' => $fallbackTitle,
            'stylish_title' => StylishTextFormatter::toBoldSerif($fallbackTitle),
            'post_header' => StylishTextFormatter::toUpperBoldSerif($fallbackTitle),
            'description' => "Click to open this resource on {$domain} ({$url}).",
            'image' => null,
            'has_image' => false,
            'badge' => null,
        ];
    }

    /**
     * Resolve relative or protocol-relative image URLs to absolute URLs.
     */
    protected static function resolveImageUrl(?string $rawImage, string $baseOrigin): ?string
    {
        if (!$rawImage || trim($rawImage) === '') {
            return null;
        }

        $img = trim($rawImage);
        if (str_starts_with($img, '//')) {
            return 'https:' . $img;
        }
        if (preg_match('/^https?:\/\//i', $img)) {
            return $img;
        }
        return rtrim($baseOrigin, '/') . '/' . ltrim($img, '/');
    }

    /**
     * Check whether a hostname resolves to a private, loopback, or reserved IP range.
     */
    protected static function isPrivateOrReservedHost(string $host): bool
    {
        $lower = strtolower($host);
        if (in_array($lower, ['localhost', '127.0.0.1', '0.0.0.0', '::1'], true)) {
            return true;
        }

        if (filter_var($lower, FILTER_VALIDATE_IP)) {
            return !filter_var($lower, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
        }

        return false;
    }
}
