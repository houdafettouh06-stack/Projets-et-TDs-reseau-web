-- Structure de la base de données pour le Projet 2 (Notifications Absences)

CREATE DATABASE IF NOT EXISTS absences_notify CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE absences_notify;

-- 1. Table de configuration des mappings (sauvegarde des préférences)
CREATE TABLE IF NOT EXISTS config_mapping (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_config VARCHAR(100) NOT NULL,
    mapping_json JSON NOT NULL, -- Stocke le tableau associatif { "colonne_csv": "champ_db" }
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Table des absences mensuelles (Table unique pour simplifier selon la demande, ou partitionnée par mois si besoin. Ici on suit "absences_mensuelles")
CREATE TABLE IF NOT EXISTS absences_mensuelles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mois_annee VARCHAR(7) NOT NULL, -- Format 'MM-YYYY' ou 'YYYY-MM'
    nom_etudiant VARCHAR(100) DEFAULT NULL,
    prenom_etudiant VARCHAR(100) DEFAULT NULL,
    classe VARCHAR(50) DEFAULT NULL,
    email_parent VARCHAR(255) DEFAULT NULL,
    telephone_parent VARCHAR(50) DEFAULT NULL,
    date_absence DATE NOT NULL,
    motif VARCHAR(255) DEFAULT NULL,
    
    -- Statuts de notification
    statut_email ENUM('non_n', 'envoye', 'echec') DEFAULT 'non_n',
    statut_whatsapp ENUM('non_n', 'envoye', 'echec') DEFAULT 'non_n',
    
    date_import TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Index pour recherche rapide
    INDEX idx_mois (mois_annee),
    INDEX idx_statut (statut_email, statut_whatsapp)
) ENGINE=InnoDB;

-- 3. Table des templates de messages
CREATE TABLE IF NOT EXISTS templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type_canal ENUM('email', 'whatsapp') NOT NULL,
    nom_template VARCHAR(100) NOT NULL,
    sujet VARCHAR(255) DEFAULT NULL, -- Uniquement pour Email
    corps_message TEXT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB;

-- Données initiales pour les templates
INSERT INTO templates (type_canal, nom_template, sujet, corps_message) VALUES
('email', 'Notification Absence Standard', 'Absence de votre enfant', 
 'Bonjour,\n\nNous vous informons que votre enfant {nom_etudiant} a été marqué(e) absent(e) le {date_absence}.\n\nCordialement,\nL\'administration.'),
('whatsapp', 'Alerte Absence', NULL, 
 'Alerte Absence : {nom_etudiant} était absent(e) le {date_absence}. Merci de nous contacter.');

-- 4. Table de Logs (Audit & Tra�abilit�)
CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(50) DEFAULT 'system',
    action VARCHAR(50) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_action (action),
    INDEX idx_date (created_at)
) ENGINE=InnoDB;
