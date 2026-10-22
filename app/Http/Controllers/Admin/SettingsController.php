<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        return view('admin.settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $data = $request->except(['_token', 'logo', 'has_registration_fields']);

        if ($request->has('has_registration_fields')) {
            $registrationFields = $request->input('registration_fields', []);
            $processedFields = [];
            
            foreach ($registrationFields as $field) {
                if (!empty($field['name']) && !empty($field['label'])) {
                    $processedFields[] = [
                        'name' => \Illuminate\Support\Str::slug($field['name'], '_'),
                        'label' => $field['label'],
                        'type' => $field['type'] ?? 'text',
                        'options' => $field['options'] ?? '',
                        'is_mandatory' => isset($field['is_mandatory']) && $field['is_mandatory'] === '1'
                    ];
                }
            }
            
            Setting::updateOrCreate(
                ['key' => 'registration_fields'],
                ['value' => json_encode($processedFields), 'type' => 'json']
            );
            
            unset($data['registration_fields']);
        }

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => is_array($value) ? json_encode($value) : $value]
            );
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $url = Storage::url($path);
            Setting::updateOrCreate(
                ['key' => 'site_logo'],
                ['value' => $url, 'type' => 'image']
            );
        } elseif ($request->filled('logo_base64')) {
            $data = $request->input('logo_base64');
            if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                $data = substr($data, strpos($data, ',') + 1);
                $type = strtolower($type[1]);
                $data = base64_decode($data);
                if ($data !== false) {
                    $filename = 'logo_' . time() . '.' . $type;
                    Storage::disk('public')->put('logos/' . $filename, $data);
                    Setting::updateOrCreate(
                        ['key' => 'site_logo'],
                        ['value' => Storage::url('logos/' . $filename), 'type' => 'image']
                    );
                }
            }
        }

        Cache::forget('global_settings');

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}
