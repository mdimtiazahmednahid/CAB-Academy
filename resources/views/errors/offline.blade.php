<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#021f59">
    <title>Offline — ACS Academy</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #01143d 0%, #021f59 50%, #063b96 100%);
            color: #ffffff;
        }
        .card {
            max-width: 420px;
            width: 100%;
            text-align: center;
            padding: 2rem 1.75rem;
            border-radius: 1.25rem;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(18px);
            border: 1px solid rgba(220, 162, 44, 0.45);
            box-shadow: 0 20px 50px rgba(1, 12, 40, 0.45);
        }
        img {
            width: 68px;
            height: 68px;
            object-fit: contain;
            margin: 0 auto 1rem;
            display: block;
        }
        h1 {
            font-size: 1.25rem;
            font-weight: 800;
            margin: 0 0 0.5rem;
        }
        p {
            font-size: 0.875rem;
            color: #cbd5e1;
            line-height: 1.55;
            margin: 0 0 1.5rem;
        }
        button {
            border: 1px solid rgba(255, 226, 122, 0.45);
            background: linear-gradient(135deg, #d62839 0%, #b0141c 55%, #8a0e16 100%);
            color: #ffffff;
            font-weight: 700;
            font-size: 0.875rem;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="card">
        <img src="/images/logo.png" alt="ACS Academy">
        <h1>You're Currently Offline</h1>
        <p>Check your internet connection to access live classes, enrollment, and course materials.</p>
        <button type="button" onclick="window.location.reload()">Retry Connection</button>
    </div>
</body>
</html>
