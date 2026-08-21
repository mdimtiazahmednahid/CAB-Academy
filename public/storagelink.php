<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    \Illuminate\Support\Facades\Artisan::call('storage:link');
    echo "<h1>Storage Linked successfully!</h1>";
    echo "<pre>" . \Illuminate\Support\Facades\Artisan::output() . "</pre>";
} catch (\Exception $e) {
    echo "<h1>Error:</h1>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    echo "<p>If you get an error about 'symlink() has been disabled', your hosting provider restricts this feature. You will have to contact their support or manually copy files from storage/app/public to public/storage.</p>";
}
