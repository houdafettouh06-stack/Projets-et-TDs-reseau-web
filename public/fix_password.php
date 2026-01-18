<?php
require_once __DIR__ . '/../config/db.php';

try {
    $pdo = Database::connect();
    
    // Hash pour "admin123"
    $newHash = password_hash('admin123', PASSWORD_BCRYPT);
    
    // Mise à jour
    $stmt = $pdo->prepare("UPDATE admins SET password_hash = ? WHERE username = 'admin'");
    $stmt->execute([$newHash]);
    
    echo "<h1>Succès ! ✅</h1>";
    echo "<p>Le mot de passe pour l'utilisateur <strong>admin</strong> est maintenant : <strong>admin123</strong></p>";
    echo "<p><a href='/login'>Aller à la page de connexion</a></p>";

} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
