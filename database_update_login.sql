-- Ajout de la table administrateurs
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insertion d'un admin par défaut (admin / admin123)
-- Le mot de passe doit être hashé en production, mais pour ce setup rapide on le met en dur ou on utilise un script PHP pour générer le hash.
-- Pour simplifier ici, on va insérer un hash connu pour 'admin123' (BCRYPT)
INSERT INTO admins (username, password_hash) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- password: password
