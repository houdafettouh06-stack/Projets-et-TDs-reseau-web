<?php
// public/index.php

// Autoloader manuel (ou include des fichiers nécessaires)
require_once __DIR__ . '/../config/db.php';

// Récupération de l'URL
$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);

session_start();

// Routing basique
$view = null;
$pageTitle = 'AbsencesPro';

// Gestion de la Deconnexion
if ($path === '/logout') {
    session_destroy();
    header('Location: /login');
    exit;
}

// Gestion du POST Login
if ($path === '/login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $pdo = Database::connect();
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $user['username'];
        header('Location: /import'); // Redirection vers l'import (début du flux)
        exit;
    } else {
        $error = "Identifiants incorrects.";
    }
}

// Protection des routes (Middleware simple)
$protectedRoutes = ['/import', '/notifications', '/settings'];
// Si on essaie d'accéder à une page protégée sans être connecté
if (in_array($path, $protectedRoutes) || $path === '/') {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        // Redirection vers login
        header('Location: /login');
        exit;
    }
}

switch ($path) {
    case '/login':
        $pageTitle = 'Connexion Administration';
        // Si déjà connecté, on redirige vers l'accueil
        if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
            header('Location: /dashboard');
            exit;
        }
        // Affichage vue login (sans le layout principal pour avoir le full screen bg)
        include __DIR__ . '/../views/login.php';
        exit; // On arrête ici pour ne pas charger le layout standard

    case '/':
        header('Location: /dashboard');
        exit;

    case '/dashboard':
        $pageTitle = 'Tableau de Bord';
        $pdo = Database::connect();
        
        // 1. Total Absences
        $stmt = $pdo->query("SELECT COUNT(*) FROM absences_mensuelles");
        $totalAbsences = $stmt->fetchColumn();

        // 2. Notifications Envoyées (Email ou WhatsApp)
        $stmt = $pdo->query("SELECT COUNT(*) FROM absences_mensuelles WHERE statut_email = 'envoye' OR statut_whatsapp = 'envoye'");
        $notifsSent = $stmt->fetchColumn();

        // 3. Stats par Classe
        $stmt = $pdo->query("SELECT classe as label, COUNT(*) as count FROM absences_mensuelles GROUP BY classe");
        $statsClasse = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 4. Stats par Justification
        $stmt = $pdo->query("SELECT motif as label, COUNT(*) as count FROM absences_mensuelles GROUP BY motif");
        $statsReason = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../views/dashboard.php';
        $content = ob_get_clean();
        break;

    case '/import':
        $pageTitle = 'Import CSV';
        ob_start();
        include __DIR__ . '/../views/import.php';
        $content = ob_get_clean();
        break;

    case '/notifications':
        $pageTitle = 'Gestion des Notifications';
        $pdo = Database::connect();
        $stmt = $pdo->query("SELECT * FROM absences_mensuelles ORDER BY date_import DESC, date_absence DESC LIMIT 100");
        $absences = $stmt->fetchAll();
        ob_start();
        include __DIR__ . '/../views/notifications.php';
        $content = ob_get_clean();
        break;

    case '/settings':
        $pageTitle = 'Configuration';
        ob_start();
        include __DIR__ . '/../views/settings.php';
        $content = ob_get_clean();
        break;

    default:
        http_response_code(404);
        ob_start();
        echo "<h2>404 - Page non trouvée</h2>";
        $content = ob_get_clean();
        break;
}

// Inclusion du layout principal
require_once __DIR__ . '/../views/layout.php';
