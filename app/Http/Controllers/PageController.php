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
        $logoUrl = Setting::getVal('site_logo') ?: asset('images/logo.png');
        $themeColor = Setting::getVal('primary_color', '#051c45');

        return response()->json([
            'name' => $brand['name'],
            'short_name' => $brand['short_name'],
            'description' => Setting::getVal('seo_description', 'Executive Professional Certification & Corporate Programs'),
            'start_url' => '/?source=pwa',
            'scope' => '/',
            'display' => 'standalone',
            'background_color' => '#051c45',
            'theme_color' => $themeColor,
            'orientation' => 'portrait-primary',
            'icons' => [
                [
                    'src' => $logoUrl,
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'any maskable',
                ],
                [
                    'src' => $logoUrl,
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'any maskable',
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

