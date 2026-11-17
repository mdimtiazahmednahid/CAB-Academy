<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\GoogleDriveBackupClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index(GoogleDriveBackupClient $drive)
    {
        $settings = Setting::all()->keyBy('key');
        $rawHistory = isset($settings['google_drive_backup_history'])
            ? json_decode((string) $settings['google_drive_backup_history']->value, true)
            : [];
        $backupHistory = is_array($rawHistory) ? $rawHistory : [];
        $backupConfigured = $drive->isConfigured();
        $backupEnabled = (bool) config('backup.enabled', true);
        $folderId = (string) config('backup.google_drive.folder_id', '');
        $backupFolderUrl = $folderId !== '' ? 'https://drive.google.com/drive/folders/' . urlencode($folderId) : null;

        return view('admin.settings.index', compact(
            'settings',
            'backupHistory',
            'backupConfigured',
            'backupEnabled',
            'backupFolderUrl'
        ));
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

        foreach (['google_site_verification', 'bing_site_verification', 'yandex_site_verification'] as $verifyKey) {
            if (array_key_exists($verifyKey, $data)) {
                $data[$verifyKey] = \App\Support\SeoIndexingManager::extractVerificationCode((string) $data[$verifyKey]);
            }
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
        \App\Support\SeoIndexingManager::syncPhysicalRobotsTxt();
        \App\Support\SeoIndexingManager::pingSearchEngines();

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    public function pingSeo(Request $request)
    {
        Cache::forget('global_settings');
        \App\Support\SeoIndexingManager::syncPhysicalRobotsTxt();
        $result = \App\Support\SeoIndexingManager::pingSearchEngines(null, true);

        return redirect()->back()->with(
            'success',
            ($result['message'] ?? 'SEO Sitemap & IndexNow synchronized.') . ' Strictly non-sensitive public pages only.'
        );
    }

    public function runBackupNow(Request $request)
    {
        $exitCode = Artisan::call('backup:google-drive');
        $output = trim(Artisan::output());

        if ($exitCode !== 0) {
            return redirect()->back()->with('error', $output !== '' ? $output : 'Google Drive backup failed. Check logs for details.');
        }

        return redirect()->back()->with('success', $output !== '' ? $output : 'Database backup uploaded to Google Drive successfully.');
    }

    public function sendTestEmail(Request $request)
    {
        $validated = $request->validate([
            'test_email' => 'required|email|max:255',
        ]);

        \App\Support\MailConfigManager::applyRuntimeMailConfig();

        try {
            $siteName = Setting::getVal('site_name', 'ACS Academy');
            \Illuminate\Support\Facades\Mail::raw(
                "This is a test email from {$siteName}. Your SMTP / mail configuration is working properly.",
                function ($message) use ($validated, $siteName) {
                    $message->to($validated['test_email'])
                        ->subject("SMTP Test Email — {$siteName}");
                }
            );

            return redirect()->back()->with('success', "Test email dispatched to {$validated['test_email']} (Mailer: " . config('mail.default') . ").");
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed to send test email: ' . $e->getMessage());
        }
    }
}
