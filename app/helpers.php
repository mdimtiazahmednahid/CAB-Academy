<?php

if (!function_exists('storage_asset')) {
    /**
     * Generate a fully-qualified URL for an uploaded storage asset,
     * ensuring proper /uploads/ or /storage/ resolution across shared hosting and symlinked environments.
     */
    function storage_asset(?string $path, ?string $fallback = null): ?string
    {
        if (empty($path)) {
            return $fallback;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, 'data:')) {
            return $path;
        }

        $trimmed = ltrim($path, '/');

        // If path explicitly starts with uploads/
        if (str_starts_with($trimmed, 'uploads/')) {
            return '/' . $trimmed;
        }

        // If path explicitly starts with storage/
        if (str_starts_with($trimmed, 'storage/')) {
            return '/' . $trimmed;
        }

        return '/storage/' . $trimmed;
    }
}


