<?php

if (!function_exists('storage_asset')) {
    /**
     * Generate a fully-qualified URL for an uploaded storage asset,
     * ensuring proper /storage/ prefixing across shared hosting and symlinked environments.
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
        if (str_starts_with($trimmed, 'storage/')) {
            return asset($trimmed);
        }
        return asset('storage/' . $trimmed);
    }
}
