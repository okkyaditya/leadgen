<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Salestracker - Next-Gen Sales Tracking Platform</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #f59e0b;
            --secondary: #6366f1;
            --text-main: #0f172a;
            --text-muted: #475569;
            --font-display: 'Outfit', sans-serif;
            --font-body: 'Plus Jakarta Sans', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100vh;
            width: 100vw;
            overflow: hidden;
        }

        body {
            background-image: url('/images/login-bg.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            font-family: var(--font-body);
            color: var(--text-main);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        /* Subtle Overlay to enhance readability */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at center, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.4));
            z-index: 1;
            pointer-events: none;
        }

        /* Header Navigation */
        header {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 1.5rem 2rem 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }

        .logo-icon {
            font-size: 1.75rem;
            color: var(--primary);
            background: linear-gradient(135deg, var(--primary), #d97706);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: pulse-logo 2s infinite alternate ease-in-out;
        }

        @keyframes pulse-logo {
            0% { transform: scale(1); }
            100% { transform: scale(1.08); }
        }

        .logo-text {
            font-family: var(--font-display);
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            color: var(--text-main);
        }

        /* Hero Container */
        .hero-section {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 1200px;
            margin: auto auto;
            padding: 0 2rem;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: calc(100% - 140px); /* Adjust to fit completely */
        }

        .hero-badge {
            background: rgba(245, 158, 11, 0.08);
            border: 1px solid rgba(245, 158, 11, 0.25);
            color: #b45309;
            padding: 0.4rem 1rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.25rem;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.05);
            animation: slide-down 0.8s ease-out;
        }

        .hero-badge i {
            animation: spin-badge 4s infinite linear;
        }

        @keyframes spin-badge {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .hero-title {
            font-family: var(--font-display);
            font-size: clamp(2.25rem, 4.5vw, 3.5rem);
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -1.2px;
            margin-bottom: 1rem;
            color: var(--text-main);
            animation: fade-in-up 1s ease-out;
        }

        .hero-title span {
            background: linear-gradient(135deg, var(--primary), #ef4444);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: clamp(0.95rem, 2vw, 1.1rem);
            color: var(--text-muted);
            max-width: 650px;
            line-height: 1.5;
            margin-bottom: 2.5rem;
            animation: fade-in-up 1.2s ease-out;
        }

        /* Action Glassmorphic Cards */
        .cta-container {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
            justify-content: center;
            width: 100%;
            max-width: 840px;
            animation: fade-in-up 1.4s ease-out;
        }

        .cta-card {
            flex: 1;
            min-width: 260px;
            max-width: 380px;
            background: rgba(255, 255, 255, 0.55);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 24px;
            padding: 2rem;
            text-align: left;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .cta-card:hover {
            transform: translateY(-6px);
            background: rgba(255, 255, 255, 0.7);
            border-color: rgba(255, 255, 255, 0.8);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        }

        .cta-card.admin-card:hover {
            box-shadow: 0 20px 40px rgba(245, 158, 11, 0.1);
            border-color: rgba(245, 158, 11, 0.35);
        }

        .cta-card.mitra-card:hover {
            box-shadow: 0 20px 40px rgba(99, 102, 241, 0.1);
            border-color: rgba(99, 102, 241, 0.35);
        }

        .card-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            margin-bottom: 1.25rem;
            transition: all 0.3s ease;
        }

        .admin-card .card-icon {
            background: rgba(245, 158, 11, 0.08);
            color: var(--primary);
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        .mitra-card .card-icon {
            background: rgba(99, 102, 241, 0.08);
            color: var(--secondary);
            border: 1px solid rgba(99, 102, 241, 0.2);
        }

        .cta-card:hover .card-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .card-title {
            font-family: var(--font-display);
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            letter-spacing: -0.3px;
            color: var(--text-main);
        }

        .card-desc {
            color: var(--text-muted);
            font-size: 0.9rem;
            line-height: 1.45;
            margin-bottom: 1.5rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            width: 100%;
            padding: 0.95rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .btn-admin {
            background: linear-gradient(135deg, var(--primary), #d97706);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.15);
        }

        .btn-admin:hover {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            box-shadow: 0 6px 16px rgba(245, 158, 11, 0.3);
            transform: scale(1.02);
        }

        .btn-mitra {
            background: linear-gradient(135deg, var(--secondary), #4f46e5);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
        }

        .btn-mitra:hover {
            background: linear-gradient(135deg, #818cf8, #6366f1);
            box-shadow: 0 6px 16px rgba(99, 102, 241, 0.3);
            transform: scale(1.02);
        }

        /* Footer */
        footer {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 1.5rem;
            color: var(--text-muted);
            font-size: 0.8rem;
            border-top: 1px solid rgba(0, 0, 0, 0.04);
            width: 100%;
        }

        /* Entrance Animations */
        @keyframes slide-down {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes fade-in-up {
            from { transform: translateY(15px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @media (max-height: 720px) {
            header { padding: 1rem 1.5rem 0.25rem; }
            .hero-badge { margin-bottom: 0.75rem; }
            .hero-title { margin-bottom: 0.5rem; }
            .hero-subtitle { margin-bottom: 1.5rem; }
            .cta-card { padding: 1.5rem; }
            .card-desc { margin-bottom: 1rem; }
            footer { padding: 1rem; }
        }

        @media (max-width: 640px) {
            header {
                justify-content: center;
                padding: 1rem;
            }
            .hero-section {
                padding: 1rem;
            }
            .cta-container {
                gap: 1rem;
            }
            .cta-card {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <header>
        <a href="/" class="logo-container">
            <i class="fa-solid fa-chart-line logo-icon"></i>
            <span class="logo-text">Salestracker</span>
        </a>
    </header>

    <!-- Hero Content -->
    <main class="hero-section">
        <div class="hero-badge">
            <i class="fa-solid fa-wand-magic-sparkles"></i>
            Next-Gen Analytics Platform
        </div>
        
        <h1 class="hero-title">
            Maximize Sales, <span>Track Leads</span>,<br>Grow Faster.
        </h1>
        
        <p class="hero-subtitle">
            Salestracker empowers internal sales teams and external partners to collaborate seamlessly, capture high-quality leads, and accelerate commercial growth in one united platform.
        </p>

        <!-- Dynamic Access Portals -->
        <div class="cta-container">
            <!-- Internal Team Card -->
            <div class="cta-card admin-card">
                <div>
                    <div class="card-icon">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>
                    <h3 class="card-title">Internal Team</h3>
                    <p class="card-desc">For Admin, Managers, Supervisors, and Support staff to manage resources, branches, and trace commercial productivity.</p>
                </div>
                <a href="/admin/login" class="btn btn-admin">
                    <span>Enter Internal Portal</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <!-- External Partner Card -->
            <div class="cta-card mitra-card">
                <div>
                    <div class="card-icon">
                        <i class="fa-solid fa-handshake"></i>
                    </div>
                    <h3 class="card-title">External Partner</h3>
                    <p class="card-desc">For registered Mitra/Partners to input hot leads, track referral status, and optimize lead conversions efficiently.</p>
                </div>
                <a href="/mitra/login" class="btn btn-mitra">
                    <span>Enter Partner Portal</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </main>

    <!-- Footer Copyright -->
    <footer>
        <p>&copy; {{ date('Y') }} Salestracker. All rights reserved. Made with <i class="fa-solid fa-heart" style="color: #ef4444;"></i> for high performance sales teams.</p>
    </footer>

</body>
</html>
