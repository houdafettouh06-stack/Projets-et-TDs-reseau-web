<?php
// public/api/save_import.php

header('Content-Type: application/json');
require_once '../../config/db.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['temp_file']) || !isset($input['mapping'])) {
        throw new Exception("Données manquantes (fichier ou mapping).");
    }

    $tempFile = __DIR__ . '/../../uploads/temp/' . basename($input['temp_file']);
    $mapping = $input['mapping'];
    
    if (!file_exists($tempFile)) {
        throw new Exception("Fichier temporaire expiré ou introuvable.");
    }

    $pdo = Database::connect();
    
    $handle = fopen($tempFile, "r");
    fgetcsv($handle); // Skip header
    
    $importedCount = 0;
    $dbFields = array_values($mapping);
    $csvIndices = array_keys($mapping);
    
    $now = date('Y-m-d H:i:s');
    $currentMonth = date('Y-m'); 
    
    $sqlFields = implode(', ', $dbFields) . ", mois_annee, date_import";
    $sqlPlaceholders = implode(', ', array_fill(0, count($dbFields), '?')) . ", ?, ?";
    
    $sql = "INSERT INTO absences_mensuelles ($sqlFields) VALUES ($sqlPlaceholders)";
    $stmt = $pdo->prepare($sql);
    
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $values = [];
        foreach ($csvIndices as $index) {
            $values[] = isset($data[$index]) ? trim($data[$index]) : null;
            // Hack pour forcer les données de test si l'utilisateur utilisait le CSV précédent avant la mise à jour, 
            // mais ici on compte sur le fait qu'il va réimporter le nouveau CSV.
            // On laisse tel quel, le CSV est source de vérité.
        }
        $values[] = $currentMonth;
        $values[] = $now;
        
        try {
            $stmt->execute($values);
            $importedCount++;
        } catch (Exception $e) { }
    }
    
    fclose($handle);
    unlink($tempFile);
    
    echo json_encode([
        'status' => 'success',
        'message' => "$importedCount absences importées avec succès.",
        'count' => $importedCount
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
