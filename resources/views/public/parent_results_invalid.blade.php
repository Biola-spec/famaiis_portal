<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Link unavailable</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #F5F7FA; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .box { background: #fff; padding: 32px; border-radius: 12px; max-width: 420px; text-align: center; border: 1px solid #E4E9F0; }
        h1 { font-size: 1.25rem; color: #1E2E4A; }
        p { color: #62728D; }
    </style>
</head>
<body>
<div class="box">
    <h1>This results link is not available</h1>
    <p>{{ $reason ?? 'The link may have expired or been deactivated. Please contact the school for a new link.' }}</p>
</div>
</body>
</html>
