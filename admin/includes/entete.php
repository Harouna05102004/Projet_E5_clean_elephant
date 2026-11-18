<?php
/*
 * admin/includes/entete.php : Haut de page commun à l'admin
 * Variables attendues avant l'include : $titre_page (texte de l'onglet).
 * La déconnexion est un petit formulaire POST avec jeton CSRF :
 * un simple lien pourrait être déclenché par un autre site (image, lien piégé).
 */

// Pastille du menu : nombre de rappels et messages pas encore traités
$aTraiter = 0;
if (isset($pdo)) {
    $aTraiter = (int) $pdo->query('SELECT (SELECT COUNT(*) FROM rappels WHERE traite_le IS NULL) + (SELECT COUNT(*) FROM contacts WHERE traite_le IS NULL)')->fetchColumn();
}
$page_admin = $page_admin ?? 'demandes';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($titre_page ?? 'Admin') ?> · Admin Clean Elephant</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body class="admin">

<header class="adm-topbar">
    <a href="index.php" class="adm-brand">
        <span class="brand__mark"><img src="../images/logo-clean-elephant.png" alt="" width="40" height="40"></span>
        <span class="adm-brand__txt"><strong>Clean Elephant</strong><small>Espace admin</small></span>
    </a>
    <nav class="adm-nav" aria-label="Menu admin">
        <a href="index.php" class="<?= $page_admin === 'demandes' ? 'is-active' : '' ?>">Demandes de devis</a>
        <a href="messages.php" class="<?= $page_admin === 'messages' ? 'is-active' : '' ?>">
            Rappels et messages<?php if ($aTraiter): ?> <span class="adm-nav__badge"><?= $aTraiter ?></span><?php endif; ?>
        </a>
    </nav>
    <div class="adm-topbar__actions">
        <a href="../index.php" class="adm-link-site" target="_blank" rel="noopener">Voir le site</a>
        <form action="logout.php" method="POST" class="adm-logout">
            <?= csrf_champ() ?>
            <button type="submit" class="btn btn-outline-dark adm-btn-sm">Déconnexion</button>
        </form>
    </div>
</header>

<main class="adm-main">