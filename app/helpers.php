<?php

if (!function_exists('storage_asset')) {
    /**
     * Generate a fully-qualified absolute URL for an uploaded storage asset,
     * ensuring proper /uploads/ or /storage/ resolution across shared hosting and social media crawlers.
     */
    function storage_asset(?string $path, ?string $fallback = null): ?string
    {
        if (empty($path)) {
            if (empty($fallback)) {
                return null;
            }
            return (str_starts_with($fallback, 'http://') || str_starts_with($fallback, 'https://') || str_starts_with($fallback, 'data:'))
                ? $fallback
                : url($fallback);
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, 'data:')) {
            return $path;
        }

        $trimmed = ltrim($path, '/');

        if (str_starts_with($trimmed, 'uploads/')) {
            return '/' . $trimmed;
        }

        if (str_starts_with($trimmed, 'storage/')) {
            return '/' . $trimmed;
        }

        // Mirror file from public/uploads to public/storage so static Apache requests to /storage/... always succeed without symlinks
        $uploadPath = public_path('uploads/' . $trimmed);
        $storagePath = public_path('storage/' . $trimmed);
        if (file_exists($uploadPath) && !is_link(public_path('storage'))) {
            if (!file_exists($storagePath) || @filesize($uploadPath) !== @filesize($storagePath)) {
                @mkdir(dirname($storagePath), 0775, true);
                @copy($uploadPath, $storagePath);
            }
        }

        return '/storage/' . $trimmed;
    }
}

if (!function_exists('acs_inline_compiled_css')) {
    /**
     * Inline the compiled production Tailwind CSS from public/build/manifest.json directly in <head>
     * as a zero-latency fallback so mobile browsers and Facebook in-app WebViews on InfinityFree
     * never render unstyled HTML even if secondary /build/assets/*.css requests are throttled or cached.
     */
    function acs_inline_compiled_css(): string
    {
        static $cachedInlineCss = null;
        if ($cachedInlineCss !== null) {
            return $cachedInlineCss;
        }

        try {
            $manifestPath = public_path('build/manifest.json');
            if (!file_exists($manifestPath)) {
                return $cachedInlineCss = '';
            }
            $manifest = json_decode((string) file_get_contents($manifestPath), true);
            $cssRelFile = $manifest['resources/css/app.css']['file'] ?? null;
            if (!$cssRelFile) {
                return $cachedInlineCss = '';
            }
            $cssFullPath = public_path('build/' . ltrim($cssRelFile, '/'));
            if (!file_exists($cssFullPath)) {
                return $cachedInlineCss = '';
            }
            $cssContent = (string) file_get_contents($cssFullPath);
            if ($cssContent === '') {
                return $cachedInlineCss = '';
            }
            $cachedInlineCss = '<style id="acs-compiled-css">' . $cssContent . '</style>';
            return $cachedInlineCss;
        } catch (\Throwable $e) {
            return $cachedInlineCss = '';
        }
    }
}



