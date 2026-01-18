<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Administration - UEMF</title>
    <!-- Import des belles polices Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- On garde le style global mais on surcharge pour le login -->
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            /* IMAGE DE FOND UEMF (Final V4 - Definitive) */
            background: url('/assets/img/uemf_login_final_v4.jpg') no-repeat center center fixed; 
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Outfit', sans-serif; /* Police principale plus moderne */
            overflow: hidden;
        }

        .login-container {
            /* EFFET FROSTED GLASS (Verre Dépoli) demandé */
            background: rgba(255, 255, 255, 0.75); /* Un peu plus opaque pour la lisibilité */
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 24px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15); /* Ombre plus douce et diffuse */
            
            padding: 3rem 2.5rem;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-header h2 {
            margin-bottom: 2rem;
            font-family: 'Montserrat', sans-serif; /* Titre plus impactant */
            font-weight: 600;
            color: #1a4f36; /* Vert très sombre et élégant */
            text-transform: none;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            opacity: 0.8;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
            color: #4a5568;
            font-weight: 500;
            font-family: 'Montserrat', sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding-left: 5px;
        }

        .form-control {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid #e2e8f0; /* Bordure douce */
            background: #f7fafc; /* Fond très légèrement gris */
            border-radius: 16px; /* Arrondis modernes */
            outline: none;
            transition: all 0.2s ease;
            font-size: 1rem;
            color: #2d3748;
            box-sizing: border-box;
            box-shadow: none; /* Flat style */
        }
        
        .form-control::placeholder {
            color: #a0aec0;
        }
        
        .form-control:focus {
            background: #ffffff;
            border-color: #43A047; /* Vert AHNH au focus */
            box-shadow: 0 0 0 4px rgba(67, 160, 71, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 16px; /* Match inputs */
            /* Gradient Vert AHNH */
            background: linear-gradient(135deg, #43A047 0%, #2E7D32 100%);
            color: white;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 1rem;
            box-shadow: 0 10px 20px rgba(67, 160, 71, 0.25);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(67, 160, 71, 0.35);
        }

        .error-message {
            background: #fff5f5;
            color: #c53030;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-size: 0.95rem;
            display: <?php echo isset($error) ? 'block' : 'none'; ?>;
            border: 1px solid #feb2b2;
            border-left: none; /* Clean style */
        }
        
        .footer-text {
            margin-top: 25px;
            font-size: 0.85rem;
            color: #718096;
            font-weight: 500;
        }
        .logo-wrapper {
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .logo-pill-img {
            max-width: 250px; /* Taille légèrement réduite pour l'élégance */
            height: auto;
            border-radius: 50px;
            /* Suppression de l'ombre CSS forcée pour laisser l'image gérer sa propre profondeur ou transparence */
            display: inline-block;
            transition: transform 0.3s;
        }

        .logo-pill-img:hover {
            transform: scale(1.02);
        }
    </style>
</head>
<body>
    


    <div class="login-container">
        <div class="login-header">
            <!-- Logo AHNH Custom -->
            <div style="display: flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: 1.5rem;">
                <div style="background: linear-gradient(135deg, #43A047 0%, #2E7D32 100%); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(67, 160, 71, 0.3);">
                    <!-- Icone Calendrier SVG Blanc -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                        <path d="M8 14h.01"></path>
                        <path d="M12 14h.01"></path>
                        <path d="M16 14h.01"></path>
                        <path d="M8 18h.01"></path>
                        <path d="M12 18h.01"></path>
                        <path d="M16 18h.01"></path>
                    </svg>
                </div>
                <h1 style="font-family: 'Montserrat', sans-serif; font-size: 2.4rem; font-weight: 800; color: #2E7D32; margin: 0; letter-spacing: -1px; text-shadow: 0 2px 10px rgba(46, 125, 50, 0.2);">AHNH</h1>
            </div>
            
            <h2>Portail d'Administration</h2>
        </div>

        <?php if(isset($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="/login">
            <div class="form-group">
                <label>Identifiant Administrateur</label>
                <input type="text" name="username" class="form-control" placeholder="Entrez votre identifiant" required>
            </div>

            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password" class="form-control" placeholder="Entrez votre mot de passe" required>
            </div>

            <button type="submit" class="btn-login">Connexion</button>
            
            <div class="footer-text">
                Portail réservé au personnel administratif de l'UEMF.
            </div>
        </form>
    </div>

</body>
</html>
