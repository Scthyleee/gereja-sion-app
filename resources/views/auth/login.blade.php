<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — {{ $profil->nama_gereja ?? 'Gereja' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #141820 0%, #1a1f2e 40%, #1c2535 70%, #0f1520 100%);
            position: relative;
            overflow: hidden;
        }
        /* Ambient light orbs */
        body::before, body::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            animation: float 8s ease-in-out infinite;
        }
        body::before {
            width: 400px; height: 400px;
            background: rgba(139,105,20,.08);
            top: -100px; right: -100px;
        }
        body::after {
            width: 300px; height: 300px;
            background: rgba(107,143,113,.06);
            bottom: -80px; left: -80px;
            animation-delay: 4s;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-30px) scale(1.05); }
        }

        .login-card {
            background: rgba(28,32,48,.6);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(201,168,76,.12);
            border-radius: 24px;
            padding: 3rem 2.5rem;
            width: 100%;
            max-width: 440px;
            color: #e4e2dd;
            box-shadow: 0 25px 60px rgba(0,0,0,.4), 0 0 0 1px rgba(255,255,255,.03);
            position: relative;
            z-index: 1;
            animation: cardEntry .8s cubic-bezier(.4,0,.2,1);
        }
        @keyframes cardEntry {
            from { opacity: 0; transform: translateY(40px) scale(.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .login-logo {
            width: 90px; height: 90px;
            margin: 0 auto 1.5rem;
            display: block;
            filter: drop-shadow(0 4px 16px rgba(201,168,76,.25));
        }
        .login-card h2 {
            font-family: 'DM Serif Display', serif;
            font-weight: 400; font-size: 1.6rem;
            text-align: center;
            letter-spacing: .5px;
        }
        .login-card .subtitle {
            text-align: center;
            color: rgba(201,168,76,.5);
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            font-weight: 600;
            margin-bottom: 2rem;
        }
        .login-card .form-control {
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(201,168,76,.12);
            color: #e4e2dd;
            border-radius: 12px;
            padding: .75rem 1rem;
            font-size: .9rem;
            transition: all .3s ease;
        }
        .login-card .form-control:focus {
            background: rgba(255,255,255,.07);
            border-color: rgba(201,168,76,.4);
            box-shadow: 0 0 0 3px rgba(201,168,76,.1);
            color: #fff;
        }
        .login-card .form-control::placeholder { color: rgba(228,226,221,.3); }
        .login-card label {
            color: rgba(228,226,221,.6);
            font-size: .78rem; font-weight: 600;
            letter-spacing: .3px;
        }
        .btn-login {
            background: linear-gradient(135deg, #8b6914, #a67c1a);
            border: none;
            border-radius: 12px;
            padding: .8rem;
            font-weight: 700;
            font-size: .95rem;
            width: 100%;
            color: #fff;
            transition: all .3s cubic-bezier(.4,0,.2,1);
            box-shadow: 0 4px 16px rgba(139,105,20,.25);
            letter-spacing: .3px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(139,105,20,.35);
            color: #fff;
            background: linear-gradient(135deg, #a67c1a, #c9a84c);
        }
        .btn-login:active { transform: translateY(0); }
        .alert-danger {
            background: rgba(196,80,80,.12);
            border: 1px solid rgba(196,80,80,.2);
            color: #e06060;
            border-radius: 12px;
            font-size: .85rem;
        }
        .login-footer {
            text-align: center;
            margin-top: 2rem;
            color: rgba(228,226,221,.2);
            font-size: .7rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-3">
            <img src="{{ asset('images/logo-gereja.svg') }}" alt="Logo" class="login-logo">
            <h2>{{ $profil->nama_gereja ?? 'Gereja' }}</h2>
            <div class="subtitle">Sistem Informasi</div>
        </div>

        @if($errors->any())
        <div class="alert alert-danger mb-3">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="admin@gereja.com" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="mb-4">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-login">
                <i class="fas fa-sign-in-alt me-2"></i>Masuk
            </button>
        </form>
        <div class="login-footer">
            &copy; {{ date('Y') }} {{ $profil->nama_gereja ?? 'Gereja' }}
        </div>
    </div>
</body>
</html>
