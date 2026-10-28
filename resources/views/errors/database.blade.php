<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Database Setup Required - CAB Academy</title>
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
            background: rgba(10, 31, 68, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(12px);
        }
        .header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .icon-wrap {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: rgba(195, 1, 2, 0.15);
            border: 1px solid rgba(195, 1, 2, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #f87171;
            font-size: 1.5rem;
            flex-shrink: 0;
        }
        h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.02em;
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
        .steps-box {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 12px;
            padding: 1.25rem;
            margin: 1.5rem 0;
        }
        .steps-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #38bdf8;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .steps-list {
            padding-left: 1.25rem;
            color: #cbd5e1;
            font-size: 0.875rem;
            line-height: 1.6;
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
        .badge {
            display: inline-block;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            background: rgba(56, 189, 248, 0.15);
            color: #38bdf8;
            margin-bottom: 0.75rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="badge">CAB Academy Diagnostic Center</div>
        <div class="header">
            <div class="icon-wrap">⚙️</div>
            <div>
                <h1>Database Setup or Migration Required</h1>
                <p style="margin-bottom: 0;">The application is loaded, but the database connection or table schema is not initialized.</p>
            </div>
        </div>

        @if(isset($message) && !empty($message))
            <div class="error-card">
                <div class="error-title">Diagnostic Details</div>
                <div class="error-message">{{ $message }}</div>
            </div>
        @elseif(isset($exception))
            <div class="error-card">
                <div class="error-title">Diagnostic Details</div>
                <div class="error-message">{{ $exception->getMessage() }}</div>
            </div>
        @endif

        <div class="steps-box">
            <div class="steps-title">
                <span>🔧</span> Quick Resolution Steps
            </div>
            <ol class="steps-list">
                <li>Click <strong>Run Database Setup Wizard</strong> below to run migrations and initialize tables automatically.</li>
                <li>If using <strong>InfinityFree</strong> or shared hosting, verify in your <code>.env</code> that <code>DB_HOST</code> starts with <code>sqlXXX.infinityfree.com</code> (not <code>127.0.0.1</code>).</li>
                <li>Make sure your database name and credentials in <code>.env</code> match your hosting control panel.</li>
            </ol>
        </div>

        <div class="actions">
            <a href="{{ url('/migrate.php') }}" class="btn btn-primary">🚀 Run Database Setup Wizard</a>
            <a href="{{ url('/cmd.php?pin=cab2026') }}" class="btn btn-secondary">💻 Command Terminal (PIN: cab2026)</a>
            <a href="{{ url('/') }}" class="btn btn-secondary">🔄 Retry Homepage</a>
        </div>
    </div>
</body>
</html>
