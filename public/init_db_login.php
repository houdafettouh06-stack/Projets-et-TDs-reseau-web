<?php
require_once __DIR__ . '/../config/db.php';

try {
    $pdo = Database::connect();
    $sql = file_get_contents(__DIR__ . '/../database_update_login.sql');
    
    // Split SQL by semicolon in case of multiple statements
    $pdo->exec($sql);
    
    echo "Mise a jour base de donnees OK (Table admins creee).";

} catch (Exception $e) {
    echo "Erreur SQL : " . $e->getMessage();
}
