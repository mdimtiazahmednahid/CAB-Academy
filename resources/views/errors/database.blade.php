<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Service Notice - ACS Academy</title>
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
            max-width: 540px;
            width: 100%;
            background: rgba(10, 31, 68, 0.88);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 20px;
            padding: 2.5rem;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(12px);
        }
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
            background: rgba(255, 255, 255, 0.1);
            color: #cbd5e1;
            margin-bottom: 1rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        h1 {
            font-size: 1.45rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 0.75rem;
        }
        p {
            color: #94a3b8;
            font-size: 0.92rem;
            line-height: 1.6;
            margin-bottom: 1.75rem;
        }
        .actions {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.8rem 1.5rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.9rem;
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
        .btn-primary:hover { background: #9b0001; }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.08);
            color: #e2e8f0;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        .btn-secondary:hover { background: rgba(255, 255, 255, 0.15); color: #ffffff; }
    </style>
</head>
<body>
    <div class="container">
        <div class="badge">ACS Academy</div>
        <h1>Service Temporarily Busy</h1>
        <p>We are optimizing our course catalog right now. Please refresh the page or return to the homepage.</p>
        <div class="actions">
            <a href="{{ url('/') }}" class="btn btn-primary">Return to Homepage</a>
            <a href="javascript:window.location.reload()" class="btn btn-secondary">Refresh Page</a>
        </div>
    </div>
</body>
</html>
