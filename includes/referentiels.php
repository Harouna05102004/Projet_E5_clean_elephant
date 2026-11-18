<?php
/*
 * includes/referentiels.php — Les listes de valeurs autorisées
 *
 * Une seule source de vérité : devis.php s'en sert pour AFFICHER les choix,
 * traitement_devis.php s'en sert pour VÉRIFIER que la valeur reçue en fait partie.
 * Pour ajouter un type de nettoyage ou un créneau, on ne modifie qu'ici.
 */

// clé courte (envoyée par le formulaire et utilisée dans les liens devis.php?type=...)
//   => libellé affiché et enregistré en base
const TYPES_NETTOYAGE = [
    'appartement' => 'Appartement',
    'maison'      => 'Maison',
    'veranda'     => 'Véranda',
    'vitrine'     => 'Vitrine commerciale',
    'hauteur'     => 'Grande hauteur',
    'facade'      => 'Façades',
    'bureaux'     => 'Bureaux',
    'immeuble'    => 'Immeuble',
    'copropriete' => 'Copropriété',
    'contrat'     => "Contrat d'entretien",
];

const CRENEAUX = ['9h - 11h', '11h - 13h', '14h - 16h', '16h - 18h'];

// On ne prend pas de rendez-vous à plus de 3 mois
const DELAI_MAX_RDV_JOURS = 90;

// Pour quel profil chaque prestation est proposée : 'particulier', 'pro' ou 'tous'.
// Sert au filtre « Vous êtes » du formulaire de devis (affichage uniquement).
const PROFIL_PAR_TYPE = [
    'appartement' => 'particulier',
    'maison'      => 'particulier',
    'veranda'     => 'particulier',
    'facade'      => 'particulier',
    'vitrine'     => 'tous',
    'hauteur'     => 'tous',
    'bureaux'     => 'pro',
    'immeuble'    => 'pro',
    'copropriete' => 'pro',
    'contrat'     => 'pro',
];