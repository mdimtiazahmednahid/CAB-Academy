<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Auto-heal base directory and missing .env in production / shared hosting environments
$baseDir = file_exists(__DIR__ . '/vendor/autoload.php') ? __DIR__ : dirname(__DIR__);
if (!file_exists($baseDir . '/.env')) {
    if (file_exists($baseDir . '/.env.production')) {
        @copy($baseDir . '/.env.production', $baseDir . '/.env');
    } elseif (file_exists($baseDir . '/.env.production.example')) {
        @copy($baseDir . '/.env.production.example', $baseDir . '/.env');
    } elseif (file_exists($baseDir . '/.env.example')) {
        @copy($baseDir . '/.env.example', $baseDir . '/.env');
    }
} else {
    $envContents = @file_get_contents($baseDir . '/.env');
    if (is_string($envContents) && (str_contains($envContents, 'cabacademy.bd') || (str_contains($envContents, 'APP_ENV=production') && str_contains($envContents, 'APP_DEBUG=true')))) {
        $updatedEnv = str_replace(
            ['https://cabacademy.bd/', 'https://cabacademy.bd', 'http://cabacademy.bd/', 'http://cabacademy.bd'],
            'https://acsacademy.bd',
            $envContents
        );
        $updatedEnv = str_replace('APP_NAME="CAB Academy"', 'APP_NAME="ACS Academy"', $updatedEnv);
        if (str_contains($updatedEnv, 'APP_ENV=production')) {
            $updatedEnv = str_replace('APP_DEBUG=true', 'APP_DEBUG=false', $updatedEnv);
        }
        if ($updatedEnv !== $envContents) {
            @file_put_contents($baseDir . '/.env', $updatedEnv);
        }
    }
}

// Ensure required framework directories exist with write permissions
foreach ([
    $baseDir . '/storage/framework/views',
    $baseDir . '/storage/framework/sessions',
    $baseDir . '/storage/framework/cache/data',
    $baseDir . '/storage/logs',
    $baseDir . '/bootstrap/cache',
] as $frameworkDir) {
    if (!is_dir($frameworkDir)) {
        @mkdir($frameworkDir, 0775, true);
    }
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $baseDir.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

try {
    if (!file_exists($baseDir . '/vendor/autoload.php')) {
        throw new \RuntimeException('Application autoloader unavailable.');
    }

    // Register the Composer autoloader...
    require $baseDir.'/vendor/autoload.php';

    // Bootstrap Laravel and handle the request...
    /** @var Application $app */
    $app = require_once $baseDir.'/bootstrap/app.php';

    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    // Log internally for administrators; never expose raw errors, paths, or admin tools to public visitors
    @file_put_contents(
        $baseDir . '/storage/logs/laravel.log',
        '[' . date('Y-m-d H:i:s') . '] production.ERROR: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() . PHP_EOL,
        FILE_APPEND
    );

    http_response_code(503);
    echo <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ACS Academy — Learn, Grow, Lead</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #030d22;
            background-image: radial-gradient(circle at 50% 20%, #0a2558 0%, #030d22 70%);
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .container {
            max-width: 520px;
            width: 100%;
            background: rgba(10, 31, 68, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 20px;
            padding: 2.5rem;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(12px);
        }
        .badge {
            display: inline-block;
            padding: 0.3rem 0.75rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            margin-bottom: 1rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        h1 {
            font-size: 1.4rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 0.75rem;
        }
        p {
            color: #94a3b8;
            font-size: 0.92rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.8rem 1.75rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.9rem;
            text-decoration: none;
            background: #c30102;
            color: #ffffff;
            border: none;
            cursor: pointer;
            transition: background 0.2s ease;
        }
        .btn:hover { background: #9b0001; }
    </style>
</head>
<body>
    <div class="container">
        <div class="badge">ACS Academy</div>
        <h1>We Are Refreshing This Page</h1>
        <p>Our learning portal is completing a brief update. Please refresh in a moment to continue browsing courses and classroom resources.</p>
        <a href="/" class="btn">Refresh Page</a>
    </div>
</body>
</html>
HTML;
    exit;
}
