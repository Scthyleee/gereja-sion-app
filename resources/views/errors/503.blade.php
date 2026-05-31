<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance — Gereja SION</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #141820 0%, #1c2030 50%, #0f1118 100%);
            color: #e4e2dd;
            overflow: hidden;
            position: relative;
        }
        body::before {
            content: ''; position: absolute; top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: radial-gradient(circle at 30% 30%, rgba(201,168,76,.08) 0%, transparent 50%),
                        radial-gradient(circle at 70% 70%, rgba(125,168,130,.06) 0%, transparent 50%);
            animation: bgFloat 15s ease-in-out infinite;
        }
        @keyframes bgFloat {
            0%,100% { transform: translate(0,0) rotate(0deg); }
            50% { transform: translate(-3%,-3%) rotate(2deg); }
        }
        .maintenance-card {
            position: relative; z-index: 1;
            max-width: 550px; width: 90%;
            text-align: center; padding: 50px 40px;
            background: rgba(28,32,48,.7);
            border: 1px solid rgba(201,168,76,.15);
            border-radius: 24px;
            backdrop-filter: blur(20px);
            box-shadow: 0 20px 60px rgba(0,0,0,.4), 0 0 80px rgba(201,168,76,.05);
        }
        .logo-circle {
            width: 90px; height: 90px; border-radius: 50%;
            background: linear-gradient(135deg, #c9a84c, #a68930);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 24px; font-size: 2.2rem; color: #fff;
            box-shadow: 0 8px 32px rgba(201,168,76,.25);
        }
        h1 { font-size: 1.8rem; font-weight: 700; margin-bottom: 8px; color: #c9a84c; }
        .subtitle { color: #b5b3ae; font-size: .95rem; margin-bottom: 32px; line-height: 1.6; }
        .countdown-wrap {
            display: flex; justify-content: center; gap: 16px; margin-bottom: 32px;
        }
        .countdown-box {
            background: rgba(201,168,76,.08); border: 1px solid rgba(201,168,76,.15);
            border-radius: 16px; padding: 16px 20px; min-width: 80px;
        }
        .countdown-box .num {
            font-size: 2rem; font-weight: 800; color: #c9a84c;
            line-height: 1;
        }
        .countdown-box .label {
            font-size: .7rem; text-transform: uppercase; letter-spacing: 1px;
            color: #7a7a85; margin-top: 4px;
        }
        .status-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(255,152,0,.12); color: #ffb74d;
            padding: 8px 20px; border-radius: 20px;
            font-size: .82rem; font-weight: 600;
            border: 1px solid rgba(255,152,0,.2);
        }
        .pulse-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: #ff9800; animation: pulse 1.5s ease-in-out infinite;
        }
        @keyframes pulse {
            0%,100% { opacity: 1; transform: scale(1); }
            50% { opacity: .5; transform: scale(1.3); }
        }
        .footer-text { margin-top: 28px; color: #555; font-size: .78rem; }
    </style>
</head>
<body>
    <div class="maintenance-card">
        <div class="logo-circle"><i class="fas fa-cross"></i></div>
        <h1>Sedang Maintenance</h1>
        <p class="subtitle">
            Sistem Informasi Gereja SION sedang dalam pemeliharaan terjadwal.<br>
            Kami akan segera kembali online.
        </p>
        <div class="countdown-wrap" id="countdown">
            <div class="countdown-box"><div class="num" id="cd-h">00</div><div class="label">Jam</div></div>
            <div class="countdown-box"><div class="num" id="cd-m">00</div><div class="label">Menit</div></div>
            <div class="countdown-box"><div class="num" id="cd-s">00</div><div class="label">Detik</div></div>
        </div>
        <div class="status-badge"><div class="pulse-dot"></div> Maintenance Berlangsung</div>
        <p class="footer-text">Gereja Kalimantan Evangelis — Resort Palangka Raya Tengah</p>
    </div>
    <script>
        // Default 2 hours countdown from page load, or read from meta tag
        let duration = {{ $exception->getHeaders()['Retry-After'] ?? 7200 }};
        let end = Date.now() + duration * 1000;
        function tick() {
            let diff = Math.max(0, Math.floor((end - Date.now()) / 1000));
            document.getElementById('cd-h').textContent = String(Math.floor(diff/3600)).padStart(2,'0');
            document.getElementById('cd-m').textContent = String(Math.floor((diff%3600)/60)).padStart(2,'0');
            document.getElementById('cd-s').textContent = String(diff%60).padStart(2,'0');
            if (diff > 0) requestAnimationFrame(()=>setTimeout(tick,1000));
            else location.reload();
        }
        tick();
    </script>
</body>
</html>
