<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group'];

    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('global_settings');
        });
        static::deleted(function () {
            Cache::forget('global_settings');
        });
    }

    /**
     * Get a setting value, preferring cached version.
     */
    public static function getVal($key, $default = null)
    {
        $settings = Cache::rememberForever('global_settings', function () {
            return self::pluck('value', 'key')->toArray();
        });

        $val = $settings[$key] ?? $default;

        if ($key === 'site_logo') {
            return self::resolveLogoUrl($val, $default);
        }

        return $val;
    }

    /**
     * Resolves site_logo to a bulletproof URL regardless of past hardcoded hosts,
     * missing protocols, or environment migrations.
     */
    public static function resolveLogoUrl($val, $default = null): string
    {
        $defaultLogo = $default ?: asset('images/logo.png');

        if (empty($val)) {
            return $defaultLogo;
        }

        // If it's an external third-party URL (e.g. Unsplash or Cloudinary)
        if (str_starts_with($val, 'http://') || str_starts_with($val, 'https://')) {
            $host = parse_url($val, PHP_URL_HOST);
            // If it's a third party CDN domain
            if ($host && !str_contains($host, '127.0.0.1') && !str_contains($host, 'localhost') && !str_contains($host, 'infinityfree') && !str_contains($host, 'cabacademy')) {
                return $val;
            }
        }

        // Extract relative path from any full URL
        $path = parse_url($val, PHP_URL_PATH);
        if ($path) {
            $path = ltrim($path, '/');
        } else {
            $path = ltrim($val, '/');
        }

        // Check if the file physically exists in any public or storage locations
        if (file_exists(public_path($path))) {
            return asset($path);
        }
        if (file_exists(public_path('uploads/' . $path))) {
            return asset('uploads/' . $path);
        }
        if (file_exists(public_path('storage/' . $path))) {
            return asset('storage/' . $path);
        }
        if (file_exists(storage_path('app/public/' . $path))) {
            return asset('storage/' . $path);
        }

        // Check if just the filename exists in uploads/logos or storage/logos
        $filename = basename($path);
        if ($filename) {
            if (file_exists(public_path('uploads/logos/' . $filename))) {
                return asset('uploads/logos/' . $filename);
            }
            if (file_exists(public_path('storage/logos/' . $filename))) {
                return asset('storage/logos/' . $filename);
            }
        }

        // If $path is already a valid image path like uploads/... or storage/... or images/...
        // Return asset($path) so we do not discard user uploaded logo on shared hosts where file_exists() might fail
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'svg', 'gif', 'ico'])) {
            return asset($path);
        }

        // If physically not found and not a valid image path, fall back safely to official default logo
        return $defaultLogo;
    }

    /**
     * Set a setting value and clear cache.
     */
    public static function setVal($key, $value, $type = 'text', $group = 'general')
    {
        $setting = self::updateOrCreate(['key' => $key], [
            'value' => $value,
            'type' => $type,
            'group' => $group,
        ]);
        Cache::forget('global_settings');
        return $setting;
    }
}
