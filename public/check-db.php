<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = \App\Models\User::all();

echo "<table border='1' cellpadding='5' style='border-collapse: collapse; font-family: sans-serif;'>";
echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Profile Picture DB Path</th><th>Storage::url()</th><th>File Exists on Disk?</th></tr>";

foreach ($users as $user) {
    $dbPath = $user->profile_picture ?: 'NULL';
    $url = $user->profile_picture ? \Illuminate\Support\Facades\Storage::url($user->profile_picture) : 'N/A';
    
    // Check if it exists in the public_path or DOCUMENT_ROOT
    $exists = 'N/A';
    if ($user->profile_picture) {
        $path1 = public_path('uploads/' . $user->profile_picture);
        $path2 = (isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . $user->profile_picture : '');
        $path3 = storage_path('app/public/' . $user->profile_picture);
        
        $existsStr = [];
        if (file_exists($path1)) $existsStr[] = 'public_path(uploads)';
        if ($path2 && file_exists($path2)) $existsStr[] = 'DOCUMENT_ROOT/uploads';
        if (file_exists($path3)) $existsStr[] = 'storage/app/public';
        
        $exists = empty($existsStr) ? '<span style="color:red">NO</span>' : '<span style="color:green">YES in: ' . implode(', ', $existsStr) . '</span>';
    }

    echo "<tr>";
    echo "<td>{$user->id}</td>";
    echo "<td>{$user->name}</td>";
    echo "<td>{$user->email}</td>";
    echo "<td>{$dbPath}</td>";
    echo "<td><a href='{$url}' target='_blank'>{$url}</a></td>";
    echo "<td>{$exists}</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h3>Settings Table:</h3>";
$logoSetting = \App\Models\Setting::where('key', 'site_logo')->first();
$logoValue = $logoSetting ? $logoSetting->value : 'NULL';
echo "<b>site_logo DB value:</b> " . htmlspecialchars($logoValue) . "<br>";
$cachedLogo = \App\Models\Setting::getVal('site_logo', 'NULL');
echo "<b>site_logo Cached value:</b> " . htmlspecialchars($cachedLogo) . "<br>";

echo "<h3>Config Debug:</h3>";
echo "FILESYSTEM_DISK (env): " . env('FILESYSTEM_DISK') . "<br>";
echo "FILESYSTEM_DISK (config): " . config('filesystems.default') . "<br>";
echo "Public Disk Root: " . config('filesystems.disks.public.root') . "<br>";
echo "Public Disk URL: " . config('filesystems.disks.public.url') . "<br>";

