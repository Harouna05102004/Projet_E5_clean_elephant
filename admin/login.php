<?php
/*
 * admin/login.php — Connexion à l'espace admin
 * Nouveautés : session sûre (securite.php), jeton CSRF, messages clairs
 * (mauvais mot de passe / trop d'essais / session expirée), page non indexée.
 */
require_once __DIR__ . '/../includes/securite.php';

header('X-Frame-Options: DENY');
header('Cache-Control: no-store');
header('X-Robots-Tag: noindex, nofollow');

// Déjà connecté ? On va directement à la liste.
if (!empty($_SESSION['admin_connecte'])) {
    header('Location: index.php');
    exit;
}

// Le message vient d'une liste fermée : on n'affiche jamais un texte venu de l'URL.
$messages = [
    'mdp'     => 'Mot de passe incorrect.',
    'bloque'  => 'Trop de tentatives. Réessayez dans 15 minutes.',
    'session' => 'Votre session a expiré, merci de réessayer.',
];
$erreur = $messages[$_GET['erreur'] ?? ''] ?? null;
$expire = isset($_GET['expire']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Connexion · Admin Clean Elephant</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body class="admin admin--login">

<main class="adm-login">
    <div class="adm-login__card">
        <div class="adm-login__brand">
            <span class="brand__mark brand__mark--lg"><img src="../images/logo-clean-elephant.png" alt="" width="56" height="56"></span>
            <div>
                <p class="adm-kicker">Espace admin</p>
                <h1>Clean Elephant</h1>
            </div>
        </div>

        <?php if ($erreur): ?>
            <div class="alert-error" role="alert"><?= e($erreur) ?></div>
        <?php elseif ($expire): ?>
            <div class="alert-success" role="status">Vous avez été déconnecté après 30 minutes d'inactivité.</div>
        <?php endif; ?>

        <form action="traitement_login.php" method="POST">
            <?= csrf_champ() ?>
            <div class="form-group">
                <label for="mot_de_passe">Mot de passe</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required autofocus autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
        </form>

        <p class="adm-login__back"><a href="../index.php">&larr; Retour au site</a></p>
    </div>
</main>

</body>
</html>