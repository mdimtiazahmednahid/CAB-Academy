---
name: infinityfree-hosting
description: >-
  Provides deployment rules, configuration guidelines, and technical workarounds for
  running Laravel applications reliably on InfinityFree free shared hosting. Use this skill
  whenever preparing deployments, debugging shared hosting errors, configuring .htaccess,
  handling file storage without symlinks, or optimizing for InfinityFree resource limits.
---

# InfinityFree Shared Hosting Compatibility Skill

InfinityFree is a popular free PHP/MySQL hosting provider. However, like most shared hosting environments, it imposes severe restrictions compared to VPS or cloud servers. This skill provides the required patterns to ensure CAB Academy runs smoothly without crashes or broken assets.

---

## 1. Key Hosting Constraints & Mandates

| Constraint | InfinityFree Limitation | Required Solution in Codebase |
| :--- | :--- | :--- |
| **No SSH / Terminal** | Cannot run `artisan`, `composer`, or `npm` | Web-based migration runner (`public/migrate.php`), pre-compiled assets |
| **`symlink()` Disabled** | `php artisan storage:link` fails | Stream storage files via dynamic route or store uploads in `public/uploads` |
| **No Background Workers** | Daemons, Redis, Supervison not allowed | Set `QUEUE_CONNECTION=sync` in `.env.production` |
| **No Node.js / Vite** | Cannot compile Tailwind/Vite on server | Always run `npm run build` locally before pushing to production |
| **Document Root** | Root folder is `/htdocs` | Root `.htaccess` redirects into `public/`, or move `public` files into `htdocs` |
| **10MB Upload Limit** | Uploading files > 10MB triggers HTTP 413 | Compress images on client/server, validate file sizes in Form Requests |
| **PHP Execution Time** | 20-30 seconds timeout | Keep migrations, seeding, and batch operations chunked and lightweight |
| **Email SMTP** | Port 25 blocked | Use authenticated TLS/SSL on port 587 or 465 (Gmail, Brevo, SendGrid) |

---

## 2. Directory Layout & .htaccess Routing

InfinityFree maps domains to an `htdocs/` folder. For Laravel:

### Root `.htaccess` (placed in `/` or `/htdocs`)
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

### Public `.htaccess` (inside `public/.htaccess`)
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

## 3. Storage Symlink Workaround

Since `symlink()` is disabled on InfinityFree, calls to `Storage::url('photos/abc.jpg')` which produce `/storage/photos/abc.jpg` will result in 404 Not Found errors unless resolved.

### Recommended Approaches:
1. **Dynamic Media Route Fallback**:
   In `routes/web.php`:
   ```php
   Route::get('/storage/{folder}/{filename}', function ($folder, $filename) {
       $path = storage_path("app/public/{$folder}/{$filename}");
       if (!file_exists($path)) {
           abort(404);
       }
       return response()->file($path);
   })->where('filename', '.*');
   ```
2. **Direct Public Disk Configuration**:
   Configure a disk in `config/filesystems.php` that writes directly into `public/uploads`:
   ```php
   'public_uploads' => [
       'driver' => 'local',
       'root' => public_path('uploads'),
       'url' => env('APP_URL').'/uploads',
       'visibility' => 'public',
   ],
   ```

---

## 4. Production Environment Checklist (`.env.production`)

```ini
APP_NAME="CAB Academy"
APP_ENV=production
APP_KEY=base64:... # Generate locally with php artisan key:generate --show
APP_DEBUG=false
APP_URL=http://your-subdomain.infinityfreeapp.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=sqlXXX.infinityfree.com
DB_PORT=3306
DB_DATABASE=epiz_XXXXXX_cab
DB_USERNAME=epiz_XXXXXX
DB_PASSWORD=your_mysql_password

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=public
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

---

## 5. Web Migration Procedure

To run migrations without SSH:
1. Upload the files including `public/migrate.php`.
2. Secure `migrate.php` with a secret key or remove it immediately after migration.
3. Access `http://your-domain.com/migrate.php?key=YOUR_SECRET_KEY` in the browser.
4. Verify migration output.
