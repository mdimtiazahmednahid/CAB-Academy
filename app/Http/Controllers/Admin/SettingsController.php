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
        $data = $request->except(['_token', 'logo', 'logo_base64', 'has_registration_fields']);

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
            $file = $request->file('logo');
            $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            
            $dirs = [
                public_path('uploads/logos'),
                public_path('storage/logos'),
                storage_path('app/public/logos'),
            ];
            foreach ($dirs as $dir) {
                if (!is_dir($dir)) {
                    @mkdir($dir, 0775, true);
                }
            }

            $destPath = public_path('uploads/logos/' . $filename);
            $file->move(public_path('uploads/logos'), $filename);

            @copy($destPath, public_path('storage/logos/' . $filename));
            @copy($destPath, storage_path('app/public/logos/' . $filename));

            Setting::updateOrCreate(
                ['key' => 'site_logo'],
                ['value' => 'uploads/logos/' . $filename, 'type' => 'image']
            );
        } elseif ($request->filled('logo_base64')) {
            $base64Data = $request->input('logo_base64');
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
                $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
                $type = strtolower($type[1]);
                $binaryData = base64_decode($base64Data);
                if ($binaryData !== false) {
                    $filename = 'logo_' . time() . '.' . $type;
                    
                    $dirs = [
                        public_path('uploads/logos'),
                        public_path('storage/logos'),
                        storage_path('app/public/logos'),
                    ];
                    foreach ($dirs as $dir) {
                        if (!is_dir($dir)) {
                            @mkdir($dir, 0775, true);
                        }
                    }

                    $destPath = public_path('uploads/logos/' . $filename);
                    @file_put_contents($destPath, $binaryData);
                    @file_put_contents(public_path('storage/logos/' . $filename), $binaryData);
                    @file_put_contents(storage_path('app/public/logos/' . $filename), $binaryData);

                    Setting::updateOrCreate(
                        ['key' => 'site_logo'],
                        ['value' => 'uploads/logos/' . $filename, 'type' => 'image']
                    );
                }
            }
        }

        Cache::forget('global_settings');

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}
