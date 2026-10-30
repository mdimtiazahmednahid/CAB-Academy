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

    /**
     * Compute adaptive brand typography styles so short names ("CAB Academy", "ACS Academy")
     * and long names ("Academy of Commercial and Supply Chain") auto-fit every screen cleanly.
     */
    public static function getBrandSizing(?string $customText = null): array
    {
        $siteName = trim((string) ($customText ?? self::getVal('site_name', 'ACS Academy')));
        $len = mb_strlen($siteName);
        $mode = self::getVal('brand_name_size_mode', 'auto');

        $explicitShort = trim((string) self::getVal('site_short_name', ''));
        if ($explicitShort !== '') {
            $shortName = $explicitShort;
        } elseif ($len <= 15) {
            $shortName = $siteName;
        } else {
            $words = preg_split('/\s+/', $siteName) ?: [];
            $stopWords = ['of', 'and', 'the', 'for', 'in', '&'];
            $acronym = '';
            foreach ($words as $w) {
                $clean = preg_replace('/[^a-zA-Z]/', '', $w);
                if ($clean !== '' && !in_array(strtolower($clean), $stopWords, true)) {
                    $acronym .= strtoupper(substr($clean, 0, 1));
                }
            }
            $shortName = (strlen($acronym) >= 2 ? substr($acronym, 0, 5) . ' Academy' : mb_substr($siteName, 0, 14));
        }

        $isLong = $len > 22;

        if (in_array($mode, ['sm', 'compact'], true)) {
            return [
                'name' => $siteName,
                'site_name' => $siteName,
                'short_name' => $shortName,
                'is_long' => $isLong,
                'header_style' => 'font-size: clamp(0.75rem, 0.95vw, 0.92rem); line-height: 1.18;',
                'sidebar_style' => 'font-size: clamp(0.70rem, 0.85vw, 0.80rem); line-height: 1.2;',
                'hero_style' => 'font-size: clamp(1.65rem, 3.2vw, 2.6rem); line-height: 1.12;',
            ];
        }

        if (in_array($mode, ['lg', 'large'], true)) {
            return [
                'name' => $siteName,
                'site_name' => $siteName,
                'short_name' => $shortName,
                'is_long' => $isLong,
                'header_style' => 'font-size: clamp(1.0rem, 1.5vw, 1.35rem); line-height: 1.12;',
                'sidebar_style' => 'font-size: clamp(0.88rem, 1.15vw, 1.0rem); line-height: 1.18;',
                'hero_style' => 'font-size: clamp(2.25rem, 5vw, 4rem); line-height: 1.06;',
            ];
        }

        // Auto-responsive calculation based on character count
        if ($len <= 14) {
            return [
                'name' => $siteName,
                'site_name' => $siteName,
                'short_name' => $shortName,
                'is_long' => false,
                'header_style' => 'font-size: clamp(0.95rem, 1.35vw, 1.25rem); line-height: 1.1;',
                'sidebar_style' => 'font-size: clamp(0.82rem, 1.05vw, 0.92rem); line-height: 1.15;',
                'hero_style' => 'font-size: clamp(2.15rem, 4.8vw, 3.75rem); line-height: 1.08;',
            ];
        }

        if ($len <= 26) {
            return [
                'name' => $siteName,
                'site_name' => $siteName,
                'short_name' => $shortName,
                'is_long' => false,
                'header_style' => 'font-size: clamp(0.82rem, 1.12vw, 1.04rem); line-height: 1.15;',
                'sidebar_style' => 'font-size: clamp(0.75rem, 0.95vw, 0.84rem); line-height: 1.2;',
                'hero_style' => 'font-size: clamp(1.85rem, 3.9vw, 3.1rem); line-height: 1.12;',
            ];
        }

        return [
            'name' => $siteName,
            'site_name' => $siteName,
            'short_name' => $shortName,
            'is_long' => true,
            'header_style' => 'font-size: clamp(0.72rem, 0.95vw, 0.92rem); line-height: 1.18;',
            'sidebar_style' => 'font-size: clamp(0.68rem, 0.85vw, 0.78rem); line-height: 1.22;',
            'hero_style' => 'font-size: clamp(1.55rem, 3.2vw, 2.55rem); line-height: 1.15;',
        ];
    }

    /**
     * Generate global fluid root font-size and responsive utility rules controlled by Admin.
     */
    public static function getTypographyCss(): string
    {
        $minFont = (int) self::getVal('ui_min_font_size', 15);
        if ($minFont < 12) {
            $minFont = 12;
        }
        if ($minFont > 20) {
            $minFont = 20;
        }

        $scaleMode = self::getVal('ui_font_scale', 'standard');
        $scaleOffset = match ($scaleMode) {
            'compact' => -1,
            'comfortable' => 1,
            'large' => 2,
            default => 0,
        };

        $effectiveMin = max(12, min(20, $minFont + $scaleOffset));
        $effectiveMax = $effectiveMin + 2;

        return "
            :root {
                --ui-min-font-size: {$effectiveMin}px;
                --ui-max-font-size: {$effectiveMax}px;
            }
            html {
                font-size: clamp({$effectiveMin}px, calc({$effectiveMin}px + 0.15vw), {$effectiveMax}px);
                -webkit-text-size-adjust: 100%;
                -webkit-tap-highlight-color: transparent;
                scroll-behavior: smooth;
            }
            .brand-adaptive-title {
                word-break: break-word;
                overflow-wrap: anywhere;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            @supports (padding-bottom: env(safe-area-inset-bottom)) {
                .pb-safe {
                    padding-bottom: max(0.5rem, env(safe-area-inset-bottom));
                }
            }
        ";
    }

    /**
     * Check if a built-in public page/module is enabled.
     */
    public static function isPageEnabled(string $pageKey): bool
    {
        return (string) self::getVal("page_{$pageKey}_enabled", '1') === '1';
    }

    /**
     * Retrieve custom standalone pages created via the CMS.
     */
    public static function getCustomPages(bool $onlyPublished = false): array
    {
        $raw = self::getVal('cms_custom_pages', '[]');
        $pages = is_string($raw) ? json_decode($raw, true) : (is_array($raw) ? $raw : []);
        if (!is_array($pages)) {
            return [];
        }
        if ($onlyPublished) {
            return array_values(array_filter($pages, fn ($p) => !empty($p['is_published'])));
        }
        return array_values($pages);
    }

    /**
     * Retrieve custom homepage sections created via the CMS.
     */
    public static function getCustomSections(bool $onlyVisible = false): array
    {
        $raw = self::getVal('landing_custom_sections', '[]');
        $sections = is_string($raw) ? json_decode($raw, true) : (is_array($raw) ? $raw : []);
        if (!is_array($sections)) {
            return [];
        }
        if ($onlyVisible) {
            return array_values(array_filter($sections, fn ($s) => !empty($s['is_visible'])));
        }
        return array_values($sections);
    }
}
