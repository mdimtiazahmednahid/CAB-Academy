<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class FrontendController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('admin.frontend.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'landing_page_mode' => 'nullable|in:default,custom',
            'landing_custom_html' => 'nullable|string',
            'landing_hero_media_image_file' => 'nullable|image|max:5120',
            'ui_min_font_size' => 'nullable|integer|min:12|max:20',
            'ui_font_scale' => 'nullable|in:compact,standard,comfortable,large',
            'brand_name_size_mode' => 'nullable|in:auto,sm,md,lg',
        ]);

        $data = $request->except([
            '_token',
            'landing_hero_media_image_file',
            'cms_custom_pages_json',
            'landing_custom_sections_json',
        ]);

        // Handle Hero Media Image File Upload if uploaded
        if ($request->hasFile('landing_hero_media_image_file')) {
            $path = $request->file('landing_hero_media_image_file')->store('frontend', 'public');
            $data['landing_hero_media_image'] = storage_asset($path);
        }

        // Handle Custom Pages JSON/Array if provided
        if ($request->filled('cms_custom_pages_json') || $request->has('cms_custom_pages')) {
            $rawPages = $request->filled('cms_custom_pages_json')
                ? json_decode($request->input('cms_custom_pages_json'), true)
                : $request->input('cms_custom_pages', []);

            $cleanPages = [];
            if (is_array($rawPages)) {
                foreach ($rawPages as $idx => $p) {
                    $title = trim((string) ($p['title'] ?? ''));
                    if ($title === '') {
                        continue;
                    }
                    $slug = Str::slug($p['slug'] ?? $title) ?: ('page-' . ($idx + 1));
                    $cleanPages[] = [
                        'id' => $p['id'] ?? ('pg_' . uniqid()),
                        'title' => $title,
                        'slug' => $slug,
                        'badge' => trim((string) ($p['badge'] ?? '')),
                        'subtitle' => trim((string) ($p['subtitle'] ?? '')),
                        'content' => (string) ($p['content'] ?? ''),
                        'cta_text' => trim((string) ($p['cta_text'] ?? '')),
                        'cta_url' => trim((string) ($p['cta_url'] ?? '')),
                        'meta_description' => trim((string) ($p['meta_description'] ?? '')),
                        'is_published' => filter_var($p['is_published'] ?? true, FILTER_VALIDATE_BOOLEAN),
                        'show_in_header' => filter_var($p['show_in_header'] ?? false, FILTER_VALIDATE_BOOLEAN),
                        'show_in_footer' => filter_var($p['show_in_footer'] ?? true, FILTER_VALIDATE_BOOLEAN),
                    ];
                }
            }
            $data['cms_custom_pages'] = json_encode($cleanPages);
        }

        // Handle Custom Homepage Sections JSON/Array if provided
        if ($request->filled('landing_custom_sections_json') || $request->has('landing_custom_sections')) {
            $rawSections = $request->filled('landing_custom_sections_json')
                ? json_decode($request->input('landing_custom_sections_json'), true)
                : $request->input('landing_custom_sections', []);

            $cleanSections = [];
            if (is_array($rawSections)) {
                foreach ($rawSections as $idx => $s) {
                    $title = trim((string) ($s['title'] ?? ''));
                    if ($title === '' && empty($s['content'])) {
                        continue;
                    }
                    $cleanSections[] = [
                        'id' => $s['id'] ?? ('sec_' . uniqid()),
                        'badge' => trim((string) ($s['badge'] ?? '')),
                        'title' => $title ?: ('Section ' . ($idx + 1)),
                        'subtitle' => trim((string) ($s['subtitle'] ?? '')),
                        'content' => (string) ($s['content'] ?? ''),
                        'theme' => in_array($s['theme'] ?? 'light', ['light', 'white', 'navy'], true) ? $s['theme'] : 'light',
                        'cta_text' => trim((string) ($s['cta_text'] ?? '')),
                        'cta_url' => trim((string) ($s['cta_url'] ?? '')),
                        'is_visible' => filter_var($s['is_visible'] ?? true, FILTER_VALIDATE_BOOLEAN),
                    ];
                }
            }
            $data['landing_custom_sections'] = json_encode($cleanSections);
        }

        // When submitting the full CMS form, ensure unchecked checkboxes default to '0'
        if ($request->has('landing_cms_form_submitted')) {
            $toggleKeys = [
                'landing_hero_enabled',
                'landing_clients_enabled',
                'landing_categories_enabled',
                'landing_courses_enabled',
                'landing_blog_enabled',
                'landing_counseling_enabled',
                'landing_faculty_enabled',
                'landing_features_enabled',
                'landing_jobs_enabled',
                'page_jobs_enabled',
                'page_blog_enabled',
                'page_feed_enabled',
                'page_instructors_enabled',
                'page_about_enabled',
                'page_contact_enabled',
                'page_verify_certificate_enabled',
            ];

            foreach ($toggleKeys as $toggleKey) {
                if ($request->has($toggleKey)) {
                    $val = $request->input($toggleKey);
                    $data[$toggleKey] = ($val === '1' || $val === 1 || $val === true || $val === 'true') ? '1' : '0';
                }
            }
        }

        // Backward compatibility sync for legacy fields
        if (isset($data['landing_hero_cta1_text'])) {
            $data['landing_cta_text'] = $data['landing_hero_cta1_text'];
        }

        // Save each setting
        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => is_array($value) ? json_encode($value) : $value,
                    'type' => 'text',
                    'group' => 'frontend'
                ]
            );
        }

        Cache::forget('global_settings');

        return back()->with('success', 'Landing page sections updated successfully!');
    }
}
