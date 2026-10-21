# Storage Symlink Workaround for Free Shared Hosting

## The Problem
On standard Laravel installations, running `php artisan storage:link` creates a symbolic link from `public/storage` pointing to `storage/app/public`.
On InfinityFree and similar cPanel/vPanel free shared hosts:
1. Shell/SSH access is not provided.
2. The PHP `symlink()` function is disabled in `php.ini` for security reasons (`symlink() has been disabled for security reasons`).
3. Running code that calls `Artisan::call('storage:link')` throws a fatal error or silently fails.
4. As a result, files uploaded to `storage/app/public` are not accessible via `http://example.com/storage/filename.jpg`.

---

## The Solution: Streaming Media Fallback Route

Add a dedicated route in `routes/web.php` to handle requests to `/storage/...` directly:

```php
Route::get('/storage/{folder}/{file}', function ($folder, $file) {
    $filePath = storage_path("app/public/{$folder}/" . $file);
    
    if (!file_exists($filePath)) {
        abort(404);
    }
    
    $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';
    
    return response()->file($filePath, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('file', '.*');
```

### Why this works:
- Any call to `Storage::url('thumbnails/xyz.png')` generates `/storage/thumbnails/xyz.png`.
- Because there is no physical `public/storage` folder or symlink, Apache's `RewriteCond %{REQUEST_FILENAME} !-f` triggers and passes the request to Laravel's `index.php`.
- Laravel matches the route above and streams the file with the proper content-type and cache headers.
- Requires zero symlinks and works out-of-the-box on InfinityFree!
