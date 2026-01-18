<?php
// public/api/upload_csv.php
require_once __DIR__ . '/../../core/Logger.php';

header('Content-Type: application/json');
session_start(); // Nécessaire pour user_id

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Méthode non autorisée');
    }

    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Erreur lors du transfert du fichier');
    }

    $file = $_FILES['file'];
    $maxSize = 10 * 1024 * 1024; // 10 Mo

    // 1. Vérification Taille
    if ($file['size'] > $maxSize) {
        throw new Exception('Le fichier dépasse la limite de 10 Mo.');
    }

    // 2. Vérification Extension
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext !== 'csv') {
        throw new Exception('Seuls les fichiers .csv sont autorisés.');
    }

    // 3. Vérification MIME Type Réel (finfo)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowedMimes = ['text/csv', 'text/plain', 'application/vnd.ms-excel', 'text/x-csv'];
    
    // Fallback pour CSV parfois détecté comme text/plain
    if (!in_array($mime, $allowedMimes)) {
        Logger::log('upload_failed', "Suspicious MIME type: $mime for file " . $file['name'], $_SESSION['admin_username'] ?? 'unknown');
        throw new Exception("Type de fichier invalide ($mime).");
    }

    // 4. Scan Contenu (Basique - recherche de balises PHP/HTML dangereuses)
    $content = file_get_contents($file['tmp_name']);
    if (preg_match('/<\?php|<\?=|script>/i', $content)) {
        Logger::log('security_alert', "Injection attempt detected in CSV: " . $file['name'], $_SESSION['admin_username'] ?? 'unknown');
        throw new Exception('Contenu malveillant détecté dans le fichier.');
    }

    // Sauvegarde sécurisée
    $uploadDir = __DIR__ . '/../../uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    // Nom unique aléatoire pour éviter écrasement et prédiction
    $fileName = 'import_' . date('Ymd_His') . '_' . bin2hex(random_bytes(8)) . '.csv';
    $destPath = $uploadDir . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        throw new Exception('Impossible de sauvegarder le fichier sur le serveur.');
    }

    Logger::log('upload_success', "File uploaded: $fileName", $_SESSION['admin_username'] ?? 'admin');

    echo json_encode(['success' => true, 'filename' => $fileName]);

} catch (Exception $e) {
    Logger::log('upload_error', $e->getMessage(), $_SESSION['admin_username'] ?? 'unknown');
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
