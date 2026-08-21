<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Force delete config cache file because Artisan command might fail on shared hosting
$cacheFile = __DIR__.'/../bootstrap/cache/config.php';
if (file_exists($cacheFile)) {
    unlink($cacheFile);
    echo "<p style='color:green'>Deleted cached config file.</p>";
} else {
    echo "<p>No cached config file found.</p>";
}

// 2. Force update .env file
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    if (strpos($envContent, 'FILESYSTEM_DISK=local') !== false) {
        $envContent = str_replace('FILESYSTEM_DISK=local', 'FILESYSTEM_DISK=public', $envContent);
        file_put_contents($envPath, $envContent);
        echo "<p style='color:green'>Updated .env FILESYSTEM_DISK to public.</p>";
    }
}

// Reload env
try {
    \Dotenv\Dotenv::createImmutable(__DIR__.'/../')->load();
} catch (\Exception $e) {}

echo "<hr>";
echo "<h3>Diagnosis:</h3>";
echo "<b>Current FILESYSTEM_DISK in .env:</b> " . env('FILESYSTEM_DISK') . "<br>";
echo "<b>Laravel's Internal Default Disk:</b> " . config('filesystems.default') . "<br>";

$testPath = 'profiles/test.jpg';
echo "<b>Generated URL for test image:</b> " . \Illuminate\Support\Facades\Storage::url($testPath) . "<br>";

if (strpos(\Illuminate\Support\Facades\Storage::url($testPath), '/uploads/') !== false) {
    echo "<h2 style='color:green'>SUCCESS! URLs are now correctly generating as /uploads/. The images will now work!</h2>";
} else {
    echo "<h2 style='color:red'>FAILURE. URLs are STILL generating as /storage/.</h2>";
}
