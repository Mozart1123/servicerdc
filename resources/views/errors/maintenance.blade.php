<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance en cours | ProConnect</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
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

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }

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

        /* Ambient glowing background blobs */
        .blob {
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(41, 182, 209, 0.15) 0%, rgba(41, 182, 209, 0) 70%);
            border-radius: 50%;
            z-index: 1;
            filter: blur(40px);
        }

        .blob-1 {
            top: -100px;
            left: -100px;
            animation: float-slow 12s infinite alternate ease-in-out;
        }

        .blob-2 {
            bottom: -150px;
            right: -100px;
            animation: float-slow 18s infinite alternate-reverse ease-in-out;
        }

        @keyframes float-slow {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(50px, 30px) scale(1.1); }
        }

        /* Main Container */
        .container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 540px;
            padding: 20px;
        }

        /* Flag stripe at top of card */
        .flag-stripe {
            height: 4px;
            background: linear-gradient(90deg, #007A5E 33.3%, #F7D000 33.3% 66.6%, #CE1020 66.6%);
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
        }

        /* Maintenance Card */
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

        /* Animated Icon */
        .icon-wrapper {
            margin-bottom: 30px;
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .pulse-ring {
            position: absolute;
            width: 80px;
            height: 80px;
            border: 2px solid var(--accent);
            border-radius: 50%;
            animation: pulse-ring-anim 2.5s infinite cubic-bezier(0.215, 0.61, 0.355, 1);
            opacity: 0;
        }

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
            z-index: 2;
            border: 1px solid rgba(41, 182, 209, 0.2);
            animation: rotate-gear 8s infinite linear;
        }

        @keyframes pulse-ring-anim {
            0% { transform: scale(0.8); opacity: 0.5; }
            50% { opacity: 0.3; }
            100% { transform: scale(1.6); opacity: 0; }
        }

        @keyframes rotate-gear {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Typography */
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

        p {
            font-size: 14px;
            line-height: 1.6;
            color: var(--text-secondary);
            margin-bottom: 30px;
        }

        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.2);
            color: #f59e0b;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            margin-bottom: 24px;
        }

        .status-dot {
            width: 6px;
            height: 6px;
            background-color: #f59e0b;
            border-radius: 50%;
            animation: blink 1.5s infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 1; }
        }

        /* Footer */
        .footer {
            margin-top: 24px;
            font-size: 11px;
            color: var(--text-secondary);
            opacity: 0.6;
        }

        .brand {
            font-weight: 600;
            color: var(--text-primary);
        }
    </style>
</head>
<body>

    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="container">
        <div class="flag-stripe"></div>
        <div class="card">
            <div class="icon-wrapper">
                <div class="pulse-ring"></div>
                <div class="main-icon">
                    <i class="fas fa-gear"></i>
                </div>
            </div>

            <h1>Maintenance en cours</h1>

            <div class="status-badge">
                <span class="status-dot"></span>
                Mise à jour du système
            </div>

            <p>
                Nous effectuons actuellement des opérations de maintenance programmées afin d'améliorer la rapidité et la sécurité de notre plateforme.<br>
                Merci de votre patience, nous serons de retour dans quelques instants.
            </p>
        </div>

        <div class="footer" style="text-align: center;">
            <span class="brand">Pro<span style="color: var(--accent);">Connect</span></span> &copy; {{ date('Y') }}. Tous droits réservés.
        </div>
    </div>

</body>
</html>
