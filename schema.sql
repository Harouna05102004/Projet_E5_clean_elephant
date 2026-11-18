-- Schéma de base de données pour Clean Elephant
-- Reconstruit à partir des requêtes préparées de traitement_devis.php,
-- traitement_contact.php, traitement_rappel.php, et de l'espace admin / espace client.
-- À adapter si ta base existante diffère légèrement.
-- Pour mettre à jour une base déjà existante (sans perdre les données), utilise
-- plutôt migration_v2_rdv_suivi.sql.

CREATE DATABASE IF NOT EXISTS clean_elephant CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE clean_elephant;

-- Demandes de devis (formulaire devis.php)
-- Depuis la v2 : le formulaire ne demande plus un devis direct, mais une prise de
-- rendez-vous (date_rdv / heure_rdv) pour qu'un technicien établisse le devis.
-- Chaque demande reçoit un numéro de suivi unique (numero_suivi), utilisé par le
-- client pour se connecter à son espace (espace-client/).
CREATE TABLE IF NOT EXISTS devis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_suivi VARCHAR(20) NOT NULL UNIQUE,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    email VARCHAR(150) NOT NULL,
    adresse VARCHAR(255),
    ville VARCHAR(100),
    type_nettoyage VARCHAR(255),
    message TEXT,
    date_rdv DATE,
    heure_rdv VARCHAR(20),
    statut ENUM('nouvelle', 'rdv_confirme', 'devis_envoye', 'accepte', 'refuse') NOT NULL DEFAULT 'nouvelle',
    montant_devis DECIMAL(10,2) DEFAULT NULL,
    fichier_devis VARCHAR(255) DEFAULT NULL,
    fichier_facture VARCHAR(255) DEFAULT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Codes de vérification à usage unique (connexion à l'espace client par email)
CREATE TABLE IF NOT EXISTS otp_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    devis_id INT NOT NULL,
    code VARCHAR(10) NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expire_at DATETIME NOT NULL,
    utilise TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (devis_id) REFERENCES devis(id) ON DELETE CASCADE
);

-- Messages du formulaire de contact (contact.php)
CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prenom VARCHAR(100) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    telephone VARCHAR(20),
    message TEXT NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Demandes de rappel gratuit (formulaire du footer, présent sur toutes les pages)
CREATE TABLE IF NOT EXISTS rappels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
