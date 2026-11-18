-- Migration v5 : suivi des demandes de rappel et des messages de contact dans l'admin
-- À exécuter UNE SEULE FOIS (phpMyAdmin > base clean_elephant > onglet SQL > coller > Exécuter).

USE clean_elephant;

-- Date à laquelle l'entreprise a traité la demande (NULL = encore à traiter).
-- Une date plutôt qu'un simple oui/non : on sait QUAND c'est fait, et on peut
-- supprimer automatiquement les demandes traitées depuis plus d'un an (RGPD).
ALTER TABLE rappels  ADD COLUMN traite_le DATETIME NULL DEFAULT NULL;
ALTER TABLE contacts ADD COLUMN traite_le DATETIME NULL DEFAULT NULL;
