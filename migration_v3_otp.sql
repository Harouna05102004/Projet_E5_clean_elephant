-- Migration v3 : limite d'essais sur les codes de connexion de l'espace client.
-- À exécuter UNE SEULE FOIS (phpMyAdmin > base clean_elephant > onglet SQL, ou en ligne de commande) :
--   mysql -u root -p clean_elephant < migration_v3_otp.sql
--
-- La colonne « tentatives » compte les mauvais codes saisis : au 5e échec,
-- traitement_verification.php désactive le code (protection contre la force brute).

USE clean_elephant;

ALTER TABLE otp_codes
    ADD COLUMN tentatives TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER utilise;
