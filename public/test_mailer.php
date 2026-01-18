<?php
// public/test_mailer.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../core/SMTPMailer.php';

echo "<h1>Test de Configuration SMTP</h1>";

// 1. Vérification OpenSSL
if (!extension_loaded('openssl')) {
    echo "<p style='color:red'>❌ L'extension PHP <strong>OpenSSL</strong> n'est pas activée !</p>";
    echo "<p>Sans OpenSSL, impossible de se connecter à Gmail (bouton SSL/TLS).</p>";
    echo "<p><strong>Solution :</strong> Ouvrez votre php.ini et décommentez ;extension=openssl</p>";
    exit;
} else {
    echo "<p style='color:green'>✅ Extension OpenSSL activée.</p>";
}

// 2. Récupération des infos depuis le fichier (un peu brut mais efficace pour débugger)
$content = file_get_contents('../public/api/send_notifications.php'); // Chemin ajusté
preg_match('/\$smtpUser = \'(.*?)\'/', $content, $uMatches);
preg_match('/\$smtpPass = \'(.*?)\'/', $content, $pMatches);

$user = $uMatches[1] ?? 'Inconnu';
$pass = $pMatches[1] ?? 'Inconnu';

echo "<p><strong>Utilisateur détecté :</strong> $user</p>";
echo "<p><strong>Mot de passe détecté :</strong> " . (strlen($pass) > 20 ? substr($pass, 0, 5) . '...' : $pass) . "</p>";

if (strpos($pass, 'VOTRE_MOT_DE_PASSE') !== false) {
    echo "<p style='color:red'>❌ Erreur : Vous n'avez pas encore remplacé le mot de passe par défaut !</p>";
    exit;
}

// 3. Test de connexion
echo "<h3>Tentative de connexion à smtp.gmail.com:587...</h3>";

try {
    $mailer = new SMTPMailer('smtp.gmail.com', 587, $user, $pass);
    echo "Connexion ouverte...<br>";
    echo "Envoi d'un mail de test à $user...<br>";
    
    if ($mailer->send($user, 'Test AbsencesPro', 'Ceci est un test réussi !')) {
        echo "<h2 style='color:green'>✅ SUCCÈS ! L'email a été envoyé.</h2>";
    } else {
        echo "<h2 style='color:red'>❌ ÉCHEC de l'envoi. Vérifiez les logs PHP.</h2>";
    }

} catch (Exception $e) {
    echo "<h2 style='color:red'>❌ Erreur Exception : " . $e->getMessage() . "</h2>";
}
