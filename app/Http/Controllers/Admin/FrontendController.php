<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FrontendController extends Controller
{
    public function index()
    {
        $settings = Setting::where('key', 'like', 'landing_%')->pluck('value', 'key')->toArray();
        return view('admin.frontend.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'landing_page_mode' => 'required|in:default,custom',
            'landing_custom_html' => 'nullable|string',
            
            'landing_hero_title' => 'nullable|string',
            'landing_hero_subtitle' => 'nullable|string',
            'landing_cta_text' => 'nullable|string',
            
            // Feature 1
            'landing_feature_1_title' => 'nullable|string',
            'landing_feature_1_desc' => 'nullable|string',
            
            // Feature 2
            'landing_feature_2_title' => 'nullable|string',
            'landing_feature_2_desc' => 'nullable|string',
            
            // Feature 3
            'landing_feature_3_title' => 'nullable|string',
            'landing_feature_3_desc' => 'nullable|string',
        ]);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => 'text', 'group' => 'frontend']
            );
        }

        Cache::forget('global_settings');

        return back()->with('success', 'Landing page settings updated successfully!');
    }
}
