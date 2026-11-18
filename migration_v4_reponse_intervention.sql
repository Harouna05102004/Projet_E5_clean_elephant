-- Migration v4 : réponse du client en ligne + planification de l'intervention
-- À exécuter UNE SEULE FOIS (phpMyAdmin > base clean_elephant > onglet SQL > coller > Exécuter).
-- Les données existantes sont conservées.

USE clean_elephant;

ALTER TABLE devis
    -- 1. Deux nouveaux statuts, à la fin du parcours :
    --    planifie = l'intervention a une date ; termine = l'intervention est faite
    MODIFY COLUMN statut ENUM('nouvelle', 'rdv_confirme', 'devis_envoye', 'accepte', 'refuse', 'planifie', 'termine')
        NOT NULL DEFAULT 'nouvelle',

    -- 2. Trace de la réponse du client au devis (preuve du « bon pour accord »)
    ADD COLUMN date_reponse DATETIME NULL DEFAULT NULL AFTER fichier_facture,  -- quand il a répondu
    ADD COLUMN ip_reponse VARCHAR(45) NULL DEFAULT NULL AFTER date_reponse,    -- depuis quelle adresse IP (45 = taille max d'une IPv6)
    ADD COLUMN motif_refus VARCHAR(500) NULL DEFAULT NULL AFTER ip_reponse,    -- raison facultative en cas de refus

    -- 3. Date et créneau de l'intervention, fixés par l'admin
    ADD COLUMN date_intervention DATE NULL DEFAULT NULL AFTER motif_refus,
    ADD COLUMN heure_intervention VARCHAR(20) NULL DEFAULT NULL AFTER date_intervention;
