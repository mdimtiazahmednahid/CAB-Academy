<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $output = "";
    
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    $output .= \Illuminate\Support\Facades\Artisan::output() . "\n";
    
    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
    $output .= \Illuminate\Support\Facades\Artisan::output();
    
    echo "<h1>Migration & Seeding completed successfully!</h1>";
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
} catch (\Exception $e) {
    echo "<h1>Error:</h1>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}
