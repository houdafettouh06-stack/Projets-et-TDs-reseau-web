<?php
// api/send_notifications.php

header('Content-Type: application/json');
require_once '../config/db.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['ids']) || !isset($input['channel'])) {
        throw new Exception("Paramètres manquants.");
    }

    $ids = $input['ids'];
    $channel = $input['channel']; // 'email' ou 'whatsapp'
    
    if (!in_array($channel, ['email', 'whatsapp'])) {
        throw new Exception("Canal invalide.");
    }

    $pdo = Database::connect();
    
    // Log file path
    $logFile = __DIR__ . '/../envois.log';
    
    $successCount = 0;
    
    // On récupère les infos pour le log
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM absences_mensuelles WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $absences = $stmt->fetchAll();
    
    foreach ($absences as $absence) {
        // Simulation d'envoi
        $timestamp = date('Y-m-d H:i:s');
        $student = $absence['nom_etudiant'] . ' ' . $absence['prenom_etudiant'];
        $contact = ($channel === 'email') ? $absence['email_parent'] : $absence['telephone_parent'];
        
        $logEntry = "[$timestamp] [SUCCESS] Canal: $channel | Dest: $contact | Etudiant: $student | Msg: Notification absence du {$absence['date_absence']}" . PHP_EOL;
        
        // Écriture dans le log
        file_put_contents($logFile, $logEntry, FILE_APPEND);
        
        $successCount++;
    }

    // Mise à jour des statuts en base
    $statusField = ($channel === 'email') ? 'statut_email' : 'statut_whatsapp';
    $updateSql = "UPDATE absences_mensuelles SET $statusField = 'envoye' WHERE id IN ($placeholders)";
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->execute($ids);

    echo json_encode([
        'status' => 'success',
        'message' => "Envois effectués.",
        'count' => $successCount
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
