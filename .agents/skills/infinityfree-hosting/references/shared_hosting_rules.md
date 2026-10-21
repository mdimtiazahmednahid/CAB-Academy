# Shared Hosting Rules & Optimization Checklist

## 1. Asset Compilation
- Never rely on Vite dev server (`npm run dev`) or `@vite(...)` hot reloading in production on InfinityFree.
- Always execute:
  ```bash
  npm run build
  ```
- Verify that `public/build/manifest.json` and compiled assets in `public/build/assets/` are committed or uploaded via FTP.
- Test that Blade `@vite(['resources/css/app.css', 'resources/js/app.js'])` reads from `public/build/manifest.json` correctly.

## 2. Database Migrations
- Use `public/migrate.php` with authentication token:
  ```php
  if (request('token') !== env('DEPLOY_TOKEN')) {
      abort(403, 'Unauthorized');
  }
  ```
- Alternatively, export SQL schema locally via `mysqldump` and import directly into InfinityFree's phpMyAdmin interface.

## 3. Session & Cache Handling
- Set `SESSION_DRIVER=file` or `cookie`. Do NOT use `redis` or `database` unless the database connection is resilient.
- Set `CACHE_STORE=file`.
- Avoid executing commands that cache configurations (`php artisan config:cache`) if `.env` values are modified via cPanel or FTP, as cached configurations override runtime environment variables until cleared.

## 4. File Upload Hygiene
- InfinityFree has an upload ceiling of 10MB per file and an account storage quota.
- Always validate incoming uploads:
  ```php
  $request->validate([
      'thumbnail' => 'nullable|image|mimes:jpeg,png,webp,jpg|max:5120', // 5MB limit
  ]);
  ```
