<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// Bootstrap the application console kernel
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;

echo "<div style='font-family: sans-serif; padding: 20px; max-width: 800px; margin: auto;'>";
echo "<h2>Database Setup & Migration Script</h2>";

try {
    echo "<h3>1. Running Migrations...</h3>";
    Artisan::call('migrate', ['--force' => true]);
    echo "<pre style='background: #f4f4f4; padding: 10px; border-radius: 5px;'>" . Artisan::output() . "</pre>";
    
    echo "<h3>2. Clearing Cache...</h3>";
    Artisan::call('optimize:clear');
    echo "<pre style='background: #f4f4f4; padding: 10px; border-radius: 5px;'>" . Artisan::output() . "</pre>";
    
    echo "<h3 style='color: green;'>✅ Success! Your database is ready.</h3>";
    echo "<p style='color: red; font-weight: bold;'>SECURITY WARNING: Please delete this <code>migrate.php</code> file immediately from your public folder.</p>";
} catch (\Exception $e) {
    echo "<h3 style='color: red;'>❌ Error:</h3>";
    echo "<pre style='background: #fee; padding: 10px; border-radius: 5px;'>" . $e->getMessage() . "</pre>";
}

echo "</div>";
