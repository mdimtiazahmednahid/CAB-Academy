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


