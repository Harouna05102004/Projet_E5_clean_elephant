<?php
/*
 * admin/logout.php — Déconnexion
 * Accepte seulement un POST avec jeton CSRF (bouton « Déconnexion » de l'en-tête).
 * Un lien piégé sur un autre site ne peut donc pas vous déconnecter à votre insu.
 */
require_once __DIR__ . '/../includes/securite.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verifier()) {
    header('Location: index.php');
    exit;
}

$_SESSION = [];
// On supprime aussi le cookie de session dans le navigateur
$p = session_get_cookie_params();
setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
session_destroy();

header('Location: login.php');
exit;