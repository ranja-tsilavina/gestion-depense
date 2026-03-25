<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion de Dépenses</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #3730a3;
            --primary-light: #818cf8;
            --secondary: #10b981;
            --dark: #0f172a;
            --gray: #64748b;
            --light: #f8fafc;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--light);
            color: var(--dark);
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        /* ── Navbar ── */
        .navbar-custom {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }
        
        .brand-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 800;
            font-size: 1.25rem;
            color: var(--dark);
            text-decoration: none;
        }
        
        .brand-icon {
            width: 36px;
            height: 36px;
            background: var(--primary);
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);
        }

        /* ── Buttons ── */
        .btn-custom {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary), #6366f1);
            color: white;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
            border: none;
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
            color: white;
        }
        
        .btn-outline-custom {
            background: rgba(255,255,255,0.8);
            color: var(--dark);
            border: 2px solid #e2e8f0;
        }
        
        .btn-outline-custom:hover {
            border-color: var(--primary-light);
            background: #e0e7ff;
            color: var(--primary-dark);
        }

        /* ── Hero Section ── */
        .hero {
            padding: 160px 0 100px;
            position: relative;
            background: radial-gradient(circle at top right, rgba(99, 102, 241, 0.1), transparent 40%),
                        radial-gradient(circle at bottom left, rgba(16, 185, 129, 0.05), transparent 40%);
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 900;
            line-height: 1.15;
            letter-spacing: -1px;
            color: #0f172a;
            margin-bottom: 1.5rem;
        }

        .hero h1 span {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero p {
            font-size: 1.25rem;
            color: var(--gray);
            margin-bottom: 2.5rem;
            max-width: 600px;
            line-height: 1.6;
        }

        .hero-img-box {
            position: relative;
        }
        
        .hero-img-box::before {
            content: '';
            position: absolute;
            inset: -20px;
            background: linear-gradient(135deg, rgba(79,70,229,0.2), rgba(16,185,129,0.2));
            filter: blur(40px);
            z-index: -1;
            border-radius: 30px;
        }

        .hero-img {
            width: 100%;
            height: auto;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255,255,255,0.5);
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(10px);
            padding: 10px;
        }

        /* ── Features ── */
        .features {
            padding: 5rem 0;
            background: white;
        }
        
        .feature-card {
            background: var(--light);
            border-radius: 24px;
            padding: 2.5rem 2rem;
            height: 100%;
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.03);
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.05);
            background: white;
            border-color: rgba(99,102,241,0.1);
        }

        .feature-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .icon-blue { background: #e0e7ff; color: var(--primary); }
        .icon-green { background: #dcfce7; color: var(--secondary); }
        .icon-orange { background: #ffedd5; color: #f97316; }

        .feature-card h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--dark);
        }

        .feature-card p {
            color: var(--gray);
            line-height: 1.6;
            margin: 0;
        }

        @media (max-width: 991px) {
            .hero h1 { font-size: 2.5rem; }
            .hero { padding: 120px 0 60px; text-align: center; }
            .hero p { margin: 0 auto 2.5rem; }
            .hero-img-box { margin-top: 3rem; }
        }
    </style>
</head>
<body>

    <!-- ── Navbar ── -->
    <nav class="navbar-custom">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="/" class="brand-logo">
                <div class="brand-icon"><i class="bi bi-wallet2"></i></div>
                Gestion de Dépenses
            </a>
            
            <div class="d-flex gap-2">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-custom btn-primary-custom">
                            Mon Tableau de bord <i class="bi bi-arrow-right"></i>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-custom btn-outline-custom">Connexion</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-custom btn-primary-custom d-none d-sm-flex">M'inscrire</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- ── Hero Section ── -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1>Reprenez le contrôle de vos <span>finances personnelles</span></h1>
                    <p>Une application simple, intuitive et gratuite pour suivre vos revenus, gérer vos dépenses et respecter vos budgets mensuels sans prise de tête.</p>
                    
                    <div class="d-flex flex-wrap gap-3 justify-content-lg-start justify-content-center">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn-custom btn-primary-custom" style="padding: 1rem 2rem; font-size: 1.1rem;">
                                Accéder à mon espace <i class="bi bi-arrow-right"></i>
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn-custom btn-primary-custom" style="padding: 1rem 2rem; font-size: 1.1rem;">
                                Commencer gratuitement
                            </a>
                            <a href="{{ route('login') }}" class="btn-custom btn-outline-custom" style="padding: 1rem 2rem; font-size: 1.1rem;">
                                Se connecter
                            </a>
                        @endauth
                    </div>
                    
                    <div class="mt-4 pt-4 d-flex align-items-center gap-4 justify-content-lg-start justify-content-center" style="border-top: 1px solid rgba(0,0,0,0.05); color: var(--gray); font-weight: 500; font-size: 0.9rem;">
                        <div><i class="bi bi-check-circle-fill text-success me-1"></i> 100% Gratuit</div>
                        <div><i class="bi bi-shield-check text-primary me-1"></i> Sécurisé</div>
                        <div><i class="bi bi-phone text-orange me-1"></i> Compatible mobile</div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="hero-img-box">
                        <!-- Abstract illustration representing the app dashboard -->
                        <div class="hero-img">
                            <div style="background:#f8fafc;border-radius:15px;overflow:hidden;border:1px solid #e2e8f0">
                                <!-- Mock Topbar -->
                                <div style="height:50px;background:white;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;padding:0 20px;gap:10px">
                                    <div style="width:12px;height:12px;border-radius:50%;background:#ef4444"></div>
                                    <div style="width:12px;height:12px;border-radius:50%;background:#eab308"></div>
                                    <div style="width:12px;height:12px;border-radius:50%;background:#10b981"></div>
                                    <div style="flex-grow:1"></div>
                                    <div style="width:120px;height:12px;background:#e2e8f0;border-radius:6px"></div>
                                </div>
                                <!-- Mock Content -->
                                <div style="padding:20px;display:flex;gap:20px">
                                    <!-- Sidebar -->
                                    <div style="width:60px;display:flex;flex-direction:column;gap:15px">
                                        <div style="height:12px;background:#e2e8f0;border-radius:4px;width:100%"></div>
                                        <div style="height:12px;background:#e2e8f0;border-radius:4px;width:80%"></div>
                                        <div style="height:12px;background:#e2e8f0;border-radius:4px;width:90%"></div>
                                    </div>
                                    <!-- Cards & Chart -->
                                    <div style="flex-grow:1;display:flex;flex-direction:column;gap:20px">
                                        <div style="display:flex;gap:15px">
                                            <div style="flex:1;background:white;height:80px;border-radius:12px;box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);padding:15px">
                                                <div style="width:30px;height:30px;border-radius:8px;background:#dcfce7;margin-bottom:10px"></div>
                                                <div style="width:80%;height:8px;background:#e2e8f0;border-radius:4px"></div>
                                            </div>
                                            <div style="flex:1;background:white;height:80px;border-radius:12px;box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);padding:15px">
                                                <div style="width:30px;height:30px;border-radius:8px;background:#fef2f2;margin-bottom:10px"></div>
                                                <div style="width:60%;height:8px;background:#e2e8f0;border-radius:4px"></div>
                                            </div>
                                        </div>
                                        <div style="background:white;height:150px;border-radius:12px;box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);padding:20px;display:flex;align-items:flex-end;justify-content:space-around">
                                            <div style="width:30px;height:60%;background:#818cf8;border-radius:4px 4px 0 0"></div>
                                            <div style="width:30px;height:40%;background:#f87171;border-radius:4px 4px 0 0"></div>
                                            <div style="width:30px;height:80%;background:#818cf8;border-radius:4px 4px 0 0"></div>
                                            <div style="width:30px;height:50%;background:#f87171;border-radius:4px 4px 0 0"></div>
                                            <div style="width:30px;height:90%;background:#818cf8;border-radius:4px 4px 0 0"></div>
                                            <div style="width:30px;height:20%;background:#f87171;border-radius:4px 4px 0 0"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── Features Section ── -->
    <section class="features">
        <div class="container">
            <div class="text-center mb-5 pb-2">
                <h2 style="font-weight:800;font-size:2.5rem;color:var(--dark)">Tout ce dont vous avez besoin</h2>
                <p style="color:var(--gray);font-size:1.15rem;max-width:600px;margin:1rem auto 0">Découvrez comment notre application peut vous aider à atteindre vos objectifs financiers.</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon icon-blue">
                            <i class="bi bi-pie-chart-fill"></i>
                        </div>
                        <h3>Suivi des dépenses</h3>
                        <p>Visualisez exactement où va votre argent grâce à des graphiques clairs et une catégorisation automatique de vos transactions.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon icon-orange">
                            <i class="bi bi-bullseye"></i>
                        </div>
                        <h3>Gestion du budget</h3>
                        <p>Définissez des limites de dépenses mensuelles par catégorie et recevez des alertes avant de dépasser votre budget.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon icon-green">
                            <i class="bi bi-wallet-fill"></i>
                        </div>
                        <h3>Analyse des revenus</h3>
                        <p>Suivez toutes vos sources de revenus pour connaître instantanément votre solde actuel et votre capacité d'épargne.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer style="background:var(--dark);padding:2rem 0;text-align:center;color:#94a3b8;font-size:0.95rem">
        <div class="container">
            &copy; {{ date('Y') }} Gestion de Dépenses. Tous droits réservés.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
