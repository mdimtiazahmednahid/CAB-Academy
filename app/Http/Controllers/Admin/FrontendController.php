<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

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
        ]);

        $data = $request->except(['_token', 'landing_hero_media_image_file']);

        // Handle Hero Media Image File Upload if uploaded
        if ($request->hasFile('landing_hero_media_image_file')) {
            $path = $request->file('landing_hero_media_image_file')->store('frontend', 'public');
            $data['landing_hero_media_image'] = Storage::url($path);
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
            ];

            foreach ($toggleKeys as $toggleKey) {
                $val = $request->input($toggleKey);
                $data[$toggleKey] = ($val === '1' || $val === 1 || $val === true) ? '1' : '0';
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
