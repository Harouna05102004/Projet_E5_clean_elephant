<?php
/*
 * includes/header.php — En-tête commun à toutes les pages publiques
 *
 * Organisation (choisie pour que le visiteur trouve tout de suite SA page) :
 *   [logo]   Particuliers · Professionnels · Zone · Contact · Suivi   [Devis gratuit] [téléphone]
 * Sous 1280px, les liens passent dans le menu mobile (bouton hamburger).
 *
 * Variables facultatives, à définir dans la page AVANT l'include :
 *   $base        : chemin vers la racine du site. '' pour les pages à la racine,
 *                  '../' pour une page dans un sous-dossier (ex. espace-client/).
 *                  Valeur fixée par le développeur, jamais par le visiteur.
 *   $page_active : 'particuliers', 'pros', 'devis', 'contact' ou 'suivi', pour
 *                  souligner le lien de la page en cours (aria-current="page").
 */
$base        = $base ?? '';
$page_active = $page_active ?? '';

// Renvoie aria-current="page" si le lien correspond à la page en cours
// (function_exists évite une erreur si le fichier était inclus deux fois)
if (!function_exists('lien_actif')) {
    function lien_actif(string $page, string $page_active): string
    {
        return $page === $page_active ? ' aria-current="page"' : '';
    }
}

// Icônes utilisées dans l'en-tête
$fleche = '<svg class="icon-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
$tel    = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/></svg>';
?>
<a href="#main-content" class="skip-link">Aller au contenu</a>

<header class="site-header" id="site-header">
    <div class="site-header__inner">

        <a class="brand" href="<?= $base ?>index.php" aria-label="Clean Elephant, retour à l'accueil">
            <!-- alt vide : le nom est déjà écrit juste à côté, inutile de le faire lire deux fois -->
                        <!-- Le logo est un carré (éléphant + texte) : la pastille ronde « zoome » sur l'éléphant -->
            <span class="brand__mark"><img src="<?= $base ?>images/logo-clean-elephant.png" alt="" width="48" height="48"></span>
            <span class="brand__text">
                <strong>Clean Elephant</strong>
                <span>Nettoyage de vitres</span>
            </span>
        </a>

        <!-- Navigation principale (visible à partir de 1280px) -->
        <nav class="nav" aria-label="Navigation principale">
            <ul class="nav__list">
                <li><a class="nav__link" href="<?= $base ?>particuliers.php"<?= lien_actif('particuliers', $page_active) ?>>Particuliers</a></li>
                <li><a class="nav__link" href="<?= $base ?>entreprises.php"<?= lien_actif('pros', $page_active) ?>>Professionnels</a></li>
                <li><a class="nav__link" href="#zone">Zone d'intervention</a></li>
                <li><a class="nav__link" href="<?= $base ?>contact.php"<?= lien_actif('contact', $page_active) ?>>Contact</a></li>
                <li><a class="nav__link" href="<?= $base ?>espace-client/index.php"<?= lien_actif('suivi', $page_active) ?>>Suivi de ma demande</a></li>
            </ul>
        </nav>

        <!-- Actions : le bouton « Devis gratuit » reste toujours visible (sauf petit mobile, où la barre du bas le remplace) -->
        <div class="header-actions">
            <a class="btn btn-primary btn--header" href="<?= $base ?>devis.php"<?= lien_actif('devis', $page_active) ?>>Devis gratuit <?= $fleche ?></a>
            <a class="btn btn--phone" href="tel:+33749187647"><?= $tel ?> 07 49 18 76 47</a>

            <!-- Bouton du menu mobile : aria-expanded indique aux lecteurs d'écran si le menu est ouvert -->
            <button type="button" class="burger" aria-expanded="false" aria-controls="menu-mobile" aria-label="Ouvrir le menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>

    <!-- Barre de progression de lecture (largeur pilotée par js/main.js) -->
    <div class="scroll-progress" aria-hidden="true"></div>

    <!-- Menu mobile : caché par l'attribut hidden, affiché par js/main.js -->
    <div class="drawer" id="menu-mobile" hidden>
        <p class="drawer__label">Vous êtes</p>
        <ul class="drawer__list">
            <li><a href="<?= $base ?>particuliers.php">Un particulier <?= $fleche ?></a></li>
            <li><a href="<?= $base ?>entreprises.php">Un professionnel <?= $fleche ?></a></li>
        </ul>
        <p class="drawer__label">Clean Elephant</p>
        <ul class="drawer__list">
            <li><a href="<?= $base ?>devis.php">Devis gratuit <?= $fleche ?></a></li>
            <li><a href="#zone">Zone d'intervention <?= $fleche ?></a></li>
            <li><a href="<?= $base ?>contact.php">Contact <?= $fleche ?></a></li>
            <li><a href="<?= $base ?>espace-client/index.php">Suivi de ma demande <?= $fleche ?></a></li>
        </ul>
        <a class="btn btn-primary" href="tel:+33749187647"><?= $tel ?> 07 49 18 76 47</a>
    </div>
</header>

<!-- Barre d'action mobile (cachée à partir de 768px par le CSS) -->
<div class="sticky-mobile-cta">
    <a class="btn btn--light" href="tel:+33749187647"><?= $tel ?> Appeler</a>
    <a class="btn btn-primary" href="<?= $base ?>devis.php">Devis gratuit <?= $fleche ?></a>
</div>