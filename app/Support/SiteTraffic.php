<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class SiteTraffic
{
    private const KEYS = [
        'site_total_views',
        'site_unique_visitors',
        'site_today_views',
        'site_today_date',
    ];

    /**
     * Record a public HTML page view and unique visitor safely (once per real 200 HTML response).
     */
    public static function recordVisit(Request $request, ?Response $response = null): void
    {
        try {
            if (!$request->isMethod('GET') || $request->ajax() || $request->expectsJson()) {
                return;
            }

            if ($request->user() && method_exists($request->user(), 'isAdmin') && $request->user()->isAdmin()) {
                return;
            }

            if ($response !== null) {
                if ($response->getStatusCode() !== 200) {
                    return;
                }
                $contentType = strtolower((string) $response->headers->get('Content-Type', ''));
                if ($contentType !== '' && !str_contains($contentType, 'text/html')) {
                    return;
                }
            }

            if (
                strtolower((string) $request->header('Purpose')) === 'prefetch' ||
                str_contains(strtolower((string) $request->header('Sec-Purpose')), 'prefetch')
            ) {
                return;
            }

            if (
                $request->is('admin') ||
                $request->is('admin/*') ||
                $request->is('api/*') ||
                $request->is('up') ||
                $request->is('cmd.php') ||
                $request->is('sw.js') ||
                $request->is('manifest.json') ||
                $request->is('favicon.ico') ||
                $request->is('storage/*') ||
                $request->is('uploads/*') ||
                $request->is('build/*') ||
                $request->is('images/*') ||
                $request->is('courses/*/og-image.jpg') ||
                $request->is('sitemap.xml') ||
                $request->is('robots.txt')
            ) {
                return;
            }

            $ua = strtolower((string) $request->userAgent());
            if ($ua !== '' && preg_match('/bot|crawl|spider|slurp|facebookexternalhit|facebot|whatsapp|telegrambot|linkedinbot|twitterbot|curl|wget|headless/i', $ua)) {
                return;
            }

            if (!Schema::hasTable('settings')) {
                return;
            }

            $today = now()->toDateString();

            // Multi-layer unique visitor check: cookie + session + atomic daily IP/UA fingerprint
            $hasCookie = $request->cookies->has('acs_visitor_counted');
            $hasSession = $request->hasSession() && (bool) $request->session()->get('acs_visitor_counted', false);
            $fingerprintKey = 'acs_vis_' . sha1(($request->ip() ?: '0.0.0.0') . '|' . $ua . '|' . $today);
            $isFreshFingerprint = Cache::add($fingerprintKey, 1, now()->endOfDay());

            $isNewVisitor = !$hasCookie && !$hasSession && $isFreshFingerprint;

            if ($request->hasSession() && !$hasSession) {
                $request->session()->put('acs_visitor_counted', true);
            }
            if (!$hasCookie) {
                Cookie::queue(Cookie::make('acs_visitor_counted', '1', 60 * 24 * 30));
            }

            $existing = DB::table('settings')
                ->whereIn('key', self::KEYS)
                ->pluck('value', 'key')
                ->toArray();

            $totalViews = (int) ($existing['site_total_views'] ?? 0) + 1;
            $uniqueVisitors = (int) ($existing['site_unique_visitors'] ?? 0) + ($isNewVisitor ? 1 : 0);
            if ($uniqueVisitors < 1) {
                $uniqueVisitors = 1;
            }

            $storedDate = (string) ($existing['site_today_date'] ?? '');
            $todayViews = ($storedDate === $today)
                ? ((int) ($existing['site_today_views'] ?? 0) + 1)
                : 1;

            self::upsertCounter('site_total_views', (string) $totalViews);
            self::upsertCounter('site_unique_visitors', (string) $uniqueVisitors);
            self::upsertCounter('site_today_views', (string) $todayViews);
            if ($storedDate !== $today) {
                self::upsertCounter('site_today_date', $today);
            }
        } catch (\Throwable $e) {
            // Never disrupt page rendering if traffic tracking encounters a transient DB issue
        }
    }

    /**
     * Retrieve current traffic counters safely.
     */
    public static function stats(): array
    {
        try {
            if (!Schema::hasTable('settings')) {
                return ['total_views' => 0, 'unique_visitors' => 0, 'today_views' => 0];
            }

            $rows = DB::table('settings')
                ->whereIn('key', self::KEYS)
                ->pluck('value', 'key')
                ->toArray();

            $today = now()->toDateString();
            $todayViews = (($rows['site_today_date'] ?? '') === $today)
                ? (int) ($rows['site_today_views'] ?? 0)
                : 0;

            return [
                'total_views' => (int) ($rows['site_total_views'] ?? 0),
                'unique_visitors' => (int) ($rows['site_unique_visitors'] ?? 0),
                'today_views' => $todayViews,
            ];
        } catch (\Throwable $e) {
            return ['total_views' => 0, 'unique_visitors' => 0, 'today_views' => 0];
        }
    }

    private static function upsertCounter(string $key, string $value): void
    {
        $now = now();
        $exists = DB::table('settings')->where('key', $key)->exists();
        if ($exists) {
            DB::table('settings')->where('key', $key)->update([
                'value' => $value,
                'updated_at' => $now,
            ]);
        } else {
            DB::table('settings')->insert([
                'key' => $key,
                'value' => $value,
                'type' => 'text',
                'group' => 'analytics',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
