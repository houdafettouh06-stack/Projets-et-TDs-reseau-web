<?php
// core/Logger.php
require_once __DIR__ . '/../config/db.php';

class Logger {
    public static function log($action, $details = '', $userId = 'admin') {
        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
            $stmt->execute([$userId, $action, $details, $ip]);
        } catch (Exception $e) {
            // Silencieux en cas d'erreur de log pour ne pas bloquer l'app
            error_log("Audit Log Error: " . $e->getMessage());
        }
    }
}
