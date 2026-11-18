<?php
// Vérifie le mot de passe admin et ouvre la session
session_start();
require '../env.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    // password_verify compare le mot de passe saisi au hash stocké dans env.php
    // (on ne stocke jamais le mot de passe en clair, même ici).
    if (password_verify($mot_de_passe, ADMIN_PASSWORD_HASH)) {
        // On régénère l'identifiant de session à la connexion : bonne pratique
        // qui empêche la réutilisation d'un ancien identifiant de session.
        session_regenerate_id(true);
        $_SESSION['admin_connecte'] = true;
        header('Location: index.php');
        exit;
    }

    header('Location: login.php?erreur=1');
    exit;
}

header('Location: login.php');
exit;
?>
