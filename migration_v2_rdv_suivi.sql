-- Migration v2 : prise de rendez-vous, numéro de suivi, espace client et admin.
-- À exécuter une seule fois sur une base "clean_elephant" déjà existante
-- (créée avec l'ancienne version de schema.sql), pour passer à la nouvelle
-- structure sans perdre les données déjà présentes.
--
-- Utilisation (phpMyAdmin : onglet SQL, ou en ligne de commande) :
--   mysql -u root -p clean_elephant < migration_v2_rdv_suivi.sql

USE clean_elephant;

-- 1. Nouvelles colonnes sur la table devis
ALTER TABLE devis
    ADD COLUMN numero_suivi VARCHAR(20) NULL AFTER id,
    ADD COLUMN date_rdv DATE NULL AFTER message,
    ADD COLUMN heure_rdv VARCHAR(20) NULL AFTER date_rdv,
    ADD COLUMN statut ENUM('nouvelle', 'rdv_confirme', 'devis_envoye', 'accepte', 'refuse') NOT NULL DEFAULT 'nouvelle' AFTER heure_rdv,
    ADD COLUMN montant_devis DECIMAL(10,2) DEFAULT NULL AFTER statut,
    ADD COLUMN fichier_devis VARCHAR(255) DEFAULT NULL AFTER montant_devis,
    ADD COLUMN fichier_facture VARCHAR(255) DEFAULT NULL AFTER fichier_devis;

-- 2. Génère un numéro de suivi pour les demandes déjà existantes (qui n'en ont pas)
--    afin que numero_suivi puisse ensuite devenir UNIQUE sans collision.
UPDATE devis
SET numero_suivi = CONCAT('CE-', UPPER(SUBSTRING(MD5(RAND()), 1, 8)))
WHERE numero_suivi IS NULL;

-- 3. Rend la colonne obligatoire et unique, une fois toutes les lignes remplies
ALTER TABLE devis
    MODIFY COLUMN numero_suivi VARCHAR(20) NOT NULL,
    ADD UNIQUE KEY uniq_numero_suivi (numero_suivi);

-- 4. Table des codes de vérification à usage unique (espace client)
CREATE TABLE IF NOT EXISTS otp_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    devis_id INT NOT NULL,
    code VARCHAR(10) NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expire_at DATETIME NOT NULL,
    utilise TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (devis_id) REFERENCES devis(id) ON DELETE CASCADE
);
