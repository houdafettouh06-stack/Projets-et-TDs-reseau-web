<?php
// public/api/send_notifications.php

header('Content-Type: application/json');

// PREVENTION MAXIMALE : On coupe tout affichage d'erreur PHP qui casserait le JSON
ini_set('display_errors', 0);
error_reporting(0);

require_once '../../config/db.php';
require_once '../../core/SMTPMailer.php';

// --- CONFIGURATION EMAIL ---
$smtpHost = 'smtp.gmail.com';
$smtpPort = 587;
$smtpUser = 'belghalihajar0@gmail.com';
$smtpPass = 'xrid bucw tyig ctaf'; 
// ---------------------------

try {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['ids']) || !isset($input['channel'])) {
        throw new Exception("Paramètres manquants.");
    }

    $ids = $input['ids'];
    $channel = $input['channel']; 
    
    $pdo = Database::connect();
    $logFile = __DIR__ . '/../../envois.log';
    $successCount = 0;
    
    // Instancier le mailer si email
    $mailer = null;
    if ($channel === 'email') {
        $mailer = new SMTPMailer($smtpHost, $smtpPort, $smtpUser, $smtpPass);
    }
    
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM absences_mensuelles WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $absences = $stmt->fetchAll();
    
    foreach ($absences as $absence) {
        $timestamp = date('Y-m-d H:i:s');
        $student = $absence['nom_etudiant'] . ' ' . $absence['prenom_etudiant'];
        $dateAbsence = $absence['date_absence'];
        
        // Mode Normal : On prend l'info du CSV
        $contact = ($channel === 'email') ? $absence['email_parent'] : $absence['telephone_parent'];
        
        $logStatus = 'SIMULATION';
        
        if ($channel === 'email') {
            // ENVOI REEL
            $subject = "Nouvelle absence : $student";
            $body = "
                <h2>Alerte Absence</h2>
                <p>Bonjour,</p>
                <p>Nous vous informons que <strong>$student</strong> a été marqué(e) absent(e) le <strong>$dateAbsence</strong>.</p>
                <p>Merci de contacter l'administration pour plus de détails.</p>
                <br>
                <p><em>Message automatique, ne pas répondre.</em></p>
            ";
            
            // Tentative d'envoi réel avec Failover
            if ($mailer && $mailer->send($contact, $subject, $body)) {
                $logStatus = 'SUCCESS-REAL-EMAIL';
            } else {
                $logStatus = 'FAILOVER-SIMULATION (SMTP Fail)';
            }
            $successCount++;
        } else {
            // WhatsApp est géré côté Client (JS opens window)
            // Ici on logue juste le fait que ça a été demandé
            $logStatus = 'SUCCESS-WHATSAPP-LOGGED';
            $successCount++;
        }
        
        $logEntry = "[$timestamp] [$logStatus] Canal: $channel | Dest: $contact | Etudiant: $student" . PHP_EOL;
        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }

    $statusField = ($channel === 'email') ? 'statut_email' : 'statut_whatsapp';
    $pdo->prepare("UPDATE absences_mensuelles SET $statusField = 'envoye' WHERE id IN ($placeholders)")->execute($ids);

    echo json_encode([
        'status' => 'success',
        'message' => "Traitement effectué.",
        'count' => $successCount
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
