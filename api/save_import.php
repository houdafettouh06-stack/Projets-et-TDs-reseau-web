<?php
// api/save_import.php

header('Content-Type: application/json');
require_once '../config/db.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['temp_file']) || !isset($input['mapping'])) {
        throw new Exception("Données manquantes (fichier ou mapping).");
    }

    $tempFile = __DIR__ . '/../uploads/temp/' . basename($input['temp_file']);
    $mapping = $input['mapping']; // array [csv_index => db_field]
    
    if (!file_exists($tempFile)) {
        throw new Exception("Fichier temporaire expiré ou introuvable.");
    }

    // Connexion DB
    $pdo = Database::connect();
    
    // Lecture du CSV
    $handle = fopen($tempFile, "r");
    // Skip headers (on suppose qu'il y en a toujours vu le step précédent)
    fgetcsv($handle); 
    // TODO: Gérer le délimiteur dynamique détecté précédemment ?
    // Pour simplifier ici on assume standard, ou on devrait le passer en paramètre.
    // Idéalement on stocke la config de détection en session ou dans le fichier temp metadata.
    
    // Pour l'exercice: on tente de redétecter ou on utilise ','
    
    $importedCount = 0;
    
    // Préparation de la requête
    // On construit dynamiquement la requête INSERT selon les champs mappés
    // db_fields: nom_etudiant, prenom_etudiant, date_absence, etc.
    
    $columns = [];
    $params = [];
    
    // On doit préparer une requête générique, mais les valeurs changent à chaque ligne.
    // L'astuce est de mapper l'index CSV vers le nom de colonne DB.
    
    $stmt = null; // On préparera dans la boucle ou on fait du dynamique
    
    // Pour optimiser, on construit la structure SQL une fois.
    // Les clés de $mapping sont les indices CSV (ex: 0, 1, 4). Les valeurs sont les colonnes DB.
    $dbFields = array_values($mapping);
    $csvIndices = array_keys($mapping);
    
    // Ajout champs fixes
    $now = date('Y-m-d H:i:s');
    $currentMonth = date('Y-m'); // Par défaut mois courant pour l'import
    
    $sqlFields = implode(', ', $dbFields) . ", mois_annee, date_import";
    $sqlPlaceholders = implode(', ', array_fill(0, count($dbFields), '?')) . ", ?, ?";
    
    $sql = "INSERT INTO absences_mensuelles ($sqlFields) VALUES ($sqlPlaceholders)";
    $stmt = $pdo->prepare($sql);
    
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        // Construction du tableau de valeurs pour l'exécution
        $values = [];
        foreach ($csvIndices as $index) {
            $values[] = isset($data[$index]) ? trim($data[$index]) : null;
        }
        
        // Ajout des valeurs fixes
        $values[] = $currentMonth;
        $values[] = $now;
        
        try {
            $stmt->execute($values);
            $importedCount++;
        } catch (Exception $e) {
            // Log erreur ligne, on continue
            // error_log("Erreur import ligne : " . $e->getMessage());
        }
    }
    
    fclose($handle);
    unlink($tempFile); // Nettoyage
    
    echo json_encode([
        'status' => 'success',
        'message' => "$importedCount absences importées avec succès pour le mois $currentMonth.",
        'count' => $importedCount
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
