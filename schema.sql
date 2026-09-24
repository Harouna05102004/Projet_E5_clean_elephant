-- Schéma de base de données pour Clean Elephant
-- Reconstruit à partir des requêtes préparées de traitement_devis.php,
-- traitement_contact.php et traitement_rappel.php.
-- À adapter si ta base existante diffère légèrement.

CREATE DATABASE IF NOT EXISTS clean_elephant CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE clean_elephant;

-- Demandes de devis (formulaire devis.php)
CREATE TABLE IF NOT EXISTS devis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    email VARCHAR(150) NOT NULL,
    adresse VARCHAR(255),
    ville VARCHAR(100),
    type_nettoyage VARCHAR(255),
    message TEXT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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
