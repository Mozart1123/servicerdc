<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Erreur')  | ProConnect</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        :root {
            --bg-main: #0f172a;
            --bg-card: rgba(30, 41, 59, 0.7);
            --border-card: rgba(255, 255, 255, 0.08);
            --accent: #29b6d1;
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body {
            background-color: var(--bg-main);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        .blob {
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(41, 182, 209, 0.15) 0%, rgba(41, 182, 209, 0) 70%);
            border-radius: 50%;
            z-index: 1;
            filter: blur(40px);
        }
        .blob-1 { top: -100px; left: -100px; animation: float-slow 12s infinite alternate ease-in-out; }
        .blob-2 { bottom: -150px; right: -100px; animation: float-slow 18s infinite alternate-reverse ease-in-out; }
        @keyframes float-slow {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(50px, 30px) scale(1.1); }
        }
        .container { position: relative; z-index: 10; width: 100%; max-width: 540px; padding: 20px; }
        .flag-stripe {
            height: 4px;
            background: linear-gradient(90deg, #007A5E 33.3%, #F7D000 33.3% 66.6%, #CE1020 66.6%);
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
        }
        .card {
            background: var(--bg-card);
            backdrop-filter: blur(16px);
            border: 1px solid var(--border-card);
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
            padding: 40px 30px;
            text-align: center;
            overflow: hidden;
            position: relative;
        }
        .icon-wrapper { margin-bottom: 30px; position: relative; display: inline-flex; align-items: center; justify-content: center; }
        .main-icon {
            width: 80px;
            height: 80px;
            background: rgba(41, 182, 209, 0.1);
            color: var(--accent);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            border: 1px solid rgba(41, 182, 209, 0.2);
        }
        h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 16px;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #ffffff 0%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        p { font-size: 14px; line-height: 1.6; color: var(--text-secondary); margin-bottom: 28px; }
        .code-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(148, 163, 184, 0.1);
            border: 1px solid rgba(148, 163, 184, 0.2);
            color: var(--text-secondary);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--accent);
            color: #fff;
            font-weight: 600;
            font-size: 14px;
            padding: 11px 22px;
            border-radius: 10px;
            text-decoration: none;
            transition: opacity .15s ease;
        }
        .btn:hover { opacity: .88; }
        .btn-secondary {
            background: transparent;
            border: 1px solid var(--border-card);
            color: var(--text-primary);
            margin-left: 10px;
        }
        .footer { margin-top: 24px; font-size: 11px; color: var(--text-secondary); opacity: 0.6; text-align: center; }
        .brand { font-weight: 600; color: var(--text-primary); }
    </style>
</head>
<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="container">
        <div class="flag-stripe"></div>
        <div class="card">
            <div class="code-badge">ERREUR @yield('code')</div>
            <div class="icon-wrapper">
                <div class="main-icon">
                    <i class="fas @yield('icon', 'fa-triangle-exclamation')"></i>
                </div>
            </div>

            <h1>@yield('title')</h1>

            <p>@yield('message')</p>

            @yield('actions')
        </div>

        <div class="footer">
            <span class="brand">Pro<span style="color: var(--accent);">Connect</span></span> &copy; {{ date('Y') }}. Tous droits réservés.
        </div>
    </div>
</body>
</html>
