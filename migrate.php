<?php
/**
 * CAB Academy - Web Migration & Database Setup Wizard
 * Designed for InfinityFree and Shared Hosting environments without SSH terminal access.
 */

@set_time_limit(300);
@ini_set('max_execution_time', '300');
@ini_set('memory_limit', '512M');

// Auto-detect base directory whether migrate.php is located in /public or project root
$baseDir = null;
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    $baseDir = __DIR__;
} elseif (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    $baseDir = dirname(__DIR__);
} else {
    $baseDir = file_exists(__DIR__ . '/artisan') ? __DIR__ : dirname(__DIR__);
}

// Auto-heal missing .env
$envFile = $baseDir . '/.env';
if (!file_exists($envFile)) {
    if (file_exists($baseDir . '/.env.production')) {
        @copy($baseDir . '/.env.production', $envFile);
    } elseif (file_exists($baseDir . '/.env.production.example')) {
        @copy($baseDir . '/.env.production.example', $envFile);
    } elseif (file_exists($baseDir . '/.env.example')) {
        @copy($baseDir . '/.env.example', $envFile);
    }
}

// Auto-create required storage directories
$directories = [
    $baseDir . '/storage/framework/views',
    $baseDir . '/storage/framework/sessions',
    $baseDir . '/storage/framework/cache/data',
    $baseDir . '/storage/logs',
    $baseDir . '/bootstrap/cache',
];
foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// Read current .env variables
function getEnvVars($file) {
    $vars = [];
    if (file_exists($file)) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) continue;
            if (str_contains($line, '=')) {
                [$k, $v] = explode('=', $line, 2);
                $vars[trim($k)] = trim(trim($v), "\"'");
            }
        }
    }
    return $vars;
}

function updateEnvVar($file, $key, $value) {
    if (!file_exists($file)) return false;
    $content = file_get_contents($file);
    if (preg_match("/^{$key}=.*/m", $content)) {
        $content = preg_replace("/^{$key}=.*/m", "{$key}=\"{$value}\"", $content);
    } else {
        $content .= "\n{$key}=\"{$value}\"\n";
    }
    return file_put_contents($file, $content) !== false;
}

$envVars = getEnvVars($envFile);
$notification = null;
$commandOutput = null;

// Handle updating DB credentials from form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_db_config'])) {
    $newHost = trim($_POST['db_host'] ?? '');
    $newPort = trim($_POST['db_port'] ?? '3306');
    $newName = trim($_POST['db_database'] ?? '');
    $newUser = trim($_POST['db_username'] ?? '');
    $newPass = trim($_POST['db_password'] ?? '');

    updateEnvVar($envFile, 'DB_CONNECTION', 'mysql');
    updateEnvVar($envFile, 'DB_HOST', $newHost);
    updateEnvVar($envFile, 'DB_PORT', $newPort);
    updateEnvVar($envFile, 'DB_DATABASE', $newName);
    updateEnvVar($envFile, 'DB_USERNAME', $newUser);
    updateEnvVar($envFile, 'DB_PASSWORD', $newPass);

    $envVars = getEnvVars($envFile);
    $notification = ['type' => 'success', 'text' => 'Database configuration updated in .env!'];
}

// Handle Artisan Actions
$action = $_REQUEST['action'] ?? null;
if ($action) {
    try {
        require_once $baseDir . '/vendor/autoload.php';
        $app = require_once $baseDir . '/bootstrap/app.php';
        $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();

        if ($action === 'migrate') {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            $commandOutput = \Illuminate\Support\Facades\Artisan::output();
            $notification = ['type' => 'success', 'text' => 'Database tables migrated successfully!'];
        } elseif ($action === 'seed') {
            \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
            $commandOutput = \Illuminate\Support\Facades\Artisan::output();
            $notification = ['type' => 'success', 'text' => 'Initial data seeded successfully!'];
        } elseif ($action === 'fresh_seed') {
            \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--force' => true]);
            $output1 = \Illuminate\Support\Facades\Artisan::output();
            \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
            $output2 = \Illuminate\Support\Facades\Artisan::output();
            $commandOutput = $output1 . "\n" . $output2;
            $notification = ['type' => 'success', 'text' => 'Fresh database migrated and seeded successfully!'];
        } elseif ($action === 'optimize_clear') {
            \Illuminate\Support\Facades\Artisan::call('optimize:clear');
            $commandOutput = \Illuminate\Support\Facades\Artisan::output();
            $notification = ['type' => 'success', 'text' => 'All caches cleared successfully!'];
        } elseif ($action === 'key_generate') {
            \Illuminate\Support\Facades\Artisan::call('key:generate', ['--force' => true]);
            $commandOutput = \Illuminate\Support\Facades\Artisan::output();
            $notification = ['type' => 'success', 'text' => 'Application encryption key generated!'];
        } elseif ($action === 'fix_instructors') {
            \Illuminate\Support\Facades\Artisan::call('instructors:fix');
            $commandOutput = \Illuminate\Support\Facades\Artisan::output();
            $notification = ['type' => 'success', 'text' => 'Instructor and user account associations resolved!'];
        }
    } catch (\Throwable $e) {
        $notification = ['type' => 'error', 'text' => 'Action failed: ' . $e->getMessage()];
        $commandOutput = $e->getTraceAsString();
    }
}

// Test database connection
$dbConn = $envVars['DB_CONNECTION'] ?? 'mysql';
$dbHost = $envVars['DB_HOST'] ?? 'sql202.infinityfree.com';
$dbPort = $envVars['DB_PORT'] ?? '3306';
$dbName = $envVars['DB_DATABASE'] ?? 'if0_42678796_cab';
$dbUser = $envVars['DB_USERNAME'] ?? 'if0_42678796';
$dbPass = $envVars['DB_PASSWORD'] ?? '';

$dbConnected = false;
$dbError = null;

if ($dbConn === 'sqlite') {
    $dbPath = $envVars['DB_DATABASE'] ?? $baseDir . '/database/database.sqlite';
    if (file_exists($dbPath)) {
        $dbConnected = true;
    } else {
        $dbError = "SQLite database file does not exist at {$dbPath}";
    }
} else {
    try {
        $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
        $pdo = new \PDO($dsn, $dbUser, $dbPass, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_TIMEOUT => 4,
        ]);
        $dbConnected = true;
    } catch (\Throwable $e) {
        $dbError = $e->getMessage();
    }
}

// Check write permissions
$permViews = is_writable($baseDir . '/storage/framework/views');
$permSessions = is_writable($baseDir . '/storage/framework/sessions');
$permLogs = is_writable($baseDir . '/storage/logs');
$permBootstrap = is_writable($baseDir . '/bootstrap/cache');
$allPermsOk = ($permViews && $permSessions && $permLogs && $permBootstrap);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Database Setup & Migration Wizard - CAB Academy</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #030d22;
            background-image: radial-gradient(circle at 50% 15%, #0a2558 0%, #030d22 75%);
            color: #f8fafc;
            min-height: 100vh;
            padding: 2.5rem 1.5rem;
            display: flex;
            justify-content: center;
        }
        .wrapper {
            max-width: 840px;
            width: 100%;
        }
        .header {
            margin-bottom: 2rem;
            text-align: center;
        }
        .logo-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(195, 1, 2, 0.15);
            border: 1px solid rgba(195, 1, 2, 0.3);
            color: #f87171;
            padding: 0.35rem 0.85rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 0.75rem;
        }
        h1 {
            font-size: 2rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.02em;
        }
        .subtitle {
            color: #94a3b8;
            font-size: 0.95rem;
            margin-top: 0.4rem;
        }
        .card {
            background: rgba(10, 31, 68, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 1.75rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
        }
        .card-title {
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
        }
        .status-pill {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #051329;
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 10px;
            padding: 0.85rem 1.15rem;
            font-size: 0.9rem;
        }
        .status-name { color: #cbd5e1; }
        .tag-ok {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
            font-weight: 700;
            font-size: 0.75rem;
            padding: 0.2rem 0.5rem;
            border-radius: 6px;
        }
        .tag-fail {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
            font-weight: 700;
            font-size: 0.75rem;
            padding: 0.2rem 0.5rem;
            border-radius: 6px;
        }
        .alert {
            padding: 1rem 1.25rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            font-weight: 500;
        }
        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.85rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
        }
        .btn-primary {
            background: #c30102;
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(195, 1, 2, 0.4);
        }
        .btn-primary:hover {
            background: #9b0001;
            transform: translateY(-1px);
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
        .btn-success {
            background: #059669;
            color: #ffffff;
        }
        .btn-success:hover {
            background: #047857;
        }
        .button-group {
            display: flex;
            gap: 0.85rem;
            flex-wrap: wrap;
        }
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            font-size: 0.8rem;
            color: #94a3b8;
            font-weight: 600;
            margin-bottom: 0.4rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .form-group input {
            width: 100%;
            background: #051329;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            color: #ffffff;
            font-size: 0.9rem;
            font-family: inherit;
        }
        .form-group input:focus {
            outline: none;
            border-color: #38bdf8;
            box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.2);
        }
        pre.console {
            background: #020919;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 1.25rem;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.85rem;
            color: #34d399;
            overflow-x: auto;
            white-space: pre-wrap;
            max-height: 350px;
            line-height: 1.5;
        }
        .help-box {
            background: rgba(56, 189, 248, 0.05);
            border: 1px solid rgba(56, 189, 248, 0.2);
            border-radius: 10px;
            padding: 1rem 1.25rem;
            font-size: 0.85rem;
            color: #bae6fd;
            line-height: 1.6;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <div class="logo-badge">⚡ CAB Academy System Engine</div>
            <h1>Database Setup & Migration Wizard</h1>
            <p class="subtitle">Complete installation and self-healing tools for InfinityFree and Shared Hosting environments.</p>
        </div>

        <?php if ($notification): ?>
            <div class="alert <?= $notification['type'] === 'success' ? 'alert-success' : 'alert-error' ?>">
                <?= htmlspecialchars($notification['text']) ?>
            </div>
        <?php endif; ?>

        <!-- System Health Status -->
        <div class="card">
            <div class="card-title">🖥️ Server & Environment Status</div>
            <div class="grid-2">
                <div class="status-pill">
                    <span class="status-name">PHP Version</span>
                    <span class="<?= version_compare(PHP_VERSION, '8.2.0', '>=') ? 'tag-ok' : 'tag-fail' ?>"><?= PHP_VERSION ?></span>
                </div>
                <div class="status-pill">
                    <span class="status-name">Application Key (APP_KEY)</span>
                    <span class="<?= !empty($envVars['APP_KEY']) ? 'tag-ok' : 'tag-fail' ?>"><?= !empty($envVars['APP_KEY']) ? 'CONFIGURED' : 'MISSING' ?></span>
                </div>
                <div class="status-pill">
                    <span class="status-name">Database Connectivity</span>
                    <span class="<?= $dbConnected ? 'tag-ok' : 'tag-fail' ?>"><?= $dbConnected ? 'CONNECTED' : 'DISCONNECTED' ?></span>
                </div>
                <div class="status-pill">
                    <span class="status-name">Storage Permissions</span>
                    <span class="<?= $allPermsOk ? 'tag-ok' : 'tag-fail' ?>"><?= $allPermsOk ? 'WRITABLE' : 'CHECK PERMS' ?></span>
                </div>
            </div>

            <?php if (!$dbConnected && $dbError): ?>
                <div class="help-box" style="border-color: rgba(239, 68, 68, 0.3); background: rgba(239, 68, 68, 0.08); color: #fca5a5;">
                    <strong>Database Connection Error:</strong> <?= htmlspecialchars($dbError) ?>
                    <br><br>
                    <strong>InfinityFree Hosting Tip:</strong> In InfinityFree, MySQL host is <em>never</em> <code>127.0.0.1</code> or <code>localhost</code>. It is found in your vPanel (e.g. <code>sql202.infinityfree.com</code>). Check the credentials below.
                </div>
            <?php endif; ?>
        </div>

        <!-- Database Setup Actions -->
        <div class="card">
            <div class="card-title">🚀 Fast Installation & Migration Actions</div>
            <p style="color: #94a3b8; font-size: 0.9rem; margin-bottom: 1.25rem;">
                Click one of the actions below to automatically construct database tables and import initial admin credentials and courses:
            </p>
            <div class="button-group">
                <a href="?action=fresh_seed" class="btn btn-primary" onclick="return confirm('This will build all database tables and seed initial courses and admin accounts. Proceed?');">
                    ⚡ Complete Clean Setup (Migrate & Seed)
                </a>
                <a href="?action=migrate" class="btn btn-secondary">
                    🔄 Run Migrations Only
                </a>
                <a href="?action=seed" class="btn btn-secondary">
                    🌱 Seed Data Only
                </a>
                <a href="?action=fix_instructors" class="btn btn-secondary">
                    👥 Fix Instructor Accounts & Duplicates
                </a>
                <a href="?action=optimize_clear" class="btn btn-secondary">
                    🧹 Clear Cache
                </a>
            </div>

            <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.08); display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                <a href="/" class="btn btn-success">🌐 Launch CAB Academy Website</a>
                <a href="cmd.php?pin=cab2026" class="btn btn-secondary">💻 Command Terminal (PIN: cab2026)</a>
            </div>
        </div>

        <!-- Live Output Terminal -->
        <?php if ($commandOutput): ?>
            <div class="card">
                <div class="card-title">📋 Execution Log</div>
                <pre class="console"><?= htmlspecialchars($commandOutput) ?></pre>
            </div>
        <?php endif; ?>

        <!-- Database Configuration Editor -->
        <div class="card">
            <div class="card-title">⚙️ Database Configuration (.env)</div>
            <form method="POST" action="migrate.php">
                <input type="hidden" name="save_db_config" value="1">
                <div class="form-row">
                    <div class="form-group">
                        <label>DB Host</label>
                        <input type="text" name="db_host" value="<?= htmlspecialchars($dbHost) ?>" required placeholder="sql202.infinityfree.com">
                    </div>
                    <div class="form-group">
                        <label>DB Port</label>
                        <input type="text" name="db_port" value="<?= htmlspecialchars($dbPort) ?>" required placeholder="3306">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>DB Database Name</label>
                        <input type="text" name="db_database" value="<?= htmlspecialchars($dbName) ?>" required placeholder="if0_42678796_cab">
                    </div>
                    <div class="form-group">
                        <label>DB Username</label>
                        <input type="text" name="db_username" value="<?= htmlspecialchars($dbUser) ?>" required placeholder="if0_42678796">
                    </div>
                    <div class="form-group">
                        <label>DB Password</label>
                        <input type="password" name="db_password" value="<?= htmlspecialchars($dbPass) ?>" placeholder="vPanel MySQL Password">
                    </div>
                </div>
                <button type="submit" class="btn btn-secondary" style="margin-top: 0.5rem;">
                    💾 Save Credentials & Re-test Connection
                </button>
            </form>
        </div>

        <div style="text-align: center; color: #64748b; font-size: 0.8rem; margin-top: 2rem;">
            CAB Academy Deployment Architecture • Compatible with InfinityFree, cPanel & VPS
        </div>
    </div>
</body>
</html>
