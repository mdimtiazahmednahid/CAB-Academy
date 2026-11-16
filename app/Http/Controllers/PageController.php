<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Serve dynamic PWA Web App Manifest (/manifest.json).
     */
    public function manifest()
    {
        $brand = Setting::getBrandSizing();
        $themeColor = Setting::getVal('primary_color', '#021f59');

        return response()->json([
            'name' => $brand['name'],
            'short_name' => $brand['short_name'],
            'description' => Setting::getVal('seo_description', 'Executive Professional Certification & Corporate Programs'),
            'start_url' => '/?source=pwa',
            'scope' => '/',
            'display' => 'standalone',
            'background_color' => '#01143d',
            'theme_color' => $themeColor,
            'orientation' => 'portrait-primary',
            'icons' => [
                [
                    'src' => asset('images/icons/icon-192x192.png'),
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'any',
                ],
                [
                    'src' => asset('images/icons/icon-512x512.png'),
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'any',
                ],
                [
                    'src' => asset('images/icons/icon-maskable-512x512.png'),
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'maskable',
                ],
            ],
            'shortcuts' => [
                [
                    'name' => 'Browse Courses',
                    'short_name' => 'Courses',
                    'url' => '/catalog',
                    'icons' => [['src' => asset('images/icons/icon-192x192.png'), 'sizes' => '192x192']],
                ],
                [
                    'name' => 'Student Portal',
                    'short_name' => 'Portal',
                    'url' => '/dashboard',
                    'icons' => [['src' => asset('images/icons/icon-192x192.png'), 'sizes' => '192x192']],
                ],
                [
                    'name' => 'Verify Certificate',
                    'short_name' => 'Verify',
                    'url' => '/verify-certificate',
                    'icons' => [['src' => asset('images/icons/icon-192x192.png'), 'sizes' => '192x192']],
                ],
            ],
        ])->header('Content-Type', 'application/manifest+json');
    }

    /**
     * Render a custom CMS page (/page/{slug}).
     */
    public function show(string $slug)
    {
        $cleanSlug = trim(strtolower($slug), '/');
        $pages = Setting::getCustomPages(false);

        $matchedPage = null;
        foreach ($pages as $page) {
            if (isset($page['slug']) && strtolower(trim($page['slug'], '/')) === $cleanSlug) {
                $matchedPage = $page;
                break;
            }
        }

        if (!$matchedPage) {
            abort(404);
        }

        $isPublished = !empty($matchedPage['is_published']);
        $isAdminPreview = auth()->check() && auth()->user()->isAdmin();

        if (!$isPublished && !$isAdminPreview) {
            abort(404);
        }

        return view('pages.show', [
            'page' => $matchedPage,
            'isPreview' => !$isPublished && $isAdminPreview,
        ]);
    }
}

