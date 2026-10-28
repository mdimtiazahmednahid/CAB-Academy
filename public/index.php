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

// Register the Composer autoloader...
require $baseDir.'/vendor/autoload.php';

try {
    // Bootstrap Laravel and handle the request...
    /** @var Application $app */
    $app = require_once $baseDir.'/bootstrap/app.php';

    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    $msg = $e->getMessage();
    $isDbError = str_contains($msg, 'SQLSTATE') || str_contains($msg, 'Connection refused') || str_contains($msg, "doesn't exist") || str_contains($msg, 'Access denied');
    $isKeyError = str_contains($msg, 'No application encryption key has been specified');

    http_response_code(500);
    $title = $isKeyError ? 'Encryption Key Required' : ($isDbError ? 'Database Setup Required' : 'Application Setup Notice');
    $desc = $isKeyError
        ? 'Your application encryption key is missing or not yet generated in .env.'
        : ($isDbError
            ? 'The application could not query the database. Initial tables may need to be migrated.'
            : 'A bootstrap configuration error occurred on this server.');

    $escapedMsg = htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');

    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Setup Required - CAB Academy</title>
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
            max-width: 680px;
            width: 100%;
            background: rgba(10, 31, 68, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(12px);
        }
        .badge {
            display: inline-block;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
            background: rgba(195, 1, 2, 0.2);
            color: #f87171;
            margin-bottom: 0.75rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.75rem;
        }
        p {
            color: #94a3b8;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 1.25rem;
        }
        .error-card {
            background: #051329;
            border: 1px solid rgba(239, 68, 68, 0.25);
            border-left: 4px solid #c30102;
            border-radius: 10px;
            padding: 1.25rem;
            margin: 1.5rem 0;
            overflow-x: auto;
        }
        .error-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #f87171;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .error-message {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.85rem;
            color: #fca5a5;
            word-break: break-all;
            white-space: pre-wrap;
            line-height: 1.5;
        }
        .actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.875rem 1.75rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .btn-primary {
            background: #c30102;
            color: #ffffff;
            border: none;
            box-shadow: 0 4px 14px rgba(195, 1, 2, 0.4);
        }
        .btn-primary:hover {
            background: #9b0001;
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.08);
            color: #e2e8f0;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="badge">CAB Academy Diagnostic</div>
        <h1>⚙️ {$title}</h1>
        <p>{$desc}</p>
        <div class="error-card">
            <div class="error-title">Exception Detail</div>
            <div class="error-message">{$escapedMsg}</div>
        </div>
        <p>Use the self-healing setup wizard below to initialize database tables or adjust credentials:</p>
        <div class="actions">
            <a href="migrate.php" class="btn btn-primary">🚀 Run Database Setup Wizard</a>
            <a href="cmd.php?pin=cab2026" class="btn btn-secondary">💻 Command Terminal (PIN: cab2026)</a>
        </div>
    </div>
</body>
</html>
HTML;
    exit;
}
