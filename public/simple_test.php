<?php
// public/simple_test.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../core/SMTPMailer.php';

// IDENTIFIANTS
$user = 'belghalihajar0@gmail.com';
$pass = 'xrid bucw tyig ctaf'; 

// Récupérer une autre adresse si spécifiée dans l'URL (?to=autre@mail.com)
$to = $_GET['to'] ?? $user;

echo "<h1>Test de dépannage Email</h1>";

try {
    $mailer = new SMTPMailer('smtp.gmail.com', 587, $user, $pass);
    
    $time = date('H:i:s');
    $subject = "Preuve de fonctionnement ($time)";
    $body = "Si vous recevez ce message sur $to, c'est que le systeme d'absence fonctionne parfaitement !";
    
    echo "<p>Tentative d'envoi de <strong>$user</strong> vers <strong>$to</strong> (Sujet: $subject)...</p>";
    
    if ($mailer->send($to, $subject, $body)) {
        echo "<h2 style='color:green'>✅ Email accepté par Google !</h2>";
        echo "<ul>";
        echo "<li><strong>Regardez dans 'Envoyés' (Sent)</strong> : Comme vous vous écrivez à vous-même, Gmail le classe souvent là directement.</li>";
        echo "<li>Regardez dans 'Spam' (Indésirables).</li>";
        echo "<li>Regardez dans l'onglet 'Promotions' ou 'Réseaux sociaux'.</li>";
        echo "</ul>";
    } else {
        echo "<h2 style='color:red'>❌ Échec technique au niveau SMTP.</h2>";
    }

} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage();
}
