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

        // Mirror file from public/uploads to public/storage if public/storage is a directory and missing the file
        if (file_exists(public_path('uploads/' . $trimmed)) && is_dir(public_path('storage')) && !is_link(public_path('storage')) && !file_exists(public_path('storage/' . $trimmed))) {
            @mkdir(dirname(public_path('storage/' . $trimmed)), 0775, true);
            @copy(public_path('uploads/' . $trimmed), public_path('storage/' . $trimmed));
        }

        return '/storage/' . $trimmed;
    }
}


