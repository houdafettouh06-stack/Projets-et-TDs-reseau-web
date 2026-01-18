<?php
// api/upload_csv.php

header('Content-Type: application/json');
require_once '../core/CSVSmartImporter.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Méthode non autorisée.");
    }

    if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Erreur lors de l'upload du fichier.");
    }

    $fileTmpPath = $_FILES['csv_file']['tmp_name'];
    $fileName = $_FILES['csv_file']['name'];
    $fileSize = $_FILES['csv_file']['size'];
    $fileType = $_FILES['csv_file']['type'];

    // Vérification extension
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if ($fileExtension !== 'csv') {
        throw new Exception("Format de fichier invalide. Veuillez uploader un fichier .csv");
    }

    // Déplacer le fichier vers un dossier temporaire pour traitement (ou traiter directement tmp)
    // Ici on traite directement le tmp pour l'analyse
    
    $importer = new CSVSmartImporter();
    
    // 1. Analyse Headers
    $headers = $importer->analyzeHeaders($fileTmpPath);
    
    // 2. Détection Mapping
    $mapping = $importer->detectMapping($headers);
    
    // 3. Sauvegarder le fichier temporairement pour l'étape suivante
    $tempDir = __DIR__ . '/../uploads/temp/';
    if (!is_dir($tempDir)) mkdir($tempDir, 0777, true);
    
    $tempFilename = uniqid('import_') . '.csv';
    $destination = $tempDir . $tempFilename;
    
    if (!move_uploaded_file($fileTmpPath, $destination)) {
        throw new Exception("Impossible de sauvegarder le fichier temporaire.");
    }

    // Réponse JSON
    echo json_encode([
        'status' => 'success',
        'message' => 'Fichier analysé avec succès.',
        'data' => [
            'headers_count' => count($headers),
            'mapping_suggestions' => $mapping,
            'temp_filename' => $tempFilename
        ]
    ]);
    
    // TODO: Dans une vraie app, déplacer le fichier dans /uploads/temp_uniqid.csv et renvoyer l'ID au front
    // pour que l'etape "Valider" sache quel fichier traiter.

} catch (Exception $e) {
    http_response_code(400); // Bad Request
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
