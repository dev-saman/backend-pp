<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Expired — AdvantageHCS</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f0f4f8; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background: #fff; border-radius: 16px; padding: 48px 40px; text-align: center; max-width: 440px; box-shadow: 0 4px 24px rgba(0,0,0,.1); }
        .icon { font-size: 56px; margin-bottom: 16px; }
        h1 { font-size: 22px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
        p { font-size: 14px; color: #64748b; line-height: 1.6; }
        .contact { margin-top: 24px; padding: 16px; background: #f8fafc; border-radius: 8px; font-size: 13px; color: #475569; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">⏰</div>
        <h1>This Link Has Expired</h1>
        <p>The form link for <strong>{{ $assignment->patient->first_name }} {{ $assignment->patient->last_name }}</strong> has expired and is no longer accessible.</p>
        <div class="contact">
            Please contact your healthcare provider to receive a new link.
        </div>
    </div>
</body>
</html>
