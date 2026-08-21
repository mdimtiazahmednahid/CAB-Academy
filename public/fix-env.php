<?php

$envPath = __DIR__ . '/../.env';

if (!file_exists($envPath)) {
    die("<h1>Error: .env file not found!</h1>");
}

$envContent = file_get_contents($envPath);

// Replace FILESYSTEM_DISK=local with FILESYSTEM_DISK=public
if (strpos($envContent, 'FILESYSTEM_DISK=local') !== false) {
    $envContent = str_replace('FILESYSTEM_DISK=local', 'FILESYSTEM_DISK=public', $envContent);
    file_put_contents($envPath, $envContent);
    echo "<h1>Success! Updated FILESYSTEM_DISK to 'public' in .env</h1>";
} elseif (strpos($envContent, 'FILESYSTEM_DISK=public') !== false) {
    echo "<h1>Already fixed! FILESYSTEM_DISK is already 'public'.</h1>";
} else {
    // If it's missing, append it
    $envContent .= "\nFILESYSTEM_DISK=public\n";
    file_put_contents($envPath, $envContent);
    echo "<h1>Success! Appended FILESYSTEM_DISK=public to .env</h1>";
}

// Clear config cache automatically
try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    echo "<p>Config cache cleared successfully!</p>";
} catch (\Exception $e) {
    echo "<p>Cache clear skipped.</p>";
}
