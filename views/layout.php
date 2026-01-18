<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Absences - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="app-container">
        <aside class="sidebar">
            <div class="brand">
                <div class="logo-icon">🎓</div>
                <h1>Absences<span class="highlight">Pro</span></h1>
            </div>
            <nav class="main-nav">
                <a href="/dashboard" class="nav-item <?php echo $path === '/dashboard' ? 'active' : ''; ?>">
                    <span class="icon">📊</span> Dashboard
                </a>
                <a href="/import" class="nav-item <?php echo ($path === '/import' || $path === '/') ? 'active' : ''; ?>">
                    <span class="icon">📂</span> Import CSV
                </a>
                <a href="/notifications" class="nav-item">
                    <span class="icon">📨</span> Notifications
                </a>
                <a href="/settings" class="nav-item">
                    <span class="icon">⚙️</span> Configuration
                </a>
                <a href="/logout" class="nav-item" style="color: #ff6b6b; margin-top: auto;">
                    <span class="icon">🚪</span> Déconnexion
                </a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="top-bar">
                <div class="breadcrumbs">
                    <span>Accueil</span> / <span class="current"><?php echo htmlspecialchars($pageTitle ?? 'Page'); ?></span>
                </div>
                <div class="user-profile">
                    <div class="avatar">A</div>
                    <span>Administrateur</span>
                </div>
            </header>

            <div class="content-area">
                <!-- VUE INJECTÉE ICI -->
                <?php if (isset($content)) echo $content; ?>
            </div>
        </main>
    </div>

    <script src="/assets/js/app.js"></script>
</body>
</html>
