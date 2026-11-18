<?php
/*
 * admin/includes/auth.php — Le « vigile » de l'espace admin
 *
 * Inclus EN PREMIER par chaque page admin (sauf login.php et traitement_login.php).
 * Rôle :
 *   1. démarrer la session de façon sûre (via securite.php) ;
 *   2. renvoyer vers login.php si l'admin n'est pas connecté ;
 *   3. déconnecter après 30 minutes sans activité ;
 *   4. envoyer des en-têtes HTTP qui protègent les pages admin ;
 *   5. fournir la liste des statuts (une seule source pour toutes les pages).
 */
require_once __DIR__ . '/../../includes/securite.php';

// --- 1. En-têtes de sécurité (avant tout affichage) -------------------------
header('X-Frame-Options: DENY');              // la page ne peut pas être affichée dans une <iframe> d'un autre site (anti-clickjacking)
header('X-Content-Type-Options: nosniff');    // le navigateur ne « devine » pas le type des fichiers
header('Referrer-Policy: same-origin');       // l'adresse de l'admin n'est jamais transmise à un autre site
header('Cache-Control: no-store');            // pas de copie des pages admin dans le cache (ordinateur partagé)
header('X-Robots-Tag: noindex, nofollow');    // Google ne doit pas référencer l'admin

// --- 2. Connecté ? -----------------------------------------------------------
if (empty($_SESSION['admin_connecte'])) {
    header('Location: login.php');
    exit;
}

// --- 3. Inactivité ----------------------------------------------------------
const ADMIN_INACTIVITE_MAX = 30 * 60; // 30 minutes, en secondes

if (isset($_SESSION['admin_derniere_activite'])
    && time() - $_SESSION['admin_derniere_activite'] > ADMIN_INACTIVITE_MAX) {
    $_SESSION = [];
    session_destroy();
    header('Location: login.php?expire=1');
    exit;
}
$_SESSION['admin_derniere_activite'] = time();

// --- 4. Statuts : clé en base => libellé affiché ---------------------------
const ADMIN_STATUTS = [
    'nouvelle'     => 'Nouvelle demande',
    'rdv_confirme' => 'Rendez-vous confirmé',
    'devis_envoye' => 'Devis envoyé',
    'accepte'      => 'Devis accepté',
    'refuse'       => 'Devis refusé',
    'planifie'     => 'Intervention planifiée',
    'termine'      => 'Intervention réalisée',
];

/** Pastille de statut prête à afficher (la clé vient de notre liste, jamais de l'URL). */
function admin_badge(string $statut): string
{
    $cle = array_key_exists($statut, ADMIN_STATUTS) ? $statut : 'nouvelle';
    return '<span class="statut statut--' . $cle . '">' . e(ADMIN_STATUTS[$cle]) . '</span>';
}