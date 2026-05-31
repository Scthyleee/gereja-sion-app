<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance — GKE Sion</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #0a0e17; color: #e8e6e3; font-family: 'Inter', sans-serif; text-align: center; padding: 2rem; }
        .wrap { max-width: 500px; }
        .icon { font-size: 4rem; margin-bottom: 1.5rem; opacity: .3; }
        h1 { font-family: 'Playfair Display', serif; font-size: 2rem; color: #c9a84c; margin-bottom: 1rem; }
        p { color: #8b95a5; line-height: 1.7; margin-bottom: 2rem; }
        a { color: #c9a84c; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="icon">🔧</div>
        <h1>Sedang Dalam Perbaikan</h1>
        <p>Website sedang dalam tahap pemeliharaan untuk meningkatkan layanan. Silakan kembali beberapa saat lagi.</p>
        @auth
        <a href="{{ route('dashboard') }}">← Kembali ke Admin</a>
        @endauth
    </div>
</body>
</html>
