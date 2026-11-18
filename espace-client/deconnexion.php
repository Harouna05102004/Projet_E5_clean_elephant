<?php
/*
 * espace-client/deconnexion.php — Déconnexion de l'espace client
 * On vide la session, on supprime le cookie de session dans le navigateur,
 * puis on détruit la session côté serveur.
 */
require_once __DIR__ . '/../includes/securite.php';

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();

header('Location: index.php?deconnecte=1');
exit;